<?php
$pageTitle = 'Order Report - VIKAS UDHYOG ERP';
$pageCode = 'rpt-order';
include 'includes/header.php';
?>

<section class="view-section active" id="view-rpt-order">
                    <div class="page-header"><div><h1 class="page-title">Order Report</h1><p class="page-subtitle">Sales order log</p></div></div>
                    <div class="card">
                        <div class="table-responsive">
                            <table class="custom-table">
                                <thead><tr><th>Order No</th><th>Date</th><th>Customer</th><th>Delivery Date</th><th>Total</th><th>Status</th></tr></thead>
                                <tbody id="rpt-order-body"></tbody>
                            </table>
                        </div>
                    </div>
                </section>

<?php include 'includes/footer.php'; ?>
