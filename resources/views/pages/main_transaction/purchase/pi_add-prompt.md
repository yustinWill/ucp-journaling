# Purchase Invoice (PI) Creation Form - Technical Specification

## Overview
This document provides comprehensive technical specifications for creating the Purchase Invoice (PI) form in the purchase module. The PI processes supplier invoices and manages payment obligations with complete financial calculations.

## Form Purpose
The Purchase Invoice form is specifically designed for:
- Recording and processing supplier invoices with accurate financial calculations
- Managing payment terms and due dates for cash flow planning
- Tracking tax obligations and compliance requirements
- Linking invoices to purchase orders and deliveries for three-way matching
- Supporting accounts payable processes and payment scheduling

## UI Structure Requirements

### 1. Form Header
- **Title**: "Buat Faktur Pembelian"
- **Card Layout**: Standard card with header and body sections
- **Form ID**: `purchaseInvoiceForm`

### 2. Section 1: Transaction Information
Contains essential invoice details in a 4-row layout:

#### Row 1: Basic Information
- **Supplier** (Required, Select2 dropdown)
  - Options: John, PT. Sumber Makmur, CV. Berkah Jaya, PT. Mandiri Sejahtera
  - Auto-populates email and address when selected
- **Email** (Required, Email input)
  - Auto-populated from supplier selection
  - Editable for corrections

#### Row 2: Address and Invoice Details
- **Alamat** (Required, Textarea, 3 rows)
  - Auto-populated from supplier selection
  - Placeholder: "autofill but editable"
- **Tanggal Transaksi** (Required, Date input)
  - Default: Today's date
  - Cannot be future date
- **Nomor Transaksi** (Required, Text input)
  - Auto-generated format: "PI-YYYY-XXX"
  - Placeholder: "autofill but editable"

#### Row 3: Purchase Reference and Payment Terms
- **Nomor Pemesanan Pembelian** (Optional, Text input with integration button)
  - Links to existing Purchase Orders
  - Sample: "Purchase Order 110001"
  - Integration button for PO lookup and auto-fill
- **Syarat Pembayaran** (Required, Select dropdown)
  - Options: Net 15, Net 30, Net 45, Cash on Delivery, Immediate
  - Default: Net 30
- **Nomor Referensi Supplier** (Optional, Text input)
  - Supplier's invoice reference number
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
- **Section Title**: "Informasi Pembelian"
- **Tax Checkbox**: "Harga Termasuk Pajak" (top-right)

#### Product Table Structure:
- **Produk** (Required, Select dropdown, 15% width)
  - Sample options: ALBUM SVT, Buku Tulis, Pensil, Penghapus
  - Auto-populates description, unit, price when selected
- **Deskripsi** (Auto-filled, Text input, 20% width)
  - Populated from product selection
  - Sample: "ALBUM SVT VOL 1"
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
  - Editable for negotiations
- **Pajak** (Select dropdown, 10% width)
  - Options: PPN, Tidak Ada Pajak
  - Sample: "PPN"
- **Jumlah** (Calculated, Currency input, 15% width)
  - Auto-calculated: Kuantitas × Harga Satuan (± Tax)
  - Sample: Rp 25,000,000.00
  - Read-only field

#### Product Management:
- **Initial Rows**: 4 rows with sample data in first row
- **Add Product Button**: "Tambah Produk" with plus icon
- **Remove Functionality**: Can remove rows (minimum 1 row)
- **Auto-calculation**: Real-time calculation of totals

### 4. Section 3: Additional Information & Invoice Summary
Split into two columns:

#### Left Column: Additional Information
- **Pesan** (Textarea, 4 rows, 500 chars max)
  - Message for supplier regarding invoice
  - Placeholder: "Catatan atau pesan untuk supplier..."
- **Memo** (Textarea, 4 rows, 500 chars max)
  - Internal notes for accounting team
  - Placeholder: "Memo internal..."
- **Lampiran** (File input)
  - Accepted formats: .xlsx, .xls, .docx, .doc, .pdf, .png, .zip
  - Maximum size: 10MB

#### Right Column: Invoice Summary
- **Subtotal**: Total before tax (Rp -)
- **Pajak**: Total tax amount (Rp -)
- **Total**: Final invoice amount (Rp -)
- **Sisa Tagihan**: Outstanding balance (Rp -)
- All fields formatted as "Rp XX,XXX,XXX"
- Read-only with real-time updates

### 5. Action Buttons
- **Batalkan** (Danger button)
  - Cancels invoice creation
  - Confirmation dialog before exit
- **Buat** (Success button)
  - Creates the invoice
  - Comprehensive validation before submission

## JavaScript Functionality Requirements

### 1. Auto-fill Features
- **Supplier Selection**: Populates email, address automatically
- **Product Selection**: Fills description, unit, and price
- **PO Integration**: Pre-fill entire form from selected Purchase Order
- **Payment Terms**: Auto-calculates due date based on terms

### 2. Tax Calculation Engine
- **Tax Mode Toggle**: Switch between tax-inclusive and tax-exclusive
- **Real-time Calculations**: Instant updates when tax mode changes
- **PPN Handling**: 11% tax calculation for applicable items
- **Summary Updates**: Automatic recalculation of subtotal, tax, total

### 3. Real-time Calculations
- **Row Calculations**: Quantity × Price with tax considerations
- **Grand Totals**: Subtotal, tax, and final total
- **Currency Formatting**: Indonesian Rupiah with thousand separators
- **Balance Tracking**: Outstanding amount calculations

### 4. Form Validation
- **Required Fields**: All marked fields must be filled
- **Email Validation**: Proper email format
- **Date Validation**: Transaction date not in future, due date after transaction date
- **Product Validation**: At least one product must be selected
- **Financial Validation**: Positive amounts and proper calculations
- **File Validation**: Type and size restrictions

### 5. Integration Features
- **PO Lookup**: Search and select purchase orders for pre-filling
- **Three-way Matching**: Link PO, Delivery, and Invoice
- **Supplier Data**: Auto-population of supplier information
- **Document Actions**: Post-creation actions (payment, download, share)

## Data Context
- **Business Flow**: PO → Delivery → **Invoice**
- **Purpose**: Financial documentation and payment obligation management
- **Integration**: Links to Purchase Orders, Deliveries, and Payment processing
- **Compliance**: Indonesian tax regulations and business practices

## Development Notes
- **Complex Calculations**: Robust tax and total calculations required
- **Financial Accuracy**: Precise currency handling and rounding
- **Document Generation**: PDF invoice generation capability
- **Payment Integration**: Links to payment recording system
- **Audit Trail**: Complete financial transaction logging

## Future Considerations
This invoice form will enable:
- **Payment Processing**: Link invoices to payment records
- **Financial Reporting**: Accounts payable and cash flow reports
- **Tax Compliance**: VAT reporting and compliance tracking
- **Supplier Analysis**: Payment performance and relationship management
- **Document Workflow**: PDF generation, email distribution, approval workflows

## Technical Implementation
- **Framework**: Laravel Blade templates
- **JavaScript**: jQuery with complex financial calculations
- **Styling**: Bootstrap + TailwindCSS
- **Validation**: Multi-layer validation (client-side, server-side, business rules)
- **File Upload**: Secure file handling for invoice supporting documents
- **Database**: Complex relationships with POs, deliveries, payments, and accounting entries
- **PDF Generation**: Invoice document generation with company branding
- **Email Integration**: Automated invoice distribution to suppliers