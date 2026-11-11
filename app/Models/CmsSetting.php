<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CmsSetting extends Model
{
    protected $fillable = ['key', 'value', 'group'];

    /**
     * Get a setting by key with an optional default.
     */
    public static function get(string $key, $default = null)
    {
        return Cache::rememberForever("cms_setting_{$key}", function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set a setting by key.
     */
    public static function set(string $key, $value, string $group = 'general'): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value, 'group' => $group]);
        Cache::forget("cms_setting_{$key}");
        Cache::forget('cms_all_settings');
    }

    /**
     * Get all settings as a key-value associative array.
     */
    public static function allKeyed(): array
    {
        return Cache::rememberForever('cms_all_settings', function () {
            return static::pluck('value', 'key')->toArray();
        });
    }
}

