<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
            'company' => \App\Http\Middleware\EnsureCompanyContext::class,
            'website-lead-webhook' => \App\Http\Middleware\VerifyWebsiteLeadWebhook::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'webhooks/leads/website',
            'logout',
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
