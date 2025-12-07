# Budget Module Restructure Plan

## Overview
Changing budget perspective from **"1 budget → many periods"** to **"1 period → many budgets"**

---

## Database Structure Changes

### Old Structure:
```
m_budgeds (main table)
├── id
├── budged_name
├── created_by, created_at
└── updated_by, updated_at

m_budgeds_history (child table)
├── id
├── budged_id (FK to m_budgeds)
├── period_start_date
├── period_end_date
├── budged_limit_value
├── budged_remaining_value
├── created_by, created_at
└── updated_by, updated_at
```

### New Structure:
```
m_budget_periods (main table) ← NEW TABLE
├── id
├── period_start_date
├── period_end_date
├── created_by, created_at
└── updated_by, updated_at

m_budgeds (child table) ← RESTRUCTURED
├── id
├── f_budget_period_id (FK to m_budget_periods)
├── budged_name
├── budged_limit_value
├── budged_remaining_value
├── created_by, created_at
└── updated_by, updated_at
```

---

## Migration Steps

### 1. Run Migration
```bash
php artisan migrate
```

The migration will:
1. Create `m_budget_periods` table
2. Extract unique periods from `m_budgeds_history`
3. Backup old `m_budgeds` → `m_budgeds_old`
4. Create new `m_budgeds` with `f_budget_period_id`
5. Migrate all data to new structure
6. Backup `m_budgeds_history` → `m_budgeds_history_old`

---

## Files That Need to be Updated

### ✅ Already Updated (Frontend):
- [x] `resources/views/pages/master_data/budged/add.blade.php` ← Uses new perspective
- [x] `resources/views/pages/master_data/budged/index.blade.php` ← Shows periods with budget counts
- [x] `resources/views/pages/master_data/budged/edit.blade.php` ← Edits period with its budgets
- [x] `resources/views/layouts/menu.blade.php` ← Icon updated to tabler-chart-line
- [x] `app/Domains/Budged/Actions/CreateBudgedAction.php` ← Created (needs update for new DB)
- [x] `app/Http/Controllers/MasterData/BudgedController.php` ← save() method updated (needs more updates)

### ⏳ Need to Update (Backend):

#### Service Layer
- [ ] **`app/Domains/Budged/Services/BudgedService.php`**
  - Update all methods to work with new structure
  - Remove: `validatePeriodOverlap()` - no longer needed
  - Update: `findAllDatatable()` - return periods with budget counts/totals
  - Add: `findByPeriodId($periodId)` - get budgets for a period
  - Add: `findPeriodById($periodId)` - get period details
  - Update: `create()` - create budget with period_id
  - Remove: `createHistory()`, `updateHistory()`, `deleteHistory()`

#### Action Layer
- [ ] **`app/Domains/Budged/Actions/CreateBudgedAction.php`** ← Update for new DB structure
  - Change to create period first
  - Then create multiple budgets for that period

- [ ] **Create: `app/Domains/Budged/Actions/UpdateBudgedPeriodAction.php`**
  - Update budgets within a period
  - Add new budgets to period
  - Update existing budget values

#### Controller Layer
- [ ] **`app/Http/Controllers/MasterData/BudgedController.php`**
  - ✅ `save()` - already updated, needs DB structure change
  - [ ] `index_data_table()` - return periods with:
    - period_id
    - period_start_date, period_end_date
    - budget_count (count of budgets)
    - total_limit (sum of limits)
    - total_remaining (sum of remaining)

  - [ ] `add()` - remove budgetlist parameter (no longer needed)

  - [ ] `edit($id)` - $id = period_id, pass:
    - period_id
    - period_start_date
    - period_end_date
    - canWrite

  - [ ] `update()` - remove (not needed for periods, they don't change)

  - [ ] **Add: `get_budgets_by_period($periodId)`**
    ```php
    public function get_budgets_by_period($periodId)
    {
        try {
            $budgets = app(BudgedService::class)->findByPeriodId($periodId);
            return response()->json([
                'success' => true,
                'data' => $budgets,
            ], SuccessCodes::GENERAL_SUCCESS->httpStatus());
        } catch (\Throwable $e) {
            // error handling
        }
    }
    ```

  - [ ] **Add: `update_period_budgets(Request $request)`**
    ```php
    public function update_period_budgets(Request $request)
    {
        try {
            $validated = $request->validate([
                'period_id' => 'required|exists:m_budget_periods,id',
                'budgets' => 'required|array|min:1',
                'budgets.*.id' => 'nullable|exists:m_budgeds,id',
                'budgets.*.budged_name' => 'required|string|max:255',
                'budgets.*.budged_limit_value' => 'required|numeric|min:0',
                'budgets.*.budged_remaining_value' => 'required|numeric|min:0',
            ]);

            app(UpdateBudgedPeriodAction::class)->execute($validated);

            return response()->json([
                'success' => true,
                'message' => 'Perubahan berhasil disimpan',
            ], SuccessCodes::GENERAL_SUCCESS->httpStatus());
        } catch (\Throwable $e) {
            // error handling
        }
    }
    ```

  - [ ] Remove methods:
    - `add_periode()`
    - `update_periode()`
    - `delete_periode()`
    - `validate_period()`

#### Routes
- [ ] **`routes/web.php`**
  ```php
  // Add new routes
  Route::get('master_data/budged/get-budgets-by-period/{periodId}',
      [BudgedController::class, 'get_budgets_by_period'])
      ->name('master_data.budged.get-budgets-by-period');

  Route::post('master_data/budged/update-period-budgets',
      [BudgedController::class, 'update_period_budgets'])
      ->name('master_data.budged.update-period-budgets');

  // Remove old routes
  // - master_data.budged.add-periode
  // - master_data.budged.update-periode
  // - master_data.budged.delete-periode
  // - master_data.budged.validate-period
  ```

#### View Files
- [ ] **`resources/views/pages/master_data/budged/detail.blade.php`**
  - Change to show period details with list of budgets
  - Not budget details with list of periods

---

## Data Migration Example

### Before Migration:
```
m_budgeds:
ID | budged_name        | created_at
1  | Marketing Budget   | 2025-01-01
2  | Operations Budget  | 2025-01-01

m_budgeds_history:
ID | budged_id | period_start | period_end | limit    | remaining
1  | 1         | 2025-01-01   | 2025-01-31 | 10000000 | 8000000
2  | 2         | 2025-01-01   | 2025-01-31 | 15000000 | 12000000
3  | 1         | 2025-02-01   | 2025-02-28 | 10000000 | 10000000
```

### After Migration:
```
m_budget_periods:
ID | period_start | period_end | created_at
1  | 2025-01-01   | 2025-01-31 | 2025-01-01
2  | 2025-02-01   | 2025-02-28 | 2025-01-01

m_budgeds:
ID | f_budget_period_id | budged_name        | limit    | remaining
1  | 1                  | Marketing Budget   | 10000000 | 8000000
2  | 1                  | Operations Budget  | 15000000 | 12000000
3  | 2                  | Marketing Budget   | 10000000 | 10000000

m_budgeds_old (backup):
[old data preserved]

m_budgeds_history_old (backup):
[old data preserved]
```

---

## Testing Checklist

After migration and code updates:

### Functional Tests:
- [ ] Can create new period with multiple budgets
- [ ] Can view list of periods with budget counts/totals
- [ ] Can edit period and update budgets
- [ ] Can add new budget to existing period
- [ ] Can update existing budget values
- [ ] Can view period details with all budgets
- [ ] Budget remaining values calculate correctly
- [ ] Flatpickr date inputs work with yyyy-mm-dd format

### Data Integrity:
- [ ] All old budgets migrated correctly
- [ ] All old periods migrated correctly
- [ ] All limit values preserved
- [ ] All remaining values preserved
- [ ] Relationships correct (period → budgets)
- [ ] No data loss during migration

### UI/UX:
- [ ] Index page shows periods correctly
- [ ] Add page works with repeater
- [ ] Edit page loads budgets for period
- [ ] Detail page shows period with budgets
- [ ] Currency formatting works (Rp)
- [ ] Date format displays yyyy-mm-dd
- [ ] Table columns aligned properly
- [ ] Icons display correctly

---

## Rollback Plan

If migration fails:
```bash
php artisan migrate:rollback
```

This will:
1. Drop new `m_budgeds` table
2. Restore `m_budgeds_old` → `m_budgeds`
3. Restore `m_budgeds_history_old` → `m_budgeds_history`
4. Drop `m_budget_periods` table

---

## Cleanup After Verification

After confirming migration is successful:
```sql
DROP TABLE IF EXISTS m_budgeds_old;
DROP TABLE IF EXISTS m_budgeds_history_old;
```

---

## Summary

**Before**: Budget-centric (1 budget → many periods)
**After**: Period-centric (1 period → many budgets)

This makes more sense because:
- Users think in terms of periods (monthly/quarterly planning)
- Multiple budgets are managed together for same period
- Easier to get overview of period's total budget
- Aligns with how add/edit forms now work
