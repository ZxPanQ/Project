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
           
        </table>
    </div>
</body>
</html>