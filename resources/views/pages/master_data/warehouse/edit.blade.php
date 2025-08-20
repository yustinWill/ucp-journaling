@extends('layouts.default')
@section('page_title')
    Edit Warehouse
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Edit Warehouse</h4>
        <form class="card-body" id="warehouse-form" autocomplete="off">
            <!-- Data Warehouse Section -->
            <div class="section-header mt-12">
                <h6>Data Warehouse</h6>
                <hr class="mx-n6">
            </div>

            <!-- Row 1: Basic Information -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Kode Warehouse <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="warehouse-code" name="code" value="{{ $warehouse->code }}" readonly />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama Warehouse <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="warehouse-name" name="name" value="{{ $warehouse->name }}" placeholder="Masukkan nama gudang" required />
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Penanggung Jawab</label>
                    <select class="global-select2 form-select" id="warehouse-pic" name="pic_users[]" data-allow-clear="false" multiple>
                        @foreach($picUsers as $user)
                            <option value="{{ $user['value'] }}" 
                                {{ in_array($user['value'], json_decode($warehouse->f_pic_ids ?? '[]')) ? 'selected' : '' }}>
                                {{ $user['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="warehouse-status" name="status" data-allow-clear="false" required>
                        <option value="">Pilih Status</option>
                        @foreach($warehouseStatuses as $status)
                            <option value="{{ $status['value'] }}" {{ $warehouse->status == $status['value'] ? 'selected' : '' }}>{{ $status['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Alamat Section -->
            <div class="section-header mt-12">
                <h6>Alamat Warehouse</h6>
                <hr class="mx-n6">
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Provinsi</label>
                    <input type="text" class="form-control" id="warehouse-province" name="province" value="{{ $warehouse->province }}" placeholder="Masukkan provinsi" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kota</label>
                    <input type="text" class="form-control" id="warehouse-city" name="city" value="{{ $warehouse->city }}" placeholder="Masukkan kota" />
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Kecamatan</label>
                    <input type="text" class="form-control" id="warehouse-district" name="district" value="{{ $warehouse->district }}" placeholder="Masukkan kecamatan" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kelurahan</label>
                    <input type="text" class="form-control" id="warehouse-sub-district" name="sub_district" value="{{ $warehouse->sub_district }}" placeholder="Masukkan kelurahan" />
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Alamat Lengkap</label>
                    <textarea class="form-control" id="warehouse-address" name="address" rows="3" placeholder="Masukkan alamat lengkap">{{ $warehouse->address }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kode POS</label>
                    <input type="text" class="form-control" id="warehouse-postal-code" name="postal_code" value="{{ $warehouse->postal_code }}" placeholder="50000" />
                </div>
            </div>

            <!-- Informasi Tambahan Section -->
            <div class="section-header mt-12">
                <h6>Informasi Tambahan</h6>
                <hr class="mx-n6">
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tag</label>
                    <select class="global-select2 form-select" id="warehouse-tags" name="tags[]" multiple data-allow-clear="false">
                        @foreach($availableTags as $tag)
                            <option value="{{ $tag['value'] }}" 
                                {{ in_array($tag['value'], json_decode($warehouse->f_tag_codes ?? '[]')) ? 'selected' : '' }}>
                                {{ $tag['label'] }}
                            </option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">Pilih satu atau lebih tag untuk kategorisasi gudang</small>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 text-end">
                <button type="button" class="btn btn-label-danger me-3" id="cancel-btn">Batalkan</button>
                <button type="button" class="btn btn-success" id="submit-btn">Perbarui</button>
            </div>
        </form>
    </div>
@endsection
@section('js_script')
    <script>
        $(function() {
            // Form submission
            $('#submit-btn').click(function(e) {
                e.preventDefault();

                // Validate required fields
                let isValid = true;
                const requiredFields = ['warehouse-code', 'warehouse-name', 'warehouse-status'];

                requiredFields.forEach(function(fieldId) {
                    const field = $('#' + fieldId);
                    if (!field.val() || field.val().trim() === '') {
                        field.addClass('is-invalid');
                        isValid = false;
                    } else {
                        field.removeClass('is-invalid');
                    }
                });

                if (!isValid) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: 'Mohon lengkapi semua field yang wajib diisi.',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    return;
                }

                function getWarehouseFormData() {
                    const data = {
                        code: $('#warehouse-code').val(),
                        name: $('#warehouse-name').val(),
                        pic_users: $('#warehouse-pic').val() || [],
                        status: $('#warehouse-status').val(),
                        province: $('#warehouse-province').val(),
                        city: $('#warehouse-city').val(),
                        district: $('#warehouse-district').val(),
                        sub_district: $('#warehouse-sub-district').val(),
                        address: $('#warehouse-address').val(),
                        postal_code: $('#warehouse-postal-code').val(),
                        tags: $('#warehouse-tags').val() || []
                    };
                    return data;
                }

                Swal.fire({
                    title: 'Perbarui Warehouse?',
                    text: 'Cek kembali data yang sudah diisi!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Perbarui!',
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-label-secondary'
                    },
                    buttonsStyling: false
                }).then(function(result) {
                    if (result.value) {
                        $('#submit-btn').prop('disabled', true).html(
                            '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Loading...'
                        );

                        const warehouseData = getWarehouseFormData();

                        $.ajax({
                            url: "{{ route('master_data.warehouse.update', $warehouse->id) }}",
                            method: 'PUT',
                            data: {
                                ...warehouseData,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Warehouse berhasil diperbarui.',
                                    customClass: {
                                        confirmButton: 'btn btn-success'
                                    },
                                    buttonsStyling: false
                                }).then(function() {
                                    window.location.href = "{{ route('master_data.warehouse.index') }}";
                                });
                            },
                            error: function(xhr) {
                                const res = xhr.responseJSON;
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal memperbarui',
                                    html: res?.message || 'Terjadi kesalahan saat memperbarui data.',
                                    customClass: {
                                        confirmButton: 'btn btn-primary'
                                    },
                                    buttonsStyling: false
                                });
                            },
                            complete: function() {
                                $('#submit-btn').prop('disabled', false).html('Perbarui');
                            }
                        });
                    }
                });
            });

            // Cancel button
            $('#cancel-btn').click(function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Batalkan Perubahan?',
                    text: 'Data yang sudah diubah tidak akan disimpan!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Batalkan!',
                    cancelButtonText: 'Tidak',
                    customClass: {
                        confirmButton: 'btn btn-danger',
                        cancelButton: 'btn btn-label-secondary'
                    },
                    buttonsStyling: false
                }).then(function(result) {
                    if (result.value) {
                        window.location.href = "{{ route('master_data.warehouse.index') }}";
                    }
                });
            });
        });
    </script>
@endsection