<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Site;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class SiteController extends Controller
{
    public function index()
    {
        try {

            $sites = Site::with('city', 'organization')->orderBy('created_at', 'desc')->paginate(10);

            return response()->json([
                'success' => true,
                'sites' => $sites,
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur, " .$th->getMessage()
            ], 500);
        }
    }
    public function appIndex()
    {
        try {

            $sites = Site::with('city', 'organization')->orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'sites' => $sites,
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
            $request->validate([
                'name' => 'required|string|max:255',
                'organization_id' => 'required|exists:organizations,id',
            ]);

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

            $response = $client->request('POST', env('API_GLN_GENERATE'), [
                'json' => [
                    'locationName' => $request->name,
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

            $data = $request->all();
            $data['gln'] = $content->data->gln;

            $site = Site::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Site créé avec succès.',
                'site' => $site
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur, " .$th->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
         try {

            $site = Site::with('city', 'organization')->findOrFail($id);

            if (!$site) {
                return response()->json([
                    'success' => false,
                    'message' => 'Site non trouvé.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'site' => $site,
                'message' => 'Site récupéré avec succès.'
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur, " .$th->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {

        try {

            $request->validate([
                'organization_id' => 'nullable|exists:organizations,id',
                'gln' => 'nullable|string|max:255',
                'name' => 'nullable|string|max:255',
            ]);

            $data = $request->all();
            $site = Site::findOrFail($id);

            if (!$site) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de mise à jour, Site non trouvé.'
                ], 404);
            }

            $site->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Site mise à jour avec succès.',
                'site' => $site
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

            $site = Site::findOrFail($id);

            if (!$site) {
                return response()->json([
                    'success' => false,
                    'message' => 'Site non trouvé.'
                ], 404);
            }

            if ($site->members()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer le site, il est associé à des membres.'
                ], 400);
            }

            $site->delete();

            return response()->json([
                'success' => true,
                'message' => 'Site supprimé avec succès.'
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur, " .$th->getMessage()
            ], 500);
        }
    }
}
