@extends('layouts.default')
@section('page_title')
    Buat Purchase Invoice
@stop

@section('content')
<div class="card mb-6">
    <h4 class="card-header">Buat Purchase Invoice</h4>
    <form class="card-body" id="purchase-invoice-form">
        @csrf
        
        <!-- Informasi Dasar Transaksi Section -->
        <div class="section-header mt-12">
            <h6>Informasi Dasar Transaksi</h6>
            <hr class="mx-n6">
        </div>
        
        <!-- Row 1: Tanggal Transaksi, Nomor Transaksi -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Tanggal Transaksi <span style="color: red;">*</span></label>
                <input type="date" class="form-control global-single-date-picker" id="purchase-invoice-date" name="date" value="{{ date('Y-m-d') }}" placeholder="YYYY-MM-DD" required />
            </div>
            <div class="col-md-6">
                <label class="form-label">Nomor Transaksi <span style="color: red;">*</span></label>
                <div class="row g-2">
                    <div class="col">
                        <input type="text" class="form-control" id="purchase-invoice-reference" name="reference_no" placeholder="Auto-generated but editable" required />
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-outline-primary d-flex align-items-center" id="generate-pi-number-btn" title="Generate New Number">
                            <i class="icon-base ti tabler-refresh"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informasi Supplier Section -->
        <div class="section-header mt-12">
            <h6>Informasi Supplier</h6>
            <hr class="mx-n6">
        </div>

        <!-- Row 1: Supplier, Purchase Order -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Supplier <span style="color: red;">*</span></label>
                <select class="global-select2 form-select" id="purchase-invoice-supplier" name="supplier_id" data-allow-clear="false" required>
                    <option value="">Pilih Supplier</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier['value'] }}">{{ $supplier['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Purchase Order</label>
                <select class="global-select2 form-select" id="purchase-invoice-po" name="purchase_order_id" data-allow-clear="false">
                    <option value="">Pilih Purchase Order</option>
                    @foreach($purchaseOrders as $po)
                        <option value="{{ $po['value'] }}">{{ $po['label'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Row 2: Purchase Delivery -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Purchase Delivery</label>
                <select class="global-select2 form-select" id="purchase-invoice-pd" name="purchase_delivery_id" data-allow-clear="false">
                    <option value="">Pilih Purchase Delivery</option>
                    @foreach($purchaseDeliveries as $pd)
                        <option value="{{ $pd['value'] }}">{{ $pd['label'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Detail Invoice Section -->
        <div class="section-header mt-12">
            <h6>Detail Invoice</h6>
            <hr class="mx-n6">
        </div>

        <!-- Row 1: Due Date, Payment Terms -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Tanggal Jatuh Tempo <span style="color: red;">*</span></label>
                <input type="date" class="form-control global-single-date-picker" id="purchase-invoice-due-date" name="due_date" placeholder="YYYY-MM-DD" required />
            </div>
            <div class="col-md-6">
                <label class="form-label">Syarat Pembayaran</label>
                <select class="global-select2 form-select" id="purchase-invoice-payment-terms" name="payment_terms" data-allow-clear="false">
                    <option value="">Pilih Payment Terms</option>
                    @foreach($paymentTerms as $term)
                        <option value="{{ $term['value'] }}">{{ $term['label'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Row 2: Currency, Invoice Amount -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Mata Uang</label>
                <select class="global-select2 form-select" id="purchase-invoice-currency" name="currency" data-allow-clear="false">
                    <option value="IDR" selected>IDR - Rupiah</option>
                    @foreach($currencies as $currency)
                        <option value="{{ $currency['value'] }}">{{ $currency['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Jumlah Invoice <span style="color: red;">*</span></label>
                <input type="number" class="form-control" id="purchase-invoice-amount" name="invoice_amount" min="0" step="0.01" placeholder="0.00" required />
            </div>
        </div>

        <!-- Row 3: Tax Amount, Total Amount -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Jumlah Pajak</label>
                <input type="number" class="form-control" id="purchase-invoice-tax" name="tax_amount" min="0" step="0.01" placeholder="0.00" />
            </div>
            <div class="col-md-6">
                <label class="form-label">Total Amount <span style="color: red;">*</span></label>
                <input type="number" class="form-control" id="purchase-invoice-total" name="total_amount" min="0" step="0.01" placeholder="0.00" required />
            </div>
        </div>

        <!-- Informasi Tambahan Section -->
        <div class="section-header mt-12">
            <h6>Informasi Tambahan</h6>
            <hr class="mx-n6">
        </div>
        
        <!-- Row 1: Notes, Attachment -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Catatan</label>
                <textarea class="form-control" id="purchase-invoice-notes" name="notes" rows="4" placeholder="Catatan invoice..."></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Lampiran</label>
                <input type="file" class="form-control" id="purchase-invoice-attachment" name="attachment" accept=".xlsx,.xls,.doc,.docx,.pdf,.png,.jpg,.jpeg,.zip" />
                <small class="form-text text-muted">File dapat berupa Excel, Word, PDF, PNG, atau Zip (Max: 10MB)</small>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="pt-6 text-end">
            <a href="{{ route('main_transaction.purchase_invoice.index') }}" class="btn btn-label-danger me-3">Batalkan</a>
            <button type="submit" class="btn btn-success" id="submit-btn">Simpan</button>
        </div>
    </form>
</div>
@stop

@section('js_script')
<script>
$(function() {
    // Initialize transaction number
    initializePITransactionNumber();
    
    // Auto-calculate total when invoice amount or tax amount changes
    $('#purchase-invoice-amount, #purchase-invoice-tax').on('input', function() {
        calculateTotal();
    });
    
    function calculateTotal() {
        const invoiceAmount = parseFloat($('#purchase-invoice-amount').val()) || 0;
        const taxAmount = parseFloat($('#purchase-invoice-tax').val()) || 0;
        const total = invoiceAmount + taxAmount;
        $('#purchase-invoice-total').val(total.toFixed(2));
    }
    
    function initializePITransactionNumber() {
        // Generate initial transaction number on page load
        generatePITransactionNumber();
        
        // Handle generate button click
        $('#generate-pi-number-btn').on('click', function() {
            generatePITransactionNumber();
        });
        
        // Validate for duplicates when manually edited
        $('#purchase-invoice-reference').on('blur', function() {
            const number = $(this).val();
            if (number) {
                checkPIDuplicateNumber(number);
            }
        });
    }
    
    function generatePITransactionNumber() {
        $('#generate-pi-number-btn').prop('disabled', true);
        
        // Mock generation - in real implementation, use AJAX
        const year = new Date().getFullYear();
        const month = String(new Date().getMonth() + 1).padStart(2, '0');
        const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
        const number = `PI-${year}${month}-${random}`;
        
        $('#purchase-invoice-reference').val(number);
        $('#generate-pi-number-btn').prop('disabled', false);
    }
    
    function checkPIDuplicateNumber(number) {
        // Mock validation - in real implementation, use AJAX
        console.log('Checking duplicate for:', number);
    }
    
    // Form submission
    $('#purchase-invoice-form').on('submit', function(e) {
        e.preventDefault();
        
        // Validation
        const requiredFields = [
            'purchase-invoice-date',
            'purchase-invoice-supplier',
            'purchase-invoice-due-date',
            'purchase-invoice-amount',
            'purchase-invoice-total'
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
            title: 'Buat Purchase Invoice?',
            text: 'Pastikan data yang diisi sudah benar.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Buat!',
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
        const formData = new FormData($('#purchase-invoice-form')[0]);
        
        // AJAX submission
        $.ajax({
            url: "{{ route('main_transaction.purchase_invoice.store') }}",
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Purchase Invoice berhasil dibuat.',
                    icon: 'success',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                }).then(() => {
                    window.location.href = "{{ route('main_transaction.purchase_invoice.index') }}";
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
                $('#submit-btn').prop('disabled', false).html('Simpan');
            }
        });
    }
});
</script>
@endsection