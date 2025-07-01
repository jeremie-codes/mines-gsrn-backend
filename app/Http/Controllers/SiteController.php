<?php

namespace App\Http\Controllers;

use App\Models\Site;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function index()
    {

        try {

            $sites = Site::with('pools', 'members')->active()->paginate(10);

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

        // return redirect()->route('sites.index')
        //     ->with('success', 'Site créé avec succès.');

        try {

            $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:3|unique:sites,code',
                'location' => 'nullable|string|max:255',
                'is_active' => 'boolean'
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

    public function show(Site $id)
    {
         try {

            $site = Site::with('pools', 'members')->findOrFail($id->id);

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

    public function edit(Site $id)
    {

        try {
            $site = Site::findOrFail($id->id);

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
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:3|unique:sites,code,' . $id,
                'location' => 'nullable|string|max:255',
                'is_active' => 'boolean'
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
                'site' => $site,
                'message' => 'Site mise à jour avec succès.'
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur, " .$th->getMessage()
            ], 500);
        }
    }

    public function destroy(Site $id)
    {

        try {

            $site = Site::findOrFail($id->id);

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
            $site->save();

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
