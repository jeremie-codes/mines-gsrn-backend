<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            // Seeders de base (rôles, permissions, fonctions)
            RolePermissionSeeder::class,
            FonctionSeeder::class,
            
            // Seeders géographiques (pays, villes, communes)
            LocationSeeder::class,
            
            // Seeders des entités principales
            SiteSeeder::class,
            PoolSeeder::class,
            
            // Seeders des membres et utilisateurs
            MemberSeeder::class,
            UserSeeder::class,
        ]);
    }
}