@extends('layouts.default')
@section('page_title')
    Detail Reimbursement
@stop
@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Detail Reimbursement</h4>
            @if ($reimbursement->status === 'PENDING')
                <span class="badge bg-label-warning">PENDING</span>
            @elseif($reimbursement->status === 'APPROVED')
                <span class="badge bg-label-success">APPROVED</span>
            @else
                <span class="badge bg-label-danger">REJECTED</span>
            @endif
        </div>

        <div class="card-body">
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Kode Transaksi</label>
                    <input type="text" class="form-control" value="{{ $reimbursement->transaction_code }}" disabled>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Tanggal Pengajuan</label>
                    <input type="text" class="form-control"
                        value="{{ $reimbursement->created_at->format('d/m/Y H:i') }}" disabled>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <label class="form-label fw-bold">Nama Reimbursement</label>
                    <input type="text" class="form-control" value="{{ $reimbursement->reimbursement_name }}" disabled>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Jumlah</label>
                    <input type="text" class="form-control"
                        value="Rp {{ number_format($reimbursement->amount, 0, ',', '.') }}" disabled>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Diajukan Oleh</label>
                    <input type="text" class="form-control" value="{{ $reimbursement->creator->name }}" disabled>
                </div>
            </div>

            @if ($reimbursement->description)
                <div class="row g-3 mb-4">
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Deskripsi</label>
                        <textarea class="form-control" rows="3" disabled>{{ $reimbursement->description }}</textarea>
                    </div>
                </div>
            @endif

            @if ($reimbursement->attachment)
                <div class="row g-3 mb-4">
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Lampiran</label>
                        <div>
                            <a href="{{ asset('storage/' . $reimbursement->attachment) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="icon-base ti tabler-paperclip me-1"></i> Lihat Lampiran
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            @if ($reimbursement->status !== 'PENDING')
                <hr class="my-4">
                <h5 class="mb-3">Informasi Review</h5>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Direview Oleh</label>
                        <input type="text" class="form-control" value="{{ $reimbursement->reviewer->name ?? '-' }}"
                            disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tanggal Review</label>
                        <input type="text" class="form-control"
                            value="{{ $reimbursement->reviewed_at ? $reimbursement->reviewed_at->format('d/m/Y H:i') : '-' }}"
                            disabled>
                    </div>
                </div>

                @if ($reimbursement->status === 'APPROVED')
                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Budget Category</label>
                            <input type="text" class="form-control"
                                value="{{ $reimbursement->budget_category->budged_name ?? '-' }}" disabled>
                        </div>
                    </div>
                @endif

                @if ($reimbursement->status === 'REJECTED' && $reimbursement->rejection_reason)
                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Alasan Penolakan</label>
                            <textarea class="form-control" rows="3" disabled>{{ $reimbursement->rejection_reason }}</textarea>
                        </div>
                    </div>
                @endif
            @endif

            <div class="d-flex justify-content-end mt-4">
                <a href="{{ route('main_transaction.reimbursement.index') }}" class="btn btn-secondary">
                    <i class="icon-base ti tabler-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>
@stop
