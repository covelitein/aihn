@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Current Subscription Status -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary bg-gradient text-white py-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-credit-card-2-front fs-4 me-3"></i>
                        <h4 class="mb-0 fw-semibold">Subscription Status</h4>
                    </div>
                </div>
                <div class="card-body p-4">
                    @if(auth()->user()->is_subscription_active)
                        <div class="alert alert-success border-0 d-flex align-items-start">
                            <div class="flex-shrink-0">
                                <i class="bi bi-check-circle-fill text-success fs-4 me-3"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="alert-heading fw-semibold mb-2">Active Subscription</h5>
                                <p class="mb-2">
                                    Your subscription is currently active and will expire in 
                                    <span class="fw-bold text-success">{{ round(auth()->user()->subscription_days_left) }} days</span>
                                    ({{ auth()->user()->subscription_expires_at->format('F d, Y') }}).
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning border-0 d-flex align-items-start">
                            <div class="flex-shrink-0">
                                <i class="bi bi-exclamation-triangle-fill text-warning fs-4 me-3"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="alert-heading fw-semibold mb-2">No Active Subscription</h5>
                                <p class="mb-2">
                                    @if(auth()->user()->hasPendingApplication())
                                        You have a pending subscription application. Please check the status below.
                                    @else
                                        You don't have an active subscription to access premium content.
                                    @endif
                                </p>
                                @unless(auth()->user()->hasPendingApplication())
                                    <a href="{{ route('subscription.plans') }}" class="btn btn-primary btn-sm mt-2">
                                        <i class="bi bi-eye me-1"></i>View Subscription Plans
                                    </a>
                                @endunless
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Application History -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-clock-history fs-4 me-3 text-muted"></i>
                            <h4 class="mb-0 fw-semibold">Application History</h4>
                        </div>
                        @if(!$applications->isEmpty())
                            <span class="badge bg-primary rounded-pill">{{ $applications->total() }} application(s)</span>
                        @endif
                    </div>
                </div>
                <div class="card-body p-4">
                    @if($applications->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-inbox fs-1 text-muted mb-3"></i>
                            <h5 class="text-muted mb-2">No Applications Yet</h5>
                            <p class="text-muted mb-3">You haven't submitted any subscription applications.</p>
                            <a href="{{ route('subscription.plans') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-1"></i>Browse Plans
                            </a>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($applications as $application)
                                <div class="list-group-item px-0 py-3 border-bottom">
                                    <x-subscription.application-card :application="$application" />
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if($applications->hasPages())
                            <div class="mt-4 d-flex justify-content-center">
                                <nav aria-label="Application history navigation">
                                    {{ $applications->links() }}
                                </nav>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border-radius: 12px;
}

.card-header {
    border-radius: 12px 12px 0 0 !important;
}

.alert {
    border-radius: 10px;
    padding: 1.25rem;
}

.bg-gradient {
    background: linear-gradient(135deg, #4361ee 0%, #3a56d4 100%) !important;
}

.progress {
    border-radius: 10px;
}

.progress-bar {
    border-radius: 10px;
}

.list-group-item {
    border: none;
    border-bottom: 1px solid #e9ecef !important;
}

.list-group-item:last-child {
    border-bottom: none !important;
}

.btn {
    border-radius: 8px;
    font-weight: 500;
}

.btn-primary {
    background: linear-gradient(135deg, #4361ee, #3a56d4);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #3a56d4, #2f46b8);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
}

.badge {
    font-weight: 500;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .card-body {
        padding: 1.5rem !important;
    }
    
    .alert {
        padding: 1rem;
    }
    
    .d-flex.align-items-center.justify-content-between {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .d-flex.align-items-center.justify-content-between .badge {
        align-self: flex-end;
    }
}

@media (max-width: 576px) {
    .alert .d-flex.align-items-start {
        flex-direction: column;
        text-align: center;
    }
    
    .alert .flex-shrink-0 {
        margin-bottom: 1rem;
    }
    
    .text-center.py-5 {
        padding: 2rem 0 !important;
    }
}
</style>
@endsection