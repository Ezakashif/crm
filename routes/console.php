<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

$remindersEnabled = fn () => (bool) config('lead_reminders.enabled', true);
$dailyTime = config('lead_reminders.schedule_time', '08:00');

Schedule::command('leads:send-follow-up-reminders --tier=day_before')
    ->dailyAt($dailyTime)
    ->when($remindersEnabled)
    ->when(fn () => config('lead_reminders.tiers.day_before.enabled', true));

Schedule::command('leads:send-follow-up-reminders --tier=due')
    ->dailyAt($dailyTime)
    ->when($remindersEnabled)
    ->when(fn () => config('lead_reminders.tiers.due.enabled', true));

Schedule::command('leads:send-follow-up-reminders --tier=hours_before')
    ->hourly()
    ->when($remindersEnabled)
    ->when(fn () => config('lead_reminders.tiers.hours_before.enabled', true));

Schedule::call(function () {
    \App\Models\PlatformSetting::query()->updateOrCreate(
        ['key' => 'scheduler_last_run_at'],
        ['value' => now()->toIso8601String()],
    );
    \Illuminate\Support\Facades\Cache::forget(\App\Services\SuperAdmin\PlatformSettingsService::CACHE_KEY);
})->everyFiveMinutes()->name('platform-scheduler-heartbeat');
