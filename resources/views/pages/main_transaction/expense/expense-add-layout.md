# Expense (Biaya Pengeluaran) Creation Form - Visual Layout

## ASCII Layout Representation

### Default State (Bayar Nanti Unchecked)

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ Header                                                                                                      │
│ Menu                                                                                                        │
│                                                                                                             │
│    Buat Biaya Pengeluaran                                                                                  │
│                                                                                                             │
│    Informasi Transaksi                                                                                     │
│    ─────────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ☐ Bayar Nanti    ┌─────────────────────────────────┐              │
│    │ Metode Pembayaran*              │                    │ Nomor Transaksi*                │              │
│    │ Cash                        ▼   │                    │ autofill but editable           │              │
│    └─────────────────────────────────┘                    └─────────────────────────────────┘              │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐                              │
│    │ Penerima                        │  │ Tanggal Transaksi*              │                              │
│    │                             ▼   │  │ DD/MM/YYYY                      │                              │
│    └─────────────────────────────────┘  └─────────────────────────────────┘                              │
│                                                                                                             │
│    ┌─────────────────────────────────────────────────────────────────────────────────────────────────── │
│    │ Alamat Penagihan                                                                                     │
│    │                                                                                                     │
│    └─────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    Informasi Biaya                                                                                         │
│    ─────────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    ┌─────────────────────────┬─────────────────────────┬─────────────────────────┬─────────────────────── │
│    │ Akun Biaya              │ Deskripsi               │ Pajak                   │ Jumlah                │
│    ├─────────────────────────┼─────────────────────────┼─────────────────────────┼─────────────────────── │
│    │ Pilih Akun Biaya    ▼   │                         │ PPN                 ▼   │ Rp                    │
│    │ Pilih Akun Biaya    ▼   │                         │ PPN                 ▼   │ Rp                    │
│    │ + Tambah baris          │                         │                         │                       │
│    └─────────────────────────┴─────────────────────────┴─────────────────────────┴─────────────────────── │
│                                                                                                             │
│    ┌─────────────────────────────────┐                  Subtotal          Rp  12,000,000.00             │
│    │ Memo                            │                  Total             Rp  12,000,000.00             │
│    │                                 │                                                                    │
│    │                                 │                  Masukan Jumlah Potongan    Rp   650,000.00       │
│    │                                 │                                                                    │
│    └─────────────────────────────────┘                  Total             Rp  11,350,000.00             │
│                                                                                                             │
│    Lampiran                                                                                                │
│    ┌─────────────────────────────────┐                           ┌─────────────┐ ┌─────────────┐         │
│    │ Choose File                     │                           │ Batalkan    │ │ Buat        │         │
│    └─────────────────────────────────┘                           └─────────────┘ └─────────────┘         │
│    File dapat berupa Excel, Word, PDF, PNG, atau Zip                                                      │
│                                                                                                             │
└─────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

### Bayar Nanti State (Checked)

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ Header                                                                                                      │
│ Menu                                                                                                        │
│                                                                                                             │
│    Buat Biaya Pengeluaran                                                                                  │
│                                                                                                             │
│    Informasi Transaksi                                                                                     │
│    ─────────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ☑ Bayar Nanti    ┌─────────────────────────────────┐              │
│    │ Metode Pembayaran               │                    │ Nomor Transaksi*                │              │
│    │ (disabled)                      │                    │ autofill but editable           │              │
│    └─────────────────────────────────┘                    └─────────────────────────────────┘              │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐                              │
│    │ Penerima                        │  │ Tanggal Transaksi*              │                              │
│    │                             ▼   │  │ DD/MM/YYYY                      │                              │
│    └─────────────────────────────────┘  └─────────────────────────────────┘                              │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐                              │
│    │ Syarat Pembayaran*              │  │ Tanggal Jatuh Tempo*            │                              │
│    │ Net 30                      ▼   │  │ autofill                        │                              │
│    └─────────────────────────────────┘  └─────────────────────────────────┘                              │
│                                                                                                             │
│    ┌─────────────────────────────────────────────────────────────────────────────────────────────────── │
│    │ Alamat Penagihan                                                                                     │
│    │                                                                                                     │
│    └─────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    [Expense Details Section Same as Above]                                                                 │
│                                                                                                             │
│    Catatan Khusus untuk Bayar Nanti                                                                       │
│    ☑ Checkbox Bayar Nanti harus dicentang.                                                                │
│    ☑ Syarat Pembayaran dan Tanggal Jatuh Tempo wajib diisi.                                              │
│    ☑ Tanggal Jatuh Tempo otomatis dihitung dari tanggal transaksi + syarat pembayaran, tetapi dapat     │
│        diedit jika perlu.                                                                                  │
│                                                                                                             │
│    Tambah Syarat Pembayaran                                                                               │
│    ┌─────────────────────────────────┐                                                                    │
│    │ Syarat Pembayaran*              │                                                                    │
│    │ Net 30                      ▼   │                                                                    │
│    └─────────────────────────────────┘                                                                    │
│    └ Syarat Pembayaran baru                                                                               │
│                                                                                                             │
│    Nama Syarat Pembayaran*                                                                                │
│    ┌─────────────────────────────────┐                                                                    │
│    │                                 │                                                                    │
│    └─────────────────────────────────┘                                                                    │
│                                                                                                             │
│    Jumlah Hari Jatuh Tempo*                                                                               │
│    ┌─────────────────────────────────┐ Hari                                                              │
│    │                                 │                                                                    │
│    └─────────────────────────────────┘                                                                    │
│                                                                                                             │
│                                         ┌─────────────┐ ┌─────────────┐                                  │
│                                         │ Batalkan    │ │ Buat        │                                  │
│                                         └─────────────┘ └─────────────┘                                  │
│                                                                                                             │
└─────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

## Grid-Based Layout

```
┌─ HEADER ─────────────────────────────────────────────────────────────────────────────────┐
│ Buat Biaya Pengeluaran                                                                  │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ SECTION 1: INFORMASI TRANSAKSI ─────────────────────────────────────────────────────────┐
│ Informasi Transaksi                                                                     │
│ ─────────────────────────────────────────────────────────────────────────────────────────│
│                                                                                          │
│ ┌─ ROW 1 ──────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 4 ─────────────────────┐ ┌─ COL 2 ──────────┐ ┌─ COL 6 ──────────────────────┐ │ │
│ │ │ Metode Pembayaran*          │ │ ☐ Bayar Nanti   │ │ Nomor Transaksi*             │ │ │
│ │ │ [Dropdown] "Cash"           │ │ [Checkbox]      │ │ [Text] "autofill editable"   │ │ │
│ │ └─────────────────────────────┘ └─────────────────┘ └──────────────────────────────┘ │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ ROW 2 ──────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 6 ─────────────────────┐    ┌─ COL 6 ─────────────────────┐                  │ │
│ │ │ Penerima [Dropdown]         │    │ Tanggal Transaksi* [Date]   │                  │ │
│ │ │                             │    │ "DD/MM/YYYY"                │                  │ │
│ │ └─────────────────────────────┘    └─────────────────────────────┘                  │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ ROW 3 (Conditional - Bayar Nanti) ─────────────────────────────────────────────────┐ │
│ │ ┌─ COL 6 ─────────────────────┐    ┌─ COL 6 ─────────────────────┐                  │ │
│ │ │ Syarat Pembayaran*          │    │ Tanggal Jatuh Tempo*        │                  │ │
│ │ │ [Dropdown] "Net 30"         │    │ [Date] "autofill"           │                  │ │
│ │ └─────────────────────────────┘    └─────────────────────────────┘                  │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ ROW 4 ──────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 12 ────────────────────────────────────────────────────────────────────────────┐ │ │
│ │ │ Alamat Penagihan [Textarea]                                                        │ │ │
│ │ └────────────────────────────────────────────────────────────────────────────────────┘ │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ SECTION 2: INFORMASI BIAYA ─────────────────────────────────────────────────────────────┐
│ Informasi Biaya                                                                         │
│ ─────────────────────────────────────────────────────────────────────────────────────────│
│                                                                                          │
│ ┌─ EXPENSE TABLE ──────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 3 ──────┐ ┌─ COL 3 ──────┐ ┌─ COL 3 ──────┐ ┌─ COL 3 ──────┐               │ │
│ │ │ Akun Biaya   │ │ Deskripsi    │ │ Pajak        │ │ Jumlah       │               │ │
│ │ │ [Dropdown]   │ │ [Text]       │ │ [Dropdown]   │ │ [Currency]   │               │ │
│ │ └──────────────┘ └──────────────┘ └──────────────┘ └──────────────┘               │ │
│ │ [+ Tambah baris]                                                                   │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ SPLIT LAYOUT ───────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ LEFT COL 6 ────────────────┐    ┌─ RIGHT COL 6 ───────────────────────────────────┐ │ │
│ │ │ Memo [Textarea]             │    │ Subtotal: Rp 12,000,000.00                    │ │ │
│ │ │                             │    │ Total: Rp 12,000,000.00                       │ │ │
│ │ │                             │    │ Masukan Jumlah Potongan: Rp 650,000.00        │ │ │
│ │ │                             │    │ Total: Rp 11,350,000.00                       │ │ │
│ │ │ Lampiran [File Upload]      │    │                                                │ │ │
│ │ └─────────────────────────────┘    └────────────────────────────────────────────────┘ │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ SECTION 3: CONDITIONAL BAYAR NANTI DETAILS ─────────────────────────────────────────────┐
│ (Only visible when Bayar Nanti is checked)                                              │
│                                                                                          │
│ ┌─ PAYMENT TERMS INFO ─────────────────────────────────────────────────────────────────┐ │
│ │ Catatan Khusus untuk Bayar Nanti                                                    │ │
│ │ ☑ Checkbox Bayar Nanti harus dicentang                                              │ │
│ │ ☑ Syarat Pembayaran dan Tanggal Jatuh Tempo wajib diisi                            │ │
│ │ ☑ Tanggal Jatuh Tempo otomatis dihitung dari tanggal transaksi + syarat pembayaran │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ ADD PAYMENT TERMS ──────────────────────────────────────────────────────────────────┐ │
│ │ Tambah Syarat Pembayaran                                                            │ │
│ │ ┌─ COL 12 ────────────────────────────────────────────────────────────────────────┐ │ │
│ │ │ Syarat Pembayaran* [Dropdown] "Net 30"                                          │ │ │
│ │ └──────────────────────────────────────────────────────────────────────────────────┘ │ │
│ │ └ Syarat Pembayaran baru                                                             │ │
│ │                                                                                      │ │
│ │ ┌─ COL 12 ────────────────────────────────────────────────────────────────────────┐ │ │
│ │ │ Nama Syarat Pembayaran* [Text]                                                   │ │ │
│ │ └──────────────────────────────────────────────────────────────────────────────────┘ │ │
│ │                                                                                      │ │
│ │ ┌─ COL 6 ─────────────────────┐ Hari                                               │ │
│ │ │ Jumlah Hari Jatuh Tempo*    │                                                    │ │
│ │ │ [Number Input]              │                                                    │ │
│ │ └─────────────────────────────┘                                                    │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
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
Header: [Title - 12 cols]
Section 1 Row 1: [Metode Pembayaran - 4 cols] [Bayar Nanti - 2 cols] [Nomor Transaksi - 6 cols]
Section 1 Row 2: [Penerima - 6 cols] [Tanggal Transaksi - 6 cols]
Section 1 Row 3: [Syarat Pembayaran - 6 cols] [Tanggal Jatuh Tempo - 6 cols] (conditional)
Section 1 Row 4: [Alamat Penagihan - 12 cols]
Section 2: [Expense Table - 12 cols]
Section 2 Split: [Memo & Lampiran - 6 cols] [Summary - 6 cols]
Section 3: [Payment Terms Management - 12 cols] (conditional)
Actions: [Buttons right-aligned]
```

### Tablet (8 columns)
```
Header: [Title - 8 cols]
Section 1: [All fields stacked in pairs - 8 cols total]
Section 2: [Expense Table horizontal scroll - 8 cols]
Section 2 Split: [Memo - 8 cols] [Summary - 8 cols] (stacked)
Section 3: [Payment Terms - 8 cols] (conditional)
Actions: [Buttons full width stacked]
```

### Mobile (4 columns)
```
All sections: [Full width - 4 cols]
All fields stacked vertically
Expense Table: Card layout or horizontal scroll
Buttons: Full width stacked
```

## Key Layout Features

### **Expense Management Specific Elements:**
- **Title**: "Buat Biaya Pengeluaran" clearly identifying expense creation
- **Payment Methods**: Cash, transfer, and other payment options
- **Conditional Logic**: "Bayar Nanti" checkbox controls additional fields
- **Expense Details**: Multi-line expense entry with account selection

### **Form Structure (Dynamic Sections):**
1. **Informasi Transaksi**: Payment method, recipient, dates (2-3 rows depending on Bayar Nanti)
2. **Informasi Biaya**: Expense line items with account, description, tax, amount
3. **Conditional Payment Terms**: Additional payment term management (only when Bayar Nanti checked)

### **Conditional Logic - Bayar Nanti States:**

#### **Unchecked State (Immediate Payment):**
- **Metode Pembayaran**: Active dropdown (Cash, Transfer, etc.)
- **Payment Terms**: Hidden
- **Due Date**: Not applicable
- **Focus**: Immediate expense recording

#### **Checked State (Payment Later):**
- **Metode Pembayaran**: Disabled/grayed out
- **Syarat Pembayaran**: Required dropdown (Net 30, Net 45, etc.)
- **Tanggal Jatuh Tempo**: Auto-calculated but editable
- **Additional Section**: Payment terms management interface

### **Field Specifications:**
- **Metode Pembayaran**: Dropdown with payment methods (disabled when Bayar Nanti checked)
- **Bayar Nanti**: Checkbox that controls form behavior
- **Nomor Transaksi**: Auto-generated transaction number with edit capability
- **Penerima**: Recipient/vendor selection dropdown
- **Syarat Pembayaran**: Payment terms (Net 30, Net 45, etc.) - conditional
- **Tanggal Jatuh Tempo**: Auto-calculated due date - conditional

### **Expense Table Features:**
- **Akun Biaya**: Chart of accounts integration for expense categorization
- **Deskripsi**: Expense description for each line item
- **Pajak**: Tax selection (PPN) for applicable expenses
- **Jumlah**: Amount in Indonesian Rupiah format
- **Dynamic Rows**: Add/remove expense line items as needed

### **Financial Calculations:**
- **Subtotal**: Sum of all expense line items
- **Tax Calculations**: Automatic PPN calculation where applicable
- **Discount Entry**: Manual discount/adjustment entry
- **Final Total**: Calculated total after discounts

### **Interactive Elements:**
- **Conditional Fields**: Dynamic show/hide based on Bayar Nanti checkbox
- **Auto-calculations**: Due date calculation based on payment terms
- **Dynamic Table**: Add/remove expense rows
- **Account Integration**: Chart of accounts dropdown for expense categorization

### **Validation Indicators:**
- **Required fields** marked with asterisks (*)
- **Conditional requirements** based on Bayar Nanti state
- **Currency formatting** for monetary fields
- **Date validation** for transaction and due dates

### **Business Logic Integration:**
- **Payment Terms Management**: Create new payment terms within the form
- **Account Mapping**: Integration with chart of accounts for expense categorization
- **Tax Handling**: Proper PPN tax calculations and recording
- **Vendor Integration**: Link expenses to vendor/recipient records

### **Payment Terms Management (Conditional):**
- **Term Selection**: Choose from existing payment terms
- **New Term Creation**: Add custom payment terms with days specification
- **Auto-calculation**: Automatic due date calculation based on selected terms
- **Validation**: Ensure proper payment term configuration

### **Document Management:**
- **File Upload**: Supporting document attachment (Excel, Word, PDF, PNG, Zip)
- **Memo Field**: Internal notes for expense processing
- **Expense Documentation**: Complete audit trail for expense approval

### **Navigation Flow:**
- **Standard Creation**: Immediate expense entry and payment
- **Credit Terms**: Deferred payment with proper accounts payable setup
- **Account Integration**: Proper expense categorization for financial reporting
- **Approval Workflow**: Foundation for expense approval processes

This expense form provides comprehensive expense management with conditional payment terms, supporting both immediate payments and accounts payable scenarios while maintaining proper financial controls and integration with accounting systems.