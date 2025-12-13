-- ============================================
-- PACKING MODULE PERMISSIONS - DBeaver Compatible
-- ============================================
-- Run each section separately in DBeaver
-- Select the section and press Ctrl+Enter to execute

-- ============================================
-- SECTION 1: Insert the Packing module
-- ============================================
INSERT INTO modules (name, slug, description, is_active, created_at, updated_at)
VALUES (
    'Packing Management',
    'packing',
    'Packing product management and transactions',
    1,
    NOW(),
    NOW()
);

-- ============================================
-- SECTION 2: Check module was created and get the ID
-- ============================================
SELECT id, name, slug FROM modules WHERE slug = 'packing';
-- Note the ID from the result (let's assume it's 15 for example)

-- ============================================
-- SECTION 3: Insert permissions
-- ============================================
-- IMPORTANT: Replace the number 15 below with the actual module ID from SECTION 2

INSERT INTO permissions (module_id, permission_type, created_at, updated_at)
VALUES
    (15, 'read', NOW(), NOW()),
    (15, 'write', NOW(), NOW()),
    (15, 'delete', NOW(), NOW()),
    (15, 'export', NOW(), NOW());

-- ============================================
-- SECTION 4: Check permissions were created
-- ============================================
SELECT
    p.id,
    p.permission_type,
    CONCAT('packing.', p.permission_type) as full_permission
FROM permissions p
JOIN modules m ON p.module_id = m.id
WHERE m.slug = 'packing'
ORDER BY p.permission_type;
-- Note the permission IDs (e.g., 50, 51, 52, 53)

-- ============================================
-- SECTION 5: Check Super Admin role ID
-- ============================================
SELECT id, name FROM roles WHERE name LIKE '%Admin%';
-- Note the Super Admin role ID (usually 1)

-- ============================================
-- SECTION 6: Assign permissions to Super Admin
-- ============================================
-- IMPORTANT: Replace the role_id (1) and permission IDs (50,51,52,53) with actual values

INSERT INTO role_permissions (role_id, permission_id, created_at, updated_at)
VALUES
    (1, 50, NOW(), NOW()),  -- packing.read
    (1, 51, NOW(), NOW()),  -- packing.write
    (1, 52, NOW(), NOW()),  -- packing.delete
    (1, 53, NOW(), NOW());  -- packing.export

-- ============================================
-- SECTION 7: Verify everything is set up
-- ============================================
SELECT
    r.name as role_name,
    CONCAT('packing.', p.permission_type) as permission
FROM role_permissions rp
JOIN permissions p ON rp.permission_id = p.id
JOIN modules m ON p.module_id = m.id
JOIN roles r ON rp.role_id = r.id
WHERE m.slug = 'packing'
ORDER BY r.name, p.permission_type;
