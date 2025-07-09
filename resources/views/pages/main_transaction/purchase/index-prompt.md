# Purchase Module Index View Requirements

## Overview
This document defines the structure and requirements for the Purchase module index view, which can be used as a template for other transaction modules (e.g., Sales).

## Page Structure

### 1. Summary Information Cards
Location: Top of the page (following `@resources/views/pages/master_data/contact/index.blade.php#L6-49`)

**Cards Required:**
- **Card 1**: Faktur Belum Dibayar
  - Icon: `tabler-file-invoice`
  - Color: Primary theme
  - Sample Amount: Rp 150,000,000
  
- **Card 2**: Faktur Telat Dibayar
  - Icon: `tabler-clock-exclamation`
  - Color: Warning theme
  - Sample Amount: Rp 35,000,000
  
- **Card 3**: Pelunasan 30 Hari Terakhir
  - Icon: `tabler-check-circle`
  - Color: Success theme
  - Sample Amount: Rp 85,000,000

### 2. Navigation Tabs
Location: Below the summary cards, outside any card container

**Tab Structure:**
- Tabs should be standalone (`<ul class="nav nav-tabs">`)
- Each tab should have appropriate icons and labels
- Tab order: Pesanan → Pengiriman → Faktur

### 3. Tab Content Structure
Each tab should contain:
- Individual card wrapper
- Filter controls in card body
- Action buttons (Import, Export, Tambah Data)
- DataTable in card-datatable section

---

## Tab Specifications

### Tab 1: Pesanan (Orders)
**Purpose:** Display all purchase orders created by users with filtering capabilities

**Filter Controls:**
- **Status Filter**: Dropdown with options:
  - "Semua Status" (default)
  - "Belum Ditagih"
  - "Dikirim Sebagian"
  - "Selesai"
- **Search Bar**: Text input with placeholder "Cari Transaksi"

**Action Buttons:**
- Import (`tabler-file-upload`)
- Export (`tabler-file-download`)
- Tambah Data (`tabler-plus`)

**Table Columns:**
1. **Tanggal**: Date in YYYY-MM-DD format
2. **No**: Clickable Purchase Order number (links to detail view)
3. **Supplier**: Clickable supplier name (links to supplier detail)
4. **Tgl Jatuh Tempo**: Due date in YYYY-MM-DD format
5. **Status**: Badge-styled status with color coding:
   - "Belum Dibayar" (secondary)
   - "Menunggu Pembayaran" (warning)
   - "Telat Bayar" (danger)
   - "Dibayar" (success)
   - "Dibayar Sebagian" (info)
6. **Jumlah DP**: Right-aligned currency format (Rp XX,XXX,XXX)
7. **Total**: Right-aligned currency format (Rp XX,XXX,XXX)

**Data Structure:**
```php
[
    "id" => 1,
    "date" => "2025-07-01",
    "reference_no" => "PO-2025-001",
    "supplier" => "PT. Sumber Makmur",
    "due_date" => "2025-07-15",
    "status" => "Belum Dibayar",
    "down_payment" => 3000000,
    "total" => 15000000,
]
```

---

### Tab 2: Pengiriman (Delivery)
**Purpose:** Display all delivery data from suppliers as part of the purchase process for monitoring and billing follow-up

**Filter Controls:**
- **Status Filter**: Dropdown with options:
  - "Semua Status" (default)
  - "Belum Ditagih"
  - "Selesai"
- **Search Bar**: Text input with placeholder "Cari Transaksi"

**Action Buttons:**
- Import (`tabler-file-upload`)
- Export (`tabler-file-download`)
- Tambah Data (`tabler-plus`)

**Table Columns:**
1. **Tanggal**: Date in YYYY-MM-DD format
2. **No**: Clickable Purchase Delivery number (links to detail view)
3. **Supplier**: Clickable supplier name (links to supplier detail)
4. **Status**: Badge-styled status with color coding:
   - "Belum Ditagih" (warning)
   - "Selesai" (success)

**Data Structure:**
```php
[
    "id" => 1,
    "date" => "2025-07-02",
    "reference_no" => "PD-2025-001",
    "supplier" => "PT. Sumber Makmur",
    "status" => "Belum Ditagih",
]
```

---

### Tab 3: Faktur (Invoice)
**Purpose:** Display all purchase invoices from suppliers reflecting payment status of purchase transactions

**Filter Controls:**
- **Status Filter**: Dropdown with options:
  - "Semua Status" (default)
  - "Menunggu Pembayaran"
  - "Telat Bayar"
  - "Dibayar"
  - "Dibayar Sebagian"
  - "Belum Dibayar"
- **Search Bar**: Text input with placeholder "Cari Transaksi"

**Action Buttons:**
- Import (`tabler-file-upload`)
- Export (`tabler-file-download`)
- Tambah Data (`tabler-plus`)

**Table Columns:**
1. **Tanggal**: Date in YYYY-MM-DD format
2. **No**: Clickable Purchase Invoice number (links to detail view)
3. **Supplier**: Clickable supplier name (links to supplier detail)
4. **Tgl Jatuh Tempo**: Due date in YYYY-MM-DD format
5. **Status**: Badge-styled status with color coding:
   - "Belum Dibayar" (secondary)
   - "Menunggu Pembayaran" (warning)
   - "Telat Bayar" (danger)
   - "Dibayar" (success)
   - "Dibayar Sebagian" (info)
6. **Sisa Tagihan**: Right-aligned currency format (Rp XX,XXX,XXX)
7. **Total**: Right-aligned currency format (Rp XX,XXX,XXX)

**Data Structure:**
```php
[
    "id" => 1,
    "date" => "2025-07-01",
    "reference_no" => "PI-2025-001",
    "supplier" => "PT. Sumber Makmur",
    "due_date" => "2025-07-15",
    "status" => "Belum Dibayar",
    "remaining_bill" => 15000000,
    "total" => 15000000,
]
```

---

## Technical Implementation

### Controller Structure
```php
public function index_data_table(Request $request)
{
    $tab = $request->get('tab', 'pesanan');
    
    switch ($tab) {
        case 'pesanan':
            return $this->getPesananData();
        case 'pengiriman':
            return $this->getPengirimanData();
        case 'faktur':
            return $this->getFakturData();
        default:
            return $this->getPesananData();
    }
}
```

### Frontend Features
- **Tab Switching**: Initialize appropriate DataTable on tab change
- **Currency Formatting**: Indonesian format with thousand separators
- **Status Badges**: Color-coded based on status values
- **Clickable Links**: Reference numbers and supplier names
- **Responsive Design**: Bootstrap grid system
- **Action Buttons**: Unique IDs per tab for event handling

### JavaScript Event Handlers
- Tab switching with DataTable re-initialization
- Import/Export button handlers per tab
- Add button handlers (all redirect to same add route)
- Search and filter functionality

---

## Adaptation Guidelines for Other Modules

When creating similar modules (e.g., Sales), adapt the following:

1. **Entity Names**: Replace "Purchase" with target entity (e.g., "Sales")
2. **Reference Prefixes**: Update reference number prefixes (PO → SO, PD → SD, PI → SI)
3. **Stakeholder**: Replace "Supplier" with appropriate stakeholder (e.g., "Customer")
4. **Status Values**: Adjust status options based on business flow
5. **Currency Fields**: Modify based on transaction type (e.g., "Jumlah DP" vs "Deposit")
6. **Icons**: Update tab icons to match module context
7. **Summary Cards**: Adjust card titles and calculations for the module
8. **Controller Logic**: Implement appropriate data retrieval methods

This structure ensures consistency across transaction modules while allowing for necessary customizations.