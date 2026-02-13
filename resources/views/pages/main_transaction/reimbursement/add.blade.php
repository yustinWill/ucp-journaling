@extends('layouts.default')
@section('page_title')
    Ajukan Reimbursement Baru
@stop
@section('content')
    <div class="card">
        <h4 class="card-header">Ajukan Reimbursement Baru</h4>
        <form class="card-body" id="reimbursementForm">
            @csrf

            <div class="alert alert-info mb-4">
                <i class="icon-base ti tabler-info-circle me-2"></i>
                <strong>Informasi:</strong> Ajukan reimbursement Anda di sini. Admin akan melakukan review dan approval.
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <label class="form-label">Nama Reimbursement <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="reimbursement_name" id="reimbursement_name"
                        placeholder="Contoh: Biaya Transport Meeting Client, Pembelian Alat Tulis" required maxlength="255">
                    <small class="text-muted">Masukkan nama/deskripsi reimbursement yang jelas</small>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Jumlah (IDR) <span class="text-danger">*</span></label>
                    <input type="text" class="form-control numeric-input" name="amount" id="amount"
                        placeholder="Rp 0" required>
                    <small class="text-muted">Masukkan jumlah reimbursement dalam Rupiah</small>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <label class="form-label">Deskripsi Tambahan</label>
                    <textarea class="form-control" name="description" id="description" rows="4"
                        placeholder="Jelaskan detail reimbursement Anda (opsional)" maxlength="1000"></textarea>
                    <small class="text-muted">Berikan informasi tambahan jika diperlukan (maks. 1000 karakter)</small>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-end gap-3 mt-6">
                <a href="{{ route('main_transaction.reimbursement.index') }}" class="btn btn-secondary">
                    <i class="icon-base ti tabler-arrow-left me-1"></i> Batal
                </a>
                <button type="submit" class="btn btn-primary" id="submit-btn">
                    <i class="icon-base ti tabler-device-floppy me-1"></i> Ajukan Reimbursement
                </button>
            </div>
        </form>
    </div>
@stop

@section('js_script')
    <script>
        $(document).ready(function() {
            // Initialize numeric input
            initCleaveCurrency(document);

            // Form submission
            $('#reimbursementForm').on('submit', function(e) {
                e.preventDefault();
                submitReimbursement();
            });
        });

        function submitReimbursement() {
            const formData = {
                _token: '{{ csrf_token() }}',
                reimbursement_name: $('#reimbursement_name').val(),
                amount: parseCurrency($('#amount').val()),
                description: $('#description').val(),
            };

            Swal.fire({
                title: 'Ajukan Reimbursement?',
                text: 'Pastikan data yang diisi sudah benar.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Ajukan!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-label-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#submit-btn').prop('disabled', true).html(
                        '<span class="spinner-border spinner-border-sm me-2"></span>Loading...'
                    );

                    $.ajax({
                        url: "{{ route('main_transaction.reimbursement.store') }}",
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                showConfirmButton: true
                            }).then(() => {
                                window.location.href =
                                    "{{ route('main_transaction.reimbursement.index') }}";
                            });
                        },
                        error: function(xhr) {
                            let errorMessage = 'Terjadi kesalahan saat menyimpan data.';

                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }

                            Swal.fire({
                                title: 'Gagal!',
                                text: errorMessage,
                                icon: 'error',
                                customClass: {
                                    confirmButton: 'btn btn-primary'
                                },
                                buttonsStyling: false
                            });
                        },
                        complete: function() {
                            $('#submit-btn').prop('disabled', false).html(
                                '<i class="icon-base ti tabler-device-floppy me-1"></i> Ajukan Reimbursement'
                            );
                        }
                    });
                }
            });
        }
    </script>
@endsection
