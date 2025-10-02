@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <!-- Header Section -->
        <div class="row mb-5">
            <div class="col-12">
                <div
                    class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                    <div class="flex-grow-1">
                        <h2 class="fw-bold text-dark mb-2">Browse Content</h2>
                        <p class="text-muted mb-0 fs-5">Discover the latest articles, videos, audio, and documents</p>
                    </div>

                    <!-- Search and Filter -->
                    <form action="{{ route('content.index') }}" method="GET"
                        class="d-flex flex-column flex-md-row gap-3 w-100 w-md-auto">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input type="text" name="search" class="form-control border-start-0"
                                placeholder="Search content..." value="{{ request('search') }}">
                        </div>

                        <select name="type" class="form-select" style="min-width: 160px;">
                            <option value="">All Types</option>
                            @foreach($contentTypes as $value => $label)
                                <option value="{{ $value }}" @selected(request('type') == $value)>{{ $label }}</option>
                            @endforeach
                        </select>

                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-funnel me-2"></i>Filter
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Content Grid -->
        @if($content->isEmpty())
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="bi bi-folder-x fs-1 text-muted"></i>
                </div>
                <h4 class="text-muted mb-3">No Content Found</h4>
                <p class="text-muted mb-4">Try adjusting your search criteria or browse all content.</p>
                @if(request()->hasAny(['search', 'type']))
                    <a href="{{ route('content.index') }}" class="btn btn-primary">
                        <i class="bi bi-arrow-clockwise me-2"></i>Clear Filters
                    </a>
                @endif
            </div>
        @else
            <div class="row g-4">
                @foreach($content as $item)
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <div class="card border-0 shadow-sm h-100 content-card">
                            <!-- Content Type Badge -->
                            <div class="card-header bg-white border-0 pb-0 pt-3 px-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge rounded-pill px-3 py-2 type-badge type-{{ $item->type }}">
                                        <i
                                            class="bi bi-{{ $item->type === 'video' ? 'play-btn' : ($item->type === 'audio' ? 'music-note-beamed' : 'file-text') }} me-1"></i>
                                        {{ ucfirst($item->type) }}
                                    </span>
                                    @if($item->created_at->gt(now()->subDays(7)))
                                        <span class="badge bg-warning text-dark">New</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Content Body -->
                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title fw-bold text-dark mb-2 line-clamp-2">{{ $item->title }}</h6>
                                <p class="card-text text-muted small mb-3 flex-grow-1 line-clamp-3">
                                    {{ $item->description }}
                                </p>

                                <!-- Metadata -->
                                <div class="d-flex justify-content-between align-items-center text-muted small mb-3">
                                    <span>
                                        <i class="bi bi-calendar3 me-1"></i>
                                        {{ $item->created_at->format('M d, Y') }}
                                    </span>
                                    @if($item->file_size)
                                        <span>
                                            <i class="bi bi-hdd me-1"></i>
                                            {{ $item->file_size }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="card-footer bg-transparent border-0 pt-0">
                                <div class="d-flex gap-2">
                                    <a href="{{ route('content.show', $item) }}" class="btn btn-primary btn-sm flex-fill">
                                        <i class="bi bi-eye me-1"></i>View
                                    </a>

                                    @if(auth()->check() && auth()->user()->is_subscription_active)
                                        @if(in_array($item->type, ['video', 'audio']))
                                            <a href="{{ route('content.stream', $item) }}" class="btn btn-outline-primary btn-sm"
                                                title="Stream">
                                                <i class="bi bi-play-circle"></i>
                                            </a>
                                        @elseif($item->file_path)
                                            <a href="{{ route('content.download', $item) }}" class="btn btn-outline-primary btn-sm"
                                                title="Download">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        @endif
                                    @else
                                        <span class="btn btn-outline-secondary btn-sm disabled" title="Subscribe to access">
                                            <i class="bi bi-lock"></i>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($content->hasPages())
                <div class="d-flex justify-content-center mt-5">
                    <nav aria-label="Content navigation">
                        {{ $content->withQueryString()->links() }}
                    </nav>
                </div>
            @endif
        @endif
    </div>

    <style>
        .content-card {
            border-radius: 12px;
            transition: all 0.3s ease;
            border: 1px solid #e9ecef;
        }

        .content-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
            border-color: #4361ee;
        }

        .type-badge {
            font-weight: 500;
            font-size: 0.75rem;
        }

        .type-article {
            background: rgba(13, 110, 253, 0.1) !important;
            color: #0d6efd !important;
        }

        .type-video {
            background: rgba(220, 53, 69, 0.1) !important;
            color: #dc3545 !important;
        }

        .type-audio {
            background: rgba(25, 135, 84, 0.1) !important;
            color: #198754 !important;
        }

        .type-document {
            background: rgba(108, 117, 125, 0.1) !important;
            color: #6c757d !important;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #4361ee, #3a56d4);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #3a56d4, #2f46b8);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
        }

        .btn-outline-primary {
            border: 2px solid #4361ee;
            color: #4361ee;
        }

        .btn-outline-primary:hover {
            background: #4361ee;
            border-color: #4361ee;
            transform: translateY(-2px);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #4361ee;
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.15);
        }

        .input-group-text {
            border-radius: 8px 0 0 8px;
        }

        .input-group .form-control {
            border-radius: 0 8px 8px 0;
        }

        /* Pagination styling */
        .pagination .page-link {
            border-radius: 8px;
            margin: 0 2px;
            border: 1px solid #dee2e6;
            color: #4361ee;
        }

        .pagination .page-item.active .page-link {
            background: #4361ee;
            border-color: #4361ee;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .container {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .d-flex.flex-column.flex-md-row {
                gap: 1.5rem !important;
            }

            form.d-flex.flex-column.flex-md-row {
                width: 100% !important;
            }

            .form-select {
                min-width: 100% !important;
            }
        }

        @media (max-width: 576px) {

            .col-xl-3,
            .col-lg-4,
            .col-md-6 {
                margin-bottom: 1rem;
            }

            .card-body {
                padding: 1.25rem !important;
            }

            .text-center.py-5 {
                padding: 3rem 0 !important;
            }
        }
    </style>
@endsection