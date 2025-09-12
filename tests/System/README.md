# System Tests

This directory contains system-level integration tests for the RIYO application.

## Available Tests

### PurchaseOrderDeliverySystemTest.php
**PRIMARY COMPREHENSIVE TEST** - Tests complete Purchase Order and Purchase Delivery workflow with batch creation integration.

### StockAdjustmentSystemTest.php
Comprehensive test for stock adjustment and batch management system functionality.

### LegacyStockSystemTest.php  
Legacy comprehensive test with transaction rollback - tests all stock operations but rolls back changes.

### CreateTestDataScript.php
Script to create test data for stock management testing - creates products, warehouses, and initial batches.

### StockApiQuickTest.php
Quick test for existing Stock API endpoints - validates that controller and services work correctly for future API usage.

### fix_po_test.php
**DEPRECATED** - Historical script used to fix PO data structure during development. Kept for reference only.

**Test Coverage:**
- Stock increase adjustments with batch creation
- Stock decrease adjustments with FIFO batch consumption  
- Batch management service integration
- Stock movement logging
- Stock information APIs
- Database consistency validation

**Usage:**
```bash
# Run PRIMARY comprehensive test (complete PO/PD/batch workflow)
php tests/System/PurchaseOrderDeliverySystemTest.php

# Run stock adjustment test (modifies data)
php tests/System/StockAdjustmentSystemTest.php

# Run legacy test (with rollback - no data modification)
php tests/System/LegacyStockSystemTest.php

# Create test data for testing
php tests/System/CreateTestDataScript.php

# Test API endpoints (controller methods)
php tests/System/StockApiQuickTest.php

# Historical fix script (DEPRECATED - for reference only)
php tests/System/fix_po_test.php
```

**Test Scenarios:**

### PurchaseOrderDeliverySystemTest.php (PRIMARY TEST)
1. **Purchase Order Creation**: Creates 5 POs with different delivery scenarios
2. **Purchase Delivery Processing**: Creates partial and complete deliveries
3. **Production Increase Simulation**: Stock adjustments simulating production
4. **Batch Integration Validation**: Ensures all deliveries create proper batches
5. **Comprehensive Data Validation**: Stock, batches, movements, and pricing consistency

### Other System Tests
1. **Stock Increase Test**: Creates adjustment to increase stock, verifies batch creation and movement logging
2. **Stock Decrease Test**: Creates adjustment to decrease stock, verifies FIFO batch consumption  
3. **Batch Service Test**: Direct testing of BatchManagementService COGS calculation
4. **API Integration Test**: Tests StockInformationService APIs
5. **Consistency Check**: Validates database consistency across all tables

**Expected Results:**
- All stock adjustments process correctly
- Batch quantities match product stock quantities
- Movement logs are created for all adjustments
- Audit logs track all changes
- FIFO batch consumption works properly
- Stock information APIs return correct data

## Running Tests

All system tests can be run individually or as part of the test suite. Tests use actual database connections and should be run in development environment only.

## Test Data

### StockAdjustmentSystemTest.php
- Creates temporary data prefixed with "TEST:" for easy identification and cleanup
- Test data is preserved after completion for manual verification if needed

### LegacyStockSystemTest.php  
- Uses database transactions with rollback - no permanent data changes
- Safe to run on production-like data without affecting actual records

### CreateTestDataScript.php
- Creates permanent test products, warehouses, and batches
- Use this to set up controlled test environment
- Creates products with code "TEST-STOCK-001" and warehouse "WH-TEST"

### StockApiQuickTest.php
- Tests existing API controller methods directly (no HTTP calls needed)
- Validates all stock API endpoints work with current data
- Saves test results to `storage/logs/api_test_results.json` for reference
- Provides cURL examples for actual API testing later

## File Organization

```
tests/System/
├── README.md                      # This documentation
├── StockAdjustmentSystemTest.php  # Main comprehensive test
├── LegacyStockSystemTest.php      # Legacy test with rollback
├── CreateTestDataScript.php       # Test data creation script
└── StockApiQuickTest.php          # API endpoint validation
```

## API Reference Data

The StockApiQuickTest saves current API endpoint information to `storage/logs/api_test_results.json`:

**Current Test Data:**
- **Test Product**: ID 24 (Galon) - 270 units, Rp 1,350,000 total value
- **Test Warehouse**: ID 4 (MAIN WAREHOUSE) - 3 products, 1,270 total units

**Available API Endpoints:**
```
GET  /api/stock                              - Overall stock information
GET  /api/stock/warehouse/{warehouseId}      - Stock by specific warehouse  
GET  /api/stock/product/{productId}/batches  - Product batch information
POST /api/stock/product/{productId}/cogs     - Calculate COGS for quantity
GET  /api/stock/product/{productId}/movements - Stock movement history
GET  /api/stock/low-stock?threshold=X        - Low stock products alert
GET  /api/stock/valuation                    - Complete stock valuation
GET  /api/stock/dashboard                    - Stock dashboard summary
```

**Quick API Test Examples:**
```bash
# Start Laravel server
php artisan serve

# Test overall stock
curl -X GET http://localhost:8000/api/stock

# Test COGS calculation
curl -X POST http://localhost:8000/api/stock/product/24/cogs \
  -H 'Content-Type: application/json' \
  -d '{"quantity": 25}'

# Test dashboard
curl -X GET http://localhost:8000/api/stock/dashboard
```