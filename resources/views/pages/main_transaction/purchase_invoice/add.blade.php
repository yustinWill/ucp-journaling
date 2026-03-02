@extends('layouts.default')
@section('page_title')
    Tambah Invoice
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Tambah Invoice</h4>
        <form class="card-body" id="purchaseInvoiceForm" autocomplete="off">
            @csrf

            <!-- Informasi Dasar Transaksi Section -->
            <div class="section-header mt-12">
                <h6>Informasi Dasar Transaksi</h6>
                <hr class="mx-n6">
            </div>

            <!-- Row 1: Invoice Date, Invoice Number -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tanggal Invoice <span style="color: red;">*</span></label>
                    <input type="date" class="form-control global-single-date-picker" id="invoice-date"
                        name="invoice_date" value="{{ date('Y-m-d') }}" required />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nomor Transaksi <span style="color: red;">*</span></label>
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

            <!-- Hidden field for PO id -->
            <input type="hidden" id="purchase-order-id" name="purchase_order_id" value="{{ $purchaseOrder->id ?? $preselectedPOId }}" />

            <!-- Informasi Purchase Order Section -->
            <div class="section-header mt-12">
                <h6>Informasi Purchase Order</h6>
                <hr class="mx-n6">
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Supplier</label>
                    <input type="text" class="form-control" value="{{ $purchaseOrder->supplier_name ?? '' }}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email Supplier</label>
                    <input type="text" class="form-control" value="{{ $purchaseOrder->supplier_email ?? '' }}" disabled />
                </div>
            </div>

            <!-- Informasi Invoice Section -->
            <div class="section-header mt-12">
                <h6>Informasi Invoice</h6>
                <hr class="mx-n6">
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tanggal Transaksi <span style="color: red;">*</span></label>
                    <input type="date" class="form-control global-single-date-picker" id="transaction-date"
                        name="transaction_date" value="{{ date('Y-m-d') }}" required />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Jatuh Tempo</label>
                    <input type="text" class="form-control" disabled
                        value="{{ $purchaseOrder->payment_term_due_date ?? 'N/A' }}" />
                    <small class="text-muted">*Dihitung otomatis dari payment terms PO</small>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Termin Pembayaran</label>
                    <input type="text" class="form-control"
                        value="{{ $purchaseOrder->payment_term_name ?? ($purchaseOrder->payment_terms ?? '') }}"
                        disabled />
                    <small class="text-muted">*Dari Purchase Order</small>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Keterangan</label>
                    <textarea class="form-control" id="description" name="description" rows="4"
                        placeholder="Keterangan tambahan untuk invoice" maxlength="1000"></textarea>
                </div>
            </div>

            <!-- Items Invoice Section -->
            <div class="section-header mt-12">
                <h6>Items Purchase Order</h6>
            </div>

            <!-- Product Table (reference only) -->
            <div class="mb-4 mx-n5">
                <div class="table-responsive">
                    <table class="table table-bordered" id="invoice-items-table">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Qty PO</th>
                                <th>Harga Satuan</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody id="invoice-items-tbody">
                            @if ($purchaseOrderProducts && count($purchaseOrderProducts) > 0)
                                @php $counter = 0; @endphp
                                @foreach ($purchaseOrderProducts as $product)
                                    @php
                                        $qty = $product->qty;
                                        $unitPrice = $product->price_per_qty ?? 0;
                                        $rowTotal = $qty * $unitPrice;

                                        $formattedQty = $qty == floor($qty)
                                            ? number_format($qty, 0, ',', '.')
                                            : rtrim(rtrim(number_format($qty, 4, ',', '.'), '0'), ',');

                                        $formattedPrice = $unitPrice == floor($unitPrice)
                                            ? number_format($unitPrice, 0, ',', '.')
                                            : rtrim(rtrim(number_format($unitPrice, 2, ',', '.'), '0'), ',');

                                        $formattedTotal = number_format($rowTotal, 0, ',', '.');
                                    @endphp
                                    <tr>
                                        <td>{{ $product->product_code }} - {{ $product->product_name }}</td>
                                        <td>{{ $formattedQty }} {{ $product->unit_name }}</td>
                                        <td>Rp {{ $formattedPrice }}</td>
                                        <td>Rp {{ $formattedTotal }}</td>
                                        {{-- Hidden inputs for store --}}
                                        <input type="hidden" name="products[{{ $counter }}][product_order_product_id]"
                                            value="{{ $product->id }}" />
                                        <input type="hidden" name="products[{{ $counter }}][product_id]"
                                            value="{{ $product->f_product_id }}" />
                                        <input type="hidden" name="products[{{ $counter }}][po_qty]"
                                            value="{{ $product->qty }}" />
                                        <input type="hidden" name="products[{{ $counter }}][unit_price]"
                                            value="{{ $unitPrice }}" />
                                        <input type="hidden" name="products[{{ $counter }}][total_price]"
                                            value="{{ $rowTotal }}" />
                                    </tr>
                                    @php $counter++; @endphp
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="icon-base ti tabler-package me-2"></i>
                                        Data produk tidak ditemukan untuk Purchase Order ini
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
                                        <option value="{{ $tag->id }}">{{ $tag->code }} - {{ $tag->name }}</option>
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
                        <small class="form-text text-muted">File dapat berupa Excel, Word, PDF, PNG, atau Zip (Max: 10MB)</small>
                    </div>
                </div>

                <!-- Right Side: Invoice Summary -->
                <div class="col-md-6">
                    <div class="summary-section ps-4">
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Grand Total PO</label>
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control" id="po-grand-total-display" dir="rtl" disabled
                                    value="Rp {{ number_format($purchaseOrder->grand_price ?? 0, 0, ',', '.') }}">
                            </div>
                        </div>
                        @if ($alreadyInvoicedAmount > 0)
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="form-label">Sudah Diinvoice</label>
                                </div>
                                <div class="col-6">
                                    <input type="text" class="form-control" id="already-invoiced-display" dir="rtl"
                                        disabled value="Rp {{ number_format($alreadyInvoicedAmount, 0, ',', '.') }}">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="form-label">Sisa Belum Diinvoice</label>
                                </div>
                                <div class="col-6">
                                    <input type="text" class="form-control" id="remaining-display" dir="rtl" disabled
                                        value="Rp {{ number_format($remainingAmount, 0, ',', '.') }}">
                                </div>
                            </div>
                        @endif
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold">Jumlah Invoice Ini <span style="color: red;">*</span></label>
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control numeric-input" id="invoice-amount-input"
                                    name="total_amount" dir="rtl" required />
                                <input type="hidden" id="invoice-status-hidden" name="invoice_status" value="PARTIAL" />
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Status Invoice</label>
                            </div>
                            <div class="col-6 d-flex align-items-center">
                                <span class="badge bg-label-warning" id="invoice-status">PARTIAL</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hidden values for JS -->
            <input type="hidden" id="po-grand-total-value" value="{{ $purchaseOrder->grand_price ?? 0 }}" />
            <input type="hidden" id="already-invoiced-value" value="{{ $alreadyInvoicedAmount }}" />
            <input type="hidden" id="remaining-value" value="{{ $remainingAmount }}" />

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

            const poGrandTotal = parseFloat($('#po-grand-total-value').val()) || 0;
            const alreadyInvoiced = parseFloat($('#already-invoiced-value').val()) || 0;
            const remaining = poGrandTotal - alreadyInvoiced;

            // Pre-fill invoice amount with remaining balance
            const amountInput = document.getElementById('invoice-amount-input');
            if (amountInput && amountInput.cleave) {
                amountInput.cleave.setRawValue(remaining);
            } else {
                $('#invoice-amount-input').val(remaining > 0 ? remaining : poGrandTotal);
            }

            updateStatus();

            // Generate initial invoice number
            generateInvoiceNumber();
            $('#generate-invoice-number').on('click', generateInvoiceNumber);

            // Update status whenever amount changes
            $('#invoice-amount-input').on('input change', function() {
                updateStatus();
            });

            $('#purchaseInvoiceForm').on('submit', submitForm);

            function updateStatus() {
                const amount = parseCurrency($('#invoice-amount-input').val()) || 0;
                if (amount > 0 && amount >= remaining) {
                    $('#invoice-status')
                        .removeClass('bg-label-warning')
                        .addClass('bg-label-success')
                        .text('LUNAS');
                    $('#invoice-status-hidden').val('COMPLETED');
                } else {
                    $('#invoice-status')
                        .removeClass('bg-label-success')
                        .addClass('bg-label-warning')
                        .text('PARTIAL');
                    $('#invoice-status-hidden').val('PARTIAL');
                }
            }

            function generateInvoiceNumber() {
                $('#generate-invoice-number').prop('disabled', true);
                const year = new Date().getFullYear();
                const month = String(new Date().getMonth() + 1).padStart(2, '0');
                const random = Math.floor(Math.random() * 999) + 1;
                $('#invoice-number').val(`INV-${year}${month}-${String(random).padStart(3, '0')}`);
                $('#generate-invoice-number').prop('disabled', false);
            }

            function submitForm(e) {
                e.preventDefault();

                if (!validateForm()) return;

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
                const requiredFields = ['invoice-number', 'invoice-date', 'transaction-date', 'invoice-amount-input'];
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

                const amount = parseCurrency($('#invoice-amount-input').val()) || 0;
                if (amount <= 0) {
                    $('#invoice-amount-input').addClass('is-invalid');
                    isValid = false;
                }

                if (!isValid) {
                    Swal.fire({
                        title: 'Field Wajib!',
                        text: 'Silakan lengkapi semua field yang wajib diisi.',
                        icon: 'warning',
                        customClass: { confirmButton: 'btn btn-primary' },
                        buttonsStyling: false
                    });
                }

                return isValid;
            }

            function processFormSubmission() {
                const submitBtn = $('#save-invoice-btn');
                const originalText = submitBtn.text();

                submitBtn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...'
                );

                const formData = new FormData($('#purchaseInvoiceForm')[0]);

                // Ensure the raw numeric value is used for total_amount
                const rawAmount = parseCurrency($('#invoice-amount-input').val()) || 0;
                formData.set('total_amount', rawAmount);

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
                            customClass: { confirmButton: 'btn btn-primary' },
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
                            customClass: { confirmButton: 'btn btn-primary' },
                            buttonsStyling: false
                        });
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).text(originalText);
                    }
                });
            }
        });
    </script>
@endsection
