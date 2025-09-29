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
                            $totalActions = 2; // Edit and Revenue Split Config always present
                        @endphp

                        @if ($totalActions < 3)
                            <!-- Show buttons directly when less than 3 actions -->
                            <div class="d-flex gap-2 justify-content-end">
                                <button type="button" class="btn btn-outline-warning btn-md waves-effect waves-light" 
                                    onclick="openRevenueSplitModal()" id="revenue-split-btn">
                                    <i class="icon-base ti tabler-percentage me-2"></i>Revenue Split
                                </button>
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
                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="openRevenueSplitModal()">
                                            <i class="icon-base ti tabler-percentage me-2"></i>Revenue Split Config</a></li>
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
                                            {{-- <th>Stok Reserved</th> --}}
                                            {{-- <th>Total Stok</th> --}}
                                            <th>Harga Rata-rata</th>
                                            <th>Nilai Persediaan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($inventoryData as $inventory)
                                            <tr>
                                                <td>{{ $inventory->warehouse_code }}</td>
                                                <td>
                                                    <a href="{{ route('master_data.warehouse.detail', ['id' => $inventory->warehouse_id]) }}"
                                                        class="text-primary fw-semibold">
                                                        {{ $inventory->warehouse_name }}
                                                    </a>
                                                </td>
                                                <td class="text-end">{{ number_format($inventory->system_stock) }} {{ $inventory->unit }}
                                                </td>
                                                {{-- <td class="text-end">{{ number_format($inventory->reserved_stock) }} --}}
                                                {{-- </td> --}}
                                                {{-- <td class="text-end">{{ number_format($inventory->total_stock) }}</td> --}}
                                                <td class="text-end">0</td>
                                                <td class="text-end">0</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    {{-- <tfoot>
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
                                    </tfoot> --}}
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

    <!-- Revenue Split Configuration Modal -->
    <div class="modal fade" id="revenueSplitModal" tabindex="-1" aria-labelledby="revenueSplitModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="revenueSplitModalLabel">
                        <i class="icon-base ti tabler-percentage me-2"></i>Konfigurasi Revenue Split: {{ $product->name }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="revenueSplitForm">
                    <div class="modal-body">
                        <!-- Configuration Status -->
                        <div class="alert alert-info" id="configStatus" style="display: none;">
                            <i class="icon-base ti tabler-info-circle me-2"></i>
                            <span id="configStatusText"></span>
                        </div>

                        <!-- Base Price Section -->
                        <div class="section-header">
                            <h6>Harga Dasar Konfigurasi</h6>
                            <hr>
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Harga Dasar <span style="color: red;">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control currency-input" id="basePrice" name="base_price" 
                                        placeholder="25000" required>
                                </div>
                                <small class="text-muted">Harga referensi untuk perhitungan proporsi</small>
                            </div>
                        </div>

                        <!-- Revenue Split Configuration -->
                        <div class="section-header">
                            <h6>Konfigurasi Porsi Revenue</h6>
                            <hr>
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Porsi Perusahaan <span style="color: red;">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control currency-input" id="porsiPerusahaan" name="porsi_perusahaan" 
                                        placeholder="0" required>
                                </div>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="radio" name="remaining_allocation" value="perusahaan" id="remainingPerusahaan">
                                    <label class="form-check-label text-success" for="remainingPerusahaan">
                                        <small>Dapat sisa</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Porsi Owner <span style="color: red;">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control currency-input" id="porsiOwner" name="porsi_owner" 
                                        placeholder="0" required>
                                </div>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="radio" name="remaining_allocation" value="owner" id="remainingOwner">
                                    <label class="form-check-label text-success" for="remainingOwner">
                                        <small>Dapat sisa</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Porsi Pabrik <span style="color: red;">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control currency-input" id="porsiPabrik" name="porsi_pabrik" 
                                        placeholder="0" required>
                                </div>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="radio" name="remaining_allocation" value="pabrik" id="remainingPabrik">
                                    <label class="form-check-label text-success" for="remainingPabrik">
                                        <small>Dapat sisa</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Total and Remaining Display -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Total Porsi Tetap</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control" id="totalPorsi" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Sisa yang Akan Dialokasikan</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control" id="sisaAlokasi" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Notes Section -->
                        <div class="section-header">
                            <h6>Catatan</h6>
                            <hr>
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-12">
                                <label class="form-label">Catatan Konfigurasi</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3" 
                                    placeholder="Catatan tambahan mengenai konfigurasi revenue split ini..."></textarea>
                            </div>
                        </div>

                        <!-- Example Calculation -->
                        <div class="section-header">
                            <h6>Contoh Perhitungan</h6>
                            <hr>
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Test Harga Jual</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control currency-input" id="testPrice" placeholder="30000">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Quantity</label>
                                <input type="number" class="form-control" id="testQuantity" placeholder="1" value="1" min="1">
                            </div>
                        </div>
                        <div id="calculationResult" style="display: none;">
                            <div class="alert alert-secondary">
                                <h6>Hasil Perhitungan:</h6>
                                <div class="row">
                                    <div class="col-md-3">
                                        <strong>Perusahaan:</strong><br>
                                        <span id="resultPerusahaan">Rp 0</span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Owner:</strong><br>
                                        <span id="resultOwner">Rp 0</span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Pabrik:</strong><br>
                                        <span id="resultPabrik">Rp 0</span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Total:</strong><br>
                                        <span id="resultTotal">Rp 0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-warning" id="calculateBtn" onclick="calculateSplit()">
                            <i class="icon-base ti tabler-calculator me-2"></i>Hitung
                        </button>
                        <button type="button" class="btn btn-danger" id="deleteConfigBtn" onclick="deleteConfiguration()" style="display: none;">
                            <i class="icon-base ti tabler-trash me-2"></i>Hapus
                        </button>
                        <button type="submit" class="btn btn-success" id="saveConfigBtn">
                            <i class="icon-base ti tabler-device-floppy me-2"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @stop

    @section('js_script')
        <script>
            const productId = {{ $product->id }};

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

                // Currency formatting
                $('.currency-input').on('keyup', function() {
                    const value = this.value.replace(/[^0-9]/g, '');
                    this.value = formatCurrency(value);
                    calculateTotals();
                });

                // Revenue split form submission
                $('#revenueSplitForm').on('submit', function(e) {
                    e.preventDefault();
                    saveConfiguration();
                });

                // Remaining allocation radio change
                $('input[name="remaining_allocation"]').on('change', function() {
                    calculateTotals();
                });

                // Check for existing configuration on page load
                loadExistingConfiguration();
            });

            function formatCurrency(value) {
                if (!value) return '';
                return parseInt(value).toLocaleString('id-ID');
            }

            function parseCurrency(value) {
                if (!value) return 0;
                return parseInt(value.replace(/[^0-9]/g, '')) || 0;
            }

            function calculateTotals() {
                const basePrice = parseCurrency($('#basePrice').val());
                const porsiPerusahaan = parseCurrency($('#porsiPerusahaan').val());
                const porsiOwner = parseCurrency($('#porsiOwner').val());
                const porsiPabrik = parseCurrency($('#porsiPabrik').val());

                const totalPorsi = porsiPerusahaan + porsiOwner + porsiPabrik;
                const sisaAlokasi = Math.max(0, basePrice - totalPorsi);

                $('#totalPorsi').val(formatCurrency(totalPorsi.toString()));
                $('#sisaAlokasi').val(formatCurrency(sisaAlokasi.toString()));

                // Validation
                if (totalPorsi > basePrice) {
                    $('#sisaAlokasi').addClass('is-invalid');
                } else {
                    $('#sisaAlokasi').removeClass('is-invalid');
                }
            }

            function openRevenueSplitModal() {
                $('#revenueSplitModal').modal('show');
                loadExistingConfiguration();
            }

            function loadExistingConfiguration() {
                $.get(`/master_data/product/revenue-split/${productId}`)
                    .done(function(response) {
                        if (response.success && response.data.has_config) {
                            const config = response.data.config;
                            populateForm(config);
                            $('#configStatus').show();
                            $('#configStatusText').text('Konfigurasi sudah ada. Anda dapat mengubah atau menghapusnya.');
                            $('#deleteConfigBtn').show();
                        } else {
                            resetForm();
                            $('#configStatus').hide();
                            $('#deleteConfigBtn').hide();
                        }
                    })
                    .fail(function() {
                        console.error('Failed to load configuration');
                    });
            }

            function populateForm(config) {
                $('#basePrice').val(formatCurrency(config.base_price.toString()));
                $('#porsiPerusahaan').val(formatCurrency(config.porsi_perusahaan.toString()));
                $('#porsiOwner').val(formatCurrency(config.porsi_owner.toString()));
                $('#porsiPabrik').val(formatCurrency(config.porsi_pabrik.toString()));
                $('#notes').val(config.notes || '');
                $(`input[name="remaining_allocation"][value="${config.remaining_allocation}"]`).prop('checked', true);
                calculateTotals();
            }

            function resetForm() {
                $('#revenueSplitForm')[0].reset();
                $('#totalPorsi').val('');
                $('#sisaAlokasi').val('');
                $('#calculationResult').hide();
                $('input[name="remaining_allocation"][value="perusahaan"]').prop('checked', true);
            }

            function saveConfiguration() {
                const data = {
                    base_price: parseCurrency($('#basePrice').val()),
                    porsi_perusahaan: parseCurrency($('#porsiPerusahaan').val()),
                    porsi_owner: parseCurrency($('#porsiOwner').val()),
                    porsi_pabrik: parseCurrency($('#porsiPabrik').val()),
                    remaining_allocation: $('input[name="remaining_allocation"]:checked').val(),
                    notes: $('#notes').val()
                };

                // Validation
                if (data.base_price <= 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: 'Harga dasar harus lebih dari 0'
                    });
                    return;
                }

                if ((data.porsi_perusahaan + data.porsi_owner + data.porsi_pabrik) > data.base_price) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: 'Total porsi tetap tidak boleh melebihi harga dasar'
                    });
                    return;
                }

                $('#saveConfigBtn').prop('disabled', true).html('<i class="spinner-border spinner-border-sm me-2"></i>Menyimpan...');

                $.ajax({
                    url: `/master_data/product/revenue-split/${productId}`,
                    method: 'POST',
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                })
                .done(function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Konfigurasi revenue split berhasil disimpan'
                        });
                        $('#revenueSplitModal').modal('hide');
                        // Update button text if this is a new config
                        $('#revenue-split-btn').removeClass('btn-outline-warning').addClass('btn-outline-success')
                            .html('<i class="icon-base ti tabler-percentage me-2"></i>Revenue Split <i class="icon-base ti tabler-check ms-1"></i>');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: response.message || 'Terjadi kesalahan saat menyimpan konfigurasi'
                        });
                    }
                })
                .fail(function(xhr) {
                    const response = xhr.responseJSON;
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: response?.message || 'Terjadi kesalahan saat menyimpan konfigurasi'
                    });
                })
                .always(function() {
                    $('#saveConfigBtn').prop('disabled', false).html('<i class="icon-base ti tabler-device-floppy me-2"></i>Simpan');
                });
            }

            function calculateSplit() {
                const testPrice = parseCurrency($('#testPrice').val());
                const testQuantity = parseInt($('#testQuantity').val()) || 1;

                if (testPrice <= 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Masukkan harga test yang valid'
                    });
                    return;
                }

                $('#calculateBtn').prop('disabled', true).html('<i class="spinner-border spinner-border-sm me-2"></i>Menghitung...');

                $.ajax({
                    url: `/master_data/product/revenue-split/${productId}/calculate`,
                    method: 'POST',
                    data: {
                        selling_price: testPrice,
                        quantity: testQuantity
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                })
                .done(function(response) {
                    if (response.success) {
                        const data = response.data;
                        $('#resultPerusahaan').text('Rp ' + formatCurrency(data.porsi_perusahaan.toString()));
                        $('#resultOwner').text('Rp ' + formatCurrency(data.porsi_owner.toString()));
                        $('#resultPabrik').text('Rp ' + formatCurrency(data.porsi_pabrik.toString()));
                        $('#resultTotal').text('Rp ' + formatCurrency(data.total_amount.toString()));
                        $('#calculationResult').show();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: response.message || 'Gagal menghitung revenue split'
                        });
                    }
                })
                .fail(function(xhr) {
                    const response = xhr.responseJSON;
                    if (response?.message?.includes('No revenue split configuration')) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Perhatian',
                            text: 'Silakan simpan konfigurasi terlebih dahulu sebelum menghitung'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: response?.message || 'Terjadi kesalahan saat menghitung'
                        });
                    }
                })
                .always(function() {
                    $('#calculateBtn').prop('disabled', false).html('<i class="icon-base ti tabler-calculator me-2"></i>Hitung');
                });
            }

            function deleteConfiguration() {
                Swal.fire({
                    title: 'Hapus Konfigurasi?',
                    text: 'Konfigurasi revenue split akan dihapus. Tindakan ini tidak dapat dibatalkan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-danger',
                        cancelButton: 'btn btn-secondary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            url: `/master_data/product/revenue-split/${productId}`,
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        })
                        .done(function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Konfigurasi revenue split berhasil dihapus'
                                });
                                $('#revenueSplitModal').modal('hide');
                                // Reset button appearance
                                $('#revenue-split-btn').removeClass('btn-outline-success').addClass('btn-outline-warning')
                                    .html('<i class="icon-base ti tabler-percentage me-2"></i>Revenue Split');
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: response.message || 'Terjadi kesalahan saat menghapus konfigurasi'
                                });
                            }
                        })
                        .fail(function(xhr) {
                            const response = xhr.responseJSON;
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: response?.message || 'Terjadi kesalahan saat menghapus konfigurasi'
                            });
                        });
                    }
                });
            }
        </script>
    @endsection
