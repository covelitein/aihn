<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Models\SubscriptionApplication;
use App\Models\SubscriberProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SubscriptionController extends Controller
{
    public function plans()
    {
        $plans = SubscriptionPlan::active()->ordered()->get();
        return view('subscription.plans', compact('plans'));
    }

    public function showApplicationForm(SubscriptionPlan $plan)
    {
        if (auth()->user()->hasPendingApplication()) {
            return redirect()->route('subscription.status')
                ->with('warning', 'You already have a pending subscription application.');
        }

        return view('subscription.apply', compact('plan'));
    }

    public function submitApplication(Request $request, SubscriptionPlan $plan)
    {
        $request->validate([
            'proof_of_payment' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'transaction_id' => 'nullable|string|max:100',
            'amount_paid' => 'required|numeric|min:' . $plan->price,
            'payment_method' => 'required|in:bank_transfer,credit_card,paypal,other',
            'company_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500'
        ]);

        // Handle file upload
        $proofPath = $request->file('proof_of_payment')->store('proofs', 'public');

        // Create subscription application
        $application = SubscriptionApplication::create([
            'user_id' => auth()->id(),
            'plan_id' => $plan->id,
            'transaction_id' => $request->transaction_id ?: 'TXN-' . Str::random(10),
            'amount_paid' => $request->amount_paid,
            'payment_method' => $request->payment_method,
            'proof_of_payment' => $proofPath,
            'status' => 'pending',
            'submitted_at' => now()
        ]);

        // Update or create subscriber profile
        SubscriberProfile::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'company_name' => $request->company_name,
                'phone' => $request->phone,
                'address' => $request->address
            ]
        );

        return redirect()->route('subscription.status')
            ->with('success', 'Subscription application submitted successfully! It will be reviewed within 24-48 hours.');
    }

    public function applicationStatus()
    {
        $applications = auth()->user()->subscriptionApplications()
            ->with('plan')
            ->latest()
            ->paginate(10);

        $currentSubscription = auth()->user()->activeSubscription;

        return view('subscription.status', compact('applications', 'currentSubscription'));
    }

    public function downloadProof(SubscriptionApplication $application)
    {
        // Check if user owns this application
        if ($application->user_id !== auth()->id()) {
            abort(403);
        }

        if (!Storage::disk('public')->exists($application->proof_of_payment)) {
            abort(404);
        }

        return Storage::disk('public')->download($application->proof_of_payment);
    }

    public function cancelApplication(SubscriptionApplication $application)
    {
        if ($application->user_id !== auth()->id()) {
            abort(403);
        }

        if (!in_array($application->status, ['pending', 'under_review'])) {
            return redirect()->back()->with('error', 'Cannot cancel application in current status.');
        }

        $application->update(['status' => 'rejected', 'rejection_reason' => 'Cancelled by user']);

        return redirect()->route('subscription.status')
            ->with('success', 'Subscription application cancelled successfully.');
    }
}