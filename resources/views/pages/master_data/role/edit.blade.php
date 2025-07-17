@extends('layouts.default')
@section('page_title')
    Edit Role - {{ $role['name'] }}
@stop
@section('content')
    <div class="card mb-6">
        <h5 class="card-header">Edit Role</h5>
        <form class="card-body" id="roleEditForm">
            <!-- Role Information Section -->
            <div class="section-header mt-12">
                <h6>Role Information</h6>
                <hr class="mx-n6">
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Role Name <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="role-name" value="{{ $role['name'] }}" placeholder="Enter role name" maxlength="100" />
                    <small class="form-text text-muted">Maximum 100 characters</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <div class="demo-inline-spacing">
                        <label class="switch switch-primary">
                            <input type="checkbox" class="switch-input" id="role-status" @if($role['is_active']) checked @endif>
                            <span class="switch-toggle-slider">
                                <span class="switch-on">
                                    <i class="icon-base ti tabler-check"></i>
                                </span>
                                <span class="switch-off">
                                    <i class="icon-base ti tabler-x"></i>
                                </span>
                            </span>
                            <span class="switch-label">Active</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" id="role-description" rows="3" placeholder="Enter role description" maxlength="500">{{ $role['description'] }}</textarea>
                    <small class="form-text text-muted">Maximum 500 characters</small>
                </div>
            </div>

            <!-- Permissions Section -->
            <div class="section-header mt-12">
                <h6>Permissions</h6>
                <hr class="mx-n6">
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Module Permissions</h6>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-primary" id="selectAll">Select All</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAll">Deselect All</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="permissions-container">
                                @foreach($modules as $module)
                                <div class="module-permissions mb-4">
                                    <h6 class="mb-3">
                                        <i class="icon-base ti tabler-folder me-2"></i>
                                        {{ $module['name'] }}
                                    </h6>
                                    <div class="row g-3">
                                        @foreach($permission_types as $type)
                                        @php
                                            $permissionId = $module['id'] . '-' . $type['value'];
                                            $isChecked = false;
                                            
                                            // Check if this permission is in the role's permissions
                                            foreach($permissions as $permission) {
                                                if ($permission['module_id'] == $module['id'] && $permission['type'] == $type['value']) {
                                                    $isChecked = in_array($permission['id'], $role['permissions']);
                                                    break;
                                                }
                                            }
                                        @endphp
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input permission-checkbox" 
                                                       type="checkbox" 
                                                       id="permission-{{ $permissionId }}" 
                                                       data-module="{{ $module['id'] }}" 
                                                       data-type="{{ $type['value'] }}" 
                                                       value="{{ $permissionId }}"
                                                       @if($isChecked) checked @endif>
                                                <label class="form-check-label" for="permission-{{ $permissionId }}">
                                                    <i class="icon-base ti tabler-{{ $type['value'] == 'read' ? 'eye' : ($type['value'] == 'write' ? 'edit' : ($type['value'] == 'delete' ? 'trash' : 'check')) }} me-1"></i>
                                                    {{ $type['label'] }}
                                                </label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    <hr class="my-3">
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Permission Summary -->
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title">Permission Summary</h6>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h4 class="mb-0 text-success" id="read-count">0</h4>
                                        <small class="text-muted">Read</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h4 class="mb-0 text-primary" id="write-count">0</h4>
                                        <small class="text-muted">Write</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h4 class="mb-0 text-danger" id="delete-count">0</h4>
                                        <small class="text-muted">Delete</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h4 class="mb-0 text-warning" id="approval-count">0</h4>
                                        <small class="text-muted">Approval</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 text-end">
                <button type="button" class="btn btn-label-danger me-3" id="cancel-btn">Cancel</button>
                <button type="button" class="btn btn-success" id="submit-btn">Update Role</button>
            </div>
        </form>
    </div>
@endsection

@section('css_script')
<style>
    .section-header h6 {
        margin-bottom: 0;
        font-weight: 600;
    }
    
    .module-permissions {
        border: 1px solid #e9ecef;
        border-radius: 0.375rem;
        padding: 1rem;
        background-color: #f8f9fa;
    }
    
    .permission-checkbox:checked {
        background-color: #007bff;
        border-color: #007bff;
    }
    
    .form-check-label {
        font-weight: 500;
    }
    
    .form-text.text-muted {
        font-size: 0.875rem;
    }
</style>
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
                    title: 'Validation Error',
                    text: 'Please select at least one permission for this role.',
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
                is_active: $('#role-status').is(':checked'),
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
                title: 'Update Role?',
                text: 'Are you sure you want to update this role?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Update!',
                cancelButtonText: 'Cancel',
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
                        title: 'Success!',
                        text: 'Role has been updated successfully.',
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
                title: 'Cancel Changes?',
                text: 'All unsaved changes will be lost!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Cancel!',
                cancelButtonText: 'No, Continue',
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
                $(this).next('.form-text').html(`${remaining} characters remaining`);
                if (remaining < 0) {
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            } else {
                $(this).next('.form-text').html('Maximum 500 characters');
                $(this).removeClass('is-invalid');
            }
        });

        // Initialize permission counts
        updatePermissionCounts();
    });
</script>
@endsection