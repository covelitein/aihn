@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header Section -->
            <div class="text-center mb-5">
                <h2 class="fw-bold text-dark mb-3">Choose Your Plan</h2>
                <p class="text-muted fs-5">Select the subscription that best fits your needs</p>
            </div>

            <!-- Plans Grid -->
            @if($plans->isEmpty())
                <div class="alert alert-info text-center py-4">
                    <i class="bi bi-info-circle fs-4 me-2"></i>
                    No subscription plans are currently available.
                </div>
            @else
                <div class="row g-4">
                    @foreach($plans as $plan)
                        <div class="col-lg-4 col-md-6">
                            <div class="card border-0 shadow-sm h-100 plan-card">
                                <div class="card-body p-4 d-flex flex-column">
                                    <!-- Plan Header -->
                                    <div class="text-center mb-4">
                                        <h5 class="card-title fw-bold text-dark mb-2">{{ $plan->name }}</h5>
                                        @if($plan->description)
                                            <p class="card-text text-muted small mb-3">{{ $plan->description }}</p>
                                        @endif
                                    </div>

                                    <!-- Price Section -->
                                    <div class="text-center mb-4">
                                        <div class="d-flex justify-content-center align-items-baseline mb-2">
                                            <span class="h3 fw-bold text-primary">${{ number_format($plan->price, 2) }}</span>
                                        </div>
                                    </div>

                                    <!-- Features List -->
                                    @if(!empty($plan->features))
                                        <div class="mb-4 flex-grow-1">
                                            <ul class="list-unstyled mb-0">
                                                @foreach($plan->features as $feature)
                                                    <li class="mb-2 d-flex align-items-start">
                                                        <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                                                        <span class="small">{{ $feature }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <!-- Subscribe Button -->
                                    <div class="mt-auto">
                                        @auth
                                            <a href="{{ route('subscription.apply', $plan) }}" class="btn btn-primary w-100 py-2">
                                                <i class="bi bi-arrow-right-circle me-2"></i>
                                                Subscribe Now
                                            </a>
                                        @else
                                            <a href="{{ route('login') }}" class="btn btn-outline-primary w-100 py-2">
                                                <i class="bi bi-box-arrow-in-right me-2"></i>
                                                Login to Subscribe
                                            </a>
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.plan-card {
    border-radius: 12px;
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
}

.plan-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
    border-color: #4361ee;
}

.card {
    border-radius: 12px;
}

.btn {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(135deg, #4361ee, #3a56d4);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #3a56d4, #2f46b8);
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
}

.btn-outline-primary {
    border: 2px solid #4361ee;
    color: #4361ee;
    font-weight: 500;
}

.btn-outline-primary:hover {
    background: #4361ee;
    border-color: #4361ee;
    transform: translateY(-2px);
}

.badge {
    border-radius: 20px;
    font-weight: 500;
}

.bg-opacity-10 {
    background-color: rgba(13, 110, 253, 0.1) !important;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .text-center.mb-5 h2 {
        font-size: 1.75rem;
    }
    
    .text-center.mb-5 p {
        font-size: 1rem;
    }
}

@media (max-width: 576px) {
    .col-lg-4, .col-md-6 {
        margin-bottom: 1.5rem;
    }
    
    .card-body {
        padding: 1.5rem !important;
    }
}
</style>
@endsection