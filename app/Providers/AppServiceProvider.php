<?php

namespace App\Providers;

use App\Models\Setting;
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
        // Component aliases are not needed since we're using @include directly
        // Blade::component('admin-layout', 'layouts.admin');
        // Blade::component('admin-sidebar', 'components.admin.sidebar');
        // Blade::component('admin-navbar', 'components.admin.navbar');
    }
}
