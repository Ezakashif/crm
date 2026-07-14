<?php

namespace App\Http\Requests\Auth;

use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'company' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * Tenant users must supply a company/workspace slug so the same email
     * across companies cannot authenticate into the wrong tenant.
     * Super Admins authenticate without a company slug.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $user = $this->resolveAuthenticatableUser();

        if (! $user || ! Hash::check($this->input('password'), $user->password)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        if (! $user->isActive()) {
            throw ValidationException::withMessages([
                'email' => 'Your account is not active. Please contact an administrator.',
            ]);
        }

        Auth::login($user, $this->boolean('remember'));

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Resolve the user for this login attempt without cross-tenant ambiguity.
     */
    private function resolveAuthenticatableUser(): ?User
    {
        $email = (string) $this->input('email');
        $companySlug = Str::lower(trim((string) $this->input('company', '')));

        if ($companySlug !== '') {
            $company = Company::query()->where('slug', $companySlug)->first();

            if (! $company) {
                return null;
            }

            return User::withoutCompanyScope()
                ->where('email', $email)
                ->where('company_id', $company->id)
                ->where('is_super_admin', false)
                ->first();
        }

        // No workspace slug: only platform Super Admins may authenticate.
        return User::withoutCompanyScope()
            ->where('email', $email)
            ->where('is_super_admin', true)
            ->whereNull('company_id')
            ->first();
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        $company = Str::lower(trim((string) $this->input('company', '')));

        return Str::transliterate(
            ($company !== '' ? $company.'|' : '')
            .Str::lower($this->string('email')).'|'.$this->ip()
        );
    }
}
