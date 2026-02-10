@extends('layouts.default')
@section('page_title')
    Detail Warehouse
@stop
@php
    // Helper: format number, strip trailing zeros (5,00 → 5 | 0,5000 → 0,5 | 0,501000 → 0,501)
    function formatQty($value) {
        if ($value == floor($value)) {
            return number_format($value, 0, ',', '.');
        }
        $formatted = number_format($value, 4, ',', '.');
        return rtrim(rtrim($formatted, '0'), ',');
    }
@endphp
@section('content')
    <div class="card mb-6">
        <div class="card-body">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <h4 class="mb-3">Detail Warehouse</h4>
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
                                <a href="{{ route('master_data.warehouse.edit', $warehouse->id) }}" class="btn btn-primary btn-md waves-effect waves-light">
                                    <i class="icon-base ti tabler-edit me-2"></i>Edit
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Storage Title -->
            <h2>{{ $warehouse->name }}</h2>
            <span>
                <h6>Data gudang berdasarkan tanggal {{ $warehouse->updated_at }}</h6>
            </span>
            <hr class="my-6 mx-n6" />

            <div class="row g-6">
                <div class="col-md-6">
                    <label class="form-label">Kode Warehouse</label>
                    <input type="text" class="form-control" value="{{ $warehouse->code }}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <div class="form-control-plaintext">
                        @if($warehouse->status == 'ACTIVE')
                            <span class="badge bg-label-success">{{ $warehouse->status }}</span>
                        @elseif($warehouse->status == 'MAINTENANCE')
                            <span class="badge bg-label-warning">{{ $warehouse->status }}</span>
                        @else
                            <span class="badge bg-label-secondary">{{ $warehouse->status }}</span>
                        @endif
                    </div>
                </div>
            </div>
            <hr class="my-6 mx-n6" />
            
            <div class="col-lg-12">
                <div class="demo-inline-spacing mt-4">
                    <div class="list-group list-group-horizontal-md text-md-center" role="tablist">
                        <a class="list-group-item list-group-item-action waves-effect active" id="warehouse-info-list-item"
                            data-bs-toggle="list" href="#horizontal-warehouse-info" aria-selected="true" role="tab">
                            <i class="icon-base ti tabler-info-circle me-2"></i>
                            <b>Informasi Detail</b>
                        </a>
                        <a class="list-group-item list-group-item-action waves-effect" id="inventory-list-item"
                            data-bs-toggle="list" href="#horizontal-inventory" aria-selected="false" role="tab">
                            <i class="icon-base ti tabler-package me-2"></i>
                            <b>Persediaan</b>
                        </a>
                        <a class="list-group-item list-group-item-action waves-effect" id="audit-log-list-item"
                            data-bs-toggle="list" href="#horizontal-audit-log" aria-selected="false" role="tab">
                            <i class="icon-base ti tabler-history me-2"></i>
                            <b>Audit Log</b>
                        </a>
                        <a class="list-group-item list-group-item-action waves-effect" id="stock-movement-log-list-item"
                            data-bs-toggle="list" href="#horizontal-stock-movement-log" aria-selected="false" role="tab">
                            <i class="icon-base ti tabler-history me-2"></i>
                            <b>Stock Movement Log</b>
                        </a>
                    </div>
                    <div class="tab-content px-0 mt-0">
                        <div class="tab-pane fade show active" id="horizontal-warehouse-info" role="tabpanel">
                            <!-- Info Warehouse Section -->
                            <div class="section-header mt-12">
                                <h6>Info Warehouse</h6>
                                <hr class="mx-n6">
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Nama Warehouse</label>
                                    <input type="text" class="form-control" value="{{ $warehouse->name }}" disabled />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Kode Warehouse</label>
                                    <input type="text" class="form-control" value="{{ $warehouse->code }}" disabled />
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Penanggung Jawab</label>
                                    <input type="text" class="form-control" value="{{ $warehouse->pic_names ?? '-' }}" disabled />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Status</label>
                                    <div class="form-control-plaintext">
                                        @if($warehouse->status == 'ACTIVE')
                                            <span class="badge bg-label-success">{{ $warehouse->status }}</span>
                                        @elseif($warehouse->status == 'MAINTENANCE')
                                            <span class="badge bg-label-warning">{{ $warehouse->status }}</span>
                                        @else
                                            <span class="badge bg-label-secondary">{{ $warehouse->status }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Alamat Section -->
                            <div class="section-header mt-12">
                                <h6>Alamat Warehouse</h6>
                                <hr class="mx-n6">
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Provinsi</label>
                                    <input type="text" class="form-control" value="{{ $warehouse->province ?? '-' }}" disabled />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Kota</label>
                                    <input type="text" class="form-control" value="{{ $warehouse->city ?? '-' }}" disabled />
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Kecamatan</label>
                                    <input type="text" class="form-control" value="{{ $warehouse->district ?? '-' }}" disabled />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Kelurahan</label>
                                    <input type="text" class="form-control" value="{{ $warehouse->sub_district ?? '-' }}" disabled />
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Alamat Lengkap</label>
                                    <textarea class="form-control" rows="3" disabled>{{ $warehouse->address ?? '-' }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Kode POS</label>
                                    <input type="text" class="form-control" value="{{ $warehouse->postal_code ?? '-' }}" disabled />
                                </div>
                            </div>

                            <!-- Tags Section -->
                            <div class="section-header mt-12">
                                <h6>Informasi Tambahan</h6>
                                <hr class="mx-n6">
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Tag</label>
                                    <div class="form-control-plaintext">
                                        @if($warehouse->tags)
                                            {{-- @php
                                                $tags = json_decode($warehouse->tags, true);
                                            @endphp --}}
                                            @if($warehouse->tags && count($warehouse->tags) > 0)
                                                @foreach($warehouse->tags as $tag)
                                                    @if($tag->code)
                                                        <span class="badge {{ $tag->preview ?? 'bg-label-primary' }} me-1">{{ $tag->code }}</span>
                                                    @endif
                                                @endforeach
                                            @else
                                                -
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="horizontal-inventory" role="tabpanel">
                            <div class="card-datatable">
                                <table id="product-table" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Kode Produk</th>
                                            <th>Nama Produk</th>
                                            <th>Kategori Produk</th>
                                            <th>Qty Tersedia</th>
                                            <th>Batas Minimum</th>
                                            <th>Unit</th>
                                            <th>Harga Beli Terakhir</th>
                                            <th>Harga Jual</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($productWareHouseStocks as $product)
                                            <tr>
                                                <td>{{ $product->code }}</td>
                                                <td>{{ $product->name }}</td>
                                                <td>{{ $product->category_name }}</td>
                                                <td class="text-end">{{ formatQty($product->system_stock) }}</td>
                                                <td class="text-end">{{ formatQty($product->minimum_stock) }}</td>
                                                <td>{{ $product->unit }}</td>
                                                <td class="text-end">Rp {{ number_format($product->latest_purchase_price ?? 0, 0, ',', '.') }}</td>
                                                <td class="text-end">Rp {{ number_format($product->unit_sell_price ?? 0, 0, ',', '.') }}</td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="9" class="text-center">Tidak ada Stok yang tersedia</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Audit Log Tab -->
                        <div class="tab-pane fade" id="horizontal-audit-log" role="tabpanel">
                            <div class="section-header mt-6">
                                <h6>Log Audit Warehouse</h6>
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
                                        @forelse($auditLogs as $log)
                                            <tr>
                                                <td>{{ $log->created_at }}</td>
                                                <td><a href="#" class="text-primary fw-semibold">{{ $log->user_name ?? 'System' }}</a></td>
                                                <td>
                                                    @if($log->action_type == 'CREATE')
                                                        <span class="badge bg-label-success">{{ $log->action_type }}</span>
                                                    @elseif($log->action_type == 'UPDATE') 
                                                        <span class="badge bg-label-info">{{ $log->action_type }}</span>
                                                    @elseif($log->action_type == 'DELETE')
                                                        <span class="badge bg-label-danger">{{ $log->action_type }}</span>
                                                    @else
                                                        <span class="badge bg-label-secondary">{{ $log->action_type }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $log->description }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">Tidak ada log audit yang tersedia</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Stock Movement Log Tab -->
                        <div class="tab-pane fade" id="horizontal-stock-movement-log" role="tabpanel">
                            <div class="section-header mt-6">
                                <h6>Log Stock Movement Warehouse</h6>
                            </div>
                            <div class="mx-n6">
                                <table id="stock-movement-log-table" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Produk</th>
                                            <th>Tipe</th>
                                            <th>Referensi</th>
                                            <th>Qty</th>
                                            <th>Stok Sebelum</th>
                                            <th>Stok Setelah</th>
                                            <th>Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($StockMovementLogs as $stock)
                                            <tr>
                                                <td>{{ $stock->movement_date }}</td>
                                                <td><a href="{{ route('master_data.product.detail', ['id' => $stock->product_id]) }}" class="text-primary fw-semibold">{{ $stock->product_code }}</a><br><small class="text-muted">{{ $stock->product_name }}</small></td>
                                                <td>
                                                    @if($stock->direction === 'IN')
                                                        <span class="badge bg-success">{{ $stock->movement_type }}</span>
                                                    @elseif($stock->direction === 'OUT')
                                                        <span class="badge bg-danger">{{ $stock->movement_type }}</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ $stock->movement_type }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $stock->reference_number }}</td>
                                                <td class="text-end">{{ number_format($stock->qty ?? 0, 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($stock->stock_before ?? 0, 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($stock->stock_after ?? 0, 0, ',', '.') }}</td>
                                                <td>
                                                    @if($stock->warehouse_from)
                                                        Dari: {{ $stock->warehouse_from }}
                                                    @elseif($stock->warehouse_to)
                                                        Ke: {{ $stock->warehouse_to }}
                                                    @else
                                                        {{ $stock->notes ?? '-' }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">Tidak ada log pergerakan stok yang tersedia</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js_script')
    <script>
        $(function() {
            //DataTable for inventory
            // const product_table = document.querySelector('#product-table');

            // if (product_table) {
            //     let dt_adv_filter = new DataTable(product_table, {
            //         ajax: `{{ route('master_data.warehouse.detail_product_data_table') }}`,
            //         columns: [{
            //                 data: 'product_code'
            //             },
            //             {
            //                 data: 'product_name'
            //             },
            //             {
            //                 data: 'category'
            //             },
            //             {
            //                 data: 'total_stock'
            //             },
            //             {
            //                 data: 'available_qty'
            //             },
            //             {
            //                 data: 'minimum'
            //             },
            //             {
            //                 data: 'unit'
            //             },
            //             {
            //                 data: 'average_price'
            //             },
            //             {
            //                 data: 'last_purchase_price'
            //             },
            //             {
            //                 data: 'purchase_price'
            //             },
            //             {
            //                 data: 'selling_price'
            //             }
            //         ],
            //         columnDefs: [{
            //             targets: [-1, -2, -3, -4, -6, -7, -8],
            //             className: 'text-end',
            //             render: function(data, type, row) {
            //                 if (type === 'display' && data != null && !isNaN(data)) {
            //                     return parseFloat(data).toLocaleString('id-ID');
            //                 }
            //                 return data ?? '';
            //             }
            //         }],
            //         // scrollX: true,
            //         responsive: true,
            //         orderCellsTop: true
            //     });
            // }
            
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