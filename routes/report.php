<?php

use Illuminate\Support\Facades\Route;

//Controllers
use App\Http\Controllers\Report\JournalUmumController;

$base_segment = '/report';
$jurnal_umum_report_segment = '/accounting_jurnal_umum';

Route::middleware(['auth'])->group(function () use ($base_segment, $jurnal_umum_report_segment) {
   
    Route::controller(JournalUmumController::class)->group(function () use ($base_segment, $jurnal_umum_report_segment) {
        Route::get($base_segment . $jurnal_umum_report_segment . '/index', 'index')->name('report.jurnal_umum.index');
    });
});
