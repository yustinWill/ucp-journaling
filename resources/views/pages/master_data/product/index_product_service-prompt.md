# Product List Page - Barang & Jasa Tab - Technical Specification

## Overview
This document provides comprehensive technical specifications for creating the Product List page (Barang & Jasa tab) in the master data module. This tab displays and manages all product and service information with comprehensive filtering, import/export capabilities, and stock management features.

## Form Purpose
The Product List page (Barang & Jasa tab) is specifically designed for:
- Displaying comprehensive product and service inventory with stock levels and pricing information
- Managing product categories and warehouse-specific stock levels
- Supporting product import/export operations for bulk data management
- Providing advanced filtering capabilities for product search and analysis
- Enabling product management operations including creation, editing, and categorization
- Supporting stock monitoring with available, allocated, and minimum stock tracking
- Integrating with warehouse management and transaction processing systems

## UI Structure Requirements

### 1. Page Header
- **Title**: "Produk"
- **Layout**: Full-width header with navigation tabs
- **Page ID**: `productListPage`

### 2. Action Panel (Top Right)
- **Tindakan**: Primary action dropdown (blue background)
  - Dropdown menu with multiple product management actions
  - Positioned in top-right corner of content area

### 3. Sub-Tab Navigation
Located below page title:

#### Sub-Tab Structure:
- **Barang & Jasa**: Active tab (green background #28a745)
  - Primary product and service listing with stock information
  - Complete product details and inventory management
- **Gudang**: Inactive tab (gray background #e9ecef)
  - Warehouse-specific product and stock management
  - Location-based inventory tracking

### 4. Stock Summary Cards
Dashboard overview of product inventory status:

#### Summary Cards Layout:
- **Stok Tersedia**: Available stock summary
  - Color: Default/neutral background
  - Total Produk: 0 (sample data)
  - Icon: Info icon for detailed view
- **Stok Segera Habis**: Low stock warning
  - Color: Yellow/warning background (#ffc107)
  - Total Produk: 0 (sample data)
  - Critical stock level monitoring
- **Stok Habis**: Out of stock alert
  - Color: Orange/danger background (#fd7e14)
  - Total Produk: 0 (sample data)
  - Immediate attention required
- **Gudang**: Warehouse summary
  - Color: Green/success background (#28a745)
  - Total Produk: 0 (sample data)
  - Warehouse distribution overview

### 5. Product Management Actions
Action buttons below summary cards:

#### Action Button Row:
- **Atur Kategori Produk**: Manage product categories
  - Background: Blue (#007bff)
  - Category management functionality
- **Impor**: Import products
  - Background: Blue (#007bff)
  - Dropdown with import options
- **Ekspor**: Export products
  - Background: Blue (#007bff)
  - Dropdown with export options

### 6. Product Search and Filter
Search functionality above product table:

#### Search Elements:
- **Cari Kontak**: Search input field
  - Placeholder: "Cari kontak..."
  - Real-time search capability
- **Filter**: Advanced filter options
  - Filter icon button
  - Opens filter modal for detailed search

### 7. Product Data Table
Comprehensive product listing with detailed information:

#### Table Columns:
- **Nama Produk** (Product Name)
  - Sample: "Pulpen Pilot G2", "Kertas A4 80gsm"
  - Clickable links to product detail pages
- **Kode Produk** (Product Code)
  - Sample: "P-G2-001", "KRT-A4-80"
  - Unique product identifiers
- **Kategori Produk** (Product Category)
  - Sample: "Alat Tulis Kantor", "Kertas"
  - Product categorization for organization
- **Total Stok** (Total Stock)
  - Sample: 150, 500
  - Current inventory levels
- **Qty Tersedia** (Available Quantity)
  - Sample: 145, 480
  - Available for sale/use
- **Batas Minimum** (Minimum Threshold)
  - Sample: 20, 50
  - Low stock warning levels
- **Unit** (Unit of Measure)
  - Sample: "pcs", "rim"
  - Product measurement units
- **Harga Rata-Rata** (Average Price)
  - Sample: Rp 9,500.00, Rp 42,000.00
  - Average cost pricing
- **Harga Beli Terakhir** (Last Purchase Price)
  - Sample: Rp 10,000.00, Rp 45,000.00
  - Most recent purchase pricing
- **Harga Beli** (Purchase Price)
  - Sample: Rp 9,000.00, Rp 40,000.00
  - Standard purchase pricing
- **Harga Jual** (Selling Price)
  - Sample: Rp 12,000.00, Rp 50,000.00
  - Customer selling pricing

#### Table Features:
- **Pagination**: "Showing 5 of 10 entries"
- **Sorting**: Clickable column headers for data sorting
- **Row Selection**: Support for bulk operations
- **Responsive Design**: Horizontal scroll for smaller screens

## JavaScript Functionality Requirements

### 1. Sub-Tab Management
- **Sub-Tab Switching**: Switch between "Barang & Jasa" and "Gudang" views
- **State Management**: Maintain filter and search states between sub-tabs
- **Content Loading**: Dynamic loading of tab-specific content
- **URL Management**: Update URL based on active sub-tab

### 2. Product Import/Export
- **Import Options**: Support for various import formats and operations
  - Produk Baru: New product creation
  - Update Produk: Existing product updates
  - Produk Bundle: Bundle and composite product management
- **Export Options**: Multiple export formats and data selections
  - Semua Produk: Complete product export
  - Produk Bundle & Komposisi: Bundle-specific export
- **File Validation**: Format and data validation for imports
- **Progress Tracking**: Import/export progress monitoring

### 3. Advanced Filtering System
- **Kata Kunci**: Keyword search across multiple fields
- **Tipe Produk**: Product type filtering with dropdown selection
- **Kategori Produk**: Category-based filtering with dropdown selection
- **Checkbox Filters**: Multiple selection options
  - Mencangkup semua: Include all products
  - Salah satu: Include any matching criteria
  - Tampilkan arsip: Show archived products
- **Filter Persistence**: Save and restore filter states
- **Real-time Filtering**: Live updates as filters are applied

### 4. Stock Management
- **Stock Level Monitoring**: Real-time stock level updates
- **Low Stock Alerts**: Automatic warnings for products below minimum levels
- **Stock Calculation**: Available stock vs. allocated stock tracking
- **Bulk Stock Operations**: Mass stock updates and adjustments

### 5. Product Management Actions
- **Tindakan Dropdown**: Product management operations
- **Category Management**: Product categorization and organization
- **Bulk Operations**: Mass product updates and modifications
- **Product Detail Navigation**: Quick access to detailed product information

## Data Context
- **Business Flow**: Product Management → Inventory Control → Transaction Processing
- **Purpose**: Comprehensive product and service management with real-time stock tracking
- **Integration**: Links to all transaction modules, warehouse management, and accounting systems
- **Analytics**: Foundation for inventory analysis, pricing management, and stock optimization

## Development Notes
- **Performance**: Efficient handling of large product datasets with pagination and lazy loading
- **Real-time Data**: Live stock level updates and inventory tracking
- **Mobile Responsive**: Touch-friendly interface for mobile inventory management
- **Export Capabilities**: Multiple export formats for reporting and data analysis
- **Integration Points**: Seamless navigation to transaction creation and inventory management

## Future Considerations
This product list page will enable:
- **Advanced Analytics**: Product performance analysis and sales tracking
- **Automated Reordering**: Automatic purchase order generation for low stock items
- **Price Management**: Dynamic pricing and margin analysis
- **Bundle Management**: Complex product bundles and composite item handling
- **Warehouse Integration**: Multi-location inventory management and transfers
- **API Integration**: Real-time synchronization with external inventory and ERP systems

## Technical Implementation
- **Framework**: Laravel Blade templates with advanced table functionality
- **JavaScript**: jQuery with DataTables for complex table operations and real-time updates
- **Styling**: Bootstrap + TailwindCSS with responsive design
- **Data Loading**: Efficient API endpoints for product data retrieval with pagination
- **Caching**: Strategic caching of product data for improved performance
- **Security**: Role-based access control for product management operations

## Special Features & Business Logic

### Integration Notes (Based on Additional Information)
**Menu Produk - Master Data Produk**:

**1. Tujuan**:
Menu Produk digunakan untuk memanage seluruh data produk/barang yang digunakan dalam operasional perusahaan, baik untuk dijual, dibeli, maupun digunakan dalam proses produksi, secara tersusun dalam satu master data.

**2. Fitur Utama**:
- **Tambah Produk**: User dapat menambahkan produk atau jasa baru melalui form input manual. Field disesuaikan berdasarkan jenis (Barang atau Jasa).
- **Gudang**: Menampilkan daftar gudang yang tersedia, masing-masing produk dapat dilihatkan dan dikelola berdasarkan lokasi gudang tertentu.
- **Daftar Transfer Gudang**: Memfasilitasi proses transfer yang dapat mencatat, mengelola, dan melacak transfer barang dari satu gudang ke gudang lain.
- **Impor dan Ekspor**: User dapat mengimpor dan mengekspor data produk dalam format Excel/CSV. Cocok untuk input massal atau pencadangan data.
- **Filter**: Memungkinkan pencarian dan penyaringan berdasarkan kategori produk, jenis (barang/jasa), stok, status aktif/arsip, dan kata kunci (nama, kode, dll).
- **Atur Kategori Produk**: User dapat menambahkan dan mengelola kategori produk untuk memudahkan pengelompokan, analisa, dan pelaporan produk.

**Tindakan Dropdown Actions**:
- **Produk - Tambah Produk Baru**: Create new products
- **Gudang - Tambah Gudang Baru**: Create new warehouses  
- **Gudang - Transfer Gudang**: Manage warehouse transfers

**Clickable Elements**:
- **Stok Tersedia**: Menampilkan laporan jumlah stok saat ini yang masih tersedia untuk dijual atau digunakan.
- **Stok Segera Habis**: Menampilkan laporan produk yang jumlah stoknya berada di bawah ambang batas minimum.
- **Stok Habis**: Menampilkan laporan daftar produk yang tidak memiliki stok tersisa.
- **Gudang**: Lokasi fisik tempat penyimpanan stok produk.
- **Nama Produk**: Nama unik atau deskripsi produk.
- **Nama Gudang**: Nama dari gudang tempat stok disimpan.

### Enhanced Product Management
- **Category Management**: Comprehensive product categorization system
- **Stock Monitoring**: Real-time stock level tracking with alerts
- **Warehouse Integration**: Multi-location inventory management
- **Import/Export**: Bulk data management capabilities
- **Filter System**: Advanced search and filtering options

### Stock Management Features
- **Stock Levels**: Available, allocated, and minimum threshold tracking
- **Low Stock Alerts**: Automatic notifications for inventory replenishment
- **Stock Transfers**: Warehouse-to-warehouse transfer management
- **Price Tracking**: Multiple price points including average, purchase, and selling prices

### Business Rules Implementation
- **Stock Calculations**: Real-time available vs. total stock calculations
- **Minimum Thresholds**: Configurable low stock warning levels
- **Category Organization**: Hierarchical product categorization
- **Pricing Management**: Multiple price tracking for cost and profitability analysis

### Performance Optimization
- **Efficient Queries**: Optimized database queries for large product datasets
- **Pagination Strategy**: Smart pagination for handling extensive product catalogs
- **Caching**: Strategic caching of frequently accessed product data
- **Real-time Updates**: Live inventory level updates across multiple users