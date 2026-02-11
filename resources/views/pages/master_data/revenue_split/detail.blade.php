@extends('layouts.default')
@section('page_title')
    Detail {{ $revenueSplt->cycle_name }}
@stop
@section('content')
    <!-- Breadcrumb -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('master_data.revenue_split.index') }}">Keuntungan
                            Penjualan</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Detail {{ $revenueSplt->cycle_name }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Main Detail Card -->
    <div class="card">
        <!-- Header Section -->
        <div class="card-body border-bottom">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="mb-2">Detail {{ $revenueSplt->cycle_name }}</h4>
                    <p class="text-muted mb-0">Monitoring dan analisis keuntungan penjualan per periode cycle</p>
                </div>
            </div>
        </div>

        <!-- Basic Information Section -->
        <div class="card-body border-bottom">
            <h5 class="card-title mb-4">Informasi Dasar</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Cycle</label>
                    <input type="text" class="form-control" value="{{ $revenueSplt->cycle_name }}" disabled>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <input type="text" class="form-control"
                        value="{{ $revenueSplt->status === 'ACTIVE' ? 'Sedang Berjalan' : ($revenueSplt->status === 'SELESAI' ? 'Selesai' : 'Draft') }}"
                        disabled>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" class="form-control" value="{{ $revenueSplt->start_date }}" disabled>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Selesai</label>
                    <input type="date" class="form-control" value="{{ $revenueSplt->end_date }}" disabled>
                </div>
                @if ($revenueSplt->description)
                    <div class="col-md-12">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" rows="2" disabled>{{ $revenueSplt->description }}</textarea>
                    </div>
                @endif
            </div>

            <div class="col-lg-12">
                <div class="demo-inline-spacing mt-4">
                    <div class="list-group list-group-horizontal-md text-md-center" role="tablist">
                        <a class="list-group-item list-group-item-action waves-effect active"
                            id="informasi-detail-list-item" data-bs-toggle="list" href="#horizontal-informasi-detail"
                            aria-selected="true" role="tab">
                            <i class="icon-base ti tabler-info-circle me-2"></i>
                            <b>Informasi Detail</b>
                        </a>
                        <a class="list-group-item list-group-item-action waves-effect" id="sales-orders-list-item"
                            data-bs-toggle="list" href="#horizontal-sales-orders" aria-selected="false" role="tab">
                            <i class="icon-base ti tabler-shopping-cart me-2"></i>
                            <b>Sales Orders</b>
                        </a>
                        <a class="list-group-item list-group-item-action waves-effect" id="product-revenue-split-list-item"
                            data-bs-toggle="list" href="#horizontal-product-revenue-split" aria-selected="false"
                            role="tab">
                            <i class="icon-base ti tabler-percentage me-2"></i>
                            <b>Product Revenue Split</b>
                        </a>
                        <a class="list-group-item list-group-item-action waves-effect" id="audit-log-list-item"
                            data-bs-toggle="list" href="#horizontal-audit-log" aria-selected="false" role="tab">
                            <i class="icon-base ti tabler-history me-2"></i>
                            <b>Audit Log</b>
                        </a>
                    </div>
                    <div class="tab-content px-0 mt-0">
                        <!-- Informasi Detail Tab -->
                        <div class="tab-pane fade show active" id="horizontal-informasi-detail" role="tabpanel">

                            @php
                                    $actualPabrikRevenue = $actual_keuntungan_parbrik;
                                    $actualOwnerRevenue = $actual_keuntungan_owner;
                                    $actualPerusahaanRevenue = $actual_keuntungan_perusahaan;
                                    $actualRevenue = $actualPabrikRevenue + $actualOwnerRevenue + $actualPerusahaanRevenue;

                                    $actualPabrikAchievement =
                                        $revenueSplt->target_pabrik > 0
                                            ? ($actualPabrikRevenue / $revenueSplt->target_pabrik) * 100
                                            : 0;
                                    $actualOwnerAchievement =
                                        $revenueSplt->target_owner > 0
                                            ? ($actualOwnerRevenue / $revenueSplt->target_owner) * 100
                                            : 0;
                                    $actualPerusahaanAchievement =
                                        $revenueSplt->target_perusahaan > 0
                                            ? ($actualPerusahaanRevenue / $revenueSplt->target_perusahaan) * 100
                                            : 0;
                                    $actualTotalAchievement =
                                        $revenueSplt->total_target > 0
                                            ? (($actualRevenue ?? 0) / $revenueSplt->total_target) * 100
                                            : 0;
                            @endphp

                            {{-- Expected Revenue Progress - commented out for now
                            <div class="col-12 mb-6">
                                <h5 class="mb-4">Expected Revenue Progress</h5>
                                <p class="text-muted mb-4"><small>Perhitungan berdasarkan semua Sales Order dalam periode
                                        cycle
                                        (yang sudah dibayar + belum dibayar + belum ada invoice). Revenue didistribusikan
                                        sesuai
                                        proporsi target masing-masing kategori.</small></p>

                                <!-- Expected Progress Bars -->
                                <div class="row g-4 mb-4">
                                    <!-- Expected Pabrik Progress -->
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="avatar me-3">
                                                <span class="avatar-initial rounded bg-label-primary">
                                                    <i class="icon-base ti tabler-building-factory icon-24px"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Expected Keuntungan Pabrik</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Expected Owner Progress -->
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="avatar me-3">
                                                <span class="avatar-initial rounded bg-label-warning">
                                                    <i class="icon-base ti tabler-user-star icon-24px"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Expected Keuntungan Owner</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Expected Perusahaan Progress -->
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="avatar me-3">
                                                <span class="avatar-initial rounded bg-label-success">
                                                    <i class="icon-base ti tabler-building icon-24px"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Expected Keuntungan Perusahaan</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Expected Total Progress -->
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="avatar me-3">
                                                <span class="avatar-initial rounded bg-label-info">
                                                    <i class="icon-base ti tabler-target icon-24px"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Expected Total Achievement</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            --}}

                            <!-- Actual Revenue Progress -->
                            <div class="col-12 mb-6">
                                <h5 class="mb-4">Actual Revenue Progress</h5>
                                <p class="text-muted mb-4"><small>Perhitungan berdasarkan Sales Order yang sudah terbayar
                                        saja.
                                        Revenue didistribusikan sesuai proporsi target masing-masing kategori untuk
                                        mencerminkan
                                        pencapaian riil.</small></p>

                                <!-- Actual Progress Bars -->
                                <div class="row g-4 mb-4">
                                    <!-- Actual Pabrik Progress -->
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="avatar me-3">
                                                <span class="avatar-initial rounded bg-label-primary">
                                                    <i class="icon-base ti tabler-building-factory icon-24px"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Actual Keuntungan Pabrik</h6>
                                                <small class="text-muted">Rp
                                                    {{ number_format($actualPabrikRevenue ?? 0, 0, ',', '.') }} / Rp
                                                    {{ number_format($revenueSplt->target_pabrik, 0, ',', '.') }}</small>
                                            </div>
                                            <span
                                                class="badge bg-label-primary">{{ number_format($actualPabrikAchievement ?? 0, 1) }}%</span>
                                        </div>
                                        <div class="progress mb-2" style="height: 8px;">
                                            <div class="progress-bar bg-primary"
                                                style="width: {{ min($actualPabrikAchievement ?? 0, 100) }}%;"></div>
                                        </div>
                                    </div>

                                    <!-- Actual Owner Progress -->
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="avatar me-3">
                                                <span class="avatar-initial rounded bg-label-warning">
                                                    <i class="icon-base ti tabler-user-star icon-24px"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Actual Keuntungan Owner</h6>
                                                <small class="text-muted">Rp
                                                    {{ number_format($actualOwnerRevenue ?? 0, 0, ',', '.') }} / Rp
                                                    {{ number_format($revenueSplt->target_owner, 0, ',', '.') }}</small>
                                            </div>
                                            <span
                                                class="badge bg-label-warning">{{ number_format($actualOwnerAchievement ?? 0, 1) }}%</span>
                                        </div>
                                        <div class="progress mb-2" style="height: 8px;">
                                            <div class="progress-bar bg-warning"
                                                style="width: {{ min($actualOwnerAchievement ?? 0, 100) }}%;"></div>
                                        </div>
                                    </div>

                                    <!-- Actual Perusahaan Progress -->
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="avatar me-3">
                                                <span class="avatar-initial rounded bg-label-success">
                                                    <i class="icon-base ti tabler-building icon-24px"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Actual Keuntungan Perusahaan</h6>
                                                <small class="text-muted">Rp
                                                    {{ number_format($actualPerusahaanRevenue ?? 0, 0, ',', '.') }} / Rp
                                                    {{ number_format($revenueSplt->target_perusahaan, 0, ',', '.') }}</small>
                                            </div>
                                            <span
                                                class="badge bg-label-success">{{ number_format($actualPerusahaanAchievement ?? 0, 1) }}%</span>
                                        </div>
                                        <div class="progress mb-2" style="height: 8px;">
                                            <div class="progress-bar bg-success"
                                                style="width: {{ min($actualPerusahaanAchievement ?? 0, 100) }}%;"></div>
                                        </div>
                                    </div>

                                    <!-- Actual Total Progress -->
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="avatar me-3">
                                                <span class="avatar-initial rounded bg-label-info">
                                                    <i class="icon-base ti tabler-target icon-24px"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Actual Total Achievement</h6>
                                                <small class="text-muted">Rp
                                                    {{ number_format($actualRevenue ?? 0, 0, ',', '.') }} / Rp
                                                    {{ number_format($revenueSplt->total_target, 0, ',', '.') }}</small>
                                            </div>
                                            <span
                                                class="badge bg-label-info">{{ number_format($actualTotalAchievement ?? 0, 1) }}%</span>
                                        </div>
                                        <div class="progress mb-2" style="height: 8px;">
                                            <div class="progress-bar bg-info"
                                                style="width: {{ min($actualTotalAchievement ?? 0, 100) }}%;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Summary Statistics Section -->
                            <div class="col-12">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Total Sales Orders</label>
                                        <input type="text" class="form-control"
                                            value="{{ $total_so ?? 0 }} SO" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Sales Orders Terbayar</label>
                                        <input type="text" class="form-control"
                                            value="{{ $total_so_paid ?? 0 }} SO" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Expected Revenue</label>
                                        <input type="text" class="form-control"
                                            value="Rp {{ number_format($expectedRevenue ?? 0, 0, ',', '.') }}" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Actual Revenue</label>
                                        <input type="text" class="form-control"
                                            value="Rp {{ number_format($actualRevenue ?? 0, 0, ',', '.') }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sales Orders Tab -->
                        <div class="tab-pane fade" id="horizontal-sales-orders" role="tabpanel">
                            <!-- Filter Controls -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label">Cari Sales Order</label>
                                    <input type="text" class="form-control" id="searchSalesOrder"
                                        placeholder="Cari berdasarkan nomor SO atau customer...">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Filter Status Revenue</label>
                                    <select class="form-select" id="revenueStatusFilter">
                                        <option value="">Semua Revenue Status</option>
                                        <option value="paid">Sudah Dibayar</option>
                                        <option value="unpaid">Belum Dibayar</option>
                                        <option value="no_invoice">Belum Ada Invoice</option>
                                    </select>
                                </div>
                                {{-- <div class="col-md-4">
                                    <label class="form-label">Filter Status SO</label>
                                    <select class="form-select" id="statusFilter">
                                        <option value="">Semua Status SO</option>
                                        <option value="PENDING">PENDING</option>
                                        <option value="CONFIRMED">CONFIRMED</option>
                                        <option value="DELIVERED">DELIVERED</option>
                                    </select>
                                </div> --}}
                            </div>

                            <!-- Sales Orders Table -->
                            <div class="table-responsive">
                                <table class="table table-striped mb-0" id="sales-orders-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>NO. SALES ORDER</th>
                                            <th>TANGGAL</th>
                                            <th>CUSTOMER</th>
                                            <th>PRODUK</th>
                                            <th class="text-end">TOTAL PENJUALAN</th>
                                            {{-- <th class="text-end">KEUNTUNGAN</th> --}}
                                            {{-- <th>STATUS SO</th> --}}
                                            <th>STATUS REVENUE</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (!empty($salesOrdersTable) && count($salesOrdersTable) > 0)
                                            @foreach ($salesOrdersTable as $so)
                                                @php
                                                    // Determine revenue status
                                                    $revenueStatus = 'no_invoice';
                                                    $revenueStatusLabel = 'Belum Ada Invoice';
                                                    $revenueStatusClass = 'bg-label-secondary';

                                                    if (isset($so['invoice_status'])) {
                                                        if ($so['invoice_status'] === 'PAID') {
                                                            $revenueStatus = 'paid';
                                                            $revenueStatusLabel = 'Sudah Dibayar';
                                                            $revenueStatusClass = 'bg-label-success';
                                                        } else {
                                                            $revenueStatus = 'unpaid';
                                                            $revenueStatusLabel = 'Belum Dibayar';
                                                            $revenueStatusClass = 'bg-label-warning';
                                                        }
                                                    }
                                                @endphp
                                                <tr data-revenue-status="{{ $revenueStatus }}">
                                                    <td>
                                                        <strong>{{ $so['so_number'] }}</strong>
                                                        <br><small
                                                            class="text-muted">{{ $so['reference_no'] ?: 'N/A' }}</small>
                                                    </td>
                                                    <td>{{ date('d/m/Y', strtotime($so['transaction_date'])) }}</td>
                                                    <td>
                                                        <div>
                                                            <h6 class="mb-0">{{ $so['customer_name'] }}</h6>
                                                            <small class="text-muted">{{ $so['customer_type'] }}</small>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted">{{ $so['product_count'] }}
                                                            produk</small>
                                                        <br><small
                                                            class="text-muted">{{ number_format($so['total_quantity'], 0, ',', '.') }}
                                                            unit</small>
                                                    </td>
                                                    <td class="text-end">
                                                        <strong>Rp
                                                            {{ number_format($so['total_amount'], 0, ',', '.') }}</strong>
                                                    </td>
                                                    {{-- <td class="text-end">
                                                        <strong>Rp
                                                            {{ number_format($so['profit_amount'], 0, ',', '.') }}</strong>
                                                        <br><small
                                                            class="text-muted">{{ number_format($so['profit_margin'], 1) }}%</small>
                                                    </td> --}}
                                                    {{-- <td>
                                                        @php
                                                            $badgeClass =
                                                                $so['status'] === 'DELIVERED'
                                                                    ? 'bg-label-success'
                                                                    : ($so['status'] === 'CONFIRMED'
                                                                        ? 'bg-label-warning'
                                                                        : 'bg-label-secondary');
                                                        @endphp
                                                        <span
                                                            class="badge {{ $badgeClass }}">{{ $so['status'] }}</span>
                                                    </td> --}}
                                                    <td>
                                                        <span
                                                            class="badge {{ $revenueStatusClass }}">{{ $revenueStatusLabel }}</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="8" class="text-center py-4">
                                                    <div class="text-muted">
                                                        <i
                                                            class="icon-base ti tabler-shopping-cart-off icon-48px mb-3"></i>
                                                        <h6>Tidak Ada Sales Order</h6>
                                                        <p class="mb-0">Belum ada sales order dalam periode cycle ini.
                                                        </p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>

                            @if (!empty($salesOrders) && count($salesOrders) > 0)
                                <div class="mt-4">
                                    <div class="row align-items-center">
                                        <div class="col-md-12">
                                            <p class="mb-0 text-muted">
                                                <strong>Ringkasan Sales Orders</strong><br>
                                                Total {{ count($salesOrders) }} sales order berkontribusi dalam cycle
                                                ini<br>
                                                Expected Revenue: <strong>Rp
                                                    {{ number_format($expectedRevenue ?? 0, 0, ',', '.') }}</strong> |
                                                Actual Revenue: <strong>Rp
                                                    {{ number_format($actualRevenue ?? 0, 0, ',', '.') }}</strong>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Product Revenue Split Tab -->
                        <div class="tab-pane fade" id="horizontal-product-revenue-split" role="tabpanel">
                            <!-- Product Revenue Split Configuration Section -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Cari Produk</label>
                                    <input type="text" class="form-control" id="searchProduct"
                                        placeholder="Cari berdasarkan nama atau kode produk...">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Filter Status Konfigurasi</label>
                                    <select class="form-select" id="configStatusFilter">
                                        <option value="">Semua Status</option>
                                        <option value="configured">Sudah Dikonfigurasi</option>
                                        <option value="not_configured">Belum Dikonfigurasi</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Product Revenue Split Table -->
                            <div class="table-responsive">
                                <table class="table table-striped mb-0" id="product-revenue-split-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>PRODUK</th>
                                            <th class="text-end">BASE PRICE</th>
                                            <th class="text-end">PORSI PABRIK</th>
                                            <th class="text-end">PORSI OWNER</th>
                                            <th class="text-end">PORSI PERUSAHAAN</th>
                                            <th>REMAINING ALLOCATION</th>
                                            <th>STATUS</th>
                                            {{-- <th class="text-center">AKSI</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- This will be populated via AJAX -->
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                                <p class="mt-2 mb-0">Memuat konfigurasi product revenue split...</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Summary Information -->
                            <div class="mt-4">
                                <div class="row align-items-center">
                                    <div class="col-md-12">
                                        <p class="mb-0 text-muted">
                                            <strong>Informasi Konfigurasi Product Revenue Split</strong><br>
                                            Konfigurasi ini akan digunakan sebagai dasar perhitungan revenue split pada
                                            setiap
                                            sales order.<br>
                                            Produk yang belum dikonfigurasi akan menggunakan split default (100% ke
                                            perusahaan).
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Audit Log Tab -->
                        <div class="tab-pane fade" id="horizontal-audit-log" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-striped mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>TANGGAL</th>
                                            <th>USER</th>
                                            <th>AKSI</th>
                                            <th>DESKRIPSI</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (!empty($auditLogs) && count($auditLogs) > 0)
                                            @foreach ($auditLogs as $log)
                                                <tr>
                                                    <td>{{ date('d/m/Y H:i', strtotime($log->created_at)) }}</td>
                                                    <td>
                                                        <div>
                                                            <h6 class="mb-0">{{ $log->creator_name ?? 'System' }}</h6>
                                                            <small
                                                                class="text-muted">{{ $log->creator_email ?? 'system@riyo.com' }}</small>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $actionBadge =
                                                                $log->action_type === 'CREATE'
                                                                    ? 'bg-label-success'
                                                                    : ($log->action_type === 'UPDATE'
                                                                        ? 'bg-label-warning'
                                                                        : 'bg-label-danger');
                                                        @endphp
                                                        <span
                                                            class="badge {{ $actionBadge }}">{{ $log->action_type }}</span>
                                                    </td>
                                                    <td>{{ $log->description }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="4" class="text-center py-4">
                                                    <div class="text-muted">
                                                        <i class="icon-base ti tabler-file-text icon-48px mb-3"></i>
                                                        <h6>Tidak Ada Audit Log</h6>
                                                        <p class="mb-0">Belum ada aktivitas yang tercatat untuk cycle
                                                            ini.
                                                        </p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs Section -->

    </div>
@stop

@section('js_script')
    <script>
        $(function() {
            // Search and filter functionality for sales order table
            $('#searchSalesOrder, #revenueStatusFilter, #statusFilter').on('keyup change', function() {
                filterSalesOrderTable();
            });

            function filterSalesOrderTable() {
                const searchTerm = $('#searchSalesOrder').val().toLowerCase();
                const revenueStatusFilter = $('#revenueStatusFilter').val();
                const statusFilter = $('#statusFilter').val();

                $('#sales-orders-table tbody tr').each(function() {
                    if ($(this).find('td[colspan]').length > 0) return; // Skip empty state row

                    const soNumber = $(this).find('td:nth-child(1) strong').text().toLowerCase();
                    const customerName = $(this).find('td:nth-child(3) h6').text().toLowerCase();
                    const soStatus = $(this).find('td:nth-child(7) .badge').text();
                    const revenueStatus = $(this).data('revenue-status');

                    let showRow = true;

                    // Search filter
                    if (searchTerm && !soNumber.includes(searchTerm) && !customerName.includes(
                            searchTerm)) {
                        showRow = false;
                    }

                    // SO Status filter
                    if (statusFilter && soStatus !== statusFilter) {
                        showRow = false;
                    }

                    // Revenue Status filter
                    if (revenueStatusFilter && revenueStatus !== revenueStatusFilter) {
                        showRow = false;
                    }

                    $(this).toggle(showRow);
                });
            }

            // Search and filter functionality for product revenue split table
            $('#searchProduct, #configStatusFilter').on('keyup change', function() {
                filterProductTable();
            });

            function filterProductTable() {
                const searchTerm = $('#searchProduct').val().toLowerCase();
                const statusFilter = $('#configStatusFilter').val();

                $('#product-revenue-split-table tbody tr').each(function() {
                    if ($(this).find('td[colspan]').length > 0) return; // Skip loading/empty state row

                    const productName = $(this).find('td:nth-child(1)').text().toLowerCase();
                    const configStatus = $(this).data('config-status');

                    let showRow = true;

                    // Search filter
                    if (searchTerm && !productName.includes(searchTerm)) {
                        showRow = false;
                    }

                    // Configuration Status filter
                    if (statusFilter && configStatus !== statusFilter) {
                        showRow = false;
                    }

                    $(this).toggle(showRow);
                });
            }

            // Load product revenue split configurations
            function loadProductRevenueSplitConfigurations() {
                const tableBody = $('#product-revenue-split-table tbody');

                $.ajax({
                    url: '/master_data/product_revenue_split/configurations',
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            tableBody.empty();

                            if (response.data && response.data.length > 0) {
                                response.data.forEach(function(config) {
                                    const configStatus = config.is_configured ? 'configured' :
                                        'not_configured';
                                    const statusBadge = config.is_configured ?
                                        '<span class="badge bg-label-success">Dikonfigurasi</span>' :
                                        '<span class="badge bg-label-warning">Default</span>';

                                    const remainingBadge = config.remaining_allocation ===
                                        'perusahaan' ? 'bg-label-primary' :
                                        (config.remaining_allocation === 'owner' ?
                                            'bg-label-info' : 'bg-label-success');
                                    const remainingText = config.remaining_allocation ===
                                        'perusahaan' ? 'Perusahaan' :
                                        (config.remaining_allocation === 'owner' ? 'Owner' :
                                            'Pabrik');

                                    const row = `
                                        <tr data-config-status="${configStatus}" data-product-id="${config.product_id}">
                                            <td>
                                                <div>
                                                    <h6 class="mb-1">${config.product_name}</h6>
                                                    <small class="text-muted">${config.product_code}</small>
                                                </div>
                                            </td>
                                            <td class="text-end">Rp ${formatCurrency(config.base_price || 0)}</td>
                                            <td class="text-end">Rp ${formatCurrency(config.porsi_pabrik || 0)}</td>
                                            <td class="text-end">Rp ${formatCurrency(config.porsi_owner || 0)}</td>
                                            <td class="text-end">Rp ${formatCurrency(config.porsi_perusahaan || 0)}</td>
                                            <td><span class="badge ${remainingBadge}">${remainingText}</span></td>
                                            <td>${statusBadge}</td>
                                        </tr>
                                    `;
                                    tableBody.append(row);
                                });
                            } else {
                                tableBody.html(`
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="icon-base ti tabler-package icon-48px mb-3"></i>
                                                <h6>Tidak Ada Produk</h6>
                                                <p class="mb-0">Belum ada produk yang tersedia untuk dikonfigurasi.</p>
                                            </div>
                                        </td>
                                    </tr>
                                `);
                            }
                        } else {
                            tableBody.html(`
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-danger">
                                        <i class="icon-base ti tabler-alert-circle icon-48px mb-3"></i>
                                        <h6>Error Loading Data</h6>
                                        <p class="mb-0">${response.message || 'Terjadi kesalahan saat memuat data.'}</p>
                                    </td>
                                </tr>
                            `);
                        }
                    },
                    error: function(xhr) {
                        tableBody.html(`
                            <tr>
                                <td colspan="8" class="text-center py-4 text-danger">
                                    <i class="icon-base ti tabler-alert-circle icon-48px mb-3"></i>
                                    <h6>Error Loading Data</h6>
                                    <p class="mb-0">Terjadi kesalahan saat memuat konfigurasi product revenue split.</p>
                                </td>
                            </tr>
                        `);
                    }
                });
            }

            // Format currency helper function
            function formatCurrency(amount) {
                return new Intl.NumberFormat('id-ID').format(amount || 0);
            }

            // Open revenue split configuration modal
            window.openRevenueSplitModal = function(productId, productName) {
                // This will trigger the modal from the product detail page
                // For now, show an alert since we need to integrate with the existing modal
                alert(
                    `Opening revenue split configuration for: ${productName}\nProduct ID: ${productId}\n\nThis will open the configuration modal where you can set the revenue split percentages.`
                    );

                // TODO: Integrate with existing revenue split modal from product detail page
                // or create a dedicated modal for this page
            };

            // Tab functionality - updated for list-group pattern
            $('a[data-bs-toggle="list"]').on('shown.bs.tab', function(e) {
                const target = $(e.target).attr('href');
                if (target === '#horizontal-sales-orders') {
                    // Refresh sales order filters when tab is shown
                    filterSalesOrderTable();
                } else if (target === '#horizontal-product-revenue-split') {
                    // Load product revenue split configurations when tab is shown
                    loadProductRevenueSplitConfigurations();
                }
            });
        });
    </script>
@endsection
