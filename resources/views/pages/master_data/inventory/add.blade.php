@extends('layouts.default')

@section('page_title')
    Buat Transaksi - Pembelian Aset
@stop

@section('content')
<div class="card mb-6">
    <h4 class="card-header">Buat Transaksi - Pembelian Aset</h4>
    <form class="card-body" id="inventory-purchase-form" autocomplete="off">
        @csrf

        <!-- Informasi Aset Section -->
        <div class="section-header mt-12">
            <h6>Informasi Aset</h6>
            <hr class="mx-n6">
        </div>
        <!-- Row 1: Tanggal Transaksi, Nomor Transaksi -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Tanggal Transaksi <span style="color: red;">*</span></label>
                <input type="date" class="form-control global-single-date-picker" id="transaction_date"
                    name="transaction_date" value="{{ date('Y-m-d') }}" placeholder="YYYY-MM-DD" required />
            </div>
            <div class="col-md-6">
                <label class="form-label">Nomor Transaksi <span style="color: red;">*</span></label>
                <div class="input-group">
                    <input type="text" class="form-control" id="transaction_number" name="transaction_number"
                        placeholder="Auto-generated but editable" required />
                    <button type="button" class="btn btn-outline-secondary" id="refresh-transaction-number">
                        <i class="icon-base ti tabler-refresh"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Nama Aset <span style="color: red;">*</span></label>
                <input type="text" class="form-control" name="asset_name" id="asset_name" placeholder="Mobil Kantor" required />
            </div>
            <div class="col-md-6">
                <label class="form-label">Nomor Aset <span style="color: red;">*</span></label>
                <input type="text" class="form-control" name="asset_code" id="asset_code" placeholder="Cash Advance" required />
            </div>
        </div>

        <!-- Row 3: Syarat Pembayaran -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Syarat Pembayaran <span style="color: red;">*</span></label>
                <select class="form-select global-select2" name="payment_terms" id="payment_terms" data-allow-clear="false" required>
                    <option value="">Pilih Syarat Pembayaran</option>
                    @foreach($paymentTerms as $term)
                        <option value="{{ $term['value'] }}">{{ $term['label'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Row 3b: Jumlah Hari, Tanggal Jatuh Tempo (conditional) -->
        <div class="row g-3 mb-4" id="payment-days-row" style="display: none;">
            <div class="col-md-6">
                <label class="form-label">Jumlah Hari</label>
                <div class="input-group">
                    <input type="number" class="form-control" id="payment_days" name="payment_days" placeholder="30" min="0" max="365" readonly />
                    <span class="input-group-text">Hari</span>
                </div>
                <small class="form-text text-muted">Jumlah hari untuk pembayaran.</small>
            </div>
            <div class="col-md-6">
                <label class="form-label">Tanggal Jatuh Tempo</label>
                <input type="date" class="form-control global-single-date-picker" name="due_date" id="due_date" placeholder="YYYY-MM-DD" readonly />
                <small class="form-text text-muted">Otomatis dihitung berdasarkan tanggal transaksi.</small>
            </div>
        </div>

        <!-- Row 4: Nilai Pembelian, Deskripsi -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Nilai Pembelian <span style="color: red;">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="text" class="form-control cleave-numeral" name="price" id="price" placeholder="500,000,000.00" required />
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Deskripsi</label>
                <textarea class="form-control" name="description" id="description" rows="3" placeholder="Keterangan transaksi..."></textarea>
            </div>
        </div>

        <!-- Informasi Pembelian Aset -->
        <div class="section-header mt-12">
            <h6>Informasi Pembelian Aset</h6>
            <hr class="mx-n6">
        </div>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Akun Aset Tetap <span style="color: red;">*</span></label>
                <select class="form-select global-select2" name="fixed_asset_account" id="fixed_asset_account" data-allow-clear="false" required>
                    <option value="">Pilih Akun</option>
                    @foreach($coaAccounts as $coa)
                        <option value="{{ $coa->code }}">{{ $coa->code }} - {{ $coa->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Akun Pembelian <span style="color: red;">*</span></label>
                <select class="form-select global-select2" name="purchase_account" id="purchase_account" data-allow-clear="false" required>
                    <option value="">Pilih Akun</option>
                    @foreach($coaAccounts as $coa)
                        <option value="{{ $coa->code }}">{{ $coa->code }} - {{ $coa->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Informasi Penyusutan Aset -->
        <div class="section-header mt-12">
            <h6>Informasi Penyusutan Aset</h6>
            <hr class="mx-n6">
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_depreciated" id="is_depreciated" />
                    <label class="form-check-label" for="is_depreciated">
                        Aset Depresiasi
                    </label>
                </div>
                <small class="text-muted">Centang jika aset mengalami penyusutan.</small>
            </div>
        </div>

        <div id="depreciation-fields" style="display: none;">
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Nilai/Tahun <span style="color: red;">*</span></label>
                    <div class="input-group">
                        <input type="number" class="form-control" name="depreciation_rate_per_year" id="depreciation_rate_per_year" placeholder="10" step="0.01" />
                        <span class="input-group-text">%</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jangka Waktu Penyusutan <span style="color: red;">*</span></label>
                    <div class="input-group">
                        <input type="number" class="form-control" name="depreciation_period_months" id="depreciation_period_months" placeholder="12" />
                        <span class="input-group-text">Bulan</span>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Total Penyusutan <span style="color: red;">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" class="form-control cleave-numeral" id="total_depreciation" disabled />
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nilai Buku <span style="color: red;">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" class="form-control cleave-numeral" id="book_value" disabled />
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Akun Beban Penyusutan <span style="color: red;">*</span></label>
                    <select class="form-select global-select2" name="depreciation_account" id="depreciation_account" data-allow-clear="false">
                        <option value="">Pilih Akun</option>
                        @foreach($coaAccounts as $coa)
                            <option value="{{ $coa->code }}">{{ $coa->code }} - {{ $coa->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Akun Akumulasi Penyusutan <span style="color: red;">*</span></label>
                    <select class="form-select global-select2" name="accumulated_depreciation_account" id="accumulated_depreciation_account" data-allow-clear="false">
                        <option value="">Pilih Akun</option>
                        @foreach($coaAccounts as $coa)
                            <option value="{{ $coa->code }}">{{ $coa->code }} - {{ $coa->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Informasi Tambahan & Ringkasan Section -->
        <div class="section-header mt-12">
            <h6>Informasi Tambahan & Ringkasan</h6>
            <hr class="mx-n6">
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Tag</label>
                <div class="row g-2">
                    <div class="col">
                        <select class="global-select2 form-select" id="tag_codes" name="tag_codes[]" multiple data-allow-clear="false">
                            @foreach($availableTags as $tag)
                                <option value="{{ $tag->id }}">{{ $tag->code }} - {{ $tag->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('master_data.tag.add') }}" target="_blank"
                            class="btn btn-outline-primary d-flex align-items-center" title="Tambah Tag Baru">
                            <i class="icon-base ti tabler-plus"></i>
                        </a>
                    </div>
                </div>
                <small class="form-text text-muted">Pilih tag untuk kategorisasi aset</small>
            </div>
            <div class="col-md-6">
                <label class="form-label">Memo (Internal)</label>
                <textarea class="form-control" id="memo" name="memo" rows="4" placeholder="Catatan internal..."></textarea>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="pt-6 text-end">
            <a href="{{ route('master_data.inventory.index') }}" class="btn btn-label-danger me-3">Batalkan</a>
            <button type="submit" class="btn btn-success" id="submit-btn">Simpan</button>
        </div>
    </form>
</div>
@stop

@section('js_script')
<script>
$(function() {
    // Initialize Select2
    $('.global-select2').select2({
        allowClear: false
    });

    // Generate transaction number on page load
    function generateTransactionNumber() {
        $.ajax({
            url: '{{ route("master_data.inventory.generate_transaction_number") }}',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    $('#transaction_number').val(response.transaction_number);
                }
            },
            error: function(xhr) {
                console.error('Failed to generate transaction number:', xhr);
            }
        });
    }

    // Auto-generate on page load
    generateTransactionNumber();

    // Refresh button click
    $('#refresh-transaction-number').on('click', function() {
        generateTransactionNumber();
    });

    // Payment terms change - show/hide and calculate
    function handlePaymentTermsChange() {
        const terms = $('#payment_terms').val();
        const daysRow = $('#payment-days-row');
        const daysField = $('#payment_days');
        const dueDateField = $('#due_date');

        if (!terms || terms === 'CASH') {
            // Hide row for CASH or no selection
            daysRow.hide();
            daysField.val('');
            dueDateField.val('');
            return;
        }

        // Show row for NET terms
        daysRow.show();

        // Set days based on payment term
        let days = 0;
        switch(terms) {
            case 'NET_7': days = 7; break;
            case 'NET_15': days = 15; break;
            case 'NET_30': days = 30; break;
            case 'NET_45': days = 45; break;
            case 'NET_60': days = 60; break;
        }

        daysField.val(days);
        updateDueDate();
    }

    function updateDueDate() {
        const transactionDate = $('#transaction_date').val();
        const days = parseInt($('#payment_days').val()) || 0;
        const dueDateField = $('#due_date');

        if (!transactionDate || days === 0) {
            dueDateField.val('');
            return;
        }

        const date = new Date(transactionDate);
        date.setDate(date.getDate() + days);
        dueDateField.val(date.toISOString().split('T')[0]);
    }

    // Payment terms change handler
    $('#payment_terms').on('change', function() {
        handlePaymentTermsChange();
    });

    // Transaction date change - recalculate due date
    $('#transaction_date').on('change', function() {
        updateDueDate();
    });

    // Initialize depreciation Select2 elements (hidden initially)
    $('#depreciation_account, #accumulated_depreciation_account').select2({
        allowClear: false
    });

    // Toggle depreciation fields - using same pattern as product add module
    $('#is_depreciated').change(function() {
        $('#depreciation-fields').toggle(this.checked);

        // Update required attributes based on checkbox state
        if (this.checked) {
            $('#depreciation_rate_per_year, #depreciation_period_months, #depreciation_account, #accumulated_depreciation_account').prop('required', true);
        } else {
            $('#depreciation_rate_per_year, #depreciation_period_months, #depreciation_account, #accumulated_depreciation_account').prop('required', false);
            // Clear depreciation fields when unchecked
            $('#depreciation_rate_per_year, #depreciation_period_months').val('');
            $('#depreciation_account, #accumulated_depreciation_account').val('').trigger('change');
            $('#total_depreciation, #book_value').val('');
        }
    });
    

    // Calculate depreciation when price or rate changes
    function calculateDepreciation() {
        const price = parseFloat($('#price').val().replace(/,/g, '')) || 0;
        const rate = parseFloat($('#depreciation_rate_per_year').val()) || 0;

        const totalDepreciation = price * (rate / 100);
        const bookValue = price - totalDepreciation;

        // Format and display
        $('#total_depreciation').val(totalDepreciation.toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#book_value').val(bookValue.toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    }

    $('#price, #depreciation_rate_per_year').on('input', function() {
        if ($('#is_depreciated').is(':checked')) {
            calculateDepreciation();
        }
    });

    // Form submission
    $('#inventory-purchase-form').on('submit', function(e) {
        e.preventDefault();

        // Show confirmation
        Swal.fire({
            title: 'Buat Transaksi Pembelian Aset?',
            text: 'Pastikan data yang diisi sudah benar.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Buat!',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-label-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                submitForm();
            }
        });
    });

    function submitForm() {
        // Show loading state
        $('#submit-btn').prop('disabled', true).html(
            '<span class="spinner-border spinner-border-sm me-2"></span>Loading...'
        );

        // Collect form data
        const formData = new FormData($('#inventory-purchase-form')[0]);

        // AJAX submission
        $.ajax({
            url: '{{ route("master_data.inventory.save") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Transaksi pembelian aset berhasil dibuat.',
                    icon: 'success',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                }).then(() => {
                    window.location.href = '{{ route("master_data.inventory.index") }}';
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
                $('#submit-btn').prop('disabled', false).html('Simpan');
            }
        });
    }
});
</script>
@stop
