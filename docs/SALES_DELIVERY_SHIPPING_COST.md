# Sales Delivery - Shipping Cost Field Implementation

**Date**: December 23, 2025
**Feature**: Add shipping cost (ongkir) field to Sales Delivery module

## Overview
Added a shipping cost field to the Sales Delivery module to track delivery shipping costs for each sales delivery transaction.

## Files Modified/Created

### 1. Database Migration
**File**: [database/migrations/2025_12_23_000000_add_shipping_cost_to_sales_deliveries_table.php](../database/migrations/2025_12_23_000000_add_shipping_cost_to_sales_deliveries_table.php)

**Migration**:
```php
Schema::table('t_sales_deliveries', function (Blueprint $table) {
    $table->decimal('shipping_cost', 15, 2)->default(0)->after('delivery_notes');
});
```

**To run migration**:
```bash
php artisan migrate
```

### 2. Add Form View
**File**: [resources/views/pages/main_transaction/sales_delivery/add.blade.php](../resources/views/pages/main_transaction/sales_delivery/add.blade.php:171-181)

**Added Field** (in "Informasi Pengiriman" section):
```blade
<!-- Row 2: Shipping Cost -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label class="form-label">Ongkos Kirim <span class="text-muted">(Opsional)</span></label>
        <div class="input-group">
            <span class="input-group-text">Rp</span>
            <input type="number" class="form-control" id="shipping-cost" name="shipping_cost"
                placeholder="0" min="0" step="0.01" value="0" />
        </div>
        <small class="form-text text-muted">Biaya ongkir untuk pengiriman ini</small>
    </div>
</div>
```

### 3. Detail View
**File**: [resources/views/pages/main_transaction/sales_delivery/detail.blade.php](../resources/views/pages/main_transaction/sales_delivery/detail.blade.php:150-156)

**Added Field** (in "Informasi Pengiriman" section):
```blade
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label class="form-label">Ongkos Kirim</label>
        <input type="text" class="form-control"
            value="Rp {{ number_format($salesDelivery->shipping_cost ?? 0, 0, ',', '.') }}" disabled />
    </div>
</div>
```

### 4. Backend Service
**File**: [app/Domains/SalesDelivery/Services/SalesDeliveryService.php](../app/Domains/SalesDelivery/Services/SalesDeliveryService.php:25)

**Updated create() method**:
```php
return DB::table($this->table_name)->insertGetId([
    'company_code' => session('user_company_code'),
    'delivery_number' => $data['delivery_number'],
    'attachment_path' => $data['attachment'] ?? null,
    'sales_order_id' => $data['sales_order_id'] ?? null,
    'customer_id' => $data['customer_id'] ?? null,
    'customer_reference' => $data['customer_reference'] ?? null,
    'delivery_notes' => $data['delivery_notes'] ?? null,
    'delivery_address' => $data['delivery_address'] ?? null,
    'shipping_cost' => $data['shipping_cost'] ?? 0,  // ← NEW FIELD
    'tags' => isset($data['tags']) ? json_encode($data['tags']) : null,
    'delivery_date' => $data['delivery_date'] ?? now(),
    'status' => $data['status'] ?? 'PENDING',
    'warehouse_id' => $data['warehouse_id'] ?? null,
    'created_at' => now(),
    'created_by' => Auth::id(),
]);
```

## Database Schema

### Table: `t_sales_deliveries`

| Column | Type | Default | Description |
|--------|------|---------|-------------|
| shipping_cost | decimal(15,2) | 0 | Biaya ongkir pengiriman |

## Field Details

### Input Field (Add Form)
- **Label**: Ongkos Kirim (Opsional)
- **Type**: Number input with Rupiah prefix
- **Default Value**: 0
- **Min Value**: 0
- **Step**: 0.01 (allows decimal input)
- **Validation**: Optional
- **Location**: "Informasi Pengiriman" section, below "Nomor Resi Pengiriman"

### Display Field (Detail View)
- **Label**: Ongkos Kirim
- **Format**: Rp xxx.xxx (Indonesian currency format)
- **Read-only**: Yes
- **Location**: "Informasi Pengiriman" section

## Usage Flow

### Creating Sales Delivery with Shipping Cost

1. Navigate to Sales Delivery → Add
2. Fill in required fields (Date, Customer, Warehouse, etc.)
3. Scroll to "Informasi Pengiriman" section
4. Enter shipping cost in the "Ongkos Kirim" field (optional)
5. Add products
6. Submit form

### Viewing Shipping Cost

1. Navigate to Sales Delivery → List
2. Click on any delivery record
3. View the "Informasi Pengiriman" section
4. Shipping cost is displayed in Rupiah format

## Data Flow

```
User Input (Add Form)
    ↓
shipping_cost: 50000
    ↓
Form Submission
    ↓
SalesDeliveryController
    ↓
CreateSalesDeliveryAction
    ↓
SalesDeliveryService::create()
    ↓
t_sales_deliveries table
    shipping_cost = 50000.00
    ↓
Display (Detail View)
    ↓
"Rp 50.000"
```

## Testing Checklist

- [x] Migration created and ready to run
- [x] Field added to add form
- [x] Field added to detail view
- [x] Backend service updated to save shipping_cost
- [x] Default value set to 0
- [x] Input accepts decimal values
- [x] Display shows proper Indonesian currency format
- [x] Field is optional (not required)

## Migration Steps

1. **Run the migration**:
   ```bash
   php artisan migrate
   ```

2. **Verify column added**:
   ```sql
   DESC t_sales_deliveries;
   ```

3. **Test the feature**:
   - Create a new sales delivery with shipping cost
   - Verify data is saved correctly
   - Check detail page displays the shipping cost

## Notes

- Shipping cost is **optional** - defaults to 0 if not provided
- Field supports decimal values (e.g., 50000.50)
- Currency formatting follows Indonesian standard (e.g., Rp 50.000)
- No validation rules applied (can be 0 or any positive number)

## Related Files

- **Migration**: [database/migrations/2025_12_23_000000_add_shipping_cost_to_sales_deliveries_table.php](../database/migrations/2025_12_23_000000_add_shipping_cost_to_sales_deliveries_table.php)
- **Add Form**: [resources/views/pages/main_transaction/sales_delivery/add.blade.php](../resources/views/pages/main_transaction/sales_delivery/add.blade.php)
- **Detail View**: [resources/views/pages/main_transaction/sales_delivery/detail.blade.php](../resources/views/pages/main_transaction/sales_delivery/detail.blade.php)
- **Service**: [app/Domains/SalesDelivery/Services/SalesDeliveryService.php](../app/Domains/SalesDelivery/Services/SalesDeliveryService.php)
- **Controller**: [app/Http/Controllers/MainTransaction/SalesDeliveryController.php](../app/Http/Controllers/MainTransaction/SalesDeliveryController.php)

## Future Enhancements

1. **Validation**: Add validation rules if needed (e.g., max shipping cost)
2. **Auto-calculation**: Could integrate with shipping provider APIs
3. **Reporting**: Include shipping costs in delivery reports
4. **Invoice Integration**: Consider linking shipping cost to invoices
