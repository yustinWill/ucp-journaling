# Sales Order (SO) Detail Form - Technical Specification

## Overview
This document provides comprehensive technical specifications for creating the Sales Order (SO) detail view in the sales management module. The form displays complete sales order information with tabbed navigation for delivery and invoice history, enabling comprehensive sales transaction management and workflow control.

## Form Purpose
The Sales Order detail form is specifically designed for:
- Displaying complete sales order information with comprehensive transaction details and customer information
- Managing sales workflow through tabbed interfaces for delivery history and invoice history tracking
- Providing quick access to create new sales deliveries and sales invoices directly from the sales order
- Supporting multiple partial deliveries and invoices with complete status tracking and quantity management
- Maintaining comprehensive audit trail of all related transactions linked to the original sales order
- Enabling workflow control and document relationship management for complete sales process oversight
- Supporting business rule compliance through proper status tracking and document relationship validation
- Facilitating sales analytics and customer relationship management through complete transaction history display

## UI Structure Requirements

### 1. Form Header
- **Title**: "📄 Detail Sales Order"
- **SO Number**: Display SO number (e.g., "SO-20250B-673")
- **Status Badge**: Current SO status with appropriate color coding
  - "Confirmed" (Green badge)
  - "Draft" (Gray badge)
  - "Cancelled" (Red badge)
- **Layout**: Document-style header with clear identification
- **Form ID**: `salesOrderDetailForm`

### 2. Section 1: Basic Information (Informasi Dasar)
Read-only display of core sales order information:

#### Transaction Details Grid:
- **Tanggal Transaksi** (Transaction Date) (Display only)
  - SO creation date
  - Sample: "27 Agustus 2025"
  - Format: DD Month YYYY (Indonesian)
- **Nomor Transaksi** (Transaction Number) (Display only)
  - Sales order number
  - Sample: "SO-20250B-673"
  - Clickable for SO editing (if permitted)
- **Status** (Display only)
  - Current SO status
  - Sample: "Confirmed"
  - Color-coded status indicator
- **Dibuat Oleh** (Created By) (Display only)
  - User who created the SO
  - Sample: "Admin User"
  - User identification for audit trail

### 3. Section 2: Customer Information (Informasi Customer)
Complete customer details from SO:

#### Customer Details Grid:
- **Customer** (Display only)
  - Customer company/individual name
  - Sample: "PT. Customer Example"
  - Link to customer detail if available
- **Email** (Display only)
  - Customer email address
  - Sample: "purchasing@customer.com"
  - Clickable mailto link
- **Alamat** (Address) (Display only)
  - Customer shipping/billing address
  - Sample: "Jl. Sudirman No. 456, Jakarta"
  - Complete address information
- **No. Referensi** (Reference Number) (Display only)
  - Customer reference/PO number