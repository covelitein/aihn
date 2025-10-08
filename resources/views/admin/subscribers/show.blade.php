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
                        <!-- Subscription management moved to admin settings; simple delete action kept -->
                        <form id="delete-subscriber-{{ $subscriber->id }}" action="{{ route('admin.subscribers.destroy', $subscriber) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-danger w-100" onclick="AppUI.confirm('Are you sure you want to delete this subscriber?', 'Confirm Deletion').then(function(ok){ if(ok) document.getElementById('delete-subscriber-{{ $subscriber->id }}').submit(); });">
                                <i class="bi bi-trash me-1"></i> Delete Subscriber
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            @can('superadmin')
                <!-- Mentor Assignment -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Mentor</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.subscribers.assign-mentor', $subscriber) }}" method="POST" class="row g-2 align-items-end">
                            @csrf
                            <div class="col-md-8">
                                <label class="form-label">Assign Mentor</label>
                                <select name="mentor_id" class="form-select">
                                    @foreach($mentors as $mentor)
                                        <option value="{{ $mentor->id }}">{{ $mentor->name }} ({{ $mentor->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-primary w-100">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endcan

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

            <!-- Subscription sections removed; subscription/application management moved to admin workflows -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="mb-0">Access & Activity</h5>
                    <p class="text-muted">Subscription plans and applications have been removed from the user interface. Manage user access via admin settings or contact support.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- subscription extension modal removed -->

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