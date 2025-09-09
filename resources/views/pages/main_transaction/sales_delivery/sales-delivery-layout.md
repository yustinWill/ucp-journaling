# Sales Delivery Layout Structure

**Module**: Sales Delivery  
**Pattern**: Following Purchase Delivery layout structure  

## Form Layout Structure
```
Card Header: "Tambah/Edit Sales Delivery"
│
├── Section: "Informasi Pengiriman"
│   ├── Sales Order Reference (Select2/Lookup)
│   ├── Customer (Auto-filled from SO)
│   ├── Delivery Date
│   ├── Delivery Number (Auto-generated, editable)
│   ├── Warehouse (Select2)
│   └── Delivery Address (Textarea)
│
├── Section: "Detail Produk"
│   └── Product Delivery Table (Dynamic rows)
│       ├── Product (From Sales Order)
│       ├── Quantity Ordered (Read-only)
│       ├── Quantity Delivered (Editable)
│       ├── Unit (Read-only)
│       ├── Delivery Status
│       └── Actions (Remove if applicable)
│
└── Section: "Informasi Tambahan & Ringkasan"
    ├── Left Side:
    │   ├── Tag (Multiple Select2)
    │   ├── Memo Internal
    │   ├── Delivery Notes (Customer-facing)
    │   └── Attachment
    │
    └── Right Side:
        ├── Total Items Ordered
        ├── Total Items Delivered
        ├── Delivery Progress (%)
        └── Delivery Status
```

## Detail Page Structure
```
Tab 1: "Informasi Pengiriman" - Delivery details and customer info
Tab 2: "Detail Produk" - Product delivery status table
Tab 3: "Audit Log" - Change history
```

## Index Page Structure
- Filter by: Date Range, Customer, Warehouse, Status
- Columns: Delivery Date, Delivery Number, Customer, Warehouse, Total Items, Status, Actions
- Export/Import functionality

## TODO: Specific Implementation
- Add delivery route planning
- Implement GPS tracking integration
- Add proof of delivery capture
- Configure delivery notifications
- Set up inventory update triggers