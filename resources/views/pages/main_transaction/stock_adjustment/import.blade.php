@extends('layouts.default')
@section('page_title')
    Import Stock Adjustment
@stop
@section('content')
    <div class="card mb-6">
        <h5 class="card-header">Import Stock Adjustment</h5>
        <form class="card-body" id="importForm">
            <!-- Instructions Section -->
            <div class="section-header mt-12">
                <h6>Petunjuk Import</h6>
                <hr class="mx-n6">
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="icon-base ti tabler-info-circle me-2"></i>
                            Panduan Import Stock Adjustment
                        </h6>
                        <ul class="mb-0">
                            <li>Download template Excel terlebih dahulu untuk memastikan format yang benar</li>
                            <li>File yang didukung: Excel (.xlsx, .xls) dan CSV (.csv)</li>
                            <li>Maksimal ukuran file: 10MB</li>
                            <li>Pastikan semua field wajib telah diisi dengan benar</li>
                            <li>Nomor adjustment akan di-generate otomatis jika kosong</li>
                            <li>Status default akan diset ke DRAFT jika tidak diisi</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Template Download Section -->
            <div class="section-header mt-12">
                <h6>Download Template</h6>
                <hr class="mx-n6">
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <div class="card border-primary">
                        <div class="card-body text-center">
                            <i class="icon-base ti tabler-file-download icon-48px text-primary mb-3"></i>
                            <h6>Template Import Stock Adjustment</h6>
                            <p class="text-muted mb-3">Download template Excel dengan format dan contoh data yang benar</p>
                            <button type="button" class="btn btn-md btn-primary waves-effect waves-light" id="download-template-btn">
                                <i class="icon-base ti tabler-download me-2"></i>
                                Download Template Excel
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- File Upload Section -->
            <div class="section-header mt-12">
                <h6>Upload File</h6>
                <hr class="mx-n6">
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <label class="form-label">Pilih File Import <span style="color: red;">*</span></label>
                    <input type="file" class="form-control" id="import-file" accept=".xlsx,.xls,.csv" />
                    <small class="form-text text-muted">Format yang didukung: Excel (.xlsx, .xls) dan CSV (.csv). Maksimal 10MB.</small>
                </div>
            </div>

            <!-- Validation Options -->
            <div class="section-header mt-12">
                <h6>Opsi Validasi</h6>
                <hr class="mx-n6">
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="skip-duplicate" checked>
                        <label class="form-check-label" for="skip-duplicate">
                            Skip data duplikat
                        </label>
                    </div>
                    <small class="form-text text-muted">Lewati baris yang memiliki nomor adjustment yang sudah ada</small>
                </div>
                <div class="col-md-6">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="validate-warehouse">
                        <label class="form-check-label" for="validate-warehouse">
                            Validasi gudang
                        </label>
                    </div>
                    <small class="form-text text-muted">Pastikan gudang yang dimasukkan benar-benar exist</small>
                </div>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="validate-pic" checked>
                        <label class="form-check-label" for="validate-pic">
                            Validasi PIC
                        </label>
                    </div>
                    <small class="form-text text-muted">Pastikan penanggung jawab yang dimasukkan valid</small>
                </div>
                <div class="col-md-6">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="auto-generate-number" checked>
                        <label class="form-check-label" for="auto-generate-number">
                            Auto-generate nomor adjustment
                        </label>
                    </div>
                    <small class="form-text text-muted">Generate otomatis nomor jika kolom nomor kosong</small>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 text-end">
                <button type="button" class="btn btn-label-danger me-3" id="cancel-btn">Batalkan</button>
                <button type="button" class="btn btn-warning me-3" id="validate-btn" style="display: none;">
                    <i class="icon-base ti tabler-search me-2"></i>
                    Validasi Data
                </button>
                <button type="button" class="btn btn-success" id="import-btn" style="display: none;">
                    <i class="icon-base ti tabler-upload me-2"></i>
                    Import Data
                </button>
            </div>
        </form>
    </div>

    <!-- Validation Results Modal -->
    <div class="modal fade" id="validationModal" tabindex="-1" aria-labelledby="validationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="validationModalLabel">Hasil Validasi Import</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="validation-content">
                        <!-- Validation content will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-success" id="proceed-import-btn" style="display: none;">
                        <i class="icon-base ti tabler-upload me-2"></i>
                        Lanjutkan Import
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js_script')
<script>
$(function() {
    let validationData = null;

    // Download template
    $('#download-template-btn').on('click', function() {
        // In real implementation, call the download template route
        window.location.href = "{{ route('main_transaction.stock_adjustment.downloadTemplate') }}";
    });

    // File input change handler
    $('#import-file').on('change', function() {
        const file = this.files[0];
        if (file) {
            // Validate file size
            if (file.size > 10 * 1024 * 1024) { // 10MB
                Swal.fire({
                    icon: 'error',
                    title: 'File Terlalu Besar',
                    text: 'Ukuran file tidak boleh lebih dari 10MB.',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                });
                $(this).val('');
                $('#validate-btn, #import-btn').hide();
                return;
            }

            // Validate file type
            const allowedTypes = ['.xlsx', '.xls', '.csv'];
            const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
            if (!allowedTypes.includes(fileExtension)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Format File Tidak Didukung',
                    text: 'Hanya file Excel (.xlsx, .xls) dan CSV (.csv) yang didukung.',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                });
                $(this).val('');
                $('#validate-btn, #import-btn').hide();
                return;
            }

            // Show validation button
            $('#validate-btn').show();
            $('#import-btn').hide();
        } else {
            $('#validate-btn, #import-btn').hide();
        }
    });

    // Validate button
    $('#validate-btn').on('click', function() {
        const file = $('#import-file')[0].files[0];
        if (!file) {
            Swal.fire({
                icon: 'warning',
                title: 'Pilih File',
                text: 'Silakan pilih file yang akan divalidasi.',
                customClass: {
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false
            });
            return;
        }

        // Show loading
        Swal.fire({
            title: 'Memvalidasi File...',
            text: 'Mohon tunggu, sistem sedang memvalidasi data Anda.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Create FormData for file upload
        const formData = new FormData();
        formData.append('file', file);
        formData.append('skip_duplicate', $('#skip-duplicate').is(':checked'));
        formData.append('validate_warehouse', $('#validate-warehouse').is(':checked'));
        formData.append('validate_pic', $('#validate-pic').is(':checked'));
        formData.append('auto_generate_number', $('#auto-generate-number').is(':checked'));

        // Simulate API call for validation
        setTimeout(() => {
            Swal.close();
            
            // Mock validation response
            validationData = {
                total_count: 3,
                success_count: 2,
                error_count: 1,
                success_data: [
                    {
                        adjustment_number: 'SA-2025-007',
                        adjustment_date: '2025-01-21',
                        adjustment_type: 'MANUAL_ADJUSTMENT',
                        warehouse_name: 'Gudang Utama Pusat',
                        total_items: 1,
                        total_adjustment_qty: 10,
                        status: 'DRAFT',
                        description: 'Import adjustment test 1'
                    },
                    {
                        adjustment_number: 'SA-2025-008',
                        adjustment_date: '2025-01-22',
                        adjustment_type: 'PRODUCT_RETURN',
                        warehouse_name: 'Gudang Finished Goods',
                        total_items: 2,
                        total_adjustment_qty: 15,
                        status: 'DRAFT',
                        description: 'Import adjustment test 2'
                    }
                ],
                error_data: [
                    {
                        adjustment_number: 'SA-2025-001',
                        adjustment_date: '2025-01-23',
                        adjustment_type: 'INVALID_TYPE',
                        warehouse_name: 'Gudang Invalid',
                        total_items: 0,
                        total_adjustment_qty: 0,
                        status: 'DRAFT',
                        description: 'Import adjustment test error',
                        errors: ['Nomor SA sudah ada', 'Tipe adjustment tidak valid', 'Gudang tidak ditemukan']
                    }
                ]
            };
            
            showValidationResults(validationData);
        }, 2000);

        // In real implementation, use AJAX
        /*
        $.ajax({
            url: "{{ route('main_transaction.stock_adjustment.validateImport') }}",
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.close();
                validationData = response;
                showValidationResults(response);
            },
            error: function(xhr) {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error Validasi',
                    text: 'Terjadi kesalahan saat validasi file: ' + xhr.responseJSON.message,
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                });
            }
        });
        */
    });

    // Show validation results
    function showValidationResults(data) {
        let content = `
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="summary-card">
                        <h6>Total Data</h6>
                        <h4>${data.total_count}</h4>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="summary-card success">
                        <h6>Data Valid</h6>
                        <h4>${data.success_count}</h4>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="summary-card danger">
                        <h6>Data Error</h6>
                        <h4>${data.error_count}</h4>
                    </div>
                </div>
            </div>
        `;

        if (data.success_count > 0) {
            content += `
                <h6 class="text-success">
                    <i class="icon-base ti tabler-check me-2"></i>
                    Data Valid (${data.success_count})
                </h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>Nomor Adjustment</th>
                                <th>Tanggal</th>
                                <th>Tipe</th>
                                <th>Gudang</th>
                                <th>Total Item</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            data.success_data.forEach(function(item) {
                content += `
                    <tr>
                        <td>${item.adjustment_number}</td>
                        <td>${item.adjustment_date}</td>
                        <td>${item.adjustment_type}</td>
                        <td>${item.warehouse_name}</td>
                        <td>${item.total_items}</td>
                        <td><span class="badge bg-secondary">${item.status}</span></td>
                    </tr>
                `;
            });

            content += `</tbody></table></div>`;
        }

        if (data.error_count > 0) {
            content += `
                <h6 class="text-danger">
                    <i class="icon-base ti tabler-alert-circle me-2"></i>
                    Data Error (${data.error_count})
                </h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>Nomor Adjustment</th>
                                <th>Tanggal</th>
                                <th>Tipe</th>
                                <th>Gudang</th>
                                <th>Error</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            data.error_data.forEach(function(item) {
                content += `
                    <tr>
                        <td>${item.adjustment_number}</td>
                        <td>${item.adjustment_date}</td>
                        <td>${item.adjustment_type}</td>
                        <td>${item.warehouse_name}</td>
                        <td>
                            <ul class="mb-0 text-danger">
                `;
                
                item.errors.forEach(function(error) {
                    content += `<li>${error}</li>`;
                });
                
                content += `
                            </ul>
                        </td>
                    </tr>
                `;
            });

            content += `</tbody></table></div>`;
        }

        $('#validation-content').html(content);
        
        // Show proceed button if there are valid data
        if (data.success_count > 0) {
            $('#proceed-import-btn').show();
        } else {
            $('#proceed-import-btn').hide();
        }
        
        $('#validationModal').modal('show');
        $('#import-btn').show();
    }

    // Import button (both from main form and modal)
    $('#import-btn, #proceed-import-btn').on('click', function() {
        if (!validationData || validationData.success_count === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Tidak Ada Data Valid',
                text: 'Tidak ada data valid untuk diimport. Silakan validasi file terlebih dahulu.',
                customClass: {
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false
            });
            return;
        }

        // Show confirmation
        Swal.fire({
            title: 'Konfirmasi Import',
            text: `Apakah Anda yakin ingin mengimport ${validationData.success_count} data stock adjustment?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Import',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                processImport();
            }
        });
    });

    // Process import
    function processImport() {
        // Show loading
        Swal.fire({
            title: 'Mengimport Data...',
            text: 'Mohon tunggu, sistem sedang memproses import data Anda.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Simulate import process
        setTimeout(() => {
            Swal.close();
            $('#validationModal').modal('hide');
            
            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Import Berhasil!',
                text: `${validationData.success_count} data stock adjustment berhasil diimport.`,
                customClass: {
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false
            }).then(() => {
                window.location.href = "{{ route('main_transaction.stock_adjustment.index') }}";
            });
        }, 3000);

        // In real implementation, use AJAX
        /*
        $.ajax({
            url: "{{ route('main_transaction.stock_adjustment.processImport') }}",
            method: 'POST',
            data: {
                data: validationData.success_data
            },
            success: function(response) {
                Swal.close();
                $('#validationModal').modal('hide');
                
                Swal.fire({
                    icon: 'success',
                    title: 'Import Berhasil!',
                    text: response.message,
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                }).then(() => {
                    window.location.href = "{{ route('main_transaction.stock_adjustment.index') }}";
                });
            },
            error: function(xhr) {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error Import',
                    text: 'Terjadi kesalahan saat import: ' + xhr.responseJSON.message,
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                });
            }
        });
        */
    }

    // Cancel button
    $('#cancel-btn').on('click', function() {
        window.location.href = "{{ route('main_transaction.stock_adjustment.index') }}";
    });
});
</script>

<style>
.summary-card {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 1rem;
    text-align: center;
    margin-bottom: 1rem;
}

.summary-card.success {
    background-color: #28a745;
    color: white;
}

.summary-card.danger {
    background-color: #dc3545;
    color: white;
}
</style>
@endsection