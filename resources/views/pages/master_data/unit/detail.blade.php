@extends('layouts.default')
@section('page_title')
    Detail Unit
@stop
@section('content')
    <div class="card mb-6">
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-8">
                    <h4 class="mb-3">Detail Unit</h4>
                </div>
                
                <!-- Action Panel -->
                <div class="col-md-4">
                    <div class="text-end">
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('master_data.unit.edit', $unit['id']) }}" class="btn btn-primary btn-md waves-effect waves-light">
                                <i class="icon-base ti tabler-edit me-2"></i>Edit
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <h5 class="text-primary mb-1">{{ $unit['unit_name'] }}</h5>
                    <p class="text-muted mb-1">{{ $unit['unit_code'] }} - {{ $unit['unit_symbol'] }}</p>
                    @if($unit['status'] == 'ACTIVE')
                        <span class="badge bg-label-success">Aktif</span>
                    @else
                        <span class="badge bg-label-secondary">Inaktif</span>
                    @endif
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-muted mb-1">Terakhir diperbarui</p>
                    <p class="fw-semibold">{{ $unit['last_updated'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="list-group list-group-horizontal-md text-md-center mb-4" role="tablist">
        <a class="list-group-item list-group-item-action waves-effect active" id="informasi-list-item"
            data-bs-toggle="list" href="#informasi-tab" aria-selected="true" role="tab" tabindex="-1">
            <i class="icon-base ti tabler-info-circle me-2"></i>
            <b>Informasi</b>
        </a>
        <a class="list-group-item list-group-item-action waves-effect" id="audit-list-item"
            data-bs-toggle="list" href="#audit-tab" aria-selected="false" role="tab" tabindex="-1">
            <i class="icon-base ti tabler-history me-2"></i>
            <b>Audit Log</b>
        </a>
    </div>
    
    <div class="tab-content px-0 mt-0">
        <!-- Informasi Tab -->
        <div class="tab-pane fade show active" id="informasi-tab" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    <form class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Kode Unit</label>
                            <input type="text" class="form-control" value="{{ $unit['unit_code'] }}" disabled />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Unit</label>
                            <input type="text" class="form-control" value="{{ $unit['unit_name'] }}" disabled />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Simbol Unit</label>
                            <input type="text" class="form-control" value="{{ $unit['unit_symbol'] }}" disabled />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tipe Unit</label>
                            <input type="text" class="form-control" value="{{ $unit['unit_type'] }}" disabled />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <input type="text" class="form-control" value="{{ $unit['status'] == 'ACTIVE' ? 'Aktif' : 'Inaktif' }}" disabled />
                        </div>
                        <div class="col-md-6">
                            <!-- Empty for alignment -->
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" rows="3" disabled>{{ $unit['description'] }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <!-- Empty for alignment -->
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Audit Log Tab -->
        <div class="tab-pane fade" id="audit-tab" role="tabpanel">
            <div class="card">
                <div class="section-header mt-6">
                    <h6>Log Aktivitas Unit</h6>
                </div>
                <div class="mx-n6">
                    <table id="audit-log-table" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Tanggal & Waktu</th>
                                <th>Pengguna</th>
                                <th>Aksi</th>
                                <th>Deskripsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($auditLogs as $log)
                            <tr>
                                <td>{{ $log['created_at'] }}</td>
                                <td>
                                    <a href="{{ route('master_data.user.detail', ['id' => $log['user_id']]) }}" class="text-primary fw-semibold">
                                        {{ $log['user_name'] }}
                                    </a>
                                </td>
                                <td>
                                    @if($log['action_type'] == 'CREATE')
                                        <span class="badge bg-label-success">Buat</span>
                                    @elseif($log['action_type'] == 'UPDATE')
                                        <span class="badge bg-label-warning">Ubah</span>
                                    @elseif($log['action_type'] == 'DELETE')
                                        <span class="badge bg-label-danger">Hapus</span>
                                    @else
                                        <span class="badge bg-label-info">{{ $log['action_type'] }}</span>
                                    @endif
                                </td>
                                <td>{{ $log['description'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js_script')
    <script>
        $(function() {
            // Initialize audit log table
            $('#audit-log-table').DataTable({
                responsive: true,
                orderCellsTop: true,
                searching: false,
                paging: false,
                info: false
            });
        });
    </script>
@endsection