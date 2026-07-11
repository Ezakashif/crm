<?php

namespace App\Http\Middleware;

use App\Models\Company;
use App\Support\CurrentCompany;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompanyContext
{
    public function __construct(
        private CurrentCompany $currentCompany,
    ) {}

    /**
     * Resolve the authenticated user's company into CurrentCompany for the request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $this->currentCompany->clear();

        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        // Platform / Super Admin users (null company_id) are not supported on CRM routes yet.
        if ($user->company_id === null) {
            return $this->deny($request, 'Your account is not assigned to a company. Please contact support.');
        }

        $company = Company::query()->find($user->company_id);

        if (! $company) {
            return $this->deny($request, 'Your company could not be found. Please contact support.');
        }

        if (! $company->isActive()) {
            return $this->deny($request, 'Your company account is suspended. Please contact support.');
        }

        $this->currentCompany->set($company);

        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        $this->currentCompany->clear();
    }

    private function deny(Request $request, string $message): Response
    {
        $this->currentCompany->clear();

        Auth::logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['message' => $message], 403);
        }

        return redirect()
            ->route('login')
            ->withErrors(['email' => $message]);
    }
}
