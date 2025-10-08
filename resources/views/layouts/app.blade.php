<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.scss', 'resources/js/app.js'])

    <style>
        :root {
            --sidebar-width: 280px;
            --sidebar-collapsed-width: 80px;
            --header-height: 70px;
        }

        .main-wrapper {
            display: flex;
            min-height: 100vh;
            background-color: #f8f9fa;
        }

        .main-content {
            flex: 1;
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
            width: 100%;
            margin-left: var(--sidebar-width);
        }

        .main-content.full-width {
            margin-left: 0;
        }

        .content-area {
            flex: 1;
            padding: 2rem;
            overflow-y: auto;
        }

        /* Preloader Styles */
        .preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.5s ease, visibility 0.5s ease;
        }

        .preloader.hidden {
            opacity: 0;
            visibility: hidden;
        }

        .preloader-content {
            text-align: center;
        }

        .preloader-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #047;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }

        .preloader-text {
            color: #047;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .preloader-progress {
            width: 200px;
            height: 4px;
            background: #f0f0f0;
            border-radius: 2px;
            margin: 1rem auto 0;
            overflow: hidden;
        }

        .preloader-progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #047, #025);
            width: 0%;
            animation: progress 2s ease-in-out infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes progress {
            0% { width: 0%; }
            50% { width: 60%; }
            100% { width: 100%; }
        }

        /* Logo animation for preloader */
        .preloader-logo {
            font-size: 2rem;
            font-weight: 700;
            color: #047;
            margin-bottom: 1.5rem;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { opacity: 0.7; }
            50% { opacity: 1; }
            100% { opacity: 0.7; }
        }

        /* Fade in animation for main content */
        .main-content {
            opacity: 0;
            animation: fadeIn 0.5s ease-in-out 0.3s forwards;
        }

        @keyframes fadeIn {
            to {
                opacity: 1;
            }
        }

        @media (max-width: 1330px) {
            .main-content {
                margin-left: 0 !important;
            }
        }

        @media (max-width: 768px) {
            .content-area {
                padding: 1.5rem 0.2rem;
            }

            .preloader-spinner {
                width: 40px;
                height: 40px;
            }

            .preloader-text {
                font-size: 1rem;
            }

            .preloader-progress {
                width: 150px;
            }
        }
    </style>
</head>

<body>
    <!-- Preloader -->
    <div class="preloader" id="preloader">
        <div class="preloader-content">
            <div class="preloader-logo">
                {{ config('app.name', 'Subscribers') }}
            </div>
            <div class="preloader-spinner"></div>
            <div class="preloader-text">Loading...</div>
            <div class="preloader-progress">
                <div class="preloader-progress-bar"></div>
            </div>
        </div>
    </div>

    <div class="main-wrapper">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Main Content Area -->
        <div class="main-content" id="mainContent">
            <!-- Header -->
            @include('layouts.header')

            <!-- Page Content -->
            <main class="content-area">
                @hasSection('content')
                    @includeIf('components.alerts')
                    @yield('content')
                @else
                    @includeIf('components.alerts')
                    {{ $slot ?? '' }}
                @endif
            </main>
        </div>
    </div>

    <!-- Global Toast Container -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1080">
        <div id="globalToast" class="toast align-items-center text-bg-dark border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="globalToastBody"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- Global Confirm Modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalTitle">Please Confirm</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="confirmModalMessage">Are you sure?</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmModalOk">Yes, continue</button>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery and DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

    <script>
        // Preloader functionality
        class Preloader {
            constructor() {
                this.preloader = document.getElementById('preloader');
                this.minDisplayTime = 1000; // Minimum display time in milliseconds
                this.startTime = Date.now();
                this.init();
            }

            init() {
                // Hide preloader when page is fully loaded
                window.addEventListener('load', () => {
                    this.hide();
                });

                // Fallback: hide preloader after maximum time
                setTimeout(() => {
                    if (this.preloader && !this.preloader.classList.contains('hidden')) {
                        this.hide();
                    }
                }, 5000); // Maximum 5 seconds

                // Handle page transitions
                this.setupPageTransitions();
            }

            hide() {
                const elapsedTime = Date.now() - this.startTime;
                const remainingTime = Math.max(0, this.minDisplayTime - elapsedTime);

                setTimeout(() => {
                    if (this.preloader) {
                        this.preloader.classList.add('hidden');
                        
                        // Remove from DOM after animation
                        setTimeout(() => {
                            if (this.preloader && this.preloader.parentNode) {
                                this.preloader.parentNode.removeChild(this.preloader);
                            }
                        }, 500);
                    }
                }, remainingTime);
            }

            setupPageTransitions() {
                // Show preloader during page navigation (if using traditional navigation)
                document.addEventListener('click', (e) => {
                    const link = e.target.closest('a');
                    if (link && link.href && !link.target && !link.hasAttribute('download')) {
                        const href = link.getAttribute('href');
                        if (href && !href.startsWith('#') && !href.startsWith('javascript:')) {
                            // For traditional navigation, you might want to show preloader
                            // This is useful if you're not using SPA
                        }
                    }
                });

                // Handle browser back/forward buttons
                window.addEventListener('beforeunload', () => {
                    // You can show a minimal loader here if needed
                });
            }
        }

        // Initialize preloader when DOM is ready
        document.addEventListener('DOMContentLoaded', () => {
            new Preloader();
        });

        // Handle main content adjustment when sidebar toggles
        window.addEventListener('sidebarToggle', function (event) {
            const mainContent = document.getElementById('mainContent');
            if (event.detail.collapsed) {
                mainContent.classList.add('expanded');
            } else {
                mainContent.classList.remove('expanded');
            }
        });

        // Close mobile sidebar when clicking on a nav link
        document.addEventListener('click', function (e) {
            if (e.target.matches('.nav-link') && window.innerWidth <= 1330) {
                document.dispatchEvent(new CustomEvent('closeSidebar'));
            }
        });

        // Handle AJAX requests with loading states
        if (typeof $ !== 'undefined') {
            $(document).ajaxStart(function() {
                // Show a mini loader for AJAX requests
                if (!document.getElementById('ajax-loader')) {
                    const ajaxLoader = document.createElement('div');
                    ajaxLoader.id = 'ajax-loader';
                    ajaxLoader.innerHTML = `
                        <div style="position: fixed; top: 20px; right: 20px; background: #047; color: white; padding: 10px 15px; border-radius: 5px; z-index: 10000; display: flex; align-items: center; gap: 8px;">
                            <div style="width: 16px; height: 16px; border: 2px solid transparent; border-top: 2px solid white; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                            Loading...
                        </div>
                    `;
                    document.body.appendChild(ajaxLoader);
                }
            });

            $(document).ajaxStop(function() {
                const ajaxLoader = document.getElementById('ajax-loader');
                if (ajaxLoader) {
                    ajaxLoader.remove();
                }
            });
        }

        // AppUI helpers (toast + confirm) - wait for Bootstrap to be available
        (function initAppUIWhenReady() {
            function init() {
                const toastEl = document.getElementById('globalToast');
                const toastBody = document.getElementById('globalToastBody');
                let toast;
                if (toastEl && window.bootstrap?.Toast) {
                    toast = window.bootstrap.Toast.getOrCreateInstance(toastEl, { delay: 3000 });
                }

                function showToast(message, variant = 'dark') {
                    if (!toastEl || !toastBody || !toast) return;
                    toastEl.className = `toast align-items-center text-bg-${variant} border-0`;
                    toastBody.textContent = message;
                    toast.show();
                }

                function confirm(message, title = 'Please Confirm') {
                    return new Promise((resolve) => {
                        const modalEl = document.getElementById('confirmModal');
                        const okBtn = document.getElementById('confirmModalOk');
                        const titleEl = document.getElementById('confirmModalTitle');
                        const msgEl = document.getElementById('confirmModalMessage');
                        if (!modalEl || !okBtn || !titleEl || !msgEl || !window.bootstrap?.Modal) return resolve(false);

                        titleEl.textContent = title;
                        msgEl.textContent = message;
                        const modal = window.bootstrap.Modal.getOrCreateInstance(modalEl);

                        function cleanup(result) {
                            okBtn.removeEventListener('click', onOk);
                            modalEl.removeEventListener('hidden.bs.modal', onCancel);
                            resolve(result);
                        }

                        function onOk() {
                            modal.hide();
                            cleanup(true);
                        }

                        function onCancel() {
                            cleanup(false);
                        }

                        okBtn.addEventListener('click', onOk, { once: true });
                        modalEl.addEventListener('hidden.bs.modal', onCancel, { once: true });
                        modal.show();
                    });
                }

                window.AppUI = { showToast, confirm };
            }

            if (window.bootstrap) {
                init();
            } else {
                document.addEventListener('bootstrap:ready', init, { once: true });
            }
        })();
    </script>

    <!-- Stack for additional scripts -->
    @stack('scripts')
</body>

</html>