-- ============================================
-- PACKING MODULE PERMISSIONS - Simple Version
-- ============================================
-- Copy each INSERT statement one by one

-- 1. Create module
INSERT INTO modules (name, slug, description, is_active, created_at, updated_at)
VALUES ('Packing Management', 'packing', 'Packing product management and transactions', 1, NOW(), NOW());

-- 2. Check what ID was created
SELECT id FROM modules WHERE slug = 'packing';
-- Let's say the result is: 15

-- 3. Create permissions (replace 15 with your actual module_id)
INSERT INTO permissions (module_id, permission_type, created_at, updated_at) VALUES (15, 'read', NOW(), NOW());
INSERT INTO permissions (module_id, permission_type, created_at, updated_at) VALUES (15, 'write', NOW(), NOW());
INSERT INTO permissions (module_id, permission_type, created_at, updated_at) VALUES (15, 'delete', NOW(), NOW());
INSERT INTO permissions (module_id, permission_type, created_at, updated_at) VALUES (15, 'export', NOW(), NOW());

-- 4. Check permission IDs
SELECT p.id, p.permission_type FROM permissions p JOIN modules m ON p.module_id = m.id WHERE m.slug = 'packing';
-- Let's say the results are: 50 (read), 51 (write), 52 (delete), 53 (export)

-- 5. Get Super Admin role ID
SELECT id FROM roles WHERE name = 'Super Admin';
-- Let's say the result is: 1

-- 6. Assign to Super Admin (replace role_id and permission_ids with your actual values)
INSERT INTO role_permissions (role_id, permission_id, created_at, updated_at) VALUES (1, 50, NOW(), NOW());
INSERT INTO role_permissions (role_id, permission_id, created_at, updated_at) VALUES (1, 51, NOW(), NOW());
INSERT INTO role_permissions (role_id, permission_id, created_at, updated_at) VALUES (1, 52, NOW(), NOW());
INSERT INTO role_permissions (role_id, permission_id, created_at, updated_at) VALUES (1, 53, NOW(), NOW());

-- 7. Verify
SELECT r.name, CONCAT('packing.', p.permission_type) as permission
FROM role_permissions rp
JOIN permissions p ON rp.permission_id = p.id
JOIN modules m ON p.module_id = m.id
JOIN roles r ON rp.role_id = r.id
WHERE m.slug = 'packing';
