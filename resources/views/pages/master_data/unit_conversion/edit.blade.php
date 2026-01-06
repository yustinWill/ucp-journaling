@extends('layouts.default')
@section('page_title')
    Edit Konversi Unit
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Edit Konversi Unit</h4>
        <form class="card-body" id="unit-conversion-form" action="#" method="POST" autocomplete="off">
            @csrf
            @method('PUT')

            <!-- Info Konversi Section -->
            <div class="section-header mt-12">
                <h6>Info Konversi Unit</h6>
                <hr class="mx-n6">
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Unit Asal <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="from-unit" name="from_unit_id" data-allow-clear="false"
                        required>
                        <option value="">Pilih Unit Asal</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id }}" {{ $unit->id == $unitConversion->f_from_unit_id ? 'selected' : '' }}>
                                {{ $unit->code }} - {{ $unit->name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">Unit yang akan dikonversi</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Unit Tujuan <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="to-unit" name="to_unit_id" data-allow-clear="false"
                        required>
                        <option value="">Pilih Unit Tujuan</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id }}" {{ $unit->id == $unitConversion->f_to_unit_id ? 'selected' : '' }}>
                                {{ $unit->code }} - {{ $unit->name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">Unit hasil konversi</small>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Faktor Konversi <span style="color: red;">*</span></label>
                    <input type="number" class="form-control" id="conversion-factor" name="conversion_factor"
                        placeholder="10" step="0.0001" min="0.0001" value="{{ $unitConversion->conversion_factor }}" required />
                    <small class="form-text text-muted">Contoh: 1 Box = 10 KG, faktor = 10</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Formula Konversi</label>
                    <input type="text" class="form-control" id="conversion-formula" name="conversion_formula"
                        placeholder="1 Box = 10 KG" maxlength="255" value="{{ $unitConversion->conversion_formula }}" />
                    <small class="form-text text-muted">Deskripsi formula (opsional)</small>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Status <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="status" name="status" data-allow-clear="false"
                        required>
                        <option value="">Pilih Status</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status['value'] }}" {{ $status['value'] == $unitConversion->status ? 'selected' : '' }}>
                                {{ $status['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Catatan</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"
                        placeholder="Catatan tambahan (opsional)">{{ $unitConversion->notes }}</textarea>
                </div>
            </div>

            <!-- Preview Section -->
            <div class="row g-3 mb-4">
                <div class="col-12">
                    <div class="alert alert-info" role="alert">
                        <h5 class="alert-heading mb-2">
                            <i class="icon-base ti tabler-info-circle me-2"></i>Preview Konversi
                        </h5>
                        <p class="mb-0" id="conversion-preview">
                            <span class="text-muted">Pilih unit dan faktor konversi untuk melihat preview</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 text-end">
                <button type="button" class="btn btn-label-danger me-3" onclick="window.history.back()">Batalkan</button>
                @if($canWrite)
                    <button type="submit" class="btn btn-success" id="submit-btn">Update</button>
                @endif
            </div>
        </form>
    </div>
@stop

@section('js_script')
    <script>
        $(function() {
            // Preview update
            function updatePreview() {
                const fromUnitText = $('#from-unit option:selected').text();
                const toUnitText = $('#to-unit option:selected').text();
                const factor = $('#conversion-factor').val();

                if (fromUnitText && toUnitText && factor && fromUnitText !== 'Pilih Unit Asal' && toUnitText !== 'Pilih Unit Tujuan') {
                    const fromUnitCode = fromUnitText.split(' - ')[0];
                    const toUnitCode = toUnitText.split(' - ')[0];
                    $('#conversion-preview').html(
                        `<strong>1 ${fromUnitCode} = ${factor} ${toUnitCode}</strong>`
                    );
                } else {
                    $('#conversion-preview').html(
                        '<span class="text-muted">Pilih unit dan faktor konversi untuk melihat preview</span>'
                    );
                }
            }

            // Initial preview update
            updatePreview();

            // Update preview on change
            $('#from-unit, #to-unit, #conversion-factor').on('change input', updatePreview);

            // Form validation and submission
            $('#unit-conversion-form').on('submit', function(e) {
                e.preventDefault();

                // Validate required fields
                let isValid = true;
                const requiredFields = ['from-unit', 'to-unit', 'conversion-factor', 'status'];

                requiredFields.forEach(function(fieldId) {
                    const field = document.getElementById(fieldId);
                    if (!field.value.trim()) {
                        isValid = false;
                        field.classList.add('is-invalid');
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });

                // Check if from and to units are different
                if ($('#from-unit').val() === $('#to-unit').val()) {
                    isValid = false;
                    Swal.fire({
                        icon: 'error',
                        title: 'Kesalahan Validasi',
                        text: 'Unit asal dan unit tujuan harus berbeda.',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    return;
                }

                if (!isValid) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Kesalahan Validasi',
                        text: 'Silakan lengkapi semua field yang wajib diisi.',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    return;
                }

                // Confirm update
                Swal.fire({
                    title: 'Update Konversi Unit?',
                    text: 'Apakah Anda yakin ingin mengupdate konversi unit ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Update!',
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
                            url: "{{ route('master_data.unit_conversion.update', $unitConversion->id) }}",
                            method: 'PUT',
                            data: {
                                _token: '{{ csrf_token() }}',
                                from_unit_id: $('#from-unit').val(),
                                to_unit_id: $('#to-unit').val(),
                                conversion_factor: $('#conversion-factor').val(),
                                conversion_formula: $('#conversion-formula').val(),
                                status: $('#status').val(),
                                notes: $('#notes').val()
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Konversi unit berhasil diupdate.',
                                    customClass: {
                                        confirmButton: 'btn btn-success'
                                    },
                                    buttonsStyling: false
                                }).then(function() {
                                    window.location.href =
                                        "{{ route('master_data.unit_conversion.index') }}";
                                });
                            },
                            error: function(xhr) {
                                const res = xhr.responseJSON;
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal mengupdate',
                                    html: res?.message ||
                                        'Terjadi kesalahan saat mengupdate data.',
                                    customClass: {
                                        confirmButton: 'btn btn-primary'
                                    },
                                    buttonsStyling: false
                                });
                            },
                            complete: function() {
                                $('#submit-btn').prop('disabled', false).html(
                                    'Update');
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
