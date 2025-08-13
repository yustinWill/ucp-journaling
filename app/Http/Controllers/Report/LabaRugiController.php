<?php

namespace App\Http\Controllers\Report;

//Main
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\AccountingReportingLogic;
use DateTime;

//Services
use App\Domains\Coa\Services\CoaService;

class LabaRugiController extends Controller
{
    use AccountingReportingLogic;

    public function index(Request $request)
    {
        $coas = app(CoaService::class)->findAll();

        $date_range = $request->input('date_range');
        $coa_code_filter = $request->input('coa_code_filter');
        $journal_data = [];
        $from = null;
        $to = null;
        $html = "";
        $revenue_amount = 0;
        $expense_amount = 0;

        if ($date_range != null) {
            [$from, $to] = array_map('trim', explode('-', $date_range));
            $from = DateTime::createFromFormat('m/d/Y', $from)->format('Y-m-d');
            $to = DateTime::createFromFormat('m/d/Y', $to)->format('Y-m-d');

            $report_data = $this->get_data_logic($from, $to);
            $tree = $this->buildTree($report_data);
            $this->printTree($html, $revenue_amount, $expense_amount, $tree);
        }

        return view('pages.report.laba_rugi.index', [
            'coas' => $coas,
            'date_range' => $date_range,
            'coa_code_filter' => $coa_code_filter,
            'journal_data' => $journal_data,
            'from' => $from,
            'to' => $to,
            'html' => $html,
            'revenue_amount' => abs($revenue_amount),
            'expense_amount' => abs($expense_amount),
            'variance_amount' => abs($revenue_amount) - abs($expense_amount),
        ]);
    }

    public function get_data_logic($from, $to)
    {
        $report_data = $this->get_coa_list($from, ['4', '5', '6', '7', '8', '9']);

        foreach ($report_data as $row) {
            $row->amount = 0;
        }

        $curr_month_report_data = $this->get_coa_sum_data($from, $to, $report_data);

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
        }

        $temp_data = $report_data;
        for ($i = 0; $i < count($temp_data); $i++) {
            if ($temp_data[$i]['amount'] == 0 && $temp_data[$i]['coa_type'] == 'D') {
                unset($report_data[$i]);
            }
        }
        return array_values($report_data);
    }
}
