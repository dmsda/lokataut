<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\AggregationCheckpoint;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PruneOldVisitDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lokataut:prune-visits';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prune raw visit logs older than 90 days after verifying aggregation completion';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $cutoff = Carbon::today()->subDays(90)->startOfDay();

        // Check if there are unaggregated checkpoints older than 90 days
        $unaggregatedDates = AggregationCheckpoint::where('target_date', '<', $cutoff->toDateString())
            ->where('status', '!=', 'completed')
            ->pluck('target_date')
            ->toArray();

        if (!empty($unaggregatedDates)) {
            $this->warn('Found unaggregated dates older than 90 days: ' . implode(', ', $unaggregatedDates));
            $this->warn('Skipping purge for dates that have not been aggregated.');
        }

        $deletedCount = Visit::where('created_at', '<', $cutoff)
            ->whereNotIn(
                \DB::raw('DATE(created_at)'),
                $unaggregatedDates
            )
            ->delete();

        $this->info("Pruned {$deletedCount} visit event records older than 90 days.");

        return Command::SUCCESS;
    }
}

