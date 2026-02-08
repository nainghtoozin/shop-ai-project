<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;

if (!function_exists('setting')) {
    /**
     * Get a website setting from the database (cached).
     *
     * @param  string|null  $key  If null, returns all settings as array.
     * @param  mixed  $default
     */
    function setting(?string $key = null, $default = null)
    {
        static $loaded = false;
        static $all = [];
        static $hasTable = null;

        try {
            if ($hasTable === null) {
                $hasTable = Schema::hasTable('settings');
            }

            if (!$hasTable) {
                return $key === null ? [] : (is_callable($default) ? $default() : $default);
            }

            if (!$loaded) {
                $all = Setting::allCached();
                $loaded = true;
            }

            if ($key === null) return $all;

            $value = $all[$key] ?? null;

            // Treat null/empty-string as missing and fall back.
            if ($value === null) return is_callable($default) ? $default() : $default;
            if (is_string($value) && trim($value) === '') return is_callable($default) ? $default() : $default;

            return $value;
        } catch (Throwable $e) {
            return $key === null ? [] : (is_callable($default) ? $default() : $default);
        }
    }
}
