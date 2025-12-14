# Top Up Module - Deposit Calculation Update

**Date**: December 14, 2025
**Status**: Updated to calculate remaining amount based on deposits

## Overview
Updated the `getOutstandingSalesOrders` and `calculateFifoAllocation` methods to correctly calculate remaining amounts based on previously allocated deposits from the `t_topup_sales_order` table.

## Changes Made

### 1. getOutstandingSalesOrders() Method

**Updated Logic**:
- Simplified the SELECT to only sum the `porsi_perusahaan` or `porsi_owner` field
- Removed the complex `remaining_allocation` CASE statement
- Added post-processing to calculate remaining amount by subtracting allocated deposits
- Filters out fully paid SOs (where remaining_amount <= 0)

**New Query**:
```php
// Get outstanding SOs with total amount
$outstandingSO = DB::table('t_sales_orders as so')
    ->join('t_sales_order_revenue_splits as rs', 'so.id', '=', 'rs.f_sales_order_id')
    ->leftJoin('m_contacts as c', 'so.f_customer_id', '=', 'c.id')
    ->select([
        'so.id',
        'so.transaction_code',
        'so.transaction_date',
        'c.name as customer_name',
        DB::raw("SUM(rs.{$porsiField}) as total_amount"),
    ])
    ->where('so.company_code', $companyCode)
    ->where('rs.' . $isPaidField, false)
    ->groupBy('so.id', 'so.transaction_code', 'so.transaction_date', 'c.name')
    ->orderBy('so.transaction_date', 'ASC')
    ->orderBy('so.id', 'ASC')
    ->get();

// Calculate remaining for each SO
$formattedData = $outstandingSO->map(function ($item) {
    // Get deposits already allocated
    $allocatedDeposit = DB::table('t_topup_sales_order as tso')
        ->where('tso.sales_order_id', $item->id)
        ->sum('tso.allocated_amount');

    $remainingAmount = $item->total_amount - $allocatedDeposit;

    return [
        'id' => $item->id,
        'so_number' => $item->transaction_code,
        'so_date' => $item->transaction_date,
        'customer_name' => $item->customer_name,
        'total_amount' => $item->total_amount,
        'allocated_deposit' => $allocatedDeposit,
        'remaining_amount' => $remainingAmount,
    ];
});

// Filter out fully paid
$filteredData = $formattedData->filter(function ($item) {
    return $item['remaining_amount'] > 0;
})->values();
```

### 2. calculateFifoAllocation() Method

**Updated Logic**:
- Simplified the SELECT to only sum the `porsi_perusahaan` or `porsi_owner` field
- Removed the complex `remaining_allocation` CASE statement and HAVING clause
- Added deposit calculation within the FIFO loop
- Skips SOs that are already fully paid

**New Calculation**:
```php
foreach ($outstandingSO as $so) {
    if ($remainingTopup <= 0) {
        break;
    }

    // Get deposits already allocated to this SO
    $allocatedDeposit = DB::table('t_topup_sales_order')
        ->where('sales_order_id', $so->id)
        ->sum('allocated_amount');

    // Calculate remaining amount
    $soRemaining = $so->total_amount - $allocatedDeposit;

    // Skip if already fully paid
    if ($soRemaining <= 0) {
        continue;
    }

    $allocationAmount = min($remainingTopup, $soRemaining);

    // ... create allocation record
}
```

## How It Works

### Step 1: Get Unpaid SOs
Query returns all SOs where:
- `is_paid_perusahaan = false` (for KINGDOM)
- `is_paid_owner = false` (for OWNER)
- Sums the revenue portion for that entity

### Step 2: Calculate Deposits
For each SO, query `t_topup_sales_order` to get total deposits already allocated:
```sql
SELECT SUM(allocated_amount)
FROM t_topup_sales_order
WHERE sales_order_id = ?
```

### Step 3: Calculate Remaining
```
remaining_amount = total_amount - allocated_deposit
```

**Example**:
```
SO-001:
  - Total Amount (porsi_perusahaan): Rp 5,000,000
  - Allocated Deposit (from previous Top Ups): Rp 2,000,000
  - Remaining Amount: Rp 3,000,000 ← This shows in the table
```

### Step 4: Filter and Display
- Filter out SOs where `remaining_amount <= 0`
- Display only SOs with outstanding balance
- Show in FIFO order (oldest first)

## Database Relationships

### t_sales_order_revenue_splits
```
Stores: porsi_perusahaan, porsi_owner, porsi_pabrik
Flags: is_paid_perusahaan, is_paid_owner, is_paid_pabrik
```

### t_topup_sales_order (Deposit Tracking)
```
Stores: allocated_amount per Top Up per SO
Purpose: Track how much deposit has been applied to each SO
```

### Calculation Flow
```
1. SUM(porsi_perusahaan) from revenue_splits
   → Get total owed by entity

2. SUM(allocated_amount) from topup_sales_order
   → Get total deposits already applied

3. remaining = total - deposits
   → Get what's still outstanding
```

## Benefits of This Approach

### 1. Accurate Deposit Tracking
- Each Top Up creates records in `t_topup_sales_order`
- Historical tracking of all deposits
- Can see deposit history per SO

### 2. Flexible Payments
- Entity can pay partial amounts over time
- Each payment is tracked separately
- Remaining balance always accurate

### 3. FIFO Compliance
- Oldest SOs paid first
- Partial payments follow same FIFO order
- Transparent allocation sequence

### 4. Audit Trail
- Full history of deposits in `t_topup_sales_order`
- Can trace which Top Up paid which SO
- Allocation sequence preserved

## Example Scenario

### Initial State
```
SO-001 (2025-11-01): Total = Rp 5,000,000, Deposits = Rp 0, Remaining = Rp 5,000,000
SO-002 (2025-11-15): Total = Rp 3,000,000, Deposits = Rp 0, Remaining = Rp 3,000,000
SO-003 (2025-12-01): Total = Rp 2,000,000, Deposits = Rp 0, Remaining = Rp 2,000,000
```

### After First Top Up (Rp 6,000,000)
```
t_topup_sales_order records created:
- SO-001: allocated_amount = Rp 5,000,000
- SO-002: allocated_amount = Rp 1,000,000

Current Outstanding:
SO-001: Total = Rp 5,000,000, Deposits = Rp 5,000,000, Remaining = Rp 0 (PAID)
SO-002: Total = Rp 3,000,000, Deposits = Rp 1,000,000, Remaining = Rp 2,000,000
SO-003: Total = Rp 2,000,000, Deposits = Rp 0, Remaining = Rp 2,000,000
```

### After Second Top Up (Rp 3,000,000)
```
t_topup_sales_order records created:
- SO-002: allocated_amount = Rp 2,000,000 (additional)
- SO-003: allocated_amount = Rp 1,000,000

Current Outstanding:
SO-001: Not shown (fully paid)
SO-002: Total = Rp 3,000,000, Deposits = Rp 3,000,000, Remaining = Rp 0 (PAID)
SO-003: Total = Rp 2,000,000, Deposits = Rp 1,000,000, Remaining = Rp 1,000,000
```

## SQL Verification Query

Check outstanding with deposits for Kingdom:
```sql
SELECT
    so.transaction_code,
    so.transaction_date,
    SUM(rs.porsi_perusahaan) as total_amount,
    COALESCE((
        SELECT SUM(tso.allocated_amount)
        FROM t_topup_sales_order tso
        WHERE tso.sales_order_id = so.id
    ), 0) as allocated_deposit,
    SUM(rs.porsi_perusahaan) - COALESCE((
        SELECT SUM(tso.allocated_amount)
        FROM t_topup_sales_order tso
        WHERE tso.sales_order_id = so.id
    ), 0) as remaining_amount
FROM t_sales_orders so
JOIN t_sales_order_revenue_splits rs ON so.id = rs.f_sales_order_id
WHERE so.company_code = 'YOUR_CODE'
  AND rs.is_paid_perusahaan = false
GROUP BY so.id, so.transaction_code, so.transaction_date
HAVING remaining_amount > 0
ORDER BY so.transaction_date ASC;
```

## Updated API Responses

### getOutstandingSalesOrders
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "so_number": "SO-001",
      "so_date": "2025-11-15",
      "customer_name": "Customer ABC",
      "total_amount": 5000000,
      "allocated_deposit": 2000000,
      "remaining_amount": 3000000
    }
  ]
}
```

### calculateFifoAllocation
```json
{
  "success": true,
  "data": {
    "topup_amount": 4000000,
    "total_allocated": 4000000,
    "remaining_balance": 0,
    "allocations": [
      {
        "sequence": 1,
        "so_id": 1,
        "so_number": "SO-001",
        "so_date": "2025-11-15",
        "so_total": 5000000,
        "so_remaining_before": 3000000,
        "allocated_amount": 3000000,
        "so_remaining_after": 0
      },
      {
        "sequence": 2,
        "so_id": 2,
        "so_number": "SO-002",
        "so_date": "2025-11-20",
        "so_total": 3000000,
        "so_remaining_before": 2000000,
        "allocated_amount": 1000000,
        "so_remaining_after": 1000000
      }
    ]
  }
}
```

## Testing Checklist

### Outstanding Table Display
- [ ] Shows correct total amount per SO
- [ ] Shows allocated deposits correctly
- [ ] Shows remaining amount (total - deposits)
- [ ] Filters out fully paid SOs
- [ ] Orders by transaction date (FIFO)

### FIFO Allocation
- [ ] Considers deposits when calculating remaining
- [ ] Skips fully paid SOs
- [ ] Allocates to oldest unpaid/partial SOs first
- [ ] Calculates remaining correctly after allocation
- [ ] Handles multiple partial payments correctly

### Edge Cases
- [ ] SO with no deposits shows full amount
- [ ] SO with partial deposits shows correct remaining
- [ ] SO with full deposits is filtered out
- [ ] Multiple Top Ups to same SO accumulate correctly
- [ ] Empty outstanding list when all SOs paid

## Related Files

- **Controller**: [app/Http/Controllers/MainTransaction/TopUpController.php](../app/Http/Controllers/MainTransaction/TopUpController.php)
- **Add Form**: [resources/views/pages/main_transaction/topup/add.blade.php](../resources/views/pages/main_transaction/topup/add.blade.php)
- **Implementation Doc**: [docs/TOPUP_ADD_FORM_IMPLEMENTATION.md](TOPUP_ADD_FORM_IMPLEMENTATION.md)

## Notes

- Deposit calculation happens on every query (real-time)
- No caching of remaining amounts
- Accurate even if deposits change between page loads
- Performance: Additional subquery per SO (acceptable for reasonable number of SOs)
- Consider indexing `t_topup_sales_order.sales_order_id` for better performance
