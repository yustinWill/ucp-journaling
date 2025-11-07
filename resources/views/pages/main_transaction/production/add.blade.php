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
                                    <option value="{{ $product->id }}">{{ $product->code }} - {{ $product->name }}</option>
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
                                    <option value="{{ $warehouse->id }}">{{ $warehouse->code }} - {{ $warehouse->name }}
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
                        <input class="form-check-input" type="checkbox" id="production-mode-toggle" />
                        <label class="form-check-label" for="production-mode-toggle">
                            <span id="production-mode-label">Mode: Quantity Planned (Default)</span>
                        </label>
                    </div>
                    <small class="form-text text-muted" id="production-mode-description">
                        Tentukan jumlah produksi, sistem akan hitung kebutuhan material
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

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Quantity Planned <span style="color: red;">*</span></label>
                    <input type="number" class="form-control" id="quantity-planned" name="quantity_planned"
                        placeholder="100" min="1" step="1" required />
                    <small class="form-text text-muted" id="quantity-planned-note">Jumlah bundle yang akan diproduksi</small>
                </div>
                <div class="col-md-6" id="calculated-quantity-container" style="display: none;">
                    <label class="form-label">Maximum Producible Quantity</label>
                    <input type="text" class="form-control" id="calculated-quantity" disabled
                        value="0" />
                    <small class="form-text text-muted">Jumlah maksimal yang bisa diproduksi dari material yang tersedia</small>
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
                                <th style="width: 25%;">Komponen Product</th>
                                <th style="width: 10%;">Satuan</th>
                                <th style="width: 13%;">Qty per Bundle</th>
                                <th style="width: 15%;">Stock Available</th>
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

            <!-- Informasi Tambahan Section -->
            <div class="section-header mt-12">
                <h6>Informasi Tambahan</h6>
                <hr class="mx-n6">
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Deskripsi</label>
                    <textarea class="form-control" id="description" name="description" rows="3"
                        placeholder="Jelaskan detail dan tujuan produksi..." maxlength="1000"></textarea>
                    <small class="form-text text-muted">Maksimal 1000 karakter</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Memo <small class="text-muted">(Internal)</small></label>
                    <textarea class="form-control" id="production-memo" name="memo" rows="3"
                        placeholder="Catatan internal untuk produksi..." maxlength="500"></textarea>
                    <small class="form-text text-muted">Maksimal 500 karakter - Hanya untuk keperluan internal</small>
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
            let currentProductionMode = 'QUANTITY_PLANNED'; // QUANTITY_PLANNED or MATERIAL_BASED
            let bundleComponents = [];

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

                    // Disable quantity planned and show calculated quantity
                    $('#quantity-planned').prop('disabled', true).val('');
                    $('#quantity-planned-note').html('<span style="color: #ff6b6b;">Field disabled - jumlah dihitung otomatis dari material input</span>');
                    $('#calculated-quantity-container').show();

                    // Switch component tables
                    $('#components-section-preview').hide();
                    $('#components-section-input').show();

                } else {
                    // Switch to Quantity Planned mode
                    currentProductionMode = 'QUANTITY_PLANNED';
                    $('#production-mode-label').text('Mode: Quantity Planned (Default)');
                    $('#production-mode-description').text('Tentukan jumlah produksi, sistem akan hitung kebutuhan material');

                    // Enable quantity planned and hide calculated quantity
                    $('#quantity-planned').prop('disabled', false);
                    $('#quantity-planned-note').text('Jumlah bundle yang akan diproduksi');
                    $('#calculated-quantity-container').hide();

                    // Switch component tables
                    $('#components-section-preview').show();
                    $('#components-section-input').hide();
                }

                // Reload components if bundle product already selected
                const bundleProductId = $('#bundle-product').val();
                if (bundleProductId) {
                    loadBundleComponents(bundleProductId);
                }
            });

            // Bundle product selection change
            $('#bundle-product').on('change', function() {
                const bundleProductId = $(this).val();

                if (bundleProductId) {
                    loadBundleComponents(bundleProductId);
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

            // Quantity planned change
            $('#quantity-planned').on('input', function() {
                if (currentProductionMode === 'QUANTITY_PLANNED') {
                    updateTotalRequiredQuantities();
                }
            });

            function loadBundleComponents(bundleProductId) {
                // Load bundle components via AJAX
                $.ajax({
                    url: "{{ route('main_transaction.production.get_bundle_components') }}",
                    type: 'GET',
                    data: {
                        bundle_product_id: bundleProductId
                    },
                    success: function(response) {
                        if (response.success) {
                            bundleComponents = response.components;
                            if (currentProductionMode === 'QUANTITY_PLANNED') {
                                displayComponentsPreview(bundleComponents);
                            } else {
                                displayComponentsInput(bundleComponents);
                            }
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
                if (num % 1 === 0) {
                    // Whole number - no decimals
                    return toIndonesianNumberFormat(String(Math.floor(num)));
                } else {
                    // Has decimals - show only necessary decimals (strip trailing zeros)
                    let formatted = num.toFixed(4); // Max 4 decimals
                    formatted = formatted.replace(/\.?0+$/, ''); // Remove trailing zeros
                    return toIndonesianNumberFormat(formatted);
                }
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
                    <td>${component.product_name}</td>
                    <td class="text-center">${component.unit}</td>
                    <td class="text-center">${formatNumber(component.quantity_required)}</td>
                    <td class="text-center total-required" data-stock-available="${component.stock_available}" data-qty-per-bundle="${component.quantity_required}">${formatNumber(totalRequired)}</td>
                    <td class="text-center">${formatNumber(component.stock_available)}</td>
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
                    data-stock-available="${component.stock_available}">
                    <td>${component.product_name}</td>
                    <td class="text-center">${component.unit}</td>
                    <td class="text-center">${formatNumber(component.quantity_required)}</td>
                    <td class="text-center stock-available">${formatNumber(component.stock_available)}</td>
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
                });
            }

            function updateTotalRequiredQuantities() {
                const qtyPlanned = $('#quantity-planned').val() || 0;

                $('#components-rows-preview .total-required').each(function() {
                    const qtyPerBundle = $(this).data('qty-per-bundle');
                    const totalRequired = qtyPerBundle * qtyPlanned;

                    $(this).text(formatNumber(totalRequired));

                    // Update status based on availability
                    const row = $(this).closest('tr');
                    const stockAvailable = $(this).data('stock-available');
                    const statusCell = row.find('td').eq(5);

                    if (totalRequired <= stockAvailable) {
                        statusCell.html('<span class="badge bg-label-success">Available</span>');
                    } else {
                        statusCell.html('<span class="badge bg-label-danger">Insufficient Stock</span>');
                    }
                });
            }

            // Calculate maximum production based on material inputs
            function calculateMaximumProduction() {
                let maxProduction = Infinity;
                let allInputsFilled = true;
                let bottleneckComponent = '';

                // First pass: Find bottleneck and validate inputs
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

                    // Calculate how many bundles can be produced from this material
                    const possibleProduction = Math.floor(materialInput / qtyPerBundle);

                    // Find the minimum (bottleneck)
                    if (possibleProduction < maxProduction) {
                        maxProduction = possibleProduction;
                        bottleneckComponent = productName;
                    }
                });

                // Second pass: Update display and status based on bottleneck
                $('#components-rows-input tr').each(function() {
                    const $row = $(this);
                    const qtyPerBundle = parseFloat($row.data('qty-per-bundle'));
                    const materialInputRaw = $row.find('.material-input').val();
                    const materialInput = parseCurrency(materialInputRaw) || 0;
                    const componentIndex = $row.data('component-index');
                    const stockAvailable = parseFloat($row.data('stock-available'));
                    const unit = bundleComponents[componentIndex].unit;

                    // Update status
                    const $statusCell = $row.find('.material-status');

                    if (materialInput === 0) {
                        $statusCell.html('<span class="badge bg-label-secondary">Waiting Input</span>');
                        $row.find('.leftover-material').text('-');
                    } else if (materialInput > stockAvailable) {
                        // CRITICAL FIX: Only show error when EXCEEDS stock
                        $statusCell.html('<span class="badge bg-label-danger">Exceeds Stock</span>');
                        $row.find('.leftover-material').text('-');
                    } else {
                        // Material is valid (less than or equal to stock)
                        if (maxProduction !== Infinity && allInputsFilled) {
                            // Use the BOTTLENECK maxProduction for all components
                            const usedMaterial = maxProduction * qtyPerBundle;
                            const leftover = materialInput - usedMaterial;

                            // Format leftover
                            $row.find('.leftover-material').text(formatNumber(leftover));

                            // Update status based on leftover
                            if (leftover === 0) {
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

                // Update calculated quantity field
                if (maxProduction === Infinity || !allInputsFilled) {
                    $('#calculated-quantity').val('0');
                    $('#quantity-planned').val('0');
                } else {
                    $('#calculated-quantity').val(maxProduction);
                    $('#quantity-planned').val(maxProduction); // Sync for form submission

                    // Console log for debugging
                    console.log('✅ Material-Based Calculation:', {
                        maxProduction: maxProduction,
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
