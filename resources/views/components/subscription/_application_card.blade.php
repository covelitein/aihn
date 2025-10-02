@props(['application'])

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center bg-light py-2">
        <span class="badge" style="background-color: {{ 
            $application->status === 'approved' ? '#198754' : 
            ($application->status === 'rejected' ? '#dc3545' : 
            ($application->status === 'expired' ? '#6c757d' : 
            ($application->status === 'under_review' ? '#fd7e14' : '#0d6efd')))
        }}">
            {{ ucfirst($application->status) }}
        </span>
        <small class="text-muted">{{ $application->created_at->format('M d, Y') }}</small>
    </div>
    <div class="card-body">
        <!-- Plan Info -->
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <h5 class="card-title mb-1">{{ $application->plan->name }}</h5>
                <p class="text-muted small mb-0">
                    {{ $application->plan->duration }} ({{ $application->plan->duration_in_months }} months)
                </p>
            </div>
            <h5 class="mb-0">₱{{ number_format($application->amount_paid, 2) }}</h5>
        </div>

        <!-- Payment Details -->
        <div class="mb-3">
            <div class="d-flex justify-content-between text-muted small mb-1">
                <span>Payment Method:</span>
                <span class="text-uppercase">{{ str_replace('_', ' ', $application->payment_method) }}</span>
            </div>
            <div class="d-flex justify-content-between text-muted small">
                <span>Transaction ID:</span>
                <span>{{ $application->transaction_id ?? 'N/A' }}</span>
            </div>
        </div>

        <!-- Status Info -->
        @if($application->status === 'rejected')
            <div class="alert alert-danger mb-3">
                <strong>Rejection Reason:</strong><br>
                {{ $application->rejection_reason }}
            </div>
        @elseif($application->status === 'approved')
            <div class="alert alert-success mb-3">
                <i class="bi bi-check-circle me-2"></i>
                Approved on {{ $application->approved_at->format('M d, Y') }}<br>
                <small>Expires on {{ $application->expires_at->format('M d, Y') }}</small>
            </div>
        @elseif($application->status === 'under_review')
            <div class="alert alert-warning mb-3">
                <i class="bi bi-hourglass me-2"></i>
                Your application is being reviewed
            </div>
        @elseif($application->status === 'expired')
            <div class="alert alert-secondary mb-3">
                <i class="bi bi-clock-history me-2"></i>
                Expired on {{ $application->expires_at->format('M d, Y') }}
            </div>
        @else
            <div class="alert alert-info mb-3">
                <i class="bi bi-info-circle me-2"></i>
                Awaiting admin review
            </div>
        @endif

        <!-- Actions -->
        <div class="d-flex gap-2">
            @if($application->proof_of_payment && $application->status !== 'approved')
                <a href="{{ route('subscription.proof.download', $application) }}" 
                   class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-download me-1"></i>
                    View Payment Proof
                </a>
            @endif

            @if($application->status === 'pending')
                <form action="{{ route('subscription.cancel', $application) }}" 
                      method="POST"
                      onsubmit="return confirm('Are you sure you want to cancel this application?')">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-x-circle me-1"></i>
                        Cancel Application
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>