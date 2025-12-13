# Packing Module - Refactored Flow

## Concept Clarification

### Previous (WRONG) Understanding:
- 1 packing operation = 1 production record
- Multiple packaging products stored in that single production

### Correct Understanding:
- **1 packing operation = MULTIPLE production records (one per packaging product line)**
- **Each packaging product = 1 separate production record**
- The bundle product selected in UI = **component/material consumed**
- Each packaging product = **the bundle product being produced**

## Example

### User Input:
- Source Bundle: Cookie Box (ID: 5)
- Packaging Products:
  - 10 × Small Pack (6 cookies each) → needs 60 cookies from Cookie Box
  - 5 × Medium Pack (9 cookies each) → needs 45 cookies from Cookie Box

### Database Records Created:

#### Production Record 1 (Small Pack)
**t_productions**:
```
id: 1001
production_number: 'PACK-202512-001-001'
bundle_product_id: 201 (Small Pack) ← THIS is the product being PRODUCED
warehouse_id: 1
quantity_planned: 10
quantity_produced: 10
is_packaging: true
status: 'COMPLETED'
hpp_production: 8233.33
```

**t_production_items** (materials consumed):
```
production_id: 1001
raw_material_product_id: 5 (Cookie Box) ← consumed material
quantity_consumed: 60
```

**t_packaging** (mapping):
```
production_id: 1001
bundle_product_id: 5 (Cookie Box) ← source bundle consumed
packaging_product_id: 201 (Small Pack) ← packaging product produced
quantity_packed: 10
```

#### Production Record 2 (Medium Pack)
**t_productions**:
```
id: 1002
production_number: 'PACK-202512-001-002'
bundle_product_id: 202 (Medium Pack) ← THIS is the product being PRODUCED
warehouse_id: 1
quantity_planned: 5
quantity_produced: 5
is_packaging: true
status: 'COMPLETED'
hpp_production: 8233.33
```

**t_production_items** (materials consumed):
```
production_id: 1002
raw_material_product_id: 5 (Cookie Box) ← consumed material
quantity_consumed: 45
```

**t_packaging** (mapping):
```
production_id: 1002
bundle_product_id: 5 (Cookie Box) ← source bundle consumed
packaging_product_id: 202 (Medium Pack) ← packaging product produced
quantity_packed: 5
```

## Data Flow

### 1. Single Packing Form Submission
User enters:
- Production Number: `PACK-202512-001`
- Production Date: `2025-12-13`
- Source Bundle: Cookie Box (ID: 5)
- Warehouse: Main Warehouse (ID: 1)
- PIC: John Doe (ID: 10)
- Packaging Products:
  - Small Pack (ID: 201): Qty = 10, Bundle Qty Needed = 60
  - Medium Pack (ID: 202): Qty = 5, Bundle Qty Needed = 45

### 2. CreatePackingAction Processing

For **EACH** packaging product line:

#### Step 1: Calculate HPP for Single Packaging
```php
calculateTotalsForSinglePackaging($data, $packaging)
```
- Source bundle cost (FIFO from t_product_stock_batches)
- Other components cost (FIFO)
- Total cost / quantity packed = HPP per unit

#### Step 2: Create Production Record
```php
$productionData = [
    'production_number' => 'PACK-202512-001-001', // Suffixed with line number
    'bundle_product_id' => $packaging['packaging_product_id'], // Product being produced!
    'quantity_produced' => $packaging['quantity_packed'],
    'is_packaging' => true,
    'hpp_production' => $calculatedHpp,
    // ...
];
```

#### Step 3: Create Production Items (Materials Consumed)
```php
createProductionItemsForSinglePackaging($productionId, $sourceBundleId, $packaging)
```
- Source bundle as `raw_material_product_id`
- Other components as `raw_material_product_id`

#### Step 4: Process Stock Movements
```php
processStockMovementsForSinglePackaging($productionId, $productionData, $sourceBundleId, $packaging)
```
- **Deduct**: Source bundle stock
- **Deduct**: Other components stock
- **Add**: Packaging product stock (via ProductionStockService)

#### Step 5: Create t_packaging Mapping
```php
createPackagingMapping($productionId, $sourceBundleId, $packaging)
```
- Maps: production_id → bundle_product_id (consumed) → packaging_product_id (produced)

#### Step 6: Create Audit Log
- Logs the creation event for this specific packaging product

### 3. Controller Response
```json
{
  "success": true,
  "message": "Data packing berhasil disimpan. 2 production record(s) created.",
  "production_ids": [1001, 1002],
  "count": 2
}
```

## Table Structure Summary

### t_productions (one record per packaging product)
- `bundle_product_id` = packaging product being produced
- `is_packaging` = true
- `quantity_produced` = quantity of packaging product produced
- `hpp_production` = HPP of packaging product

### t_production_items (materials consumed per production)
- `raw_material_product_id` = source bundle OR other components
- Tracks what was consumed to produce the packaging product

### t_packaging (mapping table)
- `production_id` = references t_productions
- `bundle_product_id` = source bundle that was consumed
- `packaging_product_id` = packaging product that was produced
- `quantity_packed` = quantity produced

### t_product_stock_batches
- New batches created for each packaging product produced
- Source bundle batches have `remaining_qty` reduced
- Component batches have `remaining_qty` reduced

### t_stock_movement_logs
- One PRODUCTION_OUT per consumed material (source bundle + components)
- One PRODUCTION_IN per produced packaging product

## Key Differences from Regular Production

| Aspect | Regular Production | Packing Production |
|--------|-------------------|-------------------|
| **Records Created** | 1 production per operation | Multiple productions (one per packaging product) |
| **bundle_product_id** | Single bundle being produced | Each packaging product |
| **Raw Materials** | Components from formula | Source bundle + other components |
| **Production Number** | Single number | Base number + suffix (-001, -002, etc.) |
| **Mapping Table** | No | Yes (t_packaging) |

## Benefits of This Approach

1. **Consistency**: Each production record represents ONE product being produced
2. **HPP Accuracy**: Each packaging product gets its own HPP calculation
3. **Stock Tracking**: Clear stock movements per packaging product
4. **Reporting**: Easy to query production by packaging product type
5. **Reversal**: Can reverse individual packaging products if needed
6. **Audit**: Clear audit trail per packaging product

## Migration Notes

If you have existing packing records created with the old approach (single production, multiple packaging), they will still display but won't follow this structure. Consider them archived and use the new approach going forward.
