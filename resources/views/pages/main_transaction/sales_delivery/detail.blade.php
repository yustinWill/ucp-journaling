@extends('layouts.default')
@section('page_title')
    Detail Sales Delivery - {{ $salesDelivery->delivery_number }}
@stop
@section('content')
    <div class="card mb-6">
        <div class="card-body">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <h4 class="mb-3">Detail Sales Delivery</h4>
                </div>
                <div class="col-md-4">
                    <div class="d-flex gap-2 justify-content-end align-items-center">
                        @if (!$salesDelivery->is_accepted)
                            <span class="badge bg-label-dark fs-6 py-2 px-3 me-2">
                                <i class="icon-base ti tabler-clock me-1"></i>
                                Menunggu Pengiriman
                            </span>
                            <button type="button" id="btn-accept-delivery" class="btn btn-warning btn-md waves-effect waves-light">
                                <i class="icon-base ti tabler-check me-2"></i>
                                Terima Pengiriman
                            </button>
                        @elseif ($salesDelivery->is_confirmed != '1' && $salesDelivery->status !== 'CONFIRMED')
                            <span class="badge bg-label-warning fs-6 py-2 px-3 me-2">
                                <i class="icon-base ti tabler-truck me-1"></i>
                                Dikirim
                            </span>
                            <button type="button" id="btn-confirm-delivery" class="btn btn-success btn-md waves-effect waves-light">
                                <i class="icon-base ti tabler-check me-2"></i>
                                Konfirm
                            </button>
                        @else
                            <span class="badge bg-label-success fs-6 py-2 px-3">
                                <i class="icon-base ti tabler-check me-1"></i>
                                Terkirim
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sales Delivery Title -->
            <h2>{{ $salesDelivery->delivery_number }}</h2>
            <span>
                <h6>Data sales delivery berdasarkan tanggal {{ $salesDelivery->created_at }}</h6>
            </span>
            <hr class="my-6 mx-n6" />

            <!-- Informasi Dasar Transaksi Section - Outside tabs, on top -->
            
            <!-- Row 1: Tanggal Transaksi, Nomor Transaksi -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tanggal Transaksi</label>
                    <input type="text" class="form-control" value="{{ $salesDelivery->delivery_date }}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nomor Transaksi</label>
                    <input type="text" class="form-control" value="{{ $salesDelivery->delivery_number }}" disabled />
                </div>
            </div>

            <hr class="my-6 mx-n6" />

            <!-- Tabs Section -->
            <div class="col-lg-12">
                <div class="demo-inline-spacing mt-4">
                    <div class="list-group list-group-horizontal-md text-md-center" role="tablist">
                        <a class="list-group-item list-group-item-action waves-effect active" id="sales-delivery-info-list-item"
                            data-bs-toggle="list" href="#horizontal-sales-delivery-info" aria-selected="true" role="tab">
                            <i class="icon-base ti tabler-info-circle me-2"></i>
                            <b>Informasi Detail</b>
                        </a>
                        <a class="list-group-item list-group-item-action waves-effect" id="audit-log-list-item"
                            data-bs-toggle="list" href="#horizontal-audit-log" aria-selected="false" role="tab">
                            <i class="icon-base ti tabler-history me-2"></i>
                            <b>Audit Log</b>
                        </a>
                    </div>
                    @if ($salesDelivery->is_accepted)
                        <div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">No Resi</label>
                                    <input type="text" id="customer-reference" class="form-control"
                                        value="{{ $salesDelivery->customer_reference ?? '' }}"
                                        {{ !$salesDelivery->is_accepted || $salesDelivery->is_confirmed == '1' ? 'disabled' : '' }} />
                                    @if (!$salesDelivery->is_accepted)
                                        <small class="form-text text-muted">Terima pengiriman terlebih dahulu untuk mengisi field ini</small>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Ongkos Kirim</label>
                                    <input type="text" id="shipping-cost" class="form-control currency-input text-end"
                                        value="{{ $salesDelivery->shipping_cost ?? 0 }}"
                                        {{ !$salesDelivery->is_accepted || $salesDelivery->is_confirmed == '1' ? 'disabled' : '' }} />
                                    @if (!$salesDelivery->is_accepted)
                                        <small class="form-text text-muted">Terima pengiriman terlebih dahulu untuk mengisi field ini</small>
                                    @endif
                                </div>
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Catatan Pengiriman</label>
                                    <textarea id="delivery-notes" class="form-control" rows="3"
                                        {{ !$salesDelivery->is_accepted || $salesDelivery->is_confirmed == '1' ? 'disabled' : '' }}>{{ $salesDelivery->delivery_notes ?? '' }}</textarea>
                                    @if (!$salesDelivery->is_accepted)
                                        <small class="form-text text-muted">Terima pengiriman terlebih dahulu untuk mengisi field ini</small>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label d-block">Reimburse Ongkir ?</label>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" id="is-reimburse-ongkir"
                                            {{ $hasReimbursement ? 'checked' : '' }}
                                            {{ $salesDelivery->is_confirmed == '1' ? 'disabled' : '' }} />
                                        <label class="form-check-label" for="is-reimburse-ongkir">
                                            @if ($salesDelivery->is_confirmed == '1')
                                                {{ $hasReimbursement ? 'Reimbursement sudah diajukan' : 'Tidak ada reimbursement ongkir' }}
                                            @else
                                                Ajukan reimbursement untuk ongkos kirim ini
                                            @endif
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Lampiran</label>
                                @if (isset($salesDelivery->attachment) && $salesDelivery->attachment)
                                    <div>
                                        <a href="{{ $salesDelivery->attachment }}" target="_blank" class="btn btn-outline-primary">
                                            <i class="icon-base ti tabler-file me-2"></i>
                                            {{ basename($salesDelivery->attachment) }}
                                            <i class="icon-base ti tabler-external-link ms-2"></i>
                                        </a>
                                    </div>
                                @else
                                    @if ($salesDelivery->is_accepted && $salesDelivery->is_confirmed != '1')
                                        <input type="file" id="attachment-file" class="form-control"
                                            accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                                        <small class="text-muted">Format: JPG, PNG, PDF, DOC, DOCX. Max: 5MB</small>
                                    @else
                                        <input type="text" class="form-control" value="Tidak ada lampiran" disabled />
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endif
                    <div class="tab-content px-0 mt-0">
                        <!-- Information Tab -->
                        <div class="tab-pane fade show active" id="horizontal-sales-delivery-info" role="tabpanel">
                            <div id="sales-delivery-info-content">
                                <!-- Customer Information Section -->
                                <div class="section-header mt-12">
                        <h6>Informasi Customer</h6>
                        <hr class="mx-n6">
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Customer</label>
                            <input type="text" class="form-control" 
                                value="{{ $customer->name ?? 'N/A' }}" disabled />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email Customer</label>
                            <input type="text" class="form-control" 
                                value="{{ $customer->email ?? 'N/A' }}" disabled />
                        </div>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label class="form-label">Alamat Customer</label>
                            <textarea class="form-control" rows="2" disabled>{{ $customer->address ?? 'N/A' }}</textarea>
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
                            <input type="text" class="form-control" 
                                value="{{ $warehouse->name ?? 'N/A' }}" disabled />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Dikirim Oleh</label>
                            <input type="text" class="form-control" 
                                value="{{ $salesDelivery->created_by ?? 'N/A' }}" disabled />
                        </div>
                    </div>

                    <!-- Sales Order Information Section -->
                    @if($salesOrder)
                    <div class="section-header mt-12">
                        <h6>Informasi Sales Order</h6>
                        <hr class="mx-n6">
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">No. Sales Order</label>
                            <input type="text" class="form-control"
                                value="{{ $salesOrder->transaction_code ?? 'N/A' }}" disabled />
                        </div>
                        {{-- <div class="col-md-6">
                            <label class="form-label">Status SO</label>
                            <input type="text" class="form-control"
                                value="{{ $salesOrder->status ?? 'N/A' }}" disabled />
                        </div> --}}
                    </div>
                    @endif

                    <!-- Delivery Information Section -->
                    <div class="section-header mt-12">
                        <h6>Informasi Pengiriman</h6>
                        <hr class="mx-n6">
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Alamat Pengiriman</label>
                            <textarea class="form-control" rows="3" disabled>{{ $salesDelivery->delivery_address ?? '-' }}</textarea>
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
                                        <th style="width: 12%;">Qty Ordered</th>
                                        <th style="width: 12%;">Qty Delivered</th>
                                        <th style="width: 12%;">Unit</th>
                                        <th style="width: 14%;">Status</th>
                                        <th style="width: 20%;">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($productLines ?? [] as $product)
                                        <tr>
                                            <td>{{ $product->product_name ?? $product->product_code }}</td>
                                            <td>
                                                @php
                                                    $quantityOrdered = $product->quantity_ordered;
                                                    if ($quantityOrdered == floor($quantityOrdered)) {
                                                        $formattedQuantityOrdered = number_format($quantityOrdered, 0, ',', '.');
                                                    } else {
                                                        $formatted = number_format($quantityOrdered, 4, ',', '.');
                                                        $formattedQuantityOrdered = rtrim(rtrim($formatted, '0'), ',');
                                                    }
                                                @endphp
                                                {{ $formattedQuantityOrdered }}
                                            </td>
                                            <td>
                                                @php
                                                    $quantityDelivered = $product->quantity_delivered;
                                                    if ($quantityDelivered == floor($quantityDelivered)) {
                                                        $formattedQuantityDelivered = number_format($quantityDelivered, 0, ',', '.');
                                                    } else {
                                                        $formatted = number_format($quantityDelivered, 4, ',', '.');
                                                        $formattedQuantityDelivered = rtrim(rtrim($formatted, '0'), ',');
                                                    }
                                                @endphp
                                                {{ $formattedQuantityDelivered }}
                                            </td>
                                            <td>{{ $product->unit_name ?? '-' }}</td>
                                            <td>
                                                @php
                                                    $deliveryStatus = $product->delivery_status ?? 'PENDING';
                                                    $deliveryBadges = [
                                                        'PENDING' => 'bg-label-warning',
                                                        'DELIVERED' => 'bg-label-success',
                                                        'PARTIAL' => 'bg-label-info',
                                                        'CANCELLED' => 'bg-label-danger'
                                                    ];
                                                    $deliveryBadge = $deliveryBadges[$deliveryStatus] ?? 'bg-label-secondary';
                                                @endphp
                                                <span class="badge {{ $deliveryBadge }}">{{ $deliveryStatus }}</span>
                                            </td>
                                            <td>{{ $product->description ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">Tidak ada produk delivery</td>
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

                    <!-- Split Layout: Tag, Memo, Attachment -->
                    <div class="row">
                        <!-- Left Side: Tag and Attachment -->
                        <div class="col-md-6 mb-4">
                            <div class="mb-3">
                                <label class="form-label">Tag</label>
                                @if (isset($salesDelivery->tags) && $salesDelivery->tags != null)
                                    @php $tags = is_string($salesDelivery->tags) ? json_decode($salesDelivery->tags, true) : $salesDelivery->tags; @endphp
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach ($tags as $tag)
                                            @if(isset($tag->name) && $tag->name)
                                                <span class="badge {{ $tag->preview ?? 'bg-primary' }}">{{ $tag->name }}</span>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-muted">Tidak ada tag</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="mb-3">
                                <label class="form-label">Memo (Internal)</label>
                                <textarea class="form-control" rows="4" disabled>{{ $salesDelivery->memo ?? '-' }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Timestamps Section -->
                    <div class="section-header mt-5">
                        <h6>Informasi Sistem</h6>
                        <hr class="mx-n6">
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Dibuat Pada</label>
                            <input type="text" class="form-control" value="{{ $salesDelivery->created_at }}"
                                disabled />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Dibuat Oleh</label>
                            <input type="text" class="form-control" value="{{ $salesDelivery->created_by_name ?? '-' }}"
                                disabled />
                        </div>
                    </div>

                    @if ($salesDelivery->is_accepted)
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Diterima Pada</label>
                                <input type="text" class="form-control" value="{{ $salesDelivery->accepted_at ?? '-' }}"
                                    disabled />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Diterima Oleh</label>
                                <input type="text" class="form-control" value="{{ $salesDelivery->accepted_by_name ?? '-' }}"
                                    disabled />
                            </div>
                        </div>
                    @endif

                    @if ($salesDelivery->is_confirmed == '1')
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Dikonfirmasi Pada</label>
                                <input type="text" class="form-control" value="{{ $salesDelivery->confirm_at ?? '-' }}"
                                    disabled />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Dikonfirmasi Oleh</label>
                                <input type="text" class="form-control" value="{{ $salesDelivery->confirmed_by_name ?? '-' }}"
                                    disabled />
                            </div>
                        </div>
                    @endif
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
                                        @forelse($audit_logs ?? [] as $log)
                                            <tr>
                                                <td>{{ $log->created_at ?? $log['created_at'] }}</td>
                                                <td>{{ $log->user_name ?? $log['user_name'] ?? 'System' }}</td>
                                                <td>
                                                    @php
                                                        $actionType = $log->action_type ?? $log['action_type'] ?? 'UNKNOWN';
                                                    @endphp
                                                    @if($actionType === 'CREATE')
                                                        <span class="badge bg-label-success">CREATE</span>
                                                    @elseif($actionType === 'UPDATE')
                                                        <span class="badge bg-label-warning">UPDATE</span>
                                                    @elseif($actionType === 'DELETE')
                                                        <span class="badge bg-label-danger">DELETE</span>
                                                    @else
                                                        <span class="badge bg-label-info">{{ $actionType }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $log->description ?? $log['description'] ?? 'No description' }}</td>
                                            </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Tidak ada riwayat perubahan</td>
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
            // Format existing currency value before Cleave initialization
            const shippingCostInput = $('#shipping-cost');
            if (shippingCostInput.length && shippingCostInput.val()) {
                const rawValue = shippingCostInput.val();
                shippingCostInput.val(convertToIndonesianFormat(rawValue));
            }

            // Initialize currency formatting
            initCleaveCurrency(document);

            $('#audit-log-table').DataTable({
                orderCellsTop: true,
                order: [[0, 'desc']], // Sort by date descending
                searching: false,
                paging: false,
                info: false
            });

            // Handle Terima Pengiriman button click
            $('#btn-accept-delivery').on('click', function() {
                const deliveryId = {{ $salesDelivery->id }};
                const deliveryNumber = '{{ $salesDelivery->delivery_number }}';

                Swal.fire({
                    title: 'Terima Pengiriman?',
                    html: `Anda akan menerima sales delivery:<br><strong>${deliveryNumber}</strong><br><br>` +
                          `<div class="alert alert-info">` +
                          `<i class="icon-base ti tabler-info-circle me-2"></i>` +
                          `Setelah diterima, Anda dapat melengkapi detail pengiriman dan konfirmasi.` +
                          `</div>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Terima',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#ff9f43',
                    cancelButtonColor: '#82868b'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        Swal.fire({
                            title: 'Memproses...',
                            text: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Send AJAX request
                        $.ajax({
                            url: '{{ route('main_transaction.sales_delivery.accept') }}',
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                id: deliveryId
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.message,
                                    confirmButtonColor: '#28c76f'
                                }).then(() => {
                                    // Reload page to show updated status
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                let errorMessage = 'Terjadi kesalahan saat menerima delivery.';
                                let shouldReload = false;

                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;

                                    // Check if it's an "already accepted" or "already confirmed" error
                                    if (errorMessage.includes('sudah diterima') ||
                                        errorMessage.includes('sudah dikonfirmasi')) {
                                        shouldReload = true;
                                    }
                                }

                                Swal.fire({
                                    icon: 'info',
                                    title: 'Informasi',
                                    text: errorMessage,
                                    confirmButtonColor: '#28c76f'
                                }).then(() => {
                                    if (shouldReload) {
                                        location.reload();
                                    }
                                });
                            }
                        });
                    }
                });
            });

            // Handle Konfirm button click
            $('#btn-confirm-delivery').on('click', function() {
                const deliveryId = {{ $salesDelivery->id }};
                const deliveryNumber = '{{ $salesDelivery->delivery_number }}';

                Swal.fire({
                    title: 'Konfirmasi Sales Delivery?',
                    html: `Anda akan mengkonfirmasi sales delivery:<br><strong>${deliveryNumber}</strong><br><br>` +
                          `<div class="alert alert-warning">` +
                          `<i class="icon-base ti tabler-alert-triangle me-2"></i>` +
                          `Stock akan otomatis dikurangi dan movement log akan dibuat.` +
                          `</div>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Konfirmasi',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#28c76f',
                    cancelButtonColor: '#82868b'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const isReimburse = $('#is-reimburse-ongkir').is(':checked') ? 1 : 0;

                        // Show loading
                        Swal.fire({
                            title: 'Memproses...',
                            text: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Get field values
                        const shippingCost = parseCurrency($('#shipping-cost').val());
                        const customerReference = $('#customer-reference').val();
                        const deliveryNotes = $('#delivery-notes').val();
                        const attachmentFile = $('#attachment-file')[0]?.files[0];

                        // Prepare FormData for file upload
                        const formData = new FormData();
                        formData.append('_token', '{{ csrf_token() }}');
                        formData.append('shipping_cost', shippingCost);
                        formData.append('customer_reference', customerReference);
                        formData.append('delivery_notes', deliveryNotes);
                        formData.append('is_reimburse_ongkir', isReimburse);

                        if (attachmentFile) {
                            formData.append('attachment', attachmentFile);
                        }

                        // Send AJAX request
                        $.ajax({
                            url: `{{ url('main_transaction/sales_delivery/confirm') }}/${deliveryId}`,
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.message,
                                    confirmButtonColor: '#28c76f'
                                }).then(() => {
                                    // Reload page to show updated status
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                let errorMessage = 'Terjadi kesalahan saat mengkonfirmasi delivery.';
                                let shouldReload = false;

                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;

                                    // Check if it's an "already confirmed" or "not accepted yet" error
                                    if (errorMessage.includes('sudah dikonfirmasi') ||
                                        errorMessage.includes('harus diterima terlebih dahulu')) {
                                        shouldReload = true;
                                    }
                                }

                                Swal.fire({
                                    icon: 'info',
                                    title: 'Informasi',
                                    text: errorMessage,
                                    confirmButtonColor: '#28c76f'
                                }).then(() => {
                                    if (shouldReload) {
                                        location.reload();
                                    }
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection