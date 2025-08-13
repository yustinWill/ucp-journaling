<?php

namespace App\Http\Controllers\Report;

//Main
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ArrayObjectManipulator;
use DateTime;

//Services
use App\Domains\BukuBesarReport\Services\BukuBesarReportService;
use App\Domains\Coa\Services\CoaService;

class BukuBesarController extends Controller
{
    use ArrayObjectManipulator;

    public function index(Request $request)
    {
        $coas = app(CoaService::class)->findAll();

        $date_range = $request->input('date_range');
        $coa_code_filter = $request->input('coa_code_filter');
        $buku_besar_data = [];
        $from = null;
        $to = null;
        if ($date_range != null) {
            [$from, $to] = array_map('trim', explode(' - ', $date_range));
            if ($coa_code_filter) {
                $coa_list = $coas->whereIn('code', $coa_code_filter);
            } else {
                $coa_list = $coas;
            }

            $journal_data = app(BukuBesarReportService::class)->getJournalData($from, $to, $coa_list);

            $journal_data = $this->array_group_by('f_coa_code', $journal_data->toArray());

            foreach ($journal_data as $key => $row) {
                $coa_data = $coa_list->firstWhere('code', $key);
                $get_closing_balance = app(BukuBesarReportService::class)->getClosingBalance($key, $from);

                $valid_date_row = [];

                $opening_balance_amount = $get_closing_balance->closing_balance;

                foreach ($row as $journal_row) {
                    if ($journal_row->date >= $from) {
                        $valid_date_row[] = $journal_row;
                    } else if ($journal_row->date < $from) {
                        if ($journal_row->debit_credit == "D") {
                            $opening_balance_amount += $journal_row->amount;
                        } else {
                            $opening_balance_amount -= $journal_row->amount;
                        }
                    }
                }

                $buku_besar_data[] = [
                    'coa_code' => $coa_data->code,
                    'coa_name' => $coa_data->name,
                    'opening_balance_amount' => $opening_balance_amount,
                    'journal_data' => $valid_date_row
                ];

                $closing_balance_amount = $get_closing_balance->closing_balance;
                foreach ($row as $jurnal_row) {
                    if ($jurnal_row->debit_credit == "D") {
                        $closing_balance_amount += $jurnal_row->amount;
                    } else {
                        $closing_balance_amount -= $jurnal_row->amount;
                    }
                    $jurnal_row->after_amount = $closing_balance_amount;
                }
            }
        }

        return view('pages.report.buku_besar.index', [
            'coas' => $coas,
            'date_range' => $date_range,
            'coa_code_filter' => $coa_code_filter,
            'buku_besar_data' => $buku_besar_data,
            'from' => $from,
            'to' => $to,
        ]);
    }
}
