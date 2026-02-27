<?php

namespace App\Domains\SalesDelivery\Actions;

use App\Domains\SalesDelivery\Services\SalesDeliveryAuditLogService;
use App\Domains\SalesDelivery\Services\SalesDeliveryService;
use App\Enums\ActionTypes;
use Illuminate\Support\Facades\DB;

class CreateSalesDeliveryAction
{
    public function execute(array $data)
    {
        // Validate that delivery quantities don't exceed remaining quantities
        $this->validateDeliveryQuantities($data['products']);

        // Handle file upload
        if (isset($data['attachment']) && $data['attachment'] instanceof \Illuminate\Http\UploadedFile) {
            $path = $data['attachment']->store('sales_delivery', 'public');
            $data['attachment'] = $path;
        }
        return DB::transaction(function () use ($data) {
            $sales_delivery_id = app(SalesDeliveryService::class)->create($data);

            app(SalesDeliveryService::class)->createDeliveryProducts($sales_delivery_id, $data['products']);

            // NOTE: Stock movement logic has been moved to ConfirmSalesDeliveryAction
            // Stock will be updated when the delivery is confirmed via "Konfirm" button

            $log_data = [
                'action_type' => ActionTypes::CREATE,
                'description' => 'Pembuatan Sales Delivery',
                'metadata' => json_encode([
                    'before' => null,
                    'after' => $data,
                ]),
            ];
            app(SalesDeliveryAuditLogService::class)->create($log_data, $sales_delivery_id);

            return $sales_delivery_id;
        });
    }

    /**
     * Validate that delivery quantities do not exceed remaining quantities
     */
    private function validateDeliveryQuantities(array $products): void
    {
        $errors = [];

        foreach ($products as $product) {
            $deliveryQty = floatval($product['delivery_qty'] ?? 0);
            $remainingQty = floatval($product['remaining_qty_before'] ?? 0);

            if ($deliveryQty > $remainingQty) {
                // Get product name for better error message
                $productName = $this->getProductName($product['product_id']);

                $errors[] = sprintf(
                    'Produk "%s": Qty pengiriman (%.2f) melebihi qty tersisa (%.2f)',
                    $productName,
                    $deliveryQty,
                    $remainingQty
                );
            }

            if ($deliveryQty <= 0) {
                $productName = $this->getProductName($product['product_id']);
                $errors[] = sprintf(
                    'Produk "%s": Qty pengiriman harus lebih dari 0',
                    $productName
                );
            }
        }

        if (!empty($errors)) {
            throw new \Exception('Validasi qty pengiriman gagal: ' . implode('; ', $errors));
        }
    }

    /**
     * Get product name by ID for error messages
     */
    private function getProductName(int $productId): string
    {
        $product = DB::table('m_products')
            ->where('id', $productId)
            ->first();

        return $product ? $product->name : "ID: {$productId}";
    }
}
