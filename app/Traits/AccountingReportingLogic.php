<?php

namespace App\Traits;

use App\Enums\GeneralStatuses;
use App\Enums\GlobalConfig;
use Illuminate\Support\Facades\DB;

trait AccountingReportingLogic
{
    use DateTime;

    private function get_date($date, $company_code)
    {
        $custom_closing_date = DB::table('m_global_configs'.' as config')
            ->select([
                'value',
            ])
            ->where('code', '=', GlobalConfig::CUSTOM_CLOSING_ACCOUNTING_DATE)
            ->first();

        if ($custom_closing_date->value != '') {
            return $custom_closing_date->value;
        }

        $last_date = $this->getLastMonthEndOfMonth($date);

        $closing_data = DB::table('l_accounting_closing'.' as closing')
            ->select([
                'closing.date_period AS date_period',
            ])
            ->where('closing.status', '=', GeneralStatuses::ACTIVE)
            ->where('closing.date_period', '=', $last_date)
            // ->where('closing.company_code', '=', $company_code)
            ->first();

        if ($closing_data == null) {
            if (isset($date)) {
                $closing_data = DB::table('l_accounting_closing'.' as closing')
                    ->select([
                        'closing.date_period AS date_period',
                    ])
                    ->where('closing.status', '=', GeneralStatuses::ACTIVE)
                    ->where('closing.date_period', '<=', $date)
                    // ->where('closing.company_code', '=', Session::get('company_code'))
                    ->orderBy('closing.date_period', 'DESC')
                    ->first();
            } else {
                $closing_data = DB::table('l_accounting_closing'.' as closing')
                    ->select([
                        'closing.date_period AS date_period',
                    ])
                    ->where('closing.status', '=', GeneralStatuses::ACTIVE)
                    // ->where('closing.company_code', '=', Session::get('company_code'))
                    ->orderBy('closing.date_period', 'DESC')
                    ->first();
            }

            if (! isset($closing_data->date_period)) {
                return $date;
            }
            $last_date = $closing_data->date_period;
        }

        return $last_date;
    }

    public function get_coa_list(string $start_date, array $coa_group_list)
    {
        $company_code = session('user_company_code');
        $last_date = $this->get_date($start_date, $company_code);

        $coa_query = DB::table('m_coas'.' as coa')
            ->select([
                'coa.code AS coa_code',
                'coa.name AS coa_name',
                'coa.type AS coa_type',
                'coa.group_type AS coa_group_type',
                'coa.f_parent_coa_code AS parent_coa_code',
                DB::raw('COALESCE(coa_summary.closing_balance, 0) AS amount'),
                DB::raw("'0' AS adjustment_amount"),
                DB::raw('COALESCE(coa_summary.closing_balance, 0) AS final_amount'),
            ])
            ->leftJoin('l_coa_summary'.' as coa_summary', function ($join) use ($last_date) {
                $join->on('coa_summary.f_coa_code', '=', 'coa.code')
                    ->on('coa_summary.company_code', '=', 'coa.company_code')
                    ->where('coa_summary.date', '=', $last_date)
                    ->where('coa_summary.is_active', '=', true);
            })
            ->where('coa.company_code', '=', $company_code);

        if ($coa_group_list != null) {
            $coa_query->whereIn('coa.group_type', $coa_group_list);
        }
        $coa_list = $coa_query->get();

        return $coa_list;
    }

    public function buildTree(array &$elements, $parentId = null)
    {
        $branch = [];

        foreach ($elements as &$element) {
            if ($element['parent_coa_code'] === $parentId) {
                $children = $this->buildTree($elements, $element['coa_code']);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
                unset($element);
            }
        }

        return $branch;
    }

    public function printTree(&$html, &$asset_amount, &$liability_amount, &$equity_amount, &$revenue_amount, &$expense_amount, array $tree, $indent = 0)
    {
        foreach ($tree as $node) {
            $separator = str_repeat('&emsp;', $indent);
            if ($node['coa_type'] == 'D') {
                if ($node['coa_group_type'] == 1) {
                    $asset_amount += $node['amount'];
                } elseif ($node['coa_group_type'] == 2) {
                    $liability_amount += $node['amount'];
                } elseif ($node['coa_group_type'] == 3) {
                    if ($node['coa_code'] !== session(GlobalConfig::LABA_RUGI_BERJALAN_COA)) {
                        $equity_amount += $node['amount'];
                    }
                } elseif ($node['coa_group_type'] == 4) {
                    $revenue_amount += $node['amount'];
                } elseif ($node['coa_group_type'] >= 5) {
                    $expense_amount += $node['amount'];
                }

                if ($node['coa_group_type'] == 2 || $node['coa_group_type'] == 3) {
                    $node['amount'] = $node['amount'] * -1;
                }
                $html .= '<tr><td><b>'.$separator.$node['coa_code'].' - '.$node['coa_name'].'</b></td><td dir="rtl">Rp. '.number_format($node['amount'], 2, '.', ',').'</td></tr>';
            } else {
                $html .= '<tr><td><b>'.$separator.$node['coa_code'].' - '.$node['coa_name'].'</b></td><td dir="rtl">-</td></tr>';
            }

            if (isset($node['children'])) {
                $this->printTree($html, $asset_amount, $liability_amount, $equity_amount, $revenue_amount, $expense_amount, $node['children'], $indent + 1);
            }
        }
    }

    public function get_coa_sum_data(string $from, string $to, $report_data)
    {
        $company_code = session('user_company_code');
        $coa_codes = [];
        foreach ($report_data as $coa_row) {
            $coa_codes[] = $coa_row->coa_code;
        }

        $journal_data_query = DB::table('t_accounting_journals'.' as journal')
            ->select([
                'journal.f_coa_code AS coa_code',
            ])
            ->selectRaw('SUM(CASE 
            WHEN SUBSTRING(f_coa_code, 1, 1) = "1" AND debit_credit = "D" THEN amount 
            WHEN SUBSTRING(f_coa_code, 1, 1) = "1" AND debit_credit = "C" THEN (-1)*amount 

            WHEN SUBSTRING(f_coa_code, 1, 1) = "2" AND debit_credit = "C" THEN amount 
            WHEN SUBSTRING(f_coa_code, 1, 1) = "2" AND debit_credit = "D" THEN (-1)*amount 

            WHEN SUBSTRING(f_coa_code, 1, 1) = "3" AND debit_credit = "C" THEN amount 
            WHEN SUBSTRING(f_coa_code, 1, 1) = "3" AND debit_credit = "D" THEN (-1)*amount 

            WHEN SUBSTRING(f_coa_code, 1, 1) = "4" AND debit_credit = "C" THEN amount 
            WHEN SUBSTRING(f_coa_code, 1, 1) = "4" AND debit_credit = "D" THEN (-1)*amount 

            WHEN SUBSTRING(f_coa_code, 1, 1) = "5" AND debit_credit = "D" THEN amount 
            WHEN SUBSTRING(f_coa_code, 1, 1) = "5" AND debit_credit = "C" THEN (-1)*amount 

            WHEN SUBSTRING(f_coa_code, 1, 1) = "6" AND debit_credit = "D" THEN amount 
            WHEN SUBSTRING(f_coa_code, 1, 1) = "6" AND debit_credit = "C" THEN (-1)*amount 

            WHEN SUBSTRING(f_coa_code, 1, 1) = "7" AND debit_credit = "D" THEN amount 
            WHEN SUBSTRING(f_coa_code, 1, 1) = "7" AND debit_credit = "C" THEN (-1)*amount 

            WHEN SUBSTRING(f_coa_code, 1, 1) = "8" AND debit_credit = "D" THEN amount 
            WHEN SUBSTRING(f_coa_code, 1, 1) = "8" AND debit_credit = "C" THEN (-1)*amount

            WHEN SUBSTRING(f_coa_code, 1, 1) = "9" AND debit_credit = "D" THEN amount 
            WHEN SUBSTRING(f_coa_code, 1, 1) = "9" AND debit_credit = "C" THEN (-1)*amount
                
            ELSE 0 
            END) as amount')
            ->whereIn('journal.f_coa_code', $coa_codes)
            ->where('journal.status', '=', GeneralStatuses::ACTIVE)
            ->where('journal.date', '>=', $from)
            ->where('journal.date', '<=', $to)
            ->where('journal.company_code', '=', $company_code)
            ->groupBy('journal.f_coa_code');

        return $journal_data_query->get();
    }
}
