<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'permission' => \App\Http\Middleware\EnsureUserHasPermission::class,
            'active' => \App\Http\Middleware\EnsureUserIsActive::class,
            'website-lead-webhook' => \App\Http\Middleware\VerifyWebsiteLeadWebhook::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'webhooks/leads/website',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
