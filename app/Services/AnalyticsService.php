<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\DailyStat;
use App\Models\Link;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    /**
     * Get summary KPI statistics for the admin dashboard.
     *
     * @return array{
     *   total_links: int,
     *   active_links: int,
     *   total_visits: int,
     *   total_completions: int,
     *   today_visits: int,
     *   today_completions: int,
     *   conversion_rate: float
     * }
     */
    public function getDashboardSummary(): array
    {
        $totalLinks = Link::count();
        $activeLinks = Link::active()->count();
        $totalVisits = (int) Link::sum('total_visits');
        $totalCompletions = (int) Link::sum('total_completions');

        $today = Carbon::today();
        $todayVisits = Visit::where('event_type', 'impression')
            ->where('created_at', '>=', $today)
            ->count();

        $todayCompletions = Visit::where('event_type', 'completed')
            ->where('created_at', '>=', $today)
            ->count();

        $conversionRate = $totalVisits > 0 
            ? round(($totalCompletions / $totalVisits) * 100, 2) 
            : 0.0;

        return [
            'total_links' => $totalLinks,
            'active_links' => $activeLinks,
            'total_visits' => $totalVisits,
            'total_completions' => $totalCompletions,
            'today_visits' => $todayVisits,
            'today_completions' => $todayCompletions,
            'conversion_rate' => $conversionRate,
        ];
    }

    /**
     * Get daily trend chart data for the past N days.
     *
     * @return list<array{date: string, impressions: int, completions: int}>
     */
    public function getDailyTrend(int $days = 7): array
    {
        $startDate = Carbon::today()->subDays($days - 1);
        $result = [];

        // Query daily_stats for historical days, combine with today's live visits
        for ($i = 0; $i < $days; $i++) {
            $currentDate = $startDate->copy()->addDays($i);
            $dateString = $currentDate->format('Y-m-d');

            if ($currentDate->isToday()) {
                $impressions = Visit::where('event_type', 'impression')
                    ->where('created_at', '>=', $currentDate)
                    ->count();

                $completions = Visit::where('event_type', 'completed')
                    ->where('created_at', '>=', $currentDate)
                    ->count();
            } else {
                $stat = DailyStat::where('date', $dateString)
                    ->selectRaw('SUM(impressions) as total_imp, SUM(completions) as total_comp')
                    ->first();

                $impressions = (int) ($stat?->total_imp ?? 0);
                $completions = (int) ($stat?->total_comp ?? 0);
            }

            $result[] = [
                'date' => $dateString,
                'label' => $currentDate->format('d M'),
                'impressions' => $impressions,
                'completions' => $completions,
            ];
        }

        return $result;
    }

    /**
     * Get top links by traffic.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Link>
     */
    public function getTopLinks(int $limit = 5)
    {
        return Link::orderByDesc('total_visits')
            ->take($limit)
            ->get();
    }

    /**
     * Get device breakdown percentages.
     *
     * @return array<string, int>
     */
    public function getDeviceBreakdown(): array
    {
        $breakdown = Visit::select('device_type', DB::raw('count(*) as count'))
            ->groupBy('device_type')
            ->pluck('count', 'device_type')
            ->toArray();

        return array_merge([
            'desktop' => 0,
            'mobile' => 0,
            'tablet' => 0,
            'bot' => 0,
        ], $breakdown);
    }
}

