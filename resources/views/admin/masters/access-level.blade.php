@extends('admin.layouts.app')

@section('title', 'Access Level & Role Management - VIKAS UDHYOG ERP')
@section('page_code', 'master-access')

@section('content')
<section class="view-section active" id="view-master-access">
    <!-- Breadcrumb & Top Bar -->
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.25rem; display: flex; align-items: center; gap: 0.5rem;">
                <a href="{{ route('admin.dashboard') }}" style="color: var(--text-muted); text-decoration: none;">Dashboard</a>
                <i class="fa-solid fa-chevron-right" style="font-size: 0.65rem;"></i>
                <span>Masters</span>
                <i class="fa-solid fa-chevron-right" style="font-size: 0.65rem;"></i>
                <span style="color: var(--primary); font-weight: 600;">Access Level &amp; Roles</span>
            </div>
            <h1 class="page-title" style="margin: 0; font-size: 1.6rem; font-weight: 800; color: var(--text-primary); display: flex; align-items: center; gap: 0.6rem;">
                <i class="fa-solid fa-shield-halved" style="color: var(--primary);"></i> Access Level &amp; Role Management
            </h1>
            <p class="page-subtitle" style="margin: 0.2rem 0 0; color: var(--text-muted); font-size: 0.88rem;">
                Configure granular sub-module authorization (View, Add, Edit, Delete, Export) and role activation status.
            </p>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            <button type="button" class="btn btn-outline" onclick="openAddRoleModal()" style="border-radius: 8px; font-weight: 600;">
                <i class="fa-solid fa-plus"></i> Add Custom Role
            </button>
            <button type="button" class="btn btn-outline" onclick="resetCurrentRoleDefaults()" style="border-radius: 8px; font-weight: 600;" title="Restore standard default permissions for selected role">
                <i class="fa-solid fa-arrows-rotate"></i> Reset Defaults
            </button>
            <button type="button" class="btn btn-primary" onclick="saveCurrentPermissions()" style="border-radius: 8px; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem;">
                <i class="fa-solid fa-floppy-disk"></i> Save Permissions
            </button>
        </div>
    </div>

    <!-- Main 2-Column Master Layout -->
    <div style="display: grid; grid-template-columns: 330px 1fr; gap: 1.5rem; align-items: start;">
        
        <!-- Left Column: Role Directory -->
        <div class="card" style="box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04); border-radius: 16px; overflow: hidden; border: 1px solid var(--border-color);">
            <div style="padding: 1.15rem 1.25rem; border-bottom: 1px solid var(--border-color); background: #FCFDFB; display: flex; align-items: center; justify-content: space-between;">
                <div style="font-weight: 700; font-size: 0.92rem; color: var(--text-primary); display: flex; align-items: center; gap: 0.45rem;">
                    <i class="fa-solid fa-id-badge" style="color: var(--primary);"></i> Roles (<span id="role-count-badge">8</span>)
                </div>
                <button type="button" onclick="openAddRoleModal()" class="btn btn-outline" style="padding: 0.2rem 0.6rem; font-size: 0.75rem; border-radius: 6px; color: var(--primary);" title="Create New Role">
                    <i class="fa-solid fa-plus"></i> New
                </button>
            </div>

            <!-- Role Filter Search -->
            <div style="padding: 0.85rem 1.25rem; border-bottom: 1px solid var(--border-color); background: #FFFFFF;">
                <div style="position: relative;">
                    <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.8rem;"></i>
                    <input type="text" id="role-search-input" placeholder="Search roles..." class="form-control" style="padding-left: 2rem; height: 36px; border-radius: 8px; font-size: 0.82rem;" oninput="filterRolesList(this.value)">
                </div>
            </div>

            <!-- Role List Container -->
            <div id="roles-list-wrapper" style="padding: 0.75rem; display: flex; flex-direction: column; gap: 0.5rem; max-height: calc(100vh - 280px); overflow-y: auto;">
                <!-- Roles injected via JS -->
            </div>
        </div>

        <!-- Right Column: Permission Matrix -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            
            <!-- Active Role Hero Banner Card -->
            <div class="card" style="box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04); border-radius: 16px; overflow: hidden; border: 1px solid var(--border-color);">
                <div id="role-hero-header" style="background: linear-gradient(135deg, #2D4010 0%, #5B841E 100%); padding: 1.5rem 1.75rem; color: #FFFFFF; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1.25rem;">
                    <div style="display: flex; align-items: center; gap: 1.15rem;">
                        <div id="hero-role-icon-box" style="width: 52px; height: 52px; border-radius: 14px; background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(8px); border: 1.5px solid rgba(255, 255, 255, 0.3); color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <div>
                            <div style="display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap;">
                                <h2 id="hero-role-name" style="margin: 0; font-size: 1.35rem; font-weight: 800; color: #FFFFFF;">
                                    Super Administrator
                                </h2>
                                <span id="hero-role-badge" style="font-size: 0.72rem; background: rgba(255, 255, 255, 0.25); color: #FFFFFF; padding: 0.2rem 0.55rem; border-radius: 6px; font-weight: 700;">
                                    System Built-in
                                </span>
                                <span id="hero-role-status-badge" style="font-size: 0.72rem; background: #DCFCE7; color: #166534; padding: 0.2rem 0.55rem; border-radius: 6px; font-weight: 700; display: inline-flex; align-items: center; gap: 0.3rem;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #16A34A;"></span> Active
                                </span>
                            </div>
                            <p id="hero-role-desc" style="margin: 0.25rem 0 0; font-size: 0.84rem; color: rgba(255, 255, 255, 0.9);">
                                Unrestricted master privilege. Full control over system configurations, plants & users.
                            </p>
                        </div>
                    </div>

                    <!-- Role Management & Quick Controls -->
                    <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                        <!-- Edit Role Button -->
                        <button type="button" class="btn" onclick="openEditRoleModal()" style="background: rgba(255, 255, 255, 0.2); color: #FFFFFF; border: 1px solid rgba(255, 255, 255, 0.3); font-size: 0.78rem; font-weight: 700; padding: 0.45rem 0.85rem; border-radius: 6px; cursor: pointer;" title="Edit Role Details">
                            <i class="fa-solid fa-pen-to-square"></i> Edit Role
                        </button>

                        <!-- Toggle Role Status Button -->
                        <button type="button" id="btn-toggle-role-status" class="btn" onclick="toggleRoleStatus()" style="background: rgba(255, 255, 255, 0.2); color: #FFFFFF; border: 1px solid rgba(255, 255, 255, 0.3); font-size: 0.78rem; font-weight: 700; padding: 0.45rem 0.85rem; border-radius: 6px; cursor: pointer;" title="Toggle Active / Inactive Status">
                            <i class="fa-solid fa-power-off"></i> <span id="lbl-toggle-status">Deactivate</span>
                        </button>

                        <!-- Delete Role Button -->
                        <button type="button" id="btn-delete-role" class="btn" onclick="confirmDeleteCurrentRole()" style="background: rgba(239, 68, 68, 0.3); color: #FFFFFF; border: 1px solid rgba(239, 68, 68, 0.5); font-size: 0.78rem; font-weight: 700; padding: 0.45rem 0.85rem; border-radius: 6px; cursor: pointer;" title="Delete this role">
                            <i class="fa-solid fa-trash-can"></i> Delete
                        </button>
                    </div>
                </div>

                <!-- Sub-Permission Quick Preset Bar -->
                <div style="padding: 0.9rem 1.75rem; background: #FCFDFB; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                    <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                        <span style="font-size: 0.84rem; font-weight: 700; color: var(--text-primary);">
                            Sub-Permission Scope:
                        </span>
                        <span id="permission-ratio-text" style="font-size: 0.84rem; font-weight: 800; color: var(--primary);">
                            140 / 140 Actions Granted (100%)
                        </span>
                        <div style="width: 140px; height: 8px; border-radius: 4px; background: #E2E8F0; overflow: hidden;">
                            <div id="permission-progress-bar" style="height: 100%; width: 100%; background: var(--primary); transition: width 0.3s ease;"></div>
                        </div>
                    </div>

                    <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                        <button type="button" class="btn btn-outline" style="font-size: 0.75rem; padding: 0.3rem 0.65rem; border-radius: 6px; font-weight: 700;" onclick="toggleAllSubPermissions(true)">
                            <i class="fa-solid fa-check-double"></i> Full Access
                        </button>
                        <button type="button" class="btn btn-outline" style="font-size: 0.75rem; padding: 0.3rem 0.65rem; border-radius: 6px; font-weight: 700;" onclick="setReadOnlyPreset()">
                            <i class="fa-regular fa-eye"></i> Read Only
                        </button>
                        <button type="button" class="btn btn-outline" style="font-size: 0.75rem; padding: 0.3rem 0.65rem; border-radius: 6px; font-weight: 700; color: #EF4444;" onclick="toggleAllSubPermissions(false)">
                            <i class="fa-solid fa-ban"></i> Revoke All
                        </button>
                    </div>
                </div>

                <!-- Inactive Role Warning Banner -->
                <div id="inactive-role-notice" style="display: none; padding: 0.9rem 1.5rem; background: #FEF2F2; border-bottom: 1px solid #FECACA; color: #991B1B; font-size: 0.82rem; align-items: center; gap: 0.6rem;">
                    <i class="fa-solid fa-circle-exclamation" style="font-size: 1.1rem; color: #EF4444;"></i>
                    <span><strong>This role is currently INACTIVE.</strong> Operators assigned to this role cannot log in or perform actions until it is reactivated.</span>
                </div>

                <!-- Super Admin Notice Banner -->
                <div id="super-admin-notice" style="display: none; padding: 0.9rem 1.5rem; background: #FEFCE8; border-bottom: 1px solid #FEF08A; color: #854D0E; font-size: 0.82rem; align-items: center; gap: 0.6rem;">
                    <i class="fa-solid fa-crown" style="font-size: 1.1rem; color: #CA8A04;"></i>
                    <span><strong>Super Administrator</strong> inherently possesses unrestricted access across all ERP master registers, transactional ledgers, and database management modules.</span>
                </div>

                <!-- Granular Permissions Grid Grouped by Category -->
                <div style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1.75rem;">
                    
                    <!-- Group 1: Masters & Static Records -->
                    <div>
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.85rem; padding-bottom: 0.4rem; border-bottom: 1.5px solid #F1F5F9;">
                            <div style="font-weight: 800; font-size: 0.95rem; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
                                <span style="width: 28px; height: 28px; border-radius: 6px; background: rgba(91, 132, 30, 0.12); color: var(--primary); display: inline-flex; align-items: center; justify-content: center; font-size: 0.85rem;">
                                    <i class="fa-solid fa-folder-tree"></i>
                                </span>
                                1. Master Records &amp; Directory Permissions
                            </div>
                            <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">9 Modules</span>
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 0.85rem;">
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
                                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                                            <div class="perm-icon-box">
                                                <i class="fa-solid {{ $data[1] }}"></i>
                                            </div>
                                            <div>
                                                <div style="font-weight: 700; font-size: 0.9rem; color: var(--text-primary);">{{ $data[0] }}</div>
                                                <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $data[2] }}</div>
                                            </div>
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 0.75rem;">
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
                    <div>
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.85rem; padding-bottom: 0.4rem; border-bottom: 1.5px solid #F1F5F9;">
                            <div style="font-weight: 800; font-size: 0.95rem; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
                                <span style="width: 28px; height: 28px; border-radius: 6px; background: rgba(59, 130, 246, 0.12); color: #2563EB; display: inline-flex; align-items: center; justify-content: center; font-size: 0.85rem;">
                                    <i class="fa-solid fa-right-left"></i>
                                </span>
                                2. Commercial Transactions &amp; Vouchers
                            </div>
                            <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">8 Modules</span>
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 0.85rem;">
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
                                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                                            <div class="perm-icon-box" style="background: rgba(59, 130, 246, 0.1); color: #2563EB;">
                                                <i class="fa-solid {{ $data[1] }}"></i>
                                            </div>
                                            <div>
                                                <div style="font-weight: 700; font-size: 0.9rem; color: var(--text-primary);">{{ $data[0] }}</div>
                                                <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $data[2] }}</div>
                                            </div>
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 0.75rem;">
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
                    <div>
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.85rem; padding-bottom: 0.4rem; border-bottom: 1.5px solid #F1F5F9;">
                            <div style="font-weight: 800; font-size: 0.95rem; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
                                <span style="width: 28px; height: 28px; border-radius: 6px; background: rgba(217, 119, 6, 0.12); color: #D97706; display: inline-flex; align-items: center; justify-content: center; font-size: 0.85rem;">
                                    <i class="fa-solid fa-boxes-stacked"></i>
                                </span>
                                3. Inventory &amp; Warehouse Control
                            </div>
                            <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">4 Modules</span>
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 0.85rem;">
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
                                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                                            <div class="perm-icon-box" style="background: rgba(217, 119, 6, 0.1); color: #D97706;">
                                                <i class="fa-solid {{ $data[1] }}"></i>
                                            </div>
                                            <div>
                                                <div style="font-weight: 700; font-size: 0.9rem; color: var(--text-primary);">{{ $data[0] }}</div>
                                                <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $data[2] }}</div>
                                            </div>
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 0.75rem;">
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
                    <div>
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.85rem; padding-bottom: 0.4rem; border-bottom: 1.5px solid #F1F5F9;">
                            <div style="font-weight: 800; font-size: 0.95rem; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
                                <span style="width: 28px; height: 28px; border-radius: 6px; background: rgba(5, 150, 105, 0.12); color: #059669; display: inline-flex; align-items: center; justify-content: center; font-size: 0.85rem;">
                                    <i class="fa-solid fa-file-contract"></i>
                                </span>
                                4. Reports &amp; Financial Statements
                            </div>
                            <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">4 Modules</span>
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 0.85rem;">
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
                                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                                            <div class="perm-icon-box" style="background: rgba(5, 150, 105, 0.1); color: #059669;">
                                                <i class="fa-solid {{ $data[1] }}"></i>
                                            </div>
                                            <div>
                                                <div style="font-weight: 700; font-size: 0.9rem; color: var(--text-primary);">{{ $data[0] }}</div>
                                                <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $data[2] }}</div>
                                            </div>
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 0.75rem;">
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
                    <div>
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.85rem; padding-bottom: 0.4rem; border-bottom: 1.5px solid #F1F5F9;">
                            <div style="font-weight: 800; font-size: 0.95rem; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
                                <span style="width: 28px; height: 28px; border-radius: 6px; background: rgba(139, 92, 246, 0.12); color: #8B5CF6; display: inline-flex; align-items: center; justify-content: center; font-size: 0.85rem;">
                                    <i class="fa-solid fa-gears"></i>
                                </span>
                                5. System Administration &amp; Integration
                            </div>
                            <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">3 Modules</span>
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 0.85rem;">
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
                                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                                            <div class="perm-icon-box" style="background: rgba(139, 92, 246, 0.1); color: #8B5CF6;">
                                                <i class="{{ $data[1] }}"></i>
                                            </div>
                                            <div>
                                                <div style="font-weight: 700; font-size: 0.9rem; color: var(--text-primary);">{{ $data[0] }}</div>
                                                <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $data[2] }}</div>
                                            </div>
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 0.75rem;">
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
<div class="modal-overlay" id="modal-add-role" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div class="modal-card" style="background: #FFFFFF; width: 100%; max-width: 480px; border-radius: 16px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); overflow: hidden;">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color); background: #FCFDFB; display: flex; align-items: center; justify-content: space-between;">
            <div style="font-weight: 700; font-size: 1.05rem; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
                <i class="fa-solid fa-shield-plus" style="color: var(--primary);"></i> Add Custom Role
            </div>
            <button type="button" onclick="closeAddRoleModal()" style="background: none; border: none; font-size: 1.25rem; color: var(--text-muted); cursor: pointer;">&times;</button>
        </div>
        <form onsubmit="handleCreateRoleSubmit(event)" style="padding: 1.5rem;">
            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label class="form-label" style="font-weight: 600; font-size: 0.88rem; margin-bottom: 0.4rem; display: block;">
                    Role Title <span style="color: #EF4444;">*</span>
                </label>
                <input type="text" id="new-role-name" class="form-control" placeholder="e.g. Quality Inspector, Dispatch Supervisor" required style="height: 42px; border-radius: 8px;">
            </div>

            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label class="form-label" style="font-weight: 600; font-size: 0.88rem; margin-bottom: 0.4rem; display: block;">
                    Role Description
                </label>
                <input type="text" id="new-role-desc" class="form-control" placeholder="Short summary of role scope" style="height: 42px; border-radius: 8px;">
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-label" style="font-weight: 600; font-size: 0.88rem; margin-bottom: 0.4rem; display: block;">
                    Base Permission Template
                </label>
                <select id="new-role-template" class="form-control" style="height: 42px; border-radius: 8px;">
                    <option value="Staff">Staff (Standard view & data entry)</option>
                    <option value="Manager">Manager (Operations & reports)</option>
                    <option value="Accountant">Accountant (Ledgers & vouchers)</option>
                    <option value="Sales Manager">Sales Manager (Commercial & dispatch)</option>
                    <option value="Purchase Manager">Purchase Manager (Inward & procurement)</option>
                    <option value="Inventory Operator">Inventory Operator (Warehouse)</option>
                </select>
            </div>

            <div style="display: flex; align-items: center; justify-content: flex-end; gap: 0.75rem;">
                <button type="button" class="btn btn-outline" onclick="closeAddRoleModal()" style="border-radius: 8px;">Cancel</button>
                <button type="submit" class="btn btn-primary" style="border-radius: 8px; font-weight: 700;">Create Role</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Role Details -->
<div class="modal-overlay" id="modal-edit-role" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div class="modal-card" style="background: #FFFFFF; width: 100%; max-width: 480px; border-radius: 16px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); overflow: hidden;">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color); background: #FCFDFB; display: flex; align-items: center; justify-content: space-between;">
            <div style="font-weight: 700; font-size: 1.05rem; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
                <i class="fa-solid fa-pen-to-square" style="color: var(--primary);"></i> Edit Role Details
            </div>
            <button type="button" onclick="closeEditRoleModal()" style="background: none; border: none; font-size: 1.25rem; color: var(--text-muted); cursor: pointer;">&times;</button>
        </div>
        <form onsubmit="handleEditRoleSubmit(event)" style="padding: 1.5rem;">
            <input type="hidden" id="edit-role-old-key">

            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label class="form-label" style="font-weight: 600; font-size: 0.88rem; margin-bottom: 0.4rem; display: block;">
                    Role Title <span style="color: #EF4444;">*</span>
                </label>
                <input type="text" id="edit-role-name" class="form-control" required style="height: 42px; border-radius: 8px;">
            </div>

            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label class="form-label" style="font-weight: 600; font-size: 0.88rem; margin-bottom: 0.4rem; display: block;">
                    Role Description
                </label>
                <textarea id="edit-role-desc" class="form-control" rows="3" style="border-radius: 8px; resize: none;"></textarea>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-label" style="font-weight: 600; font-size: 0.88rem; margin-bottom: 0.4rem; display: block;">
                    Role Status
                </label>
                <select id="edit-role-status" class="form-control" style="height: 42px; border-radius: 8px;">
                    <option value="active">Active (Available for user assignment)</option>
                    <option value="inactive">Inactive (Suspended / Deactivated)</option>
                </select>
            </div>

            <div style="display: flex; align-items: center; justify-content: flex-end; gap: 0.75rem;">
                <button type="button" class="btn btn-outline" onclick="closeEditRoleModal()" style="border-radius: 8px;">Cancel</button>
                <button type="submit" class="btn btn-primary" style="border-radius: 8px; font-weight: 700;">Save Role</button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
.perm-module-card {
    background: #FFFFFF;
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 1rem 1.25rem;
    transition: all 0.2s ease;
}

.perm-module-card:hover {
    border-color: #CBD5E1;
    box-shadow: 0 3px 10px rgba(0,0,0,0.03);
}

.perm-module-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 0.75rem;
}

.perm-icon-box {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: rgba(91, 132, 30, 0.1);
    color: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.95rem;
    flex-shrink: 0;
}

.sub-perm-badge {
    font-size: 0.72rem;
    font-weight: 700;
    color: var(--primary);
    background: rgba(91, 132, 30, 0.1);
    padding: 0.2rem 0.55rem;
    border-radius: 6px;
}

.sub-actions-container {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
    padding-top: 0.75rem;
    border-top: 1px dashed #F1F5F9;
}

.action-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.3rem 0.65rem;
    border-radius: 6px;
    font-size: 0.76rem;
    font-weight: 600;
    cursor: pointer;
    background: #F8FAFC;
    color: #475569;
    border: 1px solid #E2E8F0;
    user-select: none;
    transition: all 0.15s ease;
}

.action-pill:hover {
    background: #F1F5F9;
    border-color: #CBD5E1;
}

.action-pill.active {
    background: #F0FDF4;
    color: #15803D;
    border-color: #86EFAC;
    box-shadow: 0 1px 3px rgba(22, 163, 74, 0.1);
}

.action-pill input[type="checkbox"] {
    margin: 0;
    accent-color: var(--primary);
}

.role-nav-item {
    padding: 0.9rem 1rem;
    border-radius: 10px;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
}

.role-nav-item:hover {
    background: #F8FAF6;
    border-color: #E2E8F0;
}

.role-nav-item.active {
    background: #F4F7EE;
    border-color: var(--primary);
    box-shadow: 0 2px 8px rgba(91, 132, 30, 0.12);
}

.role-nav-item.inactive-role {
    opacity: 0.65;
}
</style>
@endpush

@push('scripts')
<script>
const ACTIONS_LIST = ['view', 'add', 'edit', 'delete', 'export'];

// Roles Database
let ROLE_DEFINITIONS = [
    {
        key: 'Super Administrator',
        name: 'Super Administrator',
        icon: 'fa-shield-halved',
        color: '#7E22CE',
        bg: '#FAF5FF',
        badge: 'Master Level',
        isSystem: true,
        status: 'active',
        desc: 'Unrestricted master privilege. Full control over system configurations, plants & users.',
        defaultModules: ['company', 'user', 'access_level', 'vendor', 'customer', 'broker', 'item', 'unit', 'account', 'purchase_entry', 'wb_purchase_entry', 'sales_entry', 'wb_sales_entry', 'order_dispatch', 'sales_purchase_order', 'receipt_voucher', 'payment_voucher', 'stock_overview', 'item_ledger', 'stock_adjustment', 'low_stock_alert', 'purchase_report', 'sales_report', 'order_report', 'cash_bank_register', 'company_settings', 'whatsapp_settings', 'backup_restore']
    },
    {
        key: 'Admin',
        name: 'Admin',
        icon: 'fa-user-gear',
        color: '#15803D',
        bg: '#F0FDF4',
        badge: 'Admin Level',
        isSystem: true,
        status: 'active',
        desc: 'Operational administrator with management access across all transaction modules.',
        defaultModules: ['company', 'user', 'vendor', 'customer', 'broker', 'item', 'unit', 'account', 'purchase_entry', 'wb_purchase_entry', 'sales_entry', 'wb_sales_entry', 'order_dispatch', 'sales_purchase_order', 'receipt_voucher', 'payment_voucher', 'stock_overview', 'item_ledger', 'stock_adjustment', 'low_stock_alert', 'purchase_report', 'sales_report', 'order_report', 'cash_bank_register', 'company_settings', 'whatsapp_settings']
    },
    {
        key: 'Manager',
        name: 'Manager',
        icon: 'fa-briefcase',
        color: '#1D4ED8',
        bg: '#EFF6FF',
        badge: 'Operations',
        isSystem: true,
        status: 'active',
        desc: 'Plant & production oversight, operational approvals and analytical summary reports.',
        defaultModules: ['vendor', 'customer', 'broker', 'item', 'unit', 'purchase_entry', 'wb_purchase_entry', 'sales_entry', 'wb_sales_entry', 'order_dispatch', 'sales_purchase_order', 'receipt_voucher', 'payment_voucher', 'stock_overview', 'item_ledger', 'stock_adjustment', 'low_stock_alert', 'purchase_report', 'sales_report', 'order_report']
    },
    {
        key: 'Accountant',
        name: 'Accountant',
        icon: 'fa-calculator',
        color: '#0F766E',
        bg: '#F0FDFA',
        badge: 'Finance',
        isSystem: true,
        status: 'active',
        desc: 'Financial ledgers, payment/receipt vouchers, billing and tax GST audit reports.',
        defaultModules: ['vendor', 'customer', 'broker', 'item', 'unit', 'account', 'sales_entry', 'purchase_entry', 'receipt_voucher', 'payment_voucher', 'stock_overview', 'item_ledger', 'purchase_report', 'sales_report', 'cash_bank_register']
    },
    {
        key: 'Sales Manager',
        name: 'Sales Manager',
        icon: 'fa-chart-line',
        color: '#B45309',
        bg: '#FFFBEB',
        badge: 'Commercial',
        isSystem: true,
        status: 'active',
        desc: 'Customer orders, dispatch manifests, sales invoices and client ledger monitoring.',
        defaultModules: ['customer', 'broker', 'item', 'sales_entry', 'wb_sales_entry', 'order_dispatch', 'sales_purchase_order', 'stock_overview', 'sales_report', 'order_report']
    },
    {
        key: 'Purchase Manager',
        name: 'Purchase Manager',
        icon: 'fa-cart-flatbed',
        color: '#047857',
        bg: '#ECFDF5',
        badge: 'Procurement',
        isSystem: true,
        status: 'active',
        desc: 'Raw material procurement, weighbridge receipts and supplier purchase entries.',
        defaultModules: ['vendor', 'broker', 'item', 'unit', 'purchase_entry', 'wb_purchase_entry', 'sales_purchase_order', 'stock_overview', 'item_ledger', 'purchase_report']
    },
    {
        key: 'Inventory Operator',
        name: 'Inventory Operator',
        icon: 'fa-boxes-stacked',
        color: '#0E7490',
        bg: '#ECFEFF',
        badge: 'Warehouse',
        isSystem: true,
        status: 'active',
        desc: 'Warehouse stock adjustments, item tracking, transfer vouchers and low stock monitoring.',
        defaultModules: ['item', 'unit', 'stock_overview', 'item_ledger', 'stock_adjustment', 'low_stock_alert', 'order_dispatch']
    },
    {
        key: 'Staff',
        name: 'Staff',
        icon: 'fa-user-pen',
        color: '#475569',
        bg: '#F8FAFC',
        badge: 'Standard',
        isSystem: true,
        status: 'active',
        desc: 'Basic transactional data entry and view permissions with restricted settings.',
        defaultModules: ['item', 'customer', 'vendor', 'sales_entry', 'purchase_entry', 'stock_overview']
    }
];

const ALL_MODULE_KEYS = [
    'company', 'user', 'access_level', 'vendor', 'customer', 'broker', 'item', 'unit', 'account',
    'purchase_entry', 'wb_purchase_entry', 'sales_entry', 'wb_sales_entry', 'order_dispatch', 'sales_purchase_order', 'receipt_voucher', 'payment_voucher',
    'stock_overview', 'item_ledger', 'stock_adjustment', 'low_stock_alert',
    'purchase_report', 'sales_report', 'order_report', 'cash_bank_register',
    'company_settings', 'whatsapp_settings', 'backup_restore'
];

// User counts passed from backend
const DB_USER_COUNTS = @json($userCounts ?? []);

let currentRoleKey = 'Super Administrator';
let roleSubPermissionsStore = {};

function initAccessLevelManager() {
    // Load custom roles if saved
    const savedRoles = localStorage.getItem('vu_custom_roles_list');
    if (savedRoles) {
        try {
            ROLE_DEFINITIONS = JSON.parse(savedRoles);
        } catch(e) {}
    }

    // Load sub-permissions matrix
    const savedMatrix = localStorage.getItem('vu_role_granular_permissions');
    if (savedMatrix) {
        try {
            roleSubPermissionsStore = JSON.parse(savedMatrix);
        } catch(e) {
            roleSubPermissionsStore = {};
        }
    }

    // Initialize defaults for missing roles
    ROLE_DEFINITIONS.forEach(r => {
        if (!roleSubPermissionsStore[r.key]) {
            roleSubPermissionsStore[r.key] = {};
            ALL_MODULE_KEYS.forEach(mod => {
                const isEnabled = (r.defaultModules || []).includes(mod);
                roleSubPermissionsStore[r.key][mod] = {
                    view: isEnabled,
                    add: isEnabled && !['access_level', 'backup_restore'].includes(mod),
                    edit: isEnabled && !['access_level', 'backup_restore'].includes(mod),
                    delete: isEnabled && r.key.includes('Admin'),
                    export: isEnabled
                };
            });
        }
    });

    renderRolesList();
    selectRole(currentRoleKey);
}

function renderRolesList() {
    const container = document.getElementById('roles-list-wrapper');
    if (!container) return;

    let html = '';
    ROLE_DEFINITIONS.forEach(r => {
        const userCount = DB_USER_COUNTS[r.key] || 0;
        const isActive = r.key === currentRoleKey;
        const isInactive = r.status === 'inactive';
        html += `
            <div class="role-nav-item ${isActive ? 'active' : ''} ${isInactive ? 'inactive-role' : ''}" onclick="selectRole('${r.key}')">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 36px; height: 36px; border-radius: 8px; background: ${r.bg}; color: ${r.color}; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0;">
                        <i class="fa-solid ${r.icon}"></i>
                    </div>
                    <div>
                        <div style="font-weight: 700; font-size: 0.88rem; color: var(--text-primary); display: flex; align-items: center; gap: 0.35rem;">
                            ${r.name}
                            ${isInactive ? '<span style="font-size: 0.65rem; background: #FEE2E2; color: #991B1B; padding: 0.1rem 0.35rem; border-radius: 4px; font-weight: 700;">Inactive</span>' : ''}
                        </div>
                        <div style="font-size: 0.72rem; color: var(--text-muted);">
                            ${userCount} ${userCount === 1 ? 'Operator' : 'Operators'} Assigned
                        </div>
                    </div>
                </div>
                ${isActive ? '<i class="fa-solid fa-chevron-right" style="color: var(--primary); font-size: 0.75rem;"></i>' : ''}
            </div>
        `;
    });

    container.innerHTML = html;
    document.getElementById('role-count-badge').innerText = ROLE_DEFINITIONS.length;
}

function filterRolesList(query) {
    const q = query.toLowerCase().trim();
    document.querySelectorAll('#roles-list-wrapper .role-nav-item').forEach(el => {
        const text = el.innerText.toLowerCase();
        el.style.display = text.includes(q) ? 'flex' : 'none';
    });
}

function selectRole(roleKey) {
    currentRoleKey = roleKey;
    const role = ROLE_DEFINITIONS.find(r => r.key === roleKey) || ROLE_DEFINITIONS[0];

    renderRolesList();

    // Update Hero Banner
    document.getElementById('hero-role-name').innerText = role.name;
    document.getElementById('hero-role-desc').innerText = role.desc;
    document.getElementById('hero-role-badge').innerText = role.badge || (role.isSystem ? 'System Built-in' : 'Custom Role');
    document.getElementById('hero-role-icon-box').innerHTML = `<i class="fa-solid ${role.icon}"></i>`;

    const isSuperAdmin = role.key === 'Super Administrator';
    const isInactive = role.status === 'inactive';

    // Status Badge
    const statusBadge = document.getElementById('hero-role-status-badge');
    if (isInactive) {
        statusBadge.style.background = '#FEE2E2';
        statusBadge.style.color = '#991B1B';
        statusBadge.innerHTML = '<span style="width: 6px; height: 6px; border-radius: 50%; background: #DC2626;"></span> Inactive';
        document.getElementById('lbl-toggle-status').innerText = 'Activate';
        document.getElementById('inactive-role-notice').style.display = 'flex';
    } else {
        statusBadge.style.background = '#DCFCE7';
        statusBadge.style.color = '#166534';
        statusBadge.innerHTML = '<span style="width: 6px; height: 6px; border-radius: 50%; background: #16A34A;"></span> Active';
        document.getElementById('lbl-toggle-status').innerText = 'Deactivate';
        document.getElementById('inactive-role-notice').style.display = 'none';
    }

    // Toggle status button disabled for Super Admin
    document.getElementById('btn-toggle-role-status').disabled = isSuperAdmin;
    document.getElementById('btn-toggle-role-status').style.opacity = isSuperAdmin ? '0.5' : '1';

    // Delete button disabled for System Roles
    const deleteBtn = document.getElementById('btn-delete-role');
    if (role.isSystem) {
        deleteBtn.style.display = 'none';
    } else {
        deleteBtn.style.display = 'inline-flex';
    }

    // Notice
    document.getElementById('super-admin-notice').style.display = isSuperAdmin ? 'flex' : 'none';

    // Ensure store exists
    if (!roleSubPermissionsStore[role.key]) {
        roleSubPermissionsStore[role.key] = {};
    }

    // Populate all module cards & sub-actions
    ALL_MODULE_KEYS.forEach(mod => {
        let modData = roleSubPermissionsStore[role.key][mod] || { view: false, add: false, edit: false, delete: false, export: false };
        if (isSuperAdmin) {
            modData = { view: true, add: true, edit: true, delete: true, export: true };
        }

        const masterSwitch = document.getElementById(`perm-${mod}-master`);
        const anyChecked = Object.values(modData).some(v => v === true);
        if (masterSwitch) {
            masterSwitch.checked = anyChecked;
            masterSwitch.disabled = isSuperAdmin;
        }

        ACTIONS_LIST.forEach(act => {
            const chk = document.getElementById(`act-${mod}-${act}`);
            const pill = document.getElementById(`pill-${mod}-${act}`);
            if (chk) {
                chk.checked = !!modData[act];
                chk.disabled = isSuperAdmin;
            }
            if (pill) {
                if (modData[act]) {
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
    if (currentRoleKey === 'Super Administrator') return;

    if (!roleSubPermissionsStore[currentRoleKey]) {
        roleSubPermissionsStore[currentRoleKey] = {};
    }
    if (!roleSubPermissionsStore[currentRoleKey][modKey]) {
        roleSubPermissionsStore[currentRoleKey][modKey] = { view: false, add: false, edit: false, delete: false, export: false };
    }

    roleSubPermissionsStore[currentRoleKey][modKey][actionKey] = isChecked;

    // If add/edit/delete/export is checked, automatically ensure view is checked
    if (isChecked && actionKey !== 'view') {
        roleSubPermissionsStore[currentRoleKey][modKey]['view'] = true;
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
    const anyActive = Object.values(roleSubPermissionsStore[currentRoleKey][modKey]).some(v => v === true);
    const masterSwitch = document.getElementById(`perm-${modKey}-master`);
    if (masterSwitch) masterSwitch.checked = anyActive;

    updateModuleActionCountBadge(modKey);
    updateCoverageMeter();
}

function toggleModuleAllActions(modKey, enableAll) {
    if (currentRoleKey === 'Super Administrator') return;

    if (!roleSubPermissionsStore[currentRoleKey]) {
        roleSubPermissionsStore[currentRoleKey] = {};
    }

    roleSubPermissionsStore[currentRoleKey][modKey] = {
        view: enableAll,
        add: enableAll,
        edit: enableAll,
        delete: enableAll,
        export: enableAll
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

    const modData = roleSubPermissionsStore[currentRoleKey]?.[modKey] || {};
    const activeCount = ACTIONS_LIST.filter(a => modData[a] === true).length;
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

function toggleAllSubPermissions(enableAll) {
    if (currentRoleKey === 'Super Administrator') return;

    if (!roleSubPermissionsStore[currentRoleKey]) {
        roleSubPermissionsStore[currentRoleKey] = {};
    }

    ALL_MODULE_KEYS.forEach(mod => {
        roleSubPermissionsStore[currentRoleKey][mod] = {
            view: enableAll,
            add: enableAll,
            edit: enableAll,
            delete: enableAll,
            export: enableAll
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
    if (currentRoleKey === 'Super Administrator') return;

    if (!roleSubPermissionsStore[currentRoleKey]) {
        roleSubPermissionsStore[currentRoleKey] = {};
    }

    ALL_MODULE_KEYS.forEach(mod => {
        roleSubPermissionsStore[currentRoleKey][mod] = {
            view: true,
            add: false,
            edit: false,
            delete: false,
            export: true
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

function resetCurrentRoleDefaults() {
    const role = ROLE_DEFINITIONS.find(r => r.key === currentRoleKey);
    if (!role) return;

    roleSubPermissionsStore[role.key] = {};
    ALL_MODULE_KEYS.forEach(mod => {
        const isEnabled = (role.defaultModules || []).includes(mod);
        roleSubPermissionsStore[role.key][mod] = {
            view: isEnabled,
            add: isEnabled && !['access_level', 'backup_restore'].includes(mod),
            edit: isEnabled && !['access_level', 'backup_restore'].includes(mod),
            delete: isEnabled && role.key.includes('Admin'),
            export: isEnabled
        };
    });

    selectRole(currentRoleKey);
    toastr.success(`Restored default permissions for ${role.name}.`);
}

function updateCoverageMeter() {
    const totalPossible = ALL_MODULE_KEYS.length * 5;
    let totalGranted = 0;

    if (currentRoleKey === 'Super Administrator') {
        totalGranted = totalPossible;
    } else {
        const modStore = roleSubPermissionsStore[currentRoleKey] || {};
        ALL_MODULE_KEYS.forEach(mod => {
            const m = modStore[mod] || {};
            ACTIONS_LIST.forEach(act => {
                if (m[act] === true) totalGranted++;
            });
        });
    }

    const pct = Math.round((totalGranted / totalPossible) * 100);
    document.getElementById('permission-ratio-text').innerText = `${totalGranted} / ${totalPossible} Actions Granted (${pct}%)`;
    document.getElementById('permission-progress-bar').style.width = pct + '%';
}

function saveCurrentPermissions() {
    localStorage.setItem('vu_role_granular_permissions', JSON.stringify(roleSubPermissionsStore));
    localStorage.setItem('vu_custom_roles_list', JSON.stringify(ROLE_DEFINITIONS));
    toastr.success(`Access permissions and role configuration for "${currentRoleKey}" updated successfully!`);
}

function toggleRoleStatus() {
    const role = ROLE_DEFINITIONS.find(r => r.key === currentRoleKey);
    if (!role) return;

    if (role.key === 'Super Administrator') {
        toastr.error('Super Administrator role cannot be deactivated.');
        return;
    }

    role.status = role.status === 'active' ? 'inactive' : 'active';
    localStorage.setItem('vu_custom_roles_list', JSON.stringify(ROLE_DEFINITIONS));

    selectRole(currentRoleKey);
    toastr.success(`Role "${role.name}" status changed to ${role.status.toUpperCase()}.`);
}

function openAddRoleModal() {
    document.getElementById('modal-add-role').style.display = 'flex';
}

function closeAddRoleModal() {
    document.getElementById('modal-add-role').style.display = 'none';
}

function handleCreateRoleSubmit(e) {
    e.preventDefault();
    const name = document.getElementById('new-role-name').value.trim();
    const desc = document.getElementById('new-role-desc').value.trim() || 'Custom organizational designation.';
    const template = document.getElementById('new-role-template').value;

    if (!name) return;

    const baseRole = ROLE_DEFINITIONS.find(r => r.key === template) || ROLE_DEFINITIONS[0];

    const newRole = {
        key: name,
        name: name,
        icon: 'fa-user-tag',
        color: '#2563EB',
        bg: '#EFF6FF',
        badge: 'Custom Role',
        isSystem: false,
        status: 'active',
        desc: desc,
        defaultModules: [...(baseRole.defaultModules || [])]
    };

    ROLE_DEFINITIONS.push(newRole);

    // Initialize granular permissions for new role
    roleSubPermissionsStore[name] = {};
    ALL_MODULE_KEYS.forEach(mod => {
        const isEnabled = (newRole.defaultModules || []).includes(mod);
        roleSubPermissionsStore[name][mod] = {
            view: isEnabled,
            add: isEnabled,
            edit: isEnabled,
            delete: false,
            export: isEnabled
        };
    });

    localStorage.setItem('vu_custom_roles_list', JSON.stringify(ROLE_DEFINITIONS));
    localStorage.setItem('vu_role_granular_permissions', JSON.stringify(roleSubPermissionsStore));

    closeAddRoleModal();
    renderRolesList();
    selectRole(name);
    toastr.success(`Custom role "${name}" created with ${template} template!`);
}

function openEditRoleModal() {
    const role = ROLE_DEFINITIONS.find(r => r.key === currentRoleKey);
    if (!role) return;

    document.getElementById('edit-role-old-key').value = role.key;
    document.getElementById('edit-role-name').value = role.name;
    document.getElementById('edit-role-desc').value = role.desc || '';
    document.getElementById('edit-role-status').value = role.status || 'active';

    if (role.key === 'Super Administrator') {
        document.getElementById('edit-role-status').disabled = true;
    } else {
        document.getElementById('edit-role-status').disabled = false;
    }

    document.getElementById('modal-edit-role').style.display = 'flex';
}

function closeEditRoleModal() {
    document.getElementById('modal-edit-role').style.display = 'none';
}

function handleEditRoleSubmit(e) {
    e.preventDefault();
    const oldKey = document.getElementById('edit-role-old-key').value;
    const newName = document.getElementById('edit-role-name').value.trim();
    const newDesc = document.getElementById('edit-role-desc').value.trim();
    const newStatus = document.getElementById('edit-role-status').value;

    const role = ROLE_DEFINITIONS.find(r => r.key === oldKey);
    if (!role) return;

    role.name = newName;
    role.desc = newDesc;
    if (role.key !== 'Super Administrator') {
        role.status = newStatus;
    }

    // If key changed for custom role
    if (oldKey !== newName && !role.isSystem) {
        role.key = newName;
        roleSubPermissionsStore[newName] = roleSubPermissionsStore[oldKey];
        delete roleSubPermissionsStore[oldKey];
        currentRoleKey = newName;
    }

    localStorage.setItem('vu_custom_roles_list', JSON.stringify(ROLE_DEFINITIONS));
    localStorage.setItem('vu_role_granular_permissions', JSON.stringify(roleSubPermissionsStore));

    closeEditRoleModal();
    renderRolesList();
    selectRole(currentRoleKey);
    toastr.success(`Role "${newName}" details updated successfully!`);
}

function confirmDeleteCurrentRole() {
    const role = ROLE_DEFINITIONS.find(r => r.key === currentRoleKey);
    if (!role || role.isSystem) {
        toastr.error('System built-in roles cannot be deleted.');
        return;
    }

    const assignedCount = DB_USER_COUNTS[role.key] || 0;

    Swal.fire({
        title: `Delete Role "${role.name}"?`,
        html: assignedCount > 0 
            ? `<div style="color: #DC2626; font-weight: 700; margin-bottom: 0.5rem;">Warning: ${assignedCount} active user(s) currently assigned to this role!</div><div>Deleting this role will revoke their permissions.</div>` 
            : `Are you sure you want to delete this custom role?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC2626',
        cancelButtonColor: '#64748B',
        confirmButtonText: '<i class="fa-solid fa-trash-can"></i> Yes, Delete Role',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            ROLE_DEFINITIONS = ROLE_DEFINITIONS.filter(r => r.key !== role.key);
            delete roleSubPermissionsStore[role.key];

            localStorage.setItem('vu_custom_roles_list', JSON.stringify(ROLE_DEFINITIONS));
            localStorage.setItem('vu_role_granular_permissions', JSON.stringify(roleSubPermissionsStore));

            currentRoleKey = 'Super Administrator';
            renderRolesList();
            selectRole(currentRoleKey);
            toastr.success(`Role "${role.name}" deleted successfully.`);
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initAccessLevelManager();
});
</script>
@endpush
@endsection
