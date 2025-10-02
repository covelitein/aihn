<x-app-layout>
    <div class="container-fluid">
        <!-- Stats Cards -->
        <div class="row mb-4">
            @php
                $statConfig = [
                    'total' => ['color' => 'primary', 'icon' => 'people', 'label' => 'Total Applications', 'description' => 'All time applications'],
                    'pending' => ['color' => 'warning', 'icon' => 'clock-history', 'label' => 'Pending Applications', 'description' => 'Needs review'],
                    'under_review' => ['color' => 'info', 'icon' => 'eye', 'label' => 'Under Review', 'description' => 'In progress'],
                    'approved' => ['color' => 'success', 'icon' => 'check-circle', 'label' => 'Approved', 'description' => 'Active subscribers'],
                    'rejected' => ['color' => 'danger', 'icon' => 'x-circle', 'label' => 'Rejected', 'description' => 'Not approved'],
                ];
            @endphp

            @foreach($statConfig as $key => $config)
                @if(isset($stats[$key]))
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="card-label text-{{ $config['color'] }} mb-2">
                                            {{ $config['label'] }}
                                        </h6>
                                        <h3 class="stat-number mb-2">{{ $stats[$key] }}</h3>
                                        <p class="stat-description text-muted mb-0">
                                            {{ $config['description'] }}
                                        </p>
                                    </div>
                                    <div class="stat-icon bg-{{ $config['color'] }} bg-opacity-10">
                                        <i class="bi bi-{{ $config['icon'] }} text-{{ $config['color'] }}"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <!-- Datatable Card -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0 fw-semibold text-dark">Subscription Applications</h5>
                    </div>
                    <div class="col-md-6">
                        <div class="row g-2 justify-content-end">
                            <div class="col-md-4">
                                <select class="form-select form-select-sm border" id="statusFilter">
                                    <option value="">All Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="under_review">Under Review</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="date" class="form-control form-control-sm border" id="dateFromFilter" placeholder="From Date">
                            </div>
                            <div class="col-md-4">
                                <input type="date" class="form-control form-control-sm border" id="dateToFilter" placeholder="To Date">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <!-- Loading Overlay -->
                <div id="loadingOverlay" class="text-center py-5" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-muted mt-2">Loading applications...</p>
                </div>

                <!-- Table Container -->
                <div id="tableWrapper" style="display: none;">
                    <div class="table-container">
                        <table class="table table-hover mb-0" id="subscriptionsTable">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="15%">User</th>
                                    <th width="20%">Email</th>
                                    <th width="15%">Plan</th>
                                    <th width="10%">Amount</th>
                                    <th width="10%">Status</th>
                                    <th width="15%">Submitted</th>
                                    <th width="10%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function () {
                // Show loading overlay initially
                $('#loadingOverlay').show();
                $('#tableWrapper').hide();

                // Initialize DataTable with proper scroll configuration
                const table = $('#subscriptionsTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('admin.subscriptions.index') }}',
                        data: function (d) {
                            d.status = $('#statusFilter').val();
                            d.date_from = $('#dateFromFilter').val();
                            d.date_to = $('#dateToFilter').val();
                        },
                        beforeSend: function () {
                            $('#loadingOverlay').show();
                            $('#tableWrapper').hide();
                        },
                        complete: function () {
                            $('#loadingOverlay').hide();
                            $('#tableWrapper').show();
                        },
                        error: function (xhr, error, thrown) {
                            console.log('AJAX Error:', error);
                            $('#loadingOverlay').hide();
                            $('#tableWrapper').show();
                        }
                    },
                    columns: [
                        {
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        },
                        {
                            data: 'user_name',
                            name: 'user.name',
                            className: 'fw-semibold'
                        },
                        {
                            data: 'user_email',
                            name: 'user.email',
                            className: 'text-muted'
                        },
                        {
                            data: 'plan_name',
                            name: 'plan.name',
                            className: 'fw-medium'
                        },
                        {
                            data: 'amount_formatted',
                            name: 'amount_paid',
                            className: 'text-end fw-semibold'
                        },
                        {
                            data: 'status_badge',
                            name: 'status',
                            orderable: false,
                            className: 'text-center'
                        },
                        {
                            data: 'submitted_at_formatted',
                            name: 'submitted_at',
                            className: 'text-muted'
                        },
                        {
                            data: 'actions',
                            name: 'actions',
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        }
                    ],
                    order: [[0, 'desc']],
                    pageLength: 25,
                    lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                    dom: '<"row mx-2 my-3"<"col-md-6"l><"col-md-6"f>>t<"row mx-2 my-3"<"col-md-5"i><"col-md-7"p>>',
                    scrollX: true,
                    scrollY: '400px',
                    scrollCollapse: true,
                    fixedHeader: true,
                    language: {
                        processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
                        emptyTable: '<div class="text-center py-4"><i class="bi bi-inbox display-4 text-muted"></i><p class="text-muted mt-2">No applications found</p></div>',
                        zeroRecords: '<div class="text-center py-4"><i class="bi bi-search display-4 text-muted"></i><p class="text-muted mt-2">No matching applications found</p></div>',
                        info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                        infoEmpty: 'Showing 0 to 0 of 0 entries',
                        infoFiltered: '(filtered from _MAX_ total entries)',
                        search: '',
                        searchPlaceholder: 'Search applications...',
                        paginate: {
                            first: '<i class="bi bi-chevron-double-left"></i>',
                            last: '<i class="bi bi-chevron-double-right"></i>',
                            next: '<i class="bi bi-chevron-right"></i>',
                            previous: '<i class="bi bi-chevron-left"></i>'
                        }
                    },
                    initComplete: function (settings, json) {
                        $('#loadingOverlay').hide();
                        $('#tableWrapper').show();
                        
                        // Adjust column widths after initialization
                        this.api().columns.adjust();
                    },
                    drawCallback: function (settings) {
                        // Re-adjust column widths on each draw
                        this.api().columns.adjust();
                    }
                });

                // Filter handlers
                $('#statusFilter, #dateFromFilter, #dateToFilter').on('change', function () {
                    $('#loadingOverlay').show();
                    $('#tableWrapper').hide();
                    table.ajax.reload(function() {
                        // Re-adjust columns after reload
                        table.columns.adjust();
                    });
                });

                // Handle window resize
                $(window).on('resize', function() {
                    table.columns.adjust();
                });

                // Add search box styling
                $('.dataTables_filter input').addClass('form-control form-control-sm');
                $('.dataTables_length select').addClass('form-select form-select-sm');
            });
        </script>

        <style>
            /* Stat Cards */
            .stat-card {
                border-radius: 12px;
                transition: all 0.3s ease;
            }

            .stat-card:hover {
                transform: translateY(-3px);
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1) !important;
            }

            .card-label {
                font-size: 0.875rem;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .stat-number {
                font-size: 2rem;
                font-weight: 700;
                color: #2c3e50;
                margin-bottom: 0.5rem;
            }

            .stat-description {
                font-size: 0.8rem;
                margin-bottom: 0;
            }

            .stat-icon {
                width: 50px;
                height: 50px;
                border-radius: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.5rem;
            }

            /* Table Container */
            .table-container {
                position: relative;
                overflow: auto;
            }

            /* DataTable Scroll Container */
            .dataTables_scroll {
                position: relative;
            }

            .dataTables_scrollHead {
                background-color: #f8f9fa;
            }

            .dataTables_scrollHead thead th {
                border-bottom: 2px solid #e9ecef;
                position: relative;
            }

            .dataTables_scrollBody {
                border-bottom: 1px solid #e9ecef;
            }

            /* Ensure header and body columns align */
            .dataTables_scrollHeadInner {
                width: 100% !important;
            }

            .dataTables_scrollHeadInner table {
                width: 100% !important;
                margin-bottom: 0 !important;
            }

            .dataTables_scrollBody table {
                width: 100% !important;
                margin-top: 0 !important;
            }

            /* Table Styling */
            #subscriptionsTable {
                width: 100% !important;
                margin-bottom: 0;
                table-layout: fixed;
            }

            #subscriptionsTable thead th {
                background-color: #f8f9fa;
                font-weight: 600;
                font-size: 0.875rem;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                color: #6c757d;
                padding: 1rem 0.75rem;
                white-space: nowrap;
                border-bottom: 2px solid #e9ecef;
                position: sticky;
                top: 0;
                z-index: 10;
            }

            #subscriptionsTable tbody td {
                padding: 1rem 0.75rem;
                vertical-align: middle;
                border-bottom: 1px solid #f8f9fa;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            #subscriptionsTable tbody tr:hover {
                background-color: #f8f9fa;
            }

            /* Fixed Header Styling */
            .dataTables_scrollHead {
                border-bottom: 2px solid #e9ecef;
            }

            .dataTables_scrollHead thead th {
                background-color: #f8f9fa;
            }

            /* Loading overlay */
            #loadingOverlay {
                background: rgba(255, 255, 255, 0.95);
                border-radius: 0 0 12px 12px;
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                z-index: 20;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
            }

            /* DataTables wrapper adjustments */
            .dataTables_wrapper {
                position: relative;
            }

            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter {
                padding: 1rem;
                margin: 0;
            }

            .dataTables_wrapper .dataTables_info {
                padding: 1rem;
                color: #6c757d;
                font-size: 0.875rem;
            }

            .dataTables_wrapper .dataTables_paginate {
                padding: 1rem;
            }

            /* Custom scrollbar */
            .table-container::-webkit-scrollbar {
                height: 8px;
                width: 8px;
            }

            .table-container::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 4px;
            }

            .table-container::-webkit-scrollbar-thumb {
                background: #c1c1c1;
                border-radius: 4px;
            }

            .table-container::-webkit-scrollbar-thumb:hover {
                background: #a8a8a8;
            }

            /* Ensure proper card styling */
            .card {
                border-radius: 12px;
            }

            .card-header {
                border-radius: 12px 12px 0 0 !important;
            }

            /* Badge styling */
            .badge {
                font-size: 0.75rem;
                padding: 0.35rem 0.65rem;
                border-radius: 6px;
            }

            /* Form controls */
            .form-select-sm, .form-control-sm {
                border-radius: 6px;
                border: 1px solid #dee2e6;
            }

            /* Make sure the table wrapper has proper dimensions */
            #tableWrapper {
                min-height: 400px;
                position: relative;
            }
        </style>
    @endpush
</x-app-layout>