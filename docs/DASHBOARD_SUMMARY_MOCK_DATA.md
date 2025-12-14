# Dashboard Summary - Mock Data Implementation

**Date**: December 14, 2025
**Status**: Mock data successfully connected to dashboard

## Overview
Added comprehensive mock data to the dashboard_summary page to populate all three entity tabs (PABRIK, KINGDOM, OWNER) with realistic sample transactions based on the finance_ui_viz template.

## Files Modified

### 1. HomeController.php
**File**: `app/Http/Controllers/HomeController.php`
**Method**: `dashboardSummary()`

**Changes**:
- Added mock data collections for all three entity tabs
- Implemented automatic summary calculations
- Structured data to match the template requirements

## Mock Data Structure

### PABRIK Tab

#### Summary Cards
- **Total Sales Pabrik**: Rp 54,250,000
  - Calculated from: Sum of all sales_pabrik_data total_value
  - Stock transfers from Pabrik to Kingdom & Owner

- **Total Piutang Pabrik**: Rp 25,000,000
  - Calculated from: Sum of remaining_amount in piutang_pabrik_data
  - **Piutang Count**: 2 Open Invoices

#### Sales Pabrik Data (Stock Keluar)
5 sample transactions:

```php
[
    'date' => '2025-12-12',
    'destination' => 'Kingdom',
    'product' => 'RIYO Water 600ml',
    'qty' => 1000,
    'transfer_price' => 15000,
    'total_value' => 15000000,
    'status' => 'PAID',
]
```

**Fields**:
- `date`: Transaction date
- `destination`: Kingdom or Owner
- `product`: RIYO Water product variant
- `qty`: Quantity transferred
- `transfer_price`: Internal transfer price
- `total_value`: Total value (qty × transfer_price)
- `status`: PAID, PARTIAL, or OPEN

#### Piutang Pabrik Data
4 sample receivables:

```php
[
    'source_entity' => 'Kingdom',
    'ref_sales' => 'SK-001',
    'receivable_amount' => 15000000,
    'paid_amount' => 15000000,
    'remaining_amount' => 0,
    'status' => 'PAID',
]
```

**Fields**:
- `source_entity`: Kingdom or Owner (who owes Pabrik)
- `ref_sales`: Reference to sales transaction
- `receivable_amount`: Original amount owed
- `paid_amount`: Amount already paid
- `remaining_amount`: Outstanding balance
- `status`: PAID, PARTIAL, or OPEN

### KINGDOM Tab

#### Summary Cards
- **Total Sales Kingdom**: Rp 56,200,000
  - Calculated from: Sum of all sales_kingdom_data total
  - Sales to external customers

- **Total Pengeluaran**: Rp 12,000,000
  - Mock operational expenses for Kingdom

#### Sales Kingdom Data
3 sample customer sales:

```php
[
    'date' => '2025-12-13',
    'customer' => 'Toko Segar Mart',
    'product' => 'RIYO Water 600ml',
    'qty' => 800,
    'price' => 25000,
    'total' => 20000000,
    'status' => 'PAID',
]
```

**Fields**:
- `date`: Sale date
- `customer`: Customer name
- `product`: Product sold
- `qty`: Quantity sold
- `price`: Selling price per unit
- `total`: Total revenue (qty × price)
- `status`: Payment status

### OWNER Tab

#### Summary Cards
- **Total Sales Owner**: Rp 39,000,000
  - Calculated from: Sum of all sales_owner_data total
  - Sales to external customers

- **Total Pengeluaran**: Rp 8,500,000
  - Mock operational expenses for Owner

#### Sales Owner Data
3 sample customer sales:

```php
[
    'date' => '2025-12-13',
    'customer' => 'Hotel Grand Deluxe',
    'product' => 'RIYO Water 330ml',
    'qty' => 2000,
    'price' => 8500,
    'total' => 17000000,
    'status' => 'PAID',
]
```

## Mock Products Used

All mock data uses realistic RIYO Water products:

1. **RIYO Water 600ml**
   - Pabrik transfer price: Rp 15,000
   - Kingdom selling price: Rp 25,000
   - Owner selling price: Rp 25,000

2. **RIYO Water 1500ml**
   - Pabrik transfer price: Rp 40,000
   - Kingdom selling price: Rp 65,000

3. **RIYO Water 330ml**
   - Pabrik transfer price: Rp 4,000
   - Kingdom selling price: Rp 8,500
   - Owner selling price: Rp 8,500

4. **RIYO Water 19L Galon**
   - Pabrik transfer price: Rp 20,000
   - Owner selling price: Rp 35,000

5. **RIYO Water 240ml Cup**
   - Pabrik transfer price: Rp 3,500

## Mock Customers

Realistic customer names for Kingdom and Owner sales:

### Kingdom Customers
- Toko Segar Mart
- Minimarket Berkah
- Warung Bu Ita

### Owner Customers
- Hotel Grand Deluxe
- Restoran Seafood Asli
- Kantin Pabrik ABC

## Payment Status Distribution

### PABRIK Tab
- **Sales**: 3 PAID, 1 OPEN, 1 PARTIAL
- **Piutang**: 2 PAID, 1 OPEN, 1 PARTIAL

### KINGDOM Tab
- **Sales**: 1 PAID, 1 PARTIAL, 1 OPEN

### OWNER Tab
- **Sales**: 1 PAID, 1 PARTIAL, 1 OPEN

## View Updates

### dashboard_summary.blade.php

**Updated Sections**:

1. **KINGDOM Sales Table** (lines 328-359)
   - Added `@forelse` loop for `$sales_kingdom_data`
   - Displays customer sales with status badges

2. **OWNER Sales Table** (lines 442-473)
   - Added `@forelse` loop for `$sales_owner_data`
   - Displays customer sales with status badges

**Already Working**:
- PABRIK Sales Table (already had proper loop)
- PABRIK Piutang Table (already had proper loop)
- All summary cards (using calculated totals)

## How to Use

### Accessing the Dashboard
Navigate to: `/dashboard/summary`

### Filter by Date Range
The dashboard accepts date range parameters:
- `date_from`: Start date (defaults to start of current month)
- `date_to`: End date (defaults to end of current month)

Example:
```
/dashboard/summary?date_from=2025-12-01&date_to=2025-12-31
```

## Mock Data Values Summary

```
PABRIK Tab:
├── Sales Pabrik: Rp 54,250,000 (5 transactions)
└── Piutang Pabrik: Rp 25,000,000 (2 open invoices)

KINGDOM Tab:
├── Sales Kingdom: Rp 56,200,000 (3 transactions)
└── Pengeluaran: Rp 12,000,000

OWNER Tab:
├── Sales Owner: Rp 39,000,000 (3 transactions)
└── Pengeluaran: Rp 8,500,000
```

## Next Steps for Real Data

When connecting to actual database tables, replace the mock data in `HomeController::dashboardSummary()` with:

### For PABRIK Tab
```php
// Replace sales_pabrik_data with actual query
$sales_pabrik_data = DB::table('t_stock_movements')
    ->where('movement_type', 'TRANSFER')
    ->whereBetween('movement_date', [$dateFrom, $dateTo])
    ->get();

// Replace piutang_pabrik_data with actual query from t_topup_sales_order
$piutang_pabrik_data = DB::table('t_topup_sales_order')
    ->join('t_sales_orders', ...)
    ->whereBetween('date', [$dateFrom, $dateTo])
    ->get();
```

### For KINGDOM Tab
```php
// Replace sales_kingdom_data with actual sales orders
$sales_kingdom_data = DB::table('t_sales_orders as so')
    ->join('t_sales_order_revenue_splits as rs', ...)
    ->where('so.entity_type', 'KINGDOM')
    ->whereBetween('so.transaction_date', [$dateFrom, $dateTo])
    ->get();
```

### For OWNER Tab
```php
// Replace sales_owner_data with actual sales orders
$sales_owner_data = DB::table('t_sales_orders as so')
    ->join('t_sales_order_revenue_splits as rs', ...)
    ->where('so.entity_type', 'OWNER')
    ->whereBetween('so.transaction_date', [$dateFrom, $dateTo])
    ->get();
```

## Status Badges

All tables use consistent status badges:

- **PAID**: Green badge with checkmark icon
  ```blade
  <span class="badge bg-label-success">
      <i class="icon-xs ti ti-check"></i> PAID
  </span>
  ```

- **PARTIAL**: Yellow/Warning badge with half-circle icon
  ```blade
  <span class="badge bg-label-warning">
      <i class="icon-xs ti ti-circle-half"></i> PARTIAL
  </span>
  ```

- **OPEN**: Red/Danger badge with circle icon
  ```blade
  <span class="badge bg-label-danger">
      <i class="icon-xs ti ti-circle"></i> OPEN
  </span>
  ```

## Testing Checklist

- [x] PABRIK tab displays summary cards with correct totals
- [x] PABRIK sales table shows 5 sample transactions
- [x] PABRIK piutang table shows 4 sample receivables
- [x] KINGDOM tab displays summary cards with correct totals
- [x] KINGDOM sales table shows 3 sample transactions
- [x] OWNER tab displays summary cards with correct totals
- [x] OWNER sales table shows 3 sample transactions
- [x] All status badges display correctly
- [x] Currency formatting works (Rp format with thousands separator)
- [x] Date formatting works (dd/mm/yyyy)
- [x] Empty state handling with @forelse

## Notes

- All monetary values use Indonesian Rupiah format: `Rp 1.000.000`
- Dates use Indonesian format: `dd/mm/yyyy`
- Mock data includes realistic product names and customer names
- Transfer prices (Pabrik to Kingdom/Owner) are lower than selling prices
- Status distribution ensures UI properly displays all badge types
- Collections use Laravel's `collect()` for easy manipulation

## Related Files

- **Controller**: [app/Http/Controllers/HomeController.php](../app/Http/Controllers/HomeController.php)
- **View**: [resources/views/pages/dashboard_summary.blade.php](../resources/views/pages/dashboard_summary.blade.php)
- **Route**: [routes/home.php](../routes/home.php) - `Route::get('/dashboard/summary', ...)`
- **Template Reference**: `C:\Users\user\Downloads\finance_ui_viz (1).html`
