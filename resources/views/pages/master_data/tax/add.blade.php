@extends('layouts.default')
@section('page_title')
    Tambah Pajak
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Tambah Pajak</h4>
        <form class="card-body" id="tax-form" action="#" method="POST" autocomplete="off">
                @csrf
                
                <!-- Info Pajak Section -->
                <div class="section-header mt-12">
                    <h6>Info Pajak</h6>
                    <hr class="mx-n6">
                </div>
                
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Kode Pajak <span style="color: red;">*</span></label>
                        <input type="text" class="form-control" id="tax-code" name="tax_code" placeholder="PPN11" maxlength="10" required />
                        <small class="form-text text-muted">Kode unik untuk identifikasi pajak</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nama Pajak <span style="color: red;">*</span></label>
                        <input type="text" class="form-control" id="tax-name" name="tax_name" placeholder="PPN 11%" maxlength="100" required />
                    </div>
                </div>
                
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Tarif Pajak <span style="color: red;">*</span></label>
                        <input type="number" class="form-control" id="tax-rate" name="tax_rate" placeholder="11.00" step="0.01" min="0" max="100" required />
                        <small class="form-text text-muted">Dalam persen atau nominal tetap</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tipe Pajak <span style="color: red;">*</span></label>
                        <select class="global-select2 form-select" id="tax-type" name="tax_type" data-allow-clear="false" required>
                            <option value="">Pilih Tipe Pajak</option>
                            @foreach($taxTypes as $type)
                                <option value="{{ $type['value'] }}">{{ $type['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Status <span style="color: red;">*</span></label>
                        <select class="global-select2 form-select" id="status" name="status" data-allow-clear="false" required>
                            <option value="">Pilih Status</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status['value'] }}">{{ $status['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <!-- Empty for alignment -->
                    </div>
                </div>
                
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Deskripsi pajak (opsional)"></textarea>
                        <small class="form-text text-muted">Penjelasan mengenai pajak ini</small>
                    </div>
                    <div class="col-md-6">
                        <!-- Empty for alignment -->
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="pt-6 text-end">
                    <button type="button" class="btn btn-label-danger me-3" onclick="window.history.back()">
                        <i class="icon-base ti tabler-arrow-left me-2"></i>Batal
                    </button>
                    @if($canWrite)
                        <button type="submit" class="btn btn-success">
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

        // Form validation and submission
        $('#tax-form').on('submit', function(e) {
            e.preventDefault();
            
            // Validate required fields
            let isValid = true;
            const requiredFields = ['tax-code', 'tax-name', 'tax-rate', 'tax-type', 'status'];
            
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
                title: 'Simpan Pajak?',
                text: 'Apakah Anda yakin ingin membuat pajak ini?',
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
                    // Simulate save success
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Pajak berhasil dibuat.',
                        customClass: { confirmButton: 'btn btn-success' },
                        buttonsStyling: false
                    }).then(() => {
                        window.location.href = "{{ route('master_data.tax.index') }}";
                    });
                }
            });
        });

        // Auto-generate tax name based on code and rate
        $('#tax-code, #tax-rate').on('input', function() {
            const code = $('#tax-code').val();
            const rate = $('#tax-rate').val();
            if (code && rate) {
                $('#tax-name').val(code + ' ' + rate + '%');
            }
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