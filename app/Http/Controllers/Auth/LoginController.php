<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AdminLoginRequest;
use App\Services\SecurityLogService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(
        protected SecurityLogService $securityLogService
    ) {}

    /**
     * Show the admin login form.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle admin authentication attempt.
     */
    public function login(AdminLoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        /** @var \App\Models\Admin $admin */
        $admin = Auth::user();
        $admin->update([
            'last_login_at' => Carbon::now(),
            'last_login_ip' => $request->ip(),
        ]);

        $this->securityLogService->log(
            eventType: 'admin_login_success',
            ipAddress: $request->ip() ?? '127.0.0.1',
            userAgent: $request->userAgent(),
            details: ['email' => $admin->email],
            adminId: $admin->id
        );

        return redirect()->intended(route('admin.dashboard'));
    }

    /**
     * Log the admin out and invalidate session.
     */
    public function logout(Request $request): RedirectResponse
    {
        $admin = Auth::user();

        if ($admin !== null) {
            $this->securityLogService->log(
                eventType: 'admin_logout',
                ipAddress: $request->ip() ?? '127.0.0.1',
                userAgent: $request->userAgent(),
                details: ['email' => $admin->email],
                adminId: $admin->id
            );
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}

