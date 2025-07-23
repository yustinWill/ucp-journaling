@extends('layouts.default')
@section('page_title')
    Edit Kategori Produk
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Edit Kategori Produk</h4>
        <form class="card-body" id="category-form" action="#" method="POST">
                <input type="hidden" id="category-id" value="{{ $category->id }}">
                @csrf
                @method('POST')
                
                <!-- Info Kategori Section -->
                <div class="section-header mt-12">
                    <h6>Info Kategori</h6>
                    <hr class="mx-n6">
                </div>
                
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Kode Kategori <span style="color: red;">*</span></label>
                        <input type="text" class="form-control" id="category-code" name="category_code" value="{{ $category->code }}" disabled maxlength="50" required />
                        <small class="form-text text-muted">Kode unik untuk identifikasi kategori</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nama Kategori <span style="color: red;">*</span></label>
                        <input type="text" class="form-control" id="category-name" name="category_name" value="{{ $category->name }}" maxlength="100" required />
                    </div>
                </div>
                
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Tipe Kategori <span style="color: red;">*</span></label>
                        <select class="global-select2 form-select" id="category-type" name="category_type" data-allow-clear="false" required>
                            <option value="">Pilih Tipe Kategori</option>
                            @foreach($categoryTypes as $type)
                                <option value="{{ $type['value'] }}" {{ $category->type == $type['value'] ? 'selected' : '' }}>{{ $type['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status <span style="color: red;">*</span></label>
                        <select class="global-select2 form-select" id="status" name="status" data-allow-clear="false" required>
                            @foreach($statuses as $status)
                                <option value="{{ $status['value'] }}" {{ $category->status == $status['value'] ? 'selected' : '' }}>{{ $status['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Deskripsi kategori (opsional)">{{ $category->description }}</textarea>
                        <small class="form-text text-muted">Penjelasan mengenai kategori ini</small>
                    </div>
                    <div class="col-md-6">
                        <!-- Empty for alignment -->
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="pt-6 text-end">
                    <a href="{{ route('master_data.category.detail', $category->id) }}" class="btn btn-label-danger me-3">Batalkan</a>
                    @if($canWrite)
                        <button type="submit" class="btn btn-success" id="submit-btn">Simpan</button>
                    @endif
                </div>
            </form>
    </div>
@stop

@section('js_script')
<script>
    $(function() {
        // Form validation and submission
        $('#category-form').on('submit', function(e) {
            e.preventDefault();
            
            // Validate required fields
            let isValid = true;
            const requiredFields = ['category-code', 'category-name', 'category-type', 'status'];
            
            requiredFields.forEach(function(fieldId) {
                const field = document.getElementById(fieldId);
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Validasi',
                    text: 'Silakan lengkapi semua field yang wajib diisi.',
                    customClass: { confirmButton: 'btn btn-primary' },
                    buttonsStyling: false
                });
                return;
            }

            // Confirm save
            Swal.fire({
                title: 'Perbarui Kategori?',
                text: 'Apakah Anda yakin ingin menyimpan perubahan ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#submit-btn').prop('disabled', true).html(
                        '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Loading...'
                    );
                    
                    $.ajax({
                        url: "{{ route('master_data.category.update') }}",
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: $('#category-id').val(),
                            name: $('#category-name').val(),
                            type: $('#category-type').val(),
                            status: $('#status').val(),
                            description: $('#description').val()
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Kategori berhasil diperbarui.',
                                customClass: {
                                    confirmButton: 'btn btn-success'
                                },
                                buttonsStyling: false
                            }).then(function() {
                                window.location.href = "{{ route('master_data.category.detail', $category->id) }}";
                            });
                        },
                        error: function(xhr) {
                            const res = xhr.responseJSON;
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal menyimpan',
                                html: res?.message || 'Terjadi kesalahan saat menyimpan data.',
                                customClass: {
                                    confirmButton: 'btn btn-primary'
                                },
                                buttonsStyling: false
                            });
                        },
                        complete: function() {
                            $('#submit-btn').prop('disabled', false).html('Simpan');
                        }
                    });
                }
            });
        });
        
        // Real-time validation
        $('input, select, textarea').on('change blur', function() {
            if (this.hasAttribute('required') && !this.value.trim()) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
    });
</script>
@endsection