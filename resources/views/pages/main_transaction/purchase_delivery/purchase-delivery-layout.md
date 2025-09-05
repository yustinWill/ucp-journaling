# Purchase Delivery (PD) Creation Form - Visual Layout

## ASCII Layout Representation

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ Header                                                                                                      │
│ Menu                                                                                                        │
│                                                                                                             │
│    Tambah Delivery                                                                                         │
│    Reference: PO-202508-673                                                                                │
│                                                                                                             │
│    ┌─────────────────────────────────────────────────────────────────────────────────────────────────── │
│    │ Supplier                              Email                                                         │ │
│    │ PT SYNTOBA GRAFIKA                    supplier@syntoba.com                                          │ │
│    │                                                                                                     │ │
│    │ Alamat pengiriman                     Tel. pengiriman                                              │ │
│    │ Jl. Raya No. 123                      Tel. 08123456789                                             │ │
│    │ Jakarta 12345                                                                                       │ │
│    │                                       No Transaksi                                                 │ │
│    │ Kirim melalui                         Purchase Delivery #PD2508.01401/DO/STG                       │ │
│    │ Express/Courier                                                                                     │ │
│    │                                       No. Referensi                                                │ │
│    │ No. pelacakan                         -                                                             │ │
│    │ -                                                                                                   │ │
│    │                                       No. pesanan                                                  │ │
│    │                                       Purchase Order #PO/SYN/15/0825-2                            │ │
│    │                                                                                                     │ │
│    │                                       Tag                                                           │ │
│    │                                       -                                                             │ │
│    └─────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    Informasi Delivery                                                                                      │
│    ─────────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐                              │
│    │ Nomor Delivery*                 │  │ Tanggal Delivery*               │                              │
│    │ DN-003                          │  │ 08/28/2025                      │                              │
│    └─────────────────────────────────┘  └─────────────────────────────────┘                              │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐                              │
│    │ Nomor Resi                      │  │ Kurir/Ekspedisi                 │                              │
│    │ Masukkan nomor resi             │  │ Nama kurir/ekspedisi            │                              │
│    └─────────────────────────────────┘  └─────────────────────────────────┘                              │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐                              │
│    │ Tag                             │  │ Keterangan                      │                              │
│    │ Masukkan tag (optional)         │  │ Keterangan delivery (optional)  │                              │
│    └─────────────────────────────────┘  │                                 │                              │
│                                         │                                 │                              │
│                                         │                                 │                              │
│                                         └─────────────────────────────────┘                              │
│                                                                                                             │
│    Items Delivery                                                                                          │
│    ─────────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    Info: Pilih dan masukkan jumlah item yang akan di-deliver. Sistem akan otomatis menghitung sisa quantity│
│                                                                                                             │
│    ┌─────────────────────────────────────────────────────────────────────────────────────────────────── │
│    │ Produk          │ Qty PO │ Delivered │ Remaining │ Qty Delivery                                    │ │
│    ├─────────────────┼────────┼───────────┼───────────┼─────────────────────────────────────────────── │
│    │ Laptop Dell     │   5    │     0     │     5     │ [5      ] (input field)                      │ │
│    │ Inspiron 15     │        │           │           │                                                 │ │
│    ├─────────────────┼────────┼───────────┼───────────┼─────────────────────────────────────────────── │
│    │ Mouse Wireless  │   10   │     0     │    10     │ [10     ] (input field)                      │ │
│    │ Logitech        │        │           │           │                                                 │ │
│    ├─────────────────┼────────┼───────────┼───────────┼─────────────────────────────────────────────── │
│    │ Keyboard        │   5    │     0     │     5     │ [5      ] (input field)                      │ │
│    │ Mechanical      │        │           │           │                                                 │ │
│    └─────────────────┴────────┴───────────┴───────────┴─────────────────────────────────────────────── │
│                                                                                                             │
│                                                    Total Qty Delivery: 0 items                            │
│                                                                                                             │
│    ┌─────────────┐ ┌─────────────┐                                                                         │
│    │ Batal       │ │ Simpan      │                                                                         │
│    │             │ │ Delivery    │                                                                         │
│    └─────────────┘ └─────────────┘                                                                         │
│                                                                                                             │
└─────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

## Grid-Based Layout

```
┌─ HEADER ─────────────────────────────────────────────────────────────────────────────────┐
│ Tambah Delivery                                                                         │
│ Reference: PO-202508-673                                                                │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ SECTION 1: PURCHASE ORDER INFORMATION (AUTO-FILLED) ───────────────────────────────────┐
│ ┌─ PO INFO CARD ───────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ LEFT COLUMN (COL 6) ─────────────────────────────────────────────────────────────┐ │ │
│ │ │ Supplier: PT SYNTOBA GRAFIKA                                                      │ │ │
│ │ │ Alamat pengiriman: Jl. Raya No. 123, Jakarta 12345                               │ │ │
│ │ │ Kirim melalui: Express/Courier                                                    │ │ │
│ │ │ No. pelacakan: -                                                                  │ │ │
│ │ └───────────────────────────────────────────────────────────────────────────────────┘ │ │
│ │                                                                                       │ │
│ │ ┌─ RIGHT COLUMN (COL 6) ────────────────────────────────────────────────────────────┐ │ │
│ │ │ Email: supplier@syntoba.com                                                       │ │ │
│ │ │ Tel. pengiriman: Tel. 08123456789                                                 │ │ │
│ │ │ No Transaksi: Purchase Delivery #PD2508.01401/DO/STG                             │ │ │
│ │ │ No. Referensi: -                                                                  │ │ │
│ │ │ No. pesanan: Purchase Order #PO/SYN/15/0825-2                                    │ │ │
│ │ │ Tag: -                                                                            │ │ │
│ │ └───────────────────────────────────────────────────────────────────────────────────┘ │ │
│ └───────────────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ SECTION 2: INFORMASI DELIVERY ─────────────────────────────────────────────────────────┐
│ Informasi Delivery                                                                     │
│ ─────────────────────────────────────────────────────────────────────────────────────────│
│                                                                                          │
│ ┌─ ROW 1 ──────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 6 ─────────────────────┐    ┌─ COL 6 ─────────────────────┐                  │ │
│ │ │ Nomor Delivery* [Text]      │    │ Tanggal Delivery* [Date]    │                  │ │
│ │ │ "DN-003"                    │    │ "08/28/2025"                │                  │ │
│ │ └─────────────────────────────┘    └─────────────────────────────┘                  │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ ROW 2 ──────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 6 ─────────────────────┐    ┌─ COL 6 ─────────────────────┐                  │ │
│ │ │ Nomor Resi [Text]           │    │ Kurir/Ekspedisi [Text]      │                  │ │
│ │ │ "Masukkan nomor resi"       │    │ "Nama kurir/ekspedisi"      │                  │ │
│ │ └─────────────────────────────┘    └─────────────────────────────┘                  │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ ROW 3 ──────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 6 ─────────────────────┐    ┌─ COL 6 ─────────────────────┐                  │ │
│ │ │ Tag [Text]                  │    │ Keterangan [Textarea]       │                  │ │
│ │ │ "Masukkan tag (optional)"   │    │ "Keterangan delivery        │                  │ │
│ │ └─────────────────────────────┘    │  (optional)"                │                  │ │
│ │                                    └─────────────────────────────┘                  │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ SECTION 3: ITEMS DELIVERY ─────────────────────────────────────────────────────────────┐
│ Items Delivery                                                                          │
│ ─────────────────────────────────────────────────────────────────────────────────────────│
│                                                                                          │
│ ┌─ INFO BOX ───────────────────────────────────────────────────────────────────────────┐ │
│ │ Info: Pilih dan masukkan jumlah item yang akan di-deliver.                          │ │
│ │ Sistem akan otomatis menghitung sisa quantity.                                      │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ DELIVERY ITEMS TABLE ───────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 4 ──────┐ ┌─ COL 2 ──┐ ┌─ COL 2 ──┐ ┌─ COL 2 ──┐ ┌─ COL 2 ──────────────┐   │ │
│ │ │ Produk       │ │ Qty PO   │ │ Delivered │ │ Remaining │ │ Qty Delivery       │   │ │
│ │ │ [Display]    │ │ [Display]│ │ [Display] │ │ [Display] │ │ [Input]            │   │ │
│ │ └──────────────┘ └──────────┘ └───────────┘ └───────────┘ └────────────────────┘   │ │
│ │                                                                                      │ │
│ │ [Sample Data Rows:]                                                                  │ │
│ │ Laptop Dell Inspiron 15 | 5 | 0 | 5 | [5] (input field)                            │ │
│ │ Mouse Wireless Logitech | 10 | 0 | 10 | [10] (input field)                         │ │
│ │ Keyboard Mechanical | 5 | 0 | 5 | [5] (input field)                                │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ DELIVERY SUMMARY ───────────────────────────────────────────────────────────────────┐ │
│ │                                 Total Qty Delivery: 0 items                         │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ ACTION BUTTONS ─────────────────────────────────────────────────────────────────────────┐
│ ┌─────────────┐ ┌─────────────┐                                                         │
│ │ Batal       │ │ Simpan      │                                                         │
│ │ [Gray]      │ │ Delivery    │                                                         │
│ │             │ │ [Blue]      │                                                         │
│ └─────────────┘ └─────────────┘                                                         │
└──────────────────────────────────────────────────────────────────────────────────────────┘
```

## Responsive Breakpoints

### Desktop (12 columns)
```
Header: [Title and Reference - 12 cols]
PO Info Card: [Left Info - 6 cols] [Right Info - 6 cols]
Delivery Info Row 1: [Nomor Delivery - 6 cols] [Tanggal Delivery - 6 cols]
Delivery Info Row 2: [Nomor Resi - 6 cols] [Kurir/Ekspedisi - 6 cols]
Delivery Info Row 3: [Tag - 6 cols] [Keterangan - 6 cols]
Items Table: [Full width - 12 cols with 5 columns]
Actions: [Buttons left-aligned]
```

### Tablet (8 columns)
```
Header: [Title and Reference - 8 cols]
PO Info: [Stacked sections - 8 cols each]
Delivery Info: [All fields - 8 cols, stacked in pairs]
Items Table: [Horizontal scroll - 8 cols]
Actions: [Buttons full width]
```

### Mobile (4 columns)
```
All sections: [Full width - 4 cols]
All fields stacked vertically
PO Info: [Card layout with vertical information]
Items Table: [Card layout or horizontal scroll]
Buttons: [Full width stacked]
```

## Key Layout Features

### **Purchase Delivery Specific Elements:**
- **Title**: "Tambah Delivery" with PO reference clearly displayed
- **PO Integration**: Auto-filled supplier and order information from selected Purchase Order
- **Delivery Tracking**: Comprehensive delivery information with courier and tracking details
- **Partial Delivery Support**: Flexible quantity input for partial deliveries with remaining quantity tracking

### **Form Structure (3 Main Sections):**
1. **Purchase Order Information**: Auto-filled supplier and order details from selected PO
2. **Informasi Delivery**: Delivery-specific information and tracking details
3. **Items Delivery**: Product delivery quantities with PO reference and remaining quantity tracking

### **Auto-filled PO Information:**
- **Supplier Details**: Company name, address, email, and phone auto-populated from PO
- **Order Reference**: Clear display of source Purchase Order number
- **Shipping Information**: Pre-filled shipping method and address from PO
- **Transaction Numbers**: Auto-generated delivery number with PO reference linkage

### **Delivery Information Fields:**
- **Nomor Delivery**: Auto-generated delivery number (required field)
- **Tanggal Delivery**: Actual delivery date selection (required field)
- **Nomor Resi**: Tracking number from courier/shipping company
- **Kurir/Ekspedisi**: Courier or shipping company name
- **Tag**: Optional classification or reference tag
- **Keterangan**: Optional delivery notes and comments

### **Items Delivery Management:**
- **PO Item Display**: Complete list of items from referenced Purchase Order
- **Quantity Tracking**: PO quantity, previously delivered, remaining, and current delivery amounts
- **Flexible Delivery**: Support for partial deliveries with remaining quantity calculation
- **Input Validation**: Prevent delivery quantities exceeding remaining amounts
- **Real-time Calculation**: Automatic total delivery quantity calculation

### **Interactive Elements:**
- **PO Selection**: Choose Purchase Order reference (triggers auto-fill)
- **Quantity Input**: Editable delivery quantities per item with validation
- **Real-time Updates**: Dynamic remaining quantity and total calculation
- **Validation Feedback**: Clear indication of quantity limits and requirements

### **Validation Indicators:**
- **Required fields** marked with asterisks (*)
- **Quantity limits** enforced through remaining quantity display
- **Total tracking** with automatic calculation
- **PO reference** validation and linkage

### **Business Logic Integration:**
- **PO Dependency**: Cannot create delivery without valid Purchase Order reference
- **Quantity Control**: Prevent over-delivery beyond PO quantities
- **Multi-delivery Support**: Handle multiple partial deliveries for same PO
- **Status Tracking**: Track delivery completion status per PO

### **Delivery Process Flow:**
- **Step 1**: Select Purchase Order reference (triggers auto-fill)
- **Step 2**: Enter delivery details (date, tracking, courier)
- **Step 3**: Specify delivery quantities per item
- **Step 4**: Validate and save delivery record

### **Financial and Inventory Integration:**
- **Inventory Updates**: Delivered items update inventory levels
- **Cost Tracking**: Link delivery costs to purchase order costs
- **Supplier Integration**: Maintain supplier relationship and delivery history
- **Order Completion**: Track PO fulfillment through delivery records

### **Tracking and Documentation:**
- **Courier Integration**: Support for various shipping companies and methods
- **Tracking Numbers**: Complete tracking information for delivery monitoring
- **Delivery History**: Maintain complete delivery audit trail per Purchase Order
- **Status Updates**: Real-time delivery status for Purchase Order management

This purchase delivery form provides comprehensive delivery management with Purchase Order integration, flexible partial delivery support, and complete tracking capabilities while maintaining proper validation and audit trails.