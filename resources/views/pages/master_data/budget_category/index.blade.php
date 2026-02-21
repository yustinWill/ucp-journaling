@extends('layouts.default')
@section('page_title')
    Kategori Anggaran
@stop

@section('content')
    <!-- Main Content Card -->
    <div class="mt-6">
        <div class="card">
            <h4 class="card-header">Kelola Kategori Anggaran</h4>
            <div class="card-body">
                <!-- Advanced Search Form -->
                <form class="dt_adv_search" method="GET" autocomplete="off">
                    <div class="row">
                        <div class="col-12">
                            <div class="row g-3">
                                <div class="col-12 col-sm-6 col-lg-4">
                                    <label class="form-label">Nama Kategori:</label>
                                    <input type="text" class="form-control dt-input" data-column="1"
                                        placeholder="Cari berdasarkan nama kategori" data-column-index="1" />
                                </div>
                                <div class="col-12 col-sm-6 col-lg-4">
                                    <label class="form-label">Status:</label>
                                    <select class="global-select2 form-select status-filter" data-allow-clear="true">
                                        <option value="">Semua Status</option>
                                        <option value="active">Aktif</option>
                                        <option value="inactive">Tidak Aktif</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <hr class="my-6 mx-n6" />

                <!-- Action Buttons -->
                @can('budget_category.create')
                    <div class="mt-4">
                        <div class="d-flex justify-content-end">
                            <div class="d-flex gap-2">
                                <a href="{{ route('master_data.budget_category.add') }}"
                                    class="btn btn-md btn-primary waves-effect waves-light">
                                    <span class="icon-xs icon-base ti tabler-plus me-2"></span> Tambah Kategori
                                </a>
                            </div>
                        </div>
                    </div>
                @endcan
            </div>

            <!-- Data Table -->
            <div class="card-datatable">
                <table id="main-table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tanggal Dibuat</th>
                            <th>Nama Kategori</th>
                            <th>Status</th>
                            <th>Dibuat Oleh</th>
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
            const canEdit = @json(auth()->user()->can('budget_category.edit'));
            const canDelete = @json(auth()->user()->can('budget_category.delete'));

            //DataTable
            const main_table = document.querySelector('#main-table');
            let dt_adv_filter;

            if (main_table) {
                dt_adv_filter = new DataTable(main_table, {
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: `{{ route('master_data.budget_category.index_data_table') }}`,
                        data: function(d) {
                            d.status = $('.status-filter').val();
                            d.category_name = $('input[data-column="1"]').val();
                        }
                    },
                    columns: [{
                            data: 'created_at'
                        },
                        {
                            data: 'category_name'
                        },
                        {
                            data: 'status'
                        },
                        {
                            data: 'created_by'
                        },
                        {
                            data: 'id'
                        }
                    ],
                    columnDefs: [{
                            targets: -1, // Action column (last column)
                            orderable: false,
                            searchable: false,
                            width: '120px',
                            responsivePriority: 1,
                            render: function(data, type, row, meta) {
                                const editUrl = `{{ route('master_data.budget_category.edit', ':id') }}`
                                    .replace(':id', row.id);
                                let actions = '<div class="d-flex gap-2">';

                                if (canEdit) {
                                    actions += `<a href="${editUrl}" class="btn btn-sm btn-primary waves-effect waves-light" title="Edit">
                                        <i class="icon-base ti tabler-edit"></i>
                                    </a>`;
                                }

                                if (canDelete) {
                                    actions += `<button type="button" class="btn btn-sm btn-danger waves-effect waves-light delete-btn"
                                        data-id="${row.id}" data-name="${row.category_name}" title="Hapus">
                                        <i class="icon-base ti tabler-trash"></i>
                                    </button>`;
                                }

                                actions += '</div>';
                                return actions;
                            }
                        },
                        {
                            targets: [0], // Date column (first column)
                            responsivePriority: 2,
                            render: function(data, type, row) {
                                return data;
                            }
                        },
                        {
                            targets: [2], // Status column
                            render: function(data, type, row) {
                                if (data === 'active') {
                                    return '<span class="badge bg-label-success">Aktif</span>';
                                } else {
                                    return '<span class="badge bg-label-secondary">Tidak Aktif</span>';
                                }
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
                    orderCellsTop: true
                });
            }

            let searchTimeout;
            $('.dt-input, .global-select2').on('keyup change',
                function() {
                    clearTimeout(searchTimeout);
                    const input = this;
                    searchTimeout = setTimeout(function() {
                        if (dt_adv_filter) {
                            dt_adv_filter.search(input.value).draw();
                        }
                    }, 400);
                });

            // Delete functionality
            $(document).on('click', '.delete-btn', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');

                Swal.fire({
                    title: 'Hapus Kategori?',
                    text: `Apakah Anda yakin ingin menghapus kategori "${name}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-danger',
                        cancelButton: 'btn btn-secondary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            url: "{{ route('master_data.budget_category.delete') }}",
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                id: id
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.message || 'Kategori berhasil dihapus',
                                    customClass: {
                                        confirmButton: 'btn btn-success'
                                    },
                                    buttonsStyling: false
                                });
                                dt_adv_filter.draw();
                            },
                            error: function(xhr) {
                                const res = xhr.responseJSON;
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal menghapus',
                                    html: res?.message || 'Terjadi kesalahan saat menghapus data.',
                                    customClass: {
                                        confirmButton: 'btn btn-primary'
                                    },
                                    buttonsStyling: false
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
