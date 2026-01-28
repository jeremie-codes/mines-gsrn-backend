<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Site;
use App\Models\Organization;
use App\Models\Rapport;
use App\Models\User;
use App\Models\Township;
use Carbon\Carbon;
use App\Models\Stock;
use App\Services\UnitConverter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class MemberController extends Controller
{

    public function stats()
    {
        try {
            $members = [
                'total' => Member::count(),
                'actives' => Member::where('is_active', true)->count(),
                'inactives' => Member::where('is_active', false)->count()
            ];

            $sites = [
                'total' => Site::count(),
                'actives' => Site::where('is_active', true)->count(),
                'inactives' => Site::where('is_active', false)->count()
            ];

            $organisations = [
                'total' => Organization::count(),
                'actives' => Organization::where('is_active', true)->count(),
                'inactives' => Organization::where('is_active', false)->count()
            ];

            $users = [
                'total' => User::count(),
                'actives' => User::where('is_active', true)->count(),
                'inactives' => User::where('is_active', false)->count()
            ];

            return response()->json([
                'success' => true,
                'members' => $members,
                'sites' => $sites,
                'organizations' => $organisations,
                'users' => $users
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur, " .$th->getMessage()
            ], 500);
        }
    }

    public function index()
    {
        try {

            $members =  Member::orderBy('created_at', 'desc')->paginate(10);

            return response()->json([
                'success' => true,
                'members' => $members,
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur, " .$th->getMessage()
            ], 500);
        }
    }

    // GET /dashboard/stocks-chart
    public function stockStats(Request $request)
    {
        $user = auth()->user();
        $organizationId = $user->assigned_organization_id;

        // Dates par défaut : dernier mois
        $dateDebut = $request->date_debut
            ? Carbon::parse($request->date_debut)->startOfDay()
            : now()->subMonth()->startOfDay();

        $dateFin = $request->date_fin
            ? Carbon::parse($request->date_fin)->endOfDay()
            : now()->endOfDay();

        // Pagination
        $page = (int) $request->get('page', 1);
        $perPage = (int) $request->get('per_page', 10);

        // Requête stocks
        $query = Stock::whereHas('site', function ($q) use ($organizationId) {
                $q->where('organization_id', $organizationId);
            })
            ->whereBetween('created_at', [$dateDebut, $dateFin]);

        if ($request->site_id) {
            $query->where('site_id', $request->site_id);
        }

        $stocks = $query->get();

        // Agrégation par substance
        $grouped = [];

        foreach ($stocks as $stock) {
            $converted = UnitConverter::convert(
                substanceCode: $stock->substance_code,
                qty: $stock->qte,
                from: $stock->mesure,
            );

            $grouped[$stock->substance_code] ??= [
                'substance' => $converted['substance'],
                'qte' => 0,
                'unit' => $converted['unit'],
            ];

            $grouped[$stock->substance_code]['qte'] += $converted['qty'];
        }

        // Collection des substances agrégées
        $collection = collect($grouped)->values();

        // Pagination manuelle
        $paginator = new LengthAwarePaginator(
            $collection->forPage($page, $perPage)->values(),
            $collection->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        // Données de la page courante
        $currentItems = collect($paginator->items());

        return response()->json([
            // FORMAT CHART (comme demandé)
            'labels' => $currentItems->pluck('substance')->values(),
            'data'   => $currentItems->pluck('qte')->values(),
            'unit'   => $currentItems->first()['unit'] ?? null,

            // Pagination
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'last_page'    => $paginator->lastPage(),
            ],

            // Dates
            'date' => [
                'debut' => $dateDebut->toDateString(),
                'fin'   => $dateFin->toDateString(),
            ],
        ]);
    }

    public function stockIndex(Request $request)
    {
        try {
            $request->validate([
                'searchByName' => 'nullable|string|max:255',
                'searchByFirstName' => 'nullable|string|max:255',
                'searchByLastName' => 'nullable|string|max:255',
                'searchBySite' => 'nullable|numeric',
                'per_page' => 'nullable|numeric'
            ]);

            $user = auth()->user();
            $organisationId = $user->assigned_organization_id;

            $searchByName = $request->searchByName ?? $request->searchByFirstName ?? $request->searchByLastName ?? null;
            $members =  Member::Where('organization_id', $organisationId)->orderBy('created_at', 'desc')->paginate($request->per_page ?? 10);

            if (!empty($searchByName)) {
                $members = Member::where('firstname', 'like', $searchByName . '%')
                    ->Where('organization_id', $organisationId)
                    ->orWhere('lastname', 'like', $searchByName . '%')
                    ->orWhere('middlename', 'like', $searchByName . '%')
                    ->paginate($request->per_page ?? 10);
            }

            $searchBySite = $request->searchBySite;

            if (!empty($searchBySite)) {
                $members = Member::where('site_id', $searchBySite)->Where('organization_id', $organisationId)->paginate($request->per_page ?? 10) ?? [];
            }

            return response()->json([
                'success' => true,
                'members' => $members,
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur, " .$th->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'firstname' => 'nullable|string|max:255',
                'lastname' => 'nullable|string|max:255',
                'middlename' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:255',
                'organization_id' => 'nullable|exists:organizations,id',
                'site_id' => 'nullable|exists:sites,id',
                'address' => 'nullable|string|max:255',
                'gender' => 'nullable|string',
                'agent_type' => 'nullable|string',
                'birth_date' => 'nullable|string',
                'face_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'face_base64' => 'nullable|string',
                'is_active' => 'nullable|boolean'
            ]);

            $data = $request->all();
            $data['date_adhesion'] = now(); // équivalent propre à date('Y-m-d H:i:s')

            $client = new Client([
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
            ]);

            $gcp = Organization::find($request->organization_id)->gcp;

            if (!$gcp) {
                return response()->json([
                    'success' => false,
                    'message' => 'GCP non trouvé'
                ], 404);
            }

            $response = $client->request('POST', env('API_GSRN_GENERATE'), [
                'json' => [
                    "firstname" => $request->firstname,
                    "middlename" => $request->middlename,
                    "lastname" => $request->lastname,
                    "birthdate" => $request->date_adhesion,
                    "phone" => $request->phone,
                    "gender" => $request->gender,
                    "title" => $request->agent_type,
                    'projectExternalId' => $gcp,
                ],
                'verify' => false,
            ]);

            $content = json_decode($response->getBody()->getContents());

            if ($content->code != "0") {
                return response()->json([
                    'success' => false,
                    'message' => $content->error
                ], 400);
            }

            // Gérer l'upload d'image
            $data['face_path'] = $this->handleImageUpload($request);
            $data['membershipNumber'] = $content->data->gsrn;

            // Créer le membre
            $member = Member::create($data);
            //$user = null;

            if ($member->agent_type == "chief_cooperative") {
                User::create([
                    'member_id' => $member->id,
                    'password' => Hash::make('@mines123'),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Membre créé avec succès',
                "member" => $member
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur, " . $th->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $member = Member::with('site', 'city', 'organization')->findOrFail($id);

            $base64Image = null;

            if ($member->face_path && file_exists(public_path('storage/' . $member->face_path))) {
                $fileContent = file_get_contents(public_path('storage/' . $member->face_path));
                $mimeType = mime_content_type(public_path('storage/' . $member->face_path));
                $base64Image = 'data:' . $mimeType . ';base64,' . base64_encode($fileContent);
            }

            return response()->json([
                'success' => true,
                'member' => $member,
                'face_base64' => $base64Image,
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur, " . $th->getMessage()
            ], 500);
        }
    }

    public function update(Request $request)
    {

        try {
            $request->validate([
               'firstname' => 'nullable|string|max:255',
                'lastname' => 'nullable|string|max:255',
                'middlename' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:255',
                'organization_id' => 'nullable|exists:organizations,id',
                'site_id' => 'nullable|exists:sites,id',
                'address' => 'nullable|string|max:255',
                'gender' => 'nullable|string',
                'face_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'face_base64' => 'nullable|string',
                'date_adhesion' => 'nullable|date',
                'birth_date' => 'nullable|date',
                'is_active' => 'nullable|boolean',
                'membershipNumber' => 'nullable|string|max:255'
            ]);


            $member = Member::find($request->member_id);

            if(!$member) {
                return response()->json([
                    'success' => false,
                    'message' => 'Membre non trouvé'
                ], 404);
            }

            $data = $request->all();

            if ($request->has('membershipNumber') && !empty($request->membershipNumber)) {
                $data['membershipNumber'] = $request->membershipNumber;
            }

            // Gérer l'upload d'image
            $newImagePath = $this->handleImageUpload($request);
            if ($newImagePath) {
                // Supprimer l'ancienne image si elle existe
                if ($member->face_path && file_exists(public_path('storage/' . $member->face_path))) {
                    unlink(public_path('storage/' . $member->face_path));
                }
                $data['face_path'] = $newImagePath;
            }


            $member->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Membre mis à jour avec succès'
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur, " .$th->getMessage()
            ], 500);
        }
    }

    public function export()
    {
        try {
            $members = Member::orderBy('created_at', 'desc')->get();
            return response()->json([
                'success' => true,
                'members' => $members
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur, " .$th->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {

            $member = Member::find($id);

            if(!$member) {
                return response()->json([
                    'success' => false,
                    'message' => 'Membre non trouvé'
                ], 404);
            }

            // Supprimer l'image si elle existe
            if ($member->face_path && file_exists(public_path('storage/' . $member->face_path))) {
                unlink(public_path('storage/' . $member->face_path));
            }

            $member->delete();

            return response()->json([
                'success' => true,
                'message' => 'Membre supprimé avec succès',
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur, " .$th->getMessage()
            ], 500);
        }
    }

    /**
     * API pour récupérer les townships d'une ville
     */
    public function getTownshipsByCity($cityId)
    {
        try {

            $townships = Township::where('city_id', $cityId)->get();

            if (!$townships) {
                return response()->json([
                    'success' => false,
                    'message' => 'Township not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'townships' => $townships
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur, " .$th->getMessage()
            ], 500);
        }

    }

    /**
     * Gérer l'upload d'image (fichier ou base64)
     */
    private function handleImageUpload(Request $request)
    {
        // Vérifier s'il y a une image en base64 (depuis mobile)
        if ($request->has('face_base64') && !empty($request->face_base64)) {
            return $this->saveBase64Image($request->face_base64);
        }

        // Vérifier s'il y a un fichier uploadé (depuis web)
        if ($request->hasFile('face_image')) {
            return $this->saveUploadedFile($request->file('face_image'));
        }

        return null;
    }

    /**
     * Sauvegarder une image base64
     */
    private function saveBase64Image($base64String)
    {
        try {
            // Extraire les données de l'image base64
            if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $type)) {
                $data = substr($base64String, strpos($base64String, ',') + 1);
                $type = strtolower($type[1]);

                if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                    throw new \Exception('Type d\'image non valide');
                }

                $data = base64_decode($data);

                if ($data === false) {
                    throw new \Exception('Échec du décodage base64');
                }
            } else {
                throw new \Exception('Format base64 non valide');
            }

            // Nom de fichier unique
            $fileName = 'profile_' . Str::uuid() . '.' . $type;

            // Chemin physique vers public/storage/profiles
            $folder = public_path('storage/profiles');
            $filePath = $folder . '/' . $fileName;

            // Créer le dossier s'il n'existe pas
            if (!file_exists($folder)) {
                mkdir($folder, 0755, true);
            }

            // Enregistrer l'image
            file_put_contents($filePath, $data);

            // Retourner le chemin relatif (URL)
            return 'profiles/' . $fileName;

        } catch (\Exception $e) {
            Log::error('Erreur lors de la sauvegarde de l\'image base64: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Sauvegarder un fichier uploadé dans public/storage/profiles
     */
    private function saveUploadedFile($file)
    {
        try {
            // Générer un nom de fichier unique
            $fileName = 'profile_' . Str::uuid() . '.' . $file->getClientOriginalExtension();

            // Définir le dossier de destination
            $destinationPath = public_path('storage/profiles');

            // Créer le dossier s'il n'existe pas
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            // Déplacer le fichier uploadé dans le dossier
            $file->move($destinationPath, $fileName);

            // Retourner le chemin relatif pour l'affichage (ex: storage/profiles/xxx.jpg)
            return 'profiles/' . $fileName;

        } catch (\Exception $e) {
            Log::error('Erreur lors de la sauvegarde du fichier: ' . $e->getMessage());
            return null;
        }
    }

     /**
     * API pour créer/modifier un membre depuis mobile (avec base64)
     */
    public function apiStore(Request $request)
    {
        try {
            $validated = $request->validate([
                'firstname' => 'nullable|string|max:255',
                'lastname' => 'nullable|string|max:255',
                'middlename' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:255',
                'organization_id' => 'nullable|exists:organizations,id',
                'site_id' => 'nullable|exists:sites,id',
                'address' => 'nullable|string|max:255',
                'gender' => 'nullable|string',
                'agent_type' => 'nullable|string',
                'birth_date' => 'nullable|string',
                'face_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'face_base64' => 'nullable|string',
                'is_active' => 'nullable|boolean'
            ]);

            $data = $request->all();
            $data['date_adhesion'] = now(); // équivalent propre à date('Y-m-d H:i:s')

            $client = new Client([
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
            ]);

            $gcp = Organization::find($request->organization_id)->gcp;

            if (!$gcp) {
                return response()->json([
                    'success' => false,
                    'message' => 'GCP non trouvé'
                ], 404);
            }

            $response = $client->request('POST', env('API_GSRN_GENERATE'), [
                'json' => [
                    "firstname" => $request->firstname,
                    "middlename" => $request->middlename,
                    "lastname" => $request->lastname,
                    "birthdate" => $request->date_adhesion,
                    "phone" => $request->phone,
                    "gender" => $request->gender,
                    "title" => $request->agent_type,
                    'projectExternalId' => $gcp,
                ],
                'verify' => false,
            ]);

            $content = json_decode($response->getBody()->getContents());

            if ($content->code != "0") {
                return response()->json([
                    'success' => false,
                    'message' => $content->error
                ], 400);
            }


            // Gérer l'upload d'image
            $data['face_path'] = $this->handleImageUpload($request);
            $data['membershipNumber'] = $content->data->gsrn;

            // Créer le membre
            $member = Member::create($data);
            //$user = null;

            if ($member->agent_type == "chief_cooperative") {
                User::create([
                    'member_id' => $member->id,
                    'password' => Hash::make('@mines123'),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Membre créé avec succès',
                "member" => $member
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur, " . $th->getMessage()
            ], 500);
        }
    }

    /**
     * API pour modifier un membre depuis mobile
     */
    public function apiUpdate(Request $request)
    {
        try {

            $request->validate([
                'firstname' => 'nullable|string|max:255',
                'lastname' => 'nullable|string|max:255',
                'middlename' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:255',
                'site_id' => 'required|exists:sites,id',
                'city_id' => 'nullable|exists:cities,id',
                'organization_id' => 'nullable|exists:organizations,id',
                'face_base64' => 'nullable|string',
                'is_active' => 'nullable|boolean',
                'member_id' => 'required|exists:members,id'
            ]);

            $member = Member::findOrFail($request->member_id);

            if(!$member) {
                return response()->json([
                    'success' => false,
                    'message' => 'Membre non trouvé'
                ], 404);
            }

            $data = $request->all();

            // Gérer l'image base64
            if ($request->has('face_base64') && !empty($request->face_base64)) {
                // Supprimer l'ancienne image si elle existe
                if ($member->face_path && file_exists(public_path('storage/' . $member->face_path))) {
                    unlink(public_path('storage/' . $member->face_path));
                }

                $data['face_path'] = $this->saveBase64Image($request->face_base64);
            }

            $member->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Membre mis à jour avec succès',
                'data' => $member->load('site', 'city', 'organization')
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur, " .$th->getMessage()
            ], 500);
        }
    }

}
