@extends('layouts.default')
@section('page_title')
    Import Purchase Delivery
@stop
@section('content')
    <div class="card mb-6">
        <h5 class="card-header">Import Purchase Delivery</h5>
        <form class="card-body">
            <!-- Instructions Section -->
            <div class="row gy-3 mt-0">
                <div class="col-md-12">
                    <div class="alert alert-primary alert-dismissible d-flex" role="alert">
                        <span class="alert-icon rounded">
                            <i class="icon-base ti tabler-bell icon-md w-px-30"></i>
                        </span>
                        <div class="d-flex flex-column ps-1">
                            <h5 class="alert-heading mb-2"><b>Tahapan Import Purchase Delivery!</b></h5>
                            <ol>
                                <li><b>Unduh Template</b>
                                    <ul>
                                        <li>Klik tombol "Download Template".</li>
                                        <li>Sistem akan memberikan file Excel sesuai format yang dibutuhkan.</li>
                                    </ul>
                                </li>
                                <li><b>Isi Template</b>
                                    <ul>
                                        <li>Buka file template yang telah diunduh.</li>
                                        <li>Masukkan data sesuai format kolom: No. Delivery, Tanggal, Supplier, PO Reference, Delivery Date, Carrier, Tracking Number, Total Items, Delivered Items, Warehouse, Received By, Notes.</li>
                                        <li>Pastikan nomor delivery unik dan tanggal dalam format YYYY-MM-DD.</li>
                                        <li>Pilih supplier, PO reference, dan warehouse yang valid sesuai dengan data master.</li>
                                    </ul>
                                </li>
                                <li><b>Unggah Template</b>
                                    <ul>
                                        <li>Setelah data diisi, simpan file.</li>
                                        <li>Kembali ke sistem dan klik tombol "Upload Template".</li>
                                        <li>Pilih file yang telah diisi, lalu klik "Unggah".</li>
                                    </ul>
                                </li>
                                <li><b>Cek dan Validasi Data</b>
                                    <ul>
                                        <li>Sistem akan melakukan validasi otomatis terhadap data yang diunggah.</li>
                                        <li>Jika ada kesalahan, sistem akan menampilkan pesan error dengan detail baris dan kolom.</li>
                                        <li>Perbaiki data di template dan ulangi proses unggah jika diperlukan.</li>
                                    </ul>
                                </li>
                                <li><b>Proses Import Data</b>
                                    <ul>
                                        <li>Setelah semua data valid, klik "Import"</li>
                                        <li>Data akan disimpan ke dalam sistem dan bisa langsung terlihat di list purchase delivery</li>
                                    </ul>
                                </li>
                            </ol>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            </div>

            <!-- Filter Options -->
            <div class="section-header mt-12">
                <h6>Filter Import</h6>
                <hr class="mx-n6">
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Supplier</label>
                    <select class="global-select2 form-select" id="supplier-filter" data-allow-clear="true">
                        <option value="">Semua Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier['value'] }}">{{ $supplier['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Gudang</label>
                    <select class="global-select2 form-select" id="warehouse-filter" data-allow-clear="true">
                        <option value="">Semua Gudang</option>
                        @foreach($storages as $storage)
                            <option value="{{ $storage['value'] }}">{{ $storage['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Step 1: Download Template -->
            <div class="section-header mt-12">
                <h6>Step 1: Download Template</h6>
                <hr class="mx-n6">
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <div class="alert alert-info" role="alert">
                        <i class="icon-base ti tabler-info-circle me-2"></i>
                        Unduh template Excel untuk memulai proses import data purchase delivery.
                    </div>
                    <a href="{{ route('main_transaction.purchase_delivery.download_template') }}" 
                       class="btn btn-lg btn-primary waves-effect waves-light">
                        <i class="icon-base ti tabler-download me-2"></i>
                        Download Template
                    </a>
                </div>
            </div>

            <!-- Step 2: Upload Template -->
            <div class="section-header mt-12">
                <h6>Step 2: Upload Template</h6>
                <hr class="mx-n6">
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <div class="alert alert-warning" role="alert">
                        <i class="icon-base ti tabler-upload me-2"></i>
                        Unggah file template yang sudah diisi dengan data purchase delivery.
                    </div>
                    <div class="mb-3">
                        <input type="file" class="form-control" id="import-file" accept=".xlsx,.xls,.csv" />
                        <div class="form-text">Format file yang didukung: Excel (.xlsx, .xls) atau CSV (.csv)</div>
                    </div>
                    <button type="button" class="btn btn-success waves-effect waves-light" id="upload-btn" disabled>
                        <i class="icon-base ti tabler-upload me-2"></i>
                        Upload & Validasi
                    </button>
                </div>
            </div>

            <!-- Step 3: Validation Results -->
            <div class="section-header mt-12" id="validation-section" style="display: none;">
                <h6>Step 3: Hasil Validasi</h6>
                <hr class="mx-n6">
            </div>
            
            <div id="validation-results" style="display: none;">
                <!-- Validation results will be populated here -->
            </div>

            <!-- Step 4: Import Data -->
            <div class="section-header mt-12" id="import-section" style="display: none;">
                <h6>Step 4: Import Data</h6>
                <hr class="mx-n6">
            </div>
            
            <div id="import-controls" style="display: none;">
                <div class="row g-3 mb-4">
                    <div class="col-md-12">
                        <div class="alert alert-success" role="alert">
                            <i class="icon-base ti tabler-check me-2"></i>
                            Data sudah tervalidasi dan siap untuk diimport ke sistem.
                        </div>
                        <button type="button" class="btn btn-primary waves-effect waves-light" id="import-btn">
                            <i class="icon-base ti tabler-database-import me-2"></i>
                            Import Data ke Sistem
                        </button>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <div class="d-flex justify-content-end gap-3 mt-6">
                <a href="{{ route('main_transaction.purchase_delivery.index') }}" class="btn btn-outline-secondary waves-effect">
                    <i class="icon-base ti tabler-arrow-left me-2"></i>
                    Kembali ke List
                </a>
            </div>
        </form>
    </div>
@stop

@section('js_script')
<script>
$(function() {
    let uploadedData = null;
    let validationResults = null;

    // File input change handler
    $('#import-file').on('change', function() {
        const file = this.files[0];
        if (file) {
            $('#upload-btn').prop('disabled', false);
        } else {
            $('#upload-btn').prop('disabled', true);
        }
    });

    // Upload and validate handler
    $('#upload-btn').on('click', function() {
        const fileInput = $('#import-file')[0];
        const file = fileInput.files[0];
        
        if (!file) {
            Swal.fire({
                title: 'File Tidak Dipilih',
                text: 'Silakan pilih file template yang akan diupload.',
                icon: 'warning',
                customClass: {
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false
            });
            return;
        }

        // Show loading state
        const btn = $(this);
        const originalText = btn.html();
        btn.html('<i class="icon-base ti tabler-loader-2 me-2"></i>Validating...').prop('disabled', true);

        // Create FormData for file upload
        const formData = new FormData();
        formData.append('file', file);
        formData.append('supplier_filter', $('#supplier-filter').val());
        formData.append('warehouse_filter', $('#warehouse-filter').val());

        // AJAX call to validate file
        $.ajax({
            url: '{{ route("main_transaction.purchase_delivery.validate_import") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                validationResults = response;
                displayValidationResults(response);
                
                // Show validation section
                $('#validation-section').show();
                $('#validation-results').show();
                
                // Show import section if validation passed
                if (response.success && response.errors.length === 0) {
                    $('#import-section').show();
                    $('#import-controls').show();
                }
                
                // Reset button
                btn.html(originalText).prop('disabled', false);
            },
            error: function(xhr, status, error) {
                let errorMessage = 'Terjadi kesalahan saat memvalidasi file.';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    title: 'Validasi Gagal!',
                    text: errorMessage,
                    icon: 'error',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                });
                
                // Reset button
                btn.html(originalText).prop('disabled', false);
            }
        });
    });

    // Import data handler
    $('#import-btn').on('click', function() {
        if (!validationResults || !validationResults.success) {
            Swal.fire({
                title: 'Data Belum Valid',
                text: 'Silakan validasi data terlebih dahulu sebelum melakukan import.',
                icon: 'warning',
                customClass: {
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false
            });
            return;
        }

        // Confirmation dialog
        Swal.fire({
            title: 'Import Data?',
            text: `Akan mengimport ${validationResults.valid_count} data purchase delivery ke sistem.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Import',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-primary',
                cancelButton: 'btn btn-outline-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                performImport();
            }
        });
    });

    function displayValidationResults(results) {
        let html = '';
        
        if (results.success) {
            // Success summary
            html += `
                <div class="alert alert-success" role="alert">
                    <i class="icon-base ti tabler-check me-2"></i>
                    <strong>Validasi Berhasil!</strong> ${results.valid_count} data siap untuk diimport.
                </div>
            `;
            
            // Valid data table
            if (results.valid_data.length > 0) {
                html += `
                    <div class="card mt-4">
                        <h6 class="card-header text-success">Data Valid (${results.valid_data.length})</h6>
                        <div class="card-datatable">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No. Delivery</th>
                                        <th>Tanggal</th>
                                        <th>Supplier</th>
                                        <th>PO Reference</th>
                                        <th>Total Items</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                `;
                
                results.valid_data.forEach(function(row) {
                    html += `
                        <tr>
                            <td>${row.reference_no || '-'}</td>
                            <td>${row.date || '-'}</td>
                            <td>${row.supplier || '-'}</td>
                            <td>${row.po_reference || '-'}</td>
                            <td>${parseInt(row.total_items || 0).toLocaleString('id-ID')}</td>
                            <td><span class="badge bg-label-success">${row.status || '-'}</span></td>
                        </tr>
                    `;
                });
                
                html += `
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;
            }
        }
        
        // Error data table
        if (results.errors.length > 0) {
            html += `
                <div class="alert alert-danger mt-4" role="alert">
                    <i class="icon-base ti tabler-alert-circle me-2"></i>
                    <strong>Ditemukan ${results.errors.length} Error!</strong> Silakan perbaiki data dan upload ulang.
                </div>
                
                <div class="card mt-4">
                    <h6 class="card-header text-danger">Data Error (${results.errors.length})</h6>
                    <div class="card-datatable">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Baris</th>
                                    <th>Error</th>
                                    <th>Data</th>
                                </tr>
                            </thead>
                            <tbody>
            `;
            
            results.errors.forEach(function(error) {
                html += `
                    <tr>
                        <td>${error.row}</td>
                        <td><span class="badge bg-label-danger">${error.message}</span></td>
                        <td>${JSON.stringify(error.data)}</td>
                    </tr>
                `;
            });
            
            html += `
                            </tbody>
                        </table>
                    </div>
                </div>
            `;
        }
        
        $('#validation-results').html(html);
    }

    function performImport() {
        const btn = $('#import-btn');
        const originalText = btn.html();
        btn.html('<i class="icon-base ti tabler-loader-2 me-2"></i>Importing...').prop('disabled', true);

        $.ajax({
            url: '{{ route("main_transaction.purchase_delivery.process_import") }}',
            type: 'POST',
            data: {
                validation_key: validationResults.validation_key
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                Swal.fire({
                    title: 'Import Berhasil!',
                    text: `${response.imported_count} purchase delivery berhasil diimport ke sistem.`,
                    icon: 'success',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                }).then(() => {
                    window.location.href = '{{ route("main_transaction.purchase_delivery.index") }}';
                });
            },
            error: function(xhr, status, error) {
                let errorMessage = 'Terjadi kesalahan saat melakukan import.';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    title: 'Import Gagal!',
                    text: errorMessage,
                    icon: 'error',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                });
                
                // Reset button
                btn.html(originalText).prop('disabled', false);
            }
        });
    }
});
</script>
@endsection