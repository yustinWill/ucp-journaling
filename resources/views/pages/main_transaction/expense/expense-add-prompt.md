# Expense (Biaya Pengeluaran) Creation Form - Technical Specification

## Overview
This document provides comprehensive technical specifications for creating the Expense (Biaya Pengeluaran) form in the financial management module. The form manages business expense transactions with conditional payment terms and comprehensive expense categorization.

## Form Purpose
The Expense form is specifically designed for:
- Recording business expenses with proper account categorization and tax handling
- Managing immediate payments and accounts payable scenarios through conditional "Bayar Nanti" functionality
- Supporting multi-line expense entries with detailed descriptions and tax calculations
- Integrating payment terms management for deferred payment scenarios
- Providing comprehensive expense tracking with vendor/recipient management
- Enabling proper financial reporting through chart of accounts integration
- Supporting expense approval workflows and document attachment capabilities
- Managing discount/adjustment entries and final expense calculations

## UI Structure Requirements

### 1. Form Header
- **Title**: "Buat Biaya Pengeluaran"
- **Card Layout**: Standard card with header and body sections
- **Form ID**: `expenseForm`

### 2. Section 1: Transaction Information (Informasi Transaksi)
Contains essential expense transaction details with conditional fields:

#### Row 1: Payment Method and Transaction Number
- **Metode Pembayaran** (Payment Method) (Required when Bayar Nanti unchecked, Select dropdown)
  - Options: Cash, Bank Transfer, Credit Card, Check, E-wallet
  - Sample: "Cash"
  - Disabled/grayed out when "Bayar Nanti" is checked
- **Bayar Nanti** (Pay Later) (Checkbox)
  - Controls conditional field visibility
  - Unchecked: Immediate payment mode
  - Checked: Accounts payable mode
- **Nomor Transaksi** (Transaction Number) (Required, Text input)
  - Auto-generated format: "EXP-YYYY-XXX"
  - Placeholder: "autofill but editable"
  - Editable if needed

#### Row 2: Recipient and Date
- **Penerima** (Recipient) (Optional, Select2 dropdown)
  - Vendor/contact selection
  - Auto-populates billing address when selected
- **Tanggal Transaksi** (Transaction Date) (Required, Date input)
  - Default: Today's date
  - Format: DD/MM/YYYY
  - Cannot be future date

#### Row 3: Payment Terms (Conditional - only when Bayar Nanti checked)
- **Syarat Pembayaran** (Payment Terms) (Required when Bayar Nanti checked, Select dropdown)
  - Options: Net 15, Net 30, Net 45, Net 60, Custom
  - Sample: "Net 30"
  - Triggers due date auto-calculation
- **Tanggal Jatuh Tempo** (Due Date) (Required when Bayar Nanti checked, Date input)
  - Auto-calculated based on transaction date + payment terms
  - Editable for manual adjustment
  - Placeholder: "autofill"

#### Row 4: Billing Address
- **Alamat Penagihan** (Billing Address) (Optional, Textarea)
  - 2-3 rows
  - Auto-populated from recipient selection
  - Manual editing allowed

### 3. Section 2: Expense Information (Informasi Biaya)
Contains detailed expense line items and calculations:

#### Expense Table:
- **Dynamic Table** with add/remove row functionality
- **Columns**:
  - **Akun Biaya** (Expense Account) (Required, Select dropdown)
    - Chart of accounts integration
    - Expense category selection
  - **Deskripsi** (Description) (Optional, Text input)
    - Expense line item description
  - **Pajak** (Tax) (Optional, Select dropdown)
    - Options: PPN (11%), None
    - Sample: "PPN"
  - **Jumlah** (Amount) (Required, Currency input)
    - Indonesian Rupiah format
    - Sample: "Rp 12,000,000.00"
- **Add Row**: "+ Tambah baris" button for additional expense lines

#### Split Layout (Left-Right):
##### Left Side - Additional Information:
- **Memo** (Textarea, 4 rows, 500 chars max)
  - Internal notes for expense processing
  - Placeholder: "Catatan tambahan..."
- **Lampiran** (File input)
  - Accepted formats: .xlsx, .xls, .docx, .doc, .pdf, .png, .zip
  - Maximum size: 10MB
  - Help text: "File dapat berupa Excel, Word, PDF, PNG, atau Zip"

##### Right Side - Financial Summary:
- **Subtotal**: Sum of all expense line amounts
- **Total**: Subtotal + taxes
- **Masukan Jumlah Potongan** (Discount Entry): Manual discount amount
- **Final Total**: Total after discount adjustments
- **Currency Format**: Indonesian Rupiah (Rp X,XXX,XXX.XX)

### 4. Section 3: Conditional Payment Terms Management (Only when Bayar Nanti checked)
Advanced payment terms configuration and management:

#### Payment Terms Information Box:
- **Catatan Khusus untuk Bayar Nanti** (Special Notes for Pay Later)
  - Informational section with checkmarked requirements:
    - ☑ Checkbox Bayar Nanti harus dicentang
    - ☑ Syarat Pembayaran dan Tanggal Jatuh Tempo wajib diisi
    - ☑ Tanggal Jatuh Tempo otomatis dihitung dari tanggal transaksi + syarat pembayaran, tetapi dapat diedit jika perlu

#### Add New Payment Terms:
- **Tambah Syarat Pembayaran** (Add Payment Terms)
  - **Syarat Pembayaran** (Select dropdown)
    - Current terms: Net 30, Net 45, etc.
    - Option to add new terms
  - **└ Syarat Pembayaran baru** (New Payment Terms)
    - Expandable section for creating new payment terms

#### New Payment Terms Creation:
- **Nama Syarat Pembayaran** (Payment Terms Name) (Required, Text input)
  - Custom name for new payment terms
  - Example: "Net 60", "2/10 Net 30"
- **Jumlah Hari Jatuh Tempo** (Days Until Due) (Required, Number input)
  - Number of days for payment
  - Suffix: "Hari" (Days)
  - Range: 1-365 days

### 5. Action Buttons
- **Batalkan** (Cancel) (Danger button)
  - Cancels expense creation
  - Confirmation dialog before exit
- **Buat** (Create) (Success button)
  - Creates the expense transaction
  - Comprehensive validation before submission

## JavaScript Functionality Requirements

### 1. Conditional Logic Management
- **Bayar Nanti Toggle**: Show/hide payment terms fields based on checkbox state
- **Payment Method Control**: Disable payment method dropdown when Bayar Nanti checked
- **Field Validation**: Dynamic required field validation based on Bayar Nanti state
- **Due Date Calculation**: Auto-calculate due date when payment terms selected

### 2. Dynamic Expense Table
- **Row Management**: Add/remove expense line items dynamically
- **Account Selection**: Chart of accounts integration for expense categorization
- **Tax Calculation**: Automatic tax calculation when PPN selected
- **Amount Validation**: Positive amounts with proper currency formatting
- **Real-time Calculations**: Update subtotal and total as amounts change

### 3. Financial Calculations
- **Subtotal Calculation**: Sum of all expense line items
- **Tax Processing**: Automatic PPN (11%) calculation for applicable items
- **Discount Handling**: Manual discount entry and application
- **Final Total**: Real-time calculation of final expense amount
- **Currency Formatting**: Indonesian Rupiah formatting throughout

### 4. Auto-fill and Integration
- **Recipient Selection**: Auto-populate billing address from contact
- **Transaction Number**: Auto-generation with edit capability
- **Payment Terms**: Auto-calculate due dates based on selected terms
- **Account Integration**: Smart account suggestions based on expense type

## Data Context
- **Business Flow**: Expense Request → **Expense Creation** → Payment Processing → Financial Recording
- **Purpose**: Comprehensive expense management with payment flexibility
- **Integration**: Links to Contact, Chart of Accounts, and Payment modules
- **Financial Impact**: Direct expense recording with proper accounting integration

## Development Notes
- **Conditional Rendering**: Proper state management for Bayar Nanti functionality
- **Financial Accuracy**: Precise calculation handling and currency formatting
- **Account Integration**: Seamless chart of accounts integration
- **Payment Terms**: Flexible payment terms management and creation
- **Validation Logic**: Complex validation rules based on form state
- **Responsive Design**: Proper mobile handling of dynamic tables

## Future Considerations
This expense form will enable:
- **Approval Workflows**: Multi-level expense approval processes
- **Recurring Expenses**: Template-based recurring expense creation
- **Budget Integration**: Expense tracking against budget allocations
- **Analytics Integration**: Expense pattern analysis and reporting
- **Mobile Optimization**: Enhanced mobile expense entry capabilities
- **Receipt Management**: Advanced receipt scanning and attachment

## Technical Implementation
- **Framework**: Laravel Blade templates with dynamic form sections
- **JavaScript**: jQuery with conditional logic and real-time calculations
- **Styling**: Bootstrap + TailwindCSS with responsive design
- **Validation**: Multi-layer validation including business rules
- **File Upload**: Secure file handling for expense documentation
- **Database**: Integration with expenses, contacts, accounts, and payment terms tables
- **Accounting**: Proper expense categorization and journal entry creation
- **Currency**: Indonesian Rupiah formatting and calculation standards

## Special Features & Business Logic

### Integration Notes (Based on Right Panel Information)
**Expense Management Context**:
- **Purpose**: "Memungkinkan pengguna untuk mencatat transaksi biaya yang langsung dibayar (bayar sekarang) lengkap dengan detail informasi transaksi, informasi biaya, memo, dan lampiran."

**Field Specifications Table from Right Panel**:

#### Form - Informasi Transaksi:
- **metode_pembayaran**: Dropdown - Pilih metode pembayaran (tunai, transfer, dll.)
- **bayar_nanti**: Checkbox (dicentang) - Jika dicentang → bukan bayar sekarang (tidak dipilih untuk kasus ini)
- **nomor_transaksi**: Text (autofill & editable) - Nomor transaksi otomatis, bisa diedit
- **penerima**: Dropdown/text - Nama pihak/vendor penerima pembayaran
- **tanggal_transaksi**: Date Picker - Tanggal transaksi dilakukan
- **syarat_pembayaran**: Dropdown - Ketentuan pembayaran (misal: Net 30 = bayar dalam 30 hari)
- **tanggal_jatuh_tempo**: Date Picker (autofill) - Tanggal jatuh tempo pembayaran sesuai syarat pembayaran
- **alamat_penagihan**: Textarea - Alamat untuk penagihan dari vendor

#### Form - Informasi Biaya:
- **akun_biaya**: Dropdown - Pilih akun biaya yang relevan
- **deskripsi**: Text - Deskripsi/detail biaya
- **pajak**: Dropdown - Jenis pajak yang berlaku (misal: PPN)
- **jumlah**: Currency Input - Nominal biaya per baris
- **tambah_biaya**: Button - Menambahkan baris biaya baru

#### Summary - Potongan:
- **subtotal**: Currency (readonly) - Total biaya sebelum potongan
- **masukan_jumlah_potongan**: Currency Input - Jumlah potongan yang ingin diberikan
- **total_setelah_potongan**: Currency (readonly) - Total akhir setelah potongan

#### Memo & Lampiran:
- **memo**: Textarea - Catatan tambahan untuk transaksi
- **lampiran**: File Upload - Upload dokumen pendukung (Excel, Word, PDF, PNG, Zip)

### Enhanced Conditional Logic
**Bayar Nanti State Management**:
- **Unchecked (Immediate Payment)**: Focus on payment method selection and immediate expense processing
- **Checked (Accounts Payable)**: Payment method disabled, payment terms and due date management enabled

**Payment Terms Integration**:
- **Existing Terms**: Net 15, Net 30, Net 45, Net 60 standard options
- **Custom Terms**: Ability to create new payment terms with custom day calculations
- **Auto-calculation**: Due date automatically calculated but manually editable

### Financial Processing
**Expense Categorization**:
- **Chart of Accounts**: Integration with Indonesian chart of accounts
- **Tax Handling**: Proper PPN (11%) tax calculation and recording
- **Multiple Expense Lines**: Support for complex expense entries with different accounts
- **Discount Management**: Manual discount/adjustment entry and application

**Currency and Calculations**:
- **Indonesian Rupiah**: Proper formatting (Rp X,XXX,XXX.XX) with thousand separators
- **Real-time Calculations**: Automatic subtotal, tax, and total calculations
- **Precision Handling**: Accurate financial calculations for accounting compliance

### Business Rules and Validation
**Conditional Validation**:
- **Bayar Nanti Unchecked**: Payment method required, payment terms hidden
- **Bayar Nanti Checked**: Payment method disabled, payment terms and due date required
- **Payment Terms**: When selected, auto-calculate due date based on transaction date + term days

**Financial Validation**:
- **Positive Amounts**: All expense amounts must be positive
- **Account Selection**: Valid expense accounts only from chart of accounts
- **Tax Calculations**: Proper PPN application where applicable
- **Total Accuracy**: Ensure calculation accuracy across all financial fields

### Document and Workflow Management
**File Management**:
- **Multiple Formats**: Support for Excel, Word, PDF, PNG, Zip files
- **Size Limits**: 10MB maximum file size for attachments
- **Document Tracking**: Proper file storage and retrieval for audit purposes

**Integration Points**:
- **Contact Management**: Link expenses to vendor/supplier records
- **Accounting System**: Proper journal entry creation for expense recording
- **Payment Processing**: Foundation for payment execution and tracking
- **Reporting**: Expense categorization for financial reporting and analysis

This comprehensive expense form provides flexible expense management supporting both immediate payments and accounts payable scenarios while maintaining proper financial controls and integration with contact management and accounting systems.