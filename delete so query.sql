-- 1. Find the SO id first
SELECT id FROM t_sales_orders WHERE transaction_code = 'SO-2604-114';
-- assume id = X, replace X below

SET @so_id = (SELECT id FROM t_sales_orders WHERE transaction_code = 'SO-2604-114');
SET @si_id = (SELECT id FROM t_sales_invoices WHERE f_sales_order_id = @so_id);
SET @sd_id = (SELECT id FROM t_sales_deliveries WHERE sales_order_id = @so_id);

-- 2. Delete revenue splits
DELETE FROM t_sales_order_revenue_splits WHERE f_sales_order_id = @so_id;

-- 3. Delete SI payments / settlements
-- DELETE FROM t_sales_invoice_payments WHERE f_sales_invoice_id = @si_id;

-- 4. Delete SI products & audit logs
DELETE FROM t_sales_invoice_products WHERE f_sales_invoice_id = @si_id;
DELETE FROM t_sales_invoice_audit_logs WHERE f_sales_invoice_id = @si_id;

-- 5. Delete SI
DELETE FROM t_sales_invoices WHERE id = @si_id;

-- 6. Reverse stock movement logs from SD
DELETE FROM t_stock_movement_logs WHERE reference_number = (SELECT delivery_number FROM t_sales_deliveries WHERE id = @sd_id);

-- 7. Restore stock batch remaining qty
UPDATE t_product_stock_batches psb
JOIN t_product_batch_transactions pbt ON pbt.f_product_batch_id = psb.id
SET psb.remaining_qty = psb.remaining_qty + pbt.qty_moved
WHERE pbt.source_type = 'SALES_DELIVERY' AND pbt.f_source_transaction_id = @sd_id;

-- 8. Delete batch transactions
DELETE FROM t_product_batch_transactions WHERE source_type = 'SALES_DELIVERY' AND f_source_transaction_id = @sd_id;

-- 9. Recalculate product total_stock (for each product in the SD)
-- Run after: php artisan tinker -> app(ProductService::class)->updateStock($productId)

-- 10. Delete SD products & audit logs
DELETE FROM t_sales_delivery_products WHERE sales_delivery_id = @sd_id;
DELETE FROM t_sales_delivery_audit_logs WHERE sales_delivery_id = @sd_id;

-- 11. Delete SD
DELETE FROM t_sales_deliveries WHERE id = @sd_id;

-- 12. Delete SO products & audit logs
DELETE FROM t_sales_order_products WHERE f_sales_order_id = @so_id;
DELETE FROM t_sales_order_audit_logs WHERE f_sales_order_id = @so_id;

-- 13. Delete SO
DELETE FROM t_sales_orders WHERE id = @so_id;

-- 14. Delete journals linked to this SO/SI/SD (if any)
DELETE FROM t_accounting_journals WHERE reference_number IN (
    SELECT transaction_code FROM t_sales_orders WHERE id = @so_id
);