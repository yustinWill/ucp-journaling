@extends('layouts.default')
@section('page_title')
    Edit Purchase Delivery - {{ $purchaseDelivery->reference_no }}
@stop

@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Edit Purchase Delivery - {{ $purchaseDelivery->reference_no }}</h4>
        <form class="card-body" id="purchase-delivery-form" autocomplete="off">
            @csrf
            <input type="hidden" id="purchase-delivery-id" value="{{ $purchaseDelivery->id }}">

            <!-- Informasi Dasar Transaksi Section -->
            <div class="section-header mt-12">
                <h6>Informasi Dasar Transaksi</h6>
                <hr class="mx-n6">
            </div>

            <!-- Row 1: Tanggal Pengiriman, Nomor Transaksi -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tanggal Pengiriman <span style="color: red;">*</span></label>
                    <input type="date" class="form-control global-single-date-picker" id="purchase-delivery-date"
                        name="date" value="{{ $purchaseDelivery->date }}" placeholder="YYYY-MM-DD" required />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nomor Transaksi <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="purchase-delivery-reference"
                        name="reference_no" value="{{ $purchaseDelivery->reference_no }}" required />
                </div>
            </div>

            <!-- Row 2: Purchase Order Reference -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Purchase Order</label>
                    @php
                        $currentPO = collect($purchaseOrders)->firstWhere('value', $purchaseDelivery->purchase_order_id);
                    @endphp
                    <input type="text" class="form-control" 
                        value="{{ $currentPO['reference_no'] ?? $purchaseDelivery->po_reference ?? 'N/A' }}" disabled />
                    <input type="hidden" name="purchase_order_id" value="{{ $purchaseDelivery->purchase_order_id }}" />
                    <small class="form-text text-muted">PO tidak dapat diubah setelah delivery dibuat</small>
                </div>
            </div>

        <!-- Informasi Purchase Order Section -->
        <div class="section-header mt-12">
            <h6>Informasi Purchase Order</h6>
            <hr class="mx-n6">
        </div>

        <!-- Row 1: Supplier (Auto-filled), Email (Auto-filled) -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Supplier</label>
                <input type="text" class="form-control" id="po-supplier-display"
                    value="{{ $purchaseDelivery->supplier_name }}" disabled />
                <input type="hidden" id="purchase-delivery-supplier" name="supplier_id" value="{{ $purchaseDelivery->supplier_id }}" />
            </div>
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" id="purchase-delivery-email" name="email"
                    value="{{ $purchaseDelivery->supplier_email }}" disabled />
            </div>
        </div>

        <!-- Row 2: Alamat Pengiriman, Tel. Pengiriman -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Alamat Pengiriman</label>
                <textarea class="form-control" id="purchase-delivery-address" name="address" rows="2"
                    disabled>{{ $purchaseDelivery->supplier_address }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Tel. Pengiriman</label>
                <input type="text" class="form-control" id="supplier-phone" name="supplier_phone"
                    placeholder="Telepon akan otomatis terisi" disabled />
            </div>
        </div>

        <!-- Informasi Delivery Section -->
        <div class="section-header mt-12">
            <h6>Informasi Delivery</h6>
            <hr class="mx-n6">
        </div>

        <!-- Row 1: Nomor Delivery, Tanggal Delivery -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Nomor Delivery <span style="color: red;">*</span></label>
                <input type="text" class="form-control" id="delivery-number" name="delivery_number"
                    value="{{ $purchaseDelivery->delivery_number }}" required />
            </div>
            <div class="col-md-6">
                <label class="form-label">Tanggal Delivery <span style="color: red;">*</span></label>
                <input type="date" class="form-control global-single-date-picker" id="delivery-date"
                    name="delivery_date" value="{{ $purchaseDelivery->delivery_date }}" placeholder="YYYY-MM-DD" required />
            </div>
        </div>

        <!-- Row 2: Nomor Resi, Kurir/Ekspedisi -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Nomor Resi</label>
                <input type="text" class="form-control" id="tracking-number" name="tracking_number"
                    value="{{ $purchaseDelivery->tracking_number }}" placeholder="Masukkan nomor resi" />
            </div>
            <div class="col-md-6">
                <label class="form-label">Kurir/Ekspedisi</label>
                <input type="text" class="form-control" id="courier-name" name="courier_name"
                    value="{{ $purchaseDelivery->courier_name }}" placeholder="Nama kurir/ekspedisi" />
            </div>
        </div>

        <!-- Row 3: Gudang Tujuan -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Gudang Tujuan <span style="color: red;">*</span></label>
                <div class="row g-2">
                    <div class="col">
                        <select class="global-select2 form-select" id="purchase-delivery-warehouse"
                            name="warehouse_id" data-allow-clear="false" required>
                            <option value="">Pilih Gudang</option>
                            @foreach ($warehouses as $warehouse)
                                <option value="{{ $warehouse['value'] }}" {{ $purchaseDelivery->warehouse_id == $warehouse['value'] ? 'selected' : '' }}>
                                    {{ $warehouse['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @if ($canAddStorages)
                        <div class="col-auto">
                            <a href="{{ route('master_data.warehouse.add') }}" target="_blank"
                                class="btn btn-outline-primary d-flex align-items-center" title="Tambah Gudang Baru">
                                <i class="icon-base ti tabler-plus"></i>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Row 4: Keterangan -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Keterangan</label>
                <textarea class="form-control" id="delivery-description" name="description" rows="4"
                    placeholder="Keterangan delivery (optional)">{{ $purchaseDelivery->description }}</textarea>
                <small class="form-text text-muted">Catatan khusus terkait delivery</small>
            </div>
        </div>

        <!-- Informasi Tambahan Section -->
        <div class="section-header mt-12">
            <h6>Informasi Tambahan</h6>
            <hr class="mx-n6">
        </div>

        <!-- Split Layout: Tag, Memo & Keterangan -->
        <div class="row">
            <!-- Left Side: Tag and Memo -->
            <div class="col-md-6 mb-4">
                <div class="mb-3">
                    <label class="form-label">Tag</label>
                    <div class="row g-2">
                        <div class="col">
                            <input type="text" class="form-control" id="delivery-tag" name="tag"
                                value="{{ $purchaseDelivery->tag }}" placeholder="Masukkan tag (optional)" />
                        </div>
                        @if ($canAddTags)
                            <div class="col-auto">
                                <a href="{{ route('master_data.tag.add') }}" target="_blank"
                                    class="btn btn-outline-primary d-flex align-items-center" title="Tambah Tag Baru">
                                    <i class="icon-base ti tabler-plus"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                    <small class="form-text text-muted">Label/tag opsional untuk memudahkan pencarian/filter</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Memo (Internal)</label>
                    <textarea class="form-control" id="purchase-delivery-memo" name="memo" rows="4"
                        placeholder="Catatan internal...">{{ $purchaseDelivery->memo }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Lampiran</label>
                    @if($purchaseDelivery->attachment)
                        <div class="mb-2">
                            <small class="text-muted">File saat ini: {{ basename($purchaseDelivery->attachment) }}</small>
                        </div>
                    @endif
                    <input type="file" class="form-control" id="purchase-delivery-attachment" name="attachment"
                        accept=".xlsx,.xls,.doc,.docx,.pdf,.png,.jpg,.jpeg,.zip" />
                    <small class="form-text text-muted">File dapat berupa Excel, Word, PDF, PNG, atau Zip (Max: 10MB)</small>
                </div>
            </div>

        </div>
        
        <!-- Action Buttons -->
        <div class="pt-6 text-end">
            <a href="{{ route('main_transaction.purchase_delivery.detail', $purchaseDelivery->id) }}" class="btn btn-label-danger me-3">Batalkan</a>
            <button type="submit" class="btn btn-success" id="submit-btn">Update</button>
        </div>
    </form>
</div>
@stop

@section('js_script')
    <script>
        $(function() {
            // Form submission
            $('#purchase-delivery-form').on('submit', function(e) {
                e.preventDefault();

                // Validation
                const requiredFields = [
                    'purchase-delivery-date',
                    'purchase-delivery-po',
                    'delivery-number',
                    'delivery-date',
                    'purchase-delivery-warehouse'
                ];

                let isValid = true;

                requiredFields.forEach(function(fieldId) {
                    const field = $('#' + fieldId);
                    if (!field.val() || field.val().trim() === '') {
                        isValid = false;
                        field.addClass('is-invalid');
                    } else {
                        field.removeClass('is-invalid');
                    }
                });

                if (!isValid) {
                    Swal.fire({
                        title: 'Field Wajib!',
                        text: 'Silakan lengkapi semua field yang wajib diisi (bertanda *).',
                        icon: 'warning',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    return;
                }

                // Show confirmation
                Swal.fire({
                    title: 'Update Purchase Delivery?',
                    text: 'Pastikan data yang diisi sudah benar.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Update!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-label-secondary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitForm();
                    }
                });
            });

            function submitForm() {
                // Show loading state
                $('#submit-btn').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-2"></span>Loading...'
                );
                
                // Collect form data
                const formData = new FormData($('#purchase-delivery-form')[0]);
                
                // AJAX submission
                $.ajax({
                    url: "{{ route('main_transaction.purchase_delivery.update', $purchaseDelivery->id) }}",
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Purchase Delivery berhasil diperbarui.',
                            icon: 'success',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        }).then(() => {
                            window.location.href = "{{ route('main_transaction.purchase_delivery.detail', $purchaseDelivery->id) }}";
                        });
                    },
                    error: function(xhr) {
                        let errorMessage = 'Terjadi kesalahan saat menyimpan data.';
                        
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        Swal.fire({
                            title: 'Gagal!',
                            text: errorMessage,
                            icon: 'error',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        });
                    },
                    complete: function() {
                        $('#submit-btn').prop('disabled', false).html('Update');
                    }
                });
            }

            // PO selection handler for auto-fill
            $('#purchase-delivery-po').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                
                if (selectedOption.val()) {
                    // Auto-fill PO information
                    $('#po-supplier-display').val(selectedOption.data('supplier') || '');
                    $('#purchase-delivery-supplier').val(selectedOption.data('supplier-id') || '');
                    $('#purchase-delivery-email').val(selectedOption.data('email') || '');
                    $('#purchase-delivery-address').val(selectedOption.data('address') || '');
                    $('#purchase-order-reference').val(selectedOption.data('reference') || selectedOption.text());
                    
                    // Auto-fill warehouse from PO
                    const warehouseId = selectedOption.data('warehouse-id');
                    if (warehouseId) {
                        $('#purchase-delivery-warehouse').val(warehouseId).trigger('change');
                    }
                } else {
                    // Clear all auto-filled fields
                    $('#po-supplier-display, #purchase-delivery-email, #purchase-delivery-address, #purchase-order-reference').val('');
                    $('#purchase-delivery-supplier').val('');
                }
            });
        });
    </script>
@endsection