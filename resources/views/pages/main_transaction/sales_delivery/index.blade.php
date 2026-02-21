@extends('layouts.default')
@section('page_title')
    List Sales Delivery
@stop
@section('content')
<div class="row g-4 mb-4">
        <!-- Total SO -->
        <div class="col-lg-6 col-sm-6">
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

        <!-- Total SD -->
        <div class="col-lg-6 col-sm-6">
            <div class="card card-border-shadow-warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="icon-base ti tabler-truck icon-28px"></i>
                            </span>
                        </div>
                        <div>
                            <h4 class="mb-0">
                                <span class="text-warning">{{ $stats['sd_non_confirm'] }}</span>
                                <span class="text-muted fs-6">/</span>
                                <span class="text-success">{{ $stats['sd_confirm'] }}</span>
                            </h4>
                        </div>
                    </div>
                    <p class="mb-0 fw-semibold">Sales Delivery</p>
                    <small class="text-muted">
                        <span class="text-warning">Belum Konfirm</span> /
                        <span class="text-success">Terkonfirmasi</span>
                    </small>
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
                        <input type="text" id="date-range-picker" class="form-control" />
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
                        <label class="form-label">No Sales Delivery / Sales Order:</label>
                        <input type="text" class="form-control" id="delivery-no-filter"
                            placeholder="Cari Sales Delivery / Sales Order">
                    </div>
                </div>

                {{-- <div class="row mb-4"> --}}
                {{-- <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label">Status:</label>
                        <select class="global-select2 form-select" id="status-filter" data-allow-clear="true">
                            <option value="">Semua Status</option>
                            <option value="PENDING">PENDING - Menunggu</option>
                            <option value="IN_TRANSIT">IN_TRANSIT - Dalam Perjalanan</option>
                            <option value="DELIVERED">DELIVERED - Terkirim</option>
                            <option value="CANCELLED">CANCELLED - Dibatalkan</option>
                            <option value="RETURNED">RETURNED - Dikembalikan</option>
                        </select>
                    </div> --}}
                {{-- <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label">No Sales Order:</label>
                        <input type="text" class="form-control" id="sales-order-no-filter"
                            placeholder="Cari Sales Order">
                    </div> --}}
                {{-- </div> --}}

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
                            <th>No Sales Delivery</th>
                            <th>No Sales Order</th>
                            <th>Customer</th>
                            <th>Gudang</th>
                            <th>Ongkir</th>
                            <th>Status</th>
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
            const canEdit = @json(auth()->user()->can('sales_delivery.edit'));
            const canViewContact = @json(auth()->user()->can('contact.view'));
            const canViewUser = @json(auth()->user()->can('user.view'));
            const canViewSalesOrder = @json(auth()->user()->can('sales_order.view'));

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
                            // d.sales_order_no = $('#sales-order-no-filter').val();
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
                    columns: [{
                            data: 'delivery_date',
                            render: function(data) {
                                if (!data) return '-';
                                return new Date(data).toLocaleDateString('id-ID');
                            }
                        },
                        {
                            data: 'delivery_number',
                        },
                        {
                            data: 'sales_order_number',
                            render: function(data, type, row) {
                                if (!data) return '-';
                                if (canViewSalesOrder && row.sales_order_id) {
                                    const detailUrl =
                                        "{{ route('main_transaction.sales_order.detail', ':id') }}"
                                        .replace(':id', row.sales_order_id);
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
                            data: 'warehouse',
                        },
                        {
                            data: 'shipping_cost',
                            render: function(data, type, row) {
                                return `Rp ${parseInt(data || 0).toLocaleString('id-ID')}`;
                            }
                        },
                        {
                            data: 'is_confirmed',
                            width: '100px',
                            render: function(data, type, row) {
                                if (data === '1' || data === 1) {
                                    return `<span class="badge bg-label-success">Diterima</span>`;
                                } else {
                                    return `<span class="badge bg-label-info">Dikirim</span>`;
                                }
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
                                }).join('') || '-';
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
                                    "{{ route('main_transaction.sales_delivery.detail', ':id') }}"
                                    .replace(':id', row.id);
                                const editUrl =
                                    "{{ route('main_transaction.sales_delivery.edit', ':id') }}"
                                    .replace(':id', row.id);

                                let actions = '<div class="d-flex gap-2">';

                                // Detail button (always available)
                                actions += `<a href="${detailUrl}" class="btn btn-sm btn-primary waves-effect waves-light" title="Detail">
                                            <i class="icon-base ti tabler-eye"></i></a>`;

                                // Edit button 
                                // if (canEdit) {
                                //     actions += `<a href="${editUrl}" class="btn btn-sm btn-primary waves-effect waves-light" title="Edit">
                            //                <i class="icon-base ti tabler-edit"></i></a>`;
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
                    responsive: true,
                    orderCellsTop: true,
                    ordering: false
                });
            }

            // Initialize table
            initDataTable();

            // Filter functionality with timeout
            let searchTimeout;
            $('#date-range-picker, #customer-filter, #warehouse-filter, #delivery-no-filter, #status-filter, #sales-order-no-filter')
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

            // Tag Column Toggle Functionality
            $('#toggleTagColumn').on('change', function() {
                const tagColumn = salesDeliveryTable.column(
                    7); // Tag column index (0-based, tags is 8th column = index 7)
                tagColumn.visible(this.checked);
            });
        });
    </script>
@endsection
