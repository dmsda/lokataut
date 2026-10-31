<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingsRequest;
use App\Models\Setting;
use App\Services\SecurityLogService;
use App\Services\SettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function __construct(
        protected SettingsService $settingsService,
        protected SecurityLogService $securityLogService
    ) {}

    /**
     * Display the settings configuration form.
     */
    public function index(): View
    {
        $settings = Setting::all()->keyBy('key');
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update configuration settings.
     */
    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        foreach ($validated as $key => $value) {
            $this->settingsService->set($key, $value);
        }

        $this->securityLogService->log(
            eventType: 'settings_updated',
            ipAddress: $request->ip() ?? '127.0.0.1',
            userAgent: $request->userAgent(),
            details: ['updated_keys' => array_keys($validated)],
            adminId: auth()->id()
        );

        return redirect()->route('admin.settings.index')
            ->with('success', 'Konfigurasi sistem berhasil disimpan.');
    }
}

