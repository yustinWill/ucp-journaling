@extends('layouts.default')
@section('page_title')
    Tambah Anggaran
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Tambah Anggaran</h4>
        <form class="card-body" id="budged-form" action="#" method="POST" autocomplete="off">
            @csrf

            <!-- Period Information -->
            <div class="section-header mt-12">
                <h6>Informasi Periode</h6>
                <hr class="mx-n6">
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label">Tanggal Mulai Periode <span style="color: red;">*</span></label>
                    <input type="text" class="form-control global-single-date-picker" id="period_start_date"
                           name="period_start_date" placeholder="yyyy-mm-dd" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal Akhir Periode <span style="color: red;">*</span></label>
                    <input type="text" class="form-control global-single-date-picker" id="period_end_date"
                           name="period_end_date" placeholder="yyyy-mm-dd" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tipe <span style="color: red;">*</span></label>
                    <select class="form-select" id="type" name="type" required>
                        <option value="">Pilih Tipe</option>
                        <option value="kingdom">Kingdom</option>
                        <option value="owner">Owner</option>
                    </select>
                </div>
            </div>

            <!-- Budget Items Section -->
            <div class="section-header mt-12">
                <h6>Daftar Anggaran</h6>
                <hr class="mx-n6">
            </div>

            <div id="budget-items-container">
                <!-- Budget items will be added here -->
            </div>

            <div class="mb-4">
                <button type="button" class="btn btn-primary" id="add-budget-item">
                    <i class="icon-base ti tabler-plus me-2"></i> Tambah Anggaran
                </button>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 text-end">
                <button type="button" class="btn btn-label-danger me-3" onclick="window.history.back()">Batalkan</button>
                <button type="submit" class="btn btn-success" id="submit-btn">Simpan</button>
            </div>
        </form>
    </div>
@stop

@section('js_script')
    <script>
        $(function() {
            let budgetItemIndex = 0;

            // Duplicate data from server
            const duplicateData = @json($duplicateData);

            // Budget item template
            function getBudgetItemTemplate(index, categoryId = '', limitValue = '') {
                let categoryOptions = '<option value="">Pilih Kategori</option>';
                @foreach($budgetCategories as $category)
                    categoryOptions += `<option value="{{ $category->id }}" ${categoryId == '{{ $category->id }}' ? 'selected' : ''}>{{ $category->category_name }}</option>`;
                @endforeach

                return `
                    <div class="card mb-3 budget-item" data-index="${index}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Anggaran #${index + 1}</h6>
                                <button type="button" class="btn btn-sm btn-danger remove-budget-item" data-index="${index}">
                                    <i class="ti ti-trash"></i> Hapus
                                </button>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Kategori Anggaran <span style="color: red;">*</span></label>
                                    <select class="form-select budget-category-select" name="budgets[${index}][f_budget_category_id]" id="category_${index}" required>
                                        ${categoryOptions}
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nilai Limit Anggaran <span style="color: red;">*</span></label>
                                    <input type="text" class="form-control currency-input" name="budgets[${index}][budged_limit_value]"
                                        placeholder="Rp 0" value="${limitValue}" required>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }

            // Function to update category dropdowns (disable selected options)
            function updateCategoryOptions() {
                const selectedCategories = [];

                // Collect all selected category IDs
                $('.budget-category-select').each(function() {
                    const value = $(this).val();
                    if (value) {
                        selectedCategories.push(value);
                    }
                });

                // Update each dropdown
                $('.budget-category-select').each(function() {
                    const currentSelect = $(this);
                    const currentValue = currentSelect.val();

                    // Enable all options first
                    currentSelect.find('option').prop('disabled', false);

                    // Disable options that are selected in other dropdowns
                    selectedCategories.forEach(function(categoryId) {
                        if (categoryId !== currentValue) {
                            currentSelect.find(`option[value="${categoryId}"]`).prop('disabled', true);
                        }
                    });

                    // Refresh select2 to show disabled state
                    if (currentSelect.hasClass('select2-hidden-accessible')) {
                        currentSelect.select2('destroy');
                        currentSelect.select2({
                            placeholder: 'Pilih Kategori',
                            allowClear: true
                        });
                    }
                });
            }

            // Add budget item
            function addBudgetItem() {
                const template = getBudgetItemTemplate(budgetItemIndex);
                $('#budget-items-container').append(template);

                // Initialize currency formatting for new item
                initializeCurrencyInput($(`[name="budgets[${budgetItemIndex}][budged_limit_value]"]`));

                // Initialize select2 for the new dropdown
                $(`#category_${budgetItemIndex}`).select2({
                    placeholder: 'Pilih Kategori',
                    allowClear: true
                });

                budgetItemIndex++;
                updateBudgetNumbers();
                updateCategoryOptions();
            }

            // Remove budget item
            $(document).on('click', '.remove-budget-item', function() {
                const index = $(this).data('index');
                $(`.budget-item[data-index="${index}"]`).remove();
                updateBudgetNumbers();
                updateCategoryOptions();
            });

            // Update budget numbers
            function updateBudgetNumbers() {
                $('.budget-item').each(function(i) {
                    $(this).find('h6').text(`Anggaran #${i + 1}`);
                });
            }

            // Handle category selection change
            $(document).on('change', '.budget-category-select', function() {
                updateCategoryOptions();
            });

            // Add budget item button
            $('#add-budget-item').on('click', function() {
                addBudgetItem();
            });

            // Initialize: Load duplicate data or start with empty form
            if (duplicateData && duplicateData.period && duplicateData.budgets) {
                // Pre-fill period info (but don't set dates - let user choose new period)
                $('#type').val(duplicateData.period.type);

                // Add budget items from duplicate
                duplicateData.budgets.forEach(function(budget) {
                    const limitValue = 'Rp ' + new Intl.NumberFormat('id-ID').format(budget.budged_limit_value);
                    const template = getBudgetItemTemplate(budgetItemIndex, budget.f_budget_category_id, limitValue);
                    $('#budget-items-container').append(template);

                    // Initialize currency formatting for new item
                    initializeCurrencyInput($(`[name="budgets[${budgetItemIndex}][budged_limit_value]"]`));

                    // Initialize select2 for the new dropdown
                    $(`#category_${budgetItemIndex}`).select2({
                        placeholder: 'Pilih Kategori',
                        allowClear: true
                    });

                    budgetItemIndex++;
                });

                updateBudgetNumbers();
                updateCategoryOptions();
            } else {
                // Initialize with one budget item
                addBudgetItem();
            }

            // Currency formatting
            function initializeCurrencyInput($element) {
                $element.on('input', function() {
                    let value = $(this).val().replace(/[^0-9]/g, '');
                    if (value) {
                        $(this).val('Rp ' + new Intl.NumberFormat('id-ID').format(value));
                    }
                });
            }

            // Initialize currency inputs for all existing items
            $('.currency-input').each(function() {
                initializeCurrencyInput($(this));
            });

            // Parse currency helper
            function parseCurrency(value) {
                if (!value) return 0;
                return parseFloat(value.toString().replace(/[^0-9]/g, '')) || 0;
            }

            // Format date to yyyy-mm-dd
            function formatDateToYMD(dateString) {
                if (!dateString) return '';

                // If already in yyyy-mm-dd format, return as is
                if (/^\d{4}-\d{2}-\d{2}$/.test(dateString)) {
                    return dateString;
                }

                // Parse and format to yyyy-mm-dd
                const date = new Date(dateString);
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }

            // Form validation and submission
            $('#budged-form').on('submit', async function(e) {
                e.preventDefault();

                // Get and format dates to yyyy-mm-dd
                const periodStartDate = formatDateToYMD($('#period_start_date').val());
                const periodEndDate = formatDateToYMD($('#period_end_date').val());

                // Validate date format
                if (!periodStartDate || !periodEndDate) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Silakan isi tanggal periode dengan benar (format: yyyy-mm-dd)',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    return;
                }

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

                // Validate at least one budget item
                if ($('.budget-item').length === 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Silakan tambahkan minimal satu anggaran',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    return;
                }

                // Collect budget items
                const budgets = [];
                let hasError = false;

                $('.budget-item').each(function() {
                    const index = $(this).data('index');
                    const categoryId = $(`[name="budgets[${index}][f_budget_category_id]"]`).val();
                    const limitValue = parseCurrency($(`[name="budgets[${index}][budged_limit_value]"]`).val());

                    if (!categoryId || limitValue === null || limitValue === '') {
                        hasError = true;
                        return false;
                    }

                    budgets.push({
                        f_budget_category_id: categoryId,
                        budged_limit_value: limitValue
                    });
                });

                if (hasError) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Silakan lengkapi semua data anggaran',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    return;
                }

                const formData = {
                    period_start_date: periodStartDate,
                    period_end_date: periodEndDate,
                    type: $('#type').val(),
                    budgets: budgets
                };

                // Debug: Log the formatted dates
                console.log('Formatted dates:', {
                    period_start_date: periodStartDate,
                    period_end_date: periodEndDate
                });

                // Confirm save
                Swal.fire({
                    title: 'Simpan Anggaran?',
                    text: `Apakah Anda yakin ingin membuat ${budgets.length} anggaran untuk periode ini?`,
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
            $(document).on('change blur', 'input[required], select[required], textarea[required]', function() {
                if (!this.value.trim()) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });
        });
    </script>
@endsection
