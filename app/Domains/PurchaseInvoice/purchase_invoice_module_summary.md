# Purchase Invoice (PI) Module Summary

**Generated**: September 7, 2025  
**Module**: Purchase Invoice (PI) - Main Transaction Module  
**Status**: Fully Implemented with DDD Architecture

## Module Overview

The Purchase Invoice module manages the invoicing process for Purchase Orders, tracking payment information and invoice lifecycle from creation to payment completion. It follows Domain Driven Design (DDD) patterns and integrates with Purchase Orders for comprehensive procurement workflow management.

## Core Features

### 1. **Invoice Management**
- Create invoices from approved Purchase Orders
- Edit invoice details (dates, descriptions, attachments)
- Track payment status and due dates
- Support for partial and full invoicing

### 2. **Payment Tracking**
- Pre-filled payment information with historical comparison
- Track amounts paid vs due amounts
- Payment status management (DRAFT, UNPAID, PAID, PARTIAL_PAID, OVERDUE, CANCELLED)
- Due date calculation based on payment terms

### 3. **Integration Features**
- Seamless integration with Purchase Order module
- Automatic prefilling of PO information (supplier, products, amounts)
- Payment history comparison across multiple invoices for same PO
- Real-time inventory value tracking

### 4. **Additional Information Management**
- Tags for categorization and filtering
- Internal memo system for team communication
- File attachments (Excel, Word, PDF, Images, ZIP)
- Comprehensive audit logging

## Technical Architecture

### **Domain Driven Design (DDD) Structure**

```
app/Domains/PurchaseInvoice/
├── Actions/
│   ├── CreatePurchaseInvoiceAction.php    # Invoice creation business logic
│   └── UpdatePurchaseInvoiceAction.php    # Invoice update business logic
├── Services/
│   ├── PurchaseInvoiceService.php         # Core business operations
│   └── PurchaseInvoiceAuditLogService.php # Audit trail management
```

### **Database Schema**

#### Primary Tables:
- **`t_purchase_invoices`** - Main invoice header data
- **`t_purchase_invoice_products`** - Invoice line items with payment tracking
- **`m_purchase_invoice_audit_logs`** - Complete audit trail

#### Key Relationships:
- **Purchase Order** (1:N) - One PO can have multiple invoices
- **Products** (N:M) - Through invoice products table
- **Suppliers** - Inherited from Purchase Order
- **Payment Terms** - Inherited from Purchase Order

### **Service Layer Architecture**

#### PurchaseInvoiceService Methods:
- `create()` - Invoice creation with validation
- `update()` - Invoice modification handling
- `findOne()` - Single invoice retrieval with joins
- `findAllDatatable()` - DataTable integration with filtering
- `findAllHeaderBasedOnPoId()` - PO-related invoices
- `findAvailableForInvoicing()` - Available POs for invoicing
- `createInvoiceProducts()` - Line item management
- `findAllProducts()` - Invoice product details

### **Action Layer Pattern**

#### CreatePurchaseInvoiceAction:
```php
- Validation of all input data
- File upload handling for attachments
- Database transaction management
- Audit log creation with metadata
- Email notifications (future implementation)
```

#### UpdatePurchaseInvoiceAction:
```php
- Change detection and validation
- Before/after data comparison
- Audit trail with detailed metadata
- File replacement handling
```

## User Interface Implementation

### **Page Structure**

#### 1. **Index Page** (`resources/views/pages/main_transaction/purchase_invoice/index.blade.php`)
- DataTable with server-side processing
- Advanced filtering (date ranges, supplier, status, invoice search)
- Action buttons (View, conditional Edit based on status)
- Responsive design with mobile optimization

#### 2. **Add Page** (`resources/views/pages/main_transaction/purchase_invoice/add.blade.php`)
- **Standard Form Sections**:
  - Informasi Dasar Transaksi (Basic Transaction Info)
  - Informasi Purchase Order (PO Information)
  - Informasi Invoice (Invoice Details)
  - Items Invoice (Payment Tracking Table)
  - Informasi Tambahan & Ringkasan (Additional Info & Summary)

- **Key Features**:
  - PO selection with auto-fill functionality
  - Pre-filled payment data with historical comparison
  - Due date calculation based on payment terms
  - Payment tracking table similar to PD delivery tracking
  - Tags, Internal Memo, and Attachment support

#### 3. **Edit Page** (`resources/views/pages/main_transaction/purchase_invoice/edit.blade.php`)
- Limited editing capability (dates, descriptions, notes, attachments only)
- Product quantities and prices are read-only after creation
- Clear indicators for non-editable fields
- Consistent with business rules

#### 4. **Detail Page** (`resources/views/pages/main_transaction/purchase_invoice/detail.blade.php`)
- **Tab Structure**:
  - Informasi Invoice (Invoice Information)
  - Data Invoice (Invoice Data with product breakdown)
  - Audit Log (Complete change history)
- Smart action buttons based on invoice status
- Comprehensive data display with proper formatting

### **Form Standards Compliance**

#### Field Layout:
- **Maximum Width**: All inputs use `col-md-6` (never exceed)
- **Select2 Integration**: `global-select2 form-select` with `data-allow-clear="false"`
- **Required Field Indicators**: Red asterisk (`*`) for mandatory fields
- **Field State Rules**:
  - **Disabled**: Auto-calculated fields (no `name` attribute)
  - **Readonly**: System values that need submission (with `name` attribute)
  - **Editable**: User-controllable fields with proper validation

#### Section Structure:
```html
<div class="section-header mt-12">
    <h6>Section Title</h6>
    <hr class="mx-n6">  <!-- Only when followed by form fields -->
</div>
```

### **JavaScript Implementation**

#### Key Functions:
- **Form Validation**: Client-side validation with SweetAlert2 integration
- **PO Information Auto-fill**: Automatic population of supplier and payment data
- **Due Date Calculation**: Real-time calculation based on invoice date and payment terms
- **Payment Calculations**: Dynamic totals and remaining amount calculations
- **File Upload Handling**: Validation and progress indication

## Business Logic

### **Payment Tracking Workflow**

1. **Invoice Creation**:
   - Select approved Purchase Order
   - System pre-fills with previous payment history
   - User specifies invoice quantities and amounts
   - System calculates remaining amounts

2. **Payment Status Management**:
   ```
   DRAFT → UNPAID → PARTIAL_PAID → PAID
                 ↓
               OVERDUE (time-based)
                 ↓
             CANCELLED (manual)
   ```

3. **Due Date Calculation**:
   - Based on invoice date + payment term days
   - Automatic updates when invoice date changes
   - Clear display of payment timeline

### **Integration Points**

#### With Purchase Order:
- Automatic data inheritance (supplier, products, terms)
- Payment status updates back to PO
- Delivery vs Invoice quantity tracking
- Real-time remaining balance calculations

#### With Inventory:
- Cost tracking integration
- Average price calculations
- Stock valuation updates

## Data Validation & Security

### **Input Validation**
```php
'invoice_date' => 'required|date',
'transaction_date' => 'required|date|after_or_equal:invoice_date',
'due_date' => 'required|date|after_or_equal:transaction_date',
'purchase_order_id' => 'required|exists:t_purchase_orders,id',
'products' => 'required|array',
'attachment' => 'nullable|file|mimes:xlsx,xls,doc,docx,pdf,png,jpg,jpeg,zip|max:10240'
```

### **Business Rules**
- Invoice date cannot be in the future
- Transaction date cannot be before invoice date
- Due date calculated automatically from payment terms
- Product quantities cannot be modified after invoice creation
- Only specific fields editable after creation

### **Security Features**
- Permission-based access control
- Company code isolation
- User session validation
- File upload security (type and size restrictions)
- SQL injection prevention via parameter binding

## Audit & Compliance

### **Audit Log Implementation**
- **Complete Change Tracking**: All create/update operations logged
- **Metadata Storage**: Before/after data in JSON format
- **User Attribution**: Who made changes and when
- **Action Types**: CREATE, UPDATE, DELETE with descriptions
- **Retention**: Permanent audit trail for compliance

### **Audit Log Data Structure**
```php
[
    'purchase_invoice_id' => $invoiceId,
    'user_id' => Auth::id(),
    'action_type' => ActionTypes::UPDATE,
    'description' => 'Invoice updated with new due date',
    'metadata' => [
        'before' => [...],
        'after' => [...],
        'changed_fields' => [...]
    ]
]
```

## Performance Optimizations

### **Database Query Optimization**
- Optimized joins for related data retrieval
- Indexed foreign keys for faster lookups
- Efficient DataTable queries with server-side processing
- Minimal N+1 query patterns

### **Frontend Optimization**
- Asset bundling with Laravel Vite
- Lazy loading for large datasets
- Client-side form validation to reduce server load
- Optimized Select2 implementations

## Integration Status

### **Current Integrations**
- ✅ Purchase Order module (complete)
- ✅ Supplier/Contact management
- ✅ Product catalog integration
- ✅ Payment terms system
- ✅ Tag management system
- ✅ Warehouse tracking
- ✅ User permission system

### **Future Integration Points**
- 🔄 Payment processing system
- 🔄 Email notification system
- 🔄 Report generation module
- 🔄 Dashboard analytics
- 🔄 API endpoints for mobile apps

## File Structure Summary

### **Controllers**
```
app/Http/Controllers/MainTransaction/PurchaseInvoiceController.php
├── index() - List view with filtering
├── add() - Invoice creation form
├── store() - Process new invoice
├── detail($id) - Invoice details with tabs
├── edit($id) - Edit form (limited fields)
├── update() - Process invoice updates
├── index_data_table() - DataTable AJAX
└── auditLogDataTable($id) - Audit log AJAX
```

### **Views**
```
resources/views/pages/main_transaction/purchase_invoice/
├── index.blade.php - Main listing page
├── add.blade.php - Invoice creation form
├── edit.blade.php - Invoice editing form (limited)
└── detail.blade.php - Invoice details with tabs
```

### **Routes**
```php
// Standard CRUD routes
Route::get('purchase_invoice', 'index')->name('index');
Route::get('purchase_invoice/add', 'add')->name('add');
Route::post('purchase_invoice/store', 'store')->name('store');
Route::get('purchase_invoice/detail/{id}', 'detail')->name('detail');
Route::get('purchase_invoice/edit/{id}', 'edit')->name('edit');
Route::post('purchase_invoice/update', 'update')->name('update');

// AJAX endpoints
Route::post('purchase_invoice/index_data_table', 'index_data_table')->name('index_data_table');
Route::post('purchase_invoice/audit_log_data_table/{id}', 'auditLogDataTable')->name('audit_log_data_table');
```

## Testing & Quality Assurance

### **Manual Testing Completed**
- ✅ Invoice creation from Purchase Orders
- ✅ Payment tracking calculations
- ✅ Due date calculations
- ✅ File upload functionality
- ✅ Edit restrictions and validations
- ✅ Audit log generation
- ✅ Integration with Purchase Order workflow

### **Code Quality**
- ✅ Laravel Pint formatting compliance
- ✅ DDD architecture standards
- ✅ Consistent naming conventions
- ✅ Proper error handling
- ✅ Security best practices

## Development Standards

### **Form Implementation Standards**
- All forms follow the established sectioning pattern
- Consistent field layout and validation
- Proper JavaScript integration with global libraries
- Standard button placement and styling
- Responsive design principles

### **DDD Compliance**
- Clear separation of concerns
- Action-based business logic
- Service layer for data operations
- Proper domain modeling
- Consistent audit logging

### **UI/UX Standards**
- Bootstrap component usage
- SweetAlert2 for user interactions
- Select2 for enhanced dropdowns
- DataTables for list management
- Consistent color scheme and typography

## Summary

The Purchase Invoice module is a comprehensive, production-ready implementation that follows all established patterns and standards. It provides complete payment tracking functionality with seamless Purchase Order integration, robust audit trails, and user-friendly interfaces. The module supports the full invoice lifecycle from creation to payment completion while maintaining data integrity and providing detailed change tracking for compliance requirements.

**Key Achievements:**
- ✅ Full DDD architecture implementation
- ✅ Complete payment tracking workflow
- ✅ Seamless PO integration with historical comparison
- ✅ Comprehensive audit logging
- ✅ Mobile-responsive user interface
- ✅ Production-ready security and validation
- ✅ Established coding standards compliance

The module is ready for production deployment and provides a solid foundation for future enhancements and integrations.