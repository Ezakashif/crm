@php
    $trialDays = isset($trialDays)
        ? max(1, (int) $trialDays)
        : max(1, (int) (\App\Models\Plan::query()->active()->where('is_public', true)->where('trial_days', '>', 0)->min('trial_days') ?? app(\App\Services\SuperAdmin\PlatformSettingsService::class)->getInt('trial_duration_days', 14)));
    $trialDuration = $trialDays.' '.\Illuminate\Support\Str::plural('day', $trialDays);
@endphp

Start {{ $trialDuration }} free trial
