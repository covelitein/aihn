<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
    // Subscription Blade components removed (subscription flow archived)

        // Authorization Gates
        Gate::define('admin', function ($user) {
            return (bool) ($user?->is_admin) || (bool) ($user?->is_super_admin);
        });

        Gate::define('superadmin', function ($user) {
            return (bool) ($user?->is_super_admin);
        });
    }
}
