@extends('layouts.default')
@section('page_title')
    Informasi User - {{ $user['name'] ?? 'User 1' }}
@stop
@section('content')
    <div class="card mb-6">
        <div class="card-body">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <h4 class="mb-3">Informasi User</h4>
                </div>
            </div>
            
            <!-- User Title -->
            <h2>{{ $user['name'] ?? 'User 1' }}</h2>
            <span>
                <h6>Data di bawah berdasarkan tanggal {{ date('Y-m-d H:i:s') }}</h6>
            </span>
            <hr class="my-6 mx-n6" />

            <div class="row g-6">
                <div class="col-md-6">
                    <label class="form-label">Role</label>
                    <input type="text" class="form-control" value="{{ $user['role'] ?? 'Super Admin' }}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <input type="text" class="form-control" value="{{ $user['status'] ?? 'Aktif' }}" disabled />
                </div>
            </div>
            <hr class="my-6 mx-n6" />
            
            <div class="col-lg-12">
                <div class="demo-inline-spacing mt-4">
                    <div class="list-group list-group-horizontal-md text-md-center" role="tablist">
                        <a class="list-group-item list-group-item-action waves-effect active" id="profil-list-item"
                            data-bs-toggle="list" href="#horizontal-profil" aria-selected="true" role="tab"
                            tabindex="-1"><b>Profil</b></a>
                        <a class="list-group-item list-group-item-action waves-effect" id="activity-list-item"
                            data-bs-toggle="list" href="#horizontal-activity" aria-selected="false" role="tab"
                            tabindex="-1"><b>Activity Log</b></a>
                    </div>
                    <div class="tab-content px-0 mt-0">
                        <div class="tab-pane fade show active" id="horizontal-profil" role="tabpanel"
                            aria-labelledby="profil-list-item">
                            <div id="profil-content">
                                <!-- Content will be loaded here by default -->
                            </div>
                        </div>
                        <div class="tab-pane fade" id="horizontal-activity" role="tabpanel"
                            aria-labelledby="activity-list-item">
                            <div id="activity-content">
                                <!-- Content will be loaded dynamically -->
                                <div class="text-center py-4">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('css_script')
<style>
    .contact-tabs .nav-link.active {
        background-color: #28a745;
        border-color: #28a745;
        color: white;
    }
    
    .contact-tabs .nav-link {
        background-color: #e9ecef;
        border-color: #e9ecef;
        color: #495057;
        margin-right: 2px;
    }
    
    .contact-tabs .nav-link:hover:not(.active) {
        background-color: #dee2e6;
        border-color: #dee2e6;
    }
    
    .readonly-field {
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        color: #6c757d;
    }
    
    .section-header h5 {
        margin-bottom: 0;
        font-weight: 600;
    }
    
    .summary-card {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 1rem;
        text-align: center;
    }
    
    .summary-card.danger {
        background-color: #dc3545;
        color: white;
    }
    
    .summary-card.success {
        background-color: #28a745;
        color: white;
    }
    
    .selected-row {
        background-color: #007bff !important;
        color: white;
    }
</style>
@endsection

@section('js_script')
<script>
$(function() {
    const userId = {{ $user['id'] ?? 1 }};
    
    // User data from server
    const userData = {
        name: "{{ $user['name'] ?? 'User 1' }}",
        email: "{{ $user['email'] ?? 'user1@example.com' }}",
        phone: "{{ $user['phone'] ?? '08127213123' }}",
        role: "{{ $user['role'] ?? 'Super Admin' }}",
        last_login: "{{ $user['last_login'] ?? '2025-06-01 12:00:00' }}",
        last_password_change: "{{ $user['last_password_change'] ?? '2025-06-01 12:00:00' }}",
        status: "{{ $user['status'] ?? 'ACTIVE' }}"
    };
    
    // Load profile content immediately
    loadProfilContent();
    
    // Tab switch event handlers
    $('#activity-list-item').on('shown.bs.tab', function () {
        loadActivityContent();
    });
    
    
    function loadProfilContent() {
        const profilContent = `
            <h6>1. Data User</h6>
            <div class="row g-6">
                <div class="col-md-6">
                    <label class="form-label">Nama User</label>
                    <input type="text" class="form-control" value="${userData.name}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email User</label>
                    <input type="text" class="form-control" value="${userData.email}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nomor Telepon</label>
                    <input type="text" class="form-control" value="${userData.phone}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Role</label>
                    <input type="text" class="form-control" value="${userData.role}" disabled />
                </div>
            </div>

            <hr class="my-6 mx-n6" />
            <h6>2. Informasi Login</h6>
            <div class="row g-6">
                <div class="col-md-6">
                    <label class="form-label">Terakhir Login</label>
                    <input type="text" class="form-control" value="${userData.last_login}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Password Terakhir Dirubah</label>
                    <input type="text" class="form-control" value="${userData.last_password_change}" disabled />
                </div>
            </div>

        `;
        
        $('#profil-content').html(profilContent);
    }
    
    function loadActivityContent() {
        const activityContent = `
            <h6>Activity Log</h6>
            <div class="card-datatable">
                <table id="activity-table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Aktivitas</th>
                            <th>IP Address</th>
                            <th>User Agent</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>2025-06-01 12:00:00</td>
                            <td>Login</td>
                            <td>192.168.1.1</td>
                            <td>Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36</td>
                            <td><span class="badge bg-success">SUCCESS</span></td>
                        </tr>
                        <tr>
                            <td>2025-06-01 11:30:00</td>
                            <td>Password Change</td>
                            <td>192.168.1.1</td>
                            <td>Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36</td>
                            <td><span class="badge bg-success">SUCCESS</span></td>
                        </tr>
                        <tr>
                            <td>2025-05-31 15:45:00</td>
                            <td>Login Failed</td>
                            <td>192.168.1.1</td>
                            <td>Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36</td>
                            <td><span class="badge bg-danger">FAILED</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        `;
        
        $('#activity-content').html(activityContent);
    }
    
    
    // Action functions
    window.editUser = function() {
        window.location.href = "{{ route('master_data.user.edit', $user['id'] ?? 1) }}";
    };
    
});
</script>
@endsection