# Sales Invoice Module Prompt

**Module**: Sales Invoice (Money In Collection + Financial Recording)  
**Purpose**: Generate invoices and collect payments from customers  
**Flow**: Sales Order → Sales Delivery → **Sales Invoice**

## Business Context
- **Product Flow**: N/A (products already delivered)
- **Money Flow**: Actual incoming payment collection and recording
- **Integration**: References Sales Delivery, integrates with accounting/payments

## Key Features
1. **Invoice Generation**: Create invoices from deliveries
2. **Payment Collection**: Track and record customer payments
3. **Financial Integration**: Post to chart of accounts
4. **Tax Management**: Handle sales tax calculations and reporting
5. **Credit Management**: Track customer credit and outstanding amounts
6. **Import/Export**: Bulk invoice and payment processing

## Invoice Statuses
- **DRAFT**: Invoice created but not finalized
- **PENDING**: Invoice sent, awaiting payment
- **PARTIALLY_PAID**: Partial payment received
- **PAID**: Fully paid invoice
- **OVERDUE**: Payment past due date
- **CANCELLED**: Invoice cancelled
- **REFUNDED**: Payment refunded

## Payment Methods
- **CASH**: Cash payment
- **BANK_TRANSFER**: Bank transfer
- **CREDIT_CARD**: Credit card payment
- **CHECK**: Check payment
- **TRADE_CREDIT**: Credit terms payment

## TODO: Implementation Details
- Add payment term calculations
- Define credit limit enforcement
- Specify tax calculation rules
- Add aging report generation
- Define collection procedures