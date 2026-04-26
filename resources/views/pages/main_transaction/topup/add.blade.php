@extends('layouts.default')
@section('page_title')
    Top Up Pembayaran - Tambah Baru
@stop
@section('content')

    <!-- Page Header -->
    <div class="mb-4">
        <h4 class="mb-1">
            <i class="icon-base ti tabler-cash me-2"></i>Form Top Up Pembayaran Pabrik
        </h4>
        <p class="text-muted">Pilih Sales Order yang ingin dibayar, atau biarkan kosong untuk alokasi otomatis FIFO.</p>
    </div>

    <!-- Form -->
    <form id="topupForm" method="POST">
        @csrf

        <!-- Section 1: Input Data Top Up -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="icon-base ti tabler-forms me-2"></i>Informasi Top Up
                </h5>
            </div>
            <div class="card-body">
                <input type="hidden" id="entity_type" name="entity_type" value="OWNER">
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label required">Tanggal Top Up</label>
                        <input type="date" class="form-control" id="topup_date" name="topup_date"
                            value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label required">Jumlah Top Up</label>
                        <input type="text" class="form-control" id="topup_amount" name="topup_amount"
                            placeholder="Rp 0" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Total SO Dipilih</label>
                        <input type="text" class="form-control bg-light" id="total_selected_so" readonly
                            placeholder="Rp 0" title="Total remaining amount dari SO yang dipilih">
                    </div>
                </div>
                <div class="mb-0">
                    <label class="form-label">Notes (Optional)</label>
                    <textarea class="form-control" id="notes" name="notes" rows="2"
                        placeholder="Catatan tambahan untuk referensi internal"></textarea>
                </div>
            </div>
        </div>

        <!-- Section 2: Outstanding SO Table with Checkboxes -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="icon-base ti tabler-list-check me-2"></i>Pilih Sales Order
                </h5>
                <div class="d-flex gap-2 align-items-center">
                    <small class="text-muted" id="selected-count">0 SO dipilih</small>
                    <button type="button" class="btn btn-sm btn-label-secondary" id="btn-check-all">Pilih Semua</button>
                    <button type="button" class="btn btn-sm btn-label-secondary" id="btn-uncheck-all">Batal Semua</button>
                </div>
            </div>
            <div class="card-body pb-1">
                <div class="alert alert-info mb-3">
                    <i class="icon-base ti tabler-info-circle me-2"></i>
                    Centang SO yang ingin dibayar. Jika tidak ada yang dipilih, sistem akan menggunakan alokasi FIFO otomatis.
                    Jika jumlah top up melebihi total SO dipilih, sisa akan dialokasikan ke SO lain secara FIFO.
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="50px" class="text-center">
                                <input type="checkbox" id="checkAll" class="form-check-input">
                            </th>
                            <th width="60px">#</th>
                            <th>SO Date</th>
                            <th>SO Number</th>
                            <th>Customer</th>
                            <th class="text-end">Original Amount</th>
                            <th class="text-end">Remaining Amount</th>
                            <th width="90px" class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody id="outstandingTable">
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="spinner-border text-primary" role="status"></div>
                                <div class="text-muted mt-2">Memuat data...</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 3: Allocation Preview -->
        <div class="card mb-4" id="allocationSection" style="display: none;">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="icon-base ti tabler-target me-2"></i>Preview Alokasi
                </h5>
            </div>
            <div class="card-body">
                <!-- Summary -->
                <div class="card bg-primary text-white mb-3">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-white border-opacity-25">
                            <span><i class="icon-base ti tabler-coin me-1"></i> Total Top Up</span>
                            <span class="fs-5 fw-bold" id="summaryTopup">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-white border-opacity-25">
                            <span><i class="icon-base ti tabler-check me-1"></i> Total Dialokasikan</span>
                            <span class="fs-5 fw-bold" id="summaryAllocated">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pt-1">
                            <span><i class="icon-base ti tabler-wallet me-1"></i> Sisa Saldo</span>
                            <span class="fs-4 fw-bold" id="summaryRemaining">Rp 0</span>
                        </div>
                    </div>
                </div>

                <!-- Allocation List -->
                <div id="allocationList"></div>
            </div>
        </div>

        <!-- Action Bar -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('main_transaction.topup.index') }}" class="btn btn-label-danger">
                        <i class="icon-base ti tabler-arrow-left me-1"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-success" id="submitBtn" disabled>
                        <i class="icon-base ti tabler-check me-1"></i> Submit Top Up
                    </button>
                </div>
            </div>
        </div>
    </form>
@stop

@section('js_script')
    <script>
        $(function() {
            let currentOutstanding = []; // all outstanding SOs
            let allocationData = [];     // final allocation for submission

            // ── Format helpers ────────────────────────────────────────────
            function fmt(num) {
                return parseInt(num || 0).toLocaleString('id-ID');
            }
            function fmtDate(d) {
                return new Date(d).toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric' });
            }
            function getTopupAmount() {
                return parseInt($('#topup_amount').val().replace(/[^0-9]/g, '') || 0);
            }

            // ── Currency input ────────────────────────────────────────────
            $('#topup_amount').on('input', function() {
                let v = $(this).val().replace(/[^0-9]/g, '');
                $(this).val(v ? 'Rp ' + parseInt(v).toLocaleString('id-ID') : '');
                recalculate();
            });

            // ── Load outstanding SOs on page load ─────────────────────────
            loadOutstanding();

            function loadOutstanding() {
                $('#outstandingTable').html(`
                    <tr><td colspan="8" class="text-center py-4">
                        <div class="spinner-border text-primary spinner-border-sm"></div>
                        <span class="ms-2 text-muted">Memuat data...</span>
                    </td></tr>`);

                $.ajax({
                    url: "{{ route('main_transaction.topup.get_outstanding_sales_orders') }}",
                    method: 'GET',
                    data: { entity_type: 'OWNER' },
                    success: function(response) {
                        if (response.success && response.data.length > 0) {
                            currentOutstanding = response.data;
                            renderOutstandingTable(response.data);
                        } else {
                            currentOutstanding = [];
                            $('#outstandingTable').html(`
                                <tr><td colspan="8" class="text-center py-5">
                                    <i class="icon-base ti tabler-mood-empty" style="font-size:40px;opacity:0.3"></i>
                                    <div class="text-muted mt-2">Tidak ada piutang outstanding</div>
                                </td></tr>`);
                        }
                        recalculate();
                    },
                    error: function() {
                        $('#outstandingTable').html(`<tr><td colspan="8" class="text-center text-danger py-3">Gagal memuat data</td></tr>`);
                    }
                });
            }

            // ── Render SO table ───────────────────────────────────────────
            function renderOutstandingTable(data) {
                let html = '';
                data.forEach((item, index) => {
                    const isPartial = parseFloat(item.remaining_amount) < parseFloat(item.total_amount);
                    const statusClass = isPartial ? 'warning' : 'danger';
                    const statusText  = isPartial ? 'PARTIAL' : 'OPEN';
                    html += `
                        <tr class="so-row" data-id="${item.id}" data-remaining="${item.remaining_amount}" data-total="${item.total_amount}">
                            <td class="text-center">
                                <input type="checkbox" class="form-check-input so-checkbox" value="${item.id}"
                                    data-remaining="${item.remaining_amount}">
                            </td>
                            <td class="text-center text-muted">${index + 1}</td>
                            <td>${fmtDate(item.so_date)}</td>
                            <td><strong>${item.so_number}</strong></td>
                            <td>${item.customer_name || '-'}</td>
                            <td class="text-end">Rp ${fmt(item.total_amount)}</td>
                            <td class="text-end"><strong>Rp ${fmt(item.remaining_amount)}</strong></td>
                            <td class="text-center"><span class="badge bg-label-${statusClass}">${statusText}</span></td>
                        </tr>`;
                });
                $('#outstandingTable').html(html);
            }

            // ── Checkbox handlers ─────────────────────────────────────────
            $(document).on('change', '.so-checkbox', function() {
                updateCheckAllState();
                recalculate();
            });

            $('#checkAll').on('change', function() {
                $('.so-checkbox').prop('checked', $(this).is(':checked'));
                recalculate();
            });

            $('#btn-check-all').on('click', function() {
                $('.so-checkbox').prop('checked', true);
                $('#checkAll').prop('checked', true);
                recalculate();
            });

            $('#btn-uncheck-all').on('click', function() {
                $('.so-checkbox').prop('checked', false);
                $('#checkAll').prop('checked', false);
                recalculate();
            });

            function updateCheckAllState() {
                const total   = $('.so-checkbox').length;
                const checked = $('.so-checkbox:checked').length;
                $('#checkAll').prop('checked', total > 0 && checked === total);
            }

            // ── Get selected SO ids ───────────────────────────────────────
            function getSelectedIds() {
                return $('.so-checkbox:checked').map(function() {
                    return parseInt($(this).val());
                }).get();
            }

            // ── Recalculate allocation preview ────────────────────────────
            function recalculate() {
                const selectedIds = getSelectedIds();
                const selectedCount = selectedIds.length;

                // Update selected count badge
                $('#selected-count').text(selectedCount + ' SO dipilih');

                // Highlight selected rows
                $('.so-row').removeClass('table-primary');
                if (selectedCount > 0) {
                    $('.so-checkbox:checked').closest('tr').addClass('table-primary');
                }

                // Compute total selected remaining
                let totalSelectedRemaining = 0;
                selectedIds.forEach(id => {
                    const row = currentOutstanding.find(s => s.id == id);
                    if (row) totalSelectedRemaining += parseFloat(row.remaining_amount);
                });
                $('#total_selected_so').val(totalSelectedRemaining > 0 ? 'Rp ' + fmt(totalSelectedRemaining) : '');

                const topupAmount = getTopupAmount();
                if (topupAmount <= 0 || currentOutstanding.length === 0) {
                    $('#allocationSection').hide();
                    $('#submitBtn').prop('disabled', topupAmount <= 0);
                    return;
                }

                // ── Build allocation ──────────────────────────────────────
                const allocations = [];
                let remaining = topupAmount;
                let totalAllocated = 0;
                let seq = 1;

                // Step 1: allocate to selected SOs first (in FIFO order of selected)
                const selectedSOs = currentOutstanding.filter(s => selectedIds.includes(s.id));
                selectedSOs.forEach(so => {
                    if (remaining <= 0) return;
                    const soRemaining = parseFloat(so.remaining_amount);
                    if (soRemaining <= 0) return;
                    const alloc = Math.min(remaining, soRemaining);
                    allocations.push({
                        sequence: seq++,
                        so_id: so.id,
                        so_number: so.so_number,
                        so_date: so.so_date,
                        so_total: parseFloat(so.total_amount),
                        so_remaining_before: soRemaining,
                        allocated_amount: alloc,
                        so_remaining_after: soRemaining - alloc,
                        is_selected: true,
                    });
                    remaining -= alloc;
                    totalAllocated += alloc;
                });

                // Step 2: if still remaining, FIFO on unselected SOs
                if (remaining > 0) {
                    const unselectedSOs = currentOutstanding.filter(s => !selectedIds.includes(s.id));
                    unselectedSOs.forEach(so => {
                        if (remaining <= 0) return;
                        const soRemaining = parseFloat(so.remaining_amount);
                        if (soRemaining <= 0) return;
                        const alloc = Math.min(remaining, soRemaining);
                        allocations.push({
                            sequence: seq++,
                            so_id: so.id,
                            so_number: so.so_number,
                            so_date: so.so_date,
                            so_total: parseFloat(so.total_amount),
                            so_remaining_before: soRemaining,
                            allocated_amount: alloc,
                            so_remaining_after: soRemaining - alloc,
                            is_selected: false,
                        });
                        remaining -= alloc;
                        totalAllocated += alloc;
                    });
                }

                allocationData = allocations;
                renderAllocationPreview(topupAmount, totalAllocated, remaining, allocations);
            }

            // ── Render allocation preview ─────────────────────────────────
            function renderAllocationPreview(topupAmount, totalAllocated, remaining, allocations) {
                $('#summaryTopup').text('Rp ' + fmt(topupAmount));
                $('#summaryAllocated').text('Rp ' + fmt(totalAllocated));
                $('#summaryRemaining').text('Rp ' + fmt(remaining));

                let html = '';
                if (allocations.length === 0) {
                    html = `<div class="alert alert-info mb-0">
                        <i class="icon-base ti tabler-info-circle me-2"></i>
                        Tidak ada piutang yang akan dialokasikan. Saldo akan disimpan sebagai sisa.
                    </div>`;
                } else {
                    allocations.forEach(alloc => {
                        const isPaid = alloc.so_remaining_after === 0;
                        const icon   = isPaid ? 'ti-circle-check text-success' : 'ti-circle-dashed text-warning';
                        const badge  = alloc.is_selected
                            ? '<span class="badge bg-label-primary ms-1">Dipilih</span>'
                            : '<span class="badge bg-label-secondary ms-1">FIFO</span>';
                        html += `
                            <div class="d-flex justify-content-between align-items-center p-3 mb-2 bg-white rounded border">
                                <div class="flex-fill">
                                    <div class="fw-bold mb-1">
                                        <i class="icon-base ti ${icon} me-1"></i>
                                        ${alloc.sequence}. ${alloc.so_number} ${badge}
                                    </div>
                                    <div class="text-muted small">
                                        ${fmtDate(alloc.so_date)} &middot; Sisa setelah: <strong>Rp ${fmt(alloc.so_remaining_after)}</strong>
                                        &middot; Status: <strong>${isPaid ? 'PAID' : 'PARTIAL'}</strong>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="fs-5 fw-bold text-success">Rp ${fmt(alloc.allocated_amount)}</div>
                                </div>
                            </div>`;
                    });

                    if (remaining > 0) {
                        html += `<div class="alert alert-warning mb-0 mt-2">
                            <i class="icon-base ti tabler-wallet me-2"></i>
                            Sisa saldo <strong>Rp ${fmt(remaining)}</strong> akan disimpan untuk alokasi berikutnya.
                        </div>`;
                    }
                }

                $('#allocationList').html(html);
                $('#allocationSection').show();
                $('#submitBtn').prop('disabled', false);
            }

            // ── Submit ────────────────────────────────────────────────────
            $('#topupForm').on('submit', function(e) {
                e.preventDefault();

                const topupAmount = getTopupAmount();
                if (topupAmount <= 0) {
                    Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Masukkan jumlah top up.' });
                    return;
                }

                const selectedCount = getSelectedIds().length;
                const modeText = selectedCount > 0
                    ? `${selectedCount} SO dipilih + FIFO untuk sisa`
                    : 'Alokasi FIFO otomatis (tidak ada SO dipilih)';

                Swal.fire({
                    title: 'Konfirmasi Top Up',
                    html: `<div class="text-start">
                        <p><strong>Tanggal:</strong> ${$('#topup_date').val()}</p>
                        <p><strong>Jumlah:</strong> Rp ${fmt(topupAmount)}</p>
                        <p><strong>Mode:</strong> ${modeText}</p>
                    </div>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Submit!',
                    cancelButtonText: 'Batal',
                    customClass: { confirmButton: 'btn btn-success', cancelButton: 'btn btn-label-secondary' },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) submitData();
                });
            });

            function submitData() {
                const formData = {
                    _token: '{{ csrf_token() }}',
                    entity_type: 'OWNER',
                    topup_date: $('#topup_date').val(),
                    topup_amount: getTopupAmount(),
                    notes: $('#notes').val(),
                    selected_so_ids: getSelectedIds(),
                };

                $('#submitBtn').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1"></span> Processing...'
                );

                $.ajax({
                    url: "{{ route('main_transaction.topup.store') }}",
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({ icon: 'success', title: 'Berhasil!', text: response.message })
                                .then(() => { window.location.href = "{{ route('main_transaction.topup.index') }}"; });
                        } else {
                            Swal.fire({ icon: 'error', title: 'Gagal', text: response.message });
                            $('#submitBtn').prop('disabled', false).html('<i class="icon-base ti tabler-check me-1"></i> Submit Top Up');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Terjadi kesalahan' });
                        $('#submitBtn').prop('disabled', false).html('<i class="icon-base ti tabler-check me-1"></i> Submit Top Up');
                    }
                });
            }
        });
    </script>
@endsection
