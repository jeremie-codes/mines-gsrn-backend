<?php

// namespace Database\Seeders;

// use Illuminate\Database\Seeder;
// use App\Models\Member;
// use App\Models\Site;
// use App\Models\Pool;
// use App\Models\Fonction;
// use App\Models\City;
// use App\Models\Township;
// use Faker\Factory as Faker;

// class MemberSeeder extends Seeder
// {
//     public function run()
//     {
//         $faker = Faker::create('fr_FR');

//         // Récupérer les données nécessaires
//         $sites = Site::all();
//         $pools = Pool::all();
//         $fonctions = Fonction::all();
//         $cities = City::all();
//         $townships = Township::all();

//         if ($sites->isEmpty()) {
//             $this->command->error('Aucun site trouvé. Veuillez d\'abord exécuter le SiteSeeder.');
//             return;
//         }

//         $this->command->info('Création de membres avec données géographiques...');

//         // Créer 5 membres avec des données réalistes
//         for ($i = 1; $i <= 20; $i++) {
//             // Sélectionner aléatoirement une ville et une commune
//             $city = $cities->random();
//             $cityTownships = $townships->where('city_id', $city->id);
//             $township = $cityTownships->isNotEmpty() ? $cityTownships->random() : null;

//             // Sélectionner un site aléatoire
//             $site = $sites->random();

//             // Sélectionner un pool aléatoire (optionnel)
//             $pool = $pools->isNotEmpty() && $faker->boolean(70) ? $pools->random() : null;

//             // Sélectionner une fonction aléatoire (optionnel)
//             $fonction = $fonctions->isNotEmpty() && $faker->boolean(80) ? $fonctions->random() : null;

//             // Générer des noms réalistes congolais
//             $firstNames = [
//                 'Jean', 'Marie', 'Pierre', 'Paul', 'Joseph', 'Emmanuel', 'Grace', 'Patience',
//                 'Espoir', 'Joie', 'Paix', 'Amour', 'Fidèle', 'Béni', 'Gloire', 'Victoire',
//                 'Merci', 'Chance', 'Bonheur', 'Lumière', 'Sagesse', 'Force', 'Courage',
//                 'Mukendi', 'Kabongo', 'Tshimanga', 'Mbuyi', 'Ngoy', 'Kalala', 'Ilunga',
//                 'Kasongo', 'Mwamba', 'Katanga', 'Luboya', 'Mujinga', 'Kapinga', 'Mulamba'
//             ];

//             $lastNames = [
//                 'Kabila', 'Tshisekedi', 'Katumbi', 'Bemba', 'Kamerhe', 'Muzito', 'Ruberwa',
//                 'Mende', 'Lumumba', 'Kasavubu', 'Mobutu', 'Mulele', 'Gizenga', 'Kamitatu',
//                 'Mukendi', 'Kabongo', 'Tshimanga', 'Mbuyi', 'Ngoy', 'Kalala', 'Ilunga',
//                 'Kasongo', 'Mwamba', 'Katanga', 'Luboya', 'Mujinga', 'Kapinga', 'Mulamba',
//                 'Ndala', 'Mbala', 'Nkulu', 'Mpiana', 'Wemba', 'Koffi', 'Fally', 'Werrason'
//             ];

//             $middleNames = [
//                 'wa', 'bin', 'ben', 'de', 'du', 'le', 'la', 'van', 'von', 'Mac', 'Mc',
//                 'Mwana', 'Muana', 'Bana', 'Tatu', 'Nkashama', 'Ngalula', 'Mpiana'
//             ];

//             $genders = ['M', 'F'];

//             $firstName = $faker->randomElement($firstNames);
//             $lastName = $faker->randomElement($lastNames);
//             $middleName = $faker->boolean(60) ? $faker->randomElement($middleNames) : null;

//             // Générer un numéro de téléphone congolais
//             $phoneFormats = [
//                 '+243 9' . $faker->numerify('## ### ###'),
//                 '+243 8' . $faker->numerify('## ### ###'),
//                 '+243 99' . $faker->numerify('# ### ###'),
//                 '+243 97' . $faker->numerify('# ### ###'),
//                 '+243 81' . $faker->numerify('# ### ###'),
//                 '+243 82' . $faker->numerify('# ### ###'),
//                 '+243 83' . $faker->numerify('# ### ###'),
//                 '+243 84' . $faker->numerify('# ### ###'),
//                 '+243 85' . $faker->numerify('# ### ###'),
//                 '+243 89' . $faker->numerify('# ### ###'),
//             ];

//             $member = Member::create([
//                 'firstname' => $firstName,
//                 'lastname' => $lastName,
//                 'middlename' => $middleName,
//                 'phone' => $faker->randomElement($phoneFormats),
//                 'gender' => $faker->randomElement($genders),
//                 'site_id' => $site->id,
//                 'city_id' => $city->id,
//                 'township_id' => $township ? $township->id : null,
//                 'pool_id' => $pool ? $pool->id : null,
//                 'libelle_pool' => !$pool && $faker->boolean(30) ? $faker->words(2, true) : null,
//                 'fonction_id' => $fonction ? $fonction->id : null,
//                 'is_active' => $faker->boolean(90), // 90% de chance d'être actif
//                 'created_at' => $faker->dateTimeBetween('-2 years', 'now'),
//                 'updated_at' => now(),
//             ]);

//             // Le numéro d'adhésion sera généré automatiquement par le modèle
//             $this->command->info("Membre créé: {$member->full_name} - {$member->membershipNumber} ({$city->name}, {$site->name})");
//         }

//         $this->command->info('50 membres créés avec succès !');
//     }
// }


namespace Database\Seeders;

use App\Models\City;
use App\Models\Fonction;
use App\Models\Member;
use App\Models\Pool;
use App\Models\Site;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MemberSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // Récupérer la ville de Kinshasa
            $city = City::where('name', 'Kinshasa')->first();
            if (!$city) {
                $this->command->error("La ville de Kinshasa n'est pas trouvée. Veuillez exécuter RDCLocationSeeder d'abord.");
                return;
            }

            // Créer le site DAIPIN
            $site = Site::firstOrCreate(
                ['code' => 'DPN'],
                [
                    'name' => 'DAIPIN',
                    'location' => 'Kinshasa',
                    'city_id' => $city->id,
                    'is_active' => true,
                ]
            );

            // Récupérer la fonction chef de pool
            $chefDePoolFonction = Fonction::where('name', 'Chef de Pool')->first();
            if (!$chefDePoolFonction) {
                $this->command->error("Fonction 'Chef de Pool' non trouvée. Veuillez exécuter le FonctionSeeder d'abord.");
                return;
            }

            // Définir les pools avec les chefs
            $pools = [
                'Bambala' => ['Mr Junior', 'Mr Justin'],
                'Secteur Agricole' => ['?'],
                'Makaku Mbwa' => ['Maman Zizi'],
                'Poto Poto' => ['Mr Luc'],
                'Ebiko I' => ['Mr Eli'],
                'Ebiko II' => ['Mr Ardent'],
                'Bukanga Lonzo I' => ['Mr Mbuta'],
                'Bukanga Lonzo II' => ['?'],
                'Parc' => ['Maman Mado'],
                'Likana I' => ['Mr Crispin'],
                'Likana II' => ['Mr Andunga', '?'],
                'Vuku Vuku' => ['Mr Nool'],
                'Pisciculture I' => ['Mme Mireille'],
                'Pisciculture II' => ['?'],
                'Couvoirs' => ['Mr Olivier Ngesimi'],
                'V.A.B' => ['Mr Alpha'],
                'Antenne' => ['Mr Coralie'],
                'Bambou' => ['Pélagie'],
                'Mukuna' => ['Gorethie'],
                'Jules' => ['Romain'],
            ];

            foreach ($pools as $poolName => $chefs) {
                $pool = Pool::create([
                    'site_id' => $site->id,
                    'name' => $poolName,
                    'description' => 'Pool de ' . $poolName,
                    'is_active' => true,
                ]);

                foreach ($chefs as $chefName) {
                    if ($chefName === '?') continue;

                    $names = explode(' ', $chefName);
                    $firstname = array_shift($names);
                    $lastname = implode(' ', $names) ?: null;

                    $membershipNumber = $this->generateMembershipNumber($site->id, $city->id);

                    $member = Member::create([
                        'firstname' => $firstname,
                        'lastname' => $lastname,
                        'membershipNumber' => $membershipNumber,
                        'site_id' => $site->id,
                        'city_id' => $city->id,
                        'pool_id' => $pool->id,
                        'fonction_id' => $chefDePoolFonction->id,
                        'is_active' => true,
                        'date_adhesion' => now(),
                    ]);

                    // Générer un QR code
                    $qrcodePath = 'qrcodes/' . $membershipNumber . '.png';
                    \QrCode::format('png')->size(300)->generate($membershipNumber, public_path('storage/' . $qrcodePath));
                    $member->update(['qrcode_url' => $qrcodePath]);
                }
            }

            $this->command->info('Site DAIPIN, pools et chefs de pool créés avec succès.');
        });
    }

    private function generateMembershipNumber($siteId, $cityId = null)
    {
        return DB::transaction(function () use ($siteId, $cityId) {
            $site = Site::lockForUpdate()->find($siteId);
            $provinceCode = 'XXX';
            if ($cityId) {
                $city = City::find($cityId);
                if ($city && $city->province_code) {
                    $provinceCode = strtoupper($city->province_code);
                }
            }
            return $site->generateMembershipNumber($provinceCode);
        });
    }
}

