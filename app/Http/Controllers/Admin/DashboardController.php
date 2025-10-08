<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
// Subscription models removed
use App\Models\User;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller implements HasMiddleware
{
	public static function middleware(): array
	{
		return [
			new Middleware('auth'),
			new Middleware('can:admin'),
		];
	}

	public function index()
	{
        $authUser = Auth::user();
		$totalUsers = User::count();
		$newUsersThisMonth = User::where('created_at', '>=', now()->startOfMonth())->count();

		// Subscription statistics removed; provide safe placeholders
		$activeSubscriptions = 0;
		$pendingApplications = 0;
		$monthlyRevenue = 0.0;
		$revenueGrowth = 0.0;
		$subscriptionRate = 0.0;
		$planDistribution = collect();
		$recentSubscriptions = collect();

        $recentUsers = User::latest()->take(10)->get();

        // If the admin is also a mentor, load their mentees via accepted mentor_requests mapping
        $mentees = collect();
        if ($authUser && $authUser->is_mentor) {
            $mentees = User::whereIn('id', \App\Models\MentorRequest::where('mentor_id', $authUser->id)
                    ->where('status', 'accepted')
                    ->pluck('user_id'))
                ->latest()
                ->take(10)
                ->get(['id','name','email','created_at']);
        }

	// Subscription metrics removed; set safe defaults
	$averageSubscriptionValue = 0.0;
	$averageSubscriptionDuration = 0;
	$renewalRate = 0.0;
	$churnRate = 0.0;

		return view('admin.dashboard.index', compact(
			'totalUsers',
			'newUsersThisMonth',
			'activeSubscriptions',
			'pendingApplications',
			'monthlyRevenue',
			'revenueGrowth',
			'subscriptionRate',
			'planDistribution',
			'recentSubscriptions',
            'recentUsers',
            'mentees',
			'averageSubscriptionValue',
			'averageSubscriptionDuration',
			'renewalRate',
			'churnRate'
		));
	}
}
