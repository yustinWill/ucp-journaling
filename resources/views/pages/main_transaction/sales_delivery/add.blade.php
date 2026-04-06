@extends('layouts.default')
@section('page_title')
    Tambah Sales Delivery
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Buat Sales Delivery</h4>
        <form class="card-body" id="sales-delivery-form" autocomplete="off">
            @csrf

            <!-- Section 1: Basic Transaction Information -->
            <div class="section-header mt-12">
                <h6>Informasi Dasar Transaksi</h6>
                <hr class="mx-n6">
            </div>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tanggal Pengiriman <span style="color: red;">*</span></label>
                    <input type="date" class="form-control global-single-date-picker" id="sales-delivery-date"
                        name="delivery_date" value="{{ date('Y-m-d') }}" placeholder="YYYY-MM-DD" required />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nomor Transaksi <span style="color: red;">*</span></label>
                    <div class="row g-2">
                        <div class="col">
                            <input type="text" class="form-control" id="sales-delivery-reference" name="delivery_number"
                                placeholder="Auto-generated but editable" required />
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-outline-primary d-flex align-items-center"
                                id="generate-sd-number-btn" title="Generate New Number">
                                <i class="icon-base ti tabler-refresh"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Sales Order Reference (Optional) -->
            <div class="section-header mt-12">
                <h6>Sales Order (Opsional)</h6>
                <hr class="mx-n6">
            </div>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Sales Order</label>
                    <div class="row g-2">
                        <div class="col">
                            <select class="global-select2 form-select" id="sales-delivery-so" name="sales_order_id"
                                data-allow-clear="true" {{ isset($preselectedSOId) ? 'disabled' : '' }}>
                                <option value="">Pilih Sales Order (atau kosongkan untuk direct delivery)</option>
                                @foreach ($salesOrderList as $salesOrder)
                                    <option value="{{ $salesOrder->id }}"
                                        data-customer-id="{{ $salesOrder->f_customer_id }}"
                                        data-customer-name="{{ $salesOrder->customer }}"
                                        data-customer-email="{{ $salesOrder->customer_email ?? '' }}"
                                        data-customer-address="{{ $salesOrder->customer_address ?? '' }}"
                                        data-warehouse-id="{{ $salesOrder->f_warehouse_id }}"
                                        {{ isset($preselectedSOId) && $preselectedSOId == $salesOrder->id ? 'selected' : '' }}>
                                        {{ $salesOrder->transaction_code }} - {{ $salesOrder->customer }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @if (!isset($preselectedSOId))
                            <div class="col-auto">
                                <a href="{{ route('main_transaction.sales_order.add') }}" target="_blank"
                                    class="btn btn-outline-primary d-flex align-items-center" title="Tambah Sales Order">
                                    <i class="icon-base ti tabler-plus"></i>
                                </a>
                            </div>
                            <small class="form-text text-muted">Pilih Sales Order untuk delivery dari order yang ada, atau
                                kosongkan
                                untuk direct delivery</small>
                        @endif
                    </div>

                </div>
            </div>

            <!-- Section 3: Customer Information -->
            <div class="section-header mt-12">
                <h6>Informasi Customer</h6>
                <hr class="mx-n6">
            </div>

            <!-- Row 1: Customer -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Customer <span style="color: red;">*</span></label>
                    <div class="row g-2">
                        <div class="col">
                            <select class="global-select2 form-select" id="sales-delivery-customer" name="customer_id"
                                data-allow-clear="false" required @if (isset($preselectedSOId)) disabled @endif>
                                <option value="">Pilih Customer</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}" data-email="{{ $customer->email ?? '' }}"
                                        data-address="{{ $customer->billing_address ?? '' }}">
                                        {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @if (!isset($preselectedSOId))
                            <div class="col-auto">
                                <a href="{{ route('master_data.contact.add') }}" target="_blank"
                                    class="btn btn-outline-primary d-flex align-items-center" title="Tambah Customer">
                                    <i class="icon-base ti tabler-plus"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email Customer</label>
                    <input type="email" class="form-control" id="sales-delivery-email" name="customer_email" disabled />
                </div>
            </div>

            <!-- Row 2: Customer Address -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Customer Address</label>
                    <textarea class="form-control" id="sales-delivery-customer-address" rows="2" disabled></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Alamat Pengiriman</label>
                    <textarea class="form-control" id="sales-delivery-address" name="delivery_address" rows="2"
                        placeholder="Alamat pengiriman (jika berbeda dari alamat customer)"></textarea>
                </div>
            </div>

            <!-- Section 4: Delivery Information -->
            <div class="section-header mt-12">
                <h6>Informasi Pengiriman</h6>
                <hr class="mx-n6">
            </div>

            <!-- Row 1: Warehouse, Status -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Gudang <span style="color: red;">*</span></label>
                    <div class="row g-2">
                        <div class="col">
                            <select class="global-select2 form-select" id="sales-delivery-warehouse" name="warehouse_id"
                                data-allow-clear="false" required @if (isset($preselectedSOId)) disabled @endif>
                                <option value="">Pilih Gudang</option>
                                @foreach ($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ $loop->first ? 'selected' : '' }}>{{ $warehouse->code }} - {{ $warehouse->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @if (!isset($preselectedSOId))
                            <div class="col-auto">
                                <a href="{{ route('master_data.warehouse.add') }}" target="_blank"
                                    class="btn btn-outline-primary d-flex align-items-center" title="Tambah Gudang">
                                    <i class="icon-base ti tabler-plus"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nomor Resi Pengiriman</label>
                    <input type="text" class="form-control" id="sales-delivery-customer-ref"
                        name="customer_reference" placeholder="Nomor Resi Pengiriman" disabled/>
                </div>
            </div>

            <!-- Row 2: Shipping Cost -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Ongkos Kirim <span class="text-muted">(Opsional)</span></label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" class="form-control global-currency" id="shipping-cost" name="shipping_cost"
                            placeholder="0" value="0" disabled />
                    </div>
                    <small class="form-text text-muted">Biaya ongkir untuk pengiriman ini</small>
                </div>
                {{-- <div class="col-md-6">
                    <label class="form-label">Status <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="sales-delivery-status" name="status"
                        data-allow-clear="false" required>
                        <option value="">Pilih Status</option>
                        <option value="PENDING" selected>PENDING - Menunggu</option>
                        <option value="IN_TRANSIT">IN_TRANSIT - Dalam Perjalanan</option>
                        <option value="DELIVERED">DELIVERED - Terkirim</option>
                        <option value="CANCELLED">CANCELLED - Dibatalkan</option>
                        <option value="RETURNED">RETURNED - Dikembalikan</option>
                    </select>
                </div> --}}
            </div>

            <!-- Row 2: Customer Reference -->
            {{-- <div class="row g-3 mb-4">
                
            </div> --}}

            <!-- Section 5: Product Lines -->
            <div class="section-header mt-12">
                <h6>Detail Produk</h6>
            </div>

            @if (isset($preselectedSOId))
                <div class="mb-4 mx-n5">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="product-table">
                            <thead>
                                <tr>
                                    <th style="width: 30%;">Produk</th>
                                    <th style="width: 12%;">Qty Dipesan</th>
                                    <th style="width: 12%;">Qty Terkirim</th>
                                    <th style="width: 12%;">Qty Tersisa</th>
                                    <th style="width: 20%;">Qty Dikirim</th>
                                    <th style="width: 20%;">Note</th>
                                    <th style="width: 14%;">Status</th>
                                </tr>
                            </thead>
                            <tbody id="products-tbody">
                                @php
                                    $counter = 0;
                                @endphp
                                @foreach ($salesOrderProducts as $row)
                                    <tr>
                                        <td>{{ $row->product_code }} -
                                            {{ $row->product_name }}</td>
                                        <td>
                                            @php
                                                $quantity = $row->quantity;
                                                if ($quantity == floor($quantity)) {
                                                    $formattedQuantity = number_format($quantity, 0, ',', '.');
                                                } else {
                                                    $formatted = number_format($quantity, 4, ',', '.');
                                                    $formattedQuantity = rtrim(rtrim($formatted, '0'), ',');
                                                }
                                            @endphp
                                            {{ $formattedQuantity }} {{ $row->unit_name }}
                                        </td>
                                        <td>
                                            @php
                                                $deliveredQty = $row->delivered_qty;
                                                if ($deliveredQty == floor($deliveredQty)) {
                                                    $formattedDeliveredQty = number_format($deliveredQty, 0, ',', '.');
                                                } else {
                                                    $formatted = number_format($deliveredQty, 4, ',', '.');
                                                    $formattedDeliveredQty = rtrim(rtrim($formatted, '0'), ',');
                                                }
                                            @endphp
                                            {{ $formattedDeliveredQty }} {{ $row->unit_name }}
                                        </td>
                                        <td>
                                            @php
                                                $remainingQty = $row->remaining_qty;
                                                if ($remainingQty == floor($remainingQty)) {
                                                    $formattedRemainingQty = number_format($remainingQty, 0, ',', '.');
                                                } else {
                                                    $formatted = number_format($remainingQty, 4, ',', '.');
                                                    $formattedRemainingQty = rtrim(rtrim($formatted, '0'), ',');
                                                }
                                            @endphp
                                            {{ $formattedRemainingQty }} {{ $row->unit_name }}
                                        </td>
                                        <td>
                                            @php
                                                $deliveryQtyValue = $row->quantity;
                                                if ($deliveryQtyValue == floor($deliveryQtyValue)) {
                                                    $formattedDeliveryQty = number_format($deliveryQtyValue, 0, ',', '.');
                                                } else {
                                                    $formatted = number_format($deliveryQtyValue, 4, ',', '.');
                                                    $formattedDeliveryQty = rtrim(rtrim($formatted, '0'), ',');
                                                }
                                            @endphp
                                            <input type="hidden"
                                                name="products[{{ $counter }}][sales_order_product_id]"
                                                value="{{ $row->id }}" />
                                            <input type="hidden" name="products[{{ $counter }}][product_id]"
                                                value="{{ $row->f_product_id }}" />
                                            <input type="hidden" name="products[{{ $counter }}][unit_id]"
                                                value="{{ $row->f_unit_id }}" />
                                            <input type="hidden" name="products[{{ $counter }}][so_qty]"
                                                value="{{ $row->quantity }}" />
                                            <input type="hidden"
                                                name="products[{{ $counter }}][delivered_qty_before]"
                                                value="{{ $row->delivered_qty }}" />
                                            <input type="hidden"
                                                name="products[{{ $counter }}][remaining_qty_before]"
                                                value="{{ $row->remaining_qty }}" />
                                            <input type="text" data-remaining="{{ $row->remaining_qty }}"
                                                value="{{ $formattedDeliveryQty }}" class="form-control delivery-qty-input numeric-input"
                                                name="products[{{ $counter }}][delivery_qty]" min="1"
                                                max="{{ $row->remaining_qty }}" />
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
            @endif
            <!-- Section 6: Additional Information -->
            <div class="section-header mt-12">
                <h6>Informasi Tambahan</h6>
                <hr class="mx-n6">
            </div>

            <!-- Row 1: Notes & Memo -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Catatan Pengiriman</label>
                    <textarea class="form-control" id="sales-delivery-notes" name="delivery_notes" rows="3"
                        placeholder="Catatan yang terlihat oleh customer"></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Memo Internal</label>
                    <textarea class="form-control" id="sales-delivery-memo" name="memo" rows="3"
                        placeholder="Catatan internal (tidak terlihat oleh customer)"></textarea>
                </div>
            </div>

            <!-- Row 2: Tags, Attachment -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tag</label>
                    <select class="global-select2 form-select" id="sales-delivery-tags" name="tags[]" multiple>
                        @foreach ($tags as $tag)
                            <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">Pilih tag untuk kategorisasi (opsional)</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Lampiran</label>
                    <input type="file" class="form-control" id="sales-delivery-attachment" name="attachment"
                        accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" />
                    <small class="form-text text-muted">Format: PDF, DOC, XLS, JPG, PNG (Max: 10MB)</small>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="row mt-6">
                <div class="col-12">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('main_transaction.sales_delivery.index') }}" class="btn btn-outline-secondary">
                            <i class="icon-base ti tabler-arrow-left me-1"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-primary" id="submit-btn">
                            <i class="icon-base ti tabler-device-floppy me-1"></i>Simpan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@stop

@section('js_script')
    <script>
        $(document).ready(function() {
            initCleaveCurrency(document);

            let productLineIndex = 0;

            // Generate transaction number
            $('#generate-sd-number-btn').click(function() {
                $.ajax({
                    url: "{{ route('main_transaction.sales_delivery.generate_transaction_number') }}",
                    type: 'GET',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#sales-delivery-reference').val(response.transaction_number);
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error!', 'Gagal generate nomor transaksi', 'error');
                    }
                });
            });

            // Auto-generate transaction number on page load
            $('#generate-sd-number-btn').click();

            onSalesListSelected($('#sales-delivery-so'));

            function onSalesListSelected(currthis) {
                const selectedOption = $(currthis).find('option:selected');
                if (selectedOption.val()) {
                    // Auto-fill customer from selected SO
                    const customerId = selectedOption.data('customer-id');
                    const customerName = selectedOption.data('customer-name');
                    const customerEmail = selectedOption.data('customer-email');
                    const customerAddress = selectedOption.data('customer-address');
                    const warehouseId = selectedOption.data('warehouse-id');

                    $('#sales-delivery-customer').val(customerId).trigger('change');
                    $('#sales-delivery-email').val(customerEmail || '');
                    $('#sales-delivery-customer-address').val(customerAddress || '');
                    $('#sales-delivery-address').val(customerAddress || '');

                    $('#sales-delivery-warehouse').val(warehouseId).trigger('change');
                } else {
                    // Clear customer selection
                    $('#sales-delivery-customer').val('').trigger('change');
                    $('#sales-delivery-email').val('');
                    $('#sales-delivery-customer-address').val('');
                }
            }
            // Sales Order selection handler
            $('#sales-delivery-so').change(function() {
                onSalesListSelected(this);
            });

            const selectedOptionCust = $('#sales-delivery-customer').find('option:selected');
            const cust_email = selectedOptionCust.data('email') || '';
            const cust_address = selectedOptionCust.data('address') || '';

            $('#sales-delivery-email').val(cust_email);
            $('#sales-delivery-customer-address').val(cust_address);
            $('#sales-delivery-address').val(cust_address);

            // Product line management
            // $('#add-product-line-btn').click(function() {
            //     addProductLine();
            // });

            // $(document).on('click', '.delete-line-btn', function() {
            //     $(this).closest('tr').remove();
            //     updateProductLineIndexes();
            // });

            // $(document).on('change', '.product-select', function() {
            //     const selectedOption = $(this).find('option:selected');
            //     const unit = selectedOption.data('unit') || '';
            //     $(this).closest('tr').find('.product-unit').val(unit);
            // });

            // function addProductLine(productData = null) {
            //     const template = $('#product-line-template').clone();
            //     template.attr('id', `product-line-${productLineIndex}`);
            //     template.show();

            //     // Update indexes
            //     template.find('[name*="INDEX"]').each(function() {
            //         const name = $(this).attr('name').replace('INDEX', productLineIndex);
            //         $(this).attr('name', name);
            //     });

            //     // Pre-fill data if from SO
            //     if (productData) {
            //         template.find('.product-select').val(productData.product_id);
            //         template.find('.product-unit').val(productData.unit_name);
            //         template.find('.qty-ordered').val(productData.quantity);
            //         template.find('.qty-delivered').val(productData.quantity); // Default to same as ordered
            //     }

            //     $('#product-lines-table tbody').append(template);
            //     productLineIndex++;

            //     // Initialize select2 for new row
            //     template.find('.product-select, .delivery-status').select2({
            //         theme: 'bootstrap',
            //         width: '100%'
            //     });
            // }

            // function updateProductLineIndexes() {
            //     let index = 0;
            //     $('#product-lines-table tbody tr:visible').each(function() {
            //         if ($(this).attr('id') !== 'product-line-template') {
            //             $(this).find('[name*="products["]').each(function() {
            //                 const name = $(this).attr('name').replace(/products\[\d+\]/,
            //                     `products[${index}]`);
            //                 $(this).attr('name', name);
            //             });
            //             index++;
            //         }
            //     });
            //     productLineIndex = index;
            // }

            // function loadSalesOrderProducts(salesOrderId) {
            //     // Clear existing product lines
            //     clearProductLines();

            //     // This would be an AJAX call to get SO products
            //     // For now, add one empty line
            //     addProductLine();
            // }

            // function clearProductLines() {
            //     $('#product-lines-table tbody tr:not(#product-line-template)').remove();
            //     productLineIndex = 0;
            // }

            // Add initial product line if no SO selected
            // if (!$('#sales-delivery-so').val()) {
            //     addProductLine();
            // }

            // Form submission
            $('#sales-delivery-form').submit(function(e) {
                e.preventDefault();

                const formData = new FormData(this);


                // Append all disabled select values to formData
                $('#sales-delivery-form select:disabled').each(function() {
                    const name = $(this).attr('name');
                    const value = $(this).val();
                    if (name && value !== null) {
                        formData.append(name, value);
                    }
                });

                // Append disabled input values (e.g. shipping_cost, customer_reference)
                $('#sales-delivery-form input:disabled[name]').each(function() {
                    const name = $(this).attr('name');
                    const value = $(this).val();
                    if (name && value !== null && value !== '') {
                        const parsed = name === 'shipping_cost' ? parseCurrency(value) : value;
                        formData.append(name, parsed);
                    }
                });

                // Append parsed delivery qty for each product line
                $('.delivery-qty-input').each(function(index) {
                    const name = $(this).attr('name');
                    const rawValue = $(this).val();
                    const parsedValue = parseCurrency(rawValue);
                    if (name) {
                        formData.set(name, parsedValue); // overwrite with parsed numeric value
                    }
                });

                // Show confirmation
                Swal.fire({
                    title: 'Buat Sales Delivery?',
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
                        // Show loading state
                        $('#submit-btn').prop('disabled', true).html(
                            '<span class="spinner-border spinner-border-sm me-2"></span>Loading...'
                        );
                        $.ajax({
                            url: "{{ route('main_transaction.sales_delivery.create') }}",
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.message,
                                    showConfirmButton: true
                                }).then((result) => {
                                    window.location.href =
                                        "{{ route('main_transaction.sales_delivery.index') }}";
                                });
                            },
                            error: function(xhr) {
                                let errorMessage =
                                    'Terjadi kesalahan saat menyimpan data.';

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
            });
            //END

            //JS for product table when SO is selected
            $('.delivery-qty-input').on('input', function() {
                attachQuantityListeners($(this));
                // updateSummary();
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
                    const statusClass = originalRemaining > 0 ? 'bg-label-warning' : 'bg-label-warning';
                    const statusText = originalRemaining > 0 ? 'Available' : 'Complete';
                    statusCell.html(`<span class="badge ${statusClass}">${statusText}</span>`);
                }
            }
            //END
        });
    </script>
@endsection
