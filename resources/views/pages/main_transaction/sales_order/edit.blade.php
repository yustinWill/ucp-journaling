@extends('layouts.default')
@section('page_title')
    Edit Sales Order - {{ $salesOrder['transaction_code'] }}
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Edit Sales Order</h4>
        <form class="card-body" id="sales-order-form" autocomplete="off">
            @csrf
            @method('PUT')
            
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
                    <select class="global-select2 form-select" name="customer_id" id="customer_id" 
                            data-allow-clear="false" required>
                        <option value="">Pilih Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ $customer->id == $salesOrder['f_customer_id'] ? 'selected' : '' }}>
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
                    <select class="global-select2 form-select" name="payment_term_id" id="payment_term_id" 
                            data-allow-clear="false" required>
                        <option value="">Pilih Syarat Pembayaran</option>
                        @foreach($paymentTerms as $term)
                            <option value="{{ $term->id }}" data-type="{{ strtoupper($term->type) }}" data-days="{{ $term->days }}"
                                    {{ $term->id == $salesOrder['f_payment_term_id'] ? 'selected' : '' }}>
                                {{ $term->code }} - {{ $term->name }}
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
                        <input type="number" class="form-control" id="payment_term_days"
                            name="payment_term_day_count" placeholder="0" min="0" max="365" 
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
                            <option value="{{ $person->id }}" {{ $person->id == $salesOrder['customer_sales_person_id'] ? 'selected' : '' }}>
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
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" {{ $warehouse->id == $salesOrder['f_warehouse_id'] ? 'selected' : '' }}>
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
                    <textarea class="form-control" name="description" id="description" rows="4"
                              maxlength="1000">{{ $salesOrder['description'] ?? '' }}</textarea>
                </div>
            </div>

            <!-- Section 4: Product Information -->
            <div class="section-header mt-12">
                <h6>Informasi Produk</h6>
            </div>
            
            <!-- Information Box -->
            <div class="alert alert-info mb-4">
                <i class="icon-base ti tabler-info-circle me-2"></i>
                <strong>Catatan:</strong> Pilih Customer dan Gudang terlebih dahulu untuk dapat menambahkan produk. 
                Produk yang muncul adalah produk yang sudah di-mapping dengan customer tersebut.
            </div>

            <!-- Product Table -->
            <div class="table-responsive">
                <table class="table table-bordered" id="product-table">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 30%;">PRODUK <span style="color: red;">*</span></th>
                            <th style="width: 12%;">KUANTITAS <span style="color: red;">*</span></th>
                            <th style="width: 10%;">SATUAN</th>
                            <th style="width: 15%;">HARGA SATUAN <span style="color: red;">*</span></th>
                            <th style="width: 13%;">PAJAK</th>
                            <th style="width: 15%;">JUMLAH</th>
                            <th style="width: 5%;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody id="product-tbody">
                        <!-- Product lines will be loaded here -->
                    </tbody>
                </table>
            </div>
            
            <!-- Add Product Line Button -->
            <div class="mb-4">
                <button type="button" class="btn btn-primary" id="add-product-line">
                    <i class="icon-base ti tabler-plus me-2"></i>Tambah Baris
                </button>
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
                            <select class="global-select2 form-select" name="tags[]" id="tags" multiple data-allow-clear="false">
                                @foreach($tags as $tag)
                                    <option value="{{ $tag->id }}" 
                                        {{ in_array($tag->id, $salesOrder['selected_tags'] ?? []) ? 'selected' : '' }}>
                                        {{ $tag->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Memo Internal</label>
                            <textarea class="form-control" name="internal_memo" id="internal_memo" rows="4"
                                      maxlength="500">{{ $salesOrder['internal_memo'] ?? '' }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Lampiran</label>
                            <input type="file" class="form-control" name="attachment" id="attachment" 
                                   accept=".xlsx,.xls,.docx,.doc,.pdf,.png,.jpg,.jpeg,.zip" />
                            <div class="form-text">File dapat berupa Excel, Word, PDF, PNG, atau Zip (Max: 10MB)</div>
                            @if($salesOrder['attachment'])
                                <div class="mt-2">
                                    <small class="text-muted">File saat ini: </small>
                                    <a href="{{ Storage::url($salesOrder['attachment']) }}" target="_blank" class="text-primary">
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
                                <div class="col-6"><h5><strong>Total Akhir:</strong></h5></div>
                                <div class="col-6 text-end"><h5 id="display-grand-total">Rp {{ number_format($salesOrder['grand_price'] ?? 0, 0, ',', '.') }}</h5></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hidden inputs for calculations -->
            <input type="hidden" name="subtotal" id="subtotal" value="{{ $salesOrder['subtotal'] ?? 0 }}">
            <input type="hidden" name="total_tax" id="total_tax" value="{{ $salesOrder['total_tax'] ?? 0 }}">
            <input type="hidden" name="total_price_before_discount" id="total_price_before_discount" value="{{ $salesOrder['total_price_before_discount'] ?? 0 }}">
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
    
    // Load existing product lines
    loadExistingProductLines();
    
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
    
    // Warehouse selection change (disabled for edit)
    $('#warehouse_id').on('change', function() {
        // In edit mode, we don't clear products when warehouse changes
        // as products are already established
    });
    
    // Add product line button
    $('#add-product-line').on('click', function() {
        addProductLine();
    });
    
    // Discount change
    $('#discount').on('input', function() {
        calculateTotals();
    });
    
    // Form submission
    $('#salesOrderForm').on('submit', function(e) {
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
        
        formData.append('product_lines', JSON.stringify(productLines));
        
        $.ajax({
            url: '{{ route("main_transaction.sales_order.update", $salesOrder["id"]) }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    window.location.href = '{{ route("main_transaction.sales_order.detail", $salesOrder["id"]) }}';
                } else {
                    alert('Terjadi kesalahan: ' + response.message);
                }
            },
            error: function(xhr) {
                let errorMsg = 'Terjadi kesalahan saat menyimpan data.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMsg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                }
                alert(errorMsg);
            }
        });
    });
    
    // Load existing product lines
    function loadExistingProductLines() {
        const productLines = @json($salesOrder['product_lines'] ?? []);
        
        productLines.forEach(function(line, index) {
            productRowIndex = index + 1;
            addProductLineWithData(line);
        });
        
        calculateTotals();
    }
    
    // Add product line with existing data
    function addProductLineWithData(lineData) {
        const rowHtml = `
            <tr data-row-index="${productRowIndex}">
                <td>
                    <select class="global-select2 form-select product-select" name="products[${productRowIndex}][product_id]" 
                            data-allow-clear="false" required>
                        <option value="">Pilih Produk</option>
                    </select>
                </td>
                <td>
                    <input type="number" class="form-control quantity-input" name="products[${productRowIndex}][quantity]" 
                           min="0.01" step="0.01" value="${lineData.quantity || 0}" required />
                </td>
                <td>
                    <input type="text" class="form-control unit-display" value="${lineData.unit_name || ''}" disabled />
                </td>
                <td>
                    <input type="number" class="form-control unit-price-input" name="products[${productRowIndex}][unit_price]" 
                           min="0" step="0.01" value="${lineData.unit_price || 0}" required />
                </td>
                <td>
                    <select class="form-select tax-select" name="products[${productRowIndex}][tax_id]">
                        <option value="">Tanpa Pajak</option>
                        @foreach($taxOptions as $tax)
                            <option value="{{ $tax->id }}" data-rate="{{ $tax->rate }}">{{ $tax->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control line-total-display" value="${formatCurrency(lineData.line_total || 0)}" disabled />
                    <input type="hidden" class="line-total-value" name="products[${productRowIndex}][line_total]" value="${lineData.line_total || 0}" />
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger remove-product-line" 
                            title="Hapus baris">
                        <i class="icon-base ti tabler-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        
        $('#product-tbody').append(rowHtml);
        
        // Initialize select2 for the new row
        const productSelect = $(`tr[data-row-index="${productRowIndex}"] .product-select`);
        productSelect.select2();
        
        // Load products and set selected product
        loadProductsForRowWithSelection(productRowIndex, $('#customer_id').val(), $('#warehouse_id').val(), lineData.product_id, lineData.tax_id);
        
        // Bind events for new row
        bindProductRowEvents(productRowIndex);
    }
    
    // Load customer information
    function loadCustomerInfo(customerId) {
        // Customer info is already loaded from server data
        // This function is kept for consistency but may not be needed in edit mode
    }
    
    // Clear customer information
    function clearCustomerInfo() {
        $('#customer_email').val('');
        $('#customer_address').val('');
        $('#sales_person_name').val('');
    }
    
    // Add product line (for new products)
    function addProductLine() {
        const customerId = $('#customer_id').val();
        const warehouseId = $('#warehouse_id').val();
        
        if (!customerId || !warehouseId) {
            alert('Customer dan Gudang harus dipilih.');
            return;
        }
        
        productRowIndex++;
        const rowHtml = `
            <tr data-row-index="${productRowIndex}">
                <td>
                    <select class="global-select2 form-select product-select" name="products[${productRowIndex}][product_id]" 
                            data-allow-clear="false" required>
                        <option value="">Pilih Produk</option>
                    </select>
                </td>
                <td>
                    <input type="number" class="form-control quantity-input" name="products[${productRowIndex}][quantity]" 
                           min="0.01" step="0.01" value="1" readonly required />
                </td>
                <td>
                    <input type="text" class="form-control unit-display" disabled />
                </td>
                <td>
                    <input type="number" class="form-control unit-price-input" name="products[${productRowIndex}][unit_price]" 
                           min="0" step="0.01" required />
                </td>
                <td>
                    <select class="form-select tax-select" name="products[${productRowIndex}][tax_id]">
                        <option value="">Tanpa Pajak</option>
                        @foreach($taxOptions as $tax)
                            <option value="{{ $tax->id }}" data-rate="{{ $tax->rate }}">{{ $tax->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control line-total-display" disabled />
                    <input type="hidden" class="line-total-value" name="products[${productRowIndex}][line_total]" />
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger remove-product-line" 
                            title="Hapus baris">
                        <i class="icon-base ti tabler-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        
        $('#product-tbody').append(rowHtml);
        
        // Initialize select2 for the new row
        $(`tr[data-row-index="${productRowIndex}"] .product-select`).select2();
        
        // Load products for this row
        loadProductsForRow(productRowIndex, customerId, warehouseId);
        
        // Bind events for new row
        bindProductRowEvents(productRowIndex);
    }
    
    // Load products for a specific row with selection
    function loadProductsForRowWithSelection(rowIndex, customerId, warehouseId, selectedProductId, selectedTaxId) {
        $.ajax({
            url: '{{ route("main_transaction.sales_order.get_warehouse_products", ":warehouse_id") }}'.replace(':warehouse_id', warehouseId),
            method: 'GET',
            data: { 
                customer_id: customerId
            },
            success: function(response) {
                if (response.success) {
                    const productSelect = $(`tr[data-row-index="${rowIndex}"] .product-select`);
                    const taxSelect = $(`tr[data-row-index="${rowIndex}"] .tax-select`);
                    
                    productSelect.empty();
                    productSelect.append('<option value="">Pilih Produk</option>');
                    
                    response.data.forEach(function(product) {
                        const selected = product.value == selectedProductId ? 'selected' : '';
                        productSelect.append(`<option value="${product.value}" 
                            data-unit="${product.unit}" 
                            data-price="${product.price}"
                            data-tax-id="${product.tax_id}" ${selected}>${product.label}</option>`);
                    });
                    
                    // Set tax selection
                    if (selectedTaxId) {
                        taxSelect.val(selectedTaxId);
                    }
                    
                    // Trigger change to populate unit and price
                    productSelect.trigger('change');
                } else {
                    console.error('Failed to load products');
                }
            },
            error: function() {
                console.error('Error loading products');
            }
        });
    }
    
    // Load products for a specific row (new products)
    function loadProductsForRow(rowIndex, customerId, warehouseId) {
        $.ajax({
            url: '{{ route("main_transaction.sales_order.get_warehouse_products", ":warehouse_id") }}'.replace(':warehouse_id', warehouseId),
            method: 'GET',
            data: { 
                customer_id: customerId
            },
            success: function(response) {
                if (response.success) {
                    const productSelect = $(`tr[data-row-index="${rowIndex}"] .product-select`);
                    productSelect.empty();
                    productSelect.append('<option value="">Pilih Produk</option>');
                    
                    response.data.forEach(function(product) {
                        productSelect.append(`<option value="${product.value}" 
                            data-unit="${product.unit}" 
                            data-price="${product.price}"
                            data-tax-id="${product.tax_id}">${product.label}</option>`);
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
    
    // Bind events for product row
    function bindProductRowEvents(rowIndex) {
        const row = $(`tr[data-row-index="${rowIndex}"]`);
        
        // Product selection change
        row.find('.product-select').on('change', function() {
            const selectedOption = $(this).find(':selected');
            const unit = selectedOption.data('unit') || '';
            const price = selectedOption.data('price') || 0;
            const taxId = selectedOption.data('tax-id') || '';
            
            row.find('.unit-display').val(unit);
            row.find('.unit-price-input').val(price);
            row.find('.tax-select').val(taxId);
            
            calculateLineTotal(rowIndex);
        });
        
        // Quantity change (disabled for mapped products)
        // row.find('.quantity-input').on('input', function() {
        //     calculateLineTotal(rowIndex);
        // });
        
        // Unit price change
        row.find('.unit-price-input').on('input', function() {
            calculateLineTotal(rowIndex);
        });
        
        // Tax change
        row.find('.tax-select').on('change', function() {
            calculateLineTotal(rowIndex);
        });
        
        // Remove button
        row.find('.remove-product-line').on('click', function() {
            row.remove();
            calculateTotals();
        });
    }
    
    // Calculate line total for specific row
    function calculateLineTotal(rowIndex) {
        const row = $(`tr[data-row-index="${rowIndex}"]`);
        const quantity = parseFloat(row.find('.quantity-input').val()) || 0;
        const unitPrice = parseFloat(row.find('.unit-price-input').val()) || 0;
        const taxId = row.find('.tax-select').val();
        
        let lineTotal = quantity * unitPrice;
        
        // Add tax if selected
        if (taxId) {
            const taxOption = row.find('.tax-select option:selected');
            const taxRate = parseFloat(taxOption.data('rate')) || 0;
            lineTotal = lineTotal * (1 + taxRate / 100);
        }
        
        row.find('.line-total-display').val(formatCurrency(lineTotal));
        row.find('.line-total-value').val(lineTotal);
        
        calculateTotals();
    }
    
    // Calculate all totals
    function calculateTotals() {
        let subtotal = 0;
        let totalTax = 0;
        
        $('#product-tbody tr').each(function() {
            const quantity = parseFloat($(this).find('.quantity-input').val()) || 0;
            const unitPrice = parseFloat($(this).find('.unit-price-input').val()) || 0;
            const taxId = $(this).find('.tax-select').val();
            
            const lineSubtotal = quantity * unitPrice;
            subtotal += lineSubtotal;
            
            if (taxId) {
                const taxOption = $(this).find('.tax-select option:selected');
                const taxRate = parseFloat(taxOption.data('rate')) || 0;
                totalTax += lineSubtotal * (taxRate / 100);
            }
        });
        
        const totalBeforeDiscount = subtotal + totalTax;
        const discount = parseFloat($('#discount').val()) || 0;
        const grandTotal = totalBeforeDiscount - discount;
        
        $('#subtotal').val(subtotal);
        $('#total_tax').val(totalTax);
        $('#total_price_before_discount').val(totalBeforeDiscount);
        $('#grand_price').val(grandTotal);
        
        $('#display-subtotal').text(formatCurrency(subtotal));
        $('#display-total-tax').text(formatCurrency(totalTax));
        $('#display-total-before-discount').text(formatCurrency(totalBeforeDiscount));
        $('#display-discount').text(formatCurrency(discount));
        $('#display-grand-total').text(formatCurrency(grandTotal));
    }
    
    
    // Collect product lines for form submission
    function collectProductLines() {
        const productLines = [];
        
        $('#product-tbody tr').each(function() {
            const productId = $(this).find('.product-select').val();
            const quantity = parseFloat($(this).find('.quantity-input').val()) || 0;
            const unitPrice = parseFloat($(this).find('.unit-price-input').val()) || 0;
            const taxId = $(this).find('.tax-select').val() || null;
            const lineTotal = parseFloat($(this).find('.line-total-value').val()) || 0;
            
            if (productId && quantity > 0 && unitPrice > 0) {
                productLines.push({
                    product_id: productId,
                    quantity: quantity,
                    unit_price: unitPrice,
                    tax_id: taxId,
                    line_total: lineTotal
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
        const transactionDate = $('#transaction_date').val();
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
});
</script>
@endsection