@extends('layouts.default')
@section('page_title')
    List Tag
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
    </div> --}}

    <!-- Main Content Card -->
    <div class="mt-6">
        <div class="card">
            <h4 class="card-header">Kelola Tag</h4>
            <div class="card-body">
                <!-- Advanced Search Form -->
                <form class="dt_adv_search" method="GET" autocomplete="off">
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
                                    <select class="global-select2 form-select" data-allow-clear="true">
                                        <option value="">Semua Warna</option>
                                        @foreach ($availableColors as $color)
                                            <option value="{{ $color->id }}">{{ $color->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-sm-6 col-lg-3">
                                    <label class="form-label">Status:</label>
                                    <select class="global-select2 form-select" data-allow-clear="true">
                                        <option value="">Semua Status</option>
                                        @foreach ($availableStatus as $status)
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
                            @can('tag.create')
                                <a href="{{ route('master_data.tag.add') }}"
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
            // Permission variables - using Spatie permissions
            const canRead = @json(auth()->user()->can('tag.view'));
            const canWrite = @json(auth()->user()->can('tag.create'));
            const canDelete = @json(auth()->user()->can('tag.delete'));
            const canShowEditButton = @json(auth()->user()->can('tag.edit'));
            const canShowDeleteButton = @json(auth()->user()->can('tag.delete'));

            //DataTable
            const main_table = document.querySelector('#main-table');
            let dt_adv_filter;

            if (main_table) {
                dt_adv_filter = new DataTable(main_table, {
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: `{{ route('master_data.tag.index_data_table') }}`,
                        data: function(d) {
                            d.status = $('select.form-select').eq(1).val();
                            d.f_tag_color_id = $('select.form-select').eq(0).val();
                            d.name = $('input[data-column="2"]').val();
                            d.code = $('input[data-column="1"]').val();
                        }
                    },
                    columns: [{
                            data: 'created_at'
                        },
                        {
                            data: 'code'
                        },
                        {
                            data: 'name'
                        },
                        {
                            data: 'tag_color'
                        },
                        {
                            data: 'description'
                        },
                        {
                            data: 'status'
                        },
                        {
                            data: 'usage_count'
                        },
                        {
                            data: 'created_by_name',
                            render: function(data) {
                                return data || '-';
                            }
                        },
                        {
                            data: 'id'
                        }
                    ],
                    columnDefs: [{
                            targets: -1, // Action column (last column)
                            orderable: false,
                            searchable: false,
                            width: '120px',
                            responsivePriority: 1,
                            render: function(data, type, row, meta) {
                                const editUrl = `{{ route('master_data.tag.edit', ':id') }}`
                                    .replace(':id', row.id);
                                const detailUrl = `{{ route('master_data.tag.detail', ':id') }}`
                                    .replace(':id', row.id);

                                let buttons = `<div class="d-flex gap-2">`;
                                
                                if (canRead) {
                                    buttons += `<a href="${detailUrl}" class="btn btn-sm btn-primary waves-effect waves-light" title="Detail">
                                        <i class="icon-base ti tabler-eye"></i>
                                    </a>`;
                                }
                                
                                if (canShowEditButton) {
                                    buttons += `<a href="${editUrl}" class="btn btn-sm btn-primary waves-effect waves-light" title="Edit">
                                        <i class="icon-base ti tabler-edit"></i>
                                    </a>`;
                                }
                                
                                buttons += `</div>`;
                                return buttons;
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
                            targets: [1], // Code column
                            responsivePriority: 3
                        },
                        {
                            targets: [3], // Color column
                            render: function(data, type, row) {
                                if (!data) return '';
                                const parts = data.split('|');
                                const preview = parts[0]?.trim().toLowerCase() || 'secondary';
                                const label = parts[1]?.trim() || preview;
                                return `<span class="badge ${preview} me-1">${label}</span>`;
                            }
                        },
                        {
                            targets: [5], // Status column
                            render: function(data, type, row) {
                                return data; // Plain text for status
                            }
                        },
                        {
                            targets: [6], // Usage count column
                            className: 'text-center',
                            render: function(data, type, row) {
                                return data; // Plain text for count
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

            let searchTimeout;
            $('.dt-input, .global-select2').on('keyup change',
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
