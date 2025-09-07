@extends('layouts.default')
@section('page_title')
    Impor Warehouse
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Impor Warehouse</h4>
        <form class="card-body" autocomplete="off">
            <div class="row gy-3 mt-0">
                <div class="col-md-12">
                    <div class="alert alert-primary alert-dismissible d-flex" role="alert">
                        <span class="alert-icon rounded">
                            <i class="icon-base ti tabler-bell icon-md w-px-30"></i>
                        </span>
                        <div class="d-flex flex-column ps-1">
                            <h5 class="alert-heading mb-2"><b>Tahapan Impor Template!</b></h5>
                            <ol>
                                <li><b>Unduh Template</b>
                                    <ul>
                                        <li>Klik tombol “Download Template”.</li>
                                        <li>Sistem akan memberikan file Excel sesuai format yang dibutuhkan.</li>
                                    </ul>
                                </li>
                                <li><b>Isi Template</b>
                                    <ul>
                                        <li>Buka file template yang telah diunduh.</li>
                                        <li>Masukkan data sesuai format kolom yang tersedia.</li>
                                        <li>Pastikan tidak ada kolom yang kosong jika bersifat wajib.</li>
                                        <li>Perhatikan format data (misalnya: tanggal dalam format YYYY-MM-DD, angka tanpa
                                            titik/koma ribuan, dll).</li>
                                    </ul>
                                </li>
                                <li><b>Unggah Template</b>
                                    <ul>
                                        <li>Setelah data diisi, simpan file.</li>
                                        <li>Kembali ke sistem dan klik tombol “Upload Template”.</li>
                                        <li>Pilih file yang telah diisi, lalu klik “Unggah”.</li>
                                    </ul>
                                </li>
                                <li><b>Cek dan Validasi Data</b>
                                    <ul>
                                        <li>Sistem akan melakukan validasi otomatis terhadap data yang diunggah.</li>
                                        <li>Jika ada kesalahan, sistem akan menampilkan pesan error (misalnya: baris ke-5
                                            kolom “Tanggal” tidak sesuai format).</li>
                                        <li>Perbaiki data di template dan ulangi proses unggah jika diperlukan.</li>
                                    </ul>
                                </li>
                                <li><b>Proses Import Data</b>
                                    <ul>
                                        <li>Setelah semua data valid, klik “Import”</li>
                                        <li>Data akan disimpan ke dalam sistem dan bisa langsung terlihat di list data</li>
                                    </ul>
                                </li>
                            </ol>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                </div>
            </div>
            <h6>1. Unduh Template</h6>
            <button type="button" class="btn btn-primary btn-lg waves-effect waves-light"><span
                    class="icon-xs icon-base ti tabler-file-type-xls me-2"></span>Download Template</button>
            <hr class="my-6 mx-n6" />
            <h6>2. Unggah Template</h6>
            <div class="row">
                <div class="col-md-4">
                    <input class="form-control" type="file" id="formFile">
                </div>
            </div>
            <hr class="my-6 mx-n6" />
            <h6>3. Validasi Data</h6>
            <div class="row">
                <div class="row">
                    <div class="col-lg-4 col-sm-6">
                        <div class="card card-border-shadow-primary h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="avatar me-4">
                                        <span class="avatar-initial rounded bg-label-primary"><i
                                                class="icon-base ti tabler-upload icon-28px"></i></span>
                                    </div>
                                    <h4 class="mb-0">10 Baris</h4>
                                </div>
                                <p class="mb-1">Data Yang Diunggah</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <div class="card card-border-shadow-primary h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="avatar me-4">
                                        <span class="avatar-initial rounded bg-label-primary"><i
                                                class="icon-base ti tabler-check icon-28px"></i></span>
                                    </div>
                                    <h4 class="mb-0">9 Baris</h4>
                                </div>
                                <p class="mb-1">Data Sukses</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <div class="card card-border-shadow-primary h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="avatar me-4">
                                        <span class="avatar-initial rounded bg-label-primary"><i
                                                class="icon-base ti tabler-x icon-28px"></i></span>
                                    </div>
                                    <h4 class="mb-0">1 Baris</h4>
                                </div>
                                <p class="mb-1">Data Error</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <h6>Data Sukses</h6>
                <div class="card-datatable">
                    <table class="success-table table table-bordered">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>City</th>
                                <th>Date</th>
                                <th>Age</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

            <div class="row">
                <h6>Data Error</h6>
                <div class="card-datatable">
                    <table class="error-table table table-bordered">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>City</th>
                                <th>Date</th>
                                <th>Age</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="pt-6 text-end">
                <button id="submit-btn" class="btn btn-primary me-4">Import</button>
            </div>
        </form>
    </div>
@endsection
@section('js_script')
    <script>
        $(function() {
            const success_table = document.querySelector('.success-table');
            let success_table_child;
            if (success_table) {
                success_table_child = new DataTable(success_table, {
                    ajax: assetsPath + 'json/table-datatable.json',
                    columns: [{
                            data: null
                        },
                        {
                            data: 'full_name'
                        },
                        {
                            data: 'email'
                        },
                        {
                            data: 'city'
                        },
                        {
                            data: 'start_date'
                        },
                        {
                            data: 'age'
                        },
                        {
                            data: 'status'
                        }
                    ],
                    columnDefs: [{
                        className: 'dt-control',
                        orderable: false,
                        targets: 0,
                        searchable: false,
                        defaultContent: ''
                    }],
                    layout: {
                        topStart: {
                            rowClass: 'row mx-3 my-0 justify-content-between',
                            features: [{
                                pageLength: {
                                    menu: [7, 10, 25, 50, 100],
                                    text: 'Show_MENU_entries'
                                }
                            }]
                        },
                        topEnd: {
                            search: {
                                placeholder: ''
                            }
                        },
                        bottomStart: {
                            rowClass: 'row mx-3 justify-content-between',
                            features: ['info']
                        },
                        bottomEnd: 'paging'
                    },
                    // scrollX: true,
                    language: {
                        paginate: {
                            next: '<i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>',
                            previous: '<i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>',
                            first: '<i class="icon-base ti tabler-chevrons-left scaleX-n1-rtl icon-18px"></i>',
                            last: '<i class="icon-base ti tabler-chevrons-right scaleX-n1-rtl icon-18px"></i>'
                        }
                    }
                });
            }


            const error_table = document.querySelector('.error-table');
            let error_table_child;
            if (error_table) {
                error_table_child = new DataTable(error_table, {
                    ajax: assetsPath + 'json/table-datatable.json',
                    columns: [{
                            data: null
                        },
                        {
                            data: 'full_name'
                        },
                        {
                            data: 'email'
                        },
                        {
                            data: 'city'
                        },
                        {
                            data: 'start_date'
                        },
                        {
                            data: 'age'
                        },
                        {
                            data: 'status'
                        }
                    ],
                    columnDefs: [{
                        className: 'dt-control',
                        orderable: false,
                        targets: 0,
                        searchable: false,
                        defaultContent: ''
                    }],
                    layout: {
                        topStart: {
                            rowClass: 'row mx-3 my-0 justify-content-between',
                            features: [{
                                pageLength: {
                                    menu: [7, 10, 25, 50, 100],
                                    text: 'Show_MENU_entries'
                                }
                            }]
                        },
                        topEnd: {
                            search: {
                                placeholder: ''
                            }
                        },
                        bottomStart: {
                            rowClass: 'row mx-3 justify-content-between',
                            features: ['info']
                        },
                        bottomEnd: 'paging'
                    },
                    // scrollX: true,
                    language: {
                        paginate: {
                            next: '<i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>',
                            previous: '<i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>',
                            first: '<i class="icon-base ti tabler-chevrons-left scaleX-n1-rtl icon-18px"></i>',
                            last: '<i class="icon-base ti tabler-chevrons-right scaleX-n1-rtl icon-18px"></i>'
                        }
                    }
                });
            }

            // Formatting function for row details - modify as you need
            function format(d) {
                // `d` is the original data object for the row
                return (
                    '<dl>' +
                    '<dt>Full name:</dt>' +
                    '<dd>' +
                    d.full_name +
                    '</dd>' +
                    '<dt>Post:</dt>' +
                    '<dd>' +
                    d.post +
                    '</dd>' +
                    '<dt>Salary:</dt>' +
                    '<dd>' +
                    d.salary +
                    '</dd>' +
                    '<dt>Experience:</dt>' +
                    '<dd>' +
                    d.experience +
                    '</dd>' +
                    '</dl>'
                );
            }

            // Add event listener for opening and closing details
            error_table_child.on('click', 'td.dt-control', function(e) {
                let tr = e.target.closest('tr');
                let row = error_table_child.row(tr);

                if (row.child.isShown()) {
                    // This row is already open - close it
                    row.child.hide();
                } else {
                    // Open this row
                    row.child(format(row.data())).show();
                }
            });

             success_table_child.on('click', 'td.dt-control', function(e) {
                let tr = e.target.closest('tr');
                let row = success_table_child.row(tr);

                if (row.child.isShown()) {
                    // This row is already open - close it
                    row.child.hide();
                } else {
                    // Open this row
                    row.child(format(row.data())).show();
                }
            });


        });
    </script>
@endsection
