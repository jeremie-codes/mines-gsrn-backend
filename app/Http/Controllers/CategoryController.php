<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index ()
    {
        try {
            $categories = Category::orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'categories' => $categories
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
                'name' => 'required|string|max:5',
                'amount' => 'required|numeric',
                'currency' => 'required|string'
            ]);

            Category::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Catégorie créée avec succès'
            ], 201); // Code 201 pour "created"

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la catégorie : ' . $th->getMessage()
            ], 500);
        }
    }


    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'nullable|string|max:5',
                'amount' => 'nullable|numeric',
                'currency' => 'nullable|string',
            ]);

            $category = Category::findOrFail($id);

            $category->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Catégorie mise à jour avec succès',
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur : " . $th->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);

            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Catégorie supprimée avec succès.'
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression : ' . $th->getMessage()
            ], 500);
        }
    }


}
