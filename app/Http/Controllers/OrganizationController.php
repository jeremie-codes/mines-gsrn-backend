<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Organization;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function index()
    {

        try {
            $organizations = Organization::orderBy('created_at', 'desc')->paginate(10);

            return response()->json([
                'success' => true,
                'organizations' => $organizations
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur, " .$th->getMessage()
            ], 500);
        }
    }

    public function indexApi()
    {

        try {
            $organizations = Organization::orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'organizations' => $organizations
            ], 200);

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
                'gcp' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'is_active' => 'nullable|boolean'
            ]);

            $organization = Organization::create($request->all());

            $client = new Client([
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
            ]);

            $response = $client->request('POST', env('API_GLN_GENERATE'), [
                'json' => [
                    "name" => $request->name,
                    "customer" => $request->name,
                    "description" => $request->description,
                    "companyPrefix" => $request->description
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

            return response()->json([
                'success' => true,
                'message' => 'organisation créé avec succès.',
                'organization' => $organization
            ], 200);

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
            $organization = Organization::findOrFail($id);

            if (!$organization) {
                return response()->json([
                    'success' => false,
                    'message' => 'organisation non trouvé.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'organization' => $organization
            ], 200);
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
                'name' => 'nullable|string|max:255',
                'gcp' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'is_active' => 'nullable|boolean'
            ]);

            $organization = Organization::findOrFail($id);

            if (!$organization) {
                return response()->json([
                    'success' => false,
                    'message' => 'organisation non trouvé.'
                ], 404);
            }

            $organization->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'organisation mis à jour avec succès.',
                'organization' => $organization
            ], 200);

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
            $organization = Organization::findOrFail($id);

            if (!$organization) {
                return response()->json([
                    'success' => false,
                    'message' => 'organisation non trouvé.'
                ], 404);
            }

            // Vérifier si le organisation a des membres associés
            if ($organization->members()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer le organisation car il a des membres associés.'
                ], 400);
            }

            $organization->delete();

            return response()->json([
                'success' => true,
                'message' => 'organisation supprimé avec succès.'
            ], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'success' => false,
                'message' => "Erreur, " .$th->getMessage()
            ], 500);
        }

    }
}
