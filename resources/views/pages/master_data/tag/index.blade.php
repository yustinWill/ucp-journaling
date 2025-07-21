@extends('layouts.default')
@section('page_title')
    List Tag
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
                                    class="icon-base ti tabler-tags icon-28px"></i></span>
                        </div>
                        <h4 class="mb-0">7</h4>
                    </div>
                    <p class="mb-1">Total Tag Aktif</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6">
            <div class="card card-border-shadow-success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-success"><i
                                    class="icon-base ti tabler-chart-line icon-28px"></i></span>
                        </div>
                        <h4 class="mb-0">482</h4>
                    </div>
                    <p class="mb-1">Total Penggunaan</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6">
            <div class="card card-border-shadow-info h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-info"><i
                                    class="icon-base ti tabler-palette icon-28px"></i></span>
                        </div>
                        <h4 class="mb-0">7</h4>
                    </div>
                    <p class="mb-1">Warna Tersedia</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Content Card -->
    <div class="mt-6">
        <div class="card">
        <h4 class="card-header">Kelola Tag</h4>
        <div class="card-body">
            <!-- Advanced Search Form -->
            <form class="dt_adv_search" method="GET">
                <div class="row">
                    <div class="col-12">
                        <div class="row g-3">
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label">Kode Tag:</label>
                                <input type="text" class="form-control dt-input" data-column="1"
                                    placeholder="Cari berdasarkan kode tag" data-column-index="1" />
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label">Nama Tag:</label>
                                <input type="text" class="form-control dt-input" data-column="2"
                                    placeholder="Cari berdasarkan nama tag" data-column-index="2" />
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label">Warna:</label>
                                <select class="global-select2 form-select" data-allow-clear="false">
                                    <option value="">Semua Warna</option>
                                    @foreach($availableColors as $color)
                                        <option value="{{ $color->code }}">{{ $color->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label">Status:</label>
                                <select class="global-select2 form-select" data-allow-clear="false">
                                    <option value="">Semua Status</option>
                                    @foreach($availableStatus as $status)
                                        <option value="{{ $status['value'] }}">{{ $status['label'] }}</option>
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
                        <a href="{{ route('master_data.tag.add') }}"
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
                        <th>Tanggal Dibuat</th>
                        <th>Kode</th>
                        <th>Nama Tag</th>
                        <th>Warna</th>
                        <th>Deskripsi</th>
                        <th>Status</th>
                        <th>Penggunaan</th>
                        <th>Dibuat Oleh</th>
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
            //DataTable
            const main_table = document.querySelector('#main-table');
            let dt_adv_filter;

            if (main_table) {
                dt_adv_filter = new DataTable(main_table, {
                    processing: true,
                    serverSide: true,
                    ajax: `{{ route('master_data.tag.index_data_table') }}`,
                    columns: [
                        { data: 'created_at' },
                        { data: 'code' },
                        { data: 'name' },
                        { data: 'tag_color' },
                        { data: 'description' },
                        { data: 'status' },
                        { data: 'usage_count' },
                        { data: 'created_by' },
                        { data: 'id' }
                    ],
                    columnDefs: [{
                            targets: -1, // Action column (last column)
                            orderable: false,
                            searchable: false,
                            width: '120px',
                            responsivePriority: 1,
                            render: function(data, type, row, meta) {
                                const editUrl = `{{ route('master_data.tag.edit', ':id') }}`.replace(':id', row.id);
                                const detailUrl = `{{ route('master_data.tag.detail', ':id') }}`.replace(':id', row.id);
                                
                                return `
                                <div class="d-flex gap-2">
                                    <a href="${detailUrl}" class="btn btn-md btn-primary waves-effect waves-light" title="Detail">
                                        <i class="icon-base ti tabler-eye"></i>
                                    </a>
                                    <a href="${editUrl}" class="btn btn-md btn-primary waves-effect waves-light" title="Edit">
                                        <i class="icon-base ti tabler-edit"></i>
                                    </a>
                                </div>
                            `;
                            }
                        },
                        {
                            targets: [0], // Date column (first column)
                            responsivePriority: 2,
                            render: function(data, type, row) {
                                return data; // Keep original date format
                            }
                        },
                        {
                            targets: [1], // Code column - make clickable
                            responsivePriority: 3,
                            render: function(data, type, row) {
                                const detailUrl = `{{ route('master_data.tag.detail', ':id') }}`.replace(':id', row.id);
                                return `<a href="${detailUrl}" class="text-primary fw-semibold">${data}</a>`;
                            }
                        },
                        {
                            targets: [3], // Color column
                            render: function(data, type, row) {
                                console.log(data)
                                if (!data) return '-';
                                const parts = data.split('|');
                                const preview = parts[0]?.trim().toLowerCase() || 'secondary';
                                const label = parts[1]?.trim() || preview;
                                return `<span class="badge ${preview} me-1">${label}</span>`;
                            }
                        },
                        {
                            targets: [5], // Status column
                            render: function(data, type, row) {
                                const badgeClass = data === 'ACTIVE' ? 'bg-label-success' : 'bg-label-secondary';
                                return `<span class="badge ${badgeClass}">${data}</span>`;
                            }
                        },
                        {
                            targets: [6], // Usage count column
                            className: 'text-center',
                            render: function(data, type, row) {
                                return `<span class="badge bg-label-info">${data}</span>`;
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
        });
    </script>
@endsection