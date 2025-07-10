# Sales Order (SO) Creation Form - Technical Specification

## Overview
This document provides comprehensive technical specifications for creating the Sales Order (SO) form in the sales module. The SO is the first step in the sales process where orders are received from customers.

## Form Purpose
The Sales Order form is specifically designed for:
- Creating formal sales orders from customer requests
- Specifying product requirements, quantities, and delivery terms
- Establishing payment terms and conditions with customers
- Managing customer relationships and order fulfillment

## Business Context
**Indonesian Business Process**: Sales Order represents "Pesanan Penjualan" which is a critical step in Indonesian sales processes. This form is used to:
- Record customer orders and commitments
- Set pricing and delivery schedules
- Establish payment terms and credit arrangements
- Create the foundation for inventory allocation and delivery planning

## Technical Implementation Requirements

### 1. Form Structure and Layout
The form should be organized into logical sections with proper Bootstrap grid layout:

#### Section 1: Informasi Transaksi (Transaction Information)
- **Customer Selection**: Dropdown to select existing customers
- **Customer Email**: Auto-filled or editable email field
- **Customer Address**: Auto-filled address from customer master
- **Transaction Date**: Date picker for order date
- **Transaction Number**: Auto-generated SO number with format "SO-YYYY-###"
- **Payment Terms**: Dropdown for payment terms (Net 15, Net 30, etc.)
- **Due Date**: Auto-calculated based on payment terms

#### Section 2: Informasi Penjualan (Sales Information)
- **Tax Inclusion Checkbox**: "Harga Termasuk Pajak" for tax-inclusive pricing
- **Responsive table** for product selection and pricing
- **Columns**: Produk, Deskripsi, Kuantitas, Unit, Harga Satuan, Pajak, Jumlah, Aksi
- **Dynamic row addition**: Add/remove product lines
- **Tax calculation**: Support for PPN (Value Added Tax)

#### Section 3: Informasi Tambahan & Ringkasan (Additional Information & Summary)
- **Left Side**: Customer messages, internal memos, file attachments
- **Right Side**: Financial summary with subtotals, tax, total, down payment
- **Down Payment**: Optional down payment from customer

### 2. Data Flow and Relationships
- **Customer Master**: Load customer details when selected
- **Product Catalog**: Product selection with auto-fill pricing
- **Tax Calculation**: Real-time tax calculation based on settings
- **Inventory Check**: Validate product availability (future enhancement)

### 3. Validation Rules
- **Required Fields**: Customer, Email, Address, Transaction Date, SO Number, Due Date
- **Customer Validation**: Ensure valid customer selection
- **Product Validation**: At least one product must be selected
- **Date Validation**: Transaction date and due date validation
- **Amount Validation**: Ensure positive amounts and proper calculations

### 4. User Interface Elements
- **Auto-complete**: Customer selection with search functionality
- **Date Picker**: For transaction and due dates
- **Currency Mask**: For all amount fields with Indonesian Rupiah formatting
- **Dynamic Tables**: Add/remove rows for products
- **Tax Calculator**: Real-time tax calculation display

### 5. JavaScript Functionality
- **Customer Selection Handler**: Auto-populate fields when customer is selected
- **Product Selection**: Auto-fill product details and pricing
- **Real-time Calculations**: Update totals as values change
- **Tax Calculations**: Handle tax-inclusive/exclusive calculations
- **Currency Formatting**: Format amounts according to Indonesian standards
- **Form Validation**: Comprehensive client-side validation

### 6. Business Logic
- **Customer Credit Check**: Validate customer credit limits (future enhancement)
- **Inventory Allocation**: Reserve inventory for confirmed orders
- **Pricing Rules**: Apply customer-specific pricing and discounts
- **Order Workflow**: Track order status from quote to confirmation

### 7. Indonesian Business Requirements
- **Sales Process**: Follow Indonesian sales order procedures
- **Tax Compliance**: Support PPN calculation and reporting
- **Customer Management**: Integrate with customer master data
- **Multi-Currency**: Handle foreign currency orders when applicable

### 8. Next Steps Integration
- **Link to SD**: This SO can be referenced when creating Sales Delivery
- **Inventory Management**: Integration with inventory allocation system
- **Customer Portal**: Future integration with customer self-service portal

## Example Use Cases
1. **Standard Order**: Regular customer placing standard product order
2. **Custom Pricing**: VIP customer with special pricing agreements
3. **Partial Delivery**: Order with multiple delivery schedules
4. **International Sale**: Foreign currency order with export requirements

## Technical Notes
- Use Laravel validation for server-side validation
- Implement proper error handling and user feedback
- Use consistent currency formatting (Indonesian Rupiah)
- Follow existing UI patterns and design system
- Ensure responsive design for mobile devices

## Future Enhancements
- **Customer Portal Integration**: Allow customers to place orders online
- **Advanced Pricing**: Complex pricing rules and discount structures
- **Inventory Integration**: Real-time inventory checking and allocation
- **Workflow Automation**: Automated approval processes for large orders