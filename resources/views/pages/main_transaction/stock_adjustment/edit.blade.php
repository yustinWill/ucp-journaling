@extends('layouts.default')
@section('page_title')
    Edit Stock Adjustment - {{ $stockAdjustment->adjustment_number }}
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Edit Stock Adjustment - {{ $stockAdjustment->adjustment_number }}</h4>
        <form class="card-body" id="stockAdjustmentEditForm" autocomplete="off">
            <input type="hidden" id="adjustment-id" value="{{ $stockAdjustment->id }}">
            <!-- Basic Information Section -->
            <div class="section-header mt-12">
                <h6>Informasi Dasar</h6>
                <hr class="mx-n6">
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Nomor Adjustment</label>
                    <input type="text" class="form-control" id="adjustment-number"
                        value="{{ $stockAdjustment->adjustment_number }}" readonly />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Adjustment <span style="color: red;">*</span></label>
                    <input type="date" class="form-control global-single-date-picker" id="adjustment-date"
                        value="{{ $stockAdjustment->adjustment_date }}" />
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tipe Adjustment <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="adjustment-type" data-allow-clear="false">
                        <option value="">Pilih Tipe Adjustment</option>
                        @foreach ($adjustmentTypes as $type)
                            <option value="{{ $type['value'] }}" @if ($stockAdjustment->adjustment_type == $type['value']) selected @endif>
                                {{ $type['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Gudang <span style="color: red;">*</span></label>
                    <div class="row g-2">
                        <div class="col">
                            <select class="global-select2 form-select" id="warehouse-select" data-allow-clear="false"
                                disabled>
                                <option value="">Pilih Gudang</option>
                                @foreach ($warehouses as $warehouse)
                                    <option value="{{ $warehouse['value'] }}"
                                        @if ($stockAdjustment->warehouse_id == $warehouse['value']) selected @endif>{{ $warehouse['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($canAddWarehouses)
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

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Penanggung Jawab <span style="color: red;">*</span></label>
                    <div class="row g-2">
                        <div class="col">
                            <select class="global-select2 form-select" id="pic-select" data-allow-clear="false">
                                <option value="">Pilih PIC</option>
                                @foreach ($picOptions as $pic)
                                    <option value="{{ $pic['value'] }}" @if ($stockAdjustment->pic_id == $pic['value']) selected @endif>
                                        {{ $pic['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="status-select" data-allow-clear="false">
                        <option value="">Pilih Status</option>
                        @foreach ($statusOptions as $status)
                            <option value="{{ $status['value'] }}" @if ($stockAdjustment->status == $status['value']) selected @endif>
                                {{ $status['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>


            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Deskripsi <span style="color: red;">*</span></label>
                    <textarea class="form-control" id="description" rows="3"
                        placeholder="Jelaskan alasan dan detail stock adjustment..." maxlength="1000">{{ $stockAdjustment->description }}</textarea>
                    <small class="form-text text-muted">Maksimal 1000 karakter</small>
                </div>
            </div>

            <!-- Product Selection Section -->
            <div class="section-header mt-12">
                <h6>Pemilihan Produk</h6>
                <hr class="mx-n6">
            </div>

            <!-- Filter Section -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Filter Kategori Produk</label>
                    <div class="row g-2">
                        <div class="col">
                            <select class="global-select2 form-select" id="product-category-filter"
                                data-allow-clear="false">
                                <option value="">Semua Kategori</option>
                                @foreach ($productCategories as $category)
                                    <option value="{{ $category['value'] }}">{{ $category['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($canAddCategories)
                            <div class="col-auto">
                                <a href="{{ route('master_data.category.add') }}" target="_blank"
                                    class="btn btn-outline-primary d-flex align-items-center" title="Tambah Kategori Baru">
                                    <i class="icon-base ti tabler-plus"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Cari Produk</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="product-search"
                            placeholder="Nama atau kode produk..." />
                        <button type="button" class="btn btn-outline-primary" id="search-products-btn">
                            <i class="icon-base ti tabler-search"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Produk yang Tersedia di Gudang</h6>
                        <button type="button" class="btn btn-md btn-primary waves-effect waves-light"
                            id="load-products-btn">
                            <i class="icon-base ti tabler-box me-2"></i>
                            Load Produk dari Gudang
                        </button>
                    </div>

                    <div class="mx-n5">
                        <table class="table table-bordered" id="available-products-table">
                            <thead>
                                <tr>
                                    <th style="width: 20%;">Kode Produk</th>
                                    <th style="width: 30%;">Nama Produk</th>
                                    <th style="width: 10%;">Unit</th>
                                    <th style="width: 15%;">Stok Tersedia</th>
                                    <th style="width: 15%;">Harga Satuan</th>
                                    <th style="width: 10%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="available-products-tbody">
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <i class="icon-base ti tabler-box-off me-2"></i>
                                        Pilih gudang dan klik "Load Produk dari Gudang" untuk memuat data
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- <!-- Load Products Button -->
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <button type="button" class="btn btn-md btn-primary waves-effect waves-light" id="load-products-btn">
                        <i class="icon-base ti tabler-box me-2"></i>
                        Load Produk dari Gudang
                    </button>
                    <small class="form-text text-muted ms-3">Klik untuk memuat ulang daftar produk dari gudang</small>
                </div>
            </div> --}}

            <!-- Product Adjustment Table -->


            <!-- Summary Section -->
            <div class="section-header mt-12">
                <h6>Ringkasan Adjustment</h6>
                <hr class="mx-n6">
            </div>

            <div class="row g-3 mb-6">
                <div class="col-md-4">
                    <div class="summary-card success">
                        <h6>Total Item</h6>
                        <h4 id="total-items-summary">0</h4>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="summary-card" id="total-qty-card">
                        <h6>Total Qty Adjustment</h6>
                        <h4 id="total-qty-summary">0</h4>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="summary-card" id="total-value-card">
                        <h6>Estimasi Nilai Adjustment</h6>
                        <h4 id="total-value-summary">Rp 0</h4>
                    </div>
                </div>
            </div>

            {{-- <div class="section-header mt-12">
                <h6>Daftar Produk untuk Adjustment</h6>
                <hr class="mx-n6">
            </div> --}}
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Produk yang akan disesuaikan</h6>
                    </div>

                    <div class="mx-n5">
                        <table class="table table-bordered" id="products-table">
                            <thead>
                                <tr>
                                    <th style="width: 15%;">Kode Produk</th>
                                    <th style="width: 25%;">Nama Produk</th>
                                    <th style="width: 8%;">Unit</th>
                                    <th style="width: 10%;">Stok Sistem</th>
                                    <th style="width: 12%;">Qty Adjustment</th>
                                    <th style="width: 10%;">Selisih</th>
                                    <th style="width: 15%;">Alasan</th>
                                    <th style="width: 5%;">Hapus</th>
                                </tr>
                            </thead>
                            <tbody id="selected-products-tbody">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Informasi Tambahan Section -->
            <div class="section-header mt-12">
                <h6>Informasi Tambahan</h6>
                <hr class="mx-n6">
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Memo <small class="text-muted">(Internal)</small></label>
                    <textarea class="form-control" id="adjustment-memo" rows="3"
                        placeholder="Catatan internal untuk stock adjustment..." maxlength="500">{{ $stockAdjustment->memo }}</textarea>
                    <small class="form-text text-muted">Maksimal 500 karakter - Hanya untuk keperluan internal</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tag</label>
                    <div class="row g-2">
                        <div class="col">
                            <select class="global-select2 form-select" id="adjustment-tags" multiple
                                data-allow-clear="false">
                                @foreach ($availableTags as $tag)
                                    {{-- <option value="{{ $tag['id'] }}" data-color="{{ $tag['tag_color_code'] }}" >
                                        {{ $tag['label'] }}</option> --}}


                                    <option value="{{ $tag->id }}" data-color="{{ $tag->tag_color_code }}"
                                        @if (isset($stockAdjustment->f_tag_codes) && in_array($tag->id, $stockAdjustment->f_tag_codes)) selected @endif>{{ $tag->code }} -
                                        {{ $tag->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($canAddTags)
                            <div class="col-auto">
                                <a href="{{ route('master_data.tag.add') }}" target="_blank"
                                    class="btn btn-outline-primary d-flex align-items-center" id="add-new-adjustment-tag"
                                    title="Tambah Tag Baru">
                                    <i class="icon-base ti tabler-plus"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                    <small class="form-text text-muted">Pilih satu atau lebih tag untuk kategorisasi</small>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 text-end">
                <button type="button" class="btn btn-label-danger me-3" id="cancel-btn">Batalkan</button>
                <button type="button" class="btn btn-success" id="update-btn">Update Adjustment</button>
            </div>
        </form>
    </div>
@endsection

@section('js_script')
    <script>
        $(function() {
            // Existing adjustment items from controller
            const existingAdjustmentItems = @json($adjustmentItems ?? []);
            let selectedProducts = [];

            // Initialize existing data on page load
            $(document).ready(function() {
                if (existingAdjustmentItems && existingAdjustmentItems.length > 0) {
                    // Merge existing items with product data
                    const mergedProducts = existingAdjustmentItems.map(item => {
                        selectedProducts.push({
                            product_id: item.product_id,
                            product_code: item.product_code,
                            product_name: item.product_name,
                            unit: item.unit,
                            current_stock: item.stock_before,
                            unit_cost: item.unit_cost,
                            adjustment_qty: item.adjustment_qty,
                            reason: item.reason
                        });

                        return {
                            code: item.product_code,
                            name: item.product_name,
                            unit: item.unit,
                            current_stock: item.stock_before,
                            category: item.category || 'FINISHED_GOODS',
                            unit_cost: item.unit_cost,
                            adjustment_qty: item.adjustment_qty,
                            reason: item.reason,
                            is_existing: true
                        };
                    });

                    renderProductTable(mergedProducts, true);
                } else {
                    clearProductTable();
                }
            });

            // Form validation
            function validateForm() {
                let isValid = true;
                const requiredFields = ['adjustment-date', 'adjustment-type', 'warehouse-select', 'pic-select',
                    'status-select', 'description'
                ];

                requiredFields.forEach(function(fieldId) {
                    const field = $('#' + fieldId);
                    if (!field.val() || field.val().trim() === '') {
                        field.addClass('is-invalid');
                        isValid = false;
                    } else {
                        field.removeClass('is-invalid');
                    }
                });

                const hasValidProducts = selectedProducts.filter(function(product) {
                    return product.adjustment_qty && product.adjustment_qty !== 0;
                }).length > 0;

                if (!hasValidProducts) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Produk Adjustment',
                        text: 'Pilih minimal satu produk dengan qty adjustment yang tidak nol.',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    isValid = false;
                }

                return isValid;
            }

            function loadProducts() {
                const warehouse = $('#warehouse-select').val();
                const categoryFilter = $('#product-category-filter').val();
                const searchFilter = $('#product-search').val();

                if (!warehouse) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih Gudang',
                        text: 'Silakan pilih gudang terlebih dahulu.',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    return;
                }

                // Show loading
                $('#load-products-btn').prop('disabled', true).html(
                    '<i class="icon-base ti tabler-loader-2 me-2"></i>Loading...');

                // Load products from server
                $.get('/main_transaction/stock_adjustment/get_warehouse_products', {
                        warehouse_id: warehouse,
                        category_filter: categoryFilter,
                        search_filter: searchFilter
                    })
                    .done(function(response) {
                        if (response.success) {
                            availableProducts = response.data;
                            renderAvailableProductsTable();
                            currentWarehouse = warehouse;
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Memuat Produk',
                                text: response.message || 'Terjadi kesalahan saat memuat produk',
                                customClass: {
                                    confirmButton: 'btn btn-primary'
                                },
                                buttonsStyling: false
                            });
                        }
                    })
                    .fail(function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Memuat Produk',
                            text: 'Terjadi kesalahan koneksi. Silakan coba lagi.',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        });
                    })
                    .always(function() {
                        $('#load-products-btn').prop('disabled', false).html(
                            '<i class="icon-base ti tabler-box me-2"></i>Load Produk dari Gudang');
                    });
            }

            $('#load-products-btn').on('click', loadProducts);
            $('#search-products-btn').on('click', loadProducts);

            // Auto search when Enter key is pressed
            $('#product-search').on('keypress', function(e) {
                if (e.which === 13) {
                    loadProducts();
                }
            });

            // Auto filter when category changes
            $('#product-category-filter').on('change', function() {
                if (currentWarehouse) {
                    loadProducts();
                }
            });

            function renderAvailableProductsTable() {
                const tbody = $('#available-products-tbody');

                if (availableProducts.length === 0) {
                    tbody.html(`
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="icon-base ti tabler-search-off me-2"></i>
                        Tidak ada produk ditemukan dengan filter yang dipilih
                    </td>
                </tr>
            `);
                    return;
                }

                let rows = '';
                availableProducts.forEach(function(product) {
                    //TEST
                    const isAlreadySelected = selectedProducts.find(p => p.product_code === product.code);
                    const buttonClass = isAlreadySelected ? 'btn-secondary' : 'btn-primary';
                    const buttonText = isAlreadySelected ? 'Sudah Dipilih' : 'Tambah';
                    const buttonDisabled = isAlreadySelected ? 'disabled' : '';

                    rows += `
                <tr data-product-code="${product.code}">
                    <td><strong>${product.code}</strong></td>
                    <td>${product.name}</td>
                    <td>${product.unit}</td>
                    <td><span class="text-info">${product.current_stock.toLocaleString('id-ID')}</span></td>
                    <td>Rp ${product.unit_cost.toLocaleString('id-ID')}</td>
                    <td>
                        <button type="button" class="btn btn-sm ${buttonClass} add-product-btn" 
                                data-product='${JSON.stringify(product)}' ${buttonDisabled}>
                            <i class="icon-base ti tabler-plus me-1"></i>${buttonText}
                        </button>
                    </td>
                </tr>
            `;
                });

                tbody.html(rows);
            }

            function renderProductTable(products, preserveExisting = false) {
                const tbody = $('#selected-products-tbody');

                if (products.length === 0) {
                    tbody.html(`
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                        <i class="icon-base ti tabler-search-off me-2"></i>
                        Tidak ada produk ditemukan dengan filter yang dipilih
                    </td>
                </tr>
            `);
                    return;
                }

                let rows = '';
                products.forEach(function(product, index) {
                    const isChecked = preserveExisting && product.is_existing ? 'checked' : '';
                    const adjustmentQty = preserveExisting && product.adjustment_qty ? product
                        .adjustment_qty : '';
                    const reason = preserveExisting && product.reason ? product.reason : '';

                    rows += `
                <tr data-product-code="${product.code}">
                    <td><strong>${product.code}</strong></td>
                    <td>${product.name}</td>
                    <td>${product.unit}</td>
                    <td><span class="text-info">${product.current_stock.toLocaleString('id-ID')}</span></td>
                    <td>
                        <input type="number" class="form-control form-control-sm adjustment-qty" 
                               placeholder="0" data-current-stock="${product.current_stock}" 
                               data-unit-cost="${product.unit_cost}" value="${adjustmentQty}" 
                               min="-${product.current_stock}">
                    </td>
                    <td class="variance-display">
                        ${adjustmentQty ? (adjustmentQty > 0 ? 
                            `<span class="text-success">+${parseInt(adjustmentQty).toLocaleString('id-ID')}</span>` :
                            adjustmentQty < 0 ? 
                            `<span class="text-danger">${parseInt(adjustmentQty).toLocaleString('id-ID')}</span>` :
                            '<span class="text-muted">0</span>'
                        ) : '<span class="text-muted">0</span>'}
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm reason-input" 
                               placeholder="Alasan adjustment..." maxlength="200" value="${reason}">
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger remove-product-btn" 
                                data-product-code="${product.code}">
                            <i class="icon-base ti tabler-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
                });

                tbody.html(rows);
                updateSummary();
            }

            // Add product to adjustment list
            $(document).on('click', '.add-product-btn', function() {
                const productData = JSON.parse($(this).attr('data-product'));
                const selectedProduct = {
                    product_id: productData.id,
                    product_code: productData.code,
                    product_name: productData.name,
                    unit: productData.unit,
                    current_stock: productData.current_stock,
                    unit_cost: productData.unit_cost,
                    adjustment_qty: 0,
                    reason: ''
                };

                selectedProducts.push(selectedProduct);
                renderSelectedProductsTable();
                renderAvailableProductsTable(); // Refresh to show "Sudah Dipilih" status
            });

            // Remove product from adjustment list
            $(document).on('click', '.remove-product-btn', function() {
                const productCode = $(this).data('product-code');
                selectedProducts = selectedProducts.filter(p => p.product_code !== productCode);
                renderSelectedProductsTable();
                renderAvailableProductsTable(); // Refresh to show "Tambah" button again
                updateSummary();
            });

            // Auto-calculation for variance and summary
            $(document).on('input', '.adjustment-qty', function() {
                const adjustmentQty = parseInt($(this).val()) || 0;
                const currentStock = parseInt($(this).data('current-stock')) || 0;
                const row = $(this).closest('tr');
                const productCode = row.data('product-code');
                const varianceCell = row.find('.variance-display');

                // Update variance display
                varianceCell.html(getVarianceDisplay(adjustmentQty));

                // Update selectedProducts array
                const productIndex = selectedProducts.findIndex(p => p.product_code === productCode);
                if (productIndex !== -1) {
                    selectedProducts[productIndex].adjustment_qty = adjustmentQty;
                }

                if (adjustmentQty > 0) {
                    varianceCell.html(
                        `<span class="text-success">+${adjustmentQty.toLocaleString('id-ID')}</span>`);
                } else if (adjustmentQty < 0) {
                    varianceCell.html(
                        `<span class="text-danger">${adjustmentQty.toLocaleString('id-ID')}</span>`);
                } else {
                    varianceCell.html('<span class="text-muted">0</span>');
                }

                updateSummary();
            });

            $(document).on('input', '.reason-input', function() {
                const row = $(this).closest('tr');
                const productCode = row.data('product-code');
                const productIndex = selectedProducts.findIndex(p => p.product_code === productCode);
                if (productIndex !== -1) {
                    selectedProducts[productIndex].reason = $(this).val();
                }
            });

            // Checkbox functionality
            $('#select-all-products').on('change', function() {
                $('.row-checkbox').prop('checked', $(this).prop('checked'));
                updateSummary();
            });

            $(document).on('change', '.row-checkbox', function() {
                const totalCheckboxes = $('.row-checkbox').length;
                const checkedCheckboxes = $('.row-checkbox:checked').length;
                $('#select-all-products').prop('checked', checkedCheckboxes === totalCheckboxes &&
                    totalCheckboxes > 0);
                updateSummary();
            });


            function renderSelectedProductsTable() {
                const tbody = $('#selected-products-tbody');

                if (selectedProducts.length === 0) {
                    tbody.html(`
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                        <i class="icon-base ti tabler-plus me-2"></i>
                        Tambahkan produk dari daftar di atas untuk mulai melakukan adjustment
                    </td>
                </tr>
            `);
                    $('#selected-products-count').text('0');
                    return;
                }

                let rows = '';
                selectedProducts.forEach(function(product, index) {
                    rows += `
                <tr data-product-code="${product.product_code}">
                    <td><strong>${product.product_code}</strong></td>
                    <td>${product.product_name}</td>
                    <td>${product.unit}</td>
                    <td><span class="text-info">${product.current_stock.toLocaleString('id-ID')}</span></td>
                    <td>
                        <input type="number" class="form-control form-control-sm adjustment-qty" 
                               placeholder="0" value="${product.adjustment_qty || 0}" 
                               data-current-stock="${product.current_stock}" 
                               data-unit-cost="${product.unit_cost}" 
                               min="-${product.current_stock}">
                    </td>
                    <td class="variance-display">
                        ${getVarianceDisplay(product.adjustment_qty || 0)}
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm reason-input" 
                               placeholder="Alasan adjustment..." maxlength="200" 
                               value="${product.reason || ''}">
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger remove-product-btn" 
                                data-product-code="${product.product_code}">
                            <i class="icon-base ti tabler-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
                });

                tbody.html(rows);
                $('#selected-products-count').text(selectedProducts.length);
                updateSummary();
            }

            function getVarianceDisplay(qty) {
                const adjustmentQty = parseInt(qty) || 0;
                if (adjustmentQty > 0) {
                    return `<span class="text-success">+${adjustmentQty.toLocaleString('id-ID')}</span>`;
                } else if (adjustmentQty < 0) {
                    return `<span class="text-danger">${adjustmentQty.toLocaleString('id-ID')}</span>`;
                } else {
                    return '<span class="text-muted">0</span>';
                }
            }

            // Update summary cards
            function updateSummary() {
                let totalItems = 0;
                let totalQty = 0;
                let totalValue = 0;

                $('#selected-products-tbody tr').each(function() {
                    const adjustmentQtyInput = $(this).find('.adjustment-qty');
                    const adjustmentQty = parseInt(adjustmentQtyInput.val()) || 0;
                    const unitCost = parseInt(adjustmentQtyInput.data('unit-cost')) || 0;

                    totalItems++;
                    totalQty += adjustmentQty;
                    totalValue += (adjustmentQty * unitCost);
                });

                // Update summary display
                $('#total-items-summary').text(totalItems.toLocaleString('id-ID'));

                // Update qty card styling based on positive/negative
                const qtyCard = $('#total-qty-card');
                if (totalQty > 0) {
                    qtyCard.removeClass('danger').addClass('success');
                    $('#total-qty-summary').text('+' + totalQty.toLocaleString('id-ID'));
                } else if (totalQty < 0) {
                    qtyCard.removeClass('success').addClass('danger');
                    $('#total-qty-summary').text(totalQty.toLocaleString('id-ID'));
                } else {
                    qtyCard.removeClass('success danger');
                    $('#total-qty-summary').text('0');
                }

                // Update value card styling
                const valueCard = $('#total-value-card');
                const formattedValue = new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(Math.abs(totalValue));

                if (totalValue > 0) {
                    valueCard.removeClass('danger').addClass('success');
                    $('#total-value-summary').text('+' + formattedValue);
                } else if (totalValue < 0) {
                    valueCard.removeClass('success').addClass('danger');
                    $('#total-value-summary').text('-' + formattedValue);
                } else {
                    valueCard.removeClass('success danger');
                    $('#total-value-summary').text('Rp 0');
                }
            }

            // Clear table when warehouse changes
            $('#warehouse-select').on('change', function() {
                clearProductTable();
            });

            function clearProductTable() {
                $('#selected-products-tbody').html(`
            <tr>
                <td colspan="8" class="text-center text-muted py-5">
                    <i class="icon-base ti tabler-box-off me-2"></i>
                    Klik "Load Produk dari Gudang" untuk memuat data
                </td>
            </tr>
        `);
                $('#select-all-products').prop('checked', false);
                updateSummary();
            }

            // Cancel button
            $('#cancel-btn').on('click', function() {
                window.location.href = "{{ route('main_transaction.stock_adjustment.index') }}";
            });

            $('#update-btn').on('click', function(e) {
                e.preventDefault();

                if (!validateForm()) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: 'Mohon lengkapi semua field yang wajib diisi.',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    return;
                }

                // Collect form data
                const formData = {
                    adjustment_id: $('#adjustment-id').val(),
                    adjustment_date: $('#adjustment-date').val(),
                    adjustment_type: $('#adjustment-type').val(),
                    warehouse_id: $('#warehouse-select').val(),
                    pic_id: $('#pic-select').val(),
                    status: $('#status-select').val(),
                    description: $('#description').val(),
                    memo: $('#adjustment-memo').val(),
                    tags: $('#adjustment-tags').val(),
                    products: []
                };

                // Collect selected products with valid adjustment quantities
                selectedProducts.forEach(function(product) {
                    if (product.adjustment_qty && product.adjustment_qty !== 0) {
                        formData.products.push({
                            product_id: product.product_id,
                            product_code: product.product_code,
                            product_name: product.product_name,
                            unit: product.unit,
                            stock_before: product.current_stock,
                            adjustment_qty: product.adjustment_qty,
                            unit_cost: product.unit_cost,
                            reason: product.reason
                        });
                    }
                });

                // Show confirmation
                Swal.fire({
                    title: 'Konfirmasi Simpan',
                    text: `Apakah Anda yakin ingin menyimpan stock adjustment dengan ${formData.products.length} produk?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-secondary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.value) {
                        $('#submit-btn').prop('disabled', true).html(
                            '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Loading...'
                        );
                        $.ajax({
                            url: "{{ route('main_transaction.stock_adjustment.update') }}",
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                ...formData
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Stock adjustment berhasil diubah.',
                                    customClass: {
                                        confirmButton: 'btn btn-success'
                                    },
                                    buttonsStyling: false
                                }).then(function() {
                                    window.location.href =
                                        "{{ route('main_transaction.stock_adjustment.index') }}";
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
                });
            });
        });
    </script>
@endsection
