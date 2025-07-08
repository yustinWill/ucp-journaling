@extends('layouts.default')
@section('page_title')
    Tambah COA
@stop
@section('content')
    <div class="card mb-6">
        <h5 class="card-header">Tambah COA Baru</h5>
        <form class="card-body form-repeater">
            <h6>1. Data COA</h6>
            <div class="row g-6">
                <div class="col-md-6">
                    <label class="form-label">Kode COA</label>
                    <input type="text" class="form-control" placeholder="Masukkan Kode COA" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama COA</label>
                    <input type="text" class="form-control" placeholder="Masukkan Nama COA" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Level COA</label>
                    <input type="text" class="form-control" placeholder="Masukkan Level COA" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jenis COA</label>
                    <div class="col mt-2">
                        <div class="form-check form-check-inline">
                            <input name="contact_type" class="form-check-input" type="radio" value="" id=""
                                checked="">
                            <label class="form-check-label">Header</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input name="contact_type" class="form-check-input" type="radio" value=""
                                id="">
                            <label class="form-check-label">Detail</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Note COA</label>
                    <input type="text" class="form-control" placeholder="Masukkan Note COA" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status COA</label>
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
