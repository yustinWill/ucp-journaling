-- ============================================
-- PACKING MODULE PERMISSIONS - AUTOMATED VERSION
-- ============================================
-- This version uses variables and can be run as a complete script
-- Compatible with MySQL 5.7+ and MariaDB

-- Start transaction
START TRANSACTION;

-- Step 1: Insert the Packing module
INSERT INTO `modules` (`name`, `slug`, `description`, `is_active`, `created_at`, `updated_at`)
VALUES (
    'Packing Management',
    'packing',
    'Packing product management and transactions',
    1,
    NOW(),
    NOW()
);

-- Step 2: Get the module ID
SET @module_id = LAST_INSERT_ID();

-- Step 3: Insert permissions for the Packing module
INSERT INTO `permissions` (`module_id`, `permission_type`, `created_at`, `updated_at`)
VALUES
    (@module_id, 'read', NOW(), NOW()),
    (@module_id, 'write', NOW(), NOW()),
    (@module_id, 'delete', NOW(), NOW()),
    (@module_id, 'export', NOW(), NOW());

-- Step 4: Get the Super Admin role ID (adjust the WHERE clause if needed)
SET @super_admin_role_id = (SELECT id FROM roles WHERE name = 'Super Admin' LIMIT 1);

-- Step 5: Assign all packing permissions to Super Admin
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `created_at`, `updated_at`)
SELECT
    @super_admin_role_id,
    p.id,
    NOW(),
    NOW()
FROM permissions p
WHERE p.module_id = @module_id;

-- Commit the transaction
COMMIT;

-- ============================================
-- Verification
-- ============================================
SELECT 'Packing Module Created:' as status;
SELECT * FROM modules WHERE slug = 'packing';

SELECT 'Packing Permissions Created:' as status;
SELECT
    p.id,
    p.permission_type,
    CONCAT('packing.', p.permission_type) as full_permission
FROM permissions p
WHERE p.module_id = (SELECT id FROM modules WHERE slug = 'packing')
ORDER BY p.permission_type;

SELECT 'Permissions Assigned to Roles:' as status;
SELECT
    r.name as role_name,
    CONCAT('packing.', p.permission_type) as permission
FROM role_permissions rp
JOIN permissions p ON rp.permission_id = p.id
JOIN modules m ON p.module_id = m.id
JOIN roles r ON rp.role_id = r.id
WHERE m.slug = 'packing'
ORDER BY r.name, p.permission_type;
