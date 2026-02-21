# Spatie Permission Implementation Guide

## Overview

Spatie Permission is a powerful package for managing user roles and permissions in Laravel applications. This guide covers the implementation in your Riyo App.

## Installation Status

✅ **Installed**: `spatie/laravel-permission` (v6.24.0)
✅ **Configuration**: Published to `config/permission.php`
✅ **Migrations**: Already run and tables created
✅ **User Model**: Updated with `HasRoles` and `HasPermissions` traits
✅ **Helper Class**: Created at `app/Helpers/PermissionHelper.php`
✅ **Seeder**: Initial roles and permissions set up

## Database Tables

The following tables were created:

- `permissions` - Stores all available permissions
- `roles` - Stores all available roles
- `model_has_permissions` - Maps permissions directly to models (users)
- `model_has_roles` - Maps roles to models (users)
- `role_has_permissions` - Maps permissions to roles

## Default Roles

Four roles have been created:

### 1. **super_admin**
- Full access to all permissions
- Use for system administrators

### 2. **admin**
- User management, viewing transactions and reports
- Full dashboard access

### 3. **manager**
- Can view and create transactions
- Access to reports and dashboard
- Limited administrative capabilities

### 4. **user**
- Basic access to dashboard and view transactions only

## Default Permissions

### User Management
- `view_users` - View user list
- `create_users` - Create new users
- `edit_users` - Edit user information
- `delete_users` - Delete users

### Role & Permission Management
- `manage_roles` - Manage all roles
- `manage_permissions` - Manage all permissions

### Dashboard
- `view_dashboard` - Access dashboard

### Master Data
- `view_master_data` - View master data
- `create_master_data` - Create master data
- `edit_master_data` - Edit master data
- `delete_master_data` - Delete master data

### Transactions
- `view_transactions` - View transactions
- `create_transactions` - Create transactions
- `edit_transactions` - Edit transactions
- `delete_transactions` - Delete transactions

### Reporting
- `view_reports` - View reports
- `export_reports` - Export reports

### Administration
- `view_audit_logs` - View audit logs
- `manage_settings` - Manage application settings

## Usage Examples

### 1. Check User Permissions

```php
use App\Helpers\PermissionHelper;
use App\Models\User;

$user = User::find(1);

// Check single permission
if (PermissionHelper::hasPermission($user, 'view_users')) {
    // User has permission
}

// Direct method on user
if ($user->hasPermissionTo('view_users')) {
    // User has permission
}
```

### 2. Check User Roles

```php
// Check single role
if (PermissionHelper::hasRole($user, 'admin')) {
    // User is admin
}

// Check any role
if (PermissionHelper::hasAnyRole($user, ['admin', 'super_admin'])) {
    // User is either admin or super_admin
}

// Check all roles
if (PermissionHelper::hasAllRoles($user, ['admin', 'manager'])) {
    // User has both roles
}

// Direct method
if ($user->hasRole('admin')) {
    // User is admin
}
```

### 3. Assign Roles

```php
// Assign single role
PermissionHelper::assignRole($user, 'admin');

// Assign multiple roles
PermissionHelper::assignRoles($user, ['admin', 'manager']);

// Direct method
$user->assignRole('admin');
$user->assignRole(['admin', 'manager']);
```

### 4. Sync Roles (Replace existing)

```php
// This will replace all existing roles
PermissionHelper::syncRoles($user, ['manager', 'user']);

// Direct method
$user->syncRoles(['manager', 'user']);
```

### 5. Remove Role

```php
PermissionHelper::removeRole($user, 'admin');

// Direct method
$user->removeRole('admin');
```

### 6. Get User Permissions & Roles

```php
// Get all permissions (from roles + direct)
$permissions = PermissionHelper::getUserPermissions($user);

// Get all roles
$roles = PermissionHelper::getUserRoles($user);

// Direct methods
$permissions = $user->getAllPermissions();
$roles = $user->getRoleNames();
```

### 7. Assign Permissions to Roles

```php
use Spatie\Permission\Models\Role;

$adminRole = Role::findByName('admin');

// Give permission to role
$adminRole->givePermissionTo('edit_users');

// Or use helper
PermissionHelper::assignPermissionToRole($adminRole, 'edit_users');
```

## Using Permissions in Controllers

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        // Check permission before showing users
        if (!auth()->user()->hasPermissionTo('view_users')) {
            abort(403, 'Unauthorized');
        }

        return view('users.index');
    }

    public function store()
    {
        // Check permission before creating
        if (!auth()->user()->hasPermissionTo('create_users')) {
            abort(403, 'Unauthorized');
        }

        // Create user...
    }
}
```

## Using Permissions in Middleware

Create a custom middleware to check permissions:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    public function handle(Request $request, Closure $next, ...$permissions)
    {
        foreach ($permissions as $permission) {
            if (auth()->check() && auth()->user()->hasPermissionTo($permission)) {
                return $next($request);
            }
        }

        abort(403, 'Unauthorized');
    }
}
```

Register in `app/Http/Kernel.php`:

```php
protected $routeMiddleware = [
    // ... other middleware
    'permission' => \App\Http\Middleware\CheckPermission::class,
];
```

Use in routes:

```php
Route::middleware(['auth', 'permission:view_users'])->group(function () {
    Route::get('/users', [UserController::class, 'index']);
});
```

## Using Permissions in Blade Templates

```blade
@if($user->hasPermissionTo('view_users'))
    <a href="/users">View Users</a>
@endif

@if($user->hasRole('admin'))
    <a href="/admin">Admin Panel</a>
@endif

@can('edit_users')
    <button>Edit User</button>
@endcan

@cannot('delete_users')
    <p>You cannot delete users</p>
@endcannot
```

## Using Permissions in Policies

```php
<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function view(User $user)
    {
        return $user->hasPermissionTo('view_users');
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('create_users');
    }

    public function update(User $user)
    {
        return $user->hasPermissionTo('edit_users');
    }

    public function delete(User $user)
    {
        return $user->hasPermissionTo('delete_users');
    }
}
```

## Creating New Permissions

```php
use Spatie\Permission\Models\Permission;

// Create new permission
Permission::create([
    'name' => 'export_invoices',
    'guard_name' => 'web',
]);

// Or use helper
PermissionHelper::createPermission('export_invoices');
```

## Creating New Roles

```php
use Spatie\Permission\Models\Role;

// Create new role
$role = Role::create([
    'name' => 'accountant',
    'guard_name' => 'web',
]);

// Assign permissions to role
$role->givePermissionTo(['view_reports', 'export_reports']);

// Or use helper
$role = PermissionHelper::createRole('accountant');
PermissionHelper::assignPermissionToRole($role, 'view_reports');
```

## Cache Management

Spatie Permission uses caching for better performance. If you make changes to permissions/roles, clear the cache:

```php
// In code
app()['cache']->forget('spatie.permission.cache');

// Or artisan command
php artisan cache:clear
```

## Best Practices

1. **Use Meaningful Names**: Name permissions based on actions (view, create, edit, delete)
2. **Organize by Resource**: Group permissions by the resource they protect (users, reports, etc.)
3. **Use Helper Class**: Use the `PermissionHelper` class for consistency
4. **Cache Permissions**: Spatie Permission caches permissions automatically
5. **Check in Controllers**: Always check permissions in controllers before performing actions
6. **Use Policies**: For more complex authorization logic, use Laravel Policies
7. **Audit Changes**: Log role and permission changes for security

## Testing Permissions

Example test:

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class UserPermissionTest extends TestCase
{
    public function test_user_can_view_users_with_permission()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view_users');

        $this->assertTrue($user->hasPermissionTo('view_users'));
    }

    public function test_admin_role_has_all_permissions()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->assertTrue($user->hasRole('admin'));
    }
}
```

## Troubleshooting

### Permissions not working after assignment

Clear cache:
```bash
php artisan cache:clear
```

### Role not found error

Ensure role exists:
```php
$role = Role::where('name', 'admin')->first();
if (!$role) {
    $role = Role::create(['name' => 'admin']);
}
```

### Permission denied even with correct role

Check the guard name matches (default is 'web'):
```php
$user->hasPermissionTo('view_users', 'web');
```

## Configuration

Edit `config/permission.php` to customize:

- Model classes
- Table names
- Column names
- Guard names
- Cache expiration

## Additional Resources

- [Spatie Permission Documentation](https://spatie.be/docs/laravel-permission/)
- [Laravel Authorization Docs](https://laravel.com/docs/authorization)
- [Policy Authorization](https://laravel.com/docs/policies)

---

**Last Updated**: 2026-02-08
