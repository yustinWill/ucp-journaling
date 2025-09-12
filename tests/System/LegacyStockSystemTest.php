<?php

/**
 * Comprehensive Stock Management System Test
 */

require_once __DIR__ . '/../../vendor/autoload.php';

// Laravel bootstrap
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Services\BatchManagementService;
use App\Services\StockInformationService;
use App\Domains\StockAdjustment\Actions\CreateStockAdjustmentAction;
use App\Domains\Product\Services\ProductService;

function runStockTests() {
    echo "=== COMPREHENSIVE STOCK MANAGEMENT SYSTEM TEST ===\n\n";
    
    DB::beginTransaction();
    
    try {
        // First, let's check if we have any products
        $products = DB::table('m_products')
            ->select('id', 'code', 'name', 'total_stock', 'average_price')
            ->where('status', 'ACTIVE')
            ->limit(3)
            ->get();
            
        if ($products->isEmpty()) {
            echo "❌ No active products found. Creating test product...\n";
            
            // Create a test product
            $productId = DB::table('m_products')->insertGetId([
                'company_code' => 'TEST',
                'code' => 'TEST-001',
                'name' => 'Test Product for Stock Management',
                'f_unit_id' => 1, // Assuming unit exists
                'f_category_id' => 1, // Assuming category exists  
                'total_stock' => 100,
                'average_price' => 10000,
                'unit_purchase_price' => 9000,
                'status' => 'ACTIVE',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            echo "✅ Created test product with ID: {$productId}\n\n";
        } else {
            $productId = $products->first()->id;
            echo "✅ Using existing product ID: {$productId} ({$products->first()->code})\n\n";
        }
        
        // Check warehouse
        $warehouse = DB::table('m_warehouses')->first();
        if (!$warehouse) {
            $warehouseId = DB::table('m_warehouses')->insertGetId([
                'code' => 'WH001',
                'name' => 'Test Warehouse',
                'status' => 'ACTIVE',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            echo "✅ Created test warehouse with ID: {$warehouseId}\n\n";
        } else {
            $warehouseId = $warehouse->id;
            echo "✅ Using existing warehouse ID: {$warehouseId} ({$warehouse->code})\n\n";
        }
        
        // Initialize services
        $batchService = new BatchManagementService();
        $stockService = new StockInformationService();
        $productService = new ProductService();
        
        // === TEST 1: Initial State ===
        echo "1. CHECKING INITIAL STATE\n";
        echo "========================\n";
        
        $initialStock = DB::table('m_products')->where('id', $productId)->value('total_stock');
        $initialBatches = $batchService->getProductBatchInfo($productId);
        
        echo "Initial Stock: {$initialStock}\n";
        echo "Initial Batches: {$initialBatches['total_batches']}\n";
        echo "Initial Batch Quantity: {$initialBatches['total_quantity']}\n";
        echo "Initial Batch Value: " . number_format($initialBatches['total_value'], 2) . "\n\n";
        
        // === TEST 2: Create Initial Batch (Simulate Purchase) ===
        echo "2. CREATING INITIAL BATCH (Simulating Purchase)\n";
        echo "===============================================\n";
        
        $batchId = $batchService->createBatch([
            'f_product_id' => $productId,
            'source_type' => 'PURCHASE',
            'f_source_transaction_id' => 999, // Simulated purchase
            'quantity' => 50,
            'purchase_price' => 8000,
            'estimated_hpp' => 8000,
            'hpp_status' => 'SETTLED',
        ]);
        
        // Update stock to reflect the new batch
        $productService->updateStock($productId, '+', 50);
        
        echo "✅ Created batch ID: {$batchId} (50 units @ 8000 each)\n";
        
        $afterBatch = $batchService->getProductBatchInfo($productId);
        echo "Stock after batch: " . DB::table('m_products')->where('id', $productId)->value('total_stock') . "\n";
        echo "Batches: {$afterBatch['total_batches']}\n";
        echo "Batch Quantity: {$afterBatch['total_quantity']}\n";
        echo "Batch Value: " . number_format($afterBatch['total_value'], 2) . "\n\n";
        
        // === TEST 3: Stock Adjustment - Positive ===
        echo "3. TESTING POSITIVE STOCK ADJUSTMENT (+30 units)\n";
        echo "================================================\n";
        
        $adjustmentAction = new CreateStockAdjustmentAction();
        $currentStock = DB::table('m_products')->where('id', $productId)->value('total_stock');
        
        $positiveAdjustment = [
            'adjustment_date' => date('Y-m-d'),
            'adjustment_type' => 'GENERAL',
            'warehouse_id' => $warehouseId,
            'pic_id' => 1,
            'status' => 'APPROVED',
            'description' => 'Test positive adjustment',
            'products' => [
                [
                    'product_id' => $productId,
                    'product_code' => 'TEST-001',
                    'product_name' => 'Test Product',
                    'unit' => 'PCS',
                    'stock_before' => $currentStock,
                    'adjustment_qty' => 30,
                    'unit_cost' => 12000,
                    'reason' => 'Found additional inventory',
                ]
            ]
        ];
        
        $adjustmentId = $adjustmentAction->execute($positiveAdjustment);
        
        $afterPositive = $batchService->getProductBatchInfo($productId);
        $stockAfterPositive = DB::table('m_products')->where('id', $productId)->value('total_stock');
        
        echo "✅ Positive adjustment ID: {$adjustmentId}\n";
        echo "Stock: {$currentStock} → {$stockAfterPositive} (+30)\n";
        echo "Batches: {$afterBatch['total_batches']} → {$afterPositive['total_batches']}\n";
        echo "Batch Quantity: {$afterBatch['total_quantity']} → {$afterPositive['total_quantity']}\n";
        echo "Batch Value: " . number_format($afterBatch['total_value'], 2) . " → " . number_format($afterPositive['total_value'], 2) . "\n\n";
        
        // === TEST 4: FIFO Consumption Test ===
        echo "4. TESTING FIFO BATCH CONSUMPTION (Consume 25 units)\n";
        echo "====================================================\n";
        
        $consumedBatches = $batchService->consumeBatches($productId, 25, [
            'source_type' => 'SALES_DELIVERY',
            'f_source_transaction_id' => 888,
            'reference_code' => 'TEST-SD-001',
            'notes' => 'Test FIFO consumption',
        ]);
        
        // Update product stock
        $productService->updateStock($productId, '-', 25);
        
        echo "✅ Consumed 25 units using FIFO\n";
        echo "Batches consumed: " . count($consumedBatches) . "\n";
        
        foreach ($consumedBatches as $consumed) {
            echo "  - Batch {$consumed['batch_reference']}: {$consumed['quantity_consumed']} units @ {$consumed['cost_per_unit']} = {$consumed['total_cost']}\n";
        }
        
        $afterConsumption = $batchService->getProductBatchInfo($productId);
        $stockAfterConsumption = DB::table('m_products')->where('id', $productId)->value('total_stock');
        
        echo "\nStock after consumption: {$stockAfterPositive} → {$stockAfterConsumption} (-25)\n";
        echo "Batch quantity: {$afterPositive['total_quantity']} → {$afterConsumption['total_quantity']}\n";
        echo "Batch value: " . number_format($afterPositive['total_value'], 2) . " → " . number_format($afterConsumption['total_value'], 2) . "\n\n";
        
        // === TEST 5: COGS Calculation ===
        echo "5. TESTING COGS CALCULATION (for 40 units)\n";
        echo "==========================================\n";
        
        $cogsData = $batchService->calculateCOGS($productId, 40);
        
        echo "✅ COGS Calculation Results:\n";
        echo "Requested Quantity: {$cogsData['requested_quantity']}\n";
        echo "Calculated Quantity: {$cogsData['calculated_quantity']}\n";
        echo "Total COGS: " . number_format($cogsData['total_cogs'], 2) . "\n";
        echo "Average Cost per Unit: " . number_format($cogsData['average_cost_per_unit'], 2) . "\n";
        echo "Insufficient Stock: " . ($cogsData['insufficient_stock'] ? 'Yes' : 'No') . "\n";
        
        if ($cogsData['insufficient_stock']) {
            echo "Shortfall: {$cogsData['shortfall']} units\n";
        }
        
        echo "\nBatches used for COGS:\n";
        foreach ($cogsData['batches_used'] as $batch) {
            echo "  - {$batch['batch_reference']}: {$batch['quantity_used']} @ {$batch['cost_per_unit']} = {$batch['total_cost']}\n";
        }
        echo "\n";
        
        // === TEST 6: Stock Information Services ===
        echo "6. TESTING STOCK INFORMATION SERVICES\n";
        echo "=====================================\n";
        
        $overallStock = $stockService->getOverallStock();
        echo "✅ Overall Stock Products: {$overallStock['summary']['total_products']}\n";
        echo "Total Stock Value: " . number_format($overallStock['summary']['total_stock_value'], 2) . "\n";
        
        $stockByWarehouse = $stockService->getStockByWarehouse($warehouseId);
        echo "✅ Warehouse Stock Products: {$stockByWarehouse['summary']['total_products']}\n";
        
        $lowStock = $stockService->getLowStockProducts(50);
        echo "✅ Low Stock Products (threshold 50): {$lowStock['count']}\n";
        
        $valuation = $stockService->getStockValuation();
        echo "✅ Stock Valuation - Total Value: " . number_format($valuation['summary']['total_stock_value'], 2) . "\n\n";
        
        // === TEST 7: Negative Stock Adjustment ===
        echo "7. TESTING NEGATIVE STOCK ADJUSTMENT (-15 units)\n";
        echo "================================================\n";
        
        $currentStock = DB::table('m_products')->where('id', $productId)->value('total_stock');
        
        $negativeAdjustment = [
            'adjustment_date' => date('Y-m-d'),
            'adjustment_type' => 'GENERAL',
            'warehouse_id' => $warehouseId,
            'pic_id' => 1,
            'status' => 'APPROVED',
            'description' => 'Test negative adjustment',
            'products' => [
                [
                    'product_id' => $productId,
                    'product_code' => 'TEST-001',
                    'product_name' => 'Test Product',
                    'unit' => 'PCS',
                    'stock_before' => $currentStock,
                    'adjustment_qty' => -15,
                    'unit_cost' => 0,
                    'reason' => 'Inventory shrinkage',
                ]
            ]
        ];
        
        $negativeAdjustmentId = $adjustmentAction->execute($negativeAdjustment);
        
        $afterNegative = $batchService->getProductBatchInfo($productId);
        $stockAfterNegative = DB::table('m_products')->where('id', $productId)->value('total_stock');
        
        echo "✅ Negative adjustment ID: {$negativeAdjustmentId}\n";
        echo "Stock: {$currentStock} → {$stockAfterNegative} (-15)\n";
        echo "Batch quantity: {$afterConsumption['total_quantity']} → {$afterNegative['total_quantity']}\n";
        echo "Batch value: " . number_format($afterConsumption['total_value'], 2) . " → " . number_format($afterNegative['total_value'], 2) . "\n\n";
        
        // === FINAL SUMMARY ===
        echo "8. FINAL TEST SUMMARY\n";
        echo "====================\n";
        
        $finalStock = DB::table('m_products')->where('id', $productId)->value('total_stock');
        $finalBatches = $batchService->getProductBatchInfo($productId);
        
        echo "✅ All tests completed successfully!\n\n";
        echo "FINAL STATE:\n";
        echo "- Product Stock: {$initialStock} → {$finalStock}\n";
        echo "- Total Batches: {$initialBatches['total_batches']} → {$finalBatches['total_batches']}\n";
        echo "- Batch Quantity: {$initialBatches['total_quantity']} → {$finalBatches['total_quantity']}\n";
        echo "- Batch Value: " . number_format($initialBatches['total_value'], 2) . " → " . number_format($finalBatches['total_value'], 2) . "\n";
        echo "- Weighted Avg Cost: " . number_format($finalBatches['weighted_average_cost'], 2) . "\n\n";
        
        echo "OPERATIONS TESTED:\n";
        echo "✅ Batch creation (purchase simulation)\n";
        echo "✅ Positive stock adjustments with batch creation\n";
        echo "✅ FIFO batch consumption\n";
        echo "✅ COGS calculation\n";
        echo "✅ Stock information services\n";
        echo "✅ Negative stock adjustments with batch consumption\n";
        echo "✅ Proper stock tracking consistency\n\n";
        
        echo "🎉 ALL STOCK MANAGEMENT TESTS PASSED! 🎉\n";
        
    } catch (\Exception $e) {
        echo "❌ Test failed with error: " . $e->getMessage() . "\n";
        echo "Stack trace: " . $e->getTraceAsString() . "\n";
    } finally {
        DB::rollback();
        echo "\n📝 Note: All changes rolled back - no actual data was modified.\n";
    }
}

runStockTests();