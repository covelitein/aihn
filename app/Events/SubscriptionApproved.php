<?php

namespace App\Events;

use App\Models\SubscriptionApplication;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubscriptionApproved
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public SubscriptionApplication $application)
    {
        //
    }
}