# Purchase Order Module - Summary

## Overview
The Purchase Order (PO) module manages procurement requests and order placement for RIYO Water company. It follows the established Laravel DDD architecture with proper service layer patterns and audit logging.

## Business Flow
```
PO Creation → PO Approval → Purchase Delivery → Purchase Invoice → Payment
```

## Key Features

### 1. **Purchase Order Management**
- Create, Read, Update purchase orders
- Multi-supplier support with contact integration
- Payment terms configuration
- Product line management with tax calculations
- File attachment support
- Comprehensive audit logging

### 2. **Integration Points**
- **Suppliers**: Links to Contact module for supplier information
- **Products**: Integration with Product catalog for item selection
- **Warehouses**: Delivery destination management
- **Purchase Delivery**: Creation of delivery orders from PO
- **Purchase Invoice**: Invoice generation based on delivered items
- **Tags**: Categorization and filtering support

### 3. **Delivery Management Integration**
- **Create Delivery Button**: Available on PO detail page
- **Multiple Partial Deliveries**: Support for splitting PO into multiple deliveries
- **Quantity Tracking**: Monitors delivered vs. ordered quantities
- **Remaining Items**: Calculates available items for future deliveries

## File Structure

### Controllers
- `PurchaseOrderController.php` - Main controller with CRUD operations
- Routes: `/main_transaction/purchase_order/*`

### Views
- `index.blade.php` - DataTable listing with advanced filters
- `add.blade.php` - Multi-section form with product lines
- `edit.blade.php` - Edit form maintaining form patterns
- `detail.blade.php` - Comprehensive detail view with tabs and actions
- `import.blade.php` / `export.blade.php` - Data management tools

### Domain Services (DDD)
```
app/Domains/PurchaseOrder/
├── Actions/
│   ├── CreatePurchaseOrderAction.php
│   └── UpdatePurchaseOrderAction.php
├── Services/
│   ├── PurchaseOrderService.php
│   ├── PurchaseOrderProductService.php
│   └── PurchaseOrderAuditLogService.php
```

### Key Methods
- `findAvailableForDelivery()` - Returns POs eligible for delivery creation
- `findAllDatatable()` - Server-side processing for DataTable
- `createWithProducts()` - Transaction-wrapped creation with product lines

## Form Patterns

### Add/Edit Forms
- **Multi-section layout**: Informasi Dasar, Supplier, Payment, Products, Additional
- **Product lines table**: Dynamic row management with calculations
- **Auto-calculations**: Subtotal, tax, discount, grand total
- **FormData submission**: Support for file uploads
- **Real-time validation**: Client and server-side validation

### Detail Page
- **Tab structure**: Informasi → Transaksi → Audit Log
- **Smart actions**: Context-sensitive button display
- **Create Delivery**: Direct link to PD creation with pre-filled data
- **Delivery tracking**: Shows related deliveries and quantities

## Purchase Delivery Integration

### Workflow Implementation
1. **PO Creation**: Complete purchase order with product quantities
2. **PO Detail**: Review order with "Buat Delivery" button
3. **Create Delivery**: Opens PD form with pre-filled PO data
4. **Partial Delivery**: User selects quantities to deliver (≤ remaining)
5. **Multiple Deliveries**: Process can repeat until all items delivered

### Business Rules
- PO quantities are editable until fully delivered
- Delivery quantities cannot exceed remaining PO quantities
- Each delivery reduces available quantity for future deliveries
- PO status tracks delivery completion

## Permissions
- `main_transaction_purchase_order.read` - View access
- `main_transaction_purchase_order.write` - Create/Edit access
- `main_transaction_purchase_order.delete` - Delete access
- `main_transaction_purchase_delivery.write` - Create delivery access

## Technical Standards
- **DDD Architecture**: Proper separation of concerns
- **Transaction Safety**: Database transactions for data integrity
- **Audit Logging**: Complete action tracking with metadata
- **Permission-based UI**: Dynamic button/field visibility
- **Responsive Design**: Mobile-friendly interface
- **DataTable Integration**: Server-side processing with filters

## Status Management
- **DRAFT**: Initial creation state
- **PENDING_APPROVAL**: Awaiting approval
- **APPROVED**: Ready for delivery
- **PARTIALLY_DELIVERED**: Some items delivered
- **FULLY_DELIVERED**: All items delivered
- **CANCELLED**: Order cancelled

## Next Steps
- Integration with Purchase Invoice module
- Advanced approval workflow
- Supplier performance tracking
- Automated reorder points
- Mobile delivery confirmation

---
**Last Updated**: January 2025  
**Module Status**: Production Ready with Delivery Integration