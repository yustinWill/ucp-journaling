@extends('layouts.default')
@section('page_title')
    Tambah Packing Produk
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Tambah Packing Produk</h4>
        <form class="card-body" id="production-form" autocomplete="off" enctype="multipart/form-data">
            @csrf

            <!-- Informasi Dasar Transaksi Section -->
            <div class="section-header mt-12">
                <h6>Informasi Dasar Transaksi</h6>
                <hr class="mx-n6">
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tanggal Transaksi <span style="color: red;">*</span></label>
                    <input type="date" class="form-control global-single-date-picker" id="production-date"
                        name="production_date" value="{{ date('Y-m-d') }}" required />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nomor Transaksi <span style="color: red;">*</span></label>
                    <div class="row g-2">
                        <div class="col">
                            <input type="text" class="form-control" id="production-number" name="production_number"
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

            <!-- Informasi Produksi Section -->
            <div class="section-header mt-12">
                <h6>Informasi Produksi</h6>
                <hr class="mx-n6">
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Bundle Product <span style="color: red;">*</span></label>
                    <div class="row g-2">
                        <div class="col">
                            <select class="global-select2 form-select" id="bundle-product" name="bundle_product_id"
                                data-allow-clear="false" required>
                                <option value="">Pilih Bundle Product</option>
                                @foreach ($bundleProducts as $product)
                                    <option data-bundle-batch-qty="{{$product->bundle_batch_qty}}" value="{{ $product->id }}">{{ $product->code }} - {{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('master_data.product.add') }}" target="_blank"
                                class="btn btn-outline-primary d-flex align-items-center"
                                title="Tambah Bundle Product Baru">
                                <i class="icon-base ti tabler-plus"></i>
                            </a>
                        </div>
                    </div>
                    <small class="form-text text-muted">Pilih product dengan tipe bundle</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Gudang <span style="color: red;">*</span></label>
                    <div class="row g-2">
                        <div class="col">
                            <select class="global-select2 form-select" id="warehouse" name="warehouse_id"
                                data-allow-clear="false" required>
                                <option value="">Pilih Gudang</option>
                                @foreach ($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ $loop->first ? 'selected' : '' }}>{{ $warehouse->code }} - {{ $warehouse->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('master_data.warehouse.add') }}" target="_blank"
                                class="btn btn-outline-primary d-flex align-items-center" title="Tambah Gudang Baru">
                                <i class="icon-base ti tabler-plus"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Penanggung Jawab <span style="color: red;">*</span></label>
                    <div class="row g-2">
                        <div class="col">
                            <select class="global-select2 form-select" id="pic-user" name="pic_user_id"
                                data-allow-clear="false" required>
                                <option value="">Pilih PIC</option>
                                @foreach ($picUsers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('master_data.contact.add') }}" target="_blank"
                                class="btn btn-outline-primary d-flex align-items-center" title="Tambah Contact Baru">
                                <i class="icon-base ti tabler-plus"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Available Bundle Stock Section -->
            <div class="row g-3 mb-4" id="bundle-stock-container" style="display: none;">
                <div class="col-md-6">
                    <label class="form-label">Available Bundle Stock</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="available-bundle-qty" disabled value="0" />
                        <span class="input-group-text" id="bundle-unit-label">-</span>
                    </div>
                    <small class="form-text text-muted">Stock tersedia untuk bundle yang dipilih</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Remaining Bundle Stock</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="remaining-bundle-qty" disabled value="0" />
                        <span class="input-group-text" id="remaining-unit-label">-</span>
                    </div>
                    <small class="form-text text-muted">Sisa stock setelah packing</small>
                </div>
            </div>

            <!-- Packaging Products Section -->
            <div class="section-header mt-12">
                <h6>Produk Packaging</h6>
                <hr class="mx-n6">
            </div>
            <div id="packaging-disabled-message" class="alert alert-info">
                <i class="icon-base ti tabler-info-circle me-2"></i>
                Silakan pilih <strong>Bundle Product</strong> dan <strong>Gudang</strong> terlebih dahulu untuk melihat produk packaging yang tersedia.
            </div>

            <div id="packaging-products-container" style="display: none;">
                <!-- Packaging products will be dynamically inserted here -->
            </div>

            <!-- Informasi Tambahan Section -->
            <div class="section-header mt-12">
                <h6>Informasi Tambahan</h6>
                <hr class="mx-n6">
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Sisa Bundle Product</label>
                    <div class="input-group">
                        <input type="number" step="0.01" class="form-control" id="remaining-bundle-adjustment"
                            name="remaining_bundle_adjustment" value="0" placeholder="0">
                        <span class="input-group-text" id="adjustment-unit-label">-</span>
                    </div>
                    <small class="form-text text-muted">
                        Sisa bundle setelah packing selesai. Positif (+) untuk kelebihan, Negatif (-) untuk kekurangan.
                        Akan otomatis membuat stock adjustment.
                    </small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Alasan Penyesuaian Stock</label>
                    <textarea class="form-control" id="adjustment-reason" name="adjustment_reason" rows="2"
                        placeholder="Jelaskan alasan penyesuaian stock (jika ada)..." maxlength="500"></textarea>
                    <small class="form-text text-muted">Wajib diisi jika ada sisa bundle product</small>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Memo <small class="text-muted">(Internal)</small></label>
                    <textarea class="form-control" id="production-memo" name="memo" rows="3"
                        placeholder="Catatan internal untuk packing..." maxlength="500"></textarea>
                    <small class="form-text text-muted">Maksimal 500 karakter - Hanya untuk keperluan internal</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Deskripsi</label>
                    <textarea class="form-control" id="description" name="description" rows="3"
                        placeholder="Jelaskan detail dan tujuan packing..." maxlength="1000"></textarea>
                    <small class="form-text text-muted">Maksimal 1000 karakter</small>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Lampiran Dokumen</label>
                    <input type="file" class="form-control" id="attachment" name="attachment"
                        accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                    <small class="form-text text-muted">Format: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG (Max: 5MB)</small>
                </div>
                <div class="col-md-6">
                    <!-- Empty for alignment -->
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 text-end">
                <a href="{{ route('main_transaction.packing.index') }}" class="btn btn-label-danger me-3">Batalkan</a>
                <button type="button" class="btn btn-success" id="submit-btn">Simpan</button>
            </div>
        </form>
    </div>
@stop

@section('js_script')
    <script>
        $(function() {
            // Global state variables for packing
            let packagingProducts = [];
            let availableBundleQty = 0;
            let bundleUnitCode = '';

            $('.currency-input').each(function() {
                new Cleave(this, {
                    numeral: true,
                    numeralThousandsGroupStyle: 'thousand',
                    prefix: 'Rp ',
                    noImmediatePrefix: false,
                    rawValueTrimPrefix: true
                });
            });

            // Generate number button functionality
            $('#generate-number-btn').on('click', function() {
                $.ajax({
                    url: "{{ route('main_transaction.packing.generate_transaction_number') }}",
                    type: 'GET',
                    success: function(response) {
                        if (response.production_number) {
                            $('#production-number').val(response.production_number);
                        }
                    },
                    error: function() {
                        // Fallback to client-side generation
                        $('#production-number').val('PACK-' + new Date().getFullYear() + new Date().getMonth() + '-' +
                            String(Date.now()).substr(-4));
                    }
                });
            });

            // Auto-generate number on page load
            $('#generate-number-btn').click();

            // Bundle product + Warehouse change - load packaging products and bundle stock
            function loadPackingData() {
                const bundleProductId = $('#bundle-product').val();
                const warehouseId = $('#warehouse').val();

                if (!bundleProductId || !warehouseId) {
                    $('#packaging-disabled-message').show();
                    $('#packaging-section').hide();
                    $('#bundle-stock-container').hide();
                    $('#packaging-rows').empty();
                    packagingProducts = [];
                    return;
                }

                // Load bundle stock
                $.ajax({
                    url: "{{ route('main_transaction.packing.get_bundle_stock') }}",
                    type: 'GET',
                    data: {
                        bundle_product_id: bundleProductId,
                        warehouse_id: warehouseId
                    },
                    success: function(response) {
                        if (response.success) {
                            availableBundleQty = response.available_qty;
                            bundleUnitCode = response.unit_code;

                            $('#available-bundle-qty').val(formatNumber(availableBundleQty));
                            $('#bundle-unit-label').text(bundleUnitCode);
                            $('#remaining-unit-label').text(bundleUnitCode);
                            $('#adjustment-unit-label').text(bundleUnitCode);
                            $('#bundle-stock-container').show();

                            updateRemainingBundleQty();
                        }
                    },
                    error: function(xhr) {
                        console.error('Failed to load bundle stock:', xhr);
                    }
                });

                // Load packaging products
                $.ajax({
                    url: "{{ route('main_transaction.packing.get_packaging_products') }}",
                    type: 'GET',
                    data: {
                        bundle_product_id: bundleProductId,
                        warehouse_id: warehouseId
                    },
                    success: function(response) {
                        if (response.success) {
                            packagingProducts = response.data;
                            displayPackagingProducts(packagingProducts);
                            $('#packaging-disabled-message').hide();
                            $('#packaging-products-container').show();
                        }
                    },
                    error: function(xhr) {
                        console.error('Failed to load packaging products:', xhr);
                        packagingProducts = [];
                    }
                });
            }

            // Bundle product change
            $('#bundle-product').on('change', function() {
                loadPackingData();
            });

            // Warehouse change
            $('#warehouse').on('change', function() {
                loadPackingData();
            });

            // Display packaging products as individual sections with component tables
            function displayPackagingProducts(products) {
                let sectionsHtml = '';

                products.forEach((packaging, index) => {
                    // Build components table rows
                    let componentsTableHtml = '';

                    if (packaging.other_components && packaging.other_components.length > 0) {
                        packaging.other_components.forEach((comp, compIndex) => {
                            componentsTableHtml += `
                                <tr data-component-id="${comp.product_id}"
                                    data-qty-per-unit="${comp.qty_per_unit}"
                                    data-available-stock="${comp.available_stock || 0}">
                                    <td>${comp.product_code} - ${comp.product_name}</td>
                                    <td class="text-center">${comp.unit_code}</td>
                                    <td class="text-center">${formatNumber(comp.qty_per_unit)}</td>
                                    <td class="text-center">${formatNumber(comp.available_stock || 0)}</td>
                                    <td class="text-center component-qty-needed-${index}">0</td>
                                    <td class="text-center component-stock-status">
                                        <span class="badge bg-label-secondary">No Input</span>
                                    </td>
                                </tr>
                            `;
                        });
                    }

                    // Create section for this packaging product
                    sectionsHtml += `
                        <div class="packaging-product-section mb-6" data-packaging-index="${index}" data-packaging-id="${packaging.id}">
                            <!-- Packaging Product Header -->
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">${packaging.code} - ${packaging.name}</h6>
                                    <span class="badge bg-label-info">${packaging.unit_code}</span>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Qty to Produce <span style="color: red;">*</span></label>
                                            <input type="number"
                                                class="form-control packaging-qty-input"
                                                data-index="${index}"
                                                data-bundle-qty-needed="${packaging.bundle_qty_needed}"
                                                placeholder="0"
                                                value="0"
                                                min="0"
                                                step="1" />
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Bundle Qty Needed</label>
                                            <input type="text"
                                                class="form-control text-end"
                                                id="bundle-qty-needed-${index}"
                                                readonly
                                                value="0" />
                                            <small class="form-text text-muted">Qty per unit: ${formatNumber(packaging.bundle_qty_needed)}</small>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Stock Status</label>
                                            <div class="pt-2">
                                                <span class="component-overall-status-${index} badge bg-label-secondary">No Input</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Components Table -->
                            ${packaging.other_components && packaging.other_components.length > 0 ? `
                            <div class="section-header">
                                <h6 class="text-muted">Other Components</h6>
                                <hr class="mx-n6">
                            </div>
                            <div class="mx-n5 mb-4">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="width: 25%;">Component Product</th>
                                            <th style="width: 10%;">Unit</th>
                                            <th style="width: 15%;">Qty per Unit</th>
                                            <th style="width: 15%;">Stock Available</th>
                                            <th style="width: 15%;">Total Needed</th>
                                            <th style="width: 20%;">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="components-table-${index}">
                                        ${componentsTableHtml}
                                    </tbody>
                                </table>
                            </div>
                            ` : '<p class="text-muted fst-italic">No other components required</p>'}
                        </div>
                        ${index < products.length - 1 ? '<hr class="my-6" style="border-top: 2px solid #e0e0e0;">' : ''}
                    `;
                });

                $('#packaging-products-container').html(sectionsHtml);

                // Attach event listeners to qty inputs
                $('.packaging-qty-input').on('input', function() {
                    const index = $(this).data('index');
                    const qty = parseFloat($(this).val()) || 0;
                    const bundleQtyNeeded = parseFloat($(this).data('bundle-qty-needed'));
                    const packaging = packagingProducts[index];

                    // Update bundle qty needed display
                    $(`#bundle-qty-needed-${index}`).val(formatNumber(qty * bundleQtyNeeded));

                    // Update component quantities in the table
                    if (packaging && packaging.other_components) {
                        $(`.components-table-${index} tr`).each(function() {
                            const $row = $(this);
                            const qtyPerUnit = parseFloat($row.data('qty-per-unit'));
                            const totalNeeded = qty * qtyPerUnit;
                            const availableStock = parseFloat($row.data('available-stock'));

                            // Update total needed column
                            $row.find(`.component-qty-needed-${index}`).text(formatNumber(totalNeeded));

                            // Update status badge
                            const $statusCell = $row.find('.component-stock-status');
                            if (qty === 0) {
                                $statusCell.html('<span class="badge bg-label-secondary">No Input</span>');
                            } else if (totalNeeded > availableStock) {
                                $statusCell.html('<span class="badge bg-label-danger">Insufficient</span>');
                            } else {
                                $statusCell.html('<span class="badge bg-label-success">Available</span>');
                            }
                        });
                    }

                    // Update overall status for this packaging product
                    updatePackagingOverallStatus(index);

                    // Recalculate totals and check stock
                    updateRemainingBundleQty();
                    checkComponentStockOverlap();
                });
            }

            // Update overall status badge for a packaging product
            function updatePackagingOverallStatus(index) {
                const qty = parseFloat($(`.packaging-qty-input[data-index="${index}"]`).val()) || 0;
                const $statusBadge = $(`.component-overall-status-${index}`);

                if (qty === 0) {
                    $statusBadge.removeClass().addClass('badge bg-label-secondary component-overall-status-' + index).text('No Input');
                    return;
                }

                // Check if any component is insufficient
                let hasInsufficientStock = false;
                $(`.components-table-${index} .component-stock-status .badge`).each(function() {
                    if ($(this).hasClass('bg-label-danger')) {
                        hasInsufficientStock = true;
                    }
                });

                if (hasInsufficientStock) {
                    $statusBadge.removeClass().addClass('badge bg-label-danger component-overall-status-' + index).text('Insufficient Stock');
                } else {
                    $statusBadge.removeClass().addClass('badge bg-label-success component-overall-status-' + index).text('Stock OK');
                }
            }

            // Update remaining bundle qty
            function updateRemainingBundleQty() {
                let totalBundleNeeded = 0;

                $('.packaging-qty-input').each(function() {
                    const qty = parseFloat($(this).val()) || 0;
                    const bundleQtyNeeded = parseFloat($(this).data('bundle-qty-needed'));
                    totalBundleNeeded += (qty * bundleQtyNeeded);
                });

                const remaining = availableBundleQty - totalBundleNeeded;
                $('#remaining-bundle-qty').val(formatNumber(remaining));

                // Show warning if insufficient
                if (remaining < 0) {
                    $('#remaining-bundle-qty').addClass('is-invalid');
                } else {
                    $('#remaining-bundle-qty').removeClass('is-invalid');
                }
            }

            // Check component stock overlap across all packaging products
            function checkComponentStockOverlap() {
                const componentUsage = {};

                // Calculate total usage for each component across all packaging products
                $('.packaging-qty-input').each(function() {
                    const qty = parseFloat($(this).val()) || 0;
                    const packagingIndex = $(this).data('index');
                    const packaging = packagingProducts[packagingIndex];

                    if (packaging && packaging.other_components) {
                        packaging.other_components.forEach(comp => {
                            if (!componentUsage[comp.product_id]) {
                                componentUsage[comp.product_id] = {
                                    needed: 0,
                                    available: comp.available_stock || 0,
                                    name: comp.product_name
                                };
                            }
                            componentUsage[comp.product_id].needed += (qty * comp.qty_per_unit);
                        });
                    }
                });

                // Update component status based on overlap
                $('.packaging-product-section').each(function() {
                    const packagingIndex = $(this).data('packaging-index');
                    const packaging = packagingProducts[packagingIndex];
                    const qty = parseFloat($(`.packaging-qty-input[data-index="${packagingIndex}"]`).val()) || 0;

                    if (qty === 0) return;

                    // Update each component row in this packaging's table
                    $(`.components-table-${packagingIndex} tr`).each(function() {
                        const $row = $(this);
                        const componentId = $row.data('component-id');
                        const usage = componentUsage[componentId];
                        const $statusCell = $row.find('.component-stock-status');

                        if (usage && usage.needed > usage.available) {
                            $statusCell.html('<span class="badge bg-label-danger">Insufficient</span>');
                        } else if (qty > 0) {
                            $statusCell.html('<span class="badge bg-label-success">Available</span>');
                        }
                    });

                    // Update overall status
                    updatePackagingOverallStatus(packagingIndex);
                });

                console.log('Component Usage (Overlap Check):', componentUsage);
            }


            // Smart formatting - display natural numbers (no forced decimals)
            function formatNumber(num) {
                if (typeof num !== 'number') {
                    num = parseFloat(num);
                }
                if (isNaN(num)) return '0';

                // Use toIndonesianNumberFormat which already handles decimal formatting correctly
                return toIndonesianNumberFormat(num.toString());
            }

            // Format batch bundle qty - remove trailing zeros and unnecessary decimals
            function formatBatchBundleQty(num) {
                if (typeof num !== 'number') {
                    num = parseFloat(num);
                }
                if (isNaN(num)) return '0';

                // Check if it's an integer
                if (Number.isInteger(num)) {
                    return num.toString();
                }

                // For decimals, format with up to 4 decimal places, removing trailing zeros
                let formatted = num.toFixed(4);
                // Remove trailing zeros
                formatted = formatted.replace(/\.?0+$/, '');

                return formatted;
            }


            // Form submission
            $('#submit-btn').on('click', function(e) {
                e.preventDefault();

                // Validate required fields
                let isValid = true;
                const requiredFields = ['production-number', 'production-date', 'bundle-product',
                    'warehouse', 'pic-user'
                ];

                requiredFields.forEach(function(fieldId) {
                    const field = document.getElementById(fieldId);
                    if (!field.value.trim()) {
                        isValid = false;
                        field.classList.add('is-invalid');
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });

                if (!isValid) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Kesalahan Validasi',
                        text: 'Silakan lengkapi semua field yang wajib diisi.',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    return;
                }

                // Check if at least one packaging product has qty > 0
                let hasValidPackaging = false;
                $('.packaging-qty-input').each(function() {
                    if (parseFloat($(this).val()) > 0) {
                        hasValidPackaging = true;
                    }
                });

                if (!hasValidPackaging) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Tidak Ada Packaging',
                        text: 'Minimal satu packaging product harus memiliki qty > 0.',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    return;
                }

                // Check remaining bundle stock
                const remainingBundleQty = parseCurrency($('#remaining-bundle-qty').val());
                if (remainingBundleQty < 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Bundle Stock Tidak Cukup',
                        text: 'Total bundle yang dibutuhkan melebihi stock yang tersedia.',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    return;
                }

                // Validate adjustment reason if there's a remaining bundle adjustment
                const remainingAdjustment = parseFloat($('#remaining-bundle-adjustment').val()) || 0;
                if (remainingAdjustment !== 0) {
                    const adjustmentReason = $('#adjustment-reason').val().trim();
                    if (!adjustmentReason) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Alasan Penyesuaian Diperlukan',
                            text: 'Silakan isi alasan penyesuaian stock untuk sisa bundle product.',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        });
                        $('#adjustment-reason').focus();
                        return;
                    }
                }

                // Check component stock
                const hasInsufficientStock = $('#packaging-table .badge.bg-label-danger').length > 0;
                if (hasInsufficientStock) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Stock Komponen Tidak Cukup',
                        text: 'Beberapa komponen memiliki stock yang tidak mencukupi. Apakah Anda yakin ingin melanjutkan?',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Lanjutkan',
                        cancelButtonText: 'Batal',
                        customClass: {
                            confirmButton: 'btn btn-warning',
                            cancelButton: 'btn btn-secondary'
                        },
                        buttonsStyling: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            submitForm();
                        }
                    });
                    return;
                }

                Swal.fire({
                    title: 'Simpan Data Packing?',
                    text: 'Cek kembali data yang sudah diisi!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan!',
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-label-secondary'
                    },
                    buttonsStyling: false
                }).then(function(result) {
                    if (result.value) {
                        submitForm();
                    }
                });
            });

            function submitForm() {
                $('#submit-btn').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-2"></span>Loading...'
                );

                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('production_number', $('#production-number').val());
                formData.append('production_date', $('#production-date').val());
                formData.append('bundle_product_id', $('#bundle-product').val());
                formData.append('warehouse_id', $('#warehouse').val());
                formData.append('pic_id', $('#pic-user').val());
                formData.append('description', $('#description').val());
                formData.append('memo', $('#production-memo').val());
                formData.append('remaining_bundle_adjustment', $('#remaining-bundle-adjustment').val());
                formData.append('adjustment_reason', $('#adjustment-reason').val());

                // Collect packaging products data (only those with qty > 0)
                const packagingData = [];
                $('.packaging-qty-input').each(function() {
                    const qty = parseFloat($(this).val()) || 0;
                    if (qty > 0) {
                        const index = $(this).data('index');
                        const packaging = packagingProducts[index];

                        if (packaging) {
                            console.log('Packaging to submit:', packaging, 'Qty:', qty);
                            packagingData.push({
                                packaging_product_id: packaging.id,
                                packaging_product_code: packaging.code,
                                packaging_product_name: packaging.name,
                                quantity_packed: qty,
                                bundle_qty_needed: packaging.bundle_qty_needed,
                                other_components: packaging.other_components || []
                            });
                        }
                    }
                });
                formData.append('packaging_products', JSON.stringify(packagingData));

                // Handle file attachment
                const attachmentFile = $('#attachment')[0].files[0];
                if (attachmentFile) {
                    formData.append('attachment', attachmentFile);
                }

                $.ajax({
                    url: "{{ route('main_transaction.packing.store') }}",
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message || 'Data packing berhasil disimpan',
                                customClass: {
                                    confirmButton: 'btn btn-primary'
                                },
                                buttonsStyling: false
                            }).then(() => {
                                window.location.href = "{{ route('main_transaction.packing.index') }}";
                            });
                        }
                    },
                    error: function(xhr) {
                        let message = 'Terjadi kesalahan saat menyimpan data.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: message,
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
