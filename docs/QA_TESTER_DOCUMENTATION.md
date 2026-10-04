# Vikas Udhyog ERP - QA & Tester Master Documentation

> **Document Version:** 1.0.0  
> **Target Environment:** Local (Laragon / PHP 8.2-8.4 / MySQL) & Production (cPanel / Apache / MySQL)  
> **Automated Test Coverage:** 125 Tests | 462 Assertions | 100% Passing  
> **Last Updated:** October 2026

---

## 1. Executive Summary & Architecture Overview

Vikas Udhyog ERP is a specialized manufacturing, agro-commodity trading, and processing ERP built on Laravel 11. It manages the entire workflow of agricultural mandi procurement, raw material processing (henna/mehndi leaves, powders, herbal extracts), dual-rate billing (official bill vs. without-bill mandi transactions), inventory tracking, and accounts ledgers.

### Core Architecture Highlights
- **Framework:** Laravel 11.x (PHP 8.2 / 8.3 / 8.4)
- **Database Engine:** MySQL 8.x / MariaDB (Production), SQLite in-memory (Automated Testing)
- **Frontend / Styling:** Blade Views, Bootstrap 5 + AdminLTE components, custom design tokens (`public/admin/css/custom.css`), FontAwesome 6 icons
- **Brand Palette:** Primary Olive Green (`#5B841E`), Hover (`#4A6D18`), Slate Dark (`#0F172A`), Light Gray Table Headers (`#F8FAFC`)

---

## 2. Mandatory ERP Guidelines & Quality Standards

When testing any module in this system, verify strict compliance with the following architectural rules:

### A. The 4-View Standard Per Module
Every entity must provide 4 consistent views:
1. **`index.blade.php`**: KPI statistics ribbon (4 cards), inline search/filter toolbar (`.erp-table-filter-header`), edge-to-edge table (`.card.erp-main-card`), status toggling buttons, and pagination.
2. **`create.blade.php`**: 2-column section layout with iconified inputs, live preview card (`.erp-preview-card`), and sidebar actions (`.erp-sidebar-actions-card`).
3. **`edit.blade.php`**: 2-column layout matching create view, plus audit trail timestamps card and archive danger zone card.
4. **`show.blade.php`**: 360-degree profile view with circular avatar, unique code badge, status pill, 4-stat KPI ribbon, and tabbed/detailed breakdown.

### B. The "No Status in Form" Rule
- **Rule:** `status` must **NEVER** appear as an input, select dropdown, or editable field in `create.blade.php` or `edit.blade.php`.
- **Expected Behavior:** Every record automatically defaults to `active` upon creation.
- **Toggling:** Status changes are handled strictly via the status toggle button in the table list (`PATCH toggle-status`) or via the Danger Zone archive button.

### C. Seeder Rule for Data Integrity
- **Masters:** Seeders are exclusively for master modules (`Company`, `User`, `Vendor`, `Broker`, `Customer`, `Item`, `Unit`, `Account`, `Role`).
- **Transactions:** Transaction modules (`Purchase Entry`, `WB Purchase Entry`, `Sales Entry`, `WB Sales Entry`, `Vouchers`) must **NEVER** have database seeders. Transaction tables are populated solely through active user operations or feature test cases.

### D. 100% Dynamic Unit Master Integration
- Unit dropdowns and auto-selections across all forms must **NEVER** rely on hardcoded unit names (e.g., `'KG'`, `'PACKET'`, `'QUINTAL'`).
- The system must dynamically match values from the `Unit` master using:
  1. Unit `code` (e.g. `KG`, `LTR`, `TON`, `M`)
  2. Unit `name` (e.g. `Kilogram`, `Litre`, `Metric Ton`)
  3. Comma-separated `synonyms` (e.g. `KGS,KILO`, `LITRE,LITRES`, `MT,TONS`)
- Dropdowns must render compact unit codes (`KG`, `PKT`, `QTL`, etc.) to prevent table column cut-off.

---

## 3. Automated Test Suite Summary

All test suites are automated using PHPUnit and run against an isolated in-memory SQLite database (`phpunit.xml`).

```bash
# Run all automated tests
php artisan test

# Run tests with failure halt
php artisan test --stop-on-failure

# Run tests for a specific module
php artisan test --filter=ItemMasterTest
php artisan test --filter=SaleEntryTest
php artisan test --filter=ServerMigrationRunnerTest
```

### Test Suite Execution Matrix (125 Tests / 462 Assertions)

| Test File | Test Suite Focus | Test Cases | Assertions | Status |
|---|---|:---:|:---:|:---:|
| `AuthenticationTest.php` | Login, Logout, Auth Middleware, Session | 4 | 14 | PASS |
| `CompanyMasterTest.php` | Multi-company, code generator, switch active | 7 | 25 | PASS |
| `UserMasterTest.php` | User CRUD, password hashing, role linkage, toggle | 8 | 21 | PASS |
| `AccessLevelTest.php` | Custom roles, 28-module permission matrix, toggle | 4 | 12 | PASS |
| `VendorMasterTest.php` | Supplier CRUD, GSTIN, PAN, code generation, toggle | 9 | 25 | PASS |
| `BrokerMasterTest.php` | Broker commission %, brokerage type, balance, toggle | 9 | 25 | PASS |
| `CustomerMasterTest.php` | Customer CRUD, credit limit, receivables ledger | 9 | 26 | PASS |
| `UnitMasterTest.php` | Base units, derived units (e.g. 100 CM = 1 M), conversion | 9 | 25 | PASS |
| `UnitModelTest.php` | Unit model unit conversions, formula, synonyms match | 4 | 17 | PASS |
| `ItemMasterTest.php` | Product CRUD, stock calculation, Unit Master relation | 10 | 33 | PASS |
| `AccountMasterTest.php` | Bank/Cash accounts, opening balance, passbook ledger | 9 | 25 | PASS |
| `PurchaseEntryTest.php` | Inward purchase with bill, stock increase, balance update | 9 | 43 | PASS |
| `WBPurchaseEntryTest.php`| Without-bill purchase, direct mandi rate, inward stock | 9 | 42 | PASS |
| `SaleEntryTest.php` | Outward sale, stock check, over-stock validation | 7 | 35 | PASS |
| `WBSaleEntryTest.php` | Without-bill sale, outward stock deduction | 7 | 34 | PASS |
| `ServerMigrationRunnerTest.php` | Web migration runner `/run-migration`, status, seed | 4 | 14 | PASS |
| `ExampleTest.php` | Basic sanity verification | 3 | 5 | PASS |
| **TOTAL** | **Comprehensive Full System Coverage** | **125** | **462** | **100% PASS** |

---

## 4. Manual QA Test Scenarios & Step-by-Step Verification

### Test Scenario 1: Dynamic Unit Master Addition & Transaction Auto-Selection
**Goal:** Verify that creating a brand new unit in Unit Master makes it immediately available and auto-selectable in Item and Transaction forms without any code changes.

1. Navigate to **Masters &rarr; Unit Master** (`/admin/masters/unit`).
2. Click **"+ Add New Unit"** (`/admin/masters/unit/create`).
3. Fill in the fields:
   - **Unit Name:** `Litre`
   - **Unit Code:** `LTR`
   - **Synonyms:** `LITER,LITRES,LT,L`
   - **Is Base Unit:** Checked
4. Click **"Save Unit of Measure"**. Verify unit appears in the list as `active`.
5. Navigate to **Masters &rarr; Item Master &rarr; "+ Add New Item"** (`/admin/masters/item/create`).
6. In **Primary Unit of Measure (UOM)** select dropdown, verify that `Litre (LTR)` is present.
7. Create an item:
   - **Name:** `Organic Henna Oil (Cold Pressed)`
   - **Unit:** Select `Litre (LTR)`
   - **GST Rate:** `5.00`
   - **Purchase Rate:** `500.00`
   - **Sale Rate:** `750.00`
8. Click **"Save Item Profile"**.
9. Now navigate to **Transactions &rarr; Purchase Entry &rarr; "+ Add Purchase Entry"** (`/admin/transactions/purchase-entry/create`).
10. In Line Items, select `Organic Henna Oil (Cold Pressed)`.
    - **Expected Result:** The `UNIT TYPE` dropdown must automatically switch to `LTR`.
    - **Expected Result:** The dropdown text displays `LTR` (compact, not truncated).

---

### Test Scenario 2: Outward Stock Availability & UI Table Alignment
**Goal:** Verify that when outward quantity exceeds available inward purchase stock, a warning is displayed and table row inputs remain perfectly aligned horizontally.

1. Navigate to **Transactions &rarr; Sales Entry &rarr; "+ Add Sales Entry"** (`/admin/transactions/sales-entry/create`).
2. Select a Customer and Company.
3. In **Outward Product Line Items**, select an item that has limited stock (e.g. Stock: `18.00 KG`).
4. In **DISPATCH QTY**, enter a quantity exceeding the stock (e.g. `250`).
5. **Verify UI Behavior:**
   - Red warning appears beneath the input: `▲ Exceeds available stock (18.00 KG)`.
   - Inspect adjacent columns (Item select, HSN, GST %, Unit Type, Rates, Amounts, and Delete button).
   - **Expected Result:** All inputs remain strictly top-aligned (`vertical-align: top`). No column jumps down or looks displaced.
6. Attempt to submit the form with excessive quantity.
   - **Expected Result:** Form submission fails validation with an error message: *"Dispatched quantity for [Item Name] exceeds available stock"*.

---

### Test Scenario 3: Server Database Migration & Schema Update Via Web Route
**Goal:** Verify that database schema changes can be applied via the web route without terminal or SSH access.

1. Open your browser and navigate to:
   ```text
   http://localhost/run-migration?action=status
   ```
   *(Or on live server: `https://your-domain.com/run-migration?action=status`)*
2. **Verify Output:**
   - Modern dark-themed terminal console window appears.
   - Shows `$ php artisan migrate:status`.
   - Lists all migration files and their status (`Ran`).
3. Now test the migration-only action:
   ```text
   http://localhost/run-migration?action=migrate-only
   ```
   - **Verify Output:** Returns status `Execution Completed Successfully` with `$ php artisan migrate`.
4. Test the Unit Master synchronizer:
   ```text
   http://localhost/run-migration?action=seed
   ```
   - **Verify Output:** Returns `$ php artisan db:seed --class=UnitSeeder`.
5. Navigate to **Admin Settings &rarr; Backup & Restore** (`/admin/settings/backup-restore`):
   - Verify the **"Server Database Migration"** card is present.
   - Click **"Run Updated Migrations Only"** and verify it opens the console runner.

---

### Test Scenario 4: Access Level & Dynamic Role Permissions
**Goal:** Verify custom role creation and granular permission toggling for all 28 modules.

1. Navigate to **Masters &rarr; Access Level** (`/admin/masters/access-level`).
2. Verify all built-in roles appear (Super Administrator, Admin, Store Manager, Purchase Executive, Sales Executive, Accounts Manager, Quality Inspector).
3. Click **"+ Add Custom Role"**:
   - **Role Name:** `Mandi Procurement Clerk`
   - **Theme Color:** `#5B841E`
   - **Badge Title:** `Procurement Branch`
4. Click **"Create Role"**. Verify role card appears dynamically.
5. In the Permission Matrix for `Mandi Procurement Clerk`:
   - Enable `View` and `Add` for `purchase_entry` and `wb_purchase_entry`.
   - Disable `Delete` for all modules.
6. Click **"Save Role Permissions"**.
7. Reload the page and verify all saved checkboxes remain exactly as configured.

---

### Test Scenario 5: Financial Balances & Stock Movement Integrity
**Goal:** Verify dual-ledger consistency on Purchases and Sales.

1. Record current vendor balance and item stock.
2. Create a **Purchase Entry** with:
   - Item Quantity: `100 KG` at `₹50.00/KG` = `₹5,000.00`.
3. Check **Inventory &rarr; Stock Overview**:
   - Stock must have increased by exactly `100.00 KG`.
4. Check **Masters &rarr; Vendor Master &rarr; View Vendor**:
   - Vendor current balance must have increased by the purchase total amount.
5. Delete or Archive the Purchase Entry:
   - **Expected Result:** Stock decreases back by `100.00 KG`, and vendor balance is automatically reverted.

---

## 5. Tester Checklist Matrix

Use this quick checklist during regression and release testing:

- [ ] **Forms:** No `status` input in any Create or Edit form across all modules.
- [ ] **Table Views:** Standard KPI grid (4 cards) present on all `index.blade.php` pages.
- [ ] **Search & Filters:** Real-time search, status filter (`All`, `Active`, `Inactive`), and pagination work properly.
- [ ] **Table Row Styling:** Clean padding (`0.4rem 0.45rem`), `#F8FAFC` headers, and no broken inputs.
- [ ] **Unit Master:** Adding custom units with synonyms auto-selects in purchases, sales, and items.
- [ ] **Numbers & Currency:** Formatted with `₹` and 2 decimal places using monospace font stack (`Consolas`, `monospace`).
- [ ] **Stock Validation:** Cannot dispatch more than available physical stock in Sales Entry or WB Sales Entry.
- [ ] **Server Migrations:** `/run-migration` executes cleanly without requiring terminal SSH access.
- [ ] **Automated Tests:** All 125 test cases pass with `php artisan test`.

---

## 6. Common Issues & Solutions For Testers

| Issue Observed | Root Cause | Solution |
|---|---|---|
| `Access denied for user '@'localhost'` | `.env` has unquoted password containing `#` symbol (e.g. `DB_PASSWORD=etY%^L%#YAY5`) | Wrap password in double quotes: `DB_PASSWORD="etY%^L%#YAY5"`. |
| Unit dropdown shows `Packet (I...` cut-off | Long text label in compact column | Keep label as `{{ strtoupper($u->code ?: $u->name) }}` (e.g. `PKT`, `KG`). |
| Rows jump vertically on stock warning | Table cells used `vertical-align: middle` | `.erp-items-table tbody td` has `vertical-align: top !important;`. |
| Migration fails on production | Laravel prompts for confirmation in production environment | Use `/run-migration` which automatically passes `--force`. |
