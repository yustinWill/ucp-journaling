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

            <!-- Repeater Items -->
            <div class="section-header">
                <h6>Item Reimbursement <span class="text-danger">*</span></h6>
                <hr class="mx-n6">
            </div>

            <div id="reimbursement-items">
                <!-- Row template will be added here -->
            </div>

            <button type="button" class="btn btn-outline-primary btn-sm mt-3" id="btn-add-item">
                <i class="icon-base ti tabler-plus me-1"></i> Tambah Item
            </button>

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

    <!-- Row Template (hidden) -->
    <template id="item-row-template">
        <div class="reimbursement-item border rounded p-3 mb-3 position-relative">
            <button type="button" class="btn btn-sm btn-icon btn-outline-danger btn-remove-item position-absolute"
                style="top: 8px; right: 8px;" title="Hapus item">
                <i class="icon-base ti tabler-trash"></i>
            </button>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Nama Reimbursement <span class="text-danger">*</span></label>
                    <input type="text" class="form-control item-name"
                        placeholder="Contoh: Biaya Transport Meeting Client" required maxlength="255">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" class="form-control item-date" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Jumlah (IDR) <span class="text-danger">*</span></label>
                    <input type="text" class="form-control numeric-input item-amount"
                        placeholder="Rp 0" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Lampiran</label>
                    <div class="d-flex align-items-center gap-2">
                        <input type="file" class="form-control item-attachment" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                    </div>
                    <small class="text-muted">JPG, PNG, PDF, DOC (maks. 5 MB)</small>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Deskripsi</label>
                    <textarea class="form-control item-description" rows="2"
                        placeholder="Jelaskan detail reimbursement (opsional)" maxlength="1000"></textarea>
                </div>
            </div>
        </div>
    </template>
@stop

@section('js_script')
    <script>
        $(document).ready(function() {
            // Add first row on load
            addItemRow();

            // Add item button
            $('#btn-add-item').on('click', function() {
                addItemRow();
            });

            // Remove item button (delegated)
            $('#reimbursement-items').on('click', '.btn-remove-item', function() {
                if ($('.reimbursement-item').length > 1) {
                    $(this).closest('.reimbursement-item').remove();
                    updateRowNumbers();
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Minimal 1 Item',
                        text: 'Setidaknya harus ada 1 item reimbursement.',
                        customClass: { confirmButton: 'btn btn-primary' },
                        buttonsStyling: false
                    });
                }
            });

            // File validation on change
            $('#reimbursement-items').on('change', '.item-attachment', function() {
                validateFile(this);
            });

            // Form submission
            $('#reimbursementForm').on('submit', function(e) {
                e.preventDefault();
                submitReimbursement();
            });
        });

        function addItemRow() {
            const template = document.getElementById('item-row-template');
            const clone = template.content.cloneNode(true);
            $('#reimbursement-items').append(clone);

            // Re-initialize cleave on new numeric inputs
            const newRow = $('#reimbursement-items .reimbursement-item').last();
            initCleaveCurrency(newRow[0]);
            updateRowNumbers();
        }

        function updateRowNumbers() {
            $('.reimbursement-item').each(function(index) {
                $(this).find('.item-name').attr('name', 'items[' + index + '][reimbursement_name]');
                $(this).find('.item-date').attr('name', 'items[' + index + '][reimbursement_date]');
                $(this).find('.item-amount').attr('name', 'items[' + index + '][amount]');
                $(this).find('.item-description').attr('name', 'items[' + index + '][description]');
                $(this).find('.item-attachment').attr('name', 'items[' + index + '][attachment]');
            });
        }

        function validateFile(input) {
            const file = input.files[0];
            if (!file) return true;

            const allowedTypes = ['image/jpeg', 'image/png', 'application/pdf',
                'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            const maxSize = 5 * 1024 * 1024; // 5MB

            if (!allowedTypes.includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Format File Tidak Valid',
                    text: 'Hanya format JPG, PNG, PDF, DOC, DOCX yang diperbolehkan.',
                    customClass: { confirmButton: 'btn btn-primary' },
                    buttonsStyling: false
                });
                input.value = '';
                return false;
            }

            if (file.size > maxSize) {
                Swal.fire({
                    icon: 'error',
                    title: 'Ukuran File Terlalu Besar',
                    text: 'Ukuran file maksimal 5 MB.',
                    customClass: { confirmButton: 'btn btn-primary' },
                    buttonsStyling: false
                });
                input.value = '';
                return false;
            }

            return true;
        }

        function submitReimbursement() {
            // Validate at least one item has data
            let hasValidItem = false;
            let validationError = null;

            $('.reimbursement-item').each(function(index) {
                const name = $(this).find('.item-name').val().trim();
                const amount = parseCurrency($(this).find('.item-amount').val());

                if (name && amount > 0) {
                    hasValidItem = true;
                } else if (name || amount > 0) {
                    validationError = 'Item #' + (index + 1) + ': Nama dan Jumlah harus diisi.';
                    return false;
                }
            });

            if (validationError) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    text: validationError,
                    customClass: { confirmButton: 'btn btn-primary' },
                    buttonsStyling: false
                });
                return;
            }

            if (!hasValidItem) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    text: 'Minimal 1 item reimbursement harus diisi.',
                    customClass: { confirmButton: 'btn btn-primary' },
                    buttonsStyling: false
                });
                return;
            }

            // Build FormData
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');

            let itemIndex = 0;
            $('.reimbursement-item').each(function() {
                const name = $(this).find('.item-name').val().trim();
                const date = $(this).find('.item-date').val();
                const amount = parseCurrency($(this).find('.item-amount').val());
                const description = $(this).find('.item-description').val().trim();
                const fileInput = $(this).find('.item-attachment')[0];

                if (name && amount > 0) {
                    formData.append('items[' + itemIndex + '][reimbursement_name]', name);
                    formData.append('items[' + itemIndex + '][reimbursement_date]', date);
                    formData.append('items[' + itemIndex + '][amount]', amount);
                    formData.append('items[' + itemIndex + '][description]', description);

                    if (fileInput && fileInput.files.length > 0) {
                        formData.append('items[' + itemIndex + '][attachment]', fileInput.files[0]);
                    }
                    itemIndex++;
                }
            });

            Swal.fire({
                title: 'Ajukan Reimbursement?',
                text: 'Akan mengajukan ' + itemIndex + ' item reimbursement. Pastikan data sudah benar.',
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
                        processData: false,
                        contentType: false,
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
