<?php
require_once 'db_connect.php';

$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'staff';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_batch'])) {
    if ($user_role === 'admin') {
        $batch_id = (int)$_POST['batch_id'];
        mysqli_query($conn, "DELETE FROM batch WHERE batch_id = '$batch_id'");
        echo "<div class='alert' style='background-color:#d4edda; color:#155724; padding:10px; margin-bottom:15px;'>Batch deleted successfully!</div>";
    }
}
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
                    <th>Batch No</th>
                    <th>Product</th>
                    <th>Qty Remaining</th>
                    <th>Expiry Date</th>
                    <?php if ($user_role === 'admin'): ?>
                        <th>Action</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php
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
                            <td>{$b['expiry_date']}</td>";
                    
                    if ($user_role === 'admin') {
                        echo "<td>
                                <form method='POST' onsubmit='return confirm(\"Are you sure you want to delete this batch?\");' style='margin:0;'>
                                    <input type='hidden' name='batch_id' value='{$b['batch_id']}'>
                                    <button type='submit' name='delete_batch' class='btn' style='background-color:#e74c3c; color:white; border:none; padding:5px 10px; cursor:pointer;'>Delete</button>
                                </form>
                              </td>";
                    }

                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>