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
                        <h4 class="mb-0">5 Total Role</h4>
                    </div>
                    <p class="mb-1">Role Terdaftar</p>
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
                        <h4 class="mb-0">4 Role Aktif</h4>
                    </div>
                    <p class="mb-1">Status Aktif</p>
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
                        <h4 class="mb-0">10 Total User</h4>
                    </div>
                    <p class="mb-1">User Terdaftar</p>
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
                        <h4 class="mb-0">36 Permission</h4>
                    </div>
                    <p class="mb-1">Total Permission</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="card mt-6">
        <h5 class="card-header">Role Management</h5>
        <div class="card-body">
            <!-- Advanced Search Form -->
            <form class="dt_adv_search" method="GET">
                <div class="row">
                    <div class="col-12">
                        <div class="row g-3">
                            <div class="col-12 col-sm-6 col-lg-4">
                                <label class="form-label">Status:</label>
                                <select class="form-select" id="role-status-filter">
                                    <option value="">Semua Status</option>
                                    <option value="active">Aktif</option>
                                    <option value="inactive">Tidak Aktif</option>
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <label class="form-label">Jumlah User:</label>
                                <select class="form-select" id="role-user-filter">
                                    <option value="">Semua</option>
                                    <option value="0">Tanpa User</option>
                                    <option value="1-5">1-5 User</option>
                                    <option value="6-10">6-10 User</option>
                                    <option value="10+">10+ User</option>
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
                <div class="d-flex justify-content-end">
                    <div class="d-flex gap-2">
                        <button type="button" id="btn-import-role" class="btn btn-md btn-primary waves-effect waves-light">
                            <span class="icon-xs icon-base ti tabler-file-upload me-2"></span> Import
                        </button>
                        <button type="button" id="btn-export-role" class="btn btn-md btn-primary waves-effect waves-light">
                            <span class="icon-xs icon-base ti tabler-file-download me-2"></span> Export
                        </button>
                        <button type="button" id="btn-add-role" class="btn btn-md btn-primary waves-effect waves-light">
                            <span class="icon-xs icon-base ti tabler-plus me-2"></span> Tambah Data
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Data Table -->
        <div class="card-datatable">
            <table id="role-table" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nama Role</th>
                        <th>Deskripsi</th>
                        <th>Users</th>
                        <th>Permissions</th>
                        <th>Status</th>
                        <th>Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
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
                ajax: {
                    url: `{{ route('master_data.role.index_data_table') }}`,
                    data: {
                        // Additional parameters
                    }
                },
                columns: [
                    { 
                        data: 'name',
                        render: function(data, type, row) {
                            return `<a href="#" class="text-primary">${data}</a>`;
                        }
                    },
                    { data: 'description' },
                    { 
                        data: 'user_count',
                        render: function(data, type, row) {
                            return `<span class="badge bg-primary">${data} users</span>`;
                        }
                    },
                    { 
                        data: 'permissions_count',
                        render: function(data, type, row) {
                            return `<span class="badge bg-info">${data} permissions</span>`;
                        }
                    },
                    { 
                        data: 'status',
                        render: function(data, type, row) {
                            let badgeClass = data === 'ACTIVE' ? 'bg-success' : 'bg-danger';
                            let statusText = data === 'ACTIVE' ? 'Aktif' : 'Tidak Aktif';
                            return `<span class="badge ${badgeClass}">${statusText}</span>`;
                        }
                    },
                    { data: 'created_at' },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            const editUrl = '{{ route("master_data.role.edit", ":id") }}'.replace(':id', row.id);
                            const detailUrl = '{{ route("master_data.role.detail", ":id") }}'.replace(':id', row.id);
                            
                            let deleteButton = '';
                            if (row.user_count == 0) {
                                deleteButton = `
                                    <button class="btn btn-danger btn-sm waves-effect" onclick="deleteRole(${row.id})" title="Delete">
                                        <i class="icon-base ti tabler-trash"></i>
                                    </button>
                                `;
                            } else {
                                deleteButton = `
                                    <button class="btn btn-secondary btn-sm waves-effect" disabled title="Cannot delete - has ${row.user_count} user(s)">
                                        <i class="icon-base ti tabler-trash"></i>
                                    </button>
                                `;
                            }
                            
                            return `
                            <div class="btn-group" role="group">
                                <a href="${editUrl}" class="btn btn-primary btn-sm waves-effect">
                                    <i class="icon-base ti tabler-edit"></i>
                                </a>
                                <a href="${detailUrl}" class="btn btn-primary btn-sm waves-effect">
                                    <i class="icon-base ti tabler-eye"></i>
                                </a>
                                ${deleteButton}
                            </div>
                            `;
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
                responsive: true
            });
        }
        
        // Initialize table
        initRoleTable();
        
        // Button click events
        $('#btn-add-role').on('click', function() {
            window.location.href = "{{ route('master_data.role.add') }}";
        });
        
        $('#btn-export-role').on('click', function() {
            window.location.href = "{{ route('master_data.role.export') }}";
        });
        
        $('#btn-import-role').on('click', function() {
            window.location.href = "{{ route('master_data.role.import') }}";
        });
        
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
    });

    // Delete role function
    function deleteRole(id) {
        Swal.fire({
            title: 'Hapus Role?',
            text: "Tindakan ini tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                // Send DELETE request to server
                fetch(`{{ route('master_data.role.destroy', ':id') }}`.replace(':id', id), {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error === 'role_has_users') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Tidak Dapat Menghapus Role',
                            text: data.message,
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                            customClass: {
                                confirmButton: 'btn btn-success'
                            },
                            buttonsStyling: false
                        }).then(() => {
                            // Reload the table
                            if (roleTable) {
                                roleTable.ajax.reload();
                            }
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat menghapus role.',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                });
            }
        });
    }
</script>
@endsection