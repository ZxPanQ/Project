<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="content-box">
    <h3>Log Stock Purchase & New Batch</h3>
    <form method="POST" style="display:grid; grid-template-columns: 1fr 1fr; gap:15px; margin-top:10px;">
        <div>
            <label>Supplier</label>
            <select name="supplier_id" required style="width:100%; padding:8px;">
            </select>
        </div>
        <div>
            <label>Product</label>
            <select name="product_id" required style="width:100%; padding:8px;">
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
</body>
</html>
