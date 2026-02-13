@extends('layouts.default')
@section('page_title')
    Stock Transfer
@stop
@section('content')
    <!-- Main Content Card -->
    <div class="card mt-6">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Daftar Stock Transfer</h4>
            <a href="{{ route('master_data.warehouse.stock_transfer_add') }}" class="btn btn-md btn-primary waves-effect waves-light">
                <span class="icon-xs icon-base ti tabler-plus me-2"></span> Tambah Stock Transfer
            </a>
        </div>
        <div class="card-body">
            <!-- Search Form -->
            <form class="dt_adv_search" method="GET" autocomplete="off">
                <div class="row g-3 mb-4">
                    <div class="col-12 col-sm-6 col-lg-4">
                        <label class="form-label">Tanggal:</label>
                        <input type="text" class="form-control global-daterangepicker" id="date-filter" placeholder="Pilih tanggal">
                    </div>
                    <div class="col-12 col-sm-6 col-lg-4">
                        <label class="form-label">Gudang Sumber:</label>
                        <input type="text" class="form-control" id="warehouse-filter" placeholder="Cari gudang sumber...">
                    </div>
                    <div class="col-12 col-sm-6 col-lg-4">
                        <label class="form-label">Pencarian:</label>
                        <input type="text" class="form-control" id="search-filter" placeholder="Cari nomor transaksi...">
                    </div>
                </div>
            </form>
        </div>
        <!-- Data Table -->
        <div class="card-datatable">
            <table id="stock-transfer-table" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Nomor Transaksi</th>
                        <th>Gudang Sumber</th>
                        <th>Gudang Tujuan</th>
                        <th>PIC</th>
                        <th>Total Item</th>
                        <th>Total Qty</th>
                        <th>Status</th>
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
            let stockTransferTable;

            stockTransferTable = new DataTable('#stock-transfer-table', {
                processing: true,
                serverSide: true,
                ajax: {
                    url: `{{ route('master_data.warehouse.stock_transfer_index_data_table') }}`,
                    data: function(d) {
                        d.date_filter = $('#date-filter').val();
                        d.warehouse_filter = $('#warehouse-filter').val();
                        d.search_filter = $('#search-filter').val();
                        return d;
                    }
                },
                columns: [{
                        data: 'adjustment_date',
                        responsivePriority: 2,
                        render: function(data, type, row) {
                            return moment(data).format('DD MMM YYYY');
                        }
                    },
                    {
                        data: 'adjustment_number',
                        responsivePriority: 1,
                        render: function(data, type, row) {
                            const detailUrl = `{{ route('master_data.warehouse.stock_transfer_detail', ':id') }}`.replace(':id', row.id);
                            return `<a href="${detailUrl}" class="text-primary fw-semibold">${data}</a>`;
                        }
                    },
                    {
                        data: 'warehouse_code',
                        responsivePriority: 3,
                        render: function(data, type, row) {
                            return `${data} - ${row.warehouse_name}`;
                        }
                    },
                    {
                        data: 'destination_warehouse_code',
                        responsivePriority: 4,
                        render: function(data, type, row) {
                            return `${data} - ${row.destination_warehouse_name}`;
                        }
                    },
                    {
                        data: 'pic_name',
                        responsivePriority: 5,
                        render: function(data, type, row) {
                            return data || '-';
                        }
                    },
                    {
                        data: 'total_items',
                        responsivePriority: 6,
                        className: 'text-end'
                    },
                    {
                        data: 'total_adjustment_qty',
                        responsivePriority: 7,
                        className: 'text-end',
                        render: function(data, type, row) {
                            // Format number, strip trailing zeros
                            const num = parseFloat(data);
                            if (num === Math.floor(num)) {
                                return num.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
                            }
                            const formatted = num.toFixed(4);
                            return parseFloat(formatted).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 4 }).replace(/\.?0+$/, '');
                        }
                    },
                    {
                        data: 'status',
                        responsivePriority: 8,
                        render: function(data, type, row) {
                            const badgeClass = data === 'APPROVED' ? 'bg-label-success' :
                                (data === 'WAITING_APPROVAL' ? 'bg-label-warning' : 'bg-label-secondary');
                            return `<span class="badge ${badgeClass}">${data}</span>`;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        responsivePriority: 1,
                        width: '80px',
                        render: function(data, type, row, meta) {
                            const detailUrl = `{{ route('master_data.warehouse.stock_transfer_detail', ':id') }}`.replace(':id', row.id);
                            return `
                                <div class="d-flex gap-2">
                                    <a href="${detailUrl}" class="btn btn-sm btn-primary waves-effect waves-light" title="Detail">
                                        <i class="icon-base ti tabler-eye"></i>
                                    </a>
                                </div>
                            `;
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

            // Search functionality
            let searchTimeout;
            $('#date-filter, #warehouse-filter, #search-filter').on('keyup change', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    if (stockTransferTable) {
                        stockTransferTable.draw();
                    }
                }, 400); // 400ms delay
            });
        });
    </script>
@endsection
