@extends('layouts.default')
@section('page_title')
    Top Up Pembayaran
@stop
@section('content')
    <!-- Page Header -->
    <div class="mb-6">
        <h4 class="mb-1">
            <i class="icon-base ti tabler-cash me-2"></i>Top Up Pembayaran Pabrik
        </h4>
        <p class="text-muted">Kelola pembayaran lump-sum untuk piutang Pabrik dengan alokasi otomatis FIFO</p>
    </div>

    <!-- Filter Bar -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('main_transaction.topup.index') }}" id="filter-form">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Entity</label>
                        <select name="entity_type" class="form-select">
                            <option value="">All Entities</option>
                            <option value="KINGDOM" {{ request('entity_type') == 'KINGDOM' ? 'selected' : '' }}>Kingdom</option>
                            <option value="OWNER" {{ request('entity_type') == 'OWNER' ? 'selected' : '' }}>Owner</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date', date('Y-m-01')) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date', date('Y-m-d')) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="OPEN" {{ request('status') == 'OPEN' ? 'selected' : '' }}>Open</option>
                            <option value="PARTIAL" {{ request('status') == 'PARTIAL' ? 'selected' : '' }}>Partial</option>
                            <option value="PAID" {{ request('status') == 'PAID' ? 'selected' : '' }}>Paid</option>
                        </select>
                    </div>
                </div>
                <div class="mt-3 d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">
                        <i class="icon-base ti tabler-filter me-1"></i> Apply Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Top Up List Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="icon-base ti tabler-list me-2"></i>Riwayat Top Up
            </h5>
            
            <a href="{{ route('main_transaction.topup.add') }}" class="btn btn-success">
                <i class="icon-base ti tabler-plus me-1"></i> New Top Up
            </a>
        </div>

        <div class="card-datatable">
            <table id="topup-table" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Entity</th>
                        <th>Top Up Reference</th>
                        <th>Top Up Amount</th>
                        <th>Allocated Amount</th>
                        <th>Remaining Balance</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@stop

@section('js_script')
    <script>
        $(function() {
            // Define permissions
            const canEdit = @json($canEdit ?? false);
            const canDelete = @json($canDelete ?? false);

            // Initialize DataTable
            const topupTable = new DataTable('#topup-table', {
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('main_transaction.topup.index_data_table') }}",
                    data: function(d) {
                        d.entity_type = $('select[name="entity_type"]').val();
                        d.start_date = $('input[name="start_date"]').val();
                        d.end_date = $('input[name="end_date"]').val();
                        d.status = $('select[name="status"]').val();
                    },
                    error: function(xhr, error, code) {
                        console.log('DataTable Ajax Error:', xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal memuat data tabel',
                            showConfirmButton: true
                        });
                    }
                },
                columns: [
                    {
                        data: 'topup_date',
                        render: function(data) {
                            if (!data) return '-';
                            return new Date(data).toLocaleDateString('id-ID');
                        }
                    },
                    {
                        data: 'entity_type',
                        render: function(data) {
                            if (data === 'KINGDOM') {
                                return '<span class="badge" style="background-color: #f3e8ff; color: #9333ea; border: 1px solid #9333ea;">🏪 Kingdom</span>';
                            } else if (data === 'OWNER') {
                                return '<span class="badge" style="background-color: #ffedd5; color: #ea580c; border: 1px solid #ea580c;">👤 Owner</span>';
                            }
                            return data;
                        }
                    },
                    {
                        data: 'topup_number',
                        render: function(data, type, row) {
                            const detailUrl = "{{ route('main_transaction.topup.detail', ':id') }}".replace(':id', row.id);
                            return `<a href="${detailUrl}" class="fw-semibold text-primary">${data}</a>`;
                        }
                    },
                    {
                        data: 'topup_amount',
                        render: function(data) {
                            return '<strong>Rp ' + parseFloat(data).toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0}) + '</strong>';
                        }
                    },
                    {
                        data: 'allocated_amount',
                        render: function(data) {
                            return 'Rp ' + parseFloat(data).toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0});
                        }
                    },
                    {
                        data: 'remaining_balance',
                        render: function(data) {
                            const balance = parseFloat(data);
                            const color = balance > 0 ? 'text-success' : 'text-muted';
                            return `<span class="${color}">Rp ${balance.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0})}</span>`;
                        }
                    },
                    {
                        data: 'status',
                        width: '100px',
                        render: function(data) {
                            if (data === 'PAID') {
                                return '<span class="badge bg-label-success"><i class="icon-xs ti ti-check"></i> PAID</span>';
                            } else if (data === 'PARTIAL') {
                                return '<span class="badge bg-label-warning"><i class="icon-xs ti ti-clock"></i> PARTIAL</span>';
                            } else if (data === 'OPEN') {
                                return '<span class="badge bg-label-danger"><i class="icon-xs ti ti-circle"></i> OPEN</span>';
                            }
                            return data;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        responsivePriority: 1,
                        width: '100px',
                        render: function(data, type, row, meta) {
                            const detailUrl = "{{ route('main_transaction.topup.detail', ':id') }}".replace(':id', row.id);

                            // Only show Detail button - Edit functionality disabled
                            return `<a href="${detailUrl}" class="btn btn-sm btn-primary waves-effect waves-light" title="Detail">
                                        <i class="icon-base ti tabler-eye"></i> Detail
                                    </a>`;
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
                responsive: true,
                orderCellsTop: true,
                ordering: false
            });

            // Auto-reload table when filter changes
            let filterTimeout;
            $('#filter-form select, #filter-form input').on('change', function() {
                clearTimeout(filterTimeout);
                filterTimeout = setTimeout(function() {
                    if (topupTable) {
                        topupTable.draw();
                    }
                }, 300);
            });
        });
    </script>
@endsection
