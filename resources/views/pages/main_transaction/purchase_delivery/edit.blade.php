@extends('layouts.default')
@section('page_title')
    Edit Purchase Delivery - {{ $purchaseDelivery['reference_no'] }}
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Edit Purchase Delivery - {{ $purchaseDelivery['reference_no'] }}</h4>
        <form class="card-body" id="purchaseDeliveryEditForm">
            <!-- Basic Information Section -->
            <div class="section-header mt-12">
                <h6>Informasi Dasar</h6>
                <hr class="mx-n6">
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">No. Purchase Delivery</label>
                    <input type="text" class="form-control" id="reference-no" value="{{ $purchaseDelivery['reference_no'] }}" readonly />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal <span style="color: red;">*</span></label>
                    <input type="date" class="form-control global-single-date-picker" id="delivery-date" value="{{ $purchaseDelivery['date'] }}" />
                </div>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Supplier <span style="color: red;">*</span></label>
                    <div class="row g-2">
                        <div class="col">
                            <select class="global-select2 form-select" id="supplier-select" data-allow-clear="false">
                                <option value="">Pilih Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier['value'] }}" @if($purchaseDelivery['supplier_id'] == $supplier['value']) selected @endif>{{ $supplier['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('master_data.contact.add') }}" target="_blank" class="btn btn-outline-primary d-flex align-items-center" 
                               title="Tambah Supplier Baru">
                                <i class="icon-base ti tabler-plus"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="status-select" data-allow-clear="false">
                        <option value="">Pilih Status</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status['value'] }}" @if($purchaseDelivery['status'] == $status['value']) selected @endif>{{ $status['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Purchase Order Reference Section -->
            <div class="section-header mt-12">
                <h6>Referensi Purchase Order</h6>
                <hr class="mx-n6">
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">PO Reference <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="po-reference-select" data-allow-clear="false">
                        <option value="">Pilih Purchase Order</option>
                        @foreach($purchaseOrders as $po)
                            <option value="{{ $po['value'] }}" @if($purchaseDelivery['po_reference'] == $po['value']) selected @endif>{{ $po['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Pengiriman</label>
                    <input type="date" class="form-control global-single-date-picker" id="delivery-date-actual" value="{{ $purchaseDelivery['delivery_date'] }}" />
                </div>
            </div>

            <!-- Shipping Information Section -->
            <div class="section-header mt-12">
                <h6>Informasi Pengiriman</h6>
                <hr class="mx-n6">
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Carrier</label>
                    <select class="global-select2 form-select" id="carrier-select" data-allow-clear="false">
                        <option value="">Pilih Carrier</option>
                        @foreach($carriers as $carrier)
                            <option value="{{ $carrier['value'] }}" @if($purchaseDelivery['carrier'] == $carrier['value']) selected @endif>{{ $carrier['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tracking Number</label>
                    <input type="text" class="form-control" id="tracking-number" value="{{ $purchaseDelivery['tracking_number'] }}" placeholder="Masukkan nomor tracking" />
                </div>
            </div>

            <!-- Items Information Section -->
            <div class="section-header mt-12">
                <h6>Informasi Barang</h6>
                <hr class="mx-n6">
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Total Items <span style="color: red;">*</span></label>
                    <input type="number" class="form-control" id="total-items" value="{{ $purchaseDelivery['total_items'] }}" min="0" required />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Items Diterima <span style="color: red;">*</span></label>
                    <input type="number" class="form-control" id="delivered-items" value="{{ $purchaseDelivery['delivered_items'] }}" min="0" required />
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
                    <div class="row g-2">
                        <div class="col">
                            <select class="global-select2 form-select" id="warehouse-select" data-allow-clear="false">
                                <option value="">Pilih Gudang</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse['value'] }}" @if($purchaseDelivery['warehouse_id'] == $warehouse['value']) selected @endif>{{ $warehouse['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('master_data.storage.add') }}" target="_blank" class="btn btn-outline-primary d-flex align-items-center" 
                               title="Tambah Gudang Baru">
                                <i class="icon-base ti tabler-plus"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Diterima Oleh</label>
                    <select class="global-select2 form-select" id="received-by-select" data-allow-clear="false">
                        <option value="">Pilih Penerima</option>
                        @foreach($receiverOptions as $receiver)
                            <option value="{{ $receiver['value'] }}" @if($purchaseDelivery['received_by'] == $receiver['value']) selected @endif>{{ $receiver['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <label class="form-label">Catatan Pengiriman</label>
                    <textarea class="form-control" id="delivery-notes" rows="3" placeholder="Masukkan catatan pengiriman..." maxlength="500">{{ $purchaseDelivery['delivery_notes'] }}</textarea>
                    <small class="form-text text-muted">Maksimal 500 karakter</small>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-end gap-3 mt-6">
                <a href="{{ route('main_transaction.purchase_delivery.detail', $purchaseDelivery['id']) }}" class="btn btn-outline-secondary waves-effect">
                    <i class="icon-base ti tabler-arrow-left me-2"></i>
                    Kembali
                </a>
                @if($canWrite)
                    <button type="submit" class="btn btn-primary waves-effect waves-light">
                        <i class="icon-base ti tabler-device-floppy me-2"></i>
                        Simpan Perubahan
                    </button>
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
    $('#purchaseDeliveryEditForm').on('submit', function(e) {
        e.preventDefault();
        
        // Show loading state
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.html('<i class="icon-base ti tabler-loader-2 me-2"></i>Menyimpan...').prop('disabled', true);
        
        // Gather form data
        const formData = {
            id: {{ $purchaseDelivery['id'] }},
            date: $('#delivery-date').val(),
            supplier_id: $('#supplier-select').val(),
            status: $('#status-select').val(),
            po_reference: $('#po-reference-select').val(),
            delivery_date: $('#delivery-date-actual').val(),
            carrier: $('#carrier-select').val(),
            tracking_number: $('#tracking-number').val(),
            total_items: $('#total-items').val(),
            delivered_items: $('#delivered-items').val(),
            warehouse_id: $('#warehouse-select').val(),
            received_by: $('#received-by-select').val(),
            delivery_notes: $('#delivery-notes').val()
        };
        
        // Validate required fields
        if (!formData.date || !formData.supplier_id || !formData.status || !formData.po_reference || !formData.total_items || !formData.delivered_items) {
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
    $('#total-items, #delivered-items').on('input', function() {
        const totalItems = parseInt($('#total-items').val()) || 0;
        const deliveredItems = parseInt($('#delivered-items').val()) || 0;
        
        if (deliveredItems > totalItems) {
            $('#delivered-items').addClass('is-invalid');
            if (!$('#delivered-items').next('.invalid-feedback').length) {
                $('#delivered-items').after('<div class="invalid-feedback">Items diterima tidak boleh lebih dari total items</div>');
            }
        } else {
            $('#delivered-items').removeClass('is-invalid');
            $('#delivered-items').next('.invalid-feedback').remove();
        }
    });
    
    // PO Reference change handler - could trigger loading of PO-specific data
    $('#po-reference-select').on('change', function() {
        const poReference = $(this).val();
        if (poReference) {
            // Load PO-specific data like supplier, items, etc.
            console.log('Selected PO Reference: ' + poReference);
        }
    });
    
    // Status change handler for conditional logic
    $('#status-select').on('change', function() {
        const status = $(this).val();
        
        // Auto-populate delivered items when status is DELIVERED
        if (status === 'DELIVERED') {
            const totalItems = $('#total-items').val();
            if (totalItems && !$('#delivered-items').val()) {
                $('#delivered-items').val(totalItems);
            }
        }
    });
});
</script>
@endsection