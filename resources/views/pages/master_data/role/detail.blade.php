@extends('layouts.default')
@section('page_title')
    Detail Role - {{ $role->name }}
@stop
@section('content')
    <div class="card mb-6">
        <div class="card-body">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <h4 class="mb-3">Detail Role</h4>
                </div>
                <!-- Action Panel -->
                <div class="col-md-4">
                    <div class="text-end">
                        @php
                            $totalActions = 1; // Edit always present
                            if ($role->user_count == 0) {
                                $totalActions++; // Add delete action when no users
                            }
                        @endphp

                        @if ($totalActions < 3)
                            <!-- Show buttons directly when less than 3 actions -->
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="{{ route('master_data.role.edit', $role->id) }}"
                                    class="btn btn-primary btn-md waves-effect waves-light">
                                    <i class="icon-base ti tabler-edit me-2"></i>Edit
                                </a>
                                {{-- @if ($role->user_count == 0)
                                    <button type="button" class="btn btn-danger btn-md waves-effect waves-light"
                                        id="delete-role-btn">
                                        <i class="icon-base ti tabler-trash me-2"></i>Hapus
                                    </button>
                                @endif --}}
                            </div>
                        @else
                            <!-- Show dropdown for 3 or more actions -->
                            <div class="dropdown">
                                <button type="button"
                                    class="btn btn-primary btn-md waves-effect waves-light dropdown-toggle"
                                    data-bs-toggle="dropdown">
                                    Aksi
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('master_data.role.edit', $role->id) }}">
                                            <i class="icon-base ti tabler-edit me-2"></i>Edit</a></li>
                                    @if ($role->user_count == 0)
                                        <li><a class="dropdown-item text-danger" href="javascript:void(0);"
                                                id="delete-role-btn">
                                                <i class="icon-base ti tabler-trash me-2"></i>Hapus</a></li>
                                    @endif
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Role Title -->
            <h2>{{ $role->name }}</h2>
            <span>
                <h6>Data di bawah berdasarkan tanggal {{ date('Y-m-d H:i:s') }}</h6>
            </span>
            <hr class="my-6 mx-n6" />

            <!-- Role Information Section -->
            <div class="section-header mt-12">
                <h6>Informasi Role</h6>
                <hr class="mx-n6">
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <input type="text" class="form-control" value="Aktif" disabled />
                </div>
                <div class="col-md-6">
                    <!-- Empty column for layout balance -->
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Total Pengguna</label>
                    <input type="text" class="form-control" value="{{ $role->user_count }} pengguna" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Total Hak Akses</label>
                    <input type="text" class="form-control" value="{{ $role->permission_count }} hak akses" disabled />
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Deskripsi</label>
                    <textarea class="form-control" rows="3" disabled>{{ $role->description ?? 'Tidak ada deskripsi' }}</textarea>
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

            <div class="row g-3 mb-12">
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

            <!-- Tab System -->
            <div class="col-lg-12">
                <div class="demo-inline-spacing mt-4">
                    <div class="list-group list-group-horizontal-md text-md-center" role="tablist">
                        <a class="list-group-item list-group-item-action waves-effect active" id="permissions-list-item"
                            data-bs-toggle="list" href="#horizontal-permissions" aria-selected="true" role="tab">
                            <i class="icon-base ti tabler-shield-check me-2"></i>
                            <b>Hak Akses</b>
                        </a>
                        <a class="list-group-item list-group-item-action waves-effect" id="users-list-item"
                            data-bs-toggle="list" href="#horizontal-users" aria-selected="false" role="tab">
                            <i class="icon-base ti tabler-users me-2"></i>
                            <b>Pengguna</b>
                        </a>
                        {{-- <a class="list-group-item list-group-item-action waves-effect" id="audit-list-item"
                            data-bs-toggle="list" href="#horizontal-audit" aria-selected="false" role="tab">
                            <i class="icon-base ti tabler-history me-2"></i>
                            <b>Audit Log</b>
                        </a> --}}
                    </div>
                    <div class="tab-content px-0 mt-0">
                        <!-- Permissions Tab -->
                        <div class="tab-pane fade show active" id="horizontal-permissions" role="tabpanel">
                            <div class="section-header mt-6">
                                <h6>Hak Akses Role</h6>
                            </div>
                            <div id="permissions-content">
                                <!-- Content will be loaded here -->
                            </div>
                        </div>

                        <!-- Users Tab -->
                        <div class="tab-pane fade" id="horizontal-users" role="tabpanel">
                            <div class="section-header mt-6">
                                <h6>Pengguna dengan Role Ini</h6>
                            </div>
                            <div id="users-content">
                                <table id="users-log-table" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Nama</th>
                                            <th>Email</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($userList as $user)
                                            <tr>
                                                <td>{{ $user->name }}</td>
                                                <td>{{ $user->email }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Audit Log Tab -->
                        {{-- <div class="tab-pane fade" id="horizontal-audit" role="tabpanel">
                            <div class="section-header mt-6">
                                <h6>Log Audit Role</h6>
                            </div>
                            <div class="mx-n6">
                                <table id="audit-log-table" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Tanggal & Waktu</th>
                                            <th>Pengguna</th>
                                            <th>Aksi</th>
                                            <th>Deskripsi</th>
                                            <th>Lihat Detail</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($auditLogs as $log)
                                            <tr>
                                                <td>{{ $log->created_at }}</td>
                                                <td>
                                                    <a href="{{ route('master_data.user.detail', ['id' => $log->user_id]) }}"
                                                        class="text-primary fw-semibold">
                                                        {{ $log->user_name }}
                                                    </a>
                                                </td>
                                                <td>
                                                    <span class="badge bg-label-info">{{ $log->action_type }}</span>
                                                </td>
                                                <td>{{ $log->description }}</td>
                                                <td>
                                                    @if ($log->action_type == 'UPDATE')
                                                        <a target="_blank"
                                                            href="{{ route('audit_log.detail', ['id' => $log->id, 'audit_log_type' => $auditLogType]) }}"
                                                            class="btn btn-sm btn-primary waves-effect waves-light"
                                                            title="Detail">
                                                            <i class="icon-xs icon-base ti tabler-eye"></i>
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js_script')
    <script>
        $(function() {
            var roleId = parseInt('{{ $role->id }}');

            // Initialize DataTable for audit log
            $('#audit-log-table').DataTable({
                responsive: true,
                orderCellsTop: true,
                searching: false,
                paging: false,
                info: false,
                order: [
                    [0, 'desc']
                ] // Sort by date descending
            });

            // Load permissions content on page load
            loadPermissionsContent();

            // Tab event handlers
            $('#permissions-list-item').on('shown.bs.tab', function() {
                loadPermissionsContent();
            });

            // Update permission counts based on loaded data
            function updatePermissionCounts(permissionsData) {
                var readCount = 0,
                    writeCount = 0,
                    deleteCount = 0,
                    approvalCount = 0;

                // Check each module
                for (var moduleName in permissionsData) {
                    if (permissionsData.hasOwnProperty(moduleName)) {
                        permissionsData[moduleName].forEach(function(permission) {
                            if (permission.granted) {
                                var permType = permission.type.toLowerCase();
                                if (permType.includes('read') || permType.includes('view')) {
                                    readCount++;
                                } else if (permType.includes('write') || permType.includes('create') || permType.includes('edit')) {
                                    writeCount++;
                                } else if (permType.includes('delete')) {
                                    deleteCount++;
                                } else if (permType.includes('approval') || permType.includes('approve')) {
                                    approvalCount++;
                                }
                            }
                        });
                    }
                }

                $('#read-count').text(readCount);
                $('#write-count').text(writeCount);
                $('#delete-count').text(deleteCount);
                $('#approval-count').text(approvalCount);
            }

            // Load permissions content
            function loadPermissionsContent() {
                $.ajax({
                    url: '/master_data/role/permissions/' + roleId,
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        var content = '';

                        for (var moduleName in data) {
                            if (data.hasOwnProperty(moduleName)) {
                                content += '<div class="mb-4">';
                                content += '<div class="d-flex justify-content-between align-items-center mb-3">';
                                content += '<h6 class="mb-0">';
                                content += '<i class="icon-base ti tabler-folder me-2"></i>';
                                content += moduleName;
                                content += '</h6>';
                                content += '</div>';
                                content += '<div class="row g-3">';

                                data[moduleName].forEach(function(permission) {
                                    var badgeClass = permission.granted ? 'bg-label-success' : 'bg-label-secondary';
                                    var iconClass = permission.type === 'view' ? 'tabler-eye' :
                                        permission.type === 'edit' || permission.type === 'write' ? 'tabler-edit' :
                                        permission.type === 'delete' ? 'tabler-trash' :
                                        'tabler-check';

                                    content += '<div class="col-md-3">';
                                    content += '<span class="badge ' + badgeClass + ' d-flex align-items-center">';
                                    content += '<i class="icon-base ti ' + iconClass + ' me-1"></i>';
                                    content += permission.name;
                                    content += '</span>';
                                    content += '</div>';
                                });

                                content += '</div>';
                                var moduleNames = Object.keys(data);
                                if (moduleNames.indexOf(moduleName) < moduleNames.length - 1) {
                                    content += '<hr class="my-4">';
                                }
                                content += '</div>';
                            }
                        }

                        $('#permissions-content').html(content);
                        // Update counts after loading permissions
                        updatePermissionCounts(data);
                    },
                    error: function() {
                        $('#permissions-content').html(
                            '<div class="alert alert-danger">Error loading permissions data</div>'
                        );
                    }
                });
            }
        });
    </script>
@endsection
