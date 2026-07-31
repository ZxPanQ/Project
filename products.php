<?php
if (isset($_POST['add_category'])) {
    $cat = mysqli_real_escape_string($conn, $_POST['category_name']);
    mysqli_query($conn, "INSERT INTO category (category_name) VALUES ('$cat')");
}
if (isset($_POST['add_product'])) {
    $name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $cat_id = (int)$_POST['category_id'];
    $price = (float)$_POST['unit_price'];
    mysqli_query($conn, "INSERT INTO product (category_id, product_name, unit_price) VALUES ('$cat_id', '$name', '$price')");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
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
<div class="content-box">
    <h3>Product List</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Product</th>
            <th>Category</th>
            <th>Price</th>
        </tr>
        <?php
        $prods = mysqli_query($conn, "SELECT p.*, c.category_name FROM product p JOIN category c ON p.category_id = c.category_id");
        while ($row = mysqli_fetch_assoc($prods)) {
            echo "<tr>
                    <td>{$row['product_id']}</td>
                    <td>{$row['product_name']}</td>
                    <td>{$row['category_name']}</td>
                    <td>Rs.{$row['unit_price']}</td>
                  </tr>";
        }
        ?>
    </table>
</div>
</body>
</html>
