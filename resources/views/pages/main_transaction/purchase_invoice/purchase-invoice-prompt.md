# Purchase Invoice (PI) Creation Form - Technical Specification

## Overview
This document provides comprehensive technical specifications for creating the Purchase Invoice (PI) form in the financial management module. The form manages payment fulfillment for Purchase Orders with support for partial payments, automatic payment term calculations, and comprehensive financial tracking.

## Form Purpose
The Purchase Invoice form is specifically designed for:
- Creating payment fulfillment records exclusively from approved Purchase Orders with mandatory PO reference validation
- Managing partial and complete payment processing with flexible item selection and quantity-based invoicing
- Automatically calculating payment due dates based on Purchase Order payment terms and invoice dates
- Supporting multiple partial invoices against single Purchase Orders for flexible payment arrangements
- Integrating with accounts payable systems for comprehensive financial tracking and vendor payment management
- Providing automatic payment term application with due date calculation from Purchase Order configurations
- Enabling selective item invoicing for complex payment scenarios and budget management requirements
- Maintaining complete financial audit trail linking invoices to source Purchase Orders for compliance and reporting

## UI Structure Requirements

### 1. Form Header
- **Title**: "Tambah Invoice"
- **Card Layout**: Standard card with header and body sections
- **Form ID**: `purchaseInvoiceForm`

### 2. Section 1: Purchase Order Reference (Referensi Purchase Order)
Auto-filled information from selected Purchase Order:

#### PO Information Card (Display only):
##### Row 1: Basic PO Information
- **Supplier** (Display only)
  - Supplier company name from PO
  - Sample: "PT SYNTOBA GRAFIKA"
  - Auto-populated, non-editable
- **Email** (Display only)
  - Supplier email address from PO
  - Sample: "supplier@syntoba.com"
  - Contact information for payment processing
- **No. Transaksi** (Display only)
  - Purchase Order transaction number
  - Sample: "Purchase Order #PO/SYN/15/0825-2"
  - Clickable link to PO details
- **Tgl Purchase Order** (Display only)
  - Purchase Order date
  - Sample: "26/08/2025"
  - Format: DD/MM/YYYY

##### Row 2: Financial Information
- **Status PO** (Display only)
  - Purchase Order approval status
  - Sample: "Approved"
  - Required status for invoice creation
- **Syarat Pembayaran** (Display only)
  - Payment terms from PO
  - Sample: "NET 30"
  - Used for due date calculation
- **Grand Total** (Display only)
  - Total PO amount
  - Sample: "Rp 85,750,000"
  - Indonesian Rupiah format
  - Reference amount for payment validation

#### Footer Note:
- **Source Indicator**: "*Dari Purchase Order" (From Purchase Order)
- **Data Integrity**: Indicates auto-filled data source

### 3. Section 2: Invoice Information (Informasi Invoice)
User-input fields for invoice-specific information:

#### Row 1: Invoice Identification
- **Nomor Invoice** (Invoice Number) (Required, Text input)
  - Auto-generated invoice identifier
  - Sample: "INV-003"
  - Format: INV-XXX, editable if needed
- **Tanggal Invoice** (Invoice Date) (Required, Date input)
  - Invoice creation date
  - Sample: "08/28/2025"
  - Format: DD/MM/YYYY
  - Used for due date calculation

#### Row 2: Transaction and Due Dates
- **Tanggal Transaksi** (Transaction Date) (Required, Date input)
  - Payment transaction processing date
  - Sample: "08/29/2025"
  - Format: DD/MM/YYYY
  - Cannot be before invoice date
- **Tanggal Jatuh Tempo** (Due Date) (Auto-calculated, Date display)
  - Payment due date
  - Sample: "09/27/2025"
  - Auto-calculated: Invoice Date + Payment Terms
  - Note: "*Dihitung dari Purchase Order"

#### Row 3: Payment Terms
- **Termin Pembayaran** (Payment Terms) (Display only)
  - Payment terms from PO
  - Sample: "NET 30"
  - Auto-populated from Purchase Order
  - Note: "*Dari Purchase Order"

#### Row 4: Additional Information
- **Keterangan** (Notes) (Optional, Textarea)
  - Invoice notes and payment comments
  - Placeholder: "Keterangan tambahan untuk invoice"
  - 4 rows, 1000 characters max
  - Payment-specific information

### 4. Section 3: Items Invoice
Selective item invoicing with quantity control:

#### Information Box:
- **Instructional Text**: "Info: Pilih dan masukkan quantity item yang akan di-invoice dari Purchase Order. Sistem akan otomatis menghitung total amount."
- **User Guidance**: Clear explanation of selective invoicing and automatic calculations

#### Invoice Items Table:
- **Auto-populated Table** from PO items
- **Columns**:
  - **Produk** (Product) (Display only, Text)
    - Product name from PO
    - Sample: "Laptop Dell Inspiron 15", "Mouse Wireless Logitech"
    - Multi-line product names supported
  - **Qty PO** (PO Quantity) (Display only, Number)
    - Original quantity from Purchase Order
    - Sample: "5", "10"
    - Reference quantity for invoice validation
  - **Qty Invoice** (Invoice Quantity) (Required, Number input)
    - User-input invoice quantity
    - Sample: "[5]", "[0]" (input fields)
    - Validation: Cannot exceed Qty PO
    - Support for zero quantities (partial invoicing)
  - **Harga Satuan** (Unit Price) (Display only, Currency)
    - Unit price from PO
    - Sample: "Rp 15,500,000", "Rp 450,000"
    - Indonesian Rupiah format
  - **Total** (Total Amount) (Auto-calculated, Currency)
    - Calculated: Qty Invoice × Harga Satuan
    - Sample: "Rp 77,500,000", "Rp 0"
    - Real-time calculation

#### Invoice Summary:
- **Total Invoice Amount**: Auto-calculated sum of all item totals
- **Total Items Invoice**: Count of items with quantity > 0
- **Format**: "Total Invoice Amount: Rp X,XXX,XXX" and "Total Items Invoice: X items"

### 5. Action Buttons
- **Batal** (Cancel) (Secondary button)
  - Color: Gray
  - Function: Cancel invoice creation and return to invoice list
  - No confirmation required
- **Simpan Invoice** (Save Invoice) (Primary button)
  - Color: Blue (#007bff)
  - Function: Create invoice record and update payment status
  - Validation: Require at least one item with quantity > 0
  - Total amount must be greater than zero

## JavaScript Functionality Requirements

### 1. Purchase Order Integration
- **PO Selection**: Dropdown or modal for selecting approved Purchase Orders
- **Auto-fill Population**: Populate all PO-related fields when PO selected
- **Status Validation**: Verify PO is approved and available for invoicing
- **Financial Validation**: Check PO financial details and payment terms

### 2. Payment Term Management
- **Due Date Calculation**: Auto-calculate due dates based on payment terms
- **Payment Term Display**: Show payment terms from selected PO
- **Date Validation**: Ensure transaction dates are logical and valid
- **Term Application**: Apply payment terms consistently across invoice

### 3. Partial Payment Support
- **Quantity Validation**: Prevent invoice quantities exceeding PO quantities
- **Selective Invoicing**: Allow zero quantities for items not being invoiced
- **Real-time Calculations**: Update totals as user enters quantities
- **Multiple Invoice Support**: Track previous invoices against same PO

### 4. Financial Calculations
- **Amount Calculations**: Auto-calculate line totals and invoice totals
- **Currency Formatting**: Proper Indonesian Rupiah formatting
- **Validation Rules**: Ensure positive amounts and valid financial data
- **Payment Tracking**: Track payment amounts against PO totals

## Data Context
- **Business Flow**: Purchase Order Approval → **Purchase Invoice** → Payment Processing → Financial Recording
- **Purpose**: Payment fulfillment and financial settlement for approved purchases
- **Integration**: Links to Purchase Order, Accounts Payable, and Financial modules
- **Dependency**: Cannot exist without approved Purchase Order reference

## Development Notes
- **PO Approval Requirement**: Only approved Purchase Orders can be invoiced
- **Partial Payment Logic**: Support multiple partial invoices per Purchase Order
- **Financial Accuracy**: Precise calculation and currency handling
- **Payment Term Integration**: Seamless integration with PO payment terms
- **Audit Trail**: Complete financial audit trail for compliance

## Future Considerations
This purchase invoice form will enable:
- **Payment Automation**: Integration with automated payment processing systems
- **Multi-currency Support**: Support for international suppliers and multi-currency invoicing
- **Advanced Approval**: Multi-level approval workflows for large invoice amounts
- **Integration Enhancement**: Advanced ERP and financial system integration
- **Mobile Invoice**: Mobile-optimized invoice creation and approval capabilities
- **Document Management**: Electronic document attachment and invoice imaging

## Technical Implementation
- **Framework**: Laravel Blade templates with PO integration and financial calculations
- **JavaScript**: jQuery with real-time calculations, validation, and payment term handling
- **Styling**: Bootstrap + TailwindCSS with responsive design and financial data display
- **Database**: Integration with purchase orders, accounts payable, and financial tables
- **Validation**: Multi-layer validation including financial rules and payment term logic
- **Transaction Management**: Atomic transactions for invoice creation and payment tracking
- **Financial Integration**: Real-time integration with accounting and financial systems

## Special Features & Business Logic

### Integration Notes (Based on Requirements and Right Panel Information)
**Purchase Invoice Context**:

**Business Requirements from Right Panel**:

#### **Requirement Purchase Invoice (PI)**:
1. **Relasi dengan Purchase Order (PO)**: "Purchase Invoice (PI) hanya bisa dibuat jika ada Purchase Order (PO). Saat user buat PI, user harus pilih referensi PO terlebih dahulu. Semua data dasar (supplier, alamat pengiriman, tanggal PO, item PO, qty PO, dll.) autofill dari PO."

#### **Business Rules**:
- **Invoice hanya bisa dibuat berdasarkan PO yang Approved**: Only approved Purchase Orders can be invoiced
- **Bisa multiple invoice selama masih ada qty tersisa pada PO**: Support multiple partial invoices while quantities remain
- **Sistem menghitung otomatis jatuh tempo berdasarkan termin pembayaran**: Automatic due date calculation based on payment terms
- **Jika total invoice = 0, sistem tidak boleh menyimpan invoice**: Cannot save invoice with zero total amount

### Enhanced Purchase Invoice Management Features

#### **Purchase Order Dependency Management**:
- **Approval Validation**: Strict validation that only approved Purchase Orders can be invoiced
- **Complete Data Integration**: All supplier, product, and financial data auto-filled from PO
- **Status Tracking**: Real-time tracking of PO invoice status and completion
- **Reference Integrity**: Maintain complete linkage between invoices and source Purchase Orders

#### **Flexible Partial Payment System**:
- **Selective Item Invoicing**: Choose specific items and quantities to invoice from PO
- **Multiple Invoice Support**: Create multiple partial invoices against single Purchase Order
- **Quantity Tracking**: Track invoiced quantities against original PO quantities
- **Remaining Balance Management**: Monitor remaining uninvoiced amounts per Purchase Order

#### **Automatic Payment Term Application**:
- **Payment Term Integration**: Auto-apply payment terms from Purchase Order
- **Due Date Calculation**: Automatic calculation of payment due dates
- **Term Consistency**: Maintain payment term consistency between PO and invoices
- **Financial Planning**: Support financial planning through accurate due date management

### Business Rules and Validation

#### **Invoice Creation Rules**:
- **PO Approval Requirement**: Only approved Purchase Orders eligible for invoicing
- **Quantity Limits**: Invoice quantities cannot exceed remaining PO quantities
- **Zero Amount Prevention**: Cannot save invoices with zero total amounts
- **Multiple Invoice Logic**: Support multiple invoices while tracking cumulative amounts

#### **Financial Validation Controls**:
- **Payment Term Validation**: Ensure proper payment terms application from PO
- **Amount Accuracy**: Validate invoice amounts against PO pricing and quantities
- **Date Logic**: Ensure logical date relationships (invoice, transaction, due dates)
- **Currency Consistency**: Maintain currency consistency between PO and invoices

#### **Data Integrity Management**:
- **PO Data Immutability**: PO-sourced data cannot be modified during invoice creation
- **Reference Tracking**: Complete audit trail linking invoices to source Purchase Orders
- **Status Updates**: Update PO invoice status based on invoice completion
- **Financial Reconciliation**: Ensure proper financial reconciliation between PO and invoices

### Advanced Payment Management

#### **Partial Payment Processing**:
- **Flexible Invoicing**: Support various partial payment scenarios and arrangements
- **Quantity-based Billing**: Bill specific quantities independent of delivery status
- **Progressive Invoicing**: Support progressive billing against long-term Purchase Orders
- **Payment Milestone**: Support milestone-based payment arrangements

#### **Financial Integration**:
- **Accounts Payable Integration**: Real-time integration with AP systems
- **Payment Tracking**: Complete payment status tracking and management
- **Vendor Management**: Supplier payment history and relationship management
- **Cash Flow Management**: Support cash flow planning through due date management

#### **Compliance and Audit**:
- **Complete Audit Trail**: Full documentation of invoice creation and approval
- **Financial Compliance**: Ensure compliance with financial regulations and standards
- **Vendor Relations**: Maintain proper vendor relationship documentation
- **Payment Documentation**: Complete payment processing documentation

### System Integration Capabilities

#### **Purchase Order Integration**:
- **Real-time PO Status**: Live Purchase Order status checking and validation
- **Data Synchronization**: Seamless data flow between PO and invoice systems
- **Status Updates**: Automatic PO status updates based on invoice processing
- **Completion Tracking**: Track PO completion through invoice fulfillment

#### **Financial System Integration**:
- **Chart of Accounts**: Integration with accounting systems for proper cost allocation
- **Payment Processing**: Foundation for payment processing and cash management
- **Budget Management**: Support budget tracking and expense management
- **Financial Reporting**: Data foundation for financial reporting and analysis

### User Experience and Workflow

#### **Streamlined Invoice Creation**:
- **PO Selection Interface**: Intuitive Purchase Order selection and validation
- **Auto-fill Efficiency**: Minimize data entry through comprehensive auto-fill
- **Validation Feedback**: Clear, immediate feedback on validation errors
- **Process Guidance**: Step-by-step guidance through invoice creation process

#### **Payment Management Interface**:
- **Payment Term Clarity**: Clear display of payment terms and due dates
- **Amount Validation**: Real-time validation of invoice amounts and calculations
- **Partial Payment Support**: Intuitive interface for partial payment scenarios
- **Financial Summary**: Clear financial summary and invoice total display

This comprehensive purchase invoice system provides complete payment fulfillment management with Purchase Order integration, flexible partial payment support, and proper financial controls while ensuring compliance with business rules and maintaining complete audit trails for financial accuracy.