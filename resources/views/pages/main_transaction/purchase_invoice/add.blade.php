@extends('layouts.default')
@section('page_title')
    Tambah Invoice
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Tambah Invoice</h4>
        <form class="card-body" id="purchaseInvoiceForm" autocomplete="off"
            data-po-data="{{ $purchaseOrder ? base64_encode(json_encode($purchaseOrder)) : '' }}"
            data-po-products="{{ $purchaseOrderProducts ? base64_encode(json_encode($purchaseOrderProducts)) : '' }}">
            @csrf

            <!-- Informasi Dasar Transaksi Section -->
            <div class="section-header mt-12">
                <h6>Informasi Dasar Transaksi</h6>
                <hr class="mx-n6">
            </div>

            <!-- Row 1: Tanggal Invoice, Nomor Invoice -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tanggal Invoice <span style="color: red;">*</span></label>
                    <input type="date" class="form-control global-single-date-picker" id="invoice-date"
                        name="invoice_date" value="{{ date('Y-m-d') }}" placeholder="YYYY-MM-DD" required />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nomor Invoice <span style="color: red;">*</span></label>
                    <div class="row g-2">
                        <div class="col">
                            <input type="text" class="form-control" id="invoice-number" name="invoice_number"
                                placeholder="Auto-generated but editable" required />
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-outline-primary d-flex align-items-center"
                                id="generate-invoice-number" title="Generate New Number">
                                <i class="icon-base ti tabler-refresh"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 2: Purchase Order Reference -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Purchase Order</label>
                    <input type="text" class="form-control" id="po-reference"
                        value="{{ $purchaseOrder->transaction_code ?? '' }}" disabled />
                    <small class="form-text text-muted">Purchase Order dipilih dari halaman detail PO</small>
                </div>
            </div>

            <!-- Hidden fields for PO data -->
            <input type="hidden" id="purchase-order-id" name="purchase_order_id" value="{{ $purchaseOrder->id ?? '' }}" />

            <!-- Informasi Purchase Order Section -->
            <div class="section-header mt-12" id="po-info-section">
                <h6>Informasi Purchase Order</h6>
                <hr class="mx-n6">
            </div>

            <div class="row g-3 mb-4" id="po-info-content">
                <div class="col-md-6">
                    <label class="form-label">Supplier</label>
                    <input type="text" class="form-control" id="po-supplier"
                        value="{{ $purchaseOrder->supplier_name ?? '' }}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email Supplier</label>
                    <input type="text" class="form-control" id="po-email"
                        value="{{ $purchaseOrder->supplier_email ?? '' }}" disabled />
                </div>
            </div>

            <div class="row g-3 mb-4" id="po-info-content-2">
                <div class="col-md-6">
                    <label class="form-label">Status PO</label>
                    <input type="text" class="form-control" id="po-status" value="{{ $purchaseOrder->status ?? '' }}"
                        disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Grand Total PO</label>
                    <input type="text" class="form-control" id="po-grand-total"
                        value="{{ $purchaseOrder->grand_price ? 'Rp ' . number_format($purchaseOrder->grand_price, 0, ',', '.') : '' }}"
                        disabled />
                </div>
            </div>

            <!-- Informasi Invoice Section -->
            <div class="section-header mt-12">
                <h6>Informasi Invoice</h6>
                <hr class="mx-n6">
            </div>

            <!-- Row 1: Transaction and Due Dates -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tanggal Transaksi <span style="color: red;">*</span></label>
                    <input type="date" class="form-control global-single-date-picker" id="transaction-date"
                        name="transaction_date" value="{{ date('Y-m-d') }}" required />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Jatuh Tempo</label>
                    <input type="text" class="form-control" id="due-date" disabled
                        value="{{ $purchaseOrder->payment_term_due_date ?? 'N/A' }}" />
                    <small class="text-muted">*Dihitung otomatis dari tanggal transaksi dan payment terms</small>
                </div>
            </div>

            <!-- Row 3: Payment Terms -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Termin Pembayaran</label>
                    <input type="text" class="form-control" id="payment-terms" name="payment_terms"
                        value="{{ $purchaseOrder->payment_term_name ?? ($purchaseOrder->payment_terms ?? '') }}"
                        disabled />
                    <small class="text-muted">*Dari Purchase Order</small>
                </div>
            </div>

            <!-- Row 4: Additional Information -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Keterangan</label>
                    <textarea class="form-control" id="description" name="description" rows="4"
                        placeholder="Keterangan tambahan untuk invoice" maxlength="1000"></textarea>
                </div>
            </div>

            <!-- Items Invoice Section -->
            <div class="section-header mt-12">
                <h6>Items Invoice</h6>
            </div>

            <!-- Info Box -->
            <div class="alert alert-info">
                <i class="icon-base ti tabler-info-circle me-2"></i>
                Info: Pilih dan masukkan jumlah item yang akan di-invoice. Sistem akan otomatis menghitung total amount.
            </div>

            <!-- Product Table -->
            <div class="mb-4 mx-n5">
                <div class="table-responsive">
                    <table class="table table-bordered" id="invoice-items-table">
                        <thead>
                            <tr>
                                <th style="width: 30%;">Produk</th>
                                <th style="width: 12%;">Qty PO</th>
                                <th style="width: 12%;">Invoiced</th>
                                <th style="width: 12%;">Remaining</th>
                                <th style="width: 20%;">Qty Invoice</th>
                                <th style="width: 20%;">Unit Price</th>
                                <th style="width: 20%;">Total</th>
                            </tr>
                        </thead>
                        <tbody id="invoice-items-tbody">
                            @if ($purchaseOrderProducts && count($purchaseOrderProducts) > 0)
                                @php $counter = 0; @endphp
                                @foreach ($purchaseOrderProducts as $product)
                                    <tr>
                                        <td>{{ $product->product_code }} - {{ $product->product_name }}</td>
                                        <td>{{ number_format($product->qty, 0, ',', '.') }} {{ $product->unit_name }}</td>
                                        <td>{{ number_format($product->invoiced_qty ?? 0, 0, ',', '.') }}
                                            {{ $product->unit_name }}</td>
                                        <td>{{ number_format($product->remaining_qty_for_invoice ?? $product->qty, 0, ',', '.') }}
                                            {{ $product->unit_name }}</td>
                                        <td>
                                            <input type="hidden"
                                                name="products[{{ $counter }}][product_order_product_id]"
                                                value="{{ $product->id }}" />
                                            <input type="hidden" name="products[{{ $counter }}][product_id]"
                                                value="{{ $product->f_product_id }}" />
                                            <input type="hidden" name="products[{{ $counter }}][po_qty]"
                                                value="{{ $product->qty }}" />
                                            <input type="hidden"
                                                name="products[{{ $counter }}][invoiced_qty_before]"
                                                value="{{ $product->invoiced_qty ?? 0 }}" />
                                            <input type="hidden"
                                                name="products[{{ $counter }}][remaining_qty_before]"
                                                value="{{ $product->remaining_qty_for_invoice ?? $product->qty }}" />
                                            <input type="text"
                                                data-remaining="{{ $product->remaining_qty_for_invoice ?? $product->qty }}"
                                                value="0" class="form-control invoice-qty-input numeric-input"
                                                name="products[{{ $counter }}][invoice_qty]" min="0"
                                                max="{{ $product->remaining_qty_for_invoice ?? $product->qty }}" />
                                        </td>
                                        <td>{{ number_format($product->price_per_qty ?? 0, 0, ',', '.') }}
                                            <input type="hidden" name="products[{{ $counter }}][unit_price]"
                                                value="{{ $product->price_per_qty ?? 0 }}" />
                                        </td>
                                        <td>
                                            <span class="total-price" id="total-{{ $counter }}">0</span>
                                            <input type="hidden" name="products[{{ $counter }}][total_price]"
                                                value="0" />
                                        </td>
                                    </tr>
                                    @php $counter++; @endphp
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="icon-base ti tabler-package me-2"></i>
                                        Data produk akan muncul setelah Purchase Order dipilih
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Informasi Tambahan & Ringkasan Section -->
            <div class="section-header mt-12">
                <h6>Informasi Tambahan & Ringkasan</h6>
                <hr class="mx-n6">
            </div>

            <!-- Split Layout: Tag, Memo, Attachment & Invoice Summary -->
            <div class="row">
                <!-- Left Side: Tag, Memo and Attachment -->
                <div class="col-md-6 mb-4">
                    <div class="mb-3">
                        <label class="form-label">Tag</label>
                        <div class="row g-2">
                            <div class="col">
                                <select class="global-select2 form-select" id="invoice-tags" name="tags[]" multiple
                                    data-allow-clear="false">
                                    @foreach ($availableTags as $tag)
                                        <option value="{{ $tag->id }}">{{ $tag->code }} - {{ $tag->name }}
                                        </option>
                                    @endforeach
                                </select>
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
                        <textarea class="form-control" id="internal-memo" name="internal_memo" rows="4"
                            placeholder="Catatan internal..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lampiran</label>
                        <input type="file" class="form-control" id="invoice-attachment" name="attachment"
                            accept=".xlsx,.xls,.doc,.docx,.pdf,.png,.jpg,.jpeg,.zip" />
                        <small class="form-text text-muted">File dapat berupa Excel, Word, PDF, PNG, atau Zip (Max:
                            10MB)</small>
                    </div>
                </div>

                <!-- Right Side: Invoice Summary Section -->
                <div class="col-md-6">
                    <div class="summary-section ps-4">
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Total Amount</label>
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control readonly-field" id="total-invoice-amount"
                                    dir="rtl" value="Rp 0" disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Total Items</label>
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control readonly-field" id="total-items-invoice"
                                    dir="rtl" value="0 items" disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Status Invoice</label>
                            </div>
                            <div class="col-6">
                                <span class="badge bg-label-warning" id="invoice-status">UNPAID</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hidden fields for calculations -->
            <input type="hidden" id="purchase-order-id" name="purchase_order_id" value="{{ $preselectedPOId }}" />
            <input type="hidden" id="invoice-total-amount" name="total_amount" value="0" />
            <input type="hidden" id="invoice-total-items" name="total_items" value="0" />

            <!-- Action Buttons -->
            <div class="pt-6 text-end">
                <a href="{{ route('main_transaction.purchase_order.detail', $preselectedPOId) }}"
                    class="btn btn-label-secondary me-3">Batal</a>
                <button type="submit" class="btn btn-primary" id="save-invoice-btn">Simpan Invoice</button>
            </div>
        </form>
    </div>
@stop

@section('js_script')
    <script>
        $(function() {
            initCleaveCurrency(document);
            // Initialize the form
            initializeForm();

            function initializeForm() {
                // Generate initial invoice number
                generateInvoiceNumber();

                // Event handlers
                $('#generate-invoice-number').on('click', generateInvoiceNumber);
                $('#invoice-date').on('change', function() {
                    // No due date calculation on invoice date change
                });

                $('#purchaseInvoiceForm').on('submit', submitForm);

                // Initialize quantity change handlers for pre-filled data
                $('.invoice-qty-input').on('input change', function() {

                    const remaining = parseFloat($(this).data('remaining')) || 0;
                    const invoiceQty = convertToIndonesianFormat($(this).val()) || 0;

                    if (invoiceQty > remaining) {
                        $(this).addClass('is-invalid');
                        // Optionally show a message below the input
                        if (!$(this).next('.invalid-feedback').length) {
                            $(this).after(
                                '<div class="invalid-feedback">Qty invoice tidak boleh lebih dari remaining.</div>'
                            );
                        }
                    } else {
                        $(this).removeClass('is-invalid');
                        $(this).next('.invalid-feedback').remove();
                    }

                    const index = $(this).closest('tr').index();
                    calculateRowTotal(index);
                    calculateInvoiceTotal();
                    updateInvoiceStatus();
                });
            }

            function generateInvoiceNumber() {
                $('#generate-invoice-number').prop('disabled', true);

                // Mock generation - in real implementation, call backend
                const year = new Date().getFullYear();
                const month = String(new Date().getMonth() + 1).padStart(2, '0');
                const random = Math.floor(Math.random() * 999) + 1;
                const number = `INV-${year}${month}-${String(random).padStart(3, '0')}`;

                $('#invoice-number').val(number);
                $('#generate-invoice-number').prop('disabled', false);
            }

            function calculateRowTotal(index) {
                const qtyInput = $(`input[name="products[${index}][invoice_qty]"]`);
                const unitPriceInput = $(`input[name="products[${index}][unit_price]"]`);
                const totalSpan = $(`#total-${index}`);
                const totalHidden = $(`input[name="products[${index}][total_price]"]`);

                const qty = parseCurrency(qtyInput.val()) || 0;
                const unitPrice = parseFloat(unitPriceInput.val()) || 0;
                const total = qty * unitPrice;

                totalSpan.text(formatCurrency(total));
                totalHidden.val(total);
            }

            function calculateInvoiceTotal() {
                let totalAmount = 0;
                let totalItems = 0;

                $('input[name*="[total_price]"]').each(function() {
                    const amount = parseFloat($(this).val()) || 0;
                    totalAmount += amount;
                });

                $('input[name*="[invoice_qty]"]').each(function() {
                    const qty = parseFloat($(this).val()) || 0;
                    if (qty > 0) {
                        totalItems++;
                    }
                });

                $('#total-invoice-amount').val(formatCurrency(totalAmount));
                $('#total-items-invoice').val(`${totalItems} items`);
                $('#invoice-total-amount').val(totalAmount);
                $('#invoice-total-items').val(totalItems);
            }

            function updateInvoiceStatus() {
                let allCompleted = true;

                $('#invoice-items-tbody tr').each(function() {
                    const remaining = parseFloat($(this).find('input[name*="[remaining_qty_before]"]')
                        .val()) || 0;
                    const invoiceQty = parseFloat($(this).find('input[name*="[invoice_qty]"]').val()) || 0;
                    if ((remaining - invoiceQty) !== 0) {
                        allCompleted = false;
                    }
                });

                if (allCompleted) {
                    $('#invoice-status').removeClass('bg-label-warning').addClass('bg-label-success').text(
                        'COMPLETED');
                } else {
                    $('#invoice-status').removeClass('bg-label-success').addClass('bg-label-warning').text(
                        'PARTIAL');
                }
            }

            // Call this function after every quantity change
            $('.invoice-qty-input, .invoice-qty').on('input change', function() {
                updateInvoiceStatus();
            });

            function validateTransactionDate() {
                const invoiceDate = new Date($('#invoice-date').val());
                const transactionDate = new Date($('#transaction-date').val());

                if (transactionDate < invoiceDate) {
                    Swal.fire({
                        title: 'Tanggal Invalid!',
                        text: 'Tanggal transaksi tidak boleh lebih awal dari tanggal invoice.',
                        icon: 'warning',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });

                    $('#transaction-date').val($('#invoice-date').val());
                }
            }

            function submitForm(e) {
                e.preventDefault();

                // Validation
                if (!validateForm()) {
                    return;
                }

                // Check if at least one item has quantity > 0
                let hasItems = false;
                $('input[name*="[invoice_qty]"]').each(function() {
                    if (parseFloat($(this).val()) > 0) {
                        hasItems = true;
                        return false;
                    }
                });

                if (!hasItems) {
                    Swal.fire({
                        title: 'Tidak Ada Item!',
                        text: 'Pilih minimal satu item dengan quantity lebih dari 0.',
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
                    title: 'Simpan Invoice?',
                    text: 'Pastikan data yang diisi sudah benar.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-primary',
                        cancelButton: 'btn btn-label-secondary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        processFormSubmission();
                    }
                });
            }

            function validateForm() {
                const requiredFields = ['invoice-number', 'invoice-date', 'transaction-date'];
                let isValid = true;

                requiredFields.forEach(fieldId => {
                    const field = $('#' + fieldId);
                    if (!field.val() || field.val().trim() === '') {
                        field.addClass('is-invalid');
                        isValid = false;
                    } else {
                        field.removeClass('is-invalid');
                    }
                });

                if (!isValid) {
                    Swal.fire({
                        title: 'Field Wajib!',
                        text: 'Silakan lengkapi semua field yang wajib diisi.',
                        icon: 'warning',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                }

                return isValid;
            }

            function processFormSubmission() {
                const submitBtn = $('#save-invoice-btn');
                const originalText = submitBtn.text();

                // Show loading state
                submitBtn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...'
                );

                // Prepare form data
                const formData = new FormData($('#purchaseInvoiceForm')[0]);
                const invoiceStatus = $('#invoice-status').text().trim();
                formData.append('invoice_status', invoiceStatus);
                // Submit via AJAX
                $.ajax({
                    url: "{{ route('main_transaction.purchase_invoice.store') }}",
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Invoice berhasil dibuat.',
                            icon: 'success',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        }).then(() => {
                            if (response.data && response.data.id) {
                                window.location.href =
                                    "{{ route('main_transaction.purchase_invoice.detail', ':id') }}"
                                    .replace(':id', response.data.id);
                            } else {
                                window.location.href =
                                    "{{ route('main_transaction.purchase_invoice.index') }}";
                            }
                        });
                    },
                    error: function(xhr) {
                        let errorMessage = 'Terjadi kesalahan saat menyimpan invoice.';

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
                        submitBtn.prop('disabled', false).text(originalText);
                    }
                });
            }

            function formatDate(dateString) {
                if (!dateString) return '';
                const date = new Date(dateString);
                return date.toLocaleDateString('id-ID');
            }

            function formatCurrency(amount) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(amount || 0);
            }
        });
    </script>
@endsection
