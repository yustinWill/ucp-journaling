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
                            <select class="global-select2 form-select" id="sales-delivery-so" name="sales_order_id" data-allow-clear="true">
                                <option value="">Pilih Sales Order (atau kosongkan untuk direct delivery)</option>
                                @if($selectedSalesOrder)
                                    <option value="{{ $selectedSalesOrder['id'] }}" selected
                                        data-customer-id="{{ $selectedSalesOrder['customer_id'] }}"
                                        data-customer-name="{{ $selectedSalesOrder['customer_name'] ?? '' }}"
                                        data-customer-email="{{ $selectedSalesOrder['customer_email'] ?? '' }}"
                                        data-customer-address="{{ $selectedSalesOrder['customer_address'] ?? '' }}">
                                        {{ $selectedSalesOrder['reference_no'] }} - {{ $selectedSalesOrder['customer_name'] ?? 'Unknown' }}
                                    </option>
                                @endif
                                @foreach ($salesOrders as $salesOrder)
                                    <option value="{{ $salesOrder->id }}" 
                                        data-customer-id="{{ $salesOrder->customer_id }}"
                                        data-customer-name="{{ $salesOrder->customer_name }}"
                                        data-customer-email="{{ $salesOrder->customer_email ?? '' }}"
                                        data-customer-address="{{ $salesOrder->customer_address ?? '' }}">
                                        {{ $salesOrder->reference_no }} - {{ $salesOrder->customer_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('main_transaction.sales_order.add') }}" target="_blank"
                                class="btn btn-outline-primary d-flex align-items-center"
                                title="Tambah Sales Order">
                                <i class="icon-base ti tabler-plus"></i>
                            </a>
                        </div>
                    </div>
                    <small class="form-text text-muted">Pilih Sales Order untuk delivery dari order yang ada, atau kosongkan untuk direct delivery</small>
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
                            <select class="global-select2 form-select" id="sales-delivery-customer" name="customer_id" data-allow-clear="false" required>
                                <option value="">Pilih Customer</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}" 
                                        data-email="{{ $customer->email ?? '' }}"
                                        data-address="{{ $customer->billing_address ?? '' }}">
                                        {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('master_data.contact.add') }}" target="_blank"
                                class="btn btn-outline-primary d-flex align-items-center"
                                title="Tambah Customer">
                                <i class="icon-base ti tabler-plus"></i>
                            </a>
                        </div>
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
                            <select class="global-select2 form-select" id="sales-delivery-warehouse" name="warehouse_id" data-allow-clear="false" required>
                                <option value="">Pilih Gudang</option>
                                @foreach ($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}">{{ $warehouse->code }} - {{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('master_data.warehouse.add') }}" target="_blank"
                                class="btn btn-outline-primary d-flex align-items-center"
                                title="Tambah Gudang">
                                <i class="icon-base ti tabler-plus"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="sales-delivery-status" name="status" data-allow-clear="false" required>
                        <option value="">Pilih Status</option>
                        <option value="PENDING" selected>PENDING - Menunggu</option>
                        <option value="IN_TRANSIT">IN_TRANSIT - Dalam Perjalanan</option>
                        <option value="DELIVERED">DELIVERED - Terkirim</option>
                        <option value="CANCELLED">CANCELLED - Dibatalkan</option>
                        <option value="RETURNED">RETURNED - Dikembalikan</option>
                    </select>
                </div>
            </div>

            <!-- Row 2: Customer Reference -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Referensi Customer</label>
                    <input type="text" class="form-control" id="sales-delivery-customer-ref" name="customer_reference"
                        placeholder="Nomor referensi dari customer (PO Number, dll)" />
                </div>
            </div>

            <!-- Section 5: Product Lines -->
            <div class="section-header mt-12">
                <h6>Detail Produk</h6>
            </div>
            <div class="table-responsive">
                <table id="product-lines-table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 200px;">Produk <span style="color: red;">*</span></th>
                            <th style="width: 120px;">Qty Dipesan</th>
                            <th style="width: 120px;">Qty Dikirim <span style="color: red;">*</span></th>
                            <th style="width: 80px;">Unit</th>
                            <th style="width: 120px;">Status</th>
                            <th style="width: 200px;">Keterangan</th>
                            <th style="width: 80px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr id="product-line-template" style="display: none;">
                            <td>
                                <select class="form-select product-select" name="products[INDEX][product_id]" required>
                                    <option value="">Pilih Produk</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}" 
                                            data-unit="{{ $product->unit_name }}">
                                            {{ $product->code }} - {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number" class="form-control qty-ordered" name="products[INDEX][quantity_ordered]" 
                                    step="0.01" min="0" value="0" disabled />
                            </td>
                            <td>
                                <input type="number" class="form-control qty-delivered" name="products[INDEX][quantity_delivered]" 
                                    step="0.01" min="0" required />
                            </td>
                            <td>
                                <input type="text" class="form-control product-unit" disabled />
                            </td>
                            <td>
                                <select class="form-select delivery-status" name="products[INDEX][delivery_status]">
                                    <option value="PENDING" selected>PENDING</option>
                                    <option value="DELIVERED">DELIVERED</option>
                                    <option value="PARTIAL">PARTIAL</option>
                                    <option value="CANCELLED">CANCELLED</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control" name="products[INDEX][description]" placeholder="Keterangan" />
                            </td>
                            <td>
                                <button type="button" class="btn btn-outline-danger btn-sm delete-line-btn">
                                    <i class="icon-base ti tabler-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="mt-3">
                    <button type="button" id="add-product-line-btn" class="btn btn-outline-primary">
                        <i class="icon-base ti tabler-plus me-1"></i>Tambah Produk
                    </button>
                </div>
            </div>

            <!-- Section 6: Additional Information -->
            <div class="section-header mt-12">
                <h6>Informasi Tambahan</h6>
                <hr class="mx-n6">
            </div>
            
            <!-- Row 1: Notes -->
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
    let productLineIndex = 0;

    // Generate transaction number
    $('#generate-sd-number-btn').click(function() {
        $.ajax({
            url: "{{ route('main_transaction.sales_delivery.generate_transaction_number') }}",
            type: 'POST',
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
    
    // Auto-trigger SO selection if preselected (coming from SO detail)
    @if($preselectedSOId && $selectedSalesOrder)
        // Trigger the change event to auto-fill customer data
        $('#sales-delivery-so').trigger('change');
    @endif

    // Sales Order selection handler
    $('#sales-delivery-so').change(function() {
        const selectedOption = $(this).find('option:selected');
        if (selectedOption.val()) {
            // Auto-fill customer from selected SO
            const customerId = selectedOption.data('customer-id');
            const customerName = selectedOption.data('customer-name');
            const customerEmail = selectedOption.data('customer-email');
            const customerAddress = selectedOption.data('customer-address');
            
            $('#sales-delivery-customer').val(customerId).trigger('change');
            $('#sales-delivery-email').val(customerEmail || '');
            $('#sales-delivery-customer-address').val(customerAddress || '');
            
            // Load SO products
            loadSalesOrderProducts(selectedOption.val());
        } else {
            // Clear customer selection
            $('#sales-delivery-customer').val('').trigger('change');
            $('#sales-delivery-email').val('');
            $('#sales-delivery-customer-address').val('');
            
            // Clear product lines
            clearProductLines();
        }
    });

    // Customer selection handler
    $('#sales-delivery-customer').change(function() {
        const selectedOption = $(this).find('option:selected');
        const email = selectedOption.data('email') || '';
        const address = selectedOption.data('address') || '';
        
        $('#sales-delivery-email').val(email);
        $('#sales-delivery-customer-address').val(address);
    });

    // Product line management
    $('#add-product-line-btn').click(function() {
        addProductLine();
    });

    $(document).on('click', '.delete-line-btn', function() {
        $(this).closest('tr').remove();
        updateProductLineIndexes();
    });

    $(document).on('change', '.product-select', function() {
        const selectedOption = $(this).find('option:selected');
        const unit = selectedOption.data('unit') || '';
        $(this).closest('tr').find('.product-unit').val(unit);
    });

    function addProductLine(productData = null) {
        const template = $('#product-line-template').clone();
        template.attr('id', `product-line-${productLineIndex}`);
        template.show();
        
        // Update indexes
        template.find('[name*="INDEX"]').each(function() {
            const name = $(this).attr('name').replace('INDEX', productLineIndex);
            $(this).attr('name', name);
        });
        
        // Pre-fill data if from SO
        if (productData) {
            template.find('.product-select').val(productData.product_id);
            template.find('.product-unit').val(productData.unit_name);
            template.find('.qty-ordered').val(productData.quantity);
            template.find('.qty-delivered').val(productData.quantity); // Default to same as ordered
        }
        
        $('#product-lines-table tbody').append(template);
        productLineIndex++;
        
        // Initialize select2 for new row
        template.find('.product-select, .delivery-status').select2({
            theme: 'bootstrap',
            width: '100%'
        });
    }

    function updateProductLineIndexes() {
        let index = 0;
        $('#product-lines-table tbody tr:visible').each(function() {
            if ($(this).attr('id') !== 'product-line-template') {
                $(this).find('[name*="products["]').each(function() {
                    const name = $(this).attr('name').replace(/products\[\d+\]/, `products[${index}]`);
                    $(this).attr('name', name);
                });
                index++;
            }
        });
        productLineIndex = index;
    }

    function loadSalesOrderProducts(salesOrderId) {
        // Clear existing product lines
        clearProductLines();
        
        // This would be an AJAX call to get SO products
        // For now, add one empty line
        addProductLine();
    }

    function clearProductLines() {
        $('#product-lines-table tbody tr:not(#product-line-template)').remove();
        productLineIndex = 0;
    }

    // Add initial product line if no SO selected
    if (!$('#sales-delivery-so').val()) {
        addProductLine();
    }

    // Form submission
    $('#sales-delivery-form').submit(function(e) {
        e.preventDefault();
        
        // Validate at least one product line
        const productLines = $('#product-lines-table tbody tr:visible').length - 1; // Excluding template
        if (productLines === 0) {
            Swal.fire('Error!', 'Minimal harus ada satu produk', 'error');
            return;
        }
        
        const formData = new FormData(this);
        
        $.ajax({
            url: "{{ route('main_transaction.sales_delivery.create') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        showConfirmButton: true
                    }).then((result) => {
                        window.location.href = "{{ route('main_transaction.sales_delivery.index') }}";
                    });
                } else {
                    Swal.fire('Error!', response.message, 'error');
                }
            },
            error: function(xhr) {
                let message = 'Terjadi kesalahan';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    message = errors.join(', ');
                }
                Swal.fire('Error!', message, 'error');
            }
        });
    });
});
</script>
@endsection