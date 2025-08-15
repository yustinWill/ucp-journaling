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

                <!-- Action Panel -->
                <div class="col-md-4">
                    <div class="text-end">
                        @php
                            $totalActions = 1; // Edit always present
                            // Add conditional actions based on product status if needed
                            // if($product['status'] === 'DRAFT') {
                            //     $totalActions++;
                            // }
                        @endphp

                        @if ($totalActions < 3)
                            <!-- Show buttons directly when less than 3 actions -->
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="{{ route('master_data.product.edit', ['id' => $product->id]) }}"
                                    class="btn btn-primary btn-md waves-effect waves-light">
                                    <i class="icon-base ti tabler-edit me-2"></i>Edit
                                </a>
                            </div>
                        @else
                            <!-- Show dropdown for 3 or more actions -->
                            <div class="dropdown">
                                <button type="button"
                                    class="btn btn-primary btn-md waves-effect waves-light dropdown-toggle"
                                    data-bs-toggle="dropdown">
                                    Aksi
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item"
                                            href="{{ route('master_data.product.edit', ['id' => $product->id]) }}">
                                            <i class="icon-base ti tabler-edit me-2"></i>Edit</a></li>
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Product Title -->
            <h2>{{ $product->name }}</h2>
            <span>
                <h6>Data produk berdasarkan tanggal {{ $product->updated_at }}</h6>
            </span>
            <hr class="my-6 mx-n6" />

            <div class="row g-6">
                <div class="col-md-6">
                    <label class="form-label">Kode Produk</label>
                    <input type="text" class="form-control" value="{{ $product->code }}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kategori Produk</label>
                    <input type="text" class="form-control" value="{{ $product->category_name }}" disabled />
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
                                                <input type="text" class="form-control" value="{{ $product->name }}"
                                                    disabled />
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Unit</label>
                                                <input type="text" class="form-control" value="{{ $product->unit_name }}"
                                                    disabled />
                                            </div>
                                        </div>
                                        <div class="row g-3 mb-4">
                                            <div class="col-md-6">
                                                <label class="form-label">Kode Produk</label>
                                                <input type="text" class="form-control" value="{{ $product->code }}"
                                                    disabled />
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Kategori Produk</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $product->category_name }}" disabled />
                                            </div>
                                        </div>
                                        <div class="row g-3 mb-4">
                                            <div class="col-md-12">
                                                <label class="form-label">Deskripsi</label>
                                                <textarea class="form-control" rows="3" disabled>{{ $product->description }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Gambar Produk</label>
                                        <div class="image-display-area"
                                            style="height: 200px; width: 200px; border: 2px solid #ddd; display: flex; align-items: center; justify-content: center; flex-direction: column; border-radius: 8px;">
                                            @if (isset($product->main_image) && $product->main_image)
                                                <img src="{{ $product->main_image }}"
                                                    style="max-width: 100%; max-height: 100%; object-fit: cover;" />
                                            @else
                                                <div class="text-center">
                                                    <i class="icon-base ti tabler-photo"
                                                        style="font-size: 48px; color: #ccc;"></i>
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
                                        <input type="text" class="form-control" value="{{ $product->type }}"
                                            disabled />
                                    </div>
                                </div>

                                <!-- Pricing & Inventory Section -->
                                <div class="section-header mt-12">
                                    <h6>Harga & Persediaan</h6>
                                    <hr class="mx-n6">
                                </div>

                                <!-- Purchase Information -->
                                <div class="mb-8">
                                    <h6 class="mb-3">Informasi Pembelian</h6>
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Harga Beli</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->unit_purchase_price }}" disabled />
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Akun Pembelian</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->f_purchase_coa_code }} {{ $product->purchase_coa_name }}"
                                                disabled />
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Pajak Pembelian</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->f_purchase_tax_id }} {{ $product->purchase_tax_name }}" disabled />
                                        </div>
                                    </div>
                                </div>

                                <!-- Sales Information -->
                                <div class="mb-8">
                                    <h6 class="mb-3">Informasi Penjualan</h6>
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Harga Jual</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->unit_sell_price }}" disabled />
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Akun Penjualan</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->f_sell_coa_code }} {{ $product->sell_coa_name }}" disabled />
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Pajak Penjualan</label>
                                            <input type="text" class="form-control" value="{{ $product->f_sell_tax_id }} {{ $product->sell_tax_name }}"
                                                disabled />
                                        </div>
                                    </div>
                                </div>

                                <!-- Cost Analysis Information -->
                                <div class="mb-8">
                                    <h6 class="mb-3">Analisis Biaya</h6>
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Estimasi HPP</label>
                                            <input type="text" class="form-control"
                                                value="0" disabled />
                                            <small class="form-text text-muted">Harga Pokok Penjualan rata-rata</small>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Margin Keuntungan</label>
                                            <input type="text" class="form-control"
                                                value="0" disabled />
                                            <small class="form-text text-muted">Persentase margin dari harga jual</small>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Keuntungan per Unit</label>
                                            <input type="text" class="form-control"
                                                value="0" disabled />
                                            <small class="form-text text-muted">Selisih harga jual dan HPP</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Inventory Information -->
                                <div class="mb-4">
                                    <h6 class="mb-3">Informasi Persediaan</h6>
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Batas Stok Minimum</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->minimum_stock }}" disabled />
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Akun Persediaan</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->f_inventory_coa_code }} {{ $product->inventory_coa_name }}" disabled />
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <label class="form-label">Pelacakan Persediaan</label>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        {{ $product->track_qty_only ? 'checked' : '' }} disabled>
                                                    <label class="form-check-label">
                                                        Lacak kuantitas
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        {{ $product->track_batch ? 'checked' : '' }} disabled>
                                                    <label class="form-check-label">
                                                        Lacak batch
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tag and Memo Section -->
                                <div class="section-header mt-12">
                                    <h6>Tag & Catatan</h6>
                                    <hr class="mx-n6">
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Tag</label>
                                        @if ($product->tags && count($product->tags) > 0)
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach ($product->tags as $tag)
                                                    <span
                                                        class="badge bg-label-{{ $tag->preview }}">{{ $tag->code }}</span>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-muted">Tidak ada tag</div>
                                        @endif
                                    </div>
                                    {{-- <div class="col-md-6">
                                        <label class="form-label">Memo (Internal)</label>
                                        <textarea class="form-control" rows="3" disabled>{{ $product['memo'] }}</textarea>
                                    </div> --}}
                                </div>
                            </div>
                        </div>

                        <!-- Inventory Tab -->
                        <div class="tab-pane fade" id="horizontal-inventory" role="tabpanel">
                            <div class="section-header mt-6">
                                <h6>Stok Persediaan per Gudang</h6>
                            </div>
                            <div class="mx-n6">
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
                                        @foreach ($inventoryData as $inventory)
                                            <tr>
                                                <td>{{ $inventory['warehouse_code'] }}</td>
                                                <td>
                                                    <a href="{{ route('master_data.storage.detail', ['id' => $inventory['warehouse_id']]) }}"
                                                        class="text-primary fw-semibold">
                                                        {{ $inventory['warehouse_name'] }}
                                                    </a>
                                                </td>
                                                <td class="text-end">{{ number_format($inventory['available_stock']) }}
                                                </td>
                                                <td class="text-end">{{ number_format($inventory['reserved_stock']) }}
                                                </td>
                                                <td class="text-end">{{ number_format($inventory['total_stock']) }}</td>
                                                <td class="text-end">{{ $inventory['average_price'] }}</td>
                                                <td class="text-end">{{ $inventory['inventory_value'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-info">
                                            <th></th>
                                            <th>Total Keseluruhan</th>
                                            <th class="text-end">
                                                {{ number_format(array_sum(array_column($inventoryData, 'available_stock'))) }}
                                            </th>
                                            <th class="text-end">
                                                {{ number_format(array_sum(array_column($inventoryData, 'reserved_stock'))) }}
                                            </th>
                                            <th class="text-end">
                                                {{ number_format(array_sum(array_column($inventoryData, 'total_stock'))) }}
                                            </th>
                                            <th class="text-end">Rp
                                                {{ number_format(array_sum(array_column($inventoryData, 'total_stock')) > 0? array_sum(array_map(function ($item) {return $item['total_stock'] * (float) str_replace(['Rp ', ','], '', $item['average_price']);}, $inventoryData)) / array_sum(array_column($inventoryData, 'total_stock')): 0) }}
                                            </th>
                                            <th class="text-end">Rp
                                                {{ number_format(array_sum(array_map(function ($item) {return (float) str_replace(['Rp ', ','], '', $item['inventory_value']);}, $inventoryData))) }}
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <!-- Audit Log Tab -->
                        <div class="tab-pane fade" id="horizontal-audit-log" role="tabpanel">
                            <div class="section-header mt-6">
                                <h6>Riwayat Perubahan</h6>
                            </div>
                            <div class="mx-n6">
                                <table class="table table-bordered" id="audit-log-table">
                                    <thead>
                                        <tr>
                                            <th>Tanggal & Waktu</th>
                                            <th>Pengguna</th>
                                            <th>Aksi</th>
                                            <th>Deskripsi</th>
                                            <th>Lihat Detail</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($auditLogs as $log)
                                            <tr>
                                                <td>{{ $log->created_at }}</td>
                                                <td><a href="{{ route('master_data.user.detail', ['id' => $log->user_id]) }}"
                                                        class="text-primary fw-semibold">{{ $log->user_name }}</a></td>
                                                <td>
                                                    @if ($log->action_type === 'CREATE')
                                                        <span class="badge bg-label-success">CREATE</span>
                                                    @elseif($log->action_type === 'UPDATE')
                                                        <span class="badge bg-label-warning">UPDATE</span>
                                                    @elseif($log->action_type === 'DELETE')
                                                        <span class="badge bg-label-danger">DELETE</span>
                                                    @else
                                                        <span class="badge bg-label-info">{{ $log->action_type }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $log->description }}</td>
                                                <td>
                                                    @if ($log->action_type == 'UPDATE')
                                                        <a target="_blank"
                                                            href="{{ route('audit_log.detail', ['id' => $log->id, 'audit_log_type' => $auditLogType]) }}"
                                                            class="btn btn-sm btn-primary waves-effect waves-light"
                                                            title="Detail">
                                                            <i class="icon-xs icon-base ti tabler-eye"></i>
                                                        </a>
                                                    @endif
                                                </td>
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
                // Initialize DataTables for tables inside tabs (no scrollX)
                $('#inventory-table').DataTable({
                    responsive: true,
                    orderCellsTop: true,
                    searching: false,
                    paging: false,
                    info: false
                });

                $('#audit-log-table').DataTable({
                    responsive: true,
                    orderCellsTop: true,
                    order: [
                        [0, 'desc']
                    ], // Sort by date descending
                    searching: false,
                    paging: false,
                    info: false
                });
            });
        </script>
    @endsection
