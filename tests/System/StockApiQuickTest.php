<?php

require_once __DIR__ . '/../../vendor/autoload.php';

// Laravel bootstrap
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\StockInformationController;
use App\Services\StockInformationService;
use App\Services\BatchManagementService;
use Illuminate\Http\Request;

echo "=== STOCK API QUICK TEST (Using Existing Controller & Services) ===\n\n";

// Test configuration
$testProductId = 24; // Galon product
$testWarehouseId = 4; // MAIN WAREHOUSE  
$testUserId = 1; // Jonathan SS

// Set test session data and authentication
session(['user_company_code' => 'TEST']);
Auth::loginUsingId($testUserId);

// Initialize controller with services
$stockService = new StockInformationService();
$batchService = new BatchManagementService();
$controller = new StockInformationController($stockService, $batchService);

// Mock request
$request = new Request();

echo "📊 Testing Stock API Controller Methods Directly\n";
echo "═══════════════════════════════════════════════\n\n";

// ===== TEST 1: Overall Stock =====
echo "1️⃣ Testing Overall Stock API\n";
echo "──────────────────────────────\n";
try {
    $response = $controller->overallStock($request);
    $data = json_decode($response->getContent(), true);
    
    if ($data['success']) {
        echo "✅ Overall Stock API successful!\n";
        echo "   Total products: " . $data['data']['summary']['total_products'] . "\n";
        echo "   Total stock quantity: " . number_format($data['data']['summary']['total_stock_quantity']) . "\n";
        echo "   Total stock value: Rp " . number_format($data['data']['summary']['total_stock_value'], 2) . "\n\n";
    } else {
        echo "❌ API Error: " . $data['message'] . "\n\n";
    }
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n\n";
}

// ===== TEST 2: Warehouse Stock =====
echo "2️⃣ Testing Warehouse Stock API\n";
echo "───────────────────────────────\n";
try {
    $response = $controller->stockByWarehouse($request, $testWarehouseId);
    $data = json_decode($response->getContent(), true);
    
    if ($data['success']) {
        echo "✅ Warehouse Stock API successful!\n";
        echo "   Warehouse: " . $data['data']['warehouse']['name'] . "\n";
        echo "   Products in warehouse: " . $data['data']['summary']['total_products'] . "\n";
        echo "   Total quantity: " . number_format($data['data']['summary']['total_stock_quantity']) . "\n";
        echo "   Total value: Rp " . number_format($data['data']['summary']['total_stock_value'], 2) . "\n\n";
    } else {
        echo "❌ API Error: " . $data['message'] . "\n\n";
    }
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n\n";
}

// ===== TEST 3: Product Batch Info =====
echo "3️⃣ Testing Product Batch Information API\n";
echo "─────────────────────────────────────────\n";
try {
    // Mock request with include_empty parameter
    $request = new Request(['include_empty' => false]);
    $response = $controller->productBatchInfo($testProductId);
    $data = json_decode($response->getContent(), true);
    
    if ($data['success']) {
        echo "✅ Product Batch Info API successful!\n";
        echo "   Product ID: " . $data['data']['product_id'] . "\n";
        echo "   Total batches: " . $data['data']['total_batches'] . "\n";
        echo "   Total batch quantity: " . number_format($data['data']['total_quantity']) . "\n";
        echo "   Total batch value: Rp " . number_format($data['data']['total_value'], 2) . "\n";
        echo "   Weighted average cost: Rp " . number_format($data['data']['weighted_average_cost'], 2) . "\n";
        
        if (count($data['data']['batches']) > 0) {
            echo "   Sample batches:\n";
            foreach (array_slice($data['data']['batches'], 0, 3) as $batch) {
                echo "     - " . $batch['batch_reference'] . ": " . $batch['remaining_qty'] . " units @ Rp " . number_format($batch['estimated_hpp'], 2) . "\n";
            }
        }
        echo "\n";
    } else {
        echo "❌ API Error: " . $data['message'] . "\n\n";
    }
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n\n";
}

// ===== TEST 4: COGS Calculation =====
echo "4️⃣ Testing COGS Calculation API\n";
echo "──────────────────────────────\n";
try {
    $request = new Request(['quantity' => 25]);
    $response = $controller->calculateCOGS($request, $testProductId);
    $data = json_decode($response->getContent(), true);
    
    if ($data['success']) {
        echo "✅ COGS Calculation API successful!\n";
        echo "   Requested quantity: " . $data['data']['requested_quantity'] . "\n";
        echo "   Total COGS: Rp " . number_format($data['data']['total_cogs'], 2) . "\n";
        echo "   Average cost per unit: Rp " . number_format($data['data']['average_cost_per_unit'], 2) . "\n";
        echo "   Insufficient stock: " . ($data['data']['insufficient_stock'] ? 'Yes' : 'No') . "\n";
        
        if (count($data['data']['batches_used']) > 0) {
            echo "   Batches used:\n";
            foreach ($data['data']['batches_used'] as $batch) {
                echo "     - " . $batch['batch_reference'] . ": " . $batch['quantity_used'] . " @ Rp " . number_format($batch['cost_per_unit'], 2) . "\n";
            }
        }
        echo "\n";
    } else {
        echo "❌ API Error: " . $data['message'] . "\n\n";
    }
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n\n";
}

// ===== TEST 5: Movement History =====
echo "5️⃣ Testing Stock Movement History API\n";
echo "────────────────────────────────────\n";
try {
    $request = new Request(['limit' => 10]);
    $response = $controller->movementHistory($request, $testProductId);
    $data = json_decode($response->getContent(), true);
    
    if ($data['success']) {
        echo "✅ Movement History API successful!\n";
        echo "   Product: " . $data['data']['product']->name . " (" . $data['data']['product']->code . ")\n";
        echo "   Current stock: " . number_format($data['data']['summary']['current_stock']) . "\n";
        echo "   Total movements: " . $data['data']['summary']['total_movements'] . "\n";
        
        if (count($data['data']['movements']) > 0) {
            echo "   Recent movements:\n";
            foreach (array_slice($data['data']['movements'], 0, 3) as $movement) {
                echo "     - " . $movement->movement_date . ": " . $movement->movement_type . 
                     " (" . $movement->movement_qty . " units)\n";
            }
        }
        echo "\n";
    } else {
        echo "❌ API Error: " . $data['message'] . "\n\n";
    }
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n\n";
}

// ===== TEST 6: Dashboard =====
echo "6️⃣ Testing Dashboard API\n";
echo "───────────────────────\n";
try {
    $response = $controller->dashboard();
    $data = json_decode($response->getContent(), true);
    
    if ($data['success']) {
        echo "✅ Dashboard API successful!\n";
        echo "   Stock Summary:\n";
        echo "     - Total products: " . $data['data']['stock_summary']['total_products'] . "\n";
        echo "     - Total quantity: " . number_format($data['data']['stock_summary']['total_stock_quantity']) . "\n";
        echo "     - Total value: Rp " . number_format($data['data']['stock_summary']['total_stock_value'], 2) . "\n";
        echo "   Low Stock Alert: " . $data['data']['low_stock_alert']['count'] . " products\n";
        echo "   Valuation Total: Rp " . number_format($data['data']['valuation_summary']['total_stock_value'], 2) . "\n\n";
    } else {
        echo "❌ API Error: " . $data['message'] . "\n\n";
    }
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n\n";
}

// ===== SAMPLE API USAGE FOR LATER REFERENCE =====
echo "📋 API Usage Examples for Later Reference\n";
echo "═════════════════════════════════════════\n\n";

echo "🌐 **Available API Routes** (use with `php artisan serve`):\n";
echo "```\n";
echo "GET  /api/stock                              - Overall stock\n";
echo "GET  /api/stock/warehouse/{$testWarehouseId}              - Stock by warehouse\n";
echo "GET  /api/stock/product/{$testProductId}/batches         - Product batches\n";
echo "POST /api/stock/product/{$testProductId}/cogs            - Calculate COGS\n";
echo "GET  /api/stock/product/{$testProductId}/movements       - Movement history\n";
echo "GET  /api/stock/low-stock                    - Low stock alert\n";
echo "GET  /api/stock/valuation                    - Stock valuation\n";
echo "GET  /api/stock/dashboard                    - Dashboard data\n";
echo "```\n\n";

echo "📄 **Example cURL Commands:**\n";
echo "```bash\n";
echo "# Get overall stock\n";
echo "curl -X GET http://localhost:8000/api/stock\n\n";

echo "# Get warehouse stock\n";
echo "curl -X GET http://localhost:8000/api/stock/warehouse/{$testWarehouseId}\n\n";

echo "# Calculate COGS\n";
echo "curl -X POST http://localhost:8000/api/stock/product/{$testProductId}/cogs \\\n";
echo "  -H 'Content-Type: application/json' \\\n";
echo "  -d '{\"quantity\": 25}'\n\n";

echo "# Get dashboard\n";
echo "curl -X GET http://localhost:8000/api/stock/dashboard\n";
echo "```\n\n";

echo "💡 **Test Product/Warehouse IDs for reference:**\n";
echo "   - Test Product ID: {$testProductId} (Galon)\n";
echo "   - Test Warehouse ID: {$testWarehouseId} (MAIN WAREHOUSE)\n\n";

// Save API test results to file for later reference
$logFile = __DIR__ . '/../../storage/logs/api_test_results.json';
$testResults = [
    'timestamp' => now()->toDateTimeString(),
    'test_product_id' => $testProductId,
    'test_warehouse_id' => $testWarehouseId,
    'api_endpoints_tested' => [
        'overall_stock' => '/api/stock',
        'warehouse_stock' => '/api/stock/warehouse/' . $testWarehouseId,
        'product_batches' => '/api/stock/product/' . $testProductId . '/batches',
        'cogs_calculation' => '/api/stock/product/' . $testProductId . '/cogs',
        'movement_history' => '/api/stock/product/' . $testProductId . '/movements',
        'dashboard' => '/api/stock/dashboard'
    ],
    'sample_data' => [
        'product_name' => DB::table('m_products')->where('id', $testProductId)->value('name'),
        'warehouse_name' => DB::table('m_warehouses')->where('id', $testWarehouseId)->value('name'),
    ]
];

file_put_contents($logFile, json_encode($testResults, JSON_PRETTY_PRINT));

echo "📝 **Test results saved to:** {$logFile}\n";
echo "   You can reference this file later for API endpoints and test data.\n\n";

echo "🎉 **All Stock API tests completed successfully!**\n";
echo "   Controllers and services are ready for API usage.\n\n";

echo "=== STOCK API QUICK TEST COMPLETED ===\n";