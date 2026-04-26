@extends('layouts.default')
@section('page_title')
    My Reimbursements
@stop
@section('content')
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar me-3">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="icon-base ti tabler-clock icon-24px"></i>
                            </span>
                        </div>
                        <div>
                            <small class="text-muted d-block">Pending</small>
                            <h4 class="mb-0">{{ $stats['total_pending'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar me-3">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="icon-base ti tabler-check icon-24px"></i>
                            </span>
                        </div>
                        <div>
                            <small class="text-muted d-block">Approved</small>
                            <h4 class="mb-0">{{ $stats['total_approved'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar me-3">
                            <span class="avatar-initial rounded bg-label-danger">
                                <i class="icon-base ti tabler-x icon-24px"></i>
                            </span>
                        </div>
                        <div>
                            <small class="text-muted d-block">Rejected</small>
                            <h4 class="mb-0">{{ $stats['total_rejected'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar me-3">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="icon-base ti tabler-currency-dollar icon-24px"></i>
                            </span>
                        </div>
                        <div>
                            <small class="text-muted d-block">Total Approved</small>
                            <h5 class="mb-0">Rp {{ number_format($stats['total_amount_approved'], 0, ',', '.') }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="card">
        <h4 class="card-header">My Reimbursements</h4>
        <div class="card-body">
            <!-- Filters -->
            <div class="row g-3 mb-4">
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label">Dari Tanggal</label>
                    <input type="text" class="form-control global-single-date-picker" id="date-from" placeholder="yyyy-mm-dd">
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="text" class="form-control global-single-date-picker" id="date-to" placeholder="yyyy-mm-dd">
                </div>
            </div>

            <div class="d-flex justify-content-between mb-4">
                <div></div>
                @can('reimbursement.create')
                <a href="{{ route('main_transaction.reimbursement.add') }}" class="btn btn-primary">
                    <i class="icon-base ti tabler-plus me-1"></i> Ajukan Reimbursement Baru
                </a>
                @endcan
            </div>
        </div>

        <!-- Data Table -->
        <div class="card-datatable">
            <table id="reimbursement-table" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Kode</th>
                        <th>Nama Reimbursement</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Budget Category</th>
                        <th>Diajukan oleh</th>
                        <th>DiApprove oleh</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@stop

@section('js_script')
    <script>
        $(function() {
            const canRead = @json(auth()->user()->can('reimbursement.view'));

            // Calculate smart default date range
            function getDefaultDateRange() {
                const today = new Date();
                const dayOfWeek = today.getDay(); // 0=Sun, 1=Mon, ..., 6=Sat
                let dateFrom;

                if (dayOfWeek === 1) {
                    // Today is Monday: go back 7 days
                    dateFrom = new Date(today);
                    dateFrom.setDate(today.getDate() - 7);
                } else {
                    // Find the most recent Monday
                    const daysFromMonday = (dayOfWeek === 0) ? 6 : dayOfWeek - 1;
                    dateFrom = new Date(today);
                    dateFrom.setDate(today.getDate() - daysFromMonday);
                }

                const fmt = d => d.toISOString().slice(0, 10);
                return { from: fmt(dateFrom), to: fmt(today) };
            }

            const defaultRange = getDefaultDateRange();
            $('#date-from').val(defaultRange.from);
            $('#date-to').val(defaultRange.to);

            // Initialize DataTable
            const reimbursementTable = new DataTable('#reimbursement-table', {
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('main_transaction.reimbursement.index_data_table') }}',
                    data: function(d) {
                        d.date_from = $('#date-from').val();
                        d.date_to   = $('#date-to').val();
                    }
                },
                columns: [
                    {
                        data: 'reimbursement_date',
                        render: function(data) {
                            return new Date(data).toLocaleDateString('id-ID');
                        }
                    },
                    {
                        data: 'transaction_code',
                        render: function(data, type, row) {
                            if (!data) return '-';
                            if (canRead) {
                                const detailUrl =
                                    `{{ route('main_transaction.reimbursement.detail', ':id') }}`
                                    .replace(':id', row.id);
                                return `<a href="${detailUrl}" class="text-primary fw-semibold">${data}</a>`;
                            }
                            return data;
                        }
                    },
                    {
                        data: 'reimbursement_name',
                        render: function(data) {
                            return data || '-';
                        }
                    },
                    {
                        data: 'amount',
                        render: function(data) {
                            return 'Rp ' + parseInt(data || 0).toLocaleString('id-ID');
                        }
                    },
                    {
                        data: 'status',
                        render: function(data) {
                            const badges = {
                                'PENDING': 'bg-label-warning',
                                'APPROVED': 'bg-label-success',
                                'REJECTED': 'bg-label-danger'
                            };
                            const cls = badges[data] || 'bg-label-secondary';
                            return `<span class="badge ${cls}">${data}</span>`;
                        }
                    },
                    {
                        data: 'budget_category_name',
                        render: function(data) {
                            return data || '-';
                        }
                    },
                    {
                        data: 'creator_name',
                        render: function(data) {
                            return data || '-';
                        }
                    },
                    {
                        data: 'reviewer_name',
                        render: function(data) {
                            return data || '-';
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
                ordering: false
            });

            // Reload table when date filters change
            $('#date-from, #date-to').on('change', function() {
                reimbursementTable.ajax.reload();
            });

        });
    </script>
@endsection
