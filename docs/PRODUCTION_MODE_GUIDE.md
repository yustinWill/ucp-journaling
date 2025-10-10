# Production Mode Quick Reference Guide

## 🎯 Overview

The production system supports **two calculation modes** to handle different business scenarios:

### Mode 1: Quantity Planned (Default) 📊
**When to use**: When you know how many units you want to produce

**Workflow**:
1. Set production quantity
2. System calculates material requirements
3. System checks stock availability
4. Proceed with production

**Example**: "I want to produce 100 bottles of water"

---

### Mode 2: Material-Based 🔧
**When to use**: When you have specific materials available and want to maximize production

**Workflow**:
1. Input available material quantities
2. System calculates maximum producible units
3. System shows leftover materials (4 decimals)
4. Proceed with production

**Example**: "I have 500 bottles, 500 caps, 450 labels - how many can I produce?"

---

## 🔄 How to Switch Modes

1. Open production form: `/main_transaction/production/add`
2. Find **"Mode Produksi"** toggle switch
3. Toggle to switch between modes

**Default**: Quantity Planned
**Toggled**: Material-Based Input

---

## 📝 Usage Examples

### Example 1: Quantity Planned Mode

**Scenario**: Produce 10 benches

**Recipe**: 1 Bench = 4 legs + 1 seat + 8 screws

**Steps**:
1. Keep toggle OFF (default mode)
2. Select "Bench" as bundle product
3. Enter "10" in Quantity Planned
4. System shows:
   - 40 legs needed
   - 10 seats needed
   - 80 screws needed
   - Stock status for each
5. Submit production order

---

### Example 2: Material-Based Mode

**Scenario**: Have limited materials, want to maximize production

**Available Materials**:
- 20 legs
- 10 seats
- 100 screws

**Steps**:
1. Toggle switch ON (Material-Based mode)
2. Select "Bench" as bundle product
3. Input materials:
   - Legs: 20
   - Seats: 10
   - Screws: 100
4. System calculates:
   - **Max Production: 5 benches** (limited by legs)
   - Leftover: 0 legs, 5 seats, 60 screws
5. Quantity Planned auto-fills: 5
6. Submit production order

---

## 🎨 Status Badges Explained

### Material-Based Mode

| Badge | Meaning | Action |
|-------|---------|--------|
| 🔘 **Waiting Input** | No material entered yet | Enter material quantity |
| ✅ **Exact Match** | Perfect match, no leftover | Optimal! Proceed |
| ⚠️ **Has Leftover** | Material remaining after production | Normal, leftover tracked |
| ❌ **Exceeds Stock** | Input > available stock | Reduce material input |

### Quantity Planned Mode

| Badge | Meaning | Action |
|-------|---------|--------|
| ✅ **Available** | Stock is sufficient | Proceed normally |
| ❌ **Insufficient Stock** | Stock not enough | Reduce quantity or add stock |

---

## 🧮 Calculation Logic

### Material-Based Mode Algorithm

```
For each material component:
  1. Calculate: possible_production = material_input ÷ qty_per_bundle
  2. Round down to whole number (Math.floor)

Find minimum possible_production across all materials = MAX PRODUCTION

For each material:
  3. Calculate: used_material = max_production × qty_per_bundle
  4. Calculate: leftover = material_input - used_material (4 decimals)
```

**Example**:
```
Recipe: 1 Bench = 4 legs, 1 seat, 8 screws

Input: 20 legs, 10 seats, 100 screws

Calculations:
  Legs: 20 ÷ 4 = 5.00 → 5 benches possible
  Seats: 10 ÷ 1 = 10.00 → 10 benches possible
  Screws: 100 ÷ 8 = 12.50 → 12 benches possible

Bottleneck: Legs (minimum = 5)
Result: Can produce 5 benches

Leftovers:
  Legs: 20 - (5 × 4) = 0.0000
  Seats: 10 - (5 × 1) = 5.0000
  Screws: 100 - (5 × 8) = 60.0000
```

---

## ⚠️ Important Notes

### Material-Based Mode
- ✅ Quantity Planned field is **disabled** (auto-calculated)
- ✅ Leftover materials tracked with **4-decimal precision**
- ✅ Leftover data saved for stock opname process
- ✅ Cannot submit if calculated quantity = 0
- ✅ Cannot submit if any material exceeds available stock

### Quantity Planned Mode
- ✅ Quantity Planned field is **editable**
- ✅ Can proceed with warning if stock insufficient
- ✅ Standard production workflow

---

## 🔍 Validation Rules

### Common Validation (Both Modes)
- Production Number: Required
- Production Date: Required
- Bundle Product: Required
- Warehouse: Required
- PIC (Person in Charge): Required

### Mode-Specific Validation

**Quantity Planned Mode**:
- Quantity Planned: Required, must be > 0

**Material-Based Mode**:
- At least one material input: Required
- All material inputs: Must not exceed stock
- Calculated quantity: Must be ≥ 1

---

## 📊 Where is Data Stored?

### Database: t_productions
```sql
production_mode: 'QUANTITY_PLANNED' or 'MATERIAL_BASED'
leftover_materials: JSON (Material-Based mode only)
```

### Database: t_production_items
```sql
material_input: Original input amount (Material-Based mode)
leftover_amount: Leftover with 4 decimals (Material-Based mode)
```

---

## 💡 Tips & Best Practices

1. **Use Quantity Planned Mode** when:
   - You have a production target
   - Materials are abundant
   - Standard production planning

2. **Use Material-Based Mode** when:
   - Materials are limited
   - Want to maximize output from available materials
   - Need accurate leftover tracking for reconciliation

3. **Leftover Materials**:
   - Automatically saved with 4-decimal precision
   - Used for stock opname (inventory reconciliation)
   - Viewable in production detail page

4. **Stock Validation**:
   - Material-Based mode is stricter (blocks if exceeds stock)
   - Quantity Planned mode is flexible (warns but allows)

---

## 🆘 Troubleshooting

### "Tidak Bisa Produksi" Error
**Problem**: Calculated quantity = 0

**Solution**:
- Check material inputs are sufficient for at least 1 unit
- Example: If recipe needs 4 legs, input minimum 4 legs

### "Material Melebihi Stock" Error
**Problem**: Material input > available stock

**Solution**:
- Reduce material input to match available stock
- Or update stock master data if incorrect

### Quantity Planned is Disabled
**Status**: Normal in Material-Based mode

**Solution**: This is expected behavior - toggle back to Quantity Planned mode if you need to edit it

---

## 📞 Support

For questions or issues:
1. Check this guide first
2. Review session documentation: `.claude-sessions/session_20251010_dual_production_mode.md`
3. Contact system administrator

---

**Last Updated**: October 10, 2025
**Version**: 1.0
