-- =====================================================
-- DELETE SALES ORDER WITH STOCK REVERSAL
-- =====================================================
-- This script deletes a sales order and reverts all stock movements
-- Replace @sales_order_id with the actual ID you want to delete
-- =====================================================

SET @sales_order_id = 1; -- CHANGE THIS TO YOUR SALES ORDER ID

-- Start Transaction
START TRANSACTION;

-- =====================================================
-- STEP 1: Check if Sales Order exists and get transaction code
-- =====================================================
SELECT @transaction_code := transaction_code,
       @company_code := company_code
FROM t_sales_orders
WHERE id = @sales_order_id;

SELECT CONCAT('Deleting Sales Order: ', @transaction_code) AS 'Status';

-- =====================================================
-- STEP 2: Check if Sales Order has invoices (prevent deletion)
-- =====================================================
SELECT @invoice_count := COUNT(*)
FROM t_sales_invoices
WHERE f_sales_order_id = @sales_order_id;

SELECT IF(@invoice_count > 0,
    CONCAT('ERROR: Sales Order has ', @invoice_count, ' invoice(s). Cannot delete.'),
    'OK: No invoices found. Safe to proceed.'
) AS 'Invoice Check';

-- If has invoices, stop here (manually check the result above)

-- =====================================================
-- STEP 3: Revert Stock Movements from Confirmed Deliveries
-- =====================================================

-- Loop through each confirmed sales delivery and revert stock
-- This requires creating a temporary table to store delivery info

CREATE TEMPORARY TABLE IF NOT EXISTS temp_deliveries_to_revert AS
SELECT
    sd.id AS delivery_id,
    sd.delivery_number,
    sd.warehouse_id AS warehouse_id,
    sd.delivery_date,
    sdp.product_id AS product_id,
    sdp.quantity_delivered AS qty,
    p.code AS product_code,
    p.name AS product_name,
    w.code AS warehouse_code,
    w.name AS warehouse_name,
    u.code AS unit_code
FROM t_sales_deliveries sd
JOIN t_sales_delivery_products sdp ON sd.id = sdp.sales_delivery_id
JOIN m_products p ON sdp.product_id = p.id
JOIN m_warehouses w ON sd.warehouse_id = w.id
JOIN m_units u ON p.f_unit_id = u.id
WHERE sd.sales_order_id = @sales_order_id
  AND sd.is_confirmed = '1';

SELECT COUNT(*) AS 'Confirmed Deliveries to Revert' FROM temp_deliveries_to_revert;

-- =====================================================
-- STEP 4: Restore Product Stock Quantities
-- =====================================================

-- Increase stock back for each product
UPDATE m_products p
JOIN (
    SELECT product_id, SUM(qty) AS total_qty
    FROM temp_deliveries_to_revert
    GROUP BY product_id
) AS agg ON p.id = agg.product_id
SET p.qty = p.qty + agg.total_qty;

SELECT 'Product stock quantities restored' AS 'Status';

-- =====================================================
-- STEP 5: Restore Stock Batches (FIFO Reversal)
-- =====================================================

-- Get batch consumption transactions to revert
CREATE TEMPORARY TABLE IF NOT EXISTS temp_batch_reversals AS
SELECT
    pbt.f_product_batch_id AS batch_id,
    pbt.f_product_id AS product_id,
    pbt.qty_moved AS qty_to_restore,
    pbt.f_source_transaction_id AS delivery_id
FROM t_product_batch_transactions pbt
JOIN t_sales_deliveries sd ON pbt.f_source_transaction_id = sd.id
WHERE sd.sales_order_id = @sales_order_id
  AND pbt.source_type = 'SALES_DELIVERY'
  AND pbt.transaction_type = 'OUT';

SELECT COUNT(*) AS 'Batch Transactions to Revert' FROM temp_batch_reversals;

-- Restore batch quantities
UPDATE t_product_stock_batches psb
JOIN (
    SELECT f_product_batch_id, SUM(qty_to_restore) AS total_restore
    FROM temp_batch_reversals
    GROUP BY f_product_batch_id
) AS agg ON psb.id = agg.f_product_batch_id
SET psb.remaining_qty = psb.remaining_qty + agg.total_restore,
    psb.updated_at = NOW(),
    psb.updated_by = 1; -- Replace with actual user ID

SELECT 'Stock batches restored' AS 'Status';

-- =====================================================
-- STEP 6: Create Reversal Stock Movement Logs
-- =====================================================

-- Insert reversal movement logs (RETURN_IN to cancel SALES_OUT)
INSERT INTO t_stock_movement_logs (
    company_code,
    movement_date,
    movement_type,
    reference_type,
    reference_number,
    warehouse_id,
    warehouse_code,
    warehouse_name,
    product_id,
    product_code,
    product_name,
    unit,
    stock_before,
    movement_qty,
    stock_after,
    unit_cost,
    total_value,
    notes,
    created_at,
    created_by
)
SELECT
    @company_code,
    NOW(),
    'RETURN_IN',
    'SALES_ORDER_DELETION',
    @transaction_code,
    warehouse_id,
    warehouse_code,
    warehouse_name,
    product_id,
    product_code,
    product_name,
    unit_code,
    0, -- stock_before (will be calculated)
    qty,
    0, -- stock_after (will be calculated)
    0, -- unit_cost
    0, -- total_value
    CONCAT('Stock reverted due to Sales Order deletion: ', @transaction_code),
    NOW(),
    1 -- Replace with actual user ID
FROM temp_deliveries_to_revert;

SELECT 'Reversal stock movement logs created' AS 'Status';

-- =====================================================
-- STEP 7: Create Reversal Batch Transaction Logs
-- =====================================================

INSERT INTO t_product_batch_transactions (
    company_code,
    f_product_batch_id,
    f_product_id,
    transaction_type,
    source_type,
    f_source_transaction_id,
    transaction_date,
    qty_moved,
    reference_code,
    notes,
    created_at,
    created_by
)
SELECT
    @company_code,
    batch_id,
    product_id,
    'IN',
    'SALES_ORDER_DELETION',
    delivery_id,
    NOW(),
    qty_to_restore,
    CONCAT('SO-DEL-', @sales_order_id),
    CONCAT('Batch restored due to Sales Order deletion: ', @transaction_code),
    NOW(),
    1 -- Replace with actual user ID
FROM temp_batch_reversals;

SELECT 'Reversal batch transactions created' AS 'Status';

-- =====================================================
-- STEP 8: Delete Original Stock Movement Logs
-- =====================================================

DELETE FROM t_stock_movement_logs
WHERE reference_type = 'SALES_DELIVERY'
  AND reference_number IN (
      SELECT delivery_number
      FROM t_sales_deliveries
      WHERE sales_order_id = @sales_order_id
  )
  AND movement_type = 'SALES_OUT';

SELECT 'Original stock movement logs deleted' AS 'Status';

-- =====================================================
-- STEP 9: Delete Original Batch Consumption Transactions
-- =====================================================

DELETE pbt FROM t_product_batch_transactions pbt
JOIN t_sales_deliveries sd ON pbt.f_source_transaction_id = sd.id
WHERE sd.sales_order_id = @sales_order_id
  AND pbt.source_type = 'SALES_DELIVERY'
  AND pbt.transaction_type = 'OUT';

SELECT 'Original batch consumption transactions deleted' AS 'Status';

-- =====================================================
-- STEP 10: Delete Sales Delivery Products
-- =====================================================

DELETE sdp FROM t_sales_delivery_products sdp
JOIN t_sales_deliveries sd ON sdp.sales_delivery_id = sd.id
WHERE sd.sales_order_id = @sales_order_id;

SELECT 'Sales delivery products deleted' AS 'Status';

-- =====================================================
-- STEP 11: Delete Sales Delivery Audit Logs
-- =====================================================

DELETE sdal FROM t_sales_delivery_audit_logs sdal
JOIN t_sales_deliveries sd ON sdal.f_sales_delivery_id = sd.id
WHERE sd.sales_order_id = @sales_order_id;

SELECT 'Sales delivery audit logs deleted' AS 'Status';

-- =====================================================
-- STEP 12: Delete Sales Deliveries
-- =====================================================

DELETE FROM t_sales_deliveries
WHERE sales_order_id = @sales_order_id;

SELECT 'Sales deliveries deleted' AS 'Status';

-- =====================================================
-- STEP 13: Delete Sales Order Products
-- =====================================================

DELETE FROM t_sales_order_products
WHERE f_sales_order_id = @sales_order_id;

SELECT 'Sales order products deleted' AS 'Status';

-- =====================================================
-- STEP 14: Delete Sales Order Audit Logs
-- =====================================================

DELETE FROM t_sales_order_audit_logs
WHERE f_sales_order_id = @sales_order_id;

SELECT 'Sales order audit logs deleted' AS 'Status';

-- =====================================================
-- STEP 15: Delete Sales Order
-- =====================================================

DELETE FROM t_sales_orders
WHERE id = @sales_order_id;

SELECT CONCAT('Sales Order ', @transaction_code, ' deleted successfully!') AS 'Final Status';

-- =====================================================
-- STEP 16: Clean up temporary tables
-- =====================================================

DROP TEMPORARY TABLE IF EXISTS temp_deliveries_to_revert;
DROP TEMPORARY TABLE IF EXISTS temp_batch_reversals;

-- =====================================================
-- COMMIT or ROLLBACK
-- =====================================================
-- Review the output above, then run ONE of these:

-- COMMIT;  -- Uncomment this line to commit the changes
-- ROLLBACK;  -- Uncomment this line to rollback if something looks wrong
