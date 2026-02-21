@extends('layouts.default')
@section('page_title')
    Top Up Detail - {{ $topup->topup_number }}
@stop
@section('content')
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">
                <i class="icon-base ti tabler-cash me-2"></i>Detail Top Up Pembayaran
            </h4>
            <p class="text-muted mb-0">{{ $topup->topup_number }}</p>
        </div>
        <div>
            <a href="{{ route('main_transaction.topup.index') }}" class="btn btn-label-secondary">
                <i class="icon-base ti tabler-arrow-left me-1"></i> Kembali ke List
            </a>
        </div>
    </div>

    <!-- Top Up Information Card -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="icon-base ti tabler-info-circle me-2"></i>Informasi Top Up
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label text-muted small">Nomor Top Up</label>
                        <div class="fw-bold fs-5">{{ $topup->topup_number }}</div>
                    </div>
                    {{-- <div class="mb-3">
                        <label class="form-label text-muted small">Entity</label>
                        <div>
                            @if ($topup->entity_type === 'KINGDOM')
                                <span class="badge" style="background-color: #f3e8ff; color: #9333ea; border: 1px solid #9333ea; font-size: 14px; padding: 6px 12px;">
                                    <i class="icon-base ti tabler-building-store me-1"></i> Kingdom (Perusahaan)
                                </span>
                            @else
                                <span class="badge" style="background-color: #ffedd5; color: #ea580c; border: 1px solid #ea580c; font-size: 14px; padding: 6px 12px;">
                                    <i class="icon-base ti tabler-user me-1"></i> Owner
                                </span>
                            @endif
                        </div>
                    </div> --}}
                    <div class="mb-3">
                        <label class="form-label text-muted small">Tanggal Top Up</label>
                        <div class="fw-semibold">
                            <i class="icon-base ti tabler-calendar me-1"></i>
                            {{ \Carbon\Carbon::parse($topup->topup_date)->format('d F Y') }}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label text-muted small">Status</label>
                        <div>
                            @if ($topup->status === 'PAID')
                                <span class="badge bg-label-success" style="font-size: 14px; padding: 6px 12px;">
                                    <i class="icon-base ti tabler-check me-1"></i> PAID
                                </span>
                            @elseif ($topup->status === 'PARTIAL')
                                <span class="badge bg-label-warning" style="font-size: 14px; padding: 6px 12px;">
                                    <i class="icon-base ti tabler-clock me-1"></i> PARTIAL
                                </span>
                            @else
                                <span class="badge bg-label-danger" style="font-size: 14px; padding: 6px 12px;">
                                    <i class="icon-base ti tabler-circle me-1"></i> OPEN
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small">Dibuat Oleh</label>
                        <div class="fw-semibold">
                            @php
                                $creator = DB::table('users')->where('id', $topup->created_by)->first();
                            @endphp
                            {{ $creator ? $creator->name : '-' }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small">Tanggal Dibuat</label>
                        <div class="fw-semibold">
                            {{ \Carbon\Carbon::parse($topup->created_at)->format('d F Y H:i') }}
                        </div>
                    </div>
                </div>
            </div>

            @if ($topup->notes)
                <hr class="my-4">
                <div>
                    <label class="form-label text-muted small">Catatan</label>
                    <div class="alert alert-info mb-0">
                        <i class="icon-base ti tabler-note me-1"></i>
                        {{ $topup->notes }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Summary Card -->
    <div class="card bg-primary text-white mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="d-flex justify-content-between align-items-center pb-2 border-bottom border-white border-opacity-25">
                        <span><i class="icon-base ti tabler-coin me-1"></i> Total Top Up Amount</span>
                        <span class="fs-5 fw-bold">Rp {{ number_format($topup->topup_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex justify-content-between align-items-center pb-2 border-bottom border-white border-opacity-25">
                        <span><i class="icon-base ti tabler-check me-1"></i> Total Allocated</span>
                        <span class="fs-5 fw-bold">Rp {{ number_format($topup->allocated_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex justify-content-between align-items-center pb-2 border-bottom border-white border-opacity-25">
                        <span><i class="icon-base ti tabler-wallet me-1"></i> Remaining Balance</span>
                        <span class="fs-5 fw-bold">Rp {{ number_format($topup->remaining_balance, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Allocation Details Card -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="icon-base ti tabler-list-details me-2"></i>Detail Alokasi FIFO
            </h5>
        </div>
        <div class="card-body">
            @if ($allocations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th width="80px" class="text-center">Urutan</th>
                                <th>SO Number</th>
                                <th>SO Date</th>
                                <th class="text-end">SO Total</th>
                                <th class="text-end">Remaining Before</th>
                                <th class="text-end">Allocated Amount</th>
                                <th class="text-end">Remaining After</th>
                                <th width="100px" class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($allocations as $allocation)
                                <tr>
                                    <td class="text-center">
                                        <span class="badge bg-label-primary" style="font-size: 14px;">{{ $allocation->allocation_sequence }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $allocation->so_number }}</strong>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($allocation->so_date)->format('d F Y') }}</td>
                                    <td class="text-end">Rp {{ number_format($allocation->so_total_amount, 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($allocation->so_remaining_before, 0, ',', '.') }}</td>
                                    <td class="text-end">
                                        <strong class="text-success">Rp {{ number_format($allocation->allocated_amount, 0, ',', '.') }}</strong>
                                    </td>
                                    <td class="text-end">
                                        @if ($allocation->so_remaining_after > 0)
                                            <span class="text-warning">Rp {{ number_format($allocation->so_remaining_after, 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-muted">Rp 0</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($allocation->so_remaining_after <= 0)
                                            <span class="badge bg-label-success">
                                                <i class="icon-base ti tabler-circle-check"></i> PAID
                                            </span>
                                        @else
                                            <span class="badge bg-label-warning">
                                                <i class="icon-base ti tabler-clock"></i> PARTIAL
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="5" class="text-end">Total Allocated:</th>
                                <th class="text-end">
                                    <strong class="text-primary">Rp {{ number_format($topup->allocated_amount, 0, ',', '.') }}</strong>
                                </th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if ($topup->remaining_balance > 0)
                    <div class="alert alert-warning mt-3 mb-0">
                        <i class="icon-base ti tabler-alert-circle me-2"></i>
                        <strong>Sisa Deposit:</strong> Rp {{ number_format($topup->remaining_balance, 0, ',', '.') }}
                        - Saldo ini akan otomatis dialokasikan ke SO berikutnya saat ada Top Up baru atau akan tersimpan sebagai deposit.
                    </div>
                @else
                    <div class="alert alert-success mt-3 mb-0">
                        <i class="icon-base ti tabler-circle-check me-2"></i>
                        <strong>Fully Allocated:</strong> Seluruh Top Up telah dialokasikan ke Sales Orders.
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="icon-base ti tabler-inbox" style="font-size: 48px; opacity: 0.3;"></i>
                    <div class="text-muted mt-2">Tidak Ada Alokasi</div>
                    <small class="text-muted">Top Up ini belum dialokasikan ke Sales Order manapun</small>
                </div>
            @endif
        </div>
    </div>

    <!-- FIFO Explanation -->
    <div class="card mt-4 bg-light">
        <div class="card-body">
            <h6 class="fw-bold mb-2">
                <i class="icon-base ti tabler-info-circle me-1"></i>
                Penjelasan Alokasi FIFO (First In, First Out)
            </h6>
            <p class="mb-0 small text-muted">
                Pembayaran dialokasikan otomatis ke Sales Order tertua terlebih dahulu (berdasarkan tanggal SO).
                Urutan alokasi menunjukkan prioritas pembayaran. SO dengan urutan 1 dibayar pertama, kemudian urutan 2, dan seterusnya.
                Jika saldo Top Up habis sebelum semua SO terbayar, sisa SO akan menunggu Top Up berikutnya.
            </p>
        </div>
    </div>
@stop
