-- ============================================
-- Add is_packaging flags to tables
-- ============================================
-- You can either run these SQL queries manually
-- OR run: php artisan migrate

-- ============================================
-- Option 1: Run these SQL queries manually
-- ============================================

-- Add is_packaging flag to t_productions table
ALTER TABLE `t_productions`
ADD COLUMN `is_packaging` TINYINT(1) NOT NULL DEFAULT 0
COMMENT 'Flag to distinguish packing production from regular production'
AFTER `status`;

-- Add is_packaging flag to m_products table
ALTER TABLE `m_products`
ADD COLUMN `is_packaging` TINYINT(1) NOT NULL DEFAULT 0
COMMENT 'Flag to mark products that are used for packing (subset of bundle products)'
AFTER `is_bundle`;

-- ============================================
-- Verification queries
-- ============================================

-- Check t_productions table
SHOW COLUMNS FROM t_productions LIKE 'is_packaging';

-- Check m_products table
SHOW COLUMNS FROM m_products LIKE 'is_packaging';

-- ============================================
-- Example: Mark a product as packaging product
-- ============================================
-- UPDATE m_products SET is_packaging = 1 WHERE id = YOUR_PRODUCT_ID;

-- ============================================
-- Rollback (if needed)
-- ============================================
-- ALTER TABLE t_productions DROP COLUMN is_packaging;
-- ALTER TABLE m_products DROP COLUMN is_packaging;
