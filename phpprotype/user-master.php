<?php
$pageTitle = 'User Master - VIKAS UDHYOG ERP';
$pageCode = 'master-user';
include 'includes/header.php';
?>

<section class="view-section active" id="view-master-user">
                    <div class="page-header">
                        <div>
                            <h1 class="page-title">User Master</h1>
                            <p class="page-subtitle">Manage system users and login credentials</p>
                        </div>
                        <button class="btn btn-primary" onclick="document.getElementById('user-form').reset(); document.getElementById('usr-id').value=''; Masters.populateUserCompanyDropdown(); App.openModal('modal-user');">+ Add User</button>
                    </div>

                    <div class="card">
                        <div class="table-responsive">
                            <table class="custom-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Username</th>
                                        <th>Password</th>
                                        <th>Email</th>
                                        <th>Mobile</th>
                                        <th>Role</th>
                                        <th>Company</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="user-table-body"></tbody>
                            </table>
                        </div>
                    </div>
                </section>

<?php include 'includes/footer.php'; ?>
