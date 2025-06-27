<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Queue worker — process all available jobs then exit, no overlapping
Schedule::command('queue:work --stop-when-empty')
    ->everyMinute()
    ->withoutOverlapping();

// Graceful queue worker restart every hour
Schedule::command('queue:restart')
    ->hourly();
