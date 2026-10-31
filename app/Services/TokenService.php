<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;

class TokenService
{
    /**
     * Generate a cryptographically secure random 32-byte raw token.
     */
    public function generateRawToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Compute SHA-256 hash of raw session token for database persistence.
     */
    public function hashToken(string $rawToken): string
    {
        return hash('sha256', $rawToken);
    }

    /**
     * Pseudonymize visitor IP address using keyed HMAC-SHA256 with isolated ANALYTICS_SALT.
     * Complies strictly with NIST SP 800-57 (no fallback to APP_KEY).
     */
    public function hashIp(string $ip): string
    {
        $salt = (string) config('services.analytics.salt');

        if (trim($salt) === '') {
            throw new RuntimeException('ANALYTICS_SALT is not configured. Fallback to APP_KEY is strictly prohibited under NIST SP 800-57.');
        }

        return hash_hmac('sha256', $ip, $salt);
    }

    /**
     * Compute SHA-256 hash of User-Agent string for session binding.
     */
    public function hashUserAgent(?string $userAgent): ?string
    {
        if ($userAgent === null || trim($userAgent) === '') {
            return null;
        }

        return hash('sha256', $userAgent);
    }
}

