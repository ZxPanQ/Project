<?php
// Handle Add Supplier
if (isset($_POST['add_supplier'])) {
    $sup_name    = mysqli_real_escape_string($conn, $_POST['supplier_name']);
    $sup_address = mysqli_real_escape_string($conn, $_POST['supplier_address']);
    $sup_contact = mysqli_real_escape_string($conn, $_POST['supplier_contact']);
    
    // Adjusted column names (phone, address) to match database schema
    mysqli_query($conn, "INSERT INTO supplier (supplier_name, address, phone) VALUES ('$sup_name', '$sup_address', '$sup_contact')");
    echo "<div class='alert' style='background-color:#d4edda; color:#155724;'>Supplier added successfully!</div>";
}

// Handle Delete Supplier
if (isset($_POST['delete_supplier'])) {
    $supplier_id = (int)$_POST['supplier_id'];
    
    $delete_query = mysqli_query($conn, "DELETE FROM supplier WHERE supplier_id = '$supplier_id'");
    if ($delete_query) {
        echo "<div class='alert' style='background-color:#d4edda; color:#155724;'>Supplier deleted successfully!</div>";
    } else {
        echo "<div class='alert' style='background-color:#f8d7da; color:#721c24;'>Cannot delete supplier linked to existing purchase records.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Management</title>
</head>
<body>
    <div class="content-box">
        <h3>Add New Supplier</h3>
        <form method="POST" style="display:grid; grid-template-columns: 1fr 1fr; gap:15px; margin-top:10px;">
            <div>
                <label>Supplier Name</label>
                <input type="text" name="supplier_name" placeholder="Supplier Name" required style="width:100%; padding:8px;">
            </div>
            <div>
                <label>Contact (Phone)</label>
                <input type="text" name="supplier_contact" placeholder="Phone Number" required style="width:100%; padding:8px;">
            </div>
            <div style="grid-column: span 2;">
                <label>Address</label>
                <input type="text" name="supplier_address" placeholder="Supplier Address" required style="width:100%; padding:8px;">
            </div>
            <div style="grid-column: span 2;">
                <button type="submit" name="add_supplier" class="btn">Save Supplier</button>
            </div>
        </form>
    </div>

    <div class="content-box">
        <h3>Supplier List</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Supplier Name</th>
                <th>Contact</th>
                <th>Address</th>
                <th>Action</th>
            </tr>
            <?php
            $sups = mysqli_query($conn, "SELECT * FROM supplier");
            while ($s = mysqli_fetch_assoc($sups)) {
                echo "<tr>
                        <td>{$s['supplier_id']}</td>
                        <td>{$s['supplier_name']}</td>
                        <td>{$s['phone']}</td>
                        <td>{$s['address']}</td>
                        <td>
                            <form method='POST' onsubmit='return confirm(\"Are you sure you want to delete this supplier?\");' style='margin:0;'>
                                <input type='hidden' name='supplier_id' value='{$s['supplier_id']}'>
                                <button type='submit' name='delete_supplier' class='btn' style='background-color:#e74c3c; color:white; border:none; padding:5px 10px; cursor:pointer;'>Delete</button>
                            </form>
                        </td>
                      </tr>";
            }
            ?>
        </table>
    </div>
</body>
</html>