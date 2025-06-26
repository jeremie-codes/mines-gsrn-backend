<?php

namespace Database\Seeders;

use App\Models\Configuration;
use Illuminate\Database\Seeder;

class ConfigurationSeeder extends Seeder
{
    public function run()
    {
        $configurations = [
            [
                'code' => 'SMS_CONFIG_DEFAULT',
                'active' => true,
                'schedule_date_format' => 'Y-m-d H:i:s',
                'schedule_date_value' => null,
                'sms_from' => 'SMS Gateway',
                'sms_login' => 'default_login',
                'sms_url' => 'https://api.sms-provider.com/send',
                'sms_url_check' => 'https://api.sms-provider.com/status',
            ],
            [
                'code' => 'SMS_CONFIG_BACKUP',
                'active' => false,
                'schedule_date_format' => 'Y-m-d H:i:s',
                'schedule_date_value' => null,
                'sms_from' => 'Backup SMS',
                'sms_login' => 'backup_login',
                'sms_url' => 'https://backup.sms-provider.com/send',
                'sms_url_check' => 'https://backup.sms-provider.com/status',
            ],
        ];

        foreach ($configurations as $config) {
            Configuration::create($config);
        }
    }
}