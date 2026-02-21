@extends('layouts.default')
@section('page_title')
    Konversi Unit
@stop
@section('content')
    <!-- Main Content Card -->
    <div class="mt-6">
        <div class="card">
            <h4 class="card-header">Kelola Konversi Unit</h4>
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
                                    <input type="text" class="form-control" id="search" placeholder="Cari Konversi Unit">
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
                            @can('unit_conversion.create')
                                <a href="{{ route('master_data.unit_conversion.add') }}"
                                    class="btn btn-md btn-primary waves-effect waves-light"><span
                                        class="icon-xs icon-base ti tabler-plus me-2"></span> Tambah Data</a>
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
                            <th>Unit Asal</th>
                            <th>Unit Tujuan</th>
                            <th>Faktor Konversi</th>
                            <th>Formula</th>
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
            const canRead = @json(auth()->user()->can('unit_conversion.view'));
            const canWrite = @json(auth()->user()->can('unit_conversion.create'));
            const canDelete = @json(auth()->user()->can('unit_conversion.delete'));
            const canShowEditButton = @json(auth()->user()->can('unit_conversion.edit'));
            const canShowDeleteButton = @json(auth()->user()->can('unit_conversion.delete'));

            // Initialize DataTable
            const mainTable = document.querySelector('#main-table');
            let dt_adv_filter;

            if (mainTable) {
                dt_adv_filter = new DataTable(mainTable, {
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: `{{ route('master_data.unit_conversion.index_data_table') }}`,
                        data: function(d) {
                            d.status = $('#status-filter').val();
                            d.search = $('#search').val();
                        }
                    },
                    columns: [{
                            data: 'created_at',
                            responsivePriority: 2
                        },
                        {
                            data: 'from_unit_name',
                            responsivePriority: 3,
                            render: function(data, type, row) {
                                return `<span class="badge bg-label-primary">${row.from_unit_code}</span> ${data}`;
                            }
                        },
                        {
                            data: 'to_unit_name',
                            responsivePriority: 4,
                            render: function(data, type, row) {
                                return `<span class="badge bg-label-info">${row.to_unit_code}</span> ${data}`;
                            }
                        },
                        {
                            data: 'conversion_factor',
                            responsivePriority: 5,
                            render: function(data, type, row) {
                                return `<strong>${parseFloat(data)}</strong>`;
                            }
                        },
                        {
                            data: 'conversion_formula',
                            responsivePriority: 6,
                            render: function(data, type, row) {
                                return data ? data : '<span class="text-muted">-</span>';
                            }
                        },
                        {
                            data: 'status',
                            responsivePriority: 7,
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
                            responsivePriority: 1,
                            width: '120px',
                            render: function(data, type, row, meta) {
                                const detailUrl =
                                    `{{ route('master_data.unit_conversion.detail', ':id') }}`
                                    .replace(':id', row.id);
                                const editUrl = `{{ route('master_data.unit_conversion.edit', ':id') }}`
                                    .replace(':id', row.id);
                                let actions = '<div class="d-flex gap-2">';

                                if (canRead) {
                                    actions +=
                                        `<a href="${detailUrl}" class="btn btn-sm btn-primary waves-effect waves-light" title="Detail">
                                        <i class="icon-xs icon-base ti tabler-eye"></i>
                                    </a>`;
                                }

                                if (canShowEditButton) {
                                    actions +=
                                        `<a href="${editUrl}" class="btn btn-sm btn-primary waves-effect waves-light" title="Edit">
                                        <i class="icon-xs icon-base ti tabler-edit"></i>
                                    </a>`;
                                }

                                if (canShowDeleteButton) {
                                    actions +=
                                        `<button class="btn btn-sm btn-danger waves-effect waves-light" title="Hapus" onclick="deleteItem(${row.id})">
                                        <i class="icon-xs icon-base ti tabler-trash"></i>
                                    </button>`;
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

        // Delete item function
        function deleteItem(id) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Apakah Anda yakin ingin menghapus konversi unit ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-danger me-3 waves-effect waves-light',
                    cancelButton: 'btn btn-label-secondary waves-effect'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ route('master_data.unit_conversion.delete', ':id') }}`.replace(':id', id),
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                customClass: {
                                    confirmButton: 'btn btn-success waves-effect waves-light'
                                }
                            }).then(() => {
                                $('#main-table').DataTable().ajax.reload();
                            });
                        },
                        error: function(xhr) {
                            const errorMessage = xhr.responseJSON?.message ||
                                'Terjadi kesalahan saat menghapus data.';
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: errorMessage,
                                customClass: {
                                    confirmButton: 'btn btn-danger waves-effect waves-light'
                                }
                            });
                        }
                    });
                }
            });
        }
    </script>
@endsection
