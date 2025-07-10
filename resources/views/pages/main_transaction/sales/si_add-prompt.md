# Sales Invoice (SI) Creation Form - Technical Specification

## Overview
This document provides comprehensive technical specifications for creating the Sales Invoice (SI) form in the sales module. The SI processes customer invoices and manages revenue recognition with complete financial calculations and discount handling.

## Form Purpose
The Sales Invoice form is specifically designed for:
- Recording and processing customer invoices with accurate financial calculations including multi-level discounts
- Managing payment terms and due dates for accounts receivable and cash flow planning
- Tracking tax obligations and compliance requirements for sales transactions
- Linking invoices to sales orders and deliveries for comprehensive sales workflow management
- Supporting accounts receivable processes and revenue recognition systems
- Handling complex discount structures for customer relationship management

## UI Structure Requirements

### 1. Form Header
- **Title**: "Buat Faktur Penjualan"
- **Card Layout**: Standard card with header and body sections
- **Form ID**: `salesInvoiceForm`

### 2. Section 1: Transaction Information
Contains essential invoice details in a 4-row layout:

#### Row 1: Basic Information
- **Pelanggan** (Required, Select2 dropdown)
  - Options: John, PT. Maju Jaya, CV. Sukses Mandiri, PT. Berkah Sejahtera
  - Auto-populates email and address when selected
- **Email** (Required, Email input)
  - Auto-populated from customer selection
  - Editable for corrections

#### Row 2: Address and Invoice Details
- **Alamat** (Required, Textarea, 3 rows)
  - Auto-populated from customer selection
  - Placeholder: "autofill but editable"
- **Tanggal Transaksi** (Required, Date input)
  - Default: Today's date
  - Cannot be future date
- **Nomor Transaksi** (Required, Text input)
  - Auto-generated format: "SI-YYYY-XXX"
  - Placeholder: "autofill but editable"

#### Row 3: Sales Reference and Payment Terms
- **Nomor Pemesanan Penjualan** (Optional, Text input with integration button)
  - Links to existing Sales Orders
  - Sample: "Sales Order 110001"
  - Integration button for SO lookup and auto-fill
- **Syarat Pembayaran** (Required, Select dropdown)
  - Options: Net 15, Net 30, Net 45, Cash on Delivery, Immediate
  - Default: Net 30
- **Nomor Referensi Pelanggan** (Optional, Text input)
  - Customer's invoice reference number
- **Tag** (Optional, Text input)
  - For categorizing invoices

#### Row 4: Due Date and Warehouse
- **Tanggal Jatuh Tempo** (Required, Date input)
  - Auto-calculated based on payment terms
  - Placeholder: "autofill"
  - Must be after transaction date
- **Gudang** (Optional, Select dropdown)
  - Options: Gudang Utama, Gudang Cabang A, Gudang Cabang B
  - Placeholder: "Pilih Gudang"

### 3. Section 2: Product Information
- **Section Title**: "Informasi Penjualan"
- **Tax Checkbox**: "Harga Termasuk Pajak" (top-right)

#### Product Table Structure:
- **Produk** (Required, Select dropdown, 15% width)
  - Sample options: ALBUM SVT, Buku Tulis, Pensil, Penghapus
  - Auto-populates description, unit, price when selected
- **Deskripsi** (Auto-filled, Text input, 20% width)
  - Populated from product selection
  - Sample: "ALBUM SVT VOL 1"
  - Editable for custom descriptions
- **Kuantitas** (Required, Number input, 10% width)
  - Quantity being invoiced
  - Sample: 100
  - Minimum value: 1
- **Unit** (Auto-filled, Text input, 8% width)
  - Unit of measurement (pcs, box, kg, etc.)
  - Sample: "pcs"
  - Read-only field
- **Harga Satuan** (Required, Currency input, 12% width)
  - Auto-filled from product data
  - Sample: Rp 250,000.00
  - Editable for pricing adjustments
- **Diskon** (Optional, Percentage input, 10% width)
  - Discount percentage per line item
  - Sample: 5%
  - Range: 0-100%
- **Pajak** (Select dropdown, 10% width)
  - Options: PPN, Tidak Ada Pajak
  - Sample: "PPN"
  - Affects tax calculations
- **Jumlah** (Calculated, Currency input, 15% width)
  - Auto-calculated: (Kuantitas × Harga Satuan) - Diskon (± Tax)
  - Sample: Rp 25,000,000.00
  - Read-only field

#### Product Management:
- **Initial Rows**: 4 rows with sample data in first row
- **Add Product Button**: "Tambah Produk" with plus icon
- **Remove Functionality**: Can remove rows (minimum 1 row)
- **Auto-calculation**: Real-time calculation of totals with discount handling

### 4. Section 3: Additional Information & Invoice Summary
Split into two columns:

#### Left Column: Additional Information
- **Pesan** (Textarea, 4 rows, 500 chars max)
  - Message for customer regarding invoice
  - Placeholder: "Catatan atau pesan untuk pelanggan..."
- **Memo** (Textarea, 4 rows, 500 chars max)
  - Internal notes for sales and accounting team
  - Placeholder: "Memo internal..."
- **Lampiran** (File input)
  - Accepted formats: .xlsx, .xls, .docx, .doc, .pdf, .png, .zip
  - Maximum size: 10MB

#### Right Column: Invoice Summary
**Enhanced Calculation Structure with Multi-Level Discounts:**

**A. Jika Harga Tidak Termasuk Pajak (Default):**
- **Subtotal**: Rp 12,000,000.00 (Sum of all line totals before discount and tax)
- **Diskon Perbaris**: Rp 500,000.00 (Line-level discounts applied)
- **Diskon**: 5% = Rp 600,000.00 (Additional order-level discount)
- **Pajak**: Rp 1,221,000.00 (Tax calculated on discounted amount)
- **Total**: Rp 12,321,000.00 (Final invoice total)
- **Sisa Tagihan**: Rp 12,321,000.00 (Outstanding balance)

**B. Jika Harga Sudah Termasuk Pajak:**
- **Subtotal**: Calculated excluding tax from gross amounts
- **Diskon Perbaris**: Line-level discounts
- **Diskon**: Order-level discount percentage
- **Pajak**: Tax amount extracted from inclusive prices
- **Total**: Final amount after all calculations
- **Sisa Tagihan**: Outstanding balance

### 5. Action Buttons
- **Batalkan** (Danger button)
  - Cancels invoice creation
  - Confirmation dialog before exit
- **Buat** (Success button)
  - Creates the invoice
  - Comprehensive validation before submission

## JavaScript Functionality Requirements

### 1. Auto-fill Features
- **Customer Selection**: Populates email, address automatically
- **Product Selection**: Fills description, unit, and price
- **SO Integration**: Pre-fill entire form from selected Sales Order
- **Payment Terms**: Auto-calculates due date based on terms

### 2. Advanced Calculation Engine
- **Multi-level Discounts**: Both line-level and order-level discount handling
- **Tax Calculations**: Support for tax-inclusive and tax-exclusive modes
- **Real-time Updates**: Instant updates when tax mode or discounts change
- **Discount Validation**: Ensure discount percentages are within valid ranges
- **Currency Formatting**: Indonesian Rupiah with proper formatting

### 3. Complex Discount Management
- **Line Discounts**: Individual product line discount percentages (5% in sample)
- **Order Discount**: Overall invoice discount percentage (5% order-level)
- **Discount Sequence**: Apply line discounts first, then order discount
- **Tax on Discount**: Calculate tax on final discounted amounts
- **Discount Limits**: Configurable maximum discount percentages per customer

### 4. Form Validation
- **Required Fields**: Customer, Email, Address, Transaction Date, Invoice Number, Payment Terms, Due Date, Products
- **Email Validation**: Proper email format
- **Date Validation**: Transaction date not in future, due date after transaction date
- **Product Validation**: At least one product must be selected
- **Discount Validation**: Percentage values between 0-100%
- **Financial Validation**: Positive amounts and proper calculations
- **File Validation**: Type and size restrictions

### 5. Customer Management
- **Customer Data**: Auto-population of customer information
- **Credit Limits**: Integration with customer credit management
- **Pricing Rules**: Customer-specific pricing and discount rules
- **Payment History**: Access to customer payment behavior

## Data Context
- **Business Flow**: SO → Delivery → **Invoice**
- **Purpose**: Revenue recognition and accounts receivable management
- **Integration**: Links to Sales Orders, Deliveries, and Payment processing
- **Financial**: Foundation for revenue reporting and customer billing

## Development Notes
- **Complex Calculations**: Multi-level discount and tax calculations required
- **Revenue Focus**: Accurate revenue recognition and financial reporting
- **Customer Relationship**: Enhanced discount management for customer satisfaction
- **Payment Integration**: Links to payment recording and accounts receivable
- **Audit Trail**: Complete financial transaction logging for compliance

## Future Considerations
This invoice form will enable:
- **Payment Processing**: Link invoices to payment records and bank reconciliation
- **Revenue Analytics**: Sales performance and customer profitability analysis
- **Customer Portal**: Self-service invoice access and payment processing
- **Discount Analysis**: Customer discount patterns and profitability tracking
- **Collection Management**: Automated dunning and collection processes
- **Financial Reporting**: Integration with accounting systems and tax reporting

## Technical Implementation
- **Framework**: Laravel Blade templates
- **JavaScript**: jQuery with complex financial calculations
- **Styling**: Bootstrap + TailwindCSS
- **Validation**: Multi-layer validation including business rules and discount limits
- **File Upload**: Secure file handling for invoice supporting documents
- **Database**: Complex relationships with customers, sales orders, deliveries, payments, and accounting entries
- **PDF Generation**: Professional invoice document generation with company branding
- **Email Integration**: Automated invoice distribution to customers
- **Payment Gateway**: Integration with online payment processing systems

## Special Features & Business Logic

### Integration Notes (Based on Right Panel Information)
**Button Functions at Detail Pengiriman Penjualan**:

**1. Fitur: Tombol Aksi & Cetak di Detail Pengiriman Penjualan**
**Deskripsi**: Di halaman Detail Pengiriman Penjualan (faktur), pengguna akan melihat dua jenis tombol utama:

**1. Tombol Tindakan**
- Berisikan aksi yang bisa dilakukan terhadap faktur penjualan:
  - Kirim Pembayaran: Untuk mencatat pembayaran atas faktur
  - Buat Pembelian: Untuk memulai proses retur jika ada pengiriman barang

**2. Tombol Cetak & Bagikan**
- Digunakan untuk mengakses dokumen faktur:
  - Download Faktur: Mengunduh dokumen faktur dalam format PDF
  - Bagikan Faktur: Mengirim atau menyebarkan link email untuk dibagikan ke pihak terkait (misal: pelanggan)

**Tujuan**: 
- Memberikan akses cepat ke tindakan penting terkait pengiriman penjualan dan distribusi dokumen

**Clickable Integration**:
- Field Name: Sales Order reference
- Aksi: Klik akan membuka Detail Sales Order (SO)
- Purpose: Quick navigation to source sales order details

### Enhanced Customer Experience
- **Professional Invoicing**: PDF generation with company branding
- **Payment Options**: Multiple payment method integration
- **Customer Communication**: Automated invoice delivery and reminders
- **Self-Service**: Customer portal access for invoice viewing and payment

### Revenue Management
- **Discount Control**: Sophisticated discount management and approval workflows
- **Revenue Recognition**: Accurate financial reporting and compliance
- **Customer Profitability**: Analysis of customer discount patterns and margins
- **Collection Optimization**: Automated payment reminders and collection processes