<?php

namespace App\Services\SuperAdmin;

use App\Models\PlatformSetting;
use Illuminate\Support\Facades\Cache;

class PlatformSettingsService
{
    public const CACHE_KEY = 'platform_settings';

    /**
     * @return array<string, string|null>
     */
    public function all(): array
    {
        return Cache::remember(self::CACHE_KEY, 300, function () {
            return PlatformSetting::query()
                ->pluck('value', 'key')
                ->all();
        });
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $all = $this->all();

        return array_key_exists($key, $all) ? $all[$key] : $default;
    }

    public function getBool(string $key, bool $default = false): bool
    {
        $value = $this->get($key);

        if ($value === null) {
            return $default;
        }

        return in_array((string) $value, ['1', 'true', 'yes', 'on'], true);
    }

    public function getInt(string $key, int $default = 0): int
    {
        $value = $this->get($key);

        return is_numeric($value) ? (int) $value : $default;
    }

    /**
     * @param  array<string, mixed>  $values
     */
    public function setMany(array $values): void
    {
        foreach ($values as $key => $value) {
            if (is_bool($value)) {
                $value = $value ? '1' : '0';
            }

            PlatformSetting::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $value === null ? null : (string) $value],
            );
        }

        Cache::forget(self::CACHE_KEY);
    }

    public function platformName(): string
    {
        return (string) ($this->get('platform_name') ?: config('app.name'));
    }

    /**
     * Public-relative path suitable for asset(), e.g. "storage/platform/logo.png".
     */
    public function logoAssetPath(): ?string
    {
        $path = $this->get('platform_logo_path');

        if (! filled($path)) {
            return null;
        }

        return 'storage/'.ltrim((string) $path, '/');
    }

    public function logoUrl(): ?string
    {
        $path = $this->logoAssetPath();

        if ($path === null) {
            return null;
        }

        return asset($path);
    }

    /**
     * Apply platform name/logo to AdminLTE (CRM sidebar, login) and app.name.
     */
    public function applyBranding(): void
    {
        $name = e($this->platformName());
        $logoPath = $this->logoAssetPath();

        config([
            'app.name' => $this->platformName(),
            'adminlte.logo' => '<b>'.$name.'</b>',
            'adminlte.logo_img_alt' => $this->platformName(),
        ]);

        if ($logoPath !== null) {
            config([
                'adminlte.logo_img' => $logoPath,
                'adminlte.logo_img_class' => 'brand-image',
                'adminlte.logo' => '',
                'adminlte.auth_logo.enabled' => true,
                'adminlte.auth_logo.img.path' => $logoPath,
                'adminlte.auth_logo.img.alt' => $this->platformName(),
                'adminlte.auth_logo.img.class' => '',
                'adminlte.auth_logo.img.width' => null,
                'adminlte.auth_logo.img.height' => 56,
            ]);
        }
    }

    public function announcement(): ?string
    {
        $value = $this->get('broadcast_announcement');

        return filled($value) ? (string) $value : null;
    }
}
