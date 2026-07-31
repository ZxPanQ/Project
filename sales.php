<?php
if (isset($_POST['process_sale'])) {
    $u_id       = $_SESSION['user_id'];
    $batch_ids  = $_POST['batch_id'];
    $quantities = $_POST['quantity'];
    $grand_total = 0;
    $errors = [];
    $valid_items = [];
    foreach ($batch_ids as $i => $batch_id) {
        $batch_id = (int)$batch_id;
        $qty      = (int)$quantities[$i];
        if ($batch_id == 0 || $qty < 1) continue;

        $b_q   = mysqli_query($conn, "SELECT b.*, p.unit_price, p.product_id FROM batch b JOIN product p ON b.product_id = p.product_id WHERE b.batch_id = '$batch_id'");
        $batch = mysqli_fetch_assoc($b_q);

        if ($batch && $batch['quantity_remaining'] >= $qty) {
            $subtotal      = $batch['unit_price'] * $qty;
            $grand_total  += $subtotal;
            $valid_items[] = ['batch' => $batch, 'qty' => $qty, 'subtotal' => $subtotal];
        } else {
            $errors[] = "Not enough stock for one of the selected batches.";
        }
    }
    if (empty($errors) && !empty($valid_items)) {
        // Create Sale Header
        mysqli_query($conn, "INSERT INTO sales (user_id, total_amount) VALUES ('$u_id', '$grand_total')");
        $sales_id = mysqli_insert_id($conn);
        foreach ($valid_items as $vi) {
            $b   = $vi['batch'];
            $qty = $vi['qty'];
            $sub = $vi['subtotal'];

            // Insert each Sales Item
            mysqli_query($conn, "INSERT INTO sales_item (sales_id, product_id, batch_id, quantity, unit_price, subtotal) 
                                 VALUES ('$sales_id', '{$b['product_id']}', '{$b['batch_id']}', '$qty', '{$b['unit_price']}', '$sub')");
            // Update Batch Stock
            $new_qty = $b['quantity_remaining'] - $qty;
            mysqli_query($conn, "UPDATE batch SET quantity_remaining = '$new_qty' WHERE batch_id = '{$b['batch_id']}'");
        }
        echo "<div class='alert' style='background-color:#d4edda; color:#155724;'>Sale registered successfully!</div>";
    } else {
        foreach ($errors as $err) {
            echo "<div class='alert'>$err</div>";
        }
    }
}

// Build batch options once for JS reuse
$avail = mysqli_query($conn, "SELECT b.*, p.product_name, p.unit_price FROM batch b JOIN product p ON b.product_id = p.product_id WHERE b.quantity_remaining > 0");
$batch_options = "<option value='' data-price='0'>Select Product Batch</option>";
while ($item = mysqli_fetch_assoc($avail)) {
    $batch_options .= "<option value='{$item['batch_id']}' data-price='{$item['unit_price']}'>"
        . "{$item['product_name']} (Batch: {$item['batch_number']} | In Stock: {$item['quantity_remaining']}) - Rs.{$item['unit_price']}"
        . "</option>";
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
    <h3>New Sale Transaction</h3>
    <form method="POST" action="dashboard.php?page=sales">

        <table id="itemsTable" style="width:100%; border-collapse:collapse; margin-bottom:10px;">
            <tr>
                <th style="text-align:left; padding:6px;">Item Batch</th>
                <th style="text-align:left; padding:6px;">Qty</th>
                <th style="text-align:left; padding:6px;">Subtotal</th>
                <th style="padding:6px;"></th>
            </tr>
            <tr class="item-row">
                <td style="padding:6px;">
                    <select name="batch_id[]" required onchange="recalcTotal()" style="width:100%; padding:8px;">
                        <?php echo $batch_options; ?>
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
        </table>

        <button type="button" onclick="addRow()" class="btn" style="background:#3498db; margin-bottom:15px;">+ Add Item</button>

        <div class="form-group">
            <label>Grand Total (Rs.)</label>
            <input type="text" id="grandTotal" readonly value="0.00" style="padding:8px; background:#eee; font-weight:bold;">
        </div>

        <button type="submit" name="process_sale" class="btn" style="background-color:#2ecc71;">Complete Sale</button>
    </form>
</div>

<script>
var batchOptionsHTML = <?php echo json_encode($batch_options); ?>;

function addRow() {
    var tbody = document.getElementById('itemsTable');
    var newRow = document.createElement('tr');
    newRow.className = 'item-row';
    newRow.innerHTML = '<td style="padding:6px;"><select name="batch_id[]" required onchange="recalcTotal()" style="width:100%; padding:8px;">' + batchOptionsHTML + '</select></td>'
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
