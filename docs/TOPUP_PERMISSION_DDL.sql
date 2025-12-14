-- ============================================
-- Top Up Module - Permission DDL
-- ============================================
-- This script adds the Top Up module and its permissions to the system
-- Run this after the Top Up tables are created
-- ============================================

-- Step 1: Insert Top Up Module
-- ============================================
INSERT INTO `modules` (
    `name`,
    `slug`,
    `description`,
    `is_active`,
    `created_at`,
    `updated_at`
) VALUES (
    'main_transaction_topup',
    'main-transaction-topup',
    'Main Transaction Top Up Payment',
    1,
    NOW(),
    NOW()
);

-- Step 2: Get the module ID (for reference)
-- ============================================
-- Run this to verify the module was created:
-- SELECT * FROM modules WHERE name = 'main_transaction_topup';

-- Step 3: Insert Permissions for Top Up Module
-- ============================================
-- Replace @module_id with the actual module ID from Step 2

-- METHOD 1: Using the module ID directly (replace XXX with actual ID)
INSERT INTO `permissions` (
    `module_id`,
    `permission_type`,
    `name`,
    `created_at`,
    `updated_at`
) VALUES
    -- Read permission
    (
        (SELECT id FROM modules WHERE name = 'main_transaction_topup'),
        'read',
        'main_transaction_topup.read',
        NOW(),
        NOW()
    ),
    -- Write permission
    (
        (SELECT id FROM modules WHERE name = 'main_transaction_topup'),
        'write',
        'main_transaction_topup.write',
        NOW(),
        NOW()
    ),
    -- Delete permission
    (
        (SELECT id FROM modules WHERE name = 'main_transaction_topup'),
        'delete',
        'main_transaction_topup.delete',
        NOW(),
        NOW()
    ),
    -- Approval permission (if needed for workflow)
    (
        (SELECT id FROM modules WHERE name = 'main_transaction_topup'),
        'approval',
        'main_transaction_topup.approval',
        NOW(),
        NOW()
    );

-- Step 4: Verify Permissions
-- ============================================
-- Run this to verify permissions were created:
SELECT
    m.id as module_id,
    m.name as module_name,
    p.id as permission_id,
    p.permission_type,
    p.name as permission_name,
    p.created_at
FROM permissions p
JOIN modules m ON p.module_id = m.id
WHERE m.name = 'main_transaction_topup'
ORDER BY p.permission_type;

-- Step 5: Grant Permissions to a Role (Example)
-- ============================================
-- Replace @role_id with actual role ID (e.g., 1 for Super Admin)
-- Replace @permission_ids with actual permission IDs from Step 4

-- Example: Grant all Top Up permissions to Super Admin role (role_id = 1)
INSERT INTO `role_permissions` (
    `role_id`,
    `permission_id`,
    `created_at`,
    `updated_at`
)
SELECT
    1 as role_id,  -- Change this to your role ID
    p.id as permission_id,
    NOW(),
    NOW()
FROM permissions p
JOIN modules m ON p.module_id = m.id
WHERE m.name = 'main_transaction_topup';

-- ============================================
-- Rollback Queries
-- ============================================

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

-- ============================================
-- Verification Queries
-- ============================================

-- 1. Check if module exists
SELECT * FROM modules WHERE name = 'main_transaction_topup';

-- 2. Check all permissions for Top Up
SELECT
    m.name as module_name,
    p.permission_type,
    p.name as permission_name
FROM permissions p
JOIN modules m ON p.module_id = m.id
WHERE m.name = 'main_transaction_topup';

-- 3. Check which roles have Top Up permissions
SELECT
    r.id as role_id,
    r.name as role_name,
    p.permission_type,
    p.name as permission_name
FROM role_permissions rp
JOIN roles r ON rp.role_id = r.id
JOIN permissions p ON rp.permission_id = p.id
JOIN modules m ON p.module_id = m.id
WHERE m.name = 'main_transaction_topup'
ORDER BY r.name, p.permission_type;

-- 4. Check users with Top Up access (through roles)
SELECT DISTINCT
    u.id as user_id,
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
