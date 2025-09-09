@extends('layouts.default')
@section('page_title')
    List Sales Order
@stop
@section('content')
    <!-- Main Content Card -->
    <div class="mt-6">
        <div class="card">
            <h4 class="card-header">Kelola Sales Order</h4>
            <div class="card-body">
                <!-- Advanced Search Form -->
                <div class="row mb-4">
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label">Tanggal Transaksi:</label>
                        <input type="text" id="date-range-picker" class="form-control" />
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label">Tanggal Jatuh Tempo:</label>
                        <input type="date" class="form-control global-single-date-picker" id="on-due-date-filter" />
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Customer:</label>
                        <select class="global-select2 form-select" id="customer-filter" data-allow-clear="true">
                            <option value="">Pilih Customer</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label">No Sales Order:</label>
                        <input type="text" class="form-control" id="sales-order-no-filter"
                            placeholder="Cari Sales Order">
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
                            {{-- @if ($canImportSalesOrder)
                                <a href="{{ route('main_transaction.sales_order.import') }}"
                                    class="btn btn-md btn-primary waves-effect waves-light"><span
                                        class="icon-xs icon-base ti tabler-file-upload me-2"></span> Import</a>
                            @endif
                            @if ($canExportSalesOrder)
                                <a href="{{ route('main_transaction.sales_order.export') }}"
                                    class="btn btn-md btn-primary waves-effect waves-light"><span
                                        class="icon-xs icon-base ti tabler-file-download me-2"></span> Export</a>
                            @endif --}}
                            @if ($canAddSalesOrder)
                                <a href="{{ route('main_transaction.sales_order.add') }}"
                                    class="btn btn-md btn-primary waves-effect waves-light"><span
                                        class="icon-xs icon-base ti tabler-plus me-2"></span> Tambah Data</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="card-datatable">
                <table id="sales-order-table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 100px;">Tanggal</th>
                            <th style="width: 140px;">No Sales Order</th>
                            <th style="width: 180px;">Customer</th>
                            <th style="width: 120px;">Tgl Jatuh Tempo</th>
                            <th style="width: 120px;">Total</th>
                            <th style="width: 120px;">Diinput Oleh</th>
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
            // Note: global-select2 initialization is handled globally by layout

            let salesOrderTable;

            // Permission variables
            const canRead = @json($canViewSalesOrder ?? true);
            const canEdit = @json($canEditSalesOrder ?? true);

            // Initialize DataTable
            function initSalesOrderTable() {
                if (salesOrderTable) {
                    salesOrderTable.destroy();
                }

                salesOrderTable = new DataTable('#sales-order-table', {
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('main_transaction.sales_order.index_data_table') }}',
                        data: function(d) {
                            d.date_range_filter = $('#date-range-picker').val()
                            d.on_due_date_filter = $('#on-due-date-filter').val()
                            d.customer_filter = $('#customer-filter').val()
                            d.sales_order_no_filter = $('#sales-order-no-filter').val()
                        }
                    },
                    columns: [{
                            data: 'transaction_date',
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
                            data: 'customer_name',
                            width: '180px',
                            render: function(data, type, row) {
                                if (!data) return '-';
                                if (canRead) {
                                    const detailUrl =
                                        `{{ route('master_data.contact.detail', ':id') }}`
                                        .replace(':id', row.f_customer_id);
                                    return `<a href="${detailUrl}" class="text-primary fw-semibold">${data}</a>`;
                                }
                                return data;
                            }
                        },
                        {
                            data: 'payment_term_due_date',
                            width: '120px'
                        },
                        {
                            data: 'grand_price',
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
                                    if (tag.code == null) {
                                        return ``;
                                    }
                                    return `<span class="badge ${tag.preview} me-1">${tag.code}</span>`;
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
                                    `{{ route('main_transaction.sales_order.detail', ':id') }}`
                                    .replace(':id', row.id);
                                const editUrl =
                                    `{{ route('main_transaction.sales_order.edit', ':id') }}`
                                    .replace(':id', row.id);
                                let actions = '<div class="d-flex gap-2">';

                                if (canRead) {
                                    actions += `<a href="${detailUrl}" class="btn btn-sm btn-primary waves-effect waves-light" title="Detail">
                                <i class="icon-xs icon-base ti tabler-eye"></i>
                            </a>`;
                                }

                                if (canEdit) {
                                    actions += `<a href="${editUrl}" class="btn btn-sm btn-primary waves-effect waves-light" title="Edit">
                                <i class="icon-xs icon-base ti tabler-edit"></i>
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
                    scrollX: true,
                    responsive: true,
                    orderCellsTop: true,
                    ordering: false
                });
            }

            // Initialize table
            initSalesOrderTable();

            // Tag Column Toggle Functionality
            $('#toggleTagColumn').on('change', function() {
                const tagColumn = salesOrderTable.column(6); // Tag column index (0-based)
                tagColumn.visible(this.checked);
            });
            let searchTimeout;
            $('#date-range-picker, #on-due-date-filter, #customer-filter, #sales-order-no-filter').on(
                'keyup change',
                function() {
                    clearTimeout(searchTimeout);
                    const input = this;
                    searchTimeout = setTimeout(function() {
                        if (salesOrderTable) {
                            salesOrderTable.search(input.value).draw();
                        }
                    }, 400); // 400ms delay
                });
        });

        // Action button functions
        function viewDetail(id) {
            // Navigate to sales order detail page
            window.location.href = "{{ route('main_transaction.sales_order.detail', ':id') }}".replace(':id', id);
        }

        function editRecord(id) {
            // Navigate to sales order edit page
            window.location.href = "{{ route('main_transaction.sales_order.edit', ':id') }}".replace(':id', id);
        }
    </script>
@endsection