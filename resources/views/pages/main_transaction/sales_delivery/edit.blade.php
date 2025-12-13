@extends('layouts.default')
@section('page_title')
    Edit Sales Delivery
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Edit Sales Delivery</h4>
        <form class="card-body" id="sales-delivery-form" autocomplete="off">
            @csrf
            <input type="hidden" name="id" value="{{ $salesDelivery->id }}" />
            
            <!-- Section 1: Basic Transaction Information -->
            <div class="section-header mt-12">
                <h6>Informasi Dasar Transaksi</h6>
                <hr class="mx-n6">
            </div>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tanggal Pengiriman <span style="color: red;">*</span></label>
                    <input type="date" class="form-control global-single-date-picker" id="sales-delivery-date"
                        name="delivery_date" value="{{ $salesDelivery->delivery_date }}" placeholder="YYYY-MM-DD" required />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nomor Transaksi <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="sales-delivery-reference" name="delivery_number"
                        value="{{ $salesDelivery->delivery_number }}" readonly />
                </div>
            </div>

            <!-- Section 2: Sales Order Reference (Read Only in Edit) -->
            @if($salesDelivery->sales_order_id)
            <div class="section-header mt-12">
                <h6>Sales Order</h6>
                <hr class="mx-n6">
            </div>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Sales Order</label>
                    <input type="text" class="form-control" 
                        value="{{ $salesOrder->reference_no ?? 'N/A' }} - {{ $salesOrder->customer_name ?? '' }}" readonly />
                    <input type="hidden" name="sales_order_id" value="{{ $salesDelivery->sales_order_id }}" />
                </div>
            </div>
            @endif

            <!-- Section 3: Customer Information (Read Only in Edit) -->
            <div class="section-header mt-12">
                <h6>Informasi Customer</h6>
                <hr class="mx-n6">
            </div>
            
            <!-- Row 1: Customer -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Customer</label>
                    <input type="text" class="form-control" 
                        value="{{ $customer->name ?? 'N/A' }}" readonly />
                    <input type="hidden" name="customer_id" value="{{ $salesDelivery->customer_id }}" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email Customer</label>
                    <input type="email" class="form-control" value="{{ $customer->email ?? '' }}" readonly />
                </div>
            </div>

            <!-- Row 2: Customer Address -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Customer Address</label>
                    <textarea class="form-control" rows="2" readonly>{{ $customer->address ?? '' }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Alamat Pengiriman</label>
                    <textarea class="form-control" id="sales-delivery-address" name="delivery_address" rows="2" 
                        placeholder="Alamat pengiriman (jika berbeda dari alamat customer)">{{ $salesDelivery->delivery_address ?? '' }}</textarea>
                </div>
            </div>

            <!-- Section 4: Delivery Information -->
            <div class="section-header mt-12">
                <h6>Informasi Pengiriman</h6>
                <hr class="mx-n6">
            </div>
            
            <!-- Row 1: Warehouse (Read Only), Status -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Gudang</label>
                    <input type="text" class="form-control" 
                        value="{{ $warehouse->name ?? 'N/A' }}" readonly />
                    <input type="hidden" name="warehouse_id" value="{{ $salesDelivery->warehouse_id }}" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="sales-delivery-status" name="status" data-allow-clear="false" required>
                        <option value="PENDING" {{ $salesDelivery->status === 'PENDING' ? 'selected' : '' }}>PENDING - Menunggu</option>
                        <option value="IN_TRANSIT" {{ $salesDelivery->status === 'IN_TRANSIT' ? 'selected' : '' }}>IN_TRANSIT - Dalam Perjalanan</option>
                        <option value="DELIVERED" {{ $salesDelivery->status === 'DELIVERED' ? 'selected' : '' }}>DELIVERED - Terkirim</option>
                        <option value="CANCELLED" {{ $salesDelivery->status === 'CANCELLED' ? 'selected' : '' }}>CANCELLED - Dibatalkan</option>
                        <option value="RETURNED" {{ $salesDelivery->status === 'RETURNED' ? 'selected' : '' }}>RETURNED - Dikembalikan</option>
                    </select>
                </div>
            </div>

            <!-- Row 2: Customer Reference -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Nomor Resi Pengiriman</label>
                    <input type="text" class="form-control" id="sales-delivery-customer-ref" name="customer_reference"
                        value="{{ $salesDelivery->customer_reference ?? '' }}"
                        placeholder="Nomor Resi Pengiriman" />
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
                            <th style="width: 200px;">Produk</th>
                            <th style="width: 120px;">Qty Dipesan</th>
                            <th style="width: 120px;">Qty Dikirim <span style="color: red;">*</span></th>
                            <th style="width: 80px;">Unit</th>
                            <th style="width: 120px;">Status</th>
                            <th style="width: 200px;">Keterangan</th>
                            @if(!in_array($salesDelivery->status, ['DELIVERED', 'CANCELLED']))
                            <th style="width: 80px;">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($productLines as $index => $line)
                        <tr id="product-line-{{ $index }}">
                            <td>
                                @if(in_array($salesDelivery->status, ['DELIVERED', 'CANCELLED']))
                                    <input type="text" class="form-control" 
                                        value="{{ $line['product_code'] }} - {{ $line['product_name'] }}" readonly />
                                    <input type="hidden" name="products[{{ $index }}][product_id]" value="{{ $line['product_id'] }}" />
                                @else
                                    <select class="global-select2 form-select product-select" name="products[{{ $index }}][product_id]" required>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}" 
                                                data-unit="{{ $product->unit_name }}"
                                                {{ $product->id == $line['product_id'] ? 'selected' : '' }}>
                                                {{ $product->code }} - {{ $product->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                            </td>
                            <td>
                                <input type="number" class="form-control qty-ordered" 
                                    value="{{ $line['quantity_ordered'] }}" 
                                    name="products[{{ $index }}][quantity_ordered]" 
                                    step="0.01" min="0" disabled />
                            </td>
                            <td>
                                <input type="number" class="form-control qty-delivered" 
                                    name="products[{{ $index }}][quantity_delivered]" 
                                    value="{{ $line['quantity_delivered'] }}"
                                    step="0.01" min="0" required 
                                    {{ in_array($salesDelivery->status, ['DELIVERED', 'CANCELLED']) ? 'readonly' : '' }} />
                            </td>
                            <td>
                                <input type="text" class="form-control product-unit" 
                                    value="{{ $line['unit_name'] ?? '' }}" disabled />
                            </td>
                            <td>
                                @if(in_array($salesDelivery->status, ['DELIVERED', 'CANCELLED']))
                                    <input type="text" class="form-control" value="{{ $line['delivery_status'] }}" readonly />
                                    <input type="hidden" name="products[{{ $index }}][delivery_status]" value="{{ $line['delivery_status'] }}" />
                                @else
                                    <select class="global-select2 form-select delivery-status" name="products[{{ $index }}][delivery_status]">
                                        <option value="PENDING" {{ $line['delivery_status'] === 'PENDING' ? 'selected' : '' }}>PENDING</option>
                                        <option value="DELIVERED" {{ $line['delivery_status'] === 'DELIVERED' ? 'selected' : '' }}>DELIVERED</option>
                                        <option value="PARTIAL" {{ $line['delivery_status'] === 'PARTIAL' ? 'selected' : '' }}>PARTIAL</option>
                                        <option value="CANCELLED" {{ $line['delivery_status'] === 'CANCELLED' ? 'selected' : '' }}>CANCELLED</option>
                                    </select>
                                @endif
                            </td>
                            <td>
                                <input type="text" class="form-control" name="products[{{ $index }}][description]" 
                                    value="{{ $line['description'] ?? '' }}" placeholder="Keterangan"
                                    {{ in_array($salesDelivery->status, ['DELIVERED', 'CANCELLED']) ? 'readonly' : '' }} />
                            </td>
                            @if(!in_array($salesDelivery->status, ['DELIVERED', 'CANCELLED']))
                            <td>
                                <button type="button" class="btn btn-outline-danger btn-sm delete-line-btn">
                                    <i class="icon-base ti tabler-trash"></i>
                                </button>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                        
                        @if(!in_array($salesDelivery->status, ['DELIVERED', 'CANCELLED']))
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
                        @endif
                    </tbody>
                </table>
                
                @if(!in_array($salesDelivery->status, ['DELIVERED', 'CANCELLED']))
                <div class="mt-3">
                    <button type="button" id="add-product-line-btn" class="btn btn-outline-primary">
                        <i class="icon-base ti tabler-plus me-1"></i>Tambah Produk
                    </button>
                </div>
                @endif
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
                        placeholder="Catatan yang terlihat oleh customer">{{ $salesDelivery->delivery_notes ?? '' }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Memo Internal</label>
                    <textarea class="form-control" id="sales-delivery-memo" name="memo" rows="3"
                        placeholder="Catatan internal (tidak terlihat oleh customer)">{{ $salesDelivery->memo ?? '' }}</textarea>
                </div>
            </div>

            <!-- Row 2: Tags, Attachment -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tag</label>
                    <select class="global-select2 form-select" id="sales-delivery-tags" name="tags[]" multiple>
                        @foreach ($tags as $tag)
                            <option value="{{ $tag->id }}" 
                                {{ in_array($tag->id, $selectedTags ?? []) ? 'selected' : '' }}>
                                {{ $tag->name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">Pilih tag untuk kategorisasi (opsional)</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Lampiran</label>
                    @if($salesDelivery->attachment_path)
                        <div class="mb-2">
                            <a href="{{ Storage::url($salesDelivery->attachment_path) }}" target="_blank" class="btn btn-outline-info btn-sm">
                                <i class="icon-base ti tabler-download me-1"></i>Download Current
                            </a>
                        </div>
                    @endif
                    <input type="file" class="form-control" id="sales-delivery-attachment" name="attachment"
                        accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" />
                    <small class="form-text text-muted">Format: PDF, DOC, XLS, JPG, PNG (Max: 10MB) - Kosongkan jika tidak ingin mengganti</small>
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
                            <i class="icon-base ti tabler-device-floppy me-1"></i>Update
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
    let productLineIndex = {{ count($productLines) }};

    // Product line management (only if status allows editing)
    @if(!in_array($salesDelivery->status, ['DELIVERED', 'CANCELLED']))
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

    function addProductLine() {
        const template = $('#product-line-template').clone();
        template.attr('id', `product-line-${productLineIndex}`);
        template.show();
        
        // Update indexes
        template.find('[name*="INDEX"]').each(function() {
            const name = $(this).attr('name').replace('INDEX', productLineIndex);
            $(this).attr('name', name);
        });
        
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
    @endif

    // Form submission
    $('#sales-delivery-form').submit(function(e) {
        e.preventDefault();
        
        // Validate at least one product line
        const productLines = $('#product-lines-table tbody tr:visible').length - 
            @if(!in_array($salesDelivery->status, ['DELIVERED', 'CANCELLED'])) 1 @else 0 @endif; // Excluding template if exists
            
        if (productLines === 0) {
            Swal.fire('Error!', 'Minimal harus ada satu produk', 'error');
            return;
        }
        
        const formData = new FormData(this);
        
        $.ajax({
            url: "{{ route('main_transaction.sales_delivery.update') }}",
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
                        window.location.href = "{{ route('main_transaction.sales_delivery.detail', $salesDelivery->id) }}";
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