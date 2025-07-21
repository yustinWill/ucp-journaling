@extends('layouts.default')
@section('page_title')
    Tambah Gudang
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Tambah Gudang</h4>
        <form class="card-body form-repeater">
            <h6>1. Data Gudang</h6>
            <div class="row g-6">
                <div class="col-md-6">
                    <label class="form-label">Kode Gudang</label>
                    <input type="text" class="form-control" placeholder="Masukkan Kode Gudang" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama Gudang</label>
                    <input type="text" class="form-control" placeholder="Masukkan Nama Gudang" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Penanggung Jawab</label>
                    <select class="global-select2 form-select" data-allow-clear="true" multiple>
                        @foreach($picUsers as $user)
                            <option value="{{ $user['value'] }}">{{ $user['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status Gudang</label>
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
            <h6>2. Alamat Gudang</h6>
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
