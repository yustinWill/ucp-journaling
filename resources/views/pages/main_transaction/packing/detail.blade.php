@extends('layouts.default')
@section('page_title')
    Detail Packing Produk
@stop
@section('content')
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">
                <i class="icon-base ti tabler-package me-2"></i>Detail Packing Produk
            </h4>
            <p class="text-muted mb-0">{{ $production->production_number }}</p>
        </div>
        <div>
            <a href="{{ route('main_transaction.packing.index') }}" class="btn btn-label-secondary">
                <i class="icon-base ti tabler-arrow-left me-1"></i> Kembali ke List
            </a>
        </div>
    </div>

    <div class="card mb-6">
        <h4 class="card-header">Detail Packing Produk</h4>
        <div class="card-body">

            <!-- Informasi Dasar Transaksi Section -->
            <div class="section-header mt-6">
                <h6>Informasi Dasar Transaksi</h6>
                <hr class="mx-n6">
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tanggal Transaksi</label>
                    <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($production->production_date)->format('d F Y') }}" readonly />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nomor Transaksi</label>
                    <input type="text" class="form-control" value="{{ $production->production_number }}" readonly />
                </div>
            </div>

            <!-- Informasi Produksi Section -->
            <div class="section-header mt-12">
                <h6>Informasi Produksi</h6>
                <hr class="mx-n6">
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Bundle Product (Sumber)</label>
                    <input type="text" class="form-control"
                        value="{{ $sourceBundleProduct ? $sourceBundleProduct->code . ' - ' . $sourceBundleProduct->name : '-' }}"
                        readonly />
                    <small class="form-text text-muted">Product bundle yang dikonsumsi untuk packing</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Gudang</label>
                    <input type="text" class="form-control"
                        value="{{ $production->warehouse_code }} - {{ $production->warehouse_name }}"
                        readonly />
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Penanggung Jawab</label>
                    <input type="text" class="form-control" value="{{ $production->pic_name }}" readonly />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <div class="pt-2">
                        @if($production->status === 'COMPLETED')
                            <span class="badge bg-label-success" style="font-size: 14px; padding: 6px 12px;">
                                <i class="icon-base ti tabler-check me-1"></i> COMPLETED
                            </span>
                        @elseif($production->status === 'IN_PROGRESS')
                            <span class="badge bg-label-warning" style="font-size: 14px; padding: 6px 12px;">
                                <i class="icon-base ti tabler-clock me-1"></i> IN PROGRESS
                            </span>
                        @elseif($production->status === 'PLANNED')
                            <span class="badge bg-label-info" style="font-size: 14px; padding: 6px 12px;">
                                <i class="icon-base ti tabler-calendar me-1"></i> PLANNED
                            </span>
                        @else
                            <span class="badge bg-label-secondary" style="font-size: 14px; padding: 6px 12px;">
                                <i class="icon-base ti tabler-file me-1"></i> {{ $production->status }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Packaging Products Section -->
            <div class="section-header mt-12">
                <h6>Produk Packaging</h6>
                <hr class="mx-n6">
            </div>

            @foreach($packagingProducts as $index => $packaging)
                <div class="packaging-product-section mb-6">
                    <!-- Packaging Product Header -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <span class="badge bg-label-primary me-2">#{{ $index + 1 }}</span>
                                {{ $packaging->product_code }} - {{ $packaging->product_name }}
                            </h6>
                            <span class="badge bg-label-info">{{ $packaging->unit_code }}</span>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Production Number</label>
                                    <input type="text" class="form-control"
                                        value="{{ $packaging->production_number }}"
                                        readonly />
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Qty Produced</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control text-end"
                                            value="{{ number_format($packaging->quantity_packed, 0, ',', '.') }}"
                                            readonly />
                                        <span class="input-group-text">{{ $packaging->unit_code }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">HPP per Unit</label>
                                    <input type="text" class="form-control text-end"
                                        value="Rp {{ number_format($packaging->hpp_packing ?? 0, 0, ',', '.') }}"
                                        readonly />
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Total HPP</label>
                                    <input type="text" class="form-control text-end"
                                        value="Rp {{ number_format(($packaging->hpp_packing ?? 0) * $packaging->quantity_packed, 0, ',', '.') }}"
                                        readonly />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Components Table -->
                    @if($packaging->items && $packaging->items->count() > 0)
                        <div class="section-header">
                            <h6 class="text-muted">Materials Consumed</h6>
                            <hr class="mx-n6">
                        </div>
                        <div class="mx-n5 mb-4">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 5%;">#</th>
                                        <th style="width: 30%;">Component Product</th>
                                        <th style="width: 10%;">Unit</th>
                                        <th style="width: 15%;">Qty Required</th>
                                        <th style="width: 15%;">Qty Consumed</th>
                                        <th style="width: 25%;">Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($packaging->items as $itemIndex => $item)
                                        <tr>
                                            <td class="text-center">{{ $itemIndex + 1 }}</td>
                                            <td>{{ $item->product_code }} - {{ $item->product_name }}</td>
                                            <td class="text-center">{{ $item->unit_code }}</td>
                                            <td class="text-end">
                                                @php
                                                    $qtyRequired = $item->quantity_required;
                                                    if ($qtyRequired == floor($qtyRequired)) {
                                                        echo number_format($qtyRequired, 0, ',', '.');
                                                    } else {
                                                        $formatted = number_format($qtyRequired, 4, ',', '.');
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
                        <p class="text-muted fst-italic">No materials consumed data</p>
                    @endif
                </div>

                @if($index < $packagingProducts->count() - 1)
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
                    <label class="form-label">Memo <small class="text-muted">(Internal)</small></label>
                    <textarea class="form-control" rows="3" readonly>{{ $production->notes ?? '-' }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Deskripsi</label>
                    <textarea class="form-control" rows="3" readonly>{{ $production->description ?? '-' }}</textarea>
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
                        <input type="text" class="form-control" value="No attachment" readonly />
                    @endif
                </div>
                <div class="col-md-6">
                    <label class="form-label">Created At</label>
                    <input type="text" class="form-control"
                        value="{{ \Carbon\Carbon::parse($production->created_at)->format('d F Y H:i') }}"
                        readonly />
                </div>
            </div>

            <!-- Summary Section -->
            <div class="section-header mt-12">
                <h6>Summary</h6>
                <hr class="mx-n6">
            </div>

            <div class="row g-3">
                <div class="col-md-3">
                    <div class="card bg-label-primary h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <i class="icon-base ti tabler-package icon-24px me-2"></i>
                                <h6 class="mb-0">Total Packaging Products</h6>
                            </div>
                            <h3 class="mb-0">{{ $packagingProducts->count() }}</h3>
                            <small class="text-muted">Items produced</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-label-success h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <i class="icon-base ti tabler-box icon-24px me-2"></i>
                                <h6 class="mb-0">Total Quantity</h6>
                            </div>
                            <h3 class="mb-0">{{ number_format($packagingProducts->sum('quantity_packed'), 0, ',', '.') }}</h3>
                            <small class="text-muted">Units packed</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-label-warning h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <i class="icon-base ti tabler-calculator icon-24px me-2"></i>
                                <h6 class="mb-0">Average HPP</h6>
                            </div>
                            <h3 class="mb-0">Rp {{ number_format($packagingProducts->avg('hpp_packing') ?? 0, 0, ',', '.') }}</h3>
                            <small class="text-muted">Per unit</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-label-info h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <i class="icon-base ti tabler-currency-dollar icon-24px me-2"></i>
                                <h6 class="mb-0">Total Production Cost</h6>
                            </div>
                            <h3 class="mb-0">Rp {{ number_format($packagingProducts->sum(function($p) { return ($p->hpp_packing ?? 0) * $p->quantity_packed; }), 0, ',', '.') }}</h3>
                            <small class="text-muted">All items</small>
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
            // No interactive functionality needed for detail page
            console.log('Packing detail page loaded');
        });
    </script>
@endsection
