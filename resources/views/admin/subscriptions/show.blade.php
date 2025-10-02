@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- Application Details -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Application Details</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Back to Applications
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Plan Information -->
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Subscription Plan</h6>
                            <p class="h5">{{ $application->plan->name }}</p>
                            <p class="text-muted mb-0">${{ number_format($application->plan->price, 2) }} / {{ $application->plan->duration }}</p>
                        </div>

                        <!-- Payment Information -->
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Payment Details</h6>
                            <p class="mb-1">Amount Paid: <strong>${{ number_format($application->amount_paid, 2) }}</strong></p>
                            <p class="mb-0">Transaction ID: <strong>{{ $application->transaction_id ?? 'Not provided' }}</strong></p>
                        </div>

                        <!-- Timeline -->
                        <div class="col-12">
                            <h6 class="text-muted mb-3">Application Timeline</h6>
                            <div class="timeline">
                                <div class="timeline-item">
                                    <i class="bi bi-circle-fill text-primary"></i>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Submitted</h6>
                                        <p class="small text-muted mb-0">{{ $application->submitted_at->format('M d, Y h:i A') }}</p>
                                    </div>
                                </div>

                                @if($application->reviewed_at)
                                <div class="timeline-item">
                                    <i class="bi bi-circle-fill text-info"></i>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Under Review</h6>
                                        <p class="small text-muted mb-0">{{ $application->reviewed_at->format('M d, Y h:i A') }}</p>
                                    </div>
                                </div>
                                @endif

                                @if($application->approved_at)
                                <div class="timeline-item">
                                    <i class="bi bi-circle-fill text-success"></i>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Approved</h6>
                                        <p class="small text-muted mb-0">{{ $application->approved_at->format('M d, Y h:i A') }}</p>
                                        @if($application->expires_at)
                                            <p class="small text-muted mb-0">
                                                Expires: {{ $application->expires_at->format('M d, Y') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="col-12 mt-4">
                            <hr>
                            <div class="d-flex gap-2">
                                @if($application->status === 'pending')
                                    <form action="{{ route('admin.subscriptions.update-status', $application) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="under_review">
                                        <button type="submit" class="btn btn-info">
                                            <i class="bi bi-eye me-1"></i> Mark Under Review
                                        </button>
                                    </form>
                                @endif

                                @if(in_array($application->status, ['pending', 'under_review']))
                                    <form action="{{ route('admin.subscriptions.update-status', $application) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to approve this application?')">
                                            <i class="bi bi-check-circle me-1"></i> Approve
                                        </button>
                                    </form>

                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                        <i class="bi bi-x-circle me-1"></i> Reject
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admin Notes -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Admin Notes</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.subscriptions.update-status', $application) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <textarea class="form-control" name="admin_notes" rows="3" placeholder="Add private notes about this application...">{{ $application->admin_notes }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            Save Notes
                        </button>
                    </form>

                    @if($application->rejection_reason)
                        <div class="mt-4">
                            <h6 class="text-danger mb-2">Rejection Reason</h6>
                            <p class="mb-0">{{ $application->rejection_reason }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Subscriber Information -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Subscriber Information</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-lg">
                                <span class="avatar-text rounded-circle">
                                    {{ substr($application->user->name, 0, 2) }}
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">{{ $application->user->name }}</h6>
                            <p class="mb-0 text-muted">{{ $application->user->email }}</p>
                        </div>
                    </div>

                    @if($application->user->profile)
                        <div class="mb-4">
                            <h6 class="text-muted mb-2">Contact Details</h6>
                            @if($application->user->profile->company_name)
                                <p class="mb-2">
                                    <strong>Company:</strong> {{ $application->user->profile->company_name }}
                                </p>
                            @endif
                            <p class="mb-2">
                                <strong>Phone:</strong> {{ $application->user->profile->phone }}
                            </p>
                            <p class="mb-0">
                                <strong>Address:</strong><br>
                                {{ $application->user->profile->full_address }}
                            </p>
                        </div>
                    @endif

                    <!-- Subscription History -->
                    <div class="mb-0">
                        <h6 class="text-muted mb-2">Subscription History</h6>
                        @forelse($application->user->subscriptionApplications->sortByDesc('created_at') as $history)
                            <div class="mb-2">
                                <span class="badge bg-{{ $history->status_color }} me-1">{{ ucfirst($history->status) }}</span>
                                {{ $history->plan->name }} - {{ $history->created_at->format('M d, Y') }}
                            </div>
                        @empty
                            <p class="text-muted mb-0">No subscription history</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Proof of Payment -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Proof of Payment</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.subscriptions.view-proof', $application) }}" 
                       class="btn btn-outline-primary w-100" 
                       target="_blank">
                        <i class="bi bi-file-earmark-text me-1"></i>
                        View Document
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.subscriptions.update-status', $application) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="rejected">
                
                <div class="modal-header">
                    <h5 class="modal-title">Reject Application</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Reason for Rejection</label>
                        <textarea class="form-control @error('rejection_reason') is-invalid @enderror" 
                                name="rejection_reason" 
                                rows="3" 
                                required></textarea>
                        @error('rejection_reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Application</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 1.5rem;
}
.timeline:before {
    content: '';
    position: absolute;
    left: 0.5rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}
.timeline-item {
    position: relative;
    padding-bottom: 1.5rem;
}
.timeline-item:last-child {
    padding-bottom: 0;
}
.timeline-item i {
    position: absolute;
    left: -1.25rem;
    top: 0.25rem;
    font-size: 0.5rem;
}
.timeline-content {
    padding-left: 0.5rem;
}
.avatar {
    width: 48px;
    height: 48px;
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
}
</style>
@endpush

@endsection