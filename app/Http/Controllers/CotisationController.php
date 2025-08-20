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
            $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJcL2xvZ2luIiwicm9sZXMiOlsiTUVSQ0hBTlQiXSwiZXhwIjoxNzkyNDUyNzA5LCJzdWIiOiJkMzY1ZDdmMjU1NGY1ZDIzMGQ5ODA4MTgxMWE2NTE3YSJ9.y5uiKVPY0w8aexcaa6sB-UjKUDHRX9u8L1u04-JVzV0";

            $urlCallback = url('/flexpaie_callback');

            $response = $client->request('POST', 'https://backend.flexpay.cd/api/rest/v1/paymentService', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'phone' => $request->phone,
                    'amount' => $request->amount * $request->nombre_retard,
                    'currency' => $request->currency,
                    'pay_method' => $request->type,
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

    public function payBySms(Request $request)
    {

        $request->validate([
            'transaction_id' => 'required|string|max:255',
            'member' => 'required|string|max:255',
            'month' => 'required|numeric|min:1',
            'currency' => 'required|string|max:10',
            'phone' => 'required|string|max:255',
        ]);

        try {
            $member = Member::with('category')->where('membershipNumber', $request->member)->first();
            if (!$member) {
                return response()->json([
                    'code' =>"1",
                    'message' => "NOK",
                    'member' => ""
                ], 404);
            }

            if ($member->category == null) {
                return response()->json([
                    'code' => "1",
                    'message' => "Pas de categorie",
                    'member' => ""
                ], 404);
            }

            $amount = $member->category->amount;

            // on verifie si la currency est la meme en uppercase
            if (strtoupper($member->category->currency) != strtoupper($request->currency)) {
                $amount = $member->category->equivalent;
            }

            $response = $this->pushFlexpaie($amount, $request->phone, $request->currency, $member, $request->month, $request->transaction_id);

            return $response;

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur, " .$th->getMessage()
            ], 500);
        }
    }

    private function pushFlexpaie ($amount, $phone, $currency, $member, $month, $transaction_id) {
        try {
            $client = new Client();
            $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJcL2xvZ2luIiwicm9sZXMiOlsiTUVSQ0hBTlQiXSwiZXhwIjoxNzkyNDUyNzA5LCJzdWIiOiJkMzY1ZDdmMjU1NGY1ZDIzMGQ5ODA4MTgxMWE2NTE3YSJ9.y5uiKVPY0w8aexcaa6sB-UjKUDHRX9u8L1u04-JVzV0";
            $urlCallback = url('/flexpaie_callback');
    
            $response = $client->request('POST', 'https://backend.flexpay.cd/api/rest/v1/paymentService', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'phone' => $phone,
                    'amount' => $amount * $month,
                    'currency' => $currency,
                    "callbackUrl" => $urlCallback,
                    "merchant" => 'tajiri',
                    "reference" => $transaction_id,
                    "type" => "1",
                ]
            ]);
    
            $data = json_decode($response->getBody()->getContents());
    
            if ($data->code == 0) {
                $nombreMois = (int) $month;
    
                $cotisations = [];
                $baseDate = $member->next_payment 
                    ? Carbon::parse($member->next_payment)
                    : Carbon::now();
    
                for ($i = 0; $i < $nombreMois; $i++) {
                    $cotisation = Cotisation::create([
                        'member_id' => $member->id,
                        'type' => 'flexpaie by sms',
                        'amount' => $amount,
                        'currency' => $currency,
                        'status' => 'pending',
                        'reference' => $data->reference,
                        'description' => 'Paiement cotisation',
                    ]);
                    $cotisations[] = $cotisation;
    
                    Transaction::create([
                        'cotisation_id' => $cotisation->id,
                        'transaction_id' => $transaction_id,
                        'phone' => $phone,
                        'amount' => $amount,
                        'currency' => $currency,
                        'month' => $month,
                        'callback_response' => json_encode($data),
                    ]);
                }
    
                $member->next_payment = $baseDate->copy()->addMonths($nombreMois);
                $member->save();
    
                return response()->json([
                    'code' => "0",
                    'message' => "OK",
                    'member' => $member->firstname . ' ' . $member->lastname . ' ' . $member->middlename,
                ], 201);
            }
    
            return response()->json([
                'code' => "1",
                'message' => "NOK",
                'member' => "",
            ], 400);
    
        } catch (\Throwable $th) {
            return response()->json([
                'code' => "1",
                'message' => 'Erreur : ' . $th->getMessage()
            ], 500);
        }
    }  

    public function callback (Request $request) {
        return response()->json([
            'success' => true,
            'data' => $request->all()
        ], 201);
    }

}
