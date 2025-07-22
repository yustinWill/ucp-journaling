@extends('layouts.default')
@section('page_title')
    List Pengeluaran
@stop
@section('content')
    <!-- Summary Cards -->
    <div class="row">
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
    </div>
    
    <!-- Main Content Card -->
    <div class="mt-6">
        <div class="card">
        <h4 class="card-header">List Pengeluaran</h4>
        <div class="card-body">
            <!-- Advanced Search Form -->
            <form class="dt_adv_search" method="GET">
                <div class="row">
                    <div class="col-12">
                        <div class="row g-3">
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label">Tanggal Pengeluaran:</label>
                                <input type="text" id="date-range-picker" class="form-control" />
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label">No Referensi:</label>
                                <input type="text" class="form-control dt-input" data-column="2"
                                    placeholder="Cari Berdasarkan No Referensi" data-column-index="1" />
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label">Kategori:</label>
                                <select class="global-select2 form-select" id="category-filter" data-allow-clear="false">
                                    <option value="">Semua Kategori</option>
                                    @foreach($expenseCategories as $category)
                                        <option value="{{ $category['value'] }}">{{ $category['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label">Status:</label>
                                <select class="global-select2 form-select" id="status-filter" data-allow-clear="false">
                                    <option value="">Semua Status</option>
                                    @foreach($expenseStatuses as $status)
                                        <option value="{{ $status['value'] }}">{{ $status['label'] }}</option>
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
                <div class="d-flex justify-content-end">
                    <div class="d-flex gap-2">
                        <a href="{{ route('main_transaction.expense.import') }}"
                            class="btn btn-md btn-primary waves-effect waves-light">
                            <span class="icon-xs icon-base ti tabler-file-upload me-2"></span> Import
                        </a>
                        <a href="{{ route('main_transaction.expense.export') }}"
                            class="btn btn-md btn-primary waves-effect waves-light">
                            <span class="icon-xs icon-base ti tabler-file-download me-2"></span> Export
                        </a>
                        <a href="{{ route('main_transaction.expense.add') }}"
                            class="btn btn-md btn-primary waves-effect waves-light">
                            <span class="icon-xs icon-base ti tabler-plus me-2"></span> Tambah Data
                        </a>
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
                        <th>Kategori</th>
                        <th>Supplier/Vendor</th>
                        <th>Status</th>
                        <th>Amount</th>
                        <th>Diinput Oleh</th>
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
            // DataTable initialization
            const main_table = document.querySelector('#main-table');
            let dt_adv_filter;

            if (main_table) {
                dt_adv_filter = new DataTable(main_table, {
                    ajax: `{{ route('main_transaction.expense.index_data_table') }}`,
                    columns: [
                        { data: 'date' },
                        { 
                            data: 'reference_no',
                            render: function(data, type, row) {
                                return `<a href="#" class="text-primary">${data}</a>`;
                            }
                        },
                        { data: 'description' },
                        { data: 'category' },
                        { 
                            data: 'supplier',
                            render: function(data, type, row) {
                                return data ? `<a href="#" class="text-primary">${data}</a>` : '-';
                            }
                        },
                        { 
                            data: 'status',
                            render: function(data, type, row) {
                                let badgeClass = '';
                                switch (data) {
                                    case 'Draft':
                                        badgeClass = 'bg-label-secondary';
                                        break;
                                    case 'Diajukan':
                                        badgeClass = 'bg-label-info';
                                        break;
                                    case 'Disetujui':
                                        badgeClass = 'bg-label-success';
                                        break;
                                    case 'Dibayar':
                                        badgeClass = 'bg-label-primary';
                                        break;
                                    case 'Ditolak':
                                        badgeClass = 'bg-label-danger';
                                        break;
                                    default:
                                        badgeClass = 'bg-label-secondary';
                                }
                                return `<span class="badge ${badgeClass}">${data}</span>`;
                            }
                        },
                        { 
                            data: 'amount',
                            className: 'text-end',
                            render: function(data, type, row) {
                                if (type === 'display' && data != null && !isNaN(data)) {
                                    return 'Rp ' + parseFloat(data).toLocaleString('id-ID');
                                }
                                return data ?? '';
                            }
                        },
                        { data: 'created_by' },
                        {
                            data: null,
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row, meta) {
                                const detailUrl = `{{ route('main_transaction.expense.detail', ':id') }}`.replace(':id', row.id);
                                const editUrl = `{{ route('main_transaction.expense.edit', ':id') }}`.replace(':id', row.id);
                                return `
                                <div class="d-flex gap-2">
                                    <a href="${detailUrl}" class="btn btn-sm btn-primary waves-effect waves-light" title="Detail">
                                        <i class="icon-base ti tabler-eye"></i>
                                    </a>
                                    <a href="${editUrl}" class="btn btn-sm btn-primary waves-effect waves-light" title="Edit">
                                        <i class="icon-base ti tabler-edit"></i>
                                    </a>
                                </div>
                                `;
                            }
                        }
                    ],
                    layout: {
                        topStart: { rowClass: 'm-0', features: [] },
                        topEnd: {},
                        bottomStart: { rowClass: 'row mx-3 justify-content-between', features: ['info'] },
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
                    scrollX: true,
                    responsive: true,
                    orderCellsTop: true
                });
            }

            // Navigation buttons now use direct <a> elements with href - no JavaScript needed
        });
    </script>
@endsection