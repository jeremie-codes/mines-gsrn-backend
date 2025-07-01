<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Member;
use App\Models\Site;
use App\Models\Pool;
use App\Models\Fonction;
use App\Models\City;
use App\Models\Township;
use Faker\Factory as Faker;

class MemberSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('fr_FR');

        // Récupérer les données nécessaires
        $sites = Site::all();
        $pools = Pool::all();
        $fonctions = Fonction::all();
        $cities = City::all();
        $townships = Township::all();

        if ($sites->isEmpty()) {
            $this->command->error('Aucun site trouvé. Veuillez d\'abord exécuter le SiteSeeder.');
            return;
        }

        $this->command->info('Création de membres avec données géographiques...');

        // Créer 5 membres avec des données réalistes
        for ($i = 1; $i <= 5; $i++) {
            // Sélectionner aléatoirement une ville et une commune
            $city = $cities->random();
            $cityTownships = $townships->where('city_id', $city->id);
            $township = $cityTownships->isNotEmpty() ? $cityTownships->random() : null;

            // Sélectionner un site aléatoire
            $site = $sites->random();

            // Sélectionner un pool aléatoire (optionnel)
            $pool = $pools->isNotEmpty() && $faker->boolean(70) ? $pools->random() : null;

            // Sélectionner une fonction aléatoire (optionnel)
            $fonction = $fonctions->isNotEmpty() && $faker->boolean(80) ? $fonctions->random() : null;

            // Générer des noms réalistes congolais
            $firstNames = [
                'Jean', 'Marie', 'Pierre', 'Paul', 'Joseph', 'Emmanuel', 'Grace', 'Patience',
                'Espoir', 'Joie', 'Paix', 'Amour', 'Fidèle', 'Béni', 'Gloire', 'Victoire',
                'Merci', 'Chance', 'Bonheur', 'Lumière', 'Sagesse', 'Force', 'Courage',
                'Mukendi', 'Kabongo', 'Tshimanga', 'Mbuyi', 'Ngoy', 'Kalala', 'Ilunga',
                'Kasongo', 'Mwamba', 'Katanga', 'Luboya', 'Mujinga', 'Kapinga', 'Mulamba'
            ];

            $lastNames = [
                'Kabila', 'Tshisekedi', 'Katumbi', 'Bemba', 'Kamerhe', 'Muzito', 'Ruberwa',
                'Mende', 'Lumumba', 'Kasavubu', 'Mobutu', 'Mulele', 'Gizenga', 'Kamitatu',
                'Mukendi', 'Kabongo', 'Tshimanga', 'Mbuyi', 'Ngoy', 'Kalala', 'Ilunga',
                'Kasongo', 'Mwamba', 'Katanga', 'Luboya', 'Mujinga', 'Kapinga', 'Mulamba',
                'Ndala', 'Mbala', 'Nkulu', 'Mpiana', 'Wemba', 'Koffi', 'Fally', 'Werrason'
            ];

            $middleNames = [
                'wa', 'bin', 'ben', 'de', 'du', 'le', 'la', 'van', 'von', 'Mac', 'Mc',
                'Mwana', 'Muana', 'Bana', 'Tatu', 'Nkashama', 'Ngalula', 'Mpiana'
            ];

            $genders = ['M', 'F'];

            $firstName = $faker->randomElement($firstNames);
            $lastName = $faker->randomElement($lastNames);
            $middleName = $faker->boolean(60) ? $faker->randomElement($middleNames) : null;

            // Générer un numéro de téléphone congolais
            $phoneFormats = [
                '+243 9' . $faker->numerify('## ### ###'),
                '+243 8' . $faker->numerify('## ### ###'),
                '+243 99' . $faker->numerify('# ### ###'),
                '+243 97' . $faker->numerify('# ### ###'),
                '+243 81' . $faker->numerify('# ### ###'),
                '+243 82' . $faker->numerify('# ### ###'),
                '+243 83' . $faker->numerify('# ### ###'),
                '+243 84' . $faker->numerify('# ### ###'),
                '+243 85' . $faker->numerify('# ### ###'),
                '+243 89' . $faker->numerify('# ### ###'),
            ];

            $member = Member::create([
                'firstname' => $firstName,
                'lastname' => $lastName,
                'middlename' => $middleName,
                'phone' => $faker->randomElement($phoneFormats),
                'gender' => $faker->randomElement($genders),
                'site_id' => $site->id,
                'city_id' => $city->id,
                'township_id' => $township ? $township->id : null,
                'pool_id' => $pool ? $pool->id : null,
                'libelle_pool' => !$pool && $faker->boolean(30) ? $faker->words(2, true) : null,
                'fonction_id' => $fonction ? $fonction->id : null,
                'is_active' => $faker->boolean(90), // 90% de chance d'être actif
                'created_at' => $faker->dateTimeBetween('-2 years', 'now'),
                'updated_at' => now(),
            ]);

            // Le numéro d'adhésion sera généré automatiquement par le modèle
            $this->command->info("Membre créé: {$member->full_name} - {$member->membershipNumber} ({$city->name}, {$site->name})");
        }

        $this->command->info('50 membres créés avec succès !');
    }
}
