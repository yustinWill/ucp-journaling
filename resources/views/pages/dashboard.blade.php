@extends('layouts.default')
@section('page_title')
    Dashboard
@stop
@section('content')
    <!-- Filter 1: Periode Bulan (SO, SD, Ongkir, SI, Penjualan Harian) -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <div class="d-flex align-items-center gap-2">
                            <i class="icon-base ti tabler-calendar-month"></i>
                            <span class="fw-semibold">Periode:</span>
                        </div>
                        <div>
                            <input type="month" id="dashboard-filter-month" class="form-control form-control-sm"
                                value="{{ $filterMonth }}" style="min-width: 160px;" />
                        </div>
                        <small class="text-muted">Berlaku untuk: Total SO, Sales Delivery, Ongkir, Sales Invoice, Penjualan Harian</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 1: SO / SD / Ongkir / SI Cards -->
    <div class="row g-4 mb-4">
        <!-- Total SO -->
        <div class="col-lg-3 col-sm-6">
            <a href="{{ route('main_transaction.sales_order.index') }}" class="text-decoration-none">
                <div class="card card-border-shadow-primary h-100 card-hover">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="icon-base ti tabler-file-text icon-28px"></i>
                                </span>
                            </div>
                            <div>
                                <h4 class="mb-0">{{ $soTotal }}</h4>
                            </div>
                        </div>
                        <p class="mb-0 fw-semibold">Total Sales Order</p>
                        <small class="text-muted">Klik untuk melihat daftar SO</small>
                    </div>
                </div>
            </a>
        </div>

        <!-- Total SD -->
        <div class="col-lg-3 col-sm-6">
            <a href="{{ route('main_transaction.sales_delivery.index') }}" class="text-decoration-none">
                <div class="card card-border-shadow-warning h-100 card-hover">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded bg-label-warning">
                                    <i class="icon-base ti tabler-truck icon-28px"></i>
                                </span>
                            </div>
                            <div>
                                <h4 class="mb-0">
                                    <span class="text-warning">{{ $sdNonConfirm }}</span>
                                    <span class="text-muted fs-6">/</span>
                                    <span class="text-success">{{ $sdConfirm }}</span>
                                </h4>
                            </div>
                        </div>
                        <p class="mb-0 fw-semibold">Sales Delivery</p>
                        <small class="text-muted">
                            <span class="text-warning">Dikirim</span> /
                            <span class="text-success">Terkonfirmasi</span>
                        </small>
                    </div>
                </div>
            </a>
        </div>

        <!-- Ongkir -->
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-info h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="icon-base ti tabler-package icon-28px"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-0">
                                <span class="text-success">Rp {{ number_format($ongkirApproved, 0, ',', '.') }}</span>
                            </h6>
                            <h6 class="mb-0">
                                <span class="text-secondary">Rp {{ number_format($ongkirTotal, 0, ',', '.') }}</span>
                            </h6>
                        </div>
                    </div>
                    <p class="mb-0 fw-semibold">Ongkir</p>
                    <small class="text-muted">
                        <span class="text-success">Approved</span> /
                        <span class="text-secondary">Total</span>
                    </small>
                </div>
            </div>
        </div>

        <!-- Total SI -->
        <div class="col-lg-3 col-sm-6">
            <a href="{{ route('main_transaction.sales_invoice.index') }}" class="text-decoration-none">
                <div class="card card-border-shadow-success h-100 card-hover">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded bg-label-success">
                                    <i class="icon-base ti tabler-receipt icon-28px"></i>
                                </span>
                            </div>
                            <div>
                                <h6 class="mb-0">
                                    <span class="text-warning">{{ $siExpected }}</span>
                                </h6>
                                <h6 class="mb-0">
                                    <span class="text-success">{{ $siSettled }}</span>
                                </h6>
                            </div>
                        </div>
                        <p class="mb-0 fw-semibold">Sales Invoice</p>
                        <small class="text-muted">
                            <span class="text-warning">Expected</span> /
                            <span class="text-success">Settled</span>
                        </small>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Section 2: Daily Sales Bar Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Penjualan Harian</h5>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-primary active" id="chart-mode-qty">Qty</button>
                        <button type="button" class="btn btn-outline-primary" id="chart-mode-amount">Amount</button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="daily-sales-chart" style="height: 350px;"></div>
                    <small class="text-muted d-block mt-2">
                        <i class="ti tabler-info-circle me-1"></i>
                        Klik bar untuk melihat detail Sales Order pada tanggal tersebut
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 3: Top Up Cards -->
    <div class="row g-4 mb-4">
        <div class="col-lg-6 col-sm-6">
            <a href="{{ route('main_transaction.topup.index') }}?status=unpaid" class="text-decoration-none">
                <div class="card card-border-shadow-danger h-100 card-hover">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded bg-label-danger">
                                    <i class="icon-base ti tabler-clock-dollar icon-28px"></i>
                                </span>
                            </div>
                            <div>
                                <h4 class="mb-0">{{ $topupBelumBayar }}</h4>
                            </div>
                        </div>
                        <p class="mb-0 fw-semibold">Top Up Belum Dibayar</p>
                        <small class="text-muted">Lifetime - Klik untuk melihat detail</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-6 col-sm-6">
            <a href="{{ route('main_transaction.topup.index') }}?status=paid" class="text-decoration-none">
                <div class="card card-border-shadow-success h-100 card-hover">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded bg-label-success">
                                    <i class="icon-base ti tabler-circle-check icon-28px"></i>
                                </span>
                            </div>
                            <div>
                                <h4 class="mb-0">{{ $topupTerbayar }}</h4>
                            </div>
                        </div>
                        <p class="mb-0 fw-semibold">Top Up Terbayar</p>
                        <small class="text-muted">Lifetime - Klik untuk melihat detail</small>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Section 4: Target Keuntungan (Revenue Split) -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="card-title mb-0">Target Keuntungan</h5>
                    <div style="min-width: 280px;">
                        <select class="form-select form-select-sm" id="revenue-split-select">
                            @foreach($revenueSplitOptions as $option)
                                <option value="{{ $option['id'] }}" {{ $option['id'] == $selectedCycleId ? 'selected' : '' }}>{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    @if($activeRevenueSplit)
                        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                            <div>
                                <span class="badge bg-label-success me-2">{{ $activeRevenueSplit->status }}</span>
                                <span class="fw-semibold">{{ $activeRevenueSplit->cycle_name }}</span>
                                <span class="text-muted ms-2">
                                    ({{ date('d M Y', strtotime($activeRevenueSplit->start_date)) }} - {{ date('d M Y', strtotime($activeRevenueSplit->end_date)) }})
                                </span>
                            </div>
                            <a href="{{ route('master_data.revenue_split.detail', $activeRevenueSplit->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="ti tabler-eye me-1"></i> Lihat Detail
                            </a>
                        </div>

                        <div class="row g-4">
                            <!-- Pabrik -->
                            <div class="col-lg-4 col-sm-6">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="icon-base ti tabler-building-factory me-2" style="font-size: 1.5rem;"></i>
                                            <h6 class="text-white mb-0">Pabrik</h6>
                                        </div>
                                        <h4 class="text-white mb-1">Rp {{ number_format($activeRevenueSplit->current_pabrik, 0, ',', '.') }}</h4>
                                        <small class="text-white-50">dari Rp {{ number_format($activeRevenueSplit->target_pabrik, 0, ',', '.') }}</small>
                                        <div class="progress mt-3" style="height: 8px; background: rgba(255,255,255,0.2);">
                                            <div class="progress-bar bg-white" style="width: {{ $activeRevenueSplit->target_pabrik > 0 ? min(round(($activeRevenueSplit->current_pabrik / $activeRevenueSplit->target_pabrik) * 100), 100) : 0 }}%"></div>
                                        </div>
                                        <small class="text-white-50 mt-1 d-block">{{ $activeRevenueSplit->target_pabrik > 0 ? round(($activeRevenueSplit->current_pabrik / $activeRevenueSplit->target_pabrik) * 100) : 0 }}%</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Owner -->
                            <div class="col-lg-4 col-sm-6">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="icon-base ti tabler-user-circle me-2" style="font-size: 1.5rem;"></i>
                                            <h6 class="text-white mb-0">Owner</h6>
                                        </div>
                                        <h4 class="text-white mb-1">Rp {{ number_format($activeRevenueSplit->current_owner, 0, ',', '.') }}</h4>
                                        <small class="text-white-50">dari Rp {{ number_format($activeRevenueSplit->target_owner, 0, ',', '.') }}</small>
                                        <div class="progress mt-3" style="height: 8px; background: rgba(255,255,255,0.2);">
                                            <div class="progress-bar bg-white" style="width: {{ $activeRevenueSplit->target_owner > 0 ? min(round(($activeRevenueSplit->current_owner / $activeRevenueSplit->target_owner) * 100), 100) : 0 }}%"></div>
                                        </div>
                                        <small class="text-white-50 mt-1 d-block">{{ $activeRevenueSplit->target_owner > 0 ? round(($activeRevenueSplit->current_owner / $activeRevenueSplit->target_owner) * 100) : 0 }}%</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Kingdom -->
                            <div class="col-lg-4 col-sm-6">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="icon-base ti tabler-building me-2" style="font-size: 1.5rem;"></i>
                                            <h6 class="text-white mb-0">Kingdom</h6>
                                        </div>
                                        <h4 class="text-white mb-1">Rp {{ number_format($activeRevenueSplit->current_perusahaan, 0, ',', '.') }}</h4>
                                        <small class="text-white-50">dari Rp {{ number_format($activeRevenueSplit->target_perusahaan, 0, ',', '.') }}</small>
                                        <div class="progress mt-3" style="height: 8px; background: rgba(255,255,255,0.2);">
                                            <div class="progress-bar bg-white" style="width: {{ $activeRevenueSplit->target_perusahaan > 0 ? min(round(($activeRevenueSplit->current_perusahaan / $activeRevenueSplit->target_perusahaan) * 100), 100) : 0 }}%"></div>
                                        </div>
                                        <small class="text-white-50 mt-1 d-block">{{ $activeRevenueSplit->target_perusahaan > 0 ? round(($activeRevenueSplit->current_perusahaan / $activeRevenueSplit->target_perusahaan) * 100) : 0 }}%</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Owner & Kingdom Breakdown -->
                        <div class="row g-4 mt-2">
                            <!-- Rincian Owner -->
                            <div class="col-lg-6">
                                <div class="card h-100">
                                    <div class="card-header py-2">
                                        <h6 class="card-title mb-0">
                                            <i class="icon-base ti tabler-user-circle me-1 text-success"></i> Total Owner
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        @php
                                            $ownerNet = $activeRevenueSplit->current_owner - $activeRevenueSplit->owner_expense;
                                        @endphp
                                        <div class="mb-3">
                                            <div class="fs-4 fw-bold {{ $ownerNet >= 0 ? 'text-success' : 'text-danger' }}">
                                                Rp {{ number_format($ownerNet, 0, ',', '.') }}
                                            </div>
                                            <small class="text-muted">Pemasukan - Pengeluaran</small>
                                        </div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="text-muted">Pemasukan</span>
                                            <span class="fw-semibold text-success">Rp {{ number_format($activeRevenueSplit->current_owner, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-3">
                                            <span class="text-muted">Pengeluaran</span>
                                            <span class="fw-semibold text-danger">Rp {{ number_format($activeRevenueSplit->owner_expense, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Rincian Kingdom -->
                            <div class="col-lg-6">
                                <div class="card h-100">
                                    <div class="card-header py-2">
                                        <h6 class="card-title mb-0">
                                            <i class="icon-base ti tabler-building me-1 text-info"></i> Total Kingdom
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        @php
                                            $kingdomNet = $activeRevenueSplit->current_perusahaan - $activeRevenueSplit->kingdom_expense;
                                        @endphp
                                        <div class="mb-3">
                                            <div class="fs-4 fw-bold {{ $kingdomNet >= 0 ? 'text-success' : 'text-danger' }}">
                                                Rp {{ number_format($kingdomNet, 0, ',', '.') }}
                                            </div>
                                            <small class="text-muted">Pemasukan - Pengeluaran</small>
                                        </div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="text-muted">Pemasukan</span>
                                            <span class="fw-semibold text-success">Rp {{ number_format($activeRevenueSplit->current_perusahaan, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-3">
                                            <span class="text-muted">Pengeluaran</span>
                                            <span class="fw-semibold text-danger">Rp {{ number_format($activeRevenueSplit->kingdom_expense, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="icon-base ti tabler-chart-pie text-muted mb-3" style="font-size: 3rem;"></i>
                            <p class="text-muted mb-0">Target keuntungan belum disetup untuk tanggal ini</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Section 5: Pengeluaran vs Anggaran - Owner & Kingdom -->
    @if($budgetOwner['data'] || $budgetKingdom['data'])
    <div class="row g-4 mb-4">
        <!-- Owner Budget -->
        @if($budgetOwner['data'])
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="card-title mb-0">
                        <i class="icon-base ti tabler-user-circle me-1 text-success"></i> Pengeluaran vs Anggaran Owner
                    </h5>
                    <div style="min-width: 220px;">
                        <select class="form-select form-select-sm" id="budget-owner-select">
                            @foreach($budgetOwner['options'] as $option)
                                <option value="{{ $option['id'] }}" {{ $option['id'] == $budgetOwner['selectedId'] ? 'selected' : '' }}>{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                        <span class="text-muted">
                            {{ date('d M Y', strtotime($budgetOwner['data']->period_start_date)) }} - {{ date('d M Y', strtotime($budgetOwner['data']->period_end_date)) }}
                        </span>
                        <a href="{{ route('master_data.budged.detail', $budgetOwner['data']->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="ti tabler-eye me-1"></i> Detail
                        </a>
                    </div>
                    @php
                        $ownerTotalUsed = collect($budgetOwner['data']->categories)->sum('used');
                        $ownerTotalLimit = collect($budgetOwner['data']->categories)->sum('limit');
                        $ownerTotalPct = $ownerTotalLimit > 0 ? round(($ownerTotalUsed / $ownerTotalLimit) * 100) : 0;
                        $ownerTotalLeft = 100 - $ownerTotalPct;
                    @endphp
                    <div class="p-3 mb-3 rounded" style="background: #f8f9fa; border: 1px solid #e9ecef;">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Pengeluaran: <span class="text-dark fw-bold">Rp {{ number_format($ownerTotalUsed, 0, ',', '.') }}</span></span>
                            <span class="text-muted">Anggaran: <span class="text-dark fw-bold">Rp {{ number_format($ownerTotalLimit, 0, ',', '.') }}</span></span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between">
                            <span class="fw-semibold {{ $ownerTotalPct >= 90 ? 'text-danger' : ($ownerTotalPct >= 70 ? 'text-warning' : 'text-success') }}">{{ $ownerTotalPct }}% used</span>
                            <span class="fw-semibold text-muted">{{ max(0, $ownerTotalLeft) }}% anggaran left</span>
                        </div>
                    </div>
                    @foreach($budgetOwner['data']->categories as $cat)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-semibold">{{ $cat->name }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted">Pengeluaran: <span class="text-dark fw-semibold">Rp {{ number_format($cat->used, 0, ',', '.') }}</span></small>
                            <small class="text-muted">Anggaran: <span class="text-dark fw-semibold">Rp {{ number_format($cat->limit, 0, ',', '.') }}</span></small>
                        </div>
                        @php $pct = $cat->limit > 0 ? round(($cat->used / $cat->limit) * 100) : 0; @endphp
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar {{ $pct >= 90 ? 'bg-danger' : ($pct >= 70 ? 'bg-warning' : 'bg-success') }}"
                                 style="width: {{ min($pct, 100) }}%"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-1">
                            <small class="text-muted">{{ $pct }}% terpakai</small>
                            <small class="text-muted">Sisa: Rp {{ number_format($cat->limit - $cat->used, 0, ',', '.') }}</small>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Kingdom Budget -->
        @if($budgetKingdom['data'])
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="card-title mb-0">
                        <i class="icon-base ti tabler-building me-1 text-info"></i> Pengeluaran vs Anggaran Kingdom
                    </h5>
                    <div style="min-width: 220px;">
                        <select class="form-select form-select-sm" id="budget-kingdom-select">
                            @foreach($budgetKingdom['options'] as $option)
                                <option value="{{ $option['id'] }}" {{ $option['id'] == $budgetKingdom['selectedId'] ? 'selected' : '' }}>{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                        <span class="text-muted">
                            {{ date('d M Y', strtotime($budgetKingdom['data']->period_start_date)) }} - {{ date('d M Y', strtotime($budgetKingdom['data']->period_end_date)) }}
                        </span>
                        <a href="{{ route('master_data.budged.detail', $budgetKingdom['data']->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="ti tabler-eye me-1"></i> Detail
                        </a>
                    </div>
                    @php
                        $kingdomTotalUsed = collect($budgetKingdom['data']->categories)->sum('used');
                        $kingdomTotalLimit = collect($budgetKingdom['data']->categories)->sum('limit');
                        $kingdomTotalPct = $kingdomTotalLimit > 0 ? round(($kingdomTotalUsed / $kingdomTotalLimit) * 100) : 0;
                        $kingdomTotalLeft = 100 - $kingdomTotalPct;
                    @endphp
                    <div class="p-3 mb-3 rounded" style="background: #f8f9fa; border: 1px solid #e9ecef;">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Pengeluaran: <span class="text-dark fw-bold">Rp {{ number_format($kingdomTotalUsed, 0, ',', '.') }}</span></span>
                            <span class="text-muted">Anggaran: <span class="text-dark fw-bold">Rp {{ number_format($kingdomTotalLimit, 0, ',', '.') }}</span></span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between">
                            <span class="fw-semibold {{ $kingdomTotalPct >= 90 ? 'text-danger' : ($kingdomTotalPct >= 70 ? 'text-warning' : 'text-success') }}">{{ $kingdomTotalPct }}% used</span>
                            <span class="fw-semibold text-muted">{{ max(0, $kingdomTotalLeft) }}% anggaran left</span>
                        </div>
                    </div>
                    @foreach($budgetKingdom['data']->categories as $cat)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-semibold">{{ $cat->name }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted">Pengeluaran: <span class="text-dark fw-semibold">Rp {{ number_format($cat->used, 0, ',', '.') }}</span></small>
                            <small class="text-muted">Anggaran: <span class="text-dark fw-semibold">Rp {{ number_format($cat->limit, 0, ',', '.') }}</span></small>
                        </div>
                        @php $pct = $cat->limit > 0 ? round(($cat->used / $cat->limit) * 100) : 0; @endphp
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar {{ $pct >= 90 ? 'bg-danger' : ($pct >= 70 ? 'bg-warning' : 'bg-success') }}"
                                 style="width: {{ min($pct, 100) }}%"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-1">
                            <small class="text-muted">{{ $pct }}% terpakai</small>
                            <small class="text-muted">Sisa: Rp {{ number_format($cat->limit - $cat->used, 0, ',', '.') }}</small>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    <!-- Section 6: Warehouse Stock -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="card-title mb-0">Stok Gudang</h5>
                    <div style="min-width: 220px;">
                        <select class="form-select form-select-sm" id="warehouse-select">
                            @foreach($warehouseOptions as $wh)
                                <option value="{{ $wh['id'] }}" {{ $wh['id'] == $selectedWarehouseId ? 'selected' : '' }}>{{ $wh['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Nama Barang</th>
                                    <th class="text-end">Qty Available</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($warehouseStock as $item)
                                <tr>
                                    <td>{{ $item['name'] }}</td>
                                    <td class="text-end {{ $item['qty'] < $item['minimum_stock'] ? 'text-danger fw-semibold' : '' }}">
                                        {{ number_format($item['qty'], 0, ',', '.') }}
                                        @if($item['qty'] < $item['minimum_stock'])
                                            <i class="ti tabler-alert-triangle ms-1"></i>
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
    </div>
@stop

@section('js_script')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
$(function() {
    var baseUrl = '{{ route("dashboard.index") }}';

    // Helper: build URL preserving all current filter params
    function buildDashboardUrl(overrides) {
        var params = {
            filter_month: '{{ $filterMonth }}',
            revenue_split_id: '{{ $selectedCycleId }}',
            budget_owner_id: '{{ $budgetOwner['selectedId'] }}',
            budget_kingdom_id: '{{ $budgetKingdom['selectedId'] }}',
            warehouse_id: '{{ $selectedWarehouseId }}',
        };
        Object.assign(params, overrides || {});
        var query = Object.keys(params).map(function(k) {
            return k + '=' + encodeURIComponent(params[k]);
        }).join('&');
        return baseUrl + '?' + query;
    }

    // ---- Filter 1: Month picker ----
    $('#dashboard-filter-month').on('change', function() {
        window.location.href = buildDashboardUrl({ filter_month: $(this).val() });
    });

    // ---- Section Selects -> reload ----
    $('#revenue-split-select').on('change', function() {
        window.location.href = buildDashboardUrl({ revenue_split_id: $(this).val() });
    });
    $('#budget-owner-select').on('change', function() {
        window.location.href = buildDashboardUrl({ budget_owner_id: $(this).val() });
    });
    $('#budget-kingdom-select').on('change', function() {
        window.location.href = buildDashboardUrl({ budget_kingdom_id: $(this).val() });
    });
    $('#warehouse-select').on('change', function() {
        window.location.href = buildDashboardUrl({ warehouse_id: $(this).val() });
    });

    // ---- Daily Sales Bar Chart ----
    var dailySalesData = @json($dailySales);
    var chartLabels = dailySalesData.map(d => d.label);
    var chartQty = dailySalesData.map(d => d.qty);
    var chartAmount = dailySalesData.map(d => d.amount);
    var chartDates = dailySalesData.map(d => d.date);

    var currentMode = 'qty';

    var chartOptions = {
        series: [{
            name: 'Qty Terjual',
            data: chartQty
        }],
        chart: {
            type: 'bar',
            height: 350,
            toolbar: { show: false },
            events: {
                dataPointSelection: function(event, chartContext, config) {
                    var date = chartDates[config.dataPointIndex];
                    window.location.href = '{{ route("main_transaction.sales_order.index") }}?date_from=' + date + '&date_to=' + date;
                }
            }
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                columnWidth: '60%',
            }
        },
        dataLabels: { enabled: false },
        xaxis: {
            categories: chartLabels,
            labels: {
                rotate: -45,
                style: { fontSize: '11px' }
            }
        },
        yaxis: {
            labels: {
                formatter: function(val) {
                    return currentMode === 'qty'
                        ? val.toLocaleString('id-ID')
                        : 'Rp ' + (val / 1000000).toFixed(1) + ' jt';
                }
            }
        },
        tooltip: {
            y: {
                formatter: function(val, opts) {
                    var idx = opts.dataPointIndex;
                    if (currentMode === 'qty') {
                        return val.toLocaleString('id-ID') + ' unit (Rp ' + (chartAmount[idx] / 1000000).toFixed(1) + ' jt)';
                    }
                    return 'Rp ' + val.toLocaleString('id-ID') + ' (' + chartQty[idx].toLocaleString('id-ID') + ' unit)';
                }
            }
        },
        colors: ['#696cff']
    };

    var chart = new ApexCharts(document.querySelector("#daily-sales-chart"), chartOptions);
    chart.render();

    $('#chart-mode-qty').on('click', function() {
        currentMode = 'qty';
        $(this).addClass('active');
        $('#chart-mode-amount').removeClass('active');
        chart.updateSeries([{ name: 'Qty Terjual', data: chartQty }]);
    });

    $('#chart-mode-amount').on('click', function() {
        currentMode = 'amount';
        $(this).addClass('active');
        $('#chart-mode-qty').removeClass('active');
        chart.updateSeries([{ name: 'Total Penjualan', data: chartAmount }]);
    });
});
</script>
@endsection
