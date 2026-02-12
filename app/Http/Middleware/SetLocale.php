<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $supported = array_keys((array) config('localization.supported', ['en' => 'English']));
        $sessionKey = (string) config('localization.session_key', 'locale');
        $settingKey = (string) config('localization.setting_key', 'default_language');

        $locale = $request->session()->get($sessionKey);

        if (!is_string($locale) || !in_array($locale, $supported, true)) {
            // Default for new visitors: settings table -> config(app.locale) -> 'en'
            $dbDefault = null;
            try {
                $dbDefault = setting($settingKey);
            } catch (\Throwable $e) {
                $dbDefault = null;
            }

            $locale = is_string($dbDefault) ? $dbDefault : (string) config('app.locale', 'en');
        }

        if (!in_array($locale, $supported, true)) {
            $locale = 'en';
        }

        App::setLocale($locale);

        if (class_exists(\Carbon\Carbon::class)) {
            \Carbon\Carbon::setLocale($locale);
        }

        return $next($request);
    }
}
