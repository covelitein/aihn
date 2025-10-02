<?php

namespace App\View\Components\Subscription;

use Illuminate\View\Component;
use App\Models\SubscriptionApplication;

class ApplicationCard extends Component
{
    public $application;

    public function __construct(SubscriptionApplication $application)
    {
        $this->application = $application;
    }

    public function render()
    {
        return view('components.subscription._application_card');
    }
}