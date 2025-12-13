-- ============================================
-- PACKING MODULE PERMISSIONS - STEP BY STEP
-- ============================================
-- Copy and paste each section into your SQL client

-- ============================================
-- STEP 1: Create the Packing Module
-- ============================================
INSERT INTO `modules` (`name`, `slug`, `description`, `is_active`, `created_at`, `updated_at`)
VALUES (
    'Packing Management',
    'packing',
    'Packing product management and transactions',
    1,
    NOW(),
    NOW()
);

-- ============================================
-- STEP 2: Get the Module ID
-- ============================================
-- Run this to get the module_id (note the ID for the next steps)
SELECT id, name, slug FROM modules WHERE slug = 'packing';

-- ============================================
-- STEP 3: Create Permissions
-- ============================================
-- IMPORTANT: Replace {module_id} with the actual ID from STEP 2
-- For example, if the module_id is 15, replace all {module_id} with 15

INSERT INTO `permissions` (`module_id`, `permission_type`, `created_at`, `updated_at`)
VALUES
    ({module_id}, 'read', NOW(), NOW()),
    ({module_id}, 'write', NOW(), NOW()),
    ({module_id}, 'delete', NOW(), NOW()),
    ({module_id}, 'export', NOW(), NOW());

-- ============================================
-- STEP 4: Verify Permissions Were Created
-- ============================================
-- Run this to see the permission IDs (note them for the next step)
SELECT p.id, m.name as module_name, p.permission_type
FROM permissions p
JOIN modules m ON p.module_id = m.id
WHERE m.slug = 'packing'
ORDER BY p.id;

-- ============================================
-- STEP 5: Get Super Admin Role ID
-- ============================================
-- Run this to check Super Admin role ID
SELECT id, name FROM roles WHERE name LIKE '%Super Admin%' OR name LIKE '%Admin%';

-- ============================================
-- STEP 6: Assign Permissions to Super Admin
-- ============================================
-- IMPORTANT: Replace {role_id} with your Super Admin role ID from STEP 5
-- IMPORTANT: Replace {perm_id_1}, {perm_id_2}, etc. with actual permission IDs from STEP 4

INSERT INTO `role_permissions` (`role_id`, `permission_id`, `created_at`, `updated_at`)
VALUES
    ({role_id}, {perm_id_1}, NOW(), NOW()),  -- packing.read
    ({role_id}, {perm_id_2}, NOW(), NOW()),  -- packing.write
    ({role_id}, {perm_id_3}, NOW(), NOW()),  -- packing.delete
    ({role_id}, {perm_id_4}, NOW(), NOW());  -- packing.export

-- ============================================
-- STEP 7: Verification
-- ============================================
-- Check everything is set up correctly

-- View all packing permissions
SELECT
    m.name as module_name,
    m.slug as module_slug,
    p.id as permission_id,
    p.permission_type
FROM permissions p
JOIN modules m ON p.module_id = m.id
WHERE m.slug = 'packing';

-- View role assignments
SELECT
    r.name as role_name,
    m.slug as module,
    p.permission_type,
    CONCAT(m.slug, '.', p.permission_type) as full_permission
FROM role_permissions rp
JOIN permissions p ON rp.permission_id = p.id
JOIN modules m ON p.module_id = m.id
JOIN roles r ON rp.role_id = r.id
WHERE m.slug = 'packing'
ORDER BY r.name, p.permission_type;
