@extends('layouts.default')
@section('page_title')
    Edit Biaya Pengeluaran
@stop
@section('content')
    <!-- Main Content Card -->
    <div class="card mb-6">
        <h4 class="card-header">Edit Biaya Pengeluaran #{{ $expense->transaction_no }}</h4>
        <form class="card-body" id="expense-form" method="POST" enctype="multipart/form-data" autocomplete="off">
            <input type="hidden" id="expense-id" value="{{ $expense->id }}">
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
                    <select id="payment-method" name="payment_method" class="global-select2 form-select"
                        data-allow-clear="false" required>
                        <option value="">Pilih Metode Pembayaran</option>
                        @foreach ($paymentMethods as $method)
                            <option value="{{ $method->id }}"
                                {{ $expense->f_payment_method_id == $method->id ? 'selected' : '' }}>{{ $method->code }} -
                                {{ $method->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-center">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" id="pay-later" name="pay_later" value="1"
                            {{ $expense->to_be_paid_later ? 'checked' : '' }}>
                        <label class="form-check-label" for="pay-later">
                            Bayar Nanti
                        </label>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nomor Transaksi <span class="text-danger">*</span></label>
                    <div class="row g-2">
                        <div class="col">
                            <input type="text" id="transaction-number" name="transaction_no" class="form-control"
                                value="{{ $expense->transaction_no }}" placeholder="Auto-generated but editable" required
                                disabled>
                        </div>
                        {{-- <div class="col-auto">
                            <button type="button" class="btn btn-outline-primary d-flex align-items-center"
                                id="generate-number-btn" title="Generate New Number">
                                <i class="icon-base ti tabler-refresh"></i>
                            </button>
                        </div> --}}
                    </div>
                </div>
            </div>

            <!-- Row 2: Recipient and Transaction Date -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Penerima</label>
                    <div class="row g-2">
                        <div class="col">
                            <select id="recipient" name="recipient" class="global-select2 form-select recipients-select"
                                data-allow-clear="false">
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}"
                                        {{ $expense->supplier_id == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($canAddContacts)
                            <div class="col-auto">
                                <a href="{{ route('master_data.contact.add') }}" target="_blank"
                                    class="btn btn-outline-primary d-flex align-items-center" title="Tambah Kontak Baru">
                                    <i class="icon-base ti tabler-plus"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Transaksi <span class="text-danger">*</span></label>
                    <input type="date" class="form-control global-single-date-picker" id="transaction-date"
                        name="transaction_date" value="{{ $expense->transaction_date }}" placeholder="YYYY-MM-DD" required>
                </div>
                
            </div>

            <!-- Row 3: Payment Terms (Conditional) -->
            <div id="payment-terms-section" class="row g-3 mb-3" style="display: none;">
                <div class="col-md-6">
                    <label class="form-label">Syarat Pembayaran <span class="text-danger">*</span></label>
                    <div class="row g-2">
                        <div class="col">
                            <select id="payment-terms" name="payment_terms"
                                class="global-select2 form-select payment-terms-select">
                                <option value="">Pilih Syarat Pembayaran</option>
                                @foreach ($paymentTerms as $item)
                                    <option value="{{ $item->id }}" data-day-count="{{ $item->days }}"
                                        {{ $expense->f_payment_term_id == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($canAddPaymentTerms)
                            <div class="col-auto">
                                <a href="{{ route('master_data.payment_term.add') }}" target="_blank"
                                    class="btn btn-outline-primary d-flex align-items-center"
                                    title="Tambah Syarat Pembayaran Baru">
                                    <i class="icon-base ti tabler-plus"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Jatuh Tempo <span class="text-danger">*</span></label>
                    <input type="date" class="form-control global-single-date-picker" id="due-date" name="due_date"
                        placeholder="YYYY-MM-DD" disabled>
                </div>
            </div>

            <!-- Row 4: Billing Address & Description -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Alamat Penagihan</label>
                    <textarea id="billing-address" name="billing_address" class="form-control" rows="3"
                        placeholder="Alamat penagihan dari vendor...">{{ $expense->billing_address }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Keterangan</label>
                    <textarea id="description" name="description" class="form-control" rows="3"
                        placeholder="Keterangan tambahan...">{{ $expense->description }}</textarea>
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
                            <th>Akun Biaya <span class="text-danger">*</span></th>
                            <th>Pajak</th>
                            <th>Jumlah <span class="text-danger">*</span></th>
                            <th>Aksi</th>
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
                        <label class="form-label">Entitas <span class="text-danger">*</span></label>
                        <select id="expense-entity" name="entity_type" class="global-select2 form-select" required data-allow-clear="false">
                            <option value="">Pilih Entitas</option>
                            <option value="KINGDOM" {{ $expense->entity_type == 'KINGDOM' ? 'selected' : '' }}>Kingdom</option>
                            <option value="OWNER" {{ $expense->entity_type == 'OWNER' ? 'selected' : '' }}>Owner</option>
                            <option value="PABRIK" {{ $expense->entity_type == 'PABRIK' ? 'selected' : '' }}>Pabrik</option>
                        </select>
                        <small class="form-text text-muted">Pilih kategori pengeluaran untuk klasifikasi biaya</small>
                    </div>
                    {{-- @if(session('user_company_code') !== '001') --}}
                    @php
                        $currentEntityType = null;
                        if ($currentBudget) {
                            $currentBudgetData = $activeBudgets->firstWhere('budget_id', $currentBudget->budged_history_id);
                            $currentEntityType = $currentBudgetData->entity_type ?? null;
                        }
                    @endphp
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Entity Type</label>
                            <select id="entity-type" name="entity_type" class="form-select">
                                <option value="">Pilih Entity Type</option>
                                <option value="kingdom" {{ $currentEntityType === 'kingdom' ? 'selected' : '' }}>Kingdom</option>
                                <option value="owner" {{ $currentEntityType === 'owner' ? 'selected' : '' }}>Owner</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Anggaran</label>
                            <select id="budget" name="budged_history_id" class="global-select2 form-select"
                                data-allow-clear="true">
                                <option value="">Pilih Entity Type terlebih dahulu</option>
                                @foreach ($activeBudgets as $budget)
                                    <option value="{{ $budget->budget_id }}"
                                        data-entity-type="{{ $budget->entity_type }}"
                                        {{ $currentBudget && $currentBudget->budged_history_id == $budget->budget_id ? 'selected' : '' }}>
                                        {{ $budget->budged_name }}
                                        ({{ date('d/m/Y', strtotime($budget->period_start_date)) }} - {{ date('d/m/Y', strtotime($budget->period_end_date)) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- @else
                    <div class="mb-3">
                        <label class="form-label">Kategori Pengeluaran <span class="text-danger">*</span></label>
                        <select id="expense-category" name="category_type" class="global-select2 form-select" required data-allow-clear="false">
                            <option value="">Pilih Kategori</option>
                            <option value="ANGGARAN" {{ $expense->category_type == 'ANGGARAN' ? 'selected' : '' }}>Anggaran</option>
                            <option value="OVERHEAD" {{ $expense->category_type == 'OVERHEAD' ? 'selected' : '' }}>Overhead</option>
                            <option value="LAINNYA" {{ $expense->category_type == 'LAINNYA' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        <small class="form-text text-muted">Pilih kategori pengeluaran untuk klasifikasi biaya</small>
                    </div>
                    @endif --}}
                    <div class="mb-3">
                        <label class="form-label">Tag</label>
                        <div class="row g-2">
                            <div class="col">
                                <select class="global-select2 form-select tags-select" id="expense-tags" multiple
                                    data-allow-clear="false">
                                    @foreach ($availableTags as $tag)
                                        <option value="{{ $tag->id }}"
                                            {{ in_array($tag->id, $expense->selected_tags ?? []) ? 'selected' : '' }}>
                                            {{ $tag->code }} - {{ $tag->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @if ($canAddTags)
                                <div class="col-auto">
                                    <a href="{{ route('master_data.tag.add') }}" target="_blank"
                                        class="btn btn-outline-primary d-flex align-items-center" id="add-new-expense-tag"
                                        title="Tambah Tag Baru">
                                        <i class="icon-base ti tabler-plus"></i>
                                    </a>
                                </div>
                            @endif
                        </div>
                        <small class="form-text text-muted">Pilih satu atau lebih tag untuk kategorisasi</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Memo <small class="text-muted">(Internal)</small></label>
                        <textarea id="memo" name="memo" class="form-control" rows="4" maxlength="500"
                            placeholder="Catatan internal...">{{ $expense->memo }}</textarea>
                        <small class="form-text text-muted">Maksimal 500 karakter - Hanya untuk keperluan internal</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lampiran</label>
                        <input type="file" id="attachment" name="attachment" class="form-control"
                            accept=".xlsx,.xls,.docx,.doc,.pdf,.png,.zip">
                        <small class="text-muted">File dapat berupa Excel, Word, PDF, PNG, atau Zip (Max: 10MB)</small>
                    </div>
                    @if ($expense->attachment)
                        <div class="mt-2">
                            <small class="text-muted">File saat ini: </small>
                            <a href="{{ Storage::url($expense->attachment) }}" target="_blank" class="text-primary">
                                <i class="icon-base ti tabler-download me-1"></i>Download File
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Right Side: Summary Section -->
                <div class="col-md-6">
                    <div class="summary-section ps-4">
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Subtotal</label>
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control readonly-field" id="subtotal-amount"
                                    dir="rtl" value="Rp 0" disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Total Pajak</label>
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control readonly-field" id="tax-amount" dir="rtl"
                                    value="Rp 0" disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Total Sebelum Diskon</label>
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control readonly-field" id="total-before-discount"
                                    dir="rtl" value="Rp 0" disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Diskon</label>
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control currency-input" id="discount-amount"
                                    name="discount_amount" dir="rtl" placeholder="Rp 0"
                                    value="{{ number_format($expense->total_discount, 0, ',', '.') }}">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label"><strong>Total Akhir</strong></label>
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control readonly-field fw-bold" id="final-total"
                                    dir="rtl" value="Rp 0" disabled>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Action Buttons -->
            <div class="pt-6 text-end">
                <a href="{{ route('main_transaction.expense.detail', $expense->id) }}"
                    class="btn btn-label-danger me-3">Batalkan</a>
                <button type="submit" class="btn btn-success" id="submit-btn">Update</button>
            </div>
        </form>
    </div>
@stop

@section('js_script')
    <script>
        $(function() {
            // Budget data from backend
            const allBudgets = @json($activeBudgets);

            // Initialize entity type filter
            initializeEntityTypeFilter();

            // Initialize form functionality
            initializeFormLogic();

            // Initialize expense table
            initializeExpenseTable();

            // Initialize calculations
            initializeCalculations();

            function initializeEntityTypeFilter() {
                // Trigger change on page load to filter budgets
                const initialType = $('#entity-type').val();
                if (initialType) {
                    filterBudgetsByType(initialType, false);
                }

                $('#entity-type').on('change', function() {
                    const selectedType = $(this).val();
                    filterBudgetsByType(selectedType, true);
                });

                function filterBudgetsByType(selectedType, clearSelection) {
                    const budgetSelect = $('#budget');

                    // Clear and reset budget dropdown
                    budgetSelect.empty();

                    if (!selectedType) {
                        budgetSelect.append('<option value="">Pilih Entity Type terlebih dahulu</option>');
                        if (budgetSelect.hasClass('select2-hidden-accessible')) {
                            budgetSelect.select2('destroy');
                            budgetSelect.select2({ allowClear: true });
                        }
                        return;
                    }

                    // Filter budgets by selected entity type
                    const filteredBudgets = allBudgets.filter(budget => budget.entity_type === selectedType);

                    budgetSelect.append('<option value="">Pilih Anggaran</option>');
                    filteredBudgets.forEach(budget => {
                        const startDate = new Date(budget.period_start_date).toLocaleDateString('id-ID');
                        const endDate = new Date(budget.period_end_date).toLocaleDateString('id-ID');

                        const isSelected = !clearSelection && {{ $currentBudget ? $currentBudget->budged_history_id : 'null' }} == budget.budget_id;

                        budgetSelect.append(
                            `<option value="${budget.budget_id}" ${isSelected ? 'selected' : ''}>
                                ${budget.budged_name} (${startDate} - ${endDate})
                            </option>`
                        );
                    });

                    // Reinitialize select2
                    if (budgetSelect.hasClass('select2-hidden-accessible')) {
                        budgetSelect.select2('destroy');
                    }
                    budgetSelect.select2({ allowClear: true });
                }
            }

            function initializeFormLogic() {
                // Pay Later checkbox logic
                const isChecked = $('#pay-later').is(':checked');
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

                const paymentTermsSelect = $('#payment-terms');
                const selectedOption = paymentTermsSelect.find('option:selected');
                const days = selectedOption.data('day-count'); // Get dat
                const dueDateField = $('#due-date');

                // Only auto-calculate for non-custom payment terms when pay later is checked
                if (transactionDate && paymentTerms && paymentTerms !== 'Custom' && $('#pay-later').is(
                        ':checked')) {

                    if (days > 0) {
                        const date = new Date(transactionDate);
                        date.setDate(date.getDate() + days);
                        dueDateField.val(date.toISOString().split('T')[0]);
                    }
                }
            }

            function initializeExpenseTable() {
                // Load existing expense lines
                @foreach ($expense->expense_lines as $line)
                    addExpenseRowWithData({
                        account_code: '{{ $line->coa_code }}',
                        account_name: '{{ $line->coa_name }}',
                        tax_id: '{{ $line->tax_id }}',
                        tax_code: '{{ $line->tax_code }}',
                        tax_name: '{{ $line->tax_name }}',
                        amount: {{ $line->amount }}
                    });
                @endforeach

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
                const coaAccountOptions = `
                    <option value="">Pilih Akun Biaya</option>
                    @foreach ($coa_accounts as $account)
                        <option value="{{ $account->code }}">{{ $account->code }} - {{ $account->name }}</option>
                    @endforeach
                `;
                const taxOptions = `
                    <option value="">Pilih Pajak</option>
                    @foreach ($taxOptions as $tax)
                        <option value="{{ $tax->id }}" data-rate="{{ $tax->rate }}">{{ $tax->code }} - {{ $tax->name }}</option>
                    @endforeach
                `;
                const newRow = `
            <tr class="expense-row">
                <td>
                    <select name="expense_accounts[]" class="form-select expense-account" required>
                        ${coaAccountOptions}
                    </select>
                </td>
                <td>
                    <select name="taxes[]" class="form-select tax-select" data-allow-clear="true">
                          ${taxOptions}
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
                });

                // // Initialize tax select with AJAX
                newExpenseRow.find('select[name="taxes[]"]').select2({
                    placeholder: 'Tanpa Pajak',
                    allowClear: true,
                });
            }

            function addExpenseRowWithData(data) {
                const coaAccountOptions = `
                    <option value="">Pilih Akun Biaya</option>
                    @foreach ($coa_accounts as $account)
                        <option value="{{ $account->code }}">{{ $account->code }} - {{ $account->name }}</option>
                    @endforeach
                `;
                const taxOptions = `
                    <option value="">Pilih Pajak</option>
                    @foreach ($taxOptions as $tax)
                        <option value="{{ $tax->id }}" data-rate="{{ $tax->rate }}">{{ $tax->code }} - {{ $tax->name }}</option>
                    @endforeach
                `;

                const newRow = `
            <tr class="expense-row">
                <td>
                    <select name="expense_accounts[]" class="form-select expense-account" required>
                        
                        ${coaAccountOptions}
                    </select>
                </td>
                <td>
                    <select name="taxes[]" class="form-select tax-select" data-allow-clear="true">
                    
                        ${taxOptions}
                    </select>
                </td>
                <td>
                    <input type="text" name="amounts[]" class="form-control currency-input" 
                           placeholder="Rp 0" value="${formatCurrency(data.amount)}" required>
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
                    allowClear: true
                }).val(data.account_code).trigger('change');

                // Initialize tax select and set value
                newExpenseRow.find('select[name="taxes[]"]').select2({
                    placeholder: 'Tanpa Pajak',
                    allowClear: true
                }).val(data.tax_id).trigger('change');
            }

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
                    const taxRate = parseFloat($(this).find('select[name="taxes[]"] option:selected').data(
                        'rate')) || 0;
                    subtotal += amount;

                    totalTax += amount * taxRate / 100;
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

            function collectFormData() {
                const formData = new FormData();

                // Basic fields
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('expense_id', $('#expense-id').val());
                formData.append('payment_method', $('#payment-method').val());
                formData.append('pay_later', $('#pay-later').is(':checked') ? '1' : '0');
                formData.append('transaction_number', $('#transaction-number').val());
                formData.append('recipient_id', $('#recipient').val() || '');
                formData.append('transaction_date', $('#transaction-date').val());
                formData.append('payment_terms', $('#payment-terms').val() || '');
                formData.append('due_date', $('#due-date').val() || '');
                formData.append('billing_address', $('#billing-address').val());
                formData.append('description', $('#description').val());
                formData.append('memo', $('#memo').val());
                formData.append('discount_amount', parseCurrency($('#discount-amount').val()) || 0);
                formData.append('budged_history_id', $('#budget').val() || '');
                formData.append('category_type', $('#expense-category').val() || '');
                formData.append('entity_type', $('#expense-entity').val() || '');

                // Tags
                const tags = $('#expense-tags').val() || [];
                tags.forEach((tag, index) => {
                    formData.append(`tags[${index}]`, tag);
                });

                // Attachment file
                const attachmentFile = $('#attachment')[0].files[0];
                if (attachmentFile) {
                    formData.append('attachment', attachmentFile);
                }

                // Expense lines
                $('.expense-row').each(function(index) {
                    const expenseAccount = $(this).find('select[name="expense_accounts[]"]').val();
                    const taxId = $(this).find('select[name="taxes[]"]').val();
                    const taxRate = parseFloat($(this).find('select[name="taxes[]"] option:selected').data(
                        'rate')) || 0;
                    const amount = parseCurrency($(this).find('input[name="amounts[]"]').val()) || 0;

                    if (expenseAccount && amount > 0) {
                        formData.append(`expense_lines[${index}][expense_account]`, expenseAccount);
                        formData.append(`expense_lines[${index}][tax_id]`, taxId);
                        formData.append(`expense_lines[${index}][tax_rate]`, taxRate);
                        formData.append(`expense_lines[${index}][amount]`, amount);
                    }
                });

                // Totals
                formData.append('subtotal', parseCurrency($('#subtotal-amount').val()) || 0);
                formData.append('tax_amount', parseCurrency($('#tax-amount').val()) || 0);
                formData.append('total_before_discount', parseCurrency($('#total-before-discount').val()) || 0);
                formData.append('final_total', parseCurrency($('#final-total').val()) || 0);

                return formData;
            }

            // Form submission
            $('#expense-form').on('submit', function(e) {
                e.preventDefault();

                // Validation
                if (!validateForm()) {
                    return false;
                }

                const formData = collectFormData();

                Swal.fire({
                    title: 'Simpan Data?',
                    text: 'Cek kembali data yang sudah diisi!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan!',
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-label-secondary'
                    },
                    buttonsStyling: false
                }).then(function(result) {
                    if (result.value) {
                        if (result.value) {
                            $('#submit-btn').prop('disabled', true).html(
                                '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Loading...'
                            );
                            $.ajax({
                                url: "{{ route('main_transaction.expense.update') }}",
                                method: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: 'Data Pengeluaran berhasil diubah.',
                                        customClass: {
                                            confirmButton: 'btn btn-success'
                                        },
                                        buttonsStyling: false
                                    }).then(function() {
                                        window.location.href =
                                            "{{ route('main_transaction.expense.index') }}";
                                    });
                                },
                                error: function(xhr) {
                                    const res = xhr.responseJSON;
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal menyimpan',
                                        html: res?.message ||
                                            'Terjadi kesalahan saat menyimpan data.',
                                        customClass: {
                                            confirmButton: 'btn btn-primary'
                                        },
                                        buttonsStyling: false
                                    });
                                },
                                complete: function() {
                                    $('#submit-btn').prop('disabled', false).html(
                                        'Simpan Data');
                                }
                            });
                        }
                    }
                });
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
