# Purchase Delivery (PD) Form - Visual Layout

## ASCII Layout Representation

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ Header                                                                                                      │
│ Menu                                                                                                        │
│                                                                                                             │
│    Buat Pengiriman Pembelian                                                                              │
│                                                                                                             │
│    Informasi Transaksi                                                                                     │
│    ─────────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐                              │
│    │ Supplier*                       │  │ Email*                          │                              │
│    │ John                        ▼   │  │                                 │                              │
│    └─────────────────────────────────┘  └─────────────────────────────────┘                              │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐                              │
│    │ Tanggal Pengiriman*             │  │ Nomor Transaksi*                │                              │
│    │                                 │  │ autofill but editable           │                              │
│    └─────────────────────────────────┘  └─────────────────────────────────┘                              │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐  ┌─────────────────────────┐ │
│    │ Nomor Referensi Pembelian       │  │ Nomor Referensi Supplier        │  │ Tag                     │ │
│    │ Kerjasama Utama 11008       🔗  │  │                                 │  │                         │ │
│    └─────────────────────────────────┘  └─────────────────────────────────┘  └─────────────────────────┘ │
│                                                                                                             │
│    ┌─────────────────────────────────┐                                                                    │
│    │ Gudang                          │                                                                    │
│    │ Pilih Gudang                ▼   │                                                                    │
│    └─────────────────────────────────┘                                                                    │
│                                                                                                             │
│    ┌─────────────────────────────────────────────────────────────────────────────────────────────────────┐ │
│    │ Nama Produk              │ Deskripsi                    │ Kuantitas │ Unit                          │ │
│    │─────────────────────────────────────────────────────────────────────────────────────────────────────│ │
│    │ Robot plastik 600 ml     │ mainan keramgelit            │ 1         │ pcs                           │ │
│    └─────────────────────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                                             │
│    ┌─────────────────────────────────┐                                                                    │
│    │ Pesan                           │                                                                    │
│    │                                 │                                                                    │
│    │                                 │                                                                    │
│    │                                 │                                                                    │
│    └─────────────────────────────────┘                                                                    │
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
│ ┌─ COL 6 ─────────────────────┐    ┌─ COL 6 ─────────────────────┐                      │
│ │ Tanggal Pengiriman*         │    │ Nomor Transaksi*            │                      │
│ │ [Date Picker]               │    │ "autofill but editable"     │                      │
│ └─────────────────────────────┘    └─────────────────────────────┘                      │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ ROW 3 ──────────────────────────────────────────────────────────────────────────────────┐
│ ┌─ COL 4 ─────────────────────┐ ┌─ COL 4 ─────────────────────┐ ┌─ COL 4 ──────────────┐ │
│ │ Nomor Ref Pembelian         │ │ Nomor Ref Supplier          │ │ Tag                  │ │
│ │ "Kerjasama Utama 11008" 🔗  │ │ [Text Input]                │ │ [Text Input]         │ │
│ └─────────────────────────────┘ └─────────────────────────────┘ └──────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ ROW 4 ──────────────────────────────────────────────────────────────────────────────────┐
│ ┌─ COL 6 ─────────────────────┐                                                          │
│ │ Gudang [Dropdown]           │                                                          │
│ │ "Pilih Gudang"              │                                                          │
│ └─────────────────────────────┘                                                          │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ PRODUCT TABLE ──────────────────────────────────────────────────────────────────────────┐
│ ┌─ 25% ──────────────┐ ┌─ 40% ─────────────────┐ ┌─ 15% ──┐ ┌─ 20% ─────────────────┐ │
│ │ Nama Produk        │ │ Deskripsi             │ │ Qty    │ │ Unit                  │ │
│ ├────────────────────┤ ├───────────────────────┤ ├────────┤ ├───────────────────────┤ │
│ │ Robot plastik      │ │ mainan keramgelit     │ │ 1      │ │ pcs                   │ │
│ │ 600 ml             │ │                       │ │        │ │                       │ │
│ └────────────────────┘ └───────────────────────┘ └────────┘ └───────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ BOTTOM SECTION ─────────────────────────────────────────────────────────────────────────┐
│ ┌─ SINGLE COLUMN (COL 12) ───────────────────────────────────────────────────────────────┐ │
│ │ ┌─ Pesan ─────────────────────────────────────────────┐                               │ │
│ │ │ [Textarea - 4 rows]                                 │                               │ │
│ │ │                                                     │                               │ │
│ │ └─────────────────────────────────────────────────────┘                               │ │
│ │                                                                                       │ │
│ │ ┌─ Memo ──────────────────────────────────────────────┐                               │ │
│ │ │ [Textarea - 4 rows]                                 │                               │ │
│ │ │                                                     │                               │ │
│ │ └─────────────────────────────────────────────────────┘                               │ │
│ │                                                                                       │ │
│ │ Lampiran                                                                              │ │
│ │ ┌─ Choose File ───────────────────────────────────────┐                               │ │
│ │ └─────────────────────────────────────────────────────┘                               │ │
│ │ File dapat berupa Excel, Word, PDF, PNG, atau Zip                                    │ │
│ └───────────────────────────────────────────────────────────────────────────────────────┘ │
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
Row 2: [Tanggal - 6 cols] [Nomor - 6 cols]
Row 3: [Ref Pembelian - 4 cols] [Ref Supplier - 4 cols] [Tag - 4 cols]
Row 4: [Gudang - 6 cols]
Table: [Product 25%] [Description 40%] [Qty 15%] [Unit 20%]
Bottom: [Full Content - 12 cols]
```

### Tablet (8 columns)
```
Row 1: [Supplier - 4 cols] [Email - 4 cols]
Row 2: [Tanggal - 4 cols] [Nomor - 4 cols]
Row 3: [Ref Pembelian - 4 cols] [Ref Supplier - 4 cols]
Row 4: [Tag - 4 cols] [Gudang - 4 cols]
Table: Horizontal scroll
Bottom: [Full Content - 8 cols]
```

### Mobile (4 columns)
```
All fields: [Full width - 4 cols]
Stacked vertically
Table: Horizontal scroll or card layout
Buttons: Full width stacked
```

## Key Layout Features

### **Simplified Structure:**
- **No Financial Calculations**: Focus on delivery documentation only
- **Simple Product Table**: Only product name, description, quantity, unit
- **Single Bottom Section**: No split layout for summary calculations
- **Integration Button**: PO reference with clickable link (🔗)

### **Interactive Elements:**
- **Dropdowns**: Supplier, Gudang
- **Auto-fill**: Email, Transaction Number
- **PO Integration**: Clickable reference link
- **File Upload**: Document attachment capability

### **Form Sections:**
1. **Transaction Info**: 4-row supplier and delivery details
2. **Product Table**: Simple 4-column delivery tracking
3. **Documentation**: Message, memo, and file attachments
4. **Actions**: Cancel and submit buttons

### **Key Differences from PO:**
- **No Payment Terms**: Delivery tracking doesn't require payment information
- **No Financial Data**: No pricing, tax, or total calculations
- **Simplified Product Table**: Only tracks delivered quantities vs. ordered amounts
- **PO Reference Integration**: Links back to original purchase order
- **Single Column Bottom**: No summary calculations needed
- **Focus on Logistics**: Delivery-specific fields and documentation

### **Validation Indicators:**
- **Required fields** marked with asterisks (*)
- **Auto-fill placeholders** clearly marked
- **Dropdown indicators** shown with ▼ symbols
- **Integration links** shown with 🔗 symbols