@extends('layouts.default')
@section('page_title')
    Detail Tag
@stop
@section('content')
    <div class="card mb-6">
        <div class="card-body">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <h4 class="mb-3">Detail Tag</h4>
                </div>
                
                <!-- Action Panel -->
                <div class="col-md-4">
                    <div class="text-end">
                        @php
                            $totalActions = 1; // Edit always present
                            if($tag['usage_count'] == 0) {
                                $totalActions++; // Add delete action when usage is 0
                            }
                        @endphp
                        
                        @if($totalActions < 3)
                            <!-- Show buttons directly when less than 3 actions -->
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="{{ route('master_data.tag.edit', $tag['id']) }}" class="btn btn-primary btn-md waves-effect waves-light">
                                    <i class="icon-base ti tabler-edit me-2"></i>Edit
                                </a>
                                @if($tag['usage_count'] == 0)
                                    <button type="button" class="btn btn-danger btn-md waves-effect waves-light" id="delete-tag-btn">
                                        <i class="icon-base ti tabler-trash me-2"></i>Hapus
                                    </button>
                                @endif
                            </div>
                        @else
                            <!-- Show dropdown for 3 or more actions -->
                            <div class="dropdown">
                                <button type="button" class="btn btn-primary btn-md waves-effect waves-light dropdown-toggle" data-bs-toggle="dropdown">
                                    Aksi
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('master_data.tag.edit', $tag['id']) }}">
                                        <i class="icon-base ti tabler-edit me-2"></i>Edit</a></li>
                                    @if($tag['usage_count'] == 0)
                                        <li><a class="dropdown-item text-danger" href="javascript:void(0);" id="delete-tag-btn">
                                            <i class="icon-base ti tabler-trash me-2"></i>Hapus</a></li>
                                    @endif
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Tag Title -->
            <h2>{{ $tag['code'] }} - {{ $tag['name'] }}</h2>
            <span>
                <h6>Data di bawah berdasarkan tanggal {{ date('Y-m-d H:i:s') }}</h6>
            </span>
            <hr class="my-6 mx-n6" />

            <div class="row g-6">
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <div class="form-control-plaintext">
                        <span class="badge {{ $tag['status'] === 'ACTIVE' ? 'bg-label-success' : 'bg-label-secondary' }}">
                            {{ $tag['status'] }}
                        </span>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Total Penggunaan</label>
                    <input type="text" class="form-control text-center fw-bold" value="{{ $tag['usage_count'] }}" disabled />
                </div>
            </div>
            <hr class="my-6 mx-n6" />
            
            <div class="col-lg-12">
                <div class="demo-inline-spacing mt-4">
                    <div class="list-group list-group-horizontal-md text-md-center" role="tablist">
                        <a class="list-group-item list-group-item-action waves-effect active" id="tag-info-list-item"
                            data-bs-toggle="list" href="#tag-info-tab" aria-selected="true" role="tab">
                            <i class="icon-base ti tabler-info-circle me-2"></i>
                            <b>Informasi Tag</b>
                        </a>
                        <a class="list-group-item list-group-item-action waves-effect" id="tag-usage-list-item"
                            data-bs-toggle="list" href="#tag-usage-tab" aria-selected="false" role="tab">
                            <i class="icon-base ti tabler-chart-line me-2"></i>
                            <b>Penggunaan Tag</b>
                        </a>
                        <a class="list-group-item list-group-item-action waves-effect" id="audit-log-list-item"
                            data-bs-toggle="list" href="#audit-log-tab" aria-selected="false" role="tab">
                            <i class="icon-base ti tabler-history me-2"></i>
                            <b>Audit Log</b>
                        </a>
                    </div>
                    <div class="tab-content px-0 mt-0">
                        <!-- Tag Information Tab -->
                        <div class="tab-pane fade show active" id="tag-info-tab" role="tabpanel">
                            <!-- Info Tag Section -->
                            <div class="section-header mt-6">
                                <h6>Informasi Tag</h6>
                            </div>
                            
                            <!-- Row 1: Basic Information -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Kode Tag</label>
                                    <input type="text" class="form-control" value="{{ $tag['code'] }}" disabled />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nama Tag</label>
                                    <input type="text" class="form-control" value="{{ $tag['name'] }}" disabled />
                                </div>
                            </div>
                            
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Warna Badge</label>
                                    <div class="form-control-plaintext">
                                        <span class="badge bg-label-{{ $tag['color'] }} me-2">{{ $tag['code'] }}</span>
                                        <small class="text-muted">({{ ucfirst($tag['color']) }})</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <!-- Empty column for alignment -->
                                </div>
                            </div>
                            
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Deskripsi</label>
                                    <textarea class="form-control" rows="3" disabled>{{ $tag['description'] }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Memo</label>
                                    <textarea class="form-control" rows="3" disabled>{{ $tag['memo'] ?? '-' }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Tag Usage Tab -->
                        <div class="tab-pane fade" id="tag-usage-tab" role="tabpanel">
                            <div class="section-header mt-6">
                                <h6>Transaksi Terbaru Menggunakan Tag Ini</h6>
                            </div>
                            <div class="mx-n6">
                                <table class="table table-bordered table-sm" id="tag-usage-table">
                                    <thead>
                                        <tr>
                                            <th>ID Transaksi</th>
                                            <th>Modul</th>
                                            <th>Deskripsi</th>
                                            <th>Nominal</th>
                                            <th>Tanggal</th>
                                            <th>Dibuat Oleh</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tag['recent_transactions'] as $transaction)
                                        <tr>
                                            <td><a href="#" class="text-primary fw-semibold">{{ $transaction['id'] }}</a></td>
                                            <td><span class="badge bg-label-info">{{ $transaction['module'] }}</span></td>
                                            <td>{{ $transaction['description'] }}</td>
                                            <td class="text-end">{{ $transaction['amount'] }}</td>
                                            <td>{{ $transaction['date'] }}</td>
                                            <td>{{ $transaction['created_by'] }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Audit Log Tab -->
                        <div class="tab-pane fade" id="audit-log-tab" role="tabpanel">
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
                                        @foreach($tag['audit_logs'] as $log)
                                        <tr>
                                            <td>{{ $log['created_at'] }}</td>
                                            <td><a href="{{ route('master_data.user.detail', ['id' => $log['user_id']]) }}" class="text-primary fw-semibold">{{ $log['user_name'] }}</a></td>
                                            <td><span class="badge bg-label-{{ $log['action_type'] === 'CREATE' ? 'success' : ($log['action_type'] === 'UPDATE' ? 'warning' : 'info') }}">{{ $log['action_type'] }}</span></td>
                                            <td>{{ $log['description'] }}</td>
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
        // Initialize DataTables for tag usage and audit log
        $('#tag-usage-table').DataTable({
            responsive: true,
            orderCellsTop: true,
            searching: false,
            paging: false,
            info: false,
            columnDefs: [
                {
                    targets: [3], // Amount column
                    className: 'text-end'
                }
            ],
            language: {
                paginate: {
                    next: '<i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>',
                    previous: '<i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>',
                    first: '<i class="icon-base ti tabler-chevrons-left scaleX-n1-rtl icon-18px"></i>',
                    last: '<i class="icon-base ti tabler-chevrons-right scaleX-n1-rtl icon-18px"></i>'
                }
            }
        });

        $('#audit-log-table').DataTable({
            responsive: true,
            orderCellsTop: true,
            searching: false,
            paging: false,
            info: false,
            language: {
                paginate: {
                    next: '<i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>',
                    previous: '<i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>',
                    first: '<i class="icon-base ti tabler-chevrons-left scaleX-n1-rtl icon-18px"></i>',
                    last: '<i class="icon-base ti tabler-chevrons-right scaleX-n1-rtl icon-18px"></i>'
                }
            }
        });

        // Delete tag functionality
        $('#delete-tag-btn').click(function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: 'Hapus Tag?',
                text: 'Data tag akan dihapus permanen dan tidak dapat dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-label-secondary'
                },
                buttonsStyling: false
            }).then(function(result) {
                if (result.value) {
                    // TODO: Submit delete request to server
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Tag berhasil dihapus.',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        },
                        buttonsStyling: false
                    }).then(function() {
                        window.location.href = "{{ route('master_data.tag.index') }}";
                    });
                }
            });
        });
    });
</script>
@endsection