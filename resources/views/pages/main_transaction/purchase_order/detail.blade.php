@extends('layouts.default')
@section('page_title')
    Detail Purchase Order - {{ $purchaseOrder['reference_no'] }}
@stop
@section('content')
    <div class="card mb-6">
        <div class="card-body">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <h4 class="mb-3">Detail Purchase Order</h4>
                </div>
                <div class="col-md-4">
                    {{-- @php
                        $totalActions = 1; // Print always present
                        if($canEditPurchaseOrder) {
                            $totalActions++;
                        }
                        if($canApprovePurchaseOrder && $purchaseOrder['status'] === 'PENDING_APPROVAL') {
                            $totalActions += 2; // Approve and Reject
                        }
                        if($canDeletePurchaseOrder && in_array($purchaseOrder['status'], ['DRAFT', 'CANCELLED'])) {
                            $totalActions++; // Delete action
                        }
                    @endphp --}}

                    {{-- @if($totalActions < 3) --}}
                        <!-- Show buttons directly when less than 3 actions -->
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button" class="btn btn-primary btn-md waves-effect waves-light" onclick="window.print()">
                                <i class="icon-base ti tabler-printer me-2"></i>Print
                            </button>
                            @if($canCreatePurchaseDelivery && !in_array($purchaseOrder['status'], ['DRAFT', 'CANCELLED']))
                                <a href="{{ route('main_transaction.purchase_delivery.add', ['po_id' => $purchaseOrder['id']]) }}" class="btn btn-warning btn-md waves-effect waves-light">
                                    <i class="icon-base ti tabler-truck-delivery me-2"></i>Buat Delivery
                                </a>
                            @endif
                            @if($canCreatePurchaseInvoice && !in_array($purchaseOrder['status'], ['DRAFT', 'CANCELLED']))
                                <a href="{{ route('main_transaction.purchase_invoice.add', ['po_id' => $purchaseOrder['id']]) }}" class="btn btn-info btn-md waves-effect waves-light">
                                    <i class="icon-base ti tabler-file-invoice me-2"></i>Buat Invoice
                                </a>
                            @endif
                            @if($canEditPurchaseOrder)
                                <a href="{{ route('main_transaction.purchase_order.edit', $purchaseOrder['id']) }}" class="btn btn-success btn-md waves-effect waves-light">
                                    <i class="icon-base ti tabler-edit me-2"></i>Edit
                                </a>
                            @endif
                        </div>
                    {{-- @else --}}
                        <!-- Show dropdown for 3 or more actions -->
                        {{-- <div class="dropdown">
                            <button type="button" class="btn btn-primary btn-md waves-effect waves-light dropdown-toggle" data-bs-toggle="dropdown">
                                Aksi
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><button class="dropdown-item" onclick="window.print()">
                                    <i class="icon-base ti tabler-printer me-2"></i>Print</button></li>
                                @if($canEditPurchaseOrder)
                                    <li><a class="dropdown-item" href="{{ route('main_transaction.purchase_order.edit', $purchaseOrder['id']) }}">
                                        <i class="icon-base ti tabler-edit me-2"></i>Edit</a></li>
                                @endif
                                @if($canApprovePurchaseOrder && $purchaseOrder['status'] === 'PENDING_APPROVAL')
                                    <li><a class="dropdown-item" href="javascript:void(0);" id="approve-po-btn">
                                        <i class="icon-base ti tabler-check me-2"></i>Setujui</a></li>
                                    <li><a class="dropdown-item" href="javascript:void(0);" id="reject-po-btn">
                                        <i class="icon-base ti tabler-x me-2"></i>Tolak</a></li>
                                @endif
                                @if($canDeletePurchaseOrder && in_array($purchaseOrder['status'], ['DRAFT', 'CANCELLED']))
                                    <li><a class="dropdown-item" href="javascript:void(0);" id="delete-po-btn">
                                        <i class="icon-base ti tabler-trash me-2"></i>Hapus</a></li>
                                @endif
                            </ul>
                        </div> --}}
                    {{-- @endif --}}
                </div>
            </div>

            <!-- Purchase Order Title -->
            <h2>{{ $purchaseOrder['reference_no'] }}</h2>
            <span>
                <h6>Data purchase order berdasarkan tanggal {{ $purchaseOrder['created_at'] }}</h6>
            </span>
            <hr class="my-6 mx-n6" />

            <!-- Informasi Dasar Transaksi Section - Outside tabs, on top -->
            
            <!-- Row 1: Tanggal Transaksi, Nomor Transaksi -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tanggal Transaksi</label>
                    <input type="text" class="form-control" value="{{ $purchaseOrder['date'] }}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nomor Transaksi</label>
                    <input type="text" class="form-control" value="{{ $purchaseOrder['reference_no'] }}" disabled />
                </div>
            </div>

            <!-- Row 2: Status -->
            {{-- <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="icon-base ti tabler-flag"></i></span>
                        <input type="text" class="form-control" value="{{ $purchaseOrder['status'] }}" disabled />
                    </div>
                </div>
            </div> --}}

            <hr class="my-6 mx-n6" />

            <!-- Tabs Section -->
            <div class="col-lg-12">
                <div class="demo-inline-spacing mt-4">
                    <div class="list-group list-group-horizontal-md text-md-center" role="tablist">
                        <a class="list-group-item list-group-item-action waves-effect active" id="purchase-order-info-list-item"
                            data-bs-toggle="list" href="#horizontal-purchase-order-info" aria-selected="true" role="tab">
                            <i class="icon-base ti tabler-info-circle me-2"></i>
                            <b>Informasi Detail</b>
                        </a>
                        <a class="list-group-item list-group-item-action waves-effect" id="delivery-invoice-list-item"
                            data-bs-toggle="list" href="#horizontal-delivery-invoice" aria-selected="false" role="tab">
                            <i class="icon-base ti tabler-truck-delivery me-2"></i>
                            <b>Purchase Delivery & Invoice</b>
                        </a>
                        <a class="list-group-item list-group-item-action waves-effect" id="return-history-list-item"
                            data-bs-toggle="list" href="#horizontal-return-history" aria-selected="false" role="tab">
                            <i class="icon-base ti tabler-arrow-back-up me-2"></i>
                            <b>Return History</b>
                        </a>
                        <a class="list-group-item list-group-item-action waves-effect" id="audit-log-list-item"
                            data-bs-toggle="list" href="#horizontal-audit-log" aria-selected="false" role="tab">
                            <i class="icon-base ti tabler-history me-2"></i>
                            <b>Audit Log</b>
                        </a>
                    </div>
                    <div class="tab-content px-0 mt-0">
                        <!-- Information Tab -->
                        <div class="tab-pane fade show active" id="horizontal-purchase-order-info" role="tabpanel">
                            <div id="purchase-order-info-content">
                                <!-- Informasi Supplier Section -->
                                <div class="section-header mt-12">
                                    <h6>Informasi Supplier</h6>
                                    <hr class="mx-n6">
                                </div>

                                <!-- Row 1: Supplier, Email -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Supplier</label>
                                        <input type="text" class="form-control" value="{{ $purchaseOrder['supplier'] }}" disabled />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email</label>
                                        <input type="text" class="form-control" value="{{ $purchaseOrder['email'] ?? '-' }}" disabled />
                                    </div>
                                </div>

                                <!-- Row 2: Alamat, Nomor Referensi Supplier -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Alamat</label>
                                        <textarea class="form-control" rows="2" disabled>{{ $purchaseOrder['address'] ?? '-' }}</textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Nomor Referensi Supplier</label>
                                        <input type="text" class="form-control" value="{{ $purchaseOrder['supplier_reference'] ?? '-' }}" disabled />
                                    </div>
                                </div>

                                <!-- Detail Pembelian Section -->
                                <div class="section-header mt-12">
                                    <h6>Detail Pembelian</h6>
                                    <hr class="mx-n6">
                                </div>

                                <!-- Row 1: Syarat Pembayaran -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Syarat Pembayaran</label>
                                        <input type="text" class="form-control" value="{{ $purchaseOrder['payment_terms'] ?? '-' }}" disabled />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Buyer</label>
                                        <input type="text" class="form-control" value="{{ $purchaseOrder['customer_name'] }}" disabled />
                                    </div>
                                </div>

                                <!-- Row 2: Jumlah Hari, Tanggal Jatuh Tempo (conditional) -->
                                @if(isset($purchaseOrder['payment_terms_type']) && in_array($purchaseOrder['payment_terms_type'], ['NET', 'CUSTOM']))
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Jumlah Hari</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ $purchaseOrder['days'] ?? '0' }}" disabled>
                                            <span class="input-group-text">Hari</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Tanggal Jatuh Tempo</label>
                                        <input type="text" class="form-control" value="{{ $purchaseOrder['due_date'] ?? '-' }}" disabled />
                                    </div>
                                </div>
                                @endif

                                <!-- Row 3: Gudang, Keterangan -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Gudang</label>
                                        <input type="text" class="form-control" value="{{ $purchaseOrder['warehouse'] }}" disabled />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Keterangan</label>
                                        <textarea class="form-control" rows="3" disabled>{{ $purchaseOrder['description'] ?? '-' }}</textarea>
                                    </div>
                                </div>

                                <!-- Informasi Pembelian Section -->
                                <div class="section-header mt-12 d-flex justify-content-between align-items-center">
                                    <h6>Informasi Pembelian</h6>
                                    {{-- <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="include-tax" name="include_tax" {{ $purchaseOrder['include_tax'] ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="include-tax">Harga Termasuk Pajak</label>
                                    </div> --}}
                                </div>

                                <!-- Product Table -->
                                <div class="mb-4 mx-n6">
                                    <table class="table table-bordered" id="product-table">
                                            <thead>
                                                <tr>
                                                    <th>Produk</th>
                                                    <th>Kuantitas</th>
                                                    <th>Satuan</th>
                                                    <th>Harga Satuan</th>
                                                    {{-- <th>Pajak</th> --}}
                                                    <th>Jumlah</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($purchaseOrder['product_lines'] as $line)
                                                <tr>
                                                    <td>
                                                        @if($canViewProduct)
                                                            <a href="{{ route('master_data.product.detail', $line['product_id']) }}" class="text-primary fw-semibold">
                                                                <strong>{{ $line['product_name'] }}</strong>
                                                            </a>
                                                        @else
                                                            <input type="text" class="form-control" value="{{ $line['product_name'] }}" disabled />
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @php
                                                            // Smart decimal formatting for quantity
                                                            if ($line['quantity'] == floor($line['quantity'])) {
                                                                $formattedQty = number_format($line['quantity'], 0, ',', '.');
                                                            } else {
                                                                $formatted = number_format($line['quantity'], 4, ',', '.');
                                                                $formattedQty = rtrim(rtrim($formatted, '0'), ',');
                                                            }
                                                        @endphp
                                                        <input type="text" class="form-control" value="{{ $formattedQty }}" disabled />
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="{{ $line['unit'] }}" disabled />
                                                    </td>
                                                    <td>
                                                        @php
                                                            // Smart decimal formatting for price
                                                            if ($line['unit_price'] == floor($line['unit_price'])) {
                                                                $formattedPrice = number_format($line['unit_price'], 0, ',', '.');
                                                            } else {
                                                                $formatted = number_format($line['unit_price'], 4, ',', '.');
                                                                $formattedPrice = rtrim(rtrim($formatted, '0'), ',');
                                                            }
                                                        @endphp
                                                        <input type="text" class="form-control" value="Rp {{ $formattedPrice }}" disabled />
                                                    </td>
                                                    {{-- <td>
                                                        <input type="text" class="form-control" value="{{ $line['tax_name'] ?: 'Tanpa Pajak' }}" disabled />
                                                    </td> --}}
                                                    <td>
                                                        @php
                                                            // Smart decimal formatting for total
                                                            if ($line['total'] == floor($line['total'])) {
                                                                $formattedTotal = number_format($line['total'], 0, ',', '.');
                                                            } else {
                                                                $formatted = number_format($line['total'], 4, ',', '.');
                                                                $formattedTotal = rtrim(rtrim($formatted, '0'), ',');
                                                            }
                                                        @endphp
                                                        <input type="text" class="form-control" value="Rp {{ $formattedTotal }}" disabled />
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                </div>

                                <!-- Informasi Tambahan & Ringkasan Section -->
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
                                            @if(isset($purchaseOrder['tags']) && is_array($purchaseOrder['tags']) && count($purchaseOrder['tags']) > 0)
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($purchaseOrder['tags'] as $tag)
                                                        <span class="badge {{ $tag->preview }}">{{ $tag->code }}</span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="text-muted">Tidak ada tag</div>
                                            @endif
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Memo (Internal)</label>
                                            <textarea class="form-control" rows="4" disabled>{{ $purchaseOrder['memo'] ?? '-' }}</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Lampiran</label>
                                            @if(isset($purchaseOrder['attachment']) && $purchaseOrder['attachment'])
                                                <div class="input-group">
                                                    <input type="text" class="form-control" value="{{ $purchaseOrder['attachment']['name'] }}" disabled />
                                                    <a href="{{ $purchaseOrder['attachment']['url'] }}" target="_blank" class="btn btn-outline-primary">
                                                        <i class="icon-base ti tabler-download"></i>
                                                    </a>
                                                </div>
                                            @else
                                                <input type="text" class="form-control" value="Tidak ada lampiran" disabled />
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Right Side: Financial Summary Section -->
                                    <div class="col-md-6">
                                        <div class="summary-section ps-4">
                                            {{-- <div class="row mb-3">
                                                <div class="col-6">
                                                    <label class="form-label">Subtotal</label>
                                                </div>
                                                <div class="col-6">
                                                    <input type="text" class="form-control readonly-field" dir="rtl" value="Rp {{ number_format($purchaseOrder['subtotal'] ?? 0, 0, ',', '.') }}" disabled>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-6">
                                                    <label class="form-label">Total Pajak</label>
                                                </div>
                                                <div class="col-6">
                                                    <input type="text" class="form-control readonly-field" dir="rtl" value="Rp {{ number_format($purchaseOrder['tax_amount'] ?? 0, 0, ',', '.') }}" disabled>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-6">
                                                    <label class="form-label">Total Sebelum Diskon</label>
                                                </div>
                                                <div class="col-6">
                                                    <input type="text" class="form-control readonly-field" dir="rtl" value="Rp {{ number_format($purchaseOrder['total_before_discount'] ?? $purchaseOrder['total'], 0, ',', '.') }}" disabled>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-6">
                                                    <label class="form-label">Diskon</label>
                                                </div>
                                                <div class="col-6">
                                                    <input type="text" class="form-control readonly-field" dir="rtl" value="Rp {{ number_format($purchaseOrder['discount_amount'] ?? 0, 0, ',', '.') }}" disabled>
                                                </div>
                                            </div> --}}
                                            <div class="row mb-3">
                                                <div class="col-6">
                                                    <label class="form-label"><strong>Total Akhir</strong></label>
                                                </div>
                                                <div class="col-6">
                                                    <input type="text" class="form-control readonly-field fw-bold" dir="rtl" value="Rp {{ number_format($purchaseOrder['total'], 0, ',', '.') }}" disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Purchase Delivery & Invoice Tab -->
                        <div class="tab-pane fade" id="horizontal-delivery-invoice" role="tabpanel">
                            
                            {{-- <!-- Summary Section -->
                            <div class="section-header mt-12">
                                <h6>Ringkasan Pembayaran & Pengiriman</h6>
                                <hr class="mx-n6">
                            </div>
                            
                            <!-- Payment Summary Cards -->
                            <div class="row g-3 mb-12">
                                <div class="col-md-3">
                                    <div class="text-center p-3" style="background-color: #f8f9fa; border-radius: 0.375rem;">
                                        <h4 class="mb-0 text-primary">Rp {{ number_format($purchaseOrder['down_payment'] ?? 0, 0, ',', '.') }}</h4>
                                        <small class="text-muted">Down Payment</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center p-3" style="background-color: #f8f9fa; border-radius: 0.375rem;">
                                        <h4 class="mb-0 text-info">Rp {{ number_format($purchaseOrder['total'], 0, ',', '.') }}</h4>
                                        <small class="text-muted">Total Amount</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center p-3" style="background-color: #f8f9fa; border-radius: 0.375rem;">
                                        <h4 class="mb-0 text-success">Rp {{ number_format($purchaseOrder['total_paid'] ?? $purchaseOrder['down_payment'] ?? 0, 0, ',', '.') }}</h4>
                                        <small class="text-muted">Amount Paid</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center p-3" style="background-color: #f8f9fa; border-radius: 0.375rem;">
                                        <h4 class="mb-0 text-warning">Rp {{ number_format($purchaseOrder['remaining_bill'] ?? ($purchaseOrder['total'] - ($purchaseOrder['total_paid'] ?? $purchaseOrder['down_payment'] ?? 0)), 0, ',', '.') }}</h4>
                                        <small class="text-muted">Amount Left</small>
                                    </div>
                                </div>
                            </div> --}}

                            <!-- Delivery Summary Table -->
                            <div class="section-header mt-12">
                                <h6>Ringkasan Pengiriman Produk</h6>
                            </div>
                            
                            <div class="mx-n5 my-4">
                                <table class="table table-bordered" id="delivery-summary-table">
                                    <thead>
                                        <tr>
                                            <th>Produk</th>
                                            <th class="text-end">Total Qty</th>
                                            <th class="text-end">Qty Delivered</th>
                                            <th class="text-end">Remaining Qty</th>
                                            <th class="text-start">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($purchaseOrder['delivery_summary'] as $delivery)
                                        <tr>
                                            <td>
                                                @if($canViewProduct)
                                                    <a href="{{ route('master_data.product.detail', $delivery['product_id']) }}" class="text-primary fw-semibold">
                                                        {{ $delivery['product_name'] }}
                                                    </a>
                                                @else
                                                    {{ $delivery['product_name'] }}
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                @php
                                                    // Smart decimal formatting
                                                    if ($delivery['total_qty'] == floor($delivery['total_qty'])) {
                                                        echo number_format($delivery['total_qty'], 0, ',', '.');
                                                    } else {
                                                        $formatted = number_format($delivery['total_qty'], 4, ',', '.');
                                                        echo rtrim(rtrim($formatted, '0'), ',');
                                                    }
                                                @endphp
                                            </td>
                                            <td class="text-end">
                                                @php
                                                    // Smart decimal formatting
                                                    if ($delivery['qty_delivered'] == floor($delivery['qty_delivered'])) {
                                                        echo number_format($delivery['qty_delivered'], 0, ',', '.');
                                                    } else {
                                                        $formatted = number_format($delivery['qty_delivered'], 4, ',', '.');
                                                        echo rtrim(rtrim($formatted, '0'), ',');
                                                    }
                                                @endphp
                                            </td>
                                            <td class="text-end">
                                                @php
                                                    // Smart decimal formatting
                                                    if ($delivery['remaining_qty'] == floor($delivery['remaining_qty'])) {
                                                        echo number_format($delivery['remaining_qty'], 0, ',', '.');
                                                    } else {
                                                        $formatted = number_format($delivery['remaining_qty'], 4, ',', '.');
                                                        echo rtrim(rtrim($formatted, '0'), ',');
                                                    }
                                                @endphp
                                            </td>
                                            <td class="text-start">
                                                @if($delivery['remaining_qty'] == 0)
                                                    <span class="badge bg-label-success">Complete</span>
                                                @elseif($delivery['qty_delivered'] > 0)
                                                    <span class="badge bg-label-warning">Partial</span>
                                                @else
                                                    <span class="badge bg-label-secondary">Pending</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Purchase Delivery Table -->
                            <div class="section-header mt-12">
                                <h6>Daftar Purchase Delivery</h6>
                            </div>
                            
                            <div class="mx-n5 my-4">
                                <table class="table table-bordered" id="purchase-delivery-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 20%;">Tanggal & Waktu</th>
                                            <th>Nomor Transaksi</th>
                                            <th style="width: 12%;" class="text-start">Status</th>
                                            <th style="width: 15%;">Dibuat Oleh</th>
                                            <th style="width: 15%;">Tag</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($purchaseOrder['purchase_deliveries'] as $delivery)
                                        <tr>
                                            <td>{{ $delivery['date'] }}</td>
                                            <td>
                                                @if($canViewPurchaseDelivery)
                                                    <a href="{{ route('main_transaction.purchase_delivery.detail', $delivery['id']) }}" class="text-primary fw-semibold">
                                                        {{ $delivery['reference_no'] }}
                                                    </a>
                                                @else
                                                    {{ $delivery['reference_no'] }}
                                                @endif
                                            </td>
                                            <td class="text-start">
                                                <span class="badge {{ $delivery['status_badge_class'] }}">{{ $delivery['status'] }}</span>
                                            </td>
                                            <td>
                                                @if($canViewUser)
                                                    <a href="{{ route('master_data.user.detail', $delivery['created_by_id']) }}" class="text-primary fw-semibold">
                                                        {{ $delivery['created_by'] }}
                                                    </a>
                                                @endif
                                            </td>
                                            <td>
                                                @if(isset($delivery['tags']) && count($delivery['tags']) > 0)
                                                    @foreach($delivery['tags'] as $tag)
                                                        <span class="badge {{ $tag->preview }} me-1">{{ $tag->code }}</span>
                                                    @endforeach
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Purchase Invoice Table -->
                            <div class="section-header mt-12">
                                <h6>Daftar Purchase Invoice</h6>
                            </div>
                            
                            <div class="mx-n5 my-4">
                                <table class="table table-bordered" id="purchase-invoice-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 20%;">Tanggal & Waktu</th>
                                            <th>Nomor Transaksi</th>
                                            <th style="width: 15%;" class="text-end">Jumlah</th>
                                            <th style="width: 12%;" class="text-start">Status</th>
                                            <th style="width: 15%;">Dibuat Oleh</th>
                                            <th style="width: 15%;">Tag</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($purchaseOrder['purchase_invoices'] as $invoice)
                                        <tr>
                                            <td>{{ $invoice['date'] }}</td>
                                            <td>
                                                @if($canViewPurchaseInvoice)
                                                    <a href="{{ route('main_transaction.purchase_invoice.detail', $invoice['id']) }}" class="text-primary fw-semibold">
                                                        {{ $invoice['reference_no'] }}
                                                    </a>
                                                @else
                                                    {{ $invoice['reference_no'] }}
                                                @endif
                                            </td>
                                            <td class="text-end">Rp {{ number_format($invoice['amount'], 0, ',', '.') }}</td>
                                            <td class="text-start">
                                                <span class="badge {{ $invoice['status_badge_class'] }}">{{ $invoice['status'] }}</span>
                                            </td>
                                            <td>
                                                @if($canViewUser)
                                                    <a href="{{ route('master_data.user.detail', $invoice['created_by_id']) }}" class="text-primary fw-semibold">
                                                        {{ $invoice['created_by'] }}
                                                    </a>
                                                @else
                                                    {{ $invoice['created_by'] }}
                                                @endif
                                            </td>
                                            <td>
                                                @if(isset($invoice['tags']) && count($invoice['tags']) > 0)
                                                    @foreach($invoice['tags'] as $tag)
                                                        <span class="badge {{ $tag->preview }} me-1">{{ $tag->code }}</span>
                                                    @endforeach
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Return History Tab -->
                        <div class="tab-pane fade" id="horizontal-return-history" role="tabpanel">
                            <div class="section-header mt-12">
                                <h6>Daftar Purchase Return</h6>
                            </div>
                            
                            <div class="mx-n5 my-4">
                                <table class="table table-bordered" id="purchase-return-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 15%;">Tanggal Return</th>
                                            <th>No Return</th>
                                            <th>Invoice Terkait</th>
                                            <th style="width: 15%;" class="text-end">Total Return</th>
                                            <th style="width: 12%;" class="text-start">Status</th>
                                            <th style="width: 12%;" class="text-start">Stock Adjustment</th>
                                            <th style="width: 8%;" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($purchaseOrder['purchase_returns'] as $return)
                                        <tr>
                                            <td>{{ $return['return_date'] }}</td>
                                            <td>
                                                @if($canViewReturnManagement)
                                                    <a href="{{ route('main_transaction.return_management.detail', $return['id']) }}" class="text-primary fw-semibold">
                                                        {{ $return['transaction_code'] }}
                                                    </a>
                                                @else
                                                    {{ $return['transaction_code'] }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($canViewPurchaseInvoice && $return['f_source_invoice_id'])
                                                    <a href="{{ route('main_transaction.purchase_invoice.detail', $return['f_source_invoice_id']) }}" class="text-primary fw-semibold">
                                                        {{ $return['source_invoice_code'] }}
                                                    </a>
                                                @else
                                                    {{ $return['source_invoice_code'] }}
                                                @endif
                                            </td>
                                            <td class="text-end">Rp {{ number_format($return['grand_total'], 0, ',', '.') }}</td>
                                            <td class="text-start">
                                                <span class="badge {{ $return['status_badge_class'] }}">{{ $return['status'] }}</span>
                                            </td>
                                            <td class="text-start">
                                                <span class="badge {{ $return['stock_adjustment_status_badge_class'] }}">
                                                    @if($return['stock_adjustment_status'] === 'ADJUSTED')
                                                        Sudah Disesuaikan
                                                    @else
                                                        Belum Disesuaikan
                                                    @endif
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @if($canViewReturnManagement)
                                                    <a href="{{ route('main_transaction.return_management.detail', $return['id']) }}" class="btn btn-sm btn-primary waves-effect waves-light" title="Detail">
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
                                        @foreach($auditLogs as $log)
                                        <tr>
                                            <td>{{ $log['created_at'] }}</td>
                                            <td>
                                                @if($canViewUser)
                                                    <a href="{{ route('master_data.user.detail', $log['user_id']) }}" class="text-primary fw-semibold">
                                                        {{ $log['user_name'] }}
                                                    </a>
                                                @else
                                                    {{ $log['user_name'] }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($log['action_type'] === 'CREATE')
                                                    <span class="badge bg-label-success">CREATE</span>
                                                @elseif($log['action_type'] === 'UPDATE')
                                                    <span class="badge bg-label-warning">UPDATE</span>
                                                @elseif($log['action_type'] === 'DELETE')
                                                    <span class="badge bg-label-danger">DELETE</span>
                                                @else
                                                    <span class="badge bg-label-info">{{ $log['action_type'] }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $log['description'] }}</td>
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
        $statusMeta = match ($purchaseOrder['status'] ?? '') {
            'DRAFT' => ['label' => 'Draft', 'class' => 'secondary'],
            'PENDING', 'PENDING_APPROVAL' => ['label' => 'Menunggu Persetujuan', 'class' => 'warning'],
            'APPROVED', 'PROCESSED' => ['label' => 'Diproses', 'class' => 'info'],
            'COMPLETED' => ['label' => 'Selesai', 'class' => 'success'],
            'CANCELLED' => ['label' => 'Dibatalkan', 'class' => 'danger'],
            default => ['label' => $humanStatus($purchaseOrder['status'] ?? null), 'class' => 'secondary'],
        };
        $deliveryLabel = function ($status) use ($humanStatus) {
            return match ($status) {
                'COMPLETED', 'DELIVERED' => 'Diterima',
                'PARTIAL', 'IN_TRANSIT' => 'Sebagian Diterima',
                'PENDING' => 'Menunggu',
                'CANCELLED' => 'Dibatalkan',
                default => $humanStatus($status),
            };
        };
        $invoiceLabel = function ($status) use ($humanStatus) {
            return match ($status) {
                'PAID', 'SETTLED', 'COMPLETED' => 'Lunas',
                'PARTIAL_PAID', 'PARTIALLY_PAID' => 'Dibayar Sebagian',
                'UNPAID', 'PENDING' => 'Belum Dibayar',
                'OVERDUE' => 'Lewat Jatuh Tempo',
                'CANCELLED' => 'Dibatalkan',
                default => $humanStatus($status),
            };
        };
        $validTags = collect($purchaseOrder['tags'] ?? [])->filter(fn($tag) => is_object($tag) && !empty($tag->code));
        $isOffer = str_starts_with((string) ($purchaseOrder['reference_no'] ?? ''), 'PR');
        $hasDelivery = count($purchaseOrder['purchase_deliveries'] ?? []) > 0;
        $total = (float) ($purchaseOrder['total'] ?? 0);
        $paid = (float) ($purchaseOrder['total_paid'] ?? 0);
        $remaining = (float) ($purchaseOrder['remaining_bill'] ?? max($total - $paid, 0));
    @endphp

    <div class="compact-detail">
        <div class="detail-card mb-4">
            <div class="detail-header d-flex flex-wrap align-items-start justify-content-between gap-3">
                <div>
                    <div class="detail-breadcrumb">
                        <a href="{{ route('main_transaction.purchase_order.index') }}">Pembelian</a>
                        <i class="icon-base ti tabler-chevron-right"></i>
                        <span>Pesanan</span>
                        <i class="icon-base ti tabler-chevron-right"></i>
                        <span>Detail</span>
                    </div>
                    <h1 class="detail-title">Detail Pesanan Pembelian</h1>
                </div>
                <div class="detail-actions d-flex flex-wrap justify-content-end gap-2">
                    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="window.print()">
                        <i class="icon-base ti tabler-printer me-2"></i>Print
                    </button>
                    @if (!$isOffer && $canCreatePurchaseDelivery && !in_array($purchaseOrder['status'], ['DRAFT', 'CANCELLED']))
                        <a href="{{ route('main_transaction.purchase_delivery.add', ['po_id' => $purchaseOrder['id']]) }}"
                            class="btn btn-warning waves-effect waves-light">
                            <i class="icon-base ti tabler-truck-delivery me-2"></i>Buat Pengiriman
                        </a>
                    @endif
                    @if (!$isOffer && $canEditPurchaseOrder && !$hasDelivery && $purchaseOrder['status'] !== 'CANCELLED')
                        <a href="{{ route('main_transaction.purchase_order.edit', $purchaseOrder['id']) }}"
                            class="btn btn-success waves-effect waves-light">
                            <i class="icon-base ti tabler-edit me-2"></i>Edit
                        </a>
                    @endif
                </div>
            </div>

            <div class="summary-grid">
                <div class="summary-item">
                    <span class="summary-label">Pesanan Pembelian</span>
                    <div class="summary-code">{{ $purchaseOrder['reference_no'] }}</div>
                    <div class="summary-note">
                        Dibuat pada {{ $formatDate($purchaseOrder['created_at'] ?? null, true) }}
                        @if (!empty($purchaseOrder['created_by_name']))
                            oleh {{ $purchaseOrder['created_by_name'] }}
                        @endif
                    </div>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Status Pesanan</span>
                    <span class="badge bg-label-{{ $statusMeta['class'] }} fs-6 py-2 px-3">
                        <i class="icon-base ti tabler-check me-1"></i>{{ $statusMeta['label'] }}
                    </span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Syarat Pembayaran</span>
                    <span class="badge bg-label-primary fs-6 py-2 px-3">
                        <i class="icon-base ti tabler-check me-1"></i>{{ $purchaseOrder['payment_terms'] ?? '-' }}
                    </span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Sisa Tagihan</span>
                    <div class="summary-money">{{ $formatCurrency($remaining) }}</div>
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
                            <div class="info-value">{{ $purchaseOrder['supplier'] ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="info-icon icon-base ti tabler-map-pin"></i>
                        <div>
                            <span class="info-label">Alamat Supplier</span>
                            <div class="info-value">{{ $purchaseOrder['address'] ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="info-icon icon-base ti tabler-calendar"></i>
                        <div>
                            <span class="info-label">Tanggal Transaksi</span>
                            <div class="info-value">{{ $formatDate($purchaseOrder['date'] ?? null) }}</div>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="info-icon icon-base ti tabler-calendar-due"></i>
                        <div>
                            <span class="info-label">Jatuh Tempo</span>
                            <div class="info-value">{{ $formatDate($purchaseOrder['due_date'] ?? null) }}</div>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="info-icon icon-base ti tabler-file-text"></i>
                        <div>
                            <span class="info-label">No. Transaksi</span>
                            <div class="info-value">{{ $purchaseOrder['reference_no'] ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="info-icon icon-base ti tabler-file-symlink"></i>
                        <div>
                            <span class="info-label">No. Referensi</span>
                            <div class="info-value">{{ $purchaseOrder['supplier_reference'] ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="info-icon icon-base ti tabler-home"></i>
                        <div>
                            <span class="info-label">Gudang</span>
                            <div class="info-value">{{ $purchaseOrder['warehouse'] ?? '-' }}</div>
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
                        <i class="info-icon icon-base ti tabler-user"></i>
                        <div>
                            <span class="info-label">Buyer</span>
                            <div class="info-value">{{ $purchaseOrder['customer_name'] ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="info-icon icon-base ti tabler-user-check"></i>
                        <div>
                            <span class="info-label">Dibuat Oleh</span>
                            <div class="info-value">{{ $purchaseOrder['created_by_name'] ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="detail-card">
            <div class="detail-section">
                <h2 class="detail-section-title">Informasi Produk</h2>
                <div class="compact-table table-responsive">
                    <table class="table" id="product-table">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Deskripsi</th>
                                <th class="text-end">Qty</th>
                                <th>Satuan</th>
                                <th class="text-end">Harga Satuan</th>
                                <th class="text-end">Diskon</th>
                                <th class="text-end">Pajak</th>
                                <th class="text-end">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($purchaseOrder['product_lines'] as $line)
                                <tr>
                                    <td>
                                        @if($canViewProduct)
                                            <a href="{{ route('master_data.product.detail', $line['product_id']) }}" class="fw-semibold">
                                                {{ $line['product_name'] }}
                                            </a>
                                        @else
                                            {{ $line['product_name'] }}
                                        @endif
                                    </td>
                                    <td>{{ $line['description'] ?: '-' }}</td>
                                    <td class="text-end">{{ $formatQty($line['quantity']) }}</td>
                                    <td>{{ $line['unit'] ?? '-' }}</td>
                                    <td class="text-end">{{ $formatCurrency($line['unit_price']) }}</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end fw-semibold">{{ $formatCurrency($line['total']) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="empty-row">Tidak ada produk</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="totals-panel">
                    <div class="total-line">
                        <strong>Total</strong>
                        <strong>{{ $formatCurrency($total) }}</strong>
                    </div>
                    <div class="total-line">
                        <span>Jumlah Terbayar</span>
                        <strong>{{ $formatCurrency($paid) }}</strong>
                    </div>
                    <div class="total-line total-highlight">
                        <strong>Sisa Tagihan</strong>
                        <strong>{{ $formatCurrency($remaining) }}</strong>
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
                        <div class="info-value">{{ $purchaseOrder['description'] ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <span class="info-label">Memo Internal</span>
                        <div class="info-value">{{ $purchaseOrder['memo'] ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <span class="info-label">Lampiran</span>
                        @if(!empty($purchaseOrder['attachment']['url']))
                            <a href="{{ $purchaseOrder['attachment']['url'] }}" target="_blank" class="btn btn-outline-primary btn-sm">
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
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#purchase-delivery-history" type="button" role="tab">
                            Riwayat Pengiriman
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#purchase-invoice-history" type="button" role="tab">
                            Riwayat Penagihan
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#purchase-return-history" type="button" role="tab">
                            Retur History
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#purchase-audit-history" type="button" role="tab">
                            Log Audit
                        </button>
                    </li>
                </ul>

                <div class="tab-content pt-4 px-0">
                    <div class="tab-pane fade show active" id="purchase-delivery-history" role="tabpanel">
                        <div class="compact-table table-responsive">
                            <table class="table" id="purchase-delivery-table">
                                <thead>
                                    <tr>
                                        <th>Tanggal Pengiriman</th>
                                        <th>Nomor Pengiriman</th>
                                        <th>Status Pengiriman</th>
                                        <th>Dibuat Oleh</th>
                                        <th>Tag</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($purchaseOrder['purchase_deliveries'] as $delivery)
                                        <tr>
                                            <td>{{ $formatDate($delivery['date'] ?? null) }}</td>
                                            <td>
                                                @if($canViewPurchaseDelivery)
                                                    <a href="{{ route('main_transaction.purchase_delivery.detail', $delivery['id']) }}" class="fw-semibold">
                                                        {{ $delivery['reference_no'] }}
                                                    </a>
                                                @else
                                                    {{ $delivery['reference_no'] }}
                                                @endif
                                            </td>
                                            <td><span class="badge {{ $delivery['status_badge_class'] }}">{{ $deliveryLabel($delivery['status'] ?? null) }}</span></td>
                                            <td>{{ $delivery['created_by'] ?? '-' }}</td>
                                            <td>
                                                @forelse(collect($delivery['tags'] ?? [])->filter(fn($tag) => is_object($tag) && !empty($tag->code)) as $tag)
                                                    <span class="badge {{ $tag->preview }} me-1">{{ $tag->code }}</span>
                                                @empty
                                                    -
                                                @endforelse
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="empty-row">Belum ada riwayat pengiriman</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="purchase-invoice-history" role="tabpanel">
                        <div class="compact-table table-responsive">
                            <table class="table" id="purchase-invoice-table">
                                <thead>
                                    <tr>
                                        <th>Tanggal Penagihan</th>
                                        <th>Nomor Penagihan</th>
                                        <th class="text-end">Total Penagihan</th>
                                        <th>Status Penagihan</th>
                                        <th>Dibuat Oleh</th>
                                        <th>Tag</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($purchaseOrder['purchase_invoices'] as $invoice)
                                        <tr>
                                            <td>{{ $formatDate($invoice['date'] ?? null) }}</td>
                                            <td>
                                                @if($canViewPurchaseInvoice)
                                                    <a href="{{ route('main_transaction.purchase_invoice.detail', $invoice['id']) }}" class="fw-semibold">
                                                        {{ $invoice['reference_no'] }}
                                                    </a>
                                                @else
                                                    {{ $invoice['reference_no'] }}
                                                @endif
                                            </td>
                                            <td class="text-end">{{ $formatCurrency($invoice['amount'] ?? 0) }}</td>
                                            <td><span class="badge {{ $invoice['status_badge_class'] }}">{{ $invoiceLabel($invoice['status'] ?? null) }}</span></td>
                                            <td>{{ $invoice['created_by'] ?? '-' }}</td>
                                            <td>
                                                @forelse(collect($invoice['tags'] ?? [])->filter(fn($tag) => is_object($tag) && !empty($tag->code)) as $tag)
                                                    <span class="badge {{ $tag->preview }} me-1">{{ $tag->code }}</span>
                                                @empty
                                                    -
                                                @endforelse
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="empty-row">Belum ada riwayat penagihan</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="purchase-return-history" role="tabpanel">
                        <div class="compact-table table-responsive">
                            <table class="table" id="purchase-return-table">
                                <thead>
                                    <tr>
                                        <th>Tanggal Retur</th>
                                        <th>No. Retur</th>
                                        <th>Penagihan Terkait</th>
                                        <th class="text-end">Total Retur</th>
                                        <th>Status Retur</th>
                                        <th>Penyesuaian Stok</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($purchaseOrder['purchase_returns'] as $return)
                                        <tr>
                                            <td>{{ $formatDate($return['return_date'] ?? null) }}</td>
                                            <td>
                                                @if($canViewReturnManagement)
                                                    <a href="{{ route('main_transaction.return_management.detail', $return['id']) }}" class="fw-semibold">
                                                        {{ $return['transaction_code'] }}
                                                    </a>
                                                @else
                                                    {{ $return['transaction_code'] }}
                                                @endif
                                            </td>
                                            <td>{{ $return['source_invoice_code'] ?? '-' }}</td>
                                            <td class="text-end">{{ $formatCurrency($return['grand_total'] ?? 0) }}</td>
                                            <td><span class="badge {{ $return['status_badge_class'] }}">{{ $humanStatus($return['status'] ?? null) }}</span></td>
                                            <td>
                                                <span class="badge {{ $return['stock_adjustment_status_badge_class'] }}">
                                                    {{ ($return['stock_adjustment_status'] ?? null) === 'ADJUSTED' ? 'Sudah Disesuaikan' : 'Belum Disesuaikan' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="empty-row">Belum ada riwayat retur</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="purchase-audit-history" role="tabpanel">
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
                                            <td>{{ $formatDate($log['created_at'] ?? null, true) }}</td>
                                            <td>{{ $log['user_name'] ?? '-' }}</td>
                                            <td><span class="badge bg-label-info">{{ $log['action_type'] ?? '-' }}</span></td>
                                            <td>{{ $log['description'] ?? '-' }}</td>
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
        // Initialize DataTables for tables inside tabs
        $('#product-table').DataTable({
            orderCellsTop: true,
            searching: false,
            paging: false,
            info: false,
            ordering: false
        });

        $('#delivery-summary-table').DataTable({
            orderCellsTop: true,
            searching: false,
            paging: false,
            info: false,
            ordering: false
        });

        $('#purchase-delivery-table').DataTable({
            orderCellsTop: true,
            ordering: false,
            searching: false,
            paging: false,
            info: false
        });

        $('#purchase-invoice-table').DataTable({
            orderCellsTop: true,
            ordering: false,
            searching: false,
            paging: false,
            info: false
        });

        // Initialize Purchase Return Table
        $('#purchase-return-table').DataTable({
            orderCellsTop: true,
            ordering: false,
            searching: false,
            paging: false,
            info: false
        });

        $('#audit-log-table').DataTable({
            orderCellsTop: true,
            order: [[0, 'desc']], // Sort by date descending
            searching: false,
            paging: false,
            info: false
        });

        // Action button handlers
        $('#approve-po-btn').on('click', function() {
            Swal.fire({
                title: 'Setujui Purchase Order?',
                text: 'Purchase Order akan disetujui dan dapat diproses lebih lanjut.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Setujui',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-outline-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Purchase Order berhasil disetujui.',
                        icon: 'success',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                }
            });
        });

        $('#reject-po-btn').on('click', function() {
            Swal.fire({
                title: 'Tolak Purchase Order?',
                text: 'Purchase Order akan ditolak dan tidak dapat diproses.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Tolak',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-outline-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Purchase Order Ditolak',
                        text: 'Purchase Order berhasil ditolak.',
                        icon: 'success',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                }
            });
        });

        $('#delete-po-btn').on('click', function() {
            Swal.fire({
                title: 'Hapus Purchase Order?',
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
                    Swal.fire({
                        title: 'Dihapus!',
                        text: 'Purchase Order berhasil dihapus.',
                        icon: 'success',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    }).then(() => {
                        window.location.href = '{{ route("main_transaction.purchase_order.index") }}';
                    });
                }
            });
        });
    });
</script>
@endsection
