@extends('layouts.default')
@section('page_title')
    Buat Kredit Memo
@stop
@section('content')
    <div class="card mb-6">
        <h5 class="card-header">Buat Kredit Memo</h5>
        <form class="card-body" id="creditMemoForm">
            <!-- Informasi Transaksi Section -->
            <div class="section-header mt-12">
                <h5>Informasi Transaksi</h5>
                <hr>
            </div>
            
            <!-- Row 1: Basic Information -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label">Yang Membayar <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="payer-select" data-allow-clear="true">
                        <option value="">Pilih Kontak</option>
                        <option value="1" @if(isset($pre_filled_contact) && $pre_filled_contact['id'] == 1) selected @endif>John</option>
                        <option value="2" @if(isset($pre_filled_contact) && $pre_filled_contact['id'] == 2) selected @endif>PT. Maju Jaya</option>
                        <option value="3" @if(isset($pre_filled_contact) && $pre_filled_contact['id'] == 3) selected @endif>CV. Sukses Mandiri</option>
                        <option value="4" @if(isset($pre_filled_contact) && $pre_filled_contact['id'] == 4) selected @endif>PT. Cahaya Abadi</option>
                        <option value="5" @if(isset($pre_filled_contact) && $pre_filled_contact['id'] == 5) selected @endif>CV. Berkah Sentosa</option>
                    </select>
                    @if(isset($pre_filled_contact))
                        <small class="form-text text-muted">Pre-filled from contact: {{ $pre_filled_contact['name'] }}</small>
                    @endif
                </div>
                <div class="col-md-4">
                    <label class="form-label">Nomor Transaksi <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="transaction-number" value="CM-2025-001" placeholder="autofill but editable" />
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tag</label>
                    <input type="text" class="form-control" id="tag" placeholder="Kategori transaksi" />
                </div>
            </div>
            
            <!-- Transaksi 1 Section -->
            <div class="section-header mt-12">
                <h5>Transaksi 1</h5>
                <hr>
            </div>
            
            <!-- Row 1: Date and Transaction Type -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Tanggal Transaksi <span style="color: red;">*</span></label>
                    <input type="date" class="form-control global-single-date-picker" id="transaction-date" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jenis Transaksi <span style="color: red;">*</span></label>
                    <select class="form-select" id="transaction-type">
                        <option value="pengeluaran-credit-memo" selected>Pengeluaran Kredit Memo</option>
                        <option value="penerimaan-credit-memo">Penerimaan Kredit Memo</option>
                        <option value="adjustment">Adjustment</option>
                        <option value="return">Return</option>
                        <option value="discount">Discount</option>
                    </select>
                </div>
            </div>
            
            <!-- Row 2: Transaction Name and Account -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Transaksi <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="transaction-name" placeholder="Nama deskriptif untuk transaksi" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Pilih Akun <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="account-select" data-allow-clear="true">
                        <option value="">Pilih Akun</option>
                        <option value="1">(1-20100) Piutang</option>
                        <option value="2">(2-20100) Hutang</option>
                        <option value="3">(4-40100) Pendapatan Penjualan</option>
                        <option value="4">(5-50100) Beban Operasional</option>
                        <option value="5">(1-11100) Kas dan Bank</option>
                    </select>
                </div>
            </div>
            
            <!-- Row 3: Amount and Description -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Nilai <span style="color: red;">*</span></label>
                    <input type="text" class="form-control curr-mask" id="credit-amount" value="Rp 500,000.00" dir="rtl" placeholder="Rp 0" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Deskripsi</label>
                    <textarea class="form-control" rows="4" id="transaction-description" placeholder="Penjelasan tambahan tentang transaksi..." maxlength="500"></textarea>
                    <small class="form-text text-muted">Maksimal 500 karakter</small>
                </div>
            </div>
            
            <!-- Additional Information Section -->
            <div class="section-header mt-12">
                <h5>Informasi Tambahan</h5>
                <hr>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">Memo</label>
                        <textarea class="form-control" rows="4" id="internal-memo" placeholder="Catatan internal..." maxlength="500"></textarea>
                        <small class="form-text text-muted">Catatan untuk internal, tidak ditampilkan kepada kontak</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lampiran</label>
                        <input type="file" class="form-control" id="attachment" accept=".xlsx,.xls,.docx,.doc,.pdf,.png,.zip">
                        <small class="form-text text-muted">File dapat berupa Excel, Word, PDF, PNG, atau Zip (Max: 10MB)</small>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="pt-6 text-end">
                <button type="button" class="btn btn-label-danger me-3" id="cancel-btn">Batalkan</button>
                <button type="button" class="btn btn-success" id="submit-btn">Simpan</button>
            </div>
        </form>
    </div>
@endsection

@section('css_script')
<style>
    .section-header h5 {
        margin-bottom: 0;
        font-weight: 600;
    }
    
    .readonly-field {
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        color: #6c757d;
    }
    
    .curr-mask {
        text-align: right;
    }
    
    .form-text.text-muted {
        font-size: 0.875rem;
    }
</style>
@endsection

@section('js_script')
    <script>
        $(function() {
            // Set default transaction date to today
            $('#transaction-date').val(new Date().toISOString().split('T')[0]);
            
            // Payer selection change handler
            $('#payer-select').change(function() {
                const selectedPayer = $(this).val();
                if (selectedPayer) {
                    // TODO: Load payer details and populate related fields
                    console.log('Loading payer details for:', selectedPayer);
                }
            });
            
            // Transaction type change handler
            $('#transaction-type').change(function() {
                const transactionType = $(this).val();
                // TODO: Update account suggestions based on transaction type
                console.log('Transaction type changed to:', transactionType);
                
                // Update account selection based on type
                updateAccountSuggestions(transactionType);
            });
            
            // Account selection change handler
            $('#account-select').change(function() {
                const selectedAccount = $(this).val();
                if (selectedAccount) {
                    console.log('Account selected:', selectedAccount);
                }
            });
            
            // Amount input validation
            $('#credit-amount').on('input', function() {
                const amount = $(this).val();
                // Remove non-numeric characters except comma and period
                const cleanAmount = amount.replace(/[^\d,.-]/g, '');
                
                // Validate positive amount
                const numericAmount = parseFloat(cleanAmount.replace(/[Rp,\s]/g, ''));
                if (numericAmount < 0) {
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });
            
            // Character count for textareas
            $('#transaction-description, #internal-memo').on('input', function() {
                const maxLength = parseInt($(this).attr('maxlength'));
                const currentLength = $(this).val().length;
                const remaining = maxLength - currentLength;
                
                // Update character count display if needed
                const helpText = $(this).siblings('.form-text');
                if (remaining < 50) {
                    helpText.html(`${remaining} karakter tersisa dari ${maxLength}`);
                }
            });
            
            // File upload validation
            $('#attachment').change(function() {
                const file = this.files[0];
                if (file) {
                    // Validate file size (10MB = 10485760 bytes)
                    if (file.size > 10485760) {
                        alert('Ukuran file terlalu besar. Maksimal 10MB.');
                        $(this).val('');
                        return;
                    }
                    
                    // Validate file type
                    const allowedTypes = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                        'application/vnd.ms-excel',
                                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                        'application/msword',
                                        'application/pdf',
                                        'image/png',
                                        'application/zip'];
                    
                    if (!allowedTypes.includes(file.type)) {
                        alert('Tipe file tidak didukung. Hanya Excel, Word, PDF, PNG, atau Zip yang diperbolehkan.');
                        $(this).val('');
                        return;
                    }
                }
            });
            
            // Update account suggestions based on transaction type
            function updateAccountSuggestions(transactionType) {
                // TODO: Load appropriate accounts based on transaction type
                // For now, just log the type
                console.log('Updating account suggestions for:', transactionType);
            }
            
            // Cancel button handler
            $('#cancel-btn').click(function(e) {
                e.preventDefault();
                
                Swal.fire({
                    title: 'Batalkan Pembuatan?',
                    text: "Data yang sudah diisi akan hilang!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Batalkan!',
                    cancelButtonText: 'Tidak',
                    customClass: {
                        confirmButton: 'btn btn-danger',
                        cancelButton: 'btn btn-label-secondary'
                    },
                    buttonsStyling: false
                }).then(function(result) {
                    if (result.value) {
                        // TODO: Navigate back to credit memo list or previous page
                        window.history.back();
                    }
                });
            });
            
            // Submit button handler
            $('#submit-btn').click(function(e) {
                e.preventDefault();
                
                // Validate required fields
                let isValid = true;
                const requiredFields = ['payer-select', 'transaction-number', 'transaction-date', 'transaction-type', 'transaction-name', 'account-select', 'credit-amount'];
                
                requiredFields.forEach(function(fieldId) {
                    const field = $('#' + fieldId);
                    if (!field.val() || field.val().trim() === '') {
                        field.addClass('is-invalid');
                        isValid = false;
                    } else {
                        field.removeClass('is-invalid');
                    }
                });
                
                // Validate amount is positive
                const amountText = $('#credit-amount').val().replace(/[Rp,\s]/g, '');
                const amount = parseFloat(amountText);
                if (isNaN(amount) || amount <= 0) {
                    $('#credit-amount').addClass('is-invalid');
                    isValid = false;
                }
                
                if (!isValid) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: 'Mohon lengkapi semua field yang wajib diisi.',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    return;
                }
                
                Swal.fire({
                    title: 'Simpan Kredit Memo?',
                    text: "Cek kembali data yang sudah diisi!",
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
                        // TODO: Submit form data to server
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Kredit memo berhasil dibuat.',
                            customClass: {
                                confirmButton: 'btn btn-success'
                            },
                            buttonsStyling: false
                        }).then(function() {
                            // TODO: Navigate to credit memo list or detail page
                            console.log('Credit memo created successfully');
                        });
                    }
                });
            });
        });
        
        // Add Cleave.js script import for currency formatting
        if (!document.querySelector('script[src*="cleave"]')) {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js';
            document.head.appendChild(script);
            
            script.onload = function() {
                // Initialize Cleave.js for currency field
                new Cleave('#credit-amount', {
                    numeral: true,
                    numeralThousandsGroupStyle: 'thousand',
                    prefix: 'Rp ',
                    noImmediatePrefix: false,
                    rawValueTrimPrefix: true
                });
            };
        }
    </script>
@endsection