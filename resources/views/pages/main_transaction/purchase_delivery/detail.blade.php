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

            <!-- Entity Title -->
            <h2>{{ $purchaseDelivery->transaction_code }}</h2>
            <span>
                <h6>Data di bawah berdasarkan tanggal {{ date('Y-m-d H:i:s') }}</h6>
            </span>
            <hr class="my-6 mx-n6" />

            <!-- Tab Navigation -->
            <ul class="nav nav-tabs nav-fill mb-6" role="tablist">
                <li class="nav-item">
                    <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
                        data-bs-target="#informasi-detail" aria-controls="informasi-detail" aria-selected="true">
                        <i class="icon-base ti tabler-info-circle me-2"></i>
                        <span class="align-middle">Informasi Detail</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#audit-log"
                        aria-controls="audit-log" aria-selected="false">
                        <i class="icon-base ti tabler-history me-2"></i>
                        <span class="align-middle">Audit Log</span>
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content p-0">
                <!-- Informasi Detail Tab -->
                <div class="tab-pane fade show active" id="informasi-detail" role="tabpanel">
                    <!-- Basic Information Section -->
                    <div class="section-header mt-12">
                        <h6>Informasi Umum</h6>
                        <hr class="mx-n6">
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">No. Purchase Delivery</label>
                            <input type="text" class="form-control" value="{{ $purchaseDelivery->transaction_code }}"
                                disabled />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal</label>
                            <input type="text" class="form-control" value="{{ $purchaseDelivery->delivery_date }}"
                                disabled />
                        </div>
                    </div>
                    <div class="row g-3 mb-4">
                        {{-- <div class="col-md-6">
                            <label class="form-label">Supplier</label>
                            <input type="text" class="form-control" value="{{ $purchaseDelivery->supplier }}"
                                disabled />
                        </div> --}}
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <input type="text" class="form-control" value="{{ $purchaseDelivery->delivery_status }}"
                                disabled />
                        </div>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">PO Reference</label>
                            <input type="text" class="form-control" value="{{ $purchaseDelivery->po_reference }}"
                                disabled />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Pengiriman</label>
                            <input type="text" class="form-control" value="{{ $purchaseDelivery->delivery_date }}"
                                disabled />
                        </div>
                    </div>

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
                    {{-- <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Total Items</label>
                            <input type="text" class="form-control"
                                value="{{ number_format($purchaseDelivery->total_item, 0, ',', '.') }}" disabled />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Items Diterima</label>
                            <input type="text" class="form-control"
                                value="{{ number_format($purchaseDelivery->delivered_items, 0, ',', '.') }}" disabled />
                        </div>
                    </div> --}}

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
                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label class="form-label">Catatan Pengiriman</label>
                            <textarea class="form-control" rows="3" disabled>{{ $purchaseDelivery->description }}</textarea>
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
                                        <th style="width: 30%;">Produk</th>
                                        <th style="width: 12%;">Qty PO</th>
                                        <th style="width: 12%;">Previously Delivered</th>
                                        <th style="width: 12%;">This Delivery</th>
                                        <th style="width: 12%;">Remaining</th>
                                        <th style="width: 14%;">Unit Price</th>
                                        <th style="width: 14%;">Note</th>
                                        <th style="width: 8%;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($purchaseDeliveryProducts ?? [] as $product)
                                        <tr>
                                            <td>{{ $product->product_name }}</td>
                                            <td class="text-center">{{ number_format($product->ordered_qty ?? 0) }} {{ $product->unit_name }}</td>
                                            <td class="text-center">
                                                {{ number_format($product->delivered_qty_before ?? 0) }} {{ $product->unit_name }}
                                            </td>
                                            <td class="text-center">{{ number_format($product->qty ?? 0) }} {{ $product->unit_name }}</td>
                                            </td>
                                            <td class="text-center">
                                                {{ number_format($product->remaining_qty_after ?? 0) }} {{ $product->unit_name }}
                                            </td>
                                            <td class="text-end">
                                                {{ number_format($product->price_per_qty ?? 0, 0, ',', '.') }}</td>
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
                                        <a href="{{ $purchaseDelivery->attachment }}" target="_blank"
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

                <!-- Audit Log Tab -->
                <div class="tab-pane fade" id="audit-log" role="tabpanel">
                    <div class="section-header mt-12">
                        <h6>Riwayat Perubahan</h6>
                        <hr class="mx-n6">
                    </div>

                    <div class="card-datatable">
                        <table id="audit-log-table" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>User</th>
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
                                            @php
                                                $badgeClass = '';
                                                switch ($log->action_type) {
                                                    case 'CREATE':
                                                        $badgeClass = 'bg-label-success';
                                                        break;
                                                    case 'UPDATE':
                                                        $badgeClass = 'bg-label-primary';
                                                        break;
                                                    case 'DELETE':
                                                        $badgeClass = 'bg-label-danger';
                                                        break;
                                                    default:
                                                        $badgeClass = 'bg-label-secondary';
                                                }
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">{{ $log->action_type }}</span>
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
@stop

@section('js_script')
    <script>
        $(function() {
            // Initialize audit log table
            if ($('#audit-log-table').length) {
                new DataTable('#audit-log-table', {
                    order: [
                        [0, 'desc']
                    ], // Sort by date descending (latest first)
                    pageLength: 10,
                    language: {
                        paginate: {
                            next: '<i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>',
                            previous: '<i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>',
                            first: '<i class="icon-base ti tabler-chevrons-left scaleX-n1-rtl icon-18px"></i>',
                            last: '<i class="icon-base ti tabler-chevrons-right scaleX-n1-rtl icon-18px"></i>'
                        }
                    }
                });
            }

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
