@extends('layouts.default')
@section('page_title')
    List Penjualan
@stop
@section('content')
    <!-- Summary Cards -->
    <div class="row">
        <div class="col-lg-4 col-sm-6">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="icon-base ti tabler-receipt icon-28px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0" id="unpaid-receivables-count">Rp 240,000,000</h4>
                    </div>
                    <p class="mb-1">Piutang Belum Dibayar</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6">
            <div class="card card-border-shadow-warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="icon-base ti tabler-clock-exclamation icon-28px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0" id="overdue-receivables-count">Rp 45,000,000</h4>
                    </div>
                    <p class="mb-1">Piutang Telat Dibayar</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6">
            <div class="card card-border-shadow-success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="icon-base ti tabler-cash icon-28px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0" id="recent-receipts-count">Rp 125,000,000</h4>
                    </div>
                    <p class="mb-1">Penerimaan 30 Hari Terakhir</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Content Card with Tabs -->
    <div class="mt-6">
        <div class="card">
            <h4 class="card-header">Kelola Penjualan</h4>
            <div class="card-body pb-0">
                <!-- Tab Navigation Inside Card -->
                <div class="list-group list-group-horizontal-md text-md-center" role="tablist">
                    <a class="list-group-item list-group-item-action active" data-bs-toggle="list" 
                        href="#pesanan-tab" role="tab" aria-controls="pesanan-tab" aria-selected="true">
                        <i class="icon-base ti tabler-shopping-cart me-2"></i>
                        <b>Pesanan</b>
                    </a>
                    <a class="list-group-item list-group-item-action" data-bs-toggle="list" 
                        href="#pengiriman-tab" role="tab" aria-controls="pengiriman-tab" aria-selected="false">
                        <i class="icon-base ti tabler-truck-delivery me-2"></i>
                        <b>Pengiriman</b>
                    </a>
                    <a class="list-group-item list-group-item-action" data-bs-toggle="list" 
                        href="#faktur-tab" role="tab" aria-controls="faktur-tab" aria-selected="false">
                        <i class="icon-base ti tabler-file-invoice me-2"></i>
                        <b>Faktur</b>
                    </a>
                </div>
            </div>

            <!-- Tab Content Inside Card -->
            <div class="tab-content pt-0">
                <!-- Pesanan Tab -->
                <div class="tab-pane fade show active mx-n6" id="pesanan-tab" role="tabpanel">
                    <div class="card-body">
                        <!-- Advanced Search Form -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Status:</label>
                                <select class="global-select2 form-select" id="pesanan-status-filter" data-allow-clear="true">
                                    <option value="">Semua Status</option>
                                    @foreach($pesananStatuses as $status)
                                        <option value="{{ $status['value'] }}">{{ $status['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pencarian:</label>
                                <input type="text" class="form-control" id="pesanan-search-field" placeholder="Cari Pesanan Penjualan">
                            </div>
                        </div>
                        
                        <hr class="my-6 mx-n6" />
                        
                        <!-- Action Buttons -->
                        <div class="mt-4">
                            <div class="d-flex justify-content-between">
                                <!-- Column Toggle -->
                                <div class="d-flex align-items-center">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="toggleTagColumnPesanan" checked>
                                        <label class="form-check-label" for="toggleTagColumnPesanan">
                                            <i class="icon-base ti tabler-tag me-1"></i>Tampilkan Kolom Tag
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    @if($canImportSales)
                                        <a href="{{ route('main_transaction.sales.import') }}?type=pesanan"
                                            class="btn btn-md btn-primary waves-effect waves-light"><span
                                                class="icon-xs icon-base ti tabler-file-upload me-2"></span> Import</a>
                                    @endif
                                    @if($canExportSales)
                                        <a href="{{ route('main_transaction.sales.export') }}?type=pesanan"
                                            class="btn btn-md btn-primary waves-effect waves-light"><span
                                                class="icon-xs icon-base ti tabler-file-download me-2"></span> Export</a>
                                    @endif
                                    @if($canAddSales)
                                        <a href="{{ route('main_transaction.sales.add.so') }}"
                                            class="btn btn-md btn-primary waves-effect waves-light"><span
                                                class="icon-xs icon-base ti tabler-plus me-2"></span> Tambah Data</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Data Table -->
                    <div class="card-datatable">
                        <table id="pesanan-table" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>No</th>
                                    <th>Customer</th>
                                    <th>Tag</th>
                                    <th>Tgl Jatuh Tempo</th>
                                    <th>Status</th>
                                    <th>Uang Muka</th>
                                    <th>Total</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            
                <!-- Pengiriman Tab -->
                <div class="tab-pane fade mx-n6" id="pengiriman-tab" role="tabpanel">
                    <div class="card-body">
                        <!-- Advanced Search Form -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Status:</label>
                                <select class="global-select2 form-select" id="pengiriman-status-filter" data-allow-clear="true">
                                    <option value="">Semua Status</option>
                                    @foreach($pengirimanStatuses as $status)
                                        <option value="{{ $status['value'] }}">{{ $status['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pencarian:</label>
                                <input type="text" class="form-control" id="pengiriman-search-field" placeholder="Cari Pengiriman Penjualan">
                            </div>
                        </div>
                        
                        <hr class="my-6 mx-n6" />
                        
                        <!-- Action Buttons -->
                        <div class="mt-4">
                            <div class="d-flex justify-content-between">
                                <!-- Column Toggle -->
                                <div class="d-flex align-items-center">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="toggleTagColumnPengiriman" checked>
                                        <label class="form-check-label" for="toggleTagColumnPengiriman">
                                            <i class="icon-base ti tabler-tag me-1"></i>Tampilkan Kolom Tag
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    @if($canImportSales)
                                        <a href="{{ route('main_transaction.sales.import') }}?type=pengiriman"
                                            class="btn btn-md btn-primary waves-effect waves-light"><span
                                                class="icon-xs icon-base ti tabler-file-upload me-2"></span> Import</a>
                                    @endif
                                    @if($canExportSales)
                                        <a href="{{ route('main_transaction.sales.export') }}?type=pengiriman"
                                            class="btn btn-md btn-primary waves-effect waves-light"><span
                                                class="icon-xs icon-base ti tabler-file-download me-2"></span> Export</a>
                                    @endif
                                    @if($canAddSales)
                                        <a href="{{ route('main_transaction.sales.add.sd') }}"
                                            class="btn btn-md btn-primary waves-effect waves-light"><span
                                                class="icon-xs icon-base ti tabler-plus me-2"></span> Tambah Data</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Data Table -->
                    <div class="card-datatable">
                        <table id="pengiriman-table" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>No</th>
                                    <th>Customer</th>
                                    <th>Tag</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            
                <!-- Faktur Tab -->
                <div class="tab-pane fade mx-n6" id="faktur-tab" role="tabpanel">
                    <div class="card-body">
                        <!-- Advanced Search Form -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Status:</label>
                                <select class="global-select2 form-select" id="faktur-status-filter" data-allow-clear="true">
                                    <option value="">Semua Status</option>
                                    @foreach($fakturStatuses as $status)
                                        <option value="{{ $status['value'] }}">{{ $status['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pencarian:</label>
                                <input type="text" class="form-control" id="faktur-search-field" placeholder="Cari Faktur Penjualan">
                            </div>
                        </div>
                        
                        <hr class="my-6 mx-n6" />
                        
                        <!-- Action Buttons -->
                        <div class="mt-4">
                            <div class="d-flex justify-content-between">
                                <!-- Column Toggle -->
                                <div class="d-flex align-items-center">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="toggleTagColumnFaktur" checked>
                                        <label class="form-check-label" for="toggleTagColumnFaktur">
                                            <i class="icon-base ti tabler-tag me-1"></i>Tampilkan Kolom Tag
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    @if($canImportSales)
                                        <a href="{{ route('main_transaction.sales.import') }}?type=faktur"
                                            class="btn btn-md btn-primary waves-effect waves-light"><span
                                                class="icon-xs icon-base ti tabler-file-upload me-2"></span> Import</a>
                                    @endif
                                    @if($canExportSales)
                                        <a href="{{ route('main_transaction.sales.export') }}?type=faktur"
                                            class="btn btn-md btn-primary waves-effect waves-light"><span
                                                class="icon-xs icon-base ti tabler-file-download me-2"></span> Export</a>
                                    @endif
                                    @if($canAddSales)
                                        <a href="{{ route('main_transaction.sales.add.si') }}"
                                            class="btn btn-md btn-primary waves-effect waves-light"><span
                                                class="icon-xs icon-base ti tabler-plus me-2"></span> Tambah Data</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Data Table -->
                    <div class="card-datatable">
                        <table id="faktur-table" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>No</th>
                                    <th>Customer</th>
                                    <th>Tag</th>
                                    <th>Tgl Jatuh Tempo</th>
                                    <th>Status</th>
                                    <th>Sisa Tagihan</th>
                                    <th>Total</th>
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
     * Sales Tabs Explanation:
     * 1. Pesanan (orders): Sales orders from customers
     * 2. Pengiriman (deliveries): Items delivered to customers
     * 3. Faktur (invoices): Customer invoices to be collected
     */
    
    let pesananTable, pengirimanTable, fakturTable;
    let currentActiveTab = 'pesanan';
    
    // Permission variables
    const canRead = @json($canViewSales ?? true);
    const canEdit = @json($canEditSales ?? true);
    
    // Initialize all DataTables for each tab
    function initPesananTable() {
        if (pesananTable) {
            pesananTable.destroy();
        }
        
        pesananTable = new DataTable('#pesanan-table', {
            ajax: {
                url: '{{ route("main_transaction.sales.index_data_table") }}',
                data: {
                    tab: 'pesanan'
                }
            },
            columns: [
                { data: 'date', responsivePriority: 2 },
                { 
                    data: 'reference_no',
                    responsivePriority: 3,
                    render: function(data, type, row, meta) {
                        const detailUrl = `{{ route('main_transaction.sales.detail', ':id') }}`.replace(':id', row.id);
                        return `<a href="${detailUrl}" class="text-primary fw-semibold">${data}</a>`;
                    }
                },
                { 
                    data: 'customer',
                    responsivePriority: 4,
                    render: function(data, type, full, meta) {
                        const customerDetailUrl = "{{ route('master_data.contact.detail', ':id') }}".replace(':id', full.customer_id);
                        return `<a href="${customerDetailUrl}" class="text-primary fw-semibold">${data}</a>`;
                    }
                },
                {
                    data: 'tags',
                    responsivePriority: 5,
                    render: function(data, type, row) {
                        if (!data || data.length === 0) return '-';
                        return data.map(tag => {
                            const colorClass = `bg-label-${tag.color}`;
                            return `<span class="badge ${colorClass} me-1">${tag.name}</span>`;
                        }).join('');
                    }
                },
                { data: 'due_date', responsivePriority: 6 },
                { 
                    data: 'status',
                    responsivePriority: 7,
                    render: function(data, type, row, meta) {
                        let badgeClass = '';
                        switch (data) {
                            case 'Belum Ditagih':
                                badgeClass = 'bg-label-secondary';
                                break;
                            case 'Dikirim Sebagian':
                                badgeClass = 'bg-label-warning';
                                break;
                            case 'Selesai':
                                badgeClass = 'bg-label-success';
                                break;
                            default:
                                badgeClass = 'bg-label-primary';
                        }
                        return `<span class="badge ${badgeClass}">${data}</span>`;
                    }
                },
                { 
                    data: 'down_payment',
                    className: 'text-end',
                    responsivePriority: 8,
                    render: function(data, type, row, meta) {
                        if (type === 'display' && data != null && !isNaN(data)) {
                            return 'Rp ' + parseFloat(data).toLocaleString('id-ID');
                        }
                        return data ?? '';
                    }
                },
                { 
                    data: 'total',
                    className: 'text-end',
                    responsivePriority: 9,
                    render: function(data, type, row, meta) {
                        if (type === 'display' && data != null && !isNaN(data)) {
                            return 'Rp ' + parseFloat(data).toLocaleString('id-ID');
                        }
                        return data ?? '';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    responsivePriority: 1,
                    width: '120px',
                    render: function(data, type, row, meta) {
                        const editUrl = `{{ route('main_transaction.sales.edit', ':id') }}`.replace(':id', row.id);
                        const detailUrl = `{{ route('main_transaction.sales.detail', ':id') }}`.replace(':id', row.id);
                        
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
            // scrollX: true,
            responsive: true,
            orderCellsTop: true,
            ordering: false
        });
    }
    
    function initPengirimanTable() {
        if (pengirimanTable) {
            pengirimanTable.destroy();
        }
        
        pengirimanTable = new DataTable('#pengiriman-table', {
            ajax: {
                url: '{{ route("main_transaction.sales.index_data_table") }}',
                data: {
                    tab: 'pengiriman'
                }
            },
            columns: [
                { data: 'date', responsivePriority: 2 },
                { 
                    data: 'reference_no',
                    responsivePriority: 3,
                    render: function(data, type, row, meta) {
                        const detailUrl = `{{ route('main_transaction.sales.detail', ':id') }}`.replace(':id', row.id);
                        return `<a href="${detailUrl}" class="text-primary fw-semibold">${data}</a>`;
                    }
                },
                { 
                    data: 'customer',
                    responsivePriority: 4,
                    render: function(data, type, full, meta) {
                        const customerDetailUrl = "{{ route('master_data.contact.detail', ':id') }}".replace(':id', full.customer_id);
                        return `<a href="${customerDetailUrl}" class="text-primary fw-semibold">${data}</a>`;
                    }
                },
                {
                    data: 'tags',
                    responsivePriority: 5,
                    render: function(data, type, row) {
                        if (!data || data.length === 0) return '-';
                        return data.map(tag => {
                            const colorClass = `bg-label-${tag.color}`;
                            return `<span class="badge ${colorClass} me-1">${tag.name}</span>`;
                        }).join('');
                    }
                },
                { 
                    data: 'status',
                    responsivePriority: 6,
                    render: function(data, type, row, meta) {
                        let badgeClass = '';
                        switch (data) {
                            case 'Belum Ditagih':
                                badgeClass = 'bg-label-secondary';
                                break;
                            case 'Dikirim Sebagian':
                                badgeClass = 'bg-label-warning';
                                break;
                            case 'Selesai':
                                badgeClass = 'bg-label-success';
                                break;
                            default:
                                badgeClass = 'bg-label-primary';
                        }
                        return `<span class="badge ${badgeClass}">${data}</span>`;
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    responsivePriority: 1,
                    width: '120px',
                    render: function(data, type, row, meta) {
                        const editUrl = `{{ route('main_transaction.sales.edit', ':id') }}`.replace(':id', row.id);
                        const detailUrl = `{{ route('main_transaction.sales.detail', ':id') }}`.replace(':id', row.id);
                        
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
            // scrollX: true,
            responsive: true,
            orderCellsTop: true,
            ordering: false
        });
    }
    
    function initFakturTable() {
        if (fakturTable) {
            fakturTable.destroy();
        }
        
        fakturTable = new DataTable('#faktur-table', {
            ajax: {
                url: '{{ route("main_transaction.sales.index_data_table") }}',
                data: {
                    tab: 'faktur'
                }
            },
            columns: [
                { data: 'date', responsivePriority: 2 },
                { 
                    data: 'reference_no',
                    responsivePriority: 3,
                    render: function(data, type, row, meta) {
                        const detailUrl = `{{ route('main_transaction.sales.detail', ':id') }}`.replace(':id', row.id);
                        return `<a href="${detailUrl}" class="text-primary fw-semibold">${data}</a>`;
                    }
                },
                { 
                    data: 'customer',
                    responsivePriority: 4,
                    render: function(data, type, full, meta) {
                        const customerDetailUrl = "{{ route('master_data.contact.detail', ':id') }}".replace(':id', full.customer_id);
                        return `<a href="${customerDetailUrl}" class="text-primary fw-semibold">${data}</a>`;
                    }
                },
                {
                    data: 'tags',
                    responsivePriority: 5,
                    render: function(data, type, row) {
                        if (!data || data.length === 0) return '-';
                        return data.map(tag => {
                            const colorClass = `bg-label-${tag.color}`;
                            return `<span class="badge ${colorClass} me-1">${tag.name}</span>`;
                        }).join('');
                    }
                },
                { data: 'due_date', responsivePriority: 6 },
                { 
                    data: 'status',
                    responsivePriority: 7,
                    render: function(data, type, row, meta) {
                        let badgeClass = '';
                        switch (data) {
                            case 'Belum Dibayar':
                                badgeClass = 'bg-label-secondary';
                                break;
                            case 'Menunggu Pembayaran':
                                badgeClass = 'bg-label-warning';
                                break;
                            case 'Telat Bayar':
                                badgeClass = 'bg-label-danger';
                                break;
                            case 'Dibayar':
                                badgeClass = 'bg-label-success';
                                break;
                            case 'Dibayar Sebagian':
                                badgeClass = 'bg-label-info';
                                break;
                            default:
                                badgeClass = 'bg-label-primary';
                        }
                        return `<span class="badge ${badgeClass}">${data}</span>`;
                    }
                },
                { 
                    data: 'remaining_bill',
                    className: 'text-end',
                    responsivePriority: 8,
                    render: function(data, type, row, meta) {
                        if (type === 'display' && data != null && !isNaN(data)) {
                            return 'Rp ' + parseFloat(data).toLocaleString('id-ID');
                        }
                        return data ?? '';
                    }
                },
                { 
                    data: 'total',
                    className: 'text-end',
                    responsivePriority: 9,
                    render: function(data, type, row, meta) {
                        if (type === 'display' && data != null && !isNaN(data)) {
                            return 'Rp ' + parseFloat(data).toLocaleString('id-ID');
                        }
                        return data ?? '';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    responsivePriority: 1,
                    width: '120px',
                    render: function(data, type, row, meta) {
                        const editUrl = `{{ route('main_transaction.sales.edit', ':id') }}`.replace(':id', row.id);
                        const detailUrl = `{{ route('main_transaction.sales.detail', ':id') }}`.replace(':id', row.id);
                        
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
            // scrollX: true,
            responsive: true,
            orderCellsTop: true,
            ordering: false
        });
    }
    
    // Initialize first table
    initPesananTable();
    
    // Tab switching
    $('a[data-bs-toggle="list"]').on('shown.bs.tab', function (e) {
        const target = $(e.target).attr('href');
        
        if (target === '#pesanan-tab') {
            initPesananTable();
        } else if (target === '#pengiriman-tab') {
            initPengirimanTable();
        } else if (target === '#faktur-tab') {
            initFakturTable();
        }
    });
    
    // Search functionality for each tab
    $('#pesanan-search-field').on('keyup', function() {
        if (pesananTable) {
            pesananTable.search(this.value).draw();
        }
    });
    
    $('#pengiriman-search-field').on('keyup', function() {
        if (pengirimanTable) {
            pengirimanTable.search(this.value).draw();
        }
    });
    
    $('#faktur-search-field').on('keyup', function() {
        if (fakturTable) {
            fakturTable.search(this.value).draw();
        }
    });
    
    // Filter functionality for each tab
    $('#pesanan-status-filter').on('change', function() {
        if (pesananTable) {
            pesananTable.draw();
        }
    });
    
    $('#pengiriman-status-filter').on('change', function() {
        if (pengirimanTable) {
            pengirimanTable.draw();
        }
    });
    
    $('#faktur-status-filter').on('change', function() {
        if (fakturTable) {
            fakturTable.draw();
        }
    });
    
    // Tag Column Toggle Functionality
    $('#toggleTagColumnPesanan').on('change', function() {
        const tagColumn = pesananTable.column(3); // Tag column index (0-based)
        tagColumn.visible(this.checked);
    });

    $('#toggleTagColumnPengiriman').on('change', function() {
        const tagColumn = pengirimanTable.column(3); // Tag column index (0-based) 
        tagColumn.visible(this.checked);
    });

    $('#toggleTagColumnFaktur').on('change', function() {
        const tagColumn = fakturTable.column(3); // Tag column index (0-based)
        tagColumn.visible(this.checked);
    });
});

// Action button functions
function viewDetail(id) {
    // Navigate to sales detail page
    window.location.href = "{{ route('main_transaction.sales.detail', ':id') }}".replace(':id', id);
}

function editRecord(id) {
    // Navigate to sales edit page
    window.location.href = "{{ route('main_transaction.sales.edit', ':id') }}".replace(':id', id);
}
</script>
@endsection