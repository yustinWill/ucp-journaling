@extends('layouts.default')
@section('page_title')
    Produk - Barang & Jasa
@stop
@section('content')
    <!-- Summary Cards -->
    <div class="row">
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-primary"><i
                                    class="icon-base ti tabler-box icon-28px"></i></span>
                        </div>
                        <h4 class="mb-0">6 Total Produk</h4>
                    </div>
                    <p class="mb-1">Stok Tersedia</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-warning"><i
                                    class="icon-base ti tabler-package-import icon-28px"></i></span>
                        </div>
                        <h4 class="mb-0">1 Total Produk</h4>
                    </div>
                    <p class="mb-1">Stok Segera Habis</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-danger h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-danger"><i
                                    class="icon-base ti tabler-box-off icon-28px"></i></span>
                        </div>
                        <h4 class="mb-0">1 Total Produk</h4>
                    </div>
                    <p class="mb-1">Stok Habis</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-success"><i
                                    class="icon-base ti tabler-building-warehouse icon-28px"></i></span>
                        </div>
                        <h4 class="mb-0">5 Total Gudang</h4>
                    </div>
                    <p class="mb-1">Gudang</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="mt-4">
        <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#products-tab" type="button"
                role="tab" aria-controls="products-tab" aria-selected="true">
                <i class="icon-base ti tabler-box me-2"></i>
                Barang & Jasa
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#warehouse-tab" type="button"
                role="tab" aria-controls="warehouse-tab" aria-selected="false">
                <i class="icon-base ti tabler-building-warehouse me-2"></i>
                Gudang
            </button>
        </li>
        </ul>
    </div>

    <div class="tab-content p-0 mt-3">
        <!-- Products Tab -->
        <div class="tab-pane fade show active" id="products-tab" role="tabpanel">
            <div class="card">
                <h4 class="card-header">Kelola Produk</h4>
                <div class="card-body">
                    <!-- Advanced Search Form -->
                    <form class="dt_adv_search" method="GET">
                        <div class="row">
                            <div class="col-12">
                                <div class="row g-3">
                                    <div class="col-12 col-sm-6 col-lg-4">
                                        <label class="form-label">Kategori:</label>
                                        <select class="global-select2 form-select" id="products-category-filter">
                                            <option value="">Semua Kategori</option>
                                            @foreach($productCategories as $category)
                                                <option value="{{ $category['value'] }}">{{ $category['label'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4">
                                        <label class="form-label">Status Stok:</label>
                                        <select class="global-select2 form-select" id="products-stock-filter">
                                            <option value="">Semua Status</option>
                                            @foreach($stockStatuses as $status)
                                                <option value="{{ $status['value'] }}">{{ $status['label'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4">
                                        <label class="form-label">Pencarian:</label>
                                        <input type="text" class="form-control" id="products-search" placeholder="Cari Produk">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <hr class="my-6 mx-n6" />
                    <!-- Action Buttons -->
                    <div class="mt-4">
                        <div class="d-flex justify-content-end">
                            <div class="d-flex gap-2">
                                <div class="dropdown">
                                    <button type="button" class="btn btn-md btn-primary dropdown-toggle waves-effect waves-light" data-bs-toggle="dropdown">
                                        <span class="icon-xs icon-base ti tabler-file-upload me-2"></span> Import
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" id="btn-import-products">Produk Baru</a></li>
                                        <li><a class="dropdown-item" href="#" id="btn-import-update">Update Produk</a></li>
                                        <li><a class="dropdown-item" href="#" id="btn-import-bundle">Produk Bundle</a></li>
                                    </ul>
                                </div>
                                <div class="dropdown">
                                    <button type="button" class="btn btn-md btn-primary dropdown-toggle waves-effect waves-light" data-bs-toggle="dropdown">
                                        <span class="icon-xs icon-base ti tabler-file-download me-2"></span> Export
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" id="btn-export-products">Semua Produk</a></li>
                                        <li><a class="dropdown-item" href="#" id="btn-export-bundle">Produk Bundle & Komposisi</a></li>
                                    </ul>
                                </div>
                                <button type="button" id="btn-add-products"
                                    class="btn btn-md btn-primary waves-effect waves-light"><span
                                        class="icon-xs icon-base ti tabler-plus me-2"></span> Tambah Data</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Data Table -->
                <div class="card-datatable">
                    <table id="products-table" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nama Produk</th>
                                <th>Kode Produk</th>
                                <th>Kategori</th>
                                <th>Total Stok</th>
                                <th>Qty Tersedia</th>
                                <th>Batas Minimum</th>
                                <th>Unit</th>
                                <th>Harga Rata-Rata</th>
                                <th>Harga Beli Terakhir</th>
                                <th>Harga Beli</th>
                                <th>Harga Jual</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        <!-- Warehouse Tab -->
        <div class="tab-pane fade" id="warehouse-tab" role="tabpanel">
            <div class="card">
                <h4 class="card-header">Kelola Gudang</h4>
                <div class="card-body">
                    <!-- Advanced Search Form -->
                    <form class="dt_adv_search" method="GET">
                        <div class="row">
                            <div class="col-12">
                                <div class="row g-3">
                                    <div class="col-12 col-sm-6 col-lg-4">
                                        <label class="form-label">Status:</label>
                                        <select class="global-select2 form-select" id="warehouse-status-filter">
                                            <option value="">Semua Status</option>
                                            @foreach($warehouseStatuses as $status)
                                                <option value="{{ $status['value'] }}">{{ $status['label'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4">
                                        <label class="form-label">Lokasi:</label>
                                        <select class="global-select2 form-select" id="warehouse-location-filter">
                                            <option value="">Semua Lokasi</option>
                                            @foreach($warehouseLocations as $location)
                                                <option value="{{ $location['value'] }}">{{ $location['label'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4">
                                        <label class="form-label">Pencarian:</label>
                                        <input type="text" class="form-control" id="warehouse-search" placeholder="Cari Gudang">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <hr class="my-6 mx-n6" />
                    <!-- Action Buttons -->
                    <div class="mt-4">
                        <div class="d-flex justify-content-end">
                            <div class="d-flex gap-2">
                                <button type="button" id="btn-import-warehouse"
                                    class="btn btn-md btn-primary waves-effect waves-light"><span
                                        class="icon-xs icon-base ti tabler-file-upload me-2"></span> Import</button>
                                <button type="button" id="btn-export-warehouse"
                                    class="btn btn-md btn-primary waves-effect waves-light"><span
                                        class="icon-xs icon-base ti tabler-file-download me-2"></span> Export</button>
                                <button type="button" id="btn-add-warehouse"
                                    class="btn btn-md btn-primary waves-effect waves-light"><span
                                        class="icon-xs icon-base ti tabler-plus me-2"></span> Tambah Data</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Data Table -->
                <div class="card-datatable">
                    <table id="warehouse-table" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Kode Gudang</th>
                                <th>Nama Gudang</th>
                                <th>Penanggung Jawab</th>
                                <th>Alamat</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop
@section('js_script')
    <script>
        $(function() {
            let productsTable, warehouseTable;
            
            // Initialize Products Table
            function initProductsTable() {
                if (productsTable) {
                    productsTable.destroy();
                }
                
                productsTable = new DataTable('#products-table', {
                    ajax: {
                        url: `{{ route('master_data.product.index_data_table') }}`,
                        data: {
                            tab: 'products'
                        }
                    },
                    columns: [
                        { 
                            data: 'product_name',
                            render: function(data, type, row) {
                                const detailUrl = `{{ route('master_data.product.detail', ':id') }}`.replace(':id', row.id);
                                return `<a href="${detailUrl}" class="text-primary fw-semibold">${data}</a>`;
                            }
                        },
                        { data: 'product_code' },
                        { data: 'category' },
                        { 
                            data: 'total_stock',
                            className: 'text-end',
                            render: function(data, type, row) {
                                if (type === 'display' && data != null && !isNaN(data)) {
                                    return parseFloat(data).toLocaleString('id-ID');
                                }
                                return data ?? '0';
                            }
                        },
                        { 
                            data: 'available_qty',
                            className: 'text-end',
                            render: function(data, type, row) {
                                if (type === 'display' && data != null && !isNaN(data)) {
                                    return parseFloat(data).toLocaleString('id-ID');
                                }
                                return data ?? '0';
                            }
                        },
                        { 
                            data: 'minimum',
                            className: 'text-end',
                            render: function(data, type, row) {
                                if (type === 'display' && data != null && !isNaN(data)) {
                                    return parseFloat(data).toLocaleString('id-ID');
                                }
                                return data ?? '0';
                            }
                        },
                        { data: 'unit' },
                        { 
                            data: 'average_price',
                            className: 'text-end',
                            render: function(data, type, row) {
                                if (type === 'display' && data != null && !isNaN(data)) {
                                    return 'Rp ' + parseFloat(data).toLocaleString('id-ID');
                                }
                                return data ?? 'Rp 0';
                            }
                        },
                        { 
                            data: 'last_purchase_price',
                            className: 'text-end',
                            render: function(data, type, row) {
                                if (type === 'display' && data != null && !isNaN(data)) {
                                    return 'Rp ' + parseFloat(data).toLocaleString('id-ID');
                                }
                                return data ?? 'Rp 0';
                            }
                        },
                        { 
                            data: 'purchase_price',
                            className: 'text-end',
                            render: function(data, type, row) {
                                if (type === 'display' && data != null && !isNaN(data)) {
                                    return 'Rp ' + parseFloat(data).toLocaleString('id-ID');
                                }
                                return data ?? 'Rp 0';
                            }
                        },
                        { 
                            data: 'selling_price',
                            className: 'text-end',
                            render: function(data, type, row) {
                                if (type === 'display' && data != null && !isNaN(data)) {
                                    return 'Rp ' + parseFloat(data).toLocaleString('id-ID');
                                }
                                return data ?? 'Rp 0';
                            }
                        },
                        {
                            data: null,
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row, meta) {
                                const detailUrl = `{{ route('master_data.product.detail', ':id') }}`.replace(':id', row.id);
                                const editUrl = `{{ route('master_data.product.edit', ':id') }}`.replace(':id', row.id);
                                return `
                                <div class="d-flex gap-2">
                                    <a href="${detailUrl}" class="btn btn-sm btn-primary waves-effect waves-light" title="Detail">
                                        <i class="icon-base ti tabler-eye"></i>
                                    </a>
                                    <a href="${editUrl}" class="btn btn-sm btn-primary waves-effect waves-light" title="Edit">
                                        <i class="icon-base ti tabler-edit"></i>
                                    </a>
                                </div>
                                `;
                            }
                        }
                    ],
                    layout: {
                        topStart: { rowClass: 'm-0', features: [] },
                        topEnd: {},
                        bottomStart: { rowClass: 'row mx-3 justify-content-between', features: ['info'] },
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
                    responsive: true
                });
            }
            
            // Initialize Warehouse Table
            function initWarehouseTable() {
                if (warehouseTable) {
                    warehouseTable.destroy();
                }
                
                warehouseTable = new DataTable('#warehouse-table', {
                    ajax: {
                        url: `{{ route('master_data.product.index_warehouse_data_table') }}`,
                        data: {
                            tab: 'warehouse'
                        }
                    },
                    columns: [
                        { data: 'warehouse_code' },
                        { 
                            data: 'warehouse_name',
                            render: function(data, type, row) {
                                return `<a href="#" class="text-primary">${data}</a>`;
                            }
                        },
                        { data: 'pic_name' },
                        { data: 'address' },
                        { 
                            data: 'status',
                            render: function(data, type, row) {
                                let badgeClass = data === 'ACTIVE' ? 'bg-success' : 'bg-danger';
                                return `<span class="badge ${badgeClass}">${data}</span>`;
                            }
                        },
                        {
                            data: null,
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row, meta) {
                                const detailUrl = `{{ route('master_data.product.detail', ':id') }}`.replace(':id', row.id);
                                const editUrl = `{{ route('master_data.product.edit', ':id') }}`.replace(':id', row.id);
                                return `
                                <div class="d-flex gap-2">
                                    <a href="${detailUrl}" class="btn btn-sm btn-primary waves-effect waves-light" title="Detail">
                                        <i class="icon-base ti tabler-eye"></i>
                                    </a>
                                    <a href="${editUrl}" class="btn btn-sm btn-primary waves-effect waves-light" title="Edit">
                                        <i class="icon-base ti tabler-edit"></i>
                                    </a>
                                </div>
                                `;
                            }
                        }
                    ],
                    layout: {
                        topStart: { rowClass: 'm-0', features: [] },
                        topEnd: {},
                        bottomStart: { rowClass: 'row mx-3 justify-content-between', features: ['info'] },
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
                    responsive: true
                });
            }
            
            // Initialize first table
            initProductsTable();
            
            // Tab switching
            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                const target = $(e.target).attr('data-bs-target');
                
                if (target === '#products-tab') {
                    initProductsTable();
                } else if (target === '#warehouse-tab') {
                    initWarehouseTable();
                }
            });
            
            // Button click events - Products Tab
            $('#btn-add-products').on('click', function() {
                window.location.href = "{{ route('master_data.product.add') }}";
            });
            
            $('#btn-export-products').on('click', function() {
                window.location.href = "{{ route('master_data.product.export') }}?type=products";
            });
            
            $('#btn-export-bundle').on('click', function() {
                window.location.href = "{{ route('master_data.product.export') }}?type=bundle";
            });
            
            // Button click events - Warehouse Tab
            $('#btn-add-warehouse').on('click', function() {
                window.location.href = "{{ route('master_data.storage.add') }}";
            });
            
            $('#btn-export-warehouse').on('click', function() {
                window.location.href = "{{ route('master_data.storage.export') }}";
            });
            
            // Import button events
            $('#btn-import-products, #btn-import-update, #btn-import-bundle').on('click', function() {
                window.location.href = "{{ route('master_data.product.import') }}";
            });
            
            $('#btn-import-warehouse').on('click', function() {
                window.location.href = "{{ route('master_data.storage.import') }}";
            });
            
            // Search functionality
            $('#products-search').on('keyup', function() {
                if (productsTable) {
                    productsTable.search(this.value).draw();
                }
            });
            
            $('#warehouse-search').on('keyup', function() {
                if (warehouseTable) {
                    warehouseTable.search(this.value).draw();
                }
            });
            
            // Filter functionality
            $('#products-category-filter, #products-stock-filter').on('change', function() {
                if (productsTable) {
                    productsTable.draw();
                }
            });
            
            $('#warehouse-status-filter, #warehouse-location-filter').on('change', function() {
                if (warehouseTable) {
                    warehouseTable.draw();
                }
            });
        });
    </script>
@endsection
