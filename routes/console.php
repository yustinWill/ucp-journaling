<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Failures notify Discord through config/backup.php.
Schedule::command('backup:run')->daily();
Schedule::command('backup:clean')->dailyAt('01:00');
Schedule::command('telescope:prune --hours=336')
    ->dailyAt('02:00')
    ->withoutOverlapping();
