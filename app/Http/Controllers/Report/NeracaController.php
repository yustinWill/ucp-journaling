<?php

namespace App\Http\Controllers\Report;

// Main
use App\Domains\Coa\Services\CoaService;
use App\Http\Controllers\Controller;
use App\Traits\AccountingReportingLogic;
// Services
use Illuminate\Http\Request;

class NeracaController extends Controller
{
    use AccountingReportingLogic;

    public function index(Request $request)
    {
        $coas = app(CoaService::class)->findAll();

        $as_of_date = $request->input('as_of_date');
        $coa_code_filter = $request->input('coa_code_filter');
        $journal_data = [];
        $from = null;
        $to = null;
        $html = '';
        $asset_amount = 0;
        $liability_amount = 0;
        $equity_amount = 0;
        $revenue_amount = 0;
        $expense_amount = 0;
        $html = '';

        if ($as_of_date != null) {
            $report_data = $this->get_data_logic($as_of_date);
            $tree = $this->buildTree($report_data['report_data']);
            $this->printTree($html, $asset_amount, $liability_amount, $equity_amount, $revenue_amount, $expense_amount, $tree);
            $equity_amount = $equity_amount * -1;
            $equity_amount += $report_data['laba_rugi_berjalan'];
            $liability_amount = $liability_amount * -1;
        } else {
            $as_of_date = now()->format('Y-m-d');
        }

        return view('pages.report.neraca.index', [
            'coas' => $coas,
            'as_of_date' => $as_of_date,
            'coa_code_filter' => $coa_code_filter,
            'journal_data' => $journal_data,
            'from' => $from,
            'to' => $to,
            'html' => $html,
            'asset_amount' => $asset_amount,
            'liability_amount' => $liability_amount,
            'equity_amount' => $equity_amount,
            'variance_amount' => $asset_amount - ($liability_amount + $equity_amount),
        ]);
    }

    private function get_laba_rugi_berjalan(string $last_date, string $as_of_date)
    {
        $laba_rugi_report = $this->get_coa_list($as_of_date, ['4', '5', '6', '7', '8', '9']);

        $curr_month_laba_rugi_report_data = $this->get_coa_sum_data($last_date, $as_of_date, $laba_rugi_report);

        $laba_rugi_report = json_decode(json_encode($laba_rugi_report), true);
        $curr_month_laba_rugi_report_data = json_decode(json_encode($curr_month_laba_rugi_report_data), true);

        foreach ($laba_rugi_report as &$report_item_lr) {
            foreach ($curr_month_laba_rugi_report_data as $curr_month_item_lr) {
                if ($curr_month_item_lr['coa_code'] === $report_item_lr['coa_code']) {
                    if (substr($curr_month_item_lr['coa_code'], 0, 1) >= 2 && substr($curr_month_item_lr['coa_code'], 0, 1) <= 4) {
                        $report_item_lr['amount'] -= $curr_month_item_lr['amount'];
                    } else {
                        $report_item_lr['amount'] += $curr_month_item_lr['amount'];
                    }
                }
            }
        }

        $pendapatan_amount = 0;
        $beban_amount = 0;
        foreach ($laba_rugi_report as $lr_row) {
            if (substr($lr_row['coa_code'], 0, 1) == 4) {
                $pendapatan_amount += $lr_row['amount'];
            } elseif (substr($lr_row['coa_code'], 0, 1) > 4) {
                $beban_amount += $lr_row['amount'];
            }
        }

        return abs($pendapatan_amount) - abs($beban_amount);
    }

    private function get_data_logic(string $as_of_date)
    {

        $last_date = $this->get_date($as_of_date, session('user_company_code'));

        $laba_rugi_berjalan = $this->get_laba_rugi_berjalan($last_date, $as_of_date);

        $report_data = $this->get_coa_list($as_of_date, ['1', '2', '3']);

        $curr_month_report_data = $this->get_coa_sum_data($last_date, $as_of_date, $report_data);

        $curr_month_report_data = json_decode(json_encode($curr_month_report_data), true);

        $report_data = json_decode(json_encode($report_data), true);
        foreach ($report_data as &$report_item) {
            foreach ($curr_month_report_data as $curr_month_item) {
                if ($curr_month_item['coa_code'] === $report_item['coa_code']) {
                    if (substr($curr_month_item['coa_code'], 0, 1) >= 2 && substr($curr_month_item['coa_code'], 0, 1) <= 4) {
                        $report_item['amount'] -= $curr_month_item['amount'];
                    } else {
                        $report_item['amount'] += $curr_month_item['amount'];
                    }
                }
            }

            if ($report_item['coa_code'] === session('coa_laba_rugi_berjalan')) {
                $report_item['amount'] = $laba_rugi_berjalan * -1;
            }
        }
        $temp_data = $report_data;
        for ($i = 0; $i < count($temp_data); $i++) {
            if ($temp_data[$i]['amount'] == 0 && $temp_data[$i]['coa_type'] == 'D') {
                unset($report_data[$i]);
            }
        }

        return [
            'report_data' => $report_data,
            'laba_rugi_berjalan' => $laba_rugi_berjalan,
        ];
    }
}
