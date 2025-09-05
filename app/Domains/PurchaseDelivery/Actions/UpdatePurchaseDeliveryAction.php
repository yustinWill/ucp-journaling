<?php

namespace App\Domains\PurchaseDelivery\Actions;

use App\Domains\PurchaseDelivery\Services\PurchaseDeliveryAuditLogService;
use App\Domains\PurchaseDelivery\Services\PurchaseDeliveryService;
use App\Enums\ActionTypes;
use Illuminate\Support\Facades\DB;

class UpdatePurchaseDeliveryAction
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

        return [
            'is_valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    public function execute(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            // Get current data for audit log
            $beforeData = app(PurchaseDeliveryService::class)->findOne($id);

            // Handle file upload
            if (isset($data['attachment']) && $data['attachment'] instanceof \Illuminate\Http\UploadedFile) {
                $path = $data['attachment']->store('purchase_delivery', 'public');
                $data['attachment'] = $path;
            }

            // Update purchase delivery record
            app(PurchaseDeliveryService::class)->update($id, $data);

            // Create audit log
            $logData = [
                'action_type' => ActionTypes::UPDATE,
                'description' => 'Pembaruan Purchase Delivery',
                'metadata' => json_encode([
                    'before' => $beforeData,
                    'after' => $data,
                ]),
            ];
            app(PurchaseDeliveryAuditLogService::class)->create($logData, $id);

            return true;
        });
    }
}
