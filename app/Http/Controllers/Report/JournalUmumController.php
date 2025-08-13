<?php

namespace App\Http\Controllers\Report;

//Main
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DateTime;

//Services
use App\Domains\JournalUmumReport\Services\JournalUmumReportService;
use App\Domains\Coa\Services\CoaService;

class JournalUmumController extends Controller
{
    public function index(Request $request)
    {
        $coas = app(CoaService::class)->findAll();

        $date_range = $request->input('date_range');
        $coa_code_filter = $request->input('coa_code_filter');
        $journal_data = [];
        $from = null;
        $to = null;
        if ($date_range != null) {
            [$from, $to] = array_map('trim', explode(' - ', $date_range));

            $journal_data = app(JournalUmumReportService::class)->getJournalData($from, $to, $coa_code_filter);
        }

        return view('pages.report.jurnal_umum.index', [
            'coas' => $coas,
            'date_range' => $date_range,
            'coa_code_filter' => $coa_code_filter,
            'journal_data' => $journal_data,
            'from' => $from,
            'to' => $to,
        ]);
    }
}
