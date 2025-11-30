@extends('layouts.default')
@section('page_title')
    Absorpsi Overhead
@stop

@section('css_script')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
@stop

@section('content')
    <div class="mt-6">
        <!-- Header Card -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Absorpsi Overhead</h4>
                <span id="settlement-status-badge" class="badge bg-label-warning" style="display: none;">
                    <i class="ti ti-alert-circle me-1"></i> Belum Disettle
                </span>
            </div>
            <div class="card-body">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Periode <span class="text-danger">*</span></label>
                        <input type="month" class="form-control" id="period-selector" value="{{ date('Y-m') }}">
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-primary" id="load-data-btn">
                            <i class="ti ti-refresh me-1"></i> Muat Data
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div class="card mb-4" id="empty-state" style="display: none;">
            <div class="card-body text-center py-5">
                <div class="avatar avatar-xl mx-auto mb-3">
                    <span class="avatar-initial rounded bg-label-secondary">
                        <i class="ti ti-file-off ti-lg"></i>
                    </span>
                </div>
                <h5 class="mb-2">Tidak ada data pada periode ini</h5>
                <p class="text-muted mb-0">Pilih periode dan klik "Muat Data" untuk menampilkan data produksi dan overhead</p>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4" id="summary-cards" style="display: none;">
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="ti ti-package ti-md"></i>
                                </span>
                            </div>
                            <div>
                                <small class="text-muted d-block">Total Produksi</small>
                                <h3 class="mb-0" id="total-production">0 <small class="text-muted">unit</small></h3>
                                <small class="text-muted">Dari <span id="batch-count">0</span> batch produksi</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-success">
                                    <i class="ti ti-cash ti-md"></i>
                                </span>
                            </div>
                            <div>
                                <small class="text-muted d-block">Total Overhead</small>
                                <h3 class="mb-0 text-success" id="grand-total-overhead">Rp 0</h3>
                                <small class="text-muted">Produksi + Expense</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Production Batch Table -->
        <div class="card mb-4" id="production-card" style="display: none;">
            <div class="card-header">
                <h5 class="mb-0">Batch Produksi Bulan Ini</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Kode Produk</th>
                                <th>Nama Produk</th>
                                <th class="text-end">Qty</th>
                                <th class="text-end">HPP Sementara</th>
                                <th class="text-end">HPP Final</th>
                            </tr>
                        </thead>
                        <tbody id="production-tbody">
                        </tbody>
                        <tfoot>
                            <tr class="table-primary fw-bold">
                                <td colspan="3">Total</td>
                                <td class="text-end" id="footer-total-qty">0</td>
                                <td class="text-end" id="footer-total-hpp-sementara">Rp 0</td>
                                <td class="text-end" id="footer-total-hpp-final">Rp 0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Overhead Expenses Table -->
        <div class="card mb-4" id="expense-card" style="display: none;">
            <div class="card-header">
                <h5 class="mb-0">Biaya Overhead</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No. Transaksi</th>
                                <th>Tanggal</th>
                                <th>Keterangan</th>
                                <th class="text-end">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody id="expense-tbody">
                        </tbody>
                        <tfoot>
                            <tr class="table-primary fw-bold">
                                <td colspan="3">Total Biaya Overhead</td>
                                <td class="text-end" id="footer-total-expenses">Rp 0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Summary and Settlement Section -->
        <div class="card mb-4" id="summary-card" style="display: none;">
            <div class="card-header">
                <h5 class="mb-0">Ringkasan Total Overhead</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between py-2">
                            <span class="text-muted">Real-Time Overhead (dari batch produksi)</span>
                            <span class="fw-semibold" id="summary-prod-overhead">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between py-2">
                            <span class="text-muted">End-of-Month Overhead (biaya tambahan)</span>
                            <span class="fw-semibold" id="summary-expense-overhead">Rp 0</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between py-2">
                            <span class="fw-bold">Grand Total Overhead</span>
                            <span class="fw-bold text-success fs-5" id="summary-grand-total">Rp 0</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-info">
                            <i class="ti ti-info-circle me-2"></i>
                            <small>Total overhead ini akan dialokasikan ke seluruh batch produksi saat proses settle dilakukan.</small>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Catatan (opsional)</label>
                        <textarea class="form-control" id="settlement-notes" rows="3" placeholder="Tambahkan catatan untuk settlement ini..."></textarea>
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <button type="button" class="btn btn-success btn-lg w-100" id="settle-btn">
                            <i class="ti ti-check me-2"></i> Settle Overhead Bulan Ini
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Riwayat Batch Produk Settlement History -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Riwayat Settlement Overhead</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle" id="batch-table">
                        <thead class="table-light">
                            <tr>
                                <th>No. Transaksi</th>
                                <th>Periode</th>
                                <th>Tanggal Settlement</th>
                                <th class="text-end">Total Qty</th>
                                <th class="text-end">Total Overhead</th>
                                <th class="text-end">Overhead/Unit</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="history-tbody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
@stop

@section('js_script')
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            let currentPreviewData = null;
            let historyTable = null;

            console.log('Overhead Absorption initialized');

            // Initialize DataTable for history
            historyTable = $('#batch-table').DataTable({
                processing: true,
                serverSide: false,
                pageLength: 10,
                order: [[1, 'desc']], // Sort by period descending
                responsive: true,
                autoWidth: false,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                },
                columnDefs: [
                    { targets: 0, width: '15%' },
                    { targets: 1, width: '10%' },
                    { targets: 2, width: '13%' },
                    { targets: 3, width: '10%', className: 'text-end' },
                    { targets: 4, width: '15%', className: 'text-end' },
                    { targets: 5, width: '12%', className: 'text-end' },
                    { targets: 6, width: '10%', className: 'text-center', orderable: false },
                    { targets: 7, width: '15%', className: 'text-center', orderable: false, searchable: false }
                ],
                columns: [
                    { data: 'transaction_no', name: 'transaction_no' },
                    { data: 'period', name: 'period' },
                    { data: 'settlement_date', name: 'settlement_date' },
                    { data: 'total_production_qty', name: 'total_production_qty' },
                    { data: 'total_overhead_cost', name: 'total_overhead_cost' },
                    { data: 'overhead_per_unit', name: 'overhead_per_unit' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action' }
                ]
            });

            // Load settlement history on page load
            loadHistory();

            // Auto-load data for current period on page load
            const currentPeriod = $('#period-selector').val();
            if (currentPeriod) {
                loadPreviewData(currentPeriod);
            }

            // Period selector change event
            $('#period-selector').on('change', function() {
                const period = $(this).val();
                if (period) {
                    loadPreviewData(period);
                }
            });

            // Load data button
            $('#load-data-btn').click(function() {
                const period = $('#period-selector').val();
                if (!period) {
                    showToast('error', 'Pilih periode terlebih dahulu');
                    return;
                }
                loadPreviewData(period);
            });

            // Settle button
            $('#settle-btn').click(function() {
                if (!currentPreviewData) {
                    showToast('error', 'Muat data terlebih dahulu');
                    return;
                }

                Swal.fire({
                    title: 'Konfirmasi Settlement',
                    html: 'Apakah Anda yakin ingin settle overhead bulan ini?<br><br>' +
                        '<small class="text-muted">Proses ini akan menghitung HPP final dan tidak dapat dibatalkan.</small>',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Settle!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-label-secondary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        settleOverhead();
                    }
                });
            });

            function loadPreviewData(period) {
                console.log('Loading preview data for period:', period);
                $('#load-data-btn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Loading...');

                $.ajax({
                    url: '{{ route("main_transaction.overhead_absorption.preview") }}',
                    method: 'GET',
                    data: { period: period },
                    success: function(response) {
                        console.log('Preview response:', response);
                        if (response.success) {
                            if (response.data.can_settle || response.data.is_settled) {
                                currentPreviewData = response.data;
                                displayPreviewData(response.data);
                            } else {
                                hideAllSections();
                            }
                        } else {
                            hideAllSections();
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading preview:', xhr);
                        console.error('Response:', xhr.responseText);
                        hideAllSections();
                    },
                    complete: function() {
                        $('#load-data-btn').prop('disabled', false).html('<i class="ti ti-refresh me-1"></i> Muat Data');
                    }
                });
            }

            function displayPreviewData(data) {
                // Hide empty state and show all sections
                $('#empty-state').hide();
                $('#summary-cards, #production-card, #expense-card, #summary-card').show();

                // Update summary cards
                $('#total-production').html(formatNumber(data.total_qty) + ' <small class="text-muted">unit</small>');
                $('#batch-count').text(data.batch_count);
                $('#grand-total-overhead').text(formatCurrency(data.grand_total_overhead));

                // Display production data
                displayProductionData(data.productions, data.total_production_overhead, data.is_settled, data.overhead_per_unit);

                // Display expense data
                displayExpenseData(data.expenses, data.total_expense_overhead);

                // Update summary section
                $('#summary-prod-overhead').text(formatCurrency(data.total_production_overhead));
                $('#summary-expense-overhead').text(formatCurrency(data.total_expense_overhead));
                $('#summary-grand-total').text(formatCurrency(data.grand_total_overhead));

                // Show/hide settle button and update status badge
                if (data.is_settled) {
                    // Remove settle button and notes field for settled periods
                    $('#settle-btn').parent().remove();
                    $('#settlement-notes').parent().remove();
                    $('#settlement-status-badge').show().removeClass('bg-label-warning')
                        .addClass('bg-label-success').html('<i class="ti ti-check me-1"></i> Settled - ' + data.settlement_no);
                } else {
                    // Show settle button and notes field for unsettled periods
                    $('#settle-btn').parent().show();
                    $('#settlement-notes').parent().show();
                    $('#settlement-status-badge').show().removeClass('bg-label-success')
                        .addClass('bg-label-warning').html('<i class="ti ti-alert-circle me-1"></i> Belum Disettle');
                }
            }

            function displayProductionData(productions, totalOverhead, isSettled, overheadPerUnit) {
                const tbody = $('#production-tbody');
                tbody.empty();

                let totalQty = 0;
                let totalHppSementara = 0;
                let totalHppFinal = 0;

                productions.forEach(function(prod) {
                    totalQty += parseFloat(prod.qty);

                    // HPP Sementara from production table before overhead allocation
                    const hppSementara = parseFloat(prod.hpp_sementara);
                    totalHppSementara += hppSementara; // Sum of HPP Sementara, not multiplied by qty

                    // HPP Final = HPP Sementara + (allocated_overhead / qty)
                    let hppFinalDisplay = '-';
                    let hppFinal = 0;
                    if (isSettled) {
                        const allocatedOverheadPerUnit = parseFloat(prod.allocated_overhead) / parseFloat(prod.qty);
                        hppFinal = hppSementara + allocatedOverheadPerUnit;
                        hppFinalDisplay = formatCurrency(hppFinal);
                        totalHppFinal += hppFinal; // Sum of HPP Final, not multiplied by qty
                    }

                    const row = `
                        <tr>
                            <td>${formatDate(prod.production_date)}</td>
                            <td>${prod.product_code || '-'}</td>
                            <td>${prod.product_name}</td>
                            <td class="text-end">${formatNumber(prod.qty)}</td>
                            <td class="text-end">${formatCurrency(hppSementara)}</td>
                            <td class="text-end">${hppFinalDisplay}</td>
                        </tr>
                    `;
                    tbody.append(row);
                });

                $('#footer-total-qty').text(formatNumber(totalQty));
                $('#footer-total-hpp-sementara').text(formatCurrency(totalHppSementara));
                $('#footer-total-hpp-final').text(isSettled ? formatCurrency(totalHppFinal) : '-');
            }

            function displayExpenseData(expenses, total) {
                const tbody = $('#expense-tbody');
                tbody.empty();

                if (expenses.length === 0) {
                    tbody.append(`
                        <tr>
                            <td colspan="4" class="text-center text-muted">Tidak ada biaya overhead untuk periode ini</td>
                        </tr>
                    `);
                } else {
                    expenses.forEach(function(exp) {
                        const row = `
                            <tr>
                                <td>${exp.transaction_no}</td>
                                <td>${formatDate(exp.transaction_date)}</td>
                                <td>${exp.description || '-'}</td>
                                <td class="text-end">${formatCurrency(exp.total_amount)}</td>
                            </tr>
                        `;
                        tbody.append(row);
                    });
                }

                $('#footer-total-expenses').text(formatCurrency(total));
            }

            function settleOverhead() {
                const period = $('#period-selector').val();
                const notes = $('#settlement-notes').val();

                $.ajax({
                    url: '{{ route("main_transaction.overhead_absorption.settle") }}',
                    method: 'POST',
                    data: {
                        period: period,
                        notes: notes,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Overhead berhasil disettle. HPP final telah dihitung.',
                                customClass: {
                                    confirmButton: 'btn btn-success'
                                },
                                buttonsStyling: false
                            }).then(() => {
                                location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        const message = xhr.responseJSON?.message || 'Gagal melakukan settlement';
                        showToast('error', message);
                    }
                });
            }

            function loadHistory() {
                console.log('Loading history...');
                $.ajax({
                    url: '{{ route("main_transaction.overhead_absorption.history") }}',
                    method: 'GET',
                    success: function(response) {
                        console.log('History response:', response);
                        if (response.success) {
                            displayHistory(response.data);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading history:', xhr);
                        historyTable.clear().draw();
                    }
                });
            }

            function displayHistory(settlements) {
                // Clear existing data
                historyTable.clear();

                if (settlements.length === 0) {
                    historyTable.draw();
                    return;
                }

                // Format data for DataTable
                const formattedData = settlements.map(function(settlement) {
                    const statusBadge = settlement.is_settled
                        ? '<span class="badge bg-label-success"><i class="ti ti-check me-1"></i>Settled</span>'
                        : '<span class="badge bg-label-warning"><i class="ti ti-alert-circle me-1"></i>Draft</span>';

                    const actionButton = `
                        <button class="btn btn-sm btn-info" onclick="viewDetails(${settlement.id})">
                            <i class="ti ti-eye me-1"></i> Detail
                        </button>
                    `;

                    return {
                        transaction_no: `<strong>${settlement.transaction_no}</strong>`,
                        period: settlement.period,
                        settlement_date: formatDate(settlement.settlement_date),
                        total_production_qty: formatNumber(settlement.total_production_qty),
                        total_overhead_cost: formatCurrency(settlement.total_overhead_cost),
                        overhead_per_unit: formatCurrency(settlement.overhead_per_unit),
                        status: statusBadge,
                        action: actionButton
                    };
                });

                // Add data to DataTable and redraw
                historyTable.rows.add(formattedData).draw();
            }

            window.viewDetails = function(id) {
                window.location.href = '{{ route("main_transaction.overhead_absorption.index") }}/' + id;
            };

            function hideAllSections() {
                $('#summary-cards, #production-card, #expense-card, #summary-card').hide();
                $('#empty-state').show();
            }

            function formatCurrency(value) {
                return 'Rp ' + parseFloat(value).toLocaleString('id-ID', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                });
            }

            function formatNumber(value) {
                return parseFloat(value).toLocaleString('id-ID');
            }

            function formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleDateString('id-ID');
            }

            function showToast(type, message) {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });

                Toast.fire({
                    icon: type,
                    title: message
                });
            }
        });
    </script>
@stop
