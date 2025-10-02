<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\SubscriptionApplication;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Determine active subscription and plan
        $activeSubscription = $user->activeSubscription()->with('plan')->first();
        $currentPlan = $activeSubscription?->plan;
        $currentPlanId = $currentPlan?->id;
        
        // Get recently added content
        $recentContent = Content::published()
            ->when($currentPlanId, function($query) use ($currentPlanId) {
                $query->forPlan($currentPlanId);
            })
            ->latest()
            ->take(4)
            ->get();

        // Get subscription application if pending
        $pendingApplication = null;
        if (!$user->is_subscription_active) {
            $pendingApplication = SubscriptionApplication::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'under_review'])
                ->latest()
                ->first();
        }

        // Get available plans if no active subscription
        $availablePlans = null;
        if (!$user->is_subscription_active && !$pendingApplication) {
            $availablePlans = SubscriptionPlan::active()
                ->orderBy('price')
                ->take(3)
                ->get();
        }

        // Get content statistics by type
        $contentStats = [];
        if ($user->is_subscription_active && $currentPlanId) {
            $contentStats = Content::published()
                ->forPlan($currentPlanId)
                ->selectRaw('type, count(*) as count')
                ->groupBy('type')
                ->get()
                ->pluck('count', 'type')
                ->toArray();
        }

        return view('dashboard', compact(
            'recentContent',
            'pendingApplication',
            'availablePlans',
            'contentStats',
            'currentPlan'
        ));
    }
}