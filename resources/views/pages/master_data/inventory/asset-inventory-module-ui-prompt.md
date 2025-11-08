# Asset and Inventory Module - Visual Layout & Business Context

## Business Overview & Context

### **Business Purpose:**
- **Primary Objective**: Comprehensive management of company assets and inventory from acquisition through disposal, including tracking, depreciation, valuation, and financial reporting
- **Target Users**: Finance team, Asset managers, Inventory controllers, Accounting staff, Operations managers, CFO/Finance executives
- **Business Value**: Ensures accurate asset and inventory tracking, automates depreciation calculations, maintains compliance with accounting standards, provides real-time asset valuation, and optimizes inventory levels
- **Key Performance Indicators**: Total asset value, depreciation accuracy, asset utilization rate, disposal ROI, compliance audit scores

### **Business Process Flow:**
1. **Entry Point**: Users access from main menu under Asset/Inventory section or from dashboard
2. **Core Activities**: 
   - Register new fixed assets and inventory items
   - Track asset movements and inventory stock levels
   - Calculate and record asset depreciation
   - Monitor inventory turnover and reorder points
   - Process asset disposals and inventory adjustments
   - Generate comprehensive asset and inventory reports
3. **Decision Points**: 
   - Asset vs inventory categorization
   - Depreciation method selection for assets
   - Reorder point triggers for inventory
   - Disposal timing and method for assets
   - Stock adjustment approvals
4. **Exit Points**: Reports generation, journal entry creation, stock alerts, integration with GL
5. **Integration Points**: 
   - General Ledger for automatic journal entries
   - Purchase module for asset acquisition and inventory procurement
   - Sales module for inventory depletion
   - Financial reporting for balance sheet and P&L

### **Business Rules & Logic:**
- **Validation Rules**: 
  - Asset value must be greater than minimum capitalization threshold
  - Depreciation cannot exceed asset net book value
  - Disposal date must be after acquisition date
  - Asset codes must be unique
- **Calculation Logic**: 
  - Straight-line depreciation: (Cost - Salvage Value) / Useful Life
  - Reducing balance: Net Book Value × Depreciation Rate
  - Monthly depreciation = Annual depreciation / 12
- **Approval Workflows**: Asset disposal requires manager approval for assets > threshold value
- **Compliance Requirements**: Must comply with local GAAP/IFRS standards
- **Data Retention**: Asset records kept for 7 years after disposal

## User Journey & Experience Design

### **User Personas:**
1. **Primary User - Asset Accountant**:
   - Role: Fixed Asset Accountant
   - Goals: Accurate asset recording, timely depreciation, compliance
   - Pain Points: Manual calculations, tracking multiple assets, report generation
   - Technical Proficiency: Medium to High

2. **Secondary User - Finance Manager**:
   - Role: Finance Manager/CFO
   - Goals: Asset oversight, financial reporting, strategic decisions
   - Pain Points: Lack of real-time visibility, manual consolidation
   - Technical Proficiency: Medium

### **User Task Analysis:**
- **Frequent Tasks**: View asset list, calculate monthly depreciation, update asset status
- **Critical Tasks**: Year-end asset reconciliation, disposal processing, depreciation posting
- **Complex Tasks**: Bulk asset imports, revaluation, split/merge assets
- **Time-Sensitive Tasks**: Month-end depreciation, quarterly reporting

## ASCII Layout Representation

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ Asset and Inventory Management System                                                                       │
│─────────────────────────────────────────────────────────────────────────────────────────────────────────────│
│                                                                                                             │
│ ┌─ TAB NAVIGATION ───────────────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌────────────────┐ ┌────────────────┐ ┌────────────────┐ ┌────────────────┐                          │ │
│ │ │ Daftar Aset    │ │ Akuisisi Aset  │ │ Aset Dijual/   │ │ Penyusutan     │                          │ │
│ │ │ [ACTIVE-BLUE]  │ │ [INACTIVE]     │ │ Dilepas        │ │ [INACTIVE]     │                          │ │
│ │ │                │ │                │ │ [INACTIVE]     │ │                │                          │ │
│ │ └────────────────┘ └────────────────┘ └────────────────┘ └────────────────┘                          │ │
│ └─────────────────────────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                                             │
│ ┌─ SECTION 1: DAFTAR ASET (Fixed Assets List) ──────────────────────────────────────────────────────────┐ │
│ │                                                                                                         │ │
│ │ ┌─ ACTION BAR ────────────────────────────────────────────────────────────────────────────────────┐   │ │
│ │ │ ┌─────────────┐ ┌─────────────┐ ┌─────────────┐ ┌─────────────┐ ┌─────────────┐              │   │ │
│ │ │ │ Inventaris  │ │ Cari Data   │ │ Refresh     │ │ Kolom       │ │ Ekspor      │              │   │ │
│ │ │ │ [Blue]      │ │ [Blue]      │ │ [Blue]      │ │ [Blue]      │ │ [Blue]      │              │   │ │
│ │ │ └─────────────┘ └─────────────┘ └─────────────┘ └─────────────┘ └─────────────┘              │   │ │
│ │ └───────────────────────────────────────────────────────────────────────────────────────────────┘   │ │
│ │                                                                                                         │ │
│ │ ┌─ FILTER BAR ────────────────────────────────────────────────────────────────────────────────────┐   │ │
│ │ │ Kode Aset: [___________]  Nama Aset: [___________]  Lokasi: [▼ Dropdown]  [Tambah Filter]      │   │ │
│ │ └───────────────────────────────────────────────────────────────────────────────────────────────┘   │ │
│ │                                                                                                         │ │
│ │ ┌─ DATA TABLE ────────────────────────────────────────────────────────────────────────────────────┐   │ │
│ │ │ ┌──────┬─────────────┬───────┬──────────────┬────────────┬──────────┬──────────┬────────────┐ │   │ │
│ │ │ │ No   │ Nama Aset   │ Jenis │ Tanggal      │ Nilai      │ Nilai    │ Lokasi   │ Aksi      │ │   │ │
│ │ │ │      │             │ Aset  │ Perolehan    │ Perolehan  │ Buku     │          │           │ │   │ │
│ │ │ ├──────┼─────────────┼───────┼──────────────┼────────────┼──────────┼──────────┼────────────┤ │   │ │
│ │ │ │ 1    │ Laptop      │ IT    │ 01-01-2023   │ 15,000,000 │ 12,500,000│ Jakarta │ [Edit][Del]│ │   │ │
│ │ │ │ 2    │ Mobil Box   │ Kend. │ 15-03-2023   │ 250,000,000│ 225,000,000│ Bandung│ [Edit][Del]│ │   │ │
│ │ │ │ 3    │ AC Split    │ Kantor│ 20-06-2023   │ 5,500,000  │ 5,000,000 │ Jakarta │ [Edit][Del]│ │   │ │
│ │ │ └──────┴─────────────┴───────┴──────────────┴────────────┴──────────┴──────────┴────────────┘ │   │ │
│ │ │                                                                                                   │   │ │
│ │ │ Showing 3 of 45 entries                                                   < 1 2 3 ... 15 >      │   │ │
│ │ └───────────────────────────────────────────────────────────────────────────────────────────────┘   │ │
│ └─────────────────────────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                                             │
│ ┌─ SECTION 2: AKUISISI ASET (Asset Acquisition) ────────────────────────────────────────────────────────┐ │
│ │                                                                                                         │ │
│ │ ┌─ DATA TABLE ────────────────────────────────────────────────────────────────────────────────────┐   │ │
│ │ │ Nama Aset │ Kode Aset │ Jenis │ Status      │ Tgl      │ Nilai     │ Vendor   │ Aksi          │   │ │
│ │ │           │           │       │ Perolehan   │ Perolehan │ Perolehan │          │               │   │ │
│ │ │───────────────────────────────────────────────────────────────────────────────────────────────│   │ │
│ │ │ Server HP │ A-SRV-001 │ IT    │ Purchase    │ 05-01-24  │ 75,000,000│ PT Tech  │ [Process]     │   │ │
│ │ │ Forklift  │ A-FRK-002 │ Alat  │ Lease       │ 10-01-24  │ 150,000,000│ PT Lease│ [Process]     │   │ │
│ │ └───────────────────────────────────────────────────────────────────────────────────────────────┘   │ │
│ └─────────────────────────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                                             │
│ ┌─ SECTION 3: ASET DIJUAL/DILEPAS (Disposed Assets) ────────────────────────────────────────────────────┐ │
│ │                                                                                                         │ │
│ │ ┌─ DATA TABLE ────────────────────────────────────────────────────────────────────────────────────┐   │ │
│ │ │ Nama │ Kode │ Tgl      │ Nilai    │ Nilai  │ Nilai    │ Keuntungan/│ Status   │ Aksi         │   │ │
│ │ │ Aset │ Aset │ Disposal │ Perolehan│ Buku   │ Jual     │ Kerugian   │          │              │   │ │
│ │ │──────────────────────────────────────────────────────────────────────────────────────────────│   │ │
│ │ │ PC   │ A-001│ 15-11-23 │ 8,000,000│ 2,000,000│ 2,500,000│ 500,000    │ Completed│ [View]       │   │ │
│ │ │ Mobil│ A-002│ 20-12-23 │ 200,000,000│ 50,000,000│ 65,000,000│ 15,000,000│ Pending │ [Process]   │   │ │
│ │ └───────────────────────────────────────────────────────────────────────────────────────────────┘   │ │
│ └─────────────────────────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                                             │
│ ┌─ SECTION 4: PENYUSUTAN (Depreciation) ─────────────────────────────────────────────────────────────────┐ │
│ │                                                                                                         │ │
│ │ ┌─ FILTER BAR ────────────────────────────────────────────────────────────────────────────────────┐   │ │
│ │ │ Periode: [▼ Bulan] [▼ Tahun]  Kategori: [▼ All]  [Calculate Depreciation] [Post to GL]         │   │ │
│ │ └───────────────────────────────────────────────────────────────────────────────────────────────┘   │ │
│ │                                                                                                         │ │
│ │ ┌─ DATA TABLE ────────────────────────────────────────────────────────────────────────────────────┐   │ │
│ │ │ Nama │ Kode  │ Metode     │ Nilai      │ Rate │ Penyusutan │ Akumulasi  │ Nilai Buku  │ Status│   │ │
│ │ │ Aset │ Aset  │ Penyusutan │ Perolehan  │ (%)  │ Bulan Ini  │ Penyusutan │ Akhir       │       │   │ │
│ │ │──────────────────────────────────────────────────────────────────────────────────────────────│   │ │
│ │ │ AC   │ A-001 │ St. Line   │ 10,000,000 │ 10%  │ 83,333     │ 1,000,000  │ 9,000,000   │ Posted│   │ │
│ │ │ Mobil│ A-002 │ St. Line   │ 250,000,000│ 20%  │ 4,166,667  │ 50,000,000 │ 200,000,000 │ Draft │   │ │
│ │ │ PC   │ A-003 │ Reducing B.│ 15,000,000 │ 25%  │ 312,500    │ 3,750,000  │ 11,250,000  │ Draft │   │ │
│ │ └───────────────────────────────────────────────────────────────────────────────────────────────┘   │ │
│ └─────────────────────────────────────────────────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

## Component Specifications

### **Tab Navigation System:**
- **Visual Design**: Horizontal tabs with rounded corners
- **Active State**: Light blue background (#E3F2FD) with darker blue border
- **Inactive State**: Light gray background (#F5F5F5) 
- **Hover Effect**: Subtle shadow and slight color darkening
- **Click Behavior**: Switches content area without page reload
- **Tab Order**: 
  1. Daftar Aset (Fixed Assets List)
  2. Akuisisi Aset (Asset Acquisition) 
  3. Aset Dijual/Dilepas (Disposed Assets)
  4. Penyusutan (Depreciation)

### **Section 1: Daftar Aset (Fixed Assets List)**

#### Action Buttons:
- **Inventaris**: Opens asset inventory form/modal
- **Cari Data**: Advanced search functionality
- **Refresh**: Reloads current data
- **Kolom**: Column visibility toggle
- **Ekspor**: Export to Excel/PDF options

#### Filter Components:
- **Kode Aset**: Text input for asset code search
- **Nama Aset**: Text input for asset name search
- **Lokasi**: Dropdown for location filtering
- **Tambah Filter**: Opens advanced filter modal

#### Table Columns:
1. **No**: Sequential row number
2. **Nama Aset**: Asset name/description
3. **Jenis Aset**: Asset category (IT, Kendaraan, Kantor, etc.)
4. **Tanggal Perolehan**: Acquisition date (DD-MM-YYYY)
5. **Nilai Perolehan**: Original purchase value (Rp format)
6. **Nilai Buku**: Current net book value (Rp format)
7. **Lokasi**: Physical location
8. **Aksi**: Action buttons [Edit][Delete]

### **Section 2: Akuisisi Aset (Asset Acquisition)**

#### Purpose: 
Queue for new assets pending full registration

#### Table Columns:
1. **Nama Aset**: Asset name
2. **Kode Aset**: Assigned asset code
3. **Jenis**: Asset type
4. **Status Perolehan**: Acquisition method (Purchase/Lease/Grant)
5. **Tanggal Perolehan**: Acquisition date
6. **Nilai Perolehan**: Purchase value
7. **Vendor**: Supplier/vendor name
8. **Aksi**: [Process] button to complete registration

### **Section 3: Aset Dijual/Dilepas (Disposed Assets)**

#### Purpose: 
Track and process asset disposals

#### Table Columns:
1. **Nama Aset**: Asset name
2. **Kode Aset**: Asset code
3. **Tanggal Disposal**: Disposal date
4. **Nilai Perolehan**: Original cost
5. **Nilai Buku**: Book value at disposal
6. **Nilai Jual**: Selling price
7. **Keuntungan/Kerugian**: Gain/loss on disposal
8. **Status**: Completed/Pending/In Process
9. **Aksi**: [View] for completed, [Process] for pending

### **Section 4: Penyusutan (Depreciation)**

#### Filter/Action Bar:
- **Periode**: Month and Year dropdowns
- **Kategori**: Asset category filter
- **Calculate Depreciation**: Runs depreciation calculation
- **Post to GL**: Posts depreciation to General Ledger

#### Table Columns:
1. **Nama Aset**: Asset name
2. **Kode Aset**: Asset code
3. **Metode Penyusutan**: Depreciation method (Straight Line/Reducing Balance)
4. **Nilai Perolehan**: Original cost
5. **Rate (%)**: Depreciation rate percentage
6. **Penyusutan Bulan Ini**: Current month depreciation
7. **Akumulasi Penyusutan**: Accumulated depreciation
8. **Nilai Buku Akhir**: Ending book value
9. **Status**: Posted/Draft

## Technical Specifications

### **Data Management:**
- **Real-time Updates**: Asset values update automatically
- **Batch Operations**: Support bulk asset imports/updates
- **Data Validation**: Client and server-side validation
- **Auto-calculation**: Depreciation calculates on schedule
- **Audit Trail**: Track all changes with user/timestamp

### **UI Components:**
- **Tables**: Sortable, filterable with pagination
- **Forms**: Modal dialogs for add/edit operations
- **Dropdowns**: Searchable select components
- **Date Pickers**: Calendar widgets for date selection
- **Number Inputs**: Formatted for currency (Rp)

### **Color Scheme:**
```css
/* Primary Colors */
--primary-blue: #1976D2;
--active-tab: #E3F2FD;
--inactive-tab: #F5F5F5;
--button-blue: #2196F3;
--success-green: #4CAF50;
--warning-orange: #FF9800;
--error-red: #F44336;

/* Table Colors */
--table-header: #F5F5F5;
--table-border: #E0E0E0;
--row-hover: #F5F5F5;
--row-alternate: #FAFAFA;
```

### **Responsive Design:**
- **Desktop (12 columns)**: Full layout as shown
- **Tablet (8 columns)**: Stacked action buttons, horizontal scroll for tables
- **Mobile (4 columns)**: Card-based layout, simplified tables

### **Performance Requirements:**
- **Initial Load**: < 2 seconds
- **Tab Switch**: < 200ms
- **Search/Filter**: < 500ms
- **Depreciation Calc**: < 3 seconds for 1000 assets

### **Integration Points:**
- **General Ledger**: Auto-post depreciation entries
- **Purchase Module**: Pull acquisition data
- **Document Management**: Attach invoices/photos
- **Reporting Module**: Generate asset reports
- **Audit Module**: Compliance tracking

### **Security & Permissions:**
- **View Assets**: Basic user permission
- **Add/Edit Assets**: Asset manager role
- **Delete Assets**: Requires approval workflow
- **Post Depreciation**: Finance manager only
- **Export Data**: Controlled by role

### **Business Logic Implementation:**

#### Asset Lifecycle:
1. **Acquisition**: Entry via purchase/transfer/grant
2. **Registration**: Full details and categorization
3. **Active Use**: Regular depreciation calculation
4. **Disposal**: Sale/write-off with gain/loss
5. **Archive**: Historical record retention

#### Depreciation Methods:
1. **Straight Line**: (Cost - Salvage) / Useful Life
2. **Reducing Balance**: NBV × Rate%
3. **Units of Production**: Based on usage
4. **Custom**: User-defined calculation

#### Validation Rules:
- Asset code format: [Category]-[Location]-[Sequence]
- Minimum capitalization: Rp 1,000,000
- Maximum useful life: 50 years
- Disposal price cannot be negative
- Depreciation cannot exceed cost

This comprehensive Fixed Asset Management module provides complete lifecycle management from acquisition through disposal, with automated depreciation calculation and seamless integration with financial systems.
