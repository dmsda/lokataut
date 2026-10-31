<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'site_name',
                'value' => 'LokaTaut',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Nama publik platform safelink',
            ],
            [
                'key' => 'default_countdown_seconds',
                'value' => '10',
                'type' => 'integer',
                'group' => 'safelink',
                'description' => 'Durasi default hitung mundur safelink (detik)',
            ],
            [
                'key' => 'turnstile_mode',
                'value' => 'enabled',
                'type' => 'string',
                'group' => 'security',
                'description' => 'Mode proteksi Turnstile: enabled, test, bypass',
            ],
            [
                'key' => 'session_ttl_minutes',
                'value' => '30',
                'type' => 'integer',
                'group' => 'safelink',
                'description' => 'Masa berlaku token sesi safelink (menit)',
            ],
            [
                'key' => 'adsterra_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'monetization',
                'description' => 'Status aktif tayangan ad network',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}

