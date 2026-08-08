<?php

namespace App\Support;

use App\Services\SuperAdmin\PlatformSettingsService;
use Illuminate\Support\Facades\Route;

/**
 * Resolves public marketing CTAs based on platform registration settings.
 */
class MarketingCta
{
    public static function registrationEnabled(): bool
    {
        try {
            return app(PlatformSettingsService::class)->getBool('registration_enabled');
        } catch (\Throwable) {
            return false;
        }
    }

    public static function trialAvailable(): bool
    {
        $route = (string) config('marketing.cta.trial_route', 'register');

        return self::registrationEnabled() && Route::has($route);
    }

    /**
     * Platform-wide free-trial length from Super Admin settings.
     */
    public static function trialDurationDays(): int
    {
        try {
            return max(1, app(PlatformSettingsService::class)->getInt('trial_duration_days', 14));
        } catch (\Throwable) {
            return 14;
        }
    }

    /**
     * Whether a public plan should advertise the free trial CTA.
     */
    public static function planOffersTrial(object $plan): bool
    {
        return (bool) ($plan->is_free ?? false) || (int) ($plan->trial_days ?? 0) > 0;
    }

    public static function trialHref(): ?string
    {
        if (! self::trialAvailable()) {
            return null;
        }

        return route((string) config('marketing.cta.trial_route', 'register'));
    }

    public static function demoHref(): string
    {
        return route(
            (string) config('marketing.cta.demo_route', 'marketing.contact'),
            (array) config('marketing.cta.demo_query', []),
        );
    }

    /**
     * Preferred primary action: free trial when registration is open, otherwise demo.
     */
    public static function primaryHref(): string
    {
        return self::trialHref() ?? self::demoHref();
    }
}
