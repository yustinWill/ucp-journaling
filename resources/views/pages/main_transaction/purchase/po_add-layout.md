# Purchase Order (PO) Form - Visual Layout

## ASCII Layout Representation

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ Header                                                                                                      │
│ Menu                                                                                                        │
│                                                                                                             │
│    Buat Pemesanan Pembelian                                                                               │
│                                                                                                             │
│    Informasi Transaksi                                                                                     │
│    ─────────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐                              │
│    │ Supplier*                       │  │ Email*                          │                              │
│    │ Pilih Supplier              ▼   │  │                                 │                              │
│    └─────────────────────────────────┘  └─────────────────────────────────┘                              │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐  ┌─────────────────────────┐ │
│    │ Alamat*                         │  │ Tanggal Transaksi*              │  │ Nomor Transaksi*        │ │
│    │ autofill but editable           │  │                                 │  │ autofill but editable   │ │
│    │                                 │  │                                 │  │                         │ │
│    └─────────────────────────────────┘  └─────────────────────────────────┘  └─────────────────────────┘ │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐  ┌─────────────────────────┐ │
│    │ Tag                             │  │ Syarat Pembayaran*              │  │ Nomor Referensi Supplier│ │
│    │                                 │  │ Net 30                      ▼   │  │                         │ │
│    └─────────────────────────────────┘  └─────────────────────────────────┘  └─────────────────────────┘ │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐                              │
│    │ Tanggal Jatuh Tempo*            │  │ Gudang                          │                              │
│    │ autofill                        │  │ Pilih Gudang                ▼   │                              │
│    └─────────────────────────────────┘  └─────────────────────────────────┘                              │
│                                                                                                             │
│    Informasi Pembelian                                    ☐ Harga Termasuk Pajak                         │
│    ─────────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    ┌─────────────────────────────────────────────────────────────────────────────────────────────────────┐ │
│    │ Produk     │ Deskripsi          │ Kuantitas │ Unit │ Harga Satuan │ Pajak │ Jumlah                 │ │
│    │────────────────────────────────────────────────────────────────────────────────────────────────────│ │
│    │ ALBUM SVT▼ │ ALBUM SVT VOL 1    │ 100       │ pcs  │ Rp 250,000.00│ PPN▼  │ Rp 25,000,000.00       │ │
│    │ Pilih Produk▼│                  │           │      │ Rp           │ PPN▼  │ Rp                     │ │
│    │ Pilih Produk▼│                  │           │      │ Rp           │ PPN▼  │ Rp                     │ │
│    │ Pilih Produk▼│                  │           │      │ Rp           │ PPN▼  │ Rp                     │ │
│    └─────────────────────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                                             │
│    ┌─────────────────────────────────┐                      Subtotal                    Rp             - │
│    │ Pesan                           │                      Pajak                       Rp             - │
│    │                                 │                      Total                       Rp             - │
│    │                                 │                      Sisa Tagihan               Rp             - │
│    │                                 │                                                                   │
│    └─────────────────────────────────┘                                                                   │
│                                                                                                             │
│    ┌─────────────────────────────────┐                                                                    │
│    │ Memo                            │                                                                    │
│    │                                 │                                                                    │
│    │                                 │                                                                    │
│    │                                 │                                                                    │
│    └─────────────────────────────────┘                                                                    │
│                                                                                                             │
│    Lampiran                                                                                                │
│    ┌─────────────────────────────────┐                           ┌─────────────┐ ┌─────────────┐         │
│    │ Choose File                     │                           │ Batalkan    │ │ Buat        │         │
│    └─────────────────────────────────┘                           └─────────────┘ └─────────────┘         │
│    File dapat berupa Excel, Word, PDF, PNG, atau Zip                                                      │
│                                                                                                             │
└─────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

## Grid-Based Layout

```
┌─ ROW 1 ──────────────────────────────────────────────────────────────────────────────────┐
│ ┌─ COL 6 ─────────────────────┐    ┌─ COL 6 ─────────────────────┐                      │
│ │ Supplier* [Dropdown]        │    │ Email* [Text Input]         │                      │
│ │ "Pilih Supplier"            │    │ [Auto-filled]               │                      │
│ └─────────────────────────────┘    └─────────────────────────────┘                      │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ ROW 2 ──────────────────────────────────────────────────────────────────────────────────┐
│ ┌─ COL 6 ─────────────────────┐ ┌─ COL 3 ─────────────────────┐ ┌─ COL 3 ──────────────┐ │
│ │ Alamat* [Textarea]          │ │ Tanggal Transaksi*          │ │ Nomor Transaksi*     │ │
│ │ "autofill but editable"     │ │ [Date Picker]               │ │ "autofill but edit"  │ │
│ └─────────────────────────────┘ └─────────────────────────────┘ └──────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ ROW 3 ──────────────────────────────────────────────────────────────────────────────────┐
│ ┌─ COL 3 ─────────────────────┐ ┌─ COL 3 ─────────────────────┐ ┌─ COL 3 ──────────────┐ │
│ │ Tag [Text Input]            │ │ Syarat Pembayaran*          │ │ Nomor Ref Supplier   │ │
│ │                             │ │ "Net 30" [Dropdown]         │ │ [Text Input]         │ │
│ └─────────────────────────────┘ └─────────────────────────────┘ └──────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ ROW 4 ──────────────────────────────────────────────────────────────────────────────────┐
│ ┌─ COL 6 ─────────────────────┐    ┌─ COL 6 ─────────────────────┐                      │
│ │ Tanggal Jatuh Tempo*        │    │ Gudang [Dropdown]           │                      │
│ │ "autofill" [Date Picker]    │    │ "Pilih Gudang"              │                      │
│ └─────────────────────────────┘    └─────────────────────────────┘                      │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ PRODUCT SECTION ────────────────────────────────────────────────────────────────────────┐
│ Informasi Pembelian                                      [☐] Harga Termasuk Pajak       │
│ ─────────────────────────────────────────────────────────────────────────────────────────│
│                                                                                          │
│ ┌─ 20% ──────┐ ┌─ 20% ──────┐ ┌─ 10% ─┐ ┌─ 8% ─┐ ┌─ 15% ─────┐ ┌─ 12% ─┐ ┌─ 15% ─────┐ │
│ │ Produk     │ │ Deskripsi  │ │ Qty   │ │ Unit │ │ Harga Sat  │ │ Pajak │ │ Jumlah     │ │
│ ├────────────┤ ├────────────┤ ├───────┤ ├──────┤ ├───────────┤ ├───────┤ ├───────────┤ │
│ │ ALBUM SVT  │ │ ALBUM SVT  │ │ 100   │ │ pcs  │ │ Rp 250,000│ │ PPN   │ │ Rp 25,000k│ │
│ │ [Dropdown] │ │ VOL 1      │ │       │ │      │ │           │ │[Drop] │ │           │ │
│ ├────────────┤ ├────────────┤ ├───────┤ ├──────┤ ├───────────┤ ├───────┤ ├───────────┤ │
│ │ Pilih      │ │            │ │       │ │      │ │ Rp        │ │ PPN   │ │ Rp        │ │
│ │ Produk     │ │            │ │       │ │      │ │           │ │       │ │           │ │
│ └────────────┘ └────────────┘ └───────┘ └──────┘ └───────────┘ └───────┘ └───────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ BOTTOM SECTION ─────────────────────────────────────────────────────────────────────────┐
│ ┌─ LEFT COLUMN (COL 6) ──────────────────────────────────┐ ┌─ RIGHT COLUMN (COL 6) ────┐ │
│ │ ┌─ Pesan ─────────────────────────────────────────────┐ │ │ Subtotal    Rp        - │ │
│ │ │ [Textarea - 4 rows]                                 │ │ │ Pajak       Rp        - │ │
│ │ │                                                     │ │ │ Total       Rp        - │ │
│ │ └─────────────────────────────────────────────────────┘ │ │ Sisa Tagihan Rp       - │ │
│ │                                                         │ │                           │ │
│ │ ┌─ Memo ──────────────────────────────────────────────┐ │ │                           │ │
│ │ │ [Textarea - 4 rows]                                 │ │ │                           │ │
│ │ │                                                     │ │ │                           │ │
│ │ └─────────────────────────────────────────────────────┘ │ │                           │ │
│ │                                                         │ │                           │ │
│ │ Lampiran                                                │ │                           │ │
│ │ ┌─ Choose File ───────────────────────────────────────┐ │ │                           │ │
│ │ └─────────────────────────────────────────────────────┘ │ │                           │ │
│ └─────────────────────────────────────────────────────────┘ └───────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ ACTION BUTTONS ─────────────────────────────────────────────────────────────────────────┐
│                                                          ┌─────────────┐ ┌─────────────┐ │
│                                                          │ Batalkan    │ │ Buat        │ │
│                                                          │ [Red]       │ │ [Green]     │ │
│                                                          └─────────────┘ └─────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘
```

## Responsive Breakpoints

### Desktop (12 columns)
```
Row 1: [Supplier - 6 cols] [Email - 6 cols]
Row 2: [Alamat - 6 cols] [Tanggal - 3 cols] [Nomor - 3 cols]
Row 3: [Tag - 3 cols] [Payment - 3 cols] [Ref - 3 cols]
Row 4: [Due Date - 6 cols] [Gudang - 6 cols]
Table: [Product 20%] [Desc 20%] [Qty 10%] [Unit 8%] [Price 15%] [Tax 12%] [Total 15%]
Bottom: [Left Content - 6 cols] [Summary - 6 cols]
```

### Tablet (8 columns)
```
Row 1: [Supplier - 4 cols] [Email - 4 cols]
Row 2: [Alamat - 8 cols]
Row 3: [Tanggal - 4 cols] [Nomor - 4 cols]
Row 4: [Tag - 4 cols] [Payment - 4 cols]
Row 5: [Ref - 4 cols] [Due Date - 4 cols]
Row 6: [Gudang - 4 cols]
Table: Horizontal scroll
Bottom: [Left Content - 8 cols] [Summary - 8 cols stacked]
```

### Mobile (4 columns)
```
All fields: [Full width - 4 cols] stacked vertically
Product table: Horizontal scroll or card layout
Summary: Full width stacked
Buttons: Full width stacked
```

## Key Layout Features

### **Interactive Elements:**
- **Dropdowns**: Supplier, Payment Terms, Gudang, Product, Pajak
- **Auto-fill**: Email, Address, Transaction Number, Due Date
- **Calculations**: Real-time total calculations
- **File Upload**: Document attachment capability

### **Form Sections:**
1. **Transaction Info**: 4-row supplier and order details
2. **Product Table**: Dynamic multi-column product entry
3. **Additional Info**: Message, memo, and file attachments
4. **Summary**: Financial calculations and totals
5. **Actions**: Cancel and submit buttons

### **Validation Indicators:**
- **Required fields** marked with asterisks (*)
- **Auto-fill placeholders** clearly marked
- **Dropdown indicators** shown with ▼ symbols
- **Checkbox** for tax inclusion option