@extends('layouts.default')
@section('page_title')
    Keuntungan Penjualan
@stop
@section('content')
    <!-- Summary Cards -->
    <div class="row">
        <div class="col-lg-6 col-sm-6">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-primary"><i
                                    class="icon-base ti tabler-chart-line icon-28px"></i></span>
                        </div>
                        <h4 class="mb-0">{{ $summaryCards['total_cycles'] }}</h4>
                    </div>
                    <p class="mb-1">Total Cycle</p>
                    <small class="text-muted">Total Cycle Keuntungan</small>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-sm-6">
            <div class="card card-border-shadow-success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-success"><i
                                    class="icon-base ti tabler-coins icon-28px"></i></span>
                        </div>
                        <h4 class="mb-0">Rp {{ number_format($summaryCards['total_keuntungan'], 0, ',', '.') }}</h4>
                    </div>
                    <p class="mb-1">Total Keuntungan</p>
                    <small class="text-muted">Akumulasi Keuntungan</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="mt-6">
        <div class="card">
            <h4 class="card-header">Kelola Cycle Keuntungan Penjualan</h4>
            <div class="card-body">
                <!-- Advanced Search Form -->
                <form class="dt_adv_search" method="GET" autocomplete="off">
                    <div class="row">
                        <div class="col-12">
                            <div class="row g-3">
                                <div class="col-12 col-sm-6 col-lg-3">
                                    <label class="form-label">Status:</label>
                                    <select class="global-select2 form-select" id="status-filter" data-allow-clear="false">
                                        <option value="">Semua Status</option>
                                        @foreach ($statuses as $status)
                                            <option value="{{ $status['value'] }}">{{ $status['label'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-sm-6 col-lg-3">
                                    <label class="form-label">Nama Cycle:</label>
                                    <input type="text" class="form-control" id="cycle-name-filter" placeholder="Cari Berdasarkan Nama Cycle">
                                </div>
                                <div class="col-12 col-sm-6 col-lg-3">
                                    <label class="form-label">Periode:</label>
                                    <input type="text" class="form-control" id="periode-filter" placeholder="Cari Berdasarkan Periode">
                                </div>
                                <div class="col-12 col-sm-6 col-lg-3">
                                    <label class="form-label">Total Minimal:</label>
                                    <input type="text" class="form-control" id="total-minimal-filter" placeholder="Cari Berdasarkan Total Minimal">
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
                            
                            @can('revenue_split.create')
                                <a href="{{ route('master_data.revenue_split.add') }}"
                                    class="btn btn-md btn-primary waves-effect waves-light"><span
                                        class="icon-xs icon-base ti tabler-plus me-2"></span> Tambah Cycle</a>
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
                            <th>Tanggal</th>
                            <th>Nama Cycle</th>
                            <th>Periode</th>
                            <th>Target Keuntungan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Cycle Preview Modal -->
    <div class="modal fade" id="cyclePreviewModal" tabindex="-1" aria-labelledby="cyclePreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cyclePreviewModalLabel">Preview Cycle Keuntungan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="cyclePreviewContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
@stop

@section('js_script')
    <script>
        $(function() {
            // Permission variables
            const canRead = @json(auth()->user()->can('revenue_split.view'));
            const canWrite = @json(auth()->user()->can('revenue_split.create'));
            const canDelete = @json(auth()->user()->can('revenue_split.delete'));
            const canShowEditButton = @json(auth()->user()->can('revenue_split.edit'));
            const canShowDeleteButton = @json(auth()->user()->can('revenue_split.delete'));

            // Initialize DataTable
            const mainTable = document.querySelector('#main-table');
            let dt_adv_filter;

            if (mainTable) {
                dt_adv_filter = new DataTable(mainTable, {
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: `{{ route('master_data.revenue_split.index_data_table') }}`,
                        data: function(d) {
                            d.status = $('#status-filter').val();
                            d.cycle_name = $('#cycle-name-filter').val();
                            d.periode = $('#periode-filter').val();
                            d.total_minimal = $('#total-minimal-filter').val();
                        }
                    },
                    columns: [{
                            data: 'created_at',
                            responsivePriority: 5,
                            render: function(data, type, row) {
                                return new Date(data).toLocaleDateString('id-ID', {
                                    day: '2-digit',
                                    month: '2-digit',
                                    year: 'numeric'
                                });
                            }
                        },
                        {
                            data: 'cycle_name',
                            responsivePriority: 2,
                            render: function(data, type, row) {
                                return `<div>
                                    <strong>${data}</strong>
                                    <br><small class="text-muted">${row.description || ''}</small>
                                </div>`;
                            }
                        },
                        {
                            data: null,
                            responsivePriority: 3,
                            render: function(data, type, row) {
                                const startDate = new Date(row.start_date).toLocaleDateString('id-ID', {
                                    day: '2-digit',
                                    month: 'short',
                                    year: 'numeric'
                                });
                                const endDate = new Date(row.end_date).toLocaleDateString('id-ID', {
                                    day: '2-digit',
                                    month: 'short',
                                    year: 'numeric'
                                });
                                return `${startDate} - ${endDate}`;
                            }
                        },
                        {
                            data: 'total_target',
                            responsivePriority: 4,
                            render: function(data, type, row) {
                                return `<div class="text-end">
                                    <strong>Rp ${Number(data).toLocaleString('id-ID')}</strong>
                                    <br><small class="text-muted">
                                        Pabrik: Rp ${Number(row.target_pabrik).toLocaleString('id-ID')}<br>
                                        Owner: Rp ${Number(row.target_owner).toLocaleString('id-ID')}<br>
                                        Perusahaan: Rp ${Number(row.target_perusahaan).toLocaleString('id-ID')}
                                    </small>
                                </div>`;
                            }
                        },
                        {
                            data: 'status',
                            responsivePriority: 3,
                            render: function(data, type, row) {
                                const badgeClass = data === 'ACTIVE' ? 'bg-label-warning' : 
                                                   data === 'SELESAI' ? 'bg-label-success' : 'bg-label-secondary';
                                return `<span class="badge ${badgeClass}">${data}</span>`;
                            }
                        },
                        {
                            data: null,
                            orderable: false,
                            searchable: false,
                            responsivePriority: 1,
                            width: '120px',
                            render: function(data, type, row, meta) {
                                const detailUrl = `{{ route('master_data.revenue_split.detail', ':id') }}`
                                    .replace(':id', row.id);
                                const editUrl = `{{ route('master_data.revenue_split.edit', ':id') }}`
                                    .replace(':id', row.id);
                                
                                let actions = '<div class="d-flex gap-1">';

                                // Primary action: View Detail (always visible if can read)
                                if (canRead) {
                                    actions += `<a href="${detailUrl}" class="btn btn-primary btn-sm" title="Lihat Detail">
                                        <i class="icon-xs icon-base ti tabler-eye"></i>
                                    </a>`;
                                }

                                // Secondary actions in dropdown if more than 1 additional action
                                let additionalActions = 0;
                                if (canShowEditButton) additionalActions++;
                                
                                if (additionalActions > 0) {
                                    actions += '<div class="btn-group" role="group">';
                                    actions += '<button type="button" class="btn btn-label-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">';
                                    actions += '<i class="icon-xs icon-base ti tabler-dots-vertical"></i>';
                                    actions += '</button>';
                                    actions += '<ul class="dropdown-menu">';

                                    // // Preview Action
                                    // actions += `<li><a class="dropdown-item" href="javascript:void(0)" onclick="showCyclePreview(${row.id})">
                                    //     <i class="icon-xs icon-base ti tabler-chart-line me-2"></i>Preview Cycle
                                    // </a></li>`;

                                    if (canShowEditButton) {
                                        actions += `<li><a class="dropdown-item" href="${editUrl}">
                                            <i class="icon-xs icon-base ti tabler-edit me-2"></i>Edit
                                        </a></li>`;
                                    }

                                    actions += '</ul></div>';
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
            $('#status-filter, #cycle-name-filter, #periode-filter, #total-minimal-filter').on('keyup change',
                function() {
                    clearTimeout(searchTimeout);
                    const input = this;
                    searchTimeout = setTimeout(function() {
                        if (dt_adv_filter) {
                            dt_adv_filter.draw();
                        }
                    }, 400); // 400ms delay
                });
        });

        // Function to show cycle preview
        function showCyclePreview(cycleId) {
            $('#cyclePreviewContent').html('<div class="text-center p-4"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>');
            $('#cyclePreviewModal').modal('show');
            
            // Load cycle preview data
            fetch(`/master_data/revenue_split/preview/${cycleId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        $('#cyclePreviewModalLabel').text(`Preview ${data.cycle.cycle_name}`);
                        $('#cyclePreviewContent').html(data.html);
                    } else {
                        $('#cyclePreviewContent').html('<div class="alert alert-danger">Error loading preview data</div>');
                    }
                })
                .catch(error => {
                    $('#cyclePreviewContent').html('<div class="alert alert-danger">Error loading preview data</div>');
                });
        }
    </script>
@endsection