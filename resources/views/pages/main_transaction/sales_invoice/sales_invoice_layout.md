# Sales Invoice (SI) Creation Form - Visual Layout

## ASCII Layout Representation

```
┌──────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────
│ Header                                                                                                      │
│ Menu                                                                                                        │
│                                                                                                             │
│    Tambah Invoice                                                                                          │
│                                                                                                             │
│    ┌─ LEFT COLUMN (COL 8) ──────────────────────────────────    ┌─ RIGHT COLUMN (COL 4) ─────────────────┐ │
│    │                                                         │    │ Requirement Sales Invoice (SI)         │ │
│    │ Referensi Purchase Order                                │    │ 1. Relasi dengan Sales Order (SO)      │ │
│    │ ────────────────────────────────                       │    │ Sales Invoice (SI) hanya bisa dibuat   │ │
│    │                                                         │    │ jika ada Sales Order (SO).             │ │
│    │ Supplier                         No Purchase Order      │    │ Saat user buat SI, user harus Silih    │ │
│    │ PT SINTOBA GRAFIKA              Purchase Order          │    │ referensi SO terlebih dahulu.          │ │
│    │                                  #PO/SYN/15/0825-      │    │ Semua data dasar (Customer, alamat     │ │
│    │ Status PO                        Tgl Purchase Order     │    │ pengiriman, tanggal SO, item SO, qty   │ │
│    │ Approved                         26/08/2025             │    │ SO, dll.) autofill dari SO.           │ │
│    │                                                         │    │                                        │ │
│    │ Termin Pembayaran                Total PO               │    │ Business Rules                         │ │
│    │ NET 30                           Rp 85.750.000          │    │ Invoice hanya bisa dibuat berdasarkan  │ │
│    │                                                         │    │ SO yang Approved.                      │ │
│    │                                                         │    │ Bisa multiple invoice selama masih     │ │
│    │ Informasi Invoice                                       │    │ ada qty tersisa pada SO.              │ │
│    │ ────────────────────────────────────────────────────   │    │ Sistem menghitung otomatis jatuh      │ │
│    │                                                         │    │ temSO berdasarkan termin pembayaran.   │ │
│    │ Nomor Invoice *                  Tanggal Invoice *      │    │ Jika total invoice = 0, sistem tidak  │ │
│    │ INV-003                          08/28/2025       📅   │    │ boleh menyimpan invoice.               │ │
│    │                                                         │    │                                        │ │
│    │ Tanggal Transaksi *              Tanggal Jatuh Tempo   │    │                                        │ │
│    │ 08/29/2025                 📅    09/27/2025       📅   │    │                                        │ │
│    │                                  *Otomatis dari Purchase│    │                                        │ │
│    │                                   Order                 │    │                                        │ │
│    │ Termin Pembayaran                                       │    │                                        │ │
│    │ NET 30                                                  │    │                                        │ │
│    │ *Dari Purchase Order                                    │    │                                        │ │
│    │                                                         │    │                                        │ │
│    │ Keterangan                                              │    │                                        │ │
│    │ ┌─────────────────────────────────────────────────────┐ │    │                                        │ │
│    │ │ Keterangan tambahan untuk Invoice                   │ │    │                                        │ │
│    │ │                                                     │ │    │                                        │ │
│    │ │                                                     │ │    │                                        │ │
│    │ │                                                     │ │    │                                        │ │
│    │ └─────────────────────────────────────────────────────┘ │    │                                        │ │
│    │                                                         │    │                                        │ │
│    │ Items Invoice                                           │    │                                        │ │
│    │ ────────────────────────────────────────────────────   │    │                                        │ │
│    │                                                         │    │                                        │ │
│    │ ┌─ INFO BOX ─────────────────────────────────────────┐  │    │                                        │ │
│    │ │ Pilih dan masukkan quantity item yang akan di-     │  │    │                                        │ │
│    │ │ invoice dari Purchase Order. Sistem akan otomatis  │  │    │                                        │ │
│    │ │ menghitung total amount.                            │  │    │                                        │ │
│    │ └───────────────────────────────────────────────────┘  │    │                                        │ │
│    │                                                         │    │                                        │ │
│    │ ┌─ ITEMS TABLE ─────────────────────────────────────┐   │    │                                        │ │
│    │ │ Produk          │ Qty PO │ Qty Invoice │ Harga   │   │    │                                        │ │
│    │ │                 │        │             │ Satuan  │   │    │                                        │ │
│    │ ├─────────────────┼────────┼─────────────┼─────────┤   │    │                                        │ │
│    │ │ Laptop Dell     │   5    │      5      │ Rp 15.500│   │    │                                        │ │
│    │ │ Inspiron 15     │        │             │ .000    │   │    │                                        │ │
│    │ ├─────────────────┼────────┼─────────────┼─────────┤   │    │                                        │ │
│    │ │ Mouse Wireless  │  10    │      0      │ Rp 450.000│   │    │                                        │ │
│    │ │ Logitech        │        │             │         │   │    │                                        │ │
│    │ ├─────────────────┼────────┼─────────────┼─────────┤   │    │                                        │ │
│    │ │ Keyboard        │   5    │      0      │ Rp 1.650│   │    │                                        │ │
│    │ │ Mechanical      │        │             │ .000    │   │    │                                        │ │
│    │ │                 │        │             │         │   │    │                                        │ │
│    │ │                 │        │             │ Total   │   │    │                                        │ │
│    │ │                 │        │             │         │   │    │                                        │ │
│    │ │                 │        │             │ Rp 77.500│   │    │                                        │ │
│    │ │                 │        │             │ .000    │   │    │                                        │ │
│    │ └─────────────────┴────────┴─────────────┴─────────┘   │    │                                        │ │
│    │                                                         │    │                                        │ │
│    │ Total Invoice Amount:             Rp 77.500.000        │    │                                        │ │
│    │                                                         │    │                                        │ │
│    │ Total Items Invoice:              1 Items               │    │                                        │ │
│    │                                                         │    │                                        │ │
│    │                      ┌─────────┐ ┌──────────────────┐   │    │                                        │ │
│    │                      │ Batal   │ │ 💼 Simpan Invoice│   │    │                                        │ │
│    │                      │ [Gray]  │ │ [Blue]           │   │    │                                        │ │
│    │                      └─────────┘ └──────────────────┘   │    │                                        │ │
│    └─────────────────────────────────────────────────────────┘    └────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────────────────────────
```

## Grid-Based Layout

```
┌─ HEADER ─────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ Tambah Invoice                                                                                                 │
└────────────────────────────────────────────────────────────────────────────────────────────────────────────┘

┌─ MAIN CONTENT SPLIT LAYOUT ─────────────────────────────────────────────────────────────────────────────────┐
│ ┌─ LEFT COLUMN (COL 8) ──────────────────────────    ┌─ RIGHT COLUMN (COL 4) ─────────────────────────────┐ │
│ │                                                 │    │ Requirement Information Panel                   │ │
│ │ ┌─ SECTION 1: SO REFERENCE INFO ───────────────┐ │    │ ┌─ Requirements List ─────────────────────────┐ │ │
│ │ │ Referensi Purchase Order                    │ │    │ │ 1. Relasi dengan Sales Order (SO)         │ │ │
│ │ │ ─────────────────────────────────────────   │ │    │ │ 2. Business Rules                          │ │ │
│ │ │                                             │ │    │ └───────────────────────────────────────────┘ │ │
│ │ │ ┌─ ROW 1 ─────────────────────────────────┐ │ │    └─────────────────────────────────────────────────┘ │
│ │ │ │ ┌─ COL 6 ─────────┐ ┌─ COL 6 ─────────┐ │ │                                                      │
│ │ │ │ │ Supplier        │ │ No Purchase     │ │ │                                                      │
│ │ │ │ │ [Display]       │ │ Order [Display] │ │ │                                                      │
│ │ │ │ └─────────────────┘ └─────────────────┘ │ │                                                      │
│ │ │ └─────────────────────────────────────────┘ │ │                                                      │
│ │ │                                             │ │                                                      │
│ │ │ ┌─ ROW 2 ─────────────────────────────────┐ │ │                                                      │
│ │ │ │ ┌─ COL 6 ─────────┐ ┌─ COL 6 ─────────┐ │ │                                                      │
│ │ │ │ │ Status PO       │ │ Tgl Purchase    │ │ │                                                      │
│ │ │ │ │ [Display]       │ │ Order [Display] │ │ │                                                      │
│ │ │ │ └─────────────────┘ └─────────────────┘ │ │                                                      │
│ │ │ └─────────────────────────────────────────┘ │ │                                                      │
│ │ │                                             │ │                                                      │
│ │ │ ┌─ ROW 3 ─────────────────────────────────┐ │ │                                                      │
│ │ │ │ ┌─ COL 6 ─────────┐ ┌─ COL 6 ─────────┐ │ │                                                      │
│ │ │ │ │ Termin          │ │ Total PO        │ │ │                                                      │
│ │ │ │ │ Pembayaran      │ │ [Display]       │ │ │                                                      │
│ │ │ │ │ [Display]       │ │                 │ │ │                                                      │
│ │ │ │ └─────────────────┘ └─────────────────┘ │ │                                                      │
│ │ │ └─────────────────────────────────────────┘ │ │                                                      │
│ │ └─────────────────────────────────────────────┘ │                                                      │
│ │                                                 │                                                      │
│ │ ┌─ SECTION 2: INVOICE INFORMATION ─────────────┐ │                                                      │
│ │ │ Informasi Invoice                           │ │                                                      │
│ │ │ ─────────────────────────────────────────── │ │                                                      │
│ │ │                                             │ │                                                      │
│ │ │ ┌─ ROW 1 ─────────────────────────────────┐ │ │                                                      │
│ │ │ │ ┌─ COL 6 ─────────┐ ┌─ COL 6 ─────────┐ │ │                                                      │
│ │ │ │ │ Nomor Invoice*  │ │ Tanggal         │ │ │                                                      │
│ │ │ │ │ [Text Input]    │ │ Invoice*        │ │ │                                                      │
│ │ │ │ │ "INV-003"       │ │ [Date Input]    │ │ │                                                      │
│ │ │ │ └─────────────────┘ └─────────────────┘ │ │                                                      │
│ │ │ └─────────────────────────────────────────┘ │ │                                                      │
│ │ │                                             │ │                                                      │
│ │ │ ┌─ ROW 2 ─────────────────────────────────┐ │ │                                                      │
│ │ │ │ ┌─ COL 6 ─────────┐ ┌─ COL 6 ─────────┐ │ │                                                      │
│ │ │ │ │ Tanggal         │ │ Tanggal Jatuh   │ │ │                                                      │
│ │ │ │ │ Transaksi*      │ │ Tempo           │ │ │                                                      │
│ │ │ │ │ [Date Input]    │ │ [Auto-calc]     │ │ │                                                      │
│ │ │ │ └─────────────────┘ └─────────────────┘ │ │                                                      │
│ │ │ └─────────────────────────────────────────┘ │ │                                                      │
│ │ │                                             │ │                                                      │
│ │ │ ┌─ ROW 3 ─────────────────────────────────┐ │ │                                                      │
│ │ │ │ ┌─ COL 12 ────────────────────────────┐ │ │ │                                                      │
│ │ │ │ │ Termin Pembayaran [Display]         │ │ │ │                                                      │
│ │ │ │ │ "NET 30" *Dari Purchase Order       │ │ │ │                                                      │
│ │ │ │ └─────────────────────────────────────┘ │ │ │                                                      │
│ │ │ └─────────────────────────────────────────┘ │ │                                                      │
│ │ │                                             │ │                                                      │
│ │ │ ┌─ ROW 4 ─────────────────────────────────┐ │ │                                                      │
│ │ │ │ ┌─ COL 12 ────────────────────────────┐ │ │ │                                                      │
│ │ │ │ │ Keterangan [Textarea]               │ │ │ │                                                      │
│ │ │ │ │ "Keterangan tambahan untuk Invoice" │ │ │ │                                                      │
│ │ │ │ └─────────────────────────────────────┘ │ │ │                                                      │
│ │ │ └─────────────────────────────────────────┘ │ │                                                      │
│ │ └─────────────────────────────────────────────┘ │                                                      │
│ │                                                 │                                                      │
│ │ ┌─ SECTION 3: ITEMS INVOICE ───────────────────┐ │                                                      │
│ │ │ Items Invoice                               │ │                                                      │
│ │ │ ─────────────────────────────────────────── │ │                                                      │
│ │ │                                             │ │                                                      │
│ │ │ ┌─ INFO BOX ─────────────────────────────┐ │ │                                                      │
│ │ │ │ Pilih dan masukkan quantity item yang  │ │ │                                                      │
│ │ │ │ akan di-invoice dari Purchase Order.   │ │ │                                                      │
│ │ │ │ Sistem akan otomatis menghitung total  │ │ │                                                      │
│ │ │ │ amount.                                │ │ │                                                      │
│ │ │ └───────────────────────────────────────┘ │ │                                                      │
│ │ │                                             │ │                                                      │
│ │ │ ┌─ ITEMS TABLE ─────────────────────────┐ │ │                                                      │
│ │ │ │ ┌─ COL 3 ──┐ ┌─ COL 2 ┐ ┌─ COL 2 ──┐ │ │ │                                                      │
│ │ │ │ │ Produk   │ │ Qty PO │ │ Qty      │ │ │ │                                                      │
│ │ │ │ │[Display] │ │[Display│ │ Invoice  │ │ │ │                                                      │
│ │ │ │ │          │ │]       │ │[Input]   │ │ │ │                                                      │
│ │ │ │ └──────────┘ └────────┘ └─────────┘ │ │ │                                                      │
│ │ │ │                                     │ │ │                                                      │
│ │ │ │ ┌─ COL 2.5 ┐ ┌─ COL 2.5 ──────────┐ │ │ │                                                      │
│ │ │ │ │ Harga    │ │ Total              │ │ │ │                                                      │
│ │ │ │ │ Satuan   │ │ [Auto-calc]        │ │ │ │                                                      │
│ │ │ │ │[Display] │ │                    │ │ │ │                                                      │
│ │ │ │ └──────────┘ └────────────────────┘ │ │ │                                                      │
│ │ │ │                                     │ │ │                                                      │
│ │ │ │ [Sample rows from SO items]         │ │ │                                                      │
│ │ │ │ Laptop Dell Inspiron 15: 5/5        │ │ │                                                      │
│ │ │ │ Mouse Wireless Logitech: 10/0       │ │ │                                                      │
│ │ │ │ Keyboard Mechanical: 5/0            │ │ │                                                      │
│ │ │ └─────────────────────────────────────┘ │ │                                                      │
│ │ │                                             │ │                                                      │
│ │ │ Total Invoice Amount: Rp 77.500.000        │ │                                                      │
│ │ │ Total Items Invoice: 1 Items                │ │                                                      │
│ │ └─────────────────────────────────────────────┘ │                                                      │
│ │                                                 │                                                      │
│ │ ┌─ ACTION BUTTONS ──────────────────────────────┐ │                                                      │
│ │ │                  ┌─────────┐ ┌──────────────┐ │ │                                                      │
│ │ │                  │ Batal   │ │💼 Simpan     │ │ │                                                      │
│ │ │                  │ [Gray]  │ │ Invoice      │ │ │                                                      │
│ │ │ │                  │         │ │ [Blue]       │ │ │                                                      │
│ │ │                  └─────────┘ └──────────────┘ │ │                                                      │
│ │ └─────────────────────────────────────────────────┘ │                                                      │
│ └─────────────────────────────────────────────────────┘ └──────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

## Responsive Breakpoints

### Desktop (12 columns)
```
Header: [Title - 12 cols]
Main Layout: [Form Content - 8 cols] [Requirements Panel - 4 cols]
SO Reference: [6 fields in 2-column layout]
Invoice Info: [Input fields in 2-column layout]
Items Table: [Full width with 5 columns]
Actions: [Buttons right-aligned]
```

### Tablet (8 columns)
```
Header: [Title - 8 cols]
Main Content: [Form - 8 cols, Requirements panel below]
All sections: [Fields in 2-column layout]
Items Table: [Horizontal scroll - 8 cols]
Actions: [Buttons full width]
```

### Mobile (4 columns)
```
All sections: [Full width - 4 cols]
All fields stacked vertically
Items Table: [Card layout or scroll]
Requirements: [Collapsible panel]
Buttons: [Full width stacked]
```

## Key Layout Features

### **Sales Invoice Specific Elements:**
- **SO Reference Panel**: Complete Sales Order information display
- **Invoice Information**: Invoice-specific details with auto-calculation
- **Items from SO**: SO items with invoice quantity input capability
- **Financial Calculations**: Automatic total calculation based on invoiced quantities

### **Form Structure (3 Main Sections):**
1. **SO Reference Information**: Complete Sales Order details and financial summary
2. **Invoice Information**: Invoice-specific details (number, dates, payment terms)
3. **Items Invoice**: SO items with quantity selection and total calculations

### **Sales Order Integration:**
- **SO Data Display**: Complete customer, order details, and financial information
- **Status Validation**: Only approved SOs can generate invoices
- **Payment Terms**: Auto-populated from SO with due date calculation
- **Item Source**: All items sourced from originating Sales Order

### **Invoice Management Features:**
- **Invoice Number**: Auto-generated with edit capability (INV-XXX format)
- **Invoice Date**: Required date input for invoice creation
- **Due Date Calculation**: Automatic calculation based on SO payment terms
- **Invoice Notes**: Optional detailed notes for invoice specifics

### **Item Invoice Processing:**
- **SO Items Display**: Shows all items from the originating Sales Order
- **Quantity Selection**: User selects quantities to invoice from SO items
- **Price Integration**: Uses SO pricing for accurate financial calculations
- **Total Calculation**: Real-time calculation of invoice totals based on selected quantities
- **Financial Summary**: Complete invoice amount and item count display

### **Interactive Elements:**
- **Quantity Input**: Select invoice quantities for each SO item
- **Real-time Calculations**: Update invoice totals as quantities change
- **Date Calculations**: Auto-calculate due dates based on payment terms
- **Form Validation**: Ensure required fields and non-zero invoice totals

### **Validation and Business Rules:**
- **SO Dependency**: Can only create invoice from existing approved Sales Order
- **Quantity Selection**: Invoice quantities based on SO items and availability
- **Non-zero Total**: System prevents saving invoices with zero total
- **Multiple Invoices**: Support multiple partial invoices against single SO
- **Due Date Automation**: Calculate payment due dates based on SO terms

### **Financial Integration:**
- **SO Pricing**: Use Sales Order pricing for invoice calculations
- **Payment Terms**: Inherit payment terms from SO for consistency
- **Total Tracking**: Track invoiced amounts against SO total value
- **Currency Formatting**: Proper Indonesian Rupiah formatting throughout

### **Document Workflow:**
- **SO Foundation**: Sales Invoice built upon existing Sales Order
- **Status Updates**: Invoice creation updates SO invoice status
- **Multiple Invoicing**: Support partial invoicing with remaining balance tracking
- **Financial Audit**: Complete invoice audit trail linked to Sales Order

This sales invoice form provides complete invoicing capability with SO integration, quantity selection, automatic calculations, and proper business rule enforcement while maintaining clear financial tracking and audit capabilities.