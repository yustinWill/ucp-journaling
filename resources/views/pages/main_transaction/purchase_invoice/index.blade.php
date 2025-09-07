@extends('layouts.default')
@section('page_title')
    List Purchase Invoice
@stop
@section('content')
    <!-- Summary Cards -->
    {{-- <div class="row">
        <div class="col-lg-4 col-sm-6">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="icon-base ti tabler-file-invoice icon-28px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0" id="unpaid-invoices-count">Rp 75,000,000</h4>
                    </div>
                    <p class="mb-1">Invoice Belum Dibayar</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6">
            <div class="card card-border-shadow-warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="icon-base ti tabler-clock-exclamation icon-28px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0" id="overdue-invoices-count">Rp 15,000,000</h4>
                    </div>
                    <p class="mb-1">Invoice Overdue</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6">
            <div class="card card-border-shadow-success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="icon-base ti tabler-calendar-check icon-28px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0" id="paid-invoices-count">Rp 120,000,000</h4>
                    </div>
                    <p class="mb-1">Dibayar Bulan Ini</p>
                </div>
            </div>
        </div>
    </div>
     --}}
    <!-- Main Content Card -->
    <div class="mt-6">
        <div class="card">
            <h4 class="card-header">Kelola Purchase Invoice</h4>
            <div class="card-body">
                <!-- Advanced Search Form -->
                <div class="row mb-4">
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label">Tanggal Transaksi:</label>
                        <input type="text" id="date-range-picker" class="form-control"
                            placeholder="Pilih rentang tanggal" />
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label">Tanggal Invoice:</label>
                        <input type="date" class="form-control global-single-date-picker" id="invoice-date-filter" />
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Supplier:</label>
                        <select class="global-select2 form-select" id="supplier-filter" data-allow-clear="true">
                            <option value="">Pilih Supplier</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label">Status:</label>
                        <select class="global-select2 form-select" id="status-filter" data-allow-clear="true">
                            <option value="">Semua Status</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status['value'] }}">{{ $status['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label">Tanggal Jatuh Tempo:</label>
                        <input type="date" class="form-control global-single-date-picker" id="due-date-filter" />
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label">No Invoice / PO:</label>
                        <input type="text" class="form-control" id="invoice-search-filter"
                            placeholder="Cari Invoice atau PO">
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

                        {{-- <div class="d-flex gap-2">
                            @if ($canExportPurchaseInvoice)
                                <a href="{{ route('main_transaction.purchase_invoice.export') }}"
                                    class="btn btn-md btn-primary waves-effect waves-light"><span
                                        class="icon-xs icon-base ti tabler-file-download me-2"></span> Export</a>
                            @endif
                        </div> --}}
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="card-datatable">
                <table id="purchase-invoice-table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>No Invoice</th>
                            <th>Tgl Invoice</th>
                            <th>Supplier</th>
                            <th>PO Reference</th>
                            <th>Tgl Jatuh Tempo</th>
                            <th>Grand Total</th>
                            <th>Status</th>
                            <th>Diinput Oleh</th>
                            <th>Tag</th>
                            <th>Aksi</th>
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

            let purchaseInvoiceTable;

            // Permission variables
            const canRead = @json($canViewPurchaseInvoice ?? true);
            const canEdit = @json($canEditPurchaseInvoice ?? true);
            const canViewUser = @json($canViewUser ?? true);
            const canViewPurchaseOrder = @json($canViewPurchaseOrder ?? true);
            const canViewPurchaseDelivery = @json($canViewPurchaseDelivery ?? true);

            // Initialize DataTable
            function initPurchaseInvoiceTable() {
                if (purchaseInvoiceTable) {
                    purchaseInvoiceTable.destroy();
                }

                purchaseInvoiceTable = new DataTable('#purchase-invoice-table', {
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('main_transaction.purchase_invoice.index_data_table') }}',
                        data: function(d) {
                            d.date_range_filter = $('#date-range-picker').val();
                            d.invoice_date_filter = $('#invoice-date-filter').val();
                            d.supplier_filter = $('#supplier-filter').val();
                            d.status_filter = $('#status-filter').val();
                            d.due_date_filter = $('#due-date-filter').val();
                            d.invoice_search_filter = $('#invoice-search-filter').val();
                        }
                    },
                    columns: [{
                            data: 'transaction_date',
                        },
                        {
                            data: 'invoice_number',
                            render: function(data, type, row) {
                                return data; // Non-clickable as per standard
                            }
                        },
                        {
                            data: 'invoice_date',
                        },
                        {
                            data: 'supplier_name',
                            render: function(data, type, row) {
                                if (!data) return '-';
                                if (canRead && row.supplier_id) {
                                    const detailUrl =
                                        `{{ route('master_data.contact.detail', ':id') }}`
                                        .replace(':id', row.supplier_id);
                                    return `<a href="${detailUrl}" class="text-primary fw-semibold">${data}</a>`;
                                }
                                return data;
                            }
                        },
                        {
                            data: 'po_reference',
                            render: function(data, type, row) {
                                if (!data) return '-';
                                if (canViewPurchaseOrder && row.po_id) {
                                    const detailUrl =
                                        `{{ route('main_transaction.purchase_order.detail', ':id') }}`
                                        .replace(':id', row.po_id);
                                    return `<a href="${detailUrl}" class="text-primary fw-semibold">${data}</a>`;
                                }
                                return data;
                            }
                        },
                        {
                            data: 'due_date',
                        },
                        {
                            data: 'grand_total',
                            render: function(data, type, row) {
                                return `Rp ${parseInt(data || 0).toLocaleString('id-ID')}`;
                            }
                        },
                        {
                            data: 'status',
                            render: function(data, type, row) {
                                let badgeClass = 'bg-label-secondary';
                                switch (data) {
                                    case 'DRAFT':
                                        badgeClass = 'bg-label-secondary';
                                        break;
                                    case 'UNPAID':
                                        badgeClass = 'bg-label-warning';
                                        break;
                                    case 'PAID':
                                        badgeClass = 'bg-label-success';
                                        break;
                                    case 'PARTIAL_PAID':
                                        badgeClass = 'bg-label-info';
                                        break;
                                    case 'OVERDUE':
                                        badgeClass = 'bg-label-danger';
                                        break;
                                    case 'CANCELLED':
                                        badgeClass = 'bg-label-dark';
                                        break;
                                }
                                return `<span class="badge ${badgeClass}">${data}</span>`;
                            }
                        },
                        {
                            data: 'created_by_name',
                            render: function(data, type, row) {
                                if (canViewUser && row.created_by) {
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
                            render: function(data, type, row) {
                                if (!data) return '-';
                                let tags = JSON.parse(data);
                                return tags.map(tag => {
                                    if (tag.code == null) {
                                        return '';
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
                            render: function(data, type, row, meta) {
                                const detailUrl =
                                    `{{ route('main_transaction.purchase_invoice.detail', ':id') }}`
                                    .replace(':id', row.id);
                                const editUrl =
                                    `{{ route('main_transaction.purchase_invoice.edit', ':id') }}`
                                    .replace(':id', row.id);
                                let actions = '<div class="d-flex gap-2">';

                                if (canRead) {
                                    actions += `<a href="${detailUrl}" class="btn btn-sm btn-primary waves-effect waves-light" title="Detail">
                                <i class="icon-xs icon-base ti tabler-eye"></i>
                            </a>`;
                                }

                                // Edit button removed per requirement

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
                    orderCellsTop: true,
                    ordering: false
                });
            }

            // Initialize table
            initPurchaseInvoiceTable();

            // Filter functionality with timeout
            let searchTimeout;
            $('#date-range-picker, #invoice-date-filter, #supplier-filter, #status-filter, #due-date-filter, #invoice-search-filter')
                .on(
                    'keyup change',
                    function() {
                        clearTimeout(searchTimeout);
                        const input = this;
                        searchTimeout = setTimeout(function() {
                            if (purchaseInvoiceTable) {
                                purchaseInvoiceTable.draw();
                            }
                        }, 400); // 400ms delay
                    });

            // Tag Column Toggle Functionality
            $('#toggleTagColumn').on('change', function() {
                const tagColumn = purchaseInvoiceTable.column(
                9); // Tag column index (0-based, adjusted for new column)
                tagColumn.visible(this.checked);
            });
        });
    </script>
@endsection
