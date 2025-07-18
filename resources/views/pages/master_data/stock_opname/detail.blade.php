@extends('layouts.default')
@section('page_title')
    Detail Stock Opname - {{ $stockOpname['opname_number'] }}
@stop
@section('content')
    <div class="card mb-6">
        <div class="card-body">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <h4 class="mb-3">Detail Stock Opname</h4>
                </div>
                
                <!-- Action Panel -->
                <div class="col-md-4">
                    <div class="text-end">
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('master_data.stock_opname.edit', $stockOpname['id']) }}" class="btn btn-primary btn-sm">
                                <i class="icon-base ti tabler-edit me-1"></i>
                                Edit
                            </a>
                            @if($stockOpname['status'] === 'adjustment_pending')
                                <button type="button" class="btn btn-success btn-sm" id="create-adjustment-btn">
                                    <i class="icon-base ti tabler-adjustments me-1"></i>
                                    Buat Stok Adjustment
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Opname Title -->
            <h2>{{ $stockOpname['opname_number'] }}</h2>
            <span>
                <h6>Data di bawah berdasarkan tanggal {{ date('Y-m-d H:i:s') }}</h6>
            </span>
            <hr class="my-6 mx-n6" />

            <!-- Basic Information -->
            <div class="row g-6">
                <div class="col-md-6">
                    <label class="form-label">Nomor Stock Opname</label>
                    <input type="text" class="form-control" value="{{ $stockOpname['opname_number'] }}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Opname</label>
                    <input type="text" class="form-control" value="{{ date('d/m/Y', strtotime($stockOpname['opname_date'])) }}" disabled />
                </div>
            </div>
            <div class="row g-6 mt-3">
                <div class="col-md-6">
                    <label class="form-label">Gudang</label>
                    <input type="text" class="form-control" value="{{ $stockOpname['warehouse_name'] }}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Penanggung Jawab</label>
                    <input type="text" class="form-control" value="{{ $stockOpname['pic_name'] }}" disabled />
                </div>
            </div>
            <div class="row g-6 mt-3">
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <input type="text" class="form-control" value="{{ $stockOpname['status'] }}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nomor Adjustment</label>
                    <input type="text" class="form-control" value="{{ $stockOpname['adjustment_number'] ?? 'Belum dibuat' }}" disabled />
                </div>
            </div>
            <hr class="my-6 mx-n6" />

            <!-- Summary Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="summary-card primary">
                        <h6>Total Produk</h6>
                        <h4>{{ $stockOpname['total_products'] }}</h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card success">
                        <h6>Produk Terhitung</h6>
                        <h4>{{ $stockOpname['counted_products'] }}</h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card warning">
                        <h6>Selisih Qty</h6>
                        <h4>{{ $stockOpname['variance_qty'] > 0 ? '+' . $stockOpname['variance_qty'] : $stockOpname['variance_qty'] }}</h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card danger">
                        <h6>Selisih Nilai</h6>
                        <h4>{{ $stockOpname['variance_value'] > 0 ? '+' : '' }}{{ number_format($stockOpname['variance_value'], 0, ',', '.') }}</h4>
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
                        <a class="list-group-item list-group-item-action waves-effect" id="variances-list-item"
                            data-bs-toggle="list" href="#horizontal-variances" aria-selected="false" role="tab">
                            <b>Selisih Detail</b>
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
                                    <h6 class="mb-0">Daftar Produk Stock Opname</h6>
                                    <span class="badge bg-info">{{ $stockOpname['total_products'] }} Produk</span>
                                </div>
                                
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="width: 15%;">Kode Produk</th>
                                            <th style="width: 30%;">Nama Produk</th>
                                            <th style="width: 10%;">Unit</th>
                                            <th style="width: 10%;">Stok Sistem</th>
                                            <th style="width: 10%;">Qty Fisik</th>
                                            <th style="width: 10%;">Selisih</th>
                                            <th style="width: 15%;">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>AIR-330</td>
                                            <td>RIYO Water 330ml</td>
                                            <td>pcs</td>
                                            <td class="text-end">1,500</td>
                                            <td class="text-end">1,502</td>
                                            <td class="text-end">
                                                <span class="text-success">+2</span>
                                            </td>
                                            <td>Ada penambahan dari produksi</td>
                                        </tr>
                                        <tr>
                                            <td>AIR-600</td>
                                            <td>RIYO Water 600ml</td>
                                            <td>pcs</td>
                                            <td class="text-end">800</td>
                                            <td class="text-end">800</td>
                                            <td class="text-end">
                                                <span class="text-muted">0</span>
                                            </td>
                                            <td>Sesuai dengan sistem</td>
                                        </tr>
                                        <tr>
                                            <td>AIR-1500</td>
                                            <td>RIYO Water 1500ml</td>
                                            <td>pcs</td>
                                            <td class="text-end">400</td>
                                            <td class="text-end">400</td>
                                            <td class="text-end">
                                                <span class="text-muted">0</span>
                                            </td>
                                            <td>-</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Variances Tab -->
                        <div class="tab-pane fade" id="horizontal-variances" role="tabpanel">
                            <div id="variances-content">
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
    // Tab event handlers
    $('#variances-list-item').on('shown.bs.tab', function () {
        loadVariancesContent();
    });

    $('#history-list-item').on('shown.bs.tab', function () {
        loadHistoryContent();
    });

    // Load variances content
    function loadVariancesContent() {
        const content = `
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">Detail Selisih Stock Opname</h6>
                <span class="badge bg-warning">2 Item dengan Selisih</span>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="card border-success">
                        <div class="card-body">
                            <h6 class="card-title text-success">Selisih Positif</h6>
                            <p class="card-text">
                                <strong>Total Qty:</strong> +2 pcs<br>
                                <strong>Estimasi Nilai:</strong> +Rp 24.000
                            </p>
                            <small class="text-muted">Kelebihan stok dari perhitungan sistem</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-danger">
                        <div class="card-body">
                            <h6 class="card-title text-danger">Selisih Negatif</h6>
                            <p class="card-text">
                                <strong>Total Qty:</strong> 0 pcs<br>
                                <strong>Estimasi Nilai:</strong> Rp 0
                            </p>
                            <small class="text-muted">Kekurangan stok dari perhitungan sistem</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <h6>Produk dengan Selisih</h6>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Kode Produk</th>
                        <th>Nama Produk</th>
                        <th>Stok Sistem</th>
                        <th>Qty Fisik</th>
                        <th>Selisih</th>
                        <th>Nilai Estimasi</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>AIR-330</td>
                        <td>RIYO Water 330ml</td>
                        <td class="text-end">1,500</td>
                        <td class="text-end">1,502</td>
                        <td class="text-end">
                            <span class="text-success">+2</span>
                        </td>
                        <td class="text-end">
                            <span class="text-success">+Rp 24.000</span>
                        </td>
                        <td>Ada penambahan dari produksi</td>
                    </tr>
                </tbody>
            </table>
        `;
        $('#variances-content').html(content);
    }

    // Load history content
    function loadHistoryContent() {
        const content = `
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">Riwayat Stock Opname</h6>
                <span class="badge bg-info">{{ $stockOpname['status'] }}</span>
            </div>
            
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-marker bg-primary"></div>
                    <div class="timeline-content">
                        <h6>Stock Opname Dibuat</h6>
                        <p>{{ date('d/m/Y H:i', strtotime($stockOpname['created_at'])) }}</p>
                        <small class="text-muted">Oleh: {{ $stockOpname['pic_name'] }}</small>
                    </div>
                </div>
                
                <div class="timeline-item">
                    <div class="timeline-marker bg-warning"></div>
                    <div class="timeline-content">
                        <h6>Proses Perhitungan</h6>
                        <p>{{ date('d/m/Y H:i', strtotime($stockOpname['updated_at'])) }}</p>
                        <small class="text-muted">Status: Sedang Dikerjakan</small>
                    </div>
                </div>
                
                <div class="timeline-item">
                    <div class="timeline-marker bg-info"></div>
                    <div class="timeline-content">
                        <h6>Perhitungan Selesai</h6>
                        <p>{{ date('d/m/Y H:i', strtotime($stockOpname['updated_at'])) }}</p>
                        <small class="text-muted">Ditemukan selisih: {{ $stockOpname['variance_qty'] > 0 ? '+' . $stockOpname['variance_qty'] : $stockOpname['variance_qty'] }} qty</small>
                    </div>
                </div>
                
                @if($stockOpname['status'] === 'adjustment_pending')
                <div class="timeline-item">
                    <div class="timeline-marker bg-warning"></div>
                    <div class="timeline-content">
                        <h6>Menunggu Adjustment</h6>
                        <p>{{ date('d/m/Y H:i', strtotime($stockOpname['updated_at'])) }}</p>
                        <small class="text-muted">Status: Menunggu dibuat stock adjustment</small>
                    </div>
                </div>
                @endif
                
                @if($stockOpname['adjustment_number'])
                <div class="timeline-item">
                    <div class="timeline-marker bg-success"></div>
                    <div class="timeline-content">
                        <h6>Stock Adjustment Dibuat</h6>
                        <p>{{ date('d/m/Y H:i') }}</p>
                        <small class="text-muted">Nomor: {{ $stockOpname['adjustment_number'] }}</small>
                    </div>
                </div>
                @endif
            </div>
        `;
        $('#history-content').html(content);
    }

    // Create adjustment button
    $('#create-adjustment-btn').on('click', function() {
        Swal.fire({
            title: 'Buat Stok Adjustment?',
            text: 'Apakah Anda yakin ingin membuat stok adjustment dari hasil opname ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Buat Adjustment',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("master_data.stock_opname.create_adjustment", $stockOpname["id"]) }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: response.message + ' dengan nomor ' + response.adjustment_number,
                                icon: 'success',
                                customClass: {
                                    confirmButton: 'btn btn-primary'
                                },
                                buttonsStyling: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Gagal!',
                                text: response.message || 'Terjadi kesalahan saat membuat adjustment',
                                icon: 'error',
                                customClass: {
                                    confirmButton: 'btn btn-primary'
                                },
                                buttonsStyling: false
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat membuat adjustment',
                            icon: 'error',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        });
                    }
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

.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    padding-bottom: 20px;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: -25px;
    top: 20px;
    width: 2px;
    height: calc(100% - 10px);
    background-color: #dee2e6;
}

.timeline-marker {
    position: absolute;
    left: -30px;
    top: 5px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px;
}

.timeline-content h6 {
    margin-bottom: 5px;
    font-weight: 600;
}

.timeline-content p {
    margin-bottom: 5px;
    color: #6c757d;
}
</style>
@endsection