# Return Management Form - Technical Specification (Purchase Return / Sales Return)

## Overview
This document provides comprehensive technical specifications for creating the Return Management form that handles both Purchase Returns and Sales Returns in their respective modules. The form manages product returns from invoices, with selective product return capabilities, quantity validation, reason tracking, and financial impact calculations while maintaining complete integration with both purchase invoice and sales invoice systems.

## Form Purpose
The Return Management form is specifically designed for:
- Creating product returns from existing invoices (both purchase invoices and sales invoices) with complete invoice data integration
- Managing selective product returns with checkbox-based selection allowing users to choose specific items for return processing across both purchase and sales workflows
- Supporting quantity-controlled returns with validation ensuring return quantities do not exceed remaining returnable amounts for both supplier returns and customer returns
- Requiring mandatory return reason documentation for each returned product to maintain proper return audit trails in both purchase and sales contexts
- Providing real-time financial calculations of return impact including individual item values and total return amounts for both directions
- Generating return documents (Purchase Return or Sales Return) that automatically update invoice return status and remaining quantities
- Maintaining comprehensive return history tracking for each invoice enabling complete return audit and analysis across purchase and sales
- Integrating with supplier/customer management and accounts payable/receivable for proper financial reconciliation and credit processing

## Return Type Context

### Purchase Return Context:
- **Flow**: Purchase Invoice → Purchase Return → Supplier Credit
- **Party**: Returning products TO supplier
- **Document**: PR-YYYY-XXX (Purchase Return)
- **Financial Impact**: Credit from supplier / Accounts Payable reduction
- **Access**: From Purchase Invoice detail page

### Sales Return Context:
- **Flow**: Sales Invoice → Sales Return → Customer Credit
- **Party**: Accepting products FROM customer  
- **Document**: SR-YYYY-XXX (Sales Return)
- **Financial Impact**: Credit to customer / Accounts Receivable reduction
- **Access**: From Sales Invoice detail page

## UI Structure Requirements

### 1. Form Header
- **Title**: "📋 Form Retur [Purchase/Sales]" 
  - Dynamic title based on context
  - Purchase: "📋 Form Retur Pembelian"
  - Sales: "📋 Form Retur Penjualan"
- **Date Display**: Current date "31/8/2025" in top-right corner
- **Icon**: Document icon (📋) indicating form nature
- **Form ID**: `returnManagementForm`

### 2. Main Layout Structure
- **Split Layout**: 8-column main form area + 4-column requirements information panel
- **Left Column**: Primary form content with invoice reference and return management
- **Right Column**: Requirements, business rules, and process flow guidance

### 3. Section 1: Invoice Reference Information (Auto-filled from Invoice)
Complete invoice context and financial summary (adapts to Purchase/Sales):

#### Invoice Identification:
- **Nomor Invoice** (Invoice Number) (Display only)
  - Auto-filled from selected invoice
  - **Purchase**: "INV/2025/0023" (Purchase Invoice)
  - **Sales**: "SI/2025/0045" (Sales Invoice)
  - Read-only field showing source invoice
  - Primary reference for return process

#### Party Information:
- **[Supplier/Customer]** (Party Name) (Display only)
  - **Purchase**: "Supplier" - "PT. Supplier Utama" 
  - **Sales**: "Customer" - "PT. Customer Indonesia"
  - Auto-populated from invoice data
  - Cannot be modified during return process

#### Invoice Details:
- **Tanggal Invoice** (Invoice Date) (Display only)
  - Original invoice date for both purchase and sales
  - Sample: "15/8/2025"
  - Reference date from invoice
  - Indonesian date format
- **Total Nilai Invoice** (Invoice Total Value) (Display only)
  - Complete invoice amount for both types
  - Sample: "Rp 15.750.000"
  - Indonesian Rupiah formatting
  - Reference for return percentage calculations

### 4. Section 2: Product Selection for Return (Daftar Produk yang Dapat Diretur)
Selective product return with quantity tracking (same for both types):

#### Product Selection Table:
- **Interactive Table** with checkbox selection
- **Columns**:
  - **Select** (Checkbox input)
    - Product selection for return
    - Only products with Qty Sisa Retur > 0 can be selected
    - Must select at least one product for return
  - **Kode Produk** (Product Code) (Display only)
    - Product identification code
    - Sample: "PRD001", "PRD002", "PRD003"
    - Auto-populated from invoice items
  - **Nama Produk** (Product Name) (Display only)
    - Complete product description
    - Sample: "Laptop Asus VivoBook", "Mouse Wireless Logitech", "Keyboard Mechanical"
    - Auto-populated from invoice items
  - **Qty Invoice** (Invoice Quantity) (Display only)
    - Original quantity in invoice (purchased or sold)
    - Sample: 5, 10, 3
    - Reference for return validation
  - **Qty Sudah Retur** (Already Returned Quantity) (Auto-calculated, Display only)
    - Quantity already returned in previous returns
    - Sample: 1, 0, 0
    - Auto-calculated from return history
  - **Qty Sisa Retur** (Remaining Returnable Quantity) (Auto-calculated, Display only)
    - Available quantity for return
    - Calculation: Qty Invoice - Qty Sudah Retur
    - Sample: 4, 10, 3
    - Used for return quantity validation

### 5. Section 3: Return Quantity and Reason Input (Dynamic based on selection)
Return details for selected products (consistent for both types):

#### Return Input Table (Dynamic):
- **Appears only for selected products**
- **Columns**:
  - **Qty Retur** (Return Quantity) (Required, Number input)
    - Quantity being returned for selected product
    - Validation: Must be > 0
    - Validation: Cannot exceed Qty Sisa Retur
    - Sample: 5, 3 (for selected products)
    - Error message: "Qty retur tidak boleh kosong !" if empty
  - **Alasan Retur** (Return Reason) (Required, Dropdown)
    - Mandatory reason selection for each returned product
    - **Purchase Return reasons**: "Barang Rusak", "Tidak Sesuai Spesifikasi", "Cacat Produk", "Salah Kirim", "Lainnya"
    - **Sales Return reasons**: "Barang Rusak", "Tidak Sesuai Pesanan", "Cacat Produk", "Tidak Puas", "Lainnya"
    - Placeholder: "Pilih Alasan"
    - If "Lainnya" selected, additional text field required

### 6. Section 4: Return Summary (Ringkasan Retur)
Financial summary of current return (same calculation for both types):

#### Return Items Summary:
- **Dynamic List** of selected products with return details
- **Format**: "Product Name (Quantity unit) - Return Value"
- **Sample**: 
  - "Keyboard Mechanical (3 unit) - Rp 2.350.000"
  - "Mouse Wireless Logitech (5 unit) - Rp 625.000"
- **Auto-calculated** based on selected products and return quantities

#### Financial Totals:
- **Total Nilai Retur** (Total Return Value)
  - Sum of all returned product values
  - Sample: "Rp 3.175.000"
  - Real-time calculation as selections change
  - Indonesian Rupiah formatting

### 7. Section 5: Return History (Riwayat Retur Invoice Ini)
Previous returns for current invoice (with type indicators):

#### Return History Table:
- **Historical Data** showing previous returns from same invoice
- **Columns**:
  - **Tanggal** (Date) - Return date
  - **No. Retur** (Return Number) - Return document number with type prefix
    - **Purchase**: "[PR]RT/2025/0018" 
    - **Sales**: "[SR]RT/2025/0025"
  - **Produk** (Product) - Returned product name
  - **Qty** (Quantity) - Returned quantity
  - **Alasan** (Reason) - Return reason
- **Sample Entries**: 
  - Purchase: "2025-06-26 | [PR]RT/2025/0018 | Laptop Asus VivoBook | 1 | Barang Rusak"
  - Sales: "2025-07-15 | [SR]RT/2025/0025 | Mouse Wireless Logitech | 2 | Tidak Sesuai Spec"

### 8. Action Buttons
- **✖ Batal** (Cancel) (Secondary button)
  - Color: Gray
  - Function: Cancel return creation
  - Returns to invoice detail page
- **🛒 Buat Return [Purchase/Sales]** (Create Return) (Primary button)
  - Color: Blue (#007bff)
  - Icon: Shopping cart/return icon
  - Dynamic text based on context:
    - **Purchase**: "🛒 Process Purchase Return"
    - **Sales**: "🛒 Process Sales Return"
  - Function: Process return and create return document
  - Validation: At least one product selected, valid quantities, reasons provided

## JavaScript Functionality Requirements

### 1. Invoice Integration (Context-Aware)
- **Invoice Data Loading**: Auto-populate invoice information and product list based on type
- **Product Availability**: Display only products with returnable quantities > 0
- **Return History**: Load and display previous returns with type indicators (PR/SR)
- **Quantity Calculations**: Calculate remaining returnable quantities automatically
- **Context Detection**: Automatically detect Purchase vs Sales context from invoice type

### 2. Product Selection Management (Universal)
- **Dynamic Selection**: Enable/disable products based on returnable quantities
- **Checkbox Validation**: Ensure at least one product selected
- **Table Updates**: Show/hide return input fields based on selection
- **Selection Feedback**: Visual feedback for selected/unselected products

### 3. Return Quantity and Reason Validation (Universal)
- **Quantity Validation**: Prevent return quantities exceeding remaining amounts
- **Non-zero Validation**: Ensure return quantities are greater than zero
- **Reason Requirement**: Validate reason selection for each returned product
- **Context-Specific Reasons**: Load appropriate reason options based on Purchase/Sales context
- **Additional Notes**: Show additional text field when "Lainnya" reason selected

### 4. Real-time Financial Calculations (Universal)
- **Line Calculations**: Calculate return value per selected product
- **Total Updates**: Update total return value as selections change
- **Currency Formatting**: Proper Indonesian Rupiah formatting
- **Summary Generation**: Auto-generate return summary list

## Data Context

### Purchase Return Context:
- **Business Flow**: Purchase Invoice → **Purchase Return** → Supplier Credit Note
- **Purpose**: Product return to suppliers and purchase credit processing
- **Integration**: Direct integration with Purchase Invoice module
- **Financial Impact**: Accounts Payable reduction and supplier credit

### Sales Return Context:
- **Business Flow**: Sales Invoice → **Sales Return** → Customer Credit Note
- **Purpose**: Product return from customers and sales credit processing
- **Integration**: Direct integration with Sales Invoice module
- **Financial Impact**: Accounts Receivable reduction and customer refund

## Development Notes
- **Invoice Dependency**: Can only be accessed from Invoice detail pages (Purchase or Sales)
- **Context-Aware Design**: Single form that adapts to Purchase or Sales context automatically
- **Data Inheritance**: All product and pricing data inherited from respective invoice type
- **Quantity Tracking**: Precise tracking of returnable vs. returned quantities for both contexts
- **Return Documentation**: Generate proper return documents with audit trail for both types
- **Financial Updates**: Update invoice return status and remaining quantities appropriately

## Future Considerations
This return management form will enable:
- **Supplier/Customer Integration**: Direct integration with supplier/customer return authorization systems
- **Quality Management**: Integration with quality control and defect tracking for both directions
- **Automated Processing**: Workflow automation for return approvals and processing
- **Advanced Reporting**: Comprehensive return analytics for supplier and customer performance
- **Mobile Access**: Mobile-optimized interface for warehouse return processing
- **API Integration**: External system integration for return processing automation

## Technical Implementation
- **Framework**: Laravel Blade templates with dynamic invoice integration
- **JavaScript**: jQuery with context-aware form management and real-time calculations
- **Styling**: Bootstrap + TailwindCSS with responsive design
- **Database**: Integration with purchase_invoices/sales_invoices, return_items, and return_documents tables
- **Validation**: Multi-layer validation including quantity limits and business rules
- **Real-time Data**: Live invoice data and return history integration
- **Transaction Management**: Proper database transactions for return processing and invoice updates

## Special Features & Business Logic

### Context-Aware Business Rules

#### **Universal Return Rules (Apply to both Purchase and Sales)**:
**1. Access & Creation**:
- Form can only be created from invoice detail pages (Purchase Invoice or Sales Invoice)
- Products shown are products recorded in the respective invoice
- Single form handles both Purchase Return and Sales Return workflows

**2. Product Selection**:
- Users can only select products with Qty Sisa Retur > 0
- If product fully returned (Qty Sisa Retur = 0), product cannot be selected
- User must select minimum 1 product to create return

**3. Quantity Input**:
- Return qty mandatory if product selected
- Return qty cannot be 0
- Return qty cannot exceed remaining returnable qty
- System shows validation error for invalid quantities

**4. Reason Documentation**:
- Return reason mandatory for each selected product
- Context-specific reason options:
  - **Purchase**: Supplier-focused reasons (product defects, wrong delivery, etc.)
  - **Sales**: Customer-focused reasons (dissatisfaction, product issues, etc.)
- If reason = "Lainnya" → user must fill additional notes

**5. Validation & Processing**:
- System validates before saving return:
  - Products are selected
  - Return quantities are valid  
  - Return reasons are filled
- If validation passes → system saves return, creates return document, updates invoice
- If validation fails → show error message, return cannot be saved

#### **Purchase Return Specific Features**:
- **Document Numbering**: PR-YYYY-XXX format
- **Financial Flow**: Creates supplier credit, reduces accounts payable
- **Return Direction**: Products returned TO supplier
- **Reason Categories**: Quality defects, specification mismatches, delivery errors

#### **Sales Return Specific Features**:
- **Document Numbering**: SR-YYYY-XXX format  
- **Financial Flow**: Creates customer credit, reduces accounts receivable
- **Return Direction**: Products returned FROM customer
- **Reason Categories**: Customer dissatisfaction, product issues, order errors

### Enhanced Return Management Features

#### **Unified Return Processing System**:
- **Single Form Design**: One form handles both Purchase and Sales return workflows
- **Context Auto-Detection**: Automatically adapts based on source invoice type
- **Consistent Business Logic**: Same validation and processing rules for both contexts
- **Type-Specific Customization**: Appropriate labels, reasons, and document types per context

#### **Advanced Integration Capabilities**:
- **Dual Invoice Support**: Seamless integration with both Purchase and Sales Invoice modules
- **Financial Impact Tracking**: Accurate financial calculations for both supplier credits and customer refunds
- **Document Generation**: Proper return document creation with appropriate numbering schemes
- **History Tracking**: Complete return history with clear type indicators (PR/SR)

#### **Comprehensive Audit and Compliance**:
- **Return Documentation**: Complete audit trail for both purchase and sales returns
- **Financial Reconciliation**: Accurate financial impact tracking for both accounts payable and receivable
- **Regulatory Compliance**: Proper documentation supporting tax and regulatory requirements
- **Performance Analytics**: Return pattern analysis for both supplier and customer relationships

This comprehensive return management system provides unified return processing capabilities that seamlessly handle both Purchase Returns and Sales Returns while maintaining context-appropriate business logic, financial calculations, and document generation for complete return workflow management across both purchase and sales operations.# Purchase Return (PR) Creation Form - Technical Specification

## Overview
This document provides comprehensive technical specifications for creating the Purchase Return (PR) form in the purchase management module. The form manages product returns from purchase invoices, with selective product return capabilities, quantity validation, reason tracking, and financial impact calculations while maintaining complete integration with the purchase invoice system.

## Form Purpose
The Purchase Return form is specifically designed for:
- Creating product returns exclusively from existing purchase invoices with complete invoice data integration and validation
- Managing selective product returns with checkbox-based selection allowing users to choose specific items for return processing
- Supporting quantity-controlled returns with validation ensuring return quantities do not exceed remaining returnable amounts
- Requiring mandatory return reason documentation for each returned product to maintain proper return audit trails
- Providing real-time financial calculations of return impact including individual item values and total return amounts
- Generating return documents that automatically update purchase invoice return status and remaining quantities
- Maintaining comprehensive return history tracking for each invoice enabling complete return audit and analysis
- Integrating with supplier management and accounts payable for proper financial reconciliation and credit processing

## UI Structure Requirements

### 1. Form Header
- **Title**: "📋 Form Retur Pembelian"
- **Date Display**: Current date "31/8/2025" in top-right corner
- **Icon**: Document icon (📋) indicating form nature
- **Form ID**: `purchaseReturnForm`

### 2. Main Layout Structure
- **Split Layout**: 8-column main form area + 4-column requirements information panel
- **Left Column**: Primary form content with invoice reference and return management
- **Right Column**: Requirements, business rules, and process flow guidance

### 3. Section 1: Purchase Invoice Reference Information (Auto-filled from Invoice)
Complete purchase invoice context and financial summary:

#### Invoice Identification:
- **Nomor Invoice** (Invoice Number) (Display only)
  - Auto-filled from selected purchase invoice
  - Sample: "INV/2025/0023"
  - Read-only field showing source invoice
  - Primary reference for return process

#### Supplier Information:
- **Supplier** (Supplier Name) (Display only)
  - Supplier name from purchase invoice
  - Sample: "PT. Supplier Utama"
  - Auto-populated from invoice data
  - Cannot be modified during return process

#### Invoice Details:
- **Tanggal Invoice** (Invoice Date) (Display only)
  - Original purchase invoice date
  - Sample: "15/8/2025"
  - Reference date from invoice
  - Indonesian date format
- **Total Nilai Invoice** (Invoice Total Value) (Display only)
  - Complete purchase invoice amount
  - Sample: "Rp 15.750.000"
  - Indonesian Rupiah formatting
  - Reference for return percentage calculations

### 4. Section 2: Product Selection for Return (Daftar Produk yang Dapat Diretur)
Selective product return with quantity tracking:

#### Product Selection Table:
- **Interactive Table** with checkbox selection
- **Columns**:
  - **Select** (Checkbox input)
    - Product selection for return
    - Only products with Qty Sisa Retur > 0 can be selected
    - Must select at least one product for return
  - **Kode Produk** (Product Code) (Display only)
    - Product identification code
    - Sample: "PRD001", "PRD002", "PRD003"
    - Auto-populated from invoice items
  - **Nama Produk** (Product Name) (Display only)
    - Complete product description
    - Sample: "Laptop Asus VivoBook", "Mouse Wireless Logitech", "Keyboard Mechanical"
    - Auto-populated from invoice items
  - **Qty Invoice** (Invoice Quantity) (Display only)
    - Original quantity purchased in invoice
    - Sample: 5, 10, 3
    - Reference for return validation
  - **Qty Sudah Retur** (Already Returned Quantity) (Auto-calculated, Display only)
    - Quantity already returned in previous returns
    - Sample: 1, 0, 0
    - Auto-calculated from return history
  - **Qty Sisa Retur** (Remaining Returnable Quantity) (Auto-calculated, Display only)
    - Available quantity for return
    - Calculation: Qty Invoice - Qty Sudah Retur
    - Sample: 4, 10, 3
    - Used for return quantity validation

### 5. Section 3: Return Quantity and Reason Input (Dynamic based on selection)
Return details for selected products:

#### Return Input Table (Dynamic):
- **Appears only for selected products**
- **Columns**:
  - **Qty Retur** (Return Quantity) (Required, Number input)
    - Quantity being returned for selected product
    - Validation: Must be > 0
    - Validation: Cannot exceed Qty Sisa Retur
    - Sample: 5, 3 (for selected products)
    - Error message: "Qty retur tidak boleh kosong !" if empty
  - **Alasan Retur** (Return Reason) (Required, Dropdown)
    - Mandatory reason selection for each returned product
    - Sample options: "Barang Rusak", "Tidak Sesuai Spesifikasi", "Lainnya"
    - Placeholder: "Pilih Alasan"
    - If "Lainnya" selected, additional text field required

### 6. Section 4: Return Summary (Ringkasan Retur)
Financial summary of current return:

#### Return Items Summary:
- **Dynamic List** of selected products with return details
- **Format**: "Product Name (Quantity unit) - Return Value"
- **Sample**: 
  - "Keyboard Mechanical (3 unit) - Rp 2.350.000"
  - "Mouse Wireless Logitech (5 unit) - Rp 625.000"
- **Auto-calculated** based on selected products and return quantities

#### Financial Totals:
- **Total Nilai Retur** (Total Return Value)
  - Sum of all returned product values
  - Sample: "Rp 3.175.000"
  - Real-time calculation as selections change
  - Indonesian Rupiah formatting

### 7. Section 5: Return History (Riwayat Retur Invoice Ini)
Previous returns for current invoice:

#### Return History Table:
- **Historical Data** showing previous returns from same invoice
- **Columns**:
  - **Tanggal** (Date) - Return date
  - **No. Retur** (Return Number) - Return document number
  - **Produk** (Product) - Returned product name
  - **Qty** (Quantity) - Returned quantity
  - **Alasan** (Reason) - Return reason
- **Sample Entry**: "2025-06-26 | RT/2025/0018 | Laptop Asus VivoBook | 1 | Barang Rusak"
- **Reference Purpose**: Shows return history for audit and reference

### 8. Action Buttons
- **✖ Batal** (Cancel) (Secondary button)
  - Color: Gray
  - Function: Cancel return creation
  - Returns to invoice detail page
- **🛒 Buat Purchase Return** (Create Purchase Return) (Primary button)
  - Color: Blue (#007bff)
  - Icon: Shopping cart/return icon
  - Function: Process return and create return document
  - Validation: At least one product selected, valid quantities, reasons provided

## JavaScript Functionality Requirements

### 1. Purchase Invoice Integration
- **Invoice Data Loading**: Auto-populate invoice information and product list
- **Product Availability**: Display only products with returnable quantities > 0
- **Return History**: Load and display previous returns for reference
- **Quantity Calculations**: Calculate remaining returnable quantities automatically

### 2. Product Selection Management
- **Dynamic Selection**: Enable/disable products based on returnable quantities
- **Checkbox Validation**: Ensure at least one product selected
- **Table Updates**: Show/hide return input fields based on selection
- **Selection Feedback**: Visual feedback for selected/unselected products

### 3. Return Quantity and Reason Validation
- **Quantity Validation**: Prevent return quantities exceeding remaining amounts
- **Non-zero Validation**: Ensure return quantities are greater than zero
- **Reason Requirement**: Validate reason selection for each returned product
- **Additional Notes**: Show additional text field when "Lainnya" reason selected

### 4. Real-time Financial Calculations
- **Line Calculations**: Calculate return value per selected product
- **Total Updates**: Update total return value as selections change
- **Currency Formatting**: Proper Indonesian Rupiah formatting
- **Summary Generation**: Auto-generate return summary list

## Data Context
- **Business Flow**: Purchase Invoice → **Purchase Return** → Credit Note/Supplier Adjustment
- **Purpose**: Product return management and supplier credit processing
- **Integration**: Direct integration with Purchase Invoice module
- **Financial Impact**: Updates invoice return status and creates credit documentation

## Development Notes
- **Invoice Dependency**: Can only be accessed from Purchase Invoice detail page
- **Data Inheritance**: All product and pricing data inherited from invoice
- **Quantity Tracking**: Precise tracking of returnable vs. returned quantities
- **Return Documentation**: Generate proper return documents with audit trail
- **Financial Updates**: Update invoice return status and remaining quantities

## Future Considerations
This purchase return form will enable:
- **Supplier Integration**: Direct integration with supplier return authorization systems
- **Quality Management**: Integration with quality control and defect tracking
- **Automated Processing**: Workflow automation for return approvals and processing
- **Advanced Reporting**: Comprehensive return analytics and supplier performance metrics
- **Mobile Access**: Mobile-optimized interface for warehouse return processing
- **API Integration**: External system integration for return processing automation

## Technical Implementation
- **Framework**: Laravel Blade templates with invoice integration
- **JavaScript**: jQuery with dynamic form management and real-time calculations
- **Styling**: Bootstrap + TailwindCSS with responsive design
- **Database**: Integration with purchase_invoices, return_items, and return_documents tables
- **Validation**: Multi-layer validation including quantity limits and business rules
- **Real-time Data**: Live invoice data and return history integration
- **Transaction Management**: Proper database transactions for return processing and invoice updates

## Special Features & Business Logic

### Integration Notes (Based on Requirements and Right Panel Information)
**Purchase Return Context**:

**Business Requirements from Right Panel**:

#### **Requirement Retur Pembelian**:
**Flow Retur**:
- **Mencatat retur produk dari invoice pembelian**: Record product returns from purchase invoice
- **User dapat memilih produk, input qty retur, dan alasan retur secara terkontrol**: Users can select products, input return quantities, and reasons in controlled manner
- **Mengontrol validasi retur**: Control return validation
- **Qty retur tidak boleh melebihi qty sisa retur, alasan retur wajib diisi**: Return qty cannot exceed remaining returnable qty, reason must be filled
- **Menghasilkan dokumen Sales Return & update invoice**: Generate return document and update invoice
- **Sistem membuat nomor retur baru, mengurangi qty sisa retur di invoice, serta menampilkan riwayat**: System creates new return number, reduces remaining returnable qty in invoice, and displays history

#### **Business Rules – Form Retur Pembelian**:

**1. Akses & Pembuatan**:
- **Form retur hanya bisa dibuat dari detail invoice pembelian**: Return form can only be created from purchase invoice detail
- **Produk yang muncul di form retur adalah produk yang tercatat di invoice**: Products shown in return form are products recorded in invoice

**2. Pemilihan Produk Retur**:
- **User hanya bisa memilih produk dengan Qty Sisa Retur > 0**: Users can only select products with remaining returnable qty > 0
- **Jika produk sudah diretur penuh (Qty Sisa Retur = 0) → produk tidak bisa dipilih lagi**: If product fully returned (remaining qty = 0), product cannot be selected
- **User harus memilih minimal 1 produk untuk bisa membuat retur**: User must select minimum 1 product to create return

**3. Input Qty Retur**:
- **Qty Retur wajib diisi jika produk dipilih**: Return qty mandatory if product selected
- **Qty Retur tidak boleh 0**: Return qty cannot be 0
- **Qty Retur tidak boleh melebihi Qty Sisa Retur → sistem tampilkan error validasi**: Return qty cannot exceed remaining returnable qty → system shows validation error

**4. Alasan Retur**:
- **Alasan retur wajib diisi untuk setiap produk yang dipilih**: Return reason mandatory for each selected product
- **Jika alasan = "Lainnya" → user wajib mengisi Keterangan tambahan**: If reason = "Other" → user must fill additional notes

**5. Validasi & Penyimpanan**:
- **Sistem melakukan validasi sebelum retur disimpan**: System validates before saving return:
  - **Ada produk yang dipilih**: Products are selected
  - **Qty retur valid**: Return quantities are valid
  - **Alasan retur terisi**: Return reasons are filled
- **Jika validasi lolos → sistem simpan retur, buat dokumen retur, dan update qty sudah retur di invoice**: If validation passes → system saves return, creates return document, and updates returned qty in invoice
- **Jika validasi gagal → tampilkan pesan error dan retur tidak bisa disimpan**: If validation fails → show error message and return cannot be saved

### Enhanced Purchase Return Management Features

#### **Invoice Integration System**:
- **Invoice Dependency**: Return form accessible only from purchase invoice detail page
- **Complete Product Integration**: All returnable products sourced from invoice items
- **Quantity Tracking**: Accurate tracking of original, returned, and remaining quantities
- **Financial Integration**: Return values calculated from invoice pricing

#### **Advanced Product Selection and Validation**:
- **Intelligent Product Filtering**: Only show products with remaining returnable quantities
- **Dynamic Selection Interface**: Checkbox-based selection with real-time validation
- **Quantity Constraints**: Enforce return quantity limits based on remaining amounts
- **Selection Requirements**: Minimum one product selection for return processing

#### **Comprehensive Return Processing**:
- **Mandatory Reason Tracking**: Required return reasons for audit and quality management
- **Flexible Reason System**: Support for predefined reasons and custom "Other" option
- **Real-time Calculations**: Live calculation of return values and financial impact
- **Document Generation**: Automatic return document creation with proper numbering

### Business Rules and Validation

#### **Access and Creation Controls**:
- **Invoice-Only Access**: Return forms accessible exclusively through invoice detail pages
- **Product Scope**: Return limited to products actually invoiced and not fully returned
- **Authorization**: Proper user access controls for return creation and processing

#### **Product Selection and Quantity Rules**:
- **Availability-Based Selection**: Only products with remaining returnable quantities available
- **Minimum Selection**: At least one product must be selected for return
- **Quantity Validation**: Return quantities must be positive and within remaining limits
- **Real-time Updates**: Dynamic updates as selections and quantities change

#### **Return Documentation Requirements**:
- **Mandatory Reasons**: Every returned product must have documented reason
- **Additional Notes**: Custom reason support for "Other" category selections
- **Complete Validation**: Comprehensive validation before return submission
- **Error Handling**: Clear error messages for validation failures

### Workflow Integration

#### **Purchase Invoice Integration**:
- **Seamless Navigation**: Direct access from invoice detail with context preservation
- **Status Updates**: Return creation updates invoice return status automatically
- **History Tracking**: Complete return history maintained per invoice
- **Financial Impact**: Accurate calculation and recording of return financial effects

#### **Supplier and Financial Management**:
- **Credit Processing**: Foundation for supplier credit note generation
- **Accounts Payable**: Integration with payables for financial reconciliation
- **Quality Tracking**: Return reason analysis for supplier quality management
- **Audit Trail**: Complete return documentation for compliance and analysis

#### **Operational Excellence**:
- **Return Authorization**: Systematic return approval and processing workflow
- **Inventory Updates**: Return processing updates inventory levels appropriately
- **Performance Analytics**: Return data collection for supplier performance analysis
- **Process Improvement**: Return pattern analysis for process optimization

This comprehensive purchase return system provides complete return management with strict invoice integration, selective product returns, comprehensive validation, and proper financial tracking while maintaining business rule compliance and supporting various return scenarios in the purchase workflow.