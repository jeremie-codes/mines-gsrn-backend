<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\City;
use App\Models\Fonction;
use App\Models\Member;
use App\Models\Pool;
use App\Models\Site;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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

            $site2 = Site::create(
            [
                'code' => 'TSH',
                'name' => 'Tshuenge',
                'location' => 'Kinshasa',
                'city_id' => $city->id,
                'is_active' => true,
            ]);

            $poolsOther = [
                'Lokali 1' => ['Ma Marie', 'Théo Makawu'],
                'Masina' => ['Dieu Nzila', 'Mr Souley'],
            ];

            $sitesOther = [
                ['code' => 'RVA', 'name' => 'RVA'],
                ['code' => 'BNO', 'name' => 'Bono'],
                ['code' => 'NSB', 'name' => 'Nsele Bambou'],
                ['code' => 'IDP', 'name' => 'indépendant'],
            ];

            foreach ($sitesOther as $siteoth) {
                Site::create([
                    'code' => $siteoth['code'],
                    'name' => $siteoth['name'],
                    'location' => 'Kinshasa',
                    'city_id' => $city->id,
                    'is_active' => true,
                ]);
            }

            $categories = [
                ['amount' => '500', 'name' => 'A', 'currency' => 'CDF'],
                ['amount' => '500', 'name' => 'B', 'currency' => 'CDF'],
                ['amount' => '500', 'name' => 'C', 'currency' => 'CDF'],
                ['amount' => '500', 'name' => 'D', 'currency' => 'CDF'],
            ];

            foreach ($categories as $category) {
                Category::create([
                    'name' => $category['name'],
                    'amount' => $category['amount'],
                    'currency' => $category['currency'],
                ]);
            }


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
                        'category_id' => 1,
                        'pool_id' => $pool->id,
                        'fonction_id' => $chefDePoolFonction->id,
                        'is_active' => true,
                        'date_adhesion' => now(),
                    ]);

                }
            }

            foreach ($poolsOther as $poolNameOth => $chefsOth) {
                $pool = Pool::create([
                    'site_id' => $site->id,
                    'name' => $poolNameOth,
                    'description' => 'Pool de ' . $poolNameOth,
                    'is_active' => true,
                ]);

                foreach ($chefsOth as $chefNameOth) {
                    if ($chefNameOth === '?') continue;

                    $namesOth = explode(' ', $chefNameOth);
                    $firstnameOth = array_shift($namesOth);
                    $lastnameOth = implode(' ', $namesOth) ?: null;

                    $membershipNumberOth = $this->generateMembershipNumber($site->id, $city->id);

                    $member = Member::create([
                        'firstname' => $firstnameOth,
                        'lastname' => $lastnameOth,
                        'membershipNumber' => $membershipNumberOth,
                        'site_id' => $site2->id,
                        'city_id' => $city->id,
                        'category_id' => 1,
                        'pool_id' => $pool->id,
                        'fonction_id' => $chefDePoolFonction->id,
                        'is_active' => true,
                        'date_adhesion' => now(),
                    ]);

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

