@extends('layouts.default')
@section('page_title')
    Detail Purchase Invoice - {{ $invoice_data->transaction_code }}
@stop
@section('content')
    <div class="card mb-6">
        <div class="card-body">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <h4 class="mb-3">Detail Purchase Invoice</h4>
                </div>
                <div class="col-md-4">
                    <div class="d-flex gap-2 justify-content-end">
                        {{-- Future: Invoice Settlement Button - will redirect to settlement page for payment processing --}}
                        {{-- @if (in_array($invoice_data->status, ['DRAFT', 'PENDING', 'APPROVED']))
                            <button type="button" class="btn btn-info btn-md waves-effect waves-light" onclick="alert('Feature coming soon: Invoice Settlement')">
                                <i class="icon-base ti tabler-credit-card me-2"></i>
                                Settle Invoice
                            </button>
                        @endif
                        
                        @if ($canEditPurchaseInvoice)
                            <a href="{{ route('main_transaction.purchase_invoice.edit', $invoice_data->id) }}" class="btn btn-primary btn-md waves-effect waves-light">
                                <i class="icon-base ti tabler-edit me-2"></i>
                                Edit
                            </a>
                        @endif --}}

                        @if ($hasCompletedDelivery && !$returnData)
                            <a href="{{ route('main_transaction.return_management.add') }}?return_type=PURCHASE_RETURN&source_invoice_id={{ $invoice_data->id }}"
                                class="btn btn-warning btn-md waves-effect waves-light">
                                <i class="icon-base ti tabler-arrow-back-up me-2"></i>
                                Return
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Purchase Invoice Title -->
            <h2>{{ $invoice_data->transaction_code }}</h2>
            <span>
                <h6>Data purchase invoice berdasarkan tanggal {{ $invoice_data->created_at }}</h6>
            </span>
            <hr class="my-6 mx-n6" />

            <!-- Informasi Dasar Transaksi Section - Outside tabs, on top -->

            <!-- Row 1: Tanggal Transaksi, Nomor Transaksi -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tanggal Transaksi</label>
                    <input type="text" class="form-control"
                        value="{{ $invoice_data->invoice_date ? date('d/m/Y', strtotime($invoice_data->invoice_date)) : '-' }}"
                        disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nomor Transaksi</label>
                    <input type="text" class="form-control" value="{{ $invoice_data->transaction_code }}" disabled />
                </div>
            </div>

            <hr class="my-6 mx-n6" />

            <!-- Tabs Section -->
            <div class="col-lg-12">
                <div class="demo-inline-spacing mt-4">
                    <div class="list-group list-group-horizontal-md text-md-center" role="tablist">
                        <a class="list-group-item list-group-item-action waves-effect active"
                            id="purchase-invoice-info-list-item" data-bs-toggle="list"
                            href="#horizontal-purchase-invoice-info" aria-selected="true" role="tab">
                            <i class="icon-base ti tabler-info-circle me-2"></i>
                            <b>Informasi Detail</b>
                        </a>
                        <a class="list-group-item list-group-item-action waves-effect" id="invoice-items-list-item"
                            data-bs-toggle="list" href="#horizontal-invoice-items" aria-selected="false" role="tab">
                            <i class="icon-base ti tabler-package me-2"></i>
                            <b>Items Invoice</b>
                        </a>
                        <a class="list-group-item list-group-item-action waves-effect" id="audit-log-list-item"
                            data-bs-toggle="list" href="#horizontal-audit-log" aria-selected="false" role="tab">
                            <i class="icon-base ti tabler-history me-2"></i>
                            <b>Audit Log</b>
                        </a>
                    </div>
                    <div class="tab-content px-0 mt-0">
                        <!-- Information Tab -->
                        <div class="tab-pane fade show active" id="horizontal-purchase-invoice-info" role="tabpanel">
                            <div id="purchase-invoice-info-content">
                                <!-- Informasi Purchase Order Section -->
                                <div class="section-header mt-12">
                                    <h6>Informasi Purchase Order</h6>
                                    <hr class="mx-n6">
                                </div>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Purchase Order</label>
                                        @if ($canViewPurchaseOrder && $invoice_data->po_id)
                                            <div class="input-group">
                                                <input type="text" class="form-control"
                                                    value="{{ $invoice_data->po_reference ?? '-' }}" disabled />
                                                <a href="{{ route('main_transaction.purchase_order.detail', $invoice_data->po_id) }}"
                                                    class="btn btn-outline-primary" title="Lihat Detail PO">
                                                    <i class="icon-base ti tabler-external-link"></i>
                                                </a>
                                            </div>
                                        @else
                                            <input type="text" class="form-control"
                                                value="{{ $invoice_data->po_reference ?? '-' }}" disabled />
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Supplier</label>
                                        <input type="text" class="form-control"
                                            value="{{ $invoice_data->supplier_name ?? '-' }}" disabled />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email Supplier</label>
                                        <input type="text" class="form-control"
                                            value="{{ $invoice_data->supplier_email ?? '-' }}" disabled />
                                    </div>
                                </div>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Alamat Supplier</label>
                                        <textarea class="form-control" rows="2" disabled>{{ $invoice_data->supplier_address ?? '-' }}</textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Status PO</label>
                                        <input type="text" class="form-control"
                                            value="{{ $invoice_data->po_status ?? '-' }}" disabled />
                                    </div>
                                </div>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Tanggal PO</label>
                                        <input type="text" class="form-control"
                                            value="{{ $invoice_data->po_date ? date('d/m/Y', strtotime($invoice_data->po_date)) : '-' }}"
                                            disabled />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Grand Total PO</label>
                                        <input type="text" class="form-control"
                                            value="{{ $invoice_data->po_grand_total ? 'Rp ' . number_format($invoice_data->po_grand_total, 0, ',', '.') : '-' }}"
                                            disabled />
                                    </div>
                                </div>

                                <!-- Informasi Invoice Section -->
                                <div class="section-header mt-12">
                                    <h6>Informasi Invoice</h6>
                                    <hr class="mx-n6">
                                </div>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Tanggal Transaksi</label>
                                        <input type="text" class="form-control"
                                            value="{{ $invoice_data->transaction_date ? date('d/m/Y', strtotime($invoice_data->transaction_date)) : '-' }}"
                                            disabled />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Tanggal Jatuh Tempo</label>
                                        <input type="text" class="form-control"
                                            value="{{ $invoice_data->due_date ? date('d/m/Y', strtotime($invoice_data->due_date)) : '-' }}"
                                            disabled />
                                    </div>
                                </div>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Termin Pembayaran</label>
                                        <input type="text" class="form-control"
                                            value="{{ $invoice_data->payment_term_name ?? ($invoice_data->payment_terms ?? '-') }}"
                                            disabled />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Status</label>
                                        @php
                                            $statusClass = '';
                                            switch ($invoice_data->status) {
                                                case 'DRAFT':
                                                    $statusClass = 'bg-label-secondary';
                                                    break;
                                                case 'UNPAID':
                                                    $statusClass = 'bg-label-warning';
                                                    break;
                                                case 'PAID':
                                                    $statusClass = 'bg-label-success';
                                                    break;
                                                case 'PARTIAL_PAID':
                                                    $statusClass = 'bg-label-info';
                                                    break;
                                                case 'OVERDUE':
                                                    $statusClass = 'bg-label-danger';
                                                    break;
                                                case 'CANCELLED':
                                                    $statusClass = 'bg-label-dark';
                                                    break;
                                                default:
                                                    $statusClass = 'bg-label-secondary';
                                            }
                                        @endphp
                                        <span
                                            class="badge {{ $statusClass }} fs-6 py-2 px-3">{{ $invoice_data->status }}</span>
                                    </div>
                                </div>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Keterangan</label>
                                        <textarea class="form-control" rows="3" disabled>{{ $invoice_data->description ?? '-' }}</textarea>
                                    </div>
                                </div>

                                <!-- Financial Information Section -->
                                <div class="section-header mt-12">
                                    <h6>Informasi Keuangan</h6>
                                    <hr class="mx-n6">
                                </div>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Subtotal</label>
                                        <input type="text" class="form-control"
                                            value="Rp {{ number_format($invoice_data->subtotal ?? 0, 0, ',', '.') }}"
                                            disabled />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Total Pajak</label>
                                        <input type="text" class="form-control"
                                            value="Rp {{ number_format($invoice_data->total_tax ?? 0, 0, ',', '.') }}"
                                            disabled />
                                    </div>
                                </div>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Total Discount</label>
                                        <input type="text" class="form-control"
                                            value="Rp {{ number_format($invoice_data->total_discount ?? 0, 0, ',', '.') }}"
                                            disabled />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Grand Total</label>
                                        <input type="text" class="form-control fw-bold text-primary"
                                            value="Rp {{ number_format($invoice_data->grand_total ?? 0, 0, ',', '.') }}"
                                            disabled />
                                    </div>
                                </div>

                                <!-- Additional Information Section -->
                                <div class="section-header mt-12">
                                    <h6>Informasi Tambahan & Ringkasan</h6>
                                    <hr class="mx-n6">
                                </div>

                                <!-- Return Information Section -->
                                @if ($returnData)
                                    <div class="section-header mt-12">
                                        <h6>Informasi Return</h6>
                                        <hr class="mx-n6">
                                    </div>
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-6">
                                            <label class="form-label">No Return</label>
                                            @if ($canViewReturnManagement)
                                                <div class="input-group">
                                                    <input type="text" class="form-control"
                                                        value="{{ $returnData->transaction_code }}" disabled />
                                                    <a href="{{ route('main_transaction.return_management.detail', $returnData->id) }}"
                                                        class="btn btn-outline-primary" title="Lihat Detail Return">
                                                        <i class="icon-base ti tabler-external-link"></i>
                                                    </a>
                                                </div>
                                            @else
                                                <input type="text" class="form-control"
                                                    value="{{ $returnData->transaction_code }}" disabled />
                                            @endif
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Tanggal Return</label>
                                            <input type="text" class="form-control"
                                                value="{{ \Carbon\Carbon::parse($returnData->return_date)->format('d/m/Y') }}"
                                                disabled />
                                        </div>
                                    </div>

                                    <div class="row g-3 mb-4">
                                        <div class="col-md-6">
                                            <label class="form-label">Total Return</label>
                                            <input type="text" class="form-control"
                                                value="Rp {{ number_format($returnData->grand_total, 0, ',', '.') }}"
                                                disabled />
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Status Return</label>
                                            @php
                                                $badgeClass = match ($returnData->status) {
                                                    'DRAFT' => 'bg-label-secondary',
                                                    'PENDING_APPROVAL' => 'bg-label-warning',
                                                    'APPROVED' => 'bg-label-success',
                                                    'PROCESSED' => 'bg-label-info',
                                                    'REJECTED' => 'bg-label-danger',
                                                    default => 'bg-label-secondary',
                                                };
                                            @endphp
                                            <div class="form-control bg-light">
                                                <span class="badge {{ $badgeClass }}">{{ $returnData->status }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-3 mb-4">
                                        <div class="col-md-6">
                                            <label class="form-label">Alasan Return</label>
                                            <input type="text" class="form-control"
                                                value="{{ $returnData->return_reason }}" disabled />
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Stock Adjustment</label>
                                            <div class="form-control bg-light">
                                                @if ($returnData->processing_status === 'PROCESSED')
                                                    <span class="badge bg-label-success">Sudah Disesuaikan</span>
                                                @else
                                                    <span class="badge bg-label-warning">Belum Disesuaikan</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    @if ($returnData->return_reason_description)
                                        <div class="row g-3 mb-4">
                                            <div class="col-md-12">
                                                <label class="form-label">Deskripsi Return</label>
                                                <textarea class="form-control" rows="2" disabled>{{ $returnData->return_reason_description }}</textarea>
                                            </div>
                                        </div>
                                    @endif
                                @endif

                                <!-- System Information Section -->
                                <div class="section-header mt-12">
                                    <h6>Informasi Sistem</h6>
                                    <hr class="mx-n6">
                                </div>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Dibuat Pada</label>
                                        <input type="text" class="form-control"
                                            value="{{ $invoice_data->created_at ? date('d/m/Y H:i:s', strtotime($invoice_data->created_at)) : '-' }}"
                                            disabled />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Dibuat Oleh</label>
                                        @if ($canViewUser && $invoice_data->created_by)
                                            <div class="input-group">
                                                <input type="text" class="form-control"
                                                    value="{{ $invoice_data->created_by_name ?? '-' }}" disabled />
                                                <a href="{{ route('master_data.user.detail', $invoice_data->created_by) }}"
                                                    class="btn btn-outline-primary" title="Lihat Detail User">
                                                    <i class="icon-base ti tabler-external-link"></i>
                                                </a>
                                            </div>
                                        @else
                                            <input type="text" class="form-control"
                                                value="{{ $invoice_data->created_by_name ?? '-' }}" disabled />
                                        @endif
                                    </div>
                                </div>
                                @if ($invoice_data->updated_at)
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-6">
                                            <label class="form-label">Diupdate Pada</label>
                                            <input type="text" class="form-control"
                                                value="{{ date('d/m/Y H:i:s', strtotime($invoice_data->updated_at)) }}"
                                                disabled />
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Diupdate Oleh</label>
                                            @if ($canViewUser && $invoice_data->updated_by)
                                                <div class="input-group">
                                                    <input type="text" class="form-control"
                                                        value="{{ $invoice_data->updated_by_name ?? '-' }}" disabled />
                                                    <a href="{{ route('master_data.user.detail', $invoice_data->updated_by) }}"
                                                        class="btn btn-outline-primary" title="Lihat Detail User">
                                                        <i class="icon-base ti tabler-external-link"></i>
                                                    </a>
                                                </div>
                                            @else
                                                <input type="text" class="form-control"
                                                    value="{{ $invoice_data->updated_by_name ?? '-' }}" disabled />
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Items Invoice Tab -->
                        <div class="tab-pane fade" id="horizontal-invoice-items" role="tabpanel">
                            <!-- Items Invoice Section -->
                            <div class="section-header mt-12">
                                <h6>Items Invoice</h6>
                            </div>

                            <!-- Product Table -->
                            <div class="mb-4 mx-n5">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="width: 30%;">Produk</th>
                                                <th style="width: 12%;">Qty PO</th>
                                                <th style="width: 12%;">Qty Invoice</th>
                                                <th style="width: 15%;">Unit Price</th>
                                                <th style="width: 15%;">Total</th>
                                                <th style="width: 16%;">Note</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($invoice_products as $product)
                                                <tr>
                                                    <td>
                                                        @if ($canViewProduct)
                                                            <a href="{{ route('master_data.product.detail', $product->f_product_id) }}"
                                                                class="text-primary fw-semibold">
                                                                <strong>{{ $product->product_code ?? '' }} -
                                                                    {{ $product->product_name ?? '' }}</strong>
                                                            </a>
                                                        @else
                                                            {{ $product->product_code ?? '' }} -
                                                            {{ $product->product_name ?? '' }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @php
                                                            $orderedQty = $product->ordered_qty ?? 0;
                                                            if ($orderedQty == floor($orderedQty)) {
                                                                $formattedOrderedQty = number_format($orderedQty, 0, ',', '.');
                                                            } else {
                                                                $formatted = number_format($orderedQty, 4, ',', '.');
                                                                $formattedOrderedQty = rtrim(rtrim($formatted, '0'), ',');
                                                            }
                                                        @endphp
                                                        {{ $formattedOrderedQty }} {{ $product->unit_name ?? '' }}
                                                    </td>
                                                    <td>
                                                        @php
                                                            $qty = $product->qty ?? 0;
                                                            if ($qty == floor($qty)) {
                                                                $formattedQty = number_format($qty, 0, ',', '.');
                                                            } else {
                                                                $formatted = number_format($qty, 4, ',', '.');
                                                                $formattedQty = rtrim(rtrim($formatted, '0'), ',');
                                                            }
                                                        @endphp
                                                        {{ $formattedQty }} {{ $product->unit_name ?? '' }}
                                                    </td>
                                                    <td>
                                                        @php
                                                            $unitPrice = $product->unit_price ?? 0;
                                                            if ($unitPrice == floor($unitPrice)) {
                                                                $formattedUnitPrice = number_format($unitPrice, 0, ',', '.');
                                                            } else {
                                                                $formatted = number_format($unitPrice, 4, ',', '.');
                                                                $formattedUnitPrice = rtrim(rtrim($formatted, '0'), ',');
                                                            }
                                                        @endphp
                                                        {{ $formattedUnitPrice }}
                                                    </td>
                                                    <td>
                                                        @php
                                                            $totalPrice = $product->total_price ?? 0;
                                                            if ($totalPrice == floor($totalPrice)) {
                                                                $formattedTotalPrice = number_format($totalPrice, 0, ',', '.');
                                                            } else {
                                                                $formatted = number_format($totalPrice, 4, ',', '.');
                                                                $formattedTotalPrice = rtrim(rtrim($formatted, '0'), ',');
                                                            }
                                                        @endphp
                                                        {{ $formattedTotalPrice }}
                                                    </td>
                                                    <td>{{ $product->note ?? '-' }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted py-4">
                                                        <i class="icon-base ti tabler-package me-2"></i>
                                                        Tidak ada data produk
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                        @if (count($invoice_products) > 0)
                                            <tfoot class="table-light">
                                                <tr>
                                                    <th colspan="4" class="text-end">Total Invoice:</th>
                                                    <th class="text-end">
                                                        @php
                                                            $totalInvoice = $invoice_products->sum('total_price') ?? 0;
                                                            if ($totalInvoice == floor($totalInvoice)) {
                                                                $formattedTotalInvoice = number_format($totalInvoice, 0, ',', '.');
                                                            } else {
                                                                $formatted = number_format($totalInvoice, 4, ',', '.');
                                                                $formattedTotalInvoice = rtrim(rtrim($formatted, '0'), ',');
                                                            }
                                                        @endphp
                                                        Rp {{ $formattedTotalInvoice }}
                                                    </th>
                                                    <th></th>
                                                </tr>
                                            </tfoot>
                                        @endif
                                    </table>
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
                                        @forelse($auditLogs as $log)
                                            <tr>
                                                <td>{{ $log->created_at ? date('d/m/Y H:i:s', strtotime($log->created_at)) : '-' }}
                                                </td>
                                                <td>
                                                    @if ($canViewUser && $log->user_id)
                                                        <a href="{{ route('master_data.user.detail', $log->user_id) }}"
                                                            class="text-primary fw-semibold">
                                                            {{ $log->user_name ?? 'System' }}
                                                        </a>
                                                    @else
                                                        {{ $log->user_name ?? 'System' }}
                                                    @endif
                                                </td>
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
                                                <td>{{ $log->description ?? '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-4">
                                                    <i class="icon-base ti tabler-history me-2"></i>
                                                    Belum ada riwayat perubahan
                                                </td>
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
        $status = $invoice_data->status ?? null;
        $statusMeta = match ($status) {
            'PAID', 'SETTLED', 'COMPLETED' => ['label' => 'Lunas', 'class' => 'success'],
            'PARTIAL_PAID', 'PARTIALLY_PAID' => ['label' => 'Dibayar Sebagian', 'class' => 'info'],
            'UNPAID', 'PENDING' => ['label' => 'Belum Dibayar', 'class' => 'warning'],
            'OVERDUE' => ['label' => 'Lewat Jatuh Tempo', 'class' => 'danger'],
            'DRAFT' => ['label' => 'Draft', 'class' => 'secondary'],
            'CANCELLED' => ['label' => 'Dibatalkan', 'class' => 'danger'],
            default => ['label' => $humanStatus($status), 'class' => 'secondary'],
        };
        $paymentTerm = collect([$invoice_data->payment_term_code ?? null, $invoice_data->payment_term_name ?? null])
            ->filter()
            ->implode(' - ');
        $paymentTerm = $paymentTerm ?: ($invoice_data->payment_terms ?? '-');
        $validTags = collect($invoice_data->tags ?? [])->filter(fn($tag) => is_object($tag) && !empty($tag->code));
        $grandTotal = (float) ($invoice_data->grand_total ?? 0);
        $paidAmount = (float) ($invoice_data->paid_amount ?? $invoice_data->total_paid ?? (in_array($status, ['PAID', 'SETTLED', 'COMPLETED']) ? $grandTotal : 0));
        $remainingAmount = (float) ($invoice_data->remaining_payment ?? max($grandTotal - $paidAmount, 0));
        if (in_array($status, ['PAID', 'SETTLED', 'COMPLETED'])) {
            $remainingAmount = 0;
        }
    @endphp

    <div class="compact-detail">
        <div class="detail-card mb-4">
            <div class="detail-header d-flex flex-wrap align-items-start justify-content-between gap-3">
                <div>
                    <div class="detail-breadcrumb">
                        <a href="{{ route('main_transaction.purchase_order.index') }}">Pembelian</a>
                        <i class="icon-base ti tabler-chevron-right"></i>
                        <span>Penagihan</span>
                        <i class="icon-base ti tabler-chevron-right"></i>
                        <span>Detail</span>
                    </div>
                    <h1 class="detail-title">Detail Penagihan Pembelian</h1>
                </div>
                <div class="detail-actions d-flex flex-wrap justify-content-end gap-2">
                    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="window.print()">
                        <i class="icon-base ti tabler-printer me-2"></i>Print
                    </button>
                    @if ($hasCompletedDelivery && !$returnData)
                        <a href="{{ route('main_transaction.return_management.add') }}?return_type=PURCHASE_RETURN&source_invoice_id={{ $invoice_data->id }}"
                            class="btn btn-warning waves-effect waves-light">
                            <i class="icon-base ti tabler-arrow-back-up me-2"></i>Retur
                        </a>
                    @endif
                </div>
            </div>

            <div class="summary-grid">
                <div class="summary-item">
                    <span class="summary-label">Penagihan Pembelian</span>
                    <div class="summary-code">{{ $invoice_data->transaction_code }}</div>
                    <div class="summary-note">
                        Dibuat pada {{ $formatDate($invoice_data->created_at ?? null, true) }}
                        @if (!empty($invoice_data->created_by_name))
                            oleh {{ $invoice_data->created_by_name }}
                        @endif
                    </div>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Status Penagihan</span>
                    <span class="badge bg-label-{{ $statusMeta['class'] }} fs-6 py-2 px-3">
                        <i class="icon-base ti tabler-check me-1"></i>{{ $statusMeta['label'] }}
                    </span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Syarat Pembayaran</span>
                    <span class="badge bg-label-primary fs-6 py-2 px-3">
                        <i class="icon-base ti tabler-check me-1"></i>{{ $paymentTerm }}
                    </span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Sisa Tagihan</span>
                    <div class="summary-money">{{ $formatCurrency($remainingAmount) }}</div>
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
                            <div class="info-value">{{ $invoice_data->supplier_name ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="info-icon icon-base ti tabler-map-pin"></i>
                        <div>
                            <span class="info-label">Alamat Supplier</span>
                            <div class="info-value">{{ $invoice_data->supplier_address ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="info-icon icon-base ti tabler-calendar"></i>
                        <div>
                            <span class="info-label">Tanggal Penagihan</span>
                            <div class="info-value">{{ $formatDate($invoice_data->invoice_date ?? $invoice_data->transaction_date ?? null) }}</div>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="info-icon icon-base ti tabler-calendar-due"></i>
                        <div>
                            <span class="info-label">Jatuh Tempo</span>
                            <div class="info-value">{{ $formatDate($invoice_data->due_date ?? null) }}</div>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="info-icon icon-base ti tabler-file-text"></i>
                        <div>
                            <span class="info-label">No. Transaksi</span>
                            <div class="info-value">{{ $invoice_data->transaction_code ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="info-icon icon-base ti tabler-file-symlink"></i>
                        <div>
                            <span class="info-label">No. Pesanan</span>
                            <div class="info-value">
                                @if ($canViewPurchaseOrder && $invoice_data->po_id)
                                    <a href="{{ route('main_transaction.purchase_order.detail', $invoice_data->po_id) }}">
                                        {{ $invoice_data->po_reference ?? '-' }}
                                    </a>
                                @else
                                    {{ $invoice_data->po_reference ?? '-' }}
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="info-icon icon-base ti tabler-home"></i>
                        <div>
                            <span class="info-label">Gudang</span>
                            <div class="info-value">
                                {{ trim(($invoice_data->warehouse_code ?? '') . ' ' . ($invoice_data->warehouse_name ?? '')) ?: '-' }}
                            </div>
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
                        <i class="info-icon icon-base ti tabler-file-dollar"></i>
                        <div>
                            <span class="info-label">Grand Total PO</span>
                            <div class="info-value">{{ $formatCurrency($invoice_data->po_grand_total ?? 0) }}</div>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="info-icon icon-base ti tabler-user-check"></i>
                        <div>
                            <span class="info-label">Dibuat Oleh</span>
                            <div class="info-value">{{ $invoice_data->created_by_name ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="detail-card">
            <div class="detail-section">
                <h2 class="detail-section-title">Informasi Produk</h2>
                <div class="compact-table table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th class="text-end">Qty Pesanan</th>
                                <th class="text-end">Qty Penagihan</th>
                                <th>Satuan</th>
                                <th class="text-end">Harga Satuan</th>
                                <th class="text-end">Jumlah</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoice_products as $product)
                                <tr>
                                    <td>
                                        @if ($canViewProduct)
                                            <a href="{{ route('master_data.product.detail', $product->f_product_id) }}" class="fw-semibold">
                                                {{ $product->product_code ?? '' }} - {{ $product->product_name ?? '' }}
                                            </a>
                                        @else
                                            {{ $product->product_code ?? '' }} - {{ $product->product_name ?? '' }}
                                        @endif
                                    </td>
                                    <td class="text-end">{{ $formatQty($product->ordered_qty ?? 0) }}</td>
                                    <td class="text-end">{{ $formatQty($product->qty ?? 0) }}</td>
                                    <td>{{ $product->unit_name ?? $product->unit_code ?? '-' }}</td>
                                    <td class="text-end">{{ $formatCurrency($product->unit_price ?? 0) }}</td>
                                    <td class="text-end fw-semibold">{{ $formatCurrency($product->total_price ?? 0) }}</td>
                                    <td>{{ $product->product_invoice_note ?? $product->note ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="empty-row">Tidak ada produk penagihan</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="totals-panel">
                    <div class="total-line">
                        <span>Subtotal</span>
                        <strong>{{ $formatCurrency($invoice_data->subtotal ?? 0) }}</strong>
                    </div>
                    <div class="total-line">
                        <span>Total Pajak</span>
                        <strong>{{ $formatCurrency($invoice_data->total_tax ?? 0) }}</strong>
                    </div>
                    <div class="total-line">
                        <span>Total Diskon</span>
                        <strong>{{ $formatCurrency($invoice_data->total_discount ?? 0) }}</strong>
                    </div>
                    <div class="total-line">
                        <strong>Total</strong>
                        <strong>{{ $formatCurrency($grandTotal) }}</strong>
                    </div>
                    <div class="total-line">
                        <span>Jumlah Terbayar</span>
                        <strong>{{ $formatCurrency($paidAmount) }}</strong>
                    </div>
                    <div class="total-line total-highlight">
                        <strong>Sisa Tagihan</strong>
                        <strong>{{ $formatCurrency($remainingAmount) }}</strong>
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
                        <div class="info-value">{{ $invoice_data->description ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <span class="info-label">Memo Internal</span>
                        <div class="info-value">{{ $invoice_data->internal_memo ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <span class="info-label">Lampiran</span>
                        @if(!empty($invoice_data->attachment))
                            <a href="{{ $invoice_data->attachment }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="icon-base ti tabler-download me-1"></i>Download
                            </a>
                        @else
                            <div class="info-value">-</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if ($returnData)
            <div class="detail-card">
                <div class="detail-section">
                    <h2 class="detail-section-title">Informasi Retur</h2>
                    <div class="info-grid">
                        <div class="info-item">
                            <i class="info-icon icon-base ti tabler-arrow-back-up"></i>
                            <div>
                                <span class="info-label">No. Retur</span>
                                <div class="info-value">
                                    @if ($canViewReturnManagement)
                                        <a href="{{ route('main_transaction.return_management.detail', $returnData->id) }}">
                                            {{ $returnData->transaction_code }}
                                        </a>
                                    @else
                                        {{ $returnData->transaction_code }}
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="info-icon icon-base ti tabler-calendar"></i>
                            <div>
                                <span class="info-label">Tanggal Retur</span>
                                <div class="info-value">{{ $formatDate($returnData->return_date ?? null) }}</div>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="info-icon icon-base ti tabler-file-dollar"></i>
                            <div>
                                <span class="info-label">Total Retur</span>
                                <div class="info-value">{{ $formatCurrency($returnData->grand_total ?? 0) }}</div>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="info-icon icon-base ti tabler-flag"></i>
                            <div>
                                <span class="info-label">Status Retur</span>
                                <div class="info-value">{{ $humanStatus($returnData->status ?? null) }}</div>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="info-icon icon-base ti tabler-note"></i>
                            <div>
                                <span class="info-label">Alasan Retur</span>
                                <div class="info-value">{{ $returnData->return_reason ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="detail-card">
            <div class="detail-section">
                <ul class="nav nav-tabs compact-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#purchase-invoice-audit-history" type="button" role="tab">
                            Log Audit
                        </button>
                    </li>
                </ul>

                <div class="tab-content pt-4 px-0">
                    <div class="tab-pane fade show active" id="purchase-invoice-audit-history" role="tabpanel">
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
                                            <td>{{ $log->user_name ?? 'System' }}</td>
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
            // Audit Log DataTable
            $('#audit-log-table').DataTable({
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
