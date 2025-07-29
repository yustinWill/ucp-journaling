<?php

namespace App\Domains\SalesPerson\Actions;

use App\Domains\SalesPerson\Services\SalesPersonService;
use App\Domains\SalesPerson\Services\SalesPersonAuditLogService;
use Illuminate\Support\Facades\DB;
use App\Enums\ActionTypes;

class UpdateSalesPersonAction
{
    public function execute(int $id, array $data)
    {
        $old_data = app(SalesPersonService::class)->findOne($id);

        return DB::transaction(function () use ($id, $data, $old_data) {
            app(SalesPersonService::class)->update($id, $data);
            $log_data = [
                'action_type' => ActionTypes::UPDATE,
                'description' => "Perubahan Sales Person",
                'metadata' => json_encode([
                    'before' => $old_data,
                    'after' => $data,
                    'comparison' => [
                        'code' => [
                            'label' => 'Kode Sales Person',
                            'before' => $old_data->code,
                            'after' => $data['f_sales_person_code'],
                        ],
                        'name' => [
                            'label' => 'Nama Karyawan',
                            'before' => $old_data->f_employee_contact_id,
                            'after' => $data['f_employee_contact_id'],
                        ],
                        'type' => [
                            'label' => 'Status',
                            'before' => $old_data->status,
                            'after' => $data['status'],
                        ],
                        'status' => [
                            'label' => 'Komisi',
                            'before' => $old_data->commission_rate,
                            'after' => $data['commission_rate'],
                        ],
                        'description' => [
                            'label' => 'Keterangan',
                            'before' => $old_data->description,
                            'after' => $data['description'],
                        ],
                    ]
                ])
            ];
            app(SalesPersonAuditLogService::class)->create($log_data, $id);

            return true;
        });
    }
}
