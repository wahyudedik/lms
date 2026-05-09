<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule analytics reports
Schedule::job(new \App\Jobs\SendScheduledAnalyticsReport('daily'))
    ->daily()
    ->at('08:00')
    ->name('analytics:send-daily-report')
    ->onOneServer();

Schedule::job(new \App\Jobs\SendScheduledAnalyticsReport('weekly'))
    ->weekly()
    ->mondays()
    ->at('09:00')
    ->name('analytics:send-weekly-report')
    ->onOneServer();

Schedule::job(new \App\Jobs\SendScheduledAnalyticsReport('monthly'))
    ->monthlyOn(1, '10:00')
    ->name('analytics:send-monthly-report')
    ->onOneServer();

// Auto-unblock users whose block period has expired (runs daily at 01:00)
Schedule::command('cheating:auto-unblock --days=7')
    ->daily()
    ->at('01:00')
    ->name('cheating:auto-unblock')
    ->onOneServer()
    ->withoutOverlapping();

// Cleanup expired push subscriptions (not updated in 90 days)
Schedule::command('notifications:cleanup-subscriptions')->weekly();

// Send assignment deadline reminders (24 hours before deadline)
Schedule::command('assignments:send-deadline-reminders')
    ->hourly()
    ->name('assignments:send-deadline-reminders')
    ->onOneServer()
    ->withoutOverlapping();
