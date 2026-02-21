@extends('layouts.default')
@section('page_title')
    Reimbursement Approval
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
                            <small class="text-muted d-block">Pending Approval</small>
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
        <h4 class="card-header">Reimbursement Approval</h4>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="icon-base ti tabler-info-circle me-2"></i>
                <strong>Info:</strong> Review dan approve/reject reimbursement yang diajukan oleh staff. Pastikan untuk
                assign budget category saat approve.
            </div>
        </div>

        <!-- Data Table -->
        <div class="card-datatable">
            <table id="reimbursement-approval-table" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Tanggal</th>
                        <th>Staff</th>
                        <th>Nama Reimbursement</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Budget Category</th>
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
            const canRead = @json(auth()->user()->can('reimbursement_approval.view'));

            // Initialize DataTable
            const reimbursementTable = new DataTable('#reimbursement-approval-table', {
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('main_transaction.reimbursement_approval.index_data_table') }}',
                },
                columns: [{
                        data: 'transaction_code',
                    },
                    {
                        data: 'created_at',
                        render: function(data) {
                            return new Date(data).toLocaleDateString('id-ID');
                        }
                    },
                    {
                        data: 'creator_name',
                    },
                    {
                        data: 'reimbursement_name',
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
                        data: null,
                        width: '80px',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            const detailUrl =
                                `{{ route('main_transaction.reimbursement_approval.detail', ':id') }}`
                                .replace(':id', row.id);

                            let actions = '<div class="d-flex gap-2">';
                            if (canRead) {
                                const btnClass = row.status === 'PENDING' ? 'btn-warning' : 'btn-primary';
                                actions += `<a href="${detailUrl}" class="btn btn-sm ${btnClass}">
                                    <i class="icon-xs icon-base ti ${row.status === 'PENDING' ? 'tabler-eye-check' : 'tabler-eye'}"></i>
                                </a>`;
                            }
                            actions += '</div>';
                            return actions;
                        }
                    }
                ],
                layout: {
                    topStart: {
                        features: []
                    },
                    bottomStart: {
                        features: ['info']
                    },
                    bottomEnd: 'paging'
                },
                ordering: false
            });
        });
    </script>
@endsection
