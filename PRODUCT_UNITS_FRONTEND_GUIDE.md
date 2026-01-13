# Product Units Frontend Implementation Guide

## Overview
The frontend has been updated to allow users to manage multiple units per product with conversion factors.

## What Was Implemented

### 1. Add Product Page (`add.blade.php`)

#### New Section: "Unit Produk"
Located between "Info Produk" and "Tipe Produk" sections.

**Features:**
- Table to display all units for the product
- Add Unit button to add new unit rows
- Remove button for each unit row (with validation)
- Radio buttons to select default unit
- Input fields for conversion factor, barcode, and status

**Fields:**
- **Unit**: Dropdown to select from available units in `m_units` table
- **Default**: Radio button to mark this unit as the base/default unit
- **Faktor Konversi**: Numeric input for conversion factor (relative to default unit)
- **Barcode**: Optional text input for unit-specific barcode
- **Status**: Dropdown (ACTIVE/INACTIVE)
- **Aksi**: Delete button to remove the unit row

#### JavaScript Features

**1. Add Unit Row**
```javascript
addProductUnitRow()
```
- Adds a new row to the units table
- First unit is automatically set as default
- Default unit's conversion factor is set to 1 and readonly

**2. Remove Unit Row**
- Validates minimum 1 unit must exist
- Prevents deletion of default unit
- Shows error messages via SweetAlert2

**3. Default Unit Selection**
- Radio button to mark one unit as default
- When changed, automatically sets conversion factor to 1 and makes it readonly
- Other units' conversion factors become editable

**4. Unit Uniqueness Validation**
- Prevents selecting the same unit multiple times
- Shows error if duplicate detected

**5. Data Collection**
```javascript
// In getProductFormJson()
data.product_units = [];
$('#product-units-tbody tr.product-unit-row').each(function() {
    data.product_units.push({
        unit_id: parseInt(unitId),
        is_default: isDefault,
        conversion_factor: parseFloat(conversionFactor),
        barcode: barcode || null,
        status: status,
        notes: null
    });
});
```

## User Flow

### Creating a Product with Multiple Units

1. **Fill Basic Info**
   - Name, Code, Category, etc.

2. **Add Units**
   - Click "Tambah Unit" button
   - Select unit from dropdown (e.g., "PCS")
   - Mark as default (automatically sets conversion factor to 1)
   - Click "Tambah Unit" again for additional units

3. **Add Converted Units**
   - Select another unit (e.g., "BOX")
   - Leave default unchecked
   - Enter conversion factor (e.g., "10" means 1 BOX = 10 PCS)
   - Optionally add barcode
   - Select status

4. **Add More Units** (if needed)
   - Repeat step 3 for additional units (e.g., CARTON with factor 120)

5. **Submit Form**
   - All units data is sent in `product_units` array
   - Backend validates and saves to `m_product_units` table

## Example UI Flow

```
┌─────────────────────────────────────────────────────────────┐
│ Unit Produk *                                               │
├─────────────────────────────────────────────────────────────┤
│ ℹ️ Tambahkan unit untuk produk ini. Satu unit harus        │
│   ditandai sebagai unit default (satuan dasar)...          │
├─────────────────────────────────────────────────────────────┤
│ ┌───────────────────────────────────────────────────────┐ │
│ │ Unit  │Default│Faktor Konversi│Barcode│Status │Aksi  │ │
│ ├───────────────────────────────────────────────────────┤ │
│ │ PCS   │  (•)  │       1       │12345  │ACTIVE │  🗑️  │ │
│ │ BOX   │  ( )  │      10       │67890  │ACTIVE │  🗑️  │ │
│ │CARTON │  ( )  │      120      │      │ACTIVE │  🗑️  │ │
│ └───────────────────────────────────────────────────────┘ │
│ [+ Tambah Unit]                                             │
└─────────────────────────────────────────────────────────────┘
```

## Validation Rules

### Frontend Validation
1. **Minimum 1 Unit**: Cannot delete all units
2. **Cannot Delete Default**: Must change default before deleting
3. **Unique Units**: Same unit cannot be selected twice
4. **Required Fields**: Unit, Default, Conversion Factor, Status

### Backend Validation (already implemented)
```php
'product_units' => 'required|array|min:1',
'product_units.*.unit_id' => 'required|integer|exists:m_units,id',
'product_units.*.is_default' => 'required|boolean',
'product_units.*.conversion_factor' => 'required|numeric|min:0.0001',
'product_units.*.barcode' => 'nullable|string|max:50',
'product_units.*.status' => 'required|in:ACTIVE,INACTIVE',
'product_units.*.notes' => 'nullable|string|max:1000',
```

## Data Format Sent to Backend

```json
{
  "name": "Mineral Water",
  "code": "MW001",
  "unit_id": 1,
  "category_id": 5,
  "type": "single",
  "status": "ACTIVE",
  "product_units": [
    {
      "unit_id": 1,
      "is_default": true,
      "conversion_factor": 1,
      "barcode": "123456789",
      "status": "ACTIVE",
      "notes": null
    },
    {
      "unit_id": 2,
      "is_default": false,
      "conversion_factor": 10,
      "barcode": "987654321",
      "status": "ACTIVE",
      "notes": null
    },
    {
      "unit_id": 3,
      "is_default": false,
      "conversion_factor": 120,
      "barcode": null,
      "status": "ACTIVE",
      "notes": null
    }
  ]
}
```

## TODO - Not Yet Implemented

### Edit Product Page (`edit.blade.php`)
- Load existing units from database
- Pre-populate the units table
- Handle update logic

### Detail Product Page (`detail.blade.php`)
- Display all units in a read-only table
- Show conversion information
- Display barcodes for each unit

## Testing Checklist

- [ ] Add product with single unit
- [ ] Add product with multiple units
- [ ] Change default unit selection
- [ ] Try to delete default unit (should show error)
- [ ] Try to delete all units (should show error)
- [ ] Try to select duplicate units (should show error)
- [ ] Submit form and verify data saved correctly
- [ ] Check database `m_product_units` table

## Notes

- The `unit_id` field in the main product form is still required for backward compatibility
- The default unit in `product_units` should match the selected `unit_id`
- Conversion factors are always relative to the default unit
- The default unit always has conversion_factor = 1

## Tips for Users

1. **Set Default Unit First**: Choose your base unit (usually the smallest) as default
2. **Conversion Factor Logic**: Think "1 [this unit] = X [default units]"
   - Example: 1 BOX = 10 PCS → Enter 10
   - Example: 1 CARTON = 120 PCS → Enter 120
3. **Barcodes**: Each unit can have its own unique barcode for scanning
4. **Status**: Inactive units won't appear in transaction forms

## Support

For issues or questions about the implementation, refer to:
- Backend Documentation: `PRODUCT_UNITS_IMPLEMENTATION.md`
- Controller: `app/Http/Controllers/MasterData/ProductController.php`
- Service: `app/Domains/Product/Services/ProductUnitService.php`
