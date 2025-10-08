@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1">Application Details</h4>
                    <div class="text-muted">Reviewing subscription application</div>
                </div>
                <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>
                    Back to Applications
                </a>
            </div>

            <!-- Status Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="mb-1">Current Status</h5>
                            <span class="badge" style="background: {{ 
                                $application->status === 'approved' ? '#198754' : 
                                ($application->status === 'rejected' ? '#dc3545' : 
                                ($application->status === 'expired' ? '#6c757d' : 
                                ($application->status === 'under_review' ? '#fd7e14' : '#0d6efd')))
                            }}">
                                {{ ucfirst($application->status) }}
                            </span>
                        </div>
                        <div class="col-auto">
                            <div class="dropdown">
                                <button class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                                    Update Status
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <form action="{{ route('admin.subscriptions.update-status', $application) }}"
                                              method="POST"
                                              class="dropdown-item-form">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="under_review">
                                            <button type="submit" class="dropdown-item">
                                                <i class="bi bi-hourglass me-2"></i>
                                                Mark as Under Review
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <button type="button" 
                                               class="dropdown-item text-success"
                                               data-bs-toggle="modal"
                                               data-bs-target="#approveModal">
                                            <i class="bi bi-check-circle me-2"></i>
                                            Approve Application
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button"
                                               class="dropdown-item text-danger"
                                               data-bs-toggle="modal"
                                               data-bs-target="#rejectModal">
                                            <i class="bi bi-x-circle me-2"></i>
                                            Reject Application
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Application Details -->
            <div class="card mb-4">
                <div class="card-header bg-light py-3">
                    <h5 class="mb-0">Application Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <!-- Plan Details -->
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Plan Details</h6>
                            <p class="mb-1">
                                <strong>Plan:</strong> {{ $application->plan->name }}
                            </p>
                            <p class="mb-1">
                                <strong>Duration:</strong> {{ ucfirst($application->plan->duration) }}
                            </p>
                            <p class="mb-1">
                                <strong>Price:</strong> ₱{{ number_format($application->plan->price, 2) }}
                            </p>
                        </div>

                        <!-- Payment Details -->
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Payment Details</h6>
                            <p class="mb-1">
                                <strong>Amount Paid:</strong> ₱{{ number_format($application->amount_paid, 2) }}
                            </p>
                            <p class="mb-1">
                                <strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $application->payment_method)) }}
                            </p>
                            <p class="mb-1">
                                <strong>Transaction ID:</strong> {{ $application->transaction_id ?? 'N/A' }}
                            </p>
                            @if($application->proof_of_payment)
                                <a href="{{ route('admin.subscriptions.view-proof', $application) }}" 
                                   class="btn btn-sm btn-outline-primary mt-2"
                                   target="_blank">
                                    <i class="bi bi-file-earmark me-1"></i>
                                    View Payment Proof
                                </a>
                            @endif
                        </div>

                        <!-- Subscriber Details -->
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Subscriber Details</h6>
                            <p class="mb-1">
                                <strong>Name:</strong> {{ $application->user->name }}
                            </p>
                            <p class="mb-1">
                                <strong>Email:</strong> {{ $application->user->email }}
                            </p>
                            @if($application->company_name)
                                <p class="mb-1">
                                    <strong>Company:</strong> {{ $application->company_name }}
                                </p>
                            @endif
                            @if($application->phone)
                                <p class="mb-1">
                                    <strong>Phone:</strong> {{ $application->phone }}
                                </p>
                            @endif
                            @if($application->address)
                                <p class="mb-1">
                                    <strong>Address:</strong> {{ $application->address }}
                                </p>
                            @endif
                        </div>

                        <!-- Timeline -->
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Application Timeline</h6>
                            <div class="timeline">
                                <div class="timeline-item">
                                    <i class="bi bi-circle-fill text-primary"></i>
                                    <div class="ms-3">
                                        <div class="fw-bold">Application Submitted</div>
                                        <small class="text-muted">{{ $application->created_at->format('M d, Y h:i A') }}</small>
                                    </div>
                                </div>
                                @if($application->reviewed_at)
                                    <div class="timeline-item">
                                        <i class="bi bi-circle-fill text-warning"></i>
                                        <div class="ms-3">
                                            <div class="fw-bold">Application Reviewed</div>
                                            <small class="text-muted">{{ $application->reviewed_at->format('M d, Y h:i A') }}</small>
                                        </div>
                                    </div>
                                @endif
                                @if($application->approved_at)
                                    <div class="timeline-item">
                                        <i class="bi bi-circle-fill text-success"></i>
                                        <div class="ms-3">
                                            <div class="fw-bold">Application Approved</div>
                                            <small class="text-muted">{{ $application->approved_at->format('M d, Y h:i A') }}</small>
                                        </div>
                                    </div>
                                @endif
                                @if($application->status === 'rejected')
                                    <div class="timeline-item">
                                        <i class="bi bi-circle-fill text-danger"></i>
                                        <div class="ms-3">
                                            <div class="fw-bold">Application Rejected</div>
                                            <small class="text-muted">{{ $application->updated_at->format('M d, Y h:i A') }}</small>
                                            @if($application->rejection_reason)
                                                <div class="mt-2 text-danger">
                                                    {{ $application->rejection_reason }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Admin Notes -->
                        <div class="col-12">
                            <hr>
                            <h6 class="text-muted mb-3">Admin Notes</h6>
                            @if($application->admin_notes)
                                <p class="mb-3">{{ $application->admin_notes }}</p>
                            @endif
                            <button type="button" 
                                    class="btn btn-outline-secondary btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#notesModal">
                                <i class="bi bi-pencil me-1"></i>
                                Update Notes
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">Danger Zone</h5>
                </div>
                <div class="card-body">
                    <p>Delete this application permanently. This action cannot be undone.</p>
                    <form action="{{ route('admin.subscriptions.destroy', $application) }}" 
                          method="POST"
                          onsubmit="return AppUI.confirm('Are you sure you want to delete this application? This action cannot be undone.', 'Confirm Deletion')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash me-1"></i>
                            Delete Application
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.subscriptions.update-status', $application) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="approved">
                
                <div class="modal-header">
                    <h5 class="modal-title">Approve Application</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to approve this application?</p>
                    <div class="form-text">
                        This will grant the user access to the subscription plan.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i>
                        Approve
                    </button>
                </div>
            </form>
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
                        <label class="form-label">Rejection Reason</label>
                        <textarea name="rejection_reason" 
                                  class="form-control" 
                                  rows="3" 
                                  required></textarea>
                        <div class="form-text">
                            Please provide a reason for rejection. This will be visible to the user.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-circle me-1"></i>
                        Reject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Notes Modal -->
<div class="modal fade" id="notesModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.subscriptions.update-status', $application) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="modal-header">
                    <h5 class="modal-title">Update Admin Notes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="admin_notes" 
                                  class="form-control" 
                                  rows="3">{{ $application->admin_notes }}</textarea>
                        <div class="form-text">
                            These notes are only visible to administrators.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>
                        Save Notes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 1rem;
}

.timeline-item {
    position: relative;
    padding-bottom: 1.5rem;
    padding-left: 1.5rem;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: 0.35rem;
    top: 1rem;
    bottom: 0;
    width: 1px;
    background: #dee2e6;
}

.timeline-item i {
    position: absolute;
    left: 0;
    font-size: 0.5rem;
    transform: translateX(-50%);
}

.dropdown-item-form {
    display: block;
    width: 100%;
    padding: 0;
}

.dropdown-item-form .dropdown-item {
    border: 0;
    display: block;
    width: 100%;
    padding: .25rem 1rem;
    clear: both;
    font-weight: 400;
    color: #212529;
    text-align: inherit;
    text-decoration: none;
    white-space: nowrap;
    background-color: transparent;
    border: 0;
}

.dropdown-item-form .dropdown-item:hover {
    color: #1e2125;
    background-color: #e9ecef;
}
</style>
@endsection