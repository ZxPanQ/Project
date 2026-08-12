<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Function to restrict page access to Admin only
function require_admin() {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        echo "<div style='padding:20px; color:red; font-family:sans-serif;'>
                <h2>Access Denied</h2>
                <p>You do not have permission to access this page.</p>
                <a href='dashboard.php'>Return to Dashboard</a>
              </div>";
        exit();
    }
}
?>