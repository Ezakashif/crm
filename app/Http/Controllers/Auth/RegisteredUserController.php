<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\CompanyProvisioner;
use App\Services\SuperAdmin\PlatformSettingsService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(PlatformSettingsService $settings): View
    {
        return view('auth.register', [
            'platformName' => $settings->platformName(),
        ]);
    }

    public function store(
        Request $request,
        CompanyProvisioner $provisioner,
        PlatformSettingsService $settings,
    ): RedirectResponse {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $result = $provisioner->provision([
            'name' => $validated['company_name'],
            'status' => $settings->get('default_company_status', Company::STATUS_ACTIVE),
            'subscription_status' => Company::SUBSCRIPTION_TRIAL,
            'admin_name' => $validated['name'],
            'admin_email' => $validated['email'],
            'admin_password' => $validated['password'],
        ]);

        $admin = $result['admin'];

        event(new Registered($admin));

        Auth::login($admin);
        $request->session()->regenerate();

        return redirect()
            ->route('dashboard')
            ->with(
                'success',
                'Welcome! Your workspace slug is "'.$result['company']->slug.'". Use it when signing in next time.',
            );
    }
}
