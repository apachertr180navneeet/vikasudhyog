# Mandatory UI Structure Standard for All New & Existing Modules

All existing and newly created modules in Vikas Udhyog ERP (Masters, Transactions, Inventory, Production, Accounts, Reports) MUST strictly follow this exact structural architecture and design system. No exceptions.

---

## 1. Directory & Routing Architecture
Each module must follow a RESTful, 4-view CRUD structure under `resources/views/admin/[category]/[module]/`:
- `index.blade.php`: Listing directory with KPI cards, search/filter toolbar, data table, and pagination.
- `create.blade.php`: 2-column layout with sectioned inputs, real-time live preview card, and actions sidebar.
- `edit.blade.php`: 2-column layout with sectioned inputs, real-time live preview card, audit trail, and danger zone.
- `show.blade.php`: Full 360-degree profile with hero badge, quick KPI stats, statutory cards, and audit details.

---

## 2. Standard View Layouts

### A. Index / Directory Page
1. **Top Bar (`.erp-page-top-bar`)**:
   - Breadcrumb trail with chevrons (`.erp-breadcrumb-trail`)
   - Page title with primary icon (`.erp-page-title`) and descriptive subtitle (`.erp-page-subtitle`)
   - Header actions (`Print List`, `Add New [Entity]`)
2. **4-Card KPI Statistics Grid (`.erp-kpi-grid`)**:
   - 4 accent-colored cards (`.erp-kpi-primary`, `.erp-kpi-success`, `.erp-kpi-purple`, blue border) with icon boxes and formatted counts/currencies.
3. **Container Card (`.card.erp-main-card`)**:
   - `padding: 0 !important; border-radius: 16px; overflow: hidden;`
4. **Search & Filter Header (`.erp-table-filter-header`)**:
   - `<form class="erp-filter-form">`
   - Search input wrap (`.erp-search-wrap`) with icon inside (`.erp-search-icon`) and input (`.erp-search-input`)
   - Dropdown filters (`.erp-filter-select`) with auto width and custom SVG chevron
   - Submit filter button (`.erp-btn-filter`)
   - Clear filter button (`.erp-btn-filter-clear`)
   - Summary record badge on far right (`.erp-table-summary-count`)
5. **Data Table (`.table-responsive > table.custom-table`)**:
   - Avatar circle initials
   - Status toggle pill:
     - Active: `.erp-status-btn.erp-status-btn-active` with emerald pulsing dot (`.erp-status-dot-green`)
     - Inactive: `.erp-status-btn.erp-status-btn-inactive` with red dot (`.erp-status-dot-red`)
   - Actions: Standard action icons wrapper (`.erp-actions-cell`) with individual buttons (`.erp-table-action-icon`, `.erp-table-action-icon-danger` for delete). Must include View, Edit, and Delete (with SweetAlert2).
6. **Card Footer Pagination**:
   - Clean pagination links and per-page entries summary.

---

### B. Create & Edit Pages
1. **2-Column Grid Layout (`.erp-form-layout-2col`)**:
   - **Main Form Column (Left - 65% / 70%)**:
     - 3-5 cards (`.card.erp-form-section-card`) grouping logical fields (Basic Info, Statutory/Tax, Contact/Address, Financial/Bank).
     - Section headers with icon box (`.erp-form-section-icon-box`), title, and hint.
     - Iconified form controls (`.erp-field-icon-wrap` with `.erp-field-icon`).
     - Helper subtitles (`.erp-field-hint`).
     - Real-time client-side validation hints.
   - **Sidebar Column (Right - 35% / 30%)**:
     - **Live Dynamic Preview Card (`.erp-preview-card`)**:
       - Avatar circle with auto-generated initials
       - Live firm/entity title & code badge
       - Dynamic badges reflecting status, tax, city, and primary contact
       - Real-time JavaScript listener updates on field keystrokes
     - **Sidebar Actions Card (`.erp-sidebar-actions-card`)**:
       - Full-width submit button (`.erp-btn-action-submit`)
       - Full-width cancel button (`.erp-btn-action-cancel`)
     - **Audit Trail Card** (Edit mode only)
     - **Danger Zone Card** (Edit mode only): Soft delete/archive with SweetAlert2 confirmation.

---

### C. Show / Profile Detail Page
1. **Breadcrumb Trail** & Header Actions (`Print Profile`, `Edit Profile`, `Back to Directory`).
2. **Hero Header Banner**:
   - Large 64px avatar initial circle
   - Entity name, unique system code, and vibrant status badge (`.erp-status-btn-active`)
   - Quick action call / email buttons
3. **KPI Highlight Ribbon**:
   - 4 accent metric cards displaying financials, balances, terms, and timestamps.
4. **2-Column Profile Body**:
   - Left: Statutory registrations, addresses, procurement/operational notes.
   - Right: Bank account details, key personnel contacts, system audit stamps.

---

## 3. Formatting & Behavioral Rules
- **Brand Colors**: Primary `#5B841E` (Olive Green) / Hover `#4A6D18`. Accent colors: `#10B981` (Emerald), `#8B5CF6` (Purple), `#3B82F6` (Blue).
- **Placeholder Rule**: Strictly use `Enter [Field Name]` (e.g. `Enter full name`, `Enter PAN number`). NEVER write mock example text (e.g. `e.g. Navneet Sharma` or dummy numbers).
- **No Status Field in Forms**: Status must **NEVER** appear as an input, select dropdown, or editable field in `create.blade.php` or `edit.blade.php`. All new records automatically default to `active` on the backend. Status is exclusively toggled via the Table List status button (`PATCH toggle-status`) and the Danger Zone / Archive action.
- **Codes & Financials**: Always render codes, GSTIN, PAN, Bank Accounts, IFSC, and amounts using monospace typography.
- **Reference Specification**: Refer to `docs/ERP_UI_DESIGN_SYSTEM.md` for complete code snippets and markup blueprints.
