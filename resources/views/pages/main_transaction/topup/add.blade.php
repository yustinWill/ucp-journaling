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
        <p class="text-muted">Masukkan data pembayaran. Sistem akan mengalokasikan otomatis menggunakan metode FIFO (First
            In, First Out)</p>
    </div>

    <!-- Info Box -->
    <div class="alert alert-primary d-flex align-items-start mb-4" role="alert">
        <i class="icon-base ti tabler-info-circle me-3" style="font-size: 24px;"></i>
        <div>
            <h5 class="alert-heading mb-1">Cara Kerja Alokasi FIFO</h5>
            <p class="mb-0">
                Pembayaran akan dialokasikan otomatis ke SO (Sales Order) tertua terlebih dahulu.
                Anda tidak perlu memilih SO mana yang dibayar. Sistem akan mengalokasikan hingga saldo habis atau semua
                piutang terbayar.
            </p>
        </div>
    </div>

    <!-- Form -->
    <form id="topupForm" method="POST">
        @csrf

        <!-- Section 1: Input Data Top Up -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="icon-base ti tabler-forms me-2"></i>Step 1: Informasi Top Up
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label required">Entity</label>
                        <select class="form-select" id="entity_type" name="entity_type" required>
                            <option value="">Pilih Entity</option>
                            <option value="KINGDOM">Kingdom (Perusahaan)</option>
                            <option value="OWNER">Owner</option>
                        </select>
                        <div class="form-text">
                            <i class="icon-base ti tabler-info-circle-filled me-1"></i> Entity yang akan melakukan pembayaran
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label required">Top Up Date</label>
                        <input type="date" class="form-control" id="topup_date" name="topup_date"
                            value="{{ date('Y-m-d') }}" required>
                        <div class="form-text">
                            <i class="icon-base ti tabler-calendar me-1"></i> Tanggal pembayaran
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label required">Top Up Amount (Saldo Pembayaran)</label>
                    <input type="text" class="form-control form-control-lg" id="topup_amount" name="topup_amount"
                        placeholder="Rp 0" required style="font-size: 20px; font-weight: 600;">
                    <div class="form-text">
                        <i class="icon-base ti tabler-coin me-1"></i> Total saldo yang akan dibayarkan ke Pabrik
                    </div>
                </div>

                <div class="mb-0">
                    <label class="form-label">Notes (Optional)</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"
                        placeholder="Contoh: Pembayaran via transfer bank BCA, No. Ref: TRF123456789"></textarea>
                    <div class="form-text">
                        <i class="icon-base ti tabler-note me-1"></i> Catatan tambahan untuk referensi internal
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Outstanding Piutang -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="icon-base ti tabler-list-details me-2"></i>Step 2: Outstanding Piutang (Read Only)
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning mb-3">
                    <i class="icon-base ti tabler-alert-circle me-2"></i>
                    <strong>Informasi Penting:</strong> Tabel di bawah menampilkan semua SO yang belum lunas, diurutkan
                    dari tanggal tertua (paling atas akan dibayar pertama).
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th width="80px">Urutan FIFO</th>
                                <th>SO Date</th>
                                <th>SO Number</th>
                                <th>Customer</th>
                                <th class="text-end">Original Amount</th>
                                <th class="text-end">Remaining Amount</th>
                                <th width="100px" class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody id="outstandingTable">
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="icon-base ti tabler-inbox" style="font-size: 48px; opacity: 0.3;"></i>
                                    <div class="text-muted mt-2">Pilih Entity untuk Melihat Data</div>
                                    <small class="text-muted">Outstanding piutang akan muncul setelah Anda memilih entity di
                                        atas</small>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Section 3: Preview Alokasi -->
        <div class="card mb-4" id="allocationSection" style="display: none;">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="icon-base ti tabler-target me-2"></i>Step 3: Preview Alokasi FIFO (Otomatis)
                </h5>
            </div>
            <div class="card-body">
                <!-- Summary Box -->
                <div class="card bg-primary text-white mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-white border-opacity-25">
                            <span><i class="icon-base ti tabler-coin me-1"></i> Total Top Up Amount</span>
                            <span class="fs-5 fw-bold" id="summaryTopup">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-white border-opacity-25">
                            <span><i class="icon-base ti tabler-check me-1"></i> Total Allocated</span>
                            <span class="fs-5 fw-bold" id="summaryAllocated">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pt-2">
                            <span><i class="icon-base ti tabler-wallet me-1"></i> Remaining Balance</span>
                            <span class="fs-4 fw-bold" id="summaryRemaining">Rp 0</span>
                        </div>
                    </div>
                </div>

                <div class="alert alert-success mb-3">
                    <i class="icon-base ti tabler-circle-check me-2"></i>
                    <strong>Alokasi Berhasil Dihitung!</strong> Sistem telah mengalokasikan pembayaran secara otomatis.
                    Cek detail alokasi di bawah sebelum submit.
                </div>

                <!-- Allocation Preview -->
                <div class="card bg-light-success border-success">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0">
                            <i class="icon-base ti tabler-list me-2"></i>Detail Alokasi Per SO
                        </h6>
                    </div>
                    <div class="card-body" id="allocationList">
                        <!-- Will be populated by JavaScript -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Bar -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-label-secondary" onclick="resetForm()">
                        <i class="icon-base ti tabler-refresh me-1"></i> Reset Form
                    </button>
                    <div class="d-flex gap-2">
                        <a href="{{ route('main_transaction.topup.index') }}" class="btn btn-label-secondary">
                            <i class="icon-base ti tabler-arrow-left me-1"></i> Kembali ke List
                        </a>
                        <button type="submit" class="btn btn-success" id="submitBtn" disabled>
                            <i class="icon-base ti tabler-check me-1"></i> Submit Top Up
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@stop

@section('js_script')
    <script>
        $(function() {
            let currentEntity = null;
            let currentOutstanding = [];
            let allocationData = [];

            // Format currency input
            $('#topup_amount').on('input', function() {
                let value = $(this).val().replace(/[^0-9]/g, '');
                if (value) {
                    $(this).val('Rp ' + parseInt(value).toLocaleString('id-ID'));
                } else {
                    $(this).val('');
                }
                calculateAllocation();
            });

            // Load outstanding when entity changes
            $('#entity_type').on('change', function() {
                loadOutstanding();
            });

            // Load outstanding piutang
            function loadOutstanding() {
                const entity = $('#entity_type').val();

                if (!entity) {
                    $('#outstandingTable').html(`
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="icon-base ti tabler-inbox" style="font-size: 48px; opacity: 0.3;"></i>
                                <div class="text-muted mt-2">Pilih Entity untuk Melihat Data</div>
                                <small class="text-muted">Outstanding piutang akan muncul setelah Anda memilih entity</small>
                            </td>
                        </tr>
                    `);
                    $('#allocationSection').hide();
                    $('#submitBtn').prop('disabled', true);
                    return;
                }

                currentEntity = entity;

                // AJAX call to get outstanding sales orders
                $.ajax({
                    url: "{{ route('main_transaction.topup.get_outstanding_sales_orders') }}",
                    method: 'GET',
                    data: {
                        entity_type: entity
                    },
                    success: function(response) {
                        if (response.success && response.data.length > 0) {
                            currentOutstanding = response.data;
                            renderOutstandingTable(response.data);
                        } else {
                            $('#outstandingTable').html(`
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="icon-base ti tabler-mood-empty" style="font-size: 48px; opacity: 0.3;"></i>
                                        <div class="text-muted mt-2">Tidak Ada Piutang Outstanding</div>
                                        <small class="text-muted">Semua SO untuk entity ini sudah lunas</small>
                                    </td>
                                </tr>
                            `);
                            currentOutstanding = [];
                        }
                        calculateAllocation();
                    },
                    error: function(xhr) {
                        console.error('Error loading outstanding:', xhr);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal memuat data outstanding piutang'
                        });
                    }
                });
            }

            // Render outstanding table
            function renderOutstandingTable(data) {
                let html = '';
                data.forEach((item, index) => {
                    const statusClass = item.remaining_amount == item.total_amount ? 'danger' : 'warning';
                    const statusText = item.remaining_amount == item.total_amount ? 'OPEN' : 'PARTIAL';

                    html += `
                        <tr>
                            <td class="text-center"><strong class="text-primary">${index + 1}</strong></td>
                            <td>${formatDate(item.so_date)}</td>
                            <td><strong>${item.so_number}</strong></td>
                            <td>${item.customer_name || '-'}</td>
                            <td class="text-end">Rp ${formatNumber(item.total_amount)}</td>
                            <td class="text-end"><strong>Rp ${formatNumber(item.remaining_amount)}</strong></td>
                            <td class="text-center">
                                <span class="badge bg-label-${statusClass}">${statusText}</span>
                            </td>
                        </tr>
                    `;
                });
                $('#outstandingTable').html(html);
            }

            // Calculate FIFO allocation
            function calculateAllocation() {
                const entity = $('#entity_type').val();
                const amountStr = $('#topup_amount').val();

                if (!entity || !amountStr || currentOutstanding.length === 0) {
                    $('#allocationSection').hide();
                    $('#submitBtn').prop('disabled', true);
                    return;
                }

                let topupAmount = parseInt(amountStr.replace(/[^0-9]/g, ''));
                if (topupAmount <= 0) return;

                // AJAX call to calculate FIFO allocation
                $.ajax({
                    url: "{{ route('main_transaction.topup.calculate_fifo_allocation') }}",
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        entity_type: entity,
                        topup_amount: topupAmount
                    },
                    success: function(response) {
                        if (response.success) {
                            displayAllocation(response.data);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error calculating allocation:', xhr);
                    }
                });
            }

            // Display allocation preview
            function displayAllocation(data) {
                allocationData = data.allocations;

                // Update summary
                $('#summaryTopup').text('Rp ' + formatNumber(data.topup_amount));
                $('#summaryAllocated').text('Rp ' + formatNumber(data.total_allocated));
                $('#summaryRemaining').text('Rp ' + formatNumber(data.remaining_balance));

                // Update allocation list
                let html = '';
                data.allocations.forEach((alloc, index) => {
                    const isPaid = alloc.so_remaining_after === 0;
                    const icon = isPaid ? 'ti-circle-check text-success' : 'ti-circle-dashed text-warning';

                    html += `
                        <div class="d-flex justify-content-between align-items-center p-3 mb-2 bg-white rounded border">
                            <div class="flex-fill">
                                <div class="fw-bold mb-1">
                                    <i class="icon-base ti ${icon} me-1"></i>
                                    ${index + 1}. ${alloc.so_number}
                                </div>
                                <div class="text-muted small">
                                    ${formatDate(alloc.so_date)} · Status akan menjadi:
                                    <strong>${isPaid ? 'PAID' : 'PARTIAL'}</strong>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="fs-5 fw-bold text-success">Rp ${formatNumber(alloc.allocated_amount)}</div>
                                <div class="text-muted small">Sisa: Rp ${formatNumber(alloc.so_remaining_after)}</div>
                            </div>
                        </div>
                    `;
                });

                $('#allocationList').html(html);
                $('#allocationSection').show();
                $('#submitBtn').prop('disabled', false);
            }

            // Submit form
            $('#topupForm').on('submit', function(e) {
                e.preventDefault();

                const formData = {
                    _token: '{{ csrf_token() }}',
                    entity_type: $('#entity_type').val(),
                    topup_date: $('#topup_date').val(),
                    topup_amount: parseInt($('#topup_amount').val().replace(/[^0-9]/g, '')),
                    notes: $('#notes').val(),
                    allocations: allocationData
                };

                // Show confirmation
                Swal.fire({
                    title: 'Konfirmasi Top Up',
                    html: `
                        <div class="text-start">
                            <p><strong>Entity:</strong> ${formData.entity_type}</p>
                            <p><strong>Tanggal:</strong> ${formData.topup_date}</p>
                            <p><strong>Jumlah:</strong> Rp ${formatNumber(formData.topup_amount)}</p>
                            <p class="text-muted small">Sistem akan mengalokasikan pembayaran secara otomatis menggunakan FIFO.</p>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Submit!',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitData(formData);
                    }
                });
            });

            // Submit data via AJAX
            function submitData(formData) {
                $.ajax({
                    url: "{{ route('main_transaction.topup.store') }}",
                    method: 'POST',
                    data: formData,
                    beforeSend: function() {
                        $('#submitBtn').prop('disabled', true).html(
                            '<span class="spinner-border spinner-border-sm me-1"></span> Processing...'
                        );
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message || 'Top Up berhasil disimpan',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                window.location.href = "{{ route('main_transaction.topup.index') }}";
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message || 'Terjadi kesalahan'
                            });
                            $('#submitBtn').prop('disabled', false).html(
                                '<i class="icon-base ti tabler-check me-1"></i> Submit Top Up'
                            );
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'Terjadi kesalahan saat menyimpan data';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMsg
                        });
                        $('#submitBtn').prop('disabled', false).html(
                            '<i class="icon-base ti tabler-check me-1"></i> Submit Top Up'
                        );
                    }
                });
            }

            // Reset form
            window.resetForm = function() {
                Swal.fire({
                    title: 'Reset Form?',
                    text: 'Semua data yang sudah diinput akan hilang',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Reset',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#topupForm')[0].reset();
                        $('#entity_type').val('');
                        $('#topup_amount').val('');
                        $('#allocationSection').hide();
                        $('#submitBtn').prop('disabled', true);
                        loadOutstanding();
                    }
                });
            };

            // Helper functions
            function formatNumber(num) {
                return parseInt(num).toLocaleString('id-ID');
            }

            function formatDate(dateStr) {
                const date = new Date(dateStr);
                return date.toLocaleDateString('id-ID', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            }
        });
    </script>
@endsection
