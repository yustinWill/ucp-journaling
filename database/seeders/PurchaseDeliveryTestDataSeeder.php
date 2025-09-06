<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchaseDeliveryTestDataSeeder extends Seeder
{
    private $companyCode = '001';

    private $warehouseId;

    private $supplierId;

    private $categoryId;

    private $unitId;

    private $paymentTermId;

    private $products = [];

    public function run()
    {
        DB::transaction(function () {
            $this->createBasicMasterData();
            $this->createProducts();

            // Create POs and PDs for different scenarios
            $this->createUndeliveredPO();
            $this->createPartiallyDeliveredPO();
            $this->createFullyDeliveredPO();

            echo "Sample data created successfully!\n";
        });
    }

    private function createBasicMasterData()
    {
        // Use existing warehouse or create if needed
        $warehouse = DB::table('m_warehouses')->first();
        if ($warehouse) {
            $this->warehouseId = $warehouse->id;
            echo "Using existing warehouse: {$warehouse->name}\n";
        } else {
            $this->warehouseId = DB::table('m_warehouses')->insertGetId([
                'company_code' => $this->companyCode,
                'code' => 'GDG-001',
                'name' => 'Gudang Utama RIYO',
                'address' => 'Jl. Industri No. 123, Jakarta',
                'status' => 'ACTIVE',
                'created_at' => now(),
                'created_by' => 1,
            ]);
        }

        // Use existing supplier or create
        $supplier = DB::table('m_contacts')->where('type', 'SUPPLIER')->first();
        if ($supplier) {
            $this->supplierId = $supplier->id;
            echo "Using existing supplier: {$supplier->name}\n";
        } else {
            $this->supplierId = DB::table('m_contacts')->insertGetId([
                'company_code' => $this->companyCode,
                'name' => 'PT Supplier Air Mineral',
                'type' => 'SUPPLIER',
                'email' => 'purchase@supplierairmineral.com',
                'phone' => '021-98765432',
                'status' => 'ACTIVE',
                'created_at' => now(),
                'created_by' => 1,
            ]);
        }

        // Use existing category or create
        $category = DB::table('m_categories')->first();
        if ($category) {
            $this->categoryId = $category->id;
            echo "Using existing category: {$category->name}\n";
        } else {
            $this->categoryId = DB::table('m_categories')->insertGetId([
                'company_code' => $this->companyCode,
                'code' => 'CAT-001',
                'name' => 'Air Mineral & Kemasan',
                'type' => 'PRODUCT',
                'status' => 'ACTIVE',
                'created_at' => now(),
                'created_by' => 1,
            ]);
        }

        // Use existing unit or create
        $unit = DB::table('m_units')->first();
        if ($unit) {
            $this->unitId = $unit->id;
            echo "Using existing unit: {$unit->name}\n";
        } else {
            $this->unitId = DB::table('m_units')->insertGetId([
                'code' => 'PCS',
                'name' => 'Pieces',
                'status' => 'ACTIVE',
                'created_at' => now(),
                'created_by' => 1,
            ]);
        }

        // Use existing payment term or create
        $paymentTerm = DB::table('m_payment_terms')->first();
        if ($paymentTerm) {
            $this->paymentTermId = $paymentTerm->id;
            echo "Using existing payment term: {$paymentTerm->name}\n";
        } else {
            $this->paymentTermId = DB::table('m_payment_terms')->insertGetId([
                'company_code' => $this->companyCode,
                'code' => 'NET30',
                'name' => 'Net 30 Days',
                'type' => 'DAYS',
                'days' => 30,
                'status' => 'ACTIVE',
                'created_at' => now(),
                'created_by' => 1,
            ]);
        }
    }

    private function createProducts()
    {
        // Use existing products first
        $existingProducts = DB::table('m_products')->get();

        foreach ($existingProducts as $product) {
            $this->products[] = [
                'id' => $product->id,
                'price' => $product->unit_purchase_price ?? 10000,
            ];
            echo "Using existing product: {$product->name}\n";
        }

        // We'll use existing products only to keep things simple
        if (count($this->products) < 2) {
            throw new \Exception('Need at least 2 existing products to create test scenarios');
        }

        echo 'Using '.count($this->products)." products for test scenarios\n";
    }

    private function createUndeliveredPO()
    {
        echo "Creating undelivered PO...\n";

        $poId = $this->createPurchaseOrder([
            'products' => [
                ['product_id' => $this->products[0]['id'], 'quantity' => 100, 'unit_price' => $this->products[0]['price']],
                ['product_id' => $this->products[1]['id'], 'quantity' => 50, 'unit_price' => $this->products[1]['price']],
            ],
        ]);

        echo "Created undelivered PO with ID: {$poId}\n";
    }

    private function createPartiallyDeliveredPO()
    {
        echo "Creating partially delivered PO...\n";

        $poId = $this->createPurchaseOrder([
            'products' => [
                ['product_id' => $this->products[0]['id'], 'quantity' => 200, 'unit_price' => $this->products[0]['price']],
                ['product_id' => $this->products[1]['id'], 'quantity' => 300, 'unit_price' => $this->products[1]['price']],
            ],
        ]);

        // Create partial delivery - deliver only part of the order
        $this->createPurchaseDelivery($poId, [
            'products' => [
                ['product_id' => $this->products[0]['id'], 'delivery_qty' => 100], // Half delivered
                ['product_id' => $this->products[1]['id'], 'delivery_qty' => 150], // Half delivered
            ],
        ]);

        echo "Created partially delivered PO with ID: {$poId}\n";
    }

    private function createFullyDeliveredPO()
    {
        echo "Creating fully delivered PO...\n";

        $poId = $this->createPurchaseOrder([
            'products' => [
                ['product_id' => $this->products[0]['id'], 'quantity' => 80, 'unit_price' => $this->products[0]['price']],
                ['product_id' => $this->products[1]['id'], 'quantity' => 120, 'unit_price' => $this->products[1]['price']],
            ],
        ]);

        // Create full delivery
        $this->createPurchaseDelivery($poId, [
            'products' => [
                ['product_id' => $this->products[0]['id'], 'delivery_qty' => 80], // Fully delivered
                ['product_id' => $this->products[1]['id'], 'delivery_qty' => 120], // Fully delivered
            ],
        ]);

        echo "Created fully delivered PO with ID: {$poId}\n";
    }

    private function createPurchaseOrder($data)
    {
        $referenceNo = 'PO-'.date('Ym').'-'.str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);

        // Create PO
        $poId = DB::table('t_purchase_orders')->insertGetId([
            'company_code' => $this->companyCode,
            'transaction_code' => $referenceNo,
            'transaction_date' => now()->format('Y-m-d'),
            'f_supplier_id' => $this->supplierId,
            'supplier_email' => 'purchase@supplierairmineral.com',
            'supplier_address' => 'Jl. Supplier No. 456, Bekasi',
            'f_payment_term_id' => $this->paymentTermId,
            'f_warehouse_id' => $this->warehouseId,
            'description' => 'Purchase Order for testing delivery scenarios',
            'subtotal' => 0,
            'total_tax' => 0,
            'total_price_before_discount' => 0,
            'discount' => 0,
            'grand_price' => 0,
            'status' => 'CONFIRMED',
            'created_at' => now(),
            'created_by' => 1,
        ]);

        $subtotal = 0;
        foreach ($data['products'] as $index => $product) {
            $lineTotal = $product['quantity'] * $product['unit_price'];
            $subtotal += $lineTotal;

            DB::table('t_purchase_order_products')->insert([
                'f_purchase_order_id' => $poId,
                'f_product_id' => $product['product_id'],
                'qty' => $product['quantity'],
                'f_unit_id' => $this->unitId,
                'price_per_qty' => $product['unit_price'],
                'total' => $lineTotal,
                'created_at' => now(),
                'created_by' => 1,
            ]);
        }

        // Update PO totals
        DB::table('t_purchase_orders')->where('id', $poId)->update([
            'subtotal' => $subtotal,
            'total_price_before_discount' => $subtotal,
            'grand_price' => $subtotal,
            'updated_at' => now(),
            'updated_by' => 1,
        ]);

        return $poId;
    }

    private function createPurchaseDelivery($poId, $data)
    {
        $referenceNo = 'PD-'.date('Ym').'-'.str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        $deliveryNo = 'DN-'.date('Ym').'-'.str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);

        // Create PD
        $pdId = DB::table('t_purchase_deliveries')->insertGetId([
            'company_code' => $this->companyCode,
            'transaction_code' => $referenceNo,
            'delivery_date' => now()->format('Y-m-d'),
            'f_purchase_order_id' => $poId,
            'f_supplier_id' => $this->supplierId,
            'supplier_email' => 'purchase@supplierairmineral.com',
            'supplier_address' => 'Jl. Supplier No. 456, Bekasi',
            'f_warehouse_id' => $this->warehouseId,
            'description' => 'Test delivery for PO scenarios',
            'total_item' => 0,
            'created_at' => now(),
            'created_by' => 1,
        ]);

        $totalItems = 0;
        $deliveredItems = 0;

        foreach ($data['products'] as $product) {
            $deliveredItems += $product['delivery_qty'];

            DB::table('t_purchase_delivery_products')->insert([
                'f_purchase_delivery_id' => $pdId,
                'f_product_id' => $product['product_id'],
                'qty' => $product['delivery_qty'],
                'f_unit_id' => $this->unitId,
                'created_at' => now(),
                'created_by' => 1,
            ]);
        }

        // Update PD totals
        DB::table('t_purchase_deliveries')->where('id', $pdId)->update([
            'total_item' => $deliveredItems,
            'updated_at' => now(),
            'updated_by' => 1,
        ]);

        return $pdId;
    }
}
