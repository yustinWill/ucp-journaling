@extends('layouts.default')
@section('page_title')
    Tambah Kategori Produk
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Tambah Kategori Produk</h4>
        <form class="card-body" id="category-form" action="#" method="POST" autocomplete="off">
                @csrf
                
                <!-- Info Kategori Section -->
                <div class="section-header mt-12">
                    <h6>Info Kategori</h6>
                    <hr class="mx-n6">
                </div>
                
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Kode Kategori <span style="color: red;">*</span></label>
                        <input type="text" class="form-control" id="category-code" name="category_code" placeholder="FINISHED_GOODS" maxlength="50" required />
                        <small class="form-text text-muted">Kode unik untuk identifikasi kategori</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nama Kategori <span style="color: red;">*</span></label>
                        <input type="text" class="form-control" id="category-name" name="category_name" placeholder="Produk Jadi" maxlength="100" required />
                    </div>
                </div>
                
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Tipe Kategori <span style="color: red;">*</span></label>
                        <select class="global-select2 form-select" id="category-type" name="category_type" data-allow-clear="false" required>
                            <option value="">Pilih Tipe Kategori</option>
                            @foreach($categoryTypes as $type)
                                <option value="{{ $type['value'] }}">{{ $type['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status <span style="color: red;">*</span></label>
                        <select class="global-select2 form-select" id="status" name="status" data-allow-clear="false" required>
                            <option value="">Pilih Status</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status['value'] }}">{{ $status['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Deskripsi kategori (opsional)"></textarea>
                        <small class="form-text text-muted">Penjelasan mengenai kategori ini</small>
                    </div>
                    <div class="col-md-6">
                        <!-- Empty for alignment -->
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="pt-6 text-end">
                    <a href="{{ route('master_data.category.index') }}" class="btn btn-label-danger me-3">Batalkan</a>
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
                title: 'Simpan Kategori?',
                text: 'Apakah Anda yakin ingin membuat kategori ini?',
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
                        url: "{{ route('master_data.category.save') }}",
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            code: $('#category-code').val(),
                            name: $('#category-name').val(),
                            type: $('#category-type').val(),
                            status: $('#status').val(),
                            description: $('#description').val()
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Kategori berhasil dibuat.',
                                customClass: {
                                    confirmButton: 'btn btn-success'
                                },
                                buttonsStyling: false
                            }).then(function() {
                                window.location.href = "{{ route('master_data.category.index') }}";
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

        // Auto-generate category name based on type selection
        $('#category-type').on('change', function() {
            const selectedOption = $(this).find(':selected');
            if (selectedOption.length && selectedOption.val()) {
                const typeText = selectedOption.text().split(' - ')[1]; // Get the description part
                if (typeText && !$('#category-name').val()) {
                    $('#category-name').val(typeText);
                }
            }
        });

        // Auto-generate category code from name
        $('#category-name').on('input', function() {
            const name = $(this).val().toUpperCase();
            let code = '';
            
            // Common category mappings for RIYO Water business
            if (name.includes('PRODUK JADI') || name.includes('FINISHED')) {
                code = 'FINISHED_GOODS';
            } else if (name.includes('BAHAN BAKU') || name.includes('RAW')) {
                code = 'RAW_MATERIALS';
            } else if (name.includes('KEMASAN') || name.includes('PACKAGING')) {
                code = 'PACKAGING';
            } else if (name.includes('PERAWATAN') || name.includes('MAINTENANCE')) {
                code = 'MAINTENANCE';
            } else {
                // Generate from name
                code = name.replace(/\s+/g, '_').replace(/[^A-Z0-9_]/g, '');
            }
            
            $('#category-code').val(code);
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