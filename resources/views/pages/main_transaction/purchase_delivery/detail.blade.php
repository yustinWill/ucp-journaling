@extends('layouts.default')
@section('page_title')
    Detail Purchase Delivery - {{ $purchaseDelivery->transaction_code }}
@stop
@section('content')
    <div class="card mb-6">
        <div class="card-body">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <h4 class="mb-3">Detail Purchase Delivery</h4>
                </div>
                <div class="col-md-4">
                    {{-- @php
                        $totalActions = 1; // Edit always present
                        if($canApprovePurchaseDelivery && $purchaseDelivery['status'] === 'PENDING') {
                            $totalActions += 2; // Confirm and Cancel
                        }
                        if($canDeletePurchaseDelivery && in_array($purchaseDelivery['status'], ['PENDING', 'CANCELLED'])) {
                            $totalActions++; // Delete action
                        }
                    @endphp --}}

                    {{-- @if ($totalActions < 3)
                        <div class="d-flex gap-2 justify-content-end">
                            @if ($canEditPurchaseDelivery)
                                <a href="{{ route('main_transaction.purchase_delivery.edit', $purchaseDelivery['id']) }}"
                                    class="btn btn-primary btn-md waves-effect waves-light">
                                    <i class="icon-base ti tabler-edit me-2"></i>
                                    Edit
                                </a>
                            @endif
                            @if ($canDeletePurchaseDelivery && in_array($purchaseDelivery['status'], ['PENDING', 'CANCELLED']))
                                <button type="button" class="btn btn-danger btn-md waves-effect waves-light"
                                    id="delete-pd-btn">
                                    <i class="icon-base ti tabler-trash me-2"></i>
                                    Hapus
                                </button>
                            @endif
                        </div>
                    @else --}}
                    <!-- Show dropdown for 3 or more actions -->
                    {{-- <div class="d-flex gap-2 justify-content-end">
                        @if ($canEditPurchaseDelivery)
                                <a href="{{ route('main_transaction.purchase_delivery.edit', $purchaseDelivery['id']) }}" class="btn btn-primary btn-md waves-effect waves-light">
                                    <i class="icon-base ti tabler-edit me-2"></i>
                                    Edit
                                </a>
                            @endif
                        <div class="dropdown">
                            <button type="button" class="btn btn-primary btn-md waves-effect waves-light dropdown-toggle"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                Aksi
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @if ($canApprovePurchaseDelivery && $purchaseDelivery['status'] === 'PENDING')
                                    <li>
                                        <a class="dropdown-item" href="javascript:void(0);" id="confirm-delivery-btn">
                                            <i class="icon-base ti tabler-check me-2"></i>Konfirmasi Terima
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="javascript:void(0);" id="cancel-delivery-btn">
                                            <i class="icon-base ti tabler-x me-2"></i>Batalkan
                                        </a>
                                    </li>
                                @endif
                                @if ($canDeletePurchaseDelivery && in_array($purchaseDelivery['status'], ['PENDING', 'CANCELLED']))
                                    <li>
                                        <a class="dropdown-item" href="javascript:void(0);" id="delete-pd-btn">
                                            <i class="icon-base ti tabler-trash me-2"></i>Hapus
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div> --}}
                    {{-- @endif --}}
                </div>
            </div>

            <!-- Purchase Delivery Title -->
            <h2>{{ $purchaseDelivery->transaction_code }}</h2>
            <span>
                <h6>Data purchase delivery berdasarkan tanggal {{ $purchaseDelivery->created_at }}</h6>
            </span>
            <hr class="my-6 mx-n6" />

            <!-- Informasi Dasar Transaksi Section - Outside tabs, on top -->
            
            <!-- Row 1: Tanggal Transaksi, Nomor Transaksi -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tanggal Transaksi</label>
                    <input type="text" class="form-control" value="{{ $purchaseDelivery->delivery_date }}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nomor Transaksi</label>
                    <input type="text" class="form-control" value="{{ $purchaseDelivery->transaction_code }}" disabled />
                </div>
            </div>

            <hr class="my-6 mx-n6" />

            <!-- Tabs Section -->
            <div class="col-lg-12">
                <div class="demo-inline-spacing mt-4">
                    <div class="list-group list-group-horizontal-md text-md-center" role="tablist">
                        <a class="list-group-item list-group-item-action waves-effect active" id="purchase-delivery-info-list-item"
                            data-bs-toggle="list" href="#horizontal-purchase-delivery-info" aria-selected="true" role="tab">
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
                        <!-- Information Tab -->
                        <div class="tab-pane fade show active" id="horizontal-purchase-delivery-info" role="tabpanel">
                            <div id="purchase-delivery-info-content">
                                <!-- Delivery Information Section -->
                                <div class="section-header mt-12">
                        <h6>Informasi Pengiriman</h6>
                        <hr class="mx-n6">
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Carrier</label>
                            <input type="text" class="form-control" value="{{ $purchaseDelivery->courier_name }}"
                                disabled />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tracking Number</label>
                            <input type="text" class="form-control" value="{{ $purchaseDelivery->tracking_number }}"
                                disabled />
                        </div>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Keterangan</label>
                            <textarea class="form-control" rows="3" disabled>{{ $purchaseDelivery->description }}</textarea>
                        </div>
                    </div>

                    <!-- Warehouse Information Section -->
                    <div class="section-header mt-12">
                        <h6>Informasi Gudang</h6>
                        <hr class="mx-n6">
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Gudang</label>
                            <input type="text" class="form-control" value="{{ $purchaseDelivery->warehouse }}"
                                disabled />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Diterima Oleh</label>
                            <input type="text" class="form-control" value="{{ $purchaseDelivery->created_by_name }}"
                                disabled />
                        </div>
                    </div>

                    <!-- Purchase Order Information Section -->
                    <div class="section-header mt-12">
                        <h6>Informasi Purchase Order</h6>
                        <hr class="mx-n6">
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">No. Purchase Order</label>
                            <input type="text" class="form-control"
                                value="{{ $purchaseDelivery->po_reference ?? 'N/A' }}" disabled />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Supplier</label>
                            <input type="text" class="form-control"
                                value="{{ $purchaseDelivery->supplier_name ?? ($purchaseDelivery->supplier ?? 'N/A') }}"
                                disabled />
                        </div>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Email Supplier</label>
                            <input type="text" class="form-control"
                                value="{{ $purchaseDelivery->supplier_email ?? 'N/A' }}" disabled />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status PO</label>
                            <input type="text" class="form-control"
                                value="{{ $purchaseDelivery->po_status ?? 'N/A' }}" disabled />
                        </div>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label class="form-label">Alamat Supplier</label>
                            <textarea class="form-control" rows="2" disabled>{{ $purchaseDelivery->supplier_address ?? 'N/A' }}</textarea>
                        </div>
                    </div>

                    <!-- Items Delivery Section -->
                    <div class="section-header mt-12">
                        <h6>Items Delivery</h6>
                    </div>

                    <div class="mb-4 mx-n5">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="product-detail-table">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th>Qty PO</th>
                                        <th>Previously Delivered</th>
                                        <th>This Delivery</th>
                                        <th>Remaining</th>
                                        <th>Unit Price</th>
                                        <th>Note</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($purchaseDeliveryProducts ?? [] as $product)
                                        <tr>
                                            <td>{{ $product->product_name }}</td>
                                            <td class="text-end">
                                                @php
                                                    $orderedQty = $product->ordered_qty;
                                                    if ($orderedQty == floor($orderedQty)) {
                                                        $formattedOrderedQty = number_format($orderedQty, 0, ',', '.');
                                                    } else {
                                                        $formatted = number_format($orderedQty, 4, ',', '.');
                                                        $formattedOrderedQty = rtrim(rtrim($formatted, '0'), ',');
                                                    }
                                                @endphp
                                                {{ $formattedOrderedQty }} {{ $product->unit_name }}
                                            </td>
                                            <td class="text-end">
                                                @php
                                                    $deliveredQtyBefore = $product->delivered_qty_before;
                                                    if ($deliveredQtyBefore == floor($deliveredQtyBefore)) {
                                                        $formattedDeliveredQtyBefore = number_format($deliveredQtyBefore, 0, ',', '.');
                                                    } else {
                                                        $formatted = number_format($deliveredQtyBefore, 4, ',', '.');
                                                        $formattedDeliveredQtyBefore = rtrim(rtrim($formatted, '0'), ',');
                                                    }
                                                @endphp
                                                {{ $formattedDeliveredQtyBefore }} {{ $product->unit_name }}
                                            </td>
                                            <td class="text-end">
                                                @php
                                                    $qty = $product->qty;
                                                    if ($qty == floor($qty)) {
                                                        $formattedQty = number_format($qty, 0, ',', '.');
                                                    } else {
                                                        $formatted = number_format($qty, 4, ',', '.');
                                                        $formattedQty = rtrim(rtrim($formatted, '0'), ',');
                                                    }
                                                @endphp
                                                {{ $formattedQty }} {{ $product->unit_name }}
                                            </td>
                                            <td class="text-end">
                                                @php
                                                    $remainingQtyAfter = $product->remaining_qty_after;
                                                    if ($remainingQtyAfter == floor($remainingQtyAfter)) {
                                                        $formattedRemainingQtyAfter = number_format($remainingQtyAfter, 0, ',', '.');
                                                    } else {
                                                        $formatted = number_format($remainingQtyAfter, 4, ',', '.');
                                                        $formattedRemainingQtyAfter = rtrim(rtrim($formatted, '0'), ',');
                                                    }
                                                @endphp
                                                {{ $formattedRemainingQtyAfter }} {{ $product->unit_name }}
                                            </td>
                                            <td class="text-end">
                                                @php
                                                    $pricePerQty = $product->price_per_qty;
                                                    if ($pricePerQty == floor($pricePerQty)) {
                                                        $formattedPricePerQty = number_format($pricePerQty, 0, ',', '.');
                                                    } else {
                                                        $formatted = number_format($pricePerQty, 4, ',', '.');
                                                        $formattedPricePerQty = rtrim(rtrim($formatted, '0'), ',');
                                                    }
                                                @endphp
                                                {{ $formattedPricePerQty }}
                                            </td>
                                            <td>
                                                {{ $product->product_delivery_note ?? '-' }}
                                            </td>
                                            <td class="text-center">
                                                @php
                                                    $remaining = $product->remaining_qty_after;
                                                    if ($remaining > 0) {
                                                        echo '<span class="badge bg-label-warning">Partial</span>';
                                                    } else {
                                                        echo '<span class="badge bg-label-success">Complete</span>';
                                                    }
                                                @endphp
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">Tidak ada produk delivery
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="section-header mt-12">
                        <h6>Informasi Tambahan & Ringkasan</h6>
                        <hr class="mx-n6">
                    </div>

                    <!-- Split Layout: Tag, Memo, Attachment & Financial Summary -->
                    <div class="row">
                        <!-- Left Side: Tag, Memo and Attachment -->
                        <div class="col-md-6 mb-4">
                            <div class="mb-3">
                                <label class="form-label">Tag</label>
                                @if (isset($purchaseDelivery->tags))
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach ($purchaseDelivery->tags as $tag)
                                            <span class="badge {{ $tag->preview }}">{{ $tag->code }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-muted">Tidak ada tag</div>
                                @endif
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Lampiran</label>
                                @if (isset($purchaseDelivery->attachment) && $purchaseDelivery->attachment)
                                    <div class="input-group">
                                        <input type="text" class="form-control"
                                            value="{{ $purchaseDelivery->attachment }}" disabled />
                                        <a href="{{ Storage::url($purchaseDelivery->attachment) }}" target="_blank"
                                            class="btn btn-outline-primary">
                                            <i class="icon-base ti tabler-download"></i>
                                        </a>
                                    </div>
                                @else
                                    <input type="text" class="form-control" value="Tidak ada lampiran" disabled />
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="mb-3">
                                <label class="form-label">Memo (Internal)</label>
                                <textarea class="form-control" rows="4" disabled>{{ $purchaseDelivery->memo ?? '-' }}</textarea>
                            </div>
                        </div>
                    </div>
                    <!-- Timestamps Section -->
                    <div class="section-header mt-12">
                        <h6>Informasi Sistem</h6>
                        <hr class="mx-n6">
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Dibuat Pada</label>
                            <input type="text" class="form-control" value="{{ $purchaseDelivery->created_at }}"
                                disabled />
                        </div>
                        {{-- <div class="col-md-6">
                            <label class="form-label">Diupdate Pada</label>
                            <input type="text" class="form-control" value="{{ $purchaseDelivery->updated_at }}"
                                disabled />
                        </div> --}}
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
                                @foreach ($auditLogs as $log)
                                    <tr>
                                        <td>{{ $log->created_at }}</td>
                                        <td>{{ $log->user_name }}</td>
                                        <td>
                                            @if($log->action_type === 'CREATE')
                                                <span class="badge bg-label-success">CREATE</span>
                                            @elseif($log->action_type === 'UPDATE')
                                                <span class="badge bg-label-warning">UPDATE</span>
                                            @elseif($log->action_type === 'DELETE')
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
            $('#audit-log-table').DataTable({
                orderCellsTop: true,
                order: [[0, 'desc']], // Sort by date descending
                searching: false,
                paging: false,
                info: false
            });

            // Action button handlers
            $('#confirm-delivery-btn').on('click', function() {
                Swal.fire({
                    title: 'Konfirmasi Penerimaan?',
                    text: 'Purchase Delivery akan dikonfirmasi sebagai diterima.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Konfirmasi',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-primary',
                        cancelButton: 'btn btn-outline-secondary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Handle confirmation logic here
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Purchase Delivery berhasil dikonfirmasi.',
                            icon: 'success',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        });
                    }
                });
            });

            $('#cancel-delivery-btn').on('click', function() {
                Swal.fire({
                    title: 'Batalkan Purchase Delivery?',
                    text: 'Purchase Delivery akan dibatalkan dan tidak dapat diproses.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Batalkan',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-danger',
                        cancelButton: 'btn btn-outline-secondary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Handle cancellation logic here
                        Swal.fire({
                            title: 'Purchase Delivery Dibatalkan',
                            text: 'Purchase Delivery berhasil dibatalkan.',
                            icon: 'success',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        });
                    }
                });
            });

            $('#delete-pd-btn').on('click', function() {
                Swal.fire({
                    title: 'Hapus Purchase Delivery?',
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
                        // Handle delete logic here
                        Swal.fire({
                            title: 'Dihapus!',
                            text: 'Purchase Delivery berhasil dihapus.',
                            icon: 'success',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        }).then(() => {
                            window.location.href =
                                '{{ route('main_transaction.purchase_delivery.index') }}';
                        });
                    }
                });
            });
        });
    </script>
@endsection
