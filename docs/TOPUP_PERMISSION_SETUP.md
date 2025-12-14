# Top Up Module - Permission Setup Guide

## Overview
This guide explains how to set up permissions for the Top Up module in the system.

## Files Created

### 1. Migration File (Recommended)
**File**: `database/migrations/2025_12_14_100002_seed_topup_module_and_permissions.php`

This is the **recommended approach** as it integrates with Laravel's migration system.

### 2. SQL DDL File (Manual Alternative)
**File**: `docs/TOPUP_PERMISSION_DDL.sql`

Use this if you prefer to run SQL queries manually in your database client.

## Setup Methods

### Method 1: Using Laravel Migration (Recommended)

This is the easiest and safest method.

#### Step 1: Run the migration
```bash
php artisan migrate --path=database/migrations/2025_12_14_100002_seed_topup_module_and_permissions.php
```

#### Step 2: Verify the migration
```bash
php artisan migrate:status | grep topup
```

Expected output:
```
2025_12_14_100002_seed_topup_module_and_permissions ... Ran
```

#### Step 3: Verify in database
```bash
php artisan tinker
```

Then run:
```php
DB::table('modules')->where('name', 'main_transaction_topup')->first();
DB::table('permissions')->join('modules', 'permissions.module_id', '=', 'modules.id')->where('modules.name', 'main_transaction_topup')->get(['permissions.*']);
```

### Method 2: Using SQL DDL (Manual)

If you prefer to run SQL queries directly:

#### Step 1: Open your database client
- phpMyAdmin
- MySQL Workbench
- HeidiSQL
- Command line: `mysql -u root -p`

#### Step 2: Run the DDL
Copy and paste the queries from `docs/TOPUP_PERMISSION_DDL.sql` in order:

1. Insert module
2. Insert permissions
3. (Optional) Grant permissions to roles

#### Step 3: Verify
Run the verification queries at the end of the DDL file.

## Permission Structure

### Module Information
- **Name**: `main_transaction_topup`
- **Slug**: `main-transaction-topup`
- **Description**: Main Transaction Top Up Payment

### Permissions Created
The following permissions are created automatically:

| Permission Type | Permission Name | Description |
|----------------|-----------------|-------------|
| `read` | `main_transaction_topup.read` | View Top Up data, access index page, view details |
| `write` | `main_transaction_topup.write` | Create and edit Top Up records |
| `delete` | `main_transaction_topup.delete` | Delete Top Up records (OPEN status only) |
| `approval` | `main_transaction_topup.approval` | Approve Top Up transactions (future use) |

## Granting Permissions to Roles

### Option 1: Through Admin UI
1. Login as Super Admin
2. Go to: **Settings → Role Management**
3. Select the role you want to grant permissions to
4. Find **Main Transaction Top Up Payment** module
5. Check the permissions you want to grant:
   - ☑ Read - View Top Up data
   - ☑ Write - Create/Edit Top Up
   - ☑ Delete - Delete Top Up
   - ☑ Approval - Approve Top Up (if applicable)
6. Click **Save**

### Option 2: Using SQL
Grant all Top Up permissions to a specific role:

```sql
-- Replace 1 with your role ID
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `created_at`, `updated_at`)
SELECT
    1 as role_id,  -- Change this to target role ID
    p.id as permission_id,
    NOW(),
    NOW()
FROM permissions p
JOIN modules m ON p.module_id = m.id
WHERE m.name = 'main_transaction_topup';
```

### Option 3: Using Tinker
```bash
php artisan tinker
```

```php
// Get the role
$role = DB::table('roles')->where('name', 'Super Admin')->first();

// Get all Top Up permissions
$permissions = DB::table('permissions')
    ->join('modules', 'permissions.module_id', '=', 'modules.id')
    ->where('modules.name', 'main_transaction_topup')
    ->select('permissions.id')
    ->get();

// Grant permissions
foreach ($permissions as $permission) {
    DB::table('role_permissions')->insert([
        'role_id' => $role->id,
        'permission_id' => $permission->id,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}
```

## Controller Permission Checks

The TopUpController uses `PermissionHelper` to check permissions:

### Read Permission
- Required for: `index()`, `index_data_table()`, `detail()`
- Permission: `main_transaction_topup.read`

### Write Permission
- Required for: `add()`, `store()`, `edit()`, `update()`
- Permission: `main_transaction_topup.write`

### Delete Permission
- Required for: `destroy()`
- Permission: `main_transaction_topup.delete`

### Example Usage in Controller
```php
// Check read permission
if (!PermissionHelper::hasPermission('main_transaction_topup', 'read')) {
    abort(403, 'Anda tidak memiliki akses untuk melihat data.');
}

// Check write permission
if (!PermissionHelper::hasPermission('main_transaction_topup', 'write')) {
    abort(403, 'Anda tidak memiliki akses untuk membuat data.');
}

// Check delete permission
if (!PermissionHelper::hasPermission('main_transaction_topup', 'delete')) {
    return response()->json(['message' => 'Unauthorized'], 403);
}
```

## Verification Queries

### 1. Check if module exists
```sql
SELECT * FROM modules WHERE name = 'main_transaction_topup';
```

Expected result:
```
id | name                      | slug                         | description                          | is_active
---|---------------------------|------------------------------|--------------------------------------|----------
XX | main_transaction_topup    | main-transaction-topup       | Main Transaction Top Up Payment      | 1
```

### 2. Check all permissions
```sql
SELECT
    m.name as module_name,
    p.permission_type,
    p.name as permission_name
FROM permissions p
JOIN modules m ON p.module_id = m.id
WHERE m.name = 'main_transaction_topup';
```

Expected result:
```
module_name              | permission_type | permission_name
-------------------------|-----------------|----------------------------------
main_transaction_topup   | read            | main_transaction_topup.read
main_transaction_topup   | write           | main_transaction_topup.write
main_transaction_topup   | delete          | main_transaction_topup.delete
main_transaction_topup   | approval        | main_transaction_topup.approval
```

### 3. Check role permissions
```sql
SELECT
    r.name as role_name,
    p.permission_type,
    p.name as permission_name
FROM role_permissions rp
JOIN roles r ON rp.role_id = r.id
JOIN permissions p ON rp.permission_id = p.id
JOIN modules m ON p.module_id = m.id
WHERE m.name = 'main_transaction_topup'
ORDER BY r.name, p.permission_type;
```

### 4. Check users with Top Up access
```sql
SELECT DISTINCT
    u.username,
    u.full_name,
    r.name as role_name,
    p.permission_type
FROM users u
JOIN user_roles ur ON u.id = ur.user_id
JOIN roles r ON ur.role_id = r.id
JOIN role_permissions rp ON r.id = rp.role_id
JOIN permissions p ON rp.permission_id = p.id
JOIN modules m ON p.module_id = m.id
WHERE m.name = 'main_transaction_topup'
ORDER BY u.username, p.permission_type;
```

## Troubleshooting

### Issue: "Anda tidak memiliki akses untuk melihat data"

**Cause**: User's role doesn't have `read` permission for Top Up module.

**Solution**:
1. Check if module exists:
   ```sql
   SELECT * FROM modules WHERE name = 'main_transaction_topup';
   ```

2. Check if permissions exist:
   ```sql
   SELECT * FROM permissions p
   JOIN modules m ON p.module_id = m.id
   WHERE m.name = 'main_transaction_topup';
   ```

3. Grant permission to user's role (replace role_id):
   ```sql
   INSERT INTO role_permissions (role_id, permission_id, created_at, updated_at)
   SELECT 1, p.id, NOW(), NOW()
   FROM permissions p
   JOIN modules m ON p.module_id = m.id
   WHERE m.name = 'main_transaction_topup'
   AND p.permission_type = 'read';
   ```

### Issue: Edit/Delete buttons not showing

**Cause**: User doesn't have `write` or `delete` permissions.

**Solution**:
1. Verify user has the required permissions
2. Grant write permission:
   ```sql
   INSERT INTO role_permissions (role_id, permission_id, created_at, updated_at)
   SELECT 1, p.id, NOW(), NOW()
   FROM permissions p
   JOIN modules m ON p.module_id = m.id
   WHERE m.name = 'main_transaction_topup'
   AND p.permission_type IN ('write', 'delete');
   ```

### Issue: Permission check returns false even after granting

**Cause**: Cache or session issue.

**Solution**:
1. Clear application cache:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan view:clear
   ```

2. Logout and login again
3. Check if permission is actually granted in database

## Rollback

### Using Migration
```bash
php artisan migrate:rollback --step=1
```

### Using SQL
```sql
-- Remove role permissions
DELETE rp FROM role_permissions rp
JOIN permissions p ON rp.permission_id = p.id
JOIN modules m ON p.module_id = m.id
WHERE m.name = 'main_transaction_topup';

-- Remove permissions
DELETE p FROM permissions p
JOIN modules m ON p.module_id = m.id
WHERE m.name = 'main_transaction_topup';

-- Remove module
DELETE FROM modules WHERE name = 'main_transaction_topup';
```

## Best Practices

1. **Always grant read permission first** - Users need this to see the menu and access the module
2. **Grant write permission separately** - Not all users should be able to create/edit
3. **Restrict delete permission** - Only trusted roles should have delete access
4. **Approval permission** - Reserve for managers/supervisors who need to approve transactions
5. **Test with non-admin user** - Always verify permissions work correctly for regular users
6. **Use role-based permissions** - Don't grant permissions directly to users, use roles

## Next Steps

After setting up permissions:

1. ✅ Run the migration or execute DDL
2. ✅ Verify module and permissions are created
3. ✅ Grant permissions to appropriate roles
4. ✅ Test access with different user roles
5. ✅ Verify menu appears for users with read permission
6. ✅ Verify CRUD operations respect permission levels

## Related Documentation

- **Top Up Module Summary**: `docs/TOPUP_MODULE_SUMMARY.md`
- **Top Up DDL**: `docs/TOPUP_DDL.md`
- **Top Up Implementation Status**: `docs/TOPUP_IMPLEMENTATION_STATUS.md`
- **Permission DDL**: `docs/TOPUP_PERMISSION_DDL.sql`
