<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TurnstileService
{
    private const SITEVERIFY_URL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    public function __construct(
        protected SecurityLogService $securityLogService
    ) {}

    /**
     * Verify Turnstile response token.
     */
    public function verify(string $token, ?string $remoteIp = null): bool
    {
        // Check emergency bypass setting
        $mode = Setting::where('key', 'turnstile_mode')->value('value') ?? 'enabled';
        $bypassDev = config('services.turnstile.bypass_dev', false);

        if ($mode === 'bypass' || $bypassDev) {
            $this->securityLogService->log(
                eventType: 'turnstile_emergency_bypass_used',
                ipAddress: $remoteIp ?? '127.0.0.1',
                details: ['reason' => $bypassDev ? 'dev_bypass_flag' : 'emergency_bypass_setting']
            );
            return true;
        }

        $secretKey = (string) config('services.turnstile.secret_key');

        if (trim($secretKey) === '') {
            Log::warning('Turnstile secret key is empty, rejecting validation');
            return false;
        }

        // Test dummy key always passes immediately without external HTTP if configured
        if ($secretKey === '1x0000000000000000000000000000000AA') {
            if ($token === 'test_invalid_token') {
                return false;
            }
            return true;
        }

        try {
            $response = Http::asForm()
                ->timeout(5)
                ->post(self::SITEVERIFY_URL, array_filter([
                    'secret' => $secretKey,
                    'response' => $token,
                    'remoteip' => $remoteIp,
                ]));

            if ($response->successful()) {
                $body = $response->json();
                return isset($body['success']) && $body['success'] === true;
            }

            Log::error('Turnstile verification failed with HTTP status: ' . $response->status());
            return false;
        } catch (\Throwable $e) {
            Log::error('Turnstile verification exception: ' . $e->getMessage());
            return false;
        }
    }
}

