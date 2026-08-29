@extends('layouts.default')
@section('page_title')
    List User
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
                                    class="icon-base ti tabler-users icon-28px"></i></span>
                        </div>
                        <h4 class="mb-0">28</h4>
                    </div>
                    <p class="mb-1">Total Karyawan RIYO</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-primary"><i
                                    class="icon-base ti tabler-user-check icon-28px"></i></span>
                        </div>
                        <h4 class="mb-0">25</h4>
                    </div>
                    <p class="mb-1">User Aktif</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-primary"><i
                                    class="icon-base ti tabler-shield icon-28px"></i></span>
                        </div>
                        <h4 class="mb-0">8</h4>
                    </div>
                    <p class="mb-1">Role & Departemen</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-primary"><i
                                    class="icon-base ti tabler-login icon-28px"></i></span>
                        </div>
                        <h4 class="mb-0">22</h4>
                    </div>
                    <p class="mb-1">Login Hari Ini</p>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Main Content Card -->
    <div class="card mt-6">
        <h4 class="card-header">Kelola User</h4>
        <div class="card-body">
            <!-- Advanced Search Form -->
            <form class="dt_adv_search" method="GET" autocomplete="off">
                <div class="row">
                    <div class="col-12">
                        <div class="row g-3">
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label">Nama User:</label>
                                <input type="text" class="form-control dt-input dt-full-name" data-column="1"
                                    placeholder="Cari Berdasarkan Kode User" id="user-name" data-column-index="0" />
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label">Email User:</label>
                                <input type="text" class="form-control dt-input" data-column="2"
                                    placeholder="Cari Berdasarkan Nama User" id="user-email" data-column-index="1" />
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label">Role:</label>
                                <select class="global-select2 form-select" data-allow-clear="true" id="user-role">
                                    <option value="">Semua Status</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label">Status:</label>
                                <select class="global-select2 form-select" data-allow-clear="true" id="user-status">
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
                <div class="d-flex justify-content-end">
                    <div class="d-flex gap-2">
                        @can('user.create')
                            <a href="{{ route('master_data.user.add') }}"
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
                        <th>Tanggal Dibuat</th>
                        <th>Nama User</th>
                        <th>Email User</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Terakhir Login</th>
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
            const canView = @json(auth()->user()->can('user.view'));
            const canEdit = @json(auth()->user()->can('user.edit'));
            const canDelete = @json(auth()->user()->can('user.delete'));
            const currentUserId = @json(auth()->id());

            //DataTable
            const main_table = document.querySelector('#main-table');
            let dt_adv_filter;

            if (main_table) {
                dt_adv_filter = new DataTable(main_table, {
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: `{{ route('master_data.user.index_data_table') }}`,
                        data: function(d) {
                            d.name = $('#user-name').val();
                            d.email = $('#user-email').val();
                            d.role = $('#user-role').val();
                            d.status = $('#user-status').val();
                        }
                    },
                    columns: [{
                            data: 'created_at',
                            responsivePriority: 1
                        },
                        {
                            data: 'name',
                            responsivePriority: 2
                        },
                        {
                            data: 'email',
                            responsivePriority: 3
                        },
                        {
                            data: 'phone',
                            responsivePriority: 4
                        },
                        {
                            data: 'role_name',
                            responsivePriority: 5
                        },
                        {
                            data: 'last_login',
                            responsivePriority: 6
                        },
                        {
                            data: 'account_status',
                            responsivePriority: 7
                        },
                        {
                            data: null,
                            responsivePriority: 8
                        }
                    ],
                    columnDefs: [{
                            targets: -1, // Action column (last column)
                            orderable: false,
                            searchable: false,
                            responsivePriority: 1,
                            render: function(data, type, row, meta) {
                                const detailUrl = "{{ route('master_data.user.detail', ':id') }}"
                                    .replace(':id', row.id);
                                const editUrl = "{{ route('master_data.user.edit', ':id') }}"
                                    .replace(':id', row.id);
                                let actions = '<div class="d-flex gap-2">';

                                if (canView) {
                                    actions += `<a href="${detailUrl}" class="btn btn-sm btn-primary waves-effect waves-light" title="Detail">
                                        <i class="icon-base ti tabler-eye"></i>
                                    </a>`;
                                }

                                if (canEdit && row.deleted_at === null) {
                                    actions += `<a href="${editUrl}" class="btn btn-sm btn-primary waves-effect waves-light" title="Edit">
                                        <i class="icon-base ti tabler-edit"></i>
                                    </a>`;
                                }

                                if (canDelete && row.deleted_at === null && Number(row.id) !== Number(currentUserId)) {
                                    actions += `<button type="button" class="btn btn-sm btn-danger waves-effect waves-light delete-btn"
                                        data-id="${row.id}" title="Hapus dan nonaktifkan">
                                        <i class="icon-base ti tabler-trash"></i>
                                    </button>`;
                                }

                                actions += '</div>';
                                return actions;
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
                            targets: [1], // Name column
                            responsivePriority: 3
                        },
                        {
                            targets: [6],
                            render: function(data) {
                                const badgeClass = data === 'ACTIVE' ? 'bg-label-success' :
                                    (data === 'DELETED' ? 'bg-label-danger' : 'bg-label-secondary');
                                const statusText = data === 'ACTIVE' ? 'Aktif' :
                                    (data === 'DELETED' ? 'Dihapus' : 'Tidak Aktif');
                                return `<span class="badge ${badgeClass}">${statusText}</span>`;
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
                    orderCellsTop: true
                });
            }

            let searchTimeout;
            $('#user-name, #user-email, #user-role, #user-status').on('keyup change',
                function() {
                    clearTimeout(searchTimeout);
                    const input = this;
                    searchTimeout = setTimeout(function() {
                        if (dt_adv_filter) {
                            dt_adv_filter.search(input.value).draw();
                        }
                    }, 400); // 400ms delay
                });

            $(document).on('click', '.delete-btn', function() {
                const id = $(this).data('id');
                const row = dt_adv_filter.row($(this).closest('tr')).data();
                const name = row?.name || 'user ini';

                Swal.fire({
                    title: 'Hapus user?',
                    html: `Akun <strong>${$('<div>').text(name).html()}</strong> akan dinonaktifkan dan tidak dapat mengakses aplikasi lagi.<br><small>Data user tidak akan dihapus dari database.</small>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-danger',
                        cancelButton: 'btn btn-secondary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (!result.isConfirmed) {
                        return;
                    }

                    $.ajax({
                        url: "{{ route('master_data.user.delete') }}",
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: id
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message,
                                customClass: {
                                    confirmButton: 'btn btn-success'
                                },
                                buttonsStyling: false
                            });
                            dt_adv_filter.ajax.reload(null, false);
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal menghapus user',
                                html: xhr.responseJSON?.message || 'Terjadi kesalahan saat memproses permintaan.',
                                customClass: {
                                    confirmButton: 'btn btn-primary'
                                },
                                buttonsStyling: false
                            });
                        }
                    });
                });
            });
        });
    </script>
@endsection
