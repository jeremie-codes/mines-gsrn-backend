<?php

namespace App\Http\Controllers;

use App\Models\Cotisation;
use App\Models\Member;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\Transaction;
use Carbon\Carbon;

class CotisationController extends Controller
{

    protected string $ApipushFlexPaie = "https://backend.flexpay.cd/api/rest/v1/paymentService/"; 
    protected string $ApiCheckFlexPaie = "https://backend.flexpay.cd/api/rest/v1/check/";
    protected string $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJcL2xvZ2luIiwicm9sZXMiOlsiTUVSQ0hBTlQiXSwiZXhwIjoxODAxMjM4NDA3LCJzdWIiOiI5ZDVhYTkwN2ZiOTI2Y2FkYzdkZGU0ZmFhODk0Yzc5ZCJ9._j9WlAfDWZwRciXecND5w2SI_mGBR7x82ad3fXFv_VA";
    protected int $iterationChecking = 0;

    public function index ()
    {
        try {
            $cotisations = Cotisation::orderBy('id', 'desc')->get();

            return response()->json([
                'success' => true,
                'cotisations' => $cotisations
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur, " .$th->getMessage()
            ], 500);
        }
    }

    public function store(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'type' => 'required|in:cash,flexpaie',
                'amount' => 'required|numeric|min:0',
                'currency' => 'required|string|max:10',
                'status' => 'nullable|string|max:50',
                'reference' => 'nullable|string|max:255',
                'description' => 'nullable|string|max:255',
                'created_at' => 'nullable|date',
                'retard' => 'nullable|boolean',
                'nombre_retard' => 'nullable|numeric|min:1'
            ]);

            $member = Member::find($id);

            if (!$member) {
                return response()->json([
                    'success' => false,
                    'message' => 'Membre non trouvé !'
                ]);
            }

            $nombreMois = 1;

            if (!empty($validated['retard']) && !empty($validated['nombre_retard'])) {
                $nombreMois = (int) $validated['nombre_retard'];
            }

            $cotisations = [];

            $baseDate = $member->next_payment 
                ? Carbon::parse($member->next_payment)
                : Carbon::now();

            for ($i = 0; $i < $nombreMois; $i++) {
                $cotisationData = array_merge($validated, [
                    'member_id' => $id,
                    // 'created_at' => isset($validated['created_at']) 
                    //     ? Carbon::parse($validated['created_at'])->addMonths($i)
                    //     : now()->addMonths($i)
                ]);

                $cotisations[] = Cotisation::create($cotisationData);
            }

            // Mise à jour de la date de prochain paiement
            $member->next_payment = $baseDate->copy()->addMonths($nombreMois);

            // Si c'était le premier paiement, on le vide
            if ($member->first_payment) {
                $member->first_payment = null;
            }

            $member->save();

            return response()->json([
                'success' => true,
                'message' => 'Cotisation(s) enregistrée(s) avec succès.',
                'data' => $cotisations
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l’enregistrement de la cotisation : ' . $th->getMessage()
            ], 500);
        }
    }


    public function update(Request $request, $id)
    {
        try {

            $cotisation = Cotisation::find($id);

            if (!$cotisation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cotisation non trouvée.'
                ], 404);
            }

            if ($cotisation->status === 'payée') {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de modifier une cotisation déjà validée.'
                ], 403);
            }

            $validated = $request->validate([
                'type' => 'sometimes|required|in:cash,flexpaie',
                'amount' => 'sometimes|required|numeric|min:0',
                'currency' => 'sometimes|required|string|max:10',
                'status' => 'nullable|string|max:50',
                'reference' => 'nullable|string|max:255',
                'description' => 'nullable|string|max:255',
                'created_at' => 'nullable|date'
            ]);

            $cotisationUpdated = $cotisation->update($validated);

            $member = Member::find($cotisation->member_id);

            if ($member && $member->first_payment && $cotisation->status == "payée") {
                $lastPayment = Carbon::parse($member->next_payment);
                $member->next_payment = $lastPayment->addMonths(1);
                $member->first_payment = null;
                $member->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Cotisation mise à jour avec succès.',
                'data' => $cotisationUpdated
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de la cotisation : ' . $th->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $cotisation = Cotisation::findOrFail($id);

            // Vérifie si la cotisation est validée → empêcher la suppression
            if ($cotisation->status === 'payée') {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer une cotisation déjà payée.'
                ], 403);
            }

            $cotisation->delete();

            return response()->json([
                'success' => true,
                'message' => 'Cotisation supprimée avec succès.'
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression : ' . $th->getMessage()
            ], 500);
        }
    }

    // Route pour le flexpaie web app
    public function flexpaie (Request $request, $id) {
        try {

            $member = Member::find($id);

            if (!$member) {
                return response()->json([
                    'success' => false,
                    'message' => 'Membre non trouvé !'
                ]);
            }

            $validated = $request->validate([
                'type' => 'required',
                'phone' => 'required',
                'amount' => 'required',
                'currency' => 'required|string|max:10',
                'reference' => 'required|string|max:255',
                'merchant' => 'required',
                'retard' => 'nullable|boolean',
                'nombre_retard' => 'nullable|numeric|min:1'
            ]);

            $client = new Client();
            $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJcL2xvZ2luIiwicm9sZXMiOlsiTUVSQ0hBTlQiXSwiZXhwIjoxODAxMjM4NDA3LCJzdWIiOiI5ZDVhYTkwN2ZiOTI2Y2FkYzdkZGU0ZmFhODk0Yzc5ZCJ9._j9WlAfDWZwRciXecND5w2SI_mGBR7x82ad3fXFv_VA";

            $urlCallback = url('api/flexpaie_callback');

            $retard = $request->nombre_retard ?? 1;

            $response = $client->request('POST', $this->ApipushFlexPaie, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'phone' => $request->phone,
                    'amount' => $request->amount * $retard,
                    'currency' => $request->currency,
                    "callbackUrl" => $urlCallback,
                    "merchant" => $request->merchant,
                    "reference" => $request->reference,
                    "type" => $request->type,
                ]
            ]);

            $data = json_decode($response->getBody()->getContents());

            if ($data->code == 0) {


                $nombreMois = 1;

                if (!empty($validated['retard']) && !empty($validated['nombre_retard'])) {
                    $nombreMois = (int) $validated['nombre_retard'];
                }

                $cotisations = [];

                $baseDate = $member->next_payment 
                    ? Carbon::parse($member->next_payment)
                    : Carbon::now();

                for ($i = 0; $i < $nombreMois; $i++) {
                    $cotisationData = array_merge($validated, [
                        'member_id' => $id,
                        // 'created_at' => isset($validated['created_at']) 
                        //     ? Carbon::parse($validated['created_at'])->addMonths($i)
                        //     : now()->addMonths($i)
                    ]);

                    $cotisations[] = Cotisation::create($cotisationData);
                }

                // Mise à jour de la date de prochain paiement
                $member->next_payment = $baseDate->copy()->addMonths($nombreMois);

                // $cotisation = Cotisation::create([
                //     'member_id' => $id,
                //     'type' => 'flexpaie',
                //     'amount' => $validated['amount'],
                //     'currency' => $validated['currency'],
                //     'reference' => $validated['reference'],
                //     'description' => 'Paiement cotisation',
                // ]);
                $lastCotisation = end($cotisations);

                return response()->json([
                    'success' => true,
                    'data' => $data,
                    'cotisation_id' => $lastCotisation->id
                ], 201);
            }

            return response()->json([
                'success' => false,
                'data' => $data
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur d\'enregistrement de cotisation : ' . $th->getMessage()
            ], 500);
        }
    }

    public function findMember($member)
    {
    
        try {
            $member = Member::where('membershipNumber', $member)->first();
    
            if (!$member) {
                return response()->json([
                    'code'    => "1",
                    'message' => "Membre non trouvé.",
                    'member'  => ""
                ], 404);
            }
    
            if ($member->category == null) {
                return response()->json([
                    'code'    => "1",
                    'message' => "Membre n'a pas de categorie.",
                    'member'  => $member
                ], 404);
            }
    
            return response()->json([
                'code' => "0",
                'message' => "Membre trouvé",
                'member'  => $member->firstname . ' ' . $member->lastname . ' ' . $member->middlename,
                'amount' => $member->category->amount,
                'currency' => $member->category->currency
            ], 200);
    
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur, " . $th->getMessage()
            ], 500);
        }
    }  
       
    // Route pour le paiement par sms
    public function payBySms(Request $request)
    {
        $validated = $request->validate([
            'transaction_id' => 'required|string|max:255',
            'member'         => 'required|string|max:255',
            'month'          => 'required|numeric|min:1',
            'currency'       => 'required|string|max:10',
            'phone'          => 'required|string|max:255',
        ]);
    
        try {
            $member = Member::with('category')->where('membershipNumber', $validated['member'])->first();
    
            if (!$member) {
                return response()->json([
                    'code'    => "1",
                    'message' => "NOK",
                    'member'  => ""
                ], 404);
            }
    
            if ($member->category == null) {
                return response()->json([
                    'code'    => "1",
                    'message' => "Membre n'a pas de categorie",
                    'member'  => ""
                ], 404);
            }
    
            $amount = (int) $member->category->amount;
            $month = (int) $validated['month'] ?? 1;
    
            // Vérification de la devise
            if (strtoupper($member->category->currency) != strtoupper($validated['currency'])) {
                $amount = $member->category->equivalent;
            }

            $totalAmount = (string) ($amount * $month);

    
            $client = new Client();
            $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJcL2xvZ2luIiwicm9sZXMiOlsiTUVSQ0hBTlQiXSwiZXhwIjoxODAxMjM4NDA3LCJzdWIiOiI5ZDVhYTkwN2ZiOTI2Y2FkYzdkZGU0ZmFhODk0Yzc5ZCJ9._j9WlAfDWZwRciXecND5w2SI_mGBR7x82ad3fXFv_VA";
            $urlCallback = url('api/callback/sms/' . $month . '/' . $member->id);

            // return response()->json([
            //     $urlCallback,
            //     $member->id
            // ]);
    
            $response = $client->request('POST', $this->ApipushFlexPaie, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept'        => 'application/json',
                ],
                'json' => [
                    'phone'      => $validated['phone'],
                    'amount'     => $totalAmount,
                    'currency'   => $validated['currency'],
                    'callbackUrl'=> $urlCallback,
                    'merchant'   => 'COOPEFEMAC',
                    'reference'  => $validated['transaction_id'],
                    'type'       => "1",
                ],
                'verify' => false,
            ]);
    
            $data = json_decode($response->getBody()->getContents());

            // return response()->json($data);
    
            if ($data->code == 0) {
                $nombreMois = $month;
    
                $cotisations = [];
    
                for ($i = 0; $i < $nombreMois; $i++) {
                    $cotisation = Cotisation::create([
                        'member_id'  => $member->id,
                        'type'       => 'flexpaie by sms',
                        'amount'     => $amount,
                        'currency'   => $validated['currency'],
                        'status'     => 'en attente',
                        'reference'  => $validated['transaction_id'],
                        'description'=> 'Paiement cotisation',
                    ]);

                    $cotisations[] = $cotisation;
                }

                $lastCotisation = end($cotisations);
                
                Transaction::create([
                    'cotisation_id' => $lastCotisation->id,
                    'reference_sms' => $validated['transaction_id'],
                    'order_number' => $data->orderNumber,
                    'status' => "pending",
                    'phone' => $validated['phone'],
                    'amount' => $amount,
                    'currency' => $validated['currency'],
                    'month' => $validated['month'],
                    'callback_response' => json_encode($data),
                ]);

                return response()->json([
                    'status'    => "0",
                    'message' => "OK",
                    'member'  => $member->firstname . ' ' . $member->lastname . ' ' . $member->middlename,
                ], 201);

            }
    
            return response()->json($data);
    
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur, " . $th->getMessage()
            ], 500);
        }
    }     

    public function callback (Request $request, ) {
        return response()->json([
            'success' => true,
            'data' => $request->all()
        ], 201);
    }

    public function callbackBySms (Request $request, $month, $memberId) 
    {
        
        try {
            
            $dataRq = $request->json()->all();

            // Accéder à orderNumber
            $orderNumber = $dataRq['orderNumber'] ?? null;
            $reference = $dataRq['reference'] ?? null;
            $member = Member::find($memberId);
            $cotisation = Cotisation::where('member_id', $memberId)->where('reference', $reference)->first();

            // $client = new Client();    
            // $responses = $client->request('GET', $this->ApiCheckFlexPaie . $orderNumber, [
            //     'headers' => [
            //         'Authorization' => 'Bearer ' . $this->token,
            //         'Accept'        => 'application/json',
            //     ],
            //     'verify' => false,
            // ]);

            // $datas = json_decode($responses->getBody()->getContents());
            $transaction = Transaction::where('order_number', $orderNumber);
            
            // // if ((int) $datas->code == 0) {

            //     // $transaction->update([
            //     //     'status' => 'failed', 
            //     //     'callback_response' => "condition code lue",
            //     // ]);
                
            //     if ($datas->transaction->status == 0) {
            //         $nombreMois = $month;
                    
            //         $baseDate = $member->next_payment
            //             ? Carbon::parse($member->next_payment)
            //             : Carbon::now();
        
            //         $member->next_payment = $baseDate->copy()->addMonths($nombreMois);
            //         $member->save();

            //         $transaction->update([
            //             'status' => 'success', 
            //             'callback_response' => json_encode($datas),
            //         ]);

            //         // $cotisation->update([
            //         //     'status' => 'payée', 
            //         // ]);
        
            //         return response()->json([
            //             'message' => "Callback réçu",
            //         ], 200);
            //     }
            //     elseif ($datas->transaction->status == 2) {
            //         return response()->json([
            //             'message' => "Callback réçu",
            //         ], 200);
            //     }
            //     else {
            //         // $cotisation->update([
            //         //     'status' => 'échouée', 
            //         // ]);

            //         $transaction->update([
            //             'status' => 'failed', 
            //             'callback_response' => json_encode($datas),
            //         ]);
            //     }
            // // }

            $cotisation->status = 'échoué';
            $cotisation->save();
                
            return response()->json([
                'message' => "Callback réçu",
            ], 200);
        } catch (\Throwable $th) {
            $transaction->update([
                'status' => 'failed', 
                'callback_response' => json_encode($th->getMessage()),
            ]);
        }

    }

}
