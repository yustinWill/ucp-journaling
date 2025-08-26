@extends('layouts.default')
@section('page_title')
    Reversal Produksi
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Reversal Produksi</h4>
        <form class="card-body" id="reversal-form" autocomplete="off">
            @csrf
            
            <!-- Production Information Section -->
            <div class="section-header mt-12">
                <h6>Informasi Produksi</h6>
                <hr class="mx-n6">
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">No Produksi</label>
                    <input type="text" class="form-control" name="production_number" value="{{ $production->production_number ?? 'Loading...' }}" readonly />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Produksi</label>
                    <input type="text" class="form-control" name="production_date" value="{{ $production->production_date ?? 'Loading...' }}" readonly />
                </div>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Bundle Product</label>
                    <input type="text" class="form-control" name="bundle_product_name" value="{{ $production->bundle_product_name ?? 'Loading...' }}" readonly />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Warehouse</label>
                    <input type="text" class="form-control" name="warehouse_name" value="{{ $production->warehouse_name ?? 'Loading...' }}" readonly />
                </div>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Quantity Produced</label>
                    <input type="text" class="form-control" name="quantity_produced" value="{{ $production->quantity_produced ?? 0 }}" readonly />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <input type="text" class="form-control" name="status" value="{{ $production->status ?? 'Loading...' }}" readonly />
                </div>
            </div>

            <!-- Reversal Information Section -->
            <div class="section-header mt-12">
                <h6>Informasi Reversal</h6>
                <hr class="mx-n6">
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Quantity to Reverse</label>
                    <input type="number" class="form-control" id="quantity-to-reverse" name="quantity_to_reverse" 
                           min="1" max="{{ $production->quantity_produced ?? 0 }}" 
                           value="{{ $production->quantity_produced ?? 0 }}" />
                    <small class="form-text text-muted">Max: {{ $production->quantity_produced ?? 0 }} units</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Alasan Reversal <span style="color: red;">*</span></label>
                    <select class="form-select" id="reason-select" name="reason_select" required>
                        <option value="">Pilih alasan reversal</option>
                        <option value="quality_issue">Quality Issue - Masalah kualitas produk</option>
                        <option value="overproduction">Overproduction - Produksi berlebihan</option>
                        <option value="material_defect">Material Defect - Cacat bahan baku</option>
                        <option value="equipment_failure">Equipment Failure - Kegagalan peralatan</option>
                        <option value="correction">Data Correction - Koreksi data</option>
                        <option value="other">Other - Lainnya</option>
                    </select>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <label class="form-label">Catatan Reversal <span style="color: red;">*</span></label>
                    <textarea class="form-control" id="reason" name="reason" rows="3" 
                              placeholder="Jelaskan alasan reversal production ini..." required></textarea>
                </div>
            </div>

            <!-- Warning Section -->
            <div class="alert alert-warning">
                <i class="icon-base ti tabler-alert-triangle me-2"></i>
                <strong>Peringatan!</strong> Proses reversal akan mengembalikan stock bahan baku dan menghapus hasil produksi. 
                Aksi ini tidak dapat dibatalkan setelah diproses.
            </div>

            <!-- Confirmation Section -->
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="confirmation" name="confirmation" required>
                        <label class="form-check-label" for="confirmation">
                            Saya memahami bahwa proses reversal ini akan mempengaruhi stock dan tidak dapat dibatalkan
                        </label>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 text-end">
                <a href="{{ route('main_transaction.production.detail', $production->id ?? 1) }}" class="btn btn-label-secondary me-3">Kembali</a>
                <button type="submit" class="btn btn-danger" id="submit-btn">
                    <i class="icon-base ti tabler-arrow-back-up me-2"></i>Proses Reversal
                </button>
            </div>
        </form>
    </div>
@stop

@section('js_script')
<script>
$(function() {
    // Auto-fill reason based on selection
    $('#reason-select').on('change', function() {
        const selectedReason = $(this).val();
        const reasonText = $(this).find('option:selected').text();
        
        if (selectedReason && selectedReason !== 'other') {
            $('#reason').val(reasonText.split(' - ')[1] || reasonText);
        } else if (selectedReason === 'other') {
            $('#reason').val('').focus();
        }
    });
    
    // Form submission
    $('#reversal-form').on('submit', function(e) {
        e.preventDefault();
        
        // Validate required fields
        let isValid = true;
        const requiredFields = ['reason', 'confirmation'];
        
        requiredFields.forEach(function(fieldId) {
            const field = document.getElementById(fieldId);
            if (fieldId === 'confirmation' && !field.checked) {
                isValid = false;
                field.classList.add('is-invalid');
            } else if (fieldId !== 'confirmation' && !field.value.trim()) {
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
                text: 'Silakan lengkapi semua field yang wajib diisi dan centang konfirmasi.',
                customClass: { confirmButton: 'btn btn-primary' },
                buttonsStyling: false
            });
            return;
        }
        
        // Final confirmation
        Swal.fire({
            icon: 'warning',
            title: 'Konfirmasi Reversal',
            text: 'Apakah Anda yakin ingin melakukan reversal production ini? Aksi ini tidak dapat dibatalkan.',
            showCancelButton: true,
            confirmButtonText: 'Ya, Proses Reversal',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                submitReversal();
            }
        });
    });
    
    function submitReversal() {
        $('#submit-btn').prop('disabled', true).html(
            '<span class="spinner-border spinner-border-sm me-2"></span>Processing...'
        );
        
        const formData = {
            _token: '{{ csrf_token() }}',
            quantity_to_reverse: $('#quantity-to-reverse').val(),
            reason: $('#reason').val(),
            confirmation: $('#confirmation').is(':checked')
        };
        
        $.ajax({
            url: "{{ route('main_transaction.production.reversal', $production->id ?? 1) }}",
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        customClass: { confirmButton: 'btn btn-primary' },
                        buttonsStyling: false
                    }).then(() => {
                        window.location.href = "{{ route('main_transaction.production.index') }}";
                    });
                }
            },
            error: function(xhr) {
                let message = 'Terjadi kesalahan saat memproses reversal.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: message,
                    customClass: { confirmButton: 'btn btn-primary' },
                    buttonsStyling: false
                });
            },
            complete: function() {
                $('#submit-btn').prop('disabled', false).html(
                    '<i class="icon-base ti tabler-arrow-back-up me-2"></i>Proses Reversal'
                );
            }
        });
    }
});
</script>
@endsection