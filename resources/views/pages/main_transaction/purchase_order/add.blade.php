@extends('layouts.default')
@section('page_title')
    Buat Purchase Order
@stop

@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Buat Purchase Order</h4>
        <form class="card-body" id="purchase-order-form" autocomplete="off">
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
                    <input type="date" class="form-control global-single-date-picker" id="purchase-order-date"
                        name="date" value="{{ date('Y-m-d') }}" placeholder="YYYY-MM-DD" required />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nomor Transaksi <span style="color: red;">*</span></label>
                    <div class="row g-2">
                        <div class="col">
                            <input type="text" class="form-control" id="purchase-order-reference" name="reference_no"
                                placeholder="Auto-generated but editable" required />
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-outline-primary d-flex align-items-center"
                                id="generate-po-number-btn" title="Generate New Number">
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

            <!-- Row 1: Supplier, Email -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Supplier <span style="color: red;">*</span></label>
                    <div class="row g-2">
                        <div class="col">
                            <select class="global-select2 form-select" id="purchase-order-supplier" name="supplier_id"
                                data-allow-clear="false" required>
                                <option value="">Pilih Supplier</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
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
                    <input type="email" class="form-control" id="purchase-order-email" name="email"
                        placeholder="Email akan otomatis terisi" disabled />
                </div>
            </div>

            <!-- Row 2: Alamat, Nomor Referensi Supplier -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Alamat</label>
                    <textarea class="form-control" id="purchase-order-address" name="address" rows="2"
                        placeholder="Alamat akan otomatis terisi"></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nomor Referensi Supplier</label>
                    <input type="text" class="form-control" id="purchase-order-supplier-ref" name="supplier_reference"
                        placeholder="Masukkan nomor referensi" />
                </div>
            </div>

            <!-- Detail Pembelian Section -->
            <div class="section-header mt-12">
                <h6>Detail Pembelian</h6>
                <hr class="mx-n6">
            </div>

            <!-- Row 1: Syarat Pembayaran -->
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
                                        data-type="{{ $term->type }}">{{ $term->name }}</option>
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
            </div>

            <!-- Row 2: Jumlah Hari, Tanggal Jatuh Tempo (conditional) -->
            <div class="row g-3 mb-4" id="payment-days-row" style="display: none;">
                <div class="col-md-6">
                    <label class="form-label">Jumlah Hari <span style="color: red;">*</span></label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="purchase-order-days" name="days"
                            placeholder="30" min="0" max="365" disabled>
                        <span class="input-group-text">Hari</span>
                    </div>
                    <small class="form-text text-muted">Jumlah hari untuk pembayaran (0 untuk immediate/COD)</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Jatuh Tempo</label>
                    <input type="date" class="form-control global-single-date-picker" id="purchase-order-due-date"
                        name="due_date" placeholder="YYYY-MM-DD" disabled />
                </div>
            </div>

            <!-- Row 2: Gudang, Keterangan -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Gudang <span style="color: red;">*</span></label>
                    <div class="row g-2">
                        <div class="col">
                            <select class="global-select2 form-select" id="purchase-order-warehouse" name="warehouse_id"
                                data-allow-clear="false" required>
                                <option value="">Pilih Gudang</option>
                                @foreach ($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
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
                        placeholder="Keterangan transaksi..."></textarea>
                </div>
            </div>

            <!-- Product Section -->
            <div class="section-header mt-12 d-flex justify-content-between align-items-center">
                <h6>Informasi Pembelian</h6>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="include-tax" name="include_tax">
                    <label class="form-check-label" for="include-tax">Harga Termasuk Pajak</label>
                </div>
            </div>

            <!-- Disabled State Message -->
            <div id="product-disabled-message" class="alert alert-info">
                <i class="icon-base ti tabler-info-circle me-2"></i>
                Silakan pilih <strong>Supplier</strong> dan <strong>Gudang</strong> terlebih dahulu untuk dapat menambah
                produk.
            </div>

            <!-- Product Table -->
            <div class="mb-4 mx-n5">
                <div class="form-repeater-product">
                    <table class="table table-bordered" id="product-table">
                        <thead>
                            <tr>
                                <th style="width: 25%;">Produk</th>
                                <th style="width: 12%;">Kuantitas</th>
                                <th style="width: 10%;">Satuan</th>
                                <th style="width: 18%;">Harga Satuan</th>
                                <th style="width: 15%;">Pajak</th>
                                <th style="width: 15%;">Jumlah</th>
                                <th style="width: 5%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="products-tbody" data-repeater-list>
                            <tr data-repeater-item class="product-row">
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
                                <td><input type="text" class="form-control" name="products[0][unit]" disabled /></td>
                                <td>
                                    <input type="text" class="form-control currency-input"
                                        name="products[0][unit_price]" disabled />

                                </td>
                                <td>
                                    <div class="position-relative">
                                        <select class="select2Tax form-select" name="products[0][tax_id]"
                                            data-allow-clear="false" disabled>
                                            <option value="">Pilih Pajak</option>
                                            @foreach ($taxOptions as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </td>
                                <td><input type="text" class="form-control" name="products[0][total]" disabled /></td>
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

            <!-- Add Row Button -->
            {{-- <div class="mb-3">
                <button type="button" class="btn btn-primary" id="add-product-row" disabled>
                    <i class="icon-base ti tabler-plus me-1"></i>
                    <span class="align-middle">Tambah Baris</span>
                </button>
            </div> --}}

            <!-- Informasi Tambahan & Ringkasan Section -->
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

                                <select class="global-select2 form-select" id="purchase-order-tags" name="tags[]"
                                    multiple data-allow-clear="false">
                                    @foreach ($availableTags as $tag)
                                        <option value="{{ $tag->id }}">{{ $tag->name }}</option>
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
                            placeholder="Catatan internal..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lampiran</label>
                        <input type="file" class="form-control" id="purchase-order-attachment" name="attachment"
                            accept=".xlsx,.xls,.doc,.docx,.pdf,.png,.jpg,.jpeg,.zip" />
                        <small class="form-text text-muted">File dapat berupa Excel, Word, PDF, PNG, atau Zip (Max:
                            10MB)</small>
                    </div>
                </div>

                <!-- Right Side: Financial Summary Section -->
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

            <!-- Hidden fields for calculations -->
            <input type="hidden" id="purchase-order-subtotal" name="subtotal" value="0" />
            <input type="hidden" id="purchase-order-tax-amount" name="tax_amount" value="0" />
            <input type="hidden" id="purchase-order-total" name="total" value="0" />
            <input type="hidden" id="purchase-order-currency" name="currency" value="IDR" />

            <!-- Action Buttons -->
            <div class="pt-6 text-end">
                <a href="{{ route('main_transaction.purchase_order.index') }}"
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

            // Generate transaction number function
            function generateTransactionNumber() {
                $.get('{{ route('main_transaction.purchase_order.generate_transaction_number') }}')
                    .done(function(response) {
                        if (response.success) {
                            $('#purchase-order-reference').val(response.transaction_number);
                        }
                    })
                    .fail(function() {
                        console.error('Failed to generate transaction number');
                    });
            }

            // Generate new transaction number when button is clicked
            $('#generate-po-number-btn').on('click', function() {
                generateTransactionNumber();
            });

            // Check initial state
            checkProductTableState();

            $('select.select2Product').each(function() {
                $(this).select2({
                    placeholder: "Pilih Produk",
                    allowClear: true,
                    dropdownParent: $(this).parent(),
                    width: '100%'
                });
            });

            $('select.select2Tax').each(function() {
                $(this).select2({
                    placeholder: "Pilih Pajak",
                    allowClear: true,
                    dropdownParent: $(this).parent(),
                    width: '100%'
                });
            });

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
                                alert('Destroying existing Select2 instance');
                                $(this).select2('destroy');
                            }
                            $(this).select2({
                                placeholder: "Pilih Produk",
                                allowClear: true,
                                dropdownParent: $(this).parent(),
                                width: '100%'
                            });
                        });

                        // $(this).find('.select2-container').remove();
                        $(this).find('select.select2Tax').removeClass(
                            'select2-hidden-accessible').removeAttr(
                            'data-select2-id tabindex aria-hidden');
                        $(this).find('select.select2Tax').each(function() {
                            if ($.fn.select2 && $(this).data('select2')) {
                                alert('Destroying existing Select2 instance');
                                $(this).select2('destroy');
                            }
                            $(this).select2({
                                placeholder: "Pilih Pajak",
                                allowClear: true,
                                dropdownParent: $(this).parent(),
                                width: '100%'
                            });
                        });
                        // initCleaveCurrency(document);
                    });
                },
                hide: function(remove) {
                    $(this).slideUp(remove);
                }
            });

            // Auto-fill supplier data when supplier is selected
            $('#purchase-order-supplier').on('change', function() {
                const supplierId = $(this).val();
                if (supplierId) {
                    // Mock data - in real implementation, fetch from server
                    $('#purchase-order-email').val('supplier@example.com').prop('disabled', false);
                    $('#purchase-order-address').val('Jl. Supplier Address No. 123, Jakarta').prop(
                        'disabled', false);

                    // Reset product table when supplier changes
                    // resetProductTable();

                    // Auto-calculate due date based on payment terms
                    updateDueDate();
                } else {
                    $('#purchase-order-email').val('').prop('disabled', true);
                    $('#purchase-order-address').val('').prop('disabled', true);
                    // resetProductTable();
                }
                checkProductTableState();
            });

            // Reset product table when warehouse changes
            $('#purchase-order-warehouse').on('change', function() {
                // resetProductTable();
                checkProductTableState();
            });

            // Update due date when payment terms change
            $('#purchase-order-payment-terms').on('change', function() {
                handlePaymentTermsChange();
                updateDueDate();
            });
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
                        $(this).find('input, select, button').prop('disabled', false);
                    });
                    $('#add-product-row').prop('disabled', false);

                    // Load products for the selected supplier and warehouse
                    loadProducts();
                } else {
                    // Disable product table
                    $('#product-disabled-message').show();
                    $('#product-table tbody tr').each(function() {
                        $(this).find('input, select, button').prop('disabled', true);
                    });
                    $('#add-product-row').prop('disabled', true);
                }
            }

            // function resetProductTable() {
            //     // Reset to one empty row
            //     $('#product-table tbody').html(`
        //         <tr>
        //             <td>
        //                 <select class="form-select product-select" name="products[0][product_id]" data-allow-clear="false" disabled>
        //                     <option value="">Pilih Produk</option>
        //                 </select>
        //             </td>
        //             <td><input type="number" class="form-control" name="products[0][quantity]" min="1" step="1" disabled /></td>
        //             <td><input type="text" class="form-control" name="products[0][unit]" disabled /></td>
        //             <td><input type="text" class="form-control currency-input" name="products[0][unit_price]" placeholder="Rp 0" disabled /></td>
        //             <td>
        //                 <select class="form-select tax-select" name="products[0][tax_id]" data-allow-clear="false" disabled>
        //                     <option value="">Tanpa Pajak</option>
        //                 </select>
        //             </td>
        //             <td><input type="text" class="form-control" name="products[0][total]" disabled /></td>
        //             <td>
        //                 <button class="btn btn-label-danger btn-sm" type="button" onclick="removeProductRow(this)" disabled>
        //                     <i class="icon-base ti tabler-x me-1"></i>
        //                     <span class="align-middle">Hapus</span>
        //                 </button>
        //             </td>
        //         </tr>
        //     `);
            //     calculateTotals();
            // }

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
                            `<option value="${product.id}" data-unit="${product.unit_name}">${product.name}</option>`
                        );
                    });
                });
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

            // function initializePOTransactionNumber() {
            //     // Generate initial transaction number on page load
            //     generatePOTransactionNumber();

            //     // Handle generate button click
            //     $('#generate-po-number-btn').on('click', function() {
            //         generatePOTransactionNumber();
            //     });

            //     // Validate for duplicates when manually edited
            //     $('#purchase-order-reference').on('blur', function() {
            //         const number = $(this).val();
            //         if (number) {
            //             checkPODuplicateNumber(number);
            //         }
            //     });
            // }

            // function generatePOTransactionNumber() {
            //     $('#generate-po-number-btn').prop('disabled', true);

            //     // Mock generation - in real implementation, use AJAX
            //     const year = new Date().getFullYear();
            //     const month = String(new Date().getMonth() + 1).padStart(2, '0');
            //     const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
            //     const number = `PO-${year}${month}-${random}`;

            //     $('#purchase-order-reference').val(number);
            //     $('#generate-po-number-btn').prop('disabled', false);
            // }

            // function checkPODuplicateNumber(number) {
            //     // Mock validation - in real implementation, use AJAX
            //     console.log('Checking duplicate for:', number);
            // }

            function handlePaymentTermsChange() {
                const selectedOption = $('#purchase-order-payment-terms option:selected');
                const paymentTerms = $('#purchase-order-payment-terms').val();
                const type = selectedOption.data('type');
                const days = selectedOption.data('days');
                const daysRow = $('#payment-days-row');
                const daysField = $('#purchase-order-days');
                const dueDateField = $('#purchase-order-due-date');

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

            // Product calculation function
            function calculateTotals() {
                let subtotal = 0;
                let taxAmount = 0;

                // Calculate from product table rows
                $('#product-table tbody tr').each(function() {
                    const quantity = parseFloat($(this).find('input[name*="[quantity]"]').val()) || 0;
                    const unitPriceInput = $(this).find('input[name*="[unit_price]"]');
                    const unitPrice = parseCurrency(unitPriceInput.val()) || 0;
                    const taxSelect = $(this).find('select[name*="[tax_id]"]').val();
                    const includesTax = $('#include-tax').is(':checked');

                    if (quantity > 0 && unitPrice > 0) {
                        let rowTotal = quantity * unitPrice;
                        let rowTax = 0;

                        if (taxSelect && taxSelect !== '') {
                            if (includesTax) {
                                // Price includes tax - extract tax amount
                                rowTax = rowTotal * 0.11 / 1.11;
                                rowTotal = rowTotal - rowTax;

                            } else {
                                // Price excludes tax - add tax amount
                                rowTax = rowTotal * 0.11;
                            }
                        }

                        subtotal += rowTotal;
                        taxAmount += rowTax;

                        // Update row total display
                        $(this).find('input[name*="[total]"]').val(formatCurrency(rowTotal + rowTax));
                    } else {
                        $(this).find('input[name*="[total]"]').val('');
                    }
                });

                const totalBeforeDiscount = subtotal + taxAmount;
                const discount = parseCurrency($('#discount-amount').val());
                const finalTotal = totalBeforeDiscount - discount;

                // Update display fields
                $('#subtotal-amount').val(formatCurrency(subtotal));
                $('#tax-amount').val(formatCurrency(taxAmount));
                $('#total-before-discount').val(formatCurrency(totalBeforeDiscount));
                $('#final-total').val(formatCurrency(finalTotal));

                // Update hidden fields
                $('#purchase-order-subtotal').val(subtotal);
                $('#purchase-order-tax-amount').val(taxAmount);
                $('#purchase-order-total').val(finalTotal);
            }

            // Global currency formatting functions
            function formatCurrency(amount) {
                if (!amount || isNaN(amount)) return 'Rp 0';
                return 'Rp ' + new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(amount)
            }

            function parseCurrency(str) {
                if (!str) return 0;
                return parseFloat(str.replace(/[^\d.-]/g, '')) || 0;
            }

            function formatCurrencyInput(input) {
                const value = parseCurrency(input.value);
                input.value = formatCurrency(value);
            }

            // Initialize calculations
            calculateTotals();

            // Bind calculation events
            $(document).on('input change',
                'input[name*="[quantity]"], input[name*="[unit_price]"], select[name*="[tax_id]"]',
                function() {
                    if ($(this).attr('name').includes('[unit_price]')) {
                        // formatCurrencyInput(this);
                    }
                    calculateTotals();
                });

            // Include tax checkbox handler
            $('#include-tax').on('change', function() {
                calculateTotals();
            });

            // Discount input change handler
            $('#discount-amount').on('input', function() {
                // formatCurrencyInput(this);
                calculateTotals();
            });

            // Add product row functionality
            // $('#add-product-row').on('click', function() {
            //     if (productTableEnabled) {
            //         addProductRow();
            //     }
            // });

            // Remove product row function
            window.removeProductRow = function(button) {
                if ($('#product-table tbody tr').length > 1) {
                    $(button).closest('tr').remove();
                    calculateTotals();
                }
            };

            //     function addProductRow() {
            //         const rowCount = $('#product-table tbody tr').length;
            //         const newRow = `
        //     <tr>
        //         <td>
        //             <select class="form-select product-select" name="products[${rowCount}][product_id]" data-allow-clear="false">
        //                 <option value="">Pilih Produk</option>
        //             </select>
        //         </td>
        //         <td><input type="number" class="form-control" name="products[${rowCount}][quantity]" min="1" step="1" /></td>
        //         <td><input type="text" class="form-control" name="products[${rowCount}][unit]" disabled /></td>
        //         <td><input type="text" class="form-control currency-input" name="products[${rowCount}][unit_price]" placeholder="Rp 0" /></td>
        //         <td>
        //             <select class="form-select tax-select" name="products[${rowCount}][tax_id]" data-allow-clear="false">
        //                 <option value="">Tanpa Pajak</option>
        //             </select>
        //         </td>
        //         <td><input type="text" class="form-control" name="products[${rowCount}][total]" disabled /></td>
        //         <td>
        //             <button class="btn btn-label-danger btn-sm" type="button" onclick="removeProductRow(this)">
        //                 <i class="icon-base ti tabler-x me-1"></i>
        //                 <span class="align-middle">Hapus</span>
        //             </button>
        //         </td>
        //     </tr>
        // `;

            //         $('#product-table tbody').append(newRow);

            //         // Load products for the new row
            //         loadProducts();
            //     }

            // Form submission
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
                    'purchase-order-due-date'
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
                    title: 'Buat Purchase Order?',
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
                const formData = new FormData($('#purchase-order-form')[0]);

                // AJAX submission
                $.ajax({
                    url: "{{ route('main_transaction.purchase_order.store') }}",
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
