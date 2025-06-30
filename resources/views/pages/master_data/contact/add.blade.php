@extends('layouts.default')
@section('page_title')
    Tambah Kontak
@stop
@section('content')
    <div class="card mb-6">
        <h5 class="card-header">Tambah Kontak Baru</h5>
        <form class="card-body form-repeater">
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
                            <input name="contact_type" class="form-check-input" type="radio" value="" id=""
                                checked="">
                            <label class="form-check-label">Pelanggan</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input name="contact_type" class="form-check-input" type="radio" value=""
                                id="">
                            <label class="form-check-label">Supplier</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input name="contact_type" class="form-check-input" type="radio" value=""
                                id="">
                            <label class="form-check-label">Karyawan</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input name="contact_type" class="form-check-input" type="radio" value=""
                                id="">
                            <label class="form-check-label">Lainnya</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Group Kontak</label>
                    <input type="text" class="form-control" placeholder="Masukkan Nama Kontak" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status Kontak</label>
                    <div class="demo-inline-spacing">
                        <label class="switch switch-primary">
                            <input type="checkbox" class="switch-input" checked="">
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
                    <select class="global-select2 form-select" data-allow-clear="true">
                        <option value="AK">KTP</option>
                        <option value="HI">SIM</option>
                        <option value="HI">Passport</option>
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
                    <select class="global-select2 form-select" data-allow-clear="true">
                        <option value="AK">1 - Asset</option>
                        <option value="HI">2 - Utang</option>
                        <option value="HI">3 - Modal</option>
                        <option value="HI">4 - Pendapatan</option>
                        <option value="HI">5 - Beban</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Akun Piutang</label>
                    <select class="global-select2 form-select" data-allow-clear="true">
                        <option value="AK">1 - Asset</option>
                        <option value="HI">2 - Utang</option>
                        <option value="HI">3 - Modal</option>
                        <option value="HI">4 - Pendapatan</option>
                        <option value="HI">5 - Beban</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Syarat Pembayaran</label>
                    <select class="global-select2 form-select" data-allow-clear="true">
                        <option value="AK">Net 30</option>
                        <option value="HI">Net 15</option>
                    </select>
                </div>
            </div>

            <hr class="my-6 mx-n6" />
            <h6>7. Info Bank</h6>
            <div class="row g-6">
                <div data-repeater-list="group-a">
                    <div data-repeater-item>
                        <div class="row g-6">
                            <div class="col-md-6">
                                <label class="form-label" for="form-repeater-1-1">Nama Bank</label>
                                <select class="bank-select2 form-select" data-allow-clear="true">
                                    <option value="AK">BCA</option>
                                    <option value="HI">Mandiri</option>
                                    <option value="HI">BRI</option>
                                    <option value="HI">BNI</option>
                                    <option value="HI">Mega</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kantor Cabang</label>
                                <input type="text" class="form-control" placeholder="Masukkan Kantor Cabang" />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pemegang Akun Bank</label>
                                <input type="text" class="form-control" placeholder="Masukkan Pemegang Akun Bank" />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nomor Rekening</label>
                                <input type="text" class="form-control" placeholder="Masukkan Nomor Rekening" />
                            </div>
                            <div class="mb-6 col-lg-12 col-xl-2 col-12 d-flex align-items-center mb-0">
                                <button class="btn btn-label-danger mt-xl-6" data-repeater-delete>
                                    <i class="icon-base ti tabler-x me-1"></i>
                                    <span class="align-middle">Hapus</span>
                                </button>
                            </div>
                        </div>
                        <hr />
                    </div>
                </div>
                <div class="mb-0">
                    <button class="btn btn-primary" data-repeater-create>
                        <i class="icon-base ti tabler-plus me-1"></i>
                        <span class="align-middle">Tambah Rekening</span>
                    </button>
                </div>
            </div>
            <div class="pt-6 text-end">
                <button id="submit-btn" class="btn btn-primary me-4">Simpan Data</button>
            </div>
        </form>
    </div>
@endsection
@section('js_script')
    <script>
        $(function() {
            const formRepeater = $('.form-repeater');
            if (formRepeater.length) {
                var row = 2;
                var col = 1;
                formRepeater.on('submit', function(e) {
                    e.preventDefault();
                });
                formRepeater.repeater({
                    show: function() {
                        $(this).slideDown();
                        //     $(this).find(".bank-select2").select2({
                        //               allowClear: true,
                        // width: '100%'
                        //     });
                    },
                    hide: function(e) {
                        confirm('Are you sure you want to delete this element?') && $(this).slideUp(e);
                    }
                });

                // Initialize select2 for existing elements on page load
                // $('.bank-select2').select2({
                //     allowClear: true,
                //     width: '100%'
                // });
            }

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
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Data berhasil disimpan.',
                            customClass: {
                                confirmButton: 'btn btn-primary',
                                cancelButton: 'btn btn-label-secondary'
                            },
                        });
                    }


                })
            });
        });
    </script>
@endsection
