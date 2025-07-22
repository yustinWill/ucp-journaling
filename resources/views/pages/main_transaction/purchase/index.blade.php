@extends('layouts.default')
@section('page_title')
    List Pembelian
@stop
@section('content')
    <!-- Summary Cards -->
    <div class="row">
        <div class="col-lg-4 col-sm-6">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-primary"><i
                                    class="icon-base ti tabler-file-invoice icon-28px"></i></span>
                        </div>
                        <h4 class="mb-0">Rp 150,000,000</h4>
                    </div>
                    <p class="mb-1">Faktur Belum Dibayar</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6">
            <div class="card card-border-shadow-warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-warning"><i
                                    class="icon-base ti tabler-clock-exclamation icon-28px"></i></span>
                        </div>
                        <h4 class="mb-0">Rp 35,000,000</h4>
                    </div>
                    <p class="mb-1">Faktur Telat Dibayar</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6">
            <div class="card card-border-shadow-success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-success"><i
                                    class="icon-base ti tabler-calendar-check icon-28px"></i></span>
                        </div>
                        <h4 class="mb-0">Rp 85,000,000</h4>
                    </div>
                    <p class="mb-1">Pelunasan 30 Hari Terakhir</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tabs -->
    <div class="mt-6">
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
        
        <div class="tab-content p-0 mt-3">
            <!-- Pesanan Tab -->
            <div class="tab-pane fade show active" id="pesanan-tab" role="tabpanel">
                <div class="card">
                    <h4 class="card-header">Pesanan Pembelian</h4>
                    <div class="card-body">
                        <!-- Advanced Search Form -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Status:</label>
                                <select class="global-select2 form-select" id="pesanan-status-filter" data-allow-clear="false">
                                    <option value="">Semua Status</option>
                                    <option value="Belum Ditagih">Belum Ditagih</option>
                                    <option value="Dikirim Sebagian">Dikirim Sebagian</option>
                                    <option value="Selesai">Selesai</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pencarian:</label>
                                <input type="text" class="form-control" id="pesanan-search" placeholder="Cari Transaksi">
                            </div>
                        </div>
                        
                        <hr class="my-6 mx-n6" />
                        
                        <!-- Action Buttons -->
                        <div class="mt-4">
                            <div class="d-flex justify-content-end">
                                <div class="d-flex gap-2">
                                    <a href="{{ route('main_transaction.purchase.import') }}?type=pesanan"
                                        class="btn btn-md btn-primary waves-effect waves-light"><span
                                            class="icon-xs icon-base ti tabler-file-upload me-2"></span> Import</a>
                                    <a href="{{ route('main_transaction.purchase.export') }}?type=pesanan"
                                        class="btn btn-md btn-primary waves-effect waves-light"><span
                                            class="icon-xs icon-base ti tabler-file-download me-2"></span> Export</a>
                                    <a href="{{ route('main_transaction.purchase.add.po') }}"
                                        class="btn btn-md btn-primary waves-effect waves-light"><span
                                            class="icon-xs icon-base ti tabler-plus me-2"></span> Tambah Data</a>
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
                                    <th>Supplier</th>
                                    <th>Tgl Jatuh Tempo</th>
                                    <th>Status</th>
                                    <th>Jumlah DP</th>
                                    <th>Total</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Pengiriman Tab -->
            <div class="tab-pane fade" id="pengiriman-tab" role="tabpanel">
                <div class="card">
                    <h4 class="card-header">Pengiriman Pembelian</h4>
                    <div class="card-body">
                        <!-- Advanced Search Form -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Status:</label>
                                <select class="global-select2 form-select" id="pengiriman-status-filter" data-allow-clear="false">
                                    <option value="">Semua Status</option>
                                    <option value="Belum Ditagih">Belum Ditagih</option>
                                    <option value="Selesai">Selesai</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pencarian:</label>
                                <input type="text" class="form-control" id="pengiriman-search" placeholder="Cari Transaksi">
                            </div>
                        </div>
                        
                        <hr class="my-6 mx-n6" />
                        
                        <!-- Action Buttons -->
                        <div class="mt-4">
                            <div class="d-flex justify-content-end">
                                <div class="d-flex gap-2">
                                    <a href="{{ route('main_transaction.purchase.import') }}?type=pengiriman"
                                        class="btn btn-md btn-primary waves-effect waves-light"><span
                                            class="icon-xs icon-base ti tabler-file-upload me-2"></span> Import</a>
                                    <a href="{{ route('main_transaction.purchase.export') }}?type=pengiriman"
                                        class="btn btn-md btn-primary waves-effect waves-light"><span
                                            class="icon-xs icon-base ti tabler-file-download me-2"></span> Export</a>
                                    <a href="{{ route('main_transaction.purchase.add.pd') }}"
                                        class="btn btn-md btn-primary waves-effect waves-light"><span
                                            class="icon-xs icon-base ti tabler-plus me-2"></span> Tambah Data</a>
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
                                    <th>Supplier</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Faktur Tab -->
            <div class="tab-pane fade" id="faktur-tab" role="tabpanel">
                <div class="card">
                    <h4 class="card-header">Faktur Pembelian</h4>
                    <div class="card-body">
                        <!-- Advanced Search Form -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Status:</label>
                                <select class="global-select2 form-select" id="faktur-status-filter" data-allow-clear="false">
                                    <option value="">Semua Status</option>
                                    <option value="Menunggu Pembayaran">Menunggu Pembayaran</option>
                                    <option value="Telat Bayar">Telat Bayar</option>
                                    <option value="Dibayar">Dibayar</option>
                                    <option value="Dibayar Sebagian">Dibayar Sebagian</option>
                                    <option value="Belum Dibayar">Belum Dibayar</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pencarian:</label>
                                <input type="text" class="form-control" id="faktur-search" placeholder="Cari Transaksi">
                            </div>
                        </div>
                        
                        <hr class="my-6 mx-n6" />
                        
                        <!-- Action Buttons -->
                        <div class="mt-4">
                            <div class="d-flex justify-content-end">
                                <div class="d-flex gap-2">
                                    <a href="{{ route('main_transaction.purchase.import') }}?type=faktur"
                                        class="btn btn-md btn-primary waves-effect waves-light"><span
                                            class="icon-xs icon-base ti tabler-file-upload me-2"></span> Import</a>
                                    <a href="{{ route('main_transaction.purchase.export') }}?type=faktur"
                                        class="btn btn-md btn-primary waves-effect waves-light"><span
                                            class="icon-xs icon-base ti tabler-file-download me-2"></span> Export</a>
                                    <a href="{{ route('main_transaction.purchase.add.pi') }}"
                                        class="btn btn-md btn-primary waves-effect waves-light"><span
                                            class="icon-xs icon-base ti tabler-plus me-2"></span> Tambah Data</a>
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
                                    <th>Supplier</th>
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
            let pesananTable, pengirimanTable, fakturTable;
            
            // Initialize Pesanan Table
            function initPesananTable() {
                if (pesananTable) {
                    pesananTable.destroy();
                }
                
                pesananTable = new DataTable('#pesanan-table', {
                    ajax: {
                        url: `{{ route('main_transaction.purchase.index_data_table') }}`,
                        data: {
                            tab: 'pesanan'
                        }
                    },
                    columns: [
                        { data: 'date' },
                        { data: 'reference_no' },
                        { 
                            data: 'supplier',
                            render: function(data, type, row) {
                                return `<a href="#" class="text-primary">${data}</a>`;
                            }
                        },
                        { data: 'due_date' },
                        { 
                            data: 'status',
                            render: function(data, type, row) {
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
                            data: 'down_payment',
                            className: 'text-end',
                            render: function(data, type, row) {
                                if (type === 'display' && data != null && !isNaN(data)) {
                                    return 'Rp ' + parseFloat(data).toLocaleString('id-ID');
                                }
                                return data ?? '';
                            }
                        },
                        { 
                            data: 'total',
                            className: 'text-end',
                            render: function(data, type, row) {
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
                            render: function(data, type, row, meta) {
                                return `
                                    <div class="d-flex gap-2">
                                        <a href="/main_transaction/purchase/detail/${row.id}" class="btn btn-sm btn-primary waves-effect waves-light" title="Detail">
                                            <i class="icon-base ti tabler-eye"></i>
                                        </a>
                                        <a href="/main_transaction/purchase/edit/${row.id}" class="btn btn-sm btn-primary waves-effect waves-light" title="Edit">
                                            <i class="icon-base ti tabler-edit"></i>
                                        </a>
                                    </div>
                                `;
                            }
                        }
                    ],
                    scrollX: true,
                    responsive: true,
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
                    }
                });
            }
            
            // Initialize Pengiriman Table
            function initPengirimanTable() {
                if (pengirimanTable) {
                    pengirimanTable.destroy();
                }
                
                pengirimanTable = new DataTable('#pengiriman-table', {
                    ajax: {
                        url: `{{ route('main_transaction.purchase.index_data_table') }}`,
                        data: {
                            tab: 'pengiriman'
                        }
                    },
                    columns: [
                        { data: 'date' },
                        { data: 'reference_no' },
                        { 
                            data: 'supplier',
                            render: function(data, type, row) {
                                return `<a href="#" class="text-primary">${data}</a>`;
                            }
                        },
                        { 
                            data: 'status',
                            render: function(data, type, row) {
                                let badgeClass = data === 'Belum Ditagih' ? 'bg-label-warning' : 'bg-label-success';
                                return `<span class="badge ${badgeClass}">${data}</span>`;
                            }
                        },
                        { 
                            data: null,
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row, meta) {
                                return `
                                    <div class="d-flex gap-2">
                                        <a href="/main_transaction/purchase/detail/${row.id}" class="btn btn-sm btn-primary waves-effect waves-light" title="Detail">
                                            <i class="icon-base ti tabler-eye"></i>
                                        </a>
                                        <a href="/main_transaction/purchase/edit/${row.id}" class="btn btn-sm btn-primary waves-effect waves-light" title="Edit">
                                            <i class="icon-base ti tabler-edit"></i>
                                        </a>
                                    </div>
                                `;
                            }
                        }
                    ],
                    scrollX: true,
                    responsive: true,
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
                    }
                });
            }
            
            // Initialize Faktur Table
            function initFakturTable() {
                if (fakturTable) {
                    fakturTable.destroy();
                }
                
                fakturTable = new DataTable('#faktur-table', {
                    ajax: {
                        url: `{{ route('main_transaction.purchase.index_data_table') }}`,
                        data: {
                            tab: 'faktur'
                        }
                    },
                    columns: [
                        { data: 'date' },
                        { data: 'reference_no' },
                        { 
                            data: 'supplier',
                            render: function(data, type, row) {
                                return `<a href="#" class="text-primary">${data}</a>`;
                            }
                        },
                        { data: 'due_date' },
                        { 
                            data: 'status',
                            render: function(data, type, row) {
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
                            render: function(data, type, row) {
                                if (type === 'display' && data != null && !isNaN(data)) {
                                    return 'Rp ' + parseFloat(data).toLocaleString('id-ID');
                                }
                                return data ?? '';
                            }
                        },
                        { 
                            data: 'total',
                            className: 'text-end',
                            render: function(data, type, row) {
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
                            render: function(data, type, row, meta) {
                                return `
                                    <div class="d-flex gap-2">
                                        <a href="/main_transaction/purchase/detail/${row.id}" class="btn btn-sm btn-primary waves-effect waves-light" title="Detail">
                                            <i class="icon-base ti tabler-eye"></i>
                                        </a>
                                        <a href="/main_transaction/purchase/edit/${row.id}" class="btn btn-sm btn-primary waves-effect waves-light" title="Edit">
                                            <i class="icon-base ti tabler-edit"></i>
                                        </a>
                                    </div>
                                `;
                            }
                        }
                    ],
                    scrollX: true,
                    responsive: true,
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
                    }
                });
            }
            
            // Initialize first table
            initPesananTable();
            
            // Tab switching
            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                const target = $(e.target).attr('data-bs-target');
                
                if (target === '#pesanan-tab') {
                    initPesananTable();
                } else if (target === '#pengiriman-tab') {
                    initPengirimanTable();
                } else if (target === '#faktur-tab') {
                    initFakturTable();
                }
            });
            
            // Navigation buttons now use direct <a> elements with href - no JavaScript needed
        });
        
        // Action buttons now use direct <a> elements with href - no need for JavaScript functions
    </script>
@endsection