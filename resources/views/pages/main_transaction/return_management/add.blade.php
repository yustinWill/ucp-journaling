@extends('layouts.default')
@section('page_title')
    Buat Return Management
@stop

@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Buat Return Management</h4>
        <form class="card-body" id="return-management-form" autocomplete="off">
            @csrf
            <input type="hidden" class="form-control" id="source-invoice" name="source_invoice_id"
                value="{{ $source_invoice_id }}" />
            <!-- Informasi Dasar Return Section -->
            <div class="section-header mt-12">
                <h6>Informasi Dasar Return</h6>
                <hr class="mx-n6">
            </div>

            <!-- Row 1: Tanggal Return, Nomor Transaksi -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tanggal Return <span style="color: red;">*</span></label>
                    <input type="date" class="form-control global-single-date-picker" id="return-date" name="return_date"
                        value="{{ date('Y-m-d') }}" required />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nomor Transaksi <span style="color: red;">*</span></label>
                    <div class="row g-2">
                        <div class="col">
                            <input type="text" class="form-control" id="return-transaction-code" name="transaction_code"
                                placeholder="Auto-generated" readonly />
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-outline-primary d-flex align-items-center"
                                id="generate-return-number-btn" title="Generate New Number">
                                <i class="icon-base ti tabler-refresh"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 2: Tanggal Transaksi, Jenis Return -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tanggal Transaksi <span style="color: red;">*</span></label>
                    <input type="date" class="form-control global-single-date-picker" id="transaction-date"
                        name="transaction_date" value="{{ date('Y-m-d') }}" required />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jenis Return <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="return-type" name="return_type" data-allow-clear="false"
                        required disabled>
                        <option value="">Pilih Jenis Return</option>
                        <option value="PURCHASE_RETURN" {{ $return_type === 'PURCHASE_RETURN' ? 'selected' : '' }}>Purchase
                            Return</option>
                        <option value="SALES_RETURN" {{ $return_type === 'SALES_RETURN' ? 'selected' : '' }}>Sales Return
                        </option>
                    </select>
                </div>
            </div>

            <!-- Informasi Invoice Asal Section -->
            <div class="section-header mt-12">
                <h6>Informasi Invoice Asal</h6>
                <hr class="mx-n6">
            </div>

            <!-- Row 1: Invoice Asal -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Invoice Asal <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="source-invoice-display" name="source_invoice_id"
                        value="{{ $transaction_number }}" required disabled />
                    {{-- <select class="global-select2 form-select" id="source-invoice" name="source_invoice_id"
                        data-allow-clear="false" required>
                        <option value="">Pilih Invoice</option>
                    </select> --}}
                    {{-- <small class="text-muted">Pilih jenis return terlebih dahulu</small> --}}
                </div>
                <div class="col-md-6">
                    <label class="form-label">Gudang <span style="color: red;">*</span></label>
                    <input type="hidden" id="warehouse_id" name="warehouse_id" value="{{ $invoice_data->warehouse_id }}" />
                    <input type="text" class="form-control"value="{{ $invoice_data->warehouse_code }} - {{ $invoice_data->warehouse_name }}" required disabled />
                </div>
            </div>

            <!-- Detail Return Section -->
            <div class="section-header mt-12">
                <h6>Detail Return</h6>
                <hr class="mx-n6">
            </div>

            <!-- Row 1: Alasan Return, Deskripsi Alasan -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Alasan Return <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="return-reason" name="return_reason"
                        data-allow-clear="false" required>
                        <option value="">Pilih Alasan</option>
                        <option value="DEFECTIVE">DEFECTIVE - Produk Cacat</option>
                        <option value="WRONG_ITEM">WRONG_ITEM - Barang Salah</option>
                        <option value="DAMAGED">DAMAGED - Rusak</option>
                        <option value="EXPIRED">EXPIRED - Kadaluarsa</option>
                        <option value="OVERSTOCKED">OVERSTOCKED - Stok Berlebih</option>
                        <option value="CUSTOMER_REQUEST">CUSTOMER_REQUEST - Permintaan Customer</option>
                        <option value="OTHER">OTHER - Lainnya</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Deskripsi Alasan</label>
                    <textarea class="form-control" id="return-reason-description" name="return_reason_description" rows="3"
                        placeholder="Jelaskan alasan return lebih detail"></textarea>
                </div>
            </div>

            <!-- Row 2: Keterangan -->
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <label class="form-label">Keterangan</label>
                    <textarea class="form-control" id="description" name="description" rows="3"
                        placeholder="Keterangan tambahan untuk return ini"></textarea>
                </div>
            </div>

            <!-- Produk Return Section -->
            <div class="section-header mt-12">
                <h6>Produk Return</h6>
            </div>

            <div class="alert alert-info">
                <i class="icon-base ti tabler-info-circle me-2"></i>
                <strong>Info:</strong> Pilih invoice asal terlebih dahulu untuk menampilkan produk yang dapat di-return.
            </div>

            <!-- Product Table -->
            <div class="mb-4 mx-n5" id="products-section" style="display: none;">
                <div class="table-responsive">
                    <table class="table table-bordered" id="products-table">
                        <thead>
                            <tr>
                                <th style="width: 5%;"></th>
                                <th>Produk</th>
                                <th>Qty Invoice</th>
                                <th>Tersedia Return</th>
                                <th>Qty Return</th>
                                <th>Harga Return</th>
                                <th>Kondisi</th>
                                <th>Note Return</th>
                            </tr>
                        </thead>
                        <tbody id="products-tbody">
                            <!-- Products will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>

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
                                <select class="global-select2 form-select" id="tags" name="tags[]" multiple
                                    data-allow-clear="false">
                                    @foreach ($availableTags as $tag)
                                        <option value="{{ $tag->id }}">{{ $tag->code }} - {{ $tag->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- @if ($canAddTags) --}}
                            <div class="col-auto">
                                <a href="{{ route('master_data.tag.add') }}" target="_blank"
                                    class="btn btn-outline-primary d-flex align-items-center" title="Tambah Tag Baru">
                                    <i class="icon-base ti tabler-plus"></i>
                                </a>
                            </div>
                            {{-- @endif --}}
                        </div>
                        <small class="form-text text-muted">Pilih opsi atau tambah baru jika diperlukan</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Memo Internal</label>
                        <textarea class="form-control" id="internal-memo" name="internal_memo" rows="4"
                            placeholder="Catatan internal..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lampiran</label>
                        <input type="file" class="form-control" id="attachment" name="attachment"
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
                                <input type="text" class="form-control readonly-field" id="subtotal-display"
                                    dir="rtl" value="Rp 0" disabled>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Total Pajak</label>
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control readonly-field" id="total-tax-display"
                                    dir="rtl" value="Rp 0" disabled>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Total Diskon</label>
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control readonly-field" id="total-discount-display"
                                    dir="rtl" value="Rp 0" disabled>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label"><strong>Total Akhir</strong></label>
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control readonly-field fw-bold"
                                    id="grand-total-display" dir="rtl" value="Rp 0" disabled>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hidden fields for calculations -->
            <input type="hidden" id="subtotal" name="subtotal" value="0" />
            <input type="hidden" id="total-tax" name="total_tax" value="0" />
            <input type="hidden" id="total-discount" name="total_discount" value="0" />
            <input type="hidden" id="grand-total" name="grand_total" value="0" />

            <!-- Action Buttons -->
            <div class="pt-6 text-end">
                <a href="{{ route('main_transaction.return_management.index') }}"
                    class="btn btn-label-danger me-3">Batalkan</a>
                <button type="submit" class="btn btn-success" id="save-return-btn">Simpan</button>
            </div>

        </form>
    </div>
@stop

@section('js_script')
    <script>
        $(document).ready(function() {
            let selectedProducts = [];
            const canViewProduct = {{ $canViewProduct ? 'true' : 'false' }};

            // Generate transaction number on load
            generateTransactionNumber();

            // Return type change handler

            let returnType = $('#return-type').val();
            if (returnType) {
                loadAvailableInvoices(returnType);
                generateTransactionNumber(returnType);
            } else {
                $('#source-invoice').empty().append('<option value="">Pilih Invoice</option>');
                $('#source-invoice-code').val('');
                hideProductsSection();
            }
            // Source invoice change handler
            $('#source-invoice').on('change', function() {

            });


            const invoiceId = $('#source-invoice').val();
            returnType = $('#return-type').val();
            if (invoiceId && returnType) {
                // const selectedOption = $(this).find('option:selected');
                // $('#source-invoice-code').val(selectedOption.text().split(' - ')[0] || '');

                loadInvoiceProducts(invoiceId, returnType);
            } else {
                $('#source-invoice-code').val('');
                hideProductsSection();
            }

            // Form submission
            $('#return-management-form').on('submit', function(e) {
                e.preventDefault();
                submitReturn();
            });

            // Generate transaction number button
            $('#generate-return-number-btn').on('click', function() {
                const returnType = $('#return-type').val() || 'PURCHASE_RETURN';
                generateTransactionNumber(returnType);
            });

            function generateTransactionNumber(returnType = 'PURCHASE_RETURN') {
                $.get('{{ route('main_transaction.return_management.generate_transaction_number') }}', {
                        return_type: returnType
                    })
                    .done(function(response) {
                        if (response.success) {
                            $('#return-transaction-code').val(response.transaction_number);
                        }
                    });
            }

            function loadAvailableInvoices(returnType) {
                // This would typically make an API call to get available invoices
                // For now, we'll show a placeholder
                $('#source-invoice').empty().append('<option value="">Loading invoices...</option>');

                // TODO: Implement actual invoice loading based on return type
                setTimeout(function() {
                    $('#source-invoice').empty().append('<option value="">Pilih Invoice</option>');
                    // Add sample invoices (in real implementation, this would come from API)
                    if (returnType === 'PURCHASE_RETURN') {
                        $('#source-invoice').append(
                            '<option value="1">PI-2024-001 - Supplier ABC</option>');
                        $('#source-invoice').append(
                            '<option value="2">PI-2024-002 - Supplier XYZ</option>');
                    } else {
                        $('#source-invoice').append(
                            '<option value="3">SI-2024-001 - Customer ABC</option>');
                        $('#source-invoice').append(
                            '<option value="4">SI-2024-002 - Customer XYZ</option>');
                    }
                }, 1000);
            }

            function loadInvoiceProducts(invoiceId, returnType) {
                $.get('{{ route('main_transaction.return_management.get_invoice_products') }}', {
                        invoice_id: invoiceId,
                        return_type: returnType
                    })
                    .done(function(response) {
                        if (response.success) {
                            displayProducts(response.products);
                            showProductsSection();
                        }
                    })
                    .fail(function() {
                        hideProductsSection();
                        Swal.fire('Error', 'Gagal memuat data produk', 'error');
                    });
            }

            function displayProducts(products) {
                const tbody = $('#products-tbody');
                tbody.empty();

                products.forEach(function(product, index) {
                    const row = `
                        <tr data-product-id="${product.f_product_id}" data-invoice-product-id="${product.invoice_product_id}">
                            <td>
                                <input type="checkbox" class="form-check-input product-checkbox" value="${index}">
                            </td>
                            <td>
                                ${canViewProduct ? 
                                    `<a href="/master_data/product/detail/${product.f_product_id}" class="text-primary fw-semibold"><strong>${product.product_code} - ${product.product_name}</strong></a>` 
                                    : `<strong>${product.product_code} - ${product.product_name}</strong>`
                                }
                            </td>
                            <td class="text-center">${parseFloat(product.original_qty).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} ${product.unit_code || ''}</td>
                            
                            <td class="text-center">${parseFloat(product.available_qty).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} ${product.unit_code || ''}</td>
                            <td>
                                <input type="number" class="form-control qty-return" min="0" max="${product.available_qty}" 
                                    step="0.001" value="0" disabled>
                            </td>
                            <td>
                                <input type="text" class="form-control return-price currency-input" min="0" step="0.01" 
                                    value="${product.unit_price}" disabled>
                            </td>
                            <td>
                                <select class="form-select return-condition" disabled>
                                    <option value="NEW">NEW - Baru</option>
                                    <option value="USED_GOOD">USED_GOOD - Bekas Baik</option>
                                    <option value="USED_FAIR">USED_FAIR - Bekas Cukup</option>
                                    <option value="DAMAGED">DAMAGED - Rusak</option>
                                    <option value="DEFECTIVE">DEFECTIVE - Cacat</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control return-note" placeholder="Catatan" disabled>
                            </td>
                        </tr>
                    `;
                    tbody.append(row);
                });

                $('.currency-input').each(function() {
                    const val = $(this).val();
                    $(this).val(convertToIndonesianFormat(val));
                });
                initCleaveCurrency(document);
                // Bind events
                bindProductEvents();
            }

            function bindProductEvents() {
                // Checkbox change handler
                $('.product-checkbox').on('change', function() {
                    const row = $(this).closest('tr');
                    const isChecked = $(this).is(':checked');

                    row.find('.qty-return, .return-price, .return-condition, .return-note').prop('disabled',
                        !isChecked);

                    if (!isChecked) {
                        row.find('.qty-return').val(0);
                    }

                    calculateTotals();
                });

                // Quantity and price change handlers
                $('.qty-return, .return-price').on('input', function() {
                    calculateTotals();
                });
            }

            function calculateTotals() {
                let subtotal = 0;

                $('.product-checkbox:checked').each(function() {
                    const row = $(this).closest('tr');
                    const qty = parseFloat(row.find('.qty-return').val()) || 0;
                    const price = parseCurrency(row.find('.return-price').val()) || 0;
                    subtotal += qty * price;
                });

                const totalTax = 0; // TODO: Implement tax calculation if needed
                const totalDiscount = 0; // TODO: Implement discount if needed
                const grandTotal = subtotal + totalTax - totalDiscount;

                // Update display
                $('#subtotal-display').val(formatRupiah(subtotal));
                $('#total-tax-display').val(formatRupiah(totalTax));
                $('#total-discount-display').val(formatRupiah(totalDiscount));
                $('#grand-total-display').val(formatRupiah(grandTotal));

                // Update hidden inputs
                $('#subtotal').val(subtotal);
                $('#total-tax').val(totalTax);
                $('#total-discount').val(totalDiscount);
                $('#grand-total').val(grandTotal);
            }

            function formatRupiah(amount) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
            }

            function showProductsSection() {
                $('#products-section').show();
            }

            function hideProductsSection() {
                $('#products-section').hide();
                selectedProducts = [];
                calculateTotals();
            }

            function submitReturn() {
                // Collect selected products
                const products = [];
                $('.product-checkbox:checked').each(function() {
                    const row = $(this).closest('tr');
                    const product = {
                        f_product_id: row.data('product-id'),
                        f_source_invoice_product_id: row.data('invoice-product-id'),
                        return_qty: parseFloat(row.find('.qty-return').val()) || 0,
                        return_unit_price: parseCurrency(row.find('.return-price').val()) || 0,
                        return_condition: row.find('.return-condition').val(),
                        return_reason: row.find('.return-note').val() || '',
                        note: row.find('.return-note').val() || ''
                    };

                    if (product.return_qty > 0) {
                        products.push(product);
                    }
                });

                if (products.length === 0) {
                    Swal.fire('Peringatan', 'Pilih minimal satu produk untuk di-return', 'warning');
                    return;
                }

                // Prepare form data
                const formData = new FormData(document.getElementById('return-management-form'));
                products.forEach(function(product, idx) {
                    formData.append(`products[${idx}][f_product_id]`, product.f_product_id);
                    formData.append(`products[${idx}][f_source_invoice_product_id]`, product
                        .f_source_invoice_product_id);
                    formData.append(`products[${idx}][return_qty]`, product.return_qty);
                    formData.append(`products[${idx}][return_unit_price]`, product.return_unit_price);
                    formData.append(`products[${idx}][return_condition]`, product.return_condition);
                    formData.append(`products[${idx}][return_reason]`, product.return_reason);
                    formData.append(`products[${idx}][note]`, product.note);
                });

                formData.append('return_type', $('#return-type').val());
                formData.append('source_invoice_code', $('#source-invoice-display').val());
                formData.append('warehouse_id', $('#warehouse_id').val());

                Swal.fire({
                    title: 'Buat Return Management?',
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
                        // Submit
                        $.ajax({
                            url: '{{ route('main_transaction.return_management.store') }}',
                            method: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            beforeSend: function() {
                                $('#save-return-btn').prop('disabled', true).html(
                                    '<i class="ti ti-loader me-1"></i> Menyimpan...');
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: response.message,
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href =
                                            '{{ route('main_transaction.return_management.index') }}';
                                    }
                                });
                            },
                            error: function(xhr) {
                                let message = 'Terjadi kesalahan saat menyimpan return';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    message = xhr.responseJSON.message;
                                }

                                Swal.fire({
                                    title: 'Error!',
                                    text: message,
                                    icon: 'error'
                                });
                            },
                            complete: function() {
                                $('#save-return-btn').prop('disabled', false).html(
                                    '<i class="icon-base ti tabler-device-floppy me-1"></i> Simpan Return'
                                );
                            }
                        });
                    }
                });




            }
        });
    </script>
@endsection
