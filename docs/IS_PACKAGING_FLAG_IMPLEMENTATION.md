# is_packaging Flag Implementation - Summary

## Overview
Added `is_packaging` flag to master product forms to allow marking bundle products as packaging products for use in the packing module.

## Database Changes

### Migration Already Exists
- Migration: `2025_12_12_231913_add_is_packaging_to_products_table.php`
- Adds `is_packaging` boolean column to `m_products` table
- Default value: `false`
- Location: After `is_bundle` column

## Frontend Changes

### 1. Add Product Form ([add.blade.php](resources/views/pages/master_data/product/add.blade.php))

**HTML Section Added (Lines 140-150):**
```html
<div class="row g-3 mb-4" id="is-packaging-section" style="display: none;">
    <div class="col-md-12">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="is-packaging" name="is-packaging">
            <label class="form-check-label" for="is-packaging">
                Produk Packaging (untuk packing produk)
            </label>
        </div>
        <small class="form-text text-muted">Centang jika produk bundle ini digunakan untuk packaging/packing produk lain</small>
    </div>
</div>
```

**JavaScript Changes:**

1. **Show/Hide Logic (switchProductType function):**
   - When type = 'single': Hide `#is-packaging-section`
   - When type = 'bundle': Show `#is-packaging-section`

2. **Form Submission (collectFormData function - Line 1289):**
```javascript
// Bundle product fields
if (data.type === 'bundle') {
    data.is_packaging = $('#is-packaging').is(':checked') ? 'true' : 'false';
    // ... other bundle fields
}
```

**Important**: The value must be sent as string `'true'` or `'false'`, not boolean, because the backend validates against the string `'true'`.

### 2. Edit Product Form ([edit.blade.php](resources/views/pages/master_data/product/edit.blade.php))

**HTML Section Added (Lines 153-164):**
```html
<div class="row g-3 mb-4" id="is-packaging-section" style="display: none;">
    <div class="col-md-12">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="is-packaging" name="is-packaging"
                {{ old('is-packaging', $product->is_packaging ?? false) ? 'checked' : '' }}>
            <label class="form-check-label" for="is-packaging">
                Produk Packaging (untuk packing produk)
            </label>
        </div>
        <small class="form-text text-muted">Centang jika produk bundle ini digunakan untuk packaging/packing produk lain</small>
    </div>
</div>
```

**JavaScript Changes:**

1. **Show/Hide Logic (switchProductType function):**
   - When type = 'single': Hide `#is-packaging-section`
   - When type = 'bundle': Show `#is-packaging-section`

2. **Form Submission (collectFormData function - Line 1413):**
```javascript
// Bundle product fields
if (data.type === 'bundle') {
    data.is_packaging = $('#is-packaging').is(':checked') ? 'true' : 'false';
    // ... other bundle fields
}
```

**Important**: The value must be sent as string `'true'` or `'false'`, not boolean, because the backend validates against the string `'true'`.

### 3. Detail Product View ([detail.blade.php](resources/views/pages/master_data/product/detail.blade.php))

**Display Section Added (Lines 178-189):**
```html
@if($product->type === 'bundle')
<div class="col-md-6">
    <label class="form-label">Produk Packaging</label>
    <div class="form-check mt-2">
        <input class="form-check-input" type="checkbox"
            {{ $product->is_packaging ? 'checked' : '' }} disabled>
        <label class="form-check-label">
            {{ $product->is_packaging ? 'Ya, produk ini untuk packing' : 'Bukan produk packaging' }}
        </label>
    </div>
</div>
@endif
```

## Backend Changes

### ProductService.php ([app/Domains/Product/Services/ProductService.php](app/Domains/Product/Services/ProductService.php))

**1. Create Method (Line 117):**
```php
public function create(array $data)
{
    return DB::table($this->table_name)->insertGetId([
        // ... other fields
        'type' => $data['type'],
        'is_packaging' => $data['type'] === 'bundle' && isset($data['is_packaging']) && $data['is_packaging'] === 'true' ? true : false,
        // ... other fields
    ]);
}
```

**2. Update Method (Line 154):**
```php
public function update(int $id, array $data): int
{
    $update_data = [
        // ... other fields
        'type' => $data['type'],
        'is_packaging' => $data['type'] === 'bundle' && isset($data['is_packaging']) && $data['is_packaging'] === 'true' ? true : false,
        // ... other fields
    ];

    // ... rest of update logic
}
```

## Logic Flow

### Adding a Product
1. User selects "Bundle" as product type
2. `is_packaging` checkbox section appears (via `switchProductType` function)
3. User checks the checkbox if this bundle is a packaging product
4. On form submission:
   - JavaScript collects `is_packaging` value (true/false)
   - Sends to backend via AJAX
5. Backend receives data and saves to database:
   - Only sets `is_packaging = true` if type is 'bundle' AND checkbox is checked
   - Otherwise sets `is_packaging = false`

### Editing a Product
1. Form loads with existing product data
2. If product type is 'bundle', `is_packaging` section shows
3. Checkbox is pre-checked if `$product->is_packaging` is true
4. User can modify the checkbox value
5. On form submission, same flow as adding

### Viewing Product Detail
1. If product type is 'bundle', shows packaging status
2. Displays checked/unchecked checkbox with label
3. Checkbox is disabled (view-only)

## Data Validation

### Frontend (JavaScript)
- Checkbox only visible for bundle products
- Value sent as **string** `'true'` or `'false'` (not boolean)

### Backend (ProductService)
- Double-checks product type is 'bundle'
- Validates checkbox value is 'true' string
- Defaults to false for non-bundle products or unchecked

## Integration with Packing Module

The `is_packaging` flag is used by the packing module to:
1. Filter packaging products in `getPackagingProducts()` API
2. Prevent recursive packing (packaging products can't be packed again)
3. Display only relevant products in packing form dropdown

**Query Example (PackingController.php):**
```php
->where('p.is_packaging', true)
->where('p.is_bundle', true)
```

## Files Modified

1. **resources/views/pages/master_data/product/add.blade.php**
   - Added `is_packaging` checkbox HTML
   - Updated `switchProductType()` function
   - Updated form submission to include `is_packaging`

2. **resources/views/pages/master_data/product/edit.blade.php**
   - Added `is_packaging` checkbox HTML with pre-fill logic
   - Updated `switchProductType()` function
   - Updated form submission to include `is_packaging`

3. **resources/views/pages/master_data/product/detail.blade.php**
   - Added display section for `is_packaging` status

4. **app/Domains/Product/Services/ProductService.php**
   - Updated `create()` method to save `is_packaging`
   - Updated `update()` method to save `is_packaging`

## Testing Checklist

- [ ] Create new single product → `is_packaging` section hidden
- [ ] Create new bundle product → `is_packaging` section visible
- [ ] Create bundle with `is_packaging` checked → saves as true in database
- [ ] Create bundle with `is_packaging` unchecked → saves as false in database
- [ ] Edit bundle product → checkbox reflects database value
- [ ] Edit bundle product → can change checkbox value and save
- [ ] View bundle product detail → displays packaging status correctly
- [ ] Packing module → only shows bundle products with `is_packaging = true`

## Troubleshooting

### Issue: Checkbox checked but database shows 0

**Problem**:
- Frontend checkbox is checked
- Form submits successfully
- Database `is_packaging` column shows `0` instead of `1`

**Cause**:
JavaScript was sending boolean `true` but backend expects string `'true'`

**Solution**:
Changed form submission to send string values:
```javascript
// WRONG (sends boolean)
data.is_packaging = $('#is-packaging').is(':checked');

// CORRECT (sends string)
data.is_packaging = $('#is-packaging').is(':checked') ? 'true' : 'false';
```

Backend validation expects string comparison:
```php
'is_packaging' => $data['type'] === 'bundle' && isset($data['is_packaging']) && $data['is_packaging'] === 'true' ? true : false
```

This matches the pattern used by other boolean fields in the form (like `bundle_purchase_enabled`, `bundle_sales_enabled`, etc.)

## Notes

- Only bundle products can be marked as packaging products
- The checkbox is automatically hidden for single products
- Frontend sends string `'true'` or `'false'`, backend converts to boolean for database storage
- The flag integrates seamlessly with existing packing module queries
- This follows the same pattern as other boolean fields in the product form
