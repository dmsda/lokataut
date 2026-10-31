<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\SafelinkProceedRequest;
use App\Http\Requests\Public\SafelinkVerifyRequest;
use App\Models\AdSlot;
use App\Models\Link;
use App\Models\Setting;
use App\Services\SafelinkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SafelinkController extends Controller
{
    public function __construct(
        protected SafelinkService $safelinkService
    ) {}

    /**
     * Render the public safelink landing page.
     * Enforces that the destination URL is NEVER rendered into the client HTML or DOM.
     */
    public function show(string $slug, Request $request): View|RedirectResponse
    {
        $link = Link::withTrashed()->where('slug', $slug)->first();

        if ($link === null) {
            abort(404, 'Tautan tidak ditemukan.');
        }

        if ($link->trashed() || $link->status === 'inactive' || $link->isExpired()) {
            return view('public.expired', [
                'link' => $link,
                'isExpired' => $link->isExpired(),
                'isInactive' => $link->status === 'inactive' || $link->trashed(),
            ]);
        }

        // Initialize session and record impression event
        $sessionData = $this->safelinkService->createSession($link, $request);

        // Fetch active ad slots grouped by position
        $adSlots = AdSlot::active()->orderBy('sort_order', 'asc')->get()->groupBy('position');

        $turnstileSiteKey = (string) config('services.turnstile.site_key', '1x00000000000000000000AA');
        $turnstileMode = Setting::where('key', 'turnstile_mode')->value('value') ?? 'enabled';

        return view('public.safelink', [
            'link' => $link,
            'rawToken' => $sessionData['raw_token'],
            'countdownSeconds' => $link->countdown_seconds,
            'turnstileSiteKey' => $turnstileSiteKey,
            'turnstileMode' => $turnstileMode,
            'adSlots' => $adSlots,
        ]);
    }

    /**
     * Verify Turnstile challenge via AJAX.
     */
    public function verify(SafelinkVerifyRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $success = $this->safelinkService->verifyTurnstile(
            rawToken: $validated['token'],
            turnstileResponse: $validated['turnstile_token'],
            request: $request
        );

        if (!$success) {
            return response()->json([
                'success' => false,
                'message' => 'Verifikasi keamanan tidak berhasil. Silakan coba lagi.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Verifikasi bot berhasil. Hitung mundur dimulai.',
        ]);
    }

    /**
     * Redeem token after countdown completion and retrieve destination URL.
     */
    public function proceed(SafelinkProceedRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $destinationUrl = $this->safelinkService->proceed(
                rawToken: $validated['token'],
                request: $request
            );

            return response()->json([
                'success' => true,
                'redirect_url' => $destinationUrl,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Direct HTTP 302 redemption for standard POST form submission.
     */
    public function directProceed(SafelinkProceedRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $destinationUrl = $this->safelinkService->proceed(
                rawToken: $validated['token'],
                request: $request
            );

            // HTTP 302 redirect directly to destination
            return redirect()->away($destinationUrl, 302);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}

