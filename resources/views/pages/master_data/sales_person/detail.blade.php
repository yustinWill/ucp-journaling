@extends('layouts.default')
@section('page_title')
    Informasi Sales Person - {{ $salesPerson['employee_name'] }}
@stop
@section('content')
    <div class="card mb-6">
        <div class="card-body">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <h4 class="mb-3">Detail Sales Person</h4>
                </div>
                
                <!-- Action Panel -->
                <div class="col-md-4">
                    <div class="text-end">
                        <div class="d-flex gap-2 justify-content-end">
                            @if($canShowEditButton)
                                <a href="{{ route('master_data.sales_person.edit', $salesPerson['id']) }}" class="btn btn-primary btn-md waves-effect waves-light">
                                    <i class="icon-base ti tabler-edit me-2"></i>
                                    Edit
                                </a>
                            @endif
                            @if($canShowDeleteButton)
                                <button class="btn btn-danger btn-md waves-effect waves-light" onclick="deleteItem({{ $salesPerson['id'] }})">
                                    <i class="icon-base ti tabler-trash me-2"></i>
                                    Hapus
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Entity Title -->
            <h2>{{ $salesPerson['employee_name'] }}</h2>
            <span>
                <h6>Data di bawah berdasarkan tanggal {{ date('Y-m-d H:i:s') }}</h6>
            </span>
            <hr class="my-6 mx-n6" />

            <!-- Top-level Information (col-6 Maximum) -->
            <div class="row g-6">
                <div class="col-md-6">
                    <label class="form-label">Kode Sales Person</label>
                    <input type="text" class="form-control" value="{{ $salesPerson['code'] }}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama Karyawan</label>
                    <input type="text" class="form-control" value="{{ $salesPerson['employee_name'] }}" disabled />
                </div>
            </div>
            
            <div class="row g-6 mt-3">
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <div class="form-control-plaintext">
                        @if($salesPerson['status'] == 'ACTIVE')
                            <span class="badge bg-label-success">ACTIVE</span>
                        @else
                            <span class="badge bg-label-secondary">INACTIVE</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Komisi (%)</label>
                    <input type="text" class="form-control" value="{{ number_format($salesPerson['commission_rate'], 2) }}%" disabled />
                </div>
            </div>
            <hr class="my-6 mx-n6" />

            <!-- Tab System with Section Headers -->
            <div class="col-lg-12">
                <div class="demo-inline-spacing mt-4">
                    <div class="list-group list-group-horizontal-md text-md-center" role="tablist">
                        <a class="list-group-item list-group-item-action waves-effect active" 
                            data-bs-toggle="list" href="#horizontal-info" role="tab">
                            <i class="icon-base ti tabler-info-circle me-2"></i>
                            <b>Informasi Detail</b>
                        </a>
                        <a class="list-group-item list-group-item-action waves-effect"
                            data-bs-toggle="list" href="#horizontal-audit-log" role="tab">
                            <i class="icon-base ti tabler-history me-2"></i>
                            <b>Audit Log</b>
                        </a>
                    </div>
                    <div class="tab-content px-0 mt-0">
                        <!-- Informasi Detail Tab -->
                        <div class="tab-pane fade show active" id="horizontal-info" role="tabpanel">
                            <div class="section-header mt-6">
                                <h6>Detail Informasi Sales Person</h6>
                                <hr class="mx-n6">
                            </div>
                            
                            <div class="row g-6">
                                <div class="col-md-6">
                                    <label class="form-label">Keterangan</label>
                                    <textarea class="form-control" rows="3" disabled>{{ $salesPerson['description'] ?: '-' }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <!-- Empty for alignment -->
                                </div>
                            </div>
                        </div>
                        
                        <!-- Audit Log Tab -->
                        <div class="tab-pane fade" id="horizontal-audit-log" role="tabpanel">
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
                                        @foreach($auditLogs as $log)
                                        <tr>
                                            <td>{{ $log->created_at }}</td>
                                            <td>
                                                <a href="{{ route('master_data.user.detail', ['id' => $log->user_id]) }}" 
                                                   class="text-primary fw-semibold">{{ $log->user_name }}</a>
                                            </td>
                                            <td>
                                                @if($log->action_type == 'CREATE')
                                                    <span class="badge bg-label-success">CREATE</span>
                                                @elseif($log->action_type == 'UPDATE')
                                                    <span class="badge bg-label-warning">UPDATE</span>
                                                @elseif($log->action_type == 'DELETE')
                                                    <span class="badge bg-label-danger">DELETE</span>
                                                @else
                                                    <span class="badge bg-label-info">{{ $log->action_type }}</span>
                                                @endif
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
@stop

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
        order: [[0, 'desc']] // Sort by date descending
    });
});
</script>
@endsection