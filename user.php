<?php
require_once 'auth_check.php';
require_admin(); 
require_once 'db_connect.php';
$message = '';
if (isset($_POST['update_role'])) {
    $target_user_id = (int)$_POST['user_id'];
    $new_role = mysqli_real_escape_string($conn, $_POST['role']);
    if ($target_user_id === (int)$_SESSION['user_id']) {
        $message = "You cannot change your own role.";
    } else {
        mysqli_query($conn, "UPDATE user SET role = '$new_role' WHERE user_id = '$target_user_id'");
        $message = "User role updated successfully.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users - Shop System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="content-box" style="max-width:1000px; margin:20px auto; padding:20px;">
        <h3>User Management</h3>
        <?php if (!empty($message)): ?>
            <div class="alert" style="padding:10px; background:#e2e3e5; margin-bottom:15px;"><?php echo $message; ?></div>
        <?php endif; ?>

        <table style="width:100%; border-collapse:collapse; margin-top:15px;">
            <thead>
                <tr style="background:#f8f9fa; border-bottom:2px solid #dee2e6; text-align:left;">
                    <th style="padding:10px;">ID</th>
                    <th style="padding:10px;">Full Name</th>
                    <th style="padding:10px;">Email</th>
                    <th style="padding:10px;">Role</th>
                    <th style="padding:10px;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = mysqli_query($conn, "SELECT * FROM user ORDER BY user_id ASC");
                while ($u = mysqli_fetch_assoc($result)) {
                    echo "<tr style='border-bottom:1px solid #dee2e6;'>
                            <td style='padding:10px;'>{$u['user_id']}</td>
                            <td style='padding:10px;'>" . htmlspecialchars($u['full_name']) . "</td>
                            <td style='padding:10px;'>" . htmlspecialchars($u['email']) . "</td>
                            <td style='padding:10px;'><strong style='color:" . ($u['role'] === 'admin' ? '#2ecc71' : '#3498db') . "'>" . strtoupper($u['role']) . "</strong></td>
                            <td style='padding:10px;'>";
                    
                    if ($u['user_id'] != $_SESSION['user_id']) {
                        echo "<form method='POST' style='display:inline-flex; gap:5px;'>
                                <input type='hidden' name='user_id' value='{$u['user_id']}'>
                                <select name='role' style='padding:4px;'>
                                    <option value='staff' " . ($u['role'] === 'staff' ? 'selected' : '') . ">Staff</option>
                                    <option value='admin' " . ($u['role'] === 'admin' ? 'selected' : '') . ">Admin</option>
                                </select>
                                <button type='submit' name='update_role' class='btn' style='padding:4px 8px; font-size:0.85rem;'>Save</button>
                              </form>";
                    } else {
                        echo "<small style='color:#888;'>Current Account</small>";
                    }

                    echo "  </td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>