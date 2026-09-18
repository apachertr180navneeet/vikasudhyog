<?php
$pageTitle = 'Order Dispatch - VIKAS UDHYOG ERP';
$pageCode = 'txn-order-dispatch';
include 'includes/header.php';
?>

<section class="view-section active" id="view-txn-order-dispatch">
                    <div class="page-header">
                        <div><h1 class="page-title">Order Dispatch Management</h1><p class="page-subtitle">Track order shipments, vehicles, and driver dispatches</p></div>
                    </div>
                    <div class="card">
                        <div class="table-responsive">
                            <table class="custom-table">
                                <thead><tr><th>Order No</th><th>Customer</th><th>Dispatch Date</th><th>Vehicle No</th><th>Driver</th><th>Transporter</th><th>Status</th></tr></thead>
                                <tbody id="dispatch-table-body"></tbody>
                            </table>
                        </div>
                    </div>
                </section>

<?php include 'includes/footer.php'; ?>
