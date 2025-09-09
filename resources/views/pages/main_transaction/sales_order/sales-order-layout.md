# Sales Order (SO) Creation Form - Visual Layout

## ASCII Layout Representation

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ Header                                                                                                      │
│ Menu                                                                                                        │
│                                                                                                             │
│    Tambah Sales Order                                                                                      │
│                                                                                                             │
│    Informasi Dasar Transaksi                                                                               │
│    ─────────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐                              │
│    │ Tanggal Transaksi*              │  │ Nomor Transaksi*                │                              │
│    │ 2025-08-27                      │  │ PO-20250B-789                   │ 🔄                          │
│    └─────────────────────────────────┘  └─────────────────────────────────┘                              │
│                                                                                                             │
│    Informasi Supplier                                                                                      │
│    ─────────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐                              │
│    │ Supplier*                       │  │ Email                           │                              │
│    │ John Doe                    ▼   │  │ john.doe@customer.com           │                              │
│    └─────────────────────────────────┘  └─────────────────────────────────┘                              │
│                                                                                                             │
│    ┌─────────────────────────────────────────────────────────────────────────────────────────────────── │
│    │ Alamat                                                                                               │ │
│    │ Alamat akan otomatis terisi                                                                         │ │
│    │                                                                                                     │ │
│    └─────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐                              │
│    │ Nomor Referensi Supplier        │  │ Keterangan                      │                              │
│    │ Masukkan nomor referensi        │  │ Nomor order transaksi...        │                              │
│    └─────────────────────────────────┘  │                                 │                              │
│                                         │                                 │                              │
│                                         │                                 │                              │
│                                         └─────────────────────────────────┘                              │
│                                                                                                             │
│    Detail Pembelian                                                                                        │
│    ─────────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐                              │
│    │ Syarat Pembayaran*              │  │ Gudang*                         │                              │
│    │ Net 30                      ▼   │  │ Pusat Gresik               ▼   │                              │
│    └─────────────────────────────────┘  └─────────────────────────────────┘                              │
│                                                                                                             │
│    ┌─────────────────────────────────────────────────────────────────────────────────────────────────── │
│    │ Keterangan                                                                                           │ │
│    │ Nomor order transaksi...                                                                            │ │
│    │                                                                                                     │ │
│    │                                                                                                     │ │
│    └─────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    Informasi Pembayaran                                                                                   │
│    ─────────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    ☐ Sebelum pilih Supplier, cek Gudang terlebih dahulu untuk dapat memastikan produk                     │
│      tersedia                                                                                              │
│                                                                                                             │
│    ┌─────────────────────────────────────────────────────────────────────────────────────────────────── │
│    │ PRODUK          │ KUANTITAS │ SATUAN   │ HARGA SATUAN │ PAJAK    │ JUMLAH    │ AKSI               │ │
│    ├─────────────────┼───────────┼──────────┼──────────────┼──────────┼───────────┼─────────────────── │
│    │ Pilih Produk ▼  │     Qty   │   Unit   │ Rp 0         │ Tanpa Pajak│    Rp 0   │                   │ │
│    └─────────────────┴───────────┴──────────┴──────────────┴──────────┴───────────┴─────────────────── │
│                                                                                                             │
│    + Tambah Baris                                                                                          │
│                                                                                                             │
│    Informasi Tambahan & Ringkasan                                                                         │
│    ─────────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    ┌─────────────────────────────────┐                    Subtotal           Rp 0                        │
│    │ Tag                             │                                                                    │
│    │ PPN sipil data tambah apa yg diinginkan│               Total Pajak       Rp 0                        │
│    │                                 │                                                                    │
│    │                                 │                    Total Sebelum Diskon  Rp 0                     │
│    │                                 │                                                                    │
│    │                                 │                    Diskon              Rp 0                        │
│    │                                 │                                                                    │
│    │                                 │                    Total Akhir         Rp 0                        │
│    │                                 │                                                                    │
│    │ Memo Internal                   │                                                                    │
│    │ Catatan internal...             │                                                                    │
│    │                                 │                                                                    │
│    │                                 │                                                                    │
│    │                                 │                                                                    │
│    │                                 │                                                                    │
│    │                                 │                                                                    │
│    └─────────────────────────────────┘                                                                    │
│                                                                                                             │
│    Lampiran                                                                                                │
│    ┌─────────────────────────────────┐           ┌─────────────┐ ┌─────────────┐                         │
│    │ Choose File      No File chosen │           │ Batalkan    │ │ Simpan      │                         │
│    └─────────────────────────────────┘           └─────────────┘ └─────────────┘                         │
│    File dapat berupa Excel, Word, PDF, PNG, atau Zip (Max: 10MB)                                         │
│                                                                                                             │
└─────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

## Grid-Based Layout

```
┌─ HEADER ─────────────────────────────────────────────────────────────────────────────────┐
│ Tambah Sales Order                                                                      │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ SECTION 1: INFORMASI DASAR TRANSAKSI ──────────────────────────────────────────────────┐
│ Informasi Dasar Transaksi                                                              │
│ ─────────────────────────────────────────────────────────────────────────────────────────│
│                                                                                          │
│ ┌─ ROW 1 ──────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 6 ─────────────────────┐    ┌─ COL 6 ─────────────────────┐                  │ │
│ │ │ Tanggal Transaksi* [Date]   │    │ Nomor Transaksi* [Text]     │                  │ │
│ │ │ "2025-08-27"                │    │ "PO-20250B-789" 🔄         │                  │ │
│ │ └─────────────────────────────┘    └─────────────────────────────┘                  │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ SECTION 2: INFORMASI SUPPLIER ─────────────────────────────────────────────────────────┐
│ Informasi Supplier                                                                     │
│ ─────────────────────────────────────────────────────────────────────────────────────────│
│                                                                                          │
│ ┌─ ROW 1 ──────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 6 ─────────────────────┐    ┌─ COL 6 ─────────────────────┐                  │ │
│ │ │ Supplier* [Dropdown]        │    │ Email [Text/Auto-fill]      │                  │ │
│ │ │ "John Doe"                  │    │ "john.doe@customer.com"     │                  │ │
│ │ └─────────────────────────────┘    └─────────────────────────────┘                  │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ ROW 2 ──────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 12 ────────────────────────────────────────────────────────────────────────────┐ │ │
│ │ │ Alamat [Textarea/Auto-fill]                                                        │ │ │
│ │ │ "Alamat akan otomatis terisi"                                                      │ │ │
│ │ └────────────────────────────────────────────────────────────────────────────────────┘ │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ ROW 3 ──────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 6 ─────────────────────┐    ┌─ COL 6 ─────────────────────┐                  │ │
│ │ │ Nomor Referensi Supplier    │    │ Keterangan [Textarea]       │                  │ │
│ │ │ [Text]                      │    │ "Nomor order transaksi..."  │                  │ │
│ │ └─────────────────────────────┘    └─────────────────────────────┘                  │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ SECTION 3: DETAIL PEMBELIAN ───────────────────────────────────────────────────────────┐
│ Detail Pembelian                                                                        │
│ ─────────────────────────────────────────────────────────────────────────────────────────│
│                                                                                          │
│ ┌─ ROW 1 ──────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 6 ─────────────────────┐    ┌─ COL 6 ─────────────────────┐                  │ │
│ │ │ Syarat Pembayaran*          │    │ Gudang* [Dropdown]          │                  │ │
│ │ │ [Dropdown] "Net 30"         │    │ "Pusat Gresik"              │                  │ │
│ │ └─────────────────────────────┘    └─────────────────────────────┘                  │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ ROW 2 ──────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 12 ────────────────────────────────────────────────────────────────────────────┐ │ │
│ │ │ Keterangan [Textarea]                                                              │ │ │
│ │ │ "Nomor order transaksi..."                                                         │ │ │
│ │ └────────────────────────────────────────────────────────────────────────────────────┘ │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ SECTION 4: INFORMASI PEMBAYARAN ───────────────────────────────────────────────────────┐
│ Informasi Pembayaran                                                                   │
│ ─────────────────────────────────────────────────────────────────────────────────────────│
│                                                                                          │
│ ┌─ INFO BOX ───────────────────────────────────────────────────────────────────────────┐ │
│ │ ☐ Sebelum pilih Supplier, cek Gudang terlebih dahulu untuk dapat memastikan         │ │
│ │   produk tersedia                                                                    │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ PRODUCT TABLE ──────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 2.5 ─┐ ┌─ COL 1.5 ─┐ ┌─ COL 1.5 ─┐ ┌─ COL 2 ───┐ ┌─ COL 1.5 ─┐ ┌─ COL 1.5 ─┐ │ │
│ │ │ PRODUK    │ │ KUANTITAS │ │ SATUAN    │ │ HARGA      │ │ PAJAK     │ │ JUMLAH    │ │ │
│ │ │ [Dropdown]│ │ [Number]  │ │ [Display] │ │ SATUAN     │ │ [Dropdown]│ │ [Calc]    │ │ │
│ │ │           │ │           │ │           │ │ [Currency] │ │           │ │           │ │ │
│ │ └───────────┘ └───────────┘ └───────────┘ └────────────┘ └───────────┘ └───────────┘ │ │
│ │                                                                                      │ │
│ │ ┌─ COL 1 ─────────────────┐                                                         │ │
│ │ │ AKSI [Remove Button]    │                                                         │ │
│ │ └─────────────────────────┘                                                         │ │
│ │                                                                                      │ │
│ │ [Sample Row:]                                                                        │ │
│ │ Pilih Produk | Qty | Unit | Rp 0 | Tanpa Pajak | Rp 0 | [Remove]                   │ │
│ │                                                                                      │ │
│ │ + Tambah Baris                                                                       │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ SECTION 5: INFORMASI TAMBAHAN & RINGKASAN ─────────────────────────────────────────────┐
│ Informasi Tambahan & Ringkasan                                                         │
│ ─────────────────────────────────────────────────────────────────────────────────────────│
│                                                                                          │
│ ┌─ SPLIT LAYOUT ───────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ LEFT COL 6 ────────────────┐    ┌─ RIGHT COL 6 ───────────────────────────────────┐ │ │
│ │ │ Tag [Textarea]              │    │ Subtotal: Rp 0                                 │ │ │
│ │ │ "PPN sipil data tambah apa  │    │ Total Pajak: Rp 0                              │ │ │
│ │ │  yg diinginkan"             │    │ Total Sebelum Diskon: Rp 0                     │ │ │
│ │ │                             │    │ Diskon: Rp 0                                   │ │ │
│ │ │ Memo Internal [Textarea]    │    │ Total Akhir: Rp 0                              │ │ │
│ │ │ "Catatan internal..."       │    │                                                 │ │ │
│ │ │                             │    │                                                 │ │ │
│ │ │ Lampiran [File Upload]      │    │                                                 │ │ │
│ │ │ "Choose File No File chosen"│    │                                                 │ │ │
│ │ │ Max: 10MB                   │    │                                                 │ │ │
│ │ └─────────────────────────────┘    └────────────────────────────────────────────────┘ │ │
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
Basic Info: [Tanggal - 6 cols] [Nomor Transaksi - 6 cols]
Supplier Info Row 1: [Supplier - 6 cols] [Email - 6 cols]
Supplier Info Row 2: [Alamat - 12 cols]
Supplier Info Row 3: [Nomor Referensi - 6 cols] [Keterangan - 6 cols]
Detail Row 1: [Syarat Pembayaran - 6 cols] [Gudang - 6 cols]
Detail Row 2: [Keterangan - 12 cols]
Product Table: [Full width - 12 cols with 7 columns]
Split Layout: [Left Info - 6 cols] [Right Summary - 6 cols]
Actions: [Buttons right-aligned]
```

### Tablet (8 columns)
```
Header: [Title - 8 cols]
All sections: [Fields stacked in pairs - 8 cols total]
Product Table: [Horizontal scroll - 8 cols]
Split Layout: [Stacked - 8 cols each]
Actions: [Buttons full width]
```

### Mobile (4 columns)
```
All sections: [Full width - 4 cols]
All fields stacked vertically
Product Table: [Card layout or horizontal scroll]
Buttons: [Full width stacked]
```

## Key Layout Features

### **Sales Order Specific Elements:**
- **Title**: "Tambah Sales Order" clearly identifying sales order creation
- **Customer Focus**: Customer/supplier selection with auto-fill capabilities
- **Inventory Integration**: Warehouse selection for stock availability checking
- **Sales Pricing**: Product pricing from customer pricelist with tax calculations

### **Form Structure (5 Main Sections):**
1. **Informasi Dasar Transaksi**: Transaction date and number
2. **Informasi Supplier**: Customer information and contact details
3. **Detail Pembelian**: Payment terms and warehouse selection
4. **Informasi Pembayaran**: Product selection and pricing table
5. **Informasi Tambahan & Ringkasan**: Additional info and financial summary

### **Customer Management Features:**
- **Customer Selection**: Dropdown with existing customer list
- **Auto-fill Integration**: Email and address auto-populated from customer master
- **Customer Reference**: Optional customer reference number field
- **Contact Information**: Complete customer contact details

### **Product and Inventory Management:**
- **Warehouse Selection**: Choose warehouse for stock availability
- **Product Selection**: Dropdown with available products
- **Stock Validation**: Check product availability before adding to order
- **Pricing Integration**: Auto-fill pricing from customer pricelist
- **Tax Management**: Configurable tax options per product line

### **Financial Calculations:**
- **Subtotal**: Sum of all product line totals
- **Tax Calculation**: Automatic tax calculation based on product settings
- **Discount Management**: Support for order-level discounts
- **Final Total**: Complete order total with all adjustments
- **Currency Formatting**: Indonesian Rupiah formatting throughout

### **Interactive Elements:**
- **Customer Selection**: Auto-fill customer information when selected
- **Product Addition**: Dynamic product table with add/remove functionality
- **Real-time Calculations**: Update totals as quantities and prices change
- **Stock Checking**: Validate product availability at selected warehouse

### **Validation and Business Rules:**
- **Stock Availability**: Ensure products are available at selected warehouse
- **Customer Validation**: Require valid customer selection
- **Pricing Validation**: Ensure positive prices and valid calculations
- **Required Fields**: Customer, payment terms, warehouse selection

### **Document Management:**
- **File Attachments**: Support for order documentation
- **Internal Notes**: Memo field for internal processing notes
- **Customer Notes**: Tag field for customer-specific information
- **Audit Trail**: Complete order creation documentation

This sales order form provides comprehensive sales management with customer integration, inventory checking, and complete financial calculations while maintaining proper validation and business rule enforcement.