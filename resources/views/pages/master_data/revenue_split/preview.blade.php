<!-- Cycle Information -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-primary">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0">Informasi Cycle</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="120px"><strong>Nama Cycle:</strong></td>
                                <td>{{ $cycle->cycle_name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Periode:</strong></td>
                                <td>{{ date('d M Y', strtotime($cycle->start_date)) }} - {{ date('d M Y', strtotime($cycle->end_date)) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    <span class="badge {{ $cycle->status === 'ACTIVE' ? 'bg-warning' : ($cycle->status === 'SELESAI' ? 'bg-success' : 'bg-secondary') }}">
                                        {{ $cycle->status }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="140px"><strong>Target Pabrik:</strong></td>
                                <td>Rp {{ number_format($cycle->target_pabrik, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Target Owner:</strong></td>
                                <td>Rp {{ number_format($cycle->target_owner, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Target Perusahaan:</strong></td>
                                <td>Rp {{ number_format($cycle->target_perusahaan, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Total Target:</strong></td>
                                <td><strong>Rp {{ number_format($cycle->total_target, 0, ',', '.') }}</strong></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card text-center h-100">
            <div class="card-body">
                <div class="avatar mx-auto mb-3">
                    <span class="avatar-initial rounded bg-label-primary">
                        <i class="icon-base ti tabler-shopping-cart icon-28px"></i>
                    </span>
                </div>
                <h4 class="mb-1">{{ $totalSalesOrders }}</h4>
                <p class="mb-0">Total Sales Order</p>
                <small class="text-muted">Dalam periode cycle</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card text-center h-100">
            <div class="card-body">
                <div class="avatar mx-auto mb-3">
                    <span class="avatar-initial rounded bg-label-success">
                        <i class="icon-base ti tabler-coins icon-28px"></i>
                    </span>
                </div>
                <h5 class="mb-1">Rp {{ number_format($totalSalesValue, 0, ',', '.') }}</h5>
                <p class="mb-0">Total Nilai Penjualan</p>
                <small class="text-muted">Akumulasi SO</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card text-center h-100">
            <div class="card-body">
                <div class="avatar mx-auto mb-3">
                    <span class="avatar-initial rounded bg-label-warning">
                        <i class="icon-base ti tabler-file-invoice icon-28px"></i>
                    </span>
                </div>
                <h5 class="mb-1">{{ $invoicedCount }}</h5>
                <p class="mb-0">SO Sudah Diinvoice</p>
                <small class="text-muted">Rp {{ number_format($totalInvoiced, 0, ',', '.') }}</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card text-center h-100">
            <div class="card-body">
                <div class="avatar mx-auto mb-3">
                    <span class="avatar-initial rounded bg-label-info">
                        <i class="icon-base ti tabler-check icon-28px"></i>
                    </span>
                </div>
                <h5 class="mb-1">{{ $paidCount }}</h5>
                <p class="mb-0">Invoice Terbayar</p>
                <small class="text-muted">Rp {{ number_format($totalPaid, 0, ',', '.') }}</small>
            </div>
        </div>
    </div>
</div>

<!-- Sales Orders Breakdown -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Detail Sales Order dalam Periode Cycle</h6>
            </div>
            <div class="card-body">
                @if($salesOrders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Kode SO</th>
                                    <th>Customer</th>
                                    <th>Nilai SO</th>
                                    <th>Status SO</th>
                                    <th>Invoice</th>
                                    <th>Nilai Invoice</th>
                                    <th>Status Invoice</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($salesOrders as $order)
                                    <tr>
                                        <td>{{ date('d/m/Y', strtotime($order->transaction_date)) }}</td>
                                        <td>
                                            <strong>{{ $order->transaction_code }}</strong>
                                        </td>
                                        <td>{{ $order->customer_name ?: '-' }}</td>
                                        <td class="text-end">
                                            <strong>Rp {{ number_format($order->grand_price, 0, ',', '.') }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge {{ $order->so_status === 'COMPLETED' ? 'bg-success' : ($order->so_status === 'APPROVED' ? 'bg-warning' : 'bg-secondary') }}">
                                                {{ $order->so_status }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($order->invoice_code)
                                                <strong>{{ $order->invoice_code }}</strong>
                                            @else
                                                <span class="text-muted">Belum diinvoice</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if($order->invoice_total)
                                                <strong>Rp {{ number_format($order->invoice_total, 0, ',', '.') }}</strong>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($order->invoice_status)
                                                <span class="badge {{ $order->invoice_status === 'PAID' ? 'bg-success' : ($order->invoice_status === 'UNPAID' ? 'bg-danger' : 'bg-warning') }}">
                                                    {{ $order->invoice_status }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary Footer -->
                    <div class="row mt-4">
                        <div class="col-md-8">
                            <div class="alert alert-info">
                                <strong>Ringkasan:</strong><br>
                                • {{ $totalSalesOrders }} Sales Order dengan total nilai Rp {{ number_format($totalSalesValue, 0, ',', '.') }}<br>
                                • {{ $invoicedCount }} SO sudah diinvoice ({{ number_format(($invoicedCount/$totalSalesOrders)*100, 1) }}%) dengan nilai Rp {{ number_format($totalInvoiced, 0, ',', '.') }}<br>
                                • {{ $paidCount }} invoice sudah terbayar ({{ $invoicedCount > 0 ? number_format(($paidCount/$invoicedCount)*100, 1) : 0 }}%) dengan nilai Rp {{ number_format($totalPaid, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h6 class="card-title">Realisasi Keuntungan</h6>
                                    <h4 class="mb-0">Rp {{ number_format($totalPaid, 0, ',', '.') }}</h4>
                                    <small>dari target Rp {{ number_format($cycle->total_target, 0, ',', '.') }}</small>
                                    <br>
                                    <small>({{ $cycle->total_target > 0 ? number_format(($totalPaid/$cycle->total_target)*100, 1) : 0 }}%)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <div class="avatar mx-auto mb-3">
                            <span class="avatar-initial rounded bg-label-secondary">
                                <i class="icon-base ti tabler-database-off icon-28px"></i>
                            </span>
                        </div>
                        <h6>Tidak Ada Data Sales Order</h6>
                        <p class="text-muted">Belum ada sales order dalam periode cycle ini.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>