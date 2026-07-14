<?php

namespace App\Providers;

use App\Auth\TenantUserProvider;
use App\Models\User;
use App\Services\PermissionRegistrar;
use App\Services\SuperAdmin\PlatformSettingsService;
use App\Support\CurrentCompany;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CurrentCompany::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFour();

        Auth::provider('tenant-eloquent', function ($app, array $config) {
            return new TenantUserProvider($app['hash'], $config['model']);
        });

        ResetPassword::createUrlUsing(function (User $user, string $token) {
            return url(route('password.reset', [
                'token' => $token,
                'email' => $user->email,
                'company' => $user->company?->slug,
            ], false));
        });

        app(PermissionRegistrar::class)->registerGates();

        RateLimiter::for('website-leads', function (Request $request) {
            return Limit::perMinute(config('website_leads.rate_limit', 10))
                ->by($request->ip());
        });

        $this->applyPlatformBranding();
    }

    private function applyPlatformBranding(): void
    {
        try {
            if (! Schema::hasTable('platform_settings')) {
                return;
            }

            app(PlatformSettingsService::class)->applyBranding();
        } catch (\Throwable) {
            // Ignore during early bootstrap / migrate when DB is unavailable.
        }
    }
}
