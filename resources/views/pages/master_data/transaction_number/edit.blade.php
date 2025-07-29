@extends('layouts.default')
@section('page_title')
    Edit {{ $transactionNumber->module_display_name }} - Master Nomor Transaksi
@stop

@section('content')
<div class="card mb-6">
    <h4 class="card-header">Edit Konfigurasi - {{ $transactionNumber->module_display_name }}</h4>
    <form class="card-body" id="transaction-number-form">
        <input type="hidden" id="transaction-number-id" value="{{ $transactionNumber->id }}">
        <input type="hidden" id="module-initial" value="{{ $transactionNumber->module_initial }}">
        <input type="hidden" id="module-name" value="{{ $transactionNumber->module_name }}">
        @csrf
        @method('POST')
        
        <!-- Template Configuration Section -->
        <div class="section-header mt-12">
            <h6>Konfigurasi Template</h6>
            <hr class="mx-n6">
        </div>
        
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Format Template <span style="color: red;">*</span></label>
                <input type="text" class="form-control" id="format-template" name="format_template" 
                       value="{{ $transactionNumber->format_template }}" required 
                       placeholder="Contoh: RIYO-{YEAR}-{COUNTER}" />
                <small class="form-text text-muted">Gunakan placeholder dalam kurung kurawal {}</small>
            </div>
            <div class="col-md-6">
                <label class="form-label">Live Preview</label>
                <div class="input-group">
                    <input type="text" class="form-control fw-bold text-success bg-light" id="live-preview" 
                           value="" readonly placeholder="Preview akan muncul di sini..." />
                    <button type="button" class="btn btn-outline-secondary" id="refresh-preview" title="Refresh Preview">
                        <i class="icon-base ti tabler-refresh"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Counter Configuration Section -->
        <div class="section-header mt-12">
            <h6>Konfigurasi Counter</h6>
            <hr class="mx-n6">
        </div>
        
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Counter Saat Ini <span style="color: red;">*</span></label>
                <input type="number" class="form-control" id="current-counter" name="current_counter" 
                       value="{{ $transactionNumber->current_counter }}" min="1" required />
                <small class="form-text text-muted">Nomor yang akan digunakan selanjutnya</small>
            </div>
            <div class="col-md-6">
                <label class="form-label">Counter Padding <span style="color: red;">*</span></label>
                <input type="number" class="form-control" id="counter-padding" name="counter_padding" 
                       value="{{ $transactionNumber->counter_padding }}" min="1" max="10" required />
                <small class="form-text text-muted">Jumlah digit untuk zero-padding (contoh: 5 = 00001)</small>
            </div>
        </div>
        
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Reset Frequency <span style="color: red;">*</span></label>
                <select class="global-select2 form-select" id="reset-frequency" name="reset_frequency" data-allow-clear="false" required>
                    @foreach($resetFrequencies as $frequency)
                        <option value="{{ $frequency['value'] }}" @if($transactionNumber->reset_frequency == $frequency['value']) selected @endif>
                            {{ $frequency['label'] }}
                        </option>
                    @endforeach
                </select>
                <small class="form-text text-muted">Kapan counter akan direset ke 1</small>
            </div>
            <div class="col-md-6">
                <label class="form-label">Reset Counter</label>
                <div class="input-group">
                    <input type="number" class="form-control" id="reset-counter-value" min="1" placeholder="Nilai baru..." />
                    <button type="button" class="btn btn-outline-warning" id="reset-counter-btn">
                        <i class="icon-base ti tabler-refresh me-1"></i>Reset
                    </button>
                </div>
                <small class="form-text text-muted">Reset counter ke nilai tertentu</small>
            </div>
        </div>

        <!-- Available Placeholders Section -->
        <div class="section-header mt-12">
            <h6>Placeholder yang Tersedia</h6>
            <hr class="mx-n6">
        </div>
        
        <div class="row g-2 mb-4">
            @foreach($placeholders as $placeholder => $info)
                <div class="col-md-3 col-6">
                    <button type="button" class="btn btn-outline-secondary btn-sm w-100 placeholder-btn" 
                            data-placeholder="{{ $placeholder }}" title="{{ $info['description'] }}">
                        <code>{{ $placeholder }}</code>
                        @if($placeholder === '{COUNTER}')
                            <span class="badge bg-danger ms-1">*</span>
                        @endif
                    </button>
                </div>
            @endforeach
        </div>

        <!-- Template Examples -->
        <div class="alert alert-info">
            <h6 class="alert-heading">Contoh Template</h6>
            <ul class="mb-0">
                <li><code>RIYO-{YEAR}-{COUNTER}</code> → RIYO-2025-00001</li>
                <li><code>{MODULE_INITIAL}-{YY}{MM}{DD}-{COUNTER}</code> → EX-250726-001</li>
                <li><code>{MODULE_NAME}-{COUNTER}</code> → EXPENSE-00001</li>
                <li><code>TXN-{YEAR}-{MODULE_INITIAL}-{COUNTER}</code> → TXN-2025-EX-00001</li>
            </ul>
        </div>

        <!-- Action Buttons -->
        <div class="pt-6 text-end">
            <a href="{{ route('master_data.transaction_number.detail', $transactionNumber->id) }}" class="btn btn-label-danger me-3">Batalkan</a>
            <button type="submit" class="btn btn-success" id="submit-btn">Simpan Konfigurasi</button>
        </div>
    </form>
</div>
@stop

@section('js_script')
<script>
$(function() {
    // Initialize live preview
    updateLivePreview();
    
    // Update preview when template or counter changes
    $('#format-template, #current-counter, #counter-padding').on('input', function() {
        updateLivePreview();
    });
    
    // Refresh preview button
    $('#refresh-preview').on('click', function() {
        updateLivePreview();
    });
    
    // Placeholder buttons
    $('.placeholder-btn').on('click', function() {
        const placeholder = $(this).data('placeholder');
        const templateInput = $('#format-template');
        const currentValue = templateInput.val();
        const cursorPos = templateInput[0].selectionStart;
        
        // Insert placeholder at cursor position
        const newValue = currentValue.substring(0, cursorPos) + placeholder + currentValue.substring(cursorPos);
        templateInput.val(newValue);
        
        // Update cursor position
        const newCursorPos = cursorPos + placeholder.length;
        templateInput[0].setSelectionRange(newCursorPos, newCursorPos);
        templateInput.focus();
        
        // Update preview
        updateLivePreview();
    });
    
    // Reset counter button
    $('#reset-counter-btn').on('click', function() {
        const newValue = $('#reset-counter-value').val();
        
        if (!newValue || newValue < 1) {
            Swal.fire({
                title: 'Nilai Tidak Valid',
                text: 'Masukkan nilai counter yang valid (minimal 1).',
                icon: 'warning',
                customClass: {
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false
            });
            return;
        }
        
        Swal.fire({
            title: 'Reset Counter?',
            text: `Counter akan direset ke ${newValue}. Tindakan ini tidak dapat dibatalkan.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Reset!',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-warning',
                cancelButton: 'btn btn-label-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                resetCounter(newValue);
            }
        });
    });
    
    // Form submission
    $('#transaction-number-form').on('submit', function(e) {
        e.preventDefault();
        
        // Validate required fields
        if (!$('#format-template').val() || !$('#current-counter').val() || !$('#counter-padding').val()) {
            Swal.fire({
                title: 'Field Wajib!',
                text: 'Harap lengkapi semua field yang wajib diisi.',
                icon: 'warning',
                customClass: {
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false
            });
            return;
        }
        
        // Show loading state
        $('#submit-btn').prop('disabled', true).html(
            '<span class="spinner-border spinner-border-sm me-2"></span>Loading...'
        );
        
        // Collect form data
        const formData = {
            _token: '{{ csrf_token() }}',
            id: $('#transaction-number-id').val(),
            format_template: $('#format-template').val(),
            current_counter: $('#current-counter').val(),
            counter_padding: $('#counter-padding').val(),
            reset_frequency: $('#reset-frequency').val()
        };
        
        // Submit data via AJAX
        $.ajax({
            url: '{{ route("master_data.transaction_number.update") }}',
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: response.message || 'Konfigurasi berhasil disimpan.',
                    icon: 'success',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    // Redirect to detail page
                    window.location.href = '{{ route("master_data.transaction_number.detail", $transactionNumber->id) }}';
                });
            },
            error: function(xhr, status, error) {
                let errorMessage = 'Terjadi kesalahan saat menyimpan data.';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage += '\n\n' + xhr.responseJSON.errors.join('\n');
                }
                
                Swal.fire({
                    title: 'Gagal!',
                    text: errorMessage,
                    icon: 'error',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                });
                
                // Reset button state
                $('#submit-btn').prop('disabled', false).html('Simpan Konfigurasi');
            }
        });
    });
    
    function updateLivePreview() {
        const template = $('#format-template').val();
        const counter = $('#current-counter').val() || 1;
        const padding = $('#counter-padding').val() || 5;
        const moduleInitial = $('#module-initial').val();
        const moduleName = $('#module-name').val();
        
        if (!template) {
            $('#live-preview').val('');
            return;
        }
        
        $.ajax({
            url: '{{ route("master_data.transaction_number.preview_format") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                template: template,
                module_initial: moduleInitial,
                module_name: moduleName,
                counter: parseInt(counter) + 1, // Preview next number
                padding: parseInt(padding)
            },
            success: function(response) {
                if (response.success) {
                    $('#live-preview').val(response.preview).removeClass('is-invalid');
                } else {
                    $('#live-preview').val('Error: ' + response.errors.join(', ')).addClass('is-invalid');
                }
            },
            error: function() {
                $('#live-preview').val('Error loading preview').addClass('is-invalid');
            }
        });
    }
    
    function resetCounter(newValue) {
        $.ajax({
            url: '{{ route("master_data.transaction_number.reset_counter", $transactionNumber->id) }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                counter: newValue
            },
            success: function(response) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: response.message || 'Counter berhasil direset.',
                    icon: 'success',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                });
                
                // Update current counter field
                $('#current-counter').val(newValue);
                $('#reset-counter-value').val('');
                
                // Update preview
                updateLivePreview();
            },
            error: function(xhr) {
                let errorMessage = 'Terjadi kesalahan saat mereset counter.';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    title: 'Gagal!',
                    text: errorMessage,
                    icon: 'error',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                });
            }
        });
    }
});
</script>
@endsection