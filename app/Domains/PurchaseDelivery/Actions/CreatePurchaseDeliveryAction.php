<?php

namespace App\Domains\PurchaseDelivery\Actions;

use App\Domains\Product\Services\ProductService;
use App\Domains\PurchaseDelivery\Services\PurchaseDeliveryAuditLogService;
use App\Domains\PurchaseDelivery\Services\PurchaseDeliveryService;
use App\Domains\PurchaseOrder\Services\PurchaseOrderProductService;
use App\Enums\ActionTypes;
use App\Services\BatchManagementService;
use App\Services\StockMovementLogService;
use Illuminate\Support\Facades\DB;

class CreatePurchaseDeliveryAction
{
    public function validatePurchaseDeliveryData(array $data): array
    {
        $errors = [];

        // Basic validation
        if (empty($data['date'])) {
            $errors[] = 'Tanggal pengiriman wajib diisi';
        }

        if (empty($data['reference_no'])) {
            $errors[] = 'Nomor transaksi wajib diisi';
        }

        if (empty($data['purchase_order_id'])) {
            $errors[] = 'Purchase Order wajib dipilih';
        }

        if (empty($data['delivery_number'])) {
            $errors[] = 'Nomor delivery wajib diisi';
        }

        if (empty($data['delivery_date'])) {
            $errors[] = 'Tanggal delivery wajib diisi';
        }

        if (empty($data['warehouse_id'])) {
            $errors[] = 'Gudang tujuan wajib dipilih';
        }

        // Validate products
        if (empty($data['products']) || ! is_array($data['products'])) {
            $errors[] = 'Minimal satu produk harus dipilih';
        }

        return [
            'is_valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    public function execute(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Handle file upload
            if (isset($data['attachment']) && $data['attachment'] instanceof \Illuminate\Http\UploadedFile) {
                $path = $data['attachment']->store('purchase_delivery', 'public');
                $data['attachment'] = $path;
            }

            // Create purchase delivery record
            $deliveryId = app(PurchaseDeliveryService::class)->create($data);

            // Create delivery products
            if (isset($data['products']) && is_array($data['products'])) {
                app(PurchaseDeliveryService::class)->createDeliveryProducts($deliveryId, $data['products']);

                // Update weighted average HPP BEFORE creating batches and updating stock
                $this->updateProductAverageHPP($data);

                // Create product batches for HPP tracking with updated average price
                $this->createProductBatches($deliveryId, $data);

                foreach ($data['products'] as $product) {
                    app(PurchaseOrderProductService::class)->updateDeliveredQuantities($data['purchase_order_id'], $product['product_order_product_id'], $product['delivery_qty']);


                    // Record stock movement
                    app(StockMovementLogService::class)->recordMovement(
                        $data['warehouse_id'],
                        $product['product_id'],
                        'PURCHASE_IN',
                        'PURCHASE_DELIVERY',
                        $data['reference_no'],
                        $product['delivery_qty'],
                        'Purchase Delivery: '.($data['reference_no']),
                        $data['delivery_date'] ?? now()
                    );

                    app(ProductService::class)->updateStock($product['product_id'], '+', $product['delivery_qty']);
                }
            }

            // Create audit log
            $logData = [
                'action_type' => ActionTypes::CREATE,
                'description' => 'Pembentukan Purchase Delivery',
                'metadata' => json_encode([
                    'before' => null,
                    'after' => $data,
                ]),
            ];
            app(PurchaseDeliveryAuditLogService::class)->create($logData, $deliveryId);

            return $deliveryId;
        });
    }

    /**
     * Update product average HPP using weighted average formula
     * This must be called BEFORE updating stock
     */
    private function updateProductAverageHPP(array $data)
    {
        if (! isset($data['purchase_order_id']) || ! isset($data['products'])) {
            return;
        }

        // Get PO products with pricing information
        $poProducts = DB::table('t_purchase_order_products as pop')
            ->select('pop.f_product_id', 'pop.price_per_qty')
            ->where('pop.f_purchase_order_id', $data['purchase_order_id'])
            ->get()
            ->keyBy('f_product_id');

        foreach ($data['products'] as $product) {
            if (! isset($product['delivery_qty']) || $product['delivery_qty'] <= 0) {
                continue;
            }

            $productId = $product['product_id'];
            $deliveryQty = $product['delivery_qty'];

            // Get the purchase price from PO
            $poProduct = $poProducts->get($productId);
            if (! $poProduct) {
                continue;
            }

            $purchasePrice = (float) $poProduct->price_per_qty;

            // Update product average price using weighted average formula
            // Formula: ((harga beli × qty beli) + (harga stock sebelumnya × qty sisa stock sebelumnya)) / (stock beli + stock sisa)
            app(ProductService::class)->updateAveragePriceWeighted($productId, $purchasePrice, $deliveryQty);
        }
    }

    /**
     * Create product stock batches for HPP tracking using BatchManagementService
     * This should be called AFTER updateProductAverageHPP
     */
    private function createProductBatches(int $deliveryId, array $data)
    {
        if (! isset($data['purchase_order_id']) || ! isset($data['products'])) {
            return;
        }

        // Get PO products with pricing information
        $poProducts = DB::table('t_purchase_order_products as pop')
            ->select('pop.f_product_id', 'pop.price_per_qty', 'pop.qty as ordered_qty')
            ->where('pop.f_purchase_order_id', $data['purchase_order_id'])
            ->get()
            ->keyBy('f_product_id');

        foreach ($data['products'] as $product) {
            if (! isset($product['delivery_qty']) || $product['delivery_qty'] <= 0) {
                continue;
            }

            $productId = $product['product_id'];
            $deliveryQty = $product['delivery_qty'];

            // Get the purchase price from PO
            $poProduct = $poProducts->get($productId);
            if (! $poProduct) {
                continue;
            }

            $purchasePrice = (float) $poProduct->price_per_qty;

            // Get the updated weighted average HPP from product
            $productData = DB::table('m_products')
                ->where('id', $productId)
                ->first();
            $weightedAverageHPP = (float) ($productData->average_price ?? $purchasePrice);

            // Use BatchManagementService to create batch with weighted average HPP
            app(BatchManagementService::class)->createBatch([
                'f_product_id' => $productId,
                'source_type' => 'PURCHASE', // Use valid enum value
                'f_source_transaction_id' => $deliveryId,
                'quantity' => $deliveryQty,
                'purchase_price' => $purchasePrice,
                'estimated_hpp' => $weightedAverageHPP, // Use weighted average HPP
                'final_hpp' => $weightedAverageHPP, // Use weighted average HPP
                'batch_date' => $data['delivery_date'] ?? now()->format('Y-m-d'),
                'reference_code' => $data['reference_no'],
                'notes' => 'Purchase Delivery: '.$data['reference_no'],
            ]);
        }
    }
}
