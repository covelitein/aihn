<x-app-layout>
    <div class="container-fluid py-4">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="fw-bold mb-1 text-dark">Admin Dashboard</h3>
                        <p class="text-muted mb-0">Welcome back, {{ Auth::user()->name }}</p>
                    </div>
                    <div class="text-end">
                        <small class="text-muted">Last updated: {{ now()->format('M j, Y g:i A') }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card metric-card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-white-50 mb-2">Total Users</h6>
                                <h2 class="fw-bold mb-0">{{ $totalUsers }}</h2>
                                <small class="text-white-75">
                                    <i class="bi bi-arrow-up"></i>
                                    {{ $newUsersThisMonth }} this month
                                </small>
                            </div>
                            <div class="metric-icon">
                                <i class="bi bi-people-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card metric-card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-white-50 mb-2">Active Subscriptions</h6>
                                <h2 class="fw-bold mb-0">{{ $activeSubscriptions }}</h2>
                                <small class="text-white-75">
                                    {{ number_format($subscriptionRate, 1) }}% conversion
                                </small>
                            </div>
                            <div class="metric-icon">
                                <i class="bi bi-check-circle-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card metric-card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-white-50 mb-2">Pending Applications</h6>
                                <h2 class="fw-bold mb-0">{{ $pendingApplications }}</h2>
                                <small class="text-white-75">
                                    <a href="{{ route('admin.subscriptions.index') }}" class="text-white text-decoration-underline">
                                        Review now
                                    </a>
                                </small>
                            </div>
                            <div class="metric-icon">
                                <i class="bi bi-clock-fill"></i>
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
                                <small class="text-white-75">
                                    <i class="bi bi-arrow-up"></i>
                                    ₱{{ number_format($revenueGrowth, 2) }} growth
                                </small>
                            </div>
                            <div class="metric-icon">
                                <i class="bi bi-currency-dollar"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="row">
            <!-- Plan Distribution & Quick Actions -->
            <div class="col-lg-8 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-transparent border-bottom-0 pb-3">
                        <h5 class="card-title mb-0 fw-semibold">Plan Distribution</h5>
                    </div>
                    <div class="card-body pt-0">
                        @if($planDistribution->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Plan</th>
                                            <th class="text-center">Subscribers</th>
                                            <th class="text-end">Revenue</th>
                                            <th class="text-end">Share</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($planDistribution as $plan)
                                            <tr>
                                                <td>
                                                    <span class="badge bg-primary bg-opacity-10 text-primary border-0 py-2 px-3">
                                                        {{ $plan->name }}
                                                    </span>
                                                </td>
                                                <td class="text-center fw-semibold">{{ $plan->active_subscribers_count }}</td>
                                                <td class="text-end fw-semibold text-success">₱{{ number_format($plan->monthly_revenue, 2) }}</td>
                                                <td class="text-end">
                                                    <div class="d-flex align-items-center justify-content-end gap-2">
                                                        <div class="progress flex-grow-1" style="height: 6px; max-width: 80px;">
                                                            <div class="progress-bar" style="width: {{ $plan->percentage }}%"></div>
                                                        </div>
                                                        <small class="text-muted fw-semibold">{{ number_format($plan->percentage, 1) }}%</small>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="bi bi-graph-up text-muted display-4 mb-3"></i>
                                <p class="text-muted">No subscription data available</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-transparent border-bottom-0 pb-3">
                        <h5 class="card-title mb-0 fw-semibold">Quick Actions</h5>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-grid gap-3">
                            <a href="{{ route('admin.subscriptions.index') }}" 
                               class="btn btn-outline-primary btn-lg text-start p-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <i class="bi bi-clipboard-data me-3"></i>
                                    <span>Manage Applications</span>
                                </div>
                                @if($pendingApplications > 0)
                                    <span class="badge bg-danger rounded-pill">{{ $pendingApplications }}</span>
                                @endif
                            </a>
                            
                            <a href="{{ route('admin.subscribers.index') }}" 
                               class="btn btn-outline-success btn-lg text-start p-3">
                                <i class="bi bi-people me-3"></i>
                                Manage Subscribers
                            </a>
                            
                            <a href="{{ route('admin.subscription.plans.index') }}" 
                               class="btn btn-outline-info btn-lg text-start p-3">
                                <i class="bi bi-tags me-3"></i>
                                Manage Plans
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
        </div>

        <!-- Recent Activity -->
        <div class="row">
            <!-- Recent Subscriptions -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-transparent border-bottom-0 pb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0 fw-semibold">Recent Subscriptions</h5>
                            <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-sm btn-outline-primary">
                                View All
                            </a>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        @if($recentSubscriptions->isNotEmpty())
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

            <!-- Recent Users -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-transparent border-bottom-0 pb-3">
                        <h5 class="card-title mb-0 fw-semibold">Recent Users</h5>
                    </div>
                    <div class="card-body pt-0">
                        @if($recentUsers->isNotEmpty())
                            <div class="list-group list-group-flush">
                                @foreach($recentUsers as $user)
                                    <div class="list-group-item px-0 py-3 border-0">
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
                                            <span class="badge bg-{{ $user->is_subscription_active ? 'success' : 'secondary' }} bg-opacity-10 text-{{ $user->is_subscription_active ? 'success' : 'secondary' }} border-0 py-2">
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