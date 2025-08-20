# Expense List Page (Biaya Pengeluaran) - Technical Specification

## Overview
This document provides comprehensive technical specifications for creating the Expense List page (Biaya Pengeluaran) in the financial management module. This page displays and manages all business expense transactions with comprehensive filtering, status tracking, and expense analysis capabilities.

## Form Purpose
The Expense List page is specifically designed for:
- Displaying comprehensive expense transaction history with filtering and status tracking
- Managing expense categories and payment status monitoring
- Supporting expense analysis and financial reporting capabilities
- Providing advanced filtering capabilities for expense search and categorization
- Enabling expense management operations including creation, editing, and approval
- Supporting expense monitoring with unpaid, overdue, and total expense tracking
- Integrating with accounting systems and financial reporting modules

## UI Structure Requirements

### 1. Page Header
- **Title**: "Biaya Pengeluaran"
- **Layout**: Full-width header with action button
- **Page ID**: `expenseListPage`

### 2. Action Panel (Top Right)
- **+ Buat Biaya Baru**: Primary action button (blue background)
  - Create new expense transaction
  - Positioned in top-right corner of content area

### 3. Expense Summary Cards
Dashboard overview of expense status and totals:

#### Summary Cards Layout:
- **Total Biaya Bulan Ini**: Current month expenses
  - Color: Orange/warning background (#ff8c00)
  - Total: Rp 0 (sample data)
  - Current month expense tracking
- **Biaya 30 Hari Terakhir**: Last 30 days expenses
  - Color: Orange/warning background (#fd7e14)
  - Total: Rp 0 (sample data)
  - Recent expense period tracking
- **Biaya Belum Dibayar**: Unpaid expenses
  - Color: Default/neutral background
  - Total: Rp 0 (sample data)
  - Outstanding payment tracking

### 4. Expense Filter and Search
Search and filtering functionality:

#### Filter Elements:
- **Semua Status**: Status filter dropdown
  - Filter expenses by payment status
  - Default: All statuses view
- **Cari Transaksi**: Search input field
  - Placeholder: "Cari transaksi..."
  - Real-time search capability across expense data

### 5. Expense Data Table
Comprehensive expense listing with detailed transaction information:

#### Table Columns:
- **Tanggal** (Date)
  - Sample: "17/04/2025"
  - Transaction date in DD/MM/YYYY format
- **Nomor Biaya** (Expense Number)
  - Sample: "EXP/17/04/2025"
  - Unique expense identifiers
  - Clickable links to expense detail pages
- **Nama Biaya** (Expense Name)
  - Expense description or title
  - Brief expense identification
- **Penerima** (Recipient)
  - Payee or expense recipient information
  - Vendor, supplier, or service provider details
- **Status** (Payment Status)
  - Sample: "Paid", "Closed"
  - Payment and processing status tracking
- **Sisa Tagihan** (Outstanding Balance)
  - Remaining unpaid amount
  - Currency format: Rp X,XXX,XXX
- **Total** (Total Amount)
  - Complete expense amount
  - Currency format: Rp X,XXX,XXX
- **Tags** (Expense Tags)
  - Categorization and labeling
  - Multiple tags for expense classification

#### Table Features:
- **Pagination**: "Showing 5 of 10 entries"
- **Sorting**: Clickable column headers for data sorting
- **Row Selection**: Support for bulk operations
- **Status Indicators**: Color-coded status badges
- **Responsive Design**: Horizontal scroll for smaller screens

## JavaScript Functionality Requirements

### 1. Expense Management
- **Expense Creation**: Navigate to expense creation form
- **Status Tracking**: Real-time expense status updates
- **Payment Processing**: Track payment status and outstanding balances
- **Bulk Operations**: Mass expense updates and approvals

### 2. Advanced Filtering System
- **Status Filtering**: Filter by payment status (Paid, Pending, Overdue, etc.)
- **Date Range Filtering**: Filter expenses by date periods
- **Tag Filtering**: Filter by expense categories and tags
- **Amount Range**: Filter by expense amount ranges
- **Search Function**: Text search across expense descriptions and recipients
- **Filter Persistence**: Save and restore filter states

### 3. Expense Analytics
- **Period Analysis**: Monthly and rolling period expense tracking
- **Status Monitoring**: Track paid vs. unpaid expenses
- **Category Analysis**: Expense breakdown by tags and categories
- **Trend Tracking**: Expense patterns and financial insights

### 4. Financial Integration
- **Accounting Integration**: Link expenses to chart of accounts
- **Payment Tracking**: Monitor payment status and due dates
- **Reporting**: Generate expense reports and financial summaries
- **Budget Monitoring**: Track expenses against budget allocations

## Data Context
- **Business Flow**: Expense Management → Payment Processing → Financial Reporting
- **Purpose**: Comprehensive expense tracking and financial management
- **Integration**: Links to accounting systems, payment processing, and financial reporting
- **Analytics**: Foundation for expense analysis, budget management, and financial planning

## Development Notes
- **Performance**: Efficient handling of large expense datasets with pagination and filtering
- **Real-time Data**: Live expense status updates and payment tracking
- **Mobile Responsive**: Touch-friendly interface for mobile expense management
- **Financial Accuracy**: Precise currency handling and calculation
- **Integration Points**: Seamless connection with accounting and payment systems

## Future Considerations
This expense list page will enable:
- **Advanced Analytics**: Expense trend analysis and budget performance tracking
- **Automated Approvals**: Workflow-based expense approval processes
- **Receipt Management**: Digital receipt warehouse and document management
- **Mobile Expense Entry**: Mobile app integration for field expense entry
- **Integration Enhancement**: Advanced ERP and accounting system integration
- **Predictive Analytics**: Expense forecasting and budget optimization

## Technical Implementation
- **Framework**: Laravel Blade templates with advanced filtering functionality
- **JavaScript**: jQuery with DataTables for complex table operations and real-time updates
- **Styling**: Bootstrap + TailwindCSS with responsive design
- **Data Loading**: Efficient API endpoints for expense data retrieval with filtering
- **Caching**: Strategic caching of expense data for improved performance
- **Security**: Role-based access control for expense management operations

## Special Features & Business Logic

### Integration Notes (Based on Right Panel Information)
**Detail**:
Menampilkan daftar transaksi biaya dengan filter, status, dan rincian yang jelas untuk mempermudah pengguna melihat, mencari, dan mengelola biaya perusahaan.

**Field pada List Biaya**:
- **Tanggal**: Date (dd/mm/yyyy) - Tanggal transaksi biaya terjadi atau dicatat
- **Nomor Biaya**: String - Nomor unik biaya (contoh: EXP/17/04/2025)
- **Nama Biaya**: String - Nama atau deskripsi biaya (misal: Listrik, Internet, dll)
- **Penerima**: String - Nama pihak penerima pembayaran biaya (misal: vendor, supplier)
- **Status**: Enum, Closed - Status pembayaran biaya saat ini
- **Sisa Tagihan**: Currency (Rp) - Jumlah tagihan yang belum dibayar jika ada
- **Total**: Currency (Rp) - Jumlah total biaya untuk transaksi tersebut
- **Tags**: Array of String - Label/tag tambahan untuk mengelompokkan atau memberi konteks transaksi (misal: Operasional, Marketing)

**Semua Status Filter**:
- Paid
- Closed

**Clickable Elements**:
- **total_biaya_bulan_ini**: Filter list ke transaksi biaya bulan berjalan
- **biaya_30_hari_terakhir**: Filter list ke transaksi biaya 30 hari terakhir
- **biaya_belum_dibayar**: Filter list ke transaksi biaya yang belum dibayar

**+ Buat Biaya Baru dengan aksi menuju halaman form tambah biaya baru**:
- **Field Name**: buat_biaya_baru
- **Aksi Saat Diklik**: Navigasi ke halaman Form untuk menginput data transaksi biaya baru
- **Menampilkan**: Formulir input untuk memasukkan detail expense baru

### Enhanced Expense Management
- **Status Tracking**: Comprehensive expense status monitoring (Paid, Closed, Pending)
- **Financial Overview**: Monthly and rolling period expense summaries
- **Category Management**: Tag-based expense categorization and organization
- **Payment Monitoring**: Outstanding balance tracking and payment status

### Expense Analysis Features
- **Period Tracking**: Current month and 30-day rolling expense analysis
- **Payment Status**: Real-time tracking of paid vs. unpaid expenses
- **Category Analysis**: Expense breakdown by operational categories
- **Financial Planning**: Budget tracking and expense forecasting

### Business Rules Implementation
- **Status Management**: Automated status updates based on payment processing
- **Category Organization**: Flexible tagging system for expense classification
- **Payment Tracking**: Real-time outstanding balance calculations
- **Period Analysis**: Automated period-based expense aggregation

### Financial Integration
- **Accounting System**: Integration with chart of accounts and financial reporting
- **Payment Processing**: Link expenses to payment transactions and bank records
- **Budget Management**: Track expenses against allocated budgets
- **Tax Management**: Support for tax-deductible expense tracking and reporting

### Performance Optimization
- **Efficient Queries**: Optimized database queries for large expense datasets
- **Real-time Updates**: Live expense status and payment updates
- **Caching Strategy**: Strategic caching of frequently accessed expense data
- **Pagination**: Efficient handling of extensive expense transaction histories