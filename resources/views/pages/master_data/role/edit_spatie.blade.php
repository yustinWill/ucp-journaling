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
                <div class="col-md-8">
                    <label class="form-label">Nama Role <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="role-name" value="{{ $role->name }}"
                        placeholder="Contoh: admin, manager, staff" maxlength="100" />
                    <small class="form-text text-muted">Maksimal 100 karakter</small>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Guard</label>
                    <input type="text" class="form-control" value="{{ $role->guard_name }}" disabled />
                    <small class="form-text text-muted">Otomatis (web)</small>
                </div>
            </div>

            <!-- Permission Summary -->
            <div class="section-header mt-12">
                <h6>Ringkasan Hak Akses</h6>
                <hr class="mx-n6">
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <strong>Total Permissions:</strong> <span id="total-permissions">0</span> dipilih
                    </div>
                </div>
            </div>

            <!-- Permissions Section -->
            <div class="section-header mt-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Hak Akses (Permissions)</h6>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="selectAll">Pilih Semua</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAll">Hapus Semua</button>
                    </div>
                </div>
                <hr class="mx-n6">
            </div>

            <div class="mb-4">
                <div class="mx-n6" style="padding: 1.5rem;">
                    <div id="permissions-container" class="row g-3">
                        @forelse ($permissions as $permission)
                            <div class="col-md-4 col-lg-3">
                                <div class="form-check">
                                    <input class="form-check-input permission-checkbox" type="checkbox"
                                        id="permission-{{ $permission->id }}"
                                        value="{{ $permission->id }}"
                                        @if (in_array($permission->id, $rolePermissions)) checked @endif>
                                    <label class="form-check-label" for="permission-{{ $permission->id }}" style="cursor: pointer;">
                                        {{ $permission->name }}
                                    </label>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <p class="text-muted">Tidak ada permissions tersedia</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 text-end">
                <a href="{{ route('master_data.role.index') }}" class="btn btn-label-danger me-3">Batal</a>
                <button type="button" class="btn btn-success" id="submit-btn">Perbarui Role</button>
            </div>
        </form>
    </div>

@endsection

@section('js_script')
    <script>
        $(function() {
            // Update permission count
            function updatePermissionCounts() {
                const selectedCount = $('.permission-checkbox:checked').length;
                $('#total-permissions').text(selectedCount);
            }

            // Handle permission checkbox changes
            $('.permission-checkbox').change(function() {
                updatePermissionCounts();
            });

            // Select all permissions
            $('#selectAll').click(function(e) {
                e.preventDefault();
                $('.permission-checkbox').prop('checked', true);
                updatePermissionCounts();
            });

            // Deselect all permissions
            $('#deselectAll').click(function(e) {
                e.preventDefault();
                $('.permission-checkbox').prop('checked', false);
                updatePermissionCounts();
            });

            // Form validation
            function validateForm() {
                let isValid = true;
                const roleName = $('#role-name').val().trim();

                if (!roleName) {
                    $('#role-name').addClass('is-invalid');
                    isValid = false;
                } else {
                    $('#role-name').removeClass('is-invalid');
                }

                // Check if at least one permission is selected
                const selectedPermissions = $('.permission-checkbox:checked').length;
                if (selectedPermissions === 0) {
                    alert('Pilih minimal 1 permission');
                    return false;
                }

                return isValid;
            }

            // Submit form
            $('#submit-btn').click(function() {
                if (!validateForm()) {
                    return;
                }

                const selectedPermissions = $('.permission-checkbox:checked')
                    .map(function() {
                        return $(this).val();
                    }).get();

                const data = {
                    id: $('#role-id').val(),
                    name: $('#role-name').val(),
                    permissions: selectedPermissions,
                };

                $.ajax({
                    url: '{{ route('master_data.role.update') }}',
                    type: 'POST',
                    data: JSON.stringify(data),
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Toastr.showSuccess(response.message);
                        setTimeout(() => {
                            window.location.href = '{{ route('master_data.role.index') }}';
                        }, 1000);
                    },
                    error: function(xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            Toastr.showError(xhr.responseJSON.message);
                        } else {
                            Toastr.showError('Terjadi kesalahan');
                        }
                    }
                });
            });

            // Initialize counts
            updatePermissionCounts();
        });
    </script>
@endsection
