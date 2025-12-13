# Packing Module Implementation

## Overview

The Packing module uses the **same database tables** as Production (`t_productions`, `t_production_items`) but is distinguished by the `is_packaging` flag. This approach allows us to reuse existing infrastructure while maintaining a separate business flow for packing operations.

## Database Structure

### New Flags Added

#### 1. `t_productions.is_packaging` (BOOLEAN)
- **Purpose**: Distinguishes packing productions from regular productions
- **Default**: `false`
- **Location**: After `status` column
- **Comment**: "Flag to distinguish packing production from regular production"

#### 2. `m_products.is_packaging` (BOOLEAN)
- **Purpose**: Marks which bundle products can be used for packing
- **Default**: `false`
- **Location**: After `is_bundle` column
- **Comment**: "Flag to mark products that are used for packing (subset of bundle products)"

### Migration Files

1. `database/migrations/2025_12_12_231816_add_is_packaging_to_productions_table.php`
2. `database/migrations/2025_12_12_231913_add_is_packaging_to_products_table.php`

### Running Migrations

```bash
# Option 1: Run Laravel migrations
php artisan migrate

# Option 2: Run SQL manually
# File: database/sql/add_is_packaging_flags.sql
```

## How It Works

### Product Level

**Bundle Products** can be marked as packaging products:
- `is_bundle = true` AND `is_packaging = true` = Packaging product
- `is_bundle = true` AND `is_packaging = false` = Regular production product

### Production Level

Productions are filtered by `is_packaging` flag:
- **Regular Production**: `is_packaging = false` (shown in Production module)
- **Packing Production**: `is_packaging = true` (shown in Packing module)

## Code Implementation

### PackingController Changes

#### 1. Index Method (Filter Products)
```php
// Get only bundle products marked as packaging products
$bundleProducts = DB::table('m_products')
    ->where('company_code', session('user_company_code'))
    ->where('is_bundle', true)
    ->where('is_packaging', true)  // Only packaging products
    ->where('status', 'ACTIVE')
    ->select('id', 'code', 'name')
    ->get();
```

#### 2. DataTable Query (Filter Productions)
```php
$query = DB::table('t_productions as p')
    ->where('p.company_code', session('user_company_code'))
    ->where('p.is_packaging', true);  // Only packing productions
```

#### 3. Get Bundle Products (AJAX)
```php
$products = DB::table('m_products')
    ->where('company_code', session('user_company_code'))
    ->where('is_bundle', true)
    ->where('is_packaging', true)  // Only packaging products
    ->where('status', 'ACTIVE')
    ->select('id', 'code', 'name', 'description')
    ->get();
```

#### 4. Generate Transaction Number
```php
$lastPacking = DB::table('t_productions')
    ->where('company_code', session('user_company_code'))
    ->where('is_packaging', true)  // Only packing productions
    ->where('production_number', 'LIKE', "PACK-{$year}{$month}-%")
    ->orderBy('production_number', 'DESC')
    ->first();
```

## Workflow

### Creating a Packing Product

1. **Mark Product as Packing Product**:
```sql
UPDATE m_products
SET is_packaging = 1
WHERE id = YOUR_PRODUCT_ID
  AND is_bundle = 1;
```

2. **Verify**:
```sql
SELECT id, code, name, is_bundle, is_packaging
FROM m_products
WHERE is_packaging = 1;
```

### Creating a Packing Production

When creating a packing production, the system will:
1. Use the same `t_productions` table
2. Set `is_packaging = true`
3. Use transaction number format: `PACK-YYYYMM-NNNN`
4. Store in `t_production_items` (same as regular production)
5. Create stock batches with `source_type = 'PRODUCTION'`

### Data Separation

| Module | Table | Filter |
|--------|-------|--------|
| Production | `t_productions` | `is_packaging = false` |
| Packing | `t_productions` | `is_packaging = true` |

## Benefits of This Approach

1. **Reuse Existing Infrastructure**
   - Same tables, services, actions
   - HPP calculation logic already in place
   - Stock batch management already working

2. **Clear Separation**
   - Different menus in UI
   - Different permissions (`production.*` vs `packing.*`)
   - Different transaction number prefixes (`PROD-*` vs `PACK-*`)

3. **Consistent Data Model**
   - All production-like transactions in one place
   - Easier reporting and analytics
   - Simpler database maintenance

## Important Notes

1. **Products must be marked correctly**:
   - Regular production products: `is_packaging = false`
   - Packing products: `is_packaging = true`
   - A product can be used for both if needed (set both flags to `true`)

2. **Transaction Numbers**:
   - Production: `PROD-YYYYMM-NNNN`
   - Packing: `PACK-YYYYMM-NNNN`

3. **Permissions**:
   - Production module: `production.read`, `production.write`, etc.
   - Packing module: `packing.read`, `packing.write`, etc.

4. **Stock Batches**:
   - Both use `source_type = 'PRODUCTION'`
   - Can be differentiated by checking `t_productions.is_packaging`

## Next Steps

1. ✅ Database migrations created
2. ✅ PackingController updated with filters
3. ✅ Routes registered
4. ✅ Menu item added
5. ⏳ Run migrations to add flags
6. ⏳ Mark products as packing products
7. ⏳ Create packing add/detail views
8. ⏳ Implement full CRUD functionality

## SQL Quick Reference

### Add Flags Manually
```sql
-- Add to productions table
ALTER TABLE t_productions
ADD COLUMN is_packaging TINYINT(1) NOT NULL DEFAULT 0
AFTER status;

-- Add to products table
ALTER TABLE m_products
ADD COLUMN is_packaging TINYINT(1) NOT NULL DEFAULT 0
AFTER is_bundle;
```

### Mark Products as Packing Products
```sql
-- Mark specific products
UPDATE m_products
SET is_packaging = 1
WHERE id IN (1, 2, 3);

-- Mark all bundle products as packing products
UPDATE m_products
SET is_packaging = 1
WHERE is_bundle = 1;
```

### Query Examples
```sql
-- Get all packing productions
SELECT * FROM t_productions WHERE is_packaging = 1;

-- Get all regular productions
SELECT * FROM t_productions WHERE is_packaging = 0;

-- Get all packing products
SELECT * FROM m_products WHERE is_packaging = 1;

-- Get production counts by type
SELECT
    is_packaging,
    COUNT(*) as total,
    SUM(quantity_produced) as total_qty
FROM t_productions
GROUP BY is_packaging;
```
