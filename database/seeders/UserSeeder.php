<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'code' => 'USER_ADMIN01',
                'username' => 'admin',
                'email' => 'admin@smsapi.com',
                'password' => 'password123',
                'phone_number' => '+33123456789',
                'enabled' => true,
            ],
            [
                'code' => 'USER_MANAGER01',
                'username' => 'manager',
                'email' => 'manager@smsapi.com',
                'password' => 'password123',
                'phone_number' => '+33987654321',
                'enabled' => true,
            ],
            [
                'code' => 'USER_OPERATOR01',
                'username' => 'operator',
                'email' => 'operator@smsapi.com',
                'password' => 'password123',
                'phone_number' => '+33456789123',
                'enabled' => false,
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
