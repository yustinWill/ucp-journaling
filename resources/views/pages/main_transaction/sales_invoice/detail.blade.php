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
                        @if (in_array($invoice_data->status, ['DRAFT', 'PENDING', 'APPROVED']))
                            <button type="button" class="btn btn-info btn-md waves-effect waves-light"
                                onclick="alert('Feature coming soon: Invoice Settlement')">
                                <i class="icon-base ti tabler-credit-card me-2"></i>
                                Settle Invoice
                            </button>
                        @endif

                        {{-- <button type="button" class="btn btn-primary btn-md waves-effect waves-light" onclick="window.print()">
                            <i class="icon-base ti tabler-printer me-2"></i>Print
                        </button> --}}

                        {{-- @if ($canEditSalesInvoice)
                            <a href="{{ route('main_transaction.sales_invoice.edit', $invoice_data->id) }}" class="btn btn-success btn-md waves-effect waves-light">
                                <i class="icon-base ti tabler-edit me-2"></i>Edit
                            </a>
                        @endif --}}

                        @if (in_array($invoice_data->status, ['PAID', 'UNPAID', 'PARTIAL_PAID']))
                            <a href="{{ route('main_transaction.return_management.add') }}?return_type=SALES_RETURN&source_invoice_id={{ $invoice_data->id }}"
                                class="btn btn-warning btn-md waves-effect waves-light">
                                <i class="icon-base ti tabler-arrow-back-up me-2"></i>Return
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
                                                    <td class="text-center">{{ $product->ordered_qty ?? 0 }}
                                                        {{ $product->unit_code ?? '' }}</td>
                                                    <td class="text-center">
                                                        <span
                                                            class="badge bg-label-info">{{ number_format($product->qty ?? 0, 0, ',', '.') }}</span>
                                                        {{ $product->unit_code ?? '' }}
                                                    </td>
                                                    <td class="text-end">Rp
                                                        {{ number_format($product->unit_price ?? 0, 0, ',', '.') }}
                                                    </td>
                                                    <td class="text-end">
                                                        <strong>Rp
                                                            {{ number_format($product->line_total ?? 0, 0, ',', '.') }}</strong>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

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
