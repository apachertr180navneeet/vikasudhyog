@extends('admin.layouts.app')

@section('title', 'Access Level & Role Management - VIKAS UDHYOG ERP')
@section('page_code', 'master-access')

@section('content')
<section class="view-section active" id="view-master-access">
    <!-- Breadcrumb & Top Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span>Masters</span>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">Access Level &amp; Roles</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-shield-halved"></i> Access Level &amp; Role Management
            </h1>
            <p class="erp-page-subtitle">
                Configure granular sub-module authorization (View, Add, Edit, Delete, Export) and role activation status.
            </p>
        </div>

        <div class="erp-header-actions">
            <button type="button" class="btn btn-outline" onclick="openAddRoleModal()">
                <i class="fa-solid fa-plus"></i> Add Custom Role
            </button>
            <button type="button" class="btn btn-outline" onclick="resetCurrentRoleDefaults()" title="Restore standard default permissions for selected role">
                <i class="fa-solid fa-arrows-rotate"></i> Reset Defaults
            </button>
            <button type="button" class="btn btn-primary" onclick="saveCurrentPermissions()">
                <i class="fa-solid fa-floppy-disk"></i> Save Permissions
            </button>
        </div>
    </div>

    <!-- Main 2-Column Master Layout -->
    <div class="access-mgmt-grid">
        
        <!-- Left Column: Role Directory -->
        <div class="card role-directory-card">
            <div class="role-directory-header">
                <div class="role-directory-title">
                    <i class="fa-solid fa-shield-halved" style="color: var(--primary);"></i> Roles <span id="role-count-badge" class="role-count-pill">8</span>
                </div>
                <button type="button" onclick="openAddRoleModal()" class="btn btn-outline role-directory-btn-new" title="Create New Custom Role">
                    <i class="fa-solid fa-plus"></i> New Role
                </button>
            </div>

            <!-- Role Filter Search -->
            <div class="role-search-wrap">
                <div class="role-search-inner">
                    <i class="fa-solid fa-magnifying-glass role-search-icon"></i>
                    <input type="text" id="role-search-input" placeholder="Search roles..." class="form-control role-search-field" oninput="filterRolesList(this.value)">
                </div>
            </div>

            <!-- Role List Container -->
            <div id="roles-list-wrapper" class="roles-list-scroll">
                <!-- Roles injected via JS -->
            </div>
        </div>

        <!-- Right Column: Permission Matrix -->
        <div class="erp-form-main-col">
            
            <!-- Active Role Hero Banner Card -->
            <div class="card role-matrix-card">
                <div id="role-hero-header" class="role-hero-header">
                    <div class="hero-left-wrap">
                        <div id="hero-role-icon-box" class="hero-icon-box">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <div>
                            <div class="hero-title-row">
                                <h2 id="hero-role-name" class="hero-role-heading">
                                    Super Administrator
                                </h2>
                                <span id="hero-role-badge" class="hero-badge-pill">
                                    System Built-in
                                </span>
                                <span id="hero-role-status-badge" class="hero-status-pill">
                                    <span class="erp-status-dot-green"></span> Active
                                </span>
                            </div>
                            <p id="hero-role-desc" class="hero-role-description">
                                Unrestricted master privilege. Full control over system configurations, plants & users.
                            </p>
                        </div>
                    </div>

                    <!-- Role Management & Quick Controls -->
                    <div class="hero-actions-row">
                        <!-- Edit Role Button -->
                        <button type="button" class="btn hero-action-btn" onclick="openEditRoleModal()" title="Edit Role Details">
                            <i class="fa-solid fa-pen-to-square"></i> Edit
                        </button>

                        <!-- Toggle Role Status Button -->
                        <button type="button" id="btn-toggle-role-status" class="btn hero-action-btn" onclick="toggleRoleStatus()" title="Toggle Active / Inactive Status">
                            <i class="fa-solid fa-power-off"></i> <span id="lbl-toggle-status">Deactivate</span>
                        </button>

                        <!-- Delete Role Button -->
                        <button type="button" id="btn-delete-role" class="btn hero-delete-btn" onclick="confirmDeleteCurrentRole()" title="Delete this custom role">
                            <i class="fa-solid fa-trash-can"></i> Delete
                        </button>
                    </div>
                </div>

                <!-- Sub-Permission Quick Preset Bar -->
                <div class="perm-preset-bar">
                    <div class="perm-coverage-wrap">
                        <span class="perm-coverage-label">
                            <i class="fa-solid fa-chart-pie" style="color: var(--primary);"></i> Scope:
                        </span>
                        <span id="permission-ratio-text" class="perm-coverage-text">
                            140 / 140 Actions Granted (100%)
                        </span>
                        <div class="perm-progress-track">
                            <div id="permission-progress-bar" class="perm-progress-fill"></div>
                        </div>
                    </div>

                    <!-- Live Module Search Filter -->
                    <div class="perm-search-box-wrap">
                        <i class="fa-solid fa-magnifying-glass perm-search-box-icon"></i>
                        <input type="text" id="perm-module-search" class="form-control perm-search-box-input" placeholder="Search modules (e.g. Sales, Ledger)..." oninput="filterPermissionsModules(this.value)">
                    </div>

                    <div class="perm-preset-actions">
                        <button type="button" class="btn btn-outline perm-preset-btn" onclick="toggleAllSubPermissions(true)" title="Grant all actions across all 28 modules">
                            <i class="fa-solid fa-check-double" style="color: var(--status-success);"></i> Full Access
                        </button>
                        <button type="button" class="btn btn-outline perm-preset-btn" onclick="setReadOnlyPreset()" title="Grant only View action across all modules">
                            <i class="fa-regular fa-eye" style="color: var(--status-info);"></i> Read Only
                        </button>
                        <button type="button" class="btn btn-outline perm-preset-btn-revoke" onclick="toggleAllSubPermissions(false)" title="Revoke all actions">
                            <i class="fa-solid fa-ban"></i> Revoke All
                        </button>
                    </div>
                </div>

                <!-- Inactive Role Warning Banner -->
                <div id="inactive-role-notice" class="perm-notice-inactive">
                    <i class="fa-solid fa-circle-exclamation perm-notice-icon-inactive"></i>
                    <span><strong>This role is currently INACTIVE.</strong> Operators assigned to this role cannot log in or perform actions until it is reactivated.</span>
                </div>

                <!-- Super Admin Notice Banner -->
                <div id="super-admin-notice" class="perm-notice-superadmin">
                    <i class="fa-solid fa-crown perm-notice-icon-super"></i>
                    <span><strong>Super Administrator</strong> inherently possesses unrestricted access across all ERP master registers, transactional ledgers, and database management modules.</span>
                </div>

                <!-- Granular Permissions Grid Grouped by Category -->
                <div class="perm-groups-container">
                    
                    <!-- Group 1: Masters & Static Records -->
                    <div class="perm-theme-core">
                        <div class="perm-group-header">
                            <div class="perm-group-title">
                                <span class="perm-group-icon">
                                    <i class="fa-solid fa-folder-tree"></i>
                                </span>
                                1. Master Records &amp; Directory Permissions
                            </div>
                            <div class="perm-group-header-right">
                                <span class="perm-group-count">9 Modules</span>
                                <button type="button" class="btn perm-cat-btn" onclick="toggleCategoryGroup('masters', true)" title="Grant all 9 modules in Masters group">
                                    <i class="fa-solid fa-check"></i> Grant Group
                                </button>
                            </div>
                        </div>

                        <div class="perm-modules-list">
                            @php
                                $mastersModules = [
                                    'company' => ['Company Master', 'fa-building', 'Multi-company legal profiles, GSTIN & bank accounts'],
                                    'user' => ['User Master', 'fa-users', 'Operator login credentials & branch plant mapping'],
                                    'access_level' => ['Access Level', 'fa-shield-halved', 'Module authorizations & role security matrix'],
                                    'vendor' => ['Vendor Master', 'fa-truck-field', 'Raw herbal suppliers, credit terms & balances'],
                                    'customer' => ['Customer Master', 'fa-users-line', 'Buyer profiles, GSTIN & credit limits'],
                                    'broker' => ['Broker Master', 'fa-handshake', 'Trade agents, commission rates & accounts'],
                                    'item' => ['Item Master', 'fa-leaf', 'Henna & herbal items, batches & HSN codes'],
                                    'unit' => ['Unit Master', 'fa-scale-balanced', 'Measurement units (KG, Bag, Metric Ton)'],
                                    'account' => ['Account Master', 'fa-book-bookmark', 'General ledger accounts & finance heads'],
                                ];
                            @endphp

                            @foreach($mastersModules as $key => $data)
                                <div class="perm-module-card" id="card-mod-{{ $key }}">
                                    <div class="perm-module-header">
                                        <div class="perm-module-info">
                                            <div class="perm-icon-box">
                                                <i class="fa-solid {{ $data[1] }}"></i>
                                            </div>
                                            <div>
                                                <div class="perm-module-name">{{ $data[0] }}</div>
                                                <div class="perm-module-desc">{{ $data[2] }}</div>
                                            </div>
                                        </div>
                                        <div class="perm-module-actions-right">
                                            <span class="sub-perm-badge" id="badge-count-{{ $key }}">5/5 Actions</span>
                                            <label class="switch-control" onclick="event.stopPropagation()">
                                                <input type="checkbox" id="perm-{{ $key }}-master" onchange="toggleModuleAllActions('{{ $key }}', this.checked)">
                                                <span class="switch-slider"></span>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Sub-Actions Checkboxes Grid -->
                                    <div class="sub-actions-container" id="sub-actions-{{ $key }}">
                                        <label class="action-pill" id="pill-{{ $key }}-view">
                                            <input type="checkbox" id="act-{{ $key }}-view" onchange="updateSubAction('{{ $key }}', 'view', this.checked)">
                                            <i class="fa-regular fa-eye"></i> View
                                        </label>
                                        <label class="action-pill" id="pill-{{ $key }}-add">
                                            <input type="checkbox" id="act-{{ $key }}-add" onchange="updateSubAction('{{ $key }}', 'add', this.checked)">
                                            <i class="fa-solid fa-plus"></i> Add
                                        </label>
                                        <label class="action-pill" id="pill-{{ $key }}-edit">
                                            <input type="checkbox" id="act-{{ $key }}-edit" onchange="updateSubAction('{{ $key }}', 'edit', this.checked)">
                                            <i class="fa-solid fa-pen-to-square"></i> Edit
                                        </label>
                                        <label class="action-pill" id="pill-{{ $key }}-delete">
                                            <input type="checkbox" id="act-{{ $key }}-delete" onchange="updateSubAction('{{ $key }}', 'delete', this.checked)">
                                            <i class="fa-regular fa-trash-can"></i> Delete
                                        </label>
                                        <label class="action-pill" id="pill-{{ $key }}-export">
                                            <input type="checkbox" id="act-{{ $key }}-export" onchange="updateSubAction('{{ $key }}', 'export', this.checked)">
                                            <i class="fa-solid fa-print"></i> Export
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Group 2: Transactions & Commercial Entries -->
                    <div class="perm-theme-trans">
                        <div class="perm-group-header">
                            <div class="perm-group-title">
                                <span class="perm-group-icon">
                                    <i class="fa-solid fa-right-left"></i>
                                </span>
                                2. Commercial Transactions &amp; Vouchers
                            </div>
                            <div class="perm-group-header-right">
                                <span class="perm-group-count">8 Modules</span>
                                <button type="button" class="btn perm-cat-btn" onclick="toggleCategoryGroup('transactions', true)" title="Grant all 8 modules in Transactions group">
                                    <i class="fa-solid fa-check"></i> Grant Group
                                </button>
                            </div>
                        </div>

                        <div class="perm-modules-list">
                            @php
                                $transactionModules = [
                                    'purchase_entry' => ['Purchase Entry', 'fa-cart-shopping', 'Direct raw herbal inward procurement purchases'],
                                    'wb_purchase_entry' => ['WB Purchase Entry', 'fa-truck-ramp-box', 'Weighbridge gross/tare inward shipments'],
                                    'sales_entry' => ['Sales Entry', 'fa-file-invoice-dollar', 'Customer sales invoicing, tax bills & GST credit'],
                                    'wb_sales_entry' => ['WB Sales Entry', 'fa-truck-fast', 'Weighbridge outward bulk powder dispatch'],
                                    'order_dispatch' => ['Order Dispatch', 'fa-dolly', 'Warehouse loading slips and dispatch manifests'],
                                    'sales_purchase_order' => ['Sales / Purchase Order', 'fa-file-signature', 'Contract booking and order confirmations'],
                                    'receipt_voucher' => ['Receipt Voucher', 'fa-arrow-down-to-line', 'Customer cash & bank ledger receipts'],
                                    'payment_voucher' => ['Payment Voucher', 'fa-arrow-up-from-line', 'Supplier & expense bank/cash vouchers'],
                                ];
                            @endphp

                            @foreach($transactionModules as $key => $data)
                                <div class="perm-module-card" id="card-mod-{{ $key }}">
                                    <div class="perm-module-header">
                                        <div class="perm-module-info">
                                            <div class="perm-icon-box">
                                                <i class="fa-solid {{ $data[1] }}"></i>
                                            </div>
                                            <div>
                                                <div class="perm-module-name">{{ $data[0] }}</div>
                                                <div class="perm-module-desc">{{ $data[2] }}</div>
                                            </div>
                                        </div>
                                        <div class="perm-module-actions-right">
                                            <span class="sub-perm-badge" id="badge-count-{{ $key }}">5/5 Actions</span>
                                            <label class="switch-control" onclick="event.stopPropagation()">
                                                <input type="checkbox" id="perm-{{ $key }}-master" onchange="toggleModuleAllActions('{{ $key }}', this.checked)">
                                                <span class="switch-slider"></span>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Sub-Actions Checkboxes Grid -->
                                    <div class="sub-actions-container" id="sub-actions-{{ $key }}">
                                        <label class="action-pill" id="pill-{{ $key }}-view">
                                            <input type="checkbox" id="act-{{ $key }}-view" onchange="updateSubAction('{{ $key }}', 'view', this.checked)">
                                            <i class="fa-regular fa-eye"></i> View
                                        </label>
                                        <label class="action-pill" id="pill-{{ $key }}-add">
                                            <input type="checkbox" id="act-{{ $key }}-add" onchange="updateSubAction('{{ $key }}', 'add', this.checked)">
                                            <i class="fa-solid fa-plus"></i> Add
                                        </label>
                                        <label class="action-pill" id="pill-{{ $key }}-edit">
                                            <input type="checkbox" id="act-{{ $key }}-edit" onchange="updateSubAction('{{ $key }}', 'edit', this.checked)">
                                            <i class="fa-solid fa-pen-to-square"></i> Edit
                                        </label>
                                        <label class="action-pill" id="pill-{{ $key }}-delete">
                                            <input type="checkbox" id="act-{{ $key }}-delete" onchange="updateSubAction('{{ $key }}', 'delete', this.checked)">
                                            <i class="fa-regular fa-trash-can"></i> Delete
                                        </label>
                                        <label class="action-pill" id="pill-{{ $key }}-export">
                                            <input type="checkbox" id="act-{{ $key }}-export" onchange="updateSubAction('{{ $key }}', 'export', this.checked)">
                                            <i class="fa-solid fa-print"></i> Export
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Group 3: Inventory & Warehouse Control -->
                    <div class="perm-theme-stock">
                        <div class="perm-group-header">
                            <div class="perm-group-title">
                                <span class="perm-group-icon">
                                    <i class="fa-solid fa-boxes-stacked"></i>
                                </span>
                                3. Inventory &amp; Warehouse Control
                            </div>
                            <div class="perm-group-header-right">
                                <span class="perm-group-count">4 Modules</span>
                                <button type="button" class="btn perm-cat-btn" onclick="toggleCategoryGroup('inventory', true)" title="Grant all 4 modules in Inventory group">
                                    <i class="fa-solid fa-check"></i> Grant Group
                                </button>
                            </div>
                        </div>

                        <div class="perm-modules-list">
                            @php
                                $inventoryModules = [
                                    'stock_overview' => ['Stock Overview', 'fa-cubes', 'Real-time plant inventory balances & storage bins'],
                                    'item_ledger' => ['Item Ledger', 'fa-book-open', 'Inward & outward stock movement logs'],
                                    'stock_adjustment' => ['Stock Adjustment', 'fa-sliders', 'Physical stock reconciliation & corrections'],
                                    'low_stock_alert' => ['Low Stock Alert', 'fa-bell', 'Minimum threshold alerts & notifications'],
                                ];
                            @endphp

                            @foreach($inventoryModules as $key => $data)
                                <div class="perm-module-card" id="card-mod-{{ $key }}">
                                    <div class="perm-module-header">
                                        <div class="perm-module-info">
                                            <div class="perm-icon-box">
                                                <i class="fa-solid {{ $data[1] }}"></i>
                                            </div>
                                            <div>
                                                <div class="perm-module-name">{{ $data[0] }}</div>
                                                <div class="perm-module-desc">{{ $data[2] }}</div>
                                            </div>
                                        </div>
                                        <div class="perm-module-actions-right">
                                            <span class="sub-perm-badge" id="badge-count-{{ $key }}">5/5 Actions</span>
                                            <label class="switch-control" onclick="event.stopPropagation()">
                                                <input type="checkbox" id="perm-{{ $key }}-master" onchange="toggleModuleAllActions('{{ $key }}', this.checked)">
                                                <span class="switch-slider"></span>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Sub-Actions Checkboxes Grid -->
                                    <div class="sub-actions-container" id="sub-actions-{{ $key }}">
                                        <label class="action-pill" id="pill-{{ $key }}-view">
                                            <input type="checkbox" id="act-{{ $key }}-view" onchange="updateSubAction('{{ $key }}', 'view', this.checked)">
                                            <i class="fa-regular fa-eye"></i> View
                                        </label>
                                        <label class="action-pill" id="pill-{{ $key }}-add">
                                            <input type="checkbox" id="act-{{ $key }}-add" onchange="updateSubAction('{{ $key }}', 'add', this.checked)">
                                            <i class="fa-solid fa-plus"></i> Add
                                        </label>
                                        <label class="action-pill" id="pill-{{ $key }}-edit">
                                            <input type="checkbox" id="act-{{ $key }}-edit" onchange="updateSubAction('{{ $key }}', 'edit', this.checked)">
                                            <i class="fa-solid fa-pen-to-square"></i> Edit
                                        </label>
                                        <label class="action-pill" id="pill-{{ $key }}-delete">
                                            <input type="checkbox" id="act-{{ $key }}-delete" onchange="updateSubAction('{{ $key }}', 'delete', this.checked)">
                                            <i class="fa-regular fa-trash-can"></i> Delete
                                        </label>
                                        <label class="action-pill" id="pill-{{ $key }}-export">
                                            <input type="checkbox" id="act-{{ $key }}-export" onchange="updateSubAction('{{ $key }}', 'export', this.checked)">
                                            <i class="fa-solid fa-print"></i> Export
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Group 4: Reports & Auditing -->
                    <div class="perm-theme-reports">
                        <div class="perm-group-header">
                            <div class="perm-group-title">
                                <span class="perm-group-icon">
                                    <i class="fa-solid fa-file-contract"></i>
                                </span>
                                4. Reports &amp; Financial Statements
                            </div>
                            <div class="perm-group-header-right">
                                <span class="perm-group-count">4 Modules</span>
                                <button type="button" class="btn perm-cat-btn" onclick="toggleCategoryGroup('reports', true)" title="Grant all 4 modules in Reports group">
                                    <i class="fa-solid fa-check"></i> Grant Group
                                </button>
                            </div>
                        </div>

                        <div class="perm-modules-list">
                            @php
                                $reportModules = [
                                    'purchase_report' => ['Purchase Report', 'fa-receipt', 'Detailed procurement & supplier GST registers'],
                                    'sales_report' => ['Sales Report', 'fa-chart-pie', 'Sales revenue, GST returns & commercial analytics'],
                                    'order_report' => ['Order Report', 'fa-list-check', 'Pending orders & delivery status registers'],
                                    'cash_bank_register' => ['Cash & Bank Register', 'fa-building-columns', 'Bank passbooks, cash flows & accounts'],
                                ];
                            @endphp

                            @foreach($reportModules as $key => $data)
                                <div class="perm-module-card" id="card-mod-{{ $key }}">
                                    <div class="perm-module-header">
                                        <div class="perm-module-info">
                                            <div class="perm-icon-box">
                                                <i class="fa-solid {{ $data[1] }}"></i>
                                            </div>
                                            <div>
                                                <div class="perm-module-name">{{ $data[0] }}</div>
                                                <div class="perm-module-desc">{{ $data[2] }}</div>
                                            </div>
                                        </div>
                                        <div class="perm-module-actions-right">
                                            <span class="sub-perm-badge" id="badge-count-{{ $key }}">5/5 Actions</span>
                                            <label class="switch-control" onclick="event.stopPropagation()">
                                                <input type="checkbox" id="perm-{{ $key }}-master" onchange="toggleModuleAllActions('{{ $key }}', this.checked)">
                                                <span class="switch-slider"></span>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Sub-Actions Checkboxes Grid -->
                                    <div class="sub-actions-container" id="sub-actions-{{ $key }}">
                                        <label class="action-pill" id="pill-{{ $key }}-view">
                                            <input type="checkbox" id="act-{{ $key }}-view" onchange="updateSubAction('{{ $key }}', 'view', this.checked)">
                                            <i class="fa-regular fa-eye"></i> View
                                        </label>
                                        <label class="action-pill" id="pill-{{ $key }}-add">
                                            <input type="checkbox" id="act-{{ $key }}-add" onchange="updateSubAction('{{ $key }}', 'add', this.checked)">
                                            <i class="fa-solid fa-plus"></i> Add
                                        </label>
                                        <label class="action-pill" id="pill-{{ $key }}-edit">
                                            <input type="checkbox" id="act-{{ $key }}-edit" onchange="updateSubAction('{{ $key }}', 'edit', this.checked)">
                                            <i class="fa-solid fa-pen-to-square"></i> Edit
                                        </label>
                                        <label class="action-pill" id="pill-{{ $key }}-delete">
                                            <input type="checkbox" id="act-{{ $key }}-delete" onchange="updateSubAction('{{ $key }}', 'delete', this.checked)">
                                            <i class="fa-regular fa-trash-can"></i> Delete
                                        </label>
                                        <label class="action-pill" id="pill-{{ $key }}-export">
                                            <input type="checkbox" id="act-{{ $key }}-export" onchange="updateSubAction('{{ $key }}', 'export', this.checked)">
                                            <i class="fa-solid fa-print"></i> Export
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Group 5: System Administration & Setup -->
                    <div class="perm-theme-admin">
                        <div class="perm-group-header">
                            <div class="perm-group-title">
                                <span class="perm-group-icon">
                                    <i class="fa-solid fa-gears"></i>
                                </span>
                                5. System Administration &amp; Integration
                            </div>
                            <div class="perm-group-header-right">
                                <span class="perm-group-count">3 Modules</span>
                                <button type="button" class="btn perm-cat-btn" onclick="toggleCategoryGroup('settings', true)" title="Grant all 3 modules in Settings group">
                                    <i class="fa-solid fa-check"></i> Grant Group
                                </button>
                            </div>
                        </div>

                        <div class="perm-modules-list">
                            @php
                                $settingModules = [
                                    'company_settings' => ['Company Information', 'fa-gear', 'System master config, plant variables & invoices'],
                                    'whatsapp_settings' => ['WhatsApp API', 'fa-brands fa-whatsapp', 'Automated bill & dispatch notifications'],
                                    'backup_restore' => ['Backup & Restore', 'fa-database', 'Database dump snapshots & manual archival'],
                                ];
                            @endphp

                            @foreach($settingModules as $key => $data)
                                <div class="perm-module-card" id="card-mod-{{ $key }}">
                                    <div class="perm-module-header">
                                        <div class="perm-module-info">
                                            <div class="perm-icon-box">
                                                <i class="{{ $data[1] }}"></i>
                                            </div>
                                            <div>
                                                <div class="perm-module-name">{{ $data[0] }}</div>
                                                <div class="perm-module-desc">{{ $data[2] }}</div>
                                            </div>
                                        </div>
                                        <div class="perm-module-actions-right">
                                            <span class="sub-perm-badge" id="badge-count-{{ $key }}">5/5 Actions</span>
                                            <label class="switch-control" onclick="event.stopPropagation()">
                                                <input type="checkbox" id="perm-{{ $key }}-master" onchange="toggleModuleAllActions('{{ $key }}', this.checked)">
                                                <span class="switch-slider"></span>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Sub-Actions Checkboxes Grid -->
                                    <div class="sub-actions-container" id="sub-actions-{{ $key }}">
                                        <label class="action-pill" id="pill-{{ $key }}-view">
                                            <input type="checkbox" id="act-{{ $key }}-view" onchange="updateSubAction('{{ $key }}', 'view', this.checked)">
                                            <i class="fa-regular fa-eye"></i> View
                                        </label>
                                        <label class="action-pill" id="pill-{{ $key }}-add">
                                            <input type="checkbox" id="act-{{ $key }}-add" onchange="updateSubAction('{{ $key }}', 'add', this.checked)">
                                            <i class="fa-solid fa-plus"></i> Add
                                        </label>
                                        <label class="action-pill" id="pill-{{ $key }}-edit">
                                            <input type="checkbox" id="act-{{ $key }}-edit" onchange="updateSubAction('{{ $key }}', 'edit', this.checked)">
                                            <i class="fa-solid fa-pen-to-square"></i> Edit
                                        </label>
                                        <label class="action-pill" id="pill-{{ $key }}-delete">
                                            <input type="checkbox" id="act-{{ $key }}-delete" onchange="updateSubAction('{{ $key }}', 'delete', this.checked)">
                                            <i class="fa-regular fa-trash-can"></i> Delete
                                        </label>
                                        <label class="action-pill" id="pill-{{ $key }}-export">
                                            <input type="checkbox" id="act-{{ $key }}-export" onchange="updateSubAction('{{ $key }}', 'export', this.checked)">
                                            <i class="fa-solid fa-print"></i> Export
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>

<!-- Modal: Add Custom Role -->
<div class="custom-modal-overlay" id="modal-add-role">
    <div class="custom-modal-card">
        <div class="custom-modal-header">
            <div class="custom-modal-title">
                <i class="fa-solid fa-shield-plus custom-modal-title-icon"></i> Add Custom Role
            </div>
            <button type="button" class="custom-modal-close-btn" onclick="closeAddRoleModal()">&times;</button>
        </div>
        <form id="form-add-role" onsubmit="handleCreateRoleSubmit(event)">
            <div class="custom-modal-body">
                <div class="custom-modal-form-group">
                    <label class="custom-modal-label">
                        Role Title <span class="erp-req-star">*</span>
                    </label>
                    <input type="text" id="new-role-name" class="form-control custom-modal-input" placeholder="e.g. Quality Inspector, Dispatch Supervisor" required maxlength="60">
                </div>

                <div class="custom-modal-form-group">
                    <label class="custom-modal-label">
                        Role Description
                    </label>
                    <input type="text" id="new-role-desc" class="form-control custom-modal-input" placeholder="Short summary of role scope" maxlength="255">
                </div>

                <div class="custom-modal-form-group-lg">
                    <label class="custom-modal-label">
                        Base Permission Template
                    </label>
                    <select id="new-role-template" class="form-control custom-modal-input">
                        <option value="">Blank (No module permissions)</option>
                        @foreach($roles as $r)
                            <option value="{{ $r['id'] }}">{{ $r['name'] }} ({{ $r['badge'] }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="custom-modal-footer">
                    <button type="button" class="btn btn-outline custom-modal-btn-cancel" onclick="closeAddRoleModal()">Cancel</button>
                    <button type="submit" id="btn-submit-add-role" class="btn btn-primary custom-modal-btn-submit">
                        <i class="fa-solid fa-plus"></i> Create Role
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Role Details -->
<div class="custom-modal-overlay" id="modal-edit-role">
    <div class="custom-modal-card">
        <div class="custom-modal-header">
            <div class="custom-modal-title">
                <i class="fa-solid fa-pen-to-square custom-modal-title-icon"></i> Edit Role Details
            </div>
            <button type="button" class="custom-modal-close-btn" onclick="closeEditRoleModal()">&times;</button>
        </div>
        <form id="form-edit-role" onsubmit="handleEditRoleSubmit(event)">
            <div class="custom-modal-body">
                <input type="hidden" id="edit-role-id">

                <div class="custom-modal-form-group">
                    <label class="custom-modal-label">
                        Role Title <span class="erp-req-star">*</span>
                    </label>
                    <input type="text" id="edit-role-name" class="form-control custom-modal-input" required maxlength="60">
                    <small id="edit-role-system-hint" style="display:none; color: var(--text-muted); font-size: 11px; margin-top: 3px;">Built-in system role title cannot be renamed.</small>
                </div>

                <div class="custom-modal-form-group">
                    <label class="custom-modal-label">
                        Role Description
                    </label>
                    <textarea id="edit-role-desc" class="form-control custom-modal-textarea" rows="3" maxlength="255"></textarea>
                </div>

                <div class="custom-modal-form-group-lg">
                    <label class="custom-modal-label">
                        Role Status
                    </label>
                    <select id="edit-role-status" class="form-control custom-modal-input">
                        <option value="active">Active (Available for user assignment)</option>
                        <option value="inactive">Inactive (Suspended / Deactivated)</option>
                    </select>
                </div>

                <div class="custom-modal-footer">
                    <button type="button" class="btn btn-outline custom-modal-btn-cancel" onclick="closeEditRoleModal()">Cancel</button>
                    <button type="submit" id="btn-submit-edit-role" class="btn btn-primary custom-modal-btn-submit">
                        <i class="fa-solid fa-floppy-disk"></i> Save Role
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const ACTIONS_LIST = ['view', 'add', 'edit', 'delete', 'export'];

const ALL_MODULE_KEYS = @json($allModuleKeys ?? []);

// Database dynamic roles list & permissions loaded from controller
let DB_ROLES = @json($roles ?? []);

let selectedRoleId = (DB_ROLES.length > 0) ? DB_ROLES[0].id : null;

const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

const CATEGORY_MODULE_MAP = {
    'masters': ['company', 'user', 'access_level', 'vendor', 'customer', 'broker', 'item', 'unit', 'account'],
    'transactions': ['purchase_entry', 'wb_purchase_entry', 'sales_entry', 'wb_sales_entry', 'order_dispatch', 'sales_purchase_order', 'receipt_voucher', 'payment_voucher'],
    'inventory': ['stock_overview', 'item_ledger', 'stock_adjustment', 'low_stock_alert'],
    'reports': ['purchase_report', 'sales_report', 'order_report', 'cash_bank_register'],
    'settings': ['company_settings', 'whatsapp_settings', 'backup_restore']
};

function getActiveRole() {
    return DB_ROLES.find(r => r.id === selectedRoleId) || DB_ROLES[0];
}

function initAccessLevelManager() {
    renderRolesList();
    if (selectedRoleId) {
        selectRole(selectedRoleId);
    }
}

function renderRolesList() {
    const container = document.getElementById('roles-list-wrapper');
    if (!container) return;

    let html = '';
    DB_ROLES.forEach(r => {
        const userCount = r.users_count || 0;
        const isActive = r.id === selectedRoleId;
        const isInactive = r.status === 'inactive';
        html += `
            <div class="role-nav-item ${isActive ? 'active' : ''} ${isInactive ? 'inactive-role' : ''}" onclick="selectRole(${r.id})">
                <div class="role-nav-left">
                    <div class="role-avatar-box" style="background: ${r.bg || '#EEF2FF'}; color: ${r.color || '#4F46E5'};">
                        <i class="fa-solid ${r.icon || 'fa-shield-halved'}"></i>
                    </div>
                    <div>
                        <div class="role-item-name">
                            ${escapeHtml(r.name)}
                            ${isInactive ? '<span class="role-inactive-pill">Inactive</span>' : ''}
                        </div>
                        <div class="role-item-users">
                            ${userCount} ${userCount === 1 ? 'Operator' : 'Operators'} Assigned
                        </div>
                    </div>
                </div>
                ${isActive ? '<i class="fa-solid fa-chevron-right role-nav-chevron"></i>' : ''}
            </div>
        `;
    });

    container.innerHTML = html;
    const countBadge = document.getElementById('role-count-badge');
    if (countBadge) countBadge.innerText = DB_ROLES.length;

    // Also update template options in add modal if open
    const templateSelect = document.getElementById('new-role-template');
    if (templateSelect) {
        let tplHtml = '<option value="">Blank (No module permissions)</option>';
        DB_ROLES.forEach(r => {
            tplHtml += `<option value="${r.id}">${escapeHtml(r.name)} (${escapeHtml(r.badge || 'Role')})</option>`;
        });
        templateSelect.innerHTML = tplHtml;
    }
}

function selectRole(roleId) {
    selectedRoleId = roleId;
    const role = getActiveRole();
    if (!role) return;

    renderRolesList();

    // Update Hero Banner
    document.getElementById('hero-role-name').innerText = role.name;
    document.getElementById('hero-role-desc').innerText = role.description || 'No description provided.';
    document.getElementById('hero-role-badge').innerText = role.badge || (role.is_system ? 'System Built-in' : 'Custom Role');
    document.getElementById('hero-role-icon-box').innerHTML = `<i class="fa-solid ${role.icon || 'fa-shield-halved'}"></i>`;

    const isSuperAdmin = (role.name === 'Super Administrator');
    const isInactive = (role.status === 'inactive');

    // Status Badge
    const statusBadge = document.getElementById('hero-role-status-badge');
    if (isInactive) {
        statusBadge.style.background = '#FEE2E2';
        statusBadge.style.color = '#991B1B';
        statusBadge.innerHTML = '<span class="erp-status-dot-red"></span> Inactive';
        document.getElementById('lbl-toggle-status').innerText = 'Activate';
        document.getElementById('inactive-role-notice').style.display = 'flex';
    } else {
        statusBadge.style.background = '#DCFCE7';
        statusBadge.style.color = '#166534';
        statusBadge.innerHTML = '<span class="erp-status-dot-green"></span> Active';
        document.getElementById('lbl-toggle-status').innerText = 'Deactivate';
        document.getElementById('inactive-role-notice').style.display = 'none';
    }

    // Toggle status button disabled for Super Admin
    document.getElementById('btn-toggle-role-status').disabled = isSuperAdmin;
    document.getElementById('btn-toggle-role-status').style.opacity = isSuperAdmin ? '0.5' : '1';

    // Delete button disabled for System Roles
    const deleteBtn = document.getElementById('btn-delete-role');
    if (role.is_system) {
        deleteBtn.style.display = 'none';
    } else {
        deleteBtn.style.display = 'inline-flex';
    }

    // Super Admin notice
    document.getElementById('super-admin-notice').style.display = isSuperAdmin ? 'flex' : 'none';

    if (!role.permissions) {
        role.permissions = {};
    }

    // Populate all module cards & sub-actions
    ALL_MODULE_KEYS.forEach(mod => {
        let modData = role.permissions[mod] || { can_view: false, can_add: false, can_edit: false, can_delete: false, can_export: false };
        if (isSuperAdmin) {
            modData = { can_view: true, can_add: true, can_edit: true, can_delete: true, can_export: true };
        }

        const masterSwitch = document.getElementById(`perm-${mod}-master`);
        const anyChecked = Object.values(modData).some(v => v === true);
        if (masterSwitch) {
            masterSwitch.checked = anyChecked;
            masterSwitch.disabled = isSuperAdmin;
        }

        ACTIONS_LIST.forEach(act => {
            const prop = 'can_' + act;
            const chk = document.getElementById(`act-${mod}-${act}`);
            const pill = document.getElementById(`pill-${mod}-${act}`);
            const isChecked = Boolean(modData[prop]);

            if (chk) {
                chk.checked = isChecked;
                chk.disabled = isSuperAdmin;
            }
            if (pill) {
                if (isChecked) {
                    pill.classList.add('active');
                } else {
                    pill.classList.remove('active');
                }
            }
        });

        updateModuleActionCountBadge(mod);
    });

    updateCoverageMeter();
}

function updateSubAction(modKey, actionKey, isChecked) {
    const role = getActiveRole();
    if (!role || role.name === 'Super Administrator') return;

    if (!role.permissions) role.permissions = {};
    if (!role.permissions[modKey]) {
        role.permissions[modKey] = { can_view: false, can_add: false, can_edit: false, can_delete: false, can_export: false };
    }

    const prop = 'can_' + actionKey;
    role.permissions[modKey][prop] = isChecked;

    // If add/edit/delete/export is checked, automatically ensure view is checked
    if (isChecked && actionKey !== 'view') {
        role.permissions[modKey]['can_view'] = true;
        const viewChk = document.getElementById(`act-${modKey}-view`);
        if (viewChk) viewChk.checked = true;
        const viewPill = document.getElementById(`pill-${modKey}-view`);
        if (viewPill) viewPill.classList.add('active');
    }

    // Update pill active class
    const pill = document.getElementById(`pill-${modKey}-${actionKey}`);
    if (pill) {
        if (isChecked) pill.classList.add('active');
        else pill.classList.remove('active');
    }

    // Update master switch for module
    const anyActive = Object.values(role.permissions[modKey]).some(v => v === true);
    const masterSwitch = document.getElementById(`perm-${modKey}-master`);
    if (masterSwitch) masterSwitch.checked = anyActive;

    updateModuleActionCountBadge(modKey);
    updateCoverageMeter();
}

function toggleModuleAllActions(modKey, enableAll) {
    const role = getActiveRole();
    if (!role || role.name === 'Super Administrator') return;

    if (!role.permissions) role.permissions = {};
    role.permissions[modKey] = {
        can_view: enableAll,
        can_add: enableAll,
        can_edit: enableAll,
        can_delete: enableAll,
        can_export: enableAll
    };

    ACTIONS_LIST.forEach(act => {
        const chk = document.getElementById(`act-${modKey}-${act}`);
        const pill = document.getElementById(`pill-${modKey}-${act}`);
        if (chk) chk.checked = enableAll;
        if (pill) {
            if (enableAll) pill.classList.add('active');
            else pill.classList.remove('active');
        }
    });

    updateModuleActionCountBadge(modKey);
    updateCoverageMeter();
}

function updateModuleActionCountBadge(modKey) {
    const badge = document.getElementById(`badge-count-${modKey}`);
    if (!badge) return;

    const role = getActiveRole();
    const modData = role?.permissions?.[modKey] || {};
    const activeCount = ACTIONS_LIST.filter(a => modData['can_' + a] === true).length;
    badge.innerText = `${activeCount}/5 Actions`;

    if (activeCount === 5) {
        badge.style.background = 'rgba(22, 163, 74, 0.12)';
        badge.style.color = '#15803D';
    } else if (activeCount > 0) {
        badge.style.background = 'rgba(59, 130, 246, 0.12)';
        badge.style.color = '#2563EB';
    } else {
        badge.style.background = '#F1F5F9';
        badge.style.color = '#94A3B8';
    }
}

function toggleCategoryGroup(categoryKey, enableAll) {
    const role = getActiveRole();
    if (!role || role.name === 'Super Administrator') return;

    const moduleList = CATEGORY_MODULE_MAP[categoryKey] || [];
    moduleList.forEach(mod => {
        toggleModuleAllActions(mod, enableAll);
    });
    toastr.info(`Updated privileges for ${categoryKey.toUpperCase()} category.`);
}

function toggleAllSubPermissions(enableAll) {
    const role = getActiveRole();
    if (!role || role.name === 'Super Administrator') return;

    if (!role.permissions) role.permissions = {};

    ALL_MODULE_KEYS.forEach(mod => {
        role.permissions[mod] = {
            can_view: enableAll,
            can_add: enableAll,
            can_edit: enableAll,
            can_delete: enableAll,
            can_export: enableAll
        };

        const masterSwitch = document.getElementById(`perm-${mod}-master`);
        if (masterSwitch) masterSwitch.checked = enableAll;

        ACTIONS_LIST.forEach(act => {
            const chk = document.getElementById(`act-${mod}-${act}`);
            const pill = document.getElementById(`pill-${mod}-${act}`);
            if (chk) chk.checked = enableAll;
            if (pill) {
                if (enableAll) pill.classList.add('active');
                else pill.classList.remove('active');
            }
        });

        updateModuleActionCountBadge(mod);
    });

    updateCoverageMeter();
    toastr.info(enableAll ? 'Granted full authorization across all sub-actions.' : 'Revoked all module privileges.');
}

function setReadOnlyPreset() {
    const role = getActiveRole();
    if (!role || role.name === 'Super Administrator') return;

    if (!role.permissions) role.permissions = {};

    ALL_MODULE_KEYS.forEach(mod => {
        role.permissions[mod] = {
            can_view: true,
            can_add: false,
            can_edit: false,
            can_delete: false,
            can_export: true
        };

        const masterSwitch = document.getElementById(`perm-${mod}-master`);
        if (masterSwitch) masterSwitch.checked = true;

        ACTIONS_LIST.forEach(act => {
            const chk = document.getElementById(`act-${mod}-${act}`);
            const pill = document.getElementById(`pill-${mod}-${act}`);
            const isChecked = (act === 'view' || act === 'export');
            if (chk) chk.checked = isChecked;
            if (pill) {
                if (isChecked) pill.classList.add('active');
                else pill.classList.remove('active');
            }
        });

        updateModuleActionCountBadge(mod);
    });

    updateCoverageMeter();
    toastr.info('Applied Read-Only (View & Export) preset.');
}

function updateCoverageMeter() {
    const totalPossible = ALL_MODULE_KEYS.length * 5;
    let totalGranted = 0;

    const role = getActiveRole();
    if (!role) return;

    if (role.name === 'Super Administrator') {
        totalGranted = totalPossible;
    } else {
        const modStore = role.permissions || {};
        ALL_MODULE_KEYS.forEach(mod => {
            const m = modStore[mod] || {};
            ACTIONS_LIST.forEach(act => {
                if (m['can_' + act] === true) totalGranted++;
            });
        });
    }

    const pct = Math.round((totalGranted / totalPossible) * 100);
    document.getElementById('permission-ratio-text').innerText = `${totalGranted} / ${totalPossible} Actions Granted (${pct}%)`;
    document.getElementById('permission-progress-bar').style.width = pct + '%';
}

function filterPermissionsModules(query) {
    const q = query.toLowerCase().trim();
    ALL_MODULE_KEYS.forEach(mod => {
        const card = document.getElementById(`card-mod-${mod}`);
        if (!card) return;
        if (!q) {
            card.style.display = 'flex';
        } else {
            const text = card.innerText.toLowerCase();
            card.style.display = text.includes(q) ? 'flex' : 'none';
        }
    });

    document.querySelectorAll('.perm-groups-container > div').forEach(group => {
        const visibleCards = group.querySelectorAll('.perm-module-card:not([style*="display: none"])');
        group.style.display = (q && visibleCards.length === 0) ? 'none' : 'block';
    });
}

function filterRolesList(query) {
    const q = query.toLowerCase().trim();
    document.querySelectorAll('#roles-list-wrapper .role-nav-item').forEach(el => {
        const text = el.innerText.toLowerCase();
        el.style.display = text.includes(q) ? 'flex' : 'none';
    });
}

// ----------------- Dynamic AJAX Backend Operations -----------------

async function saveCurrentPermissions() {
    const role = getActiveRole();
    if (!role) return;

    try {
        const res = await fetch(`{{ url('/admin/masters/access-level/roles') }}/${role.id}/permissions`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                permissions: role.permissions || {}
            })
        });

        const data = await res.json();
        if (data.success) {
            if (data.permissions) {
                role.permissions = data.permissions;
            }
            toastr.success(data.message || `Permissions for "${role.name}" saved successfully to database!`);
        } else {
            toastr.error(data.message || 'Failed to save permissions.');
        }
    } catch (err) {
        toastr.error('Error saving permissions to server. Please try again.');
        console.error(err);
    }
}

async function toggleRoleStatus() {
    const role = getActiveRole();
    if (!role) return;

    if (role.name === 'Super Administrator') {
        toastr.error('Super Administrator role cannot be deactivated.');
        return;
    }

    try {
        const res = await fetch(`{{ url('/admin/masters/access-level/roles') }}/${role.id}/toggle-status`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            }
        });

        const data = await res.json();
        if (data.success) {
            role.status = data.status;
            selectRole(role.id);
            toastr.success(data.message || `Role "${role.name}" status updated.`);
        } else {
            toastr.error(data.message || 'Failed to change role status.');
        }
    } catch (err) {
        toastr.error('Network error while toggling role status.');
        console.error(err);
    }
}

async function resetCurrentRoleDefaults() {
    const role = getActiveRole();
    if (!role) return;

    Swal.fire({
        title: `Reset Permissions for "${role.name}"?`,
        text: 'This will restore the standard factory blueprint permissions for this role.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#2563EB',
        cancelButtonColor: '#64748B',
        confirmButtonText: '<i class="fa-solid fa-arrows-rotate"></i> Yes, Reset Defaults',
        cancelButtonText: 'Cancel'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const res = await fetch(`{{ url('/admin/masters/access-level/roles') }}/${role.id}/reset-defaults`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json'
                    }
                });

                const data = await res.json();
                if (data.success) {
                    if (data.permissions) {
                        role.permissions = data.permissions;
                    }
                    selectRole(role.id);
                    toastr.success(data.message || `Permissions reset to defaults for "${role.name}".`);
                } else {
                    toastr.error(data.message || 'Failed to reset defaults.');
                }
            } catch (err) {
                toastr.error('Network error while resetting role permissions.');
                console.error(err);
            }
        }
    });
}

function openAddRoleModal() {
    document.getElementById('form-add-role').reset();
    document.getElementById('modal-add-role').style.display = 'flex';
}

function closeAddRoleModal() {
    document.getElementById('modal-add-role').style.display = 'none';
}

async function handleCreateRoleSubmit(e) {
    e.preventDefault();
    const name = document.getElementById('new-role-name').value.trim();
    const desc = document.getElementById('new-role-desc').value.trim();
    const templateId = document.getElementById('new-role-template').value;

    if (!name) return;

    const submitBtn = document.getElementById('btn-submit-add-role');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Creating...';

    try {
        const res = await fetch(`{{ url('/admin/masters/access-level/roles') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                name: name,
                description: desc,
                template_role_id: templateId || null
            })
        });

        const data = await res.json();
        if (data.success && data.role) {
            DB_ROLES.push(data.role);
            closeAddRoleModal();
            renderRolesList();
            selectRole(data.role.id);
            toastr.success(data.message || `Custom role "${data.role.name}" created successfully!`);
        } else {
            toastr.error(data.message || (data.errors ? Object.values(data.errors).flat().join('<br>') : 'Error creating role.'));
        }
    } catch (err) {
        toastr.error('Network error creating role.');
        console.error(err);
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
}

function openEditRoleModal() {
    const role = getActiveRole();
    if (!role) return;

    document.getElementById('edit-role-id').value = role.id;
    document.getElementById('edit-role-name').value = role.name;
    document.getElementById('edit-role-desc').value = role.description || '';
    document.getElementById('edit-role-status').value = role.status || 'active';

    const isSystem = Boolean(role.is_system);
    const nameInput = document.getElementById('edit-role-name');
    const hint = document.getElementById('edit-role-system-hint');
    if (isSystem) {
        nameInput.readOnly = true;
        nameInput.style.backgroundColor = '#F8FAFC';
        hint.style.display = 'block';
    } else {
        nameInput.readOnly = false;
        nameInput.style.backgroundColor = '#FFFFFF';
        hint.style.display = 'none';
    }

    if (role.name === 'Super Administrator') {
        document.getElementById('edit-role-status').disabled = true;
    } else {
        document.getElementById('edit-role-status').disabled = false;
    }

    document.getElementById('modal-edit-role').style.display = 'flex';
}

function closeEditRoleModal() {
    document.getElementById('modal-edit-role').style.display = 'none';
}

async function handleEditRoleSubmit(e) {
    e.preventDefault();
    const roleId = document.getElementById('edit-role-id').value;
    const newName = document.getElementById('edit-role-name').value.trim();
    const newDesc = document.getElementById('edit-role-desc').value.trim();
    const newStatus = document.getElementById('edit-role-status').value;

    const role = DB_ROLES.find(r => r.id == roleId);
    if (!role) return;

    const submitBtn = document.getElementById('btn-submit-edit-role');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';

    try {
        const res = await fetch(`{{ url('/admin/masters/access-level/roles') }}/${role.id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                name: newName,
                description: newDesc,
                status: newStatus
            })
        });

        const data = await res.json();
        if (data.success && data.role) {
            role.name = data.role.name;
            role.description = data.role.description;
            role.status = data.role.status;
            role.badge = data.role.badge;

            closeEditRoleModal();
            renderRolesList();
            selectRole(role.id);
            toastr.success(data.message || `Role "${role.name}" updated successfully!`);
        } else {
            toastr.error(data.message || (data.errors ? Object.values(data.errors).flat().join('<br>') : 'Error updating role.'));
        }
    } catch (err) {
        toastr.error('Network error updating role.');
        console.error(err);
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
}

function confirmDeleteCurrentRole() {
    const role = getActiveRole();
    if (!role || role.is_system) {
        toastr.error('System built-in roles cannot be deleted.');
        return;
    }

    const assignedCount = role.users_count || 0;

    Swal.fire({
        title: `Delete Role "${role.name}"?`,
        html: assignedCount > 0 
            ? `<div class="erp-swal-warn-text">Warning: ${assignedCount} active user(s) currently assigned to this role!</div><div>Please reassign users to another role before deleting.</div>` 
            : `Are you sure you want to permanently delete this custom role from the database?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC2626',
        cancelButtonColor: '#64748B',
        confirmButtonText: '<i class="fa-solid fa-trash-can"></i> Yes, Delete Role',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const res = await fetch(`{{ url('/admin/masters/access-level/roles') }}/${role.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json'
                    }
                });

                const data = await res.json();
                if (data.success) {
                    DB_ROLES = DB_ROLES.filter(r => r.id !== role.id);
                    selectedRoleId = DB_ROLES[0]?.id || null;
                    renderRolesList();
                    if (selectedRoleId) {
                        selectRole(selectedRoleId);
                    }
                    toastr.success(data.message || `Role "${role.name}" deleted successfully.`);
                } else {
                    toastr.error(data.message || 'Could not delete role.');
                }
            } catch (err) {
                toastr.error('Network error while deleting role.');
                console.error(err);
            }
        }
    });
}

function escapeHtml(text) {
    if (!text) return '';
    return String(text)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

document.addEventListener('DOMContentLoaded', () => {
    initAccessLevelManager();
});
</script>
@endpush
@endsection
