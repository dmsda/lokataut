<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdatePasswordRequest;
use App\Services\SecurityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        protected SecurityLogService $securityLogService
    ) {}

    /**
     * Show admin profile and password change form.
     */
    public function edit(): View
    {
        $admin = Auth::user();
        return view('admin.profile', compact('admin'));
    }

    /**
     * Update admin password and rotate session.
     */
    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        /** @var \App\Models\Admin $admin */
        $admin = Auth::user();

        $admin->update([
            'password' => Hash::make($request->validated('new_password')),
        ]);

        // Rotate session to prevent session fixation / hijacking
        $request->session()->regenerate();

        $this->securityLogService->log(
            eventType: 'password_change_success',
            ipAddress: $request->ip() ?? '127.0.0.1',
            userAgent: $request->userAgent(),
            details: ['email' => $admin->email],
            adminId: $admin->id
        );

        return redirect()->route('admin.profile')
            ->with('success', 'Kata sandi administrator berhasil diperbarui. Sesi telah dirotasi demi keamanan.');
    }
}

