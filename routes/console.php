<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schedule;

Schedule::command('lokataut:prune-sessions')
    ->hourly()
    ->runInBackground();

Schedule::command('lokataut:aggregate-daily')
    ->dailyAt('00:05')
    ->withoutOverlapping(60)
    ->runInBackground();

Schedule::command('lokataut:prune-cache')
    ->dailyAt('03:00')
    ->runInBackground();

Schedule::command('lokataut:prune-visits')
    ->weeklyOn(0, '04:00')
    ->runInBackground();
