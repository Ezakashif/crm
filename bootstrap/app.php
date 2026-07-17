<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/superadmin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'permission' => \App\Http\Middleware\EnsureUserHasPermission::class,
            'active' => \App\Http\Middleware\EnsureUserIsActive::class,
            'company' => \App\Http\Middleware\EnsureCompanyContext::class,
            'superadmin' => \App\Http\Middleware\EnsureSuperAdmin::class,
            'website-lead-webhook' => \App\Http\Middleware\VerifyWebsiteLeadWebhook::class,
            'registration.enabled' => \App\Http\Middleware\EnsurePlatformRegistrationEnabled::class,
            'verified.when_required' => \App\Http\Middleware\EnsureEmailIsVerifiedWhenRequired::class,
        ]);

        $middleware->appendToGroup('web', [
            \App\Http\Middleware\EnsurePlatformNotInMaintenance::class,
        ]);

        // Resolve tenant context before route-model binding so CompanyScope applies.
        $middleware->prependToPriorityList(
            before: \Illuminate\Routing\Middleware\SubstituteBindings::class,
            prepend: \App\Http\Middleware\EnsureCompanyContext::class,
        );

        $middleware->validateCsrfTokens(except: [
            'webhooks/leads/website',
        ]);

        $middleware->redirectGuestsTo(fn () => route('login'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // TokenMismatchException is prepared into an HttpException(419) before render.
        $exceptions->render(function (HttpException $e, Request $request) {
            if ($e->getStatusCode() !== 419) {
                return null;
            }

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Your session has expired. Please log in again.',
                ], 419);
            }

            // Expired CSRF usually means the session ended — send the user to login.
            return redirect()
                ->route('login')
                ->with('status', 'Your session has expired. Please log in again.');
        });
    })->create();
