@extends('layouts.default')
@section('page_title')
    Detail Produksi
@stop
@section('content')
    <div class="card mb-6">
        <div class="card-body">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <h4 class="mb-3">Detail Produksi</h4>
                </div>

                <!-- Action Panel -->
                <div class="col-md-4">
                    <div class="text-end">
                        <div class="d-flex gap-2 justify-content-end">
                            {{-- Status-based action buttons --}}
                            @if ($production->status == 'IN_PROGRESS')
                                <button class="btn btn-success btn-md waves-effect waves-light"
                                    onclick="completeProduction({{ $production->id }})">
                                    <i class="icon-base ti tabler-check me-2"></i>
                                    Complete
                                </button>
                            @endif
                            @if (in_array($production->status, ['DRAFT', 'PLANNED']))
                                <button class="btn btn-danger btn-md waves-effect waves-light"
                                    onclick="cancelProduction({{ $production->id }})">
                                    <i class="icon-base ti tabler-x me-2"></i>
                                    Cancel
                                </button>
                            @endif
                            @if ($production->status == 'COMPLETED')
                                <a href="{{ route('main_transaction.production.reversal', $production->id) }}"
                                    class="btn btn-warning btn-md waves-effect waves-light">
                                    <i class="icon-base ti tabler-arrow-back-up me-2"></i>
                                    Reversal
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Entity Title -->
            <h2>{{ $production->production_number }}</h2>
            <span>
                <h6>Data di bawah berdasarkan tanggal {{ date('Y-m-d H:i:s') }}</h6>
            </span>
            <hr class="my-6 mx-n6" />

            <!-- Top-level Information -->
            <div class="row g-6">
                <div class="col-md-6">
                    <label class="form-label">No Produksi</label>
                    <input type="text" class="form-control" value="{{ $production->production_number }}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Produksi</label>
                    <input type="text" class="form-control" value="{{ $production->production_date }}" disabled />
                </div>
            </div>

            <div class="row g-6 mt-3">
                <div class="col-md-6">
                    <label class="form-label">Bundle Product</label>
                    <input type="text" class="form-control" value="{{ $production->bundle_product_name }}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Gudang</label>
                    <div class="form-control-plaintext">
                        <a href="{{ route('master_data.warehouse.detail', $production->warehouse_id) }}"
                            class="text-primary fw-semibold">
                            {{ $production->warehouse_name }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="row g-6 mt-3">
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <div class="form-control-plaintext">
                        @if ($production->status == 'COMPLETED')
                            <span class="badge bg-label-success">{{ $production->status }}</span>
                        @elseif($production->status == 'IN_PROGRESS')
                            <span class="badge bg-label-warning">{{ $production->status }}</span>
                        @elseif($production->status == 'CANCELLED')
                            <span class="badge bg-label-danger">{{ $production->status }}</span>
                        @elseif($production->status == 'PLANNED')
                            <span class="badge bg-label-info">{{ $production->status }}</span>
                        @else
                            <span class="badge bg-label-secondary">{{ $production->status }}</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Mode Produksi</label>
                    <div class="form-control-plaintext">
                        @if (($production->production_mode ?? 'QUANTITY_PLANNED') == 'MATERIAL_BASED')
                            <span class="badge bg-label-warning">Material-Based Input</span>
                            <small class="text-muted d-block mt-1">Produksi berdasarkan material yang tersedia</small>
                        @else
                            <span class="badge bg-label-info">Quantity Planned</span>
                            <small class="text-muted d-block mt-1">Produksi berdasarkan target quantity</small>
                        @endif
                    </div>
                </div>
            </div>

            <div class="row g-6 mt-3">
                <div class="col-md-6">
                    <label class="form-label">PIC Produksi</label>
                    <div class="form-control-plaintext">
                        <a href="{{ route('master_data.user.detail', $production->pic_id) }}"
                            class="text-primary fw-semibold">
                            {{ $production->pic_name }}
                        </a>
                    </div>
                </div>
                <div class="col-md-6">
                    <!-- Empty for alignment -->
                </div>
            </div>
            <hr class="my-6 mx-n6" />

            <!-- Tab Section -->
            <div class="col-lg-12">
                <div class="demo-inline-spacing mt-4">
                    <div class="list-group list-group-horizontal-md text-md-center" role="tablist">
                        <a class="list-group-item list-group-item-action waves-effect active" id="production-info-list-item"
                            data-bs-toggle="list" href="#horizontal-production-info" aria-selected="true" role="tab">
                            <i class="icon-base ti tabler-info-circle me-2"></i>
                            <b>Informasi Detail</b>
                        </a>
                        <a class="list-group-item list-group-item-action waves-effect" id="components-list-item"
                            data-bs-toggle="list" href="#horizontal-components" aria-selected="false" role="tab">
                            <i class="icon-base ti tabler-package me-2"></i>
                            <b>Komponen Bundle</b>
                        </a>
                        <a class="list-group-item list-group-item-action waves-effect" id="audit-log-list-item"
                            data-bs-toggle="list" href="#horizontal-audit-log" aria-selected="false" role="tab">
                            <i class="icon-base ti tabler-history me-2"></i>
                            <b>Audit Log</b>
                        </a>
                    </div>

                    <div class="tab-content px-0 mt-0">
                        <!-- Production Information Tab -->
                        <div class="tab-pane fade show active" id="horizontal-production-info" role="tabpanel">
                            <div class="section-header mt-6">
                                <h6>Detail Produksi</h6>
                                <hr class="mx-n6">
                            </div>

                            <div class="row g-6">
                                <div class="col-md-6">
                                    <label class="form-label">Quantity Planned</label>
                                    <input type="text" class="form-control"
                                        value="{{ $production->quantity_planned }} pcs" disabled />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Quantity Produced</label>
                                    <input type="text" class="form-control"
                                        value="{{ $production->quantity_produced }} pcs" disabled />
                                </div>
                            </div>

                            <div class="row g-6 mt-3">
                                <div class="col-md-6">
                                    <label class="form-label">Biaya Produksi</label>
                                    <input type="text" class="form-control"
                                        value="Rp {{ number_format($production->biaya_produksi ?? 0, 0, ',', '.') }}" disabled />
                                </div>
                                {{-- <div class="col-md-6">
                                    <label class="form-label">Progress</label>
                                    @php
                                        $progress =
                                            $production->quantity_planned > 0
                                                ? ($production->quantity_produced / $production->quantity_planned) * 100
                                                : 0;
                                    @endphp
                                    <div class="progress" style="height: 40px;">
                                        <div class="progress-bar" role="progressbar"
                                            style="width: {{ number_format($progress, 1) }}%;"
                                            aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
                                            {{ number_format($progress, 1) }}%
                                        </div>
                                    </div>
                                </div> --}}

                            </div>

                            <div class="row g-6 mt-3">
                                <div class="col-md-6">
                                    <label class="form-label">Deskripsi</label>
                                    <textarea class="form-control" rows="3" disabled>{{ $production->description ?: '-' }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Memo <small class="text-muted">(Internal)</small></label>
                                    <textarea class="form-control" rows="3" disabled>{{ $production->notes ?: '-' }}</textarea>
                                </div>
                            </div>

                            <div class="row g-6 mt-3">
                                <div class="col-md-6">
                                    <label class="form-label">Lampiran Dokumen</label>
                                    @if (!empty($production->attachment))
                                        <div class="form-control-plaintext">
                                            <a href="{{ asset('storage/' . $production->attachment) }}" target="_blank"
                                                class="btn btn-outline-primary btn-sm">
                                                <i class="icon-base ti tabler-download me-2"></i>
                                                Download Lampiran
                                            </a>
                                        </div>
                                    @else
                                        <div class="form-control-plaintext text-muted">Tidak ada lampiran</div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Created At</label>
                                    <input type="text" class="form-control" value="{{ $production->created_by }} - {{ $production->created_at }}"
                                        disabled />
                                </div>
                                
                            </div>
                        </div>

                        <!-- Bundle Components Tab -->
                        <div class="tab-pane fade" id="horizontal-components" role="tabpanel">
                            <div class="section-header mt-6">
                                <h6>Komponen Bundle</h6>
                            </div>

                            @if (($production->production_mode ?? 'QUANTITY_PLANNED') == 'MATERIAL_BASED')
                                {{-- Material-Based Mode Table --}}
                                <div class="mx-n5">
                                    <table class="table table-bordered" id="components-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 25%;">Komponen Product</th>
                                                <th style="width: 10%;">Satuan</th>
                                                <th style="width: 13%;">Qty per Bundle</th>
                                                <th style="width: 15%;">Stock Available</th>
                                                <th style="width: 15%;">Material Input</th>
                                                <th style="width: 12%;">Leftover Material</th>
                                                <th style="width: 10%;">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($production_items as $component)
                                                @php
                                                    $formatNumber = function($num) {
                                                        return $num == floor($num) ? number_format($num, 0, ',', '.') : rtrim(rtrim(number_format($num, 4, ',', '.'), '0'), ',');
                                                    };
                                                @endphp
                                                <tr>
                                                    <td>{{ $component->product_name }}</td>
                                                    <td class="text-center">{{ $component->unit_code }}</td>
                                                    <td class="text-center">{{ $formatNumber($component->quantity_per_bundle) }}</td>
                                                    <td class="text-center">{{ $formatNumber($component->current_stock) }}</td>
                                                    <td class="text-center">
                                                        <strong>{{ $formatNumber($component->material_input ?? $component->quantity_consumed) }}</strong>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-label-warning">{{ $formatNumber($component->leftover_amount ?? 0) }}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        @if (($component->leftover_amount ?? 0) > 0)
                                                            <span class="badge bg-label-warning">Has Leftover</span>
                                                        @elseif (($component->leftover_amount ?? 0) == 0)
                                                            <span class="badge bg-label-success">Exact Match</span>
                                                        @else
                                                            <span class="badge bg-label-secondary">-</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted">Tidak ada komponen tersedia</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-info mt-3">
                                    <i class="icon-base ti tabler-info-circle me-2"></i>
                                    <strong>Informasi:</strong> Produksi menggunakan mode Material-Based. Leftover material telah dicatat dengan presisi 4 desimal untuk proses stock opname.
                                </div>
                            @else
                                {{-- Quantity Planned Mode Table --}}
                                <div class="mx-n5">
                                    <table class="table table-bordered" id="components-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 30%;">Komponen Product</th>
                                                <th style="width: 10%;">Satuan</th>
                                                <th style="width: 15%;">Qty per Bundle</th>
                                                <th style="width: 15%;">Total Required</th>
                                                <th style="width: 15%;">Stock Available</th>
                                                <th style="width: 15%;">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($production_items as $component)
                                                @php
                                                    $formatNumber = function($num) {
                                                        return $num == floor($num) ? number_format($num, 0, ',', '.') : rtrim(rtrim(number_format($num, 4, ',', '.'), '0'), ',');
                                                    };
                                                @endphp
                                                <tr>
                                                    <td>{{ $component->product_name }}</td>
                                                    <td class="text-center">{{ $component->unit_code }}</td>
                                                    <td class="text-center">{{ $formatNumber($component->quantity_per_bundle) }}</td>
                                                    <td class="text-center">{{ $formatNumber($component->quantity_required) }}</td>
                                                    <td class="text-center">{{ $formatNumber($component->current_stock) }}</td>
                                                    <td class="text-center">
                                                        @if ($component->current_stock >= $component->quantity_required)
                                                            <span class="badge bg-label-success">Available</span>
                                                        @else
                                                            <span class="badge bg-label-danger">Insufficient</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">Tidak ada komponen tersedia</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>

                        <!-- Audit Log Tab -->
                        <div class="tab-pane fade" id="horizontal-audit-log" role="tabpanel">
                            <div class="section-header mt-6">
                                <h6>Riwayat Perubahan</h6>
                            </div>
                            <div class="mx-n5">
                                <table class="table table-bordered" id="audit-log-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 20%;">Tanggal & Waktu</th>
                                            <th style="width: 20%;">Pengguna</th>
                                            <th style="width: 15%;">Aksi</th>
                                            <th style="width: 45%;">Deskripsi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($audit_logs as $log)
                                            <tr>
                                                <td>{{ $log->created_at }}</td>
                                                <td>
                                                    <a href="{{ route('master_data.user.detail', ['id' => $log->user_id]) }}"
                                                        class="text-primary fw-semibold">
                                                        {{ $log->user_name }}
                                                    </a>
                                                </td>
                                                <td>
                                                    @if ($log->action_type == 'CREATE')
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
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">Belum ada riwayat
                                                    perubahan</td>
                                            </tr>
                                        @endforelse
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
            // Initialize DataTable for audit log if needed
            $('#audit-log-table').DataTable({
                responsive: true,
                orderCellsTop: true,
                order: [
                    [0, 'desc']
                ],
                searching: false,
                paging: false,
                info: false
            });

            // Tab switching handler
            $('a[data-bs-toggle="list"]').on('shown.bs.tab', function(e) {
                const target = $(e.target).attr('href');
                // Handle any tab-specific initialization if needed
            });
        });

        // Complete production function
        window.completeProduction = function(productionId) {
            Swal.fire({
                title: 'Complete Produksi?',
                text: 'Apakah Anda yakin ingin menandai production ini sebagai selesai?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Complete',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // TODO: Implement AJAX call to complete production
                    $.ajax({
                        url: "{{ route('main_transaction.production.complete', ':id') }}".replace(
                            ':id', productionId),
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    title: 'Success!',
                                    text: response.message,
                                    icon: 'success',
                                    customClass: {
                                        confirmButton: 'btn btn-primary'
                                    },
                                    buttonsStyling: false
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Failed to complete production',
                                icon: 'error',
                                customClass: {
                                    confirmButton: 'btn btn-primary'
                                },
                                buttonsStyling: false
                            });
                        }
                    });
                }
            });
        };

        // Cancel production function
        window.cancelProduction = function(productionId) {
            Swal.fire({
                title: 'Cancel Produksi?',
                text: 'Apakah Anda yakin ingin membatalkan production ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Cancel',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // TODO: Implement AJAX call to cancel production
                    $.ajax({
                        url: "{{ route('main_transaction.production.cancel', ':id') }}".replace(':id',
                            productionId),
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    title: 'Success!',
                                    text: response.message,
                                    icon: 'success',
                                    customClass: {
                                        confirmButton: 'btn btn-primary'
                                    },
                                    buttonsStyling: false
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Failed to cancel production',
                                icon: 'error',
                                customClass: {
                                    confirmButton: 'btn btn-primary'
                                },
                                buttonsStyling: false
                            });
                        }
                    });
                }
            });
        };
    </script>
@endsection
