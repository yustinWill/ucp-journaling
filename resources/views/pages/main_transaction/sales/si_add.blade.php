@extends('layouts.default')
@section('page_title')
    Buat Faktur Penjualan
@stop
@section('content')
    <div class="card mb-6">
        <h5 class="card-header">Buat Faktur Penjualan</h5>
        <form class="card-body" id="salesInvoiceForm">
            <!-- Transaction Information Section -->
            <div class="section-header mt-12">
                <h6>Informasi Transaksi</h6>
                <hr class="mx-n6">
            </div>
            
            <!-- Row 1: Basic Information -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Pelanggan <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="customer-select">
                        <option value="">Pilih Pelanggan</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer['value'] }}">{{ $customer['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email <span style="color: red;">*</span></label>
                    <input type="email" class="form-control" id="customer-email" placeholder="email@pelanggan.com" />
                </div>
            </div>
            
            <!-- Row 2: Address and Transaction Details -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Alamat <span style="color: red;">*</span></label>
                    <textarea class="form-control" id="customer-address" rows="3" placeholder="autofill but editable"></textarea>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Transaksi <span style="color: red;">*</span></label>
                    <input type="date" class="form-control global-single-date-picker" id="transaction-date" />
                </div>
                <div class="col-md-3">
                    <label class="form-label">Nomor Transaksi <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="transaction-number" value="SI-2025-001" placeholder="autofill but editable" />
                </div>
            </div>
            
            <!-- Row 3: Reference Information -->
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label">Nomor Pemesanan Penjualan</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="so-reference" value="Sales Order 110001" placeholder="Sales Order 110001" />
                        <button class="btn btn-outline-secondary" type="button" id="so-lookup-btn">
                            <i class="ti ti-link"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Syarat Pembayaran <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="payment-terms">
                        @foreach($paymentTerms as $term)
                            <option value="{{ $term['value'] }}" {{ $term['value'] == 'Net 30' ? 'selected' : '' }}>{{ $term['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Nomor Referensi Pelanggan</label>
                    <input type="text" class="form-control" id="customer-reference" placeholder="Referensi dari pelanggan" />
                </div>
            </div>
            
            <!-- Row 4: Additional Details -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label">Tag</label>
                    <input type="text" class="form-control" id="tag" placeholder="Kategori transaksi" />
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal Jatuh Tempo <span style="color: red;">*</span></label>
                    <input type="date" class="form-control global-single-date-picker" id="due-date" placeholder="autofill" />
                </div>
                <div class="col-md-4">
                    <label class="form-label">Gudang</label>
                    <select class="global-select2 form-select" id="warehouse" data-allow-clear="true">
                        <option value="">Pilih Gudang</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse['value'] }}">{{ $warehouse['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <!-- Product Information Section -->
            <div class="section-header mt-12">
                <h6>Informasi Penjualan</h6>
                <hr class="mx-n6">
                <div class="text-end mb-4">
                    <input type="checkbox" id="includeTax" class="form-check-input me-2">
                    <label for="includeTax" class="form-check-label">Harga Termasuk Pajak</label>
                </div>
            </div>
            
            <!-- Product Table -->
            <div class="table-responsive mx-n5 my-4">
                <table class="table table-bordered" id="invoiceTable">
                    <thead>
                        <tr>
                            <th style="width: 15%;">Produk</th>
                            <th style="width: 20%;">Deskripsi</th>
                            <th style="width: 10%;">Kuantitas</th>
                            <th style="width: 8%;">Unit</th>
                            <th style="width: 12%;">Harga Satuan</th>
                            <th style="width: 10%;">Diskon</th>
                            <th style="width: 10%;">Pajak</th>
                            <th style="width: 15%;">Jumlah</th>
                            <th style="width: 5%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="invoiceTableBody">
                        <tr>
                            <td>
                                <select class="global-select2 form-select product-select" required>
                                    <option value="">Pilih Produk</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product['value'] }}">{{ $product['label'] }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control product-description" value="ALBUM SVT VOL 1" readonly>
                            </td>
                            <td>
                                <input type="number" class="form-control quantity-field" value="100" min="1" required>
                            </td>
                            <td>
                                <input type="text" class="form-control unit-field" value="pcs" readonly>
                            </td>
                            <td>
                                <input type="text" class="form-control unit-price-field curr-mask" value="Rp 250,000.00" required>
                            </td>
                            <td>
                                <input type="text" class="form-control discount-field" value="5%" placeholder="5%">
                            </td>
                            <td>
                                <select class="global-select2 form-select tax-select" data-allow-clear="true">
                                    @foreach($taxOptions as $tax)
                                        <option value="{{ $tax['value'] }}">{{ $tax['label'] }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control line-total readonly-field" value="Rp 25,000,000" readonly>
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
            
            <!-- Add Product Button -->
            <div class="mb-3">
                <button type="button" class="btn btn-primary" id="addProductRow">
                    <i class="icon-base ti tabler-plus me-1"></i>
                    <span class="align-middle">Tambah Produk</span>
                </button>
            </div>
            
            <!-- Summary & Additional Information -->
            <div class="section-header mt-12">
                <h6>Informasi Tambahan & Ringkasan</h6>
                <hr class="mx-n6">
            </div>
            
            <div class="row g-3">
                <!-- Left Side: Additional Information -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Pesan</label>
                        <textarea class="form-control" rows="4" id="customer-message" placeholder="Catatan atau pesan tambahan terkait transaksi..." maxlength="500"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Memo</label>
                        <textarea class="form-control" rows="4" id="internal-memo" placeholder="Memo internal..." maxlength="500"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lampiran</label>
                        <input type="file" class="form-control" id="attachment" accept=".xlsx,.xls,.docx,.doc,.pdf,.png,.zip">
                        <small class="form-text text-muted">File dapat berupa Excel, Word, PDF, PNG, atau Zip (Max: 10MB)</small>
                    </div>
                </div>
                
                <!-- Right Side: Summary Section -->
                <div class="col-md-6">
                    <div class="summary-section ps-4">
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Subtotal</label>
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control readonly-field" id="subtotal-amount" dir="rtl" value="Rp 12,000,000.00" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Diskon Perbaris</label>
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control readonly-field" id="line-discount-amount" dir="rtl" value="Rp 500,000.00" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Diskon</label>
                            </div>
                            <div class="col-6">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="order-discount-percent" value="5%" style="max-width: 60px;">
                                    <input type="text" class="form-control readonly-field" id="order-discount-amount" dir="rtl" value="Rp 600,000.00" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Pajak</label>
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control readonly-field" id="tax-amount" dir="rtl" value="Rp 1,221,000.00" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label"><strong>Total</strong></label>
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control readonly-field fw-bold" id="total-amount" dir="rtl" value="Rp 12,321,000.00" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Sisa Tagihan</label>
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control readonly-field" id="outstanding-amount" dir="rtl" value="Rp 12,321,000.00" readonly>
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
            // Set default transaction date to today
            $('#transaction-date').val(new Date().toISOString().split('T')[0]);
            
            // Customer selection change handler
            $('#customer-select').change(function() {
                const selectedCustomer = $(this).val();
                if (selectedCustomer) {
                    // TODO: Load customer details and populate email and address
                    console.log('Loading customer details for:', selectedCustomer);
                }
            });
            
            // SO lookup button handler
            $('#so-lookup-btn').click(function() {
                // TODO: Open SO lookup modal and populate form
                console.log('Opening SO lookup modal');
            });
            
            // Calculate line total when quantity, price, or discount changes
            $(document).on('input', '.quantity-field, .unit-price-field, .discount-field', function() {
                const row = $(this).closest('tr');
                calculateLineTotal(row);
            });
            
            // Add product row
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
                            <select class="global-select2 form-select product-select" required>
                                ${productOptions}
                            </select>
                        </td>
                        <td>
                            <input type="text" class="form-control product-description" placeholder="Deskripsi produk" readonly>
                        </td>
                        <td>
                            <input type="number" class="form-control quantity-field" value="1" min="1" required>
                        </td>
                        <td>
                            <input type="text" class="form-control unit-field" value="pcs" readonly>
                        </td>
                        <td>
                            <input type="text" class="form-control unit-price-field curr-mask" value="Rp 0" required>
                        </td>
                        <td>
                            <input type="text" class="form-control discount-field" value="0%" placeholder="0%">
                        </td>
                        <td>
                            <select class="global-select2 form-select tax-select" data-allow-clear="true">
                                ${taxOptions}
                            </select>
                        </td>
                        <td>
                            <input type="text" class="form-control line-total readonly-field" value="Rp 0" readonly>
                        </td>
                        <td>
                            <button class="btn btn-label-danger" type="button" onclick="removeRow(this)">
                                <i class="icon-base ti tabler-x me-1"></i>
                                <span class="align-middle">Hapus</span>
                            </button>
                        </td>
                    </tr>
                `;
                $('#invoiceTableBody').append(newRow);
            });
            
            // Remove row function
            window.removeRow = function(button) {
                $(button).closest('tr').remove();
                calculateGrandTotal();
            };
            
            // Calculate line total
            function calculateLineTotal(row) {
                const quantity = parseFloat(row.find('.quantity-field').val()) || 0;
                const unitPriceText = row.find('.unit-price-field').val().replace(/[Rp,\s]/g, '');
                const unitPrice = parseFloat(unitPriceText) || 0;
                const discountText = row.find('.discount-field').val().replace('%', '');
                const discountPercent = parseFloat(discountText) || 0;
                const taxType = row.find('.tax-select').val();
                const includeTax = $('#includeTax').is(':checked');
                
                let subtotal = quantity * unitPrice;
                let discountAmount = subtotal * (discountPercent / 100);
                subtotal = subtotal - discountAmount;
                
                let taxAmount = 0;
                if (taxType === 'PPN') {
                    if (includeTax) {
                        taxAmount = subtotal * 0.11 / 1.11;
                        subtotal = subtotal - taxAmount;
                    } else {
                        taxAmount = subtotal * 0.11;
                    }
                }
                
                const total = subtotal + taxAmount;
                row.find('.line-total').val(formatCurrency(total));
                
                calculateGrandTotal();
            }
            
            // Calculate grand total
            function calculateGrandTotal() {
                // TODO: Implement comprehensive grand total calculation with discounts
                console.log('Calculating grand total with multi-level discounts');
            }
            
            // Currency functions are now available globally
            
            // Submit button handler
            $('#submit-btn').click(function(e) {
                e.preventDefault();
                
                // TODO: Add validation
                
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
        
        // Add Cleave.js script import
        if (!document.querySelector('script[src*="cleave"]')) {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js';
            document.head.appendChild(script);
        }
    </script>
@endsection