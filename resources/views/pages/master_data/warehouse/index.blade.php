@extends('layouts.default')
@section('page_title')
    List Warehouse
@stop
@section('content')
    <!-- Summary Cards -->
    {{-- <div class="row">
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-primary"><i
                                    class="icon-base ti tabler-box icon-28px"></i></span>
                        </div>
                        <h4 class="mb-0">8</h4>
                    </div>
                    <p class="mb-1">Total Warehouse Aktif</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-primary"><i
                                    class="icon-base ti tabler-package-import icon-28px"></i></span>
                        </div>
                        <h4 class="mb-0">2</h4>
                    </div>
                    <p class="mb-1">Warehouse Maintenance</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-primary"><i
                                    class="icon-base ti tabler-box-off icon-28px"></i></span>
                        </div>
                        <h4 class="mb-0">1,247,890</h4>
                    </div>
                    <p class="mb-1">Total Kapasitas (Unit)</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-primary"><i
                                    class="icon-base ti tabler-building-warehouse icon-28px"></i></span>
                        </div>
                        <h4 class="mb-0">982,456</h4>
                    </div>
                    <p class="mb-1">Stok Saat Ini (Unit)</p>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Main Content Card -->
    <div class="card mt-6">
        <h4 class="card-header">Kelola Warehouse</h4>
        <div class="card-body">
            <!-- Advanced Search Form -->
            <form class="dt_adv_search" method="GET" autocomplete="off">
                <div class="row">
                    <div class="col-12">
                        <div class="row g-3">
                            <div class="col-12 col-sm-6 col-lg-4">
                                <label class="form-label">Status:</label>
                                <select class="global-select2 form-select" id="warehouse-status-filter"
                                    data-allow-clear="true">
                                    <option value="">Semua Status</option>
                                    @foreach ($warehouseStatuses as $status)
                                        <option value="{{ $status['value'] }}">{{ $status['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <label class="form-label">Tag:</label>
                                <select class="global-select2 form-select" id="warehouse-tag-filter"
                                    data-allow-clear="true">
                                    <option value="">Semua Tag</option>
                                    @foreach ($availableTags as $tag)
                                        <option value="{{ $tag->code }}">{{ $tag->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <label class="form-label">Pencarian:</label>
                                <input type="text" class="form-control" id="warehouse-search"
                                    placeholder="Cari Warehouse">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
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
                        {{-- <a href="{{ route('master_data.warehouse.import') }}"
                            class="btn btn-md btn-primary waves-effect waves-light"><span
                                class="icon-xs icon-base ti tabler-file-upload me-2"></span> Import</a>
                        <a href="{{ route('master_data.warehouse.export') }}"
                            class="btn btn-md btn-primary waves-effect waves-light"><span
                                class="icon-xs icon-base ti tabler-file-download me-2"></span> Export</a> --}}
                        @can('warehouse.stock_transfer')
                        <a href="{{ route('master_data.warehouse.stock_transfer') }}"
                            class="btn btn-md btn-primary waves-effect waves-light"><span
                                class="icon-xs icon-base ti tabler-transfer me-2"></span> Stock Transfer</a>
                        @endcan
                        @can('warehouse.create')
                            <a href="{{ route('master_data.warehouse.add') }}"
                                class="btn btn-md btn-primary waves-effect waves-light"><span
                                    class="icon-xs icon-base ti tabler-plus me-2"></span> Tambah Data</a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
        <!-- Data Table -->
        <div class="card-datatable">
            <table id="warehouse-table" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Kode Warehouse</th>
                        <th>Nama Warehouse</th>
                        <th>Penanggung Jawab</th>
                        <th>Alamat</th>
                        <th>Status</th>
                        <th>Tag</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@stop
@section('js_script')
    <script>
        $(function() {
            // Permission variables
            const canView = @json(auth()->user()->can('warehouse.view'));
            const canEdit = @json(auth()->user()->can('warehouse.edit'));

            let warehouseTable;

            warehouseTable = new DataTable('#warehouse-table', {
                processing: true,
                serverSide: true,
                ajax: {
                    url: `{{ route('master_data.warehouse.index_data_table') }}`,
                    data: function(d) {
                        d.status_filter = $('#warehouse-status-filter').val();
                        d.tag_filter = $('#warehouse-tag-filter').val();
                        d.search = $('#warehouse-search').val();
                        return d;
                    }
                },
                columns: [{
                        data: 'code',
                        responsivePriority: 2
                    },
                    {
                        data: 'name',
                        responsivePriority: 3
                    },
                    {
                        data: 'pic_names',
                        responsivePriority: 4
                    },
                    {
                        data: 'address',
                        responsivePriority: 5
                    },
                    {
                        data: 'status',
                        responsivePriority: 6,
                        render: function(data, type, row) {
                            const badgeClass = data === 'ACTIVE' ? 'bg-label-success' :
                                (data === 'MAINTENANCE' ? 'bg-label-warning' :
                                'bg-label-secondary');
                            return `<span class="badge ${badgeClass}">${data}</span>`;
                        }
                    },
                    {
                        data: 'tags',
                        responsivePriority: 7,
                        render: function(data, type, row) {
                            return data.map(tag => {
                                if (tag.name == null) {
                                    return ``;
                                }
                                return `<span class="badge ${tag.preview} me-1">${tag.name}</span>`;
                            }).join('');
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        responsivePriority: 1,
                        width: '120px',
                        render: function(data, type, row, meta) {
                            const detailUrl = `{{ route('master_data.warehouse.detail', ':id') }}`
                                .replace(':id', row.id);
                            const editUrl = `{{ route('master_data.warehouse.edit', ':id') }}`
                                .replace(':id', row.id);
                            let actions = '<div class="d-flex gap-2">';

                            if (canView) {
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
                // scrollX: true,
                responsive: true
            });

            // Tag Column Toggle Functionality
            $('#toggleTagColumn').on('change', function() {
                if (warehouseTable) {
                    const tagColumn = warehouseTable.column(5); // Tag column index (0-based)
                    tagColumn.visible(this.checked);
                }
            });

            // Initialize tag column visibility based on checkbox state
            const initialTagVisibility = $('#toggleTagColumn').is(':checked');
            if (warehouseTable) {
                warehouseTable.column(5).visible(initialTagVisibility);
            }

            let searchTimeout;
            $('#warehouse-status-filter, #warehouse-tag-filter, #warehouse-search').on('keyup change',
                function() {
                    clearTimeout(searchTimeout);
                    const input = this;
                    searchTimeout = setTimeout(function() {
                        if (warehouseTable) {
                            warehouseTable.search(input.value).draw();
                        }
                    }, 400); // 400ms delay
                });
        });
    </script>
@endsection
