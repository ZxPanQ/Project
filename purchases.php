<?php
// Get today's date in YYYY-MM-DD format
$today = date('Y-m-d');

// Log New Purchase & Batch
if (isset($_POST['add_purchase'])) {
    $supplier_id = (int)$_POST['supplier_id'];
    $product_id  = (int)$_POST['product_id'];
    $qty         = (int)$_POST['quantity'];
    $cost        = (float)$_POST['unit_cost'];
    $batch_no    = mysqli_real_escape_string($conn, $_POST['batch_number']);
    $expiry      = mysqli_real_escape_string($conn, $_POST['expiry_date']);
    $u_id        = $_SESSION['user_id'];

    // Server-side Check: Ensure expiry date is NOT before today
    if ($expiry < $today) {
        echo "<div class='alert' style='background-color:#f8d7da; color:#721c24;'>Error: Expiry date cannot be in the past!</div>";
    } else {
        $total = $qty * $cost;

        // Insert Purchase
        mysqli_query($conn, "INSERT INTO purchase (supplier_id, user_id, total_amount) VALUES ('$supplier_id', '$u_id', '$total')");
        $purchase_id = mysqli_insert_id($conn);

        // Insert Purchase Item
        mysqli_query($conn, "INSERT INTO purchase_item (purchase_id, product_id, quantity, unit_cost) VALUES ('$purchase_id', '$product_id', '$qty', '$cost')");
        $purchase_item_id = mysqli_insert_id($conn);

        // Create Associated Stock Batch (1:1 with PurchaseItem)
        mysqli_query($conn, "INSERT INTO batch (product_id, purchase_item_id, batch_number, quantity_remaining, expiry_date) 
                             VALUES ('$product_id', '$purchase_item_id', '$batch_no', '$qty', '$expiry')");

        echo "<div class='alert' style='background-color:#d4edda; color:#155724;'>Stock Purchase Logged Successfully!</div>";
    }
}
?>

<div class="content-box">
    <h3>Log Stock Purchase & New Batch</h3>
    <form method="POST" style="display:grid; grid-template-columns: 1fr 1fr; gap:15px; margin-top:10px;">
        <div>
            <label>Supplier</label>
            <select name="supplier_id" required style="width:100%; padding:8px;">
                <?php
                // Inline supplier check/creation if empty
                $sups = mysqli_query($conn, "SELECT * FROM supplier");
                if (mysqli_num_rows($sups) == 0) {
                    mysqli_query($conn, "INSERT INTO supplier (supplier_name) VALUES ('Default Local Vendor')");
                    $sups = mysqli_query($conn, "SELECT * FROM supplier");
                }
                while ($s = mysqli_fetch_assoc($sups)) {
                    echo "<option value='{$s['supplier_id']}'>{$s['supplier_name']}</option>";
                }
                ?>
            </select>
        </div>
        <div>
            <label>Product</label>
            <select name="product_id" required style="width:100%; padding:8px;">
                <?php
                $prods = mysqli_query($conn, "SELECT * FROM product");
                while ($p = mysqli_fetch_assoc($prods)) {
                    echo "<option value='{$p['product_id']}'>{$p['product_name']}</option>";
                }
                ?>
            </select>
        </div>
        <div>
            <label>Batch Code/Number</label>
            <input type="text" name="batch_number" required style="width:100%; padding:8px;">
        </div>
        <div>
            <label>Quantity Received</label>
            <input type="number" name="quantity" min="1" required style="width:100%; padding:8px;">
        </div>
        <div>
            <label>Unit Cost (Rs.)</label>
            <input type="number" step="0.01" name="unit_cost" required style="width:100%; padding:8px;">
        </div>
        <div>
            <label>Expiry Date</label>
            <!-- Added min="<?php echo $today; ?>" to disable past dates in calendar picker -->
            <input type="date" name="expiry_date" min="<?php echo $today; ?>" required style="width:100%; padding:8px;">
        </div>
        <div style="grid-column: span 2;">
            <button type="submit" name="add_purchase" class="btn">Record Stock Intake</button>
        </div>
    </form>
</div>