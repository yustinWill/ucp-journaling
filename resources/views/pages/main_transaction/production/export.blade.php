@extends('layouts.default')
@section('page_title')
    Export Production Data
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Export Production Data</h4>
        <form class="card-body" id="export-form" autocomplete="off">
            @csrf
            
            <!-- Export Filters Section -->
            <div class="section-header mt-12">
                <h6>Filter Export Data</h6>
                <hr class="mx-n6">
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tanggal Dari</label>
                    <input type="date" class="form-control" id="date-from" name="date_from" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Sampai</label>
                    <input type="date" class="form-control" id="date-to" name="date_to" />
                </div>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select class="global-select2 form-select" id="status-filter" name="status" data-allow-clear="false">
                        <option value="">Semua Status</option>
                        <option value="DRAFT">DRAFT - Draft</option>
                        <option value="PLANNED">PLANNED - Direncanakan</option>
                        <option value="IN_PROGRESS">IN_PROGRESS - Sedang Produksi</option>
                        <option value="COMPLETED">COMPLETED - Selesai</option>
                        <option value="CANCELLED">CANCELLED - Dibatalkan</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Gudang</label>
                    <select class="global-select2 form-select" id="warehouse-filter" name="warehouse_id" data-allow-clear="false">
                        <option value="">Semua Gudang</option>
                        <option value="1">GDG-001 - Gudang Utama Pusat</option>
                        <option value="2">GDG-002 - Gudang Produksi</option>
                        <option value="3">GDG-003 - Gudang Cabang Jakarta</option>
                    </select>
                </div>
            </div>

            <!-- Export Format Section -->
            <div class="section-header mt-12">
                <h6>Format Export</h6>
                <hr class="mx-n6">
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Format File <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="export-format" name="format" data-allow-clear="false" required>
                        <option value="">Pilih Format</option>
                        <option value="excel">Excel (.xlsx)</option>
                        <option value="csv">CSV (.csv)</option>
                        <option value="pdf">PDF (.pdf)</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Include Components</label>
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" id="include-components" name="include_components" value="1">
                        <label class="form-check-label" for="include-components">
                            Include bundle component details
                        </label>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 text-end">
                <a href="{{ route('main_transaction.production.index') }}" class="btn btn-label-danger me-3">Kembali</a>
                <button type="submit" class="btn btn-success" id="export-btn">
                    <i class="icon-base ti tabler-file-download me-2"></i>
                    Export Data
                </button>
            </div>
        </form>
    </div>
@stop

@section('js_script')
<script>
$(function() {
    // Set default date range to current month
    const now = new Date();
    const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
    const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);
    
    $('#date-from').val(firstDay.toISOString().substr(0, 10));
    $('#date-to').val(lastDay.toISOString().substr(0, 10));
    
    // Form submission
    $('#export-form').on('submit', function(e) {
        e.preventDefault();
        
        // Validate required fields
        if (!$('#export-format').val()) {
            Swal.fire({
                icon: 'error',
                title: 'Format Belum Dipilih',
                text: 'Silakan pilih format file export.',
                customClass: { confirmButton: 'btn btn-primary' },
                buttonsStyling: false
            });
            return;
        }
        
        $('#export-btn').prop('disabled', true).html(
            '<span class="spinner-border spinner-border-sm me-2"></span>Processing...'
        );
        
        const formData = {
            _token: '{{ csrf_token() }}',
            date_from: $('#date-from').val(),
            date_to: $('#date-to').val(),
            status: $('#status-filter').val(),
            warehouse_id: $('#warehouse-filter').val(),
            format: $('#export-format').val(),
            include_components: $('#include-components').is(':checked') ? 1 : 0
        };
        
        $.ajax({
            url: "{{ route('main_transaction.production.process_export') }}",
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Export Berhasil!',
                        text: response.message || 'Data berhasil diexport.',
                        customClass: { confirmButton: 'btn btn-primary' },
                        buttonsStyling: false
                    }).then(() => {
                        // TODO: Download file or redirect to download link
                        if (response.download_url) {
                            window.open(response.download_url, '_blank');
                        }
                    });
                }
            },
            error: function(xhr) {
                let message = 'Terjadi kesalahan saat export data.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Export Gagal!',
                    text: message,
                    customClass: { confirmButton: 'btn btn-primary' },
                    buttonsStyling: false
                });
            },
            complete: function() {
                $('#export-btn').prop('disabled', false).html(
                    '<i class="icon-base ti tabler-file-download me-2"></i>Export Data'
                );
            }
        });
    });
});
</script>
@endsection