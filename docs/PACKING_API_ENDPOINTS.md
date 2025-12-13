# Packing Module - API Endpoints

## New Endpoints Added

### 1. Get Packaging Products
**URL**: `GET /main_transaction/packing/get_packaging_products`

**Purpose**: Get all packaging products that use a specific bundle product as a component.

**Parameters**:
- `bundle_product_id` (required) - The main bundle product ID
- `warehouse_id` (optional) - If provided, includes stock information for other components

**Response**:
```json
{
  "success": true,
  "data": [
    {
      "id": 201,
      "code": "PACK-SM",
      "name": "Small Pack (6 pcs)",
      "unit_code": "PACK",
      "bundle_qty_needed": 6,
      "other_components": [
        {
          "product_id": 301,
          "product_code": "WRAP-01",
          "product_name": "Plastic Wrap",
          "unit_code": "PCS",
          "qty_per_unit": 1,
          "available_stock": 50
        }
      ]
    },
    {
      "id": 202,
      "code": "PACK-MD",
      "name": "Medium Pack (9 pcs)",
      "unit_code": "PACK",
      "bundle_qty_needed": 9,
      "other_components": []
    }
  ]
}
```

**Usage Example**:
```javascript
$.ajax({
    url: '{{ route("main_transaction.packing.get_packaging_products") }}',
    method: 'GET',
    data: {
        bundle_product_id: bundleProductId,
        warehouse_id: warehouseId
    },
    success: function(response) {
        if (response.success) {
            // Populate packaging products table
            response.data.forEach(packaging => {
                addPackagingRow(packaging);
            });
        }
    }
});
```

---

### 2. Get Bundle Stock
**URL**: `GET /main_transaction/packing/get_bundle_stock`

**Purpose**: Get available stock of the bundle product in a specific warehouse.

**Parameters**:
- `bundle_product_id` (required) - The bundle product ID
- `warehouse_id` (required) - The warehouse ID

**Response**:
```json
{
  "success": true,
  "available_qty": 100,
  "unit_code": "BOX"
}
```

**Usage Example**:
```javascript
$.ajax({
    url: '{{ route("main_transaction.packing.get_bundle_stock") }}',
    method: 'GET',
    data: {
        bundle_product_id: bundleProductId,
        warehouse_id: warehouseId
    },
    success: function(response) {
        if (response.success) {
            $('#available-qty').val(response.available_qty);
            $('#unit-label').text(response.unit_code);
        }
    }
});
```

---

## Frontend Integration Flow

### Step 1: User Selects Bundle Product + Warehouse

```javascript
$('#bundle-product, #warehouse').on('change', function() {
    const bundleProductId = $('#bundle-product').val();
    const warehouseId = $('#warehouse').val();

    if (bundleProductId && warehouseId) {
        // Get bundle stock
        getBundleStock(bundleProductId, warehouseId);

        // Get packaging products
        getPackagingProducts(bundleProductId, warehouseId);
    }
});
```

### Step 2: Display Packaging Products

For each packaging product, create a row showing:
- Product name/code
- Qty input (default 0)
- Bundle qty needed (calculated: qty input × bundle_qty_needed)
- Other components with stock check

### Step 3: Calculate Stock as User Types

```javascript
$(document).on('input', '.packaging-qty-input', function() {
    calculateTotalBundleNeeded();
    checkComponentStock();
    updateAvailableQty();
});

function calculateTotalBundleNeeded() {
    let totalNeeded = 0;

    $('.packaging-row').each(function() {
        const qty = parseFloat($(this).find('.packaging-qty-input').val()) || 0;
        const bundleQtyNeeded = parseFloat($(this).data('bundle-qty-needed'));
        totalNeeded += (qty * bundleQtyNeeded);
    });

    $('#total-bundle-needed').text(totalNeeded);

    const available = parseFloat($('#available-qty').val());
    const remaining = available - totalNeeded;
    $('#remaining-qty').text(remaining);

    if (remaining < 0) {
        showError('Insufficient bundle stock!');
    }
}
```

### Step 4: Stock Overlap Prevention

```javascript
function checkComponentStock() {
    const componentUsage = {};

    // Calculate total usage for each component across all packaging products
    $('.packaging-row').each(function() {
        const qty = parseFloat($(this).find('.packaging-qty-input').val()) || 0;
        const otherComponents = $(this).data('other-components');

        if (otherComponents) {
            otherComponents.forEach(comp => {
                if (!componentUsage[comp.product_id]) {
                    componentUsage[comp.product_id] = {
                        needed: 0,
                        available: comp.available_stock,
                        name: comp.product_name
                    };
                }
                componentUsage[comp.product_id].needed += (qty * comp.qty_per_unit);
            });
        }
    });

    // Check if any component is over-allocated
    for (const [productId, usage] of Object.entries(componentUsage)) {
        if (usage.needed > usage.available) {
            showWarning(`Insufficient stock for ${usage.name}! Needed: ${usage.needed}, Available: ${usage.available}`);
        }
    }
}
```

---

## Controller Methods

### getPackagingProducts()
**Location**: [PackingController.php:179-243](app/Http/Controllers/MainTransaction/PackingController.php#L179-L243)

**Logic**:
1. Query packaging products that have the bundle as a component
2. For each packaging product, get other components (excluding the bundle)
3. If warehouse provided, fetch stock for each component
4. Return structured data with all information

### getBundleStock()
**Location**: [PackingController.php:245-287](app/Http/Controllers/MainTransaction/PackingController.php#L245-L287)

**Logic**:
1. Sum all active batch remaining quantities for the bundle in the warehouse
2. Return available quantity and unit code
3. Handle case when no stock exists

---

## Database Structure

### Query: Get Packaging Products
```sql
SELECT DISTINCT
    p.id,
    p.code,
    p.name,
    p.unit_code,
    pc.qty_per_batch as bundle_qty_needed
FROM m_products p
JOIN m_product_compositions pc ON p.id = pc.product_id
WHERE p.company_code = ?
  AND p.is_packaging = 1
  AND p.is_bundle = 1
  AND p.status = 'ACTIVE'
  AND pc.component_id = ?  -- bundle_product_id
```

### Query: Get Bundle Stock
```sql
SELECT
    COALESCE(SUM(psb.remaining_qty), 0) as available_qty,
    p.unit_code
FROM t_product_stock_batches psb
JOIN m_products p ON psb.f_product_id = p.id
WHERE psb.f_product_id = ?  -- bundle_product_id
  AND psb.f_warehouse_id = ?  -- warehouse_id
  AND psb.status = 'ACTIVE'
GROUP BY p.unit_code
```

---

## Testing

### Test Case 1: Basic Flow
1. Select "Box of Cookies (12 pcs)" as bundle
2. Select "Main Warehouse"
3. Verify stock shows correctly (e.g., 100 boxes)
4. Verify packaging products list shows:
   - Small Pack (6 pcs)
   - Medium Pack (9 pcs)
   - Large Pack (12 pcs)

### Test Case 2: Stock Calculation
1. Enter 10 for Small Pack → should show 60 pcs needed from bundle
2. Enter 5 for Medium Pack → should show 45 pcs needed from bundle
3. Total needed: 105 pcs
4. Available: 100 boxes × 12 = 1200 pcs ✓

### Test Case 3: Stock Overlap
1. If Small Pack and Medium Pack both need "Plastic Wrap"
2. System should sum: (10 × 1) + (5 × 1) = 15 wraps needed
3. Check available wraps ≥ 15

---

## Next Steps

- [ ] Update `add.blade.php` to call these APIs
- [ ] Implement frontend stock validation
- [ ] Add stock overlap detection
- [ ] Implement save functionality with t_packaging insertion
