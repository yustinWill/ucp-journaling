@extends('layouts.default')
@section('page_title')
    Detail Stock Adjustment - {{ $stockAdjustment['adjustment_number'] }}
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
                        if($stockAdjustment['status'] === 'PENDING_APPROVAL') {
                            $totalActions += 2; // Approve and Reject
                        }
                        if($stockAdjustment['status'] === 'APPROVED') {
                            $totalActions++; // Apply action
                        }
                    @endphp

                    @if($totalActions < 3)
                        <!-- Show buttons directly when less than 3 actions -->
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('main_transaction.stock_adjustment.edit', $stockAdjustment['id']) }}" class="btn btn-primary btn-md waves-effect waves-light">
                                <i class="icon-base ti tabler-edit me-2"></i>
                                Edit
                            </a>
                            @if($stockAdjustment['status'] === 'APPROVED')
                                <button type="button" class="btn btn-success btn-md waves-effect waves-light" id="apply-adjustment-btn">
                                    <i class="icon-base ti tabler-check me-2"></i>
                                    Terapkan ke Stok
                                </button>
                            @endif
                        </div>
                    @else
                        <!-- Show dropdown for 3 or more actions -->
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('main_transaction.stock_adjustment.detail', $stockAdjustment['id']) }}" class="btn btn-primary btn-md waves-effect waves-light">
                                <i class="icon-base ti tabler-eye me-2"></i>
                                Detail
                            </a>
                            <div class="dropdown">
                                <button type="button" class="btn btn-primary btn-md waves-effect waves-light dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    Aksi
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('main_transaction.stock_adjustment.edit', $stockAdjustment['id']) }}">
                                            <i class="icon-base ti tabler-edit me-2"></i>Edit
                                        </a>
                                    </li>
                                    @if($stockAdjustment['status'] === 'PENDING_APPROVAL')
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
                                    @if($stockAdjustment['status'] === 'APPROVED')
                                        <li>
                                            <a class="dropdown-item" href="javascript:void(0);" id="apply-adjustment-btn">
                                                <i class="icon-base ti tabler-check me-2"></i>Terapkan ke Stok
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Entity Title -->
            <h2>{{ $stockAdjustment['adjustment_number'] }}</h2>
            <span>
                <h6>Data di bawah berdasarkan tanggal {{ date('Y-m-d H:i:s') }}</h6>
            </span>
            <hr class="my-6 mx-n6" />

            <!-- Top-level Information -->
            <div class="row g-6">
                <div class="col-md-6">
                    <label class="form-label">Tanggal Adjustment</label>
                    <input type="text" class="form-control" value="{{ $stockAdjustment['adjustment_date'] }}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tipe Adjustment</label>
                    <input type="text" class="form-control" value="{{ $stockAdjustment['adjustment_type'] }}" disabled />
                </div>
            </div>
            <div class="row g-6">
                <div class="col-md-6">
                    <label class="form-label">Gudang</label>
                    <input type="text" class="form-control" value="{{ $stockAdjustment['warehouse_name'] }}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Penanggung Jawab</label>
                    <input type="text" class="form-control" value="{{ $stockAdjustment['pic_name'] }}" disabled />
                </div>
            </div>
            <div class="row g-6">
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <input type="text" class="form-control" value="{{ $stockAdjustment['status'] }}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nomor Referensi</label>
                    <input type="text" class="form-control" value="{{ $stockAdjustment['reference_number'] ?: '-' }}" disabled />
                </div>
            </div>
            <div class="row g-6">
                <div class="col-md-6">
                    <label class="form-label">Deskripsi</label>
                    <textarea class="form-control" disabled>{{ $stockAdjustment['description'] }}</textarea>
                </div>
            </div>
            <hr class="my-6 mx-n6" />

            <!-- Summary Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="summary-card primary">
                        <h6>Total Item</h6>
                        <h4>{{ $stockAdjustment['total_items'] }}</h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card {{ $stockAdjustment['total_adjustment_qty'] > 0 ? 'success' : ($stockAdjustment['total_adjustment_qty'] < 0 ? 'danger' : 'warning') }}">
                        <h6>Qty Adjustment</h6>
                        <h4>{{ $stockAdjustment['total_adjustment_qty'] > 0 ? '+' : '' }}{{ number_format($stockAdjustment['total_adjustment_qty'], 0, ',', '.') }}</h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card {{ $stockAdjustment['total_adjustment_value'] > 0 ? 'success' : ($stockAdjustment['total_adjustment_value'] < 0 ? 'danger' : 'warning') }}">
                        <h6>Nilai Adjustment</h6>
                        <h4>{{ $stockAdjustment['total_adjustment_value'] > 0 ? '+' : ($stockAdjustment['total_adjustment_value'] < 0 ? '-' : '') }}Rp {{ number_format(abs($stockAdjustment['total_adjustment_value']), 0, ',', '.') }}</h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card {{ $stockAdjustment['status'] === 'APPLIED' ? 'success' : ($stockAdjustment['status'] === 'PENDING_APPROVAL' ? 'warning' : 'primary') }}">
                        <h6>Status</h6>
                        <h4>{{ $stockAdjustment['status'] }}</h4>
                    </div>
                </div>
            </div>

            <!-- Tab System -->
            <div class="col-lg-12">
                <div class="demo-inline-spacing mt-4">
                    <div class="list-group list-group-horizontal-md text-md-center" role="tablist">
                        <a class="list-group-item list-group-item-action waves-effect active" id="products-list-item"
                            data-bs-toggle="list" href="#horizontal-products" aria-selected="true" role="tab">
                            <b>Daftar Produk</b>
                        </a>
                        <a class="list-group-item list-group-item-action waves-effect" id="adjustments-list-item"
                            data-bs-toggle="list" href="#horizontal-adjustments" aria-selected="false" role="tab">
                            <b>Detail Adjustment</b>
                        </a>
                        <a class="list-group-item list-group-item-action waves-effect" id="history-list-item"
                            data-bs-toggle="list" href="#horizontal-history" aria-selected="false" role="tab">
                            <b>Riwayat</b>
                        </a>
                    </div>
                    <div class="tab-content px-0 mt-0">
                        <!-- Products Tab -->
                        <div class="tab-pane fade show active" id="horizontal-products" role="tabpanel">
                            <div id="products-content">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">Daftar Produk Stock Adjustment</h6>
                                    <span class="badge bg-info">{{ $stockAdjustment['total_items'] }} Produk</span>
                                </div>
                                
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="width: 15%;">Kode Produk</th>
                                            <th style="width: 25%;">Nama Produk</th>
                                            <th style="width: 8%;">Unit</th>
                                            <th style="width: 12%;">Stok Sistem</th>
                                            <th style="width: 12%;">Qty Adjustment</th>
                                            <th style="width: 12%;">Harga Satuan</th>
                                            <th style="width: 16%;">Total Nilai</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($adjustmentItems as $item)
                                        <tr>
                                            <td>
                                                <a href="{{ route('master_data.product.detail', $item['product_code']) }}" class="text-primary fw-semibold" title="Lihat detail produk">
                                                    <strong>{{ $item['product_code'] }}</strong>
                                                </a>
                                            </td>
                                            <td>{{ $item['product_name'] }}</td>
                                            <td>{{ $item['unit'] }}</td>
                                            <td class="text-end">{{ number_format($item['current_stock'], 0, ',', '.') }}</td>
                                            <td class="text-end">
                                                @if($item['adjustment_qty'] > 0)
                                                    <span class="text-success">+{{ number_format($item['adjustment_qty'], 0, ',', '.') }}</span>
                                                @elseif($item['adjustment_qty'] < 0)
                                                    <span class="text-danger">{{ number_format($item['adjustment_qty'], 0, ',', '.') }}</span>
                                                @else
                                                    <span class="text-muted">0</span>
                                                @endif
                                            </td>
                                            <td class="text-end">Rp {{ number_format($item['unit_cost'], 0, ',', '.') }}</td>
                                            <td class="text-end">
                                                @if($item['total_value'] > 0)
                                                    <span class="text-success">+Rp {{ number_format($item['total_value'], 0, ',', '.') }}</span>
                                                @elseif($item['total_value'] < 0)
                                                    <span class="text-danger">-Rp {{ number_format(abs($item['total_value']), 0, ',', '.') }}</span>
                                                @else
                                                    <span class="text-muted">Rp 0</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Adjustments Detail Tab -->
                        <div class="tab-pane fade" id="horizontal-adjustments" role="tabpanel">
                            <div id="adjustments-content">
                                <!-- Content will be loaded here -->
                            </div>
                        </div>

                        <!-- History Tab -->
                        <div class="tab-pane fade" id="horizontal-history" role="tabpanel">
                            <div id="history-content">
                                <!-- Content will be loaded here -->
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
    // Adjustment data
    const adjustmentData = @json($stockAdjustment);
    const adjustmentItems = @json($adjustmentItems);

    // Tab event handlers
    $('#adjustments-list-item').on('shown.bs.tab', function () {
        loadAdjustmentsContent();
    });

    $('#history-list-item').on('shown.bs.tab', function () {
        loadHistoryContent();
    });

    // Load adjustment details content
    function loadAdjustmentsContent() {
        let content = `
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">Detail Adjustment per Produk</h6>
                <span class="badge bg-warning">${adjustmentData.total_items} Item Adjustment</span>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="card border-success">
                        <div class="card-body">
                            <h6 class="card-title text-success">Adjustment Positif</h6>
                            <p class="card-text">
                                <strong>Total Qty:</strong> +${Math.max(0, adjustmentData.total_adjustment_qty).toLocaleString('id-ID')}<br>
                                <strong>Estimasi Nilai:</strong> ${adjustmentData.total_adjustment_value > 0 ? '+Rp ' + adjustmentData.total_adjustment_value.toLocaleString('id-ID') : 'Rp 0'}
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
                                <strong>Total Qty:</strong> ${Math.min(0, adjustmentData.total_adjustment_qty).toLocaleString('id-ID')}<br>
                                <strong>Estimasi Nilai:</strong> ${adjustmentData.total_adjustment_value < 0 ? '-Rp ' + Math.abs(adjustmentData.total_adjustment_value).toLocaleString('id-ID') : 'Rp 0'}
                            </p>
                            <small class="text-muted">Pengurangan stok dari sistem</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <h6>Detail Alasan per Produk</h6>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Kode Produk</th>
                        <th>Nama Produk</th>
                        <th>Qty Adjustment</th>
                        <th>Nilai Adjustment</th>
                        <th>Alasan</th>
                        <th>Batch Number</th>
                    </tr>
                </thead>
                <tbody>
        `;
        
        adjustmentItems.forEach(function(item) {
            const qtyClass = item.adjustment_qty > 0 ? 'text-success' : (item.adjustment_qty < 0 ? 'text-danger' : 'text-muted');
            const valueClass = item.total_value > 0 ? 'text-success' : (item.total_value < 0 ? 'text-danger' : 'text-muted');
            const qtyDisplay = item.adjustment_qty > 0 ? `+${item.adjustment_qty.toLocaleString('id-ID')}` : item.adjustment_qty.toLocaleString('id-ID');
            const valueDisplay = item.total_value > 0 ? `+Rp ${item.total_value.toLocaleString('id-ID')}` : 
                               item.total_value < 0 ? `-Rp ${Math.abs(item.total_value).toLocaleString('id-ID')}` : 
                               'Rp 0';
                               
            const productDetailUrl = "{{ route('master_data.product.detail', ':code') }}".replace(':code', item.product_code);
            content += `
                <tr>
                    <td>
                        <a href="${productDetailUrl}" class="text-primary fw-semibold" title="Lihat detail produk">
                            <strong>${item.product_code}</strong>
                        </a>
                    </td>
                    <td>${item.product_name}</td>
                    <td class="text-end">
                        <span class="${qtyClass}">${qtyDisplay}</span>
                    </td>
                    <td class="text-end">
                        <span class="${valueClass}">${valueDisplay}</span>
                    </td>
                    <td>${item.reason || '-'}</td>
                    <td>${item.batch_number || '-'}</td>
                </tr>
            `;
        });

        content += `
                </tbody>
            </table>
        `;

        $('#adjustments-content').html(content);
    }

    // Load history content using activity feed pattern
    function loadHistoryContent() {
        const content = `
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">Riwayat Stock Adjustment</h6>
                <span class="badge bg-info">${adjustmentData.status}</span>
            </div>
            
            <div class="activity-feed">
                <div class="feed-item">
                    <div class="feed-icon bg-label-primary">
                        <i class="icon-base ti tabler-adjustments"></i>
                    </div>
                    <div class="feed-content">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="mb-0">Stock Adjustment Dibuat</h6>
                            <small class="text-muted">${adjustmentData.created_at}</small>
                        </div>
                        <p class="text-muted mb-1">Adjustment nomor ${adjustmentData.adjustment_number} berhasil dibuat</p>
                        <small class="text-primary">Oleh: ${adjustmentData.pic_name}</small>
                    </div>
                </div>
                
                <div class="feed-item">
                    <div class="feed-icon bg-label-info">
                        <i class="icon-base ti tabler-edit"></i>
                    </div>
                    <div class="feed-content">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="mb-0">Data Adjustment Diisi</h6>
                            <small class="text-muted">${adjustmentData.updated_at}</small>
                        </div>
                        <p class="text-muted mb-1">Ditambahkan ${adjustmentData.total_items} produk untuk adjustment</p>
                        <small class="text-info">Total qty: ${adjustmentData.total_adjustment_qty > 0 ? '+' : ''}${adjustmentData.total_adjustment_qty}</small>
                    </div>
                </div>
                
                ${adjustmentData.status !== 'DRAFT' ? `
                <div class="feed-item">
                    <div class="feed-icon bg-label-warning">
                        <i class="icon-base ti tabler-clock"></i>
                    </div>
                    <div class="feed-content">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="mb-0">Menunggu Persetujuan</h6>
                            <small class="text-muted">${adjustmentData.updated_at}</small>
                        </div>
                        <p class="text-muted mb-1">Stock adjustment disubmit untuk persetujuan</p>
                        <small class="text-warning">Status: Pending Approval</small>
                    </div>
                </div>
                ` : ''}
                
                ${adjustmentData.approved_by ? `
                <div class="feed-item">
                    <div class="feed-icon bg-label-success">
                        <i class="icon-base ti tabler-check"></i>
                    </div>
                    <div class="feed-content">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="mb-0">Adjustment Disetujui</h6>
                            <small class="text-muted">${adjustmentData.approved_date}</small>
                        </div>
                        <p class="text-muted mb-1">Stock adjustment telah disetujui</p>
                        <small class="text-success">Oleh: ${adjustmentData.approved_by}</small>
                    </div>
                </div>
                ` : ''}
                
                ${adjustmentData.status === 'APPLIED' ? `
                <div class="feed-item">
                    <div class="feed-icon bg-label-success">
                        <i class="icon-base ti tabler-circle-check"></i>
                    </div>
                    <div class="feed-content">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="mb-0">Adjustment Diterapkan</h6>
                            <small class="text-muted">${adjustmentData.approved_date}</small>
                        </div>
                        <p class="text-muted mb-1">Stock adjustment telah diterapkan ke sistem inventory</p>
                        <small class="text-success">Status: Applied</small>
                    </div>
                </div>
                ` : ''}
            </div>
        `;
        $('#history-content').html(content);
    }

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