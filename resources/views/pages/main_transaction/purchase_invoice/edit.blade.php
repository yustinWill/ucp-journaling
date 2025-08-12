@extends('layouts.default')
@section('page_title')
    Edit Purchase Invoice - {{ $purchaseInvoice['reference_no'] }}
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Edit Purchase Invoice - {{ $purchaseInvoice['reference_no'] }}</h4>
        <form class="card-body" id="purchaseInvoiceEditForm" autocomplete="off">
            <!-- Basic Information Section -->
            <div class="section-header mt-12">
                <h6>Informasi Dasar</h6>
                <hr class="mx-n6">
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">No. Purchase Invoice</label>
                    <input type="text" class="form-control" id="reference-no" value="{{ $purchaseInvoice['reference_no'] }}" readonly />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal <span style="color: red;">*</span></label>
                    <input type="date" class="form-control global-single-date-picker" id="invoice-date" value="{{ $purchaseInvoice['date'] }}" />
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
                                    <option value="{{ $supplier['value'] }}" @if($purchaseInvoice['supplier_id'] == $supplier['value']) selected @endif>{{ $supplier['label'] }}</option>
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
                            <option value="{{ $status['value'] }}" @if($purchaseInvoice['status'] == $status['value']) selected @endif>{{ $status['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Reference Information Section -->
            <div class="section-header mt-12">
                <h6>Referensi</h6>
                <hr class="mx-n6">
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">PO Reference <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="po-reference-select" data-allow-clear="false">
                        <option value="">Pilih Purchase Order</option>
                        @foreach($purchaseOrders as $po)
                            <option value="{{ $po['value'] }}" @if($purchaseInvoice['po_reference'] == $po['value']) selected @endif>{{ $po['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">PD Reference</label>
                    <select class="global-select2 form-select" id="pd-reference-select" data-allow-clear="false">
                        <option value="">Pilih Purchase Delivery</option>
                        @foreach($purchaseDeliveries as $pd)
                            <option value="{{ $pd['value'] }}" @if($purchaseInvoice['pd_reference'] == $pd['value']) selected @endif>{{ $pd['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tanggal Jatuh Tempo</label>
                    <input type="date" class="form-control global-single-date-picker" id="due-date" value="{{ $purchaseInvoice['due_date'] }}" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Currency <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="currency-select" data-allow-clear="false">
                        <option value="">Pilih Currency</option>
                        @foreach($currencies as $currency)
                            <option value="{{ $currency['value'] }}" @if($purchaseInvoice['currency'] == $currency['value']) selected @endif>{{ $currency['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Financial Information Section -->
            <div class="section-header mt-12">
                <h6>Informasi Keuangan</h6>
                <hr class="mx-n6">
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Subtotal <span style="color: red;">*</span></label>
                    <input type="number" class="form-control" id="subtotal" value="{{ $purchaseInvoice['subtotal'] }}" min="0" step="0.01" required />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tax Amount</label>
                    <input type="number" class="form-control" id="tax-amount" value="{{ $purchaseInvoice['tax_amount'] }}" min="0" step="0.01" />
                </div>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Discount</label>
                    <input type="number" class="form-control" id="discount" value="{{ $purchaseInvoice['discount'] }}" min="0" step="0.01" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Total Invoice <span style="color: red;">*</span></label>
                    <input type="number" class="form-control fw-bold" id="invoice-amount" value="{{ $purchaseInvoice['invoice_amount'] }}" min="0" step="0.01" required readonly />
                </div>
            </div>

            <!-- Payment Information Section -->
            <div class="section-header mt-12">
                <h6>Informasi Pembayaran</h6>
                <hr class="mx-n6">
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Dibayar</label>
                    <input type="number" class="form-control" id="paid-amount" value="{{ $purchaseInvoice['paid_amount'] }}" min="0" step="0.01" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Payment Terms</label>
                    <select class="global-select2 form-select" id="payment-terms-select" data-allow-clear="false">
                        <option value="">Pilih Payment Terms</option>
                        @foreach($paymentTerms as $term)
                            <option value="{{ $term['value'] }}" @if($purchaseInvoice['payment_terms'] == $term['value']) selected @endif>{{ $term['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Payment Method</label>
                    <select class="global-select2 form-select" id="payment-method-select" data-allow-clear="false">
                        <option value="">Pilih Payment Method</option>
                        @foreach($paymentMethods as $method)
                            <option value="{{ $method['value'] }}" @if($purchaseInvoice['payment_method'] == $method['value']) selected @endif>{{ $method['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Bank Account</label>
                    <select class="global-select2 form-select" id="bank-account-select" data-allow-clear="false">
                        <option value="">Pilih Bank Account</option>
                        @foreach($bankAccounts as $account)
                            <option value="{{ $account['value'] }}" @if($purchaseInvoice['bank_account'] == $account['value']) selected @endif>{{ $account['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <label class="form-label">Catatan Invoice</label>
                    <textarea class="form-control" id="invoice-notes" rows="3" placeholder="Masukkan catatan invoice..." maxlength="500">{{ $purchaseInvoice['invoice_notes'] }}</textarea>
                    <small class="form-text text-muted">Maksimal 500 karakter</small>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-end gap-3 mt-6">
                <a href="{{ route('main_transaction.purchase_invoice.detail', $purchaseInvoice['id']) }}" class="btn btn-outline-secondary waves-effect">
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
    
    // Auto-calculate invoice amount when financial fields change
    function calculateInvoiceAmount() {
        const subtotal = parseFloat($('#subtotal').val()) || 0;
        const taxAmount = parseFloat($('#tax-amount').val()) || 0;
        const discount = parseFloat($('#discount').val()) || 0;
        
        const invoiceAmount = subtotal + taxAmount - discount;
        $('#invoice-amount').val(invoiceAmount.toFixed(2));
    }
    
    // Bind calculation to relevant fields
    $('#subtotal, #tax-amount, #discount').on('input', calculateInvoiceAmount);
    
    // Form submission handler
    $('#purchaseInvoiceEditForm').on('submit', function(e) {
        e.preventDefault();
        
        // Show loading state
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.html('<i class="icon-base ti tabler-loader-2 me-2"></i>Menyimpan...').prop('disabled', true);
        
        // Gather form data
        const formData = {
            id: {{ $purchaseInvoice['id'] }},
            date: $('#invoice-date').val(),
            supplier_id: $('#supplier-select').val(),
            status: $('#status-select').val(),
            po_reference: $('#po-reference-select').val(),
            pd_reference: $('#pd-reference-select').val(),
            due_date: $('#due-date').val(),
            currency: $('#currency-select').val(),
            subtotal: $('#subtotal').val(),
            tax_amount: $('#tax-amount').val(),
            discount: $('#discount').val(),
            invoice_amount: $('#invoice-amount').val(),
            paid_amount: $('#paid-amount').val(),
            payment_terms: $('#payment-terms-select').val(),
            payment_method: $('#payment-method-select').val(),
            bank_account: $('#bank-account-select').val(),
            invoice_notes: $('#invoice-notes').val()
        };
        
        // Validate required fields
        if (!formData.date || !formData.supplier_id || !formData.status || !formData.po_reference || !formData.subtotal || !formData.invoice_amount || !formData.currency) {
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
        
        // Validate paid amount not exceeding invoice amount
        const invoiceAmount = parseFloat(formData.invoice_amount) || 0;
        const paidAmount = parseFloat(formData.paid_amount) || 0;
        
        if (paidAmount > invoiceAmount) {
            Swal.fire({
                title: 'Kesalahan Validasi',
                text: 'Jumlah yang dibayar tidak boleh lebih besar dari total invoice.',
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
            url: '{{ route("main_transaction.purchase_invoice.update") }}',
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: response.message || 'Purchase Invoice berhasil diperbarui.',
                    icon: 'success',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    // Redirect to detail page
                    window.location.href = '{{ route("main_transaction.purchase_invoice.detail", $purchaseInvoice["id"]) }}';
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
    
    // Auto-validate paid amount against invoice amount
    $('#paid-amount, #invoice-amount').on('input', function() {
        const invoiceAmount = parseFloat($('#invoice-amount').val()) || 0;
        const paidAmount = parseFloat($('#paid-amount').val()) || 0;
        
        if (paidAmount > invoiceAmount) {
            $('#paid-amount').addClass('is-invalid');
            if (!$('#paid-amount').next('.invalid-feedback').length) {
                $('#paid-amount').after('<div class="invalid-feedback">Jumlah yang dibayar tidak boleh lebih dari total invoice</div>');
            }
        } else {
            $('#paid-amount').removeClass('is-invalid');
            $('#paid-amount').next('.invalid-feedback').remove();
        }
    });
    
    // PO Reference change handler - could trigger loading of PO-specific data
    $('#po-reference-select').on('change', function() {
        const poReference = $(this).val();
        if (poReference) {
            // Load PO-specific data like supplier, amounts, etc.
            console.log('Selected PO Reference: ' + poReference);
        }
    });
    
    // Status change handler for conditional logic
    $('#status-select').on('change', function() {
        const status = $(this).val();
        
        // Auto-populate paid amount when status is PAID
        if (status === 'PAID') {
            const invoiceAmount = $('#invoice-amount').val();
            if (invoiceAmount && !$('#paid-amount').val()) {
                $('#paid-amount').val(invoiceAmount);
            }
        }
    });
    
    // Payment method change handler
    $('#payment-method-select').on('change', function() {
        const method = $(this).val();
        
        // Show/hide bank account field based on payment method
        if (method === 'Transfer Bank') {
            $('#bank-account-select').closest('.col-md-6').show();
        } else if (method === 'Cash') {
            $('#bank-account-select').closest('.col-md-6').hide();
            $('#bank-account-select').val('').trigger('change');
        }
    });
    
    // Initialize calculation
    calculateInvoiceAmount();
});
</script>
@endsection