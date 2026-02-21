@extends('layouts.default')
@section('page_title')
    List Pengeluaran
@stop
@section('content')
    <!-- Summary Cards -->
    {{-- <div class="row">
        <div class="col-lg-4 col-sm-6">
            <div class="card card-border-shadow-warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-warning"><i
                                    class="icon-base ti tabler-receipt-off icon-28px"></i></span>
                        </div>
                        <h4 class="mb-0">Rp 125,500,000</h4>
                    </div>
                    <p class="mb-1">Total Pengeluaran Bulan Ini</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6">
            <div class="card card-border-shadow-info h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-info"><i
                                    class="icon-base ti tabler-clock-pause icon-28px"></i></span>
                        </div>
                        <h4 class="mb-0">23</h4>
                    </div>
                    <p class="mb-1">Menunggu Persetujuan</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6">
            <div class="card card-border-shadow-success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-success"><i
                                    class="icon-base ti tabler-check icon-28px"></i></span>
                        </div>
                        <h4 class="mb-0">187</h4>
                    </div>
                    <p class="mb-1">Telah Dibayar</p>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Main Content Card -->
    <div class="mt-6">
        <div class="card">
            <h4 class="card-header">List Pengeluaran</h4>
            <div class="card-body">
                <!-- Advanced Search Form -->
                <form class="dt_adv_search" method="GET" autocomplete="off">
                    <div class="row">
                        <div class="col-12">
                            <div class="row g-3">
                                <div class="col-12 col-sm-6 col-lg-3">
                                    <label class="form-label">Tanggal Pengeluaran:</label>
                                    <input type="text" id="date-range-picker" class="form-control" />
                                </div>
                                <div class="col-12 col-sm-6 col-lg-3">
                                    <label class="form-label">No Transaksi:</label>
                                    <input type="text" class="form-control dt-input" id="transaction-no-filter"
                                        data-column="2" placeholder="Cari Berdasarkan No Transaksi" data-column-index="1" />
                                </div>
                                <div class="col-12 col-sm-6 col-lg-3">
                                    <label class="form-label">Supplier:</label>
                                    <select class="global-select2 form-select" id="supplier-filter"
                                        data-allow-clear="false">
                                        <option value="">Semua Supplier</option>
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <hr class="my-6 mx-n6" />

                <!-- Action Buttons -->
                <div class="mt-4">
                    <div class="d-flex justify-content-between">
                        <!-- Column Toggle -->
                        <div class="d-flex align-items-center">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="toggleTagColumn" checked>
                                <label class="form-check-label" for="toggleTagColumn">
                                    <i class="icon-base ti tabler-tag me-1"></i>Tampilkan Kolom Tag
                                </label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            @can('expense.create')
                                <a href="{{ route('main_transaction.expense.add') }}"
                                    class="btn btn-md btn-primary waves-effect waves-light">
                                    <span class="icon-xs icon-base ti tabler-plus me-2"></span> Tambah Data
                                </a>
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
                            <th>No Referensi</th>
                            <th>Keterangan</th>
                            <th>Supplier/Vendor</th>
                            <th>Amount</th>
                            <th>Diinput Oleh</th>
                            <th>Tag</th>
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
            const canRead = @json(auth()->user()->can('expense.view'));
            const canWrite = @json(auth()->user()->can('expense.create'));
            const canDelete = @json(auth()->user()->can('expense.delete'));
            const canShowEditButton = @json(auth()->user()->can('expense.edit'));
            const canShowDeleteButton = @json(auth()->user()->can('expense.delete'));

            // DataTable initialization
            const main_table = document.querySelector('#main-table');
            let dt_adv_filter;

            dt_adv_filter = new DataTable(main_table, {
                    ajax: {
                        url: '{{ route('main_transaction.expense.index_data_table') }}',
                        data: function(d) {
                            d.date_range_filter = $('#date-range-picker').val()
                            d.transaction_no = $('#transaction-no-filter').val()
                            d.supplier_filter = $('#supplier-filter').val()
                        }
                    },
                    columns: [{
                            data: 'transaction_date'
                        },
                        {
                            data: 'transaction_no',
                            render: function(data, type, row) {
                                return data;
                            }
                        },
                        {
                            data: 'description'
                        },
                        {
                            data: 'supplier_name',
                            render: function(data, type, row) {
                                if (!data) return '-';
                                if (canRead) {
                                    const detailUrl =
                                        `{{ route('master_data.contact.detail', ':id') }}`
                                        .replace(':id', row.supplier_id);
                                    return `<a href="${detailUrl}" class="text-primary fw-semibold">${data}</a>`;
                                }
                                return data;
                            }
                        },
                        {
                            data: 'total_amount',
                            className: 'text-end',
                            render: function(data, type, row) {
                                if (type === 'display' && data != null && !isNaN(data)) {
                                    return 'Rp ' + parseFloat(data).toLocaleString('id-ID');
                                }
                                return data ?? '';
                            }
                        },
                        {
                            data: 'created_by_name',
                            render: function(data, type, row) {
                                if (canRead) {
                                    const detailUrl =
                                        `{{ route('master_data.user.detail', ':id') }}`
                                        .replace(':id', row.created_by_id);
                                    return `<a href="${detailUrl}" class="text-primary fw-semibold">${data}</a>`;
                                }
                                return data;
                            }
                        },
                        {
                            data: 'tags',
                            render: function(data, type, row) {
                                let tags = JSON.parse(data);
                                return tags.map(tag => {
                                    if (tag.name == null) {
                                        return ``;
                                    }
                                    return `<span class="badge ${tag.preview} me-1">${tag.name}</span>`;
                                }).join('');
                            }
                        },
                        {
                            data: null,
                            orderable: false,
                            searchable: false,
                            responsivePriority: 1,
                            render: function(data, type, row, meta) {
                                const detailUrl =
                                    `{{ route('main_transaction.expense.detail', ':id') }}`.replace(
                                        ':id', row.id);
                                const editUrl =
                                    `{{ route('main_transaction.expense.edit', ':id') }}`.replace(
                                        ':id', row.id);
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
                    // scrollX: true,
                    responsive: true,
                    orderCellsTop: true,
                    ordering: false
                });

            // Tag column toggle functionality
            $('#toggleTagColumn').on('change', function() {
                const column = dt_adv_filter.column(6); // Tag column is index 7
                column.visible(this.checked);
            });

            let searchTimeout;
            $('#date-range-picker, #transaction-no-filter, #supplier-filter').on(
                'keyup change',
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
    </script>
@endsection
