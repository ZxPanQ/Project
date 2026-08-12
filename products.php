<?php
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'staff';

if (isset($_POST['add_category']) && $user_role === 'admin') {
    $cat = mysqli_real_escape_string($conn, $_POST['category_name']);
    mysqli_query($conn, "INSERT INTO category (category_name) VALUES ('$cat')");
}

if (isset($_POST['add_product']) && $user_role === 'admin') {
    $name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $cat_id = (int)$_POST['category_id'];
    $price = (float)$_POST['unit_price'];
    mysqli_query($conn, "INSERT INTO product (category_id, product_name, unit_price) VALUES ('$cat_id', '$name', '$price')");
}

if (isset($_POST['update_product']) && $user_role === 'admin') {
    $product_id = (int)$_POST['product_id'];
    $name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $cat_id = (int)$_POST['category_id'];
    $price = (float)$_POST['unit_price'];
    
    mysqli_query($conn, "UPDATE product SET product_name = '$name', category_id = '$cat_id', unit_price = '$price' WHERE product_id = '$product_id'");
}

if (isset($_POST['delete_product']) && $user_role === 'admin') {
    $delete_id = (int)$_POST['product_id'];
    mysqli_query($conn, "DELETE FROM product WHERE product_id = '$delete_id'");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products & Categories</title>
    <style>
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
    <?php if ($user_role === 'admin'): ?>
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
                <?php
                $cats = mysqli_query($conn, "SELECT * FROM category");
                while ($c = mysqli_fetch_assoc($cats)) {
                    echo "<option value='{$c['category_id']}'>{$c['category_name']}</option>";
                }
                ?>
            </select>
            <input type="number" step="0.01" name="unit_price" placeholder="Selling Price (Rs.)" required style="padding:8px;">
            <button type="submit" name="add_product" class="btn">Save Product</button>
        </form>
    </div>
    <?php endif; ?>

    <div class="content-box">
        <h3>Product List</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Price</th>
                    <?php if ($user_role === 'admin'): ?>
                        <th>Action</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php
                $prods = mysqli_query($conn, "SELECT p.*, c.category_name FROM product p JOIN category c ON p.category_id = c.category_id ORDER BY p.product_id ASC");
                while ($row = mysqli_fetch_assoc($prods)) {
                    $prod_name = htmlspecialchars($row['product_name'], ENT_QUOTES);
                    echo "<tr>
                            <td>{$row['product_id']}</td>
                            <td>{$prod_name}</td>
                            <td>{$row['category_name']}</td>
                            <td>Rs. " . number_format($row['unit_price'], 2) . "</td>";
                    
                    if ($user_role === 'admin') {
                        echo "<td style='display:flex; gap:5px;'>
                                <button type='button' class='btn' style='background-color:#3498db; color:white; border:none; padding:5px 10px; cursor:pointer;' 
                                    onclick='openEditModal({$row['product_id']}, \"{$prod_name}\", {$row['category_id']}, {$row['unit_price']})'>
                                    Edit
                                </button>
                                <form method='POST' onsubmit='return confirm(\"Are you sure you want to delete this product?\");' style='margin:0;'>
                                    <input type='hidden' name='product_id' value='{$row['product_id']}'>
                                    <button type='submit' name='delete_product' class='btn' style='background-color:#e74c3c; color:white; border:none; padding:5px 10px; cursor:pointer;'>Delete</button>
                                </form>
                            </td>";
                    }

                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <?php if ($user_role === 'admin'): ?>
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeEditModal()">&times;</span>
            <h3 style="margin-bottom:15px;">Edit Product</h3>
            <form method="POST" style="display:flex; flex-direction:column; gap:12px;">
                <input type="hidden" name="product_id" id="edit_product_id">
                
                <div>
                    <label style="display:block; font-size:0.85rem; margin-bottom:4px;">Product Name</label>
                    <input type="text" name="product_name" id="edit_product_name" required style="width:100%; padding:8px; box-sizing:border-box;">
                </div>

                <div>
                    <label style="display:block; font-size:0.85rem; margin-bottom:4px;">Category</label>
                    <select name="category_id" id="edit_category_id" required style="width:100%; padding:8px; box-sizing:border-box;">
                        <?php
                        $cats = mysqli_query($conn, "SELECT * FROM category");
                        while ($c = mysqli_fetch_assoc($cats)) {
                            echo "<option value='{$c['category_id']}'>{$c['category_name']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div>
                    <label style="display:block; font-size:0.85rem; margin-bottom:4px;">Selling Price (Rs.)</label>
                    <input type="number" step="0.01" name="unit_price" id="edit_unit_price" required style="width:100%; padding:8px; box-sizing:border-box;">
                </div>

                <button type="submit" name="update_product" class="btn" style="background-color:#2ecc71; color:white; padding:10px; border:none; cursor:pointer; font-weight:bold; margin-top:5px;">
                    Update Product
                </button>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(id, name, categoryId, price) {
            document.getElementById('edit_product_id').value = id;
            document.getElementById('edit_product_name').value = name;
            document.getElementById('edit_category_id').value = categoryId;
            document.getElementById('edit_unit_price').value = price;
            
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
    <?php endif; ?>
</body>
</html>