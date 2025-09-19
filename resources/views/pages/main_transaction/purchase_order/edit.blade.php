@extends('layouts.default')
@section('page_title')
    Edit Purchase Order - {{ $purchaseOrder['reference_no'] }}
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Edit Purchase Order - {{ $purchaseOrder['reference_no'] }}</h4>
        <form class="card-body" id="purchase-order-form" autocomplete="off">
            <input type="hidden" id="purchase-order-id" value="{{ $purchaseOrder['id'] }}">
            @csrf
            @method('POST')

            <!-- Informasi Dasar Transaksi Section ---->
            <div class="section-header mt-12">
                <h6>Informasi Dasar Transaksi</h6>
                <hr class="mx-n6">
            </div>

            <!-- Row 1: Tanggal Transaksi, Nomor Transaksi ---->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tanggal Transaksi <span style="color: red;">*</span></label>
                    <input type="date" class="form-control global-single-date-picker" id="purchase-order-date"
                        name="date" value="{{ $purchaseOrder['date'] }}" placeholder="YYYY-MM-DD" required />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nomor Transaksi <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="purchase-order-reference" name="reference_no"
                        value="{{ $purchaseOrder['reference_no'] }}" required />
                </div>
            </div>

            <!-- Informasi Supplier Section ---->
            <div class="section-header mt-12">
                <h6>Informasi Supplier</h6>
                <hr class="mx-n6">
            </div>

            <!-- Row 1: Supplier, Email ---->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Supplier <span style="color: red;">*</span></label>
                    <div class="row g-2">
                        <div class="col">
                            <select class="global-select2 form-select" id="purchase-order-supplier" name="supplier_id"
                                data-allow-clear="false" required disabled>
                                <option value="">Pilih Supplier</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" @if ($purchaseOrder['supplier_id'] == $supplier->id) selected @endif>
                                        {{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($canAddSuppliers)
                            <div class="col-auto">
                                <a href="{{ route('master_data.contact.add') }}" target="_blank"
                                    class="btn btn-outline-primary d-flex align-items-center" title="Tambah Supplier Baru">
                                    <i class="icon-base ti tabler-plus"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" id="purchase-order-email" disabled name="email"
                        value="{{ $purchaseOrder['email'] ?? '' }}" placeholder="Email akan otomatis terisi" />
                </div>
            </div>

            <!-- Row 2: Alamat, Nomor Referensi Supplier ---->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Alamat</label>
                    <textarea class="form-control" id="purchase-order-address" name="address" disabled rows="2"
                        placeholder="Alamat akan otomatis terisi">{{ $purchaseOrder['address'] ?? '' }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nomor Referensi Supplier</label>
                    <input type="text" class="form-control" id="purchase-order-supplier-ref" name="supplier_reference"
                        value="{{ $purchaseOrder['supplier_reference'] ?? '' }}" placeholder="Masukkan nomor referensi" />
                </div>
            </div>

            <!-- Detail Pembelian Section ---->
            <div class="section-header mt-12">
                <h6>Detail Pembelian</h6>
                <hr class="mx-n6">
            </div>

            <!-- Row 1: Syarat Pembayaran ---->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Syarat Pembayaran</label>
                    <div class="row g-2">
                        <div class="col">
                            <select class="global-select2 form-select" id="purchase-order-payment-terms"
                                name="payment_terms" data-allow-clear="false">
                                <option value="">Pilih Payment Terms</option>
                                @foreach ($paymentTerms as $term)
                                    <option value="{{ $term->id }}" data-days="{{ $term->days }}"
                                        data-type="{{ $term->type }}" @if ($purchaseOrder['payment_term_id'] == $term->id) selected @endif>
                                        {{ $term->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($canAddPaymentTerms)
                            <div class="col-auto">
                                <a href="{{ route('master_data.transaction_number.add') }}" target="_blank"
                                    class="btn btn-outline-primary d-flex align-items-center"
                                    title="Tambah Syarat Pembayaran Baru">
                                    <i class="icon-base ti tabler-plus"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Buyer <span style="color: red;">*</span></label>
                    <div class="row g-2">
                        <div class="col">
                            <select class="global-select2 form-select" id="purchase-order-customer" name="customer_id"
                                data-allow-clear="false">
                                <option value="">Pilih Customer</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}" @if ($purchaseOrder['customer_id'] == $customer->id) selected @endif>
                                        {{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 2: Jumlah Hari, Tanggal Jatuh Tempo (conditional) ---->
            <div class="row g-3 mb-4" id="payment-days-row" style="display: none;">
                <div class="col-md-6">
                    <label class="form-label">Jumlah Hari <span style="color: red;">*</span></label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="purchase-order-days" name="days"
                            value="{{ $purchaseOrder['days'] ?? '' }}" min="0" max="365" disabled>
                        <span class="input-group-text">Hari</span>
                    </div>
                    <small class="form-text text-muted">Jumlah hari untuk pembayaran (0 untuk immediate/COD)</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Jatuh Tempo</label>
                    <input type="date" class="form-control global-single-date-picker" id="purchase-order-due-date"
                        name="due_date" value="{{ $purchaseOrder['due_date'] }}" placeholder="YYYY-MM-DD" disabled />
                </div>
            </div>

            <!-- Row 3: Gudang, Keterangan ---->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Gudang <span style="color: red;">*</span></label>
                    <div class="row g-2">
                        <div class="col">
                            <select class="global-select2 form-select" id="purchase-order-warehouse" name="warehouse_id"
                                data-allow-clear="false" required>
                                <option value="">Pilih Gudang</option>
                                @foreach ($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}"
                                        @if ($purchaseOrder['warehouse_id'] == $warehouse->id) selected @endif>{{ $warehouse->code }} -
                                        {{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($canAddStorages)
                            <div class="col-auto">
                                <a href="{{ route('master_data.warehouse.add') }}" target="_blank"
                                    class="btn btn-outline-primary d-flex align-items-center" title="Tambah Gudang Baru">
                                    <i class="icon-base ti tabler-plus"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Keterangan</label>
                    <textarea class="form-control" id="purchase-order-description" name="description" rows="3"
                        placeholder="Keterangan transaksi...">{{ $purchaseOrder['description'] }}</textarea>
                </div>
            </div>

            {{-- <!-- Product Section ---->
            <div class="section-header mt-12 d-flex justify-content-between align-items-center">
                <h6>Informasi Pembelian</h6>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="include-tax" name="include_tax"
                        {{ $purchaseOrder['include_tax'] ?? false ? 'checked' : '' }}>
                    <label class="form-check-label" for="include-tax">Harga Termasuk Pajak</label>
                </div>
            </div> --}}

            <!-- Disabled State Message ---->
            <div id="product-disabled-message" class="alert alert-info" style="display: none;">
                <i class="icon-base ti tabler-info-circle me-2"></i>
                Silakan pilih <strong>Supplier</strong> dan <strong>Gudang</strong> terlebih dahulu untuk dapat menambah
                produk.
            </div>

            <!-- Product Table ---->
            <div class="mb-4 mx-n5">
                <div class="form-repeater-product">
                    <table class="table table-bordered" id="product-table">
                        <thead>
                            <tr>
                                <th style="width: 25%;">Produk</th>
                                <th style="width: 12%;">Kuantitas</th>
                                <th style="width: 10%;">Satuan</th>
                                <th style="width: 18%;">Harga Satuan</th>
                                <th style="width: 15%;">Jumlah</th>
                                <th style="width: 5%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="products-tbody" data-repeater-list>
                            @if (isset($purchaseOrder['product_lines']) && count($purchaseOrder['product_lines']) > 0)
                                @foreach ($purchaseOrder['product_lines'] as $index => $line)
                                    <tr class="product-row existing-entry">
                                        <input type="hidden" name="products[{{ $index }}][id]"
                                            value="{{ $line['id'] }}" class="product-id" />
                                        <td>
                                            <div class="position-relative">
                                                <select class="select2Product form-select product-select"
                                                    name="products[{{ $index }}][product_id]"
                                                    data-allow-clear="false"
                                                    data-selected-value="{{ $line['product_id'] }}">
                                                    <option value="">Pilih Produk</option>
                                                </select>
                                            </div>
                                        </td>
                                        <td><input type="number" class="form-control"
                                                name="products[{{ $index }}][quantity]"
                                                value="{{ $line['quantity'] }}" min="1" step="1" /></td>
                                        <td><input type="text" class="form-control"
                                                name="products[{{ $index }}][unit]" value="{{ $line['unit'] }}"
                                                disabled /></td>
                                        <td><input type="text" class="form-control currency-input"
                                                name="products[{{ $index }}][unit_price]"
                                                value="{{ $line['unit_price'] }}"
                                                title="Harga tidak dapat diubah setelah PO dibuat" /></td>
                                        {{-- <td>
                                            <div class="position-relative">
                                                <select class="form-select tax-select select2Tax"
                                                    name="products[{{ $index }}][tax_id]"
                                                    data-allow-clear="false">
                                                    <option value="">Tanpa Pajak</option>
                                                    @foreach ($taxOptions as $item)
                                                        <option value="{{ $item->id }}"
                                                            {{ $line['tax_id'] === $item->id ? 'selected' : '' }}>
                                                            {{ $item->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </td> --}}
                                        <td><input type="text" class="form-control"
                                                name="products[{{ $index }}][total]" disabled />
                                        </td>
                                        <td>
                                            <button class="btn btn-label-danger btn-sm" type="button"
                                                onclick="removeProductRow(this)" disabled>
                                                <i class="icon-base ti tabler-x me-1"></i>
                                                <span class="align-middle">Hapus</span>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            <tr data-repeater-item class="product-row" style="display:none">
                                <td>
                                    <div class="position-relative">
                                        <select class="select2Product form-select product-select"
                                            name="products[0][product_id]" data-allow-clear="false" disabled>
                                            <option value="">Pilih Produk</option>
                                        </select>
                                    </div>
                                </td>
                                <td><input type="number" class="form-control" name="products[0][quantity]"
                                        min="1" step="1" disabled /></td>
                                <td><input type="text" class="form-control" name="products[0][unit]" disabled />
                                </td>
                                <td>
                                    <input type="text" class="form-control currency-input"
                                        name="products[0][unit_price]" title="Harga tidak dapat diubah" />
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
                        <button class="btn btn-primary" data-repeater-create type="button">
                            <i class="icon-base ti tabler-plus me-1"></i>
                            <span class="align-middle">Tambah Baris</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Add Row Button ---->
            {{-- <div class="mb-3">
                <button type="button" class="btn btn-primary" id="add-product-row">
                    <i class="icon-base ti tabler-plus me-1"></i>
                    <span class="align-middle">Tambah Baris</span>
                </button>
            </div> --}}

            <!-- Informasi Tambahan & Ringkasan Section ---->
            <div class="section-header mt-12">
                <h6>Informasi Tambahan & Ringkasan</h6>
                <hr class="mx-n6">
            </div>

            <!-- Split Layout: Tag, Memo, Attachment & Financial Summary ---->
            <div class="row">
                <!-- Left Side: Tag, Memo and Attachment ---->
                <div class="col-md-6 mb-4">
                    <div class="mb-3">
                        <label class="form-label">Tag</label>
                        <div class="row g-2">
                            <div class="col">
                                <select class="global-select2 form-select" id="purchase-order-tags" name="tags[]"
                                    multiple data-allow-clear="false">
                                    @foreach ($availableTags as $tag)
                                        <option value="{{ $tag->id }}" data-preview="{{ $tag->name }}"
                                            @if (isset($purchaseOrder['f_tag_codes']) &&
                                                    is_array($purchaseOrder['f_tag_codes']) &&
                                                    in_array($tag->id, $purchaseOrder['f_tag_codes'])) selected @endif>
                                            {{ $tag->name }}
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
                        <small class="form-text text-muted">Pilih opsi atau tambah baru jika diperlukan</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Memo (Internal)</label>
                        <textarea class="form-control" id="purchase-order-memo" name="memo" rows="4"
                            placeholder="Catatan internal...">{{ $purchaseOrder['memo'] }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lampiran</label>
                        @if (isset($purchaseOrder['attachment']) && $purchaseOrder['attachment'])
                            <div class="mb-2">
                                <small class="text-muted">File saat ini:
                                    <a
                                        href="{{ $purchaseOrder['attachment']['name'] ?? 'File terlampir' }}">{{ $purchaseOrder['attachment']['name'] ?? 'File terlampir' }}</a></small>
                            </div>
                        @endif
                        <input type="file" class="form-control" id="purchase-order-attachment" name="attachment"
                            accept=".xlsx,.xls,.doc,.docx,.pdf,.png,.jpg,.jpeg,.zip" />
                        <small class="form-text text-muted">File dapat berupa Excel, Word, PDF, PNG, atau Zip (Max:
                            10MB)</small>
                    </div>
                </div>

                <!-- Right Side: Financial Summary Section ---->
                <div class="col-md-6">
                    <div class="summary-section ps-4">
                        {{-- <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Subtotal</label>
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control" id="subtotal-amount" dir="rtl"
                                    value="{{ $purchaseOrder['subtotal'] }}" disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Total Pajak</label>
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control" id="tax-amount" dir="rtl"
                                    value="{{ $purchaseOrder['tax_amount'] }}" disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Total Sebelum Diskon</label>
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control" id="total-before-discount" dir="rtl"
                                    value="{{ number_format($purchaseOrder['total_before_discount']) }}" disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Diskon</label>
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control currency-input" id="discount-amount"
                                    name="discount_amount" dir="rtl"
                                    value="{{ $purchaseOrder['discount_amount'] }}">
                            </div>
                        </div> --}}
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label"><strong>Total Akhir</strong></label>
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control fw-bold" id="final-total" dir="rtl"
                                    value="{{ $purchaseOrder['total'] }}" disabled>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hidden fields for calculations ---->
            {{-- <input type="hidden" id="purchase-order-subtotal" name="subtotal"
                value="{{ $purchaseOrder['subtotal'] ?? 0 }}" />
            <input type="hidden" id="purchase-order-tax-amount" name="tax_amount"
                value="{{ $purchaseOrder['tax_amount'] ?? 0 }}" /> --}}
            <input type="hidden" id="purchase-order-total" name="total" value="{{ $purchaseOrder['total'] }}" />

            <!-- Action Buttons ---->
            <div class="pt-6 text-end">
                <a href="{{ route('main_transaction.purchase_order.detail', $purchaseOrder['id']) }}"
                    class="btn btn-label-danger me-3">Batalkan</a>
                @if ($canWrite)
                    <button type="submit" class="btn btn-success" id="submit-btn">Simpan</button>
                @endif
            </div>
        </form>
    </div>
@stop


@section('js_script')
    <script>
        $(function() {
            $('.currency-input').each(function() {
                const val = $(this).val();
                $(this).val(convertToIndonesianFormat(val));
            });
            initCleaveCurrency(document);

            let productTableEnabled = true; // Enable by default for edit form

            // Initialize payment terms handling on page load
            handlePaymentTermsChange(true);

            // Check initial state for product table
            checkProductTableState();

            // Reset product table when warehouse changes
            $('#purchase-order-warehouse').on('change', function() {
                resetProductTable();
                checkProductTableState();
            });

            // Handle payment terms change
            $('#purchase-order-payment-terms').on('change', function() {
                handlePaymentTermsChange(false);
                updateDueDate();
            });

            // Handle transaction date change
            $('#purchase-order-date').on('change', updateDueDate);

            // Update due date when days field changes (for CUSTOM type)
            $('#purchase-order-days').on('input', function() {
                updateDueDate();
            });

            function checkProductTableState() {
                const hasSupplier = $('#purchase-order-supplier').val();
                const hasStorage = $('#purchase-order-warehouse').val();

                productTableEnabled = hasSupplier && hasStorage;

                if (productTableEnabled) {
                    // Enable product table
                    $('#product-disabled-message').hide();
                    $('#product-table tbody tr').each(function() {
                        // Enable editable fields (excluding disabled calculation fields)
                        $(this).find('select, button').prop('disabled', false);
                        // Only enable quantity, keep prices readonly
                        $(this).find('input[name*="[quantity]"]').prop('disabled', false);
                    });
                    $('#add-product-row').prop('disabled', false);

                    // Load products for the selected supplier and warehouse
                    loadProducts();
                } else {
                    // Show disabled message and disable controls
                    $('#product-disabled-message').show();
                    $('#product-table tbody tr').each(function() {
                        // Disable all inputs, selects, and buttons
                        $(this).find('input, select, button').prop('disabled', true);
                    });
                    $('#add-product-row').prop('disabled', true);
                }
            }

            function resetProductTable() {
                // Keep existing rows but reset their content
                $('#product-table tbody tr').each(function() {
                    const row = $(this);
                    row.find('.product-select').val('').trigger('change');
                    row.find('input[name*="[quantity]"]').val('');
                    row.find('input[name*="[unit]"]').val('');
                    row.find('input[name*="[unit_price]"]').val('');
                    // row.find('.tax-select').val('').trigger('change');
                    row.find('input[name*="[total]"]').val('');
                });
                calculateTotals();
            }

            function loadProducts() {
                const supplierId = $('#purchase-order-supplier').val();
                const warehouseId = $('#purchase-order-warehouse').val();

                if (!supplierId || !warehouseId) return;

                // Mock products - in real implementation, fetch via AJAX
                const products = @json($products);

                // Update all product selects
                $('#product-table tbody tr').each(function() {
                    const productSelect = $(this).find('.product-select');

                    productSelect.empty().append('<option value="">Pilih Produk</option>');
                    products.forEach(product => {
                        productSelect.append(
                            `<option value="${product.id}" data-unit="${product.unit_name}">${product.code} - ${product.name}</option>`
                        );
                    });

                    const selectedValue = productSelect.data('selected-value');
                    if (selectedValue !== undefined && selectedValue !== null && selectedValue !== '') {
                        productSelect.val(selectedValue).trigger('change');
                    }
                });
            }

            function handlePaymentTermsChange(isOnLoad) {
                const selectedOption = $('#purchase-order-payment-terms option:selected');
                const paymentTerms = $('#purchase-order-payment-terms').val();
                const type = selectedOption.data('type');
                const days = selectedOption.data('days');
                const daysRow = $('#payment-days-row');
                const daysField = $('#purchase-order-days');
                const dueDateField = $('#purchase-order-due-date');

                const current_day = $('#purchase-order-days').val();
                if (type === 'NET' || type === 'CUSTOM') {
                    // Show the row for NET and CUSTOM types
                    daysRow.show();

                    if (type === 'NET') {
                        // For NET type: auto-fill days and disable field
                        daysField.val(days).prop('disabled', true);
                        dueDateField.prop('disabled', true);
                    } else if (type === 'CUSTOM') {
                        // For CUSTOM type: enable days field only, due date is always auto-calculated
                        daysField.val(days || '').prop('disabled', false);
                        dueDateField.prop('disabled', true);
                    }
                } else {
                    // Hide the row for other types (COD, Immediate, etc.)
                    daysRow.hide();
                    daysField.val('').prop('disabled', true);
                    dueDateField.prop('disabled', true);
                }

                if (isOnLoad === true) {
                    daysField.val(current_day)
                }
            }

            function updateDueDate() {
                const transactionDate = $('#purchase-order-date').val();
                const selectedOption = $('#purchase-order-payment-terms option:selected');
                const type = selectedOption.data('type');
                const dueDateField = $('#purchase-order-due-date');
                const daysField = $('#purchase-order-days');

                // Auto-calculate for NET type, use manual input for CUSTOM type
                if (transactionDate && type) {
                    const date = new Date(transactionDate);
                    let days = 0;

                    if (type === 'NET') {
                        days = parseInt(daysField.val()) || 0;
                    } else if (type === 'CUSTOM') {
                        days = parseInt(daysField.val()) || 0;
                    }

                    if (type === 'NET' || (type === 'CUSTOM' && days > 0)) {
                        date.setDate(date.getDate() + days);
                        const dueDate = date.toISOString().split('T')[0];
                        dueDateField.val(dueDate);
                    }
                }
            }

            // Product selection handler
            $(document).on('change', '.product-select', function() {
                const selectedOption = $(this).find('option:selected');
                const unit = selectedOption.data('unit');
                const row = $(this).closest('tr');

                if (unit) {
                    row.find('input[name*="[unit]"]').val(unit);
                } else {
                    row.find('input[name*="[unit]"]').val('');
                }

                calculateTotals();
            });

            window.removeProductRow = function(button) {
                if ($('#product-table tbody tr').length > 1) {
                    $(button).closest('tr').remove();
                    calculateTotals();
                }
            };

            // Product calculation function
            function calculateTotals() {
                let subtotal = 0;
                // let taxAmount = 0;

                // Calculate from product table rows
                $('#product-table tbody tr').each(function() {
                    const quantity = parseFloat($(this).find('input[name*="[quantity]"]').val()) || 0;
                    const unitPriceInput = $(this).find('input[name*="[unit_price]"]');
                    const unitPrice = parseCurrency(unitPriceInput.val()) || 0;
                    // const taxSelect = $(this).find('select[name*="[tax_id]"]').val();
                    // const includesTax = $('#include-tax').is(':checked');

                    if (quantity > 0 && unitPrice > 0) {
                        let rowTotal = quantity * unitPrice;
                        // let rowTax = 0;

                        // if (taxSelect && taxSelect !== '') {
                        //     if (includesTax) {
                        //         // Price includes tax - extract tax amount
                        //         rowTax = rowTotal * 0.11 / 1.11;
                        //         rowTotal = rowTotal - rowTax;
                        //     } else {
                        //         // Price excludes tax - add tax amount
                        //         rowTax = rowTotal * 0.11;
                        //     }
                        // }

                        subtotal += rowTotal;
                        // taxAmount += rowTax;

                        // Update row total display
                        // $(this).find('input[name*="[total]"]').val(formatCurrency(rowTotal + rowTax));
                        $(this).find('input[name*="[total]"]').val(formatCurrency(rowTotal));
                    } else {
                        $(this).find('input[name*="[total]"]').val('');
                    }
                });

                // const totalBeforeDiscount = subtotal + taxAmount;
                // const discount = parseCurrency($('#discount-amount').val());
                // const finalTotal = totalBeforeDiscount - discount;
                const finalTotal = subtotal;

                // Update display fields
                // $('#subtotal-amount').val(formatCurrency(subtotal));
                // $('#tax-amount').val(formatCurrency(taxAmount));
                // $('#total-before-discount').val(formatCurrency(totalBeforeDiscount));
                $('#final-total').val(formatCurrency(finalTotal));

                // Update hidden fields
                // $('#purchase-order-subtotal').val(subtotal);
                // $('#purchase-order-tax-amount').val(taxAmount);
                $('#purchase-order-total').val(finalTotal);
            }

            // Initialize calculations
            calculateTotals();

            // Bind calculation events
            $(document).on('input change',
                // 'input[name*="[quantity]"], input[name*="[unit_price]"], select[name*="[tax_id]"]',
                'input[name*="[quantity]"], input[name*="[unit_price]"]',
                function() {
                    if ($(this).attr('name').includes('[unit_price]')) {
                        formatCurrencyInput(this);
                    }
                    calculateTotals();
                });

            // Include tax checkbox handler
            // $('#include-tax').on('change', function() {
            //     calculateTotals();
            // });

            // Discount input change handler
            // $('#discount-amount').on('input', function() {
            //     formatCurrencyInput(this);
            //     calculateTotals();
            // });


            //Repeater Section
            $('select.select2Product').each(function() {
                $(this).select2({
                    placeholder: "Pilih Produk",
                    allowClear: true,
                    dropdownParent: $(this).parent(),
                    width: '100%'
                });
            });

            // $('select.select2Tax').each(function() {
            //     $(this).select2({
            //         placeholder: "Pilih Pajak",
            //         allowClear: true,
            //         dropdownParent: $(this).parent(),
            //         width: '100%'
            //     });
            // });

            $('.form-repeater-product').repeater({
                show: function() {
                    $(this).slideDown(() => {
                        checkProductTableState()
                        $(this).find('.select2-container').remove();
                        $(this).find('select.select2Product').removeClass(
                            'select2-hidden-accessible').removeAttr(
                            'data-select2-id tabindex aria-hidden');
                        $(this).find('select.select2Product').each(function() {
                            if ($.fn.select2 && $(this).data('select2')) {
                                $(this).select2('destroy');
                            }
                            $(this).select2({
                                placeholder: "Pilih Produk",
                                allowClear: true,
                                dropdownParent: $(this).parent(),
                                width: '100%'
                            });
                        });

                        // $(this).find('select.select2Tax').removeClass(
                        //     'select2-hidden-accessible').removeAttr(
                        //     'data-select2-id tabindex aria-hidden');
                        // $(this).find('select.select2Tax').each(function() {
                        //     if ($.fn.select2 && $(this).data('select2')) {
                        //         alert('Destroying existing Select2 instance');
                        //         $(this).select2('destroy');
                        //     }
                        //     $(this).select2({
                        //         placeholder: "Pilih Pajak",
                        //         allowClear: true,
                        //         dropdownParent: $(this).parent(),
                        //         width: '100%'
                        //     });
                        // });
                        initCleaveCurrency(document);
                    });
                },
                hide: function(remove) {
                    $(this).slideUp(remove);
                }
            });


            $('#purchase-order-form').on('submit', function(e) {
                e.preventDefault();

                // Validation
                const requiredFields = [
                    'purchase-order-supplier',
                    'purchase-order-email',
                    'purchase-order-address',
                    'purchase-order-date',
                    'purchase-order-warehouse',
                    'purchase-order-payment-terms',
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
                    title: 'Update Purchase Order?',
                    text: 'Pastikan data yang diisi sudah benar.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Update!',
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
                const formData = new FormData($('#purchase-order-form')[0]);
                formData.append('id', $('#purchase-order-id').val());

                formData.append('email', $('#purchase-order-email').val());
                formData.append('address', $('#purchase-order-address').val());
                formData.append('supplier_id', $('#purchase-order-supplier').val());
                // const includeTax = $('#include-tax').is(':checked') ? 1 : 0;
                // formData.append('include_tax', includeTax);
                // const totalBeforeDiscount = parseCurrency($('#total-before-discount').val());
                // formData.append('total_before_discount', totalBeforeDiscount);

                // const discountAmount = parseCurrency($('#discount-amount').val());
                // formData.append('discount_amount', discountAmount);

                const paymentTermDays = $('#purchase-order-days').val();
                formData.append('payment_term_day_count', paymentTermDays);

                const dueDate = $('#purchase-order-due-date').val();
                formData.append('payment_term_due_date', dueDate);

                // Collect product rows from data-repeater-list

                let counter = 0;
                $('.product-row.existing-entry:visible').each(function() {
                    const id = $(this).find('.product-id').val();
                    const productId = $(this).find('.product-select').val();
                    const quantity = $(this).find('input[name*="[quantity]"]').val();
                    const unit = $(this).find('input[name*="[unit]"]').val();
                    const unitPrice = parseCurrency($(this).find('input[name*="[unit_price]"]').val());
                    // const taxId = $(this).find('select[name*="[tax_id]"]').val();
                    const total = parseCurrency($(this).find('input[name*="[total]"]').val());

                    if (id && id !== '' &&
                        productId &&
                        quantity > 0 &&
                        unit !== undefined && unit !== null && unit !== '' &&
                        unitPrice > 0 &&
                        // taxId !== undefined && taxId !== null && taxId !== '' &&
                        total > 0) {
                        // Append each product field to FormData
                        formData.append(`products[${counter}][id]`, id);
                        formData.append(`products[${counter}][product_id]`, productId);
                        formData.append(`products[${counter}][quantity]`, quantity);
                        formData.append(`products[${counter}][unit]`, unit);
                        formData.append(`products[${counter}][unit_price]`, unitPrice);
                        // formData.append(`products[${counter}][tax_id]`, taxId);
                        formData.append(`products[${counter}][total]`, total);
                        counter++;
                    }
                });

                $('#products-tbody tr[data-repeater-item]').each(function(index) {
                    const productId = $(this).find('.product-select').val();
                    const quantity = $(this).find('input[name*="[quantity]"]').val();
                    const unit = $(this).find('input[name*="[unit]"]').val();
                    const unitPrice = parseCurrency($(this).find('input[name*="[unit_price]"]').val());
                    // const taxId = $(this).find('select[name*="[tax_id]"]').val();
                    const total = parseCurrency($(this).find('input[name*="[total]"]').val());

                    if (productId && productId !== '' &&
                        quantity > 0 &&
                        unit !== undefined && unit !== null && unit !== '' &&
                        unitPrice > 0 &&
                        // taxId !== undefined && taxId !== null && taxId !== '' &&
                        total > 0) {

                        formData.append(`products[${counter}][product_id]`, productId);
                        formData.append(`products[${counter}][quantity]`, quantity);
                        formData.append(`products[${counter}][unit]`, unit);
                        formData.append(`products[${counter}][unit_price]`, unitPrice);
                        // formData.append(`products[${counter}][tax_id]`, taxId);
                        formData.append(`products[${counter}][total]`, total);
                        counter++;
                    }
                });

                const attachment = $('#purchase-order-attachment')[0];
                if (attachment && attachment.files.length) {
                    formData.append('attachment', attachment.files[0]);
                }

                // AJAX submission
                $.ajax({
                    url: "{{ route('main_transaction.purchase_order.update') }}",
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Purchase Order berhasil dibuat.',
                            icon: 'success',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        }).then(() => {
                            window.location.href =
                                "{{ route('main_transaction.purchase_order.index') }}";
                        });
                    },
                    error: function(xhr) {
                        let errorMessage = 'Terjadi kesalahan saat menyimpan data.';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            title: 'Gagal!',
                            // text: errorMessage,
                            html: errorMessage,
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
