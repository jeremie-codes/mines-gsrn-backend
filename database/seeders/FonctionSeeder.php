<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Fonction;

class FonctionSeeder extends Seeder
{
    public function run()
    {
        $fonctions = [
            'Président',
            'Vice-Président',
            'Secrétaire',
            'Trésorier',
            'Coordonateur',
            'Chef de Pool',
            'Membre'
        ];

        foreach ($fonctions as $fonction) {
            Fonction::create(['nom' => $fonction]);
        }
    }
}