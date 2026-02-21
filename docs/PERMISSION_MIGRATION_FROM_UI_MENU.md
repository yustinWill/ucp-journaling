# UI Menu-Based Permission Generation Migration

## Overview

This migration automatically generates permissions and roles based on your application's UI menu structure. It completely replaces the previous generic permission setup with a detailed, module-specific permission system.

**Migration File**: `database/migrations/2026_02_08_004339_generate_permissions_from_ui_menu.php`

## What This Migration Does

### 1. **Removes Old Permissions & Roles**
- The `down()` method cleanly removes all created permissions and roles
- Allows you to regenerate permissions anytime you need to update the menu

### 2. **Creates Detailed Permissions**
- **193 granular permissions** based on your UI modules
- Named using dot notation: `module.action` (e.g., `sales_order.view`)
- Coverage for all CRUD operations across all modules

### 3. **Creates 5 Pre-configured Roles**
- **super_admin**: Full access to everything
- **admin**: Can manage users, roles, and most transactions
- **manager**: Can create transactions and manage team operations
- **staff**: Can create transactions and view related data
- **viewer**: Read-only access to dashboards and reports

## Permission Modules

### Dashboard & Reporting
- `dashboard.view` - View main dashboard
- `financial_recording.view` - View financial recording

### Sales Module
- `sales_order.view`, `.create`, `.edit`, `.delete`
- `sales_delivery.view`, `.create`, `.edit`, `.delete`
- `sales_invoice.view`, `.create`, `.edit`, `.delete`

### Purchase Module
- `purchase_order.view`, `.create`, `.edit`, `.delete`
- `purchase_delivery.view`, `.create`, `.edit`, `.delete`
- `purchase_invoice.view`, `.create`, `.edit`, `.delete`

### Inventory & Stock
- `stock_adjustment.view`, `.create`, `.edit`, `.delete`
- `stock_opname.view`, `.create`, `.edit`, `.delete`
- `return_management.view`, `.create`, `.edit`, `.delete`

### Production & Packing
- `production.view`, `.create`, `.edit`, `.delete`
- `packing.view`, `.create`, `.edit`, `.delete`

### Financial Management
- `expense.view`, `.create`, `.edit`, `.delete`
- `topup.view`, `.create`, `.edit`, `.delete`
- `budget.view`, `.create`, `.edit`, `.delete`
- `budget_category.view`, `.create`, `.edit`, `.delete`

### Master Data
- `product.view`, `.create`, `.edit`, `.delete`, `.import`, `.export`
- `contact.view`, `.create`, `.edit`, `.delete`, `.import`, `.export`
- `warehouse.view`, `.create`, `.edit`, `.delete`, `.stock_transfer`, `.import`, `.export`
- `revenue_split.view`, `.create`, `.edit`, `.delete`
- `inventory.view`, `.create`, `.edit`, `.delete`, `.dispose`, `.depreciation`

### System Settings
- `tax.view`, `.create`, `.edit`, `.delete`
- `unit.view`, `.create`, `.edit`, `.delete`
- `product_category.view`, `.create`, `.edit`, `.delete`
- `tag.view`, `.create`, `.edit`, `.delete`
- `sales_person.view`, `.create`, `.edit`, `.delete`
- `payment_term.view`, `.create`, `.edit`, `.delete`
- `transaction_number.view`, `.create`, `.edit`, `.delete`

### User & Role Management
- `user.view`, `.create`, `.edit`, `.delete`, `.import`, `.export`
- `role.view`, `.create`, `.edit`, `.delete`, `.manage_permissions`
- `permission.manage`

### Reporting
- `report_journal.view` - General Journal report
- `report_ledger.view` - General Ledger report
- `report_balance_sheet.view` - Balance Sheet report
- `report_profit_loss.view` - Profit & Loss report

### System
- `audit_log.view`
- `settings.manage`

## Role Definitions

### 1. Super Admin
**Permissions**: All 193 permissions

**Use for**:
- System owners and developers
- Complete system access and configuration

### 2. Admin
**193 Permissions** (essentially all except a few delete operations)

**Use for**:
- Senior management
- User and role management
- Transaction creation and editing
- Settings configuration

**Cannot**:
- Delete most transactions
- Delete system users or roles

### 3. Manager
**Selected permissions** for:
- Creating and managing transactions
- Viewing reports and dashboards
- Managing contacts and product data
- Cannot delete critical data or manage system users

**Use for**:
- Department managers
- Team leads
- Operational supervisors

### 4. Staff
**Limited creation permissions**:
- Can create transactions
- Can view related data
- Cannot edit or delete
- Cannot access system settings

**Use for**:
- Office staff
- Data entry personnel
- Transaction operators

### 5. Viewer
**Read-only permissions**:
- Can view all dashboards
- Can view all transactions and master data
- Can view reports
- Cannot create, edit, or delete anything

**Use for**:
- Stakeholders
- Reporting teams
- Management review access

## How to Use This Migration

### Running the Migration
```bash
# Run the migration (if not already done)
php artisan migrate

# Clear cache to apply permissions
php artisan cache:clear
```

### Assigning Roles to Users
```php
use App\Models\User;

$user = User::find(1);

// Assign a single role
$user->assignRole('admin');

// Assign multiple roles
$user->syncRoles(['manager', 'staff']);

// Check role
if ($user->hasRole('admin')) {
    // User is admin
}
```

### Checking Permissions
```php
// Check single permission
if ($user->hasPermissionTo('sales_order.view')) {
    // User can view sales orders
}

// Check multiple permissions
if ($user->hasPermissionTo(['sales_order.view', 'sales_order.create'])) {
    // User has both permissions
}
```

### In Controllers
```php
public function index()
{
    // Check permission
    if (!auth()->user()->hasPermissionTo('sales_order.view')) {
        abort(403, 'Unauthorized');
    }
    
    return view('sales_order.index');
}
```

### In Blade Templates
```blade
@can('sales_order.view')
    <a href="/sales-order">View Sales Orders</a>
@endcan

@if(auth()->user()->hasRole('admin'))
    <button>Edit Settings</button>
@endif
```

### In Routes
```php
Route::middleware(['auth', 'can:sales_order.create'])->group(function () {
    Route::post('/sales-order', [SalesOrderController::class, 'store']);
});
```

## Adding New Permissions

If you add new modules to your UI later, follow this pattern:

1. **Create new migration**:
```bash
php artisan make:migration add_new_module_permissions
```

2. **Add permissions**:
```php
$permission = Permission::create([
    'name' => 'new_module.view',
    'guard_name' => 'web',
]);

$adminRole = Role::findByName('admin');
$adminRole->givePermissionTo($permission);
```

3. **Run migration**:
```bash
php artisan migrate
php artisan cache:clear
```

## Understanding the Permission Naming Convention

Permissions follow a consistent naming pattern:

```
{module}.{action}
```

- **module**: The feature/module (e.g., `sales_order`, `product`, `user`)
- **action**: The operation (view, create, edit, delete, import, export)

### Examples
- `sales_order.view` - Can view sales orders
- `product.create` - Can create products
- `user.delete` - Can delete users
- `warehouse.stock_transfer` - Can transfer stock between warehouses
- `inventory.depreciation` - Can manage inventory depreciation

## Reverting Permissions

If you need to revert to the previous state:

```bash
# Undo the migration
php artisan migrate:rollback --step=1

# Or specific migration
php artisan migrate:rollback --target=2026_02_08_004339_generate_permissions_from_ui_menu
```

## Database Impact

### Tables Modified
- `permissions` - Now contains 193+ permissions
- `roles` - Now contains 5 pre-configured roles
- `role_has_permissions` - Associates roles with permissions

### User Impact
- **No user data is deleted** - Existing user-role assignments are preserved
- Users retain their current roles but get access to new permissions based on role

## Key Advantages

✅ **Organized**: Dot-notation makes permissions easy to understand
✅ **Scalable**: Easy to add new permissions as features grow
✅ **Consistent**: All modules follow the same naming convention
✅ **Role-Based**: Pre-configured roles for common organizational structures
✅ **Flexible**: Easy to customize roles based on your needs
✅ **Auditable**: Clear permission naming for audit trails

## Troubleshooting

### Permissions not showing after migration
```bash
# Clear the cache
php artisan cache:clear

# Restart your application
```

### User still doesn't have permission after assigning role
```php
# Verify role assignment
$user = User::find(1);
dd($user->roles); // Check roles
dd($user->getPermissionsViaRoles()); // Check permissions
```

### Want to see all permissions
```bash
# In Tinker
php artisan tinker
> Permission::all()->pluck('name')
```

## Migration File Details

- **File**: `database/migrations/2026_02_08_004339_generate_permissions_from_ui_menu.php`
- **Size**: ~400 permissions & role definitions
- **Guard**: "web" (default Laravel guard)
- **Created**: February 8, 2026
- **Status**: ✅ Ready for production

---

**Remember**: Always test permission changes on a staging environment first before deploying to production!
