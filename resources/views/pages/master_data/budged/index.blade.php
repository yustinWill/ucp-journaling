@extends('layouts.default')
@section('page_title')
    Data Anggaran
@stop
@section('content')
    <!-- Main Content Card -->
    <div class="mt-6">
        <div class="card">
            <h4 class="card-header">Kelola Anggaran Berdasarkan Periode</h4>
            <div class="card-body">
                <!-- Advanced Search Form -->
                <form class="dt_adv_search" method="GET" autocomplete="off">
                    <div class="row">
                        <div class="col-12">
                            <div class="row g-3">
                                <div class="col-12 col-sm-6 col-lg-4">
                                    <label class="form-label">Pencarian:</label>
                                    <input type="text" class="form-control" id="search" placeholder="Cari berdasarkan periode atau anggaran">
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
                            @if (true)
                                <button type="button" id="btn-add-budged"
                                    class="btn btn-md btn-primary waves-effect waves-light"><span
                                        class="icon-xs icon-base ti tabler-plus me-2"></span> Tambah Periode & Anggaran</button>
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
                            <th>Periode</th>
                            <th>Tipe Entity</th>
                            <th>Jumlah Anggaran</th>
                            <th>Total Limit</th>
                            <th>Total Sisa</th>
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
            const canRead = true;
            const canWrite = true;
            const canDelete = true;
            const canShowEditButton = true;
            const canShowDeleteButton = true;

            let dt_adv_filter;

            // Initialize DataTable
            dt_adv_filter = new DataTable('#main-table', {
                processing: true,
                serverSide: true,
                ordering: false,
                ajax: {
                    url: `{{ route('master_data.budged.index-data-table') }}`,
                    data: function(d) {
                        d.search = $('#search').val();
                        return d;
                    }
                },
                columns: [{
                        data: 'period',
                        responsivePriority: 2,
                        render: function(data, type, row) {
                            const startDate = moment(row.period_start_date).format('DD/MM/YYYY');
                            const endDate = moment(row.period_end_date).format('DD/MM/YYYY');
                            return `${startDate} - ${endDate}`;
                        }
                    },
                    {
                        data: 'type',
                        responsivePriority: 3,
                        className: 'text-center',
                        render: function(data, type, row) {
                            if (data === 'owner') {
                                return '<span class="badge bg-label-primary">Owner</span>';
                            } else {
                                return '<span class="badge bg-label-info">Kingdom</span>';
                            }
                        }
                    },
                    {
                        data: 'budget_count',
                        responsivePriority: 4,
                        className: 'text-center'
                    },
                    {
                        data: 'total_limit',
                        responsivePriority: 5,
                        className: 'text-end',
                        render: function(data) {
                            return data ? 'Rp ' + new Intl.NumberFormat('id-ID').format(data) : 'Rp 0';
                        }
                    },
                    {
                        data: 'total_remaining',
                        responsivePriority: 6,
                        className: 'text-end',
                        render: function(data) {
                            return data ? 'Rp ' + new Intl.NumberFormat('id-ID').format(data) : 'Rp 0';
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
                                `{{ route('master_data.budged.detail', ':id') }}`.replace(
                                    ':id', row.period_id);
                            const editUrl = `{{ route('master_data.budged.edit', ':id') }}`
                                .replace(':id', row.period_id);
                            let actions = '<div class="d-flex gap-2">';

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
                responsive: true
            });

            // Button click event
            $('#btn-add-budged').on('click', function() {
                window.location.href = "{{ route('master_data.budged.add') }}";
            });

            // Search functionality with debounce
            let searchTimeout;
            $('#search').on('keyup change', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    if (dt_adv_filter) {
                        dt_adv_filter.draw();
                    }
                }, 400); // 400ms delay
            });
        });
    </script>
@endsection
