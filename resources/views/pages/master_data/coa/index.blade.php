@extends('layouts.default')
@section('page_title')
    List COA
@stop
@section('content')
    @if (session('CLIENT_CODE') === '001')
        <!-- Summary Cards -->
        {{-- <div class="row">
            <div class="col-lg-3 col-sm-6">
                <div class="card card-border-shadow-primary h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded bg-label-primary"><i
                                        class="icon-base ti tabler-category icon-28px"></i></span>
                            </div>
                            <h4 class="mb-0">287</h4>
                        </div>
                        <p class="mb-1">Total COA Aktif</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card card-border-shadow-success h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded bg-label-success"><i
                                        class="icon-base ti tabler-cash icon-28px"></i></span>
                            </div>
                            <h4 class="mb-0">45</h4>
                        </div>
                        <p class="mb-1">Akun Aktiva</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card card-border-shadow-warning h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded bg-label-warning"><i
                                        class="icon-base ti tabler-credit-card icon-28px"></i></span>
                            </div>
                            <h4 class="mb-0">23</h4>
                        </div>
                        <p class="mb-1">Akun Kewajiban</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card card-border-shadow-info h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded bg-label-info"><i
                                        class="icon-base ti tabler-receipt icon-28px"></i></span>
                            </div>
                            <h4 class="mb-0">156</h4>
                        </div>
                        <p class="mb-1">Akun Pendapatan & Biaya</p>
                    </div>
                </div>
            </div>
        </div> --}}
    @endif


    <!-- Main Content Card -->
    <div class="card mt-6">
        <h4 class="card-header">List COA</h4>
        <div class="card-body">
            <!-- Advanced Search Form -->
            <form class="dt_adv_search" method="GET" autocomplete="off">
                <div class="row">
                    <div class="col-12">
                        <div class="row g-3">
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label">Kode COA:</label>
                                <input type="text" class="form-control dt-input dt-full-name"
                                    placeholder="Cari Berdasarkan Kode COA" id="coa-code" />
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label">Nama COA:</label>
                                <input type="text" class="form-control dt-input" placeholder="Cari Berdasarkan Nama COA"
                                    id="coa-name" />
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label">Level COA:</label>
                                <input type="text" class="form-control dt-input" placeholder="Cari Berdasarkan Level COA"
                                    id="coa-level" />
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label">Status:</label>
                                <select class="global-select2 form-select" data-allow-clear="true" id="coa-status">
                                    <option value="">Semua Status</option>
                                    @foreach ($statuses as $status)
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
                        
                        @can('coa.write')
                            <a href="{{ route('master_data.coa.add') }}"
                                class="btn btn-md btn-primary waves-effect waves-light"><span
                                    class="icon-xs icon-base ti tabler-plus me-2"></span> Tambah Data</a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
        <!-- Data Table -->
        <div class="card-datatable">
            <table id="main-table" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Kode COA</th>
                        <th>Nama COA</th>
                        <th>Level</th>
                        <th>Jenis</th>
                        <th>Tag</th>
                        <th>Note</th>
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
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: `{{ route('master_data.coa.index_data_table') }}`,
                        data: function(d) {
                            d.code = $('#coa-code').val();
                            d.name = $('#coa-name').val();
                            d.level = $('#coa-level').val();
                            d.status = $('#coa-status').val();
                        }
                    },
                    columns: [{
                            data: 'code',
                            responsivePriority: 3
                        },
                        {
                            data: 'name',
                            responsivePriority: 2,
                            render: function(data, type, row) {
                                const detailUrl = `{{ route('master_data.coa.detail', ':id') }}`
                                    .replace(':id', row.id);
                                return `<a href="${detailUrl}" class="text-primary fw-semibold">${data}</a>`;
                            }
                        },
                        {
                            data: 'level',
                            responsivePriority: 4
                        },
                        {
                            data: 'type',
                            responsivePriority: 5
                        },
                        {
                            data: 'tags',
                            responsivePriority: 6,
                            render: function(data, type, row) {
                                let tags = JSON.parse(data);
                                return tags.map(tag => {
                                    if (tag.name == null) {
                                        return ``;
                                    }
                                    return `<span class="badge ${tag.preview} me-1">${tag.name}</span>`;
                                }).join('');
                            }
                        },
                        {
                            data: 'note',
                            responsivePriority: 8
                        },
                        {
                            data: 'status',
                            responsivePriority: 7,
                            render: function(data, type, row) {
                                const badgeClass = data === 'ACTIVE' ? 'bg-label-success' :
                                    'bg-label-secondary';
                                const statusText = data === 'ACTIVE' ? 'ACTIVE' : 'INACTIVE';
                                return `<span class="badge ${badgeClass}">${statusText}</span>`;
                            }
                        },
                        {
                            data: null,
                            responsivePriority: 1,
                            width: '120px'
                        }
                    ],
                    columnDefs: [{
                            orderable: false,
                            targets: -2, // Status column (second to last)
                            render: function(data, type, full, meta) {
                                return data; // Plain text for status
                            }
                        },
                        {
                            targets: -1,
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row, meta) {
                                const detailUrl = `{{ route('master_data.coa.detail', ':id') }}`
                                    .replace(':id', row.id);
                                const editUrl = `{{ route('master_data.coa.edit', ':id') }}`
                                    .replace(':id', row.id);
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
                    // scrollX: true,
                    responsive: true,
                    orderCellsTop: true
                });
            }

            // Tag Column Toggle Functionality
            $('#toggleTagColumn').on('change', function() {
                const tagColumn = dt_adv_filter.column(4); // Tag column index (0-based)
                tagColumn.visible(this.checked);
            });

            // Initialize tag column visibility based on checkbox state
            const initialTagVisibility = $('#toggleTagColumn').is(':checked');
            dt_adv_filter.column(4).visible(initialTagVisibility);

            // Search functionality with debounce (500ms)
            $('#coa-code, #coa-name, #coa-level').on('keyup', debounce(function() {
                if (dt_adv_filter) {
                    dt_adv_filter.search(this.value).draw();
                }
            }, 1000));

            // Filter functionality with debounce (1000ms)
            $('#coa-status').on('change', debounce(function() {
                if (dt_adv_filter) {
                    dt_adv_filter.draw();
                }
            }, 1000));

        });
    </script>
@endsection
