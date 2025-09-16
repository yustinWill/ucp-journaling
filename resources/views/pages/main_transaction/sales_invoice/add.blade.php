@extends('layouts.default')
@section('page_title')
    Tambah Invoice
@stop
@section('content')
<div class="row">
    <!-- LEFT COLUMN (COL 8) - Main Form -->
    <div class="col-lg-8">
        <div class="card mb-6">
            <h4 class="card-header">Tambah Invoice</h4>
            <form class="card-body" id="salesInvoiceForm">
                
                <!-- Section 1: Sales Order Reference Information -->
                <div class="section-header mt-12">
                    <h6>Referensi Sales Order</h6>
                    <hr class="mx-n6">
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Sales Order <span style="color: red;">*</span></label>
                        <select class="form-select global-select2" name="sales_order_id" id="sales_order_id" data-allow-clear="false" required>
                            <option value="">Pilih Sales Order</option>
                            @foreach($availableSalesOrders as $so)
                                <option value="{{ $so['value'] }}" data-so-data="{{ json_encode($so) }}">{{ $so['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nomor Sales Order</label>
                        <input type="text" class="form-control" id="so_reference_no" disabled />
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Customer</label>
                        <input type="text" class="form-control" id="customer_name" disabled />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status SO</label>
                        <input type="text" class="form-control" id="so_status" disabled />
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Sales Order</label>
                        <input type="text" class="form-control" id="so_date" disabled />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Total SO</label>
                        <input type="text" class="form-control" id="so_total" disabled />
                    </div>
                </div>

                <div class="row g-3 mb-6">
                    <div class="col-md-6">
                        <label class="form-label">Termin Pembayaran</label>
                        <input type="text" class="form-control" id="so_payment_terms" disabled />
                        <small class="text-muted">*Dari Sales Order</small>
                    </div>
                </div>

                <!-- Section 2: Invoice Information -->
                <div class="section-header mt-12">
                    <h6>Informasi Invoice</h6>
                    <hr class="mx-n6">
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Nomor Invoice <span style="color: red;">*</span></label>
                        <input type="text" class="form-control" name="invoice_number" id="invoice_number" placeholder="INV-003" required />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Invoice <span style="color: red;">*</span></label>
                        <input type="date" class="form-control" name="invoice_date" id="invoice_date" required />
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Transaksi <span style="color: red;">*</span></label>
                        <input type="date" class="form-control" name="transaction_date" id="transaction_date" required />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Jatuh Tempo</label>
                        <input type="date" class="form-control" name="due_date" id="due_date" disabled />
                        <small class="text-muted">*Otomatis dari Sales Order</small>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Keterangan</label>
                        <textarea class="form-control" name="description" id="description" rows="3" placeholder="Keterangan tambahan untuk Invoice" maxlength="1000"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Internal Memo</label>
                        <textarea class="form-control" name="memo" id="memo" rows="3" placeholder="Memo internal" maxlength="500"></textarea>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    
                    <div class="col-md-6">
                        <label class="form-label">Tags</label>
                        <select class="form-select global-select2" name="tags[]" id="tags" multiple>
                            @foreach($availableTags as $tag)
                                <option value="{{ $tag->id }}">{{ $tag->code }} - {{ $tag->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Lampiran</label>
                        <input type="file" class="form-control" name="attachment" id="attachment" accept=".xlsx,.xls,.doc,.docx,.pdf,.png,.jpg,.jpeg,.zip" />
                        <small class="text-muted">Max 10MB. Format: xlsx, xls, doc, docx, pdf, png, jpg, jpeg, zip</small>
                    </div>
                </div>

                <div class="row g-3 mb-6">
                    
                </div>

                <!-- Section 3: Items Invoice -->
                <div class="section-header mt-12">
                    <h6>Items Invoice</h6>
                </div>

                <div class="alert alert-info" id="invoice-instructions" style="display: none;">
                    <i class="ti ti-info-circle me-2"></i>
                    Pilih dan masukkan quantity item yang akan di-invoice dari Sales Order. Sistem akan otomatis menghitung total amount.
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" id="invoice-items-table" style="display: none;">
                        <thead>
                            <tr>
                                <th style="width: 30%;">Produk</th>
                                <th style="width: 15%;">Qty SO</th>
                                <th style="width: 15%;">Qty Invoice</th>
                                <th style="width: 20%;">Harga Satuan</th>
                                <th style="width: 20%;">Total</th>
                            </tr>
                        </thead>
                        <tbody id="invoice-items-body">
                            <!-- Dynamic content will be loaded here -->
                        </tbody>
                    </table>
                </div>

                <div class="row g-3 mt-4" id="invoice-summary" style="display: none;">
                    <div class="col-md-6">
                        <div class="fw-bold h5 text-primary">
                            Total Invoice Amount: <span id="total_invoice_amount">Rp 0</span>
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="fw-bold h5 text-secondary">
                            Total Items Invoice: <span id="total_items_invoice">0 Items</span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-end gap-3 mt-6">
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='{{ route('main_transaction.sales_invoice.index') }}';">
                        <i class="ti ti-arrow-left me-1"></i>
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary" id="save-invoice-btn">
                        <i class="ti ti-briefcase me-1"></i>
                        Simpan Invoice
                    </button>
                </div>

            </form>
        </div>
    </div>

    <!-- RIGHT COLUMN (COL 4) - Requirements Information Panel -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Requirement Sales Invoice (SI)</h5>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h6 class="text-primary mb-2">1. Relasi dengan Sales Order (SO)</h6>
                    <ul class="list-unstyled small text-muted">
                        <li>• Sales Invoice (SI) hanya bisa dibuat jika ada Sales Order (SO)</li>
                        <li>• Saat user buat SI, user harus pilih referensi SO terlebih dahulu</li>
                        <li>• Semua data dasar (Customer, alamat pengiriman, tanggal SO, item SO, qty SO, dll.) autofill dari SO</li>
                    </ul>
                </div>
                
                <div class="mb-4">
                    <h6 class="text-primary mb-2">Business Rules</h6>
                    <ul class="list-unstyled small text-muted">
                        <li>• Invoice hanya bisa dibuat berdasarkan SO yang Approved</li>
                        <li>• Bisa multiple invoice selama masih ada qty tersisa pada SO</li>
                        <li>• Sistem menghitung otomatis jatuh tempo berdasarkan termin pembayaran</li>
                        <li>• Jika total invoice = 0, sistem tidak boleh menyimpan invoice</li>
                    </ul>
                </div>
                
                <div class="alert alert-warning small">
                    <i class="ti ti-alert-triangle me-2"></i>
                    <strong>Penting:</strong> Pastikan memilih Sales Order terlebih dahulu sebelum mengisi data invoice.
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js_script')
<script>
$(document).ready(function() {
    // Initialize date inputs with today's date
    const today = new Date().toISOString().split('T')[0];
    $('#invoice_date').val(today);
    $('#transaction_date').val(today);

    // Generate invoice number
    generateInvoiceNumber();

    // Sales Order selection change event
    $('#sales_order_id').on('change', function() {
        const selectedOption = $(this).find(':selected');
        const soData = selectedOption.data('so-data');
        
        if (soData) {
            populateSOData(soData);
            calculateDueDate();
        } else {
            clearSOData();
        }
    });

    // Transaction date change - recalculate due date
    $('#transaction_date').on('change', function() {
        calculateDueDate();
    });

    // Form submission
    $('#salesInvoiceForm').on('submit', function(e) {
        e.preventDefault();
        submitInvoice();
    });
});

function generateInvoiceNumber() {
    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
    $('#invoice_number').val(`INV-${year}${month}-${random}`);
}

function populateSOData(soData) {
    // Populate SO reference fields
    $('#so_reference_no').val(soData.reference_no);
    $('#customer_name').val(soData.customer);
    $('#so_status').val('Approved');
    $('#so_date').val(formatDate(new Date()));
    $('#so_total').val(formatRupiah(soData.total));
    $('#so_payment_terms').val(soData.payment_terms);
    
    // Show invoice instructions and items table
    $('#invoice-instructions').show();
    $('#invoice-items-table').show();
    $('#invoice-summary').show();
    
    // Load SO items (mock data for now)
    loadSOItems(soData);
}

function clearSOData() {
    // Clear SO reference fields
    $('#so_reference_no, #customer_name, #so_status, #so_date, #so_total, #so_payment_terms').val('');
    $('#due_date').val('');
    
    // Hide invoice sections
    $('#invoice-instructions, #invoice-items-table, #invoice-summary').hide();
    
    // Clear items table
    $('#invoice-items-body').empty();
    updateInvoiceTotals();
}

function loadSOItems(soData) {
    // Mock SO items - in real implementation, this would be loaded via AJAX
    const mockItems = [
        {
            id: 1,
            product_id: 1,
            product_name: 'Botol Air Mineral 600ml',
            qty_so: 1000,
            unit_price: 2500,
            remaining_qty: 1000
        },
        {
            id: 2,
            product_id: 2,
            product_name: 'Galon Air Mineral 19L',
            qty_so: 50,
            unit_price: 25000,
            remaining_qty: 50
        },
        {
            id: 3,
            product_id: 3,
            product_name: 'Cup Air Mineral 240ml',
            qty_so: 500,
            unit_price: 1500,
            remaining_qty: 500
        }
    ];
    
    let itemsHtml = '';
    mockItems.forEach(function(item) {
        itemsHtml += `
            <tr data-item-id="${item.id}">
                <td>
                    <strong>${item.product_name}</strong>
                    <input type="hidden" name="products[${item.id}][sales_order_product_id]" value="${item.id}">
                    <input type="hidden" name="products[${item.id}][product_id]" value="${item.product_id}">
                </td>
                <td class="text-center">
                    <span class="badge bg-label-info">${item.qty_so.toLocaleString('id-ID')}</span>
                    <input type="hidden" name="products[${item.id}][po_qty]" value="${item.qty_so}">
                </td>
                <td>
                    <input type="number" class="form-control invoice-qty" 
                           name="products[${item.id}][invoice_qty]" 
                           min="0" max="${item.remaining_qty}" value="0" 
                           data-item-id="${item.id}" data-unit-price="${item.unit_price}">
                    <input type="hidden" name="products[${item.id}][remaining_qty_before]" value="${item.remaining_qty}">
                </td>
                <td class="text-end">
                    ${formatRupiah(item.unit_price)}
                    <input type="hidden" name="products[${item.id}][unit_price]" value="${item.unit_price}">
                </td>
                <td class="text-end">
                    <strong class="item-total" data-item-id="${item.id}">${formatRupiah(0)}</strong>
                </td>
            </tr>
        `;
    });
    
    $('#invoice-items-body').html(itemsHtml);
    
    // Bind quantity change events
    $('.invoice-qty').on('input', function() {
        const itemId = $(this).data('item-id');
        const unitPrice = $(this).data('unit-price');
        const qty = parseInt($(this).val()) || 0;
        const total = qty * unitPrice;
        
        $(`.item-total[data-item-id="${itemId}"]`).text(formatRupiah(total));
        updateInvoiceTotals();
    });
    
    updateInvoiceTotals();
}

function updateInvoiceTotals() {
    let totalAmount = 0;
    let totalItems = 0;
    
    $('.invoice-qty').each(function() {
        const qty = parseInt($(this).val()) || 0;
        const unitPrice = $(this).data('unit-price') || 0;
        
        if (qty > 0) {
            totalAmount += qty * unitPrice;
            totalItems++;
        }
    });
    
    $('#total_invoice_amount').text(formatRupiah(totalAmount));
    $('#total_items_invoice').text(`${totalItems} Items`);
    
    // Enable/disable save button based on total
    $('#save-invoice-btn').prop('disabled', totalAmount === 0);
}

function calculateDueDate() {
    const transactionDate = $('#transaction_date').val();
    const paymentTerms = $('#so_payment_terms').val();
    
    if (transactionDate && paymentTerms) {
        const date = new Date(transactionDate);
        const termMatch = paymentTerms.match(/NET (\d+)/i);
        
        if (termMatch) {
            const days = parseInt(termMatch[1]);
            date.setDate(date.getDate() + days);
            $('#due_date').val(date.toISOString().split('T')[0]);
        }
    }
}

function submitInvoice() {
    // Validate total amount
    const totalAmount = parseFloat($('#total_invoice_amount').text().replace(/[^\d]/g, '')) || 0;
    if (totalAmount === 0) {
        Swal.fire({
            title: 'Error!',
            text: 'Total invoice harus lebih dari 0',
            icon: 'error'
        });
        return;
    }
    
    // Prepare form data
    const formData = new FormData(document.getElementById('salesInvoiceForm'));
    
    // Add additional data
    formData.append('total_amount', totalAmount);
    formData.append('total_items', $('#total_items_invoice').text().split(' ')[0]);
    
    // Submit via AJAX
    $.ajax({
        url: '{{ route("main_transaction.sales_invoice.store") }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function() {
            $('#save-invoice-btn').prop('disabled', true).html('<i class="ti ti-loader me-1"></i> Menyimpan...');
        },
        success: function(response) {
            Swal.fire({
                title: 'Berhasil!',
                text: response.message,
                icon: 'success',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '{{ route("main_transaction.sales_invoice.index") }}';
                }
            });
        },
        error: function(xhr) {
            let message = 'Terjadi kesalahan saat menyimpan invoice';
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
            $('#save-invoice-btn').prop('disabled', false).html('<i class="ti ti-briefcase me-1"></i> Simpan Invoice');
        }
    });
}

function formatRupiah(amount) {
    return 'Rp ' + amount.toLocaleString('id-ID');
}

function formatDate(date) {
    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}
</script>
@endsection