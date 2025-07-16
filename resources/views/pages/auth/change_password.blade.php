@extends('layouts.default')
@section('page_title')
    Change Password
@stop
@section('content')
    <div class="card mb-6">
        <h5 class="card-header">Change Password</h5>
        <form class="card-body" id="changePasswordForm">
            @csrf
            <div class="row g-3">
                <div class="col-md-12">
                    <div class="section-header mt-12">
                        <h6>Password Information</h6>
                        <hr class="mx-n6">
                    </div>
                    
                    <div class="row g-3 mb-4">
                        <!-- Left Side: Input Fields -->
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label">Current Password <span style="color: red;">*</span></label>
                                <div class="input-group input-group-merge">
                                    <input type="password" class="form-control" id="current-password" name="current_password" 
                                           placeholder="Enter your current password" required>
                                    <span class="input-group-text cursor-pointer" onclick="togglePassword('current-password')">
                                        <i class="icon-base ti tabler-eye-off"></i>
                                    </span>
                                </div>
                                <div class="invalid-feedback" id="current-password-error"></div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">New Password <span style="color: red;">*</span></label>
                                <div class="input-group input-group-merge">
                                    <input type="password" class="form-control" id="new-password" name="new_password" 
                                           placeholder="Enter new password" required>
                                    <span class="input-group-text cursor-pointer" onclick="togglePassword('new-password')">
                                        <i class="icon-base ti tabler-eye-off"></i>
                                    </span>
                                </div>
                                <div class="invalid-feedback" id="new-password-error"></div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Confirm New Password <span style="color: red;">*</span></label>
                                <div class="input-group input-group-merge">
                                    <input type="password" class="form-control" id="confirm-password" name="confirm_password" 
                                           placeholder="Confirm new password" required>
                                    <span class="input-group-text cursor-pointer" onclick="togglePassword('confirm-password')">
                                        <i class="icon-base ti tabler-eye-off"></i>
                                    </span>
                                </div>
                                <div class="invalid-feedback" id="confirm-password-error"></div>
                            </div>
                        </div>
                        
                        <!-- Right Side: Validation Information -->
                        <div class="col-md-6">
                            <label class="form-label">Password Requirements</label>
                            <div class="password-requirements">
                                <div class="requirement-item" id="req-length">
                                    <i class="icon-base ti tabler-circle-x text-danger me-2"></i>
                                    <span>At least 8 characters</span>
                                </div>
                                <div class="requirement-item" id="req-uppercase">
                                    <i class="icon-base ti tabler-circle-x text-danger me-2"></i>
                                    <span>One uppercase letter</span>
                                </div>
                                <div class="requirement-item" id="req-lowercase">
                                    <i class="icon-base ti tabler-circle-x text-danger me-2"></i>
                                    <span>One lowercase letter</span>
                                </div>
                                <div class="requirement-item" id="req-number">
                                    <i class="icon-base ti tabler-circle-x text-danger me-2"></i>
                                    <span>One number</span>
                                </div>
                                <div class="requirement-item" id="req-symbol">
                                    <i class="icon-base ti tabler-circle-x text-danger me-2"></i>
                                    <span>One symbol</span>
                                </div>
                            </div>
                            
                            <div class="password-match mt-4">
                                <div class="requirement-item" id="req-match" style="display: none;">
                                    <i class="icon-base ti tabler-circle-x text-danger me-2"></i>
                                    <span>Passwords match</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="pt-6 text-end">
                <button type="button" class="btn btn-label-secondary me-3" onclick="window.history.back()">Cancel</button>
                <button type="submit" class="btn btn-primary" id="submit-btn">Change Password</button>
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
    
    .input-group-text {
        cursor: pointer;
        user-select: none;
    }
    
    .form-text.text-muted {
        font-size: 0.875rem;
    }
    
    .is-invalid {
        border-color: #dc3545;
    }
    
    .invalid-feedback {
        display: block;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875rem;
        color: #dc3545;
    }
    
    .password-requirements {
        margin-top: 0.5rem;
        padding: 0.75rem;
        border: 1px solid #e9ecef;
        border-radius: 0.375rem;
        background-color: #f8f9fa;
    }
    
    .requirement-item {
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }
    
    .requirement-item:last-child {
        margin-bottom: 0;
    }
    
    .requirement-item.valid i {
        color: #28a745 !important;
    }
    
    .requirement-item.valid span {
        color: #28a745;
        font-weight: 500;
    }
    
    .requirement-item.valid {
        background-color: #d4edda;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        border: 1px solid #c3e6cb;
    }
    
    .password-match {
        padding-left: 0.75rem;
    }
</style>
@endsection

@section('js_script')
<script>
    $(function() {
        // Toggle password visibility
        window.togglePassword = function(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = field.nextElementSibling.querySelector('i');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('tabler-eye-off');
                icon.classList.add('tabler-eye');
            } else {
                field.type = 'password';
                icon.classList.remove('tabler-eye');
                icon.classList.add('tabler-eye-off');
            }
        };
        
        // Clear validation errors on input
        $('#current-password, #new-password, #confirm-password').on('input', function() {
            $(this).removeClass('is-invalid');
            $(`#${this.id}-error`).text('');
        });
        
        // Real-time password validation
        $('#new-password').on('input', function() {
            const password = $(this).val();
            validatePasswordRequirements(password);
        });
        
        // Real-time password match validation
        $('#confirm-password').on('input', function() {
            const newPassword = $('#new-password').val();
            const confirmPassword = $(this).val();
            
            if (confirmPassword.length > 0) {
                $('#req-match').show();
                validatePasswordMatch(newPassword, confirmPassword);
            } else {
                $('#req-match').hide();
            }
        });
        
        // Also check match when new password changes
        $('#new-password').on('input', function() {
            const newPassword = $(this).val();
            const confirmPassword = $('#confirm-password').val();
            
            if (confirmPassword.length > 0) {
                validatePasswordMatch(newPassword, confirmPassword);
            }
        });
        
        // Password validation functions
        function validatePasswordRequirements(password) {
            // Length requirement
            if (password.length >= 8) {
                setRequirementValid('req-length', true);
            } else {
                setRequirementValid('req-length', false);
            }
            
            // Uppercase requirement
            if (/[A-Z]/.test(password)) {
                setRequirementValid('req-uppercase', true);
            } else {
                setRequirementValid('req-uppercase', false);
            }
            
            // Lowercase requirement
            if (/[a-z]/.test(password)) {
                setRequirementValid('req-lowercase', true);
            } else {
                setRequirementValid('req-lowercase', false);
            }
            
            // Number requirement
            if (/\d/.test(password)) {
                setRequirementValid('req-number', true);
            } else {
                setRequirementValid('req-number', false);
            }
            
            // Symbol requirement
            if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
                setRequirementValid('req-symbol', true);
            } else {
                setRequirementValid('req-symbol', false);
            }
        }
        
        function validatePasswordMatch(newPassword, confirmPassword) {
            if (newPassword === confirmPassword && newPassword.length > 0) {
                setRequirementValid('req-match', true);
            } else {
                setRequirementValid('req-match', false);
            }
        }
        
        function setRequirementValid(requirementId, isValid) {
            const element = $('#' + requirementId);
            const icon = element.find('i');
            
            if (isValid) {
                element.addClass('valid');
                icon.removeClass('tabler-circle-x').addClass('tabler-circle-check');
                icon.removeClass('text-danger').addClass('text-success');
            } else {
                element.removeClass('valid');
                icon.removeClass('tabler-circle-check').addClass('tabler-circle-x');
                icon.removeClass('text-success').addClass('text-danger');
            }
        }
        
        // Form submission
        $('#changePasswordForm').on('submit', function(e) {
            e.preventDefault();
            
            // Clear previous errors
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            
            const formData = {
                current_password: $('#current-password').val(),
                new_password: $('#new-password').val(),
                confirm_password: $('#confirm-password').val(),
                _token: $('input[name="_token"]').val()
            };
            
            // Disable submit button
            $('#submit-btn').prop('disabled', true).text('Changing Password...');
            
            $.ajax({
                url: '{{ route("change_password.update") }}',
                method: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            customClass: {
                                confirmButton: 'btn btn-success'
                            },
                            buttonsStyling: false
                        }).then(function() {
                            // Reset form
                            $('#changePasswordForm')[0].reset();
                        });
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        
                        // Display validation errors
                        $.each(errors, function(field, messages) {
                            const fieldId = field.replace('_', '-');
                            $(`#${fieldId}`).addClass('is-invalid');
                            $(`#${fieldId}-error`).text(messages[0]);
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: xhr.responseJSON?.message || 'Something went wrong. Please try again.',
                            customClass: {
                                confirmButton: 'btn btn-danger'
                            },
                            buttonsStyling: false
                        });
                    }
                },
                complete: function() {
                    // Re-enable submit button
                    $('#submit-btn').prop('disabled', false).text('Change Password');
                }
            });
        });
    });
</script>
@endsection