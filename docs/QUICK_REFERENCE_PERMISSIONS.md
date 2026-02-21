# Quick Reference - Implementasi Permission Checks

## 📋 Checklist Cepat

### ✅ Sudah Selesai
- ✅ Migration `display_name` added to permissions table
- ✅ Menu.blade.php updated dengan `@can()` directives
- ✅ Dokumentasi lengkap tersedia

### 🔄 Perlu Dilakukan
- [ ] Add permission checks ke tombol di setiap module
- [ ] Backend validation di controller

---

## 🎯 Implementasi Cepat di View

### Format Tombol dengan Permission

```blade
@can('permission.name')
    <button>Aksi</button>
@endcan
```

### Pattern Lengkap (Copy & Paste)

#### 1️⃣ Index Page - Tombol Tambah
```blade
<div class="d-flex justify-content-between">
    <h5>Daftar Produk</h5>
    @can('product.create')
        <a href="{{ route('master_data.product.create') }}" class="btn btn-primary">
            <i class="ti tabler-plus"></i> Tambah
        </a>
    @endcan
</div>
```

#### 2️⃣ Table Row - Edit & Delete
```blade
<td>
    @can('product.edit')
        <a href="{{ route('master_data.product.edit', $product->id) }}" class="btn btn-sm btn-warning">
            <i class="ti tabler-pencil"></i>
        </a>
    @endcan
    @can('product.delete')
        <form method="POST" action="{{ route('master_data.product.destroy', $product->id) }}" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus?')">
                <i class="ti tabler-trash"></i>
            </button>
        </form>
    @endcan
</td>
```

#### 3️⃣ Form - Tombol Submit
```blade
@can('product.create')
    <button type="submit" class="btn btn-primary">
        <i class="ti tabler-check"></i> Simpan
    </button>
@endcan
<a href="{{ route('master_data.product.index') }}" class="btn btn-secondary">Batal</a>
```

#### 4️⃣ Detail Page - Edit & Delete
```blade
<div class="btn-group">
    @can('product.edit')
        <a href="{{ route('master_data.product.edit', $product->id) }}" class="btn btn-warning">
            <i class="ti tabler-pencil"></i> Edit
        </a>
    @endcan
    @can('product.delete')
        <form method="POST" action="{{ route('master_data.product.destroy', $product->id) }}" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger" onclick="return confirm('Hapus?')">
                <i class="ti tabler-trash"></i> Hapus
            </button>
        </form>
    @endcan
</div>
```

---

## 📝 Permission Name Pattern

Untuk setiap module gunakan:
```
module.view     ← Lihat/Read
module.create   ← Tambah/Create
module.edit     ← Ubah/Update
module.delete   ← Hapus/Delete
module.import   ← (jika ada)
module.export   ← (jika ada)
```

**Contoh:**
- `product.view` - Lihat daftar produk
- `product.create` - Tambah produk baru
- `product.edit` - Ubah produk
- `product.delete` - Hapus produk
- `product.import` - Import produk
- `product.export` - Export produk

---

## 🔐 Backend Validation

Jangan lupa check di controller juga!

```php
public function store(Request $request)
{
    // Check permission
    if (!auth()->user()->hasPermissionTo('product.create')) {
        abort(403, 'Unauthorized');
    }
    
    // Process
}

public function update(Request $request, $id)
{
    if (!auth()->user()->hasPermissionTo('product.edit')) {
        abort(403, 'Unauthorized');
    }
}

public function destroy($id)
{
    if (!auth()->user()->hasPermissionTo('product.delete')) {
        abort(403, 'Unauthorized');
    }
}
```

---

## 🎓 Contoh Implementation

### Sebelum (Tidak Ada Permission Check)
```blade
<a href="{{ route('master_data.product.create') }}" class="btn btn-primary">
    Tambah Produk
</a>
```

### Sesudah (Dengan Permission Check)
```blade
@can('product.create')
    <a href="{{ route('master_data.product.create') }}" class="btn btn-primary">
        <i class="ti tabler-plus"></i> Tambah Produk
    </a>
@endcan
```

---

## 🚀 Implementation Order

1. **Index Page**
   - Add tombol Create with `@can('module.create')`
   - Add Edit button di table dengan `@can('module.edit')`
   - Add Delete button di table dengan `@can('module.delete')`

2. **Show/Detail Page**
   - Add Edit button dengan `@can('module.edit')`
   - Add Delete button dengan `@can('module.delete')`

3. **Create/Edit Form**
   - Add Submit button dengan `@can('module.create')` atau `@can('module.edit')`

4. **Controller Methods**
   - Add permission check di store, update, destroy methods

---

## 📚 Module Checklist

Tandai setiap module setelah permission ditambahkan:

- [ ] Dashboard
- [ ] Sales Order
- [ ] Sales Delivery
- [ ] Sales Invoice
- [ ] Purchase Order
- [ ] Purchase Delivery
- [ ] Purchase Invoice
- [ ] Expense
- [ ] Budget
- [ ] Top Up
- [ ] Production
- [ ] Packing
- [ ] Stock Adjustment
- [ ] Return Management
- [ ] Stock Opname
- [ ] Product
- [ ] Contact
- [ ] Warehouse
- [ ] Inventory
- [ ] Revenue Split
- [ ] Tax
- [ ] Unit
- [ ] Product Category
- [ ] Tag
- [ ] Sales Person
- [ ] Payment Term
- [ ] Transaction Number
- [ ] Budget Category
- [ ] User
- [ ] Role
- [ ] Reports

---

## 🔍 Testing

### Test Permission Visibility

```bash
php artisan tinker

# Test untuk super_admin (punya semua permission)
> Auth::loginUsingId(1); # If ID 1 is super_admin

# Akses halaman, menu seharusnya muncul penuh

# Test untuk limited user
> Auth::loginUsingId(2); # Limited user

# Akses halaman, hanya menu yang sesuai permission yang muncul
```

### Clear Cache Jika Ada Masalah
```bash
php artisan cache:clear
php artisan cache:forget spatie.permission.cache
```

---

## 💡 Tips

1. **Gunakan Copy-Paste Template**
   - Jangan ketik dari awal, copy dari contoh di atas

2. **Konsisten Dengan Naming**
   - Selalu gunakan format `module.action` (lowercase)
   - Contoh: ✅ `product.create` ❌ `Product Create`

3. **Test Setiap Module**
   - Coba dengan user yang punya permission
   - Coba dengan user yang tidak punya permission
   - Tombol harus hilang untuk user tanpa permission

4. **Dokumentasi Permissions**
   - Catat permission apa yang digunakan di setiap halaman
   - Memudahkan saat maintenance dan debugging

---

## 🐛 Common Issues

| Issue | Solusi |
|-------|--------|
| Tombol tidak hilang meski tanpa permission | Clear cache: `php artisan cache:clear` |
| Permission tidak ditemukan | Check DB: `php artisan tinker` → `Permission::all()` |
| Login gagal | Check user has role/permission assigned |
| @can tidak work | Pastikan permission name exact match dengan DB |

---

## 📞 Bantuan

Refer ke dokumentasi lengkap:
- [BUTTONS_PERMISSIONS_IMPLEMENTATION.md](./BUTTONS_PERMISSIONS_IMPLEMENTATION.md) - Panduan detail
- [PERMISSIONS_MENU_BUTTONS_GUIDE.md](./PERMISSIONS_MENU_BUTTONS_GUIDE.md) - Contoh lengkap
- [PERMISSIONS_IMPLEMENTATION_SUMMARY.md](./PERMISSIONS_IMPLEMENTATION_SUMMARY.md) - Overview

---

**Mulai dari sini:** Copy template di atas dan paste ke view Anda, ganti `product` dengan module name yang sesuai! 🚀
