<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Link;
use App\Models\Setting;
use App\Models\VisitorSession;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SafelinkService
{
    public function __construct(
        protected TokenService $tokenService,
        protected TurnstileService $turnstileService,
        protected VisitRecorderService $visitRecorderService,
        protected SecurityLogService $securityLogService
    ) {}

    /**
     * Initialize a new visitor session for a safelink view.
     *
     * @return array{session: VisitorSession, raw_token: string}
     */
    public function createSession(Link $link, Request $request): array
    {
        $rawToken = $this->tokenService->generateRawToken();
        $tokenHash = $this->tokenService->hashToken($rawToken);
        $userAgentHash = $this->tokenService->hashUserAgent($request->userAgent());

        $ttlMinutes = (int) (Setting::where('key', 'session_ttl_minutes')->value('value') ?? 30);
        $expiresAt = Carbon::now()->addMinutes($ttlMinutes);

        $session = VisitorSession::create([
            'link_id' => $link->id,
            'token_hash' => $tokenHash,
            'user_agent_hash' => $userAgentHash,
            'turnstile_verified' => false,
            'turnstile_verified_at' => null,
            'countdown_started_at' => null,
            'countdown_seconds' => $link->countdown_seconds,
            'completed' => false,
            'completed_at' => null,
            'expires_at' => $expiresAt,
        ]);

        // Record initial impression visit event
        $this->visitRecorderService->record($link, 'impression', $request, $session);

        return [
            'session' => $session,
            'raw_token' => $rawToken,
        ];
    }

    /**
     * Verify Turnstile token and begin the server-side countdown.
     */
    public function verifyTurnstile(string $rawToken, string $turnstileResponse, Request $request): bool
    {
        $tokenHash = $this->tokenService->hashToken($rawToken);
        $session = VisitorSession::where('token_hash', $tokenHash)->first();

        if ($session === null || $session->completed || $session->isExpired()) {
            return false;
        }

        $ip = $request->ip() ?? '127.0.0.1';
        $isValid = $this->turnstileService->verify($turnstileResponse, $ip);

        if (!$isValid) {
            $this->securityLogService->log(
                eventType: 'turnstile_verification_failed',
                ipAddress: $ip,
                userAgent: $request->userAgent(),
                details: ['link_id' => $session->link_id]
            );
            return false;
        }

        $session->update([
            'turnstile_verified' => true,
            'turnstile_verified_at' => Carbon::now(),
            'countdown_started_at' => Carbon::now(),
        ]);

        $this->visitRecorderService->record($session->link, 'verified', $request, $session);

        return true;
    }

    /**
     * Validate countdown completion and execute atomic token redemption.
     * Returns the decrypted destination URL.
     */
    public function proceed(string $rawToken, Request $request): string
    {
        $tokenHash = $this->tokenService->hashToken($rawToken);
        $session = VisitorSession::where('token_hash', $tokenHash)->first();
        $ip = $request->ip() ?? '127.0.0.1';

        if ($session === null) {
            throw new RuntimeException('Sesi tidak valid atau telah berakhir.');
        }

        if ($session->completed) {
            $this->securityLogService->log(
                eventType: 'token_replay_attempt',
                ipAddress: $ip,
                userAgent: $request->userAgent(),
                details: ['link_id' => $session->link_id, 'token_hash' => $tokenHash]
            );
            throw new RuntimeException('Token ini telah digunakan (Replay attack prevented).');
        }

        if ($session->isExpired()) {
            throw new RuntimeException('Sesi safelink telah kedaluwarsa. Silakan muat ulang halaman.');
        }

        if (!$session->turnstile_verified || $session->countdown_started_at === null) {
            $this->securityLogService->log(
                eventType: 'unverified_turnstile_proceed_attempt',
                ipAddress: $ip,
                userAgent: $request->userAgent(),
                details: ['link_id' => $session->link_id]
            );
            throw new RuntimeException('Verifikasi keamanan belum diselesaikan.');
        }

        // Server-side countdown validation (robust epoch timestamp diff)
        $elapsed = Carbon::now()->getTimestamp() - $session->countdown_started_at->getTimestamp();
        if ($elapsed < $session->countdown_seconds) {
            $this->securityLogService->log(
                eventType: 'countdown_bypass_attempt',
                ipAddress: $ip,
                userAgent: $request->userAgent(),
                details: [
                    'link_id' => $session->link_id,
                    'elapsed_seconds' => $elapsed,
                    'required_seconds' => $session->countdown_seconds,
                ]
            );
            throw new RuntimeException('Hitung mundur belum selesai di server. Percobaan bypass terdeteksi.');
        }

        // Atomic transition in database
        $affected = DB::update(
            'UPDATE visitor_sessions SET completed = 1, completed_at = ? WHERE token_hash = ? AND completed = 0',
            [Carbon::now(), $tokenHash]
        );

        if ($affected === 0) {
            $this->securityLogService->log(
                eventType: 'token_race_condition_detected',
                ipAddress: $ip,
                userAgent: $request->userAgent(),
                details: ['link_id' => $session->link_id]
            );
            throw new RuntimeException('Sesi sedang diproses atau sudah digunakan.');
        }

        // Record completion visit
        $this->visitRecorderService->record($session->link, 'completed', $request, $session);

        // Return the decrypted destination URL
        return (string) $session->link->destination_url;
    }
}
