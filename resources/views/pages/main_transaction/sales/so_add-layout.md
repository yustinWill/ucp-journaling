# Sales Order (SO) Form - Visual Layout

## ASCII Layout Representation

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ Header                                                                                                      │
│ Menu                                                                                                        │
│                                                                                                             │
│    Buat Pemesanan Penjualan                                                                               │
│                                                                                                             │
│    Informasi Transaksi                                                                                     │
│    ─────────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐                              │
│    │ Pelanggan*                      │  │ Email*                          │                              │
│    │ Pilih Pelanggan             ▼   │  │                                 │                              │
│    └─────────────────────────────────┘  └─────────────────────────────────┘                              │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐  ┌─────────────────────────┐ │
│    │ Alamat*                         │  │ Tanggal Transaksi*              │  │ Nomor Transaksi*        │ │
│    │ autofill but editable           │  │                                 │  │ autofill but editable   │ │
│    │                                 │  │                                 │  │                         │ │
│    └─────────────────────────────────┘  └─────────────────────────────────┘  └─────────────────────────┘ │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐  ┌─────────────────────────┐ │
│    │ Tag                             │  │ Syarat Pembayaran*              │  │ Nomor Referensi Pelanggan│ │
│    │                                 │  │ Net 30                      ▼   │  │                         │ │
│    └─────────────────────────────────┘  └─────────────────────────────────┘  └─────────────────────────┘ │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐                              │
│    │ Tanggal Jatuh Tempo*            │  │ Gudang                          │                              │
│    │ autofill                        │  │ Pilih Gudang                ▼   │                              │
│    └─────────────────────────────────┘  └─────────────────────────────────┘                              │
│                                                                                                             │
│    Informasi Penjualan                                    ☐ Harga Termasuk Pajak                         │
│    ─────────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    ┌─────────────────────────────────────────────────────────────────────────────────────────────────────┐ │
│    │ Produk     │ Deskripsi          │ Kuantitas │ Unit │ Harga Satuan │ Diskon │ Pajak │ Jumlah         │ │
│    │────────────────────────────────────────────────────────────────────────────────────────────────────│ │
│    │ ALBUM SVT▼ │ ALBUM SVT VOL 1    │ 100       │ pcs  │ Rp 250,000.00│ 5%     │ PPN▼  │ Rp 25,000,000  │ │
│    │ Pilih Produk▼│                  │           │      │ Rp           │ 0%     │ PPN▼  │ Rp             │ │
│    │ Pilih Produk▼│                  │           │      │ Rp           │ 0%     │ PPN▼  │ Rp             │ │
│    │ Pilih Produk▼│                  │           │      │ Rp           │ 0%     │ PPN▼  │ Rp             │ │
│    └─────────────────────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                                             │
│    ┌─────────────────────────────────┐              Subtotal                    Rp      12,000,000.00    │
│    │ Pesan                           │              Diskon Perbaris             Rp         300,000.00    │
│    │                                 │              Diskon                  5%  Rp         600,000.00    │
│    │                                 │              Pajak                       Rp       1,221,000.00    │
│    │                                 │              Total                       Rp      12,321,000.00    │
│    └─────────────────────────────────┘              Sisa Tagihan               Rp      12,321,000.00    │
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
│ │ Pelanggan* [Dropdown]       │    │ Email* [Text Input]         │                      │
│ │ "Pilih Pelanggan"           │    │ [Auto-filled]               │                      │
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
│ │ Tag [Text Input]            │ │ Syarat Pembayaran*          │ │ Nomor Ref Pelanggan  │ │
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
│ Informasi Penjualan                                      [☐] Harga Termasuk Pajak       │
│ ─────────────────────────────────────────────────────────────────────────────────────────│
│                                                                                          │
│ ┌─ 15% ──┐ ┌─ 20% ──┐ ┌─ 10% ┐ ┌─ 8% ┐ ┌─ 12% ─┐ ┌─ 10% ┐ ┌─ 10% ┐ ┌─ 15% ────────┐ │
│ │ Produk │ │ Desk   │ │ Qty  │ │Unit│ │ H.Sat  │ │Diskon │ │ Pajak │ │ Jumlah       │ │
│ ├────────┤ ├────────┤ ├──────┤ ├────┤ ├────────┤ ├───────┤ ├───────┤ ├──────────────┤ │
│ │ALBUM   │ │ALBUM   │ │ 100  ││pcs │ │Rp 250k │ │  5%   │ │ PPN   │ │ Rp 25,000k   │ │
│ │SVT     │ │SVT V1  │ │      ││    │ │        │ │       │ │[Drop] │ │              │ │
│ ├────────┤ ├────────┤ ├──────┤ ├────┤ ├────────┤ ├───────┤ ├───────┤ ├──────────────┤ │
│ │Pilih   │ │        │ │      ││    │ │ Rp     │ │  0%   │ │ PPN   │ │ Rp           │ │
│ │Produk  │ │        │ │      ││    │ │        │ │       │ │       │ │              │ │
│ └────────┘ └────────┘ └──────┘ └────┘ └────────┘ └───────┘ └───────┘ └──────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ BOTTOM SECTION ─────────────────────────────────────────────────────────────────────────┐
│ ┌─ LEFT COLUMN (COL 6) ──────────────────────────────────┐ ┌─ RIGHT COLUMN (COL 6) ────┐ │
│ │ ┌─ Pesan ─────────────────────────────────────────────┐ │ │ Subtotal         Rp 12M  │ │
│ │ │ [Textarea - 4 rows]                                 │ │ │ Diskon Perbaris  Rp 300k │ │
│ │ │                                                     │ │ │ Diskon        5% Rp 600k │ │
│ │ └─────────────────────────────────────────────────────┘ │ │ Pajak            Rp 1.2M │ │
│ │                                                         │ │ Total            Rp 12.3M│ │
│ │ ┌─ Memo ──────────────────────────────────────────────┐ │ │ Sisa Tagihan     Rp 12.3M│ │
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
Row 1: [Pelanggan - 6 cols] [Email - 6 cols]
Row 2: [Alamat - 6 cols] [Tanggal - 3 cols] [Nomor - 3 cols]
Row 3: [Tag - 3 cols] [Payment - 3 cols] [Ref - 3 cols]
Row 4: [Due Date - 6 cols] [Gudang - 6 cols]
Table: [Product 15%] [Desc 20%] [Qty 10%] [Unit 8%] [Price 12%] [Discount 10%] [Tax 10%] [Total 15%]
Bottom: [Left Content - 6 cols] [Summary - 6 cols]
```

### Tablet (8 columns)
```
Row 1: [Pelanggan - 4 cols] [Email - 4 cols]
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

### **Sales-Specific Elements:**
- **Customer Focus**: "Pelanggan" instead of "Supplier"
- **Sales Terminology**: "Informasi Penjualan" section header
- **Customer Reference**: Field for customer's internal reference/PO number
- **Revenue Orientation**: Sales-focused workflow and terminology

### **Enhanced Product Table (8 Columns):**
- **Product Selection**: Dropdown with product options
- **Description**: Auto-filled product descriptions
- **Quantity**: Sales quantities
- **Unit**: Product units of measurement
- **Unit Price**: Sales pricing
- **Discount Column**: Line-level discount percentages (5% shown)
- **Tax**: PPN tax selection
- **Amount**: Calculated line totals

### **Advanced Summary Calculations:**
- **Subtotal**: Rp 12,000,000.00 (before discounts and tax)
- **Diskon Perbaris**: Rp 300,000.00 (line-level discounts)
- **Diskon**: 5% = Rp 600,000.00 (order-level discount)
- **Pajak**: Rp 1,221,000.00 (tax on discounted amount)
- **Total**: Rp 12,321,000.00 (final sales amount)
- **Sisa Tagihan**: Rp 12,321,000.00 (outstanding balance)

### **Multi-Level Discount System:**
1. **Line Discounts**: Applied to individual products (5% in sample)
2. **Order Discount**: Applied to subtotal after line discounts (5% order-level)
3. **Tax Calculation**: Applied to final discounted amount
4. **Complex Calculation**: Proper discount sequence and tax handling

### **Key Differences from Purchase Forms:**
- **Customer vs Supplier**: Different stakeholder orientation
- **Discount Focus**: Enhanced discount capabilities for sales negotiations
- **Revenue Tracking**: Sales-specific financial calculations
- **Customer Service**: Enhanced customer relationship features

### **Key Differences from Purchase Order:**
- **Discount Column**: Additional column for line-level discounts
- **Enhanced Summary**: More complex financial calculations
- **Customer Fields**: Customer-specific reference fields
- **Sales Process**: Revenue-focused business flow

### **Validation Indicators:**
- **Required fields** marked with asterisks (*)
- **Auto-fill placeholders** clearly marked
- **Dropdown indicators** shown with ▼ symbols
- **Checkbox** for tax inclusion option
- **Discount percentages** with % symbols

### **Interactive Features:**
- **Customer Selection**: Auto-populates email and address
- **Product Selection**: Auto-fills description, unit, and price
- **Discount Calculations**: Real-time discount and total updates
- **Tax Mode Toggle**: Switch between tax-inclusive/exclusive
- **Multi-level Calculations**: Complex discount and tax processing
│ │