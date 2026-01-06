@extends('layouts.default')
@section('page_title')
    Detail Stock Adjustment - {{ $stockAdjustment->adjustment_number }}
@stop
@section('content')
    <div class="card mb-6">
        <div class="card-body">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <h4 class="mb-3">Detail Stock Adjustment</h4>
                </div>
                <div class="col-md-4">
                    @php
                        $totalActions = 1; // Edit always present
                        if($stockAdjustment->status === 'PENDING_APPROVAL') {
                            $totalActions += 2; // Approve and Reject
                        }
                        if($stockAdjustment->status === 'APPROVED') {
                            $totalActions++; // Apply action
                        }
                    @endphp

                    @if($totalActions < 3)
                        <!-- Show buttons directly when less than 3 actions -->
                        {{-- <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('main_transaction.stock_adjustment.edit', $stockAdjustment->id) }}" class="btn btn-primary btn-md waves-effect waves-light">
                                <i class="icon-base ti tabler-edit me-2"></i>
                                Edit
                            </a>
                            @if($stockAdjustment->status === 'APPROVED')
                                <button type="button" class="btn btn-success btn-md waves-effect waves-light" id="apply-adjustment-btn">
                                    <i class="icon-base ti tabler-check me-2"></i>
                                    Terapkan ke Stok
                                </button>
                            @endif
                        </div> --}}
                    @else
                        <!-- Show dropdown for 3 or more actions -->
                        {{-- <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('main_transaction.stock_adjustment.detail', $stockAdjustment->id) }}" class="btn btn-primary btn-md waves-effect waves-light">
                                <i class="icon-base ti tabler-eye me-2"></i>
                                Detail
                            </a>
                            <div class="dropdown">
                                <button type="button" class="btn btn-primary btn-md waves-effect waves-light dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    Aksi
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('main_transaction.stock_adjustment.edit', $stockAdjustment->id) }}">
                                            <i class="icon-base ti tabler-edit me-2"></i>Edit
                                        </a>
                                    </li>
                                    @if($stockAdjustment->status === 'PENDING_APPROVAL')
                                        <li>
                                            <a class="dropdown-item" href="javascript:void(0);" id="approve-adjustment-btn">
                                                <i class="icon-base ti tabler-check me-2"></i>Setujui
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="javascript:void(0);" id="reject-adjustment-btn">
                                                <i class="icon-base ti tabler-x me-2"></i>Tolak
                                            </a>
                                        </li>
                                    @endif
                                    @if($stockAdjustment->status === 'APPROVED')
                                        <li>
                                            <a class="dropdown-item" href="javascript:void(0);" id="apply-adjustment-btn">
                                                <i class="icon-base ti tabler-check me-2"></i>Terapkan ke Stok
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div> --}}
                    @endif
                </div>
            </div>

            <!-- Entity Title -->
            <h2>{{ $stockAdjustment->adjustment_number }}</h2>
            <span>
                <h6>Data di bawah berdasarkan tanggal {{ date('Y-m-d H:i:s') }}</h6>
            </span>
            <hr class="my-6 mx-n6" />

            <!-- Top-level Information -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tanggal Adjustment</label>
                    <input type="text" class="form-control" value="{{ $stockAdjustment->adjustment_date }}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tipe Adjustment</label>
                    <input type="text" class="form-control" value="{{ $stockAdjustment->adjustment_type }}" disabled />
                </div>
            </div>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Gudang</label>
                    <input type="text" class="form-control" value="{{ $stockAdjustment->warehouse_name }}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Penanggung Jawab</label>
                    <input type="text" class="form-control" value="{{ $stockAdjustment->pic_name }}" disabled />
                </div>
            </div>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <input type="text" class="form-control" value="{{ $stockAdjustment->status }}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nomor Referensi</label>
                    <input type="text" class="form-control" value="{{ $stockAdjustment->reference_number ?: '-' }}" disabled />
                </div>
            </div>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Deskripsi</label>
                    <textarea class="form-control" rows="3" disabled>{{ $stockAdjustment->description }}</textarea>
                </div>
            </div>
            <hr class="my-6 mx-n6" />

            <!-- Summary Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="summary-card primary">
                        <h6>Total Item</h6>
                        <h4>{{ $stockAdjustment->total_items }}</h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card {{ $stockAdjustment->total_adjustment_qty > 0 ? 'success' : ($stockAdjustment->total_adjustment_qty < 0 ? 'danger' : 'warning') }}">
                        <h6>Qty Adjustment</h6>
                        <h4>{{ $stockAdjustment->total_adjustment_qty > 0 ? '+' : '' }}{{ number_format($stockAdjustment->total_adjustment_qty, 0, ',', '.') }}</h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card {{ $stockAdjustment->total_adjustment_value > 0 ? 'success' : ($stockAdjustment->total_adjustment_value < 0 ? 'danger' : 'warning') }}">
                        <h6>Nilai Adjustment</h6>
                        <h4>{{ $stockAdjustment->total_adjustment_value > 0 ? '+' : ($stockAdjustment->total_adjustment_value < 0 ? '-' : '') }}Rp {{ number_format(abs($stockAdjustment->total_adjustment_value), 0, ',', '.') }}</h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card {{ $stockAdjustment->status === 'APPLIED' ? 'success' : ($stockAdjustment->status === 'PENDING_APPROVAL' ? 'warning' : 'primary') }}">
                        <h6>Status</h6>
                        <h4>{{ $stockAdjustment->status }}</h4>
                    </div>
                </div>
            </div>

            <!-- Tab System -->
            <div class="col-lg-12">
                <div class="demo-inline-spacing mt-4">
                    <div class="list-group list-group-horizontal-md text-md-center" role="tablist">
                        <a class="list-group-item list-group-item-action waves-effect active" id="products-list-item"
                            data-bs-toggle="list" href="#horizontal-products" aria-selected="true" role="tab">
                            <i class="icon-base ti tabler-box me-2"></i>
                            <b>Detail Produk & Adjustment</b>
                        </a>
                        <a class="list-group-item list-group-item-action waves-effect" id="history-list-item"
                            data-bs-toggle="list" href="#horizontal-history" aria-selected="false" role="tab">
                            <i class="icon-base ti tabler-history me-2"></i>
                            <b>Audit Log</b>
                        </a>
                    </div>
                    <div class="tab-content px-0 mt-0">
                        <!-- Products & Adjustments Detail Tab -->
                        <div class="tab-pane fade show active" id="horizontal-products" role="tabpanel">
                            <!-- Summary Cards Section -->
                            <div class="section-header mt-6">
                                <h6>Ringkasan Adjustment</h6>
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <div class="card border-success">
                                        <div class="card-body">
                                            <h6 class="card-title text-success">Adjustment Positif</h6>
                                            <p class="card-text">
                                                <strong>Total Qty:</strong> +{{ max(0, $stockAdjustment->total_adjustment_qty) }}<br>
                                                <strong>Estimasi Nilai:</strong> {{ $stockAdjustment->total_adjustment_value > 0 ? '+Rp ' . number_format($stockAdjustment->total_adjustment_value, 0, ',', '.') : 'Rp 0' }}
                                            </p>
                                            <small class="text-muted">Penambahan stok ke sistem</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-danger">
                                        <div class="card-body">
                                            <h6 class="card-title text-danger">Adjustment Negatif</h6>
                                            <p class="card-text">
                                                <strong>Total Qty:</strong> {{ min(0, $stockAdjustment->total_adjustment_qty) }}<br>
                                                <strong>Estimasi Nilai:</strong> {{ $stockAdjustment->total_adjustment_value < 0 ? '-Rp ' . number_format(abs($stockAdjustment->total_adjustment_value), 0, ',', '.') : 'Rp 0' }}
                                            </p>
                                            <small class="text-muted">Pengurangan stok dari sistem</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Detailed Products Table -->
                            <div class="section-header mt-6">
                                <h6>Detail Produk & Adjustment</h6>
                            </div>
                            <div class="mx-n6">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="width: 10%;">Kode Produk</th>
                                            <th style="width: 15%;">Nama Produk</th>
                                            <th style="width: 6%;">Unit</th>
                                            <th style="width: 8%;">Stock Sebelum</th>
                                            <th style="width: 10%;">Tipe Adjustment</th>
                                            <th style="width: 8%;">Nilai Adjustment</th>
                                            <th style="width: 8%;">Stock Final</th>
                                            <th style="width: 8%;">Harga Satuan</th>
                                            <th style="width: 10%;">Total Nilai</th>
                                            <th style="width: 17%;">Alasan & Detail</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($adjustmentItems as $item)
                                        @php
                                            // Calculate stock final
                                            $stockFinal = $item->stock_before + $item->adjustment_qty;

                                            // Determine adjustment type
                                            if ($item->adjustment_qty > 0) {
                                                $adjustmentType = 'Menambah';
                                                $adjustmentBadge = 'bg-label-success';
                                                $adjustmentIcon = 'tabler-arrow-up';
                                            } elseif ($item->adjustment_qty < 0) {
                                                $adjustmentType = 'Mengurangi';
                                                $adjustmentBadge = 'bg-label-danger';
                                                $adjustmentIcon = 'tabler-arrow-down';
                                            } else {
                                                $adjustmentType = 'Tidak Ada';
                                                $adjustmentBadge = 'bg-label-secondary';
                                                $adjustmentIcon = 'tabler-minus';
                                            }

                                            // Calculate adjustment value (absolute difference)
                                            $adjustmentValue = abs($item->adjustment_qty);
                                        @endphp
                                        <tr>
                                            <td>
                                                <a href="{{ route('master_data.product.detail', $item->product_code) }}" class="text-primary fw-semibold" title="Lihat detail produk">
                                                    <strong>{{ $item->product_code }}</strong>
                                                </a>
                                            </td>
                                            <td>{{ $item->product_name }}</td>
                                            <td class="text-center">{{ $item->unit }}</td>
                                            <td class="text-end">
                                                <strong>{{ number_format($item->stock_before, 0, ',', '.') }}</strong>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge {{ $adjustmentBadge }}">
                                                    <i class="icon-base ti {{ $adjustmentIcon }} me-1"></i>{{ $adjustmentType }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                @if($adjustmentValue > 0)
                                                    <strong>{{ number_format($adjustmentValue, 0, ',', '.') }}</strong>
                                                @else
                                                    <span class="text-muted">0</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <strong class="text-primary">{{ number_format($stockFinal, 0, ',', '.') }}</strong>
                                            </td>
                                            <td class="text-end">Rp {{ number_format($item->unit_cost, 0, ',', '.') }}</td>
                                            <td class="text-end">
                                                @if($item->total_value > 0)
                                                    <span class="text-success">+Rp {{ number_format($item->total_value, 0, ',', '.') }}</span>
                                                @elseif($item->total_value < 0)
                                                    <span class="text-danger">-Rp {{ number_format(abs($item->total_value), 0, ',', '.') }}</span>
                                                @else
                                                    <span class="text-muted">Rp 0</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="mb-1"><strong>Alasan:</strong> {{ $item->reason ?? '-' }}</div>
                                                @if(isset($item->batch_number) && $item->batch_number)
                                                    <small class="text-muted">Batch: {{ $item->batch_number }}</small>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Audit Log Tab -->
                        <div class="tab-pane fade" id="horizontal-history" role="tabpanel" aria-labelledby="history-list-item">
                            <div class="section-header mt-6">
                                <h6>Log Audit Stock Adjustment</h6>
                            </div>
                            <div class="mx-n6">
                                <table id="audit-log-table" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Tanggal & Waktu</th>
                                            <th>Pengguna</th>
                                            <th>Aksi</th>
                                            <th>Deskripsi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($auditLog))
                                            @foreach($auditLog as $log)
                                                <tr>
                                                    <td>{{ $log->created_at }}</td>
                                                    <td><a href="{{ route('master_data.user.detail', ['id' => $log->created_by]) }}" class="text-primary fw-semibold">{{ $log->user_name }}</a></td>
                                                    <td><span class="badge bg-label-{{ $log->action_type === 'CREATE' ? 'primary' : 'warning' }}">{{ $log->action_type }}</span></td>
                                                    <td>{{ $log->description }}</td>
                                                </tr>
                                            @endforeach
                                        @else
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
@endsection

@section('js_script')
<script>
$(function() {
    // Initialize DataTable for audit log
    $('#audit-log-table').DataTable({
        responsive: true,
        orderCellsTop: true,
        searching: false,
        paging: false,
        info: false,
        order: [[0, 'desc']] // Sort by date descending
    });

    function getActionBadgeClass(action) {
        switch(action) {
            case 'CREATED':
                return 'bg-primary';
            case 'UPDATED':
                return 'bg-warning';
            case 'STATUS_CHANGED':
                return 'bg-info';
            case 'APPROVED':
                return 'bg-success';
            case 'REJECTED':
                return 'bg-danger';
            case 'APPLIED':
                return 'bg-success';
            case 'CANCELLED':
                return 'bg-dark';
            default:
                return 'bg-secondary';
        }
    }

    // Action button handlers
    $('#approve-adjustment-btn').on('click', function() {
        Swal.fire({
            title: 'Setujui Stock Adjustment',
            text: 'Apakah Anda yakin ingin menyetujui stock adjustment ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Setujui',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                // In real implementation, send approval request
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Stock adjustment berhasil disetujui.',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                }).then(() => {
                    location.reload();
                });
            }
        });
    });

    $('#reject-adjustment-btn').on('click', function() {
        Swal.fire({
            title: 'Tolak Stock Adjustment',
            input: 'textarea',
            inputLabel: 'Alasan penolakan',
            inputPlaceholder: 'Masukkan alasan penolakan...',
            inputAttributes: {
                'aria-label': 'Alasan penolakan'
            },
            showCancelButton: true,
            confirmButtonText: 'Ya, Tolak',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false,
            inputValidator: (value) => {
                if (!value) {
                    return 'Alasan penolakan wajib diisi!'
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // In real implementation, send rejection request
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Stock adjustment berhasil ditolak.',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                }).then(() => {
                    location.reload();
                });
            }
        });
    });

    $('#apply-adjustment-btn').on('click', function() {
        Swal.fire({
            title: 'Terapkan Stock Adjustment',
            text: 'Apakah Anda yakin ingin menerapkan adjustment ini ke sistem inventory? Aksi ini tidak dapat dibatalkan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Terapkan',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                // In real implementation, apply adjustment to inventory
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Stock adjustment berhasil diterapkan ke sistem inventory.',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                }).then(() => {
                    location.reload();
                });
            }
        });
    });
});
</script>

<style>
.summary-card {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 1rem;
    text-align: center;
    margin-bottom: 1rem;
}

.summary-card.primary {
    background-color: #e3f2fd;
    border-color: #2196f3;
}

.summary-card.success {
    background-color: #e8f5e8;
    border-color: #4caf50;
}

.summary-card.warning {
    background-color: #fff3e0;
    border-color: #ff9800;
}

.summary-card.danger {
    background-color: #ffebee;
    border-color: #f44336;
}

.activity-feed {
    position: relative;
    padding-left: 0;
}

.feed-item {
    display: flex;
    position: relative;
    padding-bottom: 1.5rem;
    margin-bottom: 1rem;
}

.feed-item:not(:last-child)::after {
    content: '';
    position: absolute;
    left: 27px;
    top: 54px;
    width: 2px;
    height: calc(100% - 10px);
    background-color: #e3ebf0;
    z-index: 1;
}

.feed-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 54px;
    height: 54px;
    border-radius: 50%;
    flex-shrink: 0;
    margin-right: 1rem;
    position: relative;
    z-index: 2;
    border: 3px solid #fff;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.feed-icon i {
    font-size: 20px;
    color: #fff;
}

.feed-content {
    flex: 1;
    min-width: 0;
    padding-top: 0.25rem;
}

.feed-content h6 {
    margin-bottom: 0.25rem;
    font-weight: 600;
    font-size: 0.875rem;
}

.feed-content p {
    margin-bottom: 0.25rem;
    font-size: 0.8125rem;
    line-height: 1.4;
}

.feed-content small {
    font-size: 0.75rem;
    font-weight: 500;
}
</style>
@endsection