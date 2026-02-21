@extends('layouts.default')
@section('page_title')
    Tambah Stock Transfer
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Tambah Stock Transfer</h4>
        <form class="card-body" id="stockAdjustmentForm" autocomplete="off">
            <input type="hidden" id="form-type" value="{{ $header_data['form_type'] ?? null }}">
            <input type="hidden" id="form-reference_id" value="{{ $header_data['form_reference_id'] ?? null }}">
            <!-- Informasi Dasar Transaksi Section -->
            <div class="section-header mt-12">
                <h6>Informasi Dasar Transaksi</h6>
                <hr class="mx-n6">
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tanggal Transaksi <span style="color: red;">*</span></label>
                    <input type="date" class="form-control global-single-date-picker"
                        @if (isset($header_data['form_type'])) disabled @endif id="adjustment-date"
                        @if (isset($header_data['form_type'])) value="{{ $header_data['transaction_date'] }}"
                    @else
                    value="{{ date('Y-m-d') }}" @endif />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nomor Transaksi <span style="color: red;">*</span></label>
                    <div class="row g-2">
                        <div class="col">
                            <input type="text" class="form-control" id="adjustment-number" name="adjustment_number"
                                placeholder="Auto-generated but editable" />
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


            <!-- Informasi Adjustment Section -->
            <div class="section-header mt-12">
                <h6>Informasi Adjustment</h6>
                <hr class="mx-n6">
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Gudang Sumber<span style="color: red;">*</span></label>
                    <div class="row g-2">
                        <div class="col">
                            <select class="global-select2 form-select" id="warehouse-select"
                                @if (isset($header_data['form_type'])) disabled @endif data-allow-clear="false">
                                <option value="">Pilih Gudang</option>
                                @foreach ($warehouses as $warehouse)
                                    <option value="{{ $warehouse['value'] }}"
                                        @if (isset($header_data['warehouse_id']) && $header_data['warehouse_id'] === $warehouse['value']) selected @elseif(!isset($header_data['warehouse_id']) && $loop->first) selected @endif>{{ $warehouse['label'] }}</option>
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

                <div class="col-md-6">
                    <label class="form-label">Gudang Tujuan<span style="color: red;">*</span></label>
                    <div class="row g-2">
                        <div class="col">
                            <select class="global-select2 form-select" id="warehouse-select-dest"
                                @if (isset($header_data['form_type'])) disabled @endif data-allow-clear="false">
                                <option value="">Pilih Gudang</option>
                                @foreach ($warehouses as $warehouse)
                                    <option value="{{ $warehouse['value'] }}">{{ $warehouse['label'] }}</option>
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
                            <select class="global-select2 form-select" id="pic-select"
                                @if (isset($header_data['form_type'])) disabled @endif data-allow-clear="false">
                                <option value="">Pilih PIC</option>
                                @foreach ($picOptions as $pic)
                                    <option value="{{ $pic['value'] }}" @if (isset($header_data['pic']) && $header_data['pic'] === $pic['value']) selected @endif>
                                        {{ $pic['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($canAddContacts)
                            <div class="col-auto">
                                <a href="{{ route('master_data.contact.add') }}" target="_blank"
                                    class="btn btn-outline-primary d-flex align-items-center" title="Tambah Contact Baru">
                                    <i class="icon-base ti tabler-plus"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Deskripsi <span style="color: red;">*</span></label>
                    <textarea class="form-control" id="description" rows="3"
                        placeholder="Jelaskan alasan dan detail stock transfer..." maxlength="1000">
@if (isset($header_data['description']))
{{ $header_data['description'] }}
@endif
</textarea>
                    <small class="form-text text-muted">Maksimal 1000 karakter</small>
                </div>
                {{-- <div class="col-md-6">
                    <label class="form-label">Status <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="status-select" data-allow-clear="false">
                        <option value="">Pilih Status</option>
                        @foreach ($statusOptions as $status)
                            <option value="{{ $status['value'] }}">{{ $status['label'] }}</option>
                        @endforeach
                    </select>
                </div> --}}
            </div>

            <div class="row g-3 mb-4">

                <div class="col-md-6">
                    <!-- Empty column for alignment -->
                </div>
            </div>

            <!-- Available Products Section -->
            @if (!isset($header_data['form_type']))
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
                                    data-allow-clear="true">
                                    <option value="">Semua Kategori</option>
                                    @foreach ($productCategories as $category)
                                        <option value="{{ $category['value'] }}">{{ $category['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if ($canAddCategories)
                                <div class="col-auto">
                                    <a href="{{ route('master_data.category.add') }}" target="_blank"
                                        class="btn btn-outline-primary d-flex align-items-center"
                                        title="Tambah Kategori Baru">
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
                                        <th style="width: 15%;">Kode Produk</th>
                                        <th style="width: 25%;">Nama Produk</th>
                                        <th style="width: 8%;">Unit</th>
                                        <th style="width: 12%;">Stok Tersedia</th>
                                        <th style="width: 12%;">Qty Transfer</th>
                                        <th style="width: 12%;">Sisa Stok</th>
                                        <th style="width: 16%;">Alasan</th>
                                    </tr>
                                </thead>
                                <tbody id="available-products-tbody">
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-5">
                                            <i class="icon-base ti tabler-box-off me-2"></i>
                                            Pilih gudang dan klik "Load Produk dari Gudang" untuk memuat data
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif


            <!-- Summary Section -->
            <div class="section-header mt-12">
                <h6>Ringkasan Stock Transfer</h6>
                <hr class="mx-n6">
            </div>

            <div class="row g-3 mb-6">
                <div class="col-md-6">
                    <div class="summary-card success">
                        <h6>Total Item</h6>
                        <h4 id="total-items-summary">0</h4>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="summary-card" id="total-qty-card">
                        <h6>Total Qty Transfer</h6>
                        <h4 id="total-qty-summary">0</h4>
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
                        placeholder="Catatan internal untuk stock transfer..." maxlength="500">
@if (isset($header_data['memo']))
{{ $header_data['memo'] }}
@endif
</textarea>
                    <small class="form-text text-muted">Maksimal 500 karakter - Hanya untuk keperluan internal</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tag</label>
                    <div class="row g-2">
                        <div class="col">
                            <select class="global-select2 form-select" id="adjustment-tags" multiple
                                data-allow-clear="false">
                                @foreach ($availableTags as $tag)
                                    <option value="{{ $tag['code'] }}" data-color="{{ $tag['color'] }}">
                                        {{ $tag['label'] }}</option>
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
                <button type="button" class="btn btn-success" id="submit-btn">Simpan Stock Transfer</button>
            </div>
        </form>
    </div>
@endsection

@section('js_script')
    <script>
        $(function() {
            // Global variables
            let availableProducts = [];
            let selectedProducts = [];
            let currentWarehouse = null;

            // Generate initial transaction number on page load
            generateTransactionNumber();

            // Generate transaction number function
            function generateTransactionNumber() {
                $.get('{{ route('main_transaction.stock_adjustment.generate_transaction_number') }}')
                    .done(function(response) {
                        if (response.success) {
                            $('#adjustment-number').val(response.transaction_number);
                        }
                    })
                    .fail(function() {
                        console.error('Failed to generate transaction number');
                    });
            }

            // Generate new transaction number when button is clicked
            $('#generate-number-btn').on('click', function() {
                generateTransactionNumber();
            });

            // Form validation
            function validateForm() {
                let isValid = true;
                const requiredFields = ['adjustment-date', 'warehouse-select', 'warehouse-select-dest',
                    'description'
                ];

                requiredFields.forEach(function(fieldId) {
                    const field = $('#' + fieldId);
                    if (!field.val() || field.val().trim() === '') {
                        console.log('Field ' + fieldId + ' is required.');
                        field.addClass('is-invalid');
                        isValid = false;
                    } else {
                        field.removeClass('is-invalid');
                    }
                });

                // Check if at least one product has qty transfer > 0
                let hasValidProducts = false;
                $('#available-products-tbody .qty-transfer-input').each(function() {
                    const qtyTransfer = parseInt($(this).val()) || 0;
                    if (qtyTransfer > 0) {
                        hasValidProducts = true;
                        return false; // break the loop
                    }
                });

                if (!hasValidProducts) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Produk Transfer',
                        text: 'Minimal satu produk harus memiliki qty transfer lebih dari 0.',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    isValid = false;
                }

                return isValid;
            }

            // Load products based on warehouse and filters
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

            // // Auto search when Enter key is pressed
            // $('#product-search').on('keypress', function(e) {
            //     if (e.which === 13) {
            //         loadProducts();
            //     }
            // });

            let searchTimeout;
            $('#product-category-filter, #product-search').on('keyup change',
                function() {
                    clearTimeout(searchTimeout);
                    const input = this;
                    searchTimeout = setTimeout(function() {
                        if (currentWarehouse) {
                            loadProducts();
                        }
                    }, 400); // 400ms delay
                });

            function renderAvailableProductsTable() {
                const tbody = $('#available-products-tbody');

                if (availableProducts.length === 0) {
                    tbody.html(`
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="icon-base ti tabler-search-off me-2"></i>
                                Tidak ada produk ditemukan dengan filter yang dipilih
                            </td>
                        </tr>
                    `);
                    return;
                }

                let rows = '';
                availableProducts.forEach(function(product) {
                    const existingProduct = selectedProducts.find(p => p.product_code === product.code);
                    const qtyTransfer = existingProduct ? existingProduct.adjustment_qty : 0;

                    // Ensure current_stock is a number
                    const currentStock = parseFloat(product.current_stock) || 0;
                    const sisaStok = currentStock - qtyTransfer;
                    const reason = existingProduct ? existingProduct.reason : '';

                    // Format stock: remove trailing zeros (0.500000 → 0.5), keep meaningful decimals (0.5 → 0.5)
                    const formattedStock = parseFloat(currentStock.toFixed(2));
                    const formattedSisaStok = parseFloat(sisaStok.toFixed(2));
                    
                    const cleanStock = formattedStock.toLocaleString('en-US');
                    const cleanSisaStok = formattedSisaStok.toLocaleString('en-US');
                    rows += `
                        <tr data-product-code="${product.code}" data-product-id="${product.id}">
                            <td><strong>${product.code}</strong></td>
                            <td>${product.name}</td>
                            <td>${product.unit}</td>
                            <td><span class="text-info">${cleanStock}</span></td>
                            <td>
                                <input type="number" class="form-control form-control-sm qty-transfer-input"
                                    value="${qtyTransfer}"
                                    data-product-code="${product.code}"
                                    data-product-id="${product.id}"
                                    data-product-name="${product.name}"
                                    data-unit="${product.unit}"
                                    data-current-stock="${currentStock}"
                                    data-unit-cost="${product.unit_cost}"
                                    min="0"
                                    max="${currentStock}"
                                    step="0.01"
                                    placeholder="0">
                            </td>
                            <td class="sisa-stok-display">
                                <span class="text-${sisaStok < 0 ? 'danger' : 'info'}">${cleanSisaStok}</span>
                            </td>
                            <td>
                                <input type="text" class="form-control form-control-sm reason-input"
                                    data-product-code="${product.code}"
                                    placeholder="Alasan transfer..."
                                    value="${reason}"
                                    maxlength="200">
                            </td>
                        </tr>
                    `;
                });

                tbody.html(rows);
            }


            // Handle qty transfer input changes
            $(document).on('input', '.qty-transfer-input', function() {
                const $input = $(this);
                const qtyTransfer = parseFloat($input.val()) || 0;
                const currentStock = parseFloat($input.data('current-stock')) || 0;
                const productCode = $input.data('product-code');
                const productId = $input.data('product-id');
                const productName = $input.data('product-name');
                const unit = $input.data('unit');
                const unitCost = $input.data('unit-cost');
                const row = $input.closest('tr');
                const sisaStok = currentStock - qtyTransfer;

                // Update sisa stok display: remove trailing zeros
                const formattedSisaStok = parseFloat(sisaStok.toFixed(2));
                row.find('.sisa-stok-display').html(
                    `<span class="text-${sisaStok < 0 ? 'danger' : 'info'}">${formattedSisaStok}</span>`
                );

                // Update or add to selectedProducts array
                const productIndex = selectedProducts.findIndex(p => p.product_code === productCode);
                if (productIndex !== -1) {
                    selectedProducts[productIndex].adjustment_qty = qtyTransfer;
                } else if (qtyTransfer > 0) {
                    // Add new product to selected list
                    selectedProducts.push({
                        product_id: productId,
                        product_code: productCode,
                        product_name: productName,
                        unit: unit,
                        current_stock: currentStock,
                        unit_cost: unitCost,
                        adjustment_qty: qtyTransfer,
                        reason: row.find('.reason-input').val() || ''
                    });
                }

                updateSummary();
            });

            // Update reason in selectedProducts array
            $(document).on('input', '.reason-input', function() {
                const reason = $(this).val();
                const productCode = $(this).data('product-code');

                const productIndex = selectedProducts.findIndex(p => p.product_code === productCode);
                if (productIndex !== -1) {
                    selectedProducts[productIndex].reason = reason;
                }
            });

            // Warehouse change confirmation
            $('#warehouse-select').on('change', function() {
                const newWarehouse = $(this).val();

                if (currentWarehouse && newWarehouse !== currentWarehouse && selectedProducts.length > 0) {
                    Swal.fire({
                        title: 'Konfirmasi Ganti Gudang',
                        text: `Mengganti gudang akan menghapus semua produk yang sudah dipilih (${selectedProducts.length} produk). Apakah Anda yakin?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Ganti Gudang',
                        cancelButtonText: 'Batal',
                        customClass: {
                            confirmButton: 'btn btn-warning',
                            cancelButton: 'btn btn-secondary'
                        },
                        buttonsStyling: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            clearAllProductData();
                        } else {
                            // Revert warehouse selection
                            $(this).val(currentWarehouse).trigger('change.select2');
                        }
                    });
                } else {
                    clearAllProductData();
                }
            });


            // Update summary cards
            function updateSummary() {
                let totalItems = 0;
                let totalQty = 0;
                let totalValue = 0;

                // Loop through all qty-transfer-input fields in the table
                $('#available-products-tbody .qty-transfer-input').each(function() {
                    const qtyTransfer = parseFloat($(this).val()) || 0;
                    const unitCost = parseFloat($(this).data('unit-cost')) || 0;

                    // Add to total qty (sum of all qty transfer inputs)
                    totalQty += qtyTransfer;

                    // Count items only if qty transfer > 0
                    if (qtyTransfer > 0) {
                        totalItems++;
                        totalValue += (qtyTransfer * unitCost);
                    }
                });

                // Update summary display (without thousand separators, with decimals)
                $('#total-items-summary').text(totalItems);

                // Update qty card styling based on positive/negative
                // const qtyCard = $('#total-qty-card');
                // if (totalQty > 0) {
                //     qtyCard.removeClass('danger').addClass('success');
                //     $('#total-qty-summary').text('+' + parseFloat(totalQty.toFixed(2)));
                // } else if (totalQty < 0) {
                //     qtyCard.removeClass('success').addClass('danger');
                //     $('#total-qty-summary').text(parseFloat(totalQty.toFixed(2)));
                // } else {
                //     qtyCard.removeClass('success danger');
                //     $('#total-qty-summary').text('0');
                // }

                // Format total qty: remove trailing zeros
                $('#total-qty-summary').text(parseFloat(totalQty.toFixed(2)));

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

            function clearAllProductData() {
                availableProducts = [];
                selectedProducts = [];
                currentWarehouse = null;

                // Clear available products table
                $('#available-products-tbody').html(`
            <tr>
                <td colspan="7" class="text-center text-muted py-5">
                    <i class="icon-base ti tabler-box-off me-2"></i>
                    Pilih gudang dan klik "Load Produk dari Gudang" untuk memuat data
                </td>
            </tr>
        `);

                updateSummary();
            }

            // Cancel button
            $('#cancel-btn').on('click', function() {
                window.location.href = "{{ route('main_transaction.stock_adjustment.index') }}";
            });

            // Submit button
            $('#submit-btn').on('click', function(e) {
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
                    adjustment_date: $('#adjustment-date').val(),
                    adjustment_type: $('#adjustment-type').val(),
                    warehouse_id: $('#warehouse-select').val(),
                    warehouse_dest_id: $('#warehouse-select-dest').val(),
                    pic_id: $('#pic-select').val(),
                    // status: $('#status-select').val(),
                    description: $('#description').val(),
                    memo: $('#adjustment-memo').val(),
                    tags: $('#adjustment-tags').val(),
                    form_type: $('#form-type').val(),
                    form_reference_id: $('#form-reference_id').val(),
                    products: []
                };

                if ($('#warehouse-select').val() === $('#warehouse-select-dest').val()) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: 'Gudang asal dan tujuan tidak boleh sama.',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    return;
                }

                // Collect products directly from table with qty_transfer > 0
                $('#available-products-tbody tr').each(function() {
                    const $row = $(this);
                    const qtyTransferInput = $row.find('.qty-transfer-input');

                    if (qtyTransferInput.length) {
                        const qtyTransfer = parseFloat(qtyTransferInput.val()) || 0;

                        // Only include products with qty_transfer > 0
                        if (qtyTransfer > 0) {
                            formData.products.push({
                                product_id: qtyTransferInput.data('product-id'),
                                product_code: qtyTransferInput.data('product-code'),
                                product_name: qtyTransferInput.data('product-name'),
                                unit: qtyTransferInput.data('unit'),
                                stock_before: qtyTransferInput.data('current-stock'),
                                adjustment_qty: qtyTransfer,
                                unit_cost: qtyTransferInput.data('unit-cost'),
                                reason: $row.find('.reason-input').val() || ''
                            });
                        }
                    }
                });

                // Show confirmation
                Swal.fire({
                    title: 'Konfirmasi Simpan',
                    text: `Apakah Anda yakin ingin menyimpan stock transfer dengan ${formData.products.length} produk?`,
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
                            url: "{{ route('master_data.warehouse.stock_transfer_save') }}",
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                ...formData
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Stock transfer berhasil disimpan.',
                                    customClass: {
                                        confirmButton: 'btn btn-success'
                                    },
                                    buttonsStyling: false
                                }).then(function() {
                                    window.location.href =
                                        "{{ route('master_data.warehouse.index') }}";
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
