<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    private const CACHE_PREFIX = 'setting_';
    private const CACHE_TTL_SECONDS = 3600;

    /**
     * Get a setting by key, with default value fallback.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember(self::CACHE_PREFIX . $key, self::CACHE_TTL_SECONDS, function () use ($key, $default) {
            $setting = Setting::where('key', $key)->first();
            return $setting ? $setting->typed_value : $default;
        });
    }

    /**
     * Set a setting value.
     */
    public function set(string $key, mixed $value, ?string $type = null, ?string $group = null): void
    {
        $setting = Setting::firstOrNew(['key' => $key]);

        if ($type !== null) {
            $setting->type = $type;
        }

        if ($group !== null) {
            $setting->group = $group;
        }

        $setting->value = is_array($value) ? json_encode($value) : (string) $value;
        $setting->save();

        Cache::forget(self::CACHE_PREFIX . $key);
    }

    /**
     * Clear all settings cache.
     */
    public function clearCache(): void
    {
        $keys = Setting::pluck('key');
        foreach ($keys as $key) {
            Cache::forget(self::CACHE_PREFIX . $key);
        }
    }
}

