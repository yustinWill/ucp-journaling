@extends('layouts.default')
@section('page_title')
    Detail Purchase Invoice - {{ $purchaseInvoice['reference_no'] }}
@stop
@section('content')
    <div class="card mb-6">
        <div class="card-body">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <h4 class="mb-3">Detail Purchase Invoice</h4>
                </div>
                <div class="col-md-4">
                    @php
                        $totalActions = 1; // Edit always present
                        if($canApprovePurchaseInvoice && $purchaseInvoice['status'] === 'PENDING_PAYMENT') {
                            $totalActions += 2; // Pay and Cancel
                        }
                        if($canDeletePurchaseInvoice && in_array($purchaseInvoice['status'], ['DRAFT', 'CANCELLED'])) {
                            $totalActions++; // Delete action
                        }
                        if($purchaseInvoice['status'] === 'PAID') {
                            $totalActions++; // Print receipt
                        }
                    @endphp

                    @if($totalActions < 3)
                        <!-- Show buttons directly when less than 3 actions -->
                        <div class="d-flex gap-2 justify-content-end">
                            @if($canEditPurchaseInvoice)
                                <a href="{{ route('main_transaction.purchase_invoice.edit', $purchaseInvoice['id']) }}" class="btn btn-primary btn-md waves-effect waves-light">
                                    <i class="icon-base ti tabler-edit me-2"></i>
                                    Edit
                                </a>
                            @endif
                            @if($purchaseInvoice['status'] === 'PAID')
                                <button type="button" class="btn btn-success btn-md waves-effect waves-light" id="print-receipt-btn">
                                    <i class="icon-base ti tabler-printer me-2"></i>
                                    Print
                                </button>
                            @endif
                        </div>
                    @else
                        <!-- Show dropdown for 3 or more actions -->
                        <div class="d-flex gap-2 justify-content-end">
                            @if($canEditPurchaseInvoice)
                                <a href="{{ route('main_transaction.purchase_invoice.edit', $purchaseInvoice['id']) }}" class="btn btn-primary btn-md waves-effect waves-light">
                                    <i class="icon-base ti tabler-edit me-2"></i>
                                    Edit
                                </a>
                            @endif
                            <div class="dropdown">
                                <button type="button" class="btn btn-primary btn-md waves-effect waves-light dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    Aksi
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    @if($canApprovePurchaseInvoice && $purchaseInvoice['status'] === 'PENDING_PAYMENT')
                                        <li>
                                            <a class="dropdown-item" href="javascript:void(0);" id="pay-invoice-btn">
                                                <i class="icon-base ti tabler-credit-card me-2"></i>Bayar Invoice
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="javascript:void(0);" id="cancel-invoice-btn">
                                                <i class="icon-base ti tabler-x me-2"></i>Batalkan
                                            </a>
                                        </li>
                                    @endif
                                    @if($purchaseInvoice['status'] === 'PAID')
                                        <li>
                                            <a class="dropdown-item" href="javascript:void(0);" id="print-receipt-btn">
                                                <i class="icon-base ti tabler-printer me-2"></i>Print Receipt
                                            </a>
                                        </li>
                                    @endif
                                    @if($canDeletePurchaseInvoice && in_array($purchaseInvoice['status'], ['DRAFT', 'CANCELLED']))
                                        <li>
                                            <a class="dropdown-item" href="javascript:void(0);" id="delete-pi-btn">
                                                <i class="icon-base ti tabler-trash me-2"></i>Hapus
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Entity Title -->
            <h2>{{ $purchaseInvoice['reference_no'] }}</h2>
            <span>
                <h6>Data di bawah berdasarkan tanggal {{ date('Y-m-d H:i:s') }}</h6>
            </span>
            <hr class="my-6 mx-n6" />

            <!-- Tab Navigation -->
            <ul class="nav nav-tabs nav-fill mb-6" role="tablist">
                <li class="nav-item">
                    <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#informasi-detail" aria-controls="informasi-detail" aria-selected="true">
                        <i class="icon-base ti tabler-info-circle me-2"></i>
                        <span class="align-middle">Informasi Detail</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#audit-log" aria-controls="audit-log" aria-selected="false">
                        <i class="icon-base ti tabler-history me-2"></i>
                        <span class="align-middle">Audit Log</span>
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content p-0">
                <!-- Informasi Detail Tab -->
                <div class="tab-pane fade show active" id="informasi-detail" role="tabpanel">
                    <!-- Basic Information Section -->
                    <div class="section-header mt-12">
                        <h6>Informasi Umum</h6>
                        <hr class="mx-n6">
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">No. Purchase Invoice</label>
                            <input type="text" class="form-control" value="{{ $purchaseInvoice['reference_no'] }}" disabled />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal</label>
                            <input type="text" class="form-control" value="{{ $purchaseInvoice['date'] }}" disabled />
                        </div>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Supplier</label>
                            <input type="text" class="form-control" value="{{ $purchaseInvoice['supplier'] }}" disabled />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <input type="text" class="form-control" value="{{ $purchaseInvoice['status'] }}" disabled />
                        </div>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">PO Reference</label>
                            <input type="text" class="form-control" value="{{ $purchaseInvoice['po_reference'] }}" disabled />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">PD Reference</label>
                            <input type="text" class="form-control" value="{{ $purchaseInvoice['pd_reference'] }}" disabled />
                        </div>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Jatuh Tempo</label>
                            <input type="text" class="form-control" value="{{ $purchaseInvoice['due_date'] }}" disabled />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Currency</label>
                            <input type="text" class="form-control" value="{{ $purchaseInvoice['currency'] }}" disabled />
                        </div>
                    </div>

                    <!-- Financial Information Section -->
                    <div class="section-header mt-12">
                        <h6>Informasi Keuangan</h6>
                        <hr class="mx-n6">
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Subtotal</label>
                            <input type="text" class="form-control" value="Rp {{ number_format($purchaseInvoice['subtotal'], 0, ',', '.') }}" disabled />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tax Amount</label>
                            <input type="text" class="form-control" value="Rp {{ number_format($purchaseInvoice['tax_amount'], 0, ',', '.') }}" disabled />
                        </div>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Discount</label>
                            <input type="text" class="form-control" value="Rp {{ number_format($purchaseInvoice['discount'], 0, ',', '.') }}" disabled />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Total Invoice</label>
                            <input type="text" class="form-control fw-bold" value="Rp {{ number_format($purchaseInvoice['invoice_amount'], 0, ',', '.') }}" disabled />
                        </div>
                    </div>

                    <!-- Payment Information Section -->
                    <div class="section-header mt-12">
                        <h6>Informasi Pembayaran</h6>
                        <hr class="mx-n6">
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Dibayar</label>
                            <input type="text" class="form-control" value="Rp {{ number_format($purchaseInvoice['paid_amount'], 0, ',', '.') }}" disabled />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sisa</label>
                            <input type="text" class="form-control" value="Rp {{ number_format($purchaseInvoice['remaining_amount'], 0, ',', '.') }}" disabled />
                        </div>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Payment Terms</label>
                            <input type="text" class="form-control" value="{{ $purchaseInvoice['payment_terms'] }}" disabled />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Payment Method</label>
                            <input type="text" class="form-control" value="{{ $purchaseInvoice['payment_method'] }}" disabled />
                        </div>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label class="form-label">Bank Account</label>
                            <input type="text" class="form-control" value="{{ $purchaseInvoice['bank_account'] }}" disabled />
                        </div>
                    </div>

                    <!-- Additional Information Section -->
                    <div class="section-header mt-12">
                        <h6>Informasi Tambahan</h6>
                        <hr class="mx-n6">
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label class="form-label">Catatan Invoice</label>
                            <textarea class="form-control" rows="3" disabled>{{ $purchaseInvoice['invoice_notes'] }}</textarea>
                        </div>
                    </div>

                    <!-- Timestamps Section -->
                    <div class="section-header mt-12">
                        <h6>Informasi Sistem</h6>
                        <hr class="mx-n6">
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Dibuat Pada</label>
                            <input type="text" class="form-control" value="{{ $purchaseInvoice['created_at'] }}" disabled />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Diupdate Pada</label>
                            <input type="text" class="form-control" value="{{ $purchaseInvoice['updated_at'] }}" disabled />
                        </div>
                    </div>
                </div>

                <!-- Audit Log Tab -->
                <div class="tab-pane fade" id="audit-log" role="tabpanel">
                    <div class="section-header mt-12">
                        <h6>Riwayat Perubahan</h6>
                        <hr class="mx-n6">
                    </div>
                    
                    <div class="card-datatable">
                        <table id="audit-log-table" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>User</th>
                                    <th>Aksi</th>
                                    <th>Deskripsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($auditLogs as $log)
                                    <tr>
                                        <td>{{ $log['created_at'] }}</td>
                                        <td>{{ $log['user_name'] }}</td>
                                        <td>
                                            @php
                                                $badgeClass = '';
                                                switch($log['action_type']) {
                                                    case 'CREATE':
                                                        $badgeClass = 'bg-label-success';
                                                        break;
                                                    case 'UPDATE':
                                                        $badgeClass = 'bg-label-primary';
                                                        break;
                                                    case 'DELETE':
                                                        $badgeClass = 'bg-label-danger';
                                                        break;
                                                    default:
                                                        $badgeClass = 'bg-label-secondary';
                                                }
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">{{ $log['action_type'] }}</span>
                                        </td>
                                        <td>{{ $log['description'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js_script')
<script>
$(function() {
    // Initialize audit log table
    if ($('#audit-log-table').length) {
        new DataTable('#audit-log-table', {
            order: [[0, 'desc']], // Sort by date descending (latest first)
            pageLength: 10,
            language: {
                paginate: {
                    next: '<i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>',
                    previous: '<i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>',
                    first: '<i class="icon-base ti tabler-chevrons-left scaleX-n1-rtl icon-18px"></i>',
                    last: '<i class="icon-base ti tabler-chevrons-right scaleX-n1-rtl icon-18px"></i>'
                }
            }
        });
    }

    // Action button handlers
    $('#pay-invoice-btn').on('click', function() {
        const remainingAmount = {{ $purchaseInvoice['remaining_amount'] }};
        
        Swal.fire({
            title: 'Bayar Invoice?',
            html: `
                <div class="text-start">
                    <div class="mb-3">
                        <label class="form-label">Jumlah Pembayaran</label>
                        <input type="number" id="payment-amount" class="form-control" value="${remainingAmount}" min="1" max="${remainingAmount}">
                        <small class="text-muted">Sisa: Rp ${remainingAmount.toLocaleString('id-ID')}</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Pembayaran</label>
                        <input type="date" id="payment-date" class="form-control" value="${new Date().toISOString().split('T')[0]}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea id="payment-notes" class="form-control" rows="2" placeholder="Catatan pembayaran..."></textarea>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Bayar',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-primary',
                cancelButton: 'btn btn-outline-secondary'
            },
            buttonsStyling: false,
            preConfirm: () => {
                const amount = document.getElementById('payment-amount').value;
                const date = document.getElementById('payment-date').value;
                const notes = document.getElementById('payment-notes').value;
                
                if (!amount || !date) {
                    Swal.showValidationMessage('Jumlah dan tanggal pembayaran harus diisi');
                    return false;
                }
                
                if (parseFloat(amount) > remainingAmount) {
                    Swal.showValidationMessage('Jumlah pembayaran tidak boleh lebih dari sisa tagihan');
                    return false;
                }
                
                return {
                    amount: amount,
                    date: date,
                    notes: notes
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Handle payment logic here
                Swal.fire({
                    title: 'Pembayaran Berhasil!',
                    text: `Invoice berhasil dibayar sebesar Rp ${parseFloat(result.value.amount).toLocaleString('id-ID')}.`,
                    icon: 'success',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                }).then(() => {
                    location.reload();
                });
            }
        });
    });

    $('#cancel-invoice-btn').on('click', function() {
        Swal.fire({
            title: 'Batalkan Invoice?',
            text: 'Invoice akan dibatalkan dan tidak dapat diproses.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Batalkan',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-outline-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                // Handle cancellation logic here
                Swal.fire({
                    title: 'Invoice Dibatalkan',
                    text: 'Purchase Invoice berhasil dibatalkan.',
                    icon: 'success',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                });
            }
        });
    });

    $('#print-receipt-btn').on('click', function() {
        // Handle print receipt logic here
        Swal.fire({
            title: 'Print Receipt',
            text: 'Fitur print sedang dalam pengembangan.',
            icon: 'info',
            customClass: {
                confirmButton: 'btn btn-primary'
            },
            buttonsStyling: false
        });
    });

    $('#delete-pi-btn').on('click', function() {
        Swal.fire({
            title: 'Hapus Purchase Invoice?',
            text: 'Data yang dihapus tidak dapat dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-outline-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                // Handle delete logic here
                Swal.fire({
                    title: 'Dihapus!',
                    text: 'Purchase Invoice berhasil dihapus.',
                    icon: 'success',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                }).then(() => {
                    window.location.href = '{{ route("main_transaction.purchase_invoice.index") }}';
                });
            }
        });
    });
});
</script>
@endsection