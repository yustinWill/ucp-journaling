<?php

namespace App\Domains\SalesDelivery\Actions;

use App\Domains\SalesDelivery\Services\SalesDeliveryAuditLogService;
use App\Domains\SalesDelivery\Services\SalesDeliveryService;
use App\Enums\ActionTypes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AcceptSalesDeliveryAction
{
    /**
     * Accept sales delivery (step before confirmation)
     *
     * @param int $salesDeliveryId
     * @return int
     * @throws \Exception
     */
    public function execute(int $salesDeliveryId)
    {
        return DB::transaction(function () use ($salesDeliveryId) {
            // Get sales delivery data
            $salesDelivery = app(SalesDeliveryService::class)->findOne($salesDeliveryId);

            if (!$salesDelivery) {
                throw new \Exception("Sales Delivery dengan ID {$salesDeliveryId} tidak ditemukan.");
            }

            // Check if already accepted
            if ($salesDelivery->is_accepted) {
                throw new \Exception("Sales Delivery ini sudah diterima sebelumnya.");
            }

            // Check if already confirmed (shouldn't accept after confirmation)
            if ($salesDelivery->is_confirmed === '1') {
                throw new \Exception("Sales Delivery ini sudah dikonfirmasi, tidak dapat diterima lagi.");
            }

            // Update sales delivery status to accepted
            DB::table('t_sales_deliveries')
                ->where('id', $salesDeliveryId)
                ->update([
                    'is_accepted' => true,
                    'accepted_at' => now(),
                    'accepted_by' => Auth::id(),
                    'updated_at' => now(),
                    'updated_by' => Auth::id(),
                ]);

            // Create audit log
            $log_data = [
                'action_type' => ActionTypes::UPDATE,
                'description' => 'Penerimaan Sales Delivery - Delivery Accepted',
                'metadata' => json_encode([
                    'before' => [
                        'is_accepted' => false,
                    ],
                    'after' => [
                        'is_accepted' => true,
                        'accepted_at' => now(),
                        'accepted_by' => Auth::id(),
                    ],
                ]),
            ];
            app(SalesDeliveryAuditLogService::class)->create($log_data, $salesDeliveryId);

            return $salesDeliveryId;
        });
    }
}
