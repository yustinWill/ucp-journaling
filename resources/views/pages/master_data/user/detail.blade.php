@extends('layouts.default')
@section('page_title')
    Informasi User - {{ $user->name }}
@stop
@section('content')
    <div class="card mb-6">
        <div class="card-body">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <h4 class="mb-3">Detail User</h4>
                </div>

                <!-- Action Panel -->
                <div class="col-md-4">
                    <div class="text-end">
                        @php
                            $totalActions = 1; // Edit always present
                        @endphp

                        @if ($totalActions < 3)
                            <!-- Show buttons directly when less than 3 actions -->
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="{{ route('master_data.user.edit', $user->id) }}"
                                    class="btn btn-primary btn-md waves-effect waves-light">
                                    <i class="icon-base ti tabler-edit me-2"></i>Edit
                                </a>
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
                                    <li><a class="dropdown-item" href="{{ route('master_data.user.edit', $user->id) }}">
                                            <i class="icon-base ti tabler-edit me-2"></i>Edit</a></li>
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- User Title -->
            <h2>{{ $user->name ?? 'User 1' }}</h2>
            <span>
                <h6>Data di bawah berdasarkan tanggal {{ date('Y-m-d H:i:s') }}</h6>
            </span>
            <hr class="my-6 mx-n6" />

            <!-- Key Information Summary (Non-duplicated) -->
            <div class="row g-6">
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <div class="form-control-plaintext">
                        <span
                            class="badge {{ ($user->status ?? 'ACTIVE') === 'ACTIVE' ? 'bg-label-success' : 'bg-label-secondary' }}">
                            {{ $user->status ?? 'ACTIVE' }}
                        </span>
                    </div>
                </div>
                <div class="col-md-6">
                    <!-- Empty column for layout balance -->
                </div>
            </div>
            <hr class="my-6 mx-n6" />

            <div class="col-lg-12">
                <div class="demo-inline-spacing mt-4">
                    <div class="list-group list-group-horizontal-md text-md-center" role="tablist">
                        <a class="list-group-item list-group-item-action waves-effect active" id="profil-list-item"
                            data-bs-toggle="list" href="#horizontal-profil" aria-selected="true" role="tab">
                            <i class="icon-base ti tabler-info-circle me-2"></i>
                            <b>Informasi User</b>
                        </a>
                        <a class="list-group-item list-group-item-action waves-effect" id="activity-list-item"
                            data-bs-toggle="list" href="#horizontal-activity" aria-selected="false" role="tab">
                            <i class="icon-base ti tabler-history me-2"></i>
                            <b>Audit Log</b>
                        </a>
                    </div>
                    <div class="tab-content px-0 mt-0">
                        <div class="tab-pane fade show active" id="horizontal-profil" role="tabpanel"
                            aria-labelledby="profil-list-item">
                            <div class="section-header mt-6">
                                <h6>Data User</h6>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Nama User</label>
                                    <input type="text" class="form-control" value="{{ $user->name }}" disabled />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email User</label>
                                    <input type="text" class="form-control" value="{{ $user->email }}" disabled />
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Nomor Telepon</label>
                                    <input type="text" class="form-control" value="{{ $user->phone ?: '-' }}" disabled />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Role</label>
                                    <input type="text" class="form-control" value="{{ $user->role_name }}" disabled />
                                </div>
                            </div>

                            <div class="section-header mt-6">
                                <h6>Informasi Login</h6>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Terakhir Login</label>
                                    <input type="text" class="form-control" value="{{ $user->last_login ?: '-' }}"
                                        disabled />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Password Terakhir Dirubah</label>
                                    <input type="text" class="form-control"
                                        value="{{ $user->last_password_change ?: '-' }}" disabled />
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="horizontal-activity" role="tabpanel"
                            aria-labelledby="activity-list-item">
                            <div class="section-header mt-6">
                                <h6>Riwayat Perubahan</h6>
                            </div>
                            <div class="mx-n6">
                                <table class="table table-bordered" id="audit-log-table">
                                    <thead>
                                        <tr>
                                            <th>Tanggal & Waktu</th>
                                            <th>Pengguna</th>
                                            <th>Aksi</th>
                                            <th>Deskripsi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($auditLogs as $log)
                                            <tr>
                                                <td>{{ $log->created_at }}</td>
                                                <td><a href="{{ route('master_data.user.detail', ['id' => $log->user_id]) }}"
                                                        class="text-primary fw-semibold">{{ $log->user_name }}</a></td>
                                                <td><span
                                                        class="badge bg-label-{{ $log->action_type === 'CREATE' ? 'success' : ($log->action_type === 'UPDATE' ? 'warning' : 'info') }}">{{ $log->action_type }}</span>
                                                </td>
                                                <td>{{ $log->description }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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
        });
    </script>
@endsection
