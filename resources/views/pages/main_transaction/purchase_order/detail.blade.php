@extends('layouts.default')
@section('page_title')
    Detail Purchase Order - {{ $purchaseOrder['reference_no'] }}
@stop
@section('content')
    <div class="card mb-6">
        <div class="card-body">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <h4 class="mb-3">Detail Purchase Order</h4>
                </div>
                <div class="col-md-4">
                    @php
                        $totalActions = 1; // Print always present
                        if($canEditPurchaseOrder) {
                            $totalActions++;
                        }
                        if($canApprovePurchaseOrder && $purchaseOrder['status'] === 'PENDING_APPROVAL') {
                            $totalActions += 2; // Approve and Reject
                        }
                        if($canDeletePurchaseOrder && in_array($purchaseOrder['status'], ['DRAFT', 'CANCELLED'])) {
                            $totalActions++; // Delete action
                        }
                    @endphp

                    @if($totalActions < 3)
                        <!-- Show buttons directly when less than 3 actions -->
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button" class="btn btn-primary btn-md waves-effect waves-light" onclick="window.print()">
                                <i class="icon-base ti tabler-printer me-2"></i>Print
                            </button>
                            @if($canEditPurchaseOrder)
                                <a href="{{ route('main_transaction.purchase_order.edit', $purchaseOrder['id']) }}" class="btn btn-success btn-md waves-effect waves-light">
                                    <i class="icon-base ti tabler-edit me-2"></i>Edit
                                </a>
                            @endif
                        </div>
                    @else
                        <!-- Show dropdown for 3 or more actions -->
                        <div class="dropdown">
                            <button type="button" class="btn btn-primary btn-md waves-effect waves-light dropdown-toggle" data-bs-toggle="dropdown">
                                Aksi
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><button class="dropdown-item" onclick="window.print()">
                                    <i class="icon-base ti tabler-printer me-2"></i>Print</button></li>
                                @if($canEditPurchaseOrder)
                                    <li><a class="dropdown-item" href="{{ route('main_transaction.purchase_order.edit', $purchaseOrder['id']) }}">
                                        <i class="icon-base ti tabler-edit me-2"></i>Edit</a></li>
                                @endif
                                @if($canApprovePurchaseOrder && $purchaseOrder['status'] === 'PENDING_APPROVAL')
                                    <li><a class="dropdown-item" href="javascript:void(0);" id="approve-po-btn">
                                        <i class="icon-base ti tabler-check me-2"></i>Setujui</a></li>
                                    <li><a class="dropdown-item" href="javascript:void(0);" id="reject-po-btn">
                                        <i class="icon-base ti tabler-x me-2"></i>Tolak</a></li>
                                @endif
                                @if($canDeletePurchaseOrder && in_array($purchaseOrder['status'], ['DRAFT', 'CANCELLED']))
                                    <li><a class="dropdown-item" href="javascript:void(0);" id="delete-po-btn">
                                        <i class="icon-base ti tabler-trash me-2"></i>Hapus</a></li>
                                @endif
                            </ul>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Purchase Order Title -->
            <h2>{{ $purchaseOrder['reference_no'] }}</h2>
            <span>
                <h6>Data purchase order berdasarkan tanggal {{ $purchaseOrder['created_at'] }}</h6>
            </span>
            <hr class="my-6 mx-n6" />

            <div class="row g-6">
                <div class="col-md-6">
                    <label class="form-label">No. Purchase Order</label>
                    <input type="text" class="form-control" value="{{ $purchaseOrder['reference_no'] }}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <span class="badge bg-label-primary d-block p-2 text-center">{{ $purchaseOrder['status'] }}</span>
                </div>
            </div>
            <hr class="my-6 mx-n6" />

            <div class="col-lg-12">
                <div class="demo-inline-spacing mt-4">
                    <div class="list-group list-group-horizontal-md text-md-center" role="tablist">
                        <a class="list-group-item list-group-item-action waves-effect active" id="purchase-order-info-list-item"
                            data-bs-toggle="list" href="#horizontal-purchase-order-info" aria-selected="true" role="tab">
                            <i class="icon-base ti tabler-info-circle me-2"></i>
                            <b>Informasi Detail</b>
                        </a>
                        <a class="list-group-item list-group-item-action waves-effect" id="product-list-item"
                            data-bs-toggle="list" href="#horizontal-product" aria-selected="false" role="tab">
                            <i class="icon-base ti tabler-package me-2"></i>
                            <b>Produk</b>
                        </a>
                        <a class="list-group-item list-group-item-action waves-effect" id="audit-log-list-item"
                            data-bs-toggle="list" href="#horizontal-audit-log" aria-selected="false" role="tab">
                            <i class="icon-base ti tabler-history me-2"></i>
                            <b>Audit Log</b>
                        </a>
                    </div>
                    <div class="tab-content px-0 mt-0">
                        <!-- Information Tab -->
                        <div class="tab-pane fade show active" id="horizontal-purchase-order-info" role="tabpanel">
                            <div id="purchase-order-info-content">
                                <!-- Transaction Information Section -->
                                <div class="section-header mt-12">
                                    <h6>Informasi Transaksi</h6>
                                    <hr class="mx-n6">
                                </div>
                                
                                <!-- Row 1: Tanggal, Supplier -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Tanggal</label>
                                        <input type="text" class="form-control" value="{{ $purchaseOrder['date'] }}" disabled />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Supplier</label>
                                        <input type="text" class="form-control" value="{{ $purchaseOrder['supplier'] }}" disabled />
                                    </div>
                                </div>
                                
                                <!-- Row 2: Tanggal Jatuh Tempo, Currency -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Tanggal Jatuh Tempo</label>
                                        <input type="text" class="form-control" value="{{ $purchaseOrder['due_date'] }}" disabled />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Currency</label>
                                        <input type="text" class="form-control" value="{{ $purchaseOrder['currency'] }}" disabled />
                                    </div>
                                </div>
                                
                                <!-- Row 3: Down Payment, Total -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Down Payment</label>
                                        <input type="text" class="form-control" value="Rp {{ number_format($purchaseOrder['down_payment'], 0, ',', '.') }}" disabled />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Total</label>
                                        <input type="text" class="form-control" value="Rp {{ number_format($purchaseOrder['total'], 0, ',', '.') }}" disabled />
                                    </div>
                                </div>

                                <!-- Row 4: Sisa Tagihan, Payment Method -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Sisa Tagihan</label>
                                        <input type="text" class="form-control" value="Rp {{ number_format($purchaseOrder['remaining_bill'], 0, ',', '.') }}" disabled />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Payment Method</label>
                                        <input type="text" class="form-control" value="{{ $purchaseOrder['payment_method'] }}" disabled />
                                    </div>
                                </div>

                                <!-- Row 5: Payment Terms, Warehouse -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Payment Terms</label>
                                        <input type="text" class="form-control" value="{{ $purchaseOrder['payment_terms'] }}" disabled />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Gudang</label>
                                        <input type="text" class="form-control" value="{{ $purchaseOrder['warehouse'] }}" disabled />
                                    </div>
                                </div>

                                <!-- Row 6: PIC, Keterangan -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">PIC</label>
                                        <input type="text" class="form-control" value="{{ $purchaseOrder['pic'] }}" disabled />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Keterangan</label>
                                        <textarea class="form-control" rows="3" disabled>{{ $purchaseOrder['description'] ?? '-' }}</textarea>
                                    </div>
                                </div>

                                <!-- Informasi Tambahan & Ringkasan Section -->
                                <div class="section-header mt-12">
                                    <h6>Informasi Tambahan & Ringkasan</h6>
                                    <hr class="mx-n6">
                                </div>
                                
                                <!-- Split Layout: Tag, Memo, Notes -->
                                <div class="row">
                                    <!-- Left Side: Tag and Memo -->
                                    <div class="col-md-6 mb-4">
                                        <div class="mb-3">
                                            <label class="form-label">Tag</label>
                                            @if(isset($purchaseOrder['tags']) && is_array($purchaseOrder['tags']) && count($purchaseOrder['tags']) > 0)
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($purchaseOrder['tags'] as $tag)
                                                        <span class="badge {{ $tag['preview'] }}">{{ $tag['name'] }}</span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="text-muted">Tidak ada tag</div>
                                            @endif
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Memo (Internal)</label>
                                            <textarea class="form-control" rows="4" disabled>{{ $purchaseOrder['memo'] ?? '-' }}</textarea>
                                        </div>
                                    </div>
                                    
                                    <!-- Right Side: Notes -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Catatan</label>
                                            <textarea class="form-control" rows="4" disabled>{{ $purchaseOrder['notes'] ?? '-' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Product Tab -->
                        <div class="tab-pane fade" id="horizontal-product" role="tabpanel">
                            <!-- Product List Section -->
                            <div class="section-header mt-12">
                                <h6>Daftar Produk Purchase Order</h6>
                            </div>
                            
                            <div class="mx-n5 my-4">
                                <table class="table table-bordered" id="product-lines-table">
                                    <thead>
                                        <tr>
                                            <th>Produk</th>
                                            <th>Deskripsi</th>
                                            <th>Qty</th>
                                            <th>Satuan</th>
                                            <th>Harga Satuan</th>
                                            <th>Pajak</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($purchaseOrder['product_lines'] as $line)
                                        <tr>
                                            <td>{{ $line['product_name'] }}</td>
                                            <td>{{ $line['description'] }}</td>
                                            <td class="text-center">{{ number_format($line['quantity'], 0, ',', '.') }}</td>
                                            <td class="text-center">{{ $line['unit'] }}</td>
                                            <td class="text-end">Rp {{ number_format($line['unit_price'], 0, ',', '.') }}</td>
                                            <td class="text-center">{{ $line['tax_name'] ?: '-' }}</td>
                                            <td class="text-end">Rp {{ number_format($line['total'], 0, ',', '.') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-info">
                                            <th colspan="6">Total</th>
                                            <th class="text-end">Rp {{ number_format($purchaseOrder['total'], 0, ',', '.') }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
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
                                        <tr>
                                            <td>{{ $purchaseOrder['created_at'] }}</td>
                                            <td><a href="#" class="text-primary fw-semibold">{{ $purchaseOrder['pic'] }}</a></td>
                                            <td><span class="badge bg-label-success">CREATE</span></td>
                                            <td>Purchase Order dibuat dengan nomor {{ $purchaseOrder['reference_no'] }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ $purchaseOrder['updated_at'] }}</td>
                                            <td><a href="#" class="text-primary fw-semibold">{{ $purchaseOrder['pic'] }}</a></td>
                                            <td><span class="badge bg-label-warning">UPDATE</span></td>
                                            <td>Status diubah menjadi {{ $purchaseOrder['status'] }}</td>
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
        $('#product-lines-table').DataTable({
            responsive: true,
            orderCellsTop: true,
            searching: false,
            paging: false,
            info: false
        });

        $('#audit-log-table').DataTable({
            responsive: true,
            orderCellsTop: true,
            order: [[0, 'desc']], // Sort by date descending
            searching: false,
            paging: false,
            info: false
        });

        // Action button handlers
        $('#approve-po-btn').on('click', function() {
            Swal.fire({
                title: 'Setujui Purchase Order?',
                text: 'Purchase Order akan disetujui dan dapat diproses lebih lanjut.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Setujui',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-outline-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Purchase Order berhasil disetujui.',
                        icon: 'success',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                }
            });
        });

        $('#reject-po-btn').on('click', function() {
            Swal.fire({
                title: 'Tolak Purchase Order?',
                text: 'Purchase Order akan ditolak dan tidak dapat diproses.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Tolak',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-outline-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Purchase Order Ditolak',
                        text: 'Purchase Order berhasil ditolak.',
                        icon: 'success',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                }
            });
        });

        $('#delete-po-btn').on('click', function() {
            Swal.fire({
                title: 'Hapus Purchase Order?',
                text: 'Data yang dihapus tidak dapat dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-outline-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Dihapus!',
                        text: 'Purchase Order berhasil dihapus.',
                        icon: 'success',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    }).then(() => {
                        window.location.href = '{{ route("main_transaction.purchase_order.index") }}';
                    });
                }
            });
        });
    });
</script>
@endsection