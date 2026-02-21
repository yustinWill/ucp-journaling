@extends('layouts.default')
@section('page_title')
    List Sales Invoice
@stop
@section('content')
    <!-- Main Content Card -->
    <div class="mt-6">
        <div class="card">
            <h4 class="card-header">Kelola Sales Invoice</h4>
            <div class="card-body">
                <!-- Advanced Search Form -->
                <div class="row mb-4">
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label">Tanggal Invoice:</label>
                        <input type="text" id="date-range-picker" class="form-control"
                            placeholder="Pilih rentang tanggal" />
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label">Tanggal Jatuh Tempo:</label>
                        <input type="date" class="form-control global-single-date-picker" id="due-date-filter" />
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
                    {{-- <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label">Status:</label>
                        <select class="global-select2 form-select" id="status-filter" data-allow-clear="true">
                            <option value="">Semua Status</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status['value'] }}">{{ $status['label'] }}</option>
                            @endforeach
                        </select>
                    </div> --}}
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label">No Invoice / SO:</label>
                        <input type="text" class="form-control" id="invoice-search-filter"
                            placeholder="Cari Invoice atau SO">
                    </div>
                </div>
                {{-- <div class="row mb-4">
                   
                </div> --}}

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
                            @if ($canImportSalesInvoice)
                                <a href="{{ route('main_transaction.sales_invoice.import') }}"
                                    class="btn btn-md btn-primary waves-effect waves-light"><span
                                        class="icon-xs icon-base ti tabler-file-upload me-2"></span> Import</a>
                            @endif
                            @if ($canExportSalesInvoice)
                                <a href="{{ route('main_transaction.sales_invoice.export') }}"
                                    class="btn btn-md btn-primary waves-effect waves-light"><span
                                        class="icon-xs icon-base ti tabler-file-download me-2"></span> Export</a>
                            @endif
                        </div> --}}
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="card-datatable">
                <table id="sales-invoice-table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>No Invoice</th>
                            <th>Customer</th>
                            <th>Referensi SO</th>
                            <th>Grand Total</th>
                            {{-- <th>Tgl Jatuh Tempo</th> --}}
                            <th>Status</th>
                            {{-- <th>Diinput Oleh</th> --}}
                            <th>Tag</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>

            <!-- Information Text (right-aligned at bottom) -->
            <div class="card-footer">
                <div class="d-flex justify-content-end">
                    <small class="text-muted">
                        <i class="icon-base ti tabler-info-circle me-1"></i>
                        Sales Invoice dapat dibuat dari halaman detail Sales Order
                    </small>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js_script')
    <script>
        $(function() {
            // Note: global-select2 initialization is handled globally by layout

            let salesInvoiceTable;

            // Permission variables - using Spatie permissions
            const canRead = @json(auth()->user()->can('sales_invoice.view'));
            const canEdit = @json(auth()->user()->can('sales_invoice.edit'));
            const canViewUser = @json(auth()->user()->can('user.view'));
            const canViewSalesOrder = @json(auth()->user()->can('sales_order.view'));

            // Initialize DataTable
            function initSalesInvoiceTable() {
                if (salesInvoiceTable) {
                    salesInvoiceTable.destroy();
                }

                salesInvoiceTable = new DataTable('#sales-invoice-table', {
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('main_transaction.sales_invoice.index_data_table') }}',
                        data: function(d) {
                            d.date_range_filter = $('#date-range-picker').val();
                            d.due_date_filter = $('#due-date-filter').val();
                            d.customer_filter = $('#customer-filter').val();
                            d.status_filter = $('#status-filter').val();
                            d.invoice_search_filter = $('#invoice-search-filter').val();
                        }
                    },
                    columns: [{
                            data: 'invoice_date',
                        },
                        {
                            data: 'transaction_code',
                            render: function(data, type, row) {
                                return data; // Non-clickable as per standard
                            }
                        },
                        {
                            data: 'customer_name',
                        },
                        {
                            data: 'so_reference',
                            render: function(data, type, row) {
                                if (!data) return '-';
                                if (canViewSalesOrder && row.f_sales_order_id) {
                                    const detailUrl =
                                        `{{ route('main_transaction.sales_order.detail', ':id') }}`
                                        .replace(':id', row.f_sales_order_id);
                                    return `<a href="${detailUrl}" class="text-primary fw-semibold">${data}</a>`;
                                }
                                return data;
                            }
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
                                let statusText = data;

                                switch (data) {
                                    case 'PAID':
                                        badgeClass = 'bg-label-success';
                                        statusText = 'Settled';
                                        break;
                                    case 'UNPAID':
                                    case 'DRAFT':
                                    case 'PARTIAL_PAID':
                                    default:
                                        badgeClass = 'bg-label-warning';
                                        statusText = 'Not Yet Settled';
                                        break;
                                }
                                return `<span class="badge ${badgeClass}">${statusText}</span>`;
                            }
                        },
                        {
                            data: 'tags',
                            render: function(data, type, row) {
                                if (!data) return '-';
                                let tags = JSON.parse(data);
                                return tags.map(tag => {
                                    if (tag.name == null) {
                                        return '';
                                    }
                                    return `<span class="badge ${tag.preview} me-1">${tag.name}</span>`;
                                }).join('');
                            }
                        },
                        {
                            data: null,
                            width: '80px',
                            orderable: false,
                            searchable: false,
                            responsivePriority: 1,
                            render: function(data, type, row, meta) {
                                const detailUrl =
                                    `{{ route('main_transaction.sales_invoice.detail', ':id') }}`
                                    .replace(':id', row.id);
                                // const editUrl =
                                //     `{{ route('main_transaction.sales_invoice.edit', ':id') }}`
                                //     .replace(':id', row.id);
                                let actions = '<div class="d-flex gap-2">';

                                if (canRead) {
                                    actions += `<a href="${detailUrl}" class="btn btn-sm btn-primary waves-effect waves-light" title="Detail">
                                <i class="icon-xs icon-base ti tabler-eye"></i>
                            </a>`;
                                }

                                // if (canEdit) {
                                //     actions += `<a href="${editUrl}" class="btn btn-sm btn-warning waves-effect waves-light" title="Edit">
                            //         <i class="icon-xs icon-base ti tabler-edit"></i>
                            //     </a>`;
                                // }

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
            initSalesInvoiceTable();

            // Tag Column Toggle Functionality
            $('#toggleTagColumn').on('change', function() {
                const tagColumn = salesInvoiceTable.column(6); // Tag column index (0-based)
                tagColumn.visible(this.checked);
            });

            // Filter functionality with timeout
            let searchTimeout;
            $('#date-range-picker, #due-date-filter, #customer-filter, #status-filter, #invoice-search-filter')
                .on(
                    'keyup change',
                    function() {
                        clearTimeout(searchTimeout);
                        const input = this;
                        searchTimeout = setTimeout(function() {
                            if (salesInvoiceTable) {
                                salesInvoiceTable.draw();
                            }
                        }, 400); // 400ms delay
                    });
        });
    </script>
@endsection
