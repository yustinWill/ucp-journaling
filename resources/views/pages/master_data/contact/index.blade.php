@extends('layouts.default')
@section('page_title')
    List Kontak
@stop
@section('content')
    <div class="card">
        <h5 class="card-header">List Kontak</h5>
        <div class="card-body">
            <form class="dt_adv_search" method="GET">
                <div class="row">
                    <div class="col-12">
                        <div class="row g-3">
                            <div class="col-12 col-sm-6 col-lg-4">
                                <label class="form-label">Nama Kontak:</label>
                                <input type="text" class="form-control dt-input dt-full-name" data-column="1"
                                    placeholder="Cari Berdasarkan Nama Kontak" data-column-index="0" />
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <label class="form-label">Nama Perusahaan:</label>
                                <input type="text" class="form-control dt-input" data-column="2"
                                    placeholder="Cari Berdasarkan Nama Perusahaan" data-column-index="1" />
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <label class="form-label">Email:</label>
                                <input type="text" class="form-control dt-input" data-column="3"
                                    placeholder="Cari Berdasarkan Email" data-column-index="2" />
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <label class="form-label">Phone:</label>
                                <input type="text" class="form-control dt-input" data-column="4"
                                    placeholder="Cari Berdasarkan Phone" data-column-index="3" />
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <label class="form-label">Group:</label>
                                <select class="global-select2 form-select" data-allow-clear="true">
                                    <option value="AK">Group A</option>
                                    <option value="HI">Group B</option>
                                    <option value="HI">Group C</option>
                                    <option value="CA">Group D</option>
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <label class="form-label">Tipe:</label>
                                <select class="global-select2 form-select" data-allow-clear="true">
                                    <option value="AK">Pelanggan</option>
                                    <option value="HI">Supplier</option>
                                    <option value="HI">Karyawan</option>
                                    <option value="CA">Lainnya</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
            <br>

            <div class="mt-4">
                <div class="d-flex justify-content-end">
                    <div class="btn-group" role="group" aria-label="Basic example">
                        <button type="button" class="btn btn-md btn-primary waves-effect waves-light"><span
                                class="icon-xs icon-base ti tabler-file-upload me-2"></span> Import</button>
                        <button type="button" class="btn btn-md btn-primary waves-effect waves-light"><span
                                class="icon-xs icon-base ti tabler-file-download me-2"></span> Export</button>
                        <button type="button" id="btn-add-data" class="btn btn-md btn-primary waves-effect waves-light"><span
                                class="icon-xs icon-base ti tabler-plus me-2"></span> Tambah Data</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-datatable">
            <table id="main-table" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nama Kontak</th>
                        <th>Nama Perusahaan</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Group</th>
                        <th>Tipe</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Nama Kontak</th>
                        <th>Nama Perusahaan</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Group</th>
                        <th>Tipe</th>
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
                    ajax: '{{ route('master_data.contact.index_data_table') }}',
                    columns: [{
                            data: 'contact_name'
                        },

                        {
                            data: 'company_name'
                        },

                        {
                            data: 'email'
                        },
                        {
                            data: 'phone'
                        },

                        {
                            data: 'group_text'
                        },
                        {
                            data: 'type'
                        },
                        {
                            data: null
                        }
                    ],
                    columnDefs: [{
                            orderable: false,
                            targets: 5,
                            render: function(data, type, full, meta) {
                                if (data == 'Pelanggan') {
                                    return `<span class="badge bg-success">${data}</span>`;
                                } else if (data == 'Supplier') {
                                    return `<span class="badge bg-warning">${data}</span>`;
                                } else if (data == 'Karyawan') {
                                    return `<span class="badge bg-info">${data}</span>`;
                                } else {
                                    return `<span class="badge bg-secondary">${data}</span>`;
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
                window.location.href = "{{ route('master_data.contact.add') }}";
            });


        });
    </script>
@endsection
