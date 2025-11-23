@extends('layouts.default')
@section('page_title')
    Edit Anggaran
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Edit Anggaran</h4>
        <form class="card-body" id="budged-form" action="#" method="POST" autocomplete="off">
            <input type="hidden" id="budged-id" value="{{ $budged->id }}">
            @csrf
            @method('POST')

            <!-- Info Anggaran Section -->
            <div class="section-header mt-12">
                <h6>Informasi Anggaran</h6>
                <hr class="mx-n6">
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Nama Anggaran <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="budged-name" name="budged_name"
                        value="{{ $budged->budged_name }}" placeholder="Masukkan nama anggaran" required />
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 text-end">
                <a href="{{ route('master_data.budged.detail', $budged->id) }}" class="btn btn-label-danger me-3">Batalkan</a>
                @if($canWrite)
                    <button type="submit" class="btn btn-success" id="submit-btn">Simpan</button>
                @endif
            </div>
        </form>
    </div>

    <!-- Budget Periods -->
    <div class="card mb-6">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <h5 class="card-title mb-0 flex-grow-1">Daftar Periode Anggaran</h5>
                @if($canWrite)
                    <div>
                        <button type="button" class="btn btn-primary btn-md waves-effect waves-light" data-bs-toggle="modal"
                            data-bs-target="#addPeriodeModal">
                            <span class="icon-xs icon-base ti tabler-plus me-2"></span> Tambah Periode
                        </button>
                    </div>
                @endif
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Akhir</th>
                            <th>Nilai Limit</th>
                            <th>Nilai Sisa</th>
                            <th>Nilai Terpakai</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="periode-table-body">
                        <tr>
                            <td colspan="7" class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Periode Modal -->
    <div class="modal fade" id="addPeriodeModal" tabindex="-1" aria-labelledby="addPeriodeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPeriodeModalLabel">Tambah Periode Anggaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="add-periode-form">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="add_period_start_date" class="form-label">Tanggal Mulai Periode <span
                                    class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="add_period_start_date" name="period_start_date" required>
                            <div class="invalid-feedback">
                                Periode bertumpang tindih dengan periode yang sudah ada
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="add_period_end_date" class="form-label">Tanggal Akhir Periode <span
                                    class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="add_period_end_date" name="period_end_date" required>
                            <div class="invalid-feedback">
                                Periode bertumpang tindih dengan periode yang sudah ada
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="add_budged_limit_value" class="form-label">Nilai Limit Anggaran <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control currency-input" id="add_budged_limit_value"
                                name="budged_limit_value" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-danger" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success" id="btn-add-periode">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Periode Modal -->
    <div class="modal fade" id="editPeriodeModal" tabindex="-1" aria-labelledby="editPeriodeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPeriodeModalLabel">Edit Periode Anggaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="edit-periode-form">
                    <input type="hidden" id="edit_history_id" name="history_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_period_start_date" class="form-label">Tanggal Mulai Periode <span
                                    class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="edit_period_start_date" name="period_start_date" required>
                            <div class="invalid-feedback">
                                Periode bertumpang tindih dengan periode yang sudah ada
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_period_end_date" class="form-label">Tanggal Akhir Periode <span
                                    class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="edit_period_end_date" name="period_end_date" required>
                            <div class="invalid-feedback">
                                Periode bertumpang tindih dengan periode yang sudah ada
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_budged_limit_value" class="form-label">Nilai Limit Anggaran <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control currency-input" id="edit_budged_limit_value"
                                name="budged_limit_value" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_budged_remaining_value" class="form-label">Nilai Sisa Anggaran <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control currency-input" id="edit_budged_remaining_value"
                                name="budged_remaining_value" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-danger" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success" id="btn-edit-periode">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js_script')
    <script>
        $(function() {
            const budgedId = {{ $budged->id }};

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

            // Load periods
            function loadPeriods() {
                $.ajax({
                    url: "{{ url('master_data/budged/get-periods') }}/" + budgedId,
                    type: 'GET',
                    success: function(response) {
                        if (response.success && response.data) {
                            renderPeriods(response.data);
                        }
                    },
                    error: function() {
                        $('#periode-table-body').html('<tr><td colspan="7" class="text-center text-danger">Gagal memuat data periode</td></tr>');
                    }
                });
            }

            // Render periods table
            function renderPeriods(periods) {
                if (periods.length === 0) {
                    $('#periode-table-body').html('<tr><td colspan="7" class="text-center">Belum ada periode anggaran</td></tr>');
                    return;
                }

                let html = '';
                periods.forEach(function(item, index) {
                    const startDate = moment(item.period_start_date).format('DD/MM/YYYY');
                    const endDate = moment(item.period_end_date).format('DD/MM/YYYY');
                    const limit = 'Rp ' + new Intl.NumberFormat('id-ID').format(item.budged_limit_value);
                    const remaining = 'Rp ' + new Intl.NumberFormat('id-ID').format(item.budged_remaining_value);
                    const used = 'Rp ' + new Intl.NumberFormat('id-ID').format(item.budged_limit_value - item.budged_remaining_value);

                    html += `<tr>
                        <td>${index + 1}</td>
                        <td>${startDate}</td>
                        <td>${endDate}</td>
                        <td class="text-end">${limit}</td>
                        <td class="text-end">${remaining}</td>
                        <td class="text-end">${used}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-primary waves-effect waves-light btn-edit-periode"
                                data-id="${item.id}"
                                data-start-date="${item.period_start_date}"
                                data-end-date="${item.period_end_date}"
                                data-limit="${item.budged_limit_value}"
                                data-remaining="${item.budged_remaining_value}">
                                <i class="icon-xs icon-base ti tabler-edit"></i>
                            </button>
                        </td>
                    </tr>`;
                });

                $('#periode-table-body').html(html);
            }

            // Validate period overlap
            async function validatePeriodDates(budgetId, startDate, endDate, excludeHistoryId = null) {
                try {
                    const response = await $.ajax({
                        url: "{{ url('master_data/budged/validate-period') }}",
                        type: 'POST',
                        data: {
                            budged_id: budgetId,
                            period_start_date: startDate,
                            period_end_date: endDate,
                            exclude_history_id: excludeHistoryId
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

            // Update budget name
            $('#budged-form').on('submit', function(e) {
                e.preventDefault();

                // Validate required fields
                let isValid = true;
                const requiredFields = ['budged-name'];

                requiredFields.forEach(function(fieldId) {
                    const field = document.getElementById(fieldId);
                    if (!field.value.trim()) {
                        isValid = false;
                        field.classList.add('is-invalid');
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });

                if (!isValid) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Kesalahan Validasi',
                        text: 'Silakan lengkapi semua field yang wajib diisi.',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    return;
                }

                // Confirm save
                Swal.fire({
                    title: 'Perbarui Anggaran?',
                    text: 'Apakah Anda yakin ingin menyimpan perubahan ini?',
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
                            url: "{{ route('master_data.budged.update') }}",
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                id: $('#budged-id').val(),
                                budged_name: $('#budged-name').val(),
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Anggaran berhasil diperbarui.',
                                    customClass: {
                                        confirmButton: 'btn btn-success'
                                    },
                                    buttonsStyling: false
                                }).then(function() {
                                    window.location.href =
                                        "{{ route('master_data.budged.detail', $budged->id) }}";
                                });
                            },
                            error: function(xhr) {
                                const res = xhr.responseJSON;
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal menyimpan',
                                    html: res?.message ||
                                        'Terjadi kesalahan saat menyimpan data.',
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

            // Add Periode
            $('#add-periode-form').on('submit', async function(e) {
                e.preventDefault();

                const startDate = $('#add_period_start_date').val();
                const endDate = $('#add_period_end_date').val();

                // Validate dates
                if (new Date(startDate) > new Date(endDate)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Tanggal mulai periode tidak boleh lebih besar dari tanggal akhir periode',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    return;
                }

                // Validate overlap
                const validation = await validatePeriodDates(budgedId, startDate, endDate);
                if (!validation.valid) {
                    $('#add_period_start_date, #add_period_end_date').addClass('is-invalid');
                    Swal.fire({
                        icon: 'error',
                        title: 'Periode Tidak Valid!',
                        text: validation.message,
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    return;
                }

                const submitBtn = $('#btn-add-periode');
                const originalText = submitBtn.html();

                submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Loading...');

                const formData = {
                    budged_id: budgedId,
                    period_start_date: startDate,
                    period_end_date: endDate,
                    budged_limit_value: parseCurrency($('#add_budged_limit_value').val())
                };

                $.ajax({
                    url: "{{ route('master_data.budged.add-periode') }}",
                    type: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            customClass: {
                                confirmButton: 'btn btn-success'
                            },
                            buttonsStyling: false
                        });
                        $('#addPeriodeModal').modal('hide');
                        $('#add-periode-form')[0].reset();
                        loadPeriods();
                    },
                    error: function(xhr) {
                        const res = xhr.responseJSON;
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: res?.message || 'Terjadi kesalahan',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        });
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).html(originalText);
                    }
                });
            });

            // Edit Periode
            $(document).on('click', '.btn-edit-periode', function() {
                const historyId = $(this).data('id');
                const startDate = $(this).data('start-date');
                const endDate = $(this).data('end-date');
                const limit = $(this).data('limit');
                const remaining = $(this).data('remaining');

                $('#edit_history_id').val(historyId);
                $('#edit_period_start_date').val(startDate);
                $('#edit_period_end_date').val(endDate);
                $('#edit_budged_limit_value').val('Rp ' + new Intl.NumberFormat('id-ID').format(limit));
                $('#edit_budged_remaining_value').val('Rp ' + new Intl.NumberFormat('id-ID').format(remaining));

                $('#editPeriodeModal').modal('show');
            });

            $('#edit-periode-form').on('submit', async function(e) {
                e.preventDefault();

                const historyId = $('#edit_history_id').val();
                const startDate = $('#edit_period_start_date').val();
                const endDate = $('#edit_period_end_date').val();

                // Validate dates
                if (new Date(startDate) > new Date(endDate)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Tanggal mulai periode tidak boleh lebih besar dari tanggal akhir periode',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    return;
                }

                // Validate overlap (excluding current period)
                const validation = await validatePeriodDates(budgedId, startDate, endDate, historyId);
                if (!validation.valid) {
                    $('#edit_period_start_date, #edit_period_end_date').addClass('is-invalid');
                    Swal.fire({
                        icon: 'error',
                        title: 'Periode Tidak Valid!',
                        text: validation.message,
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    return;
                }

                const submitBtn = $('#btn-edit-periode');
                const originalText = submitBtn.html();

                submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Loading...');

                const formData = {
                    history_id: historyId,
                    period_start_date: startDate,
                    period_end_date: endDate,
                    budged_limit_value: parseCurrency($('#edit_budged_limit_value').val()),
                    budged_remaining_value: parseCurrency($('#edit_budged_remaining_value').val())
                };

                $.ajax({
                    url: "{{ route('master_data.budged.update-periode') }}",
                    type: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            customClass: {
                                confirmButton: 'btn btn-success'
                            },
                            buttonsStyling: false
                        });
                        $('#editPeriodeModal').modal('hide');
                        loadPeriods();
                    },
                    error: function(xhr) {
                        const res = xhr.responseJSON;
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: res?.message || 'Terjadi kesalahan',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        });
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).html(originalText);
                    }
                });
            });

            // Clear validation errors on input
            $('#add_period_start_date, #add_period_end_date').on('change', function() {
                $(this).removeClass('is-invalid');
            });

            $('#edit_period_start_date, #edit_period_end_date').on('change', function() {
                $(this).removeClass('is-invalid');
            });

            // Reset form when modal is closed
            $('#addPeriodeModal').on('hidden.bs.modal', function() {
                $('#add-periode-form')[0].reset();
                $('#add_period_start_date, #add_period_end_date').removeClass('is-invalid');
            });

            $('#editPeriodeModal').on('hidden.bs.modal', function() {
                $('#edit_period_start_date, #edit_period_end_date').removeClass('is-invalid');
            });

            // Real-time validation
            $('input, select, textarea').on('change blur', function() {
                if (this.hasAttribute('required') && !this.value.trim()) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });

            // Load periods on page load
            loadPeriods();
        });
    </script>
@endsection
