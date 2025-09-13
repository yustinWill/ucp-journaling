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
                        <button type="button" class="btn btn-primary btn-md waves-effect waves-light" onclick="window.print()">
                            <i class="icon-base ti tabler-printer me-2"></i>Print
                        </button>
                        @if($canCreateSalesDelivery && !in_array($salesOrder['status'], ['DRAFT', 'CANCELLED']))
                            <a href="{{ route('main_transaction.sales_delivery.add', ['so_id' => $salesOrder['id']]) }}" class="btn btn-warning btn-md waves-effect waves-light">
                                <i class="icon-base ti tabler-truck-delivery me-2"></i>Buat Delivery
                            </a>
                        @endif
                        @if($canCreateSalesInvoice && !in_array($salesOrder['status'], ['DRAFT', 'CANCELLED']))
                            <a href="{{ route('main_transaction.sales_invoice.add', ['so_id' => $salesOrder['id']]) }}" class="btn btn-info btn-md waves-effect waves-light">
                                <i class="icon-base ti tabler-file-invoice me-2"></i>Buat Invoice
                            </a>
                        @endif
                        @if($canEdit)
                            <a href="{{ route('main_transaction.sales_order.edit', $salesOrder['id']) }}" class="btn btn-success btn-md waves-effect waves-light">
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
                        <a class="list-group-item list-group-item-action waves-effect active" id="sales-order-info-list-item"
                            data-bs-toggle="list" href="#horizontal-sales-order-info" aria-selected="true" role="tab">
                            <i class="icon-base ti tabler-info-circle me-2"></i>
                            <b>Informasi Detail</b>
                        </a>
                        <a class="list-group-item list-group-item-action waves-effect" id="delivery-invoice-list-item"
                            data-bs-toggle="list" href="#horizontal-delivery-invoice" aria-selected="false" role="tab">
                            <i class="icon-base ti tabler-truck-delivery me-2"></i>
                            <b>Sales Delivery & Invoice</b>
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
                                        <input type="text" class="form-control" value="{{ $salesOrder['customer'] }}" disabled />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Sales Person</label>
                                        <input type="text" class="form-control" value="{{ $salesOrder['sales_person_name'] ?? '-' }}" disabled />
                                    </div>
                                </div>

                                <!-- Row 2: Email and Address -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Email</label>
                                        <input type="text" class="form-control" value="{{ $salesOrder['customer_email'] ?? '-' }}" disabled />
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
                                        <input type="text" class="form-control" value="{{ $salesOrder['customer_reference_no'] ?? '-' }}" disabled />
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
                                        <input type="text" class="form-control" value="{{ $salesOrder['payment_terms'] ?? '-' }}" disabled />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Gudang</label>
                                        <input type="text" class="form-control" value="{{ $salesOrder['warehouse'] ?? '-' }}" disabled />
                                    </div>
                                </div>

                                <!-- Row 2: Days and Due Date (conditional) -->
                                @if(isset($salesOrder['payment_terms_type']) && in_array($salesOrder['payment_terms_type'], ['NET', 'CUSTOM']))
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Jumlah Hari</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ $salesOrder['payment_term_day_count'] ?? '0' }}" disabled>
                                            <span class="input-group-text">Hari</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Tanggal Jatuh Tempo</label>
                                        <input type="text" class="form-control" value="{{ $salesOrder['payment_term_due_date'] ?? '-' }}" disabled />
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
                                                <th style="width: 25%;">Produk</th>
                                                <th style="width: 12%;">Kuantitas</th>
                                                <th style="width: 10%;">Satuan</th>
                                                <th style="width: 18%;">Harga Satuan</th>
                                                <th style="width: 15%;">Pajak</th>
                                                <th style="width: 20%;">Jumlah</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($salesOrder['product_lines']) && count($salesOrder['product_lines']) > 0)
                                                @foreach($salesOrder['product_lines'] as $line)
                                                <tr>
                                                    <td>
                                                        <input type="text" class="form-control" value="{{ $line['product_code'] }} - {{ $line['product_name'] }}" disabled />
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control text-end" value="{{ number_format($line['quantity'], 2) }}" disabled />
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="{{ $line['unit_name'] }}" disabled />
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control text-end" value="Rp {{ number_format($line['unit_price'], 0, ',', '.') }}" disabled />
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="{{ $line['tax_name'] ?? 'Tanpa Pajak' }}" disabled />
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control text-end" value="Rp {{ number_format($line['line_total'], 0, ',', '.') }}" disabled />
                                                    </td>
                                                </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted py-3">Tidak ada produk</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Financial Summary & Additional Information Section -->
                                <div class="section-header mt-12">
                                    <h6>Ringkasan & Informasi Tambahan</h6>
                                    <hr class="mx-n6">
                                </div>

                                <div class="row">
                                    <!-- Left Side: Additional Info -->
                                    <div class="col-md-6 mb-4">
                                        <div class="row g-3 mb-4">
                                            <div class="col-md-12">
                                                <label class="form-label">Tag</label>
                                                @if(isset($salesOrder['tags']) && count($salesOrder['tags']) > 0)
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @foreach($salesOrder['tags'] as $tag)
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
                                                @if($salesOrder['attachment'])
                                                    <div>
                                                        <a href="{{ Storage::url($salesOrder['attachment']) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                                            <i class="icon-base ti tabler-download me-1"></i>Download
                                                        </a>
                                                    </div>
                                                @else
                                                    <input type="text" class="form-control" value="Tidak ada lampiran" disabled />
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Right Side: Financial Summary -->
                                    <div class="col-md-6">
                                        <div class="summary-section ps-4">
                                            <div class="row mb-3">
                                                <div class="col-6">
                                                    <label class="form-label">Subtotal</label>
                                                </div>
                                                <div class="col-6">
                                                    <input type="text" class="form-control readonly-field" dir="rtl" value="Rp {{ number_format($salesOrder['subtotal'] ?? 0, 0, ',', '.') }}" disabled>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-6">
                                                    <label class="form-label">Total Pajak</label>
                                                </div>
                                                <div class="col-6">
                                                    <input type="text" class="form-control readonly-field" dir="rtl" value="Rp {{ number_format($salesOrder['total_tax'] ?? 0, 0, ',', '.') }}" disabled>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-6">
                                                    <label class="form-label">Total Sebelum Diskon</label>
                                                </div>
                                                <div class="col-6">
                                                    <input type="text" class="form-control readonly-field" dir="rtl" value="Rp {{ number_format($salesOrder['total_price_before_discount'] ?? 0, 0, ',', '.') }}" disabled>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-6">
                                                    <label class="form-label">Diskon</label>
                                                </div>
                                                <div class="col-6">
                                                    <input type="text" class="form-control readonly-field" dir="rtl" value="Rp {{ number_format($salesOrder['discount'] ?? 0, 0, ',', '.') }}" disabled>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-6">
                                                    <label class="form-label"><strong>Grand Total</strong></label>
                                                </div>
                                                <div class="col-6">
                                                    <input type="text" class="form-control readonly-field fw-bold" dir="rtl" value="Rp {{ number_format($salesOrder['grand_price'] ?? 0, 0, ',', '.') }}" disabled>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-6">
                                                    <label class="form-label">Status</label>
                                                </div>
                                                <div class="col-6">
                                                    <span class="badge bg-label-{{ $salesOrder['status'] == 'PENDING' ? 'warning' : ($salesOrder['status'] == 'COMPLETED' ? 'success' : 'secondary') }} w-100 text-center py-2">
                                                        {{ $salesOrder['status'] }}
                                                    </span>
                                                </div>
                                            </div>
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
                                            <th style="width: 35%;">Produk</th>
                                            <th style="width: 13%;">Total Qty</th>
                                            <th style="width: 13%;">Qty Terkirim</th>
                                            <th style="width: 13%;">Sisa Qty</th>
                                            <th style="width: 10%;">Satuan</th>
                                            <th style="width: 16%;">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($salesOrder['product_lines']) && count($salesOrder['product_lines']) > 0)
                                            @foreach($salesOrder['product_lines'] as $line)
                                            <tr>
                                                <td>{{ $line['product_code'] }} - {{ $line['product_name'] }}</td>
                                                <td class="text-end">{{ number_format($line['quantity'], 2) }}</td>
                                                <td class="text-end">0.00</td>
                                                <td class="text-end">{{ number_format($line['quantity'], 2) }}</td>
                                                <td>{{ $line['unit_name'] }}</td>
                                                <td>
                                                    <span class="badge bg-label-secondary">PENDING</span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-3">Tidak ada produk</td>
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
                                            <th style="width: 15%;">Tanggal</th>
                                            <th style="width: 25%;">No. Referensi</th>
                                            <th style="width: 15%;">Status</th>
                                            <th style="width: 25%;">Dibuat Oleh</th>
                                            <th style="width: 20%;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">
                                                <div class="d-flex flex-column align-items-center">
                                                    <i class="icon-base ti tabler-truck-delivery mb-2 text-muted" style="font-size: 2rem;"></i>
                                                    <span class="mb-2">Belum ada Sales Delivery yang dibuat</span>
                                                    @if($canCreateSalesDelivery && !in_array($salesOrder['status'], ['DRAFT', 'CANCELLED']))
                                                        <a href="{{ route('main_transaction.sales_delivery.add', ['so_id' => $salesOrder['id']]) }}" class="btn btn-warning btn-sm">
                                                            <i class="icon-base ti tabler-truck-delivery me-1"></i>Buat Sales Delivery
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
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
                                            <th style="width: 15%;">Tanggal</th>
                                            <th style="width: 20%;">No. Referensi</th>
                                            <th style="width: 15%;">Jumlah</th>
                                            <th style="width: 15%;">Status</th>
                                            <th style="width: 20%;">Dibuat Oleh</th>
                                            <th style="width: 15%;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">
                                                <div class="d-flex flex-column align-items-center">
                                                    <i class="icon-base ti tabler-file-invoice mb-2 text-muted" style="font-size: 2rem;"></i>
                                                    <span class="mb-2">Belum ada Sales Invoice yang dibuat</span>
                                                    @if($canCreateSalesInvoice && !in_array($salesOrder['status'], ['DRAFT', 'CANCELLED']))
                                                        <a href="{{ route('main_transaction.sales_invoice.add', ['so_id' => $salesOrder['id']]) }}" class="btn btn-info btn-sm">
                                                            <i class="icon-base ti tabler-file-invoice me-1"></i>Buat Sales Invoice
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
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
                                        @if(isset($audit_logs) && count($audit_logs) > 0)
                                            @foreach($audit_logs as $log)
                                            <tr>
                                                <td>{{ $log->date }}</td>
                                                <td>
                                                    @if(isset($canViewUser) && $canViewUser)
                                                        <a href="#" class="text-primary fw-semibold">{{ $log->user_name }}</a>
                                                    @else
                                                        {{ $log->user_name }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($log->action === 'CREATE')
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
        order: [[0, 'desc']], // Sort by date descending
        searching: false,
        paging: false,
        info: false
    });
});
</script>
@endsection