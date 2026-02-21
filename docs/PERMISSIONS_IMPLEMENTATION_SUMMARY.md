# ✅ Implementasi Spatie Permissions di Menu dan Buttons

Dokumentasi lengkap implementasi permission checks untuk menampilkan/menyembunyikan menu items dan action buttons.

---

## 📋 Status Implementasi

- ✅ Migration: `display_name` column ditambahkan ke permissions table
- ✅ Menu.blade.php: Permission checks ditambahkan ke semua menu items
- ✅ Dokumentasi: Lengkap dengan contoh praktis untuk buttons

---

## 📁 File-file Penting

### 1. **Menu dengan Permissions**
📄 [`resources/views/layouts/menu.blade.php`](../resources/views/layouts/menu.blade.php)

Menu sudah diupdate dengan permission checks:
- Dashboard & Reports
- Transaksi (Sales, Purchase, Expense)
- Finansial (Budget, Top Up)
- Operasional (Production, Packing, Stock, Returns)
- Master Data (Products, Contacts, Warehouse, Inventory)
- Reports & Financial
- System Settings (Tax, Unit, Categories, Users, Roles)

### 2. **Guide Praktis untuk Buttons**
📄 [`docs/BUTTONS_PERMISSIONS_IMPLEMENTATION.md`](./BUTTONS_PERMISSIONS_IMPLEMENTATION.md)

Panduan lengkap dengan contoh:
- Tombol CREATE/TAMBAH
- Tombol EDIT
- Tombol DELETE
- Import/Export buttons
- Bulk actions
- Form submission buttons

### 3. **Permission Migration**
📄 [`database/migrations/2026_02_21_100001_add_display_name_to_permissions_table.php`](../database/migrations/2026_02_21_100001_add_display_name_to_permissions_table.php)

Migration sudah dijalankan! ✅

---

## 🚀 Cara Menggunakan

### Permission Check di Menu

Menu sudah selesai diupdate dengan `@can()` directives. Setiap menu item akan:
- ✅ Hanya muncul jika user punya permission
- ✅ Parent menu hilang otomatis jika semua sub-menu tersembunyi

**Contoh struktur di menu.blade.php:**
```blade
@can('dashboard.view')
    <li class="menu-item">
        <a href="{{ route('dashboard.index') }}" class="menu-link">
            Dashboard
        </a>
    </li>
@endcan
```

### Permission Check di Buttons

Untuk setiap halaman yang punya tombol action, wrap tombol dengan `@can()`:

#### Index Page - Tombol Tambah
```blade
@can('product.create')
    <a href="{{ route('master_data.product.create') }}" class="btn btn-primary">
        <i class="ti tabler-plus"></i> Tambah
    </a>
@endcan
```

#### Table Row - Tombol Edit & Delete
```blade
@foreach($products as $product)
    <tr>
        <td>{{ $product->name }}</td>
        <td>
            @can('product.edit')
                <a href="{{ route('master_data.product.edit', $product->id) }}" 
                   class="btn btn-sm btn-warning">
                    <i class="ti tabler-pencil"></i>
                </a>
            @endcan
            
            @can('product.delete')
                <form method="POST" action="{{ route('master_data.product.destroy', $product->id) }}" 
                      style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" 
                            onclick="return confirm('Yakin?')">
                        <i class="ti tabler-trash"></i>
                    </button>
                </form>
            @endcan
        </td>
    </tr>
@endforeach
```

#### Form - Tombol Submit
```blade
<form action="{{ route('master_data.product.store') }}" method="POST">
    @csrf
    
    {{-- Form fields --}}
    <input type="text" name="name" class="form-control">
    
    {{-- Submit button dengan permission check --}}
    @can('product.create')
        <button type="submit" class="btn btn-primary">
            <i class="ti tabler-check"></i> Simpan
        </button>
    @endcan
    
    <a href="{{ route('master_data.product.index') }}" class="btn btn-secondary">Batal</a>
</form>
```

---

## 🔑 List Permission Lengkap

Gunakan permission ini untuk implement di tombol-tombol:

### Dashboard & Reporting
```
dashboard.view
financial_recording.view
report_journal.view
report_ledger.view
report_balance_sheet.view
report_profit_loss.view
```

### Sales Transactions
```
sales_order.view/create/edit/delete
sales_delivery.view/create/edit/delete
sales_invoice.view/create/edit/delete
return_management.view/create/edit/delete
```

### Purchase Transactions
```
purchase_order.view/create/edit/delete
purchase_delivery.view/create/edit/delete
purchase_invoice.view/create/edit/delete
```

### Financial & Operational
```
expense.view/create/edit/delete
budget.view/create/edit/delete
topup.view/create/edit/delete
production.view/create/edit/delete
packing.view/create/edit/delete
stock_adjustment.view/create/edit/delete
stock_opname.view/create/edit/delete
```

### Master Data
```
product.view/create/edit/delete/import/export
contact.view/create/edit/delete/import/export
warehouse.view/create/edit/delete/stock_transfer/import/export
inventory.view/create/edit/delete/dispose/depreciation
revenue_split.view/create/edit/delete
```

### Settings & Master Data
```
tax.view/create/edit/delete
unit.view/create/edit/delete
product_category.view/create/edit/delete
tag.view/create/edit/delete
sales_person.view/create/edit/delete
payment_term.view/create/edit/delete
transaction_number.view/create/edit/delete
budget_category.view/create/edit/delete
```

### Administration
```
user.view/create/edit/delete/import/export
role.view/create/edit/delete/manage_permissions
permission.manage
audit_log.view
settings.manage
```

---

## ⚙️ Setup Permissions untuk Users

### Via Artisan Tinker
```bash
php artisan tinker

# Assign permissions langsung ke user
> $user = App\Models\User::find(1);
> $user->givePermissionTo('product.create');
> $user->givePermissionTo('product.edit');

# Atau assign role (lebih praktis)
> $user->assignRole('admin'); // Akan dapat semua permission dari role admin
```

### Via Laravel Admin Package
Jika pakai admin yang support permissions, setup di:
- User Management → Edit User → Assign Permissions/Roles

---

## 🎯 Implementation Checklist

Untuk implementasi permissions di setiap module:

### [ ] Dashboard
- [ ] Add `@can('dashboard.view')` to menu item
- [ ] Add permission checks to dashboard widgets/buttons

### [ ] Master Data (Product, Contact, Warehouse, etc)
- [ ] Add `@can('module.view')` to menu item
- [ ] Add `@can('module.create')` to Create button
- [ ] Add `@can('module.edit')` to Edit button  
- [ ] Add `@can('module.delete')` to Delete button
- [ ] Add `@can('module.import')` to Import button (if available)
- [ ] Add `@can('module.export')` to Export button (if available)

### [ ] Transactions (Sales, Purchase, etc)
- [ ] Add `@can('module.view')` to menu
- [ ] Add permission checks to Index page actions
- [ ] Add permission checks to Show/Detail page actions
- [ ] Add permission checks to Create/Edit form
- [ ] Check permissions in Controller methods

### [ ] Administration
- [ ] Add `@can('user.view')` to Users menu
- [ ] Add `@can('role.view')` to Roles menu
- [ ] Add permission checks to users/roles management pages
- [ ] Add `@can('audit_log.view')` to Audit Log menu

---

## 🛡️ Security Best Practices

### 1. Always Check on Backend (Not Just UI)
```php
// Di Controller - jangan hanya di UI
public function edit($id)
{
    if (!auth()->user()->hasPermissionTo('product.edit')) {
        abort(403, 'Unauthorized');
    }
    
    $product = Product::findOrFail($id);
    return view('master_data.product.edit', compact('product'));
}
```

### 2. Use Method Authorization (Routes)
```php
Route::resource('products', 'ProductController')
    ->middleware('can:product.view'); // Untuk resource

Route::post('/products', 'ProductController@store')
    ->middleware('can:product.create');
```

### 3. Clear Cache After Changing Permissions
```bash
php artisan cache:forget spatie.permission.cache
```

### 4. Test Permission in Local Dev
```bash
php artisan tinker
> Auth::user()->givePermissionTo('product.create')
```

---

## 📖 Dokumentasi Referensi

### Blade Helpers untuk Permission Check
```blade
{{-- Check single permission --}}
@can('product.edit')
    {{-- Visible if user has permission --}}
@endcan

{{-- Check if not have permission --}}
@cannot('product.delete')
    {{-- Visible if user does NOT have permission --}}
@endcannot

{{-- Check multiple permissions (OR) --}}
@canany(['product.create', 'product.edit'])
    {{-- Visible if user has any of these --}}
@endcanany

{{-- In PHP code --}}
@if(Auth::user()->hasPermissionTo('product.view'))
    Punya akses
@endif

{{-- Multiple checks --}}
@if(Auth::user()->hasAnyPermission(['product.create', 'product.edit']))
    Punya akses create atau edit
@endif
```

### Controller Usage
```php
// Check permission
auth()->user()->hasPermissionTo('product.edit');

// Get user permissions
$permissions = auth()->user()->permissions;

// Get user roles
$roles = auth()->user()->roles;

// Check role
auth()->user()->hasRole('admin');
```

---

## 🔧 Troubleshooting

### Menu Item Tidak Muncul Meski Punya Permission
- [ ] Clear cache: `php artisan cache:clear`
- [ ] Check permission name di DB: `php artisan tinker` → `Permission::all()`
- [ ] Check user permission: `Auth::user()->hasPermissionTo('permission.name')`

### Permission Cache Not Updated
```php
// Clear permission cache
app()['cache']->store(config('permission.cache.store'))
    ->forget(config('permission.cache.key'));
```

### Permission String Tidak Match
- Permission names are case-sensitive
- Gunakan format: `module.action` (lowercase with dot)
- Contoh: ✅ `product.create` ❌ `Product.Create` ❌ `Product Create`

---

## 📝 Next Steps

### 1. Review Menu Implementation
Sudah selesai! Menu item akan muncul/hilang berdasarkan permission.

### 2. Implement Buttons Permission
Gunakan guide di [BUTTONS_PERMISSIONS_IMPLEMENTATION.md](./BUTTONS_PERMISSIONS_IMPLEMENTATION.md) untuk:
- [ ] Product Management
- [ ] Contact Management
- [ ] Warehouse Management
- [ ] Sales Transactions
- [ ] Purchase Transactions
- [ ] Financial Modules
- [ ] Admin Modules

### 3. Backend Security
Tambahkan permission checks di setiap controller:
```php
// Di store method
if (!auth()->user()->hasPermissionTo('product.create')) {
    abort(403);
}

// Di update method  
if (!auth()->user()->hasPermissionTo('product.edit')) {
    abort(403);
}

// Di delete method
if (!auth()->user()->hasPermissionTo('product.delete')) {
    abort(403);
}
```

### 4. Test All Scenarios
- [ ] Login sebagai super_admin → Semua menu muncul
- [ ] Login sebagai admin → Sesuai role
- [ ] Login sebagai manager → Sesuai permission
- [ ] Login sebagai user → Minimal dashboard

---

## 📚 Tips for Faster Implementation

### 1. Copy-Paste Template
Gunakan code snippet di BUTTONS_PERMISSIONS_IMPLEMENTATION.md untuk copy-paste ke view Anda.

### 2. Create Component (Optional)
Jika banyak tombol sama, buat Blade component:
```blade
<x-action-buttons :model="$product" routePrefix="master_data.product" />
```

### 3. Use VS Code Snippet
Buat snippet untuk `@can()` untuk faster typing:
```
Ketik: can
Hasilnya: @can('permission.name')  @endcan
```

### 4. Batch Update dengan Search & Replace
- Find: `<a href=".*" class="btn btn-sm btn-warning">`
- Replace: wrap dengan `@can() ... @endcan`

---

## 🎓 Learning Resources

- [Spatie Permission Docs](https://spatie.be/docs/laravel-permission/v6/introduction)
- [Laravel Authorization](https://laravel.com/docs/authorization)
- [Blade Helpers Guide](https://laravel.com/docs/blade)

---

**✅ Implementasi sudah siap! Next step: Tambahkan permission checks di buttons di setiap halaman.** 

Untuk bantuan lebih lanjut atau pertanyaan, refer ke dokumentasi di folder `/docs`:
- `BUTTONS_PERMISSIONS_IMPLEMENTATION.md` - Panduan praktis buttons
- `PERMISSIONS_MENU_BUTTONS_GUIDE.md` - Guide lengkap menu & buttons implementation
