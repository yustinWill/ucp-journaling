# Packing Module Permissions Setup

This folder contains SQL scripts to set up permissions for the Packing module.

## Files

1. **packing_permissions_auto.sql** - Automated script (RECOMMENDED)
2. **packing_permissions_step_by_step.sql** - Manual step-by-step guide
3. **packing_permissions.sql** - Template with placeholders

## Quick Start (Recommended Method)

Run the automated script in your MySQL client:

```sql
-- Option 1: Run the entire file
SOURCE /path/to/riyo-app/database/sql/packing_permissions_auto.sql;

-- Option 2: Copy and paste the contents
-- (Open packing_permissions_auto.sql and copy all contents into your SQL client)
```

This will:
1. Create the "Packing Management" module
2. Create 4 permissions (read, write, delete, export)
3. Assign all permissions to Super Admin role
4. Show verification results

## Manual Method (Step by Step)

If you prefer to run each step manually, use `packing_permissions_step_by_step.sql`:

### Step 1: Create Module
```sql
INSERT INTO `modules` (`name`, `slug`, `description`, `is_active`, `created_at`, `updated_at`)
VALUES (
    'Packing Management',
    'packing',
    'Packing product management and transactions',
    1,
    NOW(),
    NOW()
);
```

### Step 2: Get Module ID
```sql
SELECT id, name, slug FROM modules WHERE slug = 'packing';
```
Note the `id` value (e.g., 15)

### Step 3: Create Permissions
Replace `{module_id}` with the ID from Step 2:
```sql
INSERT INTO `permissions` (`module_id`, `permission_type`, `created_at`, `updated_at`)
VALUES
    (15, 'read', NOW(), NOW()),
    (15, 'write', NOW(), NOW()),
    (15, 'delete', NOW(), NOW()),
    (15, 'export', NOW(), NOW());
```

### Step 4: Get Permission IDs
```sql
SELECT p.id, m.name as module_name, p.permission_type
FROM permissions p
JOIN modules m ON p.module_id = m.id
WHERE m.slug = 'packing'
ORDER BY p.id;
```
Note all the `id` values

### Step 5: Get Super Admin Role ID
```sql
SELECT id, name FROM roles WHERE name LIKE '%Super Admin%';
```
Note the role `id`

### Step 6: Assign Permissions
Replace values with actual IDs:
```sql
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `created_at`, `updated_at`)
VALUES
    (1, 50, NOW(), NOW()),  -- packing.read
    (1, 51, NOW(), NOW()),  -- packing.write
    (1, 52, NOW(), NOW()),  -- packing.delete
    (1, 53, NOW(), NOW());  -- packing.export
```

## Permissions Created

The following permissions will be created:

| Permission | Description |
|------------|-------------|
| `packing.read` | View packing records and list |
| `packing.write` | Create and update packing records |
| `packing.delete` | Delete packing records |
| `packing.export` | Export packing data |

## Verification

After running the scripts, verify with:

```sql
-- Check module exists
SELECT * FROM modules WHERE slug = 'packing';

-- Check permissions
SELECT
    p.id,
    p.permission_type,
    CONCAT('packing.', p.permission_type) as full_permission
FROM permissions p
JOIN modules m ON p.module_id = m.id
WHERE m.slug = 'packing';

-- Check role assignments
SELECT
    r.name as role_name,
    CONCAT('packing.', p.permission_type) as permission
FROM role_permissions rp
JOIN permissions p ON rp.permission_id = p.id
JOIN modules m ON p.module_id = m.id
JOIN roles r ON rp.role_id = r.id
WHERE m.slug = 'packing'
ORDER BY r.name;
```

## Usage in Code

The permissions are already used in the PackingController:

```php
// Check permissions in controller
$this->hasPermission('packing.read')   // View access
$this->hasPermission('packing.write')  // Create/Update access
$this->hasPermission('packing.delete') // Delete access
$this->hasPermission('packing.export') // Export access
```

## Troubleshooting

### Issue: "Super Admin role not found"
**Solution:** Update the role name in the script to match your database:
```sql
SET @super_admin_role_id = (SELECT id FROM roles WHERE name = 'Administrator' LIMIT 1);
```

### Issue: "Module already exists"
**Solution:** The module might already be created. Check with:
```sql
SELECT * FROM modules WHERE slug = 'packing';
```
If it exists, skip Step 1 and use the existing module_id.

### Issue: "Permissions already exist"
**Solution:** Check existing permissions:
```sql
SELECT * FROM permissions WHERE module_id = (SELECT id FROM modules WHERE slug = 'packing');
```
If they exist, skip Step 3 and use existing permission IDs for Step 6.

## Rollback

If you need to remove the packing permissions:

```sql
-- Delete role permissions
DELETE rp FROM role_permissions rp
JOIN permissions p ON rp.permission_id = p.id
JOIN modules m ON p.module_id = m.id
WHERE m.slug = 'packing';

-- Delete permissions
DELETE p FROM permissions p
JOIN modules m ON p.module_id = m.id
WHERE m.slug = 'packing';

-- Delete module
DELETE FROM modules WHERE slug = 'packing';
```
