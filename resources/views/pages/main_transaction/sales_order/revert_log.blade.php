@extends('layouts.default')

@section('page_title')
    Log Revert Sales Order
@stop

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-5">
        <div>
            <h4 class="mb-1">
                <i class="icon-base ti tabler-list-details me-2"></i>Log Revert Sales Order
            </h4>
            <p class="text-muted mb-0">Riwayat permanen Sales Order yang telah direvert pada company aktif.</p>
        </div>
        @can('sales_order_revert.view')
            <a href="{{ route('main_transaction.sales_order_revert.index') }}" class="btn btn-outline-primary">
                <i class="icon-base ti tabler-history-toggle me-1"></i>Buka Revert Sales Order
            </a>
        @endcan
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label" for="revert-log-search">Cari nomor SO, customer, atau user</label>
                    <input type="search" id="revert-log-search" class="form-control" placeholder="Contoh: SO-2609-001">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label" for="date-from">Dari tanggal</label>
                    <input type="date" id="date-from" class="form-control">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label" for="date-to">Sampai tanggal</label>
                    <input type="date" id="date-to" class="form-control">
                </div>
                <div class="col-12 col-md-2">
                    <button type="button" id="reset-filter" class="btn btn-outline-secondary w-100">
                        <i class="icon-base ti tabler-filter-off me-1"></i>Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-1">Riwayat revert</h5>
            <small class="text-muted">Log dibuat dalam transaksi database yang sama dengan proses revert.</small>
        </div>
        <div class="card-datatable">
            <table id="sales-order-revert-log-table" class="table table-bordered align-middle w-100">
                <thead>
                    <tr>
                        <th scope="col">Waktu</th>
                        <th scope="col">No. SO</th>
                        <th scope="col">Customer</th>
                        <th scope="col">Nilai</th>
                        <th scope="col">Direvert oleh</th>
                        <th scope="col">Dampak</th>
                        <th scope="col" class="text-center">Detail</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@stop

@section('js_script')
    <script>
        $(function() {
            const dataUrl = @json(route('main_transaction.sales_order_revert_log.index_data_table'));
            const escapeHtml = value => $('<div>').text(value ?? '').html();
            const asCount = value => Number.parseInt(value || 0, 10);
            const asNumber = value => Number.parseFloat(value || 0);
            const formatCurrency = value => new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0
            }).format(asNumber(value));
            let searchTimer = null;

            const table = new DataTable('#sales-order-revert-log-table', {
                processing: true,
                serverSide: true,
                searching: true,
                ordering: false,
                ajax: {
                    url: dataUrl,
                    data: function(data) {
                        data.date_from = $('#date-from').val();
                        data.date_to = $('#date-to').val();
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Log tidak dapat dimuat',
                            text: xhr.responseJSON?.message || 'Silakan muat ulang halaman dan coba lagi.',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        });
                    }
                },
                columns: [
                    {
                        data: 'created_at',
                        render: data => escapeHtml(data || '-')
                    },
                    {
                        data: 'transaction_code',
                        render: data => `<span class="fw-semibold">${escapeHtml(data)}</span>`
                    },
                    {
                        data: 'customer_name',
                        render: data => escapeHtml(data || '-')
                    },
                    {
                        data: 'grand_price',
                        className: 'text-end text-nowrap',
                        render: data => escapeHtml(formatCurrency(data))
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            const name = escapeHtml(row.actor_name || 'System');
                            const email = row.actor_email
                                ? `<small class="d-block text-muted">${escapeHtml(row.actor_email)}</small>`
                                : '';
                            return `<span>${name}</span>${email}`;
                        }
                    },
                    {
                        data: 'summary',
                        render: function(summary) {
                            summary = summary || {};
                            return `
                                <div class="d-flex flex-wrap gap-1">
                                    <span class="badge bg-label-danger">SD ${asCount(summary.sales_deliveries_deleted)}</span>
                                    <span class="badge bg-label-danger">SI ${asCount(summary.sales_invoices_deleted)}</span>
                                    <span class="badge bg-label-warning">Stock ${asCount(summary.stock_movements_reversed)}</span>
                                </div>
                            `;
                        }
                    },
                    {
                        data: null,
                        searchable: false,
                        className: 'text-center',
                        render: function() {
                            return `
                                <button type="button" class="btn btn-sm btn-outline-primary show-log-detail">
                                    <i class="icon-base ti tabler-eye me-1"></i>Lihat
                                </button>
                            `;
                        }
                    }
                ],
                language: {
                    emptyTable: 'Belum ada Sales Order yang direvert.',
                    zeroRecords: 'Log revert tidak ditemukan.',
                    processing: 'Memuat log revert...'
                },
                layout: {
                    topStart: 'pageLength',
                    topEnd: {},
                    bottomStart: 'info',
                    bottomEnd: 'paging'
                },
                responsive: true
            });

            $('#revert-log-search').on('input', function() {
                clearTimeout(searchTimer);
                const searchValue = this.value;
                searchTimer = setTimeout(() => table.search(searchValue).draw(), 350);
            });

            $('#date-from, #date-to').on('change', function() {
                table.ajax.reload();
            });

            $('#reset-filter').on('click', function() {
                $('#revert-log-search, #date-from, #date-to').val('');
                table.search('');
                table.ajax.reload();
            });

            $('#sales-order-revert-log-table tbody').on('click', '.show-log-detail', function() {
                const rowElement = $(this).closest('tr');
                const row = table.row(rowElement.hasClass('child') ? rowElement.prev() : rowElement).data();
                if (!row) return;

                const summary = row.summary || {};
                const order = row.order_snapshot || {};
                const listCodes = values => Array.isArray(values) && values.length
                    ? values.join(', ')
                    : '-';
                const detailRows = [
                    ['Tanggal SO', order.transaction_date || '-'],
                    ['Status sebelum revert', order.status || '-'],
                    ['Sales Delivery dihapus', asCount(summary.sales_deliveries_deleted)],
                    ['No. Sales Delivery', listCodes(summary.sales_delivery_codes)],
                    ['Sales Invoice dihapus', asCount(summary.sales_invoices_deleted)],
                    ['No. Sales Invoice', listCodes(summary.sales_invoice_codes)],
                    ['Pembayaran dihapus', `${asCount(summary.invoice_payments_deleted)} (${formatCurrency(summary.payment_value_deleted)})`],
                    ['Stock movement reversal', asCount(summary.stock_movements_reversed)],
                    ['Stock batch dipulihkan', asCount(summary.stock_batches_restored)],
                    ['Alokasi top up dihapus', asCount(summary.topup_allocations_deleted)],
                    ['Revenue split dihapus', asCount(summary.revenue_splits_deleted)]
                ];

                Swal.fire({
                    icon: 'info',
                    title: row.transaction_code,
                    html: `
                        <div class="text-start small">
                            <p class="mb-3"><strong>${escapeHtml(row.customer_name || '-')}</strong><br>${escapeHtml(formatCurrency(row.grand_price))}</p>
                            <dl class="row mb-0">
                                ${detailRows.map(([label, value]) => `
                                    <dt class="col-6 mb-2">${escapeHtml(label)}</dt>
                                    <dd class="col-6 mb-2 text-break">${escapeHtml(String(value))}</dd>
                                `).join('')}
                            </dl>
                        </div>
                    `,
                    confirmButtonText: 'Tutup',
                    customClass: {
                        popup: 'text-start',
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                });
            });
        });
    </script>
@stop
