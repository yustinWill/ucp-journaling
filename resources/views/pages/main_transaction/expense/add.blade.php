@extends('layouts.default')
@section('page_title')
    Buat Biaya Pengeluaran
@stop
@section('content')
    <!-- Main Content Card -->
    <div class="card">
        <h5 class="card-header">Buat Biaya Pengeluaran</h5>
        <div class="card-body">
            <form id="expenseForm" method="POST" enctype="multipart/form-data">
                @csrf
                
                <!-- Section 1: Transaction Information -->
                <div class="section-header mt-12">
                    <h6>Informasi Transaksi</h6>
                    <hr class="mx-n6">
                </div>
                
                <!-- Row 1: Payment Method, Pay Later, Transaction Number -->
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                        <select id="payment-method" name="payment_method" class="global-select2 form-select" required>
                            <option value="">Pilih Metode Pembayaran</option>
                            @foreach($paymentMethods as $method)
                                <option value="{{ $method['value'] }}" {{ $method['value'] == 'cash' ? 'selected' : '' }}>{{ $method['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-center">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" id="pay-later" name="pay_later" value="1">
                            <label class="form-check-label" for="pay-later">
                                Bayar Nanti
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nomor Transaksi <span class="text-danger">*</span></label>
                        <input type="text" id="transaction-number" name="transaction_number" class="form-control" 
                               value="EXP-2025-001" placeholder="autofill but editable" required>
                    </div>
                </div>
                
                <!-- Row 2: Recipient and Transaction Date -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Penerima</label>
                        <select id="recipient" name="recipient" class="global-select2 form-select" data-allow-clear="true">
                            <option value="">Pilih Penerima</option>
                            @foreach($recipients as $recipient)
                                <option value="{{ $recipient['value'] }}">{{ $recipient['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Transaksi <span class="text-danger">*</span></label>
                        <input type="date" class="form-control global-single-date-picker" id="transaction-date" name="transaction_date" 
                               value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                
                <!-- Row 3: Payment Terms (Conditional) -->
                <div id="payment-terms-section" class="row g-3 mb-3" style="display: none;">
                    <div class="col-md-6">
                        <label class="form-label">Syarat Pembayaran <span class="text-danger">*</span></label>
                        <select id="payment-terms" name="payment_terms" class="global-select2 form-select">
                            <option value="">Pilih Syarat Pembayaran</option>
                            @foreach($paymentTerms as $term)
                                <option value="{{ $term['value'] }}" {{ $term['value'] == 'net_30' ? 'selected' : '' }}>{{ $term['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Jatuh Tempo <span class="text-danger">*</span></label>
                        <input type="date" class="form-control global-single-date-picker" id="due-date" name="due_date" placeholder="autofill">
                    </div>
                </div>
                
                <!-- Row 4: Billing Address -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Alamat Penagihan</label>
                        <textarea id="billing-address" name="billing_address" class="form-control" 
                                  rows="3" placeholder="Alamat penagihan dari vendor..."></textarea>
                    </div>
                </div>
                
                <!-- Section 2: Expense Information -->
                <div class="section-header mt-12">
                    <h6>Informasi Biaya</h6>
                </div>
                
                <!-- Expense Table -->
                <div class="mx-n5 my-4">
                    <table id="expense-table" class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="25%">Akun Biaya <span class="text-danger">*</span></th>
                                <th width="30%">Deskripsi</th>
                                <th width="20%">Pajak</th>
                                <th width="20%">Jumlah <span class="text-danger">*</span></th>
                                <th width="5%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="expense-rows">
                            <tr class="expense-row">
                                <td>
                                    <select name="expense_accounts[]" class="global-select2 form-select expense-account" required>
                                        <option value="">Pilih Akun Biaya</option>
                                        @foreach($accounts as $account)
                                            <option value="{{ $account['value'] }}">{{ $account['label'] }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="descriptions[]" class="form-control" 
                                           placeholder="Deskripsi biaya...">
                                </td>
                                <td>
                                    <select name="taxes[]" class="global-select2 form-select" data-allow-clear="true">
                                        <option value="">Tanpa Pajak</option>
                                        <option value="ppn">PPN (11%)</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="amounts[]" class="form-control currency-input" 
                                           placeholder="Rp 0" required>
                                </td>
                                <td>
                                    <button class="btn btn-label-danger" type="button" onclick="removeRow(this)">
                                        <i class="icon-base ti tabler-x me-1"></i>
                                        <span class="align-middle">Hapus</span>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Add Row Button -->
                <div class="mb-3">
                    <button type="button" class="btn btn-primary" id="add-expense-row">
                        <i class="icon-base ti tabler-plus me-1"></i>
                        <span class="align-middle">Tambah Baris</span>
                    </button>
                </div>

                <!-- Additional Information & Summary -->
                <div class="section-header mt-12">
                    <h6>Informasi Tambahan & Ringkasan</h6>
                    <hr class="mx-n6">
                </div>
                
                <!-- Split Layout: Memo & Financial Summary -->
                <div class="row">
                    <!-- Left Side: Memo and Attachment -->
                    <div class="col-md-6 mb-4">
                        <div class="mb-3">
                            <label class="form-label">Memo</label>
                            <textarea id="memo" name="memo" class="form-control" rows="4" 
                                      maxlength="500" placeholder="Catatan tambahan..."></textarea>
                            <small class="text-muted">Maksimal 500 karakter</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lampiran</label>
                            <input type="file" id="attachment" name="attachment" class="form-control" 
                                   accept=".xlsx,.xls,.docx,.doc,.pdf,.png,.zip">
                            <small class="text-muted">File dapat berupa Excel, Word, PDF, PNG, atau Zip (Max: 10MB)</small>
                        </div>
                    </div>
                    
                    <!-- Right Side: Summary Section -->
                    <div class="col-md-6">
                        <div class="summary-section ps-4">
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="form-label">Subtotal</label>
                                </div>
                                <div class="col-6">
                                    <input type="text" class="form-control readonly-field" id="subtotal-amount" dir="rtl" value="Rp 0" readonly>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="form-label">Total Pajak</label>
                                </div>
                                <div class="col-6">
                                    <input type="text" class="form-control readonly-field" id="tax-amount" dir="rtl" value="Rp 0" readonly>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="form-label">Total Sebelum Diskon</label>
                                </div>
                                <div class="col-6">
                                    <input type="text" class="form-control readonly-field" id="total-before-discount" dir="rtl" value="Rp 0" readonly>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="form-label">Diskon</label>
                                </div>
                                <div class="col-6">
                                    <input type="text" class="form-control currency-input" id="discount-amount" name="discount_amount" dir="rtl" placeholder="Rp 0">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="form-label"><strong>Total Akhir</strong></label>
                                </div>
                                <div class="col-6">
                                    <input type="text" class="form-control readonly-field fw-bold" id="final-total" dir="rtl" value="Rp 0" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Section 3: Conditional Payment Terms Management -->
                <div id="payment-terms-management" style="display: none;">
                    <div class="section-header mt-12">
                        <h6>Syarat Pembayaran</h6>
                        <hr class="mx-n6">
                    </div>
                    
                    <!-- Payment Terms Information -->
                    <div class="alert alert-info">
                        <h6 class="alert-heading">Catatan Khusus untuk Bayar Nanti</h6>
                        <ul class="mb-0">
                            <li>☑ Checkbox Bayar Nanti harus dicentang</li>
                            <li>☑ Syarat Pembayaran dan Tanggal Jatuh Tempo wajib diisi</li>
                            <li>☑ Tanggal Jatuh Tempo otomatis dihitung dari tanggal transaksi + syarat pembayaran, tetapi dapat diedit jika perlu</li>
                        </ul>
                    </div>
                    
                    <!-- Add New Payment Terms -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Tambah Syarat Pembayaran</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Syarat Pembayaran <span class="text-danger">*</span></label>
                                <select id="existing-payment-terms" class="global-select2 form-select">
                                    <option value="">Pilih Syarat Pembayaran</option>
                                    @foreach($paymentTerms as $term)
                                        <option value="{{ $term['value'] }}">{{ $term['label'] }}</option>
                                    @endforeach
                                    <option value="custom">└ Syarat Pembayaran baru</option>
                                </select>
                            </div>
                            
                            <div id="new-payment-terms" style="display: none;">
                                <div class="mb-3">
                                    <label class="form-label">Nama Syarat Pembayaran <span class="text-danger">*</span></label>
                                    <input type="text" id="payment-terms-name" name="payment_terms_name" 
                                           class="form-control" placeholder="Contoh: Net 60">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Jumlah Hari Jatuh Tempo <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" id="payment-terms-days" name="payment_terms_days" 
                                               class="form-control" min="1" max="365" placeholder="30">
                                        <span class="input-group-text">Hari</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="mt-4">
                    <div class="d-flex gap-2 justify-content-end">
                        <button type="button" class="btn btn-secondary" id="cancel-btn">
                            <i class="icon-base ti tabler-arrow-left me-2"></i>Batalkan
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="icon-base ti tabler-check me-2"></i>Buat
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js_script')
<script>
$(function() {
    // Initialize searchable selects
    initializeSearchableSelects();
    
    // Initialize form functionality
    initializeFormLogic();
    
    // Initialize expense table
    initializeExpenseTable();
    
    // Initialize calculations
    initializeCalculations();
    
    function initializeSearchableSelects() {
        // Initialize Select2 for existing selects (server-side rendered options)
        $('#payment-method, #payment-terms, #existing-payment-terms, #recipient').select2();
        
        // Only expense account selects use AJAX for dynamic row addition
        $('.expense-account').select2({
            placeholder: 'Pilih Akun Biaya',
            allowClear: true,
            ajax: {
                url: `{{ route('main_transaction.expense.accounts') }}`,
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { search: params.term };
                },
                processResults: function (data) {
                    return {
                        results: data.map(function(item) {
                            return { id: item.value, text: item.label };
                        })
                    };
                },
                cache: true
            }
        });
    }
    
    function initializeFormLogic() {
        // Pay Later checkbox logic
        $('#pay-later').on('change', function() {
            const isChecked = $(this).is(':checked');
            
            if (isChecked) {
                // Disable payment method
                $('#payment-method').prop('disabled', true).removeClass('is-invalid');
                
                // Show payment terms section
                $('#payment-terms-section').show();
                $('#payment-terms-management').show();
                
                // Make payment terms required
                $('#payment-terms, #due-date').prop('required', true);
                
                // Calculate due date if payment terms selected
                calculateDueDate();
            } else {
                // Enable payment method
                $('#payment-method').prop('disabled', false);
                
                // Hide payment terms section
                $('#payment-terms-section').hide();
                $('#payment-terms-management').hide();
                
                // Remove payment terms requirement
                $('#payment-terms, #due-date').prop('required', false);
            }
        });
        
        // Payment terms change
        $('#payment-terms').on('change', function() {
            calculateDueDate();
        });
        
        // Transaction date change
        $('#transaction-date').on('change', function() {
            calculateDueDate();
        });
        
        // Existing payment terms change
        $('#existing-payment-terms').on('change', function() {
            if ($(this).val() === 'custom') {
                $('#new-payment-terms').show();
                $('#payment-terms-name, #payment-terms-days').prop('required', true);
            } else {
                $('#new-payment-terms').hide();
                $('#payment-terms-name, #payment-terms-days').prop('required', false);
            }
        });
        
        // Recipient change - populate billing address
        $('#recipient').on('change', function() {
            const recipientId = $(this).val();
            if (recipientId) {
                // Simulate address population
                $('#billing-address').val('Alamat akan dimuat dari data penerima...');
            } else {
                $('#billing-address').val('');
            }
        });
    }
    
    function calculateDueDate() {
        const transactionDate = $('#transaction-date').val();
        const paymentTerms = $('#payment-terms').val();
        
        if (transactionDate && paymentTerms && $('#pay-later').is(':checked')) {
            let days = 0;
            switch (paymentTerms) {
                case 'net_15': days = 15; break;
                case 'net_30': days = 30; break;
                case 'net_45': days = 45; break;
                case 'net_60': days = 60; break;
            }
            
            if (days > 0) {
                const date = new Date(transactionDate);
                date.setDate(date.getDate() + days);
                $('#due-date').val(date.toISOString().split('T')[0]);
            }
        }
    }
    
    function initializeExpenseTable() {
        // Add expense row
        $('#add-expense-row').on('click', function() {
            addExpenseRow();
        });
        
        // Remove expense row function
        window.removeRow = function(button) {
            if ($('#expense-rows tr').length > 1) {
                $(button).closest('tr').remove();
                calculateTotals();
            }
        };
        
        // Currency input formatting
        $(document).on('input', '.currency-input', function() {
            formatCurrencyInput(this);
            calculateTotals();
        });
    }
    
    function addExpenseRow() {
        const newRow = `
            <tr class="expense-row">
                <td>
                    <select name="expense_accounts[]" class="form-select expense-account" required>
                        <option value="">Pilih Akun Biaya</option>
                    </select>
                </td>
                <td>
                    <input type="text" name="descriptions[]" class="form-control" 
                           placeholder="Deskripsi biaya...">
                </td>
                <td>
                    <select name="taxes[]" class="form-select">
                        <option value="">Tanpa Pajak</option>
                        <option value="ppn">PPN (11%)</option>
                    </select>
                </td>
                <td>
                    <input type="text" name="amounts[]" class="form-control currency-input" 
                           placeholder="Rp 0" required>
                </td>
                <td>
                    <button class="btn btn-label-danger" type="button" onclick="removeRow(this)">
                        <i class="icon-base ti tabler-x me-1"></i>
                        <span class="align-middle">Hapus</span>
                    </button>
                </td>
            </tr>
        `;
        
        $('#expense-rows').append(newRow);
        
        // Initialize select2 for new row
        $('#expense-rows tr:last .expense-account').select2({
            placeholder: 'Pilih Akun Biaya',
            allowClear: true,
            ajax: {
                url: `{{ route('main_transaction.expense.accounts') }}`,
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { search: params.term };
                },
                processResults: function (data) {
                    return {
                        results: data.map(function(item) {
                            return { id: item.value, text: item.label };
                        })
                    };
                },
                cache: true
            }
        });
    }
    
    // Currency functions are now available globally
    
    function initializeCalculations() {
        // Initial calculation
        calculateTotals();
        
        // Discount input change
        $('#discount-amount').on('input', function() {
            formatCurrencyInput(this);
            calculateTotals();
        });
    }
    
    function calculateTotals() {
        let subtotal = 0;
        let totalTax = 0;
        
        // Calculate subtotal and taxes
        $('.expense-row').each(function() {
            const amount = parseCurrency($(this).find('input[name="amounts[]"]').val());
            const tax = $(this).find('select[name="taxes[]"]').val();
            
            subtotal += amount;
            
            if (tax === 'ppn') {
                totalTax += amount * 0.11;
            }
        });
        
        const totalBeforeDiscount = subtotal + totalTax;
        const discount = parseCurrency($('#discount-amount').val());
        const finalTotal = totalBeforeDiscount - discount;
        
        // Update display
        $('#subtotal-amount').val(formatCurrency(subtotal));
        $('#tax-amount').val(formatCurrency(totalTax));
        $('#total-before-discount').val(formatCurrency(totalBeforeDiscount));
        $('#final-total').val(formatCurrency(finalTotal));
    }
    
    // Form submission
    $('#expenseForm').on('submit', function(e) {
        e.preventDefault();
        
        // Validation
        if (!validateForm()) {
            return false;
        }
        
        // TODO: Implement form submission
        alert('Form validation passed! Implementation will be added.');
    });
    
    function validateForm() {
        let isValid = true;
        
        // Check pay later requirements
        if ($('#pay-later').is(':checked')) {
            if (!$('#payment-terms').val()) {
                $('#payment-terms').addClass('is-invalid');
                isValid = false;
            }
            if (!$('#due-date').val()) {
                $('#due-date').addClass('is-invalid');
                isValid = false;
            }
        } else {
            if (!$('#payment-method').val()) {
                $('#payment-method').addClass('is-invalid');
                isValid = false;
            }
        }
        
        // Check expense rows
        let hasValidExpense = false;
        $('.expense-row').each(function() {
            const account = $(this).find('select[name="expense_accounts[]"]').val();
            const amount = parseCurrency($(this).find('input[name="amounts[]"]').val());
            
            if (account && amount > 0) {
                hasValidExpense = true;
            }
        });
        
        if (!hasValidExpense) {
            alert('Minimal satu baris biaya harus diisi dengan akun dan jumlah yang valid.');
            isValid = false;
        }
        
        return isValid;
    }
    
    // Remove validation classes on input
    $('input, select').on('input change', function() {
        $(this).removeClass('is-invalid');
    });

    // Cancel button handler
    $('#cancel-btn').on('click', function() {
        window.location.href = '{{ route('main_transaction.expense.index') }}';
    });
});
</script>
@endsection