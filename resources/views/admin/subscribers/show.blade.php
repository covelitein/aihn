@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- Subscriber Details -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Subscriber Information</h5>
                    <a href="{{ route('admin.subscribers.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Back to Subscribers
                    </a>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="avatar avatar-xl mx-auto mb-3">
                            <span class="avatar-text rounded-circle">
                                {{ substr($subscriber->name, 0, 2) }}
                            </span>
                        </div>
                        <h5 class="mb-1">{{ $subscriber->name }}</h5>
                        <p class="text-muted mb-0">{{ $subscriber->email }}</p>
                    </div>

                    <!-- Company Information -->
                    @if($subscriber->profile)
                        <div class="mb-4">
                            <h6 class="text-muted mb-3">Company Details</h6>
                            <dl class="row mb-0">
                                @if($subscriber->profile->company_name)
                                    <dt class="col-sm-4">Company</dt>
                                    <dd class="col-sm-8">{{ $subscriber->profile->company_name }}</dd>
                                @endif

                                @if($subscriber->profile->phone)
                                    <dt class="col-sm-4">Phone</dt>
                                    <dd class="col-sm-8">{{ $subscriber->profile->phone }}</dd>
                                @endif

                                @if($subscriber->profile->website)
                                    <dt class="col-sm-4">Website</dt>
                                    <dd class="col-sm-8">
                                        <a href="{{ $subscriber->profile->website }}" target="_blank">
                                            {{ $subscriber->profile->website }}
                                        </a>
                                    </dd>
                                @endif

                                @if($subscriber->profile->full_address)
                                    <dt class="col-sm-4">Address</dt>
                                    <dd class="col-sm-8">{{ $subscriber->profile->full_address }}</dd>
                                @endif
                            </dl>
                        </div>
                    @endif

                    <!-- Account Stats -->
                    <div class="mb-4">
                        <h6 class="text-muted mb-3">Account Statistics</h6>
                        <dl class="row mb-0">
                            <dt class="col-sm-4">Joined</dt>
                            <dd class="col-sm-8">{{ $subscriber->created_at->format('M d, Y') }}</dd>

                            <dt class="col-sm-4">Status</dt>
                            <dd class="col-sm-8">
                                @if($subscriber->is_subscribed)
                                    <span class="badge bg-success">Active</span>
                                @elseif($subscriber->hasPendingApplication())
                                    <span class="badge bg-warning">Pending</span>
                                @else
                                    <span class="badge bg-danger">Expired</span>
                                @endif
                            </dd>

                            @if($subscriber->subscription_expires_at)
                                <dt class="col-sm-4">Expires</dt>
                                <dd class="col-sm-8">
                                    <span class="{{ $subscriber->subscription_expires_at->isPast() ? 'text-danger' : 'text-success' }}">
                                        {{ $subscriber->subscription_expires_at->format('M d, Y') }}
                                    </span>
                                </dd>
                            @endif
                        </dl>
                    </div>

                    <!-- Actions -->
                    <div class="d-grid gap-2">
                        @if(!$subscriber->is_subscribed)
                            <form action="{{ route('admin.subscribers.update-subscription', $subscriber) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="action" value="activate">
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="bi bi-check-circle me-1"></i> Activate Subscription
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.subscribers.update-subscription', $subscriber) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="action" value="deactivate">
                                <button type="submit" class="btn btn-warning w-100">
                                    <i class="bi bi-pause-circle me-1"></i> Deactivate Subscription
                                </button>
                            </form>
                        @endif

                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#extendModal">
                            <i class="bi bi-calendar-plus me-1"></i> Extend Subscription
                        </button>

                        <form action="{{ route('admin.subscribers.destroy', $subscriber) }}" 
                              method="POST"
                              onsubmit="return confirm('Are you sure you want to delete this subscriber?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="bi bi-trash me-1"></i> Delete Subscriber
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            @if($subscriber->profile && $subscriber->profile->notes)
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Notes</h5>
                    </div>
                    <div class="card-body">
                        {{ $subscriber->profile->notes }}
                    </div>
                </div>
            @endif
        </div>

        <!-- Subscription History -->
        <div class="col-lg-8">
            <!-- Current Plan -->
            @if($subscriber->currentSubscription)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Current Plan</h5>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <h6>{{ $subscriber->currentSubscription->plan->name }}</h6>
                                <p class="text-muted mb-0">
                                    ${{ number_format($subscriber->currentSubscription->plan->price, 2) }} / 
                                    {{ $subscriber->currentSubscription->plan->duration }}
                                </p>
                            </div>
                            <div class="col-md-4">
                                <div class="text-muted small">Start Date</div>
                                <strong>{{ $subscriber->currentSubscription->approved_at->format('M d, Y') }}</strong>
                            </div>
                            <div class="col-md-4">
                                <div class="text-muted small">Expiry Date</div>
                                <strong>{{ $subscriber->subscription_expires_at->format('M d, Y') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Applications History -->
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Subscription History</h5>
                    <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Back to Applications
                    </a>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @forelse($subscriber->subscriptionApplications->sortByDesc('created_at') as $application)
                            <div class="timeline-item">
                                <div class="timeline-badge bg-{{ $application->status_color }}">
                                    <i class="bi bi-circle-fill"></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h6 class="mb-0">{{ $application->plan->name }}</h6>
                                        <span class="badge bg-{{ $application->status_color }}">
                                            {{ ucfirst($application->status) }}
                                        </span>
                                    </div>
                                    <p class="text-muted mb-2">
                                        Amount: ${{ number_format($application->amount_paid, 2) }}
                                        @if($application->transaction_id)
                                            <br>Transaction ID: {{ $application->transaction_id }}
                                        @endif
                                    </p>
                                    <div class="small text-muted">
                                        <i class="bi bi-clock me-1"></i>
                                        Submitted {{ $application->created_at->format('M d, Y h:i A') }}

                                        @if($application->approved_at)
                                            <br>
                                            <i class="bi bi-check-circle me-1"></i>
                                            Approved {{ $application->approved_at->format('M d, Y h:i A') }}
                                        @endif

                                        @if($application->rejection_reason)
                                            <div class="text-danger mt-1">
                                                <i class="bi bi-x-circle me-1"></i>
                                                {{ $application->rejection_reason }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted text-center py-4 mb-0">
                                No subscription history available
                            </p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Extend Subscription Modal -->
<div class="modal fade" id="extendModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.subscribers.update-subscription', $subscriber) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="action" value="extend">
                
                <div class="modal-header">
                    <h5 class="modal-title">Extend Subscription</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="extension_days" class="form-label">Extension Period (Days)</label>
                        <input type="number" 
                               class="form-control" 
                               name="extension_days" 
                               min="1" 
                               max="365" 
                               value="30" 
                               required>
                        <div class="form-text">
                            Enter the number of days to extend the subscription (1-365 days)
                        </div>
                    </div>

                    @if($subscriber->subscription_expires_at)
                        <p class="text-muted">
                            Current expiry: {{ $subscriber->subscription_expires_at->format('M d, Y') }}
                        </p>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Extend Subscription</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
.avatar-xl {
    width: 96px;
    height: 96px;
}
.avatar-text {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
    background-color: #0d6efd;
    color: white;
    font-weight: bold;
    font-size: 2rem;
}

.timeline {
    position: relative;
    padding: 0;
    list-style: none;
}

.timeline:before {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    left: 1rem;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    padding-left: 3rem;
    padding-bottom: 2rem;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

.timeline-badge {
    position: absolute;
    left: 0;
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    text-align: center;
    line-height: 2rem;
    color: white;
}

.timeline-badge i {
    font-size: 1rem;
}

.timeline-content {
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 0.25rem;
}
</style>
@endpush

@endsection