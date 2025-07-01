<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Fonction;

class FonctionSeeder extends Seeder
{
    public function run()
    {
        $fonctions = [
            'Coordonateur',
            'Chef de Pool',
            'Membre'
        ];

        foreach ($fonctions as $fonction) {
            Fonction::create(['name' => $fonction]);
        }
    }
}
