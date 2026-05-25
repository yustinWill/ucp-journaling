@extends('layouts.default')
@section('page_title')
    Kas Besar - Detail
@stop
@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Detail Kas Besar</h5>
            <a href="{{ route('main_transaction.kas_besar.index') }}" class="btn btn-label-secondary btn-sm">
                <i class="icon-base ti tabler-arrow-left me-1"></i> Kembali
            </a>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <!-- Left column -->
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td class="text-muted" style="width:180px">No Transaksi</td>
                            <td><strong>{{ $kasBesar->transaction_number }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal</td>
                            <td>{{ \Carbon\Carbon::parse($kasBesar->transaction_date)->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tipe</td>
                            <td>
                                @if($kasBesar->type === 'TOPUP')
                                    <span class="badge bg-label-success">TOPUP</span>
                                @else
                                    <span class="badge bg-label-danger">PENGELUARAN</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Diinput Oleh</td>
                            <td>{{ $kasBesar->created_by_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Input</td>
                            <td>{{ \Carbon\Carbon::parse($kasBesar->created_at)->format('d M Y H:i') }}</td>
                        </tr>
                        @if($kasBesar->type === 'DEDUCTION' && $kasBesar->expense_transaction_no)
                        <tr>
                            <td class="text-muted">No Pengeluaran</td>
                            <td>
                                <a href="{{ route('main_transaction.expense.detail', $kasBesar->f_expense_id) }}"
                                    class="text-primary fw-semibold">
                                    {{ $kasBesar->expense_transaction_no }}
                                </a>
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>

                <!-- Right column: saldo info -->
                <div class="col-md-6">
                    <div class="card bg-primary text-white">
                        <div class="card-body py-4">
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom border-white border-opacity-25">
                                <span><i class="icon-base ti tabler-wallet me-2"></i>Saldo Before</span>
                                <span class="fs-5 fw-bold">Rp {{ number_format($kasBesar->saldo_before, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom border-white border-opacity-25">
                                <span>
                                    @if($kasBesar->type === 'TOPUP')
                                        <i class="icon-base ti tabler-plus me-2"></i>Topup Amount
                                    @else
                                        <i class="icon-base ti tabler-minus me-2"></i>Deduction
                                    @endif
                                </span>
                                <span class="fs-5 fw-bold">
                                    @if($kasBesar->type === 'TOPUP')
                                        + Rp {{ number_format(abs($kasBesar->topup_amount), 0, ',', '.') }}
                                    @else
                                        - Rp {{ number_format(abs($kasBesar->topup_amount), 0, ',', '.') }}
                                    @endif
                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span><i class="icon-base ti tabler-cash me-2"></i>Saldo After</span>
                                <span class="fs-4 fw-bold">Rp {{ number_format($kasBesar->saldo_after, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    @if($kasBesar->notes)
                        <div class="mt-3">
                            <label class="form-label text-muted">Catatan</label>
                            <p class="mb-0">{{ $kasBesar->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
