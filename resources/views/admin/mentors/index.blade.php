@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="fw-bold mb-1 text-dark">Mentors</h3>
                    <p class="text-muted mb-0">Manage mentor assignments</p>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(\App\Models\User::where('is_mentor', true)->orderBy('name')->paginate(25) as $mentor)
                                <tr class="align-middle">
                                    <td>{{ $mentor->name }}</td>
                                    <td>{{ $mentor->email }}</td>
                                    <td>{{ $mentor->phone ?? optional($mentor->profile)->phone ?? '—' }}</td>
                                    <td class="text-end">
                                        <form action="{{ route('admin.subscribers.toggle-mentor', $mentor) }}" method="POST" class="d-inline" data-confirm="Remove mentor {{ $mentor->name }}?" data-confirm-title="Confirm">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-secondary">Unmake Mentor</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No mentors yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection


