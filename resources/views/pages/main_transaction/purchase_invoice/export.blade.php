@extends('layouts.default')
@section('page_title')
    Export Purchase Invoice
@stop
@section('content')
    <div class="card mb-6">
        <h5 class="card-header">Export Purchase Invoice</h5>
        <form class="card-body" id="exportForm">
            <!-- Instructions Section -->
            <div class="row gy-3 mt-0">
                <div class="col-md-12">
                    <div class="alert alert-primary alert-dismissible d-flex" role="alert">
                        <span class="alert-icon rounded">
                            <i class="icon-base ti tabler-bell icon-md w-px-30"></i>
                        </span>
                        <div class="d-flex flex-column ps-1">
                            <h5 class="alert-heading mb-2"><b>Export Data Purchase Invoice!</b></h5>
                            <p class="mb-0">
                                Fitur ini memungkinkan Anda untuk mengekspor data purchase invoice dalam berbagai format.
                                Anda dapat memfilter data berdasarkan kriteria tertentu dan memilih format file yang diinginkan.
                            </p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            </div>

            <!-- Export Format Selection -->
            <div class="section-header mt-12">
                <h6>Format Export</h6>
                <hr class="mx-n6">
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <label class="form-label">Pilih Format <span style="color: red;">*</span></label>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="export_format" id="format-excel" value="excel" checked>
                                <label class="form-check-label" for="format-excel">
                                    <i class="icon-base ti tabler-file-type-xls me-2 text-success"></i>
                                    Excel (.xlsx)
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="export_format" id="format-csv" value="csv">
                                <label class="form-check-label" for="format-csv">
                                    <i class="icon-base ti tabler-file-type-csv me-2 text-primary"></i>
                                    CSV (.csv)
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="export_format" id="format-pdf" value="pdf">
                                <label class="form-check-label" for="format-pdf">
                                    <i class="icon-base ti tabler-file-type-pdf me-2 text-danger"></i>
                                    PDF (.pdf)
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Date Range Filter -->
            <div class="section-header mt-12">
                <h6>Filter Tanggal</h6>
                <hr class="mx-n6">
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" class="form-control global-single-date-picker" id="start_date" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Akhir</label>
                    <input type="date" class="form-control global-single-date-picker" id="end_date" />
                </div>
            </div>

            <!-- Advanced Filters -->
            <div class="section-header mt-12">
                <h6>Filter Lanjutan</h6>
                <hr class="mx-n6">
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Supplier</label>
                    <select class="global-select2 form-select" id="supplier_filter" data-allow-clear="true" multiple>
                        <option value="">Semua Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier['value'] }}">{{ $supplier['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select class="global-select2 form-select" id="status_filter" data-allow-clear="true" multiple>
                        <option value="">Semua Status</option>
                        <option value="DRAFT">DRAFT - Draft</option>
                        <option value="PENDING_PAYMENT">PENDING_PAYMENT - Menunggu Pembayaran</option>
                        <option value="PAID">PAID - Lunas</option>
                        <option value="PARTIAL_PAID">PARTIAL_PAID - Sebagian Dibayar</option>
                        <option value="OVERDUE">OVERDUE - Jatuh Tempo</option>
                        <option value="CANCELLED">CANCELLED - Dibatalkan</option>
                    </select>
                </div>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Payment Terms</label>
                    <select class="global-select2 form-select" id="payment_terms_filter" data-allow-clear="true" multiple>
                        <option value="">Semua Payment Terms</option>
                        @foreach($paymentTerms as $term)
                            <option value="{{ $term['value'] }}">{{ $term['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Range Total Invoice</label>
                    <div class="row g-2">
                        <div class="col">
                            <input type="number" class="form-control" id="min_amount" placeholder="Minimal" min="0" step="0.01">
                        </div>
                        <div class="col-auto d-flex align-items-center">
                            <span>-</span>
                        </div>
                        <div class="col">
                            <input type="number" class="form-control" id="max_amount" placeholder="Maksimal" min="0" step="0.01">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Column Selection -->
            <div class="section-header mt-12">
                <h6>Pilih Kolom Export</h6>
                <hr class="mx-n6">
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" id="col-reference-no" value="reference_no" checked>
                                <label class="form-check-label" for="col-reference-no">No. Invoice</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" id="col-date" value="date" checked>
                                <label class="form-check-label" for="col-date">Tanggal</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" id="col-supplier" value="supplier" checked>
                                <label class="form-check-label" for="col-supplier">Supplier</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" id="col-status" value="status" checked>
                                <label class="form-check-label" for="col-status">Status</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" id="col-po-reference" value="po_reference" checked>
                                <label class="form-check-label" for="col-po-reference">PO Reference</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" id="col-pd-reference" value="pd_reference">
                                <label class="form-check-label" for="col-pd-reference">PD Reference</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" id="col-due-date" value="due_date" checked>
                                <label class="form-check-label" for="col-due-date">Tanggal Jatuh Tempo</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" id="col-currency" value="currency">
                                <label class="form-check-label" for="col-currency">Currency</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" id="col-subtotal" value="subtotal">
                                <label class="form-check-label" for="col-subtotal">Subtotal</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" id="col-tax-amount" value="tax_amount">
                                <label class="form-check-label" for="col-tax-amount">Tax Amount</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" id="col-discount" value="discount">
                                <label class="form-check-label" for="col-discount">Discount</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" id="col-invoice-amount" value="invoice_amount" checked>
                                <label class="form-check-label" for="col-invoice-amount">Invoice Amount</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" id="col-paid-amount" value="paid_amount" checked>
                                <label class="form-check-label" for="col-paid-amount">Paid Amount</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" id="col-remaining-amount" value="remaining_amount" checked>
                                <label class="form-check-label" for="col-remaining-amount">Remaining Amount</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" id="col-payment-terms" value="payment_terms">
                                <label class="form-check-label" for="col-payment-terms">Payment Terms</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" id="col-payment-method" value="payment_method">
                                <label class="form-check-label" for="col-payment-method">Payment Method</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="select-all-columns">
                            <i class="icon-base ti tabler-check-square me-1"></i>Pilih Semua
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="deselect-all-columns">
                            <i class="icon-base ti tabler-square me-1"></i>Hapus Semua
                        </button>
                    </div>
                </div>
            </div>

            <!-- Export Preview -->
            <div class="section-header mt-12">
                <h6>Preview Export</h6>
                <hr class="mx-n6">
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <div class="alert alert-info" role="alert">
                        <i class="icon-base ti tabler-info-circle me-2"></i>
                        <span id="preview-text">Siap untuk export data purchase invoice dengan kriteria yang dipilih.</span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-end gap-3 mt-6">
                <a href="{{ route('main_transaction.purchase_invoice.index') }}" class="btn btn-outline-secondary waves-effect">
                    <i class="icon-base ti tabler-arrow-left me-2"></i>
                    Kembali ke List
                </a>
                <button type="submit" class="btn btn-primary waves-effect waves-light">
                    <i class="icon-base ti tabler-download me-2"></i>
                    Export Data
                </button>
            </div>
        </form>
    </div>
@stop

@section('js_script')
<script>
$(function() {
    // Column selection handlers
    $('#select-all-columns').on('click', function() {
        $('.column-checkbox').prop('checked', true);
        updatePreviewText();
    });

    $('#deselect-all-columns').on('click', function() {
        $('.column-checkbox').prop('checked', false);
        updatePreviewText();
    });

    $('.column-checkbox').on('change', function() {
        updatePreviewText();
    });

    // Format change handler
    $('input[name="export_format"]').on('change', function() {
        updatePreviewText();
    });

    // Filter change handlers
    $('#supplier_filter, #status_filter, #payment_terms_filter, #start_date, #end_date, #min_amount, #max_amount').on('change', function() {
        updatePreviewText();
    });

    function updatePreviewText() {
        const format = $('input[name="export_format"]:checked').val();
        const selectedColumns = $('.column-checkbox:checked').length;
        const totalColumns = $('.column-checkbox').length;
        
        let filterCount = 0;
        if ($('#supplier_filter').val() && $('#supplier_filter').val().length > 0) filterCount++;
        if ($('#status_filter').val() && $('#status_filter').val().length > 0) filterCount++;
        if ($('#payment_terms_filter').val() && $('#payment_terms_filter').val().length > 0) filterCount++;
        if ($('#start_date').val()) filterCount++;
        if ($('#end_date').val()) filterCount++;
        if ($('#min_amount').val()) filterCount++;
        if ($('#max_amount').val()) filterCount++;

        let text = `Export dalam format ${format.toUpperCase()} dengan ${selectedColumns} dari ${totalColumns} kolom`;
        if (filterCount > 0) {
            text += ` dan ${filterCount} filter aktif`;
        }
        text += '.';

        $('#preview-text').text(text);
    }

    // Form submission
    $('#exportForm').on('submit', function(e) {
        e.preventDefault();

        // Validate at least one column is selected
        if ($('.column-checkbox:checked').length === 0) {
            Swal.fire({
                title: 'Pilih Kolom',
                text: 'Pilih minimal satu kolom untuk export.',
                icon: 'warning',
                customClass: {
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false
            });
            return;
        }

        // Show loading state
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.html('<i class="icon-base ti tabler-loader-2 me-2"></i>Exporting...').prop('disabled', true);

        // Gather form data
        const formData = {
            export_format: $('input[name="export_format"]:checked').val(),
            start_date: $('#start_date').val(),
            end_date: $('#end_date').val(),
            supplier_filter: $('#supplier_filter').val(),
            status_filter: $('#status_filter').val(),
            payment_terms_filter: $('#payment_terms_filter').val(),
            min_amount: $('#min_amount').val(),
            max_amount: $('#max_amount').val(),
            columns: $('.column-checkbox:checked').map(function() {
                return $(this).val();
            }).get()
        };

        // Submit export request
        $.ajax({
            url: '{{ route("main_transaction.purchase_invoice.process_export") }}',
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            xhrFields: {
                responseType: 'blob' // Important for file download
            },
            success: function(response, status, xhr) {
                // Get filename from header or use default
                const disposition = xhr.getResponseHeader('Content-Disposition');
                let filename = 'purchase_invoices_export.' + formData.export_format;
                
                if (disposition && disposition.indexOf('attachment') !== -1) {
                    const matches = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/.exec(disposition);
                    if (matches != null && matches[1]) {
                        filename = matches[1].replace(/['"]/g, '');
                    }
                }

                // Create download link
                const url = window.URL.createObjectURL(new Blob([response]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', filename);
                document.body.appendChild(link);
                link.click();

                // Cleanup
                window.URL.revokeObjectURL(url);
                document.body.removeChild(link);

                // Show success message
                Swal.fire({
                    title: 'Export Berhasil!',
                    text: 'File export telah berhasil diunduh.',
                    icon: 'success',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                });

                // Reset button
                submitBtn.html(originalText).prop('disabled', false);
            },
            error: function(xhr, status, error) {
                let errorMessage = 'Terjadi kesalahan saat melakukan export.';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                Swal.fire({
                    title: 'Export Gagal!',
                    text: errorMessage,
                    icon: 'error',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                });

                // Reset button
                submitBtn.html(originalText).prop('disabled', false);
            }
        });
    });

    // Initialize preview text
    updatePreviewText();
});
</script>
@endsection