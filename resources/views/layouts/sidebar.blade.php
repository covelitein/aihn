<style>
    :root {
        --sidebar-width: 280px;
        --sidebar-collapsed-width: 80px;
        --sidebar-mobile-width: 300px;
        --primary-color: #047;
        --primary-dark: #025;
    }

    .sidebar {
        width: var(--sidebar-width);
        background: linear-gradient(180deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        height: 100vh;
        position: fixed;
        left: 0;
        top: 0;
        z-index: 1050;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 2px 0 2px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
    }

    .sidebar.collapsed {
        width: var(--sidebar-collapsed-width);
    }

    .sidebar-mobile {
        width: var(--sidebar-mobile-width);
        transform: translateX(-100%);
    }

    .sidebar-mobile.show {
        transform: translateX(0);
    }

    .sidebar-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1040;
        backdrop-filter: blur(2px);
    }

    .sidebar-overlay.show {
        display: block;
    }

    .sidebar-header {
        height: 70px;
        padding: 0 1.5rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
    }

    .sidebar-logo {
        color: #fff;
        font-weight: 700;
        font-size: 1.4rem;
        opacity: 1;
        transition: opacity 0.3s ease;
    }

    .sidebar.collapsed .sidebar-logo {
        opacity: 0;
        width: 0;
        overflow: hidden;
    }

    .sidebar-toggle {
        background: rgba(255, 255, 255, 0.15);
        border: none;
        color: #fff;
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }

    .sidebar-toggle:hover {
        background: rgba(255, 255, 255, 0.25);
        transform: scale(1.05);
    }

    .sidebar.collapsed .sidebar-toggle {
        margin: 0 auto;
    }

    .sidebar-close {
        display: none;
        background: rgba(255, 255, 255, 0.15);
        border: none;
        color: #fff;
        width: 36px;
        height: 36px;
        border-radius: 8px;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .sidebar-close:hover {
        background: rgba(255, 255, 255, 0.25);
    }

    .sidebar-content {
        flex: 1;
        padding: 1rem 0;
        overflow-y: auto;
        overflow-x: hidden;
    }

    .sidebar-content::-webkit-scrollbar {
        width: 4px;
    }

    .sidebar-content::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.3);
        border-radius: 10px;
    }

    .nav-item {
        margin: 0.25rem 1rem;
    }

    .nav-link {
        color: #fff;
        padding: 0.875rem 1rem;
        border-radius: 8px;
        transition: all 0.2s ease;
        white-space: nowrap;
        display: flex;
        align-items: center;
        gap: 0.875rem;
        text-decoration: none;
        border: none;
        background: transparent;
        width: 100%;
        text-align: left;
        font-weight: 500;
    }

    .nav-link:hover {
        background: rgba(255, 255, 255, 0.15);
        transform: translateX(2px);
    }

    .nav-link.active {
        background: rgba(255, 255, 255, 0.2);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .nav-link .bi {
        font-size: 1.3rem;
        width: 24px;
        text-align: center;
        flex-shrink: 0;
        opacity: 0.9;
    }

    .nav-link.active .bi {
        opacity: 1;
    }

    .nav-text {
        transition: all 0.3s ease;
        opacity: 1;
        font-size: 0.95rem;
    }

    .sidebar.collapsed .nav-text {
        opacity: 0;
        width: 0;
        overflow: hidden;
        position: absolute;
    }

    .sidebar-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        flex-shrink: 0;
    }

    .sidebar.collapsed .sidebar-footer {
        padding: 1rem 0.5rem;
        display: flex;
        justify-content: center;
    }

    .sidebar.collapsed .sidebar-footer .nav-item {
        margin: 0;
        display: flex;
        justify-content: center;
    }

    .sidebar.collapsed .sidebar-footer .nav-link {
        justify-content: center;
        padding: 0.75rem;
    }

    .section-divider {
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        margin: 1rem 0;
    }

    .section-label {
        color: rgba(255, 255, 255, 0.7);
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 1rem 0 0.5rem 1rem;
        display: block;
    }

    /* Mobile specific styles */
    @media (max-width: 1330px) {
        .sidebar {
            width: var(--sidebar-mobile-width);
            transform: translateX(-100%);
        }

        .sidebar.show {
            transform: translateX(0);
        }

        .sidebar.collapsed {
            width: var(--sidebar-mobile-width);
            transform: translateX(-100%);
        }

        .sidebar.collapsed.show {
            transform: translateX(0);
        }

        .sidebar.collapsed .nav-text {
            opacity: 1;
            width: auto;
            position: static;
        }

        .sidebar-close {
            display: flex;
        }

        .sidebar-toggle {
            display: none;
        }
    }

    @media (max-width: 480px) {
        .sidebar {
            width: 85%;
            max-width: 300px;
        }
    }
</style>

<!-- Sidebar Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Sidebar -->
@php($onAdmin = auth()->user()?->is_admin == 1)
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            {{ config('app.name', 'Subscribers') }}
        </div>
        <div class="d-flex align-items-center gap-2">
            <button class="sidebar-toggle" id="sidebarToggle" title="Toggle Sidebar">
                <i class="bi bi-chevron-left"></i>
            </button>
            <button class="sidebar-close" id="sidebarClose" title="Close Sidebar">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    </div>

    <div class="sidebar-content">
        <ul class="nav flex-column">
            <!-- Dashboard - Always Visible for Authenticated Users -->
            @if(auth()->user()?->is_admin == 0)
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}"
                        class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </li>
            @else
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard.index') }}"
                        class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </li>
            @endif

            @auth
                @if($onAdmin)
                    <!-- ADMIN SECTION (Admin area) -->
                    @if(auth()->user()?->is_admin)
                        <div class="section-divider"></div>
                        <span class="section-label">Administration</span>

                        <li class="nav-item">
                            <a href="{{ route('admin.subscriptions.index') }}"
                                class="nav-link {{ request()->routeIs('admin.subscriptions.*') ? 'active' : '' }}">
                                <i class="bi bi-clipboard-check"></i>
                                <span class="nav-text">Applications</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.subscribers.index') }}"
                                class="nav-link {{ request()->routeIs('admin.subscribers.*') ? 'active' : '' }}">
                                <i class="bi bi-people"></i>
                                <span class="nav-text">Subscribers</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.subscription.plans.index') }}"
                                class="nav-link {{ request()->routeIs('admin.subscription.plans.*') ? 'active' : '' }}">
                                <i class="bi bi-tags"></i>
                                <span class="nav-text">Manage Plans</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.content.index') }}"
                                class="nav-link {{ request()->routeIs('admin.content.*') ? 'active' : '' }}">
                                <i class="bi bi-file-earmark-text"></i>
                                <span class="nav-text">Content Management</span>
                            </a>
                        </li>
                    @endif
                @else
                    <!-- SUBSCRIPTION SECTION (Public app area) -->
                    <span class="section-label">Subscription</span>

                    <li class="nav-item">
                        <a href="{{ route('subscription.plans') }}"
                            class="nav-link {{ request()->routeIs('subscription.plans') ? 'active' : '' }}">
                            <i class="bi bi-grid"></i>
                            <span class="nav-text">Available Plans</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('subscription.status') }}"
                            class="nav-link {{ request()->routeIs('subscription.status') ? 'active' : '' }}">
                            <i class="bi bi-clock-history"></i>
                            <span class="nav-text">My Subscription</span>
                        </a>
                    </li>
                @endif

                <!-- ACCOUNT SECTION - For All Authenticated Users -->
                <div class="section-divider"></div>
                <span class="section-label">Account</span>
                <li class="nav-item">
                    <a href="{{ route('profile.edit') }}"
                        class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                        <i class="bi bi-person"></i>
                        <span class="nav-text">My Profile</span>
                    </a>
                </li>

                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}" id="logoutForm">
                        @csrf
                        <button type="submit" class="nav-link w-100 text-start">
                            <i class="bi bi-box-arrow-right"></i>
                            <span class="nav-text">Logout</span>
                        </button>
                    </form>
                </li>
            @else
                <!-- GUEST SECTION - For Non-Authenticated Users -->
                <li class="nav-item">
                    <a href="{{ route('login') }}" class="nav-link">
                        <i class="bi bi-box-arrow-in-right"></i>
                        <span class="nav-text">Login</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('register') }}" class="nav-link">
                        <i class="bi bi-person-plus"></i>
                        <span class="nav-text">Register</span>
                    </a>
                </li>
            @endauth
        </ul>
    </div>
</aside>

<script>
    (function () {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const toggleBtn = document.getElementById('sidebarToggle');
        const closeBtn = document.getElementById('sidebarClose');

        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('collapsed');
                document.dispatchEvent(new CustomEvent('sidebarToggle', { detail: { collapsed: sidebar.classList.contains('collapsed') } }));
            });
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            });
        }

        overlay.addEventListener('click', () => {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });

        document.addEventListener('closeSidebar', () => {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });
    })();
</script>