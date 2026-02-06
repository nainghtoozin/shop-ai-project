<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value'];

    public static function allCached(int $ttlSeconds = 3600): array
    {
        return Cache::remember('settings.all', $ttlSeconds, function () {
            return static::query()->pluck('value', 'key')->toArray();
        });
    }

    public static function getValue(string $key, $default = null)
    {
        $settings = static::allCached();
        return array_key_exists($key, $settings) && $settings[$key] !== null ? $settings[$key] : $default;
    }

    public static function setValue(string $key, $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget('settings.all');
    }

    public static function setMany(array $data): void
    {
        foreach ($data as $key => $value) {
            static::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }
        Cache::forget('settings.all');
    }
}
