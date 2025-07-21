@extends('layouts.default')
@section('page_title')
    Edit User - {{ $user['name'] ?? 'User 1' }}
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Edit User</h4>
        <form class="card-body" id="userEditForm">
            <!-- Data User Section -->
            <div class="section-header mt-12">
                <h6>Data User</h6>
                <hr class="mx-n6">
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Nama User <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="user-name" value="{{ $user['name'] ?? 'User 1' }}" placeholder="Masukkan Nama User" maxlength="255" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email User <span style="color: red;">*</span></label>
                    <input type="email" class="form-control" id="user-email" value="{{ $user['email'] ?? 'user1@example.com' }}" placeholder="Masukkan Email User" maxlength="255" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nomor Telepon</label>
                    <input type="text" class="form-control" id="user-phone" value="{{ $user['phone'] ?? '08127213123' }}" placeholder="Masukkan Nomor Telepon" maxlength="20" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Role <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="user-role" data-allow-clear="false">
                        <option value="">Pilih Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role['value'] }}" @if(($user['role'] ?? '') == $role['value']) selected @endif>{{ $role['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status User</label>
                    <div class="demo-inline-spacing">
                        <label class="switch switch-primary">
                            <input type="checkbox" class="switch-input" id="user-status" @if(($user['status'] ?? 'ACTIVE') == 'ACTIVE') checked @endif>
                            <span class="switch-toggle-slider">
                                <span class="switch-on">
                                    <i class="icon-base ti tabler-check"></i>
                                </span>
                                <span class="switch-off">
                                    <i class="icon-base ti tabler-x"></i>
                                </span>
                            </span>
                            <span class="switch-label">Aktif</span>
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- Password Section -->
            <div class="section-header mt-12">
                <h6>Password</h6>
                <hr class="mx-n6">
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Password Baru</label>
                    <div class="input-group input-group-merge">
                        <input type="password" class="form-control" id="new-password" placeholder="Kosongkan jika tidak ingin mengubah" />
                        <span class="input-group-text cursor-pointer" onclick="togglePassword('new-password')">
                            <i class="icon-base ti tabler-eye-off"></i>
                        </span>
                    </div>
                    <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah password</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Konfirmasi Password Baru</label>
                    <div class="input-group input-group-merge">
                        <input type="password" class="form-control" id="confirm-password" placeholder="Konfirmasi Password Baru" />
                        <span class="input-group-text cursor-pointer" onclick="togglePassword('confirm-password')">
                            <i class="icon-base ti tabler-eye-off"></i>
                        </span>
                    </div>
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
@section('css_script')
<style>
    .section-header h6 {
        margin-bottom: 0;
        font-weight: 600;
    }
    
    .form-text.text-muted {
        font-size: 0.875rem;
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
        
        // Form validation
        function validateForm() {
            let isValid = true;
            const requiredFields = ['user-name', 'user-email', 'user-role'];
            
            requiredFields.forEach(function(fieldId) {
                const field = $('#' + fieldId);
                if (!field.val() || field.val().trim() === '') {
                    field.addClass('is-invalid');
                    isValid = false;
                } else {
                    field.removeClass('is-invalid');
                }
            });
            
            // Validate email format
            const email = $('#user-email').val();
            if (email && !isValidEmail(email)) {
                $('#user-email').addClass('is-invalid');
                isValid = false;
            }
            
            // Validate password match if provided
            const newPassword = $('#new-password').val();
            const confirmPassword = $('#confirm-password').val();
            if (newPassword && newPassword !== confirmPassword) {
                $('#confirm-password').addClass('is-invalid');
                isValid = false;
            }
            
            return isValid;
        }
        
        // Email validation helper
        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }
        
        // Submit button handler
        $('#submit-btn').click(function(e) {
            e.preventDefault();
            
            if (!validateForm()) {
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Validasi',
                    text: 'Mohon lengkapi semua field yang wajib diisi dengan benar.',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                });
                return;
            }
            
            Swal.fire({
                title: 'Perbarui User?',
                text: 'Apakah Anda yakin ingin memperbarui user ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Perbarui!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-label-secondary'
                },
                buttonsStyling: false
            }).then(function(result) {
                if (result.value) {
                    // TODO: Submit form data to server
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'User berhasil diperbarui.',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        },
                        buttonsStyling: false
                    }).then(function() {
                        // TODO: Navigate back to user list or detail
                        window.location.href = "{{ route('master_data.user.index') }}";
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
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-label-secondary'
                },
                buttonsStyling: false
            }).then(function(result) {
                if (result.value) {
                    window.location.href = "{{ route('master_data.user.index') }}";
                }
            });
        });
    });
</script>
@endsection