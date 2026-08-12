<?php
// Process Sale (Product-based with Automatic FIFO Batch Deduction)
if (isset($_POST['process_sale']) && !empty($_POST['product_id']) && is_array($_POST['product_id'])) {
    $u_id        = $_SESSION['user_id'];
    $raw_prods   = $_POST['product_id'];
    $raw_qtys    = $_POST['quantity'] ?? [];
    
    // Step 1: Consolidate quantities for duplicated products in the same POST request
    $requested_items = [];
    foreach ($raw_prods as $i => $pid) {
        $p_id = (int)$pid;
        $qty  = isset($raw_qtys[$i]) ? (int)$raw_qtys[$i] : 0;
        
        if ($p_id > 0 && $qty > 0) {
            if (!isset($requested_items[$p_id])) {
                $requested_items[$p_id] = 0;
            }
            $requested_items[$p_id] += $qty;
        }
    }

    $grand_total = 0;
    $errors      = [];
    $sale_items  = [];

    // Step 2: Validate total available stock per requested product
    foreach ($requested_items as $product_id => $req_qty) {
        $stmt = mysqli_prepare($conn, "
            SELECT p.product_name, p.unit_price, COALESCE(SUM(b.quantity_remaining), 0) AS total_stock 
            FROM product p 
            LEFT JOIN batch b ON p.product_id = b.product_id AND b.quantity_remaining > 0 
            WHERE p.product_id = ? 
            GROUP BY p.product_id, p.product_name, p.unit_price
        ");
        mysqli_stmt_bind_param($stmt, "i", $product_id);
        mysqli_stmt_execute($stmt);
        $result  = mysqli_stmt_get_result($stmt);
        $product = mysqli_fetch_assoc($result);

        if (!$product || $product['total_stock'] < $req_qty) {
            $prod_name = $product['product_name'] ?? 'Selected item';
            $avail     = $product['total_stock'] ?? 0;
            $errors[]  = "Not enough stock for <strong>" . htmlspecialchars($prod_name) . "</strong>. Requested: {$req_qty}, Available: {$avail}.";
        } else {
            $subtotal     = $product['unit_price'] * $req_qty;
            $grand_total += $subtotal;
            $sale_items[] = [
                'product_id' => $product_id,
                'req_qty'    => $req_qty,
                'unit_price' => $product['unit_price']
            ];
        }
    }

    // Step 3: Atomic Sale Processing via Database Transactions & FIFO
    if (empty($errors) && !empty($sale_items)) {
        mysqli_begin_transaction($conn);

        try {
            // Insert Sales Header
            $stmt_sale = mysqli_prepare($conn, "INSERT INTO sales (user_id, total_amount) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt_sale, "id", $u_id, $grand_total);
            mysqli_stmt_execute($stmt_sale);
            $sales_id = mysqli_insert_id($conn);

            // Prepared Statements for Sales Details and Batch Deductions
            $stmt_item   = mysqli_prepare($conn, "INSERT INTO sales_item (sales_id, product_id, batch_id, quantity, unit_price, subtotal) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt_update = mysqli_prepare($conn, "UPDATE batch SET quantity_remaining = ? WHERE batch_id = ?");

            foreach ($sale_items as $item) {
                $p_id       = $item['product_id'];
                $rem_to_ded = $item['req_qty'];
                $unit_price = $item['unit_price'];

                // Retrieve active batches sorted by Expiry Date (FIFO)
                $stmt_batch = mysqli_prepare($conn, "SELECT batch_id, quantity_remaining FROM batch WHERE product_id = ? AND quantity_remaining > 0 ORDER BY expiry_date ASC, batch_id ASC");
                mysqli_stmt_bind_param($stmt_batch, "i", $p_id);
                mysqli_stmt_execute($stmt_batch);
                $b_res = mysqli_stmt_get_result($stmt_batch);

                while ($batch = mysqli_fetch_assoc($b_res)) {
                    if ($rem_to_ded <= 0) break;

                    $batch_id   = $batch['batch_id'];
                    $available  = $batch['quantity_remaining'];
                    $deduct_qty = min($rem_to_ded, $available);
                    $item_sub   = $deduct_qty * $unit_price;

                    // Log Sale Item linked to specific batch
                    mysqli_stmt_bind_param($stmt_item, "iiiddd", $sales_id, $p_id, $batch_id, $deduct_qty, $unit_price, $item_sub);
                    mysqli_stmt_execute($stmt_item);

                    // Update batch stock balance
                    $new_qty = $available - $deduct_qty;
                    mysqli_stmt_bind_param($stmt_update, "ii", $new_qty, $batch_id);
                    mysqli_stmt_execute($stmt_update);

                    $rem_to_ded -= $deduct_qty;
                }

                if ($rem_to_ded > 0) {
                    throw new Exception("Inventory mismatch during checkout for Product ID: " . $p_id);
                }
            }

            mysqli_commit($conn);
            echo "<div class='alert' style='background-color:#d4edda; color:#155724; padding:10px; margin-bottom:15px;'>Sale completed successfully! Transaction ID: #{$sales_id}</div>";

        } catch (Exception $e) {
            mysqli_rollback($conn);
            echo "<div class='alert' style='background-color:#f8d7da; color:#721c24; padding:10px; margin-bottom:15px;'>Transaction Failed: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    } else {
        foreach ($errors as $err) {
            echo "<div class='alert' style='background-color:#f8d7da; color:#721c24; margin-bottom:10px; padding:10px;'>$err</div>";
        }
    }
}

// Fetch products with combined active stock totals
$prod_query = "SELECT p.product_id, p.product_name, p.unit_price, SUM(b.quantity_remaining) AS total_stock 
               FROM product p 
               JOIN batch b ON p.product_id = b.product_id 
               WHERE b.quantity_remaining > 0 
               GROUP BY p.product_id, p.product_name, p.unit_price 
               HAVING total_stock > 0";
$avail_products = mysqli_query($conn, $prod_query);

$product_options = "<option value='' data-price='0'>Select Product</option>";
while ($p = mysqli_fetch_assoc($avail_products)) {
    $product_options .= "<option value='{$p['product_id']}' data-price='{$p['unit_price']}'>"
        . htmlspecialchars($p['product_name']) . " (Total Stock: {$p['total_stock']}) - Rs.{$p['unit_price']}"
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
                            <button type="button" onclick="removeRow(this)" class="btn" style="background:#e74c3c; color:#fff; border:none; padding:8px 12px; cursor:pointer;">✕</button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <button type="button" onclick="addRow()" class="btn" style="background:#3498db; color:#fff; border:none; padding:8px 12px; cursor:pointer; margin-bottom:15px;">+ Add Item</button>

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
            + '<td style="padding:6px;"><button type="button" onclick="removeRow(this)" class="btn" style="background:#e74c3c; color:#fff; border:none; padding:8px 12px; cursor:pointer;">✕</button></td>';
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