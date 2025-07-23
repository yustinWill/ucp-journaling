<?php

namespace App\Domains\Tax\Actions;

use App\Domains\Tax\Services\TaxService;
use App\Domains\Tax\Services\TaxAuditLogService;
use Illuminate\Support\Facades\DB;

class UpdateTaxAction
{
    public function __construct(
        private TaxService $taxService,
        private TaxAuditLogService $taxAuditLogService
    ) {}

    public function execute(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $this->taxService->update($id, $data);
            
            $this->taxAuditLogService->create(
                $id,
                'UPDATE',
                "Pajak {$data['name']} berhasil diperbarui"
            );

            return true;
        });
    }
}