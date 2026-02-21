@extends('layouts.default')
@section('page_title')
    Add Role
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Tambah Role</h4>
        <form class="card-body" id="roleForm" autocomplete="off">
            <!-- Role Information Section -->
            <div class="section-header mt-12">
                <h6>Informasi Role</h6>
                <hr class="mx-n6">
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Nama Role <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="role-name" placeholder="Masukkan nama role"
                        maxlength="100" />
                    <small class="form-text text-muted">Maksimal 100 karakter</small>
                </div>
                {{-- <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select class="global-select2 form-select" id="role-status" data-allow-clear="false">
                        @foreach ($statuses as $status)
                            <option value="{{ $status['value'] }}">{{ $status['label'] }}</option>
                        @endforeach
                    </select>
                </div> --}}
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Deskripsi</label>
                    <textarea class="form-control" id="role-description" rows="3" placeholder="Masukkan deskripsi role"
                        maxlength="500"></textarea>
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

            {{-- <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tag</label>
                    <div class="row g-2">
                        <div class="col">
                            <select class="global-select2 form-select" id="role-tags" multiple data-allow-clear="false">
                                @foreach ($tags as $tag)
                                    <option value="{{ $tag->id }}" data-color="{{ $tag->tag_color_code }}">
                                        {{ $tag->code }} - {{ $tag->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($canAddTags)
                            <div class="col-auto">
                                <a href="{{ route('master_data.tag.add') }}" target="_blank"
                                    class="btn btn-outline-primary d-flex align-items-center" id="add-new-role-tag"
                                    title="Tambah Tag Baru">
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
            </div> --}}

            <!-- Permissions Section -->
            <div class="section-header mt-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Hak Akses Modul</h6>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="selectAll">Pilih Semua</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAll">Hapus
                            Semua</button>
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
                                                    value="{{ $permission->id }}">
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
                const totalCount = $('.permission-checkbox:checked').length;
                const totalAvailable = $('.permission-checkbox').length;
                
                $('#read-count').text(totalCount);
                $('#write-count').text(totalAvailable);
                $('#delete-count').text('');
                $('#approval-count').text('');
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
                const permissions = [];

                // Collect selected permissions
                $('.permission-checkbox:checked').each(function() {
                    permissions.push($(this).data('permission-id'));
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
                    if (result.value) {
                        $('#submit-btn').prop('disabled', true).html(
                            '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Loading...'
                        );
                        $.ajax({
                            url: "{{ route('master_data.role.save') }}",
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                name: $('#role-name').val(),
                                description: $('#role-description').val(),
                                permissions: permissions,
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Role berhasil dibuat.',
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
                                    'Simpan Data');
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
