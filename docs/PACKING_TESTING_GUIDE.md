# Packing Module - Testing Guide

## Prerequisites

Before testing, ensure you have:

1. **Database Migrations Run**
   ```bash
   php artisan migrate
   ```
   This creates:
   - `is_packaging` flag in `t_productions`
   - `is_packaging` flag in `m_products`
   - `t_packaging` mapping table

2. **Sample Data**
   - At least one bundle product
   - At least one packaging product (bundle with `is_packaging=true`) that uses the bundle as component
   - Stock batches for both products in a warehouse

## Test Scenarios

### Scenario 1: Basic Packing Flow (Happy Path)

**Setup:**
- Bundle: "Cookie Box (12 pcs)" with stock = 100 boxes in Main Warehouse
- Packaging Products:
  - Small Pack (6 pcs) - uses 6 cookies per pack
  - Medium Pack (9 pcs) - uses 9 cookies per pack

**Steps:**
1. Navigate to Packing → Add New Packing
2. Select "Cookie Box (12 pcs)" as bundle product
3. Select "Main Warehouse"
4. Enter PIC

**Expected Result:**
- Available Bundle Stock shows: 1200 pcs (100 boxes × 12)
- Packaging products table shows Small Pack and Medium Pack
- Both show "No Input" status

**User Action:**
- Enter 10 for Small Pack
- Enter 5 for Medium Pack

**Expected Calculation:**
- Small Pack bundle needed: 60 pcs (10 × 6)
- Medium Pack bundle needed: 45 pcs (5 × 9)
- Total bundle needed: 105 pcs
- Remaining: 1095 pcs ✅
- Both status show "Stock OK"

**Submit:**
- Click "Simpan"
- Should succeed

**Database Verification:**
```sql
-- Check t_productions
SELECT * FROM t_productions WHERE is_packaging = 1 ORDER BY id DESC LIMIT 1;
-- Should show: quantity_planned = 15 (10 + 5)

-- Check t_production_items
SELECT * FROM t_production_items WHERE production_id = [last_id];
-- Should show 2 rows: Small Pack (10) and Medium Pack (5)

-- Check t_packaging
SELECT * FROM t_packaging WHERE production_id = [last_id];
-- Should show 2 rows with mapping data
```

---

### Scenario 2: Insufficient Bundle Stock

**Setup:**
- Bundle: "Cookie Box" with stock = 10 pcs only

**Steps:**
1. Select bundle + warehouse
2. Available shows: 10 pcs
3. Enter 5 for Small Pack (needs 30 pcs)

**Expected Result:**
- Remaining Bundle Stock: -20 pcs (turns red)
- Cannot submit
- Error: "Bundle Stock Tidak Cukup"

---

### Scenario 3: Component Stock Overlap

**Setup:**
- Packaging products share a component:
  - Small Pack needs "Plastic Wrap" (1 per pack)
  - Medium Pack needs "Plastic Wrap" (1 per pack)
- Available Plastic Wrap stock: 10 pcs

**Steps:**
1. Enter 8 for Small Pack → needs 8 wraps, status "Stock OK"
2. Enter 5 for Medium Pack → needs 5 wraps

**Expected Result:**
- Total wrap needed: 13 pcs
- Available: 10 pcs
- Both rows show "Insufficient Stock" (red badge)
- Warning on submit: "Stock Komponen Tidak Cukup"
- User can choose to continue

---

### Scenario 4: Zero Quantity Skip

**Setup:**
- 3 packaging products available

**Steps:**
1. Enter 10 for Small Pack
2. Enter 0 for Medium Pack
3. Enter 5 for Large Pack

**Expected Result:**
- Only Small Pack and Large Pack saved
- Medium Pack skipped (qty = 0)

**Database Verification:**
```sql
SELECT * FROM t_production_items WHERE production_id = [last_id];
-- Should show only 2 rows (Small and Large)
```

---

### Scenario 5: No Packaging Products Available

**Setup:**
- Select a bundle that has NO packaging products using it as component

**Expected Result:**
- Packaging section shows empty table
- Message: "Silakan pilih Bundle Product dan Gudang terlebih dahulu..."
- Cannot submit (no packaging products)

---

### Scenario 6: Transaction Number Generation

**Steps:**
1. Click refresh button next to transaction number

**Expected Result:**
- Auto-generates: `PACK-202512-0001` (format: PACK-YYYYMM-XXXX)
- Clicking again increments: `PACK-202512-0002`

---

## Validation Testing

### Required Field Validation

**Test:** Leave fields empty and click submit

**Expected Errors:**
- Tanggal Transaksi: required
- Nomor Transaksi: required
- Bundle Product: required
- Gudang: required
- Penanggung Jawab: required

### Packaging Quantity Validation

**Test:** Don't enter any qty for packaging products

**Expected Error:**
- "Minimal satu packaging product harus memiliki qty > 0"

---

## API Endpoint Testing

### Test 1: Get Bundle Stock
```javascript
// In browser console:
$.ajax({
    url: '/main_transaction/packing/get_bundle_stock',
    data: { bundle_product_id: 1, warehouse_id: 1 },
    success: console.log
});

// Expected response:
{
    "success": true,
    "available_qty": 1200,
    "unit_code": "PCS"
}
```

### Test 2: Get Packaging Products
```javascript
$.ajax({
    url: '/main_transaction/packing/get_packaging_products',
    data: { bundle_product_id: 1, warehouse_id: 1 },
    success: console.log
});

// Expected response:
{
    "success": true,
    "data": [
        {
            "id": 201,
            "code": "PACK-SM",
            "name": "Small Pack",
            "unit_code": "PACK",
            "bundle_qty_needed": 6,
            "other_components": [...]
        }
    ]
}
```

---

## Performance Testing

### Test Real-Time Calculations

**Action:** Type in qty input field rapidly

**Expected:**
- Bundle qty needed updates instantly
- Remaining stock updates instantly
- Component status updates instantly
- No lag or delay
- No console errors

---

## Edge Cases

### Edge Case 1: Decimal Quantities
**Test:** Enter 10.5 in qty field
**Expected:** Accepts decimal (packing can produce fractional units)

### Edge Case 2: Negative Quantities
**Test:** Enter -5 in qty field
**Expected:** Browser validation prevents (min=0)

### Edge Case 3: Very Large Quantities
**Test:** Enter 999999
**Expected:** Calculates correctly, shows insufficient stock warning

### Edge Case 4: Change Bundle After Entering Qty
**Test:**
1. Enter quantities
2. Change bundle product

**Expected:**
- Packaging table reloads
- All quantities reset to 0
- Stock recalculated

---

## Browser Compatibility

Test in:
- ✅ Chrome (latest)
- ✅ Firefox (latest)
- ✅ Edge (latest)
- ✅ Safari (if available)

**Check for:**
- No console errors
- All JavaScript functions work
- Form submits correctly
- Number formatting displays properly

---

## Common Issues & Solutions

### Issue: Packaging products not showing
**Check:**
1. Is the selected bundle marked as `is_packaging=true` in m_products?
2. Does the packaging product have the bundle in its composition?
3. Is the packaging product status = 'ACTIVE'?

### Issue: "Route not found" error
**Check:**
1. Are routes registered in routes/main_transaction.php?
2. Run: `php artisan route:list | grep packing`
3. Verify route names match blade template

### Issue: Stock overlap not detecting correctly
**Check:**
1. Open browser console
2. Check componentUsage object in checkComponentStockOverlap()
3. Verify component IDs match across packaging products

### Issue: Form submission fails
**Check:**
1. Browser console for JavaScript errors
2. Network tab for AJAX response
3. Laravel logs: `storage/logs/laravel.log`
4. Verify CSRF token is present

---

## Success Criteria

All tests pass when:
- ✅ Form loads without errors
- ✅ Bundle + warehouse selection triggers API calls
- ✅ Packaging products display with correct data
- ✅ Real-time calculations work accurately
- ✅ Stock overlap detection prevents over-allocation
- ✅ Validation prevents invalid submissions
- ✅ Form submits successfully
- ✅ Database records created correctly in all 3 tables
- ✅ Packaging with qty=0 are skipped
- ✅ Transaction numbers generated sequentially

---

## Debugging Tips

### Enable Console Logging
The form already has several console.log statements:
- Bundle stock loaded
- Packaging products loaded
- Component stock checks
- Form submission data

### Check Network Tab
Monitor AJAX calls:
1. `get_bundle_stock` - Should return success
2. `get_packaging_products` - Should return array
3. `store` - Should return production_id on success

### Inspect Database
```sql
-- Check if packing was saved
SELECT * FROM t_productions WHERE is_packaging = 1;

-- Check packaging details
SELECT
    p.production_number,
    pk.quantity_packed,
    prod.name as packaging_product
FROM t_packaging pk
JOIN t_productions p ON pk.production_id = p.id
JOIN m_products prod ON pk.packaging_product_id = prod.id
ORDER BY p.id DESC;
```

---

## Next: Production Testing

After packing testing is complete, verify:
1. Packing transactions appear in packing index
2. Stock batches are created for packaging products
3. HPP is calculated correctly for packaging products
4. Original bundle stock is reduced correctly
