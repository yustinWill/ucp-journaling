@extends('layouts.default')
@section('page_title')
    Export Roles
@stop
@section('content')
    <div class="card mb-6">
        <h5 class="card-header">Export Role Data</h5>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h6>Export Configuration</h6>
                    <p class="text-muted">Configure your export settings and download the role data in your preferred format.</p>
                    
                    <form id="exportForm" autocomplete="off">
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Export Format</label>
                                <select class="global-select2 form-select" id="exportFormat" data-allow-clear="false">
                                    @foreach($exportFormats as $format)
                                        <option value="{{ $format['value'] }}">{{ $format['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status Filter</label>
                                <select class="global-select2 form-select" id="statusFilter" data-allow-clear="false">
                                    @foreach($statusFilters as $filter)
                                        <option value="{{ $filter['value'] }}">{{ $filter['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-12">
                                <label class="form-label">Include Additional Data</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="includePermissions" checked>
                                    <label class="form-check-label" for="includePermissions">
                                        Include Permission Details
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="includeUsers" checked>
                                    <label class="form-check-label" for="includeUsers">
                                        Include User Count
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="includeTimestamps">
                                    <label class="form-check-label" for="includeTimestamps">
                                        Include Created/Updated Dates
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-12">
                                <label class="form-label">Export Options</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="includeHeader" checked>
                                    <label class="form-check-label" for="includeHeader">
                                        Include Column Headers
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="includeIndex" checked>
                                    <label class="form-check-label" for="includeIndex">
                                        Include Row Numbers
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="pt-4">
                            <button type="button" class="btn btn-primary me-3" id="exportBtn">
                                <i class="icon-base ti tabler-download me-1"></i>
                                Export Data
                            </button>
                            <a href="{{ route('master_data.role.index') }}" class="btn btn-secondary">
                                <i class="icon-base ti tabler-arrow-left me-1"></i>
                                Back to List
                            </a>
                        </div>
                    </form>
                </div>
                
                <div class="col-md-4">
                    <h6>Export Preview</h6>
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title">Fields to Export</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Field</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data as $item)
                                        <tr>
                                            <td>{{ $item['no'] }}</td>
                                            <td>{{ $item['value'] }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mt-3">
                        <div class="card-body">
                            <h6 class="card-title">Export Statistics</h6>
                            <div class="row text-center">
                                <div class="col-6">
                                    <h4 class="mb-0 text-primary">5</h4>
                                    <small class="text-muted">Total Roles</small>
                                </div>
                                <div class="col-6">
                                    <h4 class="mb-0 text-success">4</h4>
                                    <small class="text-muted">Active Roles</small>
                                </div>
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
        
        $('#exportBtn').click(function() {
            const format = $('#exportFormat').val();
            const statusFilter = $('#statusFilter').val();
            const includePermissions = $('#includePermissions').is(':checked');
            const includeUsers = $('#includeUsers').is(':checked');
            const includeTimestamps = $('#includeTimestamps').is(':checked');
            const includeHeader = $('#includeHeader').is(':checked');
            const includeIndex = $('#includeIndex').is(':checked');
            
            const exportData = {
                format: format,
                status_filter: statusFilter,
                include_permissions: includePermissions,
                include_users: includeUsers,
                include_timestamps: includeTimestamps,
                include_header: includeHeader,
                include_index: includeIndex
            };
            
            Swal.fire({
                title: 'Export Role Data?',
                text: `Export data in ${format.toUpperCase()} format?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Export!',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // TODO: Implement actual export functionality
                    console.log('Export data:', exportData);
                    
                    // Simulate export process
                    Swal.fire({
                        title: 'Exporting...',
                        text: 'Please wait while we prepare your file.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Simulate export delay
                    setTimeout(() => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Export Complete!',
                            text: 'Your file has been downloaded.',
                            customClass: {
                                confirmButton: 'btn btn-success'
                            },
                            buttonsStyling: false
                        });
                    }, 2000);
                }
            });
        });
    });
</script>
@endsection