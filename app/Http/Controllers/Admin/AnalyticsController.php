<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyStat;
use App\Models\Visit;
use App\Services\AnalyticsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    /**
     * Display the analytics report page.
     */
    public function index(Request $request, AnalyticsService $analyticsService): View
    {
        $days = (int) $request->query('days', 30);
        if ($days !== 7 && $days !== 30 && $days !== 90) {
            $days = 30;
        }

        $trend = $analyticsService->getDailyTrend($days);
        $deviceBreakdown = $analyticsService->getDeviceBreakdown();
        $recentVisits = Visit::with('link')
            ->orderByDesc('id')
            ->take(20)
            ->get();

        $topDailyStats = DailyStat::selectRaw('link_slug, SUM(impressions) as total_imp, SUM(completions) as total_comp')
            ->groupBy('link_slug')
            ->orderByDesc('total_imp')
            ->take(10)
            ->get();

        return view('admin.analytics.index', compact('trend', 'deviceBreakdown', 'recentVisits', 'topDailyStats', 'days'));
    }
}

