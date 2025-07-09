# Sales Module Index View Requirements

## Overview
This document defines the structure and requirements for the Sales module index view, adapted from the Purchase module template for sales operations and customer management.

## Page Structure

### 1. Summary Information Cards
Location: Top of the page (following `@resources/views/pages/master_data/contact/index.blade.php#L6-49`)

**Cards Required:**
- **Card 1**: Piutang Belum Dibayar
  - Icon: `tabler-receipt`
  - Color: Primary theme
  - Sample Amount: Rp 240,000,000
  
- **Card 2**: Piutang Telat Dibayar
  - Icon: `tabler-clock-exclamation`
  - Color: Warning theme
  - Sample Amount: Rp 45,000,000
  
- **Card 3**: Penerimaan 30 Hari Terakhir
  - Icon: `tabler-cash`
  - Color: Success theme
  - Sample Amount: Rp 125,000,000

### 2. Navigation Tabs
Location: Below the summary cards, outside any card container

**Tab Structure:**
- Tabs should be standalone (`<ul class="nav nav-tabs">`)
- Each tab should have appropriate icons and labels
- Tab order: Pesanan → Pengiriman → Faktur

### 3. Tab Content Structure
Each tab should contain:
- Individual card wrapper with proper spacing (`<div class="row"><div class="tab-content p-3">`)
- Filter controls in card body
- Action buttons (Import, Export, Tambah Data)
- DataTable in card-datatable section

---

## Tab Specifications

### Tab 1: Pesanan (Sales Orders)
**Purpose:** Display all sales orders created by users with filtering capabilities for customer relationship management

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
2. **No**: Clickable Sales Order number (links to detail view)
3. **Customer**: Clickable customer name (links to customer detail)
4. **Tgl Jatuh Tempo**: Due date in YYYY-MM-DD format
5. **Status**: Badge-styled status with color coding:
   - "Belum Dibayar" (secondary)
   - "Menunggu Pembayaran" (warning)
   - "Telat Bayar" (danger)
   - "Dibayar" (success)
   - "Dibayar Sebagian" (info)
6. **Uang Muka**: Right-aligned currency format (Rp XX,XXX,XXX)
7. **Total**: Right-aligned currency format (Rp XX,XXX,XXX)

**Data Structure:**
```php
[
    "id" => 1,
    "date" => "2025-07-01",
    "reference_no" => "SO-2025-001",
    "customer" => "PT. Cahaya Abadi",
    "due_date" => "2025-07-15",
    "status" => "Belum Dibayar",
    "down_payment" => 5000000,
    "total" => 25000000,
]
```

---

### Tab 2: Pengiriman (Sales Delivery)
**Purpose:** Display all delivery data to customers as part of the sales process for monitoring and billing follow-up

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
2. **No**: Clickable Sales Delivery number (links to detail view)
3. **Customer**: Clickable customer name (links to customer detail)
4. **Status**: Badge-styled status with color coding:
   - "Belum Ditagih" (warning)
   - "Selesai" (success)

**Data Structure:**
```php
[
    "id" => 1,
    "date" => "2025-07-02",
    "reference_no" => "SD-2025-001",
    "customer" => "PT. Cahaya Abadi",
    "status" => "Belum Ditagih",
]
```

---

### Tab 3: Faktur (Sales Invoice)
**Purpose:** Display all sales invoices to customers reflecting payment status of sales transactions

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
2. **No**: Clickable Sales Invoice number (links to detail view)
3. **Customer**: Clickable customer name (links to customer detail)
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
    "reference_no" => "SI-2025-001",
    "customer" => "PT. Cahaya Abadi",
    "due_date" => "2025-07-15",
    "status" => "Belum Dibayar",
    "remaining_bill" => 25000000,
    "total" => 25000000,
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

### Route Configuration
```php
Route::controller(SalesController::class)->group(function () use ($base_segment, $sales_segment) {
    Route::get($base_segment . $sales_segment . '/index', 'index')->name('main_transaction.sales.index');
    Route::get($base_segment . $sales_segment . '/index_data_table', 'index_data_table')->name('main_transaction.sales.index_data_table');
    Route::get($base_segment . $sales_segment . '/add', 'add')->name('main_transaction.sales.add');
});
```

### Frontend Features
- **Tab Switching**: Initialize appropriate DataTable on tab change
- **Currency Formatting**: Indonesian format with thousand separators
- **Status Badges**: Color-coded based on status values
- **Clickable Links**: Reference numbers and customer names
- **Responsive Design**: Bootstrap grid system with proper spacing
- **Action Buttons**: Unique IDs per tab for event handling

### JavaScript Event Handlers
- Tab switching with DataTable re-initialization
- Import/Export button handlers per tab
- Add button handlers (all redirect to same add route)
- Search and filter functionality

---

## Sales-Specific Adaptations

### Business Context
- **Customer-Focused**: All operations center around customer relationships
- **Revenue Tracking**: Focus on sales amounts and receivables
- **Payment Management**: Track customer payments and outstanding balances

### Reference Number System
- **SO-YYYY-XXX**: Sales Orders
- **SD-YYYY-XXX**: Sales Delivery
- **SI-YYYY-XXX**: Sales Invoice

### Terminology Adaptations
- **"Supplier"** → **"Customer"**
- **"Jumlah DP"** → **"Uang Muka"**
- **"Hutang"** → **"Piutang"**
- **"Pelunasan"** → **"Penerimaan"**

### Status Workflow
Sales process typically follows:
1. **Draft** → **Pending** → **Approved** → **Delivered** → **Invoiced** → **Paid**
2. Special handling for partial payments and overdue accounts

---

## Implementation Notes

### Sample Customer Data
```php
$customers = [
    "PT. Cahaya Abadi",
    "CV. Maju Jaya", 
    "PT. Sukses Mandiri",
    "CV. Berkah Sentosa",
    "PT. Nusantara Prima"
];
```

### Menu Integration
```php
// In resources/views/layouts/menu.blade.php
<li class="menu-item @if (request()->routeIs(['main_transaction.sales.*'])) active @endif">
    <a href="{{ route('main_transaction.sales.index') }}" class="menu-link">
        <div data-i18n="CRM">Penjualan</div>
    </a>
</li>
```

### Data Validation
- Customer selection is mandatory
- Due dates should be future dates
- Down payment should not exceed total amount
- Status transitions should follow business rules

---

## Future Enhancements

### Potential Features
1. **Customer Credit Limits**: Track and enforce customer credit limits
2. **Payment Terms**: Automatic due date calculation based on payment terms
3. **Recurring Sales**: Support for recurring sales orders
4. **Sales Analytics**: Dashboard with sales performance metrics
5. **Commission Tracking**: Sales representative commission calculations

### Integration Points
- **Customer Management**: Link to customer master data
- **Inventory Management**: Real-time inventory updates
- **Accounting**: Automatic journal entries for sales transactions
- **Reporting**: Sales reports and analytics

This structure ensures consistency with the Purchase module while providing sales-specific functionality and customer relationship management capabilities.