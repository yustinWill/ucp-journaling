# Purchase Delivery (PD) Creation Form - Technical Specification

## Overview
This document provides comprehensive technical specifications for creating the Purchase Delivery (PD) form in the purchase module. The PD tracks received goods from suppliers and serves as a bridge between purchase orders and invoices.

## Form Purpose
The Purchase Delivery form is specifically designed for:
- Recording receipt of goods from suppliers against existing purchase orders
- Documenting delivery details and quantities received
- Creating delivery documentation for inventory management
- Facilitating smooth transition from purchase orders to invoice processing
- Tracking delivery status and supplier performance

## UI Structure Requirements

### 1. Form Header
- **Title**: "Buat Pengiriman Pembelian"
- **Card Layout**: Standard card with header and body sections
- **Form ID**: `purchaseDeliveryForm`

### 2. Section 1: Transaction Information
Contains essential delivery details in a 4-row layout:

#### Row 1: Basic Information
- **Supplier** (Required, Select2 dropdown)
  - Options: John, PT. Sumber Makmur, CV. Berkah Jaya, PT. Mandiri Sejahtera
  - Auto-populates email when selected
- **Email** (Required, Email input)
  - Auto-populated from supplier selection
  - Editable for corrections

#### Row 2: Delivery Details
- **Tanggal Pengiriman** (Required, Date input)
  - Default: Today's date
  - Cannot be future date
- **Nomor Transaksi** (Required, Text input)
  - Auto-generated format: "PD-YYYY-XXX"
  - Placeholder: "autofill but editable"

#### Row 3: Reference Information
- **Nomor Referensi Pembelian** (Optional, Text input with integration button)
  - Links to existing Purchase Orders
  - Sample: "Kerjasama Utama 11008"
  - Integration button for PO lookup
- **Nomor Referensi Supplier** (Optional, Text input)
  - Supplier's delivery reference number
- **Tag** (Optional, Text input)
  - For categorizing deliveries

#### Row 4: Storage Information
- **Gudang** (Optional, Select dropdown)
  - Options: Gudang Utama, Gudang Cabang A, Gudang Cabang B
  - Placeholder: "Pilih Gudang"

### 3. Section 2: Product Information
Simple delivery tracking table without pricing:

#### Product Table Structure:
- **Nama Produk** (Required, Text/Select input, 25% width)
  - Product identification
  - Sample: "Robot plastik 600 ml"
- **Deskripsi** (Optional, Text input, 40% width)
  - Product description
  - Sample: "mainan keramgelit"
- **Kuantitas** (Required, Number input, 15% width)
  - Quantity received
  - Sample: "1"
  - Minimum value: 1
- **Unit** (Auto-filled, Text input, 20% width)
  - Unit of measurement
  - Sample: "pcs"

#### Product Management:
- **Initial Rows**: Single row with sample data
- **Add Product Button**: Dynamic row addition
- **Remove Functionality**: Can remove rows (minimum 1 row)
- **Simple Structure**: Focus on delivery documentation only

### 4. Section 3: Additional Information
Single column layout for documentation:

#### Documentation Fields:
- **Pesan** (Textarea, 4 rows, 500 chars max)
  - Delivery notes or special instructions
  - Placeholder: "Catatan pengiriman..."
- **Memo** (Textarea, 4 rows, 500 chars max)
  - Internal memo for delivery tracking
  - Placeholder: "Memo internal..."
- **Lampiran** (File input)
  - Accepted formats: .xlsx, .xls, .docx, .doc, .pdf, .png, .zip
  - Maximum size: 10MB
  - Help text: "File dapat berupa Excel, Word, PDF, PNG, atau Zip"

### 5. Action Buttons
- **Batalkan** (Danger button)
  - Cancels delivery creation
  - Confirmation dialog before exit
- **Buat** (Success button)
  - Creates the delivery record
  - Validation before submission

## JavaScript Functionality Requirements

### 1. Auto-fill Features
- **Supplier Selection**: Populates email field automatically
- **PO Integration**: Pre-fill form from selected Purchase Order
- **Transaction Number**: Auto-generation with edit capability

### 2. Form Management
- **Dynamic Rows**: Add/remove product lines
- **PO Lookup**: Integration button functionality for PO selection
- **Data Validation**: Required field validation

### 3. Form Validation
- **Required Fields**: Supplier, Email, Delivery Date, Transaction Number, Product Name, Quantity
- **Email Validation**: Proper email format
- **Date Validation**: Delivery date not in future
- **Product Validation**: At least one product must be entered
- **File Validation**: Type and size restrictions

### 4. Integration Features
- **PO Pre-fill**: Button to select and populate from existing PO
- **Supplier Data**: Auto-population of supplier information
- **Navigation**: Smooth workflow from PO to delivery

## Data Context
- **Business Flow**: PO → **Delivery** → Invoice
- **Purpose**: Document receipt of goods from suppliers
- **Integration**: Links Purchase Orders to Invoice processing
- **Tracking**: Delivery status and inventory updates

## Development Notes
- **Simplified Structure**: Focus on delivery documentation vs. financial calculations
- **PO Integration**: Seamless connection to purchase order system
- **Mobile Responsive**: Delivery staff may use mobile devices
- **Quick Entry**: Streamlined for fast delivery processing

## Future Considerations
This delivery form will enable:
- **Invoice Matching**: Link deliveries to supplier invoices
- **Inventory Updates**: Automatic stock level adjustments
- **Delivery Analytics**: Supplier performance tracking
- **Quality Control**: Integration with inspection processes

## Technical Implementation
- **Framework**: Laravel Blade templates
- **JavaScript**: jQuery with PO integration
- **Styling**: Bootstrap + TailwindCSS
- **Validation**: Client-side and server-side validation
- **File Upload**: Secure file handling for delivery documentation
- **Database**: Links to purchase_orders and prepares for invoices table