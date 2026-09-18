<?php
$pageTitle = 'Customer Master - VIKAS UDHYOG ERP';
$pageCode = 'master-customer';
include 'includes/header.php';
?>

<section class="view-section active" id="view-master-customer">
                    <div class="page-header">
                        <div>
                            <h1 class="page-title">Customer Master</h1>
                            <p class="page-subtitle">Client accounts, distributors & retailers</p>
                        </div>
                        <button class="btn btn-primary" onclick="document.getElementById('customer-form').reset(); document.getElementById('cst-id').value=''; App.openModal('modal-customer');">+ Add Customer</button>
                    </div>

                    <div class="card">
                        <div class="table-responsive">
                            <table class="custom-table">
                                <thead>
                                    <tr>
                                        <th>Customer Name</th>
                                        <th>Contact Person</th>
                                        <th>Phone</th>
                                        <th>GSTIN</th>
                                        <th>City</th>
                                        <th>Credit Limit</th>
                                        <th>Outstanding</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="customer-table-body"></tbody>
                            </table>
                        </div>
                    </div>
                </section>

<?php include 'includes/footer.php'; ?>
