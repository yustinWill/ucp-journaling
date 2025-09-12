<?php

use Illuminate\Support\Facades\Artisan;

// Create test data for stock management
Artisan::call('tinker', [], [
    'code' => '
        use Illuminate\Support\Facades\DB;
        use App\Services\BatchManagementService;
        use App\Domains\Product\Services\ProductService;

        // Create test product if not exists
        $product = DB::table("m_products")->where("code", "TEST-STOCK-001")->first();
        if (!$product) {
            $productId = DB::table("m_products")->insertGetId([
                "company_code" => "TEST",
                "code" => "TEST-STOCK-001", 
                "name" => "Test Stock Management Product",
                "f_unit_id" => 1,
                "f_category_id" => 1,
                "total_stock" => 0,
                "average_price" => 10000,
                "unit_purchase_price" => 9500,
                "status" => "ACTIVE",
                "created_at" => now(),
                "updated_at" => now(),
            ]);
            echo "Created test product ID: {$productId}\n";
        } else {
            $productId = $product->id;
            echo "Using existing product ID: {$productId}\n";
        }

        // Create test warehouse if not exists
        $warehouse = DB::table("m_warehouses")->where("code", "WH-TEST")->first();
        if (!$warehouse) {
            $warehouseId = DB::table("m_warehouses")->insertGetId([
                "code" => "WH-TEST",
                "name" => "Test Warehouse",
                "status" => "ACTIVE", 
                "created_at" => now(),
                "updated_at" => now(),
            ]);
            echo "Created test warehouse ID: {$warehouseId}\n";
        } else {
            $warehouseId = $warehouse->id;
            echo "Using existing warehouse ID: {$warehouseId}\n";
        }

        // Create some initial batches
        $batchService = new BatchManagementService();
        $productService = new ProductService();

        // Batch 1: Purchase at 8000/unit
        $batch1Id = $batchService->createBatch([
            "f_product_id" => $productId,
            "source_type" => "PURCHASE",
            "f_source_transaction_id" => 1001,
            "quantity" => 100,
            "purchase_price" => 8000,
            "estimated_hpp" => 8000,
            "hpp_status" => "SETTLED",
            "batch_date" => "2025-09-01",
        ]);
        $productService->updateStock($productId, "+", 100);
        echo "Created batch 1 ID: {$batch1Id} (100 units @ 8000)\n";

        // Batch 2: Purchase at 8500/unit  
        $batch2Id = $batchService->createBatch([
            "f_product_id" => $productId,
            "source_type" => "PURCHASE", 
            "f_source_transaction_id" => 1002,
            "quantity" => 75,
            "purchase_price" => 8500,
            "estimated_hpp" => 8500,
            "hpp_status" => "SETTLED",
            "batch_date" => "2025-09-05",
        ]);
        $productService->updateStock($productId, "+", 75);
        echo "Created batch 2 ID: {$batch2Id} (75 units @ 8500)\n";

        // Batch 3: Purchase at 9000/unit
        $batch3Id = $batchService->createBatch([
            "f_product_id" => $productId,
            "source_type" => "PURCHASE",
            "f_source_transaction_id" => 1003, 
            "quantity" => 50,
            "purchase_price" => 9000,
            "estimated_hpp" => 9000,
            "hpp_status" => "SETTLED",
            "batch_date" => "2025-09-10",
        ]);
        $productService->updateStock($productId, "+", 50);
        echo "Created batch 3 ID: {$batch3Id} (50 units @ 9000)\n";

        $finalStock = DB::table("m_products")->where("id", $productId)->value("total_stock");
        echo "\nFinal stock: {$finalStock} units\n";
        echo "Product ID to test: {$productId}\n";
        echo "Warehouse ID to test: {$warehouseId}\n";
        echo "\nTest data setup complete!\n";
    '
]);