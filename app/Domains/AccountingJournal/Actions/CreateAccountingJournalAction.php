<?php

namespace App\Domains\AccountingJournal\Actions;

use Illuminate\Support\Facades\DB;
use App\Enums\JournalFeatureCodes;
use App\Domains\AccountingJournal\Services\AccountingJournalService;
use App\Domains\AccountingJournal\Services\AccountingJournalAuditLogService;
use App\Enums\GeneralStatuses;
use App\Enums\ActionTypes;

class CreateAccountingJournalAction
{
    public function execute(array $data)
    {
        return DB::transaction(function () use ($data) {
            $journal_arr = [];
            foreach ($data['journals'] as $row) {
                $debit_credit = null;
                $amount = 0;
                if ($row['debit_amount'] != 0) {
                    $amount = $row['debit_amount'];
                    $debit_credit = 'D';
                }
                else{
                    $amount = $row['credit_amount'];
                    $debit_credit = 'C';
                }
                $journal_arr[] = [
                    "transaction_code" => null,
                    "reference_no" => $data['reference_no'],
                    "date" => $data['date'],
                    "feature_code" => JournalFeatureCodes::JLL,
                    "coa_code" => $row['account_code'],
                    "description" => $data['description'],
                    "internal_memo" => $data['memo'],
                    "coa_description" => $row['description'],
                    "amount" => $amount,
                    "debit_credit" => $debit_credit,
                    "f_tag_codes" => $data['tags'],
                    "status" => GeneralStatuses::ACTIVE,
                ];
            }
            $journal_ids = app(AccountingJournalService::class)->create($journal_arr);

            $log_data = [
                'action_type' => ActionTypes::CREATE,
                'description' => "Pembentukan Jurnal",
                'metadata' => json_encode([
                    'before' => null,
                    'after' => $data,
                ])
            ];
            app(AccountingJournalAuditLogService::class)->create($log_data, $journal_ids[0]);
        });
    }
}
