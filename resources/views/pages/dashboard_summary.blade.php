@extends('layouts.default')
@section('page_title')
    Pencatatan Keuangan
@stop
@section('content')
    <!-- Header -->
    <div class="mb-6">
        <h4 class="mb-1">Pencatatan Keuangan</h4>
        <p class="text-muted">Overview kinerja keuangan berdasarkan periode</p>
    </div>

    <!-- Filter Periode -->
    <div class="card mb-6">
        <div class="card-body">
            <form method="GET" action="{{ route('dashboard.summary') }}" id="filter-form">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">
                            <i class="icon-base ti tabler-calendar me-1"></i> Periode
                        </label>
                        <select name="period" id="period" class="form-select">
                            <option value="monthly" {{ $period == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                            <option value="weekly" {{ $period == 'weekly' ? 'selected' : '' }}>Mingguan</option>
                            <option value="custom" {{ $period == 'custom' ? 'selected' : '' }}>Custom Range</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Dari</label>
                        <input type="date" name="date_from" class="form-control" value="{{ $date_from }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Sampai</label>
                        <input type="date" name="date_to" class="form-control" value="{{ $date_to }}" required>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="icon-base ti tabler-filter me-1"></i> Apply Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-6">
        <div class="col-lg-4">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="flex-grow-1">
                            <p class="text-muted mb-2">Sales Pabrik</p>
                            <h3 class="mb-2">Rp {{ number_format($sales_pabrik, 0, ',', '.') }}</h3>
                            <small class="text-muted">Total penjualan produksi pada periode ini</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="icon-base ti tabler-trending-up icon-28px"></i>
                            </span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="#" class="btn btn-primary btn-sm" title="Lihat Detail">
                            <i class="icon-xs icon-base ti tabler-eye"></i> Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card card-border-shadow-warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="flex-grow-1">
                            <p class="text-muted mb-2">Biaya Pengeluaran</p>
                            <h3 class="mb-2">Rp {{ number_format($biaya_pengeluaran, 0, ',', '.') }}</h3>
                            <small class="text-muted">Total biaya operasional pada periode ini</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="icon-base ti tabler-wallet icon-28px"></i>
                            </span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="#" class="btn btn-warning btn-sm" title="Lihat Detail">
                            <i class="icon-xs icon-base ti tabler-eye"></i> Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card card-border-shadow-success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="flex-grow-1">
                            <p class="text-muted mb-2">Sales Total</p>
                            <h3 class="mb-2">Rp {{ number_format($sales_total, 0, ',', '.') }}</h3>
                            <small class="text-muted">Total penjualan keseluruhan pada periode ini</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="icon-base ti tabler-shopping-cart icon-28px"></i>
                            </span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="#" class="btn btn-success btn-sm" title="Lihat Detail">
                            <i class="icon-xs icon-base ti tabler-eye"></i> Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Panel Perhitungan Balanced -->
    <div class="card mb-6">
        <div class="card-body">
            <h5 class="card-title mb-4">Perhitungan Balanced</h5>
            <div class="text-center py-4">
                <div class="mb-4">
                    <span class="h5">Rp {{ number_format($sales_pabrik, 0, ',', '.') }}</span>
                    <span class="mx-2">+</span>
                    <span class="h5">Rp {{ number_format($biaya_pengeluaran, 0, ',', '.') }}</span>
                    <span class="mx-2">-</span>
                    <span class="h5">Rp {{ number_format($sales_total, 0, ',', '.') }}</span>
                    <span class="mx-2">=</span>
                </div>
                <div class="d-flex align-items-center justify-content-center gap-3 mb-4">
                    <h2 class="mb-0 {{ $result >= 0 ? 'text-success' : 'text-danger' }}">
                        Rp {{ number_format(abs($result), 0, ',', '.') }}
                    </h2>
                    @if($result >= 0)
                        <i class="icon-base ti tabler-arrow-up text-success icon-32px"></i>
                    @else
                        <i class="icon-base ti tabler-arrow-down text-danger icon-32px"></i>
                    @endif
                </div>
                <div>
                    <span class="badge {{ $result >= 0 ? 'bg-label-success' : 'bg-label-danger' }} px-4 py-2">
                        {{ $result >= 0 ? 'Keuntungan' : 'Perlu meningkatkan penjualan' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- <!-- Action Buttons -->
    <div class="row mb-6">
        <div class="col-lg-4">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                    <h6 class="card-title mb-2">Detail Sales Pabrik</h6>
                    <p class="text-muted small mb-3">Lihat breakdown lengkap penjualan produksi</p>
                    <button class="btn btn-sm btn-outline-primary">
                        <i class="icon-base ti tabler-eye me-1"></i> Lihat Detail
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card card-border-shadow-warning h-100">
                <div class="card-body">
                    <h6 class="card-title mb-2">Detail Biaya Pengeluaran</h6>
                    <p class="text-muted small mb-3">Lihat breakdown lengkap biaya operasional</p>
                    <button class="btn btn-sm btn-outline-warning">
                        <i class="icon-base ti tabler-eye me-1"></i> Lihat Detail
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card card-border-shadow-success h-100">
                <div class="card-body">
                    <h6 class="card-title mb-2">Detail Sales Total</h6>
                    <p class="text-muted small mb-3">Lihat breakdown lengkap penjualan keseluruhan</p>
                    <button class="btn btn-sm btn-outline-success">
                        <i class="icon-base ti tabler-eye me-1"></i> Lihat Detail
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Info -->
    <div class="card">
        <div class="card-body text-center">
            <p class="mb-1"><strong>Generated Summary Based on Selected Period</strong></p>
            <small class="text-muted">Dashboard Balanced membantu memantau kestabilan keuangan bisnis.</small>
        </div>
    </div> --}}
@stop

@section('js_script')
    <script>
        $(function() {
            // Auto-submit form when period changes
            $('#period').on('change', function() {
                const period = $(this).val();
                const today = new Date();

                if (period === 'monthly') {
                    // Set to current month
                    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
                    const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                    $('input[name="date_from"]').val(firstDay.toISOString().split('T')[0]);
                    $('input[name="date_to"]').val(lastDay.toISOString().split('T')[0]);
                } else if (period === 'weekly') {
                    // Set to current week (Monday to Sunday)
                    const day = today.getDay();
                    const diff = today.getDate() - day + (day === 0 ? -6 : 1);
                    const monday = new Date(today.setDate(diff));
                    const sunday = new Date(monday);
                    sunday.setDate(monday.getDate() + 6);
                    $('input[name="date_from"]').val(monday.toISOString().split('T')[0]);
                    $('input[name="date_to"]').val(sunday.toISOString().split('T')[0]);
                }
            });
        });
    </script>
@endsection
