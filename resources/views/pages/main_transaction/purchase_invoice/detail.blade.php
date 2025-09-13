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
                        @if($canEditPurchaseInvoice)
                            <a href="{{ route('main_transaction.purchase_invoice.edit', $invoice_data->id) }}" class="btn btn-primary btn-md waves-effect waves-light">
                                <i class="icon-base ti tabler-edit me-2"></i>
                                Edit
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
                    <input type="text" class="form-control" value="{{ $invoice_data->invoice_date ? date('d/m/Y', strtotime($invoice_data->invoice_date)) : '-' }}" disabled />
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
                        <a class="list-group-item list-group-item-action waves-effect active" id="purchase-invoice-info-list-item"
                            data-bs-toggle="list" href="#horizontal-purchase-invoice-info" aria-selected="true" role="tab">
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
                            @if($canViewPurchaseOrder && $invoice_data->po_id)
                                <div class="input-group">
                                    <input type="text" class="form-control" value="{{ $invoice_data->po_reference ?? '-' }}" disabled />
                                    <a href="{{ route('main_transaction.purchase_order.detail', $invoice_data->po_id) }}" class="btn btn-outline-primary" title="Lihat Detail PO">
                                        <i class="icon-base ti tabler-external-link"></i>
                                    </a>
                                </div>
                            @else
                                <input type="text" class="form-control" value="{{ $invoice_data->po_reference ?? '-' }}" disabled />
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Supplier</label>
                            <input type="text" class="form-control" value="{{ $invoice_data->supplier_name ?? '-' }}" disabled />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email Supplier</label>
                            <input type="text" class="form-control" value="{{ $invoice_data->supplier_email ?? '-' }}" disabled />
                        </div>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Alamat Supplier</label>
                            <textarea class="form-control" rows="2" disabled>{{ $invoice_data->supplier_address ?? '-' }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status PO</label>
                            <input type="text" class="form-control" value="{{ $invoice_data->po_status ?? '-' }}" disabled />
                        </div>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal PO</label>
                            <input type="text" class="form-control" value="{{ $invoice_data->po_date ? date('d/m/Y', strtotime($invoice_data->po_date)) : '-' }}" disabled />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Grand Total PO</label>
                            <input type="text" class="form-control" value="{{ $invoice_data->po_grand_total ? 'Rp ' . number_format($invoice_data->po_grand_total, 0, ',', '.') : '-' }}" disabled />
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
                            <input type="text" class="form-control" value="{{ $invoice_data->transaction_date ? date('d/m/Y', strtotime($invoice_data->transaction_date)) : '-' }}" disabled />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Jatuh Tempo</label>
                            <input type="text" class="form-control" value="{{ $invoice_data->due_date ? date('d/m/Y', strtotime($invoice_data->due_date)) : '-' }}" disabled />
                        </div>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Termin Pembayaran</label>
                            <input type="text" class="form-control" value="{{ $invoice_data->payment_term_name ?? $invoice_data->payment_terms ?? '-' }}" disabled />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            @php
                                $statusClass = '';
                                switch($invoice_data->status) {
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
                            <span class="badge {{ $statusClass }} fs-6 py-2 px-3">{{ $invoice_data->status }}</span>
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
                            <input type="text" class="form-control" value="Rp {{ number_format($invoice_data->subtotal ?? 0, 0, ',', '.') }}" disabled />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Total Pajak</label>
                            <input type="text" class="form-control" value="Rp {{ number_format($invoice_data->total_tax ?? 0, 0, ',', '.') }}" disabled />
                        </div>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Total Discount</label>
                            <input type="text" class="form-control" value="Rp {{ number_format($invoice_data->total_discount ?? 0, 0, ',', '.') }}" disabled />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Grand Total</label>
                            <input type="text" class="form-control fw-bold text-primary" value="Rp {{ number_format($invoice_data->grand_total ?? 0, 0, ',', '.') }}" disabled />
                        </div>
                    </div>

                    <!-- Additional Information Section -->
                    <div class="section-header mt-12">
                        <h6>Informasi Tambahan</h6>
                        <hr class="mx-n6">
                    </div>

                    <!-- System Information Section -->
                    <div class="section-header mt-12">
                        <h6>Informasi Sistem</h6>
                        <hr class="mx-n6">
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Dibuat Pada</label>
                            <input type="text" class="form-control" value="{{ $invoice_data->created_at ? date('d/m/Y H:i:s', strtotime($invoice_data->created_at)) : '-' }}" disabled />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Dibuat Oleh</label>
                            @if($canViewUser && $invoice_data->created_by)
                                <div class="input-group">
                                    <input type="text" class="form-control" value="{{ $invoice_data->created_by_name ?? '-' }}" disabled />
                                    <a href="{{ route('master_data.user.detail', $invoice_data->created_by) }}" class="btn btn-outline-primary" title="Lihat Detail User">
                                        <i class="icon-base ti tabler-external-link"></i>
                                    </a>
                                </div>
                            @else
                                <input type="text" class="form-control" value="{{ $invoice_data->created_by_name ?? '-' }}" disabled />
                            @endif
                        </div>
                    </div>
                    @if($invoice_data->updated_at)
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Diupdate Pada</label>
                            <input type="text" class="form-control" value="{{ date('d/m/Y H:i:s', strtotime($invoice_data->updated_at)) }}" disabled />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Diupdate Oleh</label>
                            @if($canViewUser && $invoice_data->updated_by)
                                <div class="input-group">
                                    <input type="text" class="form-control" value="{{ $invoice_data->updated_by_name ?? '-' }}" disabled />
                                    <a href="{{ route('master_data.user.detail', $invoice_data->updated_by) }}" class="btn btn-outline-primary" title="Lihat Detail User">
                                        <i class="icon-base ti tabler-external-link"></i>
                                    </a>
                                </div>
                            @else
                                <input type="text" class="form-control" value="{{ $invoice_data->updated_by_name ?? '-' }}" disabled />
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
                                        <td>{{ $product->product_code ?? '' }} - {{ $product->product_name ?? '' }}</td>
                                        <td>{{ $product->ordered_qty ?? 0 }} {{ $product->unit_name ?? '' }}</td>
                                        <td>{{ $product->qty ?? 0 }} {{ $product->unit_name ?? '' }}</td>
                                        <td>{{ number_format($product->unit_price ?? 0, 0, ',', '.') }}</td>
                                        <td>{{ number_format($product->total_price ?? 0, 0, ',', '.') }}</td>
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
                            @if(count($invoice_products) > 0)
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="4" class="text-end">Total Invoice:</th>
                                    <th class="text-end">
                                        Rp {{ number_format($invoice_products->sum('total_price') ?? 0, 0, ',', '.') }}
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
                                                <td>{{ $log->created_at ? date('d/m/Y H:i:s', strtotime($log->created_at)) : '-' }}</td>
                                                <td>
                                                    @if($canViewUser && $log->user_id)
                                                        <a href="{{ route('master_data.user.detail', $log->user_id) }}" class="text-primary fw-semibold">
                                                            {{ $log->user_name ?? 'System' }}
                                                        </a>
                                                    @else
                                                        {{ $log->user_name ?? 'System' }}
                                                    @endif
                                                </td>
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
});
</script>
@endsection