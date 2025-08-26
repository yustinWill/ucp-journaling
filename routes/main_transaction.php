<?php

use Illuminate\Support\Facades\Route;

//Controllers
use App\Http\Controllers\MainTransaction\JournalController;
use App\Http\Controllers\MainTransaction\PurchaseController;
use App\Http\Controllers\MainTransaction\PurchaseOrderController;
use App\Http\Controllers\MainTransaction\PurchaseDeliveryController;
use App\Http\Controllers\MainTransaction\PurchaseInvoiceController;
use App\Http\Controllers\MainTransaction\SalesController;
use App\Http\Controllers\MainTransaction\CreditMemoController;
use App\Http\Controllers\MainTransaction\ExpenseController;
use App\Http\Controllers\MainTransaction\StockAdjustmentController;
use App\Http\Controllers\MainTransaction\ProductionController;

$base_segment = '/main_transaction';
$journal_segment = '/journal';
$purchase_segment = '/purchase';
$purchase_order_segment = '/purchase_order';
$purchase_delivery_segment = '/purchase_delivery';
$purchase_invoice_segment = '/purchase_invoice';
$sales_segment = '/sales';
$credit_memo_segment = '/credit_memo';
$expense_segment = '/expense';
$stock_adjustment_segment = '/stock_adjustment';
$production_segment = '/production';

Route::middleware(['auth'])->group(function () use ($base_segment, $journal_segment, $purchase_segment, $purchase_order_segment, $purchase_delivery_segment, $purchase_invoice_segment, $sales_segment, $credit_memo_segment, $expense_segment, $stock_adjustment_segment, $production_segment) {
    Route::controller(JournalController::class)->group(function () use ($base_segment, $journal_segment) {
        Route::get($base_segment . $journal_segment . '/index', 'index')->name('main_transaction.journal.index');
        Route::get($base_segment . $journal_segment . '/index_data_table', 'index_data_table')->name('main_transaction.journal.index_data_table');
        Route::get($base_segment . $journal_segment . '/add', 'add')->name('main_transaction.journal.add');
        Route::post($base_segment . $journal_segment . '/save', 'save')->name('main_transaction.journal.save');
        Route::get($base_segment . $journal_segment . '/import', 'import')->name('main_transaction.journal.import');
        Route::get($base_segment . $journal_segment . '/export', 'export')->name('main_transaction.journal.export');
        Route::get($base_segment . $journal_segment . '/detail/{id}', 'detail')->name('main_transaction.journal.detail');
        Route::get($base_segment . $journal_segment . '/edit/{id}', 'edit')->name('main_transaction.journal.edit');
        Route::post($base_segment . $journal_segment . '/update', 'update')->name('main_transaction.journal.update');
    });

    Route::controller(PurchaseController::class)->group(function () use ($base_segment, $purchase_segment) {
        Route::get($base_segment . $purchase_segment . '/index', 'index')->name('main_transaction.purchase.index');
        Route::get($base_segment . $purchase_segment . '/index_data_table', 'index_data_table')->name('main_transaction.purchase.index_data_table');

        // Purchase Order routes
        Route::get($base_segment . $purchase_segment . '/add_po', 'purchaseOrderAdd')->name('main_transaction.purchase.add.po');

        // Purchase Delivery routes
        Route::get($base_segment . $purchase_segment . '/add_pd', 'purchaseDeliveryAdd')->name('main_transaction.purchase.add.pd');

        // Purchase Invoice routes
        Route::get($base_segment . $purchase_segment . '/add_pi', 'purchaseInvoiceAdd')->name('main_transaction.purchase.add.pi');

        // Detail and Edit routes
        Route::get($base_segment . $purchase_segment . '/detail/{id}', 'detail')->name('main_transaction.purchase.detail');
        Route::get($base_segment . $purchase_segment . '/edit/{id}', 'edit')->name('main_transaction.purchase.edit');
        Route::post($base_segment . $purchase_segment . '/update', 'update')->name('main_transaction.purchase.update');

        // Import/Export routes
        Route::get($base_segment . $purchase_segment . '/import', 'import')->name('main_transaction.purchase.import');
        Route::get($base_segment . $purchase_segment . '/download_template', 'downloadTemplate')->name('main_transaction.purchase.download_template');
        Route::post($base_segment . $purchase_segment . '/validate_import', 'validateImport')->name('main_transaction.purchase.validate_import');
        Route::post($base_segment . $purchase_segment . '/process_import', 'processImport')->name('main_transaction.purchase.process_import');
        Route::get($base_segment . $purchase_segment . '/export', 'export')->name('main_transaction.purchase.export');
        Route::post($base_segment . $purchase_segment . '/process_export', 'processExport')->name('main_transaction.purchase.process_export');
    });

    // Purchase Order Routes
    Route::controller(PurchaseOrderController::class)->group(function () use ($base_segment, $purchase_order_segment) {
        Route::get($base_segment . $purchase_order_segment . '/index', 'index')->name('main_transaction.purchase_order.index');
        Route::get($base_segment . $purchase_order_segment . '/index_data_table', 'index_data_table')->name('main_transaction.purchase_order.index_data_table');
        Route::get($base_segment . $purchase_order_segment . '/generate_transaction_number', 'generateTransactionNumber')->name('main_transaction.purchase_order.generate_transaction_number');
        Route::get($base_segment . $purchase_order_segment . '/add', 'add')->name('main_transaction.purchase_order.add');
        Route::post($base_segment . $purchase_order_segment . '/store', 'store')->name('main_transaction.purchase_order.store');
        Route::get($base_segment . $purchase_order_segment . '/detail/{id}', 'detail')->name('main_transaction.purchase_order.detail');
        Route::get($base_segment . $purchase_order_segment . '/audit_log_data_table/{id}', 'auditLogDataTable')->name('main_transaction.purchase_order.audit_log_data_table');
        Route::get($base_segment . $purchase_order_segment . '/edit/{id}', 'edit')->name('main_transaction.purchase_order.edit');
        Route::post($base_segment . $purchase_order_segment . '/update', 'update')->name('main_transaction.purchase_order.update');
        Route::get($base_segment . $purchase_order_segment . '/import', 'import')->name('main_transaction.purchase_order.import');
        Route::get($base_segment . $purchase_order_segment . '/download_template', 'downloadTemplate')->name('main_transaction.purchase_order.download_template');
        Route::post($base_segment . $purchase_order_segment . '/validate_import', 'validateImport')->name('main_transaction.purchase_order.validate_import');
        Route::post($base_segment . $purchase_order_segment . '/process_import', 'processImport')->name('main_transaction.purchase_order.process_import');
        Route::get($base_segment . $purchase_order_segment . '/export', 'export')->name('main_transaction.purchase_order.export');
        Route::post($base_segment . $purchase_order_segment . '/process_export', 'processExport')->name('main_transaction.purchase_order.process_export');
    });

    // Purchase Delivery Routes
    Route::controller(PurchaseDeliveryController::class)->group(function () use ($base_segment, $purchase_delivery_segment) {
        Route::get($base_segment . $purchase_delivery_segment . '/index', 'index')->name('main_transaction.purchase_delivery.index');
        Route::get($base_segment . $purchase_delivery_segment . '/index_data_table', 'index_data_table')->name('main_transaction.purchase_delivery.index_data_table');
        Route::get($base_segment . $purchase_delivery_segment . '/add', 'add')->name('main_transaction.purchase_delivery.add');
        Route::post($base_segment . $purchase_delivery_segment . '/store', 'store')->name('main_transaction.purchase_delivery.store');
        Route::get($base_segment . $purchase_delivery_segment . '/detail/{id}', 'detail')->name('main_transaction.purchase_delivery.detail');
        Route::get($base_segment . $purchase_delivery_segment . '/audit_log_data_table/{id}', 'auditLogDataTable')->name('main_transaction.purchase_delivery.audit_log_data_table');
        Route::get($base_segment . $purchase_delivery_segment . '/edit/{id}', 'edit')->name('main_transaction.purchase_delivery.edit');
        Route::post($base_segment . $purchase_delivery_segment . '/update', 'update')->name('main_transaction.purchase_delivery.update');
        Route::get($base_segment . $purchase_delivery_segment . '/import', 'import')->name('main_transaction.purchase_delivery.import');
        Route::get($base_segment . $purchase_delivery_segment . '/download_template', 'downloadTemplate')->name('main_transaction.purchase_delivery.download_template');
        Route::post($base_segment . $purchase_delivery_segment . '/validate_import', 'validateImport')->name('main_transaction.purchase_delivery.validate_import');
        Route::post($base_segment . $purchase_delivery_segment . '/process_import', 'processImport')->name('main_transaction.purchase_delivery.process_import');
        Route::get($base_segment . $purchase_delivery_segment . '/export', 'export')->name('main_transaction.purchase_delivery.export');
        Route::post($base_segment . $purchase_delivery_segment . '/process_export', 'processExport')->name('main_transaction.purchase_delivery.process_export');
        Route::get($base_segment . $purchase_delivery_segment . '/print_pdf/{po_id}', 'printPDF')->name('main_transaction.purchase_delivery.print_pdf');
        Route::get($base_segment . $purchase_delivery_segment . '/get_po_products', 'getPOProducts')->name('main_transaction.purchase_delivery.get_po_products');
        Route::get($base_segment . $purchase_delivery_segment . '/get_previous_deliveries', 'getPreviousDeliveries')->name('main_transaction.purchase_delivery.get_previous_deliveries');
        Route::get($base_segment . $purchase_delivery_segment . '/get_products', 'getProducts')->name('main_transaction.purchase_delivery.get_products');
    });

    // Purchase Invoice Routes
    Route::controller(PurchaseInvoiceController::class)->group(function () use ($base_segment, $purchase_invoice_segment) {
        Route::get($base_segment . $purchase_invoice_segment . '/index', 'index')->name('main_transaction.purchase_invoice.index');
        Route::get($base_segment . $purchase_invoice_segment . '/index_data_table', 'index_data_table')->name('main_transaction.purchase_invoice.index_data_table');
        Route::get($base_segment . $purchase_invoice_segment . '/add', 'add')->name('main_transaction.purchase_invoice.add');
        Route::post($base_segment . $purchase_invoice_segment . '/store', 'store')->name('main_transaction.purchase_invoice.store');
        Route::get($base_segment . $purchase_invoice_segment . '/detail/{id}', 'detail')->name('main_transaction.purchase_invoice.detail');
        Route::get($base_segment . $purchase_invoice_segment . '/audit_log_data_table/{id}', 'auditLogDataTable')->name('main_transaction.purchase_invoice.audit_log_data_table');
        Route::get($base_segment . $purchase_invoice_segment . '/edit/{id}', 'edit')->name('main_transaction.purchase_invoice.edit');
        Route::post($base_segment . $purchase_invoice_segment . '/update', 'update')->name('main_transaction.purchase_invoice.update');
        Route::get($base_segment . $purchase_invoice_segment . '/import', 'import')->name('main_transaction.purchase_invoice.import');
        Route::get($base_segment . $purchase_invoice_segment . '/download_template', 'downloadTemplate')->name('main_transaction.purchase_invoice.download_template');
        Route::post($base_segment . $purchase_invoice_segment . '/validate_import', 'validateImport')->name('main_transaction.purchase_invoice.validate_import');
        Route::post($base_segment . $purchase_invoice_segment . '/process_import', 'processImport')->name('main_transaction.purchase_invoice.process_import');
        Route::get($base_segment . $purchase_invoice_segment . '/export', 'export')->name('main_transaction.purchase_invoice.export');
        Route::post($base_segment . $purchase_invoice_segment . '/process_export', 'processExport')->name('main_transaction.purchase_invoice.process_export');
    });

    Route::controller(SalesController::class)->group(function () use ($base_segment, $sales_segment) {
        Route::get($base_segment . $sales_segment . '/index', 'index')->name('main_transaction.sales.index');
        Route::get($base_segment . $sales_segment . '/index_data_table', 'index_data_table')->name('main_transaction.sales.index_data_table');

        // Sales Order routes
        Route::get($base_segment . $sales_segment . '/add_so', 'salesOrderAdd')->name('main_transaction.sales.add.so');

        // Sales Delivery routes
        Route::get($base_segment . $sales_segment . '/add_sd', 'salesDeliveryAdd')->name('main_transaction.sales.add.sd');

        // Sales Invoice routes
        Route::get($base_segment . $sales_segment . '/add_si', 'salesInvoiceAdd')->name('main_transaction.sales.add.si');

        // Detail and Edit routes
        Route::get($base_segment . $sales_segment . '/detail/{id}', 'detail')->name('main_transaction.sales.detail');
        Route::get($base_segment . $sales_segment . '/edit/{id}', 'edit')->name('main_transaction.sales.edit');

        // Import/Export routes
        Route::get($base_segment . $sales_segment . '/import', 'import')->name('main_transaction.sales.import');
        Route::get($base_segment . $sales_segment . '/download_template', 'downloadTemplate')->name('main_transaction.sales.download_template');
        Route::post($base_segment . $sales_segment . '/validate_import', 'validateImport')->name('main_transaction.sales.validate_import');
        Route::post($base_segment . $sales_segment . '/process_import', 'processImport')->name('main_transaction.sales.process_import');
        Route::get($base_segment . $sales_segment . '/export', 'export')->name('main_transaction.sales.export');
        Route::post($base_segment . $sales_segment . '/process_export', 'processExport')->name('main_transaction.sales.process_export');
    });

    Route::controller(CreditMemoController::class)->group(function () use ($base_segment, $credit_memo_segment) {
        Route::get($base_segment . $credit_memo_segment . '/index', 'index')->name('main_transaction.credit_memo.index');
        Route::get($base_segment . $credit_memo_segment . '/index_data_table', 'index_data_table')->name('main_transaction.credit_memo.index_data_table');
        Route::get($base_segment . $credit_memo_segment . '/add', 'add')->name('main_transaction.credit_memo.add');
        Route::get($base_segment . $credit_memo_segment . '/add/{contact_id?}', 'addWithContact')->name('main_transaction.credit_memo.add_with_contact');
        Route::get($base_segment . $credit_memo_segment . '/export', 'export')->name('main_transaction.credit_memo.export');
        Route::get($base_segment . $credit_memo_segment . '/import', 'import')->name('main_transaction.credit_memo.import');
    });

    Route::controller(ExpenseController::class)->group(function () use ($base_segment, $expense_segment) {
        Route::get($base_segment . $expense_segment . '/index', 'index')->name('main_transaction.expense.index');
        Route::get($base_segment . $expense_segment . '/index_data_table', 'index_data_table')->name('main_transaction.expense.index_data_table');
        Route::get($base_segment . $expense_segment . '/add', 'add')->name('main_transaction.expense.add');
        Route::get($base_segment . $expense_segment . '/edit/{id}', 'edit')->name('main_transaction.expense.edit');
        Route::get($base_segment . $expense_segment . '/detail/{id}', 'detail')->name('main_transaction.expense.detail');
        Route::get($base_segment . $expense_segment . '/categories', 'categories')->name('main_transaction.expense.categories');
        Route::get($base_segment . $expense_segment . '/statuses', 'statuses')->name('main_transaction.expense.statuses');
        Route::get($base_segment . $expense_segment . '/recipients', 'recipients')->name('main_transaction.expense.recipients');
        Route::get($base_segment . $expense_segment . '/accounts', 'accounts')->name('main_transaction.expense.accounts');
        Route::get($base_segment . $expense_segment . '/payment_methods', 'paymentMethods')->name('main_transaction.expense.payment_methods');
        Route::get($base_segment . $expense_segment . '/payment_terms', 'paymentTerms')->name('main_transaction.expense.payment_terms');
        Route::get($base_segment . $expense_segment . '/taxes', 'taxes')->name('main_transaction.expense.taxes');
        Route::get($base_segment . $expense_segment . '/tags', 'tags')->name('main_transaction.expense.tags');
        Route::get($base_segment . $expense_segment . '/export', 'export')->name('main_transaction.expense.export');
        Route::post($base_segment . $expense_segment . '/import', 'import')->name('main_transaction.expense.import');
    });

    Route::controller(StockAdjustmentController::class)->group(function () use ($base_segment, $stock_adjustment_segment) {
        Route::get($base_segment . $stock_adjustment_segment . '/index', 'index')->name('main_transaction.stock_adjustment.index');
        Route::get($base_segment . $stock_adjustment_segment . '/index_data_table', 'index_data_table')->name('main_transaction.stock_adjustment.index_data_table');
        Route::get($base_segment . $stock_adjustment_segment . '/add', 'add')->name('main_transaction.stock_adjustment.add');
        Route::post($base_segment . $stock_adjustment_segment . '/save', 'save')->name('main_transaction.stock_adjustment.save');
        Route::get($base_segment . $stock_adjustment_segment . '/edit/{id}', 'edit')->name('main_transaction.stock_adjustment.edit');
        Route::post($base_segment . $stock_adjustment_segment . '/update', 'update')->name('main_transaction.stock_adjustment.update');
        Route::get($base_segment . $stock_adjustment_segment . '/detail/{id}', 'detail')->name('main_transaction.stock_adjustment.detail');
        Route::get($base_segment . $stock_adjustment_segment . '/get_warehouse_products', 'getWarehouseProducts')->name('main_transaction.stock_adjustment.get_warehouse_products');
        Route::get($base_segment . $stock_adjustment_segment . '/generate_transaction_number', 'generateTransactionNumber')->name('main_transaction.stock_adjustment.generate_transaction_number');
        Route::get($base_segment . $stock_adjustment_segment . '/export', 'export')->name('main_transaction.stock_adjustment.export');
        Route::post($base_segment . $stock_adjustment_segment . '/process_export', 'processExport')->name('main_transaction.stock_adjustment.process_export');
        Route::get($base_segment . $stock_adjustment_segment . '/import', 'import')->name('main_transaction.stock_adjustment.import');
        Route::get($base_segment . $stock_adjustment_segment . '/download_template', 'downloadTemplate')->name('main_transaction.stock_adjustment.download_template');
        Route::post($base_segment . $stock_adjustment_segment . '/validate_import', 'validateImport')->name('main_transaction.stock_adjustment.validate_import');
        Route::post($base_segment . $stock_adjustment_segment . '/process_import', 'processImport')->name('main_transaction.stock_adjustment.process_import');
    });

    // Production Routes
    Route::controller(ProductionController::class)->group(function () use ($base_segment, $production_segment) {
        Route::get($base_segment . $production_segment . '/index', 'index')->name('main_transaction.production.index');
        Route::get($base_segment . $production_segment . '/index_data_table', 'index_data_table')->name('main_transaction.production.index_data_table');
        Route::get($base_segment . $production_segment . '/add', 'add')->name('main_transaction.production.add');
        Route::post($base_segment . $production_segment . '/store', 'store')->name('main_transaction.production.store');
        Route::get($base_segment . $production_segment . '/detail/{id}', 'detail')->name('main_transaction.production.detail');
        // Edit/Update removed - use Reversal process instead
        Route::delete($base_segment . $production_segment . '/{id}', 'destroy')->name('main_transaction.production.destroy');
        Route::get($base_segment . $production_segment . '/export', 'export')->name('main_transaction.production.export');
        Route::post($base_segment . $production_segment . '/process_export', 'processExport')->name('main_transaction.production.process_export');
        
        // AJAX Routes for dynamic functionality
        Route::get($base_segment . $production_segment . '/get_bundle_components', 'getBundleComponents')->name('main_transaction.production.get_bundle_components');
        Route::get($base_segment . $production_segment . '/get_warehouses', 'getWarehouses')->name('main_transaction.production.get_warehouses');
        Route::get($base_segment . $production_segment . '/get_bundle_products', 'getBundleProducts')->name('main_transaction.production.get_bundle_products');
        Route::get($base_segment . $production_segment . '/get_pic_users', 'getPicUsers')->name('main_transaction.production.get_pic_users');
        Route::get($base_segment . $production_segment . '/generate_transaction_number', 'generateTransactionNumber')->name('main_transaction.production.generate_transaction_number');
        
        // AJAX endpoints for form data
        Route::get($base_segment . $production_segment . '/get-formula/{bundleProductId}', 'getFormula')->name('main_transaction.production.get_formula');
        Route::get($base_segment . $production_segment . '/get-expenses/{bundleProductId}', 'getExpenses')->name('main_transaction.production.get_expenses');

        // Reversal Routes
        Route::get($base_segment . $production_segment . '/reversal/{id}', 'reversal')->name('main_transaction.production.reversal');
        Route::post($base_segment . $production_segment . '/reversal/{id}', 'processReversal')->name('main_transaction.production.process_reversal');
        
        // Action Routes for production management
        Route::post($base_segment . $production_segment . '/complete/{id}', 'complete')->name('main_transaction.production.complete');
        Route::post($base_segment . $production_segment . '/cancel/{id}', 'cancel')->name('main_transaction.production.cancel');
    });
});
