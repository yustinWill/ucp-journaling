# Purchase Delivery Module - Summary

## Overview
The Purchase Delivery (PD) module manages the receipt and delivery tracking of items ordered through Purchase Orders. It implements a PO-dependent workflow where all deliveries must reference existing Purchase Orders with comprehensive quantity tracking and partial delivery support.

## Business Flow
```
Purchase Order → **Purchase Delivery** → Inventory Update → Purchase Invoice
```

## Key Features

### 1. **PO-Dependent Delivery Management**
- **Mandatory PO Reference**: Cannot create delivery without existing PO
- **Auto-fill Functionality**: Supplier, shipping, and product info from PO
- **Quantity Tracking**: Ordered, delivered, remaining quantities
- **Partial Deliveries**: Support for multiple deliveries per PO
- **Over-delivery Prevention**: Strict validation against remaining quantities

### 2. **Comprehensive Delivery Tracking**
- **Delivery Information**: Number, date, courier, tracking details
- **Status Management**: PENDING → IN_TRANSIT → DELIVERED
- **Multi-location Support**: Warehouse destination management
- **Real-time Updates**: Inventory level updates upon confirmation

### 3. **Enhanced Search and Filtering**
- **Transaction Date Range**: Date picker with range selection
- **Delivery Date Filter**: Single date selection
- **Supplier Filter**: Dropdown with all suppliers
- **Dual Search**: Search by PD number OR PO reference number
- **Tag Support**: Categorization and filtering

## File Structure

### Controllers
- `PurchaseDeliveryController.php` - Main controller following PO patterns
- Routes: `/main_transaction/purchase_delivery/*`

### Views
- `index.blade.php` - Enhanced DataTable with comprehensive filters
- `add.blade.php` - PO-dependent form with pre-filling support
- `edit.blade.php` - Edit form maintaining PO dependency
- `detail.blade.php` - Comprehensive detail view with audit trail
- `import.blade.php` / `export.blade.php` - Data management tools

### Domain Services (DDD)
```
app/Domains/PurchaseDelivery/
├── Actions/
│   ├── CreatePurchaseDeliveryAction.php
│   └── UpdatePurchaseDeliveryAction.php
├── Services/
│   ├── PurchaseDeliveryService.php
│   └── PurchaseDeliveryAuditLogService.php
```

### Key Methods
- `findAllDatatable()` - Enhanced filtering with PO/PD search
- `createDeliveryProducts()` - Handles delivery item creation
- `validatePurchaseDeliveryData()` - Business rule validation
- `getPODetails()` - Loads PO data for form pre-filling

## PO Integration Workflow

### 1. **Creation from PO Detail**
```
PO Detail Page → "Buat Delivery" Button → PD Add Form (Pre-filled)
```
- PO information auto-populated
- Items table loaded with quantities
- User adjusts delivery quantities
- Validation ensures quantities don't exceed remaining

### 2. **Form Pre-filling Process**
1. PO selected → AJAX call to `getPODetails()`
2. PO data loaded: supplier, warehouse, items
3. Items table populated with:
   - Product name and unit
   - Ordered quantity (from PO)
   - Previously delivered quantity
   - Remaining quantity (calculated)
   - Delivery quantity input (user-editable)

### 3. **Quantity Management**
```
Ordered Qty - Delivered Qty = Remaining Qty
Delivery Qty ≤ Remaining Qty (validation)
```

## Business Rules Implementation

### **Partial Delivery Support**
- **Example**: PO has 50 items
  - PD #1: Deliver 20 items (Remaining: 30)
  - PD #2: Deliver 30 items (Remaining: 0, PO complete)
  - OR: PD #1: Deliver 50 items (Complete in one delivery)

### **Validation Rules**
- Delivery quantity cannot exceed remaining quantity
- At least one item must have delivery quantity > 0
- PO must be in valid status (not DRAFT or CANCELLED)
- All required fields must be completed

### **Inventory Integration**
- Real-time stock updates upon delivery confirmation
- Multi-location inventory management
- Cost basis updates from PO pricing
- Stock reconciliation capabilities

## Form Patterns

### Add Form Structure
```
1. Informasi Dasar Transaksi (Date, Transaction Number)
2. Purchase Order Selection (Required, with auto-fill)
3. Informasi Purchase Order (Auto-filled, disabled)
4. Informasi Delivery (User inputs: delivery number, date, courier)
5. Items Delivery (Table with quantity inputs)
6. Informasi Tambahan (Tags, memo, attachments)
```

### JavaScript Functionality
- **PO Selection Handler**: Auto-fills form fields from PO data
- **Dynamic Items Loading**: AJAX call to load PO items and quantities
- **Real-time Validation**: Quantity validation against remaining stock
- **Summary Updates**: Auto-calculate total delivery quantities
- **Pre-selection Support**: Handle PO pre-selection from URL parameters

## Enhanced Filtering System

### DataTable Filters
- **Date Range Picker**: Transaction date filtering
- **Delivery Date**: Single date selection
- **Supplier Dropdown**: All suppliers with names
- **Search Field**: Searches both PD reference AND PO reference numbers

### Search Capabilities
```javascript
// Searches in multiple fields
WHERE (
    pd.reference_no LIKE '%search%' OR
    pd.delivery_number LIKE '%search%' OR  
    po.reference_no LIKE '%search%'
)
```

## Status Management

### Delivery Statuses
- **PENDING**: Created, awaiting shipment
- **IN_TRANSIT**: Shipped, on the way
- **DELIVERED**: Received and confirmed
- **CANCELLED**: Delivery cancelled

### Status-based Actions
- **PENDING/IN_TRANSIT**: Can edit, can cancel
- **DELIVERED**: View only, cannot modify
- **CANCELLED**: View only, audit trail maintained

## Integration Points

### **Purchase Order Integration**
- Reads PO data for auto-fill
- Updates PO delivery status
- Tracks remaining quantities
- Links delivery history

### **Inventory Management**
- Updates stock levels upon delivery
- Multi-warehouse support
- Cost basis updates
- Stock movement tracking

### **Audit System**
- Complete action logging
- Before/after data capture
- User activity tracking
- Metadata storage in JSON

## Technical Implementation

### **PO → PD Creation Flow**
1. User clicks "Buat Delivery" on PO detail page
2. Route: `purchase_delivery.add?po_id={id}`
3. Controller loads PO data and pre-selects in form
4. JavaScript detects pre-selection and auto-loads PO details
5. Form populated with PO information and available quantities
6. User adjusts delivery quantities and submits

### **AJAX Endpoints**
- `getPODetails()` - Load PO information and items
- `generateTransactionNumber()` - Generate PD reference
- `generateDeliveryNumber()` - Generate delivery number

### **Validation Layers**
- Client-side: Real-time quantity validation
- Server-side: Business rule validation in Actions
- Database: Foreign key constraints and transactions

## Permissions
- `main_transaction_purchase_delivery.read` - View access
- `main_transaction_purchase_delivery.write` - Create/Edit access
- `main_transaction_purchase_delivery.delete` - Delete access
- Related: PO read access for integration

## Performance Optimizations
- **Efficient Queries**: Optimized joins for DataTable loading
- **Caching**: Static data caching for dropdowns
- **Lazy Loading**: Items loaded only when PO selected
- **Pagination**: Server-side DataTable processing

## Future Enhancements
- **Photo Documentation**: Delivery photo capture
- **Digital Signatures**: Electronic delivery confirmation
- **Barcode Scanning**: Product verification via scanning
- **Mobile Interface**: Mobile-optimized delivery confirmation
- **Courier API Integration**: Real-time tracking updates
- **Automated Notifications**: Email/SMS delivery updates

---
**Last Updated**: January 2025  
**Module Status**: Production Ready with Complete PO Integration  
**Business Flow**: Fully Operational PO → PD → Inventory Workflow