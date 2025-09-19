@extends('layouts.default')
@section('page_title')
    Edit Syarat Pembayaran
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Edit Syarat Pembayaran</h4>
        <form class="card-body" id="payment-term-form" autocomplete="off">
            @csrf
            <input type="hidden" id="payment-term-id" name="id" value="{{ $paymentTerm->id }}">
            
            <!-- Info Payment Term Section -->
            <div class="section-header mt-12">
                <h6>Info Syarat Pembayaran</h6>
                <hr class="mx-n6">
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Kode <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="code" name="code" 
                           value="{{ $paymentTerm->code }}" placeholder="Contoh: NET_45" maxlength="20" required>
                    <small class="form-text text-muted">Kode unik untuk syarat pembayaran (max 20 karakter)</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" 
                           value="{{ $paymentTerm->name }}" placeholder="Contoh: Net 45" maxlength="100" required>
                    <small class="form-text text-muted">Nama syarat pembayaran yang mudah dipahami</small>
                </div>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Jumlah Hari <span style="color: red;">*</span></label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="days" name="days" 
                               value="{{ $paymentTerm->days }}" placeholder="30" min="0" max="365" required>
                        <span class="input-group-text">Hari</span>
                    </div>
                    <small class="form-text text-muted">Jumlah hari untuk pembayaran (0 untuk immediate/COD)</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tipe <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="type" name="type" data-allow-clear="false" required>
                        <option value="">Pilih Tipe</option>
                        @foreach($types as $type)
                            <option value="{{ $type['value'] }}" {{ $paymentTerm->type == $type['value'] ? 'selected' : '' }}>{{ $type['label'] }}</option>
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
                            <option value="{{ $status['value'] }}" {{ $paymentTerm->status == $status['value'] ? 'selected' : '' }}>{{ $status['label'] }}</option>
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
                        <input class="form-check-input" type="checkbox" id="is_default" name="is_default" value="1" {{ $paymentTerm->is_default ? 'checked' : '' }}>
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
                              rows="3" placeholder="Deskripsi syarat pembayaran (opsional)">{{ $paymentTerm->description }}</textarea>
                    <small class="form-text text-muted">Penjelasan mengenai syarat pembayaran ini</small>
                </div>
                <div class="col-md-6">
                    <!-- Empty for alignment -->
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="pt-6 text-end">
                <a href="{{ route('master_data.payment_term.detail', $paymentTerm->id) }}" class="btn btn-label-danger me-3">Batalkan</a>
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
    
    // Auto-format code field to uppercase
    $('#code').on('input', function() {
        this.value = this.value.toUpperCase();
    });
    
    // Type change handler
    $('#type').on('change', function() {
        const selectedType = $(this).val();
        const daysField = $('#days');
        
        // Auto-set days based on type (only if days is currently 0)
        if (selectedType === 'immediate' || selectedType === 'cod') {
            if (daysField.val() != 0) {
                if (confirm('Tipe yang dipilih biasanya menggunakan 0 hari. Apakah ingin mengubah jumlah hari menjadi 0?')) {
                    daysField.val(0);
                }
            }
        } else if (selectedType === 'net' && daysField.val() == 0) {
            if (confirm('Tipe NET biasanya menggunakan lebih dari 0 hari. Apakah ingin mengubah menjadi 30 hari?')) {
                daysField.val(30);
            }
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

        // Confirm update
        Swal.fire({
            title: 'Perbarui Syarat Pembayaran?',
            text: 'Apakah Anda yakin ingin memperbarui syarat pembayaran ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Perbarui!',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.value) {
                $('#submit-btn').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Loading...'
                );
                
                $.ajax({
                    url: "{{ route('master_data.payment_term.update') }}",
                    method: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: $('#payment-term-id').val(),
                        code: $('#code').val(),
                        name: $('#name').val(),
                        description: $('#description').val(),
                        days: parseInt($('#days').val()) || 0,
                        type: $('#type').val(),
                        status: $('#status').val(),
                        is_default: $('#is_default').is(':checked')
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Syarat pembayaran berhasil diperbarui.',
                            customClass: {
                                confirmButton: 'btn btn-success'
                            },
                            buttonsStyling: false
                        }).then(function() {
                            window.location.href = "{{ route('master_data.payment_term.detail', $paymentTerm->id) }}";
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
                        $('#submit-btn').prop('disabled', false).html('Simpan Data');
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