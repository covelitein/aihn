<?php

namespace App\Listeners;

use App\Events\SubscriptionApproved;
use Illuminate\Support\Facades\DB;

class HandleSubscriptionApproval
{
    /**
     * Handle the event.
     */
    public function handle(SubscriptionApproved $event): void
    {
        $application = $event->application;
        $user = $application->user;

        // Calculate expiration date based on plan duration
        $expiresAt = now()->addMonths($application->plan->duration_in_months);

        DB::transaction(function () use ($user, $application, $expiresAt) {
            // Update application
            $application->update([
                'status' => 'approved',
                'approved_at' => now(),
                'expires_at' => $expiresAt
            ]);

            // Update user's subscription status
            $user->update([
                'is_subscribed' => true,
                'subscription_status' => 'active',
                'subscription_expires_at' => $expiresAt,
                'current_subscription_id' => $application->id,
                'last_subscription_at' => now(),
                'total_subscriptions' => DB::raw('total_subscriptions + 1')
            ]);
        });
    }
}