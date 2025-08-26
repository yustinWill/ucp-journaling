# Production (Produksi) Detail Form - Visual Layout

## ASCII Layout Representation

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ Header                                                                                                      │
│ Menu                                                                                                        │
│                                                                                                             │
│    Detail Produksi Produk #1001                                                                           │
│                                                                                                             │
│    Riyo Botol 600ml                                                                                        │
│    ─────────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐                              │
│    │ Tanggal Produksi Produk         │  │ Batch Produksi                  │                              │
│    │ 15-Oct-25                       │  │ 2                               │                              │
│    └─────────────────────────────────┘  └─────────────────────────────────┘                              │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐                              │
│    │ Produk Bundling                 │  │ Jumlah Produksi                 │                              │
│    │ Riyo Botol 600ml            ▼   │  │ 5000                        pcs │                              │
│    └─────────────────────────────────┘  └─────────────────────────────────┘                              │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐                              │
│    │ Kode Produk*                    │  │ Kategori Produk                 │                              │
│    │ #1001                           │  │ Air Mineral Botol           ▼   │                              │
│    └─────────────────────────────────┘  └─────────────────────────────────┘                              │
│                                                                                                             │
│    Info Detail Produksi                                                                                   │
│    ─────────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    ┌─────────────────────────────────────────────────────────────────────────────────────────────────── │
│    │ Nama Produk │ Harga    │ Jumlah │ Unit │ Total                                                     │ │
│    ├─────────────┼──────────┼────────┼──────┼─────────────────────────────────────────────────────────── │
│    │ Lakban      │ Rp  2,500│    200 │ pcs  │ Rp      500,000                                         │ │
│    │ Botol       │ Rp    500│   5000 │ pcs  │ Rp    2,500,000                                         │ │
│    │ Tutup Botol │ Rp    100│  50000 │ pcs  │ Rp    5,000,000                                         │ │
│    │ Label       │ Rp    100│    200 │ pcs  │ Rp       20,000                                         │ │
│    │ Karton      │ Rp  1,000│    200 │ pcs  │ Rp      200,000                                         │ │
│    ├─────────────┼──────────┼────────┼──────┼─────────────────────────────────────────────────────────── │
│    │ Akun Biaya  │ Harga    │        │      │ Total                                                   │ │
│    │ Biaya       │ Rp5,000,000│      │      │ Rp     5,000,000                                        │ │
│    │ Produksi    │          │        │      │                                                         │ │
│    └─────────────┴──────────┴────────┴──────┴─────────────────────────────────────────────────────────── │
│                                                                                                             │
│                    ┌─────────────────────────────────────────────────────────────────────────────────── │
│                    │                    Total Biaya Bahan Baku        Rp    13,220,000               │ │
│                    │                    Biaya per unit                 Rp         2,644               │ │
│                    └─────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│                                                                  ┌─────────────┐                         │
│                                                                  │ Reversal    │                         │
│                                                                  │ [Green]     │                         │
│                                                                  └─────────────┘                         │
│                                                                                                             │
└─────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

## ASCII Layout Representation - Reversal Form

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ Header                                                                                                      │
│ Menu                                                                                                        │
│                                                                                                             │
│    Reversal Produksi Produk #1001                                                                         │
│                                                                                                             │
│    Riyo Botol 600ml                                                                                        │
│    ─────────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐                              │
│    │ Tanggal Produksi Produk         │  │ Jumlah Produksi                 │                              │
│    │ 15-Oct-25                       │  │ 5000                        pcs │                              │
│    └─────────────────────────────────┘  └─────────────────────────────────┘                              │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐                              │
│    │ Kode Produk*                    │  │ Kategori Produk                 │                              │
│    │ #1001                           │  │ Air Mineral Botol           ▼   │                              │
│    └─────────────────────────────────┘  └─────────────────────────────────┘                              │
│                                                                                                             │
│    Info Detail Produksi                                                                                   │
│    ─────────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    ┌─────────────────────────────────────────────────────────────────────────────────────────────────── │
│    │ Nama Produk │ Harga    │ Jumlah │ Unit │ Total                                                     │ │
│    ├─────────────┼──────────┼────────┼──────┼─────────────────────────────────────────────────────────── │
│    │ Lakban      │ Rp  2,500│    200 │ pcs  │ Rp      500,000                                         │ │
│    │ Botol       │ Rp    500│   5000 │ pcs  │ Rp    2,500,000                                         │ │
│    │ Tutup Botol │ Rp    100│  50000 │ pcs  │ Rp    5,000,000                                         │ │
│    │ Label       │ Rp    100│    200 │ pcs  │ Rp       20,000                                         │ │
│    │ Karton      │ Rp  1,000│    200 │ pcs  │ Rp      200,000                                         │ │
│    ├─────────────┼──────────┼────────┼──────┼─────────────────────────────────────────────────────────── │
│    │ Akun Biaya  │ Harga    │        │      │ Total                                                   │ │
│    │ Biaya       │ Rp5,000,000│      │      │ Rp     5,000,000                                        │ │
│    │ Produksi    │          │        │      │                                                         │ │
│    └─────────────┴──────────┴────────┴──────┴─────────────────────────────────────────────────────────── │
│                                                                                                             │
│                    ┌─────────────────────────────────────────────────────────────────────────────────── │
│                    │                    Total Biaya Bahan Baku        Rp    13,220,000               │ │
│                    │                    Biaya per unit                 Rp         2,644               │ │
│                    └─────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│                                         ┌─────────────┐ ┌─────────────┐                                  │
│                                         │ Batalkan    │ │ Simpan      │                                  │
│                                         │ [Red]       │ │ [Green]     │                                  │
│                                         └─────────────┘ └─────────────┘                                  │
│                                                                                                             │
└─────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

## Grid-Based Layout

```
┌─ HEADER ─────────────────────────────────────────────────────────────────────────────────┐
│ Detail Produksi Produk #1001                                                           │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ SECTION 1: PRODUCTION HEADER ──────────────────────────────────────────────────────────┐
│ Riyo Botol 600ml                                                                       │
│ ─────────────────────────────────────────────────────────────────────────────────────────│
│                                                                                          │
│ ┌─ ROW 1 ──────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 6 ─────────────────────┐    ┌─ COL 6 ─────────────────────┐                  │ │
│ │ │ Tanggal Produksi Produk     │    │ Batch Produksi              │                  │ │
│ │ │ [Display] "15-Oct-25"       │    │ [Display] "2"               │                  │ │
│ │ └─────────────────────────────┘    └─────────────────────────────┘                  │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ ROW 2 ──────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 6 ─────────────────────┐    ┌─ COL 6 ─────────────────────┐                  │ │
│ │ │ Produk Bundling             │    │ Jumlah Produksi             │                  │ │
│ │ │ [Display] "Riyo Botol 600ml"│    │ [Display] "5000 pcs"        │                  │ │
│ │ └─────────────────────────────┘    └─────────────────────────────┘                  │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ ROW 3 ──────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 6 ─────────────────────┐    ┌─ COL 6 ─────────────────────┐                  │ │
│ │ │ Kode Produk*                │    │ Kategori Produk             │                  │ │
│ │ │ [Display] "#1001"           │    │ [Display] "Air Mineral Botol"│                 │ │
│ │ └─────────────────────────────┘    └─────────────────────────────┘                  │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ SECTION 2: INFO DETAIL PRODUKSI ───────────────────────────────────────────────────────┐
│ Info Detail Produksi                                                                   │
│ ─────────────────────────────────────────────────────────────────────────────────────────│
│                                                                                          │
│ ┌─ PRODUCTION DETAILS TABLE ───────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 3 ──────┐ ┌─ COL 2 ──────┐ ┌─ COL 2 ──────┐ ┌─ COL 2 ──────┐ ┌─ COL 3 ──────┐ │ │
│ │ │ Nama Produk  │ │ Harga        │ │ Jumlah       │ │ Unit         │ │ Total        │ │ │
│ │ │ [Display]    │ │ [Display]    │ │ [Display]    │ │ [Display]    │ │ [Display]    │ │ │
│ │ └──────────────┘ └──────────────┘ └──────────────┘ └──────────────┘ └──────────────┘ │ │
│ │                                                                                      │ │
│ │ [Raw Materials Section:]                                                             │ │
│ │ Lakban | Rp 2,500 | 200 | pcs | Rp 500,000                                          │ │
│ │ Botol | Rp 500 | 5000 | pcs | Rp 2,500,000                                          │ │
│ │ Tutup Botol | Rp 100 | 50000 | pcs | Rp 5,000,000                                   │ │
│ │ Label | Rp 100 | 200 | pcs | Rp 20,000                                              │ │
│ │ Karton | Rp 1,000 | 200 | pcs | Rp 200,000                                          │ │
│ │                                                                                      │ │
│ │ [Production Costs Section:]                                                          │ │
│ │ Akun Biaya | Harga | [empty] | [empty] | Total                                      │ │
│ │ Biaya Produksi | Rp 5,000,000 | [empty] | [empty] | Rp 5,000,000                   │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ FINANCIAL SUMMARY ──────────────────────────────────────────────────────────────────┐ │
│ │                    Total Biaya Bahan Baku        Rp    13,220,000                   │ │
│ │                    Biaya per unit                 Rp         2,644                   │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ SECTION 3: ACTION BUTTONS ─────────────────────────────────────────────────────────────┐
│ [For Detail View:]                                                                      │
│                                                          ┌─────────────┐               │
│                                                          │ Reversal    │               │
│                                                          │ [Green]     │               │
│                                                          └─────────────┘               │
│                                                                                          │
│ [For Reversal View:]                                                                    │
│                                         ┌─────────────┐ ┌─────────────┐               │
│                                         │ Batalkan    │ │ Simpan      │               │
│                                         │ [Red]       │ │ [Green]     │               │
│                                         └─────────────┘ └─────────────┘               │
└──────────────────────────────────────────────────────────────────────────────────────────┘
```

## Responsive Breakpoints

### Desktop (12 columns)
```
Header: [Title - 12 cols]
Production Header Row 1: [Tanggal - 6 cols] [Batch - 6 cols]
Production Header Row 2: [Produk Bundling - 6 cols] [Jumlah Produksi - 6 cols]
Production Header Row 3: [Kode Produk - 6 cols] [Kategori - 6 cols]
Details Table: [Full width - 12 cols with 5 columns]
Financial Summary: [Full width - 12 cols]
Actions: [Buttons right-aligned]
```

### Tablet (8 columns)
```
Header: [Title - 8 cols]
Production Header: [All fields - 8 cols, stacked in pairs]
Details Table: [Horizontal scroll - 8 cols]
Financial Summary: [Full width - 8 cols]
Actions: [Buttons full width]
```

### Mobile (4 columns)
```
All sections: [Full width - 4 cols]
All fields stacked vertically
Table: [Card layout or horizontal scroll]
Buttons: [Full width stacked]
```

## Key Layout Features

### **Production Detail Specific Elements:**
- **Transaction Header**: Clear production identification with "#1001" transaction number
- **Product Focus**: Product name as primary header "Riyo Botol 600ml"
- **Read-only Display**: All fields display-only, no editing capabilities
- **Reversal Access**: Single reversal button for production cancellation

### **Page Structure (3 Main Sections):**
1. **Production Header**: Basic production transaction information
2. **Info Detail Produksi**: Complete breakdown of materials and costs
3. **Action Buttons**: Context-specific buttons (detail view vs reversal)

### **Production Information Display:**
- **Transaction Details**: Date, batch, product, quantity, code, category
- **Historical Record**: Snapshot of production transaction at time of creation
- **Immutable Data**: Cannot be modified, only viewed or reversed
- **Complete Audit Trail**: Full production details for compliance and analysis

### **Material and Cost Breakdown:**
- **Raw Materials Section**: Complete list of consumed materials with quantities and costs
- **Production Costs Section**: Production expenses and overhead allocations
- **Financial Summary**: Total material costs and per-unit cost calculations
- **Cost Transparency**: Clear breakdown of all cost components

### **Reversal Functionality:**
- **Conditional Button**: Reversal button only visible if reversal is possible
- **Stock Validation**: Check if sufficient finished goods exist for reversal
- **Separate Reversal Form**: Dedicated reversal page with confirmation process
- **Two-step Process**: Detail view → Reversal form → Confirmation → Execution

### **Business Logic Integration:**
- **Stock Sufficiency Check**: Verify adequate finished goods inventory for reversal
- **Reversal Validation**: Ensure reversal doesn't create negative inventory
- **Audit Maintenance**: Maintain complete audit trail of original and reversal transactions
- **Financial Impact**: Calculate financial impact of production reversal

### **Data Integrity Features:**
- **Historical Accuracy**: Display exact data from time of production
- **Immutable Records**: Production records cannot be modified after creation
- **Reversal Tracking**: Clear distinction between original and reversal transactions
- **Compliance Support**: Complete documentation for regulatory compliance

### **Navigation and User Experience:**
- **Clear Context**: Obvious distinction between detail view and reversal process
- **Confirmation Flow**: Multi-step reversal process with clear confirmations
- **Cancel Options**: Ability to cancel reversal process at any point
- **Status Indication**: Clear indication of reversible vs non-reversible productions

This production detail and reversal system provides comprehensive transaction viewing with controlled reversal capabilities while maintaining data integrity and proper audit trails.