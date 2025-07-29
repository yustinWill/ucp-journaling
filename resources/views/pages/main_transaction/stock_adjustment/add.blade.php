@extends('layouts.default')
@section('page_title')
    Tambah Stock Adjustment Baru
@stop
@section('content')
    <div class="card mb-6">
        <h5 class="card-header">Tambah Stock Adjustment Baru</h5>
        <form class="card-body" id="stockAdjustmentForm">
            <!-- Basic Information Section -->
            <div class="section-header mt-12">
                <h6>Informasi Dasar</h6>
                <hr class="mx-n6">
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Nomor Adjustment <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="adjustment-number" placeholder="Auto Generate" readonly />
                    <small class="form-text text-muted">Nomor akan di-generate otomatis saat disimpan</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Adjustment <span style="color: red;">*</span></label>
                    <input type="date" class="form-control global-single-date-picker" id="adjustment-date" value="{{ date('Y-m-d') }}" />
                </div>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tipe Adjustment <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="adjustment-type" data-allow-clear="false">
                        <option value="">Pilih Tipe Adjustment</option>
                        @foreach($adjustmentTypes as $type)
                            <option value="{{ $type['value'] }}">{{ $type['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Gudang <span style="color: red;">*</span></label>
                    <div class="row g-2">
                        <div class="col">
                            <select class="global-select2 form-select" id="warehouse-select" data-allow-clear="false">
                                <option value="">Pilih Gudang</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse['value'] }}">{{ $warehouse['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if($canAddWarehouses)
                            <div class="col-auto">
                                <a href="{{ route('master_data.storage.add') }}" target="_blank" class="btn btn-outline-primary d-flex align-items-center" 
                                   title="Tambah Gudang Baru">
                                    <i class="icon-base ti tabler-plus"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Penanggung Jawab <span style="color: red;">*</span></label>
                    <div class="row g-2">
                        <div class="col">
                            <select class="global-select2 form-select" id="pic-select" data-allow-clear="false">
                                <option value="">Pilih PIC</option>
                                @foreach($picOptions as $pic)
                                    <option value="{{ $pic['value'] }}">{{ $pic['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if($canAddUsers)
                            <div class="col-auto">
                                <a href="{{ route('master_data.user.add') }}" target="_blank" class="btn btn-outline-primary d-flex align-items-center" 
                                   title="Tambah User Baru">
                                    <i class="icon-base ti tabler-plus"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="status-select" data-allow-clear="false">
                        <option value="">Pilih Status</option>
                        @foreach($statusOptions as $status)
                            <option value="{{ $status['value'] }}">{{ $status['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Deskripsi <span style="color: red;">*</span></label>
                    <textarea class="form-control" id="description" rows="3" placeholder="Jelaskan alasan dan detail stock adjustment..." maxlength="1000"></textarea>
                    <small class="form-text text-muted">Maksimal 1000 karakter</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Memo <small class="text-muted">(Internal)</small></label>
                    <textarea class="form-control" id="adjustment-memo" rows="3" placeholder="Catatan internal untuk stock adjustment..." maxlength="500"></textarea>
                    <small class="form-text text-muted">Maksimal 500 karakter - Hanya untuk keperluan internal</small>
                </div>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tag</label>
                    <div class="row g-2">
                        <div class="col">
                            <select class="global-select2 form-select" id="adjustment-tags" multiple data-allow-clear="false">
                                @foreach($availableTags as $tag)
                                    <option value="{{ $tag['code'] }}" data-color="{{ $tag['color'] }}">{{ $tag['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if($canAddTags)
                            <div class="col-auto">
                                <a href="{{ route('master_data.tag.add') }}" target="_blank" class="btn btn-outline-primary d-flex align-items-center" id="add-new-adjustment-tag" title="Tambah Tag Baru">
                                    <i class="icon-base ti tabler-plus"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                    <small class="form-text text-muted">Pilih satu atau lebih tag untuk kategorisasi</small>
                </div>
                <div class="col-md-6">
                    <!-- Empty column for alignment -->
                </div>
            </div>

            <!-- Product Selection Section -->
            <div class="section-header mt-12">
                <h6>Pemilihan Produk</h6>
                <hr class="mx-n6">
            </div>
            
            <!-- Filter Section -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Filter Kategori Produk</label>
                    <select class="global-select2 form-select" id="product-category-filter" data-allow-clear="false">
                        <option value="">Semua Kategori</option>
                        @foreach($productCategories as $category)
                            <option value="{{ $category['value'] }}">{{ $category['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Cari Produk</label>
                    <input type="text" class="form-control" id="product-search" placeholder="Nama atau kode produk..." />
                </div>
            </div>
            
            <!-- Load Products Button -->
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <button type="button" class="btn btn-md btn-primary waves-effect waves-light" id="load-products-btn">
                        <i class="icon-base ti tabler-box me-2"></i>
                        Load Produk dari Gudang
                    </button>
                    <small class="form-text text-muted ms-3">Pilih gudang terlebih dahulu, kemudian klik untuk memuat daftar produk</small>
                </div>
            </div>

            <!-- Product Adjustment Table -->
            <div class="section-header mt-12">
                <h6>Daftar Produk untuk Adjustment</h6>
                <hr class="mx-n6">
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Produk yang akan disesuaikan</h6>
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
                                    <th style="width: 8%;">Unit</th>
                                    <th style="width: 10%;">Stok Sistem</th>
                                    <th style="width: 12%;">Qty Adjustment</th>
                                    <th style="width: 10%;">Selisih</th>
                                    <th style="width: 15%;">Alasan</th>
                                </tr>
                            </thead>
                            <tbody id="products-table-tbody">
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-5">
                                        <i class="icon-base ti tabler-box-off me-2"></i>
                                        Pilih gudang dan klik "Load Produk dari Gudang" untuk memuat data
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Summary Section -->
            <div class="section-header mt-12">
                <h6>Ringkasan Adjustment</h6>
                <hr class="mx-n6">
            </div>
            
            <div class="row g-3 mb-6">
                <div class="col-md-4">
                    <div class="summary-card success">
                        <h6>Total Item</h6>
                        <h4 id="total-items-summary">0</h4>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="summary-card" id="total-qty-card">
                        <h6>Total Qty Adjustment</h6>
                        <h4 id="total-qty-summary">0</h4>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="summary-card" id="total-value-card">
                        <h6>Estimasi Nilai Adjustment</h6>
                        <h4 id="total-value-summary">Rp 0</h4>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 text-end">
                <button type="button" class="btn btn-label-danger me-3" id="cancel-btn">Batalkan</button>
                <button type="button" class="btn btn-success" id="submit-btn">Simpan Adjustment</button>
            </div>
        </form>
    </div>
@endsection

@section('js_script')
<script>
$(function() {
    // Mock product data for RIYO Water
    const mockProducts = [
        {
            code: 'AIR-330',
            name: 'RIYO Water 330ml',
            unit: 'pcs',
            current_stock: 1500,
            category: 'FINISHED_GOODS',
            unit_cost: 12000
        },
        {
            code: 'AIR-600',
            name: 'RIYO Water 600ml',
            unit: 'pcs',
            current_stock: 800,
            category: 'FINISHED_GOODS',
            unit_cost: 18000
        },
        {
            code: 'AIR-1500',
            name: 'RIYO Water 1500ml',
            unit: 'pcs',
            current_stock: 400,
            category: 'FINISHED_GOODS',
            unit_cost: 21000
        },
        {
            code: 'BTL-330',
            name: 'Botol PET 330ml',
            unit: 'pcs',
            current_stock: 5000,
            category: 'PACKAGING',
            unit_cost: 1650
        },
        {
            code: 'BTL-600',
            name: 'Botol PET 600ml',
            unit: 'pcs',
            current_stock: 3000,
            category: 'PACKAGING',
            unit_cost: 2200
        },
        {
            code: 'LBL-330',
            name: 'Label RIYO 330ml',
            unit: 'pcs',
            current_stock: 5000,
            category: 'PACKAGING',
            unit_cost: 150
        },
        {
            code: 'CAP-330',
            name: 'Tutup Botol 330ml',
            unit: 'pcs',
            current_stock: 5000,
            category: 'PACKAGING',
            unit_cost: 200
        }
    ];

    // Form validation
    function validateForm() {
        let isValid = true;
        const requiredFields = ['adjustment-date', 'adjustment-type', 'warehouse-select', 'pic-select', 'status-select', 'description'];
        
        requiredFields.forEach(function(fieldId) {
            const field = $('#' + fieldId);
            if (!field.val() || field.val().trim() === '') {
                field.addClass('is-invalid');
                isValid = false;
            } else {
                field.removeClass('is-invalid');
            }
        });
        
        // Check if at least one product is selected with adjustment qty
        const hasValidProducts = $('#products-table tbody tr').filter(function() {
            const checkbox = $(this).find('.row-checkbox:checked');
            const adjustmentQty = $(this).find('.adjustment-qty').val();
            return checkbox.length > 0 && adjustmentQty && adjustmentQty !== '0';
        }).length > 0;
        
        if (!hasValidProducts) {
            Swal.fire({
                icon: 'warning',
                title: 'Produk Adjustment',
                text: 'Pilih minimal satu produk dengan qty adjustment yang tidak nol.',
                customClass: {
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false
            });
            isValid = false;
        }
        
        return isValid;
    }

    // Load products based on warehouse and filters
    $('#load-products-btn').on('click', function() {
        const warehouse = $('#warehouse-select').val();
        const categoryFilter = $('#product-category-filter').val();
        const searchFilter = $('#product-search').val().toLowerCase();
        
        if (!warehouse) {
            Swal.fire({
                icon: 'warning',
                title: 'Pilih Gudang',
                text: 'Silakan pilih gudang terlebih dahulu.',
                customClass: {
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false
            });
            return;
        }
        
        // Filter products
        let filteredProducts = mockProducts;
        
        if (categoryFilter) {
            filteredProducts = filteredProducts.filter(product => product.category === categoryFilter);
        }
        
        if (searchFilter) {
            filteredProducts = filteredProducts.filter(product => 
                product.code.toLowerCase().includes(searchFilter) || 
                product.name.toLowerCase().includes(searchFilter)
            );
        }
        
        // Generate table rows
        renderProductTable(filteredProducts);
    });

    function renderProductTable(products) {
        const tbody = $('#products-table-tbody');
        
        if (products.length === 0) {
            tbody.html(`
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                        <i class="icon-base ti tabler-search-off me-2"></i>
                        Tidak ada produk ditemukan dengan filter yang dipilih
                    </td>
                </tr>
            `);
            return;
        }
        
        let rows = '';
        products.forEach(function(product, index) {
            rows += `
                <tr data-product-code="${product.code}">
                    <td>
                        <div class="form-check">
                            <input class="form-check-input row-checkbox" type="checkbox" value="${product.code}">
                        </div>
                    </td>
                    <td><strong>${product.code}</strong></td>
                    <td>${product.name}</td>
                    <td>${product.unit}</td>
                    <td><span class="text-info">${product.current_stock.toLocaleString('id-ID')}</span></td>
                    <td>
                        <input type="number" class="form-control form-control-sm adjustment-qty" 
                               placeholder="0" data-current-stock="${product.current_stock}" 
                               data-unit-cost="${product.unit_cost}" min="-${product.current_stock}">
                    </td>
                    <td class="variance-display">
                        <span class="text-muted">0</span>
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm reason-input" 
                               placeholder="Alasan adjustment..." maxlength="200">
                    </td>
                </tr>
            `;
        });
        
        tbody.html(rows);
        updateSummary();
    }

    // Auto-calculation for variance and summary
    $(document).on('input', '.adjustment-qty', function() {
        const adjustmentQty = parseInt($(this).val()) || 0;
        const currentStock = parseInt($(this).data('current-stock')) || 0;
        const row = $(this).closest('tr');
        const varianceCell = row.find('.variance-display');
        
        if (adjustmentQty > 0) {
            varianceCell.html(`<span class="text-success">+${adjustmentQty.toLocaleString('id-ID')}</span>`);
        } else if (adjustmentQty < 0) {
            varianceCell.html(`<span class="text-danger">${adjustmentQty.toLocaleString('id-ID')}</span>`);
        } else {
            varianceCell.html('<span class="text-muted">0</span>');
        }
        
        updateSummary();
    });

    // Checkbox functionality
    $('#select-all-products').on('change', function() {
        $('.row-checkbox').prop('checked', $(this).prop('checked'));
        updateSummary();
    });
    
    $(document).on('change', '.row-checkbox', function() {
        const totalCheckboxes = $('.row-checkbox').length;
        const checkedCheckboxes = $('.row-checkbox:checked').length;
        $('#select-all-products').prop('checked', checkedCheckboxes === totalCheckboxes && totalCheckboxes > 0);
        updateSummary();
    });


    // Update summary cards
    function updateSummary() {
        let totalItems = 0;
        let totalQty = 0;
        let totalValue = 0;
        
        $('#products-table tbody tr').each(function() {
            const checkbox = $(this).find('.row-checkbox:checked');
            const adjustmentQtyInput = $(this).find('.adjustment-qty');
            const adjustmentQty = parseInt(adjustmentQtyInput.val()) || 0;
            const unitCost = parseInt(adjustmentQtyInput.data('unit-cost')) || 0;
            
            if (checkbox.length > 0 && adjustmentQty !== 0) {
                totalItems++;
                totalQty += adjustmentQty;
                totalValue += (adjustmentQty * unitCost);
            }
        });
        
        // Update summary display
        $('#total-items-summary').text(totalItems.toLocaleString('id-ID'));
        
        // Update qty card styling based on positive/negative
        const qtyCard = $('#total-qty-card');
        if (totalQty > 0) {
            qtyCard.removeClass('danger').addClass('success');
            $('#total-qty-summary').text('+' + totalQty.toLocaleString('id-ID'));
        } else if (totalQty < 0) {
            qtyCard.removeClass('success').addClass('danger');
            $('#total-qty-summary').text(totalQty.toLocaleString('id-ID'));
        } else {
            qtyCard.removeClass('success danger');
            $('#total-qty-summary').text('0');
        }
        
        // Update value card styling
        const valueCard = $('#total-value-card');
        const formattedValue = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(Math.abs(totalValue));
        
        if (totalValue > 0) {
            valueCard.removeClass('danger').addClass('success');
            $('#total-value-summary').text('+' + formattedValue);
        } else if (totalValue < 0) {
            valueCard.removeClass('success').addClass('danger');
            $('#total-value-summary').text('-' + formattedValue);
        } else {
            valueCard.removeClass('success danger');
            $('#total-value-summary').text('Rp 0');
        }
    }

    // Clear table when warehouse changes
    $('#warehouse-select').on('change', function() {
        clearProductTable();
    });
    
    function clearProductTable() {
        $('#products-table-tbody').html(`
            <tr>
                <td colspan="8" class="text-center text-muted py-5">
                    <i class="icon-base ti tabler-box-off me-2"></i>
                    Pilih gudang dan klik "Load Produk dari Gudang" untuk memuat data
                </td>
            </tr>
        `);
        $('#select-all-products').prop('checked', false);
        updateSummary();
    }

    // Cancel button
    $('#cancel-btn').on('click', function() {
        window.location.href = "{{ route('main_transaction.stock_adjustment.index') }}";
    });

    // Submit button
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
            adjustment_date: $('#adjustment-date').val(),
            adjustment_type: $('#adjustment-type').val(),
            warehouse_id: $('#warehouse-select').val(),
            pic_id: $('#pic-select').val(),
            status: $('#status-select').val(),
            description: $('#description').val(),
            products: []
        };
        
        // Collect selected products
        $('#products-table tbody tr').each(function() {
            const checkbox = $(this).find('.row-checkbox:checked');
            const adjustmentQtyInput = $(this).find('.adjustment-qty');
            const adjustmentQty = parseInt(adjustmentQtyInput.val()) || 0;
            const reason = $(this).find('.reason-input').val();
            
            if (checkbox.length > 0 && adjustmentQty !== 0) {
                formData.products.push({
                    product_code: $(this).data('product-code'),
                    adjustment_qty: adjustmentQty,
                    reason: reason
                });
            }
        });
        
        // Show confirmation
        Swal.fire({
            title: 'Konfirmasi Simpan',
            text: `Apakah Anda yakin ingin menyimpan stock adjustment dengan ${formData.products.length} produk?`,
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
                // In real implementation, submit to server
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Stock adjustment berhasil disimpan.',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                }).then(() => {
                    window.location.href = "{{ route('main_transaction.stock_adjustment.index') }}";
                });
            }
        });
    });
});
</script>
@endsection