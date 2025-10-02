@props([
    'id' => 'dataTable',
    'columns' => [],
    'ajaxUrl' => '',
    'filters' => [],
    'showExport' => true,
    'showSearch' => true,
    'showLength' => true,
    'cardView' => true,
    'title' => 'Data Table',
    'createUrl' => null,
    'createLabel' => 'Add New',
])

<style>
    .dataTables-wrapper {
        position: relative;
    }

    .dataTables-filter-section {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e9ecef;
    }

    .filter-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .filter-actions {
        display: flex;
        gap: 0.5rem;
        justify-content: flex-end;
        flex-wrap: wrap;
    }

    .table-responsive {
        border-radius: 8px;
        overflow: hidden;
    }

    .dataTable {
        width: 100% !important;
        margin: 0 !important;
    }

    .dataTables_length select {
        border-radius: 4px;
        border: 1px solid #ddd;
        padding: 0.25rem 0.5rem;
    }

    .dataTables_filter input {
        border-radius: 4px;
        border: 1px solid #ddd;
        padding: 0.25rem 0.5rem;
        margin-left: 0.5rem;
    }

    .badge-filter {
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .badge-filter:hover {
        transform: translateY(-1px);
    }

    .export-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .table-actions {
        display: flex;
        gap: 0.25rem;
        flex-wrap: wrap;
    }

    .btn-table-action {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    /* Mobile responsive styles */
    @media (max-width: 768px) {
        .filter-row {
            grid-template-columns: 1fr;
        }

        .filter-actions {
            justify-content: stretch;
        }

        .filter-actions .btn {
            flex: 1;
            min-width: 120px;
        }

        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            float: none;
            text-align: center;
            margin-bottom: 1rem;
        }

        .dataTables_wrapper .dataTables_paginate {
            float: none;
            text-align: center;
        }

        .table-actions {
            flex-direction: column;
            gap: 0.5rem;
        }

        .btn-table-action {
            width: 100%;
            text-align: center;
        }
    }

    /* Dark mode support */
    @media (prefers-color-scheme: dark) {
        .dataTables-filter-section {
            background: #2d3748;
            border-color: #4a5568;
        }

        .dataTables_wrapper .dataTables_filter input {
            background: #2d3748;
            border-color: #4a5568;
            color: white;
        }

        .dataTables_wrapper .dataTables_length select {
            background: #2d3748;
            border-color: #4a5568;
            color: white;
        }
    }

    /* Loading overlay */
    .dataTables_processing {
        background: rgba(255, 255, 255, 0.9) !important;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    /* Custom checkbox for row selection */
    .dt-checkboxes {
        width: 18px;
        height: 18px;
    }
</style>

<div class="dataTables-wrapper" id="{{ $id }}-wrapper">
    @if($cardView)
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">{{ $title }}</h5>
                
                @if($createUrl)
                <a href="{{ $createUrl }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-1"></i>{{ $createLabel }}
                </a>
                @endif
            </div>
        </div>
        <div class="card-body">
    @endif

    <!-- Filters Section -->
    @if(count($filters) > 0)
    <div class="dataTables-filter-section">
        <form id="{{ $id }}-filters">
            <div class="filter-row">
                @foreach($filters as $filter)
                    <div class="filter-group">
                        <label class="form-label small fw-bold text-muted mb-1">{{ $filter['label'] }}</label>
                        @if($filter['type'] === 'select')
                            <select class="form-select form-select-sm" name="{{ $filter['name'] }}" id="{{ $filter['name'] }}-filter">
                                <option value="">All {{ $filter['label'] }}</option>
                                @foreach($filter['options'] as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        @elseif($filter['type'] === 'date')
                            <input type="date" class="form-control form-control-sm" 
                                   name="{{ $filter['name'] }}" id="{{ $filter['name'] }}-filter">
                        @elseif($filter['type'] === 'daterange')
                            <div class="input-group input-group-sm">
                                <input type="date" class="form-control" name="{{ $filter['name'] }}_from" 
                                       placeholder="From">
                                <span class="input-group-text">to</span>
                                <input type="date" class="form-control" name="{{ $filter['name'] }}_to" 
                                       placeholder="To">
                            </div>
                        @elseif($filter['type'] === 'text')
                            <input type="text" class="form-control form-control-sm" 
                                   name="{{ $filter['name'] }}" placeholder="Search {{ $filter['label'] }}...">
                        @elseif($filter['type'] === 'number')
                            <input type="number" class="form-control form-control-sm" 
                                   name="{{ $filter['name'] }}" placeholder="{{ $filter['label'] }}...">
                        @endif
                    </div>
                @endforeach
            </div>
            
            <div class="filter-actions">
                <button type="button" class="btn btn-primary btn-sm" onclick="applyFilters('{{ $id }}')">
                    <i class="bi bi-funnel me-1"></i>Apply Filters
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetFilters('{{ $id }}')">
                    <i class="bi bi-arrow-clockwise me-1"></i>Reset
                </button>
                
                @if($showExport)
                <div class="export-buttons">
                    <button type="button" class="btn btn-success btn-sm" onclick="exportData('{{ $id }}', 'excel')">
                        <i class="bi bi-file-earmark-excel me-1"></i>Excel
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" onclick="exportData('{{ $id }}', 'pdf')">
                        <i class="bi bi-file-earmark-pdf me-1"></i>PDF
                    </button>
                </div>
                @endif
            </div>
        </form>
    </div>
    @endif

    <!-- Quick Filter Badges -->
    <div class="quick-filters mb-3" id="{{ $id }}-quick-filters">
        <!-- Dynamic quick filters will be added here -->
    </div>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-striped table-hover" id="{{ $id }}">
            <thead>
                <tr>
                    @if(isset($columns['checkbox']))
                    <th width="20">
                        <input type="checkbox" id="select-all-{{ $id }}">
                    </th>
                    @endif
                    
                    @foreach($columns as $key => $column)
                        @if($key !== 'checkbox' && $key !== 'actions')
                        <th {{ isset($column['width']) ? 'width='.$column['width'] : '' }}>
                            {{ $column['title'] ?? ucfirst(str_replace('_', ' ', $key)) }}
                        </th>
                        @endif
                    @endforeach
                    
                    @if(isset($columns['actions']))
                    <th width="100">Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                <!-- Data will be loaded via AJAX -->
            </tbody>
        </table>
    </div>

    @if($cardView)
        </div>
    </div>
    @endif
</div>

<script>
// Global datatable instances
const dataTableInstances = {};

function initializeDataTable(tableId, options = {}) {
    const defaultOptions = {
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: '{{ $ajaxUrl }}',
            data: function (d) {
                // Add custom filters
                const filterForm = $(`#${tableId}-filters`);
                if (filterForm.length) {
                    $.each(filterForm.serializeArray(), function (i, item) {
                        d[item.name] = item.value;
                    });
                }
                
                // Add quick filters
                const quickFilters = $(`#${tableId}-quick-filters .badge-filter.active`);
                quickFilters.each(function() {
                    const filterName = $(this).data('filter');
                    const filterValue = $(this).data('value');
                    d[filterName] = filterValue;
                });
            }
        },
        columns: {!! json_encode(array_map(function($col, $key) {
            if ($key === 'checkbox') {
                return [
                    'data' => 'checkbox',
                    'name' => 'checkbox',
                    'orderable' => false,
                    'searchable' => false,
                    'className' => 'dt-checkboxes-cell'
                ];
            } elseif ($key === 'actions') {
                return [
                    'data' => 'actions',
                    'name' => 'actions',
                    'orderable' => false,
                    'searchable' => false,
                    'className' => 'text-center'
                ];
            } else {
                return [
                    'data' => $key,
                    'name' => $key,
                    'title' => $col['title'] ?? ucfirst(str_replace('_', ' ', $key)),
                    'orderable' => $col['orderable'] ?? true,
                    'searchable' => $col['searchable'] ?? true,
                    'className' => $col['className'] ?? '',
                    'render' => $col['render'] ?? null
                ];
            }
        }, $columns, array_keys($columns))) !!},
        order: [[0, 'desc']],
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        language: {
            processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
            emptyTable: 'No data available in table',
            info: 'Showing _START_ to _END_ of _TOTAL_ entries',
            infoEmpty: 'Showing 0 to 0 of 0 entries',
            infoFiltered: '(filtered from _MAX_ total entries)',
            lengthMenu: 'Show _MENU_ entries',
            loadingRecords: 'Loading...',
            search: 'Search:',
            zeroRecords: 'No matching records found',
            paginate: {
                first: 'First',
                last: 'Last',
                next: 'Next',
                previous: 'Previous'
            }
        },
        initComplete: function(settings, json) {
            updateQuickFilters(tableId, json);
        },
        drawCallback: function(settings) {
            // Update select all checkbox
            $(`#select-all-${tableId}`).prop('checked', false);
            
            // Add tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    };

    const finalOptions = {...defaultOptions, ...options};
    dataTableInstances[tableId] = $(`#${tableId}`).DataTable(finalOptions);

    // Select all functionality
    $(`#select-all-${tableId}`).on('click', function() {
        const isChecked = this.checked;
        $(`#${tableId} tbody input[type="checkbox"]`).prop('checked', isChecked);
    });

    return dataTableInstances[tableId];
}

function applyFilters(tableId) {
    if (dataTableInstances[tableId]) {
        dataTableInstances[tableId].ajax.reload();
    }
}

function resetFilters(tableId) {
    const filterForm = $(`#${tableId}-filters`);
    filterForm[0].reset();
    
    // Clear quick filters
    $(`#${tableId}-quick-filters .badge-filter`).removeClass('active');
    
    applyFilters(tableId);
}

function updateQuickFilters(tableId, json) {
    const quickFiltersContainer = $(`#${tableId}-quick-filters`);
    quickFiltersContainer.empty();

    if (json && json.quickFilters) {
        let html = '<span class="small text-muted me-2">Quick Filters:</span>';
        json.quickFilters.forEach(filter => {
            html += `<span class="badge badge-filter bg-${filter.active ? 'primary' : 'light text-dark'} me-1 mb-1" 
                     data-filter="${filter.name}" data-value="${filter.value}">
                     ${filter.label} ${filter.count ? `(${filter.count})` : ''}
                     </span>`;
        });
        quickFiltersContainer.html(html);

        // Add click event to quick filters
        $(`#${tableId}-quick-filters .badge-filter`).on('click', function() {
            $(this).toggleClass('active bg-primary bg-light text-dark');
            applyFilters(tableId);
        });
    }
}

function exportData(tableId, type) {
    let url = '{{ $ajaxUrl }}'.replace('/datatable', `/export/${type}`);
    
    const filterForm = $(`#${tableId}-filters`);
    const params = new URLSearchParams();
    
    if (filterForm.length) {
        $.each(filterForm.serializeArray(), function (i, item) {
            params.append(item.name, item.value);
        });
    }

    window.location.href = `${url}?${params.toString()}`;
}

// Initialize when document is ready
document.addEventListener('DOMContentLoaded', function() {
    initializeDataTable('{{ $id }}');
});

// Export functions for use in other scripts
window.DataTableComponent = {
    getInstance: (tableId) => dataTableInstances[tableId],
    reload: (tableId) => dataTableInstances[tableId]?.ajax.reload(),
    applyFilters,
    resetFilters
};
</script>