@props(['application'])

<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">{{ $application->plan->name }}</h5>
        @switch($application->status)
            @case('pending')
                <span class="badge bg-warning">Pending Review</span>
                @break
            @case('under_review')
                <span class="badge bg-info">Under Review</span>
                @break
            @case('approved')
                <span class="badge bg-success">Approved</span>
                @break
            @case('rejected')
                <span class="badge bg-danger">Rejected</span>
                @break
            @case('expired')
                <span class="badge bg-secondary">Expired</span>
                @break
        @endswitch
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p class="mb-1">
                    <strong>Amount Paid:</strong> ${{ number_format($application->amount_paid, 2) }}
                </p>
                <p class="mb-1">
                    <strong>Submitted:</strong> {{ $application->created_at->format('M d, Y') }}
                </p>
                @if($application->transaction_id)
                    <p class="mb-1">
                        <strong>Transaction ID:</strong> {{ $application->transaction_id }}
                    </p>
                @endif
            </div>
            <div class="col-md-6">
                @if($application->approved_at)
                    <p class="mb-1">
                        <strong>Approved:</strong> {{ $application->approved_at->format('M d, Y') }}
                    </p>
                @endif
                @if($application->expires_at)
                    <p class="mb-1">
                        <strong>Expires:</strong> {{ $application->expires_at->format('M d, Y') }}
                    </p>
                @endif
                @if($application->rejection_reason)
                    <p class="mb-1">
                        <strong>Rejection Reason:</strong> {{ $application->rejection_reason }}
                    </p>
                @endif
            </div>
        </div>

        <div class="mt-3">
            <a href="{{ route('subscription.proof.download', $application) }}" 
               class="btn btn-sm btn-outline-primary">
                <i class="bi bi-download"></i> Download Proof of Payment
            </a>

            @if(in_array($application->status, ['pending', 'under_review']))
                <form action="{{ route('subscription.cancel', $application) }}" 
                      method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-danger" 
                            onclick="return confirm('Are you sure you want to cancel this application?')">
                        <i class="bi bi-x-circle"></i> Cancel Application
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>