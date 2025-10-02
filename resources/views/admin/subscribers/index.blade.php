@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="fw-bold mb-1 text-dark">Subscriber Management</h3>
                        <p class="text-muted mb-0">Manage and monitor all platform subscribers</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            @php
                $statCards = [
                    'total' => [
                        'color' => 'primary',
                        'icon' => 'people',
                        'label' => 'Total Subscribers',
                        'description' => 'All registered subscribers'
                    ],
                    'active' => [
                        'color' => 'success',
                        'icon' => 'check-circle',
                        'label' => 'Active',
                        'description' => 'Currently active subscriptions'
                    ],
                    'expired' => [
                        'color' => 'warning',
                        'icon' => 'exclamation-circle',
                        'label' => 'Expired',
                        'description' => 'Subscriptions needing renewal'
                    ],
                    'pending' => [
                        'color' => 'info',
                        'icon' => 'hourglass-split',
                        'label' => 'Pending',
                        'description' => 'Applications under review'
                    ]
                ];
            @endphp

            @foreach($statCards as $key => $card)
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card stat-card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="card-label text-{{ $card['color'] }} mb-2">
                                        {{ $card['label'] }}
                                    </h6>
                                    <h3 class="stat-number mb-2">{{ $stats[$key] ?? 0 }}</h3>
                                    <p class="stat-description text-muted mb-0">
                                        {{ $card['description'] }}
                                    </p>
                                </div>
                                <div class="stat-icon bg-{{ $card['color'] }} bg-opacity-10">
                                    <i class="bi bi-{{ $card['icon'] }} text-{{ $card['color'] }}"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Subscribers List Card -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <form action="{{ route('admin.subscribers.index') }}" method="GET"
                            class="row g-2 align-items-center">
                            <div class="col-auto flex-grow-1">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-search text-muted"></i>
                                    </span>
                                    <input type="text" name="search" class="form-control border-start-0"
                                        placeholder="Search subscribers..." value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-auto">
                                <select name="status" class="form-select form-select-sm">
                                    <option value="all" @selected(request('status') == 'all')>All Status</option>
                                    <option value="active" @selected(request('status') == 'active')>Active</option>
                                    <option value="expired" @selected(request('status') == 'expired')>Expired</option>
                                    <option value="pending" @selected(request('status') == 'pending')>Pending</option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="bi bi-funnel me-1"></i>Filter
                                </button>
                                @if(request('search') || request('status') != 'all')
                                    <a href="{{ route('admin.subscribers.index') }}" class="btn btn-sm btn-outline-secondary">
                                        Clear
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>
                    <div class="col-md-4 text-md-end mt-2 mt-md-0">
                        <span class="text-muted small">
                            Showing {{ $subscribers->firstItem() ?? 0 }}-{{ $subscribers->lastItem() ?? 0 }} of
                            {{ $subscribers->total() }} subscribers
                        </span>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Subscriber</th>
                                <th>Email</th>
                                <th>Company</th>
                                <th>Plan</th>
                                <th>Status</th>
                                <th>Expiry Date</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subscribers as $subscriber)
                                <tr class="align-middle">
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <div class="fw-semibold text-dark">{{ $subscriber->name }}</div>
                                                <small class="text-muted">ID: {{ $subscriber->id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 200px;" title="{{ $subscriber->email }}">
                                            {{ $subscriber->email }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted">
                                            {{ $subscriber->profile->company_name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($subscriber->currentSubscription)
                                            <span class="badge bg-light text-dark border">
                                                {{ $subscriber->currentSubscription->plan->name }}
                                            </span>
                                        @else
                                            <span class="text-muted small">No active plan</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($subscriber->is_subscribed)
                                            <span class="badge bg-success bg-opacity-10 text-success border-0">
                                                <i class="bi bi-check-circle me-1"></i>Active
                                            </span>
                                        @elseif($subscriber->hasPendingApplication())
                                            <span class="badge bg-warning bg-opacity-10 text-warning border-0">
                                                <i class="bi bi-clock me-1"></i>Pending
                                            </span>
                                        @else
                                            <span class="badge bg-danger bg-opacity-10 text-danger border-0">
                                                <i class="bi bi-x-circle me-1"></i>Expired
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($subscriber->subscription_expires_at)
                                            <span
                                                class="{{ $subscriber->subscription_expires_at->isPast() ? 'text-danger' : 'text-success' }} fw-medium">
                                                {{ $subscriber->subscription_expires_at->format('M d, Y') }}
                                            </span>
                                            @if($subscriber->subscription_expires_at->isPast())
                                                <div class="text-danger small">Expired</div>
                                            @else
                                                <div class="text-muted small">
                                                    {{ $subscriber->subscription_expires_at->diffForHumans() }}</div>
                                            @endif
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.subscribers.show', $subscriber) }}"
                                                class="btn btn-outline-primary" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal"
                                                data-bs-target="#extendModal{{ $subscriber->id }}" title="Extend Subscription">
                                                <i class="bi bi-calendar-plus"></i>
                                            </button>
                                            <form action="{{ route('admin.subscribers.destroy', $subscriber) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger"
                                                    onclick="return confirm('Are you sure you want to delete {{ $subscriber->name }}?')"
                                                    title="Delete Subscriber">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Extend Subscription Modal -->
                                <div class="modal fade" id="extendModal{{ $subscriber->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-sm">
                                        <div class="modal-content">
                                            <form action="{{ route('admin.subscribers.update-subscription', $subscriber) }}"
                                                method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="action" value="extend">

                                                <div class="modal-header">
                                                    <h6 class="modal-title fw-semibold">Extend Subscription</h6>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="extension_days"
                                                            class="form-label small fw-semibold">Extension Period (Days)</label>
                                                        <input type="number" class="form-control form-control-sm"
                                                            name="extension_days" min="1" max="365" value="30" required>
                                                    </div>
                                                    @if($subscriber->subscription_expires_at)
                                                        <div class="alert alert-light py-2 small">
                                                            <strong>Current expiry:</strong><br>
                                                            {{ $subscriber->subscription_expires_at->format('M d, Y') }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                                        data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-sm btn-primary">Extend</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="bi bi-people display-4 d-block mb-3"></i>
                                            <h5 class="fw-semibold">No subscribers found</h5>
                                            <p class="mb-0">No subscribers match your current filters.</p>
                                            @if(request('search') || request('status') != 'all')
                                                <a href="{{ route('admin.subscribers.index') }}"
                                                    class="btn btn-sm btn-primary mt-2">
                                                    Clear filters
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($subscribers->hasPages())
                    <div class="card-footer bg-white border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                Showing {{ $subscribers->firstItem() }} to {{ $subscribers->lastItem() }} of
                                {{ $subscribers->total() }} results
                            </div>
                            {{ $subscribers->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            /* Stat Cards */
            .stat-card {
                border-radius: 12px;
                transition: all 0.3s ease;
            }

            .stat-card:hover {
                transform: translateY(-3px);
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1) !important;
            }

            .card-label {
                font-size: 0.875rem;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .stat-number {
                font-size: 2rem;
                font-weight: 700;
                color: #2c3e50;
                margin-bottom: 0.5rem;
            }

            .stat-description {
                font-size: 0.8rem;
                margin-bottom: 0;
            }

            .stat-icon {
                width: 50px;
                height: 50px;
                border-radius: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.5rem;
            }

            /* Avatar */
            .avatar-sm {
                width: 40px;
                height: 40px;
            }

            .avatar-initials {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 100%;
                height: 100%;
                border-radius: 8px;
                color: white;
                font-weight: 600;
                font-size: 0.875rem;
            }

            /* Table Styling */
            .table {
                margin-bottom: 0;
            }

            .table> :not(caption)>*>* {
                padding: 1rem 0.75rem;
            }

            .table th {
                font-weight: 600;
                font-size: 0.875rem;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                color: #6c757d;
                border-bottom: 2px solid #e9ecef;
            }

            .table tbody tr:hover {
                background-color: #f8f9fa;
            }

            /* Badge Styling */
            .badge {
                font-size: 0.75rem;
                padding: 0.35rem 0.65rem;
                border-radius: 6px;
            }

            /* Button Group */
            .btn-group-sm>.btn {
                padding: 0.25rem 0.5rem;
            }

            /* Form Controls */
            .form-select-sm,
            .form-control-sm {
                border-radius: 6px;
            }

            .input-group-sm>.form-control {
                border-radius: 0 6px 6px 0;
            }

            .input-group-sm>.input-group-text {
                border-radius: 6px 0 0 6px;
            }

            /* Card Styling */
            .card {
                border-radius: 12px;
            }

            .card-header {
                border-radius: 12px 12px 0 0 !important;
            }

            /* Modal Styling */
            .modal-header {
                border-bottom: 1px solid #e9ecef;
                padding: 1rem 1.5rem;
            }

            .modal-footer {
                border-top: 1px solid #e9ecef;
                padding: 1rem 1.5rem;
            }

            /* Responsive */
            @media (max-width: 768px) {
                .card-header .row {
                    flex-direction: column;
                    gap: 1rem;
                }

                .card-header .col-md-8,
                .card-header .col-md-4 {
                    width: 100%;
                    text-align: left;
                }

                .btn-group {
                    flex-wrap: wrap;
                    gap: 0.25rem;
                }
            }
        </style>
    @endpush

@endsection