# Production (Produksi) Add Form - Visual Layout

## ASCII Layout Representation

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ Header                                                                                                      │
│ Menu                                                                                                        │
│                                                                                                             │
│    Tambah Produksi Produk                                                                                  │
│                                                                                                             │
│    Info Produk                                                                                             │
│    ─────────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐                              │
│    │ Tanggal Produksi Produk         │  │ Batch Produksi                  │                              │
│    │ 15-Oct-25                       │  │ 2                               │                              │
│    └─────────────────────────────────┘  └─────────────────────────────────┘                              │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐                              │
│    │ Gudang*                         │  │ Jumlah Produksi                 │                              │
│    │ Gudang Utama                ▼   │  │ 5000                        pcs │                              │
│    └─────────────────────────────────┘  └─────────────────────────────────┘                              │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐                              │
│    │ Pilih Produk Bundling           │  │ Kategori Produk                 │                              │
│    │ Riyo Botol 600ml            ▼   │  │ autofill setelah pilih produk▼  │                              │
│    └─────────────────────────────────┘  └─────────────────────────────────┘                              │
│                                                                                                             │
│    ┌─────────────────────────────────┐                                                                    │
│    │ Kode Produk*                    │                                                                    │
│    │ autofill setelah pilih produk   │                                                                    │
│    └─────────────────────────────────┘                                                                    │
│                                                                                                             │
│    Info Detail Produksi                                                                                   │
│    ─────────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    ┌─────────────────────────────────────────────────────────────────────────────────────────────────── │
│    │ Nama Produk │ Harga    │ Jumlah │ Unit │ Stok Saat │ Sisa Stok │ Total                             │ │
│    │             │          │        │      │ Ini       │ Setelah   │                                   │ │
│    ├─────────────┼──────────┼────────┼──────┼───────────┼───────────┼─────────────────────────────────── │
│    │ Lakban      │ Rp  2,500│    200 │ pcs  │     800   │    600    │ Rp      500,000                 │ │
│    │ Botol       │ Rp    500│   5000 │ pcs  │    8000   │   3000    │ Rp    2,500,000                 │ │
│    │ Tutup Botol │ Rp    100│  50000 │ pcs  │   55000   │   5000    │ Rp    5,000,000                 │ │
│    │ Label       │ Rp    100│    200 │ pcs  │     400   │    200    │ Rp       20,000                 │ │
│    │ Karton      │ Rp  1,000│    200 │ pcs  │     500   │    300    │ Rp      200,000                 │ │
│    └─────────────┴──────────┴────────┴──────┴───────────┴───────────┴─────────────────────────────────── │
│                                                                                                             │
│    ┌─────────────────────────────────────────────────────────────────────────────────────────────────── │
│    │ Akun Biaya       │ Harga             │                              │ Total                       │ │
│    ├──────────────────┼───────────────────┼──────────────────────────────┼──────────────────────────── │
│    │ Biaya Produksi ▼ │ Rp5,000,000       │                              │ Rp     5,000,000            │ │
│    │ + Tambah Biaya   │                   │                              │                             │ │
│    └──────────────────┴───────────────────┴──────────────────────────────┴──────────────────────────── │
│                                                                                                             │
│                    ┌─────────────────────────────────────────────────────────────────────────────────── │
│                    │                    Total Biaya Bahan Baku        Rp     8,220,000               │ │
│                    │                    Total Biaya Produksi          Rp                             │ │
│                    │                    Total Keseluruhan             Rp     8,220,000               │ │
│                    │                                                                                  │ │
│                    │                    Total Harga Perunit           Rp         1,644               │ │
│                    └─────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│                                         ┌─────────────┐ ┌─────────────┐                                  │
│                                         │ Batalkan    │ │ Simpan      │                                  │
│                                         └─────────────┘ └─────────────┘                                  │
│                                                                                                             │
└─────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

## Grid-Based Layout

```
┌─ HEADER ─────────────────────────────────────────────────────────────────────────────────┐
│ Tambah Produksi Produk                                                                  │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ SECTION 1: INFO PRODUK ─────────────────────────────────────────────────────────────────┐
│ Info Produk                                                                             │
│ ─────────────────────────────────────────────────────────────────────────────────────────│
│                                                                                          │
│ ┌─ ROW 1 ──────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 6 ─────────────────────┐    ┌─ COL 6 ─────────────────────┐                  │ │
│ │ │ Tanggal Produksi Produk     │    │ Batch Produksi              │                  │ │
│ │ │ [Date] "15-Oct-25"          │    │ [Text] "2"                  │                  │ │
│ │ └─────────────────────────────┘    └─────────────────────────────┘                  │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ ROW 2 ──────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 6 ─────────────────────┐    ┌─ COL 6 ─────────────────────┐                  │ │
│ │ │ Gudang* [Dropdown]          │    │ Jumlah Produksi             │                  │ │
│ │ │ "Gudang Utama"              │    │ [Number + Unit] "5000 pcs"  │                  │ │
│ │ └─────────────────────────────┘    └─────────────────────────────┘                  │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ ROW 3 ──────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 6 ─────────────────────┐    ┌─ COL 6 ─────────────────────┐                  │ │
│ │ │ Pilih Produk Bundling       │    │ Kategori Produk             │                  │ │
│ │ │ [Dropdown] "Riyo Botol 600ml"│   │ [Dropdown/Autofill]         │                  │ │
│ │ └─────────────────────────────┘    └─────────────────────────────┘                  │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ ROW 4 ──────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 6 ─────────────────────┐                                                      │ │
│ │ │ Kode Produk* [Text]         │                                                      │ │
│ │ │ "autofill setelah pilih produk"                                                   │ │
│ │ └─────────────────────────────┘                                                      │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ SECTION 2: INFO DETAIL PRODUKSI ───────────────────────────────────────────────────────┐
│ Info Detail Produksi                                                                   │
│ ─────────────────────────────────────────────────────────────────────────────────────────│
│                                                                                          │
│ ┌─ RAW MATERIALS TABLE ────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 2 ──┐ ┌─ COL 1.5 ─┐ ┌─ COL 1 ─┐ ┌─ COL 1 ─┐ ┌─ COL 1.5 ─┐ ┌─ COL 1.5 ─┐   │ │
│ │ │ Nama     │ │ Harga     │ │ Jumlah  │ │ Unit    │ │ Stok Saat │ │ Sisa Stok │   │ │
│ │ │ Produk   │ │           │ │         │ │         │ │ Ini       │ │ Setelah   │   │ │
│ │ └──────────┘ └───────────┘ └─────────┘ └─────────┘ └───────────┘ └───────────┘   │ │
│ │                                                                                  │ │
│ │ ┌─ COL 3 ──────────────────┐                                                     │ │
│ │ │ Total                    │                                                     │ │
│ │ └──────────────────────────┘                                                     │ │
│ │                                                                                  │ │
│ │ [Sample Data Rows:]                                                              │ │
│ │ Lakban | Rp 2,500 | 200 | pcs | 800 | 600 | Rp 500,000                        │ │
│ │ Botol | Rp 500 | 5000 | pcs | 8000 | 3000 | Rp 2,500,000                      │ │
│ │ Tutup Botol | Rp 100 | 50000 | pcs | 55000 | 5000 | Rp 5,000,000              │ │
│ │ Label | Rp 100 | 200 | pcs | 400 | 200 | Rp 20,000                             │ │
│ │ Karton | Rp 1,000 | 200 | pcs | 500 | 300 | Rp 200,000                        │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ PRODUCTION COSTS TABLE ─────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 4 ──────────┐ ┌─ COL 4 ───────────┐ ┌─ COL 4 ──────────────────────────────┐ │ │
│ │ │ Akun Biaya       │ │ Harga             │ │ Total                                │ │ │
│ │ │ [Dropdown]       │ │ [Currency]        │ │ [Currency]                           │ │ │
│ │ └──────────────────┘ └───────────────────┘ └──────────────────────────────────────┘ │ │
│ │                                                                                      │ │
│ │ [Sample Row:]                                                                        │ │
│ │ Biaya Produksi | Rp 5,000,000 | Rp 5,000,000                                       │ │
│ │ + Tambah Biaya                                                                       │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ FINANCIAL SUMMARY ──────────────────────────────────────────────────────────────────┐ │
│ │                    Total Biaya Bahan Baku        Rp     8,220,000                   │ │
│ │                    Total Biaya Produksi          Rp                                 │ │
│ │                    Total Keseluruhan             Rp     8,220,000                   │ │
│ │                                                                                      │ │
│ │                    Total Harga Perunit           Rp         1,644                   │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ ACTION BUTTONS ─────────────────────────────────────────────────────────────────────────┐
│                                                          ┌─────────────┐ ┌─────────────┐ │
│                                                          │ Batalkan    │ │ Simpan      │ │
│                                                          │ [Red]       │ │ [Green]     │ │
│                                                          └─────────────┘ └─────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘
```

## Responsive Breakpoints

### Desktop (12 columns)
```
Header: [Title - 12 cols]
Info Produk Row 1: [Tanggal - 6 cols] [Batch - 6 cols]
Info Produk Row 2: [Gudang - 6 cols] [Jumlah Produksi - 6 cols]
Info Produk Row 3: [Pilih Produk - 6 cols] [Kategori - 6 cols]
Info Produk Row 4: [Kode Produk - 6 cols]
Raw Materials Table: [Full width - 12 cols with 7 columns]
Production Costs: [Akun - 4 cols] [Harga - 4 cols] [Total - 4 cols]
Financial Summary: [Full width - 12 cols]
Actions: [Buttons right-aligned]
```

### Tablet (8 columns)
```
Header: [Title - 8 cols]
Info Produk: [All fields - 8 cols, stacked in pairs]
Raw Materials Table: [Horizontal scroll - 8 cols]
Production Costs: [Stacked fields - 8 cols]
Financial Summary: [Full width - 8 cols]
Actions: [Buttons full width stacked]
```

### Mobile (4 columns)
```
All sections: [Full width - 4 cols]
All fields stacked vertically
Tables: [Card layout or horizontal scroll]
Buttons: [Full width stacked]
```

## Key Layout Features

### **Production Management Specific Elements:**
- **Title**: "Tambah Produksi Produk" clearly identifying production creation
- **Recipe-based Production**: Pre-defined material list from product bundle configuration
- **Warehouse Integration**: Warehouse selection with stock tracking
- **Cost Management**: Editable production costs with automatic calculations

### **Form Structure (2 Main Sections):**
1. **Info Produk**: Production setup and product selection
2. **Info Detail Produksi**: Material requirements and cost breakdown

### **Production Setup Fields:**
- **Tanggal Produksi Produk**: Production date selection
- **Batch Produksi**: Batch number for tracking
- **Gudang**: Warehouse selection (required field)
- **Pilih Produk Bundling**: Bundle product selection (triggers auto-fill)
- **Jumlah Produksi**: Production quantity with unit
- **Kode Produk**: Auto-filled product code
- **Kategori Produk**: Auto-filled product category

### **Raw Materials Table Features:**
- **Pre-filled Data**: Material list from bundle recipe (non-editable)
- **Stock Tracking**: Current stock and remaining stock after production
- **Cost Calculation**: Automatic total calculation per material
- **Warehouse Stock**: Real-time stock levels at selected warehouse
- **Stock Validation**: Ensure sufficient stock for production

### **Production Costs Management:**
- **Flexible Costs**: Editable production expense amounts
- **Multiple Cost Categories**: Support for various production expenses
- **Add/Remove**: Dynamic cost line management
- **Account Integration**: Chart of accounts for proper cost allocation

### **Financial Calculations:**
- **Total Biaya Bahan Baku**: Sum of all raw material costs
- **Total Biaya Produksi**: Sum of all production expenses
- **Total Keseluruhan**: Grand total of all costs
- **Total Harga Perunit**: Unit cost calculation (total ÷ quantity)

### **Interactive Elements:**
- **Product Selection**: Auto-fill recipe, category, and code
- **Warehouse Selection**: Update stock levels display
- **Quantity Changes**: Recalculate material requirements
- **Cost Management**: Add/remove production expense lines
- **Real-time Calculations**: Update totals as values change

### **Validation and Business Rules:**
- **Stock Sufficiency**: Validate adequate stock for production
- **Required Fields**: Warehouse, product selection, quantities
- **Cost Validation**: Ensure positive cost values
- **Recipe Integrity**: Maintain bundle recipe proportions

### **Stock Management Integration:**
- **Current Stock Display**: Show available stock at warehouse
- **Projected Stock**: Calculate remaining stock after production
- **Multi-warehouse Support**: Different stock levels per warehouse
- **Real-time Updates**: Dynamic stock level updates

This production add form provides comprehensive production planning with recipe-based material requirements, warehouse stock integration, and flexible cost management while ensuring proper validation and real-time calculations.