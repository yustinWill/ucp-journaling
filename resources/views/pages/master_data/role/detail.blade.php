@extends('layouts.default')
@section('page_title')
    Role Details - {{ $role['name'] }}
@stop
@section('content')
    <div class="card mb-6">
        <div class="card-body">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <h4 class="mb-3">Role Details</h4>
                </div>
                <div class="col-md-4 text-end">
                    <a href="{{ route('master_data.role.edit', $role['id']) }}" class="btn btn-primary me-2">
                        <i class="icon-base ti tabler-edit me-1"></i>
                        Edit Role
                    </a>
                    <a href="{{ route('master_data.role.index') }}" class="btn btn-secondary">
                        <i class="icon-base ti tabler-arrow-left me-1"></i>
                        Back to List
                    </a>
                </div>
            </div>

            <!-- Role Title -->
            <h2>{{ $role['name'] }}</h2>
            <span>
                <h6>Data below is based on {{ date('Y-m-d H:i:s') }}</h6>
            </span>
            <hr class="my-6 mx-n6" />

            <!-- Basic Information -->
            <div class="row g-6">
                <div class="col-md-6">
                    <label class="form-label">Role Name</label>
                    <input type="text" class="form-control" value="{{ $role['name'] }}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <input type="text" class="form-control" value="{{ $role['is_active'] ? 'Active' : 'Inactive' }}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Total Users</label>
                    <input type="text" class="form-control" value="{{ $role['user_count'] }} users" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Total Permissions</label>
                    <input type="text" class="form-control" value="{{ $role['permissions_count'] }} permissions" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Created At</label>
                    <input type="text" class="form-control" value="{{ $role['created_at'] }}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Last Updated</label>
                    <input type="text" class="form-control" value="{{ $role['updated_at'] }}" disabled />
                </div>
            </div>

            <div class="row g-6">
                <div class="col-md-12">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" disabled>{{ $role['description'] }}</textarea>
                </div>
            </div>
            <hr class="my-6 mx-n6" />

            <!-- Tab System -->
            <div class="col-lg-12">
                <div class="demo-inline-spacing mt-4">
                    <div class="list-group list-group-horizontal-md text-md-center" role="tablist">
                        <a class="list-group-item list-group-item-action waves-effect active" id="permissions-list-item"
                            data-bs-toggle="list" href="#horizontal-permissions" aria-selected="true" role="tab">
                            <i class="icon-base ti tabler-shield-check me-2"></i>
                            <b>Permissions</b>
                        </a>
                        <a class="list-group-item list-group-item-action waves-effect" id="users-list-item"
                            data-bs-toggle="list" href="#horizontal-users" aria-selected="false" role="tab">
                            <i class="icon-base ti tabler-users me-2"></i>
                            <b>Users</b>
                        </a>
                    </div>
                    <div class="tab-content px-0 mt-0">
                        <div class="tab-pane fade show active" id="horizontal-permissions" role="tabpanel">
                            <div id="permissions-content">
                                <!-- Content will be loaded here -->
                            </div>
                        </div>
                        <div class="tab-pane fade" id="horizontal-users" role="tabpanel">
                            <div id="users-content">
                                <!-- Content will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js_script')
<script>
    $(function() {
        // Load permissions content on page load
        loadPermissionsContent();

        // Tab event handlers
        $('#permissions-list-item').on('shown.bs.tab', function () {
            loadPermissionsContent();
        });

        $('#users-list-item').on('shown.bs.tab', function () {
            loadUsersContent();
        });

        // Load permissions content
        function loadPermissionsContent() {
            $.get('{{ route('master_data.role.permissions', $role['id']) }}', function(data) {
                let content = '';
                
                Object.keys(data).forEach(function(moduleName) {
                    content += `
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="icon-base ti tabler-folder me-2"></i>
                                    ${moduleName}
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                    `;
                    
                    data[moduleName].forEach(function(permission) {
                        const badgeClass = permission.granted ? 'bg-success' : 'bg-secondary';
                        const iconClass = permission.type === 'read' ? 'tabler-eye' : 
                                        permission.type === 'write' ? 'tabler-edit' : 
                                        permission.type === 'delete' ? 'tabler-trash' : 'tabler-check';
                        
                        content += `
                            <div class="col-md-3">
                                <span class="badge ${badgeClass} d-flex align-items-center">
                                    <i class="icon-base ti ${iconClass} me-1"></i>
                                    ${permission.name}
                                </span>
                            </div>
                        `;
                    });
                    
                    content += `
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                $('#permissions-content').html(content);
            }).fail(function() {
                $('#permissions-content').html('<div class="alert alert-danger">Error loading permissions data</div>');
            });
        }

        // Load users content
        function loadUsersContent() {
            $.get('{{ route('master_data.role.users', $role['id']) }}', function(data) {
                let content = `
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Users with this Role</h6>
                            <span class="badge bg-primary">${data.length} users</span>
                        </div>
                        <div class="card-body">
                `;
                
                if (data.length > 0) {
                    content += `
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;
                    
                    data.forEach(function(user) {
                        content += `
                            <tr>
                                <td>${user.name}</td>
                                <td>${user.email}</td>
                                <td>
                                    <a href="/master_data/user/detail/${user.id}" class="btn btn-sm btn-primary">
                                        <i class="icon-base ti tabler-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        `;
                    });
                    
                    content += `
                                </tbody>
                            </table>
                        </div>
                    `;
                } else {
                    content += `
                        <div class="text-center py-4">
                            <i class="icon-base ti tabler-users-off" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="text-muted mt-2">No users assigned to this role</p>
                        </div>
                    `;
                }
                
                content += `
                        </div>
                    </div>
                `;
                
                $('#users-content').html(content);
            }).fail(function() {
                $('#users-content').html('<div class="alert alert-danger">Error loading users data</div>');
            });
        }
    });
</script>
@endsection