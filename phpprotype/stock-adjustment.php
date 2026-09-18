<?php
$pageTitle = 'Stock Adjustment - VIKAS UDHYOG ERP';
$pageCode = 'inv-adjustment';
include 'includes/header.php';
?>

<section class="view-section active" id="view-inv-adjustment">
                    <div class="page-header">
                        <div>
                            <h1 class="page-title">Stock Adjustment & Physical Audit</h1>
                            <p class="page-subtitle">Reconcile physical stock counts with digital inventory balance</p>
                        </div>
                        <button class="btn btn-primary" onclick="Inventory.renderAdjustment(); App.openModal('modal-stock-adj');">+ Adjust Stock</button>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                        <div class="card">
                            <h3 class="card-title" style="margin-bottom: 1rem;">Quick Stock Adjustment</h3>
                            <form onsubmit="Inventory.saveAdjustment(event)">
                                <div class="form-group" style="margin-bottom: 1rem;">
                                    <label class="form-label">Select Herbal Product *</label>
                                    <select id="adj-item-select-page" class="form-control" required></select>
                                </div>
                                <div class="form-group" style="margin-bottom: 1rem;">
                                    <label class="form-label">Adjustment Type *</label>
                                    <select id="adj-type-page" class="form-control">
                                        <option value="Add">Add Stock (+ Stock In / Surplus)</option>
                                        <option value="Reduce">Reduce Stock (- Damage / Wastage / Audit Shortage)</option>
                                    </select>
                                </div>
                                <div class="form-group" style="margin-bottom: 1rem;">
                                    <label class="form-label">Quantity *</label>
                                    <input type="number" step="0.01" id="adj-qty-page" class="form-control" value="10" required>
                                </div>
                                <div class="form-group" style="margin-bottom: 1.5rem;">
                                    <label class="form-label">Reason / Audit Remark</label>
                                    <input type="text" id="adj-reason-page" class="form-control" placeholder="e.g. Monthly Physical Verification">
                                </div>
                                <button type="submit" class="btn btn-primary" style="width: 100%;"><i class="fa-solid fa-arrows-rotate"></i> Update Stock Balance</button>
                            </form>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Recent Stock Adjustments & Audit Log</h3>
                            </div>
                            <div class="table-responsive">
                                <table class="custom-table">
                                    <thead>
                                        <tr>
                                            <th>Date & Time</th>
                                            <th>Product Name</th>
                                            <th>Adjustment Type</th>
                                            <th>Quantity</th>
                                            <th>Audited By</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="adjustment-log-body">
                                        <tr>
                                            <td>2026-09-18 10:30 AM</td>
                                            <td><strong>Mehndi Special Blend</strong></td>
                                            <td><span class="badge badge-success">+ Add 50 KG</span></td>
                                            <td>50 KG</td>
                                            <td>Admin</td>
                                            <td><span class="badge badge-success">Completed</span></td>
                                        </tr>
                                        <tr>
                                            <td>2026-09-15 04:15 PM</td>
                                            <td><strong>Neem Powder</strong></td>
                                            <td><span class="badge badge-danger">- Reduce 5 KG</span></td>
                                            <td>5 KG (Spillage)</td>
                                            <td>Store Manager</td>
                                            <td><span class="badge badge-success">Completed</span></td>
                                        </tr>
                                        <tr>
                                            <td>2026-09-10 11:00 AM</td>
                                            <td><strong>Amla Herbal Powder</strong></td>
                                            <td><span class="badge badge-success">+ Add 20 KG</span></td>
                                            <td>20 KG</td>
                                            <td>Admin</td>
                                            <td><span class="badge badge-success">Completed</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>

<?php include 'includes/footer.php'; ?>
