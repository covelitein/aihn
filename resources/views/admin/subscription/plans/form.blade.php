@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ isset($plan) ? 'Edit Plan' : 'Create New Plan' }}</h5>
                    <a href="{{ route('admin.subscription.plans.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back to Plans
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ isset($plan) ? route('admin.subscription.plans.update', $plan) : route('admin.subscription.plans.store') }}" 
                          method="POST" 
                          class="row g-3">
                        @csrf
                        @if(isset($plan))
                            @method('PUT')
                        @endif

                        <!-- Basic Information -->
                        <div class="col-md-6">
                            <label for="name" class="form-label">Plan Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $plan->name ?? '') }}" 
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" 
                                       class="form-control @error('price') is-invalid @enderror" 
                                       id="price" 
                                       name="price" 
                                       value="{{ old('price', $plan->price ?? '') }}" 
                                       step="0.01" 
                                       min="0" 
                                       required>
                            </div>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="duration" class="form-label">Duration <span class="text-danger">*</span></label>
                            <select class="form-select @error('duration') is-invalid @enderror" 
                                    id="duration" 
                                    name="duration" 
                                    required>
                                <option value="">Select Duration</option>
                                <option value="monthly" @selected(old('duration', $plan->duration ?? '') == 'monthly')>Monthly</option>
                                <option value="quarterly" @selected(old('duration', $plan->duration ?? '') == 'quarterly')>Quarterly</option>
                                <option value="yearly" @selected(old('duration', $plan->duration ?? '') == 'yearly')>Yearly</option>
                            </select>
                            @error('duration')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="sort_order" class="form-label">Sort Order</label>
                            <input type="number" 
                                   class="form-control @error('sort_order') is-invalid @enderror" 
                                   id="sort_order" 
                                   name="sort_order" 
                                   value="{{ old('sort_order', $plan->sort_order ?? 0) }}" 
                                   min="0">
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Lower numbers appear first</div>
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3">{{ old('description', $plan->description ?? '') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Features -->
                        <div class="col-12">
                            <label class="form-label">Features</label>
                            <div class="features-container">
                                @foreach(old('features', $plan->features ?? ['']) as $index => $feature)
                                    <div class="input-group mb-2">
                                        <input type="text" 
                                               class="form-control" 
                                               name="features[]" 
                                               value="{{ $feature }}"
                                               placeholder="e.g., Access to all content">
                                        <button type="button" class="btn btn-outline-danger remove-feature">
                                            <i class="bi bi-dash-circle"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="addFeature">
                                <i class="bi bi-plus-circle me-1"></i>
                                Add Feature
                            </button>
                        </div>

                        <!-- Status -->
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1"
                                       @checked(old('is_active', $plan->is_active ?? true))>
                                <label class="form-check-label" for="is_active">
                                    Active Plan
                                </label>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="col-12">
                            <hr>
                            <div class="d-flex justify-content-end gap-2">
                                <button type="reset" class="btn btn-light">
                                    <i class="bi bi-x-circle me-1"></i>
                                    Reset
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-1"></i>
                                    {{ isset($plan) ? 'Update' : 'Create' }} Plan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const featuresContainer = document.querySelector('.features-container');
    const addFeatureBtn = document.getElementById('addFeature');

    // Add feature field
    addFeatureBtn.addEventListener('click', function() {
        const featureField = document.createElement('div');
        featureField.className = 'input-group mb-2';
        featureField.innerHTML = `
            <input type="text" 
                   class="form-control" 
                   name="features[]" 
                   placeholder="e.g., Access to all content">
            <button type="button" class="btn btn-outline-danger remove-feature">
                <i class="bi bi-dash-circle"></i>
            </button>
        `;
        featuresContainer.appendChild(featureField);
    });

    // Remove feature field
    featuresContainer.addEventListener('click', function(e) {
        if (e.target.closest('.remove-feature')) {
            e.target.closest('.input-group').remove();
        }
    });

    // Ensure at least one feature field exists
    if (featuresContainer.children.length === 0) {
        addFeatureBtn.click();
    }
});
</script>
@endpush
@endsection