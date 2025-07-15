<?php

use App\Http\Middleware\EnsureTokenIsValid;
use Illuminate\Support\Facades\Route;

//Controllers
use App\Http\Controllers\MainTransaction\JournalController;
use App\Http\Controllers\MainTransaction\PurchaseController;
use App\Http\Controllers\MainTransaction\SalesController;
use App\Http\Controllers\MainTransaction\CreditMemoController;
use App\Http\Controllers\MainTransaction\ExpenseController;

$base_segment = '/main_transaction';
$journal_segment = '/jurnal';
$purchase_segment = '/pembelian';
$sales_segment = '/penjualan';
$credit_memo_segment = '/credit_memo';
$expense_segment = '/pengeluaran';

Route::middleware([EnsureTokenIsValid::class])->group(function () use ($base_segment, $journal_segment, $purchase_segment, $sales_segment, $credit_memo_segment, $expense_segment) {
    Route::controller(JournalController::class)->group(function () use ($base_segment, $journal_segment) {
        Route::get($base_segment . $journal_segment . '/index', 'index')->name('main_transaction.jurnal.index')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $journal_segment . '/index_data_table', 'index_data_table')->name('main_transaction.jurnal.index_data_table')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $journal_segment . '/add', 'add')->name('main_transaction.jurnal.add')->middleware(EnsureTokenIsValid::class);
    });

    Route::controller(PurchaseController::class)->group(function () use ($base_segment, $purchase_segment) {
        Route::get($base_segment . $purchase_segment . '/index', 'index')->name('main_transaction.purchase.index')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $purchase_segment . '/index_data_table', 'index_data_table')->name('main_transaction.purchase.index_data_table')->middleware(EnsureTokenIsValid::class);
        
        // Purchase Order routes
        Route::get($base_segment . $purchase_segment . '/add_po', 'purchaseOrderAdd')->name('main_transaction.purchase.add.po')->middleware(EnsureTokenIsValid::class);
        
        // Purchase Delivery routes
        Route::get($base_segment . $purchase_segment . '/add_pd', 'purchaseDeliveryAdd')->name('main_transaction.purchase.add.pd')->middleware(EnsureTokenIsValid::class);
        
        // Purchase Invoice routes
        Route::get($base_segment . $purchase_segment . '/add_pi', 'purchaseInvoiceAdd')->name('main_transaction.purchase.add.pi')->middleware(EnsureTokenIsValid::class);
        
        // Import/Export routes
        Route::post($base_segment . $purchase_segment . '/import', 'handleImport')->name('main_transaction.purchase.import')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $purchase_segment . '/export', 'handleExport')->name('main_transaction.purchase.export')->middleware(EnsureTokenIsValid::class);
    });

    Route::controller(SalesController::class)->group(function () use ($base_segment, $sales_segment) {
        Route::get($base_segment . $sales_segment . '/index', 'index')->name('main_transaction.sales.index')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $sales_segment . '/index_data_table', 'index_data_table')->name('main_transaction.sales.index_data_table')->middleware(EnsureTokenIsValid::class);
        
        // Sales Order routes
        Route::get($base_segment . $sales_segment . '/add_so', 'salesOrderAdd')->name('main_transaction.sales.add.so')->middleware(EnsureTokenIsValid::class);
        
        // Sales Delivery routes
        Route::get($base_segment . $sales_segment . '/add_sd', 'salesDeliveryAdd')->name('main_transaction.sales.add.sd')->middleware(EnsureTokenIsValid::class);
        
        // Sales Invoice routes
        Route::get($base_segment . $sales_segment . '/add_si', 'salesInvoiceAdd')->name('main_transaction.sales.add.si')->middleware(EnsureTokenIsValid::class);
        
        // Import/Export routes
        Route::post($base_segment . $sales_segment . '/import', 'handleImport')->name('main_transaction.sales.import')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $sales_segment . '/export', 'handleExport')->name('main_transaction.sales.export')->middleware(EnsureTokenIsValid::class);
    });

    Route::controller(CreditMemoController::class)->group(function () use ($base_segment, $credit_memo_segment) {
        Route::get($base_segment . $credit_memo_segment . '/index', 'index')->name('main_transaction.credit_memo.index')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $credit_memo_segment . '/index_data_table', 'index_data_table')->name('main_transaction.credit_memo.index_data_table')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $credit_memo_segment . '/add', 'add')->name('main_transaction.credit_memo.add')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $credit_memo_segment . '/add/{contact_id?}', 'addWithContact')->name('main_transaction.credit_memo.add_with_contact')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $credit_memo_segment . '/export', 'export')->name('main_transaction.credit_memo.export')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $credit_memo_segment . '/import', 'import')->name('main_transaction.credit_memo.import')->middleware(EnsureTokenIsValid::class);
    });

    Route::controller(ExpenseController::class)->group(function () use ($base_segment, $expense_segment) {
        Route::get($base_segment . $expense_segment . '/index', 'index')->name('main_transaction.expense.index')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $expense_segment . '/index_data_table', 'index_data_table')->name('main_transaction.expense.index_data_table')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $expense_segment . '/add', 'add')->name('main_transaction.expense.add')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $expense_segment . '/edit/{id}', 'edit')->name('main_transaction.expense.edit')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $expense_segment . '/detail/{id}', 'detail')->name('main_transaction.expense.detail')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $expense_segment . '/categories', 'categories')->name('main_transaction.expense.categories')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $expense_segment . '/statuses', 'statuses')->name('main_transaction.expense.statuses')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $expense_segment . '/recipients', 'recipients')->name('main_transaction.expense.recipients')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $expense_segment . '/accounts', 'accounts')->name('main_transaction.expense.accounts')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $expense_segment . '/payment_methods', 'paymentMethods')->name('main_transaction.expense.payment_methods')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $expense_segment . '/payment_terms', 'paymentTerms')->name('main_transaction.expense.payment_terms')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $expense_segment . '/export', 'export')->name('main_transaction.expense.export')->middleware(EnsureTokenIsValid::class);
        Route::post($base_segment . $expense_segment . '/import', 'import')->name('main_transaction.expense.import')->middleware(EnsureTokenIsValid::class);
    });
});
