<?php

namespace Database\Seeders;

use App\Models\Merchant;
use Illuminate\Database\Seeder;

class MerchantSeeder extends Seeder
{
    public function run()
    {
        $merchants = [
            [
                'code' => 'MERCHANT_001',
                'name' => 'TechCorp Solutions',
                'active' => true,
                'own_config' => true,
                'sms_from' => 'TechCorp',
                'sms_login' => 'tech_login',
            ],
            [
                'code' => 'MERCHANT_002',
                'name' => 'Digital Services Ltd',
                'active' => true,
                'own_config' => false,
                'sms_from' => 'DigitalSvc',
                'sms_login' => 'digital_login',
            ],
            [
                'code' => 'MERCHANT_003',
                'name' => 'Startup Innovation',
                'active' => false,
                'own_config' => false,
                'sms_from' => null,
                'sms_login' => null,
            ],
        ];

        foreach ($merchants as $merchant) {
            Merchant::create($merchant);
        }
    }
}