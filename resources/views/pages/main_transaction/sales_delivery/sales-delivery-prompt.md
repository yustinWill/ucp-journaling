# Sales Delivery Module Prompt

**Module**: Sales Delivery (Actual Product Out + Delivery Confirmation)  
**Purpose**: Execute product deliveries from warehouse to customers  
**Flow**: Sales Order → **Sales Delivery** → Sales Invoice

## Business Context
- **Product Flow**: Actual physical outbound movement from warehouse
- **Money Flow**: Confirmation of delivered goods (basis for invoicing)
- **Integration**: References Sales Order, updates inventory, triggers invoicing

## Key Features
1. **Delivery Execution**: Convert orders to actual deliveries
2. **Inventory Management**: Update stock levels on delivery
3. **Delivery Tracking**: Status and delivery confirmation
4. **Warehouse Integration**: Multi-warehouse delivery support
5. **Customer Communication**: Delivery notifications
6. **Import/Export**: Bulk delivery processing

## Delivery Statuses
- **PENDING**: Delivery scheduled but not executed
- **IN_TRANSIT**: Products shipped, delivery in progress  
- **DELIVERED**: Successfully delivered to customer
- **CANCELLED**: Delivery cancelled
- **RETURNED**: Products returned by customer

## TODO: Implementation Details
- Add delivery route optimization
- Define delivery confirmation process
- Specify inventory update logic
- Add delivery tracking integration
- Define return/exchange procedures