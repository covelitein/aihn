<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubscriptionPlanController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::orderBy('sort_order')
            ->orderBy('price')
            ->get();

        return view('admin.subscription.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.subscription.plans.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|in:monthly,quarterly,yearly',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0'
        ]);

        // Set duration in months based on selected duration
        $validated['duration_in_months'] = match($validated['duration']) {
            'monthly' => 1,
            'quarterly' => 3,
            'yearly' => 12,
        };

        // Generate slug from name
        $validated['slug'] = Str::slug($validated['name']);

        // Create the plan
        SubscriptionPlan::create($validated);

        return redirect()
            ->route('admin.subscription.plans.index')
            ->with('success', 'Subscription plan created successfully.');
    }

    public function edit(SubscriptionPlan $plan)
    {
        return view('admin.subscription.plans.form', compact('plan'));
    }

    public function update(Request $request, SubscriptionPlan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|in:monthly,quarterly,yearly',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0'
        ]);

        // Set duration in months based on selected duration
        $validated['duration_in_months'] = match($validated['duration']) {
            'monthly' => 1,
            'quarterly' => 3,
            'yearly' => 12,
        };

        // Update slug only if name changed
        if ($request->name !== $plan->name) {
            $validated['slug'] = Str::slug($request->name);
        }

        $plan->update($validated);

        return redirect()
            ->route('admin.subscription.plans.index')
            ->with('success', 'Subscription plan updated successfully.');
    }

    public function destroy(SubscriptionPlan $plan)
    {
        // Check if plan has active subscribers
        if ($plan->activeSubscribers()->count() > 0) {
            return redirect()
                ->route('admin.subscription.plans.index')
                ->with('error', 'Cannot delete plan with active subscribers.');
        }

        $plan->delete();

        return redirect()
            ->route('admin.subscription.plans.index')
            ->with('success', 'Subscription plan deleted successfully.');
    }

    public function toggleStatus(SubscriptionPlan $plan)
    {
        $plan->update(['is_active' => !$plan->is_active]);

        return redirect()
            ->route('admin.subscription.plans.index')
            ->with('success', 'Plan status updated successfully.');
    }
}