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
        // Register Blade Components
        Blade::component('subscription.application-card', \App\View\Components\Subscription\ApplicationCard::class);

        // Authorization Gate for admin
        Gate::define('admin', function ($user) {
            return (bool) ($user?->is_admin);
        });
    }
}
