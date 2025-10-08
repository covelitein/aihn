@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Add New User</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.subscribers.store') }}">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" placeholder="Optional">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Set initial password (optional)">
                                <small class="text-muted">If empty, a random password will be generated and reset link sent.</small>
                            </div>

                            <div class="form-check mb-3">
                                <input type="checkbox" name="is_admin" value="1" class="form-check-input" id="isAdmin" {{ request('role') === 'admin' ? 'checked' : '' }}>
                                <label class="form-check-label" for="isAdmin">Make admin</label>
                            </div>

                            <div class="form-check mb-3">
                                <input type="checkbox" name="is_mentor" value="1" class="form-check-input" id="isMentor" {{ request('role') === 'mentor' ? 'checked' : '' }}>
                                <label class="form-check-label" for="isMentor">Make mentor</label>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Renewal date (optional)</label>
                                <input type="date" name="renewal_date" class="form-control">
                                <small class="text-muted">Users will see when their access should be renewed.</small>
                            </div>

                            <div class="d-flex justify-content-end">
                                <a href="{{ route('admin.subscribers.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                                <button class="btn btn-primary">Create User</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
