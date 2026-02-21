@extends('layouts.default')
@section('page_title')
    Reimbursement Approval
@stop
@section('content')
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar me-3">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="icon-base ti tabler-clock icon-24px"></i>
                            </span>
                        </div>
                        <div>
                            <small class="text-muted d-block">Pending Approval</small>
                            <h4 class="mb-0">{{ $stats['total_pending'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar me-3">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="icon-base ti tabler-check icon-24px"></i>
                            </span>
                        </div>
                        <div>
                            <small class="text-muted d-block">Approved</small>
                            <h4 class="mb-0">{{ $stats['total_approved'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar me-3">
                            <span class="avatar-initial rounded bg-label-danger">
                                <i class="icon-base ti tabler-x icon-24px"></i>
                            </span>
                        </div>
                        <div>
                            <small class="text-muted d-block">Rejected</small>
                            <h4 class="mb-0">{{ $stats['total_rejected'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar me-3">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="icon-base ti tabler-currency-dollar icon-24px"></i>
                            </span>
                        </div>
                        <div>
                            <small class="text-muted d-block">Total Approved</small>
                            <h5 class="mb-0">Rp {{ number_format($stats['total_amount_approved'], 0, ',', '.') }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="card">
        <h4 class="card-header">Reimbursement Approval</h4>

        <!-- Data Table -->
        <div class="card-datatable">
            <table id="reimbursement-approval-table" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Tanggal</th>
                        <th>Staff</th>
                        <th>Nama Reimbursement</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Budget Category</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- Detail / Approval Modal -->
    <div class="modal fade" id="reimbursementModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Reimbursement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Loading -->
                    <div id="modal-loading" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2 text-muted">Memuat data...</p>
                    </div>

                    <!-- Content -->
                    <div id="modal-content" style="display: none;">
                        <!-- Info Section -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Kode Transaksi</label>
                                <input type="text" class="form-control" id="modal-code" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tanggal Pengajuan</label>
                                <input type="text" class="form-control" id="modal-date" disabled>
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Diajukan Oleh</label>
                                <input type="text" class="form-control" id="modal-creator" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Jumlah</label>
                                <input type="text" class="form-control" id="modal-amount" disabled>
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Nama Reimbursement</label>
                                <input type="text" class="form-control" id="modal-name" disabled>
                            </div>
                        </div>
                        <div class="row g-3 mb-3" id="modal-description-row" style="display: none;">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Deskripsi</label>
                                <textarea class="form-control" id="modal-description" rows="2" disabled></textarea>
                            </div>
                        </div>
                        <div class="row g-3 mb-3" id="modal-attachment-row" style="display: none;">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Lampiran</label>
                                <div>
                                    <a href="#" id="modal-attachment-link" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="icon-base ti tabler-paperclip me-1"></i> Lihat Lampiran
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Review Info (for non-PENDING) -->
                        <div id="modal-review-section" style="display: none;">
                            <hr class="my-3">
                            <h6 class="mb-3">Informasi Review</h6>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Direview Oleh</label>
                                    <input type="text" class="form-control" id="modal-reviewer" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Tanggal Review</label>
                                    <input type="text" class="form-control" id="modal-reviewed-at" disabled>
                                </div>
                            </div>
                            <div class="row g-3 mb-3" id="modal-budget-row" style="display: none;">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Anggaran</label>
                                    <input type="text" class="form-control" id="modal-budget-name" disabled>
                                </div>
                            </div>
                            <div class="row g-3 mb-3" id="modal-rejection-row" style="display: none;">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Alasan Penolakan</label>
                                    <textarea class="form-control" id="modal-rejection-reason" rows="2" disabled></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Approval Action (for PENDING only) -->
                        <div id="modal-approval-section" style="display: none;">
                            <hr class="my-3">
                            <h6 class="mb-3">Approval Action</h6>
                            <div class="row g-3 mb-3">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Entity Type <span class="text-danger">*</span></label>
                                    <select name="entity_type" class="global-select2 form-select" id="modal-budged-entity-type">
                                        <option value="">Pilih Entity Type</option>
                                        <option value="kingdom">Kingdom</option>
                                        <option value="owner">Owner</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Pilih Anggaran <span class="text-danger">*</span></label>
                                    <select class="global-select2 form-select" id="modal-budget-select">
                                        <option value="">Pilih Anggaran</option>
                                        {{-- @foreach ($activeBudgets as $budget)
                                            <option value="{{ $budget->budget_id }}">
                                                {{ $budget->budged_name }}
                                                ({{ \Carbon\Carbon::parse($budget->period_start_date)->format('d/m/Y') }} -
                                                {{ \Carbon\Carbon::parse($budget->period_end_date)->format('d/m/Y') }})
                                                - Sisa: Rp {{ number_format($budget->budged_remaining_value, 0, ',', '.') }}
                                            </option>
                                        @endforeach --}}
                                    </select>
                                    <small class="text-muted">Pilih anggaran sebelum approve</small>
                                </div>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Alasan Penolakan</label>
                                    <textarea class="form-control" id="modal-reject-reason-input" rows="2"
                                        placeholder="Masukkan alasan jika akan reject..."></textarea>
                                    <small class="text-muted">Wajib diisi jika akan reject</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" id="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <!-- Approve/Reject buttons shown for PENDING -->
                    <button type="button" class="btn btn-danger" id="btn-modal-reject" style="display: none;" onclick="rejectFromModal()">
                        <i class="icon-base ti tabler-x me-1"></i> Reject
                    </button>
                    <button type="button" class="btn btn-success" id="btn-modal-approve" style="display: none;" onclick="approveFromModal()">
                        <i class="icon-base ti tabler-check me-1"></i> Approve
                    </button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js_script')
    <script>
        var currentReimbursementId = null;
        var reimbursementModal = null;
        var storageBaseUrl = '{{ asset("storage") }}/';
        const allBudgets = @json($activeBudgets);

        $(function() {
            reimbursementModal = new bootstrap.Modal(document.getElementById('reimbursementModal'));
            
            initializeEntityTypeFilter();
            // Initialize DataTable
            const reimbursementTable = new DataTable('#reimbursement-approval-table', {
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('main_transaction.reimbursement_approval.index_data_table') }}',
                },
                columns: [{
                        data: 'transaction_code',
                    },
                    {
                        data: 'created_at',
                        render: function(data) {
                            return new Date(data).toLocaleDateString('id-ID');
                        }
                    },
                    {
                        data: 'creator_name',
                    },
                    {
                        data: 'reimbursement_name',
                    },
                    {
                        data: 'amount',
                        render: function(data) {
                            return 'Rp ' + parseInt(data || 0).toLocaleString('id-ID');
                        }
                    },
                    {
                        data: 'status',
                        render: function(data) {
                            const badges = {
                                'PENDING': 'bg-label-warning',
                                'APPROVED': 'bg-label-success',
                                'REJECTED': 'bg-label-danger'
                            };
                            const cls = badges[data] || 'bg-label-secondary';
                            return `<span class="badge ${cls}">${data}</span>`;
                        }
                    },
                    {
                        data: 'budget_category_name',
                        render: function(data) {
                            return data || '-';
                        }
                    },
                    {
                        data: null,
                        width: '80px',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            const btnClass = row.status === 'PENDING' ? 'btn-warning' : 'btn-primary';
                            const icon = row.status === 'PENDING' ? 'tabler-eye-check' : 'tabler-eye';
                            return `<button type="button" class="btn btn-sm ${btnClass} btn-open-modal" data-id="${row.id}">
                                <i class="icon-xs icon-base ti ${icon}"></i>
                            </button>`;
                        }
                    }
                ],
                layout: {
                    topStart: {
                        features: []
                    },
                    bottomStart: {
                        features: ['info']
                    },
                    bottomEnd: 'paging'
                },
                ordering: false
            });

            // Open modal on button click
            $(document).on('click', '.btn-open-modal', function() {
                var id = $(this).data('id');
                openReimbursementModal(id);
                initializeEntityTypeFilter();
            });
        });
        function initializeEntityTypeFilter() {

            $('#modal-budged-entity-type')
                .on('change', function() {

                    const selectedEntityType = $(this).val();
                    const budgetSelect = $('#modal-budget-select');

                    budgetSelect.empty();
                    budgetSelect.append('<option value="">Pilih Anggaran</option>');

                    if (!selectedEntityType) {
                        budgetSelect.trigger('change');
                        return;
                    }

                    const filteredBudgets = allBudgets.filter(budget =>
                        budget.entity_type === selectedEntityType
                    );

                    filteredBudgets.forEach(budget => {
                        const startDate = new Date(budget.period_start_date).toLocaleDateString('id-ID');
                        const endDate = new Date(budget.period_end_date).toLocaleDateString('id-ID');

                        budgetSelect.append(
                            `<option value="${budget.budget_id}">
                                ${budget.budged_name} (${startDate} - ${endDate})
                            </option>`
                        );
                    });

                    budgetSelect.trigger('change');
                });
        }
        // function initializeEntityTypeFilter() {
            
        //     // Listen to entity type changes only
        //     $('#modal-budged-entity-type').on('change', function() {
        //         const selectedEntityType = $(this).val();
        //         const budgetSelect = $('#modal-budget-select');

        //         // Clear and reset budget dropdown
        //         budgetSelect.empty();
        //         if (!selectedEntityType) {
        //             budgetSelect.append('<option value="">Pilih Entity Type terlebih dahulu</option>');
        //             if (budgetSelect.hasClass('select2-hidden-accessible')) {
        //                 budgetSelect.select2('destroy');
        //                 budgetSelect.select2({ allowClear: true });
        //             }
        //             return;
        //         }

        //         // Filter budgets by entity type only
        //         const filteredBudgets = allBudgets.filter(budget => {
        //             return budget.entity_type === selectedEntityType;
        //         });
        //         budgetSelect.append('<option value="">Pilih Anggaran</option>');

        //         if (filteredBudgets.length === 0) {
        //             budgetSelect.append('<option value="" disabled>Tidak ada anggaran yang sesuai</option>');
        //         } else {
        //             console.log(filteredBudgets);
        //             filteredBudgets.forEach(budget => {
        //                 console.log(budget);
        //                 const startDate = new Date(budget.period_start_date).toLocaleDateString('id-ID');
        //                 const endDate = new Date(budget.period_end_date).toLocaleDateString('id-ID');
        //                 budgetSelect.append(
        //                     `<option value="${budget.budget_id}">
        //                         ${budget.budged_name} (${startDate} - ${endDate})
        //                     </option>`
        //                 );
        //             });
        //         }

        //         // Reinitialize select2
        //         if (budgetSelect.hasClass('select2-hidden-accessible')) {
        //             budgetSelect.select2('destroy');
        //         }
        //         budgetSelect.select2({ allowClear: true });
        //         budgetSelect.trigger('change');
        //     });
        // }
        function openReimbursementModal(id) {
            currentReimbursementId = id;
            // Reset modal state
            $('#modal-loading').show();
            $('#modal-content').hide();
            $('#btn-modal-approve, #btn-modal-reject').hide();
            $('#modal-review-section, #modal-approval-section').hide();
            $('#modal-description-row, #modal-attachment-row').hide();
            $('#modal-budget-row, #modal-rejection-row').hide();
            $('#modal-budged-entity-type').val('');
            $('#modal-budget-select').val('');
            $('#modal-reject-reason-input').val('');

            reimbursementModal.show();

            initializeEntityTypeFilter();
            // Fetch detail
            var detailUrl = '{{ route("main_transaction.reimbursement_approval.detail_json", ":id") }}'.replace(':id', id);

            $.ajax({
                url: detailUrl,
                type: 'GET',
                success: function(response) {
                    var d = response.data;

                    // Fill info
                    $('#modal-code').val(d.transaction_code);
                    $('#modal-date').val(d.created_at);
                    $('#modal-creator').val(d.creator_name);
                    $('#modal-amount').val('Rp ' + parseInt(d.amount).toLocaleString('id-ID'));
                    $('#modal-name').val(d.reimbursement_name);

                    // Description
                    if (d.description) {
                        $('#modal-description').val(d.description);
                        $('#modal-description-row').show();
                    }

                    // Attachment
                    if (d.attachment) {
                        $('#modal-attachment-link').attr('href', storageBaseUrl + d.attachment);
                        $('#modal-attachment-row').show();
                    }

                    // Status badge in title
                    var badges = { 'PENDING': 'bg-label-warning', 'APPROVED': 'bg-label-success', 'REJECTED': 'bg-label-danger' };
                    $('.modal-title').html('Detail Reimbursement <span class="badge ' + (badges[d.status] || '') + ' ms-2">' + d.status + '</span>');

                    if (d.status === 'PENDING') {
                        // Show approval section
                        $('#modal-approval-section').show();
                        $('#btn-modal-approve, #btn-modal-reject').show();
                    } else {
                        // Show review info
                        $('#modal-reviewer').val(d.reviewer_name || '-');
                        $('#modal-reviewed-at').val(d.reviewed_at || '-');
                        $('#modal-review-section').show();

                        if (d.status === 'APPROVED' && d.budget_category_name) {
                            $('#modal-budget-name').val(d.budget_category_name);
                            $('#modal-budget-row').show();
                        }
                        if (d.status === 'REJECTED' && d.rejection_reason) {
                            $('#modal-rejection-reason').val(d.rejection_reason);
                            $('#modal-rejection-row').show();
                        }
                    }

                    $('#modal-loading').hide();
                    $('#modal-content').show();
                },
                error: function() {
                    reimbursementModal.hide();
                    Swal.fire({ icon: 'error', title: 'Gagal', text: 'Gagal memuat data reimbursement.' });
                }
            });
        }

        function approveFromModal() {
            var budgetEntity = $('#modal-budged-entity-type').val();
            var budgetCategoryId = $('#modal-budget-select').val();

            if (!budgetCategoryId) {
                Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Silakan pilih anggaran terlebih dahulu!' });
                return;
            }

            Swal.fire({
                title: 'Approve Reimbursement?',
                text: 'Anda yakin akan menyetujui reimbursement ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Approve!',
                cancelButtonText: 'Batal',
                customClass: { confirmButton: 'btn btn-success', cancelButton: 'btn btn-label-secondary' },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#btn-modal-approve').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Loading...');

                    var approveUrl = '{{ route("main_transaction.reimbursement_approval.approve", ":id") }}'.replace(':id', currentReimbursementId);

                    $.ajax({
                        url: approveUrl,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            budget_entity: budgetEntity,
                            budget_category_id: budgetCategoryId
                        },
                        success: function(response) {
                            reimbursementModal.hide();
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: xhr.responseJSON?.message || 'Terjadi kesalahan.'
                            });
                        },
                        complete: function() {
                            $('#btn-modal-approve').prop('disabled', false).html('<i class="icon-base ti tabler-check me-1"></i> Approve');
                        }
                    });
                }
            });
        }

        function rejectFromModal() {
            var rejectionReason = $('#modal-reject-reason-input').val().trim();

            if (!rejectionReason) {
                Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Silakan masukkan alasan penolakan terlebih dahulu!' });
                return;
            }

            Swal.fire({
                title: 'Reject Reimbursement?',
                text: 'Anda yakin akan menolak reimbursement ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Reject!',
                cancelButtonText: 'Batal',
                customClass: { confirmButton: 'btn btn-danger', cancelButton: 'btn btn-label-secondary' },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#btn-modal-reject').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Loading...');

                    var rejectUrl = '{{ route("main_transaction.reimbursement_approval.reject", ":id") }}'.replace(':id', currentReimbursementId);

                    $.ajax({
                        url: rejectUrl,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            rejection_reason: rejectionReason
                        },
                        success: function(response) {
                            reimbursementModal.hide();
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: xhr.responseJSON?.message || 'Terjadi kesalahan.'
                            });
                        },
                        complete: function() {
                            $('#btn-modal-reject').prop('disabled', false).html('<i class="icon-base ti tabler-x me-1"></i> Reject');
                        }
                    });
                }
            });
        }
    </script>
@endsection
