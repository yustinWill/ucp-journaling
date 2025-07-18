@extends('layouts.default')
@section('page_title')
    Import Roles
@stop
@section('content')
    <div class="card mb-6">
        <h5 class="card-header">Import Role Data</h5>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h6>Import Configuration</h6>
                    <p class="text-muted">Upload a file containing role data to import into the system.</p>
                    
                    <form id="importForm" enctype="multipart/form-data">
                        <div class="row g-3 mb-4">
                            <div class="col-md-12">
                                <label class="form-label">Select File <span style="color: red;">*</span></label>
                                <input type="file" class="form-control" id="importFile" accept=".xlsx,.csv">
                                <div class="form-text">Supported formats: Excel (.xlsx), CSV (.csv). Maximum file size: 10MB.</div>
                            </div>
                        </div>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Import Mode</label>
                                <select class="global-select2 form-select" id="importMode" data-allow-clear="true">
                                    @foreach($importModes as $mode)
                                        <option value="{{ $mode['value'] }}">{{ $mode['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Duplicate Handling</label>
                                <select class="global-select2 form-select" id="duplicateHandling" data-allow-clear="true">
                                    @foreach($duplicateHandlings as $handling)
                                        <option value="{{ $handling['value'] }}">{{ $handling['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-12">
                                <label class="form-label">Import Options</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="validateData" checked>
                                    <label class="form-check-label" for="validateData">
                                        Validate Data Before Import
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="skipErrors" checked>
                                    <label class="form-check-label" for="skipErrors">
                                        Skip Rows with Errors
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="generateReport" checked>
                                    <label class="form-check-label" for="generateReport">
                                        Generate Import Report
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-12">
                                <label class="form-label">File Structure</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="hasHeader" checked>
                                    <label class="form-check-label" for="hasHeader">
                                        First Row Contains Headers
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="skipEmptyRows" checked>
                                    <label class="form-check-label" for="skipEmptyRows">
                                        Skip Empty Rows
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="pt-4">
                            <button type="button" class="btn btn-primary me-3" id="importBtn">
                                <i class="icon-base ti tabler-upload me-1"></i>
                                Import Data
                            </button>
                            <button type="button" class="btn btn-secondary me-3" id="downloadTemplateBtn">
                                <i class="icon-base ti tabler-download me-1"></i>
                                Download Template
                            </button>
                            <a href="{{ route('master_data.role.index') }}" class="btn btn-outline-secondary">
                                <i class="icon-base ti tabler-arrow-left me-1"></i>
                                Back to List
                            </a>
                        </div>
                    </form>
                </div>
                
                <div class="col-md-4">
                    <h6>Import Instructions</h6>
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title">Required Fields</h6>
                            <ul class="mb-0">
                                <li>Role Name (required)</li>
                                <li>Description (optional)</li>
                                <li>Status (Active/Inactive)</li>
                                <li>Permissions (comma-separated)</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="card mt-3">
                        <div class="card-body">
                            <h6 class="card-title">Import Tips</h6>
                            <ul class="mb-0">
                                <li>Use the template for best results</li>
                                <li>Ensure all required fields are filled</li>
                                <li>Check for duplicate role names</li>
                                <li>Validate permissions exist in system</li>
                                <li>Maximum 1000 roles per import</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="card mt-3">
                        <div class="card-body">
                            <h6 class="card-title">Sample Data</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Role Name</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Editor</td>
                                            <td>Active</td>
                                        </tr>
                                        <tr>
                                            <td>Viewer</td>
                                            <td>Active</td>
                                        </tr>
                                        <tr>
                                            <td>Moderator</td>
                                            <td>Inactive</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js_script')
<script>
    $(function() {
        
        // Import button handler
        $('#importBtn').click(function() {
            const file = $('#importFile')[0].files[0];
            
            if (!file) {
                Swal.fire({
                    icon: 'error',
                    title: 'No File Selected',
                    text: 'Please select a file to import.',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                });
                return;
            }
            
            // Validate file size (10MB limit)
            if (file.size > 10 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Too Large',
                    text: 'Please select a file smaller than 10MB.',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                });
                return;
            }
            
            // Validate file type
            const allowedTypes = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'text/csv'];
            if (!allowedTypes.includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid File Type',
                    text: 'Please select an Excel (.xlsx) or CSV (.csv) file.',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                });
                return;
            }
            
            const importData = {
                file: file,
                import_mode: $('#importMode').val(),
                duplicate_handling: $('#duplicateHandling').val(),
                validate_data: $('#validateData').is(':checked'),
                skip_errors: $('#skipErrors').is(':checked'),
                generate_report: $('#generateReport').is(':checked'),
                has_header: $('#hasHeader').is(':checked'),
                skip_empty_rows: $('#skipEmptyRows').is(':checked')
            };
            
            Swal.fire({
                title: 'Import Role Data?',
                text: `Import ${file.name} with ${importData.import_mode} mode?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Import!',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // TODO: Implement actual import functionality
                    console.log('Import data:', importData);
                    
                    // Simulate import process
                    Swal.fire({
                        title: 'Importing...',
                        text: 'Please wait while we process your file.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Simulate import delay
                    setTimeout(() => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Import Complete!',
                            html: `
                                <div class="text-start">
                                    <p><strong>Import Summary:</strong></p>
                                    <ul>
                                        <li>Total Rows: 10</li>
                                        <li>Successfully Imported: 8</li>
                                        <li>Skipped (Duplicates): 1</li>
                                        <li>Errors: 1</li>
                                    </ul>
                                </div>
                            `,
                            customClass: {
                                confirmButton: 'btn btn-success'
                            },
                            buttonsStyling: false
                        }).then(() => {
                            // Redirect to roles list
                            window.location.href = "{{ route('master_data.role.index') }}";
                        });
                    }, 3000);
                }
            });
        });
        
        // Download template button handler
        $('#downloadTemplateBtn').click(function() {
            // TODO: Implement template download
            Swal.fire({
                icon: 'info',
                title: 'Download Template',
                text: 'Template download functionality will be implemented.',
                customClass: {
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false
            });
        });
        
        // File input change handler
        $('#importFile').change(function() {
            const file = this.files[0];
            if (file) {
                const fileSize = (file.size / 1024 / 1024).toFixed(2);
                const fileName = file.name;
                
                $(this).next('.form-text').html(`
                    Selected: <strong>${fileName}</strong> (${fileSize} MB)
                    <br>Supported formats: Excel (.xlsx), CSV (.csv). Maximum file size: 10MB.
                `);
            }
        });
    });
</script>
@endsection