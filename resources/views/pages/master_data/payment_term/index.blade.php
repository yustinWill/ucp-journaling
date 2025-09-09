@extends('layouts.default')
@section('page_title')
    Syarat Pembayaran
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
                                    class="icon-base ti tabler-credit-card icon-28px"></i></span>
                        </div>
                        <h4 class="mb-0">{{ $totalPaymentTerms }}</h4>
                    </div>
                    <p class="mb-1">Total Syarat Pembayaran</p>
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
                        <h4 class="mb-0">{{ $activePaymentTerms }}</h4>
                    </div>
                    <p class="mb-1">Syarat Pembayaran Aktif</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-warning"><i
                                    class="icon-base ti tabler-star icon-28px"></i></span>
                        </div>
                        <h4 class="mb-0">{{ $defaultPaymentTerm }}</h4>
                    </div>
                    <p class="mb-1">Default Payment Term</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-info h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-info"><i
                                    class="icon-base ti tabler-calendar icon-28px"></i></span>
                        </div>
                        <h4 class="mb-0">{{ count($types) }}</h4>
                    </div>
                    <p class="mb-1">Tipe Tersedia</p>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Main Content Card -->
    <div class="mt-6">
        <div class="card">
            <h4 class="card-header">Kelola Syarat Pembayaran</h4>
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
                                    <label class="form-label">Tipe:</label>
                                    <select class="global-select2 form-select" id="type-filter" data-allow-clear="false">
                                        <option value="">Semua Tipe</option>
                                        @foreach ($types as $type)
                                            <option value="{{ $type['value'] }}">{{ $type['label'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-sm-6 col-lg-4">
                                    <label class="form-label">Pencarian:</label>
                                    <input type="text" class="form-control" id="search"
                                        placeholder="Cari Syarat Pembayaran">
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
                            {{-- @if ($canImportPaymentTerm)
                                <a href="{{ route('master_data.payment_term.import') }}"
                                    class="btn btn-md btn-primary waves-effect waves-light"><span
                                        class="icon-xs icon-base ti tabler-file-upload me-2"></span> Import</a>
                            @endif
                            @if ($canExportPaymentTerm)
                                <a href="{{ route('master_data.payment_term.export') }}"
                                    class="btn btn-md btn-primary waves-effect waves-light"><span
                                        class="icon-xs icon-base ti tabler-file-download me-2"></span> Export</a>
                            @endif --}}
                            @if ($canAddPaymentTerm)
                                <a href="{{ route('master_data.payment_term.add') }}"
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
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Hari</th>
                            <th>Tipe</th>
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
            const canDelete = @json($canDelete);
            const canShowEditButton = @json($canShowEditButton);
            const canShowDeleteButton = @json($canShowDeleteButton);

            // Initialize DataTable
            const mainTable = document.querySelector('#main-table');
            let dt_adv_filter;

            if (mainTable) {
                dt_adv_filter = new DataTable(mainTable, {
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: `{{ route('master_data.payment_term.index_data_table') }}`,
                        data: function(d) {
                            d.status = $('#status-filter').val();
                            d.type = $('#type-filter').val();
                            d.search = $('#search').val();
                        },
                        error: function(xhr, error, thrown) {
                            console.error('DataTable AJAX Error:', xhr.responseText);
                            console.error('Error:', error);
                            console.error('Thrown:', thrown);
                        }
                    },
                    columns: [{
                            data: 'created_at',
                            responsivePriority: 2,
                            render: function(data, type, row) {
                                return new Date(data).toLocaleDateString('id-ID') + ' ' + new Date(
                                    data).toLocaleTimeString('id-ID', {
                                    hour: '2-digit',
                                    minute: '2-digit'
                                });
                            }
                        },
                        {
                            data: 'code',
                            responsivePriority: 3
                        },
                        {
                            data: 'name',
                            responsivePriority: 4
                        },
                        {
                            data: 'days',
                            className: 'text-center',
                            responsivePriority: 5,
                            render: function(data, type, row) {
                                return data == 0 ? '-' : `${data} hari`;
                            }
                        },
                        {
                            data: 'type',
                            responsivePriority: 6,
                            render: function(data, type, row) {
                                const typeMap = {
                                    'net': 'Net Payment',
                                    'immediate': 'Immediate Payment',
                                    'cod': 'Cash on Delivery',
                                    'custom': 'Custom Terms'
                                };
                                return typeMap[data] || data.toUpperCase();
                            }
                        },
                        {
                            data: 'status',
                            responsivePriority: 7,
                            render: function(data, type, row) {
                                const badgeClass = data === 'active' ? 'bg-label-success' :
                                    'bg-label-secondary';
                                const statusText = data === 'active' ? 'AKTIF' : 'TIDAK AKTIF';
                                return `<span class="badge ${badgeClass}">${statusText}</span>`;
                            }
                        },
                        {
                            data: null,
                            orderable: false,
                            searchable: false,
                            responsivePriority: 1,
                            width: '120px',
                            render: function(data, type, row, meta) {
                                const detailUrl =
                                    `{{ route('master_data.payment_term.detail', ':id') }}`.replace(
                                        ':id', row.id);
                                const editUrl =
                                    `{{ route('master_data.payment_term.edit', ':id') }}`.replace(
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
                    scrollX: true,
                    responsive: true,
                    orderCellsTop: true,
                    ordering: false
                });
            }

            let searchTimeout;
            $('#search, #status-filter, #type-filter').on('keyup change',
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
