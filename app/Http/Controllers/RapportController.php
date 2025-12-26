<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Rapport;
use App\Models\Stock;
use App\Services\UnitConverter;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class RapportController extends Controller
{
    // 🔹 GET /rapports
    public function index()
    {
        try {
            $user = auth()->user();
            $organizationId = $user->assigned_organization_id;

            $rapports = Rapport::with('stocks') // Charger les stocks pour chaque rapport
                ->where('organization_id', $organizationId)
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            // Transformer chaque rapport pour calculer les totaux
            $rapports->getCollection()->transform(function ($rapport) {

                // Grouper par substance_code et sommer les qtes du pivot
                $totaux = $rapport->stocks
                    ->groupBy('substance_code')
                    ->map(function ($items) {
                        $unit = $items->first()->converted->metric; // unité commune
                        $qtySum = $items->sum(fn($s) => floatval($s->converted->qte));
                        return [
                            'substance_code' => $items->first()->substance_code,
                            'qte' => $qtySum,
                            'metric' => $unit
                        ];
                    })
                    ->values(); // reset des clés

                // Supprimer la propriété stocks pour la réponse
                unset($rapport->stocks);

                // Ajouter la propriété totaux
                $rapport->stocks = $totaux;

                return $rapport;
            });

            return response()->json([
                'success' => true,
                'data' => $rapports,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des rapports',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // Validation
            $validated = $request->validate([
                'date_debut' => 'required|date',
                'date_fin' => 'required|date',
            ]);

            $user = auth()->user();
            $organizationId = $user->assigned_organization_id;

            $dateDebut = \Carbon\Carbon::parse($validated['date_debut'])->startOfDay();
            $dateFin = \Carbon\Carbon::parse($validated['date_fin'])->endOfDay();

            // Récupérer les stocks des sites liés à cette organisation
            $stocks = Stock::whereHas('site', function ($query) use ($organizationId) {
                    $query->where('organization_id', $organizationId);
                })
                ->whereBetween('created_at', [$dateDebut, $dateFin])
                ->orderBy('created_at', 'desc')
                ->get();

            if ($stocks->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun stock trouvé pour cette période.',
                ], 404);
            }

            // 2️⃣ Préparer le pivot avec conversion
            $pivotData = [];

            foreach ($stocks as $stock) {
                // Convertir la qte de l’unité du stock → unité finale
                $convertedQty = UnitConverter::convert(
                    substanceCode: $stock->substance_code,
                    qty: $stock->qte,
                    from: $stock->mesure,
                );

                $pivotData[$stock->id] = [
                    'qte' => $convertedQty['qty'],
                    'metric' => $convertedQty['unit']
                ];
            }

            // 1️⃣ Créer le rapport
            $rapport = Rapport::create([
                'reference' => Rapport::generateReference(),
                'date_debut' => $validated['date_debut'],
                'date_fin' => $validated['date_fin'],
                'organization_id' => $organizationId
            ]);

            // 3️⃣ Synchroniser le pivot
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
    public function show($ref)
    {
        try {
            $rapport = Rapport::with('stocks')->where('reference', $ref)->firstOrFail();

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
                'date_debut' => 'nullable|date',
                'date_fin' => 'nullable|date',
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
