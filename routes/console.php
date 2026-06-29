<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('leads:send-follow-up-reminders')
    ->dailyAt(config('lead_reminders.schedule_time', '08:00'))
    ->when(fn () => config('lead_reminders.enabled', true));
