@extends('layouts.default')
@section('page_title')
    List Kontak
@stop
@section('content')
    <!-- Summary Cards -->
    {{-- <div class="row">
        <div class="col-lg-4 col-sm-6">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-primary"><i
                                    class="icon-base ti tabler-file-invoice icon-28px"></i></span>
                        </div>
                        <h4 class="mb-0">1,247</h4>
                    </div>
                    <p class="mb-1">Total Customer Aktif</p>
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
                        <h4 class="mb-0">89</h4>
                    </div>
                    <p class="mb-1">Supplier Aktif</p>
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
                        <h4 class="mb-0">47</h4>
                    </div>
                    <p class="mb-1">Kontak Baru Bulan Ini</p>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Main Content Card -->
    <div class="card mt-6">
        <h4 class="card-header">Kelola Kontak</h4>
        <div class="card-body">
            <!-- Advanced Search Form -->
            <form class="dt_adv_search" method="GET" autocomplete="off">
                <div class="row">
                    <div class="col-12">
                        <div class="row g-3">
                            <div class="col-12 col-sm-6 col-lg-4">
                                <label class="form-label">Nama Kontak:</label>
                                <input type="text" class="form-control dt-input dt-full-name" data-column="1"
                                    placeholder="Cari Berdasarkan Nama Kontak" id="contact-name" data-column-index="0" />
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <label class="form-label">Nama Perusahaan:</label>
                                <input type="text" class="form-control dt-input" data-column="2"
                                    placeholder="Cari Berdasarkan Nama Perusahaan" id="contact-company"
                                    data-column-index="1" />
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <label class="form-label">Email:</label>
                                <input type="text" class="form-control dt-input" data-column="3"
                                    placeholder="Cari Berdasarkan Email" id="contact-email" data-column-index="2" />
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <label class="form-label">Phone:</label>
                                <input type="text" class="form-control dt-input" data-column="4"
                                    placeholder="Cari Berdasarkan Phone" id="contact-phone" data-column-index="3" />
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <label class="form-label">Group:</label>
                                <input type="text" class="form-control dt-input" data-column="4"
                                    placeholder="Cari Berdasarkan Group" id="contact-group" data-column-index="3" />
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <label class="form-label">Tipe:</label>
                                <select class="global-select2 form-select" id="contact-type" data-allow-clear="true">
                                    <option value="">Semua Tipe</option>
                                    @foreach ($contact_types as $type)
                                        <option value="{{ $type->code }}">{{ $type->name }}</option>
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
                        <a href="{{ route('master_data.contact.import') }}"
                            class="btn btn-md btn-primary waves-effect waves-light"><span
                                class="icon-xs icon-base ti tabler-file-upload me-2"></span> Import</a>
                        <a href="{{ route('master_data.contact.export') }}"
                            class="btn btn-md btn-primary waves-effect waves-light"><span
                                class="icon-xs icon-base ti tabler-file-download me-2"></span> Export</a>
                        <a href="{{ route('master_data.contact.add') }}"
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
                        <th>Nama Kontak</th>
                        <th>Nama Perusahaan</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Group</th>
                        <th>Tag</th>
                        <th>Tipe</th>
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
                        url: `{{ route('master_data.contact.index_data_table') }}`,
                        data: function(d) {
                            d.contact_name = $('#contact-name').val();
                            d.company_name = $('#contact-company').val();
                            d.email = $('#contact-email').val();
                            d.phone = $('#contact-phone').val();
                            d.group_name = $('#contact-group').val();
                            d.type = $('#contact-type').val();
                        }
                    },
                    columns: [{
                            data: 'contact_name',
                            responsivePriority: 3
                        },
                        {
                            data: 'company_name',
                            responsivePriority: 4
                        },
                        {
                            data: 'email',
                            responsivePriority: 7
                        },
                        {
                            data: 'phone',
                            responsivePriority: 6
                        },
                        {
                            data: 'group_name',
                            responsivePriority: 8
                        },
                        {
                            data: 'tags',
                            responsivePriority: 5,
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
                            data: 'type',
                            responsivePriority: 9
                        },
                        {
                            data: null,
                            responsivePriority: 1,
                            width: '120px'
                        }
                    ],
                    columnDefs: [{
                            orderable: false,
                            targets: 6, // Type column (updated index due to added tag column)
                            render: function(data, type, full, meta) {
                                return data; // Plain text for type
                            }
                        },
                        {
                            targets: -1,
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row, meta) {
                                const detailUrl = "{{ route('master_data.contact.detail', ':id') }}"
                                    .replace(':id', row.id);
                                const editUrl = "{{ route('master_data.contact.edit', ':id') }}"
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

            // Tag Column Toggle Functionality
            $('#toggleTagColumn').on('change', function() {
                const tagColumn = dt_adv_filter.column(5); // Tag column index (0-based)
                tagColumn.visible(this.checked);
            });

            // Initialize tag column visibility based on checkbox state
            const initialTagVisibility = $('#toggleTagColumn').is(':checked');
            dt_adv_filter.column(5).visible(initialTagVisibility);

            let searchTimeout;
            $('#contact-name, #contact-company, #contact-email, #contact-group, #contact-phone, #contact-type').on(
                'keyup change',
                function() {
                    clearTimeout(searchTimeout);
                    const input = this;
                    searchTimeout = setTimeout(function() {
                        if (dt_adv_filter) {
                            dt_adv_filter.search(input.value).draw();
                        }
                    }, 400); // 400ms delay
                });
        });
    </script>
@endsection
