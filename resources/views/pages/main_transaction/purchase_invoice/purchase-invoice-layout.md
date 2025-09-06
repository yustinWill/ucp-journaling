# Purchase Invoice (PI) Creation Form - Visual Layout

## ASCII Layout Representation

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ Header                                                                                                      │
│ Menu                                                                                                        │
│                                                                                                             │
│    Tambah Invoice                                                                                           │
│                                                                                                             │
│    Referensi Purchase Order                                                                                 │
│    ─────────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    ┌─────────────────────────────────────────────────────────────────────────────────────────────────── │
│    │ Supplier                       Email                      No. Transaksi               Tgl Purchase Order│
│    │ PT SYNTOBA GRAFIKA             supplier@syntoba.com       Purchase Order #PO/SYN/15/0825-2   26/08/2025  │
│    │                                                                                                     │ │
│    │ Status PO                      Syarat Pembayaran         Grand Total                                  │ │
│    │ Approved                       NET 30                    Rp 85,750,000                              │ │
│    │                                                          2                                            │ │
│    │                                                                                                     │ │
│    │ *Dari Purchase Order                                                                                │ │
│    └─────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    Informasi Invoice                                                                                       │
│    ─────────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐                              │
│    │ Nomor Invoice*                  │  │ Tanggal Invoice*                │                              │
│    │ INV-003                         │  │ 08/28/2025                      │                              │
│    └─────────────────────────────────┘  └─────────────────────────────────┘                              │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐                              │
│    │ Tanggal Transaksi*              │  │ Tanggal Jatuh Tempo             │                              │
│    │ 08/29/2025                      │  │ 09/27/2025                      │                              │
│    └─────────────────────────────────┘  └─────────────────────────────────┘                              │
│                                         *Dihitung dari Purchase Order                                      │
│                                                                                                             │
│    ┌─────────────────────────────────┐                                                                    │
│    │ Termin Pembayaran               │                                                                    │
│    │ NET 30                          │                                                                    │
│    └─────────────────────────────────┘                                                                    │
│    *Dari Purchase Order                                                                                    │
│                                                                                                             │
│    ┌─────────────────────────────────────────────────────────────────────────────────────────────────── │
│    │ Keterangan                                                                                           │ │
│    │ Keterangan tambahan untuk invoice                                                                   │ │
│    │                                                                                                     │ │
│    │                                                                                                     │ │
│    └─────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    Items Invoice                                                                                           │
│    ─────────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    Info: Pilih dan masukkan quantity item yang akan di-invoice dari Purchase Order.                       │
│    Sistem akan otomatis menghitung total amount.                                                          │
│                                                                                                             │
│    ┌─────────────────────────────────────────────────────────────────────────────────────────────────── │
│    │ Produk          │ Qty PO │ Qty Invoice │ Harga Satuan   │ Total                                   │ │
│    ├─────────────────┼────────┼─────────────┼────────────────┼─────────────────────────────────────── │
│    │ Laptop Dell     │   5    │     5       │ Rp 15,500,000  │ Rp 77,500,000                        │ │
│    │ Inspiron 15     │        │             │                │                                         │ │
│    ├─────────────────┼────────┼─────────────┼────────────────┼─────────────────────────────────────── │
│    │ Mouse Wireless  │   10   │     0       │ Rp 450,000     │ Rp 0                                 │ │
│    │ Logitech        │        │             │                │                                         │ │
│    ├─────────────────┼────────┼─────────────┼────────────────┼─────────────────────────────────────── │
│    │ Keyboard        │   5    │     0       │ Rp 1,650,000   │ Rp 0                                 │ │
│    │ Mechanical      │        │             │                │                                         │ │
│    └─────────────────┴────────┴─────────────┴────────────────┴─────────────────────────────────────── │
│                                                                                                             │
│    Total Invoice Amount:                                     Rp 77,500,000                              │
│                                                                                                             │
│                                    Total Items Invoice:                   1 items                         │
│                                                                                                             │
│    ┌─────────────┐ ┌─────────────┐                                                                         │
│    │ Batal       │ │ Simpan      │                                                                         │
│    │             │ │ Invoice     │                                                                         │
│    └─────────────┘ └─────────────┘                                                                         │
│                                                                                                             │
└─────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

## Grid-Based Layout

```
┌─ HEADER ─────────────────────────────────────────────────────────────────────────────────┐
│ Tambah Invoice                                                                          │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ SECTION 1: REFERENSI PURCHASE ORDER (AUTO-FILLED) ─────────────────────────────────────┐
│ Referensi Purchase Order                                                                │
│ ─────────────────────────────────────────────────────────────────────────────────────────│
│                                                                                          │
│ ┌─ PO REFERENCE CARD ──────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ ROW 1 ──────────────────────────────────────────────────────────────────────────┐ │ │
│ │ │ ┌─ COL 3 ──────────┐ ┌─ COL 3 ──────────┐ ┌─ COL 3 ──────────┐ ┌─ COL 3 ────────┐ │ │ │
│ │ │ │ Supplier         │ │ Email            │ │ No. Transaksi    │ │ Tgl PO         │ │ │ │
│ │ │ │ [Display]        │ │ [Display]        │ │ [Display]        │ │ [Display]      │ │ │ │
│ │ │ └──────────────────┘ └──────────────────┘ └──────────────────┘ └────────────────┘ │ │ │
│ │ └──────────────────────────────────────────────────────────────────────────────────┘ │ │
│ │                                                                                      │ │
│ │ ┌─ ROW 2 ──────────────────────────────────────────────────────────────────────────┐ │ │
│ │ │ ┌─ COL 3 ──────────┐ ┌─ COL 3 ──────────┐ ┌─ COL 6 ──────────────────────────────┐ │ │ │
│ │ │ │ Status PO        │ │ Syarat Pembayaran│ │ Grand Total                          │ │ │ │
│ │ │ │ [Display]        │ │ [Display]        │ │ [Display]                            │ │ │ │
│ │ │ └──────────────────┘ └──────────────────┘ └──────────────────────────────────────┘ │ │ │
│ │ └──────────────────────────────────────────────────────────────────────────────────┘ │ │
│ │ *Dari Purchase Order                                                                 │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ SECTION 2: INFORMASI INVOICE ──────────────────────────────────────────────────────────┐
│ Informasi Invoice                                                                       │
│ ─────────────────────────────────────────────────────────────────────────────────────────│
│                                                                                          │
│ ┌─ ROW 1 ──────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 6 ─────────────────────┐    ┌─ COL 6 ─────────────────────┐                  │ │
│ │ │ Nomor Invoice* [Text]       │    │ Tanggal Invoice* [Date]     │                  │ │
│ │ │ "INV-003"                   │    │ "08/28/2025"                │                  │ │
│ │ └─────────────────────────────┘    └─────────────────────────────┘                  │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ ROW 2 ──────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 6 ─────────────────────┐    ┌─ COL 6 ─────────────────────┐                  │ │
│ │ │ Tanggal Transaksi* [Date]   │    │ Tanggal Jatuh Tempo         │                  │ │
│ │ │ "08/29/2025"                │    │ "09/27/2025"                │                  │ │
│ │ └─────────────────────────────┘    │ *Dihitung dari Purchase Order│                 │ │
│ │                                    └─────────────────────────────┘                  │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ ROW 3 ──────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 6 ─────────────────────┐                                                      │ │
│ │ │ Termin Pembayaran           │                                                      │ │
│ │ │ [Display] "NET 30"          │                                                      │ │
│ │ │ *Dari Purchase Order        │                                                      │ │
│ │ └─────────────────────────────┘                                                      │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ ROW 4 ──────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 12 ────────────────────────────────────────────────────────────────────────────┐ │ │
│ │ │ Keterangan [Textarea]                                                              │ │ │
│ │ │ "Keterangan tambahan untuk invoice"                                                │ │ │
│ │ └────────────────────────────────────────────────────────────────────────────────────┘ │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ SECTION 3: ITEMS INVOICE ──────────────────────────────────────────────────────────────┐
│ Items Invoice                                                                           │
│ ─────────────────────────────────────────────────────────────────────────────────────────│
│                                                                                          │
│ ┌─ INFO BOX ───────────────────────────────────────────────────────────────────────────┐ │
│ │ Info: Pilih dan masukkan quantity item yang akan di-invoice dari Purchase Order.    │ │
│ │ Sistem akan otomatis menghitung total amount.                                       │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ INVOICE ITEMS TABLE ────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 3 ──────┐ ┌─ COL 2 ──┐ ┌─ COL 2 ──┐ ┌─ COL 2.5 ──┐ ┌─ COL 2.5 ────────────┐ │ │
│ │ │ Produk       │ │ Qty PO   │ │ Qty      │ │ Harga       │ │ Total             │ │ │
│ │ │ [Display]    │ │ [Display]│ │ Invoice  │ │ Satuan      │ │ [Calculated]      │ │ │
│ │ │              │ │          │ │ [Input]  │ │ [Display]   │ │                   │ │ │
│ │ └──────────────┘ └──────────┘ └──────────┘ └─────────────┘ └───────────────────┘ │ │
│ │                                                                                      │ │
│ │ [Sample Data Rows:]                                                                  │ │
│ │ Laptop Dell Inspiron 15 | 5 | [5] | Rp 15,500,000 | Rp 77,500,000                 │ │
│ │ Mouse Wireless Logitech | 10 | [0] | Rp 450,000 | Rp 0                             │ │
│ │ Keyboard Mechanical | 5 | [0] | Rp 1,650,000 | Rp 0                                │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ INVOICE SUMMARY ────────────────────────────────────────────────────────────────────┐ │
│ │                            Total Invoice Amount: Rp 77,500,000                      │ │
│ │                            Total Items Invoice: 1 items                             │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ ACTION BUTTONS ─────────────────────────────────────────────────────────────────────────┐
│ ┌─────────────┐ ┌─────────────┐                                                         │
│ │ Batal       │ │ Simpan      │                                                         │
│ │ [Gray]      │ │ Invoice     │                                                         │
│ │             │ │ [Blue]      │                                                         │
│ └─────────────┘ └─────────────┘                                                         │
└──────────────────────────────────────────────────────────────────────────────────────────┘
```

## Responsive Breakpoints

### Desktop (12 columns)
```
Header: [Title - 12 cols]
PO Reference Row 1: [Supplier - 3 cols] [Email - 3 cols] [No. Transaksi - 3 cols] [Tgl PO - 3 cols]
PO Reference Row 2: [Status PO - 3 cols] [Syarat Pembayaran - 3 cols] [Grand Total - 6 cols]
Invoice Info Row 1: [Nomor Invoice - 6 cols] [Tanggal Invoice - 6 cols]
Invoice Info Row 2: [Tanggal Transaksi - 6 cols] [Tanggal Jatuh Tempo - 6 cols]
Invoice Info Row 3: [Termin Pembayaran - 6 cols]
Invoice Info Row 4: [Keterangan - 12 cols]
Items Table: [Full width - 12 cols with 5 columns]
Actions: [Buttons left-aligned]
```

### Tablet (8 columns)
```
Header: [Title - 8 cols]
PO Reference: [Stacked fields - 4 cols each in rows]
Invoice Info: [All fields - 8 cols, stacked in pairs]
Items Table: [Horizontal scroll - 8 cols]
Actions: [Buttons full width]
```

### Mobile (4 columns)
```
All sections: [Full width - 4 cols]
All fields stacked vertically
PO Reference: [Card layout with vertical information]
Items Table: [Card layout or horizontal scroll]
Buttons: [Full width stacked]
```

## Key Layout Features

### **Purchase Invoice Specific Elements:**
- **Title**: "Tambah Invoice" for creating payment fulfillment records
- **PO Integration**: Auto-filled Purchase Order information with comprehensive details
- **Payment Focus**: Emphasis on payment processing and financial settlement
- **Partial Invoice Support**: Flexible quantity input for partial payment processing

### **Form Structure (3 Main Sections):**
1. **Referensi Purchase Order**: Auto-filled PO details including payment terms and totals
2. **Informasi Invoice**: Invoice-specific information with payment terms and due dates
3. **Items Invoice**: Selective item invoicing with quantity control for partial payments

### **Auto-filled PO Reference Information:**
- **Supplier Details**: Company name and email auto-populated from PO
- **Transaction Information**: PO number, date, and transaction details
- **Financial Details**: PO status, payment terms (NET 30), and grand total amount
- **Payment Terms**: Auto-filled payment terms with due date calculation

### **Invoice Information Fields:**
- **Nomor Invoice**: Auto-generated invoice number (required field)
- **Tanggal Invoice**: Invoice date selection (required field)
- **Tanggal Transaksi**: Transaction processing date (required field)
- **Tanggal Jatuh Tempo**: Auto-calculated due date based on payment terms
- **Termin Pembayaran**: Payment terms from PO (display only)
- **Keterangan**: Optional invoice notes and payment comments

### **Partial Payment Management:**
- **Selective Invoicing**: Choose specific items and quantities to invoice
- **Quantity Control**: Input invoice quantities up to PO quantities
- **Price Integration**: Auto-filled unit prices from Purchase Order
- **Total Calculation**: Real-time calculation of invoice amounts
- **Payment Flexibility**: Support partial payments against Purchase Orders

### **Interactive Elements:**
- **PO Selection**: Choose Purchase Order reference (triggers auto-fill)
- **Quantity Input**: Editable invoice quantities per item with validation
- **Real-time Calculations**: Dynamic total amount calculation
- **Due Date Calculation**: Automatic due date based on payment terms

### **Validation Indicators:**
- **Required fields** marked with asterisks (*)
- **Quantity limits** based on PO quantities
- **Amount validation** with currency formatting
- **Date validation** for payment processing

### **Business Logic Integration:**
- **PO Dependency**: Cannot create invoice without approved Purchase Order
- **Payment Terms**: Auto-calculation of due dates based on PO terms
- **Partial Invoicing**: Support multiple invoices against single Purchase Order
- **Financial Tracking**: Complete payment audit trail and financial integration

### **Payment Processing Features:**
- **Multiple Invoices**: Support multiple partial invoices per Purchase Order
- **Payment Tracking**: Track payment status and outstanding amounts
- **Financial Integration**: Link to accounts payable and financial systems
- **Due Date Management**: Automatic payment due date calculation

### **Invoice Management:**
- **Payment Fulfillment**: Focus on payment processing rather than delivery
- **Financial Settlement**: Complete payment settlement tracking
- **Vendor Relations**: Maintain supplier payment history and relationships
- **Compliance**: Financial compliance and audit trail maintenance

This purchase invoice form provides comprehensive payment management with Purchase Order integration, partial payment support, and complete financial tracking while maintaining proper validation and audit trails for financial compliance.