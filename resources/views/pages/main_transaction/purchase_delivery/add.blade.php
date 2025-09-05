@extends('layouts.default')
@section('page_title')
    Buat Purchase Delivery
@stop

@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Buat Purchase Delivery</h4>
        <form class="card-body" id="purchase-delivery-form" autocomplete="off">
            <input type="hidden" name="purchase_order_id" value="{{ $purchaseOrder->id }}">
            @csrf
            <!-- Informasi Dasar Transaksi Section -->
            <div class="section-header mt-12">
                <h6>Informasi Dasar Transaksi</h6>
                <hr class="mx-n6">
            </div>

            <!-- Row 1: Tanggal Pengiriman, Nomor Transaksi -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tanggal Pengiriman <span style="color: red;">*</span></label>
                    <input type="date" class="form-control global-single-date-picker" id="purchase-delivery-date"
                        name="date" value="{{ date('Y-m-d') }}" placeholder="YYYY-MM-DD" required />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nomor Transaksi <span style="color: red;">*</span></label>
                    <div class="row g-2">
                        <div class="col">
                            <input type="text" class="form-control" id="purchase-delivery-reference" name="reference_no"
                                placeholder="Auto-generated but editable" required />
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-outline-primary d-flex align-items-center"
                                id="generate-pd-number-btn" title="Generate New Number">
                                <i class="icon-base ti tabler-refresh"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 2: Purchase Order Reference -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Purchase Order</label>
                    <input type="text" class="form-control" id="purchase-delivery-po"
                        value="{{ $purchaseOrder->transaction_code ?? '' }}" disabled />
                    <small class="form-text text-muted">Purchase Order dipilih dari halaman detail PO</small>
                </div>
            </div>

            <!-- Hidden fields for PO data -->
            <input type="hidden" id="purchase-delivery-supplier" name="supplier_id" />
            <input type="hidden" id="purchase-delivery-email" name="email" />
            <input type="hidden" id="purchase-delivery-address" name="address" />

            <!-- Informasi Purchase Order Section -->
            <div class="section-header mt-12" id="po-info-section">
                <h6>Informasi Purchase Order</h6>
                <hr class="mx-n6">
            </div>

            <div class="row g-3 mb-4" id="po-info-content">
                <div class="col-md-6">
                    <label class="form-label">Supplier</label>
                    <input type="text" class="form-control" id="po-supplier-info"
                        value="{{ $purchaseOrder->supplier_name ?? '' }}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email Supplier</label>
                    <input type="text" class="form-control" id="po-email-info"
                        value="{{ $purchaseOrder->supplier_email ?? '' }}" disabled />
                </div>
            </div>

            <div class="row g-3 mb-4" id="po-info-content-2">
                <div class="col-md-6">
                    <label class="form-label">Alamat Supplier</label>
                    <textarea class="form-control" id="po-address-info" rows="2" disabled>{{ $purchaseOrder->supplier_address ?? '' }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Gudang Tujuan (dari PO)</label>
                    <input type="text" class="form-control" id="po-warehouse-info"
                        value="{{ $purchaseOrder->warehouse_name ?? '' }}" disabled />
                </div>
            </div>

            <!-- Informasi Delivery Section -->
            <div class="section-header mt-12">
                <h6>Informasi Delivery</h6>
                <hr class="mx-n6">
            </div>

            <!-- Row 1: Nomor Delivery, Tanggal Delivery -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Nomor Delivery <span style="color: red;">*</span></label>
                    <div class="row g-2">
                        <div class="col">
                            <input type="text" class="form-control" id="delivery-number" name="delivery_number"
                                placeholder="Auto-generated but editable" required />
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-outline-primary d-flex align-items-center"
                                id="generate-delivery-number-btn" title="Generate New Number">
                                <i class="icon-base ti tabler-refresh"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Delivery <span style="color: red;">*</span></label>
                    <input type="date" class="form-control global-single-date-picker" id="delivery-date"
                        name="delivery_date" value="{{ date('Y-m-d') }}" placeholder="YYYY-MM-DD" required />
                </div>
            </div>

            <!-- Row 2: Nomor Resi, Kurir/Ekspedisi -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Nomor Resi</label>
                    <input type="text" class="form-control" id="tracking-number" name="tracking_number"
                        placeholder="Masukkan nomor resi" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kurir/Ekspedisi</label>
                    <input type="text" class="form-control" id="courier-name" name="courier_name"
                        placeholder="Nama kurir/ekspedisi" />
                </div>
            </div>

            <!-- Row 3: Gudang Tujuan -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Gudang Tujuan <span style="color: red;">*</span></label>
                    <div class="row g-2">
                        <div class="col">
                            <select class="global-select2 form-select" id="purchase-delivery-warehouse"
                                name="warehouse_id" data-allow-clear="false" required>
                                <option value="">Pilih Gudang</option>
                                @foreach ($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($canAddStorages)
                            <div class="col-auto">
                                <a href="{{ route('master_data.warehouse.add') }}" target="_blank"
                                    class="btn btn-outline-primary d-flex align-items-center" title="Tambah Gudang Baru">
                                    <i class="icon-base ti tabler-plus"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Items Delivery Section -->
            <div class="section-header mt-12">
                <h6>Items Delivery</h6>
            </div>

            <!-- Info Box -->
            <div class="alert alert-info">
                <i class="icon-base ti tabler-info-circle me-2"></i>
                Info: Pilih dan masukkan jumlah item yang akan di-deliver. Sistem akan otomatis menghitung sisa quantity.
            </div>

            <!-- Product Table -->
            <div class="mb-4 mx-n5">
                <div class="table-responsive">
                    <table class="table table-bordered" id="product-table">
                        <thead>
                            <tr>
                                <th style="width: 30%;">Produk</th>
                                <th style="width: 12%;">Qty PO</th>
                                <th style="width: 12%;">Delivered</th>
                                <th style="width: 12%;">Remaining</th>
                                <th style="width: 20%;">Qty Delivery</th>
                                <th style="width: 20%;">Note</th>
                                <th style="width: 14%;">Status</th>
                            </tr>
                        </thead>
                        <tbody id="products-tbody">
                            @php
                                $counter = 0;
                            @endphp
                            @foreach ($purchaseOrderProducts as $purchaseOrderProduct)
                                <tr>
                                    <td>{{ $purchaseOrderProduct->product_code }} - {{ $purchaseOrderProduct->product_name }}</td>
                                    <td>{{ $purchaseOrderProduct->qty }} {{ $purchaseOrderProduct->unit_name }}</td>
                                    <td>{{ $purchaseOrderProduct->delivered_qty }} {{ $purchaseOrderProduct->unit_name }}</td>
                                    <td>{{ $purchaseOrderProduct->remaining_qty }} {{ $purchaseOrderProduct->unit_name }}</td>
                                    <td>
                                        <input type="hidden" name="products[{{ $counter }}][product_order_product_id]"
                                            value="{{ $purchaseOrderProduct->id }}" />
                                        <input type="hidden" name="products[{{ $counter }}][product_id]"
                                            value="{{ $purchaseOrderProduct->f_product_id }}" />
                                        <input type="hidden" name="products[{{ $counter }}][po_qty]"
                                            value="{{ $purchaseOrderProduct->qty }}" />
                                        <input type="hidden" name="products[{{ $counter }}][delivered_qty_before]"
                                            value="{{ $purchaseOrderProduct->delivered_qty }}" />
                                        <input type="hidden" name="products[{{ $counter }}][remaining_qty_before]"
                                            value="{{ $purchaseOrderProduct->remaining_qty }}" />
                                        <input type="text" data-remaining="{{ $purchaseOrderProduct->remaining_qty }}"
                                            value="0" class="form-control delivery-qty-input numeric-input"
                                            name="products[{{ $counter }}][delivery_qty]" min="1"
                                            max="{{ $purchaseOrderProduct->remaining_qty }}" />
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" placeholder="Catatan (optional)"
                                            name="products[{{ $counter }}][note]" />
                                        <input type="hidden" class="product-status"
                                            name="products[{{ $counter }}][status]" />
                                    </td>
                                    <td></td>
                                </tr>
                                @php
                                    $counter++;
                                @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Informasi Tambahan & Ringkasan Section -->
            <div class="section-header mt-12">
                <h6>Informasi Tambahan & Ringkasan</h6>
                <hr class="mx-n6">
            </div>

            <!-- Split Layout: Tag, Memo, Attachment & Delivery Summary -->
            <div class="row">
                <!-- Left Side: Tag, Memo and Attachment -->
                <div class="col-md-6 mb-4">
                    <div class="mb-3">
                        <label class="form-label">Tag</label>
                        <div class="row g-2">
                            <div class="col">
                                <select class="global-select2 form-select" id="purchase-order-tags" name="tags[]"
                                    multiple data-allow-clear="false">
                                    @foreach ($availableTags as $tag)
                                        <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if ($canAddTags)
                                <div class="col-auto">
                                    <a href="{{ route('master_data.tag.add') }}" target="_blank"
                                        class="btn btn-outline-primary d-flex align-items-center" title="Tambah Tag Baru">
                                        <i class="icon-base ti tabler-plus"></i>
                                    </a>
                                </div>
                            @endif
                        </div>
                        <small class="form-text text-muted">Label/tag opsional untuk memudahkan pencarian/filter</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Memo (Internal)</label>
                        <textarea class="form-control" id="purchase-delivery-memo" name="memo" rows="4"
                            placeholder="Catatan internal..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lampiran</label>
                        <input type="file" class="form-control" id="purchase-delivery-attachment" name="attachment"
                            accept=".xlsx,.xls,.doc,.docx,.pdf,.png,.jpg,.jpeg,.zip" />
                        <small class="form-text text-muted">File dapat berupa Excel, Word, PDF, PNG, atau Zip (Max:
                            10MB)</small>
                    </div>
                </div>

                <!-- Right Side: Delivery Summary Section -->
                <div class="col-md-6">
                    <div class="summary-section ps-4">
                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea class="form-control" id="delivery-description" name="description" rows="4"
                                placeholder="Keterangan delivery (optional)"></textarea>
                            <small class="form-text text-muted">Catatan khusus terkait delivery</small>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Total Qty Delivery</label>
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control readonly-field" id="total-delivery-qty"
                                    dir="rtl" value="0 items" disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Status Delivery</label>
                            </div>
                            <div class="col-6">
                                <span class="badge bg-label-secondary" id="delivery-status">Belum Ada Data</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hidden fields for calculations -->
            <input type="hidden" id="purchase-delivery-total-items" name="total_items" value="0" />
            <input type="hidden" id="purchase-delivery-total-delivered" name="total_delivered" value="0" />

            <!-- Action Buttons -->
            <div class="pt-6 text-end">
                <a href="{{ route('main_transaction.purchase_delivery.index') }}"
                    class="btn btn-label-danger me-3">Batalkan</a>
                <button type="button" class="btn btn-info me-3" id="print-delivery-btn" style="display: none;">
                    <i class="icon-base ti tabler-printer me-2"></i>Print PDF
                </button>
                <button type="submit" class="btn btn-success" id="submit-btn">Simpan</button>
            </div>
        </form>
    </div>
@stop

@section('js_script')
    <script>
        $(function() {
            initCleaveCurrency(document);
            // Generate initial transaction numbers on page load
            generateTransactionNumber();
            generateDeliveryNumber();

            // Generate transaction number function
            function generateTransactionNumber() {
                $.get('{{ route('main_transaction.purchase_delivery.generate_transaction_number') }}')
                    .done(function(response) {
                        if (response.success) {
                            $('#purchase-delivery-reference').val(response.transaction_number);
                        }
                    })
                    .fail(function() {
                        console.error('Failed to generate transaction number');
                    });
            }

            // Generate delivery number function
            function generateDeliveryNumber() {
                $.get('{{ route('main_transaction.purchase_delivery.generate_delivery_number') }}')
                    .done(function(response) {
                        if (response.success) {
                            $('#delivery-number').val(response.delivery_number);
                        }
                    })
                    .fail(function() {
                        // Fallback generation
                        const year = new Date().getFullYear();
                        const month = String(new Date().getMonth() + 1).padStart(2, '0');
                        const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
                        $('#delivery-number').val(`DN-${year}${month}-${random}`);
                    });
            }

            // Generate new numbers when buttons are clicked
            $('#generate-pd-number-btn').on('click', function() {
                generateTransactionNumber();
            });

            $('#generate-delivery-number-btn').on('click', function() {
                generateDeliveryNumber();
            });


            $('.delivery-qty-input').on('input', function() {
                attachQuantityListeners($(this));
            });

            $('.delivery-qty-input').each(function() {
                // 'this' is the current input element
                attachQuantityListeners($(this));
            });

            function attachQuantityListeners(input) {
                const remaining = parseFloat(input.data('remaining'));
                const deliveryQty = parseCurrency(input.val()) || 0;

                const newRemaining = remaining - deliveryQty;

                // Update remaining display
                const remainingCell = input.closest('tr').find('.remaining-qty');
                remainingCell.text(newRemaining);

                // Update validation styling
                if (newRemaining < 0) {
                    input.addClass('is-invalid');
                    remainingCell.addClass('text-danger');
                } else {
                    input.removeClass('is-invalid');
                    remainingCell.removeClass('text-danger');
                }

                // Update status badge
                const statusCell = input.closest('tr').find('td:last-child');
                if (deliveryQty > 0) {
                    let productStatus = '';
                    if (newRemaining === 0) {
                        statusCell.html('<span class="badge bg-label-success">Complete</span>');
                        productStatus = 'COMPLETED';
                    } else if (newRemaining > 0) {
                        statusCell.html('<span class="badge bg-label-warning">Partial</span>');
                        productStatus = 'PARTIAL';
                    } else {
                        statusCell.html('<span class="badge bg-label-danger">Over Delivery</span>');
                        productStatus = 'OVER_DELIVERY';
                    }
                    input.closest('tr').find('.product-status').val(productStatus);
                } else {
                    const originalRemaining = parseInt(input.data('remaining'));
                    const statusClass = originalRemaining > 0 ? 'bg-label-success' : 'bg-label-warning';
                    const statusText = originalRemaining > 0 ? 'Available' : 'Complete';
                    statusCell.html(`<span class="badge ${statusClass}">${statusText}</span>`);
                }

                updateSummary();
            }

            function updateSummary() {
                let totalDelivered = 0;
                let hasPartialDelivery = false;
                let hasCompleteItems = false;

                $('.delivery-qty-input').each(function() {
                    const deliveryQty = parseInt($(this).val()) || 0;
                    const remaining = parseInt($(this).data('remaining'));

                    totalDelivered += deliveryQty;

                    if (deliveryQty > 0 && (remaining - deliveryQty) > 0) {
                        hasPartialDelivery = true;
                    }
                    if (deliveryQty > 0 && (remaining - deliveryQty) === 0) {
                        hasCompleteItems = true;
                    }
                });

                // Update display fields
                $('#total-delivery-qty').val(totalDelivered + ' items');
                $('#purchase-delivery-total-items').val(totalDelivered);
                $('#purchase-delivery-total-delivered').val(totalDelivered);

                // Update delivery status
                const statusBadge = $('#delivery-status');
                if (totalDelivered === 0) {
                    statusBadge.removeClass().addClass('badge bg-label-secondary').text('Belum Ada Data');
                } else if (hasPartialDelivery && hasCompleteItems) {
                    statusBadge.removeClass().addClass('badge bg-label-info').text('Mixed Delivery');
                } else if (hasPartialDelivery) {
                    statusBadge.removeClass().addClass('badge bg-label-warning').text('Partial Delivery');
                } else if (hasCompleteItems) {
                    statusBadge.removeClass().addClass('badge bg-label-success').text('Complete Delivery');
                }
            }

            $('#purchase-delivery-form').on('submit', function(e) {
                e.preventDefault();

                // Validation
                const requiredFields = [
                    'purchase-delivery-date',
                    'purchase-delivery-reference',
                    'purchase-delivery-po',
                    'delivery-number',
                    'delivery-date',
                    'purchase-delivery-warehouse'
                ];

                let isValid = true;

                requiredFields.forEach(function(fieldId) {
                    const field = $('#' + fieldId);
                    if (!field.val() || field.val().trim() === '') {
                        isValid = false;
                        console.log(fieldId)
                        field.addClass('is-invalid');
                    } else {
                        field.removeClass('is-invalid');
                    }
                });

                // Check if at least one product has delivery quantity > 0
                const deliveryInputs = $('.delivery-qty-input:not(:disabled)');
                let hasDeliveryQty = false;
                let hasOverDelivery = false;

                deliveryInputs.each(function() {
                    const deliveryQty = parseInt($(this).val()) || 0;
                    const remaining = parseInt($(this).data('remaining'));

                    if (deliveryQty > 0) {
                        hasDeliveryQty = true;
                    }

                    if (deliveryQty > remaining) {
                        hasOverDelivery = true;
                        $(this).addClass('is-invalid');
                    }
                });

                if (!hasDeliveryQty && deliveryInputs.length > 0) {
                    isValid = false;
                    Swal.fire({
                        title: 'Validasi Error!',
                        text: 'Minimal satu produk harus memiliki kuantitas pengiriman > 0.',
                        icon: 'warning',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    return;
                }

                if (hasOverDelivery) {
                    isValid = false;
                    Swal.fire({
                        title: 'Validasi Error!',
                        text: 'Kuantitas pengiriman tidak boleh melebihi sisa yang belum dikirim.',
                        icon: 'warning',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    return;
                }

                if (!isValid) {
                    Swal.fire({
                        title: 'Field Wajib!',
                        text: 'Silakan lengkapi semua field yang wajib diisi (bertanda *).',
                        icon: 'warning',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    return;
                }

                // Show confirmation
                Swal.fire({
                    title: 'Buat Purchase Delivery?',
                    text: 'Pastikan data yang diisi sudah benar.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Buat!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-label-secondary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitForm();
                    }
                });
            });

            function submitForm() {
                // Show loading state
                $('#submit-btn').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-2"></span>Loading...'
                );

                // Collect form data
                const formData = new FormData($('#purchase-delivery-form')[0]);

                const deliveryStatus = $('#delivery-status').text().trim();

                const DeliveryStatus = {
                    NONE: 'Belum Ada Data',
                    MIXED: 'Mixed Delivery',
                    PARTIAL: 'Partial Delivery',
                    COMPLETED: 'Complete Delivery'
                };
                // Optionally, map to enum value if needed
                let statusValue = '';
                switch (deliveryStatus) {
                    case DeliveryStatus.NONE:
                        statusValue = 'NONE';
                        break;
                    case DeliveryStatus.MIXED:
                        statusValue = 'MIXED';
                        break;
                    case DeliveryStatus.PARTIAL:
                        statusValue = 'PARTIAL';
                        break;
                    case DeliveryStatus.COMPLETED:
                        statusValue = 'COMPLETED';
                        break;
                    default:
                        statusValue = 'NONE';
                }

                formData.append('delivery_status', statusValue);
                // AJAX submission
                $.ajax({
                    url: "{{ route('main_transaction.purchase_delivery.store') }}",
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Purchase Delivery berhasil dibuat.',
                            icon: 'success',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        }).then(() => {
                            window.location.href =
                                "{{ route('main_transaction.purchase_delivery.index') }}";
                        });
                    },
                    error: function(xhr) {
                        let errorMessage = 'Terjadi kesalahan saat menyimpan data.';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            title: 'Gagal!',
                            text: errorMessage,
                            icon: 'error',
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
    </script>
@endsection
