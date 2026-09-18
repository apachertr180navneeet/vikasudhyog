<?php
$pageTitle = 'Sales / Purchase Order - VIKAS UDHYOG ERP';
$pageCode = 'txn-sales-invoice';
include 'includes/header.php';
?>

<section class="view-section active" id="view-txn-sales-invoice">
                    <div class="page-header">
                        <div><h1 class="page-title">Sales / Purchase Order</h1><p class="page-subtitle">Manage customer sales orders and vendor purchase orders</p></div>
                        <button class="btn btn-primary" onclick="App.openModal('modal-sales-invoice');">+ New Sales / Purchase Order</button>
                    </div>
                    <div class="card">
                        <div class="table-responsive">
                            <table class="custom-table">
                                <thead><tr><th>Invoice No</th><th>Date</th><th>Customer Name</th><th>Taxable Amount</th><th>GST</th><th>Grand Total</th><th>Payment</th><th>Action</th></tr></thead>
                                <tbody id="sales-invoice-table-body"></tbody>
                            </table>
                        </div>
                    </div>
                </section>

<?php include 'includes/footer.php'; ?>
