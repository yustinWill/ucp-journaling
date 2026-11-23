@extends('layouts.default')
@section('page_title')
    Kas Besar
@stop
@section('content')
    <div class="mt-6">
        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
                <h4 class="mb-0">Kas Besar</h4>
                <div class="text-end">
                    <small class="text-muted d-block">Saldo Saat Ini</small>
                    <h4 class="mb-0 text-primary" id="sum-current">-</h4>
                </div>
            </div>
            <div class="card-body">
                <!-- Search Form -->
                <form class="dt_adv_search" method="GET" autocomplete="off">
                    <div class="row">
                        <div class="col-12">
                            <div class="row g-3">
                                <div class="col-12 col-sm-6 col-lg-3">
                                    <label class="form-label">No Transaksi:</label>
                                    <input type="text" class="form-control" id="search-transaction-no"
                                        placeholder="No Kas Besar / Pengeluaran / Reimbursement" />
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

                <!-- Saldo awal + masuk - keluar = saldo akhir, for the dates in the filter -->
                <small class="text-muted d-block mb-2">Ringkasan periode: <span id="sum-period">Semua tanggal</span></small>
                <div class="row g-3">
                    <div class="col-6 col-lg-3">
                        <div class="border rounded p-3 h-100">
                            <small class="text-muted d-block">Saldo Awal</small>
                            <h5 class="mb-0" id="sum-opening">-</h5>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="border rounded p-3 h-100">
                            <small class="text-muted d-block">Masuk</small>
                            <h5 class="mb-0 text-success" id="sum-in">-</h5>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="border rounded p-3 h-100">
                            <small class="text-muted d-block">Keluar</small>
                            <h5 class="mb-0 text-danger" id="sum-out">-</h5>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="border rounded p-3 h-100">
                            <small class="text-muted d-block">Saldo Akhir</small>
                            <h5 class="mb-0" id="sum-closing">-</h5>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-6">
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
                            <th>Keterangan</th>
                            <th class="text-end">Masuk</th>
                            <th class="text-end">Keluar</th>
                            <th class="text-end">Saldo</th>
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

            function rp(num) {
                return 'Rp ' + parseFloat(num || 0).toLocaleString('id-ID');
            }

            function esc(text) {
                return $('<div>').text(text).html();
            }

            function fmtDate(date) {
                return new Date(date + 'T00:00:00').toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
            }

            function showSummary(sum) {
                const start = $('#start-date').val();
                const end = $('#end-date').val();
                $('#sum-period').text(
                    start && end ? `${fmtDate(start)} – ${fmtDate(end)}` :
                    start ? `sejak ${fmtDate(start)}` :
                    end ? `sampai ${fmtDate(end)}` : 'Semua tanggal'
                );
                $('#sum-current').text(rp(sum.current));
                $('#sum-opening').text(rp(sum.opening));
                $('#sum-in').text(rp(sum.in));
                $('#sum-out').text(rp(sum.out));
                $('#sum-closing').text(rp(sum.closing));
            }

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
                        data: 'notes',
                        render: function(data, type, row) {
                            if (row.f_expense_id) {
                                const url = `{{ route('main_transaction.expense.detail', ':id') }}`.replace(':id', row.f_expense_id);
                                return `Pengeluaran <a href="${url}">${esc(row.expense_transaction_no || '')}</a>`;
                            }
                            if (row.reimbursement_id) {
                                const url = `{{ route('main_transaction.reimbursement_approval.detail', ':id') }}`.replace(':id', row.reimbursement_id);
                                return `Reimbursement <a href="${url}">${esc(row.reimbursement_code || '')}</a>`;
                            }
                            if (parseFloat(row.topup_amount) < 0) {
                                return esc(data || 'Pengeluaran');
                            }
                            return data ? `Topup <span class="text-muted">· ${esc(data)}</span>` : 'Topup';
                        }
                    },
                    {
                        data: 'topup_amount',
                        className: 'text-end text-nowrap',
                        render: function(data) {
                            return parseFloat(data) > 0 ? `<span class="text-success">${rp(data)}</span>` : '';
                        }
                    },
                    {
                        data: 'topup_amount',
                        className: 'text-end text-nowrap',
                        render: function(data) {
                            return parseFloat(data) < 0 ? `<span class="text-danger">${rp(-data)}</span>` : '';
                        }
                    },
                    {
                        data: 'saldo_after',
                        className: 'text-end text-nowrap fw-semibold',
                        render: function(data) {
                            return rp(data);
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

            dt.on('xhr', function() {
                const json = dt.ajax.json();
                if (json && json.summary) showSummary(json.summary);
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
