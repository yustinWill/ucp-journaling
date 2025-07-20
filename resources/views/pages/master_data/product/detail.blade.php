@extends('layouts.default')
@section('page_title')
    Detail Produk
@stop
@section('content')
    <div class="card mb-6">
        <div class="card-body">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <h4 class="mb-3">Detail Produk</h4>
                </div>
                <div class="col-md-4">
                    <div class="text-end">
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('master_data.product.edit', ['id' => $product['id']]) }}"
                                class="btn btn-md btn-warning waves-effect waves-light">
                                <i class="icon-base ti tabler-edit me-2"></i>
                                Edit Produk
                            </a>
                            <a href="{{ route('master_data.product.index') }}"
                                class="btn btn-md btn-secondary waves-effect waves-light">
                                <i class="icon-base ti tabler-arrow-left me-2"></i>
                                Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Product Title -->
            <h2>{{ $product['product_name'] ?? 'Air Mineral RIYO 600ml' }}</h2>
            <span>
                <h6>Data produk berdasarkan tanggal {{ date('Y-m-d H:i:s') }}</h6>
            </span>
            <hr class="my-6 mx-n6" />

            <div class="row g-6">
                <div class="col-md-6">
                    <label class="form-label">Kode Produk</label>
                    <input type="text" class="form-control" value="{{ $product['product_code'] ?? 'PROD-2025-001' }}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kategori Produk</label>
                    <input type="text" class="form-control" value="{{ $product['category'] ?? 'Minuman' }}" disabled />
                </div>
            </div>
            <hr class="my-6 mx-n6" />
            
            <div class="col-lg-12">
                <div class="demo-inline-spacing mt-4">
                    <div class="list-group list-group-horizontal-md text-md-center" role="tablist">
                        <a class="list-group-item list-group-item-action waves-effect active" id="product-info-list-item"
                            data-bs-toggle="list" href="#horizontal-product-info" aria-selected="true" role="tab">
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
                    </div>
                    <div class="tab-content px-0 mt-0">
                        <div class="tab-pane fade show active" id="horizontal-product-info" role="tabpanel">
                            <div id="product-info-content">
                                <!-- Info Produk Section -->
                    <div class="section-header mt-12">
                        <h6>Info Produk</h6>
                        <hr class="mx-n6">
                    </div>
                    
                    <!-- Row 1: Basic Information -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-8">
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Nama Produk</label>
                                    <input type="text" class="form-control" value="{{ $product['product_name'] ?? 'Air Mineral RIYO 600ml' }}" readonly />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Unit</label>
                                    <input type="text" class="form-control" value="{{ $product['unit'] ?? 'botol' }}" readonly />
                                </div>
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Kode Produk</label>
                                    <input type="text" class="form-control" value="{{ $product['product_code'] ?? 'PROD-2025-001' }}" readonly />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Kategori Produk</label>
                                    <input type="text" class="form-control" value="{{ $product['category'] ?? 'Minuman' }}" readonly />
                                </div>
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-12">
                                    <label class="form-label">Deskripsi</label>
                                    <textarea class="form-control" rows="3" readonly>{{ $product['description'] ?? 'Air mineral premium dengan kualitas terbaik, dikemas dalam botol 600ml yang praktis untuk konsumsi sehari-hari.' }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Gambar Produk</label>
                            <div class="image-display-area" style="height: 200px; width: 200px; border: 2px solid #ddd; display: flex; align-items: center; justify-content: center; flex-direction: column; border-radius: 8px;">
                                @if(isset($product['image']) && $product['image'])
                                    <img src="{{ $product['image'] }}" style="max-width: 100%; max-height: 100%; object-fit: cover;" />
                                @else
                                    <div class="text-center">
                                        <i class="icon-base ti tabler-photo" style="font-size: 48px; color: #ccc;"></i>
                                        <p class="text-muted mt-2">Tidak ada gambar</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Product Type Section -->
                    <div class="section-header mt-12">
                        <h6>Tipe Produk</h6>
                        <hr class="mx-n6">
                    </div>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Tipe</label>
                            <input type="text" class="form-control" value="{{ $product['product_type'] ?? 'Single' }}" readonly />
                        </div>
                    </div>
                    
                    <!-- Pricing & Inventory Section -->
                    <div class="section-header mt-12">
                        <h6>Harga & Persediaan</h6>
                        <hr class="mx-n6">
                    </div>
                    
                    <!-- Purchase Information -->
                    <div class="mb-4">
                        <h6 class="mb-3">Informasi Pembelian</h6>
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Harga Beli</label>
                                <input type="text" class="form-control" value="{{ $product['purchase_price'] ?? 'Rp 2,500' }}" readonly />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Akun Pembelian</label>
                                <input type="text" class="form-control" value="{{ $product['purchase_account'] ?? '[5001] - Pembelian Barang Dagang' }}" readonly />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Pajak Pembelian</label>
                                <input type="text" class="form-control" value="{{ $product['purchase_tax'] ?? '[PPN11] - PPN 11%' }}" readonly />
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sales Information -->
                    <div class="mb-4">
                        <h6 class="mb-3">Informasi Penjualan</h6>
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Harga Jual</label>
                                <input type="text" class="form-control" value="{{ $product['selling_price'] ?? 'Rp 4,000' }}" readonly />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Akun Penjualan</label>
                                <input type="text" class="form-control" value="{{ $product['sales_account'] ?? '[4001] - Penjualan Barang Dagang' }}" readonly />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Pajak Penjualan</label>
                                <input type="text" class="form-control" value="{{ $product['sales_tax'] ?? '[PPN11] - PPN 11%' }}" readonly />
                            </div>
                        </div>
                    </div>
                    
                    <!-- Inventory Information -->
                    <div class="mb-4">
                        <h6 class="mb-3">Informasi Persediaan</h6>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Batas Stok Minimum</label>
                                <input type="text" class="form-control" value="{{ $product['minimum_stock'] ?? '100' }}" readonly />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Akun Persediaan</label>
                                <input type="text" class="form-control" value="{{ $product['inventory_account'] ?? '[1301] - Persediaan Barang Dagang' }}" readonly />
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <label class="form-label">Pelacakan Persediaan</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" {{ ($product['track_quantity'] ?? true) ? 'checked' : '' }} disabled>
                                        <label class="form-check-label">
                                            Lacak kuantitas
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" {{ ($product['track_batch'] ?? false) ? 'checked' : '' }} disabled>
                                        <label class="form-check-label">
                                            Lacak batch
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                            </div>
                        </div>

                        <!-- Inventory Tab -->
                        <div class="tab-pane fade" id="horizontal-inventory" role="tabpanel">
                            <h5>Stok Persediaan per Gudang</h5>
                <div class="card-datatable mx-n6">
                    <table class="table table-bordered" id="inventory-table">
                        <thead>
                            <tr>
                                <th>Kode Gudang</th>
                                <th>Nama Gudang</th>
                                <th>Stok Tersedia</th>
                                <th>Stok Reserved</th>
                                <th>Total Stok</th>
                                <th>Harga Rata-rata</th>
                                <th>Nilai Persediaan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>GD-001</td>
                                <td>Gudang Utama</td>
                                <td class="text-end">2,500</td>
                                <td class="text-end">150</td>
                                <td class="text-end">2,650</td>
                                <td class="text-end">Rp 2,750</td>
                                <td class="text-end">Rp 7,287,500</td>
                            </tr>
                            <tr>
                                <td>GD-002</td>
                                <td>Gudang Cabang</td>
                                <td class="text-end">800</td>
                                <td class="text-end">50</td>
                                <td class="text-end">850</td>
                                <td class="text-end">Rp 2,700</td>
                                <td class="text-end">Rp 2,295,000</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="table-info">
                                <th></th>
                                <th>Total Keseluruhan</th>
                                <th class="text-end">3,300</th>
                                <th class="text-end">200</th>
                                <th class="text-end">3,500</th>
                                <th class="text-end">Rp 2,738</th>
                                <th class="text-end">Rp 9,582,500</th>
                            </tr>
                        </tfoot>
                    </table>
                            </div>
                        </div>

                        <!-- Audit Log Tab -->
                        <div class="tab-pane fade" id="horizontal-audit-log" role="tabpanel">
                            <h5>Riwayat Perubahan</h5>
                <div class="card-datatable mx-n6">
                    <table class="table table-bordered" id="audit-log-table">
                        <thead>
                            <tr>
                                <th>Tanggal & Waktu</th>
                                <th>Pengguna</th>
                                <th>Aksi</th>
                                <th>Field yang Diubah</th>
                                <th>Nilai Lama</th>
                                <th>Nilai Baru</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>2025-01-20 14:30:25</td>
                                <td>Admin User</td>
                                <td><span class="badge bg-label-warning">UPDATE</span></td>
                                <td>Harga Jual</td>
                                <td>Rp 3,800</td>
                                <td>Rp 4,000</td>
                            </tr>
                            <tr>
                                <td>2025-01-18 09:15:10</td>
                                <td>Manager Warehouse</td>
                                <td><span class="badge bg-label-info">UPDATE</span></td>
                                <td>Batas Minimum</td>
                                <td>50</td>
                                <td>100</td>
                            </tr>
                            <tr>
                                <td>2025-01-15 16:45:33</td>
                                <td>Admin User</td>
                                <td><span class="badge bg-label-success">CREATE</span></td>
                                <td>-</td>
                                <td>-</td>
                                <td>Produk baru dibuat</td>
                            </tr>
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
        // Initialize DataTables for inventory and audit log
        $('#inventory-table').DataTable({
            scrollX: true,
            responsive: true,
            orderCellsTop: true,
            pageLength: 10,
            language: {
                paginate: {
                    next: '<i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>',
                    previous: '<i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>',
                    first: '<i class="icon-base ti tabler-chevrons-left scaleX-n1-rtl icon-18px"></i>',
                    last: '<i class="icon-base ti tabler-chevrons-right scaleX-n1-rtl icon-18px"></i>'
                }
            }
        });

        $('#audit-log-table').DataTable({
            scrollX: true,
            responsive: true,
            orderCellsTop: true,
            order: [[0, 'desc']], // Sort by date descending
            pageLength: 10,
            language: {
                paginate: {
                    next: '<i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>',
                    previous: '<i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>',
                    first: '<i class="icon-base ti tabler-chevrons-left scaleX-n1-rtl icon-18px"></i>',
                    last: '<i class="icon-base ti tabler-chevrons-right scaleX-n1-rtl icon-18px"></i>'
                }
            }
        });
    });
</script>
@endsection