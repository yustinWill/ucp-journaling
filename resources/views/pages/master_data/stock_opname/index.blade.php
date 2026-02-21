@extends('layouts.default')
@section('page_title')
    Stock Opname Management
@stop
@section('content')
    <!-- Summary Cards -->
    {{-- <div class="row">
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="icon-base ti tabler-clipboard-check icon-28px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0">6 Total Opname</h4>
                    </div>
                    <p class="mb-1">Stock Opname</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="icon-base ti tabler-clock icon-28px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0">2 Sedang Proses</h4>
                    </div>
                    <p class="mb-1">Sedang Dikerjakan</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="icon-base ti tabler-check icon-28px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0">3 Selesai</h4>
                    </div>
                    <p class="mb-1">Opname Selesai</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-danger h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-danger">
                                <i class="icon-base ti tabler-adjustments icon-28px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0">1 Perlu Adjustment</h4>
                    </div>
                    <p class="mb-1">Menunggu Adjustment</p>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Main Content -->
    <div class="mt-6">
        <div class="card">
            <h4 class="card-header">Kelola Stock Opname</h4>
            <div class="card-body">
                <!-- Advanced Search Form -->
                <form class="dt_adv_search" method="GET" autocomplete="off">
                    <div class="row">
                        <div class="col-12">
                            <div class="row g-3">
                                <div class="col-12 col-sm-6 col-lg-3">
                                    <label class="form-label">Tanggal Jurnal:</label>
                                    <input type="text" id="date-range-picker" class="form-control" />
                                </div>
                                <div class="col-12 col-sm-6 col-lg-3">
                                    <label class="form-label">Gudang:</label>
                                    <select class="global-select2 form-select" id="warehouse-filter"
                                        data-allow-clear="true">
                                        <option value="">Semua Gudang</option>
                                        @foreach ($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}">{{ $warehouse->code }} -
                                                {{ $warehouse->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-sm-6 col-lg-3">
                                    <label class="form-label">Status:</label>
                                    <select class="global-select2 form-select" id="status-filter" data-allow-clear="true">
                                        <option value="">Semua Status</option>
                                        @foreach ($stockOpnameStatuses as $status)
                                            <option value="{{ $status['value'] }}">{{ $status['label'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-sm-6 col-lg-3">
                                    <label class="form-label">No Opname:</label>
                                    <input type="text" class="form-control" id="no-opname-filter"
                                        placeholder="Cari Berdasarkan No Opname">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
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
                            {{-- <button type="button" id="btn-export" class="btn btn-md btn-primary waves-effect waves-light">
                                <span class="icon-xs icon-base ti tabler-file-download me-2"></span> Export
                            </button>
                            <button type="button" id="btn-import" class="btn btn-md btn-primary waves-effect waves-light">
                                <span class="icon-xs icon-base ti tabler-file-upload me-2"></span> Import
                            </button> --}}
                            @can('stock_opname.create')
                                <button type="button" id="btn-add-stock-opname"
                                    class="btn btn-md btn-primary waves-effect waves-light">
                                    <span class="icon-xs icon-base ti tabler-plus me-2"></span> Tambah Stock Opname
                                </button>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="card-datatable">
                <table id="stock-opname-table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No. Opname</th>
                            <th>Tanggal</th>
                            <th>Gudang</th>
                            <th>PIC</th>
                            <th>Tag</th>
                            <th>Varian Produk</th>
                            <th>Sistem QTY</th>
                            <th>Fisik QTY</th>
                            <th>Selisih QTY</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('js_script')
    <script>
        $(function() {
            //DataTable
            const stockOpnameTable = document.querySelector('#stock-opname-table');
            let dt_adv_filter;

            if (stockOpnameTable) {
                dt_adv_filter = new DataTable(stockOpnameTable, {
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: `{{ route('master_data.stock_opname.index_data_table') }}`,
                        data: function(d) {
                            d.date = $('#date-range-picker').val();
                            d.warehouse = $('#warehouse-filter').val();
                            d.status = $('#status-filter').val();
                            d.no_opname = $('#no-opname-filter').val();
                        }
                    },
                    columns: [{
                            data: 'stock_opname_no',
                        },
                        {
                            data: 'transaction_date',
                        },
                        {
                            data: 'warehouse_name',
                            render: function(data, type, row) {
                                const detailUrl =
                                    `{{ route('master_data.warehouse.detail', ':id') }}`.replace(
                                        ':id', row.warehouse_id);
                                return `<a href="${detailUrl}" class="text-primary fw-semibold">${data}</a>`;
                            }
                        },
                        {
                            data: 'pic_name',
                            render: function(data, type, row) {
                                const detailUrl = `{{ route('master_data.user.detail', ':id') }}`
                                    .replace(':id', row.pic_id);
                                return `<a href="${detailUrl}" class="text-primary fw-semibold">${data}</a>`;
                            }
                        },
                        {
                            data: 'tags',
                            render: function(data, type, row) {
                                return data.map(tag => {
                                    if (tag.name == null) {
                                        return ``;
                                    }
                                    return `<span class="badge ${tag.preview} me-1">${tag.name}</span>`;
                                }).join('');
                            }
                        },
                        {
                            data: 'count_product',
                        },
                        {
                            data: 'total_system_stock',
                        },
                        {
                            data: 'total_physical_stock',
                        },
                        {
                            data: 'total_variance',
                        },
                        {
                            data: 'status',
                        },
                        {
                            data: null,
                            width: '120px'
                        }
                    ],
                    columnDefs: [{
                            targets: 1,
                            render: function(data, type, full, meta) {
                                return data; // Display YYYY-MM-DD format directly
                            }
                        },
                        {
                            targets: 2, // Warehouse column
                            render: function(data, type, full, meta) {
                                const warehouseDetailUrl =
                                    "{{ route('master_data.warehouse.detail', ':id') }}".replace(
                                        ':id', full.warehouse_id);
                                return `<a href="${warehouseDetailUrl}" class="text-primary fw-semibold">${data}</a>`;
                            }
                        },
                        {
                            targets: 3, // PIC column
                            render: function(data, type, full, meta) {
                                const userDetailUrl =
                                    "{{ route('master_data.user.detail', ':id') }}".replace(':id',
                                        full.pic_id);
                                return `<a href="${userDetailUrl}" class="text-primary fw-semibold">${data}</a>`;
                            }
                        },
                        {
                            targets: 8,
                            render: function(data, type, full, meta) {
                                if (data > 0) {
                                    return `<span class="text-success">+${data}</span>`;
                                } else if (data < 0) {
                                    return `<span class="text-danger">${data}</span>`;
                                }
                                return data;
                            }
                        },
                        {
                            targets: 9, // Status column (updated index due to added tag column)
                            render: function(data, type, full, meta) {
                                let badgeClass = '';
                                // switch (data) {
                                //     case 'ADJUSTMENT_PENDING':
                                //         badgeClass = 'bg-label-info';
                                //         break;
                                //     case 'ADJUSTMENT_CREATED':
                                //         badgeClass = 'bg-label-primary';
                                //         break;
                                //     case 'COMPLETED':
                                //         badgeClass = 'bg-label-success';
                                //         break;
                                //     case 'CANCELLED':
                                //         badgeClass = 'bg-label-danger';
                                //         break;
                                //     default:
                                //         badgeClass = 'bg-label-secondary';
                                // }

                                // const statusLabels = {
                                //     'ADJUSTMENT_PENDING': 'PENDING ADJUSTMENT',
                                //     'ADJUSTMENT_CREATED': 'ADJUSTMENT CREATED',
                                //     'COMPLETED': 'COMPLETED',
                                //     'CANCELLED': 'CANCELLED'
                                // };

                                switch (data) {
                                    case 'EXECUTED':
                                        badgeClass = 'bg-label-primary';
                                        break;
                                    case 'SUBMITTED':
                                        badgeClass = 'bg-label-warning';
                                        break;
                                }

                                const statusLabels = {
                                    'EXECUTED': 'EXECUTED',
                                    'SUBMITTED': 'SUBMITTED'
                                };

                                return `<span class="badge ${badgeClass}">${statusLabels[data] || data}</span>`;
                            }
                        },
                        {
                            targets: -1,
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row, meta) {
                                const editUrl =
                                    "{{ route('master_data.stock_opname.edit', ':id') }}".replace(
                                        ':id', row.id);
                                const detailUrl =
                                    "{{ route('master_data.stock_opname.detail', ':id') }}"
                                    .replace(':id', row.id);

                                // Only show Detail and Edit buttons (removed Buat Adjustment from table)
                                let buttons = `
                                <div class="d-flex gap-2">
                                    <a href="${detailUrl}" class="btn btn-sm btn-primary waves-effect waves-light" title="Detail">
                                        <i class="icon-base ti tabler-eye"></i>
                                    </a>
                                    
                                </div>`;
                                // <a href="${editUrl}" class="btn btn-sm btn-primary waves-effect waves-light" title="Edit">
                                //         <i class="icon-base ti tabler-edit"></i>
                                //     </a>

                                return buttons;
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
                    orderCellsTop: true
                });
            }

            // Button click events
            $('#btn-add-stock-opname').on('click', function() {
                window.location.href = "{{ route('master_data.stock_opname.add') }}";
            });

            $('#btn-export').on('click', function() {
                window.location.href = "{{ route('master_data.stock_opname.export') }}";
            });

            $('#btn-import').on('click', function() {
                window.location.href = "{{ route('master_data.stock_opname.import') }}";
            });

            // Create adjustment function
            window.createAdjustment = function(id) {
                Swal.fire({
                    title: 'Buat Stok Adjustment?',
                    text: 'Apakah Anda yakin ingin membuat stok adjustment dari hasil opname ini?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Buat Adjustment',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-secondary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('master_data.stock_opname.create_adjustment', ':id') }}'
                                .replace(':id', id),
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: response.message + ' dengan nomor ' +
                                            response.adjustment_number,
                                        icon: 'success',
                                        customClass: {
                                            confirmButton: 'btn btn-primary'
                                        },
                                        buttonsStyling: false
                                    }).then(() => {
                                        dt_adv_filter.draw();
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Gagal!',
                                        text: response.message ||
                                            'Terjadi kesalahan saat membuat adjustment',
                                        icon: 'error',
                                        customClass: {
                                            confirmButton: 'btn btn-primary'
                                        },
                                        buttonsStyling: false
                                    });
                                }
                            },
                            error: function() {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Terjadi kesalahan saat membuat adjustment',
                                    icon: 'error',
                                    customClass: {
                                        confirmButton: 'btn btn-primary'
                                    },
                                    buttonsStyling: false
                                });
                            }
                        });
                    }
                });
            };

            // Tag Column Toggle Functionality
            $('#toggleTagColumn').on('change', function() {
                const tagColumn = dt_adv_filter.column(4); // Tag column index (0-based)
                tagColumn.visible(this.checked);
            });

            // Initialize tag column visibility based on checkbox state
            const initialTagVisibility = $('#toggleTagColumn').is(':checked');
            dt_adv_filter.column(4).visible(initialTagVisibility);

            let searchTimeout;
            $('#date-range-picker, #no-opname-filter, #warehouse-filter, #status-filter').on('keyup change',
                function() {
                    clearTimeout(searchTimeout);
                    const input = this;
                    searchTimeout = setTimeout(function() {
                        if (dt_adv_filter) {
                            dt_adv_filter.search(input.value).draw();
                        }
                    }, 400);
                });
        });
    </script>
@endsection
