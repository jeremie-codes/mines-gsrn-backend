<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Member;
use App\Models\Site;
use App\Models\Organization;
use App\Models\User;
use App\Models\Township;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class MemberController extends Controller
{

    protected $projectExternalId = "1c4214e9-c6e6-4862-9fb3-b0b4af9541ce";
    protected $baseUrlMidleware = "http://localhost:8001/api/rest/v1/gsrn/generate";

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

            $members = Member::orderBy('created_at', 'desc')->paginate(10);

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
                'city_id' => 'nullable|exists:cities,id',
                'address' => 'nullable|string|max:255',
                'gender' => 'nullable|string',
                'face_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'face_base64' => 'nullable|string',
                'date_adhesion' => 'nullable|date',
                'birth_date' => 'nullable|date',
                'is_active' => 'nullable|boolean'
            ]);

            $data = $request->all();

            $client = new Client();

            /*$response = $client->request('POST', $this->baseUrlMidleware, [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'firstname' => $data['firstname'],
                    'lastname' => $data['lastname'],
                    "phone" => $data['phone'],
                    "gender" => $data['gender'],
                    "title" => "mineur",
                    "projectExternalId" => $this->projectExternalId
                ]
            ]);

            $content = json_decode($response->getBody()->getContents());

            //return response()->json([
            //    'success' => false,
            //    'message' => $content->data->barcodeValue
            //], 200);*/


            /*if ($content->code != 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la géneration du numero de membre : ' . $content->message
                ], 500);
            }*/

            // Générer automatiquement le numéro de membre
            //$data['membershipNumber'] = $content->data->gsrn; // pour le test je génère une clé unique depuis boot dans le model member
            // $data['qrcode_url'] = $content->data->barcodeValue;

            // Gérer l'upload d'image
            $data['face_path'] = $this->handleImageUpload($request);


            // Créer le membre
            $member = Member::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Membre créé avec succès'
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
            $member = Member::with('category','site', 'city', 'township', 'organisation', 'fonction', 'chef', 'user', 'cotisations')->findOrFail($id);

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
                'city_id' => 'nullable|exists:cities,id',
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
            $members = Member::with('user')->orderBy('created_at', 'desc')->get();
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
            $fileName = 'profile_' . \Str::uuid() . '.' . $type;

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
            \Log::error('Erreur lors de la sauvegarde de l\'image base64: ' . $e->getMessage());
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
            $fileName = 'profile_' . \Str::uuid() . '.' . $file->getClientOriginalExtension();

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
            \Log::error('Erreur lors de la sauvegarde du fichier: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * API pour créer/modifier un membre depuis mobile (avec base64)
     */
    public function apiStore(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'firstname' => 'nullable|string|max:255',
                'lastname' => 'nullable|string|max:255',
                'middlename' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:255',
                'organization_id' => 'nullable|exists:organizations,id',
                'site_id' => 'nullable|exists:sites,id',
                'city_id' => 'nullable|exists:cities,id',
                'address' => 'nullable|string|max:255',
                'gender' => 'nullable|string',
                'face_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'face_base64' => 'nullable|string',
                'date_adhesion' => 'nullable|date',
                'birth_date' => 'nullable|date',
                'is_active' => 'nullable|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                    'message' => 'Les données envoyées ne sont pas valides.'
                ], 422);
            }

            $validated = $validator->validated();

            // Vérification de la catégorie
            $categoryModel = Category::where('name', $request->category)->first() ?? Category::find(1);

            // Si la catégorie n'est pas trouvée, choisir la catégorie par défaut (ID = 1)
            if (!$categoryModel) {
                // Si la catégorie n'existe pas, affecte la catégorie par défaut
                return response()->json([
                    'success' => false,
                    'message' => 'Catégorie par défaut introuvable'
                ], 404);
            }

            $validated['category_id'] = $categoryModel->id;

            /*$client = new Client();

            $response = $client->request('POST', $this->baseUrlMidleware, [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'firstname' => $validator['firstname'],
                    'lastname' => $validator['lastname'],
                    "birthdate" => $validator['date_adhesion'],
                    "phone" => $validator['phone'],
                    "gender" => $validator['gender'],
                    "title" => "mineur",
                    "projectExternalId" => $this->projectExternalId
                ],
                true
            ]);

            $content = json_decode($response->getBody()->getContents());

            if ($content->code != 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la géneration du numero de membre : ' . $content->message
                ], 500);
            }

            // Générer automatiquement le numéro de membre
            $data['membershipNumber'] = $content->data->gsrn;
            $data['qrcode_url'] = $content->data->barcodeValue;*/

            // Traitement de l'image
            $validated['face_path'] = $this->handleImageUpload($request);

            $member = Member::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Membre créé avec succès'
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur interne : ' . $th->getMessage()
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
                'township_id' => 'nullable|exists:townships,id',
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
                'data' => $member->load('site', 'city', 'township', 'organisation', 'fonction')
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur, " .$th->getMessage()
            ], 500);
        }
    }

}
