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
                <span style="color: var(--primary); font-weight: 600;">Access Level Management</span>
            </div>
            <h1 class="page-title" style="margin: 0; font-size: 1.6rem; font-weight: 800; color: var(--text-primary); display: flex; align-items: center; gap: 0.6rem;">
                <i class="fa-solid fa-shield-halved" style="color: var(--primary);"></i> Access Level &amp; Role Management
            </h1>
            <p class="page-subtitle" style="margin: 0.2rem 0 0; color: var(--text-muted); font-size: 0.88rem;">
                Configure granular module authorization, operational access scopes &amp; privilege matrices for employee designations.
            </p>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem;">
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
    <div style="display: grid; grid-template-columns: 320px 1fr; gap: 1.5rem; align-items: start;">
        
        <!-- Left Column: Role Directory -->
        <div class="card" style="box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04); border-radius: 16px; overflow: hidden; border: 1px solid var(--border-color);">
            <div style="padding: 1.15rem 1.25rem; border-bottom: 1px solid var(--border-color); background: #FCFDFB; display: flex; align-items: center; justify-content: space-between;">
                <div style="font-weight: 700; font-size: 0.92rem; color: var(--text-primary); display: flex; align-items: center; gap: 0.45rem;">
                    <i class="fa-solid fa-id-badge" style="color: var(--primary);"></i> System Roles (<span id="role-count-badge">8</span>)
                </div>
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
                <div id="role-hero-header" style="background: linear-gradient(135deg, #2D4010 0%, #5B841E 100%); padding: 1.5rem 1.75rem; color: #FFFFFF; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                    <div style="display: flex; align-items: center; gap: 1.15rem;">
                        <div id="hero-role-icon-box" style="width: 52px; height: 52px; border-radius: 14px; background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(8px); border: 1.5px solid rgba(255, 255, 255, 0.3); color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <div>
                            <div style="display: flex; align-items: center; gap: 0.6rem;">
                                <h2 id="hero-role-name" style="margin: 0; font-size: 1.35rem; font-weight: 800; color: #FFFFFF;">
                                    Super Administrator
                                </h2>
                                <span id="hero-role-badge" style="font-size: 0.72rem; background: rgba(255, 255, 255, 0.25); color: #FFFFFF; padding: 0.2rem 0.55rem; border-radius: 6px; font-weight: 700;">
                                    System Built-in
                                </span>
                            </div>
                            <p id="hero-role-desc" style="margin: 0.25rem 0 0; font-size: 0.84rem; color: rgba(255, 255, 255, 0.9);">
                                Unrestricted master privilege. Full control over system configurations, plants & users.
                            </p>
                        </div>
                    </div>

                    <!-- Quick Granular Controls -->
                    <div style="display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap;">
                        <button type="button" class="btn" style="background: rgba(255, 255, 255, 0.2); color: #FFFFFF; border: 1px solid rgba(255, 255, 255, 0.3); font-size: 0.78rem; font-weight: 700; padding: 0.4rem 0.8rem; border-radius: 6px; cursor: pointer;" onclick="toggleAllPermissions(true)">
                            <i class="fa-solid fa-check-double"></i> Select All
                        </button>
                        <button type="button" class="btn" style="background: rgba(255, 255, 255, 0.2); color: #FFFFFF; border: 1px solid rgba(255, 255, 255, 0.3); font-size: 0.78rem; font-weight: 700; padding: 0.4rem 0.8rem; border-radius: 6px; cursor: pointer;" onclick="toggleAllPermissions(false)">
                            <i class="fa-solid fa-ban"></i> Deselect All
                        </button>
                    </div>
                </div>

                <!-- Permission Stats Bar -->
                <div style="padding: 0.9rem 1.75rem; background: #FCFDFB; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <span style="font-size: 0.84rem; font-weight: 700; color: var(--text-primary);">
                            Module Coverage:
                        </span>
                        <span id="permission-ratio-text" style="font-size: 0.84rem; font-weight: 800; color: var(--primary);">
                            28 / 28 Enabled (100%)
                        </span>
                    </div>

                    <div style="flex: 1; max-width: 240px; height: 8px; border-radius: 4px; background: #E2E8F0; overflow: hidden;">
                        <div id="permission-progress-bar" style="height: 100%; width: 100%; background: var(--primary); transition: width 0.3s ease;"></div>
                    </div>
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
                                1. Master Records &amp; Directory Access
                            </div>
                            <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">9 Modules</span>
                        </div>

                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 0.85rem;">
                            @php
                                $mastersModules = [
                                    'company' => ['Company Master', 'fa-building', 'Corporate entities, plant codes & bank accounts'],
                                    'user' => ['User Master', 'fa-users', 'Operator login credentials & assignments'],
                                    'access_level' => ['Access Level', 'fa-shield-halved', 'Module permissions & role configuration'],
                                    'vendor' => ['Vendor Master', 'fa-truck-field', 'Supplier directory, credit terms & balances'],
                                    'customer' => ['Customer Master', 'fa-users-line', 'Buyer profiles, GSTIN & credit limits'],
                                    'broker' => ['Broker Master', 'fa-handshake', 'Trade agents, commissions & accounts'],
                                    'item' => ['Item Master', 'fa-leaf', 'Henna & herbal items, batches & HSN codes'],
                                    'unit' => ['Unit Master', 'fa-scale-balanced', 'Measurement units (KG, Bag, Metric Ton)'],
                                    'account' => ['Account Master', 'fa-book-bookmark', 'General ledger accounts & finance heads'],
                                ];
                            @endphp

                            @foreach($mastersModules as $key => $data)
                                <div class="perm-card" onclick="togglePermissionItem('{{ $key }}')">
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <div class="perm-icon-box">
                                            <i class="fa-solid {{ $data[1] }}"></i>
                                        </div>
                                        <div>
                                            <div style="font-weight: 700; font-size: 0.88rem; color: var(--text-primary);">{{ $data[0] }}</div>
                                            <div style="font-size: 0.72rem; color: var(--text-muted); line-height: 1.3;">{{ $data[2] }}</div>
                                        </div>
                                    </div>
                                    <label class="switch-control" onclick="event.stopPropagation()">
                                        <input type="checkbox" id="perm-{{ $key }}" onchange="updatePermissionState('{{ $key }}', this.checked)">
                                        <span class="switch-slider"></span>
                                    </label>
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

                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 0.85rem;">
                            @php
                                $transactionModules = [
                                    'purchase_entry' => ['Purchase Entry', 'fa-cart-shopping', 'Direct raw herbal inward purchases'],
                                    'wb_purchase_entry' => ['WB Purchase Entry', 'fa-truck-ramp-box', 'Weighbridge gross/tare inward shipments'],
                                    'sales_entry' => ['Sales Entry', 'fa-file-invoice-dollar', 'Customer sales invoicing and tax bills'],
                                    'wb_sales_entry' => ['WB Sales Entry', 'fa-truck-fast', 'Weighbridge outward goods dispatch'],
                                    'order_dispatch' => ['Order Dispatch', 'fa-dolly', 'Warehouse loading slips and dispatch slips'],
                                    'sales_purchase_order' => ['Sales / Purchase Order', 'fa-file-signature', 'Contract booking and order confirmations'],
                                    'receipt_voucher' => ['Receipt Voucher', 'fa-arrow-down-to-line', 'Customer cash/bank receipt entries'],
                                    'payment_voucher' => ['Payment Voucher', 'fa-arrow-up-from-line', 'Vendor & expense payment entries'],
                                ];
                            @endphp

                            @foreach($transactionModules as $key => $data)
                                <div class="perm-card" onclick="togglePermissionItem('{{ $key }}')">
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <div class="perm-icon-box" style="background: rgba(59, 130, 246, 0.1); color: #2563EB;">
                                            <i class="fa-solid {{ $data[1] }}"></i>
                                        </div>
                                        <div>
                                            <div style="font-weight: 700; font-size: 0.88rem; color: var(--text-primary);">{{ $data[0] }}</div>
                                            <div style="font-size: 0.72rem; color: var(--text-muted); line-height: 1.3;">{{ $data[2] }}</div>
                                        </div>
                                    </div>
                                    <label class="switch-control" onclick="event.stopPropagation()">
                                        <input type="checkbox" id="perm-{{ $key }}" onchange="updatePermissionState('{{ $key }}', this.checked)">
                                        <span class="switch-slider"></span>
                                    </label>
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
                                3. Inventory &amp; Stock Warehouse
                            </div>
                            <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">4 Modules</span>
                        </div>

                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 0.85rem;">
                            @php
                                $inventoryModules = [
                                    'stock_overview' => ['Stock Overview', 'fa-cubes', 'Real-time plant inventory balances'],
                                    'item_ledger' => ['Item Ledger', 'fa-book-open', 'Inward & outward stock movement logs'],
                                    'stock_adjustment' => ['Stock Adjustment', 'fa-sliders', 'Physical stock reconciliation & corrections'],
                                    'low_stock_alert' => ['Low Stock Alert', 'fa-bell', 'Minimum threshold alerts & notifications'],
                                ];
                            @endphp

                            @foreach($inventoryModules as $key => $data)
                                <div class="perm-card" onclick="togglePermissionItem('{{ $key }}')">
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <div class="perm-icon-box" style="background: rgba(217, 119, 6, 0.1); color: #D97706;">
                                            <i class="fa-solid {{ $data[1] }}"></i>
                                        </div>
                                        <div>
                                            <div style="font-weight: 700; font-size: 0.88rem; color: var(--text-primary);">{{ $data[0] }}</div>
                                            <div style="font-size: 0.72rem; color: var(--text-muted); line-height: 1.3;">{{ $data[2] }}</div>
                                        </div>
                                    </div>
                                    <label class="switch-control" onclick="event.stopPropagation()">
                                        <input type="checkbox" id="perm-{{ $key }}" onchange="updatePermissionState('{{ $key }}', this.checked)">
                                        <span class="switch-slider"></span>
                                    </label>
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

                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 0.85rem;">
                            @php
                                $reportModules = [
                                    'purchase_report' => ['Purchase Report', 'fa-receipt', 'Detailed procurement & supplier GST registers'],
                                    'sales_report' => ['Sales Report', 'fa-chart-pie', 'Sales revenue, GST returns & summaries'],
                                    'order_report' => ['Order Report', 'fa-list-check', 'Pending orders & delivery status registers'],
                                    'cash_bank_register' => ['Cash & Bank Register', 'fa-building-columns', 'Bank passbooks, cash flows & vouchers'],
                                ];
                            @endphp

                            @foreach($reportModules as $key => $data)
                                <div class="perm-card" onclick="togglePermissionItem('{{ $key }}')">
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <div class="perm-icon-box" style="background: rgba(5, 150, 105, 0.1); color: #059669;">
                                            <i class="fa-solid {{ $data[1] }}"></i>
                                        </div>
                                        <div>
                                            <div style="font-weight: 700; font-size: 0.88rem; color: var(--text-primary);">{{ $data[0] }}</div>
                                            <div style="font-size: 0.72rem; color: var(--text-muted); line-height: 1.3;">{{ $data[2] }}</div>
                                        </div>
                                    </div>
                                    <label class="switch-control" onclick="event.stopPropagation()">
                                        <input type="checkbox" id="perm-{{ $key }}" onchange="updatePermissionState('{{ $key }}', this.checked)">
                                        <span class="switch-slider"></span>
                                    </label>
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

                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 0.85rem;">
                            @php
                                $settingModules = [
                                    'company_settings' => ['Company Information', 'fa-gear', 'System master config & plant variables'],
                                    'whatsapp_settings' => ['WhatsApp API', 'fa-brands fa-whatsapp', 'Automated bill & dispatch notifications'],
                                    'backup_restore' => ['Backup & Restore', 'fa-database', 'Database dump snapshots & archival'],
                                ];
                            @endphp

                            @foreach($settingModules as $key => $data)
                                <div class="perm-card" onclick="togglePermissionItem('{{ $key }}')">
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <div class="perm-icon-box" style="background: rgba(139, 92, 246, 0.1); color: #8B5CF6;">
                                            <i class="{{ $data[1] }}"></i>
                                        </div>
                                        <div>
                                            <div style="font-weight: 700; font-size: 0.88rem; color: var(--text-primary);">{{ $data[0] }}</div>
                                            <div style="font-size: 0.72rem; color: var(--text-muted); line-height: 1.3;">{{ $data[2] }}</div>
                                        </div>
                                    </div>
                                    <label class="switch-control" onclick="event.stopPropagation()">
                                        <input type="checkbox" id="perm-{{ $key }}" onchange="updatePermissionState('{{ $key }}', this.checked)">
                                        <span class="switch-slider"></span>
                                    </label>
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

@push('styles')
<style>
.perm-card {
    background: #FFFFFF;
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 0.9rem 1.15rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.perm-card:hover {
    border-color: var(--primary);
    background: #FAFCF7;
    box-shadow: 0 4px 12px rgba(91, 132, 30, 0.08);
    transform: translateY(-1px);
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
</style>
@endpush

@push('scripts')
<script>
// Roles Database
const ROLE_DEFINITIONS = [
    {
        key: 'Super Administrator',
        name: 'Super Administrator',
        icon: 'fa-shield-halved',
        color: '#7E22CE',
        bg: '#FAF5FF',
        badge: 'Master Level',
        isSystem: true,
        desc: 'Unrestricted master privilege. Full control over system configurations, plants & users.',
        defaultPermissions: ['company', 'user', 'access_level', 'vendor', 'customer', 'broker', 'item', 'unit', 'account', 'purchase_entry', 'wb_purchase_entry', 'sales_entry', 'wb_sales_entry', 'order_dispatch', 'sales_purchase_order', 'receipt_voucher', 'payment_voucher', 'stock_overview', 'item_ledger', 'stock_adjustment', 'low_stock_alert', 'purchase_report', 'sales_report', 'order_report', 'cash_bank_register', 'company_settings', 'whatsapp_settings', 'backup_restore']
    },
    {
        key: 'Admin',
        name: 'Admin',
        icon: 'fa-user-gear',
        color: '#15803D',
        bg: '#F0FDF4',
        badge: 'Admin Level',
        isSystem: true,
        desc: 'Operational administrator with management access across all transaction modules.',
        defaultPermissions: ['company', 'user', 'vendor', 'customer', 'broker', 'item', 'unit', 'account', 'purchase_entry', 'wb_purchase_entry', 'sales_entry', 'wb_sales_entry', 'order_dispatch', 'sales_purchase_order', 'receipt_voucher', 'payment_voucher', 'stock_overview', 'item_ledger', 'stock_adjustment', 'low_stock_alert', 'purchase_report', 'sales_report', 'order_report', 'cash_bank_register', 'company_settings', 'whatsapp_settings']
    },
    {
        key: 'Manager',
        name: 'Manager',
        icon: 'fa-briefcase',
        color: '#1D4ED8',
        bg: '#EFF6FF',
        badge: 'Operations',
        isSystem: true,
        desc: 'Plant & production oversight, operational approvals and analytical summary reports.',
        defaultPermissions: ['vendor', 'customer', 'broker', 'item', 'unit', 'purchase_entry', 'wb_purchase_entry', 'sales_entry', 'wb_sales_entry', 'order_dispatch', 'sales_purchase_order', 'receipt_voucher', 'payment_voucher', 'stock_overview', 'item_ledger', 'stock_adjustment', 'low_stock_alert', 'purchase_report', 'sales_report', 'order_report']
    },
    {
        key: 'Accountant',
        name: 'Accountant',
        icon: 'fa-calculator',
        color: '#0F766E',
        bg: '#F0FDFA',
        badge: 'Finance',
        isSystem: true,
        desc: 'Financial ledgers, payment/receipt vouchers, billing and tax GST audit reports.',
        defaultPermissions: ['vendor', 'customer', 'broker', 'item', 'unit', 'account', 'sales_entry', 'purchase_entry', 'receipt_voucher', 'payment_voucher', 'stock_overview', 'item_ledger', 'purchase_report', 'sales_report', 'cash_bank_register']
    },
    {
        key: 'Sales Manager',
        name: 'Sales Manager',
        icon: 'fa-chart-line',
        color: '#B45309',
        bg: '#FFFBEB',
        badge: 'Commercial',
        isSystem: true,
        desc: 'Customer orders, dispatch manifests, sales invoices and client ledger monitoring.',
        defaultPermissions: ['customer', 'broker', 'item', 'sales_entry', 'wb_sales_entry', 'order_dispatch', 'sales_purchase_order', 'stock_overview', 'sales_report', 'order_report']
    },
    {
        key: 'Purchase Manager',
        name: 'Purchase Manager',
        icon: 'fa-cart-flatbed',
        color: '#047857',
        bg: '#ECFDF5',
        badge: 'Procurement',
        isSystem: true,
        desc: 'Raw material procurement, weighbridge receipts and supplier purchase entries.',
        defaultPermissions: ['vendor', 'broker', 'item', 'unit', 'purchase_entry', 'wb_purchase_entry', 'sales_purchase_order', 'stock_overview', 'item_ledger', 'purchase_report']
    },
    {
        key: 'Inventory Operator',
        name: 'Inventory Operator',
        icon: 'fa-boxes-stacked',
        color: '#0E7490',
        bg: '#ECFEFF',
        badge: 'Warehouse',
        isSystem: true,
        desc: 'Warehouse stock adjustments, item tracking, transfer vouchers and low stock monitoring.',
        defaultPermissions: ['item', 'unit', 'stock_overview', 'item_ledger', 'stock_adjustment', 'low_stock_alert', 'order_dispatch']
    },
    {
        key: 'Staff',
        name: 'Staff',
        icon: 'fa-user-pen',
        color: '#475569',
        bg: '#F8FAFC',
        badge: 'Standard',
        isSystem: true,
        desc: 'Basic transactional data entry and view permissions with restricted settings.',
        defaultPermissions: ['item', 'customer', 'vendor', 'sales_entry', 'purchase_entry', 'stock_overview']
    }
];

const ALL_PERMISSION_KEYS = [
    'company', 'user', 'access_level', 'vendor', 'customer', 'broker', 'item', 'unit', 'account',
    'purchase_entry', 'wb_purchase_entry', 'sales_entry', 'wb_sales_entry', 'order_dispatch', 'sales_purchase_order', 'receipt_voucher', 'payment_voucher',
    'stock_overview', 'item_ledger', 'stock_adjustment', 'low_stock_alert',
    'purchase_report', 'sales_report', 'order_report', 'cash_bank_register',
    'company_settings', 'whatsapp_settings', 'backup_restore'
];

// User counts passed from backend
const DB_USER_COUNTS = @json($userCounts ?? []);

let currentRoleKey = 'Super Administrator';
let rolePermissionsStore = {};

function initAccessLevelManager() {
    // Load stored permissions from localStorage if present
    const saved = localStorage.getItem('vu_role_permissions_matrix');
    if (saved) {
        try {
            rolePermissionsStore = JSON.parse(saved);
        } catch(e) {
            rolePermissionsStore = {};
        }
    }

    // Initialize defaults for any missing roles
    ROLE_DEFINITIONS.forEach(r => {
        if (!rolePermissionsStore[r.key]) {
            rolePermissionsStore[r.key] = [...r.defaultPermissions];
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
        html += `
            <div class="role-nav-item ${isActive ? 'active' : ''}" onclick="selectRole('${r.key}')">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 36px; height: 36px; border-radius: 8px; background: ${r.bg}; color: ${r.color}; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0;">
                        <i class="fa-solid ${r.icon}"></i>
                    </div>
                    <div>
                        <div style="font-weight: 700; font-size: 0.88rem; color: var(--text-primary); display: flex; align-items: center; gap: 0.35rem;">
                            ${r.name}
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

    // Update active class in list
    renderRolesList();

    // Update Hero Banner
    document.getElementById('hero-role-name').innerText = role.name;
    document.getElementById('hero-role-desc').innerText = role.desc;
    document.getElementById('hero-role-badge').innerText = role.badge;
    document.getElementById('hero-role-icon-box').innerHTML = `<i class="fa-solid ${role.icon}"></i>`;

    const isSuperAdmin = role.key === 'Super Administrator';
    const notice = document.getElementById('super-admin-notice');
    if (notice) {
        notice.style.display = isSuperAdmin ? 'flex' : 'none';
    }

    // Set switches
    const activePerms = isSuperAdmin ? [...ALL_PERMISSION_KEYS] : (rolePermissionsStore[role.key] || []);
    ALL_PERMISSION_KEYS.forEach(k => {
        const checkbox = document.getElementById(`perm-${k}`);
        if (checkbox) {
            checkbox.checked = activePerms.includes(k);
            checkbox.disabled = isSuperAdmin;
        }
    });

    updateCoverageMeter();
}

function togglePermissionItem(key) {
    if (currentRoleKey === 'Super Administrator') return;
    const checkbox = document.getElementById(`perm-${key}`);
    if (checkbox && !checkbox.disabled) {
        checkbox.checked = !checkbox.checked;
        updatePermissionState(key, checkbox.checked);
    }
}

function updatePermissionState(key, isChecked) {
    if (!rolePermissionsStore[currentRoleKey]) {
        rolePermissionsStore[currentRoleKey] = [];
    }

    if (isChecked) {
        if (!rolePermissionsStore[currentRoleKey].includes(key)) {
            rolePermissionsStore[currentRoleKey].push(key);
        }
    } else {
        rolePermissionsStore[currentRoleKey] = rolePermissionsStore[currentRoleKey].filter(k => k !== key);
    }

    updateCoverageMeter();
}

function toggleAllPermissions(enableAll) {
    if (currentRoleKey === 'Super Administrator') return;

    if (enableAll) {
        rolePermissionsStore[currentRoleKey] = [...ALL_PERMISSION_KEYS];
    } else {
        rolePermissionsStore[currentRoleKey] = [];
    }

    ALL_PERMISSION_KEYS.forEach(k => {
        const checkbox = document.getElementById(`perm-${k}`);
        if (checkbox) checkbox.checked = enableAll;
    });

    updateCoverageMeter();
    toastr.info(enableAll ? 'Granted full access across all modules.' : 'Revoked all module privileges.');
}

function resetCurrentRoleDefaults() {
    const role = ROLE_DEFINITIONS.find(r => r.key === currentRoleKey);
    if (!role) return;

    rolePermissionsStore[role.key] = [...role.defaultPermissions];
    selectRole(currentRoleKey);
    toastr.success(`Restored default permissions for ${role.name}.`);
}

function updateCoverageMeter() {
    const total = ALL_PERMISSION_KEYS.length;
    let enabled = 0;

    if (currentRoleKey === 'Super Administrator') {
        enabled = total;
    } else {
        enabled = (rolePermissionsStore[currentRoleKey] || []).length;
    }

    const pct = Math.round((enabled / total) * 100);
    document.getElementById('permission-ratio-text').innerText = `${enabled} / ${total} Enabled (${pct}%)`;
    document.getElementById('permission-progress-bar').style.width = pct + '%';
}

function saveCurrentPermissions() {
    localStorage.setItem('vu_role_permissions_matrix', JSON.stringify(rolePermissionsStore));
    toastr.success(`Access permissions for "${currentRoleKey}" updated successfully!`);
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
        desc: desc,
        defaultPermissions: [...(baseRole.defaultPermissions || [])]
    };

    ROLE_DEFINITIONS.push(newRole);
    rolePermissionsStore[name] = [...newRole.defaultPermissions];
    localStorage.setItem('vu_role_permissions_matrix', JSON.stringify(rolePermissionsStore));

    closeAddRoleModal();
    renderRolesList();
    selectRole(name);
    toastr.success(`Custom role "${name}" created with ${template} template!`);
}

document.addEventListener('DOMContentLoaded', () => {
    initAccessLevelManager();
});
</script>
@endpush
@endsection
