<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PruneCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lokataut:prune-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired file cache entries to prevent inode quota exhaustion on shared hosting';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $cachePath = storage_path('framework/cache/data');

        if (!File::isDirectory($cachePath)) {
            $this->info('Cache data directory does not exist.');
            return Command::SUCCESS;
        }

        $now = time();
        $deleted = 0;

        $files = File::allFiles($cachePath);
        foreach ($files as $file) {
            $contents = File::get($file->getRealPath());
            $expire = (int) substr($contents, 0, 10);

            if ($expire > 0 && $now >= $expire) {
                File::delete($file->getRealPath());
                $deleted++;
            }
        }

        $this->info("Pruned {$deleted} expired cache files.");

        return Command::SUCCESS;
    }
}

