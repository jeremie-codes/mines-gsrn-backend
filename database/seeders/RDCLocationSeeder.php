<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\City;
use App\Models\Township;

class RDCLocationSeeder extends Seeder
{
    public function run()
    {
        // Récupérer la RDC
        $rdc = Country::where('code', 'CD')->first();
        
        if (!$rdc) {
            $this->command->error('Le pays RDC doit être créé en premier');
            return;
        }

        // Définir toutes les provinces avec leurs villes principales
        $provinces = [
            'KIN' => [
                'name' => 'Kinshasa',
                'cities' => [
                    [
                        'code' => 'KIN',
                        'name' => 'Kinshasa',
                        'townships' => [
                            'Bandalungwa', 'Barumbu', 'Bumbu', 'Gombe', 'Kalamu',
                            'Kasa-Vubu', 'Kimbanseke', 'Kinshasa', 'Kintambo', 'Kisenso',
                            'Lemba', 'Limete', 'Lingwala', 'Makala', 'Maluku',
                            'Masina', 'Matete', 'Mont-Ngafula', 'Ndjili', 'Ngaba',
                            'Ngaliema', 'Ngiri-Ngiri', 'Nsele', 'Selembao'
                        ]
                    ]
                ]
            ],
            'BAS' => [
                'name' => 'Kongo Central',
                'cities' => [
                    [
                        'code' => 'MAT',
                        'name' => 'Matadi',
                        'townships' => ['Matadi', 'Nzanza', 'Mvuzi']
                    ],
                    [
                        'code' => 'BOM',
                        'name' => 'Boma',
                        'townships' => ['Boma', 'Kalamu', 'Nzadi']
                    ],
                    [
                        'code' => 'MBZ',
                        'name' => 'Mbanza-Ngungu',
                        'townships' => ['Mbanza-Ngungu', 'Gombe-Matadi', 'Kwilu-Ngongo']
                    ]
                ]
            ],
            'BAN' => [
                'name' => 'Bandundu',
                'cities' => [
                    [
                        'code' => 'BAN',
                        'name' => 'Bandundu',
                        'townships' => ['Bandundu', 'Masiala', 'Bagata']
                    ],
                    [
                        'code' => 'KIK',
                        'name' => 'Kikwit',
                        'townships' => ['Kikwit', 'Lukemi', 'Nzinda']
                    ]
                ]
            ],
            'EQU' => [
                'name' => 'Équateur',
                'cities' => [
                    [
                        'code' => 'MBD',
                        'name' => 'Mbandaka',
                        'townships' => ['Mbandaka', 'Wangata', 'Bolenge']
                    ],
                    [
                        'code' => 'LIS',
                        'name' => 'Lisala',
                        'townships' => ['Lisala', 'Bongandanga', 'Bumba']
                    ]
                ]
            ],
            'KAS' => [
                'name' => 'Kasaï',
                'cities' => [
                    [
                        'code' => 'KAN',
                        'name' => 'Kananga',
                        'townships' => ['Kananga', 'Katoka', 'Ndesha']
                    ],
                    [
                        'code' => 'TSH',
                        'name' => 'Tshikapa',
                        'townships' => ['Tshikapa', 'Kamonia', 'Ilebo']
                    ]
                ]
            ],
            'KAO' => [
                'name' => 'Kasaï-Oriental',
                'cities' => [
                    [
                        'code' => 'MBJ',
                        'name' => 'Mbuji-Mayi',
                        'townships' => ['Mbuji-Mayi', 'Bipemba', 'Diulu', 'Kanshi', 'Muya']
                    ],
                    [
                        'code' => 'KAB',
                        'name' => 'Kabinda',
                        'townships' => ['Kabinda', 'Lomela', 'Tshofa']
                    ]
                ]
            ],
            'KAT' => [
                'name' => 'Katanga',
                'cities' => [
                    [
                        'code' => 'LUB',
                        'name' => 'Lubumbashi',
                        'townships' => ['Annexe', 'Kampemba', 'Katuba', 'Kenya', 'Lubumbashi', 'Ruashi', 'Rwashi']
                    ],
                    [
                        'code' => 'LIK',
                        'name' => 'Likasi',
                        'townships' => ['Likasi', 'Shituru', 'Panda']
                    ],
                    [
                        'code' => 'KOL',
                        'name' => 'Kolwezi',
                        'townships' => ['Kolwezi', 'Dilala', 'Manika']
                    ]
                ]
            ],
            'MAN' => [
                'name' => 'Maniema',
                'cities' => [
                    [
                        'code' => 'KIN',
                        'name' => 'Kindu',
                        'townships' => ['Kindu', 'Alunguli', 'Mikelenge']
                    ],
                    [
                        'code' => 'KAS',
                        'name' => 'Kasongo',
                        'townships' => ['Kasongo', 'Kibombo', 'Kama']
                    ]
                ]
            ],
            'NKI' => [
                'name' => 'Nord-Kivu',
                'cities' => [
                    [
                        'code' => 'GOM',
                        'name' => 'Goma',
                        'townships' => ['Goma', 'Karisimbi', 'Nyiragongo']
                    ],
                    [
                        'code' => 'BEN',
                        'name' => 'Beni',
                        'townships' => ['Beni', 'Mulekera', 'Rwenzori']
                    ],
                    [
                        'code' => 'BUT',
                        'name' => 'Butembo',
                        'townships' => ['Butembo', 'Bulengera', 'Kimemi']
                    ]
                ]
            ],
            'ORI' => [
                'name' => 'Province Orientale',
                'cities' => [
                    [
                        'code' => 'KIS',
                        'name' => 'Kisangani',
                        'townships' => ['Kisangani', 'Makiso', 'Tshopo', 'Kabondo', 'Lubunga', 'Mangobo']
                    ],
                    [
                        'code' => 'ISI',
                        'name' => 'Isiro',
                        'townships' => ['Isiro', 'Rungu', 'Wamba']
                    ]
                ]
            ],
            'SKI' => [
                'name' => 'Sud-Kivu',
                'cities' => [
                    [
                        'code' => 'BUK',
                        'name' => 'Bukavu',
                        'townships' => ['Bagira', 'Ibanda', 'Kadutu']
                    ],
                    [
                        'code' => 'UVI',
                        'name' => 'Uvira',
                        'townships' => ['Uvira', 'Kiliba', 'Sange']
                    ]
                ]
            ]
        ];

        // Créer les villes et communes pour chaque province
        foreach ($provinces as $provinceCode => $provinceData) {
            $this->command->info("Création des données pour la province: {$provinceData['name']} ({$provinceCode})");
            
            foreach ($provinceData['cities'] as $cityData) {
                // Créer la ville
                $city = City::create([
                    'code' => $cityData['code'],
                    'name' => $cityData['name'],
                    'province_code' => $provinceCode,
                    'country_id' => $rdc->id
                ]);

                $this->command->info("  - Ville créée: {$city->name}");

                // Créer les communes/townships
                foreach ($cityData['townships'] as $townshipName) {
                    Township::create([
                        'code' => strtoupper(substr($townshipName, 0, 3)),
                        'name' => $townshipName,
                        'city_id' => $city->id
                    ]);
                }

                $this->command->info("    - {$city->townships->count()} communes créées");
            }
        }

        $this->command->info("Création terminée !");
        $this->command->info("Total: " . City::count() . " villes et " . Township::count() . " communes créées");
    }
}