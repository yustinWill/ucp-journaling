# Top Up Module - Implementation Summary

## Overview
Created Top Up Payment module to track lump-sum payments from Kingdom and Owner entities to Pabrik, with automatic FIFO (First In, First Out) allocation to outstanding Sales Orders and deposit balance management.

## Business Logic

### Concept:
1. **Payment Flow**: Kingdom and Owner entities pay Pabrik for their sales revenue
2. **Top Up Submission**: Payment is submitted as a "Top Up" with a total amount
3. **Automatic Allocation**: System automatically allocates payment to outstanding Sales Orders using FIFO
4. **Deposit Balance**: Any remaining amount after allocation becomes a "deposit" balance
5. **FIFO Based on Date**: Oldest Sales Orders (by transaction_date) are paid first

### Example FIFO Scenario:
```
Owner Top Up: Rp 3,000,000

Outstanding SO (ordered by date):
- SO-001 (2025-12-01): Rp 1,500,000 remaining → Allocated: Rp 1,500,000 (PAID)
- SO-002 (2025-12-05): Rp 2,000,000 remaining → Allocated: Rp 1,500,000 (PARTIAL - Rp 500,000 left)
- SO-003 (2025-12-10): Rp 800,000 remaining → Allocated: Rp 0 (still OPEN)

Result: Allocated Rp 3,000,000, Remaining Balance: Rp 0
```

## Files Created

### 1. Database Migrations

#### t_topup table
**File**: `database/migrations/2025_12_14_100000_create_t_topup_table.php`

**Purpose**: Main table to track Top Up transactions

**Key Fields**:
- `topup_number`: Unique reference (e.g., TU-202512-001)
- `entity_type`: KINGDOM or OWNER
- `topup_amount`: Original payment amount
- `allocated_amount`: Total allocated to SOs
- `remaining_balance`: Deposit balance (topup_amount - allocated_amount)
- `status`: OPEN, PARTIAL, or PAID

#### t_topup_sales_order table
**File**: `database/migrations/2025_12_14_100001_create_t_topup_sales_order_table.php`

**Purpose**: Mapping table to track which SOs were paid by which Top Up

**Key Fields**:
- `topup_id`: FK to t_topup
- `sales_order_id`: FK to t_sales_orders
- `allocated_amount`: Amount allocated to this SO
- `so_remaining_before`: SO remaining before allocation
- `so_remaining_after`: SO remaining after allocation
- `allocation_sequence`: FIFO sequence number (1, 2, 3, ...)

### 2. Routes

**File**: `routes/main_transaction.php`

**Added Routes**:
```php
// Main routes
GET    /main_transaction/topup/index
GET    /main_transaction/topup/index_data_table
GET    /main_transaction/topup/add
POST   /main_transaction/topup/store
GET    /main_transaction/topup/detail/{id}
GET    /main_transaction/topup/edit/{id}
POST   /main_transaction/topup/update
DELETE /main_transaction/topup/{id}

// AJAX routes
GET    /main_transaction/topup/generate_transaction_number
GET    /main_transaction/topup/get_outstanding_sales_orders
POST   /main_transaction/topup/calculate_fifo_allocation
```

### 3. Views

#### Index Page
**File**: `resources/views/pages/main_transaction/topup/index.blade.php`

**Features**:
- Filter by Entity (Kingdom/Owner), Date Range, Status
- DataTable with server-side processing
- Displays: Date, Entity, Reference, Amounts, Status
- Entity badges with color coding (Purple for Kingdom, Orange for Owner)
- Status badges (Green=PAID, Yellow=PARTIAL, Red=OPEN)
- Action buttons (Detail, Edit for OPEN status)
- Responsive design

### 4. Menu Integration

**File**: `resources/views/layouts/menu.blade.php`

**Added Menu Item**:
```blade
<li class="menu-item">
    <a href="{{ route('main_transaction.topup.index') }}" class="menu-link">
        <i class="menu-icon icon-base ti tabler-cash"></i>
        <div data-i18n="Top Up">Top Up</div>
    </a>
</li>
```

**Location**: After "Absorpsi Overhead" menu, under "Transaksi" section
**Icon**: `tabler-cash` (money/cash icon)

### 5. Documentation

#### DDL Documentation
**File**: `docs/TOPUP_DDL.md`

**Contents**:
- Complete DDL for both tables
- Field specifications
- FIFO allocation logic (pseudocode)
- Business rules
- Verification queries
- Rollback instructions
- Integration points with Sales Order table

#### Implementation Summary
**File**: `docs/TOPUP_MODULE_SUMMARY.md` (this file)

## Database Schema

### t_topup Table Structure
```sql
CREATE TABLE `t_topup` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `company_code` VARCHAR(10) NOT NULL,
    `topup_number` VARCHAR(50) NOT NULL UNIQUE,
    `topup_date` DATE NOT NULL,
    `entity_type` ENUM('KINGDOM', 'OWNER') NOT NULL,
    `topup_amount` DECIMAL(15,2) NOT NULL DEFAULT 0,
    `allocated_amount` DECIMAL(15,2) NOT NULL DEFAULT 0,
    `remaining_balance` DECIMAL(15,2) NOT NULL DEFAULT 0,
    `notes` TEXT NULL,
    `attachment_path` VARCHAR(255) NULL,
    `status` ENUM('OPEN', 'PARTIAL', 'PAID') NOT NULL DEFAULT 'OPEN',
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    `created_by` BIGINT UNSIGNED NULL,
    `updated_by` BIGINT UNSIGNED NULL,
    -- indexes and foreign keys
);
```

### t_topup_sales_order Table Structure
```sql
CREATE TABLE `t_topup_sales_order` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `company_code` VARCHAR(10) NOT NULL,
    `topup_id` BIGINT UNSIGNED NOT NULL,
    `sales_order_id` BIGINT UNSIGNED NOT NULL,
    `allocated_amount` DECIMAL(15,2) NOT NULL DEFAULT 0,
    `so_total_amount` DECIMAL(15,2) NOT NULL DEFAULT 0,
    `so_remaining_before` DECIMAL(15,2) NOT NULL DEFAULT 0,
    `so_remaining_after` DECIMAL(15,2) NOT NULL DEFAULT 0,
    `allocation_sequence` INT NOT NULL DEFAULT 0,
    `notes` TEXT NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    -- indexes and foreign keys
);
```

## Status Values

### Top Up Status:
- **OPEN**: No allocation yet (allocated_amount = 0, remaining_balance = topup_amount)
- **PARTIAL**: Partially allocated (0 < allocated_amount < topup_amount)
- **PAID**: Fully allocated (allocated_amount = topup_amount, remaining_balance = 0)

## Next Steps - Controller Implementation

To complete the module, you need to create:

### 1. Controller
**File**: `app/Http/Controllers/MainTransaction/TopUpController.php`

**Required Methods**:
```php
- index()                      // Show index page
- index_data_table()           // DataTable AJAX
- add()                        // Show add form
- store(Request $request)      // Save new top up with FIFO allocation
- detail($id)                  // Show detail page
- edit($id)                    // Show edit form (for OPEN status only)
- update(Request $request)     // Update top up
- destroy($id)                 // Delete top up (for OPEN status only)

// AJAX methods
- generateTransactionNumber()         // Generate TU-YYYYMM-NNN
- getOutstandingSalesOrders($entity)  // Get unpaid SOs by entity (FIFO order)
- calculateFifoAllocation(Request)    // Calculate allocation preview
```

### 2. Service Layer
**File**: `app/Domains/TopUp/Services/TopUpService.php`

**Required Methods**:
```php
- create(array $data)
- update(int $id, array $data)
- findOne(int $id)
- findAll()
- findAllDatatable()
- allocateToSalesOrders(int $topupId, float $amount, string $entityType)
- getOutstandingSalesOrders(string $entityType)
```

### 3. Action Layer
**File**: `app/Domains/TopUp/Actions/CreateTopUpAction.php`

**Logic**:
1. Validate input data
2. Create t_topup record
3. Get outstanding Sales Orders (ordered by transaction_date ASC)
4. Loop through SOs and allocate using FIFO
5. Create t_topup_sales_order records
6. Update SO paid_amount and payment_status
7. Update topup allocated_amount, remaining_balance, status
8. Return allocation result

### 4. Views to Create
- `add.blade.php` - Form to create new top up with allocation preview
- `detail.blade.php` - Show top up details and allocation breakdown
- `edit.blade.php` - Edit form (for OPEN status)

## Integration Requirements

### Sales Order Table Modifications

Need to add these fields to `t_sales_orders` (if not already present):
```sql
ALTER TABLE `t_sales_orders`
ADD COLUMN `paid_amount` DECIMAL(15,2) NOT NULL DEFAULT 0
    COMMENT 'Total payment received from Top Up allocations',
ADD COLUMN `payment_status` ENUM('UNPAID', 'PARTIAL', 'PAID') NOT NULL DEFAULT 'UNPAID'
    COMMENT 'Payment status based on paid_amount vs grand_total';
```

### Transaction Number Generation

Top Up numbers should follow format: `TU-YYYYMM-NNN`
- Example: TU-202512-001, TU-202512-002
- Use existing TransactionNumberService

## Testing Checklist

### Database
- [ ] Run migrations successfully
- [ ] Verify table structures
- [ ] Test foreign key constraints
- [ ] Test cascade delete (topup -> allocations)

### FIFO Allocation
- [ ] Test allocation with sufficient balance
- [ ] Test allocation with insufficient balance (creates deposit)
- [ ] Test allocation with exact amount
- [ ] Verify allocation sequence ordering
- [ ] Verify SO status updates (UNPAID → PARTIAL → PAID)

### UI/UX
- [ ] Filter works correctly
- [ ] DataTable loads and paginates
- [ ] Entity badges display correctly
- [ ] Status badges display correctly
- [ ] Detail view shows allocation breakdown
- [ ] Edit only available for OPEN status
- [ ] Delete only available for OPEN status

### Business Logic
- [ ] Cannot edit/delete PARTIAL or PAID top ups
- [ ] FIFO strictly follows transaction_date order
- [ ] Balance calculation is accurate
- [ ] Multiple top ups can allocate to same SO
- [ ] One top up can allocate to multiple SOs

## UI Reference

The implementation follows the design from `topup_payment_ui (1).html` with these adaptations:
- Uses existing Bootstrap/Tabler theme instead of custom gradient styles
- Maintains same layout structure (filters, table, status badges)
- Entity badges: Kingdom (purple), Owner (orange)
- Status badges: PAID (green), PARTIAL (yellow), OPEN (red)
- Icons: Tabler Icons instead of emojis

## Migration Commands

```bash
# Run migrations
php artisan migrate

# Or run manually
php artisan migrate --path=database/migrations/2025_12_14_100000_create_t_topup_table.php
php artisan migrate --path=database/migrations/2025_12_14_100001_create_t_topup_sales_order_table.php

# Rollback
php artisan migrate:rollback --step=2
```

## Manual DDL Execution

See `docs/TOPUP_DDL.md` for manual DDL queries if needed.

## Support

For questions or issues:
- **DDL Documentation**: `docs/TOPUP_DDL.md`
- **This Summary**: `docs/TOPUP_MODULE_SUMMARY.md`
- **Reference UI**: Provided HTML file

## Notes

- Module is ready for controller implementation
- All routes, views (index), and database schema are complete
- Follow existing code patterns from other transaction modules (Sales Order, Purchase Order, etc.)
- Use existing TransactionNumberService for number generation
- Implement proper permission checks in controller
- Add audit logging for create/update/delete operations
