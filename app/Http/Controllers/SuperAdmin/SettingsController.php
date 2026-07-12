<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\UpdatePlatformSettingsRequest;
use App\Services\ActivityLogger;
use App\Services\SuperAdmin\PlatformLogoProcessor;
use App\Services\SuperAdmin\PlatformSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function __construct(
        protected PlatformSettingsService $settings,
        protected PlatformLogoProcessor $logoProcessor,
    ) {}

    public function edit(): View
    {
        return view('superadmin.settings.edit', [
            'settings' => $this->settings->all(),
            'logoUrl' => $this->settings->logoUrl(),
        ]);
    }

    public function update(UpdatePlatformSettingsRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->boolean('remove_logo')) {
            $this->deleteLogo();
            $validated['platform_logo_path'] = null;
        }

        if ($request->hasFile('platform_logo')) {
            $this->ensurePublicStorageLink();
            $this->deleteLogo();
            $validated['platform_logo_path'] = $this->logoProcessor->storeProcessed(
                $request->file('platform_logo')
            );
        }

        unset($validated['platform_logo'], $validated['remove_logo']);

        $this->settings->setMany([
            'platform_name' => $validated['platform_name'],
            'default_timezone' => $validated['default_timezone'],
            'default_currency' => $validated['default_currency'],
            'mail_from_name' => $validated['mail_from_name'] ?? null,
            'mail_from_address' => $validated['mail_from_address'] ?? null,
            'registration_enabled' => $request->boolean('registration_enabled'),
            'maintenance_mode' => $request->boolean('maintenance_mode'),
            'trial_duration_days' => $validated['trial_duration_days'],
            'default_company_status' => $validated['default_company_status'],
            'platform_logo_path' => array_key_exists('platform_logo_path', $validated)
                ? $validated['platform_logo_path']
                : $this->settings->get('platform_logo_path'),
        ]);

        $this->settings->applyBranding();

        ActivityLogger::log('platform.settings_updated', null, [
            'keys' => array_keys($validated),
        ]);

        return back()->with('success', 'Platform settings saved.');
    }

    public function announcement(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'broadcast_announcement' => ['nullable', 'string', 'max:2000'],
        ]);

        $this->settings->setMany([
            'broadcast_announcement' => $validated['broadcast_announcement'] ?: null,
        ]);

        ActivityLogger::log('platform.announcement_updated', null, [
            'has_message' => filled($validated['broadcast_announcement'] ?? null),
        ]);

        return back()->with('success', 'Announcement updated.');
    }

    private function deleteLogo(): void
    {
        $path = $this->settings->get('platform_logo_path');

        if (filled($path) && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    private function ensurePublicStorageLink(): void
    {
        $link = public_path('storage');
        $target = storage_path('app/public');

        if (file_exists($link) || is_link($link)) {
            return;
        }

        if (! is_dir($target)) {
            mkdir($target, 0755, true);
        }

        symlink($target, $link);
    }
}
