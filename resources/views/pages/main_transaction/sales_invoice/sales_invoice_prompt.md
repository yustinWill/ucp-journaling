# Sales Invoice (SI) Creation Form - Technical Specification

## Overview
This document provides comprehensive technical specifications for creating the Sales Invoice (SI) form in the sales management module. The form manages customer invoicing based on existing Sales Orders, with automatic financial calculations, payment term integration, and selective quantity invoicing for flexible billing processes in the sales workflow.

## Form Purpose
The Sales Invoice form is specifically designed for:
- Creating customer invoices exclusively from existing approved Sales Orders with complete SO data integration and validation
- Managing selective invoicing with quantity-based billing allowing partial invoicing against Sales Order items
- Supporting multiple invoice generation from single Sales Orders with accurate financial tracking and remaining balance calculations
- Automating payment terms and due date calculations based on Sales Order payment configuration for consistent billing cycles
- Providing comprehensive financial calculations with real-time total updates and Indonesian Rupiah formatting
- Integrating with customer payment tracking and accounts receivable management for complete financial workflow
- Enabling flexible billing scenarios including partial shipment invoicing and progressive billing arrangements
- Maintaining complete invoice audit trail linked to Sales Orders for customer service and financial reconciliation

## UI Structure Requirements

### 1. Form Header
- **Title**: "Tambah Invoice"
- **Card Layout**: Standard card with header and body sections
- **Form ID**: `salesInvoiceForm`

### 2. Main Layout Structure
- **Split Layout**: 8-column main form area + 4-column requirements information panel
- **Left Column**: Primary form content with SO reference and invoice details
- **Right Column**: Requirements and business rules for user guidance

### 3. Section 1: Sales Order Reference Information (Auto-filled from SO)
Complete Sales Order context and financial summary:

#### Row 1: Customer and Order Details
- **Supplier** (Customer Name) (Display only)
  - Auto-filled from SO customer data
  - Sample: "PT SINTOBA GRAFIKA"
  - Read-only field showing SO customer
- **No Purchase Order** (Sales Order Number) (Display only)
  - SO transaction number reference
  - Sample: "Purchase Order #PO/SYN/15/0825-"
  - Direct link to originating SO

#### Row 2: Status and Date Information
- **Status PO** (SO Status) (Display only)
  - Sales Order approval status
  - Sample: "Approved"
  - Only approved SOs can generate invoices
- **Tgl Purchase Order** (SO Date) (Display only)
  - Original Sales Order date
  - Sample: "26/08/2025"
  - Reference date from SO

#### Row 3: Financial Summary
- **Termin Pembayaran** (Payment Terms) (Display only)
  - Payment terms from SO
  - Sample: "NET 30"
  - Auto-populated from SO configuration
- **Total PO** (SO Total) (Display only)
  - Total Sales Order amount
  - Sample: "Rp 85.750.000"
  - Complete SO financial value

### 4. Section 2: Invoice Information (User Input Section)
Invoice-specific details and configuration:

#### Row 1: Invoice Identification
- **Nomor Invoice** (Invoice Number) (Required, Text input)
  - Auto-generated invoice number
  - Sample: "INV-003"
  - Format: INV-XXX, editable if needed
  - Unique identifier for invoice
- **Tanggal Invoice** (Invoice Date) (Required, Date input)
  - Invoice creation date
  - Sample: "08/28/2025"
  - Date picker with validation
  - Default: Today's date

#### Row 2: Transaction and Payment Dates
- **Tanggal Transaksi** (Transaction Date) (Required, Date input)
  - Invoice transaction date
  - Sample: "08/29/2025"
  - Date picker with validation
  - Can be different from invoice date
- **Tanggal Jatuh Tempo** (Due Date) (Auto-calculated, Display only)
  - Payment due date
  - Sample: "09/27/2025"
  - Auto-calculated: Transaction Date + Payment Terms
  - Help text: "*Otomatis dari Purchase Order"

#### Row 3: Payment Terms Reference
- **Termin Pembayaran** (Payment Terms) (Display only)
  - Payment terms inherited from SO
  - Sample: "NET 30"
  - Help text: "*Dari Purchase Order"
  - Used for due date calculation

#### Row 4: Invoice Notes
- **Keterangan** (Notes) (Optional, Textarea)
  - Invoice-specific notes and comments
  - Placeholder: "Keterangan tambahan untuk Invoice"
  - 4 rows, 1000 characters max
  - For billing clarifications and special instructions

### 5. Section 3: Items Invoice (SO Items with Invoice Quantities)
Product invoicing with quantity selection and financial calculations:

#### Information Box:
- **Invoice Instructions**: "Pilih dan masukkan quantity item yang akan di-invoice dari Purchase Order. Sistem akan otomatis menghitung total amount."
- **Process Guidance**: Clear instruction for invoice quantity selection

#### Items Invoice Table:
- **Dynamic Table** populated from SO items
- **Columns**:
  - **Produk** (Product) (Display only)
    - Product names from SO
    - Sample: "Laptop Dell Inspiron 15", "Mouse Wireless Logitech", "Keyboard Mechanical"
    - Auto-populated from SO items
    - Cannot be modified
  - **Qty PO** (SO Quantity) (Display only)
    - Original quantity ordered in SO
    - Sample: 5, 10, 5
    - Shows total ordered quantity per item
    - Reference for invoice quantity limits
  - **Qty Invoice** (Invoice Quantity) (Required, Number input)
    - Quantity being invoiced for each item
    - Sample: 5, 0, 0 (partial invoicing example)
    - Validation: Cannot exceed SO quantity
    - Validation: Must be zero or positive integer
  - **Harga Satuan** (Unit Price) (Display only)
    - Product selling price from SO
    - Sample: "Rp 15.500.000", "Rp 450.000", "Rp 1.650.000"
    - Auto-populated from SO pricing
    - Indonesian Rupiah formatting
  - **Total** (Line Total) (Auto-calculated, Display only)
    - Line total calculation
    - Calculation: Qty Invoice × Unit Price
    - Sample: "Rp 77.500.000" (for 5 × Rp 15.500.000)
    - Real-time updates as quantities change

#### Invoice Financial Summary:
- **Total Invoice Amount**: "Rp 77.500.000"
  - Sum of all line totals
  - Real-time calculation
  - Indonesian Rupiah formatting
  - Must be greater than zero for save
- **Total Items Invoice**: "1 Items"
  - Count of items with non-zero invoice quantities
  - Updates automatically as quantities change

### 6. Action Buttons
- **Batal** (Cancel) (Secondary button)
  - Color: Gray
  - Function: Cancel invoice creation
  - Returns to previous screen
- **💼 Simpan Invoice** (Save Invoice) (Primary button)
  - Color: Blue (#007bff)
  - Icon: Briefcase/invoice icon
  - Function: Create invoice record
  - Validation: Invoice number, invoice date, transaction date, total > 0

## JavaScript Functionality Requirements

### 1. Sales Order Integration
- **SO Data Loading**: Auto-populate all SO reference information on form load
- **Customer Data**: Display complete customer and order information from SO
- **Financial Summary**: Show SO totals and payment terms for reference
- **Item Population**: Load all SO items into invoice table automatically

### 2. Invoice Financial Calculations
- **Line Total Calculation**: Auto-calculate line totals (Qty × Unit Price)
- **Invoice Total**: Sum all line totals for complete invoice amount
- **Real-time Updates**: Update totals as invoice quantities change
- **Currency Formatting**: Proper Indonesian Rupiah formatting throughout

### 3. Payment Terms and Date Management
- **Due Date Calculation**: Auto-calculate due date (Transaction Date + Payment Terms)
- **Date Validation**: Ensure dates are valid and logical
- **Payment Terms Display**: Show inherited payment terms from SO
- **Date Format**: Indonesian date format (DD/MM/YYYY)

### 4. Invoice Validation and Business Rules
- **Required Fields**: Validate invoice number, invoice date, and transaction date
- **Non-zero Total**: Prevent saving invoices with zero total amount
- **Quantity Validation**: Ensure invoice quantities don't exceed SO quantities
- **Approved SO Only**: Only allow invoicing from approved Sales Orders

## Data Context
- **Business Flow**: Sales Order → Sales Delivery → **Sales Invoice** (or SO → SI → SD)
- **Purpose**: Customer billing and accounts receivable management
- **Integration**: Links to Sales Order module and updates invoice status
- **Financial Process**: Foundation for payment tracking and revenue recognition

## Development Notes
- **SO Dependency**: Cannot create invoice without valid approved Sales Order reference
- **Data Inheritance**: All pricing and customer data inherited from SO
- **Quantity Flexibility**: Support partial invoicing with quantity selection
- **Multiple Invoices**: Support for multiple invoices against single SO
- **Financial Accuracy**: Precise calculations and validation for billing integrity

## Future Considerations
This sales invoice form will enable:
- **Advanced Invoicing**: Complex invoicing rules and progressive billing
- **Payment Integration**: Direct payment processing and gateway integration
- **Tax Management**: Advanced tax calculation and compliance features
- **Customer Portal**: Customer access to invoice history and payment status
- **Accounting Integration**: Direct integration with accounting and ERP systems
- **Invoice Templates**: Customizable invoice layouts and branding options

## Technical Implementation
- **Framework**: Laravel Blade templates with SO integration
- **JavaScript**: jQuery with real-time financial calculations
- **Styling**: Bootstrap + TailwindCSS with responsive design
- **Database**: Integration with sales_orders, invoice_items, and financial_transactions tables
- **Validation**: Multi-layer validation including business rules and financial controls
- **Real-time Data**: Live SO data and pricing integration
- **Transaction Management**: Proper database transactions for invoice creation and SO updates

## Special Features & Business Logic

### Integration Notes (Based on Requirements and Right Panel Information)
**Sales Invoice Context**:

**Business Requirements from Right Panel**:

#### **Requirement Sales Invoice (SI)**:
**1. Relasi dengan Sales Order (SO)**:
- **Sales Invoice (SI) hanya bisa dibuat jika ada Sales Order (SO)**: SI can only be created if SO exists
- **Saat user buat SI, user harus Silih referensi SO terlebih dahulu**: When creating SI, user must select SO reference first
- **Semua data dasar (Customer, alamat pengiriman, tanggal SO, item SO, qty SO, dll.) autofill dari SO**: All basic data auto-fills from SO

#### **Business Rules**:
- **Invoice hanya bisa dibuat berdasarkan SO yang Approved**: Invoice can only be created from approved SOs
- **Bisa multiple invoice selama masih ada qty tersisa pada SO**: Multiple invoices allowed while quantities remain on SO
- **Sistem menghitung otomatis jatuh temSO berdasarkan termin pembayaran**: System automatically calculates due date based on payment terms
- **Jika total invoice = 0, sistem tidak boleh menyimpan invoice**: If invoice total = 0, system cannot save invoice

### Enhanced Sales Invoice Management Features

#### **Sales Order Integration System**:
- **Mandatory SO Reference**: Invoice cannot be created without valid approved Sales Order
- **Complete Data Inheritance**: All customer, pricing, and payment data from SO
- **Approval Validation**: Only approved SOs can generate invoices
- **Reference Chain**: Maintain clear audit trail to originating SO

#### **Advanced Invoicing Capabilities**:
- **Selective Quantity Invoicing**: Choose specific quantities to invoice per item
- **Multiple Invoice Support**: Generate multiple partial invoices against single SO
- **Progressive Billing**: Support for milestone-based or partial delivery invoicing
- **Remaining Balance Tracking**: Track uninvoiced quantities and amounts

#### **Financial Management and Calculations**:
- **Automatic Due Date Calculation**: Calculate payment due dates based on SO payment terms
- **Real-time Financial Totals**: Live calculation of invoice amounts as quantities change
- **Zero-Total Prevention**: Business rule preventing empty or zero-value invoices
- **Currency Integration**: Proper Indonesian Rupiah formatting and calculations

### Business Rules and Validation

#### **Sales Order Dependency Rules**:
- **SO Approval Requirement**: Only approved Sales Orders can generate invoices
- **SO Reference Mandatory**: Valid SO selection required before invoice creation
- **Data Consistency**: Invoice data must match SO pricing and customer information
- **Status Tracking**: Invoice creation updates SO invoice status appropriately

#### **Financial and Quantity Controls**:
- **Non-zero Invoice Rule**: Invoices must have total amount greater than zero
- **Quantity Limits**: Invoice quantities cannot exceed SO ordered quantities
- **Pricing Consistency**: Use SO pricing for accurate financial calculations
- **Multiple Invoice Tracking**: Track cumulative invoiced amounts against SO totals

#### **Payment and Terms Management**:
- **Payment Terms Inheritance**: Auto-apply payment terms from Sales Order
- **Due Date Automation**: Calculate payment due dates automatically
- **Date Validation**: Ensure transaction and invoice dates are logical and valid
- **Payment Integration**: Foundation for payment tracking and accounts receivable

### Workflow Integration

#### **Sales Document Flow Integration**:
- **SO Foundation**: Sales Invoice built upon existing approved Sales Order
- **Flexible Workflow**: Support SO → SI → SD or SO → SD → SI flows
- **Status Updates**: Invoice creation updates SO invoice status and remaining balances
- **Financial Tracking**: Complete invoice audit trail linked to Sales Orders

#### **Customer Financial Management**:
- **Accounts Receivable**: Integration with customer payment tracking
- **Credit Management**: Invoice creation considers customer credit limits
- **Payment Processing**: Foundation for payment collection and reconciliation
- **Customer Communication**: Invoice documentation for customer billing processes

#### **Operational and Compliance**:
- **Financial Accuracy**: Precise calculations for revenue recognition and accounting
- **Audit Trail**: Complete invoice creation and modification history
- **Tax Integration**: Proper tax calculation and compliance documentation
- **Reporting Foundation**: Data structure supporting financial and sales reporting

This comprehensive sales invoice system provides complete billing management with strict SO integration, flexible quantity-based invoicing, and proper financial controls while maintaining business rule compliance and supporting various invoicing scenarios in the sales workflow.