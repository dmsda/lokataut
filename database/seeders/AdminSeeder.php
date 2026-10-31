<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'dimas@lokataut.com');

        $existingAdmin = Admin::where('email', $email)->first();

        // If admin already exists, do not overwrite their password to preserve changes made via profile
        if ($existingAdmin !== null) {
            return;
        }

        $initialPassword = env('ADMIN_INITIAL_PASSWORD');

        if (empty($initialPassword)) {
            // Default initial password for local setup if not explicitly configured
            $initialPassword = 'LokaTaut#Admin2026!';
        }

        Admin::create([
            'email' => $email,
            'name' => 'Dimas Aditya',
            'password' => Hash::make($initialPassword),
        ]);
    }
}
