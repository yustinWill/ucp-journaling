@extends('layouts.default')
@section('page_title')
    List Purchase Order
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
                                <i class="icon-base ti tabler-shopping-cart icon-28px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0" id="pending-orders-count">Rp 125,000,000</h4>
                    </div>
                    <p class="mb-1">Total PO Pending</p>
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
                        <h4 class="mb-0" id="overdue-orders-count">15</h4>
                    </div>
                    <p class="mb-1">PO Melewati Deadline</p>
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
                        <h4 class="mb-0" id="completed-orders-count">28</h4>
                    </div>
                    <p class="mb-1">PO Selesai Bulan Ini</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Content Card -->
    <div class="mt-6">
        <div class="card">
            <h4 class="card-header">Kelola Purchase Order</h4>
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
                        <input type="text" class="form-control" id="search-field" placeholder="Cari Purchase Order">
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
                            @if($canImportPurchaseOrder)
                                <a href="{{ route('main_transaction.purchase_order.import') }}"
                                    class="btn btn-md btn-primary waves-effect waves-light"><span
                                        class="icon-xs icon-base ti tabler-file-upload me-2"></span> Import</a>
                            @endif
                            @if($canExportPurchaseOrder)
                                <a href="{{ route('main_transaction.purchase_order.export') }}"
                                    class="btn btn-md btn-primary waves-effect waves-light"><span
                                        class="icon-xs icon-base ti tabler-file-download me-2"></span> Export</a>
                            @endif
                            @if($canAddPurchaseOrder)
                                <a href="{{ route('main_transaction.purchase_order.add') }}"
                                    class="btn btn-md btn-primary waves-effect waves-light"><span
                                        class="icon-xs icon-base ti tabler-plus me-2"></span> Tambah Data</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Data Table -->
            <div class="card-datatable">
                <table id="purchase-order-table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 100px;">Tanggal</th>
                            <th style="width: 140px;">No Purchase Order</th>
                            <th style="width: 180px;">Supplier</th>
                            <th style="width: 120px;">Tgl Jatuh Tempo</th>
                            <th style="width: 120px;">Total</th>
                            <th style="width: 100px;">Status</th>
                            <th style="width: 120px;">Diinput Oleh</th>
                            <th style="width: 120px;">Tag</th>
                            <th style="width: 80px;">Aksi</th>
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
    
    let purchaseOrderTable;
    
    // Permission variables
    const canRead = @json($canViewPurchaseOrder ?? true);
    const canEdit = @json($canEditPurchaseOrder ?? true);
    
    // Initialize DataTable
    function initPurchaseOrderTable() {
        if (purchaseOrderTable) {
            purchaseOrderTable.destroy();
        }
        
        purchaseOrderTable = new DataTable('#purchase-order-table', {
            ajax: {
                url: '{{ route("main_transaction.purchase_order.index_data_table") }}',
                data: function(d) {
                    d.status = $('#status-filter').val();
                }
            },
            columns: [
                { data: 'date', width: '100px' },
                { 
                    data: 'reference_no',
                    width: '140px',
                    render: function(data, type, row) {
                        return data; // Non-clickable as per standard
                    }
                },
                { 
                    data: 'supplier',
                    width: '180px',
                    render: function(data, type, row) {
                        if (!data) return '-';
                        if (canRead) {
                            const detailUrl = `{{ route('master_data.contact.detail', ':id') }}`
                                .replace(':id', row.supplier_id);
                            return `<a href="${detailUrl}" class="text-primary fw-semibold">${data}</a>`;
                        }
                        return data;
                    }
                },
                { data: 'due_date', width: '120px' },
                { 
                    data: 'total',
                    width: '120px',
                    className: 'text-end',
                    render: function(data, type, row) {
                        if (type === 'display' && data != null && !isNaN(data)) {
                            return 'Rp ' + parseFloat(data).toLocaleString('id-ID');
                        }
                        return data ?? '';
                    }
                },
                { 
                    data: 'status',
                    width: '100px',
                    render: function(data, type, row) {
                        return `<span class="badge ${row.status_badge_class}">${data}</span>`;
                    }
                },
                { 
                    data: 'created_by',
                    width: '120px',
                    render: function(data, type, row) {
                        if (canRead) {
                            const detailUrl = `{{ route('master_data.user.detail', ':id') }}`
                                .replace(':id', row.created_by_id);
                            return `<a href="${detailUrl}" class="text-primary fw-semibold">${data}</a>`;
                        }
                        return data;
                    }
                },
                { 
                    data: 'tags',
                    width: '120px',
                    render: function(data, type, row) {
                        if (!data || data.length === 0) {
                            return '-';
                        }
                        let tagsHtml = '';
                        data.forEach(function(tag) {
                            let badgeClass = tag.preview || 'bg-label-secondary';
                            tagsHtml += `<span class="badge me-1 ${badgeClass}">${tag.name}</span>`;
                        });
                        return tagsHtml;
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    responsivePriority: 1,
                    width: '80px',
                    render: function(data, type, row, meta) {
                        const detailUrl = `{{ route('main_transaction.purchase_order.detail', ':id') }}`.replace(':id', row.id);
                        const editUrl = `{{ route('main_transaction.purchase_order.edit', ':id') }}`.replace(':id', row.id);
                        let actions = '<div class="d-flex gap-2">';
                        
                        if (canRead) {
                            actions += `<a href="${detailUrl}" class="btn btn-sm btn-primary waves-effect waves-light" title="Detail">
                                <i class="icon-xs icon-base ti tabler-eye"></i>
                            </a>`;
                        }
                        
                        if (canEdit) {
                            actions += `<a href="${editUrl}" class="btn btn-sm btn-primary waves-effect waves-light" title="Edit">
                                <i class="icon-xs icon-base ti tabler-edit"></i>
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
    initPurchaseOrderTable();
    
    // Search functionality
    $('#search-field').on('keyup', function() {
        if (purchaseOrderTable) {
            purchaseOrderTable.search(this.value).draw();
        }
    });
    
    // Filter functionality
    $('#status-filter').on('change', function() {
        if (purchaseOrderTable) {
            purchaseOrderTable.draw();
        }
    });
    
    // Tag Column Toggle Functionality
    $('#toggleTagColumn').on('change', function() {
        const tagColumn = purchaseOrderTable.column(7); // Tag column index (0-based)
        tagColumn.visible(this.checked);
    });
});

// Action button functions
function viewDetail(id) {
    // Navigate to purchase order detail page
    window.location.href = "{{ route('main_transaction.purchase_order.detail', ':id') }}".replace(':id', id);
}

function editRecord(id) {
    // Navigate to purchase order edit page
    window.location.href = "{{ route('main_transaction.purchase_order.edit', ':id') }}".replace(':id', id);
}
</script>
@endsection