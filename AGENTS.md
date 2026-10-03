# Vikas Udhyog ERP - Agent & Development Guidelines

## Unified UI Structure Standard for All Modules
Whenever creating or updating ANY module in this ERP (Masters, Transactions, Inventory, Manufacturing, Accounts, Reports):

1. **Strictly adhere to the design system blueprint**:
   - Location: `docs/ERP_UI_DESIGN_SYSTEM.md`
   - Agent Rule: `.agents/rules/ui-structure-standard.md`

2. **Every module MUST provide the 4 standard views**:
   - `index.blade.php`: Top bar with breadcrumb trail, 4-card KPI statistics grid, inline search/filter toolbar (`.erp-table-filter-header`), edge-to-edge table (`.card.erp-main-card`), status toggle buttons (`.erp-status-btn-active` / `.erp-status-btn-inactive`), and pagination.
   - `create.blade.php`: 2-column layout with section cards (`.erp-form-section-card`), iconified inputs, live real-time preview card (`.erp-preview-card`), and sidebar action buttons (`.erp-sidebar-actions-card`).
   - `edit.blade.php`: 2-column layout matching create view, plus audit trail timestamps card and danger zone archive card.
   - `show.blade.php`: 360-degree profile view with hero initial avatar, unique code badge, status pill, 4-stat KPI ribbon, and 2-column detailed breakdown.

3. **Styling & Typography Guidelines**:
   - Brand Primary: `#5B841E` (Olive Green) / Hover `#4A6D18`.
   - Table Container: `.erp-main-card` with `padding: 0 !important; overflow: hidden; border-radius: 16px;`.
   - Filters: Always inline flex (`.erp-filter-form`), search icon positioned inside input (`.erp-search-wrap`), compact auto-width selects (`.erp-filter-select`), and right-aligned counter badge (`.erp-table-summary-count`).
   - Placeholders: Strictly use `Enter [Field Name]` format. Never include dummy/example names or numbers.
   - Monospace: Always use monospace font for codes, GSTIN, PAN, Bank Accounts, and monetary values.

4. **No Status Field in Any Form (Create or Edit)**:
   - Status must **NEVER** appear as an input, select dropdown, or editable field in `create.blade.php` or `edit.blade.php`.
   - Every new record automatically defaults to `active` on the backend upon creation.
   - Status toggling is handled strictly via the vibrant status button on the table list (`PATCH toggle-status`) and the Danger Zone / Archive actions.
   - Live preview cards may display status as a read-only badge, but forms must not contain an editable status field.

