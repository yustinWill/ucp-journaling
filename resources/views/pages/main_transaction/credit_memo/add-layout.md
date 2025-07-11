# Credit Memo (CM) Creation Form - Visual Layout

## ASCII Layout Representation

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ Header                                                                                                      │
│ Menu                                                                                                        │
│                                                                                                             │
│    Buat Kredit Memo                                                                                        │
│                                                                                                             │
│    Informasi Transaksi                                                                                     │
│    ─────────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐  ┌─────────────────────────┐ │
│    │ Yang Membayar*                  │  │ Nomor Transaksi*                │  │ Tag                     │ │
│    │ John                        ▼   │  │ autofill but editable           │  │                         │ │
│    └─────────────────────────────────┘  └─────────────────────────────────┘  └─────────────────────────┘ │
│                                                                                                             │
│    Transaksi 1                                                                                             │
│    ─────────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐                              │
│    │ Tanggal Transaksi*              │  │ Jenis Transaksi*                │                              │
│    │                                 │  │ Pengeluaran Kredit Memo     ▼   │                              │
│    └─────────────────────────────────┘  └─────────────────────────────────┘                              │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐                              │
│    │ Nama Transaksi*                 │  │ Pilih Akun*                     │                              │
│    │                             ▼   │  │                             ▼   │                              │
│    └─────────────────────────────────┘  └─────────────────────────────────┘                              │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐                              │
│    │ Nilai*                          │  │ Deskripsi                       │                              │
│    │ Rp 500,000.00                   │  │                                 │                              │
│    └─────────────────────────────────┘  │                                 │                              │
│                                         │                                 │                              │
│                                         └─────────────────────────────────┘                              │
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
│    │ Choose File                     │                           │ Batalkan    │ │ Simpan      │         │
│    └─────────────────────────────────┘                           └─────────────┘ └─────────────┘         │
│    File dapat berupa Excel, Word, PDF, PNG, atau Zip                                                      │
│                                                                                                             │
└─────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

## Grid-Based Layout

```
┌─ HEADER ─────────────────────────────────────────────────────────────────────────────────┐
│ Buat Kredit Memo                                                                        │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ SECTION 1: INFORMASI TRANSAKSI ─────────────────────────────────────────────────────────┐
│ Informasi Transaksi                                                                     │
│ ─────────────────────────────────────────────────────────────────────────────────────────│
│                                                                                          │
│ ┌─ ROW 1 ──────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 4 ─────────────────────┐ ┌─ COL 4 ─────────────────────┐ ┌─ COL 4 ──────────┐ │ │
│ │ │ Yang Membayar* [Dropdown]   │ │ Nomor Transaksi* [Text]     │ │ Tag [Text]       │ │ │
│ │ │ "John"                      │ │ "autofill but editable"     │ │                  │ │ │
│ │ └─────────────────────────────┘ └─────────────────────────────┘ └──────────────────┘ │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ SECTION 2: TRANSAKSI 1 ─────────────────────────────────────────────────────────────────┐
│ Transaksi 1                                                                             │
│ ─────────────────────────────────────────────────────────────────────────────────────────│
│                                                                                          │
│ ┌─ ROW 1 ──────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 6 ─────────────────────┐    ┌─ COL 6 ─────────────────────┐                  │ │
│ │ │ Tanggal Transaksi*          │    │ Jenis Transaksi*            │                  │ │
│ │ │ [Date Picker]               │    │ "Pengeluaran Kredit Memo"   │                  │ │
│ │ └─────────────────────────────┘    └─────────────────────────────┘                  │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ ROW 2 ──────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 6 ─────────────────────┐    ┌─ COL 6 ─────────────────────┐                  │ │
│ │ │ Nama Transaksi* [Dropdown]  │    │ Pilih Akun* [Dropdown]      │                  │ │
│ │ │                             │    │                             │                  │ │
│ │ └─────────────────────────────┘    └─────────────────────────────┘                  │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ ROW 3 ──────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 6 ─────────────────────┐    ┌─ COL 6 ─────────────────────┐                  │ │
│ │ │ Nilai* [Currency]           │    │ Deskripsi [Textarea]        │                  │ │
│ │ │ "Rp 500,000.00"             │    │                             │                  │ │
│ │ └─────────────────────────────┘    │                             │                  │ │
│ │                                    │                             │                  │ │
│ │                                    └─────────────────────────────┘                  │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ SECTION 3: ADDITIONAL INFORMATION ──────────────────────────────────────────────────────┐
│ ┌─ SINGLE COLUMN (COL 12) ───────────────────────────────────────────────────────────────┐ │
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
│                                                          │ Batalkan    │ │ Simpan      │ │
│                                                          │ [Red]       │ │ [Green]     │ │
│                                                          └─────────────┘ └─────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘
```

## Responsive Breakpoints

### Desktop (12 columns)
```
Header: [Title - 12 cols]
Section 1: [Yang Membayar - 4 cols] [Nomor Transaksi - 4 cols] [Tag - 4 cols]
Section 2 Row 1: [Tanggal - 6 cols] [Jenis Transaksi - 6 cols]
Section 2 Row 2: [Nama Transaksi - 6 cols] [Pilih Akun - 6 cols]
Section 2 Row 3: [Nilai - 6 cols] [Deskripsi - 6 cols]
Section 3: [Memo - 12 cols] [Lampiran - 12 cols]
Actions: [Buttons right-aligned]
```

### Tablet (8 columns)
```
Header: [Title - 8 cols]
Section 1: [Yang Membayar - 8 cols] [Nomor Transaksi - 4 cols] [Tag - 4 cols]
Section 2: [All fields - 8 cols stacked in pairs]
Section 3: [Memo - 8 cols] [Lampiran - 8 cols]
Actions: [Buttons full width stacked]
```

### Mobile (4 columns)
```
All sections: [Full width - 4 cols]
All fields stacked vertically
Buttons: Full width stacked
```

## Key Layout Features

### **Credit Memo Specific Elements:**
- **Title**: "Buat Kredit Memo" clearly identifying credit memo creation
- **Transaction Focus**: Emphasis on credit memo transaction details
- **Account Integration**: Chart of accounts selection for proper bookkeeping
- **Amount Tracking**: Precise credit amount entry with currency formatting

### **Form Structure (3 Sections):**
1. **Informasi Transaksi**: Basic credit memo information (1 row, 3 fields)
2. **Transaksi 1**: Detailed transaction information (3 rows, multiple fields)
3. **Additional Information**: Memo and file attachment (single column)

### **Field Specifications:**
- **Yang Membayar**: Dropdown with contact selection ("John" sample)
- **Nomor Transaksi**: Auto-generated with edit capability
- **Jenis Transaksi**: "Pengeluaran Kredit Memo" dropdown selection
- **Nilai**: Currency field with sample "Rp 500,000.00"
- **Account Selection**: Integration with chart of accounts
- **Memo**: Internal notes for credit memo processing

### **Interactive Elements:**
- **Dropdowns**: Contact selection, transaction type, account selection
- **Auto-fill**: Transaction number generation with edit capability
- **Currency Input**: Indonesian Rupiah formatting for credit amount
- **File Upload**: Supporting document attachment capability

### **Validation Indicators:**
- **Required fields** marked with asterisks (*)
- **Dropdown indicators** shown with ▼ symbols
- **Currency formatting** for monetary fields
- **File type restrictions** clearly specified

### **Business Logic Integration:**
- **Contact Pre-fill**: When accessed from contact detail, auto-populate payer
- **Account Selection**: Chart of accounts integration for proper categorization
- **Transaction Types**: Support for different credit memo types
- **Amount Validation**: Positive credit amounts with proper formatting

### **Financial Features:**
- **Double-Entry Support**: Foundation for proper accounting journal entries
- **Account Mapping**: Integration with chart of accounts
- **Credit Tracking**: Amount tracking for future application to invoices
- **Currency Standards**: Indonesian Rupiah formatting and validation

### **Document Management:**
- **File Upload**: Supporting document attachment
- **Memo Field**: Internal processing notes
- **Transaction Description**: Detailed credit memo reasoning
- **Audit Trail**: Complete documentation for compliance

### **Navigation Flow:**
- **From Contact Detail**: Direct navigation with pre-filled contact information
- **Credit Memo Creation**: Streamlined credit memo generation process
- **Integration Ready**: Foundation for credit application to outstanding invoices
- **Accounting Integration**: Proper journal entry creation for financial recording

This credit memo form provides a comprehensive interface for creating credit transactions while maintaining proper financial controls and integration with contact management and accounting systems. The design supports efficient credit memo creation with appropriate validation and documentation capabilities.