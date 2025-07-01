<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Site;

class SiteSeeder extends Seeder
{
    public function run()
    {
        $sites = [
            // Sites principaux par province
            ['name' => 'Site Principal Kinshasa', 'code' => 'KIN', 'location' => 'Kinshasa, Gombe'],
            ['name' => 'Site Lubumbashi', 'code' => 'LUB', 'location' => 'Lubumbashi, Kenya'],
            ['name' => 'Site Goma', 'code' => 'GOM', 'location' => 'Goma Centre'],
            ['name' => 'Site Bukavu', 'code' => 'BUK', 'location' => 'Bukavu, Ibanda'],
            ['name' => 'Site Matadi', 'code' => 'MAT', 'location' => 'Matadi Port'],
            ['name' => 'Site Kananga', 'code' => 'KAN', 'location' => 'Kananga Centre'],
            ['name' => 'Site Mbuji-Mayi', 'code' => 'MBJ', 'location' => 'Mbuji-Mayi, Bipemba'],
            ['name' => 'Site Kisangani', 'code' => 'KIS', 'location' => 'Kisangani, Makiso'],
            ['name' => 'Site Mbandaka', 'code' => 'MBD', 'location' => 'Mbandaka Centre'],
            ['name' => 'Site Bandundu', 'code' => 'BAN', 'location' => 'Bandundu Ville'],
            ['name' => 'Site Kindu', 'code' => 'KDU', 'location' => 'Kindu Centre'],
            ['name' => 'Site Kikwit', 'code' => 'KIK', 'location' => 'Kikwit Centre'],
            ['name' => 'Site Kolwezi', 'code' => 'KOL', 'location' => 'Kolwezi Centre'],
            ['name' => 'Site Likasi', 'code' => 'LIK', 'location' => 'Likasi Centre'],
            ['name' => 'Site Beni', 'code' => 'BEN', 'location' => 'Beni Centre'],
            ['name' => 'Site Butembo', 'code' => 'BUT', 'location' => 'Butembo Centre'],
            ['name' => 'Site Uvira', 'code' => 'UVI', 'location' => 'Uvira Centre'],
            ['name' => 'Site Tshikapa', 'code' => 'TSH', 'location' => 'Tshikapa Centre'],
            ['name' => 'Site Boma', 'code' => 'BOM', 'location' => 'Boma Centre'],
            ['name' => 'Site Isiro', 'code' => 'ISI', 'location' => 'Isiro Centre'],
        ];

        foreach ($sites as $site) {
            Site::create($site);
        }

        $this->command->info(count($sites) . " sites créés avec succès !");
    }
}