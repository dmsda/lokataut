<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Link;
use App\Models\Visit;
use App\Models\VisitorSession;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VisitRecorderService
{
    public function __construct(
        protected TokenService $tokenService
    ) {}

    /**
     * Record a visit event.
     */
    public function record(
        Link $link,
        string $eventType,
        Request $request,
        ?VisitorSession $session = null
    ): Visit {
        $ip = $request->ip() ?? '127.0.0.1';
        $ipHash = $this->tokenService->hashIp($ip);
        $userAgent = (string) $request->userAgent();
        
        $deviceInfo = $this->detectDevice($userAgent);
        $countryCode = $request->header('CF-IPCountry') 
            ?? $request->header('X-Country-Code') 
            ?? null;
        
        if ($countryCode !== null) {
            $countryCode = strtoupper(substr($countryCode, 0, 2));
        }

        $referrer = $request->header('referer');
        if ($referrer !== null) {
            $referrer = substr($referrer, 0, 500);
        }

        $visit = Visit::create([
            'link_id' => $link->id,
            'link_id_snapshot' => $link->id,
            'link_slug_snapshot' => $link->slug,
            'visitor_session_id' => $session?->id,
            'event_type' => $eventType,
            'ip_hash' => $ipHash,
            'device_type' => $deviceInfo['device_type'],
            'browser' => $deviceInfo['browser'],
            'os' => $deviceInfo['os'],
            'country_code' => $countryCode,
            'referrer' => $referrer,
            'created_at' => now(),
        ]);

        if ($eventType === 'impression') {
            $link->increment('total_visits');
        } elseif ($eventType === 'completed') {
            $link->increment('total_completions');
        }

        return $visit;
    }

    /**
     * Simple, fast user agent parser for shared hosting (no heavyweight external dependencies).
     *
     * @return array{device_type: 'desktop'|'mobile'|'tablet'|'bot'|'unknown', browser: ?string, os: ?string}
     */
    public function detectDevice(string $ua): array
    {
        $uaLower = strtolower($ua);

        // 1. Detect bots
        if (preg_match('/(bot|crawl|slurp|spider|mediapartners|curl|wget|python|headlesschrome)/i', $uaLower)) {
            return [
                'device_type' => 'bot',
                'browser' => 'Bot/Crawler',
                'os' => null,
            ];
        }

        // 2. Detect device type
        $deviceType = 'desktop';
        if (preg_match('/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i', $uaLower)) {
            $deviceType = 'tablet';
        } elseif (preg_match('/(iphone|ipod|android.*mobile|windows phone|blackberry|opera mini)/i', $uaLower)) {
            $deviceType = 'mobile';
        }

        // 3. Detect OS
        $os = 'Unknown OS';
        if (str_contains($uaLower, 'windows nt 10.0')) $os = 'Windows 10/11';
        elseif (str_contains($uaLower, 'windows')) $os = 'Windows';
        elseif (str_contains($uaLower, 'android')) $os = 'Android';
        elseif (str_contains($uaLower, 'iphone') || str_contains($uaLower, 'ipad')) $os = 'iOS';
        elseif (str_contains($uaLower, 'mac os x')) $os = 'macOS';
        elseif (str_contains($uaLower, 'linux')) $os = 'Linux';

        // 4. Detect Browser
        $browser = 'Unknown Browser';
        if (str_contains($uaLower, 'edg/')) $browser = 'Edge';
        elseif (str_contains($uaLower, 'chrome/') && !str_contains($uaLower, 'edg/')) $browser = 'Chrome';
        elseif (str_contains($uaLower, 'safari/') && !str_contains($uaLower, 'chrome/')) $browser = 'Safari';
        elseif (str_contains($uaLower, 'firefox/')) $browser = 'Firefox';
        elseif (str_contains($uaLower, 'opera/') || str_contains($uaLower, 'opr/')) $browser = 'Opera';

        return [
            'device_type' => $deviceType,
            'browser' => $browser,
            'os' => $os,
        ];
    }
}

