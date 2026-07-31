<?php
// Total Products Query
$res1 = mysqli_query($conn, "SELECT COUNT(*) AS total FROM product");
$data1 = mysqli_fetch_assoc($res1);

// Low Stock Query
$res2 = mysqli_query($conn, "SELECT COUNT(*) AS low FROM batch WHERE quantity_remaining > 0 AND quantity_remaining <= 5");
$data2 = mysqli_fetch_assoc($res2);

// Today's Sales Query
$res3 = mysqli_query($conn, "SELECT SUM(total_amount) AS sales FROM sales WHERE DATE(sales_date) = CURDATE()");
$data3 = mysqli_fetch_assoc($res3);
$today_sales = $data3['sales'] ? $data3['sales'] : 0.00;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
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
        <div class="number" style="color: #2ecc71;">Rs.<?php echo number_format($today_sales, 2); ?></div>
    </div>
</div>
</body>
</html>
