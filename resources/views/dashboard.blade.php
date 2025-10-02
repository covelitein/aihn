<x-app-layout>
    <div class="container-fluid py-4">
        <!-- Welcome Section -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="d-flex align-items-center mb-3">
                    <div>
                        <h3 class="fw-bold mb-1 text-dark">Welcome, {{ Auth::user()->name }}</h3>
                        <p class="text-muted mb-0">Here's an overview of your subscription and available content.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Dashboard Cards -->
        <div class="row mb-5">
            <!-- Subscription Status -->
            <div class="col-lg-12 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 py-4">
                        <h5 class="card-title mb-0 fw-bold text-dark">
                            <i class="bi bi-credit-card-2-front me-2 text-primary"></i>
                            Subscription Status
                        </h5>
                    </div>
                    <div class="card-body">
                        @if(Auth::user()->is_subscription_active)
                            <div class="alert alert-success border-0 d-flex align-items-center mb-4">
                                <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                    <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="alert-heading mb-1 fw-bold">Active Subscription</h6>
                                    <p class="mb-0 small">
                                        @if(isset($currentPlan))
                                            Your <span class="fw-semibold">{{ $currentPlan->name }}</span> subscription is active and will expire in 
                                        @else
                                            Your subscription is active and will expire in 
                                        @endif
                                        <span class="fw-bold text-success">{{ round(Auth::user()->subscription_days_left) }} days</span>
                                        ({{ Auth::user()->subscription_expires_at?->format('M d, Y') }})
                                    </p>
                                </div>
                            </div>
                            <a href="{{ route('content.index') }}" class="btn btn-primary px-4 py-2">
                                <i class="bi bi-collection-play me-2"></i>
                                Browse Content
                            </a>
                        @elseif($pendingApplication)
                            <div class="alert alert-warning border-0 d-flex align-items-center mb-4">
                                <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-3">
                                    <i class="bi bi-hourglass-split text-warning fs-5"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="alert-heading mb-1 fw-bold">Pending Application</h6>
                                    <p class="mb-0 small">
                                        Your subscription application for <span class="fw-semibold">{{ $pendingApplication->plan->name }}</span> 
                                        is <span class="badge bg-warning text-dark">{{ ucfirst($pendingApplication->status) }}</span>
                                    </p>
                                </div>
                            </div>
                            <a href="{{ route('subscription.status') }}" class="btn btn-primary px-4 py-2">
                                <i class="bi bi-eye me-2"></i>
                                View Application Status
                            </a>
                        @else
                            <div class="alert alert-info border-0 d-flex align-items-center mb-4">
                                <div class="bg-info bg-opacity-10 rounded-circle p-2 me-3">
                                    <i class="bi bi-info-circle-fill text-info fs-5"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="alert-heading mb-1 fw-bold">No Active Subscription</h6>
                                    <p class="mb-0 small">Subscribe to access premium content and features</p>
                                </div>
                            </div>
                            <a href="{{ route('subscription.plans') }}" class="btn btn-primary px-4 py-2">
                                <i class="bi bi-star me-2"></i>
                                View Subscription Plans
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            @if(Auth::user()->is_subscription_active && !empty($contentStats))
                <!-- Available Content Stats -->
                <div class="col-lg-4 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white border-0 py-4">
                            <h5 class="card-title mb-0 fw-bold text-dark">
                                <i class="bi bi-folder2-open me-2 text-primary"></i>
                                Available Content
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex flex-column gap-3">
                                @foreach($contentStats as $type => $count)
                                    <div class="d-flex justify-content-between align-items-center p-3 rounded-3 bg-light bg-opacity-50">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                                <i class="bi bi-{{ $type === 'video' ? 'play-btn' : ($type === 'audio' ? 'music-note-beamed' : 'file-text') }} text-primary"></i>
                                            </div>
                                            <span class="fw-semibold text-dark">{{ ucfirst($type) }}s</span>
                                        </div>
                                        <span class="badge bg-primary fs-6 px-3 py-2">{{ $count }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Recent Content Section -->
        @if($recentContent->isNotEmpty())
            <div class="row mb-5">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold text-dark mb-0">
                            <i class="bi bi-clock-history me-2 text-primary"></i>
                            Recent Content
                        </h5>
                        <a href="{{ route('content.index') }}" class="btn btn-outline-primary btn-sm">
                            View All <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="row g-4">
                        @foreach($recentContent as $content)
                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <div class="card border-0 shadow-sm h-100 content-card">
                                    <div class="card-header bg-white border-0 py-3">
                                        <span class="badge rounded-pill px-3 py-2" style="background: {{ 
                                            $content->type === 'article' ? '#0d6efd' : 
                                            ($content->type === 'video' ? '#dc3545' : 
                                            ($content->type === 'audio' ? '#198754' : '#6c757d'))
                                        }}; color: white;">
                                            <i class="bi bi-{{ $content->type === 'video' ? 'play-btn' : ($content->type === 'audio' ? 'music-note-beamed' : 'file-text') }} me-1"></i>
                                            {{ ucfirst($content->type) }}
                                        </span>
                                    </div>
                                    <div class="card-body">
                                        <h6 class="card-title fw-bold text-dark mb-2">{{ $content->title }}</h6>
                                        <p class="card-text small text-muted mb-3">
                                            {{ Str::limit($content->description, 100) }}
                                        </p>
                                        <div class="d-flex align-items-center text-muted small">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            {{ $content->created_at?->format('M d, Y') }}
                                        </div>
                                    </div>
                                    <div class="card-footer bg-transparent border-0 pt-0">
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('content.show', $content) }}" class="btn btn-primary btn-sm flex-fill">
                                                <i class="bi bi-eye me-1"></i>
                                                View
                                            </a>
                                            @if(Auth::user()->is_subscription_active)
                                                <a href="{{ route('content.download', $content) }}" class="btn btn-outline-secondary btn-sm">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Available Plans Section -->
        @if($availablePlans && $availablePlans->isNotEmpty())
            <div class="row">
                <div class="col-12">
                    <h5 class="fw-bold text-dark mb-4">
                        <i class="bi bi-stars me-2 text-primary"></i>
                        Available Plans
                    </h5>
                    <div class="row g-4">
                        @foreach($availablePlans as $plan)
                            <div class="col-lg-4">
                                <div class="card border-0 shadow-sm h-100 plan-card">
                                    <div class="card-body p-4">
                                        <div class="text-center mb-4">
                                            <h5 class="card-title fw-bold text-dark mb-2">{{ $plan->name }}</h5>
                                            <div class="mb-3">
                                                <span class="h2 fw-bold text-primary mb-0">₱{{ number_format($plan->price, 2) }}</span>
                                                <span class="text-muted">/ {{ $plan->duration }}</span>
                                            </div>
                                        </div>
                                        
                                        @if(!empty($plan->features))
                                            <ul class="list-unstyled mb-4">
                                                @foreach($plan->features as $feature)
                                                    <li class="mb-2 d-flex align-items-center">
                                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                                        <span class="small">{{ $feature }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                        
                                        <a href="{{ route('subscription.apply', $plan) }}" class="btn btn-primary w-100 py-2">
                                            <i class="bi bi-cart-plus me-2"></i>
                                            Subscribe Now
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>

    <style>
        .card {
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
        }

        .content-card {
            transition: all 0.3s ease;
        }

        .content-card:hover {
            transform: translateY(-3px);
        }

        .plan-card {
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .plan-card:hover {
            border-color: #4361ee;
            transform: translateY(-5px);
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
            border-color: #4361ee;
            color: #4361ee;
        }

        .btn-outline-primary:hover {
            background: #4361ee;
            border-color: #4361ee;
            transform: translateY(-2px);
        }

        .badge {
            font-weight: 500;
            font-size: 0.75rem;
        }

        .alert {
            border-radius: 10px;
        }

        .bg-opacity-10 {
            background-opacity: 0.1;
        }

        /* Custom colors for content types */
        .content-type-article { background-color: #0d6efd; }
        .content-type-video { background-color: #dc3545; }
        .content-type-audio { background-color: #198754; }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .container-fluid {
                padding-left: 1rem;
                padding-right: 1rem;
            }
            
            .card-body {
                padding: 1.25rem;
            }
            
            .d-flex.align-items-center.mb-3 {
                flex-direction: column;
                text-align: center;
            }
            
            .bg-primary.bg-opacity-10.rounded-circle.p-3.me-3 {
                margin-right: 0 !important;
                margin-bottom: 1rem;
            }
        }

        @media (max-width: 576px) {
            .row.g-4 {
                margin-left: -0.5rem;
                margin-right: -0.5rem;
            }
            
            .col-xl-3, .col-lg-4, .col-md-6 {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }
            
            .card {
                margin-bottom: 1rem;
            }
        }
    </style>
</x-app-layout>