# Packing Module Routes

This document lists all available routes for the Packing Produk module.

## Route Overview

All packing routes are prefixed with `/main_transaction/packing` and require authentication (`auth` middleware).

## Available Routes

### Main Routes

| Method | URI | Route Name | Controller Action | Description |
|--------|-----|------------|------------------|-------------|
| GET | `/main_transaction/packing/index` | `main_transaction.packing.index` | `PackingController@index` | Display packing list page |
| GET | `/main_transaction/packing/index_data_table` | `main_transaction.packing.index_data_table` | `PackingController@index_data_table` | Get packing data for DataTable (AJAX) |
| GET | `/main_transaction/packing/add` | `main_transaction.packing.add` | `PackingController@add` | Show add packing form |
| POST | `/main_transaction/packing/store` | `main_transaction.packing.store` | `PackingController@store` | Store new packing record |
| GET | `/main_transaction/packing/detail/{id}` | `main_transaction.packing.detail` | `PackingController@detail` | Show packing detail |
| DELETE | `/main_transaction/packing/{id}` | `main_transaction.packing.destroy` | `PackingController@destroy` | Delete packing record |

### Export Routes

| Method | URI | Route Name | Controller Action | Description |
|--------|-----|------------|------------------|-------------|
| GET | `/main_transaction/packing/export` | `main_transaction.packing.export` | `PackingController@export` | Show export form |
| POST | `/main_transaction/packing/process_export` | `main_transaction.packing.process_export` | `PackingController@processExport` | Process export request |

### AJAX Helper Routes

| Method | URI | Route Name | Controller Action | Description |
|--------|-----|------------|------------------|-------------|
| GET | `/main_transaction/packing/get_bundle_components` | `main_transaction.packing.get_bundle_components` | `PackingController@getBundleComponents` | Get bundle product components |
| GET | `/main_transaction/packing/get_warehouses` | `main_transaction.packing.get_warehouses` | `PackingController@getWarehouses` | Get warehouse list |
| GET | `/main_transaction/packing/get_bundle_products` | `main_transaction.packing.get_bundle_products` | `PackingController@getBundleProducts` | Get bundle product list |
| GET | `/main_transaction/packing/get_pic_users` | `main_transaction.packing.get_pic_users` | `PackingController@getPicUsers` | Get PIC user list |
| GET | `/main_transaction/packing/generate_transaction_number` | `main_transaction.packing.generate_transaction_number` | `PackingController@generateTransactionNumber` | Generate packing transaction number |
| GET | `/main_transaction/packing/calculate_fifo_prices` | `main_transaction.packing.calculate_fifo_prices` | `PackingController@calculateFifoPrices` | Calculate FIFO prices for materials |
| GET | `/main_transaction/packing/get_bundle_price` | `main_transaction.packing.get_bundle_price` | `PackingController@getBundlePrice` | Get bundle product price |

## Permissions

All routes require the user to be authenticated. Specific permissions are checked within each controller method:

- `packing.read` - Required for viewing packing records
- `packing.write` - Required for creating and updating packing records
- `packing.delete` - Required for deleting packing records
- `packing.export` - Required for exporting packing data

## Usage Examples

### Accessing the Packing Index Page
```
GET /main_transaction/packing/index
```

### Fetching Data for DataTable (AJAX)
```javascript
$.ajax({
    url: '/main_transaction/packing/index_data_table',
    method: 'GET',
    data: {
        date: '2024-01-01 - 2024-01-31',
        warehouse: 1,
        product: 5,
        packing_no: 'PACK-'
    },
    success: function(response) {
        // Handle response
    }
});
```

### Getting Bundle Components (AJAX)
```javascript
$.ajax({
    url: '/main_transaction/packing/get_bundle_components',
    method: 'GET',
    data: {
        bundle_product_id: 10,
        quantity_planned: 100
    },
    success: function(response) {
        // Handle response
    }
});
```

### Calculating FIFO Prices (AJAX)
```javascript
$.ajax({
    url: '/main_transaction/packing/calculate_fifo_prices',
    method: 'GET',
    data: {
        bundle_product_id: 10,
        quantity_planned: 100,
        product_requirements: JSON.stringify([
            {product_id: 1, quantity_required: 50},
            {product_id: 2, quantity_required: 30}
        ])
    },
    success: function(response) {
        // response.prices contains FIFO price calculations
    }
});
```

## Implementation Status

✅ **Implemented:**
- Routes registered in `routes/main_transaction.php`
- PackingController created with basic structure
- Index page with DataTable
- Menu item added to navigation
- Permission checks

⏳ **Pending:**
- Database migrations (t_packings, t_packing_items, t_packing_audit_logs)
- Packing domain services (PackingService, PackingItemService, etc.)
- Add and detail views
- Full CRUD implementation
- Export functionality

## Related Files

- **Routes:** `routes/main_transaction.php` (lines 303-321)
- **Controller:** `app/Http/Controllers/MainTransaction/PackingController.php`
- **Views:** `resources/views/pages/main_transaction/packing/`
- **Menu:** `resources/views/layouts/menu.blade.php` (lines 169-174)
