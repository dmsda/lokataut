<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateAdSlotRequest;
use App\Models\AdSlot;
use App\Services\SecurityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdSlotController extends Controller
{
    public function __construct(
        protected SecurityLogService $securityLogService
    ) {}

    /**
     * Display a listing of ad slots.
     */
    public function index(): View
    {
        $slots = AdSlot::orderBy('sort_order', 'asc')->get();
        return view('admin.ads.index', compact('slots'));
    }

    /**
     * Show form for editing an ad slot.
     */
    public function edit(AdSlot $ad): View
    {
        return view('admin.ads.edit', ['slot' => $ad]);
    }

    /**
     * Update the specified ad slot.
     */
    public function update(UpdateAdSlotRequest $request, AdSlot $ad): RedirectResponse
    {
        $validated = $request->validated();

        $oldCode = $ad->ad_code;
        $ad->update($validated);

        if ($oldCode !== $ad->ad_code) {
            $this->securityLogService->log(
                eventType: 'ad_code_modified',
                ipAddress: $request->ip() ?? '127.0.0.1',
                userAgent: $request->userAgent(),
                details: [
                    'slot_id' => $ad->id,
                    'slot_position' => $ad->position,
                ],
                adminId: auth()->id()
            );
        }

        return redirect()->route('admin.ads.index')
            ->with('success', "Slot iklan '{$ad->name}' berhasil diperbarui.");
    }
}

