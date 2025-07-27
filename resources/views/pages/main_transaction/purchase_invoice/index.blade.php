@extends('layouts.default')
@section('page_title')
    List Purchase Invoice
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
                                <i class="icon-base ti tabler-file-invoice icon-28px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0" id="unpaid-invoices-count">Rp 75,000,000</h4>
                    </div>
                    <p class="mb-1">Invoice Belum Dibayar</p>
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
                        <h4 class="mb-0" id="overdue-invoices-count">Rp 15,000,000</h4>
                    </div>
                    <p class="mb-1">Invoice Overdue</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6">
            <div class="card card-border-shadow-success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="icon-base ti tabler-calendar-check icon-28px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0" id="paid-invoices-count">Rp 120,000,000</h4>
                    </div>
                    <p class="mb-1">Dibayar Bulan Ini</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Content Card -->
    <div class="mt-6">
        <div class="card">
            <h4 class="card-header">Kelola Purchase Invoice</h4>
            <div class="card-body">
                <!-- Advanced Search Form -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Status:</label>
                        <select class="global-select2 form-select" id="status-filter" data-allow-clear="true">
                            <option value="">Semua Status</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status['value'] }}">{{ $status['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Pencarian:</label>
                        <input type="text" class="form-control" id="search-field" placeholder="Cari Purchase Invoice">
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
                            @if($canImportPurchaseInvoice)
                                <a href="{{ route('main_transaction.purchase_invoice.import') }}"
                                    class="btn btn-md btn-primary waves-effect waves-light"><span
                                        class="icon-xs icon-base ti tabler-file-upload me-2"></span> Import</a>
                            @endif
                            @if($canExportPurchaseInvoice)
                                <a href="{{ route('main_transaction.purchase_invoice.export') }}"
                                    class="btn btn-md btn-primary waves-effect waves-light"><span
                                        class="icon-xs icon-base ti tabler-file-download me-2"></span> Export</a>
                            @endif
                            @if($canAddPurchaseInvoice)
                                <a href="{{ route('main_transaction.purchase_invoice.add') }}"
                                    class="btn btn-md btn-primary waves-effect waves-light"><span
                                        class="icon-xs icon-base ti tabler-plus me-2"></span> Tambah Data</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Data Table -->
            <div class="card-datatable">
                <table id="purchase-invoice-table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>No Invoice</th>
                            <th>Supplier</th>
                            <th>Tag</th>
                            <th>PO Reference</th>
                            <th>Tgl Jatuh Tempo</th>
                            <th>Status</th>
                            <th>Dibayar</th>
                            <th>Sisa</th>
                            <th>Total</th>
                            <th>Aksi</th>
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
    // Note: global-select2 initialization is handled globally by layout
    
    let purchaseInvoiceTable;
    
    // Permission variables
    const canRead = @json($canViewPurchaseInvoice ?? true);
    const canEdit = @json($canEditPurchaseInvoice ?? true);
    
    // Initialize DataTable
    function initPurchaseInvoiceTable() {
        if (purchaseInvoiceTable) {
            purchaseInvoiceTable.destroy();
        }
        
        purchaseInvoiceTable = new DataTable('#purchase-invoice-table', {
            ajax: {
                url: '{{ route("main_transaction.purchase_invoice.index_data_table") }}',
                data: function(d) {
                    d.status = $('#status-filter').val();
                }
            },
            columns: [
                { data: 'date', responsivePriority: 2 },
                { 
                    data: 'reference_no',
                    responsivePriority: 3,
                    render: function(data, type, row, meta) {
                        const detailUrl = `{{ route('main_transaction.purchase_invoice.detail', ':id') }}`.replace(':id', row.id);
                        return `<a href="${detailUrl}" class="text-primary fw-semibold">${data}</a>`;
                    }
                },
                { 
                    data: 'supplier',
                    responsivePriority: 4,
                    render: function(data, type, full, meta) {
                        const supplierDetailUrl = "{{ route('master_data.contact.detail', ':id') }}".replace(':id', full.supplier_id);
                        return `<a href="${supplierDetailUrl}" class="text-primary fw-semibold">${data}</a>`;
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
                { data: 'po_reference', responsivePriority: 6 },
                { data: 'due_date', responsivePriority: 7 },
                { 
                    data: 'status',
                    responsivePriority: 8,
                    render: function(data, type, row, meta) {
                        let badgeClass = '';
                        switch (data) {
                            case 'DRAFT':
                                badgeClass = 'bg-label-secondary';
                                break;
                            case 'PENDING_PAYMENT':
                                badgeClass = 'bg-label-warning';
                                break;
                            case 'PAID':
                                badgeClass = 'bg-label-success';
                                break;
                            case 'PARTIAL_PAID':
                                badgeClass = 'bg-label-info';
                                break;
                            case 'OVERDUE':
                                badgeClass = 'bg-label-danger';
                                break;
                            case 'CANCELLED':
                                badgeClass = 'bg-label-dark';
                                break;
                            default:
                                badgeClass = 'bg-label-primary';
                        }
                        return `<span class="badge ${badgeClass}">${data}</span>`;
                    }
                },
                { 
                    data: 'paid_amount',
                    className: 'text-end',
                    responsivePriority: 9,
                    render: function(data, type, row, meta) {
                        if (type === 'display' && data != null && !isNaN(data)) {
                            return 'Rp ' + parseFloat(data).toLocaleString('id-ID');
                        }
                        return data ?? 'Rp 0';
                    }
                },
                { 
                    data: 'remaining_amount',
                    className: 'text-end',
                    responsivePriority: 10,
                    render: function(data, type, row, meta) {
                        if (type === 'display' && data != null && !isNaN(data)) {
                            return 'Rp ' + parseFloat(data).toLocaleString('id-ID');
                        }
                        return data ?? 'Rp 0';
                    }
                },
                { 
                    data: 'invoice_amount',
                    className: 'text-end',
                    responsivePriority: 11,
                    render: function(data, type, row, meta) {
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
                    responsivePriority: 1,
                    width: '120px',
                    render: function(data, type, row, meta) {
                        const editUrl = `{{ route('main_transaction.purchase_invoice.edit', ':id') }}`.replace(':id', row.id);
                        const detailUrl = `{{ route('main_transaction.purchase_invoice.detail', ':id') }}`.replace(':id', row.id);
                        
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
            scrollX: true,
            responsive: true,
            orderCellsTop: true,
            ordering: false
        });
    }
    
    // Initialize table
    initPurchaseInvoiceTable();
    
    // Search functionality
    $('#search-field').on('keyup', function() {
        if (purchaseInvoiceTable) {
            purchaseInvoiceTable.search(this.value).draw();
        }
    });
    
    // Filter functionality
    $('#status-filter').on('change', function() {
        if (purchaseInvoiceTable) {
            purchaseInvoiceTable.draw();
        }
    });
    
    // Tag Column Toggle Functionality
    $('#toggleTagColumn').on('change', function() {
        const tagColumn = purchaseInvoiceTable.column(3); // Tag column index (0-based)
        tagColumn.visible(this.checked);
    });
});
</script>
@endsection