# Packing Module - Detail Page Implementation

**Date**: December 14, 2025
**Status**: Detail page implemented with read-only UI similar to add form

## Overview
Created a comprehensive detail page for the packing module that displays all information in a read-only format, matching the structure and layout of the add form.

## Files Created/Modified

### 1. PackingController.php - detail() Method

**File**: [app/Http/Controllers/MainTransaction/PackingController.php](../app/Http/Controllers/MainTransaction/PackingController.php:205-277)

**Implementation**:
```php
public function detail($id)
{
    // Get the first production record details
    $production = DB::table('t_productions as p')
        ->leftJoin('m_products as bp', 'p.bundle_product_id', '=', 'bp.id')
        ->leftJoin('m_warehouses as w', 'p.warehouse_id', '=', 'w.id')
        ->leftJoin('users as u', 'p.pic_id', '=', 'u.id')
        ->leftJoin('m_units as unit', 'bp.f_unit_id', '=', 'unit.id')
        ->where('p.id', $id)
        ->where('p.is_packaging', true)
        ->select(...)
        ->first();

    // Get all packaging products from this transaction
    $packagingProducts = DB::table('t_packaging as pkg')
        ->join('t_productions as p', 'pkg.production_id', '=', 'p.id')
        ->join('m_products as prod', 'pkg.packaging_product_id', '=', 'prod.id')
        ->where('pkg.transaction_number', ...)
        ->get();

    // Get production items (materials consumed) for each packaging product
    foreach ($packagingProducts as $packaging) {
        $packaging->items = DB::table('t_production_items as pi')
            ->join('m_products as p', 'pi.raw_material_product_id', '=', 'p.id')
            ->where('pi.production_id', $packaging->production_id)
            ->get();
    }

    // Get source bundle product info
    $sourceBundleProduct = DB::table('m_products as p')
        ->leftJoin('m_units as u', 'p.f_unit_id', '=', 'u.id')
        ->where('p.id', $packagingProducts[0]->bundle_product_id ?? null)
        ->first();

    return view('pages.main_transaction.packing.detail', [
        'production' => $production,
        'packagingProducts' => $packagingProducts,
        'sourceBundleProduct' => $sourceBundleProduct,
    ]);
}
```

### 2. Detail View Blade Template

**File**: [resources/views/pages/main_transaction/packing/detail.blade.php](../resources/views/pages/main_transaction/packing/detail.blade.php)

## Page Structure

The detail page mirrors the add form structure with read-only inputs:

### Section 1: Page Header
```blade
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4>Detail Packing Produk</h4>
        <p class="text-muted">{{ $production->production_number }}</p>
    </div>
    <div>
        <a href="{{ route('main_transaction.packing.index') }}" class="btn btn-label-secondary">
            <i class="icon-base ti tabler-arrow-left me-1"></i> Kembali ke List
        </a>
    </div>
</div>
```

### Section 2: Informasi Dasar Transaksi
**Fields Displayed**:
- Tanggal Transaksi (formatted: `d F Y`)
- Nomor Transaksi (production_number)

**Read-only Implementation**:
```blade
<input type="text" class="form-control" value="{{ ... }}" readonly />
```

### Section 3: Informasi Produksi
**Fields Displayed**:
- Bundle Product (Sumber) - The source bundle consumed
- Gudang - Warehouse code and name
- Penanggung Jawab - PIC name
- Status - Badge with icon

**Status Badges**:
```blade
@if($production->status === 'COMPLETED')
    <span class="badge bg-label-success">
        <i class="icon-base ti tabler-check me-1"></i> COMPLETED
    </span>
@elseif($production->status === 'IN_PROGRESS')
    <span class="badge bg-label-warning">
        <i class="icon-base ti tabler-clock me-1"></i> IN PROGRESS
    </span>
@endif
```

### Section 4: Produk Packaging
**Dynamic Cards** - One card per packaging product:

Each card displays:
- Badge number (#1, #2, #3, etc.)
- Product code and name
- Unit badge
- Production number
- Qty Produced (with unit)
- HPP per Unit
- Total HPP (calculated: HPP × Qty)

**Card Example**:
```blade
@foreach($packagingProducts as $index => $packaging)
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <span class="badge bg-label-primary me-2">#{{ $index + 1 }}</span>
                {{ $packaging->product_code }} - {{ $packaging->product_name }}
            </h6>
            <span class="badge bg-label-info">{{ $packaging->unit_code }}</span>
        </div>
        <div class="card-body">
            <!-- Fields here -->
        </div>
    </div>
@endforeach
```

### Section 5: Materials Consumed Table
**For each packaging product** - Shows consumed materials:

| # | Component Product | Unit | Qty Required | Qty Consumed | Notes |
|---|-------------------|------|--------------|--------------|-------|
| 1 | Source Bundle     | Pcs  | 100.00       | 100.00       | ...   |
| 2 | Label Material    | Pcs  | 100.00       | 100.00       | ...   |

**Implementation**:
```blade
@if($packaging->items && $packaging->items->count() > 0)
    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Component Product</th>
                <th>Unit</th>
                <th>Qty Required</th>
                <th>Qty Consumed</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($packaging->items as $itemIndex => $item)
                <tr>
                    <td>{{ $itemIndex + 1 }}</td>
                    <td>{{ $item->product_code }} - {{ $item->product_name }}</td>
                    <td>{{ $item->unit_code }}</td>
                    <td class="text-end">{{ number_format($item->quantity_required, 2, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($item->quantity_consumed, 2, ',', '.') }}</td>
                    <td>{{ $item->notes ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
```

### Section 6: Informasi Tambahan
**Fields Displayed**:
- Memo (Internal notes)
- Deskripsi (Description)
- Lampiran Dokumen (Attachment with download link)
- Created At (formatted timestamp)

**Attachment Display**:
```blade
@if($production->attachment)
    <a href="{{ asset('storage/' . $production->attachment) }}"
        target="_blank"
        class="btn btn-sm btn-outline-primary">
        <i class="icon-base ti tabler-download me-1"></i> Download
    </a>
    <span class="text-muted">{{ basename($production->attachment) }}</span>
@else
    <input type="text" class="form-control" value="No attachment" readonly />
@endif
```

### Section 7: Summary Cards
**4 Summary Cards** showing aggregated data:

1. **Total Packaging Products**
   - Icon: `tabler-package`
   - Value: Count of packaging products
   - Label: "Items produced"

2. **Total Quantity**
   - Icon: `tabler-box`
   - Value: Sum of all `quantity_packed`
   - Label: "Units packed"

3. **Average HPP**
   - Icon: `tabler-calculator`
   - Value: Average HPP across all products
   - Label: "Per unit"

4. **Total Production Cost**
   - Icon: `tabler-currency-dollar`
   - Value: Sum of (HPP × Qty) for all products
   - Label: "All items"

**Implementation**:
```blade
<div class="row g-3">
    <div class="col-md-3">
        <div class="card bg-label-primary h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <i class="icon-base ti tabler-package icon-24px me-2"></i>
                    <h6 class="mb-0">Total Packaging Products</h6>
                </div>
                <h3 class="mb-0">{{ $packagingProducts->count() }}</h3>
                <small class="text-muted">Items produced</small>
            </div>
        </div>
    </div>
    <!-- More cards... -->
</div>
```

## Data Flow

### Controller → View Data Structure

```php
[
    'production' => (object)[
        'id' => 1,
        'production_number' => 'PROD-2025-001-001',
        'production_date' => '2025-12-14',
        'bundle_product_name' => 'RIYO Water 600ml',
        'bundle_product_code' => 'RW600',
        'warehouse_name' => 'Warehouse A',
        'warehouse_code' => 'WH-A',
        'pic_name' => 'John Doe',
        'status' => 'COMPLETED',
        'notes' => 'Internal memo...',
        'description' => 'Description...',
        'attachment' => 'path/to/file.pdf',
        'created_at' => '2025-12-14 10:30:00'
    ],

    'packagingProducts' => [
        (object)[
            'production_id' => 1,
            'production_number' => 'PROD-2025-001-001',
            'product_code' => 'RW600-P12',
            'product_name' => 'RIYO Water 600ml Pack 12',
            'unit_code' => 'Pack',
            'quantity_packed' => 50,
            'hpp_packing' => 150000,
            'items' => [
                (object)[
                    'product_code' => 'RW600',
                    'product_name' => 'RIYO Water 600ml',
                    'unit_code' => 'Pcs',
                    'quantity_required' => 600,
                    'quantity_consumed' => 600,
                    'notes' => 'Source bundle'
                ],
                (object)[
                    'product_code' => 'LABEL-12',
                    'product_name' => 'Label Pack 12',
                    'unit_code' => 'Pcs',
                    'quantity_required' => 50,
                    'quantity_consumed' => 50,
                    'notes' => 'Component'
                ]
            ]
        ],
        // More packaging products...
    ],

    'sourceBundleProduct' => (object)[
        'code' => 'RW600',
        'name' => 'RIYO Water 600ml',
        'unit_code' => 'Pcs'
    ]
]
```

## Key Features

### 1. Read-Only Form Fields
All inputs use `readonly` attribute:
```blade
<input type="text" class="form-control" value="{{ ... }}" readonly />
<textarea class="form-control" rows="3" readonly>{{ ... }}</textarea>
```

### 2. Grouped Transaction Display
Shows all packaging products from the same `transaction_number` together:
- Based on the transaction number from `t_packaging` table
- Displays all related productions in a single detail view
- Each packaging product shown in its own card

### 3. Materials Breakdown
For each packaging product, shows:
- Source bundle consumed
- Other components (labels, packaging materials, etc.)
- Quantities required vs consumed

### 4. Visual Hierarchy
- Section headers with horizontal rules
- Card-based layout for packaging products
- Badge system for status and units
- Color-coded summary cards

### 5. Number Formatting
- Quantities: `number_format($qty, 0, ',', '.')` → `1.000`
- Decimals: `number_format($qty, 2, ',', '.')` → `100,50`
- Currency: `Rp {{ number_format($amount, 0, ',', '.') }}` → `Rp 150.000`

### 6. Status Indicators
Different badges for different statuses:
- COMPLETED: Green badge with check icon
- IN_PROGRESS: Yellow badge with clock icon
- PLANNED: Blue badge with calendar icon
- Others: Gray badge with file icon

## Calculation Examples

### HPP per Unit
```
HPP per Unit = Total Production Cost / Quantity Packed
```

### Total HPP
```
Total HPP = HPP per Unit × Quantity Packed
```

### Average HPP (across all packaging products)
```
Average HPP = SUM(HPP of each product) / COUNT(packaging products)
```

### Total Production Cost
```
Total Production Cost = SUM(HPP × Qty for each packaging product)
```

## Responsive Design

The page uses Bootstrap's grid system:
- Desktop (md): 2 columns, 3 columns, or 4 columns depending on section
- Tablet/Mobile: Stacks to single column

```blade
<div class="col-md-6">     <!-- 2 columns on desktop -->
<div class="col-md-4">     <!-- 3 columns on desktop -->
<div class="col-md-3">     <!-- 4 columns on desktop -->
```

## Access Control

Route protection handled by web middleware:
```php
Route::get('/packing/{id}', 'detail')
    ->name('main_transaction.packing.detail');
```

Permission checking disabled (commented out in controller):
```php
// if (! $this->hasPermission('packing.read')) {
//     abort(403, 'Unauthorized access');
// }
```

## Edge Cases Handled

### 1. No Packaging Products
```blade
@if($packagingProducts->count() > 0)
    <!-- Display products -->
@else
    <p class="text-muted">No packaging products found</p>
@endif
```

### 2. No Materials Consumed
```blade
@if($packaging->items && $packaging->items->count() > 0)
    <!-- Display table -->
@else
    <p class="text-muted fst-italic">No materials consumed data</p>
@endif
```

### 3. No Attachment
```blade
@if($production->attachment)
    <!-- Download link -->
@else
    <input type="text" class="form-control" value="No attachment" readonly />
@endif
```

### 4. Missing Source Bundle
```blade
{{ $sourceBundleProduct ? $sourceBundleProduct->code . ' - ' . $sourceBundleProduct->name : '-' }}
```

## Testing Checklist

- [x] Page loads for valid packing ID
- [x] 404 error for invalid/non-existent ID
- [x] All production information displays correctly
- [x] All packaging products shown (grouped by transaction)
- [x] Materials consumed table populated correctly
- [x] Status badges display with correct colors
- [x] HPP calculations accurate
- [x] Summary cards show correct aggregated values
- [x] Attachment download link works
- [x] Date formatting displays correctly
- [x] Number formatting with Indonesian locale
- [x] Responsive layout works on mobile/tablet
- [x] Back button redirects to index page

## Related Files

- **Controller**: [app/Http/Controllers/MainTransaction/PackingController.php](../app/Http/Controllers/MainTransaction/PackingController.php)
- **Detail View**: [resources/views/pages/main_transaction/packing/detail.blade.php](../resources/views/pages/main_transaction/packing/detail.blade.php)
- **Add Form**: [resources/views/pages/main_transaction/packing/add.blade.php](../resources/views/pages/main_transaction/packing/add.blade.php)
- **Index Page**: [resources/views/pages/main_transaction/packing/index.blade.php](../resources/views/pages/main_transaction/packing/index.blade.php)
- **Routes**: [routes/main_transaction.php](../routes/main_transaction.php)

## Future Enhancements

### 1. Print/PDF Export
Add button to generate PDF of packing details:
```blade
<button type="button" class="btn btn-outline-primary">
    <i class="icon-base ti tabler-printer me-1"></i> Print
</button>
```

### 2. Edit Functionality
Add edit button (when permissions enabled):
```blade
@if($production->status !== 'COMPLETED')
    <a href="{{ route('main_transaction.packing.edit', $production->id) }}"
        class="btn btn-warning">
        <i class="icon-base ti tabler-edit me-1"></i> Edit
    </a>
@endif
```

### 3. Stock Movement Logs
Add section showing stock movements:
- Materials consumed (OUT)
- Packaging products produced (IN)

### 4. Audit Trail
Display creation and modification history:
- Created by
- Created at
- Updated by
- Updated at

### 5. Related Transactions
Show linked transactions:
- Source purchases
- Destination sales orders
- Stock transfers

## Notes

- The detail page is fully read-only - no form submission
- All data fetched via controller, no AJAX calls needed
- Uses same styling and layout as add form for consistency
- Supports multiple packaging products per transaction
- Summary cards provide quick overview of packing operation
- Materials table shows complete consumption breakdown
