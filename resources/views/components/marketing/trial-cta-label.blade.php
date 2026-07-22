@php
    $trialDays = max(1, app(\App\Services\SuperAdmin\PlatformSettingsService::class)->getInt('trial_duration_days', 14));
@endphp

Start {{ $trialDays }}-day free trial
