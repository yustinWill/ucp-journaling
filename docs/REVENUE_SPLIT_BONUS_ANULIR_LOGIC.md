# Revenue Split: Bonus and Anulir Logic

## Status

This document defines the revenue-split calculation used by new or recalculated Sales Order
revenue splits. Bonus cost is funded by Owner, while a sales-value shortage is distributed using
the Pabrik, Owner, then Perusahaan priority.

This is a calculation-only change and requires no database migration. Existing stored split
amounts are not rewritten automatically. Resetting or editing an existing split recalculates it
with this logic.

## Business meaning

- **Qty Total**: original quantity, including normal, bonus, and later-anulir pieces.
- **Qty Bonus**: free pieces. They produce no revenue, but Pabrik is paid for producing them.
- **Qty Anulir**: canceled pieces. They produce no revenue and no party is paid for them.
- **Qty Efektif / Normal**: pieces that produce revenue.
- **Sisa Ke**: party receiving a positive difference between the effective Sales Order value and
  the configured per-piece rates. A negative difference uses the fixed shortage priority instead.

Bonus production is funded by **Owner**:

1. Pabrik receives its configured rate for normal and bonus pieces.
2. Owner receives its configured rate for normal pieces, minus the Pabrik rate for every bonus
   piece.
3. Perusahaan receives its configured rate only for normal pieces.

## Inputs

| Input | Stored/source field |
|---|---|
| `qty_total` | `product_quantity` |
| `qty_anulir` | `return_qty` |
| `qty_bonus` | Sum of `t_sales_order_products.quantity` where the matching row has `is_bonus = 1` |
| `rate_pabrik` | product config `porsi_pabrik` |
| `rate_owner` | product config `porsi_owner` |
| `rate_perusahaan` | product config `porsi_perusahaan` |
| `total_produk` | Calculated as `product_unit_price × product_quantity` |
| `sisa_ke` | product config or split `remaining_allocation` |

The gross value before bonus and anulir is derived at calculation time:

```text
total_produk = product_unit_price × qty_total
```

`product_total_amount` stores the calculated effective total after bonus and anulir.

## Formula

```text
qty_normal = qty_total - qty_anulir - qty_bonus

porsi_pabrik_base =
    (qty_normal + qty_bonus) × rate_pabrik

porsi_owner_base =
    (qty_normal × rate_owner) - (qty_bonus × rate_pabrik)

porsi_perusahaan_base =
    qty_normal × rate_perusahaan

total_split_base =
    porsi_pabrik_base + porsi_owner_base + porsi_perusahaan_base

total_efektif =
    round(qty_normal × total_produk / qty_total)

selisih =
    total_efektif - total_split_base

if selisih >= 0:
    porsi[sisa_ke] = porsi[sisa_ke] + selisih

if selisih < 0:
    allocate total_efektif in order:
        1. Pabrik, up to porsi_pabrik_base
        2. Owner, up to porsi_owner_base
        3. Perusahaan, up to porsi_perusahaan_base
```

When `qty_total` is zero, `total_efektif` is zero and no division is performed.

## Required invariants

The calculation may be saved only when all invariants pass:

```text
qty_normal >= 0
qty_anulir + qty_bonus <= qty_total
porsi_owner_base >= 0
porsi_pabrik + porsi_owner + porsi_perusahaan == total_efektif
```

The former 1% remainder guard is disabled. A positive remainder is fully assigned to `sisa_ke`.
When the base split exceeds the effective total, the available value is distributed by the
Pabrik, Owner, then Perusahaan priority so no party receives a negative final portion.

## Negative Owner validation

If bonus production costs more than Owner earns from normal pieces, the calculator still shows
the mathematical result but blocks saving:

```text
Porsi Owner negatif (Rp X). Kurangi qty bonus atau sesuaikan rate.
```

The system must not silently reduce Owner to zero or move this shortage to Perusahaan.

## Example: Anulir 75 and Bonus 5

Inputs:

```text
qty_total       = 580
qty_anulir      = 75
qty_bonus       = 5
rate_pabrik     = Rp 250.000
rate_owner      = Rp 120.000
rate_perusahaan = Rp 30.000
total_produk    = Rp 232.000.000
sisa_ke         = perusahaan
```

Result:

```text
qty_normal      = 500
porsi_pabrik    = 505 × Rp 250.000
                 = Rp 126.250.000
porsi_owner     = 500 × Rp 120.000 - 5 × Rp 250.000
                 = Rp 58.750.000
porsi_perusahaan = 500 × Rp 30.000
                  = Rp 15.000.000
total_efektif    = Rp 200.000.000
```

## Remainder example

If the original total is Rp 232.500.000:

```text
total_efektif = round(500 × 232.500.000 / 580)
               = Rp 200.431.034

total_split_base = Rp 200.000.000
selisih          = Rp 431.034
```

With `sisa_ke = perusahaan`, the final Perusahaan portion becomes Rp 15.431.034.

## Shortage example

If the base portions are Pabrik Rp 450.000, Owner Rp 360.000, and Perusahaan Rp 90.000 while
the effective sales value is only Rp 800.000:

```text
total_split_base = Rp 900.000
selisih          = -Rp 100.000

porsi_pabrik     = Rp 450.000
porsi_owner      = Rp 350.000
porsi_perusahaan = Rp 0
total_split      = Rp 800.000
```

The configured `sisa_ke` is ignored for this shortage because the fixed priority applies.

## Manual overrides

The edit screen calculates the recommended portions whenever Qty Anulir or Sisa Ke changes.
A user may manually override a portion. Manual input is preserved while the live total and
difference are recomputed.

The backend does not trust client-derived quantities or totals. It reads bonus quantity from the
Sales Order product rows, derives the gross total from existing revenue-split fields, recalculates
Qty Efektif and Total Efektif, and only accepts manual portions whose sum equals the
server-calculated effective total.

## Precision

- Quantities are whole pieces.
- Money is calculated as whole Rupiah.
- Intermediate percentage or floating-point calculations are not used.
- Division occurs only for `total_efektif`, which is rounded once to the nearest Rupiah.
- `sisa_ke` absorbs a positive difference. A shortage uses the Pabrik, Owner, then Perusahaan
  priority so the total invariant remains exact.

## Existing fields and data

No schema migration or new field is required. The calculation reuses:

```text
product_quantity     = qty total
return_qty            = qty anulir
effective_qty         = qty normal after bonus and anulir
product_unit_price    = unit price used to derive the gross total
product_total_amount  = calculated effective total
```

Qty Bonus remains sourced from the matching Sales Order product rows. Historical Pabrik, Owner,
Perusahaan, and effective-total amounts are not rewritten automatically. Use **Reset to Default**
or edit the split when an existing row should adopt the new formula.

## Automated coverage

`tests/Unit/RevenueSplitCalculatorTest.php` covers:

1. Normal sale.
2. Anulir and bonus.
3. Anulir only.
4. Bonus only.
5. Fully canceled.
6. Negative Owner.
7. Rounding remainder.
8. Anulir, bonus, and remainder together.
9. Remainder allocation to a non-Perusahaan party.
10. Remainder above 1%.
11. Shortage allocation priority.
12. Sales value below the Pabrik base portion.
13. Invalid combined quantity.

## Operational note

Qty Anulir in this screen remains a revenue-split adjustment. Creating a Sales Return restores
stock, but does not automatically populate Qty Anulir. Connecting Return Management to financial
split adjustments is a separate workflow.
