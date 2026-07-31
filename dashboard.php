<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
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
            <header style="margin-bottom: 20px;">
                <h1>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></h1>
            </header>
            <?php
            $allowed_pages = ['home', 'products', 'inventory', 'sales', 'suppliers', 'purchases'];
            if (in_array($page, $allowed_pages)) {
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