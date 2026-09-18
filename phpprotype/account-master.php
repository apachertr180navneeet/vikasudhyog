<?php
$pageTitle = 'Account Master - VIKAS UDHYOG ERP';
$pageCode = 'master-account';
include 'includes/header.php';
?>

<section class="view-section active" id="view-master-account">
                    <div class="page-header">
                        <div><h1 class="page-title">Account Master</h1><p class="page-subtitle">Chart of Accounts (Bank, Cash, Income, Expense)</p></div>
                        <button class="btn btn-primary" onclick="document.getElementById('account-form').reset(); App.openModal('modal-account');">+ Add Account</button>
                    </div>
                    <div class="card">
                        <div class="table-responsive">
                            <table class="custom-table">
                                <thead><tr><th>Account Name</th><th>Type</th><th>Opening Balance</th><th>Current Balance</th><th>Status</th><th>Actions</th></tr></thead>
                                <tbody id="account-table-body"></tbody>
                            </table>
                        </div>
                    </div>
                </section>

<?php include 'includes/footer.php'; ?>
