@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="fw-bold mb-1 text-dark">Administrators</h3>
                    <p class="text-muted mb-0">Manage platform admins</p>
                </div>
                @can('superadmin')
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.admins.create') }}" class="btn btn-sm btn-success">
                            <i class="bi bi-plus-circle me-1"></i> Add Admin
                        </a>
                        <a href="{{ route('admin.subscribers.create', ['role' => 'mentor']) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-person-badge me-1"></i> Add Mentor
                        </a>
                    </div>
                @endcan
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
                                <th>Mentor</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($admins as $admin)
                                <tr class="align-middle">
                                    <td>{{ $admin->name }}</td>
                                    <td>{{ $admin->email }}<div class="text-muted small">{{ $admin->phone ?? '—' }}</div></td>
                                    <td>
                                        @if($admin->is_mentor)
                                            <span class="badge bg-info me-2">Mentor</span>
                                            <form action="{{ route('admin.subscribers.toggle-mentor', $admin) }}" method="POST" class="d-inline" data-confirm="Unmake mentor {{ $admin->name }}?" data-confirm-title="Confirm">
                                                @csrf
                                                <button class="btn btn-sm btn-outline-secondary">Unmake</button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.subscribers.toggle-mentor', $admin) }}" method="POST" class="d-inline" data-confirm="Make {{ $admin->name }} a mentor?" data-confirm-title="Confirm">
                                                @csrf
                                                <button class="btn btn-sm btn-outline-primary">Make Mentor</button>
                                            </form>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @can('superadmin')
                                            <button class="btn btn-sm btn-outline-danger" data-remote-delete-url="{{ route('admin.admins.destroy', $admin) }}" data-name="admin {{ $admin->name }}">Delete</button>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No administrators found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($admins->hasPages())
                <div class="card-footer bg-white border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Showing {{ $admins->firstItem() }} to {{ $admins->lastItem() }} of
                            {{ $admins->total() }} results
                        </div>
                        {{ $admins->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection