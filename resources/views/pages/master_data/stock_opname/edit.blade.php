@extends('layouts.default')
@section('page_title')
    Edit Stock Opname - {{ $stockOpname['opname_number'] }}
@stop
@section('content')
    <div class="card mb-6">
        <h5 class="card-header">Edit Stock Opname - {{ $stockOpname['opname_number'] }}</h5>
        <form class="card-body" id="stockOpnameForm">
            <!-- Informasi Dasar Section -->
            <div class="section-header mt-12">
                <h6>Informasi Dasar</h6>
                <hr class="mx-n6">
            </div>
            
            <!-- Row 1: Basic Information -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Nomor Stock Opname <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="opname-number" value="{{ $stockOpname['opname_number'] }}" maxlength="50" readonly />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Opname <span style="color: red;">*</span></label>
                    <input type="date" class="form-control global-single-date-picker" id="opname-date" value="{{ $stockOpname['opname_date'] }}" />
                </div>
            </div>
            
            <!-- Row 2: Warehouse and PIC -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Gudang <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="warehouse-select" data-allow-clear="false">
                        <option value="">Pilih Gudang</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse['value'] }}" @if(($stockOpname['warehouse_code'] ?? '') == $warehouse['value']) selected @endif>{{ $warehouse['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Penanggung Jawab <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="pic-select" data-allow-clear="false">
                        <option value="">Pilih PIC</option>
                        @foreach($picOptions as $pic)
                            <option value="{{ $pic['value'] }}" @if(($stockOpname['pic_name'] ?? '') == $pic['value']) selected @endif>{{ $pic['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <!-- Row 3: Status and Description -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Status <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="status-select" data-allow-clear="false" 
                            @if(in_array($stockOpname['status'] ?? '', ['adjustment_created', 'completed'])) disabled @endif>
                        <option value="">Pilih Status</option>
                        @foreach($statusOptions as $status)
                            <option value="{{ $status['value'] }}" @if(($stockOpname['status'] ?? '') == $status['value']) selected @endif>{{ $status['label'] }}</option>
                        @endforeach
                    </select>
                    @if(in_array($stockOpname['status'] ?? '', ['adjustment_created', 'completed']))
                        <small class="form-text text-warning">
                            <i class="icon-base ti tabler-info-circle me-1"></i>
                            Status tidak dapat diubah setelah adjustment dibuat
                        </small>
                    @endif
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kategori Produk Filter</label>
                    <select class="global-select2 form-select" id="product-category-filter" data-allow-clear="false">
                        <option value="">Semua Kategori</option>
                        @foreach($productCategories as $category)
                            <option value="{{ $category['value'] }}">{{ $category['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <!-- Row 4: Description -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Deskripsi</label>
                    <textarea class="form-control" id="description" rows="3" placeholder="Deskripsi stock opname (opsional)" maxlength="500">{{ $stockOpname['description'] ?? '' }}</textarea>
                    <small class="form-text text-muted">Maksimal 500 karakter</small>
                </div>
            </div>

            <!-- Produk Section -->
            <div class="section-header mt-12">
                <h6>Daftar Produk</h6>
                <hr class="mx-n6">
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Produk untuk Stock Opname</h6>
                        @if(!in_array($stockOpname['status'] ?? '', ['adjustment_created', 'completed']))
                            <button type="button" class="btn btn-md btn-primary waves-effect waves-light" id="load-products-btn">
                                <i class="icon-base ti tabler-refresh me-2"></i>
                                Muat Produk dari Gudang
                            </button>
                        @else
                            <span class="badge bg-label-warning">
                                <i class="icon-base ti tabler-lock me-1"></i>
                                Terkunci - Adjustment sudah dibuat
                            </span>
                        @endif
                    </div>
                    
                    <div class="mx-n5">
                        <table class="table table-bordered" id="products-table">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="select-all-products">
                                        </div>
                                    </th>
                                    <th style="width: 15%;">Kode Produk</th>
                                    <th style="width: 25%;">Nama Produk</th>
                                    <th style="width: 10%;">Unit</th>
                                    <th style="width: 10%;">Stok Sistem</th>
                                    <th style="width: 10%;">Qty Fisik</th>
                                    <th style="width: 10%;">Selisih</th>
                                    <th style="width: 15%;">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody id="products-tbody">
                                <!-- Pre-loaded products with sample data -->
                                <tr data-product-id="1">
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input product-checkbox" type="checkbox" value="1" checked>
                                        </div>
                                    </td>
                                    <td>AIR-330</td>
                                    <td>RIYO Water 330ml</td>
                                    <td>pcs</td>
                                    <td class="text-end">1,500</td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm physical-qty" 
                                               data-product-id="1" 
                                               data-system-stock="1500"
                                               value="1502" min="0"
                                               @if(in_array($stockOpname['status'] ?? '', ['adjustment_created', 'completed'])) readonly @endif>
                                    </td>
                                    <td class="text-end variance-display">
                                        <span class="text-success">+2</span>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm notes" 
                                               data-product-id="1" 
                                               value="Ada penambahan dari produksi"
                                               placeholder="Catatan"
                                               @if(in_array($stockOpname['status'] ?? '', ['adjustment_created', 'completed'])) readonly @endif>
                                    </td>
                                </tr>
                                <tr data-product-id="2">
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input product-checkbox" type="checkbox" value="2" checked>
                                        </div>
                                    </td>
                                    <td>AIR-600</td>
                                    <td>RIYO Water 600ml</td>
                                    <td>pcs</td>
                                    <td class="text-end">800</td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm physical-qty" 
                                               data-product-id="2" 
                                               data-system-stock="800"
                                               value="800" min="0"
                                               @if(in_array($stockOpname['status'] ?? '', ['adjustment_created', 'completed'])) readonly @endif>
                                    </td>
                                    <td class="text-end variance-display">
                                        <span class="text-muted">0</span>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm notes" 
                                               data-product-id="2" 
                                               value="Sesuai dengan sistem"
                                               placeholder="Catatan"
                                               @if(in_array($stockOpname['status'] ?? '', ['adjustment_created', 'completed'])) readonly @endif>
                                    </td>
                                </tr>
                                <tr data-product-id="3">
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input product-checkbox" type="checkbox" value="3" checked>
                                        </div>
                                    </td>
                                    <td>AIR-1500</td>
                                    <td>RIYO Water 1500ml</td>
                                    <td>pcs</td>
                                    <td class="text-end">400</td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm physical-qty" 
                                               data-product-id="3" 
                                               data-system-stock="400"
                                               value="400" min="0"
                                               @if(in_array($stockOpname['status'] ?? '', ['adjustment_created', 'completed'])) readonly @endif>
                                    </td>
                                    <td class="text-end variance-display">
                                        <span class="text-muted">0</span>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm notes" 
                                               data-product-id="3" 
                                               value=""
                                               placeholder="Catatan"
                                               @if(in_array($stockOpname['status'] ?? '', ['adjustment_created', 'completed'])) readonly @endif>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Summary Section -->
            <div class="section-header mt-12">
                <h6>Ringkasan</h6>
                <hr class="mx-n6">
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h6 class="card-title">Total Produk</h6>
                            <h4 class="text-primary" id="total-products">3</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h6 class="card-title">Produk Terhitung</h6>
                            <h4 class="text-info" id="counted-products">3</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h6 class="card-title">Selisih Positif</h6>
                            <h4 class="text-success" id="positive-variance">+2</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h6 class="card-title">Selisih Negatif</h6>
                            <h4 class="text-danger" id="negative-variance">0</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 text-end">
                <button type="button" class="btn btn-label-danger me-3" id="cancel-btn">Batalkan</button>
                @if(in_array($stockOpname['status'] ?? '', ['adjustment_created', 'completed']))
                    <button type="button" class="btn btn-secondary" disabled>
                        <i class="icon-base ti tabler-lock me-2"></i>
                        Terkunci - Adjustment Sudah Dibuat
                    </button>
                @else
                    <button type="button" class="btn btn-success" id="submit-btn">Update Stock Opname</button>
                @endif
            </div>
        </form>
    </div>
@endsection

@section('js_script')
<script>
$(function() {
    // Products data loaded from controller
    const existingProducts = @json($stockOpnameProducts ?? []);
    
    // Initialize variance calculation on page load
    calculateVarianceAndSummary();
    
    // Clear products table when warehouse changes
    $('#warehouse-select').on('change', function() {
        clearProductsTable();
    });
    
    // Function to clear products table
    function clearProductsTable() {
        $('#products-tbody').html(`
            <tr>
                <td colspan="8" class="text-center text-muted">
                    <i class="icon-base ti tabler-box-off me-2"></i>
                    Pilih gudang dan klik "Muat Produk dari Gudang" untuk menampilkan produk
                </td>
            </tr>
        `);
        // Also clear the select all checkbox
        $('#select-all-products').prop('checked', false);
        // Reset summary cards
        $('#total-products').text('0');
        $('#counted-products').text('0');
        $('#positive-variance').text('0');
        $('#negative-variance').text('0');
    }
    
    // Load products based on warehouse and category filter
    $('#load-products-btn').on('click', function() {
        const warehouse = $('#warehouse-select').val();
        const categoryFilter = $('#product-category-filter').val();
        
        if (!warehouse) {
            Swal.fire({
                title: 'Peringatan!',
                text: 'Silakan pilih gudang terlebih dahulu',
                icon: 'warning',
                customClass: {
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false
            });
            return;
        }
        
        // Get products from server based on warehouse and category
        // In real implementation, this would be an AJAX call to get products
        // For now, using existing products from controller data
        let filteredProducts = existingProducts;
        if (categoryFilter) {
            filteredProducts = existingProducts.filter(product => product.category === categoryFilter);
        }
        
        // Generate table rows
        let tableRows = '';
        filteredProducts.forEach(product => {
            tableRows += `
                <tr data-product-id="${product.id}">
                    <td>
                        <div class="form-check">
                            <input class="form-check-input product-checkbox" type="checkbox" value="${product.id}">
                        </div>
                    </td>
                    <td>${product.code}</td>
                    <td>${product.name}</td>
                    <td>${product.unit}</td>
                    <td class="text-end">${product.system_stock.toLocaleString('id-ID')}</td>
                    <td>
                        <input type="number" class="form-control form-control-sm physical-qty" 
                               data-product-id="${product.id}" 
                               data-system-stock="${product.system_stock}"
                               placeholder="0" min="0">
                    </td>
                    <td class="text-end variance-display">-</td>
                    <td>
                        <input type="text" class="form-control form-control-sm notes" 
                               data-product-id="${product.id}" 
                               placeholder="Catatan">
                    </td>
                </tr>
            `;
        });
        
        $('#products-tbody').html(tableRows);
        bindProductEvents();
        
        // Show success message
        Swal.fire({
            title: 'Berhasil!',
            text: `${filteredProducts.length} produk berhasil dimuat dari gudang`,
            icon: 'success',
            timer: 2000,
            showConfirmButton: false
        });
    });
    
    // Calculate variance and summary
    function calculateVarianceAndSummary() {
        let totalProducts = 0;
        let countedProducts = 0;
        let positiveVariance = 0;
        let negativeVariance = 0;
        
        $('#products-tbody tr').each(function() {
            const row = $(this);
            const physicalQtyInput = row.find('.physical-qty');
            
            if (physicalQtyInput.length > 0) {
                totalProducts++;
                
                const physicalQty = parseInt(physicalQtyInput.val()) || 0;
                const systemStock = parseInt(physicalQtyInput.data('system-stock')) || 0;
                
                if (physicalQty > 0 || physicalQtyInput.val() !== '') {
                    countedProducts++;
                }
                
                const variance = physicalQty - systemStock;
                
                // Update variance display
                const varianceCell = row.find('.variance-display');
                if (variance > 0) {
                    varianceCell.html(`<span class="text-success">+${variance.toLocaleString('id-ID')}</span>`);
                    positiveVariance += variance;
                } else if (variance < 0) {
                    varianceCell.html(`<span class="text-danger">${variance.toLocaleString('id-ID')}</span>`);
                    negativeVariance += Math.abs(variance);
                } else {
                    varianceCell.html('<span class="text-muted">0</span>');
                }
            }
        });
        
        // Update summary cards
        $('#total-products').text(totalProducts);
        $('#counted-products').text(countedProducts);
        $('#positive-variance').text(positiveVariance > 0 ? `+${positiveVariance.toLocaleString('id-ID')}` : '0');
        $('#negative-variance').text(negativeVariance > 0 ? `-${negativeVariance.toLocaleString('id-ID')}` : '0');
    }
    
    // Bind product events
    function bindProductEvents() {
        // Physical quantity changes
        $('.physical-qty').on('input', function() {
            calculateVarianceAndSummary();
        });
        
        // Product checkbox changes
        $('.product-checkbox').on('change', function() {
            const totalCheckboxes = $('.product-checkbox').length;
            const checkedCheckboxes = $('.product-checkbox:checked').length;
            
            if (checkedCheckboxes === totalCheckboxes) {
                $('#select-all-products').prop('checked', true);
            } else {
                $('#select-all-products').prop('checked', false);
            }
        });
    }
    
    // Initialize events for existing products
    bindProductEvents();
    
    // Auto-calculate variance on input changes (removed manual calculate button)
    
    // Select all products checkbox
    $('#select-all-products').on('change', function() {
        $('.product-checkbox').prop('checked', $(this).prop('checked'));
    });
    
    // Form validation
    function validateForm() {
        let isValid = true;
        const requiredFields = ['opname-date', 'warehouse-select', 'pic-select', 'status-select'];
        
        requiredFields.forEach(function(fieldId) {
            const field = $('#' + fieldId);
            if (!field.val() || field.val().trim() === '') {
                field.addClass('is-invalid');
                isValid = false;
            } else {
                field.removeClass('is-invalid');
            }
        });
        
        return isValid;
    }
    
    // Submit form
    $('#submit-btn').on('click', function(e) {
        e.preventDefault();
        
        // Check if adjustment is already created
        const currentStatus = @json($stockOpname['status'] ?? '');
        if (currentStatus === 'adjustment_created' || currentStatus === 'completed') {
            Swal.fire({
                icon: 'warning',
                title: 'Tidak Dapat Diubah',
                text: 'Stock opname tidak dapat diubah karena adjustment sudah dibuat.',
                customClass: {
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false
            });
            return;
        }
        
        if (!validateForm()) {
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                text: 'Mohon lengkapi semua field yang wajib diisi.',
                customClass: {
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false
            });
            return;
        }
        
        // Collect form data
        const formData = {
            opname_number: $('#opname-number').val(),
            opname_date: $('#opname-date').val(),
            warehouse: $('#warehouse-select').val(),
            pic: $('#pic-select').val(),
            status: $('#status-select').val(),
            description: $('#description').val(),
            products: []
        };
        
        // Collect product data
        $('.product-checkbox:checked').each(function() {
            const productId = $(this).val();
            const row = $(this).closest('tr');
            const physicalQty = parseInt(row.find('.physical-qty').val()) || 0;
            const systemStock = parseInt(row.find('.physical-qty').data('system-stock'));
            const notes = row.find('.notes').val();
            
            formData.products.push({
                product_id: productId,
                system_stock: systemStock,
                physical_qty: physicalQty,
                variance: physicalQty - systemStock,
                notes: notes
            });
        });
        
        // Show confirmation
        Swal.fire({
            title: 'Konfirmasi Update',
            text: `Apakah Anda yakin ingin mengupdate stock opname dengan ${formData.products.length} produk?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Update',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                // In real implementation, send data to server
                console.log('Form data:', formData);
                
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Stock opname berhasil diupdate',
                    icon: 'success',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                }).then(() => {
                    window.location.href = "{{ route('master_data.stock_opname.index') }}";
                });
            }
        });
    });
    
    // Cancel button
    $('#cancel-btn').on('click', function() {
        Swal.fire({
            title: 'Konfirmasi Batal',
            text: 'Apakah Anda yakin ingin membatalkan perubahan?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Batalkan',
            cancelButtonText: 'Tidak',
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ route('master_data.stock_opname.index') }}";
            }
        });
    });
});
</script>
@endsection