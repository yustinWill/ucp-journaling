<?php

namespace App\Domains\PurchaseOrder\Actions;

use App\Domains\Product\Services\ProductService;
use App\Domains\PurchaseOrder\Services\PurchaseOrderAuditLogService;
use App\Domains\PurchaseOrder\Services\PurchaseOrderProductService;
use App\Domains\PurchaseOrder\Services\PurchaseOrderService;
use App\Domains\PurchaseDelivery\Actions\CreatePurchaseDeliveryAction;
use App\Domains\GlobalConfig\Services\GlobalConfigService;
use App\Enums\ActionTypes;
use Illuminate\Support\Facades\DB;

class CreatePurchaseOrderAction
{
    public function execute(array $data)
    {
        return DB::transaction(function () use ($data) {

            if (isset($data['attachment']) && $data['attachment'] instanceof \Illuminate\Http\UploadedFile) {
                $path = $data['attachment']->store('purchase_order', 'public');
                $data['attachment'] = $path;
            }

            $purchase_order_id = app(PurchaseOrderService::class)->create($data);

            foreach ($data['products'] as $product) {
                app(PurchaseOrderProductService::class)->create($purchase_order_id, [
                    'product_id' => $product['product_id'],
                    'qty' => $product['quantity'],
                    'unit_id' => $product['unit_id'],
                    'price_per_qty' => $product['unit_price'],
                    // 'tax_id' => $product['tax_id'],
                    'total' => $product['total'],
                ]);
            }

            $log_data = [
                'action_type' => ActionTypes::CREATE,
                'description' => 'Pembentukan Purchase Order',
                'metadata' => json_encode([
                    'before' => null,
                    'after' => $data,
                ]),
            ];
            app(PurchaseOrderAuditLogService::class)->create($log_data, $purchase_order_id);

            $config_data = app(GlobalConfigService::class)->findOne('AUTO_GENERATE_PD_AFTER_PO');

              if ($config_data && $config_data->value == 'true') {
                $pd_data = [
                    'purchase_order_id' => $purchase_order_id,
                    'date' => $data['date'],
                    'reference_no' => $data['reference_no'],
                    'delivery_number' => null,
                    'delivery_date' => $data['date'],
                    'tracking_number' => null,
                    'courier_name' => null,
                    'description' => null,
                    'memo' => $data['memo'] ?? null,
                    'tags' => isset($data['tags']) ? json_encode($data['tags']) : null,
                    'total_delivered' => 0,
                    'warehouse_id' => $data['warehouse_id'],
                    'delivery_status' => 'COMPLETED',
                    'products' => [],
                ];

                $delivered_qty = 0;

                $poProducts = app(PurchaseOrderProductService::class)->findAllBasedOnPOId($purchase_order_id);

                foreach ($poProducts as $product) {
                    $pd_data['products'][] = [
                        'product_order_product_id' => $product->id,
                        'product_id' => $product->f_product_id,
                        'po_qty' => $product->qty,
                        'delivered_qty_before' => $product->delivered_qty, // or use actual invoice qty if available
                        'remaining_qty_before' => $product->remaining_qty,
                        'delivery_qty' => $product->remaining_qty,
                        'note' => null,
                        'status' => 'COMPLETED'
                    ];
                    $delivered_qty += $product->remaining_qty;
                }

                $pd_data['total_delivered'] = $delivered_qty;
                app(CreatePurchaseDeliveryAction::class)->execute($pd_data);
            }

            return $purchase_order_id;
        });
    }
}
