<?php
$pageTitle = 'Unit Master - VIKAS UDHYOG ERP';
$pageCode = 'master-unit';
include 'includes/header.php';
?>

<section class="view-section active" id="view-master-unit">
                    <div class="page-header">
                        <div><h1 class="page-title">Unit Master</h1><p class="page-subtitle">Measurement units (KG, BOX, BAG)</p></div>
                        <button class="btn btn-primary" onclick="document.getElementById('unit-form').reset(); App.openModal('modal-unit');">+ Add Unit</button>
                    </div>
                    <div class="card">
                        <div class="table-responsive">
                            <table class="custom-table">
                                <thead><tr><th>Unit Name</th><th>Description</th><th>Actions</th></tr></thead>
                                <tbody id="unit-table-body"></tbody>
                            </table>
                        </div>
                    </div>
                </section>

<?php include 'includes/footer.php'; ?>
