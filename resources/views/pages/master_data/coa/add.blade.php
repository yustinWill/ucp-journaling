@extends('layouts.default')
@section('page_title')
    Tambah COA
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Tambah COA Baru</h4>
        <form class="card-body form-repeater">
            <!-- Informasi COA Section -->
            <div class="section-header mt-12">
                <h6>Informasi COA</h6>
                <hr class="mx-n6">
            </div>
            <div class="row g-6 mb-4">
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
                    <select class="global-select2 form-select" data-allow-clear="false">
                        <option value="">Pilih Jenis COA</option>
                        <option value="HEADER">Header</option>
                        <option value="DETAIL">Detail</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Note COA</label>
                    <input type="text" class="form-control" placeholder="Masukkan Note COA" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select class="global-select2 form-select" data-allow-clear="false">
                        <option value="ACTIVE" selected>ACTIVE - Aktif</option>
                        <option value="INACTIVE">INACTIVE - Tidak Aktif</option>
                    </select>
                </div>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tag</label>
                    <div class="row g-2">
                        <div class="col">
                            <select class="global-select2 form-select" id="coa-tags" multiple data-allow-clear="false">
                                @foreach($tags as $tag)
                                    <option value="{{ $tag['value'] }}" data-color="{{ $tag['color'] }}">{{ $tag['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if($canAddTags)
                            <div class="col-auto">
                                <a href="{{ route('master_data.tag.add') }}" target="_blank" class="btn btn-outline-primary d-flex align-items-center" id="add-new-coa-tag" title="Tambah Tag Baru">
                                    <i class="icon-base ti tabler-plus"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                    <small class="form-text text-muted">Pilih satu atau lebih tag untuk kategorisasi akun</small>
                </div>
                <div class="col-md-6">
                    <!-- Empty column for alignment -->
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
                    title: 'Simpan COA?',
                    text: 'Apakah Anda yakin ingin membuat COA ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-secondary'
                    },
                    buttonsStyling: false
                }).then(function(result) {
                    if (result.isConfirmed) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'COA berhasil dibuat.',
                            customClass: {
                                confirmButton: 'btn btn-success'
                            },
                            buttonsStyling: false
                        });
                    }


                })
            });
        });
    </script>
@endsection
