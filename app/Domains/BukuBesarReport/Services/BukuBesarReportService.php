<?php

namespace App\Domains\BukuBesarReport\Services;

use App\Enums\GeneralStatuses;
use Illuminate\Support\Facades\DB;
use App\Traits\DateTime;

class BukuBesarReportService
{
    use DateTime;

    public function getJournalData($from, $to, $coa_list)
    {
        $company_code = session("user_company_code");
        $coa_codes = [];
        foreach ($coa_list as $coa_row) {
            $coa_codes[] = $coa_row->code;
        }

        $journal_data_query = DB::table("t_accounting_journals" . ' as journal')
            ->select([
                "journal.reference_no AS reference_no",
                "journal.date AS date",
                "journal.f_coa_code AS f_coa_code",
                "journal.description AS description",
                "journal.debit_credit AS debit_credit",
                "journal.amount AS amount",
                "journal.created_by AS created_by",
                "user.name AS created_by_name",
                "journal.created_at AS created_at"
            ])
            ->join("m_coas" . " as coas", function ($join) {
                $join->on('coas.code', '=', 'journal.f_coa_code')
                    ->on('coas.company_code', '=', 'journal.company_code');
            })
            ->join("users" . " as user", 'user.id', '=', 'journal.created_by')
            ->whereIn('journal.f_coa_code', $coa_codes)
            ->where('journal.company_code', '=', $company_code)
            ->where('journal.amount', '!=', 0)
            ->where('journal.status', '=', GeneralStatuses::ACTIVE)
            ->where('journal.date', '>=', $from)
            ->where('journal.date', '<=', $to)
            ->orderBy('journal.date', 'ASC')
            ->orderBy('journal.created_at', 'ASC');

        $journal_data_query->orderBy('journal.id', 'ASC');
        return $journal_data_query->get();
    }

    public function getClosingBalance($coa_code, $start_date)
    {
        $company_code = session("user_company_code");
        $coa_sum_log_query = DB::table("l_coa_summary" . ' as coa_sum')
            ->select([
                DB::raw('coa_sum.closing_balance AS closing_balance')
            ])
            ->where('coa_sum.f_coa_code', '=', $coa_code)
            ->where('coa_sum.company_code', '=', $company_code)
            ->where('coa_sum.date', '<=', $this->getLastMonthEndOfMonth($start_date))
            ->where('coa_sum.is_active', '=', true)
            ->orderBy('coa_sum.date', 'DESC');

        $data = $coa_sum_log_query->first();

        if ($data == null) {
            $journal_data_query = DB::table("t_accounting_journals" . ' as journal')
                ->select([
                    "journal.reference_no AS reference_no",
                    "journal.date AS date",
                    "journal.f_coa_code AS f_coa_code",
                    "journal.description AS description",
                    "journal.debit_credit AS debit_credit",
                    "journal.amount AS amount",
                    "journal.created_by AS created_by",
                    "user.name AS created_by_name",
                    "journal.created_at AS created_at",
                ])
                ->join("users" . " as user", 'user.id', '=', 'journal.created_by')
                ->where('journal.f_coa_code', $coa_code)
                ->where('journal.company_code', '=', $company_code)
                ->where('journal.amount', '!=', 0)
                ->where('journal.status', '=', GeneralStatuses::ACTIVE)
                ->where('journal.date', '<', $start_date)
                ->orderBy('journal.date', 'ASC')
                ->orderBy('journal.created_at', 'ASC');

            $journal_data_query->orderBy('journal.id', 'ASC');
            $jurnal_opening = $journal_data_query->get();

            $opening_balance = 0;
            foreach ($jurnal_opening as $journal_row) {
                if ($journal_row->debit_credit == "D") {
                    $opening_balance += $journal_row->amount;
                } else {
                    $opening_balance -= $journal_row->amount;
                }
            }

            $data = (object) [];
            $data->closing_balance = $opening_balance;
        }
        return $data;
    }
}
