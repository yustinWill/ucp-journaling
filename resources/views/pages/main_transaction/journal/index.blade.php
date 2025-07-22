@extends('layouts.default')
@section('page_title')
    List Jurnal
@stop
@section('content')
    <!-- Summary Cards -->
    <div class="row">
        <div class="col-lg-4 col-sm-6">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-primary"><i
                                    class="icon-base ti tabler-receipt icon-28px"></i></span>
                        </div>
                        <h4 class="mb-0">152</h4>
                    </div>
                    <p class="mb-1">Total Jurnal Bulan Ini</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6">
            <div class="card card-border-shadow-success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-success"><i
                                    class="icon-base ti tabler-cash icon-28px"></i></span>
                        </div>
                        <h4 class="mb-0">Rp 85,500,000</h4>
                    </div>
                    <p class="mb-1">Total Debit Bulan Ini</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6">
            <div class="card card-border-shadow-info h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-info"><i
                                    class="icon-base ti tabler-coins icon-28px"></i></span>
                        </div>
                        <h4 class="mb-0">Rp 85,500,000</h4>
                    </div>
                    <p class="mb-1">Total Kredit Bulan Ini</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Content Card -->
    <div class="mt-6">
        <div class="card">
        <h4 class="card-header">List Jurnal</h4>
        <div class="card-body">
            <!-- Advanced Search Form -->
            <form class="dt_adv_search" method="GET">
                <div class="row">
                    <div class="col-12">
                        <div class="row g-3">
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label">Tanggal Jurnal:</label>
                                <input type="text" id="date-range-picker" class="form-control" />
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label">No Referensi:</label>
                                <input type="text" class="form-control dt-input" data-column="2"
                                    placeholder="Cari Berdasarkan Nama Perusahaan" data-column-index="1" />
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label">Dibuat Oleh:</label>
                                <select class="global-select2 form-select" data-allow-clear="false">
                                    <option value="">Semua User</option>
                                    @foreach($createdByUsers as $user)
                                        <option value="{{ $user['value'] }}">{{ $user['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label">Notes:</label>
                                <input type="text" class="form-control dt-input" data-column="3"
                                    placeholder="Cari Berdasarkan Email" data-column-index="2" />
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
                        <a href="{{ route('main_transaction.journal.import') }}"
                            class="btn btn-md btn-primary waves-effect waves-light"><span
                                class="icon-xs icon-base ti tabler-file-upload me-2"></span> Import</a>
                        <a href="{{ route('main_transaction.journal.export') }}"
                            class="btn btn-md btn-primary waves-effect waves-light"><span
                                class="icon-xs icon-base ti tabler-file-download me-2"></span> Export</a>
                        <a href="{{ route('main_transaction.journal.add') }}"
                            class="btn btn-md btn-primary waves-effect waves-light"><span
                                class="icon-xs icon-base ti tabler-plus me-2"></span> Tambah Data</a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Data Table -->
        <div class="card-datatable">
            <table id="main-table" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tanggal Jurnal</th>
                        <th>No Referensi</th>
                        <th>Notes</th>
                        <th>Tags</th>
                        <th>Amount</th>
                        <th>Diinput Oleh</th>
                        <th>Diinput Pada</th>
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
            //DataTable
            const main_table = document.querySelector('#main-table');
            let dt_adv_filter;

            if (main_table) {
                dt_adv_filter = new DataTable(main_table, {
                    ajax: `{{ route('main_transaction.journal.index_data_table') }}`,
                    columns: [{
                            data: 'date'
                        },
                        {
                            data: 'reference_no'
                        },
                        {
                            data: 'notes'
                        },
                        {
                            data: 'tags'
                        },
                        {
                            data: 'amount'
                        },
                        {
                            data: 'created_by'
                        },
                        {
                            data: 'created_at',
                        },
                        {
                            data: null
                        }
                    ],
                    columnDefs: [{
                            targets: -1,
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row, meta) {
                                const editUrl = `{{ route('main_transaction.journal.edit', ':id') }}`.replace(':id', row.id);
                                const detailUrl = `{{ route('main_transaction.journal.detail', ':id') }}`.replace(':id', row.id);
                                
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
                        },
                        {
                            targets: [3], // Tags column
                            orderable: false,
                            render: function(data, type, row) {
                                if (data && Array.isArray(data) && data.length > 0) {
                                    return data.map(tag => {
                                        // Map tag code to color (would come from backend in real app)
                                        const tagColors = {
                                            'PENJUALAN': 'success',
                                            'PEMBELIAN': 'info', 
                                            'OPERASIONAL': 'warning',
                                            'INVESTASI': 'primary',
                                            'KEUANGAN': 'secondary',
                                            'PAJAK': 'danger',
                                            'KOREKSI': 'dark'
                                        };
                                        const color = tagColors[tag] || 'primary';
                                        return `<span class="badge bg-label-${color} me-1">${tag}</span>`;
                                    }).join('');
                                }
                                return '<span class="text-muted">-</span>';
                            }
                        },
                        {
                            targets: [-4],
                            className: 'text-end',
                            render: function(data, type, row) {
                                if (type === 'display' && data != null && !isNaN(data)) {
                                    return parseFloat(data).toLocaleString('id-ID');
                                }
                                return data ?? '';
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
                    scrollX: true,
                    responsive: true,
                    orderCellsTop: true
                });
            }

            // Navigation buttons now use direct <a> elements with href - no JavaScript needed
        });
    </script>
@endsection
