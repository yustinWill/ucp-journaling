@extends('layouts.default')
@section('page_title')
    Tambah Produk Baru
@stop
@section('content')
    <div class="card mb-6">
        <h4 class="card-header">Tambah Produk Baru</h4>
        <form class="card-body" id="productForm" autocomplete="off">
            <!-- Info Produk Section -->
            <div class="section-header mt-12">
                <h6>Info Produk</h6>
                <hr class="mx-n6">
            </div>

            <!-- Row 1: Basic Information -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Nama Produk <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="product-name" placeholder="Masukkan nama produk"
                        maxlength="255" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Unit <span style="color: red;">*</span></label>
                    <div class="row g-2">
                        <div class="col">
                            <select class="global-select2 form-select" id="unit-select" data-allow-clear="false">
                                <option value="">Pilih Unit</option>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
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
                    <input type="text" class="form-control" id="product-code" placeholder="PROD-2025-001" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kategori Produk</label>
                    <div class="row g-2">
                        <div class="col">
                            <select class="global-select2 form-select" id="category-select" data-allow-clear="false">
                                <option value="">Pilih Kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
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
                    <select class="global-select2 form-select" id="product-status" name="status" data-allow-clear="false"
                        required>
                        <option value="">Pilih Status</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status['value'] }}">{{ $status['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Deskripsi</label>
                    <textarea class="form-control" id="product-description" rows="3" placeholder="Deskripsi detail produk..."
                        maxlength="1000"></textarea>
                    <small class="form-text text-muted">Maksimal 1000 karakter</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Gambar Produk</label>
                    <div class="image-upload-area"
                        style="height: 200px; width: 200px; border: 2px dashed #ccc; display: flex; align-items: center; justify-content: center; flex-direction: column;">
                        <div id="image-placeholder" class="text-center">
                            <i class="icon-base ti tabler-photo" style="font-size: 48px; color: #ccc;"></i>
                            <p class="text-muted mt-2">Pilih gambar produk</p>
                            <small class="text-muted">Format file JPG atau PNG (maks. 5 MB)</small>
                        </div>
                        <img id="image-preview"
                            style="display: none; max-width: 100%; max-height: 100%; object-fit: cover;" />
                        <input type="file" id="product-image" class="form-control" accept=".jpg,.jpeg,.png"
                            style="display: none;" />
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary mt-2"
                        onclick="document.getElementById('product-image').click()">
                        Pilih Gambar
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
                        <input class="form-check-input" type="radio" name="product-type" id="type-single"
                            value="single">
                        <label class="form-check-label" for="type-single">
                            Single
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="product-type" id="type-bundle"
                            value="bundle">
                        <label class="form-check-label" for="type-bundle">
                            Bundle
                        </label>
                    </div>
                </div>
            </div>

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
                        <input class="form-check-input" type="checkbox" id="enable-purchase">
                        <label class="form-check-label" for="enable-purchase">
                            Saya beli produk ini
                        </label>
                    </div>

                    <div id="purchase-fields" style="display: none;">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Harga Satuan <span style="color: red;">*</span></label>
                                <input type="text" class="form-control currency-input text-end" id="purchase-price"
                                    placeholder="Rp 0" />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Akun Pembelian <span style="color: red;">*</span></label>
                                <select class="global-select2 form-select flex-grow-1" id="purchase-account"
                                    data-allow-clear="false">
                                    <option value="">Pilih Akun</option>
                                    @foreach ($accounts as $account)
                                        <option value="{{ $account->code }}">{{ $account->code }} -
                                            {{ $account->name }}</option>
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
                                    data-allow-clear="false">
                                    <option value="">Pilih Pajak</option>
                                    @foreach ($taxes as $tax)
                                        <option value="{{ $tax->id }}">{{ $tax->name }}</option>
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
                        <input class="form-check-input" type="checkbox" id="enable-sales">
                        <label class="form-check-label" for="enable-sales">
                            Saya jual produk ini
                        </label>
                    </div>

                    <div id="sales-fields" style="display: none;">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Harga Satuan <span style="color: red;">*</span></label>
                                <input type="text" class="form-control currency-input text-end" id="sales-price"
                                    placeholder="Rp 0" />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Akun Penjualan <span style="color: red;">*</span></label>
                                <select class="global-select2 form-select flex-grow-1" id="sales-account"
                                    data-allow-clear="false">
                                    <option value="">Pilih Akun</option>
                                    @foreach ($accounts as $account)
                                        <option value="{{ $account->code }}">{{ $account->code }} -
                                            {{ $account->name }}</option>
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
                                <select class="global-select2 form-select flex-grow-1" id="sales-tax"
                                    data-allow-clear="false">
                                    <option value="">Pilih Pajak</option>
                                    @foreach ($taxes as $tax)
                                        <option value="{{ $tax->id }}">{{ $tax->name }}</option>
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

                <!-- Inventory Section -->
                <div class="mb-4">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="enable-inventory">
                        <label class="form-check-label" for="enable-inventory">
                            Monitoring Persediaan Barang
                        </label>
                    </div>

                    <div id="inventory-fields" style="display: none;">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Batas stok minimum <span style="color: red;">*</span></label>
                                <input type="text" class="form-control numeric-input text-end" id="minimum-stock"
                                    placeholder="0" />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Akun Persediaan Default <span
                                        style="color: red;">*</span></label>
                                <select class="global-select2 form-select flex-grow-1" id="inventory-account"
                                    data-allow-clear="false">
                                    <option value="">Pilih Akun</option>
                                    @foreach ($accounts as $account)
                                        <option value="{{ $account->code }}">{{ $account->code }} -
                                            {{ $account->name }}</option>
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
                        <small class="form-text text-muted">*Ketentuan stok awal dapat menggunakan penyesuaian stok</small>

                        <div class="mt-3">
                            <label class="form-label">Pelacakan Persediaan</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="track-quantity" checked>
                                        <label class="form-check-label" for="track-quantity">
                                            Hanya lacak kuantitas
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="track-batch">
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
                                <input class="form-check-input" type="checkbox" id="bundle-enable-purchase">
                                <label class="form-check-label" for="bundle-enable-purchase">
                                    Saya beli produk ini
                                </label>
                            </div>

                            <div id="bundle-purchase-fields" style="display: none;">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Harga Satuan <span style="color: red;">*</span></label>
                                        <input type="text" class="form-control currency-input text-end"
                                            id="bundle-purchase-price" placeholder="Rp 0" />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Akun Pembelian <span
                                                style="color: red;">*</span></label>
                                        <select class="global-select2 form-select" id="bundle-purchase-account"
                                            data-allow-clear="false">
                                            <option value="">Pilih Akun</option>
                                            @foreach ($accounts as $account)
                                                <option value="{{ $account->code }}">{{ $account->code }} -
                                                    {{ $account->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Pajak Pembelian <span
                                                style="color: red;">*</span></label>
                                        <select class="global-select2 form-select" id="bundle-purchase-tax"
                                            data-allow-clear="false">
                                            <option value="">Pilih Pajak</option>
                                            @foreach ($taxes as $tax)
                                                <option value="{{ $tax->id }}">{{ $tax->name }}</option>
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
                                <input class="form-check-input" type="checkbox" id="bundle-enable-sales">
                                <label class="form-check-label" for="bundle-enable-sales">
                                    Saya jual produk ini
                                </label>
                            </div>

                            <div id="bundle-sales-fields" style="display: none;">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Harga Satuan <span style="color: red;">*</span></label>
                                        <input type="text" class="form-control currency-input text-end"
                                            id="bundle-sales-price" placeholder="Rp 0" />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Akun Penjualan <span
                                                style="color: red;">*</span></label>
                                        <select class="global-select2 form-select" id="bundle-sales-account"
                                            data-allow-clear="false">
                                            <option value="">Pilih Akun</option>
                                            @foreach ($accounts as $account)
                                                <option value="{{ $account->code }}">{{ $account->code }} -
                                                    {{ $account->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Pajak Penjualan <span
                                                style="color: red;">*</span></label>
                                        <select class="global-select2 form-select" id="bundle-sales-tax"
                                            data-allow-clear="false">
                                            <option value="">Pilih Pajak</option>
                                            @foreach ($taxes as $tax)
                                                <option value="{{ $tax->id }}">{{ $tax->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <!-- Empty column for layout balance -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Inventory Section -->
                        <div class="mb-4">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="bundle-enable-inventory">
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
                                            id="bundle-minimum-stock" placeholder="0" />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Akun Persediaan Default <span
                                                style="color: red;">*</span></label>
                                        <select class="global-select2 form-select" id="bundle-inventory-account"
                                            data-allow-clear="false">
                                            <option value="">Pilih Akun</option>
                                            @foreach ($accounts as $account)
                                                <option value="{{ $account->code }}">{{ $account->code }} -
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
                                                    id="bundle-track-quantity" checked>
                                                <label class="form-check-label" for="bundle-track-quantity">
                                                    Hanya lacak kuantitas
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="bundle-track-batch">
                                                <label class="form-check-label" for="bundle-track-batch">
                                                    Batch
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bundle Configuration Tab -->
                    <div class="tab-pane fade mx-n6" id="bundle-config-tab" role="tabpanel">
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
                                        <tr data-repeater-item class="bundle-component-row">
                                            <td>
                                                <div class="position-relative">
                                                    <select class="select2Product form-select component-product">
                                                        <option value="">Pilih Produk</option>
                                                        @foreach ($products as $product)
                                                            <option value="{{ $product->id }}"
                                                                data-unit="{{ $product->unit_name ?? 'pcs' }}">
                                                                {{ $product->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control numeric-input component-qty"
                                                    id="minimum-stock" min="1" value="1" />
                                            </td>
                                            <td>
                                                <input type="text" class="form-control component-unit" readonly>
                                            </td>
                                            <td>
                                                <button class="btn btn-label-danger" data-repeater-delete type="button">
                                                    <i class="icon-base ti tabler-x me-1"></i>
                                                    <span class="align-middle">Hapus</span>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="mb-0">
                                    <button class="btn btn-primary" data-repeater-create type="button">
                                        <i class="icon-base ti tabler-plus me-1"></i>
                                        <span class="align-middle">Tambah Komponen</span>
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
                                        <tr data-repeater-item class="bundle-account-row">
                                            <td>
                                                <div class="position-relative">
                                                    <select class="select2Coa form-select account-select">
                                                        <option value="">Pilih Akun</option>
                                                        @foreach ($accounts as $account)
                                                            <option value="{{ $account->code }}">{{ $account->code }} -
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
                                                <button class="btn btn-label-danger" data-repeater-delete type="button">
                                                    <i class="icon-base ti tabler-x me-1"></i>
                                                    <span class="align-middle">Hapus</span>
                                                </button>
                                            </td>
                                        </tr>
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
                            <select class="global-select2 form-select" id="product-tags" multiple
                                data-allow-clear="false">
                                @foreach ($tags as $tag)
                                    <option value="{{ $tag->id }}" data-color="{{ $tag->tag_color_code }}">
                                        {{ $tag->code }} - {{ $tag->name }}</option>
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
                    <textarea class="form-control" id="product-memo" rows="2" placeholder="Catatan internal untuk produk ini"></textarea>
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
            initCleaveCurrency(document);
            let currentProductType = null;
            let bundleComponentCounter = 0;
            let bundleAccountCounter = 0;

            // Initialize form
            generateProductCode();


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



            $('.form-repeater-component').repeater({
                show: function() {
                    $(this).slideDown(() => {
                        $(this).find('.select2-container').remove();
                        $(this).find('select.select2Product').removeClass(
                            'select2-hidden-accessible').removeAttr(
                            'data-select2-id tabindex aria-hidden');
                        $(this).find('select.select2Product').each(function() {
                            if ($.fn.select2 && $(this).data('select2')) {
                                alert('Destroying existing Select2 instance');
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
                                alert('Destroying existing Select2 instance');
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

            function switchProductType(type) {
                currentProductType = type;

                // Hide all content first
                $('#single-content, #bundle-content').hide();

                // Reset form fields
                resetConditionalFields();

                // Show appropriate content
                if (type === 'single') {
                    $('#single-content').show();
                } else if (type === 'bundle') {
                    $('#bundle-content').show();
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
                $('#productForm select').not('#unit-select,#category-select').val('').trigger('change');
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

            // Bundle component events
            $(document).on('change', '.component-product', function() {
                const selectedOption = $(this).find(':selected');
                const unit = selectedOption.data('unit') || 'pcs';
                const price = selectedOption.data('price') || 0;

                // Find the closest row and update fields in that row
                const $row = $(this).closest('tr');
                $row.find('.component-unit').val(unit);
                updateComponentPriceRow($row);
            });

            function updateComponentPriceRow($row) {
                const qty = parseInt($row.find('.component-qty').val() || 1);
                const selectedOption = $row.find('.component-product option:selected');
                const unitPrice = parseFloat(selectedOption.data('price') || 0);
                const totalPrice = unitPrice * qty;
            }
            $(document).on('input', '.component-qty', function() {
                const $row = $(this).closest('tr');
                updateComponentPriceRow($row);
            });

            $(document).on('click', '.remove-account', function() {
                const id = $(this).data('id');
                $(`.bundle-account-row[data-id="${id}"]`).remove();
            });

            // Generate product code
            function generateProductCode() {
                const year = new Date().getFullYear();
                const randomNum = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
                $('#product-code').val(`PROD-${year}-${randomNum}`);
            }

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

                if (!isValid) {
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

                function getProductFormJson() {
                    const data = {};

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
                            data.purchase_price = $('#purchase-price').val().replace(/[^0-9]/g, '');
                            data.purchase_account = $('#purchase-account').val();
                            data.purchase_tax = $('#purchase-tax').val();
                        }
                        if (data.sales_enabled) {
                            data.sales_price = $('#sales-price').val().replace(/[^0-9]/g, '');
                            data.sales_account = $('#sales-account').val();
                            data.sales_tax = $('#sales-tax').val();
                        }
                        if (data.inventory_enabled) {
                            data.minimum_stock = $('#minimum-stock').val().replace(/[^0-9]/g, '');;
                            data.inventory_account = $('#inventory-account').val();
                            data.track_quantity = $('#track-quantity').is(':checked');
                            data.track_batch = $('#track-batch').is(':checked');
                        }
                    }

                    // Bundle product fields
                    if (data.type === 'bundle') {
                        data.bundle_purchase_enabled = $('#bundle-enable-purchase').is(':checked');
                        data.bundle_sales_enabled = $('#bundle-enable-sales').is(':checked');
                        data.bundle_inventory_enabled = $('#bundle-enable-inventory').is(':checked');

                        if (data.bundle_purchase_enabled) {
                            data.bundle_purchase_price = $('#bundle-purchase-price').val().replace(
                                /[^0-9]/g, '');
                            data.bundle_purchase_account = $('#bundle-purchase-account').val();
                            data.bundle_purchase_tax = $('#bundle-purchase-tax').val();
                        }
                        if (data.bundle_sales_enabled) {
                            data.bundle_sales_price = $('#bundle-sales-price').val().replace(/[^0-9]/g, '');
                            data.bundle_sales_account = $('#bundle-sales-account').val();
                            data.bundle_sales_tax = $('#bundle-sales-tax').val();
                        }
                        if (data.bundle_inventory_enabled) {
                            data.bundle_minimum_stock = $('#bundle-minimum-stock').val().replace(/[^0-9]/g,
                                '');
                            data.bundle_inventory_account = $('#bundle-inventory-account').val();
                            data.bundle_track_quantity = $('#bundle-track-quantity').is(':checked');
                            data.bundle_track_batch = $('#bundle-track-batch').is(':checked');
                        }

                        data.bundle_components = [];
                        // $('#bundle-components-tbody tr.bundle-component-row').each(function() {
                        //     data.bundle_components.push({
                        //         product_id: $(this).find('.component-product').val(),
                        //         qty: $(this).find('.component-qty').val().replace(/[^0-9]/g,
                        //             '')
                        //     });
                        // });

                        $('#bundle-components-tbody tr.bundle-component-row').each(function() {
                            const productId = $(this).find('.component-product').val();
                            const qtyRaw = $(this).find('.component-qty').val();
                            const qty = qtyRaw ? qtyRaw.replace(/[^0-9]/g, '') : null;

                            if (productId && productId !== "null" && qty && qty !== "null") {
                                data.bundle_components.push({
                                    product_id: productId,
                                    qty: qty
                                });
                            }
                        });

                        data.bundle_accounts = [];
                        // $('#bundle-accounts-tbody tr.bundle-account-row').each(function() {
                        //     if ($(this).find('.account-select').val() != null && $(this).find(
                        //             '.account-select').val() != "null" && $(this).find(
                        //             '.account-select').val() != "" && $(this).find('.account-cost')
                        //         .val() != null && $(this).find('.account-cost').val() != "null" &&
                        //         $(this).find('.account-cost').val() != "") {
                        //         data.bundle_accounts.push({
                        //             account: $(this).find('.account-select').val() == "" ||
                        //                 $(this).find('.account-select').val() == "null" ?
                        //                 null : $(this).find('.account-select').val(),
                        //             cost: $(this).find('.account-cost').val().replace(
                        //                     /[^0-9]/g,
                        //                     '') == "" || $(this).find('.account-cost')
                        //                 .val() == "null" ? null : $(this).find(
                        //                     '.account-cost')
                        //                 .val().replace(/[^0-9]/g, ''),
                        //         });
                        //     }

                        // });

                        $('#bundle-accounts-tbody tr.bundle-account-row').each(function() {
                            const account = $(this).find('.account-select').val();
                            const costRaw = $(this).find('.account-cost').val();
                            const cost = costRaw ? costRaw.replace(/[^0-9]/g, '') : null;

                            if (account && account !== "null" && cost && cost !== "null") {
                                data.bundle_accounts.push({
                                    account: account,
                                    cost: cost
                                });
                            }
                        });
                        console.log(data.bundle_accounts)
                    }

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
                            url: "{{ route('master_data.product.save') }}",
                            method: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Produk berhasil ditambahkan.',
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

            // Initialize currency formatting
            // if (typeof Cleave !== 'undefined') {
            //     $('.currency-input').each(function() {
            //         new Cleave(this, {
            //             numeral: true,
            //             numeralThousandsGroupStyle: 'thousand',
            //             prefix: 'Rp ',
            //             noImmediatePrefix: false,
            //             rawValueTrimPrefix: true
            //         });
            //     });
            // }
        });
    </script>
@endsection
