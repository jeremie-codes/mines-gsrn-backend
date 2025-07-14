<?php

namespace App\Http\Controllers;

use App\Models\Cotisation;
use Illuminate\Http\Request;

class CotisationController extends Controller
{

    public function index ()
    {
        try {
            $cotisations = Cotisation::orderBy('created_at', 'desc')->get();

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

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'type' => 'required|in:cash,flexpaie',
                'amount' => 'required|numeric|min:0',
                'currency' => 'required|string|max:10',
                'status' => 'nullable|string|max:50',
                'reference' => 'nullable|string|max:255',
                'description' => 'nullable|string|max:255',
                'created_at' => 'nullable|date'
            ]);

            // Création de la cotisation
            $cotisation = Cotisation::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Cotisation enregistrée avec succès.',
                'data' => $cotisation
            ], 201); // 201 = Created

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

            if ($cotisation->status === 'success') {
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

            $cotisation->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Cotisation mise à jour avec succès.',
                'data' => $cotisation
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
            if ($cotisation->status === 'validée') {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer une cotisation déjà validée.'
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


}
