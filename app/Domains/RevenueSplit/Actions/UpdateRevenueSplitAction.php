<?php

namespace App\Domains\RevenueSplit\Actions;

use App\Domains\RevenueSplit\Services\RevenueSplitAuditLogService;
use App\Domains\RevenueSplit\Services\RevenueSplitService;
use App\Enums\ActionTypes;
use Illuminate\Support\Facades\DB;

class UpdateRevenueSplitAction
{
    public function execute(int $id, array $data)
    {
        $old_data = app(RevenueSplitService::class)->findOne($id);

        return DB::transaction(function () use ($id, $data, $old_data) {
            app(RevenueSplitService::class)->update($id, $data);
            $log_data = [
                'action_type' => ActionTypes::UPDATE,
                'description' => 'Perubahan Cycle Keuntungan Penjualan',
                'metadata' => json_encode([
                    'before' => $old_data,
                    'after' => $data,
                    'comparison' => [
                        'cycle_name' => [
                            'label' => 'Nama Cycle',
                            'before' => $old_data->cycle_name,
                            'after' => $data['cycle_name'],
                        ],
                        'description' => [
                            'label' => 'Deskripsi',
                            'before' => $old_data->description,
                            'after' => $data['description'] ?? '',
                        ],
                        'start_date' => [
                            'label' => 'Tanggal Mulai',
                            'before' => $old_data->start_date,
                            'after' => $data['start_date'],
                        ],
                        'end_date' => [
                            'label' => 'Tanggal Selesai',
                            'before' => $old_data->end_date,
                            'after' => $data['end_date'],
                        ],
                        'target_pabrik' => [
                            'label' => 'Target Keuntungan Pabrik',
                            'before' => 'Rp ' . number_format($old_data->target_pabrik, 0, ',', '.'),
                            'after' => 'Rp ' . number_format($data['target_pabrik'], 0, ',', '.'),
                        ],
                        'target_owner' => [
                            'label' => 'Target Keuntungan Owner',
                            'before' => 'Rp ' . number_format($old_data->target_owner, 0, ',', '.'),
                            'after' => 'Rp ' . number_format($data['target_owner'], 0, ',', '.'),
                        ],
                        'target_perusahaan' => [
                            'label' => 'Target Keuntungan Perusahaan',
                            'before' => 'Rp ' . number_format($old_data->target_perusahaan, 0, ',', '.'),
                            'after' => 'Rp ' . number_format($data['target_perusahaan'], 0, ',', '.'),
                        ],
                        'status' => [
                            'label' => 'Status',
                            'before' => $old_data->status,
                            'after' => $data['status'],
                        ],
                        'notes' => [
                            'label' => 'Catatan',
                            'before' => $old_data->notes,
                            'after' => $data['notes'] ?? '',
                        ],
                    ],
                ]),
            ];
            app(RevenueSplitAuditLogService::class)->create($log_data, $id);
        });
    }
}