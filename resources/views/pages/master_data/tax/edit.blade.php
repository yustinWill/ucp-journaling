@extends('layouts.default')
@section('page_title')
    Edit Pajak
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Edit Pajak</h4>
        <form class="card-body" id="tax-form" action="#" method="POST">
            <input type="hidden" id="tax-id" value="{{ $tax->id }}">
            @csrf
            @method('POST')
                
                <!-- Info Pajak Section -->
                <div class="section-header mt-12">
                    <h6>Info Pajak</h6>
                    <hr class="mx-n6">
                </div>
                
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Kode Pajak <span style="color: red;">*</span></label>
                        <input type="text" class="form-control" id="tax-code" name="tax_code" disabled
                        value="{{ $tax->code }}" maxlength="10" required />
                        <small class="form-text text-muted">Kode unik untuk identifikasi pajak</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nama Pajak <span style="color: red;">*</span></label>
                        <input type="text" class="form-control" id="tax-name" name="tax_name" value="{{ $tax->name }}"
                        maxlength="100" required />
                    </div>
                </div>
                
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Tarif Pajak <span style="color: red;">*</span></label>
                        <input type="number" class="form-control" id="tax-rate" name="tax_rate" value="{{ $tax->rate }}"
                        step="0.01" min="0" max="100" required />
                        <small class="form-text text-muted">Dalam persen atau nominal tetap</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tipe Pajak <span style="color: red;">*</span></label>
                        <select class="global-select2 form-select" id="tax-type" name="tax_type" data-allow-clear="false" required>
                            <option value="">Pilih Tipe Pajak</option>
                            @foreach($taxTypes as $type)
                                <option value="{{ $type['value'] }}" {{ $tax->type == $type['value'] ? 'selected' : '' }}>{{ $type['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Status <span style="color: red;">*</span></label>
                        <select class="global-select2 form-select" id="tax-status" name="status" data-allow-clear="false" required>
                            <option value="">Pilih Status</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status['value'] }}" {{ $tax->status == $status['value'] ? 'selected' : '' }}>{{ $status['label'] }}</option>
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
                        <textarea class="form-control" id="tax-description" name="description" rows="3"
                        placeholder="Deskripsi pajak (opsional)">{{ $tax->description }}</textarea>
                        <small class="form-text text-muted">Penjelasan mengenai pajak ini</small>
                    </div>
                    <div class="col-md-6">
                        <!-- Empty for alignment -->
                    </div>
                </div>

            <!-- Action Buttons -->
            <div class="pt-6 text-end">
                <a href="{{ route('master_data.tax.detail', $tax->id) }}" class="btn btn-label-danger me-3">Batalkan</a>
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
        $('#tax-form').on('submit', function(e) {
            e.preventDefault();
            
            // Validate required fields
            let isValid = true;
            const requiredFields = ['tax-code', 'tax-name', 'tax-rate', 'tax-type', 'tax-status'];
            
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
                title: 'Perbarui Pajak?',
                text: 'Apakah Anda yakin ingin memperbarui pajak ini?',
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
                        url: "{{ route('master_data.tax.update') }}",
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: $('#tax-id').val(),
                            name: $('#tax-name').val(),
                            rate: $('#tax-rate').val(),
                            type: $('#tax-type').val(),
                            status: $('#tax-status').val(),
                            description: $('#tax-description').val()
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Pajak berhasil diperbarui.',
                                customClass: {
                                    confirmButton: 'btn btn-success'
                                },
                                buttonsStyling: false
                            }).then(function() {
                                window.location.href =
                                    "{{ route('master_data.tax.detail', $tax->id) }}";
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