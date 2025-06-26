<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            // MerchantSeeder::class,
            UserSeeder::class,
            // ConfigurationSeeder::class,
            // AuthSeeder::class, // Uncomment after creating AuthSeeder
            // MessageSeeder::class, // Uncomment after creating MessageSeeder
        ]);
    }
}
