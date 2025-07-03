<?php

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

            // ✅ Créer le dossier des QR codes s'il n'existe pas
            $qrDir = public_path('storage/qrcodes');
            if (!file_exists($qrDir)) {
                mkdir($qrDir, 0755, true);
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
                    $qrcodePath = 'storage/qrcodes/' . $membershipNumber . '.png';
                    \QrCode::format('png')->size(300)->generate(
                        $membershipNumber,
                        // public_path('storage/' . $qrcodePath)
                        storage_path('app/public/' . $qrcodePath)
                    );
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

