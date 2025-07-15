@extends('layouts.default')
@section('page_title')
    Buat Faktur Pembelian
@stop
@section('content')
    <div class="card mb-6">
        <h5 class="card-header">Buat Faktur Pembelian</h5>
        <form class="card-body" id="purchaseInvoiceForm">
            <!-- Transaction Information Section -->
            <div class="section-header mt-12">
                <h6>Informasi Transaksi</h6>
                <hr class="mx-n6">
            </div>

            <!-- Row 1: Basic Information -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Referensi Purchase Delivery <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="pd-reference" data-allow-clear="true">
                        <option value="">Pilih Purchase Delivery</option>
                        @foreach($purchaseDeliveries as $delivery)
                            <option value="{{ $delivery['value'] }}">{{ $delivery['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nomor Invoice Supplier <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="supplier-invoice-number"
                        placeholder="Nomor invoice dari supplier" />
                </div>
            </div>

            <!-- Row 2: Invoice Details -->
            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <label class="form-label">Tanggal Invoice <span style="color: red;">*</span></label>
                    <input type="date" class="form-control global-single-date-picker" id="invoice-date"
                        placeholder="YYYY-MM-DD" />
                </div>
                <div class="col-md-3">
                    <label class="form-label">Nomor Invoice Internal <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="internal-invoice-number" value="PI-2025-001"
                        placeholder="Nomor otomatis, dapat diedit" />
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Jatuh Tempo <span style="color: red;">*</span></label>
                    <input type="date" class="form-control global-single-date-picker" id="due-date"
                        placeholder="YYYY-MM-DD" />
                </div>
                <div class="col-md-3">
                    <label class="form-label">Mata Uang</label>
                    <select class="global-select2 form-select" id="currency" data-allow-clear="true">
                        @foreach($currencies as $currency)
                            <option value="{{ $currency['value'] }}" {{ $currency['value'] == 'IDR' ? 'selected' : '' }}>{{ $currency['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Row 3: Additional Information -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Supplier</label>
                    <input type="text" class="form-control" id="supplier-name"
                        placeholder="Nama supplier akan terisi otomatis" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Metode Pembayaran</label>
                    <select class="global-select2 form-select" id="payment-method" data-allow-clear="true">
                        @foreach($paymentMethods as $method)
                            <option value="{{ $method['value'] }}">{{ $method['label'] }}</option>
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
            <div class="mx-n5 my-4">
                <table class="table table-bordered" id="invoiceTable">
                    <thead>
                        <tr>
                            <th style="width: 18%;">Produk</th>
                            <th style="width: 18%;">Deskripsi</th>
                            <th style="width: 10%;">Qty Diterima</th>
                            <th style="width: 10%;">Qty Ditagih</th>
                            <th style="width: 8%;">Unit</th>
                            <th style="width: 12%;">Harga Satuan</th>
                            <th style="width: 10%;">Pajak</th>
                            <th style="width: 14%;">Total</th>
                            <th style="width: 5%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="invoiceTableBody">
                        <tr>
                            <td>
                                <input type="text" class="form-control" value="ALBUM SVT" readonly>
                            </td>
                            <td>
                                <input type="text" class="form-control" value="ALBUM SVT VOL 1" readonly>
                            </td>
                            <td>
                                <input type="number" class="form-control" value="100" readonly>
                            </td>
                            <td>
                                <input type="number" class="form-control qty-billed" value="100" min="0"
                                    required>
                            </td>
                            <td>
                                <input type="text" class="form-control" value="pcs" readonly>
                            </td>
                            <td>
                                <input type="text" class="form-control unit-price-field curr-mask" value="250,000"
                                    required>
                            </td>
                            <td>
                                <select class="global-select2 form-select tax-select" data-allow-clear="true">
                                    @foreach($taxOptions as $tax)
                                        <option value="{{ $tax['value'] }}">{{ $tax['label'] }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control line-total" value="Rp 25,000,000" readonly>
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
                                <input type="text" class="form-control" value="Buku Tulis" readonly>
                            </td>
                            <td>
                                <input type="text" class="form-control" value="Buku Tulis HVS A4" readonly>
                            </td>
                            <td>
                                <input type="number" class="form-control" value="50" readonly>
                            </td>
                            <td>
                                <input type="number" class="form-control qty-billed" value="50" min="0"
                                    required>
                            </td>
                            <td>
                                <input type="text" class="form-control" value="pcs" readonly>
                            </td>
                            <td>
                                <input type="text" class="form-control unit-price-field curr-mask" value="15,000"
                                    required>
                            </td>
                            <td>
                                <select class="global-select2 form-select tax-select" data-allow-clear="true">
                                    @foreach($taxOptions as $tax)
                                        <option value="{{ $tax['value'] }}">{{ $tax['label'] }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control line-total" value="Rp 750,000" readonly>
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

            <!-- Additional Costs Section -->
            <div class="section-header mt-12">
                <h6>Biaya Tambahan</h6>
            </div>

            <div class="mx-n5 my-4">
                <table class="table table-bordered" id="additionalCostsTable">
                    <thead>
                        <tr>
                            <th style="width: 40%;">Jenis Biaya</th>
                            <th style="width: 25%;">Deskripsi</th>
                            <th style="width: 15%;">Jumlah</th>
                            <th style="width: 10%;">Pajak</th>
                            <th style="width: 10%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="additionalCostsTableBody">
                        <tr>
                            <td>
                                <select class="global-select2 form-select cost-type" data-allow-clear="true">
                                    <option value="">Pilih Jenis Biaya</option>
                                    @foreach($costTypes as $costType)
                                        <option value="{{ $costType['value'] }}">{{ $costType['label'] }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="Deskripsi biaya">
                            </td>
                            <td>
                                <input type="text" class="form-control additional-cost-amount curr-mask"
                                    value="0">
                            </td>
                            <td>
                                <select class="global-select2 form-select additional-tax-select" data-allow-clear="true">
                                    @foreach($taxOptions as $tax)
                                        <option value="{{ $tax['value'] }}">{{ $tax['label'] }}</option>
                                    @endforeach
                                </select>
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

            <!-- Add Cost Button -->
            <div class="mb-3">
                <button type="button" class="btn btn-primary" id="addCostRow">
                    <i class="icon-base ti tabler-plus me-1"></i>
                    <span class="align-middle">Tambah Biaya</span>
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
                        <label class="form-label">Catatan Invoice</label>
                        <textarea class="form-control" rows="4" id="invoice-notes"
                            placeholder="Catatan atau keterangan tambahan untuk invoice..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Syarat Pembayaran</label>
                        <textarea class="form-control" rows="3" id="payment-terms" placeholder="Syarat dan ketentuan pembayaran..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lampiran Invoice</label>
                        <input type="file" class="form-control" id="invoice-attachments" accept=".pdf,.jpg,.png"
                            multiple>
                        <small class="form-text text-muted">Upload dokumen pendukung invoice</small>
                    </div>
                </div>

                <!-- Right Side: Summary Section -->
                <div class="col-md-6">
                    <div class="summary-section ps-4">
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Subtotal Barang</label>
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control readonly-field" id="subtotal-items"
                                    dir="rtl" value="Rp 25,750,000" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Biaya Tambahan</label>
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control readonly-field" id="additional-costs-total"
                                    dir="rtl" value="Rp 0" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Total Sebelum Pajak</label>
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control readonly-field" id="subtotal-before-tax"
                                    dir="rtl" value="Rp 25,750,000" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Total Pajak</label>
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control readonly-field" id="total-tax" dir="rtl"
                                    value="Rp 2,832,500" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label"><strong>Total Invoice</strong></label>
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control readonly-field fw-bold" id="total-invoice"
                                    dir="rtl" value="Rp 28,582,500" readonly>
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
            // Initialize currency mask
            // initializeCurrencyMask();

            // Set default invoice date to today
            $('#invoice-date').val(new Date().toISOString().split('T')[0]);

            // Supplier selection change handler
            $('#supplier-select').change(function() {
                const selectedSupplier = $(this).val();
                if (selectedSupplier) {
                    // TODO: Load supplier details
                    console.log('Loading supplier details for:', selectedSupplier);
                }
            });

            // PO lookup button handler
            $('#po-lookup-btn').click(function() {
                // TODO: Open PO lookup modal and populate form
                console.log('Opening PO lookup modal');
            });

            // Calculate line total when quantity billed or price changes
            $(document).on('input', '.qty-billed, .unit-price-field', function() {
                const row = $(this).closest('tr');
                calculateLineTotal(row);
            });

            // Add cost row
            $('#addCostRow').click(function() {
                const costTypeOptions = `
                    <option value="">Pilih Jenis Biaya</option>
                    @foreach($costTypes as $costType)
                        <option value="{{ $costType['value'] }}">{{ $costType['label'] }}</option>
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
                            <select class="global-select2 form-select cost-type" data-allow-clear="true">
                                ${costTypeOptions}
                            </select>
                        </td>
                        <td>
                            <input type="text" class="form-control" placeholder="Deskripsi biaya">
                        </td>
                        <td>
                            <input type="text" class="form-control additional-cost-amount curr-mask" value="0">
                        </td>
                        <td>
                            <select class="global-select2 form-select additional-tax-select" data-allow-clear="true">
                                ${taxOptions}
                            </select>
                        </td>
                        <td>
                            <button class="btn btn-label-danger" type="button" onclick="removeRow(this)">
                                <i class="icon-base ti tabler-x me-1"></i>
                                <span class="align-middle">Hapus</span>
                            </button>
                        </td>
                    </tr>
                `;
                $('#additionalCostsTableBody').append(newRow);
                // initializeCurrencyMask();
            });

            // Remove row function
            window.removeRow = function(button) {
                $(button).closest('tr').remove();
                calculateGrandTotal();
            };

            // Calculate line total
            function calculateLineTotal(row) {
                const quantity = parseFloat(row.find('.qty-billed').val()) || 0;
                const unitPriceText = row.find('.unit-price-field').val().replace(/,/g, '');
                const unitPrice = parseFloat(unitPriceText) || 0;
                const taxType = row.find('.tax-select').val();
                const includeTax = $('#includeTax').is(':checked');

                let subtotal = quantity * unitPrice;
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
                let itemsSubtotal = 0;
                let totalTax = 0;
                let additionalCosts = 0;

                // Calculate items total
                $('#invoiceTableBody tr').each(function() {
                    const quantity = parseFloat($(this).find('.qty-billed').val()) || 0;
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

                    itemsSubtotal += lineSubtotal;
                    totalTax += lineTax;
                });

                // Calculate additional costs
                $('#additionalCostsTableBody tr').each(function() {
                    const costAmountText = $(this).find('.additional-cost-amount').val().replace(/,/g, '');
                    const costAmount = parseFloat(costAmountText) || 0;
                    const taxType = $(this).find('.additional-tax-select').val();

                    additionalCosts += costAmount;

                    if (taxType === 'PPN') {
                        totalTax += costAmount * 0.11;
                    }
                });

                const totalBeforeTax = itemsSubtotal + additionalCosts;
                const grandTotal = totalBeforeTax + totalTax;

                $('#subtotal-items').val(formatCurrency(itemsSubtotal));
                $('#additional-costs-total').val(formatCurrency(additionalCosts));
                $('#subtotal-before-tax').val(formatCurrency(totalBeforeTax));
                $('#total-tax').val(formatCurrency(totalTax));
                $('#total-invoice').val(formatCurrency(grandTotal));
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

            // $(".curr-mask").addEventListener('input', event => {
            //     numeralMask.value = formatNumeral(event.target.value, {
            //         numeralThousandsGroupStyle: 'thousand'
            // //     });
            // // });

            // var cleave = new CleaveZen($('.curr-mask')[0], {
            //     numeral: true,
            //     numeralThousandsGroupStyle: 'thousand'
            // });
        });


        // });
    </script>
@endsection
