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
                        @php
                            $totalActions = 1; // Edit always present
                            if ($stockOpname['status'] === 'ADJUSTMENT_PENDING') {
                                $totalActions++; // Add adjustment action
                            }
                        @endphp

                        @if ($totalActions < 3)
                            <!-- Show buttons directly when less than 3 actions -->
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="{{ route('master_data.stock_opname.edit', $stockOpname['id']) }}"
                                    class="btn btn-primary btn-md waves-effect waves-light">
                                    <i class="icon-base ti tabler-edit me-2"></i>
                                    Edit
                                </a>
                                @if ($stockOpname['status'] === 'ADJUSTMENT_PENDING')
                                    <button type="button" class="btn btn-success btn-md waves-effect waves-light"
                                        id="create-adjustment-btn">
                                        <i class="icon-base ti tabler-adjustments me-2"></i>
                                        Buat Stok Adjustment
                                    </button>
                                @endif
                            </div>
                        @else
                            <!-- Show dropdown for 3 or more actions -->
                            <div class="dropdown">
                                <button type="button"
                                    class="btn btn-primary btn-md waves-effect waves-light dropdown-toggle"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    Aksi
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item"
                                            href="{{ route('master_data.stock_opname.edit', $stockOpname['id']) }}">
                                            <i class="icon-base ti tabler-edit me-2"></i>Edit
                                        </a>
                                    </li>
                                    @if ($stockOpname['status'] === 'ADJUSTMENT_PENDING')
                                        <li>
                                            <a class="dropdown-item" href="javascript:void(0);" id="create-adjustment-btn">
                                                <i class="icon-base ti tabler-adjustments me-2"></i>Buat Stok Adjustment
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        @endif
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
                    <input type="text" class="form-control"
                        value="{{ date('d/m/Y', strtotime($stockOpname['opname_date'])) }}" disabled />
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
                    <input type="text" class="form-control"
                        value="{{ $stockOpname['adjustment_number'] ?? 'Belum dibuat' }}" disabled />
                </div>
            </div>
            <div class="row g-6 mt-3">
                <div class="col-md-6">
                    <label class="form-label">Deskripsi</label>
                    <input type="text" class="form-control" value="{{ $stockOpname['description'] ?? '' }}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Memo</label>
                    <input type="text" class="form-control" value="{{ $stockOpname['memo'] ?? '' }}" disabled />
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
                        <h4>{{ $stockOpname['variance_qty'] > 0 ? '+' . $stockOpname['variance_qty'] : $stockOpname['variance_qty'] }}
                        </h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card danger">
                        <h6>Selisih Nilai</h6>
                        <h4>{{ $stockOpname['variance_value'] > 0 ? '+' : '' }}{{ number_format($stockOpname['variance_value'], 0, ',', '.') }}
                        </h4>
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
                        {{-- <a class="list-group-item list-group-item-action waves-effect" id="variances-list-item"
                            data-bs-toggle="list" href="#horizontal-variances" aria-selected="false" role="tab">
                            <b>Selisih Detail</b>
                        </a> --}}
                        {{-- <a class="list-group-item list-group-item-action waves-effect" id="history-list-item"
                            data-bs-toggle="list" href="#horizontal-history" aria-selected="false" role="tab">
                            <b>Riwayat</b>
                        </a> --}}
                        <a class="list-group-item list-group-item-action waves-effect" id="audit-log-list-item"
                            data-bs-toggle="list" href="#horizontal-audit-log" aria-selected="false" role="tab">
                            <i class="icon-base ti tabler-history me-2"></i>
                            <b>Audit Log</b>
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
                                        @foreach ($productList as $product)
                                            <tr>
                                                <td>{{ $product['code'] }}</td>
                                                <td>{{ $product['name'] }}</td>
                                                <td>{{ $product['unit'] }}</td>
                                                <td class="text-end">
                                                    {{ number_format($product['system_stock'], 0, ',', '.') }}</td>
                                                <td class="text-end">
                                                    {{ number_format($product['physical_qty'], 0, ',', '.') }}</td>
                                                <td class="text-end">
                                                    @if ($product['variance'] > 0)
                                                        <span class="text-success">+{{ $product['variance'] }}</span>
                                                    @elseif($product['variance'] < 0)
                                                        <span class="text-danger">{{ $product['variance'] }}</span>
                                                    @else
                                                        <span class="text-muted">0</span>
                                                    @endif
                                                </td>
                                                <td>{{ $product['notes'] ?: '-' }}</td>
                                            </tr>
                                        @endforeach
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

                        <!-- Audit Log Tab -->
                        <div class="tab-pane fade" id="horizontal-audit-log" role="tabpanel">
                            <div class="section-header mt-6">
                                <h6>Log Audit Stock Opname</h6>
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
                                        @foreach ($auditLogs as $log)
                                            <tr>
                                                <td>{{ $log['created_at'] }}</td>
                                                <td><a href="{{ route('master_data.user.detail', ['id' => $log['user_id']]) }}"
                                                        class="text-primary fw-semibold">{{ $log['user_name'] }}</a></td>
                                                <td><span
                                                        class="badge bg-label-{{ $log['action_type'] === 'CREATE' ? 'success' : ($log['action_type'] === 'UPDATE' ? 'warning' : 'info') }}">{{ $log['action_type'] }}</span>
                                                </td>
                                                <td>{{ $log['description'] }}</td>
                                            </tr>
                                        @endforeach
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
            // Tab event handlers
            $('#variances-list-item').on('shown.bs.tab', function() {
                loadVariancesContent();
            });

            $('#history-list-item').on('shown.bs.tab', function() {
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
            
            <div class="activity-feed">
                <div class="feed-item">
                    <div class="feed-icon bg-label-primary">
                        <i class="icon-base ti tabler-clipboard-check"></i>
                    </div>
                    <div class="feed-content">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="mb-0">Stock Opname Dibuat</h6>
                            <small class="text-muted">{{ $stockOpname['created_at'] }}</small>
                        </div>
                        <p class="text-muted mb-1">Opname nomor {{ $stockOpname['opname_number'] }} berhasil dibuat</p>
                        <small class="text-primary">Oleh: {{ $stockOpname['pic_name'] }}</small>
                    </div>
                </div>
                
                <div class="feed-item">
                    <div class="feed-icon bg-label-info">
                        <i class="icon-base ti tabler-calculator"></i>
                    </div>
                    <div class="feed-content">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="mb-0">Perhitungan Stok Selesai</h6>
                            <small class="text-muted">{{ $stockOpname['updated_at'] }}</small>
                        </div>
                        <p class="text-muted mb-1">Hasil perhitungan: {{ $stockOpname['variant_products'] ?? $stockOpname['total_products'] }} varian produk</p>
                        <small class="text-info">Selisih: {{ $stockOpname['variance_qty'] > 0 ? '+' . $stockOpname['variance_qty'] : $stockOpname['variance_qty'] }} qty</small>
                    </div>
                </div>
                
                @if ($stockOpname['status'] === 'ADJUSTMENT_PENDING')
                <div class="feed-item">
                    <div class="feed-icon bg-label-warning">
                        <i class="icon-base ti tabler-hourglass"></i>
                    </div>
                    <div class="feed-content">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="mb-0">Menunggu Adjustment</h6>
                            <small class="text-muted">{{ $stockOpname['updated_at'] }}</small>
                        </div>
                        <p class="text-muted mb-1">Stock opname selesai, menunggu dibuat stock adjustment</p>
                        <small class="text-warning">Status: Menunggu Adjustment</small>
                    </div>
                </div>
                @endif
                
                @if ($stockOpname['adjustment_number'])
                <div class="feed-item">
                    <div class="feed-icon bg-label-success">
                        <i class="icon-base ti tabler-adjustments"></i>
                    </div>
                    <div class="feed-content">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="mb-0">Stock Adjustment Dibuat</h6>
                            <small class="text-muted">{{ date('Y-m-d H:i') }}</small>
                        </div>
                        <p class="text-muted mb-1">Stock adjustment berhasil dibuat dari hasil opname</p>
                        <small class="text-success">Nomor: {{ $stockOpname['adjustment_number'] }}</small>
                    </div>
                </div>
                @endif
                
                @if ($stockOpname['status'] === 'COMPLETED')
                <div class="feed-item">
                    <div class="feed-icon bg-label-success">
                        <i class="icon-base ti tabler-circle-check"></i>
                    </div>
                    <div class="feed-content">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="mb-0">Stock Opname Selesai</h6>
                            <small class="text-muted">{{ $stockOpname['updated_at'] }}</small>
                        </div>
                        <p class="text-muted mb-1">Proses stock opname telah selesai dan adjustment diterapkan</p>
                        <small class="text-success">Status: Selesai</small>
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
                            url: '{{ route('master_data.stock_opname.create_adjustment', $stockOpname['id']) }}',
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: response.message +
                                            ' dengan nomor ' + response
                                            .adjustment_number,
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
                                        text: response.message ||
                                            'Terjadi kesalahan saat membuat adjustment',
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

            // Initialize DataTable for audit log
            $('#audit-log-table').DataTable({
                responsive: true,
                orderCellsTop: true,
                searching: false,
                paging: false,
                info: false,
                order: [
                    [0, 'desc']
                ] // Sort by date descending
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
