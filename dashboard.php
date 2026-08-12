<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'staff';

$admin_only_pages = ['products', 'suppliers', 'purchases', 'users'];

if (in_array($page, $admin_only_pages) && $user_role !== 'admin') {
    $page = 'access_denied';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Shop System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="dashboard-layout">
        <?php include 'sidebar.php'; ?>
        <div class="main-content">
            <header style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
                <h1>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></h1>
                <span style="background: #e2e8f0; padding: 5px 12px; border-radius: 15px; font-size: 0.85rem; font-weight: bold; text-transform: uppercase;">
                    Role: <?php echo htmlspecialchars($user_role); ?>
                </span>
            </header>
            <?php
            $allowed_pages = ['home', 'products', 'inventory', 'sales', 'suppliers', 'purchases', 'records', 'users'];
            
            if ($page === 'access_denied') {
                echo "<div class='content-box' style='color: #721c24; background-color: #f8d7da; border-color: #f5c6cb;'>
                        <h3>Access Denied</h3>
                        <p>You do not have permission to access this module.</p>
                      </div>";
            } elseif (in_array($page, $allowed_pages)) {
                $file = "{$page}.php";
                if (file_exists($file)) {
                    include $file;
                } else {
                    echo "<div class='content-box'><h3>Module file missing: {$file}</h3></div>";
                }
            } else {
                echo "<div class='content-box'><h3>Page not found!</h3></div>";
            }
            ?>
        </div>
    </div>
</body>
</html>