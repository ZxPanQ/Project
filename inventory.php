<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="content-box">
    <h3>Active Inventory Batches</h3>
    <table>
        <tr>
            <th>Batch No.</th>
            <th>Product</th>
            <th>Qty Remaining</th>
            <th>Expiry Date</th>
        </tr>
        <?php
        $sql = "SELECT b.*, p.product_name FROM batch b JOIN product p ON b.product_id = p.product_id ORDER BY b.expiry_date ASC";
        $batches = mysqli_query($conn, $sql);
        while ($b = mysqli_fetch_assoc($batches)) {
            echo "<tr>
                    <td>{$b['batch_number']}</td>
                    <td>{$b['product_name']}</td>
                    <td>{$b['quantity_remaining']}</td>
                    <td>{$b['expiry_date']}</td>
                  </tr>";
        }
        ?>
    </table>
</div>
</body>
</html>
