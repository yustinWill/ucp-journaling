<?php

use App\Http\Middleware\EnsureTokenIsValid;
use Illuminate\Support\Facades\Route;

//Controllers
use App\Http\Controllers\MasterData\ContactController;
use App\Http\Controllers\MasterData\StorageController;
use App\Http\Controllers\MasterData\ProductController;
use App\Http\Controllers\MasterData\CoaController;
use App\Http\Controllers\MasterData\UserController;
use App\Http\Controllers\MasterData\RoleController;

$base_segment = '/master_data';
$contact_segment = '/contact';
$storage_segment = '/gudang';
$product_segment = '/product';
$coa_segment = '/coa';
$user_segment = '/user';
$role_segment = '/role';

Route::middleware([EnsureTokenIsValid::class])->group(function () use ($base_segment, $contact_segment, $storage_segment, $product_segment, $coa_segment, $user_segment, $role_segment) {
    Route::controller(ContactController::class)->group(function () use ($base_segment, $contact_segment) {
        Route::get($base_segment . $contact_segment . '/index', 'index')->name('master_data.contact.index')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $contact_segment . '/index_data_table', 'index_data_table')->name('master_data.contact.index_data_table')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $contact_segment . '/add', 'add')->name('master_data.contact.add')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $contact_segment . '/edit/{id}', 'edit')->name('master_data.contact.edit')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $contact_segment . '/detail/{id}', 'detail')->name('master_data.contact.detail')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $contact_segment . '/detail_profil_data/{id}', 'getProfilData')->name('master_data.contact.detail_profil_data')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $contact_segment . '/detail_transaction_data/{id}', 'getTransactionData')->name('master_data.contact.detail_transaction_data')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $contact_segment . '/detail_credit_memo_data/{id}', 'getCreditMemoData')->name('master_data.contact.detail_credit_memo_data')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $contact_segment . '/export', 'export')->name('master_data.contact.export')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $contact_segment . '/import', 'import')->name('master_data.contact.import')->middleware(EnsureTokenIsValid::class);
    });

    Route::controller(StorageController::class)->group(function () use ($base_segment, $storage_segment) {
        Route::get($base_segment . $storage_segment . '/index', 'index')->name('master_data.storage.index')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $storage_segment . '/index_data_table', 'index_data_table')->name('master_data.storage.index_data_table')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $storage_segment . '/add', 'add')->name('master_data.storage.add')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $storage_segment . '/detail', 'detail')->name('master_data.storage.detail')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $storage_segment . '/detail_product_data_table', 'detail_product_data_table')->name('master_data.storage.detail_product_data_table')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $storage_segment . '/detail_transaction_data_table', 'detail_transaction_data_table')->name('master_data.storage.detail_transaction_data_table')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $storage_segment . '/detail_batch_data_table', 'detail_batch_data_table')->name('master_data.storage.detail_batch_data_table')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $storage_segment . '/export', 'export')->name('master_data.storage.export')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $storage_segment . '/import', 'import')->name('master_data.storage.import')->middleware(EnsureTokenIsValid::class);
    });

    Route::controller(ProductController::class)->group(function () use ($base_segment, $product_segment) {
        Route::get($base_segment . $product_segment . '/index', 'index')->name('master_data.product.index')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $product_segment . '/index_data_table', 'index_data_table')->name('master_data.product.index_data_table')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $product_segment . '/index_warehouse_data_table', 'index_warehouse_data_table')->name('master_data.product.index_warehouse_data_table')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $product_segment . '/add', 'add')->name('master_data.product.add')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $product_segment . '/export', 'export')->name('master_data.product.export')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $product_segment . '/import', 'import')->name('master_data.product.import')->middleware(EnsureTokenIsValid::class);
    });

    Route::controller(CoaController::class)->group(function () use ($base_segment, $coa_segment) {
        Route::get($base_segment . $coa_segment . '/index', 'index')->name('master_data.coa.index')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $coa_segment . '/index_data_table', 'index_data_table')->name('master_data.coa.index_data_table')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $coa_segment . '/add', 'add')->name('master_data.coa.add')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $coa_segment . '/export', 'export')->name('master_data.coa.export')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $coa_segment . '/import', 'import')->name('master_data.coa.import')->middleware(EnsureTokenIsValid::class);
    });

    Route::controller(UserController::class)->group(function () use ($base_segment, $user_segment) {
        Route::get($base_segment . $user_segment . '/index', 'index')->name('master_data.user.index')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $user_segment . '/index_data_table', 'index_data_table')->name('master_data.user.index_data_table')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $user_segment . '/add', 'add')->name('master_data.user.add')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $user_segment . '/edit/{id}', 'edit')->name('master_data.user.edit')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $user_segment . '/detail/{id}', 'detail')->name('master_data.user.detail')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $user_segment . '/export', 'export')->name('master_data.user.export')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $user_segment . '/import', 'import')->name('master_data.user.import')->middleware(EnsureTokenIsValid::class);
    });

    Route::controller(RoleController::class)->group(function () use ($base_segment, $role_segment) {
        Route::get($base_segment . $role_segment . '/index', 'index')->name('master_data.role.index')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $role_segment . '/index_data_table', 'index_data_table')->name('master_data.role.index_data_table')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $role_segment . '/add', 'add')->name('master_data.role.add')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $role_segment . '/edit/{id}', 'edit')->name('master_data.role.edit')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $role_segment . '/detail/{id}', 'detail')->name('master_data.role.detail')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $role_segment . '/export', 'export')->name('master_data.role.export')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $role_segment . '/import', 'import')->name('master_data.role.import')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $role_segment . '/permissions/{id}', 'getRolePermissions')->name('master_data.role.permissions')->middleware(EnsureTokenIsValid::class);
        Route::get($base_segment . $role_segment . '/users/{id}', 'getUsersByRole')->name('master_data.role.users')->middleware(EnsureTokenIsValid::class);
        Route::post($base_segment . $role_segment . '/store', 'store')->name('master_data.role.store')->middleware(EnsureTokenIsValid::class);
        Route::put($base_segment . $role_segment . '/update/{id}', 'update')->name('master_data.role.update')->middleware(EnsureTokenIsValid::class);
        Route::delete($base_segment . $role_segment . '/delete/{id}', 'destroy')->name('master_data.role.destroy')->middleware(EnsureTokenIsValid::class);
    });
});
