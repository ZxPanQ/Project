<?php
$filter = isset($_GET['type']) ? $_GET['type'] : 'all';
?>
<div class="content-box">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
        <h3>Transaction & Movement Records</h3>
        <div>
            <a href="?page=records&type=all" class="btn <?php echo $filter == 'all' ? '' : 'btn-outline'; ?>" style="padding: 6px 12px; font-size: 0.85rem;">All Transactions</a>
            <a href="?page=records&type=sales" class="btn <?php echo $filter == 'sales' ? '' : 'btn-outline'; ?>" style="padding: 6px 12px; font-size: 0.85rem;">Sales Only</a>
            <a href="?page=records&type=purchases" class="btn <?php echo $filter == 'purchases' ? '' : 'btn-outline'; ?>" style="padding: 6px 12px; font-size: 0.85rem;">Purchases Only</a>
        </div>
    </div>
    <table>
        <thead>
            <tr>
                <th>Date & Time</th>
                <th>Type</th>
                <th>Product</th>
                <th>Qty</th>
                <th>Unit Rate</th>
                <th>Total Amount</th>
                <th>Reference / Source</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sales_query = "SELECT 
                                s.sales_date AS record_date, 
                                'Sale' AS record_type, 
                                p.product_name, 
                                si.quantity AS qty, 
                                si.unit_price AS unit_rate, 
                                si.subtotal AS total_amount, 
                                CONCAT('Invoice #', s.sales_id) AS ref_info
                            FROM sales_item si
                            JOIN sales s ON si.sales_id = s.sales_id
                            JOIN product p ON si.product_id = p.product_id";
            $purchases_query = "SELECT 
                                    pu.purchase_date AS record_date, 
                                    'Purchase' AS record_type, 
                                    p.product_name, 
                                    pi.quantity AS qty, 
                                    pi.unit_cost AS unit_rate, 
                                    (pi.quantity * pi.unit_cost) AS total_amount, 
                                    sup.supplier_name AS ref_info
                                FROM purchase_item pi
                                JOIN purchase pu ON pi.purchase_id = pu.purchase_id
                                JOIN product p ON pi.product_id = p.product_id
                                LEFT JOIN supplier sup ON pu.supplier_id = sup.supplier_id";
            if ($filter == 'sales') {
                $final_sql = "$sales_query ORDER BY record_date DESC";
            } elseif ($filter == 'purchases') {
                $final_sql = "$purchases_query ORDER BY record_date DESC";
            } else {
                $final_sql = "($sales_query) UNION ALL ($purchases_query) ORDER BY record_date DESC";
            }
            $records = mysqli_query($conn, $final_sql);
            if ($records && mysqli_num_rows($records) > 0) {
                while ($r = mysqli_fetch_assoc($records)) {
                    $is_sale = ($r['record_type'] == 'Sale');
                    $badge_style = $is_sale 
                        ? 'background-color: #d4edda; color: #155724; padding: 4px 8px; border-radius: 4px; font-weight: bold;' 
                        : 'background-color: #cce5ff; color: #004085; padding: 4px 8px; border-radius: 4px; font-weight: bold;';
                    echo "<tr>
                            <td>" . date('Y-m-d H:i', strtotime($r['record_date'])) . "</td>
                            <td><span style='{$badge_style}'>" . htmlspecialchars($r['record_type']) . "</span></td>
                            <td>" . htmlspecialchars($r['product_name']) . "</td>
                            <td>{$r['qty']}</td>
                            <td>Rs. " . number_format($r['unit_rate'], 2) . "</td>
                            <td><strong>Rs. " . number_format($r['total_amount'], 2) . "</strong></td>
                            <td>" . htmlspecialchars($r['ref_info'] ?? 'N/A') . "</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='7' style='text-align:center; color:#777;'>No transaction records found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>