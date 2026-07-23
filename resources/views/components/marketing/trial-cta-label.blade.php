@php
    $platformTrialDays = app(\App\Services\SuperAdmin\PlatformSettingsService::class)->getInt('trial_duration_days', 14);
    $planTrialDays = \Illuminate\Support\Facades\Schema::hasTable('plans')
        ? \App\Models\Plan::query()->active()->where('is_public', true)->where('trial_days', '>', 0)->min('trial_days')
        : null;
    $trialDays = isset($trialDays)
        ? max(1, (int) $trialDays)
        : max(1, (int) ($planTrialDays ?? $platformTrialDays));
    $trialDuration = $trialDays.' '.\Illuminate\Support\Str::plural('day', $trialDays);
@endphp

Start {{ $trialDuration }} free trial
