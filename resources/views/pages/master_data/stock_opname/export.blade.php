@extends('layouts.default')
@section('page_title')
    Ekspor Stock Opname
@stop
@section('content')
    <div class="card mb-6">
        <h5 class="card-header">Ekspor Stock Opname</h5>
        <form class="card-body form-repeater">
            <!-- Format Selection Section -->
            <h6>1. Pilih Jenis Format</h6>
            <div class="row gy-3 mt-0">
                <div class="col-xl-4 col-md-5 col-sm-6 col-12 form-control-validation fv-plugins-icon-container fv-plugins-bootstrap5-row-valid">
                    <div class="form-check custom-option custom-option-icon checked">
                        <label class="form-check-label custom-option-content" for="format-excel">
                            <span class="custom-option-body">
                                <i class="icon-base ti tabler-file-type-xls"></i>
                                <span class="custom-option-title">Excel</span>
                                <small>File ini berisi data stock opname yang dapat langsung dibuka dan diedit menggunakan MS Excel atau aplikasi spreadsheet lainnya.</small>
                            </span>
                            <input name="export_format" class="form-check-input" type="radio" value="excel" id="format-excel" checked>
                        </label>
                    </div>
                </div>
                <div class="col-xl-4 col-md-5 col-sm-6 col-12 form-control-validation">
                    <div class="form-check custom-option custom-option-icon">
                        <label class="form-check-label custom-option-content" for="format-csv">
                            <span class="custom-option-body">
                                <i class="icon-base ti tabler-file-type-csv"></i>
                                <span class="custom-option-title">CSV</span>
                                <small>Format teks sederhana yang cocok digunakan untuk pemrosesan data stock opname di berbagai sistem dan aplikasi.</small>
                            </span>
                            <input name="export_format" class="form-check-input" type="radio" value="csv" id="format-csv">
                        </label>
                    </div>
                </div>
                <div class="col-xl-4 col-md-5 col-sm-6 col-12 form-control-validation">
                    <div class="form-check custom-option custom-option-icon">
                        <label class="form-check-label custom-option-content" for="format-pdf">
                            <span class="custom-option-body">
                                <i class="icon-base ti tabler-file-type-pdf"></i>
                                <span class="custom-option-title">PDF</span>
                                <small>Dokumen versi tetap yang mempertahankan format dan tampilan data stock opname, ideal untuk dibaca atau dicetak.</small>
                            </span>
                            <input name="export_format" class="form-check-input" type="radio" value="pdf" id="format-pdf">
                        </label>
                    </div>
                </div>
            </div>
            <hr class="my-6 mx-n6" />

            <!-- Filter Section -->
            <h6>2. Filter Data</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Rentang Tanggal Dari</label>
                    <input type="date" class="form-control global-single-date-picker" id="date-from">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Rentang Tanggal Sampai</label>
                    <input type="date" class="form-control global-single-date-picker" id="date-to">
                </div>
            </div>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Filter Gudang</label>
                    <select class="global-select2 form-select" id="warehouse-filter" data-allow-clear="false">
                        <option value="">Semua Gudang</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse['value'] }}">{{ $warehouse['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Filter Status</label>
                    <select class="global-select2 form-select" id="status-filter" data-allow-clear="false">
                        <option value="">Semua Status</option>
                        @foreach($statusOptions as $status)
                            <option value="{{ $status['value'] }}">{{ $status['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <hr class="my-6 mx-n6" />

            <!-- Column Configuration Section -->
            <h6>3. Tentukan Urutan Kolom Yang Ingin Di Ekspor</h6>
            <div class="text-nowrap">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 15%;">Urutan Kolom</th>
                            <th style="width: 25%;">Nama Kolom</th>
                            <th style="width: 15%;">Sembunyikan Kolom?</th>
                            <th style="width: 25%;">Nama Field</th>
                        </tr>
                    </thead>
                    <tbody id="column-config-tbody">
                        @foreach ($exportColumns as $index => $column)
                            <tr data-column="{{ $column['field'] }}">
                                <td>
                                    <input type="number" class="form-control column-order" 
                                           value="{{ $index + 1 }}" min="1" max="{{ count($exportColumns) }}">
                                </td>
                                <td>{{ $column['label'] }}</td>
                                <td>
                                    <div class="form-check d-flex mb-0">
                                        <input class="form-check-input column-hidden" type="checkbox" 
                                               id="hide-{{ $column['field'] }}" value="{{ $column['field'] }}">
                                    </div>
                                </td>
                                <td>
                                    <input type="text" class="form-control column-name" 
                                           value="{{ $column['export_name'] ?? $column['label'] }}">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 text-end">
                <button type="button" class="btn btn-label-danger me-3" id="cancel-export-btn">Batalkan</button>
                <button type="button" class="btn btn-success" id="export-btn">Ekspor Data</button>
            </div>
        </form>
    </div>
@endsection

@section('js_script')
<script>
$(function() {
    // Export data
    $('#export-btn').on('click', function() {
        const format = $('input[name="export_format"]:checked').val();
        const columns = getColumnConfiguration();
        const filters = {
            date_from: $('#date-from').val(),
            date_to: $('#date-to').val(),
            warehouse: $('#warehouse-filter').val(),
            status: $('#status-filter').val()
        };
        
        if (columns.length === 0) {
            Swal.fire({
                title: 'Error!',
                text: 'Pilih minimal satu kolom untuk diekspor',
                icon: 'error',
                customClass: { confirmButton: 'btn btn-primary' },
                buttonsStyling: false
            });
            return;
        }
        
        let filterText = '';
        if (filters.date_from && filters.date_to) {
            filterText += `Tanggal: ${filters.date_from} s/d ${filters.date_to}. `;
        }
        if (filters.warehouse) {
            filterText += `Gudang: ${$('#warehouse-filter option:selected').text()}. `;
        }
        if (filters.status) {
            filterText += `Status: ${$('#status-filter option:selected').text()}. `;
        }
        
        Swal.fire({
            title: 'Konfirmasi Ekspor',
            html: `Apakah Anda yakin ingin mengekspor data stock opname dalam format <strong>${format.toUpperCase()}</strong>?<br><br><small>${filterText || 'Semua data akan diekspor.'}</small>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Ekspor',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                processExport(format, columns, filters);
            }
        });
    });
    
    function getColumnConfiguration() {
        const columns = [];
        const hiddenColumns = [];
        
        // Get hidden columns
        $('.column-hidden:checked').each(function() {
            hiddenColumns.push($(this).val());
        });
        
        // Get visible columns with their configuration
        $('#column-config-tbody tr').each(function() {
            const columnField = $(this).data('column');
            
            if (!hiddenColumns.includes(columnField)) {
                columns.push({
                    field: columnField,
                    label: $(this).find('.column-name').val(),
                    order: parseInt($(this).find('.column-order').val())
                });
            }
        });
        
        // Sort by order
        columns.sort((a, b) => a.order - b.order);
        
        return columns;
    }
    
    function processExport(format, columns, filters) {
        // Show loading
        Swal.fire({
            title: 'Exporting...',
            text: 'Sedang memproses ekspor data stock opname',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Create form for download
        const form = $('<form>', {
            method: 'POST',
            action: "{{ route('master_data.stock_opname.process_export') }}"
        });
        
        form.append($('<input>', {
            type: 'hidden',
            name: '_token',
            value: '{{ csrf_token() }}'
        }));
        
        form.append($('<input>', {
            type: 'hidden',
            name: 'format',
            value: format
        }));
        
        form.append($('<input>', {
            type: 'hidden',
            name: 'columns',
            value: JSON.stringify(columns)
        }));
        
        form.append($('<input>', {
            type: 'hidden',
            name: 'filters',
            value: JSON.stringify(filters)
        }));
        
        // Submit form for download
        $('body').append(form);
        form.submit();
        form.remove();
        
        // Close loading after a delay (file download doesn't trigger success/error)
        setTimeout(() => {
            Swal.close();
            Swal.fire({
                title: 'Export Started!',
                text: 'File download stock opname akan dimulai secara otomatis',
                icon: 'success',
                timer: 3000,
                showConfirmButton: false
            });
        }, 1000);
    }
    
    // Cancel button
    $('#cancel-export-btn').on('click', function() {
        window.location.href = "{{ route('master_data.stock_opname.index') }}";
    });
    
    // Auto-update order when changed
    $(document).on('change', '.column-order', function() {
        updateColumnOrder();
    });
    
    function updateColumnOrder() {
        const rows = $('#column-config-tbody tr').get();
        
        rows.sort(function(a, b) {
            const orderA = parseInt($(a).find('.column-order').val());
            const orderB = parseInt($(b).find('.column-order').val());
            return orderA - orderB;
        });
        
        $('#column-config-tbody').empty().append(rows);
    }
    
    // Set default date range (current month)
    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
    
    $('#date-from').val(firstDay.toISOString().split('T')[0]);
    $('#date-to').val(lastDay.toISOString().split('T')[0]);
});
</script>
@endsection