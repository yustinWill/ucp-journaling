-- ============================================
-- Packing Module Permissions
-- ============================================
-- This SQL script creates the packing module and its permissions
-- Run this script manually in your database

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

-- Step 2: Get the module ID (you'll need to replace @module_id with the actual ID after running step 1)
-- SELECT @module_id := id FROM modules WHERE slug = 'packing';
-- Or manually check: SELECT id FROM modules WHERE slug = 'packing';

-- Step 3: Insert permissions for the Packing module
-- Replace {module_id} with the actual module ID from step 2

INSERT INTO `permissions` (`module_id`, `permission_type`, `created_at`, `updated_at`)
VALUES
    ({module_id}, 'read', NOW(), NOW()),
    ({module_id}, 'write', NOW(), NOW()),
    ({module_id}, 'delete', NOW(), NOW()),
    ({module_id}, 'export', NOW(), NOW());

-- ============================================
-- Optional: Assign permissions to Super Admin role
-- ============================================
-- Step 4: Get permission IDs
-- SELECT * FROM permissions WHERE module_id = {module_id};

-- Step 5: Assign to Super Admin role (assuming role_id = 1 for Super Admin)
-- Replace {permission_id_1}, {permission_id_2}, etc. with actual permission IDs

INSERT INTO `role_permissions` (`role_id`, `permission_id`, `created_at`, `updated_at`)
VALUES
    (1, {permission_id_1}, NOW(), NOW()),  -- packing.read
    (1, {permission_id_2}, NOW(), NOW()),  -- packing.write
    (1, {permission_id_3}, NOW(), NOW()),  -- packing.delete
    (1, {permission_id_4}, NOW(), NOW());  -- packing.export

-- ============================================
-- Verification Queries
-- ============================================
-- Run these to verify the data was inserted correctly

-- Check module
-- SELECT * FROM modules WHERE slug = 'packing';

-- Check permissions
-- SELECT p.*, m.name as module_name, m.slug as module_slug
-- FROM permissions p
-- JOIN modules m ON p.module_id = m.id
-- WHERE m.slug = 'packing';

-- Check role permissions (for Super Admin)
-- SELECT r.name as role_name, m.slug as module, p.permission_type
-- FROM role_permissions rp
-- JOIN permissions p ON rp.permission_id = p.id
-- JOIN modules m ON p.module_id = m.id
-- JOIN roles r ON rp.role_id = r.id
-- WHERE m.slug = 'packing';
