@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Mentors</h4>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="width: 40%">Name</th>
                                <th style="width: 40%">Email</th>
                                <th style="width: 20%" class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mentors as $mentor)
                                <tr>
                                    <td>{{ $mentor->name }}</td>
                                    <td>
                                        {{ $mentor->email }}
                                        <span style="display: block; font-size: 0.9rem; color: #6c757d;">
                                            {{ $mentor->phone }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        @if(auth()->user()->mentor_id === $mentor->id)
                                            <span class="badge bg-success">Your mentor</span>
                                        @elseif(isset($pendingMentorIds) && in_array($mentor->id, $pendingMentorIds))
                                            <span class="badge bg-warning text-dark">Requested</span>
                                        @else
                                            <span class="text-muted small">Contact an admin to assign a mentor.</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">No mentors yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection


