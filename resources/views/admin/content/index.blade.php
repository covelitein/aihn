<x-app-layout>
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col">
                <div class="page-header mb-4">
                    <h4 class="mb-1 fw-bold">Content Management</h4>
                    <p class="text-muted mb-0">Manage and organize your content across different plans</p>
                </div>

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <!-- Filters -->
                    <div class="filter-section">
                        <form id="contentFilters" class="d-flex flex-wrap gap-2 align-items-end">
                            <div class="filter-group">
                                <label class="form-label small text-muted mb-1">Search</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-search text-muted"></i>
                                    </span>
                                    <input type="text" id="searchInput" class="form-control border-start-0"
                                        placeholder="Search content...">
                                </div>
                            </div>

                            <div class="filter-group">
                                <label class="form-label small text-muted mb-1">Content Type</label>
                                <select id="typeFilter" class="form-select form-select-sm">
                                    <option value="">All Types</option>
                                    @foreach($contentTypes as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="filter-group">
                                <label class="form-label small text-muted mb-1">Plan</label>
                                <select id="planFilter" class="form-select form-select-sm">
                                    <option value="">All Plans</option>
                                    @foreach($plans as $plan)
                                        <option value="{{ $plan->id }}">{{ $plan->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="filter-group">
                                <label class="form-label small text-muted mb-1">Status</label>
                                <select id="publishedFilter" class="form-select form-select-sm">
                                    <option value="">All Status</option>
                                    <option value="yes">Published</option>
                                    <option value="no">Draft</option>
                                </select>
                            </div>

                            <div class="filter-group">
                                <button type="button" id="applyFilters" class="btn btn-primary btn-sm">
                                    <i class="bi bi-funnel me-1"></i> Apply
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Create Button -->
                    <a href="{{ route('admin.content.create') }}" class="btn btn-success btn-sm">
                        <i class="bi bi-plus-circle me-1"></i> Create Content
                    </a>
                </div>
            </div>
        </div>

        <!-- AJAX Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="contentTable">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">#</th>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Author</th>
                                <th>Plans</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <style>
        .page-header {
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding-bottom: 1rem;
        }

        .filter-section {
            background: var(--light-bg);
            border-radius: 8px;
            padding: 1rem;
            flex-grow: 1;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-group .form-label {
            font-size: 0.75rem;
            font-weight: 500;
            margin-bottom: 0.25rem;
        }

        .form-select-sm,
        .form-control {
            border-radius: 6px;
            border: 1px solid #dee2e6;
            font-size: 0.875rem;
        }

        .form-select-sm:focus,
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.15);
        }

        .input-group-sm .input-group-text {
            border-radius: 6px 0 0 6px;
            background-color: #f8f9fa;
            border-right: none;
        }

        .input-group-sm .form-control {
            border-radius: 0 6px 6px 0;
            border-left: none;
        }


        .card {
            border-radius: 10px;
            overflow: hidden;
        }

        .table {
            margin-bottom: 0;
            table-layout: fixed;
            width: 100%;
        }

        .table thead th {
            border-bottom: 1px solid #dee2e6;
            font-weight: 600;
            font-size: 0.875rem;
            color: #495057;
            padding: 1rem 0.75rem;
            background-color: #f8f9fa;
        }

        .table thead th:nth-child(1) {
            width: 5%;
        }

        /* # */
        .table thead th:nth-child(2) {
            width: 25%;
        }

        /* Title */
        .table thead th:nth-child(3) {
            width: 12%;
        }

        /* Type */
        .table thead th:nth-child(4) {
            width: 15%;
        }

        /* Author */
        .table thead th:nth-child(5) {
            width: 15%;
        }

        /* Plans */
        .table thead th:nth-child(6) {
            width: 8%;
        }

        /* Status */
        .table thead th:nth-child(7) {
            width: 10%;
        }

        /* Created */
        .table thead th:nth-child(8) {
            width: 10%;
        }

        /* Actions */

        .table tbody td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f3f4;
            font-size: 0.875rem;
        }

        /* Title truncation */
        .table tbody td:nth-child(2) {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 0;
        }

        /* Ensure other cells also handle overflow properly */
        .table tbody td:nth-child(1),
        .table tbody td:nth-child(3),
        .table tbody td:nth-child(4),
        .table tbody td:nth-child(7) {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .table tbody tr:hover {
            background-color: rgba(67, 97, 238, 0.02);
        }

        .table-hover tbody tr:hover {
            background-color: rgba(67, 97, 238, 0.03);
        }

        /* Plan badges */
        .plan-badge {
            display: inline-block;
            background: rgba(67, 97, 238, 0.1);
            color: var(--primary-color);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            margin: 0.1rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }

        /* Plan badges container */
        .table tbody td:nth-child(5) {
            max-width: 0;
        }

        .table tbody td:nth-child(5) .d-flex {
            flex-wrap: wrap;
            gap: 0.25rem;
        }

        /* Action buttons */
        .action-buttons .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            border-radius: 4px;
            margin-left: 0.25rem;
            white-space: nowrap;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .filter-section {
                width: 100%;
            }

            #contentFilters {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-group {
                margin-bottom: 0.5rem;
            }

            .d-flex.justify-content-between {
                flex-direction: column;
                gap: 1rem;
            }

            .table-responsive {
                font-size: 0.8rem;
            }

            /* Adjust column widths for mobile */
            .table thead th:nth-child(2) {
                width: 20%;
            }

            /* Title */
            .table thead th:nth-child(4) {
                width: 12%;
            }

            /* Author */
            .table thead th:nth-child(5) {
                width: 18%;
            }

            /* Plans */
        }

        @media (max-width: 576px) {

            .table thead th:nth-child(4),
            .table tbody td:nth-child(4) {
                display: none;
                /* Hide Author column on very small screens */
            }

            .table thead th:nth-child(2) {
                width: 30%;
            }

            /* Give more space to Title */
        }

        /* DataTables customization */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            padding: 1rem;
            font-size: 0.875rem;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 4px !important;
            margin: 0 2px;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
        }

        /* Ensure no horizontal scroll */
        .table-responsive {
            overflow-x: hidden;
        }

        body {
            overflow-x: hidden;
        }
    </style>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const table = $('#contentTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('admin.content.index') }}',
                        data: function (d) {
                            d.search = document.getElementById('searchInput').value;
                            d.type = document.getElementById('typeFilter').value;
                            d.plan_id = document.getElementById('planFilter').value;
                            d.published = document.getElementById('publishedFilter').value;
                        }
                    },
                    columns: [
                        {
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false,
                            width: '5%',
                            className: 'ps-4'
                        },
                        {
                            data: 'title',
                            name: 'title',
                            render: function (data, type, row) {
                                // Truncate title to prevent overflow
                                if (type === 'display' && data && data.length > 50) {
                                    return '<span title="' + data + '">' + data.substring(0, 50) + '...</span>';
                                }
                                return data;
                            }
                        },
                        {
                            data: 'type',
                            name: 'type',
                            render: function (data, type, row) {
                                if (type === 'display' && data && data.length > 15) {
                                    return '<span title="' + data + '">' + data.substring(0, 15) + '...</span>';
                                }
                                return data;
                            }
                        },
                        {
                            data: 'author_name',
                            name: 'author.name',
                            orderable: false,
                            render: function (data, type, row) {
                                if (type === 'display' && data && data.length > 15) {
                                    return '<span title="' + data + '">' + data.substring(0, 15) + '...</span>';
                                }
                                return data;
                            }
                        },
                        {
                            data: 'plans',
                            name: 'accessible_plans',
                            orderable: false,
                            searchable: false,
                            render: function (data, type, row) {
                                if (type === 'display' && data) {
                                    return '<div class="d-flex flex-wrap gap-1">' + data + '</div>';
                                }
                                return data;
                            }
                        },
                        {
                            data: 'published_badge',
                            name: 'is_published',
                            orderable: false,
                            searchable: false,
                        },
                        {
                            data: 'created_at_formatted',
                            name: 'created_at',
                            render: function (data, type, row) {
                                if (type === 'display' && data && data.length > 10) {
                                    return '<span title="' + data + '">' + data + '</span>';
                                }
                                return data;
                            }
                        },
                        {
                            data: 'actions',
                            name: 'actions',
                            orderable: false,
                            searchable: false,
                            className: 'text-end pe-4 action-buttons'
                        }
                    ],
                    order: [[1, 'asc']],
                    pageLength: 10,
                    lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                    language: {
                        emptyTable: 'No content found',
                        processing: '<div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading...'
                    },
                    autoWidth: false,
                    drawCallback: function (settings) {
                        // Add any additional UI enhancements after table draw
                    }
                });

                // Filter event handlers
                document.getElementById('applyFilters').addEventListener('click', function () {
                    table.ajax.reload();
                });

                document.getElementById('typeFilter').addEventListener('change', function () {
                    table.ajax.reload();
                });

                document.getElementById('planFilter').addEventListener('change', function () {
                    table.ajax.reload();
                });

                document.getElementById('publishedFilter').addEventListener('change', function () {
                    table.ajax.reload();
                });

                document.getElementById('searchInput').addEventListener('keydown', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        table.ajax.reload();
                    }
                });

                // Debounced search for better performance
                let searchTimeout;
                document.getElementById('searchInput').addEventListener('input', function (e) {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(function () {
                        table.ajax.reload();
                    }, 500);
                });
            });
        </script>
    @endpush

</x-app-layout>