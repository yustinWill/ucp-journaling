@extends('layouts.default')
@section('page_title')
    Edit Invoice - {{ $invoice_data->transaction_code }}
@stop
@section('content')
<div class="card mb-6">
    <h4 class="card-header">Edit Invoice - {{ $invoice_data->transaction_code }}</h4>
    <form class="card-body" id="purchaseInvoiceEditForm" autocomplete="off" 
          data-invoice-data="{{ base64_encode(json_encode($invoice_data)) }}"
          data-invoice-products="{{ base64_encode(json_encode($invoice_products)) }}">
        @csrf
        @method('PUT')
        
        <!-- Informasi Dasar Transaksi Section -->
        <div class="section-header mt-12">
            <h6>Informasi Dasar Transaksi</h6>
            <hr class="mx-n6">
        </div>

        <!-- Row 1: Tanggal Invoice, Nomor Invoice -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Tanggal Invoice <span style="color: red;">*</span></label>
                <input type="date" class="form-control global-single-date-picker" id="invoice-date" name="invoice_date" value="{{ $invoice_data->invoice_date }}" required />
            </div>
            <div class="col-md-6">
                <label class="form-label">Nomor Invoice <span style="color: red;">*</span></label>
                <input type="text" class="form-control" name="invoice_number" value="{{ $invoice_data->transaction_code }}" required />
            </div>
        </div>

        <!-- Row 2: Purchase Order Reference -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Purchase Order</label>
                <input type="text" class="form-control" value="{{ $invoice_data->po_reference ?? '-' }}" disabled />
                <small class="form-text text-muted">Purchase Order terkait</small>
            </div>
        </div>

        <!-- Informasi Purchase Order Section -->
        <div class="section-header mt-12">
            <h6>Informasi Purchase Order</h6>
            <hr class="mx-n6">
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Supplier</label>
                <input type="text" class="form-control" value="{{ $invoice_data->supplier_name ?? '-' }}" disabled />
            </div>
            <div class="col-md-6">
                <label class="form-label">Email Supplier</label>
                <input type="text" class="form-control" value="{{ $invoice_data->supplier_email ?? '-' }}" disabled />
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Status PO</label>
                <input type="text" class="form-control" value="{{ $invoice_data->po_status ?? '-' }}" disabled />
            </div>
            <div class="col-md-6">
                <label class="form-label">Grand Total PO</label>
                <input type="text" class="form-control" value="{{ $invoice_data->po_grand_total ? 'Rp ' . number_format($invoice_data->po_grand_total, 0, ',', '.') : '-' }}" disabled />
            </div>
        </div>

        <!-- Informasi Invoice Section -->
        <div class="section-header mt-12">
            <h6>Informasi Invoice</h6>
            <hr class="mx-n6">
        </div>
        
        <!-- Row 2: Transaction and Due Dates -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Tanggal Transaksi <span style="color: red;">*</span></label>
                <input type="date" class="form-control global-single-date-picker" id="transaction-date" name="transaction_date" value="{{ $invoice_data->transaction_date }}" required />
            </div>
            <div class="col-md-6">
                <label class="form-label">Tanggal Jatuh Tempo</label>
                <input type="date" class="form-control" id="due-date" name="due_date" value="{{ $invoice_data->due_date }}" disabled />
                <small class="text-muted">*Dihitung otomatis dari tanggal invoice dan payment terms</small>
            </div>
        </div>
        
        <!-- Row 3: Payment Terms -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Termin Pembayaran</label>
                <input type="text" class="form-control" value="{{ $invoice_data->payment_term_name ?? $invoice_data->payment_terms ?? '-' }}" disabled />
                <small class="text-muted">*Dari Purchase Order</small>
            </div>
            <div class="col-md-6">
                <label class="form-label">Status</label>
                <span class="badge fs-6 py-2 px-3 
                    @if($invoice_data->status == 'DRAFT') bg-label-secondary
                    @elseif($invoice_data->status == 'UNPAID') bg-label-warning
                    @elseif($invoice_data->status == 'PAID') bg-label-success
                    @elseif($invoice_data->status == 'PARTIAL_PAID') bg-label-info
                    @elseif($invoice_data->status == 'OVERDUE') bg-label-danger
                    @elseif($invoice_data->status == 'CANCELLED') bg-label-dark
                    @else bg-label-secondary
                    @endif
                ">{{ $invoice_data->status }}</span>
                <input type="hidden" name="status" value="{{ $invoice_data->status }}" />
            </div>
        </div>

        <!-- Row 4: Keterangan -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Keterangan</label>
                <textarea class="form-control" id="description" name="description" rows="4" placeholder="Keterangan tambahan untuk invoice" maxlength="1000">{{ $invoice_data->description }}</textarea>
            </div>
        </div>
        
        <!-- Row 5: Additional Information -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Memo (Internal)</label>
                <textarea class="form-control" id="internal_memo" name="internal_memo" rows="4" placeholder="Catatan internal..." maxlength="1000">{{ $invoice_data->internal_memo }}</textarea>
            </div>
        </div>
        
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Tag</label>
                <select class="global-select2 form-select" id="tags" name="tags[]" multiple data-allow-clear="true">
                    @foreach($availableTags as $tag)
                        <option value="{{ $tag->id }}" 
                            @if($invoice_data->f_tag_codes && in_array($tag->id, $invoice_data->f_tag_codes)) selected @endif>
                            {{ $tag->code }} - {{ $tag->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Lampiran</label>
                <input type="file" class="form-control" id="attachment" name="attachment" accept=".xlsx,.xls,.doc,.docx,.pdf,.png,.jpg,.jpeg,.zip" />
                @if($invoice_data->attachment)
                    <small class="form-text text-muted">
                        File saat ini: <a href="{{ Storage::url($invoice_data->attachment) }}" target="_blank">{{ basename($invoice_data->attachment) }}</a>
                    </small>
                @endif
                <small class="form-text text-muted">File dapat berupa Excel, Word, PDF, PNG, atau Zip (Max: 10MB)</small>
            </div>
        </div>

        <!-- Items Invoice Section -->
        <div class="section-header mt-12">
            <h6>Items Invoice</h6>
        </div>

        <!-- Info Box -->
        <div class="alert alert-warning">
            <i class="icon-base ti tabler-info-circle me-2"></i>
            Catatan: Item dan quantity tidak dapat diubah setelah invoice dibuat. Hanya catatan produk yang dapat diubah.
        </div>

        <!-- Product Table -->
        <div class="mb-4 mx-n5">
            <div class="table-responsive">
                <table class="table table-bordered" id="invoice-items-table">
                    <thead>
                        <tr>
                            <th style="width: 30%;">Produk</th>
                            <th style="width: 12%;">Qty PO</th>
                            <th style="width: 12%;">Qty Invoice</th>
                            <th style="width: 15%;">Unit Price</th>
                            <th style="width: 15%;">Total</th>
                            <th style="width: 16%;">Note</th>
                        </tr>
                    </thead>
                <tbody id="invoice-items-tbody">
                    @forelse($invoice_products as $index => $product)
                        <tr>
                            <td>{{ $product->product_code ?? '' }} - {{ $product->product_name ?? '' }}</td>
                            <td>{{ $product->ordered_qty ?? 0 }} {{ $product->unit_name ?? '' }}</td>
                            <td>{{ $product->qty ?? 0 }} {{ $product->unit_name ?? '' }}</td>
                            <td>{{ number_format($product->unit_price ?? 0, 0, ',', '.') }}</td>
                            <td>{{ number_format($product->total_price ?? 0, 0, ',', '.') }}</td>
                            <td>
                                <input type="text" class="form-control form-control-sm" 
                                       name="products[{{ $index }}][note]" 
                                       value="{{ $product->note ?? '' }}" 
                                       placeholder="Catatan produk" />
                                <input type="hidden" name="products[{{ $index }}][id]" value="{{ $product->id }}" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="icon-base ti tabler-package me-2"></i>
                                Tidak ada data produk
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if(count($invoice_products) > 0)
                <tfoot class="table-light">
                    <tr>
                        <th colspan="4" class="text-end">Total Invoice:</th>
                        <th class="text-end">
                            Rp {{ number_format($invoice_products->sum('total_price') ?? 0, 0, ',', '.') }}
                        </th>
                        <th></th>
                    </tr>
                </tfoot>
                @endif
            </table>
            </div>
        </div>

        <!-- Hidden fields for reference -->
        <input type="hidden" name="id" value="{{ $invoice_data->id }}" />
        <input type="hidden" name="purchase_order_id" value="{{ $invoice_data->f_purchase_order_id }}" />

        <!-- Action Buttons -->
        <div class="pt-6 text-end">
            <a href="{{ route('main_transaction.purchase_invoice.detail', $invoice_data->id) }}" class="btn btn-label-secondary me-3">Batal</a>
            <button type="submit" class="btn btn-primary" id="update-invoice-btn">Update Invoice</button>
        </div>
    </form>
</div>
@stop

@section('js_script')
<script>
$(function() {
    // Initialize the form
    initializeEditForm();
    
    function initializeEditForm() {
        // Load existing data from data attributes
        const form = $('#purchaseInvoiceEditForm');
        const invoiceDataEncoded = form.data('invoice-data');
        const invoiceProductsEncoded = form.data('invoice-products');
        
        let invoiceData = null;
        let invoiceProducts = [];
        
        if (invoiceDataEncoded) {
            try {
                invoiceData = JSON.parse(atob(invoiceDataEncoded));
            } catch (e) {
                console.error('Error parsing invoice data:', e);
            }
        }
        
        if (invoiceProductsEncoded) {
            try {
                invoiceProducts = JSON.parse(atob(invoiceProductsEncoded));
            } catch (e) {
                console.error('Error parsing invoice products:', e);
            }
        }
        
        // Event handlers
        $('#invoice-date').on('change', calculateDueDate);
        $('#transaction-date').on('change', validateTransactionDate);
        $('#purchaseInvoiceEditForm').on('submit', submitEditForm);
        
        // Calculate due date initially
        calculateDueDate();
    }
    
    function calculateDueDate() {
        const invoiceDate = $('#invoice-date').val();
        const paymentTerms = "{{ $invoice_data->payment_term_name ?? $invoice_data->payment_terms ?? '' }}";
        
        if (invoiceDate && paymentTerms) {
            // Extract days from payment terms (e.g., "NET 30" -> 30 days)
            const match = paymentTerms.match(/\d+/);
            if (match) {
                const days = parseInt(match[0]);
                const date = new Date(invoiceDate);
                date.setDate(date.getDate() + days);
                $('#due-date').val(date.toISOString().split('T')[0]);
            }
        }
    }
    
    function validateTransactionDate() {
        const invoiceDate = new Date($('#invoice-date').val());
        const transactionDate = new Date($('#transaction-date').val());
        
        if (transactionDate < invoiceDate) {
            Swal.fire({
                title: 'Tanggal Invalid!',
                text: 'Tanggal transaksi tidak boleh lebih awal dari tanggal invoice.',
                icon: 'warning',
                customClass: {
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false
            });
            
            $('#transaction-date').val($('#invoice-date').val());
        }
    }
    
    function submitEditForm(e) {
        e.preventDefault();
        
        // Validation
        if (!validateEditForm()) {
            return;
        }
        
        // Show confirmation
        Swal.fire({
            title: 'Update Invoice?',
            text: 'Pastikan perubahan yang dilakukan sudah benar.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Update!',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-primary',
                cancelButton: 'btn btn-label-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                processEditFormSubmission();
            }
        });
    }
    
    function validateEditForm() {
        const requiredFields = ['invoice-date', 'transaction-date'];
        let isValid = true;
        
        requiredFields.forEach(fieldId => {
            const field = $('#' + fieldId);
            if (!field.val() || field.val().trim() === '') {
                field.addClass('is-invalid');
                isValid = false;
            } else {
                field.removeClass('is-invalid');
            }
        });
        
        if (!isValid) {
            Swal.fire({
                title: 'Field Wajib!',
                text: 'Silakan lengkapi semua field yang wajib diisi.',
                icon: 'warning',
                customClass: {
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false
            });
        }
        
        return isValid;
    }
    
    function processEditFormSubmission() {
        const submitBtn = $('#update-invoice-btn');
        const originalText = submitBtn.text();
        
        // Show loading state
        submitBtn.prop('disabled', true).html(
            '<span class="spinner-border spinner-border-sm me-2"></span>Memperbarui...'
        );
        
        // Prepare form data
        const formData = new FormData($('#purchaseInvoiceEditForm')[0]);
        
        // Submit via AJAX
        $.ajax({
            url: "{{ route('main_transaction.purchase_invoice.update') }}",
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Invoice berhasil diperbarui.',
                    icon: 'success',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                }).then(() => {
                    window.location.href = "{{ route('main_transaction.purchase_invoice.detail', $invoice_data->id) }}";
                });
            },
            error: function(xhr) {
                let errorMessage = 'Terjadi kesalahan saat memperbarui invoice.';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    title: 'Gagal!',
                    text: errorMessage,
                    icon: 'error',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                });
            },
            complete: function() {
                submitBtn.prop('disabled', false).text(originalText);
            }
        });
    }
});
</script>
@endsection