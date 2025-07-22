<?php

use Illuminate\Support\Facades\Route;

//Controllers
use App\Http\Controllers\MasterData\ContactController;
use App\Http\Controllers\MasterData\StorageController;
use App\Http\Controllers\MasterData\ProductController;
use App\Http\Controllers\MasterData\CoaController;
use App\Http\Controllers\MasterData\UserController;
use App\Http\Controllers\MasterData\RoleController;
use App\Http\Controllers\MasterData\StockOpnameController;
use App\Http\Controllers\MasterData\TagController;
use App\Http\Controllers\MasterData\TaxController;
use App\Http\Controllers\MasterData\UnitController;
use App\Http\Controllers\MasterData\CategoryController;

$base_segment = '/master_data';
$contact_segment = '/contact';
$storage_segment = '/gudang';
$product_segment = '/product';
$coa_segment = '/coa';
$user_segment = '/user';
$role_segment = '/role';
$stock_opname_segment = '/stock_opname';
$tag_segment = '/tag';
$tax_segment = '/tax';
$unit_segment = '/unit';
$category_segment = '/category';

Route::middleware(['auth'])->group(function () use ($base_segment, $contact_segment, $storage_segment, $product_segment, $coa_segment, $user_segment, $role_segment, $stock_opname_segment, $tag_segment, $tax_segment, $unit_segment, $category_segment) {
    Route::controller(ContactController::class)->group(function () use ($base_segment, $contact_segment) {
        Route::get($base_segment . $contact_segment . '/index', 'index')->name('master_data.contact.index');
        Route::get($base_segment . $contact_segment . '/index_data_table', 'index_data_table')->name('master_data.contact.index_data_table');
        Route::get($base_segment . $contact_segment . '/add', 'add')->name('master_data.contact.add');
        Route::get($base_segment . $contact_segment . '/edit/{id}', 'edit')->name('master_data.contact.edit');
        Route::get($base_segment . $contact_segment . '/detail/{id}', 'detail')->name('master_data.contact.detail');
        Route::get($base_segment . $contact_segment . '/detail_profil_data/{id}', 'getProfilData')->name('master_data.contact.detail_profil_data');
        Route::get($base_segment . $contact_segment . '/detail_transaction_data/{id}', 'getTransactionData')->name('master_data.contact.detail_transaction_data');
        Route::get($base_segment . $contact_segment . '/detail_credit_memo_data/{id}', 'getCreditMemoData')->name('master_data.contact.detail_credit_memo_data');
        Route::get($base_segment . $contact_segment . '/export', 'export')->name('master_data.contact.export');
        Route::get($base_segment . $contact_segment . '/import', 'import')->name('master_data.contact.import');
    });

    Route::controller(StorageController::class)->group(function () use ($base_segment, $storage_segment) {
        Route::get($base_segment . $storage_segment . '/index', 'index')->name('master_data.storage.index');
        Route::get($base_segment . $storage_segment . '/index_data_table', 'index_data_table')->name('master_data.storage.index_data_table');
        Route::get($base_segment . $storage_segment . '/add', 'add')->name('master_data.storage.add');
        Route::get($base_segment . $storage_segment . '/edit/{id}', 'edit')->name('master_data.storage.edit');
        Route::get($base_segment . $storage_segment . '/detail/{id}', 'detail')->name('master_data.storage.detail');
        Route::get($base_segment . $storage_segment . '/detail_product_data_table', 'detail_product_data_table')->name('master_data.storage.detail_product_data_table');
        Route::get($base_segment . $storage_segment . '/detail_transaction_data_table', 'detail_transaction_data_table')->name('master_data.storage.detail_transaction_data_table');
        Route::get($base_segment . $storage_segment . '/detail_batch_data_table', 'detail_batch_data_table')->name('master_data.storage.detail_batch_data_table');
        Route::get($base_segment . $storage_segment . '/export', 'export')->name('master_data.storage.export');
        Route::get($base_segment . $storage_segment . '/import', 'import')->name('master_data.storage.import');
    });

    Route::controller(ProductController::class)->group(function () use ($base_segment, $product_segment) {
        Route::get($base_segment . $product_segment . '/index', 'index')->name('master_data.product.index');
        Route::get($base_segment . $product_segment . '/index_data_table', 'index_data_table')->name('master_data.product.index_data_table');
        Route::get($base_segment . $product_segment . '/index_warehouse_data_table', 'index_warehouse_data_table')->name('master_data.product.index_warehouse_data_table');
        Route::get($base_segment . $product_segment . '/add', 'add')->name('master_data.product.add');
        Route::get($base_segment . $product_segment . '/detail/{id}', 'detail')->name('master_data.product.detail');
        Route::get($base_segment . $product_segment . '/edit/{id}', 'edit')->name('master_data.product.edit');
        Route::get($base_segment . $product_segment . '/export', 'export')->name('master_data.product.export');
        Route::get($base_segment . $product_segment . '/import', 'import')->name('master_data.product.import');
    });

    Route::controller(CoaController::class)->group(function () use ($base_segment, $coa_segment) {
        Route::get($base_segment . $coa_segment . '/index', 'index')->name('master_data.coa.index');
        Route::get($base_segment . $coa_segment . '/index_data_table', 'index_data_table')->name('master_data.coa.index_data_table');
        Route::get($base_segment . $coa_segment . '/add', 'add')->name('master_data.coa.add');
        Route::get($base_segment . $coa_segment . '/detail/{id}', 'detail')->name('master_data.coa.detail');
        Route::get($base_segment . $coa_segment . '/edit/{id}', 'edit')->name('master_data.coa.edit');
        Route::get($base_segment . $coa_segment . '/export', 'export')->name('master_data.coa.export');
        Route::get($base_segment . $coa_segment . '/import', 'import')->name('master_data.coa.import');
    });

    Route::controller(UserController::class)->group(function () use ($base_segment, $user_segment) {
        Route::get($base_segment . $user_segment . '/index', 'index')->name('master_data.user.index');
        Route::get($base_segment . $user_segment . '/index_data_table', 'index_data_table')->name('master_data.user.index_data_table');
        Route::get($base_segment . $user_segment . '/add', 'add')->name('master_data.user.add');
        Route::get($base_segment . $user_segment . '/edit/{id}', 'edit')->name('master_data.user.edit');
        Route::get($base_segment . $user_segment . '/detail/{id}', 'detail')->name('master_data.user.detail');
        Route::get($base_segment . $user_segment . '/export', 'export')->name('master_data.user.export');
        Route::get($base_segment . $user_segment . '/import', 'import')->name('master_data.user.import');
    });

    Route::controller(RoleController::class)->group(function () use ($base_segment, $role_segment) {
        Route::get($base_segment . $role_segment . '/index', 'index')->name('master_data.role.index');
        Route::get($base_segment . $role_segment . '/index_data_table', 'index_data_table')->name('master_data.role.index_data_table');
        Route::get($base_segment . $role_segment . '/add', 'add')->name('master_data.role.add');
        Route::get($base_segment . $role_segment . '/edit/{id}', 'edit')->name('master_data.role.edit');
        Route::get($base_segment . $role_segment . '/detail/{id}', 'detail')->name('master_data.role.detail');
        Route::get($base_segment . $role_segment . '/export', 'export')->name('master_data.role.export');
        Route::get($base_segment . $role_segment . '/import', 'import')->name('master_data.role.import');
        Route::get($base_segment . $role_segment . '/permissions/{id}', 'getRolePermissions')->name('master_data.role.permissions');
        Route::get($base_segment . $role_segment . '/users/{id}', 'getUsersByRole')->name('master_data.role.users');
        Route::post($base_segment . $role_segment . '/store', 'store')->name('master_data.role.store');
        Route::put($base_segment . $role_segment . '/update/{id}', 'update')->name('master_data.role.update');
        Route::delete($base_segment . $role_segment . '/delete/{id}', 'destroy')->name('master_data.role.destroy');
    });

    Route::controller(StockOpnameController::class)->group(function () use ($base_segment, $stock_opname_segment) {
        Route::get($base_segment . $stock_opname_segment . '/index', 'index')->name('master_data.stock_opname.index');
        Route::get($base_segment . $stock_opname_segment . '/index_data_table', 'index_data_table')->name('master_data.stock_opname.index_data_table');
        Route::get($base_segment . $stock_opname_segment . '/add', 'add')->name('master_data.stock_opname.add');
        Route::get($base_segment . $stock_opname_segment . '/edit/{id}', 'edit')->name('master_data.stock_opname.edit');
        Route::get($base_segment . $stock_opname_segment . '/detail/{id}', 'detail')->name('master_data.stock_opname.detail');
        
        // Import routes
        Route::get($base_segment . $stock_opname_segment . '/import', 'import')->name('master_data.stock_opname.import');
        Route::get($base_segment . $stock_opname_segment . '/download-template', 'downloadTemplate')->name('master_data.stock_opname.download_template');
        Route::post($base_segment . $stock_opname_segment . '/validate-import', 'validateImport')->name('master_data.stock_opname.validate_import');
        Route::post($base_segment . $stock_opname_segment . '/process-import', 'processImport')->name('master_data.stock_opname.process_import');
        
        // Export routes
        Route::get($base_segment . $stock_opname_segment . '/export', 'export')->name('master_data.stock_opname.export');
        Route::post($base_segment . $stock_opname_segment . '/process-export', 'processExport')->name('master_data.stock_opname.process_export');
        
        Route::post($base_segment . $stock_opname_segment . '/create_adjustment/{id}', 'createAdjustment')->name('master_data.stock_opname.create_adjustment');
    });

    Route::controller(TagController::class)->group(function () use ($base_segment, $tag_segment) {
        Route::get($base_segment . $tag_segment . '/index', 'index')->name('master_data.tag.index');
        Route::get($base_segment . $tag_segment . '/index_data_table', 'index_data_table')->name('master_data.tag.index_data_table');
        Route::get($base_segment . $tag_segment . '/add', 'add')->name('master_data.tag.add');
        Route::post($base_segment . $tag_segment . '/save', 'save')->name('master_data.tag.save');
        Route::get($base_segment . $tag_segment . '/edit/{id}', 'edit')->name('master_data.tag.edit');
        Route::get($base_segment . $tag_segment . '/detail/{id}', 'detail')->name('master_data.tag.detail');
    });

    Route::controller(TaxController::class)->group(function () use ($base_segment, $tax_segment) {
        Route::get($base_segment . $tax_segment . '/index', 'index')->name('master_data.tax.index');
        Route::get($base_segment . $tax_segment . '/index_data_table', 'index_data_table')->name('master_data.tax.index_data_table');
        Route::get($base_segment . $tax_segment . '/add', 'add')->name('master_data.tax.add');
        Route::get($base_segment . $tax_segment . '/edit/{id}', 'edit')->name('master_data.tax.edit');
        Route::get($base_segment . $tax_segment . '/detail/{id}', 'detail')->name('master_data.tax.detail');
        Route::get($base_segment . $tax_segment . '/export', 'export')->name('master_data.tax.export');
        Route::get($base_segment . $tax_segment . '/import', 'import')->name('master_data.tax.import');
    });

    Route::controller(UnitController::class)->group(function () use ($base_segment, $unit_segment) {
        Route::get($base_segment . $unit_segment . '/index', 'index')->name('master_data.unit.index');
        Route::get($base_segment . $unit_segment . '/index_data_table', 'index_data_table')->name('master_data.unit.index_data_table');
        Route::get($base_segment . $unit_segment . '/add', 'add')->name('master_data.unit.add');
        Route::get($base_segment . $unit_segment . '/edit/{id}', 'edit')->name('master_data.unit.edit');
        Route::get($base_segment . $unit_segment . '/detail/{id}', 'detail')->name('master_data.unit.detail');
        Route::get($base_segment . $unit_segment . '/export', 'export')->name('master_data.unit.export');
        Route::get($base_segment . $unit_segment . '/import', 'import')->name('master_data.unit.import');
    });

    Route::controller(CategoryController::class)->group(function () use ($base_segment, $category_segment) {
        Route::get($base_segment . $category_segment . '/index', 'index')->name('master_data.category.index');
        Route::get($base_segment . $category_segment . '/index_data_table', 'index_data_table')->name('master_data.category.index_data_table');
        Route::get($base_segment . $category_segment . '/add', 'add')->name('master_data.category.add');
        Route::get($base_segment . $category_segment . '/edit/{id}', 'edit')->name('master_data.category.edit');
        Route::get($base_segment . $category_segment . '/detail/{id}', 'detail')->name('master_data.category.detail');
        Route::get($base_segment . $category_segment . '/export', 'export')->name('master_data.category.export');
        Route::get($base_segment . $category_segment . '/import', 'import')->name('master_data.category.import');
    });
});
