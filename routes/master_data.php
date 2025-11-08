<?php

use App\Http\Controllers\MasterData\CategoryController;
// Controllers
use App\Http\Controllers\MasterData\CoaController;
use App\Http\Controllers\MasterData\ContactController;
use App\Http\Controllers\MasterData\InventoryAssetController;
use App\Http\Controllers\MasterData\PaymentTermController;
use App\Http\Controllers\MasterData\ProductController;
use App\Http\Controllers\MasterData\ProductRevenueSplitController;
use App\Http\Controllers\MasterData\RevenueSplitController;
use App\Http\Controllers\MasterData\RoleController;
use App\Http\Controllers\MasterData\SalesPersonController;
use App\Http\Controllers\MasterData\StockOpnameController;
use App\Http\Controllers\MasterData\TagController;
use App\Http\Controllers\MasterData\TaxController;
use App\Http\Controllers\MasterData\TransactionNumberController;
use App\Http\Controllers\MasterData\UnitController;
use App\Http\Controllers\MasterData\UserController;
use App\Http\Controllers\MasterData\WarehouseController;
use Illuminate\Support\Facades\Route;

$base_segment = '/master_data';
$contact_segment = '/contact';
$warehouse_segment = '/warehouse';
$product_segment = '/product';
$coa_segment = '/coa';
$user_segment = '/user';
$role_segment = '/role';
$stock_opname_segment = '/stock_opname';
$inventory_segment = '/inventory';
$tag_segment = '/tag';
$tax_segment = '/tax';
$unit_segment = '/unit';
$category_segment = '/category';
$sales_person_segment = '/sales_person';
$payment_term_segment = '/payment_term';
$transaction_number_segment = '/transaction_number';
$revenue_split_segment = '/revenue_split';

Route::middleware(['auth'])->group(function () use ($base_segment, $contact_segment, $warehouse_segment, $product_segment, $coa_segment, $user_segment, $role_segment, $stock_opname_segment, $inventory_segment, $tag_segment, $tax_segment, $unit_segment, $category_segment, $sales_person_segment, $payment_term_segment, $transaction_number_segment, $revenue_split_segment) {
    Route::controller(ContactController::class)->group(function () use ($base_segment, $contact_segment) {
        Route::get($base_segment.$contact_segment.'/index', 'index')->name('master_data.contact.index');
        Route::get($base_segment.$contact_segment.'/index_data_table', 'index_data_table')->name('master_data.contact.index_data_table');
        Route::get($base_segment.$contact_segment.'/add', 'add')->name('master_data.contact.add');
        Route::post($base_segment.$contact_segment.'/save', 'save')->name('master_data.contact.save');
        Route::get($base_segment.$contact_segment.'/edit/{id}', 'edit')->name('master_data.contact.edit');
        Route::post($base_segment.$contact_segment.'/update', 'update')->name('master_data.contact.update');
        Route::get($base_segment.$contact_segment.'/detail/{id}', 'detail')->name('master_data.contact.detail');
        Route::get($base_segment.$contact_segment.'/detail_profil_data/{id}', 'getProfilData')->name('master_data.contact.detail_profil_data');
        Route::get($base_segment.$contact_segment.'/detail_transaction_data/{id}', 'getTransactionData')->name('master_data.contact.detail_transaction_data');
        Route::get($base_segment.$contact_segment.'/detail_credit_memo_data/{id}', 'getCreditMemoData')->name('master_data.contact.detail_credit_memo_data');
        Route::get($base_segment.$contact_segment.'/export', 'export')->name('master_data.contact.export');
        Route::get($base_segment.$contact_segment.'/import', 'import')->name('master_data.contact.import');
    });

    Route::controller(WarehouseController::class)->group(function () use ($base_segment, $warehouse_segment) {
        Route::get($base_segment.$warehouse_segment.'/index', 'index')->name('master_data.warehouse.index');
        Route::get($base_segment.$warehouse_segment.'/index_data_table', 'index_data_table')->name('master_data.warehouse.index_data_table');
        Route::get($base_segment.$warehouse_segment.'/add', 'add')->name('master_data.warehouse.add');
        Route::post($base_segment.$warehouse_segment.'/store', 'store')->name('master_data.warehouse.store');
        Route::get($base_segment.$warehouse_segment.'/edit/{id}', 'edit')->name('master_data.warehouse.edit');
        Route::put($base_segment.$warehouse_segment.'/update/{id}', 'update')->name('master_data.warehouse.update');
        Route::get($base_segment.$warehouse_segment.'/detail/{id}', 'detail')->name('master_data.warehouse.detail');
        Route::get($base_segment.$warehouse_segment.'/detail_product_data_table', 'detail_product_data_table')->name('master_data.warehouse.detail_product_data_table');
        Route::get($base_segment.$warehouse_segment.'/detail_transaction_data_table', 'detail_transaction_data_table')->name('master_data.warehouse.detail_transaction_data_table');
        Route::get($base_segment.$warehouse_segment.'/detail_batch_data_table', 'detail_batch_data_table')->name('master_data.warehouse.detail_batch_data_table');
        Route::get($base_segment.$warehouse_segment.'/export', 'export')->name('master_data.warehouse.export');
        Route::get($base_segment.$warehouse_segment.'/import', 'import')->name('master_data.warehouse.import');
    });

    Route::controller(ProductController::class)->group(function () use ($base_segment, $product_segment) {
        Route::get($base_segment.$product_segment.'/index', 'index')->name('master_data.product.index');
        Route::get($base_segment.$product_segment.'/index_data_table', 'index_data_table')->name('master_data.product.index_data_table');
        Route::get($base_segment.$product_segment.'/index_warehouse_data_table', 'index_warehouse_data_table')->name('master_data.product.index_warehouse_data_table');
        Route::get($base_segment.$product_segment.'/add', 'add')->name('master_data.product.add');
        Route::post($base_segment.$product_segment.'/save', 'save')->name('master_data.product.save');
        Route::get($base_segment.$product_segment.'/detail/{id}', 'detail')->name('master_data.product.detail');
        Route::get($base_segment.$product_segment.'/edit/{id}', 'edit')->name('master_data.product.edit');
        Route::post($base_segment.$product_segment.'/update', 'update')->name('master_data.product.update');
        Route::get($base_segment.$product_segment.'/export', 'export')->name('master_data.product.export');
        Route::get($base_segment.$product_segment.'/import', 'import')->name('master_data.product.import');
    });

    Route::controller(CoaController::class)->group(function () use ($base_segment, $coa_segment) {
        Route::get($base_segment.$coa_segment.'/index', 'index')->name('master_data.coa.index');
        Route::get($base_segment.$coa_segment.'/index_data_table', 'index_data_table')->name('master_data.coa.index_data_table');
        Route::get($base_segment.$coa_segment.'/journal_log_per_coa_data_table', 'journal_log_per_coa_data_table')->name('master_data.coa.journal_log_per_coa_data_table');
        Route::get($base_segment.$coa_segment.'/add', 'add')->name('master_data.coa.add');
        Route::post($base_segment.$coa_segment.'/save', 'save')->name('master_data.coa.save');
        Route::get($base_segment.$coa_segment.'/detail/{id}', 'detail')->name('master_data.coa.detail');
        Route::get($base_segment.$coa_segment.'/edit/{id}', 'edit')->name('master_data.coa.edit');
        Route::post($base_segment.$coa_segment.'/update', 'update')->name('master_data.coa.update');
        Route::get($base_segment.$coa_segment.'/export', 'export')->name('master_data.coa.export');
        Route::get($base_segment.$coa_segment.'/import', 'import')->name('master_data.coa.import');
    });

    Route::controller(UserController::class)->group(function () use ($base_segment, $user_segment) {
        Route::get($base_segment.$user_segment.'/index', 'index')->name('master_data.user.index');
        Route::get($base_segment.$user_segment.'/index_data_table', 'index_data_table')->name('master_data.user.index_data_table');
        Route::get($base_segment.$user_segment.'/add', 'add')->name('master_data.user.add');
        Route::post($base_segment.$user_segment.'/save', 'save')->name('master_data.user.save');
        Route::get($base_segment.$user_segment.'/edit/{id}', 'edit')->name('master_data.user.edit');
        Route::post($base_segment.$user_segment.'/update', 'update')->name('master_data.user.update');
        Route::get($base_segment.$user_segment.'/detail/{id}', 'detail')->name('master_data.user.detail');
        Route::get($base_segment.$user_segment.'/export', 'export')->name('master_data.user.export');
        Route::get($base_segment.$user_segment.'/import', 'import')->name('master_data.user.import');
    });

    Route::controller(RoleController::class)->group(function () use ($base_segment, $role_segment) {
        Route::get($base_segment.$role_segment.'/index', 'index')->name('master_data.role.index');
        Route::get($base_segment.$role_segment.'/index_data_table', 'index_data_table')->name('master_data.role.index_data_table');
        Route::get($base_segment.$role_segment.'/add', 'add')->name('master_data.role.add');
        Route::post($base_segment.$role_segment.'/save', 'save')->name('master_data.role.save');
        Route::get($base_segment.$role_segment.'/edit/{id}', 'edit')->name('master_data.role.edit');
        Route::get($base_segment.$role_segment.'/detail/{id}', 'detail')->name('master_data.role.detail');
        Route::get($base_segment.$role_segment.'/export', 'export')->name('master_data.role.export');
        Route::get($base_segment.$role_segment.'/import', 'import')->name('master_data.role.import');
        Route::get($base_segment.$role_segment.'/permissions/{id}', 'getRolePermissions')->name('master_data.role.permissions');
        Route::get($base_segment.$role_segment.'/users/{id}', 'getUsersByRole')->name('master_data.role.users');
        Route::post($base_segment.$role_segment.'/update', 'update')->name('master_data.role.update');
    });

    Route::controller(StockOpnameController::class)->group(function () use ($base_segment, $stock_opname_segment) {
        Route::get($base_segment.$stock_opname_segment.'/index', 'index')->name('master_data.stock_opname.index');
        Route::get($base_segment.$stock_opname_segment.'/index_data_table', 'index_data_table')->name('master_data.stock_opname.index_data_table');
        Route::get($base_segment.$stock_opname_segment.'/get_product_list', 'get_product_list')->name('master_data.stock_opname.get_product_list');
        Route::get($base_segment.$stock_opname_segment.'/add', 'add')->name('master_data.stock_opname.add');
        Route::post($base_segment.$stock_opname_segment.'/save', 'save')->name('master_data.stock_opname.save');
        Route::get($base_segment.$stock_opname_segment.'/edit/{id}', 'edit')->name('master_data.stock_opname.edit');
        Route::get($base_segment.$stock_opname_segment.'/detail/{id}', 'detail')->name('master_data.stock_opname.detail');

        // Import routes
        Route::get($base_segment.$stock_opname_segment.'/import', 'import')->name('master_data.stock_opname.import');
        Route::get($base_segment.$stock_opname_segment.'/download-template', 'downloadTemplate')->name('master_data.stock_opname.download_template');
        Route::post($base_segment.$stock_opname_segment.'/validate-import', 'validateImport')->name('master_data.stock_opname.validate_import');
        Route::post($base_segment.$stock_opname_segment.'/process-import', 'processImport')->name('master_data.stock_opname.process_import');

        // Export routes
        Route::get($base_segment.$stock_opname_segment.'/export', 'export')->name('master_data.stock_opname.export');
        Route::post($base_segment.$stock_opname_segment.'/process-export', 'processExport')->name('master_data.stock_opname.process_export');

        Route::post($base_segment.$stock_opname_segment.'/create_adjustment/{id}', 'createAdjustment')->name('master_data.stock_opname.create_adjustment');
    });

    Route::controller(InventoryAssetController::class)->group(function () use ($base_segment, $inventory_segment) {
        Route::get($base_segment.$inventory_segment.'/index', 'index')->name('master_data.inventory.index');
        Route::get($base_segment.$inventory_segment.'/index_data_table', 'index_data_table')->name('master_data.inventory.index_data_table');
        Route::get($base_segment.$inventory_segment.'/add', 'add')->name('master_data.inventory.add');
        Route::get($base_segment.$inventory_segment.'/generate_transaction_number', 'generateTransactionNumber')->name('master_data.inventory.generate_transaction_number');
        Route::post($base_segment.$inventory_segment.'/save', 'save')->name('master_data.inventory.save');
        Route::get($base_segment.$inventory_segment.'/edit/{id}', 'edit')->name('master_data.inventory.edit');
        Route::post($base_segment.$inventory_segment.'/update', 'update')->name('master_data.inventory.update');
        Route::get($base_segment.$inventory_segment.'/detail/{id}', 'detail')->name('master_data.inventory.detail');
    });

    Route::controller(TagController::class)->group(function () use ($base_segment, $tag_segment) {
        Route::get($base_segment.$tag_segment.'/index', 'index')->name('master_data.tag.index');
        Route::get($base_segment.$tag_segment.'/index_data_table', 'index_data_table')->name('master_data.tag.index_data_table');
        Route::get($base_segment.$tag_segment.'/add', 'add')->name('master_data.tag.add');
        Route::post($base_segment.$tag_segment.'/save', 'save')->name('master_data.tag.save');
        Route::get($base_segment.$tag_segment.'/edit/{id}', 'edit')->name('master_data.tag.edit');
        Route::post($base_segment.$tag_segment.'/update', 'update')->name('master_data.tag.update');
        Route::get($base_segment.$tag_segment.'/detail/{id}', 'detail')->name('master_data.tag.detail');
    });

    Route::controller(TaxController::class)->group(function () use ($base_segment, $tax_segment) {
        Route::get($base_segment.$tax_segment.'/index', 'index')->name('master_data.tax.index');
        Route::get($base_segment.$tax_segment.'/index_data_table', 'index_data_table')->name('master_data.tax.index_data_table');
        Route::get($base_segment.$tax_segment.'/add', 'add')->name('master_data.tax.add');
        Route::post($base_segment.$tax_segment.'/save', 'save')->name('master_data.tax.save');
        Route::get($base_segment.$tax_segment.'/edit/{id}', 'edit')->name('master_data.tax.edit');
        Route::post($base_segment.$tax_segment.'/update', 'update')->name('master_data.tax.update');
        Route::get($base_segment.$tax_segment.'/detail/{id}', 'detail')->name('master_data.tax.detail');
        Route::get($base_segment.$tax_segment.'/export', 'export')->name('master_data.tax.export');
        Route::get($base_segment.$tax_segment.'/import', 'import')->name('master_data.tax.import');
    });

    Route::controller(UnitController::class)->group(function () use ($base_segment, $unit_segment) {
        Route::get($base_segment.$unit_segment.'/index', 'index')->name('master_data.unit.index');
        Route::get($base_segment.$unit_segment.'/index_data_table', 'index_data_table')->name('master_data.unit.index_data_table');
        Route::get($base_segment.$unit_segment.'/add', 'add')->name('master_data.unit.add');
        Route::post($base_segment.$unit_segment.'/save', 'save')->name('master_data.unit.save');
        Route::get($base_segment.$unit_segment.'/edit/{id}', 'edit')->name('master_data.unit.edit');
        Route::post($base_segment.$unit_segment.'/update', 'update')->name('master_data.unit.update');
        Route::get($base_segment.$unit_segment.'/detail/{id}', 'detail')->name('master_data.unit.detail');
        Route::get($base_segment.$unit_segment.'/export', 'export')->name('master_data.unit.export');
        Route::get($base_segment.$unit_segment.'/import', 'import')->name('master_data.unit.import');
    });

    Route::controller(CategoryController::class)->group(function () use ($base_segment, $category_segment) {
        Route::get($base_segment.$category_segment.'/index', 'index')->name('master_data.category.index');
        Route::get($base_segment.$category_segment.'/index_data_table', 'index_data_table')->name('master_data.category.index_data_table');
        Route::get($base_segment.$category_segment.'/add', 'add')->name('master_data.category.add');
        Route::post($base_segment.$category_segment.'/save', 'save')->name('master_data.category.save');
        Route::get($base_segment.$category_segment.'/edit/{id}', 'edit')->name('master_data.category.edit');
        Route::post($base_segment.$category_segment.'/update', 'update')->name('master_data.category.update');
        Route::get($base_segment.$category_segment.'/detail/{id}', 'detail')->name('master_data.category.detail');
        Route::get($base_segment.$category_segment.'/export', 'export')->name('master_data.category.export');
        Route::get($base_segment.$category_segment.'/import', 'import')->name('master_data.category.import');
    });

    Route::controller(SalesPersonController::class)->group(function () use ($base_segment, $sales_person_segment) {
        Route::get($base_segment.$sales_person_segment.'/index', 'index')->name('master_data.sales_person.index');
        Route::get($base_segment.$sales_person_segment.'/index_data_table', 'index_data_table')->name('master_data.sales_person.index_data_table');
        Route::get($base_segment.$sales_person_segment.'/add', 'add')->name('master_data.sales_person.add');
        Route::post($base_segment.$sales_person_segment.'/save', 'save')->name('master_data.sales_person.save');
        Route::get($base_segment.$sales_person_segment.'/edit/{id}', 'edit')->name('master_data.sales_person.edit');
        Route::post($base_segment.$sales_person_segment.'/update', 'update')->name('master_data.sales_person.update');
        Route::get($base_segment.$sales_person_segment.'/detail/{id}', 'detail')->name('master_data.sales_person.detail');
        Route::get($base_segment.$sales_person_segment.'/export', 'export')->name('master_data.sales_person.export');
        Route::get($base_segment.$sales_person_segment.'/import', 'import')->name('master_data.sales_person.import');
    });

    Route::controller(PaymentTermController::class)->group(function () use ($base_segment, $payment_term_segment) {
        Route::get($base_segment.$payment_term_segment.'/index', 'index')->name('master_data.payment_term.index');
        Route::get($base_segment.$payment_term_segment.'/index_data_table', 'indexDataTable')->name('master_data.payment_term.index_data_table');
        Route::get($base_segment.$payment_term_segment.'/add', 'add')->name('master_data.payment_term.add');
        Route::post($base_segment.$payment_term_segment.'/store', 'store')->name('master_data.payment_term.store');
        Route::get($base_segment.$payment_term_segment.'/edit/{id}', 'edit')->name('master_data.payment_term.edit');
        Route::put($base_segment.$payment_term_segment.'/update', 'update')->name('master_data.payment_term.update');
        Route::get($base_segment.$payment_term_segment.'/detail/{id}', 'detail')->name('master_data.payment_term.detail');
        Route::delete($base_segment.$payment_term_segment.'/delete', 'delete')->name('master_data.payment_term.delete');
        Route::get($base_segment.$payment_term_segment.'/export', 'export')->name('master_data.payment_term.export');
        Route::get($base_segment.$payment_term_segment.'/import', 'import')->name('master_data.payment_term.import');
    });

    // Transaction Number Routes
    Route::controller(TransactionNumberController::class)->group(function () use ($base_segment, $transaction_number_segment) {
        Route::get($base_segment.$transaction_number_segment.'/index', 'index')->name('master_data.transaction_number.index');
        Route::get($base_segment.$transaction_number_segment.'/index_data_table', 'indexDataTable')->name('master_data.transaction_number.index_data_table');
        Route::get($base_segment.$transaction_number_segment.'/detail/{id}', 'detail')->name('master_data.transaction_number.detail');
        Route::get($base_segment.$transaction_number_segment.'/edit/{id}', 'edit')->name('master_data.transaction_number.edit');
        Route::post($base_segment.$transaction_number_segment.'/update', 'update')->name('master_data.transaction_number.update');
        Route::post($base_segment.$transaction_number_segment.'/preview_format', 'previewFormat')->name('master_data.transaction_number.preview_format');
        Route::post($base_segment.$transaction_number_segment.'/generate_number/{module}', 'generateNumber')->name('master_data.transaction_number.generate_number');
        Route::post($base_segment.$transaction_number_segment.'/check_duplicate/{module}', 'checkDuplicate')->name('master_data.transaction_number.check_duplicate');
        Route::post($base_segment.$transaction_number_segment.'/reset_counter/{id}', 'resetCounter')->name('master_data.transaction_number.reset_counter');
    });

    // Revenue Split Routes
    Route::controller(RevenueSplitController::class)->group(function () use ($base_segment, $revenue_split_segment) {
        Route::get($base_segment.$revenue_split_segment, function() {
            return redirect()->route('master_data.revenue_split.index');
        });
        Route::get($base_segment.$revenue_split_segment.'/index', 'index')->name('master_data.revenue_split.index');
        Route::get($base_segment.$revenue_split_segment.'/index_data_table', 'index_data_table')->name('master_data.revenue_split.index_data_table');
        Route::get($base_segment.$revenue_split_segment.'/add', 'add')->name('master_data.revenue_split.add');
        Route::post($base_segment.$revenue_split_segment.'/save', 'save')->name('master_data.revenue_split.save');
        Route::get($base_segment.$revenue_split_segment.'/edit/{id}', 'edit')->name('master_data.revenue_split.edit');
        Route::post($base_segment.$revenue_split_segment.'/update', 'update')->name('master_data.revenue_split.update');
        Route::get($base_segment.$revenue_split_segment.'/detail/{id}', 'detail')->name('master_data.revenue_split.detail');
        Route::get($base_segment.$revenue_split_segment.'/preview/{id}', 'preview')->name('master_data.revenue_split.preview');
        Route::get($base_segment.$revenue_split_segment.'/audit_log_data_table/{id}', 'audit_log_data_table')->name('master_data.revenue_split.audit_log_data_table');
        Route::get($base_segment.$revenue_split_segment.'/export', 'export')->name('master_data.revenue_split.export');
        Route::get($base_segment.$revenue_split_segment.'/import', 'import')->name('master_data.revenue_split.import');
    });

    // Product Revenue Split Configuration Routes
    Route::controller(ProductRevenueSplitController::class)->group(function () use ($base_segment, $product_segment) {
        Route::get($base_segment.'/product_revenue_split/configurations', 'getAllConfigurations')->name('master_data.product_revenue_split.configurations');
        Route::get($base_segment.$product_segment.'/revenue-split/{productId}', 'getConfiguration')->name('master_data.product.revenue_split.get');
        Route::post($base_segment.$product_segment.'/revenue-split/{productId}', 'saveConfiguration')->name('master_data.product.revenue_split.save');
        Route::delete($base_segment.$product_segment.'/revenue-split/{productId}', 'deleteConfiguration')->name('master_data.product.revenue_split.delete');
        Route::post($base_segment.$product_segment.'/revenue-split/{productId}/calculate', 'calculateSplit')->name('master_data.product.revenue_split.calculate');
    });
});
