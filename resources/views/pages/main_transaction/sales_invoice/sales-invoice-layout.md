# Sales Invoice Layout Structure

**Module**: Sales Invoice  
**Pattern**: Following Purchase Invoice layout structure  

## Form Layout Structure
```
Card Header: "Tambah/Edit Sales Invoice"
│
├── Section: "Informasi Invoice"
│   ├── Sales Delivery Reference (Select2/Lookup)
│   ├── Customer (Auto-filled from SD)
│   ├── Invoice Date
│   ├── Invoice Number (Auto-generated, editable)
│   ├── Payment Terms (Select2)
│   └── Due Date (Auto-calculated)
│
├── Section: "Detail Tagihan"
│   └── Invoice Line Items Table (Dynamic rows)
│       ├── Product/Service (From Delivery)
│       ├── Description
│       ├── Quantity Invoiced
│       ├── Unit Price
│       ├── Tax (Select2)
│       ├── Line Total (Auto-calculated)
│       └── Actions (Remove if applicable)
│
└── Section: "Informasi Tambahan & Ringkasan"
    ├── Left Side:
    │   ├── Tag (Multiple Select2)
    │   ├── Memo Internal
    │   ├── Invoice Terms & Conditions
    │   └── Attachment
    │
    └── Right Side:
        ├── Subtotal (Auto-calculated)
        ├── Tax Total (Auto-calculated)
        ├── Discount Amount
        ├── Grand Total (Auto-calculated)
        └── Amount Due (Calculated)
```

## Detail Page Structure
```
Tab 1: "Informasi Invoice" - Invoice details and customer info
Tab 2: "Detail Tagihan" - Line items and pricing
Tab 3: "Pembayaran" - Payment history and status
Tab 4: "Audit Log" - Change history
```

## Index Page Structure
- Filter by: Date Range, Customer, Status, Payment Terms
- Columns: Invoice Date, Invoice Number, Customer, Total Amount, Amount Due, Status, Actions
- Export/Import functionality

## Payment Management
- Record payments against invoices
- Track partial payments
- Generate payment receipts
- Handle overpayments and credits

## TODO: Specific Implementation
- Add recurring invoice generation
- Implement dunning process
- Add payment reminder automation
- Configure tax reporting integration
- Set up collection workflow