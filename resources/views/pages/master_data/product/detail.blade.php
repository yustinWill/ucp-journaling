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
                                    <!-- <li><a class="dropdown-item" href="javascript:void(0)" onclick="openRevenueSplitModal()">
                                                    <i class="icon-base ti tabler-percentage me-2"></i>Revenue Split Config</a></li> -->
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
                        <a class="list-group-item list-group-item-action waves-effect" id="batch-list-item"
                            data-bs-toggle="list" href="#horizontal-batch" aria-selected="false" role="tab">
                            <i class="icon-base ti tabler-layers-linked me-2"></i>
                            <b>Batch Produk</b>
                        </a>
                        @if ($product->type === 'bundle')
                            <a class="list-group-item list-group-item-action waves-effect" id="bundle-composition-list-item"
                                data-bs-toggle="list" href="#horizontal-bundle-composition" aria-selected="false"
                                role="tab">
                                <i class="icon-base ti tabler-components me-2"></i>
                                <b>Komposisi Bundle</b>
                            </a>
                        @endif
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

                                <!-- Product Units Section -->
                                <div class="section-header mt-12">
                                    <h6>Unit Produk</h6>
                                    <hr class="mx-n6">
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-12">
                                        @if(count($productUnits) > 0)
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th style="width: 50%">Unit</th>
                                                            <th style="width: 30%">Konversi</th>
                                                            <th style="width: 20%">Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($productUnits as $productUnit)
                                                            <tr>
                                                                <td>
                                                                    <strong>{{ $productUnit->unit_name }}</strong>
                                                                    @if($productUnit->is_default == 1)
                                                                        <span class="badge bg-primary ms-2">Unit Dasar</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if($productUnit->is_default == 1)
                                                                        1 {{ $productUnit->unit_name }} = 1 {{ $productUnit->unit_name }}
                                                                    @else
                                                                        @php
                                                                            $baseUnit = collect($productUnits)->firstWhere('is_default', 1);
                                                                            // Smart decimal formatting for conversion factor
                                                                            if ($productUnit->conversion_factor == floor($productUnit->conversion_factor)) {
                                                                                $formattedFactor = number_format($productUnit->conversion_factor, 0, ',', '.');
                                                                            } else {
                                                                                $formatted = number_format($productUnit->conversion_factor, 4, ',', '.');
                                                                                $formattedFactor = rtrim(rtrim($formatted, '0'), ',');
                                                                            }
                                                                        @endphp
                                                                        1 {{ $productUnit->unit_name }} = {{ $formattedFactor }} {{ $baseUnit->unit_name ?? 'Unit Dasar' }}
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if($productUnit->status == 'ACTIVE')
                                                                        <span class="badge bg-success">Aktif</span>
                                                                    @else
                                                                        <span class="badge bg-secondary">Tidak Aktif</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="alert alert-info">
                                                <i class="ti ti-info-circle me-2"></i>
                                                Tidak ada unit tambahan. Produk menggunakan unit dasar: <strong>{{ $product->unit_name }}</strong>
                                            </div>
                                        @endif
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
                                    @if($product->type === 'bundle')
                                    <div class="col-md-6">
                                        <label class="form-label">Produk Packaging</label>
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox"
                                                {{ $product->is_packaging ? 'checked' : '' }} disabled>
                                            <label class="form-check-label">
                                                {{ $product->is_packaging ? 'Ya, produk ini untuk packing' : 'Bukan produk packaging' }}
                                            </label>
                                        </div>
                                    </div>
                                    @endif
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
                                                value="{{ $product->f_purchase_tax_id }} {{ $product->purchase_tax_name }}"
                                                disabled />
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
                                                value="{{ $product->f_sell_coa_code }} {{ $product->sell_coa_name }}"
                                                disabled />
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Pajak Penjualan</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->f_sell_tax_id }} {{ $product->sell_tax_name }}"
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
                                            <input type="text" class="form-control" value="Rp. {{ number_format($product->estimated_hpp, 0, ',', '.') }}" disabled />
                                            <small class="form-text text-muted">Harga Pokok Penjualan rata-rata</small>
                                        </div>
                                        {{-- <div class="col-md-4">
                                            <label class="form-label">Margin Keuntungan</label>
                                            <input type="text" class="form-control" value="0" disabled />
                                            <small class="form-text text-muted">Persentase margin dari harga jual</small>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Keuntungan per Unit</label>
                                            <input type="text" class="form-control" value="0" disabled />
                                            <small class="form-text text-muted">Selisih harga jual dan HPP</small>
                                        </div> --}}
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
                                                value="{{ $product->f_inventory_coa_code }} {{ $product->inventory_coa_name }}"
                                                disabled />
                                        </div>
                                    </div>

                                    @if($product->type === 'bundle')
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Qty Batch Bundle</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->bundle_batch_qty ?? 0 }}" disabled />
                                        </div>
                                    </div>
                                    @endif

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
                                                <td class="text-end">{{ number_format($inventory->system_stock) }}
                                                    {{ $inventory->unit }}
                                                </td>
                                                {{-- <td class="text-end">{{ number_format($inventory->reserved_stock) }} --}}
                                                {{-- </td> --}}
                                                {{-- <td class="text-end">{{ number_format($inventory->total_stock) }}</td> --}}
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

                        <!-- Batch Tab -->
                        <div class="tab-pane fade" id="horizontal-batch" role="tabpanel">
                            <div class="section-header mt-6">
                                <h6>Riwayat Batch Produk</h6>
                            </div>
                            <div class="mx-n6">
                                <table class="table table-bordered" id="batch-table">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Tipe</th>
                                            <th>Kode Batch</th>
                                            <th>Referensi</th>
                                            <th>Sumber</th>
                                            <th>Qty IN</th>
                                            <th>Qty OUT</th>
                                            <th>Qty Tersisa</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            // Build combined list of IN (batch creation) and OUT (transactions)
                                            $combinedHistory = [];

                                            // Add batch creations as IN
                                            foreach ($batchData['batches'] as $batch) {
                                                $combinedHistory[] = [
                                                    'date' => $batch['batch_date'],
                                                    'type' => 'IN',
                                                    'batch_reference' => $batch['batch_reference'],
                                                    'reference_code' => $batch['batch_reference'],
                                                    'source_type' => $batch['source_type'],
                                                    'qty_in' => $batch['initial_qty'],
                                                    'qty_out' => 0,
                                                    'remaining_qty' => $batch['remaining_qty'],
                                                ];

                                                // Add transactions (OUT) for this batch
                                                if (isset($batch['transactions']) && count($batch['transactions']) > 0) {
                                                    foreach ($batch['transactions'] as $transaction) {
                                                        $combinedHistory[] = [
                                                            'date' => \Carbon\Carbon::parse($transaction['transaction_date'])->format('Y-m-d'),
                                                            'type' => $transaction['transaction_type'],
                                                            'batch_reference' => $batch['batch_reference'],
                                                            'reference_code' => $transaction['reference_code'],
                                                            'source_type' => $transaction['source_type'],
                                                            'qty_in' => $transaction['transaction_type'] === 'IN' ? $transaction['qty_moved'] : 0,
                                                            'qty_out' => $transaction['transaction_type'] === 'OUT' ? $transaction['qty_moved'] : 0,
                                                            'remaining_qty' => null,
                                                        ];
                                                    }
                                                }
                                            }

                                            // Sort by date descending
                                            usort($combinedHistory, function($a, $b) {
                                                return strtotime($b['date']) - strtotime($a['date']);
                                            });
                                        @endphp

                                        @if (count($combinedHistory) === 0)
                                            <tr>
                                                <td colspan="8" class="text-center">Belum ada data batch untuk produk ini</td>
                                            </tr>
                                        @else
                                            @foreach ($combinedHistory as $history)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($history['date'])->format('d/m/Y') }}</td>
                                                    <td>
                                                        @if ($history['type'] === 'IN')
                                                            <span class="badge bg-label-success">IN</span>
                                                        @else
                                                            <span class="badge bg-label-danger">OUT</span>
                                                        @endif
                                                    </td>
                                                    <td><span class="badge bg-label-info">{{ $history['batch_reference'] }}</span></td>
                                                    <td>{{ $history['reference_code'] }}</td>
                                                    <td><span class="badge bg-label-primary">{{ $history['source_type'] }}</span></td>
                                                    <td class="text-end">
                                                        @if ($history['qty_in'] > 0)
                                                            <span class="text-success">+{{ number_format($history['qty_in'], 0, ',', '.') }}</span>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        @if ($history['qty_out'] > 0)
                                                            <span class="text-danger">-{{ number_format($history['qty_out'], 0, ',', '.') }}</span>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        @if ($history['remaining_qty'] !== null)
                                                            {{ number_format($history['remaining_qty'], 0, ',', '.') }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>

                            <!-- Transaction Summary -->
                            @if (isset($batchData['transaction_summary']))
                            <div class="section-header mt-6">
                                <h6>Ringkasan Transaksi</h6>
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <div class="card bg-light-success">
                                        <div class="card-body text-center">
                                            <h6 class="text-success">Total Masuk (IN)</h6>
                                            <h4 class="text-success">{{ number_format($batchData['transaction_summary']['total_in'], 0, ',', '.') }}</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light-danger">
                                        <div class="card-body text-center">
                                            <h6 class="text-danger">Total Keluar (OUT)</h6>
                                            <h4 class="text-danger">{{ number_format($batchData['transaction_summary']['total_out'], 0, ',', '.') }}</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light-primary">
                                        <div class="card-body text-center">
                                            <h6 class="text-primary">Sisa Stock</h6>
                                            <h4 class="text-primary">{{ number_format($batchData['total_quantity'], 0, ',', '.') }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Bundle Composition Tab -->
                        @if ($product->type === 'bundle')
                            <div class="tab-pane fade" id="horizontal-bundle-composition" role="tabpanel">
                                <div class="section-header mt-6">
                                    <h6>Komposisi Bundle</h6>
                                </div>
                                <div class="mx-n6">
                                    <table class="table table-bordered" id="bundle-composition-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 35%;">Nama Produk</th>
                                                <th style="width: 15%;">Qty</th>
                                                <th style="width: 15%;">Satuan</th>
                                                <th style="width: 20%;">HPP</th>
                                                <th style="width: 15%;">Total HPP</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (isset($bundleComponents) && count($bundleComponents) > 0)
                                                @foreach ($bundleComponents as $component)
                                                    <tr>
                                                        <td>
                                                            <a href="{{ route('master_data.product.detail', ['id' => $component->f_product_composition_product_id]) }}"
                                                                class="text-primary fw-semibold">
                                                                {{ $component->product_name }}
                                                            </a>
                                                        </td>
                                                        <td class="text-center">
                                                            @php
                                                                // Smart decimal formatting
                                                                if ($component->qty == floor($component->qty)) {
                                                                    echo number_format($component->qty, 0, ',', '.');
                                                                } else {
                                                                    $formatted = number_format(
                                                                        $component->qty,
                                                                        4,
                                                                        ',',
                                                                        '.',
                                                                    );
                                                                    echo rtrim(rtrim($formatted, '0'), ',');
                                                                }
                                                            @endphp
                                                        </td>
                                                        <td class="text-center">{{ $component->unit_name }}</td>
                                                        <td class="text-end">Rp {{ number_format($component->hpp ?? 0, 0, ',', '.') }}</td>
                                                        <td class="text-end">Rp {{ number_format(($component->hpp ?? 0) * $component->qty, 0, ',', '.') }}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">Tidak ada komponen
                                                        bundle</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                                <br>
                                <div class="section-header mt-6">
                                    <h6>Biaya Bundle</h6>
                                </div>
                                <div class="mx-n6">
                                    <table class="table table-bordered" id="bundle-cost-composition-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 40%;">Nomor Akun Produk</th>
                                                <th style="width: 20%;">Biaya</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (isset($productCompositionCost) && count($productCompositionCost) > 0)
                                                @foreach ($productCompositionCost as $cost)
                                                    <tr>
                                                        <td class="text-left">{{ $cost->account_code }}</td>
                                                        <td class="text-right">Rp {{ number_format($cost->cost_value, 0, ',', '.') }}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted">Tidak ada komponen
                                                        bundle</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

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
    </div>
    <!-- Revenue Split Configuration Modal -->
    <div class="modal fade" id="revenueSplitModal" tabindex="-1" aria-labelledby="revenueSplitModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="revenueSplitModalLabel">
                        <i class="icon-base ti tabler-percentage me-2"></i>Konfigurasi Revenue Split:
                        {{ $product->name }}
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
                                    <input type="text" class="form-control currency-input" id="basePrice"
                                        name="base_price" placeholder="25000" required>
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
                                    <input type="text" class="form-control currency-input" id="porsiPerusahaan"
                                        name="porsi_perusahaan" placeholder="0" required>
                                </div>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="radio" name="remaining_allocation"
                                        value="perusahaan" id="remainingPerusahaan">
                                    <label class="form-check-label text-success" for="remainingPerusahaan">
                                        <small>Dapat sisa</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Porsi Owner <span style="color: red;">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control currency-input" id="porsiOwner"
                                        name="porsi_owner" placeholder="0" required>
                                </div>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="radio" name="remaining_allocation"
                                        value="owner" id="remainingOwner">
                                    <label class="form-check-label text-success" for="remainingOwner">
                                        <small>Dapat sisa</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Porsi Pabrik <span style="color: red;">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control currency-input" id="porsiPabrik"
                                        name="porsi_pabrik" placeholder="0" required>
                                </div>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="radio" name="remaining_allocation"
                                        value="pabrik" id="remainingPabrik">
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
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
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

            $('#batch-table').DataTable({
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

            $('#saveConfigBtn').prop('disabled', true).html(
                '<i class="spinner-border spinner-border-sm me-2"></i>Menyimpan...');

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
                            .html(
                                '<i class="icon-base ti tabler-percentage me-2"></i>Revenue Split <i class="icon-base ti tabler-check ms-1"></i>'
                            );
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
                    $('#saveConfigBtn').prop('disabled', false).html(
                        '<i class="icon-base ti tabler-device-floppy me-2"></i>Simpan');
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

            $('#calculateBtn').prop('disabled', true).html(
                '<i class="spinner-border spinner-border-sm me-2"></i>Menghitung...');

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
                    $('#calculateBtn').prop('disabled', false).html(
                        '<i class="icon-base ti tabler-calculator me-2"></i>Hitung');
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
                                $('#revenue-split-btn').removeClass('btn-outline-success').addClass(
                                        'btn-outline-warning')
                                    .html('<i class="icon-base ti tabler-percentage me-2"></i>Revenue Split');
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: response.message ||
                                        'Terjadi kesalahan saat menghapus konfigurasi'
                                });
                            }
                        })
                        .fail(function(xhr) {
                            const response = xhr.responseJSON;
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: response?.message ||
                                    'Terjadi kesalahan saat menghapus konfigurasi'
                            });
                        });
                }
            });
        }
    </script>
@endsection
