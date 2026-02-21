# Implementasi Permissions di Menu dan Buttons

## 1. Permission Check di Menu (menu.blade.php)

Gunakan `@can()` directive untuk menampilkan/menyembunyikan menu items berdasarkan permissions.

### Contoh 1: Menu Item Sederhana
```blade
@can('dashboard.view')
    <li class="menu-item @if (request()->routeIs(['dashboard.index'])) active @endif">
        <a href="{{ route('dashboard.index') }}" class="menu-link">
            <i class="menu-icon icon-base ti tabler-home"></i>
            <div data-i18n="Dashboard">Dashboard</div>
        </a>
    </li>
@endcan
```

### Contoh 2: Menu Dropdown Group
```blade
@if(Auth::check() && (Auth::user()->hasPermissionTo('sales_order.view') || Auth::user()->hasPermissionTo('sales_delivery.view') || Auth::user()->hasPermissionTo('sales_invoice.view')))
    <li class="menu-item">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon icon-base ti tabler-cash-register"></i>
            <div data-i18n="Chat">Penjualan</div>
        </a>
        <ul class="menu-sub">
            @can('sales_order.view')
                <li class="menu-item">
                    <a href="{{ route('main_transaction.sales_order.index') }}" class="menu-link">
                        <div data-i18n="Chat">Sales Order</div>
                    </a>
                </li>
            @endcan
            @can('sales_delivery.view')
                <li class="menu-item">
                    <a href="{{ route('main_transaction.sales_delivery.index') }}" class="menu-link">
                        <div data-i18n="Chat">Sales Delivery</div>
                    </a>
                </li>
            @endcan
        </ul>
    </li>
@endif
```

**Penjelasan:**
- Wrapper `@if(Auth::check() && (...))` memastikan header menu hanya muncul jika minimal ada 1 sub-menu yang bisa diakses
- Setiap sub-menu dibungkus dengan `@can('permission.view')`

## 2. Permission Checks di Buttons (CRUD)

### Contoh: Index Page dengan Tombol Create
```blade
<div class="d-flex justify-content-between mb-3">
    <h5>Daftar Produk</h5>
    @can('product.create')
        <a href="{{ route('master_data.product.create') }}" class="btn btn-primary">
            <i class="ti tabler-plus"></i> Tambah Produk
        </a>
    @endcan
</div>

<table class="table">
    <tbody>
        @foreach($products as $product)
            <tr>
                <td>{{ $product->name }}</td>
                <td>
                    <div class="d-flex gap-2">
                        @can('product.edit')
                            <a href="{{ route('master_data.product.edit', $product->id) }}" class="btn btn-sm btn-warning">
                                <i class="ti tabler-pencil"></i>
                            </a>
                        @endcan
                        @can('product.delete')
                            <form method="POST" action="{{ route('master_data.product.destroy', $product->id) }}" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin?')">
                                    <i class="ti tabler-trash"></i>
                                </button>
                            </form>
                        @endcan
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
```

### Contoh: Form dengan Tombol Submit
```blade
<form action="{{ route('master_data.product.store') }}" method="POST">
    @csrf
    <div class="form-group mb-3">
        <label>Nama Produk</label>
        <input type="text" name="name" class="form-control" required>
    </div>
    
    @can('product.create')
        <button type="submit" class="btn btn-primary">
            <i class="ti tabler-check"></i> Simpan
        </button>
    @endcan
</form>
```

### Contoh: Edit Page dengan Permissions
```blade
{{-- Edit Button hanya muncul di detail page jika user punya permission --}}
@can('product.edit')
    <div class="mb-3">
        <a href="{{ route('master_data.product.edit', $product->id) }}" class="btn btn-warning">
            <i class="ti tabler-pencil"></i> Edit
        </a>
    </div>
@endcan

{{-- Delete Button --}}
@can('product.delete')
    <form method="POST" action="{{ route('master_data.product.destroy', $product->id) }}" style="display:inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin akan dihapus?')">
            <i class="ti tabler-trash"></i> Hapus
        </button>
    </form>
@endcan
```

## 3. Struktur Permission untuk CRUD

Untuk setiap module, gunakan struktur permission ini:
```
module.view    - Akses lihat/read
module.create  - Akses tambah
module.edit    - Akses ubah
module.delete  - Akses hapus
module.import  - Akses import (jika ada)
module.export  - Akses export (jika ada)
```

Contoh untuk Sales Order:
```
sales_order.view    - Lihat daftar & detail
sales_order.create  - Buat sales order baru
sales_order.edit    - Ubah sales order
sales_order.delete  - Hapus sales order
```

## 4. Helper Blade: Cek Multiple Permissions

Untuk cek multiple permissions (OR logic):
```blade
@if(Auth::user()->hasPermissionTo('product.view') || Auth::user()->hasPermissionTo('contact.view'))
    {{-- Tampilkan sesuatu --}}
@endif
```

Atau menggunakan helper:
```blade
@if(Auth::user()->hasAnyPermission(['product.view', 'contact.view']))
    {{-- Tampilkan sesuatu --}}
@endif
```

## 5. Controller Side: Policy & Authorization

Anda juga bisa check di controller:

```php
public function edit($id)
{
    // Check permission di controller
    if (!auth()->user()->hasPermissionTo('product.edit')) {
        abort(403, 'Unauthorized');
    }
    
    $product = Product::find($id);
    return view('master_data.product.edit', compact('product'));
}
```

Atau lebih elegan dengan middleware:

```php
Route::group(['middleware' => 'can:product.edit'], function () {
    Route::post('/product/{id}/update', 'ProductController@update');
});
```

## 6. Permission List untuk Reference

Dashboard & Reports:
- `dashboard.view` - Lihat dashboard
- `financial_recording.view` - Lihat pencatatan keuangan

Transaksi (Sales):
- `sales_order.view`, `sales_order.create`, `sales_order.edit`, `sales_order.delete`
- `sales_delivery.view`, `sales_delivery.create`, `sales_delivery.edit`, `sales_delivery.delete`
- `sales_invoice.view`, `sales_invoice.create`, `sales_invoice.edit`, `sales_invoice.delete`

Transaksi (Purchase):
- `purchase_order.view`, `purchase_order.create`, `purchase_order.edit`, `purchase_order.delete`
- `purchase_delivery.view`, `purchase_delivery.create`, `purchase_delivery.edit`, `purchase_delivery.delete`
- `purchase_invoice.view`, `purchase_invoice.create`, `purchase_invoice.edit`, `purchase_invoice.delete`

Transaksi (Lainnya):
- `expense.view`, `expense.create`, `expense.edit`, `expense.delete`
- `production.view`, `production.create`, `production.edit`, `production.delete`
- `packing.view`, `packing.create`, `packing.edit`, `packing.delete`
- `topup.view`, `topup.create`, `topup.edit`, `topup.delete`

Master Data:
- `product.view/create/edit/delete/import/export`
- `contact.view/create/edit/delete/import/export`
- `warehouse.view/create/edit/delete`
- `tax.view/create/edit/delete`
- `unit.view/create/edit/delete`
- `product_category.view/create/edit/delete`
- `tag.view/create/edit/delete`
- `sales_person.view/create/edit/delete`
- `payment_term.view/create/edit/delete`
- `transaction_number.view/create/edit/delete`

Admin:
- `user.view/create/edit/delete/import/export`
- `role.view/create/edit/delete`
- `role.manage_permissions` - Manage role permissions
- `permission.manage` - Manage permissions
- `audit_log.view` - Lihat audit log
- `settings.manage` - Manage settings

## 7. Assign Permissions ke Role

Di controller atau artisan command:

```php
use Spatie\Permission\Models\Role;

$role = Role::findByName('admin');
$role->givePermissionTo(['dashboard.view', 'product.view', 'product.create']);

// atau
$user->assignRole('admin'); // User akan memiliki semua permission dari role admin
```

## 8. Cek Permission di Blade

```blade
{{-- Simple check --}}
@can('product.edit')
    Tombol edit terlihat
@endcan

{{-- Negation --}}
@cannot('product.delete')
    User tidak bisa delete
@endcannot

{{-- Multiple permissions (OR) --}}
@canany(['product.create', 'product.edit'])
    User bisa create atau edit
@endcanany
```

## Catatan Penting

1. **Always Check on Backend**: Selalu validate permissions di backend juga, jangan hanya di UI
2. **User Experience**: Hide buttons/menus jika user tidak punya akses, jangan show error
3. **Permission Naming**: Gunakan format `module.action` untuk konsistensi
4. **Caching**: Spatie permission di-cache, clear cache jika ubah permissions:
   ```php
   php artisan cache:forget spatie.permission.cache
   ```
