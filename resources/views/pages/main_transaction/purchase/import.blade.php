@extends('layouts.default')
@section('page_title')
    Impor Pembelian
@stop
@section('content')
    <div class="card mb-6">
        <h5 class="card-header">Impor Pembelian</h5>
        <form class="card-body">
            <!-- Instructions Section -->
            <div class="row gy-3 mt-0">
                <div class="col-md-12">
                    <div class="alert alert-primary alert-dismissible d-flex" role="alert">
                        <span class="alert-icon rounded">
                            <i class="icon-base ti tabler-bell icon-md w-px-30"></i>
                        </span>
                        <div class="d-flex flex-column ps-1">
                            <h5 class="alert-heading mb-2"><b>Tahapan Impor Pembelian!</b></h5>
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
                                        <li>Masukkan data sesuai format kolom: Tanggal, No Referensi, Supplier, Jenis Transaksi, Total, Status.</li>
                                        <li>Pastikan no referensi unik dan tanggal dalam format YYYY-MM-DD.</li>
                                        <li>Pilih supplier dan status yang valid sesuai dengan data master.</li>
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
                                        <li>Data akan disimpan ke dalam sistem dan bisa langsung terlihat di list pembelian</li>
                                    </ul>
                                </li>
                            </ol>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Download Template Section -->
            <h6>1. Unduh Template</h6>
            <button type="button" id="btn-download-template" class="btn btn-primary btn-lg waves-effect waves-light">
                <span class="icon-xs icon-base ti tabler-file-type-xls me-2"></span>Download Template
            </button>
            <hr class="my-6 mx-n6" />

            <!-- Upload Template Section -->
            <h6>2. Unggah Template</h6>
            <div class="row">
                <div class="col-md-4">
                    <input class="form-control" type="file" id="import-file" accept=".xlsx,.xls,.csv">
                </div>
                <div class="col-md-4">
                    <button type="button" id="btn-upload-file" class="btn btn-md btn-primary waves-effect waves-light">
                        <span class="icon-xs icon-base ti tabler-upload me-2"></span>Upload File
                    </button>
                </div>
            </div>
            <hr class="my-6 mx-n6" />

            <!-- Validation Section -->
            <h6>3. Validasi Data</h6>
            <div class="row" id="validation-section" style="display: none;">
                <!-- Summary Cards -->
                <div class="row">
                    <div class="col-lg-4 col-sm-6">
                        <div class="card card-border-shadow-primary h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="avatar me-4">
                                        <span class="avatar-initial rounded bg-label-primary">
                                            <i class="icon-base ti tabler-upload icon-28px"></i>
                                        </span>
                                    </div>
                                    <h4 class="mb-0" id="total-uploaded">0 Baris</h4>
                                </div>
                                <p class="mb-1">Data Yang Diunggah</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <div class="card card-border-shadow-success h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="avatar me-4">
                                        <span class="avatar-initial rounded bg-label-success">
                                            <i class="icon-base ti tabler-check icon-28px"></i>
                                        </span>
                                    </div>
                                    <h4 class="mb-0" id="total-success">0 Baris</h4>
                                </div>
                                <p class="mb-1">Data Sukses</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <div class="card card-border-shadow-danger h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="avatar me-4">
                                        <span class="avatar-initial rounded bg-label-danger">
                                            <i class="icon-base ti tabler-x icon-28px"></i>
                                        </span>
                                    </div>
                                    <h4 class="mb-0" id="total-error">0 Baris</h4>
                                </div>
                                <p class="mb-1">Data Error</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <br>
                
                <!-- Success Data Table -->
                <div class="row" id="success-data-section">
                    <h6>Data Sukses</h6>
                    <div class="card-datatable">
                        <table class="success-table table table-bordered">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Tanggal</th>
                                    <th>No Referensi</th>
                                    <th>Supplier</th>
                                    <th>Jenis</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                <!-- Error Data Table -->
                <div class="row" id="error-data-section">
                    <h6>Data Error</h6>
                    <div class="card-datatable">
                        <table class="error-table table table-bordered">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Tanggal</th>
                                    <th>No Referensi</th>
                                    <th>Supplier</th>
                                    <th>Jenis</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Error Message</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 text-end">
                <button type="button" class="btn btn-label-danger me-3" id="cancel-btn">Batalkan</button>
                <button type="button" class="btn btn-success" id="import-btn" disabled>Import Data</button>
            </div>
        </form>
    </div>
@endsection

@section('js_script')
<script>
$(function() {
    let uploadedData = [];
    let successTable, errorTable;
    
    // Download template
    $('#btn-download-template').on('click', function() {
        window.location.href = "{{ route('main_transaction.purchase.download_template') }}";
    });
    
    // Upload file
    $('#btn-upload-file').on('click', function() {
        const fileInput = $('#import-file')[0];
        const file = fileInput.files[0];
        
        if (!file) {
            Swal.fire({
                title: 'Error!',
                text: 'Silakan pilih file terlebih dahulu',
                icon: 'error',
                customClass: { confirmButton: 'btn btn-primary' },
                buttonsStyling: false
            });
            return;
        }
        
        const formData = new FormData();
        formData.append('file', file);
        formData.append('_token', '{{ csrf_token() }}');
        
        // Show loading
        Swal.fire({
            title: 'Processing...',
            text: 'Sedang memvalidasi file',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        $.ajax({
            url: "{{ route('main_transaction.purchase.validate_import') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.close();
                
                if (response.success) {
                    uploadedData = response.success_data;
                    displayValidationResults(response);
                    $('#validation-section').show();
                    $('#import-btn').prop('disabled', response.error_count > 0);
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: response.message || 'Gagal memvalidasi file',
                        icon: 'error',
                        customClass: { confirmButton: 'btn btn-primary' },
                        buttonsStyling: false
                    });
                }
            },
            error: function() {
                Swal.close();
                Swal.fire({
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat upload file',
                    icon: 'error',
                    customClass: { confirmButton: 'btn btn-primary' },
                    buttonsStyling: false
                });
            }
        });
    });
    
    // Display validation results
    function displayValidationResults(data) {
        // Update summary cards
        $('#total-uploaded').text(data.total_count + ' Baris');
        $('#total-success').text(data.success_count + ' Baris');
        $('#total-error').text(data.error_count + ' Baris');
        
        // Initialize success table
        if (successTable) {
            successTable.destroy();
        }
        
        successTable = new DataTable('.success-table', {
            data: data.success_data,
            columns: [
                { data: null, defaultContent: '', className: 'dt-control' },
                { data: 'date' },
                { data: 'reference_no' },
                { data: 'customer' },
                { data: 'type' },
                { data: 'total' },
                { data: 'status' }
            ],
            columnDefs: [{
                className: 'dt-control',
                orderable: false,
                targets: 0,
                searchable: false
            }],
            scrollX: true,
            responsive: true
        });
        
        // Initialize error table
        if (errorTable) {
            errorTable.destroy();
        }
        
        errorTable = new DataTable('.error-table', {
            data: data.error_data,
            columns: [
                { data: null, defaultContent: '', className: 'dt-control' },
                { data: 'date' },
                { data: 'reference_no' },
                { data: 'customer' },
                { data: 'type' },
                { data: 'total' },
                { data: 'status' },
                { 
                    data: 'errors',
                    render: function(data, type, row) {
                        return `<span class="text-danger">${data.join(', ')}</span>`;
                    }
                }
            ],
            columnDefs: [{
                className: 'dt-control',
                orderable: false,
                targets: 0,
                searchable: false
            }],
            scrollX: true,
            responsive: true
        });
    }
    
    // Import data
    $('#import-btn').on('click', function() {
        if (uploadedData.length === 0) {
            Swal.fire({
                title: 'Error!',
                text: 'Tidak ada data yang valid untuk diimport',
                icon: 'error',
                customClass: { confirmButton: 'btn btn-primary' },
                buttonsStyling: false
            });
            return;
        }
        
        Swal.fire({
            title: 'Konfirmasi Import',
            text: `Apakah Anda yakin ingin mengimport ${uploadedData.length} data pembelian?`,
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
    
    function processImport() {
        Swal.fire({
            title: 'Importing...',
            text: 'Sedang mengimport data pembelian',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        $.ajax({
            url: "{{ route('main_transaction.purchase.process_import') }}",
            type: 'POST',
            data: {
                data: uploadedData,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                Swal.close();
                
                if (response.success) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: `${response.imported_count} data pembelian berhasil diimport`,
                        icon: 'success',
                        customClass: { confirmButton: 'btn btn-primary' },
                        buttonsStyling: false
                    }).then(() => {
                        window.location.href = "{{ route('main_transaction.purchase.index') }}";
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: response.message || 'Gagal mengimport data',
                        icon: 'error',
                        customClass: { confirmButton: 'btn btn-primary' },
                        buttonsStyling: false
                    });
                }
            },
            error: function() {
                Swal.close();
                Swal.fire({
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat import data',
                    icon: 'error',
                    customClass: { confirmButton: 'btn btn-primary' },
                    buttonsStyling: false
                });
            }
        });
    }
    
    // Cancel button
    $('#cancel-btn').on('click', function() {
        window.location.href = "{{ route('main_transaction.purchase.index') }}";
    });
});
</script>
@endsection