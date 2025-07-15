@extends('layouts.default')
@section('page_title')
    Buat Pemesanan Pembelian
@stop
@section('content')
    <div class="card mb-6">
        <h5 class="card-header">Buat Pemesanan Pembelian</h5>
        <form class="card-body" id="purchaseOrderForm">
            <!-- Transaction Information Section -->
            <div class="section-header mt-12">
                <h6>Informasi Transaksi</h6>
                <hr class="mx-n6">
            </div>

            <!-- Row 1: Basic Information -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Supplier <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="supplier-select">
                        <option value="">Pilih Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier['value'] }}">{{ $supplier['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email <span style="color: red;">*</span></label>
                    <input type="email" class="form-control" id="supplier-email" placeholder="email@supplier.com" />
                </div>
            </div>

            <!-- Row 2: Address and Transaction Details -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Alamat <span style="color: red;">*</span></label>
                    <textarea class="form-control" id="supplier-address" rows="3" placeholder="autofill but editable"></textarea>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Transaksi <span style="color: red;">*</span></label>
                    <input type="text" class="form-control global-single-date-picker" placeholder="YYYY-MM-DD" />
                </div>
                <div class="col-md-3">
                    <label class="form-label">Nomor Transaksi <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="po-number" value="PO-2025-001"
                        placeholder="autofill but editable" />
                </div>
            </div>

            <!-- Row 3: Payment and Reference -->
            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <label class="form-label">Tag</label>
                    <input type="text" class="form-control" id="tag" placeholder="Kategori transaksi" />
                </div>
                <div class="col-md-3">
                    <label class="form-label">Syarat Pembayaran <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="payment-terms">
                        @foreach($paymentTerms as $term)
                            <option value="{{ $term['value'] }}" {{ $term['value'] == 'Net 30' ? 'selected' : '' }}>{{ $term['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Nomor Referensi Supplier</label>
                    <input type="text" class="form-control" id="supplier-reference"
                        placeholder="Referensi dari supplier" />
                </div>
            </div>

            <!-- Row 4: Due Date and Warehouse -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tanggal Jatuh Tempo <span style="color: red;">*</span></label>
                    <input type="date" class="form-control global-single-date-picker" id="due-date" placeholder="YYYY-MM-DD" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Gudang</label>
                    <select class="global-select2 form-select" id="warehouse-select" data-allow-clear="true">
                        <option value="">Pilih Gudang</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse['value'] }}">{{ $warehouse['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Product Information Section -->
            <div class="section-header mt-12">
                <h6>Informasi Pembelian</h6>
                <hr class="mx-n6">
                <div class="text-end mb-4">
                    <input type="checkbox" id="includeTax" class="form-check-input me-2">
                    <label for="includeTax" class="form-check-label">Harga Termasuk Pajak</label>
                </div>
            </div>

            <!-- Product Table -->
            <div class="table-responsive mx-n5 my-4">
                <table class="table table-bordered" id="productTable">
                    <thead>
                        <tr>
                            <th style="width: 18%;">Produk <span style="color: red;">*</span></th>
                            <th style="width: 18%;">Deskripsi</th>
                            <th style="width: 10%;">Kuantitas <span style="color: red;">*</span></th>
                            <th style="width: 8%;">Unit</th>
                            <th style="width: 15%;">Harga Satuan <span style="color: red;">*</span></th>
                            <th style="width: 12%;">Pajak</th>
                            <th style="width: 14%;">Jumlah</th>
                            <th style="width: 5%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="productTableBody">
                        <tr>
                            <td>
                                <select class="global-select2 form-select product-select" name="product[]" required>
                                    <option value="">Pilih Produk</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product['value'] }}">{{ $product['label'] }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control product-description" name="description[]"
                                    placeholder="Deskripsi produk" readonly>
                            </td>
                            <td>
                                <input type="number" class="form-control quantity-field" name="quantity[]"
                                    value="1" min="1" required>
                            </td>
                            <td>
                                <input type="text" class="form-control unit-field" name="unit[]" value="pcs"
                                    readonly>
                            </td>
                            <td>
                                <input type="text" class="form-control unit-price-field curr-mask" name="unit_price[]"
                                    dir="rtl" value="0" required>
                            </td>
                            <td>
                                <select class="global-select2 form-select tax-select" name="tax[]" data-allow-clear="true">
                                    @foreach($taxOptions as $tax)
                                        <option value="{{ $tax['value'] }}">{{ $tax['label'] }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control subtotal-field readonly-field"
                                    name="subtotal[]" dir="rtl" value="Rp 0" readonly>
                            </td>
                            <td>
                                <button class="btn btn-label-danger" type="button" onclick="removeRow(this)">
                                    <i class="icon-base ti tabler-x me-1"></i>
                                    <span class="align-middle">Hapus</span>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <select class="global-select2 form-select product-select" name="product[]">
                                    <option value="">Pilih Produk</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product['value'] }}">{{ $product['label'] }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control product-description" name="description[]"
                                    placeholder="Deskripsi produk" readonly>
                            </td>
                            <td>
                                <input type="number" class="form-control quantity-field" name="quantity[]"
                                    value="1" min="1">
                            </td>
                            <td>
                                <input type="text" class="form-control unit-field" name="unit[]" value="pcs"
                                    readonly>
                            </td>
                            <td>
                                <input type="text" class="form-control unit-price-field curr-mask" name="unit_price[]"
                                    dir="rtl" value="0">
                            </td>
                            <td>
                                <select class="global-select2 form-select tax-select" name="tax[]" data-allow-clear="true">
                                    @foreach($taxOptions as $tax)
                                        <option value="{{ $tax['value'] }}">{{ $tax['label'] }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control subtotal-field readonly-field"
                                    name="subtotal[]" dir="rtl" value="Rp 0" readonly>
                            </td>
                            <td>
                                <button class="btn btn-label-danger" type="button" onclick="removeRow(this)">
                                    <i class="icon-base ti tabler-x me-1"></i>
                                    <span class="align-middle">Hapus</span>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <select class="global-select2 form-select product-select" name="product[]">
                                    <option value="">Pilih Produk</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product['value'] }}">{{ $product['label'] }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control product-description" name="description[]"
                                    placeholder="Deskripsi produk" readonly>
                            </td>
                            <td>
                                <input type="number" class="form-control quantity-field" name="quantity[]"
                                    value="1" min="1">
                            </td>
                            <td>
                                <input type="text" class="form-control unit-field" name="unit[]" value="pcs"
                                    readonly>
                            </td>
                            <td>
                                <input type="text" class="form-control unit-price-field curr-mask" name="unit_price[]"
                                    dir="rtl" value="0">
                            </td>
                            <td>
                                <select class="global-select2 form-select tax-select" name="tax[]" data-allow-clear="true">
                                    @foreach($taxOptions as $tax)
                                        <option value="{{ $tax['value'] }}">{{ $tax['label'] }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control subtotal-field readonly-field"
                                    name="subtotal[]" dir="rtl" value="Rp 0" readonly>
                            </td>
                            <td>
                                <button class="btn btn-label-danger" type="button" onclick="removeRow(this)">
                                    <i class="icon-base ti tabler-x me-1"></i>
                                    <span class="align-middle">Hapus</span>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <select class="global-select2 form-select product-select" name="product[]">
                                    <option value="">Pilih Produk</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product['value'] }}">{{ $product['label'] }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control product-description" name="description[]"
                                    placeholder="Deskripsi produk" readonly>
                            </td>
                            <td>
                                <input type="number" class="form-control quantity-field" name="quantity[]"
                                    value="1" min="1">
                            </td>
                            <td>
                                <input type="text" class="form-control unit-field" name="unit[]" value="pcs"
                                    readonly>
                            </td>
                            <td>
                                <input type="text" class="form-control unit-price-field curr-mask" name="unit_price[]"
                                    dir="rtl" value="0">
                            </td>
                            <td>
                                <select class="global-select2 form-select tax-select" name="tax[]" data-allow-clear="true">
                                    @foreach($taxOptions as $tax)
                                        <option value="{{ $tax['value'] }}">{{ $tax['label'] }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control subtotal-field readonly-field"
                                    name="subtotal[]" dir="rtl" value="Rp 0" readonly>
                            </td>
                            <td>
                                <button class="btn btn-label-danger" type="button" onclick="removeRow(this)">
                                    <i class="icon-base ti tabler-x me-1"></i>
                                    <span class="align-middle">Hapus</span>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Add Row Button -->
            <div class="mb-3">
                <button type="button" class="btn btn-primary" id="addProductRow">
                    <i class="icon-base ti tabler-plus me-1"></i>
                    <span class="align-middle">Tambah Produk</span>
                </button>
            </div>

            <!-- Additional Information & Summary -->
            <div class="section-header mt-12">
                <h6>Informasi Tambahan & Ringkasan</h6>
                <hr class="mx-n6">
            </div>

            <div class="row g-3">
                <!-- Left Side: Additional Information -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Pesan</label>
                        <textarea class="form-control" rows="4" id="supplier-message"
                            placeholder="Catatan atau instruksi khusus untuk supplier" maxlength="500"></textarea>
                        <small class="form-text text-muted">Pesan yang akan dikirim ke supplier bersama PO</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Memo</label>
                        <textarea class="form-control" rows="4" id="internal-memo" placeholder="Catatan internal untuk tim purchasing"
                            maxlength="500"></textarea>
                        <small class="form-text text-muted">Memo internal untuk tim purchasing</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lampiran</label>
                        <input type="file" class="form-control" id="attachment"
                            accept=".xlsx,.xls,.docx,.doc,.pdf,.png,.zip">
                        <small class="form-text text-muted">File dapat berupa Excel, Word, PDF, PNG, atau Zip</small>
                    </div>
                </div>

                <!-- Right Side: Summary Section -->
                <div class="col-md-6">
                    <div class="summary-section ps-4">
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Subtotal</label>
                                <small class="form-text text-muted d-block">Total sebelum pajak</small>
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control readonly-field" id="subtotal-amount"
                                    dir="rtl" value="Rp 0" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">PPN</label>
                                <small class="form-text text-muted d-block">Total pajak pertambahan nilai</small>
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control readonly-field" id="tax-amount" dir="rtl"
                                    value="Rp 0" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label"><strong>Total PO</strong></label>
                                <small class="form-text text-muted d-block">Total nilai purchase order</small>
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control readonly-field fw-bold" id="total-amount"
                                    dir="rtl" value="Rp 0" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Sisa Tagihan</label>
                                <small class="form-text text-muted d-block">Jumlah yang harus dibayar</small>
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control readonly-field" id="estimated-payment"
                                    dir="rtl" value="Rp 0" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 text-end">
                <button type="button" class="btn btn-primary" id="submit-btn">Buat</button>
            </div>
        </form>
    </div>
@endsection
@section('js_script')
    <script>
        $(function() {
            // Initialize currency mask for existing fields
            // initializeCurrencyMask();

            // Product data for auto-fill
            const productData = {
                '1': {
                    description: 'ALBUM SVT VOL 1',
                    unit: 'pcs',
                    price: 250000
                },
                '2': {
                    description: 'Buku Tulis HVS A4',
                    unit: 'pcs',
                    price: 15000
                },
                '3': {
                    description: 'Pensil 2B',
                    unit: 'pcs',
                    price: 3000
                },
                '4': {
                    description: 'Penghapus Karet',
                    unit: 'pcs',
                    price: 2000
                }
            };

            // Supplier data for auto-fill
            const supplierData = {
                '1': {
                    email: 'admin@sumbermakmur.com',
                    address: 'Jl. Industri No. 123, Jakarta Pusat'
                },
                '2': {
                    email: 'info@berkahjaya.com',
                    address: 'Jl. Raya Bekasi No. 456, Bekasi'
                },
                '3': {
                    email: 'contact@mandirisejahtera.com',
                    address: 'Jl. Sudirman No. 789, Jakarta Selatan'
                },
                '4': {
                    email: 'sales@majubersama.com',
                    address: 'Jl. Gatot Subroto No. 321, Jakarta Barat'
                }
            };

            // Supplier selection change handler
            $('#supplier-select').change(function() {
                const selectedSupplier = $(this).val();
                if (selectedSupplier && supplierData[selectedSupplier]) {
                    const supplier = supplierData[selectedSupplier];
                    $('#supplier-email').val(supplier.email);
                    $('#supplier-address').val(supplier.address);
                    $('#delivery-address').val(supplier.address);
                }
            });

            // Product selection change handler
            $(document).on('change', '.product-select', function() {
                const selectedProduct = $(this).val();
                const row = $(this).closest('tr');
                const descriptionField = row.find('.product-description');
                const unitField = row.find('.unit-field');
                const priceField = row.find('.unit-price-field');

                if (selectedProduct && productData[selectedProduct]) {
                    const product = productData[selectedProduct];
                    descriptionField.val(product.description);
                    unitField.val(product.unit);
                    priceField.val(product.price.toLocaleString('id-ID'));
                } else {
                    descriptionField.val('');
                    unitField.val('pcs');
                    priceField.val('0');
                }
                calculateRowTotal(row);
            });

            // Calculate row total when quantity or price changes
            $(document).on('input', '.quantity-field, .unit-price-field', function() {
                const row = $(this).closest('tr');
                calculateRowTotal(row);
            });

            // Tax selection change handler
            $(document).on('change', '.tax-select', function() {
                const row = $(this).closest('tr');
                calculateRowTotal(row);
            });

            // Include tax checkbox handler
            $('#includeTax').change(function() {
                $('#productTableBody tr').each(function() {
                    calculateRowTotal($(this));
                });
            });

            // Add new product row
            $('#addProductRow').click(function() {
                const productOptions = `
                    <option value="">Pilih Produk</option>
                    @foreach($products as $product)
                        <option value="{{ $product['value'] }}">{{ $product['label'] }}</option>
                    @endforeach
                `;
                
                const taxOptions = `
                    @foreach($taxOptions as $tax)
                        <option value="{{ $tax['value'] }}">{{ $tax['label'] }}</option>
                    @endforeach
                `;

                const newRow = `
                    <tr>
                        <td>
                            <select class="global-select2 form-select product-select" name="product[]">
                                ${productOptions}
                            </select>
                        </td>
                        <td>
                            <input type="text" class="form-control product-description" name="description[]" placeholder="Deskripsi produk" readonly>
                        </td>
                        <td>
                            <input type="number" class="form-control quantity-field" name="quantity[]" value="1" min="1">
                        </td>
                        <td>
                            <input type="text" class="form-control unit-field" name="unit[]" value="pcs" readonly>
                        </td>
                        <td>
                            <input type="text" class="form-control unit-price-field curr-mask" name="unit_price[]" dir="rtl" value="0">
                        </td>
                        <td>
                            <select class="global-select2 form-select tax-select" name="tax[]" data-allow-clear="true">
                                ${taxOptions}
                            </select>
                        </td>
                        <td>
                            <input type="text" class="form-control subtotal-field readonly-field" name="subtotal[]" dir="rtl" value="Rp 0" readonly>
                        </td>
                        <td>
                            <button class="btn btn-label-danger" type="button" onclick="removeRow(this)">
                                <i class="icon-base ti tabler-x me-1"></i>
                                <span class="align-middle">Hapus</span>
                            </button>
                        </td>
                    </tr>
                `;
                $('#productTableBody').append(newRow);
                initializeCurrencyMask();
            });

            // Remove product row function
            window.removeRow = function(button) {
                if ($('#productTableBody tr').length > 1) {
                    $(button).closest('tr').remove();
                    calculateGrandTotal();
                }
            };

            // Calculate row total
            function calculateRowTotal(row) {
                const quantity = parseFloat(row.find('.quantity-field').val()) || 0;
                const unitPriceText = row.find('.unit-price-field').val().replace(/,/g, '');
                const unitPrice = parseFloat(unitPriceText) || 0;
                const taxType = row.find('.tax-select').val();
                const includeTax = $('#includeTax').is(':checked');

                let subtotal = quantity * unitPrice;
                let taxAmount = 0;

                if (taxType === 'PPN') {
                    if (includeTax) {
                        // Tax-inclusive calculation
                        taxAmount = subtotal * 0.11 / 1.11;
                        subtotal = subtotal - taxAmount;
                    } else {
                        // Tax-exclusive calculation
                        taxAmount = subtotal * 0.11;
                    }
                }

                const total = subtotal + taxAmount;
                row.find('.subtotal-field').val(formatCurrency(total));

                calculateGrandTotal();
            }

            // Calculate grand total
            function calculateGrandTotal() {
                let subtotal = 0;
                let taxTotal = 0;

                $('#productTableBody tr').each(function() {
                    const quantity = parseFloat($(this).find('.quantity-field').val()) || 0;
                    const unitPriceText = $(this).find('.unit-price-field').val().replace(/,/g, '');
                    const unitPrice = parseFloat(unitPriceText) || 0;
                    const taxType = $(this).find('.tax-select').val();
                    const includeTax = $('#includeTax').is(':checked');

                    let lineSubtotal = quantity * unitPrice;
                    let lineTax = 0;

                    if (taxType === 'PPN') {
                        if (includeTax) {
                            lineTax = lineSubtotal * 0.11 / 1.11;
                            lineSubtotal = lineSubtotal - lineTax;
                        } else {
                            lineTax = lineSubtotal * 0.11;
                        }
                    }

                    subtotal += lineSubtotal;
                    taxTotal += lineTax;
                });

                const total = subtotal + taxTotal;

                $('#subtotal-amount').val(formatCurrency(subtotal));
                $('#tax-amount').val(formatCurrency(taxTotal));
                $('#total-amount').val(formatCurrency(total));
                $('#estimated-payment').val(formatCurrency(total));
            }

            // Currency functions are now available globally

            // Initialize currency mask
            function initializeCurrencyMask() {
                $('.curr-mask').each(function() {
                    new Cleave(this, {
                        numeral: true,
                        numeralThousandsGroupStyle: 'thousand'
                    });
                });
            }

            // Auto-calculate due date based on payment terms
            $('#payment-terms').change(function() {
                const terms = $(this).val();
                const poDate = new Date($('#po-date').val());

                if (poDate && !isNaN(poDate)) {
                    let dueDate = new Date(poDate);

                    switch (terms) {
                        case 'Net 15':
                            dueDate.setDate(dueDate.getDate() + 15);
                            break;
                        case 'Net 30':
                            dueDate.setDate(dueDate.getDate() + 30);
                            break;
                        case 'Net 45':
                            dueDate.setDate(dueDate.getDate() + 45);
                            break;
                        case 'Cash on Delivery':
                        case 'Immediate':
                            dueDate = new Date(poDate);
                            break;
                        default:
                            dueDate.setDate(dueDate.getDate() + 30);
                    }

                    $('#due-date').val(dueDate.toISOString().split('T')[0]);
                }
            });

            // Set default PO date to today
            $('#po-date').val(new Date().toISOString().split('T')[0]);

            // File upload handling
            $('#attachment').change(function() {
                const file = this.files[0];
                if (file) {
                    const maxSize = 10 * 1024 * 1024; // 10MB
                    const allowedTypes = [
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/msword', 'application/pdf', 'image/png', 'application/zip'
                    ];

                    if (file.size > maxSize) {
                        Swal.fire({
                            icon: 'error',
                            title: 'File Terlalu Besar',
                            text: 'Ukuran file maksimal 10MB!'
                        });
                        this.value = '';
                        return;
                    }

                    if (!allowedTypes.includes(file.type)) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Tipe File Tidak Diizinkan',
                            text: 'File harus berupa Excel, Word, PDF, PNG, atau Zip!'
                        });
                        this.value = '';
                        return;
                    }
                }
            });

            // Form validation function
            function validateForm() {
                // Check required fields
                if (!$('#supplier-select').val()) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: 'Silakan pilih supplier terlebih dahulu!'
                    });
                    return false;
                }

                if (!$('#supplier-email').val()) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: 'Email supplier harus diisi!'
                    });
                    return false;
                }

                if (!$('#supplier-address').val() || $('#supplier-address').val().length < 10) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: 'Alamat supplier harus diisi minimal 10 karakter!'
                    });
                    return false;
                }

                if (!$('#po-date').val()) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: 'Tanggal PO harus diisi!'
                    });
                    return false;
                }

                if (!$('#po-number').val()) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: 'Nomor PO harus diisi!'
                    });
                    return false;
                }

                if (!$('#due-date').val()) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: 'Tanggal jatuh tempo harus diisi!'
                    });
                    return false;
                }

                // Check if at least one product is selected
                let hasProduct = false;
                $('#productTableBody tr').each(function() {
                    if ($(this).find('.product-select').val()) {
                        hasProduct = true;
                        return false;
                    }
                });

                if (!hasProduct) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: 'Minimal harus ada satu produk yang dipilih!'
                    });
                    return false;
                }

                // Validate email format
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test($('#supplier-email').val())) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: 'Format email tidak valid!'
                    });
                    return false;
                }

                // Validate dates
                const poDate = new Date($('#po-date').val());
                const dueDate = new Date($('#due-date').val());
                const today = new Date();
                today.setHours(0, 0, 0, 0);

                if (poDate > today) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: 'Tanggal PO tidak boleh di masa depan!'
                    });
                    return false;
                }

                if (dueDate <= poDate) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: 'Tanggal jatuh tempo harus setelah tanggal PO!'
                    });
                    return false;
                }

                return true;
            }

            // Submit button handler
            $('#submit-btn').click(function(e) {
                e.preventDefault();

                if (!validateForm()) {
                    return;
                }

                Swal.fire({
                    title: 'Simpan Data?',
                    text: "Cek kembali data yang sudah diisi!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan Data!',
                    customClass: {
                        confirmButton: 'btn btn-primary',
                        cancelButton: 'btn btn-label-secondary'
                    },
                    buttonsStyling: false
                }).then(function(result) {
                    if (result.value) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Data berhasil disimpan.',
                            customClass: {
                                confirmButton: 'btn btn-primary',
                                cancelButton: 'btn btn-label-secondary'
                            },
                        });
                    }
                });
            });
        });
    </script>
@endsection
