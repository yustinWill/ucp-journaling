@extends('layouts.default')
@section('page_title')
    Tambah Anggaran
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Tambah Anggaran</h4>
        <form class="card-body" id="budged-form" action="#" method="POST" autocomplete="off">
            @csrf

            <!-- Info Anggaran Section -->
            <div class="section-header mt-12">
                <h6>Info Anggaran</h6>
                <hr class="mx-n6">
            </div>

            <!-- Budget Type Selection -->
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="budget_type" id="new_budget" value="new" checked>
                        <label class="form-check-label" for="new_budget">
                            Buat Anggaran Baru
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="budget_type" id="existing_budget" value="existing">
                        <label class="form-check-label" for="existing_budget">
                            Gunakan Anggaran Yang Ada
                        </label>
                    </div>
                </div>
            </div>

            <!-- New Budget Name Input -->
            <div class="row g-3 mb-4" id="new-budget-section">
                <div class="col-md-6">
                    <label class="form-label">Nama Anggaran <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="budged_name" name="budged_name"
                        placeholder="Masukkan nama anggaran">
                </div>
            </div>

            <!-- Existing Budget Selection -->
            <div class="row g-3 mb-4" id="existing-budget-section" style="display: none;">
                <div class="col-md-6">
                    <label class="form-label">Pilih Anggaran <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="existing_budged_id" name="existing_budged_id" data-allow-clear="false">
                        <option value="">-- Pilih Anggaran --</option>
                        @foreach ($budgedlist as $row)
                            <option value='{{ $row->id; }}'>{{ $row->budged_name; }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Period Information -->
            <div class="section-header mt-12">
                <h6>Informasi Periode</h6>
                <hr class="mx-n6">
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label">Tanggal Mulai Periode <span style="color: red;">*</span></label>
                    <input type="date" class="form-control" id="period_start_date" name="period_start_date" required>
                    <div class="invalid-feedback">
                        Periode bertumpang tindih dengan periode yang sudah ada
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal Akhir Periode <span style="color: red;">*</span></label>
                    <input type="date" class="form-control" id="period_end_date" name="period_end_date" required>
                    <div class="invalid-feedback">
                        Periode bertumpang tindih dengan periode yang sudah ada
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Nilai Limit Anggaran <span style="color: red;">*</span></label>
                    <input type="text" class="form-control currency-input" id="budged_limit_value"
                        name="budged_limit_value" placeholder="Rp 0" required>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 text-end">
                <button type="button" class="btn btn-label-danger me-3" onclick="window.history.back()">Batalkan</button>
                @if($canWrite)
                    <button type="submit" class="btn btn-success" id="submit-btn">Simpan</button>
                @endif
            </div>
        </form>
    </div>
@stop

@section('js_script')
    <script>
        $(function() {
            // Currency formatting
            $('.currency-input').on('input', function() {
                let value = $(this).val().replace(/[^0-9]/g, '');
                if (value) {
                    $(this).val('Rp ' + new Intl.NumberFormat('id-ID').format(value));
                }
            });

            // Parse currency helper
            function parseCurrency(value) {
                return parseFloat(value.replace(/[^0-9]/g, '')) || 0;
            }

            // Initialize Select2
            function initializeSelect2() {
                $('#existing_budged_id').select2({
                    placeholder: '-- Pilih Anggaran --',
                    allowClear: true,
                    width: '100%'
                });
            }

            // Load existing budgets
            function loadExistingBudgets() {
                $.ajax({
                    url: "{{ route('master_data.budged.get-all-budgets') }}",
                    type: 'GET',
                    success: function(response) {
                        // Destroy Select2 if already initialized
                        if ($('#existing_budged_id').hasClass('select2-hidden-accessible')) {
                            $('#existing_budged_id').select2('destroy');
                        }

                        $('#existing_budged_id').empty().append('<option value="">-- Pilih Anggaran --</option>');
                        if (response.success && response.data && response.data.length > 0) {
                            response.data.forEach(function(budget) {
                                $('#existing_budged_id').append(
                                    `<option value="${budget.id}">${budget.budged_name}</option>`
                                );
                            });
                        }

                        // Re-initialize Select2
                        initializeSelect2();
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Gagal memuat daftar anggaran',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        });
                    }
                });
            }

            // Toggle between new and existing budget
            $('input[name="budget_type"]').on('change', function() {
                const budgetType = $(this).val();
                if (budgetType === 'new') {
                    $('#new-budget-section').show();
                    $('#existing-budget-section').hide();
                    $('#budged_name').prop('required', true);
                    $('#existing_budged_id').prop('required', false);
                } else {
                    $('#new-budget-section').hide();
                    $('#existing-budget-section').show();
                    $('#budged_name').prop('required', false);
                    $('#existing_budged_id').prop('required', true);
                    loadExistingBudgets();
                }
            });

            // Validate period dates don't overlap
            async function validatePeriodDates(budgetId, startDate, endDate) {
                try {
                    const response = await $.ajax({
                        url: "{{ url('master_data/budged/validate-period') }}",
                        type: 'POST',
                        data: {
                            budged_id: budgetId,
                            period_start_date: startDate,
                            period_end_date: endDate
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    return response;
                } catch (error) {
                    return { valid: false, message: 'Gagal memvalidasi periode' };
                }
            }

            // Validate period dates when budget or dates change
            let validationTimeout = null;
            $('#existing_budged_id, #period_start_date, #period_end_date').on('change', function() {
                const budgetType = $('input[name="budget_type"]:checked').val();

                // Only validate if using existing budget
                if (budgetType === 'existing') {
                    const budgetId = $('#existing_budged_id').val();
                    const startDate = $('#period_start_date').val();
                    const endDate = $('#period_end_date').val();

                    // Clear previous timeout
                    if (validationTimeout) {
                        clearTimeout(validationTimeout);
                    }

                    // Only validate if all fields are filled
                    if (budgetId && startDate && endDate) {
                        // Add small delay to avoid too many requests
                        validationTimeout = setTimeout(async function() {
                            // First check if start date is before end date
                            if (new Date(startDate) > new Date(endDate)) {
                                $('#period_end_date').addClass('is-invalid');
                                return;
                            }

                            // Validate period overlap
                            const validation = await validatePeriodDates(budgetId, startDate, endDate);

                            if (!validation.valid) {
                                // Show error styling
                                $('#period_start_date').addClass('is-invalid');
                                $('#period_end_date').addClass('is-invalid');

                                // Show error message
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Periode Tidak Valid',
                                    text: validation.message || 'Periode yang dipilih bertumpang tindih dengan periode yang sudah ada',
                                    confirmButtonText: 'OK',
                                    customClass: {
                                        confirmButton: 'btn btn-primary'
                                    },
                                    buttonsStyling: false
                                });
                            } else {
                                // Remove error styling if valid
                                $('#period_start_date').removeClass('is-invalid');
                                $('#period_end_date').removeClass('is-invalid');
                            }
                        }, 500);
                    }
                }
            });

            // Form validation and submission
            $('#budged-form').on('submit', async function(e) {
                e.preventDefault();

                const budgetType = $('input[name="budget_type"]:checked').val();
                const periodStartDate = $('#period_start_date').val();
                const periodEndDate = $('#period_end_date').val();

                // Validate dates
                if (new Date(periodStartDate) > new Date(periodEndDate)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Tanggal mulai periode tidak boleh lebih besar dari tanggal akhir periode',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    return;
                }

                let formData = {
                    budget_type: budgetType,
                    period_start_date: periodStartDate,
                    period_end_date: periodEndDate,
                    budged_limit_value: parseCurrency($('#budged_limit_value').val())
                };

                if (budgetType === 'new') {
                    formData.budged_name = $('#budged_name').val();
                    if (!formData.budged_name) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Silakan masukkan nama anggaran',
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        });
                        return;
                    }
                } else {
                    const existingBudgetId = $('#existing_budged_id').val();
                    if (!existingBudgetId) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Silakan pilih anggaran yang ada',
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        });
                        return;
                    }

                    // Validate period doesn't overlap
                    const validation = await validatePeriodDates(existingBudgetId, periodStartDate, periodEndDate);
                    if (!validation.valid) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Periode Tidak Valid!',
                            text: validation.message || 'Periode yang dipilih bertumpang tindih dengan periode yang sudah ada',
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        });
                        return;
                    }

                    formData.existing_budged_id = existingBudgetId;
                }

                // Confirm save
                Swal.fire({
                    title: 'Simpan Anggaran?',
                    text: 'Apakah Anda yakin ingin membuat anggaran ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-secondary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.value) {
                        $('#submit-btn').prop('disabled', true).html(
                            '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Loading...'
                        );

                        $.ajax({
                            url: "{{ route('master_data.budged.save') }}",
                            type: 'POST',
                            data: formData,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Anggaran berhasil dibuat.',
                                    customClass: {
                                        confirmButton: 'btn btn-success'
                                    },
                                    buttonsStyling: false
                                }).then(function() {
                                    window.location.href = "{{ route('master_data.budged.index') }}";
                                });
                            },
                            error: function(xhr) {
                                const res = xhr.responseJSON;
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal menyimpan',
                                    html: res?.message || 'Terjadi kesalahan saat menyimpan data.',
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
            });

            // Real-time validation
            $('input, select, textarea').on('change blur', function() {
                if (this.hasAttribute('required') && !this.value.trim()) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });
        });
    </script>
@endsection
