<?php

use App\Http\Controllers\Report\BukuBesarController;
// Controllers
use App\Http\Controllers\Report\JournalUmumController;
use App\Http\Controllers\Report\LabaRugiController;
use App\Http\Controllers\Report\NeracaController;
use Illuminate\Support\Facades\Route;

$base_segment = '/report';
$jurnal_umum_report_segment = '/accounting_jurnal_umum';
$buku_besar_report_segment = '/accounting_buku_besar';
$laba_rugi_report_segment = '/accounting_laba_rugi';
$neraca_report_segment = '/accounting_neraca';

Route::middleware(['auth'])->group(function () use ($base_segment, $jurnal_umum_report_segment, $buku_besar_report_segment, $laba_rugi_report_segment, $neraca_report_segment) {

    Route::controller(JournalUmumController::class)->group(function () use ($base_segment, $jurnal_umum_report_segment) {
        Route::get($base_segment.$jurnal_umum_report_segment.'/index', 'index')->name('report.jurnal_umum.index');
    });

    Route::controller(BukuBesarController::class)->group(function () use ($base_segment, $buku_besar_report_segment) {
        Route::get($base_segment.$buku_besar_report_segment.'/index', 'index')->name('report.buku_besar.index');
    });

    Route::controller(LabaRugiController::class)->group(function () use ($base_segment, $laba_rugi_report_segment) {
        Route::get($base_segment.$laba_rugi_report_segment.'/index', 'index')->name('report.laba_rugi.index');
    });

    Route::controller(NeracaController::class)->group(function () use ($base_segment, $neraca_report_segment) {
        Route::get($base_segment.$neraca_report_segment.'/index', 'index')->name('report.neraca.index');
    });
});
