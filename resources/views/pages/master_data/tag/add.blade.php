@extends('layouts.default')
@section('page_title')
    Tambah Tag
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Tambah Tag</h4>
        <form class="card-body" id="tagAddForm">
            <!-- Info Tag Section -->
            <div class="section-header mt-12">
                <h6>Informasi Tag</h6>
                <hr class="mx-n6">
            </div>
            
            <!-- Row 1: Code and Name -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Kode Tag <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="code" placeholder="Contoh: PENJUALAN" maxlength="50" style="text-transform: uppercase;" />
                    <small class="form-text text-muted">Kode unik untuk tag, akan otomatis diubah menjadi huruf besar</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama Tag <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="name" placeholder="Contoh: Transaksi Penjualan" maxlength="100" />
                    <small class="form-text text-muted">Nama tag yang akan ditampilkan</small>
                </div>
            </div>
            
            <!-- Row 2: Color and Status -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Warna Badge <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="color-select" data-allow-clear="false">
                        <option value="">Pilih Warna</option>
                        @foreach($availableColors as $color)
                            <option value="{{ $color['value'] }}" data-preview="{{ $color['preview'] }}">{{ $color['label'] }}</option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">Warna badge yang akan ditampilkan di sistem</small>
                    <!-- Color Preview -->
                    <div class="mt-2" id="color-preview" style="display: none;">
                        <small class="form-text text-muted">Preview: </small>
                        <span class="badge" id="preview-badge">SAMPLE</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select class="global-select2 form-select" id="status-select" data-allow-clear="false">
                        <option value="ACTIVE" selected>ACTIVE - Aktif</option>
                        <option value="INACTIVE">INACTIVE - Tidak Aktif</option>
                    </select>
                </div>
            </div>
            
            <!-- Row 3: Description and Memo -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Deskripsi</label>
                    <textarea class="form-control" id="description" rows="3" placeholder="Deskripsi penggunaan tag ini..." maxlength="500"></textarea>
                    <small class="form-text text-muted">Maksimal 500 karakter - Deskripsi optional untuk menjelaskan penggunaan tag</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Memo</label>
                    <textarea class="form-control" id="memo" rows="3" placeholder="Memo internal..." maxlength="500"></textarea>
                    <small class="form-text text-muted">Memo internal untuk catatan penggunaan tag</small>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="pt-6 text-end">
                <button type="button" class="btn btn-label-danger me-3" id="cancel-btn">Batalkan</button>
                <button type="button" class="btn btn-success" id="submit-btn">Simpan Data</button>
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
                $('#preview-badge').removeClass().addClass('badge ' + previewClass).text($('#code').val() || 'SAMPLE');
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
                title: 'Simpan Tag?',
                text: 'Cek kembali data yang sudah diisi!',
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
                        text: 'Tag berhasil disimpan.',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        },
                        buttonsStyling: false
                    }).then(function() {
                        window.location.href = "{{ route('master_data.tag.index') }}";
                    });
                }
            });
        });
        
        // Cancel button
        $('#cancel-btn').click(function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: 'Batalkan Perubahan?',
                text: 'Data yang sudah diisi akan hilang!',
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
                    window.location.href = "{{ route('master_data.tag.index') }}";
                }
            });
        });
    });
</script>
@endsection