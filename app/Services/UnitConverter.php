<?php

namespace App\Services;

use Exception;
use GuzzleHttp\Client;

class UnitConverter
{

    /**
     * Tableau standard de conversions — multipliers vers l'unité inférieure.
     * Exemple : 1 kg = 1000 g → 'kg' => ['g' => 1000]
     */
    protected static $conversions = [
    'g' => [
        'g'  => 1,
        'mg' => 1000,
        'kg' => 0.001,
        't'  => 0.000001,
        'ct' => 5,            // 1 g = 5 ct (car 1 ct = 0.2 g)
    ],

    'kg' => [
        'kg' => 1,
        'g'  => 1000,
        'mg' => 1_000_000,
        't'  => 0.001,
        'ct' => 5000,         // 1 kg = 1000 g → 1000 * 5 = 5000 ct
    ],

    't' => [
        't'  => 1,
        'kg' => 1000,
        'g'  => 1_000_000,
        'mg' => 1_000_000_000,
        'ct' => 5_000_000,    // 1t = 1M g → 1M * 5 = 5M ct
    ],

    'ct' => [
        'ct' => 1,
        'mg' => 200,          // 1 ct = 200 mg
        'g'  => 0.2,          // 1 ct = 0.2 g
        'kg' => 0.0002,       // 0.2 g / 1000
        't'  => 0.0000002,    // 0.0002 kg / 1000
    ],

    'mg' => [
        'mg' => 1,
        'g'  => 0.001,
        'kg' => 0.000001,
        't'  => 0.000000001,
        'ct' => 0.005,        // 1 mg = 0.001 g → 0.001 g = 0.005 ct (car 1 ct = 0.2 g)
    ],
];


    public static function convert(string $substanceCode, float $qty, string $from)
    {
        $from = strtolower($from);

        // ---- 1. Récupérer l’unité de référence depuis ton API ----
        $client = new Client();
        $response = $client->get('https://mines-doc.devi7.in/api/rest/substances/' . $substanceCode);
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
        // Appliquer le multiplicateur
        $multiplier = self::$conversions[$from][$to];
        return [
            'substance' => $data->name,
            'qty' => $qty * $multiplier,
            'unit' => $to
        ];
    }

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
