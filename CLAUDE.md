# CLAUDE.md: ucp (`staging-ucp`)

A Laravel 12 accounting/ERP app for the ucp company. It is **live in production**. It covers sales (SO → Sales Delivery → Sales Invoice, revenue split, returns), purchases, expenses and Kas Besar, inventory/production/packing, fixed assets, manual journals and four GL reports.

## Repository and branches
- This repo (`yustinWill/ucp-journaling`) holds only ucp. Since 2026-11-16 the riyo app has its own repo, `joessimanta/riyo-app`. The two codebases split at `daaa958f` (2026-02-14) and have diverged a lot. **Never merge riyo into ucp.** To port a fix, cherry-pick one small commit with `-x`, then check it against this code. Many riyo services don't exist here (see "Not in ucp").
- `staging-ucp` (default) deploys to staging. `PROD-UCP` deploys to production.
  - `PROD-UCP` also gets direct commits and cherry-picks, so compare the two by patch, not by SHA: `git log --cherry-mark origin/PROD-UCP...origin/staging-ucp`.
  - Agents branch from `staging-ucp` (`ucp/<topic>`) and push only that branch. The user does every merge into `staging-ucp` and `PROD-UCP`.
- **Migrations:** never edit a committed one. The Spatie migration is `2026_02_07_234433_create_permission_tables.php`.

## Stack
- **Backend:** PHP 8.2, Laravel 12, mostly the query builder on raw tables. The Eloquent models are `User` (with `SoftDeletes`) and `Reimbursement`. `Module`, `Permission`, `Role` and `RolePermission` are legacy models whose tables were dropped (see Permissions).
- **Database: MySQL 8 is required.** Queries use `JSON_TABLE` and `JSON_ARRAYAGG`. `config/database.php` defaults to sqlite, so set `DB_CONNECTION=mysql`.
- **UI:** server-rendered Blade on the **Vuexy Bootstrap 5 HTML/jQuery template**, with no Vue.
  - Vendor libraries are static files under `public/assets/vendor/`. Shared JS is in `public/custom_js/`. Page JS is written inline in `@section('js_script')`.
  - `layouts/header.blade.php:10` calls `@vite(['resources/js/app.js'])` (axios plus Echo/Reverb). Run `npm run build` or `npm run dev` first, or every `layouts.default` page fails.
  - `layouts/script.blade.php` loads `custom_js` with relative paths (`../../custom_js/x.js`) and no cache-busting version, so browsers see a deploy only after a hard refresh.
- **Packages:** spatie/laravel-permission 6, spatie/laravel-backup 9, laravel/reverb, laravel/telescope, laravel-lang.

## Commands
```
composer install && npm ci
composer dev                 # serve + queue:listen + pail + vite
php artisan reverb:start     # live notifications; not part of composer dev
npm run build                # -> public/build (gitignored)
composer test                # config:clear + php artisan test (sqlite :memory:)
vendor/bin/pint --dirty      # format only touched files
php artisan budget:recalculate   # recompute budget remaining values
```
- **Tests:** `phpunit.xml` forces sqlite `:memory:` and sets no `APP_KEY`. Feature tests build their tables with `Schema::create` in `setUp()` (copy `tests/Feature/DeletedUserAccessTest.php`), so SQL that runs only on MySQL can't be tested there.
  - The suites are small: `DashboardAccessTest`, `DeletedUserAccessTest`, `SalesOrderRevertControllerTest`, `RevenueSplitCalculatorTest` and the two `ExampleTest`s.
  - `tests/System/*.php` are standalone scripts that **change data in the real `.env` database**. Never run them casually. `tests/StockAdjustmentTest.php` is not in any suite.
- The repo has no ESLint, Prettier, PHPStan or CI.
- **Deploy:** no deploy script is in the repo. Keep hosts and credentials out of it. Take a `backup:run --only-db` before running a data migration on production. ucp runs a queue worker and Reverb under supervisor on staging, so restart both after a deploy. Production caches its routes (`route:cache`), so a deploy that touches routes must re-run it or the change is silently ignored.

## Database
- **Naming:** `m_` = master, `t_` = transaction, `l_` = log/closing, and an `f_` prefix marks a foreign-key column. Users live in `users`. Master rows use `status` ACTIVE/INACTIVE (`App\Enums\GeneralStatuses`).
- **Deletes are hard deletes.** The exceptions are `users` (soft delete, `2026_08_29_000000_add_deleted_at_to_users_table.php`) and `t_reimbursements`.
- **Session config:** at login, `AuthenticateUserAction` copies every `m_global_configs` row into the session and sets `user_company_code` from the user's `default_company` row in `m_user_companies`. A user without that row cannot log in. A config change takes effect at the user's next login.
- Don't run `RoleManagementSeeder` or `ReturnManagementSeeder`: they write pre-Spatie RBAC rows into tables that no longer exist.
- `truncate all data latest 12-02-2026.txt` and `delete so query.sql` are hand-run SQL. Never run them against a shared or production database.

## Architecture and conventions
- **Write flow** (reference: `app/Http/Controllers/MasterData/TagController.php`):
  1. The controller validates inline with `$request->validate(...)`.
  2. It calls `app(Create<X>Action::class)->execute($data)`.
  3. The action wraps its service writes and the `<X>AuditLogService` call in `DB::transaction`.
  4. The controller answers with JSON `{message}`.
  - Error handling: `ValidationException` → HTTP 400 with `ValidationHelper::formatErrors`. `AppException(ErrorCodes::X)` → its own status. `\Throwable` → `DiscordNotifier::reportException` and a 500.
  - Add new cases to `app/Enums/ErrorCodes.php` and `SuccessCodes.php`.
  - Some newer controllers (`KasBesarController`) return `{success, message}`.
- **Multi-company scoping is manual. There is no global scope.** Every query filters on `company_code = session('user_company_code')`, and every insert sets the code. From CLI or a queue job, set the session and `Auth` yourself.
- **Routes:** everything runs in the `web` group (session, CSRF and `EnsureUserIsActive`). New routes go inside the existing `Route::middleware(['auth'])` group in `routes/master_data.php` or `routes/main_transaction.php`.
  - Each entity has a `$<entity>_segment` variable, which must also be added to the group closure's `use (...)` list.
  - Route names follow `<area>.<entity>.<action>`.
- **Views:** `resources/views/pages/<area>/<entity>/{index,add,edit,detail}.blade.php`, extending `layouts.default`.
  - The layouts yield only `page_title`, `content`, `ext_js_script` and `js_script`. There is no `@stack` (`@push` does nothing) and no CSS section, so page CSS goes in an inline `<style>` inside `content`.
  - List pages use server-side DataTables with the protocol written by hand in the service's `findAllDatatable`.
  - Use the classes `global-select2`, `global-single-date-picker` and `currency-input` plus `initCleaveCurrency(container)`.
- **UI text** is hard-coded Bahasa Indonesia. Never translate `value=` codes.
- **Menu:** `resources/views/layouts/menu.blade.php` is written by hand, and each item is wrapped in `@can('<entity>.view')`.

## Permissions
- **Spatie is the only working permission system.** `2026_02_07_234157_remove_legacy_role_permission_tables.php` dropped `roles`/`permissions`/`role_permissions`/`modules` (the legacy tables, later recreated by Spatie under its own schema) and `users.role_id`.
  - `RolePermissionMiddleware` (`role.permission`) and `PermissionGateProvider`'s `<module>.read|write|delete|approval` gates still query the old tables. They allow everything, because `isset($user->role_id)` is now false. Don't build on them.
  - Spatie names are `<entity>.<verb>` (`view`, `create`, `edit`, `delete`, plus custom verbs such as `inventory.dispose` and `warehouse.stock_transfer`). They are seeded by migrations: copy `2026_09_10_000001_add_sales_order_revert_view_permission.php`.
- **Enforcement is on the routes and in the UI.** Every signed-in non-GET route, and every add/edit/import/payment page, has `->middleware('can:<entity>.<verb>')`: `create` for add/store/save/import, `edit` for edit/update and status changes (cancel, complete, reversal, SD accept/confirm, SI and expense payment, overhead settle), `delete` for delete/destroy, `view` for exports, plus custom verbs (`inventory.depreciation`/`dispose`, `warehouse.stock_transfer`, `reimbursement_approval.create` for approve/reject). Route names that differ from the permission: `budged` → `budget`, `category` → `product_category`, `unit_conversion` → `unit`, `purchase` (mock) → `purchase_order`, `overhead_absorption` → `production`, Kas Besar uses `kas_besar.view`/`create`. `PermissionRoutesTest` fails on any new unguarded write route; only the three `transaction_number` form helpers (`check_duplicate`, `generate_number`, `preview_format`) and `kas_besar.get_latest_saldo` stay open.
  - Menu items and buttons use `@can`; controller flags (`canAddTags`, `canEdit<X>`, …) come from `can()`. `journal.*` and `credit_memo.*` are seeded by `2026_11_17_000001_add_journal_and_credit_memo_permissions.php` (granted to role id 1).
  - A new write route needs its own `->middleware('can:<entity>.<verb>')` (the test enforces it), and page flags must come from `can()`, never a hardcoded `true`.
  - `PermissionHelper::hasPermission(User $user, string $permission)` calls `hasPermissionTo`, which throws on an unseeded name. `getViewPermissions` swallows that.
- **Dashboard:** `/dashboard` is limited to Spatie role ids 1 and 2 (owner/admin, `User::DASHBOARD_ROLE_IDS`, `EnsureDashboardAccess`). Login sends everyone else to `/home` (`LoginController.php:29`).
- **`EnsureUserIsActive`** (appended to `web`) logs out a soft-deleted user or one whose `status` isn't `ACTIVE`, answering 401 JSON or redirecting to login. `DeletedUserAccessTest` covers it.

## General ledger and reports
- `t_accounting_journals` holds the GL, one row per line: `reference_no`, `f_accounting_feature_code` (`app/Enums/JournalFeatureCodes.php`), `debit_credit` `'D'`/`'C'`, and `company_code` (not `f_company_code`).
- **Only two things post to the GL:**
  - manual journals (`JLL`, `Create/UpdateAccountingJournalAction`);
  - fixed-asset postings (`InventoryAssetJournalService`, e.g. `INDEP`).
  - Sales, purchase, expense and stock documents do **not** post journals, even though `JournalFeatureCodes` defines `SIJL`, `PDJL`, `EXPJL` and others.
- `AccountingJournalService::create()` validates nothing: it doesn't check balance or COA. Make each caller balance its own rows.
- `updateSetInactive($reference_no)` flips rows to INACTIVE but isn't scoped by company or feature code. Reports read only ACTIVE rows.
- Reports (`routes/report.php`): Jurnal Umum, Buku Besar, Laba Rugi and Neraca. The logic is in `app/Domains/{JournalUmumReport,BukuBesarReport,LabaRugiReport}/` and `app/Traits/AccountingReportingLogic.php`.

## Stock
- Quantity comes from `t_stock_movement_logs` (`StockMovementLogsService`, `app/Services/StockMovementLogService.php`). Cost layers are FIFO in `t_product_stock_batches`: consume them only through `BatchManagementService::consumeBatches()`.

## ucp-only features
- **Sales-order revert** (`main_transaction.sales_order_revert.*`, `DeleteSalesOrderAction`): hard-deletes an SO together with its Sales Deliveries and Sales Invoices in one transaction, restores stock and batch consumption, and writes `t_sales_order_revert_logs`.
  - The list needs `sales_order_revert.view`, the action needs `sales_order.delete`, and the log page needs `sales_order_revert_log.view`.
  - It is different from `cancel/{id}`, which only sets `CANCELLED` and only when the SO has no SD or SI.
- **Sales-delivery accept** (`main_transaction.sales_delivery.accept`, `t_sales_deliveries.is_accepted`).
- **Kas Besar** (`KasBesarController`, `t_kas_besar`, staging only):
  - A running cash balance per company: each row stores `saldo_before`/`saldo_after`, computed from the latest row by id (entry order, not transaction date).
  - Every row goes through `KasBesarService::record`, which takes the number first (locking the counter row, so writes are serialized) and refuses a deduction below zero. A row dated in an earlier month continues that month's own sequence (max existing number + 1) instead of using the current counter; top-ups can't be future-dated.
  - It's topped up on its own page. A Kas/Tunai expense (`t_expenses.payment_method_type`) is deducted by `PaymentExpenseAction` when it is paid, so a pay-later expense deducts nothing until each payment, and an edit changes no deduction.
  - It has its own `kas_besar` transaction number. It posts no journal.
- **Revenue split** on SO lines (`SalesOrderRevenueSplitService`, `RevenueSplitCalculator`; see `docs/REVENUE_SPLIT_BONUS_ANULIR_LOGIC.md`), and **Topup**.

## Gotchas
- **Schedules live in `routes/console.php`** (`backup:run` 00:00, `backup:clean` 01:00, `telescope:prune` 02:00). Laravel 12 never loads `app/Console/Kernel.php`, so don't recreate it. The servers need the `schedule:run` cron. Backups go to the same server's `local` disk (`storage/app/private/<APP_NAME>/`), and without `backup:clean` they fill it (production reached 99% on 2026-11-16).
- **`TransactionNumberService::generateNumber`** locks the module's `m_transaction_numbers` row (`lockForUpdate`) and throws a plain `\Exception` when that row is missing. A new numbered module needs a seed migration (e.g. `2026_05_24_000003_seed_kas_besar_transaction_number.php`).
- **`env()` returns null under `config:cache`.** Use `config()` in code and views.
- **Docs are mostly stale dev logs.** The root `*.md` files and most of `docs/` (especially the `*PERMISSION*` ones, written for the dropped RBAC) are out of date: trust the code over them.
- `public/custom_assets/` is gitignored. Provide `company_logo.png` there on each machine.

## Not in ucp (riyo-only, don't assume these exist)
`AuthorizeRoute`/`PERMISSION_ENFORCE`, `FormToken`, Tanggal Penguncian (`PeriodLockService`), COA mapping (`CompanyCoaMappingService`), auto-journals from documents, moving-average costing (`InventoryCostService`), bank reconciliation, the Mekari report groups and `report_tools.js`, dot-matrix prints (`DocumentPrintService`), and the riyo test guards (`RouteActionsExistTest`, `PickerCodeFormatTest`, `ScriptTemplateLiteralTest`).
