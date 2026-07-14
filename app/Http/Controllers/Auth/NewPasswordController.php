<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'company' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $company = Company::query()
            ->where('slug', strtolower(trim((string) $request->input('company'))))
            ->first();

        if (! $company) {
            throw ValidationException::withMessages([
                'company' => __('This password reset token is invalid.'),
            ]);
        }

        $status = Password::reset(
            [
                'email' => $request->input('email'),
                'company_id' => $company->id,
                'is_super_admin' => false,
                'password' => $request->input('password'),
                'password_confirmation' => $request->input('password_confirmation'),
                'token' => $request->input('token'),
            ],
            function (User $user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status == Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withInput($request->only('company', 'email'))
                ->withErrors(['email' => __($status)]);
    }
}
