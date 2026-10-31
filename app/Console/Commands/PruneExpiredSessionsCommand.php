<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\VisitorSession;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PruneExpiredSessionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lokataut:prune-sessions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prune visitor sessions older than 24 hours to conserve disk space';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $cutoff = Carbon::now()->subHours(24);

        $deletedCount = VisitorSession::where('created_at', '<', $cutoff)->delete();

        $this->info("Pruned {$deletedCount} expired visitor sessions older than 24 hours.");

        return Command::SUCCESS;
    }
}

