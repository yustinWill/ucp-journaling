# Sales Order (SO) Detail Form - Visual Layout

## ASCII Layout Representation

### History Delivery Tab View

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ Header                                                                                                      │
│ Menu                                                                                                        │
│                                                                                                             │
│    📄 Detail Sales Order                                                                                   │
│    SO-20250B-673                                                                  Confirmed               │
│                                                                                                             │
│    Informasi Dasar                                                                                         │
│    ─────────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    Tanggal Transaksi:                                           27 Agustus 2025                           │
│    Nomor Transaksi:                                              SO-20250B-673                            │
│    Status:                                                       Confirmed                                 │
│    Dibuat Oleh:                                                  Admin User                                │
│                                                                                                             │
│    👤 Informasi Customer                                                                                   │
│                                                                                                             │
│    Customer:                                                     PT. Customer Example                     │
│    Email:                                                        purchasing@customer.com                  │
│    Alamat:                                                       Jl. Sudirman No. 456, Jakarta           │
│    No. Referensi:                                                PO-CUS-001                               │
│                                                                                                             │
│    🛒 Detail Penjualan                                                                                     │
│                                                                                                             │
│    ┌─────────────────────────────────────────────────────────────────────────────────────────────────── │
│    │ Produk          │ Kuantitas │ Satuan   │ Harga Satuan │ Pajak │ Jumlah                             │ │
│    ├─────────────────┼───────────┼──────────┼──────────────┼───────┼─────────────────────────────────── │
│    │ Laptop Dell     │     5     │   Unit   │ Rp 8,500,000 │ 11%   │ Rp 42,500,000                   │ │
│    │ Inspiron 15     │           │          │              │       │                                   │ │
│    ├─────────────────┼───────────┼──────────┼──────────────┼───────┼─────────────────────────────────── │
│    │ Mouse Wireless  │    10     │   Unit   │ Rp 250,000   │ 11%   │ Rp 2,500,000                    │ │
│    │ Logitech        │           │          │              │       │                                   │ │
│    ├─────────────────┼───────────┼──────────┼──────────────┼───────┼─────────────────────────────────── │
│    │ Keyboard        │     5     │   Unit   │ Rp 750,000   │ 11%   │ Rp 3,750,000                    │ │
│    │ Mechanical      │           │          │              │       │                                   │ │
│    └─────────────────┴───────────┴──────────┴──────────────┴───────┴─────────────────────────────────── │
│                                                                                                             │
│    Total Qty:                                                       20 Items                              │
│    Subtotal:                                                        Rp 48,750,000                        │
│    Total Pajak:                                                     Rp 5,362,500                         │
│                                                                                                             │
│    Total Akhir:                                                     Rp 54,112,500                        │
│                                                                                                             │
│    📋 History Delivery    📋 History Invoice                                                               │
│    ────────────────────  ────────────────────                                                             │
│                                                                                                             │
│    Riwayat Pengiriman                                                                                     │
│                                                                    ┌─────────────────┐                   │
│                                                                    │ + Tambah Delivery│                   │
│                                                                    └─────────────────┘                   │
│                                                                                                             │
│    ┌─────────────────────────────────────────────────────────────────────────────────────────────────── │
│    │ Tanggal         │ Nomor Transaksi │ Qty │ Status      │ Aksi                                      │ │
│    ├─────────────────┼─────────────────┼─────┼─────────────┼─────────────────────────────────────────── │
│    │ 25 Agus 2025    │ DN-001          │  3  │ Partial     │ 🔍 Detail                                 │ │
│    ├─────────────────┼─────────────────┼─────┼─────────────┼─────────────────────────────────────────── │
│    │ 26 Agus 2025    │ DN-002          │ 10  │ Delivered   │ 🔍 Detail                                 │ │
│    └─────────────────┴─────────────────┴─────┴─────────────┴─────────────────────────────────────────── │
│                                                                                                             │
└─────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

### History Invoice Tab View

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ Header                                                                                                      │
│ Menu                                                                                                        │
│                                                                                                             │
│    📄 Detail Sales Order                                                                                   │
│    SO-20250B-673                                                                  Confirmed               │
│                                                                                                             │
│    Informasi Dasar                                                                                         │
│    ─────────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    Tanggal Transaksi:                                           27 Agustus 2025                           │
│    Nomor Transaksi:                                              SO-20250B-673                            │
│    Status:                                                       Confirmed                                 │
│    Dibuat Oleh:                                                  Admin User                                │
│                                                                                                             │
│    👤 Informasi Customer                                                                                   │
│                                                                                                             │
│    Customer:                                                     PT. Customer Example                     │
│    Email:                                                        purchasing@customer.com                  │
│    Alamat:                                                       Jl. Sudirman No. 456, Jakarta           │
│    No. Referensi:                                                PO-CUS-001                               │
│                                                                                                             │
│    🛒 Detail Penjualan                                                                                     │
│                                                                                                             │
│    ┌─────────────────────────────────────────────────────────────────────────────────────────────────── │
│    │ Produk          │ Kuantitas │ Satuan   │ Harga Satuan │ Pajak │ Jumlah                             │ │
│    ├─────────────────┼───────────┼──────────┼──────────────┼───────┼─────────────────────────────────── │
│    │ Laptop Dell     │     5     │   Unit   │ Rp 8,500,000 │ 11%   │ Rp 42,500,000                   │ │
│    │ Inspiron 15     │           │          │              │       │                                   │ │
│    ├─────────────────┼───────────┼──────────┼──────────────┼───────┼─────────────────────────────────── │
│    │ Mouse Wireless  │    10     │   Unit   │ Rp 250,000   │ 11%   │ Rp 2,500,000                    │ │
│    │ Logitech        │           │          │              │       │                                   │ │
│    ├─────────────────┼───────────┼──────────┼──────────────┼───────┼─────────────────────────────────── │
│    │ Keyboard        │     5     │   Unit   │ Rp 750,000   │ 11%   │ Rp 3,750,000                    │ │
│    │ Mechanical      │           │          │              │       │                                   │ │
│    └─────────────────┴───────────┴──────────┴──────────────┴───────┴─────────────────────────────────── │
│                                                                                                             │
│    Total Qty:                                                       20 Items                              │
│    Subtotal:                                                        Rp 48,750,000                        │
│    Total Pajak:                                                     Rp 5,362,500                         │
│                                                                                                             │
│    Total Akhir:                                                     Rp 54,112,500                        │
│                                                                                                             │
│    📋 History Delivery    📋 History Invoice                                                               │
│    ────────────────────  ────────────────────                                                             │
│                                                                                                             │
│    Riwayat Invoice                                                                                         │
│                                                                    ┌─────────────────┐                   │
│                                                                    │ + Buat Invoice  │                   │
│                                                                    └─────────────────┘                   │
│                                                                                                             │
│    ┌─────────────────────────────────────────────────────────────────────────────────────────────────── │
│    │ Tanggal         │ Nomor Invoice   │ Jumlah        │ Status      │ Aksi                             │ │
│    ├─────────────────┼─────────────────┼───────────────┼─────────────┼─────────────────────────────────── │
│    │ 27 Agus 2025    │ INV-001         │ Rp 25,000,000 │ Paid        │ 🔍 Detail                        │ │
│    ├─────────────────┼─────────────────┼───────────────┼─────────────┼─────────────────────────────────── │
│    │ 28 Agus 2025    │ INV-002         │ Rp 29,112,500 │ Pending     │ 🔍 Detail                        │ │
│    └─────────────────┴─────────────────┴───────────────┴─────────────┴─────────────────────────────────── │
│                                                                                                             │
└─────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

## Grid-Based Layout

```
┌─ HEADER ─────────────────────────────────────────────────────────────────────────────────┐
│ 📄 Detail Sales Order                                                                  │
│ SO-20250B-673                                                     Confirmed           │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ SECTION 1: INFORMASI DASAR ────────────────────────────────────────────────────────────┐
│ Informasi Dasar                                                                        │
│ ─────────────────────────────────────────────────────────────────────────────────────────│
│                                                                                          │
│ ┌─ BASIC INFO GRID ────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 6 ─────────────────────┐    ┌─ COL 6 ─────────────────────┐                  │ │
│ │ │ Tanggal Transaksi:          │    │ Nomor Transaksi:            │                  │ │
│ │ │ [Display] "27 Agustus 2025" │    │ [Display] "SO-20250B-673"   │                  │ │
│ │ └─────────────────────────────┘    └─────────────────────────────┘                  │ │
│ │                                                                                      │ │
│ │ ┌─ COL 6 ─────────────────────┐    ┌─ COL 6 ─────────────────────┐                  │ │
│ │ │ Status:                     │    │ Dibuat Oleh:                │                  │ │
│ │ │ [Display] "Confirmed"       │    │ [Display] "Admin User"      │                  │ │
│ │ └─────────────────────────────┘    └─────────────────────────────┘                  │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ SECTION 2: INFORMASI CUSTOMER ─────────────────────────────────────────────────────────┐
│ 👤 Informasi Customer                                                                  │
│                                                                                          │
│ ┌─ CUSTOMER INFO GRID ─────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 6 ─────────────────────┐    ┌─ COL 6 ─────────────────────┐                  │ │
│ │ │ Customer:                   │    │ Email:                      │                  │ │
│ │ │ [Display] "PT. Customer     │    │ [Display] "purchasing@      │                  │ │
│ │ │ Example"                    │    │ customer.com"               │                  │ │
│ │ └─────────────────────────────┘    └─────────────────────────────┘                  │ │
│ │                                                                                      │ │
│ │ ┌─ COL 6 ─────────────────────┐    ┌─ COL 6 ─────────────────────┐                  │ │
│ │ │ Alamat:                     │    │ No. Referensi:              │                  │ │
│ │ │ [Display] "Jl. Sudirman     │    │ [Display] "PO-CUS-001"      │                  │ │
│ │ │ No. 456, Jakarta"           │    │                             │                  │ │
│ │ └─────────────────────────────┘    └─────────────────────────────┘                  │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ SECTION 3: DETAIL PENJUALAN ───────────────────────────────────────────────────────────┐
│ 🛒 Detail Penjualan                                                                    │
│                                                                                          │
│ ┌─ SALES DETAILS TABLE ────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 3 ──────┐ ┌─ COL 1.5 ─┐ ┌─ COL 1.5 ─┐ ┌─ COL 2 ───┐ ┌─ COL 1 ─┐ ┌─ COL 3 ──┐ │ │
│ │ │ Produk       │ │ Kuantitas │ │ Satuan    │ │ Harga      │ │ Pajak   │ │ Jumlah   │ │ │
│ │ │ [Display]    │ │ [Display] │ │ [Display] │ │ Satuan     │ │ [Disp.] │ │ [Display]│ │ │
│ │ │              │ │           │ │           │ │ [Display]  │ │         │ │          │ │ │
│ │ └──────────────┘ └───────────┘ └───────────┘ └────────────┘ └─────────┘ └──────────┘ │ │
│ │                                                                                      │ │
│ │ [Sample Data Rows:]                                                                  │ │
│ │ Laptop Dell Inspiron 15 | 5 | Unit | Rp 8,500,000 | 11% | Rp 42,500,000           │ │
│ │ Mouse Wireless Logitech | 10 | Unit | Rp 250,000 | 11% | Rp 2,500,000              │ │
│ │ Keyboard Mechanical | 5 | Unit | Rp 750,000 | 11% | Rp 3,750,000                   │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ FINANCIAL SUMMARY ──────────────────────────────────────────────────────────────────┐ │
│ │ Total Qty: 20 Items                                                                 │ │
│ │ Subtotal: Rp 48,750,000                                                             │ │
│ │ Total Pajak: Rp 5,362,500                                                           │ │
│ │ Total Akhir: Rp 54,112,500                                                          │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ SECTION 4: TAB NAVIGATION ─────────────────────────────────────────────────────────────┐
│ ┌─ TAB 1 ──────────────────────┐ ┌─ TAB 2 ──────────────────────┐                     │
│ │ 📋 History Delivery          │ │ 📋 History Invoice           │                     │
│ │ [Active/Inactive]            │ │ [Active/Inactive]            │                     │
│ └──────────────────────────────┘ └──────────────────────────────┘                     │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ SECTION 5A: HISTORY DELIVERY TAB CONTENT ──────────────────────────────────────────────┐
│ Riwayat Pengiriman                                                                     │
│                                                          ┌─────────────────┐           │
│                                                          │ + Tambah        │           │
│                                                          │   Delivery      │           │
│                                                          └─────────────────┘           │
│                                                                                          │
│ ┌─ DELIVERY HISTORY TABLE ─────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 3 ──────┐ ┌─ COL 3 ──────┐ ┌─ COL 2 ──┐ ┌─ COL 2 ──┐ ┌─ COL 2 ──────────────┐ │ │
│ │ │ Tanggal      │ │ Nomor        │ │ Qty      │ │ Status   │ │ Aksi             │ │ │
│ │ │ [Display]    │ │ Transaksi    │ │ [Disp.]  │ │ [Disp.]  │ │ [Action Button]  │ │ │
│ │ │              │ │ [Display]    │ │          │ │          │ │                  │ │ │
│ │ └──────────────┘ └──────────────┘ └──────────┘ └──────────┘ └──────────────────┘ │ │
│ │                                                                                      │ │
│ │ [Sample Data Rows:]                                                                  │ │
│ │ 25 Agus 2025 | DN-001 | 3 | Partial | 🔍 Detail                                    │ │
│ │ 26 Agus 2025 | DN-002 | 10 | Delivered | 🔍 Detail                                 │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ SECTION 5B: HISTORY INVOICE TAB CONTENT ───────────────────────────────────────────────┐
│ Riwayat Invoice                                                                        │
│                                                          ┌─────────────────┐           │
│                                                          │ + Buat Invoice  │           │
│                                                          └─────────────────┘           │
│                                                                                          │
│ ┌─ INVOICE HISTORY TABLE ──────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 3 ──────┐ ┌─ COL 3 ──────┐ ┌─ COL 2.5 ─┐ ┌─ COL 1.5 ─┐ ┌─ COL 2 ──────────┐ │ │
│ │ │ Tanggal      │ │ Nomor        │ │ Jumlah    │ │ Status     │ │ Aksi           │ │ │
│ │ │ [Display]    │ │ Invoice      │ │ [Display] │ │ [Display]  │ │ [Action Button]│ │ │
│ │ │              │ │ [Display]    │ │           │ │            │ │                │ │ │
│ │ └──────────────┘ └──────────────┘ └───────────┘ └────────────┘ └────────────────┘ │ │
│ │                                                                                      │ │
│ │ [Sample Data Rows:]                                                                  │ │
│ │ 27 Agus 2025 | INV-001 | Rp 25,000,000 | Paid | 🔍 Detail                         │ │
│ │ 28 Agus 2025 | INV-002 | Rp 29,112,500 | Pending | 🔍 Detail                      │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘
```

## Responsive Breakpoints

### Desktop (12 columns)
```
Header: [SO Title and Status - 12 cols]
Basic Info: [Transaction details in 2x2 grid - 6 cols each]
Customer Info: [Customer details in 2x2 grid - 6 cols each]
Sales Details: [Full width table - 12 cols]
Financial Summary: [Full width - 12 cols]
Tab Navigation: [Two tabs - 6 cols each]
History Tables: [Full width - 12 cols with appropriate column distribution]
Action Buttons: [Right-aligned in tab content]
```

### Tablet (8 columns)
```
Header: [SO Title and Status - 8 cols]
Basic Info: [Stacked pairs - 8 cols]
Customer Info: [Stacked pairs - 8 cols]
Sales Details: [Horizontal scroll table - 8 cols]
Financial Summary: [Full width - 8 cols]
Tab Navigation: [Full width tabs - 8 cols]
History Tables: [Horizontal scroll - 8 cols]
Action Buttons: [Full width - 8 cols]
```

### Mobile (4 columns)
```
All sections: [Full width - 4 cols]
All information stacked vertically
Tables: [Card layout or horizontal scroll]
Tabs: [Stacked navigation]
Action Buttons: [Full width]
```

## Key Layout Features

### **Sales Order Detail Specific Elements:**
- **Document Header**: Clear SO identification with status badge
- **Tab Navigation**: Switch between History Delivery and History Invoice
- **Historical Data Display**: Complete transaction history with status tracking
- **Action Integration**: Quick access to create new deliveries and invoices

### **Page Structure (5 Main Sections):**
1. **Document Header**: SO number, status, and confirmation badge
2. **Informasi Dasar**: Basic transaction information (date, number, status, creator)
3. **Informasi Customer**: Complete customer details and reference information
4. **Detail Penjualan**: Product breakdown with financial summary
5. **Tab Content**: History tables for deliveries and invoices with creation buttons

### **Information Display Features:**
- **Status Indicators**: Clear status badges (Confirmed, Paid, Pending, Partial, Delivered)
- **Financial Summary**: Complete breakdown of quantities, subtotal, tax, and total
- **Historical Tracking**: Complete audit trail of related delivery and invoice transactions
- **Quick Actions**: Direct access to create new deliveries and invoices from SO

### **Tab-based Navigation:**
- **History Delivery Tab**: Shows all delivery transactions related to the SO
- **History Invoice Tab**: Shows all invoice transactions related to the SO
- **Dynamic Content**: Tab content changes based on selection
- **Action Buttons**: Context-specific action buttons per tab

### **Delivery History Features:**
- **Delivery Tracking**: Complete list of delivery transactions with dates and numbers
- **Quantity Tracking**: Show delivered quantities per delivery
- **Status Monitoring**: Track delivery status (Partial, Delivered)
- **Quick Access**: Direct links to delivery detail pages
- **Create New**: Button to create new delivery from SO

### **Invoice History Features:**
- **Invoice Tracking**: Complete list of invoice transactions with amounts
- **Payment Status**: Track payment status (Paid, Pending)
- **Amount Tracking**: Show invoiced amounts per invoice
- **Financial Monitoring**: Track total invoiced vs SO amount
- **Create New**: Button to create new invoice from SO

### **Business Logic Integration:**
- **Document Relationships**: Clear linkage between SO, deliveries, and invoices
- **Status Management**: Proper status tracking across related documents
- **Partial Processing**: Support for multiple partial deliveries and invoices
- **Workflow Control**: Manage document creation workflow from SO detail

This sales order detail page provides comprehensive SO management with complete transaction history, status tracking, and quick access to create related delivery and invoice documents while maintaining proper audit trails and business rule compliance.

## JavaScript Functionality Requirements

### 1. Tab Navigation Management
- **Tab Switching**: Dynamic content loading for History Delivery and History Invoice tabs
- **State Management**: Maintain tab state during page navigation
- **Content Loading**: Load tab-specific data when tab is activated
- **Active State**: Visual indication of currently active tab

### 2. Document Creation Integration
- **Delivery Creation**: Navigate to delivery creation form with SO pre-populated
- **Invoice Creation**: Navigate to invoice creation form with SO pre-populated
- **Permission Checking**: Verify user permissions for document creation
- **Status Validation**: Ensure SO status allows for new document creation

### 3. History Data Management
- **Real-time Updates**: Refresh history data when new documents created
- **Status Tracking**: Display current status of related deliveries and invoices
- **Quantity Tracking**: Show delivered/invoiced quantities vs SO quantities
- **Progress Calculation**: Calculate completion percentages for SO fulfillment

### 4. Detail Navigation
- **Document Links**: Navigate to delivery and invoice detail pages
- **Status Updates**: Real-time status updates for related documents
- **Audit Trail**: Complete document relationship tracking
- **Quick Actions**: Context-sensitive actions based on document status

## Data Context
- **Business Flow**: Sales Order → **Sales Order Detail** → Delivery/Invoice Creation → Transaction Completion
- **Purpose**: Central hub for sales order management and related document oversight
- **Integration**: Links to Sales Delivery, Sales Invoice, and Customer modules
- **Document Relationships**: Foundation for managing complete sales transaction workflow

## Development Notes
- **Read-only Display**: All SO information displayed as read-only historical data
- **Tab Performance**: Efficient tab switching with lazy loading of history data
- **Real-time Updates**: Live updates of related document status and progress
- **Responsive Design**: Proper mobile handling of tabbed interface and data tables
- **Permission Integration**: Role-based access control for document creation actions

## Future Considerations
This sales order detail form will enable:
- **Advanced Analytics**: SO performance metrics and fulfillment tracking
- **Automated Workflows**: Automated delivery and invoice creation based on rules
- **Customer Portal**: Customer self-service access to SO status and history
- **Mobile Optimization**: Mobile-friendly SO detail viewing and management
- **Integration Enhancement**: Advanced CRM and ERP system integration
- **Notification System**: Real-time notifications for SO status changes

## Technical Implementation
- **Framework**: Laravel Blade templates with tabbed interface and AJAX content loading
- **JavaScript**: jQuery with tab management, real-time updates, and navigation control
- **Styling**: Bootstrap + TailwindCSS with responsive tab design and status indicators
- **Database**: Integration with sales orders, deliveries, invoices, and customer tables
- **Real-time Data**: Live status updates and document relationship tracking
- **Security**: Proper access control for SO viewing and related document creation
- **Performance**: Optimized tab content loading and efficient data retrieval

## Special Features & Business Logic

### Integration Notes (Based on Requirements and Right Panel Information)
**Sales Order Detail Context**:

**Business Requirements from Right Panel**:

#### **3. Relasi Dokumen**:
**a. Form Sales Order (SO)**:
- **Data Utama**: "Customer, tanggal transaksi, item produk, qty, harga satuan, pajak, total"
- **Tab/History**: "menampilkan daftar SD & SI yang terkait dengan SO"

#### **4. Additional Rules**:
- **Semua data autofill dari SO**: "Customer, alamat, item, qty, harga, pajak"
- **Qty / nilai yang sudah pernah digunakan pada SD atau SI akan mengurangi sisa di SO**
- **Status dokumen pada SO diperbarui secara otomatis**:
  - **Draft** → "SO baru dibuat"
  - **Partial** → "sudah ada SD/SI sebagian"
  - **Delivered / Invoiced** → "seluruh qty / nilai sudah terpenuhi"

#### **Field Specifications**:

**A. Informasi Dasar Transaksi**:
- **Tanggal Transaksi**: "Tanggal pembuatan SO"
- **Nomor Transaksi**: "Nomor unik SO (auto generate oleh sistem)"
- **Status**: "Status SO (Draft, Partial, Delivered, Invoiced, Closed)"
- **Dibuat Oleh**: "User yang membuat SO"

**B. Informasi Customer**:
- **Customer**: "Nama Customer, diSilih dari master Customer"
- **Email**: "Email Customer (autofill dari master)"
- **Alamat**: "Alamat Customer (autofill dari master)"
- **Nomor Referensi**: "Nomor referensi dari Customer (opsional, bisa untuk cross-check)"

**C. Detail Pembelian**:
- **Syarat Pembayaran**: "Termin pembayaran (misal: COD, 30 Hari, 60 Hari)"
- **Gudang**: "Lokasi gudang penerima barang"
- **Keterangan**: "Catatan tambahan terkait pembelian"

**D. Item Barang (Detail Line)**:
- **Produk**: "Nama produk/barang yang dibeli"
- **Kuantitas**: "Jumlah barang yang dipesan"
- **Satuan**: "Unit barang (pcs, unit, box, dll)"
- **Harga Satuan**: "Harga per unit barang"
- **Pajak**: "Pajak yang berlaku (misal PPN 11%)"
- **Jumlah**: "Total harga per baris (Qty × Harga Satuan + Pajak jika ada)"

### Enhanced Sales Order Detail Management

#### **Document Relationship Tracking**:
- **SO as Foundation**: Sales Order serves as the master document for all related transactions
- **History Integration**: Complete tracking of all delivery and invoice documents created from SO
- **Status Synchronization**: Automatic SO status updates based on related document progress
- **Quantity Management**: Real-time tracking of delivered and invoiced quantities against SO

#### **Advanced Status Management**:
- **Dynamic Status Updates**: Automatic status calculation based on completion progress
- **Multi-document Tracking**: Handle multiple partial deliveries and invoices simultaneously
- **Completion Monitoring**: Track both quantity (delivery) and value (invoice) completion
- **Business Rule Enforcement**: Ensure proper document flow and status transitions

#### **Workflow Control and Navigation**:
- **Document Creation Control**: Enable/disable creation buttons based on SO status and completion
- **Quick Navigation**: Direct access to create deliveries and invoices with SO data pre-populated
- **History Management**: Complete audit trail of all related document activities
- **Progress Visualization**: Clear indication of SO fulfillment progress

This comprehensive sales order detail system provides complete sales transaction oversight with proper document relationship management, status tracking, and workflow control while maintaining audit trails and supporting the complete sales process from order to fulfillment.