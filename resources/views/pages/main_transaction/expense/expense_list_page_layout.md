# Expense List Page (Biaya Pengeluaran) - Visual Layout

## ASCII Layout Representation

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ Header                                                                                                      │
│ Menu                                                                                                        │
│                                                                                                             │
│    Biaya Pengeluaran                                                  ┌─────────────────────────────────┐ │
│                                                                        │ + Buat Biaya Baru              │ │
│                                                                        │ [Blue Background #007bff]       │ │
│                                                                        └─────────────────────────────────┘ │
│                                                                                                             │
│    ┌─────────────────────────────┐ ┌─────────────────────────────┐ ┌─────────────────────────────┐       │
│    │ Total Biaya Bulan Ini       │ │ Biaya 30 Hari Terakhir      │ │ Biaya Belum Dibayar         │       │
│    │ Total                       │ │ Total                       │ │ Total                       │       │
│    │ Rp 0                        │ │ Rp 0                        │ │ Rp 0                        │       │
│    │ [Orange Background #ff8c00] │ │ [Orange Background #fd7e14] │ │ [Default Background]        │       │
│    └─────────────────────────────┘ └─────────────────────────────┘ └─────────────────────────────┘       │
│                                                                                                             │
│    ┌─────────────────────────────┐    ┌─────────────────────────────┐                                    │
│    │ Semua Status            ▼   │    │ Cari Transaksi          🔍  │                                    │
│    │                             │    │                             │                                    │
│    └─────────────────────────────┘    └─────────────────────────────┘                                    │
│                                                                                                             │
│    ┌─────────────────────────────────────────────────────────────────────────────────────────────────────┐ │
│    │ Tanggal     │ Nomor Biaya      │ Nama Biaya │ Penerima │ Status │ Sisa Tagihan │ Total    │ Tags   │ │
│    │─────────────────────────────────────────────────────────────────────────────────────────────────────│ │
│    │ 17/04/2025  │ EXP/17/04/2025   │            │          │ Paid   │              │          │        │ │
│    │─────────────────────────────────────────────────────────────────────────────────────────────────────│ │
│    │             │                  │            │          │ Closed │              │          │        │ │
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
│ │ Biaya Pengeluaran                           │ │ ┌─ Action Button ─────────────────┐ │ │
│ │                                             │ │ │ + Buat Biaya Baru              │ │ │
│ │                                             │ │ │ [Blue Background #007bff]       │ │ │
│ │                                             │ │ └─────────────────────────────────┘ │ │
│ └─────────────────────────────────────────────┘ └─────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ EXPENSE SUMMARY CARDS ──────────────────────────────────────────────────────────────────┐
│ ┌─ COL 4 ─────────────────────┐ ┌─ COL 4 ─────────────────────┐ ┌─ COL 4 ──────────────┐ │
│ │ Total Biaya Bulan Ini       │ │ Biaya 30 Hari Terakhir      │ │ Biaya Belum Dibayar  │ │
│ │ Total: Rp 0                 │ │ Total: Rp 0                 │ │ Total: Rp 0          │ │
│ │ [Orange Background #ff8c00] │ │ [Orange Background #fd7e14] │ │ [Default Background] │ │
│ └─────────────────────────────┘ └─────────────────────────────┘ └──────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ FILTER AND SEARCH ──────────────────────────────────────────────────────────────────────┐
│ ┌─ COL 6 ─────────────────────┐    ┌─ COL 6 ─────────────────────┐                      │
│ │ Semua Status            ▼   │    │ Cari Transaksi          🔍  │                      │
│ │ [Status Filter Dropdown]    │    │ [Search Input Field]        │                      │
│ └─────────────────────────────┘    └─────────────────────────────┘                      │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ EXPENSE TABLE SECTION ──────────────────────────────────────────────────────────────────┐
│ ┌─ EXPENSE TABLE (COL 12) ────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ 12% ─────┐ ┌─ 15% ─────┐ ┌─ 12% ─┐ ┌─ 12% ─┐ ┌─ 10% ─┐ ┌─ 12% ─┐ ┌─ 12% ─┐ ┌─ 15% ┐ │ │
│ │ │ Tanggal   │ │ Nomor     │ │ Nama  │ │ Penerima│ │ Status│ │ Sisa   │ │ Total │ │ Tags │ │ │
│ │ │           │ │ Biaya     │ │ Biaya │ │         │ │       │ │ Tagihan│ │       │ │      │ │ │
│ │ ├───────────┤ ├───────────┤ ├───────┤ ├────────┤ ├───────┤ ├────────┤ ├───────┤ ├──────┤ │ │
│ │ │17/04/2025 │ │EXP/17/04/ │ │       │ │        │ │ Paid  │ │        │ │       │ │      │ │ │
│ │ │           │ │2025       │ │       │ │        │ │       │ │        │ │       │ │      │ │ │
│ │ ├───────────┤ ├───────────┤ ├───────┤ ├────────┤ ├───────┤ ├────────┤ ├───────┤ ├──────┤ │ │
│ │ │           │ │           │ │       │ │        │ │Closed │ │        │ │       │ │      │ │ │
│ │ │           │ │           │ │       │ │        │ │       │ │        │ │       │ │      │ │ │
│ │ └───────────┘ └───────────┘ └───────┘ └────────┘ └───────┘ └────────┘ └───────┘ └──────┘ │ │
│ └──────────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ PAGINATION (COL 12) ───────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 6 ─────────────────────┐    ┌─ COL 6 ─────────────────────┐                  │ │
│ │ │ Showing 5 of 10 entries     │    │ < 1 2 3 ... 10 >            │                  │ │
│ │ └─────────────────────────────┘    └─────────────────────────────┘                  │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘
```

## Status Filter Dropdown

```
┌─────────────────────────────┐
│ Semua Status                │
│ Paid                        │
│ Closed                      │
└─────────────────────────────┘
```

## Responsive Breakpoints

### Desktop (12 columns)
```
Header: [Title - 8 cols] [Action Button - 4 cols]
Summary Cards: [Card 1 - 4 cols] [Card 2 - 4 cols] [Card 3 - 4 cols]
Filter/Search: [Status Filter - 6 cols] [Search - 6 cols]
Table: [Full width - 12 cols]
Pagination: [Info - 6 cols] [Controls - 6 cols]
```

### Tablet (8 columns)
```
Header: [Title - 6 cols] [Action Button - 2 cols]
Summary Cards: [Card 1 - 4 cols] [Card 2 - 4 cols] [Card 3 - 8 cols stacked]
Filter/Search: [Status Filter - 4 cols] [Search - 4 cols]
Table: [Horizontal scroll - 8 cols]
```

### Mobile (4 columns)
```
All sections: [Full width - 4 cols]
Summary cards: Stacked vertically
Filter/Search: Full width stacked
Table: Horizontal scroll or card layout
Action button: Full width
```

## Key Layout Features

### **Expense List Specific Elements:**
- **Page Title**: "Biaya Pengeluaran" clearly identifying expense management
- **Action Button**: "+ Buat Biaya Baru" for creating new expenses (blue background)
- **Financial Focus**: Emphasis on expense tracking and payment status monitoring
- **Comprehensive Data**: Complete expense information with status and financial details

### **Expense Summary Dashboard:**
- **Total Biaya Bulan Ini**: Current month expenses (orange background #ff8c00)
- **Biaya 30 Hari Terakhir**: Last 30 days expenses (orange background #fd7e14)
- **Biaya Belum Dibayar**: Unpaid expenses (default background)
- **Clickable Cards**: Link to filtered expense views

### **Filter and Search System:**
- **Semua Status**: Status filter dropdown with options (Paid, Closed)
- **Cari Transaksi**: Search input field with search icon
- **Real-time Filtering**: Live updates as filters are applied
- **Status Management**: Filter by payment and processing status

### **Comprehensive Expense Table:**
- **8-Column Layout**: Complete expense transaction information
- **Date Information**: Transaction dates in DD/MM/YYYY format
- **Expense Identification**: Unique expense numbers (EXP/17/04/2025)
- **Financial Data**: Outstanding balances and total amounts
- **Status Tracking**: Payment status with color-coded indicators
- **Categorization**: Tags for expense classification and organization

### **Sample Expense Data:**
- **Date**: 17/04/2025 (DD/MM/YYYY format)
- **Expense Number**: EXP/17/04/2025 (standardized numbering)
- **Status Examples**: "Paid", "Closed"
- **Financial Tracking**: Outstanding balances and total amounts
- **Tag System**: Category-based expense organization

### **Interactive Elements:**
- **Clickable Cards**: Summary cards filter expense list by period and status
- **Status Filtering**: Dropdown filter for payment status
- **Search Function**: Real-time expense search capability
- **Table Sorting**: Sortable columns for data organization
- **Pagination**: Navigate through large expense datasets

### **Business Logic Integration:**
- **Period Tracking**: Monthly and rolling 30-day expense monitoring
- **Payment Status**: Real-time tracking of paid vs. unpaid expenses
- **Outstanding Balance**: Track remaining payments and financial obligations
- **Category Management**: Tag-based expense classification and reporting

### **Data Display Features:**
- **Transaction Details**: Complete expense transaction information
- **Financial Metrics**: Outstanding balances and total expense amounts
- **Status Indicators**: Clear payment and processing status display
- **Currency Formatting**: Indonesian Rupiah format (Rp X,XXX,XXX)
- **Pagination Info**: "Showing 5 of 10 entries" with navigation controls

### **Visual Design Elements:**
- **Color-Coded Cards**: Orange backgrounds for expense summaries, default for unpaid
- **Status Badges**: Color-coded status indicators for quick identification
- **Action Button**: Prominent blue button for expense creation
- **Icon Integration**: 🔍 search icon for visual clarity
- **Responsive Table**: Horizontal scroll support for smaller screens

### **Performance Features:**
- **Efficient Layout**: Optimized for expense data display and management
- **Quick Actions**: Streamlined access to expense creation and filtering
- **Search Integration**: Built-in expense search and filtering capabilities
- **Pagination Support**: Handle large numbers of expense transactions

### **Financial Management Capabilities:**
- **Expense Tracking**: Comprehensive transaction monitoring and analysis
- **Payment Management**: Track payment status and outstanding obligations
- **Period Analysis**: Monthly and rolling period expense summaries
- **Category Organization**: Tag-based expense classification and reporting

### **Integration Points:**
- **Accounting System**: Link expenses to chart of accounts and financial reporting
- **Payment Processing**: Integration with payment systems and bank records
- **Budget Management**: Track expenses against allocated budgets
- **Reporting**: Generate comprehensive expense reports and financial analysis

This expense list page provides comprehensive expense management functionality while maintaining excellent user experience across all device types. The design supports efficient expense tracking, payment monitoring, and financial analysis essential for effective business expense management.