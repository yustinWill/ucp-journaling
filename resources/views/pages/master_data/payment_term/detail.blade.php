@extends('layouts.default')
@section('page_title')
    Informasi Syarat Pembayaran - {{ $paymentTerm->name }}
@stop
@section('content')
    <div class="card mb-6">
        <div class="card-body">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <h4 class="mb-3">Detail Syarat Pembayaran</h4>
                </div>
                
                <!-- Action Panel -->
                <div class="col-md-4">
                    <div class="text-end">
                        @php
                            $totalActions = 1; // Edit always present
                        @endphp
                        
                        @if($totalActions < 3)
                            <!-- Show buttons directly when less than 3 actions -->
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="{{ route('master_data.payment_term.edit', $paymentTerm->id) }}" class="btn btn-primary btn-md waves-effect waves-light">
                                    <i class="icon-base ti tabler-edit me-2"></i>Edit
                                </a>
                            </div>
                        @else
                            <!-- Show dropdown for 3 or more actions -->
                            <div class="dropdown">
                                <button type="button" class="btn btn-primary btn-md waves-effect waves-light dropdown-toggle" data-bs-toggle="dropdown">
                                    Aksi
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('master_data.payment_term.edit', $paymentTerm->id) }}">
                                        <i class="icon-base ti tabler-edit me-2"></i>Edit</a></li>
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Entity Title -->
            <h2>{{ $paymentTerm->name }}</h2>
            <span>
                <h6>Data di bawah berdasarkan tanggal {{ date('Y-m-d H:i:s') }}</h6>
            </span>
            <hr class="my-6 mx-n6" />

            <!-- Top-level Information (col-6 Maximum) -->
            <div class="row g-6">
                <div class="col-md-6">
                    <label class="form-label">Kode Syarat Pembayaran</label>
                    <input type="text" class="form-control" value="{{ $paymentTerm->code }}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jumlah Hari</label>
                    <input type="text" class="form-control" value="{{ $paymentTerm->days == 0 ? 'Immediate/COD' : $paymentTerm->days . ' hari' }}" disabled />
                </div>
            </div>
            
            <div class="row g-6 mt-3">
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <div class="form-control-plaintext">
                        @if($paymentTerm->status == 'active')
                            <span class="badge bg-label-success">Aktif</span>
                        @else
                            <span class="badge bg-label-secondary">Inaktif</span>
                        @endif
                        @if($paymentTerm->is_default)
                            <span class="badge bg-label-warning ms-2">DEFAULT</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <!-- Empty for alignment -->
                </div>
            </div>
            <hr class="my-6 mx-n6" />
            
            <div class="col-lg-12">
                <div class="demo-inline-spacing mt-4">
                    <div class="list-group list-group-horizontal-md text-md-center" role="tablist">
                        <a class="list-group-item list-group-item-action waves-effect active" id="payment-term-info-list-item"
                            data-bs-toggle="list" href="#horizontal-payment-term-info" aria-selected="true" role="tab">
                            <i class="icon-base ti tabler-info-circle me-2"></i>
                            <b>Informasi Detail</b>
                        </a>
                        <a class="list-group-item list-group-item-action waves-effect" id="audit-log-list-item"
                            data-bs-toggle="list" href="#horizontal-audit-log" aria-selected="false" role="tab">
                            <i class="icon-base ti tabler-history me-2"></i>
                            <b>Audit Log</b>
                        </a>
                    </div>
                    <div class="tab-content px-0 mt-0">
                        <div class="tab-pane fade show active" id="horizontal-payment-term-info" role="tabpanel">
                            <div class="section-header mt-6">
                                <h6>Detail Informasi Syarat Pembayaran</h6>
                                <hr class="mx-n6">
                            </div>
                            
                            <div class="row g-6">
                                <div class="col-md-6">
                                    <label class="form-label">Tipe Syarat Pembayaran</label>
                                    <input type="text" class="form-control" value="{{ $paymentTerm->type == 'net' ? 'Net Payment' : ($paymentTerm->type == 'immediate' ? 'Immediate Payment' : ($paymentTerm->type == 'cod' ? 'Cash on Delivery' : 'Custom Terms')) }}" disabled />
                                </div>
                                <div class="col-md-6">
                                    <!-- Empty for alignment -->
                                </div>
                            </div>
                            
                            @if($paymentTerm->description)
                                <div class="row g-6 mt-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Deskripsi</label>
                                        <textarea class="form-control" rows="3" disabled>{{ $paymentTerm->description }}</textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <!-- Empty for alignment -->
                                    </div>
                                </div>
                            @endif
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
                                        <tr>
                                            <td>{{ $paymentTerm->created_at }}</td>
                                            <td>
                                                <span class="text-primary fw-semibold">System</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-label-success">CREATE</span>
                                            </td>
                                            <td>Syarat pembayaran dibuat</td>
                                        </tr>
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
        // Initialize DataTables for tables inside tabs (no scrollX)
        $('#audit-log-table').DataTable({
            responsive: true,
            orderCellsTop: true,
            order: [[0, 'desc']], // Sort by date descending
            searching: false,
            paging: false,
            info: false
        });
    });
</script>
@endsection