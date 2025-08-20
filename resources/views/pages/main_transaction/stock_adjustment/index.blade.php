@extends('layouts.default')
@section('page_title')
    List Stock Adjustment
@stop
@section('content')
    <!-- Summary Cards -->
    {{-- <div class="row">
        <div class="col-lg-4 col-sm-6">
            <div class="card card-border-shadow-warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="icon-base ti tabler-clock icon-28px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0" id="waiting-adjustment-count">3</h4>
                    </div>
                    <p class="mb-1">Butuh Penyesuaian Stok</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6">
            <div class="card card-border-shadow-info h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="icon-base ti tabler-hourglass icon-28px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0" id="waiting-approval-count">2</h4>
                    </div>
                    <p class="mb-1">Menunggu Persetujuan</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6">
            <div class="card card-border-shadow-success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="icon-base ti tabler-check icon-28px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0" id="finished-count">198</h4>
                    </div>
                    <p class="mb-1">Selesai</p>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Main Content Card with Tabs -->
    <div class="mt-6">
        <div class="card">
            <h4 class="card-header">Kelola Stock Adjustment</h4>
            <div class="card-body pb-0">
                <!-- Tab Navigation Inside Card -->
                <div class="list-group list-group-horizontal-md text-md-center" role="tablist">
                    <a class="list-group-item list-group-item-action active" data-bs-toggle="list"
                        href="#waiting-adjustment-tab" role="tab" aria-controls="waiting-adjustment-tab"
                        aria-selected="true">
                        <i class="icon-base ti tabler-clock me-2"></i>
                        <b>Butuh Penyesuaian Stok</b>
                    </a>
                    <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#waiting-approval-tab"
                        role="tab" aria-controls="waiting-approval-tab" aria-selected="false">
                        <i class="icon-base ti tabler-hourglass me-2"></i>
                        <b>Menunggu Persetujuan</b>
                    </a>
                    <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#finished-tab"
                        role="tab" aria-controls="finished-tab" aria-selected="false">
                        <i class="icon-base ti tabler-check me-2"></i>
                        <b>Selesai</b>
                    </a>
                </div>
            </div>

            <!-- Tab Content Inside Card -->
            <div class="tab-content pt-0">
                <!-- Waiting Adjustment Tab -->
                <div class="tab-pane fade show active mx-n6" id="waiting-adjustment-tab" role="tabpanel">
                    <div class="card-body">
                        <!-- Advanced Search Form -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Filter Tipe:</label>
                                <select class="global-select2 form-select" id="waiting-type-filter" data-allow-clear="true">
                                    <option value="">Semua Tipe</option>
                                    @foreach ($adjustmentTypes as $type)
                                        <option value="{{ $type['value'] }}">{{ $type['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pencarian:</label>
                                <input type="text" class="form-control" id="waiting-search-field"
                                    placeholder="Cari Stock Adjustment">
                            </div>
                        </div>

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
                                    @if ($canAddStockAdjustment)
                                        <a href="{{ route('main_transaction.stock_adjustment.add') }}"
                                            class="btn btn-md btn-primary waves-effect waves-light"><span
                                                class="icon-xs icon-base ti tabler-plus me-2"></span> Tambah Data</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Table -->
                    <div class="card-datatable">
                        <table id="waiting-adjustment-table" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Tanggal Dibuat</th>
                                    <th>Nomor Referensi</th>
                                    <th>Tipe</th>
                                    <th>Gudang</th>
                                    <th>Tag</th>
                                    <th>Total Item</th>
                                    <th>Total Qty</th>
                                    <th>PIC</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                <!-- Waiting Approval Tab -->
                <div class="tab-pane fade mx-n6" id="waiting-approval-tab" role="tabpanel">
                    <div class="card-body">
                        <!-- Advanced Search Form -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Filter Tipe:</label>
                                <select class="global-select2 form-select" id="approval-type-filter"
                                    data-allow-clear="true">
                                    <option value="">Semua Tipe</option>
                                    @foreach ($adjustmentTypes as $type)
                                        <option value="{{ $type['value'] }}">{{ $type['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pencarian:</label>
                                <input type="text" class="form-control" id="approval-search-field"
                                    placeholder="Cari Stock Adjustment">
                            </div>
                        </div>

                        <hr class="my-6 mx-n6" />

                        <!-- Action Buttons -->
                        <div class="mt-4">
                            <div class="d-flex justify-content-between">
                                <!-- Column Toggle -->
                                <div class="d-flex align-items-center">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="toggleTagColumnApproval"
                                            checked>
                                        <label class="form-check-label" for="toggleTagColumnApproval">
                                            <i class="icon-base ti tabler-tag me-1"></i>Tampilkan Kolom Tag
                                        </label>
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    @if ($canAddStockAdjustment)
                                        <a href="{{ route('main_transaction.stock_adjustment.add') }}"
                                            class="btn btn-md btn-primary waves-effect waves-light"><span
                                                class="icon-xs icon-base ti tabler-plus me-2"></span> Tambah Data</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Table -->
                    <div class="card-datatable">
                        <table id="waiting-approval-table" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Nomor Adjustment</th>
                                    <th>Tipe</th>
                                    <th>Gudang</th>
                                    <th>Tag</th>
                                    <th>Referensi</th>
                                    <th>Total Item</th>
                                    <th>Total Qty</th>
                                    <th>Total Nilai</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                <!-- Finished Tab -->
                <div class="tab-pane fade mx-n6" id="finished-tab" role="tabpanel">
                    <div class="card-body">
                        <!-- Advanced Search Form -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Filter Tipe:</label>
                                <select class="global-select2 form-select" id="finished-type-filter"
                                    data-allow-clear="true">
                                    <option value="">Semua Tipe</option>
                                    @foreach ($adjustmentTypes as $type)
                                        <option value="{{ $type['value'] }}">{{ $type['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pencarian:</label>
                                <input type="text" class="form-control" id="finished-search-field"
                                    placeholder="Cari Stock Adjustment">
                            </div>
                        </div>

                        <hr class="my-6 mx-n6" />

                        <!-- Action Buttons -->
                        <div class="mt-4">
                            <div class="d-flex justify-content-between">
                                <!-- Column Toggle -->
                                <div class="d-flex align-items-center">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="toggleTagColumnFinished"
                                            checked>
                                        <label class="form-check-label" for="toggleTagColumnFinished">
                                            <i class="icon-base ti tabler-tag me-1"></i>Tampilkan Kolom Tag
                                        </label>
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    @if ($canImportStockAdjustment)
                                        <a href="{{ route('main_transaction.stock_adjustment.import') }}"
                                            class="btn btn-md btn-primary waves-effect waves-light"><span
                                                class="icon-xs icon-base ti tabler-file-upload me-2"></span> Import</a>
                                    @endif
                                    @if ($canExportStockAdjustment)
                                        <a href="{{ route('main_transaction.stock_adjustment.export') }}"
                                            class="btn btn-md btn-primary waves-effect waves-light"><span
                                                class="icon-xs icon-base ti tabler-file-download me-2"></span> Export</a>
                                    @endif
                                    @if ($canAddStockAdjustment)
                                        <a href="{{ route('main_transaction.stock_adjustment.add') }}"
                                            class="btn btn-md btn-primary waves-effect waves-light"><span
                                                class="icon-xs icon-base ti tabler-plus me-2"></span> Tambah Data</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Table -->
                    <div class="card-datatable">
                        <table id="finished-table" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Nomor Adjustment</th>
                                    <th>Tipe</th>
                                    <th>Gudang</th>
                                    <th>Tag</th>
                                    <th>Referensi</th>
                                    <th>Total Item</th>
                                    <th>Total Qty</th>
                                    <th>Total Nilai</th>
                                    <th>PIC</th>
                                    <th>Aksi</th>
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
            // Note: global-select2 initialization is handled globally by layout

            /**
             * Stock Adjustment Tabs Explanation:
             * 1. Butuh Penyesuaian Stok (waiting-adjustment): Contains references from other transactions 
             *    (Stock Opname, Product Return, etc.) that require manual stock adjustment
             * 2. Menunggu Persetujuan (waiting-approval): Stock adjustments created but requiring approval process
             * 3. Selesai (finished): Completed stock adjustments (most adjustments go directly here)
             */

            let waitingAdjustmentTable, waitingApprovalTable, finishedTable;
            let currentActiveTab = 'waiting-adjustment';

            // Permission variables
            const canRead = @json($canViewStockAdjustment ?? true);
            const canEdit = @json($canEditStockAdjustment ?? true);

            // Initialize all DataTables for each tab
            function initWaitingAdjustmentTable() {
                if (waitingAdjustmentTable) {
                    waitingAdjustmentTable.destroy();
                }
                waitingAdjustmentTable = new DataTable('#waiting-adjustment-table', {
                    ajax: {
                        url: '{{ route('main_transaction.stock_adjustment.index_data_table') }}',
                        data: {
                            tab: 'waiting_adjustment',
                            type_filter: $('#waiting-type-filter').val() // Pass filter value
                        }
                    },
                    columns: [{
                            data: 'created_date',
                            responsivePriority: 2
                        },
                        {
                            data: 'reference_number',
                            responsivePriority: 3,
                            render: function(data, type, row, meta) {
                                if (!data) {
                                    return '-';
                                }

                                // Make reference clickable based on adjustment type
                                let detailUrl = '#';
                                switch (row.adjustment_type) {
                                    case 'STOCK_OPNAME':
                                        detailUrl =
                                            "{{ route('master_data.stock_opname.detail', ':ref') }}"
                                            .replace(':ref', data);
                                        break;
                                    case 'PRODUCT_RETURN':
                                        // Link to Product Return detail page (when implemented)
                                        break;
                                    default:
                                        return data;
                                }

                                return `<a href="${detailUrl}" class="text-primary fw-semibold" title="Lihat detail ${row.adjustment_type}">${data}</a>`;
                            }
                        },
                        {
                            data: 'adjustment_type',
                            responsivePriority: 4,
                            render: function(data, type, row, meta) {
                                const typeLabels = {
                                    'MANUAL_ADJUSTMENT': 'Penyesuaian Manual',
                                    'STOCK_OPNAME': 'Hasil Stock Opname',
                                    'PRODUCT_RETURN': 'Retur Produk',
                                    'DAMAGE_LOSS': 'Kerusakan/Kehilangan',
                                    'EXPIRY_DISPOSAL': 'Produk Kadaluarsa'
                                };
                                return typeLabels[data] || data;
                            }
                        },
                        {
                            data: 'warehouse_name',
                            responsivePriority: 5,
                            render: function(data, type, full, meta) {
                                const warehouseDetailUrl =
                                    "{{ route('master_data.warehouse.detail', ':id') }}".replace(
                                        ':id', full.warehouse_id);
                                return `<a href="${warehouseDetailUrl}" class="text-primary fw-semibold">${data}</a>`;
                            }
                        },
                        {
                            data: 'tags',
                            responsivePriority: 6,
                            render: function(data, type, row) {
                                if (!data || data.length === 0) return '-';
                                return data.map(tag => {
                                    const colorClass = `bg-label-${tag.color}`;
                                    return tag.code ?
                                        `<span class="badge ${colorClass} me-1">${tag.name}</span>` :
                                        '';
                                }).join('');
                            }
                        },
                        {
                            data: 'total_items',
                            responsivePriority: 7,
                            render: function(data, type, row, meta) {
                                return data.toLocaleString('id-ID');
                            }
                        },
                        {
                            data: 'total_adjustment_qty',
                            responsivePriority: 8,
                            render: function(data, type, row, meta) {
                                if (data > 0) {
                                    return `<span class="text-success">+${data.toLocaleString('id-ID')}</span>`;
                                } else if (data < 0) {
                                    return `<span class="text-danger">${data.toLocaleString('id-ID')}</span>`;
                                }
                                return `<span class="text-muted">${data}</span>`;
                            }
                        },
                        {
                            data: 'pic_name',
                            responsivePriority: 9
                        },
                        {
                            data: null,
                            orderable: false,
                            searchable: false,
                            responsivePriority: 1,
                            width: '120px',
                            render: function(data, type, row, meta) {
                                const editUrl =
                                    `{{ route('main_transaction.stock_adjustment.edit', ':id') }}`
                                    .replace(':id', row.id);
                                const detailUrl =
                                    `{{ route('main_transaction.stock_adjustment.detail', ':id') }}`
                                    .replace(':id', row.id);

                                let actions = '<div class="d-flex gap-2">';

                                if (canRead) {
                                    actions += `<a href="${detailUrl}" class="btn btn-sm btn-primary waves-effect waves-light" title="Detail">
                                <i class="icon-base ti tabler-eye"></i>
                            </a>`;
                                }

                                if (canEdit) {
                                    actions += `<a href="${editUrl}" class="btn btn-sm btn-primary waves-effect waves-light" title="Edit">
                                <i class="icon-base ti tabler-edit"></i>
                            </a>`;
                                }

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
                    scrollX: true,
                    responsive: true,
                    orderCellsTop: true,
                    ordering: false
                });
            }

            function initWaitingApprovalTable() {
                if (waitingApprovalTable) {
                    waitingApprovalTable.destroy();
                }

                waitingApprovalTable = new DataTable('#waiting-approval-table', {
                    ajax: {
                        url: '{{ route('main_transaction.stock_adjustment.index_data_table') }}',
                        data: {
                            tab: 'waiting_approval',
                            type_filter: $('#approval-type-filter').val() // Pass filter value
                        }
                    },
                    columns: [{
                            data: 'adjustment_date',
                            responsivePriority: 2
                        },
                        {
                            data: 'adjustment_number',
                            responsivePriority: 3,
                            render: function(data, type, row, meta) {
                                const detailUrl =
                                    `{{ route('main_transaction.stock_adjustment.detail', ':id') }}`
                                    .replace(':id', row.id);
                                return `<a href="${detailUrl}" class="text-primary fw-semibold">${data}</a>`;
                            }
                        },
                        {
                            data: 'adjustment_type',
                            responsivePriority: 4,
                            render: function(data, type, row, meta) {
                                const typeLabels = {
                                    'MANUAL_ADJUSTMENT': 'Penyesuaian Manual',
                                    'STOCK_OPNAME': 'Hasil Stock Opname',
                                    'PRODUCT_RETURN': 'Retur Produk',
                                    'DAMAGE_LOSS': 'Kerusakan/Kehilangan',
                                    'EXPIRY_DISPOSAL': 'Produk Kadaluarsa'
                                };
                                return typeLabels[data] || data;
                            }
                        },
                        {
                            data: 'warehouse_name',
                            responsivePriority: 5,
                            render: function(data, type, full, meta) {
                                const warehouseDetailUrl =
                                    "{{ route('master_data.warehouse.detail', ':id') }}".replace(
                                        ':id', full.warehouse_id);
                                return `<a href="${warehouseDetailUrl}" class="text-primary fw-semibold">${data}</a>`;
                            }
                        },
                        {
                            data: 'tags',
                            responsivePriority: 6,
                            render: function(data, type, row) {
                                if (!data || data.length === 0) return '-';
                                return data.map(tag => {
                                    const colorClass = `bg-label-${tag.color}`;
                                    return tag.code ?
                                        `<span class="badge ${colorClass} me-1">${tag.name}</span>` :
                                        '';
                                }).join('');
                            }
                        },
                        {
                            data: 'reference_number',
                            responsivePriority: 7,
                            render: function(data, type, row, meta) {
                                if (!data) {
                                    return '-';
                                }

                                let detailUrl = '#';
                                switch (row.adjustment_type) {
                                    case 'STOCK_OPNAME':
                                        detailUrl =
                                            "{{ route('master_data.stock_opname.detail', ':ref') }}"
                                            .replace(':ref', data);
                                        break;
                                    case 'PRODUCT_RETURN':
                                        break;
                                    default:
                                        return data;
                                }

                                return `<a href="${detailUrl}" class="text-primary fw-semibold" title="Lihat detail ${row.adjustment_type}">${data}</a>`;
                            }
                        },
                        {
                            data: 'total_items',
                            responsivePriority: 8,
                            render: function(data, type, row, meta) {
                                return data.toLocaleString('id-ID');
                            }
                        },
                        {
                            data: 'total_adjustment_qty',
                            responsivePriority: 9,
                            render: function(data, type, row, meta) {
                                if (data > 0) {
                                    return `<span class="text-success">+${data.toLocaleString('id-ID')}</span>`;
                                } else if (data < 0) {
                                    return `<span class="text-danger">${data.toLocaleString('id-ID')}</span>`;
                                }
                                return `<span class="text-muted">${data}</span>`;
                            }
                        },
                        {
                            data: 'total_adjustment_value',
                            responsivePriority: 10,
                            render: function(data, type, row, meta) {
                                const formatted = new Intl.NumberFormat('id-ID', {
                                    style: 'currency',
                                    currency: 'IDR',
                                    minimumFractionDigits: 0
                                }).format(Math.abs(data));

                                if (data > 0) {
                                    return `<span class="text-success">+${formatted}</span>`;
                                } else if (data < 0) {
                                    return `<span class="text-danger">-${formatted}</span>`;
                                }
                                return `<span class="text-muted">${formatted}</span>`;
                            }
                        },
                        {
                            data: null,
                            orderable: false,
                            searchable: false,
                            responsivePriority: 1,
                            width: '120px',
                            render: function(data, type, row, meta) {
                                const editUrl =
                                    `{{ route('main_transaction.stock_adjustment.edit', ':id') }}`
                                    .replace(':id', row.id);
                                const detailUrl =
                                    `{{ route('main_transaction.stock_adjustment.detail', ':id') }}`
                                    .replace(':id', row.id);

                                let actions = '<div class="d-flex gap-2">';

                                if (canRead) {
                                    actions += `<a href="${detailUrl}" class="btn btn-sm btn-primary waves-effect waves-light" title="Detail">
                                <i class="icon-base ti tabler-eye"></i>
                            </a>`;
                                }

                                if (canEdit) {
                                    actions += `<a href="${editUrl}" class="btn btn-sm btn-primary waves-effect waves-light" title="Edit">
                                <i class="icon-base ti tabler-edit"></i>
                            </a>`;
                                }

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
                    scrollX: true,
                    responsive: true,
                    orderCellsTop: true,
                    ordering: false
                });
            }

            function initFinishedTable() {
                if (finishedTable) {
                    finishedTable.destroy();
                }

                finishedTable = new DataTable('#finished-table', {
                    ajax: {
                        url: '{{ route('main_transaction.stock_adjustment.index_data_table') }}',
                        data: {
                            tab: 'finished',
                            type_filter: $('#finished-type-filter').val() // Pass filter value
                        }
                    },
                    columns: [{
                            data: 'adjustment_date',
                            responsivePriority: 2
                        },
                        {
                            data: 'adjustment_number',
                            responsivePriority: 3,
                            render: function(data, type, row, meta) {
                                const detailUrl =
                                    `{{ route('main_transaction.stock_adjustment.detail', ':id') }}`
                                    .replace(':id', row.id);
                                return `<a href="${detailUrl}" class="text-primary fw-semibold">${data}</a>`;
                            }
                        },
                        {
                            data: 'adjustment_type',
                            responsivePriority: 4,
                            render: function(data, type, row, meta) {
                                const typeLabels = {
                                    'MANUAL_ADJUSTMENT': 'Penyesuaian Manual',
                                    'STOCK_OPNAME': 'Hasil Stock Opname',
                                    'PRODUCT_RETURN': 'Retur Produk',
                                    'DAMAGE_LOSS': 'Kerusakan/Kehilangan',
                                    'EXPIRY_DISPOSAL': 'Produk Kadaluarsa'
                                };
                                return typeLabels[data] || data;
                            }
                        },
                        {
                            data: 'warehouse_name',
                            responsivePriority: 5,
                            render: function(data, type, full, meta) {
                                const warehouseDetailUrl =
                                    "{{ route('master_data.warehouse.detail', ':id') }}".replace(
                                        ':id', full.warehouse_id);
                                return `<a href="${warehouseDetailUrl}" class="text-primary fw-semibold">${data}</a>`;
                            }
                        },
                        {
                            data: 'tags',
                            responsivePriority: 6,
                            render: function(data, type, row) {
                                if (!data || data.length === 0) return '-';
                                return data.map(tag => {
                                    const colorClass = `bg-label-${tag.color}`;
                                    return tag.code ?
                                        `<span class="badge ${colorClass} me-1">${tag.name}</span>` :
                                        '';
                                }).join('');
                            }
                        },
                        {
                            data: 'reference_number',
                            responsivePriority: 7,
                            render: function(data, type, row, meta) {
                                if (!data) {
                                    return '-';
                                }

                                let detailUrl = '#';
                                switch (row.adjustment_type) {
                                    case 'STOCK_OPNAME':
                                        detailUrl =
                                            "{{ route('master_data.stock_opname.detail', ':ref') }}"
                                            .replace(':ref', data);
                                        break;
                                    case 'PRODUCT_RETURN':
                                        break;
                                    default:
                                        return data;
                                }

                                return `<a href="${detailUrl}" class="text-primary fw-semibold" title="Lihat detail ${row.adjustment_type}">${data}</a>`;
                            }
                        },
                        {
                            data: 'total_items',
                            responsivePriority: 8,
                            render: function(data, type, row, meta) {
                                return data.toLocaleString('id-ID');
                            }
                        },
                        {
                            data: 'total_adjustment_qty',
                            responsivePriority: 9,
                            render: function(data, type, row, meta) {
                                if (data > 0) {
                                    return `<span class="text-success">+${data.toLocaleString('id-ID')}</span>`;
                                } else if (data < 0) {
                                    return `<span class="text-danger">${data.toLocaleString('id-ID')}</span>`;
                                }
                                return `<span class="text-muted">${data}</span>`;
                            }
                        },
                        {
                            data: 'total_adjustment_value',
                            responsivePriority: 10,
                            render: function(data, type, row, meta) {
                                const formatted = new Intl.NumberFormat('id-ID', {
                                    style: 'currency',
                                    currency: 'IDR',
                                    minimumFractionDigits: 0
                                }).format(Math.abs(data));

                                if (data > 0) {
                                    return `<span class="text-success">+${formatted}</span>`;
                                } else if (data < 0) {
                                    return `<span class="text-danger">-${formatted}</span>`;
                                }
                                return `<span class="text-muted">${formatted}</span>`;
                            }
                        },
                        {
                            data: 'pic_name',
                            responsivePriority: 11
                        },
                        {
                            data: null,
                            orderable: false,
                            searchable: false,
                            responsivePriority: 1,
                            width: '120px',
                            render: function(data, type, row, meta) {
                                const detailUrl =
                                    `{{ route('main_transaction.stock_adjustment.detail', ':id') }}`
                                    .replace(':id', row.id);

                                let actions = '<div class="d-flex gap-2">';

                                if (canRead) {
                                    actions += `<a href="${detailUrl}" class="btn btn-sm btn-primary waves-effect waves-light" title="Detail">
                                <i class="icon-base ti tabler-eye"></i>
                            </a>`;
                                }

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
                    scrollX: true,
                    responsive: true,
                    orderCellsTop: true,
                    ordering: false
                });
            }

            // Initialize first table
            initWaitingAdjustmentTable();

            // Tab switching
            $('a[data-bs-toggle="list"]').on('shown.bs.tab', function(e) {
                const target = $(e.target).attr('href');

                if (target === '#waiting-adjustment-tab') {
                    initWaitingAdjustmentTable();
                } else if (target === '#waiting-approval-tab') {
                    initWaitingApprovalTable();
                } else if (target === '#finished-tab') {
                    initFinishedTable();
                }
            });

            // Search functionality for each tab
            $('#waiting-search-field').on('keyup', function() {
                if (waitingAdjustmentTable) {
                    waitingAdjustmentTable.search(this.value).draw();
                }
            });

            $('#approval-search-field').on('keyup', function() {
                if (waitingApprovalTable) {
                    waitingApprovalTable.search(this.value).draw();
                }
            });

            $('#finished-search-field').on('keyup', function() {
                if (finishedTable) {
                    finishedTable.search(this.value).draw();
                }
            });

            // Filter functionality for each tab
            $('#waiting-type-filter').on('change', function() {
                if (waitingAdjustmentTable) {
                    // waitingAdjustmentTable.draw();
                    initWaitingAdjustmentTable();
                }
            });

            $('#approval-type-filter').on('change', function() {
                if (waitingApprovalTable) {
                    // waitingApprovalTable.draw();
                    initWaitingApprovalTable();
                }
            });

            $('#finished-type-filter').on('change', function() {
                if (finishedTable) {
                    finishedTable.draw();
                    initFinishedTable();
                }
            });

            // Tag Column Toggle Functionality
            $('#toggleTagColumn').on('change', function() {
                const tagColumn = waitingAdjustmentTable.column(4); // Tag column index (0-based)
                tagColumn.visible(this.checked);
            });

            $('#toggleTagColumnApproval').on('change', function() {
                const tagColumn = waitingApprovalTable.column(4); // Tag column index (0-based)
                tagColumn.visible(this.checked);
            });

            $('#toggleTagColumnFinished').on('change', function() {
                const tagColumn = finishedTable.column(4); // Tag column index (0-based)
                tagColumn.visible(this.checked);
            });
        });

        // Action button functions
        function viewDetail(id) {
            // Navigate to stock adjustment detail page
            window.location.href = "{{ route('main_transaction.stock_adjustment.detail', ':id') }}".replace(':id', id);
        }

        function editRecord(id) {
            // Navigate to stock adjustment edit page
            window.location.href = "{{ route('main_transaction.stock_adjustment.edit', ':id') }}".replace(':id', id);
        }
    </script>
@endsection
