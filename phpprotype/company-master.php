<?php
$pageTitle = 'Company Master - VIKAS UDHYOG ERP';
$pageCode = 'master-company';
include 'includes/header.php';
?>

<section class="view-section active" id="view-master-company">
                    <div class="page-header">
                        <div>
                            <h1 class="page-title">Company Master</h1>
                            <p class="page-subtitle">Manage multi-company business profiles</p>
                        </div>
                        <button class="btn btn-primary" onclick="document.getElementById('company-form').reset(); document.getElementById('comp-id').value=''; App.openModal('modal-company');">+ Add Company</button>
                    </div>

                    <div class="card">
                        <div class="table-responsive">
                            <table class="custom-table">
                                <thead>
                                    <tr>
                                        <th>Company Name</th>
                                        <th>GSTIN</th>
                                        <th>PAN</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>City</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="company-table-body"></tbody>
                            </table>
                        </div>
                    </div>
                </section>

<?php include 'includes/footer.php'; ?>
