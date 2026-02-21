<?php

namespace App\Domains\ReturnManagement\Actions;

use App\Domains\Product\Services\ProductService;
use App\Domains\ReturnManagement\Services\ReturnManagementAuditLogService;
use App\Domains\ReturnManagement\Services\ReturnManagementProductService;
use App\Domains\ReturnManagement\Services\ReturnManagementService;
use App\Enums\ActionTypes;
use App\Services\BatchManagementService;
use App\Services\StockMovementLogService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessReturnStockAction
{
    /**
     * Process return and record stock movement
     * For SALES_RETURN: Stock returns to warehouse (IN)
     * For PURCHASE_RETURN: Stock goes out from warehouse (OUT)
     *
     * @param int $returnId
     * @return void
     */
    public function execute(int $returnId)
    {
        return DB::transaction(function () use ($returnId) {
            // Get return data
            $return = app(ReturnManagementService::class)->findById($returnId);

            if (!$return) {
                throw new \Exception("Return Management dengan ID {$returnId} tidak ditemukan.");
            }

            // Check if already processed
            if ($return->processing_status === 'COMPLETED') {
                throw new \Exception("Return ini sudah diproses sebelumnya.");
            }

            // Check if warehouse is set
            if (!$return->f_warehouse_id) {
                throw new \Exception("Gudang harus ditentukan untuk memproses return.");
            }

            // Get return products
            $returnProducts = app(ReturnManagementProductService::class)->getByReturnId($returnId);

            // Process products for stock movement
            foreach ($returnProducts as $product) {
                if (!isset($product->f_product_id) || !isset($product->return_qty) || $product->return_qty <= 0) {
                    continue;
                }

                $productId = $product->f_product_id;
                $returnQty = $product->return_qty;
                $warehouseId = $return->f_warehouse_id;

                // Get product's default unit for stock operations
                $productData = DB::table('m_products')->where('id', $productId)->first();
                $unitId = $productData->f_unit_id ?? null;

                // Convert return quantity to base unit for stock operations
                $returnQtyInBaseUnit = $returnQty;

                if ($unitId) {
                    $productUnit = DB::table('m_product_units as pu')
                        ->where('pu.f_product_id', $productId)
                        ->where('pu.f_unit_id', $unitId)
                        ->select('pu.conversion_factor', 'pu.is_default')
                        ->first();

                    if ($productUnit && !$productUnit->is_default) {
                        // Not default unit, convert to base unit
                        $conversionFactor = $productUnit->conversion_factor ?? 1;
                        $returnQtyInBaseUnit = $returnQty * $conversionFactor;
                    }
                }

                // Determine movement type based on return type
                if ($return->return_type === 'SALES_RETURN') {
                    // Sales Return: Stock comes back IN to our warehouse
                    $movementType = 'RETURN_IN';
                    $description = 'Sales Return - Stock returned to warehouse: ' . ($return->transaction_code ?? 'Unknown');

                    // Record stock movement (IN)
                    app(StockMovementLogService::class)->recordMovement(
                        $warehouseId,
                        $productId,
                        $movementType,
                        'RETURN_MANAGEMENT',
                        $return->transaction_code ?? 'Unknown',
                        $returnQtyInBaseUnit,
                        $description,
                        $return->return_date ?? now()
                    );

                    // Create stock batch for returned items using BatchManagementService
                    app(BatchManagementService::class)->createBatch([
                        'f_product_id' => $productId,
                        'source_type' => 'STOCK_ADJUSTMENT', // Use existing source_type
                        'f_source_transaction_id' => $returnId,
                        'batch_date' => $return->return_date ?? now(),
                        'quantity' => $returnQtyInBaseUnit,
                        'purchase_price' => $product->return_unit_price ?? 0,
                        'hpp_status' => 'SETTLED',
                    ]);

                } elseif ($return->return_type === 'PURCHASE_RETURN') {
                    // Purchase Return: Stock goes OUT from our warehouse back to supplier
                    $movementType = 'RETURN_OUT';
                    $description = 'Purchase Return - Stock returned to supplier: ' . ($return->transaction_code ?? 'Unknown');

                    // Record stock movement (OUT)
                    app(StockMovementLogService::class)->recordMovement(
                        $warehouseId,
                        $productId,
                        $movementType,
                        'RETURN_MANAGEMENT',
                        $return->transaction_code ?? 'Unknown',
                        $returnQtyInBaseUnit,
                        $description,
                        $return->return_date ?? now()
                    );

                    // Consume stock batches (FIFO) using BatchManagementService
                    app(BatchManagementService::class)->consumeBatches($productId, $returnQtyInBaseUnit, [
                        'source_type' => 'SALES_DELIVERY', // Use existing source_type
                        'f_source_transaction_id' => $returnId,
                        'transaction_date' => $return->return_date ?? now(),
                        'reference_code' => $return->transaction_code,
                        'notes' => 'Stock consumed by Purchase Return',
                    ]);
                }

                // Recalculate product total_stock from movement logs
                app(ProductService::class)->updateStock($productId);
            }

            // Update return status to PROCESSED and COMPLETED
            DB::table('t_return_managements')
                ->where('id', $returnId)
                ->update([
                    'status' => 'PROCESSED',
                    'processing_status' => 'COMPLETED',
                    'updated_at' => now(),
                    'updated_by' => Auth::id(),
                ]);

            // Create audit log
            $log_data = [
                'f_return_management_id' => $returnId,
                'action_type' => ActionTypes::UPDATE,
                'description' => 'Return Processed - Stock Movement Recorded',
                'metadata' => [
                    'return_type' => $return->return_type,
                    'processing_status' => 'COMPLETED',
                    'processed_at' => now()->toDateTimeString(),
                    'processed_by' => Auth::id(),
                ],
            ];
            app(ReturnManagementAuditLogService::class)->create($log_data);

            return $returnId;
        });
    }
}
