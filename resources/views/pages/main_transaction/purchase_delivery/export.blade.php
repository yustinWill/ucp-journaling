@extends('layouts.default')
@section('page_title')
    Export Purchase Delivery
@stop
@section('content')
    <div class="card mb-6">
        <h5 class="card-header">Export Purchase Delivery</h5>
        <form class="card-body" id="exportForm" autocomplete="off">
            <!-- Instructions Section -->
            <div class="row gy-3 mt-0">
                <div class="col-md-12">
                    <div class="alert alert-primary alert-dismissible d-flex" role="alert">
                        <span class="alert-icon rounded">
                            <i class="icon-base ti tabler-bell icon-md w-px-30"></i>
                        </span>
                        <div class="d-flex flex-column ps-1">
                            <h5 class="alert-heading mb-2"><b>Export Data Purchase Delivery!</b></h5>
                            <p class="mb-0">
                                Fitur ini memungkinkan Anda untuk mengekspor data purchase delivery dalam berbagai format.
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
                        <option value="PENDING">PENDING - Menunggu</option>
                        <option value="IN_TRANSIT">IN_TRANSIT - Dalam Perjalanan</option>
                        <option value="DELIVERED">DELIVERED - Terkirim</option>
                        <option value="PARTIAL_DELIVERED">PARTIAL_DELIVERED - Sebagian Terkirim</option>
                        <option value="CANCELLED">CANCELLED - Dibatalkan</option>
                    </select>
                </div>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Gudang</label>
                    <select class="global-select2 form-select" id="warehouse_filter" data-allow-clear="true" multiple>
                        <option value="">Semua Gudang</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse['value'] }}">{{ $warehouse['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Range Items</label>
                    <div class="row g-2">
                        <div class="col">
                            <input type="number" class="form-control" id="min_items" placeholder="Minimal" min="0">
                        </div>
                        <div class="col-auto d-flex align-items-center">
                            <span>-</span>
                        </div>
                        <div class="col">
                            <input type="number" class="form-control" id="max_items" placeholder="Maksimal" min="0">
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
                                <label class="form-check-label" for="col-reference-no">No. Delivery</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" id="col-date" value="date" checked>
                                <label class="form-check-label" for="col-date">Tanggal</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" id="col-supplier" value="supplier" checked>
                                <label class="form-check-label" for="col-supplier">Supplier</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" id="col-po-reference" value="po_reference" checked>
                                <label class="form-check-label" for="col-po-reference">PO Reference</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" id="col-status" value="status" checked>
                                <label class="form-check-label" for="col-status">Status</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" id="col-delivery-date" value="delivery_date">
                                <label class="form-check-label" for="col-delivery-date">Tanggal Pengiriman</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" id="col-carrier" value="carrier">
                                <label class="form-check-label" for="col-carrier">Carrier</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" id="col-tracking-number" value="tracking_number">
                                <label class="form-check-label" for="col-tracking-number">Tracking Number</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" id="col-total-items" value="total_items" checked>
                                <label class="form-check-label" for="col-total-items">Total Items</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" id="col-delivered-items" value="delivered_items" checked>
                                <label class="form-check-label" for="col-delivered-items">Items Diterima</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" id="col-warehouse" value="warehouse">
                                <label class="form-check-label" for="col-warehouse">Gudang</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" id="col-received-by" value="received_by">
                                <label class="form-check-label" for="col-received-by">Diterima Oleh</label>
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
                        <span id="preview-text">Siap untuk export data purchase delivery dengan kriteria yang dipilih.</span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-end gap-3 mt-6">
                <a href="{{ route('main_transaction.purchase_delivery.index') }}" class="btn btn-outline-secondary waves-effect">
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
    $('#supplier_filter, #status_filter, #warehouse_filter, #start_date, #end_date, #min_items, #max_items').on('change', function() {
        updatePreviewText();
    });

    function updatePreviewText() {
        const format = $('input[name="export_format"]:checked').val();
        const selectedColumns = $('.column-checkbox:checked').length;
        const totalColumns = $('.column-checkbox').length;
        
        let filterCount = 0;
        if ($('#supplier_filter').val() && $('#supplier_filter').val().length > 0) filterCount++;
        if ($('#status_filter').val() && $('#status_filter').val().length > 0) filterCount++;
        if ($('#warehouse_filter').val() && $('#warehouse_filter').val().length > 0) filterCount++;
        if ($('#start_date').val()) filterCount++;
        if ($('#end_date').val()) filterCount++;
        if ($('#min_items').val()) filterCount++;
        if ($('#max_items').val()) filterCount++;

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
            warehouse_filter: $('#warehouse_filter').val(),
            min_items: $('#min_items').val(),
            max_items: $('#max_items').val(),
            columns: $('.column-checkbox:checked').map(function() {
                return $(this).val();
            }).get()
        };

        // Submit export request
        $.ajax({
            url: '{{ route("main_transaction.purchase_delivery.process_export") }}',
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
                let filename = 'purchase_deliveries_export.' + formData.export_format;
                
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