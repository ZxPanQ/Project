<?php
require_once 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Active Inventory Batches</title>
</head>
<body>
    <div class="content-box">
        <h3>Active Inventory Batches</h3>
        <table>
            <thead>
                <tr>
                    <th>Batch #</th>
                    <th>Product</th>
                    <th>Qty Remaining</th>
                    <th>Expiry Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Changed ORDER BY from b.expiry_date ASC to b.batch_id DESC
                $sql = "SELECT b.*, p.product_name 
                        FROM batch b 
                        JOIN product p ON b.product_id = p.product_id 
                        ORDER BY b.batch_id DESC";
                        
                $batches = mysqli_query($conn, $sql);
                while ($b = mysqli_fetch_assoc($batches)) {
                    echo "<tr>
                            <td>" . htmlspecialchars($b['batch_number']) . "</td>
                            <td>" . htmlspecialchars($b['product_name']) . "</td>
                            <td>{$b['quantity_remaining']}</td>
                            <td>{$b['expiry_date']}</td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>