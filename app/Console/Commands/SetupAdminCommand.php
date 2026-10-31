<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Admin;
use App\Services\SecurityLogService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SetupAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lokataut:setup-admin 
                            {--email= : The administrator email address}
                            {--name= : The administrator display name}
                            {--password= : The administrator password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create or reset the single administrator account securely';

    public function handle(SecurityLogService $securityLogService): int
    {
        $email = $this->option('email') ?: (string) env('ADMIN_EMAIL', 'dimas@lokataut.com');
        $name = $this->option('name') ?: 'Dimas Aditya';
        $password = $this->option('password');

        if (empty($password)) {
            $password = $this->secret('Enter admin password (leave empty to generate a secure random password)') ?: null;
        }

        $generated = false;
        if (empty($password)) {
            $password = Str::password(16);
            $generated = true;
        }

        $admin = Admin::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
            ]
        );

        $securityLogService->log(
            eventType: 'admin_setup_cli',
            ipAddress: '127.0.0.1',
            userAgent: 'CLI / Artisan',
            details: ['email' => $admin->email, 'generated' => $generated],
            adminId: $admin->id
        );

        $this->info("Admin account successfully configured for: {$admin->email}");
        if ($generated) {
            $this->warn("Generated Password: {$password}");
            $this->comment('Please save this password securely and change it upon first login.');
        } else {
            $this->info('Password has been set and hashed using Bcrypt.');
        }

        return Command::SUCCESS;
    }
}

