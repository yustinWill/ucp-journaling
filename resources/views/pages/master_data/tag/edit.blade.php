@extends('layouts.default')
@section('page_title')
    Edit Tag
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Edit Tag</h4>
        <form class="card-body" id="tagEditForm">
            <input type="hidden" id="tag_id" value="{{ $tag->id }}">
            <!-- Info Tag Section -->
            <div class="section-header mt-12">
                <h6>Informasi Tag</h6>
                <hr class="mx-n6">
            </div>

            <!-- Row 1: Code and Name -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Kode Tag <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="code" value="{{ $tag->code }}" maxlength="50"
                        style="text-transform: uppercase;" disabled />
                    <small class="form-text text-muted">Kode unik untuk tag, akan otomatis diubah menjadi huruf
                        besar</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama Tag <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="name" value="{{ $tag->name }}" maxlength="100" />
                    <small class="form-text text-muted">Nama tag yang akan ditampilkan</small>
                </div>
            </div>

            <!-- Row 2: Color and Status -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Warna Badge <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="color-select" data-allow-clear="false">
                        <option value="">Pilih Warna</option>
                        @foreach ($availableColors as $color)
                            <option value="{{ $color->id }}" data-preview="{{ $color->preview }}"
                                {{ $color->id === $tag->tag_id ? 'selected' : '' }}>{{ $color->name }}</option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">Warna badge yang akan ditampilkan di sistem</small>
                    <!-- Color Preview -->
                    <div class="mt-2" id="color-preview">
                        <small class="form-text text-muted">Preview: </small>
                        <span class="badge {{ $tag->tag_preview }}" id="preview-badge">{{ $tag->code }}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select class="global-select2 form-select" id="status-select" data-allow-clear="false">
                        <option value="ACTIVE" {{ $tag->status === 'ACTIVE' ? 'selected' : '' }}>ACTIVE - Aktif</option>
                        <option value="INACTIVE" {{ $tag->status === 'INACTIVE' ? 'selected' : '' }}>INACTIVE - Tidak
                            Aktif</option>
                    </select>
                </div>
            </div>

            <!-- Row 3: Description and Memo -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Deskripsi</label>
                    <textarea class="form-control" id="description" rows="3" maxlength="500">{{ $tag->description }}</textarea>
                    <small class="form-text text-muted">Maksimal 500 karakter - Deskripsi optional untuk menjelaskan
                        penggunaan tag</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Memo</label>
                    <textarea class="form-control" id="memo" rows="3" maxlength="500">{{ $tag->memo ?? '' }}</textarea>
                    <small class="form-text text-muted">Memo internal untuk catatan penggunaan tag</small>
                </div>
            </div>

            <!-- Row 4: Usage Count -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Jumlah Penggunaan</label>
                    <input type="text" class="form-control text-center" value="{{ $tag->usage_count }}" disabled />
                    <small class="form-text text-muted">Total transaksi yang menggunakan tag ini</small>
                    @if ($tag->usage_count > 0)
                        <div class="alert alert-info mt-2 p-2">
                            <small><i class="icon-base ti tabler-info-circle me-1"></i>Tag ini sedang digunakan di
                                {{ $tag->usage_count }} transaksi</small>
                        </div>
                    @endif
                </div>
                <div class="col-md-6">
                    <!-- Empty column for alignment -->
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 text-end">
                <button type="button" class="btn btn-label-danger me-3" id="cancel-btn">Batalkan</button>
                <button type="button" class="btn btn-success" id="submit-btn">Simpan Perubahan</button>
            </div>
        </form>
    </div>
@endsection

@section('js_script')
    <script>
        $(function() {
            // Convert code to uppercase on input
            $('#code').on('input', function() {
                this.value = this.value.toUpperCase();
            });

            // Color preview functionality
            $('#color-select').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const previewClass = selectedOption.data('preview');

                if (previewClass) {
                    $('#color-preview').show();
                    $('#preview-badge').removeClass().addClass('badge ' + previewClass).text($('#code')
                        .val() || 'SAMPLE');
                } else {
                    $('#color-preview').hide();
                }
            });

            // Update preview badge text when code changes
            $('#code').on('input', function() {
                const codeValue = $(this).val() || 'SAMPLE';
                $('#preview-badge').text(codeValue);
            });

            // Form submission
            $('#submit-btn').click(function(e) {
                e.preventDefault();

                // Validate required fields
                let isValid = true;
                let errorMessage = '';

                if (!$('#code').val().trim()) {
                    isValid = false;
                    errorMessage = 'Kode tag harus diisi.';
                } else if (!$('#name').val().trim()) {
                    isValid = false;
                    errorMessage = 'Nama tag harus diisi.';
                } else if (!$('#color-select').val()) {
                    isValid = false;
                    errorMessage = 'Warna badge harus dipilih.';
                }

                if (!isValid) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Kesalahan Validasi',
                        text: errorMessage,
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    return;
                }

                Swal.fire({
                    title: 'Perbarui Tag?',
                    text: 'Cek kembali data yang sudah diubah!',
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
                        $('#submit-btn').prop('disabled', true).html(
                            '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Loading...'
                        );
                        $.ajax({
                            url: "{{ route('master_data.tag.update') }}",
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                id: $('#tag_id').val(),
                                code: $('#code').val(),
                                name: $('#name').val(),
                                f_tag_color_id: $('#color-select').val(),
                                status: $('#status-select').val(),
                                description: $('#description').val(),
                                memo: $('#memo').val()
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Tag berhasil diperbarui.',
                                    customClass: {
                                        confirmButton: 'btn btn-success'
                                    },
                                    buttonsStyling: false
                                }).then(function() {
                                    window.location.href =
                                        "{{ route('master_data.tag.detail', ['id' => $tag->id]) }}"
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
                });
            });

            // Cancel button
            $('#cancel-btn').click(function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Batalkan Perubahan?',
                    text: 'Perubahan yang sudah dibuat akan hilang!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Batalkan!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-danger',
                        cancelButton: 'btn btn-label-secondary'
                    },
                    buttonsStyling: false
                }).then(function(result) {
                    if (result.value) {
                        window.location.href =
                            "{{ route('master_data.tag.detail', ['id' => $tag->id]) }}";
                    }
                });
            });
        });
    </script>
@endsection
