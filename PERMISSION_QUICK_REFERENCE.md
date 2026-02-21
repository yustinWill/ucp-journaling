# Permission System - Quick Reference Card

## 🎯 Permission Naming Pattern
```
module.action
```
Example: `sales_order.view`, `product.create`, `user.delete`

## 📋 Modules & Permissions

| Module | Permissions |
|--------|-------------|
| **Dashboard** | view |
| **Sales Order** | view, create, edit, delete |
| **Sales Delivery** | view, create, edit, delete |
| **Sales Invoice** | view, create, edit, delete |
| **Purchase Order** | view, create, edit, delete |
| **Purchase Delivery** | view, create, edit, delete |
| **Purchase Invoice** | view, create, edit, delete |
| **Expense** | view, create, edit, delete |
| **Stock Adjustment** | view, create, edit, delete |
| **Stock Opname** | view, create, edit, delete |
| **Return Management** | view, create, edit, delete |
| **Production** | view, create, edit, delete |
| **Packing** | view, create, edit, delete |
| **Product** | view, create, edit, delete, import, export |
| **Contact** | view, create, edit, delete, import, export |
| **Warehouse** | view, create, edit, delete, stock_transfer, import, export |
| **Budget** | view, create, edit, delete |
| **Budget Category** | view, create, edit, delete |
| **Inventory** | view, create, edit, delete, dispose, depreciation |
| **Revenue Split** | view, create, edit, delete |
| **Tax** | view, create, edit, delete |
| **Unit** | view, create, edit, delete |
| **Product Category** | view, create, edit, delete |
| **Tag** | view, create, edit, delete |
| **Sales Person** | view, create, edit, delete |
| **Payment Term** | view, create, edit, delete |
| **Transaction Number** | view, create, edit, delete |
| **User** | view, create, edit, delete, import, export |
| **Role** | view, create, edit, delete, manage_permissions |
| **Reports** | journal.view, ledger.view, balance_sheet.view, profit_loss.view |
| **System** | audit_log.view, settings.manage |

## 👥 Available Roles

### Super Admin
- Permissions: 137 (all)
- Best for: System administrators, owners
- Access: Everything

### Admin
- Permissions: 73
- Best for: Managers, supervisors
- Restricted: Cannot delete users/roles

### Manager
- Permissions: 33
- Best for: Department heads, team leads
- Can: Create transactions, view data
- Cannot: Manage users, system settings

### Staff
- Permissions: 16
- Best for: Data entry, operators
- Can: Create transactions
- Cannot: Edit, delete, manage users

### Viewer
- Permissions: 21
- Best for: Stakeholders, auditors
- Can: View everything
- Cannot: Create, edit, delete

## 💻 Code Examples

### Controller Permission Check
```php
if (!auth()->user()->hasPermissionTo('sales_order.view')) {
    abort(403);
}
```

### Blade Template Check
```blade
@can('sales_order.create')
    <a href="{{ route('sales_order.create') }}">Create Order</a>
@endcan
```

### Route Protection
```php
Route::middleware(['can:sales_order.view'])->group(function () {
    Route::get('/sales-order', /* ... */);
});
```

### Check Role
```php
if (auth()->user()->hasRole('admin')) {
    // Admin-only code
}
```

### Assign Role
```php
$user->assignRole('manager');
$user->syncRoles(['manager', 'staff']);
```

## 🔧 Common Tasks

### Check if user has permission
```php
auth()->user()->hasPermissionTo('sales_order.view')
```

### Check if user has role
```php
auth()->user()->hasRole('admin')
```

### Get all user permissions
```php
auth()->user()->getAllPermissions()
```

### Get all user roles
```php
auth()->user()->getRoleNames()
```

### Assign role to user
```php
$user->assignRole('manager')
```

### Remove role from user
```php
$user->removeRole('staff')
```

### Give direct permission to user
```php
$user->givePermissionTo('sales_order.delete')
```

## 📊 Permission Count

| Item | Count |
|------|-------|
| **Total Permissions** | 156 |
| **Total Roles** | 6 |
| **Modules Covered** | 30+ |

## 🔍 Permission Lookup

Need to check what permissions a user needs?

| Task | Permission |
|------|-----------|
| View sales orders list | `sales_order.view` |
| Create new sales order | `sales_order.create` |
| Edit existing order | `sales_order.edit` |
| Delete order | `sales_order.delete` |
| Import products | `product.import` |
| Export products | `product.export` |
| Transfer stock | `warehouse.stock_transfer` |
| Manage roles | `role.create`, `role.edit`, `role.manage_permissions` |
| View reports | `report_journal.view`, `report_balance_sheet.view`, etc. |
| Manage settings | `settings.manage` |

## 📚 Documentation Links

| Document | Purpose |
|----------|---------|
| `PERMISSION_MIGRATION_FROM_UI_MENU.md` | Full migration details |
| `SPATIE_PERMISSION_GUIDE.md` | Complete permission guide |
| `app/Permissions/PermissionReference.php` | PHP constants & examples |

## ⚠️ Important Commands

```bash
# Clear cache after changes
php artisan cache:clear

# Run migration (if not run)
php artisan migrate

# Check permissions in Tinker
php artisan tinker
> Permission::count()
> Role::all()
```

## 🚀 Quick Start

### 1. Assign role to user
```php
User::find(1)->assignRole('admin');
```

### 2. Check permission in controller
```php
if (!auth()->user()->hasPermissionTo('sales_order.view')) {
    abort(403);
}
```

### 3. Show content in blade
```blade
@can('sales_order.create')
    Show content
@endcan
```

### 4. Clear cache
```bash
php artisan cache:clear
```

---

**For detailed information**, see:
- `PERMISSION_MIGRATION_FROM_UI_MENU.md`
- `app/Permissions/PermissionReference.php`
- Database: `permissions`, `roles`, `role_has_permissions` tables
