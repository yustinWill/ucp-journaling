# Sales Invoice (SI) Form - Visual Layout

## ASCII Layout Representation

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ Header                                                                                                      │
│ Menu                                                                                                        │
│                                                                                                             │
│    Buat Faktur Penjualan                                                                                   │
│                                                                                                             │
│    Informasi Transaksi                                                                                     │
│    ─────────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐                              │
│    │ Pelanggan*                      │  │ Email*                          │                              │
│    │ John                        ▼   │  │                                 │                              │
│    └─────────────────────────────────┘  └─────────────────────────────────┘                              │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐  ┌─────────────────────────┐ │
│    │ Alamat*                         │  │ Tanggal Transaksi*              │  │ Nomor Transaksi*        │ │
│    │ autofill but editable           │  │                                 │  │ autofill but editable   │ │
│    │                                 │  │                                 │  │                         │ │
│    └─────────────────────────────────┘  └─────────────────────────────────┘  └─────────────────────────┘ │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐  ┌─────────────────────────┐ │
│    │ Nomor Pemesanan Penjualan       │  │ Syarat Pembayaran*              │  │ Nomor Referensi Pelanggan│ │
│    │ Sales Order 110001          🔗  │  │ Net 30                      ▼   │  │                         │ │
│    └─────────────────────────────────┘  └─────────────────────────────────┘  └─────────────────────────┘ │
│                                                                                                             │
│    ┌─────────────────────────────────┐  ┌─────────────────────────────────┐  ┌─────────────────────────┐ │
│    │ Tag                             │  │ Tanggal Jatuh Tempo*            │  │ Gudang                  │ │
│    │                                 │  │ autofill                        │  │ Pilih Gudang        ▼   │ │
│    └─────────────────────────────────┘  └─────────────────────────────────┘  └─────────────────────────┘ │
│                                                                                                             │
│    Informasi Penjualan                                    ☐ Harga Termasuk Pajak                         │
│    ─────────────────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                                             │
│    ┌─────────────────────────────────────────────────────────────────────────────────────────────────────┐ │
│    │ Produk     │ Deskripsi          │ Kuantitas │ Unit │ Harga Satuan │ Diskon │ Pajak │ Jumlah         │ │
│    │────────────────────────────────────────────────────────────────────────────────────────────────────│ │
│    │ ALBUM SVT▼ │ ALBUM SVT VOL 1    │ 100       │ pcs  │ Rp 250,000.00│ 5%     │ PPN▼  │ Rp 25,000,000  │ │
│    │ Pilih Produk▼│                  │           │      │ Rp           │ 0%     │ PPN▼  │ Rp             │ │
│    │ Pilih Produk▼│                  │           │      │ Rp           │ 0%     │ PPN▼  │ Rp             │ │
│    │ Pilih Produk▼│                  │           │      │ Rp           │ 0%     │ PPN▼  │ Rp             │ │
│    └─────────────────────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                                             │
│    ┌─────────────────────────────────┐              Subtotal                    Rp      12,000,000.00    │
│    │ Pesan                           │              Diskon Perbaris             Rp         500,000.00    │
│    │                                 │              Diskon                  5%  Rp         600,000.00    │
│    │                                 │              Pajak                       Rp       1,221,000.00    │
│    │                                 │              Total                       Rp      12,321,000.00    │
│    └─────────────────────────────────┘              Sisa Tagihan               Rp      12,321,000.00    │
│                                                                                                             │
│    ┌─────────────────────────────────┐                                                                    │
│    │ Memo                            │                                                                    │
│    │                                 │                                                                    │
│    │                                 │                                                                    │
│    │                                 │                                                                    │
│    └─────────────────────────────────┘                                                                    │
│                                                                                                             │
│    Lampiran                                                                                                │
│    ┌─────────────────────────────────┐                           ┌─────────────┐ ┌─────────────┐         │
│    │ Choose File                     │                           │ Batalkan    │ │ Buat        │         │
│    └─────────────────────────────────┘                           └─────────────┘ └─────────────┘         │
│    File dapat berupa Excel, Word, PDF, PNG, atau Zip                                                      │
│                                                                                                             │
└─────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

## Grid-Based Layout

```
┌─ ROW 1 ──────────────────────────────────────────────────────────────────────────────────┐
│ ┌─ COL 6 ─────────────────────┐    ┌─ COL 6 ─────────────────────┐                      │
│ │ Pelanggan* [Dropdown]       │    │ Email* [Text Input]         │                      │
│ │ "John"                      │    │ [Auto-filled]               │                      │
│ └─────────────────────────────┘    └─────────────────────────────┘                      │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ ROW 2 ──────────────────────────────────────────────────────────────────────────────────┐
│ ┌─ COL 6 ─────────────────────┐ ┌─ COL 3 ─────────────────────┐ ┌─ COL 3 ──────────────┐ │
│ │ Alamat* [Textarea]          │ │ Tanggal Transaksi*          │ │ Nomor Transaksi*     │ │
│ │ "autofill but editable"     │ │ [Date Picker]               │ │ "autofill but edit"  │ │
│ └─────────────────────────────┘ └─────────────────────────────┘ └──────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ ROW 3 ──────────────────────────────────────────────────────────────────────────────────┐
│ ┌─ COL 4 ─────────────────────┐ ┌─ COL 4 ─────────────────────┐ ┌─ COL 4 ──────────────┐ │
│ │ Nomor Pemesanan Penjualan   │ │ Syarat Pembayaran*          │ │ Nomor Ref Pelanggan  │ │
│ │ "Sales Order 110001" 🔗     │ │ "Net 30" [Dropdown]         │ │ [Text Input]         │ │
│ └─────────────────────────────┘ └─────────────────────────────┘ └──────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ ROW 4 ──────────────────────────────────────────────────────────────────────────────────┐
│ ┌─ COL 4 ─────────────────────┐ ┌─ COL 4 ─────────────────────┐ ┌─ COL 4 ──────────────┐ │
│ │ Tag [Text Input]            │ │ Tanggal Jatuh Tempo*        │ │ Gudang [Dropdown]    │ │
│ │                             │ │ "autofill" [Date Picker]    │ │ "Pilih Gudang"       │ │
│ └─────────────────────────────┘ └─────────────────────────────┘ └──────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ PRODUCT SECTION ────────────────────────────────────────────────────────────────────────┐
│ Informasi Penjualan                                      [☐] Harga Termasuk Pajak       │
│ ─────────────────────────────────────────────────────────────────────────────────────────│
│                                                                                          │
│ ┌─ 15% ──┐ ┌─ 20% ──┐ ┌─ 10% ┐ ┌─ 8% ┐ ┌─ 12% ─┐ ┌─ 10% ┐ ┌─ 10% ┐ ┌─ 15% ────────┐ │
│ │ Produk │ │ Desk   │ │ Qty  │ │Unit│ │ H.Sat  │ │Diskon │ │ Pajak │ │ Jumlah       │ │
│ ├────────┤ ├────────┤ ├──────┤ ├────┤ ├────────┤ ├───────┤ ├───────┤ ├──────────────┤ │
│ │ALBUM   │ │ALBUM   │ │ 100  ││pcs │ │Rp 250k │ │  5%   │ │ PPN   │ │ Rp 25,000k   │ │
│ │SVT     │ │SVT V1  │ │      ││    │ │        │ │       │ │[Drop] │ │              │ │
│ ├────────┤ ├────────┤ ├──────┤ ├────┤ ├────────┤ ├───────┤ ├───────┤ ├──────────────┤ │
│ │Pilih   │ │        │ │      ││    │ │ Rp     │ │  0%   │ │ PPN   │ │ Rp           │ │
│ │Produk  │ │        │ │      ││    │ │        │ │       │ │       │ │              │ │
│ └────────┘ └────────┘ └──────┘ └────┘ └────────┘ └───────┘ └───────┘ └──────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ BOTTOM SECTION ─────────────────────────────────────────────────────────────────────────┐
│ ┌─ LEFT COLUMN (COL 6) ──────────────────────────────────┐ ┌─ RIGHT COLUMN (COL 6) ────┐ │
│ │ ┌─ Pesan ─────────────────────────────────────────────┐ │ │ Subtotal         Rp 12M  │ │
│ │ │ [Textarea - 4 rows]                                 │ │ │ Diskon Perbaris  Rp 500k │ │
│ │ │                                                     │ │ │ Diskon        5% Rp 600k │ │
│ │ └─────────────────────────────────────────────────────┘ │ │ Pajak            Rp 1.2M │ │
│ │                                                         │ │ Total            Rp 12.3M│ │
│ │ ┌─ Memo ──────────────────────────────────────────────┐ │ │ Sisa Tagihan     Rp 12.3M│ │
│ │ │ [Textarea - 4 rows]                                 │ │ │                           │ │
│ │ │                                                     │ │ │                           │ │
│ │ └─────────────────────────────────────────────────────┘ │ │                           │ │
│ │                                                         │ │                           │ │
│ │ Lampiran                                                │ │                           │ │
│ │ ┌─ Choose File ───────────────────────────────────────┐ │ │                           │ │
│ │ └─────────────────────────────────────────────────────┘ │ │                           │ │
│ └─────────────────────────────────────────────────────────┘ └───────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘

┌─ ACTION BUTTONS ─────────────────────────────────────────────────────────────────────────┐
│                                                          ┌─────────────┐ ┌─────────────┐ │
│                                                          │ Batalkan    │ │ Buat        │ │
│                                                          │ [Red]       │ │ [Green]     │ │
│                                                          └─────────────┘ └─────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────┘
```

## Responsive Breakpoints

### Desktop (12 columns)
```
Row 1: [Pelanggan - 6 cols] [Email - 6 cols]
Row 2: [Alamat - 6 cols] [Tanggal - 3 cols] [Nomor - 3 cols]
Row 3: [SO Reference - 4 cols] [Payment - 4 cols] [Ref - 4 cols]
Row 4: [Tag - 4 cols] [Due Date - 4 cols] [Gudang - 4 cols]
Table: [Product 15%] [Desc 20%] [Qty 10%] [Unit 8%] [Price 12%] [Discount 10%] [Tax 10%] [Total 15%]
Bottom: [Left Content - 6 cols] [Summary - 6 cols]
```

### Tablet (8 columns)
```
Row 1: [Pelanggan - 4 cols] [Email - 4 cols]
Row 2: [Alamat - 8 cols]
Row 3: [Tanggal - 4 cols] [Nomor - 4 cols]
Row 4: [SO Reference - 4 cols] [Payment - 4 cols]
Row 5: [Ref - 4 cols] [Tag - 4 cols]
Row 6: [Due Date - 4 cols] [Gudang - 4 cols]
Table: Horizontal scroll
Bottom: [Left Content - 8 cols] [Summary - 8 cols stacked]
```

### Mobile (4 columns)
```
All fields: [Full width - 4 cols] stacked vertically
Product table: Horizontal scroll or card layout
Summary: Full width stacked
Buttons: Full width stacked
```

## Key Layout Features

### **Sales Invoice Specific Elements:**
- **Customer Focus**: "Pelanggan" instead of "Supplier"
- **Sales Terminology**: "Informasi Penjualan" section header
- **Revenue Processing**: Complete financial calculations for sales revenue
- **Customer Relationship**: Enhanced customer communication features

### **Advanced Product Table (8 Columns):**
- **Product Selection**: Dropdown with product options
- **Description**: Auto-filled product descriptions (editable)
- **Quantity**: Invoice quantities
- **Unit**: Product units of measurement
- **Unit Price**: Sales pricing (editable)
- **Discount Column**: Line-level discount percentages (5% shown)
- **Tax**: PPN tax selection
- **Amount**: Calculated line totals after discounts

### **Enhanced Summary Calculations (6 Lines):**
- **Subtotal**: Rp 12,000,000.00 (before discounts and tax)
- **Diskon Perbaris**: Rp 500,000.00 (line-level discounts total)
- **Diskon**: 5% = Rp 600,000.00 (order-level discount)
- **Pajak**: Rp 1,221,000.00 (tax on final discounted amount)
- **Total**: Rp 12,321,000.00 (final invoice amount)
- **Sisa Tagihan**: Rp 12,321,000.00 (outstanding balance until payment)

### **Sophisticated Discount System:**
1. **Line Discounts**: Applied to individual products (5% in sample)
2. **Order Discount**: Applied to subtotal after line discounts (5% order-level)
3. **Tax Calculation**: Applied to final discounted amount
4. **Complex Sequence**: Line → Order → Tax calculation chain
5. **Customer-Specific**: Support for customer discount agreements

### **Key Differences from Purchase Invoice:**
- **Customer vs Supplier**: Different stakeholder orientation
- **Discount Enhanced**: More sophisticated discount management
- **Revenue vs Cost**: Sales revenue processing vs purchase cost
- **Customer Service**: Enhanced customer relationship features
- **Payment Processing**: Accounts receivable vs accounts payable

### **Key Differences from Sales Order:**
- **Financial Completion**: Final invoice vs preliminary order
- **Payment Terms**: Due dates and payment processing
- **Revenue Recognition**: Actual revenue vs committed sales
- **Document Generation**: Professional invoice PDFs
- **Account Integration**: Links to accounting and payment systems

### **Enhanced Integration Features:**
- **SO Reference**: "Sales Order 110001" with clickable link (🔗)
- **Delivery Tracking**: Links to delivery records
- **Payment Processing**: Integration with payment systems
- **Customer Portal**: Self-service invoice access
- **Document Workflow**: PDF generation and email distribution

### **Advanced Financial Controls:**
- **Discount Approval**: Workflow for large discount approvals
- **Credit Limits**: Customer credit limit validation
- **Payment Terms**: Flexible payment scheduling
- **Revenue Recognition**: Proper accounting integration
- **Tax Compliance**: VAT reporting and compliance

### **Validation Indicators:**
- **Required fields** marked with asterisks (*)
- **Auto-fill placeholders** clearly marked
- **Dropdown indicators** shown with ▼ symbols
- **Integration links** shown with 🔗 symbols
- **Checkbox** for tax inclusion option
- **Discount percentages** with % symbols and validation

### **Interactive Features:**
- **Customer Selection**: Auto-populates email, address, and payment terms
- **Product Selection**: Auto-fills description, unit, price, and discount rules
- **SO Integration**: Complete form pre-fill from sales order
- **Discount Calculations**: Real-time multi-level discount processing
- **Tax Mode Toggle**: Switch between tax-inclusive/exclusive calculations
- **Payment Integration**: Links to payment processing and tracking

### **Document Management:**
- **PDF Generation**: Professional invoice layout with company branding
- **Email Distribution**: Automated customer delivery
- **Payment Links**: Integration with online payment gateways
- **Archive System**: Document warehouse and retrieval
- **Audit Trail**: Complete transaction history and modifications

This Sales Invoice layout represents the most sophisticated form in the sales workflow, combining comprehensive financial calculations with advanced customer relationship management and professional document generation capabilities.