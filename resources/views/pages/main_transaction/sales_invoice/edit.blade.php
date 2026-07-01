@extends('layouts.default')
@section('page_title')
    Edit Invoice
@stop
@section('content')
<div class="card mb-6">
    <h4 class="card-header">
        Edit Sales Invoice
        <small class="text-muted d-block">{{ $invoice_data->transaction_code }}</small>
    </h4>
    <form class="card-body" id="salesInvoiceEditForm">
        
        <!-- Note: Limited Edit Capability -->
        <div class="alert alert-info">
            <i class="ti ti-info-circle me-2"></i>
            <strong>Informasi:</strong> Edit invoice hanya dapat mengubah tanggal, keterangan, memo internal, dan lampiran. Data produk tidak dapat diubah setelah invoice dibuat.
        </div>

        <!-- Section 1: Sales Order Reference Information (Read-only) -->
        <div class="section-header mt-12">
            <h6>Referensi Sales Order (Tidak dapat diubah)</h6>
            <hr class="mx-n6">
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Customer</label>
                <input type="text" class="form-control" value="{{ $invoice_data->customer_name }}" readonly />
            </div>
            <div class="col-md-6">
                <label class="form-label">Referensi SO</label>
                <input type="text" class="form-control" value="{{ $invoice_data->so_reference }}" readonly />
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Nomor Invoice</label>
                <input type="text" class="form-control" value="{{ $invoice_data->transaction_code }}" readonly />
            </div>
            <div class="col-md-6">
                <label class="form-label">Grand Total</label>
                <input type="text" class="form-control" value="Rp {{ number_format($invoice_data->grand_total, 0, ',', '.') }}" readonly />
            </div>
        </div>

        <!-- Section 2: Editable Invoice Information -->
        <div class="section-header mt-12">
            <h6>Informasi Invoice (Dapat diubah)</h6>
            <hr class="mx-n6">
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Tanggal Invoice <span style="color: red;">*</span></label>
                <input type="date" class="form-control" name="invoice_date" id="invoice_date" 
                       value="{{ \Carbon\Carbon::parse($invoice_data->invoice_date)->format('Y-m-d') }}" required />
            </div>
            <div class="col-md-6">
                <label class="form-label">Tanggal Transaksi <span style="color: red;">*</span></label>
                <input type="date" class="form-control" name="transaction_date" id="transaction_date" 
                       value="{{ \Carbon\Carbon::parse($invoice_data->transaction_date)->format('Y-m-d') }}" required />
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Tanggal Jatuh Tempo <span style="color: red;">*</span></label>
                <input type="date" class="form-control" name="due_date" id="due_date" 
                       value="{{ \Carbon\Carbon::parse($invoice_data->due_date)->format('Y-m-d') }}" required />
            </div>
            <div class="col-md-6">
                <label class="form-label">Status</label>
                <input type="text" class="form-control" value="{{ $invoice_data->status }}" readonly />
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-12">
                <label class="form-label">Keterangan</label>
                <textarea class="form-control" name="description" id="description" rows="4" 
                          placeholder="Keterangan tambahan untuk Invoice" maxlength="1000">{{ $invoice_data->description }}</textarea>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Internal Memo</label>
                <textarea class="form-control" name="internal_memo" id="internal_memo" rows="3" 
                          placeholder="Memo internal" maxlength="500">{{ $invoice_data->internal_memo }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Tags</label>
                <select class="form-select global-select2" name="tags[]" id="tags" multiple>
                    @foreach($availableTags as $tag)
                        @php
                            $selected = false;
                            if ($invoice_data->f_tag_codes) {
                                $selectedTags = is_string($invoice_data->f_tag_codes) 
                                    ? json_decode($invoice_data->f_tag_codes, true) 
                                    : $invoice_data->f_tag_codes;
                                $selected = $selectedTags && in_array($tag->id, $selectedTags);
                            }
                        @endphp
                        <option value="{{ $tag->id }}" {{ $selected ? 'selected' : '' }}>{{ $tag->code }} - {{ $tag->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row g-3 mb-6">
            <div class="col-md-6">
                <label class="form-label">Lampiran</label>
                <input type="file" class="form-control" name="attachment" id="attachment" 
                       accept=".xlsx,.xls,.doc,.docx,.pdf,.png,.jpg,.jpeg,.zip" />
                <small class="text-muted">Max 10MB. Format: xlsx, xls, doc, docx, pdf, png, jpg, jpeg, zip</small>
                @if($invoice_data->attachment)
                    <div class="mt-2">
                        <small class="text-muted">Lampiran saat ini:</small>
                        <a href="{{ $invoice_data->attachment }}" target="_blank" class="d-block">
                            <i class="ti ti-paperclip me-1"></i>
                            Lihat lampiran
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Section 3: Items Invoice (Read-only) -->
        <div class="section-header mt-12">
            <h6>Items Invoice (Tidak dapat diubah)</h6>
        </div>

        <div class="alert alert-warning">
            <i class="ti ti-alert-triangle me-2"></i>
            Data produk tidak dapat diubah setelah invoice dibuat. Untuk mengubah produk, batalkan invoice ini dan buat invoice baru.
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th class="text-center">Qty Invoice</th>
                        <th class="text-center">Unit</th>
                        <th class="text-end">Harga Satuan</th>
                        <th class="text-end">Total</th>
                        <th>Note</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoice_products as $product)
                        <tr>
                            <td><strong>{{ $product->product_name }}</strong></td>
                            <td class="text-center">
                                @php
                                    $qty = $product->qty;
                                    if ($qty == floor($qty)) {
                                        $formattedQty = number_format($qty, 0, ',', '.');
                                    } else {
                                        $formatted = number_format($qty, 4, ',', '.');
                                        $formattedQty = rtrim(rtrim($formatted, '0'), ',');
                                    }
                                @endphp
                                <span class="badge bg-label-info">{{ $formattedQty }}</span>
                            </td>
                            <td class="text-center">{{ $product->unit_code ?: '-' }}</td>
                            <td class="text-end">Rp {{ number_format($product->unit_price, 0, ',', '.') }}</td>
                            <td class="text-end">
                                <strong>Rp {{ number_format($product->line_total, 0, ',', '.') }}</strong>
                            </td>
                            <td>{{ $product->product_invoice_note ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="ti ti-package-off fs-1"></i>
                                <div class="mt-2">Tidak ada item dalam invoice ini</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($invoice_products->count() > 0)
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-end">Total Invoice:</th>
                            <th class="text-end">
                                <span class="h5 text-primary">Rp {{ number_format($invoice_data->grand_total, 0, ',', '.') }}</span>
                            </th>
                            <th></th>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex justify-content-end gap-3 mt-6">
            <a href="{{ route('main_transaction.sales_invoice.detail', $invoice_data->id) }}" class="btn btn-secondary">
                <i class="ti ti-arrow-left me-1"></i>
                Batal
            </a>
            <button type="submit" class="btn btn-primary" id="save-invoice-btn">
                <i class="ti ti-device-floppy me-1"></i>
                Simpan Perubahan
            </button>
        </div>

    </form>
</div>
@stop

@section('js_script')
<script>
$(document).ready(function() {
    // Form submission
    $('#salesInvoiceEditForm').on('submit', function(e) {
        e.preventDefault();
        submitInvoice();
    });
});

function submitInvoice() {
    // Prepare form data
    const formData = new FormData(document.getElementById('salesInvoiceEditForm'));
    formData.append('_method', 'PUT');
    
    // Submit via AJAX
    $.ajax({
        url: '{{ route("main_transaction.sales_invoice.update", $invoice_data->id) }}',
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
                    window.location.href = '{{ route("main_transaction.sales_invoice.detail", $invoice_data->id) }}';
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
            $('#save-invoice-btn').prop('disabled', false).html('<i class="ti ti-device-floppy me-1"></i> Simpan Perubahan');
        }
    });
}
</script>
@endsection
