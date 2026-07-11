<?php

namespace App\Providers;

use App\Support\CurrentCompany;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use App\Services\PermissionRegistrar;
use Illuminate\Support\Facades\RateLimiter;
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

        app(PermissionRegistrar::class)->registerGates();

        RateLimiter::for('website-leads', function (Request $request) {
            return Limit::perMinute(config('website_leads.rate_limit', 10))
                ->by($request->ip());
        });
    }
}
