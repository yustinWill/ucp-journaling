# Contact Detail - Transaction Tab - Visual Layout

## ASCII Layout Representation

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ Header                                                                                                      │
│ Menu                                                                                                        │
│                                                                                                             │
│    Informasi Kontak                                                      ┌─────────────────────────────┐   │
│                                                                           │ Kelola Kontak               │   │
│    ┌─────────────┐ ┌─────────────┐ ┌─────────────┐                       │ Gabungkan Kontak            │   │
│    │   Profil    │ │  Transaksi  │ │ Credit Memo │                       │ Arsipkan Kontak             │   │
│    │ [INACTIVE]  │ │  [ACTIVE]   │ │ [DISABLED]  │                       └─────────────────────────────┘   │
│    │   #e9ecef   │ │   #28a745   │ │   #e9ecef   │                                                         │
│    └─────────────┘ └─────────────┘ └─────────────┘                                                         │
│                                                                                                             │
│    PT Maju Jaya Sentosa                                                                                    │
│                                                                                                             │
│    Tipe Kontak: ┌─────────────┐     Grup: ┌─────────────────────────────────┐                            │
│                 │ Pelanggan   │           │                                 │                            │
│                 │ [#ff8c00]   │           │                                 │                            │
│                 └─────────────┘           └─────────────────────────────────┘                            │
│                                                                                                             │
│    ┌─────────────────────────────┐ ┌─────────────────────────────┐ ┌─────────────────────────────┐       │
│    │ Transaksi Bulan Ditagih     │ │ Total                       │ │ Piutang Pengiriman          │       │
│    │ 5                           │ │ Rp 5                        │ │ Rp 0                        │       │
│    │ [#dc3545]                   │ │ [#dc3545]                   │ │ [#28a745]                   │       │
│    └─────────────────────────────┘ └─────────────────────────────┘ └─────────────────────────────┘       │
│                                                                                                             │
│    List History Transaksi                                                  ┌─────────────────────────┐   │
│                                                                             │ Tambah Faktur Penjualan │   │
│    ┌─────────────────────────────────────────────────────────────────────┐ └─────────────────────────┘   │
│    │ Tanggal Transaksi │ No. Transaksi        │ Tanggal Jatuh Tempo │ Status │ Total               │ │
│    │──────────────────────────────────────────────────────────────────────────────────────────────│ │
│    │ 12/12/2024        │ Sales Invoice #100002 │ 2/12/2024           │ Paid   │ Rp 5,000,000.00    │ │
│    │ 8/12/2024         │ Purchase Order #10001 │ 3/1/2025            │ Open   │ Rp 3,000,000.00    │ │
│    └─────────────────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                                             │
│    Displaying 2 of 10 entries                                                                             │
│                                                                                                             │
└─────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

## Grid-Based Layout

```
┌─ HEADER SECTION ─────────────────────────────────────────────────────────────────────────┐
│ ┌─ COL 8 ─────────────────────────────────────┐ ┌─ COL 4 ─────────────────────────────┐ │
│ │ Informasi Kontak                            │ │ ┌─ Action Panel ─────────────────┐ │ │
│ │                                             │ │ │ Kelola Kontak                  │ │ │
│ │ ┌─ Tabs ──────────────────────────────────┐ │ │ │ Gabungkan Kontak               │ │ │
│ │ │ [Profil] [Transaksi] [Credit Memo]      │ │ │ │ Arsipkan Kontak                │ │ │
│ │ │ #e9ecef  #28a745    #e9ecef (disabled)  │ │ │ └───────────────────────────────┘ │ │
│ │ └─────────────────────────────────────────┘ │ └─────────────────────────────────────┘ │
│ └─────────────────────────────────────────────┘                                         │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ CONTACT TITLE ──────────────────────────────────────────────────────────────────────────┐
│ PT Maju Jaya Sentosa (Large Header Text)                                                │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ CONTACT TYPE & GROUP ───────────────────────────────────────────────────────────────────┐
│ ┌─ COL 3 ─────────────────────┐    ┌─ COL 9 ─────────────────────────────────────────┐  │
│ │ Tipe Kontak:                │    │ Grup:                                           │  │
│ │ [Pelanggan] (#ff8c00 badge) │    │ [Text Input Field]                              │  │
│ └─────────────────────────────┘    └─────────────────────────────────────────────────┘  │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ TRANSACTION SUMMARY CARDS ──────────────────────────────────────────────────────────────┐
│ ┌─ COL 4 ─────────────────────┐ ┌─ COL 4 ─────────────────────┐ ┌─ COL 4 ──────────────┐ │
│ │ Transaksi Bulan Ditagih     │ │ Total                       │ │ Piutang Pengiriman   │ │
│ │ 5                           │ │ Rp 5                        │ │ Rp 0                 │ │
│ │ [Red Background #dc3545]    │ │ [Red Background #dc3545]    │ │ [Green Bg #28a745]   │ │
│ └─────────────────────────────┘ └─────────────────────────────┘ └──────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ TRANSACTION TABLE SECTION ──────────────────────────────────────────────────────────────┐
│ ┌─ TABLE HEADER (COL 10) ────────────────────────────────┐ ┌─ ACTION BUTTON (COL 2) ─┐ │
│ │ List History Transaksi                                 │ │ Tambah Faktur Penjualan │ │
│ └────────────────────────────────────────────────────────┘ └─────────────────────────┘ │
│                                                                                          │
│ ┌─ TRANSACTION TABLE (COL 12) ────────────────────────────────────────────────────────┐ │
│ │ ┌─ 20% ─────────┐ ┌─ 25% ──────────┐ ┌─ 20% ─────────┐ ┌─ 15% ──┐ ┌─ 20% ─────┐ │ │
│ │ │ Tanggal       │ │ No. Transaksi  │ │ Tanggal Jatuh │ │ Status │ │ Total      │ │ │
│ │ │ Transaksi     │ │                │ │ Tempo         │ │        │ │            │ │ │
│ │ ├───────────────┤ ├────────────────┤ ├───────────────┤ ├────────┤ ├───────────┤ │ │
│ │ │ 12/12/2024    │ │ Sales Invoice  │ │ 2/12/2024     │ │ Paid   │ │ Rp 5,000k │ │ │
│ │ │               │ │ #100002        │ │               │ │        │ │           │ │ │
│ │ ├───────────────┤ ├────────────────┤ ├───────────────┤ ├────────┤ ├───────────┤ │ │
│ │ │ 8/12/2024     │ │ Purchase Order │ │ 3/1/2025      │ │ Open   │ │ Rp 3,000k │ │ │
│ │ │               │ │ #10001         │ │               │ │        │ │           │ │ │
│ │ └───────────────┘ └────────────────┘ └───────────────┘ └────────┘ └───────────┘ │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ PAGINATION (COL 12) ───────────────────────────────────────────────────────────────┐ │
│ │ Displaying 2 of 10 entries                                                          │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘
```

## Responsive Breakpoints

### Desktop (12 columns)
```
Header: [Content - 8 cols] [Actions - 4 cols]
Title: [Company Name - 12 cols]
Type/Group: [Type - 3 cols] [Group - 9 cols]
Summary Cards: [Card 1 - 4 cols] [Card 2 - 4 cols] [Card 3 - 4 cols]
Table Header: [Title - 10 cols] [Action Button - 2 cols]
Transaction Table: [Full width - 12 cols]
Pagination: [Full width - 12 cols]
```

### Tablet (8 columns)
```
Header: [Content - 8 cols]
Actions: [Buttons - 8 cols stacked]
Title: [Company Name - 8 cols]
Type/Group: [Type - 4 cols] [Group - 4 cols]
Summary Cards: [Card 1 - 8 cols] [Card 2 - 8 cols] [Card 3 - 8 cols] stacked
Table: [Full width - 8 cols] with horizontal scroll
```

### Mobile (4 columns)
```
All sections: [Full width - 4 cols]
Stacked vertically
Summary cards: Full width stacked
Table: Horizontal scroll or card layout
Action button: Full width
```

## Key Layout Features

### **Active Tab State:**
- **Transaksi Tab**: Green background (#28a745) indicating active state
- **Other Tabs**: Gray background (#e9ecef) for inactive states
- **Tab Content**: Dynamic transaction-specific content loaded
- **Navigation**: Maintains tab state during interactions

### **Transaction Summary Cards:**
- **Card 1**: "Transaksi Bulan Ditagih" - 5 transactions (Red #dc3545)
- **Card 2**: "Total" - Rp 5 total amount (Red #dc3545)  
- **Card 3**: "Piutang Pengiriman" - Rp 0 outstanding (Green #28a745)
- **Color Coding**: Red for amounts due, Green for positive status
- **Responsive**: Three-column layout on desktop, stacked on mobile

### **Transaction History Table:**
- **5-Column Layout**: Date, Transaction Number, Due Date, Status, Total
- **Clickable Rows**: Transaction numbers link to detail views
- **Status Badges**: Color-coded status indicators (Paid, Open, Overdue)
- **Currency Formatting**: Indonesian Rupiah format with proper separators
- **Sorting**: Clickable column headers for data sorting

### **Action Integration:**
- **Primary Action**: "Tambah Faktur Penjualan" button (blue background)
- **Pre-filled Data**: Contact information auto-populated in new transactions
- **Quick Access**: Streamlined transaction creation workflow
- **Contact Actions**: Consistent with other tabs (Kelola, Gabungkan, Arsipkan)

### **Data Display Features:**
- **Transaction Types**: Mixed sales and purchase transactions
- **Date Formatting**: DD/MM/YYYY format for Indonesian standards
- **Reference Numbers**: Module-specific numbering (SI #100002, PO #10001)
- **Status Tracking**: Real-time transaction status updates
- **Pagination**: "Displaying 2 of 10 entries" with navigation controls

### **Interactive Elements:**
- **Row Hover**: Visual feedback on table row hover
- **Clickable Links**: Transaction numbers navigate to details
- **Filter Options**: Hidden filters for date range, status, transaction type
- **Search Function**: Global search across transaction data
- **Export Options**: Download transaction data in various formats

### **Business Logic Integration:**
- **Contact-Specific**: All transactions filtered by selected contact
- **Mixed Relationships**: Support for customers who are also suppliers
- **Status Tracking**: Real-time updates for payment and delivery status
- **Relationship Analytics**: Transaction pattern analysis capabilities

### **Performance Optimizations:**
- **Lazy Loading**: Load transaction data only when tab is activated
- **Pagination**: Efficient handling of large transaction datasets
- **Caching**: Strategic caching of frequently accessed transaction data
- **Real-time Updates**: Live status updates without full page refresh

### **Accessibility Features:**
- **Keyboard Navigation**: Full keyboard support for table navigation
- **Screen Reader**: Proper ARIA labels for transaction data
- **Color Contrast**: Sufficient contrast for status indicators and text
- **Focus Management**: Clear focus indicators for interactive elements

This transaction tab layout provides comprehensive visibility into contact-related business activities while maintaining clean organization and efficient navigation. The design supports both viewing transaction history and creating new transactions with pre-filled contact information.