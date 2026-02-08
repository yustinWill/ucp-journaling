@extends('layouts.default')
@section('page_title')
    List Packing Produk
@stop
@section('content')
    <!-- Main Content Card -->
    <div class="mt-6">
        <div class="card">
            <h4 class="card-header">Kelola Packing Produk</h4>
            <div class="card-body">
                <!-- Advanced Search Form -->
                <div class="row mb-4">

                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label">Tanggal:</label>
                        <input type="text" id="date-range-picker" class="form-control" />
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Warehouse:</label>
                        <select class="global-select2 form-select" id="warehouse-filter" data-allow-clear="true">
                            <option value="">Semua Warehouse</option>
                            @foreach ($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->code }} - {{ $warehouse->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Product:</label>
                        <select class="global-select2 form-select" id="product-filter" data-allow-clear="true">
                            <option value="">Semua Product</option>
                            @foreach ($bundleProducts as $product)
                                <option value="{{ $product->id }}">{{ $product->code }} - {{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">No Packing:</label>
                        <input type="text" class="form-control dt-input dt-full-name" data-column="1"
                            placeholder="Cari Berdasarkan Kode Packing" id="packing-number" data-column-index="0" />
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-end">
                    <div class="d-flex gap-2">
                            <a href="{{ route('main_transaction.packing.add') }}"
                                class="btn btn-md btn-primary waves-effect waves-light"><span
                                    class="icon-xs icon-base ti tabler-plus me-2"></span> Tambah Packing</a>
                        
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="card-datatable">
                <table id="packing-table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 100px;">Tanggal</th>
                            <th style="width: 140px;">No Transaksi</th>
                            <th>Nama Produk</th>
                            <th style="width: 180px;">Kode Produk</th>
                            <th style="width: 100px;">Jumlah Packing</th>
                            <th style="width: 120px;">Unit</th>
                             <th style="width: 100px;">Aksi</th>
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
            let packingTable;

            function initPackingTable() {
                if (packingTable) {
                    packingTable.destroy();
                }

                packingTable = new DataTable('#packing-table', {
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('main_transaction.packing.index_data_table') }}',
                        data: function(d) {
                            d.date = $('#date-range-picker').val();
                            d.product = $('#product-filter').val();
                            d.packing_no = $('#packing-number').val();
                            d.warehouse = $('#warehouse-filter').val();
                            return d;
                        }
                    },
                    columns: [{
                            data: 'packing_date',
                            render: function(data, type, row) {
                                return moment(data).format('DD/MM/YYYY');
                            }
                        },
                        {
                            data: 'packing_number',
                            render: function(data, type, row) {
                                // Split multiple packing numbers by comma
                                const packingNumbers = data ? data.split(', ') : [];

                                if (packingNumbers.length === 1) {
                                    // Single packing number
                                    const detailUrl =
                                        "{{ route('main_transaction.packing.detail', ':id') }}"
                                        .replace(':id', row.id);
                                    return `<a href="${detailUrl}" class="text-primary fw-semibold">${data}</a>`;
                                } else if (packingNumbers.length > 1) {
                                    // Multiple packing numbers - show as badges
                                    const detailUrl =
                                        "{{ route('main_transaction.packing.detail', ':id') }}"
                                        .replace(':id', row.id);
                                    let html = `<a href="${detailUrl}" class="text-decoration-none">`;
                                    packingNumbers.forEach((num, index) => {
                                        if (index > 0) html += '<br>';
                                        html += `<span class="badge bg-label-primary me-1">${num.trim()}</span>`;
                                    });
                                    html += '</a>';
                                    return html;
                                } else {
                                    return '-';
                                }
                            }
                        },
                        {
                            data: 'bundle_product_name'
                        },
                        {
                            data: 'bundle_product_code',
                        },
                        {
                            data: 'quantity_packed',
                            className: 'text-end',
                            render: function(data, type, row) {
                                return new Intl.NumberFormat('id-ID', {
                                    minimumFractionDigits: 0,
                                    maximumFractionDigits: 2
                                }).format(data || 0);
                            }
                        },
                        {
                            data: 'unit_code',
                            className: 'text-center',
                        },
                        {
                            data: null,
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row, meta) {
                                let actions = '';

                                
                                    const detailUrl =
                                        "{{ route('main_transaction.packing.detail', ':id') }}"
                                        .replace(':id', row.id);
                                    actions += `<a href="${detailUrl}" class="btn btn-sm btn-primary waves-effect waves-light" title="Detail">
                                        <i class="icon-base ti tabler-eye"></i>
                                    </a>`;

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

            // Initialize table
            initPackingTable();

            let searchTimeout;
            $('#date-range-picker,#warehouse-filter,#product-filter,#packing-number').on('keyup change',
                function() {
                    clearTimeout(searchTimeout);
                    const input = this;
                    searchTimeout = setTimeout(function() {
                        if (packingTable) {
                            packingTable.search(input.value).draw();
                        }
                    }, 400); // 400ms delay
                });
        });

    </script>
@endsection
