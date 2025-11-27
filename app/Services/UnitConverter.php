<?php

namespace App\Services;

use Exception;

class UnitConverter
{
    /**
     * Unité de référence par substance
     */
    protected static $referenceUnits = [
        'gold' => 'g',          // Or
        'Diamant' => 'ct',      // Carat
        'OR-123-Min' => 'kg',
        'cassiterite' => 'kg',
        'cobalt' => 'kg',
        'copper' => 'kg',
        'silver' => 'g',
    ];

    /**
     * Tableau standard de conversions — multipliers vers l'unité inférieure.
     * Exemple : 1 kg = 1000 g → 'kg' => ['g' => 1000]
     */
    protected static $conversions = [
        'g' => [
            'kg' => 0.001,
            'mg' => 1000,
        ],
        'kg' => [
            'g' => 1000,
            't' => 0.001,
        ],
        't' => [
            'kg' => 1000,
        ],
        'ct' => [
            'g' => 0.2,
        ],
        'mg' => [
            'g' => 0.001,
        ],
    ];

    /**
     * Convertir une quantité pour une substance donnée
     */
    public static function convert(string $substanceCode, float $qty, string $from)
    {
        $from = strtolower($from);

        // Vérifier si la matière existe
        if (!isset(self::$referenceUnits[$substanceCode])) {
            throw new Exception("Substance inconnue : $substanceCode");
        }

        // Vérifier si l'unité de référence est correcte
        $to = self::$referenceUnits[$substanceCode];

        // Vérifier si la conversion interne existe
        if (!isset(self::$conversions[$from][$to])) {
            throw new Exception("Impossible de convertir $from vers $to pour $substanceCode");
        }

        // Appliquer le multiplicateur
        $multiplier = self::$conversions[$from][$to];
        return $qty * $multiplier;
    }

    /*public static function convert(string $substanceCode, float $qty, string $from)
    {
        $from = strtolower($from);

        // ---- 1. Récupérer l’unité de référence depuis ton API ----
        $client = new Client();
        $response = $client->get('http://localhost:8000/api/api/rest/substances/' . $substanceCode);
        $data = json_decode($response->getBody()->getContents());

        if (!$data || !isset($data->metric->code)) {
            throw new Exception("Impossible de trouver l'unité de référence pour $substanceCode");
        }

        $to = strtolower($data->metric->code);

        // ---- 2. Vérifier si la conversion existe ----
        if (!isset(self::$conversions[$from][$to])) {
            throw new Exception("Impossible de convertir $from vers $to pour $substanceCode");
        }

        // ---- 3. Appliquer la conversion ----
        $multiplier = self::$conversions[$from][$to];

        return $qty * $multiplier;
    }*/

    /**
     * Normalisation automatique :
     * ex: 1000 kg → 1 t
     */
    public static function normalize(float $qty, string $unit)
    {
        $unit = strtolower($unit);

        // Si kg => t
        if ($unit === 'kg' && $qty >= 1000) {
            return [
                'value' => $qty / 1000,
                'unit'  => 't',
            ];
        }

        // Si g => kg
        if ($unit === 'g' && $qty >= 1000) {
            return [
                'value' => $qty / 1000,
                'unit'  => 'kg',
            ];
        }

        return [
            'value' => $qty,
            'unit'  => $unit,
        ];
    }
}
