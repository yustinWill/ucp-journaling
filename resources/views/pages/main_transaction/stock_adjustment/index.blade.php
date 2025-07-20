@extends('layouts.default')
@section('page_title')
    List Stock Adjustment
@stop
@section('content')
    <!-- Summary Cards -->
    <div class="row">
        <div class="col-lg-4 col-sm-6">
            <div class="card card-border-shadow-warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="icon-base ti tabler-clock icon-28px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0" id="waiting-adjustment-count">15</h4>
                    </div>
                    <p class="mb-1">Menunggu Adjustment</p>
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
                        <h4 class="mb-0" id="waiting-approval-count">12</h4>
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
    </div>
    
    <!-- Tabs -->
    <div class="mt-6">
        <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#waiting-adjustment-tab" type="button"
                role="tab" aria-controls="waiting-adjustment-tab" aria-selected="true">
                <i class="icon-base ti tabler-clock me-2"></i>
                Menunggu Adjustment
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#waiting-approval-tab" type="button"
                role="tab" aria-controls="waiting-approval-tab" aria-selected="false">
                <i class="icon-base ti tabler-hourglass me-2"></i>
                Menunggu Persetujuan
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#finished-tab" type="button"
                role="tab" aria-controls="finished-tab" aria-selected="false">
                <i class="icon-base ti tabler-check me-2"></i>
                Selesai
            </button>
        </li>
        </ul>
        
        <div class="tab-content p-0 mt-3">
            <!-- Waiting Adjustment Tab -->
            <div class="tab-pane fade show active" id="waiting-adjustment-tab" role="tabpanel">
                <div class="card">
                    <h5 class="card-header">Menunggu Adjustment</h5>
                    <div class="card-body">
                        <!-- Advanced Search Form -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Filter Tipe:</label>
                                <select class="global-select2 form-select" id="waiting-type-filter" data-allow-clear="true">
                                    <option value="">Semua Tipe</option>
                                    @foreach($adjustmentTypes as $type)
                                        <option value="{{ $type['value'] }}">{{ $type['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pencarian:</label>
                                <input type="text" class="form-control" id="waiting-search-field" placeholder="Cari Stock Adjustment">
                            </div>
                        </div>
                        
                        <hr class="my-6 mx-n6" />
                        
                        <!-- Action Buttons -->
                        <div class="mt-4">
                            <div class="d-flex justify-content-end">
                                <div class="d-flex gap-2">
                                    <a href="{{ route('main_transaction.stock_adjustment.add') }}"
                                        class="btn btn-md btn-primary waves-effect waves-light"><span
                                            class="icon-xs icon-base ti tabler-plus me-2"></span> Tambah Data</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Data Table -->
                    <div class="card-datatable">
                        <table id="waiting-adjustment-table" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Nomor Referensi</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Tipe</th>
                                    <th>Gudang</th>
                                    <th>Total Item</th>
                                    <th>Total Qty</th>
                                    <th>PIC</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Waiting Approval Tab -->
            <div class="tab-pane fade" id="waiting-approval-tab" role="tabpanel">
                <div class="card">
                    <h5 class="card-header">Menunggu Persetujuan</h5>
                    <div class="card-body">
                        <!-- Advanced Search Form -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Filter Tipe:</label>
                                <select class="global-select2 form-select" id="approval-type-filter">
                                    <option value="">Semua Tipe</option>
                                    @foreach($adjustmentTypes as $type)
                                        <option value="{{ $type['value'] }}">{{ $type['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pencarian:</label>
                                <input type="text" class="form-control" id="approval-search-field" placeholder="Cari Stock Adjustment">
                            </div>
                        </div>
                        
                        <hr class="my-6 mx-n6" />
                        
                        <!-- Action Buttons -->
                        <div class="mt-4">
                            <div class="d-flex justify-content-end">
                                <div class="d-flex gap-2">
                                    <a href="{{ route('main_transaction.stock_adjustment.add') }}"
                                        class="btn btn-md btn-primary waves-effect waves-light"><span
                                            class="icon-xs icon-base ti tabler-plus me-2"></span> Tambah Data</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Data Table -->
                    <div class="card-datatable">
                        <table id="waiting-approval-table" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Nomor Adjustment</th>
                                    <th>Tanggal</th>
                                    <th>Tipe</th>
                                    <th>Gudang</th>
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
            </div>
            
            <!-- Finished Tab -->
            <div class="tab-pane fade" id="finished-tab" role="tabpanel">
                <div class="card">
                    <h5 class="card-header">Stock Adjustment Selesai</h5>
                    <div class="card-body">
                        <!-- Advanced Search Form -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Filter Tipe:</label>
                                <select class="global-select2 form-select" id="finished-type-filter">
                                    <option value="">Semua Tipe</option>
                                    @foreach($adjustmentTypes as $type)
                                        <option value="{{ $type['value'] }}">{{ $type['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pencarian:</label>
                                <input type="text" class="form-control" id="finished-search-field" placeholder="Cari Stock Adjustment">
                            </div>
                        </div>
                        
                        <hr class="my-6 mx-n6" />
                        
                        <!-- Action Buttons -->
                        <div class="mt-4">
                            <div class="d-flex justify-content-end">
                                <div class="d-flex gap-2">
                                    <a href="{{ route('main_transaction.stock_adjustment.import') }}"
                                        class="btn btn-md btn-primary waves-effect waves-light"><span
                                            class="icon-xs icon-base ti tabler-file-upload me-2"></span> Import</a>
                                    <a href="{{ route('main_transaction.stock_adjustment.export') }}"
                                        class="btn btn-md btn-primary waves-effect waves-light"><span
                                            class="icon-xs icon-base ti tabler-file-download me-2"></span> Export</a>
                                    <a href="{{ route('main_transaction.stock_adjustment.add') }}"
                                        class="btn btn-md btn-primary waves-effect waves-light"><span
                                            class="icon-xs icon-base ti tabler-plus me-2"></span> Tambah Data</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Data Table -->
                    <div class="card-datatable">
                        <table id="finished-table" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Nomor Adjustment</th>
                                    <th>Tanggal</th>
                                    <th>Tipe</th>
                                    <th>Gudang</th>
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
    
    let waitingAdjustmentTable, waitingApprovalTable, finishedTable;
    let currentActiveTab = 'waiting-adjustment';
    
    // Initialize all DataTables for each tab
    function initWaitingAdjustmentTable() {
        if (waitingAdjustmentTable) {
            waitingAdjustmentTable.destroy();
        }
        
        waitingAdjustmentTable = new DataTable('#waiting-adjustment-table', {
            ajax: {
                url: '{{ route("main_transaction.stock_adjustment.index_data_table") }}',
                data: {
                    tab: 'waiting_adjustment'
                }
            },
            columns: [
                { 
                    data: 'reference_number',
                    render: function(data, type, row, meta) {
                        if (!data) {
                            return '-';
                        }
                        
                        // Make reference clickable based on adjustment type
                        let detailUrl = '#';
                        switch(row.adjustment_type) {
                            case 'STOCK_OPNAME':
                                detailUrl = "{{ route('master_data.stock_opname.detail', ':ref') }}".replace(':ref', data);
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
                { data: 'created_date' },
                { 
                    data: 'adjustment_type',
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
                    render: function(data, type, full, meta) {
                        const warehouseDetailUrl = "{{ route('master_data.storage.detail') }}?warehouse_id=" + full.warehouse_id;
                        return `<a href="${warehouseDetailUrl}" class="text-primary fw-semibold">${data}</a>`;
                    }
                },
                { 
                    data: 'total_items',
                    render: function(data, type, row, meta) {
                        return data.toLocaleString('id-ID');
                    }
                },
                { 
                    data: 'total_adjustment_qty',
                    render: function(data, type, row, meta) {
                        if (data > 0) {
                            return `<span class="text-success">+${data.toLocaleString('id-ID')}</span>`;
                        } else if (data < 0) {
                            return `<span class="text-danger">${data.toLocaleString('id-ID')}</span>`;
                        }
                        return `<span class="text-muted">${data}</span>`;
                    }
                },
                { data: 'pic_name' },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row, meta) {
                        const editUrl = `{{ route('main_transaction.stock_adjustment.edit', ':id') }}`.replace(':id', row.id);
                        const detailUrl = `{{ route('main_transaction.stock_adjustment.detail', ':id') }}`.replace(':id', row.id);
                        
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
            responsive: true,
            orderCellsTop: true
        });
    }
    
    function initWaitingApprovalTable() {
        if (waitingApprovalTable) {
            waitingApprovalTable.destroy();
        }
        
        waitingApprovalTable = new DataTable('#waiting-approval-table', {
            ajax: {
                url: '{{ route("main_transaction.stock_adjustment.index_data_table") }}',
                data: {
                    tab: 'waiting_approval'
                }
            },
            columns: [
                { data: 'adjustment_number' },
                { data: 'adjustment_date' },
                { 
                    data: 'adjustment_type',
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
                    render: function(data, type, full, meta) {
                        const warehouseDetailUrl = "{{ route('master_data.storage.detail') }}?warehouse_id=" + full.warehouse_id;
                        return `<a href="${warehouseDetailUrl}" class="text-primary fw-semibold">${data}</a>`;
                    }
                },
                { 
                    data: 'reference_number',
                    render: function(data, type, row, meta) {
                        if (!data) {
                            return '-';
                        }
                        
                        let detailUrl = '#';
                        switch(row.adjustment_type) {
                            case 'STOCK_OPNAME':
                                detailUrl = "{{ route('master_data.stock_opname.detail', ':ref') }}".replace(':ref', data);
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
                    render: function(data, type, row, meta) {
                        return data.toLocaleString('id-ID');
                    }
                },
                { 
                    data: 'total_adjustment_qty',
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
                    render: function(data, type, row, meta) {
                        const editUrl = `{{ route('main_transaction.stock_adjustment.edit', ':id') }}`.replace(':id', row.id);
                        const detailUrl = `{{ route('main_transaction.stock_adjustment.detail', ':id') }}`.replace(':id', row.id);
                        
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
            responsive: true,
            orderCellsTop: true
        });
    }
    
    function initFinishedTable() {
        if (finishedTable) {
            finishedTable.destroy();
        }
        
        finishedTable = new DataTable('#finished-table', {
            ajax: {
                url: '{{ route("main_transaction.stock_adjustment.index_data_table") }}',
                data: {
                    tab: 'finished'
                }
            },
            columns: [
                { data: 'adjustment_number' },
                { data: 'adjustment_date' },
                { 
                    data: 'adjustment_type',
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
                    render: function(data, type, full, meta) {
                        const warehouseDetailUrl = "{{ route('master_data.storage.detail') }}?warehouse_id=" + full.warehouse_id;
                        return `<a href="${warehouseDetailUrl}" class="text-primary fw-semibold">${data}</a>`;
                    }
                },
                { 
                    data: 'reference_number',
                    render: function(data, type, row, meta) {
                        if (!data) {
                            return '-';
                        }
                        
                        let detailUrl = '#';
                        switch(row.adjustment_type) {
                            case 'STOCK_OPNAME':
                                detailUrl = "{{ route('master_data.stock_opname.detail', ':ref') }}".replace(':ref', data);
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
                    render: function(data, type, row, meta) {
                        return data.toLocaleString('id-ID');
                    }
                },
                { 
                    data: 'total_adjustment_qty',
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
                { data: 'pic_name' },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row, meta) {
                        return `
                            <div class="btn-group" role="group">
                                <button class="btn btn-primary btn-sm waves-effect" onclick="viewDetail(${row.id})">
                                    <i class="icon-base ti tabler-eye"></i>
                                </button>
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
            responsive: true,
            orderCellsTop: true
        });
    }
    
    // Initialize first table
    initWaitingAdjustmentTable();
    
    // Tab switching
    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        const target = $(e.target).attr('data-bs-target');
        
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
            waitingAdjustmentTable.draw();
        }
    });
    
    $('#approval-type-filter').on('change', function() {
        if (waitingApprovalTable) {
            waitingApprovalTable.draw();
        }
    });
    
    $('#finished-type-filter').on('change', function() {
        if (finishedTable) {
            finishedTable.draw();
        }
    });
    
    // Button click events - separated by tab
    $('#btn-add-waiting').on('click', function() {
        window.location.href = "{{ route('main_transaction.stock_adjustment.add') }}";
    });
    
    $('#btn-add-approval').on('click', function() {
        window.location.href = "{{ route('main_transaction.stock_adjustment.add') }}";
    });
    
    $('#btn-add-finished').on('click', function() {
        window.location.href = "{{ route('main_transaction.stock_adjustment.add') }}";
    });
    
    // Import button events - with type parameter
    $('#btn-import-finished').on('click', function() {
        // Create file input for import
        const fileInput = document.createElement('input');
        fileInput.type = 'file';
        fileInput.accept = '.xlsx,.xls,.csv';
        fileInput.onchange = function(e) {
            const formData = new FormData();
            formData.append('file', e.target.files[0]);
            formData.append('type', 'finished');
            
            // TODO: Implement actual import functionality
            console.log('Import Stock Adjustment file:', e.target.files[0].name);
            // fetch('{{ route("main_transaction.stock_adjustment.import") }}', {
            //     method: 'POST',
            //     body: formData,
            //     headers: {
            //         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            //     }
            // });
        };
        fileInput.click();
    });
    
    // Export button events - with type parameter
    $('#btn-export-finished').on('click', function() {
        // Handle export for finished stock adjustments
        window.location.href = "{{ route('main_transaction.stock_adjustment.export') }}?type=finished";
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