<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
        // Component aliases are not needed since we're using @include directly
        // Blade::component('admin-layout', 'layouts.admin');
        // Blade::component('admin-sidebar', 'components.admin.sidebar');
        // Blade::component('admin-navbar', 'components.admin.navbar');
    }
}
