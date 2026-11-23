@extends('layouts.default')
@section('page_title')
    Review Reimbursement
@stop
@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Review Reimbursement</h4>
            @if ($reimbursement->status === 'PENDING')
                <span class="badge bg-label-warning">PENDING APPROVAL</span>
            @elseif($reimbursement->status === 'APPROVED')
                <span class="badge bg-label-success">APPROVED</span>
            @else
                <span class="badge bg-label-danger">REJECTED</span>
            @endif
        </div>

        <div class="card-body">
            <!-- Reimbursement Information -->
            <h5 class="mb-3">Informasi Reimbursement</h5>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Kode Transaksi</label>
                    <input type="text" class="form-control" value="{{ $reimbursement->transaction_code }}" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Tanggal Pengajuan</label>
                    <input type="text" class="form-control"
                        value="{{ $reimbursement->created_at->format('d/m/Y H:i') }}" readonly>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Diajukan Oleh</label>
                    <input type="text" class="form-control" value="{{ $reimbursement->creator->name }}" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Jumlah</label>
                    <input type="text" class="form-control"
                        value="Rp {{ number_format($reimbursement->amount, 0, ',', '.') }}" readonly>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <label class="form-label fw-bold">Nama Reimbursement</label>
                    <input type="text" class="form-control" value="{{ $reimbursement->reimbursement_name }}" readonly>
                </div>
            </div>

            @if ($reimbursement->description)
                <div class="row g-3 mb-4">
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Deskripsi</label>
                        <textarea class="form-control" rows="3" readonly>{{ $reimbursement->description }}</textarea>
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

            @if ($reimbursement->status === 'PENDING')
                <hr class="my-4">

                <!-- Approval Section -->
                <h5 class="mb-3">Approval Action</h5>

                <div class="row g-3 mb-4">
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Pilih Anggaran <span class="text-danger">*</span></label>
                        <select class="form-select global-select2" id="budget_category_id" name="budget_category_id"
                            data-allow-clear="false">
                            <option value="">Pilih Anggaran</option>
                            @foreach ($activeBudgets as $budget)
                                <option value="{{ $budget->budget_id }}">
                                    {{ $budget->budged_name }}
                                    ({{ \Carbon\Carbon::parse($budget->period_start_date)->format('d/m/Y') }} -
                                    {{ \Carbon\Carbon::parse($budget->period_end_date)->format('d/m/Y') }})
                                    - Sisa: Rp {{ number_format($budget->budged_remaining_value, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Pilih anggaran untuk reimbursement ini sebelum approve</small>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Sumber Pembayaran <span class="text-danger">*</span></label>
                        <select class="form-select" id="payment_method_type">
                            <option value="">Pilih Sumber Pembayaran</option>
                            <option value="kas_tunai">Kas / Tunai</option>
                            <option value="transfer">Transfer</option>
                        </select>
                    </div>
                    <div class="col-md-6" id="kas-wrapper" style="display: none;">
                        <label class="form-label fw-bold">Saldo Kas Besar Hari Ini</label>
                        <input type="text" class="form-control bg-light text-end" id="kas-saldo" readonly placeholder="Memuat...">
                        <small class="text-muted">Saldo Kas Besar langsung dipotong saat approve.</small>
                    </div>
                    <div class="col-md-6" id="bank-wrapper" style="display: none;">
                        <label class="form-label fw-bold">Bank Transfer <span class="text-danger">*</span></label>
                        <select class="form-select" id="transfer_bank">
                            <option value="">Pilih Bank</option>
                            <option value="BCA">BCA</option>
                            <option value="Mandiri">Mandiri</option>
                            <option value="BTPN">BTPN</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Alasan Penolakan</label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3"
                            placeholder="Masukkan alasan jika akan reject..."></textarea>
                        <small class="text-muted">Wajib diisi jika akan reject reimbursement</small>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('main_transaction.reimbursement_approval.index') }}" class="btn btn-secondary">
                        <i class="icon-base ti tabler-arrow-left me-1"></i> Kembali
                    </a>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-danger" onclick="rejectReimbursement()">
                            <i class="icon-base ti tabler-x me-1"></i> Reject
                        </button>
                        <button type="button" class="btn btn-success" onclick="approveReimbursement()">
                            <i class="icon-base ti tabler-check me-1"></i> Approve
                        </button>
                    </div>
                </div>
            @else
                <hr class="my-4">
                <h5 class="mb-3">Informasi Review</h5>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Direview Oleh</label>
                        <input type="text" class="form-control" value="{{ $reimbursement->reviewer->name ?? '-' }}"
                            readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tanggal Review</label>
                        <input type="text" class="form-control"
                            value="{{ $reimbursement->reviewed_at ? $reimbursement->reviewed_at->format('d/m/Y H:i') : '-' }}"
                            readonly>
                    </div>
                </div>

                @if ($reimbursement->status === 'APPROVED')
                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Anggaran</label>
                            <input type="text" class="form-control"
                                value="{{ $reimbursement->budget_category->budged_name ?? '-' }}" readonly>
                        </div>
                    </div>
                    @if ($reimbursement->paymentSourceLabel())
                        <div class="row g-3 mb-4">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Sumber Pembayaran</label>
                                <input type="text" class="form-control" value="{{ $reimbursement->paymentSourceLabel() }}" readonly>
                            </div>
                        </div>
                    @endif
                @endif

                @if ($reimbursement->status === 'REJECTED' && $reimbursement->rejection_reason)
                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Alasan Penolakan</label>
                            <textarea class="form-control" rows="3" readonly>{{ $reimbursement->rejection_reason }}</textarea>
                        </div>
                    </div>
                @endif

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('main_transaction.reimbursement_approval.index') }}" class="btn btn-secondary">
                        <i class="icon-base ti tabler-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            @endif
        </div>
    </div>
@stop

@section('js_script')
    <script>
        const reimbursementAmount = {{ (float) $reimbursement->amount }};
        // The most a Kas Besar deduction dated today may take.
        let kasAvailable = 0;

        function toggleSource() {
            const source = $('#payment_method_type').val();
            $('#kas-wrapper').toggle(source === 'kas_tunai');
            $('#bank-wrapper').toggle(source === 'transfer');
            if (source !== 'transfer') $('#transfer_bank').val('');
        }

        $(function() {
            if (!$('#payment_method_type').length) return;
            $('#payment_method_type').on('change', toggleSource);
            // Start on Kas / Tunai only when Kas Besar can cover this reimbursement today, otherwise on Transfer.
            $.get('{{ route('main_transaction.kas_besar.get_latest_saldo') }}', function(res) {
                kasAvailable = parseFloat(res.available) || 0;
                $('#kas-saldo').val('Rp ' + (parseFloat(res.saldo_after) || 0).toLocaleString('id-ID'));
                if (!$('#payment_method_type').val()) {
                    $('#payment_method_type').val(kasAvailable >= reimbursementAmount ? 'kas_tunai' : 'transfer');
                    toggleSource();
                }
            });
        });

        function approveReimbursement() {
            const budgetCategoryId = $('#budget_category_id').val();
            const paymentMethodType = $('#payment_method_type').val();
            const transferBank = $('#transfer_bank').val();

            if (!budgetCategoryId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Silakan pilih budget category terlebih dahulu!',
                });
                return;
            }
            if (!paymentMethodType) {
                Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Silakan pilih sumber pembayaran terlebih dahulu!' });
                return;
            }
            if (paymentMethodType === 'transfer' && !transferBank) {
                Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Silakan pilih bank transfer terlebih dahulu!' });
                return;
            }
            if (paymentMethodType === 'kas_tunai' && reimbursementAmount > kasAvailable) {
                Swal.fire({ icon: 'error', title: 'Saldo Tidak Mencukupi', html: `Saldo Kas Besar <strong>Rp ${kasAvailable.toLocaleString('id-ID')}</strong> tidak mencukupi untuk reimbursement <strong>Rp ${reimbursementAmount.toLocaleString('id-ID')}</strong>.` });
                return;
            }

            Swal.fire({
                title: 'Approve Reimbursement?',
                html: `<div class="text-start">
                    <p>Anda akan menyetujui reimbursement ini:</p>
                    <ul>
                        <li><strong>Jumlah:</strong> Rp {{ number_format($reimbursement->amount, 0, ',', '.') }}</li>
                        <li><strong>Staff:</strong> {{ $reimbursement->creator->name }}</li>
                        <li><strong>Sumber:</strong> ${paymentMethodType === 'kas_tunai' ? 'Kas / Tunai (saldo Kas Besar dipotong)' : 'Transfer ' + transferBank}</li>
                    </ul>
                </div>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Approve!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#28a745',
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-label-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('main_transaction.reimbursement_approval.approve', $reimbursement->id) }}",
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            budget_category_id: budgetCategoryId,
                            payment_method_type: paymentMethodType,
                            transfer_bank: transferBank
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                showConfirmButton: true
                            }).then(() => {
                                window.location.href =
                                    "{{ route('main_transaction.reimbursement_approval.index') }}";
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: xhr.responseJSON?.message || 'Terjadi kesalahan saat approve.'
                            });
                        }
                    });
                }
            });
        }

        function rejectReimbursement() {
            const rejectionReason = $('#rejection_reason').val().trim();

            if (!rejectionReason) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Silakan masukkan alasan penolakan terlebih dahulu!',
                });
                return;
            }

            Swal.fire({
                title: 'Reject Reimbursement?',
                text: 'Anda yakin akan menolak reimbursement ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Reject!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc3545',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-label-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('main_transaction.reimbursement_approval.reject', $reimbursement->id) }}",
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            rejection_reason: rejectionReason
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                showConfirmButton: true
                            }).then(() => {
                                window.location.href =
                                    "{{ route('main_transaction.reimbursement_approval.index') }}";
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: xhr.responseJSON?.message || 'Terjadi kesalahan saat reject.'
                            });
                        }
                    });
                }
            });
        }
    </script>
@endsection
