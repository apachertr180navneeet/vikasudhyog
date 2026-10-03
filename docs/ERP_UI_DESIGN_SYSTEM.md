# Vikas Udhyog ERP - Unified UI Architecture & Design System

This document establishes the mandatory visual and structural standards for **all current and future modules** across Vikas Udhyog ERP (Masters, Transactions, Inventory, Reports, Settings).

---

## 1. Core Visual Foundations

- **Brand Primary Color**: `#5B841E` (Olive Green) / Hover: `#4A6D18`
- **Surface & Cards**: Pure White `#FFFFFF` on Canvas Background `#F8FAFC`, with soft border `#E2E8F0` and subtle elevation `box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);`
- **Border Radius**: Cards: `14px` - `16px`; Inputs: `8px`; Pills/Badges: `9999px` (fully rounded); Avatar Badges: `50%`
- **Typography**: Inter / Outfit (`sans-serif`), Monospace for codes, accounts, GSTIN, PAN, and currency values.
- **Placeholders Rule**: Always `Enter [Field Name]` (e.g. `Enter full name`, `Enter mobile number`). **Never** include mock example data (e.g. `e.g. Navneet Sharma` or dummy numbers).

---

## 2. Standard Index / List Page Structure

Every index view must follow this exact sequential layout:

### A. Top Breadcrumb & Action Bar
```blade
<div class="erp-page-top-bar">
    <div>
        <div class="erp-breadcrumb-trail">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
            <span>[Category Name]</span>
            <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
            <span class="erp-breadcrumb-active">[Module Name]</span>
        </div>
        <h1 class="erp-page-title">
            <i class="fa-solid [icon-class] text-primary"></i> [Module Name]
        </h1>
        <p class="erp-page-subtitle">
            [Brief, clear description of the module scope and records]
        </p>
    </div>

    <div class="erp-header-actions">
        <button type="button" class="btn btn-outline" onclick="window.print()" title="Print List">
            <i class="fa-solid fa-print"></i> Print List
        </button>
        <a href="{{ route('admin.[module].create') }}" class="btn btn-primary erp-btn-header-primary">
            <i class="fa-solid fa-plus"></i> Add New [Entity]
        </a>
    </div>
</div>
```

### B. 4-Card KPI Summary Statistics Grid
```blade
<div class="erp-kpi-grid">
    <div class="card erp-kpi-card erp-kpi-primary">
        <div class="erp-kpi-icon-box erp-kpi-icon-primary"><i class="fa-solid [icon]"></i></div>
        <div>
            <div class="erp-kpi-label">Total Records</div>
            <div class="erp-kpi-val">{{ $stats['total'] ?? 0 }}</div>
        </div>
    </div>
    <div class="card erp-kpi-card erp-kpi-success">
        <div class="erp-kpi-icon-box erp-kpi-icon-success"><i class="fa-solid fa-circle-check"></i></div>
        <div>
            <div class="erp-kpi-label">Active Records</div>
            <div class="erp-kpi-val erp-kpi-val-success">{{ $stats['active'] ?? 0 }}</div>
        </div>
    </div>
    <div class="card erp-kpi-card erp-kpi-purple">
        <div class="erp-kpi-icon-box erp-kpi-icon-purple"><i class="fa-solid [currency-or-metric-icon]"></i></div>
        <div>
            <div class="erp-kpi-label">Total Balance / Metric</div>
            <div class="erp-kpi-val">₹{{ number_format($stats['metric'] ?? 0, 2) }}</div>
        </div>
    </div>
    <div class="card erp-kpi-card" style="border-left: 4px solid #3B82F6;">
        <div class="erp-kpi-icon-box" style="background: rgba(59, 130, 246, 0.12); color: #2563EB;"><i class="fa-solid [secondary-icon]"></i></div>
        <div>
            <div class="erp-kpi-label">Secondary Counter</div>
            <div class="erp-kpi-val">{{ $stats['secondary'] ?? 0 }}</div>
        </div>
    </div>
</div>
```

### C. Search & Filter Bar
```blade
<div class="erp-table-filter-header">
    <form action="{{ route('admin.[module]') }}" method="GET" class="erp-filter-form">
        <div class="erp-search-wrap">
            <i class="fa-solid fa-magnifying-glass erp-search-icon"></i>
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search [entity] name, code, contact..." class="form-control erp-search-input">
        </div>

        <select name="status" class="form-control erp-filter-select" onchange="this.form.submit()">
            <option value="all">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>

        <button type="submit" class="btn btn-outline erp-btn-filter"><i class="fa-solid fa-filter"></i> Filter</button>
        @if(!empty($filters['search']) || $filters['status'] !== 'all')
            <a href="{{ route('admin.[module]') }}" class="btn btn-outline erp-btn-filter-clear" title="Clear Filters"><i class="fa-solid fa-xmark"></i> Clear</a>
        @endif
    </form>
    <div class="erp-table-summary-count">
        Showing <strong>{{ $records->count() }}</strong> of <strong>{{ $records->total() }}</strong> records
    </div>
</div>
```

### D. Table Row & Vibrant Status Button
```blade
<!-- Table Status Cell -->
<td style="text-align: center;">
    <form action="{{ route('admin.[module].toggle-status', $record->id) }}" method="POST" style="display: inline-block;">
        @csrf
        @method('PATCH')
        @if($record->status === 'active')
            <button type="submit" class="erp-status-btn erp-status-btn-active" title="Click to Deactivate">
                <span class="erp-status-dot-green"></span> Active
            </button>
        @else
            <button type="submit" class="erp-status-btn erp-status-btn-inactive" title="Click to Activate">
                <span class="erp-status-dot-red"></span> Inactive
            </button>
        @endif
    </form>
</td>

<!-- Table Actions Cell -->
<td style="text-align: right;">
    <div class="d-inline-flex align-items-center gap-1">
        <button type="button" class="btn btn-icon btn-sm" onclick="openQuickView({{ $record->id }})" title="Quick View">
            <i class="fa-regular fa-eye" style="color: #64748B;"></i>
        </button>
        <a href="{{ route('admin.[module].edit', $record->id) }}" class="btn btn-icon btn-sm" title="Edit">
            <i class="fa-solid fa-pen-to-square" style="color: var(--primary);"></i>
        </a>
        <button type="button" class="btn btn-icon btn-sm text-danger" onclick="confirmDelete({{ $record->id }}, '{{ addslashes($record->name) }}')" title="Delete / Archive">
            <i class="fa-solid fa-trash-can"></i>
        </button>
    </div>
</td>
```

---

## 3. Standard Create / Edit Page Structure (2-Column Form)

All data entry forms must use the **2-Column Responsive Layout**:

- **Left Main Column (`.erp-form-main-col`)**:
  - Divided into 3 to 5 logical sections using `.card.erp-form-section-card`.
  - Each section header features `.erp-form-section-icon-box`, title, and subtitle.
  - Form groups use iconified inputs (`.erp-field-icon-wrap` and `.erp-field-icon`).
  - Helper descriptions use `.erp-field-hint`.
- **Right Sidebar Column (`.erp-form-side-col`)**:
  1. **Live Card Preview (`.erp-preview-card`)**: Avatar circle, entity name, code badge, key metric, and meta list updated dynamically in real-time via `updateLivePreview()`.
  2. **Audit Trail Card** (in Edit mode): Displays Account ID, Registration date, and Last modified timestamp.
  3. **Sidebar Action Buttons (`.erp-sidebar-actions-card`)**:
     - Primary Submit button (`.erp-btn-action-submit`) with icon.
     - Secondary Cancel button (`.erp-btn-action-cancel`) with icon.
  4. **Danger Zone Card** (in Edit mode): Distinct red-bordered card for soft-deleting/archiving with SweetAlert2 confirmation.
  5. **Policy / Info Box**: Outlines immediate operational permissions and business rules.

> [!IMPORTANT]
> **No Status Field in Forms**: Forms (`create.blade.php` and `edit.blade.php`) must **NEVER** contain an editable Status dropdown or input field. All newly created records automatically default to `active` on the backend. Status is exclusively toggled via the Table List status toggle button (`PATCH toggle-status`) and the Danger Zone / Archive action.


---

## 4. Standard Show / Profile Page Structure

- **Breadcrumb Trail** & Header Actions (`Print Profile`, `Edit Profile`, `Back to List`).
- **Hero Card**:
  - Prominent 64px avatar initial circle.
  - Entity title with unique code badge and colored status pill (`.erp-status-btn-active`).
  - Secondary meta badges (assigned manufacturing plant, city/state, GSTIN).
  - Quick action contact buttons (`Call Phone`, `Send Email`).
- **Quick Stat KPI Summary**: 4 colored cards highlighting financials, balances, terms, and date joined.
- **Two-Column Details Layout**:
  - **Left**: Detailed statutory compliance cards, factory/shipping addresses, and procurement/operational notes.
  - **Right**: Bank settlement details, primary contact person card, and system ledger audit timestamps.

---

## 5. CSS Utility Class Index (in `custom.css`)

| Class | Purpose |
|---|---|
| `.erp-page-top-bar` | Flexbox wrapper for breadcrumbs, title & top action buttons |
| `.erp-breadcrumb-trail` | Hierarchy navigation with muted links and chevron separators |
| `.erp-kpi-grid` | Responsive grid for statistical KPI counter cards |
| `.erp-kpi-card` | Styled metric card with 4px colored accent border |
| `.erp-kpi-primary` | Olive green accent border & icon box |
| `.erp-kpi-success` | Emerald green accent border & icon box |
| `.erp-kpi-purple` | Vibrant purple accent border & icon box |
| `.erp-table-filter-header` | Flexbox container for table toolbar filters and summary counter |
| `.erp-filter-form` | Inline flex form keeping search, selects, and action buttons aligned |
| `.erp-search-wrap` | Relative container positioning the magnifying glass icon inside the input |
| `.erp-search-icon` | Absolutely centered search icon with focus highlight transitions |
| `.erp-search-input` | Padded search input with custom focus rings and placeholder styles |
| `.erp-filter-select` | Compact dropdown select with custom SVG chevron and auto width |
| `.erp-btn-filter` | Filter submit button with border, icon, and hover elevation |
| `.erp-btn-filter-clear` | Clear filter action button with soft red accent styling |
| `.erp-table-summary-count` | Right-aligned pill badge displaying `Showing X of Y ...` count |
| `.erp-status-btn` | Modern pill button for active/inactive status display & toggle |
| `.erp-status-btn-active` | Mint background (`#DCFCE7`), green text (`#15803D`), green border |
| `.erp-status-btn-inactive`| Soft red background (`#FEE2E2`), dark red text (`#B91C1C`), red border |
| `.erp-status-dot-green` | Pulsing 8px emerald glowing dot indicator |
| `.erp-status-dot-red` | 8px ruby red indicator dot |
| `.erp-form-layout-2col` | Main content (left) + Live preview & action sidebar (right) |
| `.erp-sidebar-actions-card` | Container for full-width action submit & cancel buttons |
| `.erp-btn-action-submit` | Full-width primary button with hover transition |
| `.erp-btn-action-cancel` | Full-width secondary cancel button |
| `.erp-field-icon-wrap` | Input wrapper positioning the left field icon |
| `.erp-field-hint` | Muted subtitle text directly beneath input fields |

