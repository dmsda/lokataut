<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\SecurityLog;

class SecurityLogService
{
    /**
     * Record a security audit log event.
     *
     * @param array<string, mixed> $details
     */
    public function log(
        string $eventType,
        string $ipAddress,
        ?string $userAgent = null,
        array $details = [],
        ?int $adminId = null
    ): SecurityLog {
        return SecurityLog::create([
            'event_type' => $eventType,
            'ip_address' => substr($ipAddress, 0, 45),
            'user_agent' => $userAgent !== null ? substr($userAgent, 0, 500) : null,
            'details' => $details,
            'admin_id' => $adminId,
            'created_at' => now(),
        ]);
    }
}

