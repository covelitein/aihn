@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0 fw-bold">Subscription Plans</h4>
                <a href="{{ route('admin.subscription.plans.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Add New Plan
                </a>
            </div>
        </div>
    </div>

    <!-- Plans List -->
    <div class="row">
        @forelse($plans as $plan)
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card plan-card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center py-3">
                        <div>
                            <span class="badge {{ $plan->is_active ? 'bg-success' : 'bg-warning' }} status-badge">
                                {{ $plan->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light rounded-circle" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a href="{{ route('admin.subscription.plans.edit', $plan) }}" class="dropdown-item">
                                        <i class="bi bi-pencil me-2"></i> Edit
                                    </a>
                                </li>
                                <li>
                                    <form action="{{ route('admin.subscription.plans.toggle-status', $plan) }}" 
                                          method="POST" 
                                          class="dropdown-item-form">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            @if($plan->is_active)
                                                <i class="bi bi-eye-slash me-2"></i> Deactivate
                                            @else
                                                <i class="bi bi-eye me-2"></i> Activate
                                            @endif
                                        </button>
                                    </form>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('admin.subscription.plans.destroy', $plan) }}" 
                                          method="POST"
                                          class="dropdown-item-form"
                                          onsubmit="return AppUI.confirm('Are you sure you want to delete this plan?', 'Confirm Deletion')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-trash me-2"></i> Delete
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <h5 class="card-title fw-bold mb-2">{{ $plan->name }}</h5>
                        <div class="mb-3">
                            <span class="h3 fw-bold text-primary mb-0">₱{{ number_format($plan->price, 2) }}</span>
                            <span class="text-muted">/ {{ $plan->duration }}</span>
                        </div>
                        
                        @if($plan->description)
                            <p class="card-text text-muted mb-3">{{ $plan->description }}</p>
                        @endif

                        @if(!empty($plan->features))
                            <ul class="list-unstyled mb-0 plan-features">
                                @foreach($plan->features as $feature)
                                    <li class="mb-2 d-flex align-items-center">
                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                        <span>{{ $feature }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="bi bi-people me-1"></i>
                                {{ $plan->subscribers_count ?? 0 }} subscribers
                            </small>
                            <small class="text-muted">
                                Sort Order: {{ $plan->sort_order }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card empty-state-card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-credit-card-2-front text-muted empty-state-icon"></i>
                        <h5 class="mt-3 mb-2">No subscription plans found</h5>
                        <p class="text-muted mb-4">Create your first plan to get started.</p>
                        <a href="{{ route('admin.subscription.plans.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i> Create New Plan
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>

<style>
.plan-card {
    border: none;
    border-radius: 8px;
    box-shadow: var(--card-shadow);
    transition: all 0.3s ease;
}

.plan-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--card-hover-shadow);
}

.plan-card .card-header {
    background-color: white;
    border-bottom: 1px solid rgba(0,0,0,0.05);
}

.status-badge {
    font-size: 0.75rem;
    padding: 0.35rem 0.75rem;
    border-radius: 50px;
}

.plan-features {
    border-top: 1px solid rgba(0,0,0,0.05);
    padding-top: 1rem;
}

.empty-state-card {
    border: 2px dashed #dee2e6;
    background-color: #fafbfc;
}

.empty-state-icon {
    font-size: 3rem;
}

.dropdown-item-form {
    display: block;
    width: 100%;
    padding: 0;
    margin: 0;
}

.dropdown-item-form .dropdown-item {
    border: 0;
    display: block;
    width: 100%;
    padding: 0.5rem 1rem;
    clear: both;
    font-weight: 400;
    color: #212529;
    text-align: inherit;
    text-decoration: none;
    white-space: nowrap;
    background-color: transparent;
    border: 0;
    display: flex;
    align-items: center;
}

.dropdown-item-form .dropdown-item:hover {
    color: #1e2125;
    background-color: #f8f9fa;
}

.dropdown-item-form .dropdown-item.text-danger:hover {
    color: #dc3545 !important;
    background-color: rgba(220, 53, 69, 0.1);
}

.card-title {
    color: #333;
}

.text-primary {
    color: var(--primary-color) !important;
}
</style>
@endsection