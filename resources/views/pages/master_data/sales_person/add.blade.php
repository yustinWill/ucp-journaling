@extends('layouts.default')
@section('page_title')
    Tambah Sales Person
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Tambah Sales Person</h4>
        <form class="card-body" id="sales-person-form" action="#" method="POST">
            @csrf
            
            <!-- Info Sales Person Section -->
            <div class="section-header mt-12">
                <h6>Info Sales Person</h6>
                <hr class="mx-n6">
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Pilih Karyawan <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="sales-person-employee" name="sales_person_employee" data-allow-clear="false" required>
                        <option value="">Pilih Karyawan</option>
                        @foreach($availableEmployees as $employee)
                            <option value="{{ $employee['id'] }}">{{ $employee['name'] }}</option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">Pilih karyawan yang akan dijadikan sales person</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="sales-person-status" name="sales_person_status" data-allow-clear="false" required>
                        <option value="">Pilih Status</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status['value'] }}">{{ $status['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Komisi (%) <span style="color: red;">*</span></label>
                    <input type="number" class="form-control" id="sales-person-commission" name="sales_person_commission" 
                           placeholder="0.00" step="0.01" min="0" max="100" required />
                    <small class="form-text text-muted">Masukkan persentase komisi (0-100)</small>
                </div>
                <div class="col-md-6">
                    <!-- Empty for alignment -->
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Keterangan</label>
                    <textarea class="form-control" id="sales-person-description" name="sales_person_description" rows="3" 
                              placeholder="Masukkan keterangan tambahan (opsional)"></textarea>
                    <small class="form-text text-muted">Keterangan tambahan mengenai sales person ini</small>
                </div>
                <div class="col-md-6">
                    <!-- Empty for alignment -->
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 text-end">
                <button type="button" class="btn btn-label-danger me-3" onclick="window.history.back()">Batalkan</button>
                <button type="submit" class="btn btn-success" id="submit-btn">Simpan</button>
            </div>
        </form>
    </div>
@endsection

@section('js_script')
<script>
$(document).ready(function() {
    $('#sales-person-form').on('submit', function(e) {
        e.preventDefault();

        // Validate required fields
        let isValid = true;
        const requiredFields = ['sales-person-employee', 'sales-person-status', 'sales-person-commission'];

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
            title: 'Simpan Sales Person?',
            text: 'Apakah Anda yakin ingin membuat sales person ini?',
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
                    url: "{{ route('master_data.sales_person.save') }}",
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        f_employee_contact_id: $('#sales-person-employee').val(),
                        status: $('#sales-person-status').val(),
                        commission_rate: $('#sales-person-commission').val(),
                        description: $('#sales-person-description').val()
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Sales Person berhasil dibuat.',
                            customClass: {
                                confirmButton: 'btn btn-success'
                            },
                            buttonsStyling: false
                        }).then(function() {
                            window.location.href = "{{ route('master_data.sales_person.index') }}";
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