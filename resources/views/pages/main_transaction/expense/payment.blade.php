@extends('layouts.default')
@section('page_title')
    Buat Pembayaran Transaksi
@stop
@section('content')
    <!-- Main Content Card -->
    <div class="card mb-6">
        <h4 class="card-header">Kirim Biaya Pengeluaran #{{ $expense->transaction_no }}</h4>
        <form class="card-body" id="expense-form" method="POST" enctype="multipart/form-data" autocomplete="off">
            <input type="hidden" id="expense-id" value="{{ $expense->id }}">
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
                        name="transaction_date" value="{{ $expense->transaction_date }}" placeholder="YYYY-MM-DD" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nomor Transaksi <span class="text-danger">*</span></label>
                    <div class="row g-2">
                        <div class="col">
                            <input type="text" id="transaction-number" name="transaction_number" class="form-control"
                                value="" placeholder="Auto-generated but editable" required>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-outline-primary d-flex align-items-center"
                                id="generate-number-btn" title="Generate New Number">
                                <i class="icon-base ti tabler-refresh"></i>
                            </button>
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
                            <select id="recipient" name="recipient" class="global-select2 form-select recipients-select"
                                data-allow-clear="false">
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}"
                                        {{ $expense->supplier_id == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($canAddContacts)
                            <div class="col-auto">
                                <a href="{{ route('master_data.contact.add') }}" target="_blank"
                                    class="btn btn-outline-primary d-flex align-items-center" title="Tambah Kontak Baru">
                                    <i class="icon-base ti tabler-plus"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Pilih Akun Pembayaran</label>
                    <div class="row g-2">
                        <div class="col">
                            <select id="payment_coa" name="payment_coa"
                                class="global-select2 form-select payment_coa-select" data-allow-clear="true">
                                <option value="">Pilih Akun Pembayaran</option>
                                @foreach ($coa_accounts as $coa_account)
                                    <option value="{{ $coa_account->code }}">
                                        {{ $coa_account->code }} - {{ $coa_account->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Keterangan</label>
                    <textarea id="description" name="description" class="form-control" rows="3" placeholder="Keterangan tambahan..."></textarea>
                </div>
            </div>
            <!-- Section 2: Expense Information -->
            <div class="section-header mt-12">
                <h6>Informasi History Kirim Uang</h6>
            </div>

            <!-- Expense Table -->
            <div class="mx-n5 my-4">
                <table id="expense-table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tanggal Transaksi</th>
                            <th>Deskripsi</th>
                            <th>Nilai Tagihan Sebelum</th>
                            <th>Nilai Pembayaran Tagihan</th>
                            <th>Sisa Tagihan</th>
                        </tr>
                    </thead>
                    <tbody id="expense-rows">
                        @foreach ($expense->payments as $payment)
                            <tr class="expense-row">
                                <td>{{ $payment->transaction_date }}</td>
                                <td>{{ $payment->description }}</td>
                                <td>Rp {{ number_format($payment->remaining_amount, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($payment->payment_amount, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($payment->latest_remaining_amount, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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
                        <div class="row g-2">
                            <div class="col">
                                <select class="global-select2 form-select tags-select" id="expense-tags" multiple
                                    data-allow-clear="false">
                                    @foreach ($availableTags as $tag)
                                        <option value="{{ $tag->id }}"
                                            {{ in_array($tag->id, $expense->selected_tags ?? []) ? 'selected' : '' }}>
                                            {{ $tag->code }} - {{ $tag->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @if ($canAddTags)
                                <div class="col-auto">
                                    <a href="{{ route('master_data.tag.add') }}" target="_blank"
                                        class="btn btn-outline-primary d-flex align-items-center" id="add-new-expense-tag"
                                        title="Tambah Tag Baru">
                                        <i class="icon-base ti tabler-plus"></i>
                                    </a>
                                </div>
                            @endif
                        </div>
                        <small class="form-text text-muted">Pilih satu atau lebih tag untuk kategorisasi</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Memo <small class="text-muted">(Internal)</small></label>
                        <textarea id="memo" name="memo" class="form-control" rows="4" maxlength="500"
                            placeholder="Catatan internal...">{{ $expense->memo }}</textarea>
                        <small class="form-text text-muted">Maksimal 500 karakter - Hanya untuk keperluan internal</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lampiran</label>
                        <input type="file" id="attachment" name="attachment" class="form-control"
                            accept=".xlsx,.xls,.docx,.doc,.pdf,.png,.zip">
                        <small class="text-muted">File dapat berupa Excel, Word, PDF, PNG, atau Zip (Max: 10MB)</small>
                    </div>
                    @if ($expense->attachment)
                        <div class="mt-2">
                            <small class="text-muted">File saat ini: </small>
                            <a href="{{ Storage::url($expense->attachment) }}" target="_blank" class="text-primary">
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
                                    value="Rp {{ number_format($expense->total_amount, 0, ',', '.') }}" disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Total Terbayar</label>
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control readonly-field" dir="rtl"
                                    value="Rp {{ number_format($expense->total_paid_amount, 0, ',', '.') }}" disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Sisa Tagihan</label>
                            </div>
                            <div class="col-6">
                                <input type="text" id="sisa-tagihan" class="form-control readonly-field"
                                    dir="rtl"
                                    value="Rp {{ number_format($expense->total_remaining_amount, 0, ',', '.') }}"
                                    disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Nominal Pembayaran</label>
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control currency-input" id="payment-amount"
                                    name="payment_amount" dir="rtl" placeholder="Rp 0"
                                    value="Rp {{ number_format($expense->total_remaining_amount, 0, ',', '.') }}">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label"><strong>Sisa Tagihan Setelah Pembayaran</strong></label>
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control readonly-field fw-bold" id="final-amount"
                                    dir="rtl" value="Rp 0" disabled>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Action Buttons -->
            <div class="pt-6 text-end">
                <a href="{{ route('main_transaction.expense.detail', $expense->id) }}"
                    class="btn btn-label-danger me-3">Batalkan</a>
                <button type="submit" class="btn btn-success" id="submit-btn">Simpan</button>
            </div>
        </form>
    </div>
@stop

@section('js_script')
    <script>
        $(function() {

            initializePaymentCalculation();

            function initializePaymentCalculation() {
                // Payment amount input change
                $('#payment-amount').on('input', function() {
                    formatCurrencyInput(this);
                    calculateFinalAmount();
                });

                // Initial calculation on page load
                calculateFinalAmount();
            }

            function calculateFinalAmount() {
                // Get sisa tagihan value (remove currency formatting)
                const sisaTagihan = parseCurrency($('#sisa-tagihan').val());

                // Get payment amount value (remove currency formatting)
                const paymentAmount = parseCurrency($('#payment-amount').val()) || 0;

                // Calculate final amount (sisa tagihan - payment amount)
                const finalAmount = sisaTagihan - paymentAmount;

                // Update final amount field with currency formatting
                $('#final-amount').val(formatCurrency(finalAmount));

                if (paymentAmount > sisaTagihan) {
                    $('#payment-amount').addClass('is-invalid');
                } else {
                    $('#payment-amount').removeClass('is-invalid');
                }

                if (finalAmount < 0) {
                    $('#final-amount').addClass('text-danger').css('background-color', '#ffebee');
                } else {
                    $('#final-amount').removeClass('text-danger').css('background-color', '');
                }
            }

            // Helper function to parse currency
            function parseCurrency(value) {
                if (!value) return 0;
                return Math.round(parseFloat(value.toString().replace(/[^\d]/g, '')) || 0);
            }

            // Helper function to format currency (assuming it exists or needs to be created)
            function formatCurrency(value) {
                if (isNaN(value) || value < 0) return 'Rp 0';
                return 'Rp ' + value.toLocaleString('id-ID');
            }

            // Helper function to format currency input (assuming it exists or needs to be created)
            function formatCurrencyInput(input) {
                let value = input.value.replace(/[^\d]/g, '');
                if (value) {
                    input.value = 'Rp ' + parseInt(value).toLocaleString('id-ID');
                } else {
                    input.value = '';
                }
            }

            initializeTransactionNumber();

            function initializeTransactionNumber() {
                // Generate initial transaction number on page load
                generateTransactionNumber();

                // Handle generate button click
                $('#generate-number-btn').on('click', function() {
                    generateTransactionNumber();
                });

                // Validate for duplicates when manually edited
                $('#transaction-number').on('blur', function() {
                    const number = $(this).val();
                    if (number) {
                        checkDuplicateNumber(number);
                    }
                });
            }

            function generateTransactionNumber() {
                $('#generate-number-btn').prop('disabled', true);

                $.ajax({
                    url: '{{ route('master_data.transaction_number.generate_number', 'expense_payment') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        preview: 1
                    },
                    success: function(response) {
                        if (response.success) {
                            // Only a preview: the number is taken on save, unless the user types their own.
                            $('#transaction-number').val(response.number).data('preview', response.number);
                        } else {
                            numberFailed(response.message);
                        }
                    },
                    error: function(xhr) {
                        numberFailed(xhr.responseJSON?.message);
                    },
                    complete: function() {
                        $('#generate-number-btn').prop('disabled', false);
                    }
                });
            }

            // No made-up number: leave the field empty so the user sees it needs fixing.
            function numberFailed(message) {
                $('#transaction-number').val('').removeData('preview');
                Swal.fire({ icon: 'error', title: 'Nomor transaksi', text: (message || 'Nomor transaksi gagal dibuat.') + ' Periksa pengaturan nomor transaksi, atau isi nomor secara manual.' });
            }

            function checkDuplicateNumber(number) {
                $.ajax({
                    url: '{{ route('master_data.transaction_number.check_duplicate', 'expense_payment') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        number: number
                    },
                    success: function(response) {
                        if (response.exists) {
                            $('#transaction-number').addClass('is-invalid');
                            // You could show a tooltip or error message here
                        } else {
                            $('#transaction-number').removeClass('is-invalid');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error checking duplicate:', xhr.responseText);
                    }
                });
            }

            function collectFormData() {
                const formData = new FormData();

                // Basic fields
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('expense_id', $('#expense-id').val());
                formData.append('transaction_number', $('#transaction-number').val());
                formData.append('auto_number', $('#transaction-number').val() === $('#transaction-number').data('preview') ? '1' : '0');
                formData.append('recipient_id', $('#recipient').val() || '');
                formData.append('payment_coa', $('#payment_coa').val() || '');
                formData.append('transaction_date', $('#transaction-date').val());
                formData.append('description', $('#description').val());
                formData.append('memo', $('#memo').val());
                formData.append('remaining_amount', parseCurrency($('#sisa-tagihan').val()) || 0);
                formData.append('payment_amount', parseCurrency($('#payment-amount').val()) || 0);

                // Tags
                const tags = $('#expense-tags').val() || [];
                tags.forEach((tag, index) => {
                    formData.append(`tags[${index}]`, tag);
                });

                // Attachment file
                const attachmentFile = $('#attachment')[0].files[0];
                if (attachmentFile) {
                    formData.append('attachment', attachmentFile);
                }

                return formData;
            }

            // Form submission
            $('#expense-form').on('submit', function(e) {
                e.preventDefault();

                // Validation: Check if payment amount exceeds remaining balance
                const sisaTagihan = parseCurrency($('#sisa-tagihan').val()) || 0;
                const paymentAmount = parseCurrency($('#payment-amount').val()) || 0;

                if (paymentAmount > sisaTagihan) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Nominal Pembayaran Tidak Valid!',
                        text: 'Nominal pembayaran tidak boleh lebih besar dari sisa tagihan.',
                        customClass: {
                            confirmButton: 'btn btn-danger'
                        },
                        buttonsStyling: false
                    });
                    return; // Stop form submission
                }

                if (paymentAmount <= 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Nominal Pembayaran Tidak Valid!',
                        text: 'Nominal pembayaran harus lebih besar dari 0.',
                        customClass: {
                            confirmButton: 'btn btn-danger'
                        },
                        buttonsStyling: false
                    });
                    return; // Stop form submission
                }

                const formData = collectFormData();

                Swal.fire({
                    title: 'Simpan Data?',
                    text: 'Cek kembali data yang sudah diisi!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan!',
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-label-secondary'
                    },
                    buttonsStyling: false
                }).then(function(result) {
                    if (result.value) {
                        if (result.value) {
                            $('#submit-btn').prop('disabled', true).html(
                                '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Loading...'
                            );
                            $.ajax({
                                url: "{{ route('main_transaction.expense.payment.save') }}",
                                method: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: 'Data Pembayaran berhasil disimpan.',
                                        customClass: {
                                            confirmButton: 'btn btn-success'
                                        },
                                        buttonsStyling: false
                                    }).then(function() {
                                        window.location.href =
                                            "{{ route('main_transaction.expense.index') }}";
                                    });
                                },
                                error: function(xhr) {
                                    const res = xhr.responseJSON;
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal menyimpan',
                                        html: res?.message ||
                                            'Terjadi kesalahan saat menyimpan data.',
                                        customClass: {
                                            confirmButton: 'btn btn-primary'
                                        },
                                        buttonsStyling: false
                                    });
                                },
                                complete: function() {
                                    $('#submit-btn').prop('disabled', false).html(
                                        'Simpan Data');
                                }
                            });
                        }
                    }
                });
            });



            // Remove validation classes on input
            $('input, select').on('input change', function() {
                $(this).removeClass('is-invalid');
            });

        });
    </script>
@endsection
