<?php

namespace App\Domains\InventoryAsset\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryAssetJournalService
{
    private $table_name = 't_accounting_journals';

    public function create($data)
    {
        $payload = [];
        foreach ($data as $journal) {
            // Ensure all fields are properly formatted and cast
            $payload[] = [
                'reference_no' => $journal->code,
                'company_code' => $journal->company_code,
                'date' => now()->toDateString(), // Ensure date is in 'Y-m-d' format
                'f_accounting_feature_code' => 'INDEP',
                'f_coa_code' => $journal->f_depreciation_expense_coa_code,
                'description' => "Jurnal Depresiasi: " . $journal->name . " Periode " . $journal->depreciation_month_year,
                'amount' => (double) $journal->depreciation_value, // Cast amount to double
                'debit_credit' => 'D',
                'status' => 'ACTIVE',
                'is_locked' => 0,
                'created_by' => Auth::id(),
                'created_at' => now(), // Use Carbon's now() for timestamps
            ];

            $payload[] = [
                'reference_no' => $journal->code,
                'company_code' => $journal->company_code,
                'date' => now()->toDateString(), // Ensure date is in 'Y-m-d' format
                'f_accounting_feature_code' => 'INDEP',
                'f_coa_code' => $journal->f_accumulated_depreciation_coa_code,
                'description' => "Jurnal Depresiasi: " . $journal->name . " Periode " . $journal->depreciation_month_year,
                'amount' => (double) $journal->depreciation_value, // Cast amount to double
                'debit_credit' => 'C',
                'status' => 'ACTIVE',
                'is_locked' => 0,
                'created_by' => Auth::id(),
                'created_at' => now(), // Use Carbon's now() for timestamps
            ];
        }
        return DB::table($this->table_name)->insert($payload);
    }
}
