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
}
