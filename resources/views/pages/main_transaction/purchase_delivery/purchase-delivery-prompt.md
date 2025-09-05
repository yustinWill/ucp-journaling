# Purchase Delivery (PD) Creation Form - Technical Specification

## Overview
This document provides comprehensive technical specifications for creating the Purchase Delivery (PD) form in the procurement management module. The form manages delivery receipts for Purchase Orders with comprehensive tracking, partial delivery support, and automatic data population from referenced Purchase Orders.

## Form Purpose
The Purchase Delivery form is specifically designed for:
- Creating delivery receipts exclusively from existing Purchase Orders with mandatory PO reference selection
- Managing partial and complete deliveries with flexible quantity input and remaining quantity tracking
- Automatically populating supplier information, shipping details, and product lists from selected Purchase Orders
- Providing comprehensive delivery tracking with courier information, tracking numbers, and delivery dates
- Supporting multiple partial deliveries against single Purchase Orders with accurate quantity management
- Integrating with inventory management to update stock levels upon delivery confirmation
- Maintaining complete audit trail of delivery activities linked to original Purchase Orders
- Enabling delivery status tracking and Purchase Order completion monitoring through delivery records

## UI Structure Requirements

### 1. Form Header
- **Title**: "Tambah Delivery"
- **Reference Display**: "Reference: [PO-Number]" (displays selected PO reference)
- **Card Layout**: Standard card with header and body sections
- **Form ID**: `purchaseDeliveryForm`

### 2. Section 1: Purchase Order Information (Auto-filled Display)
Read-only information populated from selected Purchase Order:

#### Left Column - Supplier Information:
- **Supplier** (Display only)
  - Company name from PO
  - Sample: "PT SYNTOBA GRAFIKA"
  - Auto-populated, non-editable
- **Alamat pengiriman** (Shipping Address) (Display only)
  - Shipping address from PO
  - Sample: "Jl. Raya No. 123, Jakarta 12345"
  - Multi-line address display
- **Kirim melalui** (Shipping Method) (Display only)
  - Shipping method from PO
  - Sample: "Express/Courier"
  - Transport method information
- **No. pelacakan** (Tracking Number) (Display only)
  - PO tracking reference if available
  - Sample: "-" (empty if not set)
  - May be populated from PO data

#### Right Column - Order Information:
- **Email** (Display only)
  - Supplier email from PO
  - Sample: "supplier@syntoba.com"
  - Contact information display
- **Tel. pengiriman** (Shipping Phone) (Display only)
  - Shipping contact phone from PO
  - Sample: "Tel. 08123456789"
  - Phone number formatting
- **No Transaksi** (Transaction Number) (Display only)
  - Auto-generated PD transaction number
  - Sample: "Purchase Delivery #PD2508.01401/DO/STG"
  - System-generated unique identifier
- **No. Referensi** (Reference Number) (Display only)
  - Additional reference from PO if available
  - Sample: "-" (empty if not set)
- **No. pesanan** (Order Number) (Display only)
  - Source Purchase Order number
  - Sample: "Purchase Order #PO/SYN/15/0825-2"
  - Clickable link to PO details
- **Tag** (Display only)
  - Tag from PO if available
  - Sample: "-" (empty if not set)

### 3. Section 2: Delivery Information (Informasi Delivery)
User-input fields for delivery-specific information:

#### Row 1: Basic Delivery Details
- **Nomor Delivery** (Delivery Number) (Required, Text input)
  - Auto-generated delivery identifier
  - Sample: "DN-003"
  - Format: DN-XXX, editable if needed
- **Tanggal Delivery** (Delivery Date) (Required, Date input)
  - Actual delivery date
  - Sample: "08/28/2025"
  - Format: MM/DD/YYYY
  - Cannot be future date beyond reasonable shipping time

#### Row 2: Shipping and Tracking
- **Nomor Resi** (Tracking Number) (Optional, Text input)
  - Courier tracking number
  - Placeholder: "Masukkan nomor resi"
  - For package tracking purposes
- **Kurir/Ekspedisi** (Courier/Shipping Company) (Optional, Text input)
  - Name of shipping company or courier
  - Placeholder: "Nama kurir/ekspedisi"
  - Free text for courier identification

#### Row 3: Additional Information
- **Tag** (Optional, Text input)
  - Delivery classification tag
  - Placeholder: "Masukkan tag (optional)"
  - For categorization and filtering
- **Keterangan** (Notes) (Optional, Textarea)
  - Additional delivery notes
  - Placeholder: "Keterangan delivery (optional)"
  - 3 rows, 500 characters max
  - Delivery-specific comments

### 4. Section 3: Items Delivery
Product delivery quantity management:

#### Information Box:
- **Instructional Text**: "Info: Pilih dan masukkan jumlah item yang akan di-deliver. Sistem akan otomatis menghitung sisa quantity."
- **User Guidance**: Clear explanation of quantity input and automatic calculations

#### Delivery Items Table:
- **Auto-populated Table** from PO items
- **Columns**:
  - **Produk** (Product) (Display only, Text)
    - Product name from PO
    - Sample: "Laptop Dell Inspiron 15", "Mouse Wireless Logitech"
    - Multi-line product names supported
  - **Qty PO** (PO Quantity) (Display only, Number)
    - Original quantity from Purchase Order
    - Sample: "5", "10"
    - Reference quantity for delivery validation
  - **Delivered** (Previously Delivered) (Display only, Number)
    - Previously delivered quantities from other deliveries
    - Sample: "0" (for first delivery)
    - Cumulative delivery tracking
  - **Remaining** (Remaining Quantity) (Display only, Number)
    - Calculated: Qty PO - Delivered
    - Sample: "5", "10"
    - Available quantity for delivery
  - **Qty Delivery** (Delivery Quantity) (Required, Number input)
    - User-input delivery quantity
    - Sample: "[5]", "[10]" (input fields)
    - Validation: Cannot exceed Remaining quantity

#### Delivery Summary:
- **Total Qty Delivery**: Auto-calculated sum of all delivery quantities
- **Real-time Update**: Updates as user enters quantities
- **Format**: "Total Qty Delivery: X items"

### 5. Action Buttons
- **Batal** (Cancel) (Secondary button)
  - Color: Gray
  - Function: Cancel delivery creation and return to delivery list
  - No confirmation required
- **Simpan Delivery** (Save Delivery) (Primary button)
  - Color: Blue (#007bff)
  - Function: Create delivery record and update inventory
  - Validation: Require at least one item with quantity > 0
  - Confirmation: Display delivery summary before saving

## JavaScript Functionality Requirements

### 1. Purchase Order Integration
- **PO Selection**: Dropdown or modal for selecting source Purchase Order
- **Auto-fill Population**: Populate all PO-related fields when PO selected
- **Data Validation**: Verify PO exists and is available for delivery
- **Reference Linking**: Maintain clear linkage between delivery and source PO

### 2. Quantity Management
- **Remaining Calculation**: Auto-calculate remaining quantities (Qty PO - Delivered)
- **Input Validation**: Prevent delivery quantities exceeding remaining amounts
- **Real-time Updates**: Update totals as user enters quantities
- **Partial Delivery Support**: Allow zero quantities for items not being delivered

### 3. Delivery Validation
- **Quantity Validation**: Ensure delivery quantities are within valid ranges
- **Date Validation**: Validate delivery date reasonableness
- **Required Fields**: Enforce required field completion
- **Business Rules**: Apply delivery-specific business validation rules

### 4. Form Management
- **Auto-generation**: Generate delivery numbers and transaction IDs
- **Data Preservation**: Maintain form data during user session
- **Error Handling**: Clear error messages for validation failures
- **Success Feedback**: Confirmation of successful delivery creation

## Data Context
- **Business Flow**: Purchase Order Creation → **Purchase Delivery** → Inventory Update → Invoice Processing
- **Purpose**: Track and manage receipt of ordered goods with comprehensive delivery documentation
- **Integration**: Links to Purchase Order, Inventory, and Supplier management modules
- **Dependency**: Cannot exist without valid Purchase Order reference

## Development Notes
- **PO Dependency**: Form cannot be accessed without valid Purchase Order selection
- **Partial Deliveries**: Support multiple deliveries against single Purchase Order
- **Quantity Control**: Strict validation to prevent over-delivery
- **Auto-fill Integrity**: Maintain data consistency between PO and delivery records
- **Inventory Integration**: Real-time inventory updates upon delivery confirmation

## Future Considerations
This purchase delivery form will enable:
- **Advanced Tracking**: Integration with courier APIs for real-time tracking
- **Photo Documentation**: Delivery photo capture for verification
- **Digital Signatures**: Electronic signature capture for delivery confirmation
- **Barcode Scanning**: Barcode/QR code scanning for product verification
- **Mobile Delivery**: Mobile-optimized delivery confirmation interface
- **Automated Notifications**: Email/SMS notifications for delivery status updates

## Technical Implementation
- **Framework**: Laravel Blade templates with PO integration
- **JavaScript**: jQuery with real-time calculations and validation
- **Styling**: Bootstrap + TailwindCSS with responsive table design
- **Database**: Integration with purchase orders, inventory, and supplier tables
- **Validation**: Multi-layer validation including business rules and quantity limits
- **Transaction Management**: Atomic transactions for delivery and inventory updates
- **API Integration**: Foundation for courier tracking API integration

## Special Features & Business Logic

### Integration Notes (Based on Requirements and Right Panel Information)
**Purchase Delivery Context**:
- **PO Dependency**: "Purchase Delivery (PD) hanya bisa dibuat jika ada Purchase Order (PO). Saat user buat PD, user harus pilih referensi PO terlebih dahulu. Semua data dasar (supplier, alamat pengiriman, tanggal PO, item PO, qty PO, dll.) autofill dari PO."

**Field Specifications from Right Panel**:

#### **A. Header Informasi (Autofill dari PO)**:
- **Referensi PO**: "Nomor Purchase Order yang dijadikan acuan pembuatan Purchase Delivery"
- **Supplier**: "Nama supplier dari PO"
- **Alamat Pengiriman**: "Alamat pengiriman dari PO"
- **Tgl. Pengiriman**: "Tanggal pengiriman sesuai PO, bisa diedit sesuai kebutuhan"
- **Email**: "Alamat email supplier (jika tersedia)"
- **No Transaksi PD**: "Nomor unik transaksi Purchase Delivery x"
- **No Pesanan (PO Ref)**: "Nomor Purchase Order yang menjadi referensi"
- **No Pelacakan**: "Nomor tracking dari ekspedisi"
- **Tag**: "Label/tag tambahan untuk klasifikasi"
- **Keterangan**: "Catatan tambahan terkait delivery"

#### **B. Informasi Delivery**:
- **Nomor Delivery**: "Nomor delivery (contoh: DN-003), auto-generate namun bisa diedit"
- **Tanggal Delivery**: "Tanggal aktual delivery yang diinput oleh user"
- **Nomor Resi**: "Nomor resi pengiriman"
- **Kurir/Ekspedisi**: "Nama kurir atau ekspedisi yang mengirim barang"
- **Tag**: "Label/tag opsional untuk memudahkan pencarian/filter"
- **Keterangan**: "Catatan khusus terkait delivery"

#### **C. Items Delivery**:
- **Produk**: "Nama barang yang muncul dari PO"
- **Qty PO**: "Jumlah yang dipesan pada PO"
- **Delivered Qty**: "Jumlah barang yang sudah dikirim pada PD sebelumnya"
- **Remaining Qty**: "Sisa jumlah barang yang belum dikirim (Qty PO - Delivered Qty)"
- **Qty Delivery**: "Jumlah barang yang akan dikirim pada PD ini, wajib diinput oleh user"

### Enhanced Purchase Delivery Management Features

#### **Purchase Order Integration**:
- **Mandatory PO Reference**: Cannot create delivery without selecting existing Purchase Order
- **Complete Data Auto-fill**: All supplier, shipping, and product information populated from PO
- **PO Status Validation**: Ensure PO is in appropriate status for delivery creation
- **Multi-delivery Support**: Support multiple partial deliveries against single Purchase Order

#### **Flexible Delivery Quantity Management**:
- **Partial Delivery Support**: Allow delivery of partial quantities with remaining quantity tracking
- **Over-delivery Prevention**: Strict validation to prevent delivery quantities exceeding PO amounts
- **Multi-shipment Tracking**: Handle multiple deliveries for same PO with cumulative quantity tracking
- **Zero Quantity Handling**: Allow zero quantities for items not included in current delivery

#### **Comprehensive Tracking System**:
- **Courier Integration**: Support for various shipping companies and tracking methods
- **Tracking Number Management**: Complete tracking information for delivery monitoring
- **Delivery Status Tracking**: Monitor delivery progress and completion status
- **Historical Delivery Records**: Maintain complete delivery history per Purchase Order

### Business Rules and Validation

#### **Purchase Order Validation**:
- **PO Existence**: Validate selected PO exists and is accessible
- **PO Status**: Ensure PO is in appropriate status for delivery (approved, not cancelled)
- **Supplier Consistency**: Maintain supplier consistency between PO and delivery records
- **Date Validation**: Ensure delivery date is reasonable relative to PO date

#### **Quantity Management Rules**:
- **Remaining Quantity Calculation**: Automatic calculation of available delivery quantities
- **Over-delivery Prevention**: Prevent delivery quantities exceeding remaining amounts
- **Partial Delivery Tracking**: Accurate tracking of partial deliveries and remaining quantities
- **Cumulative Delivery Validation**: Ensure total delivered quantities don't exceed PO quantities

#### **Data Integrity Controls**:
- **Auto-fill Integrity**: Maintain consistency between PO data and delivery records
- **Immutable PO Data**: PO-sourced data cannot be modified during delivery creation
- **Transaction Linking**: Maintain proper linkage between delivery and source Purchase Order
- **Audit Trail**: Complete audit trail of delivery activities and quantity movements

### Advanced Delivery Features

#### **Inventory Integration**:
- **Real-time Stock Updates**: Update inventory levels upon delivery confirmation
- **Multi-location Support**: Handle deliveries to different warehouse locations
- **Cost Basis Updates**: Update inventory cost basis from Purchase Order costs
- **Stock Reconciliation**: Ensure accurate stock levels after delivery processing

#### **Document Management**:
- **Delivery Documentation**: Complete delivery records with all relevant information
- **PO Reference Maintenance**: Clear linkage and reference to source Purchase Order
- **Supplier Communication**: Foundation for supplier delivery communication
- **Compliance Documentation**: Maintain records for regulatory compliance requirements

#### **Workflow Integration**:
- **Purchase Order Completion**: Track PO fulfillment through delivery records
- **Invoice Preparation**: Foundation for purchase invoice creation and matching
- **Supplier Performance**: Track supplier delivery performance and reliability
- **Procurement Analytics**: Data foundation for procurement performance analysis

### Quality and Compliance

#### **Delivery Verification**:
- **Quantity Verification**: Confirm delivered quantities against Purchase Order requirements
- **Quality Control Integration**: Foundation for delivery quality inspection processes
- **Damage Reporting**: Framework for reporting delivery damages or discrepancies
- **Acceptance Documentation**: Complete delivery acceptance documentation

#### **Supplier Management Integration**:
- **Supplier Performance Tracking**: Monitor supplier delivery performance and timeliness
- **Communication History**: Maintain complete supplier communication records
- **Relationship Management**: Support supplier relationship management activities
- **Performance Analytics**: Data foundation for supplier performance evaluation

### System Integration Capabilities

#### **ERP Integration**:
- **Purchase Order System**: Complete integration with Purchase Order management
- **Inventory Management**: Real-time integration with inventory control systems
- **Financial Systems**: Integration with accounts payable and cost accounting
- **Supplier Management**: Integration with supplier relationship management systems

#### **External System Integration**:
- **Courier APIs**: Foundation for integration with shipping company tracking systems
- **EDI Integration**: Support for Electronic Data Interchange with suppliers
- **Mobile Applications**: Framework for mobile delivery confirmation applications
- **Notification Systems**: Email and SMS notification capabilities for delivery events

This comprehensive purchase delivery system provides complete delivery management with Purchase Order integration, flexible quantity handling, and proper inventory updates while maintaining strict validation and audit trails for procurement compliance.