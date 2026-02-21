@extends('layouts.default')

@section('page_title')
    Aset & Inventaris
@stop

@section('content')
    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md col-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="badge rounded bg-label-primary p-2 me-3">
                            <i class="icon-base ti tabler-building fs-5"></i>
                        </div>
                        <div class="card-info">
                            <p class="mb-0 text-nowrap">Total Aset</p>
                            <h5 class="mb-0" id="summary-total-aset">{{ $summaryData['total_aset'] }} Aset</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md col-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="badge rounded bg-label-success p-2 me-3">
                            <i class="icon-base ti tabler-currency-dollar fs-5"></i>
                        </div>
                        <div class="card-info">
                            <p class="mb-0 text-nowrap">Nilai Perolehan</p>
                            <h5 class="mb-0" id="summary-nilai-perolehan">Rp {{ number_format($summaryData['nilai_perolehan'], 0, ',', '.') }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md col-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="badge rounded bg-label-warning p-2 me-3">
                            <i class="icon-base ti tabler-chart-line fs-5"></i>
                        </div>
                        <div class="card-info">
                            <p class="mb-0 text-nowrap">Akumulasi Penyusutan</p>
                            <h5 class="mb-0" id="summary-akumulasi-penyusutan">Rp {{ number_format($summaryData['akumulasi_penyusutan'], 0, ',', '.') }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md col-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="badge rounded bg-label-info p-2 me-3">
                            <i class="icon-base ti tabler-book fs-5"></i>
                        </div>
                        <div class="card-info">
                            <p class="mb-0 text-nowrap">Nilai Buku Saat Ini</p>
                            <h5 class="mb-0" id="summary-nilai-buku">Rp {{ number_format($summaryData['nilai_buku_saat_ini'], 0, ',', '.') }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md col-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="badge rounded bg-label-danger p-2 me-3">
                            <i class="icon-base ti tabler-calendar fs-5"></i>
                        </div>
                        <div class="card-info">
                            <p class="mb-0 text-nowrap">Pelunasan 30 Hari Terakhir</p>
                            <h5 class="mb-0" id="summary-pelunasan">Rp {{ number_format($summaryData['pelunasan_30_hari'], 0, ',', '.') }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabbed Content -->
    <div class="mt-4">
        <div class="card">
            <div class="card-body">
                <!-- Tab Navigation -->
                <div class="list-group list-group-horizontal-md text-md-center" role="tablist">
                    <a class="list-group-item list-group-item-action active waves-effect"
                       id="pending-assets-item" data-bs-toggle="list"
                       href="#pending-assets-tab" role="tab" aria-selected="true">
                        <i class="icon-base ti tabler-clock me-2"></i>
                        <b>Aset Tunda</b>
                    </a>
                    <a class="list-group-item list-group-item-action waves-effect"
                       id="active-assets-item" data-bs-toggle="list"
                       href="#active-assets-tab" role="tab" aria-selected="false">
                        <i class="icon-base ti tabler-check me-2"></i>
                        <b>Aset Aktif</b>
                    </a>
                    <a class="list-group-item list-group-item-action waves-effect"
                       id="disposed-item" data-bs-toggle="list"
                       href="#disposed-tab" role="tab" aria-selected="false">
                        <i class="icon-base ti tabler-trash me-2"></i>
                        <b>Dijual/Dilepas</b>
                    </a>
                    <a class="list-group-item list-group-item-action waves-effect"
                       id="depreciation-item" data-bs-toggle="list"
                       href="#depreciation-tab" role="tab" aria-selected="false">
                        <i class="icon-base ti tabler-chart-line me-2"></i>
                        <b>Penyusutan</b>
                    </a>
                </div>
            </div>

            <!-- Tab Content -->
            <div class="tab-content mt-3 px-0">
                <!-- Tab 1: Aset Tunda (Pending Assets) -->
                <div class="tab-pane fade show active" id="pending-assets-tab" role="tabpanel">
                    <div class="card-body">
                        <h5 class="mb-4">Daftar Aset Tunda</h5>

                        <!-- Filter Section -->
                        <div class="row g-3 mb-4">
                            <div class="col-12 col-sm-6 col-lg-4">
                                <label class="form-label">Kategori:</label>
                                <select class="global-select2 form-select category-filter" data-allow-clear="true">
                                    <option value="">Semua Kategori</option>
                                    <option value="KENDARAAN">Kendaraan</option>
                                    <option value="BANGUNAN">Bangunan</option>
                                    <option value="ELEKTRONIK">Elektronik</option>
                                    <option value="FURNITURE">Furniture</option>
                                    <option value="ASSET">Asset</option>
                                    <option value="LAINNYA">Lainnya</option>
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <label class="form-label">Pencarian:</label>
                                <input type="text" class="form-control search-filter" placeholder="Cari Aset/Inventaris">
                            </div>
                        </div>

                        <hr class="my-6 mx-n6" />

                        <!-- Action Buttons -->
                        <div class="mt-4 mb-4">
                            <div class="d-flex justify-content-end">
                                <div class="d-flex gap-2">
                                    @can('inventory.create')
                                    <a href="{{ route('master_data.inventory.add') }}" class="btn btn-md btn-primary waves-effect waves-light">
                                        <span class="icon-xs icon-base ti tabler-shopping-cart me-2"></span> Pembelian Aset
                                    </a>
                                    @endcan
                                    @can('inventory.dispose')
                                    <a href="{{ route('master_data.inventory.dispose') }}" class="btn btn-md btn-danger waves-effect waves-light">
                                        <span class="icon-xs icon-base ti tabler-trash me-2"></span> Pelepasan Aset
                                    </a>
                                    @endcan
                                    @can('inventory.payment')
                                    <button type="button" class="btn btn-md btn-success waves-effect waves-light">
                                        <span class="icon-xs icon-base ti tabler-cash me-2"></span> Pembayaran Aset
                                    </button>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- DataTable -->
                    <div class="card-datatable">
                        <table id="pending-assets-table" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 120px;">Tanggal</th>
                                    <th>Nama Aset</th>
                                    <th style="width: 150px;">Jenis/Kategori</th>
                                    <th style="width: 120px;">Tanggal Mulai Tunda</th>
                                    <th style="width: 150px;">Sisa Tagihan</th>
                                    <th style="width: 150px;">Harga Aset</th>
                                    <th style="width: 100px;">Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                <!-- Tab 2: Aset Aktif (Active Assets) -->
                <div class="tab-pane fade" id="active-assets-tab" role="tabpanel">
                    <div class="card-body">
                        <h5 class="mb-4">Daftar Aset Aktif</h5>

                        <!-- Filter Section -->
                        <div class="row g-3 mb-4">
                            <div class="col-12 col-sm-6 col-lg-4">
                                <label class="form-label">Kategori:</label>
                                <select class="global-select2 form-select category-filter" data-allow-clear="true">
                                    <option value="">Semua Kategori</option>
                                    <option value="KENDARAAN">Kendaraan</option>
                                    <option value="BANGUNAN">Bangunan</option>
                                    <option value="ELEKTRONIK">Elektronik</option>
                                    <option value="FURNITURE">Furniture</option>
                                    <option value="ASSET">Asset</option>
                                    <option value="LAINNYA">Lainnya</option>
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <label class="form-label">Pencarian:</label>
                                <input type="text" class="form-control search-filter" placeholder="Cari Aset/Inventaris">
                            </div>
                        </div>

                        <hr class="my-6 mx-n6" />

                        <!-- Action Buttons -->
                        <div class="mt-4 mb-4">
                            <div class="d-flex justify-content-end">
                                <div class="d-flex gap-2">
                                    @can('inventory.create')
                                    <a href="{{ route('master_data.inventory.add') }}" class="btn btn-md btn-primary waves-effect waves-light">
                                        <span class="icon-xs icon-base ti tabler-shopping-cart me-2"></span> Pembelian Aset
                                    </a>
                                    @endcan
                                    @can('inventory.dispose')
                                    <button type="button" class="btn btn-md btn-danger waves-effect waves-light">
                                        <span class="icon-xs icon-base ti tabler-trash me-2"></span> Pelepasan Aset
                                    </button>
                                    @endcan
                                    @can('inventory.payment')
                                    <button type="button" class="btn btn-md btn-success waves-effect waves-light">
                                        <span class="icon-xs icon-base ti tabler-cash me-2"></span> Pembayaran Aset
                                    </button>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- DataTable -->
                    <div class="card-datatable">
                        <table id="active-assets-table" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 120px;">Tanggal</th>
                                    <th>Nama Aset</th>
                                    <th style="width: 140px;">Kode Aset</th>
                                    <th style="width: 150px;">Jenis Aset</th>
                                    <th style="width: 150px;">Harga Aset</th>
                                    <th style="width: 100px;">Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                <!-- Tab 3: Dijual/Dilepas (Disposed Assets) -->
                <div class="tab-pane fade" id="disposed-tab" role="tabpanel">
                    <div class="card-body">
                        <h5 class="mb-4">Daftar Aset Dijual/Dilepas</h5>

                        <!-- Filter Section -->
                        <div class="row g-3 mb-4">
                            <div class="col-12 col-sm-6 col-lg-4">
                                <label class="form-label">Kategori:</label>
                                <select class="global-select2 form-select category-filter" data-allow-clear="true">
                                    <option value="">Semua Kategori</option>
                                    <option value="KENDARAAN">Kendaraan</option>
                                    <option value="BANGUNAN">Bangunan</option>
                                    <option value="ELEKTRONIK">Elektronik</option>
                                    <option value="FURNITURE">Furniture</option>
                                    <option value="ASSET">Asset</option>
                                    <option value="LAINNYA">Lainnya</option>
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <label class="form-label">Pencarian:</label>
                                <input type="text" class="form-control search-filter" placeholder="Cari Aset/Inventaris">
                            </div>
                        </div>

                        <hr class="my-6 mx-n6" />

                        <!-- Action Buttons -->
                        <div class="mt-4 mb-4">
                            <div class="d-flex justify-content-end">
                                <div class="d-flex gap-2">
                                    @can('inventory.create')
                                    <a href="{{ route('master_data.inventory.add') }}" class="btn btn-md btn-primary waves-effect waves-light">
                                        <span class="icon-xs icon-base ti tabler-shopping-cart me-2"></span> Pembelian Aset
                                    </a>
                                    @endcan
                                    @can('inventory.dispose')
                                    <button type="button" class="btn btn-md btn-danger waves-effect waves-light">
                                        <span class="icon-xs icon-base ti tabler-trash me-2"></span> Pelepasan Aset
                                    </button>
                                    @endcan
                                    @can('inventory.payment')
                                    <button type="button" class="btn btn-md btn-success waves-effect waves-light">
                                        <span class="icon-xs icon-base ti tabler-cash me-2"></span> Pembayaran Aset
                                    </button>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- DataTable -->
                    <div class="card-datatable">
                        <table id="disposed-table" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 120px;">Tanggal</th>
                                    <th>Nama Aset</th>
                                    <th style="width: 120px;">Kode Aset</th>
                                    <th style="width: 140px;">Metode Transaksi</th>
                                    <th style="width: 150px;">Harga Aset</th>
                                    <th style="width: 100px;">Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                <!-- Tab 4: Penyusutan (Depreciation) -->
                <div class="tab-pane fade" id="depreciation-tab" role="tabpanel">
                    <div class="card-body">
                        <h5 class="mb-4">Daftar Penyusutan</h5>

                        <!-- Filter Bar -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label class="form-label">Periode Bulan:</label>
                                <select class="global-select2 form-select" id="depreciation-month" data-allow-clear="false">
                                    <option value="1">Januari</option>
                                    <option value="2">Februari</option>
                                    <option value="3">Maret</option>
                                    <option value="4">April</option>
                                    <option value="5">Mei</option>
                                    <option value="6">Juni</option>
                                    <option value="7">Juli</option>
                                    <option value="8">Agustus</option>
                                    <option value="9">September</option>
                                    <option value="10">Oktober</option>
                                    <option value="11" selected>November</option>
                                    <option value="12">Desember</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tahun:</label>
                                <select class="global-select2 form-select" id="depreciation-year" data-allow-clear="false">
                                    <option value="2024">2024</option>
                                    <option value="2025" selected>2025</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Kategori:</label>
                                <select class="global-select2 form-select category-filter" data-allow-clear="true">
                                    <option value="">Semua Kategori</option>
                                    <option value="KENDARAAN">Kendaraan</option>
                                    <option value="BANGUNAN">Bangunan</option>
                                    <option value="ELEKTRONIK">Elektronik</option>
                                    <option value="FURNITURE">Furniture</option>
                                    <option value="ASSET">Asset</option>
                                    <option value="LAINNYA">Lainnya</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Pencarian:</label>
                                <input type="text" class="form-control search-filter" placeholder="Cari Aset">
                            </div>
                        </div>

                        <hr class="my-6 mx-n6" />

                        <!-- Action Buttons -->
                        <div class="mt-4 mb-4">
                            <div class="d-flex justify-content-end">
                                <div class="d-flex gap-2">
                                    @can('inventory.depreciation')
                                    <a href="{{ route('master_data.inventory.depreciation') }}"  class="btn btn-md btn-success waves-effect waves-light">
                                        <span class="icon-xs icon-base ti tabler-calculator me-2"></span> Penyusutan Bulanan
                                    </a>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- DataTable -->
                    <div class="card-datatable">
                        <table id="depreciation-table" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 120px;">Tanggal</th>
                                    <th>Nama Aset</th>
                                    <th style="width: 180px;">Periode</th>
                                    <th style="width: 140px;">Nilai</th>
                                    <th style="width: 140px;">Metode</th>
                                    <th style="width: 150px;">Akumulasi Penyusutan</th>
                                    <th style="width: 100px;">Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js_script')
<script>
$(function() {
    let pendingAssetsTable, activeAssetsTable, disposedTable, depreciationTable;
    let currentTab = 'pending_assets';

    // Helper function to format numbers
    function formatNumber(num) {
        return parseFloat(num || 0).toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0});
    }

    // Helper function to update summary cards
    function updateSummaryCards(summary) {
        $('#summary-total-aset').text(summary.total_aset + ' Aset');
        $('#summary-nilai-perolehan').text('Rp ' + formatNumber(summary.nilai_perolehan));
        $('#summary-akumulasi-penyusutan').text('Rp ' + formatNumber(summary.akumulasi_penyusutan));
        $('#summary-nilai-buku').text('Rp ' + formatNumber(summary.nilai_buku_saat_ini));
        $('#summary-pelunasan').text('Rp ' + formatNumber(summary.pelunasan_30_hari));
    }

    // Tab 1: Aset Tunda (Pending Assets)
    function initPendingAssetsTable() {
        if (pendingAssetsTable) {
            pendingAssetsTable.destroy();
        }

        const categoryFilter = $('#pending-assets-tab .category-filter').val();
        const searchFilter = $('#pending-assets-tab .search-filter').val();

        pendingAssetsTable = new DataTable('#pending-assets-table', {
            processing: true,
            ajax: {
                url: '{{ route("master_data.inventory.index_data_table") }}',
                data: {
                    tab: 'pending_assets',
                    category_filter: categoryFilter,
                    search: searchFilter
                },
                dataSrc: function(json) {
                    if (json.summary) {
                        updateSummaryCards(json.summary);
                    }
                    return json.data;
                }
            },
            columns: [
                {
                    data: 'acquisition_date',
                    width: '120px',
                    render: function(data) {
                        return data ? new Date(data).toLocaleDateString('id-ID') : '-';
                    }
                },
                { data: 'name' },
                { data: 'category', width: '150px' },
                {
                    data: 'created_at',
                    width: '120px',
                    render: function(data) {
                        return data ? new Date(data).toLocaleDateString('id-ID') : '-';
                    }
                },
                {
                    data: null,
                    width: '150px',
                    className: 'text-end',
                    render: function() {
                        return '<span class="text-warning">Rp 0</span>';
                    }
                },
                {
                    data: 'acquisition_value',
                    width: '150px',
                    className: 'text-end',
                    render: function(data) {
                        return 'Rp ' + formatNumber(data);
                    }
                },
                {
                    data: null,
                    width: '100px',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        const detailUrl = "{{ route('master_data.inventory.detail', ':id') }}".replace(':id', row.id);
                        return `
                            <a href="${detailUrl}" class="btn btn-sm btn-primary waves-effect waves-light" title="View">
                                <i class="icon-base ti tabler-eye"></i>
                            </a>
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
            responsive: true,
            orderCellsTop: true,
            ordering: false
        });
    }

    // Tab 2: Aset Aktif (Active Assets)
    function initActiveAssetsTable() {
        if (activeAssetsTable) {
            activeAssetsTable.destroy();
        }

        const categoryFilter = $('#active-assets-tab .category-filter').val();
        const searchFilter = $('#active-assets-tab .search-filter').val();

        activeAssetsTable = new DataTable('#active-assets-table', {
            processing: true,
            ajax: {
                url: '{{ route("master_data.inventory.index_data_table") }}',
                data: {
                    tab: 'active_assets',
                    category_filter: categoryFilter,
                    search: searchFilter
                },
                dataSrc: function(json) {
                    if (json.summary) {
                        updateSummaryCards(json.summary);
                    }
                    return json.data;
                }
            },
            columns: [
                {
                    data: 'acquisition_date',
                    width: '120px',
                    render: function(data) {
                        return data ? new Date(data).toLocaleDateString('id-ID') : '-';
                    }
                },
                { data: 'name' },
                { data: 'code', width: '140px' },
                { data: 'category', width: '150px' },
                {
                    data: 'acquisition_value',
                    width: '150px',
                    className: 'text-end',
                    render: function(data) {
                        return 'Rp ' + formatNumber(data);
                    }
                },
                {
                    data: null,
                    width: '100px',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        const detailUrl = "{{ route('master_data.inventory.detail', ':id') }}".replace(':id', row.id);
                        return `
                            <a href="${detailUrl}" class="btn btn-sm btn-primary waves-effect waves-light" title="View">
                                <i class="icon-base ti tabler-eye"></i>
                            </a>
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
            responsive: true,
            orderCellsTop: true,
            ordering: false
        });
    }

    // Tab 3: Disposed Assets
    function initDisposedTable() {
        if (disposedTable) {
            disposedTable.destroy();
        }

        const categoryFilter = $('#disposed-tab .category-filter').val();
        const searchFilter = $('#disposed-tab .search-filter').val();

        disposedTable = new DataTable('#disposed-table', {
            processing: true,
            ajax: {
                url: '{{ route("master_data.inventory.index_data_table") }}',
                data: {
                    tab: 'disposed',
                    category_filter: categoryFilter,
                    search: searchFilter
                },
                dataSrc: function(json) {
                    if (json.summary) {
                        updateSummaryCards(json.summary);
                    }
                    return json.data;
                }
            },
            columns: [
                {
                    data: 'disposal_date',
                    width: '120px',
                    render: function(data, type, row) {
                        return data ? new Date(data).toLocaleDateString('id-ID') : (row.acquisition_date ? new Date(row.acquisition_date).toLocaleDateString('id-ID') : '-');
                    }
                },
                { data: 'name' },
                { data: 'code', width: '120px' },
                {
                    data: null,
                    width: '140px',
                    render: function() {
                        return 'sewa/insiden';
                    }
                },
                {
                    data: 'acquisition_value',
                    width: '150px',
                    className: 'text-end',
                    render: function(data) {
                        return 'Rp ' + formatNumber(data);
                    }
                },
                {
                    data: null,
                    width: '100px',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        const detailUrl = "{{ route('master_data.inventory.detail', ':id') }}".replace(':id', row.id);
                        return `
                            <a href="${detailUrl}" class="btn btn-sm btn-info waves-effect waves-light" title="View">
                                <i class="icon-base ti tabler-eye"></i>
                            </a>
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
            responsive: true,
            orderCellsTop: true,
            ordering: false
        });
    }

    // Tab 4: Depreciation
    function initDepreciationTable() {
        if (depreciationTable) {
            depreciationTable.destroy();
        }

        const categoryFilter = $('#depreciation-tab .category-filter').val();
        const searchFilter = $('#depreciation-tab .search-filter').val();

        depreciationTable = new DataTable('#depreciation-table', {
            processing: true,
            ajax: {
                url: '{{ route("master_data.inventory.index_data_table") }}',
                data: {
                    tab: 'depreciation',
                    category_filter: categoryFilter,
                    search: searchFilter
                },
                dataSrc: function(json) {
                    if (json.summary) {
                        updateSummaryCards(json.summary);
                    }
                    return json.data;
                }
            },
            columns: [
                {
                    data: 'created_at',
                    width: '120px',
                    render: function(data) {
                        return data ? new Date(data).toLocaleDateString('id-ID') : '-';
                    }
                },
                { data: 'name' },
                {
                    data: null,
                    width: '180px',
                    render: function() {
                        return '01/11/2025 - 30/11/2025';
                    }
                },
                {
                    data: 'acquisition_value',
                    width: '140px',
                    className: 'text-end',
                    render: function(data) {
                        return 'Rp ' + formatNumber(data);
                    }
                },
                {
                    data: 'depreciation_method',
                    width: '140px',
                    render: function(data, type, row) {
                        const rate = row.depreciation_rate || 0;
                        const methodName = data === 'STRAIGHT_LINE' ? 'Straight Line' :
                                         data === 'REDUCING_BALANCE' ? 'Reducing Balance' : data;
                        return rate + '% ' + methodName;
                    }
                },
                {
                    data: 'accumulated_depreciation',
                    width: '150px',
                    className: 'text-end',
                    render: function(data) {
                        return 'Rp ' + formatNumber(data);
                    }
                },
                {
                    data: null,
                    width: '100px',
                    orderable: false,
                    searchable: false,
                    render: function() {
                        return `
                            <button type="button" class="btn btn-sm btn-warning waves-effect waves-light" title="Process Depreciation">
                                <i class="icon-base ti tabler-calculator"></i>
                            </button>
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
            responsive: true,
            orderCellsTop: true,
            ordering: false
        });
    }

    // Initialize first table
    initPendingAssetsTable();

    // Tab switching
    $('a[data-bs-toggle="list"]').on('shown.bs.tab', function (e) {
        const target = $(e.target).attr('href');

        if (target === '#pending-assets-tab') {
            currentTab = 'pending_assets';
            initPendingAssetsTable();
        } else if (target === '#active-assets-tab') {
            currentTab = 'active_assets';
            initActiveAssetsTable();
        } else if (target === '#disposed-tab') {
            currentTab = 'disposed';
            initDisposedTable();
        } else if (target === '#depreciation-tab') {
            currentTab = 'depreciation';
            initDepreciationTable();
        }
    });

    // Filter functionality with delay for each tab
    let searchTimeout;
    $('.category-filter, .search-filter').on('keyup change', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            if (currentTab === 'pending_assets' && pendingAssetsTable) {
                initPendingAssetsTable();
            } else if (currentTab === 'active_assets' && activeAssetsTable) {
                initActiveAssetsTable();
            } else if (currentTab === 'disposed' && disposedTable) {
                initDisposedTable();
            } else if (currentTab === 'depreciation' && depreciationTable) {
                initDepreciationTable();
            }
        }, 400);
    });
});
</script>
@endsection
