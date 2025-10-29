<?php
/**
 * -------------------------------------------------------------
 * Project: AIHN Platform
 * -------------------------------------------------------------
 * Description:
 * This file was collaboratively developed as part of the AIHN
 * platform modules. It demonstrates teamwork and shared logic.
 *
 * Authors:
 *  - John Nwanosike <johnnwanosike@gmail.com>
 *  - Abraham Covenant <abrahamcovenant2004@gmail.com>
 * -------------------------------------------------------------
 */

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
// use App\Models\SubscriptionApplication;
// use Illuminate\Support\Facades\View;

abstract class Controller
{
    use AuthorizesRequests;
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
