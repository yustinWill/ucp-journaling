@extends('layouts.default')
@section('page_title')
    List Gudang
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
                                    class="icon-base ti tabler-box icon-28px"></i></span>
                        </div>
                        <h4 class="mb-0">8</h4>
                    </div>
                    <p class="mb-1">Total Gudang Aktif</p>
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
                    <p class="mb-1">Gudang Maintenance</p>
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
    </div>

    <!-- Main Content Card -->
    <div class="card mt-6">
        <h4 class="card-header">Kelola Gudang</h4>
        <div class="card-body">
            <!-- Advanced Search Form -->
            <form class="dt_adv_search" method="GET">
                <div class="row">
                    <div class="col-12">
                        <div class="row g-3">
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label">Kode Gudang:</label>
                                <input type="text" class="form-control dt-input dt-full-name" data-column="1"
                                    placeholder="Cari Berdasarkan Kode Gudang" data-column-index="0" />
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label">Nama Gudang:</label>
                                <input type="text" class="form-control dt-input" data-column="2"
                                    placeholder="Cari Berdasarkan Nama Gudang" data-column-index="1" />
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label">Status:</label>
                                <select class="global-select2 form-select" data-allow-clear="false">
                                    <option value="">Semua Status</option>
                                    @foreach($storageStatuses as $status)
                                        <option value="{{ $status['value'] }}">{{ $status['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label">Penanggung Jawab:</label>
                                <select class="global-select2 form-select" data-allow-clear="true" multiple>
                                    @foreach($picUsers as $user)
                                        <option value="{{ $user['value'] }}">{{ $user['label'] }}</option>
                                    @endforeach
                                </select>
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
                        <a href="{{ route('master_data.storage.import') }}"
                            class="btn btn-md btn-primary waves-effect waves-light"><span
                                class="icon-xs icon-base ti tabler-file-upload me-2"></span> Import</a>
                        <a href="{{ route('master_data.storage.export') }}"
                            class="btn btn-md btn-primary waves-effect waves-light"><span
                                class="icon-xs icon-base ti tabler-file-download me-2"></span> Export</a>
                        <a href="{{ route('master_data.storage.add') }}"
                            class="btn btn-md btn-primary waves-effect waves-light"><span
                                class="icon-xs icon-base ti tabler-plus me-2"></span> Tambah Data</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Data Table -->
        <div class="card-datatable">
            <table id="main-table" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Kode Gudang</th>
                        <th>Nama Gudang</th>
                        <th>Penanggung Jawab</th>
                        <th>Alamat</th>
                        <th>Status</th>
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
            //DataTable
            const main_table = document.querySelector('#main-table');
            let dt_adv_filter;

            if (main_table) {
                dt_adv_filter = new DataTable(main_table, {
                    ajax: `{{ route('master_data.storage.index_data_table') }}`,
                    columns: [{
                            data: 'storage_code',
                            responsivePriority: 3
                        },
                        {
                            data: 'storage_name',
                            responsivePriority: 2,
                            render: function(data, type, row) {
                                const detailUrl = `{{ route('master_data.storage.detail', ':id') }}`.replace(':id', row.id);
                                return `<a href="${detailUrl}" class="text-primary fw-semibold">${data}</a>`;
                            }
                        },
                        {
                            data: 'pic_names',
                            responsivePriority: 4
                        },
                        {
                            data: 'address',
                            responsivePriority: 6
                        },
                        {
                            data: 'status',
                            responsivePriority: 5
                        },
                        {
                            data: null,
                            responsivePriority: 1,
                            width: '120px'
                        }
                    ],
                    columnDefs: [{
                            orderable: false,
                            targets: -2,
                            render: function(data, type, full, meta) {
                                if (data == 'ACTIVE') {
                                    return `<span class="badge bg-label-success">Aktif</span>`;
                                } else {
                                    return `<span class="badge bg-label-secondary">Inaktif</span>`;
                                }
                            }
                        },
                        {
                            targets: -1,
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row, meta) {
                                const detailUrl = `{{ route('master_data.storage.detail', ':id') }}`.replace(':id', row.id);
                                const editUrl = `{{ route('master_data.storage.edit', ':id') }}`.replace(':id', row.id);
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
                    scrollX: true,
                    responsive: true,
                    orderCellsTop: true
                });
            }

            // Navigation buttons now use direct <a> elements with href - no JavaScript needed
        });
    </script>
@endsection
