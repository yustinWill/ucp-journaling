# Contact Edit Form - Visual Layout

## ASCII Layout Representation

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ Header                                                                                                      │
│ Menu                                                                                                        │
│                                                                                                             │
│    Kontak Kontak                                                                                           │
│                                                                                                             │
│    Info Kontak                                                                                             │
│    ─────────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    ┌─────────────────────────────────┐                                                                    │
│    │ Nama Kontak*                    │                                                                    │
│    │                                 │                                                                    │
│    └─────────────────────────────────┘                                                                    │
│                                                                                                             │
│    Tipe Kontak*                                                                                            │
│    ○ Pelanggan    ○ Supplier    ○ Karyawan    ○ Lainnya                                                   │
│                                                                                                             │
│    ┌─────────────────────────────────┐                                                                    │
│    │ Grup Kontak                     │                                                                    │
│    │                                 │                                                                    │
│    └─────────────────────────────────┘                                                                    │
│                                                                                                             │
│    Info Umum                                                                                               │
│    ─────────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐  ┌─────────────────────────┐ │
│    │ Nama Perusahaan*                │  │ Nomor Handphone                 │  │ Nomor Telepon           │ │
│    │                                 │  │                                 │  │                         │ │
│    └─────────────────────────────────┘  └─────────────────────────────────┘  └─────────────────────────┘ │
│                                                                                                             │
│    ┌─────────────────────────────────┐                                                                    │
│    │ Identitas*                      │                                                                    │
│    │ KTP                             │                                                                    │
│    └─────────────────────────────────┘                                                                    │
│                                                                                                             │
│    ┌─────────────────────────────────┐                                                                    │
│    │ Email                           │                                                                    │
│    │                                 │                                                                    │
│    └─────────────────────────────────┘                                                                    │
│                                                                                                             │
│    Info Perusahaan                                                                                         │
│    ─────────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐                              │
│    │ Nama Perusahaan*                │  │ Nomor Identitas                 │                              │
│    │                                 │  │                                 │                              │
│    └─────────────────────────────────┘  └─────────────────────────────────┘                              │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐                              │
│    │ Fax*                            │  │ NPWP*                           │                              │
│    │                                 │  │                                 │                              │
│    └─────────────────────────────────┘  └─────────────────────────────────┘                              │
│                                                                                                             │
│    ┌─────────────────────────────────┐                                                                    │
│    │ Alamat Penagihan*               │                                                                    │
│    │                                 │                                                                    │
│    │                                 │                                                                    │
│    └─────────────────────────────────┘                                                                    │
│                                                                                                             │
│    ☐ Samakan dengan Alamat Perusahaan                                                                     │
│                                                                                                             │
│    ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐                                                       │
│    │     │ │     │ │     │ │     │ │     │ │     │                                                       │
│    └─────┘ └─────┘ └─────┘ └─────┘ └─────┘ └─────┘                                                       │
│                                                                                                             │
│    ┌─────────────────────────────────┐                                                                    │
│    │ Alamat Pengiriman               │                                                                    │
│    │                                 │                                                                    │
│    │                                 │                                                                    │
│    └─────────────────────────────────┘                                                                    │
│                                                                                                             │
│    Info Bank                                                                                               │
│    ─────────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐                              │
│    │ Nama Bank*                      │  │ Kantor Cabang                   │                              │
│    │                                 │  │                                 │                              │
│    └─────────────────────────────────┘  └─────────────────────────────────┘                              │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐                              │
│    │ Pemegang Akun Bank*             │  │ Nomor Rekening*                 │                              │
│    │                                 │  │                                 │                              │
│    └─────────────────────────────────┘  └─────────────────────────────────┘                              │
│                                                                                                             │
│    ┌─────────────────────────────────┐                                                                    │
│    │ Tambah Akun Bank Lain           │                                                                    │
│    └─────────────────────────────────┘                                                                    │
│                                                                                                             │
│    Pemetaan Akun                                                                                          │
│    ─────────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐                              │
│    │ Akun Hutang*                    │  │ Akun Piutang*                   │                              │
│    │ 2-20100) Hutang                 │  │ (1-20100) Piutang               │                              │
│    └─────────────────────────────────┘  └─────────────────────────────────┘                              │
│                                                                                                             │
│    ┌─────────────────────────────────┐                                                                    │
│    │ Syarat Pembayaran*              │                                                                    │
│    │ Net 30                          │                                                                    │
│    └─────────────────────────────────┘                                                                    │
│                                                                                                             │
│                                                                           ┌─────────────┐ ┌─────────────┐ │
│                                                                           │ Batalkan    │ │ Simpan      │ │
│                                                                           │ [Red]       │ │ [Green]     │ │
│                                                                           └─────────────┘ └─────────────┘ │
│                                                                                                             │
└─────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

## Grid-Based Layout

```
┌─ HEADER ─────────────────────────────────────────────────────────────────────────────────┐
│ Kontak Kontak                                                                            │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ SECTION 1: INFO KONTAK ─────────────────────────────────────────────────────────────────┐
│ Info Kontak                                                                              │
│ ─────────────────────────────────────────────────────────────────────────────────────────│
│                                                                                          │
│ ┌─ COL 12 ─────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ Nama Kontak* ──────────────────────────────────────────────────────────────────┐ │ │
│ │ │ [Text Input]                                                                    │ │ │
│ │ └─────────────────────────────────────────────────────────────────────────────────┘ │ │
│ │                                                                                     │ │
│ │ Tipe Kontak*                                                                        │ │
│ │ ○ Pelanggan    ○ Supplier    ○ Karyawan    ○ Lainnya                              │ │
│ │                                                                                     │ │
│ │ ┌─ Grup Kontak ───────────────────────────────────────────────────────────────────┐ │ │
│ │ │ [Multi-select Dropdown]                                                         │ │ │
│ │ └─────────────────────────────────────────────────────────────────────────────────┘ │ │
│ └─────────────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ SECTION 2: INFO UMUM ───────────────────────────────────────────────────────────────────┐
│ Info Umum                                                                                │
│ ─────────────────────────────────────────────────────────────────────────────────────────│
│                                                                                          │
│ ┌─ ROW 1 ──────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 4 ─────────────────────┐ ┌─ COL 4 ─────────────────────┐ ┌─ COL 4 ──────────┐ │ │
│ │ │ Nama Perusahaan*            │ │ Nomor Handphone             │ │ Nomor Telepon    │ │ │
│ │ │ [Text Input]                │ │ [Text Input]                │ │ [Text Input]     │ │ │
│ │ └─────────────────────────────┘ └─────────────────────────────┘ └──────────────────┘ │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ ROW 2 ──────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 6 ─────────────────────┐    ┌─ COL 6 ─────────────────────┐                  │ │
│ │ │ Identitas*                  │    │ Email                       │                  │ │
│ │ │ "KTP" [Text Input]          │    │ [Email Input]               │                  │ │
│ │ └─────────────────────────────┘    └─────────────────────────────┘                  │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ SECTION 3: INFO PERUSAHAAN ─────────────────────────────────────────────────────────────┐
│ Info Perusahaan                                                                          │
│ ─────────────────────────────────────────────────────────────────────────────────────────│
│                                                                                          │
│ ┌─ ROW 1 ──────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 6 ─────────────────────┐    ┌─ COL 6 ─────────────────────┐                  │ │
│ │ │ Nama Perusahaan*            │    │ Nomor Identitas             │                  │ │
│ │ │ [Text Input]                │    │ [Text Input]                │                  │ │
│ │ └─────────────────────────────┘    └─────────────────────────────┘                  │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ ROW 2 ──────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 6 ─────────────────────┐    ┌─ COL 6 ─────────────────────┐                  │ │
│ │ │ Fax*                        │    │ NPWP*                       │                  │ │
│ │ │ [Text Input]                │    │ [Text Input]                │                  │ │
│ │ └─────────────────────────────┘    └─────────────────────────────┘                  │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ ROW 3 ──────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 12 ────────────────────────────────────────────────────────────────────────┐ │ │
│ │ │ Alamat Penagihan*                                                               │ │ │
│ │ │ [Textarea - 3 rows]                                                             │ │ │
│ │ └─────────────────────────────────────────────────────────────────────────────────┘ │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ ROW 4 ──────────────────────────────────────────────────────────────────────────────┐ │
│ │ ☐ Samakan dengan Alamat Perusahaan                                                  │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ ROW 5 ──────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 2 ──┐ ┌─ COL 2 ──┐ ┌─ COL 2 ──┐ ┌─ COL 2 ──┐ ┌─ COL 2 ──┐ ┌─ COL 2 ──┐     │ │
│ │ │ [Small] │ │ [Small] │ │ [Small] │ │ [Small] │ │ [Small] │ │ [Small] │     │ │
│ │ │ Field   │ │ Field   │ │ Field   │ │ Field   │ │ Field   │ │ Field   │     │ │
│ │ └─────────┘ └─────────┘ └─────────┘ └─────────┘ └─────────┘ └─────────┘     │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ ROW 6 ──────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 12 ────────────────────────────────────────────────────────────────────────┐ │ │
│ │ │ Alamat Pengiriman                                                               │ │ │
│ │ │ [Textarea - 3 rows]                                                             │ │ │
│ │ └─────────────────────────────────────────────────────────────────────────────────┘ │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ SECTION 4: INFO BANK ───────────────────────────────────────────────────────────────────┐
│ Info Bank                                                                                │
│ ─────────────────────────────────────────────────────────────────────────────────────────│
│                                                                                          │
│ ┌─ ROW 1 ──────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 6 ─────────────────────┐    ┌─ COL 6 ─────────────────────┐                  │ │
│ │ │ Nama Bank*                  │    │ Kantor Cabang               │                  │ │
│ │ │ [Text Input]                │    │ [Text Input]                │                  │ │
│ │ └─────────────────────────────┘    └─────────────────────────────┘                  │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ ROW 2 ──────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 6 ─────────────────────┐    ┌─ COL 6 ─────────────────────┐                  │ │
│ │ │ Pemegang Akun Bank*         │    │ Nomor Rekening*             │                  │ │
│ │ │ [Text Input]                │    │ [Text Input]                │                  │ │
│ │ └─────────────────────────────┘    └─────────────────────────────┘                  │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ ROW 3 ──────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 12 ────────────────────────────────────────────────────────────────────────┐ │ │
│ │ │ Tambah Akun Bank Lain                                                           │ │ │
│ │ │ [Button - Blue Background]                                                      │ │ │
│ │ └─────────────────────────────────────────────────────────────────────────────────┘ │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ SECTION 5: PEMETAAN AKUN ───────────────────────────────────────────────────────────────┐
│ Pemetaan Akun                                                                            │
│ ─────────────────────────────────────────────────────────────────────────────────────────│
│                                                                                          │
│ ┌─ ROW 1 ──────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 6 ─────────────────────┐    ┌─ COL 6 ─────────────────────┐                  │ │
│ │ │ Akun Hutang*                │    │ Akun Piutang*               │                  │ │
│ │ │ (2-20100) Hutang [Dropdown] │    │ (1-20100) Piutang [Dropdown]│                  │ │
│ │ └─────────────────────────────┘    └─────────────────────────────┘                  │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ ROW 2 ──────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 6 ─────────────────────┐                                                      │ │
│ │ │ Syarat Pembayaran*          │                                                      │ │
│ │ │ "Net 30" [Dropdown]         │                                                      │ │
│ │ └─────────────────────────────┘                                                      │ │
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
Info Kontak: [All fields - 12 cols]
Info Umum: [Company - 4 cols] [Mobile - 4 cols] [Phone - 4 cols]
           [Identity - 6 cols] [Email - 6 cols]
Info Perusahaan: [Company - 6 cols] [ID Number - 6 cols]
                 [Fax - 6 cols] [NPWP - 6 cols]
                 [Billing Address - 12 cols]
                 [Location fields - 2 cols each, 6 total]
                 [Shipping Address - 12 cols]
Info Bank: [Bank Name - 6 cols] [Branch - 6 cols]
           [Account Holder - 6 cols] [Account Number - 6 cols]
           [Add Bank Button - 12 cols]
Pemetaan Akun: [Payable - 6 cols] [Receivable - 6 cols]
               [Payment Terms - 6 cols]
Actions: [Buttons right-aligned]
```

### Tablet (8 columns)
```
All sections: Maintain 2-column layout where possible
Reduce to single column for complex fields
Location fields: Stack in pairs (4 cols each)
Buttons: Full width stacked
```

### Mobile (4 columns)
```
All sections: [Full width - 4 cols]
All fields stacked vertically
Radio buttons: Stacked vertically
Location fields: Full width stacked
Buttons: Full width stacked
```

## Key Layout Features

### **Contact Edit Specific Elements:**
- **Form Title**: "Kontak Kontak" indicating contact editing functionality
- **Help System**: Integration with popup help text for field guidance
- **Dynamic Fields**: Contact type-based field visibility and validation
- **Multi-section Organization**: Logical grouping of related contact information

### **Form Structure (5 Main Sections):**
1. **Info Kontak**: Basic contact identification and type selection
2. **Info Umum**: General contact information (personal/business details)
3. **Info Perusahaan**: Company-specific information and addresses
4. **Info Bank**: Banking information with multiple account support
5. **Pemetaan Akun**: Accounting integration with chart of accounts

### **Contact Type Integration:**
- **Radio Button Selection**: Horizontal layout for contact type selection
- **Dynamic Field Display**: Fields appear/disappear based on contact type
- **Conditional Validation**: Different validation rules per contact type
- **Business Logic**: Appropriate defaults and field behavior per type

### **Address Management:**
- **Multiple Address Types**: Billing address and shipping address
- **Address Synchronization**: Checkbox to sync billing and company address
- **Location Details**: Grid layout for city, state, postal code fields
- **Flexible Address Input**: Support for various address formats

### **Banking Information:**
- **Multiple Bank Support**: "Tambah Akun Bank Lain" for additional accounts
- **Complete Banking Details**: Bank name, branch, account holder, account number
- **Financial Integration**: Foundation for payment processing and reconciliation

### **Account Mapping:**
- **Chart of Accounts Integration**: Dropdown selection from accounting system
- **Contact Type Specific**: Payable accounts for suppliers, receivable for customers
- **Payment Terms**: Default payment terms for transaction processing
- **Sample Account Codes**: (2-20100) Hutang, (1-20100) Piutang

### **Interactive Elements:**
- **Radio Buttons**: Contact type selection with single choice
- **Multi-select Dropdown**: Group selection with multiple options
- **Conditional Fields**: Dynamic field visibility based on selections
- **Address Sync**: Checkbox to synchronize address fields
- **Add Bank Button**: Dynamic addition of banking information

### **Form Validation:**
- **Required Field Indicators**: Asterisks (*) marking mandatory fields
- **Contact Type Validation**: Different requirements per contact type
- **Email Format**: Email field with proper format validation
- **Account Validation**: Chart of accounts integration with valid selections

### **Data Examples:**
- **Identity Sample**: "KTP" as default identity type
- **Account Samples**: Proper Indonesian chart of accounts formatting
- **Payment Terms**: "Net 30" as default payment terms
- **Field Organization**: Logical grouping for efficient data entry

### **Business Logic Implementation:**
- **Contact Type Behavior**: Dynamic field management based on type selection
- **Address Relationships**: Intelligent address field synchronization
- **Banking Support**: Multiple bank account capability for complex relationships
- **Accounting Integration**: Proper chart of accounts mapping for transaction processing

### **User Experience Features:**
- **Logical Field Flow**: Intuitive progression through contact information
- **Help Integration**: Context-sensitive help for complex fields
- **Responsive Design**: Optimal layout across different screen sizes
- **Data Integrity**: Validation and consistency checks throughout the form

This contact edit form provides comprehensive functionality for managing all aspects of contact information while maintaining proper integration with business processes, accounting systems, and transaction workflows.