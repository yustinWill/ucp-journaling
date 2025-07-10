# Contact Detail Form - Visual Layout

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
│    │  [ACTIVE]   │ │ [INACTIVE]  │ │ [DISABLED]  │                       └─────────────────────────────┘   │
│    │   #28a745   │ │   #e9ecef   │ │   #e9ecef   │                                                         │
│    └─────────────┘ └─────────────┘ └─────────────┘                                                         │
│                                                                                                             │
│    PT Maju Jaya Sentosa                                                                                    │
│                                                                                                             │
│    Tipe Kontak: ┌─────────────┐     Grup: ┌─────────────────────────────────┐                            │
│                 │ Pelanggan   │           │                                 │                            │
│                 │ [#ff8c00]   │           │                                 │                            │
│                 └─────────────┘           └─────────────────────────────────┘                            │
│                                                                                                             │
│    Info Umum                                                                                               │
│    ─────────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐                              │
│    │ Nama Kontak                     │  │ Identitas                       │                              │
│    │ Anwar                           │  │ KTP                             │                              │
│    └─────────────────────────────────┘  └─────────────────────────────────┘                              │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐                              │
│    │ Nama Perusahaan                 │  │ Nomor Identitas                 │                              │
│    │ PT Maju Jaya Sentosa            │  │ 3175091203940000.00             │                              │
│    └─────────────────────────────────┘  └─────────────────────────────────┘                              │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐                              │
│    │ Email                           │  │ Alamat Penagihan                │                              │
│    │ pelanggan1@example.com          │  │                                 │                              │
│    └─────────────────────────────────┘  └─────────────────────────────────┘                              │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐                              │
│    │ Nomor Handphone                 │  │ Alamat Pengiriman               │                              │
│    │ 081234567890                    │  │ Ciomas, Bogor                   │                              │
│    └─────────────────────────────────┘  └─────────────────────────────────┘                              │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐                              │
│    │ Nomor Telepon                   │  │ Info Lainnya                    │                              │
│    │                                 │  │ -                               │                              │
│    └─────────────────────────────────┘  └─────────────────────────────────┘                              │
│                                                                                                             │
│    ┌─────────────────────────────────┐                                                                    │
│    │ Fax                             │                                                                    │
│    │                                 │                                                                    │
│    └─────────────────────────────────┘                                                                    │
│                                                                                                             │
│    ┌─────────────────────────────────┐                                                                    │
│    │ NPWP                            │                                                                    │
│    │ 01.234.567.8-999.000            │                                                                    │
│    └─────────────────────────────────┘                                                                    │
│                                                                                                             │
│    Info Bank                                                                                               │
│    ─────────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐                              │
│    │ Nama Bank                       │  │ Kantor Cabang                   │                              │
│    │ Bank Central Asia               │  │ Bogor                           │                              │
│    └─────────────────────────────────┘  └─────────────────────────────────┘                              │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐                              │
│    │ Pemegang Akun Bank              │  │ Nomor Rekening                  │                              │
│    │ Anwar                           │  │ 541-002-7891                    │                              │
│    └─────────────────────────────────┘  └─────────────────────────────────┘                              │
│                                                                                                             │
│    Pemetaan Akun                                                                                          │
│    ─────────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    ┌─────────────────────────────────┐                                                                    │
│    │ Akun Hutang                     │                                                                    │
│    │ (2-20100) Hutang                │                                                                    │
│    └─────────────────────────────────┘                                                                    │
│                                                                                                             │
│    ┌─────────────────────────────────┐                                                                    │
│    │ Akun Piutang                    │                                                                    │
│    │ (1-20100) Piutang               │                                                                    │
│    └─────────────────────────────────┘                                                                    │
│                                                                                                             │
│    ┌─────────────────────────────────┐                                                                    │
│    │ Syarat Pembayaran Utama         │                                                                    │
│    │                                 │                                                                    │
│    └─────────────────────────────────┘                                                                    │
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
│ │ │ #28a745  #e9ecef    #e9ecef (disabled)  │ │ │ └───────────────────────────────┘ │ │
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

┌─ INFO UMUM SECTION ──────────────────────────────────────────────────────────────────────┐
│ Info Umum                                                                                │
│ ─────────────────────────────────────────────────────────────────────────────────────────│
│                                                                                          │
│ ┌─ LEFT COLUMN (COL 6) ──────────────────────────┐ ┌─ RIGHT COLUMN (COL 6) ───────────┐ │
│ │ ┌─ Nama Kontak ────────────────────────────────┐ │ │ ┌─ Identitas ─────────────────┐ │ │
│ │ │ Anwar                                        │ │ │ │ KTP                         │ │ │
│ │ └──────────────────────────────────────────────┘ │ │ └─────────────────────────────┘ │ │
│ │                                                  │ │                                 │ │
│ │ ┌─ Nama Perusahaan ────────────────────────────┐ │ │ ┌─ Nomor Identitas ───────────┐ │ │
│ │ │ PT Maju Jaya Sentosa                         │ │ │ │ 3175091203940000.00         │ │ │
│ │ └──────────────────────────────────────────────┘ │ │ └─────────────────────────────┘ │ │
│ │                                                  │ │                                 │ │
│ │ ┌─ Email ──────────────────────────────────────┐ │ │ ┌─ Alamat Penagihan ─────────┐ │ │
│ │ │ pelanggan1@example.com                       │ │ │ │ [Textarea]                  │ │ │
│ │ └──────────────────────────────────────────────┘ │ │ └─────────────────────────────┘ │ │
│ │                                                  │ │                                 │ │
│ │ ┌─ Nomor Handphone ────────────────────────────┐ │ │ ┌─ Alamat Pengiriman ────────┐ │ │
│ │ │ 081234567890                                 │ │ │ │ Ciomas, Bogor               │ │ │
│ │ └──────────────────────────────────────────────┘ │ │ └─────────────────────────────┘ │ │
│ │                                                  │ │                                 │ │
│ │ ┌─ Nomor Telepon ──────────────────────────────┐ │ │ ┌─ Info Lainnya ─────────────┐ │ │
│ │ │                                              │ │ │ │ -                           │ │ │
│ │ └──────────────────────────────────────────────┘ │ │ └─────────────────────────────┘ │ │
│ │                                                  │ │                                 │ │
│ │ ┌─ Fax ────────────────────────────────────────┐ │ │                                 │ │
│ │ │                                              │ │ │                                 │ │
│ │ └──────────────────────────────────────────────┘ │ │                                 │ │
│ │                                                  │ │                                 │ │
│ │ ┌─ NPWP ───────────────────────────────────────┐ │ │                                 │ │
│ │ │ 01.234.567.8-999.000                         │ │ │                                 │ │
│ │ └──────────────────────────────────────────────┘ │ │                                 │ │
│ └──────────────────────────────────────────────────┘ └─────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ INFO BANK SECTION ──────────────────────────────────────────────────────────────────────┐
│ Info Bank                                                                                │
│ ─────────────────────────────────────────────────────────────────────────────────────────│
│                                                                                          │
│ ┌─ ROW 1 ──────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 6 ─────────────────────┐    ┌─ COL 6 ─────────────────────┐                  │ │
│ │ │ Nama Bank                   │    │ Kantor Cabang               │                  │ │
│ │ │ Bank Central Asia           │    │ Bogor                       │                  │ │
│ │ └─────────────────────────────┘    └─────────────────────────────┘                  │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                          │
│ ┌─ ROW 2 ──────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ COL 6 ─────────────────────┐    ┌─ COL 6 ─────────────────────┐                  │ │
│ │ │ Pemegang Akun Bank          │    │ Nomor Rekening              │                  │ │
│ │ │ Anwar                       │    │ 541-002-7891                │                  │ │
│ │ └─────────────────────────────┘    └─────────────────────────────┘                  │ │
│ └──────────────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ PEMETAAN AKUN SECTION ──────────────────────────────────────────────────────────────────┐
│ Pemetaan Akun                                                                            │
│ ─────────────────────────────────────────────────────────────────────────────────────────│
│                                                                                          │
│ ┌─ COL 12 ─────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ Akun Hutang ───────────────────────────────────────────────────────────────────┐ │ │
│ │ │ (2-20100) Hutang                                                                │ │ │
│ │ └─────────────────────────────────────────────────────────────────────────────────┘ │ │
│ │                                                                                     │ │
│ │ ┌─ Akun Piutang ──────────────────────────────────────────────────────────────────┐ │ │
│ │ │ (1-20100) Piutang                                                               │ │ │
│ │ └─────────────────────────────────────────────────────────────────────────────────┘ │ │
│ │                                                                                     │ │
│ │ ┌─ Syarat Pembayaran Utama ───────────────────────────────────────────────────────┐ │ │
│ │ │                                                                                 │ │ │
│ │ └─────────────────────────────────────────────────────────────────────────────────┘ │ │
│ └─────────────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘
```

## Responsive Breakpoints

### Desktop (12 columns)
```
Header: [Content - 8 cols] [Actions - 4 cols]
Title: [Company Name - 12 cols]
Type/Group: [Type - 3 cols] [Group - 9 cols]
Info Umum: [Left Fields - 6 cols] [Right Fields - 6 cols]
Info Bank: [Bank Name - 6 cols] [Branch - 6 cols]
           [Account Holder - 6 cols] [Account Number - 6 cols]
Pemetaan: [All Fields - 12 cols stacked]
```

### Tablet (8 columns)
```
Header: [Content - 8 cols]
Actions: [Buttons - 8 cols stacked]
Title: [Company Name - 8 cols]
Type/Group: [Type - 4 cols] [Group - 4 cols]
Info Umum: [All Fields - 8 cols stacked]
Info Bank: [All Fields - 8 cols stacked]
Pemetaan: [All Fields - 8 cols]
```

### Mobile (4 columns)
```
All sections: [Full width - 4 cols]
Stacked vertically
Tabs: Horizontal scroll or dropdown
Actions: Full width buttons
```

## Key Layout Features

### **Tab Interface:**
- **Active Tab**: Green background (#28a745) with white text
- **Inactive Tab**: Light gray background (#e9ecef) with dark text
- **Disabled Tab**: Light gray background with muted text (not clickable)
- **Tab Content**: Dynamic loading based on selected tab

### **Contact Information Display:**
- **Read-only Mode**: All fields displayed as text inputs but not editable
- **Two-column Layout**: Efficient use of space for contact details
- **Section Grouping**: Logical grouping of related information
- **Visual Hierarchy**: Clear section headers with divider lines

### **Contact Type Integration:**
- **Type Badge**: Orange "Pelanggan" badge for visual identification
- **Conditional Fields**: Different fields shown based on contact type
- **Account Mapping**: Appropriate accounts shown (Hutang for suppliers, Piutang for customers)
- **Business Logic**: Fields adapt to contact classification

### **Action Panel:**
- **Primary Action**: "Kelola Kontak" - main management function
- **Secondary Actions**: Merge and archive functionality
- **Vertical Layout**: Efficient space usage in top-right corner
- **Role-based**: Actions available based on user permissions

### **Data Presentation:**
- **Sample Data**: Realistic Indonesian business contact information
- **Field Labels**: Clear Indonesian field labels
- **Account Codes**: Proper accounting code format (2-20100, 1-20100)
- **Contact Details**: Complete business contact information

### **Future Tab Content:**
- **Transaksi Tab**: Will show complete transaction history including:
  - Purchase orders and sales orders
  - Invoices and deliveries
  - Payment records and outstanding balances
  - Transaction filtering and search capabilities
- **Credit Memo Tab**: Credit management functionality including:
  - Credit memo creation and tracking
  - Credit limit management
  - Payment history and creditworthiness
  - Risk assessment and credit scoring

### **Interactive Elements:**
- **Tab Navigation**: Smooth transitions between tab content
- **Edit Mode Toggle**: Switch between view and edit modes
- **Field Validation**: Proper validation when in edit mode
- **Auto-save**: Periodic saving of changes
- **Contact Type Changes**: Dynamic field updates based on type selection

### **Business Logic Integration:**
- **Account Mapping**: Automatic account assignment based on contact type
- **Payment Terms**: Default payment terms for transactions
- **Address Management**: Separate billing and shipping addresses for customers
- **Banking Integration**: Complete banking details for payment processing

### **Accessibility Features:**
- **Keyboard Navigation**: Full keyboard support for tab navigation
- **Screen Reader**: Proper labeling for assistive technologies
- **Color Contrast**: Sufficient contrast for tab states and content
- **Focus Management**: Clear focus indicators for interactive elements

### **Performance Considerations:**
- **Lazy Loading**: Load tab content only when accessed
- **Caching**: Cache contact information for faster subsequent access
- **Optimized Queries**: Efficient database queries for contact data
- **Responsive Images**: Optimized contact photos if implemented

### **Security Features:**
- **Access Control**: Role-based access to contact information
- **Data Encryption**: Sensitive information protection
- **Audit Trail**: Track changes to contact information
- **Permission Checks**: Validate user permissions for each action

This contact detail layout provides a comprehensive view of contact information while maintaining clean organization through tabbed interface and logical section grouping. The design supports both viewing and editing modes with appropriate visual feedback for different states and user interactions.