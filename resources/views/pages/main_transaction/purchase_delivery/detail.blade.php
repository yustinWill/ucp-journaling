@extends('layouts.default')
@section('page_title')
    Detail Purchase Delivery - {{ $purchaseDelivery->transaction_code }}
@stop
@section('content')
    <div class="card mb-6">
        <div class="card-body">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <h4 class="mb-3">Detail Purchase Delivery</h4>
                </div>
                <div class="col-md-4">
                    {{-- @php
                        $totalActions = 1; // Edit always present
                        if($canApprovePurchaseDelivery && $purchaseDelivery['status'] === 'PENDING') {
                            $totalActions += 2; // Confirm and Cancel
                        }
                        if($canDeletePurchaseDelivery && in_array($purchaseDelivery['status'], ['PENDING', 'CANCELLED'])) {
                            $totalActions++; // Delete action
                        }
                    @endphp --}}

                    {{-- @if ($totalActions < 3)
                        <div class="d-flex gap-2 justify-content-end">
                            @if ($canEditPurchaseDelivery)
                                <a href="{{ route('main_transaction.purchase_delivery.edit', $purchaseDelivery['id']) }}"
                                    class="btn btn-primary btn-md waves-effect waves-light">
                                    <i class="icon-base ti tabler-edit me-2"></i>
                                    Edit
                                </a>
                            @endif
                            @if ($canDeletePurchaseDelivery && in_array($purchaseDelivery['status'], ['PENDING', 'CANCELLED']))
                                <button type="button" class="btn btn-danger btn-md waves-effect waves-light"
                                    id="delete-pd-btn">
                                    <i class="icon-base ti tabler-trash me-2"></i>
                                    Hapus
                                </button>
                            @endif
                        </div>
                    @else --}}
                    <!-- Show dropdown for 3 or more actions -->
                    {{-- <div class="d-flex gap-2 justify-content-end">
                        @if ($canEditPurchaseDelivery)
                                <a href="{{ route('main_transaction.purchase_delivery.edit', $purchaseDelivery['id']) }}" class="btn btn-primary btn-md waves-effect waves-light">
                                    <i class="icon-base ti tabler-edit me-2"></i>
                                    Edit
                                </a>
                            @endif
                        <div class="dropdown">
                            <button type="button" class="btn btn-primary btn-md waves-effect waves-light dropdown-toggle"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                Aksi
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @if ($canApprovePurchaseDelivery && $purchaseDelivery['status'] === 'PENDING')
                                    <li>
                                        <a class="dropdown-item" href="javascript:void(0);" id="confirm-delivery-btn">
                                            <i class="icon-base ti tabler-check me-2"></i>Konfirmasi Terima
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="javascript:void(0);" id="cancel-delivery-btn">
                                            <i class="icon-base ti tabler-x me-2"></i>Batalkan
                                        </a>
                                    </li>
                                @endif
                                @if ($canDeletePurchaseDelivery && in_array($purchaseDelivery['status'], ['PENDING', 'CANCELLED']))
                                    <li>
                                        <a class="dropdown-item" href="javascript:void(0);" id="delete-pd-btn">
                                            <i class="icon-base ti tabler-trash me-2"></i>Hapus
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div> --}}
                    {{-- @endif --}}
                </div>
            </div>

            <!-- Purchase Delivery Title -->
            <h2>{{ $purchaseDelivery->transaction_code }}</h2>
            <span>
                <h6>Data purchase delivery berdasarkan tanggal {{ $purchaseDelivery->created_at }}</h6>
            </span>
            <hr class="my-6 mx-n6" />

            <!-- Informasi Dasar Transaksi Section - Outside tabs, on top -->
            
            <!-- Row 1: Tanggal Transaksi, Nomor Transaksi -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tanggal Transaksi</label>
                    <input type="text" class="form-control" value="{{ $purchaseDelivery->delivery_date }}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nomor Transaksi</label>
                    <input type="text" class="form-control" value="{{ $purchaseDelivery->transaction_code }}" disabled />
                </div>
            </div>

            <hr class="my-6 mx-n6" />

            <!-- Tabs Section -->
            <div class="col-lg-12">
                <div class="demo-inline-spacing mt-4">
                    <div class="list-group list-group-horizontal-md text-md-center" role="tablist">
                        <a class="list-group-item list-group-item-action waves-effect active" id="purchase-delivery-info-list-item"
                            data-bs-toggle="list" href="#horizontal-purchase-delivery-info" aria-selected="true" role="tab">
                            <i class="icon-base ti tabler-info-circle me-2"></i>
                            <b>Informasi Detail</b>
                        </a>
                        <a class="list-group-item list-group-item-action waves-effect" id="audit-log-list-item"
                            data-bs-toggle="list" href="#horizontal-audit-log" aria-selected="false" role="tab">
                            <i class="icon-base ti tabler-history me-2"></i>
                            <b>Audit Log</b>
                        </a>
                    </div>
                    <div class="tab-content px-0 mt-0">
                        <!-- Information Tab -->
                        <div class="tab-pane fade show active" id="horizontal-purchase-delivery-info" role="tabpanel">
                            <div id="purchase-delivery-info-content">
                                <!-- Delivery Information Section -->
                                <div class="section-header mt-12">
                        <h6>Informasi Pengiriman</h6>
                        <hr class="mx-n6">
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Carrier</label>
                            <input type="text" class="form-control" value="{{ $purchaseDelivery->courier_name }}"
                                disabled />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tracking Number</label>
                            <input type="text" class="form-control" value="{{ $purchaseDelivery->tracking_number }}"
                                disabled />
                        </div>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Keterangan</label>
                            <textarea class="form-control" rows="3" disabled>{{ $purchaseDelivery->description }}</textarea>
                        </div>
                    </div>

                    <!-- Warehouse Information Section -->
                    <div class="section-header mt-12">
                        <h6>Informasi Gudang</h6>
                        <hr class="mx-n6">
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Gudang</label>
                            <input type="text" class="form-control" value="{{ $purchaseDelivery->warehouse }}"
                                disabled />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Diterima Oleh</label>
                            <input type="text" class="form-control" value="{{ $purchaseDelivery->created_by_name }}"
                                disabled />
                        </div>
                    </div>

                    <!-- Purchase Order Information Section -->
                    <div class="section-header mt-12">
                        <h6>Informasi Purchase Order</h6>
                        <hr class="mx-n6">
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">No. Purchase Order</label>
                            <input type="text" class="form-control"
                                value="{{ $purchaseDelivery->po_reference ?? 'N/A' }}" disabled />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Supplier</label>
                            <input type="text" class="form-control"
                                value="{{ $purchaseDelivery->supplier_name ?? ($purchaseDelivery->supplier ?? 'N/A') }}"
                                disabled />
                        </div>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Email Supplier</label>
                            <input type="text" class="form-control"
                                value="{{ $purchaseDelivery->supplier_email ?? 'N/A' }}" disabled />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status PO</label>
                            <input type="text" class="form-control"
                                value="{{ $purchaseDelivery->po_status ?? 'N/A' }}" disabled />
                        </div>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label class="form-label">Alamat Supplier</label>
                            <textarea class="form-control" rows="2" disabled>{{ $purchaseDelivery->supplier_address ?? 'N/A' }}</textarea>
                        </div>
                    </div>

                    <!-- Items Delivery Section -->
                    <div class="section-header mt-12">
                        <h6>Items Delivery</h6>
                    </div>

                    <div class="mb-4 mx-n5">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="product-detail-table">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th>Qty PO</th>
                                        <th>Previously Delivered</th>
                                        <th>This Delivery</th>
                                        <th>Remaining</th>
                                        <th>Unit Price</th>
                                        <th>Note</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($purchaseDeliveryProducts ?? [] as $product)
                                        <tr>
                                            <td>{{ $product->product_name }}</td>
                                            <td class="text-end">
                                                @php
                                                    $orderedQty = $product->ordered_qty;
                                                    if ($orderedQty == floor($orderedQty)) {
                                                        $formattedOrderedQty = number_format($orderedQty, 0, ',', '.');
                                                    } else {
                                                        $formatted = number_format($orderedQty, 4, ',', '.');
                                                        $formattedOrderedQty = rtrim(rtrim($formatted, '0'), ',');
                                                    }
                                                @endphp
                                                {{ $formattedOrderedQty }} {{ $product->unit_name }}
                                            </td>
                                            <td class="text-end">
                                                @php
                                                    $deliveredQtyBefore = $product->delivered_qty_before;
                                                    if ($deliveredQtyBefore == floor($deliveredQtyBefore)) {
                                                        $formattedDeliveredQtyBefore = number_format($deliveredQtyBefore, 0, ',', '.');
                                                    } else {
                                                        $formatted = number_format($deliveredQtyBefore, 4, ',', '.');
                                                        $formattedDeliveredQtyBefore = rtrim(rtrim($formatted, '0'), ',');
                                                    }
                                                @endphp
                                                {{ $formattedDeliveredQtyBefore }} {{ $product->unit_name }}
                                            </td>
                                            <td class="text-end">
                                                @php
                                                    $qty = $product->qty;
                                                    if ($qty == floor($qty)) {
                                                        $formattedQty = number_format($qty, 0, ',', '.');
                                                    } else {
                                                        $formatted = number_format($qty, 4, ',', '.');
                                                        $formattedQty = rtrim(rtrim($formatted, '0'), ',');
                                                    }
                                                @endphp
                                                {{ $formattedQty }} {{ $product->unit_name }}
                                            </td>
                                            <td class="text-end">
                                                @php
                                                    $remainingQtyAfter = $product->remaining_qty_after;
                                                    if ($remainingQtyAfter == floor($remainingQtyAfter)) {
                                                        $formattedRemainingQtyAfter = number_format($remainingQtyAfter, 0, ',', '.');
                                                    } else {
                                                        $formatted = number_format($remainingQtyAfter, 4, ',', '.');
                                                        $formattedRemainingQtyAfter = rtrim(rtrim($formatted, '0'), ',');
                                                    }
                                                @endphp
                                                {{ $formattedRemainingQtyAfter }} {{ $product->unit_name }}
                                            </td>
                                            <td class="text-end">
                                                @php
                                                    $pricePerQty = $product->price_per_qty;
                                                    if ($pricePerQty == floor($pricePerQty)) {
                                                        $formattedPricePerQty = number_format($pricePerQty, 0, ',', '.');
                                                    } else {
                                                        $formatted = number_format($pricePerQty, 4, ',', '.');
                                                        $formattedPricePerQty = rtrim(rtrim($formatted, '0'), ',');
                                                    }
                                                @endphp
                                                {{ $formattedPricePerQty }}
                                            </td>
                                            <td>
                                                {{ $product->product_delivery_note ?? '-' }}
                                            </td>
                                            <td class="text-center">
                                                @php
                                                    $remaining = $product->remaining_qty_after;
                                                    if ($remaining > 0) {
                                                        echo '<span class="badge bg-label-warning">Partial</span>';
                                                    } else {
                                                        echo '<span class="badge bg-label-success">Complete</span>';
                                                    }
                                                @endphp
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">Tidak ada produk delivery
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="section-header mt-12">
                        <h6>Informasi Tambahan & Ringkasan</h6>
                        <hr class="mx-n6">
                    </div>

                    <!-- Split Layout: Tag, Memo, Attachment & Financial Summary -->
                    <div class="row">
                        <!-- Left Side: Tag, Memo and Attachment -->
                        <div class="col-md-6 mb-4">
                            <div class="mb-3">
                                <label class="form-label">Tag</label>
                                @if (isset($purchaseDelivery->tags))
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach ($purchaseDelivery->tags as $tag)
                                            <span class="badge {{ $tag->preview }}">{{ $tag->code }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-muted">Tidak ada tag</div>
                                @endif
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Lampiran</label>
                                @if (isset($purchaseDelivery->attachment) && $purchaseDelivery->attachment)
                                    <div class="input-group">
                                        <input type="text" class="form-control"
                                            value="{{ $purchaseDelivery->attachment }}" disabled />
                                        <a href="{{ Storage::url($purchaseDelivery->attachment) }}" target="_blank"
                                            class="btn btn-outline-primary">
                                            <i class="icon-base ti tabler-download"></i>
                                        </a>
                                    </div>
                                @else
                                    <input type="text" class="form-control" value="Tidak ada lampiran" disabled />
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="mb-3">
                                <label class="form-label">Memo (Internal)</label>
                                <textarea class="form-control" rows="4" disabled>{{ $purchaseDelivery->memo ?? '-' }}</textarea>
                            </div>
                        </div>
                    </div>
                    <!-- Timestamps Section -->
                    <div class="section-header mt-12">
                        <h6>Informasi Sistem</h6>
                        <hr class="mx-n6">
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Dibuat Pada</label>
                            <input type="text" class="form-control" value="{{ $purchaseDelivery->created_at }}"
                                disabled />
                        </div>
                        {{-- <div class="col-md-6">
                            <label class="form-label">Diupdate Pada</label>
                            <input type="text" class="form-control" value="{{ $purchaseDelivery->updated_at }}"
                                disabled />
                        </div> --}}
                    </div>
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
                                        </tr>
                                    </thead>
                            <tbody>
                                @foreach ($auditLogs as $log)
                                    <tr>
                                        <td>{{ $log->created_at }}</td>
                                        <td>{{ $log->user_name }}</td>
                                        <td>
                                            @if($log->action_type === 'CREATE')
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
@stop

@section('content')
    @include('pages.main_transaction.shared._compact_detail_styles')

    @php
        $formatCurrency = fn($value) => 'Rp ' . number_format((float) ($value ?? 0), 0, ',', '.');
        $formatQty = function ($value) {
            $value = (float) ($value ?? 0);
            if ($value == floor($value)) {
                return number_format($value, 0, ',', '.');
            }

            return rtrim(rtrim(number_format($value, 4, ',', '.'), '0'), ',');
        };
        $formatDate = function ($value, $withTime = false) {
            if (empty($value)) {
                return '-';
            }

            try {
                return \Carbon\Carbon::parse($value)->format($withTime ? 'd M Y H:i' : 'd M Y');
            } catch (\Throwable $e) {
                return $value;
            }
        };
        $humanStatus = fn($value) => $value ? ucwords(strtolower(str_replace('_', ' ', $value))) : '-';
        $deliveryStatus = $purchaseDelivery->delivery_status ?? $purchaseDelivery->status ?? null;
        $statusMeta = match ($deliveryStatus) {
            'COMPLETED', 'DELIVERED' => ['label' => 'Diterima', 'class' => 'success'],
            'PARTIAL', 'IN_TRANSIT' => ['label' => 'Sebagian Diterima', 'class' => 'warning'],
            'PENDING' => ['label' => 'Menunggu', 'class' => 'warning'],
            'CANCELLED' => ['label' => 'Dibatalkan', 'class' => 'danger'],
            default => ['label' => $humanStatus($deliveryStatus), 'class' => 'secondary'],
        };
        $validTags = collect($purchaseDelivery->tags ?? [])->filter(fn($tag) => is_object($tag) && !empty($tag->code));
        $totalQty = collect($purchaseDeliveryProducts ?? [])->sum(fn($product) => (float) ($product->qty ?? 0));
        $totalValue = collect($purchaseDeliveryProducts ?? [])->sum(fn($product) => (float) ($product->qty ?? 0) * (float) ($product->price_per_qty ?? 0));
    @endphp

    <div class="compact-detail">
        <div class="detail-card mb-4">
            <div class="detail-header d-flex flex-wrap align-items-start justify-content-between gap-3">
                <div>
                    <div class="detail-breadcrumb">
                        <a href="{{ route('main_transaction.purchase_order.index') }}">Pembelian</a>
                        <i class="icon-base ti tabler-chevron-right"></i>
                        <span>Pengiriman</span>
                        <i class="icon-base ti tabler-chevron-right"></i>
                        <span>Detail</span>
                    </div>
                    <h1 class="detail-title">Detail Pengiriman Pembelian</h1>
                </div>
                <div class="detail-actions d-flex flex-wrap justify-content-end gap-2">
                    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="window.print()">
                        <i class="icon-base ti tabler-printer me-2"></i>Print
                    </button>
                </div>
            </div>

            <div class="summary-grid">
                <div class="summary-item">
                    <span class="summary-label">Pengiriman Pembelian</span>
                    <div class="summary-code">{{ $purchaseDelivery->transaction_code }}</div>
                    <div class="summary-note">
                        Dibuat pada {{ $formatDate($purchaseDelivery->created_at ?? null, true) }}
                        @if (!empty($purchaseDelivery->created_by_name))
                            oleh {{ $purchaseDelivery->created_by_name }}
                        @endif
                    </div>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Status Pengiriman</span>
                    <span class="badge bg-label-{{ $statusMeta['class'] }} fs-6 py-2 px-3">
                        <i class="icon-base ti tabler-check me-1"></i>{{ $statusMeta['label'] }}
                    </span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Pesanan Pembelian</span>
                    <div class="summary-value">
                        @if(!empty($purchaseDelivery->po_id))
                            <a href="{{ route('main_transaction.purchase_order.detail', $purchaseDelivery->po_id) }}">
                                {{ $purchaseDelivery->po_reference ?? '-' }}
                            </a>
                        @else
                            {{ $purchaseDelivery->po_reference ?? '-' }}
                        @endif
                    </div>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Qty Diterima</span>
                    <div class="summary-money">{{ $formatQty($totalQty) }}</div>
                </div>
            </div>
        </div>

        <div class="detail-card">
            <div class="detail-section">
                <h2 class="detail-section-title">Informasi Transaksi</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <i class="info-icon icon-base ti tabler-building-store"></i>
                        <div>
                            <span class="info-label">Supplier</span>
                            <div class="info-value">{{ $purchaseDelivery->supplier_name ?? $purchaseDelivery->supplier ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="info-icon icon-base ti tabler-map-pin"></i>
                        <div>
                            <span class="info-label">Alamat Supplier</span>
                            <div class="info-value">{{ $purchaseDelivery->supplier_address ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="info-icon icon-base ti tabler-calendar"></i>
                        <div>
                            <span class="info-label">Tanggal Pengiriman</span>
                            <div class="info-value">{{ $formatDate($purchaseDelivery->delivery_date ?? null) }}</div>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="info-icon icon-base ti tabler-file-text"></i>
                        <div>
                            <span class="info-label">No. Transaksi</span>
                            <div class="info-value">{{ $purchaseDelivery->transaction_code ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="info-icon icon-base ti tabler-file-symlink"></i>
                        <div>
                            <span class="info-label">No. Pesanan</span>
                            <div class="info-value">{{ $purchaseDelivery->po_reference ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="info-icon icon-base ti tabler-home"></i>
                        <div>
                            <span class="info-label">Gudang</span>
                            <div class="info-value">
                                {{ trim(($purchaseDelivery->warehouse_code ?? '') . ' ' . ($purchaseDelivery->warehouse ?? '')) ?: '-' }}
                            </div>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="info-icon icon-base ti tabler-truck-delivery"></i>
                        <div>
                            <span class="info-label">Kurir / Ekspedisi</span>
                            <div class="info-value">{{ $purchaseDelivery->courier_name ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="info-icon icon-base ti tabler-barcode"></i>
                        <div>
                            <span class="info-label">Tracking Number</span>
                            <div class="info-value">{{ $purchaseDelivery->tracking_number ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="info-icon icon-base ti tabler-tag"></i>
                        <div>
                            <span class="info-label">Tag</span>
                            <div class="info-value">
                                @forelse($validTags as $tag)
                                    <span class="badge {{ $tag->preview }} me-1">{{ $tag->code }}</span>
                                @empty
                                    -
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="info-icon icon-base ti tabler-user-check"></i>
                        <div>
                            <span class="info-label">Dibuat Oleh</span>
                            <div class="info-value">{{ $purchaseDelivery->created_by_name ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="detail-card">
            <div class="detail-section">
                <h2 class="detail-section-title">Informasi Produk</h2>
                <div class="compact-table table-responsive">
                    <table class="table" id="product-detail-table">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th class="text-end">Qty Pesanan</th>
                                <th class="text-end">Sudah Diterima</th>
                                <th class="text-end">Diterima</th>
                                <th class="text-end">Sisa</th>
                                <th>Satuan</th>
                                <th class="text-end">Harga Satuan</th>
                                <th>Catatan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($purchaseDeliveryProducts ?? [] as $product)
                                @php
                                    $remainingQty = (float) ($product->remaining_qty_after ?? 0);
                                @endphp
                                <tr>
                                    <td class="fw-semibold">{{ $product->product_name ?? '-' }}</td>
                                    <td class="text-end">{{ $formatQty($product->ordered_qty ?? 0) }}</td>
                                    <td class="text-end">{{ $formatQty($product->delivered_qty_before ?? 0) }}</td>
                                    <td class="text-end">{{ $formatQty($product->qty ?? 0) }}</td>
                                    <td class="text-end">{{ $formatQty($remainingQty) }}</td>
                                    <td>{{ $product->unit_name ?? $product->unit_code ?? '-' }}</td>
                                    <td class="text-end">{{ $formatCurrency($product->price_per_qty ?? 0) }}</td>
                                    <td>{{ $product->product_delivery_note ?? '-' }}</td>
                                    <td>
                                        @if($remainingQty > 0)
                                            <span class="badge bg-label-warning">Sebagian</span>
                                        @else
                                            <span class="badge bg-label-success">Selesai</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="empty-row">Tidak ada produk pengiriman</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="totals-panel">
                    <div class="total-line">
                        <span>Total Qty Diterima</span>
                        <strong>{{ $formatQty($totalQty) }}</strong>
                    </div>
                    <div class="total-line total-highlight">
                        <strong>Estimasi Nilai</strong>
                        <strong>{{ $formatCurrency($totalValue) }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="detail-card">
            <div class="detail-section">
                <h2 class="detail-section-title">Catatan</h2>
                <div class="row g-4">
                    <div class="col-md-6">
                        <span class="info-label">Keterangan</span>
                        <div class="info-value">{{ $purchaseDelivery->description ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <span class="info-label">Memo Internal</span>
                        <div class="info-value">{{ $purchaseDelivery->internal_memo ?? $purchaseDelivery->memo ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <span class="info-label">Lampiran</span>
                        @if(!empty($purchaseDelivery->attachment))
                            <a href="{{ $purchaseDelivery->attachment }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="icon-base ti tabler-download me-1"></i>Download
                            </a>
                        @else
                            <div class="info-value">-</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="detail-card">
            <div class="detail-section">
                <ul class="nav nav-tabs compact-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#purchase-delivery-audit-history" type="button" role="tab">
                            Log Audit
                        </button>
                    </li>
                </ul>

                <div class="tab-content pt-4 px-0">
                    <div class="tab-pane fade show active" id="purchase-delivery-audit-history" role="tabpanel">
                        <div class="compact-table table-responsive">
                            <table class="table" id="audit-log-table">
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
                                            <td>{{ $formatDate($log->created_at ?? null, true) }}</td>
                                            <td>{{ $log->user_name ?? '-' }}</td>
                                            <td><span class="badge bg-label-info">{{ $log->action_type ?? '-' }}</span></td>
                                            <td>{{ $log->description ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="empty-row">Belum ada riwayat perubahan</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
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
@overwrite

@section('js_script')
    <script>
        $(function() {
            $('#audit-log-table').DataTable({
                orderCellsTop: true,
                order: [[0, 'desc']], // Sort by date descending
                searching: false,
                paging: false,
                info: false
            });

            // Action button handlers
            $('#confirm-delivery-btn').on('click', function() {
                Swal.fire({
                    title: 'Konfirmasi Penerimaan?',
                    text: 'Purchase Delivery akan dikonfirmasi sebagai diterima.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Konfirmasi',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-primary',
                        cancelButton: 'btn btn-outline-secondary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Handle confirmation logic here
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Purchase Delivery berhasil dikonfirmasi.',
                            icon: 'success',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        });
                    }
                });
            });

            $('#cancel-delivery-btn').on('click', function() {
                Swal.fire({
                    title: 'Batalkan Purchase Delivery?',
                    text: 'Purchase Delivery akan dibatalkan dan tidak dapat diproses.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Batalkan',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-danger',
                        cancelButton: 'btn btn-outline-secondary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Handle cancellation logic here
                        Swal.fire({
                            title: 'Purchase Delivery Dibatalkan',
                            text: 'Purchase Delivery berhasil dibatalkan.',
                            icon: 'success',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        });
                    }
                });
            });

            $('#delete-pd-btn').on('click', function() {
                Swal.fire({
                    title: 'Hapus Purchase Delivery?',
                    text: 'Data yang dihapus tidak dapat dikembalikan!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-danger',
                        cancelButton: 'btn btn-outline-secondary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Handle delete logic here
                        Swal.fire({
                            title: 'Dihapus!',
                            text: 'Purchase Delivery berhasil dihapus.',
                            icon: 'success',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        }).then(() => {
                            window.location.href =
                                '{{ route('main_transaction.purchase_delivery.index') }}';
                        });
                    }
                });
            });
        });
    </script>
@endsection
