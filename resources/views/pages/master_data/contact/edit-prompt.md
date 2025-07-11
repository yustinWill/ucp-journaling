# Contact Edit Form - Technical Specification

## Overview
This document provides comprehensive technical specifications for creating the Contact Edit form in the master data module. This form allows editing of existing contact information with all the functionality needed to manage customer, supplier, employee, and other contact types.

## Form Purpose
The Contact Edit form is specifically designed for:
- Editing existing contact information across all contact types (Pelanggan, Supplier, Karyawan, Lainnya)
- Managing comprehensive contact details including personal, business, and financial information
- Handling contact type-specific fields and conditional field display
- Supporting banking information and account mapping for financial transactions
- Enabling contact relationship management and categorization
- Integrating with transaction modules and accounting systems for complete business operations

## UI Structure Requirements

### 1. Form Header
- **Title**: "Kontak Kontak"
- **Card Layout**: Standard card with header and body sections
- **Form ID**: `contactEditForm`
- **Help Text Integration**: Popup help text with field descriptions and validation rules

### 2. Section 1: Info Kontak (Contact Information)
Basic contact identification and classification:

#### Contact Type Selection:
- **Tipe Kontak** (Contact Type) (Required, Radio buttons)
  - Options: Pelanggan, Supplier, Karyawan, Lainnya
  - Horizontal layout with radio button selection
  - Affects field visibility and validation rules
  - Dynamic field showing/hiding based on selection

#### Basic Contact Fields:
- **Nama Kontak** (Contact Name) (Required, Text input)
  - Individual or primary contact person name
  - Validation: Required, minimum 2 characters
- **Grup Kontak** (Contact Group) (Optional, Multi-select dropdown)
  - User can select one or more groups
  - Group categorization for contact management

### 3. Section 2: Info Umum (General Information)
Comprehensive contact details in organized layout:

#### Personal Information:
- **Nama Perusahaan** (Company Name) (Optional, Text input)
  - Company or organization name
  - Auto-populated in some contexts
- **Nomor Handphone** (Mobile Number) (Optional, Text input)
  - Primary mobile contact number
  - Format validation for Indonesian phone numbers
- **Nomor Telepon** (Phone Number) (Optional, Text input)
  - Landline or secondary phone number
- **Email** (Email Address) (Optional, Email input)
  - Primary email address
  - Email format validation
- **Fax** (Fax Number) (Optional, Text input)
  - Fax number if applicable

#### Identification Information:
- **Identitas** (Identity Type) (Optional, Text input)
  - Type of identification (KTP, Passport, etc.)
- **NPWP** (Tax ID) (Optional, Text input)
  - Indonesian tax identification number
  - Conditional display based on contact type

### 4. Section 3: Info Perusahaan (Company Information)
Business-specific information and addresses:

#### Address Information:
- **Nama Perusahaan** (Company Name) (Optional, Text input)
  - Formal company name for business records
- **Nomor Identitas** (Identity Number) (Optional, Text input)
  - Identification number corresponding to identity type
- **Fax** (Fax Number) (Optional, Text input)
  - Business fax number
- **NPWP** (Tax ID) (Optional, Text input)
  - Business tax identification number
- **Alamat Penagihan** (Billing Address) (Optional, Textarea)
  - Address for billing and invoicing purposes
  - Multi-line address field

#### Location Details:
- **Kambeli Balam** (Checkbox)
  - Special location or delivery option
  - Checkbox for additional services
- **Location Fields** (Multiple small fields)
  - City, State, Postal Code fields
  - Organized in grid layout for space efficiency

### 5. Section 4: Info Bank (Banking Information)
Financial transaction details:

#### Banking Details:
- **Nama Bank** (Bank Name) (Optional, Text input)
  - Name of the bank (e.g., BCA, BRI, Mandiri)
- **Kantor Cabang** (Branch Office) (Optional, Text input)
  - Bank branch location
- **Pemegang Akun Bank** (Account Holder) (Optional, Text input)
  - Name of the account holder as per bank records
- **Nomor Rekening** (Account Number) (Optional, Text input)
  - Bank account number
- **Tambah Akun Bank Lain** (Add Another Bank Account)
  - Button to add additional bank accounts
  - Support for multiple banking relationships

### 6. Section 5: Pemetaan Akun (Account Mapping)
Accounting system integration:

#### Account Fields:
- **Akun Hutang** (Payable Account) (Optional, Dropdown)
  - Accounting account for supplier transactions
  - Chart of accounts integration
- **Akun Piutang** (Receivable Account) (Optional, Dropdown)
  - Accounting account for customer transactions
  - Chart of accounts integration
- **Syarat Pembayaran** (Payment Terms) (Optional, Dropdown)
  - Default payment terms for transactions
  - Options: Net 15, Net 30, Net 45, COD, etc.

### 7. Action Buttons
- **Batalkan** (Cancel button)
  - Returns to contact list or detail view
  - Confirmation dialog for unsaved changes
- **Simpan** (Save button)
  - Saves contact information
  - Comprehensive validation before submission

## JavaScript Functionality Requirements

### 1. Contact Type Management
- **Dynamic Field Display**: Show/hide fields based on contact type selection
- **Field Validation**: Adjust validation rules based on contact type
- **Form Behavior**: Adapt form behavior for different contact types
- **Default Values**: Set appropriate defaults based on contact type

### 2. Form Validation
- **Required Field Validation**: Contact name, contact type as minimum
- **Email Validation**: Proper email format validation
- **Phone Number Validation**: Indonesian phone number format validation
- **NPWP Validation**: Tax ID format validation for Indonesian businesses
- **Account Validation**: Valid account selection from chart of accounts

### 3. Dynamic Field Management
- **Conditional Fields**: Show/hide fields based on contact type and other selections
- **Multi-bank Support**: Add/remove additional bank account sections
- **Group Management**: Multi-select group assignment with dynamic options
- **Address Management**: Handle multiple address types and validation

### 4. Help System Integration
- **Context Help**: Popup help text for each field with descriptions and examples
- **Validation Help**: Error messages with helpful guidance
- **Field Dependencies**: Help text explaining field relationships and requirements
- **Business Rules**: Explanation of contact type-specific business rules

## Data Context
- **Business Flow**: Contact Management → Contact Editing → Contact Update → Transaction Integration
- **Purpose**: Comprehensive contact information management and maintenance
- **Integration**: Links to all transaction modules and accounting systems
- **Relationship**: Foundation for customer and supplier relationship management

## Development Notes
- **Responsive Design**: Maintain usability across different screen sizes
- **Performance**: Efficient form handling for complex contact data
- **Data Integrity**: Ensure data consistency during editing operations
- **User Experience**: Intuitive field organization and validation feedback
- **Integration Points**: Strong links to transaction and accounting systems

## Future Considerations
This contact edit form will enable:
- **Advanced Contact Management**: Hierarchical contact relationships and organization structures
- **Document Management**: File attachments and document linking per contact
- **Communication Tracking**: Integration with email and communication history
- **Performance Analytics**: Contact performance and relationship analysis
- **Workflow Integration**: Approval workflows for contact information changes
- **API Integration**: Real-time synchronization with external CRM and ERP systems

## Technical Implementation
- **Framework**: Laravel Blade templates with dynamic field management
- **JavaScript**: jQuery with advanced form validation and dynamic field control
- **Styling**: Bootstrap + TailwindCSS with responsive design
- **Validation**: Multi-layer validation (client-side, server-side, business rules)
- **Data Management**: Efficient contact data handling and relationship management
- **Security**: Role-based access control for contact editing operations
- **Integration**: Strong API connections to transaction and accounting modules

## Special Features & Business Logic

### Integration Notes (Based on Right Panel Information)
**Field Specifications Table**:

**Contact Information Fields:**
- **Nama Kontak**: Text - Nama lengkap atau nama perusahaan (wajib diisi)
- **Tipe Kontak**: Button - Pilihan: Pelanggan, Supplier, Karyawan, Lainnya (wajib)
- **Grup Kontak**: Multi-select Dropdown - User dapat memilih satu atau lebih grup
- **Email**: Email - Opsional. Digunakan untuk komunikasi & pengiriman invoice/PO
- **No HP / Telepon**: Text - Opsional. Nomor utama untuk kontak
- **NPWP**: Text - Opsional, hanya muncul jika kategori = Supplier atau Pelanggan
- **Identitas**: Text - Identitas nama kontak (wajib diisi)
- **Alamat Pengiriman**: Textarea - Hanya muncul jika kategori = Pelanggan. Bisa beda dari alamat utama
- **Alamat Penagihan**: Textarea - Bisa disamakan dengan alamat utama, bisa juga diisi berbeda
- **Kota / Provinsi / Kode Pos**: Text fields / Dropdown - Lokasi administratif tambahan
- **Nama Bank**: Dropdown - Contoh: BCA, BRI, Mandiri, dll
- **No Rekening**: Text - Nomor rekening bank kontak
- **Nama Pemilik Rekening**: Text - Nama sesuai di buku rekening
- **Catatan Bank**: Textarea - Opsional. Misalnya: "Gunakan untuk refund", atau "rekening utama"
- **Akun Piutang**: Dropdown akun - Digunakan jika kategori = Pelanggan. Misalnya: 1101 - Piutang Dagang
- **Akun Hutang**: Dropdown akun - Digunakan jika kategori = Supplier. Misalnya: 2101 - Hutang Usaha

### Enhanced Contact Management
- **Contact Type Behavior**: Dynamic field visibility and validation based on contact type selection
- **Address Management**: Support for different address types (billing, shipping, primary)
- **Banking Integration**: Multiple bank account support with detailed banking information
- **Account Mapping**: Integration with chart of accounts for proper financial categorization

### Business Rules Implementation
- **Conditional Field Display**: Fields appear/disappear based on contact type selection
- **Validation Rules**: Different validation requirements for different contact types
- **Default Values**: Smart defaults based on contact type and business context
- **Integration Requirements**: Proper account mapping for financial transaction integration

### Contact Type Variations
- **Pelanggan (Customer)**: Shipping address, receivable accounts, customer-specific fields
- **Supplier**: Payable accounts, supplier-specific payment terms and information
- **Karyawan (Employee)**: Employee-specific fields and simplified banking information
- **Lainnya (Other)**: Flexible field configuration for miscellaneous contacts

### Data Validation and Integrity
- **Required Field Enforcement**: Contact name and type as minimum requirements
- **Format Validation**: Email, phone number, and tax ID format validation
- **Business Rule Validation**: Ensure appropriate account selection for contact types
- **Data Consistency**: Maintain data integrity across related modules and transactions