@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ isset($content) ? 'Edit Content' : 'Create Content' }}</h5>
                    <a href="{{ route('admin.content.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back to List
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ isset($content) ? route('admin.content.update', $content) : route('admin.content.store') }}" 
                          method="POST" 
                          enctype="multipart/form-data"
                          class="row g-3">
                        @csrf
                        @if(isset($content))
                            @method('PUT')
                        @endif

                        <!-- Title -->
                        <div class="col-md-6">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('title') is-invalid @enderror" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title', $content->title ?? '') }}" 
                                   required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Content Type -->
                        <div class="col-md-6">
                            <label for="type" class="form-label">Content Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" 
                                    id="type" 
                                    name="type" 
                                    required>
                                <option value="">Select Type</option>
                                @foreach($contentTypes as $value => $label)
                                    <option value="{{ $value }}" @selected(old('type', $content->type ?? '') == $value)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="col-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3">{{ old('description', $content->description ?? '') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Content Text (for articles) -->
                        <div class="col-12 content-type-field" id="contentTextField">
                            <label for="content" class="form-label">Content</label>
                            <textarea class="form-control @error('content') is-invalid @enderror" 
                                      id="content" 
                                      name="content" 
                                      rows="10">{{ old('content', $content->content ?? '') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- File Upload -->
                        <div class="col-12 content-type-field" id="fileField">
                            <label for="file" class="form-label">File</label>
                            <input type="file" 
                                   class="form-control @error('file') is-invalid @enderror" 
                                   id="file" 
                                   name="file">
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if(isset($content) && $content->file_path)
                                <div class="mt-2">
                                    <small class="text-muted">
                                        Current file: {{ $content->file_original_name }}
                                        ({{ $content->getHumanFileSize() }})
                                    </small>
                                </div>
                            @endif
                        </div>

                        <!-- Subscription Plans -->
                        <div class="col-12">
                            <label class="form-label">Available in Plans <span class="text-danger">*</span></label>
                            <div class="row g-3">
                                @foreach($plans as $plan)
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input @error('accessible_plans') is-invalid @enderror" 
                                                   type="checkbox" 
                                                   name="accessible_plans[]" 
                                                   value="{{ $plan->id }}" 
                                                   id="plan{{ $plan->id }}"
                                                   @checked(in_array($plan->id, old('accessible_plans', $content->accessible_plans ?? [])))>
                                            <label class="form-check-label" for="plan{{ $plan->id }}">
                                                {{ $plan->name }} ({{ $plan->duration }})
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('accessible_plans')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Publication Status -->
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_published" 
                                       name="is_published" 
                                       value="1"
                                       @checked(old('is_published', $content->is_published ?? false))>
                                <label class="form-check-label" for="is_published">
                                    Publish Immediately
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
                                    {{ isset($content) ? 'Update' : 'Create' }} Content
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
    const typeSelect = document.getElementById('type');
    const contentTextField = document.getElementById('contentTextField');
    const fileField = document.getElementById('fileField');
    
    function updateFields() {
        const selectedType = typeSelect.value;
        
        if (selectedType === 'article') {
            contentTextField.style.display = 'block';
            fileField.style.display = 'none';
        } else if (['document', 'video', 'audio', 'file'].includes(selectedType)) {
            contentTextField.style.display = 'none';
            fileField.style.display = 'block';
        } else {
            contentTextField.style.display = 'none';
            fileField.style.display = 'none';
        }
    }
    
    typeSelect.addEventListener('change', updateFields);
    updateFields(); // Run on page load
});
</script>
@endpush

@endsection