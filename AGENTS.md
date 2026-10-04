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
   - Table Container: `.erp-main-card` with `padding: 0 !important; overflow: hidden; border-radius: 16px; border: 1px solid #E2E8F0; box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);`.
   - Table Headers (`th`): Clean `#F8FAFC` background, uppercase 0.73rem, font-weight 700, letter-spacing 0.05em, text `#475569`, generous padding `0.95rem 1.15rem;`.
   - Table Cells (`td`): Generous padding `0.95rem 1.15rem;` (never cramped 5px/8px padding!), clean borders `#F1F5F9`, row hover `#F9FBFA`.
   - Table Avatars: Always use 38px circular gradient avatar (`linear-gradient(135deg, #5B841E, #3D5A12)`) with crisp white bold initials. Never pale rectangular boxes.
   - Numbers & Monospace: Always use modern tabular monospace stack (`Consolas, 'SFMono-Regular', Menlo, Monaco, 'Liberation Mono', monospace`). Currency amounts must use rich slate `#0F172A`, emerald `#059669` or crimson `#B91C1C`. Never typewriter/purple fonts or raw `<code>` tags.
   - Filters: Always inline flex (`.erp-filter-form`), search icon positioned inside input (`.erp-search-wrap`), compact auto-width selects (`.erp-filter-select`), and right-aligned counter badge (`.erp-table-summary-count`).
   - Placeholders: Strictly use `Enter [Field Name]` format. Never include dummy/example names or numbers.

4. **No Status Field in Any Form (Create or Edit)**:
   - Status must **NEVER** appear as an input, select dropdown, or editable field in `create.blade.php` or `edit.blade.php`.
   - Every new record automatically defaults to `active` on the backend upon creation.
   - Status toggling is handled strictly via the vibrant status button on the table list (`PATCH toggle-status`) and the Danger Zone / Archive actions.
5. **Seeders Are Exclusively For Master Modules**:
   - Seeders must **ONLY** be created and called for Master modules (`Company`, `User`, `Vendor`, `Broker`, `Customer`, `Item`, `Unit`, `Account`).
   - Transaction modules (`Purchase Entry`, `WB Purchase Entry`, `Sales Entry`, `Orders`, `Vouchers`, etc.) must **NEVER** have database seeders. Transaction tables are populated solely through active user operations or feature test cases.

