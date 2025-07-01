<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;

class CountrySeeder extends Seeder
{
    public function run()
    {
        $countries = [
            ['code' => 'CD', 'name' => 'République Démocratique du Congo'],
            ['code' => 'CG', 'name' => 'République du Congo'],
            ['code' => 'AO', 'name' => 'Angola'],
            ['code' => 'ZM', 'name' => 'Zambie'],
            ['code' => 'TZ', 'name' => 'Tanzanie'],
            ['code' => 'UG', 'name' => 'Ouganda'],
            ['code' => 'RW', 'name' => 'Rwanda'],
            ['code' => 'BI', 'name' => 'Burundi'],
            ['code' => 'CF', 'name' => 'République Centrafricaine'],
            ['code' => 'SS', 'name' => 'Soudan du Sud'],
        ];

        foreach ($countries as $country) {
            Country::create($country);
        }
    }
}