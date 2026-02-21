@extends('layouts.default')
@section('page_title')
    Produk - Barang & Jasa
@stop
@section('content')
    <!-- Summary Cards -->
    {{-- <div class="row">
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
    </div> --}}

    <!-- Main Content Card -->
    <div class="mt-6">
        <div class="card">
            <h4 class="card-header">Kelola Produk</h4>
            <div class="card-body">
                <!-- Advanced Search Form -->
                <form class="dt_adv_search" method="GET" autocomplete="off">
                    <div class="row">
                        <div class="col-12">
                            <div class="row g-3">
                                <div class="col-12 col-sm-6 col-lg-4">
                                    <label class="form-label">Kategori:</label>
                                    <select class="global-select2 form-select" id="products-category-filter"
                                        data-allow-clear="true">
                                        <option value="">Semua Kategori</option>
                                        @foreach ($productCategories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-sm-6 col-lg-4">
                                    <label class="form-label">Status Stok:</label>
                                    <select class="global-select2 form-select" id="products-stock-filter"
                                        data-allow-clear="true">
                                        <option value="">Semua Status</option>
                                        @foreach ($stockStatuses as $status)
                                            <option value="{{ $status->code }}">{{ $status->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-sm-6 col-lg-4">
                                    <label class="form-label">Pencarian:</label>
                                    <input type="text" class="form-control" id="products-search"
                                        placeholder="Cari Produk">
                                </div>
                                <div class="col-12 col-sm-6 col-lg-4 d-flex align-items-end">
                                    <button type="button" class="btn btn-outline-secondary" id="reset-filter">
                                        <i class="icon-base ti tabler-filter-off me-1"></i> Reset Filter
                                    </button>
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
                                <input class="form-check-input" type="checkbox" id="toggleTagColumn">
                                <label class="form-check-label" for="toggleTagColumn">
                                    <i class="icon-base ti tabler-tag me-1"></i>Tampilkan Kolom Tag
                                </label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            {{-- <div class="dropdown">
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" id="btn-import-products">Produk Baru</a>
                                    </li>
                                    <li><a class="dropdown-item" href="#" id="btn-import-update">Update Produk</a>
                                    </li>
                                    <li><a class="dropdown-item" href="#" id="btn-import-bundle">Produk Bundle</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="dropdown">
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" id="btn-export-products">Semua Produk</a>
                                    </li>
                                    <li><a class="dropdown-item" href="#" id="btn-export-bundle">Produk Bundle &
                                            Komposisi</a></li>
                                </ul>
                            </div> --}}
                            @can('product.create')
                                <button type="button" id="btn-add-products"
                                    class="btn btn-md btn-primary waves-effect waves-light"><span
                                        class="icon-xs icon-base ti tabler-plus me-2"></span> Tambah Data</button>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
            <!-- Data Table -->
            <div class="card-datatable">
                <table id="products-table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Kode Produk</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>Total Stok</th>
                            <th>Qty Tersedia</th>
                            <th>Batas Minimum</th>
                            <th>Unit</th>
                            <th>Harga Rata-Rata</th>
                            <th>Harga Beli Terakhir</th>
                            <th>Harga Beli</th>
                            <th>Harga Jual</th>
                            <th>Status</th>
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
            // Permission variables
            const canRead = @json(auth()->user()->can('product.view'));

            let productsTable;

            productsTable = new DataTable('#products-table', {
                processing: true,
                serverSide: true,
                ajax: {
                    url: `{{ route('master_data.product.index_data_table') }}`,
                    data: function(d) {
                        d.category_filter = $('#products-category-filter').val();
                        d.stock_status_filter = $('#products-stock-filter').val();
                        d.search = $('#products-search').val();
                        return d;
                    }
                },
                columns: [{
                        data: 'code',
                        responsivePriority: 1,
                        render: function(data, type, row) {
                            if (!data) return '-';
                            if (canRead) {
                                const detailUrl = `{{ route('master_data.product.detail', ':id') }}`
                                    .replace(':id', row.id);
                                return `<a href="${detailUrl}" class="text-primary fw-semibold">${data}</a>`;
                            }
                            return data;
                        }
                    },
                    {
                        data: 'name',
                        responsivePriority: 3
                    },
                    {
                        data: 'category_name',
                        responsivePriority: 4
                    },
                    {
                        data: 'total_stock',
                        className: 'text-end',
                        responsivePriority: 5,
                        render: function(data, type, row) {
                            if (type === 'display' && data != null && !isNaN(data)) {
                                return parseFloat(data).toLocaleString('id-ID');
                            }
                            return data ?? '0';
                        }
                    },
                    {
                        data: 'qty_available',
                        className: 'text-end',
                        responsivePriority: 6,
                        render: function(data, type, row) {
                            if (type === 'display' && data != null && !isNaN(data)) {
                                return parseFloat(data).toLocaleString('id-ID');
                            }
                            return data ?? '0';
                        }
                    },
                    {
                        data: 'minimum_stock',
                        className: 'text-end',
                        responsivePriority: 7,
                        render: function(data, type, row) {
                            if (type === 'display' && data != null && !isNaN(data)) {
                                return parseFloat(data).toLocaleString('id-ID');
                            }
                            return data ?? '0';
                        }
                    },
                    {
                        data: 'unit_name',
                        responsivePriority: 8
                    },
                    {
                        data: 'average_price',
                        className: 'text-end',
                        responsivePriority: 9,
                        render: function(data, type, row) {
                            if (type === 'display' && data != null && !isNaN(data)) {
                                return 'Rp ' + parseFloat(data).toLocaleString('id-ID');
                            }
                            return data ?? 'Rp 0';
                        }
                    },
                    {
                        //TO DO
                        data: 'unit_purchase_price',
                        className: 'text-end',
                        responsivePriority: 10,
                        render: function(data, type, row) {
                            if (type === 'display' && data != null && !isNaN(data)) {
                                return 'Rp ' + parseFloat(data).toLocaleString('id-ID');
                            }
                            return data ?? 'Rp 0';
                        }
                    },
                    {
                        data: 'unit_purchase_price',
                        className: 'text-end',
                        responsivePriority: 11,
                        render: function(data, type, row) {
                            if (type === 'display' && data != null && !isNaN(data)) {
                                return 'Rp ' + parseFloat(data).toLocaleString('id-ID');
                            }
                            return data ?? 'Rp 0';
                        }
                    },
                    {
                        data: 'unit_sell_price',
                        className: 'text-end',
                        responsivePriority: 12,
                        render: function(data, type, row) {
                            if (type === 'display' && data != null && !isNaN(data)) {
                                return 'Rp ' + parseFloat(data).toLocaleString('id-ID');
                            }
                            return data ?? 'Rp 0';
                        }
                    },
                    {
                        data: 'status',
                        responsivePriority: 13,
                        render: function(data, type, row) {
                            const badgeClass = data === 'ACTIVE' ? 'bg-label-success' :
                                'bg-label-secondary';
                            const statusText = data === 'ACTIVE' ? 'ACTIVE' : 'INACTIVE';
                            return `<span class="badge ${badgeClass}">${statusText}</span>`;
                        }
                    },
                    {
                        data: 'tags',
                        responsivePriority: 14,
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

            // Button click events
            $('#btn-add-products').on('click', function() {
                window.location.href = "{{ route('master_data.product.add') }}";
            });

            // Tag column hidden by default
            productsTable.column(12).visible(false);

            // Tag Column Toggle Functionality
            $('#toggleTagColumn').on('change', function() {
                productsTable.column(12).visible(this.checked);
            });

            // Reset Filter
            $('#reset-filter').on('click', function() {
                $('#products-category-filter').val('').trigger('change');
                $('#products-stock-filter').val('').trigger('change');
                $('#products-search').val('');
                if (productsTable) {
                    productsTable.draw();
                }
            });

            let searchTimeout;
            $('#products-stock-filter, #products-category-filter, #products-search').on('keyup change',
                function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(function() {
                        if (productsTable) {
                            productsTable.draw();
                        }
                    }, 400); // 400ms delay
                });
        });
    </script>
@endsection
