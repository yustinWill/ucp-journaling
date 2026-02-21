@extends('layouts.default')
@section('page_title')
    Master Nomor Transaksi
@stop

@section('content')
    <div class="card mb-6">
        <div class="card-body">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <h4 class="mb-3">Master Nomor Transaksi</h4>
                    <p class="text-muted">Kelola format dan konfigurasi nomor transaksi untuk setiap modul</p>
                </div>
            </div>

            <!-- DataTable Section -->
            <div class="card-datatable">
                <table id="transaction-number-table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Modul</th>
                            <th>Format Template</th>
                            <th>Counter Saat Ini</th>
                            <th>Preview Nomor Selanjutnya</th>
                            <th>Reset Frequency</th>
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
            const canView = @json(auth()->user()->can('transaction_number.view'));
            const canEdit = @json(auth()->user()->can('transaction_number.edit'));

            // Initialize DataTable
            $('#transaction-number-table').DataTable({
                ajax: {
                    url: '{{ route('master_data.transaction_number.index_data_table') }}',
                    type: 'GET'
                },
                columns: [{
                        data: 'module_display_name',
                        title: 'Modul',
                        render: function(data, type, row) {
                            return `<span class="fw-semibold">${data}</span>`;
                        }
                    },
                    {
                        data: 'format_template',
                        title: 'Format Template',
                        render: function(data, type, row) {
                            return `<code class="text-primary">${data}</code>`;
                        }
                    },
                    {
                        data: 'current_counter',
                        title: 'Counter Saat Ini',
                        className: 'text-center',
                        render: function(data, type, row) {
                            return `<span class="badge bg-label-info">${data}</span>`;
                        }
                    },
                    {
                        data: 'next_preview',
                        title: 'Preview Nomor Selanjutnya',
                        render: function(data, type, row) {
                            return `<span class="fw-semibold text-success">${data}</span>`;
                        }
                    },
                    {
                        data: 'reset_frequency',
                        title: 'Reset Frequency',
                        className: 'text-center',
                        render: function(data, type, row) {
                            let badgeClass = 'bg-label-secondary';
                            switch (data.toLowerCase()) {
                                case 'daily':
                                    badgeClass = 'bg-label-warning';
                                    break;
                                case 'monthly':
                                    badgeClass = 'bg-label-info';
                                    break;
                                case 'yearly':
                                    badgeClass = 'bg-label-primary';
                                    break;
                                case 'never':
                                    badgeClass = 'bg-label-secondary';
                                    break;
                            }
                            return `<span class="badge ${badgeClass}">${data}</span>`;
                        }
                    },
                    {
                        data: 'status',
                        title: 'Status',
                        className: 'text-center',
                        render: function(data, type, row) {
                            const badgeClass = data === 'active' ? 'bg-label-success' :
                                'bg-label-danger';
                            const statusText = data === 'active' ? 'Aktif' : 'Nonaktif';
                            return `<span class="badge ${badgeClass}">${statusText}</span>`;
                        }
                    },
                    {
                        data: 'id',
                        title: 'Aksi',
                        orderable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            const detailUrl =
                                `{{ route('master_data.transaction_number.detail', ':id') }}`
                                .replace(':id', data);
                            const editUrl =
                                `{{ route('master_data.transaction_number.edit', ':id') }}`.replace(
                                    ':id', data);

                            let actions = `<div class="d-flex justify-content-center gap-1">`;

                            if (canView) {
                                actions += `<a href="${detailUrl}" class="btn btn-sm btn-outline-info" title="Lihat Detail">
                                    <i class="icon-base ti tabler-eye"></i>
                                </a>`;
                            }

                            if (canEdit) {
                                actions += `<a href="${editUrl}" class="btn btn-sm btn-outline-primary" title="Edit Konfigurasi">
                                    <i class="icon-base ti tabler-edit"></i>
                                </a>`;
                            }

                            actions += `</div>`;
                            return actions;
                        }
                    }
                ],
                // scrollX: true,
                responsive: true,
                orderCellsTop: true,
                ordering: true,
                order: [
                    [0, 'asc']
                ], // Sort by module name
                pageLength: 25,
                language: {
                    paginate: {
                        next: '<i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>',
                        previous: '<i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>',
                        first: '<i class="icon-base ti tabler-chevrons-left scaleX-n1-rtl icon-18px"></i>',
                        last: '<i class="icon-base ti tabler-chevrons-right scaleX-n1-rtl icon-18px"></i>'
                    }
                }
            });
        });
    </script>
@endsection
