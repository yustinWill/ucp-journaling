@extends('layouts.default')
@section('page_title')
    List Kontak
@stop
@section('content')
    <div class="row">
        <div class="col-lg-4 col-sm-6">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-primary"><i
                                    class="icon-base ti tabler-file-invoice icon-28px"></i></span>
                        </div>
                        <h4 class="mb-0">Rp 100,000,000</h4>
                    </div>
                    <p class="mb-1">Piutang Belum Dibayar</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-primary"><i
                                    class="icon-base ti tabler-calendar-exclamation icon-28px"></i></span>
                        </div>
                        <h4 class="mb-0">Rp 25,000,000</h4>
                    </div>
                    <p class="mb-1">Piutang Jatuh Tempo</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-primary"><i
                                    class="icon-base ti tabler-clipboard-list icon-28px"></i></span>
                        </div>
                        <h4 class="mb-0">Rp 5,000,000</h4>
                    </div>
                    <p class="mb-1">Kredit Memo</p>
                </div>
            </div>
        </div>
    </div>
    <hr class="my-6 mx-n6" />
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
                                const detailUrl = "{{ route('master_data.contact.detail', ':id') }}".replace(':id', row.id);
                                const editUrl = "{{ route('master_data.contact.edit', ':id') }}".replace(':id', row.id);
                                return `
                                <div class="btn-group" role="group" aria-label="Second group">
                                    <a href="${editUrl}" class="btn btn-primary btn-sm waves-effect">
                                        <i class="icon-base ti tabler-edit"></i>
                                    </a>
                                    <a href="${detailUrl}" class="btn btn-primary btn-sm waves-effect">
                                        <i class="icon-base ti tabler-eye"></i>
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
                    orderCellsTop: true
                });
            }

            //Main button click event
            $('#btn-add-data').on('click', function() {
                window.location.href = "{{ route('master_data.contact.add') }}";
            });

            $('#btn-export-data').on('click', function() {
                window.location.href = "{{ route('master_data.contact.export') }}";
            });

            $('#btn-import-data').on('click', function() {
                window.location.href = "{{ route('master_data.contact.import') }}";
            });
        });
    </script>
@endsection
