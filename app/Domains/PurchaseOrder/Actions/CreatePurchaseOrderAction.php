<?php

namespace App\Domains\PurchaseOrder\Actions;

use App\Domains\Product\Services\ProductService;
use App\Domains\PurchaseOrder\Services\PurchaseOrderAuditLogService;
use App\Domains\PurchaseOrder\Services\PurchaseOrderProductService;
use App\Domains\PurchaseOrder\Services\PurchaseOrderService;
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
                $unit_data = app(ProductService::class)->findOne($product['product_id']);
                app(PurchaseOrderProductService::class)->create($purchase_order_id, [
                    'product_id' => $product['product_id'],
                    'qty' => $product['quantity'],
                    'unit_id' => $unit_data->f_unit_id,
                    'price_per_qty' => $product['unit_price'],
                    'tax_id' => $product['tax_id'],
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
        });
    }
}
