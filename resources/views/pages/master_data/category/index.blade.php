@extends('layouts.default')
@section('page_title')
    Kategori Produk
@stop
@section('content')
    <!-- Summary Cards -->
    <div class="row">
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-primary"><i
                                    class="icon-base ti tabler-category icon-28px"></i></span>
                        </div>
                        <h4 class="mb-0">6</h4>
                    </div>
                    <p class="mb-1">Total Kategori</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-success"><i
                                    class="icon-base ti tabler-check icon-28px"></i></span>
                        </div>
                        <h4 class="mb-0">5</h4>
                    </div>
                    <p class="mb-1">Kategori Aktif</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-warning"><i
                                    class="icon-base ti tabler-package icon-28px"></i></span>
                        </div>
                        <h4 class="mb-0">68</h4>
                    </div>
                    <p class="mb-1">Total Produk</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-info h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-info"><i
                                    class="icon-base ti tabler-layers-intersect icon-28px"></i></span>
                        </div>
                        <h4 class="mb-0">4</h4>
                    </div>
                    <p class="mb-1">Tipe Kategori</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="mt-6">
        <div class="card">
            <h4 class="card-header">Kelola Kategori Produk</h4>
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
                                        @foreach($statuses as $status)
                                            <option value="{{ $status['value'] }}">{{ $status['label'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-sm-6 col-lg-4">
                                    <label class="form-label">Tipe Kategori:</label>
                                    <select class="global-select2 form-select" id="type-filter" data-allow-clear="false">
                                        <option value="">Semua Tipe</option>
                                        @foreach($categoryTypes as $type)
                                            <option value="{{ $type['value'] }}">{{ $type['label'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-sm-6 col-lg-4">
                                    <label class="form-label">Pencarian:</label>
                                    <input type="text" class="form-control" id="search" placeholder="Cari Kategori">
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
                            @if($canShowImportButton)
                                <a href="{{ route('master_data.category.import') }}"
                                    class="btn btn-md btn-primary waves-effect waves-light"><span
                                        class="icon-xs icon-base ti tabler-file-upload me-2"></span> Import</a>
                            @endif
                            @if($canShowExportButton)
                                <a href="{{ route('master_data.category.export') }}"
                                    class="btn btn-md btn-primary waves-effect waves-light"><span
                                        class="icon-xs icon-base ti tabler-file-download me-2"></span> Export</a>
                            @endif
                            @if($canShowAddButton)
                                <a href="{{ route('master_data.category.add') }}"
                                    class="btn btn-md btn-primary waves-effect waves-light"><span
                                        class="icon-xs icon-base ti tabler-plus me-2"></span> Tambah Data</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Data Table -->
            <div class="card-datatable">
                <table id="main-table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Kode Kategori</th>
                            <th>Nama Kategori</th>
                            <th>Tipe</th>
                            <th>Jumlah Produk</th>
                            <th>Status</th>
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
            // Permission variables
            const canRead = @json($canRead);
            const canWrite = @json($canWrite);
            const canDelete = @json($canDelete);
            const canShowEditButton = @json($canShowEditButton);
            const canShowDeleteButton = @json($canShowDeleteButton);
            
            // Initialize DataTable
            const mainTable = document.querySelector('#main-table');
            let dt_adv_filter;

            if (mainTable) {
                dt_adv_filter = new DataTable(mainTable, {
                    processing: true,
                    serverSide: true,
                    ordering: false,
                    ajax: {
                        url: `{{ route('master_data.category.index_data_table') }}`,
                        data: function(d) {
                            d.status_filter = $('#status-filter').val();
                            d.category_type_filter = $('#type-filter').val();
                            return d;
                        }
                    },
                    columns: [
                        { data: 'created_at', responsivePriority: 2 },
                        { 
                            data: 'category_code',
                            responsivePriority: 3
                        },
                        { data: 'category_name', responsivePriority: 4 },
                        { 
                            data: 'category_type',
                            responsivePriority: 5,
                            render: function(data, type, row) {
                                const typeMapping = {
                                    'FINISHED_GOODS': 'Produk Jadi',
                                    'RAW_MATERIALS': 'Bahan Baku',
                                    'PACKAGING': 'Kemasan',
                                    'MAINTENANCE': 'Perawatan'
                                };
                                return typeMapping[data] || data;
                            }
                        },
                        { 
                            data: 'product_count',
                            responsivePriority: 6,
                            className: 'text-end',
                            render: function(data, type, row) {
                                return `${data}`;
                            }
                        },
                        { 
                            data: 'status',
                            responsivePriority: 5,
                            render: function(data, type, row) {
                                const badgeClass = data === 'ACTIVE' ? 'bg-label-success' : 'bg-label-secondary';
                                const statusText = data === 'ACTIVE' ? 'ACTIVE' : 'INACTIVE';
                                return `<span class="badge ${badgeClass}">${statusText}</span>`;
                            }
                        },
                        {
                            data: null,
                            orderable: false,
                            searchable: false,
                            responsivePriority: 1,
                            width: '120px',
                            render: function(data, type, row, meta) {
                                const detailUrl = `{{ route('master_data.category.detail', ':id') }}`.replace(':id', row.id);
                                const editUrl = `{{ route('master_data.category.edit', ':id') }}`.replace(':id', row.id);
                                let actions = '<div class="d-flex gap-2">';
                                
                                if (canRead) {
                                    actions += `<a href="${detailUrl}" class="btn btn-sm btn-primary waves-effect waves-light" title="Detail">
                                        <i class="icon-xs icon-base ti tabler-eye"></i>
                                    </a>`;
                                }
                                
                                if (canShowEditButton) {
                                    actions += `<a href="${editUrl}" class="btn btn-sm btn-primary waves-effect waves-light" title="Edit">
                                        <i class="icon-xs icon-base ti tabler-edit"></i>
                                    </a>`;
                                }
                                
                                if (canShowDeleteButton) {
                                    actions += `<button class="btn btn-sm btn-danger waves-effect waves-light" title="Hapus" onclick="deleteItem(${row.id})">
                                        <i class="icon-xs icon-base ti tabler-trash"></i>
                                    </button>`;
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
                    orderCellsTop: true
                });
            }

            // Search functionality
            $('#search').on('keyup', function() {
                if (dt_adv_filter) {
                    dt_adv_filter.search(this.value).draw();
                }
            });
            
            // Filter functionality
            $('#status-filter, #type-filter').on('change', function() {
                if (dt_adv_filter) {
                    dt_adv_filter.draw();
                }
            });
        });
    </script>
@endsection