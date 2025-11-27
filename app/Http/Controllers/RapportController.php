<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Rapport;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class RapportController extends Controller
{
    // 🔹 GET /rapports
    public function index()
    {
        try {
            $rapports = Rapport::with('stocks')->where('organization_id', auth()->user()->organization_id)->orderBy('created_at', 'desc')->paginate(10);

            return response()->json([
                'success' => true,
                'data' => $rapports,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des rapports',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // 🔹 POST /rapports
    public function store(Request $request)
    {
        try {
            // Validation des champs
            $validated = $request->validate([
                'substance' => 'required|string|max:255',
                'date_debut' => 'required|date',
                'date_fin' => 'required|date',
                'mesure' => 'nullable|string|max:50',
            ]);

            // Récupérer le membre et son site
            $user = auth()->user();
            $site = $user->member->site;

            if (!$site) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le membre n\'a pas de site associé.',
                ], 400);
            }

            // Convertir les dates en début et fin de journée
            $dateDebut = \Carbon\Carbon::parse($validated['date_debut'])->startOfDay();
            $dateFin = \Carbon\Carbon::parse($validated['date_fin'])->endOfDay();

            // Récupérer les stocks du site pour cette période
            $stocks = Stock::where('site_id', $site->id)
                ->whereBetween('created_at', [$dateDebut, $dateFin])
                ->get();

            if ($stocks->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun stock trouvé pour cette période.',
                ], 404);
            }

            // Création du rapport
            $rapport = Rapport::create([
                'substance' => $validated['substance'],
                'date_debut' => $validated['date_debut'],
                'date_fin' => $validated['date_fin'],
                'mesure' => $validated['mesure'] ?? null,
            ]);

            // Préparer le pivot avec qte = stock->qte
            $pivotData = $stocks->mapWithKeys(fn($stock) => [
                $stock->id => ['qte' => $stock->qte]
            ])->toArray();

            $rapport->stocks()->sync($pivotData);

            return response()->json([
                'success' => true,
                'message' => 'Rapport généré avec succès',
                'data' => $rapport->load('stocks'),
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération du rapport',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // 🔹 GET /rapports/{id}
    public function show($id)
    {
        try {
            $rapport = Rapport::with('stocks')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $rapport,
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Rapport introuvable',
            ], 404);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement du rapport',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // 🔹 PUT /rapports/{id}
    public function update(Request $request, $id)
    {
        try {
            $rapport = Rapport::findOrFail($id);

            $validated = $request->validate([
                'substance' => 'nullable|string|max:255',
                'date_debut' => 'nullable|date',
                'date_fin' => 'nullable|date',
                'mesure' => 'nullable|string|max:50',
            ]);

            // Récupérer le site du membre connecté
            $site = auth()->user()->site;

            if (!$site) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le membre n\'a pas de site associé.',
                ], 400);
            }

            // Convertir les dates pour filtrage
            $dateDebut = \Carbon\Carbon::parse($validated['date_debut'])->startOfDay();
            $dateFin = \Carbon\Carbon::parse($validated['date_fin'])->endOfDay();

            // Récupérer les stocks correspondant
            $stocks = Stock::where('site_id', $site->id)
                            ->whereBetween('created_at', [$dateDebut, $dateFin])
                            ->get();

            if ($stocks->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun stock trouvé pour cette période.',
                ]);
            }

            // Mettre à jour les champs du rapport
            $rapport->update([
                'substance' => $validated['substance'],
                'date_debut' => $validated['date_debut'],
                'date_fin' => $validated['date_fin'],
                'mesure' => $validated['mesure'] ?? null,
            ]);

            // Synchroniser le pivot avec les nouvelles quantités
            $pivotData = [];
            foreach ($stocks as $stock) {
                $pivotData[$stock->id] = ['qte' => $stock->qte];
            }

            $rapport->stocks()->sync($pivotData);

            return response()->json([
                'success' => true,
                'message' => 'Rapport mis à jour avec succès',
                'data' => $rapport->load('stocks'),
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Rapport introuvable',
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du rapport',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // 🔹 DELETE /rapports/{id}
    public function destroy($id)
    {
        try {
            $rapport = Rapport::findOrFail($id);
            $rapport->stocks()->detach(); // supprime pivot
            $rapport->delete();

            return response()->json([
                'success' => true,
                'message' => 'Rapport supprimé avec succès',
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Rapport introuvable',
            ], 404);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du rapport',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
