@extends('layouts.default')
@section('page_title')
    Tambah COA
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Tambah COA Baru</h4>
        <form class="card-body form-repeater" autocomplete="off">
            <!-- Informasi COA Section -->
            <div class="section-header mt-12">
                <h6>Informasi COA</h6>
                <hr class="mx-n6">
            </div>
            <div class="row g-6 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Kode COA <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" placeholder="Masukkan Kode COA" id="coa-code" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama COA <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" placeholder="Masukkan Nama COA" id="coa-name" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Parent COA <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="parent-coa-code" data-allow-clear="false">
                        <option value="">Pilih Parent COA</option>
                        @foreach ($coa_accounts as $account)
                            <option value="{{ $account->code }}">{{ $account->code }} - {{ $account->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jenis COA <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" data-allow-clear="false" id="coa-type">
                        <option value="">Pilih Jenis COA</option>
                        <option value="I">Induk</option>
                        <option value="D">Detail</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Note COA</label>
                    <input type="text" class="form-control" placeholder="Masukkan Note COA" id="coa-note" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="coa-status" data-allow-clear="false">
                        @foreach ($statuses as $status)
                            <option value="{{ $status['value'] }}">{{ $status['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tag</label>
                    <div class="row g-2">
                        <div class="col">
                            <select class="global-select2 form-select" id="coa-tags" multiple data-allow-clear="false">
                                @foreach ($tags as $tag)
                                    <option value="{{ $tag->id }}" data-color="{{ $tag->tag_color_code }}">
                                        {{ $tag->code }} - {{ $tag->name }}</option>
                                @endforeach
                            </select>
                        </div>
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
                    if (result.value) {
                        $('#submit-btn').prop('disabled', true).html(
                            '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Loading...'
                        );
                        $.ajax({
                            url: "{{ route('master_data.coa.save') }}",
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                code: $('#coa-code').val(),
                                name: $('#coa-name').val(),
                                parent_coa_code: $('#parent-coa-code').val(),
                                type: $('#coa-type').val(),
                                note: $('#coa-note').val(),
                                status: $('#coa-status').val(),
                                tags: $('#coa-tags').val()
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'COA berhasil dibuat.',
                                    customClass: {
                                        confirmButton: 'btn btn-success'
                                    },
                                    buttonsStyling: false
                                }).then(function() {
                                    window.location.href =
                                        "{{ route('master_data.coa.index') }}";
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
                })
            });
        });
    </script>
@endsection
