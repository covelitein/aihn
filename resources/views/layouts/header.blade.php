<style>
    .header {
        height: 70px;
        background: #fff;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        position: sticky;
        top: 0;
        z-index: 1030;
        border-bottom: 1px solid #e9ecef;
    }

    .header-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 100%;
        padding: 0 1.5rem;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .header-right {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .mobile-menu-toggle {
        background: none;
        border: none;
        color: #6c757d;
        font-size: 1.4rem;
        cursor: pointer;
        padding: 0.5rem;
        border-radius: 8px;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
    }

    .mobile-menu-toggle:hover {
        background: #f8f9fa;
        color: #495057;
    }

    .header-actions {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    /* Custom Notification Dropdown Styles */
    .notification-wrapper {
        position: relative;
    }

    .notification-toggle {
        background: none;
        border: none;
        color: #6c757d;
        font-size: 1.3rem;
        padding: 0.5rem;
        border-radius: 8px;
        transition: all 0.2s ease;
        position: relative;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .notification-toggle:hover {
        background: #f8f9fa;
        color: #047;
    }

    .notification-badge {
        position: absolute;
        top: 3px;
        right: 3px;
        background: #dc3545;
        color: white;
        border-radius: 50%;
        width: 18px;
        height: 18px;
        font-size: 0.65rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        border: 2px solid #fff;
    }

    .notification-dropdown {
        position: absolute;
        top: 100%;
        right: 0;
        width: 360px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        border: 1px solid #e9ecef;
        z-index: 1100;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.3s ease;
    }

    .notification-dropdown.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(5px);
    }

    .notification-header {
        padding: 1.25rem 1.5rem 0.75rem;
        border-bottom: 1px solid #f1f3f4;
    }

    .notification-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2d3748;
        margin: 0;
    }

    .notification-badge-header {
        background: #047;
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .notification-list {
        max-height: 400px;
        overflow-y: auto;
    }

    .notification-item {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #f8f9fa;
        transition: all 0.2s ease;
        cursor: pointer;
        display: flex;
        gap: 1rem;
        align-items: flex-start;
    }

    .notification-item:hover {
        background: #f8f9fa;
    }

    .notification-item:last-child {
        border-bottom: none;
    }

    .notification-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1.1rem;
    }

    .notification-icon.primary {
        background: rgba(4, 119, 119, 0.1);
        color: #047;
    }

    .notification-icon.success {
        background: rgba(40, 167, 69, 0.1);
        color: #28a745;
    }

    .notification-icon.warning {
        background: rgba(255, 193, 7, 0.1);
        color: #ffc107;
    }

    .notification-content {
        flex: 1;
        min-width: 0;
    }

    .notification-message {
        font-size: 0.9rem;
        color: #4a5568;
        margin: 0 0 0.25rem 0;
        line-height: 1.4;
    }

    .notification-time {
        font-size: 0.75rem;
        color: #718096;
    }

    .notification-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid #f1f3f4;
        text-align: center;
    }

    .view-all-btn {
        background: none;
        border: none;
        color: #047;
        font-size: 0.9rem;
        font-weight: 500;
        cursor: pointer;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        transition: all 0.2s ease;
    }

    .view-all-btn:hover {
        background: rgba(4, 119, 119, 0.1);
    }

    /* User Dropdown Styles */
    .user-dropdown-wrapper {
        position: relative;
    }

    .user-dropdown-toggle {
        background: none;
        border: none;
        color: #495057;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.5rem 0.75rem;
        border-radius: 8px;
        transition: all 0.2s ease;
        text-decoration: none;
        cursor: pointer;
    }

    .user-dropdown-toggle:hover {
        background: #f8f9fa;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #047, #025);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    .user-info {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 0.1rem;
    }

    .user-name {
        font-weight: 600;
        font-size: 0.95rem;
        color: #495057;
    }

    .user-role {
        font-size: 0.8rem;
        color: #6c757d;
    }

    .user-dropdown {
        position: absolute;
        top: 100%;
        right: 0;
        width: 220px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        border: 1px solid #e9ecef;
        z-index: 1100;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.3s ease;
        padding: 0.5rem;
    }

    .user-dropdown.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(5px);
    }

    .dropdown-item {
        padding: 0.75rem 1rem;
        border-radius: 8px;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        color: #495057;
        text-decoration: none;
        font-size: 0.9rem;
        border: none;
        background: none;
        width: 100%;
        text-align: left;
        cursor: pointer;
    }

    .dropdown-item:hover {
        background: #f8f9fa;
        color: #047;
    }

    .dropdown-item .bi {
        width: 18px;
        text-align: center;
        opacity: 0.7;
        font-size: 1.1rem;
    }

    .dropdown-divider {
        height: 1px;
        background: #f1f3f4;
        margin: 0.5rem 0;
        border: none;
    }

    /* Overlay for dropdowns */
    .dropdown-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: transparent;
        z-index: 1090;
    }

    .dropdown-overlay.show {
        display: block;
    }

    @media (max-width: 768px) {
        .header-content {
            padding: 0 1rem;
        }

        .user-info {
            display: none;
        }

        .header-actions {
            gap: 0.5rem;
        }

        .header-right {
            gap: 1rem;
        }

        .notification-dropdown {
            width: 320px;
            right: -50px;
        }

        .user-dropdown {
            width: 200px;
        }
    }

    @media (max-width: 480px) {
        .user-dropdown-toggle {
            padding: 0.5rem;
        }

        .notification-dropdown {
            width: 280px;
            right: -80px;
        }
    }
</style>

<header class="header">
    <div class="header-content">
        <div class="header-left">
            <button class="mobile-menu-toggle d-xl-none" id="mobileMenuToggle">
                <i class="bi bi-list"></i>
            </button>
            <div class="d-none d-md-block">
                <h5 class="mb-0 text-dark" id="pageTitle">
                    @yield('title', 'Dashboard')
                </h5>
            </div>
        </div>

        <div class="header-right">
            <div class="header-actions">
                <!-- Custom Notification Dropdown -->
                <div class="notification-wrapper">
                    <button class="notification-toggle" id="notificationToggle">
                        <i class="bi bi-bell"></i>
                        <span class="notification-badge">3</span>
                    </button>

                    <div class="notification-dropdown" id="notificationDropdown">
                        <div class="notification-header d-flex justify-content-between align-items-center">
                            <h6 class="notification-title">Notifications</h6>
                            <span class="notification-badge-header">3 new</span>
                        </div>

                        <div class="notification-list">
                            <div class="notification-item">
                                <div class="notification-icon primary">
                                    <i class="bi bi-person-plus"></i>
                                </div>
                                <div class="notification-content">
                                    <p class="notification-message">New subscriber registration from John Doe</p>
                                    <span class="notification-time">2 minutes ago</span>
                                </div>
                            </div>

                            <div class="notification-item">
                                <div class="notification-icon success">
                                    <i class="bi bi-credit-card"></i>
                                </div>
                                <div class="notification-content">
                                    <p class="notification-message">Payment received for Professional Plan</p>
                                    <span class="notification-time">1 hour ago</span>
                                </div>
                            </div>

                            <div class="notification-item">
                                <div class="notification-icon warning">
                                    <i class="bi bi-exclamation-triangle"></i>
                                </div>
                                <div class="notification-content">
                                    <p class="notification-message">Subscription expiring in 3 days</p>
                                    <span class="notification-time">5 hours ago</span>
                                </div>
                            </div>

                            <div class="notification-item">
                                <div class="notification-icon primary">
                                    <i class="bi bi-chat-dots"></i>
                                </div>
                                <div class="notification-content">
                                    <p class="notification-message">New support ticket received</p>
                                    <span class="notification-time">1 day ago</span>
                                </div>
                            </div>
                        </div>

                        <div class="notification-footer">
                            <button class="view-all-btn">
                                View All Notifications
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Custom User Dropdown -->
                <div class="user-dropdown-wrapper">
                    <button class="user-dropdown-toggle" id="userDropdownToggle">
                        <div class="user-avatar">
                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                        </div>
                        <div class="user-info d-none d-md-flex">
                            <span class="user-name">{{ Auth::user()->name ?? 'User' }}</span>
                            <span class="user-role">{{ Auth::user()->isAdmin() == 1 ? "Administrator" : "User"}}</span>
                        </div>
                        <i class="bi bi-chevron-down"></i>
                    </button>

                    <div class="user-dropdown" id="userDropdown">
                        <button class="dropdown-item">
                            <i class="bi bi-person"></i>
                            <span>Profile</span>
                        </button>
                        <button class="dropdown-item">
                            <i class="bi bi-gear"></i>
                            <span>Settings</span>
                        </button>
                        <button class="dropdown-item">
                            <i class="bi bi-bell"></i>
                            <span>Notifications</span>
                        </button>

                        <div class="dropdown-divider"></div>

                        <form method="POST" action="{{ route('logout') }}" class="w-100">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dropdown Overlay -->
    <div class="dropdown-overlay" id="dropdownOverlay"></div>
</header>

<script>
    class HeaderDropdownManager {
        constructor() {
            this.notificationToggle = document.getElementById('notificationToggle');
            this.notificationDropdown = document.getElementById('notificationDropdown');
            this.userDropdownToggle = document.getElementById('userDropdownToggle');
            this.userDropdown = document.getElementById('userDropdown');
            this.dropdownOverlay = document.getElementById('dropdownOverlay');

            this.activeDropdown = null;
            this.init();
        }

        init() {
            this.bindEvents();
            this.bindOutsideClick();
        }

        bindEvents() {
            // Notification dropdown
            this.notificationToggle.addEventListener('click', (e) => {
                e.stopPropagation();
                this.toggleDropdown(this.notificationDropdown);
            });

            // User dropdown
            this.userDropdownToggle.addEventListener('click', (e) => {
                e.stopPropagation();
                this.toggleDropdown(this.userDropdown);
            });

            // Overlay click
            this.dropdownOverlay.addEventListener('click', () => {
                this.closeAllDropdowns();
            });
        }

        bindOutsideClick() {
            document.addEventListener('click', (e) => {
                if (!e.target.closest('.notification-wrapper') &&
                    !e.target.closest('.user-dropdown-wrapper')) {
                    this.closeAllDropdowns();
                }
            });

            // Close on escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    this.closeAllDropdowns();
                }
            });
        }

        toggleDropdown(dropdown) {
            if (this.activeDropdown === dropdown) {
                this.closeDropdown(dropdown);
            } else {
                this.closeAllDropdowns();
                this.openDropdown(dropdown);
            }
        }

        openDropdown(dropdown) {
            dropdown.classList.add('show');
            this.dropdownOverlay.classList.add('show');
            this.activeDropdown = dropdown;

            // Add animation class for smooth appearance
            setTimeout(() => {
                dropdown.style.transition = 'all 0.3s ease';
            }, 10);
        }

        closeDropdown(dropdown) {
            dropdown.classList.remove('show');
            this.dropdownOverlay.classList.remove('show');
            this.activeDropdown = null;
        }

        closeAllDropdowns() {
            this.closeDropdown(this.notificationDropdown);
            this.closeDropdown(this.userDropdown);
        }
    }

    // Initialize when DOM is loaded
    document.addEventListener('DOMContentLoaded', () => {
        new HeaderDropdownManager();

        // Mark notifications as read when clicked
        const notificationItems = document.querySelectorAll('.notification-item');
        notificationItems.forEach(item => {
            item.addEventListener('click', function () {
                // Remove the new notification indicator
                const badge = document.querySelector('.notification-badge');
                if (badge) {
                    const count = parseInt(badge.textContent);
                    if (count > 1) {
                        badge.textContent = count - 1;
                    } else {
                        badge.style.display = 'none';
                    }
                }

                // Update header badge
                const headerBadge = document.querySelector('.notification-badge-header');
                if (headerBadge) {
                    const count = parseInt(headerBadge.textContent);
                    if (count > 1) {
                        headerBadge.textContent = count - 1 + ' new';
                    } else {
                        headerBadge.style.display = 'none';
                    }
                }
            });
        });
    });
</script>