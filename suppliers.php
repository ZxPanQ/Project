<?php
// Handle Add Supplier
if (isset($_POST['add_supplier'])) {
    $sup_name    = mysqli_real_escape_string($conn, $_POST['supplier_name']);
    $sup_address = mysqli_real_escape_string($conn, $_POST['supplier_address']);
    $sup_contact = mysqli_real_escape_string($conn, $_POST['supplier_contact']);
    
    mysqli_query($conn, "INSERT INTO supplier (supplier_name, address, phone) VALUES ('$sup_name', '$sup_address', '$sup_contact')");
    echo "<div class='alert' style='background-color:#d4edda; color:#155724; padding:10px; margin-bottom:15px;'>Supplier added successfully!</div>";
}

// Handle Update Supplier
if (isset($_POST['update_supplier'])) {
    $sup_id      = (int)$_POST['supplier_id'];
    $sup_name    = mysqli_real_escape_string($conn, $_POST['supplier_name']);
    $sup_address = mysqli_real_escape_string($conn, $_POST['supplier_address']);
    $sup_contact = mysqli_real_escape_string($conn, $_POST['supplier_contact']);
    
    mysqli_query($conn, "UPDATE supplier SET supplier_name='$sup_name', address='$sup_address', phone='$sup_contact' WHERE supplier_id='$sup_id'");
    echo "<div class='alert' style='background-color:#d4edda; color:#155724; padding:10px; margin-bottom:15px;'>Supplier updated successfully!</div>";
}

// Handle Delete Supplier
if (isset($_POST['delete_supplier'])) {
    $supplier_id = (int)$_POST['supplier_id'];
    
    $delete_query = mysqli_query($conn, "DELETE FROM supplier WHERE supplier_id = '$supplier_id'");
    if ($delete_query) {
        echo "<div class='alert' style='background-color:#d4edda; color:#155724; padding:10px; margin-bottom:15px;'>Supplier deleted successfully!</div>";
    } else {
        echo "<div class='alert' style='background-color:#f8d7da; color:#721c24; padding:10px; margin-bottom:15px;'>Cannot delete supplier linked to existing purchase records.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Management</title>
    <style>
        /* Modal Popup Styles */
        .modal {
            display: none; 
            position: fixed; 
            z-index: 1000; 
            left: 0; 
            top: 0; 
            width: 100%; 
            height: 100%; 
            background-color: rgba(0,0,0,0.5); 
        }
        .modal-content {
            background-color: #fff; 
            margin: 10% auto; 
            padding: 20px; 
            border-radius: 5px; 
            width: 400px;
            max-width: 90%;
        }
        .close-btn {
            float: right; 
            font-size: 1.5rem; 
            font-weight: bold; 
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="content-box">
        <h3>Add New Supplier</h3>
        <form method="POST" style="display:grid; grid-template-columns: 1fr 1fr; gap:15px; margin-top:10px;">
            <div>
                <label style="display:block; font-size:0.85rem; margin-bottom:4px;">Supplier Name</label>
                <input type="text" name="supplier_name" placeholder="Supplier Name" required style="width:100%; padding:8px; box-sizing:border-box;">
            </div>
            <div>
                <label style="display:block; font-size:0.85rem; margin-bottom:4px;">Contact (Phone)</label>
                <input type="text" name="supplier_contact" placeholder="Phone Number" required style="width:100%; padding:8px; box-sizing:border-box;">
            </div>
            <div style="grid-column: span 2;">
                <label style="display:block; font-size:0.85rem; margin-bottom:4px;">Address</label>
                <input type="text" name="supplier_address" placeholder="Supplier Address" required style="width:100%; padding:8px; box-sizing:border-box;">
            </div>
            <div style="grid-column: span 2;">
                <button type="submit" name="add_supplier" class="btn">Save Supplier</button>
            </div>
        </form>
    </div>

    <div class="content-box">
        <h3>Supplier List</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Supplier Name</th>
                    <th>Contact</th>
                    <th>Address</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sups = mysqli_query($conn, "SELECT * FROM supplier ORDER BY supplier_id ASC");
                while ($s = mysqli_fetch_assoc($sups)) {
                    $sup_name = htmlspecialchars($s['supplier_name'], ENT_QUOTES);
                    $sup_phone = htmlspecialchars($s['phone'], ENT_QUOTES);
                    $sup_address = htmlspecialchars($s['address'], ENT_QUOTES);

                    echo "<tr>
                            <td>{$s['supplier_id']}</td>
                            <td>{$sup_name}</td>
                            <td>{$sup_phone}</td>
                            <td>{$sup_address}</td>
                            <td style='display:flex; gap:5px;'>
                                <button type='button' class='btn' style='background-color:#3498db; color:white; border:none; padding:5px 10px; cursor:pointer;' 
                                    onclick='openEditModal({$s['supplier_id']}, \"{$sup_name}\", \"{$sup_phone}\", \"{$sup_address}\")'>
                                    Edit
                                </button>
                                <form method='POST' onsubmit='return confirm(\"Are you sure you want to delete this supplier?\");' style='margin:0;'>
                                    <input type='hidden' name='supplier_id' value='{$s['supplier_id']}'>
                                    <button type='submit' name='delete_supplier' class='btn' style='background-color:#e74c3c; color:white; border:none; padding:5px 10px; cursor:pointer;'>Delete</button>
                                </form>
                            </td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Edit Supplier Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeEditModal()">&times;</span>
            <h3 style="margin-bottom:15px;">Edit Supplier</h3>
            <form method="POST" style="display:flex; flex-direction:column; gap:12px;">
                <input type="hidden" name="supplier_id" id="edit_supplier_id">
                
                <div>
                    <label style="display:block; font-size:0.85rem; margin-bottom:4px;">Supplier Name</label>
                    <input type="text" name="supplier_name" id="edit_supplier_name" required style="width:100%; padding:8px; box-sizing:border-box;">
                </div>

                <div>
                    <label style="display:block; font-size:0.85rem; margin-bottom:4px;">Contact (Phone)</label>
                    <input type="text" name="supplier_contact" id="edit_supplier_contact" required style="width:100%; padding:8px; box-sizing:border-box;">
                </div>

                <div>
                    <label style="display:block; font-size:0.85rem; margin-bottom:4px;">Address</label>
                    <input type="text" name="supplier_address" id="edit_supplier_address" required style="width:100%; padding:8px; box-sizing:border-box;">
                </div>

                <button type="submit" name="update_supplier" class="btn" style="background-color:#2ecc71; color:white; padding:10px; border:none; cursor:pointer; font-weight:bold; margin-top:5px;">
                    Update Supplier
                </button>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(id, name, contact, address) {
            document.getElementById('edit_supplier_id').value = id;
            document.getElementById('edit_supplier_name').value = name;
            document.getElementById('edit_supplier_contact').value = contact;
            document.getElementById('edit_supplier_address').value = address;
            
            document.getElementById('editModal').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        window.onclick = function(event) {
            var modal = document.getElementById('editModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>