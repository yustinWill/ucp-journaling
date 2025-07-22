@extends('layouts.default')
@section('page_title')
    Edit Gudang
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Edit Gudang</h4>
        <form class="card-body form-repeater">
            <h6>1. Data Gudang</h6>
            <div class="row g-6">
                <div class="col-md-6">
                    <label class="form-label">Kode Gudang</label>
                    <input type="text" class="form-control" placeholder="Masukkan Kode Gudang" value="{{ $storage['code'] ?? 'GDG-001' }}" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama Gudang</label>
                    <input type="text" class="form-control" placeholder="Masukkan Nama Gudang" value="{{ $storage['name'] ?? 'Gudang Utama Pusat RIYO Water' }}" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Penanggung Jawab</label>
                    <select class="global-select2 form-select" data-allow-clear="false" multiple>
                        @foreach($picUsers as $user)
                            <option value="{{ $user['value'] }}" 
                                @if(in_array($user['value'], $storage['pic_users'] ?? ['USR-003', 'USR-005'])) selected @endif>
                                {{ $user['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status Gudang</label>
                    <div class="demo-inline-spacing">
                        <label class="switch switch-primary">
                            <input type="checkbox" class="switch-input" @if(($storage['status'] ?? 'ACTIVE') === 'ACTIVE') checked @endif>
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
                    <input type="text" class="form-control" placeholder="Masukkan Provinsi" value="{{ $storage['province'] ?? 'Jawa Tengah' }}" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kota</label>
                    <input type="text" class="form-control" placeholder="Masukkan Kota" value="{{ $storage['city'] ?? 'Semarang' }}" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kecamatan</label>
                    <input type="text" class="form-control" placeholder="Masukkan Kecamatan" value="{{ $storage['district'] ?? 'Genuk' }}" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kelurahan</label>
                    <input type="text" class="form-control" placeholder="Masukkan Kelurahan" value="{{ $storage['village'] ?? 'Bangetayu Kulon' }}" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Alamat</label>
                    <textarea class="form-control" placeholder="Masukkan Alamat">{{ $storage['address'] ?? 'Jl. Industri Raya No. 15, Kawasan Industri Terboyo' }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kode POS</label>
                    <input type="text" class="form-control" placeholder="Masukkan Kode POS" value="{{ $storage['postal_code'] ?? '50112' }}" />
                </div>
            </div>
            <div class="pt-6 text-end">
                <button id="submit-btn" class="btn btn-primary me-4">Perbarui Data</button>
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
                    },
                    hide: function(e) {
                        confirm('Are you sure you want to delete this element?') && $(this).slideUp(e);
                    }
                });
            }

            $('#submit-btn').click(function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Perbarui Gudang?',
                    text: 'Apakah Anda yakin ingin memperbarui gudang ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Perbarui!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-secondary'
                    },
                    buttonsStyling: false
                }).then(function(result) {
                    if (result.value) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Gudang berhasil diperbarui.',
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