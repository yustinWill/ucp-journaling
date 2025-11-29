@extends('layouts.default')
@section('page_title')
    Edit Sales Order - {{ $salesOrder['transaction_code'] }}
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Edit Sales Order</h4>
        <form class="card-body" id="sales-order-form" autocomplete="off">
            @csrf
            <input type="hidden" name="id" value="{{ $salesOrder['id'] }}" />

            <!-- Section 1: Basic Transaction Information -->
            <div class="section-header mt-12">
                <h6>Informasi Dasar Transaksi</h6>
                <hr class="mx-n6">
            </div>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tanggal Transaksi <span style="color: red;">*</span></label>
                    <input type="date" class="form-control global-single-date-picker" id="sales-order-date"
                        name="date" value="{{ $salesOrder['transaction_date'] }}" placeholder="YYYY-MM-DD" required />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nomor Transaksi <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="sales-order-reference" name="reference_no"
                        value="{{ $salesOrder['transaction_code'] }}" required />
                </div>
            </div>

            <!-- Section 2: Customer Information -->
            <div class="section-header mt-12">
                <h6>Informasi Customer</h6>
                <hr class="mx-n6">
            </div>

            <!-- Row 1: Customer Selection -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Customer <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" name="customer_id" id="customer_id" data-allow-clear="false"
                        required>
                        <option value="">Pilih Customer</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}"
                                {{ $customer->id == $salesOrder['f_customer_id'] ? 'selected' : '' }}>
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Row 2: Email and Address -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="text" class="form-control" id="customer_email"
                        value="{{ $salesOrder['customer_email'] ?? '' }}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Alamat</label>
                    <textarea class="form-control" id="customer_address" rows="3" disabled>{{ $salesOrder['customer_address'] ?? '' }}</textarea>
                </div>
            </div>

            <!-- Row 3: Customer Reference -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Nomor Referensi Customer</label>
                    <input type="text" class="form-control" name="customer_reference_no" id="customer_reference_no"
                        value="{{ $salesOrder['customer_reference_no'] ?? '' }}" />
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
                    <select class="global-select2 form-select" name="payment_terms" id="payment_term_id"
                        data-allow-clear="false" required>
                        <option value="">Pilih Syarat Pembayaran</option>
                        @foreach ($paymentTerms as $term)
                            <option value="{{ $term->id }}" data-type="{{ strtoupper($term->type) }}"
                                data-days="{{ $term->days }}"
                                {{ $term->id == $salesOrder['f_payment_term_id'] ? 'selected' : '' }}>
                                {{ $term->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Row 2: Jumlah Hari, Tanggal Jatuh Tempo (conditional) -->
            <div class="row g-3 mb-4" id="payment-days-row" style="display: none;">
                <div class="col-md-6">
                    <label class="form-label">Jumlah Hari <span style="color: red;">*</span></label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="payment_term_days" name="payment_term_day_count"
                            placeholder="0" min="0" max="365"
                            value="{{ $salesOrder['payment_term_day_count'] ?? '' }}" disabled />
                        <span class="input-group-text">Hari</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Jatuh Tempo</label>
                    <input type="date" class="form-control" name="due_date" id="due_date"
                        value="{{ $salesOrder['due_date'] ?? '' }}" disabled />
                </div>
            </div>

            <!-- Row 2: Sales Person and Warehouse -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Sales Person</label>
                    <select class="global-select2 form-select" name="sales_person_id" id="sales_person_id"
                        data-allow-clear="false">
                        <option value="">Pilih Sales Person</option>
                        @foreach ($salesPersons as $person)
                            <option value="{{ $person->id }}"
                                {{ $person->id == $salesOrder['customer_sales_person_id'] ? 'selected' : '' }}>
                                {{ $person->employee_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Gudang <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" name="warehouse_id" id="warehouse_id"
                        data-allow-clear="false" required>
                        <option value="">Pilih Gudang</option>
                        @foreach ($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}"
                                {{ $warehouse->id == $salesOrder['f_warehouse_id'] ? 'selected' : '' }}>
                                {{ $warehouse->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Row 3: Additional Notes -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Keterangan</label>
                    <textarea class="form-control" name="description" id="description" rows="4" maxlength="1000">{{ $salesOrder['description'] ?? '' }}</textarea>
                </div>
            </div>

            <!-- Section 4: Product Information -->
            <div class="section-header mt-12">
                <h6>Informasi Produk</h6>
            </div>


            <!-- Product Table -->
            <div class="mb-4 mx-n5">
                <div class="form-repeater-product">
                    <table class="table table-bordered" id="product-table">
                        <thead>
                            <tr>
                                <th style="width: 8%;">Is Bonus</th>
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
                            @if (isset($salesOrder['product_lines']) && count($salesOrder['product_lines']) > 0)
                                @foreach ($salesOrder['product_lines'] as $index => $line)
                                    <tr class="product-row existing-entry">
                                        <input type="hidden" name="products[{{ $index }}][id]"
                                            value="{{ $line['id'] }}" class="product-id" />
                                        <td>
                                            <div class="form-check d-flex justify-content-center">
                                                <input class="form-check-input discount-checkbox" type="checkbox"
                                                    name="products[0][is_bonus]" value="1"
                                                    @if ($line['is_bonus'] == '1') checked @endif>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="position-relative">
                                                <select class="select2Product form-select product-select"
                                                    name="products[0][product_id]"
                                                    data-selected-product="{{ $line['f_product_id'] }}"
                                                    data-allow-clear="false">
                                                    <option value="">Pilih Produk</option>
                                                </select>
                                            </div>
                                        </td>
                                        <td><input type="text" class="form-control numeric-input quantity-input"
                                                name="products[0][quantity]" min="1" step="1"
                                                value="{{ $line['quantity'] }}" /></td>
                                        <td><input type="text" class="form-control unit-display"
                                                name="products[0][unit]" value="{{ $line['unit_name'] }}" disabled />
                                        </td>
                                        <td>
                                            <input type="text"
                                                class="form-control currency-input unit-price unit-price-input"
                                                name="products[0][unit_price]" value="{{ $line['unit_price'] }}"
                                                @if ($line['is_bonus'] == '1') disabled @endif />
                                        </td>
                                        <td><input type="text" class="form-control currency-input"
                                                name="products[0][total]" value="{{ $line['line_total'] }}" disabled />
                                        </td>
                                        <td>
                                            <button class="btn btn-label-danger btn-sm" type="button"
                                                onclick="removeProductRow(this)">
                                                <i class="icon-base ti tabler-x me-1"></i>
                                                <span class="align-middle">Hapus</span>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            <tr data-repeater-item class="product-row" style="display:none">
                                <td>
                                    <div class="form-check d-flex justify-content-center">
                                        <input class="form-check-input discount-checkbox" type="checkbox"
                                            name="products[0][is_bonus]" value="1">
                                    </div>
                                </td>
                                <td>
                                    <div class="position-relative">
                                        <select class="select2Product form-select product-select"
                                            name="products[0][product_id]" data-allow-clear="true">
                                            <option value="">Pilih Produk</option>
                                        </select>
                                    </div>
                                </td>

                                <td><input type="number" class="form-control quantity-input"
                                        name="products[0][quantity]" min="1" step="1" value="1" />
                                </td>
                                <td><input type="text" class="form-control unit-display" name="products[0][unit]"
                                        disabled /></td>
                                <td>
                                    <input type="text" class="form-control currency-input unit-price unit-price-input"
                                        name="products[0][unit_price]" />
                                </td>
                                <td><input type="text" class="form-control currency-input" name="products[0][total]"
                                        disabled /></td>
                                <td>
                                    <button class="btn btn-label-danger btn-sm" type="button"
                                        onclick="removeProductRow(this)">
                                        <i class="icon-base ti tabler-x me-1"></i>
                                        <span class="align-middle">Hapus</span>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="mb-0">
                        <button class="btn btn-primary" data-repeater-create type="button" id="add-product-row">
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

            <div class="row g-3 mb-4">
                <!-- Left Column - Additional Information -->
                <div class="col-md-6">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Tag</label>
                            <select class="global-select2 form-select" name="tags[]" id="tags" multiple
                                data-allow-clear="false">
                                @foreach ($tags as $tag)
                                    <option value="{{ $tag->id }}"
                                        {{ in_array($tag->id, $salesOrder['selected_tags'] ?? []) ? 'selected' : '' }}>
                                        {{ $tag->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Memo Internal</label>
                            <textarea class="form-control" name="internal_memo" id="internal_memo" rows="4" maxlength="500">{{ $salesOrder['internal_memo'] ?? '' }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Lampiran</label>
                            <input type="file" class="form-control" name="attachment" id="attachment"
                                accept=".xlsx,.xls,.docx,.doc,.pdf,.png,.jpg,.jpeg,.zip" />
                            <div class="form-text">File dapat berupa Excel, Word, PDF, PNG, atau Zip (Max: 10MB)</div>
                            @if ($salesOrder['attachment'])
                                <div class="mt-2">
                                    <small class="text-muted">File saat ini: </small>
                                    <a href="{{ Storage::url($salesOrder['attachment']) }}" target="_blank"
                                        class="text-primary">
                                        <i class="icon-base ti tabler-download me-1"></i>Download File
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Column - Financial Summary -->
                <div class="col-md-6">
                    <div class="card border">
                        <div class="card-body">
                            <h6 class="card-title">Ringkasan Keuangan</h6>
                            <div class="row g-2">
                                {{-- <div class="col-6"><strong>Subtotal:</strong></div>
                                <div class="col-6 text-end" id="display-subtotal">Rp {{ number_format($salesOrder['subtotal'] ?? 0, 0, ',', '.') }}</div>
                                
                                <div class="col-6"><strong>Total Pajak:</strong></div>
                                <div class="col-6 text-end" id="display-total-tax">Rp {{ number_format($salesOrder['total_tax'] ?? 0, 0, ',', '.') }}</div>
                                
                                <div class="col-6"><strong>Total Sebelum Diskon:</strong></div>
                                <div class="col-6 text-end" id="display-total-before-discount">Rp {{ number_format($salesOrder['total_price_before_discount'] ?? 0, 0, ',', '.') }}</div>
                                
                                <div class="col-6">
                                    <label for="discount" class="form-label">Diskon:</label>
                                    <input type="number" class="form-control form-control-sm" name="discount" 
                                           id="discount" value="{{ $salesOrder['discount'] ?? 0 }}" min="0" />
                                </div>
                                <div class="col-6 text-end pt-4" id="display-discount">Rp {{ number_format($salesOrder['discount'] ?? 0, 0, ',', '.') }}</div>
                                
                                <div class="col-12"><hr></div>
                                 --}}
                                <div class="col-6">
                                    <h5><strong>Total Akhir:</strong></h5>
                                </div>
                                <div class="col-6 text-end">
                                    <h5 id="display-grand-total">Rp
                                        {{ number_format($salesOrder['grand_price'] ?? 0, 0, ',', '.') }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hidden inputs for calculations -->
            {{-- <input type="hidden" name="subtotal" id="subtotal" value="{{ $salesOrder['subtotal'] ?? 0 }}">
            <input type="hidden" name="total_tax" id="total_tax" value="{{ $salesOrder['total_tax'] ?? 0 }}">
            <input type="hidden" name="total_price_before_discount" id="total_price_before_discount"
                value="{{ $salesOrder['total_price_before_discount'] ?? 0 }}"> --}}
            <input type="hidden" name="grand_price" id="grand_price" value="{{ $salesOrder['grand_price'] ?? 0 }}">

            <!-- Action Buttons -->
            <div class="row">
                <div class="col-12">
                    <div class="d-flex gap-3 justify-content-end">
                        <button type="button" class="btn btn-label-danger me-3" onclick="window.history.back()">
                            <i class="icon-base ti tabler-x me-2"></i>Batalkan
                        </button>
                        <button type="submit" class="btn btn-success" id="submit-btn">
                            <i class="icon-base ti tabler-check me-2"></i>Simpan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@stop

@section('js_script')
    <script>
        $(function() {
            let productRowIndex = 0;

            $('.currency-input').each(function() {
                const val = $(this).val();
                $(this).val(convertToIndonesianFormat(val));
            });
            $('.numeric-input').each(function() {
                const val = $(this).val();
                $(this).val(convertToIndonesianFormat(val));
            });
            initCleaveCurrency(document);

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
            // Load existing product lines
            loadProductsForRow();

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
                        // checkProductTableEnabled()
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

            // Initialize payment terms behavior using global handler
            PaymentTermsHandler.init({
                paymentTermsSelector: '#payment_term_id',
                daysRowSelector: '#payment-days-row',
                daysFieldSelector: '#payment_term_days',
                dueDateFieldSelector: '#due_date',
                transactionDateSelector: '#sales-order-date'
            });

            // Customer selection change (disabled for edit)
            $('#customer_id').on('change', function() {
                const customerId = $(this).val();
                if (customerId) {
                    loadCustomerInfo(customerId);
                } else {
                    clearCustomerInfo();
                }
            });


            // Form submission
            $('#sales-order-form').on('submit', function(e) {
                // e.preventDefault();

                // if (!validateForm()) {
                //     return false;
                // }

                // const formData = new FormData(this);
                // const productLines = collectProductLines();

                // if (productLines.length === 0) {
                //     alert('Minimal harus ada satu produk yang ditambahkan.');
                //     return false;
                // }

                // formData.append('product_lines', JSON.stringify(productLines));

                // $.ajax({
                //     url: '{{ route('main_transaction.sales_order.update', $salesOrder['id']) }}',
                //     method: 'POST',
                //     data: formData,
                //     processData: false,
                //     contentType: false,
                //     success: function(response) {
                //         if (response.success) {
                //             window.location.href =
                //                 '{{ route('main_transaction.sales_order.detail', $salesOrder['id']) }}';
                //         } else {
                //             alert('Terjadi kesalahan: ' + response.message);
                //         }
                //     },
                //     error: function(xhr) {
                //         let errorMsg = 'Terjadi kesalahan saat menyimpan data.';
                //         if (xhr.responseJSON && xhr.responseJSON.message) {
                //             errorMsg = xhr.responseJSON.message;
                //         } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                //             errorMsg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                //         }
                //         alert(errorMsg);
                //     }
                // });
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

                // formData.append('products', productLines);
                productLines.forEach(function(line, idx) {
                    formData.append(`products[${idx}][product_id]`, line.product_id);
                    formData.append(`products[${idx}][quantity]`, line.quantity);
                    formData.append(`products[${idx}][unit_price]`, line.unit_price);
                    formData.append(`products[${idx}][total]`, line.line_total);
                    formData.append(`products[${idx}][is_bonus]`, line.is_bonus);
                });

                Swal.fire({
                    title: 'Ubah Sales Order?',
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

                        console.log('Submitting form with data:', formData);
                        $.ajax({
                            url: '{{ route('main_transaction.sales_order.update') }}',
                            method: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Sales Order berhasil diubah.',
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

            // Clear customer information
            function clearCustomerInfo() {
                $('#customer_email').val('');
                $('#customer_address').val('');
                $('#sales_person_name').val('');
            }

            // Load products for a specific row (new products)
            function loadProductsForRow() {
                const customerId = $('#customer_id').val();

                if (!customerId) return;
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

                                const selectedProductId = productSelect.data(
                                    'selected-product');
                                if (selectedProductId) {
                                    productSelect.val(selectedProductId).trigger('change');
                                }
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

                    // If Cleave is already initialized, use setRawValue
                    if ($(unitPriceInput).val() == "Rp ") {
                        if (unitPriceInput && unitPriceInput.cleave) {
                            unitPriceInput.cleave.setRawValue(perPrice);
                        }
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
                $('#display-grand-total').text(formatCurrency(grandTotal));
            }


            // Collect product lines for form submission
            function collectProductLines() {
                const productLines = [];

                $('#products-tbody tr').each(function() {
                    const productRowId = $(this).find('.product-id').val();
                    const productId = $(this).find('.product-select').val();
                    const quantity = parseCurrency($(this).find('.quantity-input').val()) || 0;
                    const unitPrice = parseCurrency($(this).find('.unit-price').val()) || 0;
                    const lineTotal = parseCurrency($(this).find('input[name*="[total]"]').val()) || 0;
                    const isBonus = $(this).find('.discount-checkbox').is(':checked') ? 1 : 0;
                    if (productId && quantity > 0 && unitPrice >= 0) {
                        productLines.push({
                            id: productRowId || null,
                            product_id: productId,
                            quantity: quantity,
                            unit_price: unitPrice,
                            line_total: lineTotal,
                            is_bonus: isBonus
                        });
                    }
                });

                return productLines;
            }

            // Validate form
            function validateForm() {
                const customerId = $('#customer_id').val();
                const warehouseId = $('#warehouse_id').val();
                const paymentTermId = $('#payment_term_id').val();
                const transactionDate = $('#sales-order-date').val();
                const productLines = collectProductLines();

                console.log(productLines)
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
