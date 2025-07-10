# Purchase Invoice (PI) Form - Visual Layout

## ASCII Layout Representation

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ Header                                                                                                      │
│ Menu                                                                                                        │
│                                                                                                             │
│    Buat Faktur Pembelian                                                                                   │
│                                                                                                             │
│    Informasi Transaksi                                                                                     │
│    ─────────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐                              │
│    │ Supplier*                       │  │ Email*                          │                              │
│    │ John                        ▼   │  │                                 │                              │
│    └─────────────────────────────────┘  └─────────────────────────────────┘                              │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐  ┌─────────────────────────┐ │
│    │ Alamat*                         │  │ Tanggal Transaksi*              │  │ Nomor Transaksi*        │ │
│    │ autofill but editable           │  │                                 │  │ autofill but editable   │ │
│    │                                 │  │                                 │  │                         │ │
│    └─────────────────────────────────┘  └─────────────────────────────────┘  └─────────────────────────┘ │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐  ┌─────────────────────────┐ │
│    │ Nomor Pemesanan Pembelian       │  │ Syarat Pembayaran*              │  │ Nomor Referensi Supplier│ │
│    │ Purchase Order 110001       🔗  │  │ Net 30                      ▼   │  │                         │ │
│    └─────────────────────────────────┘  └─────────────────────────────────┘  └─────────────────────────┘ │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐  ┌─────────────────────────┐ │
│    │ Tag                             │  │ Tanggal Jatuh Tempo*            │  │ Gudang                  │ │
│    │                                 │  │ autofill                        │  │ Pilih Gudang        ▼   │ │
│    └─────────────────────────────────┘  └─────────────────────────────────┘  └─────────────────────────┘ │
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
│ │ "John"                      │    │ [Auto-filled]               │                      │
│ └─────────────────────────────┘    └─────────────────────────────┘                      │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ ROW 2 ──────────────────────────────────────────────────────────────────────────────────┐
│ ┌─ COL 6 ─────────────────────┐ ┌─ COL 3 ─────────────────────┐ ┌─ COL 3 ──────────────┐ │
│ │ Alamat* [Textarea]          │ │ Tanggal Transaksi*          │ │ Nomor Transaksi*     │ │
│ │ "autofill but editable"     │ │ [Date Picker]               │ │ "autofill but edit"  │ │
│ └─────────────────────────────┘ └─────────────────────────────┘ └──────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ ROW 3 ──────────────────────────────────────────────────────────────────────────────────┐
│ ┌─ COL 4 ─────────────────────┐ ┌─ COL 4 ─────────────────────┐ ┌─ COL 4 ──────────────┐ │
│ │ Nomor Pemesanan Pembelian   │ │ Syarat Pembayaran*          │ │ Nomor Ref Supplier   │ │
│ │ "Purchase Order 110001" 🔗  │ │ "Net 30" [Dropdown]         │ │ [Text Input]         │ │
│ └─────────────────────────────┘ └─────────────────────────────┘ └──────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ ROW 4 ──────────────────────────────────────────────────────────────────────────────────┐
│ ┌─ COL 4 ─────────────────────┐ ┌─ COL 4 ─────────────────────┐ ┌─ COL 4 ──────────────┐ │
│ │ Tag [Text Input]            │ │ Tanggal Jatuh Tempo*        │ │ Gudang [Dropdown]    │ │
│ │                             │ │ "autofill" [Date Picker]    │ │ "Pilih Gudang"       │ │
│ └─────────────────────────────┘ └─────────────────────────────┘ └──────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ PRODUCT SECTION ────────────────────────────────────────────────────────────────────────┐
│ Informasi Pembelian                                      [☐] Harga Termasuk Pajak       │
│ ─────────────────────────────────────────────────────────────────────────────────────────│
│                                                                                          │
│ ┌─ 15% ──────┐ ┌─ 20% ──────┐ ┌─ 10% ─┐ ┌─ 8% ─┐ ┌─ 12% ─────┐ ┌─ 10% ─┐ ┌─ 15% ─────┐ │
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
Row 3: [PO Reference - 4 cols] [Payment - 4 cols] [Ref - 4 cols]
Row 4: [Tag - 4 cols] [Due Date - 4 cols] [Gudang - 4 cols]
Table: [Product 15%] [Desc 20%] [Qty 10%] [Unit 8%] [Price 12%] [Tax 10%] [Total 15%]
Bottom: [Left Content - 6 cols] [Summary - 6 cols]
```

### Tablet (8 columns)
```
Row 1: [Supplier - 4 cols] [Email - 4 cols]
Row 2: [Alamat - 8 cols]
Row 3: [Tanggal - 4 cols] [Nomor - 4 cols]
Row 4: [PO Reference - 4 cols] [Payment - 4 cols]
Row 5: [Ref - 4 cols] [Tag - 4 cols]
Row 6: [Due Date - 4 cols] [Gudang - 4 cols]
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

### **Enhanced Transaction Info:**
- **4 Rows**: More comprehensive than PD, includes payment terms and due dates
- **PO Integration**: Purchase Order reference with clickable link (🔗)
- **Financial Focus**: Payment terms, due dates, and financial calculations
- **Address Field**: Full supplier address for invoice processing

### **Complete Product Table:**
- **7 Columns**: Full financial tracking including pricing and tax
- **Tax Calculations**: PPN handling and inclusive/exclusive options
- **Real-time Calculations**: Live total updates
- **Currency Formatting**: Indonesian Rupiah formatting

### **Split Bottom Section:**
- **Left**: Documentation (Pesan, Memo, Lampiran)
- **Right**: Financial Summary (Subtotal, Pajak, Total, Sisa Tagihan)
- **Balanced Layout**: 6-6 column split for comprehensive view

### **Key Differences from PO:**
- **PO Reference**: Links to existing purchase order
- **Invoice Focus**: Financial processing and payment tracking
- **Due Date Management**: Payment terms and scheduling
- **Account Payable**: Integration with financial systems

### **Key Differences from PD:**
- **Financial Calculations**: Full pricing, tax, and total calculations
- **Payment Integration**: Due dates and payment terms
- **Complex Product Table**: Price and tax columns included
- **Summary Section**: Financial totals and balance tracking

### **Validation Indicators:**
- **Required fields** marked with asterisks (*)
- **Auto-fill placeholders** clearly marked
- **Dropdown indicators** shown with ▼ symbols
- **Integration links** shown with 🔗 symbols
- **Checkbox** for tax inclusion option