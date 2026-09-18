<?php
$pageTitle = 'Payment Voucher - VIKAS UDHYOG ERP';
$pageCode = 'txn-payment';
include 'includes/header.php';
?>

<section class="view-section active" id="view-txn-payment">
                    <div class="page-header">
                        <div><h1 class="page-title">Payment Voucher</h1><p class="page-subtitle">Vendor bill payments</p></div>
                        <button class="btn btn-primary" onclick="Transactions.initPaymentModal(); App.openModal('modal-payment');">+ Add Payment</button>
                    </div>
                    <div class="card">
                        <div class="table-responsive">
                            <table class="custom-table">
                                <thead><tr><th>Payment No</th><th>Date</th><th>Vendor</th><th>Amount</th><th>Mode</th><th>Against Pur</th><th>Action</th></tr></thead>
                                <tbody id="payment-table-body"></tbody>
                            </table>
                        </div>
                    </div>
                </section>

<?php include 'includes/footer.php'; ?>
