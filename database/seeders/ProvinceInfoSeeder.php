<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProvinceInfoSeeder extends Seeder
{
    /**
     * Informations détaillées sur les provinces de la RDC
     * 
     * Cette classe contient toutes les informations sur les 26 provinces
     * de la République Démocratique du Congo selon la constitution de 2006
     */
    public function run()
    {
        $this->command->info("=== PROVINCES DE LA RÉPUBLIQUE DÉMOCRATIQUE DU CONGO ===");
        $this->command->info("");
        
        $provinces = [
            'KIN' => [
                'name' => 'Kinshasa',
                'capital' => 'Kinshasa',
                'type' => 'Ville-Province',
                'population' => '17,071,000',
                'superficie' => '9,965 km²'
            ],
            'BAS' => [
                'name' => 'Kongo Central',
                'capital' => 'Matadi',
                'type' => 'Province',
                'population' => '5,575,000',
                'superficie' => '53,920 km²'
            ],
            'KWI' => [
                'name' => 'Kwilu',
                'capital' => 'Bandundu',
                'type' => 'Province',
                'population' => '5,174,000',
                'superficie' => '78,219 km²'
            ],
            'KWA' => [
                'name' => 'Kwango',
                'capital' => 'Kenge',
                'type' => 'Province',
                'population' => '1,994,000',
                'superficie' => '89,974 km²'
            ],
            'MAI' => [
                'name' => 'Mai-Ndombe',
                'capital' => 'Inongo',
                'type' => 'Province',
                'population' => '1,768,000',
                'superficie' => '127,465 km²'
            ],
            'EQU' => [
                'name' => 'Équateur',
                'capital' => 'Mbandaka',
                'type' => 'Province',
                'population' => '1,626,000',
                'superficie' => '103,902 km²'
            ],
            'MOM' => [
                'name' => 'Mongala',
                'capital' => 'Lisala',
                'type' => 'Province',
                'population' => '1,793,000',
                'superficie' => '58,141 km²'
            ],
            'NOR' => [
                'name' => 'Nord-Ubangi',
                'capital' => 'Gbadolite',
                'type' => 'Province',
                'population' => '1,482,000',
                'superficie' => '56,644 km²'
            ],
            'SUD' => [
                'name' => 'Sud-Ubangi',
                'capital' => 'Gemena',
                'type' => 'Province',
                'population' => '2,744,000',
                'superficie' => '51,648 km²'
            ],
            'TSH' => [
                'name' => 'Tshuapa',
                'capital' => 'Boende',
                'type' => 'Province',
                'population' => '1,316,000',
                'superficie' => '132,940 km²'
            ],
            'KAS' => [
                'name' => 'Kasaï',
                'capital' => 'Kananga',
                'type' => 'Province',
                'population' => '3,199,000',
                'superficie' => '95,631 km²'
            ],
            'KAC' => [
                'name' => 'Kasaï Central',
                'capital' => 'Kananga',
                'type' => 'Province',
                'population' => '3,199,000',
                'superficie' => '59,111 km²'
            ],
            'KAO' => [
                'name' => 'Kasaï Oriental',
                'capital' => 'Mbuji-Mayi',
                'type' => 'Province',
                'population' => '2,702,000',
                'superficie' => '9,545 km²'
            ],
            'LOM' => [
                'name' => 'Lomami',
                'capital' => 'Kabinda',
                'type' => 'Province',
                'population' => '2,048,000',
                'superficie' => '56,426 km²'
            ],
            'SAN' => [
                'name' => 'Sankuru',
                'capital' => 'Lusambo',
                'type' => 'Province',
                'population' => '1,888,000',
                'superficie' => '104,331 km²'
            ],
            'MAN' => [
                'name' => 'Maniema',
                'capital' => 'Kindu',
                'type' => 'Province',
                'population' => '2,333,000',
                'superficie' => '132,250 km²'
            ],
            'NKI' => [
                'name' => 'Nord-Kivu',
                'capital' => 'Goma',
                'type' => 'Province',
                'population' => '8,147,000',
                'superficie' => '59,631 km²'
            ],
            'SKI' => [
                'name' => 'Sud-Kivu',
                'capital' => 'Bukavu',
                'type' => 'Province',
                'population' => '5,772,000',
                'superficie' => '65,070 km²'
            ],
            'TAN' => [
                'name' => 'Tanganyika',
                'capital' => 'Kalemie',
                'type' => 'Province',
                'population' => '2,482,000',
                'superficie' => '134,940 km²'
            ],
            'HKA' => [
                'name' => 'Haut-Katanga',
                'capital' => 'Lubumbashi',
                'type' => 'Province',
                'population' => '3,960,000',
                'superficie' => '132,425 km²'
            ],
            'LUA' => [
                'name' => 'Lualaba',
                'capital' => 'Kolwezi',
                'type' => 'Province',
                'population' => '2,570,000',
                'superficie' => '121,308 km²'
            ],
            'HLO' => [
                'name' => 'Haut-Lomami',
                'capital' => 'Kamina',
                'type' => 'Province',
                'population' => '2,540,000',
                'superficie' => '108,204 km²'
            ],
            'BAS' => [
                'name' => 'Bas-Uele',
                'capital' => 'Buta',
                'type' => 'Province',
                'population' => '1,093,000',
                'superficie' => '148,331 km²'
            ],
            'HAU' => [
                'name' => 'Haut-Uele',
                'capital' => 'Isiro',
                'type' => 'Province',
                'population' => '1,920,000',
                'superficie' => '89,683 km²'
            ],
            'ITO' => [
                'name' => 'Ituri',
                'capital' => 'Bunia',
                'type' => 'Province',
                'population' => '4,241,000',
                'superficie' => '65,658 km²'
            ],
            'TSH' => [
                'name' => 'Tshopo',
                'capital' => 'Kisangani',
                'type' => 'Province',
                'population' => '2,614,000',
                'superficie' => '199,567 km²'
            ]
        ];

        foreach ($provinces as $code => $info) {
            $this->command->info("Code: {$code} | {$info['name']} | Capitale: {$info['capital']}");
        }

        $this->command->info("");
        $this->command->info("Total: " . count($provinces) . " provinces");
        $this->command->info("Note: Les données utilisées dans le seeder principal sont simplifiées pour les besoins du système.");
    }
}