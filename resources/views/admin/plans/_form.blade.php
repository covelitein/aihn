<!-- Plan Form Fields -->
<div class="mb-3">
    <label for="name" class="form-label">Plan Name</label>
    <input type="text" 
           class="form-control @error('name') is-invalid @enderror" 
           id="name" 
           name="name" 
           value="{{ old('name', $plan?->name) }}"
           required>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="description" class="form-label">Description</label>
    <textarea class="form-control @error('description') is-invalid @enderror" 
              id="description" 
              name="description" 
              rows="3" 
              required>{{ old('description', $plan?->description) }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <label for="price" class="form-label">Price</label>
        <div class="input-group">
            <span class="input-group-text">$</span>
            <input type="number" 
                   step="0.01" 
                   min="0" 
                   class="form-control @error('price') is-invalid @enderror" 
                   id="price" 
                   name="price" 
                   value="{{ old('price', $plan?->price) }}"
                   required>
            @error('price')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <label for="duration" class="form-label">Duration</label>
        <select class="form-select @error('duration') is-invalid @enderror" 
                id="duration" 
                name="duration" 
                required>
            <option value="Monthly" @selected(old('duration', $plan?->duration) === 'Monthly')>Monthly</option>
            <option value="Quarterly" @selected(old('duration', $plan?->duration) === 'Quarterly')>Quarterly</option>
            <option value="Semi-Annual" @selected(old('duration', $plan?->duration) === 'Semi-Annual')>Semi-Annual</option>
            <option value="Annual" @selected(old('duration', $plan?->duration) === 'Annual')>Annual</option>
        </select>
        @error('duration')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<input type="hidden" name="duration_in_months" value="{{ old('duration_in_months', $plan?->duration_in_months) }}">

<div class="mb-3">
    <label class="form-label d-flex justify-content-between align-items-center">
        Features
        <button type="button" class="btn btn-sm btn-outline-primary add-feature">
            <i class="bi bi-plus-circle me-1"></i> Add Feature
        </button>
    </label>
    <div class="features-container">
        @if($plan && $plan->features)
            @foreach($plan->features as $feature)
                <div class="input-group mb-2">
                    <input type="text" 
                           name="features[]" 
                           class="form-control" 
                           value="{{ $feature }}" 
                           placeholder="Enter feature">
                    <button type="button" class="btn btn-outline-danger remove-feature">
                        <i class="bi bi-dash-circle"></i>
                    </button>
                </div>
            @endforeach
        @else
            <div class="input-group mb-2">
                <input type="text" 
                       name="features[]" 
                       class="form-control" 
                       placeholder="Enter feature">
                <button type="button" class="btn btn-outline-danger remove-feature">
                    <i class="bi bi-dash-circle"></i>
                </button>
            </div>
        @endif
    </div>
    @error('features')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <div class="form-check form-switch">
        <input type="checkbox" 
               class="form-check-input" 
               id="is_active" 
               name="is_active" 
               value="1"
               {{ old('is_active', $plan?->is_active) ? 'checked' : '' }}
               role="switch">
        <label class="form-check-label" for="is_active">Active</label>
    </div>
    <div class="form-text">Inactive plans won't be visible to users</div>
</div>

<div class="mb-3">
    <label for="sort_order" class="form-label">Sort Order</label>
    <input type="number" 
           class="form-control @error('sort_order') is-invalid @enderror" 
           id="sort_order" 
           name="sort_order" 
           value="{{ old('sort_order', $plan?->sort_order ?? 0) }}"
           min="0">
    <div class="form-text">Lower numbers appear first (0 is highest)</div>
    @error('sort_order')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>