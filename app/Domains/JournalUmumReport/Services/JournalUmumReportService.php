<?php

namespace App\Domains\JournalUmumReport\Services;

use App\Enums\GeneralStatuses;
use Illuminate\Support\Facades\DB;

class JournalUmumReportService
{
    public function getJournalData($from, $to, $coa_code_filter)
    {
        $company_code = session('user_company_code');
        $journal_data_query = DB::table('t_accounting_journals'.' as journal')
            ->select([
                'journal.reference_no AS reference_no',
                'journal.date AS date',
                'journal.f_coa_code AS coa_code',
                'coa.name AS coa_name',
                'journal.description AS description',
                'journal.debit_credit AS debit_credit',
                'journal.amount AS amount',
                'journal.created_by AS created_by',
                'user.name AS created_by_name',
            ])
            ->join('m_coas'.' as coa', 'coa.code', '=', 'journal.f_coa_code')
            ->join('users'.' as user', 'user.id', '=', 'journal.created_by')
            ->where('journal.company_code', '=', $company_code)
            ->where('coa.company_code', '=', $company_code)
            ->where('journal.status', '=', GeneralStatuses::ACTIVE)
            ->where('journal.date', '>=', $from)
            ->where('journal.date', '<=', $to)
            ->orderBy('journal.date', 'ASC')
            ->orderBy('journal.reference_no', 'ASC');

        if ($coa_code_filter) {
            $journal_data_query->where('journal.f_coa_code', '=', $coa_code_filter);
        }

        return $journal_data_query->get();
    }
}
