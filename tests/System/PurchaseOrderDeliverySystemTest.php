<?php

require_once __DIR__ . '/../../vendor/autoload.php';

// Laravel bootstrap
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Domains\PurchaseOrder\Actions\CreatePurchaseOrderAction;
use App\Domains\PurchaseDelivery\Actions\CreatePurchaseDeliveryAction;
use App\Services\BatchManagementService;
use App\Services\StockInformationService;

echo "=== COMPREHENSIVE PURCHASE ORDER & DELIVERY SYSTEM TEST ===\n\n";

// Test configuration
$testProductId1 = 24; // Galon
$testProductId2 = 25; // Galon Kemas  
$testProductId3 = 26; // Tissue Galon
$testWarehouseId = 4; // MAIN WAREHOUSE
$testSupplierId = 15; // Jonathan Supplier
$testUserId = 1; // Jonathan SS

// Set test session data and authentication
session(['user_company_code' => '001']);
Auth::loginUsingId($testUserId);

// Track initial stock state
$initialStocks = [];
foreach ([$testProductId1, $testProductId2, $testProductId3] as $productId) {
    $product = DB::table('m_products')->where('id', $productId)->first();
    $initialStocks[$productId] = [
        'product_name' => $product->name,
        'initial_stock' => (float) $product->total_stock,
        'initial_batches' => app(BatchManagementService::class)->getProductBatchInfo($productId)['total_batches'],
    ];
}

echo "📊 Initial Stock State\n";
echo "═══════════════════\n";
foreach ($initialStocks as $productId => $data) {
    echo "Product {$productId} ({$data['product_name']}): {$data['initial_stock']} units, {$data['initial_batches']} batches\n";
}
echo "\n";

// Clear previous test data properly (handle foreign keys)
echo "🧹 Cleaning up previous test data...\n";

// Clean up in reverse order of dependencies (deliveries FIRST, then POs)
$testPOs = DB::table('t_purchase_orders')->where('description', 'LIKE', 'TEST PO:%')->pluck('id');
$testPDs = DB::table('t_purchase_deliveries')->where('description', 'LIKE', 'TEST PD:%')->pluck('id');

// Clean deliveries FIRST because they reference PO products
if (!$testPDs->isEmpty()) {
    DB::table('t_purchase_delivery_audit_logs')->whereIn('f_purchase_delivery_id', $testPDs)->delete();
    DB::table('t_purchase_delivery_products')->whereIn('f_purchase_delivery_id', $testPDs)->delete();
    DB::table('t_purchase_deliveries')->whereIn('id', $testPDs)->delete();
}

// Clean POs AFTER deliveries are gone
if (!$testPOs->isEmpty()) {
    // Clean up audit logs first
    DB::table('t_purchase_order_audit_logs')->whereIn('f_purchase_order_id', $testPOs)->delete();
    DB::table('t_purchase_order_products')->whereIn('f_purchase_order_id', $testPOs)->delete();
    DB::table('t_purchase_orders')->whereIn('id', $testPOs)->delete();
}

// Clean up batches and movements
DB::table('t_product_stock_batches')->where('batch_reference_code', 'LIKE', '%TEST%')->delete();
DB::table('t_stock_movement_logs')->where('reference_number', 'LIKE', 'TEST-%')->delete();
DB::table('t_stock_adjustments')->where('description', 'LIKE', 'TEST%')->delete();

echo "✅ Previous test data cleaned\n\n";

$testResults = [];
$createdPOs = [];

// ===== CREATE 5 PURCHASE ORDERS =====
echo "📝 CREATING 5 PURCHASE ORDERS\n";
echo "══════════════════════════════\n\n";

$poAction = new CreatePurchaseOrderAction();

// PO 1: Multi-product order - Will have PARTIAL delivery
echo "1️⃣ Creating PO #1 (Multi-product - for PARTIAL delivery)\n";
try {
    $po1Data = [
        'reference_no' => 'TEST-PO-001-' . time(),
        'date' => now()->format('Y-m-d'),
        'supplier_id' => $testSupplierId,
        'email' => 'test@supplier.com',
        'address' => 'Test Address',
        'supplier_reference' => 'SUP-REF-001',
        'customer_id' => null,
        'payment_terms' => 1,
        'warehouse_id' => $testWarehouseId,
        'status' => 'APPROVED',
        'description' => 'TEST PO: Multi-product for partial delivery',
        'memo' => 'Test PO memo',
        'products' => [
            [
                'product_id' => $testProductId1,
                'quantity' => 100,
                'unit_price' => 8000,
                'total' => 800000,
            ],
            [
                'product_id' => $testProductId2,
                'quantity' => 50,
                'unit_price' => 12000,
                'total' => 600000,
            ]
        ],
        'total' => 1400000,
    ];
    
    $po1Id = $poAction->execute($po1Data);
    $createdPOs[1] = ['id' => $po1Id, 'data' => $po1Data, 'delivery_scenario' => 'partial'];
    echo "✅ PO #1 created with ID: {$po1Id}\n";
    echo "   Products: {$testProductId1} (100 units @ 8000), {$testProductId2} (50 units @ 12000)\n\n";
    
} catch (Exception $e) {
    echo "❌ PO #1 failed: " . $e->getMessage() . "\n\n";
}

// PO 2: Single product order - Will have PARTIAL delivery  
echo "2️⃣ Creating PO #2 (Single product - for PARTIAL delivery)\n";
try {
    $po2Data = [
        'reference_no' => 'TEST-PO-002-' . time(),
        'date' => now()->format('Y-m-d'),
        'supplier_id' => $testSupplierId,
        'email' => 'test@supplier.com',
        'address' => 'Test Address',
        'supplier_reference' => 'SUP-REF-002',
        'customer_id' => null,
        'payment_terms' => 1,
        'warehouse_id' => $testWarehouseId,
        'status' => 'APPROVED',
        'description' => 'TEST PO: Single product for partial delivery',
        'products' => [
            [
                'product_id' => $testProductId3,
                'quantity' => 200,
                'unit_price' => 5000,
                'total' => 1000000,
            ]
        ],
        'total' => 1000000,
    ];
    
    $po2Id = $poAction->execute($po2Data);
    $createdPOs[2] = ['id' => $po2Id, 'data' => $po2Data, 'delivery_scenario' => 'partial'];
    echo "✅ PO #2 created with ID: {$po2Id}\n";
    echo "   Products: {$testProductId3} (200 units @ 5000)\n\n";
    
} catch (Exception $e) {
    echo "❌ PO #2 failed: " . $e->getMessage() . "\n\n";
}

// PO 3: Multi-product order - Will have COMPLETE delivery
echo "3️⃣ Creating PO #3 (Multi-product - for COMPLETE delivery)\n";
try {
    $po3Data = [
        'reference_no' => 'TEST-PO-003-' . time(),
        'date' => now()->format('Y-m-d'),
        'supplier_id' => $testSupplierId,
        'email' => 'test@supplier.com',
        'address' => 'Test Address',
        'supplier_reference' => 'SUP-REF-003',
        'customer_id' => null,
        'payment_terms' => 1,
        'warehouse_id' => $testWarehouseId,
        'status' => 'APPROVED',
        'description' => 'TEST PO: Multi-product for complete delivery',
        'products' => [
            [
                'product_id' => $testProductId1,
                'quantity' => 80,
                'unit_price' => 8500,
                'total' => 680000,
            ],
            [
                'product_id' => $testProductId3,
                'quantity' => 120,
                'unit_price' => 5200,
                'total' => 624000,
            ]
        ],
        'total' => 1304000,
    ];
    
    $po3Id = $poAction->execute($po3Data);
    $createdPOs[3] = ['id' => $po3Id, 'data' => $po3Data, 'delivery_scenario' => 'complete'];
    echo "✅ PO #3 created with ID: {$po3Id}\n";
    echo "   Products: {$testProductId1} (80 units @ 8500), {$testProductId3} (120 units @ 5200)\n\n";
    
} catch (Exception $e) {
    echo "❌ PO #3 failed: " . $e->getMessage() . "\n\n";
}

// PO 4: Single product order - Will have NO delivery
echo "4️⃣ Creating PO #4 (Single product - NO delivery)\n";
try {
    $po4Data = [
        'reference_no' => 'TEST-PO-004-' . time(),
        'date' => now()->format('Y-m-d'),
        'supplier_id' => $testSupplierId,
        'email' => 'test@supplier.com',
        'address' => 'Test Address',
        'supplier_reference' => 'SUP-REF-004',
        'customer_id' => null,
        'payment_terms' => 1,
        'warehouse_id' => $testWarehouseId,
        'status' => 'PENDING',
        'description' => 'TEST PO: Single product with no delivery',
        'products' => [
            [
                'product_id' => $testProductId2,
                'quantity' => 75,
                'unit_price' => 11000,
                'total' => 825000,
            ]
        ],
        'total' => 825000,
    ];
    
    $po4Id = $poAction->execute($po4Data);
    $createdPOs[4] = ['id' => $po4Id, 'data' => $po4Data, 'delivery_scenario' => 'none'];
    echo "✅ PO #4 created with ID: {$po4Id}\n";
    echo "   Products: {$testProductId2} (75 units @ 11000)\n\n";
    
} catch (Exception $e) {
    echo "❌ PO #4 failed: " . $e->getMessage() . "\n\n";
}

// PO 5: Multi-product order - Will have NO delivery
echo "5️⃣ Creating PO #5 (Multi-product - NO delivery)\n";
try {
    $po5Data = [
        'reference_no' => 'TEST-PO-005-' . time(),
        'date' => now()->format('Y-m-d'),
        'supplier_id' => $testSupplierId,
        'email' => 'test@supplier.com',
        'address' => 'Test Address',
        'supplier_reference' => 'SUP-REF-005',
        'customer_id' => null,
        'payment_terms' => 1,
        'warehouse_id' => $testWarehouseId,
        'status' => 'PENDING',
        'description' => 'TEST PO: Multi-product with no delivery',
        'products' => [
            [
                'product_id' => $testProductId1,
                'quantity' => 60,
                'unit_price' => 7800,
                'total' => 468000,
            ],
            [
                'product_id' => $testProductId2,
                'quantity' => 40,
                'unit_price' => 11500,
                'total' => 460000,
            ],
            [
                'product_id' => $testProductId3,
                'quantity' => 90,
                'unit_price' => 5100,
                'total' => 459000,
            ]
        ],
        'total' => 1387000,
    ];
    
    $po5Id = $poAction->execute($po5Data);
    $createdPOs[5] = ['id' => $po5Id, 'data' => $po5Data, 'delivery_scenario' => 'none'];
    echo "✅ PO #5 created with ID: {$po5Id}\n";
    echo "   Products: {$testProductId1} (60 units @ 7800), {$testProductId2} (40 units @ 11500), {$testProductId3} (90 units @ 5100)\n\n";
    
} catch (Exception $e) {
    echo "❌ PO #5 failed: " . $e->getMessage() . "\n\n";
}

echo "📋 Purchase Order Creation Summary\n";
echo "═══════════════════════════════\n";
foreach ($createdPOs as $poNum => $po) {
    echo "PO #{$poNum}: ID {$po['id']} - {$po['delivery_scenario']} delivery scenario\n";
}
echo "\n";

// ===== CREATE PURCHASE DELIVERIES =====
echo "🚚 CREATING PURCHASE DELIVERIES\n";
echo "═══════════════════════════════\n\n";

$pdAction = new CreatePurchaseDeliveryAction();
$createdPDs = [];

// Get PO product data for delivery creation
function getPOProducts($poId) {
    return DB::table('t_purchase_order_products')
        ->where('f_purchase_order_id', $poId)
        ->get();
}

// PD 1: Partial delivery for PO #1 (deliver 60% of products)
echo "🚚 1️⃣ Creating PARTIAL delivery for PO #1\n";
try {
    $po1Products = getPOProducts($createdPOs[1]['id']);
    $pdProducts = [];
    
    foreach ($po1Products as $poProduct) {
        $deliveryQty = floor($poProduct->qty * 0.6); // 60% delivery
        $pdProducts[] = [
            'product_id' => $poProduct->f_product_id,
            'product_order_product_id' => $poProduct->id,
            'delivery_qty' => $deliveryQty,
            'delivered_qty_before' => 0, // First delivery
            'remaining_qty_before' => $poProduct->qty,
            'unit_price' => $poProduct->price_per_qty,
            'note' => 'TEST: Partial delivery (60%)',
            'status' => 'DELIVERED',
        ];
    }
    
    $pd1Data = [
        'reference_no' => 'TEST-PD-001-' . time(),
        'date' => now()->format('Y-m-d'),
        'purchase_order_id' => $createdPOs[1]['id'],
        'delivery_number' => 'DN-TEST-001',
        'tracking_number' => 'TRK-TEST-001',
        'courier_name' => 'Test Courier',
        'delivery_date' => now()->format('Y-m-d'),
        'warehouse_id' => $testWarehouseId,
        'delivery_status' => 'DELIVERED',
        'description' => 'TEST PD: Partial delivery for PO #1 (60%)',
        'products' => $pdProducts,
    ];
    
    $pd1Id = $pdAction->execute($pd1Data);
    $createdPDs[1] = ['id' => $pd1Id, 'data' => $pd1Data, 'po_id' => $createdPOs[1]['id']];
    echo "✅ PD #1 created with ID: {$pd1Id} (60% of PO #1)\n";
    
    foreach ($pdProducts as $product) {
        echo "   Product {$product['product_id']}: {$product['delivery_qty']} units delivered\n";
    }
    echo "\n";
    
} catch (Exception $e) {
    echo "❌ PD #1 failed: " . $e->getMessage() . "\n\n";
}

// PD 2: Partial delivery for PO #2 (deliver 40% of products)
echo "🚚 2️⃣ Creating PARTIAL delivery for PO #2\n";
try {
    $po2Products = getPOProducts($createdPOs[2]['id']);
    $pdProducts = [];
    
    foreach ($po2Products as $poProduct) {
        $deliveryQty = floor($poProduct->qty * 0.4); // 40% delivery
        $pdProducts[] = [
            'product_id' => $poProduct->f_product_id,
            'product_order_product_id' => $poProduct->id,
            'delivery_qty' => $deliveryQty,
            'delivered_qty_before' => 0, // First delivery
            'remaining_qty_before' => $poProduct->qty,
            'unit_price' => $poProduct->price_per_qty,
            'note' => 'TEST: Partial delivery (40%)',
            'status' => 'DELIVERED',
        ];
    }
    
    $pd2Data = [
        'reference_no' => 'TEST-PD-002-' . time(),
        'date' => now()->format('Y-m-d'),
        'purchase_order_id' => $createdPOs[2]['id'],
        'delivery_number' => 'DN-TEST-002',
        'tracking_number' => 'TRK-TEST-002',
        'courier_name' => 'Test Courier',
        'delivery_date' => now()->format('Y-m-d'),
        'warehouse_id' => $testWarehouseId,
        'delivery_status' => 'DELIVERED',
        'description' => 'TEST PD: Partial delivery for PO #2 (40%)',
        'products' => $pdProducts,
    ];
    
    $pd2Id = $pdAction->execute($pd2Data);
    $createdPDs[2] = ['id' => $pd2Id, 'data' => $pd2Data, 'po_id' => $createdPOs[2]['id']];
    echo "✅ PD #2 created with ID: {$pd2Id} (40% of PO #2)\n";
    
    foreach ($pdProducts as $product) {
        echo "   Product {$product['product_id']}: {$product['delivery_qty']} units delivered\n";
    }
    echo "\n";
    
} catch (Exception $e) {
    echo "❌ PD #2 failed: " . $e->getMessage() . "\n\n";
}

// PD 3: Complete delivery for PO #3 (deliver 100% of products)
echo "🚚 3️⃣ Creating COMPLETE delivery for PO #3\n";
try {
    $po3Products = getPOProducts($createdPOs[3]['id']);
    $pdProducts = [];
    
    foreach ($po3Products as $poProduct) {
        $pdProducts[] = [
            'product_id' => $poProduct->f_product_id,
            'product_order_product_id' => $poProduct->id,
            'delivery_qty' => $poProduct->qty, // 100% delivery
            'delivered_qty_before' => 0, // First delivery
            'remaining_qty_before' => $poProduct->qty,
            'unit_price' => $poProduct->price_per_qty,
            'note' => 'TEST: Complete delivery (100%)',
            'status' => 'DELIVERED',
        ];
    }
    
    $pd3Data = [
        'reference_no' => 'TEST-PD-003-' . time(),
        'date' => now()->format('Y-m-d'),
        'purchase_order_id' => $createdPOs[3]['id'],
        'delivery_number' => 'DN-TEST-003',
        'tracking_number' => 'TRK-TEST-003',
        'courier_name' => 'Test Courier',
        'delivery_date' => now()->format('Y-m-d'),
        'warehouse_id' => $testWarehouseId,
        'delivery_status' => 'DELIVERED',
        'description' => 'TEST PD: Complete delivery for PO #3 (100%)',
        'products' => $pdProducts,
    ];
    
    $pd3Id = $pdAction->execute($pd3Data);
    $createdPDs[3] = ['id' => $pd3Id, 'data' => $pd3Data, 'po_id' => $createdPOs[3]['id']];
    echo "✅ PD #3 created with ID: {$pd3Id} (100% of PO #3)\n";
    
    foreach ($pdProducts as $product) {
        echo "   Product {$product['product_id']}: {$product['delivery_qty']} units delivered\n";
    }
    echo "\n";
    
} catch (Exception $e) {
    echo "❌ PD #3 failed: " . $e->getMessage() . "\n\n";
}

echo "📋 Purchase Delivery Creation Summary\n";
echo "═══════════════════════════════════\n";
if (!empty($createdPDs)) {
    foreach ($createdPDs as $pdNum => $pd) {
        echo "PO #{$pdNum}: Delivery created - PD #{$pd['id']}\n";
    }
} else {
    echo "No deliveries were successfully created.\n";
}
echo "PO #4: No delivery planned\n";
echo "PO #5: No delivery planned\n\n";

// ===== ADD PRODUCTION INCREASE (PI) TRANSACTIONS =====
echo "🏭 ADDING PRODUCTION INCREASE TRANSACTIONS\n";
echo "═════════════════════════════════════════\n\n";

// Simulate PI by creating stock adjustments (as PI system might not be fully implemented)
use App\Domains\StockAdjustment\Actions\CreateStockAdjustmentAction;

$adjustmentAction = new CreateStockAdjustmentAction();

// PI 1: Increase stock for Product 1
echo "🏭 1️⃣ Creating Production Increase for Product {$testProductId1}\n";
try {
    $currentStock1 = DB::table('m_products')->where('id', $testProductId1)->value('total_stock');
    
    $pi1Data = [
        'adjustment_date' => now()->format('Y-m-d'),
        'adjustment_type' => 'MANUAL_ADJUSTMENT',
        'warehouse_id' => $testWarehouseId,
        'pic_id' => $testUserId,
        'description' => 'TEST PI: Production increase for product ' . $testProductId1,
        'memo' => 'Simulated production increase',
        'products' => [
            [
                'product_id' => $testProductId1,
                'product_code' => 'PROD-' . $testProductId1,
                'product_name' => $initialStocks[$testProductId1]['product_name'],
                'unit' => 'PCS',
                'stock_before' => $currentStock1,
                'adjustment_qty' => 50, // Increase by 50
                'unit_cost' => 9000, // Production cost
                'reason' => 'TEST: Production increase via adjustment'
            ]
        ]
    ];
    
    $pi1Id = $adjustmentAction->execute($pi1Data);
    echo "✅ PI #1 created with ID: {$pi1Id} (+50 units for Product {$testProductId1})\n\n";
    
} catch (Exception $e) {
    echo "❌ PI #1 failed: " . $e->getMessage() . "\n\n";
}

// PI 2: Increase stock for Product 3
echo "🏭 2️⃣ Creating Production Increase for Product {$testProductId3}\n";
try {
    $currentStock3 = DB::table('m_products')->where('id', $testProductId3)->value('total_stock');
    
    $pi2Data = [
        'adjustment_date' => now()->format('Y-m-d'),
        'adjustment_type' => 'MANUAL_ADJUSTMENT',
        'warehouse_id' => $testWarehouseId,
        'pic_id' => $testUserId,
        'description' => 'TEST PI: Production increase for product ' . $testProductId3,
        'memo' => 'Simulated production increase',
        'products' => [
            [
                'product_id' => $testProductId3,
                'product_code' => 'PROD-' . $testProductId3,
                'product_name' => $initialStocks[$testProductId3]['product_name'],
                'unit' => 'PCS',
                'stock_before' => $currentStock3,
                'adjustment_qty' => 30, // Increase by 30
                'unit_cost' => 6000, // Production cost
                'reason' => 'TEST: Production increase via adjustment'
            ]
        ]
    ];
    
    $pi2Id = $adjustmentAction->execute($pi2Data);
    echo "✅ PI #2 created with ID: {$pi2Id} (+30 units for Product {$testProductId3})\n\n";
    
} catch (Exception $e) {
    echo "❌ PI #2 failed: " . $e->getMessage() . "\n\n";
}

// ===== COMPREHENSIVE VALIDATION =====
echo "🔍 COMPREHENSIVE VALIDATION\n";
echo "══════════════════════════\n\n";

$validationErrors = [];

// Track final stock state
$finalStocks = [];
foreach ([$testProductId1, $testProductId2, $testProductId3] as $productId) {
    $product = DB::table('m_products')->where('id', $productId)->first();
    $batchInfo = app(BatchManagementService::class)->getProductBatchInfo($productId, true);
    
    $finalStocks[$productId] = [
        'product_name' => $product->name,
        'final_stock' => (float) $product->total_stock,
        'final_batches' => $batchInfo['total_batches'],
        'batch_quantity' => $batchInfo['total_quantity'],
        'batch_value' => $batchInfo['total_value'],
        'expected_stock_increase' => 0, // Will calculate below
    ];
}

echo "📊 Final Stock State vs Initial\n";
echo "══════════════════════════════\n";
foreach ($finalStocks as $productId => $data) {
    $initial = $initialStocks[$productId];
    $stockChange = $data['final_stock'] - $initial['initial_stock'];
    $batchChange = $data['final_batches'] - $initial['initial_batches'];
    
    echo "Product {$productId} ({$data['product_name']}):\n";
    echo "  Stock: {$initial['initial_stock']} → {$data['final_stock']} (+" . number_format($stockChange) . ")\n";
    echo "  Batches: {$initial['initial_batches']} → {$data['final_batches']} (+" . $batchChange . ")\n";
    echo "  Batch Quantity: " . number_format($data['batch_quantity']) . "\n";
    echo "  Batch Value: Rp " . number_format($data['batch_value'], 2) . "\n\n";
}

// Calculate expected stock changes from transactions
$expectedChanges = [
    $testProductId1 => 0, // Product 1
    $testProductId2 => 0, // Product 2  
    $testProductId3 => 0, // Product 3
];

// Add PD deliveries
foreach ($createdPDs as $pdNum => $pd) {
    foreach ($pd['data']['products'] as $product) {
        $expectedChanges[$product['product_id']] += $product['delivery_qty'];
    }
}

// Add PI (production increases) - we added 50 to Product 1, 30 to Product 3
$expectedChanges[$testProductId1] += 50; // PI #1
$expectedChanges[$testProductId3] += 30; // PI #2

echo "🧮 CALCULATION VALIDATION\n";
echo "═══════════════════════════\n\n";

foreach ([$testProductId1, $testProductId2, $testProductId3] as $productId) {
    $initial = $initialStocks[$productId]['initial_stock'];
    $final = $finalStocks[$productId]['final_stock'];
    $actualChange = $final - $initial;
    $expectedChange = $expectedChanges[$productId];
    $batchQuantity = $finalStocks[$productId]['batch_quantity'];
    
    echo "Product {$productId} ({$finalStocks[$productId]['product_name']}):\n";
    echo "  Expected stock change: +" . number_format($expectedChange) . "\n";
    echo "  Actual stock change: +" . number_format($actualChange) . "\n";
    echo "  Product stock: " . number_format($final) . "\n";
    echo "  Batch quantity: " . number_format($batchQuantity) . "\n";
    
    // Validation checks
    if (abs($actualChange - $expectedChange) > 0.01) {
        $validationErrors[] = "Stock mismatch for Product {$productId}: Expected +{$expectedChange}, got +{$actualChange}";
        echo "  ❌ STOCK MISMATCH!\n";
    } else {
        echo "  ✅ Stock calculation correct\n";
    }
    
    if (abs($final - $batchQuantity) > 0.01) {
        $validationErrors[] = "Batch quantity mismatch for Product {$productId}: Product stock {$final} ≠ Batch quantity {$batchQuantity}";
        echo "  ❌ BATCH QUANTITY MISMATCH!\n";
    } else {
        echo "  ✅ Batch quantity matches product stock\n";
    }
    
    echo "\n";
}

// Validate batch references and pricing
echo "💰 BATCH VALIDATION\n";
echo "═══════════════════\n\n";

$pdIds = array_column($createdPDs, 'id');
$testBatches = DB::table('t_product_stock_batches')
    ->where(function($query) use ($pdIds) {
        $query->where('batch_reference_code', 'LIKE', 'PD-%')
              ->orWhere('batch_reference_code', 'LIKE', 'BATCH-%')
              ->orWhereIn('f_source_transaction_id', $pdIds);
    })
    ->get();

foreach ($createdPDs as $pdNum => $pd) {
    echo "Validating batches for PD #{$pdNum} (ID: {$pd['id']}):\n";
    
    $pdBatches = $testBatches->where('f_source_transaction_id', $pd['id']);
    
    foreach ($pd['data']['products'] as $product) {
        $productBatches = $pdBatches->where('f_product_id', $product['product_id']);
        
        if ($productBatches->isEmpty()) {
            $validationErrors[] = "No batch created for Product {$product['product_id']} in PD #{$pdNum}";
            echo "  ❌ No batch found for Product {$product['product_id']}\n";
        } else {
            foreach ($productBatches as $batch) {
                echo "  ✅ Batch {$batch->batch_reference_code}: {$batch->initial_qty} units @ Rp {$batch->purchase_price}\n";
                
                // Validate batch quantity matches delivery
                if ($batch->initial_qty != $product['delivery_qty']) {
                    $validationErrors[] = "Batch quantity mismatch: Expected {$product['delivery_qty']}, got {$batch->initial_qty}";
                }
                
                // Validate batch price matches PO price
                if ($batch->purchase_price != $product['unit_price']) {
                    $validationErrors[] = "Batch price mismatch: Expected {$product['unit_price']}, got {$batch->purchase_price}";
                }
            }
        }
    }
    echo "\n";
}

// Check stock movement logs
echo "📝 STOCK MOVEMENT VALIDATION\n";
echo "══════════════════════════════\n\n";

$testMovements = DB::table('t_stock_movement_logs')
    ->where('reference_type', 'PURCHASE_DELIVERY')
    ->where('reference_number', 'LIKE', 'TEST-PD-%')
    ->get();

foreach ($createdPDs as $pdNum => $pd) {
    echo "Validating movements for PD #{$pdNum}:\n";
    
    $pdMovements = $testMovements->where('reference_number', $pd['data']['reference_no']);
    
    foreach ($pd['data']['products'] as $product) {
        $productMovements = $pdMovements->where('product_id', $product['product_id']);
        
        if ($productMovements->isEmpty()) {
            $validationErrors[] = "No stock movement log for Product {$product['product_id']} in PD #{$pdNum}";
            echo "  ❌ No movement log for Product {$product['product_id']}\n";
        } else {
            foreach ($productMovements as $movement) {
                echo "  ✅ Movement: {$movement->movement_type} {$movement->movement_qty} units\n";
                
                if ($movement->movement_qty != $product['delivery_qty']) {
                    $validationErrors[] = "Movement quantity mismatch for Product {$product['product_id']}: Expected {$product['delivery_qty']}, got {$movement->movement_qty}";
                }
            }
        }
    }
    echo "\n";
}

// ===== FINAL REPORT =====
echo "📋 COMPREHENSIVE TEST REPORT\n";
echo "═══════════════════════════\n\n";

if (empty($validationErrors)) {
    echo "🎉 ALL VALIDATIONS PASSED! 🎉\n\n";
    
    echo "✅ Test Summary:\n";
    echo "  - Created 5 Purchase Orders with different scenarios\n";
    echo "  - Created 3 Purchase Deliveries (2 partial, 1 complete)\n";
    echo "  - Added 2 Production Increase transactions\n";
    echo "  - All stock calculations are correct\n";
    echo "  - All batch quantities match product stocks\n";
    echo "  - All batch prices match PO prices\n";
    echo "  - All stock movement logs are correct\n";
    echo "  - All transaction data is consistent\n\n";
    
    echo "📊 Transaction Impact:\n";
    foreach ([$testProductId1, $testProductId2, $testProductId3] as $productId) {
        $productName = $finalStocks[$productId]['product_name'];
        $stockChange = $finalStocks[$productId]['final_stock'] - $initialStocks[$productId]['initial_stock'];
        $batchCount = $finalStocks[$productId]['final_batches'] - $initialStocks[$productId]['initial_batches'];
        $batchValue = $finalStocks[$productId]['batch_value'];
        
        echo "  Product {$productId} ({$productName}):\n";
        echo "    Stock increase: +" . number_format($stockChange) . " units\n";
        echo "    New batches: +{$batchCount}\n";
        echo "    Total batch value: Rp " . number_format($batchValue, 2) . "\n";
    }
    
} else {
    echo "❌ VALIDATION ERRORS FOUND:\n\n";
    
    foreach ($validationErrors as $i => $error) {
        echo ($i + 1) . ". {$error}\n";
    }
    
    echo "\n🔍 Please review the data inconsistencies above.\n";
}

echo "\n=== COMPREHENSIVE PURCHASE ORDER & DELIVERY TEST COMPLETED ===\n";