# Sales Order (SO) Creation Form - Technical Specification

## Overview
This document provides comprehensive technical specifications for creating the Sales Order (SO) form in the sales management module. The form manages customer orders with inventory validation, customer pricing integration, and comprehensive financial calculations for the sales process where items go out and money comes in.

## Form Purpose
The Sales Order form is specifically designed for:
- Creating customer sales orders with comprehensive product selection and inventory availability validation
- Managing customer information with automatic data population from customer master records
- Integrating warehouse inventory checking to ensure product availability before order confirmation
- Supporting flexible pricing with customer-specific pricing from pricelist integration and tax calculations
- Providing foundation for sales delivery and sales invoice creation through complete order documentation
- Enabling inventory reservation and stock allocation for confirmed sales orders
- Supporting various payment terms and customer credit management for sales transactions
- Maintaining complete sales audit trail for customer relationship management and sales analytics

## UI Structure Requirements

### 1. Form Header
- **Title**: "Tambah Sales Order"
- **Card Layout**: Standard card with header and body sections
- **Form ID**: `salesOrderForm`

### 2. Section 1: Basic Transaction Information (Informasi Dasar Transaksi)
Essential transaction details:

#### Row 1: Transaction Basics
- **Tanggal Transaksi** (Transaction Date) (Required, Date input)
  - Sales order creation date
  - Default: Today's date
  - Sample: "2025-08-27"
  - Cannot be future date
- **Nomor Transaksi** (Transaction Number) (Required, Text input)
  - Auto-generated sales order number
  - Sample: "SO-20250B-789"
  - Format: SO-YYYYMM-XXX
  - Refresh button (🔄) for regeneration if needed

### 3. Section 2: Customer Information (Informasi Supplier)
Customer details and contact information:

#### Row 1: Customer Selection
- **Supplier** (Customer) (Required, Select2 dropdown)
  - Customer selection from customer master
  - Sample: "John Doe"
  - Triggers auto-fill of customer information
  - Customer must be active and approved for sales
- **Email** (Auto-filled, Text display)
  - Customer email address
  - Sample: "john.doe@customer.com"
  - Auto-populated from customer master
  - Read-only after customer selection

#### Row 2: Address Information
- **Alamat** (Address) (Auto-filled, Textarea display)
  - Customer shipping/billing address
  - Sample: "Alamat akan otomatis terisi"
  - Auto-populated from customer master
  - 3 rows, read-only after customer selection

#### Row 3: Additional Customer Details
- **Nomor Referensi Supplier** (Customer Reference Number) (Optional, Text input)
  - Customer's internal reference/PO number
  - Placeholder: "Masukkan nomor referensi"
  - For customer's purchase order tracking
- **Keterangan** (Notes) (Optional, Textarea)
  - Order notes and comments
  - Placeholder: "Nomor order transaksi..."
  - 3 rows, 500 characters max

### 4. Section 3: Sales Details (Detail Pembelian)
Sales-specific configuration:

#### Row 1: Terms and Warehouse
- **Syarat Pembayaran** (Payment Terms) (Required, Select dropdown)
  - Payment terms selection
  - Sample: "Net 30"
  - Options: COD, Net 15, Net 30, Net 45, Net 60
  - Affects invoice due date calculation
- **Gudang** (Warehouse) (Required, Select dropdown)
  - Warehouse selection for inventory checking
  - Sample: "Pusat Gresik"
  - Determines stock availability for products
  - Required for inventory validation

#### Row 2: Additional Notes
- **Keterangan** (Additional Notes) (Optional, Textarea)
  - Sales order specific notes
  - Placeholder: "Nomor order transaksi..."
  - 4 rows, 1000 characters max

### 5. Section 4: Product Information (Informasi Pembayaran)
Product selection and pricing:

#### Information Box:
- **Stock Checking Notice**: "☐ Sebelum pilih Supplier, cek Gudang terlebih dahulu untuk dapat memastikan produk tersedia"
- **Process Guidance**: Instruction to check warehouse before customer selection

#### Product Table:
- **Dynamic Table** with add/remove functionality
- **Columns**:
  - **PRODUK** (Product) (Required, Select2 dropdown)
    - Product selection from available inventory
    - Sample: "Pilih Produk"
    - Filtered by warehouse stock availability
    - Auto-fills unit, price, and tax information
  - **KUANTITAS** (Quantity) (Required, Number input)
    - Sales quantity
    - Sample: "Qty"
    - Validation: Cannot exceed available stock
    - Positive numbers only
  - **SATUAN** (Unit) (Auto-filled, Text display)
    - Product unit of measure
    - Sample: "Unit"
    - Auto-populated from product master
  - **HARGA SATUAN** (Unit Price) (Auto-filled/Editable, Currency input)
    - Product selling price
    - Sample: "Rp 0"
    - Auto-filled from customer pricelist
    - Editable with proper authorization
  - **PAJAK** (Tax) (Auto-filled/Selectable, Dropdown)
    - Tax configuration per product
    - Sample: "Tanpa Pajak"
    - Options: Tanpa Pajak, PPN (11%)
    - Default from product configuration
  - **JUMLAH** (Total) (Auto-calculated, Currency display)
    - Line total calculation
    - Sample: "Rp 0"
    - Calculation: Quantity × Unit Price
  - **AKSI** (Actions) (Button)
    - Remove product line button
    - Delete/trash icon for row removal

#### Add Product Line:
- **+ Tambah Baris** button for adding new product lines
- **Dynamic Row Management**: Add/remove functionality
- **Validation**: Ensure at least one product line for order creation

### 6. Section 5: Additional Information & Summary (Informasi Tambahan & Ringkasan)
Final order details and financial summary:

#### Left Column - Additional Information:
- **Tag** (Optional, Textarea)
  - Order classification tags
  - Sample: "PPN sipil data tambah apa yg diinginkan"
  - 3 rows, for order categorization
- **Memo Internal** (Optional, Textarea)
  - Internal processing notes
  - Placeholder: "Catatan internal..."
  - 4 rows, 500 characters max
  - Not visible to customer
- **Lampiran** (Optional, File input)
  - Order documentation attachment
  - Accepted formats: Excel, Word, PDF, PNG, Zip
  - Maximum size: 10MB
  - Help text: "File dapat berupa Excel, Word, PDF, PNG, atau Zip (Max: 10MB)"

#### Right Column - Financial Summary:
- **Subtotal**: Sum of all product line totals before tax
- **Total Pajak**: Total tax amount from all taxable items
- **Total Sebelum Diskon**: Subtotal + Total Pajak
- **Diskon**: Order-level discount amount (optional)
- **Total Akhir**: Final order total after all calculations
- **Currency Format**: Indonesian Rupiah (Rp X,XXX,XXX.XX)
- **Real-time Updates**: Automatic recalculation as items change

### 7. Action Buttons
- **Batalkan** (Cancel) (Danger button)
  - Color: Red (#dc3545)
  - Function: Cancel order creation
  - Confirmation dialog before exit
- **Simpan** (Save) (Success button)
  - Color: Green (#28a745)
  - Function: Create sales order
  - Validation: Customer, warehouse, at least one product line

## JavaScript Functionality Requirements

### 1. Customer Integration
- **Customer Selection**: Auto-populate email, address, and payment terms
- **Customer Validation**: Verify customer is active and approved for sales
- **Credit Checking**: Optional customer credit limit validation
- **Contact Integration**: Link to customer master data and history

### 2. Inventory Management
- **Warehouse Integration**: Real-time stock checking at selected warehouse
- **Product Filtering**: Show only available products at selected warehouse
- **Stock Validation**: Prevent quantities exceeding available stock
- **Inventory Reservation**: Optional stock reservation for confirmed orders

### 3. Pricing and Tax Management
- **Customer Pricing**: Auto-apply pricing from customer pricelist
- **Price Override**: Allow authorized price modifications
- **Tax Calculation**: Automatic tax calculation based on product settings
- **Discount Management**: Support order-level and line-level discounts

### 4. Financial Calculations
- **Real-time Totals**: Update financial summary as items change
- **Tax Integration**: Calculate PPN (11%) where applicable
- **Discount Application**: Apply discounts and recalculate totals
- **Currency Formatting**: Proper Indonesian Rupiah formatting

## Data Context
- **Business Flow**: Customer Inquiry → **Sales Order** → Sales Delivery → Sales Invoice
- **Purpose**: Customer order management with inventory and pricing integration
- **Integration**: Links to Customer, Inventory, and Pricing modules
- **Sales Process**: Foundation for complete sales transaction workflow

## Development Notes
- **Customer Dependency**: Valid customer selection required for order creation
- **Inventory Integration**: Real-time stock checking and validation
- **Pricing Flexibility**: Support for customer-specific pricing and discounts
- **Financial Accuracy**: Precise calculations for sales and tax amounts
- **Workflow Foundation**: Prepare data for delivery and invoice creation

## Future Considerations
This sales order form will enable:
- **Advanced Pricing**: Complex pricing rules and promotional discounts
- **Inventory Allocation**: Advanced inventory allocation and reservation
- **Order Approval**: Multi-level approval workflows for large orders
- **Customer Portal**: Customer self-service order entry capabilities
- **Mobile Sales**: Mobile-optimized sales order entry for field sales
- **Integration Enhancement**: Advanced CRM and ERP system integration

## Technical Implementation
- **Framework**: Laravel Blade templates with customer and inventory integration
- **JavaScript**: jQuery with real-time calculations and stock validation
- **Styling**: Bootstrap + TailwindCSS with responsive design
- **Database**: Integration with customers, inventory, pricing, and order tables
- **Validation**: Multi-layer validation including stock and credit limits
- **Real-time Data**: Live stock levels and pricing from inventory system
- **Transaction Management**: Proper database transactions for order creation

## Special Features & Business Logic

### Integration Notes (Based on Requirements and Right Panel Information)
**Sales Order Context**:

**Business Requirements from Right Panel**:

#### **Detail**:
- **Mencatat penjualan ke customer**: "sebagai dasar proses pengiriman dan penagihan"
- **Mengontrol validasi transaksi**: "agar data sales order sesuai aturan bisnis"
- **Menghasilkan dokumen Sales Order**: "yang menjadi referensi untuk Delivery Order (DO) dan Invoice Penjualan"

#### **Business Rules - Sales Order**:

**Customer**:
- **Customer wajib diSilih sebelum menambahkan produk**: Customer selection required before product addition
- **Data email & alamat auto-terisi dari master customer**: Email and address auto-filled from customer master
- **Nomor referensi customer opsional**: Customer reference number is optional

**Produk**:
- **Produk hanya bisa ditambahkan jika customer & gudang sudah diSilih**: Products can only be added after customer and warehouse selection
- **Produk yang muncul di droSDown = hanya produk yang sudah di-mapSing ke customer tersebut**: Dropdown shows only products mapped to selected customer
- **"-> Jika customer A hanya boleh beli Produk X & Y -> droSDown Produk hanya menampilkan X & Y"**: Customer-specific product filtering
- **"- Jika tidak ada produk yang di-mapSing -> user tidak bisa menambahkan produk (sistem tampilkan notifikasi)"**: Show notification if no products mapped to customer
- **Setiap baris produk wajib memiliki**: Each product line must have:
  - **Produk (diSilih dari mapSing)**: Product selected from customer mapping
  - **Kuantitas > 0**: Quantity greater than 0
  - **Harga satuan (auto dari pricelist customer, editable sesuai akses)**: Unit price from customer pricelist, editable with access
  - **Pajak (default dari produk, bisa diubah jika perlu)**: Tax default from product, changeable if needed
- **Total baris dihitung otomatis: Qty × Harga ± Pajak**: Line total calculated automatically
- **Jika toggle Harga Termasuk Pajak aktif -> sistem hitung ulang otomatis**: If tax-inclusive pricing active, system recalculates automatically

#### **Requirement Sales Order, Delivery, & Invoice**:
1. **Pembuatan Sales Order (SO)**: "SO adalah dokumen utama"
2. **Semua Sales Delivery (SD) dan Sales Invoice (SI) hanya dapat dibuat berdasarkan SO**: All SD and SI created from SO
3. **Tidak ada opsi membuat SD atau SI tanpa melalui SO**: No option to create SD or SI without SO

#### **Flow Dinamis**:
- **Sistem mendukung urutan fleksibel dalam alur dokumen**: System supports flexible document flow
- **SO -> SD -> SI**: Standard flow
- **SO -> SI -> SD**: Alternative flow
- **Keterangan**: 
  - **SD bisa multiple selama masih ada Qty tersisa di SO**: Multiple SD while quantities remain in SO
  - **SI bisa multiple selama masih ada nilai tagihan tersisa di SO**: Multiple SI while billing amounts remain in SO

### Enhanced Sales Order Management Features

#### **Customer-Product Mapping System**:
- **Customer-Specific Products**: Only show products mapped to selected customer
- **Product Availability**: Validate product mapping before allowing selection
- **Mapping Notifications**: Clear notifications when no products available for customer
- **Product Access Control**: Enforce customer-product relationship rules

#### **Advanced Pricing Integration**:
- **Customer Pricelist**: Auto-apply pricing from customer-specific pricelists
- **Price Override Control**: Editable pricing with proper access control
- **Tax-Inclusive Pricing**: Support for tax-inclusive and tax-exclusive pricing modes
- **Dynamic Recalculation**: Automatic recalculation when pricing mode changes

#### **Inventory and Warehouse Integration**:
- **Stock Validation**: Real-time stock checking at selected warehouse
- **Warehouse-Specific Availability**: Product availability per warehouse location
- **Stock Reservation**: Optional inventory reservation for confirmed orders
- **Multi-location Support**: Handle sales across different warehouse locations

### Business Rules and Validation

#### **Customer and Product Selection Rules**:
- **Sequential Selection**: Customer and warehouse must be selected before product addition
- **Product Mapping Enforcement**: Strict enforcement of customer-product mapping rules
- **Stock Availability**: Validate product availability at selected warehouse
- **Customer Status**: Ensure customer is active and approved for sales

#### **Financial Validation Controls**:
- **Positive Quantities**: All product quantities must be greater than zero
- **Price Validation**: Ensure positive unit prices with proper authorization for changes
- **Tax Calculation Accuracy**: Proper PPN (11%) calculation where applicable
- **Total Validation**: Ensure accurate financial calculations throughout

#### **Order Creation Requirements**:
- **Minimum Order**: At least one product line required for order creation
- **Complete Information**: Customer, warehouse, payment terms, and products required
- **Financial Accuracy**: All calculations must be accurate and validated
- **Business Rule Compliance**: All customer-product mapping rules enforced

### Workflow Integration

#### **Sales Document Flow**:
- **Foundation Document**: Sales Order is the foundation for all subsequent sales documents
- **Delivery Integration**: Prepare data structure for Sales Delivery creation
- **Invoice Integration**: Prepare financial data for Sales Invoice processing
- **Multiple Deliveries**: Support multiple partial deliveries against single Sales Order

#### **Customer Relationship Management**:
- **Customer History**: Integration with customer transaction history
- **Credit Management**: Optional customer credit limit checking and enforcement
- **Communication**: Foundation for customer communication and order updates
- **Relationship Tracking**: Maintain complete customer interaction history

This comprehensive sales order system provides complete customer order management with strict business rule enforcement, customer-product mapping, and proper financial controls while serving as the foundation for the complete sales transaction workflow.