<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email'],
        ]);

        $company = Company::query()
            ->where('slug', strtolower(trim($validated['company'])))
            ->first();

        if (! $company) {
            // Do not leak whether the workspace exists.
            return back()->with('status', __(Password::RESET_LINK_SENT));
        }

        $status = Password::sendResetLink([
            'email' => $validated['email'],
            'company_id' => $company->id,
            'is_super_admin' => false,
        ]);

        return $status == Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withInput($request->only('company', 'email'))
                ->withErrors(['email' => __($status)]);
    }
}
