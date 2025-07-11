# Product List Page - Barang & Jasa Tab - Visual Layout

## ASCII Layout Representation

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ Header                                                                                                      │
│ Menu                                                                                                        │
│                                                                                                             │
│    Produk                                                                      ┌─────────────────────────┐ │
│                                                                                │ Tindakan            ▼   │ │
│    ┌─────────────┐ ┌─────────────┐                                            └─────────────────────────┘ │
│    │ Barang & Jasa│ │   Gudang    │                                                                        │
│    │  [ACTIVE]   │ │ [INACTIVE]  │                                                                        │
│    │   #28a745   │ │   #e9ecef   │                                                                        │
│    └─────────────┘ └─────────────┘                                                                        │
│                                                                                                             │
│    ┌─────────────────────────────┐ ┌─────────────────────────────┐ ┌─────────────────────────────┐       │
│    │ Stok Tersedia           📊  │ │ Stok Segera Habis       📊  │ │ Stok Habis              📊  │       │
│    │ Total Produk                │ │ Total Produk                │ │ Total Produk                │       │
│    │ 0                           │ │ 0                           │ │ 0                           │       │
│    │ [Default Background]        │ │ [Yellow Background #ffc107] │ │ [Orange Background #fd7e14] │       │
│    └─────────────────────────────┘ └─────────────────────────────┘ └─────────────────────────────┘       │
│                                                                                                             │
│    ┌─────────────────────────────┐                                                                        │
│    │ Gudang                  📊  │                                                                        │
│    │ Total Produk                │                                                                        │
│    │ 0                           │                                                                        │
│    │ [Green Background #28a745]  │                                                                        │
│    └─────────────────────────────┘                                                                        │
│                                                                                                             │
│    ┌─────────────────────────────┐ ┌─────────────────────────────┐ ┌─────────────────────────────┐       │
│    │ Atur Kategori Produk        │ │ Impor                   ▼   │ │ Ekspor                  ▼   │       │
│    │ [Blue Background #007bff]   │ │ [Blue Background #007bff]   │ │ [Blue Background #007bff]   │       │
│    └─────────────────────────────┘ └─────────────────────────────┘ └─────────────────────────────┘       │
│                                                                                                             │
│    ┌─────────────────────────────┐    ┌─────────────────────────────┐                                    │
│    │ Cari Kontak             🔍  │    │ Filter                  🔽   │                                    │
│    │                             │    │                             │                                    │
│    └─────────────────────────────┘    └─────────────────────────────┘                                    │
│                                                                                                             │
│    ┌─────────────────────────────────────────────────────────────────────────────────────────────────────┐ │
│    │ Nama Produk │ Kode Produk │ Kategori │ Total │ Qty    │ Batas │ Unit │ Harga    │ Harga Beli │ ... │ │
│    │             │             │ Produk   │ Stok  │ Tersedia│ Min   │      │ Rata-Rata│ Terakhir   │     │ │
│    │─────────────────────────────────────────────────────────────────────────────────────────────────────│ │
│    │ Pulpen      │ P-G2-001    │ Alat     │ 150   │ 145    │ 20    │ pcs  │ Rp 9,500 │ Rp 10,000  │ ... │ │
│    │ Pilot G2    │             │ Tulis    │       │        │       │      │          │            │     │ │
│    │─────────────────────────────────────────────────────────────────────────────────────────────────────│ │
│    │ Kertas A4   │ KRT-A4-80   │ Kertas   │ 500   │ 480    │ 50    │ rim  │ Rp 42,000│ Rp 45,000  │ ... │ │
│    │ 80gsm       │             │          │       │        │       │      │          │            │     │ │
│    └─────────────────────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                                             │
│    Showing 5 of 10 entries                                              < 1 2 3 ... 10 >                 │
│                                                                                                             │
└─────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

## Dropdown Menus Layout

### Tindakan Dropdown
```
┌─────────────────────────────┐
│ Produk - Tambah Produk Baru │
│ Gudang - Tambah Gudang Baru │
│ Gudang - Transfer Gudang    │
└─────────────────────────────┘
```

### Impor Dropdown
```
┌─────────────────────────────┐
│ Produk Baru                 │
│ Update Produk               │
│ Produk Bundle               │
└─────────────────────────────┘
```

### Ekspor Dropdown
```
┌─────────────────────────────┐
│ Semua Produk                │
│ Produk Bundle & Komposisi   │
└─────────────────────────────┘
```

## Filter Modal Layout

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ Filter                                                                                                  🔽  │
│                                                                                                             │
│ ┌─────────────────────────────────────────────────────────────────────────────────────────────────────────┐ │
│ │                                                                                                         │ │
│ │ Kata Kunci                                                                                              │ │
│ │ ┌─────────────────────────────────────────────────────────────────────────────────────────────────┐   │ │
│ │ │                                                                                                 │   │ │
│ │ └─────────────────────────────────────────────────────────────────────────────────────────────────┘   │ │
│ │                                                                                                         │ │
│ │ Tipe Produk                                                                                             │ │
│ │ ┌─────────────────────────────────────────────────────────────────────────────────────────────────┐   │ │
│ │ │ Pilih tipe                                                                                  ▼   │   │ │
│ │ └─────────────────────────────────────────────────────────────────────────────────────────────────┘   │ │
│ │                                                                                                         │ │
│ │ Kategori Produk                                                                                         │ │
│ │ ┌─────────────────────────────────────────────────────────────────────────────────────────────────┐   │ │
│ │ │ Pilih kategori                                                                              ▼   │   │ │
│ │ └─────────────────────────────────────────────────────────────────────────────────────────────────┘   │ │
│ │                                                                                                         │ │
│ │ ☐ Mencangkup semua        ☐ Salah satu                                                                 │ │
│ │                                                                                                         │ │
│ │ ☐ Tampilkan arsip                                                                                       │ │
│ │                                                                                                         │ │
│ │                                          ┌─────────────┐ ┌─────────────┐                              │ │
│ │                                          │ Reset Filter│ │ Terapkan    │                              │ │
│ │                                          │ [Red]       │ │ [Green]     │                              │ │
│ │                                          └─────────────┘ └─────────────┘                              │ │
│ └─────────────────────────────────────────────────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

## Grid-Based Layout

```
┌─ HEADER SECTION ─────────────────────────────────────────────────────────────────────────┐
│ ┌─ COL 8 ─────────────────────────────────────┐ ┌─ COL 4 ─────────────────────────────┐ │
│ │ Produk                                      │ │ ┌─ Tindakan Dropdown ─────────────┐ │ │
│ │                                             │ │ │ Tindakan                    ▼   │ │ │
│ │ ┌─ Sub-tabs ──────────────────────────────┐ │ │ └─────────────────────────────────┘ │ │
│ │ │ [Barang & Jasa] [Gudang]                │ │ └─────────────────────────────────────┘ │
│ │ │ #28a745        #e9ecef                  │ │                                         │
│ │ └─────────────────────────────────────────┘ │                                         │
│ └─────────────────────────────────────────────┘                                         │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ STOCK SUMMARY CARDS ────────────────────────────────────────────────────────────────────┐
│ ┌─ COL 3 ─────────────────────┐ ┌─ COL 3 ─────────────────────┐ ┌─ COL 3 ──────────────┐ │
│ │ Stok Tersedia           📊  │ │ Stok Segera Habis       📊  │ │ Stok Habis       📊  │ │
│ │ Total Produk: 0             │ │ Total Produk: 0             │ │ Total Produk: 0      │ │
│ │ [Default Background]        │ │ [Yellow Background]         │ │ [Orange Background]  │ │
│ └─────────────────────────────┘ └─────────────────────────────┘ └──────────────────────┘ │
│                                                                                          │
│ ┌─ COL 3 ─────────────────────┐                                                          │
│ │ Gudang                  📊  │                                                          │
│ │ Total Produk: 0             │                                                          │
│ │ [Green Background]          │                                                          │
│ └─────────────────────────────┘                                                          │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ ACTION BUTTONS ─────────────────────────────────────────────────────────────────────────┐
│ ┌─ COL 4 ─────────────────────┐ ┌─ COL 4 ─────────────────────┐ ┌─ COL 4 ──────────────┐ │
│ │ Atur Kategori Produk        │ │ Impor                   ▼   │ │ Ekspor           ▼   │ │
│ │ [Blue Background]           │ │ [Blue Background]           │ │ [Blue Background]    │ │
│ └─────────────────────────────┘ └─────────────────────────────┘ └──────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ SEARCH AND FILTER ──────────────────────────────────────────────────────────────────────┐
│ ┌─ COL 8 ─────────────────────┐    ┌─ COL 4 ─────────────────────┐                      │
│ │ Cari Kontak             🔍  │    │ Filter                  🔽   │                      │
│ └─────────────────────────────┘    └─────────────────────────────┘                      │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ PRODUCT TABLE ──────────────────────────────────────────────────────────────────────────┐
│ ┌─ COL 12 ─────────────────────────────────────────────────────────────────────────────┐ │
│ │ [Comprehensive Product Table with 11 columns]                                       │ │
│ │ Horizontal scroll for smaller screens                                                │ │
│ │ Sortable columns with pagination controls                                            │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ PAGINATION ─────────────────────────────────────────────────────────────────────────────┐
│ ┌─ COL 6 ─────────────────────┐    ┌─ COL 6 ─────────────────────┐                      │
│ │ Showing 5 of 10 entries     │    │ < 1 2 3 ... 10 >            │                      │
│ └─────────────────────────────┘    └─────────────────────────────┘                      │
└──────────────────────────────────────────────────────────────────────────────────────────┘
```

## Responsive Breakpoints

### Desktop (12 columns)
```
Header: [Title - 8 cols] [Tindakan - 4 cols]
Sub-tabs: [Full width - 12 cols]
Stock Cards: [Card 1 - 3 cols] [Card 2 - 3 cols] [Card 3 - 3 cols] [Card 4 - 3 cols]
Actions: [Kategori - 4 cols] [Impor - 4 cols] [Ekspor - 4 cols]
Search: [Search - 8 cols] [Filter - 4 cols]
Table: [Full width - 12 cols with horizontal scroll]
Pagination: [Info - 6 cols] [Controls - 6 cols]
```

### Tablet (8 columns)
```
Header: [Title - 6 cols] [Tindakan - 2 cols]
Sub-tabs: [Full width - 8 cols]
Stock Cards: [Card 1 - 4 cols] [Card 2 - 4 cols] (stacked 2x2)
Actions: [All buttons - 8 cols stacked]
Search: [Search - 6 cols] [Filter - 2 cols]
Table: [Horizontal scroll - 8 cols]
```

### Mobile (4 columns)
```
All sections: [Full width - 4 cols]
Stock cards: Stacked vertically
Action buttons: Full width stacked
Table: Horizontal scroll or card layout
Dropdowns: Full width
```

## Key Layout Features

### **Barang & Jasa Tab Specific Elements:**
- **Page Title**: "Produk" clearly identifying product management
- **Sub-Tab Navigation**: "Barang & Jasa" (active, green) and "Gudang" (inactive, gray)
- **Product Focus**: Emphasis on product and service inventory management
- **Comprehensive Data**: Complete product information with stock and pricing

### **Active Sub-Tab State:**
- **Barang & Jasa Tab**: Green background (#28a745) indicating active state
- **Gudang Tab**: Gray background (#e9ecef) for inactive warehouse view
- **Content Focus**: Product and service-centered functionality
- **Navigation**: Easy switching between product list and warehouse views

### **Stock Summary Dashboard:**
- **Stok Tersedia**: Available stock overview (default background)
- **Stok Segera Habis**: Low stock warning (yellow background #ffc107)
- **Stok Habis**: Out of stock alert (orange background #fd7e14)
- **Gudang**: Warehouse summary (green background #28a745)
- **Info Icons**: 📊 indicators for detailed reports

### **Action Button Integration:**
- **Atur Kategori Produk**: Category management (blue background)
- **Impor Dropdown**: Multiple import options
  - Produk Baru, Update Produk, Produk Bundle
- **Ekspor Dropdown**: Export functionality
  - Semua Produk, Produk Bundle & Komposisi
- **Tindakan Dropdown**: Primary actions
  - Tambah Produk Baru, Tambah Gudang Baru, Transfer Gudang

### **Comprehensive Product Table:**
- **11-Column Layout**: Complete product information display
- **Sample Data**: Pulpen Pilot G2, Kertas A4 80gsm with realistic pricing
- **Stock Information**: Total stock, available quantity, minimum thresholds
- **Pricing Data**: Average price, last purchase price, purchase price, selling price
- **Product Details**: Name, code, category, unit of measure

### **Advanced Filter System:**
- **Kata Kunci**: Keyword search across product fields
- **Tipe Produk**: Product type filtering (dropdown)
- **Kategori Produk**: Category-based filtering (dropdown)
- **Checkbox Options**: Mencangkup semua, Salah satu, Tampilkan arsip
- **Filter Actions**: Reset Filter (red), Terapkan (green)

### **Interactive Elements:**
- **Clickable Cards**: Stock summary cards link to detailed reports
- **Dropdown Menus**: Multiple action and filter options
- **Search Function**: Real-time product search capability
- **Table Sorting**: Sortable columns for data organization
- **Pagination**: Navigate through large product datasets

### **Business Logic Integration:**
- **Stock Level Monitoring**: Real-time inventory tracking with color-coded alerts
- **Category Management**: Hierarchical product organization and filtering
- **Multi-warehouse Support**: Warehouse-specific inventory management
- **Import/Export Operations**: Bulk data management for product catalogs
- **Pricing Management**: Multiple price point tracking for cost and profitability

### **Data Display Features:**
- **Product Information**: Complete product details with codes and categories
- **Stock Metrics**: Available vs. total stock with minimum threshold tracking
- **Pricing Structure**: Multiple price types for comprehensive cost management
- **Unit Management**: Various units of measure (pcs, rim, kg, etc.)
- **Pagination Info**: "Showing 5 of 10 entries" with navigation controls

### **Visual Design Elements:**
- **Color-Coded Alerts**: Yellow for low stock, orange for out of stock
- **Sub-Tab Interface**: Active tab highlighting with green background
- **Button Consistency**: Blue backgrounds for all action buttons
- **Icon Integration**: 📊 icons for data visualization and 🔍 for search
- **Responsive Table**: Horizontal scroll support for smaller screens

### **Performance Optimizations:**
- **Lazy Loading**: Load product data only when needed
- **Efficient Queries**: Optimized database queries for large product catalogs
- **Real-time Updates**: Live stock level updates across multiple users
- **Caching Strategy**: Strategic caching of frequently accessed product data

### **Filter and Search Capabilities:**
- **Advanced Filtering**: Multi-criteria product filtering with logical operators
- **Real-time Search**: Instant product search across multiple fields
- **Filter Persistence**: Save and restore filter states between sessions
- **Export Filtering**: Apply current filters to export operations

### **Product and Service Management:**
- **Dual Support**: Handle both physical products (Barang) and services (Jasa)
- **Category Organization**: Hierarchical categorization for products and services
- **Stock Tracking**: Inventory management for physical products
- **Service Integration**: Service offerings and pricing management

### **Integration Points:**
- **Transaction Modules**: Seamless integration with sales and purchase orders
- **Accounting System**: Real-time inventory valuation and cost tracking
- **Supplier Management**: Link products to suppliers for purchasing
- **Customer Analytics**: Product performance and customer preference analysis

This Barang & Jasa tab provides comprehensive product and service management capabilities while maintaining excellent user experience across all device types. The design supports efficient product discovery, stock monitoring, and bulk operations essential for effective inventory management in business operations.