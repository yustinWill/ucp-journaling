<?php

namespace App\Domains\Tax\Actions;

use App\Domains\Tax\Services\TaxService;
use App\Domains\Tax\Services\TaxAuditLogService;
use Illuminate\Support\Facades\DB;

class CreateTaxAction
{
    public function __construct(
        private TaxService $taxService,
        private TaxAuditLogService $taxAuditLogService
    ) {}

    public function execute(array $data)
    {
        return DB::transaction(function () use ($data) {
            $taxId = $this->taxService->create($data);
            
            $this->taxAuditLogService->create(
                $taxId,
                'CREATE',
                "Pajak {$data['name']} berhasil dibuat dengan kode {$data['code']}"
            );

            return $taxId;
        });
    }
}