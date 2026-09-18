<?php
$pageTitle = 'Broker Master - VIKAS UDHYOG ERP';
$pageCode = 'master-broker';
include 'includes/header.php';
?>

<section class="view-section active" id="view-master-broker">
                    <div class="page-header">
                        <div>
                            <h1 class="page-title">Broker Master</h1>
                            <p class="page-subtitle">Broker & mandi commission agent profiles</p>
                        </div>
                        <button class="btn btn-primary" onclick="document.getElementById('broker-form').reset(); document.getElementById('brk-id').value=''; document.getElementById('brk-code').value=''; App.openModal('modal-broker');">+ Add Broker</button>
                    </div>

                    <div class="card">
                        <div class="table-responsive">
                            <table class="custom-table">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Broker Name</th>
                                        <th>Contact Person</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>City</th>
                                        <th>Commission (%)</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="broker-table-body"></tbody>
                            </table>
                        </div>
                    </div>
                </section>

<?php include 'includes/footer.php'; ?>
