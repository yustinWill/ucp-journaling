@extends('layouts.default')
@section('page_title')
    Edit Periode Anggaran
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Edit Periode Anggaran</h4>
        <form class="card-body" id="budged-form" action="#" method="POST" autocomplete="off">
            <input type="hidden" id="period-id" value="{{ $period_id }}">
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
                           name="period_start_date" value="{{ $period_start_date }}" placeholder="yyyy-mm-dd" required readonly>
                    <small class="text-muted">Periode tidak dapat diubah</small>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal Akhir Periode <span style="color: red;">*</span></label>
                    <input type="text" class="form-control global-single-date-picker" id="period_end_date"
                           name="period_end_date" value="{{ $period_end_date }}" placeholder="yyyy-mm-dd" required readonly>
                    <small class="text-muted">Periode tidak dapat diubah</small>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tipe</label>
                    <input type="text" class="form-control" value="{{ ucfirst($period_type ?? 'kingdom') }}" readonly>
                    <small class="text-muted">Tipe tidak dapat diubah</small>
                </div>
            </div>

            <!-- Budget Items Section -->
            <div class="section-header mt-12">
                <h6>Daftar Anggaran dalam Periode Ini</h6>
                <hr class="mx-n6">
            </div>

            <div id="budget-items-container" class="mb-4">
                <!-- Budget items will be loaded here -->
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>

            @if($canWrite)
                <div class="mb-4">
                    <button type="button" class="btn btn-secondary" id="add-budget-item">
                        <i class="icon-base ti tabler-plus me-2"></i> Tambah Anggaran
                    </button>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="pt-6 text-end">
                <a href="{{ route('master_data.budged.index') }}" class="btn btn-label-danger me-3">Batalkan</a>
                @if($canWrite)
                    <button type="submit" class="btn btn-success" id="submit-btn">Simpan Perubahan</button>
                @endif
            </div>
        </form>
    </div>
@stop

@section('js_script')
    <script>
        $(function() {
            const periodId = {{ $period_id }};
            let budgetIndex = 0;
            let existingBudgets = [];

            // Currency formatting
            function initializeCurrencyInput(element) {
                element.on('input', function() {
                    let value = $(this).val().replace(/[^0-9]/g, '');
                    if (value) {
                        $(this).val('Rp ' + new Intl.NumberFormat('id-ID').format(value));
                    } else {
                        $(this).val('');
                    }
                });
            }

            function parseCurrency(value) {
                if (!value) return 0;
                return parseFloat(value.toString().replace(/[^0-9]/g, '')) || 0;
            }

            // Get budget item template
            function getBudgetItemTemplate(index, data = {}) {
                const isExisting = data.id ? true : false;
                const budgetId = data.id || '';
                const categoryId = data.f_budget_category_id || '';
                const limitValue = data.budged_limit_value ? 'Rp ' + new Intl.NumberFormat('id-ID').format(data.budged_limit_value) : '';
                const remainingValue = data.budged_remaining_value ? 'Rp ' + new Intl.NumberFormat('id-ID').format(data.budged_remaining_value) : '';

                return `
                    <div class="card mb-3 budget-item" data-index="${index}" data-id="${budgetId}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Anggaran #${index + 1}</h6>
                                ${!isExisting ? `<button type="button" class="btn btn-sm btn-danger remove-budget-item" data-index="${index}">
                                    <i class="ti ti-trash"></i> Hapus
                                </button>` : ''}
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Kategori Anggaran <span style="color: red;">*</span></label>
                                    <select class="form-select budget-category-select" name="budgets[${index}][f_budget_category_id]" id="category_${index}" required ${isExisting ? 'disabled' : ''}>
                                        <option value="">Pilih Kategori</option>
                                        @foreach($budgetCategories as $category)
                                            <option value="{{ $category->id }}" ${categoryId == '{{ $category->id }}' ? 'selected' : ''}>{{ $category->category_name }}</option>
                                        @endforeach
                                    </select>
                                    ${isExisting ? `<small class="text-muted">Kategori tidak dapat diubah untuk anggaran yang sudah ada</small>` : ''}
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nilai Limit Anggaran <span style="color: red;">*</span></label>
                                    <input type="text" class="form-control currency-input" name="budgets[${index}][budged_limit_value]"
                                        value="${limitValue}" placeholder="Rp 0" required>
                                </div>
                            </div>
                            ${isExisting ? `<input type="hidden" name="budgets[${index}][id]" value="${budgetId}">
                            <input type="hidden" name="budgets[${index}][f_budget_category_id]" value="${categoryId}">
                            <input type="hidden" name="budgets[${index}][budged_remaining_value]" value="${data.budged_remaining_value || 0}">` : ''}
                        </div>
                    </div>
                `;
            }

            // Load existing budgets for this period
            function loadBudgets() {
                $.ajax({
                    url: "{{ url('master_data/budged/get-budgets-by-period') }}/" + periodId,
                    type: 'GET',
                    success: function(response) {
                        if (response.success && response.data) {
                            existingBudgets = response.data;
                            renderBudgets();
                        }
                    },
                    error: function() {
                        $('#budget-items-container').html('<div class="alert alert-danger">Gagal memuat data anggaran</div>');
                    }
                });
            }

            // Function to update category dropdowns (disable selected options)
            function updateCategoryOptions() {
                const selectedCategories = [];

                // Collect all selected category IDs (including from existing budgets via hidden input)
                $('.budget-item').each(function() {
                    const select = $(this).find('.budget-category-select');
                    if (select.is(':disabled')) {
                        // For existing budgets, get from hidden input
                        const hiddenInput = $(this).find('input[type="hidden"][name$="[f_budget_category_id]"]');
                        if (hiddenInput.length && hiddenInput.val()) {
                            selectedCategories.push(hiddenInput.val());
                        }
                    } else {
                        // For new budgets, get from select
                        const value = select.val();
                        if (value) {
                            selectedCategories.push(value);
                        }
                    }
                });

                // Update each enabled dropdown
                $('.budget-category-select:not(:disabled)').each(function() {
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

                    // Refresh select2 if it's initialized
                    if (currentSelect.hasClass('select2-hidden-accessible')) {
                        currentSelect.select2('destroy');
                        currentSelect.select2({
                            placeholder: 'Pilih Kategori',
                            allowClear: true
                        });
                    }
                });
            }

            // Render budgets
            function renderBudgets() {
                let html = '';
                budgetIndex = 0;

                existingBudgets.forEach(function(budget) {
                    html += getBudgetItemTemplate(budgetIndex, budget);
                    budgetIndex++;
                });

                if (html === '') {
                    html = '<div class="alert alert-info">Belum ada anggaran dalam periode ini</div>';
                }

                $('#budget-items-container').html(html);

                // Initialize currency inputs
                $('.currency-input').each(function() {
                    initializeCurrencyInput($(this));
                });

                // Initialize select2 for category dropdowns
                $('.budget-category-select:not(:disabled)').each(function() {
                    $(this).select2({
                        placeholder: 'Pilih Kategori',
                        allowClear: true
                    });
                });

                // Update category options to disable already selected
                updateCategoryOptions();
            }

            // Add budget item
            $('#add-budget-item').on('click', function() {
                const newItem = getBudgetItemTemplate(budgetIndex);
                $('#budget-items-container').append(newItem);

                // Initialize currency input for new item
                $(`[name="budgets[${budgetIndex}][budged_limit_value]"]`).each(function() {
                    initializeCurrencyInput($(this));
                });

                // Initialize select2 for new dropdown
                $(`#category_${budgetIndex}`).select2({
                    placeholder: 'Pilih Kategori',
                    allowClear: true
                });

                budgetIndex++;
                updateCategoryOptions();
            });

            // Remove budget item
            $(document).on('click', '.remove-budget-item', function() {
                const index = $(this).data('index');
                $(`.budget-item[data-index="${index}"]`).remove();
                updateCategoryOptions();
            });

            // Handle category selection change
            $(document).on('change', '.budget-category-select', function() {
                updateCategoryOptions();
            });

            // Form submission
            $('#budged-form').on('submit', async function(e) {
                e.preventDefault();

                // Collect budget items
                const budgets = [];
                let hasError = false;

                $('.budget-item').each(function() {
                    const index = $(this).data('index');
                    const id = $(this).data('id');

                    // Get category ID (from select or hidden input for existing budgets)
                    let categoryId;
                    const select = $(this).find(`.budget-category-select`);
                    if (select.is(':disabled')) {
                        // For existing budgets, get from hidden input
                        categoryId = $(this).find(`input[type="hidden"][name="budgets[${index}][f_budget_category_id]"]`).val();
                    } else {
                        // For new budgets, get from select
                        categoryId = select.val();
                    }

                    const limitValue = parseCurrency($(`[name="budgets[${index}][budged_limit_value]"]`).val());

                    // Get remaining value from hidden input if it exists, otherwise use limit value
                    const remainingValueInput = $(`input[type="hidden"][name="budgets[${index}][budged_remaining_value]"]`);
                    const remainingValue = remainingValueInput.length > 0
                        ? parseFloat(remainingValueInput.val())
                        : limitValue;

                    if (!categoryId || !limitValue) {
                        hasError = true;
                        return false;
                    }

                    budgets.push({
                        id: id || null,
                        f_budget_category_id: categoryId,
                        budged_limit_value: limitValue,
                        budged_remaining_value: remainingValue
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

                if (budgets.length === 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Minimal harus ada satu anggaran',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    return;
                }

                // Confirm save
                Swal.fire({
                    title: 'Simpan Perubahan?',
                    text: `Apakah Anda yakin ingin menyimpan perubahan untuk ${budgets.length} anggaran?`,
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
                            '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Menyimpan...'
                        );

                        $.ajax({
                            url: "{{ route('master_data.budged.update-period-budgets') }}",
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                period_id: periodId,
                                budgets: budgets
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.message || 'Perubahan berhasil disimpan',
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
                                $('#submit-btn').prop('disabled', false).html('Simpan Perubahan');
                            }
                        });
                    }
                });
            });

            // Load budgets on page load
            loadBudgets();
        });
    </script>
@endsection
