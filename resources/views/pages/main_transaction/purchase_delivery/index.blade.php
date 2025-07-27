@extends('layouts.default')
@section('page_title')
    List Purchase Delivery
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
                                <i class="icon-base ti tabler-truck-delivery icon-28px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0" id="pending-deliveries-count">12</h4>
                    </div>
                    <p class="mb-1">Pengiriman Pending</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6">
            <div class="card card-border-shadow-warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="icon-base ti tabler-truck icon-28px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0" id="in-transit-deliveries-count">8</h4>
                    </div>
                    <p class="mb-1">Sedang Dalam Perjalanan</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6">
            <div class="card card-border-shadow-success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="icon-base ti tabler-package icon-28px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0" id="delivered-count">35</h4>
                    </div>
                    <p class="mb-1">Berhasil Diterima Bulan Ini</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Content Card -->
    <div class="mt-6">
        <div class="card">
            <h4 class="card-header">Kelola Purchase Delivery</h4>
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
                        <input type="text" class="form-control" id="search-field" placeholder="Cari Purchase Delivery">
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
                            @if($canImportPurchaseDelivery)
                                <a href="{{ route('main_transaction.purchase_delivery.import') }}"
                                    class="btn btn-md btn-primary waves-effect waves-light"><span
                                        class="icon-xs icon-base ti tabler-file-upload me-2"></span> Import</a>
                            @endif
                            @if($canExportPurchaseDelivery)
                                <a href="{{ route('main_transaction.purchase_delivery.export') }}"
                                    class="btn btn-md btn-primary waves-effect waves-light"><span
                                        class="icon-xs icon-base ti tabler-file-download me-2"></span> Export</a>
                            @endif
                            @if($canAddPurchaseDelivery)
                                <a href="{{ route('main_transaction.purchase_delivery.add') }}"
                                    class="btn btn-md btn-primary waves-effect waves-light"><span
                                        class="icon-xs icon-base ti tabler-plus me-2"></span> Tambah Data</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Data Table -->
            <div class="card-datatable">
                <table id="purchase-delivery-table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>No Delivery</th>
                            <th>Supplier</th>
                            <th>Tag</th>
                            <th>PO Reference</th>
                            <th>Tgl Pengiriman</th>
                            <th>Status</th>
                            <th>Total Items</th>
                            <th>Items Diterima</th>
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
    
    let purchaseDeliveryTable;
    
    // Permission variables
    const canRead = @json($canViewPurchaseDelivery ?? true);
    const canEdit = @json($canEditPurchaseDelivery ?? true);
    
    // Initialize DataTable
    function initPurchaseDeliveryTable() {
        if (purchaseDeliveryTable) {
            purchaseDeliveryTable.destroy();
        }
        
        purchaseDeliveryTable = new DataTable('#purchase-delivery-table', {
            ajax: {
                url: '{{ route("main_transaction.purchase_delivery.index_data_table") }}',
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
                        const detailUrl = `{{ route('main_transaction.purchase_delivery.detail', ':id') }}`.replace(':id', row.id);
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
                { data: 'delivery_date', responsivePriority: 7 },
                { 
                    data: 'status',
                    responsivePriority: 8,
                    render: function(data, type, row, meta) {
                        let badgeClass = '';
                        switch (data) {
                            case 'PENDING':
                                badgeClass = 'bg-label-secondary';
                                break;
                            case 'IN_TRANSIT':
                                badgeClass = 'bg-label-warning';
                                break;
                            case 'DELIVERED':
                                badgeClass = 'bg-label-success';
                                break;
                            case 'PARTIAL_DELIVERED':
                                badgeClass = 'bg-label-info';
                                break;
                            case 'CANCELLED':
                                badgeClass = 'bg-label-danger';
                                break;
                            default:
                                badgeClass = 'bg-label-primary';
                        }
                        return `<span class="badge ${badgeClass}">${data}</span>`;
                    }
                },
                { 
                    data: 'total_items',
                    className: 'text-center',
                    responsivePriority: 9,
                    render: function(data, type, row, meta) {
                        return data ?? 0;
                    }
                },
                { 
                    data: 'delivered_items',
                    className: 'text-center',
                    responsivePriority: 10,
                    render: function(data, type, row, meta) {
                        return data ?? 0;
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    responsivePriority: 1,
                    width: '120px',
                    render: function(data, type, row, meta) {
                        const editUrl = `{{ route('main_transaction.purchase_delivery.edit', ':id') }}`.replace(':id', row.id);
                        const detailUrl = `{{ route('main_transaction.purchase_delivery.detail', ':id') }}`.replace(':id', row.id);
                        
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
    initPurchaseDeliveryTable();
    
    // Search functionality
    $('#search-field').on('keyup', function() {
        if (purchaseDeliveryTable) {
            purchaseDeliveryTable.search(this.value).draw();
        }
    });
    
    // Filter functionality
    $('#status-filter').on('change', function() {
        if (purchaseDeliveryTable) {
            purchaseDeliveryTable.draw();
        }
    });
    
    // Tag Column Toggle Functionality
    $('#toggleTagColumn').on('change', function() {
        const tagColumn = purchaseDeliveryTable.column(3); // Tag column index (0-based)
        tagColumn.visible(this.checked);
    });
});
</script>
@endsection