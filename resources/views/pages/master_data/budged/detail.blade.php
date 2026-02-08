@extends('layouts.default')
@section('page_title')
    Detail Periode Anggaran
@stop
@section('content')
    <div class="card mb-6">
        <div class="card-body">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <h4 class="mb-3">Detail Periode Anggaran</h4>
                </div>

                <!-- Action Panel -->
                <div class="col-md-4">
                    <div class="text-end">
                        <div class="d-flex gap-2 justify-content-end">
                            @if ($canShowEditButton)
                                <a href="{{ route('master_data.budged.edit', $periodData->id) }}"
                                    class="btn btn-primary btn-md waves-effect waves-light">
                                    <i class="icon-base ti tabler-edit me-2"></i>
                                    Edit
                                </a>
                            @endif
                            <a href="{{ route('master_data.budged.index') }}"
                                class="btn btn-label-secondary btn-md waves-effect waves-light">
                                <i class="icon-base ti tabler-arrow-left me-2"></i>
                                Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Period Information -->
            <div class="row g-6 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Periode</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="icon-base ti tabler-calendar"></i></span>
                        <input type="text" class="form-control"
                            value="{{ \Carbon\Carbon::parse($periodData->period_start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($periodData->period_end_date)->format('d/m/Y') }}"
                            disabled />
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Tipe</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="icon-base ti tabler-tag"></i></span>
                        <input type="text" class="form-control"
                            value="{{ ucfirst($periodData->type ?? '-') }}"
                            disabled />
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Jumlah Anggaran</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="icon-base ti tabler-list"></i></span>
                        <input type="text" class="form-control"
                            value="{{ count($periodData->budgets) }} Anggaran"
                            disabled />
                    </div>
                </div>
            </div>

            @php
                $totalLimit = collect($periodData->budgets)->sum('budged_limit_value');
                $totalRemaining = collect($periodData->budgets)->sum('budged_remaining_value');
                $totalUsed = $totalLimit - $totalRemaining;
            @endphp

            <!-- Summary Cards -->
            <div class="row g-4 mb-6">
                <div class="col-md-4">
                    <div class="card bg-label-primary">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1 text-nowrap">Total Limit</h6>
                                    <h4 class="mb-0">Rp {{ number_format($totalLimit, 0, ',', '.') }}</h4>
                                </div>
                                <div class="avatar">
                                    <span class="avatar-initial rounded bg-label-primary">
                                        <i class="icon-base ti tabler-wallet"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-label-danger">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1 text-nowrap">Total Terpakai</h6>
                                    <h4 class="mb-0">Rp {{ number_format($totalUsed, 0, ',', '.') }}</h4>
                                </div>
                                <div class="avatar">
                                    <span class="avatar-initial rounded bg-label-danger">
                                        <i class="icon-base ti tabler-receipt"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-label-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1 text-nowrap">Total Sisa</h6>
                                    <h4 class="mb-0">Rp {{ number_format($totalRemaining, 0, ',', '.') }}</h4>
                                </div>
                                <div class="avatar">
                                    <span class="avatar-initial rounded bg-label-success">
                                        <i class="icon-base ti tabler-cash"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-6 mx-n6" />

            <!-- Budget List with Transactions -->
            <div class="section-header">
                <h5>Daftar Anggaran & Transaksi</h5>
                <hr class="mx-n6">
            </div>

            @forelse ($periodData->budgets as $index => $budget)
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1">
                                    <i class="icon-base ti tabler-wallet me-2"></i>
                                    {{ $budget->budged_name }}
                                </h5>
                                <small class="text-muted">
                                    <span class="badge bg-label-info">{{ count($budget->expenses) }} Transaksi</span>
                                </small>
                            </div>
                            @php
                                $budgetUsed = $budget->budged_limit_value - $budget->budged_remaining_value;
                                $percentage = $budget->budged_limit_value > 0 ? ($budgetUsed / $budget->budged_limit_value) * 100 : 0;
                                $remainingClass = $budget->budged_remaining_value < 0 ? 'text-danger' : 'text-success';
                            @endphp
                            <div class="text-end">
                                <div class="mb-1">
                                    <small class="text-muted">Limit:</small>
                                    <strong>Rp {{ number_format($budget->budged_limit_value, 0, ',', '.') }}</strong>
                                </div>
                                <div class="mb-1">
                                    <small class="text-muted">Terpakai:</small>
                                    <strong class="text-danger">Rp {{ number_format($budgetUsed, 0, ',', '.') }}</strong>
                                </div>
                                <div>
                                    <small class="text-muted">Sisa:</small>
                                    <strong class="{{ $remainingClass }}">Rp {{ number_format($budget->budged_remaining_value, 0, ',', '.') }}</strong>
                                    @if($budget->budged_remaining_value < 0)
                                        <small class="d-block text-danger">(Over Budget)</small>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mt-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted small">Penggunaan Budget</span>
                                <span class="text-muted small">{{ number_format($percentage, 1) }}%</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar {{ $percentage > 100 ? 'bg-danger' : ($percentage > 75 ? 'bg-warning' : 'bg-success') }}"
                                    role="progressbar"
                                    style="width: {{ min($percentage, 100) }}%;"
                                    aria-valuenow="{{ $percentage }}"
                                    aria-valuemin="0"
                                    aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(count($budget->expenses) > 0)
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="15%">No. Transaksi</th>
                                            <th width="10%">Tanggal</th>
                                            <th width="10%">Tipe</th>
                                            <th width="15%">Penerima</th>
                                            <th width="25%">Deskripsi</th>
                                            <th width="13%">Status Pembayaran</th>
                                            <th width="12%" class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($budget->expenses as $expense)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('main_transaction.expense.detail', $expense->id) }}"
                                                        class="text-primary fw-semibold">
                                                        {{ $expense->transaction_no }}
                                                    </a>
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($expense->transaction_date)->format('d/m/Y') }}</td>
                                                <td>
                                                    @if($expense->type == 'PURCHASE')
                                                        <span class="badge bg-label-primary">Pembelian</span>
                                                    @elseif($expense->type == 'EXPENSE')
                                                        <span class="badge bg-label-warning">Biaya</span>
                                                    @else
                                                        <span class="badge bg-label-secondary">{{ $expense->type }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $expense->receiver_name ?? '-' }}</td>
                                                <td>
                                                    <small>{{ $expense->description ? \Illuminate\Support\Str::limit($expense->description, 50) : '-' }}</small>
                                                </td>
                                                <td>
                                                    @if($expense->to_be_paid_later)
                                                        @if($expense->total_remaining_amount > 0)
                                                            <span class="badge bg-label-warning">Belum Lunas</span>
                                                            <small class="d-block text-muted mt-1">
                                                                Sisa: Rp {{ number_format($expense->total_remaining_amount, 0, ',', '.') }}
                                                            </small>
                                                        @else
                                                            <span class="badge bg-label-success">Lunas</span>
                                                        @endif
                                                    @else
                                                        <span class="badge bg-label-success">Tunai</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <strong>Rp {{ number_format($expense->total_amount, 0, ',', '.') }}</strong>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="6" class="text-end">Total Pengeluaran:</th>
                                            <th class="text-end">
                                                <strong class="text-danger">Rp {{ number_format($budget->expenses->sum('total_amount'), 0, ',', '.') }}</strong>
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="card-body">
                            <div class="text-center py-4">
                                <i class="icon-base ti tabler-inbox text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-2 mb-0">Belum ada transaksi untuk anggaran ini</p>
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="alert alert-info">
                    <i class="icon-base ti tabler-info-circle me-2"></i>
                    Belum ada anggaran dalam periode ini
                </div>
            @endforelse
        </div>
    </div>
@stop
