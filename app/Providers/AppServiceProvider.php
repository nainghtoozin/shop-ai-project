<?php

namespace App\Providers;

use App\Models\Setting;
use App\Models\PaymentMethod;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Global helpers
        require_once app_path('Support/helpers.php');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Share settings globally (cached)
        try {
            if (Schema::hasTable('settings')) {
                View::share('settings', setting());
            } else {
                View::share('settings', []);
            }
        } catch (\Throwable $e) {
            View::share('settings', []);
        }

        // Supported locales for the language switcher.
        View::share('supportedLocales', (array) config('localization.supported', ['en' => 'English']));

        // Default locale from settings (useful for views; middleware applies it for new visitors).
        try {
            View::share('defaultLocale', (string) (setting((string) config('localization.setting_key', 'default_language'), config('app.locale', 'en'))));
        } catch (\Throwable $e) {
            View::share('defaultLocale', (string) config('app.locale', 'en'));
        }

        // Share footer payment methods globally (no cache -> immediate updates)
        try {
            if (Schema::hasTable('payment_methods')) {
                View::share(
                    'footerPaymentMethods',
                    PaymentMethod::query()->active()->orderBy('name')->get(['id', 'type', 'name'])
                );
            } else {
                View::share('footerPaymentMethods', collect());
            }
        } catch (\Throwable $e) {
            View::share('footerPaymentMethods', collect());
        }
        // Component aliases are not needed since we're using @include directly
        // Blade::component('admin-layout', 'layouts.admin');
        // Blade::component('admin-sidebar', 'components.admin.sidebar');
        // Blade::component('admin-navbar', 'components.admin.navbar');
    }
}
