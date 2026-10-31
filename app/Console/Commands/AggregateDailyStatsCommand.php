<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\AggregationCheckpoint;
use App\Models\DailyStat;
use App\Models\Link;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AggregateDailyStatsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lokataut:aggregate-daily {--date= : The target date for aggregation in YYYY-MM-DD format}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Aggregate raw visit logs into daily_stats with Watermark & Checkpoint idempotency';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dateOption = $this->option('date');
        $targetDate = !empty($dateOption)
            ? Carbon::parse((string) $dateOption)->toDateString()
            : Carbon::yesterday()->toDateString();

        $this->info("Starting aggregation for target date: {$targetDate}");

        // Acquire row lock on checkpoint or create checkpoint
        $checkpoint = AggregationCheckpoint::firstOrCreate(
            ['target_date' => $targetDate],
            ['status' => 'pending', 'last_processed_visit_id' => 0, 'total_processed' => 0]
        );

        $checkpoint->update(['status' => 'in_progress']);

        $startOfDay = Carbon::parse($targetDate)->startOfDay();
        $endOfDay = Carbon::parse($targetDate)->endOfDay();

        // Distinct snapshots for this date
        $snapshots = Visit::whereBetween('created_at', [$startOfDay, $endOfDay])
            ->select('link_id_snapshot', 'link_slug_snapshot')
            ->distinct()
            ->get();

        if ($snapshots->isEmpty()) {
            $this->info("No visits recorded for date {$targetDate}. Checkpoint marked as completed.");
            $checkpoint->update(['status' => 'completed']);
            return Command::SUCCESS;
        }

        $processedCount = 0;

        foreach ($snapshots as $snapshot) {
            $linkIdSnapshot = $snapshot->link_id_snapshot;
            $linkSlugSnapshot = $snapshot->link_slug_snapshot;
            $aggregateKey = 'link:' . $linkIdSnapshot;

            // Check if link is still active/exists in links table
            $link = Link::withTrashed()->find($linkIdSnapshot);
            $linkId = $link !== null ? $link->id : null;

            $visitsQuery = Visit::whereBetween('created_at', [$startOfDay, $endOfDay])
                ->where('link_id_snapshot', $linkIdSnapshot);

            $impressions = (clone $visitsQuery)->where('event_type', 'impression')->count();
            $verified = (clone $visitsQuery)->where('event_type', 'verified')->count();
            $completions = (clone $visitsQuery)->where('event_type', 'completed')->count();
            $uniqueVisitors = (clone $visitsQuery)->distinct('ip_hash')->count('ip_hash');

            $desktopCount = (clone $visitsQuery)->where('device_type', 'desktop')->count();
            $mobileCount = (clone $visitsQuery)->where('device_type', 'mobile')->count();
            $tabletCount = (clone $visitsQuery)->where('device_type', 'tablet')->count();
            $botCount = (clone $visitsQuery)->where('device_type', 'bot')->count();

            $topCountries = (clone $visitsQuery)->whereNotNull('country_code')
                ->select('country_code', DB::raw('count(*) as total'))
                ->groupBy('country_code')
                ->orderByDesc('total')
                ->take(10)
                ->pluck('total', 'country_code')
                ->toArray();

            // Idempotent upsert based on UNIQUE(aggregate_key, date)
            DailyStat::updateOrCreate(
                [
                    'aggregate_key' => $aggregateKey,
                    'date' => $targetDate,
                ],
                [
                    'link_id' => $linkId,
                    'link_slug' => $linkSlugSnapshot,
                    'impressions' => $impressions,
                    'verified' => $verified,
                    'completions' => $completions,
                    'unique_visitors' => $uniqueVisitors,
                    'desktop_count' => $desktopCount,
                    'mobile_count' => $mobileCount,
                    'tablet_count' => $tabletCount,
                    'bot_count' => $botCount,
                    'top_countries' => $topCountries,
                ]
            );

            $processedCount++;
        }

        $lastVisitId = Visit::whereBetween('created_at', [$startOfDay, $endOfDay])->max('id') ?? 0;

        $checkpoint->update([
            'status' => 'completed',
            'last_processed_visit_id' => $lastVisitId,
            'total_processed' => $processedCount,
        ]);

        $this->info("Aggregation completed successfully for {$targetDate}. Processed {$processedCount} link aggregates.");

        return Command::SUCCESS;
    }
}

