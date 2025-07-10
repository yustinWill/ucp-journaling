# Purchase Order (PO) Creation Form - Technical Specification

## Overview
This document provides comprehensive technical specifications for creating the Purchase Order (PO) form in the purchase module. The PO is the first step in the procurement process where orders are placed with suppliers.

## Form Purpose
The Purchase Order form is specifically designed for:
- Creating formal purchase orders to be sent to suppliers
- Specifying product requirements, quantities, and delivery terms
- Establishing payment terms and conditions
- Managing supplier relationships and delivery addresses
- Tracking ordered quantities that will later be matched with deliveries

## UI Structure Requirements

### 1. Form Header
- **Title**: "Buat Purchase Order (PO)"
- **Card Layout**: Standard card with header and body sections
- **Form ID**: `purchaseOrderForm`

### 2. Section 1: Purchase Order Information
Contains essential PO details in a 4-row layout:

#### Row 1: Basic Information
- **Supplier** (Required, Select2 dropdown)
  - Options: PT. Sumber Makmur, CV. Berkah Jaya, PT. Mandiri Sejahtera, CV. Maju Bersama
- **Email Supplier** (Required, Email input)
  - Auto-populated when supplier selected
  - Editable for corrections

#### Row 2: Address and PO Details
- **Alamat Supplier** (Required, Textarea, 3 rows)
  - Auto-populated when supplier selected
  - Editable for corrections
- **Tanggal PO** (Required, Date input)
  - Default: Today's date
- **Nomor PO** (Required, Text input)
  - Auto-generated format: "PO-YYYY-XXX"
  - Editable if needed

#### Row 3: Payment and Reference
- **Tag PO** (Optional, Text input)
  - For categorizing purchase orders
- **Syarat Pembayaran** (Required, Select dropdown)
  - Options: Net 15, Net 30, Net 45, Cash on Delivery, Immediate
  - Default: Net 30
- **No. Referensi Supplier** (Optional, Text input)
  - For supplier's internal reference
- **Tgl Jatuh Tempo** (Required, Date input)
  - Auto-calculated based on payment terms

#### Row 4: Delivery Information
- **Gudang Tujuan** (Optional, Select dropdown)
  - Options: Gudang Utama, Gudang Cabang A, Gudang Cabang B
- **Alamat Pengiriman** (Optional, Textarea, 3 rows)
  - Auto-populated from supplier address
  - Editable for different delivery location

### 3. Section 2: Product Information
- **Section Title**: "Daftar Produk yang Dipesan"
- **Tax Checkbox**: "Harga Termasuk Pajak" (top-right)

#### Product Table Structure:
- **Produk** (Required, Select dropdown, 20% width)
  - Sample options: ALBUM SVT, Buku Tulis, Pensil, Penghapus
  - Auto-populates description, unit, price when selected
- **Deskripsi** (Auto-filled, Text input, 20% width)
  - Populated from product selection
  - Read-only field
- **Qty Pesan** (Required, Number input, 10% width)
  - Quantity being ordered
  - Minimum value: 1
- **Unit** (Auto-filled, Text input, 8% width)
  - Unit of measurement (pcs, box, kg, etc.)
  - Read-only field
- **Harga Satuan** (Required, Currency input, 15% width)
  - Auto-filled from product data
  - Editable for negotiations
- **Pajak** (Select dropdown, 12% width)
  - Options: PPN, Tidak Ada Pajak
- **Total Harga** (Calculated, Currency input, 15% width)
  - Auto-calculated: Qty × Harga Satuan (± Tax)
  - Read-only field
- **Aksi** (Button, 5% width)
  - Remove row button

#### Product Management:
- **Initial Rows**: 4 empty rows by default
- **Add Product Button**: "Tambah Produk" with plus icon
- **Remove Functionality**: Can remove rows (minimum 1 row)
- **Auto-calculation**: Real-time calculation of totals

### 4. Section 3: Additional Information & PO Summary
Split into two columns:

#### Left Column: Additional Information
- **Pesan untuk Supplier** (Textarea, 4 rows, 500 chars max)
  - Message to be sent with PO
  - Placeholder: "Catatan atau instruksi khusus untuk supplier"
- **Memo Internal** (Textarea, 4 rows, 500 chars max)
  - Internal notes for purchasing team
  - Placeholder: "Catatan internal untuk tim purchasing"
- **Lampiran PO** (File input)
  - Accepted formats: .xlsx, .xls, .docx, .doc, .pdf, .png, .zip
  - Maximum size: 10MB

#### Right Column: PO Summary
- **Subtotal**: Total before tax
- **Pajak (PPN)**: Total tax amount
- **Total PO**: Final total amount
- **Sisa Pembayaran**: Amount to be paid
- All fields formatted as "Rp XX,XXX,XXX"
- Read-only with descriptive help text

### 5. Action Buttons
- **Batalkan** (Danger button)
  - Cancels PO creation
  - Confirmation dialog before exit
- **Buat Purchase Order** (Success button)
  - Creates the PO
  - Comprehensive validation before submission

## JavaScript Functionality Requirements

### 1. Auto-fill Features
- **Supplier Selection**: Populates email, address, and delivery address
- **Product Selection**: Fills description, unit, and price
- **Payment Terms**: Auto-calculates due date

### 2. Real-time Calculations
- **Row Calculations**: Quantity × Price (with tax considerations)
- **Grand Totals**: Subtotal, tax, and final total
- **Tax Handling**: Both inclusive and exclusive tax calculations

### 3. Form Validation
- **Required Fields**: All marked fields must be filled
- **Email Validation**: Proper email format
- **Date Validation**: PO date not in future, due date after PO date
- **Product Validation**: At least one product must be selected
- **File Validation**: Type and size restrictions

### 4. Dynamic Features
- **Add/Remove Rows**: Dynamic product table management
- **Currency Formatting**: Indonesian Rupiah formatting
- **Date Calculations**: Automatic due date calculation
- **Real-time Updates**: Instant calculation updates

## Data Context
- **Business Flow**: PO → Delivery → Invoice
- **Purpose**: Formal ordering document sent to suppliers
- **Integration**: Links to delivery and invoice tracking
- **Compliance**: Indonesian business practices and tax regulations

## Development Notes
- **Responsive Design**: Mobile-friendly layout
- **Performance**: Efficient DOM manipulation
- **User Experience**: Clear validation messages and confirmations
- **Accessibility**: Proper labeling and keyboard navigation

## Future Considerations
This PO form will be the foundation for:
- **Purchase Delivery (PD)**: Tracking received goods against PO
- **Purchase Invoice (PI)**: Matching invoices with PO and deliveries
- **Supplier Management**: Performance tracking and relationship management
- **Inventory Integration**: Stock level updates upon delivery

## Technical Implementation
- **Framework**: Laravel Blade templates
- **JavaScript**: jQuery with real-time calculations
- **Styling**: Bootstrap + TailwindCSS
- **Validation**: Client-side and server-side validation
- **File Upload**: Secure file handling with type/size restrictions