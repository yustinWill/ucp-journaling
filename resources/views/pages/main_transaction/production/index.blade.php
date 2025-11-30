@extends('layouts.default')
@section('page_title')
    List Produksi
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
                                <i class="icon-base ti tabler-package icon-28px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0">{{ $totalProductions ?? 0 }}</h4>
                    </div>
                    <p class="mb-1">Total Produksi</p>
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
                        <h4 class="mb-0">{{ $inProgressProductions ?? 0 }}</h4>
                    </div>
                    <p class="mb-1">In Progress</p>
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
                        <h4 class="mb-0">{{ $completedProductions ?? 0 }}</h4>
                    </div>
                    <p class="mb-1">Completed</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-info h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="icon-base ti tabler-calendar icon-28px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0">{{ $plannedProductions ?? 0 }}</h4>
                    </div>
                    <p class="mb-1">Planned</p>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Main Content Card -->
    <div class="mt-6">
        <div class="card">
            <h4 class="card-header">Kelola Produksi</h4>
            <div class="card-body">
                <!-- Advanced Search Form -->
                <div class="row mb-4">

                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label">Tanggal Jurnal:</label>
                        <input type="text" id="date-range-picker" class="form-control" />
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Warehouse:</label>
                        <select class="global-select2 form-select" id="warehouse-filter" data-allow-clear="true">
                            <option value="">Semua Warehouse</option>
                            @foreach ($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->code }} - {{ $warehouse->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Product:</label>
                        <select class="global-select2 form-select" id="product-filter" data-allow-clear="true">
                            <option value="">Semua Product</option>
                            @foreach ($bundleProducts as $product)
                                <option value="{{ $product->id }}">{{ $product->code }} - {{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">No Produksi:</label>
                        <input type="text" class="form-control dt-input dt-full-name" data-column="1"
                            placeholder="Cari Berdasarkan Kode Produksi" id="production-number" data-column-index="0" />
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-end">
                    <div class="d-flex gap-2">
                        {{-- @if ($canExportProduction)
                            <a href="{{ route('main_transaction.production.export') }}"
                                class="btn btn-md btn-primary waves-effect waves-light"><span
                                    class="icon-xs icon-base ti tabler-file-download me-2"></span> Export</a>
                        @endif --}}
                        @if ($canAddProduction)
                            <a href="{{ route('main_transaction.production.add') }}"
                                class="btn btn-md btn-primary waves-effect waves-light"><span
                                    class="icon-xs icon-base ti tabler-plus me-2"></span> Tambah Produksi</a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="card-datatable">
                <table id="production-table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 100px;">Tanggal</th>
                            <th style="width: 140px;">No Transaksi</th>
                            <th>Nama Produk</th>
                            <th style="width: 180px;">Kode Produk</th>
                            <th style="width: 100px;">Jumlah Produksi</th>
                            <th style="width: 120px;">Unit</th>
                            <th style="width: 100px;">HPP</th>
                             <th style="width: 100px;">Aksi</th>
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
            let productionTable;

            function initProductionTable() {
                if (productionTable) {
                    productionTable.destroy();
                }

                productionTable = new DataTable('#production-table', {
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('main_transaction.production.index_data_table') }}',
                        data: function(d) {
                            d.date = $('#date-range-picker').val();
                            d.product = $('#product-filter').val();
                            d.production_no = $('#production-number').val();
                            d.warehouse = $('#warehouse-filter').val();
                            return d;
                        }
                    },
                    columns: [{
                            data: 'production_date',
                            render: function(data, type, row) {
                                return moment(data).format('DD/MM/YYYY');
                            }
                        },
                        {
                            data: 'production_number',
                            render: function(data, type, row) {
                                @if ($canViewDetails)
                                    const detailUrl =
                                        "{{ route('main_transaction.production.detail', ':id') }}"
                                        .replace(':id', row.id);
                                    return `<a href="${detailUrl}" class="text-primary fw-semibold">${data}</a>`;
                                @else
                                    return data;
                                @endif
                            }
                        },
                        {
                            data: 'bundle_product_name'
                        },
                        {
                            data: 'bundle_product_code',
                        },
                        {
                            data: 'quantity_produced',
                            className: 'text-end',
                            render: function(data, type, row) {
                                return new Intl.NumberFormat('id-ID', {
                                    minimumFractionDigits: 0,
                                    maximumFractionDigits: 2
                                }).format(data || 0);
                            }
                        },
                        {
                            data: 'unit_code',
                            className: 'text-center',
                        },
                        {
                            data: 'hpp_production',
                            className: 'text-end',
                            render: function(data, type, row) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(data || 0);
                            }
                        },
                        {
                            data: null,
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row, meta) {
                                let actions = '';

                                @if ($canViewDetails)
                                    const detailUrl =
                                        "{{ route('main_transaction.production.detail', ':id') }}"
                                        .replace(':id', row.id);
                                    actions += `<a href="${detailUrl}" class="btn btn-sm btn-primary waves-effect waves-light" title="Detail">
                                        <i class="icon-base ti tabler-eye"></i>
                                    </a>`;
                                @endif

                                // Edit functionality removed - use Reversal process for modifications

                                // @if ($canReverseProduction)
                                // if (row.status === 'COMPLETED') {
                                //     const reversalUrl = "{{ route('main_transaction.production.reversal', ':id') }}".replace(':id', row.id);
                                //     actions += `<a href="${reversalUrl}" class="btn btn-sm btn-danger waves-effect waves-light ms-1" title="Reversal">
                            //                     <i class="icon-base ti tabler-arrow-back-up"></i>
                            //                 </a>`;
                                // }
                                // @endif

                                // @if ($canDeleteProduction)
                                // if (row.status === 'DRAFT') {
                                //     actions += `<button type="button" class="btn btn-sm btn-danger waves-effect waves-light ms-1" 
                            //                        onclick="deleteProduction(${row.id})" title="Delete">
                            //                     <i class="icon-base ti tabler-trash"></i>
                            //                 </button>`;
                                // }
                                // @endif

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
            initProductionTable();

            let searchTimeout;
            $('#date-range-picker,#warehouse-filter,#product-filter,#production-number').on('keyup change',
                function() {
                    clearTimeout(searchTimeout);
                    const input = this;
                    searchTimeout = setTimeout(function() {
                        if (productionTable) {
                            productionTable.search(input.value).draw();
                        }
                    }, 400); // 400ms delay
                });
        });

    </script>
@endsection
