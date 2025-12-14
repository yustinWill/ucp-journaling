# Top Up Payment Module - DDL Documentation

## Purpose
Create tables to track lump-sum payments from Kingdom and Owner entities to Pabrik, with automatic FIFO allocation to outstanding Sales Orders and deposit balance management.

## Business Logic

### Concept:
- Kingdom and Owner entities pay Pabrik for their sales revenue
- Payment is submitted as a "Top Up" with a total amount
- System automatically allocates the payment to outstanding Sales Orders using **FIFO** (First In, First Out)
- Any remaining amount after allocation becomes a "deposit" balance
- Deposit balance is tracked and used for future allocations (also FIFO)

### FIFO Allocation Example:
1. Owner has 3 outstanding SO:
   - SO-001 (2025-12-01): Rp 1,500,000 remaining
   - SO-002 (2025-12-05): Rp 2,000,000 remaining
   - SO-003 (2025-12-10): Rp 800,000 remaining

2. Owner makes Top Up of Rp 3,000,000:
   - Allocated to SO-001: Rp 1,500,000 (SO-001 now PAID)
   - Allocated to SO-002: Rp 1,500,000 (SO-002 still has Rp 500,000 remaining)
   - Remaining balance (deposit): Rp 0

3. Owner makes another Top Up of Rp 2,000,000:
   - Allocated to SO-002 (continues from previous): Rp 500,000 (SO-002 now PAID)
   - Allocated to SO-003: Rp 800,000 (SO-003 now PAID)
   - Remaining balance (deposit): Rp 700,000

## Table 1: t_topup

Main table to track Top Up transactions.

### DDL Query

```sql
CREATE TABLE `t_topup` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `company_code` VARCHAR(10) NOT NULL,
    `topup_number` VARCHAR(50) NOT NULL UNIQUE COMMENT 'e.g., TU-202512-001',
    `topup_date` DATE NOT NULL,
    `entity_type` ENUM('KINGDOM', 'OWNER') NOT NULL COMMENT 'Entity yang melakukan pembayaran',
    `topup_amount` DECIMAL(15,2) NOT NULL DEFAULT 0 COMMENT 'Total amount yang dibayarkan',
    `allocated_amount` DECIMAL(15,2) NOT NULL DEFAULT 0 COMMENT 'Total yang sudah dialokasikan ke SO',
    `remaining_balance` DECIMAL(15,2) NOT NULL DEFAULT 0 COMMENT 'Sisa deposit yang belum dialokasikan',
    `notes` TEXT NULL,
    `attachment_path` VARCHAR(255) NULL COMMENT 'Bukti transfer',
    `status` ENUM('OPEN', 'PARTIAL', 'PAID') NOT NULL DEFAULT 'OPEN' COMMENT 'OPEN=belum dialokasikan, PARTIAL=sebagian dialokasikan, PAID=fully allocated',
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    `created_by` BIGINT UNSIGNED NULL,
    `updated_by` BIGINT UNSIGNED NULL,

    INDEX `idx_company_code` (`company_code`),
    INDEX `idx_topup_number` (`topup_number`),
    INDEX `idx_topup_date` (`topup_date`),
    INDEX `idx_entity_type` (`entity_type`),
    INDEX `idx_status` (`status`),
    INDEX `idx_company_entity_date` (`company_code`, `entity_type`, `topup_date`),

    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`updated_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Field Specifications

| Column | Type | Description |
|--------|------|-------------|
| **id** | BIGINT UNSIGNED | Primary key |
| **company_code** | VARCHAR(10) | Company identifier |
| **topup_number** | VARCHAR(50) | Unique top up reference (e.g., TU-202512-001) |
| **topup_date** | DATE | Date when payment was made |
| **entity_type** | ENUM | KINGDOM or OWNER |
| **topup_amount** | DECIMAL(15,2) | Original top up amount submitted |
| **allocated_amount** | DECIMAL(15,2) | Total amount allocated to Sales Orders |
| **remaining_balance** | DECIMAL(15,2) | Deposit balance (topup_amount - allocated_amount) |
| **notes** | TEXT | Optional notes |
| **attachment_path** | VARCHAR(255) | Transfer proof file path |
| **status** | ENUM | OPEN, PARTIAL, or PAID |
| **created_by** | BIGINT UNSIGNED | User who created the record |
| **updated_by** | BIGINT UNSIGNED | User who last updated the record |

### Status Values

- **OPEN**: No allocation yet (allocated_amount = 0)
- **PARTIAL**: Partially allocated (0 < allocated_amount < topup_amount)
- **PAID**: Fully allocated (allocated_amount = topup_amount)

## Table 2: t_topup_sales_order

Mapping table to track which Sales Orders were paid by which Top Up transactions.

### DDL Query

```sql
CREATE TABLE `t_topup_sales_order` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `company_code` VARCHAR(10) NOT NULL,
    `topup_id` BIGINT UNSIGNED NOT NULL COMMENT 'Reference to t_topup',
    `sales_order_id` BIGINT UNSIGNED NOT NULL COMMENT 'Reference to t_sales_orders',
    `allocated_amount` DECIMAL(15,2) NOT NULL DEFAULT 0 COMMENT 'Amount yang dialokasikan untuk SO ini',
    `so_total_amount` DECIMAL(15,2) NOT NULL DEFAULT 0 COMMENT 'Total amount SO (snapshot)',
    `so_remaining_before` DECIMAL(15,2) NOT NULL DEFAULT 0 COMMENT 'Remaining SO sebelum alokasi',
    `so_remaining_after` DECIMAL(15,2) NOT NULL DEFAULT 0 COMMENT 'Remaining SO setelah alokasi',
    `allocation_sequence` INT NOT NULL DEFAULT 0 COMMENT 'Urutan alokasi FIFO (1, 2, 3, ...)',
    `notes` TEXT NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,

    INDEX `idx_company_code` (`company_code`),
    INDEX `idx_topup_id` (`topup_id`),
    INDEX `idx_sales_order_id` (`sales_order_id`),
    INDEX `idx_allocation_sequence` (`allocation_sequence`),
    INDEX `idx_topup_sequence` (`topup_id`, `allocation_sequence`),

    FOREIGN KEY (`topup_id`) REFERENCES `t_topup`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`sales_order_id`) REFERENCES `t_sales_orders`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Field Specifications

| Column | Type | Description |
|--------|------|-------------|
| **id** | BIGINT UNSIGNED | Primary key |
| **company_code** | VARCHAR(10) | Company identifier |
| **topup_id** | BIGINT UNSIGNED | FK to t_topup |
| **sales_order_id** | BIGINT UNSIGNED | FK to t_sales_orders |
| **allocated_amount** | DECIMAL(15,2) | Amount allocated to this SO |
| **so_total_amount** | DECIMAL(15,2) | Snapshot of SO total (for history) |
| **so_remaining_before** | DECIMAL(15,2) | SO remaining before this allocation |
| **so_remaining_after** | DECIMAL(15,2) | SO remaining after this allocation |
| **allocation_sequence** | INT | FIFO sequence number (1 = first, 2 = second, etc.) |
| **notes** | TEXT | Optional notes |

## FIFO Allocation Logic (Pseudocode)

```php
function allocateTopUpToSalesOrders($topupId, $topupAmount, $entityType) {
    // 1. Get outstanding Sales Orders for this entity (ordered by date ASC - FIFO)
    $outstandingSOs = getSalesOrders([
        'entity_type' => $entityType,
        'status' => ['OPEN', 'PARTIAL'],
        'order_by' => 'transaction_date ASC'
    ]);

    $remainingTopUp = $topupAmount;
    $sequence = 1;
    $totalAllocated = 0;

    // 2. Loop through each SO and allocate
    foreach ($outstandingSOs as $so) {
        if ($remainingTopUp <= 0) break;

        $soRemaining = $so->grand_total - $so->paid_amount;
        $allocationAmount = min($remainingTopUp, $soRemaining);

        // 3. Create allocation record
        createAllocation([
            'topup_id' => $topupId,
            'sales_order_id' => $so->id,
            'allocated_amount' => $allocationAmount,
            'so_total_amount' => $so->grand_total,
            'so_remaining_before' => $soRemaining,
            'so_remaining_after' => $soRemaining - $allocationAmount,
            'allocation_sequence' => $sequence
        ]);

        // 4. Update SO paid_amount and status
        $so->paid_amount += $allocationAmount;
        if ($so->paid_amount >= $so->grand_total) {
            $so->payment_status = 'PAID';
        } else {
            $so->payment_status = 'PARTIAL';
        }
        $so->save();

        $remainingTopUp -= $allocationAmount;
        $totalAllocated += $allocationAmount;
        $sequence++;
    }

    // 5. Update topup record
    updateTopUp($topupId, [
        'allocated_amount' => $totalAllocated,
        'remaining_balance' => $remainingTopUp,
        'status' => ($remainingTopUp > 0) ? 'PARTIAL' : 'PAID'
    ]);

    return [
        'total_allocated' => $totalAllocated,
        'remaining_balance' => $remainingTopUp
    ];
}
```

## Verification Queries

### Check Top Up balance consistency
```sql
SELECT
    topup_number,
    topup_amount,
    allocated_amount,
    remaining_balance,
    (topup_amount - allocated_amount) as calculated_balance,
    CASE
        WHEN (topup_amount - allocated_amount) = remaining_balance THEN 'OK'
        ELSE 'MISMATCH'
    END as balance_check
FROM t_topup;
```

### View Top Up allocations
```sql
SELECT
    tu.topup_number,
    tu.topup_date,
    tu.entity_type,
    tu.topup_amount,
    tso.allocation_sequence,
    so.transaction_code as so_number,
    so.transaction_date as so_date,
    tso.allocated_amount,
    tso.so_remaining_before,
    tso.so_remaining_after
FROM t_topup tu
JOIN t_topup_sales_order tso ON tu.id = tso.topup_id
JOIN t_sales_orders so ON tso.sales_order_id = so.id
WHERE tu.topup_number = 'TU-202512-001'
ORDER BY tso.allocation_sequence ASC;
```

### Check entity deposit balance
```sql
SELECT
    entity_type,
    SUM(remaining_balance) as total_deposit
FROM t_topup
WHERE company_code = 'COMP01'
GROUP BY entity_type;
```

## Rollback Queries

### Drop tables
```sql
DROP TABLE IF EXISTS `t_topup_sales_order`;
DROP TABLE IF EXISTS `t_topup`;
```

### Or use Laravel migration rollback
```bash
php artisan migrate:rollback --step=2
```

## Integration Points

### Sales Order Table
Need to add/update these fields in `t_sales_orders`:
- `paid_amount` DECIMAL(15,2) - tracks total payment received
- `payment_status` ENUM('UNPAID', 'PARTIAL', 'PAID') - payment status

### Transaction Number Generation
Top Up numbers should follow format: `TU-YYYYMM-NNN`
- Example: TU-202512-001, TU-202512-002

## Testing Checklist

- [ ] Run migrations successfully
- [ ] Test Top Up creation with FIFO allocation
- [ ] Verify remaining_balance calculation
- [ ] Test partial payment scenario
- [ ] Test full payment scenario
- [ ] Test deposit balance tracking
- [ ] Verify allocation_sequence ordering
- [ ] Test cascade delete (topup -> allocations)
- [ ] Test prevent delete of allocated SO
- [ ] Generate Top Up reports by entity

## Notes

- FIFO is based on `transaction_date` of Sales Orders (oldest first)
- Deposit balance can accumulate across multiple Top Ups
- One Top Up can allocate to multiple SOs
- One SO can receive allocations from multiple Top Ups
- Status automatically updates based on allocation
