# Product (Produk) Creation Form - Technical Specification

## Overview
This document provides comprehensive technical specifications for creating the Product (Produk) form in the inventory management module. The form manages both Single and Bundle product types with dynamic conditional content and comprehensive pricing, inventory, and accounting integration.

## Form Purpose
The Product form is specifically designed for:
- Creating and managing product catalog entries with comprehensive product information
- Supporting both Single (individual) and Bundle (composite) product types with dynamic form behavior
- Configuring pricing structures for purchase and sales operations with tax integration
- Managing inventory tracking and stock monitoring with minimum stock alerts
- Integrating with chart of accounts for proper financial categorization and reporting
- Enabling bundle product composition with component product selection and pricing
- Supporting product image upload and visual product identification
- Providing flexible inventory tracking options including quantity-only and batch tracking

## UI Structure Requirements

### 1. Form Header
- **Title**: "Tambah Produk Baru"
- **Card Layout**: Standard card with header and body sections
- **Form ID**: `productForm`

### 2. Section 1: Product Information (Info Produk)
Contains essential product details (always visible):

#### Row 1: Basic Product Information
- **Nama Produk** (Product Name) (Required, Text input)
  - Unique product identifier name
  - Max 255 characters
- **Unit** (Unit of Measure) (Required, Select dropdown)
  - Options: pcs, kg, meter, liter, rim, box, set, etc.
  - Standard measurement units for Indonesian business
- **Image Upload Area** (Optional, File input)
  - Upload area with placeholder "Pilih gambar produk"
  - Accepted formats: JPG, PNG
  - Maximum size: 5MB
  - Preview area 200x200px

#### Row 1b: Additional Product Details
- **Kode Produk** (Product Code) (Required, Text input)
  - Unique SKU/product code
  - Auto-generated with edit capability
  - Format: PROD-YYYY-XXX
- **Kategori Produk** (Product Category) (Optional, Select dropdown)
  - Product categorization for organization
  - Options: Electronics, Office Supplies, Raw Materials, etc.

#### Row 2: Product Description
- **Deskripsi** (Description) (Optional, Textarea)
  - Detailed product description
  - 3 rows, 1000 characters max
  - Placeholder: "Deskripsi detail produk..."

### 3. Section 2: Product Type Selection (Tipe Produk)
Product type selection with conditional content:

#### Product Type Radio Buttons:
- **Single** (Individual Product)
  - Standard individual product entry
  - Direct pricing and inventory management
- **Bundle** (Composite Product)
  - Multi-component product package
  - Component-based pricing and inventory

#### **Confirmation Dialog**: 
- Warning before changing product type
- Message: "Mengubah tipe produk akan mengatur ulang data di bawah. Lanjutkan?"
- Prevents accidental data loss

### 4. Section 3: Conditional Content (Single Product)
Visible only when "Single" product type selected:

#### Tab: Harga & Persediaan (Single Tab)

##### Purchase Configuration:
- **Saya beli produk ini** (I buy this product) (Checkbox)
  - Enables purchase-related fields
  - **Harga Satuan** (Unit Price) (Required when checked, Currency input)
    - Purchase price per unit
    - Format: Indonesian Rupiah (Rp X,XXX,XXX.XX)
  - **Akun Pembelian** (Purchase Account) (Required when checked, Select dropdown)
    - Chart of accounts integration
    - Sample: "1 - Aset"
  - **Pajak Pembelian** (Purchase Tax) (Required when checked, Select dropdown)
    - Tax options: PPN (11%), None
    - Sample: "PPN"

##### Sales Configuration:
- **Saya jual produk ini** (I sell this product) (Checkbox)
  - Enables sales-related fields
  - **Harga Satuan** (Unit Price) (Required when checked, Currency input)
    - Sales price per unit
    - Format: Indonesian Rupiah (Rp X,XXX,XXX.XX)
  - **Akun Penjualan** (Sales Account) (Required when checked, Select dropdown)
    - Chart of accounts integration
    - Sample: "1 - Aset"
  - **Pajak Penjualan** (Sales Tax) (Required when checked, Select dropdown)
    - Tax options: PPN (11%), None
    - Sample: "PPN"

##### Inventory Management:
- **Monitoring Persediaan Barang** (Inventory Monitoring) (Checkbox)
  - Enables inventory tracking
  - **Batas stok minimum** (Minimum Stock Level) (Required when checked, Number input)
    - Minimum stock alert threshold
    - Help text: "*Ketentuan stok awal dapat menggunakan penyesuaian stok"
  - **Akun Persedian Default** (Default Inventory Account) (Required when checked, Select dropdown)
    - Inventory account from chart of accounts
    - Sample: "1 - Aset"

##### Inventory Tracking Options:
- **Pelacakan Persediaan** (Inventory Tracking)
  - **Hanya lacak kuantitas** (Track quantity only) (Checkbox, default checked)
    - Basic quantity tracking
  - **Batch** (Batch tracking) (Checkbox)
    - Advanced batch/lot tracking

### 5. Section 4: Conditional Content (Bundle Product)
Visible only when "Bundle" product type selected:

#### Tab Navigation:
- **Harga & Persediaan** (Pricing & Inventory)
  - Same content as Single product section
- **Pengaturan Bundle** (Bundle Configuration)
  - Bundle-specific component management

#### Tab Content: Harga & Persediaan
- **Same sections as Single product** (Purchase, Sales, Inventory)

#### Tab Content: Pengaturan Bundle

##### Bundle Components Table:
- **Dynamic Table** with add/remove functionality
- **Columns**:
  - **Nama Produk** (Product Name) (Required, Select dropdown)
    - Selection from existing Single products
    - Sample: "Pilih Produk"
  - **Qty** (Quantity) (Required, Number input)
    - Component quantity in bundle
    - Sample: 10, 5
  - **Unit** (Unit) (Auto-filled, Text display)
    - Unit from selected product
    - Sample: "pcs", "rim"
  - **Harga** (Price) (Auto-calculated, Currency display)
    - Calculated from product price × quantity
    - Sample: "Rp5,400.00", "Rp32,000.00"
  - **Action** (Remove button)
    - ⊖ Remove component from bundle

##### Bundle Account Mapping (Conditional):
- **Only visible when "Monitoring Persediaan Barang" checked**
- **Dynamic Table** for account assignments
- **Columns**:
  - **Akun** (Account) (Optional, Select dropdown)
    - Account assignment for inventory monitoring
    - Help text: "Anda bisa mengisi ini jika Monitor persediaan barang tercentang"
    - Sample: "Pilih Akun"
  - **Biaya** (Cost) (Optional, Currency input)
    - Additional cost assignment
    - Format: "Rp-"
  - **Action** (Remove button)
    - ⊖ Remove account mapping

##### Bundle Summary:
- **Total Harga** (Total Price)
  - Calculated sum of all component prices
  - Auto-updated when components change

### 6. Action Buttons
- **Batalkan** (Cancel) (Danger button)
  - Cancels product creation
  - Confirmation dialog before exit
- **Simpan** (Save) (Success button)
  - Creates the product
  - Comprehensive validation before submission

## JavaScript Functionality Requirements

### 1. Product Type Management
- **Type Selection**: Radio button handling with confirmation dialog
- **Content Switching**: Show/hide conditional sections based on product type
- **Data Reset**: Clear conditional data when changing product type
- **Confirmation Dialog**: Warn user about data loss before type change

### 2. Conditional Field Management
- **Checkbox Controls**: Enable/disable related fields based on checkbox states
- **Purchase Fields**: Show/hide purchase configuration when "Saya beli produk ini" checked
- **Sales Fields**: Show/hide sales configuration when "Saya jual produk ini" checked
- **Inventory Fields**: Show/hide inventory management when "Monitoring Persediaan Barang" checked

### 3. Dynamic Table Management (Bundle)
- **Component Table**: Add/remove bundle component rows
- **Product Selection**: Populate unit and price when product selected
- **Quantity Calculation**: Calculate line total (price × quantity)
- **Bundle Total**: Calculate total bundle price from all components
- **Account Table**: Conditional display and management of account mappings

### 4. Image Upload Management
- **File Validation**: Check file type (JPG, PNG) and size (max 5MB)
- **Preview Display**: Show uploaded image in preview area
- **Upload Feedback**: Progress indicator and error handling
- **Placeholder Management**: Show/hide placeholder based on upload status

## Data Context
- **Business Flow**: Product Planning → **Product Creation** → Inventory Management → Sales/Purchase Operations
- **Purpose**: Comprehensive product catalog management with inventory and financial integration
- **Integration**: Links to Inventory, Sales, Purchase, and Accounting modules
- **Product Hierarchy**: Support for both individual and composite (bundle) products

## Development Notes
- **Dynamic Form Logic**: Complex conditional rendering based on product type and checkbox states
- **Validation Complexity**: Multi-level validation rules based on form state
- **Bundle Management**: Advanced component selection and calculation logic
- **Account Integration**: Seamless chart of accounts integration throughout
- **Image Handling**: Secure file upload with format and size validation
- **Responsive Design**: Proper mobile handling of dynamic tables and complex forms

## Future Considerations
This product form will enable:
- **Product Variants**: Size, color, and other variant management
- **Barcode Integration**: Barcode generation and scanning capabilities
- **Supplier Management**: Multiple supplier assignment per product
- **Price History**: Track price changes over time
- **Bulk Import**: CSV-based bulk product import functionality
- **Advanced Inventory**: Serial number tracking and advanced inventory methods

## Technical Implementation
- **Framework**: Laravel Blade templates with dynamic sections
- **JavaScript**: jQuery with complex conditional logic and table management
- **Styling**: Bootstrap + TailwindCSS with responsive design
- **Validation**: Multi-layer validation including business rules and file validation
- **File Upload**: Secure image handling with format and size restrictions
- **Database**: Integration with products, inventory, accounts, and bundle composition tables
- **Accounting**: Proper product account mapping for purchase, sales, and inventory
- **Currency**: Indonesian Rupiah formatting and calculation standards

## Special Features & Business Logic

### Integration Notes (Based on Right Panel Information)
**Product Management Context**:
- **Purpose**: "Menu penambahan produk digunakan untuk memasukkan informasi produk baru ke dalam sistem. Produk dapat dikategorikan berdasarkan tipe, yang menentukan cara produk ditolola dan digunakan dalam aktivitas bisnis perusahaan."

**Product Type Definitions**:

#### **1. Tipe Produk**:
- **Single (Satuan)**: "Produk jadi yang tidak memerlukan pengelolaan lebih lanjut. Produk dibeli dari supplier dan dapat langsung ke pelanggan tanpa proses produksi. Contoh: Air mineral, buku tulis, smartphone."
- **Bundle (Komposisi)**: "Produk yang terdiri dari beberapa komponen atau bahan baku yang digabungkan atau dijual menjadi satu produk baru."

#### **2. Form Input Tambah Produk**:

**Field Specifications Table**:
- **Nama Produk**: Text - "Nama yang akan ditampilkan dan dicari oleh pengguna"
- **SKU / Kode Produk**: Text - "Kode unik untuk identifikasi produk"
- **Tipe Produk**: Radio - "Pilih antara Single atau Bundle, dengan konfirmasi, liter"
- **Unit**: Dropdown - "Pilih unit pengukuran (misal: pcs, kg, meter, liter)"
- **Kategori Produk**: Dropdown - "Dapat dipilih untuk pengelompokan produk"
- **Harga Jual**: Currency - "Harga jual produk jika tersedia"
- **Harga Beli**: Currency - "Harga dasar pembelian produk dari supplier"
- **Komposisi (jika Bundle)**: Table - "Memiliki bahan atau produk penyusun dari produk bundle"

#### **3. Khusus Produk Bundle**:
- **User wajib memilih komponen penyusun dari produk bundle**
- **Sistem akan secara otomatis mengurangi stok komponen saat produk bundle dibuat/dijual**
- **Tersedia opsi untuk mengatur rasio komposisi (contoh: 1 Box = 1 unit Oil + 3 unit Oil)**
- **Bundle tidak tersedia jika stok komponen tidak cukup untuk membentuk produk bundle**

#### **4. Validasi dan Logika Tambahan**:
- **Produk Single**: "Tidak dapat memiliki komposisi"
- **Produk Bundle**: "Tidak dapat dijungit stok langsung (stok tergantung bahan)"
- **Harga jual produk bundle bisa dihitung otomatis berdasarkan total harga komponen, atau disesuaikan secara manual**
- **Sistem akan menampilkan peringatan jika stok komponen tidak cukup untuk membentuk produk bundle**

#### **5. Aksi di Halaman Detail Produk**:
- **Tombol Aksi**: "Fungsi"
- **Jumlah Awal**: "Menuju halaman Tombol untuk mengirimkan detail produk, tersedia beberapa tombol aksi: penting"
- **Aksi Penambahan Stok**: "Mengarahkan produk agar tidak muncul di daftar produk kecuali diaktifkan kembali"
- **Produk yang diarsip tidak dapat dipakai di transaksi baru"

### Enhanced Product Management
- **Product Type Validation**: Ensure proper product type selection with confirmation
- **Bundle Composition**: Automatic component stock deduction when bundle sold
- **Stock Dependency**: Bundle availability based on component stock levels
- **Pricing Flexibility**: Manual or automatic bundle pricing from component costs

### Financial Integration
- **Chart of Accounts**: Integration with purchase, sales, and inventory accounts
- **Tax Configuration**: Proper PPN (11%) tax setup for purchase and sales
- **Account Mapping**: Flexible account assignment for different product operations
- **Currency Handling**: Indonesian Rupiah formatting throughout

### Inventory Management
- **Stock Monitoring**: Optional inventory tracking with minimum stock alerts
- **Bundle Stock Logic**: Component-based stock calculation for bundle products
- **Tracking Options**: Support for quantity-only and batch tracking methods
- **Stock Adjustments**: Foundation for inventory adjustments and stock corrections

### Business Rules
- **Single Products**: Direct stock management and pricing
- **Bundle Products**: Component-based stock and automatic composition management
- **Account Requirements**: Proper account selection for purchase/sales operations
- **Image Management**: Product visualization with file format and size validation

### Workflow Integration
- **Product Catalog**: Integration with main product inventory system
- **Sales Integration**: Product availability for sales order creation
- **Purchase Integration**: Product setup for purchase order processing
- **Inventory Control**: Real-time stock monitoring and management

This comprehensive product form supports both simple and complex product types while maintaining proper financial controls and inventory management integration. The dynamic interface adapts to product type selection, providing relevant configuration options for each scenario.