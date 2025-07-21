@extends('layouts.default')
@section('page_title')
    Tambah Stock Opname Baru
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Tambah Stock Opname</h4>
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
                    <input type="text" class="form-control" id="opname-number" placeholder="SO-2025-001" maxlength="50" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Opname <span style="color: red;">*</span></label>
                    <input type="date" class="form-control global-single-date-picker" id="opname-date" />
                </div>
            </div>
            
            <!-- Row 2: Warehouse and PIC -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Gudang <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="warehouse-select" data-allow-clear="false">
                        <option value="">Pilih Gudang</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse['value'] }}">{{ $warehouse['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Penanggung Jawab <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="pic-select" data-allow-clear="false">
                        <option value="">Pilih PIC</option>
                        @foreach($picOptions as $pic)
                            <option value="{{ $pic['value'] }}">{{ $pic['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <!-- Row 3: Status and Description -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Status <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="status-select" data-allow-clear="false">
                        <option value="">Pilih Status</option>
                        @foreach($statusOptions as $status)
                            <option value="{{ $status['value'] }}">{{ $status['label'] }}</option>
                        @endforeach
                    </select>
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
                    <textarea class="form-control" id="description" rows="3" placeholder="Deskripsi stock opname (opsional)" maxlength="500"></textarea>
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
                        <button type="button" class="btn btn-md btn-primary waves-effect waves-light" id="load-products-btn">
                            <i class="icon-base ti tabler-refresh me-2"></i>
                            Muat Produk dari Gudang
                        </button>
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
                                <tr>
                                    <td colspan="8" class="text-center text-muted">
                                        <i class="icon-base ti tabler-box-off me-2"></i>
                                        Pilih gudang dan klik "Muat Produk dari Gudang" untuk menampilkan produk
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Additional Fields Section -->
            <div class="section-header mt-12">
                <h6>Informasi Tambahan</h6>
                <hr class="mx-n6">
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tag</label>
                    <div class="d-flex gap-2">
                        <select class="global-select2 form-select flex-grow-1" id="opname-tags" multiple data-allow-clear="false">
                            @foreach($tags as $tag)
                                <option value="{{ $tag['value'] }}" data-color="{{ $tag['color'] }}">{{ $tag['label'] }}</option>
                            @endforeach
                        </select>
                        @if($canAddTags)
                            <button type="button" class="btn btn-outline-primary btn-sm" id="add-new-opname-tag" title="Tambah Tag Baru">
                                <i class="icon-base ti tabler-plus"></i>
                            </button>
                        @endif
                    </div>
                    <small class="form-text text-muted">Pilih satu atau lebih tag untuk kategorisasi stock opname</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Memo (Internal)</label>
                    <textarea class="form-control" id="opname-memo" rows="2" placeholder="Catatan internal untuk stock opname ini"></textarea>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 text-end">
                <button type="button" class="btn btn-label-danger me-3" id="cancel-btn">Batalkan</button>
                <button type="button" class="btn btn-success" id="submit-btn">Simpan Stock Opname</button>
            </div>
        </form>
    </div>
@endsection

@section('js_script')
<script>
$(function() {
    // Sample products data (in real implementation, this would come from server)
    const sampleProducts = [
        { id: 1, code: 'AIR-330', name: 'RIYO Water 330ml', unit: 'pcs', system_stock: 1500, category: 'finished_goods' },
        { id: 2, code: 'AIR-600', name: 'RIYO Water 600ml', unit: 'pcs', system_stock: 800, category: 'finished_goods' },
        { id: 3, code: 'AIR-1500', name: 'RIYO Water 1500ml', unit: 'pcs', system_stock: 400, category: 'finished_goods' },
        { id: 4, code: 'BTL-330', name: 'Botol Plastik 330ml', unit: 'pcs', system_stock: 5000, category: 'packaging' },
        { id: 5, code: 'LBL-330', name: 'Label RIYO 330ml', unit: 'pcs', system_stock: 5200, category: 'packaging' },
        { id: 6, code: 'CAP-330', name: 'Tutup Botol 330ml', unit: 'pcs', system_stock: 5100, category: 'packaging' }
    ];
    
    // Auto-generate opname number
    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');
    const sequence = String(Math.floor(Math.random() * 999) + 1).padStart(3, '0');
    $('#opname-number').val(`SO-${year}-${sequence}`);
    
    // Set today's date
    $('#opname-date').val(today.toISOString().split('T')[0]);
    
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
        
        // Filter products based on category if selected
        let filteredProducts = sampleProducts;
        if (categoryFilter) {
            filteredProducts = sampleProducts.filter(product => product.category === categoryFilter);
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
        
        // Bind events for physical quantity changes
        $('.physical-qty').on('input', function() {
            const physicalQty = parseInt($(this).val()) || 0;
            const systemStock = parseInt($(this).data('system-stock'));
            const variance = physicalQty - systemStock;
            
            const varianceCell = $(this).closest('tr').find('.variance-display');
            if (variance > 0) {
                varianceCell.html(`<span class="text-success">+${variance.toLocaleString('id-ID')}</span>`);
            } else if (variance < 0) {
                varianceCell.html(`<span class="text-danger">${variance.toLocaleString('id-ID')}</span>`);
            } else {
                varianceCell.html('<span class="text-muted">0</span>');
            }
        });
        
        // Show success message
        Swal.fire({
            title: 'Berhasil!',
            text: `${filteredProducts.length} produk berhasil dimuat dari gudang`,
            icon: 'success',
            timer: 2000,
            showConfirmButton: false
        });
    });
    
    // Select all products checkbox
    $('#select-all-products').on('change', function() {
        $('.product-checkbox').prop('checked', $(this).prop('checked'));
    });
    
    // Individual product checkbox change
    $(document).on('change', '.product-checkbox', function() {
        const totalCheckboxes = $('.product-checkbox').length;
        const checkedCheckboxes = $('.product-checkbox:checked').length;
        
        if (checkedCheckboxes === totalCheckboxes) {
            $('#select-all-products').prop('checked', true);
        } else {
            $('#select-all-products').prop('checked', false);
        }
    });
    
    // Form validation
    function validateForm() {
        let isValid = true;
        const requiredFields = ['opname-number', 'opname-date', 'warehouse-select', 'pic-select', 'status-select'];
        
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
            title: 'Konfirmasi Simpan',
            text: `Apakah Anda yakin ingin menyimpan stock opname dengan ${formData.products.length} produk?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Simpan',
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
                    text: 'Stock opname berhasil disimpan',
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
            text: 'Apakah Anda yakin ingin membatalkan? Data yang sudah diinput akan hilang.',
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