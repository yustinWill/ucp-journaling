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

                <!-- Row 0: Kas/Transfer Source -->
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Sumber Pembayaran <span class="text-danger">*</span></label>
                        <select id="payment-method-type" name="payment_method_type" class="form-select" required>
                            <option value="">Pilih Sumber Pembayaran</option>
                            <option value="kas_tunai">Kas / Tunai</option>
                            <option value="transfer">Transfer</option>
                        </select>
                    </div>
                    <div class="col-md-4" id="transfer-bank-wrapper" style="display:none;">
                        <label class="form-label">Bank Transfer <span class="text-danger">*</span></label>
                        <select id="transfer-bank" name="transfer_bank" class="form-select">
                            <option value="">Pilih Bank</option>
                            <option value="BCA">BCA</option>
                            <option value="Mandiri">Mandiri</option>
                            <option value="BTPN">BTPN</option>
                        </select>
                    </div>
                    <div class="col-md-4" id="kas-saldo-wrapper" style="display:none;">
                        <label class="form-label" id="kas-saldo-label">Saldo Kas Besar</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control bg-light text-end" id="kas-saldo-display" readonly placeholder="Memuat...">
                        </div>
                        <small class="text-warning d-none" id="kas-available-note"></small>
                    </div>
                </div>

                <!-- Row 1: Payment Method, Pay Later, Transaction Number -->
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                        <select id="payment-method" name="payment_method" class="global-select2 form-select" required>
                            <option value="">Pilih Metode Pembayaran</option>
                            @foreach ($paymentMethods as $method)
                                <option value="{{ $method->id }}">{{ $method->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- <div class="col-md-2 d-flex align-items-center">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" id="pay-later" name="pay_later" value="1">
                            <label class="form-check-label" for="pay-later">
                                Bayar Nanti
                            </label>
                        </div>
                    </div> --}}
                    <div class="col-md-6">
                        <label class="form-label">Nomor Transaksi <span class="text-danger">*</span></label>
                        <div class="row g-2">
                            <div class="col">
                                <input type="text" id="transaction-number" name="transaction_number" class="form-control"
                                    value="" placeholder="Auto-generated but editable" required>
                            </div>
                            <div class="col-auto">
                                <button type="button" class="btn btn-outline-primary d-flex align-items-center"
                                    id="generate-number-btn" title="Generate New Number">
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
                                <select id="recipient" name="recipient" class="global-select2 form-select recipients-select"
                                    data-allow-clear="true">
                                    <option value="">Pilih Penerima</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach

                                </select>
                            </div>
                            @if ($canAddContacts)
                                <div class="col-auto">
                                    <a href="{{ route('master_data.contact.add') }}" target="_blank"
                                        class="btn btn-outline-primary d-flex align-items-center"
                                        title="Tambah Kontak Baru">
                                        <i class="icon-base ti tabler-plus"></i>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Transaksi <span class="text-danger">*</span></label>
                        <input type="date" class="form-control global-single-date-picker" id="transaction-date"
                            name="transaction_date" value="{{ date('Y-m-d') }}" placeholder="YYYY-MM-DD" required>
                    </div>
                    
                </div>

                <!-- Row 3: Payment Terms (Conditional) -->
                <div id="payment-terms-section" class="row g-3 mb-3" style="display: none;">
                    <div class="col-md-6">
                        <label class="form-label">Syarat Pembayaran <span class="text-danger">*</span></label>
                        <div class="row g-2">
                            <div class="col">
                                <select id="payment-terms" name="payment_terms" class="global-select2 form-select">
                                    <option value="">Pilih Syarat Pembayaran</option>
                                    @foreach ($paymentTerms as $item)
                                        <option value="{{ $item->id }}" data-day-count="{{ $item->days }}" {{ $item->code == "NET_15" ? 'selected' : '' }}>
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
                            placeholder="Alamat penagihan dari vendor..."></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Keterangan</label>
                        <textarea id="description" name="description" class="form-control" rows="3"
                            placeholder="Keterangan tambahan..."></textarea>
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
                            <label class="form-label">Entity Type <span class="text-danger">*</span></label>
                            <select id="entity-type" name="entity_type" class="global-select2 form-select" required data-allow-clear="false">
                                <option value="">Pilih Entity Type</option>
                                <option value="kingdom">Kingdom</option>
                                <option value="owner">Owner</option>
                            </select>
                            <small class="form-text text-muted">Pilih entity type untuk pemetaan anggaran</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Anggaran <span class="text-danger">*</span></label>
                            <select id="budget" name="budged_history_id" class="global-select2 form-select"
                                required data-allow-clear="false">
                                <option value="">Pilih Entity Type terlebih dahulu</option>
                            </select>
                            <small class="form-text text-muted">Anggaran akan difilter berdasarkan entity type yang dipilih</small>
                        </div>
                        {{-- @else
                        <div class="mb-3">
                            <label class="form-label">Kategori Pengeluaran <span class="text-danger">*</span></label>
                            <select id="expense-category" name="category_type" class="global-select2 form-select" required data-allow-clear="false">
                                <option value="">Pilih Kategori</option>
                                <option value="ANGGARAN">Anggaran</option>
                                <option value="OVERHEAD">Overhead</option>
                                <option value="LAINNYA">Lainnya</option>
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
                                            <option value="{{ $tag->id }}">
                                                {{ $tag->code }} - {{ $tag->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @if ($canAddTags)
                                    <div class="col-auto">
                                        <a href="{{ route('master_data.tag.add') }}" target="_blank"
                                            class="btn btn-outline-primary d-flex align-items-center"
                                            id="add-new-expense-tag" title="Tambah Tag Baru">
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
                                placeholder="Catatan internal..."></textarea>
                            <small class="form-text text-muted">Maksimal 500 karakter - Hanya untuk keperluan
                                internal</small>
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
                                    <input type="text" class="form-control readonly-field" id="subtotal-amount"
                                        dir="rtl" value="Rp 0" disabled>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="form-label">Total Pajak</label>
                                </div>
                                <div class="col-6">
                                    <input type="text" class="form-control readonly-field" id="tax-amount"
                                        dir="rtl" value="Rp 0" disabled>
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
                                        name="discount_amount" dir="rtl" placeholder="Rp 0">
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
                    <a href="{{ route('main_transaction.expense.index') }}"
                        class="btn btn-label-danger me-3">Batalkan</a>
                    <button type="submit" class="btn btn-success" id="submit-btn">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js_script')
    <script>
        $(function() {
            // Budget data from backend (mutable, refreshed on date change)
            let allBudgets = @json($activeBudgets);

            // Reload budgets when transaction date changes
            $('#transaction-date').on('change', function() {
                const date = $(this).val();
                $('#budget').val('').trigger('change');
                if (!date) return;
                $.get("{{ route('main_transaction.expense.budgets_by_date') }}", { date: date }, function(data) {
                    allBudgets = data;
                    // Re-trigger entity type filter with new budget data
                    $('#entity-type').trigger('change');
                });
            });

            // Initialize entity type filter
            initializeEntityTypeFilter();

            // Initialize searchable selects
            // initializeSearchableSelects();

            // Initialize transaction number
            initializeTransactionNumber();

            // Initialize form functionality
            initializeFormLogic();

            // Initialize expense table
            initializeExpenseTable();

            // Initialize calculations
            initializeCalculations();

            function initializeEntityTypeFilter() {
                // Listen to entity type changes only
                $('#entity-type').on('change', function() {
                    const selectedEntityType = $(this).val();
                    const budgetSelect = $('#budget');

                    // Clear and reset budget dropdown
                    budgetSelect.empty();

                    if (!selectedEntityType) {
                        budgetSelect.append('<option value="">Pilih Entity Type terlebih dahulu</option>');
                        if (budgetSelect.hasClass('select2-hidden-accessible')) {
                            budgetSelect.select2('destroy');
                            budgetSelect.select2({ allowClear: false });
                        }
                        return;
                    }

                    // Filter budgets by entity type only
                    const filteredBudgets = allBudgets.filter(budget => {
                        return budget.entity_type === selectedEntityType;
                    });

                    budgetSelect.append('<option value="">Pilih Anggaran</option>');

                    if (filteredBudgets.length === 0) {
                        budgetSelect.append('<option value="" disabled>Tidak ada anggaran yang sesuai</option>');
                    } else {
                        filteredBudgets.forEach(budget => {
                            const startDate = new Date(budget.period_start_date).toLocaleDateString('id-ID');
                            const endDate = new Date(budget.period_end_date).toLocaleDateString('id-ID');
                            budgetSelect.append(
                                `<option value="${budget.budget_id}">
                                    ${budget.budged_name} (${startDate} - ${endDate})
                                </option>`
                            );
                        });
                    }

                    // Reinitialize select2
                    if (budgetSelect.hasClass('select2-hidden-accessible')) {
                        budgetSelect.select2('destroy');
                    }
                    budgetSelect.select2({ allowClear: false });
                });
            }

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
                    url: '{{ route('master_data.transaction_number.generate_number', 'expense') }}',
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
                    url: '{{ route('master_data.transaction_number.check_duplicate', 'expense') }}',
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
                // $('.payment-terms-select').select2({
                //     placeholder: 'Pilih Syarat Pembayaran',
                //     allowClear: true,
                //     ajax: {
                //         url: '{{ route('main_transaction.expense.payment_terms') }}',
                //         dataType: 'json',
                //         delay: 250,
                //         data: function(params) {
                //             return {
                //                 search: params.term
                //             };
                //         },
                //         processResults: function(data) {
                //             return {
                //                 results: data.map(function(item) {
                //                     return {
                //                         id: item.value,
                //                         text: item.label
                //                     };
                //                 })
                //             };
                //         },
                //         cache: true
                //     }
                // });

                // Initialize tax select with AJAX
                // $('.tax-select').select2({
                //     placeholder: 'Tanpa Pajak',
                //     allowClear: true,
                //     ajax: {
                //         url: '{{ route('main_transaction.expense.taxes') }}',
                //         dataType: 'json',
                //         delay: 250,
                //         data: function(params) {
                //             return {
                //                 search: params.term
                //             };
                //         },
                //         processResults: function(data) {
                //             return {
                //                 results: data.map(function(item) {
                //                     return {
                //                         id: item.value,
                //                         text: item.label
                //                     };
                //                 })
                //             };
                //         },
                //         cache: true
                //     }
                // });

                // // Only expense account selects use AJAX for dynamic row addition
                // $('.expense-account').select2({
                //     placeholder: 'Pilih Akun Biaya',
                //     allowClear: true,
                //     ajax: {
                //         url: '{{ route('main_transaction.expense.accounts') }}',
                //         dataType: 'json',
                //         delay: 250,
                //         data: function(params) {
                //             return {
                //                 search: params.term
                //             };
                //         },
                //         processResults: function(data) {
                //             return {
                //                 results: data.map(function(item) {
                //                     return {
                //                         id: item.value,
                //                         text: item.label
                //                     };
                //                 })
                //             };
                //         },
                //         cache: true
                //     }
                // });
            }

            // ── Kas Besar / Transfer source logic ────────────────────────────
            let kasBesarSaldo = 0;
            // The most this expense may take on its date without a later Kas Besar row going below zero.
            let kasBesarAvailable = 0;

            function loadKasSaldo() {
                const date = $('#transaction-date').val();
                const label = date ? new Date(date + 'T00:00:00').toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '';
                $('#kas-saldo-label').text(label ? `Saldo Kas Besar per ${label}` : 'Saldo Kas Besar');
                $('#kas-saldo-display').val('');
                $.get('{{ route('main_transaction.kas_besar.get_latest_saldo') }}', { date: date }, function(res) {
                    if (res.success && date === $('#transaction-date').val()) {
                        kasBesarSaldo = parseFloat(res.saldo_after) || 0;
                        kasBesarAvailable = parseFloat(res.available) || 0;
                        $('#kas-saldo-display').val(kasBesarSaldo.toLocaleString('id-ID'));
                        $('#kas-available-note')
                            .toggleClass('d-none', kasBesarAvailable >= kasBesarSaldo)
                            .text(`Maksimal Rp ${kasBesarAvailable.toLocaleString('id-ID')}, agar saldo transaksi sesudahnya tidak minus.`);
                    }
                });
            }

            $('#transaction-date').on('change', function() {
                if ($('#payment-method-type').val() === 'kas_tunai') loadKasSaldo();
            });

            $('#payment-method-type').on('change', function() {
                const val = $(this).val();
                if (val === 'transfer') {
                    $('#transfer-bank-wrapper').show();
                    $('#kas-saldo-wrapper').hide();
                    $('#transfer-bank').prop('required', true);
                } else if (val === 'kas_tunai') {
                    $('#transfer-bank-wrapper').hide();
                    $('#kas-saldo-wrapper').show();
                    $('#transfer-bank').prop('required', false).val('');
                    loadKasSaldo();
                } else {
                    $('#transfer-bank-wrapper').hide();
                    $('#kas-saldo-wrapper').hide();
                    $('#transfer-bank').prop('required', false).val('');
                }
            });

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

            $(document).on('change', '.tax-select', function() {
                calculateTotals();
            });

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
                formData.append('payment_method_type', $('#payment-method-type').val() || '');
                formData.append('transfer_bank', $('#transfer-bank').val() || '');
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
                formData.append('entity_type', $('#entity-type').val() || '');
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
            $('#expenseForm').on('submit', function(e) {
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
                                url: "{{ route('main_transaction.expense.save') }}",
                                method: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: 'Data Pengeluaran berhasil ditambahkan.',
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

                // Check sumber pembayaran
                const paymentMethodType = $('#payment-method-type').val();
                if (!paymentMethodType) {
                    $('#payment-method-type').addClass('is-invalid');
                    isValid = false;
                } else if (paymentMethodType === 'transfer' && !$('#transfer-bank').val()) {
                    $('#transfer-bank').addClass('is-invalid');
                    isValid = false;
                } else if (paymentMethodType === 'kas_tunai' && !$('#pay-later').is(':checked')) {
                    // Validate saldo (pay-later expenses are deducted when paid)
                    const finalTotal = parseCurrency($('#final-total').val()) || 0;
                    if (finalTotal > kasBesarAvailable) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Saldo Tidak Mencukupi',
                            html: kasBesarAvailable < kasBesarSaldo
                                ? `Pengeluaran <strong>Rp ${finalTotal.toLocaleString('id-ID')}</strong> akan membuat saldo transaksi Kas Besar sesudah tanggal ini minus. Maksimal <strong>Rp ${kasBesarAvailable.toLocaleString('id-ID')}</strong>.`
                                : `Saldo Kas Besar <strong>Rp ${kasBesarSaldo.toLocaleString('id-ID')}</strong> tidak mencukupi untuk pengeluaran <strong>Rp ${finalTotal.toLocaleString('id-ID')}</strong>.`
                        });
                        return false;
                    }
                }

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

                if (!$('#entity-type').val()) {
                    $('#entity-type').addClass('is-invalid');
                    isValid = false;
                }

                if (!$('#budget').val()) {
                    $('#budget').addClass('is-invalid');
                    isValid = false;
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
