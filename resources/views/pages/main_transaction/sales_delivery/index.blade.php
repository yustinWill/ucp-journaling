@extends('layouts.default')
@section('page_title')
    List Sales Delivery
@stop
@section('content')
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

        <!-- SD Menunggu Pengiriman -->
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-dark h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-dark">
                                <i class="icon-base ti tabler-clock icon-28px"></i>
                            </span>
                        </div>
                        <div>
                            <h4 class="mb-0 text-dark">{{ $stats['sd_waiting'] }}</h4>
                        </div>
                    </div>
                    <p class="mb-0 fw-semibold">Menunggu Pengiriman</p>
                    <small class="text-muted">Belum diterima</small>
                </div>
            </div>
        </div>

        <!-- SD Dikirim -->
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="icon-base ti tabler-truck-delivery icon-28px"></i>
                            </span>
                        </div>
                        <div>
                            <h4 class="mb-0 text-warning">{{ $stats['sd_non_confirm'] }}</h4>
                        </div>
                    </div>
                    <p class="mb-0 fw-semibold">Dikirim</p>
                    <small class="text-muted">Sudah diterima, belum konfirmasi</small>
                </div>
            </div>
        </div>

        <!-- SD Terkirim -->
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="icon-base ti tabler-truck-loading icon-28px"></i>
                            </span>
                        </div>
                        <div>
                            <h4 class="mb-0 text-success">{{ $stats['sd_confirm'] }}</h4>
                        </div>
                    </div>
                    <p class="mb-0 fw-semibold">Terkirim</p>
                    <small class="text-muted">Sudah terkonfirmasi</small>
                </div>
            </div>
        </div>
    </div>
    <!-- Main Content Card -->
    <div class="mt-6">
        <div class="card">
            <h4 class="card-header">Kelola Sales Delivery</h4>
            <div class="card-body">
                <!-- Advanced Search Form -->
                <div class="row mb-4">
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label">Tanggal Pengiriman:</label>
                        <input type="text" id="date-range-picker" class="form-control" placeholder="Pilih tanggal" />
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Customer:</label>
                        <select class="global-select2 form-select" id="customer-filter" data-allow-clear="true">
                            <option value="">Pilih Customer</option>
                            @if (isset($customers))
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Gudang:</label>
                        <select class="global-select2 form-select" id="warehouse-filter" data-allow-clear="true">
                            <option value="">Pilih Gudang</option>
                            @if (isset($warehouses))
                                @foreach ($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}">{{ $warehouse->code }} - {{ $warehouse->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label">Status Konfirmasi:</label>
                        <select class="form-select" id="status-filter" data-allow-clear="true">
                            <option value="">Semua Status</option>
                            <option value="0">Dikirim</option>
                            <option value="1">Terkonfirmasi</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label">No Sales Delivery / Sales Order:</label>
                        <input type="text" class="form-control" id="delivery-no-filter"
                            placeholder="Cari Sales Delivery / Sales Order">
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
                            

                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="card-datatable">
                <table id="sales-delivery-table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>No Delivery</th>
                            <th>Customer</th>
                            <th>Alamat</th>
                            <th>Status</th>
                            <th>Referensi SO</th>
                            <th>Gudang</th>
                            <th>Ongkir</th>
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
                        Sales Delivery dapat dibuat dari halaman detail Sales Order
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

            let salesDeliveryTable;

            // Define permissions as JavaScript variables - using Spatie permissions
            const canRead = @json(auth()->user()->can('sales_delivery.view'));
            const canEdit = @json(auth()->user()->can('sales_delivery.edit'));
            const canViewContact = @json(auth()->user()->can('contact.view'));
            const canViewUser = @json(auth()->user()->can('user.view'));
            const canViewSalesOrder = @json(auth()->user()->can('sales_order.view'));
            const canViewWarehouse = @json(auth()->user()->can('warehouse.view'));

            // Initialize DataTable
            function initDataTable() {
                if (salesDeliveryTable) {
                    salesDeliveryTable.destroy();
                }

                salesDeliveryTable = new DataTable('#sales-delivery-table', {
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('main_transaction.sales_delivery.index_data_table') }}",
                        data: function(d) {
                            d.date_range = $('#date-range-picker').val();
                            d.customer_id = $('#customer-filter').val();
                            d.warehouse_id = $('#warehouse-filter').val();
                            d.delivery_no = $('#delivery-no-filter').val();
                            d.status = $('#status-filter').val();
                        },
                        error: function(xhr, error, code) {
                            console.log('DataTable Ajax Error:', xhr.responseText);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Gagal memuat data tabel',
                                showConfirmButton: true
                            });
                        }
                    },
                    columns: [
                        {
                            data: 'delivery_date',
                            width: '110px',
                            render: function(data) {
                                if (!data) return '-';
                                return new Date(data).toLocaleDateString('id-ID');
                            }
                        },
                        {
                            data: 'delivery_number',
                            render: function(data, type, row) {
                                const detailUrl = "{{ route('main_transaction.sales_delivery.detail', ':id') }}".replace(':id', row.id);
                                return `<a href="${detailUrl}" class="text-primary fw-bold">${data}</a>`;
                            }
                        },
                        {
                            data: 'sales_order_number',
                            render: function(data, type, row) {
                                if (!data) return '-';
                                if (canRead) {
                                    const detailUrl =
                                        "{{ route('main_transaction.sales_delivery.detail', ':id') }}"
                                        .replace(':id', row.id);
                                    return `<a href="${detailUrl}" class="text-primary fw-semibold">${data}</a>`;
                                }
                                return data;
                            }
                        },
                        {
                            data: 'customer',
                            render: function(data, type, row) {
                                if (!data) return '-';
                                if (canViewContact && row.customer_id) {
                                    const detailUrl =
                                        "{{ route('master_data.contact.detail', ':id') }}"
                                        .replace(':id', row.customer_id);
                                    return `<a href="${detailUrl}" class="text-primary fw-semibold">${data}</a>`;
                                }
                                return data;
                            }
                        },
                        {
                            data: 'delivery_address',
                            render: function(data) {
                                return data || '-';
                            }
                        },
                        {
                            data: null,
                            width: '180px',
                            responsivePriority: 1,
                            render: function(data, type, row) {
                                const isAccepted = row.is_accepted;
                                const isConfirmed = row.is_confirmed === '1' || row.is_confirmed === 1;

                                if (isConfirmed) {
                                    return `<span class="badge bg-label-success">Terkirim</span>`;
                                } else if (isAccepted) {
                                    return `<span class="badge bg-label-warning">Dikirim</span>`;
                                } else {
                                    return `<span class="badge bg-label-dark">Menunggu Pengiriman</span>`;
                                }
                            }
                        },
                        {
                            data: 'sales_order_number',
                            responsivePriority: 10001,
                            render: function(data, type, row) {
                                if (!data) return '-';
                                if (canViewSalesOrder && row.so_id) {
                                    const detailUrl =
                                        "{{ route('main_transaction.sales_order.detail', ':id') }}"
                                        .replace(':id', row.so_id);
                                    return `<a href="${detailUrl}" class="text-primary fw-semibold">${data}</a>`;
                                }
                                return data;
                            }
                        },
                        {
                            data: 'warehouse',
                            responsivePriority: 10002,
                            render: function(data, type, row) {
                                if (!data) return '-';
                                if (canViewWarehouse && row.warehouse_id) {
                                    const detailUrl =
                                        "{{ route('master_data.warehouse.detail', ':id') }}"
                                        .replace(':id', row.warehouse_id);
                                    return `<a href="${detailUrl}" class="text-primary fw-semibold">${data}</a>`;
                                }
                                return data;
                            }
                        },
                        {
                            data: 'shipping_cost',
                            responsivePriority: 10003,
                            render: function(data) {
                                return `Rp ${parseInt(data || 0).toLocaleString('id-ID')}`;
                            }
                        },
                        {
                            data: 'tags',
                            responsivePriority: 10004,
                            render: function(data, type, row) {
                                if (!data) return '-';
                                let tags = JSON.parse(data);
                                return tags.map(tag => {
                                    if (tag.name == null) {
                                        return '';
                                    }
                                    return `<span class="badge ${tag.preview} me-1">${tag.name}</span>`;
                                }).join('') || '-';
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
                    responsive: true,
                    orderCellsTop: true,
                    ordering: false
                });
            }

            // Initialize table
            initDataTable();

            // Tag column hidden by default (0=date,1=delivery_number,2=customer,3=delivery_address,4=status,5=so_reference,6=warehouse,7=shipping_cost,8=tags)
            salesDeliveryTable.column(8).visible(false);

            // Date range picker event - uses global bootstrap daterangepicker (with default last 30 days)
            $('#date-range-picker').on('apply.daterangepicker', function(ev, picker) {
                if (salesDeliveryTable) {
                    salesDeliveryTable.draw();
                }
            });

            $('#date-range-picker').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                if (salesDeliveryTable) {
                    salesDeliveryTable.draw();
                }
            });

            // Filter functionality with timeout
            let searchTimeout;
            $('#customer-filter, #warehouse-filter, #delivery-no-filter, #status-filter')
                .on(
                    'keyup change',
                    function() {
                        clearTimeout(searchTimeout);
                        const input = this;
                        searchTimeout = setTimeout(function() {
                            if (salesDeliveryTable) {
                                salesDeliveryTable.draw();
                            }
                        }, 400); // 400ms delay
                    });

            // Reset Filter
            $('#reset-filter').on('click', function() {
                $('#date-range-picker').val('');
                $('#customer-filter').val('').trigger('change');
                $('#warehouse-filter').val('').trigger('change');
                $('#status-filter').val('').trigger('change');
                $('#delivery-no-filter').val('');
                if (salesDeliveryTable) {
                    salesDeliveryTable.draw();
                }
            });

            // Tag Column Toggle Functionality
            $('#toggleTagColumn').on('change', function() {
                const tagColumn = salesDeliveryTable.column(8);
                tagColumn.visible(this.checked);
            });
        });
    </script>
@endsection
