<?php

namespace App\Domains\PurchaseDelivery\Actions;

use App\Domains\PurchaseDelivery\Services\PurchaseDeliveryAuditLogService;
use App\Domains\PurchaseDelivery\Services\PurchaseDeliveryService;
use App\Enums\ActionTypes;
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
        } else {
            $hasValidDelivery = false;
            foreach ($data['products'] as $index => $product) {
                if (isset($product['delivery_qty']) && $product['delivery_qty'] > 0) {
                    $hasValidDelivery = true;

                    // Validate quantities
                    $orderedQty = $product['ordered_qty'] ?? 0;
                    $deliveredQty = $product['delivered_qty'] ?? 0;
                    $deliveryQty = $product['delivery_qty'] ?? 0;
                    $remainingQty = $orderedQty - $deliveredQty;

                    if ($deliveryQty > $remainingQty) {
                        $errors[] = 'Produk #'.($index + 1).': Kuantitas delivery melebihi sisa yang belum dikirim';
                    }
                }
            }

            if (! $hasValidDelivery) {
                $errors[] = 'Minimal satu produk harus memiliki kuantitas delivery > 0';
            }
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
                
                // Create product batches for HPP tracking
                $this->createProductBatches($deliveryId, $data);
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
     * Create product stock batches for HPP tracking
     */
    private function createProductBatches(int $deliveryId, array $data)
    {
        if (!isset($data['purchase_order_id']) || !isset($data['products'])) {
            return;
        }

        // Get PO products with pricing information
        $poProducts = \DB::table('t_purchase_order_products as pop')
            ->select('pop.f_product_id', 'pop.price_per_qty', 'pop.qty as ordered_qty')
            ->where('pop.f_purchase_order_id', $data['purchase_order_id'])
            ->get()
            ->keyBy('f_product_id');

        $batchInserts = [];
        foreach ($data['products'] as $product) {
            if (!isset($product['delivery_qty']) || $product['delivery_qty'] <= 0) {
                continue;
            }

            $productId = $product['product_id'];
            $deliveryQty = $product['delivery_qty'];
            
            // Get the purchase price from PO
            $poProduct = $poProducts->get($productId);
            if (!$poProduct) {
                continue;
            }

            $purchasePrice = (float) $poProduct->price_per_qty;
            $batchReference = 'PD-' . $deliveryId . '-' . $productId . '-' . time();

            $batchInserts[] = [
                'company_code' => session('user_company_code', '001'),
                'f_product_id' => $productId,
                'batch_reference_code' => $batchReference,
                'source_type' => 'PURCHASE',
                'f_source_transaction_id' => $deliveryId,
                'batch_date' => $data['delivery_date'] ?? now()->format('Y-m-d'),
                'initial_qty' => $deliveryQty,
                'remaining_qty' => $deliveryQty,
                'raw_material_cost' => 0.00, // Not applicable for purchase
                'production_cost' => 0.00, // Not applicable for purchase
                'purchase_price' => $purchasePrice,
                'estimated_hpp' => $purchasePrice, // Initial HPP equals purchase price
                'final_hpp' => null, // To be settled later
                'hpp_status' => 'UNSETTLED',
                'status' => 'ACTIVE',
                'created_at' => now(),
                'created_by' => \Auth::id() ?? 1, // Fallback for testing
            ];
        }

        if (!empty($batchInserts)) {
            \DB::table('t_product_stock_batches')->insert($batchInserts);
        }
    }
}
