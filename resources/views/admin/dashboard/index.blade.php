<x-app-layout>
    <div class="container-fluid py-4">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h3 class="fw-bold mb-1 text-dark">Admin Overview</h3>
                        <p class="text-muted mb-0">Welcome back, {{ Auth::user()->name }}</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.content.create') }}" class="btn btn-success">
                            <i class="bi bi-plus-circle me-1"></i> Create Content
                        </a>
                        <a href="{{ route('admin.subscribers.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-people me-1"></i> Subscribers
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Key Metrics (focused) -->
        @if(!Auth::user()->is_mentor || Auth::user()->is_super_admin)
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card metric-card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-white-50 mb-2">Total Users</h6>
                                <h2 class="fw-bold mb-0">{{ $totalUsers }}</h2>
                                <small class="text-white-75">+{{ $newUsersThisMonth }} this month</small>
                            </div>
                            <div class="metric-icon">
                                <i class="bi bi-people-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card metric-card bg-secondary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-white-50 mb-2">Active Users</h6>
                                <h2 class="fw-bold mb-0">{{ $activeUsers ?? $totalUsers }}</h2>
                                <small class="text-white-75">Currently active</small>
                            </div>
                            <div class="metric-icon">
                                <i class="bi bi-people-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card metric-card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-white-50 mb-2">Monthly Revenue</h6>
                                <h2 class="fw-bold mb-0">₱{{ number_format($monthlyRevenue, 2) }}</h2>
                                <small class="text-white-75">+₱{{ number_format($revenueGrowth, 2) }} vs last month</small>
                            </div>
                            <div class="metric-icon">
                                <i class="bi bi-currency-dollar"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Main Content Area -->
        <div class="row">
            <!-- At a glance (hide for mentors unless super admin) -->
            @if(!Auth::user()->is_mentor || Auth::user()->is_super_admin)
            <div class="col-lg-8 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-transparent border-bottom-0 pb-3">
                        <h5 class="card-title mb-0 fw-semibold">At a glance</h5>
                    </div>
                    <div class="card-body pt-0">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="p-3 rounded border bg-light">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="text-muted">New Users (30d)</span>
                                        <i class="bi bi-person-plus"></i>
                                    </div>
                                    <div class="h4 mb-0">{{ $newUsersThisMonth }}</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-3 rounded border bg-light">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="text-muted">Active Subscriptions</span>
                                        <i class="bi bi-receipt"></i>
                                    </div>
                                    <div class="h4 mb-0">{{ $activeSubscriptions ?? 0 }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Quick Actions (hide for mentors unless super admin) -->
            @if(!Auth::user()->is_mentor || Auth::user()->is_super_admin)
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-transparent border-bottom-0 pb-3">
                        <h5 class="card-title mb-0 fw-semibold">Quick Actions</h5>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-grid gap-3">
                            <a href="{{ route('admin.subscribers.index') }}" 
                               class="btn btn-outline-success btn-lg text-start p-3">
                                <i class="bi bi-people me-3"></i>
                                Manage Subscribers
                            </a>

                            <a href="{{ route('admin.content.index') }}" 
                               class="btn btn-outline-warning btn-lg text-start p-3">
                                <i class="bi bi-newspaper me-3"></i>
                                Content Management
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Recent Activity -->
        <div class="row">
            <!-- Recent Subscriptions / Mentor Requests (mentor view) -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-transparent border-bottom-0 pb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0 fw-semibold">
                                @if(Auth::user()->is_mentor)
                                    Mentor Requests
                                @else
                                    Recent Subscriptions
                                @endif
                            </h5>
                            @if(!Auth::user()->is_mentor && (Auth::user()->is_super_admin))
                                <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        @if(Auth::user()->is_mentor)
                            @php($requests = \App\Models\MentorRequest::where('mentor_id', Auth::id())->latest()->take(10)->get())
                            @if($requests->isNotEmpty())
                                <div class="list-group list-group-flush">
                                    @foreach($requests as $req)
                                        <div class="list-group-item px-0 py-3 border-0">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center me-3">
                                                        <i class="bi bi-person text-muted"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-1 fw-semibold">{{ $req->user->name }}</h6>
                                                        <small class="text-muted">Requested {{ $req->created_at->diffForHumans() }}</small>
                                                    </div>
                                                </div>
                                                <div class="d-flex gap-2">
                                                    @if($req->status === 'pending')
                                                        <form method="POST" action="{{ route('mentor-requests.accept', $req) }}">
                                                            @csrf
                                                            <button class="btn btn-sm btn-success">Accept</button>
                                                        </form>
                                                        <form method="POST" action="{{ route('mentor-requests.reject', $req) }}">
                                                            @csrf
                                                            <button class="btn btn-sm btn-outline-secondary">Reject</button>
                                                        </form>
                                                    @else
                                                        <span class="badge bg-{{ $req->status === 'accepted' ? 'success' : 'secondary' }}">{{ $req->status === 'accepted' ? 'Assigned' : 'Rejected' }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="bi bi-inbox text-muted display-4 mb-3"></i>
                                    <p class="text-muted">No mentor requests</p>
                                </div>
                            @endif
                        @elseif($recentSubscriptions->isNotEmpty())
                            <div class="list-group list-group-flush">
                                @foreach($recentSubscriptions as $subscription)
                                    <div class="list-group-item px-0 py-3 border-0">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center me-3">
                                                    <i class="bi bi-person text-muted"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1 fw-semibold">{{ $subscription->user->name }}</h6>
                                                    <small class="text-muted">
                                                        {{ $subscription->plan->name }} • {{ $subscription->created_at->diffForHumans() }}
                                                    </small>
                                                </div>
                                            </div>
                                            <span class="badge bg-{{ $subscription->status === 'active' ? 'success' : 'warning' }} bg-opacity-10 text-{{ $subscription->status === 'active' ? 'success' : 'warning' }} border-0 py-2">
                                                {{ ucfirst($subscription->status) }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="bi bi-receipt text-muted display-4 mb-3"></i>
                                <p class="text-muted">No recent subscriptions</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Users / Mentees -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100 w-100 overflow-y-hidden">
                    <div class="card-header bg-transparent border-bottom-0 pb-3 pt-3">
                        <h5 class="card-title mb-0 fw-semibold">
                            @if(Auth::user()->is_mentor)
                                My Mentees
                            @else
                                Recent Users
                            @endif
                        </h5>
                    </div>
                    <div class="card-body pt-0">
                        @if(Auth::user()->is_mentor)
                            @if($mentees->isNotEmpty())
                                <div class="list-group list-group-flush">
                                    @foreach($mentees as $user)
                                        <div class="list-group-item px-0 pe-2 py-3 border-0">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center me-3">
                                                        <i class="bi bi-person-badge text-muted"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-1 fw-semibold">{{ $user->name }}</h6>
                                                        <small class="text-muted">{{ $user->email }} • Assigned {{ $user->created_at->diffForHumans() }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="bi bi-people text-muted display-4 mb-3"></i>
                                    <p class="text-muted">No mentees assigned yet</p>
                                </div>
                            @endif
                        @elseif($recentUsers->isNotEmpty())
                            <div class="list-group list-group-flush">
                                @foreach($recentUsers as $user)
                                    <div class="list-group-item px-0 pe-2 py-3 border-0">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center me-3">
                                                    <i class="bi bi-person-plus text-muted"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1 fw-semibold">{{ $user->name }}</h6>
                                                    <small class="text-muted">
                                                        {{ $user->email }} • {{ $user->created_at->diffForHumans() }}
                                                    </small>
                                                </div>
                                            </div>
                                            <span class="badge mx-2 bg-{{ $user->is_subscription_active ? 'success' : 'secondary' }} bg-opacity-10 text-{{ $user->is_subscription_active ? 'success' : 'secondary' }} border-0 py-2">
                                                {{ $user->is_subscription_active ? 'Active' : 'No Sub' }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="bi bi-person-plus text-muted display-4 mb-3"></i>
                                <p class="text-muted">No recent users</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .metric-card {
            border: none;
            border-radius: 12px;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .metric-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .metric-icon {
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
        }

        .avatar-sm {
            width: 40px;
            height: 40px;
        }

        .btn-lg {
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-lg:hover {
            transform: translateY(-1px);
        }

        .progress {
            border-radius: 10px;
        }

        .progress-bar {
            border-radius: 10px;
        }

        .table th {
            border: none;
            font-weight: 600;
            font-size: 0.875rem;
            color: #6c757d;
        }

        .table td {
            border: none;
            padding: 1rem 0.75rem;
        }

        .list-group-item {
            border: none !important;
        }

        .list-group-item:not(:last-child) {
            border-bottom: 1px solid #f8f9fa !important;
        }
    </style>
</x-app-layout>