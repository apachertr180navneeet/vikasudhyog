<?php
$pageTitle = 'Stock Overview - VIKAS UDHYOG ERP';
$pageCode = 'inv-overview';
include 'includes/header.php';
?>

<section class="view-section active" id="view-inv-overview">
                    <div class="page-header">
                        <div><h1 class="page-title">Stock Overview</h1><p class="page-subtitle">Current stock levels & valuation</p></div>
                        <button class="btn btn-primary" onclick="Inventory.renderAdjustment(); App.openModal('modal-stock-adj');">Adjust Stock</button>
                    </div>

                    <div class="kpi-grid">
                        <div class="kpi-card"><span class="kpi-title">Total Product Items</span><div class="kpi-value" id="inv-card-items">0</div></div>
                        <div class="kpi-card"><span class="kpi-title">Low Stock Alert Items</span><div class="kpi-value" id="inv-card-low" style="color: var(--status-danger);">0</div></div>
                    </div>

                    <div class="card">
                        <div class="table-responsive">
                            <table class="custom-table">
                                <thead><tr><th>Product Name</th><th>Category</th><th>Batch</th><th>Unit</th><th>Current Stock</th><th>Min Stock</th><th>Pur. Rate</th><th>Stock Value</th><th>Status</th></tr></thead>
                                <tbody id="inv-overview-table-body"></tbody>
                            </table>
                        </div>
                    </div>
                </section>

<?php include 'includes/footer.php'; ?>
