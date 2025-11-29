@extends('layouts.default')
@section('page_title')
    Tambah Kontak
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Tambah Kontak</h4>
        <div class="card-body">
            <h6>1. Data Kontak</h6>
            <div class="row g-6">
                <div class="col-md-6">
                    <label class="form-label">Nama Kontak <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="contact_name" placeholder="Masukkan Nama Kontak"
                        maxlength="255" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Group Kontak</label>
                    <input type="text" class="form-control" id="group_name" placeholder="Masukkan Nama Group"
                        maxlength="255" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tipe Kontak</label>
                    <div class="col mt-2">
                        <div class="form-check form-check-inline">
                            <input name="is_customer" class="form-check-input contact-type-checkbox" type="checkbox"
                                value="1" id="typeCustomer">
                            <label class="form-check-label" for="typeCustomer">Customer</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input name="is_supplier" class="form-check-input contact-type-checkbox" type="checkbox"
                                value="1" id="typeSupplier">
                            <label class="form-check-label" for="typeSupplier">Supplier</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input name="is_employee" class="form-check-input contact-type-checkbox" type="checkbox"
                                value="1" id="typeEmployee">
                            <label class="form-check-label" for="typeEmployee">Karyawan</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input name="is_other" class="form-check-input contact-type-checkbox" type="checkbox"
                                value="1" id="typeOther">
                            <label class="form-check-label" for="typeOther">Lainnya</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status Kontak <span style="color: red;">*</span></label>
                    <div class="demo-inline-spacing">
                        <label class="switch switch-primary">
                            <input type="checkbox" id="status_contact" class="switch-input" checked>
                            <span class="switch-toggle-slider">
                                <span class="switch-on">
                                    <i class="icon-base ti tabler-check"></i>
                                </span>
                                <span class="switch-off">
                                    <i class="icon-base ti tabler-x"></i>
                                </span>
                            </span>
                            <span class="switch-label">Aktif</span>
                        </label>
                    </div>
                </div>
            </div>

            <hr class="my-6 mx-n6" />
            <h6>2. Data Umum</h6>
            <div class="row g-6">
                <div class="col-md-6">
                    <label class="form-label" for="multicol-country">Jenis Identitas</label>
                    <select class="global-select2 form-select" id="identity_type" data-allow-clear="false">
                        <option value="">Pilih Jenis Identitas</option>
                        @foreach ($identity_types as $identity_type)
                            <option value="{{ $identity_type->code }}">{{ $identity_type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">No Identitas</label>
                    <input type="text" class="form-control" id="identity_number" placeholder="Masukkan No Identitas"
                        maxlength="16" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" placeholder="Masukkan Email" maxlength="64" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">No Telepon <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="phone" placeholder="Masukkan No Telepon"
                        maxlength="20" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Sales Penanggung Jawab</label>
                    <select class="global-select2 form-select" id="sales_user" data-allow-clear="false">
                        <option value="">Pilih Sales</option>
                        @foreach ($sales_users as $sales_user)
                            <option value="{{ $sales_user->id }}">{{ $sales_user->code }} - {{ $sales_user->employee_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <hr class="my-6 mx-n6" />
            <h6>3. Data Perusahaan</h6>
            <div class="row g-6">
                <div class="col-md-6">
                    <label class="form-label">Nama Perusahaan</label>
                    <input type="text" class="form-control" id="company_name" placeholder="Masukkan Nama Perusahaan"
                        maxlength="255" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">No NPWP</label>
                    <input type="text" class="form-control" id="npwp" placeholder="Masukkan No NPWP"
                        maxlength="16" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">No Telepon Perusahaan</label>
                    <input type="text" class="form-control" id="company_phone"
                        placeholder="Masukkan No Telepon Perusahaan" maxlength="20" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">No Fax</label>
                    <input type="text" class="form-control" id="company_fax" placeholder="Masukkan No Fax"
                        maxlength="20" />
                </div>
            </div>

            <hr class="my-6 mx-n6" />
            <h6>4. Alamat Penagihan</h6>
            <div class="row g-6">
                <div class="col-md-6">
                    <label class="form-label">Provinsi</label>
                    <input type="text" class="form-control" id="billing_province" placeholder="Masukkan Provinsi"
                        maxlength="255" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kota</label>
                    <input type="text" class="form-control" id="billing_city" placeholder="Masukkan Kota"
                        maxlength="255" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kecamatan</label>
                    <input type="text" class="form-control" id="billing_district" placeholder="Masukkan Kecamatan"
                        maxlength="255" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kelurahan</label>
                    <input type="text" class="form-control" id="billing_subdistrict" placeholder="Masukkan Kelurahan"
                        maxlength="255" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Alamat <span style="color: red;">*</span></label>
                    <textarea class="form-control" id="billing_address" placeholder="Masukkan Alamat" maxlength="255"></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kode POS</label>
                    <input type="text" class="form-control" id="billing_postal_code" placeholder="Masukkan Kode POS"
                        maxlength="5" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Area</label>
                    <input type="text" class="form-control" id="billing_area" placeholder="Masukkan Area"
                        maxlength="255" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Url Maps</label>
                    <input type="text" class="form-control" id="billing_maps_url" placeholder="Masukkan Url Maps"
                        maxlength="255" />
                </div>
            </div>

            <hr class="my-6 mx-n6" />
            <h6>5. Alamat Pengiriman</h6>
            <div class="row g-6">
                <div class="col-md-6">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="copy-from-billing-address" checked>
                        <label class="form-check-label" for="copy-from-billing-address">
                            Samakan Dengan Alamat Penagihan
                        </label>
                    </div>
                </div>
            </div>
            <div class="row g-6">
                <div class="col-md-6">
                    <label class="form-label">Provinsi</label>
                    <input type="text" class="form-control" id="shipping_province" placeholder="Masukkan Provinsi"
                        maxlength="255" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kota</label>
                    <input type="text" class="form-control" id="shipping_city" placeholder="Masukkan Kota"
                        maxlength="255" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kecamatan</label>
                    <input type="text" class="form-control" id="shipping_district" placeholder="Masukkan Kecamatan"
                        maxlength="255" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kelurahan</label>
                    <input type="text" class="form-control" id="shipping_subdistrict"
                        placeholder="Masukkan Kelurahan" maxlength="255" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Alamat <span style="color: red;">*</span></label>
                    <textarea class="form-control" id="shipping_address" placeholder="Masukkan Alamat" maxlength="255"></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kode POS</label>
                    <input type="text" class="form-control" placeholder="Masukkan Kode POS" id="shipping_postal_code"
                        maxlength="5" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Area</label>
                    <input type="text" class="form-control" id="shipping_area" placeholder="Masukkan Area"
                        maxlength="255" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Url Maps</label>
                    <input type="text" class="form-control" id="shipping_maps_url" placeholder="Masukkan Url Maps"
                        maxlength="255" />
                </div>
            </div>

            <hr class="my-6 mx-n6" />
            <h6>6. Pemetaan Akun</h6>
            <div class="row g-6">
                <div class="col-md-6">
                    <label class="form-label">Akun Hutang</label>
                    <select class="global-select2 form-select" id="payable_account" data-allow-clear="false">
                        <option value="">Pilih Akun Hutang</option>
                        @foreach ($payable_accounts as $account)
                            <option value="{{ $account->code }}">{{ $account->code }} - {{ $account->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Akun Piutang</label>
                    <select class="global-select2 form-select" id="receivable_account" data-allow-clear="false">
                        <option value="">Pilih Akun Piutang</option>
                        @foreach ($receivable_accounts as $account)
                            <option value="{{ $account->code }}">{{ $account->code }} - {{ $account->name }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- <div class="col-md-6">
                    <label class="form-label">Syarat Pembayaran <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="payment_term" data-allow-clear="false">
                        <option value="">Pilih Syarat Pembayaran</option>
                        @foreach ($payment_terms as $payment_term)
                            <option value="{{ $payment_term->code }}">{{ $payment_term->name }}</option>
                        @endforeach
                    </select>
                </div> --}}
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tag</label>
                    <div class="row g-2">
                        <div class="col">
                            <select class="global-select2 form-select" id="contact-tags" multiple
                                data-allow-clear="false">
                                @foreach ($tags as $tag)
                                    <option value="{{ $tag->id }}" data-color="{{ $tag->tag_color_code }}">
                                        {{ $tag->code }} - {{ $tag->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($canAddTags)
                            <div class="col-auto">
                                <a href="{{ route('master_data.tag.add') }}" target="_blank"
                                    class="btn btn-outline-primary d-flex align-items-center" id="add-new-contact-tag"
                                    title="Tambah Tag Baru">
                                    <i class="icon-base ti tabler-plus"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                    <small class="form-text text-muted">Pilih satu atau lebih tag untuk kategorisasi kontak</small>
                </div>
                <div class="col-md-6">
                    <!-- Empty column for alignment -->
                </div>
            </div>

            <span class="pelanggan-additional-form">
                <hr class="my-6 mx-n6" />
                <h6>7. Info Bank</h6>
                <div class="row g-3">
                    <div class="col-12">
                        <span class="form-repeater-bank">
                            <div data-repeater-list="user-acc-list">
                                <div data-repeater-item>
                                    <div class="row g-6">
                                        <div class="col-md-6">
                                            <label class="form-label">Nama Bank</label>
                                            <div class="position-relative">
                                                <select class="select2Bank form-select bank_code" data-allow-clear="true">
                                                    <option value="">Select</option>
                                                    @foreach ($banks as $bank)
                                                        <option value="{{ $bank->code }}">{{ $bank->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Kantor Cabang</label>
                                            <input type="text" class="form-control branch_office" maxlength="255" />
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Pemegang Akun Bank</label>
                                            <input type="text" class="form-control account_holder" maxlength="255" />
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">No Rekening</label>
                                            <input type="text" class="form-control account_number" maxlength="255" />
                                        </div>
                                        <div class="mb-6 col-lg-12 col-xl-2 col-12 d-flex align-items-center mb-0">
                                            <button class="btn btn-label-danger mt-xl-6" data-repeater-delete
                                                type="button">
                                                <i class="icon-base ti tabler-x me-1"></i>
                                                <span class="align-middle">Hapus</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-0">
                                <button class="btn btn-primary" data-repeater-create type="button">
                                    <i class="icon-base ti tabler-plus me-1"></i>
                                    <span class="align-middle">Tambah Akun Bank Lain</span>
                                </button>
                            </div>
                        </span>
                    </div>
                </div>
            </span>

            <span class="pelanggan-additional-form">
                <hr class="my-6 mx-n6" />
                <h6>8. Info Produk Dibeli</h6>
                <span class="form-repeater-product w-100">
                    <div data-repeater-list="group-produk">
                        <div data-repeater-item>
                            <div class="row g-6">
                                <div class="col-md-6">
                                    <label class="form-label">Nama Produk</label>
                                    <div class="position-relative">
                                        <select class="select2Product form-select" data-allow-clear="false">
                                            <option value="">Pilih Produk</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->code }}">{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Harga Satuan</label>
                                    <input type="text" class="form-control currency-input"
                                        placeholder="Enter Numeral" />
                                </div>
                                <div class="mb-6 col-lg-12 col-xl-2 col-12 d-flex align-items-center mb-0">
                                    <button class="btn btn-label-danger mt-xl-6" data-repeater-delete type="button">
                                        <i class="icon-base ti tabler-x me-1"></i>
                                        <span class="align-middle">Hapus</span>
                                    </button>
                                </div>
                            </div>
                            <hr />
                        </div>
                    </div>
                    <div class="mb-0">
                        <button class="btn btn-primary" data-repeater-create type="button">
                            <i class="icon-base ti tabler-plus me-1"></i>
                            <span class="align-middle">Tambah Produk</span>
                        </button>
                    </div>
                </span>
            </span>


            <div class="pt-6 text-end">
                <button id="submit-btn" class="btn btn-primary me-4" type="button">Simpan Data</button>
            </div>
        </div>
    </div>
@endsection

@section('js_script')
    <script>
        $(function() {
            initCleaveCurrency(document);

            $('select.select2Bank').each(function() {
                $(this).select2({
                    placeholder: "Select value",
                    allowClear: true,
                    dropdownParent: $(this).parent(),
                    width: '100%'
                });
            });

            $('.form-repeater-bank').repeater({
                show: function() {
                    $(this).slideDown(() => {
                        $(this).find('.select2-container').remove();
                        $(this).find('select.select2Bank').removeClass(
                            'select2-hidden-accessible').removeAttr(
                            'data-select2-id tabindex aria-hidden');
                        $(this).find('select.select2Bank').each(function() {
                            if ($.fn.select2 && $(this).data('select2')) {
                                alert('Destroying existing Select2 instance');
                                $(this).select2('destroy');
                            }
                            $(this).select2({
                                placeholder: "Select value",
                                allowClear: true,
                                dropdownParent: $(this).parent(),
                                width: '100%'
                            });
                        });
                    });
                },
                hide: function(remove) {
                    $(this).slideUp(remove);
                }
            });

            $('select.select2Product').each(function() {
                $(this).select2({
                    placeholder: "Select value",
                    allowClear: true,
                    dropdownParent: $(this).parent(),
                    width: '100%'
                });
            });

            $('.form-repeater-product').repeater({
                show: function() {
                    $(this).slideDown(() => {
                        $(this).find('.select2-container').remove();
                        $(this).find('select.select2Product').removeClass(
                            'select2-hidden-accessible').removeAttr(
                            'data-select2-id tabindex aria-hidden');
                        $(this).find('select.select2Product').each(function() {
                            if ($.fn.select2 && $(this).data('select2')) {
                                alert('Destroying existing Select2 instance');
                                $(this).select2('destroy');
                            }
                            $(this).select2({
                                placeholder: "Select value",
                                allowClear: true,
                                dropdownParent: $(this).parent(),
                                width: '100%'
                            });
                        });
                        initCleaveCurrency(document);
                    });
                },
                hide: function(remove) {
                    $(this).slideUp(remove);
                }
            });

            function syncShippingWithBilling() {
                if ($('#copy-from-billing-address').is(':checked')) {
                    $('#shipping_province').val($('#billing_province').val());
                    $('#shipping_city').val($('#billing_city').val());
                    $('#shipping_district').val($('#billing_district').val());
                    $('#shipping_subdistrict').val($('#billing_subdistrict').val());
                    $('#shipping_address').val($('#billing_address').val());
                    $('#shipping_postal_code').val($('#billing_postal_code').val());
                    $('#shipping_area').val($('#billing_area').val());
                    $('#shipping_maps_url').val($('#billing_maps_url').val());
                }
            }

            // When checkbox is toggled, copy or clear shipping fields
            $('#copy-from-billing-address').on('change', function() {
                if ($(this).is(':checked')) {
                    syncShippingWithBilling();
                    // Listen for changes in billing fields and sync
                    $('.billing-sync').on('input change', syncShippingWithBilling);
                } else {
                    $('#shipping_province').val('');
                    $('#shipping_city').val('');
                    $('#shipping_district').val('');
                    $('#shipping_subdistrict').val('');
                    $('#shipping_address').val('');
                    $('#shipping_postal_code').val('');
                    $('#shipping_area').val('');
                    $('#shipping_maps_url').val('');
                    // Remove listeners when unchecked
                    $('.billing-sync').off('input change', syncShippingWithBilling);
                }
            });

            // Add a class to billing fields for easy selection
            $('#billing_province, #billing_city, #billing_district, #billing_subdistrict, #billing_address, #billing_postal_code, #billing_area, #billing_maps_url')
                .addClass('billing-sync');

            // Trigger change event on page load to sync shipping address if checkbox is checked
            $('#copy-from-billing-address').trigger('change');

            // SweetAlert for submit
            $('#submit-btn').click(function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Simpan Data?',
                    text: "Cek kembali data yang sudah diisi!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan Data!',
                    customClass: {
                        confirmButton: 'btn btn-primary',
                        cancelButton: 'btn btn-label-secondary'
                    },
                    buttonsStyling: false
                }).then(function(result) {
                    if (result.value) {
                        if (result.value) {
                            $('#submit-btn').prop('disabled', true).html(
                                '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Loading...'
                            );
                            if ($('#user-status').val() == 'on') {
                                $('#user-status').val('ACTIVE');
                            } else {
                                $('#user-status').val('INACTIVE');
                            }
                            $.ajax({
                                url: "{{ route('master_data.contact.save') }}",
                                method: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    contact_name: $('#contact_name').val(),
                                    group_name: $('#group_name').val(),
                                    is_customer: $('#typeCustomer').is(':checked') ? 1 : 0,
                                    is_supplier: $('#typeSupplier').is(':checked') ? 1 : 0,
                                    is_employee: $('#typeEmployee').is(':checked') ? 1 : 0,
                                    is_other: $('#typeOther').is(':checked') ? 1 : 0,
                                    contact_status: $('#status_contact').is(
                                        ':checked') ? 'ACTIVE' : 'INACTIVE',
                                    identity_type: $('#identity_type').val(),
                                    identity_number: $('#identity_number').val(),
                                    email: $('#email').val(),
                                    phone: $('#phone').val(),
                                    sales_user: $('#sales_user').val(),
                                    company_name: $('#company_name').val(),
                                    npwp: $('#npwp').val(),
                                    company_phone: $('#company_phone').val(),
                                    company_fax: $('#company_fax').val(),
                                    billing_province: $('#billing_province').val(),
                                    billing_city: $('#billing_city').val(),
                                    billing_district: $('#billing_district').val(),
                                    billing_subdistrict: $('#billing_subdistrict')
                                        .val(),
                                    billing_address: $('#billing_address').val(),
                                    billing_postal_code: $('#billing_postal_code')
                                        .val(),
                                    billing_area: $('#billing_area').val(),
                                    billing_maps_url: $('#billing_maps_url').val(),
                                    shipping_province: $('#shipping_province').val(),
                                    shipping_city: $('#shipping_city').val(),
                                    shipping_district: $('#shipping_district').val(),
                                    shipping_subdistrict: $('#shipping_subdistrict')
                                        .val(),
                                    shipping_address: $('#shipping_address').val(),
                                    shipping_postal_code: $('#shipping_postal_code')
                                        .val(),
                                    shipping_area: $('#shipping_area').val(),
                                    shipping_maps_url: $('#shipping_maps_url').val(),
                                    payable_account: $('#payable_account').val(),
                                    receivable_account: $('#receivable_account').val(),
                                    payment_term: $('#payment_term').val(),
                                    tags: $('#contact-tags').val(),
                                    // Bank repeater
                                    bank_accounts: $(
                                            '.form-repeater-bank [data-repeater-item]')
                                        .map(function() {
                                            const bank_code = $(this).find(
                                                'select.bank_code').val();
                                            const branch_office = $(this).find(
                                                'input.branch_office').val();
                                            const account_holder = $(this).find(
                                                'input.account_holder').val();
                                            const account_number = $(this).find(
                                                'input.account_number').val();

                                            // Only return if all fields are filled
                                            if (bank_code && branch_office &&
                                                account_holder && account_number) {
                                                return {
                                                    bank_code,
                                                    branch_office,
                                                    account_holder,
                                                    account_number
                                                };
                                            }
                                        }).get(),
                                    // Product repeater
                                    products: $(
                                        '.form-repeater-product [data-repeater-item]'
                                    ).map(function() {
                                        return {
                                            product: $(this).find(
                                                    'select.select2Product')
                                                .val(),
                                            price: parseCurrency($(this).find(
                                                    'input.currency-input')
                                                .val())
                                        };
                                    }).get()
                                },
                                success: function(response) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: 'Kontak berhasil ditambahkan.',
                                        customClass: {
                                            confirmButton: 'btn btn-success'
                                        },
                                        buttonsStyling: false
                                    }).then(function() {
                                        window.location.href =
                                            "{{ route('master_data.contact.index') }}";
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
                })
            });

        });
    </script>
@endsection
