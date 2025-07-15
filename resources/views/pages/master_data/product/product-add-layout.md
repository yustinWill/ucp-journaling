# Product (Produk) Creation Form - Visual Layout

## ASCII Layout Representation

### Initial State (Before Tipe Produk Selection)

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ Header                                                                                                      │
│ Menu                                                                                                        │
│                                                                                                             │
│    Tambah Produk Baru                                                                                      │
│                                                                                                             │
│    Info Produk                                                                                             │
│    ─────────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐  ┌─────────────────────────┐ │
│    │ Nama Produk*                    │  │ Unit*                           │  │                         │ │
│    │                                 │  │                             ▼   │  │    Pilih gambar         │ │
│    └─────────────────────────────────┘  └─────────────────────────────────┘  │       produk            │ │
│                                                                              │  Format file JPG atau   │ │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐  │     PNG (maks. 5 MB)    │ │
│    │ Kode Produk*                    │  │ Kategori Produk                 │  │                         │ │
│    │                                 │  │                             ▼   │  └─────────────────────────┘ │
│    └─────────────────────────────────┘  └─────────────────────────────────┘                              │
│                                                                                                             │
│    ┌─────────────────────────────────────────────────────────────────────────────────────────────────── │
│    │ Deskripsi                                                                                            │
│    │                                                                                                     │
│    │                                                                                                     │
│    └─────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    Tipe Produk                                                                                             │
│    ☐ Single        ☐ Bundle                                                                               │
│                                                                                                             │
│    [Content below will appear after selecting Tipe Produk]                                                │
│                                                                                                             │
│                                         ┌─────────────┐ ┌─────────────┐                                  │
│                                         │ Batalkan    │ │ Simpan      │                                  │
│                                         └─────────────┘ └─────────────┘                                  │
│                                                                                                             │
└─────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

### Single Product State (Harga & Persediaan Tab)

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ Header                                                                                                      │
│ Menu                                                                                                        │
│                                                                                                             │
│    Tambah Produk Baru                                                                                      │
│                                                                                                             │
│    Info Produk                                                                                             │
│    ─────────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐  ┌─────────────────────────┐ │
│    │ Nama Produk*                    │  │ Unit*                           │  │                         │ │
│    │                                 │  │                             ▼   │  │    Pilih gambar         │ │
│    └─────────────────────────────────┘  └─────────────────────────────────┘  │       produk            │ │
│                                                                              │  Format file JPG atau   │ │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐  │     PNG (maks. 5 MB)    │ │
│    │ Kode Produk*                    │  │ Kategori Produk                 │  │                         │ │
│    │                                 │  │                             ▼   │  └─────────────────────────┘ │
│    └─────────────────────────────────┘  └─────────────────────────────────┘                              │
│                                                                                                             │
│    ┌─────────────────────────────────────────────────────────────────────────────────────────────────── │
│    │ Deskripsi                                                                                            │
│    │                                                                                                     │
│    │                                                                                                     │
│    └─────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    Tipe Produk                                                                                             │
│    ☑ Single        ☐ Bundle                                                                               │
│                                                                                                             │
│    ┌─────────────────────────────────┐                                                                    │
│    │ Harga & Persediaan              │                                                                    │
│    └─────────────────────────────────┘                                                                    │
│                                                                                                             │
│    ☑ Saya beli produk ini                                                                                  │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐  ┌─────────────────────────┐ │
│    │ Harga Satuan*                   │  │ Akun Pembelian*                 │  │ Pajak Pembelian*        │ │
│    │ Rp                              │  │ 1 - Aset                    ▼   │  │ PPN                 ▼   │ │
│    └─────────────────────────────────┘  └─────────────────────────────────┘  └─────────────────────────┘ │
│                                                                                                             │
│    ☑ Saya jual produk ini                                                                                  │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐  ┌─────────────────────────┐ │
│    │ Harga Satuan*                   │  │ Akun Penjualan*                 │  │ Pajak Penjualan*        │ │
│    │ Rp                              │  │ 1 - Aset                    ▼   │  │ PPN                 ▼   │ │
│    └─────────────────────────────────┘  └─────────────────────────────────┘  └─────────────────────────┘ │
│                                                                                                             │
│    ☑ Monitoring Persediaan Barang                                                                         │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐                              │
│    │ Batas stok minimum*             │  │ Akun Persedian Default*         │                              │
│    │                                 │  │ 1 - Aset                    ▼   │                              │
│    └─────────────────────────────────┘  └─────────────────────────────────┘                              │
│    *Ketentuan stok awal dapat menggunakan penyesuaian stok                                                │
│                                                                                                             │
│    Pelacakan Persediaan                                                                                   │
│    ☑ Hanya lacak kuantitas                     ☐ Batch                                                    │
│                                                                                                             │
│                                         ┌─────────────┐ ┌─────────────┐                                  │
│                                         │ Batalkan    │ │ Simpan      │                                  │
│                                         └─────────────┘ └─────────────┘                                  │
│                                                                                                             │
└─────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

### Bundle Product State (Harga & Persediaan Tab)

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ Header                                                                                                      │
│ Menu                                                                                                        │
│                                                                                                             │
│    Tambah Produk Baru                                                                                      │
│                                                                                                             │
│    Info Produk                                                                                             │
│    ─────────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    [Same Info Produk section as above]                                                                    │
│                                                                                                             │
│    Tipe Produk                                                                                             │
│    ☐ Single        ☑ Bundle                                                                               │
│                                                                                                             │
│    ┌─────────────────────────────────┐ ┌─────────────────────────────────┐                              │
│    │ Harga & Persediaan              │ │ Pengaturan Bundle               │                              │
│    └─────────────────────────────────┘ └─────────────────────────────────┘                              │
│                                                                                                             │
│    ☑ Saya beli produk ini                                                                                  │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐  ┌─────────────────────────┐ │
│    │ Harga Satuan*                   │  │ Akun Pembelian*                 │  │ Pajak Pembelian*        │ │
│    │ Rp                              │  │ 1 - Aset                    ▼   │  │ PPN                 ▼   │ │
│    └─────────────────────────────────┘  └─────────────────────────────────┘  └─────────────────────────┘ │
│                                                                                                             │
│    ☑ Saya jual produk ini                                                                                  │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐  ┌─────────────────────────┐ │
│    │ Harga Satuan*                   │  │ Akun Penjualan*                 │  │ Pajak Penjualan*        │ │
│    │ Rp                              │  │ 1 - Aset                    ▼   │  │ PPN                 ▼   │ │
│    └─────────────────────────────────┘  └─────────────────────────────────┘  └─────────────────────────┘ │
│                                                                                                             │
│    ☑ Monitoring Persediaan Barang                                                                         │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐                              │
│    │ Batas stok minimum*             │  │ Akun Persedian Default*         │                              │
│    │                                 │  │ 1 - Aset                    ▼   │                              │
│    └─────────────────────────────────┘  └─────────────────────────────────┘                              │
│    *Ketentuan stok awal dapat menggunakan penyesuaian stok                                                │
│                                                                                                             │
│    Pelacakan Persediaan                                                                                   │
│    ☑ Hanya lacak kuantitas                     ☐ Batch                                                    │
│                                                                                                             │
│                                         ┌─────────────┐ ┌─────────────┐                                  │
│                                         │ Batalkan    │ │ Simpan      │                                  │
│                                         └─────────────┘ └─────────────┘                                  │
│                                                                                                             │
└─────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

### Bundle Product State (Pengaturan Bundle Tab)

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ Header                                                                                                      │
│ Menu                                                                                                        │
│                                                                                                             │
│    Tambah Produk Baru                                                                                      │
│                                                                                                             │
│    Info Produk                                                                                             │
│    ─────────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    [Same Info Produk section as above]                                                                    │
│                                                                                                             │
│    Tipe Produk                                                                                             │
│    ☐ Single        ☑ Bundle                                                                               │
│                                                                                                             │
│    ┌─────────────────────────────────┐ ┌─────────────────────────────────┐                              │
│    │ Harga & Persediaan              │ │ Pengaturan Bundle               │                              │
│    └─────────────────────────────────┘ └─────────────────────────────────┘                              │
│                                                                                                             │
│    ┌─────────────────────────────────────────────────────────────────────────────────────────────────── │
│    │ Nama Produk                     │ Qty     │ Unit     │ Harga                                       │ │
│    ├─────────────────────────────────┼─────────┼──────────┼───────────────────────────────────────────── │
│    │ Pilih Produk                ▼   │ 10      │ pcs      │ Rp5,400.00                              ⊖   │ │
│    │ Pilih Produk                ▼   │ 5       │ rim      │ Rp32,000.00                             ⊖   │ │
│    └─────────────────────────────────┴─────────┴──────────┴───────────────────────────────────────────── │
│                                                                                                             │
│    ┌─────────────────────────────────────────────────────────────────────────────────────────────────── │
│    │ Akun (Anda bisa mengisi ini jika Monitor persediaan barang tercentang)     │ Biaya                 │ │
│    ├─────────────────────────────────────────────────────────────────────────────┼───────────────────── │
│    │ Pilih Akun                                                              ▼   │ Rp-               ⊖   │ │
│    │ Pilih Akun                                                              ▼   │ Rp-               ⊖   │ │
│    │                                                           Total Harga        │                     │ │
│    └─────────────────────────────────────────────────────────────────────────────┴───────────────────── │
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
│ Tambah Produk Baru                                                                      │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ SECTION 1: INFO PRODUK ─────────────────────────────────────────────────────────────────┐
│ Info Produk                                                                             │
│ ─────────────────────────────────────────────────────────────────────────────────────────│
│                                                                                          │
│ ┌─ ROW 1 ──────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 4 ─────────────────────┐ ┌─ COL 4 ─────────────────────┐ ┌─ COL 4 ──────────┐ │ │
│ │ │ Nama Produk* [Text]         │ │ Unit* [Dropdown]            │ │ Image Upload     │ │ │
│ │ └─────────────────────────────┘ └─────────────────────────────┘ │ Area             │ │ │
│ │                                                                  │ 200x200px       │ │ │
│ │ ┌─ COL 4 ─────────────────────┐ ┌─ COL 4 ─────────────────────┐ │                  │ │ │
│ │ │ Kode Produk* [Text]         │ │ Kategori Produk [Dropdown]  │ │                  │ │ │
│ │ └─────────────────────────────┘ └─────────────────────────────┘ └──────────────────┘ │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ ROW 2 ──────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 12 ────────────────────────────────────────────────────────────────────────────┐ │ │
│ │ │ Deskripsi [Textarea - 3 rows]                                                       │ │ │
│ │ └────────────────────────────────────────────────────────────────────────────────────┘ │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ ROW 3 ──────────────────────────────────────────────────────────────────────────────┐ │
│ │ Tipe Produk                                                                          │ │
│ │ ☐ Single        ☐ Bundle                                                             │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ SECTION 2: CONDITIONAL CONTENT (SINGLE) ───────────────────────────────────────────────┐
│ (Only visible when Single selected)                                                     │
│                                                                                          │
│ ┌─ TAB HEADER ─────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ ACTIVE TAB ────────────────────┐                                                  │ │
│ │ │ Harga & Persediaan              │                                                  │ │
│ │ └─────────────────────────────────┘                                                  │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ PURCHASE SECTION ───────────────────────────────────────────────────────────────────┐ │
│ │ ☑ Saya beli produk ini                                                               │ │
│ │                                                                                      │ │
│ │ ┌─ COL 4 ─────────────┐ ┌─ COL 4 ─────────────┐ ┌─ COL 4 ─────────────┐           │ │
│ │ │ Harga Satuan*       │ │ Akun Pembelian*     │ │ Pajak Pembelian*    │           │ │
│ │ │ [Currency]          │ │ [Dropdown]          │ │ [Dropdown]          │           │ │
│ │ └─────────────────────┘ └─────────────────────┘ └─────────────────────┘           │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ SALES SECTION ──────────────────────────────────────────────────────────────────────┐ │
│ │ ☑ Saya jual produk ini                                                               │ │
│ │                                                                                      │ │
│ │ ┌─ COL 4 ─────────────┐ ┌─ COL 4 ─────────────┐ ┌─ COL 4 ─────────────┐           │ │
│ │ │ Harga Satuan*       │ │ Akun Penjualan*     │ │ Pajak Penjualan*    │           │ │
│ │ │ [Currency]          │ │ [Dropdown]          │ │ [Dropdown]          │           │ │
│ │ └─────────────────────┘ └─────────────────────┘ └─────────────────────┘           │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ INVENTORY SECTION ──────────────────────────────────────────────────────────────────┐ │
│ │ ☑ Monitoring Persediaan Barang                                                       │ │
│ │                                                                                      │ │
│ │ ┌─ COL 6 ─────────────────────┐    ┌─ COL 6 ─────────────────────┐                 │ │
│ │ │ Batas stok minimum*         │    │ Akun Persedian Default*     │                 │ │
│ │ │ [Number]                    │    │ [Dropdown]                  │                 │ │
│ │ └─────────────────────────────┘    └─────────────────────────────┘                 │ │
│ │ *Ketentuan stok awal dapat menggunakan penyesuaian stok                            │ │
│ │                                                                                      │ │
│ │ Pelacakan Persediaan                                                                │ │
│ │ ☑ Hanya lacak kuantitas                     ☐ Batch                                 │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ SECTION 3: CONDITIONAL CONTENT (BUNDLE) ───────────────────────────────────────────────┐
│ (Only visible when Bundle selected)                                                     │
│                                                                                          │
│ ┌─ TAB NAVIGATION ─────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ TAB 1 ──────────────────────┐ ┌─ TAB 2 ──────────────────────┐                   │ │
│ │ │ Harga & Persediaan           │ │ Pengaturan Bundle            │                   │ │
│ │ └──────────────────────────────┘ └──────────────────────────────┘                   │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ TAB CONTENT (HARGA & PERSEDIAAN) ──────────────────────────────────────────────────┐ │
│ │ [Same content as Single product sections above]                                     │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ TAB CONTENT (PENGATURAN BUNDLE) ───────────────────────────────────────────────────┐ │
│ │ ┌─ BUNDLE PRODUCTS TABLE ─────────────────────────────────────────────────────────────┐ │ │
│ │ │ ┌─ COL 5 ──────────┐ ┌─ COL 2 ──┐ ┌─ COL 2 ──┐ ┌─ COL 2 ──┐ ┌─ COL 1 ─┐        │ │ │
│ │ │ │ Nama Produk      │ │ Qty      │ │ Unit     │ │ Harga    │ │ Action  │        │ │ │
│ │ │ │ [Dropdown]       │ │ [Number] │ │ [Text]   │ │ [Curr.]  │ │ [Remove]│        │ │ │
│ │ │ └──────────────────┘ └──────────┘ └──────────┘ └──────────┘ └─────────┘        │ │ │
│ │ └─────────────────────────────────────────────────────────────────────────────────────┘ │ │
│ │                                                                                      │ │
│ │ ┌─ BUNDLE ACCOUNTS TABLE ─────────────────────────────────────────────────────────────┐ │ │
│ │ │ ┌─ COL 9 ──────────────────────────────────────────────┐ ┌─ COL 2 ──┐ ┌─ COL 1 ─┐ │ │ │
│ │ │ │ Akun (Anda bisa mengisi ini jika Monitor persediaan   │ │ Biaya    │ │ Action  │ │ │ │
│ │ │ │ barang tercentang) [Dropdown]                         │ │ [Curr.]  │ │ [Remove]│ │ │ │
│ │ │ └───────────────────────────────────────────────────────┘ └──────────┘ └─────────┘ │ │ │
│ │ │                                                           Total Harga              │ │ │
│ │ └─────────────────────────────────────────────────────────────────────────────────────┘ │ │
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
Info Produk Row 1: [Nama Produk - 4 cols] [Unit - 4 cols] [Image Upload - 4 cols]
Info Produk Row 1b: [Kode Produk - 4 cols] [Kategori - 4 cols] [Image continues]
Info Produk Row 2: [Deskripsi - 12 cols]
Tipe Produk: [Radio buttons - 12 cols]
Conditional Sections: [Full width - 12 cols]
Purchase/Sales: [Harga - 4 cols] [Akun - 4 cols] [Pajak - 4 cols]
Inventory: [Batas stok - 6 cols] [Akun Default - 6 cols]
Bundle Tables: [Full width tables with appropriate column distribution]
Actions: [Buttons right-aligned]
```

### Tablet (8 columns)
```
Header: [Title - 8 cols]
Info Produk: [Fields stacked in pairs - 8 cols total]
Image Upload: [Full width - 8 cols]
Tipe Produk: [Radio buttons - 8 cols]
Purchase/Sales: [Fields stacked - 8 cols]
Bundle Tables: [Horizontal scroll or stacked cards]
Actions: [Buttons full width stacked]
```

### Mobile (4 columns)
```
All sections: [Full width - 4 cols]
All fields stacked vertically
Image Upload: [Full width - 4 cols]
Bundle Tables: [Card layout with vertical stacking]
Buttons: [Full width stacked]
```

## Key Layout Features

### **Product Management Specific Elements:**
- **Title**: "Tambah Produk Baru" clearly identifying new product creation
- **Product Types**: Radio button selection between Single and Bundle products
- **Conditional Content**: Dynamic sections based on product type selection
- **Image Upload**: Product image upload with format restrictions

### **Form Structure (Dynamic Sections):**
1. **Info Produk**: Basic product information (always visible)
2. **Tipe Produk**: Product type selection (controls conditional content)
3. **Conditional Sections**: Different content based on Single vs Bundle selection
4. **Tab Navigation**: For Bundle products (Harga & Persediaan, Pengaturan Bundle)

### **Product Type States:**

#### **Initial State (No Selection):**
- **Basic Info**: Name, code, unit, category, description, image upload
- **Product Type**: Radio buttons (neither selected)
- **Conditional Content**: Hidden
- **Warning**: Content below will appear after selecting product type

#### **Single Product State:**
- **Single Tab**: "Harga & Persediaan" only
- **Purchase Section**: Buying price, purchase account, purchase tax
- **Sales Section**: Selling price, sales account, sales tax
- **Inventory Section**: Stock monitoring, minimum stock, default account
- **Tracking Options**: Quantity tracking and batch options

#### **Bundle Product State:**
- **Two Tabs**: "Harga & Persediaan" and "Pengaturan Bundle"
- **Harga & Persediaan Tab**: Same as Single product
- **Pengaturan Bundle Tab**: Bundle composition and account mapping

### **Field Specifications:**

#### **Info Produk Section:**
- **Nama Produk**: Required text input for product name
- **Kode Produk**: Required text input for product code/SKU
- **Unit**: Required dropdown for measurement unit
- **Kategori Produk**: Optional dropdown for product category
- **Deskripsi**: Multi-line textarea for product description
- **Image Upload**: JPG or PNG format, maximum 5MB

#### **Product Type Selection:**
- **Single**: Individual product with direct pricing
- **Bundle**: Composite product made from multiple components
- **Confirmation**: Warning before changing type (resets form below)

#### **Pricing & Inventory (Both Types):**
- **Saya beli produk ini**: Checkbox enabling purchase price fields
- **Saya jual produk ini**: Checkbox enabling sales price fields
- **Monitoring Persediaan Barang**: Checkbox enabling inventory tracking
- **Currency Fields**: Indonesian Rupiah format (Rp X,XXX,XXX.XX)
- **Account Integration**: Chart of accounts for purchase/sales/inventory

### **Bundle-Specific Features:**

#### **Pengaturan Bundle Tab:**
- **Product Selection Table**: Choose component products with quantities
- **Component Details**: Product name, quantity, unit, calculated price
- **Account Mapping Table**: Optional account assignments for inventory monitoring
- **Total Calculation**: Automatic total price calculation from components
- **Remove Actions**: Remove component products or account mappings

### **Interactive Elements:**
- **Product Type Confirmation**: Warn user before changing type (data loss)
- **Conditional Visibility**: Show/hide sections based on product type
- **Tab Navigation**: Switch between bundle configuration tabs
- **Dynamic Tables**: Add/remove bundle components and account mappings
- **Real-time Calculations**: Update bundle totals as components change

### **Validation Indicators:**
- **Required fields** marked with asterisks (*)
- **Conditional requirements** based on checkbox states
- **Product type** selection required before accessing conditional content
- **Image format** restrictions clearly specified

### **Business Logic Integration:**
- **Chart of Accounts**: Integration for purchase, sales, and inventory accounts
- **Tax Management**: PPN (11%) tax configuration for purchase and sales
- **Inventory Control**: Stock monitoring with minimum stock alerts
- **Bundle Composition**: Component product management with quantity tracking

### **Advanced Features:**
- **Stock Monitoring**: Optional inventory tracking with minimum stock levels
- **Batch Tracking**: Advanced inventory tracking for batch-controlled products
- **Account Flexibility**: Different accounts for purchase, sales, and inventory
- **Bundle Pricing**: Automatic pricing calculation from component products

### **File Management:**
- **Image Upload**: Product image with size and format restrictions
- **Format Support**: JPG and PNG image formats
- **Size Limit**: Maximum 5MB file size
- **Preview**: Visual preview of uploaded product image

### **Confirmation Logic:**
- **Type Change Warning**: Confirm before changing product type
- **Data Reset**: Clear conditional data when changing product type
- **Validation**: Ensure required fields completed before submission
- **Error Handling**: Clear validation messages and error states

### **Navigation Flow:**
- **Sequential Entry**: Basic info → Product type → Conditional details
- **Tab Management**: Switch between bundle configuration tabs
- **Save States**: Preserve data within selected product type
- **Cancel Options**: Exit with confirmation to prevent data loss

### **Integration Points:**
- **Product Catalog**: Add to main product inventory
- **Chart of Accounts**: Link to accounting system
- **Inventory Management**: Enable stock tracking and monitoring
- **Pricing System**: Establish purchase and sales pricing

This product form provides comprehensive product management supporting both simple individual products and complex bundle products while maintaining proper financial controls and integration with inventory management and accounting systems. The dynamic interface adapts to the selected product type, providing relevant fields and functionality for each scenario.