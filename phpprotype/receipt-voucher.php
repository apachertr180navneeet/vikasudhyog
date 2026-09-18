<?php
$pageTitle = 'Receipt Voucher - VIKAS UDHYOG ERP';
$pageCode = 'txn-receipt';
include 'includes/header.php';
?>

<section class="view-section active" id="view-txn-receipt">
                    <div class="page-header">
                        <div><h1 class="page-title">Receipt Voucher</h1><p class="page-subtitle">Customer payment collections</p></div>
                        <button class="btn btn-primary" onclick="Transactions.initReceiptModal(); App.openModal('modal-receipt');">+ Add Receipt</button>
                    </div>
                    <div class="card">
                        <div class="table-responsive">
                            <table class="custom-table">
                                <thead><tr><th>Receipt No</th><th>Date</th><th>Customer</th><th>Amount</th><th>Mode</th><th>Against Inv</th><th>Action</th></tr></thead>
                                <tbody id="receipt-table-body"></tbody>
                            </table>
                        </div>
                    </div>
                </section>

<?php include 'includes/footer.php'; ?>
