@extends('layouts.default')
@section('page_title')
    Dashboard
@stop
@section('content')
    <!-- Financial Summary Cards -->
    <div class="row">
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="icon-base ti tabler-currency-dollar icon-28px"></i>
                            </span>
                        </div>
                        <div>
                            <h4 class="mb-0">{{ $summary_cards['total_revenue']['value'] }}</h4>
                            <small class="text-success">
                                <i class="ti tabler-arrow-up me-1"></i>
                                {{ $summary_cards['total_revenue']['change'] }}
                            </small>
                        </div>
                    </div>
                    <p class="mb-1">Total Pendapatan</p>
                    <small class="text-muted">Bulan ini</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="icon-base ti tabler-receipt icon-28px"></i>
                            </span>
                        </div>
                        <div>
                            <h4 class="mb-0">{{ $summary_cards['total_expenses']['value'] }}</h4>
                            <small class="text-warning">
                                <i class="ti tabler-arrow-up me-1"></i>
                                {{ $summary_cards['total_expenses']['change'] }}
                            </small>
                        </div>
                    </div>
                    <p class="mb-1">Total Pengeluaran</p>
                    <small class="text-muted">Bulan ini</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="icon-base ti tabler-trending-up icon-28px"></i>
                            </span>
                        </div>
                        <div>
                            <h4 class="mb-0">{{ $summary_cards['net_profit']['value'] }}</h4>
                            <small class="text-success">
                                <i class="ti tabler-arrow-up me-1"></i>
                                {{ $summary_cards['net_profit']['change'] }}
                            </small>
                        </div>
                    </div>
                    <p class="mb-1">Keuntungan Bersih</p>
                    <small class="text-muted">Bulan ini</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-danger h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-danger">
                                <i class="icon-base ti tabler-file-time icon-28px"></i>
                            </span>
                        </div>
                        <div>
                            <h4 class="mb-0">{{ $summary_cards['pending_invoices']['value'] }}</h4>
                            <small class="text-danger">
                                <i class="ti tabler-arrow-down me-1"></i>
                                {{ $summary_cards['pending_invoices']['change'] }}
                            </small>
                        </div>
                    </div>
                    <p class="mb-1">Invoice Tertunda</p>
                    <small class="text-muted">Menunggu pembayaran</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row mt-6">
        <!-- Recent Transactions -->
        <div class="col-lg-8 col-md-12">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Transaksi Terbaru</h5>
                    <a href="{{ route('main_transaction.jurnal.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="icon-base ti tabler-eye me-1"></i>
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Jenis</th>
                                    <th>Deskripsi</th>
                                    <th>Jumlah</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recent_transactions as $transaction)
                                <tr>
                                    <td>
                                        <span class="badge bg-label-secondary">{{ $transaction['id'] }}</span>
                                    </td>
                                    <td>
                                        @if($transaction['type'] == 'sales')
                                            <span class="badge bg-label-success">
                                                <i class="ti tabler-shopping-cart me-1"></i>
                                                Penjualan
                                            </span>
                                        @elseif($transaction['type'] == 'purchase')
                                            <span class="badge bg-label-primary">
                                                <i class="ti tabler-shopping-bag me-1"></i>
                                                Pembelian
                                            </span>
                                        @else
                                            <span class="badge bg-label-warning">
                                                <i class="ti tabler-receipt me-1"></i>
                                                Biaya
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ $transaction['description'] }}</td>
                                    <td class="text-end">{{ $transaction['amount'] }}</td>
                                    <td>{{ date('d/m/Y', strtotime($transaction['date'])) }}</td>
                                    <td>
                                        @if($transaction['status'] == 'completed')
                                            <span class="badge bg-success">Selesai</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-lg-4 col-md-12">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Aksi Cepat</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        <a href="{{ route('main_transaction.sales.add.so') }}" class="btn btn-primary">
                            <i class="icon-base ti tabler-plus me-2"></i>
                            Buat Penjualan
                        </a>
                        <a href="{{ route('main_transaction.purchase.add.po') }}" class="btn btn-info">
                            <i class="icon-base ti tabler-plus me-2"></i>
                            Buat Pembelian
                        </a>
                        <a href="{{ route('main_transaction.expense.add') }}" class="btn btn-warning">
                            <i class="icon-base ti tabler-plus me-2"></i>
                            Catat Biaya
                        </a>
                        <a href="{{ route('main_transaction.jurnal.add') }}" class="btn btn-secondary">
                            <i class="icon-base ti tabler-plus me-2"></i>
                            Jurnal Entry
                        </a>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('master_data.product.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="icon-base ti tabler-box me-2"></i>
                            Kelola Produk
                        </a>
                        <a href="{{ route('master_data.contact.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="icon-base ti tabler-users me-2"></i>
                            Kelola Kontak
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Row -->
    <div class="row mt-6">
        <!-- Top Products -->
        <div class="col-lg-6 col-md-12">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Produk Terlaris</h5>
                    <a href="{{ route('master_data.product.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="icon-base ti tabler-eye me-1"></i>
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body">
                    @foreach($top_products as $index => $product)
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar me-3">
                            <span class="avatar-initial rounded bg-label-primary">
                                {{ $index + 1 }}
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $product['name'] }}</h6>
                            <small class="text-muted">{{ $product['qty'] }} unit terjual</small>
                        </div>
                        <div class="text-end">
                            <span class="fw-semibold">{{ $product['sales'] }}</span>
                        </div>
                    </div>
                    @if($index < count($top_products) - 1)
                    <hr class="my-2">
                    @endif
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Low Stock Alert -->
        <div class="col-lg-6 col-md-12">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Stok Menipis</h5>
                    <a href="{{ route('master_data.product.index') }}" class="btn btn-sm btn-outline-danger">
                        <i class="icon-base ti tabler-alert-triangle me-1"></i>
                        Cek Stok
                    </a>
                </div>
                <div class="card-body">
                    @if(count($low_stock_items) > 0)
                        @foreach($low_stock_items as $index => $item)
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar me-3">
                                <span class="avatar-initial rounded bg-label-danger">
                                    <i class="icon-base ti tabler-alert-triangle"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $item['name'] }}</h6>
                                <small class="text-muted">Min: {{ $item['min_stock'] }} unit</small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-danger">{{ $item['current_stock'] }} unit</span>
                            </div>
                        </div>
                        @if($index < count($low_stock_items) - 1)
                        <hr class="my-2">
                        @endif
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="icon-base ti tabler-check-circle text-success mb-2" style="font-size: 3rem;"></i>
                            <p class="text-muted mb-0">Semua stok dalam kondisi baik</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop

@section('js_script')
<script>
    $(function() {
        // Auto-refresh dashboard data every 5 minutes
        setInterval(function() {
            // In a real implementation, this would refresh specific sections
            console.log('Dashboard auto-refresh triggered');
        }, 300000); // 5 minutes
        
        // Initialize tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();
    });
</script>
@endsection