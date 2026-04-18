@extends('layouts.default')
@section('page_title')
    Detail COA
@stop
@section('content')
    <!-- Header Section -->
    <div class="card mb-6">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Detail COA</h5>
            <div class="d-flex gap-2">
                @can('coa.edit')
                <a href="{{ route('master_data.coa.edit', ['id' => $coa->id]) }}"
                    class="btn btn-md btn-warning waves-effect waves-light">
                    <i class="icon-base ti tabler-edit me-2"></i>
                    Edit COA
                </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="card mb-6">
        <div class="card-body">
            <!-- COA Title -->
            <h2>{{ $coa->code }} - {{ $coa->name }}</h2>
            <span>
                <h6>Data akun berdasarkan tanggal {{ date('Y-m-d H:i:s') }}</h6>
            </span>
            <hr class="my-6 mx-n6" />

            <div class="col-lg-12">
                <div class="demo-inline-spacing mt-4">
                    <div class="list-group list-group-horizontal-md text-md-center" role="tablist">
                        <a class="list-group-item list-group-item-action waves-effect active" id="coa-info-list-item"
                            data-bs-toggle="list" href="#coa-info-tab" aria-selected="true" role="tab">
                            <i class="icon-base ti tabler-info-circle me-2"></i>
                            <b>Informasi COA</b>
                        </a>
                        <a class="list-group-item list-group-item-action waves-effect" id="transactions-list-item"
                            data-bs-toggle="list" href="#transactions-tab" aria-selected="false" role="tab">
                            <i class="icon-base ti tabler-receipt-2 me-2"></i>
                            <b>Riwayat Transaksi</b>
                        </a>
                        <a class="list-group-item list-group-item-action waves-effect" id="audit-log-list-item"
                            data-bs-toggle="list" href="#audit-log-tab" aria-selected="false" role="tab">
                            <i class="icon-base ti tabler-history me-2"></i>
                            <b>Audit Log</b>
                        </a>
                    </div>
                    <div class="tab-content px-0 mt-0">
                        <!-- COA Information Tab -->
                        <div class="tab-pane fade show active" id="coa-info-tab" role="tabpanel">
                            <!-- Info COA Section -->
                            <div class="section-header mt-12">
                                <h6>Informasi COA</h6>
                                <hr class="mx-n6">
                            </div>

                            <!-- Row 1: Basic Information -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Kode COA</label>
                                    <input type="text" class="form-control" id="coa-code" value="{{ $coa->code }}"
                                        disabled />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nama COA</label>
                                    <input type="text" class="form-control" value="{{ $coa->name }}" disabled />
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Parent COA</label>
                                    <input type="text" class="form-control" value="{{ $coa->parent_coa_code }}"
                                        disabled />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Jenis Akun</label>
                                    <input type="text" class="form-control" value="{{ $coa->type }}" disabled />
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Status</label>
                                    <div class="form-control-plaintext">
                                        <span
                                            class="badge {{ ($coa->status ?? 'ACTIVE') === 'ACTIVE' ? 'bg-label-success' : 'bg-label-danger' }}">
                                            {{ $coa->status ?? 'ACTIVE' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Posisi Normal</label>
                                    <input type="text" class="form-control" value="{{ $coa->normal_position }}"
                                        disabled />
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Deskripsi</label>
                                    <textarea class="form-control" rows="3" disabled>{{ $coa->note }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Terakhir Diupdate</label>
                                    <input type="text" class="form-control" value="{{ $coa->updated_at }}" disabled />
                                </div>
                            </div>
                        </div>

                        <!-- Transactions Tab -->
                        <div class="tab-pane fade" id="transactions-tab" role="tabpanel">
                            <h5>Riwayat Transaksi</h5>
                            <!-- Summary Cards -->
                            {{-- <div class="row mb-4">
                                <div class="col-lg-3 col-sm-6">
                                    <div class="card card-border-shadow-primary h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="avatar me-4">
                                                    <span class="avatar-initial rounded bg-label-primary">
                                                        <i class="icon-base ti tabler-receipt icon-28px"></i>
                                                    </span>
                                                </div>
                                                <h4 class="mb-0">156</h4>
                                            </div>
                                            <p class="mb-1">Total Transaksi</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-6">
                                    <div class="card card-border-shadow-success h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="avatar me-4">
                                                    <span class="avatar-initial rounded bg-label-success">
                                                        <i class="icon-base ti tabler-trending-up icon-28px"></i>
                                                    </span>
                                                </div>
                                                <h4 class="mb-0">Rp 125.7M</h4>
                                            </div>
                                            <p class="mb-1">Total Debit</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-6">
                                    <div class="card card-border-shadow-warning h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="avatar me-4">
                                                    <span class="avatar-initial rounded bg-label-warning">
                                                        <i class="icon-base ti tabler-trending-down icon-28px"></i>
                                                    </span>
                                                </div>
                                                <h4 class="mb-0">Rp 98.3M</h4>
                                            </div>
                                            <p class="mb-1">Total Kredit</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-6">
                                    <div class="card card-border-shadow-info h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="avatar me-4">
                                                    <span class="avatar-initial rounded bg-label-info">
                                                        <i class="icon-base ti tabler-calculator icon-28px"></i>
                                                    </span>
                                                </div>
                                                <h4 class="mb-0">Rp 27.4M</h4>
                                            </div>
                                            <p class="mb-1">Selisih</p>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}

                            <!-- Advanced Search Form -->
                            {{-- <form class="dt_adv_search" method="GET">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="row g-3">
                                            <div class="col-12 col-sm-6 col-lg-3">
                                                <label class="form-label">Tanggal Transaksi:</label>
                                                <input type="text" id="transaction-date-range-picker"
                                                    class="form-control global-single-date-picker" />
                                            </div>
                                            <div class="col-12 col-sm-6 col-lg-3">
                                                <label class="form-label">No Referensi:</label>
                                                <input type="text" class="form-control" id="reference-search"
                                                    placeholder="Cari No Referensi" />
                                            </div>
                                            <div class="col-12 col-sm-6 col-lg-3">
                                                <label class="form-label">Jenis Transaksi:</label>
                                                <select class="global-select2 form-select" id="transaction-type-filter"
                                                    data-allow-clear="false">
                                                    <option value="">Semua Jenis</option>
                                                    <option value="jurnal">Jurnal</option>
                                                    <option value="penjualan">Penjualan</option>
                                                    <option value="pembelian">Pembelian</option>
                                                    <option value="kas">Kas & Bank</option>
                                                </select>
                                            </div>
                                            <div class="col-12 col-sm-6 col-lg-3">
                                                <label class="form-label">Deskripsi:</label>
                                                <input type="text" class="form-control" id="description-search"
                                                    placeholder="Cari Deskripsi" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form> --}}

                            <hr class="my-6 mx-n6" />


                            <div class="card-body">
                                <!-- Advanced Search Form -->
                                <form class="dt_adv_search" method="GET" autocomplete="off">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="row g-3">
                                                <div class="col-12 col-sm-6 col-lg-3">
                                                    <label class="form-label">No Referensi:</label>
                                                    <input type="text" class="form-control dt-input dt-full-name"
                                                        placeholder="Cari Berdasarkan No Referensi" id="reference-no" />
                                                </div>
                                                <div class="col-12 col-sm-6 col-lg-3">
                                                    <label class="form-label">Deskripsi Jurnal:</label>
                                                    <input type="text" class="form-control dt-input dt-full-name"
                                                        placeholder="Cari Berdasarkan Deskripsi Jurnal"
                                                        id="description-journal" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <hr class="my-6 mx-n6" />
                            </div>
                            <!-- Data Table -->
                            <div class="card-datatable">
                                <table id="main-table" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>No Referensi</th>
                                            <th>Jenis</th>
                                            <th>Deskripsi</th>
                                            <th>Debit</th>
                                            <th>Kredit</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>

                        <!-- Data Table -->

                    </div>

                    <!-- Audit Log Tab -->
                    <div class="tab-pane fade" id="audit-log-tab" role="tabpanel">
                        <h5>Riwayat Perubahan</h5>
                        <div class="card-datatable mx-n6">
                            <table class="table table-bordered" id="audit-log-table">
                                <thead>
                                    <tr>
                                        <th>Tanggal & Waktu</th>
                                        <th>Pengguna</th>
                                        <th>Aksi</th>
                                        <th>Deskripsi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($audit_log_data as $row)
                                        <tr>
                                            <td>{{ $row->created_at }}</td>
                                            <td><a href="{{ route('master_data.user.detail', ['id' => $row->user_id]) }}"
                                                    class="text-primary fw-semibold">{{ $row->user_name }}</a></td>
                                            <td><span
                                                    class="badge bg-label-{{ $row->action_type == 'CREATE' ? 'success' : 'warning' }}">{{ $row->action_type }}</span>
                                            </td>
                                            <td>{{ $row->description }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js_script')
    <script>
        $(function() {
            $('#audit-log-table').DataTable({
                responsive: true,
                orderCellsTop: true,
                searching: false,
                paging: false,
                info: false,
                order: [
                    [0, 'desc']
                ] // Sort by date descending
            });

            const main_table = document.querySelector('#main-table');
            let dt_adv_filter;

            if (main_table) {
                dt_adv_filter = new DataTable(main_table, {
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: `{{ route('master_data.coa.journal_log_per_coa_data_table') }}`,
                        data: function(d) {
                            d.description_journal = $('#description-journal').val();
                            d.reference_no = $('#reference-no').val();
                            d.coa_code = $('#coa-code').val();
                        }
                    },
                    columns: [{
                            data: 'date',
                        },
                        {
                            data: 'reference_no',
                            responsivePriority: 2,
                            render: function(data, type, row) {
                                const detailUrl = `{{ route('master_data.coa.detail', ':id') }}`
                                    .replace(':id', data);
                                return `<a href="${detailUrl}" class="text-primary fw-semibold">${data}</a>`;
                            }
                        },
                        {
                            data: 'type',
                        },
                        {
                            data: 'description',
                        },
                        {
                            data: 'amount',
                            createdCell: function(td, cellData, rowData, row, col) {
                                $(td).addClass('text-end');
                            },
                            render: function(data, type, row) {
                                if (row.debit_credit == 'D') {
                                    let formatted = 'Rp. ' + parseFloat(data).toLocaleString(
                                        'id-ID');
                                    return formatted;
                                }
                                return '-'
                            },
                        },
                        {
                            data: 'amount',
                            createdCell: function(td, cellData, rowData, row, col) {
                                $(td).addClass('text-end');
                            },
                            render: function(data, type, row) {
                                if (row.debit_credit == 'C') {
                                    let formatted = 'Rp. ' + parseFloat(data).toLocaleString(
                                        'id-ID');
                                    return formatted;
                                }
                                return '-'
                            },
                        },
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
                    // responsive: true,
                    // orderCellsTop: true
                });
            }

            // Search functionality with debounce (500ms)
            $('#description-journal, #reference-no').on('keyup', debounce(function() {
                if (dt_adv_filter) {
                    dt_adv_filter.search(this.value).draw();
                }
            }, 1000));

        });
    </script>
@endsection
