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
        <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#pesanan-tab" type="button"
                role="tab" aria-controls="pesanan-tab" aria-selected="true">
                <i class="icon-base ti tabler-shopping-cart me-2"></i>
                Pesanan
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#pengiriman-tab" type="button"
                role="tab" aria-controls="pengiriman-tab" aria-selected="false">
                <i class="icon-base ti tabler-truck-delivery me-2"></i>
                Pengiriman
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#faktur-tab" type="button"
                role="tab" aria-controls="faktur-tab" aria-selected="false">
                <i class="icon-base ti tabler-file-invoice me-2"></i>
                Faktur
            </button>
        </li>
        </ul>
        
        <div class="tab-content p-0 mt-3">
            <!-- Pesanan Tab -->
            <div class="tab-pane fade show active" id="pesanan-tab" role="tabpanel">
                <div class="card">
                    <h5 class="card-header">Pesanan Pembelian</h5>
                    <div class="card-body">
                        <!-- Advanced Search Form -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Status:</label>
                                <select class="form-select" id="pesanan-status-filter">
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
                                    <button type="button" id="btn-import-pesanan"
                                        class="btn btn-md btn-primary waves-effect waves-light"><span
                                            class="icon-xs icon-base ti tabler-file-upload me-2"></span> Import</button>
                                    <button type="button" id="btn-export-pesanan"
                                        class="btn btn-md btn-primary waves-effect waves-light"><span
                                            class="icon-xs icon-base ti tabler-file-download me-2"></span> Export</button>
                                    <button type="button" id="btn-add-pesanan"
                                        class="btn btn-md btn-primary waves-effect waves-light"><span
                                            class="icon-xs icon-base ti tabler-plus me-2"></span> Tambah Data</button>
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
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Pengiriman Tab -->
            <div class="tab-pane fade" id="pengiriman-tab" role="tabpanel">
                <div class="card">
                    <h5 class="card-header">Pengiriman Pembelian</h5>
                    <div class="card-body">
                        <!-- Advanced Search Form -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Status:</label>
                                <select class="form-select" id="pengiriman-status-filter">
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
                                    <button type="button" id="btn-import-pengiriman"
                                        class="btn btn-md btn-primary waves-effect waves-light"><span
                                            class="icon-xs icon-base ti tabler-file-upload me-2"></span> Import</button>
                                    <button type="button" id="btn-export-pengiriman"
                                        class="btn btn-md btn-primary waves-effect waves-light"><span
                                            class="icon-xs icon-base ti tabler-file-download me-2"></span> Export</button>
                                    <button type="button" id="btn-add-pengiriman"
                                        class="btn btn-md btn-primary waves-effect waves-light"><span
                                            class="icon-xs icon-base ti tabler-plus me-2"></span> Tambah Data</button>
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
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Faktur Tab -->
            <div class="tab-pane fade" id="faktur-tab" role="tabpanel">
                <div class="card">
                    <h5 class="card-header">Faktur Pembelian</h5>
                    <div class="card-body">
                        <!-- Advanced Search Form -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Status:</label>
                                <select class="form-select" id="faktur-status-filter">
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
                                    <button type="button" id="btn-import-faktur"
                                        class="btn btn-md btn-primary waves-effect waves-light"><span
                                            class="icon-xs icon-base ti tabler-file-upload me-2"></span> Import</button>
                                    <button type="button" id="btn-export-faktur"
                                        class="btn btn-md btn-primary waves-effect waves-light"><span
                                            class="icon-xs icon-base ti tabler-file-download me-2"></span> Export</button>
                                    <button type="button" id="btn-add-faktur"
                                        class="btn btn-md btn-primary waves-effect waves-light"><span
                                            class="icon-xs icon-base ti tabler-plus me-2"></span> Tambah Data</button>
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
                        url: '{{ route('main_transaction.purchase.index_data_table') }}',
                        data: {
                            tab: 'pesanan'
                        }
                    },
                    columns: [
                        { data: 'date' },
                        { 
                            data: 'reference_no',
                            render: function(data, type, row) {
                                return `<a href="#" class="text-primary">${data}</a>`;
                            }
                        },
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
                        url: '{{ route('main_transaction.purchase.index_data_table') }}',
                        data: {
                            tab: 'pengiriman'
                        }
                    },
                    columns: [
                        { data: 'date' },
                        { 
                            data: 'reference_no',
                            render: function(data, type, row) {
                                return `<a href="#" class="text-primary">${data}</a>`;
                            }
                        },
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
                        url: '{{ route('main_transaction.purchase.index_data_table') }}',
                        data: {
                            tab: 'faktur'
                        }
                    },
                    columns: [
                        { data: 'date' },
                        { 
                            data: 'reference_no',
                            render: function(data, type, row) {
                                return `<a href="#" class="text-primary">${data}</a>`;
                            }
                        },
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
            
            // Button click events - separated by purchase type
            $('#btn-add-pesanan').on('click', function() {
                window.location.href = "{{ route('main_transaction.purchase.add.po') }}";
            });
            
            $('#btn-add-pengiriman').on('click', function() {
                window.location.href = "{{ route('main_transaction.purchase.add.pd') }}";
            });
            
            $('#btn-add-faktur').on('click', function() {
                window.location.href = "{{ route('main_transaction.purchase.add.pi') }}";
            });
            
            // Import button events - with type parameter
            $('#btn-import-pesanan').on('click', function() {
                // Create file input for import
                const fileInput = document.createElement('input');
                fileInput.type = 'file';
                fileInput.accept = '.xlsx,.xls,.csv';
                fileInput.onchange = function(e) {
                    const formData = new FormData();
                    formData.append('file', e.target.files[0]);
                    formData.append('type', 'pesanan');
                    
                    // TODO: Implement actual import functionality
                    console.log('Import Purchase Order file:', e.target.files[0].name);
                    // fetch('{{ route("main_transaction.purchase.import") }}', {
                    //     method: 'POST',
                    //     body: formData,
                    //     headers: {
                    //         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    //     }
                    // });
                };
                fileInput.click();
            });
            
            $('#btn-import-pengiriman').on('click', function() {
                // Create file input for import
                const fileInput = document.createElement('input');
                fileInput.type = 'file';
                fileInput.accept = '.xlsx,.xls,.csv';
                fileInput.onchange = function(e) {
                    const formData = new FormData();
                    formData.append('file', e.target.files[0]);
                    formData.append('type', 'pengiriman');
                    
                    // TODO: Implement actual import functionality
                    console.log('Import Purchase Delivery file:', e.target.files[0].name);
                    // fetch('{{ route("main_transaction.purchase.import") }}', {
                    //     method: 'POST',
                    //     body: formData,
                    //     headers: {
                    //         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    //     }
                    // });
                };
                fileInput.click();
            });
            
            $('#btn-import-faktur').on('click', function() {
                // Create file input for import
                const fileInput = document.createElement('input');
                fileInput.type = 'file';
                fileInput.accept = '.xlsx,.xls,.csv';
                fileInput.onchange = function(e) {
                    const formData = new FormData();
                    formData.append('file', e.target.files[0]);
                    formData.append('type', 'faktur');
                    
                    // TODO: Implement actual import functionality
                    console.log('Import Purchase Invoice file:', e.target.files[0].name);
                    // fetch('{{ route("main_transaction.purchase.import") }}', {
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
            $('#btn-export-pesanan').on('click', function() {
                // Handle export for Purchase Order
                window.location.href = "{{ route('main_transaction.purchase.export') }}?type=pesanan";
            });
            
            $('#btn-export-pengiriman').on('click', function() {
                // Handle export for Purchase Delivery
                window.location.href = "{{ route('main_transaction.purchase.export') }}?type=pengiriman";
            });
            
            $('#btn-export-faktur').on('click', function() {
                // Handle export for Purchase Invoice
                window.location.href = "{{ route('main_transaction.purchase.export') }}?type=faktur";
            });
        });
    </script>
@endsection