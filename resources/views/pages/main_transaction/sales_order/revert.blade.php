@extends('layouts.default')

@section('page_title')
    Revert Sales Order
@stop

@section('content')
    <div class="mb-5">
        <h4 class="mb-1">
            <i class="icon-base ti tabler-history-toggle me-2"></i>Revert Sales Order
        </h4>
        <p class="text-muted mb-0">Pilih satu Sales Order, tinjau dampaknya, lalu jalankan proses revert.</p>
    </div>

    <div class="alert alert-warning d-flex align-items-start" role="alert">
        <i class="icon-base ti tabler-alert-triangle me-2 mt-1"></i>
        <div>
            <strong>Tindakan ini permanen.</strong>
            Sales Order beserta Sales Delivery dan Sales Invoice terkait akan dihapus. Stok delivery terkonfirmasi
            dan alokasi top up akan dikembalikan oleh fungsi revert yang sudah ada.
        </div>
    </div>

    @unless ($canExecuteRevert)
        <div class="alert alert-info" role="status">
            Anda dapat melihat daftar ini, tetapi memerlukan permission <code>sales_order.delete</code> untuk menjalankan revert.
        </div>
    @endunless

    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label" for="revert-search">Cari Sales Order atau customer</label>
                    <input type="search" id="revert-search" class="form-control" placeholder="Contoh: SO-2609-001">
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

    <div class="row g-4">
        <div class="col-12 col-xl-8">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-1">Pilih Sales Order</h5>
                    <small class="text-muted">Data dibatasi berdasarkan company yang sedang aktif.</small>
                </div>
                <div class="card-datatable">
                    <table id="sales-order-revert-table" class="table table-bordered align-middle w-100">
                        <thead>
                            <tr>
                                <th scope="col" class="text-center">Pilih</th>
                                <th scope="col">Tanggal</th>
                                <th scope="col">No. SO</th>
                                <th scope="col">Customer</th>
                                <th scope="col">Total</th>
                                <th scope="col">SD</th>
                                <th scope="col">SI</th>
                                <th scope="col">Risiko</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card" id="selection-card" aria-live="polite">
                <div class="card-header">
                    <h5 class="mb-0">Ringkasan pilihan</h5>
                </div>
                <div class="card-body">
                    <div id="selection-empty" class="text-center py-5 text-muted">
                        <i class="icon-base ti tabler-circle-dashed-check icon-40px mb-2"></i>
                        <p class="mb-0">Pilih satu Sales Order dari tabel.</p>
                    </div>

                    <div id="selection-detail" class="d-none">
                        <dl class="row mb-3">
                            <dt class="col-5">No. SO</dt>
                            <dd class="col-7 fw-semibold" id="selected-code"></dd>
                            <dt class="col-5">Customer</dt>
                            <dd class="col-7" id="selected-customer"></dd>
                            <dt class="col-5">Nilai</dt>
                            <dd class="col-7" id="selected-total"></dd>
                            <dt class="col-5">Delivery</dt>
                            <dd class="col-7" id="selected-deliveries"></dd>
                            <dt class="col-5">Invoice</dt>
                            <dd class="col-7" id="selected-invoices"></dd>
                        </dl>

                        <div id="selected-warning" class="alert alert-danger d-none" role="alert"></div>
                        <ul id="selected-impact" class="small ps-4 mb-4"></ul>

                        <button type="button" id="execute-revert" class="btn btn-danger w-100" disabled>
                            <i class="icon-base ti tabler-history-toggle me-1"></i>Revert Sales Order Terpilih
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js_script')
    <script>
        $(function() {
            const canExecuteRevert = @json($canExecuteRevert);
            const dataUrl = @json(route('main_transaction.sales_order_revert.index_data_table'));
            const deleteUrlTemplate = @json(route('main_transaction.sales_order.delete', ['id' => '__ID__']));
            let selectedOrder = null;
            let searchTimer = null;

            const escapeHtml = (value) => $('<div>').text(value ?? '').html();
            const asNumber = (value) => Number.parseFloat(value || 0);
            const asCount = (value) => Number.parseInt(value || 0, 10);
            const formatCurrency = (value) => new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0
            }).format(asNumber(value));

            const table = new DataTable('#sales-order-revert-table', {
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
                            title: 'Daftar tidak dapat dimuat',
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
                        data: 'id',
                        width: '64px',
                        className: 'text-center',
                        searchable: false,
                        render: function(data, type, row) {
                            if (type !== 'display') return data;
                            const checked = selectedOrder && String(selectedOrder.id) === String(data) ? 'checked' : '';
                            return `<input class="form-check-input so-selector" type="radio" name="sales_order_id" value="${Number(data)}" ${checked} aria-label="Pilih Sales Order ${escapeHtml(row.transaction_code)}">`;
                        }
                    },
                    {
                        data: 'transaction_date',
                        width: '110px',
                        render: $.fn.dataTable.render.text()
                    },
                    {
                        data: 'transaction_code',
                        render: function(data, type) {
                            if (type !== 'display') return data;
                            return `<span class="fw-semibold text-primary">${escapeHtml(data)}</span>`;
                        }
                    },
                    {
                        data: 'customer_name',
                        defaultContent: '-',
                        render: $.fn.dataTable.render.text()
                    },
                    {
                        data: 'grand_price',
                        className: 'text-end text-nowrap',
                        render: function(data, type) {
                            return type === 'display' ? formatCurrency(data) : data;
                        }
                    },
                    {
                        data: 'delivery_count',
                        className: 'text-center',
                        render: function(data, type, row) {
                            if (type !== 'display') return data;
                            return `${asCount(data)} <small class="text-muted">(${asCount(row.confirmed_delivery_count)} confirmed)</small>`;
                        }
                    },
                    {
                        data: 'invoice_count',
                        className: 'text-center',
                        render: function(data, type, row) {
                            if (type !== 'display') return data;
                            const risky = asCount(row.paid_invoice_count) + asCount(row.partial_invoice_count);
                            return `${asCount(data)}${risky > 0 ? ` <span class="badge bg-label-danger">${risky} paid</span>` : ''}`;
                        }
                    },
                    {
                        data: null,
                        className: 'text-center',
                        render: function(data, type, row) {
                            if (type !== 'display') return '';
                            if (asCount(row.sales_return_count) > 0) {
                                return '<span class="badge bg-label-danger">Blocked: return</span>';
                            }
                            if (asNumber(row.payment_total) > 0) {
                                return '<span class="badge bg-label-danger">Payment</span>';
                            }
                            if (asCount(row.reimbursement_count) > 0) {
                                return '<span class="badge bg-label-warning">Reimbursement</span>';
                            }
                            if (asCount(row.confirmed_delivery_count) > 0) {
                                return '<span class="badge bg-label-info">Stock reversal</span>';
                            }
                            return '<span class="badge bg-label-secondary">Standard</span>';
                        }
                    }
                ],
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                language: {
                    emptyTable: 'Tidak ada Sales Order yang tersedia.',
                    zeroRecords: 'Sales Order tidak ditemukan.',
                    processing: 'Memuat Sales Order...',
                    search: '',
                    paginate: {
                        next: '<i class="icon-base ti tabler-chevron-right"></i>',
                        previous: '<i class="icon-base ti tabler-chevron-left"></i>'
                    }
                },
                layout: {
                    topStart: 'pageLength',
                    topEnd: {},
                    bottomStart: 'info',
                    bottomEnd: 'paging'
                },
                responsive: true
            });

            function clearSelection() {
                selectedOrder = null;
                $('#selection-empty').removeClass('d-none');
                $('#selection-detail').addClass('d-none');
                $('#execute-revert').prop('disabled', true);
            }

            function showSelection(order) {
                selectedOrder = order;
                const salesReturnCount = asCount(order.sales_return_count);
                const paymentTotal = asNumber(order.payment_total);
                const reimbursementCount = asCount(order.reimbursement_count);
                const confirmedDeliveryCount = asCount(order.confirmed_delivery_count);
                const impacts = [];

                impacts.push(`${asCount(order.delivery_count)} Sales Delivery akan dihapus.`);
                impacts.push(`${asCount(order.invoice_count)} Sales Invoice akan dihapus.`);

                if (confirmedDeliveryCount > 0) {
                    impacts.push(`Stok dari ${confirmedDeliveryCount} delivery terkonfirmasi akan dikembalikan.`);
                }
                if (paymentTotal > 0) {
                    impacts.push(`Record pembayaran sebesar ${formatCurrency(paymentTotal)} akan dihapus oleh fungsi yang ada.`);
                }
                if (reimbursementCount > 0) {
                    impacts.push(`${reimbursementCount} reimbursement ongkir tetap tersimpan dan perlu ditinjau manual.`);
                }

                $('#selected-code').text(order.transaction_code);
                $('#selected-customer').text(order.customer_name || '-');
                $('#selected-total').text(formatCurrency(order.grand_price));
                $('#selected-deliveries').text(`${asCount(order.delivery_count)} (${confirmedDeliveryCount} confirmed)`);
                $('#selected-invoices').text(`${asCount(order.invoice_count)} (${asCount(order.paid_invoice_count)} paid/settled)`);
                $('#selected-impact').html(impacts.map(item => `<li>${escapeHtml(item)}</li>`).join(''));
                $('#selection-empty').addClass('d-none');
                $('#selection-detail').removeClass('d-none');

                if (salesReturnCount > 0) {
                    $('#selected-warning')
                        .removeClass('d-none')
                        .text('Revert tidak dapat dijalankan karena Sales Order memiliki Sales Return. Reversal return terlebih dahulu.');
                } else {
                    $('#selected-warning').addClass('d-none').text('');
                }

                $('#execute-revert').prop('disabled', !canExecuteRevert || salesReturnCount > 0);
            }

            $('#sales-order-revert-table tbody').on('change', '.so-selector', function() {
                const row = table.row($(this).closest('tr')).data();
                if (row) showSelection(row);
            });

            $('#revert-search').on('input', function() {
                clearTimeout(searchTimer);
                const searchValue = this.value;
                searchTimer = setTimeout(function() {
                    clearSelection();
                    table.search(searchValue).draw();
                }, 350);
            });

            $('#date-from, #date-to').on('change', function() {
                clearSelection();
                table.ajax.reload();
            });

            $('#reset-filter').on('click', function() {
                $('#revert-search, #date-from, #date-to').val('');
                clearSelection();
                table.search('');
                table.ajax.reload();
            });

            $('#execute-revert').on('click', async function() {
                if (!selectedOrder || !canExecuteRevert) return;

                const transactionCode = selectedOrder.transaction_code;
                const result = await Swal.fire({
                    icon: 'warning',
                    title: 'Konfirmasi revert permanen',
                    html: `
                        <p class="mb-2">Sales Order yang akan direvert:</p>
                        <div class="border rounded p-3 mb-3 bg-label-secondary">
                            <strong class="fs-5">${escapeHtml(transactionCode)}</strong>
                        </div>
                        <p class="mb-0">SO, Sales Delivery, dan Sales Invoice terkait akan dihapus secara permanen.</p>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Revert sekarang',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-danger',
                        cancelButton: 'btn btn-label-secondary'
                    },
                    buttonsStyling: false,
                    reverseButtons: true,
                    focusCancel: true
                });

                if (!result.isConfirmed) return;

                const button = $('#execute-revert');
                button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span>Memproses revert...');

                $.ajax({
                    url: deleteUrlTemplate.replace('__ID__', selectedOrder.id),
                    type: 'DELETE',
                    success: function(response) {
                        const summary = response.data || {};
                        clearSelection();
                        table.ajax.reload(null, false);
                        Swal.fire({
                            icon: 'success',
                            title: 'Sales Order berhasil direvert',
                            html: `${escapeHtml(response.message)}<br><small>${asCount(summary.sales_deliveries_deleted)} delivery dan ${asCount(summary.sales_invoices_deleted)} invoice dihapus.</small>`,
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        });
                    },
                    error: function(xhr) {
                        button.prop('disabled', false);
                        Swal.fire({
                            icon: 'error',
                            title: 'Revert gagal',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan saat menjalankan revert.',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        });
                    },
                    complete: function() {
                        button.html('<i class="icon-base ti tabler-history-toggle me-1"></i>Revert Sales Order Terpilih');
                    }
                });
            });
        });
    </script>
@endsection
