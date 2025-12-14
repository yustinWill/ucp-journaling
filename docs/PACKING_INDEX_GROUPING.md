# Packing Module - Index Page Grouping by Transaction Number

**Date**: December 14, 2025
**Status**: Implemented grouping of packing records by transaction_number

## Overview
Modified the packing index page to group multiple packing productions by their `transaction_number` from the `t_packaging` table. When a single packing form submission creates multiple packaging products, they are now grouped and displayed as one row with multiple packing numbers.

## Problem Statement

### Original Behavior
- When user creates packing for multiple packaging products in one form submission
- Each packaging product creates a separate `t_productions` record with unique `production_number`:
  - Example: `PROD-2025-001-001`, `PROD-2025-001-002`, `PROD-2025-001-003`
- Index table showed 3 separate rows (one per production)
- Difficult to see which packings were created together

### New Behavior
- Packings from the same transaction are grouped into one row
- Multiple packing numbers displayed as badges
- Shows aggregated quantity and average HPP
- Single detail link for the grouped transaction

## Database Structure

### t_packaging Table
```sql
CREATE TABLE t_packaging (
    id INT PRIMARY KEY,
    company_code VARCHAR,
    production_id INT,                -- FK to t_productions
    bundle_product_id INT,            -- Source bundle consumed
    packaging_product_id INT,         -- Packaging product produced
    quantity_packed DECIMAL,
    transaction_number VARCHAR,       -- Grouping identifier (e.g., "PROD-2025-001")
    created_at TIMESTAMP,
    updated_at TIMESTAMP
)
```

### Relationship
```
One Transaction Number → Multiple t_packaging records → Multiple t_productions records

Example:
transaction_number: "PROD-2025-001"
├── t_packaging (id=1, production_id=10, packaging_product_id=100, qty=50)
│   └── t_productions (id=10, production_number="PROD-2025-001-001")
├── t_packaging (id=2, production_id=11, packaging_product_id=101, qty=30)
│   └── t_productions (id=11, production_number="PROD-2025-001-002")
└── t_packaging (id=3, production_id=12, packaging_product_id=102, qty=20)
    └── t_productions (id=12, production_number="PROD-2025-001-003")
```

## Changes Made

### 1. PackingController.php - index_data_table()

**File**: [app/Http/Controllers/MainTransaction/PackingController.php](../app/Http/Controllers/MainTransaction/PackingController.php:39-113)

**Changed From**: Individual production records
```php
DB::table('t_productions as p')
    ->select('p.*', 'bp.name as bundle_product_name', ...)
    ->where('p.is_packaging', true)
    ->groupBy('p.id')
```

**Changed To**: Grouped by transaction_number
```php
DB::table('t_packaging as pkg')
    ->select(
        'pkg.transaction_number',
        DB::raw('MIN(p.production_date) as packing_date'),
        DB::raw('MIN(p.id) as id'), // First production ID
        DB::raw('GROUP_CONCAT(DISTINCT p.production_number ORDER BY p.production_number SEPARATOR ", ") as packing_number'),
        DB::raw('GROUP_CONCAT(DISTINCT bp.name ORDER BY p.production_number SEPARATOR ", ") as bundle_product_name'),
        DB::raw('GROUP_CONCAT(DISTINCT bp.code ORDER BY p.production_number SEPARATOR ", ") as bundle_product_code'),
        DB::raw('SUM(pkg.quantity_packed) as quantity_packed'),
        // ... other aggregated fields
    )
    ->groupBy('pkg.transaction_number')
```

### Key Aggregations:

1. **packing_number**: `GROUP_CONCAT` all production numbers, comma-separated
   - Result: `"PROD-2025-001-001, PROD-2025-001-002, PROD-2025-001-003"`

2. **bundle_product_name**: `GROUP_CONCAT` all product names
   - Result: `"RIYO Water 600ml Pack 12, RIYO Water 600ml Pack 24"`

3. **bundle_product_code**: `GROUP_CONCAT` all product codes
   - Result: `"RW600-P12, RW600-P24"`

4. **quantity_packed**: `SUM` of all quantities
   - Result: Total quantity across all packaging products

5. **hpp_packing**: `AVG` of all HPP values
   - Result: Average HPP across all productions

6. **id**: `MIN(p.id)` - Uses first production ID for detail link

### 2. index.blade.php - DataTable Column

**File**: [resources/views/pages/main_transaction/packing/index.blade.php](../resources/views/pages/main_transaction/packing/index.blade.php:106-134)

**Updated packing_number Column**:
```javascript
{
    data: 'packing_number',
    render: function(data, type, row) {
        const packingNumbers = data ? data.split(', ') : [];

        if (packingNumbers.length === 1) {
            // Single packing number - show as link
            const detailUrl = "{{ route('main_transaction.packing.detail', ':id') }}"
                .replace(':id', row.id);
            return `<a href="${detailUrl}" class="text-primary fw-semibold">${data}</a>`;
        } else if (packingNumbers.length > 1) {
            // Multiple packing numbers - show as badges
            const detailUrl = "{{ route('main_transaction.packing.detail', ':id') }}"
                .replace(':id', row.id);
            let html = `<a href="${detailUrl}" class="text-decoration-none">`;
            packingNumbers.forEach((num, index) => {
                if (index > 0) html += '<br>';
                html += `<span class="badge bg-label-primary me-1">${num.trim()}</span>`;
            });
            html += '</a>';
            return html;
        } else {
            return '-';
        }
    }
}
```

## Display Examples

### Single Packaging Product
```
+------------+-------------------+-------------------------+
| Date       | No Transaksi      | Nama Produk             |
+------------+-------------------+-------------------------+
| 12/12/2025 | PROD-2025-001-001 | RIYO Water 600ml Pack 12|
+------------+-------------------+-------------------------+
```

### Multiple Packaging Products (Grouped)
```
+------------+--------------------------------------------+----------------------------------------+
| Date       | No Transaksi                               | Nama Produk                            |
+------------+--------------------------------------------+----------------------------------------+
| 12/12/2025 | [PROD-2025-001-001]                       | RIYO Water 600ml Pack 12,              |
|            | [PROD-2025-001-002]                       | RIYO Water 600ml Pack 24,              |
|            | [PROD-2025-001-003]                       | RIYO Water 1500ml Pack 6               |
+------------+--------------------------------------------+----------------------------------------+
```
*Note: Badges shown with primary background color*

## Search Functionality

Updated search to work with both production numbers and transaction numbers:
```php
if ($packing_no = request()->input('packing_no')) {
    $query->where(function($q) use ($packing_no) {
        $q->where('p.production_number', 'like', "%{$packing_no}%")
          ->orWhere('pkg.transaction_number', 'like', "%{$packing_no}%");
    });
}
```

**Search Examples**:
- Search `"PROD-2025-001"` → Matches all productions starting with that
- Search `"001-002"` → Matches specific production number suffix
- Search `"PROD-2025-001-002"` → Matches exact production number

## Total Records Count

Updated to count distinct transaction numbers instead of production count:
```php
$recordsTotal = DB::table('t_packaging')
    ->join('t_productions', 't_packaging.production_id', '=', 't_productions.id')
    ->where('t_productions.is_packaging', true)
    ->distinct('t_packaging.transaction_number')
    ->count('t_packaging.transaction_number');
```

## Benefits

### User Experience
1. **Cleaner Table**: Reduces clutter when multiple packaging products created together
2. **Clear Grouping**: Easy to see which packings belong to same transaction
3. **Total Quantity**: Shows aggregated quantity packed
4. **Visual Badges**: Multiple packing numbers displayed as distinct badges

### Data Accuracy
1. **Correct Aggregation**: Sums quantities correctly across grouped packings
2. **Average HPP**: Shows average cost across all packaging products
3. **Preserved Detail**: Can still drill down to individual productions via detail page

## Detail Page Consideration

The detail link uses `MIN(p.id)` as the ID parameter. The detail page should be updated to:

### Option 1: Show First Production Only
- Current behavior - shows only the first production record
- Simple, no changes needed

### Option 2: Show All Productions in Group (Recommended)
- Update detail page to accept `transaction_number` parameter
- Display all productions grouped under that transaction
- Show each packaging product with its own production details

**Recommended Route Update**:
```php
// Current
Route::get('/packing/{id}', 'detail')->name('main_transaction.packing.detail');

// Suggested
Route::get('/packing/transaction/{transaction_number}', 'detailByTransaction')
    ->name('main_transaction.packing.detail_transaction');
```

## Testing Checklist

- [x] Index page groups by transaction_number
- [x] Multiple packing numbers shown as badges
- [x] Single packing number shown as plain link
- [x] Search works with production numbers
- [x] Search works with transaction numbers
- [x] Quantity aggregation correct
- [x] HPP averaging correct
- [x] Detail link points to first production
- [ ] Detail page shows all productions in group (future enhancement)
- [ ] Product name concatenation displays properly
- [ ] Product code concatenation displays properly

## Known Limitations

1. **Product Name Display**: If multiple products have long names, concatenated string may be very long
   - **Solution**: Consider limiting display to "X products" with tooltip showing all names

2. **Detail Page**: Currently shows only first production
   - **Solution**: Update detail page to show all productions in transaction (see "Detail Page Consideration" above)

3. **HPP Calculation**: Uses AVG which may not reflect true weighted average
   - **Solution**: Consider weighted average based on quantity_packed

## Future Enhancements

### 1. Transaction Number Column
Add separate column showing the base transaction number:
```javascript
{
    data: 'transaction_number',
    name: 'Base Transaction'
}
```

### 2. Count Badge
Show count of packaging products in group:
```javascript
{
    data: 'packing_number',
    render: function(data, type, row) {
        const count = data.split(', ').length;
        return `${data} <span class="badge bg-secondary">${count} items</span>`;
    }
}
```

### 3. Expandable Rows
Implement DataTables child rows to show breakdown:
```
[+] PROD-2025-001                    3 products      Rp 150,000
    └─ [Details showing each packaging product]
```

## Related Files

- **Controller**: [app/Http/Controllers/MainTransaction/PackingController.php](../app/Http/Controllers/MainTransaction/PackingController.php)
- **Index View**: [resources/views/pages/main_transaction/packing/index.blade.php](../resources/views/pages/main_transaction/packing/index.blade.php)
- **CreatePackingAction**: [app/Domains/Production/Actions/CreatePackingAction.php](../app/Domains/Production/Actions/CreatePackingAction.php)
- **Setup Documentation**: [docs/PACKING_MODULE_SETUP.md](PACKING_MODULE_SETUP.md)
- **Implementation Doc**: [docs/PACKING_MODULE_IMPLEMENTATION.md](PACKING_MODULE_IMPLEMENTATION.md)

## SQL Query Example

Raw query used for grouping:
```sql
SELECT
    pkg.transaction_number,
    MIN(p.production_date) as packing_date,
    MIN(p.id) as id,
    GROUP_CONCAT(DISTINCT p.production_number ORDER BY p.production_number SEPARATOR ', ') as packing_number,
    GROUP_CONCAT(DISTINCT bp.name ORDER BY p.production_number SEPARATOR ', ') as bundle_product_name,
    GROUP_CONCAT(DISTINCT bp.code ORDER BY p.production_number SEPARATOR ', ') as bundle_product_code,
    SUM(pkg.quantity_packed) as quantity_packed,
    MIN(w.name) as warehouse_name,
    MIN(u.name) as pic_name,
    AVG(CASE WHEN psb.hpp_status = 'SETTLED' THEN psb.final_hpp ELSE psb.estimated_hpp END) as hpp_packing
FROM t_packaging as pkg
LEFT JOIN t_productions as p ON pkg.production_id = p.id
LEFT JOIN m_products as bp ON p.bundle_product_id = bp.id
LEFT JOIN m_warehouses as w ON p.warehouse_id = w.id
LEFT JOIN users as u ON p.pic_id = u.id
WHERE p.company_code = 'YOUR_COMPANY_CODE'
  AND p.is_packaging = true
GROUP BY pkg.transaction_number
ORDER BY pkg.transaction_number DESC
```

## Notes

- `GROUP_CONCAT` has a default limit (usually 1024 bytes in MySQL)
  - If grouping many packings, consider increasing `group_concat_max_len`
  - Or implement pagination at detail page level

- Badge color: `bg-label-primary` provides good visibility
  - Consider different colors for different statuses if needed

- Transaction number format: Currently `PROD-YYYYMM-XXX`
  - Ensure consistent format for proper sorting and grouping
