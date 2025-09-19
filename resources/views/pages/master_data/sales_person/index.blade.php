@extends('layouts.default')
@section('page_title')
    Sales Person
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
                        <h4 class="mb-0">0</h4>
                    </div>
                    <p class="mb-1">Total Sales Person</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-success"><i
                                    class="icon-base ti tabler-check icon-28px"></i></span>
                        </div>
                        <h4 class="mb-0">0</h4>
                    </div>
                    <p class="mb-1">Sales Person Aktif</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-warning"><i
                                    class="icon-base ti tabler-percentage icon-28px"></i></span>
                        </div>
                        <h4 class="mb-0">0%</h4>
                    </div>
                    <p class="mb-1">Rata-rata Komisi</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-info h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-info"><i
                                    class="icon-base ti tabler-trending-up icon-28px"></i></span>
                        </div>
                        <h4 class="mb-0">0%</h4>
                    </div>
                    <p class="mb-1">Komisi Tertinggi</p>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Main Content Card -->
    <div class="mt-6">
        <div class="card">
            <h4 class="card-header">Kelola Sales Person</h4>
            <div class="card-body">
                <!-- Advanced Search Form -->
                <form class="dt_adv_search" method="GET" autocomplete="off">
                    <div class="row">
                        <div class="col-12">
                            <div class="row g-3">
                                <div class="col-12 col-sm-6 col-lg-4">
                                    <label class="form-label">Status:</label>
                                    <select class="global-select2 form-select" id="status-filter" data-allow-clear="false">
                                        <option value="">Semua Status</option>
                                        @foreach ($statuses as $status)
                                            <option value="{{ $status['value'] }}">{{ $status['label'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-sm-6 col-lg-4">
                                    <label class="form-label">Pencarian:</label>
                                    <input type="text" class="form-control" id="search"
                                        placeholder="Cari Sales Person">
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
                            {{-- @if ($canShowImportButton)
                                <a href="{{ route('master_data.sales_person.import') }}"
                                    class="btn btn-md btn-primary waves-effect waves-light"><span
                                        class="icon-xs icon-base ti tabler-file-upload me-2"></span> Import</a>
                            @endif
                            @if ($canShowExportButton)
                                <a href="{{ route('master_data.sales_person.export') }}"
                                    class="btn btn-md btn-primary waves-effect waves-light"><span
                                        class="icon-xs icon-base ti tabler-file-download me-2"></span> Export</a>
                            @endif --}}
                            @if ($canShowAddButton)
                                <a href="{{ route('master_data.sales_person.add') }}"
                                    class="btn btn-md btn-primary waves-effect waves-light"><span
                                        class="icon-xs icon-base ti tabler-plus me-2"></span> Tambah Data</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="card-datatable">
                <table id="main-table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Kode Sales</th>
                            <th>Nama Karyawan</th>
                            <th>Komisi (%)</th>
                            <th>Status</th>
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
            // Permission variables
            const canRead = @json($canRead);
            const canWrite = @json($canWrite);
            const canShowEditButton = @json($canShowEditButton);

            // Initialize DataTable
            const mainTable = document.querySelector('#main-table');
            let dt_adv_filter;

            if (mainTable) {
                dt_adv_filter = new DataTable(mainTable, {
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: `{{ route('master_data.sales_person.index_data_table') }}`,
                        data: function(d) {
                            d.status = $('#status-filter').val();
                            d.name = $('#search').val();
                        },
                        error: function(xhr, error, thrown) {
                            console.error('DataTable AJAX Error:', xhr.responseText);
                            console.error('Error:', error);
                            console.error('Thrown:', thrown);
                        }
                    },
                    columns: [{
                            data: 'created_at',
                            // responsivePriority: 2,
                            // render: function(data, type, row) {
                            //     return new Date(data).toLocaleDateString('id-ID') + ' ' + new Date(
                            //         data).toLocaleTimeString('id-ID', {
                            //         hour: '2-digit',
                            //         minute: '2-digit'
                            //     });
                            // }
                        },
                        {
                            data: 'code',
                            // responsivePriority: 3
                        },
                        {
                            data: 'employee_name',
                            // responsivePriority: 4
                        },
                        {
                            data: 'commission_rate',
                            className: 'text-end',
                            // responsivePriority: 5,
                            render: function(data, type, row) {
                                return `${parseFloat(data).toFixed(2)}%`;
                            }
                        },
                        {
                            data: 'status',
                            // responsivePriority: 6,
                            render: function(data, type, row) {
                                const badgeClass = data === 'ACTIVE' ? 'bg-label-success' :
                                    'bg-label-secondary';
                                const statusText = data === 'ACTIVE' ? 'ACTIVE' : 'INACTIVE';
                                return `<span class="badge ${badgeClass}">${statusText}</span>`;
                            }
                        },
                        {
                            data: null,
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row, meta) {
                                const detailUrl =
                                    `{{ route('master_data.sales_person.detail', ':id') }}`.replace(
                                        ':id', row.id);
                                const editUrl =
                                    `{{ route('master_data.sales_person.edit', ':id') }}`.replace(
                                        ':id', row.id);
                                let actions = '<div class="d-flex gap-2">';

                                // Always show detail button if user has read permission
                                if (canRead) {
                                    actions += `<a href="${detailUrl}" class="btn btn-sm btn-primary waves-effect waves-light" title="Detail">
                                        <i class="icon-xs icon-base ti tabler-eye"></i>
                                    </a>`;
                                }

                                if (canShowEditButton) {
                                    actions += `<a href="${editUrl}" class="btn btn-sm btn-primary waves-effect waves-light" title="Edit">
                                        <i class="icon-xs icon-base ti tabler-edit"></i>
                                    </a>`;
                                }


                                actions += '</div>';
                                return actions;
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
                    orderCellsTop: true,
                    ordering: false
                });
            }

            let searchTimeout;
            $('#search, #status-filter').on('keyup change',
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
