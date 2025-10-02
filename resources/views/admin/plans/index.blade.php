@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Subscription Plans</h4>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPlanModal">
            <i class="bi bi-plus-circle me-1"></i> Add New Plan
        </button>
    </div>

    <!-- Plans Grid -->
    <div class="row g-4">
        @forelse($plans as $plan)
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-white border-0 pt-4 pb-0">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="mb-1">{{ $plan->name }}</h5>
                                <div class="text-muted small">{{ $plan->duration }}</div>
                            </div>
                            <div class="form-check form-switch">
                                <form action="{{ route('admin.plans.toggle', $plan) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="checkbox" 
                                           class="form-check-input" 
                                           name="is_active"
                                           {{ $plan->is_active ? 'checked' : '' }}
                                           onChange="this.form.submit()"
                                           role="switch">
                                </form>
                            </div>
                        </div>
                        <div class="mt-2">
                            <span class="h4 mb-0">${{ number_format($plan->price, 2) }}</span>
                            <span class="text-muted">/{{ $plan->duration_in_months }} months</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">{{ $plan->description }}</p>

                        @if($plan->features)
                            <h6 class="text-muted mb-2">Features</h6>
                            <ul class="list-unstyled mb-0">
                                @foreach($plan->features as $feature)
                                    <li class="mb-1">
                                        <i class="bi bi-check text-success me-1"></i>
                                        {{ $feature }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                    <div class="card-footer bg-white border-0">
                        <div class="d-grid gap-2">
                            <button type="button" 
                                    class="btn btn-outline-primary" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editPlanModal{{ $plan->id }}">
                                <i class="bi bi-pencil me-1"></i> Edit Plan
                            </button>
                            <form action="{{ route('admin.plans.destroy', $plan) }}" 
                                  method="POST"
                                  onsubmit="return confirm('Are you sure you want to delete this plan?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    <i class="bi bi-trash me-1"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Plan Modal -->
            <div class="modal fade" id="editPlanModal{{ $plan->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('admin.plans.update', $plan) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Plan</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                @include('admin.plans._form', ['plan' => $plan])
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    No subscription plans have been created yet. Click the "Add New Plan" button to create one.
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($plans->hasPages())
        <div class="d-flex justify-content-end mt-4">
            {{ $plans->links() }}
        </div>
    @endif
</div>

<!-- Create Plan Modal -->
<div class="modal fade" id="createPlanModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.plans.store') }}" method="POST">
                @csrf
                
                <div class="modal-header">
                    <h5 class="modal-title">Create New Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @include('admin.plans._form', ['plan' => null])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Features Input Management
    const addFeatureField = function(container, value = '') {
        const featureRow = document.createElement('div');
        featureRow.className = 'input-group mb-2';
        featureRow.innerHTML = `
            <input type="text" 
                   name="features[]" 
                   class="form-control" 
                   value="${value}"
                   placeholder="Enter feature">
            <button type="button" class="btn btn-outline-danger remove-feature">
                <i class="bi bi-dash-circle"></i>
            </button>
        `;
        container.appendChild(featureRow);

        featureRow.querySelector('.remove-feature').addEventListener('click', function() {
            featureRow.remove();
        });
    };

    document.querySelectorAll('.add-feature').forEach(button => {
        const container = button.closest('.modal-body').querySelector('.features-container');
        
        button.addEventListener('click', () => addFeatureField(container));
    });

    // Duration Management
    document.querySelectorAll('select[name=duration]').forEach(select => {
        select.addEventListener('change', function() {
            const durationMonthsInput = this.closest('form').querySelector('input[name=duration_in_months]');
            switch(this.value) {
                case 'Monthly':
                    durationMonthsInput.value = '1';
                    break;
                case 'Quarterly':
                    durationMonthsInput.value = '3';
                    break;
                case 'Semi-Annual':
                    durationMonthsInput.value = '6';
                    break;
                case 'Annual':
                    durationMonthsInput.value = '12';
                    break;
            }
        });
    });

    // Display validation errors
    @if($errors->any())
        const modal = new bootstrap.Modal(document.getElementById('{{ old('plan_id') ? 'editPlanModal' . old('plan_id') : 'createPlanModal' }}'));
        modal.show();
    @endif
});
</script>
@endpush

@endsection