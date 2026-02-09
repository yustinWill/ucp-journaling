@extends('layouts.default')
@section('page_title')
    Detail Packing Produk
@stop
@section('content')
    <div class="card mb-6">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Detail Packing Produk</h4>
            <a href="{{ route('main_transaction.packing.index') }}" class="btn btn-label-secondary">
                <i class="icon-base ti tabler-arrow-left me-1"></i> Kembali ke List
            </a>
        </div>
        <div class="card-body">

            <!-- Informasi Dasar Transaksi Section -->
            <div class="section-header mt-12">
                <h6>Informasi Dasar Transaksi</h6>
                <hr class="mx-n6">
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tanggal Transaksi</label>
                    <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($production->production_date)->format('d F Y') }}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nomor Transaksi</label>
                    <input type="text" class="form-control" value="{{ $transactionNumber ?? $production->production_number }}" disabled />
                </div>
            </div>

            <!-- Informasi Produksi Section -->
            <div class="section-header mt-12">
                <h6>Informasi Produksi</h6>
                <hr class="mx-n6">
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Bundle Product</label>
                    <input type="text" class="form-control"
                        value="{{ $sourceBundleProduct ? $sourceBundleProduct->code . ' - ' . $sourceBundleProduct->name : '-' }}"
                        disabled />
                    <small class="form-text text-muted">Product bundle yang dikonsumsi untuk packing</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Gudang</label>
                    <input type="text" class="form-control"
                        value="{{ $production->warehouse_code }} - {{ $production->warehouse_name }}"
                        disabled />
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Penanggung Jawab</label>
                    <input type="text" class="form-control" value="{{ $production->pic_name }}" disabled />
                </div>
            </div>

            <!-- Production Code Section -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Production Code</label>
                    <input type="text" class="form-control"
                        value="{{ $sourceProduction ? $sourceProduction->production_number : '-' }}"
                        disabled />
                    <small class="form-text text-muted">Production yang di-packing</small>
                </div>
            </div>

            <!-- Available Bundle Stock Section -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Available Bundle Stock</label>
                    <div class="input-group">
                        <input type="text" class="form-control"
                            value="{{ $sourceProduction ? number_format($sourceProduction->quantity_produced, 0, ',', '.') : '0' }}"
                            disabled />
                        <span class="input-group-text">{{ $sourceBundleProduct->unit_code ?? '-' }}</span>
                    </div>
                    <small class="form-text text-muted">Qty dari production yang dipilih</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Remaining Bundle Stock</label>
                    <div class="input-group">
                        @php
                            $availableQty = $sourceProduction ? $sourceProduction->quantity_produced : 0;
                            $remainingBundle = $availableQty - $totalBundleConsumed;
                        @endphp
                        <input type="text" class="form-control {{ $remainingBundle < 0 ? 'is-invalid' : '' }}"
                            value="{{ number_format($remainingBundle, 0, ',', '.') }}"
                            disabled />
                        <span class="input-group-text">{{ $sourceBundleProduct->unit_code ?? '-' }}</span>
                    </div>
                    <small class="form-text text-muted">Sisa stock setelah packing</small>
                </div>
            </div>

            <!-- Packaging Products Section -->
            <div class="section-header mt-12">
                <h6>Produk Packaging</h6>
                <hr class="mx-n6">
            </div>

            @foreach($packagingProducts as $index => $packaging)
                @php
                    // Separate bundle item from other component items
                    $bundleItem = $packaging->items->first(function ($item) use ($sourceBundleProduct) {
                        return $sourceBundleProduct && $item->raw_material_product_id == $sourceBundleProduct->id;
                    });
                    $otherComponents = $packaging->items->filter(function ($item) use ($sourceBundleProduct) {
                        return !$sourceBundleProduct || $item->raw_material_product_id != $sourceBundleProduct->id;
                    });
                    $bundleQtyConsumed = $bundleItem ? $bundleItem->quantity_consumed : 0;
                    $bundleQtyPerUnit = $packaging->quantity_packed > 0 ? $bundleQtyConsumed / $packaging->quantity_packed : 0;
                @endphp
                <div class="packaging-product-section mb-6">
                    <!-- Packaging Product Header -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">{{ $packaging->product_code }} - {{ $packaging->product_name }}</h6>
                            <span class="badge bg-label-info">{{ $packaging->unit_code }}</span>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Qty to Produce</label>
                                    <input type="text" class="form-control"
                                        value="{{ number_format($packaging->quantity_packed, 0, ',', '.') }}"
                                        disabled />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Bundle Qty Needed</label>
                                    <input type="text" class="form-control text-end"
                                        value="{{ number_format($bundleQtyConsumed, 0, ',', '.') }}"
                                        disabled />
                                    <small class="form-text text-muted">Qty per unit:
                                        @if($bundleQtyPerUnit == floor($bundleQtyPerUnit))
                                            {{ number_format($bundleQtyPerUnit, 0, ',', '.') }}
                                        @else
                                            {{ rtrim(rtrim(number_format($bundleQtyPerUnit, 4, ',', '.'), '0'), ',') }}
                                        @endif
                                    </small>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">HPP per Unit</label>
                                    <input type="text" class="form-control text-end"
                                        value="Rp {{ number_format($packaging->hpp_packing ?? 0, 0, ',', '.') }}"
                                        disabled />
                                    <small class="form-text text-muted">Total: Rp {{ number_format(($packaging->hpp_packing ?? 0) * $packaging->quantity_packed, 0, ',', '.') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Other Components Table -->
                    @if($otherComponents->count() > 0)
                        <div class="section-header">
                            <h6 class="text-muted">Other Components</h6>
                            <hr class="mx-n6">
                        </div>
                        <div class="mx-n5 mb-4">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 25%;">Component Product</th>
                                        <th style="width: 10%;">Unit</th>
                                        <th style="width: 15%;">Qty per Unit</th>
                                        <th style="width: 15%;">Total Needed</th>
                                        <th style="width: 35%;">Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($otherComponents as $item)
                                        <tr>
                                            <td>{{ $item->product_code }} - {{ $item->product_name }}</td>
                                            <td class="text-center">{{ $item->unit_code }}</td>
                                            <td class="text-end">
                                                @php
                                                    $qtyPerUnit = $packaging->quantity_packed > 0 ? $item->quantity_consumed / $packaging->quantity_packed : 0;
                                                    if ($qtyPerUnit == floor($qtyPerUnit)) {
                                                        echo number_format($qtyPerUnit, 0, ',', '.');
                                                    } else {
                                                        $formatted = number_format($qtyPerUnit, 4, ',', '.');
                                                        echo rtrim(rtrim($formatted, '0'), ',');
                                                    }
                                                @endphp
                                            </td>
                                            <td class="text-end">
                                                @php
                                                    $qtyConsumed = $item->quantity_consumed;
                                                    if ($qtyConsumed == floor($qtyConsumed)) {
                                                        echo number_format($qtyConsumed, 0, ',', '.');
                                                    } else {
                                                        $formatted = number_format($qtyConsumed, 4, ',', '.');
                                                        echo rtrim(rtrim($formatted, '0'), ',');
                                                    }
                                                @endphp
                                            </td>
                                            <td class="text-muted small">{{ $item->notes ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted fst-italic">No other components required</p>
                    @endif
                </div>

                @if(!$loop->last)
                    <hr class="my-6" style="border-top: 2px solid #e0e0e0;">
                @endif
            @endforeach

            <!-- Informasi Tambahan Section -->
            <div class="section-header mt-12">
                <h6>Informasi Tambahan</h6>
                <hr class="mx-n6">
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Sisa Bundle Product</label>
                    <div class="input-group">
                        @php
                            $sisaValue = 0;
                            if ($stockAdjustment && $stockAdjustment->notes) {
                                // Parse sisa value from notes format: "Packing adjustment [sisa:5] - reason"
                                if (preg_match('/\[sisa:([-\d.]+)\]/', $stockAdjustment->notes, $matches)) {
                                    $sisaValue = floatval($matches[1]);
                                }
                            }
                        @endphp
                        <input type="text" class="form-control"
                            value="{{ number_format($sisaValue, 2, ',', '.') }}"
                            disabled />
                        <span class="input-group-text">{{ $sourceBundleProduct->unit_code ?? '-' }}</span>
                    </div>
                    <small class="form-text text-muted">
                        Sisa dari production qty setelah packing selesai.
                    </small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Alasan Penyesuaian Stock</label>
                    <textarea class="form-control" rows="2" disabled>{{ $stockAdjustment ? preg_replace('/^Packing adjustment \[sisa:[-\d.]+\] - /', '', $stockAdjustment->notes) : '-' }}</textarea>
                    <small class="form-text text-muted">Alasan penyesuaian stock</small>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Memo <small class="text-muted">(Internal)</small></label>
                    <textarea class="form-control" rows="3" disabled>{{ $production->notes ?? '-' }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Deskripsi</label>
                    <textarea class="form-control" rows="3" disabled>{{ $production->description ?? '-' }}</textarea>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Lampiran Dokumen</label>
                    @if($production->attachment)
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ asset('storage/' . $production->attachment) }}"
                                target="_blank"
                                class="btn btn-sm btn-outline-primary">
                                <i class="icon-base ti tabler-download me-1"></i> Download
                            </a>
                            <span class="text-muted">{{ basename($production->attachment) }}</span>
                        </div>
                    @else
                        <input type="text" class="form-control" value="No attachment" disabled />
                    @endif
                </div>
                <div class="col-md-6">
                    <label class="form-label">Created At</label>
                    <input type="text" class="form-control"
                        value="{{ \Carbon\Carbon::parse($production->created_at)->format('d F Y H:i') }}"
                        disabled />
                </div>
            </div>

            <!-- Stock Adjustment Detail (if exists) -->
            @if($stockAdjustment)
            <div class="section-header mt-12">
                <h6>
                    <i class="icon-base ti tabler-adjustments me-2"></i>Stock Adjustment
                    <span class="badge {{ $stockAdjustment->movement_type === 'ADJUSTMENT_IN' ? 'bg-label-success' : 'bg-label-warning' }} ms-2">
                        {{ $stockAdjustment->movement_type === 'ADJUSTMENT_IN' ? 'ADJUSTMENT IN' : 'ADJUSTMENT OUT' }}
                    </span>
                </h6>
                <hr class="mx-n6">
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label">Adjustment Number</label>
                    <input type="text" class="form-control" value="{{ $stockAdjustment->reference_number }}" disabled />
                </div>
                <div class="col-md-3">
                    <label class="form-label">Adjustment Date</label>
                    <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($stockAdjustment->movement_date)->format('d F Y') }}" disabled />
                </div>
                <div class="col-md-3">
                    <label class="form-label">Qty Adjusted</label>
                    <div class="input-group">
                        <input type="text" class="form-control text-end text-{{ $stockAdjustment->movement_type === 'ADJUSTMENT_IN' ? 'success' : 'warning' }}"
                            value="{{ $stockAdjustment->movement_type === 'ADJUSTMENT_IN' ? '+' : '-' }}{{ number_format($stockAdjustment->movement_qty, 2, ',', '.') }}"
                            disabled />
                        <span class="input-group-text">{{ $sourceBundleProduct->unit_code ?? '' }}</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Stock After Adjustment</label>
                    <input type="text" class="form-control text-end"
                        value="{{ number_format($stockAdjustment->stock_after, 0, ',', '.') }}"
                        disabled />
                </div>
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="pt-6 text-end">
                <a href="{{ route('main_transaction.packing.index') }}" class="btn btn-label-secondary">Kembali</a>
            </div>
        </div>
    </div>
@stop

@section('js_script')
    <script>
        $(function() {
            // Detail page - no interactive functionality needed
        });
    </script>
@endsection
