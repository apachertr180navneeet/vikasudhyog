@extends('admin.layouts.app')

@section('title', 'Access Level Management - VIKAS UDHYOG ERP')
@section('page_code', 'master-access')

@section('content')
<section class="view-section active" id="view-master-access">
                    <div class="page-header">
                        <div>
                            <h1 class="page-title">Access Level & Role Management</h1>
                            <p class="page-subtitle">Configure module permissions for employee roles</p>
                        </div>
                        <button class="btn btn-primary" onclick="Masters.savePermissions()"><i class="fa-solid fa-floppy-disk"></i> Save Permissions</button>
                    </div>

                    <div style="display: grid; grid-template-columns: 260px 1fr; gap: 1.5rem;">
                        <div class="card">
                            <div class="card-header"><h3 class="card-title">Roles</h3></div>
                            <div id="role-list-container"></div>
                        </div>

                        <div class="card">
                            <div class="card-header"><h3 class="card-title" id="selected-role-title">Permissions</h3></div>
                            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.25rem;">
                                <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                                    <span>Dashboard View</span>
                                    <label class="switch"><input type="checkbox" id="perm-dashboard" checked><span class="slider"></span></label>
                                </div>
                                <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                                    <span>Company Master</span>
                                    <label class="switch"><input type="checkbox" id="perm-company" checked><span class="slider"></span></label>
                                </div>
                                <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                                    <span>User Master</span>
                                    <label class="switch"><input type="checkbox" id="perm-users" checked><span class="slider"></span></label>
                                </div>
                                <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                                    <span>Vendor Master</span>
                                    <label class="switch"><input type="checkbox" id="perm-vendors" checked><span class="slider"></span></label>
                                </div>
                                <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                                    <span>Customer Master</span>
                                    <label class="switch"><input type="checkbox" id="perm-customers" checked><span class="slider"></span></label>
                                </div>
                                <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                                    <span>Broker Master</span>
                                    <label class="switch"><input type="checkbox" id="perm-brokers" checked><span class="slider"></span></label>
                                </div>
                                <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                                    <span>Item / Product Master</span>
                                    <label class="switch"><input type="checkbox" id="perm-items" checked><span class="slider"></span></label>
                                </div>
                                <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                                    <span>Purchase Transactions</span>
                                    <label class="switch"><input type="checkbox" id="perm-purchase" checked><span class="slider"></span></label>
                                </div>
                                <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                                    <span>Sales Transactions</span>
                                    <label class="switch"><input type="checkbox" id="perm-sales" checked><span class="slider"></span></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
@endsection


