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
                    <label class="form-label">Nama Kontak</label>
                    <input type="text" class="form-control" placeholder="Masukkan Nama Kontak" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Group Kontak</label>
                    <input type="text" class="form-control" placeholder="Masukkan Nama Kontak" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tipe Kontak</label>
                    <div class="col mt-2">
                        <div class="form-check form-check-inline">
                            <input name="contact_type" class="form-check-input" type="radio" value="pelanggan"
                                id="typePelanggan" checked>
                            <label class="form-check-label" for="typePelanggan">Pelanggan</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input name="contact_type" class="form-check-input" type="radio" value="supplier"
                                id="typeSupplier">
                            <label class="form-check-label" for="typeSupplier">Supplier</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input name="contact_type" class="form-check-input" type="radio" value="karyawan"
                                id="typeKaryawan">
                            <label class="form-check-label" for="typeKaryawan">Karyawan</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input name="contact_type" class="form-check-input" type="radio" value="lainnya"
                                id="typeLainnya">
                            <label class="form-check-label" for="typeLainnya">Lainnya</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status Kontak</label>
                    <div class="demo-inline-spacing">
                        <label class="switch switch-primary">
                            <input type="checkbox" class="switch-input" checked>
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
                    <select class="global-select2 form-select" data-allow-clear="false">
                        <option value="">Pilih Jenis Identitas</option>
                        @foreach ($identity_types as $identity_type)
                            <option value="{{ $identity_type['value'] }}">{{ $identity_type['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">No Identitas</label>
                    <input type="text" class="form-control" placeholder="Masukkan No Identitas" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="text" class="form-control" placeholder="Masukkan Email" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">No Telepon</label>
                    <input type="text" class="form-control" placeholder="Masukkan No Telepon" />
                </div>
                <div class="col-md-6 pelanggan-additional-form">
                    <label class="form-label">Sales Penanggung Jawab</label>
                    <select class="global-select2 form-select" data-allow-clear="false">
                        <option value="">Pilih Sales</option>
                        @foreach ($sales_users as $sales_user)
                            <option value="{{ $sales_user['value'] }}">{{ $sales_user['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <hr class="my-6 mx-n6" />
            <h6>3. Data Perusahaan</h6>
            <div class="row g-6">
                <div class="col-md-6">
                    <label class="form-label">Nama Perusahaan</label>
                    <input type="text" class="form-control" placeholder="Masukkan Nama Perusahaan" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">No NPWP</label>
                    <input type="text" class="form-control" placeholder="Masukkan No NPWP" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">No Telepon Perusahaan</label>
                    <input type="text" class="form-control" placeholder="Masukkan No Telepon Perusahaan" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">No Fax</label>
                    <input type="text" class="form-control" placeholder="Masukkan No Fax" />
                </div>
            </div>

            <hr class="my-6 mx-n6" />
            <h6>4. Alamat Penagihan</h6>
            <div class="row g-6">
                <div class="col-md-6">
                    <label class="form-label">Provinsi</label>
                    <input type="text" class="form-control" placeholder="Masukkan Provinsi" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kota</label>
                    <input type="text" class="form-control" placeholder="Masukkan Kota" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kecamatan</label>
                    <input type="text" class="form-control" placeholder="Masukkan Kecamatan" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kelurahan</label>
                    <input type="text" class="form-control" placeholder="Masukkan Kelurahan" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Alamat</label>
                    <textarea class="form-control" placeholder="Masukkan Alamat"></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kode POS</label>
                    <input type="text" class="form-control" placeholder="Masukkan Kode POS" />
                </div>
            </div>

            <hr class="my-6 mx-n6" />
            <h6>5. Alamat Pengiriman</h6>
            <div class="row g-6">
                <div class="col-md-6">
                    <label class="form-label">Provinsi</label>
                    <input type="text" class="form-control" placeholder="Masukkan Provinsi" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kota</label>
                    <input type="text" class="form-control" placeholder="Masukkan Kota" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kecamatan</label>
                    <input type="text" class="form-control" placeholder="Masukkan Kecamatan" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kelurahan</label>
                    <input type="text" class="form-control" placeholder="Masukkan Kelurahan" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Alamat</label>
                    <textarea class="form-control" placeholder="Masukkan Alamat"></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kode POS</label>
                    <input type="text" class="form-control" placeholder="Masukkan Kode POS" />
                </div>
            </div>

            <hr class="my-6 mx-n6" />
            <h6>6. Pemetaan Akun</h6>
            <div class="row g-6">
                <div class="col-md-6">
                    <label class="form-label">Akun Hutang</label>
                    <select class="global-select2 form-select" data-allow-clear="false">
                        <option value="">Pilih Akun Hutang</option>
                        @foreach ($payable_accounts as $account)
                            <option value="{{ $account['value'] }}">{{ $account['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Akun Piutang</label>
                    <select class="global-select2 form-select" data-allow-clear="false">
                        <option value="">Pilih Akun Piutang</option>
                        @foreach ($receivable_accounts as $account)
                            <option value="{{ $account['value'] }}">{{ $account['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Syarat Pembayaran</label>
                    <select class="global-select2 form-select" data-allow-clear="false">
                        <option value="">Pilih Syarat Pembayaran</option>
                        @foreach ($payment_terms as $payment_term)
                            <option value="{{ $payment_term['value'] }}">{{ $payment_term['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tag</label>
                    <div class="row g-2">
                        <div class="col">
                            <select class="global-select2 form-select" id="contact-tags" multiple
                                data-allow-clear="false">
                                @foreach ($tags as $tag)
                                    <option value="{{ $tag['value'] }}" data-color="{{ $tag['color'] }}">
                                        {{ $tag['label'] }}</option>
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
                                            <label class="form-label">Nama Bank <span style="color: red;">*</span></label>
                                            <div class="position-relative">
                                                <select class="select2Bank form-select" name="role_code"
                                                    data-allow-clear="true">
                                                    <option value="">Select</option>
                                                    <option value="44">4</option>
                                                    <option value="45">5</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Kantor Cabang <span
                                                    style="color: red;">*</span></label>
                                            <input type="text" name="email" class="form-control" />
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Pemegang Akun Bank <span
                                                    style="color: red;">*</span></label>
                                            <input type="text" name="user_name" class="form-control" />
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">No Rekening <span
                                                    style="color: red;">*</span></label>
                                            <input type="text" name="user_name" class="form-control" />
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
                                                <option value="{{ $product['value'] }}">{{ $product['label'] }}</option>
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
                    });
                },
                hide: function(remove) {
                    $(this).slideUp(remove);
                }
            });

            // initCleaveCurrency(document);

            // SweetAlert for submit
            // $('#submit-btn').click(function(e) {
            //     e.preventDefault();
            //     Swal.fire({
            //         title: 'Simpan Data?',
            //         text: "Cek kembali data yang sudah diisi!",
            //         icon: 'warning',
            //         showCancelButton: true,
            //         confirmButtonText: 'Ya, Simpan Data!',
            //         customClass: {
            //             confirmButton: 'btn btn-primary',
            //             cancelButton: 'btn btn-label-secondary'
            //         },
            //         buttonsStyling: false
            //     }).then(function(result) {
            //         if (result.value) {
            //             Swal.fire({
            //                 icon: 'success',
            //                 title: 'Berhasil!',
            //                 text: 'Data berhasil disimpan.',
            //                 customClass: {
            //                     confirmButton: 'btn btn-primary',
            //                     cancelButton: 'btn btn-label-secondary'
            //                 },
            //             });
            //         }
            //     })
            // });

            // Show/hide pelanggan-additional-form
            function togglePelangganForm() {
                let selected = $('input[name="contact_type"]:checked').next('label').text().trim();
                if (selected === 'Pelanggan') {
                    $('.pelanggan-additional-form').show();
                } else {
                    $('.pelanggan-additional-form').hide();
                }
            }
            togglePelangganForm();
            $('input[name="contact_type"]').on('change', function() {
                togglePelangganForm();
            });

        });
    </script>
@endsection
