# Sales Delivery (SD) Creation Form - Technical Specification

## Overview
This document provides comprehensive technical specifications for creating the Sales Delivery (SD) form in the sales module. The SD is the second step in the sales process where shipments to customers are recorded and tracked.

## Form Purpose
The Sales Delivery form is specifically designed for:
- Recording the shipment of goods to customers
- Tracking delivery status and logistics information
- Managing shipping documentation and proof of delivery
- Creating delivery records for customer service and billing

## Business Context
**Indonesian Business Process**: Sales Delivery represents "Pengiriman Penjualan" which is a critical step in Indonesian sales logistics. This form is used to:
- Create official delivery notes (surat jalan)
- Track shipments through various courier services
- Manage customer delivery expectations
- Provide documentation for customer billing and support

## Technical Implementation Requirements

### 1. Form Structure and Layout
The form should be organized into logical sections with proper Bootstrap grid layout:

#### Section 1: Informasi Pengiriman Barang (Goods Shipment Information)
- **Reference SO Selection**: Dropdown to select the related Sales Order
- **Delivery Note Number**: Auto-generated delivery note number (Surat Jalan)
- **Delivery Date**: Date picker for shipment date
- **Internal SD Number**: Auto-generated SD number with format "SD-YYYY-###"
- **Courier Selection**: Dropdown for shipping companies (JNE, TIKI, etc.)
- **Tracking Number**: Input field for courier tracking number
- **Customer Information**: Auto-populated from SO reference

#### Section 2: Informasi Barang yang Dikirim (Shipped Goods Information)
- **Responsive table** displaying items from the selected SO
- **Columns**: Produk, Deskripsi, Qty Dipesan, Qty Dikirim, Unit, Status, Catatan
- **Qty Dikirim** should be editable and default to the ordered quantity
- **Status** dropdown for shipping status per item
- **Catatan** for shipping notes per item

#### Section 3: Informasi Tambahan (Additional Information)
- **Delivery Notes**: Textarea for general delivery instructions
- **Sender Name**: Input for the person responsible for shipment
- **Estimated Arrival**: Date picker for expected delivery date
- **Shipping Cost**: Input for delivery charges
- **Supporting Documents**: File upload for delivery documents

### 2. Data Flow and Relationships
- **SO Reference**: Load SO details when selected
- **Automatic Population**: Fill customer and item details from selected SO
- **Quantity Validation**: Ensure shipped quantities don't exceed ordered quantities
- **Status Tracking**: Track shipment status for each item

### 3. Validation Rules
- **Required Fields**: SO Reference, Delivery Note Number, Delivery Date, SD Number
- **Quantity Validation**: Shipped quantity must be ≤ ordered quantity
- **Date Validation**: Delivery date cannot be in the future beyond reasonable shipping time
- **Tracking Validation**: Validate tracking number format for selected courier

### 4. User Interface Elements
- **Auto-complete**: SO reference selection with search functionality
- **Date Picker**: For delivery and estimated arrival dates
- **File Upload**: Multiple file selection for shipping documents
- **Status Indicators**: Visual status indicators for shipping progress

### 5. JavaScript Functionality
- **SO Selection Handler**: Auto-populate fields when SO is selected
- **Quantity Tracking**: Track shipped vs ordered quantities
- **Status Management**: Handle status changes and notifications
- **Courier Integration**: Future integration with courier tracking APIs
- **Form Validation**: Comprehensive client-side validation

### 6. Business Logic
- **Inventory Update**: Update inventory levels for shipped items
- **SO Status Update**: Update SO status based on shipped quantities
- **Customer Notification**: Send delivery notifications to customers
- **Shipping Integration**: Track packages through courier systems

### 7. Indonesian Business Requirements
- **Surat Jalan**: Generate official Indonesian delivery notes
- **Courier Integration**: Support for major Indonesian shipping companies
- **Tax Documentation**: Include tax information for delivery documentation
- **Multi-Location**: Support shipping from multiple warehouses/locations

### 8. Next Steps Integration
- **Link to SI**: This SD can be referenced when creating Sales Invoice
- **Customer Service**: Integration with customer service and support systems
- **Returns Management**: Handle product returns and exchanges

## Example Use Cases
1. **Complete Shipment**: All ordered items shipped in one delivery
2. **Partial Shipment**: Some items shipped, others backordered
3. **Multiple Addresses**: Different items shipped to different customer locations
4. **Expedited Shipping**: Rush orders with special handling requirements

## Technical Notes
- Use Laravel validation for server-side validation
- Implement proper error handling and user feedback
- Use consistent currency formatting (Indonesian Rupiah)
- Follow existing UI patterns and design system
- Ensure responsive design for mobile devices

## Future Enhancements
- **Real-time Tracking**: Integration with courier tracking APIs
- **Customer Notifications**: Automated SMS/email delivery updates
- **Proof of Delivery**: Digital signature and photo capture
- **Route Optimization**: Optimize delivery routes for efficiency