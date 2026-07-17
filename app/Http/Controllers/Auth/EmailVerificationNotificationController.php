<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogger;
use App\Services\SuperAdmin\PlatformSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request, PlatformSettingsService $settings): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail() || ! $settings->emailVerificationRequired()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        $user->sendEmailVerificationNotification();

        ActivityLogger::log('email.verification_resent', $user, [
            'email' => $user->email,
            'user_agent' => $request->userAgent(),
        ], $user->id);

        return back()->with('status', 'verification-link-sent');
    }
}
