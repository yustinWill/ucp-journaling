# Purchase Return (PR) Creation Form - Visual Layout

## ASCII Layout Representation

```
┌──────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────
│ Header                                                                                                      │
│ Menu                                                                                                        │
│                                                                                                             │
│    📋 Form Retur Pembelian                                                31/8/2025                       │
│                                                                                                             │
│    ┌─ LEFT COLUMN (COL 8) ──────────────────────────────────    ┌─ RIGHT COLUMN (COL 4) ─────────────────┐ │
│    │                                                         │    │ Requirement Retur Pembelian           │ │
│    │ Nomor Invoice                                           │    │ Flow Retur                             │ │
│    │ INV/2025/0023                                          │    │ Mencatat retur produk dari invoice     │ │
│    │                                                         │    │ pembelian → user dapat memilih produk, │ │
│    │ Supplier                                                │    │ input qty retur, dan alasan retur     │ │
│    │ PT. Supplier Utama                                     │    │ secara terkontrol.                     │ │
│    │                                                         │    │ Mengontrol validasi retur → qty retur │ │
│    │ Tanggal Invoice                                         │    │ tidak boleh melebihi qty sisa retur,  │ │
│    │ 15/8/2025                                              │    │ alasan retur wajib diisi.              │ │
│    │                                                         │    │ Menghasilkan dokumen Sales Return &   │ │
│    │ Total Nilai Invoice                                     │    │ update invoice → sistem membuat nomor │ │
│    │ Rp 15.750.000                                          │    │ retur baru, mengurangi qty sisa retur │ │
│    │                                                         │    │ di invoice, serta menampilkan riwayat │ │
│    │                                                         │    │                                        │ │
│    │ Daftar Produk yang Dapat Diretur                       │    │ Business Rules – Form Retur Pembelian │ │
│    │ ─────────────────────────────────────────────────────  │    │ 1. Akses & Pembuatan                  │ │
│    │                                                         │    │ Form retur hanya bisa dibuat dari     │ │
│    │ ┌─ PRODUCT SELECTION TABLE ──────────────────────────┐ │    │ detail invoice pembelian.              │ │
│    │ │ Select │ Kode  │ Nama     │ Qty  │ Qty Sudah│ Qty │ │    │ Produk yang muncul di form retur       │ │
│    │ │        │ Produk│ Produk   │ Invoice│ Retur  │ Sisa│ │    │ adalah produk yang tercatat di invoice.│ │
│    │ │        │       │          │      │        │ Retur│ │    │                                        │ │
│    │ ├────────┼───────┼──────────┼──────┼────────┼─────┤ │    │ 2. Pemilihan Produk Retur             │ │
│    │ │   ☐    │PRD001 │ Laptop   │  5   │   1    │  4  │ │    │ User hanya bisa memilih produk dengan  │ │
│    │ │        │       │ Asus     │      │        │     │ │    │ Qty Sisa Retur > 0.                   │ │
│    │ │        │       │ VivoBook │      │        │     │ │    │ Jika produk sudah diretur penuh       │ │
│    │ ├────────┼───────┼──────────┼──────┼────────┼─────┤ │    │ (Qty Sisa Retur = 0) → produk tidak   │ │
│    │ │   ☑    │PRD002 │ Mouse    │ 10   │   0    │ 10  │ │    │ bisa dipilih lagi.                     │ │
│    │ │        │       │ Wireless │      │        │     │ │    │ User harus memilih minimal 1 produk   │ │
│    │ │        │       │ Logitech │      │        │     │ │    │ untuk bisa membuat retur.              │ │
│    │ ├────────┼───────┼──────────┼──────┼────────┼─────┤ │    │                                        │ │
│    │ │   ☑    │PRD003 │ Keyboard │  3   │   0    │  3  │ │    │ 3. Input Qty Retur                    │ │
│    │ │        │       │ Mechanical│     │        │     │ │    │ Qty Retur wajib diisi jika produk     │ │
│    │ │        │       │          │      │        │     │ │    │ dipilih.                               │ │
│    │ └────────┴───────┴──────────┴──────┴────────┴─────┘ │    │ Qty Retur tidak boleh 0.              │ │
│    │                                                         │    │ Qty Retur tidak boleh melebihi       │ │
│    │ ┌─ RETURN QUANTITY & REASON TABLE ──────────────────┐   │    │ Qty Sisa Retur → sistem tampilkan    │ │
│    │ │        │ Qty   │ Alasan Retur                    │   │    │ error validasi.                       │ │
│    │ │        │ Retur │                                 │   │    │                                        │ │
│    │ ├────────┼───────┼─────────────────────────────────┤   │    │ 4. Alasan Retur                       │ │
│    │ │   5    │  [▼]  │ Pilih Alasan                    │   │    │ Alasan retur wajib diisi untuk setiap │ │
│    │ │        │       │                                 │   │    │ produk yang dipilih.                   │ │
│    │ ├────────┼───────┼─────────────────────────────────┤   │    │ Jika alasan = "Lainnya" → user wajib  │ │
│    │ │   3    │  [▼]  │ Qty retur tidak boleh           │   │    │ mengisi Keterangan tambahan.          │ │
│    │ │        │       │ kosong !                        │   │    │                                        │ │
│    │ └────────┴───────┴─────────────────────────────────┘   │    │ 5. Validasi & Penyimpanan             │ │
│    │                                                         │    │ Sistem melakukan validasi sebelum     │ │
│    │ Ringkasan Retur                                        │    │ retur disimpan:                        │ │
│    │ Keyboard Mechanical (3 unit)              Rp 2.350.000│    │ - Ada produk yang dipilih.             │ │
│    │ Mouse Wireless Logitech (5 unit)          Rp 625.000  │    │ - Qty retur valid.                     │ │
│    │                                                         │    │ - Alasan retur terisi.                 │ │
│    │ Total Nilai Retur:                        Rp 3.175.000│    │ Jika validasi lolos → sistem simpan   │ │
│    │                                                         │    │ retur, buat dokumen retur, dan update │ │
│    │ Riwayat Retur Invoice Ini                              │    │ qty sudah retur di invoice.            │ │
│    │                                                         │    │ Jika validasi gagal → tampilkan pesan │ │
│    │ ┌─ RETURN HISTORY TABLE ─────────────────────────────┐ │    │ error dan retur tidak bisa disimpan.   │ │
│    │ │ Tanggal │ No. Retur │ Produk        │ Qty │ Alasan │ │    │                                        │ │
│    │ ├─────────┼───────────┼───────────────┼─────┼────────┤ │    │                                        │ │
│    │ │2025-06-26│RT/2025/0018│Laptop Asus   │  1  │Barang │ │    │                                        │ │
│    │ │         │           │VivoBook       │     │ Rusak  │ │    │                                        │ │
│    │ └─────────┴───────────┴───────────────┴─────┴────────┘ │    │                                        │ │
│    │                                                         │    │                                        │ │
│    │                      ┌─────────┐ ┌──────────────────┐   │    │                                        │ │
│    │                      │ ✖ Batal │ │ 🛒 Buat Purchase │   │    │                                        │ │
│    │                      │ [Gray]  │ │ Return [Blue]    │   │    │                                        │ │
│    │                      └─────────┘ └──────────────────┘   │    │                                        │ │
│    └─────────────────────────────────────────────────────────┘    └────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────────────────────────
```

## Grid-Based Layout

```
┌─ HEADER ─────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ 📋 Form Retur Pembelian                                            31/8/2025                                 │
└────────────────────────────────────────────────────────────────────────────────────────────────────────────┘

┌─ MAIN CONTENT SPLIT LAYOUT ─────────────────────────────────────────────────────────────────────────────────┐
│ ┌─ LEFT COLUMN (COL 8) ──────────────────────────    ┌─ RIGHT COLUMN (COL 4) ─────────────────────────────┐ │
│ │                                                 │    │ Requirement Information Panel                   │ │
│ │ ┌─ SECTION 1: INVOICE REFERENCE INFO ──────────┐ │    │ ┌─ Flow Retur ───────────────────────────────┐ │ │
│ │ │ ┌─ ROW 1 ─────────────────────────────────┐ │ │    │ │ Process Flow and Business Rules            │ │ │
│ │ │ │ ┌─ COL 12 ────────────────────────────┐ │ │ │    │ └───────────────────────────────────────────┘ │ │
│ │ │ │ │ Nomor Invoice [Display]             │ │ │ │    └─────────────────────────────────────────────────┘ │
│ │ │ │ │ "INV/2025/0023"                     │ │ │ │                                                      │
│ │ │ │ └─────────────────────────────────────┘ │ │ │                                                      │
│ │ │ └─────────────────────────────────────────┘ │ │                                                      │
│ │ │                                             │ │                                                      │
│ │ │ ┌─ ROW 2 ─────────────────────────────────┐ │ │                                                      │
│ │ │ │ ┌─ COL 12 ────────────────────────────┐ │ │ │                                                      │
│ │ │ │ │ Supplier [Display]                  │ │ │ │                                                      │
│ │ │ │ │ "PT. Supplier Utama"                │ │ │ │                                                      │
│ │ │ │ └─────────────────────────────────────┘ │ │ │                                                      │
│ │ │ └─────────────────────────────────────────┘ │ │                                                      │
│ │ │                                             │ │                                                      │
│ │ │ ┌─ ROW 3 ─────────────────────────────────┐ │ │                                                      │
│ │ │ │ ┌─ COL 12 ────────────────────────────┐ │ │ │                                                      │
│ │ │ │ │ Tanggal Invoice [Display]           │ │ │ │                                                      │
│ │ │ │ │ "15/8/2025"                         │ │ │ │                                                      │
│ │ │ │ └─────────────────────────────────────┘ │ │ │                                                      │
│ │ │ └─────────────────────────────────────────┘ │ │                                                      │
│ │ │                                             │ │                                                      │
│ │ │ ┌─ ROW 4 ─────────────────────────────────┐ │ │                                                      │
│ │ │ │ ┌─ COL 12 ────────────────────────────┐ │ │ │                                                      │
│ │ │ │ │ Total Nilai Invoice [Display]       │ │ │ │                                                      │
│ │ │ │ │ "Rp 15.750.000"                     │ │ │ │                                                      │
│ │ │ │ └─────────────────────────────────────┘ │ │ │                                                      │
│ │ │ └─────────────────────────────────────────┘ │ │                                                      │
│ │ └─────────────────────────────────────────────┘ │                                                      │
│ │                                                 │                                                      │
│ │ ┌─ SECTION 2: PRODUCT SELECTION ───────────────┐ │                                                      │
│ │ │ Daftar Produk yang Dapat Diretur           │ │                                                      │
│ │ │ ─────────────────────────────────────────── │ │                                                      │
│ │ │                                             │ │                                                      │
│ │ │ ┌─ PRODUCT TABLE ───────────────────────┐ │ │                                                      │
│ │ │ │ ┌─ COL 1 ┐ ┌─ COL 1.5┐ ┌─ COL 2.5 ┐ │ │ │                                                      │
│ │ │ │ │ Select │ │ Kode    │ │ Nama     │ │ │ │                                                      │
│ │ │ │ │ [Check]│ │ Produk  │ │ Produk   │ │ │ │                                                      │
│ │ │ │ │        │ │[Display]│ │[Display] │ │ │ │                                                      │
│ │ │ │ └────────┘ └─────────┘ └──────────┘ │ │ │                                                      │
│ │ │ │                                     │ │ │                                                      │
│ │ │ │ ┌─ COL 1.5┐ ┌─ COL 1.5┐ ┌─ COL 2 ┐ │ │ │                                                      │
│ │ │ │ │ Qty     │ │ Qty     │ │ Qty     │ │ │ │                                                      │
│ │ │ │ │ Invoice │ │ Sudah   │ │ Sisa    │ │ │ │                                                      │
│ │ │ │ │[Display]│ │ Retur   │ │ Retur   │ │ │ │                                                      │
│ │ │ │ │         │ │[Display]│ │[Display]│ │ │ │                                                      │
│ │ │ │ └─────────┘ └─────────┘ └─────────┘ │ │ │                                                      │
│ │ │ │                                     │ │ │                                                      │
│ │ │ │ [Sample rows with data]             │ │ │                                                      │
│ │ │ │ PRD001: Laptop Asus VivoBook        │ │ │                                                      │
│ │ │ │ PRD002: Mouse Wireless Logitech     │ │ │                                                      │
│ │ │ │ PRD003: Keyboard Mechanical         │ │ │                                                      │
│ │ │ └─────────────────────────────────────┘ │ │                                                      │
│ │ └─────────────────────────────────────────────┘ │                                                      │
│ │                                                 │                                                      │
│ │ ┌─ SECTION 3: RETURN DETAILS ──────────────────┐ │                                                      │
│ │ │ Return Quantity & Reason Input              │ │                                                      │
│ │ │                                             │ │                                                      │
│ │ │ ┌─ RETURN INPUT TABLE ──────────────────┐ │ │                                                      │
│ │ │ │ ┌─ COL 3 ──┐ ┌─ COL 4 ──────────────┐ │ │ │                                                      │
│ │ │ │ │ Qty Retur│ │ Alasan Retur         │ │ │ │                                                      │
│ │ │ │ │ [Number] │ │ [Dropdown]           │ │ │ │                                                      │
│ │ │ │ └──────────┘ └──────────────────────┘ │ │ │                                                      │
│ │ │ │                                     │ │ │                                                      │
│ │ │ │ [Dynamic rows for selected items]   │ │ │                                                      │
│ │ │ └─────────────────────────────────────┘ │ │                                                      │
│ │ └─────────────────────────────────────────────┘ │                                                      │
│ │                                                 │                                                      │
│ │ ┌─ SECTION 4: RETURN SUMMARY ──────────────────┐ │                                                      │
│ │ │ Ringkasan Retur                             │ │                                                      │
│ │ │ [Return items summary with values]          │ │                                                      │
│ │ │ Total Nilai Retur: Rp 3.175.000            │ │                                                      │
│ │ └─────────────────────────────────────────────┘ │                                                      │
│ │                                                 │                                                      │
│ │ ┌─ SECTION 5: RETURN HISTORY ──────────────────┐ │                                                      │
│ │ │ Riwayat Retur Invoice Ini                   │ │                                                      │
│ │ │ [Previous returns table]                    │ │                                                      │
│ │ └─────────────────────────────────────────────┘ │                                                      │
│ │                                                 │                                                      │
│ │ ┌─ ACTION BUTTONS ──────────────────────────────┐ │                                                      │
│ │ │                  ┌─────────┐ ┌──────────────┐ │ │                                                      │
│ │ │                  │ ✖ Batal │ │🛒 Buat       │ │ │                                                      │
│ │ │                  │ [Gray]  │ │ Purchase     │ │ │                                                      │
│ │ │                  │         │ │ Return       │ │ │                                                      │
│ │ │                  │         │ │ [Blue]       │ │ │                                                      │
│ │ │                  └─────────┘ └──────────────┘ │ │                                                      │
│ │ └─────────────────────────────────────────────────┘ │                                                      │
│ └─────────────────────────────────────────────────────┘ └──────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

## Responsive Breakpoints

### Desktop (12 columns)
```
Header: [Title + Date - 12 cols]
Invoice Info: [Full width display fields - 12 cols]
Product Table: [Integrated table with all columns - 12 cols]
Return Summary: [Full width - 12 cols]
Return History: [Full width table - 12 cols]
Actions: [Buttons centered]
```

### Tablet (8 columns)
```
Header: [Title + Date - 8 cols]
All sections: [Full width - 8 cols]
Product Table: [Horizontal scroll - 8 cols]
Sections: [Stacked vertically]
Actions: [Buttons full width]
```

### Mobile (4 columns)
```
All sections: [Full width - 4 cols]
Product Table: [Card layout with vertical stacking]
All fields stacked vertically
Buttons: [Full width stacked]
```

## Key Layout Features

### **Purchase Return Specific Elements:**
- **Invoice Reference**: Complete purchase invoice information display
- **Product Selection**: Checkbox-based product selection with return quantity tracking
- **Return Input**: Dynamic quantity and reason input for selected products
- **Return Summary**: Real-time calculation of return values
- **Return History**: Previous returns for the same invoice

### **Form Structure (5 Main Sections):**
1. **Invoice Reference Information**: Purchase invoice details and totals
2. **Product Selection**: Available products with return quantity tracking
3. **Return Details**: Quantity and reason input for selected products
4. **Return Summary**: Financial summary of current return
5. **Return History**: Previous returns from same invoice

### **Invoice Integration:**
- **Invoice Data Display**: Complete invoice information including supplier and totals
- **Product Availability**: Shows invoice items with return status tracking
- **Quantity Tracking**: Displays original, returned, and remaining quantities
- **Return Validation**: Ensures return quantities don't exceed available amounts

### **Product Return Management:**
- **Selective Returns**: Checkbox selection for products to return
- **Quantity Control**: Input fields for return quantities with validation
- **Reason Tracking**: Mandatory reason selection for each returned product
- **Real-time Updates**: Dynamic calculation of return totals and summaries

### **Return Processing Features:**
- **Return Validation**: Comprehensive validation before submission
- **Document Generation**: Creates return document and updates invoice
- **Financial Tracking**: Accurate calculation of return values
- **History Management**: Complete return history for audit purposes

### **Interactive Elements:**
- **Product Selection**: Checkbox interface for product selection
- **Quantity Input**: Real-time validation of return quantities
- **Reason Selection**: Dropdown with mandatory reason selection
- **Summary Updates**: Live calculation of return totals
- **Form Validation**: Comprehensive validation before submission

### **Validation and Business Rules:**
- **Product Selection**: Must select at least one product for return
- **Quantity Validation**: Return quantities cannot exceed remaining amounts
- **Reason Requirement**: Mandatory reason for each returned product
- **Zero Quantity Prevention**: Return quantities must be greater than zero
- **Remaining Balance**: Accurate tracking of returnable quantities

### **Financial Integration:**
- **Return Value Calculation**: Automatic calculation of return financial impact
- **Invoice Updates**: Updates invoice return status and quantities
- **Currency Formatting**: Proper Indonesian Rupiah formatting
- **Audit Trail**: Complete return documentation and history

This purchase return form provides comprehensive return management with invoice integration, selective product returns, quantity validation, and proper financial tracking while maintaining complete audit capabilities and business rule enforcement.