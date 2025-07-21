@extends('layouts.default')
@section('page_title')
    Detail Gudang
@stop
@section('content')
    <div class="card mb-6">
        <div class="card-body">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <h4 class="mb-3">Detail Gudang</h4>
                </div>
                
                <!-- Action Panel -->
                <div class="col-md-4">
                    <div class="text-end">
                        @php
                            $totalActions = 1; // Edit always present
                        @endphp
                        
                        @if($totalActions < 3)
                            <!-- Show buttons directly when less than 3 actions -->
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="{{ route('master_data.storage.edit', 1) }}" class="btn btn-primary btn-md waves-effect waves-light">
                                    <i class="icon-base ti tabler-edit me-2"></i>Edit
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Storage Title -->
            <h2>Gudang Utama</h2>
            <span>
                <h6>Data di bawah berdasarkan tanggal 2025-07-07 14:35:21</h6>
            </span>
            <hr class="my-6 mx-n6" />

            <div class="row g-6">
                <div class="col-md-6">
                    <label class="form-label">Kode Gudang</label>
                    <input type="text" class="form-control" value="STG01" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama Gudang</label>
                    <input type="text" class="form-control" value="Gudang Utama" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Penanggung Jawab</label>
                    <select class="global-select2 form-select" multiple disabled>
                        <option value="AK" selected>William</option>
                        <option value="HI">Winona</option>
                        <option value="HI" selected>Akbar</option>
                        <option value="CA">Tono</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <div class="form-control-plaintext">
                        <span class="badge bg-label-success">ACTIVE</span>
                    </div>
                </div>
            </div>
            <hr class="my-6 mx-n6" />
            <div class="col-lg-12">
                <div class="demo-inline-spacing mt-4">
                    <div class="list-group list-group-horizontal-md text-md-center" role="tablist">
                        <a class="list-group-item list-group-item-action waves-effect active" id="home-list-item"
                            data-bs-toggle="list" href="#horizontal-daftar-produk" aria-selected="true" role="tab">
                            <i class="icon-base ti tabler-package me-2"></i>
                            <b>Daftar Produk</b>
                        </a>
                        <a class="list-group-item list-group-item-action waves-effect" id="profile-list-item"
                            data-bs-toggle="list" href="#horizontal-daftar-transaksi" aria-selected="false" role="tab">
                            <i class="icon-base ti tabler-receipt-2 me-2"></i>
                            <b>Daftar Transaksi</b>
                        </a>
                        <a class="list-group-item list-group-item-action waves-effect" id="messages-list-item"
                            data-bs-toggle="list" href="#horizontal-daftar-batch" aria-selected="false" role="tab">
                            <i class="icon-base ti tabler-stack me-2"></i>
                            <b>Daftar Batch</b>
                        </a>
                        <a class="list-group-item list-group-item-action waves-effect" id="audit-list-item"
                            data-bs-toggle="list" href="#horizontal-audit-log" aria-selected="false" role="tab">
                            <i class="icon-base ti tabler-history me-2"></i>
                            <b>Audit Log</b>
                        </a>
                    </div>
                    <div class="tab-content px-0 mt-0">
                        <div class="tab-pane fade show active" id="horizontal-daftar-produk" role="tabpanel"
                            aria-labelledby="home-list-item">
                            <div class="card-datatable">
                                <table id="product-table" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Kode Produk</th>
                                            <th>Nama Produk</th>
                                            <th>Kategori Produk </th>
                                            <th>Total Stok</th>
                                            <th>Qty Tersedia</th>
                                            <th>Batas Minimum</th>
                                            <th>Unit</th>
                                            <th>Harga Rata-Rata</th>
                                            <th>Harga Beli Terakhir</th>
                                            <th>Harga Beli</th>
                                            <th>Harga Jual</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="horizontal-daftar-transaksi" role="tabpanel"
                            aria-labelledby="profile-list-item">
                            <div class="card-datatable">
                                <table id="transaction-table" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>No. Transaksi</th>
                                            <th>Kontak</th>
                                            <th>Tgl Jatuh Tempo</th>
                                            <th>Status</th>
                                            <th>Jumlah DP</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="horizontal-daftar-batch" role="tabpanel"
                            aria-labelledby="messages-list-item">
                            <div class="card-datatable">
                                <table id="batch-table" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Kode Batch</th>
                                            <th>Nama Produk</th>
                                            <th>Tanggal Masuk</th>
                                            <th>Tanggal Kadaluarsa</th>
                                            <th>Stok Tersisa</th>
                                            <th>Harga/Unit</th>
                                            <th>Status</th>
                                            <th>Sumber Transaksi</th>
                                            <th>Nomor Transaksi</th>
                                            <th>Catatan</th>
                                            <th>Tgl Update Terakhir</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Audit Log Tab -->
                        <div class="tab-pane fade" id="horizontal-audit-log" role="tabpanel">
                            <div class="section-header mt-6">
                                <h6>Log Audit Gudang</h6>
                            </div>
                            <div class="mx-n6">
                                <table id="audit-log-table" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Tanggal & Waktu</th>
                                            <th>Pengguna</th>
                                            <th>Aksi</th>
                                            <th>Deskripsi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>2025-01-21 14:30:00</td>
                                            <td><a href="#" class="text-primary fw-semibold">Admin Gudang</a></td>
                                            <td><span class="badge bg-label-info">UPDATE</span></td>
                                            <td>Update status gudang dan penanggung jawab untuk RIYO Water</td>
                                        </tr>
                                        <tr>
                                            <td>2025-01-20 10:15:30</td>
                                            <td><a href="#" class="text-primary fw-semibold">System Administrator</a></td>
                                            <td><span class="badge bg-label-success">CREATE</span></td>
                                            <td>Gudang utama dibuat untuk operasional RIYO Water</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>
@endsection
@section('js_script')
    <script>
        $(function() {
            //DataTable
            const product_table = document.querySelector('#product-table');

            if (product_table) {
                let dt_adv_filter = new DataTable(product_table, {
                    ajax: `{{ route('master_data.storage.detail_product_data_table') }}`,
                    columns: [{
                            data: 'product_code'
                        },
                        {
                            data: 'product_name'
                        },
                        {
                            data: 'category'
                        },
                        {
                            data: 'total_stock'
                        },
                        {
                            data: 'available_qty'
                        },
                        {
                            data: 'minimum'
                        },
                        {
                            data: 'unit'
                        },
                        {
                            data: 'average_price'
                        },
                        {
                            data: 'last_purchase_price'
                        },
                        {
                            data: 'purchase_price'
                        },
                        {
                            data: 'selling_price'
                        }
                    ],
                    columnDefs: [{
                        targets: [-1, -2, -3, -4, -6, -7, -8],
                        className: 'text-end',
                        render: function(data, type, row) {
                            if (type === 'display' && data != null && !isNaN(data)) {
                                return parseFloat(data).toLocaleString('id-ID');
                            }
                            return data ?? '';
                        }
                    }],
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
                    orderCellsTop: true
                });
            }

            const transaction_table = document.querySelector('#transaction-table');

            if (transaction_table) {
                let dt_adv_filter = new DataTable(transaction_table, {
                    ajax: `{{ route('master_data.storage.detail_transaction_data_table') }}`,
                    columns: [{
                            data: 'transaction_date'
                        },
                        {
                            data: 'transaction_code'
                        },
                        {
                            data: 'contact'
                        },
                        {
                            data: 'due_date'
                        },
                        {
                            data: 'status'
                        },
                        {
                            data: 'down_payment_amount'
                        },
                        {
                            data: 'total_amount'
                        },
                    ],
                    columnDefs: [{
                            targets: [-1, -2],
                            className: 'text-end',
                            render: function(data, type, row) {
                                if (type === 'display' && data != null && !isNaN(data)) {
                                    return parseFloat(data).toLocaleString('id-ID');
                                }
                                return data ?? '';
                            }
                        },
                        {
                            orderable: false,
                            targets: -3,
                            render: function(data, type, full, meta) {
                                if (data == 'Belum Dibayar') {
                                    return `<span class="badge bg-danger">${data}</span>`;
                                } else {
                                    return `<span class="badge bg-danger">${data}</span>`;
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
                    orderCellsTop: true
                });
            }

            const batch_table = document.querySelector('#batch-table');

            if (batch_table) {
                let dt_adv_filter = new DataTable(batch_table, {
                    ajax: `{{ route('master_data.storage.detail_batch_data_table') }}`,
                    columns: [{
                            data: 'batch_code'
                        },
                        {
                            data: 'product_name'
                        },
                        {
                            data: 'received_date'
                        },
                        {
                            data: 'expired_date'
                        },
                        {
                            data: 'remaining_qty'
                        },
                        {
                            data: 'price_per_unit'
                        },
                        {
                            data: 'status'
                        },
                        {
                            data: 'transaction_source'
                        },
                        {
                            data: 'transaction_code'
                        },
                        {
                            data: 'notes'
                        },
                        {
                            data: 'last_updated'
                        }
                    ],
                    columnDefs: [{
                        targets: [-6],
                        className: 'text-end',
                        render: function(data, type, row) {
                            if (type === 'display' && data != null && !isNaN(data)) {
                                return parseFloat(data).toLocaleString('id-ID');
                            }
                            return data ?? '';
                        }
                    }],
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
                    orderCellsTop: true
                });
            }
            
            // Initialize DataTable for audit log
            $('#audit-log-table').DataTable({
                responsive: true,
                orderCellsTop: true,
                searching: false,
                paging: false,
                info: false,
                order: [[0, 'desc']] // Sort by date descending
            });
        });
    </script>
@endsection
