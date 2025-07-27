@extends('layouts.default')
@section('page_title')
    Edit Purchase Order - {{ $purchaseOrder['reference_no'] }}
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Edit Purchase Order - {{ $purchaseOrder['reference_no'] }}</h4>
        <form class="card-body" id="purchase-order-form">
            <input type="hidden" id="purchase-order-id" value="{{ $purchaseOrder['id'] }}">
            @csrf
            @method('POST')
            <!-- Basic Information Section -->
            <div class="section-header mt-12">
                <h6>Informasi Dasar</h6>
                <hr class="mx-n6">
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">No. Purchase Order</label>
                    <input type="text" class="form-control" id="reference-no" value="{{ $purchaseOrder['reference_no'] }}" readonly />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal <span style="color: red;">*</span></label>
                    <input type="date" class="form-control global-single-date-picker" id="po-date" value="{{ $purchaseOrder['date'] }}" />
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
                                    <option value="{{ $supplier['value'] }}" @if($purchaseOrder['supplier_id'] == $supplier['value']) selected @endif>{{ $supplier['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if($canAddSuppliers)
                            <div class="col-auto">
                                <a href="{{ route('master_data.contact.add') }}" target="_blank" class="btn btn-outline-primary d-flex align-items-center" 
                                   title="Tambah Supplier Baru">
                                    <i class="icon-base ti tabler-plus"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="status-select" data-allow-clear="false">
                        <option value="">Pilih Status</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status['value'] }}" @if($purchaseOrder['status'] == $status['value']) selected @endif>{{ $status['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tanggal Jatuh Tempo</label>
                    <input type="date" class="form-control global-single-date-picker" id="due-date" value="{{ $purchaseOrder['due_date'] }}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Currency <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="currency-select" data-allow-clear="false">
                        <option value="">Pilih Currency</option>
                        @foreach($currencies as $currency)
                            <option value="{{ $currency['value'] }}" @if($purchaseOrder['currency'] == $currency['value']) selected @endif>{{ $currency['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Keterangan</label>
                    <textarea class="form-control" id="description" name="description" rows="3" placeholder="Keterangan transaksi...">{{ $purchaseOrder['description'] }}</textarea>
                </div>
                <div class="col-md-6">
                    <!-- Empty for alignment -->
                </div>
            </div>

            <!-- Financial Information Section -->
            <div class="section-header mt-12">
                <h6>Informasi Keuangan</h6>
                <hr class="mx-n6">
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Down Payment</label>
                    <input type="number" class="form-control" id="down-payment" value="{{ $purchaseOrder['down_payment'] }}" min="0" step="0.01" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Total <span style="color: red;">*</span></label>
                    <input type="number" class="form-control" id="total-amount" value="{{ $purchaseOrder['total'] }}" min="0" step="0.01" required />
                </div>
            </div>

            <!-- Additional Information Section -->
            <div class="section-header mt-12">
                <h6>Informasi Tambahan</h6>
                <hr class="mx-n6">
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Payment Method</label>
                    <select class="global-select2 form-select" id="payment-method-select" data-allow-clear="false">
                        <option value="">Pilih Payment Method</option>
                        @foreach($paymentMethods as $method)
                            <option value="{{ $method['value'] }}" @if($purchaseOrder['payment_method'] == $method['value']) selected @endif>{{ $method['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Gudang</label>
                    <div class="row g-2">
                        <div class="col">
                            <select class="global-select2 form-select" id="warehouse-select" data-allow-clear="false">
                                <option value="">Pilih Gudang</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse['value'] }}" @if($purchaseOrder['warehouse_id'] == $warehouse['value']) selected @endif>{{ $warehouse['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if($canAddWarehouses)
                            <div class="col-auto">
                                <a href="{{ route('master_data.storage.add') }}" target="_blank" class="btn btn-outline-primary d-flex align-items-center" 
                                   title="Tambah Gudang Baru">
                                    <i class="icon-base ti tabler-plus"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">PIC</label>
                    <div class="row g-2">
                        <div class="col">
                            <select class="global-select2 form-select" id="pic-select" data-allow-clear="false">
                                <option value="">Pilih PIC</option>
                                @foreach($picOptions as $pic)
                                    <option value="{{ $pic['value'] }}" @if($purchaseOrder['pic_id'] == $pic['value']) selected @endif>{{ $pic['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if($canAddPIC)
                            <div class="col-auto">
                                <a href="{{ route('master_data.user.add') }}" target="_blank" class="btn btn-outline-primary d-flex align-items-center" 
                                   title="Tambah User Baru">
                                    <i class="icon-base ti tabler-plus"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Payment Terms</label>
                    <select class="global-select2 form-select" id="payment-terms-select" data-allow-clear="false">
                        <option value="">Pilih Payment Terms</option>
                        @foreach($paymentTerms as $term)
                            <option value="{{ $term['value'] }}" @if($purchaseOrder['payment_terms'] == $term['value']) selected @endif>{{ $term['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tag</label>
                    <div class="row g-2">
                        <div class="col">
                            <select class="global-select2 form-select" id="tags-select" name="tags[]" multiple data-allow-clear="false">
                                @foreach($availableTags as $tag)
                                    <option value="{{ $tag['code'] }}" data-preview="{{ $tag['preview'] }}" 
                                        @if(isset($purchaseOrder['tags']) && is_array($purchaseOrder['tags']) && in_array($tag['code'], array_column($purchaseOrder['tags'], 'code'))) selected @endif>
                                        {{ $tag['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @if($canAddTags)
                            <div class="col-auto">
                                <a href="{{ route('master_data.tag.add') }}" target="_blank" class="btn btn-outline-primary d-flex align-items-center" title="Tambah Tag Baru">
                                    <i class="icon-base ti tabler-plus"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                    <small class="form-text text-muted">Pilih satu atau lebih tag untuk kategorisasi</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Memo <small class="text-muted">(Internal)</small></label>
                    <textarea class="form-control" id="memo" name="memo" rows="3" placeholder="Catatan internal..." maxlength="500">{{ $purchaseOrder['memo'] }}</textarea>
                    <small class="form-text text-muted">Maksimal 500 karakter - Hanya untuk keperluan internal</small>
                </div>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Catatan</label>
                    <textarea class="form-control" id="notes" rows="3" placeholder="Masukkan catatan untuk purchase order..." maxlength="500">{{ $purchaseOrder['notes'] }}</textarea>
                    <small class="form-text text-muted">Maksimal 500 karakter</small>
                </div>
                <div class="col-md-6">
                    <!-- Empty for alignment -->
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 text-end">
                <a href="{{ route('main_transaction.purchase_order.detail', $purchaseOrder['id']) }}" class="btn btn-label-danger me-3">Batalkan</a>
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
    
    // Initialize payment terms handling
    handlePaymentTermsChange();
    
    // Handle payment terms change
    $('#payment-terms-select').on('change', function() {
        handlePaymentTermsChange();
        updateDueDate();
    });
    
    // Handle transaction date change
    $('#po-date').on('change', updateDueDate);
    
    function handlePaymentTermsChange() {
        const paymentTerms = $('#payment-terms-select').val();
        const dueDateField = $('#due-date');
        
        if (paymentTerms === 'Custom') {
            // Enable due date field for custom payment terms
            dueDateField.prop('disabled', false);
        } else {
            // Disable due date field and auto-calculate for standard terms
            dueDateField.prop('disabled', true);
        }
    }
    
    function updateDueDate() {
        const transactionDate = $('#po-date').val();
        const paymentTerms = $('#payment-terms-select').val();
        const dueDateField = $('#due-date');
        
        // Only auto-calculate for non-custom payment terms
        if (transactionDate && paymentTerms && paymentTerms !== 'Custom') {
            const date = new Date(transactionDate);
            let days = 0;
            
            // Extract days from payment terms
            if (paymentTerms.includes('Net ')) {
                days = parseInt(paymentTerms.replace('Net ', '')) || 0;
            } else if (paymentTerms === 'Cash on Delivery' || paymentTerms === 'Immediate') {
                days = 0;
            }
            
            date.setDate(date.getDate() + days);
            const dueDate = date.toISOString().split('T')[0];
            dueDateField.val(dueDate);
        }
    }
    
    // Form submission handler
    $('#purchase-order-form').on('submit', function(e) {
        e.preventDefault();
        
        // Show loading state
        $('#submit-btn').prop('disabled', true).html(
            '<span class="spinner-border spinner-border-sm me-2"></span>Loading...'
        );
        
        // Gather form data
        const formData = {
            _token: '{{ csrf_token() }}',
            id: $('#purchase-order-id').val(),
            date: $('#po-date').val(),
            supplier_id: $('#supplier-select').val(),
            status: $('#status-select').val(),
            due_date: $('#due-date').val(),
            description: $('#description').val(),
            down_payment: $('#down-payment').val(),
            total: $('#total-amount').val(),
            currency: $('#currency-select').val(),
            payment_method: $('#payment-method-select').val(),
            warehouse_id: $('#warehouse-select').val(),
            pic_id: $('#pic-select').val(),
            payment_terms: $('#payment-terms-select').val(),
            tags: $('#tags-select').val(),
            memo: $('#memo').val(),
            notes: $('#notes').val()
        };
        
        // Validate required fields
        if (!formData.date || !formData.supplier_id || !formData.status || !formData.total || !formData.currency) {
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
            $('#submit-btn').prop('disabled', false).html('Simpan');
            return;
        }
        
        // Submit data via AJAX
        $.ajax({
            url: '{{ route("main_transaction.purchase_order.update") }}',
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: response.message || 'Purchase Order berhasil diperbarui.',
                    icon: 'success',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    // Redirect to detail page
                    window.location.href = '{{ route("main_transaction.purchase_order.detail", $purchaseOrder["id"]) }}';
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
                $('#submit-btn').prop('disabled', false).html('Simpan');
            }
        });
    });
    
    // Auto-calculate remaining amount when down payment changes
    $('#down-payment, #total-amount').on('input', function() {
        const total = parseFloat($('#total-amount').val()) || 0;
        const downPayment = parseFloat($('#down-payment').val()) || 0;
        const remaining = total - downPayment;
        
        // You can add a display field for remaining amount if needed
        console.log('Remaining amount: ' + remaining);
    });
    
    // Supplier change handler - could trigger loading of supplier-specific data
    $('#supplier-select').on('change', function() {
        const supplierId = $(this).val();
        if (supplierId) {
            // Load supplier-specific payment terms, etc.
            console.log('Selected supplier ID: ' + supplierId);
        }
    });
});
</script>
@endsection