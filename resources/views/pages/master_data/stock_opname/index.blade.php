@extends('layouts.default')
@section('page_title')
    Stock Opname Management
@stop
@section('content')
    <!-- Summary Cards -->
    <div class="row">
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="icon-base ti tabler-clipboard-check icon-28px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0">6 Total Opname</h4>
                    </div>
                    <p class="mb-1">Stock Opname</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="icon-base ti tabler-clock icon-28px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0">2 Sedang Proses</h4>
                    </div>
                    <p class="mb-1">Sedang Dikerjakan</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="icon-base ti tabler-check icon-28px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0">3 Selesai</h4>
                    </div>
                    <p class="mb-1">Opname Selesai</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-danger h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-danger">
                                <i class="icon-base ti tabler-adjustments icon-28px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0">1 Perlu Adjustment</h4>
                    </div>
                    <p class="mb-1">Menunggu Adjustment</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="mt-6">
        <div class="card">
            <h5 class="card-header">Stock Opname Management</h5>
            <div class="card-body">
                <!-- Advanced Search Form -->
                <form class="dt_adv_search" method="GET">
                    <div class="row">
                        <div class="col-12">
                            <div class="row g-3">
                                <div class="col-12 col-sm-6 col-lg-4">
                                    <label class="form-label">Status:</label>
                                    <select class="global-select2 form-select" id="status-filter" data-allow-clear="false">
                                        <option value="">Semua Status</option>
                                        @foreach($stockOpnameStatuses as $status)
                                            <option value="{{ $status['value'] }}">{{ $status['label'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-sm-6 col-lg-4">
                                    <label class="form-label">Gudang:</label>
                                    <select class="global-select2 form-select" id="warehouse-filter" data-allow-clear="false">
                                        <option value="">Semua Gudang</option>
                                        @foreach($warehouseLocations as $warehouse)
                                            <option value="{{ $warehouse['value'] }}">{{ $warehouse['label'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-sm-6 col-lg-4">
                                    <label class="form-label">Pencarian:</label>
                                    <input type="text" class="form-control" id="search-field" placeholder="Cari Stock Opname">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <hr class="my-6 mx-n6" />

                <!-- Action Buttons -->
                <div class="mt-4">
                    <div class="d-flex justify-content-end">
                        <div class="d-flex gap-2">
                            <button type="button" id="btn-export" class="btn btn-md btn-primary waves-effect waves-light">
                                <span class="icon-xs icon-base ti tabler-file-download me-2"></span> Export
                            </button>
                            <button type="button" id="btn-import" class="btn btn-md btn-primary waves-effect waves-light">
                                <span class="icon-xs icon-base ti tabler-file-upload me-2"></span> Import
                            </button>
                            <button type="button" id="btn-add-stock-opname" class="btn btn-md btn-primary waves-effect waves-light">
                                <span class="icon-xs icon-base ti tabler-plus me-2"></span> Tambah Stock Opname
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Data Table -->
            <div class="card-datatable">
                <table id="stock-opname-table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No. Opname</th>
                            <th>Tanggal</th>
                            <th>Gudang</th>
                            <th>PIC</th>
                            <th>Total Produk</th>
                            <th>Terhitung</th>
                            <th>Selisih Qty</th>
                            <th>Selisih Nilai</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('js_script')
<script>
$(function() {
    //DataTable
    const stockOpnameTable = document.querySelector('#stock-opname-table');
    let dt_adv_filter;

    if (stockOpnameTable) {
        dt_adv_filter = new DataTable(stockOpnameTable, {
            ajax: `{{ route('master_data.stock_opname.index_data_table') }}`,
            columns: [
                { data: 'opname_number' },
                { data: 'opname_date' },
                { data: 'warehouse_name' },
                { data: 'pic_name' },
                { data: 'total_products' },
                { data: 'counted_products' },
                { data: 'variance_qty' },
                { data: 'variance_value' },
                { data: 'status' },
                { data: null }
            ],
            columnDefs: [
                {
                    targets: 1,
                    render: function(data, type, full, meta) {
                        return new Date(data).toLocaleDateString('id-ID');
                    }
                },
                {
                    targets: 6,
                    render: function(data, type, full, meta) {
                        if (data > 0) {
                            return `<span class="text-success">+${data}</span>`;
                        } else if (data < 0) {
                            return `<span class="text-danger">${data}</span>`;
                        }
                        return data;
                    }
                },
                {
                    targets: 7,
                    render: function(data, type, full, meta) {
                        const formatted = new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR'
                        }).format(data);
                        
                        if (data > 0) {
                            return `<span class="text-success">+${formatted}</span>`;
                        } else if (data < 0) {
                            return `<span class="text-danger">${formatted}</span>`;
                        }
                        return formatted;
                    }
                },
                {
                    targets: 8,
                    render: function(data, type, full, meta) {
                        const statusMap = {
                            'draft': '<span class="badge bg-secondary">Draft</span>',
                            'in_progress': '<span class="badge bg-warning">Sedang Dikerjakan</span>',
                            'completed': '<span class="badge bg-success">Selesai</span>',
                            'adjustment_pending': '<span class="badge bg-info">Menunggu Adjustment</span>',
                            'adjustment_created': '<span class="badge bg-primary">Adjustment Dibuat</span>',
                            'cancelled': '<span class="badge bg-danger">Dibatalkan</span>'
                        };
                        return statusMap[data] || data;
                    }
                },
                {
                    targets: -1,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row, meta) {
                        const editUrl = "{{ route('master_data.stock_opname.edit', ':id') }}".replace(':id', row.id);
                        const detailUrl = "{{ route('master_data.stock_opname.detail', ':id') }}".replace(':id', row.id);
                        
                        let buttons = `
                        <div class="btn-group" role="group" aria-label="Action buttons">
                            <a href="${editUrl}" class="btn btn-primary btn-sm waves-effect">
                                <i class="icon-base ti tabler-edit"></i>
                            </a>
                            <a href="${detailUrl}" class="btn btn-primary btn-sm waves-effect">
                                <i class="icon-base ti tabler-eye"></i>
                            </a>`;
                        
                        // Show adjustment button only for adjustment_pending status
                        if (row.status === 'adjustment_pending') {
                            buttons += `
                            <button class="btn btn-success btn-sm waves-effect" onclick="createAdjustment(${row.id})" title="Buat Stok Adjustment">
                                <i class="icon-base ti tabler-adjustments"></i>
                            </button>`;
                        }
                        
                        buttons += `</div>`;
                        
                        return buttons;
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
    
    // Button click events
    $('#btn-add-stock-opname').on('click', function() {
        window.location.href = "{{ route('master_data.stock_opname.add') }}";
    });
    
    $('#btn-export').on('click', function() {
        window.location.href = "{{ route('master_data.stock_opname.export') }}";
    });
    
    $('#btn-import').on('click', function() {
        window.location.href = "{{ route('master_data.stock_opname.import') }}";
    });
    
    // Create adjustment function
    window.createAdjustment = function(id) {
        Swal.fire({
            title: 'Buat Stok Adjustment?',
            text: 'Apakah Anda yakin ingin membuat stok adjustment dari hasil opname ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Buat Adjustment',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("master_data.stock_opname.create_adjustment", ":id") }}'.replace(':id', id),
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: response.message + ' dengan nomor ' + response.adjustment_number,
                                icon: 'success',
                                customClass: {
                                    confirmButton: 'btn btn-primary'
                                },
                                buttonsStyling: false
                            }).then(() => {
                                stockOpnameTable.draw();
                            });
                        } else {
                            Swal.fire({
                                title: 'Gagal!',
                                text: response.message || 'Terjadi kesalahan saat membuat adjustment',
                                icon: 'error',
                                customClass: {
                                    confirmButton: 'btn btn-primary'
                                },
                                buttonsStyling: false
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat membuat adjustment',
                            icon: 'error',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        });
                    }
                });
            }
        });
    };
});
</script>
@endsection