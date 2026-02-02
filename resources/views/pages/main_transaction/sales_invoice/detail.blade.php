@extends('layouts.default')
@section('page_title')
    Detail Sales Invoice - {{ $invoice_data->transaction_code }}
@stop
@section('content')
    <div class="card mb-6">
        <div class="card-body">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <h4 class="mb-3">Detail Sales Invoice</h4>
                </div>
                <div class="col-md-4">
                    <!-- Show buttons directly -->
                    <div class="d-flex gap-2 justify-content-end">
                        {{-- Future: Invoice Settlement Button - will redirect to settlement page for payment processing --}}
                        {{-- Hide button if status is DRAFT or already PAID (fully settled) --}}
                        @if (!in_array($invoice_data->status, ['DRAFT', 'PAID']))
                            <a href="{{ route('main_transaction.sales_invoice.payment_recipient') }}?source_invoice_id={{ $invoice_data->id }}" class="btn btn-info btn-md waves-effect waves-light">
                                <i class="icon-base ti tabler-credit-card me-2"></i>
                                Settle Invoice
                            </a>
                        @endif

                        <a href="{{ route('main_transaction.sales_invoice.print', $invoice_data->id) }}"
                            class="btn btn-primary btn-md waves-effect waves-light"
                            target="_blank"
                            title="Print Invoice">
                            <i class="icon-base ti tabler-printer me-2"></i>Print
                        </a>

                        {{-- @if ($canEditSalesInvoice)
                            <a href="{{ route('main_transaction.sales_invoice.edit', $invoice_data->id) }}" class="btn btn-success btn-md waves-effect waves-light">
                                <i class="icon-base ti tabler-edit me-2"></i>Edit
                            </a>
                        @endif --}}

                        {{-- @if (in_array($invoice_data->status, ['PAID', 'UNPAID', 'PARTIAL_PAID'])) --}}
                            <a href="{{ route('main_transaction.return_management.add') }}?return_type=SALES_RETURN&source_invoice_id={{ $invoice_data->id }}"
                                class="btn btn-warning btn-md waves-effect waves-light">
                                <i class="icon-base ti tabler-arrow-back-up me-2"></i>Return
                            </a>
                        {{-- @endif --}}
                    </div>
                </div>
            </div>

            <!-- Sales Invoice Title -->
            <h2>{{ $invoice_data->transaction_code }}</h2>
            <span>
                <h6>Data sales invoice berdasarkan tanggal {{ $invoice_data->created_at }}</h6>
            </span>
            <hr class="my-6 mx-n6" />

            <!-- Informasi Dasar Transaksi Section - Outside tabs, on top -->

            <!-- Row 1: Tanggal Transaksi, Nomor Transaksi -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tanggal Transaksi</label>
                    <input type="text" class="form-control" value="{{ $invoice_data->invoice_date }}" disabled />
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
                            id="sales-invoice-info-list-item" data-bs-toggle="list" href="#horizontal-sales-invoice-info"
                            aria-selected="true" role="tab">
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
                        @if ($invoice_data->f_sales_order_id)
                            <a class="list-group-item list-group-item-action waves-effect" id="revenue-split-list-item"
                                data-bs-toggle="list" href="#horizontal-revenue-split" aria-selected="false" role="tab">
                                <i class="icon-base ti tabler-percentage me-2"></i>
                                <b>Revenue Split</b>
                            </a>
                        @endif
                    </div>
                    <div class="tab-content px-0 mt-0">
                        <!-- Information Tab -->
                        <div class="tab-pane fade show active" id="horizontal-sales-invoice-info" role="tabpanel">
                            <div id="sales-invoice-info-content">
                                <!-- Informasi Customer & Sales Order Section -->
                                <div class="section-header mt-12">
                                    <h6>Informasi Customer & Sales Order</h6>
                                    <hr class="mx-n6">
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Sales Order</label>
                                        @if ($canViewSalesOrder && $invoice_data->so_id)
                                            <div class="input-group">
                                                <input type="text" class="form-control"
                                                    value="{{ $invoice_data->so_reference ?? '-' }}" disabled />
                                                <a href="{{ route('main_transaction.sales_order.detail', $invoice_data->so_id) }}"
                                                    class="btn btn-outline-primary" title="Lihat Detail SO">
                                                    <i class="icon-base ti tabler-external-link"></i>
                                                </a>
                                            </div>
                                        @else
                                            <input type="text" class="form-control"
                                                value="{{ $invoice_data->so_reference ?? '-' }}" disabled />
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Customer</label>
                                        <input type="text" class="form-control"
                                            value="{{ $invoice_data->customer_name ?? '-' }}" disabled />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email Customer</label>
                                        <input type="text" class="form-control"
                                            value="{{ $invoice_data->customer_email ?? '-' }}" disabled />
                                    </div>
                                </div>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Alamat Customer</label>
                                        <textarea class="form-control" rows="2" disabled>{{ $invoice_data->customer_address ?? '-' }}</textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Status SO</label>
                                        <input type="text" class="form-control"
                                            value="{{ $invoice_data->so_status ?? '-' }}" disabled />
                                    </div>
                                </div>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Tanggal SO</label>
                                        <input type="text" class="form-control"
                                            value="{{ $invoice_data->so_transaction_date ? date('d/m/Y', strtotime($invoice_data->so_transaction_date)) : '-' }}"
                                            disabled />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Grand Total SO</label>
                                        <input type="text" class="form-control"
                                            value="{{ $invoice_data->so_grand_price ? 'Rp ' . number_format($invoice_data->so_grand_price, 0, ',', '.') : '-' }}"
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
                                    {{-- <div class="col-md-6">
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
                                    </div> --}}
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
                                    <h6>Informasi Tambahan</h6>
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

                                <!-- Informasi Sistem Section -->
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
                                                <th>Produk</th>
                                                <th>Qty SO</th>
                                                <th>Qty Invoice</th>
                                                <th>Unit Price</th>
                                                <th>Total</th>
                                                <th>Note</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($invoice_products as $product)
                                                <tr>
                                                    <td><strong>{{ $product->product_code ?? '' }} - {{ $product->product_name ?? '' }}</strong></td>
                                                    <td class="text-center">
                                                        @php
                                                            $orderedQty = $product->ordered_qty;
                                                            if ($orderedQty == floor($orderedQty)) {
                                                                $formattedOrderedQty = number_format($orderedQty, 0, ',', '.');
                                                            } else {
                                                                $formatted = number_format($orderedQty, 4, ',', '.');
                                                                $formattedOrderedQty = rtrim(rtrim($formatted, '0'), ',');
                                                            }
                                                        @endphp
                                                        {{ $formattedOrderedQty }} {{ $product->unit_code ?? '' }}
                                                    </td>
                                                    <td class="text-center">
                                                        @php
                                                            $qty = $product->qty;
                                                            if ($qty == floor($qty)) {
                                                                $formattedQty = number_format($qty, 0, ',', '.');
                                                            } else {
                                                                $formatted = number_format($qty, 4, ',', '.');
                                                                $formattedQty = rtrim(rtrim($formatted, '0'), ',');
                                                            }
                                                        @endphp
                                                        <span class="badge bg-label-info">{{ $formattedQty }}</span>
                                                        {{ $product->unit_code ?? '' }}
                                                    </td>
                                                    <td class="text-end">
                                                        @php
                                                            $unitPrice = $product->unit_price ?? 0;
                                                            if ($unitPrice == floor($unitPrice)) {
                                                                $formattedUnitPrice = number_format($unitPrice, 0, ',', '.');
                                                            } else {
                                                                $formatted = number_format($unitPrice, 4, ',', '.');
                                                                $formattedUnitPrice = rtrim(rtrim($formatted, '0'), ',');
                                                            }
                                                        @endphp
                                                        Rp {{ $formattedUnitPrice }}
                                                    </td>
                                                    <td class="text-end">
                                                        @php
                                                            $lineTotal = $product->line_total ?? 0;
                                                            if ($lineTotal == floor($lineTotal)) {
                                                                $formattedLineTotal = number_format($lineTotal, 0, ',', '.');
                                                            } else {
                                                                $formatted = number_format($lineTotal, 4, ',', '.');
                                                                $formattedLineTotal = rtrim(rtrim($formatted, '0'), ',');
                                                            }
                                                        @endphp
                                                        <strong>Rp {{ $formattedLineTotal }}</strong>
                                                    </td>
                                                    <td>{{ $product->product_invoice_note ?? '-' }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted py-4">
                                                        <i class="icon-base ti tabler-package-off fs-1"></i>
                                                        <div class="mt-2">Tidak ada item dalam invoice ini</div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                        @if ($invoice_products->count() > 0)
                                            <tfoot class="table-light">
                                                <tr>
                                                    <th colspan="4" class="text-end">Total Invoice:</th>
                                                    <th class="text-end">
                                                        <span class="h5 text-primary">Rp
                                                            {{ number_format($invoice_data->grand_total ?? 0, 0, ',', '.') }}</span>
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
                                                    @if ($canViewUser && $log->created_by)
                                                        <a href="{{ route('master_data.user.detail', $log->created_by) }}"
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

                        <!-- Revenue Split Tab -->
                        @if ($invoice_data->f_sales_order_id)
                            <div class="tab-pane fade" id="horizontal-revenue-split" role="tabpanel">
                                <div class="section-header mt-6">
                                    <h6>Revenue Split per Produk</h6>
                                    <p class="text-muted mb-0">Masukkan pembagian revenue dalam Rupiah untuk setiap produk</p>
                                </div>

                                <div id="revenue-split-content" class="mt-4">
                                    <div class="text-center py-4">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="text-muted mt-2">Memuat data revenue split...</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js_script')
    <script>
        // Embed invoice products data as JSON
        const invoiceProductsData = @json($invoice_products);

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

            // Load Revenue Split when tab is clicked
            let revenueSplitLoaded = false;
            $('#revenue-split-list-item').on('click', function() {
                if (!revenueSplitLoaded) {
                    loadRevenueSplit();
                    revenueSplitLoaded = true;
                }
            });

            function loadRevenueSplit() {
                const salesOrderId = {{ $invoice_data->f_sales_order_id ?? 0 }};

                // Try to load existing revenue split first
                $.ajax({
                    url: `/main_transaction/sales_order/revenue-split/${salesOrderId}`,
                    method: 'GET',
                    success: function(response) {
                        if (response.success && response.data && response.data.splits && response.data.splits.length > 0) {
                            // Has existing splits - load the breakdown view
                            loadExistingBreakdown(salesOrderId);
                        } else {
                            // No splits yet - show input form
                            loadInputForm(salesOrderId);
                        }
                    },
                    error: function() {
                        // On error, try to show input form
                        loadInputForm(salesOrderId);
                    }
                });
            }

            function loadExistingBreakdown(salesOrderId) {
                $.ajax({
                    url: `/main_transaction/sales_order/revenue-split/${salesOrderId}/breakdown`,
                    method: 'GET',
                    success: function(response) {
                        if (response.success && response.html) {
                            $('#revenue-split-content').html(response.html);
                        }
                    }
                });
            }

            function loadInputForm(salesOrderId) {
                $('#revenue-split-content').html(`
                    <div id="emptyStateContainer">
                        <div class="text-center py-4">
                            <i class="icon-base ti tabler-percentage icon-48px text-muted mb-3"></i>
                            <h6 class="text-muted">Belum Ada Revenue Split</h6>
                            <p class="text-muted mb-3">Klik tombol di bawah untuk menambahkan revenue split.</p>
                            <button type="button" class="btn btn-primary" onclick="showAddRevenueSplitFormInvoice()">
                                <i class="icon-base ti tabler-plus me-2"></i>Tambah Revenue Split
                            </button>
                        </div>
                    </div>

                    <div id="addRevenueSplitFormContainer" style="display: none;">
                        <div class="alert alert-info">
                            <i class="icon-base ti tabler-info-circle me-2"></i>
                            <strong>Petunjuk:</strong> Masukkan porsi revenue untuk setiap produk. Total porsi harus sama dengan total harga produk.
                        </div>

                        <form id="addRevenueSplitForm">
                            <div id="productSplitsList">
                                <!-- Products will be loaded here dynamically -->
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <button type="button" class="btn btn-secondary" onclick="hideAddRevenueSplitFormInvoice()">
                                    <i class="icon-base ti tabler-x me-2"></i>Batal
                                </button>
                                <button type="submit" class="btn btn-success">
                                    <i class="icon-base ti tabler-device-floppy me-2"></i>Simpan Semua
                                </button>
                            </div>
                        </form>
                    </div>
                `);
            }
        });

        // Show Add Revenue Split Form
        function showAddRevenueSplitFormInvoice() {
            $('#emptyStateContainer').hide();
            $('#addRevenueSplitFormContainer').show();
            loadProductsForRevenueSplitInvoice();
        }

        // Hide Add Revenue Split Form
        function hideAddRevenueSplitFormInvoice() {
            $('#addRevenueSplitFormContainer').hide();
            $('#emptyStateContainer').show();
        }

        // Load Products for Revenue Split
        function loadProductsForRevenueSplitInvoice() {
            const salesOrderId = {{ $invoice_data->f_sales_order_id ?? 0 }};

            // Use embedded invoice products data
            if (invoiceProductsData && invoiceProductsData.length > 0) {
                // Transform invoice products to the format expected by renderProductSplitFormsInvoice
                const products = invoiceProductsData.map(function(product) {
                    return {
                        f_product_id: product.f_product_id,
                        product_name: product.product_name,
                        product_code: product.product_code,
                        quantity: product.qty,
                        unit_price: product.unit_price,
                        unit_name: product.unit_code,
                        line_total: product.line_total
                    };
                });
                renderProductSplitFormsInvoice(products, salesOrderId);
            } else {
                $('#productSplitsList').html(`
                    <div class="alert alert-warning">
                        <i class="icon-base ti tabler-alert-circle me-2"></i>
                        Tidak ada produk dalam invoice ini.
                    </div>
                `);
            }
        }

        // Render Product Split Forms
        function renderProductSplitFormsInvoice(products, salesOrderId) {
            let html = '';

            products.forEach(function(product, index) {
                const productTotal = product.line_total || (product.unit_price * product.quantity);

                html += `
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <strong>${product.product_name}</strong>
                                <br><small class="text-muted">${product.product_code}</small>
                            </h6>
                            <small class="text-muted">
                                ${formatNumberInvoice(product.quantity)} ${product.unit_name || ''} × Rp ${formatNumberInvoice(product.unit_price)} =
                                <strong>Rp ${formatNumberInvoice(productTotal)}</strong>
                            </small>
                        </div>
                        <div class="card-body">
                            <input type="hidden" class="product-id" value="${product.f_product_id}">
                            <input type="hidden" class="product-total" value="${productTotal}">

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Porsi Pabrik <span style="color: red;">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control global-currency porsi-pabrik"
                                            data-index="${index}" required>
                                    </div>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input remaining-allocation" type="radio"
                                            name="remaining_allocation_${index}" value="pabrik"
                                            id="remaining_pabrik_${index}">
                                        <label class="form-check-label text-success" for="remaining_pabrik_${index}">
                                            <small>Dapat sisa</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Porsi Owner <span style="color: red;">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control global-currency porsi-owner"
                                            data-index="${index}" required>
                                    </div>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input remaining-allocation" type="radio"
                                            name="remaining_allocation_${index}" value="owner"
                                            id="remaining_owner_${index}">
                                        <label class="form-check-label text-success" for="remaining_owner_${index}">
                                            <small>Dapat sisa</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Porsi Perusahaan <span style="color: red;">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control global-currency porsi-perusahaan"
                                            data-index="${index}" required>
                                    </div>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input remaining-allocation" type="radio"
                                            name="remaining_allocation_${index}" value="perusahaan"
                                            id="remaining_perusahaan_${index}" checked>
                                        <label class="form-check-label text-success" for="remaining_perusahaan_${index}">
                                            <small>Dapat sisa</small>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <label class="form-label">Total Split</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control total-split-${index}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Selisih</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control difference-${index}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });

            $('#productSplitsList').html(html);

            // Re-initialize currency formatting
            initializeCurrencyFormatting();

            // Add event listeners for calculation
            $('.porsi-pabrik, .porsi-owner, .porsi-perusahaan').on('input', function() {
                const index = $(this).data('index');
                calculateSplitTotalsInvoice(index);
            });

            // Handle form submission
            $('#addRevenueSplitForm').off('submit').on('submit', function(e) {
                e.preventDefault();
                submitRevenueSplitInvoice(salesOrderId);
            });
        }

        // Calculate Split Totals
        function calculateSplitTotalsInvoice(index) {
            const pabrik = parseNumberInvoice($(`.porsi-pabrik[data-index="${index}"]`).val());
            const owner = parseNumberInvoice($(`.porsi-owner[data-index="${index}"]`).val());
            const perusahaan = parseNumberInvoice($(`.porsi-perusahaan[data-index="${index}"]`).val());
            const productTotal = parseNumberInvoice($('.product-total').eq(index).val());

            const totalSplit = pabrik + owner + perusahaan;
            const difference = totalSplit - productTotal;

            $(`.total-split-${index}`).val(formatNumberInvoice(totalSplit));
            $(`.difference-${index}`).val(formatNumberInvoice(Math.abs(difference)));

            // Update difference styling
            if (Math.abs(difference) > 0.01) {
                $(`.difference-${index}`).removeClass('text-success').addClass('text-danger');
            } else {
                $(`.difference-${index}`).removeClass('text-danger').addClass('text-success');
            }
        }

        // Submit Revenue Split
        function submitRevenueSplitInvoice(salesOrderId) {
            const products = [];
            let hasError = false;

            // Collect all product splits
            $('.card', '#productSplitsList').each(function(index) {
                const productId = $(this).find('.product-id').val();
                const productTotal = parseNumberInvoice($(this).find('.product-total').val());
                const porsiPabrik = parseNumberInvoice($(this).find('.porsi-pabrik').val());
                const porsiOwner = parseNumberInvoice($(this).find('.porsi-owner').val());
                const porsiPerusahaan = parseNumberInvoice($(this).find('.porsi-perusahaan').val());
                const remainingAllocation = $(this).find(`.remaining-allocation:checked`).val();

                const totalSplit = porsiPabrik + porsiOwner + porsiPerusahaan;

                // Validate totals match
                if (Math.abs(totalSplit - productTotal) > 0.01) {
                    toastr.error(`Total porsi untuk produk index ${index + 1} tidak sesuai`);
                    hasError = true;
                    return false;
                }

                products.push({
                    product_id: productId,
                    porsi_pabrik: porsiPabrik,
                    porsi_owner: porsiOwner,
                    porsi_perusahaan: porsiPerusahaan,
                    remaining_allocation: remainingAllocation,
                    product_total_amount: productTotal
                });
            });

            if (hasError) return;

            // Submit all splits
            const requests = products.map(function(product) {
                return $.ajax({
                    url: `/main_transaction/sales_order/revenue-split/${salesOrderId}/product/${product.product_id}`,
                    type: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: product
                });
            });

            // Wait for all requests to complete
            Promise.all(requests)
                .then(function() {
                    toastr.success('Revenue split berhasil disimpan');
                    hideAddRevenueSplitFormInvoice();
                    // Reload the revenue split to show the breakdown
                    revenueSplitLoaded = false;
                    loadRevenueSplit();
                })
                .catch(function(error) {
                    toastr.error('Gagal menyimpan revenue split');
                    console.error(error);
                });
        }

        // Utility Functions
        function formatNumberInvoice(value) {
            if (!value || value === 0) return '0';
            let num = parseFloat(value);
            if (isNaN(num)) return '0';
            // Smart formatting - only show decimals if necessary
            if (num % 1 === 0) {
                return Math.floor(num).toLocaleString('id-ID');
            } else {
                // Has decimals - format with up to 4 decimals and strip trailing zeros
                let formatted = num.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 4 });
                return formatted;
            }
        }

        function parseNumberInvoice(value) {
            if (!value) return 0;
            return parseInt(value.toString().replace(/[^\d]/g, '')) || 0;
        }
    </script>
@endsection
