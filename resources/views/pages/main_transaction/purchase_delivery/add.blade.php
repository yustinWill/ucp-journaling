@extends('layouts.default')
@section('page_title')
    Buat Purchase Delivery
@stop

@section('content')
<div class="card mb-6">
    <h4 class="card-header">Buat Purchase Delivery</h4>
    <form class="card-body" id="purchase-delivery-form" autocomplete="off">
        @csrf
        
        <!-- Informasi Dasar Transaksi Section -->
        <div class="section-header mt-12">
            <h6>Informasi Dasar Transaksi</h6>
            <hr class="mx-n6">
        </div>
        
        <!-- Row 1: Tanggal Pengiriman, Nomor Transaksi -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Tanggal Pengiriman <span style="color: red;">*</span></label>
                <input type="date" class="form-control global-single-date-picker" id="purchase-delivery-date" name="date" value="{{ date('Y-m-d') }}" placeholder="YYYY-MM-DD" required />
            </div>
            <div class="col-md-6">
                <label class="form-label">Nomor Transaksi <span style="color: red;">*</span></label>
                <div class="row g-2">
                    <div class="col">
                        <input type="text" class="form-control" id="purchase-delivery-reference" name="reference_no" placeholder="Auto-generated but editable" required />
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-outline-primary d-flex align-items-center" id="generate-pd-number-btn" title="Generate New Number">
                            <i class="icon-base ti tabler-refresh"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 2: Purchase Order (Optional) -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Purchase Order (Opsional)</label>
                <select class="global-select2 form-select" id="purchase-delivery-po" name="purchase_order_id" data-allow-clear="true">
                    <option value="">Tanpa Purchase Order</option>
                    @foreach($purchaseOrders as $po)
                        <option value="{{ $po['value'] }}" data-supplier="{{ $po['supplier'] }}" data-supplier-id="{{ $po['supplier_id'] }}" data-email="{{ $po['email'] }}" data-address="{{ $po['address'] ?? '' }}" data-warehouse-id="{{ $po['warehouse_id'] ?? '' }}">{{ $po['label'] }}</option>
                    @endforeach
                </select>
                <small class="form-text text-muted">Pilih PO untuk mengisi data supplier otomatis, atau biarkan kosong untuk entry manual</small>
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
                        <select class="global-select2 form-select" id="purchase-delivery-supplier" name="supplier_id" data-allow-clear="false" required>
                            <option value="">Pilih Supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier['value'] }}" data-email="{{ $supplier['email'] ?? '' }}" data-address="{{ $supplier['address'] ?? '' }}">{{ $supplier['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if($canAddSuppliers)
                        <div class="col-auto">
                            <a href="{{ route('master_data.contact.add') }}" target="_blank" class="btn btn-outline-primary d-flex align-items-center" title="Tambah Supplier Baru">
                                <i class="icon-base ti tabler-plus"></i>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" id="purchase-delivery-email" name="email" placeholder="Email akan otomatis terisi" readonly />
            </div>
        </div>

        <!-- Row 2: Alamat, Nomor Referensi Supplier -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Alamat</label>
                <textarea class="form-control" id="purchase-delivery-address" name="address" rows="2" placeholder="Alamat akan otomatis terisi" readonly></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Nomor Referensi Supplier</label>
                <input type="text" class="form-control" id="purchase-delivery-supplier-ref" name="supplier_reference" placeholder="Masukkan nomor referensi" />
            </div>
        </div>

        <!-- Detail Pengiriman Section -->
        <div class="section-header mt-12">
            <h6>Detail Pengiriman</h6>
            <hr class="mx-n6">
        </div>

        <!-- Row 1: Gudang Tujuan, Status -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Gudang Tujuan <span style="color: red;">*</span></label>
                <div class="row g-2">
                    <div class="col">
                        <select class="global-select2 form-select" id="purchase-delivery-warehouse" name="warehouse_id" data-allow-clear="false" required>
                            <option value="">Pilih Gudang</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse['value'] }}">{{ $warehouse['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if($canAddStorages)
                        <div class="col-auto">
                            <a href="{{ route('master_data.warehouse.add') }}" target="_blank" class="btn btn-outline-primary d-flex align-items-center" title="Tambah Gudang Baru">
                                <i class="icon-base ti tabler-plus"></i>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Ekspektasi Tanggal Diterima</label>
                <input type="date" class="form-control global-single-date-picker" id="purchase-delivery-expected-date" name="expected_date" placeholder="YYYY-MM-DD" />
            </div>
        </div>

        <!-- Product Section -->
        <div class="section-header mt-12 d-flex justify-content-between align-items-center">
            <h6>Informasi Pengiriman</h6>
            <div class="form-check form-switch" id="allow-over-delivery-container">
                <input class="form-check-input" type="checkbox" id="allow-over-delivery" name="allow_over_delivery">
                <label class="form-check-label" for="allow-over-delivery">Izinkan Over Delivery</label>
            </div>
        </div>

        <!-- Disabled State Message -->
        <div id="product-disabled-message" class="alert alert-info">
            <i class="icon-base ti tabler-info-circle me-2"></i>
            Silakan pilih <strong>Supplier</strong> dan <strong>Gudang</strong> terlebih dahulu untuk dapat menambah produk.
        </div>

        <!-- Product Table -->
        <div class="mb-4 mx-n5">
            <div class="table-responsive">
                <table class="table table-bordered" id="product-table">
                    <thead>
                        <tr id="po-mode-header" style="display: none;">
                            <th style="width: 25%; text-align: left;">Produk</th>
                            <th style="width: 10%; text-align: left;">Satuan</th>
                            <th style="width: 15%; text-align: right;">Ordered</th>
                            <th style="width: 15%; text-align: right;">Delivered</th>
                            <th style="width: 20%; text-align: right;">Qty Delivery</th>
                            <th style="width: 15%; text-align: right;">Remaining</th>
                        </tr>
                        <tr id="manual-mode-header">
                            <th style="width: 30%; text-align: left;">Produk</th>
                            <th style="width: 10%; text-align: left;">Satuan</th>
                            <th style="width: 15%; text-align: right;">Kuantitas</th>
                            <th style="width: 30%; text-align: left;">Catatan</th>
                            <th style="width: 15%; text-align: left;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="text-align: left;">
                                <select class="form-select product-select" name="products[0][product_id]" data-allow-clear="false" disabled>
                                    <option value="">Pilih Produk</option>
                                </select>
                            </td>
                            <td style="text-align: left;"><input type="text" class="form-control" name="products[0][unit]" disabled /></td>
                            <td style="text-align: right;"><input type="number" class="form-control text-end" name="products[0][quantity]" min="1" step="1" disabled /></td>
                            <td style="text-align: left;"><input type="text" class="form-control" name="products[0][notes]" placeholder="Catatan produk" disabled /></td>
                            <td style="text-align: left;">
                                <select class="form-select" name="products[0][status]" disabled>
                                    <option value="GOOD">GOOD - Baik</option>
                                    <option value="DAMAGED">DAMAGED - Rusak</option>
                                    <option value="PARTIAL">PARTIAL - Sebagian</option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Row Button -->
        <div class="mb-3">
            <button type="button" class="btn btn-primary" id="add-product-row" disabled>
                <i class="icon-base ti tabler-plus me-1"></i>
                <span class="align-middle">Tambah Baris</span>
            </button>
        </div>

        <!-- Previous Deliveries Section (only shown in PO mode) -->
        <div id="previous-deliveries-section" style="display: none;">
            <div class="section-header mt-12">
                <h6>Pengiriman Sebelumnya</h6>
            </div>
            
            <div class="table-responsive">
                <table class="table table-bordered" id="previous-deliveries-table">
                    <thead>
                        <tr>
                            <th style="width: 20%; text-align: left;">Tanggal</th>
                            <th style="width: 25%; text-align: left;">No. Referensi</th>
                            <th style="width: 15%; text-align: right;">Total Items</th>
                            <th style="width: 15%; text-align: left;">Status</th>
                            <th style="width: 25%; text-align: left;">Catatan</th>
                        </tr>
                    </thead>
                    <tbody id="previous-deliveries-body">
                        <!-- Previous deliveries will be loaded dynamically -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Informasi Tambahan & Ringkasan Section -->
        <div class="section-header mt-12">
            <h6>Informasi Tambahan & Ringkasan</h6>
            <hr class="mx-n6">
        </div>
        
        <!-- Split Layout: Tag, Memo, Attachment & Summary -->
        <div class="row">
            <!-- Left Side: Tag, Memo and Attachment -->
            <div class="col-md-6 mb-4">
                <div class="mb-3">
                    <label class="form-label">Tag</label>
                    <div class="row g-2">
                        <div class="col">
                            <select class="global-select2 form-select" id="purchase-delivery-tags" name="tags[]" multiple data-allow-clear="false">
                                @foreach($availableTags as $tag)
                                    <option value="{{ $tag['value'] }}" data-preview="{{ $tag['preview'] }}">{{ $tag['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if($canAddTags)
                            <div class="col-auto">
                                <a href="{{ route('master_data.tag.add') }}" target="_blank" class="btn btn-outline-primary d-flex align-items-center" title="Tambah Tag Baru">
                                    <i class="icon-base ti tabler-plus"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                    <small class="form-text text-muted">Pilih opsi atau tambah baru jika diperlukan</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Memo (Internal)</label>
                    <textarea class="form-control" id="purchase-delivery-memo" name="memo" rows="4" placeholder="Catatan internal..."></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Lampiran</label>
                    <input type="file" class="form-control" id="purchase-delivery-attachment" name="attachment" accept=".xlsx,.xls,.doc,.docx,.pdf,.png,.jpg,.jpeg,.zip" />
                    <small class="form-text text-muted">File dapat berupa Excel, Word, PDF, PNG, atau Zip (Max: 10MB)</small>
                </div>
            </div>
            
            <!-- Right Side: Delivery Summary Section -->
            <div class="col-md-6">
                <div class="summary-section ps-4">
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label">Total Items</label>
                        </div>
                        <div class="col-6">
                            <input type="text" class="form-control readonly-field" id="total-items" dir="rtl" value="0" disabled>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label">Total Delivered</label>
                        </div>
                        <div class="col-6">
                            <input type="text" class="form-control readonly-field" id="total-delivered" dir="rtl" value="0" disabled>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label">Delivery Status</label>
                        </div>
                        <div class="col-6">
                            <span class="badge bg-label-secondary" id="delivery-status">Belum Ada Data</span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label">Pesan</label>
                        </div>
                        <div class="col-6">
                            <textarea class="form-control" id="purchase-delivery-message" name="message" rows="3" placeholder="Pesan pengiriman..."></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hidden fields -->
        <input type="hidden" id="purchase-delivery-mode" name="delivery_mode" value="manual" />
        
        <!-- Action Buttons -->
        <div class="pt-6 text-end">
            <a href="{{ route('main_transaction.purchase_delivery.index') }}" class="btn btn-label-danger me-3">Batalkan</a>
            <button type="button" class="btn btn-info me-3" id="print-delivery-btn" style="display: none;">
                <i class="icon-base ti tabler-printer me-2"></i>Print PDF
            </button>
            <button type="submit" class="btn btn-success" id="submit-btn">Simpan</button>
        </div>
    </form>
</div>
@stop

@section('js_script')
<script>
$(function() {
    let productTableEnabled = false;
    let isPOMode = false;
    
    // Initialize transaction number
    initializePDTransactionNumber();
    
    // Check initial state
    checkProductTableState();
    
    // PO selection handler
    $('#purchase-delivery-po').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const poId = selectedOption.val();
        
        if (poId) {
            // Switch to PO mode
            isPOMode = true;
            $('#purchase-delivery-mode').val('po');
            
            // Auto-fill supplier information (disabled)
            $('#purchase-delivery-supplier').val(selectedOption.data('supplier-id') || '').prop('disabled', true);
            $('#purchase-delivery-email').val(selectedOption.data('email') || '');
            $('#purchase-delivery-address').val(selectedOption.data('address') || '');
            
            // Auto-fill warehouse destination from PO
            const warehouseId = selectedOption.data('warehouse-id');
            if (warehouseId) {
                $('#purchase-delivery-warehouse').val(warehouseId).trigger('change');
            }
            
            // Show PO mode table headers
            $('#po-mode-header').show();
            $('#manual-mode-header').hide();
            $('#allow-over-delivery-container').show();
            $('#previous-deliveries-section').show();
            
            // Reset and load products for selected PO
            resetProductTable();
            loadPOProducts(poId);
            loadPreviousDeliveries(poId);
            
            // Show print button
            $('#print-delivery-btn').show();
        } else {
            // Switch to manual mode
            isPOMode = false;
            $('#purchase-delivery-mode').val('manual');
            
            // Clear and enable supplier fields
            $('#purchase-delivery-supplier').val('').prop('disabled', false);
            $('#purchase-delivery-email').val('');
            $('#purchase-delivery-address').val('');
            
            // Show manual mode table headers
            $('#po-mode-header').hide();
            $('#manual-mode-header').show();
            $('#allow-over-delivery-container').hide();
            $('#previous-deliveries-section').hide();
            
            // Reset product table
            resetProductTable();
            
            // Hide print button
            $('#print-delivery-btn').hide();
        }
        
        checkProductTableState();
        updateAddButtonVisibility();
    });
    
    // Manual supplier selection handler
    $('#purchase-delivery-supplier').on('change', function() {
        if (!isPOMode) {
            const selectedOption = $(this).find('option:selected');
            const supplierId = $(this).val();
            
            if (supplierId) {
                // Auto-fill supplier information (email and address are always readonly)
                $('#purchase-delivery-email').val(selectedOption.data('email') || '');
                $('#purchase-delivery-address').val(selectedOption.data('address') || '');
                
                // Reset product table when supplier changes
                resetProductTable();
            } else {
                $('#purchase-delivery-email').val('');
                $('#purchase-delivery-address').val('');
                resetProductTable();
            }
            checkProductTableState();
        }
    });
    
    // Reset product table when warehouse changes
    $('#purchase-delivery-warehouse').on('change', function() {
        if (!isPOMode) {
            resetProductTable();
        }
        checkProductTableState();
    });
    
    function checkProductTableState() {
        let hasSupplier, hasStorage;
        
        if (isPOMode) {
            hasSupplier = $('#purchase-delivery-po').val();
            hasStorage = $('#purchase-delivery-warehouse').val();
        } else {
            hasSupplier = $('#purchase-delivery-supplier').val();
            hasStorage = $('#purchase-delivery-warehouse').val();
        }
        
        productTableEnabled = hasSupplier && hasStorage;
        
        if (productTableEnabled) {
            // Enable product table
            $('#product-disabled-message').hide();
            $('#product-table tbody tr').each(function() {
                $(this).find('input, select, button').prop('disabled', false);
            });
            $('#add-product-row').prop('disabled', false);
            
            // Load products for manual mode
            if (!isPOMode) {
                loadProducts();
            }
        } else {
            // Disable product table
            $('#product-disabled-message').show();
            $('#product-table tbody tr').each(function() {
                $(this).find('input, select, button').prop('disabled', true);
            });
            $('#add-product-row').prop('disabled', true);
        }
    }
    
    function resetProductTable() {
        if (isPOMode) {
            // Reset PO mode table
            $('#product-table tbody').html(`
                <tr id="no-products-row">
                    <td colspan="6" class="text-center text-muted">Pilih Purchase Order untuk menampilkan produk</td>
                </tr>
            `);
        } else {
            // Reset manual mode table
            $('#product-table tbody').html(`
                <tr>
                    <td style="text-align: left;">
                        <select class="form-select product-select" name="products[0][product_id]" data-allow-clear="false" disabled>
                            <option value="">Pilih Produk</option>
                        </select>
                    </td>
                    <td style="text-align: left;"><input type="text" class="form-control" name="products[0][unit]" disabled /></td>
                    <td style="text-align: right;"><input type="number" class="form-control text-end" name="products[0][quantity]" min="1" step="1" disabled /></td>
                    <td style="text-align: left;"><input type="text" class="form-control" name="products[0][notes]" placeholder="Catatan produk" disabled /></td>
                    <td style="text-align: left;">
                        <select class="form-select" name="products[0][status]" disabled>
                            <option value="GOOD">GOOD - Baik</option>
                            <option value="DAMAGED">DAMAGED - Rusak</option>
                            <option value="PARTIAL">PARTIAL - Sebagian</option>
                        </select>
                    </td>
                </tr>
            `);
        }
        updateSummary();
    }
    
    function updateAddButtonVisibility() {
        const addButton = $('#add-product-row');
        if (isPOMode) {
            // In PO mode, hide add button if over delivery is not allowed
            const allowOverDelivery = $('#allow-over-delivery').is(':checked');
            if (allowOverDelivery) {
                addButton.show();
            } else {
                addButton.hide();
            }
        } else {
            // In manual mode, always show add button when enabled
            addButton.show();
        }
    }
    
    function loadPreviousDeliveries(poId) {
        if (!poId) return;
        
        // Show loading state
        $('#previous-deliveries-body').html('<tr><td colspan="4" class="text-center"><i class="spinner-border spinner-border-sm me-2"></i>Loading previous deliveries...</td></tr>');
        
        $.ajax({
            url: "{{ route('main_transaction.purchase_delivery.get_previous_deliveries') }}",
            method: 'GET',
            data: { po_id: poId },
            success: function(response) {
                const deliveries = response.deliveries || [];
                let html = '';
                
                if (deliveries.length === 0) {
                    html = '<tr><td colspan="4" class="text-center text-muted">Belum ada pengiriman sebelumnya untuk PO ini</td></tr>';
                } else {
                    deliveries.forEach(function(delivery) {
                        const statusClass = delivery.status === 'DELIVERED' ? 'bg-label-success' : 
                                          delivery.status === 'PENDING' ? 'bg-label-warning' : 'bg-label-secondary';
                        
                        html += `
                            <tr>
                                <td style="text-align: left;">${delivery.date}</td>
                                <td style="text-align: left;">${delivery.reference_no}</td>
                                <td style="text-align: right;">${delivery.total_items}</td>
                                <td style="text-align: left;">
                                    <span class="badge ${statusClass}">${delivery.status}</span>
                                </td>
                            </tr>
                        `;
                    });
                }
                
                $('#previous-deliveries-body').html(html);
            },
            error: function(xhr) {
                console.error('Error loading previous deliveries:', xhr);
                $('#previous-deliveries-body').html('<tr><td colspan="4" class="text-center text-danger">Error loading previous deliveries</td></tr>');
            }
        });
    }
    
    function loadPOProducts(poId) {
        if (!poId) return;
        
        // Show loading state
        $('#product-table tbody').html('<tr><td colspan="6" class="text-center"><i class="spinner-border spinner-border-sm me-2"></i>Loading products...</td></tr>');
        
        $.ajax({
            url: "{{ route('main_transaction.purchase_delivery.get_po_products') }}",
            method: 'GET',
            data: { po_id: poId },
            success: function(response) {
                const products = response.products || [];
                let html = '';
                
                if (products.length === 0) {
                    html = '<tr><td colspan="6" class="text-center text-muted">Tidak ada produk untuk PO ini</td></tr>';
                } else {
                    products.forEach(function(product, index) {
                        const remaining = product.remaining_qty;
                        const allowOverDelivery = $('#allow-over-delivery').is(':checked');
                        const maxDelivery = allowOverDelivery ? 9999 : remaining;
                        
                        html += `
                            <tr>
                                <td style="text-align: left;">
                                    <strong>${product.product_name}</strong>
                                </td>
                                <td style="text-align: left;">${product.unit}</td>
                                <td style="text-align: right;">${product.ordered_qty}</td>
                                <td style="text-align: right;">${product.delivered_qty}</td>
                                <td style="text-align: right;">
                                    <input type="number" 
                                           class="form-control text-end delivery-qty-input" 
                                           name="products[${index}][delivery_qty]" 
                                           min="0" 
                                           max="${maxDelivery}"
                                           value="0"
                                           data-product-id="${product.product_id}"
                                           data-remaining="${remaining}"
                                           ${remaining === 0 && !allowOverDelivery ? 'disabled' : ''}>
                                    <input type="hidden" name="products[${index}][product_id]" value="${product.product_id}">
                                    <input type="hidden" name="products[${index}][ordered_qty]" value="${product.ordered_qty}">
                                    <input type="hidden" name="products[${index}][delivered_qty]" value="${product.delivered_qty}">
                                    <input type="hidden" name="products[${index}][unit]" value="${product.unit}">
                                    <input type="hidden" name="products[${index}][status]" value="GOOD">
                                </td>
                                <td style="text-align: right;" class="remaining-qty" data-remaining="${remaining}">
                                    ${remaining}
                                </td>
                            </tr>
                        `;
                    });
                }
                
                $('#product-table tbody').html(html);
                
                // Attach event listeners to quantity inputs
                attachQuantityListeners();
                updateSummary();
                updateAddButtonVisibility();
            },
            error: function(xhr) {
                console.error('Error loading PO products:', xhr);
                $('#product-table tbody').html('<tr><td colspan="6" class="text-center text-danger">Error loading products</td></tr>');
            }
        });
    }
    
    function loadProducts() {
        const supplierId = $('#purchase-delivery-supplier').val();
        const warehouseId = $('#purchase-delivery-warehouse').val();
        
        if (!supplierId || !warehouseId) return;
        
        // Mock products - in real implementation, fetch via AJAX
        const products = [
            {value: '1', label: 'PRD-001 - Botol PET 330ml', unit: 'pcs'},
            {value: '2', label: 'PRD-002 - Label Kemasan', unit: 'lembar'},
            {value: '3', label: 'PRD-003 - Tutup Botol', unit: 'pcs'},
            {value: '4', label: 'PRD-004 - Kardus Packaging', unit: 'dus'}
        ];
        
        // Update all product selects
        $('#product-table tbody tr').each(function() {
            const productSelect = $(this).find('.product-select');
            
            // Clear and rebuild product options
            productSelect.empty().append('<option value="">Pilih Produk</option>');
            products.forEach(product => {
                productSelect.append(`<option value="${product.value}" data-unit="${product.unit}">${product.label}</option>`);
            });
            
            // Initialize Select2 with proper dropdown parent
            productSelect.select2({
                placeholder: 'Pilih Produk',
                allowClear: false,
                dropdownParent: $(this).closest('.table-responsive')
            });
        });
    }
    
    function attachQuantityListeners() {
        $('.delivery-qty-input').on('input', function() {
            const input = $(this);
            const remaining = parseInt(input.data('remaining'));
            const deliveryQty = parseInt(input.val()) || 0;
            const allowOverDelivery = $('#allow-over-delivery').is(':checked');
            const newRemaining = remaining - deliveryQty;
            
            // Update remaining display
            const remainingCell = input.closest('tr').find('.remaining-qty');
            remainingCell.text(newRemaining);
            
            // Update validation styling
            if (newRemaining < 0 && !allowOverDelivery) {
                input.addClass('is-invalid');
                remainingCell.addClass('text-danger');
            } else {
                input.removeClass('is-invalid');
                remainingCell.removeClass('text-danger');
            }
            
            updateSummary();
        });
    }
    
    // Over delivery checkbox handler
    $('#allow-over-delivery').on('change', function() {
        const isChecked = $(this).is(':checked');
        $('.delivery-qty-input').each(function() {
            const input = $(this);
            const remaining = parseInt(input.data('remaining'));
            if (isChecked) {
                input.attr('max', 9999);
            } else {
                input.attr('max', remaining);
                if (parseInt(input.val()) > remaining) {
                    input.val(remaining);
                }
            }
        });
        
        // Update add button visibility
        updateAddButtonVisibility();
        
        // Re-attach listeners to update colors
        attachQuantityListeners();
        $('.delivery-qty-input').trigger('input');
    });
    
    // Product selection handler for manual mode
    $(document).on('change', '.product-select', function() {
        const selectedOption = $(this).find('option:selected');
        const unit = selectedOption.data('unit');
        const row = $(this).closest('tr');
        
        if (unit) {
            row.find('input[name*="[unit]"]').val(unit);
        } else {
            row.find('input[name*="[unit]"]').val('');
        }
        
        updateSummary();
    });
    
    function updateSummary() {
        let totalItems = 0;
        let totalDelivered = 0;
        
        if (isPOMode) {
            $('.delivery-qty-input').each(function() {
                totalDelivered += parseInt($(this).val()) || 0;
            });
            totalItems = totalDelivered;
        } else {
            $('input[name*="[quantity]"]').each(function() {
                totalItems += parseInt($(this).val()) || 0;
            });
            totalDelivered = totalItems;
        }
        
        $('#total-items').val(totalItems);
        $('#total-delivered').val(totalDelivered);
        
        // Update delivery status
        const statusBadge = $('#delivery-status');
        if (totalDelivered === 0) {
            statusBadge.removeClass().addClass('badge bg-label-secondary').text('Belum Ada Data');
        } else if (isPOMode) {
            // Check if this is partial or complete delivery
            let hasRemaining = false;
            $('.remaining-qty').each(function() {
                const remaining = parseInt($(this).data('remaining'));
                const deliveryQty = parseInt($(this).closest('tr').find('.delivery-qty-input').val()) || 0;
                if (remaining - deliveryQty > 0) {
                    hasRemaining = true;
                }
            });
            
            if (hasRemaining) {
                statusBadge.removeClass().addClass('badge bg-label-warning').text('Partial Delivery');
            } else {
                statusBadge.removeClass().addClass('badge bg-label-success').text('Complete Delivery');
            }
        } else {
            statusBadge.removeClass().addClass('badge bg-label-primary').text('Manual Entry');
        }
    }
    
    function initializePDTransactionNumber() {
        // Generate initial transaction number on page load
        generatePDTransactionNumber();
        
        // Handle generate button click
        $('#generate-pd-number-btn').on('click', function() {
            generatePDTransactionNumber();
        });
        
        // Validate for duplicates when manually edited
        $('#purchase-delivery-reference').on('blur', function() {
            const number = $(this).val();
            if (number) {
                checkPDDuplicateNumber(number);
            }
        });
    }
    
    function generatePDTransactionNumber() {
        $('#generate-pd-number-btn').prop('disabled', true);
        
        // Mock generation - in real implementation, use AJAX
        const year = new Date().getFullYear();
        const month = String(new Date().getMonth() + 1).padStart(2, '0');
        const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
        const number = `PD-${year}${month}-${random}`;
        
        $('#purchase-delivery-reference').val(number);
        $('#generate-pd-number-btn').prop('disabled', false);
    }
    
    function checkPDDuplicateNumber(number) {
        // Mock validation - in real implementation, use AJAX
        console.log('Checking duplicate for:', number);
    }
    
    // Print PDF functionality
    $('#print-delivery-btn').on('click', function() {
        const poId = $('#purchase-delivery-po').val();
        if (!poId) {
            Swal.fire({
                title: 'Peringatan!',
                text: 'Pilih Purchase Order terlebih dahulu.',
                icon: 'warning',
                customClass: {
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false
            });
            return;
        }
        
        // Show loading message
        Swal.fire({
            title: 'Generate PDF',
            text: 'PDF sedang dipersiapkan...',
            icon: 'info',
            showConfirmButton: false,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Generate PDF via AJAX
        $.ajax({
            url: `{{ route('main_transaction.purchase_delivery.print_pdf', ':po_id') }}`.replace(':po_id', poId),
            method: 'GET',
            success: function(response) {
                Swal.close();
                if (response.success) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'PDF berhasil digenerate dan siap diunduh.',
                        icon: 'success',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    // In production, this would trigger actual file download
                    // window.open(response.download_url, '_blank');
                }
            },
            error: function(xhr) {
                Swal.close();
                let errorMessage = 'Terjadi kesalahan saat generate PDF.';
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
            }
        });
    });
    
    // Add product row functionality for manual mode
    $('#add-product-row').on('click', function() {
        if (productTableEnabled && !isPOMode) {
            addProductRow();
        }
    });
    
    // Remove product row function
    window.removeProductRow = function(button) {
        if (!isPOMode && $('#product-table tbody tr').length > 1) {
            $(button).closest('tr').remove();
            updateSummary();
        }
    };
    
    function addProductRow() {
        const rowCount = $('#product-table tbody tr').length;
        const newRow = `
            <tr>
                <td style="text-align: left;">
                    <select class="form-select product-select" name="products[${rowCount}][product_id]" data-allow-clear="false">
                        <option value="">Pilih Produk</option>
                    </select>
                </td>
                <td style="text-align: left;"><input type="text" class="form-control" name="products[${rowCount}][unit]" disabled /></td>
                <td style="text-align: right;"><input type="number" class="form-control text-end" name="products[${rowCount}][quantity]" min="1" step="1" /></td>
                <td style="text-align: left;"><input type="text" class="form-control" name="products[${rowCount}][notes]" placeholder="Catatan produk" /></td>
                <td style="text-align: left;">
                    <select class="form-select" name="products[${rowCount}][status]">
                        <option value="GOOD">GOOD - Baik</option>
                        <option value="DAMAGED">DAMAGED - Rusak</option>
                        <option value="PARTIAL">PARTIAL - Sebagian</option>
                    </select>
                </td>
            </tr>
        `;
        
        $('#product-table tbody').append(newRow);
        
        // Load products for the new row
        loadProducts();
        updateSummary();
    }
    
    // Form submission
    $('#purchase-delivery-form').on('submit', function(e) {
        e.preventDefault();
        
        // Validation
        const requiredFields = [
            'purchase-delivery-date',
            'purchase-delivery-warehouse'
        ];
        
        // Add supplier to required fields if not in PO mode
        if (!isPOMode) {
            requiredFields.push('purchase-delivery-supplier');
        }
        
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
        
        // Check product validation
        if (isPOMode) {
            // Check if at least one product has delivery quantity > 0
            const deliveryInputs = $('.delivery-qty-input:not(:disabled)');
            let hasDeliveryQty = false;
            deliveryInputs.each(function() {
                if (parseInt($(this).val()) > 0) {
                    hasDeliveryQty = true;
                    return false; // break
                }
            });
            
            if (!hasDeliveryQty && deliveryInputs.length > 0) {
                isValid = false;
                Swal.fire({
                    title: 'Validasi Error!',
                    text: 'Minimal satu produk harus memiliki kuantitas pengiriman > 0.',
                    icon: 'warning',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                });
                return;
            }
            
            // Check for over-delivery if not allowed
            if (!$('#allow-over-delivery').is(':checked')) {
                let hasOverDelivery = false;
                $('.delivery-qty-input').each(function() {
                    const input = $(this);
                    const remaining = parseInt(input.data('remaining'));
                    const deliveryQty = parseInt(input.val()) || 0;
                    
                    if (deliveryQty > remaining) {
                        hasOverDelivery = true;
                        input.addClass('is-invalid');
                    }
                });
                
                if (hasOverDelivery) {
                    isValid = false;
                    Swal.fire({
                        title: 'Validasi Error!',
                        text: 'Kuantitas pengiriman tidak boleh melebihi sisa yang belum dikirim.',
                        icon: 'warning',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    return;
                }
            }
        } else {
            // Manual mode: Check if at least one product is entered
            let hasProducts = false;
            $('select[name*="[product_id]"]').each(function() {
                if ($(this).val()) {
                    hasProducts = true;
                    return false; // break
                }
            });
            
            if (!hasProducts) {
                isValid = false;
                Swal.fire({
                    title: 'Validasi Error!',
                    text: 'Minimal satu produk harus dipilih.',
                    icon: 'warning',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                });
                return;
            }
        }
        
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
            title: 'Buat Purchase Delivery?',
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
        const formData = new FormData($('#purchase-delivery-form')[0]);
        
        // AJAX submission
        $.ajax({
            url: "{{ route('main_transaction.purchase_delivery.store') }}",
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Purchase Delivery berhasil dibuat.',
                    icon: 'success',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                }).then(() => {
                    window.location.href = "{{ route('main_transaction.purchase_delivery.index') }}";
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