@extends('layouts.default')
@section('page_title')
    List Sales Order
@stop
@section('content')
    <!-- Summary Cards -->
    <div class="row g-4 mb-4">
        <!-- Total SO -->
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="icon-base ti tabler-file-text icon-28px"></i>
                            </span>
                        </div>
                        <div>
                            <h4 class="mb-0">{{ $stats['total_so'] }}</h4>
                        </div>
                    </div>
                    <p class="mb-0 fw-semibold">Total Sales Order</p>
                    <small class="text-muted">Klik untuk melihat daftar SO</small>
                </div>
            </div>
        </div>

        <!-- SD Menunggu -->
        <div class="col-lg-2 col-sm-6">
            <div class="card card-border-shadow-dark h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-3">
                            <span class="avatar-initial rounded bg-label-dark">
                                <i class="icon-base ti tabler-clock icon-24px"></i>
                            </span>
                        </div>
                        <div>
                            <h4 class="mb-0 text-dark">{{ $stats['sd_waiting'] }}</h4>
                        </div>
                    </div>
                    <p class="mb-0 fw-semibold">SD Menunggu</p>
                    <small class="text-muted">Belum diterima</small>
                </div>
            </div>
        </div>

        <!-- SD Dikirim -->
        <div class="col-lg-2 col-sm-6">
            <div class="card card-border-shadow-warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-3">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="icon-base ti tabler-truck-delivery icon-24px"></i>
                            </span>
                        </div>
                        <div>
                            <h4 class="mb-0 text-warning">{{ $stats['sd_non_confirm'] }}</h4>
                        </div>
                    </div>
                    <p class="mb-0 fw-semibold">SD Dikirim</p>
                    <small class="text-muted">Belum konfirmasi</small>
                </div>
            </div>
        </div>

        <!-- SD Terkirim -->
        <div class="col-lg-2 col-sm-6">
            <div class="card card-border-shadow-success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-3">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="icon-base ti tabler-truck-loading icon-24px"></i>
                            </span>
                        </div>
                        <div>
                            <h4 class="mb-0 text-success">{{ $stats['sd_confirm'] }}</h4>
                        </div>
                    </div>
                    <p class="mb-0 fw-semibold">SD Terkirim</p>
                    <small class="text-muted">Terkonfirmasi</small>
                </div>
            </div>
        </div>

        <!-- Total SI -->
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-info h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="icon-base ti tabler-receipt icon-28px"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-0">
                                <span class="text-warning">{{ $stats['si_expected'] }}</span>
                            </h6>
                            <h6 class="mb-0">
                                <span class="text-success">{{ $stats['si_settled'] }}</span>
                            </h6>
                        </div>
                    </div>
                    <p class="mb-0 fw-semibold">Sales Invoice</p>
                    <small class="text-muted">
                        <span class="text-warning">Expected</span> /
                        <span class="text-success">Settled</span>
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="mt-2">
        <div class="card">
            <h4 class="card-header">Kelola Sales Order</h4>
            <div class="card-body">
                <!-- Advanced Search Form -->
                <div class="row mb-4">
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label">Tanggal Transaksi:</label>
                        <input type="text" id="date-range-picker" class="form-control" />
                    </div>
                    {{-- <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label">Tanggal Jatuh Tempo:</label>
                        <input type="date" class="form-control global-single-date-picker" id="on-due-date-filter" />
                    </div> --}}
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
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label">Status:</label>
                        <select class="global-select2 form-select" id="status-filter" data-allow-clear="true">
                            <option value="">Semua Status</option>
                            <option value="Menunggu">Menunggu</option>
                            <option value="Menunggu Pengiriman">Menunggu Pengiriman</option>
                            <option value="Dikirim">Dikirim</option>
                            <option value="Terkirim">Terkirim</option>
                            <option value="Dibayar">Dibayar</option>
                            <option value="Terbayar">Terbayar</option>
                            <option value="Selesai">Selesai</option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3 mt-3 d-flex align-items-end">
                        <button type="button" class="btn btn-outline-secondary" id="reset-filter">
                            <i class="icon-base ti tabler-filter-off me-1"></i> Reset Filter
                        </button>
                    </div>
                </div>

                <hr class="my-6 mx-n6" />

                <!-- Action Buttons -->
                <div class="mt-4">
                    <div class="d-flex justify-content-between">
                        <!-- Column Toggle -->
                        <div class="d-flex align-items-center">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="toggleTagColumn">
                                <label class="form-check-label" for="toggleTagColumn">
                                    <i class="icon-base ti tabler-tag me-1"></i>Tampilkan Kolom Tag
                                </label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            
                            @can('sales_order.create')
                                <a href="{{ route('main_transaction.sales_order.add') }}"
                                    class="btn btn-md btn-primary waves-effect waves-light"><span
                                        class="icon-xs icon-base ti tabler-plus me-2"></span> Tambah Data</a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="card-datatable">
                <table id="sales-order-table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>No SO</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Tgl Jatuh Tempo</th>
                            <th>Diinput Oleh</th>
                            <th>Tag</th>
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

            // Permission variables - using Spatie permissions
            const canRead = @json(auth()->user()->can('sales_order.view'));
            const canEdit = @json(auth()->user()->can('sales_order.edit'));
            const canViewContact = @json(auth()->user()->can('contact.view'));
            const canViewUser = @json(auth()->user()->can('user.view'));

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
                            d.status_filter = $('#status-filter').val()
                        }
                    },
                    columns: [
                        {
                            data: 'transaction_date',
                            width: '110px',
                        },
                        {
                            data: 'transaction_code',
                            width: '140px',
                            responsivePriority: 1,
                            render: function(data, type, row) {
                                if (!data) return '-';
                                if (canRead) {
                                    const detailUrl =
                                        `{{ route('main_transaction.sales_order.detail', ':id') }}`
                                        .replace(':id', row.id);
                                    return `<a href="${detailUrl}" class="text-primary fw-semibold">${data}</a>`;
                                }
                                return data;
                            }
                        },
                        {
                            data: 'customer_name',
                            render: function(data, type, row) {
                                if (!data) return '-';
                                if (canViewContact && row.f_customer_id) {
                                    const detailUrl =
                                        `{{ route('master_data.contact.detail', ':id') }}`
                                        .replace(':id', row.f_customer_id);
                                    return `<a href="${detailUrl}" class="text-primary fw-semibold">${data}</a>`;
                                }
                                return data;
                            }
                        },
                        {
                            data: 'grand_price',
                            className: 'text-end',
                            render: function(data, type, row) {
                                if (type === 'display' && data != null && !isNaN(data)) {
                                    return 'Rp ' + parseFloat(data).toLocaleString('id-ID');
                                }
                                return data ?? '';
                            }
                        },
                        {
                            data: null,
                            width: '180px',
                            responsivePriority: 1,
                            render: function(data, type, row) {
                                let status = 'Menunggu';
                                let badgeClass = 'bg-label-secondary';

                                const siCount = parseInt(row.si_count) || 0;
                                const sdCount = parseInt(row.sd_count) || 0;
                                const siSettledCount = parseInt(row.si_settled_count) || 0;
                                const sdWaitingCount = parseInt(row.sd_waiting_count) || 0;
                                const sdAcceptedCount = parseInt(row.sd_accepted_count) || 0;
                                const sdConfirmedCount = parseInt(row.sd_confirmed_count) || 0;

                                // Priority order: Menunggu -> Menunggu Pengiriman -> Dikirim -> Terkirim -> Dibayar -> Terbayar -> Selesai
                                if (sdConfirmedCount > 0 && siSettledCount > 0) {
                                    // Both SD confirmed and SI settled
                                    status = 'Selesai'; badgeClass = 'bg-label-success';
                                } else if (siSettledCount > 0) {
                                    // SI settled (regardless of SD status)
                                    status = 'Terbayar'; badgeClass = 'bg-label-success';
                                } else if (siCount > 0) {
                                    // SI created but not settled
                                    status = 'Dibayar'; badgeClass = 'bg-label-primary';
                                } else if (sdConfirmedCount > 0) {
                                    // SD confirmed but no SI
                                    status = 'Terkirim'; badgeClass = 'bg-label-info';
                                } else if (sdAcceptedCount > 0) {
                                    // SD accepted but not confirmed
                                    status = 'Dikirim'; badgeClass = 'bg-label-warning';
                                } else if (sdWaitingCount > 0) {
                                    // SD created but not accepted
                                    status = 'Menunggu Pengiriman'; badgeClass = 'bg-label-dark';
                                } else if (sdCount > 0) {
                                    // SD exists (fallback)
                                    status = 'Menunggu Pengiriman'; badgeClass = 'bg-label-dark';
                                } else {
                                    // No SD and no SI
                                    status = 'Menunggu'; badgeClass = 'bg-label-secondary';
                                }

                                return `<span class="badge ${badgeClass}">${status}</span>`;
                            }
                        },
                        {
                            data: 'payment_term_due_date',
                            width: '130px',
                            responsivePriority: 10001,
                        },
                        {
                            data: 'created_by_name',
                            responsivePriority: 10002,
                            render: function(data, type, row) {
                                if (!data) return '-';
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
                            responsivePriority: 10003,
                            render: function(data, type, row) {
                                let tags = JSON.parse(data);
                                return tags.map(tag => {
                                    if (tag.code == null) return '';
                                    return `<span class="badge ${tag.preview} me-1">${tag.code}</span>`;
                                }).join('');
                            }
                        },
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
                    responsive: true,
                    orderCellsTop: true,
                    ordering: false
                });
            }

            // Initialize table
            initSalesOrderTable();

            // Tag column hidden by default (0=date,1=no_so,2=customer,3=total,4=status,5=due_date,6=created_by,7=tags)
            salesOrderTable.column(7).visible(false);

            // Tag Column Toggle Functionality
            $('#toggleTagColumn').on('change', function() {
                const tagColumn = salesOrderTable.column(7);
                tagColumn.visible(this.checked);
            });
            let searchTimeout;
            $('#date-range-picker, #on-due-date-filter, #customer-filter, #sales-order-no-filter, #status-filter').on(
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

            // Reset Filter
            $('#reset-filter').on('click', function() {
                $('#date-range-picker').val('');
                $('#on-due-date-filter').val('');
                $('#sales-order-no-filter').val('');
                $('#customer-filter').val('').trigger('change');
                $('#status-filter').val('').trigger('change');
                if (salesOrderTable) {
                    salesOrderTable.search('').draw();
                }
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