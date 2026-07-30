<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Shop System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="dashboard-layout">
        <div class="sidebar">
            <h2>Local Shop System</h2>
            <nav>
                <a href="dashboard.php?page=home">Dashboard Home</a>
                <a href="dashboard.php?page=products">Products & Categories</a>
                <a href="dashboard.php?page=inventory">Inventory & Batches</a>
                <a href="dashboard.php?page=pos">Sales / POS</a>
                <a href="dashboard.php?page=suppliers">Suppliers</a>
                <a href="dashboard.php?page=purchases">Purchases & Suppliers</a>
                <a href="logout.php" style="color: #e74c3c;">Logout</a>
            </nav>
        </div>
        <div class="main-content">
            <header style="margin-bottom: 20px;">
                <h1>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></h1>
            </header>
            <div class="card-grid">
                    <div class="card">
                        <h3>TOTAL PRODUCTS</h3>
                        <div class="number"><?php echo $data1['total']; ?></div>
                    </div>
                    <div class="card">
                        <h3>LOW STOCK BATCHES</h3>
                        <div class="number" style="color: #e74c3c;"><?php echo $data2['low']; ?></div>
                    </div>
                    <div class="card">
                        <h3>TODAY'S SALES</h3>
                        <div class="number" style="color: #2ecc71;">$<?php echo number_format($today_sales, 2); ?></div>
                    </div>
                </div>