@extends('layouts.default')
@section('page_title')
    Tambah Syarat Pembayaran
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Tambah Syarat Pembayaran</h4>
        <form class="card-body" id="payment-term-form" autocomplete="off">
            @csrf
            
            <!-- Info Payment Term Section -->
            <div class="section-header mt-12">
                <h6>Info Syarat Pembayaran</h6>
                <hr class="mx-n6">
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Kode <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="code" name="code" 
                           placeholder="Contoh: NET_45" maxlength="20" required>
                    <small class="form-text text-muted">Kode unik untuk syarat pembayaran (max 20 karakter)</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" 
                           placeholder="Contoh: Net 45" maxlength="100" required>
                    <small class="form-text text-muted">Nama syarat pembayaran yang mudah dipahami</small>
                </div>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Jumlah Hari <span style="color: red;">*</span></label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="days" name="days" 
                               placeholder="30" min="0" max="365" required>
                        <span class="input-group-text">Hari</span>
                    </div>
                    <small class="form-text text-muted">Jumlah hari untuk pembayaran (0 untuk immediate/COD)</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tipe <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="type" name="type" data-allow-clear="false" required>
                        <option value="">Pilih Tipe</option>
                        @foreach($types as $type)
                            <option value="{{ $type['value'] }}">{{ $type['label'] }}</option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">Tipe syarat pembayaran</small>
                </div>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Status <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="status" name="status" data-allow-clear="false" required>
                        <option value="">Pilih Status</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status['value'] }}" {{ $status['value'] == 'active' ? 'selected' : '' }}>{{ $status['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <!-- Empty for alignment -->
                </div>
            </div>
            
            {{-- <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_default" name="is_default" value="1">
                        <label class="form-check-label" for="is_default">
                            Jadikan sebagai default
                        </label>
                    </div>
                    <small class="form-text text-muted">Jika dicentang, syarat pembayaran ini akan menjadi pilihan default</small>
                </div>
                <div class="col-md-6">
                    <!-- Empty for alignment -->
                </div>
            </div> --}}
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Deskripsi</label>
                    <textarea class="form-control" id="description" name="description" 
                              rows="3" placeholder="Deskripsi syarat pembayaran (opsional)"></textarea>
                    <small class="form-text text-muted">Penjelasan mengenai syarat pembayaran ini</small>
                </div>
                <div class="col-md-6">
                    <!-- Empty for alignment -->
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="pt-6 text-end">
                <button type="button" class="btn btn-label-danger me-3" id="cancel-btn">
                    <i class="icon-base ti tabler-arrow-left me-2"></i>Batal
                </button>
                @if($canWrite ?? true)
                    <button type="submit" class="btn btn-success" id="submit-btn">
                        <i class="icon-base ti tabler-device-floppy me-2"></i>Simpan
                    </button>
                @endif
            </div>
        </form>
    </div>
@stop

@section('js_script')
<script>
$(function() {
    
    // Auto-format code field to uppercase
    $('#code').on('input', function() {
        this.value = this.value.toUpperCase();
    });
    
    // Type change handler
    $('#type').on('change', function() {
        const selectedType = $(this).val();
        const daysField = $('#days');
        
        // Auto-set days based on type
        if (selectedType === 'immediate' || selectedType === 'cod') {
            daysField.val(0);
        } else if (selectedType === 'net' && daysField.val() == 0) {
            daysField.val(30); // Default net terms
        }
    });
    
    // Form validation and submission
    $('#payment-term-form').on('submit', function(e) {
        e.preventDefault();
        
        // Validate required fields
        let isValid = true;
        const requiredFields = ['code', 'name', 'days', 'type', 'status'];
        
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
            title: 'Simpan Syarat Pembayaran?',
            text: 'Apakah Anda yakin ingin membuat syarat pembayaran ini?',
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
                // Prepare form data
                const formData = {
                    _token: $('input[name="_token"]').val(),
                    code: $('#code').val(),
                    name: $('#name').val(),
                    description: $('#description').val(),
                    days: parseInt($('#days').val()) || 0,
                    type: $('#type').val(),
                    status: $('#status').val(),
                    is_default: $('#is_default').is(':checked')
                };
                
                // Submit via AJAX
                $.ajax({
                    url: '{{ route("master_data.payment_term.store") }}',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Syarat pembayaran berhasil dibuat.',
                            customClass: { confirmButton: 'btn btn-success' },
                            buttonsStyling: false
                        }).then(() => {
                            window.location.href = '{{ route("master_data.payment_term.index") }}';
                        });
                    },
                    error: function(xhr) {
                        const response = JSON.parse(xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal menyimpan',
                            text: response.message || 'Terjadi kesalahan saat menyimpan data.',
                            customClass: { confirmButton: 'btn btn-primary' },
                            buttonsStyling: false
                        });
                        
                        // Add validation classes for errors
                        if (xhr.status === 422) {
                            const errors = response.errors || {};
                            Object.keys(errors).forEach(function(field) {
                                $(`#${field}`).addClass('is-invalid');
                            });
                        }
                    }
                });
            }
        });
    });
    
    // Cancel button handler  
    $('#cancel-btn').on('click', function() {
        window.history.back();
    });
    
    // Real-time validation
    $('input, select, textarea').on('change blur', function() {
        if (this.hasAttribute('required') && !this.value.trim()) {
            this.classList.add('is-invalid');
        } else {
            this.classList.remove('is-invalid');
        }
    });
    
    // Auto-generate name from code
    $('#code').on('blur', function() {
        const code = $(this).val();
        const nameField = $('#name');
        
        if (code && !nameField.val()) {
            // Auto-generate name from code
            let generatedName = code.replace(/_/g, ' ');
            generatedName = generatedName.toLowerCase().replace(/\b\w/g, l => l.toUpperCase());
            nameField.val(generatedName);
        }
    });
});
</script>
@endsection