@extends('layouts.default')
@section('page_title')
    List Return Management
@stop
@section('content')
    <!-- Main Content Card -->
    <div class="mt-6">
        <div class="card">
            <h4 class="card-header">Kelola Return Management</h4>
            <div class="card-body">
                <!-- Advanced Search Form -->
                <div class="row mb-4">
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label">Tanggal Return:</label>
                        <input type="text" id="date-range-picker" class="form-control" />
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label">Jenis Return:</label>
                        <select class="global-select2 form-select" id="return-type-filter" data-allow-clear="true">
                            <option value="">Pilih Jenis Return</option>
                            <option value="PURCHASE_RETURN">Purchase Return</option>
                            <option value="SALES_RETURN">Sales Return</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status:</label>
                        <select class="global-select2 form-select" id="status-filter" data-allow-clear="true">
                            <option value="">Pilih Status</option>
                            <option value="DRAFT">Draft</option>
                            <option value="PENDING_APPROVAL">Pending Approval</option>
                            <option value="APPROVED">Approved</option>
                            <option value="PROCESSED">Processed</option>
                            <option value="REJECTED">Rejected</option>
                            <option value="CANCELLED">Cancelled</option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label">No Return:</label>
                        <input type="text" class="form-control" id="transaction-code-filter"
                            placeholder="Cari No Return">
                    </div>
                </div>

                <hr class="my-6 mx-n6" />

                <!-- Action Buttons -->
                <div class="mt-4">
                    <div class="d-flex justify-content-between">
                        <!-- Column Toggle -->
                        <div class="d-flex align-items-center">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="toggleTagColumn" checked>
                                <label class="form-check-label" for="toggleTagColumn">
                                    <i class="icon-base ti tabler-tag me-1"></i>Tampilkan Kolom Tag
                                </label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <!-- Return Management has no add button since returns are created from invoice pages -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="card-datatable">
                <table id="return-management-table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 100px;">Tanggal</th>
                            <th style="width: 140px;">No Return</th>
                            <th style="width: 120px;">Jenis Return</th>
                            <th style="width: 140px;">No Invoice Asal</th>
                            <th style="width: 120px;">Total Return</th>
                            <th style="width: 100px;">Status</th>
                            <th style="width: 120px;">Dibuat Oleh</th>
                            <th style="width: 120px;">Tag</th>
                            <th style="width: 80px;">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@stop

@section('js_script')
    <script>
        $(function() {
            let returnManagementTable;

            // Permission variables
            const canRead = @json($canViewReturnManagement ?? true);

            // Initialize DataTable
            function initReturnManagementTable() {
                if (returnManagementTable) {
                    returnManagementTable.destroy();
                }

                returnManagementTable = new DataTable('#return-management-table', {
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('main_transaction.return_management.index_data_table') }}',
                        data: function(d) {
                            d.date_range_filter = $('#date-range-picker').val()
                            d.return_type_filter = $('#return-type-filter').val()
                            d.status_filter = $('#status-filter').val()
                            d.transaction_code_filter = $('#transaction-code-filter').val()
                        }
                    },
                    columns: [{
                            data: 'return_date',
                            width: '100px'
                        },
                        {
                            data: 'transaction_code',
                            width: '140px',
                            render: function(data, type, row) {
                                return data; // Non-clickable as per standard
                            }
                        },
                        {
                            data: 'return_type',
                            width: '120px',
                            render: function(data, type, row) {
                                if (data === 'PURCHASE_RETURN') {
                                    return '<span class="badge bg-label-primary">Purchase Return</span>';
                                } else if (data === 'SALES_RETURN') {
                                    return '<span class="badge bg-label-info">Sales Return</span>';
                                }
                                return data || '-';
                            }
                        },
                        {
                            data: 'source_invoice_code',
                            width: '140px'
                        },
                        {
                            data: 'grand_total',
                            width: '120px',
                            className: 'text-end',
                            render: function(data, type, row) {
                                if (type === 'display' && data != null && !isNaN(data)) {
                                    return 'Rp ' + parseFloat(data).toLocaleString('id-ID');
                                }
                                return data ?? '';
                            }
                        },
                        {
                            data: 'status',
                            width: '100px',
                            render: function(data, type, row) {
                                const statusClasses = {
                                    'DRAFT': 'bg-label-secondary',
                                    'PENDING_APPROVAL': 'bg-label-warning',
                                    'APPROVED': 'bg-label-success',
                                    'PROCESSED': 'bg-label-info',
                                    'REJECTED': 'bg-label-danger',
                                    'CANCELLED': 'bg-label-dark'
                                };
                                const badgeClass = statusClasses[data] || 'bg-label-secondary';
                                return `<span class="badge ${badgeClass}">${data || '-'}</span>`;
                            }
                        },
                        {
                            data: 'created_by_name',
                            width: '120px',
                            render: function(data, type, row) {
                                if (canRead) {
                                    const detailUrl =
                                        `{{ route('master_data.user.detail', ':id') }}`
                                        .replace(':id', row.created_by);
                                    return `<a href="${detailUrl}" class="text-primary fw-semibold">${data}</a>`;
                                }
                                return data;
                            }
                        },
                        {
                            data: 'tags',
                            width: '120px',
                            render: function(data, type, row) {
                                let tags = JSON.parse(data);
                                return tags.map(tag => {
                                    if (tag.name == null) {
                                        return ``;
                                    }
                                    return `<span class="badge ${tag.preview} me-1">${tag.name}</span>`;
                                }).join('');
                            }
                        },
                        {
                            data: null,
                            orderable: false,
                            searchable: false,
                            responsivePriority: 1,
                            width: '80px',
                            render: function(data, type, row, meta) {
                                const detailUrl =
                                    `{{ route('main_transaction.return_management.detail', ':id') }}`
                                    .replace(':id', row.id);
                                let actions = '<div class="d-flex gap-2">';

                                if (canRead) {
                                    actions += `<a href="${detailUrl}" class="btn btn-sm btn-primary waves-effect waves-light" title="Detail">
                                <i class="icon-xs icon-base ti tabler-eye"></i>
                            </a>`;
                                }

                                actions += '</div>';
                                return actions;
                            }
                        }
                    ],
                    layout: {
                        topStart: {
                            rowClass: 'm-0',
                            features: []
                        },
                        topEnd: {},
                        bottomStart: {
                            rowClass: 'row mx-3 justify-content-between',
                            features: ['info']
                        },
                        bottomEnd: 'paging'
                    },
                    language: {
                        paginate: {
                            next: '<i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>',
                            previous: '<i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>',
                            first: '<i class="icon-base ti tabler-chevrons-left scaleX-n1-rtl icon-18px"></i>',
                            last: '<i class="icon-base ti tabler-chevrons-right scaleX-n1-rtl icon-18px"></i>'
                        }
                    },
                    // scrollX: true,
                    responsive: true,
                    orderCellsTop: true,
                    ordering: false
                });
            }

            // Initialize table
            initReturnManagementTable();

            // Tag Column Toggle Functionality
            $('#toggleTagColumn').on('change', function() {
                const tagColumn = returnManagementTable.column(7); // Tag column index (0-based)
                tagColumn.visible(this.checked);
            });

            let searchTimeout;
            $('#date-range-picker, #return-type-filter, #status-filter, #transaction-code-filter').on(
                'keyup change',
                function() {
                    clearTimeout(searchTimeout);
                    const input = this;
                    searchTimeout = setTimeout(function() {
                        if (returnManagementTable) {
                            returnManagementTable.search(input.value).draw();
                        }
                    }, 400); // 400ms delay
                });

            // Date range picker initialization
            if ($('#date-range-picker').length) {
                $('#date-range-picker').daterangepicker({
                    opens: 'left',
                    locale: {
                        format: 'DD/MM/YYYY',
                        separator: ' - ',
                        applyLabel: 'Terapkan',
                        cancelLabel: 'Batal',
                        fromLabel: 'Dari',
                        toLabel: 'Ke',
                        customRangeLabel: 'Custom',
                        weekLabel: 'W',
                        daysOfWeek: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                        monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                                   'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
                    },
                    autoUpdateInput: false
                });

                $('#date-range-picker').on('apply.daterangepicker', function(ev, picker) {
                    $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
                    if (returnManagementTable) {
                        returnManagementTable.ajax.reload();
                    }
                });

                $('#date-range-picker').on('cancel.daterangepicker', function(ev, picker) {
                    $(this).val('');
                    if (returnManagementTable) {
                        returnManagementTable.ajax.reload();
                    }
                });
            }
        });

        // Action button functions
        function viewDetail(id) {
            // Navigate to return management detail page
            window.location.href = "{{ route('main_transaction.return_management.detail', ':id') }}".replace(':id', id);
        }

        function editRecord(id) {
            // Navigate to return management edit page
            window.location.href = "{{ route('main_transaction.return_management.edit', ':id') }}".replace(':id', id);
        }
    </script>
@endsection