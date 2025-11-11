@extends('layouts.default')
@section('page_title')
    Tambah Sales Order
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Buat Sales Order</h4>
        <form class="card-body" id="sales-order-form" autocomplete="off">
            @csrf

            <!-- Section 1: Basic Transaction Information -->
            <div class="section-header mt-12">
                <h6>Informasi Dasar Transaksi</h6>
                <hr class="mx-n6">
            </div>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tanggal Transaksi <span style="color: red;">*</span></label>
                    <input type="date" class="form-control global-single-date-picker" id="sales-order-date"
                        name="date" value="{{ date('Y-m-d') }}" placeholder="YYYY-MM-DD" required />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nomor Transaksi <span style="color: red;">*</span></label>
                    <div class="row g-2">
                        <div class="col">
                            <input type="text" class="form-control" id="sales-order-reference" name="reference_no"
                                placeholder="Auto-generated but editable" required />
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-outline-primary d-flex align-items-center"
                                id="generate-so-number-btn" title="Generate New Number">
                                <i class="icon-base ti tabler-refresh"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Customer Information -->
            <div class="section-header mt-12">
                <h6>Informasi Customer</h6>
                <hr class="mx-n6">
            </div>

            <!-- Row 1: Customer -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Customer <span style="color: red;">*</span></label>
                    <div class="row g-2">
                        <div class="col">
                            <select class="global-select2 form-select" id="sales-order-customer" name="customer_id"
                                data-allow-clear="false" required>
                                <option value="">Pilih Customer</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}" data-email="{{ $customer->email }}"
                                        data-billing-address="{{ $customer->billing_address }}">{{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @if ($canWrite)
                            <div class="col-auto">
                                <a href="{{ route('master_data.contact.add') }}" target="_blank"
                                    class="btn btn-outline-primary d-flex align-items-center" title="Tambah Customer Baru">
                                    <i class="icon-base ti tabler-plus"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Row 2: Email, Alamat -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" id="sales-order-email" name="email" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Alamat</label>
                    <textarea class="form-control" id="sales-order-address" name="address" rows="2"
                        placeholder="Alamat akan otomatis terisi" disabled></textarea>
                </div>
            </div>

            <!-- Row 3: Nomor Referensi Customer -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Nomor Referensi Customer</label>
                    <input type="text" class="form-control" id="sales-order-customer-ref" name="customer_reference"
                        placeholder="Masukkan nomor referensi" />
                </div>
            </div>

            <!-- Section 3: Sales Details -->
            <div class="section-header mt-12">
                <h6>Detail Penjualan</h6>
                <hr class="mx-n6">
            </div>

            <!-- Row 1: Syarat Pembayaran -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Syarat Pembayaran <span style="color: red;">*</span></label>
                    <div class="row g-2">
                        <div class="col">
                            <select class="global-select2 form-select" id="sales-order-payment-terms" name="payment_terms"
                                data-allow-clear="false">
                                <option value="">Pilih Payment Terms</option>
                                @foreach ($paymentTerms as $term)
                                    <option value="{{ $term->id }}" data-days="{{ $term->days }}"
                                        data-type="{{ strtoupper($term->type) }}">{{ $term->code }} - {{ $term->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @if ($canWrite)
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
            </div>

            <!-- Row 2: Jumlah Hari, Tanggal Jatuh Tempo (conditional) -->
            <div class="row g-3 mb-4" id="payment-days-row" style="display: none;">
                <div class="col-md-6">
                    <label class="form-label">Jumlah Hari <span style="color: red;">*</span></label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="sales-order-days" name="payment_term_day_count"
                            placeholder="0" min="0" max="365" disabled />
                        <span class="input-group-text">Hari</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Jatuh Tempo</label>
                    <input type="date" class="form-control" id="sales-order-due-date" name="due_date" disabled />
                </div>
            </div>

            <!-- Row 2: Sales Person, Gudang -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Sales Person</label>
                    <div class="row g-2">
                        <div class="col">
                            <select class="global-select2 form-select" id="sales-order-sales-person"
                                name="sales_person_id" data-allow-clear="false">
                                <option value="">Pilih Sales Person</option>
                                @foreach ($salesPersons as $person)
                                    <option value="{{ $person->id }}">{{ $person->code }} -
                                        {{ $person->employee_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($canWrite)
                            <div class="col-auto">
                                <a href="{{ route('master_data.contact.add') }}" target="_blank"
                                    class="btn btn-outline-primary d-flex align-items-center"
                                    title="Tambah Sales Person Baru">
                                    <i class="icon-base ti tabler-plus"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Gudang <span style="color: red;">*</span></label>
                    <div class="row g-2">
                        <div class="col">
                            <select class="global-select2 form-select" id="sales-order-warehouse" name="warehouse_id"
                                data-allow-clear="false" required>
                                <option value="">Pilih Gudang</option>
                                @foreach ($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}">{{ $warehouse->code }} - {{ $warehouse->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @if ($canWrite)
                            <div class="col-auto">
                                <a href="{{ route('master_data.warehouse.add') }}" target="_blank"
                                    class="btn btn-outline-primary d-flex align-items-center" title="Tambah Gudang Baru">
                                    <i class="icon-base ti tabler-plus"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Row 3: Keterangan -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Keterangan</label>
                    <textarea class="form-control" id="sales-order-description" name="description" rows="2"
                        placeholder="Nomor order transaksi..." maxlength="1000"></textarea>
                </div>
            </div>

            <!-- Section 4: Product Information -->
            <div class="section-header mt-12">
                <h6>Informasi Produk</h6>
            </div>

            <!-- Disabled State Message -->
            <div id="product-disabled-message" class="alert alert-info">
                <i class="icon-base ti tabler-info-circle me-2"></i>
                Silakan pilih <strong>Customer</strong> dan <strong>Gudang</strong> terlebih dahulu untuk dapat menambah
                produk. Produk yang muncul adalah produk yang sudah di-mapping dengan customer tersebut.
            </div>

            <!-- Product Table -->
            <div class="mb-4 mx-n5">
                <div class="form-repeater-product">
                    <table class="table table-bordered" id="product-table">
                        <thead>
                            <tr>
                                <th style="width: 8%;">Is Discount</th>
                                <th style="width: 25%;">Produk</th>
                                <th style="width: 12%;">Kuantitas</th>
                                <th style="width: 10%;">Satuan</th>
                                <th style="width: 18%;">Harga Satuan</th>
                                {{-- <th style="width: 15%;">Pajak</th> --}}
                                <th style="width: 15%;">Jumlah</th>
                                <th style="width: 5%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="products-tbody" data-repeater-list>
                            <tr data-repeater-item class="product-row">
                                <td>
                                    <div class="form-check d-flex justify-content-center">
                                        <input class="form-check-input discount-checkbox" type="checkbox"
                                            name="products[0][is_discount]" value="1" disabled>
                                    </div>
                                </td>
                                <td>
                                    <div class="position-relative">
                                        <select class="select2Product form-select product-select"
                                            name="products[0][product_id]" data-allow-clear="false" disabled>
                                            <option value="">Pilih Produk</option>
                                        </select>
                                    </div>
                                </td>
                                <td><input type="text" class="form-control numeric-input quantity-input"
                                        name="products[0][quantity]" min="1" step="1" value="1"
                                        disabled /></td>
                                <td><input type="text" class="form-control unit-display" name="products[0][unit]"
                                        disabled /></td>
                                <td>
                                    <input type="text" class="form-control currency-input unit-price unit-price-input"
                                        name="products[0][unit_price]" disabled />
                                </td>
                                {{-- <td>
                                    <div class="position-relative">
                                        <select class="select2Tax form-select" name="products[0][tax_id]"
                                            data-allow-clear="false" disabled>
                                            <option value="">Pilih Pajak</option>
                                            @foreach ($taxOptions as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </td> --}}
                                <td><input type="text" class="form-control currency-input" name="products[0][total]"
                                        disabled /></td>
                                <td>
                                    <button class="btn btn-label-danger btn-sm" type="button"
                                        onclick="removeProductRow(this)" disabled>
                                        <i class="icon-base ti tabler-x me-1"></i>
                                        <span class="align-middle">Hapus</span>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="mb-0">
                        <button class="btn btn-primary" data-repeater-create type="button" id="add-product-row"
                            disabled>
                            <i class="icon-base ti tabler-plus me-1"></i>
                            <span class="align-middle">Tambah Baris</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Section 5: Additional Information & Summary -->
            <div class="section-header mt-12">
                <h6>Informasi Tambahan & Ringkasan</h6>
                <hr class="mx-n6">
            </div>

            <!-- Split Layout: Tag, Memo, Attachment & Financial Summary -->
            <div class="row">
                <!-- Left Side: Tag, Memo and Attachment -->
                <div class="col-md-6 mb-4">
                    <div class="mb-3">
                        <label class="form-label">Tag</label>
                        <div class="row g-2">
                            <div class="col">
                                <select class="global-select2 form-select" id="sales-order-tags" name="tags[]" multiple
                                    data-allow-clear="false">
                                    @foreach ($availableTags as $tag)
                                        <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if ($canWrite)
                                <div class="col-auto">
                                    <a href="{{ route('master_data.tag.add') }}" target="_blank"
                                        class="btn btn-outline-primary d-flex align-items-center" title="Tambah Tag Baru">
                                        <i class="icon-base ti tabler-plus"></i>
                                    </a>
                                </div>
                            @endif
                        </div>
                        <small class="form-text text-muted">Pilih opsi atau tambah baru jika diperlukan</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Memo (Internal)</label>
                        <textarea class="form-control" id="sales-order-memo" name="memo" rows="4"
                            placeholder="Catatan internal..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lampiran</label>
                        <input type="file" class="form-control" id="sales-order-attachment" name="attachment"
                            accept=".xlsx,.xls,.doc,.docx,.pdf,.png,.jpg,.jpeg,.zip" />
                        <small class="form-text text-muted">File dapat berupa Excel, Word, PDF, PNG, atau Zip (Max:
                            10MB)</small>
                    </div>
                </div>

                <!-- Right Side: Financial Summary Section -->
                <div class="col-md-6">
                    <div class="summary-section ps-4">
                        {{-- <div class="row mb-3">
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
                                    name="total_price_before_discount" dir="rtl" value="Rp 0" disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Diskon</label>
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control currency-input" id="discount-amount"
                                    name="discount_amount" dir="rtl" value="Rp 0">
                            </div>
                        </div> --}}
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label"><strong>Total Akhir</strong></label>
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control readonly-field fw-bold"
                                    id="display-grand-total" dir="rtl" value="Rp 0" disabled>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hidden fields for calculations -->
            {{-- <input type="hidden" id="sales-order-subtotal" name="subtotal" value="0" />
            <input type="hidden" id="sales-order-tax-amount" name="tax_amount" value="0" /> --}}
            <input type="hidden" id="sales-order-total" name="total" value="0" />
            <input type="hidden" id="sales-order-currency" name="currency" value="IDR" />

            <!-- Action Buttons -->
            <div class="pt-6 text-end">
                <a href="{{ route('main_transaction.sales_order.index') }}"
                    class="btn btn-label-danger me-3">Batalkan</a>
                <button type="submit" class="btn btn-success" id="submit-btn">Simpan</button>
            </div>
        </form>
    </div>
@stop

@section('js_script')
    <script>
        $(function() {
            initCleaveCurrency(document);
            let productTableEnabled = false;

            // Generate initial transaction number on page load
            generateTransactionNumber();

            // Initialize payment terms behavior using global handler
            PaymentTermsHandler.init({
                paymentTermsSelector: '#sales-order-payment-terms',
                daysRowSelector: '#payment-days-row',
                daysFieldSelector: '#sales-order-days',
                dueDateFieldSelector: '#sales-order-due-date',
                transactionDateSelector: '#sales-order-date'
            });

            // Generate transaction number function
            $('#generate-so-number-btn').on('click', function() {
                generateTransactionNumber();
            });

            // Handle discount checkbox change
            $(document).on('change', '.discount-checkbox', function() {
                const row = $(this).closest('tr');
                const unitPriceInput = row.find('.unit-price-input')[0];
                const isDiscounted = $(this).is(':checked');

                if (isDiscounted) {
                    // Set unit price to 0 when discount is checked
                    $(unitPriceInput).val('0');
                    if (unitPriceInput && unitPriceInput.cleave) {
                        unitPriceInput.cleave.setRawValue(0);
                    }
                    // Disable unit price input when discount is applied
                    $(unitPriceInput).prop('disabled', true);
                } else {
                    // Re-enable unit price input when discount is unchecked
                    $(unitPriceInput).prop('disabled', false);
                    // Optionally restore original price from product selection
                    const selectedOption = row.find('.product-select option:selected');
                    const originalPrice = selectedOption.data('price');
                    if (originalPrice) {
                        $(unitPriceInput).val(originalPrice);
                        if (unitPriceInput && unitPriceInput.cleave) {
                            unitPriceInput.cleave.setRawValue(originalPrice);
                        }
                    }
                }

                calculateTotals();
            });

            // Customer selection change
            $('#sales-order-customer').on('change', function() {
                const customerId = $(this).val();
                if (customerId) {
                    loadCustomerInfo(customerId);
                    checkProductTableEnabled();
                    loadProductsForRow();
                } else {
                    clearCustomerInfo();
                    checkProductTableEnabled();
                }
            });

            // Warehouse selection change
            $('#sales-order-warehouse').on('change', function() {
                checkProductTableEnabled();

            });

            $('select.select2Product').each(function() {
                $(this).select2({
                    placeholder: "Pilih Produk",
                    allowClear: true,
                    dropdownParent: $(this).parent(),
                    width: '100%'
                });
            });

            $('.form-repeater-product').repeater({
                show: function() {
                    $(this).slideDown(() => {
                        checkProductTableEnabled()
                        loadProductsForRow()
                        $(this).find('.select2-container').remove();
                        $(this).find('select.select2Product').removeClass(
                            'select2-hidden-accessible').removeAttr(
                            'data-select2-id tabindex aria-hidden');
                        $(this).find('select.select2Product').each(function() {
                            if ($.fn.select2 && $(this).data('select2')) {
                                // alert('Destroying existing Select2 instance');
                                $(this).select2('destroy');
                            }
                            $(this).select2({
                                placeholder: "Pilih Produk",
                                allowClear: true,
                                dropdownParent: $(this).parent(),
                                width: '100%'
                            });
                        });

                        initCleaveCurrency(document);
                    });
                },
                hide: function(remove) {
                    $(this).slideUp(remove);
                }
            });


            // Form submission
            $('#sales-order-form').on('submit', function(e) {
                e.preventDefault();

                if (!validateForm()) {
                    return false;
                }

                const formData = new FormData(this);
                const productLines = collectProductLines();

                if (productLines.length === 0) {
                    alert('Minimal harus ada satu produk yang ditambahkan.');
                    return false;
                }

                formData.append('payment_term_due_date', $('#sales-order-due-date').val());
                formData.append('payment_term_day_count', $('#sales-order-days').val());

                // formData.append('products', productLines);
                productLines.forEach(function(line, idx) {
                    formData.append(`products[${idx}][product_id]`, line.product_id);
                    formData.append(`products[${idx}][quantity]`, line.quantity);
                    formData.append(`products[${idx}][unit_price]`, line.unit_price);
                    formData.append(`products[${idx}][total]`, line.line_total);
                    formData.append(`products[${idx}][is_discount]`, line.is_discount);
                });

                Swal.fire({
                    title: 'Buat Sales Order?',
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
                        $('#submit-btn').prop('disabled', true).html(
                            '<span class="spinner-border spinner-border-sm me-2"></span>Loading...'
                        );

                        $.ajax({
                            url: '{{ route('main_transaction.sales_order.store') }}',
                            method: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Sales Order berhasil disimpan.',
                                    customClass: {
                                        confirmButton: 'btn btn-success'
                                    },
                                    buttonsStyling: false
                                }).then(function() {
                                    window.location.href =
                                        "{{ route('main_transaction.sales_order.index') }}";
                                });
                            },
                            error: function(xhr) {
                                let errorMessage =
                                    'Terjadi kesalahan saat menyimpan data.';

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
            });

            // Generate transaction number function
            function generateTransactionNumber() {
                $.ajax({
                    url: '{{ route('main_transaction.sales_order.generate_transaction_number') }}',
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            $('#sales-order-reference').val(response.transaction_number);
                        }
                    },
                    error: function() {
                        console.error('Failed to generate transaction number');
                    }
                });
            }

            // Load customer information
            function loadCustomerInfo(customerId) {
                $.ajax({
                    url: '{{ route('main_transaction.sales_order.get_customer_info', ':customer_id') }}'
                        .replace(':customer_id', customerId),
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            $('#sales-order-email').val(response.data.email || '');
                            $('#sales-order-address').val(response.data.address || '');
                            $('#sales-order-sales-person').val(response.data.sales_person_name || '-');
                        }
                    },
                    error: function() {
                        console.error('Failed to load customer info');
                    }
                });
            }

            // Clear customer information
            function clearCustomerInfo() {
                $('#sales-order-email').val('');
                $('#sales-order-address').val('');
                $('#sales-order-sales-person').val('');
            }

            // Check product table enabled state
            function checkProductTableEnabled() {
                const customerId = $('#sales-order-customer').val();
                const warehouseId = $('#sales-order-warehouse').val();

                if (customerId && warehouseId) {
                    enableProductTable();
                } else {
                    disableProductTable();
                }
            }

            // Enable product table
            function enableProductTable() {
                productTableEnabled = true;
                $('#product-disabled-message').hide();
                $('#add-product-row').prop('disabled', false);
                $('.product-row input:not(.unit-display):not([name$="[total]"]), .product-row select, .product-row button')
                    .prop('disabled', false);
            }

            // Disable product table
            function disableProductTable() {
                productTableEnabled = false;
                $('#product-disabled-message').show();
                $('#add-product-row').prop('disabled', true);
                $('.product-row input:not(.unit-display):not([name$="[total]"]), .product-row select, .product-row button')
                    .prop('disabled', true);
            }

            // Load products for a specific row
            function loadProductsForRow() {
                const customerId = $('#sales-order-customer').val();

                if (!customerId) return;
                // const warehouseId = $('#sales-order-warehouse').val();
                $.ajax({
                    url: '{{ route('main_transaction.sales_order.get_warehouse_products', ':customerId') }}'
                        .replace(':customerId', customerId),
                    method: 'GET',
                    data: {
                        customer_id: customerId
                    },
                    success: function(response) {
                        if (response.success) {
                            const products = response.data;
                            $('#product-table tbody tr').each(function() {
                                const productSelect = $(this).find('.product-select');
                                if (productSelect.val()) return;
                                productSelect.empty().append(
                                    '<option value="">Pilih Produk</option>');
                                products.forEach(product => {
                                    productSelect.append(
                                        `<option value="${product.product_id}" data-unit="${product.unit_name}" data-price="${product.price}">${product.product_code} - ${product.product_name}</option>`
                                    );
                                });
                            });
                        } else {
                            alert('Gagal memuat produk: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('Terjadi kesalahan saat memuat produk.');
                    }
                });
            }

            $(document).on('change', '.product-select', function() {
                const selectedOption = $(this).find(':selected');

                const unit = selectedOption.data('unit') || '';
                const perPrice = selectedOption.data('price');
                const row = $(this).closest('tr');

                if (unit) {
                    row.find('.unit-display').val(unit).prop('disabled', true);
                } else {
                    row.find('.unit-display').val('').prop('disabled', true);
                }

                if (perPrice) {
                    const unitPriceInput = row.find('.unit-price')[0];
                    $(unitPriceInput).val(perPrice);

                    // If Cleave is already initialized, use setRawValue
                    if (unitPriceInput && unitPriceInput.cleave) {
                        unitPriceInput.cleave.setRawValue(perPrice);
                    }
                }

                calculateTotals();
            });

            // Calculate all totals
            function calculateTotals() {
                let subtotal = 0;
                // let totalTax = 0;

                $('#products-tbody tr').each(function() {
                    const quantity = parseCurrency($(this).find('.quantity-input').val()) || 0;
                    const unitPrice = parseCurrency($(this).find('.unit-price-input').val()) || 0;
                    // const taxId = $(this).find('.tax-select').val();

                    const lineSubtotal = quantity * unitPrice;
                    subtotal += lineSubtotal;

                    $(this).find('input[name*="[total]"]').val(formatCurrency(lineSubtotal));
                });

                const grandTotal = subtotal; // No discount for now

                $('#sales-order-total').val(grandTotal);
                $('#display-grand-total').val(formatCurrency(grandTotal));
            }

            // Collect product lines for form submission
            function collectProductLines() {
                let productLines = [];

                $('#products-tbody tr').each(function() {
                    const productId = $(this).find('.product-select').val();
                    const quantity = parseCurrency($(this).find('.quantity-input').val()) || 0;
                    const unitPrice = parseCurrency($(this).find('.unit-price').val()) || 0;
                    const lineTotal = parseCurrency($(this).find('input[name*="[total]"]').val()) || 0;
                    const isDiscount = $(this).find('.discount-checkbox').is(':checked') ? 1 : 0;

                    if (productId && quantity > 0 && unitPrice >= 0) {
                        productLines.push({
                            product_id: productId,
                            quantity: quantity,
                            unit_price: unitPrice,
                            line_total: lineTotal,
                            is_discount: isDiscount
                        });
                    }
                });

                return productLines;
            }

            // Validate form
            function validateForm() {
                const customerId = $('#sales-order-customer').val();
                const warehouseId = $('#sales-order-warehouse').val();
                const paymentTermId = $('#sales-order-payment-terms').val();
                const transactionDate = $('#sales-order-date').val();
                const productLines = collectProductLines();

                if (!customerId) {
                    alert('Customer harus dipilih.');
                    return false;
                }

                if (!warehouseId) {
                    alert('Gudang harus dipilih.');
                    return false;
                }

                if (!paymentTermId) {
                    alert('Syarat Pembayaran harus dipilih.');
                    return false;
                }

                if (!transactionDate) {
                    alert('Tanggal Transaksi harus diisi.');
                    return false;
                }

                if (productLines.length === 0) {
                    alert('Minimal harus ada satu produk yang ditambahkan.');
                    return false;
                }

                return true;
            }

            // Format currency
            function formatCurrency(amount) {
                return 'Rp ' + parseFloat(amount).toLocaleString('id-ID', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 2
                });
            }

            $(document).on('input change',
                'input[name*="[quantity]"], input[name*="[unit_price]"]',
                function() {
                    calculateTotals();
                });

            window.removeProductRow = function(button) {
                if ($('#product-table tbody tr').length > 1) {
                    $(button).closest('tr').remove();
                    calculateTotals();
                }
            };
        });
    </script>
@endsection
