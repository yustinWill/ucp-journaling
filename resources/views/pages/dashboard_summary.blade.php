@extends('layouts.default')
@section('page_title')
    Pencatatan Keuangan
@stop
@section('content')
    <!-- Filter Bar -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('dashboard.summary') }}" id="filter-form">
                <div class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="icon-base ti tabler-calendar me-1"></i> Periode
                        </label>
                        <div class="d-flex gap-2">
                            <input type="date" name="date_from" class="form-control" value="{{ $date_from }}" required>
                            <span class="d-flex align-items-center text-muted">→</span>
                            <input type="date" name="date_to" class="form-control" value="{{ $date_to }}" required>
                        </div>
                    </div>
                    <div class="col-md-auto">
                        <button type="submit" class="btn btn-primary">
                            <i class="icon-base ti tabler-filter me-1"></i> Apply Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Entity Tabs -->
    <div class="card mb-4">
        <div class="card-body p-0">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <button type="button" class="nav-link active" data-bs-toggle="tab" data-bs-target="#pabrik-tab" role="tab">
                        <i class="icon-base ti tabler-building-factory-2 me-2"></i>PABRIK
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#kingdom-tab" role="tab">
                        <i class="icon-base ti tabler-building-store me-2"></i>KINGDOM
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#owner-tab" role="tab">
                        <i class="icon-base ti tabler-user me-2"></i>OWNER
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="tab-content">
        <!-- PABRIK Tab -->
        <div class="tab-pane fade show active" id="pabrik-tab" role="tabpanel">
            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-lg-6">
                    <div class="card card-border-shadow-primary h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar me-3">
                                    <span class="avatar-initial rounded bg-label-primary">
                                        <i class="icon-base ti tabler-package icon-28px"></i>
                                    </span>
                                </div>
                                <div>
                                    <p class="text-muted mb-0 small">Total Sales Pabrik</p>
                                </div>
                            </div>
                            <h2 class="mb-2">Rp {{ number_format($sales_pabrik ?? 0, 0, ',', '.') }}</h2>
                            <div class="text-success small">
                                <i class="icon-base ti tabler-trending-up me-1"></i>
                                <span>Stock keluar ke Kingdom & Owner</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card card-border-shadow-success h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar me-3">
                                    <span class="avatar-initial rounded bg-label-success">
                                        <i class="icon-base ti tabler-cash icon-28px"></i>
                                    </span>
                                </div>
                                <div>
                                    <p class="text-muted mb-0 small">Total Piutang Pabrik</p>
                                </div>
                            </div>
                            <h2 class="mb-2">Rp {{ number_format($piutang_pabrik ?? 0, 0, ',', '.') }}</h2>
                            <div class="text-muted small">
                                {{ $piutang_count ?? 0 }} Open Invoices
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales Pabrik Table -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="icon-base ti tabler-chart-bar me-2"></i>SALES PABRIK (Stock Keluar)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="icon-base ti tabler-search"></i></span>
                            <input type="text" class="form-control" placeholder="Search transactions...">
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Destination</th>
                                    <th>Product</th>
                                    <th>Qty</th>
                                    <th>Transfer Price</th>
                                    <th>Total Value</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sales_pabrik_data ?? [] as $sale)
                                <tr>
                                    <td>{{ date('d/m/Y', strtotime($sale->date)) }}</td>
                                    <td>{{ $sale->destination }}</td>
                                    <td>{{ $sale->product }}</td>
                                    <td>{{ number_format($sale->qty, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($sale->transfer_price, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($sale->total_value, 0, ',', '.') }}</td>
                                    <td>
                                        @if($sale->status == 'PAID')
                                            <span class="badge bg-label-success">
                                                <i class="icon-xs ti ti-check"></i> PAID
                                            </span>
                                        @elseif($sale->status == 'PARTIAL')
                                            <span class="badge bg-label-warning">
                                                <i class="icon-xs ti ti-circle-half"></i> PARTIAL
                                            </span>
                                        @else
                                            <span class="badge bg-label-danger">
                                                <i class="icon-xs ti ti-circle"></i> OPEN
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="icon-base ti tabler-database-off icon-32px mb-2"></i>
                                        <p class="mb-0">No transactions found</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-3">
                        <nav>
                            <ul class="pagination pagination-sm">
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Piutang Pabrik Table -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="icon-base ti tabler-credit-card me-2"></i>PIUTANG PABRIK
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="icon-base ti tabler-search"></i></span>
                            <input type="text" class="form-control" placeholder="Search piutang...">
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Source Entity</th>
                                    <th>Ref Sales</th>
                                    <th>Receivable Amount</th>
                                    <th>Paid Amount</th>
                                    <th>Remaining Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($piutang_pabrik_data ?? [] as $piutang)
                                <tr>
                                    <td>{{ $piutang->source_entity }}</td>
                                    <td>{{ $piutang->ref_sales }}</td>
                                    <td>Rp {{ number_format($piutang->receivable_amount, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($piutang->paid_amount, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($piutang->remaining_amount, 0, ',', '.') }}</td>
                                    <td>
                                        @if($piutang->status == 'PAID')
                                            <span class="badge bg-label-success">
                                                <i class="icon-xs ti ti-check"></i> PAID
                                            </span>
                                        @elseif($piutang->status == 'PARTIAL')
                                            <span class="badge bg-label-warning">
                                                <i class="icon-xs ti ti-circle-half"></i> PARTIAL
                                            </span>
                                        @else
                                            <span class="badge bg-label-danger">
                                                <i class="icon-xs ti ti-circle"></i> OPEN
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="icon-base ti tabler-database-off icon-32px mb-2"></i>
                                        <p class="mb-0">No receivables found</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-3">
                        <nav>
                            <ul class="pagination pagination-sm">
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- KINGDOM Tab -->
        <div class="tab-pane fade" id="kingdom-tab" role="tabpanel">
            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-lg-6">
                    <div class="card card-border-shadow-info h-100" style="border-left-color: #9333ea !important;">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar me-3">
                                    <span class="avatar-initial rounded" style="background-color: #f3e8ff;">
                                        <i class="icon-base ti tabler-shopping-cart icon-28px" style="color: #9333ea;"></i>
                                    </span>
                                </div>
                                <div>
                                    <p class="text-muted mb-0 small">Total Sales Kingdom</p>
                                </div>
                            </div>
                            <h2 class="mb-2">Rp {{ number_format($sales_kingdom ?? 0, 0, ',', '.') }}</h2>
                            <div class="text-success small">
                                <i class="icon-base ti tabler-trending-up me-1"></i>
                                <span>Penjualan ke Customer</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card card-border-shadow-warning h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar me-3">
                                    <span class="avatar-initial rounded bg-label-warning">
                                        <i class="icon-base ti tabler-wallet icon-28px"></i>
                                    </span>
                                </div>
                                <div>
                                    <p class="text-muted mb-0 small">Total Pengeluaran</p>
                                </div>
                            </div>
                            <h2 class="mb-2">Rp {{ number_format($pengeluaran_kingdom ?? 0, 0, ',', '.') }}</h2>
                            <div class="text-muted small">
                                Biaya operasional Kingdom
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales Kingdom Table -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="icon-base ti tabler-chart-line me-2"></i>SALES (Penjualan ke Customer)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="icon-base ti tabler-search"></i></span>
                            <input type="text" class="form-control" placeholder="Search sales...">
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Customer</th>
                                    <th>Product</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sales_kingdom_data ?? [] as $sale)
                                <tr>
                                    <td>{{ date('d/m/Y', strtotime($sale->date)) }}</td>
                                    <td>{{ $sale->customer }}</td>
                                    <td>{{ $sale->product }}</td>
                                    <td>{{ number_format($sale->qty, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($sale->price, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                                    <td>
                                        @if($sale->status == 'PAID')
                                            <span class="badge bg-label-success">
                                                <i class="icon-xs ti ti-check"></i> PAID
                                            </span>
                                        @elseif($sale->status == 'PARTIAL')
                                            <span class="badge bg-label-warning">
                                                <i class="icon-xs ti ti-circle-half"></i> PARTIAL
                                            </span>
                                        @else
                                            <span class="badge bg-label-danger">
                                                <i class="icon-xs ti ti-circle"></i> OPEN
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="icon-base ti tabler-database-off icon-32px mb-2"></i>
                                        <p class="mb-0">No sales data available</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- OWNER Tab -->
        <div class="tab-pane fade" id="owner-tab" role="tabpanel">
            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-lg-6">
                    <div class="card card-border-shadow-danger h-100" style="border-left-color: #ea580c !important;">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar me-3">
                                    <span class="avatar-initial rounded" style="background-color: #ffedd5;">
                                        <i class="icon-base ti tabler-shopping-cart icon-28px" style="color: #ea580c;"></i>
                                    </span>
                                </div>
                                <div>
                                    <p class="text-muted mb-0 small">Total Sales Owner</p>
                                </div>
                            </div>
                            <h2 class="mb-2">Rp {{ number_format($sales_owner ?? 0, 0, ',', '.') }}</h2>
                            <div class="text-success small">
                                <i class="icon-base ti tabler-trending-up me-1"></i>
                                <span>Penjualan ke Customer</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card card-border-shadow-warning h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar me-3">
                                    <span class="avatar-initial rounded bg-label-warning">
                                        <i class="icon-base ti tabler-wallet icon-28px"></i>
                                    </span>
                                </div>
                                <div>
                                    <p class="text-muted mb-0 small">Total Pengeluaran</p>
                                </div>
                            </div>
                            <h2 class="mb-2">Rp {{ number_format($pengeluaran_owner ?? 0, 0, ',', '.') }}</h2>
                            <div class="text-muted small">
                                Biaya operasional Owner
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales Owner Table -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="icon-base ti tabler-chart-line me-2"></i>SALES (Penjualan ke Customer)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="icon-base ti tabler-search"></i></span>
                            <input type="text" class="form-control" placeholder="Search sales...">
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Customer</th>
                                    <th>Product</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sales_owner_data ?? [] as $sale)
                                <tr>
                                    <td>{{ date('d/m/Y', strtotime($sale->date)) }}</td>
                                    <td>{{ $sale->customer }}</td>
                                    <td>{{ $sale->product }}</td>
                                    <td>{{ number_format($sale->qty, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($sale->price, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                                    <td>
                                        @if($sale->status == 'PAID')
                                            <span class="badge bg-label-success">
                                                <i class="icon-xs ti ti-check"></i> PAID
                                            </span>
                                        @elseif($sale->status == 'PARTIAL')
                                            <span class="badge bg-label-warning">
                                                <i class="icon-xs ti ti-circle-half"></i> PARTIAL
                                            </span>
                                        @else
                                            <span class="badge bg-label-danger">
                                                <i class="icon-xs ti ti-circle"></i> OPEN
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="icon-base ti tabler-database-off icon-32px mb-2"></i>
                                        <p class="mb-0">No sales data available</p>
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
@stop

@section('js_script')
    <script>
        $(function() {
            // Dashboard initialization
        });
    </script>
@endsection
