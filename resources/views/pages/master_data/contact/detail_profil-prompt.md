# Contact Detail Form - Technical Specification

## Overview
This document provides comprehensive technical specifications for creating the Contact Detail form in the master data module. This form displays comprehensive contact information with tabbed interface for profile, transactions, and credit memo management.

## Form Purpose
The Contact Detail form is specifically designed for:
- Displaying complete contact profile information including personal and business details
- Managing contact information across different categories (Pelanggan, Supplier, Karyawan, Lainnya)
- Providing tabbed interface for profile, transaction history, and credit memo management
- Supporting banking information and account mapping for financial transactions
- Enabling contact management operations including editing and relationship tracking
- Integrating with transaction modules for seamless business operations

## UI Structure Requirements

### 1. Form Header
- **Title**: "Informasi Kontak"
- **Layout**: Full-width header with contact name as main title
- **Contact Name Display**: "PT Maju Jaya Sentosa" (large, prominent)
- **Form ID**: `contactDetailForm`

### 2. Action Panel (Top Right)
- **Kelola Kontak**: Primary action button (blue background)
- **Gabungkan Kontak**: Secondary action button
- **Arsipkan Kontak**: Secondary action button
- **Button Layout**: Vertical stack in top-right corner

### 3. Tab Navigation
Contains three main tabs with different states:

#### Tab States:
- **Profil** (Active Tab)
  - Background: Green (#28a745)
  - Text Color: White
  - State: Currently selected and displayed
- **Transaksi** (Inactive Tab)
  - Background: Light gray (#e9ecef)
  - Text Color: Dark gray
  - State: Clickable, not currently selected
- **Credit Memo** (Disabled Tab)
  - Background: Light gray (#e9ecef)
  - Text Color: Muted gray
  - State: Disabled/not available

### 4. Contact Type and Group Information
Located below tabs, above main content:

#### Contact Classification:
- **Tipe Kontak**: "Pelanggan" (orange button/badge)
- **Grup**: Text input field showing group classification
- **Layout**: Side-by-side display for quick reference

### 5. Section 1: Info Umum (General Information)
Comprehensive contact details in two-column layout:

#### Left Column:
- **Nama Kontak** (Contact Name)
  - Value: "Anwar"
  - Type: Text input (read-only in detail view)
- **Nama Perusahaan** (Company Name)
  - Value: "PT Maju Jaya Sentosa"
  - Type: Text input (read-only in detail view)
- **Email**
  - Value: "pelanggan1@example.com"
  - Type: Email input (read-only in detail view)
- **Nomor Handphone** (Mobile Number)
  - Value: "081234567890"
  - Type: Text input (read-only in detail view)
- **Nomor Telepon** (Phone Number)
  - Type: Text input (read-only in detail view)
- **Fax**
  - Type: Text input (read-only in detail view)
- **NPWP** (Tax ID)
  - Value: "01.234.567.8-999.000"
  - Type: Text input (read-only in detail view)

#### Right Column:
- **Identitas** (Identity)
  - Value: "KTP"
  - Type: Text input (read-only in detail view)
- **Nomor Identitas** (Identity Number)
  - Value: "3175091203940000.00"
  - Type: Text input (read-only in detail view)
- **Alamat Penagihan** (Billing Address)
  - Type: Textarea (read-only in detail view)
- **Alamat Pengiriman** (Shipping Address)
  - Value: "Ciomas, Bogor"
  - Type: Textarea (read-only in detail view)
- **Info Lainnya** (Other Information)
  - Type: Text input (read-only in detail view)

### 6. Section 2: Info Bank (Banking Information)
Banking details for financial transactions:

#### Banking Fields:
- **Nama Bank** (Bank Name)
  - Value: "Bank Central Asia"
  - Type: Text input (read-only in detail view)
- **Kantor Cabang** (Branch Office)
  - Value: "Bogor"
  - Type: Text input (read-only in detail view)
- **Pemegang Akun Bank** (Account Holder)
  - Value: "Anwar"
  - Type: Text input (read-only in detail view)
- **Nomor Rekening** (Account Number)
  - Value: "541-002-7891"
  - Type: Text input (read-only in detail view)

### 7. Section 3: Pemetaan Akun (Account Mapping)
Accounting integration for financial transactions:

#### Account Fields:
- **Akun Hutang** (Payable Account)
  - Value: "(2-20100) Hutang"
  - Type: Text input (read-only in detail view)
  - Usage: For supplier transactions
- **Akun Piutang** (Receivable Account)
  - Value: "(1-20100) Piutang"
  - Type: Text input (read-only in detail view)
  - Usage: For customer transactions
- **Syarat Pembayaran Utama** (Main Payment Terms)
  - Type: Text input (read-only in detail view)
  - Usage: Default payment terms for transactions

## JavaScript Functionality Requirements

### 1. Tab Management
- **Tab Switching**: Switch between Profil, Transaksi, and Credit Memo
- **State Management**: Maintain active tab state and content loading
- **Disabled State**: Handle disabled tabs appropriately
- **Content Loading**: Load appropriate content for each tab

### 2. Contact Type Handling
- **Dynamic Fields**: Show/hide fields based on contact type
- **Pelanggan Fields**: Display shipping address and receivable accounts
- **Supplier Fields**: Display payable accounts and supplier-specific info
- **Field Visibility**: Conditional display based on contact classification

### 3. Action Button Functionality
- **Kelola Kontak**: Navigate to edit mode or contact management
- **Gabungkan Kontak**: Merge contact functionality
- **Arsipkan Kontak**: Archive contact with confirmation
- **Permission Handling**: Role-based action availability

### 4. Data Display Management
- **Read-only Mode**: Display contact information in view-only format
- **Edit Mode Toggle**: Switch between view and edit modes
- **Data Validation**: Ensure data integrity in edit mode
- **Auto-save**: Periodic saving of changes in edit mode

## Data Context
- **Business Flow**: Master Data → Contact Management → Transaction Integration
- **Purpose**: Centralized contact information management and business relationship tracking
- **Integration**: Links to all transaction modules (PO, SO, Invoices, Deliveries)
- **Relationship**: Foundation for customer and supplier relationship management

## Development Notes
- **Responsive Design**: Maintain readability across different screen sizes
- **Tab Performance**: Efficient content loading for large contact databases
- **Edit Mode**: Seamless transition between view and edit modes
- **Integration Points**: Strong links to transaction history and financial records
- **Data Security**: Appropriate access controls for sensitive contact information

## Future Considerations
This contact detail form will enable:
- **Transaction History**: Complete transaction tracking per contact
- **Credit Management**: Credit limits and payment history analysis
- **Communication Tracking**: Email and communication history integration
- **Document Management**: Attachment and document linking per contact
- **Relationship Mapping**: Contact hierarchy and relationship visualization
- **Performance Analytics**: Customer/supplier performance dashboards

## Technical Implementation
- **Framework**: Laravel Blade templates with tabbed interface
- **JavaScript**: jQuery with tab management and dynamic content loading
- **Styling**: Bootstrap + TailwindCSS with consistent tab styling
- **Data Loading**: Efficient data retrieval for large contact records
- **Caching**: Contact information caching for performance
- **Security**: Role-based access control for contact management operations
- **Integration**: Strong API connections to transaction and accounting modules

## Tab Content Specifications

### Profil Tab (Current)
- **Status**: Active (green background)
- **Content**: Complete contact profile as detailed above
- **Sections**: Info Umum, Info Bank, Pemetaan Akun
- **Mode**: Read-only display with edit capabilities

### Transaksi Tab
- **Status**: Inactive but available
- **Content**: Transaction history and related records
- **Features**: Purchase orders, sales orders, invoices, deliveries
- **Functionality**: Filter, search, and detailed transaction views

### Credit Memo Tab
- **Status**: Disabled (grayed out)
- **Content**: Credit memo management and history
- **Availability**: May require specific contact types or permissions
- **Future**: Credit management and memo creation functionality

## Contact Type Variations

### Pelanggan (Customer) Contact
- **Required Sections**: All sections active
- **Special Fields**: Alamat Pengiriman, Akun Piutang
- **Transaction Focus**: Sales orders, sales invoices, deliveries to customer
- **Account Mapping**: Receivable accounts (1-20100) Piutang

### Supplier Contact
- **Required Sections**: All sections active
- **Special Fields**: Akun Hutang, supplier-specific payment terms
- **Transaction Focus**: Purchase orders, purchase invoices, deliveries from supplier
- **Account Mapping**: Payable accounts (2-20100) Hutang

### Karyawan (Employee) Contact
- **Simplified Sections**: Basic info and banking for payroll
- **Special Focus**: Employee identification and payroll information
- **Limited Features**: Reduced transaction integration

### Lainnya (Other) Contact
- **Flexible Sections**: Configurable based on business needs
- **General Purpose**: Contacts not fitting other categories
- **Custom Fields**: Additional fields based on specific requirements