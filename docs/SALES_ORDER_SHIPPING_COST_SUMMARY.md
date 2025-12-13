# Sales Order Shipping Cost Feature - Implementation Summary

## Overview
Added "Harga Ongkos Kirim" (Shipping Cost) field to Sales Order module, reorganized the form layout to match the design specification.

## Layout Changes

### Before:
```
Row 1: [Syarat Pembayaran] [               ]
Row 2: [Jumlah Hari] [Tanggal Jatuh Tempo]
Row 3: [Sales Person] [Gudang]
Row 4: [Keterangan] [                     ]
```

### After:
```
Row 1: [Syarat Pembayaran] [               ]
Row 2: [Jumlah Hari] [Tanggal Jatuh Tempo]
Row 3: [Sales Person] [Gudang]
Row 4: [Harga Ongkos Kirim] [Keterangan]
```

## Files Modified

### 1. Views (Frontend)

#### add.blade.php
**File**: `resources/views/pages/main_transaction/sales_order/add.blade.php`

**Changes**:
- Added "Harga Ongkos Kirim" input field (left column)
- Moved "Keterangan" textarea to right column
- Added currency prefix "Rp" to shipping cost input
- Added help text: "Biaya ongkir yang dibayar seller (tidak termasuk di invoice)"

**Code**:
```html
<!-- Row 3: Harga Ongkos Kirim, Keterangan -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label class="form-label">Harga Ongkos Kirim <span class="text-muted">(Opsional)</span></label>
        <div class="input-group">
            <span class="input-group-text">Rp</span>
            <input type="number" class="form-control" id="sales-order-shipping-cost" name="shipping_cost"
                placeholder="0" min="0" step="0.01" value="0" />
        </div>
        <small class="form-text text-muted">Biaya ongkir yang dibayar seller (tidak termasuk di invoice)</small>
    </div>
    <div class="col-md-6">
        <label class="form-label">Keterangan</label>
        <textarea class="form-control" id="sales-order-description" name="description" rows="2"
            placeholder="Nomor order transaksi..." maxlength="1000"></textarea>
    </div>
</div>
```

#### edit.blade.php
**File**: `resources/views/pages/main_transaction/sales_order/edit.blade.php`

**Changes**:
- Added "Harga Ongkos Kirim" input field with value from `$salesOrder['shipping_cost']`
- Moved "Keterangan" textarea to right column
- Same layout as add.blade.php

**Code**:
```html
<!-- Row 3: Harga Ongkos Kirim, Keterangan -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label class="form-label">Harga Ongkos Kirim <span class="text-muted">(Opsional)</span></label>
        <div class="input-group">
            <span class="input-group-text">Rp</span>
            <input type="number" class="form-control" id="shipping-cost" name="shipping_cost"
                placeholder="0" min="0" step="0.01" value="{{ $salesOrder['shipping_cost'] ?? 0 }}" />
        </div>
        <small class="form-text text-muted">Biaya ongkir yang dibayar seller (tidak termasuk di invoice)</small>
    </div>
    <div class="col-md-6">
        <label class="form-label">Keterangan</label>
        <textarea class="form-control" name="description" id="description" rows="4" maxlength="1000">{{ $salesOrder['description'] ?? '' }}</textarea>
    </div>
</div>
```

#### detail.blade.php
**File**: `resources/views/pages/main_transaction/sales_order/detail.blade.php`

**Changes**:
- Added "Harga Ongkos Kirim" display field (disabled input with currency format)
- Moved "Keterangan" textarea to right column

**Code**:
```html
<!-- Row 3: Harga Ongkos Kirim, Keterangan -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label class="form-label">Harga Ongkos Kirim</label>
        <div class="input-group">
            <span class="input-group-text">Rp</span>
            <input type="text" class="form-control" value="{{ number_format($salesOrder['shipping_cost'] ?? 0, 2, ',', '.') }}" disabled />
        </div>
        <small class="form-text text-muted">Biaya ongkir yang dibayar seller</small>
    </div>
    <div class="col-md-6">
        <label class="form-label">Keterangan</label>
        <textarea class="form-control" rows="3" disabled>{{ $salesOrder['description'] ?? '-' }}</textarea>
    </div>
</div>
```

### 2. Controller (Backend)

#### SalesOrderController.php
**File**: `app/Http/Controllers/MainTransaction/SalesOrderController.php`

**Changes**:

##### store() method:
Added validation rule for `shipping_cost`:
```php
'shipping_cost' => 'nullable|numeric|min:0',
```

Added field name translation:
```php
'shipping_cost' => 'Harga Ongkos Kirim',
```

##### update() method:
Added same validation rule and translation as store() method.

### 3. Database

#### Migration File
**File**: `database/migrations/2025_12_14_000000_add_shipping_cost_to_sales_orders_table.php`

**Content**:
```php
Schema::table('t_sales_orders', function (Blueprint $table) {
    $table->decimal('shipping_cost', 15, 2)->default(0)->after('warehouse_id')
        ->comment('Biaya ongkos kirim yang dibayar seller (tidak termasuk dalam invoice)');
});
```

## DDL for Manual Execution

```sql
ALTER TABLE `t_sales_orders`
ADD COLUMN `shipping_cost` DECIMAL(15,2) NOT NULL DEFAULT 0
AFTER `warehouse_id`
COMMENT 'Biaya ongkos kirim yang dibayar seller (tidak termasuk dalam invoice)';
```

## Field Specifications

| Property | Value |
|----------|-------|
| **Field Name** | shipping_cost |
| **Label** | Harga Ongkos Kirim |
| **Data Type** | DECIMAL(15,2) |
| **Default Value** | 0 |
| **Nullable** | Yes (defaults to 0) |
| **Validation** | numeric, min:0 |
| **Input Type** | number |
| **Currency** | IDR (Rp) |
| **Decimal Places** | 2 |
| **Position** | After warehouse_id |

## Business Logic

1. **Optional Field**: Shipping cost is optional and defaults to 0
2. **Seller Cost**: This cost is paid by the seller, not the customer
3. **Invoice Exclusion**: This cost is NOT included in the customer invoice
4. **Non-negative**: Cannot be negative (validated at both frontend and backend)
5. **Currency Format**: Displayed with "Rp" prefix and formatted with thousands separator

## Export/Import

**Note**: Export and import functionality are marked as TODO in the controller. When implementing:

### Export Considerations:
- Include `shipping_cost` column in export
- Format as currency: `Rp 50,000.00`
- Column header: "Harga Ongkos Kirim"

### Import Considerations:
- Accept numeric values for shipping_cost
- Allow empty (will default to 0)
- Strip currency symbols if present
- Validate min:0

## Testing Checklist

### Manual Testing:
- [x] Run DDL query on database
- [ ] Create new sales order with shipping cost = 50000
- [ ] Create new sales order without shipping cost (verify defaults to 0)
- [ ] Edit existing sales order and add shipping cost
- [ ] Edit existing sales order and change shipping cost to 0
- [ ] View detail page shows shipping cost correctly formatted
- [ ] Try entering negative shipping cost (should be rejected)
- [ ] Try entering text in shipping cost (should be rejected)
- [ ] Verify old sales orders display shipping cost as 0

### Validation Testing:
- [ ] Submit form without shipping_cost → should succeed with default 0
- [ ] Submit with shipping_cost = -100 → should fail (min:0)
- [ ] Submit with shipping_cost = "abc" → should fail (numeric)
- [ ] Submit with shipping_cost = 1000000000000000 → should succeed (within DECIMAL(15,2))
- [ ] Submit with shipping_cost = 0.001 → should round to 0.00 (2 decimal places)

## Rollback Instructions

If you need to rollback this feature:

### 1. Database:
```sql
ALTER TABLE `t_sales_orders` DROP COLUMN `shipping_cost`;
```

Or using Laravel migration:
```bash
php artisan migrate:rollback --step=1
```

### 2. Code:
Revert the following files:
- `resources/views/pages/main_transaction/sales_order/add.blade.php`
- `resources/views/pages/main_transaction/sales_order/edit.blade.php`
- `resources/views/pages/main_transaction/sales_order/detail.blade.php`
- `app/Http/Controllers/MainTransaction/SalesOrderController.php`

## Future Enhancements

Potential improvements for future iterations:

1. **Shipping Cost Analytics**:
   - Dashboard widget showing total shipping costs per month
   - Comparison of shipping costs across different warehouses
   - Shipping cost trends over time

2. **Shipping Provider Integration**:
   - Link shipping cost to specific provider (JNE, TIKI, etc.)
   - Automatic calculation based on weight and destination
   - Tracking number integration

3. **Profit Margin Calculation**:
   - Calculate actual profit considering shipping cost
   - Alert when shipping cost exceeds threshold
   - Shipping cost vs order value ratio

4. **Bulk Operations**:
   - Bulk update shipping costs for selected orders
   - Import shipping costs from carrier invoice
   - Export shipping cost summary for accounting

## Support

For questions or issues related to this feature, refer to:
- **DDL Documentation**: `docs/SALES_ORDER_SHIPPING_COST_DDL.md`
- **This Summary**: `docs/SALES_ORDER_SHIPPING_COST_SUMMARY.md`
