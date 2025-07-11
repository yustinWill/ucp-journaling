# Credit Memo (CM) Creation Form - Technical Specification

## Overview
This document provides comprehensive technical specifications for creating the Credit Memo (CM) form in the transaction module. The CM manages customer and supplier credit transactions for returns, adjustments, and dispute resolutions.

## Form Purpose
The Credit Memo form is specifically designed for:
- Creating formal credit memo transactions for customers and suppliers
- Managing credit adjustments, returns, and dispute resolutions
- Tracking credit memo amounts and applications to invoices
- Supporting various credit memo types including returns, adjustments, and promotional credits
- Integrating with accounting systems for proper financial recording
- Providing foundation for credit management and dispute resolution processes

## UI Structure Requirements

### 1. Form Header
- **Title**: "Buat Kredit Memo"
- **Card Layout**: Standard card with header and body sections
- **Form ID**: `creditMemoForm`

### 2. Section 1: Transaction Information
Contains essential credit memo details in a 2-row layout:

#### Row 1: Basic Information
- **Yang Membayar** (Payer) (Required, Select2 dropdown)
  - Options: John, PT. Maju Jaya, CV. Sukses Mandiri, other contacts
  - Auto-populates when contact selected
- **Nomor Transaksi** (Transaction Number) (Required, Text input)
  - Auto-generated format: "CM-YYYY-XXX"
  - Placeholder: "autofill but editable"
  - Editable if needed
- **Tag** (Optional, Text input)
  - For categorizing credit memos
  - Sample usage: return codes, adjustment types

### 3. Section 2: Transaction Details (Transaksi 1)
Contains detailed transaction information:

#### Transaction Fields:
- **Tanggal Transaksi** (Transaction Date) (Required, Date input)
  - Default: Today's date
  - Cannot be future date
- **Jenis Transaksi** (Transaction Type) (Required, Select dropdown)
  - Options: "Pengeluaran Kredit Memo", "Adjustment", "Return", "Discount"
  - Default: "Pengeluaran Kredit Memo"
- **Nama Transaksi** (Transaction Name) (Required, Text input)
  - Descriptive name for the credit memo transaction
- **Pilih Akun** (Account Selection) (Required, Select dropdown)
  - Chart of accounts integration
  - Account selection for proper accounting
- **Nilai** (Amount) (Required, Currency input)
  - Credit memo amount
  - Sample: Rp 500,000.00
  - Format: Indonesian Rupiah with separators
- **Deskripsi** (Description) (Optional, Textarea)
  - Detailed description of credit memo reason
  - Max 500 characters

### 4. Section 3: Additional Information
Documentation and memo fields:

#### Documentation Fields:
- **Memo** (Textarea, 4 rows, 500 chars max)
  - Internal memo for credit memo processing
  - Placeholder: "Catatan internal..."
- **Lampiran** (File input)
  - Accepted formats: .xlsx, .xls, .docx, .doc, .pdf, .png, .zip
  - Maximum size: 10MB
  - Help text: "File dapat berupa Excel, Word, PDF, PNG, atau Zip"

### 5. Action Buttons
- **Batalkan** (Danger button)
  - Cancels credit memo creation
  - Confirmation dialog before exit
- **Simpan** (Success button)
  - Creates the credit memo
  - Comprehensive validation before submission

## JavaScript Functionality Requirements

### 1. Auto-fill Features
- **Contact Selection**: Populates related contact information
- **Transaction Number**: Auto-generation with edit capability
- **Account Integration**: Smart account selection based on transaction type
- **Date Calculations**: Current date default with validation

### 2. Credit Memo Management
- **Amount Validation**: Ensure positive credit amounts
- **Account Validation**: Verify proper account selection for credit entries
- **Transaction Type**: Dynamic field behavior based on credit memo type
- **Contact Integration**: Link to customer/supplier records

### 3. Form Validation
- **Required Fields**: Payer, Transaction Number, Date, Transaction Type, Account, Amount
- **Amount Validation**: Positive numbers only, proper currency format
- **Date Validation**: Transaction date not in future
- **Account Validation**: Valid account selection from chart of accounts
- **Contact Validation**: Valid contact selection
- **File Validation**: Type and size restrictions

### 4. Financial Integration
- **Chart of Accounts**: Integration with accounting system
- **Credit Processing**: Proper credit entry creation
- **Contact Linking**: Associate credit memo with customer/supplier
- **Amount Formatting**: Indonesian currency formatting and validation

## Data Context
- **Business Flow**: Credit Request → **Credit Memo** → Credit Application
- **Purpose**: Formal credit documentation and financial recording
- **Integration**: Links to Contact, Invoice, and Accounting modules
- **Credit Management**: Foundation for credit tracking and application

## Development Notes
- **Financial Accuracy**: Precise credit amount handling and accounting integration
- **Contact Integration**: Seamless connection to contact management system
- **Account Mapping**: Proper integration with chart of accounts
- **Credit Tracking**: Foundation for credit memo application and settlement
- **Audit Trail**: Complete transaction logging for compliance

## Future Considerations
This credit memo form will enable:
- **Credit Application**: Direct application to outstanding invoices
- **Batch Processing**: Multiple credit memo creation for bulk adjustments
- **Approval Workflows**: Multi-level approval for large credit amounts
- **Credit Analytics**: Credit memo pattern analysis and reporting
- **Integration Enhancement**: Advanced ERP and accounting system integration
- **Document Management**: Enhanced attachment and document workflow

## Technical Implementation
- **Framework**: Laravel Blade templates
- **JavaScript**: jQuery with financial calculations and validation
- **Styling**: Bootstrap + TailwindCSS
- **Validation**: Multi-layer validation including business rules
- **File Upload**: Secure file handling for supporting documents
- **Database**: Integration with contacts, accounts, and transaction tables
- **Accounting**: Proper double-entry bookkeeping integration
- **Currency**: Indonesian Rupiah formatting and calculation standards

## Special Features & Business Logic

### Integration Notes (Based on Right Panel Information)
**Credit Memo Transaction Types**:

**Jenis Transaksi**: 
- **Pengeluaran Kredit Memo**: Primary credit memo issuance type

**Notes Section Information**:
- **Jika dipilihnya pengeluaran kredit memo, jurnal debit nya adalah akun yg dipilih**
- **Jika dipilihnya penerimaan kredit memo, jurnal kredit nya adalah akun dipilih**

**Field Specifications Table**:
- **Yang Membayar**: Text Field / Dropdown - Nama kontak yang membayar / menerima credit memo (wajib)
- **Nomor Transaksi**: Text Field (readonly & editable) - Nomor unik untuk transaksi; terisi otomatis tetapi bisa diedit
- **Tag**: Text Field - Label tambahan untuk mengkategorikan transaksi (opsional)
- **Tanggal Transaksi**: Date Picker - Tanggal dibuatnya transaksi credit memo (wajib)
- **Jenis Transaksi**: Dropdown / Select - Tipe transaksi (misal: Pengeluaran Credit Memo), dropdown
- **Nama Transaksi**: Text Field - Nama deskriptif untuk transaksi ini (wajib)
- **Pilih Akun**: Dropdown / Select - Akun pembukuan yang terkait dengan transaksi, dropdown (wajib)
- **Nilai**: Number Field / Currency - Nominal kredit memo untuk haris transaksi
- **Deskripsi**: Text Area - Penjelasan tambahan tentang transaksi (opsional)
- **Memo**: Text Area - Catatan untuk internal, tidak ditam

### Enhanced Credit Memo Management
- **Transaction Type Integration**: Support for different credit memo types (issuance vs. receipt)
- **Accounting Integration**: Proper debit/credit journal entry creation based on transaction type
- **Contact Management**: Integration with customer and supplier records
- **Amount Tracking**: Precise credit amount recording and validation

### Financial Processing
- **Double-Entry Bookkeeping**: Automatic journal entry creation based on transaction type
- **Account Selection**: Integration with chart of accounts for proper categorization
- **Credit Validation**: Ensure credit amounts are within acceptable limits
- **Currency Handling**: Proper Indonesian Rupiah formatting and calculations

### Transaction Types
- **Pengeluaran Kredit Memo**: Credit memo issuance (debit to selected account)
- **Penerimaan Kredit Memo**: Credit memo receipt (credit to selected account)
- **Return Processing**: Product return credit memo handling
- **Adjustment Credits**: Manual adjustment credit memos

### Business Rules
- **Credit Limits**: Ensure credit memos don't exceed defined limits
- **Account Validation**: Verify appropriate account selection for credit type
- **Contact Authorization**: Ensure proper authorization for credit memo creation
- **Amount Validation**: Positive amounts only with proper currency formatting

### Workflow Integration
- **Contact Pre-fill**: When accessed from contact detail, auto-populate contact information
- **Account Smart Selection**: Suggest appropriate accounts based on contact type and transaction type
- **Credit Application**: Foundation for future credit application to invoices
- **Approval Process**: Support for approval workflows for large credit amounts