# Sales Delivery (SD) Creation Form - Technical Specification

## Overview
This document provides comprehensive technical specifications for creating the Sales Delivery (SD) form in the sales management module. The form manages customer deliveries based on existing Sales Orders, with automatic data population from SO, delivery tracking capabilities, and quantity management for partial deliveries in the sales process.

## Form Purpose
The Sales Delivery form is specifically designed for:
- Creating customer deliveries exclusively from existing Sales Orders with complete SO integration and data inheritance
- Managing delivery logistics including tracking numbers, courier information, and actual delivery dates
- Tracking delivery quantities against Sales Order quantities with automatic calculation of remaining items
- Supporting partial deliveries with progress tracking and remaining quantity management for multiple shipments
- Providing delivery documentation with customer information auto-populated from Sales Order data
- Enabling delivery status updates that reflect back to the originating Sales Order for accurate fulfillment tracking
- Supporting courier integration and tracking number management for complete delivery lifecycle management
- Maintaining complete delivery audit trail linked to Sales Orders for customer service and delivery analytics

## UI Structure Requirements

### 1. Form Header
- **Title**: "Tambah Delivery"
- **Back Navigation**: "← Kembali" button to return to previous screen
- **SO Reference**: "Referensi: PO-202508-673" - prominently displayed originating SO number
- **Layout**: Split header with title/back button and SO reference

### 2. Main Layout Structure
- **Split Layout**: 8-column main form area + 4-column requirements information panel
- **Left Column**: Primary form content with SO data and delivery inputs
- **Right Column**: Requirements and field descriptions for user guidance
- **Form ID**: `salesDeliveryForm`

### 3. Section 1: Sales Order Header Information (Auto-filled from SO)
Customer and order details inherited from Sales Order:

#### Customer Information Display:
- **Supplier** (Customer Name) (Display only)
  - Auto-filled from SO customer data
  - Sample: "CV TOBA GRAFIKA"
  - Read-only field showing SO customer
- **Email** (Display only)
  - Customer email from SO
  - Auto-populated, not editable
  - Shows customer contact from SO

#### Order Details Display:
- **Alamat pengiriman** (Shipping Address) (Display only)
  - Customer shipping address from SO
  - Auto-populated from SO customer data
  - Multi-line address display
- **Tgl. pengiriman** (Shipping Date) (Display only)
  - Original shipping date from SO
  - Sample: "26/08/2025"
  - Reference date from SO

#### Transaction References Display:
- **Kirim melalui** (Ship Via) (Display only)
  - Shipping method from SO
  - Auto-populated from SO
- **No Transaksi** (Transaction Number) (Display only)
  - SO transaction number
  - Sample: "Purchase Delivery #2508.01401/DO/STG"
  - Reference to originating SO

#### Additional SO References:
- **No. pelacakan** (Tracking Number) (Display only)
  - SO tracking reference if available
  - Auto-populated from SO data
- **No Referensi** (Reference Number) (Display only)
  - SO reference number
  - Links to SO system reference
- **No pesanan** (Order Number) (Display only)
  - Purchase/Sales Order number
  - Sample: "Purchase Order #PO/SYN/15/0825-2"
  - Direct reference to SO
- **Tag** (Display only)
  - SO classification tags
  - Auto-populated from SO

### 4. Section 2: Delivery Information (User Input Section)
Delivery-specific details for current shipment:

#### Row 1: Basic Delivery Details
- **Nomor Delivery** (Delivery Number) (Required, Text input)
  - Auto-generated delivery number
  - Sample: "DN-003"
  - Format: DN-XXX, editable if needed
  - Unique identifier for delivery
- **Tanggal Delivery** (Delivery Date) (Required, Date input)
  - Actual delivery date
  - Sample: "08/28/2025"
  - Date picker with validation
  - Cannot be future date

#### Row 2: Shipping Details
- **Nomor Resi** (Tracking Number) (Optional, Text input)
  - Courier tracking number
  - Placeholder: "Masukkan nomor resi"
  - For delivery tracking integration
- **Kurir/Ekspedisi** (Courier/Expedition) (Optional, Text input)
  - Courier or shipping company name
  - Placeholder: "Nama kurir/ekspedisi"
  - For delivery method tracking

#### Row 3: Additional Information
- **Tag** (Optional, Text input)
  - Delivery classification tags
  - Placeholder: "Masukkan tag (opsional)"
  - For filtering and categorization
- **Keterangan** (Notes) (Optional, Textarea)
  - Delivery-specific notes
  - Placeholder: "Keterangan delivery (opsional)"
  - 3 rows, for special delivery instructions

### 5. Section 3: Items Delivery (SO Items with Delivery Quantities)
Product delivery tracking and quantity management:

#### Information Box:
- **Delivery Instructions**: "Info: Pilih dan masukkan jumlah item yang akan di-deliver. Sistem akan otomatis menghitung sisa quantity."
- **Process Guidance**: Clear instruction for delivery quantity entry

#### Items Delivery Table:
- **Dynamic Table** populated from SO items
- **Read-only Columns**:
  - **Produk** (Product) (Display only)
    - Product names from SO
    - Sample: "Laptop Dell Inspiron 15", "Mouse Wireless Logitech", "Keyboard Mechanical"
    - Auto-populated from SO items
  - **Qty PO** (SO Quantity) (Display only)
    - Original quantity ordered in SO
    - Sample: 5, 10, 5
    - Shows total ordered quantity
  - **Delivered** (Previously Delivered) (Auto-calculated, Display only)
    - Quantity already delivered in previous shipments
    - Sample: 0, 0, 0 (if first delivery)
    - Auto-calculated from delivery history
  - **Remaining** (Remaining Quantity) (Auto-calculated, Display only)
    - Remaining quantity to be delivered
    - Calculation: Qty PO - Delivered
    - Sample: 5, 10, 5 (matching Qty PO if first delivery)

- **Input Column**:
  - **Qty Delivery** (Delivery Quantity) (Optional, Number input)
    - Quantity being delivered in this shipment
    - Validation: Cannot exceed Remaining quantity
    - Validation: Must be positive number or zero
    - Updates total delivery count

#### Delivery Summary:
- **Total Qty Delivery**: "0 items"
- **Real-time Updates**: Updates automatically as delivery quantities change
- **Validation Summary**: Shows total items being delivered

### 6. Action Buttons
- **Batal** (Cancel) (Secondary button)
  - Color: Gray
  - Function: Cancel delivery creation
  - Returns to previous screen
- **📦 Simpan Delivery** (Save Delivery) (Primary button)
  - Color: Blue (#007bff)
  - Icon: Package/delivery icon
  - Function: Create delivery record
  - Validation: Delivery number, delivery date, at least one item with delivery quantity > 0

## JavaScript Functionality Requirements

### 1. Sales Order Integration
- **SO Data Loading**: Auto-populate all SO header information on form load
- **Customer Data**: Display customer information from SO without editing capability
- **Order References**: Show all SO reference numbers and tracking information
- **Item Population**: Load all SO items into delivery table automatically

### 2. Delivery Quantity Management
- **Remaining Calculation**: Auto-calculate remaining quantities (SO Qty - Previously Delivered)
- **Delivery Input Validation**: Prevent delivery quantities exceeding remaining amounts
- **Real-time Updates**: Update total delivery quantity as individual items change
- **Zero Validation**: Allow zero quantities for items not being delivered in current shipment

### 3. Delivery Information Validation
- **Required Fields**: Validate delivery number and delivery date are provided
- **Date Validation**: Ensure delivery date is not in future
- **Delivery Number**: Validate unique delivery number format
- **Minimum Delivery**: Ensure at least one item has delivery quantity > 0

### 4. Progress Tracking
- **Delivery Status**: Track delivery progress against SO quantities
- **Partial Delivery Support**: Handle multiple partial deliveries for same SO
- **Completion Detection**: Identify when SO is fully delivered
- **History Integration**: Consider previous deliveries in remaining calculations

## Data Context
- **Business Flow**: Sales Order → **Sales Delivery** → Sales Invoice (delivery-based invoicing)
- **Purpose**: Physical delivery execution and tracking for customer orders
- **Integration**: Links to Sales Order module and updates delivery status
- **Delivery Process**: Foundation for delivery confirmation and customer notification

## Development Notes
- **SO Dependency**: Cannot create delivery without valid Sales Order reference
- **Data Inheritance**: All customer and product data inherited from SO
- **Quantity Tracking**: Precise tracking of delivered vs. remaining quantities
- **Multiple Deliveries**: Support for multiple partial deliveries against single SO
- **Status Updates**: Update SO delivery status based on delivered quantities

## Future Considerations
This sales delivery form will enable:
- **Advanced Tracking**: Real-time delivery tracking integration with courier APIs
- **Customer Notifications**: Automatic customer notifications with tracking information
- **Delivery Confirmation**: Digital delivery confirmation and signature capture
- **Route Optimization**: Integration with delivery route planning systems
- **Mobile Delivery**: Mobile app integration for delivery personnel
- **Delivery Analytics**: Comprehensive delivery performance and timing analytics

## Technical Implementation
- **Framework**: Laravel Blade templates with SO integration
- **JavaScript**: jQuery with real-time quantity calculations and validation
- **Styling**: Bootstrap + TailwindCSS with responsive design
- **Database**: Integration with sales_orders, delivery_items, and delivery_tracking tables
- **Validation**: Multi-layer validation including quantity limits and SO references
- **Real-time Data**: Live SO data and delivery history integration
- **Transaction Management**: Proper database transactions for delivery creation and SO updates

## Special Features & Business Logic

### Integration Notes (Based on Requirements and Right Panel Information)
**Sales Delivery Context**:

**Business Requirements from Right Panel**:

#### **Requirement SALES Delivery (SD)**:
**1. Relasi dengan SALES Order (SO)**:
- **SALES Delivery (SD) hanya bisa dibuat jika ada SALES Order (SO)**: SD can only be created if SO exists
- **Saat user buat SD, user harus Silih referensi SO terlebih dahulu**: When creating SD, user must select SO reference first
- **Semua data dasar (Customer, alamat pengiriman, tanggal SO, item SO, qty SO, dll.) autofill dari SO**: All basic data auto-fills from SO

#### **A. Header Informasi (Autofill dari SO)**:
- **Referensi SO**: Nomor SALES Order yang dijadikan acuan pembuatan SALES Delivery
- **Customer**: Nama Customer dari SO
- **Alamat Pengiriman**: Alamat pengiriman dari SO
- **Tgl. Pengiriman**: Tanggal pengiriman sesuai SO, bisa diedit sesuai kebutuhan
- **Email**: Alamat email Customer (jika tersedia)
- **No Transaksi SD**: Nomor unik transaksi SA x
- **No Pesanan (SO Ref)**: Nomor SALES Order yang menjadi referensi
- **No Pelacakan**: Nomor tracking dari ekspedisi
- **Tag**: Label/tag tambahan untuk klasifikasi
- **Keterangan**: Catatan tambahan terkait delivery

#### **B. Informasi Delivery**:
- **Nomor Delivery**: Nomor delivery (contoh: DN-003), auto-generate namun bisa diedit
- **Tanggal Delivery**: Tanggal aktual delivery yang diinput oleh user
- **Nomor Resi**: Nomor resi pengiriman
- **Kurir/Ekspedisi**: Nama kurir atau ekspedisi yang mengirim barang
- **Tag**: Label/tag opsional untuk memudahkan pencarian/filter
- **Keterangan**: Catatan khusus terkait delivery

#### **C. Items Delivery**:
- **Produk**: Nama barang yang muncul dari SO
- **Qty SO**: Jumlah yang dipesan pada SO
- **Delivered Qty**: Jumlah barang yang sudah dikirim pada SD sebelumnya
- **Remaining Qty**: Sisa jumlah barang yang belum dikirim (Qty SO – Delivered Qty)
- **Qty Delivery**: Jumlah barang yang akan dikirim pada SD ini, wajib diinput oleh user

### Enhanced Sales Delivery Management Features

#### **Sales Order Integration System**:
- **Mandatory SO Reference**: Delivery cannot be created without valid Sales Order
- **Complete Data Inheritance**: All customer, shipping, and product data from SO
- **Reference Chain**: Maintain clear link between SD and originating SO
- **Data Consistency**: Ensure delivery data matches SO specifications

#### **Advanced Quantity Tracking**:
- **Multi-Delivery Support**: Track quantities across multiple partial deliveries
- **Remaining Calculation**: Automatic calculation of outstanding delivery quantities
- **Delivery History**: Consider all previous deliveries for accurate remaining counts
- **Progress Monitoring**: Real-time delivery progress against SO requirements

#### **Delivery Documentation and Tracking**:
- **Unique Delivery Numbers**: Auto-generated delivery identifiers with edit capability
- **Courier Integration**: Support for multiple courier and tracking systems
- **Delivery Confirmation**: Actual delivery date and tracking information
- **Classification System**: Tag-based organization for delivery management

### Business Rules and Validation

#### **Sales Order Dependency Rules**:
- **SO Existence**: Valid Sales Order must exist before delivery creation
- **SO Selection**: User must select SO reference as first step
- **Data Lock**: SO-derived data is read-only to maintain referential integrity
- **SO Status**: Only active/confirmed Sales Orders can generate deliveries

#### **Quantity Management Controls**:
- **Delivery Limits**: Delivery quantities cannot exceed remaining SO quantities
- **Positive Quantities**: All delivery quantities must be zero or positive
- **Minimum Delivery**: At least one item must have delivery quantity > 0
- **Total Validation**: Ensure delivery totals are mathematically accurate

#### **Delivery Information Requirements**:
- **Required Fields**: Delivery number and delivery date are mandatory
- **Date Validation**: Delivery date cannot be in the future
- **Tracking Integration**: Optional but recommended tracking and courier information
- **Documentation**: Proper delivery notes and classification tags

### Workflow Integration

#### **Sales Document Flow Integration**:
- **SO Foundation**: Sales Delivery built upon existing Sales Order foundation
- **Invoice Preparation**: Delivery data feeds into Sales Invoice creation
- **Status Updates**: Delivery creation updates SO delivery status automatically
- **Multi-Delivery**: Support multiple partial deliveries against single SO

#### **Customer Communication and Service**:
- **Delivery Notifications**: Foundation for customer delivery notifications
- **Tracking Information**: Customer access to delivery tracking details
- **Service History**: Complete delivery history linked to customer records
- **Issue Resolution**: Delivery tracking for customer service resolution

#### **Operational Management**:
- **Delivery Scheduling**: Integration with delivery planning and routing
- **Performance Tracking**: Delivery timing and performance analytics
- **Inventory Updates**: Delivery confirmation updates inventory levels
- **Compliance Documentation**: Complete delivery audit trail for compliance

This comprehensive sales delivery system provides complete delivery management with strict SO integration, accurate quantity tracking, and proper delivery documentation while maintaining referential integrity with Sales Orders and supporting flexible partial delivery scenarios.