# Production (Produksi) Add Form - Technical Specification

## Overview
This document provides comprehensive technical specifications for creating the Production (Produksi) add form in the production management module. The form manages recipe-based production with warehouse integration, stock tracking, and flexible cost management for manufacturing operations.

## Form Purpose
The Production add form is specifically designed for:
- Creating production transactions based on pre-defined product bundle recipes and formulations
- Managing warehouse-specific production with real-time stock tracking and availability validation
- Executing recipe-based manufacturing with automatic material requirement calculation from bundle configurations
- Providing flexible production cost management with editable expense categories and amounts
- Integrating stock consumption and finished goods creation with proper inventory accounting
- Supporting batch production tracking for quality control and traceability requirements
- Calculating accurate unit costs including both material and production expenses
- Ensuring stock sufficiency validation before production execution with warehouse-specific inventory

## UI Structure Requirements

### 1. Form Header
- **Title**: "Tambah Produksi Produk"
- **Card Layout**: Standard card with header and body sections
- **Form ID**: `productionAddForm`

### 2. Section 1: Product Information (Info Produk)
Production setup and product selection configuration:

#### Row 1: Date and Batch Information
- **Tanggal Produksi Produk** (Production Date) (Required, Date input)
  - Default: Today's date
  - Sample: "15-Oct-25"
  - Cannot be future date
  - Used for inventory transaction dating
- **Batch Produksi** (Production Batch) (Optional, Text input)
  - Production batch identifier for tracking
  - Sample: "2"
  - Used for quality control and traceability

#### Row 2: Warehouse and Production Quantity
- **Gudang** (Warehouse) (Required, Select dropdown)
  - Warehouse selection for production location
  - Sample: "Gudang Utama"
  - Affects stock availability display
  - Stock levels shown per warehouse
- **Jumlah Produksi** (Production Quantity) (Required, Number input with unit)
  - Production quantity to be manufactured
  - Sample: "5000 pcs"
  - Triggers material requirement calculation
  - Unit auto-filled from selected product

#### Row 3: Product Selection and Category
- **Pilih Produk Bundling** (Select Bundle Product) (Required, Select2 dropdown)
  - Bundle product selection from product master
  - Sample: "Riyo Botol 600ml"
  - Triggers recipe auto-fill and category population
  - Only bundle products available for selection
- **Kategori Produk** (Product Category) (Auto-filled, Select dropdown)
  - Auto-populated from selected product
  - Placeholder: "autofill setelah pilih produk"
  - Read-only after product selection

#### Row 4: Product Code
- **Kode Produk** (Product Code) (Auto-filled, Text input)
  - Auto-populated from selected product
  - Placeholder: "autofill setelah pilih produk"
  - Read-only after product selection
  - Used for production transaction reference

### 3. Section 2: Production Details (Info Detail Produksi)
Material requirements and cost breakdown:

#### Raw Materials Table (Non-editable, Recipe-based):
- **Dynamic Table** populated from bundle recipe
- **Columns**:
  - **Nama Produk** (Product Name) (Display only)
    - Raw material/component name from bundle recipe
    - Sample: "Lakban", "Botol", "Tutup Botol"
  - **Harga** (Price) (Display only, Currency)
    - Current material price per unit
    - Sample: "Rp 2,500", "Rp 500"
  - **Jumlah** (Quantity) (Display only, Number)
    - Required quantity based on production quantity
    - Auto-calculated from recipe × production quantity
  - **Unit** (Unit) (Display only, Text)
    - Material unit of measure
    - Sample: "pcs"
  - **Stok Saat Ini** (Current Stock) (Display only, Number)
    - Available stock at selected warehouse
    - Real-time stock level display
  - **Sisa Stok Setelah** (Remaining Stock) (Display only, Number)
    - Projected stock after production
    - Calculation: Current Stock - Required Quantity
    - Color-coded: Red if insufficient, Green if sufficient
  - **Total** (Total Cost) (Display only, Currency)
    - Total cost per material line
    - Calculation: Price × Quantity

#### Production Costs Table (Editable):
- **Dynamic Table** with add/remove functionality
- **Columns**:
  - **Akun Biaya** (Cost Account) (Required, Select dropdown)
    - Chart of accounts for production expenses
    - Sample: "Biaya Produksi"
    - Integration with accounting system
  - **Harga** (Amount) (Required, Currency input)
    - Production expense amount
    - Sample: "Rp 5,000,000"
    - Editable for different production costs
  - **Total** (Total) (Display only, Currency)
    - Same as Harga for single line items
    - Auto-calculated for formula-based costs
- **Add Row**: "+ Tambah Biaya" button for additional cost lines

#### Financial Summary (Auto-calculated):
- **Total Biaya Bahan Baku** (Total Raw Material Cost)
  - Sum of all material costs
  - Sample: "Rp 8,220,000"
- **Total Biaya Produksi** (Total Production Cost)
  - Sum of all production expense entries
  - Auto-updated when costs change
- **Total Keseluruhan** (Grand Total)
  - Total Raw Material Cost + Total Production Cost
  - Sample: "Rp 8,220,000"
- **Total Harga Perunit** (Unit Cost)
  - Grand Total ÷ Production Quantity
  - Sample: "Rp 1,644"
  - Used for inventory valuation

### 4. Action Buttons
- **Batalkan** (Cancel) (Danger button)
  - Cancels production creation
  - Confirmation dialog before exit
- **Simpan** (Save) (Success button)
  - Creates production transaction
  - Validates stock sufficiency before submission

## JavaScript Functionality Requirements

### 1. Recipe-based Auto-fill
- **Product Selection**: Auto-populate recipe materials and production costs
- **Recipe Calculation**: Calculate material requirements based on production quantity
- **Category Auto-fill**: Populate category and code from selected product
- **Stock Validation**: Check stock sufficiency at selected warehouse

### 2. Warehouse Integration
- **Stock Display**: Real-time stock levels per warehouse
- **Stock Calculations**: Current stock and projected remaining stock
- **Warehouse Change**: Update stock displays when warehouse changed
- **Multi-location Support**: Handle different stock levels per warehouse

### 3. Dynamic Cost Management
- **Cost Table**: Add/remove production expense lines
- **Real-time Calculations**: Update totals when costs change
- **Account Selection**: Chart of accounts integration for cost categorization
- **Validation**: Ensure positive cost amounts and account selection

### 4. Financial Calculations
- **Material Costs**: Auto-calculate from recipe and current prices
- **Production Costs**: Sum editable production expenses
- **Grand Total**: Combine material and production costs
- **Unit Cost**: Calculate cost per unit for inventory valuation

## Data Context
- **Business Flow**: Recipe Configuration → Production Planning → **Production Execution** → Inventory Update
- **Purpose**: Recipe-based manufacturing with cost tracking and inventory integration
- **Integration**: Links to Product Management (recipes), Inventory, and Accounting modules
- **Stock Impact**: Reduces raw material stock, increases finished goods inventory

## Development Notes
- **Recipe Integrity**: Maintain bundle recipe proportions and calculations
- **Stock Validation**: Real-time stock checking with warehouse-specific levels
- **Cost Flexibility**: Allow production cost customization while maintaining recipe structure
- **Inventory Integration**: Proper stock movements for materials and finished goods
- **Performance**: Efficient stock calculations for large product catalogs

## Future Considerations
This production add form will enable:
- **Quality Control**: Integration with quality management systems
- **Advanced Costing**: Support for overhead allocation and activity-based costing
- **Production Scheduling**: Timeline management and resource planning
- **Yield Management**: Track production yields and waste optimization
- **Mobile Production**: Mobile device production entry capabilities
- **IoT Integration**: Integration with production equipment and sensors

## Technical Implementation
- **Framework**: Laravel Blade templates with dynamic recipe loading
- **JavaScript**: jQuery with real-time calculations and stock validation
- **Styling**: Bootstrap + TailwindCSS with responsive table design
- **Database**: Integration with products, recipes, inventory, and accounting tables
- **Validation**: Multi-layer validation including stock sufficiency and business rules
- **Real-time Data**: Live stock levels and price updates from inventory system
- **Transaction Management**: Proper database transactions for inventory movements

## Special Features & Business Logic

### Integration Notes (Based on Requirements and Right Panel Information)
**Production Management Context**:

**Field Specifications from Requirements Analysis**:

#### **Tanggal Produksi Produk**:
- **Description**: "Tanggal pelaksanaan produksi, digunakan sebagai acuan pencatatan dan perhitungan stok/HPP"

#### **Nomor Transaksi**:
- **Description**: "Nomor unik yang dihasilkan sistem atau diinput untuk mengidentifikasi transaksi produksi"

#### **Pilih Produk Bundling**:
- **Description**: "Hanya produk bundling yang muncul di daftar, wajib dipilih untuk menentukan hasil produksi"

#### **Jumlah Produksi**:
- **Description**: "Jumlah unit produk bundling yang akan dihasilkan dalam transaksi produksi ini"

#### **Kode Produk**:
- **Description**: "Kode dari produk bundling tersebut, field ini autofill setelah user memilih produk bundling"

#### **Kategori Produk**:
- **Description**: "Kategori ini juga autofill mengikuti produk bundling yang dipilih"

### Enhanced Production Management Features

#### **Recipe-based Production System**:
- **Bundle Integration**: Production based on pre-defined bundle recipes
- **Automatic Calculation**: Material requirements calculated from recipe × quantity
- **Recipe Integrity**: Maintain exact proportions from product configuration
- **Non-editable Recipe**: Recipe components cannot be modified during production

#### **Warehouse-specific Stock Management**:
- **Multi-warehouse Support**: Different stock levels per warehouse location
- **Real-time Stock Display**: Current available stock at selected warehouse
- **Stock Projection**: Calculate remaining stock after production consumption
- **Stock Validation**: Prevent production if insufficient stock available

#### **Flexible Production Cost Management**:
- **Pre-filled Costs**: Production expenses populated from product configuration
- **Editable Amounts**: Allow cost adjustment for actual production conditions
- **Multiple Cost Categories**: Support various production expense types
- **Account Integration**: Proper cost allocation to appropriate accounting accounts

#### **Real-time Financial Calculations**:
- **Material Cost Tracking**: Automatic calculation from current material prices
- **Production Cost Summation**: Real-time totaling of all production expenses
- **Unit Cost Calculation**: Accurate per-unit cost for inventory valuation
- **Cost Transparency**: Clear breakdown of all cost components

### Business Rules and Validation

#### **Production Workflow**:
- **Sequential Process**: Date → Warehouse → Product → Quantity → Validation → Production
- **Recipe-based Manufacturing**: Strict adherence to bundle recipe formulations
- **Stock Sufficiency**: Mandatory stock validation before production execution
- **Cost Accuracy**: Ensure accurate cost tracking for financial reporting

#### **Inventory Integration**:
- **Raw Material Consumption**: Automatic stock reduction for consumed materials
- **Finished Goods Creation**: Add produced quantity to finished goods inventory
- **Multi-location Tracking**: Handle inventory across different warehouse locations
- **Cost Basis**: Set finished goods cost basis from production calculations

#### **Financial Controls**:
- **Cost Validation**: Ensure positive cost amounts and proper account selection
- **Unit Cost Accuracy**: Precise calculation for inventory valuation and pricing
- **Account Mapping**: Proper integration with chart of accounts for cost allocation
- **Audit Trail**: Complete cost tracking for financial compliance

### Advanced Production Features

#### **Stock Management Integration**:
- **Real-time Availability**: Live stock level checking during production planning
- **Multi-warehouse Operations**: Support production across different locations
- **Stock Reservation**: Optional stock reservation during production process
- **Inventory Reconciliation**: Ensure accurate stock movements and balances

#### **Recipe and Formula Management**:
- **Bundle Recipe Integration**: Seamless integration with product bundle configurations
- **Proportional Scaling**: Automatic scaling of recipe based on production quantity
- **Recipe Integrity**: Maintain exact recipe proportions without modification
- **Component Tracking**: Track individual component consumption and costs

#### **Production Cost Flexibility**:
- **Variable Costs**: Support for production costs that vary by batch or conditions
- **Overhead Allocation**: Framework for overhead cost allocation methods
- **Multi-currency Support**: Handle costs in different currencies if needed
- **Cost History**: Track cost variations across different production runs

### Quality and Compliance

#### **Batch Tracking**:
- **Batch Identification**: Unique batch numbers for traceability
- **Quality Control**: Foundation for quality management integration
- **Compliance Tracking**: Support regulatory compliance requirements
- **Recall Management**: Enable product recall capabilities through batch tracking

#### **Production Documentation**:
- **Complete Records**: Comprehensive production transaction documentation
- **Cost Documentation**: Detailed cost breakdown for financial analysis
- **Recipe Documentation**: Record of exact recipe used in production
- **Approval Workflow**: Foundation for production approval processes

### Integration Capabilities

#### **ERP System Integration**:
- **Inventory Management**: Real-time integration with inventory management systems
- **Financial Systems**: Seamless integration with accounting and financial modules
- **Product Management**: Direct integration with product catalog and recipe management
- **Reporting Systems**: Data foundation for production reporting and analytics

#### **Workflow Management**:
- **Production Planning**: Integration with production planning and scheduling
- **Resource Management**: Foundation for resource allocation and capacity planning
- **Quality Management**: Integration with quality control and assurance systems
- **Supply Chain**: Connection to supply chain management and procurement

This comprehensive production add form provides complete recipe-based manufacturing with warehouse integration, flexible cost management, and proper inventory tracking while ensuring accurate financial calculations and compliance with business rules.