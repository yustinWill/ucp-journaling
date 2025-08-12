@extends('layouts.default')
@section('page_title')
    Buat Biaya Pengeluaran
@stop
@section('content')
    <!-- Main Content Card -->
    <div class="card">
        <h5 class="card-header">Buat Biaya Pengeluaran</h5>
        <div class="card-body">
            <form id="expenseForm" method="POST" enctype="multipart/form-data" autocomplete="off">
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
                        <div class="row g-2">
                            <div class="col">
                                <input type="text" id="transaction-number" name="transaction_number" class="form-control" 
                                       value="" placeholder="Auto-generated but editable" required>
                            </div>
                            <div class="col-auto">
                                <button type="button" class="btn btn-outline-primary d-flex align-items-center" id="generate-number-btn" title="Generate New Number">
                                    <i class="icon-base ti tabler-refresh"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Row 2: Recipient and Transaction Date -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Penerima</label>
                        <div class="row g-2">
                            <div class="col">
                                <select id="recipient" name="recipient" class="global-select2 form-select recipients-select" data-allow-clear="true">
                                    <option value="">Pilih Penerima</option>
                                </select>
                            </div>
                            @if($canAddContacts)
                                <div class="col-auto">
                                    <a href="{{ route('master_data.contact.add') }}" target="_blank" class="btn btn-outline-primary d-flex align-items-center" title="Tambah Kontak Baru">
                                        <i class="icon-base ti tabler-plus"></i>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Transaksi <span class="text-danger">*</span></label>
                        <input type="date" class="form-control global-single-date-picker" id="transaction-date" name="transaction_date" 
                               value="{{ date('Y-m-d') }}" placeholder="YYYY-MM-DD" required>
                    </div>
                </div>
                
                <!-- Row 3: Payment Terms (Conditional) -->
                <div id="payment-terms-section" class="row g-3 mb-3" style="display: none;">
                    <div class="col-md-6">
                        <label class="form-label">Syarat Pembayaran <span class="text-danger">*</span></label>
                        <div class="row g-2">
                            <div class="col">
                                <select id="payment-terms" name="payment_terms" class="global-select2 form-select payment-terms-select">
                                    <option value="">Pilih Syarat Pembayaran</option>
                                </select>
                            </div>
                            @if($canAddPaymentTerms)
                                <div class="col-auto">
                                    <a href="{{ route('master_data.payment_term.add') }}" target="_blank" class="btn btn-outline-primary d-flex align-items-center" title="Tambah Syarat Pembayaran Baru">
                                        <i class="icon-base ti tabler-plus"></i>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Jatuh Tempo <span class="text-danger">*</span></label>
                        <input type="date" class="form-control global-single-date-picker" id="due-date" name="due_date" placeholder="YYYY-MM-DD" disabled>
                    </div>
                </div>
                
                <!-- Row 4: Billing Address & Description -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Alamat Penagihan</label>
                        <textarea id="billing-address" name="billing_address" class="form-control" 
                                  rows="3" placeholder="Alamat penagihan dari vendor..."></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Keterangan</label>
                        <textarea id="description" name="description" class="form-control" 
                                  rows="3" placeholder="Keterangan tambahan..."></textarea>
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
                                <th width="40%">Akun Biaya <span class="text-danger">*</span></th>
                                <th width="25%">Pajak</th>
                                <th width="25%">Jumlah <span class="text-danger">*</span></th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="expense-rows">
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
                    <!-- Left Side: Tag, Memo and Attachment -->
                    <div class="col-md-6 mb-4">
                        <div class="mb-3">
                            <label class="form-label">Tag</label>
                            <div class="row g-2">
                                <div class="col">
                                    <select class="global-select2 form-select tags-select" id="expense-tags" multiple data-allow-clear="false">
                                    </select>
                                </div>
                                @if($canAddTags)
                                    <div class="col-auto">
                                        <a href="{{ route('master_data.tag.add') }}" target="_blank" class="btn btn-outline-primary d-flex align-items-center" id="add-new-expense-tag" title="Tambah Tag Baru">
                                            <i class="icon-base ti tabler-plus"></i>
                                        </a>
                                    </div>
                                @endif
                            </div>
                            <small class="form-text text-muted">Pilih satu atau lebih tag untuk kategorisasi</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Memo <small class="text-muted">(Internal)</small></label>
                            <textarea id="memo" name="memo" class="form-control" rows="4" 
                                      maxlength="500" placeholder="Catatan internal..."></textarea>
                            <small class="form-text text-muted">Maksimal 500 karakter - Hanya untuk keperluan internal</small>
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
                                    <input type="text" class="form-control readonly-field" id="subtotal-amount" dir="rtl" value="Rp 0" disabled>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="form-label">Total Pajak</label>
                                </div>
                                <div class="col-6">
                                    <input type="text" class="form-control readonly-field" id="tax-amount" dir="rtl" value="Rp 0" disabled>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="form-label">Total Sebelum Diskon</label>
                                </div>
                                <div class="col-6">
                                    <input type="text" class="form-control readonly-field" id="total-before-discount" dir="rtl" value="Rp 0" disabled>
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
                                    <input type="text" class="form-control readonly-field fw-bold" id="final-total" dir="rtl" value="Rp 0" disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                
                <!-- Action Buttons -->
                <div class="pt-6 text-end">
                    <a href="{{ route('main_transaction.expense.index') }}" class="btn btn-label-danger me-3">Batalkan</a>
                    <button type="submit" class="btn btn-success" id="submit-btn">Simpan</button>
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
    
    // Initialize transaction number
    initializeTransactionNumber();
    
    // Initialize form functionality
    initializeFormLogic();
    
    // Initialize expense table
    initializeExpenseTable();
    
    // Initialize calculations
    initializeCalculations();
    
    function initializeTransactionNumber() {
        // Generate initial transaction number on page load
        generateTransactionNumber();
        
        // Handle generate button click
        $('#generate-number-btn').on('click', function() {
            generateTransactionNumber();
        });
        
        // Validate for duplicates when manually edited
        $('#transaction-number').on('blur', function() {
            const number = $(this).val();
            if (number) {
                checkDuplicateNumber(number);
            }
        });
    }
    
    function generateTransactionNumber() {
        $('#generate-number-btn').prop('disabled', true);
        
        $.ajax({
            url: '{{ route("master_data.transaction_number.generate_number", "expense") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    $('#transaction-number').val(response.number);
                } else {
                    console.error('Failed to generate transaction number:', response.message);
                    // Fallback - show error or use placeholder
                    $('#transaction-number').val('EXP-' + new Date().getFullYear() + '-001');
                }
            },
            error: function(xhr) {
                console.error('Error generating transaction number:', xhr.responseText);
                // Fallback - use basic format
                $('#transaction-number').val('EXP-' + new Date().getFullYear() + '-001');
            },
            complete: function() {
                $('#generate-number-btn').prop('disabled', false);
            }
        });
    }
    
    function checkDuplicateNumber(number) {
        $.ajax({
            url: '{{ route("master_data.transaction_number.check_duplicate", "expense") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                number: number
            },
            success: function(response) {
                if (response.exists) {
                    $('#transaction-number').addClass('is-invalid');
                    // You could show a tooltip or error message here
                } else {
                    $('#transaction-number').removeClass('is-invalid');
                }
            },
            error: function(xhr) {
                console.error('Error checking duplicate:', xhr.responseText);
            }
        });
    }
    
    function initializeSearchableSelects() {
        // Note: Select2 initialization is handled globally by global-select2 class
        
        // Initialize payment terms select with AJAX
        $('.payment-terms-select').select2({
            placeholder: 'Pilih Syarat Pembayaran',
            allowClear: true,
            ajax: {
                url: '{{ route("main_transaction.expense.payment_terms") }}',
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

        // Initialize tax select with AJAX
        $('.tax-select').select2({
            placeholder: 'Tanpa Pajak',
            allowClear: true,
            ajax: {
                url: '{{ route("main_transaction.expense.taxes") }}',
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

        // Initialize tags select with AJAX
        $('.tags-select').select2({
            placeholder: 'Pilih Tag',
            allowClear: false,
            ajax: {
                url: '{{ route("main_transaction.expense.tags") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { search: params.term };
                },
                processResults: function (data) {
                    return {
                        results: data.map(function(item) {
                            return { id: item.code, text: item.label };
                        })
                    };
                },
                cache: true
            }
        });

        // Initialize recipients select with AJAX
        $('.recipients-select').select2({
            placeholder: 'Pilih Penerima',
            allowClear: true,
            ajax: {
                url: '{{ route("main_transaction.expense.recipients") }}',
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
        
        // Only expense account selects use AJAX for dynamic row addition
        $('.expense-account').select2({
            placeholder: 'Pilih Akun Biaya',
            allowClear: true,
            ajax: {
                url: '{{ route("main_transaction.expense.accounts") }}',
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
                
                // Make payment terms required
                $('#payment-terms, #due-date').prop('required', true);
                
                // Calculate due date if payment terms selected
                calculateDueDate();
            } else {
                // Enable payment method
                $('#payment-method').prop('disabled', false);
                
                // Hide payment terms section
                $('#payment-terms-section').hide();
                
                // Remove payment terms requirement
                $('#payment-terms, #due-date').prop('required', false);
            }
        });
        
        // Payment terms change
        $('#payment-terms').on('change', function() {
            handlePaymentTermsChange();
            calculateDueDate();
        });
        
        // Transaction date change
        $('#transaction-date').on('change', function() {
            calculateDueDate();
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
    
    function handlePaymentTermsChange() {
        const paymentTerms = $('#payment-terms').val();
        const dueDateField = $('#due-date');
        
        if (paymentTerms === 'Custom') {
            // Enable due date field for custom payment terms
            dueDateField.prop('disabled', false);
        } else {
            // Disable due date field and auto-calculate for standard terms
            dueDateField.prop('disabled', true);
        }
    }
    
    function calculateDueDate() {
        const transactionDate = $('#transaction-date').val();
        const paymentTerms = $('#payment-terms').val();
        const dueDateField = $('#due-date');
        
        // Only auto-calculate for non-custom payment terms when pay later is checked
        if (transactionDate && paymentTerms && paymentTerms !== 'Custom' && $('#pay-later').is(':checked')) {
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
                dueDateField.val(date.toISOString().split('T')[0]);
            }
        }
    }
    
    function initializeExpenseTable() {
        // Add initial expense row
        addExpenseRow();
        
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
                    <select name="taxes[]" class="form-select tax-select" data-allow-clear="true">
                        <option value="">Tanpa Pajak</option>
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
        const newExpenseRow = $('#expense-rows tr:last');
        
        // Initialize expense account select with AJAX
        newExpenseRow.find('.expense-account').select2({
            placeholder: 'Pilih Akun Biaya',
            allowClear: true,
            ajax: {
                url: '{{ route("main_transaction.expense.accounts") }}',
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
        
        // Initialize tax select with AJAX
        newExpenseRow.find('select[name="taxes[]"]').select2({
            placeholder: 'Tanpa Pajak',
            allowClear: true,
            ajax: {
                url: '{{ route("main_transaction.expense.taxes") }}',
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

});
</script>
@endsection