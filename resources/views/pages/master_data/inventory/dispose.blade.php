@extends('layouts.default')

@section('page_title')
    Buat Transaksi - Pelepasan Aset
@stop

@section('content')
<div class="card mb-6">
    <h4 class="card-header">Buat Transaksi - Pelepasan Aset</h4>
    <form class="card-body" id="inventory-purchase-form" autocomplete="off">
        @csrf

        <!-- Informasi Aset Section -->
        <div class="section-header mt-12">
            <h6>Informasi Aset</h6>
            <hr class="mx-n6">
        </div>
        <!-- Row 1: Tanggal Transaksi, Nomor Transaksi -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Tanggal Transaksi <span style="color: red;">*</span></label>
                <input type="date" class="form-control global-single-date-picker" id="transaction_date"
                    name="disposal_date" value="{{ date('Y-m-d') }}" placeholder="YYYY-MM-DD" required />
            </div>
            <div class="col-md-6">
                <label class="form-label">Nomor Transaksi <span style="color: red;">*</span></label>
                <div class="input-group">
                    <input type="text" class="form-control" id="transaction_number" name="disposal_code"
                        placeholder="Auto-generated but editable" required />
                    <button type="button" class="btn btn-outline-secondary" id="refresh-transaction-number">
                        <i class="icon-base ti tabler-refresh"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Pilih Aset <span style="color: red;">*</span></label>
                <select class="form-select global-select2" name="asset_id" id="asset_id" data-allow-clear="false" required>
                    <option value="">Pilih Aset</option>
                    @foreach($assets as $asset)
                        <option value="{{ $asset->id }}">{{ $asset->code }} - {{ $asset->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Nomor Aset <span style="color: red;">*</span></label>
                <input type="text" class="form-control" name="asset_code" id="asset_code" placeholder="Cash Advance" disabled />
            </div>
        </div>
        <!-- Row 4: Nilai Pembelian, Deskripsi -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Nilai Pembelian <span style="color: red;">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="text" class="form-control cleave-numeral" id="acquisition_value" placeholder="500,000,000.00" disabled />
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Sisa Waktu Penyusutan <span style="color: red;">*</span></label>
                <div class="input-group">
                    <input type="number" class="form-control" id="depreciation_left_over" placeholder="12" disabled />
                    <span class="input-group-text">Bulan</span>
                </div>
            </div>
        </div>
<!-- Row 4: Nilai Pembelian, Deskripsi -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Deskripsi</label>
                <textarea class="form-control" name="description" id="description" rows="3" placeholder="Keterangan transaksi..." readonly></textarea>
            </div>
           <div class="col-md-6">
                <label class="form-label">Tag</label>
                <div class="mt-2" id ="tag-container">
                    {{-- @if($tags && count($tags) > 0)
                        @foreach($tags as $tag)
                            <span class="badge {{ $tag->preview }} me-1">{{ $tag->code }} - {{ $tag->name }}</span>
                        @endforeach
                    @else
                        <span class="text-muted">-</span>
                    @endif --}}
                </div>
            </div>
        </div>

        <!-- Informasi Pelepasan Aset -->
        <div class="section-header mt-12">
            <h6>Informasi Pelepasan Aset</h6>
            <hr class="mx-n6">
        </div>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Nilai Pelepasan <span style="color: red;">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="text" class="form-control cleave-numeral" name="disposal_value" id="disposal_value" placeholder="500,000,000.00"  />
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Nilai Buku <span style="color: red;">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="text" class="form-control cleave-numeral" id="total_left_over_value" placeholder="500,000,000.00" disabled />
                </div>
            </div>
        </div>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Nilai Selisih Pelepasan Aset <span style="color: red;">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="text" class="form-control cleave-numeral" name="release_different_value" id="release_different_value" placeholder="500,000,000.00"  disabled/>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Akun Selisih Pelepasan Aset <span style="color: red;">*</span></label>
                <select class="form-select global-select2" name="f_difference_coa_code" id="fixed_asset_account" data-allow-clear="false" required>
                    <option value="">Pilih Akun</option>
                    @foreach($coaAccounts as $coa)
                        <option value="{{ $coa->code }}">{{ $coa->code }} - {{ $coa->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Akumulasi Penyusutan <span style="color: red;">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="text" class="form-control cleave-numeral" id="total_depreciated_value" placeholder="500,000,000.00"  disabled/>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Akun Penjualan <span style="color: red;">*</span></label>
                <select class="form-select global-select2" name="f_sales_coa_code" id="sales_account" data-allow-clear="false" required>
                    <option value="">Pilih Akun</option>
                    @foreach($coaAccounts as $coa)
                        <option value="{{ $coa->code }}">{{ $coa->code }} - {{ $coa->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="pt-6 text-end">
            <a href="{{ route('master_data.inventory.index') }}" class="btn btn-label-danger me-3">Batalkan</a>
            <button type="submit" class="btn btn-success" id="submit-btn">Simpan</button>
        </div>
    </form>
</div>
@stop

@section('js_script')
<script>
$(function() {
    // Initialize Select2
    $('.global-select2').select2({
        allowClear: false
    });

    $(document).ready(function() {
        const detailUrl = "{{ route('master_data.inventory.get_asset_data', ':id') }}"; // Base route with placeholder
        $('#asset_id').on('change', function() {
            const assetId = $(this).val();

            if (assetId) {
                // Replace the placeholder ":id" with the actual assetId
                const url = detailUrl.replace(':id', assetId);

                // Make an AJAX call to fetch asset details
                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function(response) {
                        // Assuming the response contains the asset details
                        if (response.success) {
                            const asset = response.data;

                            // Populate the fields with the asset details
                            $('#asset_code').val(asset.code);
                            $('#acquisition_value').val(asset.acquisition_value.toLocaleString('id-ID', { minimumFractionDigits: 2 }));
                            $('#depreciation_left_over').val(asset.depreciation_left_over.total_left_over);
                            $('#description').val(asset.description);
                            $('#total_left_over_value').val(asset.depreciation_left_over.total_left_over_value.toLocaleString('id-ID', { minimumFractionDigits: 2 }));
                            $('#total_depreciated_value').val(asset.depreciation_left_over.total_depreciated_value.toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                            $('#tag-container').empty();

                            // Add tags to the tag container
                            if (asset.tags && asset.tags.length > 0) {
                                asset.tags.forEach(tag => {
                                    const tagHtml = `<span class="badge ${tag.preview} me-1">${tag.code} - ${tag.name}</span>`;
                                    $('#tag-container').append(tagHtml);
                                });
                            } else {
                                $('#tag-container').html('<span class="text-muted">-</span>');
                            }
                        } else {
                            console.error('Failed to fetch asset details:', response.message);
                          }
                    },
                    error: function(xhr) {
                        console.error('Error fetching asset details:', xhr.responseText);
                    }
                });
            } else {
                // Clear the fields if no asset is selected
                $('#asset_code').val('');
                $('#price').val('');
                $('#depreciation_left_over').val('');
                $('#description').val('');
                $('#tag-container').empty();
                $('#total_left_over_value').val('');
            }
        });
    });
    // Generate transaction number on page load
    function generateTransactionNumber() {
        $.ajax({
            url: '{{ route("master_data.inventory.generate_transaction_number") }}',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    $('#transaction_number').val(response.transaction_number);
                }
            },
            error: function(xhr) {
                console.error('Failed to generate transaction number:', xhr);
            }
        });
    }

    // Auto-generate on page load
    generateTransactionNumber();

    // Refresh button click
    $('#refresh-transaction-number').on('click', function() {
        generateTransactionNumber();
    });

    // Payment terms change - show/hide and calculate
    function handlePaymentTermsChange() {
        const terms = $('#payment_terms').val();
        const daysRow = $('#payment-days-row');
        const daysField = $('#payment_days');
        const dueDateField = $('#due_date');

        if (!terms || terms === 'CASH') {
            // Hide row for CASH or no selection
            daysRow.hide();
            daysField.val('');
            dueDateField.val('');
            return;
        }

        // Show row for NET terms
        daysRow.show();

        // Set days based on payment term
        let days = 0;
        switch(terms) {
            case 'NET_7': days = 7; break;
            case 'NET_15': days = 15; break;
            case 'NET_30': days = 30; break;
            case 'NET_45': days = 45; break;
            case 'NET_60': days = 60; break;
        }

        daysField.val(days);
        updateDueDate();
    }

    function updateDueDate() {
        const transactionDate = $('#transaction_date').val();
        const days = parseInt($('#payment_days').val()) || 0;
        const dueDateField = $('#due_date');

        if (!transactionDate || days === 0) {
            dueDateField.val('');
            return;
        }

        const date = new Date(transactionDate);
        date.setDate(date.getDate() + days);
        dueDateField.val(date.toISOString().split('T')[0]);
    }

    // Payment terms change handler
    $('#payment_terms').on('change', function() {
        handlePaymentTermsChange();
    });

    // Transaction date change - recalculate due date
    $('#transaction_date').on('change', function() {
        updateDueDate();
    });

    // Initialize depreciation Select2 elements (hidden initially)
    $('#depreciation_account, #accumulated_depreciation_account').select2({
        allowClear: false
    });



    // Calculate depreciation when price or rate changes
    function calculateDisposal() {
        const disposal_value = parseFloat($('#disposal_value').val().replace(/,/g, '')) || 0;
        const total_left_over_value = parseFloat($('#total_left_over_value').val()) || 0;

        const release_different_value = disposal_value - total_left_over_value;

        $('#release_different_value').val(release_different_value.toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    }

    $('#disposal_value').on('input', function() {
        calculateDisposal();
    });

    // Form submission
    $('#inventory-purchase-form').on('submit', function(e) {
        e.preventDefault();

        // Show confirmation
        Swal.fire({
            title: 'Buat Transaksi Pembelian Aset?',
            text: 'Pastikan data yang diisi sudah benar.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Buat!',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-label-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                submitForm();
            }
        });
    });

    function submitForm() {
        // Show loading state
        $('#submit-btn').prop('disabled', true).html(
            '<span class="spinner-border spinner-border-sm me-2"></span>Loading...'
        );

        // Collect form data
        const formData = new FormData($('#inventory-purchase-form')[0]);

        // AJAX submission
        $.ajax({
            url: '{{ route("master_data.inventory.dispose_save") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Transaksi pelepasan aset berhasil dibuat.',
                    icon: 'success',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                }).then(() => {
                    window.location.href = '{{ route("master_data.inventory.index") }}';
                });
            },
            error: function(xhr) {
                let errorMessage = 'Terjadi kesalahan saat menyimpan data.';

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
            },
            complete: function() {
                $('#submit-btn').prop('disabled', false).html('Simpan');
            }
        });
    }
});
</script>
@stop
