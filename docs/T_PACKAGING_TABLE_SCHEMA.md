# t_packaging Table Schema

## Purpose
The `t_packaging` table serves as a mapping/detail table that links packing production records to the specific packaging products produced. It tracks which bundle products were unpacked and which packaging products were created.

## Table Structure

### Columns

| Column | Type | Nullable | Description |
|--------|------|----------|-------------|
| `id` | BIGINT UNSIGNED | NO | Primary key, auto-increment |
| `company_code` | VARCHAR(10) | NO | Company identifier for multi-tenant support |
| `production_id` | BIGINT UNSIGNED | NO | Foreign key to `t_productions` table (where `is_packaging = true`) |
| `bundle_product_id` | BIGINT UNSIGNED | NO | Foreign key to `m_products` - the bundle product being consumed/unpacked |
| `packaging_product_id` | BIGINT UNSIGNED | NO | Foreign key to `m_products` - the packaging product being produced (where `is_packaging = true`) |
| `quantity_packed` | DECIMAL(15,4) | NO | Quantity of packaging product produced |
| `created_at` | TIMESTAMP | YES | Record creation timestamp |
| `updated_at` | TIMESTAMP | YES | Record last update timestamp |

### Indexes

| Index Name | Columns | Type | Purpose |
|------------|---------|------|---------|
| PRIMARY | `id` | Primary Key | Unique identifier |
| `idx_packaging_company_code` | `company_code` | Index | Fast filtering by company |
| `idx_packaging_production_id` | `production_id` | Index | Fast lookups by production |
| `idx_packaging_bundle_product_id` | `bundle_product_id` | Index | Fast lookups by bundle product |
| `idx_packaging_packaging_product_id` | `packaging_product_id` | Index | Fast lookups by packaging product |

### Foreign Keys

| Constraint Name | Column | References | On Delete |
|----------------|--------|------------|-----------|
| `fk_packaging_production_id` | `production_id` | `t_productions.id` | CASCADE |
| `fk_packaging_bundle_product_id` | `bundle_product_id` | `m_products.id` | RESTRICT |
| `fk_packaging_packaging_product_id` | `packaging_product_id` | `m_products.id` | RESTRICT |

## Relationships

```
t_packaging
├── belongs to t_productions (production_id)
├── belongs to m_products as bundle (bundle_product_id)
└── belongs to m_products as packaging (packaging_product_id)
```

## Usage Example

### Scenario
A packing operation produces:
- 10 × Small Pack (6 cookies each)
- 5 × Medium Pack (9 cookies each)

From:
- Cookie Box (bundle product)

### Database Records

**t_productions** (1 record):
```sql
id: 1001
production_number: 'PACK-202512-0001'
bundle_product_id: 5 (Cookie Box)
warehouse_id: 1
quantity_planned: 15 (total packs produced)
is_packaging: true
status: 'COMPLETED'
```

**t_production_items** (2 records):
```sql
-- Item 1
production_id: 1001
product_id: 201 (Small Pack)
quantity: 10

-- Item 2
production_id: 1001
product_id: 202 (Medium Pack)
quantity: 5
```

**t_packaging** (2 records):
```sql
-- Mapping 1
id: 1
production_id: 1001
bundle_product_id: 5 (Cookie Box)
packaging_product_id: 201 (Small Pack)
quantity_packed: 10

-- Mapping 2
id: 2
production_id: 1001
bundle_product_id: 5 (Cookie Box)
packaging_product_id: 202 (Medium Pack)
quantity_packed: 5
```

## Query Examples

### Get all packaging products for a specific production
```sql
SELECT
    t.id,
    t.quantity_packed,
    p.code as packaging_code,
    p.name as packaging_name,
    b.code as bundle_code,
    b.name as bundle_name
FROM t_packaging t
INNER JOIN m_products p ON t.packaging_product_id = p.id
INNER JOIN m_products b ON t.bundle_product_id = b.id
WHERE t.production_id = 1001;
```

### Get packing history for a specific bundle product
```sql
SELECT
    t.id,
    prod.production_number,
    prod.production_date,
    p.code as packaging_code,
    p.name as packaging_name,
    t.quantity_packed
FROM t_packaging t
INNER JOIN t_productions prod ON t.production_id = prod.id
INNER JOIN m_products p ON t.packaging_product_id = p.id
WHERE t.bundle_product_id = 5
ORDER BY prod.production_date DESC;
```

### Get packing production summary by packaging product
```sql
SELECT
    p.code,
    p.name,
    COUNT(t.id) as packing_count,
    SUM(t.quantity_packed) as total_quantity_packed
FROM t_packaging t
INNER JOIN m_products p ON t.packaging_product_id = p.id
WHERE t.company_code = '001'
GROUP BY p.id, p.code, p.name
ORDER BY total_quantity_packed DESC;
```

### Get complete packing production details
```sql
SELECT
    prod.production_number,
    prod.production_date,
    prod.quantity_planned as total_packs,
    b.code as bundle_code,
    b.name as bundle_name,
    p.code as packaging_code,
    p.name as packaging_name,
    t.quantity_packed,
    w.name as warehouse_name,
    u.name as pic_name
FROM t_packaging t
INNER JOIN t_productions prod ON t.production_id = prod.id
INNER JOIN m_products b ON t.bundle_product_id = b.id
INNER JOIN m_products p ON t.packaging_product_id = p.id
INNER JOIN m_warehouses w ON prod.warehouse_id = w.id
INNER JOIN users u ON prod.pic_id = u.id
WHERE prod.is_packaging = true
  AND t.company_code = '001'
ORDER BY prod.production_date DESC, prod.id DESC;
```

## Data Integrity Rules

### Validation Rules
1. **Unique Constraint**: None explicitly defined, allows multiple packaging products per production
2. **Quantity**: Must be > 0 (validated in application layer)
3. **Company Code**: Must match production record's company_code
4. **Product Types**:
   - `bundle_product_id` should reference a product with `type = 'bundle'`
   - `packaging_product_id` should reference a product with `is_packaging = true` and `type = 'bundle'`

### Cascade Behavior
- **ON DELETE CASCADE**: When a production record is deleted, all related packaging records are automatically deleted
- **ON DELETE RESTRICT**: Cannot delete products that are referenced in packaging records

## Migration Commands

### Run Migration
```bash
php artisan migrate
```

### Check Migration Status
```bash
php artisan migrate:status
```

### Rollback Migration
```bash
php artisan migrate:rollback
```

### Fresh Migration (WARNING: Deletes all data)
```bash
php artisan migrate:fresh
```

## Table Size Estimation

Assuming:
- Average 3 packaging products per packing production
- 100 packing productions per month
- 12 months

**Estimated rows per year**: 3 × 100 × 12 = **3,600 rows/year**

**Row size**:
- Fixed columns: ~60 bytes
- Indexes: ~40 bytes per index × 4 = 160 bytes
- Total per row: ~220 bytes

**Storage per year**: 3,600 × 220 bytes ≈ **0.8 MB/year**

Very lightweight table, no performance concerns expected.

## Security Considerations

1. **Multi-tenancy**: Always filter by `company_code` in queries
2. **Soft Delete**: Consider adding `deleted_at` column if business requires audit trail
3. **Audit Trail**: Related production already logged in `t_production_audit_logs`

## Related Tables

1. **t_productions** - Parent production record
2. **m_products** - Product master data (both bundle and packaging)
3. **t_production_items** - Lists packaging products produced
4. **t_product_stock_batches** - Stock batches created for packaging products
5. **t_stock_movement_logs** - Stock movement records

## Future Enhancements

Potential future columns to consider:
- `deleted_at` - Soft delete support
- `notes` - Additional notes per packaging product
- `unit_price` - Store unit price at time of packing
- `total_cost` - Store total cost for this specific packaging
- `batch_reference` - Link to specific stock batch created

## Troubleshooting

### Common Issues

**Issue**: Foreign key constraint fails when inserting
```
SQLSTATE[23000]: Integrity constraint violation: 1452 Cannot add or update a child row
```
**Solution**: Verify that `production_id`, `bundle_product_id`, and `packaging_product_id` all exist in their respective tables

**Issue**: Cannot delete product
```
SQLSTATE[23000]: Integrity constraint violation: 1451 Cannot delete or update a parent row
```
**Solution**: Product is referenced in `t_packaging`. Delete packing records first, or change to soft delete

## Testing

### Test Data Insert
```sql
INSERT INTO t_packaging (
    company_code,
    production_id,
    bundle_product_id,
    packaging_product_id,
    quantity_packed,
    created_at,
    updated_at
) VALUES (
    '001',
    1001,
    5,
    201,
    10.0000,
    NOW(),
    NOW()
);
```

### Verify Insert
```sql
SELECT * FROM t_packaging WHERE production_id = 1001;
```

### Test Foreign Keys
```sql
-- This should fail (invalid production_id)
INSERT INTO t_packaging VALUES (NULL, '001', 99999, 5, 201, 10, NOW(), NOW());

-- This should fail (invalid bundle_product_id)
INSERT INTO t_packaging VALUES (NULL, '001', 1001, 99999, 201, 10, NOW(), NOW());
```
