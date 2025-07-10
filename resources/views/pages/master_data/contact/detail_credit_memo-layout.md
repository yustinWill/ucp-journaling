# Contact Detail - Credit Memo Tab - Visual Layout

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
│    │ [INACTIVE]  │ │ [INACTIVE]  │ │  [ACTIVE]   │                       └─────────────────────────────┘   │
│    │   #e9ecef   │ │   #e9ecef   │ │   #28a745   │                                                         │
│    └─────────────┘ └─────────────┘ └─────────────┘                                                         │
│                                                                                                             │
│    PT Maju Jaya Sentosa                                                                                    │
│                                                                                                             │
│    Tipe Kontak: ┌─────────────┐     Grup: ┌─────────────────────────────────┐                            │
│                 │ Pelanggan   │           │                                 │                            │
│                 │ [#ff8c00]   │           │                                 │                            │
│                 └─────────────┘           └─────────────────────────────────┘                            │
│                                                                                                             │
│    Tambah Credit Memo: ┌─────────────────────────────┐                                                    │
│                        │ Tambah Credit Memo          │                                                    │
│                        │ [Primary Blue]              │                                                    │
│                        └─────────────────────────────┘                                                    │
│                                                                                                             │
│    List History Credit Memo                                                                               │
│                                                                                                             │
│    ┌─────────────────────────────────────────────────────────────────────────────────────────────────────┐ │
│    │ Tanggal Transaksi │ No. Credit Memo      │ Jumlah Awal     │ Terpakai        │ Sisa Saldo        │ │
│    │──────────────────────────────────────────────────────────────────────────────────────────────────── │ │
│    │ 5/5/2024          │ Credit Memo #100001  │ Rp 5,000,000.00 │ Rp 500,000.00   │ Rp 4,500,000.00  │ │
│    │ [SELECTED ROW - Blue Background #007bff]                                                             │ │
│    │──────────────────────────────────────────────────────────────────────────────────────────────────── │ │
│    │ 5/5/2024          │ Credit Memo #100002  │ Rp 4,000,000.00 │ Rp 4,000,000.00 │ Rp -              │ │
│    │──────────────────────────────────────────────────────────────────────────────────────────────────── │ │
│    │ 5/5/2024          │ Credit Memo #100003  │ Rp 500,000.00   │ Rp 500,000.00   │ Rp -              │ │
│    └─────────────────────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                                             │
│    Showing 3 of 10 entries                                                                                │
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
│ │ │ #e9ecef  #e9ecef    #28a745 (active)    │ │ │ └───────────────────────────────┘ │ │
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

┌─ CREDIT MEMO ACTION ─────────────────────────────────────────────────────────────────────┐
│ ┌─ COL 4 ─────────────────────┐    ┌─ COL 8 ─────────────────────────────────────────┐  │
│ │ Tambah Credit Memo:         │    │                                                 │  │
│ │ ┌─ Action Button ─────────┐ │    │                                                 │  │
│ │ │ Tambah Credit Memo      │ │    │                                                 │  │
│ │ │ [Primary Blue #007bff]  │ │    │                                                 │  │
│ │ └─────────────────────────┘ │    │                                                 │  │
│ └─────────────────────────────┘    └─────────────────────────────────────────────────┘  │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ CREDIT MEMO TABLE SECTION ──────────────────────────────────────────────────────────────┐
│ ┌─ TABLE HEADER (COL 12) ────────────────────────────────────────────────────────────────┐ │
│ │ List History Credit Memo                                                               │ │
│ └────────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ CREDIT MEMO TABLE (COL 12) ─────────────────────────────────────────────────────────┐ │
│ │ ┌─ 20% ─────────┐ ┌─ 25% ──────────┐ ┌─ 20% ─────────┐ ┌─ 15% ──────┐ ┌─ 20% ─────┐ │ │
│ │ │ Tanggal       │ │ No. Credit     │ │ Jumlah Awal   │ │ Terpakai   │ │ Sisa Saldo │ │ │
│ │ │ Transaksi     │ │ Memo           │ │               │ │            │ │            │ │ │
│ │ ├───────────────┤ ├────────────────┤ ├───────────────┤ ├────────────┤ ├───────────┤ │ │
│ │ │ 5/5/2024      │ │ Credit Memo    │ │ Rp 5,000,000  │ │ Rp 500,000 │ │ Rp 4,500k │ │ │
│ │ │ [BLUE BG]     │ │ #100001 [BLUE] │ │ [BLUE BG]     │ │ [BLUE BG]  │ │ [BLUE BG] │ │ │
│ │ ├───────────────┤ ├────────────────┤ ├───────────────┤ ├────────────┤ ├───────────┤ │ │
│ │ │ 5/5/2024      │ │ Credit Memo    │ │ Rp 4,000,000  │ │ Rp 4,000k  │ │ Rp -      │ │ │
│ │ │               │ │ #100002        │ │               │ │            │ │           │ │ │
│ │ ├───────────────┤ ├────────────────┤ ├───────────────┤ ├────────────┤ ├───────────┤ │ │
│ │ │ 5/5/2024      │ │ Credit Memo    │ │ Rp 500,000    │ │ Rp 500,000 │ │ Rp -      │ │ │
│ │ │               │ │ #100003        │ │               │ │            │ │           │ │ │
│ │ └───────────────┘ └────────────────┘ └───────────────┘ └────────────┘ └───────────┘ │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ PAGINATION (COL 12) ───────────────────────────────────────────────────────────────┐ │
│ │ Showing 3 of 10 entries                                                             │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘
```

## Responsive Breakpoints

### Desktop (12 columns)
```
Header: [Content - 8 cols] [Actions - 4 cols]
Title: [Company Name - 12 cols]
Type/Group: [Type - 3 cols] [Group - 9 cols]
Action: [Button Label - 4 cols] [Empty Space - 8 cols]
Table Header: [Title - 12 cols]
Credit Memo Table: [Full width - 12 cols]
Pagination: [Full width - 12 cols]
```

### Tablet (8 columns)
```
Header: [Content - 8 cols]
Actions: [Buttons - 8 cols stacked]
Title: [Company Name - 8 cols]
Type/Group: [Type - 4 cols] [Group - 4 cols]
Action: [Button - 8 cols full width]
Table: [Full width - 8 cols] with horizontal scroll
```

### Mobile (4 columns)
```
All sections: [Full width - 4 cols]
Stacked vertically
Action button: Full width
Table: Horizontal scroll or card layout
Selected row: Touch-friendly selection
```

## Key Layout Features

### **Active Tab State:**
- **Credit Memo Tab**: Green background (#28a745) indicating active state
- **Other Tabs**: Gray background (#e9ecef) for inactive states
- **Tab Content**: Dynamic credit memo-specific content loaded
- **Navigation**: Maintains tab state during credit memo operations

### **Credit Memo Action Button:**
- **"Tambah Credit Memo"**: Primary blue button (#007bff)
- **Pre-filled Data**: Contact information auto-populated in new credit memos
- **Quick Access**: Streamlined credit memo creation workflow
- **Integration**: Links to credit memo creation form with contact context

### **Credit Memo History Table:**
- **5-Column Layout**: Date, Credit Memo Number, Original Amount, Used Amount, Remaining Balance
- **Selected Row**: Blue background highlighting (#007bff) for active selection
- **Clickable Rows**: Credit memo numbers link to detail views
- **Balance Tracking**: Clear display of remaining credit balances
- **Currency Formatting**: Indonesian Rupiah format with proper separators

### **Credit Memo Data Examples:**
- **Credit Memo #100001**: Rp 5,000,000 original, Rp 500,000 used, Rp 4,500,000 remaining
- **Credit Memo #100002**: Rp 4,000,000 original, fully used (Rp -) 
- **Credit Memo #100003**: Rp 500,000 original, fully used (Rp -)
- **Status Indication**: Clear distinction between active and exhausted credit memos

### **Interactive Elements:**
- **Row Selection**: Blue highlighting for selected credit memo row
- **Clickable Links**: Credit memo numbers navigate to detailed views
- **Balance Calculation**: Real-time display of remaining credit balances
- **Status Tracking**: Visual indication of credit memo utilization status

### **Business Logic Integration:**
- **Contact-Specific**: All credit memos filtered by selected contact
- **Balance Management**: Accurate tracking of credit memo usage and remaining balances
- **Application Tracking**: Shows how much credit has been applied to invoices
- **Financial Integration**: Links to accounting and payment systems

### **Data Display Features:**
- **Transaction Types**: Credit memo transactions with detailed amount breakdown
- **Date Formatting**: DD/MM/YYYY format for Indonesian standards
- **Reference Numbers**: Credit memo numbering (CM #100001, #100002, #100003)
- **Amount Tracking**: Original amount, used amount, and remaining balance
- **Pagination**: "Showing 3 of 10 entries" with navigation controls

### **Visual Design Elements:**
- **Row Highlighting**: Blue background (#007bff) for selected row
- **Clear Typography**: Easy-to-read credit memo information
- **Consistent Spacing**: Proper spacing between table elements
- **Color Coding**: Appropriate colors for different credit memo states

### **Performance Optimizations:**
- **Lazy Loading**: Load credit memo data only when tab is activated
- **Real-time Calculations**: Live balance updates as credit memos are applied
- **Efficient Queries**: Optimized database queries for credit memo retrieval
- **Balance Caching**: Strategic caching of credit memo balance calculations

### **Credit