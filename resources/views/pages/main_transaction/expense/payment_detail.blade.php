@extends('layouts.default')
@section('page_title')
    Detail Pembayaran Transaksi
@stop
@section('content')
    <!-- Main Content Card -->
    <div class="card mb-6">
        <h4 class="card-header">Detail Pembayaran Pengeluaran #{{ $payment->transaction_no }}</h4>
        <form class="card-body" id="expense-form" method="POST" enctype="multipart/form-data" autocomplete="off">
            @csrf
            <!-- Section 1: Transaction Information -->
            <div class="section-header mt-12">
                <h6>Informasi Transaksi</h6>
                <hr class="mx-n6">
            </div>

            <!-- Row 1: Payment Method, Pay Later, Transaction Number -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Tanggal Transaksi <span class="text-danger">*</span></label>
                    <input type="date" class="form-control global-single-date-picker" id="transaction-date"
                        name="transaction_date" value="{{ $payment->transaction_date }}" placeholder="YYYY-MM-DD" disabled>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nomor Transaksi <span class="text-danger">*</span></label>
                    <div class="row g-2">
                        <div class="col">
                            <input type="text" id="transaction-number" name="transaction_number" class="form-control"
                                value="{{ $payment->transaction_no }}" placeholder="Auto-generated but editable" disabled>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 2: Recipient and Transaction Date -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Penerima <span class="text-danger">*</span></label>
                    <div class="row g-2">
                        <div class="col">
                            <input type="text" id="transaction-number" name="transaction_number" class="form-control"
                                value="{{ $payment->transaction_no }}" placeholder="Auto-generated but editable" disabled>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Pilih Akun Pembayaran</label>
                    <div class="row g-2">
                        <div class="col">
                            <input type="text" id="transaction-number" name="transaction_number" class="form-control"
                                value="{{ $payment->transaction_no }}" placeholder="Auto-generated but editable" disabled>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Keterangan</label>
                    <textarea id="description" name="description" class="form-control" rows="3" placeholder="Keterangan tambahan..."
                        disabled>{{ $payment->description }}</textarea>
                </div>
            </div>

            <!-- Additional Information & Summary -->
            <div class="section-header mt-12">
                <h6>Informasi Tambahan & Ringkasan</h6>
                <hr class="mx-n6">
            </div>

            <!-- Split Layout: Memo & Financial Summary -->
            <div class="row">
                <!-- Left Side: Tag, Memo and Attachment -->
                <div class="col-md-6 mb-4">
                    <div class="mb-3">
                        <label class="form-label">Tag</label>
                        @if (isset($payment->tags) && is_array($payment->tags) && count($payment->tags) > 0)
                            <div class="d-flex flex-wrap gap-1">
                                @foreach ($payment->tags as $tag)
                                    <span class="badge {{ $tag->preview }}">{{ $tag->name }}</span>
                                @endforeach
                            </div>
                        @else
                            <div class="text-muted">Tidak ada tag</div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Memo <small class="text-muted">(Internal)</small></label>
                        <textarea id="memo" name="memo" class="form-control" rows="4" maxlength="500"
                            placeholder="Catatan internal..." disabled>{{ $payment->memo }}</textarea>
                        <small class="form-text text-muted">Maksimal 500 karakter - Hanya untuk keperluan internal</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lampiran</label>
                    </div>
                    @if ($payment->attachment)
                        <div class="mt-2">
                            <small class="text-muted">File saat ini: </small>
                            <a href="{{ Storage::url($payment->attachment) }}" target="_blank" class="text-primary">
                                <i class="icon-base ti tabler-download me-1"></i>Download File
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Right Side: Summary Section -->
                <div class="col-md-6">
                    <div class="summary-section ps-4">
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Total Tagihan</label>
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control readonly-field" dir="rtl"
                                    value="Rp {{ number_format($payment->total_amount, 0, ',', '.') }}" disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Total Terbayar</label>
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control readonly-field" dir="rtl"
                                    value="Rp {{ number_format($payment->total_paid_amount, 0, ',', '.') }}" disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Sisa Tagihan</label>
                            </div>
                            <div class="col-6">
                                <input type="text" id="sisa-tagihan" class="form-control readonly-field"
                                    dir="rtl"
                                    value="Rp {{ number_format($payment->total_remaining_amount, 0, ',', '.') }}"
                                    disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Nominal Pembayaran</label>
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control currency-input" disabled id="payment-amount"
                                    name="payment_amount" dir="rtl"
                                    value="Rp {{ number_format($payment->payment_amount, 0, ',', '.') }}">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label"><strong>Sisa Tagihan Setelah Pembayaran</strong></label>
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control readonly-field fw-bold" id="final-amount"
                                    dir="rtl"
                                    value="Rp {{ number_format($payment->latest_remaining_amount, 0, ',', '.') }}"
                                    disabled>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@stop

@section('js_script')
    <script></script>
@endsection
