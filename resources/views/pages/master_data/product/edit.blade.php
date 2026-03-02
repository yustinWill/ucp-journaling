@extends('layouts.default')
@section('page_title')
    Edit Produk
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Edit Produk</h4>
        <form class="card-body" id="productForm" autocomplete="off">
            <input type="hidden" id="product-id" value="{{ $product->id }}">
            <!-- Info Produk Section -->
            <div class="section-header mt-12">
                <h6>Info Produk</h6>
                <hr class="mx-n6">
            </div>

            <input type="hidden" id="default-tax-id" name="default_tax_id" value="{{ $defaultNoTaxId }}" />

            <!-- Row 1: Basic Information -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Nama Produk <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="product-name" name="product-name"
                        placeholder="Masukkan nama produk" maxlength="255"
                        value="{{ old('product-name', $product->name ?? '') }}" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Unit <span style="color: red;">*</span></label>
                    <div class="row g-2">
                        <div class="col">
                            <select class="global-select2 form-select" id="unit-select" name="unit-select"
                                data-allow-clear="false">
                                <option value="">Pilih Unit</option>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}"
                                        {{ old('unit-select', $product->f_unit_id ?? '') == $unit->id ? 'selected' : '' }}>
                                        {{ $unit->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($canAddUnits)
                            <div class="col-auto">
                                <a href="{{ route('master_data.unit.add') }}" target="_blank"
                                    class="btn btn-outline-primary d-flex align-items-center" title="Tambah Unit Baru">
                                    <i class="icon-base ti tabler-plus"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Kode Produk <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="product-code" name="product-code"
                        value="{{ old('product-code', $product->code ?? '') }}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kategori Produk</label>
                    <div class="row g-2">
                        <div class="col">
                            <select class="global-select2 form-select" id="category-select" name="category-select"
                                data-allow-clear="false">
                                <option value="">Pilih Kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category-select', $product->f_category_id ?? '') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($canAddCategories)
                            <div class="col-auto">
                                <a href="{{ route('master_data.category.add') }}" target="_blank"
                                    class="btn btn-outline-primary d-flex align-items-center" title="Tambah Kategori Baru">
                                    <i class="icon-base ti tabler-plus"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Status <span style="color: red;">*</span></label>
                    <select class="global-select2 form-select" id="product-status" data-allow-clear="false" required>
                        <option value="">Pilih Status</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status['value'] }}"
                                {{ $product->status == $status['value'] ? 'selected' : '' }}>
                                {{ $status['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Deskripsi</label>
                    <textarea class="form-control" id="product-description" name="product-description" rows="3"
                        placeholder="Deskripsi detail produk..." maxlength="1000">{{ old('product-description', $product->description ?? '') }}</textarea>
                    <small class="form-text text-muted">Maksimal 1000 karakter</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Gambar Produk</label>
                    <div class="image-upload-area"
                        style="height: 200px; width: 200px; border: 2px dashed #ccc; display: flex; align-items: center; justify-content: center; flex-direction: column;">
                        @if (!empty($product->main_image))
                            <img id="image-preview" src="{{ $product->main_image }}"
                                style="max-width: 100%; max-height: 100%; object-fit: cover;" />
                        @else
                            <div id="image-placeholder" class="text-center">
                                <i class="icon-base ti tabler-photo" style="font-size: 48px; color: #ccc;"></i>
                                <p class="text-muted mt-2">Pilih gambar produk</p>
                                <small class="text-muted">Format file JPG atau PNG (maks. 5 MB)</small>
                            </div>
                            <img id="image-preview"
                                style="display: none; max-width: 100%; max-height: 100%; object-fit: cover;" />
                        @endif
                        <input type="file" id="product-image" class="form-control" accept=".jpg,.jpeg,.png"
                            style="display: none;" />
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary mt-2"
                        onclick="document.getElementById('product-image').click()">
                        Pilih Gambar
                    </button>
                </div>
            </div>

            <!-- Product Units Section -->
            <div class="section-header mt-12">
                <h6>Unit Produk <span style="color: red;">*</span></h6>
                <hr class="mx-n6">
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <i class="ti ti-info-circle me-2"></i>
                        Unit dasar diambil dari Unit yang dipilih di atas. Tambahkan unit lain untuk menunjukkan berapa unit dasar per 1 unit tersebut (contoh: 1 BOX = 12 PCS).
                    </div>

                    <div class="mx-n5 form-repeater-product-units">
                        <table class="table table-bordered" id="product-units-table">
                            <thead>
                                <tr>
                                    <th style="width: 45%">Unit <span style="color: red;">*</span></th>
                                    <th style="width: 40%">Isi per Unit <span style="color: red;">*</span></th>
                                    <th style="width: 15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="product-units-tbody" data-repeater-list="product_units">
                                @php
                                    $hasNonDefaultUnits = $productUnits->where('is_default', '!=', 1)->count() > 0;
                                @endphp

                                @if(!$hasNonDefaultUnits)
                                    {{-- Template row for when no units exist yet --}}
                                    <tr data-repeater-item class="product-unit-row" style="display: none;">
                                        <td>
                                            <select class="form-select unit-select" required>
                                                <option value="">Pilih Unit</option>
                                                @foreach($units as $unit)
                                                    <option value="{{ $unit->id }}" data-name="{{ $unit->name }}">{{ $unit->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <span class="input-group-text">1 <span class="unit-name-display ms-1"></span> =</span>
                                                <input type="text" class="form-control numeric-input text-end conversion-factor" value="" required>
                                                <span class="input-group-text"><span class="base-unit-display">Unit Dasar</span></span>
                                            </div>
                                        </td>
                                        <td>
                                            <button class="btn btn-label-danger" data-repeater-delete type="button">
                                                <i class="icon-base ti tabler-x me-1"></i>
                                                <span class="align-middle">Hapus</span>
                                            </button>
                                        </td>
                                    </tr>
                                @endif

                                @foreach($productUnits as $index => $productUnit)
                                    @if($productUnit->is_default != 1)
                                    <tr data-repeater-item class="product-unit-row">
                                        <td>
                                            <select class="form-select unit-select" required>
                                                <option value="">Pilih Unit</option>
                                                @foreach($units as $unit)
                                                    <option value="{{ $unit->id }}" data-name="{{ $unit->name }}" {{ $productUnit->f_unit_id == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <span class="input-group-text">1 <span class="unit-name-display ms-1"></span> =</span>
                                                <input type="text" class="form-control numeric-input text-end conversion-factor" value="{{ $productUnit->conversion_factor }}" required>
                                                <span class="input-group-text"><span class="base-unit-display">Unit Dasar</span></span>
                                            </div>
                                        </td>
                                        <td>
                                            <button class="btn btn-label-danger" data-repeater-delete type="button">
                                                <i class="icon-base ti tabler-x me-1"></i>
                                                <span class="align-middle">Hapus</span>
                                            </button>
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-primary mt-2" data-repeater-create>
                            <i class="icon-base ti tabler-plus"></i>
                            <span class="align-middle">Tambah Unit</span>
                        </button>
                    </div>
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
                        <input class="form-check-input" type="radio" name="product-type" id="type-single"
                            value="single" {{ old('product-type', $product->type ?? '') == 'single' ? 'checked' : '' }}>
                        <label class="form-check-label" for="type-single">
                            Single
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="product-type" id="type-bundle"
                            value="bundle" {{ old('product-type', $product->type ?? '') == 'bundle' ? 'checked' : '' }}>
                        <label class="form-check-label" for="type-bundle">
                            Bundle
                        </label>
                    </div>
                </div>
            </div>

            {{-- is-packaging-section (commented out)
            <div class="row g-3 mb-4" id="is-packaging-section" style="display: none;">
                <div class="col-md-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is-packaging" name="is-packaging"
                            {{ old('is-packaging', $product->is_packaging ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is-packaging">
                            Produk Packaging (untuk packing produk)
                        </label>
                    </div>
                    <small class="form-text text-muted">Centang jika produk bundle ini digunakan untuk packaging/packing produk lain</small>
                </div>
            </div>
            --}}

            <!-- Conditional Content Placeholder -->
            <div id="conditional-content" style="display: none;">
                <!-- Content will be dynamically loaded here -->
            </div>

            <!-- Single Product Content -->
            <div id="single-content" style="display: none;">
                <div class="section-header mt-12">
                    <h6>Harga & Persediaan</h6>
                    <hr class="mx-n6">
                </div>

                <!-- Purchase Section -->
                <div class="mb-4">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="enable-purchase" name="enable-purchase"
                            {{ !empty(old('enable-purchase', $product->is_item_purchased == true)) ? 'checked' : '' }}>
                        <label class="form-check-label" for="enable-purchase">
                            Saya beli produk ini
                        </label>
                    </div>

                    <div id="purchase-fields" style="display: none;">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Harga Satuan <span style="color: red;">*</span></label>
                                <input type="text" class="form-control currency-input text-end" id="purchase-price"
                                    name="purchase-price" placeholder="Rp 0"
                                    value="{{ old('purchase-price', $product->unit_purchase_price ?? '') }}" />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Akun Pembelian</label>
                                <select class="global-select2 form-select flex-grow-1" id="purchase-account"
                                    name="purchase-account" data-allow-clear="true">
                                    <option value="">Pilih Akun</option>
                                    @foreach ($accounts as $account)
                                        <option value="{{ $account->code }}"
                                            {{ old('purchase-account', $product->f_purchase_coa_code ?? '') == $account->code && $product->type == 'single' ? 'selected' : '' }}>
                                            {{ $account->code }} - {{ $account->name }}</option>
                                    @endforeach
                                </select>
                                {{-- @if ($canAddAccounts)
                                    <a href="{{ route('master_data.coa.add') }}" target="_blank"
                                        class="btn btn-outline-primary btn-sm" title="Tambah Akun Baru">
                                        <i class="icon-base ti tabler-plus"></i>
                                    </a>
                                @endif --}}
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Pajak Pembelian <span style="color: red;">*</span></label>
                                <select class="global-select2 form-select flex-grow-1" id="purchase-tax"
                                    name="purchase-tax" data-allow-clear="false">
                                    <option value="">Pilih Pajak</option>
                                    @foreach ($taxes as $tax)
                                        <option value="{{ $tax->id }}"
                                            {{ old('purchase-tax', $product->f_purchase_tax_id ?? '') == $tax->id ? 'selected' : '' }}>
                                            {{ $tax->name }}</option>
                                    @endforeach
                                </select>
                                {{-- @if ($canAddTaxes)
                                        <a href="{{ route('master_data.tax.add') }}" target="_blank"
                                            class="btn btn-outline-primary btn-sm" title="Tambah Pajak Baru">
                                            <i class="icon-base ti tabler-plus"></i>
                                        </a>
                                    @endif --}}
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
                        <input class="form-check-input" type="checkbox" id="enable-sales" name="enable-sales"
                            {{ !empty(old('enable-sales', $product->is_item_sold ?? false)) ? 'checked' : '' }}>
                        <label class="form-check-label" for="enable-sales">
                            Saya jual produk ini
                        </label>
                    </div>

                    <div id="sales-fields" style="display: none;">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Harga Satuan <span style="color: red;">*</span></label>
                                <input type="text" class="form-control currency-input text-end" id="sales-price"
                                    name="sales-price" placeholder="Rp 0"
                                    value="{{ old('sales-price', $product->unit_sell_price ?? '') }}" />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Akun Penjualan</label>
                                <select class="global-select2 form-select flex-grow-1" id="sales-account"
                                    name="sales-account" data-allow-clear="true">
                                    <option value="">Pilih Akun</option>
                                    @foreach ($accounts as $account)
                                        <option value="{{ $account->code }}"
                                            {{ old('sales-account', $product->f_sell_coa_code ?? '') == $account->code ? 'selected' : '' }}>
                                            {{ $account->code }} - {{ $account->name }}</option>
                                    @endforeach
                                </select>
                                {{-- @if ($canAddAccounts)
                                    <a href="{{ route('master_data.coa.add') }}" target="_blank"
                                        class="btn btn-outline-primary btn-sm" title="Tambah Akun Baru">
                                        <i class="icon-base ti tabler-plus"></i>
                                    </a>
                                @endif --}}
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Pajak Penjualan <span style="color: red;">*</span></label>
                                <select class="global-select2 form-select flex-grow-1" id="sales-tax" name="sales-tax"
                                    data-allow-clear="false">
                                    <option value="">Pilih Pajak</option>
                                    @foreach ($taxes as $tax)
                                        <option value="{{ $tax->id }}"
                                            {{ old('sales-tax', $product->f_sell_tax_id ?? '') == $tax->id ? 'selected' : '' }}>
                                            {{ $tax->name }}</option>
                                    @endforeach
                                </select>
                                {{-- @if ($canAddTaxes)
                                        <a href="{{ route('master_data.tax.add') }}" target="_blank"
                                            class="btn btn-outline-primary btn-sm" title="Tambah Pajak Baru">
                                            <i class="icon-base ti tabler-plus"></i>
                                        </a>
                                    @endif --}}
                            </div>
                            <div class="col-md-6">
                                <!-- Empty column for layout balance -->
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Inventory Section (commented out)
                <div class="mb-4">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="enable-inventory" name="enable-inventory"
                            {{ !empty(old('enable-inventory', $product->monitor_stock ?? false)) ? 'checked' : '' }}>
                        <label class="form-check-label" for="enable-inventory">
                            Monitoring Persediaan Barang
                        </label>
                    </div>

                    <div id="inventory-fields" style="display: none;">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Batas stok minimum <span style="color: red;">*</span></label>
                                <input type="text" class="form-control numeric-input text-end" id="minimum-stock"
                                    name="minimum-stock" placeholder="0"
                                    value="{{ old('minimum-stock', $product->minimum_stock ?? '') }}" />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Akun Persediaan Default <span
                                        style="color: red;">*</span></label>
                                <select class="global-select2 form-select flex-grow-1" id="inventory-account"
                                    name="inventory-account" data-allow-clear="false">
                                    <option value="">Pilih Akun</option>
                                    @foreach ($accounts as $account)
                                        <option value="{{ $account->code }}"
                                            {{ old('inventory-account', $product->f_inventory_coa_code ?? '') == $account->code ? 'selected' : '' }}>
                                            {{ $account->code }} - {{ $account->name }}</option>
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
                                        <input class="form-check-input" type="checkbox" id="track-quantity"
                                            name="track-quantity"
                                            {{ !empty(old('track-quantity', $product->track_quantity ?? true)) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="track-quantity">
                                            Hanya lacak kuantitas
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="track-batch"
                                            name="track-batch"
                                            {{ !empty(old('track-batch', $product->track_batch ?? false)) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="track-batch">
                                            Batch
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                --}}
            </div>

            <!-- Bundle Product Content -->
            <div id="bundle-content" style="display: none;">
                <!-- Tab Navigation -->
                <div class="list-group list-group-horizontal-md text-md-center mb-4" role="tablist">
                    <a class="list-group-item list-group-item-action waves-effect active" id="bundle-pricing-list-item"
                        data-bs-toggle="list" href="#bundle-pricing-tab" aria-selected="true" role="tab"
                        tabindex="-1">
                        <i class="icon-base ti tabler-currency-dollar me-2"></i>
                        <b>Harga & Persediaan</b>
                    </a>
                    <a class="list-group-item list-group-item-action waves-effect" id="bundle-config-list-item"
                        data-bs-toggle="list" href="#bundle-config-tab" aria-selected="false" role="tab"
                        tabindex="-1">
                        <i class="icon-base ti tabler-settings me-2"></i>
                        <b>Pengaturan Bundle</b>
                    </a>
                </div>

                <div class="tab-content">
                    <!-- Bundle Pricing Tab (Same as Single) -->
                    <div class="tab-pane fade show active mx-n6" id="bundle-pricing-tab" role="tabpanel">
                        <!-- Purchase Section -->
                        <div class="mb-4">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="bundle-packaging"
                                    {{ old('is-packaging', $product->is_packaging ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="bundle-packaging">
                                    Produk Packaging (untuk packing produk)
                                </label>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="bundle-enable-purchase"
                                    {{ !empty(old('enable-purchase', $product->is_item_purchased == true)) ? 'checked' : '' }}>
                                <label class="form-check-label" for="bundle-enable-purchase">
                                    Saya beli produk ini
                                </label>
                            </div>

                            <div id="bundle-purchase-fields" style="display: none;">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Harga Satuan <span style="color: red;">*</span></label>
                                        <input type="text" class="form-control currency-input text-end"
                                            id="bundle-purchase-price" placeholder="Rp 0"
                                            value="{{ old('bundle-purchase-price', $product->unit_purchase_price ?? '') }}" />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Akun Pembelian</label>
                                        <select class="global-select2 form-select" id="bundle-purchase-account"
                                            data-allow-clear="false">
                                            <option value="">Pilih Akun</option>
                                            @foreach ($accounts as $account)
                                                <option value="{{ $account->code }}"
                                                    {{ old('bundle-purchase-account', $product->f_purchase_coa_code ?? '') == $account->code ? 'selected' : '' }}>
                                                    {{ $account->code }} -
                                                    {{ $account->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Pajak Pembelian</label>
                                        <select class="global-select2 form-select" id="bundle-purchase-tax"
                                            data-allow-clear="false">
                                            <option value="">Pilih Pajak</option>
                                            @foreach ($taxes as $tax)
                                                <option value="{{ $tax->id }}"
                                                    {{ old('bundle-purchase-tax', $product->f_purchase_tax_id ?? '') == $tax->id ? 'selected' : '' }}>
                                                    {{ $tax->name }}</option>
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
                                <input class="form-check-input" type="checkbox" id="bundle-enable-sales"
                                    {{ !empty(old('bundle-enable-sales', $product->is_item_sold == true)) ? 'checked' : '' }}>
                                <label class="form-check-label" for="bundle-enable-sales">
                                    Saya jual produk ini
                                </label>
                            </div>

                            <div id="bundle-sales-fields" style="display: none;">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Harga Satuan <span style="color: red;">*</span></label>
                                        <input type="text" class="form-control currency-input text-end"
                                            id="bundle-sales-price" placeholder="Rp 0"
                                            value="{{ old('bundle-sales-price', $product->unit_sell_price ?? '') }}" />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Akun Penjualan</label>
                                        <select class="global-select2 form-select" id="bundle-sales-account"
                                            data-allow-clear="false">
                                            <option value="">Pilih Akun</option>
                                            @foreach ($accounts as $account)
                                                <option value="{{ $account->code }}"
                                                    {{ old('bundle-sales-account', $product->f_sell_coa_code ?? '') == $account->code ? 'selected' : '' }}>
                                                    {{ $account->code }} -
                                                    {{ $account->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Pajak Penjualan</label>
                                        <select class="global-select2 form-select" id="bundle-sales-tax"
                                            data-allow-clear="false">
                                            <option value="">Pilih Pajak</option>
                                            @foreach ($taxes as $tax)
                                                <option value="{{ $tax->id }}"
                                                    {{ old('bundle-sales-tax', $product->f_sell_tax_id ?? '') == $tax->id ? 'selected' : '' }}>
                                                    {{ $tax->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <!-- Empty column for layout balance -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Inventory Section (commented out)
                        <div class="mb-4">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="bundle-enable-inventory"
                                    {{ !empty(old('bundle-enable-inventory', $product->monitor_stock == true)) ? 'checked' : '' }}>
                                <label class="form-check-label" for="bundle-enable-inventory">
                                    Monitoring Persediaan Barang
                                </label>
                            </div>

                            <div id="bundle-inventory-fields" style="display: none;">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Batas stok minimum <span
                                                style="color: red;">*</span></label>
                                        <input type="text" class="form-control numeric-input text-end"
                                            id="bundle-minimum-stock" placeholder="0"
                                            value="{{ old('bundle-minimum-stock', $product->minimum_stock ?? '') }}" />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Akun Persediaan Default <span
                                                style="color: red;">*</span></label>
                                        <select class="global-select2 form-select" id="bundle-inventory-account"
                                            data-allow-clear="false">
                                            <option value="">Pilih Akun</option>
                                            @foreach ($accounts as $account)
                                                <option value="{{ $account->code }}"
                                                    {{ old('bundle-inventory-account', $product->f_inventory_coa_code ?? '') == $account->code ? 'selected' : '' }}>
                                                    {{ $account->code }} -
                                                    {{ $account->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <small class="form-text text-muted">*Ketentuan stok awal dapat menggunakan penyesuaian
                                    stok</small>

                                <div class="mt-3">
                                    <label class="form-label">Pelacakan Persediaan</label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    id="bundle-track-quantity"
                                                    {{ !empty(old('bundle-track-quantity', $product->track_qty_only == true)) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="bundle-track-quantity">
                                                    Hanya lacak kuantitas
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="bundle-track-batch"
                                                    {{ !empty(old('bundle-track-batch', $product->track_batch == true)) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="bundle-track-batch">
                                                    Batch
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        --}}
                    </div>

                    <!-- Bundle Configuration Tab -->
                    <div class="tab-pane fade mx-n6" id="bundle-config-tab" role="tabpanel">
                        <div class="mb-4">
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Resep Qty <span style="color: red;">*</span></label>
                                    <input type="text" class="form-control numeric-input text-end"
                                        id="recipe-qty" placeholder="65"
                                        value="{{ old('recipe-qty', $product->recipe_qty ?? '') }}" />
                                    <small class="form-text text-muted">Jumlah resep/batch yang menghasilkan qty batch bundle (contoh: 65 resep)</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Qty Batch Bundle <span style="color: red;">*</span></label>
                                    <input type="text" class="form-control numeric-input text-end"
                                        id="bundle-batch-qty" placeholder="0"
                                        value="{{ old('bundle-batch-qty', $product->bundle_batch_qty ?? '') }}" />
                                    <small class="form-text text-muted">Jumlah item yang dihasilkan dari resep qty (contoh: 73412 pcs)</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6>Komposisi Bundle</h6>
                                {{-- <button type="button" class="btn btn-primary" id="add-bundle-component">
                                    <i class="icon-base ti tabler-plus"></i>
                                    <span class="align-middle">Tambah Komponen</span>
                                </button> --}}
                            </div>
                            <div class="mx-n5 form-repeater-component">
                                <table class="table table-bordered" id="bundle-components-table">
                                    <thead>
                                        <tr>
                                            <th>Nama Produk</th>
                                            <th>Qty</th>
                                            <th>Unit</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bundle-components-tbody" data-repeater-list>
                                        @if ($productCompositions->isEmpty())
                                            <tr data-repeater-item class="bundle-component-row">
                                                <td>
                                                    <div class="position-relative">
                                                        <select class="select2Product form-select component-product">
                                                            <option value="">Pilih Produk</option>
                                                            @foreach ($products as $product_master)
                                                                <option value="{{ $product_master->id }}">
                                                                    {{ $product_master->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control numeric-input component-qty"
                                                        id="minimum-stock" min="1" value="1" />
                                                </td>
                                                <td>
                                                    <select class="form-select component-unit-select" required>
                                                        <option value="">Pilih Unit</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <button class="btn btn-label-danger" data-repeater-delete
                                                        type="button">
                                                        <i class="icon-base ti tabler-x me-1"></i>
                                                        <span class="align-middle">Hapus</span>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endif
                                        @foreach ($productCompositions as $row)
                                            <tr data-repeater-item class="bundle-component-row" data-product-id="{{ $row->f_product_composition_product_id }}" data-unit-id="{{ $row->f_unit_id }}">
                                                <input type="hidden" class="form-control composition_id"
                                                    value="{{ $row->id }}" />
                                                <td>
                                                    <div class="position-relative">
                                                        <select class="select2Product form-select component-product">
                                                            <option value="">Pilih Produk</option>
                                                            @foreach ($products as $product_master)
                                                                <option value="{{ $product_master->id }}"
                                                                    {{ $row->f_product_composition_product_id == $product_master->id ? 'selected' : '' }}>
                                                                    {{ $product_master->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control numeric-input component-qty"
                                                        id="minimum-stock" min="1" value="{{ $row->qty }}" />
                                                </td>
                                                <td>
                                                    <select class="form-select component-unit-select" required>
                                                        <option value="">Loading...</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <button class="btn btn-label-danger" data-repeater-delete
                                                        type="button">
                                                        <i class="icon-base ti tabler-x me-1"></i>
                                                        <span class="align-middle">Hapus</span>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="mb-0">
                                    <button class="btn btn-primary" data-repeater-create type="button">
                                        <i class="icon-base ti tabler-plus me-1"></i>
                                        <span class="align-middle">Tambah Komponen</span>
                                    </button>
                                </div>
                            </div>
                            <br>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6>Biaya Bundle</h6>
                            </div>
                            <div class="mx-n5 form-repeater-cost-components">
                                <table class="table table-bordered" id="bundle-components-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 50%;">Akun Biaya</th>
                                            <th style="width: 35%;">Harga</th>
                                            <th style="width: 15%;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="cost-components-tbody" data-repeater-list>
                                        @if ($productCompositionCost->isEmpty())
                                            <tr data-repeater-item class="cost-component-row">
                                                <td>
                                                    <div class="position-relative">
                                                        <select class="select2CoaCost form-select cost-coa">
                                                            <option value="">Pilih Akun</option>
                                                            @foreach ($coaList as $coa)
                                                                <option value="{{ $coa->code }}">{{ $coa->code }} -
                                                                    {{ $coa->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="text"
                                                        class="form-control currency-input text-end cost-value"
                                                        placeholder="Rp 0">
                                                </td>
                                                <td>
                                                    <button class="btn btn-label-danger" data-repeater-delete
                                                        type="button">
                                                        <i class="icon-base ti tabler-x me-1"></i>
                                                        <span class="align-middle">Hapus</span>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endif
                                        @foreach ($productCompositionCost as $row)
                                            <tr data-repeater-item class="cost-component-row">
                                                <input type="hidden" class="form-control cost-id"
                                                    value="{{ $row->id }}" />
                                                <td>
                                                    <div class="position-relative">
                                                        <select class="select2CoaCost form-select cost-coa">
                                                            <option value="">Pilih Akun</option>
                                                            @foreach($coaList as $coa)
                                                                <option 
                                                                value="{{ $coa->code }}" {{ $row->account_code == $coa->code ? 'selected' : '' }}>{{ $coa->code }} - {{ $coa->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control currency-input cost-value" id="target-owner" name="target_owner" 
                                                            placeholder="500000000" value={{$row->cost_value; }} required />
                                                    </div>
                                                </td>
                                                <td>
                                                    <button class="btn btn-label-danger" data-repeater-delete type="button">
                                                        <i class="icon-base ti tabler-x me-1"></i>
                                                        <span class="align-middle">Hapus</span>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="mb-0">
                                    <button class="btn btn-primary" data-repeater-create type="button">
                                        <i class="icon-base ti tabler-plus me-1"></i>
                                        <span class="align-middle">Tambah Biaya</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="mb-4" id="bundle-account-mapping" style="display: none;">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6>Pemetaan Akun Bundle</h6>
                                {{-- <button type="button" class="btn btn-sm btn-secondary" id="add-bundle-account">
                                    <i class="icon-base ti tabler-plus"></i> Tambah Akun
                                </button> --}}
                            </div>
                            <small class="form-text text-muted mb-3">Anda bisa mengisi ini jika Monitor persediaan barang
                                tercentang</small>
                            <div class="mx-n5 form-repeater-accounts">
                                <table class="table table-bordered" id="bundle-accounts-table">
                                    <thead>
                                        <tr>
                                            <th>Akun</th>
                                            <th>Biaya</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bundle-accounts-tbody" data-repeater-list>
                                        @if ($productExpenses->isEmpty())
                                            <tr data-repeater-item class="bundle-account-row">
                                                <td>
                                                    <div class="position-relative">
                                                        <select class="select2Coa form-select account-select">
                                                            <option value="">Pilih Akun</option>
                                                            @foreach ($accounts as $account)
                                                                <option value="{{ $account->code }}">{{ $account->code }}
                                                                    -
                                                                    {{ $account->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="text"
                                                        class="form-control currency-input text-end account-cost"
                                                        placeholder="Rp 0">
                                                </td>
                                                <td>
                                                    <button class="btn btn-label-danger" data-repeater-delete
                                                        type="button">
                                                        <i class="icon-base ti tabler-x me-1"></i>
                                                        <span class="align-middle">Hapus</span>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endif
                                        @foreach ($productExpenses as $row)
                                            <tr data-repeater-item class="bundle-account-row">
                                                <input type="hidden" class="form-control expense_id"
                                                    value="{{ $row->id }}" />
                                                <td>
                                                    <div class="position-relative">
                                                        <select class="select2Coa form-select account-select">
                                                            <option value="">Pilih Akun</option>
                                                            @foreach ($accounts as $account)
                                                                <option value="{{ $account->code }}"
                                                                    {{ $row->f_expense_coa_code == $account->code ? 'selected' : '' }}>
                                                                    {{ $account->code }} -
                                                                    {{ $account->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="text"
                                                        class="form-control currency-input text-end account-cost"
                                                        value="{{ $row->expense_price ?? '0' }}">
                                                </td>
                                                <td>
                                                    <button class="btn btn-label-danger" data-repeater-delete
                                                        type="button">
                                                        <i class="icon-base ti tabler-x me-1"></i>
                                                        <span class="align-middle">Hapus</span>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                                <div class="mb-0">
                                    <button class="btn btn-primary" data-repeater-create type="button">
                                        <i class="icon-base ti tabler-plus me-1"></i>
                                        <span class="align-middle">Tambah Akun</span>
                                    </button>
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
                    <div class="row g-2">
                        <div class="col">
                            <select class="global-select2 form-select" id="product-tags" name="product-tags[]" multiple
                                data-allow-clear="false">
                                @foreach ($tags as $tag)
                                    <option value="{{ $tag->id }}" data-color="{{ $tag->tag_color_code }}"
                                        @if (in_array($tag->id, $product->f_tag_codes)) selected @endif>{{ $tag->code }} -
                                        {{ $tag->name }}</option>
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
                    <small class="form-text text-muted">Pilih satu atau lebih tag untuk kategorisasi produk</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Memo (Internal)</label>
                    <textarea class="form-control" id="product-memo" name="product-memo" rows="2"
                        placeholder="Catatan internal untuk produk ini">{{ old('product-memo', $product->internal_memo ?? '') }}</textarea>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 text-end">
                <button type="button" class="btn btn-label-danger me-3" id="cancel-btn">Batalkan</button>
                <button type="button" class="btn btn-success" id="submit-btn">Simpan</button>
            </div>
        </form>
    </div>
@endsection


@section('js_script')
    <script>
        $(function() {
            $('.currency-input').each(function() {
                const val = $(this).val();
                $(this).val(convertToIndonesianFormat(val));
            });
            $('.numeric-input').each(function() {
                const val = $(this).val();
                // Use toIndonesianNumberFormat to strip trailing zeros for these fields
                if ($(this).hasClass('conversion-factor') || $(this).attr('id') === 'recipe-qty' || $(this).attr('id') === 'bundle-batch-qty') {
                    $(this).val(toIndonesianNumberFormat(val));
                } else {
                    $(this).val(convertToIndonesianFormat(val));
                }
            });
            initCleaveCurrency(document);

            let currentProductType = null;
            let bundleComponentCounter = 0;
            let bundleAccountCounter = 0;

            const defaultNoTaxId = $('#default-tax-id').val()

            $('select.select2Product').each(function() {
                $(this).select2({
                    placeholder: "Pilih Produk",
                    allowClear: true,
                    dropdownParent: $(this).parent(),
                    width: '100%'
                });
            });

            $('select.select2Coa').each(function() {
                $(this).select2({
                    placeholder: "Pilih COA",
                    allowClear: true,
                    dropdownParent: $(this).parent(),
                    width: '100%'
                });
            });

            $('select.select2CoaCost').each(function() {
                $(this).select2({
                    placeholder: "Pilih Akun",
                    allowClear: true,
                    dropdownParent: $(this).parent(),
                    width: '100%'
                });
            });

            $('.form-repeater-component').repeater({
                show: function() {
                    $(this).slideDown(() => {
                        $(this).find('.select2-container').remove();
                        $(this).find('select.select2Product').removeClass(
                            'select2-hidden-accessible').removeAttr(
                            'data-select2-id tabindex aria-hidden');
                        $(this).find('select.select2Product').each(function() {
                            if ($.fn.select2 && $(this).data('select2')) {
                                $(this).select2('destroy');
                            }
                            $(this).select2({
                                placeholder: "Pilih Produk",
                                allowClear: true,
                                dropdownParent: $(this).parent(),
                                width: '100%'
                            });
                        });
                        initCleaveCurrency(document);
                    });
                },
                hide: function(remove) {
                    $(this).slideUp(remove);
                }
            });

            $('.form-repeater-accounts').repeater({
                show: function() {
                    $(this).slideDown(() => {
                        $(this).find('.select2-container').remove();
                        $(this).find('select.select2Coa').removeClass(
                            'select2-hidden-accessible').removeAttr(
                            'data-select2-id tabindex aria-hidden');
                        $(this).find('select.select2Coa').each(function() {
                            if ($.fn.select2 && $(this).data('select2')) {
                                $(this).select2('destroy');
                            }
                            $(this).select2({
                                placeholder: "Pilih COA",
                                allowClear: true,
                                dropdownParent: $(this).parent(),
                                width: '100%'
                            });
                        });
                        initCleaveCurrency(document);
                    });
                },
                hide: function(remove) {
                    $(this).slideUp(remove);
                }
            });

            $('.form-repeater-cost-components').repeater({
                show: function() {
                    $(this).slideDown(() => {
                        // Remove any existing Select2 container and reinitialize Select2
                        $(this).find('.select2-container').remove();
                        $(this).find('.select2CoaCost').removeClass('select2-hidden-accessible').removeAttr(
                            'data-select2-id tabindex aria-hidden');
                        $(this).find('.select2CoaCost').each(function() {
                            if ($.fn.select2 && $(this).data('select2')) {
                                $(this).select2('destroy');
                            }
                            $(this).select2({
                                placeholder: "Pilih Akun",
                                allowClear: true,
                                dropdownParent: $(this).parent(),
                                width: '100%'
                            });
                        });

                        initCleaveCurrency($(this));
                        // Initialize Cleave for dynamically added "Harga" input boxes
                        
                    });
                },
                hide: function(remove) {
                    $(this).slideUp(remove);
                }
            });

            // Product Units Repeater
            $('.form-repeater-product-units').repeater({
                show: function() {
                    // Explicitly remove inline display:none style and show the row
                    $(this).removeAttr('style').css('display', '');

                    $(this).slideDown(() => {
                        // Remove existing Select2 containers and reinitialize
                        $(this).find('.select2-container').remove();
                        $(this).find('select.unit-select').removeClass(
                            'select2-hidden-accessible').removeAttr(
                            'data-select2-id tabindex aria-hidden');
                        $(this).find('select.unit-select').each(function() {
                            if ($.fn.select2 && $(this).data('select2')) {
                                $(this).select2('destroy');
                            }
                            $(this).select2({
                                placeholder: "Pilih Unit",
                                allowClear: true,
                                dropdownParent: $(this).parent(),
                                width: '100%'
                            });
                        });
                        // Initialize Cleave for numeric inputs in the new row
                        initCleaveCurrency(this);
                        updateUnitDisplays();
                    });
                },
                hide: function(remove) {
                    $(this).slideUp(() => {
                        remove();
                        updateUnitDisplays();
                    });
                }
            });

            // Update unit displays when unit selection changes
            $(document).on('change', '#product-units-tbody .unit-select', function() {
                const selectedUnit = $(this).val();
                const baseUnitId = $('#unit-select').val();

                // Check if trying to select the base unit
                if (selectedUnit === baseUnitId && selectedUnit !== '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Unit Tidak Valid',
                        text: 'Unit ini sudah menjadi unit dasar. Silakan pilih unit lain.',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    $(this).val('');
                    updateUnitDisplays();
                    return;
                }

                // Validate unit uniqueness
                let isDuplicate = false;
                $('#product-units-tbody .unit-select').not(this).each(function() {
                    if ($(this).val() === selectedUnit && selectedUnit !== '') {
                        isDuplicate = true;
                        return false;
                    }
                });

                if (isDuplicate) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Unit Duplikat',
                        text: 'Unit ini sudah ditambahkan. Silakan pilih unit lain.',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    $(this).val('');
                }

                updateUnitDisplays();
            });

            function updateUnitDisplays() {
                // Get base unit from main unit selector
                const $mainUnitSelect = $('#unit-select');
                const baseUnitOption = $mainUnitSelect.find('option:selected');
                let baseUnitName = baseUnitOption.text() || 'Unit Dasar';

                // Update all rows
                $('#product-units-tbody tr.product-unit-row').each(function() {
                    const $row = $(this);
                    const $select = $row.find('.unit-select');
                    const selectedOption = $select.find('option:selected');
                    const selectedUnitName = selectedOption.data('name') || selectedOption.text() || '';

                    $row.find('.unit-name-display').text(selectedUnitName);
                    $row.find('.base-unit-display').text(baseUnitName);
                });
            }

            // Update unit displays when main unit selector changes
            $('#unit-select').change(function() {
                updateUnitDisplays();
            });

            // Initialize Select2 for unit selects in product units table
            $('#product-units-tbody select.unit-select').each(function() {
                $(this).select2({
                    placeholder: "Pilih Unit",
                    allowClear: true,
                    dropdownParent: $(this).parent(),
                    width: '100%'
                });
            });

            // Initialize unit displays on page load
            updateUnitDisplays();

            // Product type change handler with confirmation
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
                            cancelButton: 'btn btn-label-secondary'
                        },
                        buttonsStyling: false
                    }).then(function(result) {
                        if (result.value) {
                            switchProductType(newType);
                        } else {
                            // Revert selection
                            $('input[name="product-type"][value="' + currentProductType + '"]')
                                .prop('checked', true);
                        }
                    });
                } else {
                    switchProductType(newType);
                }
            });

            const checkedType = $('input[name="product-type"]:checked').val();
            if (checkedType) {
                switchProductType(checkedType, true);
            }

            function switchProductType(type, isInitial = false) {
                currentProductType = type;

                let isEnableSinglePurchaseChecked = $('#enable-purchase').is(':checked');
                let isEnableSingleSalesChecked = $('#enable-sales').is(':checked');
                let isEnableSingleInventoryChecked = $('#enable-inventory').is(':checked');

                let isEnableBundlePurchaseChecked = $('#bundle-enable-purchase').is(':checked');
                let isEnableBundleSalesChecked = $('#bundle-enable-sales').is(':checked');
                let isEnableBundleInventoryChecked = $('#bundle-enable-inventory').is(':checked');
                // Hide all content first
                $('#single-content, #bundle-content').hide();

                // Reset form fields
                if (isInitial === false) {
                    resetConditionalFields();
                }

                // Show appropriate content
                if (type === 'single') {
                    $('#single-content').show();
                    if (isEnableSinglePurchaseChecked === true) {
                        $('#enable-purchase').prop('checked', true);
                        $('#purchase-fields').show();
                    }
                    if (isEnableSingleSalesChecked === true) {
                        $('#enable-sales').prop('checked', true);
                        $('#sales-fields').show();
                    }
                    if (isEnableSingleInventoryChecked === true) {
                        $('#enable-inventory').prop('checked', true);
                        $('#inventory-fields').show();
                    }
                } else if (type === 'bundle') {
                    $('#bundle-content').show();
                    if (isEnableBundlePurchaseChecked === true) {
                        $('#bundle-enable-purchase').prop('checked', true);
                        $('#bundle-purchase-fields').show();
                    }
                    if (isEnableBundleSalesChecked === true) {
                        $('#bundle-enable-sales').prop('checked', true);
                        $('#bundle-sales-fields').show();
                    }
                    if (isEnableBundleInventoryChecked === true) {
                        $('#bundle-enable-inventory').prop('checked', true);
                        $('#bundle-inventory-fields').show();

                        $('#bundle-inventory-fields').toggle(this.checked);
                        $('#bundle-account-mapping').toggle(this.checked);
                    }
                }

            }

            function resetConditionalFields() {
                // Reset checkboxes
                $('#enable-purchase, #enable-sales, #enable-inventory').prop('checked', false);
                $('#bundle-enable-purchase, #bundle-enable-sales, #bundle-enable-inventory').prop('checked', false);

                // Hide conditional fields
                $('#purchase-fields, #sales-fields, #inventory-fields').hide();
                $('#bundle-purchase-fields, #bundle-sales-fields, #bundle-inventory-fields').hide();

                // Reset form values
                $('#productForm input[type="text"], #productForm input[type="number"]').not(
                    '#product-code, #product-name, #product-status').val(
                    '');
                // $('#productForm select').not('#unit-select,#category-select').val('').trigger('change');
            }

            // Conditional field toggles - Single Product
            $('#enable-purchase').change(function() {
                $('#purchase-fields').toggle(this.checked);
            });

            $('#enable-sales').change(function() {
                $('#sales-fields').toggle(this.checked);
            });

            $('#enable-inventory').change(function() {
                $('#inventory-fields').toggle(this.checked);
            });

            // Conditional field toggles - Bundle Product
            $('#bundle-enable-purchase').change(function() {
                $('#bundle-purchase-fields').toggle(this.checked);
            });

            $('#bundle-enable-sales').change(function() {
                $('#bundle-sales-fields').toggle(this.checked);
            });

            $('#bundle-enable-inventory').change(function() {
                $('#bundle-inventory-fields').toggle(this.checked);
                $('#bundle-account-mapping').toggle(this.checked);
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

            function onComponentProductChange(thisvar) {
                const productId = $(thisvar).val();
                const $row = $(thisvar).closest('tr');
                const $unitSelect = $row.find('.component-unit-select');

                if (!productId) {
                    // Destroy Select2 if exists
                    if ($unitSelect.data('select2')) {
                        $unitSelect.select2('destroy');
                    }
                    $unitSelect.html('<option value="">Pilih Unit</option>');
                    return;
                }

                // Load units for the selected product
                $.ajax({
                    url: `/master_data/product/${productId}/units`,
                    method: 'GET',
                    success: function(response) {
                        if (response.success && response.data.length > 0) {
                            let options = '<option value="">Pilih Unit</option>';
                            response.data.forEach(function(unit) {
                                const badge = unit.is_default == 1 ? ' (Default)' : '';
                                options += `<option value="${unit.f_unit_id}">${unit.unit_name}${badge}</option>`;
                            });

                            // Destroy Select2 if exists
                            if ($unitSelect.data('select2')) {
                                $unitSelect.select2('destroy');
                            }

                            $unitSelect.html(options);

                            // Initialize Select2
                            $unitSelect.select2({
                                placeholder: "Pilih Unit",
                                allowClear: true,
                                dropdownParent: $unitSelect.parent(),
                                width: '100%'
                            });

                            // Check if row has data-unit-id attribute (for existing rows)
                            const savedUnitId = $row.data('unit-id');
                            if (savedUnitId) {
                                $unitSelect.val(savedUnitId).trigger('change');
                            } else {
                                // Auto-select default unit if exists
                                const defaultUnit = response.data.find(u => u.is_default == 1);
                                if (defaultUnit) {
                                    $unitSelect.val(defaultUnit.f_unit_id).trigger('change');
                                }
                            }
                        } else {
                            if ($unitSelect.data('select2')) {
                                $unitSelect.select2('destroy');
                            }
                            $unitSelect.html('<option value="">No units available</option>');
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load product units'
                        });
                        if ($unitSelect.data('select2')) {
                            $unitSelect.select2('destroy');
                        }
                        $unitSelect.html('<option value="">Error loading units</option>');
                    }
                });
            }

            // Bundle component events
            $(document).on('change', '.component-product', function() {
                onComponentProductChange(this)
            });


            $('.component-product').each(function(index, element) {
                onComponentProductChange(this)
            });

            // function updateComponentPriceRow($row) {
            //     const qty = convertToIndonesianFormat($row.find('.component-qty').val());
            //     alert('qty: ' + qty);   
            //     const selectedOption = $row.find('.component-product option:selected');
            //     // const unitPrice = parseFloat(selectedOption.data('price') || 0);
            //     // const totalPrice = unitPrice * qty;
            // }
            // $(document).on('input', '.component-qty', function() {
            //     const $row = $(this).closest('tr');
            //     updateComponentPriceRow($row);
            // });

            $(document).on('click', '.remove-account', function() {
                const id = $(this).data('id');
                $(`.bundle-account-row[data-id="${id}"]`).remove();
            });

            // Form submission
            $('#submit-btn').click(function(e) {
                e.preventDefault();

                // Validate required fields
                let isValid = true;
                const isProductTypeEmpty = !$('input[name="product-type"]:checked').length
                const isCurrentProductTypeSingle = currentProductType === 'single'
                const isSingleEnablePurchase = $('#enable-purchase').is(':checked')
                const isSingleEnableSales = $('#enable-sales').is(':checked')
                const isSingleEnableInventory = $('#enable-inventory').is(':checked')
                
                const isCurrentProductTypeBundle = currentProductType === 'bundle'
                const isBundleEnablePurchase = $('#bundle-enable-purchase').is(':checked')
                const isBundleEnableSales = $('#bundle-enable-sales').is(':checked')
                const isBundleEnableInventory = $('#bundle-enable-inventory').is(':checked')

                const requiredFields = [
                    'product-name', 
                    'unit-select', 
                    'product-code',
                    'product-status',
                ];

                if (isCurrentProductTypeSingle) {
                    if (isSingleEnablePurchase) {
                        requiredFields.push(...[
                            'purchase-price',
                            // 'purchase-account',
                            'purchase-tax',
                        ])
                    }
                    if (isSingleEnableSales) {
                        requiredFields.push(...[
                            'sales-price',
                            // 'sales-account',
                            'sales-tax',
                        ])
                    }
                    if (isSingleEnableInventory) {
                        requiredFields.push(...[
                            'minimum-stock',
                            'inventory-account',
                        ])
                    }
                }

                if (isCurrentProductTypeBundle) {
                    if (isBundleEnablePurchase) {
                        requiredFields.push(...[
                            'bundle-purchase-price',
                            // 'bundle-purchase-account',
                            'bundle-purchase-tax',
                        ])
                    }
                    if (isBundleEnableSales) {
                        requiredFields.push(...[
                            'bundle-sales-price',
                            // 'bundle-sales-account',
                            // 'bundle-sales-tax',
                        ])
                    }
                    if (isBundleEnableInventory) {
                        requiredFields.push(...[
                            'bundle-minimum-stock',
                            'bundle-inventory-account',
                        ])
                    }

                    // recipe_qty and bundle_batch_qty are always required for bundle
                    requiredFields.push('recipe-qty', 'bundle-batch-qty');
                }

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
                    // Switch to the tab containing the first invalid field
                    const bundlePricingFields = ['bundle-purchase-price', 'bundle-purchase-tax', 'bundle-sales-price', 'bundle-sales-tax', 'bundle-minimum-stock', 'bundle-inventory-account'];
                    const bundleConfigFields = ['recipe-qty', 'bundle-batch-qty'];
                    for (const fieldId of requiredFields) {
                        if ($('#' + fieldId).hasClass('is-invalid')) {
                            if (bundlePricingFields.includes(fieldId)) {
                                bootstrap.Tab.getOrCreateInstance(document.getElementById('bundle-pricing-list-item')).show();
                            } else if (bundleConfigFields.includes(fieldId)) {
                                bootstrap.Tab.getOrCreateInstance(document.getElementById('bundle-config-list-item')).show();
                            }
                            break;
                        }
                    }
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

                // Validate product type selection
                if (!$('input[name="product-type"]:checked').length) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: 'Silakan pilih tipe produk.',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    return;
                }

                // Additional validation based on product type and enabled features
                if (currentProductType === 'bundle') {
                    if ($('#bundle-components-tbody tr').length === 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Validasi Gagal',
                            text: 'Produk bundle harus memiliki minimal satu komponen.',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        });
                        return;
                    }
                }

                // Validate Product Units - only validate visible rows (exclude hidden template)
                let hasEmptyUnit = false;
                $('#product-units-tbody tr.product-unit-row:visible .unit-select').each(function() {
                    const val = $(this).val();
                    if (!val || val === '' || val === null) {
                        hasEmptyUnit = true;
                        return false;
                    }
                });

                if (hasEmptyUnit) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: 'Semua unit yang ditambahkan harus dipilih. Hapus baris kosong atau pilih unit.',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    return;
                }

                function getProductFormJson() {
                    const data = {};

                    data.id = $('#product-id').val();
                    // Basic info
                    data.name = $('#product-name').val();
                    data.unit_id = $('#unit-select').val();
                    data.code = $('#product-code').val();
                    data.category_id = $('#category-select').val();
                    data.description = $('#product-description').val();
                    data.memo = $('#product-memo').val();
                    data.status = $('#product-status').val();
                    data.tags = $('#product-tags').val() || [];

                    // Product type
                    data.type = $('input[name="product-type"]:checked').val();

                    // Image (file input: send filename or handle separately)
                    // const imageInput = $('#product-image')[0];
                    // data.image = imageInput && imageInput.files.length ? imageInput.files[0].name : null;

                    const imageInput = $('#product-image')[0];
                    if (imageInput && imageInput.files.length) {
                        data.image = imageInput && imageInput.files.length ? imageInput.files[0].name :
                            null;
                    }
                    // Do NOT append 'image' if no file is selected

                    // Single product fields
                    if (data.type === 'single') {
                        data.purchase_enabled = $('#enable-purchase').is(':checked');
                        data.sales_enabled = $('#enable-sales').is(':checked');
                        data.inventory_enabled = $('#enable-inventory').is(':checked');

                        if (data.purchase_enabled) {
                            data.purchase_price = parseCurrency($('#purchase-price').val());
                            data.purchase_account = $('#purchase-account').val();
                            data.purchase_tax = $('#purchase-tax').val();
                        }
                        if (data.sales_enabled) {
                            data.sales_price = parseCurrency($('#sales-price').val());
                            data.sales_account = $('#sales-account').val();
                            data.sales_tax = $('#sales-tax').val();
                        }
                        if (data.inventory_enabled) {
                            data.minimum_stock = parseCurrency($('#minimum-stock').val());
                            data.inventory_account = $('#inventory-account').val();
                            data.track_quantity = $('#track-quantity').is(':checked');
                            data.track_batch = $('#track-batch').is(':checked');
                        }
                    }

                    // Bundle product fields
                    if (data.type === 'bundle') {
                        data.is_packaging = $('#bundle-packaging').is(':checked') ? 'true' : 'false';
                        data.bundle_purchase_enabled = $('#bundle-enable-purchase').is(':checked');
                        data.bundle_sales_enabled = $('#bundle-enable-sales').is(':checked');
                        data.bundle_inventory_enabled = $('#bundle-enable-inventory').is(':checked');
                        data.recipe_qty = parseCurrency($('#recipe-qty').val());
                        data.bundle_batch_qty = parseCurrency($('#bundle-batch-qty').val());

                        if (data.bundle_purchase_enabled) {
                            data.bundle_purchase_price = parseCurrency($('#bundle-purchase-price').val());
                            data.bundle_purchase_account = $('#bundle-purchase-account').val();
                            data.bundle_purchase_tax = $('#bundle-purchase-tax').val();
                        }
                        if (data.bundle_sales_enabled) {
                            data.bundle_sales_price = parseCurrency($('#bundle-sales-price').val());
                            data.bundle_sales_account = $('#bundle-sales-account').val();
                            data.bundle_sales_tax = $('#bundle-sales-tax').val();
                        }
                        if (data.bundle_inventory_enabled) {
                            data.bundle_minimum_stock = parseCurrency($('#bundle-minimum-stock').val());
                            data.bundle_inventory_account = $('#bundle-inventory-account').val();
                            data.bundle_track_quantity = $('#bundle-track-quantity').is(':checked');
                            data.bundle_track_batch = $('#bundle-track-batch').is(':checked');
                        }

                        data.bundle_components = [];

                        $('#bundle-components-tbody tr.bundle-component-row').each(function() {
                            const productId = $(this).find('.component-product').val();
                            const compositionId = $(this).find('.composition_id').val();
                            const unitId = $(this).find('.component-unit-select').val();
                            const qty = parseCurrency($(this).find('.component-qty').val());

                            if (productId && productId !== "null" && qty && qty !== "null" && unitId && unitId !== "null") {
                                const component = {
                                    product_id: productId,
                                    unit_id: unitId,
                                    qty: qty,
                                };
                                if (compositionId && compositionId !== 'null') {
                                    component.composition_id = compositionId;
                                }
                                data.bundle_components.push(component);
                            }
                        });
                        
                        data.cost = [];
                        $('#cost-components-tbody tr.cost-component-row').each(function() {
                            const cost_id = $(this).find('.cost-id').val();
                            const coa = $(this).find('.cost-coa').val();
                            const value = parseCurrency($(this).find('.cost-value').val());
                            console.log(coa, value);
                            if (coa && coa !== "null" && value && value !== "null") {
                                data.cost.push({
                                    cost_id: cost_id,
                                    coa: coa,
                                    value: value
                                });
                            }
                        });

                        data.bundle_accounts = [];

                        $('#bundle-accounts-tbody tr.bundle-account-row').each(function() {
                            const expenseId = $(this).find('.expense_id').val();
                            const account = $(this).find('.account-select').val();
                            const costRaw = $(this).find('.account-cost').val();
                            const cost = costRaw ? costRaw.replace(/[^0-9.]/g, '') : null;

                            if (account && account !== "null" && cost && cost !== "null") {
                                data.bundle_accounts.push({
                                    account: account,
                                    expense_id: expenseId,
                                    cost: cost
                                });
                            }
                        });
                    }

                    // Product Units
                    data.product_units = [];

                    // First add the base unit from main unit selector
                    const baseUnitId = $('#unit-select').val();
                    console.log('Base Unit ID:', baseUnitId);

                    if (baseUnitId && baseUnitId !== '' && baseUnitId !== null) {
                        data.product_units.push({
                            unit_id: parseInt(baseUnitId),
                            is_default: 1,
                            conversion_factor: 1,
                            status: 'ACTIVE',
                            note: ''
                        });
                    }

                    // Then add additional units from the repeater (only visible rows)
                    $('#product-units-tbody tr.product-unit-row:visible').each(function() {
                        const $row = $(this);
                        const $select = $row.find('.unit-select');
                        const unitId = $select.val();
                        const conversionFactor = $row.find('.conversion-factor').val();

                        console.log('Additional Unit ID:', unitId, 'Conversion:', conversionFactor);

                        if (unitId && unitId !== '' && unitId !== null && unitId !== baseUnitId) {
                            data.product_units.push({
                                unit_id: parseInt(unitId),
                                is_default: 0,
                                conversion_factor: parseFloat(conversionFactor) || 1,
                                status: 'ACTIVE',
                                note: ''
                            });
                        }
                    });

                    console.log('Product Units Data:', data.product_units);

                    return data;
                }

                Swal.fire({
                    title: 'Simpan Produk?',
                    text: 'Cek kembali data yang sudah diisi!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan!',
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-label-secondary'
                    },
                    buttonsStyling: false
                }).then(function(result) {
                    if (result.value) {
                        $('#submit-btn').prop('disabled', true).html(
                            '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Loading...'
                        );

                        const product_data = getProductFormJson();

                        const formData = new FormData();

                        for (const key in product_data) {
                            if (Array.isArray(product_data[key])) {
                                if (key === 'tags') {
                                    product_data[key].forEach(tag => formData.append('tags[]',
                                        tag));
                                } else {
                                    product_data[key].forEach((item, idx) => {
                                        for (const field in item) {
                                            formData.append(`${key}[${idx}][${field}]`,
                                                item[field]);
                                        }
                                    });
                                }
                            } else {
                                formData.append(key, product_data[key]);
                            }
                        }

                        // Append image file
                        const imageInput = $('#product-image')[0];
                        if (imageInput && imageInput.files.length) {
                            formData.append('image', imageInput.files[0]);
                        }

                        formData.append('_token', '{{ csrf_token() }}');


                        $.ajax({
                            url: "{{ route('master_data.product.update') }}",
                            method: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Produk berhasil diubah.',
                                    customClass: {
                                        confirmButton: 'btn btn-success'
                                    },
                                    buttonsStyling: false
                                }).then(function() {
                                    window.location.href =
                                        "{{ route('master_data.product.index') }}";
                                });
                            },
                            error: function(xhr) {
                                const res = xhr.responseJSON;
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal menyimpan',
                                    html: res?.message ||
                                        'Terjadi kesalahan saat menyimpan data.',
                                    customClass: {
                                        confirmButton: 'btn btn-primary'
                                    },
                                    buttonsStyling: false
                                });
                            },
                            complete: function() {
                                $('#submit-btn').prop('disabled', false).html(
                                    'Simpan Data');
                            }
                        });
                    }
                });
            });

            // Cancel button
            $('#cancel-btn').click(function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Batalkan Pembuatan?',
                    text: 'Data yang sudah diisi akan hilang!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Batalkan!',
                    cancelButtonText: 'Tidak',
                    customClass: {
                        confirmButton: 'btn btn-danger',
                        cancelButton: 'btn btn-label-secondary'
                    },
                    buttonsStyling: false
                }).then(function(result) {
                    if (result.value) {
                        window.location.href = "{{ route('master_data.product.index') }}";
                    }
                });
            });

            const repeaterList = document.querySelector('.form-repeater-component [data-repeater-list]');
            if (repeaterList) {
                const observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        mutation.addedNodes.forEach(function(node) {
                            if (node.nodeType === 1 && node.matches(
                                    '[data-repeater-item]')) {
                                $(node).find('.select2-container').remove();
                                // Remove select2 classes and attributes from select
                                $(node).find('select.select2Product')
                                    .removeClass('select2-hidden-accessible')
                                    .removeAttr('data-select2-id tabindex aria-hidden');
                                // Remove selected attribute from all options in new item
                                $(node).find('select.select2Product option').removeAttr(
                                    'selected');
                                $(node).find('select.select2Product').val('').trigger(
                                    'change');
                                // Destroy and re-init Select2
                                $(node).find('select.select2Product').each(function() {
                                    if ($(this).data('select2')) {
                                        $(this).select2('destroy');
                                    }
                                    $(this).select2({
                                        placeholder: "Select value",
                                        allowClear: true,
                                        dropdownParent: $(this).parent(),
                                        width: '100%'
                                    });
                                });
                                $(node).find('.component-qty').val('0');
                                $(node).find('.component-unit').val('');
                                $(node).find('.composition_id').val(null);
                            }
                        });
                    });
                });
                observer.observe(repeaterList, {
                    childList: true
                });
            }

            const repeaterListAccounts = document.querySelector('.form-repeater-accounts [data-repeater-list]');
            if (repeaterListAccounts) {
                const observercost = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        mutation.addedNodes.forEach(function(node) {
                            if (node.nodeType === 1 && node.matches(
                                    '[data-repeater-item]')) {

                                $(node).find('.select2-container').remove();
                                // Remove select2 classes and attributes from select
                                $(node).find('select.select2Coa')
                                    .removeClass('select2-hidden-accessible')
                                    .removeAttr('data-select2-id tabindex aria-hidden');
                                // Remove selected attribute from all options in new item
                                $(node).find('select.select2Coa option').removeAttr(
                                    'selected');
                                $(node).find('select.select2Coa').val('').trigger(
                                    'change');
                                // Destroy and re-init Select2
                                $(node).find('select.select2Coa').each(function() {
                                    if ($(this).data('select2')) {
                                        $(this).select2('destroy');
                                    }
                                    $(this).select2({
                                        placeholder: "Select value",
                                        allowClear: true,
                                        dropdownParent: $(this).parent(),
                                        width: '100%'
                                    });
                                });
                                $(node).find('.account-cost').val('0');
                                $(node).find('.expense_id').val(null);
                            }
                        });
                    });
                    
                    observer.observe(repeaterListAccounts, {
                        childList: true
                    });
                });

                const repeaterListCost = document.querySelector('.form-repeater-cost-components [data-repeater-list]');
                if (repeaterListCost) {
                    const observer = new MutationObserver(function(mutations) {
                        mutations.forEach(function(mutation) {
                            mutation.addedNodes.forEach(function(node) {
                                if (node.nodeType === 1 && node.matches(
                                        '[data-repeater-item]')) {
                                    
                                    $(node).find('.select2-container').remove();
                                    // Remove select2 classes and attributes from select
                                    
                                    $(node).find('select.select2CoaCost')
                                    .removeClass('select2-hidden-accessible')
                                    .removeAttr('data-select2-id tabindex aria-hidden');
                                    // Remove selected attribute from all options in new item
                                    $(node).find('select.select2CoaCost option').removeAttr(
                                        'selected');
                                    $(node).find('select.select2CoaCost').val('').trigger(
                                        'change');
                                    // Destroy and re-init Select2
                                    $(node).find('select.select2CoaCost').each(function() {
                                        if ($(this).data('select2')) {
                                            $(this).select2('destroy');
                                        }
                                        $(this).select2({
                                            placeholder: "Select value",
                                            allowClear: true,
                                            dropdownParent: $(this).parent(),
                                            width: '100%'
                                        });
                                    });
                                    $(node).find('.cost-value').val('');
                                    $(node).find('.cost-id').val(null);
                                }
                            });
                        });
                    });
                    observer.observe(repeaterListCost, {
                        childList: true
                    });
                }
                
            }
        });
    </script>
@endsection
