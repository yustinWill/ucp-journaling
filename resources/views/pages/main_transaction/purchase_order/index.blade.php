@extends('layouts.default')
@section('page_title')
    List Purchase Order
@stop
@section('content')
    <!-- Main Content Card -->
    <div class="mt-6">
        <div class="card">
            <h4 class="card-header">Kelola Purchase Order</h4>
            <div class="card-body">
                <!-- Advanced Search Form -->
                <div class="row mb-4">
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label">Tanggal Transaksi:</label>
                        <input type="text" id="date-range-picker" class="form-control" />
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label">Tanggal Jatuh Tempo</label>
                        <input type="date" class="form-control global-single-date-picker" id="on-due-date-filter" />
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Supplier:</label>
                        <select class="global-select2 form-select" id="suppllier-filter" data-allow-clear="true">
                            <option value="">Pilih Supplier</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label">No Purchase Order:</label>
                        <input type="text" class="form-control" id="purchase-order-no-filter"
                            placeholder="Cari Purchase Order">
                    </div>
                    <div class="col-12 col-sm-6 col-lg-2 mt-3">
                        <label class="form-label">Status PD:</label>
                        <select class="global-select2 form-select" id="pd-status-filter" data-allow-clear="true">
                            <option value="">Semua</option>
                            <option value="Belum Terima">Belum Terima</option>
                            <option value="Sebagian Diterima">Sebagian Diterima</option>
                            <option value="Diterima">Diterima</option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-2 mt-3">
                        <label class="form-label">Status PI:</label>
                        <select class="global-select2 form-select" id="pi-status-filter" data-allow-clear="true">
                            <option value="">Semua</option>
                            <option value="Belum Ditagih">Belum Ditagih</option>
                            <option value="Ditagih">Ditagih</option>
                            <option value="Dibayar Sebagian">Dibayar Sebagian</option>
                            <option value="Lunas">Lunas</option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-2 mt-3 d-flex align-items-end">
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
                            @can('purchase_order.create')
                                <a href="{{ route('main_transaction.purchase_order.add') }}"
                                    class="btn btn-md btn-primary waves-effect waves-light"><span
                                        class="icon-xs icon-base ti tabler-plus me-2"></span> Tambah Data</a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="card-datatable">
                <table id="purchase-order-table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>No PO</th>
                            <th>Supplier</th>
                            <th>Total</th>
                            <th>Status PD</th>
                            <th>Status PI</th>
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

            let purchaseOrderTable;

            // Permission variables - using Spatie permissions
            const canRead = @json(auth()->user()->can('purchase_order.view'));
            const canViewContact = @json(auth()->user()->can('contact.view'));
            const canViewUser = @json(auth()->user()->can('user.view'));

            // Initialize DataTable
            function initPurchaseOrderTable() {
                if (purchaseOrderTable) {
                    purchaseOrderTable.destroy();
                }

                purchaseOrderTable = new DataTable('#purchase-order-table', {
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('main_transaction.purchase_order.index_data_table') }}',
                        data: function(d) {
                            d.date_range_filter = $('#date-range-picker').val()
                            d.on_due_date_filter = $('#on-due-date-filter').val()
                            d.suppllier_filter = $('#suppllier-filter').val()
                            d.purchase_order_no_filter = $('#purchase-order-no-filter').val()
                            d.pd_status_filter = $('#pd-status-filter').val()
                            d.pi_status_filter = $('#pi-status-filter').val()
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
                                const detailUrl = `{{ route('main_transaction.purchase_order.detail', ':id') }}`.replace(':id', row.id);
                                return `<a href="${detailUrl}" class="text-primary fw-bold">${data}</a>`;
                            }
                        },
                        {
                            data: 'supplier_name',
                            width: '180px',
                            render: function(data, type, row) {
                                if (!data) return '-';
                                if (canRead) {
                                    const detailUrl =
                                        `{{ route('main_transaction.purchase_order.detail', ':id') }}`
                                        .replace(':id', row.id);
                                    return `<a href="${detailUrl}" class="text-primary fw-semibold">${data}</a>`;
                                }
                                return data;
                            }
                        },
                        {
                            data: 'supplier_name',
                            render: function(data, type, row) {
                                if (!data) return '-';
                                if (canViewContact && row.f_supplier_id) {
                                    const detailUrl =
                                        `{{ route('master_data.contact.detail', ':id') }}`
                                        .replace(':id', row.f_supplier_id);
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
                            width: '140px',
                            responsivePriority: 1,
                            render: function(data, type, row) {
                                const pdCount = parseInt(row.pd_count) || 0;
                                const pdDeliveredCount = parseInt(row.pd_delivered_count) || 0;
                                const pdPartialCount = parseInt(row.pd_in_transit_count) || 0;

                                let status = 'Belum Terima';
                                let badgeClass = 'bg-label-secondary';

                                if (pdDeliveredCount > 0) {
                                    status = 'Diterima';
                                    badgeClass = 'bg-label-success';
                                } else if (pdPartialCount > 0) {
                                    status = 'Sebagian Diterima';
                                    badgeClass = 'bg-label-warning';
                                } else if (pdCount > 0) {
                                    status = 'Menunggu';
                                    badgeClass = 'bg-label-dark';
                                }

                                return `<span class="badge ${badgeClass}">${status}</span>`;
                            }
                        },
                        {
                            data: null,
                            width: '140px',
                            responsivePriority: 1,
                            render: function(data, type, row) {
                                const piCount = parseInt(row.pi_count) || 0;
                                const piSettledCount = parseInt(row.pi_settled_count) || 0;
                                const piPartialCount = parseInt(row.pi_partial_count) || 0;

                                let status = 'Belum Ditagih';
                                let badgeClass = 'bg-label-secondary';

                                if (piSettledCount > 0) {
                                    status = 'Lunas';
                                    badgeClass = 'bg-label-success';
                                } else if (piPartialCount > 0) {
                                    status = 'Dibayar Sebagian';
                                    badgeClass = 'bg-label-warning';
                                } else if (piCount > 0) {
                                    status = 'Ditagih';
                                    badgeClass = 'bg-label-info';
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
                                    if (tag.name == null) return '';
                                    return `<span class="badge ${tag.preview} me-1">${tag.name}</span>`;
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
            initPurchaseOrderTable();

            // Tag column hidden by default (0=date,1=no_po,2=supplier,3=total,4=status_pd,5=status_pi,6=due_date,7=created_by,8=tags)
            purchaseOrderTable.column(8).visible(false);

            // Tag Column Toggle Functionality
            $('#toggleTagColumn').on('change', function() {
                purchaseOrderTable.column(8).visible(this.checked);
            });

            // Reset Filter
            $('#reset-filter').on('click', function() {
                $('#date-range-picker').val('');
                $('#on-due-date-filter').val('');
                $('#suppllier-filter').val('').trigger('change');
                $('#purchase-order-no-filter').val('');
                $('#pd-status-filter').val('').trigger('change');
                $('#pi-status-filter').val('').trigger('change');
                if (purchaseOrderTable) {
                    purchaseOrderTable.draw();
                }
            });

            let searchTimeout;
            $('#date-range-picker, #on-due-date-filter, #suppllier-filter, #purchase-order-no-filter, #pd-status-filter, #pi-status-filter').on(
                'keyup change',
                function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(function() {
                        if (purchaseOrderTable) {
                            purchaseOrderTable.draw();
                        }
                    }, 400); // 400ms delay
                });
        });
    </script>
@endsection
