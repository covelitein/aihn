@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">Subscription Applications</h4>
                    <div class="text-muted">Manage and review subscription applications</div>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" 
                            class="btn btn-outline-primary" 
                            data-bs-toggle="modal" 
                            data-bs-target="#bulkActionModal">
                        <i class="bi bi-list-check me-1"></i>
                        Bulk Actions
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" 
                                type="button" 
                                data-bs-toggle="dropdown">
                            <i class="bi bi-funnel me-1"></i>
                            Filter
                        </button>
                        <div class="dropdown-menu dropdown-menu-end p-3" style="min-width: 250px;">
                            <form action="{{ route('admin.subscriptions.index') }}" method="GET">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="">All Status</option>
                                        <option value="pending" @selected(request('status') == 'pending')>Pending</option>
                                        <option value="under_review" @selected(request('status') == 'under_review')>Under Review</option>
                                        <option value="approved" @selected(request('status') == 'approved')>Approved</option>
                                        <option value="rejected" @selected(request('status') == 'rejected')>Rejected</option>
                                        <option value="expired" @selected(request('status') == 'expired')>Expired</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Plan</label>
                                    <select name="plan_id" class="form-select">
                                        <option value="">All Plans</option>
                                        @foreach($plans as $plan)
                                            <option value="{{ $plan->id }}" @selected(request('plan_id') == $plan->id)>
                                                {{ $plan->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Date Range</label>
                                    <input type="date" 
                                           name="date_from" 
                                           class="form-control mb-2"
                                           value="{{ request('date_from') }}"
                                           placeholder="From">
                                    <input type="date" 
                                           name="date_to" 
                                           class="form-control"
                                           value="{{ request('date_to') }}"
                                           placeholder="To">
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Applications Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th style="width: 40px;">
                            <div class="form-check">
                                <input class="form-check-input select-all" type="checkbox">
                            </div>
                        </th>
                        <th>User</th>
                        <th>Plan</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $application)
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input application-checkbox" 
                                           type="checkbox"
                                           name="applications[]"
                                           value="{{ $application->id }}">
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div>
                                        <div class="fw-bold">{{ $application->user->name }}</div>
                                        <div class="small text-muted">{{ $application->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                {{ $application->plan->name }}<br>
                                <small class="text-muted">{{ $application->plan->duration }}</small>
                            </td>
                            <td>₱{{ number_format($application->amount_paid, 2) }}</td>
                            <td>
                                <span class="badge" style="background: {{ 
                                    $application->status === 'approved' ? '#198754' : 
                                    ($application->status === 'rejected' ? '#dc3545' : 
                                    ($application->status === 'expired' ? '#6c757d' : 
                                    ($application->status === 'under_review' ? '#fd7e14' : '#0d6efd')))
                                }}">
                                    {{ ucfirst($application->status) }}
                                </span>
                            </td>
                            <td>
                                {{ $application->created_at->format('M d, Y') }}<br>
                                <small class="text-muted">{{ $application->created_at->format('h:i A') }}</small>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.subscriptions.show', $application) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($application->proof_of_payment)
                                        <a href="{{ route('admin.subscriptions.view-proof', $application) }}" 
                                           class="btn btn-sm btn-outline-info"
                                           target="_blank">
                                            <i class="bi bi-file-earmark"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="text-muted">No applications found</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($applications->hasPages())
            <div class="card-footer">
                {{ $applications->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Bulk Action Modal -->
<div class="modal fade" id="bulkActionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.subscriptions.bulk-action') }}" method="POST" id="bulkActionForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Bulk Actions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Action</label>
                        <select name="action" class="form-select" required>
                            <option value="">Select Action</option>
                            <option value="approve">Approve Selected</option>
                            <option value="reject">Reject Selected</option>
                            <option value="delete">Delete Selected</option>
                        </select>
                    </div>
                    <div class="mb-3" id="rejectionReasonField" style="display: none;">
                        <label class="form-label">Rejection Reason</label>
                        <textarea name="rejection_reason" 
                                  class="form-control" 
                                  rows="3"
                                  placeholder="Enter reason for rejection"></textarea>
                    </div>
                    <input type="hidden" name="selected_applications" id="selectedApplications">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Apply Action</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle select all checkbox
    const selectAll = document.querySelector('.select-all');
    const applicationCheckboxes = document.querySelectorAll('.application-checkbox');
    
    selectAll.addEventListener('change', function() {
        applicationCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Handle bulk action form submission
    const bulkActionForm = document.getElementById('bulkActionForm');
    const selectedApplicationsInput = document.getElementById('selectedApplications');
    const actionSelect = bulkActionForm.querySelector('[name="action"]');
    const rejectionReasonField = document.getElementById('rejectionReasonField');

    bulkActionForm.addEventListener('submit', function(e) {
        const selectedIds = Array.from(applicationCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);

        if (selectedIds.length === 0) {
            e.preventDefault();
            alert('Please select at least one application.');
            return;
        }

        selectedApplicationsInput.value = selectedIds.join(',');
    });

    // Show/hide rejection reason field based on action
    actionSelect.addEventListener('change', function() {
        rejectionReasonField.style.display = this.value === 'reject' ? 'block' : 'none';
    });
});
</script>
@endpush

@endsection