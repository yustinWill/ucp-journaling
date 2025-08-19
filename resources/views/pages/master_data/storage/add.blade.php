@extends('layouts.default')
@section('page_title')
    Tambah Gudang
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Tambah Gudang</h4>
        <form class="card-body" id="storage-form" autocomplete="off">
            <!-- Data Gudang Section -->
            <div class="section-header mt-12">
                <h6>Data Gudang</h6>
                <hr class="mx-n6">
            </div>

            <!-- Row 1: Basic Information -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Kode Gudang <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="storage-code" name="code" placeholder="GDG-2025-001" required />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama Gudang <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="storage-name" name="name" placeholder="Masukkan nama gudang" required />
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Penanggung Jawab</label>
                    <select class="global-select2 form-select" id="storage-pic" name="pic_users[]" data-allow-clear="false" multiple>
                        @foreach($picUsers as $user)
                            <option value="{{ $user['value'] }}">{{ $user['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="storage-status" name="status" data-allow-clear="false" required>
                        <option value="">Pilih Status</option>
                        @foreach($storageStatuses as $status)
                            <option value="{{ $status['value'] }}" {{ $status['value'] == 'ACTIVE' ? 'selected' : '' }}>{{ $status['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Alamat Section -->
            <div class="section-header mt-12">
                <h6>Alamat Gudang</h6>
                <hr class="mx-n6">
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Provinsi</label>
                    <input type="text" class="form-control" id="storage-province" name="province" placeholder="Masukkan provinsi" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kota</label>
                    <input type="text" class="form-control" id="storage-city" name="city" placeholder="Masukkan kota" />
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Kecamatan</label>
                    <input type="text" class="form-control" id="storage-district" name="district" placeholder="Masukkan kecamatan" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kelurahan</label>
                    <input type="text" class="form-control" id="storage-sub-district" name="sub_district" placeholder="Masukkan kelurahan" />
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Alamat Lengkap</label>
                    <textarea class="form-control" id="storage-address" name="address" rows="3" placeholder="Masukkan alamat lengkap"></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kode POS</label>
                    <input type="text" class="form-control" id="storage-postal-code" name="postal_code" placeholder="50000" />
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
                    <select class="global-select2 form-select" id="storage-tags" name="tags[]" multiple data-allow-clear="false">
                        @foreach($availableTags as $tag)
                            <option value="{{ $tag['value'] }}">{{ $tag['label'] }}</option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">Pilih satu atau lebih tag untuk kategorisasi gudang</small>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 text-end">
                <button type="button" class="btn btn-label-danger me-3" id="cancel-btn">Batalkan</button>
                <button type="button" class="btn btn-success" id="submit-btn">Simpan</button>
            </div>
        </form>
    </div>
@endsection
@section('js_script')
    <script>
        $(function() {
            // Initialize form
            generateStorageCode();

            function generateStorageCode() {
                const year = new Date().getFullYear();
                const randomNum = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
                $('#storage-code').val(`GDG-${year}-${randomNum}`);
            }

            // Form submission
            $('#submit-btn').click(function(e) {
                e.preventDefault();

                // Validate required fields
                let isValid = true;
                const requiredFields = ['storage-code', 'storage-name', 'storage-status'];

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

                function getStorageFormData() {
                    const data = {
                        code: $('#storage-code').val(),
                        name: $('#storage-name').val(),
                        pic_users: $('#storage-pic').val() || [],
                        status: $('#storage-status').val(),
                        province: $('#storage-province').val(),
                        city: $('#storage-city').val(),
                        district: $('#storage-district').val(),
                        sub_district: $('#storage-sub-district').val(),
                        address: $('#storage-address').val(),
                        postal_code: $('#storage-postal-code').val(),
                        tags: $('#storage-tags').val() || []
                    };
                    return data;
                }

                Swal.fire({
                    title: 'Simpan Gudang?',
                    text: 'Cek kembali data yang sudah diisi!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan!',
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

                        const storageData = getStorageFormData();

                        $.ajax({
                            url: "{{ route('master_data.storage.store') }}",
                            method: 'POST',
                            data: {
                                ...storageData,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Gudang berhasil ditambahkan.',
                                    customClass: {
                                        confirmButton: 'btn btn-success'
                                    },
                                    buttonsStyling: false
                                }).then(function() {
                                    window.location.href = "{{ route('master_data.storage.index') }}";
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

            // Cancel button
            $('#cancel-btn').click(function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Batalkan Pembuatan?',
                    text: 'Data yang sudah diisi akan hilang!',
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
                        window.location.href = "{{ route('master_data.storage.index') }}";
                    }
                });
            });
        });
    </script>
@endsection
