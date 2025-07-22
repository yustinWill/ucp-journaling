@extends('layouts.default')
@section('page_title')
    Tambah Unit
@stop
@section('content')
    <div class="card mb-6">
        <h5 class="card-header">Tambah Unit</h5>
        <form class="card-body" id="unit-form" action="#" method="POST">
                @csrf
                
                <!-- Info Unit Section -->
                <div class="section-header mt-12">
                    <h6>Info Unit</h6>
                    <hr class="mx-n6">
                </div>
                
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Kode Unit <span style="color: red;">*</span></label>
                        <input type="text" class="form-control" id="unit-code" name="unit_code" placeholder="BTL" maxlength="10" required />
                        <small class="form-text text-muted">Kode unik untuk identifikasi unit</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nama Unit <span style="color: red;">*</span></label>
                        <input type="text" class="form-control" id="unit-name" name="unit_name" placeholder="Botol" maxlength="100" required />
                    </div>
                </div>
                
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Simbol Unit <span style="color: red;">*</span></label>
                        <input type="text" class="form-control" id="unit-symbol" name="unit_symbol" placeholder="btl" maxlength="10" required />
                        <small class="form-text text-muted">Simbol singkat untuk unit (btl, kg, ltr)</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tipe Unit <span style="color: red;">*</span></label>
                        <select class="global-select2 form-select" id="unit-type" name="unit_type" data-allow-clear="false" required>
                            <option value="">Pilih Tipe Unit</option>
                            @foreach($unitTypes as $type)
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
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Deskripsi unit (opsional)"></textarea>
                        <small class="form-text text-muted">Penjelasan mengenai unit ini</small>
                    </div>
                    <div class="col-md-6">
                        <!-- Empty for alignment -->
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-6">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary waves-effect waves-light">
                            <i class="icon-base ti tabler-device-floppy me-2"></i>Simpan
                        </button>
                        <button type="button" class="btn btn-secondary waves-effect waves-light" onclick="window.history.back()">
                            <i class="icon-base ti tabler-arrow-left me-2"></i>Batal
                        </button>
                    </div>
                </div>
            </form>
    </div>
@stop

@section('js_script')
<script>
    $(function() {
        // Form validation and submission
        $('#unit-form').on('submit', function(e) {
            e.preventDefault();
            
            // Validate required fields
            let isValid = true;
            const requiredFields = ['unit-code', 'unit-name', 'unit-symbol', 'unit-type', 'status'];
            
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
                title: 'Simpan Unit?',
                text: 'Apakah Anda yakin ingin membuat unit ini?',
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
                        text: 'Unit berhasil dibuat.',
                        customClass: { confirmButton: 'btn btn-success' },
                        buttonsStyling: false
                    }).then(() => {
                        window.location.href = "{{ route('master_data.unit.index') }}";
                    });
                }
            });
        });

        // Auto-generate symbol based on unit name
        $('#unit-name').on('input', function() {
            const name = $(this).val().toLowerCase();
            let symbol = '';
            
            // Common unit mappings for RIYO Water business
            if (name.includes('botol')) symbol = 'btl';
            else if (name.includes('gallon') || name.includes('galon')) symbol = 'gln';
            else if (name.includes('liter')) symbol = 'ltr';
            else if (name.includes('kilogram')) symbol = 'kg';
            else if (name.includes('dus') || name.includes('kotak')) symbol = 'dus';
            else if (name.includes('pieces') || name.includes('pcs')) symbol = 'pcs';
            else {
                // Generate from first 3 characters
                symbol = name.substring(0, 3);
            }
            
            $('#unit-symbol').val(symbol);
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