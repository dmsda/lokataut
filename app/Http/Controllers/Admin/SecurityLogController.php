<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SecurityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SecurityLogController extends Controller
{
    /**
     * Display a listing of security audit logs.
     */
    public function index(Request $request): View
    {
        $eventType = $request->query('event_type');

        $query = SecurityLog::with('admin')->orderByDesc('id');

        if (!empty($eventType)) {
            $query->where('event_type', $eventType);
        }

        $logs = $query->paginate(25)->withQueryString();

        $eventTypes = SecurityLog::select('event_type')
            ->distinct()
            ->pluck('event_type');

        return view('admin.security.logs', compact('logs', 'eventTypes', 'eventType'));
    }
}

