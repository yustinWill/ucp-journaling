@extends('layouts.default')
@section('page_title')
    Export Stock Adjustment
@stop
@section('content')
    <div class="card mb-6">
        <h5 class="card-header">Export Stock Adjustment</h5>
        <form class="card-body" id="exportForm" autocomplete="off">
            <!-- Export Configuration Section -->
            <div class="section-header mt-12">
                <h6>Konfigurasi Export</h6>
                <hr class="mx-n6">
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Format Export <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="export-format" data-allow-clear="false">
                        <option value="">Pilih Format</option>
                        <option value="excel">Excel (.xlsx)</option>
                        <option value="csv">CSV (.csv)</option>
                        <option value="pdf">PDF (.pdf)</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama File</label>
                    <input type="text" class="form-control" id="filename" placeholder="stock_adjustment_export" maxlength="100" />
                    <small class="form-text text-muted">Kosongkan untuk menggunakan nama default dengan timestamp</small>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="section-header mt-12">
                <h6>Filter Data</h6>
                <hr class="mx-n6">
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" class="form-control global-single-date-picker" id="date-from" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Akhir</label>
                    <input type="date" class="form-control global-single-date-picker" id="date-to" />
                </div>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Filter Tipe Adjustment</label>
                    <select class="global-select2 form-select" id="adjustment-type-filter" data-allow-clear="false">
                        <option value="">Semua Tipe</option>
                        @foreach($adjustmentTypes as $type)
                            <option value="{{ $type['value'] }}">{{ $type['label'] }}</option>
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
            </div>

            <!-- Column Selection Section -->
            <div class="section-header mt-12">
                <h6>Pilih Kolom</h6>
                <hr class="mx-n6">
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Kolom yang akan diekspor</h6>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-md btn-success waves-effect waves-light" id="select-all-columns-btn">
                                <i class="icon-base ti tabler-check-box me-2"></i>
                                Pilih Semua
                            </button>
                            <button type="button" class="btn btn-md btn-warning waves-effect waves-light" id="deselect-all-columns-btn">
                                <i class="icon-base ti tabler-square me-2"></i>
                                Batal Pilih Semua
                            </button>
                        </div>
                    </div>
                    
                    <div class="mx-n5">
                        <table class="table table-bordered" id="columns-table">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="select-all-columns">
                                        </div>
                                    </th>
                                    <th style="width: 25%;">Nama Kolom</th>
                                    <th style="width: 30%;">Nama Export</th>
                                    <th style="width: 40%;">Deskripsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($exportColumns as $column)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input column-checkbox" type="checkbox" 
                                                   value="{{ $column['field'] }}" 
                                                   data-label="{{ $column['label'] }}" 
                                                   data-export-name="{{ $column['export_name'] }}" checked>
                                        </div>
                                    </td>
                                    <td><strong>{{ $column['label'] }}</strong></td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm export-name-input" 
                                               value="{{ $column['export_name'] }}" 
                                               data-field="{{ $column['field'] }}" maxlength="100">
                                    </td>
                                    <td class="text-muted">
                                        @switch($column['field'])
                                            @case('adjustment_number')
                                                Nomor unik untuk setiap stock adjustment
                                                @break
                                            @case('adjustment_date')
                                                Tanggal dilakukannya stock adjustment
                                                @break
                                            @case('adjustment_type')
                                                Jenis stock adjustment (Manual, Stock Opname, dll)
                                                @break
                                            @case('warehouse_name')
                                                Nama gudang tempat dilakukan adjustment
                                                @break
                                            @case('reference_number')
                                                Nomor referensi dokumen pendukung
                                                @break
                                            @case('total_items')
                                                Jumlah item produk yang disesuaikan
                                                @break
                                            @case('total_adjustment_qty')
                                                Total quantity adjustment (positif/negatif)
                                                @break
                                            @case('total_adjustment_value')
                                                Total nilai adjustment dalam Rupiah
                                                @break
                                            @case('status')
                                                Status approval dan penerapan adjustment
                                                @break
                                            @case('approved_by')
                                                Nama user yang menyetujui adjustment
                                                @break
                                            @case('approved_date')
                                                Tanggal persetujuan adjustment
                                                @break
                                            @case('description')
                                                Deskripsi dan alasan stock adjustment
                                                @break
                                            @default
                                                -
                                        @endswitch
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Preview Section -->
            <div class="section-header mt-12">
                <h6>Preview Export</h6>
                <hr class="mx-n6">
            </div>
            
            <div class="row g-3 mb-6">
                <div class="col-md-4">
                    <div class="summary-card success">
                        <h6>Kolom Terpilih</h6>
                        <h4 id="selected-columns-count">{{ count($exportColumns) }}</h4>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="summary-card" id="estimated-records-card">
                        <h6>Estimasi Record</h6>
                        <h4 id="estimated-records">~245</h4>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="summary-card" id="file-size-card">
                        <h6>Estimasi Ukuran File</h6>
                        <h4 id="estimated-file-size">~50 KB</h4>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 text-end">
                <button type="button" class="btn btn-label-danger me-3" id="cancel-btn">Batalkan</button>
                <button type="button" class="btn btn-primary me-3" id="preview-btn">
                    <i class="icon-base ti tabler-eye me-2"></i>
                    Preview Data
                </button>
                <button type="button" class="btn btn-success" id="export-btn">
                    <i class="icon-base ti tabler-download me-2"></i>
                    Export Data
                </button>
            </div>
        </form>
    </div>

    <!-- Preview Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewModalLabel">Preview Data Export</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="preview-content">
                        <!-- Preview content will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-success" id="confirm-export-btn">
                        <i class="icon-base ti tabler-download me-2"></i>
                        Lanjutkan Export
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js_script')
<script>
$(function() {
    // Update selected columns count
    function updateSelectedColumnsCount() {
        const selectedCount = $('.column-checkbox:checked').length;
        $('#selected-columns-count').text(selectedCount);
        
        // Update estimates based on selected columns and filters
        updateEstimates();
    }

    // Update estimates
    function updateEstimates() {
        const selectedColumns = $('.column-checkbox:checked').length;
        const hasDateFilter = $('#date-from').val() || $('#date-to').val();
        const hasTypeFilter = $('#adjustment-type-filter').val();
        const hasStatusFilter = $('#status-filter').val();
        const hasWarehouseFilter = $('#warehouse-filter').val();
        
        // Estimate records based on filters
        let estimatedRecords = 245; // Base count
        if (hasDateFilter) estimatedRecords = Math.floor(estimatedRecords * 0.6);
        if (hasTypeFilter) estimatedRecords = Math.floor(estimatedRecords * 0.4);
        if (hasStatusFilter) estimatedRecords = Math.floor(estimatedRecords * 0.5);
        if (hasWarehouseFilter) estimatedRecords = Math.floor(estimatedRecords * 0.3);
        
        $('#estimated-records').text('~' + estimatedRecords.toLocaleString('id-ID'));
        
        // Estimate file size (rough calculation)
        const avgRowSize = selectedColumns * 15; // bytes per column
        const totalSize = estimatedRecords * avgRowSize;
        let sizeText = '';
        if (totalSize < 1024) {
            sizeText = '~' + totalSize + ' B';
        } else if (totalSize < 1024 * 1024) {
            sizeText = '~' + Math.round(totalSize / 1024) + ' KB';
        } else {
            sizeText = '~' + Math.round(totalSize / (1024 * 1024)) + ' MB';
        }
        $('#estimated-file-size').text(sizeText);
    }

    // Checkbox functionality
    $('#select-all-columns').on('change', function() {
        $('.column-checkbox').prop('checked', $(this).prop('checked'));
        updateSelectedColumnsCount();
    });
    
    $(document).on('change', '.column-checkbox', function() {
        const totalCheckboxes = $('.column-checkbox').length;
        const checkedCheckboxes = $('.column-checkbox:checked').length;
        $('#select-all-columns').prop('checked', checkedCheckboxes === totalCheckboxes && totalCheckboxes > 0);
        updateSelectedColumnsCount();
    });

    // Select/Deselect all buttons
    $('#select-all-columns-btn').on('click', function() {
        $('.column-checkbox').prop('checked', true);
        $('#select-all-columns').prop('checked', true);
        updateSelectedColumnsCount();
    });

    $('#deselect-all-columns-btn').on('click', function() {
        $('.column-checkbox').prop('checked', false);
        $('#select-all-columns').prop('checked', false);
        updateSelectedColumnsCount();
    });

    // Filter change handlers
    $('#date-from, #date-to, #adjustment-type-filter, #status-filter, #warehouse-filter').on('change', function() {
        updateEstimates();
    });

    // Export name input change handler
    $(document).on('input', '.export-name-input', function() {
        const field = $(this).data('field');
        const newName = $(this).val();
        const checkbox = $(`.column-checkbox[value="${field}"]`);
        checkbox.attr('data-export-name', newName);
    });

    // Form validation
    function validateForm() {
        let isValid = true;
        
        // Check format selection
        if (!$('#export-format').val()) {
            $('#export-format').addClass('is-invalid');
            isValid = false;
        } else {
            $('#export-format').removeClass('is-invalid');
        }
        
        // Check if at least one column is selected
        if ($('.column-checkbox:checked').length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Pilih Kolom',
                text: 'Pilih minimal satu kolom untuk diekspor.',
                customClass: {
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false
            });
            isValid = false;
        }
        
        return isValid;
    }

    // Collect form data
    function collectFormData() {
        const selectedColumns = [];
        $('.column-checkbox:checked').each(function() {
            selectedColumns.push({
                field: $(this).val(),
                label: $(this).data('label'),
                export_name: $(this).data('export-name')
            });
        });
        
        const filters = {};
        if ($('#date-from').val()) filters.date_from = $('#date-from').val();
        if ($('#date-to').val()) filters.date_to = $('#date-to').val();
        if ($('#adjustment-type-filter').val()) filters.adjustment_type = $('#adjustment-type-filter').val();
        if ($('#status-filter').val()) filters.status = $('#status-filter').val();
        if ($('#warehouse-filter').val()) filters.warehouse = $('#warehouse-filter').val();
        
        return {
            format: $('#export-format').val(),
            filename: $('#filename').val() || null,
            columns: selectedColumns,
            filters: filters
        };
    }

    // Preview button
    $('#preview-btn').on('click', function() {
        if (!validateForm()) {
            return;
        }
        
        const formData = collectFormData();
        
        // Mock preview data
        const previewData = [
            {
                adjustment_number: 'SA-2025-001',
                adjustment_date: '2025-01-15',
                adjustment_type: 'Hasil Stock Opname',
                warehouse_name: 'Gudang Utama Pusat',
                reference_number: 'SO-2025-001',
                total_items: 3,
                total_adjustment_qty: 2,
                total_adjustment_value: 66000,
                status: 'Diterapkan',
                approved_by: 'Yustinus Komunal',
                approved_date: '2025-01-15',
                description: 'Penyesuaian stok dari hasil stock opname'
            },
            {
                adjustment_number: 'SA-2025-002',
                adjustment_date: '2025-01-16',
                adjustment_type: 'Penyesuaian Manual',
                warehouse_name: 'Gudang Produksi',
                reference_number: null,
                total_items: 1,
                total_adjustment_qty: -50,
                total_adjustment_value: -825000,
                status: 'Menunggu Persetujuan',
                approved_by: null,
                approved_date: null,
                description: 'Koreksi stok botol 330ml'
            }
        ];
        
        // Generate preview table
        let previewHtml = `
            <div class="alert alert-info">
                <i class="icon-base ti tabler-info-circle me-2"></i>
                <strong>Preview</strong> - Menampilkan contoh 2 baris pertama dengan kolom yang dipilih
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
        `;
        
        formData.columns.forEach(function(column) {
            previewHtml += `<th>${column.export_name}</th>`;
        });
        previewHtml += `</tr></thead><tbody>`;
        
        previewData.forEach(function(row) {
            previewHtml += '<tr>';
            formData.columns.forEach(function(column) {
                const value = row[column.field] || '-';
                previewHtml += `<td>${value}</td>`;
            });
            previewHtml += '</tr>';
        });
        
        previewHtml += `</tbody></table></div>`;
        
        $('#preview-content').html(previewHtml);
        $('#previewModal').modal('show');
    });

    // Export button
    $('#export-btn, #confirm-export-btn').on('click', function() {
        if (!validateForm()) {
            return;
        }
        
        const formData = collectFormData();
        
        // Show loading
        Swal.fire({
            title: 'Memproses Export...',
            text: 'Mohon tunggu, sistem sedang memproses data export Anda.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Simulate export process
        setTimeout(() => {
            Swal.close();
            $('#previewModal').modal('hide');
            
            // In real implementation, submit to controller
            // For now, show success message
            Swal.fire({
                icon: 'success',
                title: 'Export Berhasil!',
                text: `File ${formData.format.toUpperCase()} berhasil diunduh.`,
                customClass: {
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false
            });
            
            // In real implementation, trigger download
            // window.open('{{ route("main_transaction.stock_adjustment.processExport") }}', '_blank');
        }, 2000);
    });

    // Cancel button
    $('#cancel-btn').on('click', function() {
        window.location.href = "{{ route('main_transaction.stock_adjustment.index') }}";
    });

    // Initialize
    updateSelectedColumnsCount();
});
</script>

<style>
.summary-card {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 1rem;
    text-align: center;
}

.summary-card.success {
    background-color: #28a745;
    color: white;
}
</style>
@endsection