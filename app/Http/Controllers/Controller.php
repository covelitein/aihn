<?php

namespace App\Http\Controllers;
// use App\Models\SubscriptionApplication;
// use Illuminate\Support\Facades\View;

abstract class Controller
{
    // public function boot()
    // {
        // Share pending applications count with all views
    //     View::composer('*', function ($view) {
    //         if (auth()->check() && auth()->user()->is_admin) {
    //             $pendingApplicationsCount = SubscriptionApplication::where('status', 'pending')->count();
    //             $view->with('pendingApplicationsCount', $pendingApplicationsCount);
    //         } else {
    //             $view->with('pendingApplicationsCount', 0);
    //         }
    //     });
    // }
}
