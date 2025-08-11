<?php

namespace App\Domains\AccountingJournal\Actions;

use Illuminate\Support\Facades\DB;
use App\Enums\JournalFeatureCodes;
use App\Domains\AccountingJournal\Services\AccountingJournalService;
use App\Domains\AccountingJournal\Services\AccountingJournalAuditLogService;
use App\Enums\GeneralStatuses;
use App\Enums\ActionTypes;
use App\Exceptions\AppException;
use App\Enums\ErrorCodes;

class UpdateAccountingJournalAction
{
    public function execute(string $reference_no, array $data)
    {
        $old_data = null;
        $current_data = app(AccountingJournalService::class)->findOne($reference_no);

        $old_data['header'] = $current_data[0];

        if ($old_data['header']->is_locked == true) {
            throw new AppException(ErrorCodes::JOURNAL_IS_LOCKED);
        }
        $old_data['journals'] = $current_data;
        return DB::transaction(function () use ($data, $reference_no, $old_data) {

            app(AccountingJournalService::class)->updateSetInactive($reference_no);

            $create_arr = [];
            foreach ($data['journals'] as $row) {
                $debit_credit = null;
                $amount = 0;
                if ($row['debit_amount'] != 0) {
                    $amount = $row['debit_amount'];
                    $debit_credit = 'D';
                } else {
                    $amount = $row['credit_amount'];
                    $debit_credit = 'C';
                }
                $create_arr[] = [
                    "transaction_code" => null,
                    "reference_no" => $reference_no,
                    "date" => $data['date'],
                    "feature_code" => JournalFeatureCodes::JLL,
                    "coa_code" => $row['account_code'],
                    "description" => $data['description'],
                    "internal_memo" => $data['memo'],
                    "coa_description" => $row['description'],
                    "amount" => $amount,
                    "debit_credit" => $debit_credit,
                    "f_tag_codes" => isset($data['tags']) ? $data['tags'] : null,
                    "status" => GeneralStatuses::ACTIVE,
                ];
            }

            $journal_ids = app(AccountingJournalService::class)->create($create_arr);

            $log_data = [
                'action_type' => ActionTypes::UPDATE,
                'description' => "Perubahan Journal",
                'metadata' => json_encode([
                    'before' => $old_data,
                    'after' => $data,
                    'comparison' => [
                        'status' => [
                            'label' => 'Status',
                            'before' => $old_data['header']->status,
                            'after' => $data['status'],
                        ],
                        'date' => [
                            'label' => 'Tanggal Jurnal',
                            'before' => $old_data['header']->date,
                            'after' => $data['date'],
                        ],
                        'description' => [
                            'label' => 'Deskripsi',
                            'before' => $old_data['header']->description,
                            'after' => $data['description'],
                        ],
                        'memo' => [
                            'label' => 'Memo',
                            'before' => $old_data['header']->internal_memo,
                            'after' => $data['memo'],
                        ],
                        'tags' => [
                            'label' => 'Tag',
                            'before' => $old_data['header']->f_tag_codes,
                            'after' => $data['tags'] ?? null,
                        ],
                    ]
                ])
            ];

            $log_data = [
                'action_type' => ActionTypes::UPDATE,
                'description' => "Perubahan Jurnal",
                'metadata' => json_encode([
                    'before' => $old_data,
                    'after' => $data,
                ])
            ];
            app(AccountingJournalAuditLogService::class)->create($log_data, $journal_ids[0]);
        });
    }
}
