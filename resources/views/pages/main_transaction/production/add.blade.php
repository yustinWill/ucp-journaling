@extends('layouts.default')
@section('page_title')
    Tambah Produksi
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Tambah Produksi</h4>
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
                        @if ($canAddProducts)
                            <div class="col-auto">
                                <a href="{{ route('master_data.product.add') }}" target="_blank"
                                    class="btn btn-outline-primary d-flex align-items-center"
                                    title="Tambah Bundle Product Baru">
                                    <i class="icon-base ti tabler-plus"></i>
                                </a>
                            </div>
                        @endif
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
                    <label class="form-label">Mode Produksi <span style="color: red;">*</span></label>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="production-mode-toggle" checked />
                        <label class="form-check-label" for="production-mode-toggle">
                            <span id="production-mode-label">Mode: Material-Based</span>
                        </label>
                    </div>
                    <small class="form-text text-muted" id="production-mode-description">
                        Input material yang tersedia, sistem akan hitung maksimal produksi
                    </small>
                </div>
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
            </div>

            <!-- Batch Bundle Qty + Max Producible (Material-Based Mode Only) -->
            <div class="row g-3 mb-4" id="material-based-container">
                <div class="col-md-6">
                    <label class="form-label">Batch Bundle Qty <span style="color: red;">*</span></label>
                    <input type="number" class="form-control" id="batch-bundle-qty"
                        placeholder="0" value="1" min="0" step="0.0001" disabled />
                    <small class="form-text text-muted">Berapa batch yang akan diproduksi (bisa desimal)</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Maximum Producible Quantity</label>
                    <input type="text" class="form-control" id="calculated-quantity" disabled
                        value="0" />
                    <small class="form-text text-muted">Jumlah maksimal yang bisa diproduksi dari material yang tersedia</small>
                </div>
            </div>

            <!-- Quantity Planned (Always Visible) -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Quantity Planned <span style="color: red;">*</span></label>
                    <input type="number" class="form-control" id="quantity-planned" name="quantity_planned"
                        placeholder="0" min="1" step="1" required disabled />
                    <small class="form-text text-muted" id="quantity-planned-note"><span style="color: #ff6b6b;">Dihitung otomatis dari material input</span></small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Biaya Produksi <span class="text-muted">(Opsional)</span></label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" class="form-control global-currency" id="biaya-produksi" name="biaya_produksi"
                            placeholder="0" value="0" />
                    </div>
                    <small class="form-text text-muted">Biaya tambahan produksi (akan ditambahkan ke HPP)</small>
                </div>
            </div>

            <!-- Bundle Components Section -->
            <div class="section-header mt-12">
                <h6>Komponen Bundle</h6>
            </div>
            <div id="components-disabled-message" class="alert alert-info">
                <i class="icon-base ti tabler-info-circle me-2"></i>
                Silakan pilih <strong>Bundle Product</strong> terlebih dahulu untuk melihat komponen yang diperlukan.
            </div>

            <!-- Mode 1: Quantity Planned (Preview Only) -->
            <div id="components-section-preview" style="display: none;">
                <div class="mx-n5">
                    <table class="table table-bordered" id="components-table-preview">
                        <thead>
                            <tr>
                                <th style="width: 30%;">Komponen Product</th>
                                <th style="width: 10%;">Satuan</th>
                                <th style="width: 15%;">Qty per Bundle</th>
                                <th style="width: 15%;">Total Required</th>
                                <th style="width: 15%;">Stock Available</th>
                                <th style="width: 15%;">HPP</th>
                                <th style="width: 15%;">Status</th>
                            </tr>
                        </thead>
                        <tbody id="components-rows-preview">
                            <!-- Rows will be populated dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mode 2: Material-Based (Editable Input) -->
            <div id="components-section-input" style="display: none;">
                <div class="mx-n5">
                    <table class="table table-bordered" id="components-table-input">
                        <thead>
                            <tr>
                                <th style="width: 20%;">Komponen Product</th>
                                <th style="width: 10%;">Satuan</th>
                                <th style="width: 13%;">Qty per Bundle</th>
                                <th style="width: 15%;">Stock Available</th>
                                <th style="width: 5%;">Harga Satuan</th>
                                <th style="width: 15%;">Material Input <span style="color: red;">*</span></th>
                                <th style="width: 12%;">Leftover Material</th>
                                <th style="width: 10%;">Status</th>
                            </tr>
                        </thead>
                        <tbody id="components-rows-input">
                            <!-- Rows will be populated dynamically -->
                        </tbody>
                    </table>
                </div>
                <div class="alert alert-warning mt-3">
                    <i class="icon-base ti tabler-info-circle me-2"></i>
                    <strong>Catatan:</strong> Sistem akan menghitung jumlah produksi maksimal berdasarkan material yang Anda input.
                    Sisa material akan ditampilkan dengan presisi 4 desimal untuk proses stock opname selanjutnya.
                </div>
            </div>

            <!-- Biaya Produk Section -->
            <div class="section-header mt-12">
                <h6>Biaya Produksi</h6>
                <hr class="mx-n6">
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Biaya Bundle</label>
                    <input type="text" class="form-control currency-input text-end" id="bundle-price" name="bundle_price" disabled placeholder="Rp 0" required/>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Biaya Perunit</label>
                    <input type="text" class="form-control currency-input text-end" id="bundle-price-unit" name="bundle_price_unit" disabled
                        value="0" />
                </div>
            </div>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Total Biaya Komponen Perunit</label>
                    <input type="text" class="form-control currency-input text-end" id="total-component-cost-unit" name="total_component_cost_unit" disabled value="0" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">HPP Sementara</label>
                    <input type="text" class="form-control currency-input text-end" id="hpp-production"  disabled value="0" />
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
                    <textarea class="form-control" id="production-memo" name="memo" rows="3"
                        placeholder="Catatan internal untuk produksi..." maxlength="500"></textarea>
                    <small class="form-text text-muted">Maksimal 500 karakter - Hanya untuk keperluan internal</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Deskripsi</label>
                    <textarea class="form-control" id="description" name="description" rows="3"
                        placeholder="Jelaskan detail dan tujuan produksi..." maxlength="1000"></textarea>
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
                <a href="{{ route('main_transaction.production.index') }}" class="btn btn-label-danger me-3">Batalkan</a>
                <button type="button" class="btn btn-success" id="submit-btn">Simpan</button>
            </div>
        </form>
    </div>
@stop

@section('js_script')
    <script>
        $(function() {
            // Global state variables
            let currentProductionMode = 'MATERIAL_BASED'; // QUANTITY_PLANNED or MATERIAL_BASED
            let bundleComponents = [];
            let productBundleBatchQty = 0; // Store the product's default bundle_batch_qty
            $('.currency-input').each(function() {
                new Cleave(this, {
                    numeral: true,
                    numeralThousandsGroupStyle: 'thousand',
                    prefix: 'Rp ',
                    noImmediatePrefix: false,
                    rawValueTrimPrefix: true
                });
            });
            function getBundleCost() {
                const bundleProductId = $('#bundle-product').val();
                const quantityPlanned = parseFloat($('#quantity-planned').val()) || 1;

                $.ajax({
                    url: "{{ route('main_transaction.production.get_bundle_price') }}",
                    type: 'GET',
                    data: {
                        bundle_product_id: bundleProductId
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#bundle-price').val('Rp ' + formatNumber(response.bundle_price));

                            const bundleUnitPrice = response.bundle_price / quantityPlanned;
                            $('#bundle-price-unit').val('Rp ' + formatNumber(bundleUnitPrice));

                            // Recalculate production costs after updating bundle price
                            calculateProductionCosts();

                            console.log('✅ Bundle cost updated:', {
                                bundlePrice: response.bundle_price,
                                bundleUnitPrice: bundleUnitPrice,
                                quantityPlanned: quantityPlanned
                            });
                        }
                    },
                    error: function(xhr) {
                        console.error('Failed to calculate bundle cost:', xhr);
                    }
                });
            }
            function updateFIFOPrices() {
                const quantityPlanned = parseFloat($('#quantity-planned').val()) || 0;
                const bundleProductId = $('#bundle-product').val();
                
                if (quantityPlanned <= 0 || !bundleProductId) {
                    return;
                }

                // Collect all product IDs and their required quantities
                const productRequirements = [];
                
                if (currentProductionMode === 'QUANTITY_PLANNED') {
                    $('#components-rows-preview tr').each(function() {
                        const $row = $(this);
                        const productId = $row.find('td:first').data('product-id');
                        const qtyPerBundle = parseFloat($row.find('.total-required').data('qty-per-bundle'));
                        const totalRequired = qtyPerBundle * quantityPlanned;
                        
                        productRequirements.push({
                            product_id: productId,
                            quantity_required: totalRequired
                        });
                    });
                } else {
                    // Material-Based mode: Use actual material input from user
                    $('#components-rows-input tr').each(function() {
                        const $row = $(this);
                        const productId = $row.data('product-id');
                        const qtyPerBundle = parseFloat($row.data('qty-per-bundle'));
                        const materialInputRaw = $row.find('.material-input').val();
                        let totalRequired;

                        // If material input is provided, use it; otherwise calculate from qty per bundle
                        if (materialInputRaw && materialInputRaw.trim() !== '') {
                            totalRequired = parseCurrency(materialInputRaw);
                        } else {
                            totalRequired = qtyPerBundle * quantityPlanned;
                        }

                        productRequirements.push({
                            product_id: productId,
                            quantity_required: totalRequired
                        });
                    });
                }

                // COMMENTED OUT: AJAX call to calculate_fifo_prices - now using purchase_price from get_bundle_components
                // $.ajax({
                //     url: "{{ route('main_transaction.production.calculate_fifo_prices') }}",
                //     type: 'GET',
                //     data: {
                //         bundle_product_id: bundleProductId,
                //         quantity_planned: quantityPlanned,
                //         product_requirements: JSON.stringify(productRequirements)
                //     },
                //     success: function(response) {
                //         if (response.success) {
                //             // Update prices in the preview table
                //             if (currentProductionMode === 'QUANTITY_PLANNED') {
                //                 $('#components-rows-preview tr').each(function() {
                //                     const $row = $(this);
                //                     const productId = $row.find('td:first').data('product-id');
                //                     const priceData = response.prices.find(p => p.product_id == productId);
                //
                //                     if (priceData) {
                //                         const $priceCell = $row.find('.purchase-price');
                //                         $priceCell.data('price-per-unit', priceData.price_per_pcs);
                //                         $priceCell.text('Rp. ' + formatNumber(priceData.price_per_pcs));
                //                     }
                //                 });
                //             } else {
                //                 // Update prices in the input table
                //                 $('#components-rows-input tr').each(function() {
                //                     const $row = $(this);
                //                     const productId = $row.data('product-id');
                //                     const priceData = response.prices.find(p => p.product_id == productId);
                //
                //                     if (priceData) {
                //                         $row.data('price-per-unit', priceData.price_per_pcs);
                //                         $row.find('.purchase-price').text('Rp. ' + formatNumber(priceData.price_per_pcs));
                //                     }
                //                 });
                //             }
                //
                //             // Recalculate production costs after updating prices
                //             calculateProductionCosts();
                //
                //             console.log('✅ FIFO Prices Updated:', response.prices);
                //         }
                //     },
                //     error: function(xhr) {
                //         console.error('Failed to calculate FIFO prices:', xhr);
                //     }
                // });

                // Use purchase_price from get_bundle_components and recalculate costs
                calculateProductionCosts();
            }

            // Calculate production costs based on component prices
            function calculateProductionCosts() {
                const quantityPlanned = parseFloat($('#quantity-planned').val()) || 0;

                if (quantityPlanned <= 0) {
                    $('#total-component-cost-unit').val('Rp 0');
                    $('#hpp-production').val('Rp 0');
                    return;
                }

                let totalComponentCost = 0;

                // Sum up all purchase prices from the visible component table
                $('.purchase-price').each(function() {
                    const pricePerUnit = parseFloat($(this).data('price-per-unit')) || 0;
                    const $row = $(this).closest('tr');

                    if (currentProductionMode === 'QUANTITY_PLANNED') {
                        // In preview mode, use total required quantity
                        const qtyPerBundle = parseFloat($row.find('.total-required').data('qty-per-bundle')) || 0;
                        const totalRequired = qtyPerBundle * quantityPlanned;
                        totalComponentCost += pricePerUnit * totalRequired;
                    } else {
                        // In material-based mode, use actual material input
                        const materialInput = parseCurrency($row.find('.material-input').val()) || 0;
                        totalComponentCost += pricePerUnit * materialInput;
                    }
                });

                // Calculate per-unit costs
                const totalComponentCostPerUnit = totalComponentCost / quantityPlanned;

                // Get bundle production cost (additional costs like labor, overhead, etc.)
                const bundlePriceText = $('#bundle-price').val() || 'Rp 0';
                const bundlePrice = parseCurrency(bundlePriceText);
                const bundlePricePerUnit = bundlePrice / quantityPlanned;

                // Get biaya produksi
                const biayaProduksiText = $('#biaya-produksi').val() || '0';
                const biayaProduksi = parseCurrency(biayaProduksiText);
                const biayaProduksiPerUnit = biayaProduksi / quantityPlanned;

                // HPP = Component costs per unit + Bundle production cost per unit + Biaya Produksi per unit
                const hppPerUnit = totalComponentCostPerUnit + bundlePricePerUnit + biayaProduksiPerUnit;

                // Update the fields
                $('#bundle-price-unit').val('Rp ' + formatNumber(bundlePricePerUnit));
                $('#total-component-cost-unit').val('Rp ' + formatNumber(totalComponentCostPerUnit));
                $('#hpp-production').val('Rp ' + formatNumber(hppPerUnit));

                console.log('💰 Production Costs Calculated:', {
                    totalComponentCost: totalComponentCost,
                    totalComponentCostPerUnit: totalComponentCostPerUnit,
                    bundlePricePerUnit: bundlePricePerUnit,
                    biayaProduksi: biayaProduksi,
                    biayaProduksiPerUnit: biayaProduksiPerUnit,
                    hppPerUnit: hppPerUnit,
                    quantityPlanned: quantityPlanned
                });
            }

            // Generate number button functionality
            $('#generate-number-btn').on('click', function() {
                $.ajax({
                    url: "{{ route('main_transaction.production.generate_transaction_number') }}",
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            $('#production-number').val(response.transaction_number);
                        }
                    },
                    error: function() {
                        // Fallback to client-side generation
                        $('#production-number').val('PROD-' + new Date().getFullYear() + '-' +
                            String(Date.now()).substr(-3));
                    }
                });
            });

            // Auto-generate number on page load
            $('#generate-number-btn').click();

            // Production mode toggle
            $('#production-mode-toggle').on('change', function() {
                const isChecked = $(this).is(':checked');

                if (isChecked) {
                    // Switch to Material-Based mode
                    currentProductionMode = 'MATERIAL_BASED';
                    $('#production-mode-label').text('Mode: Material-Based Input');
                    $('#production-mode-description').text('Input material yang tersedia, sistem akan hitung jumlah produksi maksimal');

                    // Show Material-Based container
                    $('#material-based-container').show();

                    // Disable quantity planned and update note
                    $('#quantity-planned').prop('disabled', true).val('');
                    $('#quantity-planned-note').html('<span style="color: #ff6b6b;">Dihitung otomatis dari material input</span>');

                    // Switch component tables
                    $('#components-section-preview').hide();
                    $('#components-section-input').show();

                } else {
                    // Switch to Quantity Planned mode
                    currentProductionMode = 'QUANTITY_PLANNED';
                    $('#production-mode-label').text('Mode: Quantity Planned (Default)');
                    $('#production-mode-description').text('Tentukan jumlah produksi, sistem akan hitung kebutuhan material');

                    // Hide Material-Based container
                    $('#material-based-container').hide();

                    // Enable quantity planned and update note
                    $('#quantity-planned').prop('disabled', false);
                    $('#quantity-planned-note').text('Masukkan jumlah yang direncanakan untuk diproduksi');

                    // Switch component tables
                    $('#components-section-preview').show();
                    $('#components-section-input').hide();
                }

                // Reload components if bundle product already selected
                const bundleProductId = $('#bundle-product').val();
                const warehouseId = $('#warehouse').val();
                if (bundleProductId && warehouseId) {
                    loadBundleComponents(bundleProductId, warehouseId);
                }
            });

            // Bundle product change - populate batch qty and update calculated quantity
            $('#bundle-product').on('change', function() {
                const bundleProductId = $('#bundle-product').val();
                const warehouseId = $('#warehouse').val();

                // Get batch-bundle-qty from selected option's data attribute
                const selectedOption = $(this).find('option:selected');
                productBundleBatchQty = parseFloat(selectedOption.data('bundle-batch-qty')) || 0;
                console.log(productBundleBatchQty);

                // Reset batch-multiplier field to 1 (for Quantity Planned mode)
                $('#batch-multiplier').val(1);

                // Update quantity-planned with default value (1 * productBundleBatchQty)
                $('#quantity-planned').val(productBundleBatchQty);
                getBundleCost();

                if (bundleProductId && warehouseId) {
                    loadBundleComponents(bundleProductId, warehouseId);
                    $('#components-disabled-message').hide();

                    if (currentProductionMode === 'QUANTITY_PLANNED') {
                        $('#components-section-preview').show();
                    } else {
                        $('#components-section-input').show();
                    }
                } else {
                    $('#components-disabled-message').show();
                    $('#components-section-preview').hide();
                    $('#components-section-input').hide();
                    $('#components-rows-preview').empty();
                    $('#components-rows-input').empty();
                    bundleComponents = [];
                }
            });

            // Warehouse change - only load components
            $('#warehouse').on('change', function() {
                const bundleProductId = $('#bundle-product').val();
                const warehouseId = $('#warehouse').val();

                if (bundleProductId && warehouseId) {
                    loadBundleComponents(bundleProductId, warehouseId);
                    $('#components-disabled-message').hide();

                    if (currentProductionMode === 'QUANTITY_PLANNED') {
                        $('#components-section-preview').show();
                    } else {
                        $('#components-section-input').show();
                    }
                } else {
                    $('#components-disabled-message').show();
                    $('#components-section-preview').hide();
                    $('#components-section-input').hide();
                    $('#components-rows-preview').empty();
                    $('#components-rows-input').empty();
                    bundleComponents = [];
                }
            });

            // Batch multiplier change - multiply by product default and update quantity planned
            $('#batch-multiplier').on('change input', function() {
                const batchMultiplier = parseFloat($(this).val()) || 0;

                // Calculate quantity planned: batch multiplier * product's default bundle_batch_qty
                const calculatedQtyPlanned = batchMultiplier * productBundleBatchQty;

                $('#quantity-planned').val(calculatedQtyPlanned);

                // Trigger quantity planned updates
                if (currentProductionMode === 'QUANTITY_PLANNED') {
                    updateTotalRequiredQuantities();
                }
                updateFIFOPrices();
                getBundleCost();
                calculateProductionCosts();
            });

            // Batch bundle qty change (Material-Based mode) - update quantity planned
            $('#batch-bundle-qty').on('change input', function() {
                if (currentProductionMode === 'MATERIAL_BASED') {
                    const batchBundleQty = parseFloat($(this).val()) || 0;

                    // Calculate quantity planned: batch_bundle_qty * productBundleBatchQty
                    const quantityPlanned = batchBundleQty * productBundleBatchQty;

                    $('#quantity-planned').val(Math.round(quantityPlanned));

                    // Recalculate material requirements display
                    calculateMaterialRequirements();
                }
            });

            // Quantity planned change
            $('#quantity-planned').on('input', function() {
                if (currentProductionMode === 'QUANTITY_PLANNED') {
                    updateTotalRequiredQuantities();
                }
                // Update FIFO prices when quantity changes
                updateFIFOPrices();
                getBundleCost();
                // Calculate production costs
                calculateProductionCosts();
            });

            function loadBundleComponents(bundleProductId, warehouseId) {
                // Load bundle components via AJAX
                $.ajax({
                    url: "{{ route('main_transaction.production.get_bundle_components') }}",
                    type: 'GET',
                    data: {
                        bundle_product_id: bundleProductId,
                        warehouse_id: warehouseId
                    },
                    success: function(response) {
                        if (response.success) {
                            bundleComponents = response.components;
                            if (currentProductionMode === 'QUANTITY_PLANNED') {
                                displayComponentsPreview(bundleComponents);
                            } else {
                                displayComponentsInput(bundleComponents);
                            }
                            updateFIFOPrices();
                        } else {
                            bundleComponents = [];
                        }
                    },
                    error: function() {
                        bundleComponents = [];
                    }
                });
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

            // Mode 1: Display components in preview mode (quantity planned)
            function displayComponentsPreview(components) {
                let componentsHtml = '';
                components.forEach(component => {
                    const qtyPlanned = $('#quantity-planned').val() || 0;
                    const totalRequired = component.quantity_required * qtyPlanned;
                    const isAvailable = totalRequired <= component.stock_available;

                    componentsHtml += `
                <tr>
                    <td data-product-id="${component.id}">${component.product_name}</td>
                    <td class="text-center">${component.unit}</td>
                    <td class="text-center">${formatNumber(component.quantity_required)}</td>
                    <td class="text-center total-required" data-stock-available="${component.stock_available}" data-qty-per-bundle="${component.quantity_required}">${formatNumber(totalRequired)}</td>
                    <td class="text-center">${formatNumber(component.stock_available)}</td>
                    <td class="text-center purchase-price" data-price-per-unit="${component.purchase_price}">Rp. ${formatNumber(component.purchase_price)}</td>
                    <td class="text-center">
                        ${isAvailable
                            ? '<span class="badge bg-label-success">Available</span>'
                            : '<span class="badge bg-label-danger">Insufficient Stock</span>'
                        }
                    </td>
                </tr>
            `;
                });

                $('#components-rows-preview').html(componentsHtml);
            }

            // Mode 2: Display components in input mode (material-based)
            function displayComponentsInput(components) {
                let componentsHtml = '';
                components.forEach((component, index) => {
                    componentsHtml += `
                <tr data-component-index="${index}"
                    data-product-id="${component.id}"
                    data-qty-per-bundle="${component.quantity_required}"
                    data-stock-available="${component.stock_available}"
                    data-price-per-unit="${component.purchase_price}">
                    <td>${component.product_name}</td>
                    <td class="text-center">${component.unit}</td>
                    <td class="text-center" >${formatNumber(component.quantity_required)}</td>
                    <td class="text-center stock-available" >${formatNumber(component.stock_available)}</td>
                    <td class="text-center purchase-price" data-price-per-unit="${component.purchase_price}">Rp. ${formatNumber(component.purchase_price)}</td>
                    <td>
                        <input type="text"
                            class="form-control numeric-input material-input"
                            data-index="${index}"
                            placeholder="0"
                            value="" />
                    </td>
                    <td class="text-center leftover-material">-</td>
                    <td class="text-center material-status">
                        <span class="badge bg-label-secondary">Waiting Input</span>
                    </td>
                </tr>
            `;
                });

                $('#components-rows-input').html(componentsHtml);

                // Initialize Cleave.js for numeric inputs with 4 decimals
                initCleaveCurrency($('#components-rows-input'));

                // Attach event listeners to material inputs
                $('.material-input').on('input', function() {
                    calculateMaximumProduction();
                    calculateProductionCosts();
                    updateFIFOPrices();
                });
            }

            // Biaya Produksi change - recalculate HPP
            $('#biaya-produksi').on('input', function() {
                calculateProductionCosts();
            });

            function updateTotalRequiredQuantities() {
                const qtyPlanned = $('#quantity-planned').val() || 0;

                $('#components-rows-preview .total-required').each(function() {
                    const qtyPerBundle = $(this).data('qty-per-bundle');
                    const totalRequired = qtyPerBundle * qtyPlanned;

                    $(this).text(formatNumber(totalRequired));

                    // Update status based on availability
                    const row = $(this).closest('tr');
                    const stockAvailable = $(this).data('stock-available');
                    const statusCell = row.find('td').eq(6);

                    if (totalRequired <= stockAvailable) {
                        statusCell.html('<span class="badge bg-label-success">Available</span>');
                    } else {
                        statusCell.html('<span class="badge bg-label-danger">Insufficient Stock</span>');
                    }
                });
            }

            // Calculate material requirements and validate against inputs (Material-Based mode)
            function calculateMaterialRequirements() {
                if (currentProductionMode !== 'MATERIAL_BASED') return;

                const batchBundleQty = parseFloat($('#batch-bundle-qty').val()) || 0;
                let maxProducible = Infinity;

                // Calculate required materials and check if inputs are sufficient
                $('#components-rows-input tr').each(function() {
                    const $row = $(this);
                    const qtyPerBundle = parseFloat($row.data('qty-per-bundle'));
                    const materialInputRaw = $row.find('.material-input').val();
                    const materialInput = parseCurrency(materialInputRaw) || 0;
                    const stockAvailable = parseFloat($row.data('stock-available'));

                    // Calculate required material for the planned batch qty
                    const requiredMaterial = batchBundleQty * qtyPerBundle;
                    const componentIndex = $row.data('component-index');

                    // Calculate how much can actually be produced from available input
                    const possibleBatches = qtyPerBundle > 0 ? (materialInput / qtyPerBundle) : 0;
                    if (possibleBatches < maxProducible) {
                        maxProducible = possibleBatches;
                    }

                    // Update status based on input vs requirement
                    const $statusCell = $row.find('.material-status');

                    if (materialInput === 0) {
                        $statusCell.html('<span class="badge bg-label-secondary">Waiting Input</span>');
                        $row.find('.leftover-material').text('-');
                    } else if (materialInput > stockAvailable) {
                        $statusCell.html('<span class="badge bg-label-danger">Exceeds Stock</span>');
                        $row.find('.leftover-material').text('-');
                    } else if (materialInput < requiredMaterial) {
                        $statusCell.html('<span class="badge bg-label-warning">Insufficient for Plan</span>');
                        const leftover = 0; // All will be used but still not enough
                        $row.find('.leftover-material').text(formatNumber(leftover));
                    } else {
                        // Sufficient material
                        const leftover = materialInput - requiredMaterial;
                        $row.find('.leftover-material').text(formatNumber(leftover));

                        if (Math.abs(leftover) < 0.0001) {
                            $statusCell.html('<span class="badge bg-label-success">Exact Match</span>');
                        } else {
                            $statusCell.html('<span class="badge bg-label-success">Sufficient</span>');
                        }
                    }
                });

                // Update max producible quantity
                $('#calculated-quantity').val(maxProducible !== Infinity ? (maxProducible * productBundleBatchQty).toFixed(0) : '0');
            }

            // Calculate maximum production based on material inputs
            function calculateMaximumProduction() {
                let minBatchRatio = Infinity;
                let allInputsFilled = true;
                let bottleneckComponent = '';

                // First pass: Find bottleneck batch ratio
                $('#components-rows-input tr').each(function() {
                    const $row = $(this);
                    const qtyPerBundle = parseFloat($row.data('qty-per-bundle'));
                    const materialInputRaw = $row.find('.material-input').val();
                    const materialInput = parseCurrency(materialInputRaw) || 0;
                    const componentIndex = $row.data('component-index');
                    const stockAvailable = parseFloat($row.data('stock-available'));
                    const productName = bundleComponents[componentIndex].product_name;

                    if (materialInput === 0) {
                        allInputsFilled = false;
                    }

                    // Calculate batch ratio: material_input / qty_per_bundle
                    // This tells us how many "batches worth" of this component we have
                    const batchRatio = qtyPerBundle > 0 ? (materialInput / qtyPerBundle) : 0;

                    // Find the minimum batch ratio (bottleneck)
                    if (batchRatio < minBatchRatio && materialInput > 0) {
                        minBatchRatio = batchRatio;
                        bottleneckComponent = productName;
                    }
                });

                // Second pass: Update display and status based on bottleneck batch ratio
                $('#components-rows-input tr').each(function() {
                    const $row = $(this);
                    const qtyPerBundle = parseFloat($row.data('qty-per-bundle'));
                    const materialInputRaw = $row.find('.material-input').val();
                    const materialInput = parseCurrency(materialInputRaw) || 0;
                    const componentIndex = $row.data('component-index');
                    const stockAvailable = parseFloat($row.data('stock-available'));

                    // Update status
                    const $statusCell = $row.find('.material-status');

                    if (materialInput === 0) {
                        $statusCell.html('<span class="badge bg-label-secondary">Waiting Input</span>');
                        $row.find('.leftover-material').text('-');
                    } else if (materialInput > stockAvailable) {
                        $statusCell.html('<span class="badge bg-label-danger">Exceeds Stock</span>');
                        $row.find('.leftover-material').text('-');
                    } else {
                        // Material is valid (less than or equal to stock)
                        if (minBatchRatio !== Infinity && allInputsFilled) {
                            // Calculate used material based on bottleneck batch ratio
                            const usedMaterial = minBatchRatio * qtyPerBundle;
                            const leftover = materialInput - usedMaterial;

                            // Format leftover with 4 decimals
                            $row.find('.leftover-material').text(formatNumber(leftover));

                            // Update status based on leftover
                            if (Math.abs(leftover) < 0.0001) { // Account for floating point precision
                                $statusCell.html('<span class="badge bg-label-success">Exact Match</span>');
                            } else if (leftover > 0) {
                                $statusCell.html('<span class="badge bg-label-warning">Has Leftover</span>');
                            }
                        } else {
                            $row.find('.leftover-material').text('-');
                            $statusCell.html('<span class="badge bg-label-info">Calculating...</span>');
                        }
                    }
                });

                // Update fields based on batch ratio
                if (minBatchRatio === Infinity || !allInputsFilled) {
                    $('#batch-bundle-qty').val('0');
                    $('#calculated-quantity').val('0');
                    $('#quantity-planned').val('0');
                } else {
                    // batch-bundle-qty is the bottleneck batch ratio
                    $('#batch-bundle-qty').val(formatBatchBundleQty(minBatchRatio));

                    // quantity_planned = batch_ratio × productBundleBatchQty
                    const quantityPlanned = minBatchRatio * productBundleBatchQty;
                    $('#quantity-planned').val(Math.round(quantityPlanned));
                    $('#calculated-quantity').val(Math.round(quantityPlanned));

                    // Console log for debugging
                    console.log('✅ Material-Based Calculation:', {
                        batchBundleQty: minBatchRatio,
                        quantityPlanned: quantityPlanned,
                        productBundleBatchQty: productBundleBatchQty,
                        bottleneck: bottleneckComponent,
                        mode: 'MATERIAL_BASED'
                    });
                }
            }

            // Form submission
            $('#submit-btn').on('click', function(e) {
                e.preventDefault();

                // Validate required fields
                let isValid = true;
                const requiredFields = ['production-number', 'production-date', 'bundle-product',
                    'warehouse', 'pic-user'
                ];

                // Add quantity-planned validation only for QUANTITY_PLANNED mode
                if (currentProductionMode === 'QUANTITY_PLANNED') {
                    requiredFields.push('quantity-planned');
                }

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

                // Validate material inputs for MATERIAL_BASED mode
                if (currentProductionMode === 'MATERIAL_BASED') {
                    const allMaterialInputs = $('.material-input').length;
                    const filledInputs = $('.material-input').filter(function() {
                        return parseCurrency($(this).val()) > 0;
                    }).length;

                    if (filledInputs === 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Material Belum Diisi',
                            text: 'Silakan input material untuk setiap komponen.',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        });
                        return;
                    }

                    const calculatedQty = parseInt($('#calculated-quantity').val());
                    if (calculatedQty === 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Tidak Bisa Produksi',
                            text: 'Material yang diinput tidak cukup untuk memproduksi minimal 1 bundle.',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        });
                        return;
                    }

                    // Check if any material exceeds stock
                    const hasExceedingStock = $('#components-rows-input .badge.bg-label-danger').length > 0;
                    if (hasExceedingStock) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Material Melebihi Stock',
                            text: 'Beberapa material input melebihi stock yang tersedia.',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        });
                        return;
                    }
                }

                // Check component availability for QUANTITY_PLANNED mode
                if (currentProductionMode === 'QUANTITY_PLANNED') {
                    const hasInsufficientStock = $('#components-rows-preview .badge.bg-label-danger').length > 0;
                    if (hasInsufficientStock) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Stock Tidak Cukup',
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
                }

                Swal.fire({
                    title: 'Simpan Data?',
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
                formData.append('quantity_planned', $('#quantity-planned').val());
                formData.append('pic_id', $('#pic-user').val());
                formData.append('description', $('#description').val());
                formData.append('memo', $('#production-memo').val());

                // Parse currency values to remove formatting (Rp and thousand separators)
                formData.append('bundle_price', parseCurrency($('#bundle-price').val()) || 0);
                formData.append('bundle_price_unit', parseCurrency($('#bundle-price-unit').val()) || 0);
                formData.append('total_component_cost_unit', parseCurrency($('#total-component-cost-unit').val()) || 0);
                formData.append('hpp_production', parseCurrency($('#hpp-production').val()) || 0);
                formData.append('production_mode', currentProductionMode);

                // Include material inputs and leftover data for MATERIAL_BASED mode
                if (currentProductionMode === 'MATERIAL_BASED') {
                    const materialData = [];
                    $('#components-rows-input tr').each(function() {
                        const $row = $(this);
                        const componentIndex = $row.data('component-index');
                        const materialInputRaw = $row.find('.material-input').val();
                        let materialInput = parseCurrency(materialInputRaw) || 0;

                        // Validate and limit to 4 decimal places
                        materialInput = parseFloat(materialInput.toFixed(4));

                        const leftoverText = $row.find('.leftover-material').text();
                        // Parse Indonesian format: 1.234,5678 → 1234.5678
                        let leftover = parseCurrency(leftoverText.split(' ')[0]) || 0;

                        // Limit leftover to 4 decimal places
                        leftover = parseFloat(leftover.toFixed(4));

                        materialData.push({
                            product_id: bundleComponents[componentIndex].id,
                            product_name: bundleComponents[componentIndex].product_name,
                            qty_per_bundle: bundleComponents[componentIndex].quantity_required,
                            material_input: materialInput,
                            leftover: leftover,
                            unit: bundleComponents[componentIndex].unit
                        });
                    });
                    formData.append('material_inputs', JSON.stringify(materialData));
                }

                // Handle file attachment
                const attachmentFile = $('#attachment')[0].files[0];
                if (attachmentFile) {
                    formData.append('attachment', attachmentFile);
                }

                $.ajax({
                    url: "{{ route('main_transaction.production.store') }}",
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                customClass: {
                                    confirmButton: 'btn btn-primary'
                                },
                                buttonsStyling: false
                            }).then(() => {
                                window.location.href =
                                    "{{ route('main_transaction.production.index') }}";
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
