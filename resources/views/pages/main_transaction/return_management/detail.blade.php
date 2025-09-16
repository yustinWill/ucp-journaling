@extends('layouts.default')
@section('page_title')
    Detail Return Management - {{ $return_data->transaction_code }}
@stop
@section('content')
    <div class="card mb-6">
        <div class="card-body">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <h4 class="mb-3">Detail Return Management</h4>
                </div>
                <div class="col-md-4">
                    <!-- Show buttons directly when less than 3 actions -->
                    <div class="d-flex gap-2 justify-content-end">
                        <button type="button" class="btn btn-primary btn-md waves-effect waves-light" onclick="window.print()">
                            <i class="icon-base ti tabler-printer me-2"></i>Print
                        </button>
                        @if($return_data->processing_status === 'NOT_PROCESSED' && $return_data->status === 'APPROVED')
                            <a href="{{ route('main_transaction.stock_adjustment.add') }}?return_id={{ $return_data->id }}" class="btn btn-warning btn-md waves-effect waves-light">
                                <i class="icon-base ti tabler-adjustments me-2"></i>Buat Stock Adjustment
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Return Management Title -->
            <h2>{{ $return_data->transaction_code }}</h2>
            <span>
                <h6>Data return management berdasarkan tanggal {{ $return_data->created_at }}</h6>
            </span>
            <hr class="my-6 mx-n6" />

            <!-- Informasi Dasar Transaksi Section - Outside tabs, on top -->
            
            <!-- Row 1: Tanggal Return, Nomor Transaksi -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tanggal Return</label>
                    <input type="text" class="form-control" value="{{ $return_data->return_date ? date('d/m/Y', strtotime($return_data->return_date)) : '-' }}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nomor Transaksi</label>
                    <input type="text" class="form-control" value="{{ $return_data->transaction_code }}" disabled />
                </div>
            </div>

            <hr class="my-6 mx-n6" />

            <!-- Tabs Section -->
            <div class="col-lg-12">
                <div class="demo-inline-spacing mt-4">
                    <div class="list-group list-group-horizontal-md text-md-center" role="tablist">
                        <a class="list-group-item list-group-item-action waves-effect active" id="return-info-list-item"
                            data-bs-toggle="list" href="#horizontal-return-info" aria-selected="true" role="tab">
                            <i class="icon-base ti tabler-info-circle me-2"></i>
                            <b>Informasi Detail</b>
                        </a>
                        <a class="list-group-item list-group-item-action waves-effect" id="return-items-list-item"
                            data-bs-toggle="list" href="#horizontal-return-items" aria-selected="false" role="tab">
                            <i class="icon-base ti tabler-package me-2"></i>
                            <b>Items Return</b>
                        </a>
                        <a class="list-group-item list-group-item-action waves-effect" id="audit-log-list-item"
                            data-bs-toggle="list" href="#horizontal-audit-log" aria-selected="false" role="tab">
                            <i class="icon-base ti tabler-history me-2"></i>
                            <b>Audit Log</b>
                        </a>
                    </div>
                    <div class="tab-content px-0 mt-0">
                        <!-- Information Tab -->
                        <div class="tab-pane fade show active" id="horizontal-return-info" role="tabpanel">
                            <div id="return-info-content">
                                <!-- Informasi Return & Source Invoice Section -->
                                <div class="section-header mt-12">
                                    <h6>Informasi Return & Source Invoice</h6>
                                    <hr class="mx-n6">
                                </div>

                                <!-- Row 1: Jenis Return, Invoice Asal -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Jenis Return</label>
                                        @php
                                            $returnTypeClass = $return_data->return_type === 'PURCHASE_RETURN' ? 'bg-label-primary' : 'bg-label-info';
                                            $returnTypeText = $return_data->return_type === 'PURCHASE_RETURN' ? 'Purchase Return' : 'Sales Return';
                                        @endphp
                                        <div>
                                            <span class="badge {{ $returnTypeClass }} fs-6 px-3 py-2">{{ $returnTypeText }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Invoice Asal</label>
                                        <input type="text" class="form-control" value="{{ $return_data->source_invoice_code }}" disabled />
                                    </div>
                                </div>

                                <!-- Row 2: Alasan Return, Deskripsi Alasan -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Alasan Return</label>
                                        <input type="text" class="form-control" value="{{ $return_data->return_reason }}" disabled />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Deskripsi Alasan</label>
                                        <textarea class="form-control" rows="2" disabled>{{ $return_data->return_reason_description ?? '-' }}</textarea>
                                    </div>
                                </div>

                                <!-- Detail Return Section -->
                                <div class="section-header mt-12">
                                    <h6>Detail Return</h6>
                                    <hr class="mx-n6">
                                </div>

                                <!-- Row 1: Tanggal Transaksi, Status -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Tanggal Transaksi</label>
                                        <input type="text" class="form-control" value="{{ $return_data->transaction_date ? date('d/m/Y', strtotime($return_data->transaction_date)) : '-' }}" disabled />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Status</label>
                                        @php
                                            $statusClass = '';
                                            switch($return_data->status) {
                                                case 'DRAFT':
                                                    $statusClass = 'bg-label-secondary';
                                                    break;
                                                case 'PENDING_APPROVAL':
                                                    $statusClass = 'bg-label-warning';
                                                    break;
                                                case 'APPROVED':
                                                    $statusClass = 'bg-label-success';
                                                    break;
                                                case 'PROCESSED':
                                                    $statusClass = 'bg-label-info';
                                                    break;
                                                case 'REJECTED':
                                                    $statusClass = 'bg-label-danger';
                                                    break;
                                                case 'CANCELLED':
                                                    $statusClass = 'bg-label-dark';
                                                    break;
                                                default:
                                                    $statusClass = 'bg-label-secondary';
                                            }
                                        @endphp
                                        <span class="badge {{ $statusClass }} fs-6 py-2 px-3">{{ $return_data->status }}</span>
                                    </div>
                                </div>

                                <!-- Row 2: Status Proses, Status Stock Adjustment -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Status Proses</label>
                                        @php
                                            $processingClass = '';
                                            switch($return_data->processing_status) {
                                                case 'NOT_PROCESSED':
                                                    $processingClass = 'bg-label-secondary';
                                                    break;
                                                case 'PARTIAL':
                                                    $processingClass = 'bg-label-warning';
                                                    break;
                                                case 'PROCESSED':
                                                    $processingClass = 'bg-label-success';
                                                    break;
                                                default:
                                                    $processingClass = 'bg-label-secondary';
                                            }
                                        @endphp
                                        <span class="badge {{ $processingClass }} fs-6 py-2 px-3">{{ $return_data->processing_status }}</span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Status Stock Adjustment</label>
                                        @php
                                            $stockAdjustmentClass = $return_data->processing_status === 'PROCESSED' ? 'bg-label-success' : 'bg-label-warning';
                                            $stockAdjustmentText = $return_data->processing_status === 'PROCESSED' ? 'Sudah Disesuaikan' : 'Belum Disesuaikan';
                                        @endphp
                                        <div>
                                            <span class="badge {{ $stockAdjustmentClass }} fs-6 py-2 px-3">{{ $stockAdjustmentText }}</span>
                                            @if($return_data->f_stock_adjustment_id)
                                                <br><small class="text-muted mt-2 d-block">Stock Adjustment ID: {{ $return_data->f_stock_adjustment_id }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Row 3: Keterangan -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-12">
                                        <label class="form-label">Keterangan</label>
                                        <textarea class="form-control" rows="3" disabled>{{ $return_data->description ?? '-' }}</textarea>
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
                                        <input type="text" class="form-control" value="Rp {{ number_format($return_data->subtotal ?? 0, 0, ',', '.') }}" disabled />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Total Pajak</label>
                                        <input type="text" class="form-control" value="Rp {{ number_format($return_data->total_tax ?? 0, 0, ',', '.') }}" disabled />
                                    </div>
                                </div>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Total Discount</label>
                                        <input type="text" class="form-control" value="Rp {{ number_format($return_data->total_discount ?? 0, 0, ',', '.') }}" disabled />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Grand Total</label>
                                        <input type="text" class="form-control fw-bold text-primary" value="Rp {{ number_format($return_data->grand_total ?? 0, 0, ',', '.') }}" disabled />
                                    </div>
                                </div>

                                <!-- Additional Information Section -->
                                <div class="section-header mt-12">
                                    <h6>Informasi Tambahan</h6>
                                    <hr class="mx-n6">
                                </div>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Memo Internal</label>
                                        <textarea class="form-control" rows="3" disabled>{{ $return_data->internal_memo ?? '-' }}</textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Lampiran</label>
                                        @if($return_data->attachment)
                                            <div>
                                                <a href="{{ asset('storage/' . $return_data->attachment) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                                    <i class="icon-base ti tabler-download me-1"></i>Download
                                                </a>
                                            </div>
                                        @else
                                            <input type="text" class="form-control" value="Tidak ada lampiran" disabled />
                                        @endif
                                    </div>
                                </div>

                                @if($return_data->approved_by)
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Disetujui Oleh</label>
                                        @if($canViewUser && $return_data->approved_by)
                                            <div class="input-group">
                                                <input type="text" class="form-control" value="{{ $return_data->approved_by_name ?? '-' }}" disabled />
                                                <a href="{{ route('master_data.user.detail', $return_data->approved_by) }}" class="btn btn-outline-primary" title="Lihat Detail User">
                                                    <i class="icon-base ti tabler-external-link"></i>
                                                </a>
                                            </div>
                                        @else
                                            <input type="text" class="form-control" value="{{ $return_data->approved_by_name ?? '-' }}" disabled />
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Tanggal Persetujuan</label>
                                        <input type="text" class="form-control" value="{{ $return_data->approved_at ? date('d/m/Y H:i:s', strtotime($return_data->approved_at)) : '-' }}" disabled />
                                    </div>
                                </div>
                                @endif

                                @if($return_data->approval_notes)
                                <div class="row g-3 mb-4">
                                    <div class="col-md-12">
                                        <label class="form-label">Catatan Persetujuan</label>
                                        <textarea class="form-control" rows="3" disabled>{{ $return_data->approval_notes }}</textarea>
                                    </div>
                                </div>
                                @endif

                            </div>
                        </div>

                        <!-- Items Return Tab -->
                        <div class="tab-pane fade" id="horizontal-return-items" role="tabpanel">
                            <!-- Items Return Section -->
                            <div class="section-header mt-12">
                                <h6>Items Return</h6>
                            </div>
                            
                            <!-- Product Table -->
                            <div class="mx-n5 my-4">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="width: 25%;">Produk</th>
                                            <th style="width: 10%;">Qty Original</th>
                                            <th style="width: 10%;">Qty Return</th>
                                            <th style="width: 8%;">Unit</th>
                                            <th style="width: 12%;">Harga Return</th>
                                            <th style="width: 12%;">Total Return</th>
                                            <th style="width: 10%;">Kondisi</th>
                                            <th style="width: 13%;">Note</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($return_products as $product)
                                            <tr>
                                                <td>
                                                    @if($canViewProduct)
                                                        <a href="{{ route('master_data.product.detail', $product->f_product_id) }}" class="text-primary fw-semibold">
                                                            <strong>{{ $product->product_code ?? '' }} - {{ $product->product_name ?? '' }}</strong>
                                                        </a>
                                                    @else
                                                        <strong>{{ $product->product_code ?? '' }} - {{ $product->product_name ?? '' }}</strong>
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ number_format($product->original_qty ?? 0, 3) }} {{ $product->unit_code ?? '' }}</td>
                                                <td class="text-center">
                                                    <span class="badge bg-label-info">{{ number_format($product->return_qty ?? 0, 3) }}</span> {{ $product->unit_code ?? '' }}
                                                </td>
                                                <td class="text-center">{{ $product->unit_code ?? '-' }}</td>
                                                <td class="text-end">Rp {{ number_format($product->return_unit_price ?? 0, 0, ',', '.') }}</td>
                                                <td class="text-end">
                                                    <strong>Rp {{ number_format($product->total_return_price ?? 0, 0, ',', '.') }}</strong>
                                                </td>
                                                <td>
                                                    @php
                                                        $conditionClass = '';
                                                        switch($product->return_condition) {
                                                            case 'NEW': $conditionClass = 'bg-label-success'; break;
                                                            case 'USED_GOOD': $conditionClass = 'bg-label-info'; break;
                                                            case 'USED_FAIR': $conditionClass = 'bg-label-warning'; break;
                                                            case 'DAMAGED': $conditionClass = 'bg-label-danger'; break;
                                                            case 'DEFECTIVE': $conditionClass = 'bg-label-dark'; break;
                                                            default: $conditionClass = 'bg-label-secondary'; break;
                                                        }
                                                    @endphp
                                                    <span class="badge {{ $conditionClass }}">{{ $product->return_condition }}</span>
                                                </td>
                                                <td>{{ $product->note ?? '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted py-4">
                                                    <i class="icon-base ti tabler-package-off fs-1"></i>
                                                    <div class="mt-2">Tidak ada data produk</div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    @if(count($return_products) > 0)
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="5" class="text-end">Total Return:</th>
                                            <th class="text-end">
                                                <span class="h5 text-primary">Rp {{ number_format($return_data->grand_total ?? 0, 0, ',', '.') }}</span>
                                            </th>
                                            <th colspan="2"></th>
                                        </tr>
                                    </tfoot>
                                    @endif
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
    // Audit Log DataTable
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