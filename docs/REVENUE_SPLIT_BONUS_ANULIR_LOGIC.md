# Revenue Split: Bonus and Anulir Logic

## Status

This document defines the revenue-split calculation used by new or recalculated Sales Order
revenue splits. Pabrik is always paid its full configured obligation, including bonus production.
Owner and Perusahaan receive only the sales value that remains after that obligation.

This is a calculation-only change and requires no database migration. Existing stored split
amounts are not rewritten automatically. Resetting or editing an existing split recalculates it
with this logic.

## Business meaning

- **Qty Total**: original quantity, including normal, bonus, and later-anulir pieces.
- **Qty Bonus**: free pieces. They produce no revenue, but Pabrik is paid for producing them.
- **Qty Anulir**: canceled pieces. They produce no revenue and no party is paid for them.
- **Qty Efektif / Normal**: pieces that produce revenue.
- **Sisa Ke**: party receiving a positive difference between the effective Sales Order value and
  the configured per-piece rates. A negative difference keeps Pabrik whole, then distributes any
  available value to Owner and Perusahaan in that order.

Bonus production is a guaranteed **Pabrik** obligation:

1. Pabrik receives its configured rate for normal and bonus pieces.
2. Owner's base portion remains its configured rate for normal pieces; bonus is not directly
   subtracted from the Owner formula.
3. Perusahaan receives its configured rate only when value remains after Pabrik and Owner.
4. If the effective sales value is below the Pabrik obligation, the difference remains payable to
   Pabrik and the final Owner and Perusahaan portions are zero.

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
    qty_normal × rate_owner

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
    porsi_pabrik = porsi_pabrik_base
    available_amount = max(0, total_efektif - porsi_pabrik)
    porsi_owner = min(porsi_owner_base, available_amount)
    porsi_perusahaan = min(porsi_perusahaan_base, remaining available_amount)

required_split_total =
    max(total_efektif, porsi_pabrik_base)
```

When `qty_total` is zero, `total_efektif` is zero and no division is performed.

## Required invariants

The calculation may be saved only when all invariants pass:

```text
qty_normal >= 0
qty_anulir + qty_bonus <= qty_total
porsi_owner >= 0
porsi_perusahaan >= 0
porsi_pabrik >= porsi_pabrik_base
porsi_pabrik + porsi_owner + porsi_perusahaan == required_split_total
```

The former 1% remainder guard is disabled. A positive remainder is fully assigned to `sisa_ke`.
When the base split exceeds the effective total, Pabrik remains fully payable and the remaining
available value is distributed to Owner, then Perusahaan. If Pabrik alone exceeds the effective
total, `total_split` is allowed to exceed `total_efektif` by the unfunded Pabrik obligation.

## Per-product shortage priority

Bonus increases the Pabrik obligation without reducing the Owner base formula directly. When a
product's effective sales value cannot cover all base portions, allocation follows this order:

```text
1. Pabrik receives its full obligation.
2. Owner receives up to its base portion from the remaining value.
3. Perusahaan receives up to its base portion from what remains after Owner.
```

This waterfall is calculated independently for each product. Products are not pooled together.

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
porsi_owner     = 500 × Rp 120.000
                 = Rp 60.000.000
porsi_perusahaan_base = 500 × Rp 30.000
                       = Rp 15.000.000
total_efektif    = Rp 200.000.000

available after Pabrik = Rp 73.750.000
available after Owner  = Rp 13.750.000
porsi_perusahaan       = Rp 13.750.000
```

## Remainder example

For 580 normal units with no bonus and an original total of Rp 232.500.000:

```text
total_efektif = Rp 232.500.000

total_split_base = Rp 232.000.000
selisih          = Rp 500.000
```

With `sisa_ke = perusahaan`, the final Perusahaan portion becomes Rp 17.900.000.

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

## Bonus-only product example

For 20 bonus units of Soulmate Coffee with no normal sales:

```text
qty_total        = 20
qty_bonus        = 20
qty_normal       = 0
total_efektif    = Rp 0
rate_pabrik      = Rp 40.000
rate_owner       = Rp 24.000
rate_perusahaan  = Rp 16.000

porsi_pabrik     = 20 × Rp 40.000 = Rp 800.000
porsi_owner      = Rp 0
porsi_perusahaan = Rp 0
total_split      = Rp 800.000
```

The Rp 800.000 is an unfunded but still payable Pabrik obligation.

## Manual overrides

The edit screen calculates the recommended portions whenever Qty Anulir or Sisa Ke changes.
A user may manually override a portion. Manual input is preserved while the live total and
difference are recomputed.

The backend does not trust client-derived quantities or totals. It reads bonus quantity from the
Sales Order product rows, derives the gross total from existing revenue-split fields, recalculates
Qty Efektif, Total Efektif, the minimum Pabrik obligation, and the required split total. Manual
portions must keep Pabrik at or above its obligation and sum to the server-calculated required
split total.

## Precision

- Quantities are whole pieces.
- Money is calculated as whole Rupiah.
- Intermediate percentage or floating-point calculations are not used.
- Division occurs only for `total_efektif`, which is rounded once to the nearest Rupiah.
- `sisa_ke` absorbs a positive difference. During a shortage, Pabrik remains fully payable before
  any available value is assigned to Owner and Perusahaan.

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
6. Large bonus handled by the per-product priority.
7. Rounding remainder.
8. Anulir, bonus, and remainder together.
9. Remainder allocation to a non-Perusahaan party.
10. Remainder above 1%.
11. Shortage allocation priority.
12. Sales value below the Pabrik base portion.
13. Bonus-only product with a guaranteed Pabrik obligation.
14. Two products in one order remain independently calculated.
15. Invalid combined quantity.

## Operational note

Qty Anulir in this screen remains a revenue-split adjustment. Creating a Sales Return restores
stock, but does not automatically populate Qty Anulir. Connecting Return Management to financial
split adjustments is a separate workflow.
