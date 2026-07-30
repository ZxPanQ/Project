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
                <div class="content-box">
                    <h3>Add New Category</h3>
                    <form method="POST" style="display:flex; gap:10px; margin-top:10px;">
                        <input type="text" name="category_name" placeholder="Category Name" required style="padding:8px;">
                        <button type="submit" name="add_category" class="btn">Save Category</button>
                    </form>
                </div>

                <div class="content-box">
                    <h3>Add New Product</h3>
                    <form method="POST" style="display:flex; gap:10px; margin-top:10px; flex-wrap:wrap;">
                        <input type="text" name="product_name" placeholder="Product Name" required style="padding:8px;">
                        <select name="category_id" required style="padding:8px;">
                            <option value="">Select Category</option>
                        </select>
                        <input type="number" step="0.01" name="unit_price" placeholder="Selling Price (Rs.)" required style="padding:8px;">
                        <button type="submit" name="add_product" class="btn">Save Product</button>
                    </form>
                </div>
                <div class="content-box">
                    <h3>Product List</h3>
                    <table>
                        <tr>
                            <th>ID</th>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Price</th>
                        </tr>
                    </table>
                </div>
                <div class="content-box">
                    <h3>Active Inventory Batches</h3>
                    <table>
                        <tr>
                            <th>Batch #</th>
                            <th>Product</th>
                            <th>Qty Remaining</th>
                            <th>Expiry Date</th>
                        </tr>
                    </table>
</body>
</html>