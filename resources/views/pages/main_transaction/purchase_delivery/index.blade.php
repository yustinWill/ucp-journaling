@extends('layouts.default')
@section('page_title')
    List Purchase Delivery
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
                                <i class="icon-base ti tabler-truck-delivery icon-28px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0" id="pending-deliveries-count">12</h4>
                    </div>
                    <p class="mb-1">Pengiriman Pending</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6">
            <div class="card card-border-shadow-warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="icon-base ti tabler-truck icon-28px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0" id="in-transit-deliveries-count">8</h4>
                    </div>
                    <p class="mb-1">Sedang Dalam Perjalanan</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6">
            <div class="card card-border-shadow-success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="icon-base ti tabler-package icon-28px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0" id="delivered-count">35</h4>
                    </div>
                    <p class="mb-1">Berhasil Diterima Bulan Ini</p>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Main Content Card -->
    <div class="mt-6">
        <div class="card">
            <h4 class="card-header">Kelola Purchase Delivery</h4>
            <div class="card-body">
                <!-- Advanced Search Form -->
                <div class="row mb-4">
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label">Tanggal Transaksi:</label>
                        <input type="text" id="date-range-picker" class="form-control" />
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label">Tanggal Delivery:</label>
                        <input type="date" class="form-control global-single-date-picker" id="delivery-date-filter" />
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
                        <label class="form-label">No Purchase Delivery / PO:</label>
                        <input type="text" class="form-control" id="delivery-search-filter"
                            placeholder="Cari Delivery atau PO">
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
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="card-datatable">
                <table id="purchase-delivery-table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>No PD</th>
                            <th>No Delivery</th>
                            <th>Supplier</th>
                            <th>Tgl Delivery</th>
                            <th>PO Reference</th>
                            <th>Status</th>
                            <th>Diinput Oleh</th>
                            <th>Tag</th>
                        </tr>
                    </thead>
                </table>
            </div>

            <!-- Information Text (right-aligned at bottom) -->
            <div class="card-footer">
                <div class="d-flex justify-content-end">
                    <small class="text-muted">
                        <i class="icon-base ti tabler-info-circle me-1"></i>
                        Purchase Delivery dapat dibuat dari halaman detail Purchase Order
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

            let purchaseDeliveryTable;

            // Permission variables - using Spatie permissions
            const canRead = @json(auth()->user()->can('purchase_delivery.view'));
            const canViewContact = @json(auth()->user()->can('contact.view'));
            const canViewUser = @json(auth()->user()->can('user.view'));
            const canViewPurchaseOrder = @json(auth()->user()->can('purchase_order.view'));

            // Initialize DataTable
            function initPurchaseDeliveryTable() {
                if (purchaseDeliveryTable) {
                    purchaseDeliveryTable.destroy();
                }

                purchaseDeliveryTable = new DataTable('#purchase-delivery-table', {
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('main_transaction.purchase_delivery.index_data_table') }}',
                        data: function(d) {
                            d.date_range_filter = $('#date-range-picker').val();
                            d.delivery_date_filter = $('#delivery-date-filter').val();
                            d.supplier_filter = $('#supplier-filter').val();
                            d.delivery_search_filter = $('#delivery-search-filter').val();
                        }
                    },
                    columns: [
                        {
                            data: 'transaction_date',
                            width: '110px',
                            responsivePriority: 1,
                        },
                        {
                            data: 'reference_no',
                            width: '140px',
                            render: function(data, type, row) {
                                if (!data) return '-';
                                if (canRead) {
                                    const detailUrl =
                                        `{{ route('main_transaction.purchase_delivery.detail', ':id') }}`
                                        .replace(':id', row.id);
                                    return `<a href="${detailUrl}" class="text-primary fw-semibold">${data}</a>`;
                                }
                                return data;
                            }
                        },
                        {
                            data: 'delivery_number',
                            render: function(data, type, row) {
                                return data || '-';
                            }
                        },
                        {
                            data: 'supplier_name',
                            render: function(data, type, row) {
                                if (!data) return '-';
                                if (canViewContact && row.supplier_id) {
                                    const detailUrl =
                                        `{{ route('master_data.contact.detail', ':id') }}`
                                        .replace(':id', row.supplier_id);
                                    return `<a href="${detailUrl}" class="text-primary fw-semibold">${data}</a>`;
                                }
                                return data;
                            }
                        },
                        {
                            data: 'delivery_date',
                            responsivePriority: 10001,
                        },
                        {
                            data: 'po_reference',
                            responsivePriority: 10002,
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
                            data: 'status',
                            width: '130px',
                            responsivePriority: 1,
                            render: function(data, type, row) {
                                let badgeClass = 'bg-label-secondary';
                                switch (data) {
                                    case 'PENDING':
                                        badgeClass = 'bg-label-warning';
                                        break;
                                    case 'IN_TRANSIT':
                                        badgeClass = 'bg-label-info';
                                        break;
                                    case 'DELIVERED':
                                        badgeClass = 'bg-label-success';
                                        break;
                                    case 'CANCELLED':
                                        badgeClass = 'bg-label-danger';
                                        break;
                                }
                                return `<span class="badge ${badgeClass}">${data}</span>`;
                            }
                        },
                        {
                            data: 'created_by_name',
                            responsivePriority: 10003,
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
                            responsivePriority: 10004,
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
            initPurchaseDeliveryTable();

            // Tag column hidden by default (0=date,1=ref_no,2=delivery_no,3=supplier,4=delivery_date,5=po_ref,6=status,7=created_by,8=tags)
            purchaseDeliveryTable.column(8).visible(false);

            // Reset Filter
            $('#reset-filter').on('click', function() {
                $('#date-range-picker').val('');
                $('#delivery-date-filter').val('');
                $('#supplier-filter').val('').trigger('change');
                $('#delivery-search-filter').val('');
                if (purchaseDeliveryTable) {
                    purchaseDeliveryTable.draw();
                }
            });

            // Filter functionality with timeout
            let searchTimeout;
            $('#date-range-picker, #delivery-date-filter, #supplier-filter, #delivery-search-filter').on(
                'keyup change',
                function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(function() {
                        if (purchaseDeliveryTable) {
                            purchaseDeliveryTable.draw();
                        }
                    }, 400); // 400ms delay
                });

            // Tag Column Toggle Functionality
            $('#toggleTagColumn').on('change', function() {
                purchaseDeliveryTable.column(8).visible(this.checked);
            });
        });
    </script>
@endsection
