# Packing Store Method Refactor - Summary

## Overview
Refactored the PackingController's `store()` method to follow the same pattern as ProductionController, with proper validation, HPP calculation, stock movements, and the t_packaging mapping table insert.

## Changes Made

### 1. Created CreatePackingAction Class

**File**: [app/Domains/Production/Actions/CreatePackingAction.php](app/Domains/Production/Actions/CreatePackingAction.php)

Similar to `CreateProductionAction`, this action class handles the complete packing creation flow:

#### Main Flow (`execute` method):
1. **Validate Stock Availability**
   - Check bundle stock is sufficient
   - Check component stock for each packaging product
   - Throw exception if insufficient

2. **Calculate Totals and HPP**
   - Calculate bundle cost using FIFO from `t_product_stock_batches`
   - Calculate component costs using FIFO
   - Calculate total cost and HPP per unit
   - Returns: `bundle_price`, `hpp_production`, `total_production_cost`

3. **Create Production Record**
   - Insert into `t_productions` with `is_packaging = true`
   - Status automatically set to `'COMPLETED'`

4. **Create Production Items**
   - Insert packaging products into `t_production_items`
   - These are the products being produced (output)

5. **Process Stock Movements**
   - **Deduct** bundle stock (consumed)
   - **Deduct** component stock (consumed)
   - **Add** packaging product stock (produced)
   - Uses `ProductionStockService::createProductionStockMovements()`

6. **Create t_packaging Mappings**
   - Insert records into `t_packaging` table
   - Maps production_id → packaging_product_id → bundle_product_id
   - Stores quantity_packed for each packaging product

7. **Create Audit Log**
   - Log creation event with all details

### 2. Stock Validation (`validateStockAvailability` method)

```php
// Check bundle stock
$availableStock = DB::table('t_stock_movement_logs')
    ->where('product_id', $bundleProductId)
    ->where('warehouse_id', $warehouseId)
    ->sum(IN movements - OUT movements);

if ($availableStock < $totalBundleNeeded) {
    throw AppException(INSUFFICIENT_STOCK);
}

// Check each component stock
foreach (packaging products) {
    foreach (components) {
        validate component stock >= needed qty
    }
}
```

### 3. HPP Calculation (`calculateTotals` method)

**Bundle Cost Calculation**:
```php
$batches = DB::table('t_product_stock_batches')
    ->where('f_product_id', $bundleProductId)
    ->where('status', 'ACTIVE')
    ->where('remaining_qty', '>', 0)
    ->orderBy('batch_date', 'ASC')  // FIFO
    ->get();

foreach ($batches as $batch) {
    $qtyToTake = min($batch->remaining_qty, $remainingNeed);
    $hppPerUnit = $batch->estimated_hpp ?? $batch->purchase_price ?? 0;
    $totalBundleCost += ($qtyToTake * $hppPerUnit);
}
```

**Component Cost Calculation**:
- Same FIFO pattern for each component
- Sum all component costs

**Final Calculation**:
```php
$totalCost = $totalBundleCost + $totalComponentCost;
$hppPerUnit = $totalQuantityPacked > 0 ? ($totalCost / $totalQuantityPacked) : 0;

return [
    'bundle_price' => $totalBundleCost,
    'hpp_production' => $hppPerUnit,
    'total_production_cost' => $totalCost,
    'total_raw_material_cost' => $totalCost,
    // ...
];
```

### 4. Stock Movements (`processStockMovements` method)

**Materials Consumed** (deducted from stock):
1. Bundle product (total qty needed across all packaging)
2. All components (accumulated across all packaging products)

**Products Produced** (added to stock):
- Each packaging product with its quantity

The `ProductionStockService` handles:
- Creating stock batches for produced packaging products
- Deducting from source batches (bundle + components)
- Recording in `t_stock_movement_logs`

### 5. Updated PackingController Store Method

**Before**: Direct DB inserts without validation or HPP calculation

**After**: Uses CreatePackingAction with full validation

```php
public function store(Request $request)
{
    try {
        $data = $request->all();

        // Decode and filter packaging products
        $packagingProducts = json_decode($request->input('packaging_products'), true);
        $data['packaging_products'] = array_filter($packagingProducts,
            fn($p) => $p['quantity_packed'] > 0
        );

        // Validate
        $createAction = app(CreatePackingAction::class);
        $validation = $createAction->validatePackingData($data);

        if (!$validation['is_valid']) {
            return response()->json(['errors' => $validation['errors']], 422);
        }

        // Calculate bundle qty needed (total)
        foreach ($data['packaging_products'] as &$packaging) {
            $packaging['bundle_qty_needed'] =
                $packaging['quantity_packed'] * $packaging['bundle_qty_needed'];
        }

        // Execute (handles everything)
        $productionId = $createAction->execute($data);

        return response()->json([
            'success' => true,
            'production_id' => $productionId
        ]);

    } catch (AppException $e) {
        return response()->json(['message' => $e->getMessage()], $e->getCode());
    }
}
```

### 6. Data Validation (`validatePackingData` method)

**Required Fields**:
- `production_date`
- `production_number`
- `bundle_product_id`
- `warehouse_id`
- `pic_id`

**Business Rules**:
- At least one packaging product with qty > 0
- Production number must be unique
- Production date cannot be in future
- Bundle product must exist, be active, and be type 'bundle'
- Warehouse must exist and be active

**Returns**:
```php
[
    'is_valid' => true/false,
    'errors' => ['error 1', 'error 2', ...]
]
```

## Database Tables Updated

### t_productions
- Main packing record
- `is_packaging = true`
- `status = 'COMPLETED'`
- Includes calculated HPP and costs

### t_production_items
- Lists packaging products produced (output)
- One row per packaging product

### t_packaging (Mapping Table)
- Links production to packaging details
- Fields:
  - `production_id`
  - `bundle_product_id` (source bundle)
  - `packaging_product_id` (produced packaging)
  - `quantity_packed`

### t_product_stock_batches
- New batches created for produced packaging products
- Source batches (bundle + components) have `remaining_qty` reduced

### t_stock_movement_logs
- Records all stock movements:
  - Bundle OUT (consumed)
  - Components OUT (consumed)
  - Packaging products IN (produced)

### t_production_audit_logs
- Audit trail of packing creation

## HPP Flow Example

**Scenario**: Pack 100 cookies into:
- 10 × Small Pack (6 cookies each) = 60 cookies
- 5 × Medium Pack (9 cookies each) = 45 cookies
- Total: 105 cookies needed, 15 packs produced

**Bundle Cost**:
```
Batch 1: 50 cookies @ Rp 1,000 = Rp 50,000
Batch 2: 55 cookies @ Rp 1,200 = Rp 66,000 (only use 55)
Total Bundle Cost = Rp 116,000
```

**Component Cost** (e.g., Plastic Wrap):
```
Small Pack: 10 × 1 wrap = 10 wraps @ Rp 500 = Rp 5,000
Medium Pack: 5 × 1 wrap = 5 wraps @ Rp 500 = Rp 2,500
Total Component Cost = Rp 7,500
```

**Total Cost & HPP**:
```
Total Cost = Rp 116,000 + Rp 7,500 = Rp 123,500
Total Packs Produced = 15
HPP per Pack = Rp 123,500 / 15 = Rp 8,233.33
```

**Database Records**:
```sql
-- t_productions
bundle_price = 116000
total_production_cost = 123500
hpp_production = 8233.33

-- t_product_stock_batches (for each packaging product)
Small Pack: 10 packs @ Rp 8,233.33 estimated_hpp
Medium Pack: 5 packs @ Rp 8,233.33 estimated_hpp
```

## Key Differences from Production

| Feature | Production | Packing |
|---------|-----------|---------|
| **Input** | Raw materials | Bundle + Components |
| **Output** | Bundle product | Packaging products |
| **Multiple Products** | No (1 bundle) | Yes (multiple packaging) |
| **Material Modes** | Qty-based / Material-based | Always material-based |
| **Expenses** | Yes | No |
| **HPP Calculation** | Materials + Expenses | Materials only |
| **Mapping Table** | No | Yes (`t_packaging`) |

## Error Handling

### Validation Errors (422)
- Missing required fields
- Invalid data types
- Business rule violations

### Stock Errors (400)
- Insufficient bundle stock
- Insufficient component stock
- Specific product and quantity shown in error

### System Errors (500)
- Database errors
- Unexpected exceptions
- Logged to Discord (if configured)

## Testing Checklist

- [ ] Validate required fields
- [ ] Validate unique production number
- [ ] Reject future production dates
- [ ] Check bundle stock availability
- [ ] Check component stock availability
- [ ] Calculate bundle cost correctly (FIFO)
- [ ] Calculate component cost correctly (FIFO)
- [ ] Calculate total cost and HPP
- [ ] Create production record with correct data
- [ ] Create production items for packaging products
- [ ] Process stock movements (deduct bundle & components, add packaging)
- [ ] Insert t_packaging mapping records
- [ ] Create audit log
- [ ] Filter out qty = 0 packaging products
- [ ] Handle validation errors gracefully
- [ ] Handle stock errors gracefully
- [ ] Handle system errors gracefully

## Migration Path

If you have existing packing records created with the old method, they:
- ✅ Will still display in index (is_packaging = true filter)
- ❌ May not have correct HPP calculated
- ❌ May not have stock movements recorded
- ❌ May not have t_packaging mappings

**Recommendation**: Use the new method for all future packing operations. Consider creating a migration script to recalculate HPP for existing records if needed.

## Files Created/Modified

### Created:
1. **app/Domains/Production/Actions/CreatePackingAction.php**
   - Complete action class for packing creation
   - ~400 lines

### Modified:
2. **app/Http/Controllers/MainTransaction/PackingController.php**
   - Updated `store()` method to use CreatePackingAction
   - Removed manual DB inserts
   - Added proper validation and error handling

## Benefits

✅ **Accurate HPP** - FIFO-based cost calculation
✅ **Stock Integrity** - Proper deduction and addition
✅ **Validation** - Comprehensive data validation
✅ **Error Handling** - Clear error messages
✅ **Audit Trail** - Complete audit logging
✅ **Consistency** - Follows production module pattern
✅ **Maintainable** - Centralized logic in action class
✅ **Testable** - Action class can be unit tested
