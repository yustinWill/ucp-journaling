# Sales Order - Shipping Cost Field DDL

## Purpose
Add `shipping_cost` column to `t_sales_orders` table to track shipping costs paid by the seller (not included in invoice).

## DDL Query

```sql
-- Add shipping_cost column to t_sales_orders table
ALTER TABLE `t_sales_orders`
ADD COLUMN `shipping_cost` DECIMAL(15,2) NOT NULL DEFAULT 0
AFTER `warehouse_id`
COMMENT 'Biaya ongkos kirim yang dibayar seller (tidak termasuk dalam invoice)';
```

## Verification Query

After running the DDL, verify the column was added correctly:

```sql
-- Check column exists and has correct properties
DESCRIBE t_sales_orders;

-- Or use this for more details
SHOW FULL COLUMNS FROM t_sales_orders WHERE Field = 'shipping_cost';
```

## Rollback Query

If you need to remove the column:

```sql
-- Remove shipping_cost column (CAUTION: This will delete data)
ALTER TABLE `t_sales_orders`
DROP COLUMN `shipping_cost`;
```

## Field Specifications

| Property | Value |
|----------|-------|
| **Column Name** | `shipping_cost` |
| **Data Type** | `DECIMAL(15,2)` |
| **Nullable** | `NOT NULL` |
| **Default Value** | `0` |
| **Position** | After `warehouse_id` |
| **Comment** | Biaya ongkos kirim yang dibayar seller (tidak termasuk dalam invoice) |

## Business Rules

1. **Purpose**: Track shipping costs that are paid by the seller
2. **Invoice Inclusion**: This cost is NOT included in the customer invoice
3. **Optional Field**: Can be left as 0 if no shipping cost applies
4. **Minimum Value**: Cannot be negative (validation in application layer)
5. **Precision**: Supports up to 15 digits total, with 2 decimal places

## Usage Examples

### Insert with shipping cost
```sql
INSERT INTO t_sales_orders (
    reference_no,
    date,
    customer_id,
    warehouse_id,
    shipping_cost,
    grand_total
) VALUES (
    'SO-2025-001',
    '2025-12-14',
    1,
    1,
    50000.00,  -- Shipping cost: Rp 50,000
    1000000.00
);
```

### Update shipping cost
```sql
UPDATE t_sales_orders
SET shipping_cost = 75000.00
WHERE reference_no = 'SO-2025-001';
```

### Query with shipping cost
```sql
SELECT
    reference_no,
    date,
    grand_total,
    shipping_cost,
    (grand_total + shipping_cost) as total_with_shipping
FROM t_sales_orders
WHERE date >= '2025-01-01'
ORDER BY date DESC;
```

### Get total shipping costs per month
```sql
SELECT
    DATE_FORMAT(date, '%Y-%m') as month,
    COUNT(*) as order_count,
    SUM(shipping_cost) as total_shipping_cost,
    AVG(shipping_cost) as avg_shipping_cost
FROM t_sales_orders
WHERE shipping_cost > 0
GROUP BY DATE_FORMAT(date, '%Y-%m')
ORDER BY month DESC;
```

## Migration File

Laravel migration file has been created at:
`database/migrations/2025_12_14_000000_add_shipping_cost_to_sales_orders_table.php`

To run the migration:
```bash
php artisan migrate
```

To rollback:
```bash
php artisan migrate:rollback --step=1
```

## Updated Files

### Views:
1. `resources/views/pages/main_transaction/sales_order/add.blade.php`
   - Added shipping cost input field (left column)
   - Moved description to right column

2. `resources/views/pages/main_transaction/sales_order/edit.blade.php`
   - Added shipping cost input field (left column)
   - Moved description to right column

3. `resources/views/pages/main_transaction/sales_order/detail.blade.php`
   - Added shipping cost display field (left column)
   - Moved description to right column

### Controller:
4. `app/Http/Controllers/MainTransaction/SalesOrderController.php`
   - Added `shipping_cost` validation in `store()` method
   - Added `shipping_cost` validation in `update()` method
   - Added field name translation for validation messages

### Database:
5. `database/migrations/2025_12_14_000000_add_shipping_cost_to_sales_orders_table.php`
   - Migration to add `shipping_cost` column

## Testing Checklist

- [ ] Run the DDL query manually
- [ ] Verify column exists with correct data type
- [ ] Test creating new sales order with shipping cost
- [ ] Test creating new sales order without shipping cost (should default to 0)
- [ ] Test editing existing sales order and adding shipping cost
- [ ] Test detail view displays shipping cost correctly
- [ ] Test validation rejects negative shipping costs
- [ ] Test existing sales orders still work (should show 0 for old records)

## Notes

- The field is optional (defaults to 0)
- Validation allows NULL or numeric values >= 0
- Frontend displays as currency format (Rp)
- Backend stores as decimal with 2 decimal places
- This cost is for seller's tracking purposes only, not shown to customer in invoice
