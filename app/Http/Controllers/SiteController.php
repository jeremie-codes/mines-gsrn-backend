<?php

namespace App\Http\Controllers;

use App\Models\Site;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function index()
    {

        try {

            $sites = Site::paginate(10);

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

    public function create()
    {
        return view('sites.create');
    }

    public function store(Request $request)
    {

        try {

            $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:3|unique:sites,code',
                'location' => 'nullable|string|max:255',
                'city_id' => 'nullable|exists:cities,id',
                'is_active' => 'nullable|boolean'
            ]);

            // Convertir le code en majuscules
            $data = $request->all();
            $data['code'] = strtoupper($data['code']);

            $site = Site::create($data);

            return response()->json([
                'success' => true,
                'site' => $site,
                'message' => 'Site créé avec succès.'
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

            $site = Site::with('coordonateur','city')->findOrFail($id);

            // $site->membership_counter = $site->members->count();
            // $site->save();

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

    public function edit($id)
    {

        try {
            $site = Site::findOrFail($id);

            if (!$site) {
                return response()->json([
                    'success' => false,
                    'message' => 'Site non trouvé.'
                ], 404);
            }

            return response()->json([
                'success' => true,
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
                // 'site_id' => 'required|exists:sites,id',
                'city_id' => 'nullable|exists:cities,id',
                'name' => 'nullable|string|max:255',
                'code' => 'nullable|string|max:3|unique:sites,code,' . $id,
                'location' => 'nullable|string|max:255',
                'is_active' => 'nullable|boolean',
            ]);

            // Convertir le code en majuscules
            $data = $request->all();
            $data['code'] = strtoupper($data['code']);

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
                'message' => 'Site mise à jour avec succès.'
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

            // Vérifier si le site a des pools ou des membres associés
            if ($site->pools()->count() > 0 || $site->members()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer le site, il est associé à des pools ou des membres.'
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
