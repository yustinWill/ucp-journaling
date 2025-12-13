# Packing Module - Complete Flow Documentation

## Overview

The Packing module allows creating multiple packaging variants from a single bundle product, with automatic stock management and component tracking.

## Database Tables

### t_packaging (NEW)
Stores mapping between production and packaging products:
- `production_id` - Links to t_productions (is_packaging = true)
- `bundle_product_id` - The main bundle selected
- `packaging_product_id` - The packaging variant created
- `quantity_packed` - Quantity produced

## API Endpoints Added

### GET /packing/get_packaging_products
**Parameters**: `bundle_product_id`, `warehouse_id`
**Returns**: All packaging products that use the bundle as component

### GET /packing/get_bundle_stock
**Parameters**: `bundle_product_id`, `warehouse_id`
**Returns**: Available stock of the bundle product

## Migration Files

1. `2025_12_13_074927_create_t_packaging_table.php` - Creates t_packaging table
2. Run: `php artisan migrate`

## Routes Added

- `/packing/get_packaging_products`
- `/packing/get_bundle_stock`

See [routes/main_transaction.php](routes/main_transaction.php#L319-320)
