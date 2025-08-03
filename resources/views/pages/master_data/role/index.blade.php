@extends('layouts.default')
@section('page_title')
    List Role
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
                                <i class="icon-base ti tabler-shield icon-28px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0">8</h4>
                    </div>
                    <p class="mb-1">Total Role Departemen</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="icon-base ti tabler-shield-check icon-28px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0">7</h4>
                    </div>
                    <p class="mb-1">Role Aktif</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="icon-base ti tabler-users icon-28px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0">28</h4>
                    </div>
                    <p class="mb-1">Karyawan Terdaftar</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-danger h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-danger">
                                <i class="icon-base ti tabler-key icon-28px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0">45</h4>
                    </div>
                    <p class="mb-1">Total Hak Akses</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="mt-6">
        <div class="card">
            <h4 class="card-header">Kelola Role</h4>
            <div class="card-body">
                <!-- Advanced Search Form -->
                <form class="dt_adv_search" method="GET">
                    <div class="row">
                        <div class="col-12">
                            <div class="row g-3">
                                <div class="col-12 col-sm-6 col-lg-4">
                                    <label class="form-label">Status:</label>
                                    <select class="global-select2 form-select" id="role-status-filter"
                                        data-allow-clear="false">
                                        <option value="">Semua Status</option>
                                        @foreach ($statusFilters as $filter)
                                            <option value="{{ $filter['value'] }}">{{ $filter['label'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-sm-6 col-lg-4">
                                    <label class="form-label">Jumlah User:</label>
                                    <select class="global-select2 form-select" id="role-user-filter"
                                        data-allow-clear="false">
                                        <option value="">Semua</option>
                                        @foreach ($userFilters as $filter)
                                            <option value="{{ $filter['value'] }}">{{ $filter['label'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-sm-6 col-lg-4">
                                    <label class="form-label">Pencarian:</label>
                                    <input type="text" class="form-control" id="role-search" placeholder="Cari Role">
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
                            <a href="{{ route('master_data.role.import') }}"
                                class="btn btn-md btn-primary waves-effect waves-light">
                                <span class="icon-xs icon-base ti tabler-file-upload me-2"></span> Import
                            </a>
                            <a href="{{ route('master_data.role.export') }}"
                                class="btn btn-md btn-primary waves-effect waves-light">
                                <span class="icon-xs icon-base ti tabler-file-download me-2"></span> Export
                            </a>
                            <a href="{{ route('master_data.role.add') }}"
                                class="btn btn-md btn-primary waves-effect waves-light">
                                <span class="icon-xs icon-base ti tabler-plus me-2"></span> Tambah Data
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Data Table -->
            <div class="card-datatable">
                <table id="role-table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Dibuat</th>
                            <th>Nama Role</th>
                            <th>Deskripsi</th>
                            <th>Users</th>
                            <th>Permissions</th>
                            <th>Tag</th>
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

            let roleTable;

            function initRoleTable() {
                if (roleTable) {
                    roleTable.destroy();
                }

                roleTable = new DataTable('#role-table', {
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: `{{ route('master_data.role.index_data_table') }}`,
                        data: function(d) {
                            d.status = $('#role-status-filter').val();
                            d.user_count = $('#role-user-filter').val();
                            d.name = $('#role-search').val();
                        }
                    },
                    columns: [{
                            data: 'created_at',
                            responsivePriority: 2
                        },
                        {
                            data: 'name',
                            responsivePriority: 3
                        },
                        {
                            data: 'description'
                        },
                        {
                            data: 'user_count',
                            render: function(data, type, row) {
                                return `${data} users`; // Plain text for count
                            }
                        },
                        {
                            data: 'permission_count',
                            render: function(data, type, row) {
                                return `${data} permissions`; // Plain text for count
                            }
                        },
                        {
                            data: 'tags',
                            responsivePriority: 6,
                            render: function(data, type, row) {
                                let tags = JSON.parse(data);
                                return tags.map(tag => {
                                    if (tag.code == null) {
                                        return ``;
                                    }
                                    return `<span class="badge ${tag.preview} me-1">${tag.code}</span>`;
                                }).join('');
                            }
                        },
                        {
                            data: 'is_active',
                            render: function(data, type, row) {
                                const badgeClass = data == '1' ? 'bg-label-success' :
                                    'bg-label-secondary';
                                const statusText = data == '1' ? 'ACTIVE' : 'INACTIVE';
                                return `<span class="badge ${badgeClass}">${statusText}</span>`;
                            }
                        },
                        {
                            data: null,
                            orderable: false,
                            searchable: false,
                            width: '120px',
                            responsivePriority: 1,
                            render: function(data, type, row, meta) {
                                const editUrl = '{{ route('master_data.role.edit', ':id') }}'
                                    .replace(':id', row.id);
                                const detailUrl = '{{ route('master_data.role.detail', ':id') }}'
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
                    scrollX: true,
                    responsive: true,
                    orderCellsTop: true
                });
            }

            // Initialize table
            initRoleTable();

            // Navigation buttons now use direct <a> elements with href - no JavaScript needed

            // Search functionality
            $('#role-search').on('keyup', function() {
                if (roleTable) {
                    roleTable.search(this.value).draw();
                }
            });

            // Filter functionality
            $('#role-status-filter, #role-user-filter').on('change', function() {
                if (roleTable) {
                    roleTable.draw();
                }
            });

            // Tag Column Toggle Functionality
            $('#toggleTagColumn').on('change', function() {
                if (roleTable) {
                    const tagColumn = roleTable.column(5); // Tag column index (0-based)
                    tagColumn.visible(this.checked);
                }
            });

            // Initialize tag column visibility based on checkbox state
            const initialTagVisibility = $('#toggleTagColumn').is(':checked');
            if (roleTable) {
                roleTable.column(5).visible(initialTagVisibility);
            }
        });
    </script>
@endsection
