@extends('layouts.default')
@section('page_title')
    Edit Purchase Delivery - {{ $purchaseDelivery['reference_no'] }}
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Edit Purchase Delivery - {{ $purchaseDelivery['reference_no'] }}</h4>
        <form class="card-body" id="purchase-delivery-form" autocomplete="off">
            <input type="hidden" id="purchase-delivery-id" value="{{ $purchaseDelivery['id'] }}">
            @csrf
            @method('POST')
            
            <!-- Informasi Dasar Transaksi Section -->
            <div class="section-header mt-12">
                <h6>Informasi Dasar Transaksi</h6>
                <hr class="mx-n6">
            </div>
            
            <!-- Row 1: Tanggal Transaksi, Nomor Transaksi -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tanggal Transaksi <span style="color: red;">*</span></label>
                    <input type="date" class="form-control global-single-date-picker" id="purchase-delivery-date" name="date" value="{{ $purchaseDelivery['date'] }}" placeholder="YYYY-MM-DD" required />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nomor Transaksi</label>
                    <input type="text" class="form-control" id="purchase-delivery-reference" name="reference_no" value="{{ $purchaseDelivery['reference_no'] }}" readonly />
                </div>
            </div>

            <!-- Informasi Purchase Order Section -->
            <div class="section-header mt-12">
                <h6>Informasi Purchase Order</h6>
                <hr class="mx-n6">
            </div>

            <!-- Row 1: Purchase Order, Supplier -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Purchase Order <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="purchase-delivery-po" name="purchase_order_id" data-allow-clear="false" required>
                        <option value="">Pilih Purchase Order</option>
                        @foreach($purchaseOrders as $po)
                            <option value="{{ $po['value'] }}" @if($purchaseDelivery['po_reference'] == $po['value']) selected @endif>{{ $po['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Supplier <span style="color: red;">*</span></label>
                    <div class="row g-2">
                        <div class="col">
                            <select class="global-select2 form-select" id="purchase-delivery-supplier" name="supplier_id" data-allow-clear="false" required>
                                <option value="">Pilih Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier['value'] }}" @if($purchaseDelivery['supplier_id'] == $supplier['value']) selected @endif>{{ $supplier['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if($canAddSuppliers)
                            <div class="col-auto">
                                <a href="{{ route('master_data.contact.add') }}" target="_blank" class="btn btn-outline-primary d-flex align-items-center" title="Tambah Supplier Baru">
                                    <i class="icon-base ti tabler-plus"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Detail Pengiriman Section -->
            <div class="section-header mt-12">
                <h6>Detail Pengiriman</h6>
                <hr class="mx-n6">
            </div>

            <!-- Row 1: Storage, Carrier -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Gudang Tujuan <span style="color: red;">*</span></label>
                    <div class="row g-2">
                        <div class="col">
                            <select class="global-select2 form-select" id="purchase-delivery-warehouse" name="warehouse_id" data-allow-clear="false" required>
                                <option value="">Pilih Gudang</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse['value'] }}" @if($purchaseDelivery['warehouse_id'] == $warehouse['value']) selected @endif>{{ $warehouse['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if($canAddStorages)
                            <div class="col-auto">
                                <a href="{{ route('master_data.warehouse.add') }}" target="_blank" class="btn btn-outline-primary d-flex align-items-center" title="Tambah Gudang Baru">
                                    <i class="icon-base ti tabler-plus"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kurir/Transportasi <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="purchase-delivery-carrier" name="carrier" data-allow-clear="false" required>
                        <option value="">Pilih Kurir</option>
                        @foreach($carriers as $carrier)
                            <option value="{{ $carrier['value'] }}" @if($purchaseDelivery['carrier'] == $carrier['value']) selected @endif>{{ $carrier['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Row 2: Tracking Number, Status -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Nomor Resi</label>
                    <input type="text" class="form-control" id="purchase-delivery-tracking" name="tracking_number" value="{{ $purchaseDelivery['tracking_number'] }}" placeholder="Masukkan nomor resi" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="purchase-delivery-status" name="status" data-allow-clear="false" required>
                        <option value="">Pilih Status</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status['value'] }}" @if($purchaseDelivery['status'] == $status['value']) selected @endif>{{ $status['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Row 3: Total Items, Delivered Items -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Total Items</label>
                    <input type="number" class="form-control" value="{{ $purchaseDelivery['total_items'] }}" min="0" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Items Diterima <span style="color: red;">*</span></label>
                    <input type="number" class="form-control" id="purchase-delivery-delivered-items" name="delivered_items" value="{{ $purchaseDelivery['delivered_items'] }}" min="0" required />
                </div>
            </div>

            <!-- Informasi Tambahan Section -->
            <div class="section-header mt-12">
                <h6>Informasi Tambahan</h6>
                <hr class="mx-n6">
            </div>
            
            <!-- Row 1: Notes, Received By -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Catatan Pengiriman</label>
                    <textarea class="form-control" id="purchase-delivery-notes" name="delivery_notes" rows="4" placeholder="Catatan pengiriman...">{{ $purchaseDelivery['delivery_notes'] }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Diterima Oleh</label>
                    <select class="global-select2 form-select" id="purchase-delivery-received-by" name="received_by" data-allow-clear="false">
                        <option value="">Pilih Penerima</option>
                        @foreach($receiverOptions as $receiver)
                            <option value="{{ $receiver['value'] }}" @if($purchaseDelivery['received_by'] == $receiver['value']) selected @endif>{{ $receiver['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 text-end">
                <a href="{{ route('main_transaction.purchase_delivery.detail', $purchaseDelivery['id']) }}" class="btn btn-label-danger me-3">Batalkan</a>
                @if($canWrite)
                    <button type="submit" class="btn btn-success" id="submit-btn">Simpan</button>
                @endif
            </div>
        </form>
    </div>
@stop

@section('js_script')
<script>
$(function() {
    // Note: global-select2 and global-single-date-picker initialization is handled globally by layout
    
    // Form submission handler
    $('#purchase-delivery-form').on('submit', function(e) {
        e.preventDefault();
        
        // Show loading state
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.html('<i class="icon-base ti tabler-loader-2 me-2"></i>Menyimpan...').prop('disabled', true);
        
        // Gather form data
        const formData = {
            id: {{ $purchaseDelivery['id'] }},
            date: $('#purchase-delivery-date').val(),
            supplier_id: $('#purchase-delivery-supplier').val(),
            status: $('#purchase-delivery-status').val(),
            purchase_order_id: $('#purchase-delivery-po').val(),
            expected_delivery_date: $('#purchase-delivery-expected-date').val(),
            carrier: $('#purchase-delivery-carrier').val(),
            tracking_number: $('#purchase-delivery-tracking').val(),
            total_items: $('#purchase-delivery-total-items').val(),
            delivered_items: $('#purchase-delivery-delivered-items').val(),
            warehouse_id: $('#purchase-delivery-warehouse').val(),
            received_by: $('#purchase-delivery-received-by').val(),
            delivery_notes: $('#purchase-delivery-notes').val()
        };
        
        // Validate required fields
        if (!formData.date || !formData.supplier_id || !formData.status || !formData.purchase_order_id || !formData.delivered_items) {
            Swal.fire({
                title: 'Kesalahan Validasi',
                text: 'Harap lengkapi semua field yang wajib diisi (bertanda *).',
                icon: 'error',
                customClass: {
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false
            });
            
            // Reset button state
            submitBtn.html(originalText).prop('disabled', false);
            return;
        }
        
        // Validate delivered items not exceeding total items
        const totalItems = parseInt(formData.total_items) || 0;
        const deliveredItems = parseInt(formData.delivered_items) || 0;
        
        if (deliveredItems > totalItems) {
            Swal.fire({
                title: 'Kesalahan Validasi',
                text: 'Items yang diterima tidak boleh lebih besar dari total items.',
                icon: 'error',
                customClass: {
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false
            });
            
            // Reset button state
            submitBtn.html(originalText).prop('disabled', false);
            return;
        }
        
        // Submit data via AJAX
        $.ajax({
            url: '{{ route("main_transaction.purchase_delivery.update") }}',
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: response.message || 'Purchase Delivery berhasil diperbarui.',
                    icon: 'success',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    // Redirect to detail page
                    window.location.href = '{{ route("main_transaction.purchase_delivery.detail", $purchaseDelivery["id"]) }}';
                });
            },
            error: function(xhr, status, error) {
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
                
                // Reset button state
                submitBtn.html(originalText).prop('disabled', false);
            }
        });
    });
    
    // Auto-validate delivered items against total items
    $('#purchase-delivery-total-items, #purchase-delivery-delivered-items').on('input', function() {
        const totalItems = parseInt($('#purchase-delivery-total-items').val()) || 0;
        const deliveredItems = parseInt($('#purchase-delivery-delivered-items').val()) || 0;
        
        if (deliveredItems > totalItems) {
            $('#purchase-delivery-delivered-items').addClass('is-invalid');
            if (!$('#purchase-delivery-delivered-items').next('.invalid-feedback').length) {
                $('#purchase-delivery-delivered-items').after('<div class="invalid-feedback">Items diterima tidak boleh lebih dari total items</div>');
            }
        } else {
            $('#purchase-delivery-delivered-items').removeClass('is-invalid');
            $('#purchase-delivery-delivered-items').next('.invalid-feedback').remove();
        }
    });
    
    // PO Reference change handler - could trigger loading of PO-specific data
    $('#purchase-delivery-po').on('change', function() {
        const poReference = $(this).val();
        if (poReference) {
            // Load PO-specific data like supplier, items, etc.
            console.log('Selected PO Reference: ' + poReference);
        }
    });
    
    // Status change handler for conditional logic
    $('#purchase-delivery-status').on('change', function() {
        const status = $(this).val();
        
        // Auto-populate delivered items when status is DELIVERED
        if (status === 'DELIVERED') {
            const totalItems = $('#purchase-delivery-total-items').val();
            if (totalItems && !$('#purchase-delivery-delivered-items').val()) {
                $('#purchase-delivery-delivered-items').val(totalItems);
            }
        }
    });
});
</script>
@endsection