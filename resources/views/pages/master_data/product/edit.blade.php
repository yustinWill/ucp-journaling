@extends('layouts.default')
@section('page_title')
    Edit Produk
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Edit Produk</h4>
        <form class="card-body" id="productForm" autocomplete="off">
            <!-- Info Produk Section -->
            <div class="section-header mt-12">
                <h6>Info Produk</h6>
                <hr class="mx-n6">
            </div>
            
            <!-- Row 1: Basic Information -->
            <div class="row g-3 mb-4">
                <div class="col-md-8">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Nama Produk <span style="color: red;">*</span></label>
                            <input type="text" class="form-control" id="product-name" placeholder="Masukkan nama produk" maxlength="255" value="{{ $product['name'] ?? 'RIYO Water 600ml' }}" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Unit <span style="color: red;">*</span></label>
                            <select class="global-select2 form-select" id="unit-select" data-allow-clear="false">
                                <option value="">Pilih Unit</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit['value'] }}" @if(($product['unit'] ?? 'btl') === $unit['value']) selected @endif>{{ $unit['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Kode Produk <span style="color: red;">*</span></label>
                            <input type="text" class="form-control" id="product-code" placeholder="PROD-2025-001" value="{{ $product['code'] ?? 'PROD-2025-001' }}" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kategori Produk</label>
                            <select class="global-select2 form-select" id="category-select" data-allow-clear="false">
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category['value'] }}" @if(($product['category'] ?? 'finished_goods') === $category['value']) selected @endif>{{ $category['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="product-description" rows="3" placeholder="Deskripsi detail produk..." maxlength="1000">{{ $product['description'] ?? 'Air minum dalam kemasan botol 600ml berkualitas tinggi dengan proses filtrasi canggih' }}</textarea>
                            <small class="form-text text-muted">Maksimal 1000 karakter</small>
                        </div>
                        <div class="col-md-6">
                            <!-- Empty column for layout balance -->
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Gambar Produk</label>
                    <div class="image-upload-area" style="height: 200px; width: 200px; border: 2px dashed #ccc; display: flex; align-items: center; justify-content: center; flex-direction: column;">
                        <div id="image-placeholder" class="text-center" @if($product['image'] ?? false) style="display: none;" @endif>
                            <i class="icon-base ti tabler-photo" style="font-size: 48px; color: #ccc;"></i>
                            <p class="text-muted mt-2">Pilih gambar produk</p>
                            <small class="text-muted">Format file JPG atau PNG (maks. 5 MB)</small>
                        </div>
                        <img id="image-preview" style="@if($product['image'] ?? false) display: block; @else display: none; @endif max-width: 100%; max-height: 100%; object-fit: cover;" @if($product['image'] ?? false) src="{{ $product['image'] }}" @endif />
                        <input type="file" id="product-image" class="form-control" accept=".jpg,.jpeg,.png" style="display: none;" />
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="document.getElementById('product-image').click()">
                        Ubah Gambar
                    </button>
                </div>
            </div>
            
            <!-- Product Type Selection -->
            <div class="section-header mt-12">
                <h6>Tipe Produk</h6>
                <hr class="mx-n6">
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="product-type" id="type-single" value="single" @if(($product['type'] ?? 'single') === 'single') checked @endif>
                        <label class="form-check-label" for="type-single">
                            Single
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="product-type" id="type-bundle" value="bundle" @if(($product['type'] ?? 'single') === 'bundle') checked @endif>
                        <label class="form-check-label" for="type-bundle">
                            Bundle
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- Single Product Content -->
            <div id="single-content" @if(($product['type'] ?? 'single') === 'single') style="display: block;" @else style="display: none;" @endif>
                <div class="section-header mt-12">
                    <h6>Harga & Persediaan</h6>
                    <hr class="mx-n6">
                </div>
                
                <!-- Purchase Section -->
                <div class="mb-4">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="enable-purchase" @if($product['enable_purchase'] ?? true) checked @endif>
                        <label class="form-check-label" for="enable-purchase">
                            Saya beli produk ini
                        </label>
                    </div>
                    
                    <div id="purchase-fields" @if($product['enable_purchase'] ?? true) style="display: block;" @else style="display: none;" @endif>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Harga Satuan <span style="color: red;">*</span></label>
                                <input type="text" class="form-control curr-mask text-end" id="purchase-price" placeholder="Rp 0" value="{{ $product['purchase_price'] ?? 'Rp 1,200' }}" />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Akun Pembelian <span style="color: red;">*</span></label>
                                <select class="global-select2 form-select" id="purchase-account" data-allow-clear="false">
                                    <option value="">Pilih Akun</option>
                                    @foreach($accounts as $account)
                                        <option value="{{ $account['value'] }}" @if(($product['purchase_account'] ?? '5001') === $account['value']) selected @endif>{{ $account['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Pajak Pembelian <span style="color: red;">*</span></label>
                                <select class="global-select2 form-select" id="purchase-tax" data-allow-clear="false">
                                    <option value="">Pilih Pajak</option>
                                    @foreach($taxes as $tax)
                                        <option value="{{ $tax['value'] }}" @if(($product['purchase_tax'] ?? 'ppn_11') === $tax['value']) selected @endif>{{ $tax['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <!-- Empty column for layout balance -->
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Sales Section -->
                <div class="mb-4">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="enable-sales" @if($product['enable_sales'] ?? true) checked @endif>
                        <label class="form-check-label" for="enable-sales">
                            Saya jual produk ini
                        </label>
                    </div>
                    
                    <div id="sales-fields" @if($product['enable_sales'] ?? true) style="display: block;" @else style="display: none;" @endif>
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Harga Satuan <span style="color: red;">*</span></label>
                                <input type="text" class="form-control curr-mask text-end" id="sales-price" placeholder="Rp 0" value="{{ $product['sales_price'] ?? 'Rp 2,500' }}" />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Akun Penjualan <span style="color: red;">*</span></label>
                                <select class="global-select2 form-select" id="sales-account" data-allow-clear="false">
                                    <option value="">Pilih Akun</option>
                                    @foreach($accounts as $account)
                                        <option value="{{ $account['value'] }}" @if(($product['sales_account'] ?? '4001') === $account['value']) selected @endif>{{ $account['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Pajak Penjualan <span style="color: red;">*</span></label>
                                <select class="global-select2 form-select" id="sales-tax" data-allow-clear="false">
                                    <option value="">Pilih Pajak</option>
                                    @foreach($taxes as $tax)
                                        <option value="{{ $tax['value'] }}" @if(($product['sales_tax'] ?? 'ppn_11') === $tax['value']) selected @endif>{{ $tax['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Inventory Section -->
                <div class="mb-4">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="enable-inventory" @if($product['enable_inventory'] ?? true) checked @endif>
                        <label class="form-check-label" for="enable-inventory">
                            Monitoring Persediaan Barang
                        </label>
                    </div>
                    
                    <div id="inventory-fields" @if($product['enable_inventory'] ?? true) style="display: block;" @else style="display: none;" @endif>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Batas stok minimum <span style="color: red;">*</span></label>
                                <input type="number" class="form-control" id="minimum-stock" placeholder="0" value="{{ $product['minimum_stock'] ?? '1000' }}" />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Akun Persediaan Default <span style="color: red;">*</span></label>
                                <select class="global-select2 form-select" id="inventory-account" data-allow-clear="false">
                                    <option value="">Pilih Akun</option>
                                    @foreach($accounts as $account)
                                        <option value="{{ $account['value'] }}" @if(($product['inventory_account'] ?? '1003') === $account['value']) selected @endif>{{ $account['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <small class="form-text text-muted">*Ketentuan stok awal dapat menggunakan penyesuaian stok</small>
                        
                        <div class="mt-3">
                            <label class="form-label">Pelacakan Persediaan</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="track-quantity" @if($product['track_quantity'] ?? true) checked @endif>
                                        <label class="form-check-label" for="track-quantity">
                                            Hanya lacak kuantitas
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="track-batch" @if($product['track_batch'] ?? false) checked @endif>
                                        <label class="form-check-label" for="track-batch">
                                            Batch
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                        <select class="global-select2 form-select flex-grow-1" id="product-tags" multiple data-allow-clear="false">
                            @foreach($tags as $tag)
                                <option value="{{ $tag['value'] }}" data-color="{{ $tag['color'] }}" 
                                    @if(in_array($tag['value'], $product['tags'] ?? ['finished_goods', 'retail'])) selected @endif>{{ $tag['label'] }}</option>
                            @endforeach
                        </select>
                        @if($canAddTags)
                            <button type="button" class="btn btn-outline-primary btn-sm" id="add-new-product-tag" title="Tambah Tag Baru">
                                <i class="icon-base ti tabler-plus"></i>
                            </button>
                        @endif
                    </div>
                    <small class="form-text text-muted">Pilih satu atau lebih tag untuk kategorisasi produk</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Memo (Internal)</label>
                    <textarea class="form-control" id="product-memo" rows="2" placeholder="Catatan internal untuk produk ini">{{ $product['memo'] ?? 'Produk utama RIYO Water dengan margin keuntungan terbaik' }}</textarea>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="pt-6 text-end">
                <button type="button" class="btn btn-label-danger me-3" id="cancel-btn">Batalkan</button>
                <button type="button" class="btn btn-success" id="submit-btn">Perbarui</button>
            </div>
        </form>
    </div>
@endsection

@section('js_script')
<script>
    $(function() {
        let currentProductType = '{{ $product["type"] ?? "single" }}';
        
        // Initialize form based on existing data
        if (currentProductType === 'single') {
            $('#single-content').show();
        } else if (currentProductType === 'bundle') {
            $('#bundle-content').show();
        }
        
        // Product type change handler
        $('input[name="product-type"]').change(function() {
            const newType = $(this).val();
            
            if (currentProductType && currentProductType !== newType) {
                Swal.fire({
                    title: 'Konfirmasi Perubahan',
                    text: 'Mengubah tipe produk akan mengatur ulang data di bawah. Lanjutkan?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Lanjutkan',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-warning',
                        cancelButton: 'btn btn-secondary'
                    },
                    buttonsStyling: false
                }).then(function(result) {
                    if (result.value) {
                        switchProductType(newType);
                    } else {
                        // Revert selection
                        $('input[name="product-type"][value="' + currentProductType + '"]').prop('checked', true);
                    }
                });
            } else {
                switchProductType(newType);
            }
        });
        
        function switchProductType(type) {
            currentProductType = type;
            
            // Hide all content first
            $('#single-content, #bundle-content').hide();
            
            // Show appropriate content
            if (type === 'single') {
                $('#single-content').show();
            } else if (type === 'bundle') {
                $('#bundle-content').show();
            }
        }
        
        // Conditional field toggles
        $('#enable-purchase').change(function() {
            $('#purchase-fields').toggle(this.checked);
        });
        
        $('#enable-sales').change(function() {
            $('#sales-fields').toggle(this.checked);
        });
        
        $('#enable-inventory').change(function() {
            $('#inventory-fields').toggle(this.checked);
        });
        
        // Image upload handling
        $('#product-image').change(function() {
            const file = this.files[0];
            if (file) {
                // Validate file type
                if (!file.type.match('image/(jpeg|jpg|png)')) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Format File Tidak Valid',
                        text: 'Hanya file JPG dan PNG yang diperbolehkan.',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    $(this).val('');
                    return;
                }
                
                // Validate file size (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Ukuran File Terlalu Besar',
                        text: 'Ukuran file maksimal 5MB.',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    $(this).val('');
                    return;
                }
                
                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#image-placeholder').hide();
                    $('#image-preview').attr('src', e.target.result).show();
                };
                reader.readAsDataURL(file);
            }
        });
        
        // Form submission
        $('#submit-btn').click(function(e) {
            e.preventDefault();
            
            // Validate required fields
            let isValid = true;
            const requiredFields = ['product-name', 'unit-select', 'product-code'];
            
            requiredFields.forEach(function(fieldId) {
                const field = $('#' + fieldId);
                if (!field.val() || field.val().trim() === '') {
                    field.addClass('is-invalid');
                    isValid = false;
                } else {
                    field.removeClass('is-invalid');
                }
            });
            
            if (!isValid) {
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Validasi',
                    text: 'Silakan lengkapi semua field yang wajib diisi.',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                });
                return;
            }
            
            Swal.fire({
                title: 'Perbarui Produk?',
                text: 'Apakah Anda yakin ingin memperbarui produk ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Perbarui!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            }).then(function(result) {
                if (result.value) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Produk berhasil diperbarui.',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        },
                        buttonsStyling: false
                    });
                }
            });
        });
        
        // Cancel button
        $('#cancel-btn').click(function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: 'Batalkan Perubahan?',
                text: 'Semua perubahan yang belum disimpan akan hilang!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Batalkan!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            }).then(function(result) {
                if (result.value) {
                    window.location.href = "{{ route('master_data.product.index') }}";
                }
            });
        });
        
        // Initialize currency formatting
        if (typeof Cleave !== 'undefined') {
            $('.curr-mask').each(function() {
                new Cleave(this, {
                    numeral: true,
                    numeralThousandsGroupStyle: 'thousand',
                    prefix: 'Rp ',
                    noImmediatePrefix: false,
                    rawValueTrimPrefix: true
                });
            });
        }
    });
</script>
@endsection