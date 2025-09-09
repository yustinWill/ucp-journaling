<?php
namespace App\Domains\SalesDelivery\Actions;

use App\Domains\SalesDelivery\Services\SalesDeliveryService;
use App\Domains\SalesDelivery\Services\SalesDeliveryAuditLogService;
use Illuminate\Support\Facades\DB;
use App\Enums\ActionTypes;

class CreateSalesDeliveryAction
{
    public function execute(array $data)
    {
        return DB::transaction(function () use ($data) {
            $sales_delivery_id = app(SalesDeliveryService::class)->create($data);

            $log_data = [
                'action_type' => ActionTypes::CREATE,
                'description' => "Pembuatan Sales Delivery",
                'metadata' => json_encode([
                    'before' => null,
                    'after' => $data,
                ])
            ];
            app(SalesDeliveryAuditLogService::class)->create($log_data, $sales_delivery_id);

            return $sales_delivery_id;
        });
    }
}