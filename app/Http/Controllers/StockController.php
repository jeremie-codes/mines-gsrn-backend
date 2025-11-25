<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class StockController extends Controller
{
    // 🔹 GET /stocks
    public function index()
    {
        try {
            $stocks = Stock::where('site_id', auth()->user()->site_id)->orderBy('created_at', 'desc')->paginate(10);

            return response()->json([
                'success' => true,
                'data' => $stocks,
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des stocks',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // 🔹 POST /stocks
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'substance' => 'required|string|max:255',
                'collecteur' => 'required|string|max:255',
                'qte' => 'required|numeric',
                'mesure' => 'required|string',
                'date_collecte' => 'nullable|date',
            ]);

            $validated['site_id'] = auth()->user()->site_id;

            $stock = Stock::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Stock créé avec succès',
                'data' => $stock,
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du stock',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // 🔹 GET /stocks/{id}
    public function show($id)
    {
        try {
            $stock = Stock::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $stock,
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Stock introuvable',
            ], 404);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement du stock',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // 🔹 PUT /stocks/{id}
    public function update(Request $request, $id)
    {
        try {
            $stock = Stock::findOrFail($id);

            // Vérifier que le stock a été créé aujourd'hui
            $today = now()->startOfDay();
            $createdAt = $stock->created_at->startOfDay();

            if (!$createdAt->equalTo($today)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous ne pouvez modifier que les stocks créés aujourd\'hui.',
                ], 403);
            }

            $validated = $request->validate([
                'substance' => 'required|string|max:255',
                'collecteur' => 'required|string|max:255',
                'qte' => 'required|numeric',
                'mesure' => 'required|string',
                'date_collecte' => 'nullable|date',
            ]);

            $stock->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Stock mis à jour avec succès',
                'data' => $stock,
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Stock introuvable',
            ], 404);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du stock',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    // 🔹 DELETE /stocks/{id}
    public function destroy($id)
    {
        try {
            $stock = Stock::findOrFail($id);
            $stock->delete();

            return response()->json([
                'success' => true,
                'message' => 'Stock supprimé avec succès',
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Stock introuvable',
            ], 404);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du stock',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
