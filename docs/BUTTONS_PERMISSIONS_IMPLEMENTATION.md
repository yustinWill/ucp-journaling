# Implementasi Permission Checks di Buttons

Panduan praktis untuk menambahkan permission checks pada tombol-tombol di aplikasi Anda.

## Quick Reference - Permission Checks di Blade

### 1. Tombol CREATE/TAMBAH (Index Page)

```blade
<div class="d-flex justify-content-between mb-3">
    <h5>Daftar Produk</h5>
    @can('product.create')
        <a href="{{ route('master_data.product.create') }}" class="btn btn-primary">
            <i class="ti tabler-plus"></i> Tambah Produk
        </a>
    @endcan
</div>
```

Jika tidak punya permission, tombol tidak muncul.

---

### 2. Tombol EDIT - Table Actions

```blade
<table class="table">
    <thead>
        <tr>
            <th>Nama</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($products as $product)
            <tr>
                <td>{{ $product->name }}</td>
                <td>
                    <div class="btn-group">
                        @can('product.edit')
                            <a href="{{ route('master_data.product.edit', $product->id) }}" 
                               class="btn btn-sm btn-warning" title="Edit">
                                <i class="ti tabler-pencil"></i>
                            </a>
                        @endcan
                        
                        @can('product.delete')
                            <form method="POST" 
                                  action="{{ route('master_data.product.destroy', $product->id) }}" 
                                  style="display:inline;" 
                                  class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" 
                                        title="Hapus"
                                        onclick="return confirm('Yakin akan dihapus?')">
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

---

### 3. Tombol di Detail/Show Page

```blade
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5>Detail Produk</h5>
        <div class="btn-group">
            @can('product.edit')
                <a href="{{ route('master_data.product.edit', $product->id) }}" class="btn btn-warning">
                    <i class="ti tabler-pencil"></i> Edit
                </a>
            @endcan
            
            @can('product.delete')
                <form method="POST" 
                      action="{{ route('master_data.product.destroy', $product->id) }}" 
                      style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" 
                            onclick="return confirm('Yakin akan dihapus?')">
                        <i class="ti tabler-trash"></i> Hapus
                    </button>
                </form>
            @endcan
            
            <a href="{{ route('master_data.product.index') }}" class="btn btn-secondary">
                <i class="ti tabler-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    
    <div class="card-body">
        <dl>
            <dt>Nama</dt>
            <dd>{{ $product->name }}</dd>
            <dt>Kategori</dt>
            <dd>{{ $product->category->name }}</dd>
        </dl>
    </div>
</div>
```

---

### 4. Tombol di Create/Edit Form

```blade
<form action="{{ $product->exists ? route('master_data.product.update', $product->id) : route('master_data.product.store') }}" 
      method="POST">
    @csrf
    @if($product->exists)
        @method('PUT')
    @endif
    
    <div class="form-group mb-3">
        <label class="form-label">Nama Produk</label>
        <input type="text" name="name" class="form-control" 
               value="{{ old('name', $product->name ?? '') }}" required>
        @error('name')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
    
    <div class="form-group mb-3">
        <label class="form-label">Kategori</label>
        <select name="category_id" class="form-control">
            <option>-- Pilih --</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" 
                        {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </div>
    
    <div class="form-actions">
        @if($product->exists)
            @can('product.edit')
                <button type="submit" class="btn btn-primary">
                    <i class="ti tabler-check"></i> Update
                </button>
            @endcan
        @else
            @can('product.create')
                <button type="submit" class="btn btn-primary">
                    <i class="ti tabler-check"></i> Simpan
                </button>
            @endcan
        @endif
        
        <a href="{{ route('master_data.product.index') }}" class="btn btn-secondary ms-2">
            <i class="ti tabler-x"></i> Batal
        </a>
    </div>
</form>
```

---

### 5. Conditional Render - Multiple Permissions

```blade
{{-- Jika user punya salah satu permission --}}
@if(Auth::user()->hasPermissionTo('product.create') || 
    Auth::user()->hasPermissionTo('product.edit') ||
    Auth::user()->hasPermissionTo('product.delete'))
    <div class="alert alert-info">
        Anda memiliki akses untuk mengelola produk
    </div>
@endif

{{-- Atau gunakan helper --}}
@if(Auth::user()->hasAnyPermission(['product.create', 'product.edit', 'product.delete']))
    <div class="alert alert-info">
        Anda memiliki akses untuk mengelola produk
    </div>
@endif
```

---

### 6. Export/Import Buttons

```blade
<div class="d-flex gap-2 mb-3">
    @can('product.view')
        <a href="{{ route('master_data.product.index') }}" class="btn btn-outline-secondary">
            <i class="ti tabler-list"></i> Daftar Produk
        </a>
    @endcan
    
    @can('product.import')
        <a href="{{ route('master_data.product.import') }}" class="btn btn-outline-info">
            <i class="ti tabler-upload"></i> Import
        </a>
    @endcan
    
    @can('product.export')
        <a href="{{ route('master_data.product.export') }}" class="btn btn-outline-success">
            <i class="ti tabler-download"></i> Export
        </a>
    @endcan
    
    @can('product.create')
        <a href="{{ route('master_data.product.create') }}" class="btn btn-primary">
            <i class="ti tabler-plus"></i> Tambah
        </a>
    @endcan
</div>
```

---

## Permission Naming Convention

Untuk setiap module, gunakan pattern ini:

| Permission | Deskripsi |
|-----------|-----------|
| `module.view` | Akses lihat halaman, filter, search |
| `module.create` | Akses buat data baru |
| `module.edit` | Akses ubah data |
| `module.delete` | Akses hapus data |
| `module.import` | Akses import dari file |
| `module.export` | Akses export ke file |

**Contoh untuk Sales Order:**
```
sales_order.view
sales_order.create
sales_order.edit
sales_order.delete
```

---

## Implementation Tips

### 1. Controller Side - Double Check Permission

```php
public function edit($id)
{
    // Double-check di controller (jangan hanya di UI)
    if (!auth()->user()->hasPermissionTo('product.edit')) {
        abort(403, 'Anda tidak memiliki akses untuk edit');
    }
    
    $product = Product::findOrFail($id);
    return view('master_data.product.edit', compact('product'));
}
```

### 2. Disable Button Instead of Hide (Optional)

```blade
@can('product.delete')
    <button class="btn btn-danger">Hapus</button>
@else
    <button class="btn btn-danger" disabled>Hapus</button>
@endcan
```

### 3. Show Info Message When Disabled

```blade
@can('product.delete')
    <form method="POST" action="{{ route('master_data.product.destroy', $product->id) }}">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">Hapus</button>
    </form>
@else
    <button class="btn btn-danger" disabled title="Anda tidak memiliki akses hapus">
        Hapus
    </button>
@endcan
```

---

## Bulk Actions dengan Permission

```blade
<form id="bulk-form" method="POST" action="{{ route('master_data.product.bulk-action') }}">
    @csrf
    
    {{-- Checkbox column di table --}}
    <table class="table">
        <tbody>
            @foreach($products as $product)
                <tr>
                    <td>
                        <input type="checkbox" name="ids[]" value="{{ $product->id }}" class="bulk-checkbox">
                    </td>
                    <td>{{ $product->name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    {{-- Bulk action buttons --}}
    @if(Auth::user()->hasAnyPermission(['product.edit', 'product.delete']))
        <div class="mt-3">
            @can('product.edit')
                <button type="submit" name="action" value="status" class="btn btn-warning">
                    <i class="ti tabler-pencil"></i> Update Status
                </button>
            @endcan
            
            @can('product.delete')
                <button type="submit" name="action" value="delete" class="btn btn-danger" 
                        onclick="return confirm('Hapus semua item terpilih?')">
                    <i class="ti tabler-trash"></i> Hapus
                </button>
            @endcan
        </div>
    @endif
</form>
```

---

## Component Helper (Optional)

Buat component reusable untuk button groups:

**resources/views/components/action-buttons.blade.php**
```blade
@props(['model', 'routePrefix'])

<div class="btn-group">
    @can($routePrefix . '.edit')
        <a href="{{ route($routePrefix . '.edit', $model->id) }}" 
           class="btn btn-sm btn-warning" title="Edit">
            <i class="ti tabler-pencil"></i>
        </a>
    @endcan
    
    @can($routePrefix . '.delete')
        <form method="POST" 
              action="{{ route($routePrefix . '.destroy', $model->id) }}" 
              style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-danger" 
                    title="Hapus"
                    onclick="return confirm('Yakin?')">
                <i class="ti tabler-trash"></i>
            </button>
        </form>
    @endcan
</div>
```

**Penggunaan:**
```blade
@foreach($products as $product)
    <tr>
        <td>{{ $product->name }}</td>
        <td>
            <x-action-buttons :model="$product" routePrefix="master_data.product" />
        </td>
    </tr>
@endforeach
```

---

## Testing Permissions

Untuk test permission di local development tanpa buat user baru:

**Artisan Command:**
```bash
php artisan tinker
> Auth::user()->givePermissionTo('product.create');
> Auth::user()->hasPermissionTo('product.create')
```

---

## Clear Cache

Jika mengubah permissions, clear cache:

```bash
php artisan cache:forget spatie.permission.cache
```

Atau dalam kode:
```php
// Setelah assign/revoke permission
cache()->forget('spatie.permission.cache');
```

---

## Checklisting Implementation

**Untuk setiap halaman:**
- [ ] Tambahkan @can() di tombol Create/Tambah
- [ ] Tambahkan @can() di tombol Edit setiap row di table
- [ ] Tambahkan @can() di tombol Delete setiap row
- [ ] Tambahkan @can() di tombol Edit/Delete di detail page
- [ ] Double-check permission di controller method
- [ ] Test dengan user yang tidak punya permission

**Di menu.blade.php:**
- [ ] Wrap parent menu header dengan checker
- [ ] Wrap setiap sub-menu item dengan @can()
- [ ] Test menu muncul/hilang sesuai permission

---

## Contoh Lengkap - Product Index Page

```blade
@extends('layouts.default')

@section('content')
<div class="container-xxl">
    <div class="row mb-4">
        <div class="col-md-6">
            <h3>Daftar Produk</h3>
        </div>
        <div class="col-md-6 text-end">
            @can('product.create')
                <a href="{{ route('master_data.product.create') }}" class="btn btn-primary">
                    <i class="ti tabler-plus"></i> Tambah Produk
                </a>
            @endcan
            
            @can('product.import')
                <a href="{{ route('master_data.product.import') }}" class="btn btn-info ms-2">
                    <i class="ti tabler-upload"></i> Import
                </a>
            @endcan
            
            @can('product.export')
                <a href="{{ route('master_data.product.export') }}" class="btn btn-success ms-2">
                    <i class="ti tabler-download"></i> Export
                </a>
            @endcan
        </div>
    </div>
    
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->category->name }}</td>
                            <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    @can('product.edit')
                                        <a href="{{ route('master_data.product.edit', $product->id) }}" 
                                           class="btn btn-warning">
                                            <i class="ti tabler-pencil"></i>
                                        </a>
                                    @endcan
                                    
                                    @can('product.delete')
                                        <form method="POST" 
                                              action="{{ route('master_data.product.destroy', $product->id) }}" 
                                              style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" 
                                                    onclick="return confirm('Yakin?')">
                                                <i class="ti tabler-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
```
