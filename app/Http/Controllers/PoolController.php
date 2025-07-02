<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Pool;
use App\Models\Site;
use Illuminate\Http\Request;

class PoolController extends Controller
{
    public function index()
    {

        try {

            $pools = Pool::paginate(10);

            return response()->json([
                'success' => true,
                'pools' => $pools
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur, " .$th->getMessage()
            ], 500);
        }
    }

    public function getChefs()
    {

        try {

            $chefs = Member::with('fonction')
                ->whereHas('fonction', function ($query) {
                    $query->where('nom', 'Chef de Pool');
                })
                ->get();

            return response()->json([
                'success' => true,
                'pools' => $chefs
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur, " .$th->getMessage()
            ], 500);
        }
    }

    public function create()
    {

        try {

            $sites = Site::get();

            return response()->json([
                'success' => true,
                'sites' => $sites
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
                'site_id' => 'required|exists:sites,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'is_active' => 'boolean|default:true'
            ]);

            $pool = Pool::create($request->all());

            return response()->json([
                'success' => true,
                'pool' => $pool,
                'message' => 'Pool créé avec succès.'
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
            $pool = Pool::with('site', 'members')->findOrFail($id);

            if (!$pool) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pool non trouvé.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'pool' => $pool
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur, " .$th->getMessage()
            ], 500);
        }
    }

    public function edit(Pool $id)
    {
        try {

            $sites = Site::get();
            $pool = Pool::findOrFail($id);

            return response()->json([
                'success' => true,
                'pool' => $pool,
                'sites' => $sites
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur, " .$th->getMessage()
            ], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $request->validate([
                'pool_id' => 'required|exists:pools,id',
                'site_id' => 'required|exists:sites,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'is_active' => 'boolean|default:true'
            ]);

            $pool = Pool::findOrFail($request->pool_id);

            if (!$pool) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pool non trouvé.'
                ], 404);
            }

            $pool->update($request->all());

            return response()->json([
                'success' => true,
                'pool' => $pool,
                'message' => 'Pool mis à jour avec succès.'
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur, " .$th->getMessage()
            ], 500);
        }
    }

    public function destroy(Pool $id)
    {

        try {
            $pool = Pool::findOrFail($id);

            if (!$pool) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pool non trouvé.'
                ], 404);
            }

            // Vérifier si le pool a des membres associés
            if ($pool->members()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer le pool car il a des membres associés.'
                ], 400);
            }

            $pool->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pool supprimé avec succès.'
            ], 200);
        } catch (\Throwable $th) {
            //throw $th;
        }

    }
}
