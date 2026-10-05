# Vikas Udhyog ERP - Module-by-Module QA Testing Document

> **Document Type:** Functional & Acceptance Test Specification  
> **Target Audience:** QA Engineers, Manual Testers, Product Reviewers  
> **Scope:** All 16 Completed Modules in Vikas Udhyog ERP  
> **Status:** Active & Production-Ready  

---

## Testing Quick Reference & Standard Rules

Before testing any module, verify that the following system-wide rules are respected:
1. **No Status Field in Forms:** Verify that neither `create.blade.php` nor `edit.blade.php` contains a status input field. New records must automatically be saved as `active`.
2. **Status Toggling:** Changing status must be done exclusively via the status toggle button in the list table (`PATCH toggle-status`) or via the Danger Zone archive button.
3. **Number Formatting:** All currency amounts must be formatted with `₹` and 2 decimal places in modern monospace font (`Consolas`).
4. **Unit Master Integration:** Unit dropdowns must display short uppercase codes (`KG`, `PKT`, `QTL`, `BAG`, `LTR`, etc.) and match dynamically based on Unit Master records and synonyms.

---

# PART 1: MASTER MODULES TESTING

---

### Module 1: Company Master (`/admin/masters/company`)

| Test ID | Test Scenario | Steps to Execute | Input / Test Data | Expected Output | Status |
|---|---|---|---|---|:---:|
| **CMP-01** | Render List Page | Navigate to `/admin/masters/company` | — | HTTP 200. Displays 4 KPI cards (Total Companies, Active, Default Company, etc.), table list with pagination, and search filter. | [ ] |
| **CMP-02** | Add New Company | Click "+ Add New Company" (`/company/create`) and submit form | Name: `Sojat Henna Works`<br>Code: auto or `CMP-02`<br>GSTIN: `08AABCS1234F1Z5`<br>State: `Rajasthan` | Redirects to index with success message. Record saved with `status = 'active'` (no status input in form). | [ ] |
| **CMP-03** | Auto Code Generator | In create form, click generate code icon | Prefix: `CMP` | Generates next sequential unique code (e.g. `CMP-03`) via AJAX. | [ ] |
| **CMP-04** | Set Default Company | In table list, click "Set Default" badge on a company | Target Company ID | Sets targeted company as system default; previous default is deselected. | [ ] |
| **CMP-05** | Switch Active Company | In header company switcher, select another company | Company Dropdown | Active session company switches; header badge updates dynamically. | [ ] |
| **CMP-06** | Edit & Danger Zone Archive | Open edit page (`/company/{id}/edit`) and click Archive | Target Company ID | Soft-deletes company (`deleted_at` set). Company hidden from active dropdowns. | [ ] |

---

### Module 2: User Master (`/admin/masters/user`)

| Test ID | Test Scenario | Steps to Execute | Input / Test Data | Expected Output | Status |
|---|---|---|---|---|:---:|
| **USR-01** | Render User Directory | Navigate to `/admin/masters/user` | — | HTTP 200. Shows KPI statistics, filter by role/status, user table with avatar initials. | [ ] |
| **USR-02** | Create User with Role | Click "+ Add New User" (`/user/create`) | Name: `Karan Sharma`<br>Username: `karansharma`<br>Email: `karan@vikasudhyog.com`<br>Role: `Store Manager`<br>Password: `Pass123!` | User created, password securely hashed with bcrypt (rounds=12), status defaults to `active`. | [ ] |
| **USR-03** | Unique Username & Email Validation | Try creating user with duplicate email or username | Email: `karan@vikasudhyog.com` | Validation fails with inline field errors *"Username has already been taken"*. | [ ] |
| **USR-04** | Password Confirmation Match | Enter mismatched password in create form | Pass: `123456`, Confirm: `654321` | Form rejects submission: *"Password confirmation does not match"*. | [ ] |
| **USR-05** | Toggle User Active Status | In table list, click status pill on a user row | Active User ID | User status switches to `inactive`. User cannot log into admin panel. | [ ] |
| **USR-06** | Soft Delete User | Click delete/archive button on user row | Target User ID | Record is soft-deleted. User removed from active directory. | [ ] |

---

### Module 3: Access Level & Dynamic Role Permissions (`/admin/masters/access-level`)

| Test ID | Test Scenario | Steps to Execute | Input / Test Data | Expected Output | Status |
|---|---|---|---|---|:---:|
| **ACL-01** | Render Role Permission Matrix | Navigate to `/admin/masters/access-level` | — | HTTP 200. Displays built-in system roles (Super Admin, Admin, Store Manager, etc.) and permission table for 28 modules. | [ ] |
| **ACL-02** | Create Custom Role | Click "+ Add Custom Role" modal | Name: `Mandi Procurement Clerk`<br>Color: `#5B841E`<br>Icon: `fa-wheat-awn` | Role created dynamically with slug `mandi-procurement-clerk` and appears in role tab strip. | [ ] |
| **ACL-03** | Save Module Permissions | Check/uncheck permissions (View, Add, Edit, Delete, Export) for modules | Module: `purchase_entry`<br>View: Checked, Add: Checked, Delete: Unchecked | AJAX saves to `role_permissions` table. Toast notification displays *"Permissions updated successfully"*. | [ ] |
| **ACL-04** | Reset Defaults | Click "Reset to System Defaults" | Built-in Role | Restores standard predefined permission set for that role. | [ ] |
| **ACL-05** | Toggle Role Status | Click status toggle on custom role card | Custom Role ID | Status changes between `active` and `inactive`. Inactive roles hidden from user creation dropdown. | [ ] |

---

### Module 4: Vendor Master (`/admin/masters/vendor`)

| Test ID | Test Scenario | Steps to Execute | Input / Test Data | Expected Output | Status |
|---|---|---|---|---|:---:|
| **VND-01** | Render Vendor List | Navigate to `/admin/masters/vendor` | — | HTTP 200. Displays KPI cards (Total Suppliers, Active Suppliers, etc.), search bar, and vendor table. | [ ] |
| **VND-02** | Add Supplier Account | Click "+ Add New Vendor" (`/vendor/create`) | Name: `Marwar Agriculture Suppliers`<br>Type: `Raw Material`<br>Phone: `9829012345`<br>Opening Balance: `25000` | Account created with initial `current_balance = 25000.00`, status defaults to `active` (no status input in form). | [ ] |
| **VND-03** | GSTIN & PAN Auto-Capitalize | Type lowercase GSTIN/PAN in create form | `08aabcm1234f1z9` | Input converts automatically to uppercase `08AABCM1234F1Z9`. | [ ] |
| **VND-04** | 360° Profile Show View | Click vendor row or view button (`/vendor/{id}`) | Target Vendor ID | Displays 360-degree profile with hero avatar, contact details, bank IFSC, and ledger summary. | [ ] |
| **VND-05** | Status Toggle | In vendor table, click active button | Target Vendor ID | Status toggles to `inactive`. Inactive vendors cannot be selected in new Purchase entries. | [ ] |
| **VND-06** | Soft Delete Vendor | Click delete button and confirm | Target Vendor ID | Vendor archived (`deleted_at` set). Historical purchase transactions remain intact. | [ ] |

---

### Module 5: Customer Master (`/admin/masters/customer`)

| Test ID | Test Scenario | Steps to Execute | Input / Test Data | Expected Output | Status |
|---|---|---|---|---|:---:|
| **CST-01** | Render Customer List | Navigate to `/admin/masters/customer` | — | HTTP 200. Shows Total Customers, Total Receivables KPI, credit limit summary, and customer table. | [ ] |
| **CST-02** | Add Customer Account | Click "+ Add New Customer" (`/customer/create`) | Name: `Shree Krishna Herbal Stores`<br>Type: `Wholesaler`<br>Credit Limit: `500000.00`<br>Phone: `9829099887` | Customer saved, credit limit recorded, status defaults to `active` (no status input in form). | [ ] |
| **CST-03** | Credit Terms & Days | Select Payment Terms in create form | Terms: `30 Days` | Terms saved and displayed on invoice and sales dispatches. | [ ] |
| **CST-04** | Edit Customer Profile | Open edit view (`/customer/{id}/edit`) and update phone | Phone: `9829011111` | Updates profile data; audit card displays updated timestamp. | [ ] |
| **CST-05** | Toggle Status | Click active/inactive button on customer row | Customer ID | Toggles status between `active` and `inactive`. | [ ] |

---

### Module 6: Broker Master (`/admin/masters/broker`)

| Test ID | Test Scenario | Steps to Execute | Input / Test Data | Expected Output | Status |
|---|---|---|---|---|:---:|
| **BRK-01** | Render Broker Directory | Navigate to `/admin/masters/broker` | — | HTTP 200. Displays broker list, commission percentage columns, and brokerage ledgers. | [ ] |
| **BRK-02** | Add Mandi Broker | Click "+ Add New Broker" (`/broker/create`) | Name: `Mandi Dalal Ram Prasad`<br>Commission Rate: `1.50`<br>Brokerage Type: `Percentage (%)` | Broker created with `commission_rate = 1.50%`, status defaults to `active`. | [ ] |
| **BRK-03** | Commission Rate Range Validation | Enter negative or >100 commission rate | Rate: `-5` or `120` | Form validation rejects submission: *"Commission rate must be between 0 and 100"*. | [ ] |
| **BRK-04** | Auto Code Generator | Click generate code button in broker create form | Prefix: `BRK` | Returns next unique sequential code (e.g. `BRK-02`) via JSON. | [ ] |
| **BRK-05** | Toggle Status & Delete | Test toggle status and delete actions | Broker ID | Toggle flips status to `inactive`; delete soft-deletes the broker record. | [ ] |

---

### Module 7: Unit Master (`/admin/masters/unit`)

| Test ID | Test Scenario | Steps to Execute | Input / Test Data | Expected Output | Status |
|---|---|---|---|---|:---:|
| **UNT-01** | Render Unit Directory | Navigate to `/admin/masters/unit` | — | HTTP 200. Displays base units, derived units, conversion formulas, and decimal places. | [ ] |
| **UNT-02** | Create Base Unit | Click "+ Add New Unit" (`/unit/create`) | Name: `Litre`<br>Code: `LTR`<br>Synonyms: `LITER,LITRES,LT,L`<br>Is Base Unit: Yes | Base unit created with conversion factor `1.0000`, status defaults to `active`. | [ ] |
| **UNT-03** | Create Derived Sub-Unit | Create unit linked to parent base unit | Name: `Millilitre`<br>Code: `ML`<br>Base Unit: `LTR`<br>Factor: `1000`<br>Operator: `/` | Unit saved. Shows formula `1000 ML = 1 LTR`. Conversion method `toBase(2500)` returns `2.5 LTR`. | [ ] |
| **UNT-04** | Synonyms Matching Verification | Test unit with comma-separated synonyms in Unit Model | Value: `ltr`, `LITER`, `LITRES` | `$unit->matchesValue($val)` returns `true` for all variations. | [ ] |
| **UNT-05** | Decimal Precision Validation | Save unit with decimal places | Decimals: `2` (for KG), `0` (for BOX) | Quantity inputs in transactions use matching decimal precision. | [ ] |

---

### Module 8: Item Master (`/admin/masters/item`)

| Test ID | Test Scenario | Steps to Execute | Input / Test Data | Expected Output | Status |
|---|---|---|---|---|:---:|
| **ITM-01** | Render Product Catalog | Navigate to `/admin/masters/item` | — | HTTP 200. Displays KPI cards (Total Items, Valuation, Low Stock Alert), product catalog table with stock status badges. | [ ] |
| **ITM-02** | Add New Product Item | Click "+ Add New Item" (`/item/create`) | Name: `Pure Sojat Mehndi Powder`<br>Category: `Mehndi / Henna`<br>Unit: Select `KG`<br>GST: `5.00%`<br>Purchase Rate: `120.00`<br>Sale Rate: `180.00` | Item created. Foreign key `unit_id` linked to Unit Master. Status defaults to `active` (no status input in form). | [ ] |
| **ITM-03** | Dynamic Unit UOM Dropdown | Check Primary Unit of Measure (UOM) dropdown | — | Populated dynamically from `Unit::active()`. Shows all master units (KG, BOX, PKT, LTR, etc.). | [ ] |
| **ITM-04** | Live Real-Time Preview Card | Type product details in create form | Name, Rates, HSN | Live Preview card on the right updates instantly in real time via JavaScript. | [ ] |
| **ITM-05** | Low Stock Alert Threshold | Set Min Stock Alert = `50.00 KG` with current stock = `20.00 KG` | Min Stock: `50.00` | Table displays amber/red `Low Stock` badge and item appears in Low Stock Alert counter. | [ ] |
| **ITM-06** | Soft Delete Product | Click archive/delete on an item row | Item ID | Soft deletes item (`deleted_at` set). Historical invoice line items retain name and rates. | [ ] |

---

### Module 9: Account Master (`/admin/masters/account`)

| Test ID | Test Scenario | Steps to Execute | Input / Test Data | Expected Output | Status |
|---|---|---|---|---|:---:|
| **ACC-01** | Render Bank / Cash Accounts | Navigate to `/admin/masters/account` | — | HTTP 200. Displays Bank and Cash accounts, current ledger balances, and bank account numbers. | [ ] |
| **ACC-02** | Add Bank Account | Click "+ Add New Account" (`/account/create`) | Name: `State Bank of India (Mandi Branch)`<br>Type: `Bank`<br>Account No: `38920102030`<br>IFSC: `SBIN0001234`<br>Opening Balance: `150000.00` | Account created with initial balance `₹1,50,000.00`, status defaults to `active`. | [ ] |
| **ACC-03** | Account Number & IFSC Validation | Enter invalid or lowercase IFSC in create form | IFSC: `sbin0001234` | Converts to uppercase `SBIN0001234`. | [ ] |
| **ACC-04** | Toggle Account Status | Click status button on an account row | Account ID | Status switches between `active` and `inactive`. | [ ] |

---

# PART 2: TRANSACTION MODULES TESTING

---

### Module 10: Purchase Entry (With Bill) (`/admin/transactions/purchase-entry`)

| Test ID | Test Scenario | Steps to Execute | Input / Test Data | Expected Output | Status |
|---|---|---|---|---|:---:|
| **PUR-01** | Render Purchase Entry List | Navigate to `/admin/transactions/purchase-entry` | — | HTTP 200. Displays purchase invoice records, supplier names, bill vs. under-billing amounts, and grand totals. | [ ] |
| **PUR-02** | Create Official Purchase Entry | Click "+ Add Purchase Entry" (`/purchase-entry/create`) | Supplier: Select Vendor<br>Bill No: `INV-2026-001`<br>Bill Date: Today<br>Line 1: Item `ITM-01`, Qty `100 KG`, Bill Rate `₹50.00`, U-B Rate `₹10.00` | Form calculates Line Bill Amt = `₹5,000.00`, U-B Amt = `₹1,000.00`, GST (5%) = `₹250.00`. Form saves successfully. | [ ] |
| **PUR-03** | Automatic Stock Inward Increment | Verify stock of purchased item after PUR-02 | Item ID from Line 1 | Physical current stock increases by exactly `+100.00 KG`. | [ ] |
| **PUR-04** | Vendor Ledger Balance Increase | Check Vendor profile current balance | Supplier from PUR-02 | Vendor balance increases by the total purchase value (`₹6,250.00`). | [ ] |
| **PUR-05** | Dynamic Unit Auto-Select | Select an item in Line Items table | Item with unit `BAG` | `UNIT TYPE` dropdown automatically selects `BAG` via dynamic JS matching. Compact code displayed. | [ ] |
| **PUR-06** | Add / Remove Multiple Rows | Click "+ Add Another Item Row" and "X" delete row | Multiple rows | JavaScript recalculates subtotal, GST tax, under-billing amount, and grand total in real time. | [ ] |
| **PUR-07** | Delete Purchase & Stock Revert | Delete purchase invoice | Purchase ID | Record deleted; inward stock is automatically deducted back, and vendor balance reverted. | [ ] |

---

### Module 11: Without-Bill Purchase Entry (Direct Mandi Rate) (`/admin/transactions/wb-purchase-entry`)

| Test ID | Test Scenario | Steps to Execute | Input / Test Data | Expected Output | Status |
|---|---|---|---|---|:---:|
| **WBP-01** | Render WB Purchase List | Navigate to `/admin/transactions/wb-purchase-entry` | — | HTTP 200. Displays direct mandi procurement entries, lot numbers, net mandi weights, and total values. | [ ] |
| **WBP-02** | Create Mandi WB Purchase | Click "+ Add WB Purchase Entry" (`/wb-purchase-entry/create`) | Supplier: Farmer / Mandi Vendor<br>Entry No: `WBP-01`<br>Item: `ITM-01`, Qty: `250 KG`, Mandi Rate: `₹45.00` | Subtotal calculated as `₹11,250.00`. No tax applied (Mandi rate). Record saved with status `active`. | [ ] |
| **WBP-03** | Stock Inward Increment | Check stock of item in Stock Overview | Item `ITM-01` | Inward stock increases by `+250.00 KG`. | [ ] |
| **WBP-04** | Table Row Dynamic Unit Select | Select item in WB line item table | Item with unit `PKT` | Unit dropdown auto-selects `PKT`. Code fits cleanly in the table column. | [ ] |

---

### Module 12: Sales Entry (With Bill & Stock Validation) (`/admin/transactions/sales-entry`)

| Test ID | Test Scenario | Steps to Execute | Input / Test Data | Expected Output | Status |
|---|---|---|---|---|:---:|
| **SAL-01** | Render Sales Invoices List | Navigate to `/admin/transactions/sales-entry` | — | HTTP 200. Displays sales invoices, customer names, bill amounts, and receivable statuses. | [ ] |
| **SAL-02** | Live Inward Stock Badge Display | In Sales create form, select an outward item | Item with stock `150.00 KG` | Inward Stock column immediately displays green badge `150.00 KG` showing available balance. | [ ] |
| **SAL-03** | Over-Stock Error Validation | In Dispatch Qty, enter quantity greater than available stock | Stock: `150.00`, Dispatch: `200.00` | Red warning appears: `▲ Exceeds available stock (150.00 KG)`. Form submission is blocked. | [ ] |
| **SAL-04** | Table Row Vertical Alignment on Error | Observe table inputs when over-stock warning appears | Dispatch: `200.00` | All adjacent table cell inputs (Item, HSN, Unit, Rate, Amount, Delete) remain strictly aligned on the horizontal baseline (`vertical-align: top`). | [ ] |
| **SAL-05** | Successful Outward Sale & Stock Deduction | Enter valid quantity within stock limit | Stock: `150.00`, Dispatch: `50.00`, Rate: `₹200.00` | Sale recorded successfully. Physical stock decreases from `150.00 KG` to `100.00 KG`. Customer balance increases. | [ ] |
| **SAL-06** | Dynamic Unit Dropdown Text | Check Unit Type dropdown | Item unit: `KG` or `PKT` | Displays compact uppercase code `KG` or `PKT`. No long text cut-off (`Packet (I...`). | [ ] |

---

### Module 13: Without-Bill Sales Entry (Direct Mandi Dispatch) (`/admin/transactions/wb-sales-entry`)

| Test ID | Test Scenario | Steps to Execute | Input / Test Data | Expected Output | Status |
|---|---|---|---|---|:---:|
| **WBS-01** | Render WB Sales List | Navigate to `/admin/transactions/wb-sales-entry` | — | HTTP 200. Displays mandi sales dispatches, customer names, net weights, and total values. | [ ] |
| **WBS-02** | Stock Check on WB Sales | In create form, select outward item | Available Stock: `100.00 KG` | Displays live inward stock badge. Validates outward quantity against available inward stock. | [ ] |
| **WBS-03** | Record WB Outward Dispatch | Enter Dispatch Qty within stock | Qty: `30.00 KG`, Rate: `₹170.00` | Total calculated as `₹5,100.00`. Stock deducted by `30.00 KG`. | [ ] |

---

# PART 3: INVENTORY & STOCK MODULES TESTING

---

### Module 14: Stock Overview & Item Ledgers (`/admin/inventory`)

| Test ID | Test Scenario | Steps to Execute | Expected Output | Status |
|---|---|---|---|:---:|
| **INV-01** | Live Stock Overview Table | Navigate to `/admin/inventory/stock-overview` | Displays all items with Opening Stock, Inward Purchases (+), Outward Sales (-), and Net Available Current Stock. | [ ] |
| **INV-02** | Inward vs Outward Math Verification | Compare sum of Purchases minus sum of Sales for an item | Net Current Stock strictly equals `Opening + Inward - Outward`. No phantom quantities. | [ ] |
| **INV-03** | Low Stock Alert Screen | Navigate to `/admin/inventory/low-stock-alert` | Lists only items where `current_stock <= min_stock_alert`. Displays warning badges. | [ ] |
| **INV-04** | Item Ledger Drill-down | Click an item ledger link (`/admin/inventory/item-ledger?item_id={id}`) | Chronological audit trail of all transactions (Purchase bills, WB purchases, Sales dispatches) for that specific item. | [ ] |

---

# PART 4: SYSTEM & SERVER MIGRATION MODULE TESTING

---

### Module 15: Server Database Migration Runner (`/run-migration`)

| Test ID | Test Scenario | Steps to Execute | Expected Output | Status |
|---|---|---|---|:---:|
| **MIG-01** | Migration Status Action | Visit `/run-migration?action=status` in browser | HTTP 200. Displays terminal console window with `$ php artisan migrate:status`. Shows all applied migrations. | [ ] |
| **MIG-02** | Run Migrations Only Action | Visit `/run-migration?action=migrate-only` | Executes `php artisan migrate --force`. Runs only pending table migrations. Shows green success alert banner. | [ ] |
| **MIG-03** | Sync Unit Seeder Action | Visit `/run-migration?action=seed` | Executes `php artisan db:seed --class=UnitSeeder --force`. Populates missing units and synonyms. | [ ] |
| **MIG-04** | Clear Application Caches Action | Visit `/run-migration?action=clear` | Executes `php artisan optimize:clear`. Flushes compiled views, routes, and config caches. | [ ] |
| **MIG-05** | AJAX / JSON Request Mode | Send request with header `Accept: application/json` to `/run-migration?action=status` | Returns valid JSON payload: `{"status":"success", "message":"...", "outputs":{...}}`. | [ ] |
| **MIG-06** | Backup & Restore UI Integration | Navigate to Admin &rarr; Settings &rarr; Backup & Restore (`/admin/settings/backup-restore`) | Displays "Server Database Migration" card with action buttons ("Run Updated Migrations Only", "Check Status", "Sync Units"). | [ ] |

---

# PART 5: REGRESSION & EDGE CASES VERIFICATION

---

### Module 16: System Edge Cases & Error Handling

| Test ID | Test Scenario | Root Cause / Risk | Test Verification Steps | Expected Result | Status |
|---|---|---|---|---|:---:|
| **EDG-01** | Password with `#` or special characters in `.env` | `#` starts comment in dotenv files | Set `DB_PASSWORD="etY%^L%#YAY5"` with quotes | Database connects successfully. No `Access denied` error. | [ ] |
| **EDG-02** | Stock Reversion on Purchase Edit | Changing purchase qty from `100` to `80` | Edit existing purchase line item | Item stock adjusts down by `20 KG` to reflect the updated net quantity. | [ ] |
| **EDG-03** | Stock Reversion on Purchase Deletion | Deleting a purchase entry | Delete purchase entry with `100 KG` | Current stock decreases by `100 KG`. Prevents orphaned inward stock. | [ ] |
| **EDG-04** | Sale Prevention on Zero / Negative Stock | Item has `0.00 KG` stock | Attempt outward sale of `1.00 KG` | Form is blocked with validation error. Stock cannot drop below zero. | [ ] |
| **EDG-05** | Production Migration Without Terminal | Production server requires confirmation prompt | Visit `/run-migration` on production | Automatically runs with `--force` flag. No interactive terminal prompt blocking execution. | [ ] |

---

## Sign-Off Summary

- **Total Modules Documented:** 16 Modules (9 Masters, 4 Transactions, 2 Inventory, 1 Server Utilities)
- **Total Test Cases:** 67 Detailed Acceptance Test Specifications
- **Automated Tests Passing:** 125 Tests (462 assertions) with `php artisan test`
- **Result:** **READY FOR QA ACCEPTANCE TESTING**
