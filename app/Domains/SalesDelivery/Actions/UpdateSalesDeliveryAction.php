<?php

namespace App\Domains\SalesDelivery\Actions;

use App\Domains\SalesDelivery\Services\SalesDeliveryAuditLogService;
use App\Domains\SalesDelivery\Services\SalesDeliveryService;
use App\Enums\ActionTypes;
use Illuminate\Support\Facades\DB;

class UpdateSalesDeliveryAction
{
    public function execute(int $id, array $data)
    {
        $old_data = app(SalesDeliveryService::class)->findOne($id);

        return DB::transaction(function () use ($id, $data, $old_data) {
            app(SalesDeliveryService::class)->update($id, $data);

            $log_data = [
                'action_type' => ActionTypes::UPDATE,
                'description' => 'Perubahan Sales Delivery',
                'metadata' => json_encode([
                    'before' => $old_data,
                    'after' => $data,
                ]),
            ];
            app(SalesDeliveryAuditLogService::class)->create($log_data, $id);
        });
    }
}
