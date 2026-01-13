# Product Units Implementation

## Overview
This implementation allows each product to have multiple units of measurement (UOM) with automatic conversion between them. For example, a product can be sold in both "PCS" (pieces) and "BOX", where 1 BOX = 10 PCS.

## Database Schema

### Table: `m_product_units`
Stores the relationship between products and their units with conversion factors.

**Columns:**
- `id` - Primary key
- `company_code` - Company identifier
- `f_product_id` - Foreign key to `m_products.id`
- `f_unit_id` - Foreign key to `m_units.id`
- `is_default` - Boolean flag indicating the base/default unit
- `conversion_factor` - Decimal(15,4) - Conversion multiplier to base unit
- `barcode` - Optional barcode specific to this unit
- `status` - ENUM('ACTIVE', 'INACTIVE')
- `notes` - Text field for additional information
- `created_at`, `updated_at`, `created_by`, `updated_by` - Audit fields

**Indexes:**
- `idx_product_units_lookup` - On (company_code, f_product_id)
- `idx_product_units_status` - On (status)
- `idx_product_units_default` - On (is_default)

**Unique Constraint:**
- `unique_product_unit` - On (company_code, f_product_id, f_unit_id)

## How It Works

### Concept
1. **Default Unit**: Each product must have exactly ONE default unit (base unit)
2. **Additional Units**: You can add multiple additional units with conversion factors relative to the default unit
3. **Conversion Factor**: Represents how many default units equal 1 of the current unit

### Example
Product: "Mineral Water"
- Default Unit: PCS (Pieces) - conversion_factor = 1
- Additional Unit: BOX - conversion_factor = 10 (meaning 1 BOX = 10 PCS)
- Additional Unit: CARTON - conversion_factor = 120 (meaning 1 CARTON = 120 PCS)

When you sell:
- 1 BOX → System calculates as 10 PCS
- 2 CARTONS → System calculates as 240 PCS

## Backend Implementation

### New Service: `ProductUnitService`
Location: `app/Domains/Product/Services/ProductUnitService.php`

**Key Methods:**
- `createMultiple(int $productId, array $units)` - Creates multiple units for a product
- `updateProductUnits(int $productId, array $units)` - Updates units (deletes old, creates new)
- `findByProduct(int $productId)` - Gets all units for a product
- `findDefaultUnit(int $productId)` - Gets the default unit
- `convertToDefaultUnit(int $productId, int $fromUnitId, float $quantity)` - Converts any unit to default
- `convertFromDefaultUnit(int $productId, int $toUnitId, float $quantity)` - Converts from default to any unit
- `findActiveByProduct(int $productId)` - Gets active units for dropdowns

### Updated Actions

**CreateProductAction** (`app/Domains/Product/Actions/CreateProductAction.php`)
- Now creates product units after product creation
- Accepts `product_units` array in data

**UpdateProductAction** (`app/Domains/Product/Actions/UpdateProductAction.php`)
- Now updates product units when product is updated
- Deletes old units and creates new ones based on submitted data

### Controller Validation

**ProductController** (`app/Http/Controllers/MasterData/ProductController.php`)

Both `save()` and `update()` methods now validate:
```php
'product_units' => 'required|array|min:1',
'product_units.*.unit_id' => 'required|integer|exists:m_units,id',
'product_units.*.is_default' => 'required|boolean',
'product_units.*.conversion_factor' => 'required|numeric|min:0.0001',
'product_units.*.barcode' => 'nullable|string|max:50',
'product_units.*.status' => 'required|in:ACTIVE,INACTIVE',
'product_units.*.notes' => 'nullable|string|max:1000',
```

## API Request Format

### Create Product with Units
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
      "notes": "Base unit"
    },
    {
      "unit_id": 2,
      "is_default": false,
      "conversion_factor": 10,
      "barcode": "987654321",
      "status": "ACTIVE",
      "notes": "1 BOX = 10 PCS"
    },
    {
      "unit_id": 3,
      "is_default": false,
      "conversion_factor": 120,
      "barcode": null,
      "status": "ACTIVE",
      "notes": "1 CARTON = 120 PCS"
    }
  ]
}
```

## Business Rules

1. **Exactly One Default Unit**: Each product must have exactly one unit marked as `is_default = true`
2. **Default Unit Conversion**: The default unit should have `conversion_factor = 1`
3. **No Duplicate Units**: A product cannot have the same unit_id twice (enforced by unique constraint)
4. **Positive Conversion Factors**: All conversion factors must be greater than 0.0001
5. **Active Units**: Only units with `status = 'ACTIVE'` are used in transactions

## Usage in Transactions

When creating sales orders, purchase orders, invoices, etc.:
1. User selects a unit from the product's available units
2. User enters quantity in that unit
3. System converts to default unit for stock calculation
4. Stock is always stored and tracked in the default unit

## Migration

Run the migration:
```bash
php artisan migrate
```

This creates the `m_product_units` table with all necessary constraints and indexes.

## Frontend Implementation (TODO)

The frontend views need to be updated to:
1. Show a dynamic unit management section in product add/edit forms
2. Allow users to add/remove units
3. Mark one unit as default
4. Input conversion factors
5. Display all units in product details page

Recommended UI:
- A table/list showing all units for the product
- Buttons to add new unit rows
- Dropdown to select unit from m_units table
- Checkbox/radio to mark default unit
- Input field for conversion factor
- Remove button for each unit row

## Testing

To test the implementation:

1. Create a product with multiple units via API
2. Verify units are saved in `m_product_units` table
3. Update the product and change units
4. Verify old units are deleted and new ones created
5. Test conversion methods in ProductUnitService

## Notes

- The `f_unit_id` column in `m_products` table is still required for backward compatibility
- The default unit in `product_units` should match the `f_unit_id` in the product record
- Stock movements should always convert to the default unit
- Pricing can be maintained per unit if needed (future enhancement)
