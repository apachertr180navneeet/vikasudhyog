<?php
$pageTitle = 'Item Master - VIKAS UDHYOG ERP';
$pageCode = 'master-item';
include 'includes/header.php';
?>

<section class="view-section active" id="view-master-item">
                    <div class="page-header">
                        <div>
                            <h1 class="page-title">Item Master (Products & Raw Materials)</h1>
                            <p class="page-subtitle">Mehndi, Henna Powder, Herbal Powders & Raw Ingredients</p>
                        </div>
                        <button class="btn btn-primary" onclick="document.getElementById('item-form').reset(); document.getElementById('itm-id').value=''; App.openModal('modal-item');">+ Add Product Item</button>
                    </div>

                    <div class="card">
                        <div class="table-responsive">
                            <table class="custom-table">
                                <thead>
                                    <tr>
                                        <th>Item Code</th>
                                        <th>Product Name</th>
                                        <th>Category</th>
                                        <th>Unit</th>
                                        <th>Pur. Rate</th>
                                        <th>Sale Price</th>
                                        <th>GST %</th>
                                        <th>Stock</th>
                                        <th>Min. Stock</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="item-table-body"></tbody>
                            </table>
                        </div>
                    </div>
                </section>

<?php include 'includes/footer.php'; ?>
