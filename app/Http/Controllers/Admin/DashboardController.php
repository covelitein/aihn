<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionApplication;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

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
		$totalUsers = User::count();
		$newUsersThisMonth = User::where('created_at', '>=', now()->startOfMonth())->count();

		$activeSubscriptions = User::where('is_subscribed', true)
			->where('subscription_status', 'active')
			->where('subscription_expires_at', '>', now())
			->count();

		$pendingApplications = SubscriptionApplication::whereIn('status', ['pending', 'under_review'])->count();

		$approvedThisMonth = SubscriptionApplication::where('status', 'approved')
			->where('approved_at', '>=', now()->startOfMonth())
			->get(['amount_paid']);
		$monthlyRevenue = (float) $approvedThisMonth->sum('amount_paid');

		$lastMonthRevenue = (float) SubscriptionApplication::where('status', 'approved')
			->whereBetween('approved_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
			->sum('amount_paid');
		$revenueGrowth = $monthlyRevenue - $lastMonthRevenue;

		$subscriptionRate = $totalUsers > 0 ? ($activeSubscriptions / $totalUsers) * 100 : 0.0;

		$plans = SubscriptionPlan::orderBy('price')->get();
		$planDistribution = $plans->map(function ($plan) use ($activeSubscriptions) {
			$activeCount = User::where('current_subscription_id', function ($q) use ($plan) {
				$q->select('id')
					->from('subscription_applications')
					->whereColumn('subscription_applications.id', 'users.current_subscription_id')
					->where('plan_id', $plan->id)
					->limit(1);
			})->count();

			$monthlyRevenue = SubscriptionApplication::where('status', 'approved')
				->where('plan_id', $plan->id)
				->where('approved_at', '>=', now()->startOfMonth())
				->sum('amount_paid');

			$percentage = $activeSubscriptions > 0 ? ($activeCount / $activeSubscriptions) * 100 : 0.0;
			$plan->active_subscribers_count = $activeCount;
			$plan->monthly_revenue = (float) $monthlyRevenue;
			$plan->percentage = (float) $percentage;
			$plan->color = null;
			return $plan;
		});

		$recentSubscriptions = SubscriptionApplication::with(['user', 'plan'])
			->whereIn('status', ['approved', 'under_review', 'pending'])
			->latest()
			->take(10)
			->get();

		$recentUsers = User::latest()->take(10)->get();

		$averageSubscriptionValue = SubscriptionApplication::where('status', 'approved')->avg('amount_paid') ?: 0.0;
		$averageSubscriptionDuration = 30; // Placeholder unless you track per-plan duration days elsewhere
		$renewalRate = 0.0; // Not tracked explicitly; set safe default
		$churnRate = 0.0;   // Not tracked explicitly; set safe default

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
			'averageSubscriptionValue',
			'averageSubscriptionDuration',
			'renewalRate',
			'churnRate'
		));
	}
}
