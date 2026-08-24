@extends('layouts.default')
@section('page_title')
    Kas Besar
@stop
@section('content')
    <div class="mt-6">
        <div class="card">
            <h4 class="card-header">Kas Besar</h4>
            <div class="card-body">
                <!-- Search Form -->
                <form class="dt_adv_search" method="GET" autocomplete="off">
                    <div class="row">
                        <div class="col-12">
                            <div class="row g-3">
                                <div class="col-12 col-sm-6 col-lg-3">
                                    <label class="form-label">No Transaksi:</label>
                                    <input type="text" class="form-control" id="search-transaction-no"
                                        placeholder="Cari No Transaksi" />
                                </div>
                                <div class="col-12 col-sm-6 col-lg-3">
                                    <label class="form-label">Tanggal Mulai:</label>
                                    <input type="date" class="form-control" id="start-date" />
                                </div>
                                <div class="col-12 col-sm-6 col-lg-3">
                                    <label class="form-label">Tanggal Akhir:</label>
                                    <input type="date" class="form-control" id="end-date" />
                                </div>
                                <div class="col-12 col-sm-6 col-lg-3 d-flex align-items-end">
                                    <button type="button" class="btn btn-outline-secondary" id="reset-filter">
                                        <i class="icon-base ti tabler-filter-off me-1"></i> Reset Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <hr class="my-6 mx-n6" />

                <!-- Action Buttons -->
                <div class="mt-4">
                    <div class="d-flex justify-content-end">
                        @can('kas_besar.create')
                            <a href="{{ route('main_transaction.kas_besar.add') }}"
                                class="btn btn-md btn-primary waves-effect waves-light">
                                <span class="icon-xs icon-base ti tabler-plus me-2"></span> Tambah Topup
                            </a>
                        @endcan
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="card-datatable">
                <table id="main-table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>No Transaksi</th>
                            <th>Topup Amount</th>
                            <th>Saldo Before</th>
                            <th>Saldo After</th>
                            <th>Tipe</th>
                            <th>Diinput Oleh</th>
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
            const canRead = @json(auth()->user()->can('kas_besar.view'));

            let dt;

            dt = new DataTable('#main-table', {
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('main_transaction.kas_besar.index_data_table') }}',
                    data: function(d) {
                        d.search_transaction_no = $('#search-transaction-no').val();
                        d.start_date = $('#start-date').val();
                        d.end_date = $('#end-date').val();
                    }
                },
                columns: [
                    {
                        data: 'transaction_date',
                        width: '110px',
                        render: function(data) {
                            if (!data) return '-';
                            return new Date(data).toLocaleDateString('id-ID');
                        }
                    },
                    {
                        data: 'transaction_number',
                        width: '170px',
                        responsivePriority: 1,
                        render: function(data, type, row) {
                            if (!data) return '-';
                            if (canRead) {
                                const url = `{{ route('main_transaction.kas_besar.detail', ':id') }}`.replace(':id', row.id);
                                return `<a href="${url}" class="text-primary fw-semibold">${data}</a>`;
                            }
                            return data;
                        }
                    },
                    {
                        data: 'topup_amount',
                        className: 'text-end',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                const val = parseFloat(data);
                                const cls = val < 0 ? 'text-danger' : 'text-success';
                                return `<span class="${cls}">Rp ${Math.abs(val).toLocaleString('id-ID')}</span>`;
                            }
                            return data;
                        }
                    },
                    {
                        data: 'saldo_before',
                        className: 'text-end',
                        render: function(data) {
                            return 'Rp ' + parseFloat(data || 0).toLocaleString('id-ID');
                        }
                    },
                    {
                        data: 'saldo_after',
                        className: 'text-end',
                        render: function(data) {
                            return '<strong>Rp ' + parseFloat(data || 0).toLocaleString('id-ID') + '</strong>';
                        }
                    },
                    {
                        data: 'type',
                        width: '100px',
                        render: function(data) {
                            if (data === 'TOPUP') {
                                return '<span class="badge bg-label-success">TOPUP</span>';
                            } else if (data === 'DEDUCTION') {
                                return '<span class="badge bg-label-danger">PENGELUARAN</span>';
                            }
                            return data;
                        }
                    },
                    {
                        data: 'created_by_name',
                        render: function(data) {
                            return data || '-';
                        }
                    },
                ],
                layout: {
                    topStart: { rowClass: 'm-0', features: [] },
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
                responsive: true,
                orderCellsTop: true,
                ordering: false
            });

            $('#reset-filter').on('click', function() {
                $('#search-transaction-no').val('');
                $('#start-date').val('');
                $('#end-date').val('');
                dt.draw();
            });

            let searchTimeout;
            $('#search-transaction-no, #start-date, #end-date').on('keyup change', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    dt.draw();
                }, 400);
            });
        });
    </script>
@endsection
