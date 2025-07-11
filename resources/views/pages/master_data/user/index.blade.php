@extends('layouts.default')
@section('page_title')
    List User
@stop
@section('content')
    <div class="row">
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-primary"><i
                                    class="icon-base ti tabler-users icon-28px"></i></span>
                        </div>
                        <h4 class="mb-0">50</h4>
                    </div>
                    <p class="mb-1">Jumlah User</p>
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
                        <h4 class="mb-0">45</h4>
                    </div>
                    <p class="mb-1">Jumlah User Aktif</p>
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
                        <h4 class="mb-0">5</h4>
                    </div>
                    <p class="mb-1">Jumlah Role</p>
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
                        <h4 class="mb-0">40</h4>
                    </div>
                    <p class="mb-1">User Login Hari Ini</p>
                </div>
            </div>
        </div>
    </div>
    <hr class="my-6 mx-n6" />
    <div class="card">
        <h5 class="card-header">List User</h5>
        <div class="card-body">
            <form class="dt_adv_search" method="GET">
                <div class="row">
                    <div class="col-12">
                        <div class="row g-3">
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label">Nama User:</label>
                                <input type="text" class="form-control dt-input dt-full-name" data-column="1"
                                    placeholder="Cari Berdasarkan Kode User" data-column-index="0" />
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label">Email User:</label>
                                <input type="text" class="form-control dt-input" data-column="2"
                                    placeholder="Cari Berdasarkan Nama User" data-column-index="1" />
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label">Role:</label>
                                <input type="text" class="form-control dt-input" data-column="2"
                                    placeholder="Cari Berdasarkan Nama User" data-column-index="1" />
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label">Status:</label>
                                <select class="global-select2 form-select" data-allow-clear="true">
                                    <option value="AK">Active</option>
                                    <option value="HI">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <hr class="my-6 mx-n6" />
            <div class="mt-4">
                <div class="d-flex justify-content-end">
                    <div class="btn-group" role="group" aria-label="Basic example">
                        <button type="button" id="btn-import-data"
                            class="btn btn-md btn-primary waves-effect waves-light"><span
                                class="icon-xs icon-base ti tabler-file-upload me-2"></span> Import</button>
                        <button type="button" id="btn-export-data"
                            class="btn btn-md btn-primary waves-effect waves-light"><span
                                class="icon-xs icon-base ti tabler-file-download me-2"></span> Export</button>
                        <button type="button" id="btn-add-data"
                            class="btn btn-md btn-primary waves-effect waves-light"><span
                                class="icon-xs icon-base ti tabler-plus me-2"></span> Tambah Data</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-datatable">
            <table id="main-table" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nama User</th>
                        <th>Email User</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Terakhir Login</th>
                        <th>Password Terakhir Dirubah</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Nama User</th>
                        <th>Email User</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Terakhir Login</th>
                        <th>Password Terakhir Dirubah</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </tfoot>
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
                    ajax: '{{ route('master_data.user.index_data_table') }}',
                    columns: [{
                            data: 'name'
                        },
                        {
                            data: 'email'
                        },
                        {
                            data: 'phone'
                        },
                        {
                            data: 'role'
                        },
                        {
                            data: 'last_login'
                        },
                        {
                            data: 'last_password_change'
                        },
                        {
                            data: 'status'
                        },
                        {
                            data: null
                        }
                    ],
                    columnDefs: [{
                            orderable: false,
                            targets: -2,
                            render: function(data, type, full, meta) {
                                if (data == 'ACTIVE') {
                                    return `<span class="badge bg-success">${data}</span>`;
                                } else {
                                    return `<span class="badge bg-danger">${data}</span>`;
                                }
                            }
                        },
                        {
                            targets: -1,
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row, meta) {
                                return `
                                <div class="btn-group" role="group" aria-label="Second group">
                                    <button type="button" class="btn btn-primary btn-sm waves-effect">
                                        <i class="icon-base ti tabler-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-primary btn-sm waves-effect">
                                        <i class="icon-base ti tabler-eye"></i>
                                    </button>
                                     <button type="button" class="btn btn-primary btn-sm waves-effect">
                                        <i class="icon-base ti tabler-lock-open-2"></i>
                                    </button>
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
                    orderCellsTop: true
                });
            }

            //Main button click event
            $('#btn-add-data').on('click', function() {
                window.location.href = "{{ route('master_data.user.add') }}";
            });

            $('#btn-export-data').on('click', function() {
                window.location.href = "{{ route('master_data.user.export') }}";
            });

            $('#btn-import-data').on('click', function() {
                window.location.href = "{{ route('master_data.user.import') }}";
            });
        });
    </script>
@endsection
