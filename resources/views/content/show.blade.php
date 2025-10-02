@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">{{ $content->title }}</h4>
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="mb-3 text-muted small">
                <span class="badge" style="background: {{ 
                    $content->type === 'article' ? '#0d6efd' : 
                    ($content->type === 'video' ? '#dc3545' : 
                    ($content->type === 'audio' ? '#198754' : '#6c757d'))
                }}">
                    {{ ucfirst($content->type) }}
                </span>
            </div>

            @if($content->type === 'article')
                <div class="content-body">
                    {!! nl2br(e($content->content)) !!}
                </div>
            @elseif(in_array($content->type, ['video', 'audio']) && $content->file_path)
                <div class="ratio ratio-16x9 mb-3">
                    <iframe src="{{ route('content.stream', $content) }}" frameborder="0" allowfullscreen></iframe>
                </div>
                <a href="{{ route('content.download', $content) }}" class="btn btn-primary">
                    <i class="bi bi-download me-1"></i> Download
                </a>
            @elseif($content->type === 'document' && $content->file_path)
                <a href="{{ route('content.download', $content) }}" class="btn btn-primary">
                    <i class="bi bi-download me-1"></i> Download Document
                </a>
            @else
                <div class="alert alert-info mb-0">
                    Content not available.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
