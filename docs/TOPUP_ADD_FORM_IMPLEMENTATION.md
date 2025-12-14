# Top Up Module - Add Form Implementation

**Date**: December 14, 2025
**Status**: Add Form Complete with Revenue Split Integration

## Overview
Created a complete add form for the Top Up module that integrates with the revenue split system. The form automatically calculates FIFO allocation based on outstanding Sales Orders from the `t_sales_order_revenue_splits` table.

## Files Created/Modified

### 1. Migration File
**File**: `database/migrations/2025_12_14_100003_add_payment_fields_to_sales_order_revenue_splits.php`

**Purpose**: Add payment tracking fields to revenue splits table

**Fields Added**:
- `is_paid_perusahaan` (boolean) - Payment status for Kingdom portion
- `is_paid_owner` (boolean) - Payment status for Owner portion
- `is_paid_pabrik` (boolean) - Payment status for Factory portion

**Indexes Added**:
- `is_paid_perusahaan` + `company_code`
- `is_paid_owner` + `company_code`

**Run Migration**:
```bash
php artisan migrate --path=database/migrations/2025_12_14_100003_add_payment_fields_to_sales_order_revenue_splits.php
```

### 2. Add Form View
**File**: `resources/views/pages/main_transaction/topup/add.blade.php`

**Features**:
- ✅ 3-step stepper (Input Data → Preview Alokasi → Konfirmasi)
- ✅ Entity selection (KINGDOM/OWNER)
- ✅ Top Up date picker
- ✅ Amount input with currency formatting
- ✅ Optional notes field
- ✅ Outstanding piutang table (auto-loads on entity selection)
- ✅ Real-time FIFO allocation preview
- ✅ Summary box showing total/allocated/remaining
- ✅ Detailed allocation breakdown per SO
- ✅ Form validation and confirmation dialog
- ✅ AJAX submission with loading state

### 3. Controller Updates
**File**: `app/Http/Controllers/MainTransaction/TopUpController.php`

**Updated Methods**:

#### `getOutstandingSalesOrders()`
- Joins `t_sales_orders` with `t_sales_order_revenue_splits`
- Filters by entity payment status (`is_paid_perusahaan` or `is_paid_owner`)
- Sums revenue portions (`porsi_perusahaan` or `porsi_owner`)
- Adds `remaining_amount` if the entity owns the remainder
- Orders by transaction date (FIFO)
- Returns: SO number, date, customer, total amount, remaining amount

#### `calculateFifoAllocation()`
- Gets unpaid SOs using revenue splits
- Calculates FIFO allocation preview
- Returns: topup amount, total allocated, remaining balance, allocation details per SO

## Business Logic

### Revenue Calculation by Entity

**For KINGDOM (Perusahaan)**:
```sql
Total Amount = SUM(porsi_perusahaan)
             + SUM(remaining_amount WHERE remaining_allocation = 'perusahaan')

Payment Status = is_paid_perusahaan = false
```

**For OWNER**:
```sql
Total Amount = SUM(porsi_owner)
             + SUM(remaining_amount WHERE remaining_allocation = 'owner')

Payment Status = is_paid_owner = false
```

### FIFO Allocation Process

1. **Load Outstanding SOs**:
   - Filter by entity type
   - Filter by `is_paid_perusahaan = false` (Kingdom) or `is_paid_owner = false` (Owner)
   - Group by Sales Order
   - Sum the revenue portions for that entity
   - Order by `transaction_date ASC` (oldest first)

2. **Calculate Allocation**:
   - Start with Top Up amount
   - For each SO (in order):
     - Allocate: min(remaining_topup, so_remaining)
     - Deduct from Top Up amount
     - Track allocation sequence
     - Continue until Top Up exhausted or all SOs paid

3. **Preview Display**:
   - Show each SO with allocated amount
   - Show remaining balance for each SO
   - Show new status (PAID/PARTIAL)
   - Show total allocated and remaining Top Up balance

## Form Workflow

### Step 1: Input Data
```
1. User selects Entity (KINGDOM/OWNER)
2. User enters Top Up Date
3. User enters Top Up Amount
4. User adds optional Notes
```

### Step 2: Outstanding Piutang (Auto-Load)
```
1. When entity selected, AJAX call to getOutstandingSalesOrders()
2. Display table of unpaid SOs
3. Show: FIFO order, SO date, SO number, customer, amounts, status
4. Table is read-only (no manual selection)
```

### Step 3: Preview Alokasi (Auto-Calculate)
```
1. When amount entered, AJAX call to calculateFifoAllocation()
2. Display summary box:
   - Total Top Up Amount
   - Total Allocated
   - Remaining Balance
3. Display allocation list:
   - Each SO with allocated amount
   - New status after allocation
   - Remaining balance per SO
```

### Step 4: Submit
```
1. User clicks Submit button
2. Confirmation dialog appears
3. On confirm, AJAX POST to store()
4. Backend creates:
   - t_topup record
   - t_topup_sales_order records (allocations)
   - Updates is_paid_perusahaan / is_paid_owner flags
5. Redirect to index page
```

## API Endpoints Used

### GET `/main_transaction/topup/get_outstanding_sales_orders`
**Parameters**:
- `entity_type` (KINGDOM/OWNER)

**Response**:
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "so_number": "SO-001",
      "so_date": "2025-11-15",
      "customer_name": "Customer ABC",
      "total_amount": 2000000,
      "remaining_amount": 2000000
    }
  ]
}
```

### POST `/main_transaction/topup/calculate_fifo_allocation`
**Parameters**:
- `entity_type` (KINGDOM/OWNER)
- `topup_amount` (number)

**Response**:
```json
{
  "success": true,
  "data": {
    "topup_amount": 3000000,
    "total_allocated": 3000000,
    "remaining_balance": 0,
    "allocations": [
      {
        "sequence": 1,
        "so_id": 1,
        "so_number": "SO-001",
        "so_date": "2025-11-15",
        "so_total": 2000000,
        "so_remaining_before": 2000000,
        "allocated_amount": 2000000,
        "so_remaining_after": 0
      }
    ]
  }
}
```

### POST `/main_transaction/topup/store`
**Parameters**:
- `entity_type` (KINGDOM/OWNER)
- `topup_date` (date)
- `topup_amount` (number)
- `notes` (string, optional)
- `allocations` (array of allocation objects)

**Response**:
```json
{
  "success": true,
  "message": "Top Up berhasil disimpan"
}
```

## Frontend Features

### Currency Formatting
```javascript
Input: 1000000
Display: Rp 1.000.000
```

### Real-time Calculation
- Allocation preview updates automatically when amount changes
- Debounced to avoid excessive API calls

### Validation
- Entity is required
- Date is required
- Amount is required and must be > 0
- Submit button disabled until all validations pass

### Error Handling
- Network errors display error dialog
- Validation errors shown inline
- Loading states during AJAX calls

## Testing Checklist

### Form Display
- [ ] Stepper shows correctly
- [ ] All form fields render properly
- [ ] Info boxes display with correct styling
- [ ] Tables are responsive

### Entity Selection
- [ ] KINGDOM option loads outstanding SOs
- [ ] OWNER option loads outstanding SOs
- [ ] Outstanding table shows correct data
- [ ] Empty state displays when no data
- [ ] Customer names appear correctly

### Amount Input
- [ ] Currency formatting works
- [ ] Large numbers format correctly
- [ ] Decimal handling (should be integers only)
- [ ] Copy-paste numbers work

### FIFO Allocation
- [ ] Preview appears when amount entered
- [ ] Allocation follows FIFO order (oldest first)
- [ ] Summary box shows correct totals
- [ ] Allocation list displays all affected SOs
- [ ] Remaining balance calculates correctly
- [ ] Status indicators show PAID/PARTIAL correctly

### Form Submission
- [ ] Validation prevents empty submission
- [ ] Confirmation dialog appears
- [ ] Loading state shows during submission
- [ ] Success redirects to index
- [ ] Error messages display properly

### Integration Tests
- [ ] Revenue split calculation matches expected values
- [ ] `is_paid_perusahaan` field filters correctly
- [ ] `is_paid_owner` field filters correctly
- [ ] `remaining_amount` adds when `remaining_allocation` matches entity
- [ ] Multiple products per SO sum correctly

## Next Steps

To complete the Top Up module, you still need to:

### 1. Implement `store()` Method
Update the `store()` method in TopUpController to:
- Generate Top Up number
- Create `t_topup` record
- Create `t_topup_sales_order` records for each allocation
- Update `is_paid_perusahaan` or `is_paid_owner` flags in revenue splits
- Handle partial payments (some splits paid, others not)
- Wrap in database transaction

### 2. Create Detail Page
File: `resources/views/pages/main_transaction/topup/detail.blade.php`
- Show Top Up header information
- Display allocation breakdown table
- Show affected SOs and amounts
- Download/view attachment option

### 3. Create Edit Page
File: `resources/views/pages/main_transaction/topup/edit.blade.php`
- Similar to add form
- Pre-fill existing data
- Only for OPEN status
- Cannot change entity type
- Recalculate allocations on amount change

### 4. Update Revenue Split Status
When Top Up is saved:
```sql
-- Mark as paid if SO is fully paid
UPDATE t_sales_order_revenue_splits
SET is_paid_perusahaan = true  -- or is_paid_owner
WHERE f_sales_order_id = ?
AND (allocated amount covers full remaining)

-- Keep as unpaid if partial
-- (split may still have remaining balance)
```

## Database Migration Notes

**IMPORTANT**: Run this migration before testing the add form:

```bash
php artisan migrate --path=database/migrations/2025_12_14_100003_add_payment_fields_to_sales_order_revenue_splits.php
```

This adds the required payment status fields to track which entities have paid their portions.

## SQL Queries for Verification

### Check Outstanding for Kingdom
```sql
SELECT
    so.transaction_code,
    so.transaction_date,
    SUM(rs.porsi_perusahaan) as base_portion,
    SUM(CASE
        WHEN rs.remaining_allocation = 'perusahaan'
        THEN rs.remaining_amount
        ELSE 0
    END) as extra_portion,
    SUM(
        rs.porsi_perusahaan +
        CASE
            WHEN rs.remaining_allocation = 'perusahaan'
            THEN rs.remaining_amount
            ELSE 0
        END
    ) as total_owed
FROM t_sales_orders so
JOIN t_sales_order_revenue_splits rs ON so.id = rs.f_sales_order_id
WHERE so.company_code = 'YOUR_CODE'
  AND rs.is_paid_perusahaan = false
GROUP BY so.id, so.transaction_code, so.transaction_date
HAVING total_owed > 0
ORDER BY so.transaction_date ASC;
```

### Check Outstanding for Owner
```sql
SELECT
    so.transaction_code,
    so.transaction_date,
    SUM(rs.porsi_owner) as base_portion,
    SUM(CASE
        WHEN rs.remaining_allocation = 'owner'
        THEN rs.remaining_amount
        ELSE 0
    END) as extra_portion,
    SUM(
        rs.porsi_owner +
        CASE
            WHEN rs.remaining_allocation = 'owner'
            THEN rs.remaining_amount
            ELSE 0
        END
    ) as total_owed
FROM t_sales_orders so
JOIN t_sales_order_revenue_splits rs ON so.id = rs.f_sales_order_id
WHERE so.company_code = 'YOUR_CODE'
  AND rs.is_paid_owner = false
GROUP BY so.id, so.transaction_code, so.transaction_date
HAVING total_owed > 0
ORDER BY so.transaction_date ASC;
```

## Related Documentation

- **Top Up DDL**: `docs/TOPUP_DDL.md`
- **Module Summary**: `docs/TOPUP_MODULE_SUMMARY.md`
- **Implementation Status**: `docs/TOPUP_IMPLEMENTATION_STATUS.md`
- **Permissions Disabled**: `docs/TOPUP_PERMISSIONS_DISABLED.md`

## Notes

- Form follows the design from `topup_payment_ui (2).html`
- Adapted to use existing Bootstrap/Tabler theme
- Fully integrated with revenue split system
- FIFO calculation respects `remaining_allocation` logic
- All AJAX calls use Laravel CSRF token
- jQuery and DataTables already available in base layout
