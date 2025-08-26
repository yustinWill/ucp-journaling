# Production (Produksi) List Page - Visual Layout

## ASCII Layout Representation

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ Header                                                                                                      │
│ Menu                                                                                                        │
│                                                                                                             │
│    Produksi                                                                                                 │
│                                                                                                             │
│    ┌─────────────────┐ ┌─────────────────┐                                                                 │
│    │ Total Stock     │ │ 1000 PCS        │                                                                 │
│    │ Tersedia        │ │                 │                                                                 │
│    │ [Green]         │ │ [Green]         │                                                                 │
│    └─────────────────┘ └─────────────────┘                                                                 │
│                                                                                                             │
│    ┌─────────────────┐ ┌─────────────────┐                                                                 │
│    │ Total Stok      │ │ Total Stock     │                                                                 │
│    │ [Gray]          │ │ [Gray]          │                                                                 │
│    └─────────────────┘ └─────────────────┘                                                                 │
│                                                                                                             │
│    ┌─────────────────────────────────┐    ┌─────────────────────────────────┐                            │
│    │ Kategori                        │    │ Pencarian                       │                            │
│    │ Cari Kategori               ▼   │    │ Cari Produksi                   │ ⚪                          │
│    └─────────────────────────────────┘    └─────────────────────────────────┘                            │
│                                                                                                             │
│    ┌─────────────┐ ┌─────────────┐                                     ┌─────────────────┐               │
│    │ Import      │ │ Ekspor      │                                     │ + Tambah        │               │
│    │             │ │             │                                     │   Produksi      │               │
│    └─────────────┘ └─────────────┘                                     └─────────────────┘               │
│                                                                                                             │
│    Production Data Table                                                                                   │
│    ┌─────────────────────────────────────────────────────────────────────────────────────────────────── │
│    │ Tanggal │ Nomor Transaksi │ Nama Produk │ Kode Produk │ Kategori │ Jumlah │ Unit │ Harga │ Aksi   │ │
│    │         │                 │             │  Produk     │ Produk   │ Produksi│      │ Perunit│       │ │
│    ├─────────┼─────────────────┼─────────────┼─────────────┼──────────┼─────────┼──────┼───────┼────────┤ │
│    │ 2-May-25│ Air Botol 1L    │ AM001       │ Air Mineral │ Botol    │ 5000    │ pcs  │ Rp    │ view   │ │
│    │         │                 │             │             │          │         │      │1,000.00│       │ │
│    ├─────────┼─────────────────┼─────────────┼─────────────┼──────────┼─────────┼──────┼───────┼────────┤ │
│    │ 13-Jul-25│ Air Galon 500gr │ AM002       │ Air Mineral │ Galon    │ 5000    │ pcs  │ Rp    │ view   │ │
│    │         │                 │             │             │          │         │      │5,000.00│       │ │
│    └─────────┴─────────────────┴─────────────┴─────────────┴──────────┴─────────┴──────┴───────┴────────┘ │
│                                                                                                             │
│    Showing 5 of 10 entries                                                             ← 1 2 3 ... 10 →  │
│                                                                                                             │
│    Details                                                                                                 │
│    - list produknya akan menampilkan beberapa informasi yang berkaitan dengan produksi produk             │
│    - untuk melakukan reversal atas produksi yang dilakukan, user bisa mengklik view -> masuk ke detail    │
│      produksi -> reversal                                                                                  │
│    - produksi tidak bisa di koreksi, jadi hanya bisa di view as detail                                    │
│                                                                                                             │
└─────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

## Grid-Based Layout

```
┌─ HEADER ─────────────────────────────────────────────────────────────────────────────────┐
│ Produksi                                                                                │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ SECTION 1: SUMMARY CARDS ──────────────────────────────────────────────────────────────┐
│ ┌─ ROW 1 ──────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 3 ──────────────────────┐ ┌─ COL 3 ──────────────────────┐                   │ │
│ │ │ Total Stock Tersedia         │ │ 1000 PCS                     │                   │ │
│ │ │ [Green Card]                 │ │ [Green Value Card]           │                   │ │
│ │ └──────────────────────────────┘ └──────────────────────────────┘                   │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ ROW 2 ──────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 3 ──────────────────────┐ ┌─ COL 3 ──────────────────────┐                   │ │
│ │ │ Total Stok                   │ │ Total Stock                  │                   │ │
│ │ │ [Gray Card]                  │ │ [Gray Card]                  │                   │ │
│ │ └──────────────────────────────┘ └──────────────────────────────┘                   │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ SECTION 2: FILTERS AND ACTIONS ────────────────────────────────────────────────────────┐
│ ┌─ ROW 1 ──────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 4 ──────────────────────┐ ┌─ COL 4 ──────────────────────┐ ┌─ COL 4 ──────────┐ │ │
│ │ │ Kategori [Dropdown]          │ │ Pencarian [Search Input]     │ │ [Search Button]  │ │ │
│ │ │ "Cari Kategori"              │ │ "Cari Produksi"              │ │ ⚪               │ │ │
│ │ └──────────────────────────────┘ └──────────────────────────────┘ └──────────────────┘ │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ ROW 2 ──────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 3 ──────────┐ ┌─ COL 3 ──────────┐                    ┌─ COL 6 ──────────────┐ │ │
│ │ │ Import [Button]  │ │ Ekspor [Button]  │                    │ + Tambah Produksi    │ │ │
│ │ │                  │ │                  │                    │ [Primary Button]     │ │ │
│ │ └──────────────────┘ └──────────────────┘                    └──────────────────────┘ │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ SECTION 3: PRODUCTION DATA TABLE ──────────────────────────────────────────────────────┐
│ ┌─ PRODUCTION TABLE ───────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 1.5 ─┐ ┌─ COL 2 ───┐ ┌─ COL 1.5 ─┐ ┌─ COL 1.5 ─┐ ┌─ COL 1.5 ─┐            │ │
│ │ │ Tanggal   │ │ Nomor     │ │ Nama      │ │ Kode      │ │ Kategori  │            │ │
│ │ │           │ │ Transaksi │ │ Produk    │ │ Produk    │ │ Produk    │            │ │
│ │ └───────────┘ └───────────┘ └───────────┘ └───────────┘ └───────────┘            │ │
│ │                                                                                  │ │
│ │ ┌─ COL 1.5 ─┐ ┌─ COL 1 ───┐ ┌─ COL 2 ───┐ ┌─ COL 1 ───┐                       │ │
│ │ │ Jumlah    │ │ Unit      │ │ Harga     │ │ Aksi      │                       │ │
│ │ │ Produksi  │ │           │ │ Perunit   │ │           │                       │ │
│ │ └───────────┘ └───────────┘ └───────────┘ └───────────┘                       │ │
│ │                                                                                  │ │
│ │ [Data Rows with sample entries]                                                  │ │
│ │ 2-May-25 | Air Botol 1L | AM001 | Air Mineral Botol | 5000 | pcs | Rp1,000.00 │ │
│ │ 13-Jul-25 | Air Galon 500gr | AM002 | Air Mineral Galon | 5000 | pcs | Rp5,000.00│ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ SECTION 4: PAGINATION AND DETAILS ─────────────────────────────────────────────────────┐
│ ┌─ PAGINATION ROW ─────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 6 ──────────────────────┐    ┌─ COL 6 ──────────────────────────────────────┐ │ │
│ │ │ Showing 5 of 10 entries      │    │ ← 1 2 3 ... 10 → [Pagination Controls]     │ │ │
│ │ └──────────────────────────────┘    └──────────────────────────────────────────────┘ │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ DETAILS SECTION ────────────────────────────────────────────────────────────────────┐ │
│ │ Details                                                                              │ │
│ │ - list produknya akan menampilkan beberapa informasi yang berkaitan dengan          │ │
│ │   produksi produk                                                                    │ │
│ │ - untuk melakukan reversal atas produksi yang dilakukan, user bisa mengklik view    │ │
│ │   -> masuk ke detail produksi -> reversal                                           │ │
│ │ - produksi tidak bisa di koreksi, jadi hanya bisa di view as detail                 │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘
```

## Responsive Breakpoints

### Desktop (12 columns)
```
Header: [Title - 12 cols]
Summary Row 1: [Total Stock Tersedia - 3 cols] [1000 PCS - 3 cols] [Empty - 6 cols]
Summary Row 2: [Total Stok - 3 cols] [Total Stock - 3 cols] [Empty - 6 cols]
Filter Row 1: [Kategori - 4 cols] [Pencarian - 4 cols] [Search Button - 4 cols]
Action Row: [Import - 3 cols] [Ekspor - 3 cols] [Tambah Produksi - 6 cols]
Table: [Full width - 12 cols]
Pagination: [Entry Info - 6 cols] [Page Controls - 6 cols]
Details: [Full width - 12 cols]
```

### Tablet (8 columns)
```
Header: [Title - 8 cols]
Summary Cards: [Stack vertically - 4 cols each]
Filters: [Kategori - 8 cols] [Pencarian - 8 cols] (stacked)
Actions: [Import/Ekspor - 4 cols each] [Tambah - 8 cols] (stacked)
Table: [Horizontal scroll - 8 cols]
Pagination: [Stacked - 8 cols each]
Details: [Full width - 8 cols]
```

### Mobile (4 columns)
```
All sections: [Full width - 4 cols]
Summary Cards: [Stacked vertically]
Filters and Actions: [Stacked vertically]
Table: [Card layout or horizontal scroll]
Pagination: [Simplified controls]
Details: [Full width text]
```

## Key Layout Features

### **Production Management Specific Elements:**
- **Title**: "Produksi" clearly identifying production management module
- **Summary Dashboard**: Production stock overview with color-coded status cards
- **Production Tracking**: Comprehensive production transaction history
- **Action Capabilities**: Import, Export, and Add Production functionality

### **Page Structure (4 Main Sections):**
1. **Summary Cards**: Production stock statistics and key metrics
2. **Filters and Actions**: Category filtering, search, and management actions
3. **Production Data Table**: Detailed production transaction listing
4. **Pagination and Details**: Navigation controls and module information

### **Summary Dashboard Features:**
- **Total Stock Tersedia**: Current available stock from production (Green - Active)
- **1000 PCS**: Stock quantity display (Green - Active)
- **Total Stok**: Additional stock metric (Gray - Inactive/Placeholder)
- **Total Stock**: Additional stock display (Gray - Inactive/Placeholder)
- **Color Coding**: Green for active/current data, Gray for inactive/additional metrics

### **Filter and Search Capabilities:**
- **Kategori Filter**: Dropdown for production category filtering
  - Placeholder: "Cari Kategori"
  - Product category-based filtering
- **Pencarian**: General search across production records
  - Placeholder: "Cari Produksi"
  - Search button with icon (⚪)
- **Advanced Filtering**: Category-specific production filtering

### **Management Actions:**
- **Import**: Import production data from external files
- **Ekspor**: Export production data to various formats
- **+ Tambah Produksi**: Primary action button for creating new production entries
  - Prominent placement and styling for easy access
  - Links to production creation form

### **Production Data Table:**
- **Comprehensive Columns**: 9 columns covering all production aspects
  - **Tanggal**: Production date
  - **Nomor Transaksi**: Production transaction number
  - **Nama Produk**: Product name being produced
  - **Kode Produk**: Product SKU/code
  - **Kategori Produk**: Product category classification
  - **Jumlah Produksi**: Quantity produced
  - **Unit**: Unit of measurement
  - **Harga Perunit**: Unit cost/price
  - **Aksi**: Action buttons (view, details)

### **Sample Production Data:**
- **Entry 1**: Air Botol 1L (AM001) - Air Mineral Botol - 5000 pcs - Rp1,000.00
- **Entry 2**: Air Galon 500gr (AM002) - Air Mineral Galon - 5000 pcs - Rp5,000.00
- **Date Range**: May 2025 to July 2025 entries
- **Product Variety**: Different product types and categories

### **Table Features:**
- **Sortable columns** for data organization
- **Action column** with view/detail access
- **Responsive design** with horizontal scroll on mobile
- **Data density** optimized for production information display

### **Pagination System:**
- **Entry Counter**: "Showing 5 of 10 entries" for data awareness
- **Navigation Controls**: ← 1 2 3 ... 10 → for page navigation
- **Responsive pagination** adapting to screen size
- **Data load management** for large production datasets

### **Interactive Elements:**
- **View Actions**: Access to production detail pages
- **Search Functionality**: Real-time search across production data
- **Category Filtering**: Dropdown-based category selection
- **Export/Import**: Bulk data management capabilities

### **Business Logic Integration:**
- **Production Tracking**: Complete production transaction history
- **Stock Integration**: Production impact on inventory levels
- **Category Management**: Product category-based organization
- **Reversal Capability**: Production reversal through detail view access

### **Information Architecture:**
- **Details Section**: Clear module explanation and usage instructions
- **Reversal Process**: Step-by-step reversal procedure explanation
- **View-Only Nature**: Clarification that production records are view-only (no direct editing)
- **Navigation Guidance**: Clear path to detailed production information

### **Production Management Features:**
- **Historical Tracking**: Complete production history with dates and details
- **Multi-Product Support**: Various product types and categories
- **Cost Tracking**: Unit cost tracking for financial analysis
- **Quantity Management**: Production volume tracking and reporting

### **System Integration Points:**
- **Inventory Connection**: Production output integration with stock levels
- **Product Catalog**: Link to product master data and categories
- **Financial Integration**: Cost tracking and production accounting
- **Reporting Foundation**: Data structure for production analytics and reporting

This production list page provides comprehensive production management with detailed tracking, filtering capabilities, and proper integration with inventory and product management systems while maintaining clear navigation and data accessibility.