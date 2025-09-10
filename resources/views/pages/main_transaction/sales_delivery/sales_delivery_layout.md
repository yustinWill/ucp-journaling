# Sales Delivery (SD) Creation Form - Visual Layout

## ASCII Layout Representation

```
┌──────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────
│ Header                                                                                                      │
│ Menu                                                                                                        │
│                                                                                                             │
│    Tambah Delivery                                                  ← Kembali                            │
│    Referensi: PO-202508-673                                                                               │
│                                                                                                             │
│    ┌─ LEFT COLUMN (COL 8) ──────────────────────────────────    ┌─ RIGHT COLUMN (COL 4) ─────────────────┐ │
│    │                                                         │    │ Requirement SALES Delivery (SD)        │ │
│    │ Supplier                             Email               │    │ 1. Relasi dengan SALES Order (SO)      │ │
│    │ CV TOBA GRAFIKA                     │                   │    │ SALES Delivery (SD) hanya bisa dibuat │ │
│    │                                     │                   │    │ jika ada SALES Order (SO).            │ │
│    │ Alamat pengiriman                   │ Tgl. pengiriman   │    │ Saat user buat SD, user harus Silih    │ │
│    │                                     │ 26/08/2025        │    │ referensi SO terlebih dahulu.          │ │
│    │                                     │                   │    │ Semua data dasar (Customer, alamat     │ │
│    │ Kirim melalui                       │ No Transaksi      │    │ pengiriman, tanggal SO, item SO, qty   │ │
│    │                                     │ Purchase Delivery │    │ SO, dll.) autofill dari SO.           │ │
│    │ No. pelacakan                       │ #2508.01401/DO/STG│    │                                        │ │
│    │                                     │                   │    │ A. Header Informasi (Autofill dari SO) │ │
│    │                                     │ No Referensi      │    │ Field         Deskripsi                │ │
│    │                                     │                   │    │ Referensi SO  Nomor SALES Order yang   │ │
│    │                                     │ No pesanan        │    │               dijadikan acuan pembuatan │ │
│    │                                     │ Purchase Order    │    │               SALES Delivery.          │ │
│    │                                     │ #PO/SYN/15/0825-2 │    │ Customer      Nama Customer dari SO.    │ │
│    │                                     │                   │    │ Alamat Pengiriman Alamat pengiriman    │ │
│    │                                     │ Tag               │    │               dari SO.                  │ │
│    │                                     │                   │    │ Tgl. Pengiriman Tanggal pengiriman     │ │
│    │                                     │                   │    │               sesuai SO, bisa diedit    │ │
│    │                                     │                   │    │               sesuai kebutuhan.         │ │
│    │ Informasi Delivery                                      │    │ Email         Alamat email Customer     │ │
│    │ ────────────────────────────────────────────────────   │    │               (jika tersedia).          │ │
│    │                                                         │    │ No Transaksi SD Nomor unik transaksi   │ │
│    │ Nomor Delivery *                    Tanggal Delivery *  │    │               SA x                       │ │
│    │ DN-003                              08/28/2025    📅   │    │ No Pesanan (SO Ref) Nomor SALES Order  │ │
│    │                                                         │    │               yang menjadi referensi.   │ │
│    │ Nomor Resi                          Kurir/Ekspedisi    │    │ No Pelacakan  Nomor tracking dari       │ │
│    │ Masukkan nomor resi                 Nama kurir/ekspedisi│    │               ekspedisi.                │ │
│    │                                                         │    │ Tag           Label/tag tambahan untuk  │ │
│    │ Tag                                 Keterangan          │    │               klasifikasi.              │ │
│    │ Masukkan tag (opsional)             Keterangan delivery │    │ Keterangan    Catatan tambahan terkait  │ │
│    │                                     (opsional)          │    │               delivery.                 │ │
│    │                                     ┌───────────────────│    │                                        │ │
│    │                                     │                   │    │ B. Informasi Delivery                  │ │
│    │                                     │                   │    │ Field         Deskripsi                │ │
│    │                                     │                   │    │ Nomor Delivery Nomor delivery (contoh: │ │
│    │                                     │                   │    │               DN-003), auto-generate    │ │
│    │                                     │                   │    │               namun bisa diedit.        │ │
│    │                                     │                   │    │ Tanggal Delivery Tanggal aktual        │ │
│    │                                     │                   │    │               delivery yang diinput     │ │
│    │                                     │                   │    │               oleh user.                │ │
│    │                                     │                   │    │ Nomor Resi    Nomor resi pengiriman.   │ │
│    │                                     │                   │    │ Kurir/Ekspedisi Nama kurir atau        │ │
│    │                                     │                   │    │               ekspedisi yang mengirim   │ │
│    │                                     │                   │    │               barang.                   │ │
│    │                                     │                   │    │ Tag           Label/tag opsional untuk  │ │
│    │                                     │                   │    │               memudahkan pencarian/     │ │
│    │                                     │                   │    │               filter.                   │ │
│    │                                     │                   │    │ Keterangan    Catatan khusus terkait    │ │
│    │                                     │                   │    │               delivery.                 │ │
│    │                                     └───────────────────│    │                                        │ │
│    │                                                         │    │ C. Items Delivery                      │ │
│    │ Items Delivery                                          │    │ Field         Deskripsi                │ │
│    │ ────────────────────────────────────────────────────   │    │ Produk        Nama barang yang muncul   │ │
│    │                                                         │    │               dari SO.                  │ │
│    │ ┌─ INFO BOX ─────────────────────────────────────────┐  │    │ Qty SO        Jumlah yang dipesan pada │ │
│    │ │ Info: Pilih dan masukkan jumlah item yang akan   │  │    │               SO.                       │ │
│    │ │ di-deliver. Sistem akan otomatis menghitung sisa │  │    │ Delivered Qty Jumlah barang yang sudah │ │
│    │ │ quantity.                                         │  │    │               dikirim pada SD sebelumnya.│ │
│    │ └───────────────────────────────────────────────────┘  │    │ Remaining Qty Sisa jumlah barang yang   │ │
│    │                                                         │    │               belum dikirim (Qty SO –   │ │
│    │ ┌─ ITEMS TABLE ─────────────────────────────────────┐   │    │               Delivered Qty).           │ │
│    │ │ Produk          │ Qty PO │ Delivered │ Remaining │   │    │ Qty Delivery  Jumlah barang yang akan   │ │
│    │ │                 │        │           │ Qty       │   │    │               dikirim pada SD ini, wajib│ │
│    │ ├─────────────────┼────────┼───────────┼───────────┤   │    │               diinput oleh user.        │ │
│    │ │ Laptop Dell     │   5    │     0     │     5     │   │    │                                        │ │
│    │ │ Inspiron 15     │        │           │           │   │    │                                        │ │
│    │ ├─────────────────┼────────┼───────────┼───────────┤   │    │                                        │ │
│    │ │ Mouse Wireless  │  10    │     0     │    10     │   │    │                                        │ │
│    │ │ Logitech        │        │           │           │   │    │                                        │ │
│    │ ├─────────────────┼────────┼───────────┼───────────┤   │    │                                        │ │
│    │ │ Keyboard        │   5    │     0     │     5     │   │    │                                        │ │
│    │ │ Mechanical      │        │           │           │   │    │                                        │ │
│    │ │                 │        │           │           │   │    │                                        │ │
│    │ │                 │        │           │           │   │    │                                        │ │
│    │ │                 │        │           │           │   │    │                                        │ │
│    │ │                 │        │           │           │   │    │                                        │ │
│    │ │                 │        │           │           │   │    │                                        │ │
│    │ │                 │        │           │           │   │    │                                        │ │
│    │ │                 │        │           │           │   │    │                                        │ │
│    │ │                 │        │           │           │   │    │                                        │ │
│    │ │                 │        │           │           │   │    │                                        │ │
│    │ │                 │        │           │           │   │    │                                        │ │
│    │ │                 │        │           │           │   │    │                                        │ │
│    │ │                 │        │           │           │   │    │                                        │ │
│    │ │                 │        │           │           │   │    │                                        │ │
│    │ │                 │        │           │           │   │    │                                        │ │
│    │ │                 │        │           │           │   │    │                                        │ │
│    │ │                 │        │           │           │   │    │                                        │ │
│    │ │                 │        │           │           │   │    │                                        │ │
│    │ │                 │        │           │           │   │    │                                        │ │
│    │ │                 │        │           │           │   │    │                                        │ │
│    │ │                 │        │           │           │   │    │                                        │ │
│    │ │                 │        │           │           │   │    │                                        │ │
│    │ │                 │        │           │           │   │    │                                        │ │
│    │ │                 │        │           │           │   │    │                                        │ │
│    │ │                 │        │           │           │   │    │                                        │ │
│    │ │                 │        │           │           │   │    │                                        │ │
│    │ │                 │        │           │           │   │    │                                        │ │
│    │ └─────────────────┴────────┴───────────┴───────────┘   │    │                                        │ │
│    │                                                         │    │                                        │ │
│    │ Total Qty Delivery: 0 items                           │    │                                        │ │
│    │                                                         │    │                                        │ │
│    │                      ┌─────────┐ ┌──────────────────┐   │    │                                        │ │
│    │                      │ Batal   │ │ 📦 Simpan Delivery│   │    │                                        │ │
│    │                      │ [Gray]  │ │ [Blue]            │   │    │                                        │ │
│    │                      └─────────┘ └──────────────────┘   │    │                                        │ │
│    └─────────────────────────────────────────────────────────┘    └────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────────────────────────
```

## Grid-Based Layout

```
┌─ HEADER ─────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ Tambah Delivery                                             ← Kembali                                        │
│ Referensi: PO-202508-673                                                                                      │
└────────────────────────────────────────────────────────────────────────────────────────────────────────────┘

┌─ MAIN CONTENT SPLIT LAYOUT ─────────────────────────────────────────────────────────────────────────────────┐
│ ┌─ LEFT COLUMN (COL 8) ──────────────────────────    ┌─ RIGHT COLUMN (COL 4) ─────────────────────────────┐ │
│ │                                                 │    │ Requirement Information Panel                   │ │
│ │ ┌─ SECTION 1: SO HEADER INFO (AUTO-FILLED) ───┐ │    │ ┌─ Requirements List ─────────────────────────┐ │ │
│ │ │ ┌─ ROW 1 ─────────────────────────────────┐ │ │    │ │ A. Header Informasi (Autofill dari SO)    │ │ │
│ │ │ │ ┌─ COL 6 ─────────┐ ┌─ COL 6 ─────────┐ │ │    │ │ B. Informasi Delivery                     │ │ │
│ │ │ │ │ Supplier        │ │ Email           │ │ │    │ │ C. Items Delivery                          │ │ │
│ │ │ │ │ [Display]       │ │ [Display]       │ │ │    │ └───────────────────────────────────────────┘ │ │
│ │ │ │ └─────────────────┘ └─────────────────┘ │ │    └─────────────────────────────────────────────────┘ │
│ │ │ └─────────────────────────────────────────┘ │ │                                                      │
│ │ │                                             │ │                                                      │
│ │ │ ┌─ ROW 2 ─────────────────────────────────┐ │ │                                                      │
│ │ │ │ ┌─ COL 6 ─────────┐ ┌─ COL 6 ─────────┐ │ │                                                      │
│ │ │ │ │ Alamat          │ │ Tgl. pengiriman │ │ │                                                      │
│ │ │ │ │ pengiriman      │ │ [Display]       │ │ │                                                      │
│ │ │ │ │ [Display]       │ │                 │ │ │                                                      │
│ │ │ │ └─────────────────┘ └─────────────────┘ │ │                                                      │
│ │ │ └─────────────────────────────────────────┘ │ │                                                      │
│ │ │                                             │ │                                                      │
│ │ │ ┌─ ROW 3 ─────────────────────────────────┐ │ │                                                      │
│ │ │ │ ┌─ COL 6 ─────────┐ ┌─ COL 6 ─────────┐ │ │                                                      │
│ │ │ │ │ Kirim melalui   │ │ No Transaksi    │ │ │                                                      │
│ │ │ │ │ [Display]       │ │ [Display]       │ │ │                                                      │
│ │ │ │ └─────────────────┘ └─────────────────┘ │ │                                                      │
│ │ │ └─────────────────────────────────────────┘ │ │                                                      │
│ │ │                                             │ │                                                      │
│ │ │ ┌─ ROW 4 ─────────────────────────────────┐ │ │                                                      │
│ │ │ │ ┌─ COL 6 ─────────┐ ┌─ COL 6 ─────────┐ │ │                                                      │
│ │ │ │ │ No. pelacakan   │ │ No Referensi    │ │ │                                                      │
│ │ │ │ │ [Display]       │ │ [Display]       │ │ │                                                      │
│ │ │ │ └─────────────────┘ └─────────────────┘ │ │                                                      │
│ │ │ └─────────────────────────────────────────┘ │ │                                                      │
│ │ │                                             │ │                                                      │
│ │ │ ┌─ ROW 5 ─────────────────────────────────┐ │ │                                                      │
│ │ │ │ ┌─ COL 6 ─────────┐ ┌─ COL 6 ─────────┐ │ │                                                      │
│ │ │ │ │ [Empty]         │ │ No pesanan      │ │ │                                                      │
│ │ │ │ │                 │ │ [Display]       │ │ │                                                      │
│ │ │ │ └─────────────────┘ └─────────────────┘ │ │                                                      │
│ │ │ └─────────────────────────────────────────┘ │ │                                                      │
│ │ │                                             │ │                                                      │
│ │ │ ┌─ ROW 6 ─────────────────────────────────┐ │ │                                                      │
│ │ │ │ ┌─ COL 6 ─────────┐ ┌─ COL 6 ─────────┐ │ │                                                      │
│ │ │ │ │ [Empty]         │ │ Tag             │ │ │                                                      │
│ │ │ │ │                 │ │ [Display]       │ │ │                                                      │
│ │ │ │ └─────────────────┘ └─────────────────┘ │ │                                                      │
│ │ │ └─────────────────────────────────────────┘ │ │                                                      │
│ │ └─────────────────────────────────────────────┘ │                                                      │
│ │                                                 │                                                      │
│ │ ┌─ SECTION 2: DELIVERY INFORMATION ────────────┐ │                                                      │
│ │ │ Informasi Delivery                          │ │                                                      │
│ │ │ ─────────────────────────────────────────── │ │                                                      │
│ │ │                                             │ │                                                      │
│ │ │ ┌─ ROW 1 ─────────────────────────────────┐ │ │                                                      │
│ │ │ │ ┌─ COL 6 ─────────┐ ┌─ COL 6 ─────────┐ │ │                                                      │
│ │ │ │ │ Nomor Delivery* │ │ Tanggal         │ │ │                                                      │
│ │ │ │ │ [Text Input]    │ │ Delivery*       │ │ │                                                      │
│ │ │ │ │ "DN-003"        │ │ [Date Input]    │ │ │                                                      │
│ │ │ │ └─────────────────┘ └─────────────────┘ │ │                                                      │
│ │ │ └─────────────────────────────────────────┘ │ │                                                      │
│ │ │                                             │ │                                                      │
│ │ │ ┌─ ROW 2 ─────────────────────────────────┐ │ │                                                      │
│ │ │ │ ┌─ COL 6 ─────────┐ ┌─ COL 6 ─────────┐ │ │                                                      │
│ │ │ │ │ Nomor Resi      │ │ Kurir/Ekspedisi │ │ │                                                      │
│ │ │ │ │ [Text Input]    │ │ [Text Input]    │ │ │                                                      │
│ │ │ │ └─────────────────┘ └─────────────────┘ │ │                                                      │
│ │ │ └─────────────────────────────────────────┘ │ │                                                      │
│ │ │                                             │ │                                                      │
│ │ │ ┌─ ROW 3 ─────────────────────────────────┐ │ │                                                      │
│ │ │ │ ┌─ COL 6 ─────────┐ ┌─ COL 6 ─────────┐ │ │                                                      │
│ │ │ │ │ Tag             │ │ Keterangan      │ │ │                                                      │
│ │ │ │ │ [Text Input]    │ │ [Textarea]      │ │ │                                                      │
│ │ │ │ └─────────────────┘ └─────────────────┘ │ │                                                      │
│ │ │ └─────────────────────────────────────────┘ │ │                                                      │
│ │ └─────────────────────────────────────────────┘ │                                                      │
│ │                                                 │                                                      │
│ │ ┌─ SECTION 3: ITEMS DELIVERY ──────────────────┐ │                                                      │
│ │ │ Items Delivery                              │ │                                                      │
│ │ │ ─────────────────────────────────────────── │ │                                                      │
│ │ │                                             │ │                                                      │
│ │ │ ┌─ INFO BOX ─────────────────────────────┐ │ │                                                      │
│ │ │ │ Info: Pilih dan masukkan jumlah item  │ │ │                                                      │
│ │ │ │ yang akan di-deliver. Sistem akan     │ │ │                                                      │
│ │ │ │ otomatis menghitung sisa quantity.    │ │ │                                                      │
│ │ │ └───────────────────────────────────────┘ │ │                                                      │
│ │ │                                             │ │                                                      │
│ │ │ ┌─ ITEMS TABLE ─────────────────────────┐ │ │                                                      │
│ │ │ │ ┌─ COL 3 ──┐ ┌─ COL 2 ┐ ┌─ COL 2 ──┐ │ │ │                                                      │
│ │ │ │ │ Produk   │ │ Qty PO │ │ Delivered│ │ │ │                                                      │
│ │ │ │ │[Display] │ │[Display│ │[Display] │ │ │ │                                                      │
│ │ │ │ └──────────┘ └────────┘ └─────────┘ │ │ │                                                      │
│ │ │ │                                     │ │ │                                                      │
│ │ │ │ ┌─ COL 2.5 ┐ ┌─ COL 2.5 ──────────┐ │ │ │                                                      │
│ │ │ │ │ Remaining│ │ Qty Delivery       │ │ │ │                                                      │
│ │ │ │ │ [Display]│ │ [Number Input]     │ │ │ │                                                      │
│ │ │ │ └──────────┘ └────────────────────┘ │ │ │                                                      │
│ │ │ │                                     │ │ │                                                      │
│ │ │ │ [Sample rows from SO items]         │ │ │                                                      │
│ │ │ │ Laptop Dell Inspiron 15             │ │ │                                                      │
│ │ │ │ Mouse Wireless Logitech             │ │ │                                                      │
│ │ │ │ Keyboard Mechanical                 │ │ │                                                      │
│ │ │ └─────────────────────────────────────┘ │ │                                                      │
│ │ │                                             │ │                                                      │
│ │ │ Total Qty Delivery: 0 items                │ │                                                      │
│ │ └─────────────────────────────────────────────┘ │                                                      │
│ │                                                 │                                                      │
│ │ ┌─ ACTION BUTTONS ──────────────────────────────┐ │                                                      │
│ │ │                  ┌─────────┐ ┌──────────────┐ │ │                                                      │
│ │ │                  │ Batal   │ │📦 Simpan     │ │ │                                                      │
│ │ │                  │ [Gray]  │ │ Delivery     │ │ │                                                      │
│ │ │                  │         │ │ [Blue]       │ │ │                                                      │
│ │ │                  └─────────┘ └──────────────┘ │ │                                                      │
│ │ └─────────────────────────────────────────────────┘ │                                                      │
│ └─────────────────────────────────────────────────────┘ └──────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

## Responsive Breakpoints

### Desktop (12 columns)
```
Header: [Title + Back Button - 12 cols]
Main Layout: [Form Content - 8 cols] [Requirements Panel - 4 cols]
SO Header Info: [6 fields in 2-column layout]
Delivery Info: [Input fields in 2-column layout]
Items Table: [Full width with 5 columns]
Actions: [Buttons right-aligned]
```

### Tablet (8 columns)
```
Header: [Title + Back - 8 cols]
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

### **Sales Delivery Specific Elements:**
- **SO Reference**: Clear reference to source Sales Order with back navigation
- **Auto-filled Header**: Customer and order information populated from SO
- **Delivery Tracking**: Delivery number, tracking number, and courier information
- **Items from SO**: Display SO items with delivery quantity tracking

### **Form Structure (3 Main Sections):**
1. **SO Header Information**: Auto-filled customer and order details from Sales Order
2. **Delivery Information**: Delivery-specific details (number, date, tracking, courier)
3. **Items Delivery**: SO items with delivery quantity input and remaining tracking

### **Sales Order Integration:**
- **SO Reference Display**: Shows originating Sales Order number prominently
- **Customer Information**: Auto-populated from SO (supplier, email, address)
- **Order Details**: Transaction numbers, dates, and references from SO
- **Item Tracking**: Shows ordered quantities and delivery progress

### **Delivery Management Features:**
- **Delivery Number**: Auto-generated with edit capability (DN-XXX format)
- **Delivery Date**: Required date input for actual delivery
- **Tracking Integration**: Tracking number and courier/expedition information
- **Delivery Notes**: Optional tags and detailed notes for delivery

### **Item Delivery Tracking:**
- **SO Items Display**: Shows all items from the originating Sales Order
- **Quantity Tracking**: Displays ordered quantity, delivered quantity, and remaining
- **Delivery Input**: User enters quantity being delivered in this shipment
- **Progress Validation**: Ensures delivery quantities don't exceed remaining amounts
- **Total Summary**: Shows total items being delivered in current shipment

### **Interactive Elements:**
- **Back Navigation**: Return to previous screen or SO list
- **Delivery Quantity Input**: Real-time calculation of remaining quantities
- **Date Picker**: Delivery date selection with validation
- **Form Validation**: Ensure required fields and valid delivery quantities
- **Auto-calculation**: Update total delivery quantity as items are modified

### **Validation and Business Rules:**
- **SO Dependency**: Can only create delivery from existing Sales Order
- **Quantity Limits**: Delivery quantities cannot exceed remaining SO quantities
- **Required Fields**: Delivery number, delivery date, and at least one item quantity
- **Progress Tracking**: Track delivery progress against original SO quantities

### **Navigation and User Experience:**
- **Breadcrumb Navigation**: Clear path back to Sales Order
- **Reference Display**: Prominent SO reference number throughout form
- **Progress Indicators**: Visual indication of delivery progress per item
- **Responsive Design**: Optimized layout for various screen sizes

### **Document Integration:**
- **SO Linkage**: Maintains strong reference to originating Sales Order
- **Delivery Documentation**: Complete record of delivery details and items
- **Status Updates**: Updates SO delivery status based on delivered quantities
- **Audit Trail**: Comprehensive delivery tracking and history

This sales delivery form provides complete delivery management with SO integration, quantity tracking, and proper validation while maintaining clear reference to the originating sales order and enabling accurate delivery progress monitoring.