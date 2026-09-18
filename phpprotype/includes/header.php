<?php
if (!isset($pageTitle)) {
    $pageTitle = "VIKAS UDHYOG ERP - Herbal Manufacturing & Trade Management";
}
if (!isset($pageCode)) {
    $pageCode = "dashboard";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <!-- Google Fonts & Font Awesome Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Styles -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/responsive.css">

    <!-- Chart.js for Executive Dashboard -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body data-page="<?= htmlspecialchars($pageCode) ?>">

    <div class="app-container">
        <?php include __DIR__ . '/sidebar.php'; ?>

        <div class="sidebar-overlay" id="sidebar-overlay"></div>

        <!-- Main Wrapper -->
        <main class="main-wrapper">
            <?php include __DIR__ . '/topbar.php'; ?>

            <!-- Page Content Body -->
            <div class="content-body">
