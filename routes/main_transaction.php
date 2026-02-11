<?php

use App\Http\Controllers\MainTransaction\CreditMemoController;
// Controllers
use App\Http\Controllers\MainTransaction\ExpenseController;
use App\Http\Controllers\MainTransaction\JournalController;
use App\Http\Controllers\MainTransaction\OverheadAbsorptionController;
use App\Http\Controllers\MainTransaction\PackingController;
use App\Http\Controllers\MainTransaction\ProductionController;
use App\Http\Controllers\MainTransaction\PurchaseController;
use App\Http\Controllers\MainTransaction\PurchaseDeliveryController;
use App\Http\Controllers\MainTransaction\PurchaseInvoiceController;
use App\Http\Controllers\MainTransaction\PurchaseOrderController;
use App\Http\Controllers\MainTransaction\ReturnManagementController;
use App\Http\Controllers\MainTransaction\SalesDeliveryController;
use App\Http\Controllers\MainTransaction\SalesInvoiceController;
use App\Http\Controllers\MainTransaction\SalesOrderController;
use App\Http\Controllers\MainTransaction\SalesOrderRevenueSplitController;
use App\Http\Controllers\MainTransaction\StockAdjustmentController;
use App\Http\Controllers\MainTransaction\TopUpController;
use Illuminate\Support\Facades\Route;

$base_segment = '/main_transaction';
$journal_segment = '/journal';
$purchase_segment = '/purchase';
$purchase_order_segment = '/purchase_order';
$purchase_delivery_segment = '/purchase_delivery';
$purchase_invoice_segment = '/purchase_invoice';
$sales_order_segment = '/sales_order';
$sales_delivery_segment = '/sales_delivery';
$sales_invoice_segment = '/sales_invoice';
$credit_memo_segment = '/credit_memo';
$expense_segment = '/expense';
$stock_adjustment_segment = '/stock_adjustment';
$production_segment = '/production';
$packing_segment = '/packing';
$return_management_segment = '/return_management';
$topup_segment = '/topup';

Route::middleware(['auth'])->group(function () use ($base_segment, $journal_segment, $purchase_segment, $purchase_order_segment, $purchase_delivery_segment, $purchase_invoice_segment, $sales_order_segment, $sales_delivery_segment, $sales_invoice_segment, $credit_memo_segment, $expense_segment, $stock_adjustment_segment, $production_segment, $packing_segment, $return_management_segment, $topup_segment) {
    Route::controller(JournalController::class)->group(function () use ($base_segment, $journal_segment) {
        Route::get($base_segment.$journal_segment.'/index', 'index')->name('main_transaction.journal.index');
        Route::get($base_segment.$journal_segment.'/index_data_table', 'index_data_table')->name('main_transaction.journal.index_data_table');
        Route::get($base_segment.$journal_segment.'/add', 'add')->name('main_transaction.journal.add');
        Route::post($base_segment.$journal_segment.'/save', 'save')->name('main_transaction.journal.save');
        Route::get($base_segment.$journal_segment.'/import', 'import')->name('main_transaction.journal.import');
        Route::get($base_segment.$journal_segment.'/export', 'export')->name('main_transaction.journal.export');
        Route::get($base_segment.$journal_segment.'/detail/{id}', 'detail')->name('main_transaction.journal.detail');
        Route::get($base_segment.$journal_segment.'/edit/{id}', 'edit')->name('main_transaction.journal.edit');
        Route::post($base_segment.$journal_segment.'/update', 'update')->name('main_transaction.journal.update');
    });

    Route::controller(PurchaseController::class)->group(function () use ($base_segment, $purchase_segment) {
        Route::get($base_segment.$purchase_segment.'/index', 'index')->name('main_transaction.purchase.index');
        Route::get($base_segment.$purchase_segment.'/index_data_table', 'index_data_table')->name('main_transaction.purchase.index_data_table');

        // Purchase Order routes
        Route::get($base_segment.$purchase_segment.'/add_po', 'purchaseOrderAdd')->name('main_transaction.purchase.add.po');

        // Purchase Delivery routes
        Route::get($base_segment.$purchase_segment.'/add_pd', 'purchaseDeliveryAdd')->name('main_transaction.purchase.add.pd');

        // Purchase Invoice routes
        Route::get($base_segment.$purchase_segment.'/add_pi', 'purchaseInvoiceAdd')->name('main_transaction.purchase.add.pi');

        // Detail and Edit routes
        Route::get($base_segment.$purchase_segment.'/detail/{id}', 'detail')->name('main_transaction.purchase.detail');
        Route::get($base_segment.$purchase_segment.'/edit/{id}', 'edit')->name('main_transaction.purchase.edit');
        Route::post($base_segment.$purchase_segment.'/update', 'update')->name('main_transaction.purchase.update');

        // Import/Export routes
        Route::get($base_segment.$purchase_segment.'/import', 'import')->name('main_transaction.purchase.import');
        Route::get($base_segment.$purchase_segment.'/download_template', 'downloadTemplate')->name('main_transaction.purchase.download_template');
        Route::post($base_segment.$purchase_segment.'/validate_import', 'validateImport')->name('main_transaction.purchase.validate_import');
        Route::post($base_segment.$purchase_segment.'/process_import', 'processImport')->name('main_transaction.purchase.process_import');
        Route::get($base_segment.$purchase_segment.'/export', 'export')->name('main_transaction.purchase.export');
        Route::post($base_segment.$purchase_segment.'/process_export', 'processExport')->name('main_transaction.purchase.process_export');
    });

    // Purchase Order Routes
    Route::controller(PurchaseOrderController::class)->group(function () use ($base_segment, $purchase_order_segment) {
        Route::get($base_segment.$purchase_order_segment.'/index', 'index')->name('main_transaction.purchase_order.index');
        Route::get($base_segment.$purchase_order_segment.'/index_data_table', 'index_data_table')->name('main_transaction.purchase_order.index_data_table');
        Route::get($base_segment.$purchase_order_segment.'/generate_transaction_number', 'generateTransactionNumber')->name('main_transaction.purchase_order.generate_transaction_number');
        Route::get($base_segment.$purchase_order_segment.'/add', 'add')->name('main_transaction.purchase_order.add');
        Route::post($base_segment.$purchase_order_segment.'/store', 'store')->name('main_transaction.purchase_order.store');
        Route::get($base_segment.$purchase_order_segment.'/detail/{id}', 'detail')->name('main_transaction.purchase_order.detail');
        Route::get($base_segment.$purchase_order_segment.'/audit_log_data_table/{id}', 'auditLogDataTable')->name('main_transaction.purchase_order.audit_log_data_table');
        Route::get($base_segment.$purchase_order_segment.'/edit/{id}', 'edit')->name('main_transaction.purchase_order.edit');
        Route::post($base_segment.$purchase_order_segment.'/update', 'update')->name('main_transaction.purchase_order.update');
        Route::get($base_segment.$purchase_order_segment.'/import', 'import')->name('main_transaction.purchase_order.import');
        Route::get($base_segment.$purchase_order_segment.'/download_template', 'downloadTemplate')->name('main_transaction.purchase_order.download_template');
        Route::post($base_segment.$purchase_order_segment.'/validate_import', 'validateImport')->name('main_transaction.purchase_order.validate_import');
        Route::post($base_segment.$purchase_order_segment.'/process_import', 'processImport')->name('main_transaction.purchase_order.process_import');
        Route::get($base_segment.$purchase_order_segment.'/export', 'export')->name('main_transaction.purchase_order.export');
        Route::post($base_segment.$purchase_order_segment.'/process_export', 'processExport')->name('main_transaction.purchase_order.process_export');
    });

    // Purchase Delivery Routes
    Route::controller(PurchaseDeliveryController::class)->group(function () use ($base_segment, $purchase_delivery_segment) {
        Route::get($base_segment.$purchase_delivery_segment.'/index', 'index')->name('main_transaction.purchase_delivery.index');
        Route::get($base_segment.$purchase_delivery_segment.'/index_data_table', 'index_data_table')->name('main_transaction.purchase_delivery.index_data_table');
        Route::get($base_segment.$purchase_delivery_segment.'/add', 'add')->name('main_transaction.purchase_delivery.add');
        Route::post($base_segment.$purchase_delivery_segment.'/store', 'store')->name('main_transaction.purchase_delivery.store');
        Route::get($base_segment.$purchase_delivery_segment.'/detail/{id}', 'detail')->name('main_transaction.purchase_delivery.detail');
        Route::get($base_segment.$purchase_delivery_segment.'/audit_log_data_table/{id}', 'auditLogDataTable')->name('main_transaction.purchase_delivery.audit_log_data_table');
        Route::get($base_segment.$purchase_delivery_segment.'/edit/{id}', 'edit')->name('main_transaction.purchase_delivery.edit');
        Route::post($base_segment.$purchase_delivery_segment.'/update', 'update')->name('main_transaction.purchase_delivery.update');
        Route::get($base_segment.$purchase_delivery_segment.'/import', 'import')->name('main_transaction.purchase_delivery.import');
        Route::get($base_segment.$purchase_delivery_segment.'/download_template', 'downloadTemplate')->name('main_transaction.purchase_delivery.download_template');
        Route::post($base_segment.$purchase_delivery_segment.'/validate_import', 'validateImport')->name('main_transaction.purchase_delivery.validate_import');
        Route::post($base_segment.$purchase_delivery_segment.'/process_import', 'processImport')->name('main_transaction.purchase_delivery.process_import');
        Route::get($base_segment.$purchase_delivery_segment.'/export', 'export')->name('main_transaction.purchase_delivery.export');
        Route::post($base_segment.$purchase_delivery_segment.'/process_export', 'processExport')->name('main_transaction.purchase_delivery.process_export');
        Route::get($base_segment.$purchase_delivery_segment.'/print_pdf/{po_id}', 'printPDF')->name('main_transaction.purchase_delivery.print_pdf');
        // Route::get($base_segment.$purchase_delivery_segment.'/get_po_products', 'getPOProducts')->name('main_transaction.purchase_delivery.get_po_products');
        Route::get($base_segment.$purchase_delivery_segment.'/get_po_details', 'getPODetails')->name('main_transaction.purchase_delivery.get_po_details');
        Route::get($base_segment.$purchase_delivery_segment.'/get_previous_deliveries', 'getPreviousDeliveries')->name('main_transaction.purchase_delivery.get_previous_deliveries');
        Route::get($base_segment.$purchase_delivery_segment.'/get_products', 'getProducts')->name('main_transaction.purchase_delivery.get_products');
        Route::get($base_segment.$purchase_delivery_segment.'/generate_transaction_number', 'generateTransactionNumber')->name('main_transaction.purchase_delivery.generate_transaction_number');
        Route::get($base_segment.$purchase_delivery_segment.'/generate_delivery_number', 'generateDeliveryNumber')->name('main_transaction.purchase_delivery.generate_delivery_number');
    });

    // Purchase Invoice Routes
    Route::controller(PurchaseInvoiceController::class)->group(function () use ($base_segment, $purchase_invoice_segment) {
        Route::get($base_segment.$purchase_invoice_segment.'/index', 'index')->name('main_transaction.purchase_invoice.index');
        Route::get($base_segment.$purchase_invoice_segment.'/index_data_table', 'index_data_table')->name('main_transaction.purchase_invoice.index_data_table');
        Route::get($base_segment.$purchase_invoice_segment.'/add', 'add')->name('main_transaction.purchase_invoice.add');
        Route::post($base_segment.$purchase_invoice_segment.'/store', 'store')->name('main_transaction.purchase_invoice.store');
        Route::get($base_segment.$purchase_invoice_segment.'/detail/{id}', 'detail')->name('main_transaction.purchase_invoice.detail');
        Route::get($base_segment.$purchase_invoice_segment.'/audit_log_data_table/{id}', 'auditLogDataTable')->name('main_transaction.purchase_invoice.audit_log_data_table');
        Route::get($base_segment.$purchase_invoice_segment.'/edit/{id}', 'edit')->name('main_transaction.purchase_invoice.edit');
        Route::post($base_segment.$purchase_invoice_segment.'/update', 'update')->name('main_transaction.purchase_invoice.update');
        Route::get($base_segment.$purchase_invoice_segment.'/import', 'import')->name('main_transaction.purchase_invoice.import');
        Route::get($base_segment.$purchase_invoice_segment.'/download_template', 'downloadTemplate')->name('main_transaction.purchase_invoice.download_template');
        Route::post($base_segment.$purchase_invoice_segment.'/validate_import', 'validateImport')->name('main_transaction.purchase_invoice.validate_import');
        Route::post($base_segment.$purchase_invoice_segment.'/process_import', 'processImport')->name('main_transaction.purchase_invoice.process_import');
        Route::get($base_segment.$purchase_invoice_segment.'/export', 'export')->name('main_transaction.purchase_invoice.export');
        Route::post($base_segment.$purchase_invoice_segment.'/process_export', 'processExport')->name('main_transaction.purchase_invoice.process_export');

        // API Routes for Purchase Invoice
        Route::get($base_segment.$purchase_invoice_segment.'/get_available_purchase_orders', 'get_available_purchase_orders')->name('main_transaction.purchase_invoice.get_available_purchase_orders');
        Route::get($base_segment.$purchase_invoice_segment.'/get_po_products_for_invoice', 'get_po_products_for_invoice')->name('main_transaction.purchase_invoice.get_po_products_for_invoice');
    });

    // Sales Order Routes (Separated Module)
    Route::controller(SalesOrderController::class)->group(function () use ($base_segment, $sales_order_segment) {
        Route::get($base_segment.$sales_order_segment.'/index', 'index')->name('main_transaction.sales_order.index');
        Route::get($base_segment.$sales_order_segment.'/index_data_table', 'index_data_table')->name('main_transaction.sales_order.index_data_table');
        Route::get($base_segment.$sales_order_segment.'/generate_transaction_number', 'generateTransactionNumber')->name('main_transaction.sales_order.generate_transaction_number');
        Route::get($base_segment.$sales_order_segment.'/add', 'add')->name('main_transaction.sales_order.add');
        Route::post($base_segment.$sales_order_segment.'/store', 'store')->name('main_transaction.sales_order.store');
        Route::get($base_segment.$sales_order_segment.'/detail/{id}', 'detail')->name('main_transaction.sales_order.detail');
        Route::get($base_segment.$sales_order_segment.'/edit/{id}', 'edit')->name('main_transaction.sales_order.edit');
        Route::post($base_segment.$sales_order_segment.'/update', 'update')->name('main_transaction.sales_order.update');
        Route::get($base_segment.$sales_order_segment.'/import', 'import')->name('main_transaction.sales_order.import');
        Route::get($base_segment.$sales_order_segment.'/download_template', 'downloadTemplate')->name('main_transaction.sales_order.download_template');
        Route::post($base_segment.$sales_order_segment.'/validate_import', 'validateImport')->name('main_transaction.sales_order.validate_import');
        Route::post($base_segment.$sales_order_segment.'/process_import', 'processImport')->name('main_transaction.sales_order.process_import');
        Route::get($base_segment.$sales_order_segment.'/export', 'export')->name('main_transaction.sales_order.export');
        Route::post($base_segment.$sales_order_segment.'/process_export', 'processExport')->name('main_transaction.sales_order.process_export');

        // AJAX endpoints
        Route::get($base_segment.$sales_order_segment.'/get_customer_info/{customer_id}', 'getCustomerInfo')->name('main_transaction.sales_order.get_customer_info');
        Route::get($base_segment.$sales_order_segment.'/get_warehouse_products/{customer_id}', 'getWarehouseProducts')->name('main_transaction.sales_order.get_warehouse_products');
        Route::post($base_segment.$sales_order_segment.'/cancel/{id}', 'cancel')->name('main_transaction.sales_order.cancel');
    });

    // Sales Order Revenue Split Routes
    Route::controller(SalesOrderRevenueSplitController::class)->group(function () use ($base_segment, $sales_order_segment) {
        Route::get($base_segment.$sales_order_segment.'/revenue-split/{salesOrderId}', 'getRevenueSplits')->name('main_transaction.sales_order.revenue_split.get');
        Route::get($base_segment.$sales_order_segment.'/revenue-split/{salesOrderId}/breakdown', 'showRevenueSplitBreakdown')->name('main_transaction.sales_order.revenue_split.breakdown');
        Route::put($base_segment.$sales_order_segment.'/revenue-split/{salesOrderId}/product/{productId}', 'updateRevenueSplit')->name('main_transaction.sales_order.revenue_split.update');
        Route::post($base_segment.$sales_order_segment.'/revenue-split/{salesOrderId}/product/{productId}/reset', 'resetRevenueSplit')->name('main_transaction.sales_order.revenue_split.reset');
    });

    // Sales Delivery Routes (Separated Module)
    Route::controller(SalesDeliveryController::class)->group(function () use ($base_segment, $sales_delivery_segment) {
        Route::get($base_segment.$sales_delivery_segment.'/index', 'index')->name('main_transaction.sales_delivery.index');
        Route::get($base_segment.$sales_delivery_segment.'/index_data_table', 'index_data_table')->name('main_transaction.sales_delivery.index_data_table');
        Route::get($base_segment.$sales_delivery_segment.'/generate_transaction_number', 'generateTransactionNumber')->name('main_transaction.sales_delivery.generate_transaction_number');
        Route::get($base_segment.$sales_delivery_segment.'/add', 'add')->name('main_transaction.sales_delivery.add');
        Route::post($base_segment.$sales_delivery_segment.'/create', 'create')->name('main_transaction.sales_delivery.create');
        Route::get($base_segment.$sales_delivery_segment.'/detail/{id}', 'detail')->name('main_transaction.sales_delivery.detail');
        Route::post($base_segment.$sales_delivery_segment.'/confirm/{id}', 'confirm')->name('main_transaction.sales_delivery.confirm');
        Route::get($base_segment.$sales_delivery_segment.'/edit/{id}', 'edit')->name('main_transaction.sales_delivery.edit');
        Route::post($base_segment.$sales_delivery_segment.'/update', 'update')->name('main_transaction.sales_delivery.update');
        Route::get($base_segment.$sales_delivery_segment.'/import', 'import')->name('main_transaction.sales_delivery.import');
        Route::get($base_segment.$sales_delivery_segment.'/download_template', 'downloadTemplate')->name('main_transaction.sales_delivery.download_template');
        Route::post($base_segment.$sales_delivery_segment.'/validate_import', 'validateImport')->name('main_transaction.sales_delivery.validate_import');
        Route::post($base_segment.$sales_delivery_segment.'/process_import', 'processImport')->name('main_transaction.sales_delivery.process_import');
        Route::get($base_segment.$sales_delivery_segment.'/export', 'export')->name('main_transaction.sales_delivery.export');
        Route::post($base_segment.$sales_delivery_segment.'/process_export', 'processExport')->name('main_transaction.sales_delivery.process_export');
    });

    // Sales Invoice Routes (Separated Module)
    Route::controller(SalesInvoiceController::class)->group(function () use ($base_segment, $sales_invoice_segment) {
        Route::get($base_segment.$sales_invoice_segment.'/index', 'index')->name('main_transaction.sales_invoice.index');
        Route::get($base_segment.$sales_invoice_segment.'/index_data_table', 'index_data_table')->name('main_transaction.sales_invoice.index_data_table');
        Route::get($base_segment.$sales_invoice_segment.'/add', 'add')->name('main_transaction.sales_invoice.add');
        Route::post($base_segment.$sales_invoice_segment.'/store', 'store')->name('main_transaction.sales_invoice.store');
        Route::get($base_segment.$sales_invoice_segment.'/detail/{id}', 'detail')->name('main_transaction.sales_invoice.detail');
        Route::get($base_segment.$sales_invoice_segment.'/print/{id}', 'print')->name('main_transaction.sales_invoice.print');
        Route::get($base_segment.$sales_invoice_segment.'/edit/{id}', 'edit')->name('main_transaction.sales_invoice.edit');
        Route::put($base_segment.$sales_invoice_segment.'/update/{id}', 'update')->name('main_transaction.sales_invoice.update');
        Route::get($base_segment.$sales_invoice_segment.'/import', 'import')->name('main_transaction.sales_invoice.import');
        Route::get($base_segment.$sales_invoice_segment.'/download_template', 'downloadTemplate')->name('main_transaction.sales_invoice.download_template');
        Route::post($base_segment.$sales_invoice_segment.'/validate_import', 'validateImport')->name('main_transaction.sales_invoice.validate_import');
        Route::post($base_segment.$sales_invoice_segment.'/process_import', 'processImport')->name('main_transaction.sales_invoice.process_import');
        Route::get($base_segment.$sales_invoice_segment.'/export', 'export')->name('main_transaction.sales_invoice.export');
        Route::post($base_segment.$sales_invoice_segment.'/process_export', 'processExport')->name('main_transaction.sales_invoice.process_export');
        Route::get($base_segment.$sales_invoice_segment.'/payment_recipient', 'payment_recipient')->name('main_transaction.sales_invoice.payment_recipient');

        Route::get($base_segment.$sales_invoice_segment.'/generate_transaction_number', 'generateTransactionNumber')->name('master_transaction.sales_invoice.generate_transaction_number');

        // API routes for Sales Invoice functionality
        Route::get($base_segment.$sales_invoice_segment.'/get_available_sales_orders', 'get_available_sales_orders')->name('main_transaction.sales_invoice.get_available_sales_orders');
        Route::get($base_segment.$sales_invoice_segment.'/get_so_products_for_invoice', 'get_so_products_for_invoice')->name('main_transaction.sales_invoice.get_so_products_for_invoice');
        Route::get($base_segment.$sales_invoice_segment.'/audit_log/{id}', 'auditLogDataTable')->name('main_transaction.sales_invoice.auditLogDataTable');
        Route::post($base_segment.$sales_invoice_segment.'/payment_save', 'payment_save')->name('main_transaction.sales_invoice.payment_save');
    });

    Route::controller(CreditMemoController::class)->group(function () use ($base_segment, $credit_memo_segment) {
        Route::get($base_segment.$credit_memo_segment.'/index', 'index')->name('main_transaction.credit_memo.index');
        Route::get($base_segment.$credit_memo_segment.'/index_data_table', 'index_data_table')->name('main_transaction.credit_memo.index_data_table');
        Route::get($base_segment.$credit_memo_segment.'/add', 'add')->name('main_transaction.credit_memo.add');
        Route::get($base_segment.$credit_memo_segment.'/add/{contact_id?}', 'addWithContact')->name('main_transaction.credit_memo.add_with_contact');
        Route::get($base_segment.$credit_memo_segment.'/export', 'export')->name('main_transaction.credit_memo.export');
        Route::get($base_segment.$credit_memo_segment.'/import', 'import')->name('main_transaction.credit_memo.import');
    });

    Route::controller(ExpenseController::class)->group(function () use ($base_segment, $expense_segment) {
        Route::get($base_segment.$expense_segment.'/index', 'index')->name('main_transaction.expense.index');
        Route::get($base_segment.$expense_segment.'/index_data_table', 'index_data_table')->name('main_transaction.expense.index_data_table');
        Route::get($base_segment.$expense_segment.'/add', 'add')->name('main_transaction.expense.add');
        Route::post($base_segment.$expense_segment.'/save', 'save')->name('main_transaction.expense.save');
        Route::get($base_segment.$expense_segment.'/edit/{id}', 'edit')->name('main_transaction.expense.edit');
        Route::post($base_segment.$expense_segment.'/update', 'update')->name('main_transaction.expense.update');
        Route::get($base_segment.$expense_segment.'/payment/{id}', 'payment')->name('main_transaction.expense.payment');
        Route::post($base_segment.$expense_segment.'/payment/submit', 'payment_save')->name('main_transaction.expense.payment.save');
        Route::get($base_segment.$expense_segment.'/payment_detail/{id}', 'payment_detail')->name('main_transaction.expense.payment_detail');
        Route::get($base_segment.$expense_segment.'/detail/{id}', 'detail')->name('main_transaction.expense.detail');
        Route::get($base_segment.$expense_segment.'/categories', 'categories')->name('main_transaction.expense.categories');
        Route::get($base_segment.$expense_segment.'/statuses', 'statuses')->name('main_transaction.expense.statuses');
        Route::get($base_segment.$expense_segment.'/recipients', 'recipients')->name('main_transaction.expense.recipients');
        Route::get($base_segment.$expense_segment.'/accounts', 'accounts')->name('main_transaction.expense.accounts');
        Route::get($base_segment.$expense_segment.'/payment_methods', 'paymentMethods')->name('main_transaction.expense.payment_methods');
        Route::get($base_segment.$expense_segment.'/payment_terms', 'paymentTerms')->name('main_transaction.expense.payment_terms');
        Route::get($base_segment.$expense_segment.'/taxes', 'taxes')->name('main_transaction.expense.taxes');
        Route::get($base_segment.$expense_segment.'/tags', 'tags')->name('main_transaction.expense.tags');
        Route::get($base_segment.$expense_segment.'/export', 'export')->name('main_transaction.expense.export');
        Route::post($base_segment.$expense_segment.'/import', 'import')->name('main_transaction.expense.import');
    });

    Route::controller(StockAdjustmentController::class)->group(function () use ($base_segment, $stock_adjustment_segment) {
        Route::get($base_segment.$stock_adjustment_segment.'/index', 'index')->name('main_transaction.stock_adjustment.index');
        Route::get($base_segment.$stock_adjustment_segment.'/index_data_table', 'index_data_table')->name('main_transaction.stock_adjustment.index_data_table');
        Route::get($base_segment.$stock_adjustment_segment.'/add', 'add')->name('main_transaction.stock_adjustment.add');
        Route::post($base_segment.$stock_adjustment_segment.'/save', 'save')->name('main_transaction.stock_adjustment.save');
        Route::get($base_segment.$stock_adjustment_segment.'/edit/{id}', 'edit')->name('main_transaction.stock_adjustment.edit');
        Route::post($base_segment.$stock_adjustment_segment.'/update', 'update')->name('main_transaction.stock_adjustment.update');
        Route::get($base_segment.$stock_adjustment_segment.'/detail/{id}', 'detail')->name('main_transaction.stock_adjustment.detail');
        Route::get($base_segment.$stock_adjustment_segment.'/get_warehouse_products', 'getWarehouseProducts')->name('main_transaction.stock_adjustment.get_warehouse_products');
        Route::get($base_segment.$stock_adjustment_segment.'/generate_transaction_number', 'generateTransactionNumber')->name('main_transaction.stock_adjustment.generate_transaction_number');
        Route::get($base_segment.$stock_adjustment_segment.'/export', 'export')->name('main_transaction.stock_adjustment.export');
        Route::post($base_segment.$stock_adjustment_segment.'/process_export', 'processExport')->name('main_transaction.stock_adjustment.process_export');
        Route::get($base_segment.$stock_adjustment_segment.'/import', 'import')->name('main_transaction.stock_adjustment.import');
        Route::get($base_segment.$stock_adjustment_segment.'/download_template', 'downloadTemplate')->name('main_transaction.stock_adjustment.download_template');
        Route::post($base_segment.$stock_adjustment_segment.'/validate_import', 'validateImport')->name('main_transaction.stock_adjustment.validate_import');
        Route::post($base_segment.$stock_adjustment_segment.'/process_import', 'processImport')->name('main_transaction.stock_adjustment.process_import');
    });

    // Production Routes
    Route::controller(ProductionController::class)->group(function () use ($base_segment, $production_segment) {
        Route::get($base_segment.$production_segment.'/index', 'index')->name('main_transaction.production.index');
        Route::get($base_segment.$production_segment.'/index_data_table', 'index_data_table')->name('main_transaction.production.index_data_table');
        Route::get($base_segment.$production_segment.'/add', 'add')->name('main_transaction.production.add');
        Route::post($base_segment.$production_segment.'/store', 'store')->name('main_transaction.production.store');
        Route::get($base_segment.$production_segment.'/detail/{id}', 'detail')->name('main_transaction.production.detail');
        // Edit/Update removed - use Reversal process instead
        Route::delete($base_segment.$production_segment.'/{id}', 'destroy')->name('main_transaction.production.destroy');
        Route::get($base_segment.$production_segment.'/export', 'export')->name('main_transaction.production.export');
        Route::post($base_segment.$production_segment.'/process_export', 'processExport')->name('main_transaction.production.process_export');

        // AJAX Routes for dynamic functionality
        Route::get($base_segment.$production_segment.'/get_bundle_components', 'getBundleComponents')->name('main_transaction.production.get_bundle_components');
        Route::get($base_segment.$production_segment.'/get_warehouses', 'getWarehouses')->name('main_transaction.production.get_warehouses');
        Route::get($base_segment.$production_segment.'/get_bundle_products', 'getBundleProducts')->name('main_transaction.production.get_bundle_products');
        Route::get($base_segment.$production_segment.'/get_pic_users', 'getPicUsers')->name('main_transaction.production.get_pic_users');
        Route::get($base_segment.$production_segment.'/generate_transaction_number', 'generateTransactionNumber')->name('main_transaction.production.generate_transaction_number');
        Route::get($base_segment.$production_segment.'/calculate_fifo_prices', 'calculateFifoPrices')->name('main_transaction.production.calculate_fifo_prices');
        Route::get($base_segment.$production_segment.'/get_bundle_price', 'getBundlePrice')->name('main_transaction.production.get_bundle_price');

        // AJAX endpoints for form data
        Route::get($base_segment.$production_segment.'/get-formula/{bundleProductId}', 'getFormula')->name('main_transaction.production.get_formula');
        Route::get($base_segment.$production_segment.'/get-expenses/{bundleProductId}', 'getExpenses')->name('main_transaction.production.get_expenses');

        // Reversal Routes
        Route::get($base_segment.$production_segment.'/reversal/{id}', 'reversal')->name('main_transaction.production.reversal');
        Route::post($base_segment.$production_segment.'/reversal/{id}', 'processReversal')->name('main_transaction.production.process_reversal');

        // Action Routes for production management
        Route::post($base_segment.$production_segment.'/complete/{id}', 'complete')->name('main_transaction.production.complete');
        Route::post($base_segment.$production_segment.'/cancel/{id}', 'cancel')->name('main_transaction.production.cancel');
    });

    // Packing Routes
    Route::controller(PackingController::class)->group(function () use ($base_segment, $packing_segment) {
        Route::get($base_segment.$packing_segment.'/index', 'index')->name('main_transaction.packing.index');
        Route::get($base_segment.$packing_segment.'/index_data_table', 'index_data_table')->name('main_transaction.packing.index_data_table');
        Route::get($base_segment.$packing_segment.'/add', 'add')->name('main_transaction.packing.add');
        Route::post($base_segment.$packing_segment.'/store', 'store')->name('main_transaction.packing.store');
        Route::get($base_segment.$packing_segment.'/detail/{id}', 'detail')->name('main_transaction.packing.detail');
        Route::delete($base_segment.$packing_segment.'/{id}', 'destroy')->name('main_transaction.packing.destroy');
        Route::get($base_segment.$packing_segment.'/export', 'export')->name('main_transaction.packing.export');
        Route::post($base_segment.$packing_segment.'/process_export', 'processExport')->name('main_transaction.packing.process_export');

        // AJAX Routes for dynamic functionality
        Route::get($base_segment.$packing_segment.'/get_bundle_components', 'getBundleComponents')->name('main_transaction.packing.get_bundle_components');
        Route::get($base_segment.$packing_segment.'/get_warehouses', 'getWarehouses')->name('main_transaction.packing.get_warehouses');
        Route::get($base_segment.$packing_segment.'/get_bundle_products', 'getBundleProducts')->name('main_transaction.packing.get_bundle_products');
        Route::get($base_segment.$packing_segment.'/get_packaging_products', 'getPackagingProducts')->name('main_transaction.packing.get_packaging_products');
        Route::get($base_segment.$packing_segment.'/get_bundle_stock', 'getBundleStock')->name('main_transaction.packing.get_bundle_stock');
        Route::get($base_segment.$packing_segment.'/get_pic_users', 'getPicUsers')->name('main_transaction.packing.get_pic_users');
        Route::get($base_segment.$packing_segment.'/generate_transaction_number', 'generateTransactionNumber')->name('main_transaction.packing.generate_transaction_number');
        Route::get($base_segment.$packing_segment.'/calculate_fifo_prices', 'calculateFifoPrices')->name('main_transaction.packing.calculate_fifo_prices');
        Route::get($base_segment.$packing_segment.'/get_bundle_price', 'getBundlePrice')->name('main_transaction.packing.get_bundle_price');
        Route::get($base_segment.$packing_segment.'/get_available_productions', 'getAvailableProductions')->name('main_transaction.packing.get_available_productions');
    });

    // Return Management Routes
    Route::controller(ReturnManagementController::class)->group(function () use ($base_segment, $return_management_segment) {
        Route::get($base_segment.$return_management_segment.'/index', 'index')->name('main_transaction.return_management.index');
        Route::get($base_segment.$return_management_segment.'/index_data_table', 'index_data_table')->name('main_transaction.return_management.index_data_table');
        Route::get($base_segment.$return_management_segment.'/add', 'add')->name('main_transaction.return_management.add');
        Route::post($base_segment.$return_management_segment.'/store', 'store')->name('main_transaction.return_management.store');
        Route::get($base_segment.$return_management_segment.'/detail/{id}', 'detail')->name('main_transaction.return_management.detail');
        Route::get($base_segment.$return_management_segment.'/audit_log_data_table/{id}', 'auditLogDataTable')->name('main_transaction.return_management.audit_log_data_table');
        Route::get($base_segment.$return_management_segment.'/edit/{id}', 'edit')->name('main_transaction.return_management.edit');
        Route::post($base_segment.$return_management_segment.'/update', 'update')->name('main_transaction.return_management.update');

        // API endpoints
        Route::get($base_segment.$return_management_segment.'/generate_transaction_number', 'generateTransactionNumber')->name('main_transaction.return_management.generate_transaction_number');
        Route::get($base_segment.$return_management_segment.'/get_invoice_products', 'getInvoiceProducts')->name('main_transaction.return_management.get_invoice_products');
    });

    // Overhead Absorption Routes
    Route::controller(OverheadAbsorptionController::class)->group(function () use ($base_segment) {
        Route::get($base_segment.'/overhead_absorption/index', 'index')->name('main_transaction.overhead_absorption.index');
        Route::get($base_segment.'/overhead_absorption/preview', 'getPreview')->name('main_transaction.overhead_absorption.preview');
        Route::post($base_segment.'/overhead_absorption/settle', 'settle')->name('main_transaction.overhead_absorption.settle');
        Route::get($base_segment.'/overhead_absorption/history', 'getHistory')->name('main_transaction.overhead_absorption.history');
        Route::get($base_segment.'/overhead_absorption/details/{id}', 'getDetails')->name('main_transaction.overhead_absorption.details');
    });

    // Top Up Routes
    Route::controller(TopUpController::class)->group(function () use ($base_segment, $topup_segment) {
        Route::get($base_segment.$topup_segment.'/index', 'index')->name('main_transaction.topup.index');
        Route::get($base_segment.$topup_segment.'/index_data_table', 'index_data_table')->name('main_transaction.topup.index_data_table');
        Route::get($base_segment.$topup_segment.'/add', 'add')->name('main_transaction.topup.add');
        Route::post($base_segment.$topup_segment.'/store', 'store')->name('main_transaction.topup.store');
        Route::get($base_segment.$topup_segment.'/detail/{id}', 'detail')->name('main_transaction.topup.detail');
        Route::get($base_segment.$topup_segment.'/edit/{id}', 'edit')->name('main_transaction.topup.edit');
        Route::post($base_segment.$topup_segment.'/update', 'update')->name('main_transaction.topup.update');
        Route::delete($base_segment.$topup_segment.'/{id}', 'destroy')->name('main_transaction.topup.destroy');

        // AJAX Routes
        Route::get($base_segment.$topup_segment.'/generate_transaction_number', 'generateTransactionNumber')->name('main_transaction.topup.generate_transaction_number');
        Route::get($base_segment.$topup_segment.'/get_outstanding_sales_orders', 'getOutstandingSalesOrders')->name('main_transaction.topup.get_outstanding_sales_orders');
        Route::post($base_segment.$topup_segment.'/calculate_fifo_allocation', 'calculateFifoAllocation')->name('main_transaction.topup.calculate_fifo_allocation');
    });
});
