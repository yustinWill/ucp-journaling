@extends('layouts.default')
@section('page_title')
    Add Role
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Tambah Role</h4>
        <form class="card-body" id="roleForm">
            <!-- Role Information Section -->
            <div class="section-header mt-12">
                <h6>Informasi Role</h6>
                <hr class="mx-n6">
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Nama Role <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="role-name" placeholder="Masukkan nama role" maxlength="100" />
                    <small class="form-text text-muted">Maksimal 100 karakter</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select class="global-select2 form-select" id="role-status" data-allow-clear="false">
                        <option value="ACTIVE" selected>ACTIVE - Aktif</option>
                        <option value="INACTIVE">INACTIVE - Tidak Aktif</option>
                    </select>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Deskripsi</label>
                    <textarea class="form-control" id="role-description" rows="3" placeholder="Masukkan deskripsi role" maxlength="500"></textarea>
                    <small class="form-text text-muted">Maksimal 500 karakter</small>
                </div>
                <div class="col-md-6">
                    <!-- Empty column for alignment -->
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
                    <div class="row g-2">
                        <div class="col">
                            <select class="global-select2 form-select" id="role-tags" multiple data-allow-clear="false">
                                @foreach($tags as $tag)
                                    <option value="{{ $tag['value'] }}" data-color="{{ $tag['color'] }}">{{ $tag['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if($canAddTags)
                            <div class="col-auto">
                                <a href="{{ route('master_data.tag.add') }}" target="_blank" class="btn btn-outline-primary d-flex align-items-center" id="add-new-role-tag" title="Tambah Tag Baru">
                                    <i class="icon-base ti tabler-plus"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                    <small class="form-text text-muted">Pilih satu atau lebih tag untuk kategorisasi role</small>
                </div>
                <div class="col-md-6">
                    <!-- Empty column for alignment -->
                </div>
            </div>

            <!-- Permissions Section -->
            <div class="section-header mt-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Hak Akses Modul</h6>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="selectAll">Pilih Semua</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAll">Hapus Semua</button>
                    </div>
                </div>
                <hr class="mx-n6">
            </div>
            
            <div class="mb-4">
                <div class="mx-n6">
                    <div id="permissions-container" style="padding: 1.5rem;">
                        @foreach($modules as $module)
                        <div class="module-permissions mb-4">
                            <h6 class="mb-3">
                                <i class="icon-base ti tabler-folder me-2"></i>
                                {{ $module['name'] }}
                            </h6>
                            <div class="row g-3">
                                @foreach($permission_types as $type)
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input permission-checkbox" 
                                               type="checkbox" 
                                               id="permission-{{ $module['id'] }}-{{ $type['value'] }}" 
                                               data-module="{{ $module['id'] }}" 
                                               data-type="{{ $type['value'] }}" 
                                               value="{{ $module['id'] }}-{{ $type['value'] }}">
                                        <label class="form-check-label" for="permission-{{ $module['id'] }}-{{ $type['value'] }}">
                                            <i class="icon-base ti tabler-{{ $type['value'] == 'read' ? 'eye' : ($type['value'] == 'write' ? 'edit' : ($type['value'] == 'delete' ? 'trash' : 'check')) }} me-1"></i>
                                            {{ $type['label'] }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @if(!$loop->last)
                                <hr class="my-3">
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Permission Summary -->
            <div class="section-header mt-12">
                <h6>Ringkasan Hak Akses</h6>
                <hr class="mx-n6">
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="text-center p-3" style="background-color: #f8f9fa; border-radius: 0.375rem;">
                        <h4 class="mb-0 text-success" id="read-count">0</h4>
                        <small class="text-muted">Hak Baca</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center p-3" style="background-color: #f8f9fa; border-radius: 0.375rem;">
                        <h4 class="mb-0 text-primary" id="write-count">0</h4>
                        <small class="text-muted">Hak Tulis</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center p-3" style="background-color: #f8f9fa; border-radius: 0.375rem;">
                        <h4 class="mb-0 text-danger" id="delete-count">0</h4>
                        <small class="text-muted">Hak Hapus</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center p-3" style="background-color: #f8f9fa; border-radius: 0.375rem;">
                        <h4 class="mb-0 text-warning" id="approval-count">0</h4>
                        <small class="text-muted">Hak Persetujuan</small>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 text-end">
                <button type="button" class="btn btn-label-danger me-3" id="cancel-btn">Batal</button>
                <button type="button" class="btn btn-success" id="submit-btn">Simpan Role</button>
            </div>
        </form>
    </div>
@endsection


@section('js_script')
<script>
    $(function() {
        // Update permission counts
        function updatePermissionCounts() {
            const readCount = $('.permission-checkbox[data-type="read"]:checked').length;
            const writeCount = $('.permission-checkbox[data-type="write"]:checked').length;
            const deleteCount = $('.permission-checkbox[data-type="delete"]:checked').length;
            const approvalCount = $('.permission-checkbox[data-type="approval"]:checked').length;
            
            $('#read-count').text(readCount);
            $('#write-count').text(writeCount);
            $('#delete-count').text(deleteCount);
            $('#approval-count').text(approvalCount);
        }

        // Handle permission checkbox changes
        $('.permission-checkbox').change(function() {
            updatePermissionCounts();
        });

        // Select all permissions
        $('#selectAll').click(function() {
            $('.permission-checkbox').prop('checked', true);
            updatePermissionCounts();
        });

        // Deselect all permissions
        $('#deselectAll').click(function() {
            $('.permission-checkbox').prop('checked', false);
            updatePermissionCounts();
        });

        // Form validation
        function validateForm() {
            let isValid = true;
            const requiredFields = ['role-name'];
            
            requiredFields.forEach(function(fieldId) {
                const field = $('#' + fieldId);
                if (!field.val() || field.val().trim() === '') {
                    field.addClass('is-invalid');
                    isValid = false;
                } else {
                    field.removeClass('is-invalid');
                }
            });
            
            // Check if at least one permission is selected
            const selectedPermissions = $('.permission-checkbox:checked').length;
            if (selectedPermissions === 0) {
                isValid = false;
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Validasi',
                    text: 'Silakan pilih setidaknya satu hak akses untuk role ini.',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                });
            }
            
            return isValid;
        }

        // Submit button handler
        $('#submit-btn').click(function(e) {
            e.preventDefault();
            
            if (!validateForm()) {
                return;
            }
            
            // Collect form data
            const formData = {
                name: $('#role-name').val(),
                description: $('#role-description').val(),
                status: $('#role-status').val(),
                permissions: []
            };
            
            // Collect selected permissions
            $('.permission-checkbox:checked').each(function() {
                formData.permissions.push({
                    module_id: $(this).data('module'),
                    permission_type: $(this).data('type')
                });
            });
            
            Swal.fire({
                title: 'Simpan Role?',
                text: 'Apakah Anda yakin ingin membuat role ini?',
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
                    // TODO: Submit form data to server
                    console.log('Form data:', formData);
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Role berhasil dibuat.',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        },
                        buttonsStyling: false
                    }).then(() => {
                        window.location.href = "{{ route('master_data.role.index') }}";
                    });
                }
            });
        });

        // Cancel button handler
        $('#cancel-btn').click(function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: 'Batalkan Perubahan?',
                text: 'Semua perubahan yang belum disimpan akan hilang!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Batalkan!',
                cancelButtonText: 'Tidak, Lanjutkan',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('master_data.role.index') }}";
                }
            });
        });

        // Character count for description
        $('#role-description').on('input', function() {
            const maxLength = 500;
            const currentLength = $(this).val().length;
            const remaining = maxLength - currentLength;
            
            if (remaining < 50) {
                $(this).next('.form-text').html(`${remaining} karakter tersisa`);
                if (remaining < 0) {
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            } else {
                $(this).next('.form-text').html('Maksimal 500 karakter');
                $(this).removeClass('is-invalid');
            }
        });

        // Initialize permission counts
        updatePermissionCounts();
    });
</script>
@endsection