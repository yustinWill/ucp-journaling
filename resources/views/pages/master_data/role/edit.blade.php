@extends('layouts.default')
@section('page_title')
    Edit Role - {{ $role->name }}
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Edit Role</h4>
        <form class="card-body" id="roleEditForm" autocomplete="off">
            <input type="hidden" name="role_id" id="role-id" value="{{ $role->id }}">
            <!-- Role Information Section -->
            <div class="section-header mt-12">
                <h6>Informasi Role</h6>
                <hr class="mx-n6">
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Nama Role <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="role-name" value="{{ $role->name }}"
                        placeholder="Masukkan nama role" maxlength="100" />
                    <small class="form-text text-muted">Maksimal 100 karakter. Contoh: admin, manager, staff</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Guard Name</label>
                    <input type="text" class="form-control" id="role-guard" value="{{ $role->guard_name }}" disabled />
                    <small class="form-text text-muted">Guard name diatur otomatis (web)</small>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Deskripsi</label>
                    <textarea class="form-control" id="role-description" rows="3" placeholder="Masukkan deskripsi role"
                        maxlength="500">{{ $role->description ?? '' }}</textarea>
                    <small class="form-text text-muted">Maksimal 500 karakter</small>
                </div>
                <div class="col-md-6">
                    <!-- Empty column for alignment -->
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
                        <small class="text-muted">Permission Terpilih</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center p-3" style="background-color: #f8f9fa; border-radius: 0.375rem;">
                        <h4 class="mb-0 text-primary" id="write-count">0</h4>
                        <small class="text-muted">Total Available</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center p-3" style="background-color: #f8f9fa; border-radius: 0.375rem;">
                        <h4 class="mb-0 text-danger" id="delete-count">-</h4>
                        <small class="text-muted">-</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center p-3" style="background-color: #f8f9fa; border-radius: 0.375rem;">
                        <h4 class="mb-0 text-warning" id="approval-count">-</h4>
                        <small class="text-muted">-</small>
                    </div>
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
                        @forelse ($permissions->groupBy(function($item) { return explode('.', $item->name)[0]; }) as $module => $modulePermissions)
                            <div class="module-permissions mb-4">
                                <h6 class="mb-3">
                                    <i class="icon-base ti tabler-folder me-2"></i>
                                    {{ ucfirst($module) }}
                                </h6>
                                <div class="row g-3">
                                    @foreach ($modulePermissions as $permission)
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input permission-checkbox" type="checkbox"
                                                    id="permission-{{ $permission->id }}"
                                                    data-permission-id="{{ $permission->id }}"
                                                    value="{{ $permission->id }}"
                                                    @if ($role->hasPermissionTo($permission->name)) checked @endif>
                                                <label class="form-check-label"
                                                    for="permission-{{ $permission->id }}">
                                                    <i class="icon-base ti tabler-check me-1"></i>
                                                    {{ $permission->display_name ?? $permission->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @if (!$loop->last)
                                    <hr class="my-3">
                                @endif
                            </div>
                        @empty
                            <div class="alert alert-warning" role="alert">
                                Tidak ada permission yang tersedia. Silakan jalankan <code>php artisan permission:create-default-permissions</code> untuk membuat permission.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 text-end">
                <button type="button" class="btn btn-label-danger me-3" id="cancel-btn">Batal</button>
                <button type="button" class="btn btn-success" id="submit-btn">Perbarui Role</button>
            </div>
        </form>
    </div>
@endsection


@section('js_script')
    <script>
        $(function() {
            // Update permission counts
            function updatePermissionCounts() {
                const readCount = $('.permission-checkbox:checked').length;
                const totalCount = $('.permission-checkbox').length;
                
                $('#read-count').text(readCount);
                $('#write-count').text(totalCount);
                $('#delete-count').text('-');
                $('#approval-count').text('-');
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
                    id: $('#role-id').val(),
                    permissions: []
                };

                // Collect selected permission IDs
                $('.permission-checkbox:checked').each(function() {
                    formData.permissions.push($(this).data('permission-id'));
                });

                Swal.fire({
                    title: 'Perbarui Role?',
                    text: 'Apakah Anda yakin ingin memperbarui role ini?',
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
                            url: "{{ route('master_data.role.update') }}",
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                ...formData
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Role berhasil diperbarui.',
                                    customClass: {
                                        confirmButton: 'btn btn-success'
                                    },
                                    buttonsStyling: false
                                }).then(() => {
                                    window.location.href =
                                        "{{ route('master_data.role.index') }}";
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
                                    'Perbarui Role');
                            }
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

            // Initialize permission counts
            updatePermissionCounts();

            // Trigger character count check on page load
            $('#role-description').trigger('input');
        });
    </script>
@endsection
