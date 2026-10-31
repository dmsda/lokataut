<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Services\AnalyticsService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the main admin analytics dashboard.
     */
    public function index(AnalyticsService $analyticsService): View
    {
        $summary = $analyticsService->getDashboardSummary();
        $dailyTrend = $analyticsService->getDailyTrend(7);
        $topLinks = $analyticsService->getTopLinks(5);
        $deviceBreakdown = $analyticsService->getDeviceBreakdown();

        $articleStats = [
            'total' => Article::count(),
            'published' => Article::published()->count(),
            'draft' => Article::draft()->count(),
        ];
        $latestArticles = Article::with('category')->latest('id')->take(5)->get();

        return view('admin.dashboard', compact(
            'summary',
            'dailyTrend',
            'topLinks',
            'deviceBreakdown',
            'articleStats',
            'latestArticles'
        ));
    }
}
