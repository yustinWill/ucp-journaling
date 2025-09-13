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
                        <button type="button" class="btn btn-primary btn-md waves-effect waves-light" onclick="window.print()">
                            <i class="icon-base ti tabler-printer me-2"></i>Print
                        </button>
                        @if($canEditSalesInvoice)
                            <a href="{{ route('main_transaction.sales_invoice.edit', $invoice_data->id) }}" class="btn btn-success btn-md waves-effect waves-light">
                                <i class="icon-base ti tabler-edit me-2"></i>Edit
                            </a>
                        @endif
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
                        <a class="list-group-item list-group-item-action waves-effect active" id="sales-invoice-info-list-item"
                            data-bs-toggle="list" href="#horizontal-sales-invoice-info" aria-selected="true" role="tab">
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
                        <div class="tab-pane fade show active" id="horizontal-sales-invoice-info" role="tabpanel">
                            <div id="sales-invoice-info-content">
                                <!-- Informasi Customer & Sales Order Section -->
                                <div class="section-header mt-12">
                                    <h6>Informasi Customer & Sales Order</h6>
                                    <hr class="mx-n6">
                                </div>

                        <!-- Row 1: Customer and SO Reference -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Customer</label>
                                <input type="text" class="form-control" value="{{ $invoice_data->customer_name }}" disabled />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Referensi SO</label>
                                <input type="text" class="form-control" value="{{ $invoice_data->so_reference }}" disabled />
                            </div>
                        </div>

                        <!-- Row 2: Email and Address -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Email Customer</label>
                                <input type="text" class="form-control" value="{{ $invoice_data->customer_email ?? '-' }}" disabled />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Alamat Customer</label>
                                <textarea class="form-control" rows="2" disabled>{{ $invoice_data->customer_address ?? '-' }}</textarea>
                            </div>
                        </div>

                        <!-- Invoice Details Section -->
                        <div class="section-header mt-12">
                            <h6>Detail Invoice</h6>
                            <hr class="mx-n6">
                        </div>

                        <!-- Row 1: Dates -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Transaksi</label>
                                <input type="text" class="form-control" value="{{ $invoice_data->transaction_date }}" disabled />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Jatuh Tempo</label>
                                <input type="text" class="form-control" value="{{ $invoice_data->due_date }}" disabled />
                            </div>
                        </div>

                        <!-- Row 2: Payment Terms and Status -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Syarat Pembayaran</label>
                                <input type="text" class="form-control" value="{{ $invoice_data->payment_term_name ?? '-' }}" disabled />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                @php
                                    $statusClass = 'secondary';
                                    $statusText = $invoice_data->status;
                                    switch($invoice_data->status) {
                                        case 'DRAFT': $statusClass = 'secondary'; $statusText = 'Draft'; break;
                                        case 'UNPAID': $statusClass = 'warning'; $statusText = 'Belum Dibayar'; break;
                                        case 'PAID': $statusClass = 'success'; $statusText = 'Lunas'; break;
                                        case 'PARTIAL_PAID': $statusClass = 'info'; $statusText = 'Sebagian Dibayar'; break;
                                        case 'OVERDUE': $statusClass = 'danger'; $statusText = 'Jatuh Tempo'; break;
                                        case 'CANCELLED': $statusClass = 'dark'; $statusText = 'Dibatalkan'; break;
                                    }
                                @endphp
                                <div>
                                    <span class="badge bg-label-{{ $statusClass }} fs-6 px-3 py-2">{{ $statusText }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Row 3: Description -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Keterangan</label>
                                <textarea class="form-control" rows="3" disabled>{{ $invoice_data->description ?? '-' }}</textarea>
                            </div>
                        </div>

                        <!-- Informasi Keuangan Section -->
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
                                <label class="form-label">Total Diskon</label>
                                <input type="text" class="form-control" value="Rp {{ number_format($invoice_data->total_discount ?? 0, 0, ',', '.') }}" disabled />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Grand Total</label>
                                <input type="text" class="form-control fw-bold text-primary" value="Rp {{ number_format($invoice_data->grand_total ?? 0, 0, ',', '.') }}" disabled />
                            </div>
                        </div>

                        <!-- Informasi Tambahan Section -->
                        <div class="section-header mt-12">
                            <h6>Informasi Tambahan</h6>
                            <hr class="mx-n6">
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Tag</label>
                                @if($invoice_data->tags)
                                    @php $tags = json_decode($invoice_data->tags, true); @endphp
                                    @if($tags && count($tags) > 0)
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($tags as $tag)
                                                @if($tag && isset($tag['code']))
                                                    <span class="badge" style="background-color: {{ $tag['preview'] ?? '#6c757d' }}; color: white;">{{ $tag['code'] }}</span>
                                                @endif
                                            @endforeach
                                        </div>
                                    @else
                                        <input type="text" class="form-control" value="-" disabled />
                                    @endif
                                @else
                                    <input type="text" class="form-control" value="-" disabled />
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Memo Internal</label>
                                <textarea class="form-control" rows="3" disabled>{{ $invoice_data->internal_memo ?? '-' }}</textarea>
                            </div>
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Lampiran</label>
                                @if($invoice_data->attachment)
                                    <div>
                                        <a href="{{ $invoice_data->attachment }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                            <i class="icon-base ti tabler-download me-1"></i>Download
                                        </a>
                                    </div>
                                @else
                                    <input type="text" class="form-control" value="Tidak ada lampiran" disabled />
                                @endif
                            </div>
                        </div>

                        <!-- Informasi Sistem Section -->
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
                                <input type="text" class="form-control" value="{{ $invoice_data->created_by_name ?? '-' }}" disabled />
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
                                <input type="text" class="form-control" value="{{ $invoice_data->updated_by_name ?? '-' }}" disabled />
                            </div>
                        </div>
                        @endif
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
                                                <th style="width: 12%;">Qty SO</th>
                                                <th style="width: 12%;">Qty Invoice</th>
                                                <th style="width: 15%;">Unit Price</th>
                                                <th style="width: 15%;">Total</th>
                                                <th style="width: 16%;">Note</th>
                                            </tr>
                                        </thead>
                                    <tbody>
                                        @forelse($invoice_products as $product)
                                            <tr>
                                                <td><strong>{{ $product->product_name ?? '' }}</strong></td>
                                                <td class="text-center">{{ $product->ordered_qty ?? 0 }} {{ $product->unit_code ?? '' }}</td>
                                                <td class="text-center">
                                                    <span class="badge bg-label-info">{{ number_format($product->qty ?? 0, 0, ',', '.') }}</span> {{ $product->unit_code ?? '' }}
                                                </td>
                                                <td class="text-end">Rp {{ number_format($product->unit_price ?? 0, 0, ',', '.') }}</td>
                                                <td class="text-end">
                                                    <strong>Rp {{ number_format($product->line_total ?? 0, 0, ',', '.') }}</strong>
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
                                    @if($invoice_products->count() > 0)
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="4" class="text-end">Total Invoice:</th>
                                            <th class="text-end">
                                                <span class="h5 text-primary">Rp {{ number_format($invoice_data->grand_total ?? 0, 0, ',', '.') }}</span>
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
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("main_transaction.sales_invoice.auditLogDataTable", $invoice_data->id) }}',
        },
        columns: [
            {
                data: 'created_at',
                name: 'created_at',
                render: function(data) {
                    return moment(data).format('DD/MM/YYYY HH:mm');
                }
            },
            {
                data: 'user_name',
                name: 'user_name',
                render: function(data) {
                    return data || '-';
                }
            },
            {
                data: 'action_type',
                name: 'action_type',
                render: function(data) {
                    let badgeClass = 'bg-label-secondary';
                    switch(data) {
                        case 'CREATE':
                            badgeClass = 'bg-label-success';
                            break;
                        case 'UPDATE':
                            badgeClass = 'bg-label-warning';
                            break;
                        case 'DELETE':
                            badgeClass = 'bg-label-danger';
                            break;
                    }
                    return '<span class="badge ' + badgeClass + '">' + data + '</span>';
                }
            },
            {
                data: 'description',
                name: 'description',
                render: function(data) {
                    return data || '-';
                }
            }
        ],
        orderCellsTop: true,
        order: [[0, 'desc']], // Sort by date descending
        searching: false,
        paging: false,
        info: false
    });
});
</script>
@endsection