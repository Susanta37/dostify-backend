<?php

use App\Jobs\CleanupExpiredMatchesJob;
use App\Jobs\GenerateAnalyticsJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new CleanupExpiredMatchesJob)->everyFiveMinutes();
Schedule::job(new GenerateAnalyticsJob(now()->toDateString()))->daily();
