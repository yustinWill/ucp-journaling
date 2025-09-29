@extends('layouts.default')
@section('page_title')
    Detail Sales Order - {{ $salesOrder['transaction_code'] }}
@stop
@section('content')
    <div class="card mb-6">
        <div class="card-body">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <h4 class="mb-3">Detail Sales Order</h4>
                </div>
                <div class="col-md-4">
                    <!-- Show buttons directly -->
                    <div class="d-flex gap-2 justify-content-end">
                        <button type="button" class="btn btn-primary btn-md waves-effect waves-light"
                            onclick="window.print()">
                            <i class="icon-base ti tabler-printer me-2"></i>Print
                        </button>
                        @if ($canCreateSalesDelivery && !in_array($salesOrder['status'], ['DRAFT', 'CANCELLED']))
                            <a href="{{ route('main_transaction.sales_delivery.add', ['so_id' => $salesOrder['id']]) }}"
                                class="btn btn-warning btn-md waves-effect waves-light">
                                <i class="icon-base ti tabler-truck-delivery me-2"></i>Buat Delivery
                            </a>
                        @endif
                        @if ($canCreateSalesInvoice && !in_array($salesOrder['status'], ['DRAFT', 'CANCELLED']))
                            <a href="{{ route('main_transaction.sales_invoice.add', ['so_id' => $salesOrder['id']]) }}"
                                class="btn btn-info btn-md waves-effect waves-light">
                                <i class="icon-base ti tabler-file-invoice me-2"></i>Buat Invoice
                            </a>
                        @endif
                        @if ($canEdit)
                            <a href="{{ route('main_transaction.sales_order.edit', $salesOrder['id']) }}"
                                class="btn btn-success btn-md waves-effect waves-light">
                                <i class="icon-base ti tabler-edit me-2"></i>Edit
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sales Order Title -->
            <h2>{{ $salesOrder['transaction_code'] }}</h2>
            <span>
                <h6>Data sales order berdasarkan tanggal {{ $salesOrder['created_at'] }}</h6>
            </span>
            <hr class="my-6 mx-n6" />

            <!-- Informasi Dasar Transaksi Section - Outside tabs, on top -->

            <!-- Row 1: Tanggal Transaksi, Nomor Transaksi -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tanggal Transaksi</label>
                    <input type="text" class="form-control" value="{{ $salesOrder['transaction_date'] }}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nomor Transaksi</label>
                    <input type="text" class="form-control" value="{{ $salesOrder['transaction_code'] }}" disabled />
                </div>
            </div>

            <hr class="my-6 mx-n6" />

            <!-- Tabs Section -->
            <div class="col-lg-12">
                <div class="demo-inline-spacing mt-4">
                    <div class="list-group list-group-horizontal-md text-md-center" role="tablist">
                        <a class="list-group-item list-group-item-action waves-effect active"
                            id="sales-order-info-list-item" data-bs-toggle="list" href="#horizontal-sales-order-info"
                            aria-selected="true" role="tab">
                            <i class="icon-base ti tabler-info-circle me-2"></i>
                            <b>Informasi Detail</b>
                        </a>
                        <a class="list-group-item list-group-item-action waves-effect" id="delivery-invoice-list-item"
                            data-bs-toggle="list" href="#horizontal-delivery-invoice" aria-selected="false" role="tab">
                            <i class="icon-base ti tabler-truck-delivery me-2"></i>
                            <b>Sales Delivery & Invoice</b>
                        </a>
                        <a class="list-group-item list-group-item-action waves-effect" id="revenue-split-list-item"
                            data-bs-toggle="list" href="#horizontal-revenue-split" aria-selected="false" role="tab">
                            <i class="icon-base ti tabler-percentage me-2"></i>
                            <b>Revenue Split</b>
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
                        <div class="tab-pane fade show active" id="horizontal-sales-order-info" role="tabpanel">
                            <div id="sales-order-info-content">
                                <!-- Informasi Customer Section -->
                                <div class="section-header mt-12">
                                    <h6>Informasi Customer</h6>
                                    <hr class="mx-n6">
                                </div>

                                <!-- Row 1: Customer and Sales Person -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Customer</label>
                                        <input type="text" class="form-control" value="{{ $salesOrder['customer'] }}"
                                            disabled />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Sales Person</label>
                                        <input type="text" class="form-control"
                                            value="{{ $salesOrder['sales_person_name'] ?? '-' }}" disabled />
                                    </div>
                                </div>

                                <!-- Row 2: Email and Address -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Email</label>
                                        <input type="text" class="form-control"
                                            value="{{ $salesOrder['customer_email'] ?? '-' }}" disabled />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Alamat</label>
                                        <textarea class="form-control" rows="2" disabled>{{ $salesOrder['customer_address'] ?? '-' }}</textarea>
                                    </div>
                                </div>

                                <!-- Row 3: Customer Reference -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Nomor Referensi Customer</label>
                                        <input type="text" class="form-control"
                                            value="{{ $salesOrder['customer_reference_no'] ?? '-' }}" disabled />
                                    </div>
                                </div>

                                <!-- Sales Details Section -->
                                <div class="section-header mt-12">
                                    <h6>Detail Penjualan</h6>
                                    <hr class="mx-n6">
                                </div>

                                <!-- Row 1: Payment Terms -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Syarat Pembayaran</label>
                                        <input type="text" class="form-control"
                                            value="{{ $salesOrder['payment_terms'] ?? '-' }}" disabled />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Gudang</label>
                                        <input type="text" class="form-control"
                                            value="{{ $salesOrder['warehouse'] ?? '-' }}" disabled />
                                    </div>
                                </div>

                                <!-- Row 2: Days and Due Date (conditional) -->
                                @if (isset($salesOrder['payment_terms_type']) && in_array($salesOrder['payment_terms_type'], ['NET', 'CUSTOM']))
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-6">
                                            <label class="form-label">Jumlah Hari</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control"
                                                    value="{{ $salesOrder['payment_term_day_count'] ?? '0' }}" disabled>
                                                <span class="input-group-text">Hari</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Tanggal Jatuh Tempo</label>
                                            <input type="text" class="form-control"
                                                value="{{ $salesOrder['payment_term_due_date'] ?? '-' }}" disabled />
                                        </div>
                                    </div>
                                @endif

                                <!-- Row 3: Description -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Keterangan</label>
                                        <textarea class="form-control" rows="3" disabled>{{ $salesOrder['description'] ?? '-' }}</textarea>
                                    </div>
                                </div>

                                <!-- Product Lines Section -->
                                <div class="section-header mt-12">
                                    <h6>Informasi Produk</h6>
                                </div>

                                <!-- Product Table -->
                                <div class="mb-4 mx-n6">
                                    <table class="table table-bordered" id="product-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 30%;">Produk</th>
                                                <th style="width: 15%;">Kuantitas</th>
                                                <th style="width: 15%;">Satuan</th>
                                                <th style="width: 20%;">Harga Satuan</th>
                                                <th style="width: 20%;">Jumlah</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (isset($salesOrder['product_lines']) && count($salesOrder['product_lines']) > 0)
                                                @foreach ($salesOrder['product_lines'] as $line)
                                                    <tr>
                                                        <td>
                                                            <input type="text" class="form-control"
                                                                value="{{ $line['product_code'] }} - {{ $line['product_name'] }}"
                                                                disabled />
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control text-end"
                                                                value="{{ number_format($line['quantity'], 2) }}"
                                                                disabled />
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control"
                                                                value="{{ $line['unit_name'] }}" disabled />
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control text-end"
                                                                value="Rp {{ number_format($line['unit_price'], 0, ',', '.') }}"
                                                                disabled />
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control text-end"
                                                                value="Rp {{ number_format($line['line_total'], 0, ',', '.') }}"
                                                                disabled />
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted py-3">Tidak ada
                                                        produk</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Financial Summary & Additional Information Section -->
                                <div class="section-header mt-12">
                                    <h6>Informasi Tambahan & Ringkasan</h6>
                                    <hr class="mx-n6">
                                </div>

                                <div class="row">
                                    <!-- Left Side: Additional Info -->
                                    <div class="col-md-6 mb-4">
                                        <div class="row g-3 mb-4">
                                            <div class="col-md-12">
                                                <label class="form-label">Tag</label>
                                                @if (isset($salesOrder['tags']) && count($salesOrder['tags']) > 0)
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @foreach ($salesOrder['tags'] as $tag)
                                                            <span class="badge bg-primary">{{ $tag }}</span>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <input type="text" class="form-control" value="-" disabled />
                                                @endif
                                            </div>
                                        </div>

                                        <div class="row g-3 mb-4">
                                            <div class="col-md-12">
                                                <label class="form-label">Memo Internal</label>
                                                <textarea class="form-control" rows="3" disabled>{{ $salesOrder['internal_memo'] ?? '-' }}</textarea>
                                            </div>
                                        </div>

                                        <div class="row g-3 mb-4">
                                            <div class="col-md-12">
                                                <label class="form-label">Lampiran</label>
                                                @if ($salesOrder['attachment'])
                                                    <div>
                                                        <a href="{{ Storage::url($salesOrder['attachment']) }}"
                                                            target="_blank" class="btn btn-outline-primary btn-sm">
                                                            <i class="icon-base ti tabler-download me-1"></i>Download
                                                        </a>
                                                    </div>
                                                @else
                                                    <input type="text" class="form-control" value="Tidak ada lampiran"
                                                        disabled />
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Right Side: Financial Summary -->
                                    <div class="col-md-6">
                                        <div class="summary-section ps-4">
                                            <div class="row mb-3">
                                                <div class="col-6">
                                                    <label class="form-label"><strong>Grand Total</strong></label>
                                                </div>
                                                <div class="col-6">
                                                    <input type="text" class="form-control readonly-field fw-bold"
                                                        dir="rtl"
                                                        value="Rp {{ number_format($salesOrder['grand_price'] ?? 0, 0, ',', '.') }}"
                                                        disabled>
                                                </div>
                                            </div>

                                            {{-- <div class="row mb-3">
                                                <div class="col-6">
                                                    <label class="form-label">Status</label>
                                                </div>
                                                <div class="col-6">
                                                    <span
                                                        class="badge bg-label-{{ $salesOrder['status'] == 'PENDING' ? 'warning' : ($salesOrder['status'] == 'COMPLETED' ? 'success' : 'secondary') }} w-100 text-center py-2">
                                                        {{ $salesOrder['status'] }}
                                                    </span>
                                                </div>
                                            </div> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Delivery & Invoice Tab -->
                        <div class="tab-pane fade" id="horizontal-delivery-invoice" role="tabpanel">
                            <!-- Delivery Summary Section -->
                            <div class="section-header mt-6">
                                <h6>Ringkasan Pengiriman</h6>
                            </div>

                            <div class="mx-n5 my-4">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Produk</th>
                                            <th>Total Qty</th>
                                            <th>Qty Terkirim</th>
                                            <th>Sisa Qty</th>
                                            <th>Satuan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (isset($salesOrder['product_lines']) && count($salesOrder['product_lines']) > 0)
                                            @foreach ($salesOrder['product_lines'] as $line)
                                                <tr>
                                                    <td>{{ $line['product_code'] }} - {{ $line['product_name'] }}</td>
                                                    <td class="text-end">{{ number_format($line['quantity'], 2) }}</td>
                                                    <td class="text-end">{{ number_format($line['delivered_qty'], 2) }}
                                                    </td>
                                                    <td class="text-end">{{ number_format($line['remaining_qty'], 2) }}
                                                    </td>
                                                    <td>{{ $line['unit_name'] }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-3">Tidak ada produk
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>

                            <!-- Sales Delivery Section -->
                            <div class="section-header mt-6">
                                <h6>Sales Delivery</h6>
                            </div>

                            <div class="mx-n5 my-4">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>No. Referensi</th>
                                            <th>Deskripsi</th>
                                            <th>Dibuat Oleh</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($salesDeliveries as $item)
                                            <tr>
                                                <td>{{ $item->delivery_date }}</td>
                                                <td>{{ $item->delivery_number }}</td>
                                                <td>{{ $item->memo }}</td>
                                                <td>{{ $item->created_by_name }}</td>
                                                <td>
                                                    <a href="{{ route('main_transaction.sales_delivery.detail', $item->id) }}"
                                                        class="btn btn-sm btn-primary waves-effect waves-light"
                                                        title="Detail">
                                                        <i class="icon-xs icon-base ti tabler-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                        @if (count($salesDeliveries) == 0)
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-4">
                                                    <div class="d-flex flex-column align-items-center">
                                                        <i class="icon-base ti tabler-truck-delivery mb-2 text-muted"
                                                            style="font-size: 2rem;"></i>
                                                        <span class="mb-2">Belum ada Sales Delivery yang dibuat</span>
                                                        @if ($canCreateSalesDelivery && !in_array($salesOrder['status'], ['DRAFT', 'CANCELLED']))
                                                            <a href="{{ route('main_transaction.sales_delivery.add', ['so_id' => $salesOrder['id']]) }}"
                                                                class="btn btn-warning btn-sm">
                                                                <i class="icon-base ti tabler-truck-delivery me-1"></i>Buat
                                                                Sales Delivery
                                                            </a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>

                            <!-- Sales Invoice Section -->
                            <div class="section-header mt-6">
                                <h6>Sales Invoice</h6>
                            </div>

                            <div class="mx-n5 my-4">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>No. Referensi</th>
                                            <th>Jumlah</th>
                                            <th>Deskripsi</th>
                                            <th>Dibuat Oleh</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($salesInvoices as $item)
                                            <tr>
                                                <td>{{ $item->transaction_date }}</td>
                                                <td>{{ $item->transaction_code }}</td>
                                                <td>{{ $item->grand_total }}</td>
                                                <td>{{ $item->description }}</td>
                                                <td>{{ $item->created_by_name }}</td>
                                                {{-- <td>
                                                    <div class="btn-group">
                                                        <a href="{{ route('main_transaction.sales_invoice.edit', ['id' => $item->id]) }}"
                                                            class="btn btn-sm btn-primary">
                                                            <i class="icon-base ti tabler-pencil me-1"></i>Edit
                                                        </a>
                                                        <form action="{{ route('main_transaction.sales_invoice.delete', ['id' => $item->id]) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger"
                                                                onclick="return confirm('Are you sure?')">
                                                                <i class="icon-base ti tabler-trash me-1"></i>Delete
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td> --}}
                                                <td>
                                                    <a href="{{ route('main_transaction.sales_invoice.detail', $item->id) }}"
                                                        class="btn btn-sm btn-primary waves-effect waves-light"
                                                        title="Detail">
                                                        <i class="icon-xs icon-base ti tabler-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                        @if (count($salesInvoices) == 0)
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">
                                                <div class="d-flex flex-column align-items-center">
                                                    <i class="icon-base ti tabler-file-invoice mb-2 text-muted"
                                                        style="font-size: 2rem;"></i>
                                                    <span class="mb-2">Belum ada Sales Invoice yang dibuat</span>
                                                    @if ($canCreateSalesInvoice && !in_array($salesOrder['status'], ['DRAFT', 'CANCELLED']))
                                                        <a href="{{ route('main_transaction.sales_invoice.add', ['so_id' => $salesOrder['id']]) }}"
                                                            class="btn btn-info btn-sm">
                                                            <i class="icon-base ti tabler-file-invoice me-1"></i>Buat Sales
                                                            Invoice
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @endif
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Revenue Split Tab -->
                        <div class="tab-pane fade" id="horizontal-revenue-split" role="tabpanel">
                            <div class="section-header mt-12">
                                <h6>Revenue Split Breakdown</h6>
                            </div>
                            
                            <div id="revenue-split-content">
                                <div class="text-center py-4">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2">Memuat revenue split breakdown...</p>
                                </div>
                            </div>
                        </div>

                        <!-- Return History Tab -->
                        <div class="tab-pane fade" id="horizontal-return-history" role="tabpanel">
                            <div class="section-header mt-6">
                                <h6>Daftar Sales Return</h6>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered" id="sales-return-table">
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
                                        @foreach ($sales_returns as $return)
                                            <tr>
                                                <td>{{ $return['return_date'] }}</td>
                                                <td>
                                                    @if ($canViewReturnManagement)
                                                        <a href="{{ route('main_transaction.return_management.detail', $return['id']) }}"
                                                            class="text-primary fw-semibold">
                                                            {{ $return['transaction_code'] }}
                                                        </a>
                                                    @else
                                                        {{ $return['transaction_code'] }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($canViewSalesInvoice && $return['f_source_invoice_id'])
                                                        <a href="{{ route('main_transaction.sales_invoice.detail', $return['f_source_invoice_id']) }}"
                                                            class="text-primary fw-semibold">
                                                            {{ $return['source_invoice_code'] }}
                                                        </a>
                                                    @else
                                                        {{ $return['source_invoice_code'] }}
                                                    @endif
                                                </td>
                                                <td class="text-end">Rp
                                                    {{ number_format($return['grand_total'], 0, ',', '.') }}</td>
                                                <td class="text-start">
                                                    <span
                                                        class="badge {{ $return['status_badge_class'] }}">{{ $return['status'] }}</span>
                                                </td>
                                                <td class="text-start">
                                                    <span
                                                        class="badge {{ $return['stock_adjustment_status_badge_class'] }}">
                                                        @if ($return['stock_adjustment_status'] === 'ADJUSTED')
                                                            Sudah Disesuaikan
                                                        @else
                                                            Belum Disesuaikan
                                                        @endif
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    @if ($canViewReturnManagement)
                                                        <a href="{{ route('main_transaction.return_management.detail', $return['id']) }}"
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

                        <!-- Audit Log Tab -->
                        <div class="tab-pane fade" id="horizontal-audit-log" role="tabpanel">
                            <div class="section-header mt-6">
                                <h6>Riwayat Perubahan</h6>
                            </div>

                            <div class="table-responsive">
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
                                        @if (isset($audit_logs) && count($audit_logs) > 0)
                                            @foreach ($audit_logs as $log)
                                                <tr>
                                                    <td>{{ $log->date }}</td>
                                                    <td>
                                                        @if (isset($canViewUser) && $canViewUser)
                                                            <a href="#"
                                                                class="text-primary fw-semibold">{{ $log->user_name }}</a>
                                                        @else
                                                            {{ $log->user_name }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($log->action === 'CREATE')
                                                            <span class="badge bg-label-success">CREATE</span>
                                                        @elseif($log->action === 'UPDATE')
                                                            <span class="badge bg-label-warning">UPDATE</span>
                                                        @elseif($log->action === 'DELETE')
                                                            <span class="badge bg-label-danger">DELETE</span>
                                                        @else
                                                            <span class="badge bg-label-info">{{ $log->action }}</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $log->description }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
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

@section('js_script')
    <script>
        $(document).ready(function() {
            // Initialize audit log DataTable to match PO pattern
            $('#audit-log-table').DataTable({
                orderCellsTop: true,
                order: [
                    [0, 'desc']
                ], // Sort by date descending
                searching: false,
                paging: false,
                info: false
            });

            // Initialize Sales Return Table
            $('#sales-return-table').DataTable({
                orderCellsTop: true,
                ordering: false,
                searching: false,
                paging: false,
                info: false
            });

            // Revenue Split Tab Loading
            let revenueSplitLoaded = false;
            $('#revenue-split-list-item').on('click', function() {
                if (!revenueSplitLoaded) {
                    loadRevenueSplit();
                }
            });
        });

        // Load Revenue Split Function
        function loadRevenueSplit() {
            const salesOrderId = {{ $salesOrder->id }};
            
            $.ajax({
                url: `/main_transaction/sales_order/revenue-split/${salesOrderId}/breakdown`,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        $('#revenue-split-content').html(response.html);
                        revenueSplitLoaded = true;
                        
                        // Initialize currency formatting for revenue split inputs
                        initializeCurrencyFormatting();
                    } else {
                        $('#revenue-split-content').html(`
                            <div class="alert alert-danger">
                                <i class="icon-base ti tabler-alert-circle me-2"></i>
                                ${response.message}
                            </div>
                        `);
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Terjadi kesalahan saat memuat revenue split breakdown';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    $('#revenue-split-content').html(`
                        <div class="alert alert-danger">
                            <i class="icon-base ti tabler-alert-circle me-2"></i>
                            ${errorMessage}
                        </div>
                    `);
                }
            });
        }

        // Refresh Revenue Split Function
        function refreshRevenueSplit() {
            revenueSplitLoaded = false;
            $('#revenue-split-content').html(`
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Memuat revenue split breakdown...</p>
                </div>
            `);
            loadRevenueSplit();
        }

        // Edit Revenue Split Function
        function editRevenueSplit(productId, productName, splitData) {
            // Populate modal with data
            $('#editProductId').val(productId);
            $('#editProductName').text(productName);
            $('#editProductTotal').val(splitData.product_total_amount);
            $('#editTotalDisplay').text('Rp ' + formatNumber(splitData.product_total_amount));
            
            // Set split amounts
            $('#editPorsiPabrik').val(formatNumber(splitData.porsi_pabrik));
            $('#editPorsiOwner').val(formatNumber(splitData.porsi_owner)); 
            $('#editPorsiPerusahaan').val(formatNumber(splitData.porsi_perusahaan));
            
            // Set remaining allocation radio
            $(`input[name="edit_remaining_allocation"][value="${splitData.remaining_allocation}"]`).prop('checked', true);
            
            // Set notes
            $('#editSplitNotes').val(splitData.split_notes || '');
            
            // Calculate totals
            calculateEditSplitTotals();
            
            // Show modal
            $('#editRevenueSplitModal').modal('show');
        }

        // Reset Revenue Split Function
        function resetRevenueSplit(productId, productName) {
            if (confirm(`Apakah Anda yakin ingin reset revenue split untuk produk "${productName}" ke konfigurasi default?`)) {
                const salesOrderId = {{ $salesOrder->id }};
                
                $.ajax({
                    url: `/main_transaction/sales_order/revenue-split/${salesOrderId}/product/${productId}/reset`,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            refreshRevenueSplit();
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Terjadi kesalahan saat reset revenue split';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        toastr.error(errorMessage);
                    }
                });
            }
        }

        // Initialize Currency Formatting
        function initializeCurrencyFormatting() {
            // Currency input formatting for edit modal
            $('.currency-input').on('input', function() {
                let value = $(this).val().replace(/[^\d]/g, '');
                $(this).val(formatNumber(value));
                calculateEditSplitTotals();
            });

            // Handle edit revenue split form submission
            $('#editRevenueSplitForm').on('submit', function(e) {
                e.preventDefault();
                saveRevenueSplit();
            });
        }

        // Calculate Edit Split Totals
        function calculateEditSplitTotals() {
            const pabrik = parseNumber($('#editPorsiPabrik').val()) || 0;
            const owner = parseNumber($('#editPorsiOwner').val()) || 0;
            const perusahaan = parseNumber($('#editPorsiPerusahaan').val()) || 0;
            const productTotal = parseNumber($('#editProductTotal').val()) || 0;
            
            const totalSplit = pabrik + owner + perusahaan;
            const difference = totalSplit - productTotal;
            
            $('#editTotalSplit').val(formatNumber(totalSplit));
            $('#editDifference').val(formatNumber(Math.abs(difference)));
            
            // Update difference styling
            if (Math.abs(difference) > 0.01) {
                $('#editDifference').removeClass('text-success').addClass('text-danger');
            } else {
                $('#editDifference').removeClass('text-danger').addClass('text-success');
            }
        }

        // Save Revenue Split
        function saveRevenueSplit() {
            const productId = $('#editProductId').val();
            const salesOrderId = {{ $salesOrder->id }};
            
            const formData = {
                porsi_pabrik: parseNumber($('#editPorsiPabrik').val()),
                porsi_owner: parseNumber($('#editPorsiOwner').val()),
                porsi_perusahaan: parseNumber($('#editPorsiPerusahaan').val()),
                remaining_allocation: $('input[name="edit_remaining_allocation"]:checked').val(),
                product_total_amount: parseNumber($('#editProductTotal').val()),
                split_notes: $('#editSplitNotes').val()
            };
            
            // Validate totals match
            const totalSplit = formData.porsi_pabrik + formData.porsi_owner + formData.porsi_perusahaan;
            if (Math.abs(totalSplit - formData.product_total_amount) > 0.01) {
                toastr.error('Total porsi harus sama dengan total produk');
                return;
            }
            
            $.ajax({
                url: `/main_transaction/sales_order/revenue-split/${salesOrderId}/product/${productId}`,
                type: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: formData,
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        $('#editRevenueSplitModal').modal('hide');
                        refreshRevenueSplit();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Terjadi kesalahan saat menyimpan revenue split';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    }
                    toastr.error(errorMessage);
                }
            });
        }

        // Utility Functions
        function formatNumber(value) {
            if (!value || value === 0) return '0';
            return parseInt(value).toLocaleString('id-ID');
        }

        function parseNumber(value) {
            if (!value) return 0;
            return parseInt(value.toString().replace(/[^\d]/g, '')) || 0;
        }
    </script>
@endsection
