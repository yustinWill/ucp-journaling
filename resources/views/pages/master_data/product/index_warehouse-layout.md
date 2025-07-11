# Product Warehouse Tab - Visual Layout

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
│    │ [INACTIVE]  │ │  [ACTIVE]   │                                                                        │
│    │   #e9ecef   │ │   #28a745   │                                                                        │
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
│    │ Daftar Gudang               │ │ Daftar Transfer Gudang      │ │ Impor               Cari    │       │
│    │ [Gray Background #6c757d]   │ │ [Blue Background #007bff]   │ │ [Blue Bg #007bff]   Gudang  │       │
│    └─────────────────────────────┘ └─────────────────────────────┘ └─────────────────────────────┘       │
│                                                                                                             │
│    ┌─────────────────────────────────────────────────────────────────────────────────────────────────────┐ │
│    │ Kode Gudang │ Nama Gudang           │ Penanggung Jawab │ Alamat                                     │ │
│    │─────────────────────────────────────────────────────────────────────────────────────────────────────│ │
│    │ GDG-001     │ Gudang Utama Pusat    │ Yustinus         │ Jl. Industri No. 12, Kawasan Bisnis,      │ │
│    │ [SELECTED ROW - Blue Background #007bff]                  │ Jakarta                                │ │
│    │─────────────────────────────────────────────────────────────────────────────────────────────────────│ │
│    │ GDG-002     │ Gudang Cabang Bandung │ Ririn            │ Jl. Soekarno Hatta No. 88, Bandung        │ │
│    └─────────────────────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                                             │
│    Showing 5 of 10 entries                                              < 1 2 3 ... 10 >                 │
│                                                                                                             │
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
│ │ │ #e9ecef        #28a745 (active)         │ │                                         │
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

┌─ WAREHOUSE ACTION BUTTONS ───────────────────────────────────────────────────────────────┐
│ ┌─ COL 3 ─────────────────────┐ ┌─ COL 3 ─────────────────────┐ ┌─ COL 6 ──────────────┐ │
│ │ Daftar Gudang               │ │ Daftar Transfer Gudang      │ │ Impor    Cari Gudang │ │
│ │ [Gray Background #6c757d]   │ │ [Blue Background #007bff]   │ │ [Blue Backgrounds]   │ │
│ └─────────────────────────────┘ └─────────────────────────────┘ └──────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ WAREHOUSE TABLE SECTION ────────────────────────────────────────────────────────────────┐
│ ┌─ WAREHOUSE TABLE (COL 12) ──────────────────────────────────────────────────────────────┐ │
│ │ ┌─ 15% ──────────┐ ┌─ 25% ──────────┐ ┌─ 20% ─────────┐ ┌─ 40% ─────────────────────┐ │ │
│ │ │ Kode Gudang    │ │ Nama Gudang    │ │ Penanggung    │ │ Alamat                    │ │ │
│ │ │                │ │                │ │ Jawab         │ │                           │ │ │
│ │ ├────────────────┤ ├────────────────┤ ├───────────────┤ ├───────────────────────────┤ │ │
│ │ │ GDG-001        │ │ Gudang Utama   │ │ Yustinus      │ │ Jl. Industri No. 12,      │ │ │
│ │ │ [BLUE BG]      │ │ Pusat [BLUE]   │ │ [BLUE BG]     │ │ Kawasan Bisnis, Jakarta   │ │ │
│ │ ├────────────────┤ ├────────────────┤ ├───────────────┤ ├───────────────────────────┤ │ │
│ │ │ GDG-002        │ │ Gudang Cabang  │ │ Ririn         │ │ Jl. Soekarno Hatta No.    │ │ │
│ │ │                │ │ Bandung        │ │               │ │ 88, Bandung               │ │ │
│ │ └────────────────┘ └────────────────┘ └───────────────┘ └───────────────────────────┘ │ │
│ └──────────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ PAGINATION (COL 12) ───────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 6 ─────────────────────┐    ┌─ COL 6 ─────────────────────┐                  │ │
│ │ │ Showing 5 of 10 entries     │    │ < 1 2 3 ... 10 >            │                  │ │
│ │ └─────────────────────────────┘    └─────────────────────────────┘                  │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘
```

## Responsive Breakpoints

### Desktop (12 columns)
```
Header: [Title - 8 cols] [Tindakan - 4 cols]
Sub-tabs: [Full width - 12 cols]
Stock Cards: [Card 1 - 3 cols] [Card 2 - 3 cols] [Card 3 - 3 cols] [Card 4 - 3 cols]
Actions: [Daftar - 3 cols] [Transfer - 3 cols] [Impor & Cari - 6 cols]
Table: [Full width - 12 cols]
Pagination: [Info - 6 cols] [Controls - 6 cols]
```

### Tablet (8 columns)
```
Header: [Title - 6 cols] [Tindakan - 2 cols]
Sub-tabs: [Full width - 8 cols]
Stock Cards: [Card 1 - 4 cols] [Card 2 - 4 cols] (stacked 2x2)
Actions: [All buttons - 8 cols stacked]
Table: [Horizontal scroll - 8 cols]
```

### Mobile (4 columns)
```
All sections: [Full width - 4 cols]
Stock cards: Stacked vertically
Action buttons: Full width stacked
Table: Horizontal scroll or card layout
Sub-tabs: Full width
```

## Key Layout Features

### **Warehouse Tab Specific Elements:**
- **Page Context**: "Produk" with warehouse management focus
- **Sub-tab Navigation**: "Barang & Jasa" (inactive) and "Gudang" (active, green)
- **Warehouse Operations**: Specialized buttons for warehouse management
- **Location-based Data**: Warehouse-specific information and tracking

### **Active Tab State:**
- **Gudang Tab**: Green background (#28a745) indicating active warehouse view
- **Barang & Jasa Tab**: Gray background (#e9ecef) for inactive product view
- **Content Context**: Warehouse-focused functionality and data
- **Navigation**: Easy switching between product and warehouse views

### **Stock Summary Dashboard (Warehouse Context):**
- **Stok Tersedia**: Available stock across all warehouses (default background)
- **Stok Segera Habis**: Low stock warning across locations (yellow #ffc107)
- **Stok Habis**: Out of stock across warehouses (orange #fd7e14)
- **Gudang**: Total warehouse locations (green #28a745)
- **Multi-location Focus**: Warehouse-aggregated stock information

### **Warehouse-Specific Action Buttons:**
- **Daftar Gudang**: List warehouses (gray background #6c757d)
- **Daftar Transfer Gudang**: Warehouse transfers (blue background #007bff)
- **Impor**: Import warehouse data (blue background #007bff)
- **Cari Gudang**: Search warehouses (blue background #007bff)

### **Warehouse Data Table:**
- **4-Column Layout**: Focused warehouse information display
- **Kode Gudang**: Warehouse codes (GDG-001, GDG-002)
- **Nama Gudang**: Warehouse names (Gudang Utama Pusat, Gudang Cabang Bandung)
- **Penanggung Jawab**: Responsible persons (Yustinus, Ririn)
- **Alamat**: Complete warehouse addresses
- **Selected Row**: Blue background highlighting (#007bff)

### **Sample Warehouse Data:**
- **GDG-001**: Gudang Utama Pusat, managed by Yustinus, Jakarta location
- **GDG-002**: Gudang Cabang Bandung, managed by Ririn, Bandung location
- **Address Format**: Complete Indonesian addresses with street and city
- **Hierarchy**: Main warehouse (Pusat) and branch warehouse (Cabang)

### **Interactive Elements:**
- **Sub-tab Navigation**: Switch between product and warehouse views
- **Row Selection**: Blue highlighting for selected warehouse
- **Clickable Codes**: Warehouse codes link to detailed warehouse views
- **Action Buttons**: Multiple warehouse management operations
- **Stock Cards**: Clickable cards for detailed warehouse stock reports

### **Business Logic Integration:**
- **Multi-warehouse Management**: Comprehensive warehouse location tracking
- **Transfer Operations**: Inter-warehouse transfer management capabilities
- **Location Hierarchy**: Support for main and branch warehouse structures
- **Personnel Assignment**: Responsible person tracking for each location

### **Data Display Features:**
- **Warehouse Identification**: Unique codes and descriptive names
- **Location Information**: Complete address details for each warehouse
- **Responsibility Tracking**: Assigned personnel for warehouse management
- **Geographic Distribution**: Warehouses across different cities (Jakarta, Bandung)

### **Visual Design Elements:**
- **Color Consistency**: Matching color scheme with main product page
- **Row Highlighting**: Clear selection indication with blue background
- **Icon Integration**: 📊 icons for data visualization and reporting
- **Button Hierarchy**: Different colors indicating button importance and function

### **Performance Features:**
- **Efficient Layout**: Optimized for warehouse data display and management
- **Quick Actions**: Streamlined access to common warehouse operations
- **Search Integration**: Built-in warehouse search and filtering capabilities
- **Pagination Support**: Handle large numbers of warehouse locations

### **Warehouse Management Capabilities:**
- **Location Tracking**: Complete warehouse location and address management
- **Personnel Management**: Assign and track responsible persons per warehouse
- **Transfer Operations**: Manage inventory movements between warehouses
- **Stock Distribution**: Monitor inventory levels across all warehouse locations

### **Integration Points:**
- **Product Integration**: Seamless connection with main product inventory
- **Transfer System**: Link with warehouse transfer management
- **Personnel System**: Integration with staff and responsibility tracking
- **Location Services**: Geographic and address management capabilities

This warehouse tab provides comprehensive warehouse management functionality while maintaining consistency with the main product page design. The layout supports efficient warehouse operations, location tracking, and inventory distribution management across multiple warehouse locations.