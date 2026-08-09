<?php
// Process Sale (Product-based with Automatic FIFO Batch Deduction)
if (isset($_POST['process_sale']) && !empty($_POST['product_id']) && is_array($_POST['product_id'])) {
    $u_id        = $_SESSION['user_id'];
    $product_ids = $_POST['product_id'];
    $quantities  = $_POST['quantity'] ?? [];
    $grand_total = 0;
    $errors      = [];
    $sale_items  = [];

    // Step 1: Validate total stock availability per requested product
    foreach ($product_ids as $i => $product_id) {
        $product_id = (int)$product_id;
        $req_qty    = isset($quantities[$i]) ? (int)$quantities[$i] : 0;
        if ($product_id == 0 || $req_qty < 1) continue;

        // Fetch product and calculate total available stock across all active batches
        $p_q = mysqli_query($conn, "SELECT p.*, COALESCE(SUM(b.quantity_remaining), 0) AS total_stock 
                                    FROM product p 
                                    LEFT JOIN batch b ON p.product_id = b.product_id AND b.quantity_remaining > 0 
                                    WHERE p.product_id = '$product_id' 
                                    GROUP BY p.product_id");
        $product = mysqli_fetch_assoc($p_q);

        if (!$product || $product['total_stock'] < $req_qty) {
            $prod_name = $product['product_name'] ?? 'Selected item';
            $avail = $product['total_stock'] ?? 0;
            $errors[] = "Not enough stock for <strong>{$prod_name}</strong>. Requested: {$req_qty}, Total Available: {$avail}.";
        } else {
            $subtotal = $product['unit_price'] * $req_qty;
            $grand_total += $subtotal;
            $sale_items[] = [
                'product_id' => $product_id,
                'req_qty'    => $req_qty,
                'unit_price' => $product['unit_price']
            ];
        }
    }

    // Step 2: Execute Sale and Deduct Stock via FIFO
    if (empty($errors) && !empty($sale_items)) {
        // Create Sales Header Record
        mysqli_query($conn, "INSERT INTO sales (user_id, total_amount) VALUES ('$u_id', '$grand_total')");
        $sales_id = mysqli_insert_id($conn);

        foreach ($sale_items as $item) {
            $p_id       = $item['product_id'];
            $rem_to_ded = $item['req_qty'];
            $unit_price = $item['unit_price'];

            // Query active batches ordered by expiry (or batch_id) for FIFO
            $b_q = mysqli_query($conn, "SELECT * FROM batch 
                                        WHERE product_id = '$p_id' AND quantity_remaining > 0 
                                        ORDER BY expiry_date ASC, batch_id ASC");

            while ($batch = mysqli_fetch_assoc($b_q)) {
                if ($rem_to_ded <= 0) break;

                $batch_id    = $batch['batch_id'];
                $available   = $batch['quantity_remaining'];
                $deduct_qty  = min($rem_to_ded, $available);
                $item_sub    = $deduct_qty * $unit_price;

                // Insert detail row tied to specific batch used
                mysqli_query($conn, "INSERT INTO sales_item (sales_id, product_id, batch_id, quantity, unit_price, subtotal) 
                                     VALUES ('$sales_id', '$p_id', '$batch_id', '$deduct_qty', '$unit_price', '$item_sub')");

                // Update remaining quantity for this batch
                $new_qty = $available - $deduct_qty;
                mysqli_query($conn, "UPDATE batch SET quantity_remaining = '$new_qty' WHERE batch_id = '$batch_id'");

                $rem_to_ded -= $deduct_qty;
            }
        }
        echo "<div class='alert' style='background-color:#d4edda; color:#155724;'>Sale completed successfully!</div>";
    } else {
        foreach ($errors as $err) {
            echo "<div class='alert' style='background-color:#f8d7da; color:#721c24; margin-bottom:10px;'>$err</div>";
        }
    }
}

// Fetch products with combined stock total across all batches
$prod_query = "SELECT p.*, SUM(b.quantity_remaining) AS total_stock 
               FROM product p 
               JOIN batch b ON p.product_id = b.product_id 
               WHERE b.quantity_remaining > 0 
               GROUP BY p.product_id, p.product_name, p.unit_price 
               HAVING total_stock > 0";
$avail_products = mysqli_query($conn, $prod_query);

$product_options = "<option value='' data-price='0'>Select Product</option>";
while ($p = mysqli_fetch_assoc($avail_products)) {
    $product_options .= "<option value='{$p['product_id']}' data-price='{$p['unit_price']}'>"
        . "{$p['product_name']} (Total Stock: {$p['total_stock']}) - Rs.{$p['unit_price']}"
        . "</option>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Sales Entry</title>
</head>
<body>
    <div class="content-box">
        <h3>New Sale Transaction</h3>
        <form method="POST" action="dashboard.php?page=sales" id="posForm">

            <table id="itemsTable" style="width:100%; border-collapse:collapse; margin-bottom:10px;">
                <thead>
                    <tr>
                        <th style="text-align:left; padding:6px;">Product</th>
                        <th style="text-align:left; padding:6px;">Qty</th>
                        <th style="text-align:left; padding:6px;">Subtotal</th>
                        <th style="padding:6px;"></th>
                    </tr>
                </thead>
                <tbody id="itemsBody">
                    <tr class="item-row">
                        <td style="padding:6px;">
                            <select name="product_id[]" required onchange="recalcTotal()" style="width:100%; padding:8px;">
                                <?php echo $product_options; ?>
                            </select>
                        </td>
                        <td style="padding:6px;">
                            <input type="number" name="quantity[]" min="1" value="1" required oninput="recalcTotal()" style="width:80px; padding:8px;">
                        </td>
                        <td style="padding:6px;">
                            <input type="text" readonly value="0.00" class="row-subtotal" style="width:90px; padding:8px; background:#eee;">
                        </td>
                        <td style="padding:6px;">
                            <button type="button" onclick="removeRow(this)" class="btn" style="background:#e74c3c;">✕</button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <button type="button" onclick="addRow()" class="btn" style="background:#3498db; margin-bottom:15px;">+ Add Item</button>

            <div class="form-group" style="margin-bottom:15px;">
                <label style="display:block; font-weight:bold;">Grand Total (Rs.)</label>
                <input type="text" id="grandTotal" readonly value="0.00" style="padding:8px; background:#eee; font-weight:bold;">
            </div>

            <button type="submit" name="process_sale" class="btn" style="background-color:#2ecc71; padding:10px 15px; border:none; color:white; cursor:pointer;">Complete Sale</button>
        </form>
    </div>

    <script>
    var productOptionsHTML = <?php echo json_encode($product_options); ?>;

    function addRow() {
        var tbody = document.getElementById('itemsBody');
        var newRow = document.createElement('tr');
        newRow.className = 'item-row';
        newRow.innerHTML = '<td style="padding:6px;"><select name="product_id[]" required onchange="recalcTotal()" style="width:100%; padding:8px;">' + productOptionsHTML + '</select></td>'
            + '<td style="padding:6px;"><input type="number" name="quantity[]" min="1" value="1" required oninput="recalcTotal()" style="width:80px; padding:8px;"></td>'
            + '<td style="padding:6px;"><input type="text" readonly value="0.00" class="row-subtotal" style="width:90px; padding:8px; background:#eee;"></td>'
            + '<td style="padding:6px;"><button type="button" onclick="removeRow(this)" class="btn" style="background:#e74c3c;">✕</button></td>';
        tbody.appendChild(newRow);
    }

    function removeRow(btn) {
        var rows = document.querySelectorAll('.item-row');
        if (rows.length > 1) {
            btn.closest('tr').remove();
            recalcTotal();
        }
    }

    function recalcTotal() {
        var rows = document.querySelectorAll('.item-row');
        var grand = 0;
        rows.forEach(function(row) {
            var sel   = row.querySelector('select');
            var price = parseFloat(sel.options[sel.selectedIndex].getAttribute('data-price')) || 0;
            var qty   = parseInt(row.querySelector('input[type=number]').value) || 0;
            var sub   = price * qty;
            row.querySelector('.row-subtotal').value = sub.toFixed(2);
            grand += sub;
        });
        document.getElementById('grandTotal').value = grand.toFixed(2);
    }
    </script>
</body>
</html>