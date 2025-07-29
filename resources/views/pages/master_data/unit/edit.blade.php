@extends('layouts.default')
@section('page_title')
    Edit Unit
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Edit Unit</h4>
        <form class="card-body" id="unit-form" action="#" method="POST">
            <input type="hidden" id="unit-id" value="{{ $unit->id }}">
            @csrf
            @method('POST')

            <!-- Info Unit Section -->
            <div class="section-header mt-12">
                <h6>Info Unit</h6>
                <hr class="mx-n6">
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Kode Unit <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="unit-code" name="unit_code" disabled
                        value="{{ $unit->code }}" maxlength="10" required />
                    <small class="form-text text-muted">Kode unik untuk identifikasi unit</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama Unit <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="unit-name" name="unit_name" value="{{ $unit->name }}"
                        maxlength="100" required />
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tipe Unit <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="unit-type" name="unit_type" data-allow-clear="false"
                        required>
                        <option value="">Pilih Tipe Unit</option>
                        @foreach ($unitTypes as $type)
                            <option value="{{ $type->id }}" {{ $unit->unit_type_id == $type->id ? 'selected' : '' }}>
                                {{ $type->code }} - {{ str_replace($type->code . ' - ', '', $type->name) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="unit-status" name="status" data-allow-clear="false"
                        required>
                        @foreach ($statuses as $status)
                            <option value="{{ $status['value'] }}"
                                {{ $unit->status == $status['value'] ? 'selected' : '' }}>{{ $status['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Deskripsi</label>
                    <textarea class="form-control" id="unit-description" name="description" rows="3"
                        placeholder="Deskripsi unit (opsional)">{{ $unit->description }}</textarea>
                    <small class="form-text text-muted">Penjelasan mengenai unit ini</small>
                </div>
                <div class="col-md-6">
                    <!-- Empty for alignment -->
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 text-end">
                <a href="{{ route('master_data.unit.detail', $unit->id) }}" class="btn btn-label-danger me-3">Batalkan</a>
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
            $('#unit-form').on('submit', function(e) {
                e.preventDefault();

                // Validate required fields
                let isValid = true;
                const requiredFields = ['unit-code', 'unit-name', 'unit-type', 'unit-status'];

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
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    return;
                }

                // Confirm save
                Swal.fire({
                    title: 'Perbarui Unit?',
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
                    if (result.value) {
                        $('#submit-btn').prop('disabled', true).html(
                            '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Loading...'
                        );
                        $.ajax({
                            url: "{{ route('master_data.unit.update') }}",
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                id: $('#unit-id').val(),
                                code: $('#unit-code').val(),
                                name: $('#unit-name').val(),
                                type: $('#unit-type').val(),
                                status: $('#unit-status').val(),
                                description: $('#unit-description').val()
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Unit berhasil diperbarui.',
                                    customClass: {
                                        confirmButton: 'btn btn-success'
                                    },
                                    buttonsStyling: false
                                }).then(function() {
                                    window.location.href =
                                        "{{ route('master_data.unit.detail', $unit->id) }}";
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
