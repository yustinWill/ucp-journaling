@extends('layouts.default')
@section('page_title')
    Edit Kontak - {{ $contact['contact_name'] }}
@stop
@section('content')
    <div class="card mb-6">
        <h5 class="card-header">Edit Kontak</h5>
        <form class="card-body" id="contactEditForm">
            <!-- Section 1: Info Kontak -->
            <div class="section-header mt-12">
                <h5>Info Kontak</h5>
                <hr>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <label class="form-label">Nama Kontak <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="contact-name" value="{{ $contact['contact_name'] }}" placeholder="Masukkan Nama Kontak" />
                </div>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <label class="form-label">Tipe Kontak <span style="color: red;">*</span></label>
                    <div class="mt-2">
                        <div class="form-check form-check-inline">
                            <input name="contact_type" class="form-check-input" type="radio" value="pelanggan" id="typePelanggan" @if($contact['type'] == 'Pelanggan') checked @endif>
                            <label class="form-check-label" for="typePelanggan">Pelanggan</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input name="contact_type" class="form-check-input" type="radio" value="supplier" id="typeSupplier" @if($contact['type'] == 'Supplier') checked @endif>
                            <label class="form-check-label" for="typeSupplier">Supplier</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input name="contact_type" class="form-check-input" type="radio" value="karyawan" id="typeKaryawan" @if($contact['type'] == 'Karyawan') checked @endif>
                            <label class="form-check-label" for="typeKaryawan">Karyawan</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input name="contact_type" class="form-check-input" type="radio" value="lainnya" id="typeLainnya" @if($contact['type'] == 'Lainnya') checked @endif>
                            <label class="form-check-label" for="typeLainnya">Lainnya</label>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <label class="form-label">Grup Kontak</label>
                    <select class="global-select2 form-select" id="contact-group" multiple data-allow-clear="true">
                        <option value="vip" @if(str_contains($contact['group'], 'VIP')) selected @endif>VIP Customer</option>
                        <option value="regular" @if(str_contains($contact['group'], 'Regular')) selected @endif>Regular Customer</option>
                        <option value="wholesale" @if(str_contains($contact['group'], 'Wholesale')) selected @endif>Wholesale</option>
                        <option value="retail" @if(str_contains($contact['group'], 'Retail')) selected @endif>Retail</option>
                    </select>
                </div>
            </div>
            
            <!-- Section 2: Info Umum -->
            <div class="section-header mt-12">
                <h5>Info Umum</h5>
                <hr>
            </div>
            
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label">Nama Perusahaan <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="company-name-general" value="{{ $contact['company_name'] }}" placeholder="Masukkan Nama Perusahaan" />
                </div>
                <div class="col-md-4">
                    <label class="form-label">Nomor Handphone</label>
                    <input type="text" class="form-control" id="mobile-phone" value="{{ $contact['phone'] }}" placeholder="081234567890" />
                </div>
                <div class="col-md-4">
                    <label class="form-label">Nomor Telepon</label>
                    <input type="text" class="form-control" id="landline-phone" value="{{ $contact['landline'] }}" placeholder="021-12345678" />
                </div>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Identitas <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="identity-type" value="{{ $contact['identity_type'] }}" placeholder="KTP" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" value="{{ $contact['email'] }}" placeholder="email@example.com" />
                </div>
            </div>
            
            <!-- Section 3: Info Perusahaan -->
            <div class="section-header mt-12">
                <h5>Info Perusahaan</h5>
                <hr>
            </div>
            
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Perusahaan <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="company-name" value="{{ $contact['company_name'] }}" placeholder="Masukkan Nama Perusahaan" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nomor Identitas</label>
                    <input type="text" class="form-control" id="identity-number" value="{{ $contact['identity_number'] }}" placeholder="3175091203940000" />
                </div>
            </div>
            
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Fax <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="fax" value="{{ $contact['fax'] }}" placeholder="021-12345679" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">NPWP <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="npwp" value="{{ $contact['npwp'] }}" placeholder="01.234.567.8-999.000" />
                </div>
            </div>
            
            <div class="row g-3 mb-3">
                <div class="col-md-12">
                    <label class="form-label">Alamat Penagihan <span style="color: red;">*</span></label>
                    <textarea class="form-control" id="billing-address" rows="3" placeholder="Masukkan alamat penagihan">{{ $contact['billing_address'] }}</textarea>
                </div>
            </div>
            
            <div class="row g-3 mb-3">
                <div class="col-md-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="sync-company-address">
                        <label class="form-check-label" for="sync-company-address">
                            Samakan dengan Alamat Perusahaan
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="row g-3 mb-3">
                <div class="col-md-2">
                    <label class="form-label">Provinsi</label>
                    <input type="text" class="form-control" id="province" placeholder="Jakarta" />
                </div>
                <div class="col-md-2">
                    <label class="form-label">Kota</label>
                    <input type="text" class="form-control" id="city" placeholder="Jakarta Pusat" />
                </div>
                <div class="col-md-2">
                    <label class="form-label">Kecamatan</label>
                    <input type="text" class="form-control" id="district" placeholder="Sudirman" />
                </div>
                <div class="col-md-2">
                    <label class="form-label">Kelurahan</label>
                    <input type="text" class="form-control" id="sub-district" placeholder="Karet" />
                </div>
                <div class="col-md-2">
                    <label class="form-label">Kode Pos</label>
                    <input type="text" class="form-control" id="postal-code" placeholder="10220" />
                </div>
                <div class="col-md-2">
                    <label class="form-label">RT/RW</label>
                    <input type="text" class="form-control" id="rt-rw" placeholder="001/001" />
                </div>
            </div>
            
            <div class="row g-3 mb-4" id="shipping-address-section">
                <div class="col-md-12">
                    <label class="form-label">Alamat Pengiriman</label>
                    <textarea class="form-control" id="shipping-address" rows="3" placeholder="Masukkan alamat pengiriman">{{ $contact['shipping_address'] }}</textarea>
                </div>
            </div>
            
            <!-- Section 4: Info Bank -->
            <div class="section-header mt-12">
                <h5>Info Bank</h5>
                <hr>
            </div>
            
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Bank <span style="color: red;">*</span></label>
                    <select class="form-select" id="bank-name">
                        <option value="">Pilih Bank</option>
                        <option value="BCA" @if($contact['bank_name'] == 'Bank Central Asia') selected @endif>Bank Central Asia (BCA)</option>
                        <option value="BRI" @if($contact['bank_name'] == 'BRI') selected @endif>Bank Rakyat Indonesia (BRI)</option>
                        <option value="MANDIRI" @if($contact['bank_name'] == 'Mandiri') selected @endif>Bank Mandiri</option>
                        <option value="BNI" @if($contact['bank_name'] == 'BNI') selected @endif>Bank Negara Indonesia (BNI)</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kantor Cabang</label>
                    <input type="text" class="form-control" id="bank-branch" value="{{ $contact['bank_branch'] }}" placeholder="Bogor" />
                </div>
            </div>
            
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Pemegang Akun Bank <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="account-holder" value="{{ $contact['account_holder'] }}" placeholder="Nama sesuai rekening" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nomor Rekening <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="account-number" value="{{ $contact['account_number'] }}" placeholder="541-002-7891" />
                </div>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <button type="button" class="btn btn-outline-primary" id="add-bank-account">
                        <i class="icon-base ti tabler-plus me-1"></i>
                        Tambah Akun Bank Lain
                    </button>
                </div>
            </div>
            
            <!-- Section 5: Pemetaan Akun -->
            <div class="section-header mt-12">
                <h5>Pemetaan Akun</h5>
                <hr>
            </div>
            
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Akun Hutang <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="payable-account" data-allow-clear="true">
                        <option value="">Pilih Akun Hutang</option>
                        <option value="2-20100" @if($contact['debt_account'] == '(2-20100) Hutang') selected @endif>(2-20100) Hutang</option>
                        <option value="2-20200">(2-20200) Hutang Jangka Panjang</option>
                        <option value="2-20300">(2-20300) Hutang Bank</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Akun Piutang <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="receivable-account" data-allow-clear="true">
                        <option value="">Pilih Akun Piutang</option>
                        <option value="1-20100" @if($contact['receivable_account'] == '(1-20100) Piutang') selected @endif>(1-20100) Piutang</option>
                        <option value="1-20200">(1-20200) Piutang Jangka Panjang</option>
                        <option value="1-20300">(1-20300) Piutang Lain-lain</option>
                    </select>
                </div>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Syarat Pembayaran <span style="color: red;">*</span></label>
                    <select class="form-select" id="payment-terms">
                        <option value="">Pilih Syarat Pembayaran</option>
                        <option value="Net 15" @if($contact['payment_terms'] == 'Net 15') selected @endif>Net 15</option>
                        <option value="Net 30" @if($contact['payment_terms'] == 'Net 30') selected @endif>Net 30</option>
                        <option value="Net 45" @if($contact['payment_terms'] == 'Net 45') selected @endif>Net 45</option>
                        <option value="COD" @if($contact['payment_terms'] == 'COD') selected @endif>Cash on Delivery</option>
                        <option value="Immediate" @if($contact['payment_terms'] == 'Immediate') selected @endif>Immediate</option>
                    </select>
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
    
    .form-check-inline {
        margin-right: 1rem;
    }
    
    .contact-type-fields {
        display: none;
    }
    
    .contact-type-fields.show {
        display: block;
    }
    
    .is-invalid {
        border-color: #dc3545;
    }
    
    .form-text.text-muted {
        font-size: 0.875rem;
    }
</style>
@endsection

@section('js_script')
    <script>
        $(function() {
            // Initialize form state
            updateFieldVisibility();
            
            // Contact type change handler
            $('input[name="contact_type"]').change(function() {
                updateFieldVisibility();
            });
            
            // Sync company address checkbox
            $('#sync-company-address').change(function() {
                if ($(this).is(':checked')) {
                    const billingAddress = $('#billing-address').val();
                    $('#shipping-address').val(billingAddress);
                    $('#shipping-address').prop('readonly', true);
                } else {
                    $('#shipping-address').prop('readonly', false);
                }
            });
            
            // Add bank account functionality
            $('#add-bank-account').click(function() {
                // TODO: Add additional bank account section
                alert('Functionality to add additional bank accounts will be implemented');
            });
            
            // Update field visibility based on contact type
            function updateFieldVisibility() {
                const contactType = $('input[name="contact_type"]:checked').val();
                
                // Hide all conditional fields first
                $('#shipping-address-section').hide();
                $('#npwp').closest('.col-md-6').hide();
                
                // Show fields based on contact type
                if (contactType === 'pelanggan') {
                    $('#shipping-address-section').show();
                    $('#npwp').closest('.col-md-6').show();
                } else if (contactType === 'supplier') {
                    $('#npwp').closest('.col-md-6').show();
                }
            }
            
            // Cancel button handler
            $('#cancel-btn').click(function(e) {
                e.preventDefault();
                
                Swal.fire({
                    title: 'Batalkan Perubahan?',
                    text: "Semua perubahan yang belum disimpan akan hilang!",
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
                        // TODO: Navigate back to contact list or detail
                        window.history.back();
                    }
                });
            });
            
            // Submit button handler
            $('#submit-btn').click(function(e) {
                e.preventDefault();
                
                // Validate required fields
                let isValid = true;
                const requiredFields = ['contact-name', 'company-name-general', 'identity-type', 'company-name', 'fax', 'npwp', 'billing-address', 'bank-name', 'account-holder', 'account-number', 'payable-account', 'receivable-account', 'payment-terms'];
                
                requiredFields.forEach(function(fieldId) {
                    const field = $('#' + fieldId);
                    if (field.is(':visible') && (!field.val() || field.val().trim() === '')) {
                        field.addClass('is-invalid');
                        isValid = false;
                    } else {
                        field.removeClass('is-invalid');
                    }
                });
                
                // Validate contact type selection
                if (!$('input[name="contact_type"]:checked').length) {
                    isValid = false;
                    alert('Silakan pilih tipe kontak');
                }
                
                // Validate email format if provided
                const email = $('#email').val();
                if (email && !isValidEmail(email)) {
                    $('#email').addClass('is-invalid');
                    isValid = false;
                }
                
                if (!isValid) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: 'Mohon lengkapi semua field yang wajib diisi dengan benar.',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    return;
                }
                
                Swal.fire({
                    title: 'Simpan Perubahan?',
                    text: "Data kontak akan diperbarui!",
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
                            text: 'Data kontak berhasil diperbarui.',
                            customClass: {
                                confirmButton: 'btn btn-success'
                            },
                            buttonsStyling: false
                        }).then(function() {
                            // TODO: Navigate to contact detail or list
                            console.log('Contact updated successfully');
                        });
                    }
                });
            });
            
            // Email validation helper
            function isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }
            
            // Phone number formatting (Indonesian format)
            $('#mobile-phone, #landline-phone').on('input', function() {
                // TODO: Implement Indonesian phone number formatting
            });
            
            // NPWP formatting
            $('#npwp').on('input', function() {
                let value = $(this).val().replace(/\D/g, '');
                if (value.length >= 15) {
                    value = value.substring(0, 15);
                    value = value.replace(/(\d{2})(\d{3})(\d{3})(\d{1})(\d{3})(\d{3})/, '$1.$2.$3.$4-$5.$6');
                }
                $(this).val(value);
            });
        });
    </script>
@endsection